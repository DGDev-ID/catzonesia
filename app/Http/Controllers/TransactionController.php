<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MProduct;
use App\Models\MPackage;
use App\Models\ProductMovement;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $transactions = Transaction::query()
            ->where('status', 'success')
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->with('transactionDetails.product', 'transactionDetails.package', 'transactionGrooming')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('transaction/Index', [
            'transactions' => $transactions,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(
            'transactionDetails.product',
            'transactionDetails.package',
            'transactionDetails.unit',
            'transactionGrooming',
            'transactionLogs',
        );

        return Inertia::render('transaction/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function export(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $transactions = Transaction::query()
            ->where('status', 'success')
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByDesc('id')
            ->get(['id', 'unique_id', 'cust_email', 'price', 'fee', 'total_price', 'created_at']);

        $fileName = 'transactions_success';
        if ($dateFrom || $dateTo) {
            $fileName .= '_' . ($dateFrom ?: 'start') . '_' . ($dateTo ?: 'end');
        }
        $fileName .= '.csv';

        return response()->streamDownload(function () use ($transactions) {
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'No',
                'Kode Unik Transaksi',
                'Email Customer',
                'Harga',
                'Fee',
                'Total',
                'Tanggal',
            ]);

            foreach ($transactions as $index => $trx) {
                fputcsv($handle, [
                    $index + 1,
                    $trx->unique_id,
                    $trx->cust_email,
                    $trx->price,
                    $trx->fee,
                    $trx->total_price,
                    $trx->created_at,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function markAsSuccess($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'success') {
            return redirect()->route('transaction.index');
        }

        $transaction->status = 'success';
        $transaction->save();

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'status' => 'success',
            'notes' => 'Transaksi ditandai sukses',
        ]);

        toast('Transaksi berhasil diselesaikan');
        return redirect()->route('transaction.index');
    }

    public function markAsFailed($id)
    {
        $transaction = Transaction::with('transactionDetails')->findOrFail($id);

        if ($transaction->status === 'failed') {
            return redirect()->route('transaction.index');
        }

        $transaction->status = 'failed';
        $transaction->save();

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'status' => 'failed',
            'notes' => 'Transaksi ditandai gagal',
        ]);

        foreach ($transaction->transactionDetails as $detail) {
            if ($detail->product_id) {
                $product = MProduct::find($detail->product_id);
                if ($product) {
                    ProductMovement::create([
                        'product_id' => $product->id,
                        'type' => 'inbound',
                        'quantity' => $detail->amount,
                        'unit_id' => $detail->unit_id,
                        'transaction_id' => $transaction->id,
                        'note' => 'Return stok karena transaksi gagal',
                    ]);
                }
            } else if ($detail->package_id) {
                $package = MPackage::find($detail->package_id);
                if ($package) {
                    foreach ($package->products as $product) {
                        ProductMovement::create([
                            'product_id' => $product->id,
                            'type' => 'inbound',
                            'quantity' => $detail->amount,
                            'unit_id' => $product->base_unit_id,
                            'transaction_id' => $transaction->id,
                            'note' => 'Return stok karena transaksi gagal',
                        ]);
                    }
                }
            }
        }

        toast('Transaksi dibatalkan');
        return redirect()->route('transaction.index');
    }
}

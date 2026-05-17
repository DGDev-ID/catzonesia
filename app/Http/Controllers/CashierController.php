<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MProduct;
use App\Models\MPackage;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionGrooming;
use App\Models\ProductMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $products = MProduct::where('is_display', true)->get();
        $packages = MPackage::where('is_display', true)->get();
        $todayTransactions = Transaction::whereDate('created_at', today())->with('transactionDetails')->get();

        return Inertia::render('cashier/Index', [
            'products' => $products,
            'packages' => $packages,
            'todayTransactions' => $todayTransactions,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|in:product,package',
            'items.*.quantity' => 'required|integer|min:1',
            'cust_email' => 'nullable|email',
            'grooming' => 'nullable|array',
            'grooming.owner_name' => 'required_with:grooming|string',
            'grooming.owner_phone' => 'required_with:grooming|string',
            'grooming.cat_name' => 'required_with:grooming|string',
            'grooming.cat_age' => 'required_with:grooming|integer',
            'grooming.cat_estimated_weight' => 'required_with:grooming|numeric',
            'grooming.notes' => 'nullable|string',
        ]);

        $totalPrice = 0;
        $items = $validated['items'];

        foreach ($items as $item) {
            if ($item['type'] === 'product') {
                $product = MProduct::find($item['id']);
                if ($product) {
                    $totalPrice += $product->price * $item['quantity'];
                }
            } else if ($item['type'] === 'package') {
                $package = MPackage::find($item['id']);
                if ($package) {
                    $totalPrice += $package->price * $item['quantity'];
                }
            }
        }

        $transaction = Transaction::create([
            'cust_email' => $validated['cust_email'],
            'price' => $totalPrice,
            'fee' => 0,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        foreach ($items as $item) {
            if ($item['type'] === 'product') {
                $product = MProduct::find($item['id']);
                if ($product) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'amount' => $item['quantity'],
                        'unit_id' => $product->base_unit_id,
                        'price' => $product->price,
                    ]);

                    ProductMovement::create([
                        'product_id' => $product->id,
                        'type' => 'outbound',
                        'quantity' => $item['quantity'],
                        'unit_id' => $product->base_unit_id,
                        'transaction_id' => $transaction->id,
                    ]);

                    $product->stock -= $item['quantity'];
                    $product->save();
                }
            } else if ($item['type'] === 'package') {
                $package = MPackage::find($item['id']);
                if ($package) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'package_id' => $package->id,
                        'amount' => $item['quantity'],
                        'unit_id' => 1,
                        'price' => $package->price,
                    ]);

                    foreach ($package->products as $product) {
                        ProductMovement::create([
                            'product_id' => $product->id,
                            'type' => 'outbound',
                            'quantity' => $item['quantity'],
                            'unit_id' => $product->base_unit_id,
                            'transaction_id' => $transaction->id,
                        ]);

                        $product->stock -= $item['quantity'];
                        $product->save();
                    }
                }
            }
        }

        if (isset($validated['grooming'])) {
            TransactionGrooming::create([
                'transaction_id' => $transaction->id,
                'owner_name' => $validated['grooming']['owner_name'],
                'owner_phone' => $validated['grooming']['owner_phone'],
                'cat_name' => $validated['grooming']['cat_name'],
                'cat_age' => $validated['grooming']['cat_age'],
                'cat_estimated_weight' => $validated['grooming']['cat_estimated_weight'],
                'notes' => $validated['grooming']['notes'],
            ]);
        }

        return redirect()->back()->with('success', 'Transaksi berhasil dibuat');
    }

    public function success(Transaction $transaction)
    {
        $transaction->status = 'success';
        $transaction->save();

        return redirect()->back()->with('success', 'Transaksi berhasil diselesaikan');
    }

    public function failed(Transaction $transaction)
    {
        $transaction->status = 'failed';
        $transaction->save();

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

                    $product->stock += $detail->amount;
                    $product->save();
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

                        $product->stock += $detail->amount;
                        $product->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Transaksi dibatalkan');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
        $transactions = Transaction::query()
            ->when($request->query('date'), function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->when($request->query('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->with('transactionDetails.product', 'transactionDetails.package', 'transactionGrooming')
            ->paginate(10);

        return Inertia::render('transaction/Index', [
            'transactions' => $transactions,
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('transactionDetails.product', 'transactionDetails.package', 'transactionGrooming');

        return Inertia::render('transaction/Show', [
            'transaction' => $transaction,
        ]);
    }
}
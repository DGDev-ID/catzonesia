<?php

namespace App\Http\Controllers;

use App\Models\MProduct;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $now = now();

        $revenueThisMonth = Transaction::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where('status', 'success')
            ->sum('total_price');

        $transactionsThisMonth = Transaction::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $stats = [
            'total_products'           => MProduct::count(),
            'total_users'              => User::count(),
            'transactions_this_month'  => $transactionsThisMonth,
            'revenue_this_month'       => $revenueThisMonth,
        ];

        $transactionStatus = [
            'pending' => Transaction::where('status', 'pending')->count(),
            'success' => Transaction::where('status', 'success')->count(),
            'failed'  => Transaction::where('status', 'failed')->count(),
        ];

        $recentTransactions = Transaction::latest()->take(6)->get();

        $lowStockProducts = MProduct::whereColumn('stock', '<=', 'stock_alert')
            ->with('baseUnit')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats'               => $stats,
            'transactionStatus'   => $transactionStatus,
            'recentTransactions'  => $recentTransactions,
            'lowStockProducts'    => $lowStockProducts,
        ]);
    }
}

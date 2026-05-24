<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MCategory;
use App\Models\MProduct;
use App\Models\MPackage;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionGrooming;
use App\Models\ProductMovement;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = $request->query('search', '');
        $categoryId = null;
        if (is_string($filter) && str_starts_with($filter, 'category-')) {
            $categoryId = (int) str_replace('category-', '', $filter);
        }

        $categories = MCategory::query()
            ->orderBy('name')
            ->get();

        $products = collect();
        $packages = collect();

        if ($filter === 'all' || $categoryId !== null) {
            $productsQuery = MProduct::query()
                ->where('is_display', true)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                })
                ->when($categoryId !== null, function ($query) use ($categoryId) {
                    $query->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('m_categories.id', $categoryId);
                    });
                })
                ->select(['id', 'name', 'sku', 'price', 'img_url']);

            $products = $productsQuery->get()->map(function (MProduct $product) {
                return [
                    'id' => $product->id,
                    'type' => 'product',
                    'name' => $product->name,
                    'price' => $product->price,
                    'img_url' => $product->img_url,
                    'sku' => $product->sku,
                    'is_grooming' => false,
                    'description' => null,
                ];
            });
        }

        if ($filter === 'all' || $filter === 'package') {
            $packages = MPackage::query()
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->select(['id', 'name', 'price', 'img_url', 'is_grooming', 'description'])
                ->get()
                ->map(function (MPackage $package) {
                    return [
                        'id' => $package->id,
                        'type' => 'package',
                        'name' => $package->name,
                        'price' => $package->price,
                        'img_url' => $package->img_url,
                        'sku' => null,
                        'is_grooming' => (bool) $package->is_grooming,
                        'description' => $package->description,
                    ];
                });
        }

        $catalogCollection = $products
            ->merge($packages)
            ->sortBy('name')
            ->values();

        $perPage = 20;
        $page = Paginator::resolveCurrentPage('catalog_page');
        $currentItems = $catalogCollection->forPage($page, $perPage)->values();
        $catalog = new LengthAwarePaginator(
            $currentItems,
            $catalogCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'catalog_page',
                'query' => $request->query(),
            ],
        );

        $pendingTransactions = Transaction::query()
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        $successTransactions = Transaction::query()
            ->where('status', 'success')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'success_page')
            ->withQueryString();

        return Inertia::render('cashier/Index', [
            'filter' => $filter,
            'search' => $search,
            'categories' => $categories,
            'catalog' => $catalog,
            'pendingTransactions' => $pendingTransactions,
            'successTransactions' => $successTransactions,
        ]);
    }

    public function showTransaction(Transaction $transaction)
    {
        $transaction->load([
            'transactionDetails.product',
            'transactionDetails.package',
            'transactionDetails.unit',
            'transactionGrooming',
            'transactionLogs',
        ]);

        return Inertia::render('cashier/Show', [
            'transaction' => $transaction,
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

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'status' => 'pending',
            'notes' => 'Transaksi dibuat dari kasir',
        ]);

        toast('Transaksi berhasil dibuat');
        return redirect()->back();
    }

    public function markAsSuccess($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return redirect()->back();
        }

        $transaction->status = 'success';
        $transaction->save();

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'status' => 'success',
            'notes' => 'Transaksi ditandai sukses dari kasir',
        ]);

        toast('Transaksi berhasil diselesaikan');
        return redirect()->route('cashier.index');
    }

    public function markAsFailed($id)
    {
        $transaction = Transaction::with('transactionDetails')->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return redirect()->route('cashier.index');
        }

        $transaction->status = 'failed';
        $transaction->save();

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'status' => 'failed',
            'notes' => 'Transaksi ditandai gagal dari kasir',
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
        return redirect()->route('cashier.index');
    }
}

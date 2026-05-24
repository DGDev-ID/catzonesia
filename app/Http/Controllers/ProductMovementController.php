<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MProduct;
use App\Models\ProductMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $products = MProduct::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('product-movement/Index', [
            'products' => $products,
        ]);
    }

    public function show($id)
    {
        $product = MProduct::find($id);
        if (!$product) {
            return back()->withErrors(['product' => 'Produk tidak ditemukan']);
        }
        $product->load(['baseUnit', 'productUnitConverters.unitFrom', 'productUnitConverters.unitTo']);

        $movements = ProductMovement::where('product_id', $product->id)
            ->with('unit', 'transaction')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('product-movement/Show', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }

    public function adjustment(Request $request, MProduct $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:inbound,outbound',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|exists:m_units,id',
            'price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        if ($validated['type'] === 'inbound' && $validated['price'] === null) {
            return back()->withErrors(['price' => 'Harga wajib diisi untuk inbound']);
        }

        ProductMovement::create([
            'product_id' => $product->id,
            'type' => $validated['type'],
            'quantity' => (int) $validated['quantity'],
            'unit_id' => (int) $validated['unit_id'],
            'note' => $validated['note'],
            'price' => $validated['type'] === 'inbound' ? $validated['price'] : null,
        ]);
        toast('Stok berhasil diupdate');
        return redirect()->back();
    }
}

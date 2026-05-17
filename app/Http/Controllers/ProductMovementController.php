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

    public function show(MProduct $product)
    {
        $movements = ProductMovement::where('product_id', $product->id)
            ->with('unit', 'transaction')
            ->paginate(10);

        return Inertia::render('product-movement/Show', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }

    public function adjustment(Request $request, MProduct $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        $type = $validated['quantity'] > 0 ? 'adjustment' : 'adjustment';
        $quantity = abs($validated['quantity']);

        ProductMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'unit_id' => $product->base_unit_id,
            'note' => $validated['note'],
        ]);

        $product->stock += $validated['quantity'];
        $product->save();

        return redirect()->back()->with('success', 'Stock berhasil diadjust');
    }
}
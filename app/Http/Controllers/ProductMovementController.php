<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MProduct;
use App\Models\ProductMovement;
use App\Models\ProductUnitConverter;
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

        if ($validated['type'] === 'inbound' && !array_key_exists('price', $validated)) {
            $validated['price'] = null;
        }

        if ($validated['type'] === 'inbound' && $validated['price'] === null) {
            return back()->withErrors(['price' => 'Harga wajib diisi untuk inbound']);
        }

        $product->load('productUnitConverters', 'baseUnit');

        $allowedUnitIds = collect([$product->base_unit_id])
            ->merge($product->productUnitConverters->pluck('unit_from_id'))
            ->merge($product->productUnitConverters->pluck('unit_to_id'))
            ->unique()
            ->values();

        if (!$allowedUnitIds->contains((int) $validated['unit_id'])) {
            return back()->withErrors(['unit_id' => 'Unit tidak tersedia untuk produk ini']);
        }

        $convertToBase = function (float $qty, int $unitId) use ($product): float {
            if ((int) $unitId === (int) $product->base_unit_id) {
                return $qty;
            }

            $direct = $product->productUnitConverters
                ->first(fn (ProductUnitConverter $c) => (int) $c->unit_from_id === (int) $unitId && (int) $c->unit_to_id === (int) $product->base_unit_id);
            if ($direct) {
                return $qty * (float) $direct->multiplier;
            }

            $inverse = $product->productUnitConverters
                ->first(fn (ProductUnitConverter $c) => (int) $c->unit_from_id === (int) $product->base_unit_id && (int) $c->unit_to_id === (int) $unitId);
            if ($inverse) {
                $m = (float) $inverse->multiplier;
                if ($m == 0.0) {
                    throw new \RuntimeException('Multiplier tidak valid');
                }
                return $qty / $m;
            }

            throw new \RuntimeException('Konversi unit tidak ditemukan');
        };

        $convertFromBase = function (float $qtyBase, int $unitId) use ($product): float {
            if ((int) $unitId === (int) $product->base_unit_id) {
                return $qtyBase;
            }

            $direct = $product->productUnitConverters
                ->first(fn (ProductUnitConverter $c) => (int) $c->unit_from_id === (int) $product->base_unit_id && (int) $c->unit_to_id === (int) $unitId);
            if ($direct) {
                return $qtyBase * (float) $direct->multiplier;
            }

            $inverse = $product->productUnitConverters
                ->first(fn (ProductUnitConverter $c) => (int) $c->unit_from_id === (int) $unitId && (int) $c->unit_to_id === (int) $product->base_unit_id);
            if ($inverse) {
                $m = (float) $inverse->multiplier;
                if ($m == 0.0) {
                    throw new \RuntimeException('Multiplier tidak valid');
                }
                return $qtyBase / $m;
            }

            throw new \RuntimeException('Konversi unit tidak ditemukan');
        };

        $openingStockBase = (float) $product->stock;
        $qtyBase = $convertToBase((float) $validated['quantity'], (int) $validated['unit_id']);
        $qtyBaseRounded = round($qtyBase);

        if (abs($qtyBase - $qtyBaseRounded) > 0.000001) {
            return back()->withErrors(['quantity' => 'Konversi unit menghasilkan stok tidak bulat, periksa converter']);
        }

        $qtyBaseInt = (int) $qtyBaseRounded;
        $deltaBase = $validated['type'] === 'inbound' ? $qtyBaseInt : -$qtyBaseInt;

        if ($validated['type'] === 'outbound' && ($openingStockBase + $deltaBase) < 0) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi']);
        }

        $closingStockBase = $openingStockBase + $deltaBase;

        $openingStockSelected = round($convertFromBase($openingStockBase, (int) $validated['unit_id']), 2);
        $closingStockSelected = round($convertFromBase($closingStockBase, (int) $validated['unit_id']), 2);

        ProductMovement::create([
            'product_id' => $product->id,
            'type' => $validated['type'],
            'opening_stock' => $openingStockSelected,
            'quantity' => (int) $validated['quantity'],
            'closing_stock' => $closingStockSelected,
            'unit_id' => (int) $validated['unit_id'],
            'note' => $validated['note'],
            'price' => $validated['type'] === 'inbound' ? $validated['price'] : null,
        ]);

        $product->stock = (int) $closingStockBase;
        $product->save();

        return redirect()->back()->with('success', 'Stok berhasil diupdate');
    }
}

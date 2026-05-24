<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Helpers\S3Helper;
use App\Models\MCategory;
use App\Models\MProduct;
use App\Models\MUnit;
use App\Models\ProductCategory;
use App\Models\ProductUnitConverter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $products = MProduct::query()
            ->with('baseUnit')
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('master/product/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        $categories = MCategory::all();
        $units = MUnit::all();

        return Inertia::render('master/product/Create', [
            'categories' => $categories,
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'base_unit_id' => 'required|exists:m_units,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:m_products,sku',
            'price' => 'required|numeric|min:0',
            'avg_inbound_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'is_display' => 'required|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:m_categories,id',
            'unit_converters' => 'nullable|array',
            'unit_converters.*.unit_from_id' => 'required|exists:m_units,id',
            'unit_converters.*.unit_to_id' => 'required|exists:m_units,id',
            'unit_converters.*.multiplier' => 'required|numeric|min:0',
        ]);

        $product = MProduct::create($validated);

        if ($request->hasFile('image')) {
            $tempFileName = S3Helper::storeFileTemp($request->file('image'));
            try {
                S3Helper::storeFileToS3('products', $tempFileName);
                $product->img_url = S3Helper::getUrlFileS3('products', $tempFileName);
                $product->save();
            } finally {
                S3Helper::removeFileTemp($tempFileName);
            }
        }

        foreach ($validated['categories'] as $categoryId) {
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $categoryId,
            ]);
        }

        if (isset($validated['unit_converters'])) {
            foreach ($validated['unit_converters'] as $converter) {
                ProductUnitConverter::create([
                    'product_id' => $product->id,
                    'unit_from_id' => $converter['unit_from_id'],
                    'unit_to_id' => $converter['unit_to_id'],
                    'multiplier' => $converter['multiplier'],
                ]);
            }
        }

        toast('Produk berhasil ditambahkan');
        return redirect()->route('master.product.index');
    }

    public function edit(MProduct $product)
    {
        $categories = MCategory::all();
        $units = MUnit::all();
        $product->load(['categories', 'productUnitConverters.unitFrom', 'productUnitConverters.unitTo']);
        
        return Inertia::render('master/product/Edit', [
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
        ]);
    }

    public function update(Request $request, MProduct $product)
    {
        $validated = $request->validate([
            'base_unit_id' => 'required|exists:m_units,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:m_products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'avg_inbound_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_alert' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'is_display' => 'required|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:m_categories,id',
            'unit_converters' => 'nullable|array',
            'unit_converters.*.unit_from_id' => 'required|exists:m_units,id',
            'unit_converters.*.unit_to_id' => 'required|exists:m_units,id',
            'unit_converters.*.multiplier' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        if ($request->hasFile('image')) {
            $tempFileName = S3Helper::storeFileTemp($request->file('image'));
            try {
                S3Helper::storeFileToS3('products', $tempFileName);
                $product->img_url = S3Helper::getUrlFileS3('products', $tempFileName);
                $product->save();
            } finally {
                S3Helper::removeFileTemp($tempFileName);
            }
        }

        $product->categories()->sync($validated['categories']);

        ProductUnitConverter::where('product_id', $product->id)->delete();
        if (isset($validated['unit_converters'])) {
            foreach ($validated['unit_converters'] as $converter) {
                ProductUnitConverter::create([
                    'product_id' => $product->id,
                    'unit_from_id' => $converter['unit_from_id'],
                    'unit_to_id' => $converter['unit_to_id'],
                    'multiplier' => $converter['multiplier'],
                ]);
            }
        }

        toast('Produk berhasil diupdate');
        return redirect()->route('master.product.index');
    }

    public function destroy(MProduct $product)
    {
        $product->delete();
        toast('Produk berhasil dihapus');
        return redirect()->route('master.product.index');
    }
}

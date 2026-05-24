<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Helpers\S3Helper;
use App\Models\MPackage;
use App\Models\MProduct;
use App\Models\MPackage as Package;
use App\Models\MUnit;
use App\Models\PackageProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $packages = MPackage::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('master/package/Index', [
            'packages' => $packages,
        ]);
    }

    public function create()
    {
        $products = MProduct::with('baseUnit', 'productUnitConverters.unitFrom', 'productUnitConverters.unitTo')->get();
        $units = MUnit::all();

        return Inertia::render('master/package/Create', [
            'products' => $products,
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
            'price' => 'required|numeric|min:0',
            'is_grooming' => 'required|boolean',
            'description' => 'nullable|string',
            'products' => 'required_if:is_grooming,false|array',
            'products.*.product_id' => 'required|exists:m_products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_id' => 'required|exists:m_units,id',
        ]);

        $package = MPackage::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_grooming' => $validated['is_grooming'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('image')) {
            $tempFileName = S3Helper::storeFileTemp($request->file('image'));
            try {
                S3Helper::storeFileToS3('packages', $tempFileName);
                $package->img_url = S3Helper::getUrlFileS3('packages', $tempFileName);
                $package->save();
            } finally {
                S3Helper::removeFileTemp($tempFileName);
            }
        }

        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $productData) {
                PackageProduct::create([
                    'package_id' => $package->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_id' => $productData['unit_id'],
                ]);
            }
        }

        toast('Paket berhasil ditambahkan');
        return redirect()->route('master.package.index');
    }

    public function edit(MPackage $package)
    {
        $products = MProduct::with('baseUnit', 'productUnitConverters.unitFrom', 'productUnitConverters.unitTo')->get();
        $units = MUnit::all();
        $package->load(['products' => function ($query) {
            $query->withPivot('quantity', 'unit_id');
        }]);

        return Inertia::render('master/package/Edit', [
            'package' => $package,
            'products' => $products,
            'units' => $units,
        ]);
    }

    public function update(Request $request, MPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
            'price' => 'required|numeric|min:0',
            'is_grooming' => 'required|boolean',
            'description' => 'nullable|string',
            'products' => 'required_if:is_grooming,false|array',
            'products.*.product_id' => 'required|exists:m_products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_id' => 'required|exists:m_units,id',
        ]);

        $package->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_grooming' => $validated['is_grooming'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('image')) {
            $tempFileName = S3Helper::storeFileTemp($request->file('image'));
            try {
                S3Helper::storeFileToS3('packages', $tempFileName);
                $package->img_url = S3Helper::getUrlFileS3('packages', $tempFileName);
                $package->save();
            } finally {
                S3Helper::removeFileTemp($tempFileName);
            }
        }

        $package->products()->detach();
        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $productData) {
                PackageProduct::create([
                    'package_id' => $package->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_id' => $productData['unit_id'],
                ]);
            }
        }

        toast('Paket berhasil diupdate');
        return redirect()->route('master.package.index');
    }

    public function destroy(MPackage $package)
    {
        $package->delete();
        toast('Paket berhasil dihapus');
        return redirect()->route('master.package.index');
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MPackage;
use App\Models\MProduct;
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
        $products = MProduct::all();

        return Inertia::render('master/package/Create', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_grooming' => 'required|boolean',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'products.*' => 'exists:m_products,id',
        ]);

        $package = MPackage::create($validated);

        foreach ($validated['products'] as $productId) {
            PackageProduct::create([
                'package_id' => $package->id,
                'product_id' => $productId,
            ]);
        }

        return redirect()->route('master.package.index')->with('success', 'Paket berhasil ditambahkan');
    }

    public function edit(MPackage $package)
    {
        $products = MProduct::all();
        $package->load('products');

        return Inertia::render('master/package/Edit', [
            'package' => $package,
            'products' => $products,
        ]);
    }

    public function update(Request $request, MPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_grooming' => 'required|boolean',
            'description' => 'nullable|string',
            'products' => 'required|array',
            'products.*' => 'exists:m_products,id',
        ]);

        $package->update($validated);

        $package->products()->sync($validated['products']);

        return redirect()->route('master.package.index')->with('success', 'Paket berhasil diupdate');
    }

    public function destroy(MPackage $package)
    {
        $package->delete();

        return redirect()->route('master.package.index')->with('success', 'Paket berhasil dihapus');
    }
}
<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $categories = MCategory::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('master/category/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('master/category/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MCategory::create($validated);
        toast('Kategori berhasil ditambahkan');
        return redirect()->route('master.category.index');
    }

    public function edit(MCategory $category)
    {
        return Inertia::render('master/category/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, MCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);
        toast('Kategori berhasil diupdate');
        return redirect()->route('master.category.index');
    }

    public function destroy(MCategory $category)
    {
        $category->delete();
        toast('Kategori berhasil dihapus');
        return redirect()->route('master.category.index');
    }
}
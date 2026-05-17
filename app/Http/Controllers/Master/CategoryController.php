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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MCategory::create($validated);

        return redirect()->route('master.category.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, MCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('master.category.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(MCategory $category)
    {
        $category->delete();

        return redirect()->route('master.category.index')->with('success', 'Kategori berhasil dihapus');
    }
}
<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $units = MUnit::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        return Inertia::render('master/unit/Index', [
            'units' => $units,
        ]);
    }

    public function create()
    {
        return Inertia::render('master/unit/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MUnit::create($validated);
        toast('Satuan berhasil ditambahkan');
        return redirect()->route('master.unit.index');
    }

    public function edit(MUnit $unit)
    {
        return Inertia::render('master/unit/Edit', [
            'unit' => $unit,
        ]);
    }

    public function update(Request $request, MUnit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit->update($validated);
        toast('Satuan berhasil diupdate');
        return redirect()->route('master.unit.index');
    }

    public function destroy(MUnit $unit)
    {
        $unit->delete();
        toast('Satuan berhasil dihapus');
        return redirect()->route('master.unit.index');
    }
}
<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $marketplaces = Marketplace::all();
        return view('master.marketplaces.index', compact('marketplaces'));
    }

    public function create()
    {
        return view('master.marketplaces.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Marketplace::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('marketplaces.index')->with('success', 'Marketplace created successfully.');
    }

    public function edit(Marketplace $marketplace)
    {
        return view('master.marketplaces.form', compact('marketplace'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $marketplace->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('marketplaces.index')->with('success', 'Marketplace updated successfully.');
    }

    public function destroy(Marketplace $marketplace)
    {
        $marketplace->delete();
        return redirect()->route('marketplaces.index')->with('success', 'Marketplace deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\StoreCapital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreCapitalController extends Controller
{
    // Show running total and history
    public function index()
    {
        $total = StoreCapital::currentTotal();
        $history = StoreCapital::orderBy('created_at', 'desc')->get();
        return view('store-capital.index', compact('total', 'history'));
    }

    // Show form to add a transaction
    public function create()
    {
        return view('store-capital.create');
    }

    // Store a new capital transaction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);
        $validated['created_by'] = Auth::id();
        StoreCapital::create($validated);
        return redirect()->route('store-capital.index')->with('success', 'Capital transaction added successfully.');
    }
}

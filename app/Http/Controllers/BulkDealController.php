<?php

namespace App\Http\Controllers;

use App\Models\BulkDeal;
use Illuminate\Http\Request;

class BulkDealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bulkDeals = BulkDeal::withCount('cars')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('bulk-deals.index', compact('bulkDeals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bulk-deals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:bulk_deals,name',
            'description' => 'nullable|string',
            'total_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        BulkDeal::create($validated);

        return redirect()->route('bulk-deals.index')
            ->with('success', 'Bulk deal created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BulkDeal $bulkDeal)
    {
        $bulkDeal->load(['cars' => function($query) {
            $query->with(['options', 'inspection', 'statusHistories']);
        }]);
        
        return view('bulk-deals.show', compact('bulkDeal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BulkDeal $bulkDeal)
    {
        return view('bulk-deals.edit', compact('bulkDeal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BulkDeal $bulkDeal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:bulk_deals,name,' . $bulkDeal->id,
            'description' => 'nullable|string',
            'total_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        $bulkDeal->update($validated);

        return redirect()->route('bulk-deals.show', $bulkDeal)
            ->with('success', 'Bulk deal updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BulkDeal $bulkDeal)
    {
        // Check if bulk deal has cars
        if ($bulkDeal->cars()->count() > 0) {
            return redirect()->route('bulk-deals.index')
                ->with('error', 'Cannot delete bulk deal that has associated cars. Please remove cars first.');
        }

        $bulkDeal->delete();

        return redirect()->route('bulk-deals.index')
            ->with('success', 'Bulk deal deleted successfully!');
    }
}

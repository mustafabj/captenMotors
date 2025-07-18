<?php

namespace App\Http\Controllers;

use App\Models\OtherCost;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtherCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OtherCost::with(['car', 'user']);
        
        // Search by description or car model
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhereHas('car', function($carQuery) use ($search) {
                      $carQuery->where('model', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        
        $otherCosts = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('other-costs.index', compact('otherCosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cars = Car::orderBy('model')->get();
        return view('other-costs.create', compact('cars'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'category' => 'required|in:maintenance,repair,insurance,registration,fuel,other',
            'notes' => 'nullable|string'
        ]);

        $validated['user_id'] = Auth::id();

        OtherCost::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Other cost added successfully!'
            ]);
        }

        return redirect()->route('other-costs.index')->with('success', 'Other cost added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OtherCost $otherCost)
    {
        $otherCost->load(['car', 'user']);
        return view('other-costs.show', compact('otherCost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OtherCost $otherCost)
    {
        $cars = Car::orderBy('model')->get();
        return view('other-costs.edit', compact('otherCost', 'cars'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OtherCost $otherCost)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'category' => 'required|in:maintenance,repair,insurance,registration,fuel,other',
            'notes' => 'nullable|string'
        ]);

        $otherCost->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Other cost updated successfully!'
            ]);
        }

        return redirect()->route('other-costs.index')->with('success', 'Other cost updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OtherCost $otherCost)
    {
        $otherCost->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Other cost deleted successfully!'
            ]);
        }

        return redirect()->route('other-costs.index')->with('success', 'Other cost deleted successfully!');
    }

    /**
     * Transfer equipment cost to other costs
     */
    public function transferFromEquipmentCost(Request $request, $equipmentCostId)
    {
        $equipmentCost = \App\Models\CarEquipmentCost::findOrFail($equipmentCostId);
        
        $validated = $request->validate([
            'category' => 'required|in:maintenance,repair,insurance,registration,fuel,other',
            'notes' => 'nullable|string'
        ]);

        // Create other cost from equipment cost
        $otherCost = OtherCost::create([
            'car_id' => $equipmentCost->car_id,
            'user_id' => Auth::id(),
            'description' => $equipmentCost->description,
            'amount' => $equipmentCost->amount,
            'cost_date' => $equipmentCost->cost_date,
            'category' => $validated['category'],
            'notes' => $validated['notes'] ?? 'Transferred from equipment cost'
        ]);

        // Mark equipment cost as transferred
        $equipmentCost->update(['status' => 'transferred']);

        // Create notification for the original requester
        \App\Models\EquipmentCostNotification::createTransferNotification(
            $equipmentCost->car,
            $equipmentCost,
            $equipmentCost->user,
            Auth::user()
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Equipment cost transferred to other costs successfully!'
            ]);
        }

        return redirect()->route('other-costs.index')->with('success', 'Equipment cost transferred to other costs successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::with(['options', 'inspection', 'statusHistories', 'equipmentCosts'])->paginate(12);
        return view('cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'vehicle_category' => 'nullable|string|max:255',
            'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'place_of_manufacture' => 'nullable|string|max:255',
            'number_of_keys' => 'required|integer|min:1|max:10',
            'chassis_number' => 'required|string|unique:cars,chassis_number',
            'plate_number' => 'nullable|string|unique:cars,plate_number',
            'engine_capacity' => 'required|string|max:50',
            'engine_type' => 'nullable|string|max:50',
            'purchase_date' => 'required|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'insurance_expiry_date' => 'required|date|after:purchase_date',
            'expected_sale_price' => 'required|numeric|min:0',
            'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
        ]);

        $car = Car::create($validated);

        // Create initial status history entry
        $car->statusHistories()->create([
            'status' => $car->status,
            'notes' => 'Initial status set during car creation'
        ]);

        // Handle car options
        if ($request->has('options')) {
            foreach ($request->options as $optionName) {
                $car->options()->create(['name' => $optionName]);
            }
        }

        // Handle inspection data
        if ($request->filled('front_chassis_right') || $request->filled('transmission') || $request->filled('motor')) {
            $car->inspection()->create([
                'front_chassis_right' => $request->front_chassis_right,
                'front_chassis_left' => $request->front_chassis_left,
                'rear_chassis_right' => $request->rear_chassis_right,
                'rear_chassis_left' => $request->rear_chassis_left,
                'transmission' => $request->transmission,
                'motor' => $request->motor,
                'body_notes' => $request->body_notes,
            ]);
        }

        return redirect()->route('cars.show', $car)->with('success', 'Car created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        $car->load(['options', 'inspection', 'statusHistories', 'equipmentCosts']);
        return view('cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        $car->load(['options', 'inspection', 'statusHistories', 'equipmentCosts']);
        return view('cars.create', compact('car'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        $car->load(['options', 'inspection', 'statusHistories', 'equipmentCosts']);
        
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'vehicle_category' => 'nullable|string|max:255',
            'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'place_of_manufacture' => 'nullable|string|max:255',
            'number_of_keys' => 'required|integer|min:1|max:10',
            'chassis_number' => 'required|string|unique:cars,chassis_number,' . $car->id,
            'plate_number' => 'nullable|string|unique:cars,plate_number,' . $car->id,
            'engine_capacity' => 'required|string|max:50',
            'engine_type' => 'nullable|string|max:50',
            'purchase_date' => 'required|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'insurance_expiry_date' => 'required|date|after:purchase_date',
            'expected_sale_price' => 'required|numeric|min:0',
            'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
        ]);

        // Check if status has changed
        $statusChanged = $car->status !== $validated['status'];

        $car->update($validated);

        // Create status history entry if status changed
        if ($statusChanged) {
            $car->statusHistories()->create([
                'status' => $validated['status'],
                'notes' => $request->status_notes ?? 'Status updated'
            ]);
        }

        // Handle car options
        $car->options()->delete(); // Remove existing options
        if ($request->has('options')) {
            foreach ($request->options as $optionName) {
                $car->options()->create(['name' => $optionName]);
            }
        }

        // Handle inspection data
        if ($request->filled('front_chassis_right') || $request->filled('transmission') || $request->filled('motor')) {
            if ($car->inspection) {
                $car->inspection()->update([
                    'front_chassis_right' => $request->front_chassis_right,
                    'front_chassis_left' => $request->front_chassis_left,
                    'rear_chassis_right' => $request->rear_chassis_right,
                    'rear_chassis_left' => $request->rear_chassis_left,
                    'transmission' => $request->transmission,
                    'motor' => $request->motor,
                    'body_notes' => $request->body_notes,
                ]);
            } else {
                $car->inspection()->create([
                    'front_chassis_right' => $request->front_chassis_right,
                    'front_chassis_left' => $request->front_chassis_left,
                    'rear_chassis_right' => $request->rear_chassis_right,
                    'rear_chassis_left' => $request->rear_chassis_left,
                    'transmission' => $request->transmission,
                    'motor' => $request->motor,
                    'body_notes' => $request->body_notes,
                ]);
            }
        }

        return redirect()->route('cars.show', $car)->with('success', 'Car updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Car deleted successfully!');
    }

    /**
     * Add equipment cost to car
     */
    public function addEquipmentCost(Request $request, Car $car)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $car->equipmentCosts()->create($request->only(['description', 'amount', 'cost_date', 'notes']));

        return redirect()->route('cars.show', $car)->with('success', 'Equipment cost added successfully!');
    }

    /**
     * Update car status
     */
    public function updateStatus(Request $request, Car $car)
    {
        $request->validate([
            'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
            'notes' => 'nullable|string'
        ]);

        $car->update(['status' => $request->status]);

        // Create status history entry
        $car->statusHistories()->create([
            'status' => $request->status,
            'notes' => $request->notes ?? 'Status updated'
        ]);

        return redirect()->route('cars.show', $car)->with('success', 'Car status updated successfully!');
    }
}

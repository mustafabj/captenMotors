<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\BulkDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $bulkDeals = BulkDeal::where('status', 'active')
            ->withCount('cars')
            ->get();
        return view('cars.create', compact('bulkDeals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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
                'bulk_deal_id' => 'nullable|exists:bulk_deals,id',
                'car_license' => 'nullable|string',
                'car_images_data' => 'nullable|string',
            ]);

            return DB::transaction(function () use ($validated, $request) {
                $car = Car::create($validated);

                // Handle car license image (base64)
                if ($request->filled('car_license')) {
                    $base64Data = $request->car_license;
                    if (strpos($base64Data, 'data:image/') === 0) {
                        $car->addMediaFromBase64($base64Data)
                            ->toMediaCollection('car_license');
                    }
                }

                // Handle car images (base64 array)
                if ($request->filled('car_images_data')) {
                    $carImagesData = json_decode($request->car_images_data, true);
                    if (is_array($carImagesData)) {
                        foreach ($carImagesData as $base64Data) {
                            if (strpos($base64Data, 'data:image/') === 0) {
                                $car->addMediaFromBase64($base64Data)
                                    ->toMediaCollection('car_images');
                            }
                        }
                    }
                }

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
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while creating the car. Please try again.'])
                ->withInput();
        }
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
        try {
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
                'insurance_expiry_date' => 'required|date',
                'expected_sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
                'car_license' => 'nullable|string',
                'car_images_data' => 'nullable|string',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                $car->update($validated);

                // Handle car license image update
                if ($request->filled('car_license')) {
                    $base64Data = $request->car_license;
                    if (strpos($base64Data, 'data:image/') === 0) {
                        $car->clearMediaCollection('car_license');
                        $car->addMediaFromBase64($base64Data)
                            ->toMediaCollection('car_license');
                    }
                }

                // Handle car images update
                if ($request->filled('car_images_data')) {
                    $carImagesData = json_decode($request->car_images_data, true);
                    if (is_array($carImagesData)) {
                        $car->clearMediaCollection('car_images');
                        foreach ($carImagesData as $base64Data) {
                            if (strpos($base64Data, 'data:image/') === 0) {
                                $car->addMediaFromBase64($base64Data)
                                    ->toMediaCollection('car_images');
                            }
                        }
                    }
                }

                // Create status history entry if status changed
                if ($car->status !== $validated['status']) {
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
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the car. Please try again.'])
                ->withInput();
        }
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

    /**
     * Delete car image
     */
    public function deleteImage(Request $request, Car $car)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id'
        ]);

        $media = $car->media()->findOrFail($request->media_id);
        $media->delete();

        return redirect()->route('cars.show', $car)->with('success', 'Image deleted successfully!');
    }
}

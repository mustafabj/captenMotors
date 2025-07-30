<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\BulkDeal;
use App\Models\User;
use App\Models\EquipmentCostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CarStepRequest;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::with(['options', 'inspection', 'statusHistories', 'equipmentCosts', 'media'])->orderBy('id', 'desc')->paginate(12);
        return view('cars.index', compact('cars'));
    }

    /**
     * Search cars by chassis number or model via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('search');
        $status = $request->get('status');
        $year = $request->get('year');
        
        $cars = Car::with(['options', 'inspection', 'statusHistories', 'equipmentCosts', 'media'])
            ->when($query, function ($q) use ($query) {
                $q->where('model', 'like', '%' . $query . '%')
                  ->orWhere('plate_number', 'like', '%' . $query . '%');
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($year, function ($q) use ($year) {
                $q->where('manufacturing_year', $year);
            })
            ->orderBy('id', 'desc')
            ->paginate(12);
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('cars.partials.car-grid', compact('cars'))->render(),
                'pagination' => view('cars.partials.pagination', compact('cars'))->render()
            ]);
        }
        
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
                'color' => 'nullable|string|max:255',
                'odometer' => 'nullable|integer|min:0',
                'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'place_of_manufacture' => 'nullable|string|max:255',
                'number_of_keys' => 'nullable|integer|min:1|max:10',
                'plate_number' => 'nullable|string|unique:cars,plate_number',
                'engine_capacity' => 'nullable|string|max:50',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'insurance_expiry_date' => 'nullable|date',
                'expected_sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
                'bulk_deal_id' => 'nullable|exists:bulk_deals,id',
                'car_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'chassis_inspection' => 'nullable|string',
                'transmission' => 'nullable|string|max:255',
                'motor' => 'nullable|string|max:255',
                'body_notes' => 'nullable|string',
                'hood' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'trunk_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'options' => 'nullable|array',
                'options.*' => 'string|max:255',
                'all_options' => 'nullable|string',
                'purchase_costs' => 'nullable|array',
                'purchase_costs.*.description' => 'required|string|max:255',
                'purchase_costs.*.amount' => 'required|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validated, $request) {
                $car = Car::create($validated);

                // Handle car license image
                if ($request->hasFile('car_license')) {
                    $car->addMediaFromRequest('car_license')
                        ->toMediaCollection('car_license');
                }

                // Handle car images
                if ($request->hasFile('car_images')) {
                    foreach ($request->file('car_images') as $image) {
                        $car->addMedia($image)
                            ->toMediaCollection('car_images');
                    }
                }

                // Create initial status history entry
                $car->statusHistories()->create([
                    'status' => $car->status,
                    'notes' => 'Initial status set during car creation'
                ]);

                // Handle car options
                if ($request->filled('all_options')) {
                    // Parse the JSON string from all_options
                    $allOptions = json_decode($request->all_options, true);
                    if (is_array($allOptions)) {
                        foreach ($allOptions as $optionName) {
                            $car->options()->create(['name' => $optionName]);
                        }
                    }
                } elseif ($request->has('options')) {
                    // Fallback to the old options array if all_options is not provided
                    foreach ($request->options as $optionName) {
                        $car->options()->create(['name' => $optionName]);
                    }
                }

                // Handle inspection data
                if ($request->filled('chassis_inspection') || $request->filled('transmission') || $request->filled('motor') || 
                    $request->filled('hood') || $request->filled('front_right_fender') || $request->filled('front_left_fender') ||
                    $request->filled('rear_right_fender') || $request->filled('rear_left_fender') || $request->filled('trunk_door') ||
                    $request->filled('front_right_door') || $request->filled('rear_right_door') || $request->filled('front_left_door') ||
                    $request->filled('rear_left_door') || $request->filled('body_notes')) {
                    
                    // Handle checkbox logic for inspection fields
                    $chassis_inspection = $request->chassis_inspection;
                    if ($request->has('chassis_status')) {
                        $chassis_inspection = empty($chassis_inspection) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $chassis_inspection;
                    }

                    $transmission = $request->transmission;
                    if ($request->has('transmission_status')) {
                        $transmission = empty($transmission) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $transmission;
                    }

                    $motor = $request->motor;
                    if ($request->has('motor_status')) {
                        $motor = empty($motor) ? 'جيــــــــــــــــــــدة' : 'جيــــــــــــــــــــدة / ' . $motor;
                    }
                    if ($request->filled('motor_percentage')) {
                        $motor = $motor . ' - النسبة : ' . $request->motor_percentage . '%';
                    }

                    $body_notes = $request->body_notes;

                    $car->inspection()->create([
                        'chassis_inspection' => $chassis_inspection,
                        'transmission' => $transmission,
                        'motor' => $motor,
                        'body_notes' => $body_notes,
                        'hood' => $request->hood ?? 'clean_and_free_of_filler',
                        'front_right_fender' => $request->front_right_fender ?? 'clean_and_free_of_filler',
                        'front_left_fender' => $request->front_left_fender ?? 'clean_and_free_of_filler',
                        'rear_right_fender' => $request->rear_right_fender ?? 'clean_and_free_of_filler',
                        'rear_left_fender' => $request->rear_left_fender ?? 'clean_and_free_of_filler',
                        'trunk_door' => $request->trunk_door ?? 'clean_and_free_of_filler',
                        'front_right_door' => $request->front_right_door ?? 'clean_and_free_of_filler',
                        'rear_right_door' => $request->rear_right_door ?? 'clean_and_free_of_filler',
                        'front_left_door' => $request->front_left_door ?? 'clean_and_free_of_filler',
                        'rear_left_door' => $request->rear_left_door ?? 'clean_and_free_of_filler',
                    ]);
                }

                // Handle purchase costs
                if ($request->has('purchase_costs') && is_array($request->purchase_costs)) {
                    foreach ($request->purchase_costs as $cost) {
                        if (!empty($cost['description']) && !empty($cost['amount'])) {
                            $car->equipmentCosts()->create([
                                'description' => $cost['description'],
                                'amount' => $cost['amount'],
                                'cost_date' => $car->purchase_date,
                                'user_id' => auth()->id(),
                                'status' => 'approved' // Auto-approve purchase costs
                            ]);
                        }
                    }
                }

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Car created successfully!',
                        'redirect' => route('cars.show', $car)
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Car created successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Car creation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the car: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while creating the car: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        $car->load(['options', 'inspection', 'statusHistories', 'equipmentCosts.user']);
        return view('cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Car $car)
    {
        $car->load(['options', 'inspection', 'statusHistories', 'equipmentCosts']);
        $bulkDeals = BulkDeal::where('status', 'active')
            ->withCount('cars')
            ->get();
        return view('cars.create', compact('car', 'bulkDeals'));
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
                'color' => 'nullable|string|max:255',
                'odometer' => 'nullable|integer|min:0',
                'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'place_of_manufacture' => 'nullable|string|max:255',
                'number_of_keys' => 'nullable|integer|min:1|max:10',
                'plate_number' => 'nullable|string|unique:cars,plate_number,' . $car->id,
                'engine_capacity' => 'nullable|string|max:50',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'insurance_expiry_date' => 'nullable|date',
                'expected_sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
                'bulk_deal_id' => 'nullable|exists:bulk_deals,id',
                'car_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'chassis_inspection' => 'nullable|string',
                'transmission' => 'nullable|string|max:255',
                'motor' => 'nullable|string|max:255',
                'body_notes' => 'nullable|string',
                'hood' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'trunk_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'options' => 'nullable|array',
                'options.*' => 'string|max:255',
                'all_options' => 'nullable|string',
                'purchase_costs' => 'nullable|array',
                'purchase_costs.*.description' => 'required|string|max:255',
                'purchase_costs.*.amount' => 'required|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                $car->update($validated);

                // Handle car license image update
                if ($request->hasFile('car_license')) {
                    $car->clearMediaCollection('car_license');
                    $car->addMediaFromRequest('car_license')
                        ->toMediaCollection('car_license');
                }

                // Handle car images update
                if ($request->hasFile('car_images')) {
                    $car->clearMediaCollection('car_images');
                    foreach ($request->file('car_images') as $image) {
                        $car->addMedia($image)
                            ->toMediaCollection('car_images');
                    }
                }

                // Create status history entry if status changed
                if ($car->wasChanged('status')) {
                    $car->statusHistories()->create([
                        'status' => $validated['status'],
                        'notes' => $request->status_notes ?? 'Status updated'
                    ]);
                }

                // Update car options
                if ($request->filled('all_options')) {
                    $car->options()->delete();
                    $allOptions = json_decode($request->all_options, true);
                    if (is_array($allOptions)) {
                        foreach ($allOptions as $optionName) {
                            $car->options()->create(['name' => $optionName]);
                        }
                    }
                } elseif ($request->has('options')) {
                    $car->options()->delete(); // Remove existing options
                    foreach ($request->options as $optionName) {
                        $car->options()->create(['name' => $optionName]);
                    }
                }

                // Update inspection data
                if ($request->filled('chassis_inspection') || $request->filled('transmission') || $request->filled('motor') || 
                    $request->filled('hood') || $request->filled('front_right_fender') || $request->filled('front_left_fender') ||
                    $request->filled('rear_right_fender') || $request->filled('rear_left_fender') || $request->filled('trunk_door') ||
                    $request->filled('front_right_door') || $request->filled('rear_right_door') || $request->filled('front_left_door') ||
                    $request->filled('rear_left_door') || $request->filled('body_notes')) {
                    
                    // Handle checkbox logic for inspection fields
                    $chassis_inspection = $request->chassis_inspection;
                    if ($request->has('chassis_status')) {
                        $chassis_inspection = empty($chassis_inspection) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $chassis_inspection;
                    }

                    $transmission = $request->transmission;
                    if ($request->has('transmission_status')) {
                        $transmission = empty($transmission) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $transmission;
                    }

                    $motor = $request->motor;
                    if ($request->has('motor_status')) {
                        $motor = empty($motor) ? 'جيــــــــــــــــــــدة' : 'جيــــــــــــــــــــدة / ' . $motor;
                    }
                    if ($request->filled('motor_percentage')) {
                        $motor = $motor . ' - النسبة : ' . $request->motor_percentage . '%';
                    }

                    $body_notes = $request->body_notes;

                    $car->inspection()->updateOrCreate(
                        ['car_id' => $car->id],
                        [
                            'chassis_inspection' => $chassis_inspection,
                            'transmission' => $transmission,
                            'motor' => $motor,
                            'body_notes' => $body_notes,
                            'hood' => $request->hood ?? 'clean_and_free_of_filler',
                            'front_right_fender' => $request->front_right_fender ?? 'clean_and_free_of_filler',
                            'front_left_fender' => $request->front_left_fender ?? 'clean_and_free_of_filler',
                            'rear_right_fender' => $request->rear_right_fender ?? 'clean_and_free_of_filler',
                            'rear_left_fender' => $request->rear_left_fender ?? 'clean_and_free_of_filler',
                            'trunk_door' => $request->trunk_door ?? 'clean_and_free_of_filler',
                            'front_right_door' => $request->front_right_door ?? 'clean_and_free_of_filler',
                            'rear_right_door' => $request->rear_right_door ?? 'clean_and_free_of_filler',
                            'front_left_door' => $request->front_left_door ?? 'clean_and_free_of_filler',
                            'rear_left_door' => $request->rear_left_door ?? 'clean_and_free_of_filler',
                        ]
                    );
                }

                // Handle purchase costs (only add new ones, don't delete existing)
                if ($request->has('purchase_costs') && is_array($request->purchase_costs)) {
                    foreach ($request->purchase_costs as $cost) {
                        if (!empty($cost['description']) && !empty($cost['amount'])) {
                            $car->equipmentCosts()->create([
                                'description' => $cost['description'],
                                'amount' => $cost['amount'],
                                'cost_date' => $car->purchase_date,
                                'user_id' => auth()->id(),
                                'status' => 'approved' // Auto-approve purchase costs
                            ]);
                        }
                    }
                }

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Car updated successfully!',
                        'redirect' => route('cars.show', $car)
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Car updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Car update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the car: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the car: ' . $e->getMessage()])
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
    public function addEquipmentCost(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'cost_date' => 'required|date'
            ]);

            // Add the authenticated user's ID to the validated data
            $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();
            
            // Set status based on user role
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user->hasRole('admin')) {
                $validated['status'] = 'approved'; // Admin can approve immediately
            } else {
                $validated['status'] = 'pending'; // Regular users need approval
            }
            
            $cost = $car->equipmentCosts()->create($validated);
            $cost->load('user');

            // If user is not admin, create notifications for all admins
            if (!$user->hasRole('admin')) {
                $this->createApprovalNotifications($car, $cost, $user);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $user->hasRole('admin') ? 'Equipment cost added successfully!' : 'Equipment cost request submitted and pending approval!',
                    'cost' => [
                        'description' => $cost->description,
                        'amount' => $cost->amount,
                        'cost_date' => $cost->cost_date->format('Y-m-d'),
                        'user_name' => $cost->user->name,
                        'status' => $cost->status
                    ]
                ]);
            }

            return redirect()->route('cars.show', $car)->with('success', 
                $user->hasRole('admin') ? 'Equipment cost added successfully!' : 'Equipment cost request submitted and pending approval!'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Equipment cost creation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the cost.',
                    'errors' => ['general' => ['An error occurred while adding the cost.']]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while adding the cost.'])
                ->withInput();
        }
    }

    /**
     * Update car status
     */
    public function updateStatus(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        
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
    public function deleteImage(Request $request, $id)
    {
        $car = Car::findOrFail($id);
        
        $request->validate([
            'media_id' => 'required|exists:media,id'
        ]);

        $media = $car->media()->findOrFail($request->media_id);
        $media->delete();

        return redirect()->route('cars.show', $car)->with('success', 'Image deleted successfully!');
    }

    /**
     * Validate a specific step of the car creation form.
     */
    public function validateStep(CarStepRequest $request)
    {
        // If validation fails, Laravel will automatically return a 422 response with errors
        return response()->json([
            'success' => true,
            'message' => 'Step ' . $request->input('step') . ' validation passed'
        ]);
    }

    /**
     * Update car via AJAX for inline editing
     */
    public function updateInline(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'model' => 'required|string|max:255',
                'vehicle_category' => 'nullable|string|max:255',
                'manufacturing_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'place_of_manufacture' => 'nullable|string|max:255',
                'number_of_keys' => 'nullable|integer|min:1|max:10',
                'plate_number' => 'nullable|string|unique:cars,plate_number,' . $car->id,
                'engine_capacity' => 'nullable|string|max:50',
                'purchase_date' => 'required|date',
                'purchase_price' => 'required|numeric|min:0',
                'insurance_expiry_date' => 'nullable|date',
                'expected_sale_price' => 'required|numeric|min:0',
                'status' => 'required|in:not_received,paint,upholstery,mechanic,electrical,agency,polish,ready',
                'bulk_deal_id' => 'nullable|exists:bulk_deals,id',
                'color' => 'nullable|string|max:255',
                'odometer' => 'nullable|integer|min:0',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                $car->update($validated);

                // Create status history entry if status changed
                if ($car->wasChanged('status')) {
                    $car->statusHistories()->create([
                        'status' => $validated['status'],
                        'notes' => 'Status updated via inline edit'
                    ]);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Car updated successfully!',
                        'car' => [
                            'model' => $car->model,
                            'vehicle_category' => $car->vehicle_category,
                            'manufacturing_year' => $car->manufacturing_year,
                            'place_of_manufacture' => $car->place_of_manufacture,
                            'number_of_keys' => $car->number_of_keys,
                            'plate_number' => $car->plate_number,
                            'engine_capacity' => $car->engine_capacity,
                            'purchase_date' => $car->purchase_date->format('Y-m-d'),
                            'purchase_price' => $car->purchase_price,
                            'insurance_expiry_date' => $car->insurance_expiry_date ? $car->insurance_expiry_date->format('Y-m-d') : null,
                            'expected_sale_price' => $car->expected_sale_price,
                            'status' => $car->status,
                            'bulk_deal_id' => $car->bulk_deal_id,
                            'color' => $car->color,
                            'odometer' => $car->odometer,
                        ]
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Car updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Car inline update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'car_id' => $id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'An error occurred while updating the car: ' . $e->getMessage(),
                    'errors' => ['general' => ['An error occurred while updating the car: ' . $e->getMessage()]]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the car: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update car options via AJAX for inline editing
     */
    public function updateOptions(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'options' => 'nullable|array',
                'options.*' => 'nullable|string',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                // Delete existing options
                $car->options()->delete();

                // Get options array directly
                $options = $validated['options'] ?? [];

                // Add new options
                if (!empty($options)) {
                    foreach ($options as $optionName) {
                        if (!empty(trim($optionName))) {
                            $car->options()->create(['name' => trim($optionName)]);
                        }
                    }
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Car options updated successfully!',
                        'options' => $car->options()->pluck('name')->toArray()
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Car options updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Car options update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'car_id' => $id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the options: ' . $e->getMessage(),
                    'errors' => ['general' => ['An error occurred while updating the options: ' . $e->getMessage()]]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the options: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update car inspection via AJAX for inline editing
     */
    public function updateInspection(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'chassis_inspection' => 'nullable|string',
                'transmission' => 'nullable|string|max:255',
                'motor' => 'nullable|string|max:255',
                'body_notes' => 'nullable|string',
                'hood' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_fender' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'trunk_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_right_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'front_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
                'rear_left_door' => 'nullable|in:clean_and_free_of_filler,painted,fully_repainted',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                // Handle checkbox logic for inspection fields
                $chassis_inspection = $request->chassis_inspection;
                if ($request->has('chassis_status')) {
                    $chassis_inspection = empty($chassis_inspection) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $chassis_inspection;
                }

                $transmission = $request->transmission;
                if ($request->has('transmission_status')) {
                    $transmission = empty($transmission) ? 'جيــــــــــــــــــــد' : 'جيــــــــــــــــــــد / ' . $transmission;
                }

                $motor = $request->motor;
                if ($request->has('motor_status')) {
                    $motor = empty($motor) ? 'جيــــــــــــــــــــدة' : 'جيــــــــــــــــــــدة / ' . $motor;
                }
                if ($request->filled('motor_percentage')) {
                    $motor = $motor . ' - النسبة : ' . $request->motor_percentage . '%';
                }

                $body_notes = $request->body_notes;

                // Update or create inspection
                $inspectionData = array_merge($validated, [
                    'chassis_inspection' => $chassis_inspection,
                    'transmission' => $transmission,
                    'motor' => $motor,
                    'body_notes' => $body_notes,
                    'hood' => $request->hood ?? 'clean_and_free_of_filler',
                    'front_right_fender' => $request->front_right_fender ?? 'clean_and_free_of_filler',
                    'front_left_fender' => $request->front_left_fender ?? 'clean_and_free_of_filler',
                    'rear_right_fender' => $request->rear_right_fender ?? 'clean_and_free_of_filler',
                    'rear_left_fender' => $request->rear_left_fender ?? 'clean_and_free_of_filler',
                    'trunk_door' => $request->trunk_door ?? 'clean_and_free_of_filler',
                    'front_right_door' => $request->front_right_door ?? 'clean_and_free_of_filler',
                    'rear_right_door' => $request->rear_right_door ?? 'clean_and_free_of_filler',
                    'front_left_door' => $request->front_left_door ?? 'clean_and_free_of_filler',
                    'rear_left_door' => $request->rear_left_door ?? 'clean_and_free_of_filler',
                ]);
                
                $car->inspection()->updateOrCreate(
                    ['car_id' => $car->id],
                    $inspectionData
                );

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Inspection details updated successfully!',
                        'inspection' => $car->inspection()->first()
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Inspection details updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Car inspection update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'car_id' => $id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the inspection: ' . $e->getMessage(),
                    'errors' => ['general' => ['An error occurred while updating the inspection: ' . $e->getMessage()]]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the inspection: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update car financial information via AJAX for inline editing
     */
    public function updateFinancial(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'purchase_price' => 'required|numeric|min:0',
                'expected_sale_price' => 'required|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                $car->update($validated);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Financial information updated successfully!',
                        'financial' => [
                            'purchase_price' => $car->purchase_price,
                            'expected_sale_price' => $car->expected_sale_price,
                        ]
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Financial information updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the financial information. Please try again.',
                    'errors' => ['general' => ['An error occurred while updating the financial information.']]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the financial information. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Create approval notifications for admins
     */
    private function createApprovalNotifications($car, $cost, $user)
    {
        try {
            // Get all admin users
            $adminUsers = User::role('admin')->get();
            
            foreach ($adminUsers as $admin) {
                EquipmentCostNotification::createApprovalRequest($car, $cost, $user, $admin);
            }
            
            Log::info('Equipment cost approval notifications created successfully', [
                'car_id' => $car->id,
                'cost_id' => $cost->id,
                'admin_count' => $adminUsers->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create equipment cost approval notifications', [
                'error' => $e->getMessage(),
                'car_id' => $car->id,
                'cost_id' => $cost->id
            ]);
        }
    }

    /**
     * Update car images via AJAX for inline editing
     */
    public function updateImages(Request $request, $id)
    {
        try {
            $car = Car::findOrFail($id);
            
            $validated = $request->validate([
                'car_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'car_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            return DB::transaction(function () use ($validated, $request, $car) {
                // Handle car license image
                if ($request->hasFile('car_license')) {
                    // Remove existing license images
                    $car->clearMediaCollection('car_license');
                    // Add new license image
                    $car->addMediaFromRequest('car_license')
                        ->toMediaCollection('car_license');
                }

                // Handle car images
                if ($request->hasFile('car_images')) {
                    foreach ($request->file('car_images') as $image) {
                        $car->addMedia($image)
                            ->toMediaCollection('car_images');
                    }
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Images updated successfully!',
                        'images' => [
                            'car_license_count' => $car->getMedia('car_license')->count(),
                            'car_images_count' => $car->getMedia('car_images')->count(),
                        ]
                    ]);
                }

                return redirect()->route('cars.show', $car)->with('success', 'Images updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the images. Please try again.',
                    'errors' => ['general' => ['An error occurred while updating the images.']]
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['general' => 'An error occurred while updating the images. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the inspection report for a car
     */
    public function inspectionReport(Car $car)
    {
        $car->load(['inspection', 'options']);
        return view('cars.inspection-report', compact('car'));
    }
}

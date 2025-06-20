<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($car) ? 'Edit Car' : 'Add New Car' }} - Capten Motors</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ isset($car) ? 'Edit Car' : 'Add New Car' }}</h1>
                    <a href="{{ route('cars.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="step-indicator active" data-step="1">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                            <span class="text-sm font-medium text-blue-600">Basic Info</span>
                        </div>
                        <div class="step-indicator" data-step="2">
                            <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                            <span class="text-sm font-medium text-gray-500">Options</span>
                        </div>
                        <div class="step-indicator" data-step="3">
                            <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                            <span class="text-sm font-medium text-gray-500">Inspection</span>
                        </div>
                        <div class="step-indicator" data-step="4">
                            <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">4</div>
                            <span class="text-sm font-medium text-gray-500">Review</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 25%"></div>
                </div>
            </div>
        </div>

        <!-- Wizard Form -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ isset($car) ? route('cars.update', $car) : route('cars.store') }}" method="POST" id="car-wizard-form">
                @csrf
                @if(isset($car))
                    @method('PUT')
                @endif
                
                <!-- Step 1: Basic Information -->
                <div class="step-content" id="step-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Car Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Car Model *</label>
                                <input type="text" name="model" id="model" value="{{ old('model', $car->model ?? '') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('model')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_category" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Category</label>
                                <select name="vehicle_category" id="vehicle_category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Category</option>
                                    <option value="Sedan" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                    <option value="SUV" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'SUV' ? 'selected' : '' }}>SUV</option>
                                    <option value="Hatchback" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                                    <option value="Coupe" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Coupe' ? 'selected' : '' }}>Coupe</option>
                                    <option value="Wagon" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Wagon' ? 'selected' : '' }}>Wagon</option>
                                    <option value="Convertible" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Convertible' ? 'selected' : '' }}>Convertible</option>
                                    <option value="Pickup" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                                    <option value="Van" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Van' ? 'selected' : '' }}>Van</option>
                                    <option value="Sports Car" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Sports Car' ? 'selected' : '' }}>Sports Car</option>
                                    <option value="Luxury Car" {{ old('vehicle_category', $car->vehicle_category ?? '') == 'Luxury Car' ? 'selected' : '' }}>Luxury Car</option>
                                </select>
                                @error('vehicle_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="manufacturing_year" class="block text-sm font-medium text-gray-700 mb-2">Manufacturing Year *</label>
                                <input type="number" name="manufacturing_year" id="manufacturing_year" value="{{ old('manufacturing_year', $car->manufacturing_year ?? '') }}" min="1900" max="{{ date('Y') + 1 }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('manufacturing_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="place_of_manufacture" class="block text-sm font-medium text-gray-700 mb-2">Place of Manufacture</label>
                                <input type="text" name="place_of_manufacture" id="place_of_manufacture" value="{{ old('place_of_manufacture', $car->place_of_manufacture ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Japan, Germany, USA">
                                @error('place_of_manufacture')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="number_of_keys" class="block text-sm font-medium text-gray-700 mb-2">Number of Keys *</label>
                                <input type="number" name="number_of_keys" id="number_of_keys" value="{{ old('number_of_keys', $car->number_of_keys ?? '') }}" min="1" max="10" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('number_of_keys')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-2">Chassis Number *</label>
                                <input type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number', $car->chassis_number ?? '') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="17-character VIN">
                                @error('chassis_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $car->plate_number ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., AB123CD">
                                @error('plate_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="engine_capacity" class="block text-sm font-medium text-gray-700 mb-2">Engine Capacity *</label>
                                <input type="text" name="engine_capacity" id="engine_capacity" value="{{ old('engine_capacity', $car->engine_capacity ?? '') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 2.0L">
                                @error('engine_capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="engine_type" class="block text-sm font-medium text-gray-700 mb-2">Engine Type</label>
                                <select name="engine_type" id="engine_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Engine Type</option>
                                    <option value="Gasoline" {{ old('engine_type', $car->engine_type ?? '') == 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                    <option value="Diesel" {{ old('engine_type', $car->engine_type ?? '') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="Hybrid" {{ old('engine_type', $car->engine_type ?? '') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                    <option value="Electric" {{ old('engine_type', $car->engine_type ?? '') == 'Electric' ? 'selected' : '' }}>Electric</option>
                                    <option value="Plug-in Hybrid" {{ old('engine_type', $car->engine_type ?? '') == 'Plug-in Hybrid' ? 'selected' : '' }}>Plug-in Hybrid</option>
                                </select>
                                @error('engine_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Financial Information -->
                <div class="step-content hidden" id="step-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Financial Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date *</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', isset($car) ? $car->purchase_date->format('Y-m-d') : '') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase Price</label>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $car->purchase_price ?? '') }}" min="0" step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                                @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="insurance_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Insurance Expiry Date *</label>
                                <input type="date" name="insurance_expiry_date" id="insurance_expiry_date" value="{{ old('insurance_expiry_date', isset($car) ? $car->insurance_expiry_date->format('Y-m-d') : '') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('insurance_expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expected_sale_price" class="block text-sm font-medium text-gray-700 mb-2">Expected Sale Price *</label>
                                <input type="number" name="expected_sale_price" id="expected_sale_price" value="{{ old('expected_sale_price', $car->expected_sale_price ?? '') }}" min="0" step="0.01" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                                @error('expected_sale_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Status</option>
                                    <option value="not_received" {{ old('status', $car->status ?? '') == 'not_received' ? 'selected' : '' }}>Not Received</option>
                                    <option value="paint" {{ old('status', $car->status ?? '') == 'paint' ? 'selected' : '' }}>Paint</option>
                                    <option value="upholstery" {{ old('status', $car->status ?? '') == 'upholstery' ? 'selected' : '' }}>Upholstery</option>
                                    <option value="mechanic" {{ old('status', $car->status ?? '') == 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                                    <option value="electrical" {{ old('status', $car->status ?? '') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                    <option value="agency" {{ old('status', $car->status ?? '') == 'agency' ? 'selected' : '' }}>Agency</option>
                                    <option value="polish" {{ old('status', $car->status ?? '') == 'polish' ? 'selected' : '' }}>Polish</option>
                                    <option value="ready" {{ old('status', $car->status ?? '') == 'ready' ? 'selected' : '' }}>Ready</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(isset($car))
                            <div>
                                <label for="status_notes" class="block text-sm font-medium text-gray-700 mb-2">Status Change Notes</label>
                                <textarea name="status_notes" id="status_notes" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Optional notes for status change"></textarea>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Step 3: Car Options -->
                <div class="step-content hidden" id="step-3">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Car Options & Features</h2>
                        <p class="text-gray-600 mb-6">Select the options and features available on this car.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                                $carOptions = [
                                    'Leather Seats', 'Sunroof', 'Navigation System', 'Bluetooth',
                                    'Backup Camera', 'Heated Seats', 'Ventilated Seats', 'Premium Audio',
                                    'Alloy Wheels', 'LED Headlights', 'Cruise Control', 'Keyless Entry',
                                    'Push Button Start', 'Dual Zone Climate Control', 'Power Windows',
                                    'Power Locks', 'Fog Lights', 'Spoiler', 'Tinted Windows'
                                ];
                                
                                $existingOptions = isset($car) ? $car->options->pluck('name')->toArray() : [];
                            @endphp
                            
                            @foreach($carOptions as $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="options[]" value="{{ $option }}" 
                                        {{ in_array($option, old('options', $existingOptions)) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-3 text-sm font-medium text-gray-900">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Step 4: Inspection Details -->
                <div class="step-content hidden" id="step-4">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Inspection Details</h2>
                        <p class="text-gray-600 mb-6">Provide inspection information for this car (optional).</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="front_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Front Chassis Right</label>
                                <select name="front_chassis_right" id="front_chassis_right"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Condition</option>
                                    <option value="Good" {{ old('front_chassis_right', $car->inspection->front_chassis_right ?? '') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Minor Damage" {{ old('front_chassis_right', $car->inspection->front_chassis_right ?? '') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                    <option value="Repaired" {{ old('front_chassis_right', $car->inspection->front_chassis_right ?? '') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                    <option value="Excellent" {{ old('front_chassis_right', $car->inspection->front_chassis_right ?? '') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Needs Attention" {{ old('front_chassis_right', $car->inspection->front_chassis_right ?? '') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>

                            <div>
                                <label for="front_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Front Chassis Left</label>
                                <select name="front_chassis_left" id="front_chassis_left"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Condition</option>
                                    <option value="Good" {{ old('front_chassis_left', $car->inspection->front_chassis_left ?? '') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Minor Damage" {{ old('front_chassis_left', $car->inspection->front_chassis_left ?? '') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                    <option value="Repaired" {{ old('front_chassis_left', $car->inspection->front_chassis_left ?? '') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                    <option value="Excellent" {{ old('front_chassis_left', $car->inspection->front_chassis_left ?? '') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Needs Attention" {{ old('front_chassis_left', $car->inspection->front_chassis_left ?? '') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>

                            <div>
                                <label for="rear_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Rear Chassis Right</label>
                                <select name="rear_chassis_right" id="rear_chassis_right"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Condition</option>
                                    <option value="Good" {{ old('rear_chassis_right', $car->inspection->rear_chassis_right ?? '') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Minor Damage" {{ old('rear_chassis_right', $car->inspection->rear_chassis_right ?? '') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                    <option value="Repaired" {{ old('rear_chassis_right', $car->inspection->rear_chassis_right ?? '') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                    <option value="Excellent" {{ old('rear_chassis_right', $car->inspection->rear_chassis_right ?? '') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Needs Attention" {{ old('rear_chassis_right', $car->inspection->rear_chassis_right ?? '') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>

                            <div>
                                <label for="rear_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Rear Chassis Left</label>
                                <select name="rear_chassis_left" id="rear_chassis_left"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Condition</option>
                                    <option value="Good" {{ old('rear_chassis_left', $car->inspection->rear_chassis_left ?? '') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Minor Damage" {{ old('rear_chassis_left', $car->inspection->rear_chassis_left ?? '') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                    <option value="Repaired" {{ old('rear_chassis_left', $car->inspection->rear_chassis_left ?? '') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                    <option value="Excellent" {{ old('rear_chassis_left', $car->inspection->rear_chassis_left ?? '') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Needs Attention" {{ old('rear_chassis_left', $car->inspection->rear_chassis_left ?? '') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>

                            <div>
                                <label for="transmission" class="block text-sm font-medium text-gray-700 mb-2">Transmission</label>
                                <select name="transmission" id="transmission"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Type</option>
                                    <option value="Automatic" {{ old('transmission', $car->inspection->transmission ?? '') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                    <option value="Manual" {{ old('transmission', $car->inspection->transmission ?? '') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="CVT" {{ old('transmission', $car->inspection->transmission ?? '') == 'CVT' ? 'selected' : '' }}>CVT</option>
                                    <option value="Semi-Automatic" {{ old('transmission', $car->inspection->transmission ?? '') == 'Semi-Automatic' ? 'selected' : '' }}>Semi-Automatic</option>
                                </select>
                            </div>

                            <div>
                                <label for="motor" class="block text-sm font-medium text-gray-700 mb-2">Motor Condition</label>
                                <select name="motor" id="motor"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Condition</option>
                                    <option value="Excellent" {{ old('motor', $car->inspection->motor ?? '') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Good" {{ old('motor', $car->inspection->motor ?? '') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ old('motor', $car->inspection->motor ?? '') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Needs Service" {{ old('motor', $car->inspection->motor ?? '') == 'Needs Service' ? 'selected' : '' }}>Needs Service</option>
                                    <option value="Recently Serviced" {{ old('motor', $car->inspection->motor ?? '') == 'Recently Serviced' ? 'selected' : '' }}>Recently Serviced</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="body_notes" class="block text-sm font-medium text-gray-700 mb-2">Body Notes</label>
                                <textarea name="body_notes" id="body_notes" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Additional notes about the car's body condition">{{ old('body_notes', $car->inspection->body_notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button type="button" id="prev-btn" class="kt-btn kt-btn-outline" style="display: none;">
                        <i class="ki-filled ki-arrow-left"></i>
                        Previous
                    </button>
                    
                    <div class="flex gap-2">
                        <button type="button" id="next-btn" class="kt-btn kt-btn-primary">
                            Next
                            <i class="ki-filled ki-arrow-right"></i>
                        </button>
                        
                        <button type="submit" id="submit-btn" class="kt-btn kt-btn-success" style="display: none;">
                            <i class="ki-filled ki-check"></i>
                            {{ isset($car) ? 'Update Car' : 'Create Car' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Show current step
            document.getElementById(`step-${step}`).classList.remove('hidden');

            // Update navigation buttons
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
            nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
            submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';
        }

        document.getElementById('next-btn').addEventListener('click', function() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        document.getElementById('prev-btn').addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Initialize first step
        showStep(1);
    </script>
</body>
</html> 
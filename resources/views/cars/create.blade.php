@extends('layouts.app')

@section('content')
    <!-- Dropzone.js CDN -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <form action="{{ isset($car) ? route('cars.update', $car) : route('cars.store') }}" method="POST" id="car-form" enctype="multipart/form-data">
        @csrf
        @if(isset($car))
            @method('PUT')
        @endif
        
        <!-- General Error Messages -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="ki-filled ki-information-5 text-red-400"></i>
                        </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ isset($car) ? 'Edit Car' : 'Add New Car' }}</h1>
                <p class="text-sm text-gray-500">Home - Car Management - {{ isset($car) ? 'Edit Car' : 'Add Car' }}</p>
                </div>
            <div>
                <a href="{{ route('cars.index') }}" class="kt-btn kt-btn-outline">
                    Cancel
                </a>
                <button type="submit" class="kt-btn kt-btn-primary ml-2">
                    {{ isset($car) ? 'Update Car' : 'Create Car' }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
            <!-- Left Column -->
            <div class="lg:col-span-1 space-y-8 p-2" id="left-column">
                <!-- Status Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Status</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Car Status *</label>
                                <select name="status" id="status" required class="kt-select w-full">
                                    <option value="">Select Status</option>
                                    <option value="not_received" {{ old('status') == 'not_received' ? 'selected' : '' }}>Not Received</option>
                                    <option value="paint" {{ old('status') == 'paint' ? 'selected' : '' }}>Paint</option>
                                    <option value="upholstery" {{ old('status') == 'upholstery' ? 'selected' : '' }}>Upholstery</option>
                                    <option value="mechanic" {{ old('status') == 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                                    <option value="electrical" {{ old('status') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                    <option value="agency" {{ old('status') == 'agency' ? 'selected' : '' }}>Agency</option>
                                    <option value="polish" {{ old('status') == 'polish' ? 'selected' : '' }}>Polish</option>
                                    <option value="ready" {{ old('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                                </select>
                                <p class="text-sm text-gray-500 mt-2">Set the car status.</p>
                                @error('status')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bulk_deal_id" class="block text-sm font-medium text-gray-700 mb-2">Bulk Deal (Optional)</label>
                                <select name="bulk_deal_id" id="bulk_deal_id" class="kt-select w-full">
                                    <option value="">No Bulk Deal</option>
                                    @foreach($bulkDeals as $deal)
                                        <option value="{{ $deal->id }}" {{ old('bulk_deal_id') == $deal->id ? 'selected' : '' }}>
                                            {{ $deal->name }} ({{ $deal->cars_count ?? 0 }} cars)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-2">Associate this car with a bulk deal.</p>
                                @error('bulk_deal_id')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                            </div>

                <!-- Car Details Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Car Details</h3>
                    </div>
                    <div class="kt-card-content p-6 space-y-6">
                            <div>
                            <label for="vehicle_category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <input type="text" name="vehicle_category" id="vehicle_category" value="{{ old('vehicle_category') }}" 
                                class="kt-input w-full" placeholder="e.g., Sedan, SUV">
                            <p class="text-sm text-gray-500 mt-2">Add car to a category.</p>
                             @error('vehicle_category')
                                <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        <div>
                            <label for="options" class="block text-sm font-medium text-gray-700 mb-2">Features (Tags)</label>
                             <select
                                 class="kt-select"
                                 data-kt-select="true"
                                 data-kt-select-multiple="true"
                                 data-kt-select-placeholder="Select car features..."
                                 name="options[]"
                                 multiple
                             >
                                 @php
                                     $carOptions = [
                                         'Leather Seats', 'Sunroof', 'Navigation System', 'Bluetooth',
                                         'Backup Camera', 'Heated Seats', 'Ventilated Seats', 'Premium Audio',
                                         'Alloy Wheels', 'LED Headlights', 'Cruise Control', 'Keyless Entry',
                                         'Push Button Start', 'Dual Zone Climate Control', 'Power Windows',
                                         'Power Locks', 'Fog Lights', 'Spoiler', 'Tinted Windows'
                                     ];
                                     $existingOptions = old('options', []);
                                 @endphp
                                 
                                 @foreach($carOptions as $option)
                                     <option value="{{ $option }}" 
                                         {{ in_array($option, $existingOptions) ? 'selected' : '' }}>
                                         {{ $option }}
                                     </option>
                                 @endforeach
                             </select>
                            <p class="text-sm text-gray-500 mt-2">Add features to the car.</p>
                        </div>
                    </div>
                </div>

                <!-- Insurance & Purchase Date Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Important Dates</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date *</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" required
                                    class="kt-input w-full">
                                @error('purchase_date')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="insurance_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Insurance Expiry Date *</label>
                                <input type="date" name="insurance_expiry_date" id="insurance_expiry_date" value="{{ old('insurance_expiry_date') }}" required
                                    class="kt-input w-full">
                                @error('insurance_expiry_date')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                            </div>

            <!-- Right Column -->
            <div class="lg:col-span-2 space-y-8" id="right-column">
                 <!-- General Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">General</h3>
                    </div>
                    <div class="kt-card-content p-6 space-y-6">
                            <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Car Model *</label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" required
                                class="kt-input w-full">
                            @error('model')
                                <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                            <h4 class="text-md font-semibold text-gray-800 mb-4 mt-4">Specifications</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                               <div>
                                    <label for="manufacturing_year" class="block text-sm font-medium text-gray-700 mb-2">Manufacturing Year *</label>
                                    <input type="number" name="manufacturing_year" id="manufacturing_year" value="{{ old('manufacturing_year') }}" 
                                        min="1900" max="{{ date('Y') + 1 }}" required class="kt-input w-full">
                                    @error('manufacturing_year')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-2">Chassis Number *</label>
                                    <input type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number') }}" required
                                        class="kt-input w-full font-mono">
                                    @error('chassis_number')
                                        <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="engine_capacity" class="block text-sm font-medium text-gray-700 mb-2">Engine Capacity *</label>
                                    <input type="text" name="engine_capacity" id="engine_capacity" value="{{ old('engine_capacity') }}" required
                                        class="kt-input w-full">
                                @error('engine_capacity')
                                        <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="engine_type" class="block text-sm font-medium text-gray-700 mb-2">Engine Type</label>
                                    <select name="engine_type" id="engine_type" class="kt-input w-full">
                                    <option value="">Select Engine Type</option>
                                        <option value="Gasoline" {{ old('engine_type') == 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                        <option value="Diesel" {{ old('engine_type') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                        <option value="Hybrid" {{ old('engine_type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        <option value="Electric" {{ old('engine_type') == 'Electric' ? 'selected' : '' }}>Electric</option>
                                        <option value="Plug-in Hybrid" {{ old('engine_type') == 'Plug-in Hybrid' ? 'selected' : '' }}>Plug-in Hybrid</option>
                                </select>
                                @error('engine_type')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="number_of_keys" class="block text-sm font-medium text-gray-700 mb-2">Number of Keys *</label>
                                    <input type="number" name="number_of_keys" id="number_of_keys" value="{{ old('number_of_keys') }}" 
                                        min="1" max="10" required class="kt-input w-full">
                                    @error('number_of_keys')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                    <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number') }}"
                                        class="kt-input w-full">
                                    @error('plate_number')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="place_of_manufacture" class="block text-sm font-medium text-gray-700 mb-2">Place of Manufacture</label>
                                    <input type="text" name="place_of_manufacture" id="place_of_manufacture" value="{{ old('place_of_manufacture') }}"
                                        class="kt-input w-full">
                                    @error('place_of_manufacture')
                                        <p class="error-message">{{ $message }}</p>
                                @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="kt-card mb-4">
                     <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Pricing</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase Price *</label>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" 
                                    min="0" step="0.01" required class="kt-input w-full">
                                 <p class="text-sm text-gray-500 mt-2">Set the car purchase price.</p>
                                @error('purchase_price')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="expected_sale_price" class="block text-sm font-medium text-gray-700 mb-2">Expected Sale Price *</label>
                                <input type="number" name="expected_sale_price" id="expected_sale_price" value="{{ old('expected_sale_price') }}" 
                                    min="0" step="0.01" required class="kt-input w-full">
                                <p class="text-sm text-gray-500 mt-2">Set the car expected sale price.</p>
                                @error('expected_sale_price')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                            </div>


                <!-- Images Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Car Images</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <div class="space-y-6">
                            <!-- Car License Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Car License Image</label>
                                @if(isset($car) && $car->getFirstMedia('car_license'))
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Current License Image:</p>
                                        <img src="{{ $car->getFirstMedia('car_license')->getUrl() }}" 
                                             alt="Current License" 
                                             class="w-32 h-24 object-cover rounded border">
                                    </div>
                                @endif
                                <div id="license-dropzone" class="dropzone-license border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-gray-400 transition-colors">
                                    <div class="dz-message">
                                        <i class="ki-filled ki-picture text-2xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">Drop license image here or click to upload</p>
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 2MB)</p>
                                    </div>
                                </div>
                                <input type="hidden" name="car_license" id="car_license_input">
                                @error('car_license')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Car Images -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Car Images</label>
                                @if(isset($car) && $car->getMedia('car_images')->count() > 0)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Current Car Images ({{ $car->getMedia('car_images')->count() }}):</p>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($car->getMedia('car_images') as $image)
                                                <div class="relative group">
                                                    <img src="{{ $image->getUrl() }}" 
                                                         alt="Car Image" 
                                                         class="w-full h-20 object-cover rounded border">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center">
                                                        <span class="text-white opacity-0 group-hover:opacity-100 text-xs">Existing</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div id="car-images-dropzone" class="dropzone-car-images border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-gray-400 transition-colors">
                                    <div class="dz-message">
                                        <i class="ki-filled ki-picture text-2xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">Drop car images here or click to upload</p>
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 2MB each)</p>
                                    </div>
                                </div>
                                <input type="hidden" name="car_images_data" id="car_images_input">
                                @error('car_images')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                @error('car_images.*')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inspection Card -->
                <div class="kt-card mb-4">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Inspection Details (Optional)</h3>
                    </div>
                     <div class="kt-card-content p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-md font-semibold mb-4 text-gray-800">Chassis</h5>
                                <div class="space-y-4">
                            <div>
                                <label for="front_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Front Chassis Right</label>
                                        <select name="front_chassis_right" id="front_chassis_right" class="kt-input w-full">
                                    <option value="">Select Condition</option>
                                            <option value="Good" {{ old('front_chassis_right') == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Minor Damage" {{ old('front_chassis_right') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                            <option value="Repaired" {{ old('front_chassis_right') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                            <option value="Excellent" {{ old('front_chassis_right') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="Needs Attention" {{ old('front_chassis_right') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>
                            <div>
                                <label for="front_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Front Chassis Left</label>
                                        <select name="front_chassis_left" id="front_chassis_left" class="kt-input w-full">
                                    <option value="">Select Condition</option>
                                            <option value="Good" {{ old('front_chassis_left') == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Minor Damage" {{ old('front_chassis_left') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                            <option value="Repaired" {{ old('front_chassis_left') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                            <option value="Excellent" {{ old('front_chassis_left') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="Needs Attention" {{ old('front_chassis_left') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>
                            <div>
                                <label for="rear_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Rear Chassis Right</label>
                                        <select name="rear_chassis_right" id="rear_chassis_right" class="kt-input w-full">
                                    <option value="">Select Condition</option>
                                            <option value="Good" {{ old('rear_chassis_right') == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Minor Damage" {{ old('rear_chassis_right') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                            <option value="Repaired" {{ old('rear_chassis_right') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                            <option value="Excellent" {{ old('rear_chassis_right') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="Needs Attention" {{ old('rear_chassis_right') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                            </div>
                            <div>
                                <label for="rear_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Rear Chassis Left</label>
                                        <select name="rear_chassis_left" id="rear_chassis_left" class="kt-input w-full">
                                    <option value="">Select Condition</option>
                                            <option value="Good" {{ old('rear_chassis_left') == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Minor Damage" {{ old('rear_chassis_left') == 'Minor Damage' ? 'selected' : '' }}>Minor Damage</option>
                                            <option value="Repaired" {{ old('rear_chassis_left') == 'Repaired' ? 'selected' : '' }}>Repaired</option>
                                            <option value="Excellent" {{ old('rear_chassis_left') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="Needs Attention" {{ old('rear_chassis_left') == 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                                    </div>
                                </div>
                            </div>
                             <div>
                                <h5 class="text-md font-semibold mb-4 text-gray-800">Mechanical</h5>
                                <div class="space-y-4">
                            <div>
                                <label for="transmission" class="block text-sm font-medium text-gray-700 mb-2">Transmission</label>
                                        <select name="transmission" id="transmission" class="kt-input w-full">
                                    <option value="">Select Type</option>
                                            <option value="Automatic" {{ old('transmission') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                            <option value="Manual" {{ old('transmission') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                            <option value="CVT" {{ old('transmission') == 'CVT' ? 'selected' : '' }}>CVT</option>
                                            <option value="Semi-Automatic" {{ old('transmission') == 'Semi-Automatic' ? 'selected' : '' }}>Semi-Automatic</option>
                                </select>
                            </div>
                            <div>
                                <label for="motor" class="block text-sm font-medium text-gray-700 mb-2">Motor Condition</label>
                                        <select name="motor" id="motor" class="kt-input w-full">
                                    <option value="">Select Condition</option>
                                            <option value="Excellent" {{ old('motor') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="Good" {{ old('motor') == 'Good' ? 'selected' : '' }}>Good</option>
                                            <option value="Fair" {{ old('motor') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                            <option value="Needs Service" {{ old('motor') == 'Needs Service' ? 'selected' : '' }}>Needs Service</option>
                                            <option value="Recently Serviced" {{ old('motor') == 'Recently Serviced' ? 'selected' : '' }}>Recently Serviced</option>
                                </select>
                            </div>
                                </div>
                                <div class="mt-6">
                                <label for="body_notes" class="block text-sm font-medium text-gray-700 mb-2">Body Notes</label>
                                <textarea name="body_notes" id="body_notes" rows="4"
                                        class="kt-input w-full" placeholder="Additional notes about the car's body condition">{{ old('body_notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </form>

    <script>
        // Add error styling CSS
        const style = document.createElement('style');
        style.textContent = `
            .error-message {
                color: #dc2626;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
            .error-input {
                border-color: #dc2626;
            }
            /* Dropzone custom styles */
            .dropzone {
                border: 2px dashed #e5e7eb;
                border-radius: 0.5rem;
                background: #f9fafb;
                padding: 1rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-start;
                align-items: flex-start;
                min-height: 150px;
            }
            .dropzone .dz-message {
                width: 100%;
                text-align: center;
                margin: 2rem 0;
            }
            .dropzone .dz-preview {
                position: relative;
                margin: 0.5rem;
                width: 120px;
                height: 120px;
                background: white;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .dropzone .dz-preview .dz-image {
                width: 120px;
                height: 120px;
            }
            .dropzone .dz-preview .dz-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .dropzone .dz-preview .dz-details,
            .dropzone .dz-preview .dz-progress,
            .dropzone .dz-preview .dz-success-mark,
            .dropzone .dz-preview .dz-error-mark {
                display: none;
            }
            .dropzone .dz-preview .dz-error-message {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(220, 38, 38, 0.8);
                color: white;
                font-size: 0.8rem;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem;
                text-align: center;
                opacity: 0;
                transition: opacity 0.3s;
            }
            .dropzone .dz-preview:hover .dz-error-message {
                opacity: 1;
            }
            .dropzone .dz-preview .dz-remove {
                position: absolute;
                top: 4px;
                right: 4px;
                width: 20px;
                height: 20px;
                background: rgba(0,0,0,0.6);
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 20px;
                font-size: 14px;
                font-weight: bold;
                text-decoration: none;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .dropzone .dz-preview:hover .dz-remove {
                opacity: 1;
            }
        `;
        document.head.appendChild(style);

        // Global variables to store files
        let licenseFile = null;
        let carImagesFiles = [];

        // Initialize Dropzone for License Image
        Dropzone.autoDiscover = false;
        
        const licenseDropzone = new Dropzone("#license-dropzone", {
            url: "#", // We'll handle upload manually
            maxFiles: 1,
            acceptedFiles: "image/jpeg,image/png,image/jpg",
            maxFilesize: 2, // 2MB
            addRemoveLinks: true,
            dictRemoveFile: "×",
            dictFileTooBig: "File is too big. Max filesize: 2MB.",
            dictInvalidFileType: "You can't upload files of this type.",
            init: function() {
                this.on("addedfile", function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                    licenseFile = file;
                    updateLicenseInput();
                });
                
                this.on("removedfile", function(file) {
                    if (licenseFile === file) {
                        licenseFile = null;
                        updateLicenseInput();
                    }
                });
            }
        });

        // Initialize Dropzone for Car Images
        const carImagesDropzone = new Dropzone("#car-images-dropzone", {
            url: "#", // We'll handle upload manually
            maxFiles: 10,
            acceptedFiles: "image/jpeg,image/png,image/jpg",
            maxFilesize: 2, // 2MB
            addRemoveLinks: true,
            dictRemoveFile: "×",
            dictFileTooBig: "File is too big. Max filesize: 2MB.",
            dictInvalidFileType: "You can't upload files of this type.",
            init: function() {
                this.on("addedfile", function(file) {
                    carImagesFiles.push(file);
                    updateCarImagesInput();
                });
                
                this.on("removedfile", function(file) {
                    const index = carImagesFiles.findIndex(f => f.upload.uuid === file.upload.uuid);
                    if (index > -1) {
                        carImagesFiles.splice(index, 1);
                        updateCarImagesInput();
                    }
                });
            }
        });

        // Update hidden inputs with file data
        function updateLicenseInput() {
            const input = document.getElementById('car_license_input');
            if (licenseFile) {
                // Convert file to base64 for form submission
                const reader = new FileReader();
                reader.onload = function(e) {
                    input.value = e.target.result;
                };
                reader.readAsDataURL(licenseFile);
            } else {
                input.value = '';
            }
        }

        function updateCarImagesInput() {
            const input = document.getElementById('car_images_input');
            if (carImagesFiles.length > 0) {
                // Convert files to base64 array
                const promises = carImagesFiles.map(file => {
                    return new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            resolve(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    });
                });
                
                Promise.all(promises).then(base64Files => {
                    input.value = JSON.stringify(base64Files);
                });
            } else {
                input.value = '';
            }
        }

        // Simple form validation
        document.getElementById('car-form').addEventListener('submit', function(e) {
            const requiredFields = [
                'model', 'manufacturing_year', 'chassis_number', 'engine_capacity', 'number_of_keys',
                'purchase_date', 'insurance_expiry_date', 'expected_sale_price', 'status'
            ];
            
            let isValid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    const value = field.value.trim();
                    
                    if (!value) {
                        field.classList.add('border-red-500', 'error-input');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.classList.remove('border-red-500', 'error-input');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields before submitting.');
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalidField.focus();
                }
            }
        });

        // Real-time validation on input change
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('kt-input')) {
                e.target.classList.remove('border-red-500', 'error-input');
            }
        });

        // Apply error styling to inputs with errors on page load
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(function(errorMsg) {
                const inputId = errorMsg.previousElementSibling?.id || 
                               errorMsg.previousElementSibling?.previousElementSibling?.id;
                if (inputId) {
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.classList.add('error-input');
                    }
                }
            });
        });
    </script>
@endsection
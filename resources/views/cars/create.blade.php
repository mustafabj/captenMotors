@extends('layouts.app')

@section('content')
    <!-- FilePond CSS and JS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <form action="{{ route('cars.store') }}" method="POST" id="car-form" enctype="multipart/form-data">
        @csrf

        <!-- General Error Messages -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="ki-filled ki-information-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <div>
                <h1 class="text-2xl font-bold">Add New Car</h1>
                <p class="text-sm text-gray-500">Home - Car Management - Add Car</p>
            </div>
            <div>
                <a href="{{ route('cars.index') }}" class="kt-btn kt-btn-outline">
                    Cancel
                </a>
                <button type="submit" class="kt-btn kt-btn-primary ml-2">
                    Create Car
                </button>
            </div>
        </div>

        <!-- Wizard Progress -->
        <div class="kt-card mb-6">
            <div class="kt-card-content p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="step-indicator active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Basic Info</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Specifications</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Pricing & Status</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Images</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="5">
                            <div class="step-number">5</div>
                            <div class="step-label">Inspection</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        Step <span id="current-step">1</span> of 5
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Navigation -->
        <div class="flex justify-between items-center mb-3 mt-3" id="step-navigation">
            <button type="button" class="kt-btn kt-btn-outline" id="prev-btn" disabled>
                <i class="ki-duotone ki-arrow-left fs-2"></i>
                Previous
            </button>
            <button type="button" class="kt-btn kt-btn-primary" id="next-btn">
                Next
                <i class="ki-duotone ki-arrow-right fs-2"></i>
            </button>
        </div>

        <!-- Step 1: Basic Information -->
        <div class="step-content active" data-step="1">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Car Model & Category</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Car Model *</label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" required
                                class="kt-input w-full" placeholder="e.g., Toyota Camry">
                            @error('model')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vehicle_category"
                                class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <input type="text" name="vehicle_category" id="vehicle_category"
                                value="{{ old('vehicle_category') }}" class="kt-input w-full"
                                placeholder="e.g., Sedan, SUV">
                            @error('vehicle_category')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate
                                Number</label>
                            <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number') }}"
                                class="kt-input w-full">
                            @error('plate_number')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Important Dates</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date
                                *</label>
                            <input type="date" name="purchase_date" id="purchase_date"
                                value="{{ old('purchase_date') }}" required class="kt-input w-full">
                            @error('purchase_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="insurance_expiry_date"
                                class="block text-sm font-medium text-gray-700 mb-2">Insurance Expiry Date *</label>
                            <input type="date" name="insurance_expiry_date" id="insurance_expiry_date"
                                value="{{ old('insurance_expiry_date') }}" required class="kt-input w-full">
                            @error('insurance_expiry_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Specifications -->
        <div class="step-content" data-step="2">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Technical Specifications</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="manufacturing_year"
                                class="block text-sm font-medium text-gray-700 mb-2">Manufacturing Year *</label>
                            <input type="number" name="manufacturing_year" id="manufacturing_year"
                                value="{{ old('manufacturing_year') }}" min="1900" max="{{ date('Y') + 1 }}"
                                required class="kt-input w-full">
                            @error('manufacturing_year')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-2">Chassis
                                Number *</label>
                            <input type="text" name="chassis_number" id="chassis_number"
                                value="{{ old('chassis_number') }}" required class="kt-input w-full font-mono">
                            @error('chassis_number')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="engine_capacity" class="block text-sm font-medium text-gray-700 mb-2">Engine
                                Capacity *</label>
                            <input type="text" name="engine_capacity" id="engine_capacity"
                                value="{{ old('engine_capacity') }}" required class="kt-input w-full">
                            @error('engine_capacity')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="engine_type" class="block text-sm font-medium text-gray-700 mb-2">Engine
                                Type</label>
                            <select name="engine_type" id="engine_type" class="kt-input w-full">
                                <option value="">Select Engine Type</option>
                                <option value="Gasoline" {{ old('engine_type') == 'Gasoline' ? 'selected' : '' }}>Gasoline
                                </option>
                                <option value="Diesel" {{ old('engine_type') == 'Diesel' ? 'selected' : '' }}>Diesel
                                </option>
                                <option value="Hybrid" {{ old('engine_type') == 'Hybrid' ? 'selected' : '' }}>Hybrid
                                </option>
                                <option value="Electric" {{ old('engine_type') == 'Electric' ? 'selected' : '' }}>Electric
                                </option>
                                <option value="Plug-in Hybrid"
                                    {{ old('engine_type') == 'Plug-in Hybrid' ? 'selected' : '' }}>Plug-in Hybrid</option>
                            </select>
                            @error('engine_type')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Additional Details</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="number_of_keys" class="block text-sm font-medium text-gray-700 mb-2">Number of
                                Keys *</label>
                            <input type="number" name="number_of_keys" id="number_of_keys"
                                value="{{ old('number_of_keys') }}" min="1" max="10" required
                                class="kt-input w-full">
                            @error('number_of_keys')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="place_of_manufacture" class="block text-sm font-medium text-gray-700 mb-2">Place
                                of Manufacture</label>
                            <input type="text" name="place_of_manufacture" id="place_of_manufacture"
                                value="{{ old('place_of_manufacture') }}" class="kt-input w-full">
                            @error('place_of_manufacture')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="options" class="block text-sm font-medium text-gray-700 mb-2">Features
                                (Tags)</label>
                            <select class="kt-select" data-kt-select="true" data-kt-select-multiple="true"
                                data-kt-select-placeholder="Select car features..." name="options[]" multiple>
                                @php
                                    $carOptions = [
                                        'Leather Seats',
                                        'Sunroof',
                                        'Navigation System',
                                        'Bluetooth',
                                        'Backup Camera',
                                        'Heated Seats',
                                        'Ventilated Seats',
                                        'Premium Audio',
                                        'Alloy Wheels',
                                        'LED Headlights',
                                        'Cruise Control',
                                        'Keyless Entry',
                                        'Push Button Start',
                                        'Dual Zone Climate Control',
                                        'Power Windows',
                                        'Power Locks',
                                        'Fog Lights',
                                        'Spoiler',
                                        'Tinted Windows',
                                    ];
                                    $existingOptions = old('options', []);
                                @endphp
                                @foreach ($carOptions as $option)
                                    <option value="{{ $option }}"
                                        {{ in_array($option, $existingOptions) ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Pricing & Status -->
        <div class="step-content" data-step="3">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Pricing Information</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase
                                Price *</label>
                            <div class="relative">
                                <input type="number" name="purchase_price" id="purchase_price"
                                    value="{{ old('purchase_price') }}" min="0" step="0.01" required
                                    class="kt-input w-full pl-8">
                            </div>
                            @error('purchase_price')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="expected_sale_price" class="block text-sm font-medium text-gray-700 mb-2">Expected
                                Sale Price *</label>
                            <div class="relative">
                                <input type="number" name="expected_sale_price" id="expected_sale_price"
                                    value="{{ old('expected_sale_price') }}" min="0" step="0.01" required
                                    class="kt-input w-full pl-8">
                            </div>
                            @error('expected_sale_price')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="ki-duotone ki-calculator text-blue-500 mr-2"></i>
                                <span class="text-sm font-medium text-blue-800">Profit Margin: <span
                                        id="profit-margin">0%</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Status & Deal</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Car Status
                                *</label>
                            <select name="status" id="status" required class="kt-select w-full">
                                <option value="">Select Status</option>
                                <option value="not_received" {{ old('status') == 'not_received' ? 'selected' : '' }}>Not
                                    Received</option>
                                <option value="paint" {{ old('status') == 'paint' ? 'selected' : '' }}>Paint</option>
                                <option value="upholstery" {{ old('status') == 'upholstery' ? 'selected' : '' }}>
                                    Upholstery</option>
                                <option value="mechanic" {{ old('status') == 'mechanic' ? 'selected' : '' }}>Mechanic
                                </option>
                                <option value="electrical" {{ old('status') == 'electrical' ? 'selected' : '' }}>
                                    Electrical</option>
                                <option value="agency" {{ old('status') == 'agency' ? 'selected' : '' }}>Agency
                                </option>
                                <option value="polish" {{ old('status') == 'polish' ? 'selected' : '' }}>Polish
                                </option>
                                <option value="ready" {{ old('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-2">Set the car status.</p>
                            @error('status')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bulk_deal_id" class="block text-sm font-medium text-gray-700 mb-2">Bulk Deal
                                (Optional)</label>
                            <select name="bulk_deal_id" id="bulk_deal_id" class="kt-select w-full">
                                <option value="">No Bulk Deal</option>
                                @foreach ($bulkDeals as $deal)
                                    <option value="{{ $deal->id }}"
                                        {{ old('bulk_deal_id') == $deal->id ? 'selected' : '' }}>
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
        </div>

        <!-- Step 4: Images -->
        <div class="step-content" data-step="4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Car License Image</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <!-- Car License Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Car License Image</label>
                            <input type="file" id="license-filepond" name="car_license"
                                accept="image/png, image/jpeg, image/jpg" data-max-file-size="2MB" data-max-files="1">
                            <!-- Hidden input for actual file submission -->
                            <input type="file" id="license-hidden" name="car_license" style="display: none;">
                            @error('car_license')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Car Images</h3>
                    </div>
                    <div class="kt-card-content p-6">
                        <!-- Car Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Car Images</label>
                            <input type="file" id="images-filepond" name="car_images[]" multiple
                                accept="image/png, image/jpeg, image/jpg" data-max-file-size="2MB" data-max-files="10">
                            <!-- Hidden input for actual file submission -->
                            <input type="file" id="images-hidden" name="car_images[]" multiple
                                style="display: none;">
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
        </div>

        <!-- Step 5: Inspection -->
        <div class="step-content" data-step="5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Chassis Inspection</h3>
                    </div>
                    <div class="kt-card-content p-6 space-y-4">
                        <div>
                            <label for="front_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Front
                                Chassis Right</label>
                            <input type="text" name="front_chassis_right" id="front_chassis_right"
                                class="kt-input w-full" value="{{ old('front_chassis_right') }}">
                        </div>
                        <div>
                            <label for="front_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Front
                                Chassis Left</label>
                            <input type="text" name="front_chassis_left" id="front_chassis_left"
                                class="kt-input w-full" value="{{ old('front_chassis_left') }}">
                        </div>
                        <div>
                            <label for="rear_chassis_right" class="block text-sm font-medium text-gray-700 mb-2">Rear
                                Chassis Right</label>
                            <input type="text" name="rear_chassis_right" id="rear_chassis_right"
                                class="kt-input w-full" value="{{ old('rear_chassis_right') }}">
                        </div>
                        <div>
                            <label for="rear_chassis_left" class="block text-sm font-medium text-gray-700 mb-2">Rear
                                Chassis Left</label>
                            <input type="text" name="rear_chassis_left" id="rear_chassis_left"
                                class="kt-input w-full" value="{{ old('rear_chassis_left') }}">
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Mechanical Inspection</h3>
                    </div>
                    <div class="kt-card-content p-6 space-y-4">
                        <div>
                            <label for="transmission" class="block text-sm font-medium text-gray-700 mb-2">Transmission
                                Condition</label>
                            <input type="text" name="transmission" id="transmission" class="kt-input w-full"
                                value="{{ old('transmission') }}" placeholder="Enter Transmission Type">
                        </div>
                        <div>
                            <label for="motor" class="block text-sm font-medium text-gray-700 mb-2">Motor
                                Condition</label>
                            <input type="text" name="motor" id="motor" class="kt-input w-full"
                                value="{{ old('motor') }}" placeholder="Enter Motor Condition">
                        </div>
                        <div>
                            <label for="body_notes" class="block text-sm font-medium text-gray-700 mb-2">Body
                                Notes</label>
                            <textarea name="body_notes" id="body_notes" class="kt-textarea"
                                placeholder="Additional notes about the car's body condition">{{ old('body_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .step-indicator.active .step-number {
            background-color: #3b82f6;
            color: white;
        }

        .step-indicator.completed .step-number {
            background-color: #10b981;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
        }

        .step-indicator.active .step-label {
            color: #3b82f6;
            font-weight: 600;
        }

        .step-line {
            width: 60px;
            height: 2px;
            background-color: #e5e7eb;
            margin: 0 16px;
        }

        .step-indicator.completed+.step-line {
            background-color: #10b981;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .error-input {
            border-color: #dc2626;
        }

        .filepond--root {
            margin-bottom: 1rem;
        }

        /* Validation states */
        .kt-input.border-red-500 {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 1px #dc2626;
        }

        .kt-input.border-green-500 {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 1px #10b981;
        }

        .kt-input.bg-red-50 {
            background-color: #fef2f2 !important;
        }

        .kt-input.bg-green-50 {
            background-color: #f0fdf4 !important;
        }

        .field-error {
            animation: slideIn 0.3s ease-out;
        }

        .field-success {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading state for validation */
        .validating {
            position: relative;
        }

        .validating::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }
    </style>

    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        let licensePond, imagesPond;
        let currentStep = 1;
        const totalSteps = 5;
        let validationTimeouts = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize FilePond
            licensePond = FilePond.create(document.querySelector('#license-filepond'), {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                maxFiles: 1,
                labelIdle: `Drag & Drop your license image or <span class="filepond--label-action">Browse</span><br><small>JPG, PNG (Max 2MB)</small>`,
                // Enable form submission
                allowReplace: false,
                allowRevert: false,
                instantUpload: false,
                // Add this to ensure files are included in form submission
                server: null,
                onaddfile: (error, file) => {
                    if (!error) {
                        console.log('License file added:', file.filename);
                    }
                }
            });

            imagesPond = FilePond.create(document.querySelector('#images-filepond'), {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                maxFiles: 10,
                allowMultiple: true,
                allowReorder: true,
                labelIdle: `Drag & Drop your car images or <span class="filepond--label-action">Browse</span><br><small>JPG, PNG (Max 2MB each, up to 10 images)</small>`,
                // Enable form submission
                allowReplace: false,
                allowRevert: false,
                instantUpload: false,
                // Add this to ensure files are included in form submission
                server: null,
                onaddfile: (error, file) => {
                    if (!error) {
                        console.log('Image file added:', file.filename);
                    }
                }
            });

            // Initialize real-time validation
            initializeRealTimeValidation();

            // Initialize profit margin calculator
            initializeProfitCalculator();

            // Add this for step navigation
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');

            nextBtn.addEventListener('click', function() {
                validateCurrentStep().then(isValid => {
                    if (isValid) {
                        goToStep(currentStep + 1);
                    }
                });
            });

            prevBtn.addEventListener('click', function() {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            });
        });

        // Real-time validation
        function initializeRealTimeValidation() {
            // Add input event listeners for real-time validation
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => validateField(input));
                input.addEventListener('input', debounce(() => validateField(input), 500));
            });

            // Special handling for date fields
            const purchaseDate = document.getElementById('purchase_date');
            const insuranceDate = document.getElementById('insurance_expiry_date');

            if (purchaseDate && insuranceDate) {
                purchaseDate.addEventListener('change', () => {
                    validateField(purchaseDate);
                    validateField(insuranceDate);
                });
                insuranceDate.addEventListener('change', () => {
                    validateField(purchaseDate);
                    validateField(insuranceDate);
                });
            }
        }

        function validateField(field) {
            const fieldName = field.name;
            const fieldValue = field.value;
            const step = getFieldStep(fieldName);

            // Skip validation for file fields in AJAX
            if (!fieldName || !step || field.type === 'file') return;

            // Clear previous timeout for this field
            if (validationTimeouts[fieldName]) {
                clearTimeout(validationTimeouts[fieldName]);
            }

            // Set new timeout for debounced validation
            validationTimeouts[fieldName] = setTimeout(() => {
                validateFieldAjax(fieldName, fieldValue, step);
            }, 300);
        }

        function getFieldStep(fieldName) {
            const step1Fields = ['model', 'vehicle_category', 'plate_number', 'purchase_date', 'insurance_expiry_date'];
            const step2Fields = ['manufacturing_year', 'chassis_number', 'engine_capacity', 'engine_type', 'number_of_keys',
                'place_of_manufacture', 'options'
            ];
            const step3Fields = ['purchase_price', 'expected_sale_price', 'status', 'bulk_deal_id'];
            const step4Fields = ['car_license', 'car_images'];
            const step5Fields = ['front_chassis_right', 'front_chassis_left', 'rear_chassis_right', 'rear_chassis_left',
                'transmission', 'motor', 'body_notes'
            ];

            if (step1Fields.includes(fieldName)) return 1;
            if (step2Fields.includes(fieldName)) return 2;
            if (step3Fields.includes(fieldName)) return 3;
            if (step4Fields.includes(fieldName)) return 4;
            if (step5Fields.includes(fieldName)) return 5;

            return null;
        }

        function validateFieldAjax(fieldName, fieldValue, step) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            // Add loading state
            field.classList.add('validating');

            const formData = new FormData();
            formData.append('step', step);
            formData.append('single_field', fieldName);
            formData.append(fieldName, fieldValue);

            // Add CSRF token
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            fetch('{{ route('cars.validate-step') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Remove loading state
                    field.classList.remove('validating');

                    if (data.success) {
                        showFieldSuccess(fieldName);
                    } else {
                        showFieldError(fieldName, data.errors[fieldName] ? data.errors[fieldName][0] :
                            'Validation failed');
                    }
                })
                .catch(error => {
                    // Remove loading state
                    field.classList.remove('validating');
                    console.error('Validation error:', error);
                    showFieldError(fieldName, 'Validation failed. Please try again.');
                });
        }

        function showFieldError(fieldName, message) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            // Remove success state
            field.classList.remove('border-green-500', 'bg-green-50');
            field.classList.add('border-red-500', 'bg-red-50');

            // Remove existing error message
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-red-600 text-sm mt-1 flex items-center';
            errorDiv.innerHTML = `
                <i class="ki-duotone ki-cross-circle text-red-500 mr-1"></i>
                ${message}
            `;
            field.parentNode.appendChild(errorDiv);
        }

        function showFieldSuccess(fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            // Remove error state
            field.classList.remove('border-red-500', 'bg-red-50');
            field.classList.add('border-green-500', 'bg-green-50');

            // Remove existing error message
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }

            // Remove existing success message
            const existingSuccess = field.parentNode.querySelector('.field-success');
            if (existingSuccess) {
                existingSuccess.remove();
            }

            // Add success message
            const successDiv = document.createElement('div');
            successDiv.className = 'field-success text-green-600 text-sm mt-1 flex items-center';
            successDiv.innerHTML = `
        <i class="ki-duotone ki-check-circle text-green-500 mr-1"></i>
        Valid
    `;
            field.parentNode.appendChild(successDiv);
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function initializeProfitCalculator() {
            const purchasePrice = document.getElementById('purchase_price');
            const expectedPrice = document.getElementById('expected_sale_price');
            const profitMargin = document.getElementById('profit-margin');

            function calculateProfit() {
                const purchase = parseFloat(purchasePrice.value) || 0;
                const expected = parseFloat(expectedPrice.value) || 0;

                if (purchase > 0 && expected > 0) {
                    const profit = expected - purchase;
                    const margin = (profit / purchase) * 100;
                    profitMargin.textContent = margin.toFixed(1) + '%';

                    // Color coding
                    if (margin > 20) {
                        profitMargin.className = 'text-green-600 font-bold';
                    } else if (margin > 10) {
                        profitMargin.className = 'text-yellow-600 font-bold';
                    } else {
                        profitMargin.className = 'text-red-600 font-bold';
                    }
                } else {
                    profitMargin.textContent = '0%';
                    profitMargin.className = '';
                }
            }

            purchasePrice.addEventListener('input', calculateProfit);
            expectedPrice.addEventListener('input', calculateProfit);
        }

        // Step validation before proceeding
        function validateCurrentStep() {
            const currentStepElement = document.querySelector(`.step-content[data-step="${currentStep}"]`);
            const fields = currentStepElement.querySelectorAll('input, select, textarea');
            const formData = new FormData();

            fields.forEach(field => {
                // Skip file fields in AJAX validation
                if (field.name && field.type !== 'file') {
                    formData.append(field.name, field.value);
                }
            });

            formData.append('step', currentStep);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            return fetch('{{ route('cars.validate-step') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        return true;
                    } else {
                        // Show all errors for the current step
                        Object.keys(data.errors).forEach(fieldName => {
                            showFieldError(fieldName, data.errors[fieldName][0]);
                        });
                        return false;
                    }
                })
                .catch(error => {
                    console.error('Step validation error:', error);
                    return false;
                });
        }

        // Form submission with step validation
        document.getElementById('car-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // For final submission, we need to submit the form normally to handle files
            // Only validate the current step, then submit
            validateCurrentStep().then(isValid => {
                if (isValid) {
                    // Debug: Check FilePond files before syncing
                    console.log('License Pond files:', licensePond.getFiles().length);
                    console.log('Images Pond files:', imagesPond.getFiles().length);

                    // Sync FilePond files with the form before submission
                    syncFilePondFiles();

                    // Use FormData to manually submit the form with files
                    const form = document.getElementById('car-form');
                    const formData = new FormData(form);

                    // Debug: Check what's in FormData
                    console.log('FormData entries:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key, value);
                    }

                    // Submit using fetch instead of form.submit()
                    fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else {
                                return response.text();
                            }
                        })
                        .then(html => {
                            if (html) {
                                // Replace the current page content
                                document.documentElement.innerHTML = html;
                            }
                        })
                        .catch(error => {
                            console.error('Form submission error:', error);
                            showNotification('An error occurred while submitting the form.', 'error');
                        });
                } else {
                    // Show error message
                    showNotification('Please correct the errors before submitting.', 'error');
                }
            });
        });

        // Function to sync FilePond files with form inputs
        function syncFilePondFiles() {
            // Sync license files to hidden input
            const licenseFiles = licensePond.getFiles();
            const licenseHiddenInput = document.querySelector('#license-hidden');

            if (licenseFiles.length > 0) {
                try {
                    // Create a new FileList-like object
                    const dt = new DataTransfer();
                    dt.items.add(licenseFiles[0].file);
                    licenseHiddenInput.files = dt.files;
                    console.log('License file synced successfully to hidden input');
                } catch (error) {
                    console.error('Error syncing license file:', error);
                }
            }

            // Sync car images to hidden input
            const imageFiles = imagesPond.getFiles();
            const imageHiddenInput = document.querySelector('#images-hidden');

            if (imageFiles.length > 0) {
                try {
                    // Create a new FileList-like object
                    const dt = new DataTransfer();
                    imageFiles.forEach(file => {
                        dt.items.add(file.file);
                    });
                    imageHiddenInput.files = dt.files;
                    console.log('Image files synced successfully to hidden input');
                } catch (error) {
                    console.error('Error syncing image files:', error);
                }
            }

            // Debug: Check if files are synced to hidden inputs
            console.log('License files synced to hidden input:', licenseHiddenInput.files.length);
            console.log('Image files synced to hidden input:', imageHiddenInput.files.length);

            // Additional debug: Check the actual files
            if (licenseHiddenInput.files.length > 0) {
                console.log('License file details:', {
                    name: licenseHiddenInput.files[0].name,
                    size: licenseHiddenInput.files[0].size,
                    type: licenseHiddenInput.files[0].type
                });
            }

            if (imageHiddenInput.files.length > 0) {
                console.log('Image files details:', Array.from(imageHiddenInput.files).map(f => ({
                    name: f.name,
                    size: f.size,
                    type: f.type
                })));
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'success' ? 'bg-green-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // You need a goToStep function to handle step switching:
        function goToStep(step) {
            if (step < 1 || step > totalSteps) return;

            // Hide current step
            document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
            // Show new step
            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');

            // Update indicators
            updateStepIndicators(step);

            // Update navigation
            updateNavigation(step);

            currentStep = step;
        }

        function updateStepIndicators(step) {
            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                const stepNum = index + 1;
                indicator.classList.remove('active', 'completed');
                if (stepNum < step) {
                    indicator.classList.add('completed');
                } else if (stepNum === step) {
                    indicator.classList.add('active');
                }
            });
            document.getElementById('current-step').textContent = step;
        }

        function updateNavigation(step) {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.querySelector('button[type="submit"]');
            prevBtn.disabled = step === 1;
            nextBtn.style.display = step === totalSteps ? 'none' : 'inline-flex';
            submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';
        }
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <!-- FilePond CSS and JS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
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
        @endif

    <form action="{{ route('cars.store') }}" method="POST" id="car-form" enctype="multipart/form-data" 
          data-validate-url="{{ route('cars.validate-step') }}">
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
                <button type="submit" class="kt-btn kt-btn-primary ml-2" id="submit-btn">
                    Create Car
                </button>
                <button type="button" class="kt-btn kt-btn-secondary ml-2" id="submit-normal-btn" style="display: none;">
                    Submit (Fallback)
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
                            <div class="step-label">Options</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Pricing & Status</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="5">
                            <div class="step-number">5</div>
                            <div class="step-label">Inspection</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-indicator" data-step="6">
                            <div class="step-number">6</div>
                            <div class="step-label">Images</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        Step <span id="current-step">1</span> of 6
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
                                class="block text-sm font-medium text-gray-700 mb-2">Insurance Expiry Date</label>
                            <input type="date" name="insurance_expiry_date" id="insurance_expiry_date"
                                value="{{ old('insurance_expiry_date') }}" class="kt-input w-full">
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
                            <label for="engine_capacity" class="block text-sm font-medium text-gray-700 mb-2">Engine
                                Capacity</label>
                            <input type="text" name="engine_capacity" id="engine_capacity"
                                value="{{ old('engine_capacity') }}" class="kt-input w-full">
                            @error('engine_capacity')
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
                                Keys</label>
                            <input type="number" name="number_of_keys" id="number_of_keys"
                                value="{{ old('number_of_keys') }}" min="1" max="10"
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Options -->
        <div class="step-content" data-step="3">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Predefined Features</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="options" class="block text-sm font-medium text-gray-700 mb-2">Select from Common Features</label>
                            <div id="predefined-options-buttons" class="flex flex-wrap gap-2 mb-2">
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
                                        'Adaptive Cruise Control',
                                    ];
                                @endphp
                                @foreach ($carOptions as $option)
                                    <button type="button" class="predefined-option-btn px-3 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-blue-50 focus:outline-none" data-option="{{ $option }}">
                                        {{ $option }}
                                    </button>
                                @endforeach
                            </div>
                            @error('options')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            @error('options.*')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Custom Features</h3>
                    </div>
                    <div class="kt-card-content p-5 space-y-3">
                        <div>
                            <label for="custom-option-input" class="block text-sm font-medium text-gray-700 mb-2">Add Custom Feature</label>
                            <div class="flex space-x-2">
                                <input type="text" id="custom-option-input" 
                                       class="kt-input flex-1" 
                                       placeholder="Enter custom feature (e.g., Custom Paint Job)">
                                <button type="button" id="add-custom-option" 
                                        class="kt-btn kt-btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selected Features</label>
                            <div id="selected-options-container" class="min-h-[100px] border border-gray-200 rounded-lg p-3 bg-gray-50">
                                <div class="text-sm text-gray-500 text-center py-4" id="no-options-message">
                                    No features selected yet. Choose from predefined options or add custom ones.
                                </div>
                                <div id="selected-options-list" class="space-y-2" style="display: none;">
                                    <!-- Selected options will be displayed here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden input to store all selected options -->
                        <input type="hidden" name="all_options" id="all-options-input" value="{{ old('all_options', '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Pricing & Status -->
        <div class="step-content" data-step="4">
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

        <!-- Step 5: Inspection -->
        <div class="step-content" data-step="5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="text-lg font-semibold">Chassis Inspection</h3>
                    </div>
                    <div class="kt-card-content p-6 space-y-4">
                        <div>
                            <label for="chassis_inspection" class="block text-sm font-medium text-gray-700 mb-2">Chassis Inspection</label>
                            <textarea name="chassis_inspection" id="chassis_inspection" class="kt-textarea w-full h-32"
                                placeholder="Enter comprehensive chassis inspection notes including front, rear, left, and right sides">{{ old('chassis_inspection') }}</textarea>
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

        <!-- Step 6: Images -->
        <div class="step-content" data-step="6">
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
    </form>

@push('scripts')
<script src="{{ asset('js/pages/cars-form.js') }}"></script>
@endpush
@endsection

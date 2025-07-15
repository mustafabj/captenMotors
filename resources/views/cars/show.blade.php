@extends('layouts.app')

@section('content')
    <!-- Inline Edit Styling -->
    <style>
        .editable-field.edit-mode {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .editable-field.edit-mode:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, 0.25);
        }
        
        .status-display {
            display: inline-block;
        }
        
        .edit-mode-indicator {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            z-index: 1000;
            font-size: 14px;
        }

        /* Tab-specific edit mode styling */
        .option-input, .inspection-field, .financial-field, .images-field {
            transition: all 0.2s ease;
        }

        .option-input:focus, .inspection-field:focus, .financial-field:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .option-item {
            transition: all 0.2s ease;
        }

        .remove-option-btn {
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .remove-option-btn:hover {
            opacity: 1;
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Success/Error animations */
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }

        .notification {
            animation: fadeInOut 5s ease-in-out;
        }
    </style>

    <!-- LightGallery CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-zoom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-thumbnail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-autoplay.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-fullscreen.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/zoom/lg-zoom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/autoplay/lg-autoplay.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/fullscreen/lg-fullscreen.min.js">
    </script>

    <div class="grid w-full space-y-5">

        <!--begin::Car Details Card-->
        <div class="kt-card">
            <div class="kt-card-content flex flex-col sm:flex-row items-center flex-wrap justify-between p-2 pe-5 gap-4.5">
                <!-- Image -->
                @if ($car->getFirstMedia('car_images'))
                    <div class="kt-card flex items-center justify-center bg-accent/50 h-[90px] w-[120px] shadow-none">
                        <img src="{{ $car->getFirstMedia('car_images')->getUrl() }}"
                            class="h-[90px] w-[120px] object-cover rounded" alt="Car Image">
                    </div>
                @else
                    <div class="kt-card flex items-center justify-center bg-accent/50 h-full w-[120px] shadow-none">
                        <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                    </div>
                @endif

                <!-- Details -->
                <div class="flex flex-col gap-2 flex-1 min-w-0">
                    <div class="flex items-center gap-2.5 -mt-1">
                        <h1 class="hover:text-primary text-sm font-medium text-mono leading-5.5 truncate">
                            {{ $car->model }}
                        </h1>
                    </div>
                    <div class="flex items-center flex-wrap gap-3">
                        @php
                            $statusConfig = [
                                'not_received' => ['class' => 'kt-badge-warning', 'text' => 'Not Received'],
                                'paint' => ['class' => 'kt-badge-info', 'text' => 'Paint'],
                                'upholstery' => ['class' => 'kt-badge-primary', 'text' => 'Upholstery'],
                                'mechanic' => ['class' => 'kt-badge-warning', 'text' => 'Mechanic'],
                                'electrical' => ['class' => 'kt-badge-warning', 'text' => 'Electrical'],
                                'agency' => ['class' => 'kt-badge-info', 'text' => 'Agency'],
                                'polish' => ['class' => 'kt-badge-primary', 'text' => 'Polish'],
                                'ready' => ['class' => 'kt-badge-success', 'text' => 'Ready'],
                            ];
                            $status = $statusConfig[$car->status] ?? [
                                'class' => 'kt-badge-secondary',
                                'text' => 'Unknown',
                            ];
                        @endphp
                        <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                        <span class="text-sm text-gray-500">{{ $car->manufacturing_year }} •
                            {{ $car->engine_capacity }}</span>
                        @if ($car->vehicle_category)
                            <span class="text-sm text-gray-500">• {{ $car->vehicle_category }}</span>
                        @endif
                        @if ($car->engine_type)
                            <span class="text-sm text-gray-500">• {{ $car->engine_type }}</span>
                        @endif
                    </div>
                </div>

                <!-- Price -->
                <div class="flex flex-col items-end gap-1">
                    <div class="text-lg font-bold text-gray-900">
                        ${{ number_format($car->expected_sale_price, 2) }}
                    </div>
                    @if ($car->purchase_price)
                        <div class="text-sm text-gray-500">
                            Purchase: ${{ number_format($car->purchase_price, 2) }}
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <i class="ki-filled ki-key text-gray-400"></i>
                        <span class="text-sm">{{ $car->number_of_keys }} keys</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2" id="view-actions">
                    <button id="edit-btn" class="kt-btn kt-btn-sm kt-btn-primary">
                        <i class="ki-filled ki-pencil"></i>
                        Edit
                    </button>
                    <a href="{{ route('cars.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back
                    </a>
                </div>

                <!-- Edit Mode Actions (hidden by default) -->
                <div class="flex items-center gap-2 hidden" id="edit-actions">
                    <button id="save-btn" class="kt-btn kt-btn-sm kt-btn-success">
                        <i class="ki-filled ki-check"></i>
                        Save
                    </button>
                    <button id="cancel-btn" class="kt-btn kt-btn-sm kt-btn-secondary">
                        <i class="ki-filled ki-cross"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="kt-tabs kt-tabs-line" data-kt-tabs="true">
                <button class="kt-tab-toggle active" data-kt-tab-toggle="#tab_1_1">
                    <i class="ki-filled ki-document me-2"></i>
                    Details
                </button>

                <button class="kt-tab-toggle" data-kt-tab-toggle="#tab_1_2">
                    <i class="ki-filled ki-setting me-2"></i>
                    Options
                </button>

                <button class="kt-tab-toggle" data-kt-tab-toggle="#tab_1_3">
                    <i class="ki-filled ki-search-list me-2"></i>
                    Inspection
                </button>

                <button class="kt-tab-toggle" data-kt-tab-toggle="#tab_1_4">
                    <i class="ki-filled ki-dollar me-2"></i>
                    Financial
                </button>

                <button class="kt-tab-toggle" data-kt-tab-toggle="#tab_1_5">
                    <i class="ki-filled ki-check-circle me-2"></i>
                    Status History
                </button>

                <button class="kt-tab-toggle" data-kt-tab-toggle="#tab_1_7">
                    <i class="ki-filled ki-picture me-2"></i>
                    Images
                </button>
            </div>

            <div class="text-sm">
                <!-- Details Tab -->
                <div class="" id="tab_1_1">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Car Details -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Basic Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Model</span>
                                    <input type="text" class="kt-input w-auto editable-field" 
                                           name="model" 
                                           value="{{ $car->model }}" 
                                           data-original="{{ $car->model }}"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Vehicle Category</span>
                                    <input type="text" class="kt-input w-auto editable-field"
                                           name="vehicle_category"
                                           value="{{ $car->vehicle_category ?? '' }}"
                                           data-original="{{ $car->vehicle_category ?? '' }}"
                                           placeholder="Not specified"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Manufacturing Year</span>
                                    <input type="number" class="kt-input w-auto editable-field" 
                                           name="manufacturing_year"
                                           value="{{ $car->manufacturing_year }}"
                                           data-original="{{ $car->manufacturing_year }}"
                                           min="1900" 
                                           max="{{ date('Y') + 1 }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Capacity</span>
                                    <input type="text" class="kt-input w-auto editable-field" 
                                           name="engine_capacity"
                                           value="{{ $car->engine_capacity }}"
                                           data-original="{{ $car->engine_capacity }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Place of Manufacture</span>
                                    <input type="text" class="kt-input w-auto editable-field"
                                           name="place_of_manufacture"
                                           value="{{ $car->place_of_manufacture ?? '' }}"
                                           data-original="{{ $car->place_of_manufacture ?? '' }}"
                                           placeholder="Not specified"
                                           readonly>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Type</span>
                                    <input type="text" class="kt-input w-auto editable-field"
                                           name="engine_type"
                                           value="{{ $car->engine_type ?? '' }}"
                                           data-original="{{ $car->engine_type ?? '' }}"
                                           placeholder="Not specified"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Number of Keys</span>
                                    <input type="number" class="kt-input w-auto editable-field" 
                                           name="number_of_keys"
                                           value="{{ $car->number_of_keys }}"
                                           data-original="{{ $car->number_of_keys }}"
                                           min="1" 
                                           max="10"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <!-- Identification -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Identification</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Plate Number</span>
                                    <input type="text" class="kt-input w-auto editable-field"
                                           name="plate_number"
                                           value="{{ $car->plate_number ?? '' }}"
                                           data-original="{{ $car->plate_number ?? '' }}"
                                           placeholder="Not assigned"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Status</span>
                                    <div class="status-display">
                                    <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                    </div>
                                    <select class="kt-select w-auto editable-field hidden" 
                                            name="status"
                                            data-original="{{ $car->status }}" k>
                                        <option value="not_received" {{ $car->status === 'not_received' ? 'selected' : '' }}>Not Received</option>
                                        <option value="paint" {{ $car->status === 'paint' ? 'selected' : '' }}>Paint</option>
                                        <option value="upholstery" {{ $car->status === 'upholstery' ? 'selected' : '' }}>Upholstery</option>
                                        <option value="mechanic" {{ $car->status === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                                        <option value="electrical" {{ $car->status === 'electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="agency" {{ $car->status === 'agency' ? 'selected' : '' }}>Agency</option>
                                        <option value="polish" {{ $car->status === 'polish' ? 'selected' : '' }}>Polish</option>
                                        <option value="ready" {{ $car->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Insurance -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Insurance Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Insurance Expiry</span>
                                    <input type="date" class="kt-input w-auto editable-field"
                                           name="insurance_expiry_date"
                                           value="{{ $car->insurance_expiry_date->format('Y-m-d') }}"
                                           data-original="{{ $car->insurance_expiry_date->format('Y-m-d') }}"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Insurance Status</span>
                                    <div>
                                        @php
                                            $daysUntilExpiry = (int) $car->insurance_expiry_date->diffInDays(
                                                now(),
                                                false,
                                            );
                                        @endphp
                                        @if ($daysUntilExpiry > 0)
                                            <span class="kt-badge kt-badge-destructive kt-badge-outline">
                                                Expired {{ $daysUntilExpiry }} days ago
                                            </span>
                                        @elseif($daysUntilExpiry >= -30)
                                            <span class="kt-badge kt-badge-warning kt-badge-outline">
                                                Expires in {{ abs($daysUntilExpiry) }} days
                                            </span>
                                        @else
                                            <span class="kt-badge kt-badge-success kt-badge-outline">
                                                Valid for {{ abs($daysUntilExpiry) }} days
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Purchase -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Purchase Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Purchase Date</span>
                                    <input type="date" class="kt-input w-auto editable-field"
                                           name="purchase_date"
                                           value="{{ $car->purchase_date->format('Y-m-d') }}"
                                           data-original="{{ $car->purchase_date->format('Y-m-d') }}"
                                           readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Purchase Price</span>
                                    <input type="number" class="kt-input w-auto editable-field"
                                           name="purchase_price"
                                           value="{{ $car->purchase_price }}"
                                           data-original="{{ $car->purchase_price }}"
                                           min="0"
                                           step="0.01"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Expected Sale Price</span>
                                    <input type="number" class="kt-input w-auto editable-field"
                                           name="expected_sale_price"
                                           value="{{ $car->expected_sale_price }}"
                                           data-original="{{ $car->expected_sale_price }}"
                                           min="0"
                                           step="0.01"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options Tab -->
                <div class="hidden" id="tab_1_2">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                            <h4 class="text-lg font-bold mb-6">Car Options</h4>
                            </div>
                        </div>
                        <div class="kt-card-content">
                            <!-- View Mode -->
                            <div id="options-view-mode">
                            @if ($car->options->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($car->options as $option)
                                        <div class="kt-card bg-green-50 border border-green-200">
                                            <div class="kt-card-body p-4">
                                                <div class="flex items-center">
                                                    <i class="ki-filled ki-check-circle text-green-500 text-xl me-3"></i>
                                                    <span class="text-gray-800 font-semibold">{{ $option->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">No options</h4>
                                    <p class="text-gray-600">No options have been added to this car yet.</p>
                                </div>
                            @endif
                        </div>

                            <!-- Edit Mode -->
                            <div id="options-edit-mode" class="hidden">
                                <div class="space-y-4">
                                    <div class="options-container">
                                        @if ($car->options->count() > 0)
                                            @foreach ($car->options as $index => $option)
                                                <div class="option-item flex items-center gap-2 mb-3">
                                                    <input type="text" 
                                                           class="kt-input flex-1 option-input" 
                                                           value="{{ $option->name }}"
                                                           placeholder="Enter option name">
                                                                                                    <button type="button" 
                                                        class="kt-btn kt-btn-sm kt-btn-danger remove-option-btn"
                                                        data-action="remove-option">
                                                    <i class="ki-filled ki-trash"></i>
                                                </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="option-item flex items-center gap-2 mb-3">
                                                <input type="text" 
                                                       class="kt-input flex-1 option-input" 
                                                       placeholder="Enter option name">
                                                <button type="button" 
                                                        class="kt-btn kt-btn-sm kt-btn-danger remove-option-btn"
                                                        data-action="remove-option">
                                                    <i class="ki-filled ki-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-outline"
                                            data-action="add-option-field">
                                        <i class="ki-filled ki-plus"></i>
                                        Add Option
                                    </button>

                                    <div class="flex gap-2 pt-4">
                                        <button type="button" 
                                                class="kt-btn kt-btn-sm kt-btn-success"
                                                data-action="save-options">
                                            <i class="ki-filled ki-check"></i>
                                            Save Options
                                        </button>
                                        <button type="button" 
                                                class="kt-btn kt-btn-sm kt-btn-secondary"
                                                data-action="cancel-options-edit">
                                            <i class="ki-filled ki-cross"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inspection Tab -->
                <div class="hidden" id="tab_1_3">
                        <div class="kt-card">
                            <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                                <h4 class="text-lg font-bold mb-6">Inspection Details</h4>
                            </div>
                            </div>
                            <div class="kt-card-content">
                            <!-- View Mode -->
                            <div id="inspection-view-mode">
                                @if ($car->inspection)
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Chassis Inspection</h5>
                                        <div class="space-y-3">
                                                @if ($car->inspection->chassis_inspection)
                                                    <div class="py-2">
                                                        <span class="text-gray-600 block mb-2">Chassis Inspection</span>
                                                        <div class="bg-gray-50 p-3 rounded border">
                                                            {{ $car->inspection->chassis_inspection }}
                                            </div>
                                            </div>
                                                @else
                                                    <div class="py-2 text-gray-500 italic">
                                                        No chassis inspection data available
                                            </div>
                                                @endif
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Mechanical Inspection</h5>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Transmission Condition</span>
                                                    <span class="text-gray-800">{{ $car->inspection->transmission ?? 'Not specified' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Motor Condition</span>
                                                    <span class="text-gray-800">{{ $car->inspection->motor ?? 'Not specified' }}</span>
                                            </div>
                                        </div>
                                        @if ($car->inspection->body_notes)
                                            <div class="mt-6">
                                                <h6 class="text-sm font-semibold mb-2">Body Notes</h6>
                                                <p class="text-gray-700 bg-gray-50 p-3 rounded">
                                                    {{ $car->inspection->body_notes }}</p>
                                            </div>
                                        @endif
                            </div>
                        </div>
                    @else
                                    <div class="text-center py-12">
                                <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">No inspection data</h4>
                                <p class="text-gray-600">No inspection information has been recorded for this car.</p>
                        </div>
                    @endif
                            </div>

                            <!-- Edit Mode -->
                            <div id="inspection-edit-mode" class="hidden">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Chassis Inspection</h5>
                                        <div class="space-y-3">
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Chassis Inspection Notes</label>
                                                <textarea class="kt-textarea w-full inspection-field" 
                                                          name="chassis_inspection" 
                                                          rows="4" 
                                                          placeholder="Enter chassis inspection details">{{ $car->inspection->chassis_inspection ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Mechanical Inspection</h5>
                                        <div class="space-y-3">
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Transmission Condition</label>
                                                <input type="text" 
                                                       class="kt-input w-full inspection-field" 
                                                       name="transmission" 
                                                       value="{{ $car->inspection->transmission ?? '' }}"
                                                       placeholder="Enter transmission condition">
                                            </div>
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Motor Condition</label>
                                                <input type="text" 
                                                       class="kt-input w-full inspection-field" 
                                                       name="motor" 
                                                       value="{{ $car->inspection->motor ?? '' }}"
                                                       placeholder="Enter motor condition">
                                            </div>
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Body Notes</label>
                                                <textarea class="kt-textarea w-full inspection-field" 
                                                          name="body_notes" 
                                                          rows="3" 
                                                          placeholder="Enter body notes">{{ $car->inspection->body_notes ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-6">
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-success"
                                            data-action="save-inspection">
                                        <i class="ki-filled ki-check"></i>
                                        Save Inspection
                                    </button>
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-secondary"
                                            data-action="cancel-inspection-edit">
                                        <i class="ki-filled ki-cross"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Tab -->
                <div class="hidden" id="tab_1_4">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                            <h4 class="text-lg font-bold mb-6">Financial Summary</h4>
                            </div>
                        </div>
                        <div class="kt-card-content">
                            <!-- View Mode -->
                            <div id="financial-view-mode">
                            <div class="grid {{ $car->purchase_price ? 'grid-cols-4' : 'grid-cols-2' }} gap-6">
                                @if ($car->purchase_price)
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600">
                                            ${{ number_format($car->purchase_price, 2) }}</div>
                                        <div class="text-sm text-gray-600">Purchase Price</div>
                                    </div>
                                @endif
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">
                                        ${{ number_format($car->expected_sale_price, 2) }}</div>
                                    <div class="text-sm text-gray-600">Expected Sale Price</div>
                                </div>
                                @php
                                    $totalCosts = $car->equipmentCosts->sum('amount');
                                    $profit = $car->expected_sale_price - ($car->purchase_price ?? 0) - $totalCosts;
                                @endphp
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">${{ number_format($totalCosts, 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total Equipment Costs</div>
                                </div>
                                @if ($car->purchase_price)
                                    <div
                                        class="text-center p-4 {{ $profit >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                                        <div
                                            class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($profit, 2) }}</div>
                                        <div class="text-sm text-gray-600">Estimated Profit</div>
                                    </div>
                                @endif
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div id="financial-edit-mode" class="hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="kt-form-item">
                                        <label class="kt-form-label">Purchase Price</label>
                                        <input type="number" 
                                               class="kt-input w-full financial-field" 
                                               name="purchase_price" 
                                               value="{{ $car->purchase_price }}"
                                               min="0"
                                               step="0.01"
                                               placeholder="0.00">
                                    </div>
                                    <div class="kt-form-item">
                                        <label class="kt-form-label">Expected Sale Price</label>
                                        <input type="number" 
                                               class="kt-input w-full financial-field" 
                                               name="expected_sale_price" 
                                               value="{{ $car->expected_sale_price }}"
                                               min="0"
                                               step="0.01"
                                               placeholder="0.00">
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-6">
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-success"
                                            data-action="save-financial">
                                        <i class="ki-filled ki-check"></i>
                                        Save Financial
                                    </button>
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-secondary"
                                            data-action="cancel-financial-edit">
                                        <i class="ki-filled ki-cross"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <div class="equipment mt-6">
                                <div class="kt-card-header">
                                    <div class="flex justify-between items-center w-full">
                                        <h4 class="text-lg font-bold">Equipment Costs</h4>
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                            data-kt-modal-toggle="#addCostModal">
                                            <i class="ki-filled ki-plus"></i>
                                            Add Cost
                                        </button>
                                    </div>
                                </div>
                                <div class="kt-card-content">
                                    @if ($car->equipmentCosts->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="w-full" id="equipment-costs-table">
                                                <thead>
                                                    <tr class="border-b border-gray-200">
                                                        <th class="text-left py-3 px-4 font-semibold">Description</th>
                                                        <th class="text-left py-3 px-4 font-semibold">Amount</th>
                                                        <th class="text-left py-3 px-4 font-semibold">Date</th>
                                                        <th class="text-left py-3 px-4 font-semibold">Added By</th>
                                                        <th class="text-left py-3 px-4 font-semibold">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($car->equipmentCosts->sortByDesc('cost_date') as $cost)
                                                        <tr class="border-b border-gray-200">
                                                            <td class="py-3 px-4">{{ $cost->description }}</td>
                                                            <td class="py-3 px-4 font-semibold">
                                                                ${{ number_format($cost->amount, 2) }}</td>
                                                            <td class="py-3 px-4">{{ $cost->cost_date->format('M j, Y') }}
                                                            </td>
                                                            <td class="py-3 px-4 text-gray-600">{{ $cost->user->name ?? '—' }}
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <span class="kt-badge {{ $cost->getStatusBadgeClass() }}">
                                                                    {{ $cost->getStatusText() }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-12">
                                            <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                            <h4 class="text-lg font-bold text-gray-900 mb-2">No equipment costs</h4>
                                            <p class="text-gray-600">No equipment costs have been recorded for this car.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Status History Tab -->
                <div class="hidden" id="tab_1_5">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h4 class="text-lg font-bold mb-6">Status History</h4>
                        </div>
                        <div class="kt-card-content">
                            @if ($car->statusHistories->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($car->statusHistories->sortByDesc('created_at') as $history)
                                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @php
                                                    $statusClass =
                                                        $statusConfig[$history->status]['class'] ??
                                                        'kt-badge-secondary';
                                                    $statusText = $statusConfig[$history->status]['text'] ?? 'Unknown';
                                                @endphp
                                                <span class="kt-badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-sm text-gray-500">
                                                    {{ $history->created_at->format('F j, Y \a\t g:i A') }}</div>
                                                @if ($history->notes)
                                                    <div class="text-gray-700 mt-1">{{ $history->notes }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">No status history</h4>
                                    <p class="text-gray-600">No status changes have been recorded for this car.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Images Tab -->
                <div class="hidden" id="tab_1_7">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                                <h4 class="text-lg font-bold mb-6">Car Images</h4>
                            </div>
                        </div>
                        <div class="kt-card-content">
                            <!-- View Mode -->
                            <div id="images-view-mode">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Car License Image -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Car License
                                    ({{ $car->getMedia('car_license')->count() }})</h4>
                            </div>
                            <div class="kt-card-content">
                                @if ($car->getMedia('car_license')->count() > 0)
                                    <div id="car-license-gallery"
                                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach ($car->getMedia('car_license') as $license)
                                            <div class="relative group cursor-pointer"
                                                data-src="{{ $license->getUrl() }}">
                                                <img src="{{ $license->getUrl() }}" alt="Car License"
                                                    class="w-full h-32 object-cover rounded-lg shadow-md transition-transform duration-200 group-hover:scale-105">
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                    <i
                                                        class="ki-filled ki-eye text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-xl"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <i class="ki-filled ki-picture text-4xl text-gray-400 mb-4"></i>
                                        <h4 class="text-lg font-bold text-gray-900 mb-2">No License Image</h4>
                                        <p class="text-gray-600">No car license image has been uploaded.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Car Images -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Car Images
                                    ({{ $car->getMedia('car_images')->count() }})</h4>
                            </div>
                            <div class="kt-card-content">
                                @if ($car->getMedia('car_images')->count() > 0)
                                    <div id="car-images-gallery"
                                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach ($car->getMedia('car_images') as $image)
                                            <div class="relative group cursor-pointer" data-src="{{ $image->getUrl() }}">
                                                <img src="{{ $image->getUrl() }}" alt="Car Image"
                                                    class="w-full h-32 object-cover rounded-lg shadow-md transition-transform duration-200 group-hover:scale-105">
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                    <i
                                                        class="ki-filled ki-eye text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-xl"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <i class="ki-filled ki-picture text-4xl text-gray-400 mb-4"></i>
                                        <h4 class="text-lg font-bold text-gray-900 mb-2">No Car Images</h4>
                                        <p class="text-gray-600">No car images have been uploaded.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                            <!-- Edit Mode -->
                            <div id="images-edit-mode" class="hidden">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- Car License Upload -->
                                    <div class="kt-card">
                                        <div class="kt-card-header">
                                            <h4 class="text-lg font-bold mb-6">Car License</h4>
                                        </div>
                                        <div class="kt-card-content">
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Upload License Image</label>
                                                <input type="file" 
                                                       class="kt-input w-full images-field" 
                                                       name="car_license" 
                                                       accept="image/*">
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Accepted formats: JPEG, PNG, JPG (max 2MB)
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Car Images Upload -->
                                    <div class="kt-card">
                                        <div class="kt-card-header">
                                            <h4 class="text-lg font-bold mb-6">Car Images</h4>
                                        </div>
                                        <div class="kt-card-content">
                                            <div class="kt-form-item">
                                                <label class="kt-form-label">Upload Car Images</label>
                                                <input type="file" 
                                                       class="kt-input w-full images-field" 
                                                       name="car_images[]" 
                                                       accept="image/*"
                                                       multiple>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Accepted formats: JPEG, PNG, JPG (max 2MB each)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-6">
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-success"
                                            data-action="save-images">
                                        <i class="ki-filled ki-check"></i>
                                        Save Images
                                    </button>
                                    <button type="button" 
                                            class="kt-btn kt-btn-sm kt-btn-secondary"
                                            data-action="cancel-images-edit">
                                        <i class="ki-filled ki-cross"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Equipment Cost Modal -->
    <div class="kt-modal" data-kt-modal="true" id="addCostModal">
        <div class="kt-modal-content max-w-[500px] top-[5%]">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">Add Equipment Cost</h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal"
                    data-kt-modal-dismiss="#addCostModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="kt-modal-body">
                <form method="POST" id="addCostForm">
                    @csrf
                    <div class="space-y-5">
                        <!-- Date Field -->
                        <div class="kt-form-item">
                            <label for="cost_date" class="kt-form-label">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <div class="kt-form-control">
                                <input type="date" name="cost_date" id="cost_date" required class="kt-input w-full">
                                <div class="kt-form-message"></div>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="kt-form-item">
                            <label for="description" class="kt-form-label">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <div class="kt-form-control">
                                <textarea name="description" id="description" rows="4" required class="kt-textarea w-full"
                                    placeholder="e.g., Oil change, Tire replacement"></textarea>
                                <div class="kt-form-message"></div>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div class="kt-form-item">
                            <label for="cost_amount" class="kt-form-label">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="kt-form-control">
                                <input type="number" name="amount" id="cost_amount" required min="0"
                                    step="0.01" class="kt-input w-full pl-8" placeholder="0.00">
                                <div class="kt-form-message"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="kt-modal-footer">
                <div></div>
                <div class="flex gap-4">
                    <button class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#addCostModal">
                        Cancel
                    </button>
                    <button type="submit" class="kt-btn kt-btn-primary" form="addCostForm" id="submitCostBtn">
                        <span class="submit-text">Add Cost</span>
                        <span class="loading-text hidden">
                            <i class="ki-duotone ki-spinner fs-2 rotate"></i>
                            Adding...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add data attributes for JavaScript initialization -->
    <div id="car-data" 
         data-car-id="{{ $car->id }}" 
         data-update-url="{{ route('cars.update-inline', $car) }}"
         data-equipment-cost-url="{{ route('cars.add-equipment-cost', $car->id) }}"
         style="display: none;"></div>
@endsection

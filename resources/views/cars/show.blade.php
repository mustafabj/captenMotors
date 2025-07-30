@extends('layouts.app')

@section('content')
    <!-- Responsive Design Styles -->
    <style>
        /* Edit Mode Indicator Styles */
        .edit-mode-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            text-align: center;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Mobile responsive styles for edit mode indicator */
        @media (max-width: 640px) {
            .edit-mode-indicator {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
                font-weight: 500;
            }
        }

        @media (max-width: 480px) {
            .edit-mode-indicator {
                padding: 0.375rem 0.5rem;
                font-size: 0.7rem;
            }
        }

        /* Custom responsive breakpoints for better mobile experience */
        @media (max-width: 640px) {
            .kt-card-content {
                padding: 1rem;
            }

            .kt-modal-content {
                margin: 0.5rem;
                max-height: calc(100vh - 1rem);
            }

            .kt-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .kt-tab-toggle {
                white-space: nowrap;
                flex-shrink: 0;
            }
        }

        /* Ensure proper spacing on very small screens */
        @media (max-width: 480px) {
            .kt-card {
                margin: 0.5rem 0;
            }

            .kt-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Improve table responsiveness */
        @media (max-width: 768px) {
            .equipment-costs-table {
                font-size: 0.875rem;
            }

            .equipment-costs-table th,
            .equipment-costs-table td {
                padding: 0.5rem 0.25rem;
            }
        }
    </style>

    <!-- Inline Edit Styling -->

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

    <div class="grid w-full space-y-4 sm:space-y-5 px-2 sm:px-0">

        <!--begin::Car Details Card-->
        <div class="kt-card">
            <div
                class="kt-card-content flex flex-col lg:flex-row items-start lg:items-center justify-between p-3 sm:p-4 lg:p-6 gap-4 lg:gap-6">
                <!-- Image -->
                <div class="flex-shrink-0 w-full sm:w-auto">
                    @if ($car->getFirstMedia('car_images'))
                        <div
                            class="kt-card flex items-center justify-center bg-accent/50 h-[160px] w-full sm:h-[90px] sm:w-[120px] shadow-none">
                            <img src="{{ $car->getFirstMedia('car_images')->getUrl() }}"
                                class="h-[160px] w-full sm:h-[90px] sm:w-[120px] object-cover rounded" alt="Car Image">
                        </div>
                    @else
                        <div
                            class="kt-card flex items-center justify-center bg-accent/50 h-[160px] w-full sm:h-[90px] sm:w-[120px] shadow-none">
                            <i class="ki-filled ki-car text-2xl sm:text-3xl text-gray-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div class="flex flex-col gap-2 flex-1 min-w-0 w-full lg:w-auto">
                    <div class="flex items-center gap-2.5">
                        <h1 class="hover:text-primary text-base sm:text-lg font-medium text-mono leading-tight truncate">
                            {{ $car->model }}
                        </h1>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
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
                                'sold' => ['class' => 'kt-badge-danger', 'text' => 'Sold'],
                            ];
                            $status = $statusConfig[$car->status] ?? [
                                'class' => 'kt-badge-secondary',
                                'text' => 'Unknown',
                            ];
                        @endphp
                        <span class="kt-badge {{ $status['class'] }} w-fit">{{ $status['text'] }}</span>
                        <div class="flex flex-wrap gap-1 sm:gap-2 text-sm text-gray-500">
                            <span>{{ $car->manufacturing_year }}</span>
                            <span class="hidden sm:inline">•</span>
                            <span>{{ $car->engine_capacity }}</span>
                            @if ($car->vehicle_category)
                                <span class="hidden sm:inline">•</span>
                                <span>{{ $car->vehicle_category }}</span>
                            @endif
                            @if ($car->engine_type)
                                <span class="hidden sm:inline">•</span>
                                <span>{{ $car->engine_type }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if (auth()->user()->hasRole('admin'))
                    <!-- Price -->
                    <div class="flex flex-col items-start sm:items-end gap-1 w-full sm:w-auto">
                        <div class="text-lg sm:text-xl font-semibold text-gray-900">
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
                @else
                    <!-- Car Status -->
                    <div class="flex flex-col items-start sm:items-end gap-1 w-full sm:w-auto">
                        <span class="kt-badge kt-badge-lg {{ $status['class'] }}">{{ $status['text'] }}</span>
                        <div class="flex items-center gap-2">
                            <i class="ki-filled ki-key text-gray-400"></i>
                            <span class="text-sm">{{ $car->number_of_keys }} keys</span>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto"
                    id="view-actions">
                    @if (auth()->user()->isAdmin())
                        <button id="edit-btn" class="kt-btn kt-btn-sm kt-btn-primary w-full sm:w-auto">
                            <i class="ki-filled ki-pencil"></i>
                            <span class="hidden sm:inline">Edit</span>
                        </button>
                        @if (!$car->isSold())
                            <button class="kt-btn kt-btn-sm kt-btn-success w-full sm:w-auto"
                                data-kt-modal-toggle="#sellCarModal"
                                onclick="openSellModal({{ $car->id }}, '{{ $car->model }}')">
                                <i class="ki-filled ki-dollar"></i>
                                <span class="hidden sm:inline">Mark as Sold</span>
                            </button>
                        @endif
                    @endif
                    <a href="{{ route('cars.index') }}" class="kt-btn kt-btn-sm kt-btn-outline w-full sm:w-auto">
                        <i class="ki-filled ki-arrow-left"></i>
                        <span class="hidden sm:inline">Back</span>
                    </a>
                </div>

                <!-- Edit Mode Actions (hidden by default) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 hidden w-full sm:w-auto"
                    id="edit-actions">
                    <button id="save-btn" class="kt-btn kt-btn-sm kt-btn-success w-full sm:w-auto">
                        <i class="ki-filled ki-check"></i>
                        <span class="hidden sm:inline">Save</span>
                    </button>
                    <button id="cancel-btn" class="kt-btn kt-btn-sm kt-btn-secondary w-full sm:w-auto">
                        <i class="ki-filled ki-cross"></i>
                        <span class="hidden sm:inline">Cancel</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <!-- Responsive Tabs -->
            <div class="kt-tabs kt-tabs-line overflow-x-auto" data-kt-tabs="true">
                <div class="flex flex-nowrap justify-between sm:justify-start w-full gap-3 sm:gap-4">
                    <button class="kt-tab-toggle active whitespace-nowrap text-sm sm:text-base"
                        data-kt-tab-toggle="#tab_1_1">
                        <i class="ki-filled ki-document me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Details</span>
                    </button>

                    <button class="kt-tab-toggle whitespace-nowrap text-sm sm:text-base" data-kt-tab-toggle="#tab_1_2">
                        <i class="ki-filled ki-setting me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Options</span>
                    </button>

                    <button class="kt-tab-toggle whitespace-nowrap text-sm sm:text-base" data-kt-tab-toggle="#tab_1_3">
                        <i class="ki-filled ki-search-list me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Inspection</span>
                    </button>

                    <button class="kt-tab-toggle whitespace-nowrap text-sm sm:text-base" data-kt-tab-toggle="#tab_1_4">
                        <i class="ki-filled ki-dollar me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Financial</span>
                    </button>

                    <button class="kt-tab-toggle whitespace-nowrap text-sm sm:text-base" data-kt-tab-toggle="#tab_1_5">
                        <i class="ki-filled ki-check-circle me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Status History</span>
                    </button>

                    <button class="kt-tab-toggle whitespace-nowrap text-sm sm:text-base" data-kt-tab-toggle="#tab_1_7">
                        <i class="ki-filled ki-picture me-0 sm:me-2 text-lg sm:text-base"></i>
                        <span class="hidden sm:inline">Images</span>
                    </button>
                </div>
            </div>

            <div class="text-sm">
                <!-- Details Tab -->
                <div class="" id="tab_1_1">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Car Details -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg ">Basic Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Model</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="model"
                                        value="{{ $car->model }}" data-original="{{ $car->model }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Vehicle Category</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="vehicle_category"
                                        value="{{ $car->vehicle_category ?? '' }}"
                                        data-original="{{ $car->vehicle_category ?? '' }}" placeholder="Not specified"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Color</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="color"
                                        value="{{ $car->color ?? '' }}" data-original="{{ $car->color ?? '' }}"
                                        placeholder="Not specified" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Millage (KM)</span>
                                    <input type="number" class="kt-input w-auto editable-field" name="odometer"
                                        value="{{ $car->odometer ?? '' }}" data-original="{{ $car->odometer ?? '' }}"
                                        placeholder="Not specified" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Manufacturing Year</span>
                                    <input type="number" class="kt-input w-auto editable-field"
                                        name="manufacturing_year" value="{{ $car->manufacturing_year }}"
                                        data-original="{{ $car->manufacturing_year }}" min="1900"
                                        max="{{ date('Y') + 1 }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Capacity</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="engine_capacity"
                                        value="{{ $car->engine_capacity }}" data-original="{{ $car->engine_capacity }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Place of Manufacture</span>
                                    <input type="text" class="kt-input w-auto editable-field"
                                        name="place_of_manufacture" value="{{ $car->place_of_manufacture ?? '' }}"
                                        data-original="{{ $car->place_of_manufacture ?? '' }}"
                                        placeholder="Not specified" readonly>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Type</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="engine_type"
                                        value="{{ $car->engine_type ?? '' }}"
                                        data-original="{{ $car->engine_type ?? '' }}" placeholder="Not specified"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Number of Keys</span>
                                    <input type="number" class="kt-input w-auto editable-field" name="number_of_keys"
                                        value="{{ $car->number_of_keys }}" data-original="{{ $car->number_of_keys }}"
                                        min="1" max="10" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- Identification -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg ">Identification</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Plate Number</span>
                                    <input type="text" class="kt-input w-auto editable-field" name="plate_number"
                                        value="{{ $car->plate_number ?? '' }}"
                                        data-original="{{ $car->plate_number ?? '' }}" placeholder="Not assigned"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Status</span>
                                    <div class="status-display">
                                        <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                    </div>
                                    <select class="kt-select w-auto editable-field hidden" name="status"
                                        data-original="{{ $car->status }}" k>
                                        <option value="not_received"
                                            {{ $car->status === 'not_received' ? 'selected' : '' }}>Not Received</option>
                                        <option value="paint" {{ $car->status === 'paint' ? 'selected' : '' }}>Paint
                                        </option>
                                        <option value="upholstery" {{ $car->status === 'upholstery' ? 'selected' : '' }}>
                                            Upholstery</option>
                                        <option value="mechanic" {{ $car->status === 'mechanic' ? 'selected' : '' }}>
                                            Mechanic</option>
                                        <option value="electrical" {{ $car->status === 'electrical' ? 'selected' : '' }}>
                                            Electrical</option>
                                        <option value="agency" {{ $car->status === 'agency' ? 'selected' : '' }}>Agency
                                        </option>
                                        <option value="polish" {{ $car->status === 'polish' ? 'selected' : '' }}>Polish
                                        </option>
                                        <option value="ready" {{ $car->status === 'ready' ? 'selected' : '' }}>Ready
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Insurance -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg ">Insurance Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Insurance Expiry</span>
                                    <input type="date" class="kt-input w-auto editable-field"
                                        name="insurance_expiry_date"
                                        value="{{ $car->insurance_expiry_date ? $car->insurance_expiry_date->format('Y-m-d') : '' }}"
                                        data-original="{{ $car->insurance_expiry_date ? $car->insurance_expiry_date->format('Y-m-d') : '' }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Insurance Status</span>
                                    <div>
                                        @if (isset($car->insurance_expiry_date))
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
                                        @else
                                            <span class="kt-badge kt-badge-success kt-badge-outline">
                                                No insurance information available
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Purchase -->
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg ">Purchase Information</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Purchase Date</span>
                                    <input type="date" class="kt-input w-auto editable-field" name="purchase_date"
                                        value="{{ $car->purchase_date->format('Y-m-d') }}"
                                        data-original="{{ $car->purchase_date->format('Y-m-d') }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Purchase Price</span>
                                    <input type="number" class="kt-input w-auto editable-field" name="purchase_price"
                                        value="{{ $car->purchase_price }}" data-original="{{ $car->purchase_price }}"
                                        min="0" step="0.01" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Expected Sale Price</span>
                                    <input type="number" class="kt-input w-auto editable-field"
                                        name="expected_sale_price" value="{{ $car->expected_sale_price }}"
                                        data-original="{{ $car->expected_sale_price }}" min="0" step="0.01"
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
                                <h4 class="text-lg ">Car Options</h4>
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
                                                        <i
                                                            class="ki-filled ki-check-circle text-green-500 text-xl me-3"></i>
                                                        <span
                                                            class="text-gray-800 font-semibold">{{ $option->name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                        <h4 class="text-lg  text-gray-900 mb-2">No options</h4>
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
                                                    <input type="text" class="kt-input flex-1 option-input"
                                                        value="{{ $option->name }}" placeholder="Enter option name">
                                                    <button type="button"
                                                        class="kt-btn kt-btn-sm kt-btn-danger remove-option-btn"
                                                        data-action="remove-option">
                                                        <i class="ki-filled ki-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="option-item flex items-center gap-2 mb-3">
                                                <input type="text" class="kt-input flex-1 option-input"
                                                    placeholder="Enter option name">
                                                <button type="button"
                                                    class="kt-btn kt-btn-sm kt-btn-danger remove-option-btn"
                                                    data-action="remove-option">
                                                    <i class="ki-filled ki-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                        data-action="add-option-field">
                                        <i class="ki-filled ki-plus"></i>
                                        Add Option
                                    </button>

                                    <div class="flex gap-2 pt-4">
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-success"
                                            data-action="save-options">
                                            <i class="ki-filled ki-check"></i>
                                            Save Options
                                        </button>
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-secondary"
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
                    <div class="kt-card" id="inspection-view-mode">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                                <h4 class="text-lg ">Inspection Details</h4>
                                <div class="flex gap-2">
                                    @if ($car->inspection)
                                        <a href="{{ route('cars.inspection-report', $car) }}" target="_blank"
                                            class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-printer"></i>
                                            Print Report
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($car->inspection)
                            <div class="kt-card-content">
                                <!-- View Mode -->
                                <div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        <div class="kt-card">
                                            <div class="kt-card-header">
                                                <h5 class="">Chassis Inspection</h5>
                                            </div>
                                            <div class="space-y-3 kt-card-content">
                                                @if ($car->inspection->chassis_inspection)
                                                    <div class="py-2 flex justify-between items-center">
                                                        <span class="text-gray-600 block w-full">Chassis Inspection</span>
                                                        <input class="kt-input " readonly value="{{ $car->inspection->chassis_inspection }}">
                                                    </div>
                                                @else
                                                    <div class="py-2 text-gray-500 italic">
                                                        No chassis inspection data available
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="kt-card">
                                            <div class="kt-card-header">
                                                <h5 class="">Mechanical Inspection</h5>
                                            </div>
                                            <div class="space-y-3 kt-card-content">
                                                <div class="flex justify-between items-center py-2">
                                                    <span class="text-gray-600 block w-full">Transmission Condition</span>
                                                    <input class="kt-input " readonly value="{{ $car->inspection->transmission ?? 'Not specified' }}">
                                                </div>
                                                <div class="flex justify-between items-center py-2">
                                                    <span class="text-gray-600 block w-full">Motor Condition</span>
                                                    <input class="kt-input " readonly value="{{ $car->inspection->motor ?? 'Not specified' }}">
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
                                    </div>

                                    <!-- Body Parts Inspection -->
                                    <div class="mt-8 kt-card">
                                        <div class="kt-card-header">
                                            <h5 class="">Body Parts Inspection (الهيكل)</h5>
                                        </div>
                                        <div class="kt-card-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @php
                                                $bodyPartsArabic = \App\Models\CarInspection::getCarPartsArabic();
                                                $bodyParts = [
                                                    'hood' => 'Hood (' . $bodyPartsArabic['hood'] . ')',
                                                    'front_right_fender' =>
                                                        'Front Right Fender (' .
                                                        $bodyPartsArabic['front_right_fender'] .
                                                        ')',
                                                    'front_left_fender' =>
                                                        'Front Left Fender (' .
                                                        $bodyPartsArabic['front_left_fender'] .
                                                        ')',
                                                    'rear_right_fender' =>
                                                        'Rear Right Fender (' .
                                                        $bodyPartsArabic['rear_right_fender'] .
                                                        ')',
                                                    'rear_left_fender' =>
                                                        'Rear Left Fender (' .
                                                        $bodyPartsArabic['rear_left_fender'] .
                                                        ')',
                                                    'trunk_door' =>
                                                        'Trunk Door (' . $bodyPartsArabic['trunk_door'] . ')',
                                                    'front_right_door' =>
                                                        'Front Right Door (' .
                                                        $bodyPartsArabic['front_right_door'] .
                                                        ')',
                                                    'rear_right_door' =>
                                                        'Rear Right Door (' . $bodyPartsArabic['rear_right_door'] . ')',
                                                    'front_left_door' =>
                                                        'Front Left Door (' . $bodyPartsArabic['front_left_door'] . ')',
                                                    'rear_left_door' =>
                                                        'Rear Left Door (' . $bodyPartsArabic['rear_left_door'] . ')',
                                                ];
                                                $inspectionOptions = [
                                                    'clean_and_free_of_filler' => 'سليم وخالي من المعجون',
                                                    'painted' => 'مصبوغ',
                                                    'fully_repainted' => 'مصبوغ بالكامل',
                                                ];
                                            @endphp

                                            @foreach ($bodyParts as $field => $label)
                                                <div class="bg-gray-50 p-3 rounded">
                                                    <div class="text-sm font-medium text-gray-700 mb-1">
                                                        {{ $label }}</div>
                                                    <div class="text-sm text-gray-800">
                                                        @if ($car->inspection->$field)
                                                            {{ $inspectionOptions[$car->inspection->$field] ?? $car->inspection->$field }}
                                                        @else
                                                            <span class="text-gray-500 italic">Not specified</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                <h4 class="text-lg  text-gray-900 mb-2">No inspection data</h4>
                                <p class="text-gray-600">No inspection information has been recorded for this car.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div id="inspection-edit-mode" class="hidden kt-card">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                                <h4 class="text-lg ">Inspection Details</h4>
                                <div class="flex gap-2">
                                    @if ($car->inspection)
                                        <a href="{{ route('cars.inspection-report', $car) }}" target="_blank"
                                            class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-printer"></i>
                                            Print Report
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php
                            // Parse saved data for form population
                            $chassisStatus = false;
                            $transmissionStatus = false;
                            $motorStatus = false;
                            $motorPercentage = '';
                            $motorDescription = '';
                            $chassisDescription = '';
                            $transmissionDescription = '';

                            if ($car->inspection) {
                                // Parse chassis inspection
                                if ($car->inspection->chassis_inspection) {
                                    if (strpos($car->inspection->chassis_inspection, 'جيــــــــــــــــــــد') === 0) {
                                        $chassisStatus = true;
                                        $chassisDescription = str_replace(
                                            'جيــــــــــــــــــــد / ',
                                            '',
                                            $car->inspection->chassis_inspection,
                                        );
                                        if ($chassisDescription === 'جيــــــــــــــــــــد') {
                                            $chassisDescription = '';
                                        }
                                    } else {
                                        $chassisDescription = $car->inspection->chassis_inspection;
                                    }
                                }

                                // Parse transmission
                                if ($car->inspection->transmission) {
                                    if (strpos($car->inspection->transmission, 'جيــــــــــــــــــــد') === 0) {
                                        $transmissionStatus = true;
                                        $transmissionDescription = str_replace(
                                            'جيــــــــــــــــــــد / ',
                                            '',
                                            $car->inspection->transmission,
                                        );
                                        if ($transmissionDescription === 'جيــــــــــــــــــــد') {
                                            $transmissionDescription = '';
                                        }
                                    } else {
                                        $transmissionDescription = $car->inspection->transmission;
                                    }
                                }

                                // Parse motor
                                if ($car->inspection->motor) {
                                    if (strpos($car->inspection->motor, 'جيــــــــــــــــــــدة') === 0) {
                                        $motorStatus = true;
                                        $motorDescription = $car->inspection->motor;

                                        // Extract percentage if exists
                                        if (preg_match('/النسبة : (\d+)%/', $car->inspection->motor, $matches)) {
                                            $motorPercentage = $matches[1];
                                            // Remove percentage from description
                                            $motorDescription = preg_replace(
                                                '/ - النسبة : \d+%/',
                                                '',
                                                $car->inspection->motor,
                                            );
                                        }

                                        // Remove status prefix
                                        $motorDescription = str_replace(
                                            'جيــــــــــــــــــــدة / ',
                                            '',
                                            $motorDescription,
                                        );
                                        if ($motorDescription === 'جيــــــــــــــــــــدة') {
                                            $motorDescription = '';
                                        }
                                    } else {
                                        $motorDescription = $car->inspection->motor;
                                        // Check if it has percentage without status
                                        if (preg_match('/النسبة : (\d+)%/', $car->inspection->motor, $matches)) {
                                            $motorPercentage = $matches[1];
                                            $motorDescription = preg_replace(
                                                '/ - النسبة : \d+%/',
                                                '',
                                                $car->inspection->motor,
                                            );
                                        }
                                    }
                                }
                            }
                        @endphp
                        <div class="space-y-6 kt-card-content">

                            <!-- Chassis and Mechanical Inspection -->
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                <div class="kt-card">
                                    <div class="kt-card-header">
                                        <h5 class="text-lg font-semibold">Chassis Inspection</h5>
                                    </div>
                                    <div class="kt-card-content">
                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between mb-2 w-full">
                                                <label class="kt-form-label">Chassis Inspection Notes</label>
                                                <div class="flex items-center justify-end gap-2 w-full">
                                                    <input type="checkbox" name="chassis_status" value="good"
                                                        class="kt-checkbox inspection-field"
                                                        {{ $chassisStatus ? 'checked' : '' }}>
                                                    <span class="text-sm text-gray-600">جيـد</span>
                                                </div>
                                            </div>
                                            <textarea class="kt-textarea w-full inspection-field" name="chassis_inspection" rows="4"
                                                placeholder="Enter chassis inspection details">{{ $chassisDescription }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-card">
                                    <div class="kt-card-header">
                                        <h5 class="text-lg font-semibold">Mechanical Inspection</h5>
                                    </div>
                                    <div class="kt-card-content">
                                        <div class="space-y-4">
                                            <div>
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="kt-form-label">Transmission Condition</label>
                                                    <div class="flex items-center justify-end gap-2 w-full">
                                                        <input type="checkbox" name="transmission_status" value="good"
                                                            class="kt-checkbox inspection-field"
                                                            {{ $transmissionStatus ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-600">جيـد</span>
                                                    </div>
                                                </div>
                                                <input type="text" class="kt-input w-full inspection-field"
                                                    name="transmission" value="{{ $transmissionDescription }}"
                                                    placeholder="Enter transmission condition">
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="kt-form-label">Motor Condition</label>
                                                    <div class="flex items-center justify-end gap-2 w-full">
                                                        <input type="checkbox" name="motor_status" value="good"
                                                            class="kt-checkbox inspection-field"
                                                            {{ $motorStatus ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-600">جيــــــــــــــــــــدة</span>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2">
                                                    <div class="flex items-center justify-end gap-1">
                                                        <input type="number" name="motor_percentage"
                                                            class="kt-input w-20 inspection-field"
                                                            value="{{ $motorPercentage }}" placeholder="%"
                                                            min="0" max="100">
                                                    </div>
                                                    <input type="text" class="kt-input flex-1 inspection-field"
                                                        name="motor" value="{{ $motorDescription }}"
                                                        placeholder="Enter motor condition">
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <label class="kt-form-label">Body Notes</label>
                                                <textarea class="kt-textarea w-full inspection-field" name="body_notes" rows="3"
                                                    placeholder="Enter body notes">{{ $car->inspection->body_notes ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Body Parts Inspection -->
                            <div class="kt-card">
                                <div class="kt-card-header">
                                    <h5 class="text-lg font-semibold">Body Parts Inspection (الهيكل)</h5>
                                </div>
                                <div class="kt-card-content">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                        @php
                                            $bodyPartsArabic = \App\Models\CarInspection::getCarPartsArabic();
                                            $bodyParts = [
                                                'hood' => 'Hood (' . $bodyPartsArabic['hood'] . ')',
                                                'front_right_fender' =>
                                                    'Front Right Fender (' .
                                                    $bodyPartsArabic['front_right_fender'] .
                                                    ')',
                                                'front_left_fender' =>
                                                    'Front Left Fender (' . $bodyPartsArabic['front_left_fender'] . ')',
                                                'rear_right_fender' =>
                                                    'Rear Right Fender (' . $bodyPartsArabic['rear_right_fender'] . ')',
                                                'rear_left_fender' =>
                                                    'Rear Left Fender (' . $bodyPartsArabic['rear_left_fender'] . ')',
                                                'trunk_door' => 'Trunk Door (' . $bodyPartsArabic['trunk_door'] . ')',
                                                'front_right_door' =>
                                                    'Front Right Door (' . $bodyPartsArabic['front_right_door'] . ')',
                                                'rear_right_door' =>
                                                    'Rear Right Door (' . $bodyPartsArabic['rear_right_door'] . ')',
                                                'front_left_door' =>
                                                    'Front Left Door (' . $bodyPartsArabic['front_left_door'] . ')',
                                                'rear_left_door' =>
                                                    'Rear Left Door (' . $bodyPartsArabic['rear_left_door'] . ')',
                                            ];
                                            $inspectionOptions = [
                                                'clean_and_free_of_filler' => 'سليم وخالي من المعجون',
                                                'painted' => 'مصبوغ',
                                                'fully_repainted' => 'مصبوغ بالكامل',
                                            ];
                                        @endphp
                                        @foreach ($bodyParts as $field => $label)
                                            <div class="kt-form-item">
                                                <label class="kt-form-label text-sm">{{ $label }}</label>
                                                <select class="kt-select w-full inspection-field text-sm"
                                                    name="{{ $field }}">
                                                    <option value="">Select condition</option>
                                                    @foreach ($inspectionOptions as $value => $option)
                                                        <option value="{{ $value }}"
                                                            {{ ($car->inspection->$field ?? '') == $value ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-success w-full sm:w-auto"
                                    data-action="save-inspection">
                                    <i class="ki-filled ki-check"></i>
                                    Save Inspection
                                </button>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-secondary w-full sm:w-auto"
                                    data-action="cancel-inspection-edit">
                                    <i class="ki-filled ki-cross"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Tab -->
        <div class="hidden" id="tab_1_4">
            <div class="kt-card">
                @if (auth()->user()->hasRole('admin'))
                    <div class="kt-card-header">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-lg ">Financial Summary</h4>
                        </div>
                    </div>
                @endif
                <div class="kt-card-content">
                    @if (auth()->user()->hasRole('admin'))
                        <!-- View Mode -->
                        <div id="financial-view-mode">
                            <div class="grid {{ $car->purchase_price ? 'grid-cols-4' : 'grid-cols-2' }} gap-6">
                                @if ($car->purchase_price)
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <div class="text-2xl  text-green-600">
                                            ${{ number_format($car->purchase_price, 2) }}</div>
                                        <div class="text-sm text-gray-600">Purchase Price</div>
                                    </div>
                                @endif
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl  text-blue-600">
                                        ${{ number_format($car->expected_sale_price, 2) }}</div>
                                    <div class="text-sm text-gray-600">Expected Sale Price</div>
                                </div>
                                @php
                                    $totalCosts = $car->equipmentCosts->sum('amount');
                                    $profit = $car->expected_sale_price - ($car->purchase_price ?? 0) - $totalCosts;
                                @endphp
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl  text-yellow-600">
                                        ${{ number_format($totalCosts, 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total Equipment Costs</div>
                                </div>
                                @if ($car->purchase_price)
                                    <div
                                        class="text-center p-4 {{ $profit >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                                        <div class="text-2xl  {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
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
                                    <input type="number" class="kt-input w-full financial-field" name="purchase_price"
                                        value="{{ $car->purchase_price }}" min="0" step="0.01"
                                        placeholder="0.00">
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">Expected Sale Price</label>
                                    <input type="number" class="kt-input w-full financial-field"
                                        name="expected_sale_price" value="{{ $car->expected_sale_price }}"
                                        min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>

                            <div class="flex gap-2 pt-6">
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-success"
                                    data-action="save-financial">
                                    <i class="ki-filled ki-check"></i>
                                    Save Financial
                                </button>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-secondary"
                                    data-action="cancel-financial-edit">
                                    <i class="ki-filled ki-cross"></i>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="equipment mt-6">
                        <div class="kt-card-header">
                            <div class="flex justify-between items-center w-full">
                                <h4 class="text-lg ">Equipment Costs</h4>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                    data-kt-modal-toggle="#addCostModal">
                                    <i class="ki-filled ki-plus"></i>
                                    Add Cost
                                </button>
                            </div>
                        </div>
                        <div class="kt-card-content">
                            @php
                                // Filter equipment costs based on user role
                                $equipmentCosts = auth()->user()->hasRole('admin')
                                    ? $car->equipmentCosts
                                    : $car->equipmentCosts->where('user_id', auth()->id());
                            @endphp
                            @if ($equipmentCosts->count() > 0)
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
                                            @foreach ($equipmentCosts->sortByDesc('cost_date') as $cost)
                                                <tr class="border-b border-gray-200">
                                                    <td class="py-3 px-4">{{ $cost->description }}</td>
                                                    <td class="py-3 px-4 font-semibold">
                                                        ${{ number_format($cost->amount, 2) }}</td>
                                                    <td class="py-3 px-4">{{ $cost->cost_date->format('M j, Y') }}
                                                    </td>
                                                    <td class="py-3 px-4 text-gray-600">
                                                        {{ $cost->user->name ?? '—' }}
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
                                    <h4 class="text-lg  text-gray-900 mb-2">No equipment costs</h4>
                                    <p class="text-gray-600">
                                        @if (auth()->user()->hasRole('admin'))
                                            No equipment costs have been recorded for this car.
                                        @else
                                            You haven't added any equipment costs for this car yet.
                                        @endif
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
                    <h4 class="text-lg ">Status History</h4>
                </div>
                <div class="kt-card-content">
                    @if ($car->statusHistories->count() > 0)
                        <div class="space-y-4">
                            @foreach ($car->statusHistories->sortByDesc('created_at') as $history)
                                <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @php
                                            $statusClass =
                                                $statusConfig[$history->status]['class'] ?? 'kt-badge-secondary';
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
                            <h4 class="text-lg  text-gray-900 mb-2">No status history</h4>
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
                        <h4 class="text-lg ">Car Images</h4>
                    </div>
                </div>
                <div class="kt-card-content">
                    <!-- View Mode -->
                    <div id="images-view-mode">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Car License Image -->
                            <div class="kt-card">
                                <div class="kt-card-header">
                                    <h4 class="text-lg ">Car License
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
                                            <h4 class="text-lg  text-gray-900 mb-2">No License Image</h4>
                                            <p class="text-gray-600">No car license image has been uploaded.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Car Images -->
                            <div class="kt-card">
                                <div class="kt-card-header">
                                    <h4 class="text-lg ">Car Images
                                        ({{ $car->getMedia('car_images')->count() }})</h4>
                                </div>
                                <div class="kt-card-content">
                                    @if ($car->getMedia('car_images')->count() > 0)
                                        <div id="car-images-gallery"
                                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @foreach ($car->getMedia('car_images') as $image)
                                                <div class="relative group cursor-pointer"
                                                    data-src="{{ $image->getUrl() }}">
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
                                            <h4 class="text-lg  text-gray-900 mb-2">No Car Images</h4>
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
                                    <h4 class="text-lg ">Car License</h4>
                                </div>
                                <div class="kt-card-content">
                                    <div class="kt-form-item">
                                        <label class="kt-form-label">Upload License Image</label>
                                        <input type="file" class="kt-input w-full images-field" name="car_license"
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
                                    <h4 class="text-lg ">Car Images</h4>
                                </div>
                                <div class="kt-card-content">
                                    <div class="kt-form-item">
                                        <label class="kt-form-label">Upload Car Images</label>
                                        <input type="file" class="kt-input w-full images-field" name="car_images[]"
                                            accept="image/*" multiple>
                                        <div class="text-sm text-gray-500 mt-1">
                                            Accepted formats: JPEG, PNG, JPG (max 2MB each)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-6">
                            <button type="button" class="kt-btn kt-btn-sm kt-btn-success" data-action="save-images">
                                <i class="ki-filled ki-check"></i>
                                Save Images
                            </button>
                            <button type="button" class="kt-btn kt-btn-sm kt-btn-secondary"
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
    <div id="car-data" data-car-id="{{ $car->id }}"
        data-update-url="{{ route('cars.update-inline', $car->id) }}"
        data-equipment-cost-url="{{ route('cars.add-equipment-cost', $car->id) }}" style="display: none;"></div>

    @if (auth()->user()->hasRole('admin'))
        <!-- Sell Car Modal -->
        <div class="kt-modal kt-modal-center" data-kt-modal="true" id="sellCarModal">
            <div class="kt-modal-content max-w-[500px] max-h-[95%]">
                <div class="kt-modal-header">
                    <h3 class="kt-modal-title">Sell Car: <span id="sellCarModel"></span></h3>
                    <button type="button" class="kt-modal-close" aria-label="Close modal"
                        data-kt-modal-dismiss="#sellCarModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="kt-modal-body">
                    <form id="sellCarForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="car_id" id="sellCarId">
                        <div class="space-y-4">
                            <div>
                                <label for="sale_price" class="kt-label">Sale Price *</label>
                                <input type="number" name="sale_price" id="sale_price" class="kt-input w-full" required
                                    min="0" step="0.01">
                            </div>
                            <div>
                                <label for="payment_method" class="kt-label">Payment Method *</label>
                                <select name="payment_method" id="payment_method" class="kt-select w-full" required
                                    onchange="togglePaymentFields()">
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="separated">Separated</option>
                                </select>
                            </div>
                            <div class="hidden" id="separatedFields">
                                <div>
                                    <label for="paid_amount" class="kt-label">Paid Amount</label>
                                    <input type="number" name="paid_amount" id="paid_amount" class="kt-input w-full"
                                        min="0" step="0.01">
                                </div>
                                <div class="mt-3">
                                    <label for="remaining_amount" class="kt-label">Remaining Amount</label>
                                    <input type="number" name="remaining_amount" id="remaining_amount"
                                        class="kt-input w-full" min="0" step="0.01">
                                </div>
                            </div>
                            <div>
                                <label for="attachment" class="kt-label">Attachment</label>
                                <input type="file" name="attachment" id="attachment" class="kt-input w-full">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="kt-modal-footer">
                    <div class="flex gap-4">
                        <button class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#sellCarModal">
                            Cancel
                        </button>
                        <button type="submit" form="sellCarForm" class="kt-btn kt-btn-primary">
                            Confirm Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        function openSellModal(carId, carModel) {
            document.getElementById('sellCarId').value = carId;
            document.getElementById('sellCarModel').textContent = carModel;
            document.getElementById('sellCarForm').action = '/cars/' + carId + '/sell';

            // Use KtUI modal methods
            const modal = document.getElementById('sellCarModal');
            if (window.KTModal) {
                const modalInstance = KTModal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.show();
                }
            }
        }

        function togglePaymentFields() {
            var method = document.getElementById('payment_method').value;
            document.getElementById('separatedFields').classList.toggle('hidden', method !== 'separated');
        }
    </script>
@endsection

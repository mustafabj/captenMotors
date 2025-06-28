@extends('layouts.app')

@section('content')
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
                    <div class="flex items-center gap-2">
                        <i class="ki-filled ki-gear text-gray-400"></i>
                        <span class="text-sm font-mono">{{ $car->chassis_number }}</span>
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
                <div class="flex items-center gap-2">
                    <a href="{{ route('cars.edit', $car) }}" class="kt-btn kt-btn-sm kt-btn-outline">
                        <i class="ki-filled ki-pencil"></i>
                        Edit
                    </a>
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
                                    <input type="text" class="kt-input w-auto" value="{{ $car->model }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Vehicle Category</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->vehicle_category ?? 'Not specified' }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Manufacturing Year</span>
                                    <input type="text" class="kt-input w-auto" value="{{ $car->manufacturing_year }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Capacity</span>
                                    <input type="text" class="kt-input w-auto" value="{{ $car->engine_capacity }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Place of Manufacture</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->place_of_manufacture ?? 'Not specified' }}" readonly>
                                </div>

                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Engine Type</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->engine_type ?? 'Not specified' }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Number of Keys</span>
                                    <input type="text" class="kt-input w-auto" value="{{ $car->number_of_keys }}"
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
                                    <span class="text-gray-600 font-semibold block">Chassis Number</span>
                                    <input type="text" class="kt-input w-auto font-mono"
                                        value="{{ $car->chassis_number }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Plate Number</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->plate_number ?? 'Not assigned' }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Status</span>
                                    <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
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
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->insurance_expiry_date->format('F j, Y') }}" readonly>
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
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->purchase_date->format('F j, Y') }}" readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Purchase Price</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="{{ $car->purchase_price ? '$' . number_format($car->purchase_price, 2) : 'Not specified' }}"
                                        readonly>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600 font-semibold block">Expected Sale Price</span>
                                    <input type="text" class="kt-input w-auto"
                                        value="${{ number_format($car->expected_sale_price, 2) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options Tab -->
                <div class="hidden" id="tab_1_2">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h4 class="text-lg font-bold mb-6">Car Options</h4>
                        </div>
                        <div class="kt-card-content">
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
                    </div>
                </div>

                <!-- Inspection Tab -->
                <div class="hidden" id="tab_1_3">
                    @if ($car->inspection)
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h4 class="text-lg font-bold mb-6">Inspection Details</h4>
                            </div>
                            <div class="kt-card-content">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Chassis Inspection</h5>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Front Chassis Right</span>
                                                <input class="kt-input w-auto"
                                                    value="{{ $car->inspection->front_chassis_right }}" readonly></input>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Front Chassis Left</span>
                                                <input class="kt-input w-auto"
                                                    value="{{ $car->inspection->front_chassis_left }}" readonly></input>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Rear Chassis Right</span>
                                                <input class="kt-input w-auto"
                                                    value="{{ $car->inspection->rear_chassis_right }}" readonly></input>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Rear Chassis Left</span>
                                                <input class="kt-input w-auto"
                                                    value="{{ $car->inspection->rear_chassis_left }}" readonly></input>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-md font-semibold mb-4">Mechanical Inspection</h5>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Transmission Condition</span>
                                                <input class="kt-input w-auto"
                                                    value="{{ $car->inspection->transmission }}" readonly></input>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Motor Condition</span>
                                                <input class="kt-input w-auto" value="{{ $car->inspection->motor }}"
                                                    readonly></input>
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
                            </div>
                        </div>
                    @else
                        <div class="kt-card">
                            <div class="kt-card-content text-center py-12">
                                <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">No inspection data</h4>
                                <p class="text-gray-600">No inspection information has been recorded for this car.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Financial Tab -->
                <div class="hidden" id="tab_1_4">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h4 class="text-lg font-bold mb-6">Financial Summary</h4>
                        </div>
                        <div class="kt-card-content">
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

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabToggles = document.querySelectorAll('[data-kt-tab-toggle]');
            const tabContents = document.querySelectorAll('[id^="tab_1_"]');

            tabToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-kt-tab-toggle');

                    // Remove active class from all toggles and hide all contents
                    tabToggles.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Add active class to clicked toggle and show target content
                    this.classList.add('active');
                    document.querySelector(targetId).classList.remove('hidden');
                });
            });

            // Initialize lightGallery for car images
            if (document.getElementById('car-images-gallery')) {
                lightGallery(document.getElementById('car-images-gallery'), {
                    plugins: [lgThumbnail, lgAutoplay, lgFullscreen],
                    speed: 500,
                    download: true,
                    counter: true,
                    thumbnail: true,
                    autoplay: true,
                    autoplayControls: true,
                    fullscreen: true
                });
            }

            // Initialize lightGallery for car license
            if (document.getElementById('car-license-gallery')) {
                lightGallery(document.getElementById('car-license-gallery'), {
                    plugins: [lgThumbnail, lgAutoplay, lgFullscreen],
                    speed: 500,
                    download: true,
                    counter: true,
                    thumbnail: true,
                    autoplay: true,
                    autoplayControls: true,
                    fullscreen: true
                });
            }

            // AJAX Form Submission for Equipment Cost
            document.getElementById('addCostForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const submitBtn = document.getElementById('submitCostBtn');
                const submitText = submitBtn.querySelector('.submit-text');
                const loadingText = submitBtn.querySelector('.loading-text');
                
                // Clear previous errors
                clearFormErrors();
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                
                // Prepare form data
                const formData = new FormData(form);
                
                // Submit via AJAX
                fetch("{{ route('cars.add-equipment-cost', $car) }}", {
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
                        // Success - add new row to table and close modal
                        showNotification('Equipment cost added successfully!', 'success');
                        
                        // Add new row to the equipment costs table
                        addEquipmentCostRow(data.cost);
                        
                        // Close modal
                        const modalEl = document.querySelector('#addCostModal');
                        const modal = KTModal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Reset form
                        document.getElementById('addCostForm').reset();
                    } else {
                        // Show validation errors
                        showFormErrors(data.errors);
                        showNotification('Please correct the errors below.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while adding the cost.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                });
            });

            // Function to clear all form errors
            function clearFormErrors() {
                const form = document.getElementById('addCostForm');
                const inputs = form.querySelectorAll('input, textarea');
                const messages = form.querySelectorAll('.kt-form-message');
                
                inputs.forEach(input => {
                    input.removeAttribute('aria-invalid');
                    input.classList.remove('is-invalid');
                });
                
                messages.forEach(message => {
                    message.innerHTML = '';
                    message.style.display = 'none';
                });
            }

            // Function to show form errors
            function showFormErrors(errors) {
                const form = document.getElementById('addCostForm');
                
                Object.keys(errors).forEach(fieldName => {
                    const field = form.querySelector(`[name="${fieldName}"]`);
                    const messageDiv = field ? field.closest('.kt-form-control').querySelector('.kt-form-message') : null;
                    
                    if (field && messageDiv) {
                        // Add error styling to field
                        field.setAttribute('aria-invalid', 'true');
                        field.classList.add('is-invalid');
                        
                        // Show error message
                        messageDiv.innerHTML = `<div class="text-danger">${errors[fieldName][0]}</div>`;
                        messageDiv.style.display = 'block';
                    }
                });
            }

            // Function to show notifications
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

            // Function to add new equipment cost row to table
            function addEquipmentCostRow(costData) {
                const tableBody = document.querySelector('#equipment-costs-table tbody');
                const emptyState = document.querySelector('.equipment .kt-card-content .text-center');
                
                // Remove empty state if it exists
                if (emptyState) {
                    emptyState.remove();
                }
                
                // Create new row
                const newRow = document.createElement('tr');
                newRow.className = 'border-b border-gray-200';
                newRow.innerHTML = `
                    <td class="py-3 px-4">${costData.description}</td>
                    <td class="py-3 px-4 font-semibold">$${parseFloat(costData.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="py-3 px-4">${new Date(costData.cost_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                    <td class="py-3 px-4 text-gray-600">${costData.user_name}</td>
                `;
                
                // Add row to the beginning of the table (most recent first)
                if (tableBody) {
                    tableBody.insertBefore(newRow, tableBody.firstChild);
                }
            }
        });
    </script>
@endsection

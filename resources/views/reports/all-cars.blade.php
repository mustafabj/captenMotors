@extends('layouts.app')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Header -->
        <div class="kt-card space-y-4">
            <div class="kt-card-content p-7.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">All Cars Report</h1>
                        <p class="text-gray-600 mt-1">
                            Comprehensive overview of all cars in the system.
                        </p>
                    </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="kt-card">
                <div class="kt-card-content p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="ki-filled ki-car text-2xl text-blue-500"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $totalCars }}</div>
                            <div class="text-sm text-gray-500">Total Cars</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-content p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="ki-filled ki-check-circle text-2xl text-green-500"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $totalAvailable }}</div>
                            <div class="text-sm text-gray-500">Available Cars</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-content p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="ki-filled ki-dollar text-2xl text-purple-500"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $totalSold }}</div>
                            <div class="text-sm text-gray-500">Sold Cars</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-content p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="ki-filled ki-chart-line text-2xl text-orange-500"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">${{ number_format($totalPurchaseValue, 0) }}</div>
                            <div class="text-sm text-gray-500">Total Investment</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <form method="GET" action="{{ route('reports.all-cars') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ $search }}"
                               placeholder="Search by model, plate number, or category..."
                               class="kt-input w-full">
                    </div>
                    
                    <div class="min-w-[150px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="kt-select w-full">
                            <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>All Status</option>
                            <option value="not_ready" {{ $status === 'not_ready' ? 'selected' : '' }}>Not Ready</option>
                            <option value="not_received" {{ $status === 'not_received' ? 'selected' : '' }}>Not Received</option>
                            <option value="paint" {{ $status === 'paint' ? 'selected' : '' }}>Paint</option>
                            <option value="upholstery" {{ $status === 'upholstery' ? 'selected' : '' }}>Upholstery</option>
                            <option value="mechanic" {{ $status === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                            <option value="electrical" {{ $status === 'electrical' ? 'selected' : '' }}>Electrical</option>
                            <option value="agency" {{ $status === 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="polish" {{ $status === 'polish' ? 'selected' : '' }}>Polish</option>
                            <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="sold" {{ $status === 'sold' ? 'selected' : '' }}>Sold</option>
                        </select>
                    </div>
                    
                    <div class="min-w-[120px]">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                        <select name="year" id="year" class="kt-select w-full">
                            <option value="">All Years</option>
                            @for ($year = date('Y'); $year >= 2015; $year--)
                                <option value="{{ $year }}" {{ $year == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="min-w-[150px]">
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort_by" id="sort_by" class="kt-select w-full">
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Date Added</option>
                            <option value="model" {{ $sortBy === 'model' ? 'selected' : '' }}>Model</option>
                            <option value="manufacturing_year" {{ $sortBy === 'manufacturing_year' ? 'selected' : '' }}>Year</option>
                            <option value="purchase_price" {{ $sortBy === 'purchase_price' ? 'selected' : '' }}>Purchase Price</option>
                            <option value="expected_sale_price" {{ $sortBy === 'expected_sale_price' ? 'selected' : '' }}>Expected Price</option>
                        </select>
                    </div>
                    
                    <div class="min-w-[120px]">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                        <select name="sort_order" id="sort_order" class="kt-select w-full">
                            <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-magnifier"></i>
                            Filter
                        </button>
                        <a href="{{ route('reports.all-cars') }}" class="kt-btn kt-btn-secondary">
                            <i class="ki-filled ki-cross"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cars Table -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Cars List</h3>
                <div class="text-sm text-gray-500">
                    Showing {{ $cars->firstItem() ?? 0 }} to {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} cars
                </div>
            </div>
            <div class="kt-card-content p-0">
                @if($cars->count() > 0)
                <div class="overflow-x-auto">
                    <table class="kt-table w-full">
                        <thead class="kt-table-head">
                            <tr>
                                <th class="kt-table-cell">Car Information</th>
                                <th class="kt-table-cell">Financial</th>
                                <th class="kt-table-cell">Status</th>
                                <th class="kt-table-cell">Costs</th>
                                <th class="kt-table-cell">Sale Info</th>
                                <th class="kt-table-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="kt-table-body">
                            @foreach($cars as $car)
                            <tr class="kt-table-row hover:bg-gray-50">
                                <td class="kt-table-cell">
                                    <div class="space-y-1">
                                        <div class="font-medium text-gray-900">{{ $car->model }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $car->manufacturing_year }} • {{ $car->vehicle_category ?? 'N/A' }}
                                        </div>
                                        @if($car->plate_number)
                                        <div class="text-sm text-gray-500">Plate: {{ $car->plate_number }}</div>
                                        @endif
                                        @if($car->color)
                                        <div class="text-sm text-gray-500">Color: {{ $car->color }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="space-y-1">
                                        <div class="text-sm">
                                            <span class="text-gray-500">Purchase:</span>
                                            <span class="font-medium">${{ number_format($car->purchase_price, 0) }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-gray-500">Expected:</span>
                                            <span class="font-medium">${{ number_format($car->expected_sale_price, 0) }}</span>
                                        </div>
                                        @php
                                            $profit = $car->expected_sale_price - $car->purchase_price;
                                            $profitClass = $profit >= 0 ? 'text-green-600' : 'text-red-600';
                                        @endphp
                                        <div class="text-sm {{ $profitClass }}">
                                            <span class="text-gray-500">Profit:</span>
                                            <span class="font-medium">{{ $profit >= 0 ? '+' : '' }}${{ number_format($profit, 0) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="space-y-1">
                                        <span class="kt-badge {{ $car->status === 'ready' ? 'kt-badge-success' : ($car->status === 'sold' ? 'kt-badge-danger' : 'kt-badge-warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $car->status)) }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            {{ $car->created_at->format('M j, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="space-y-1">
                                        @php
                                            $equipmentCosts = $car->equipmentCosts->sum('amount');
                                            $otherCosts = $car->otherCosts->sum('amount');
                                            $totalCosts = $equipmentCosts + $otherCosts;
                                        @endphp
                                        @if($equipmentCosts > 0)
                                        <div class="text-sm">
                                            <span class="text-gray-500">Equipment:</span>
                                            <span class="font-medium">${{ number_format($equipmentCosts, 0) }}</span>
                                        </div>
                                        @endif
                                        @if($otherCosts > 0)
                                        <div class="text-sm">
                                            <span class="text-gray-500">Other:</span>
                                            <span class="font-medium">${{ number_format($otherCosts, 0) }}</span>
                                        </div>
                                        @endif
                                        @if($totalCosts > 0)
                                        <div class="text-sm font-medium text-blue-600">
                                            Total: ${{ number_format($totalCosts, 0) }}
                                        </div>
                                        @else
                                        <div class="text-sm text-gray-400">No costs</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    @if($car->soldCar)
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium text-green-600">
                                            ${{ number_format($car->soldCar->sale_price, 0) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ucfirst($car->soldCar->payment_method) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            by {{ $car->soldCar->soldByUser->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $car->soldCar->created_at->format('M j, Y') }}
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-400">Not sold</div>
                                    @endif
                                </td>
                                <td class="kt-table-cell">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('cars.show', $car) }}" 
                                           class="kt-btn kt-btn-sm kt-btn-outline">
                                            <i class="ki-filled ki-eye"></i>
                                            View
                                        </a>
                                        @if($car->soldCar)
                                        <a href="{{ route('sold-cars.show', $car->soldCar) }}" 
                                           class="kt-btn kt-btn-sm kt-btn-outline">
                                            <i class="ki-filled ki-dollar"></i>
                                            Sale
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $cars->appends(request()->query())->links() }}
                </div>
                @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-filled ki-car text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Cars Found</h3>
                    <p class="text-gray-500">
                        @if(request()->hasAny(['search', 'status', 'year']))
                            No cars match your current filters.
                        @else
                            There are no cars in the system.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'year']))
                    <div class="mt-4">
                        <a href="{{ route('reports.all-cars') }}" class="kt-btn kt-btn-primary">
                            Clear Filters
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
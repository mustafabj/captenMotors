@extends('layouts.app')

@section('content')
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $bulkDeal->name }}</h1>
            <p class="text-sm text-gray-500">Home - Bulk Deal Management - View</p>
        </div>
        <div>
            <a href="{{ route('bulk-deals.index') }}" class="kt-btn kt-btn-outline">
                Back to List
            </a>
            <a href="{{ route('bulk-deals.edit', $bulkDeal) }}" class="kt-btn kt-btn-primary ml-2">
                Edit Deal
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Deal Information -->
        <div class="lg:col-span-1 space-y-5">
            <!-- Deal Information Card -->
            <div class="kt-card">
                <div class="kt-card-header kt-card-header-space-between">
                    <div class="kt-card-title">
                        <h3 class="kt-card-title-text">Deal Information</h3>
                    </div>
                </div>
                <div class="kt-card-content">
                    <div class="space-y-5">
                        <!-- Name -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-1 min-w-0">
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Deal Name</label>
                                <p class="text-base font-medium text-gray-900 break-words">{{ $bulkDeal->name }}</p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-1 min-w-0">
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Description</label>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $bulkDeal->description ?: 'No description provided' }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        {{-- <div class="flex items-start space-x-3">
                            <div class="flex-1 min-w-0">
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Status</label>
                                @php
                                    $statusConfig = [
                                        'active' => ['bg' => 'bg-success-light', 'text' => 'text-success'],
                                        'completed' => ['bg' => 'bg-primary-light', 'text' => 'text-primary'],
                                        'cancelled' => ['bg' => 'bg-danger-light', 'text' => 'text-danger']
                                    ];
                                    $config = $statusConfig[$bulkDeal->status] ?? $statusConfig['active'];
                                @endphp
                                <div class="inline-flex items-center px-3 py-2 rounded-lg {{ $config['bg'] }} {{ $config['text'] }}">
                                    <span class="text-sm font-medium">{{ ucfirst($bulkDeal->status) }}</span>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-4"></div>

                        <!-- Timestamps -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600">Created</label>
                                    <p class="text-sm text-gray-700">{{ $bulkDeal->created_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-600">Last Updated</label>
                                    <p class="text-sm text-gray-700">{{ $bulkDeal->updated_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="kt-card">
                <div class="kt-card-header kt-card-header-space-between">
                    <div class="kt-card-title">
                        <h3 class="kt-card-title-text">Financial Summary</h3>
                    </div>
                </div>
                <div class="kt-card-content">
                    <div class="space-y-4">
                        @if($bulkDeal->total_value !== null)
                            <!-- Manual Total -->
                            <div class="bg-warning-light rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-warning-inverse">Manual Override</span>
                                    </div>
                                    <span class="text-lg font-bold text-warning-inverse">${{ number_format($bulkDeal->total_value, 2) }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Dynamic Total -->
                        <div class="bg-success-light rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-success-inverse">Calculated Total</span>
                                </div>
                                <span class="text-xl font-bold text-success-inverse">${{ number_format($bulkDeal->calculated_total_value, 2) }}</span>
                            </div>
                            <p class="text-xs text-success-inverse mt-1 opacity-75">Based on car purchase prices</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="kt-card">
                <div class="kt-card-header kt-card-header-space-between">
                    <div class="kt-card-title">
                        <h3 class="kt-card-title-text">Quick Stats</h3>
                    </div>
                </div>
                <div class="kt-card-content">
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Total Cars -->
                        <div class="text-center bg-primary-light rounded-lg p-4">
                            <div class="text-3xl font-bold text-primary mb-1">{{ $bulkDeal->cars->count() }}</div>
                            <div class="text-sm font-medium text-primary-inverse">Total Cars</div>
                        </div>
                        
                        <!-- Average Price -->
                        @if($bulkDeal->cars->count() > 0)
                            <div class="text-center bg-info-light rounded-lg p-4">
                                <div class="text-2xl font-bold text-info mb-1">
                                    ${{ number_format($bulkDeal->calculated_total_value / $bulkDeal->cars->count(), 0) }}
                                </div>
                                <div class="text-sm font-medium text-info-inverse">Average Price</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Cars -->
        <div class="lg:col-span-2">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold">Associated Cars ({{ $bulkDeal->cars->count() }})</h3>
                </div>
                <div class="kt-card-content p-0">
                    @if($bulkDeal->cars->count() > 0)
                        <div class="kt-table-wrapper">
                            <table class="kt-table">
                                <thead>
                                    <tr>
                                        <th>Model</th>
                                        <th>Year</th>
                                        <th>Status</th>
                                        <th>Purchase Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bulkDeal->cars as $car)
                                        <tr>
                                            <td>
                                                <div class="text-sm font-medium text-gray-900">{{ $car->model }}</div>
                                            </td>
                                            <td>
                                                {{ $car->manufacturing_year }}
                                            </td>
                                            <td>
                                                @php
                                                    $carStatusColors = [
                                                        'not_received' => 'bg-gray-100 text-gray-800',
                                                        'paint' => 'bg-yellow-100 text-yellow-800',
                                                        'upholstery' => 'bg-purple-100 text-purple-800',
                                                        'mechanic' => 'bg-orange-100 text-orange-800',
                                                        'electrical' => 'bg-pink-100 text-pink-800',
                                                        'agency' => 'bg-indigo-100 text-indigo-800',
                                                        'polish' => 'bg-teal-100 text-teal-800',
                                                        'ready' => 'bg-green-100 text-green-800'
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $carStatusColors[$car->status] }}">
                                                    {{ ucfirst(str_replace('_', ' ', $car->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                ${{ number_format($car->purchase_price, 2) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('cars.show', $car) }}" 
                                                   class="inline-flex items-center px-2 py-1 rounded hover:bg-blue-50 text-blue-600"
                                                   title="View Car">
                                                    <span class="ki-duotone ki-eye text-blue-500 mr-1" style="font-size: 1.25rem;"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <i class="fas fa-car text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No cars associated</h3>
                            <p class="text-gray-500 mb-4">This bulk deal doesn't have any cars yet.</p>
                            <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                                Add Car to Deal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 
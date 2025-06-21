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
        <div class="lg:col-span-1 space-y-6">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold">Deal Information</h3>
                </div>
                <div class="kt-card-content p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="text-sm text-gray-900">{{ $bulkDeal->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="text-sm text-gray-900">{{ $bulkDeal->description ?: 'No description provided' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Total Value</label>
                        @if($bulkDeal->total_value !== null)
                            <p class="text-sm text-gray-900">${{ number_format($bulkDeal->total_value, 2) }} (Manually set)</p>
                        @endif
                        <p class="text-sm text-gray-900">${{ number_format($bulkDeal->calculated_total_value, 2) }} (Dynamic from cars)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bulkDeal->status] }}">
                            {{ ucfirst($bulkDeal->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="text-sm text-gray-900">{{ $bulkDeal->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ $bulkDeal->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold">Statistics</h3>
                </div>
                <div class="kt-card-content p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $bulkDeal->cars->count() }}</div>
                            <div class="text-sm text-gray-500">Total Cars</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                ${{ number_format($bulkDeal->calculated_total_value, 2) }}
                            </div>
                            <div class="text-sm text-gray-500">Total Value</div>
                        </div>
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
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bulkDeal->cars as $car)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $car->model }}</div>
                                                <div class="text-sm text-gray-500">{{ $car->chassis_number }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $car->manufacturing_year }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($car->purchase_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('cars.show', $car) }}" class="text-blue-600 hover:text-blue-900">View</a>
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
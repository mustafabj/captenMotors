@extends('layouts.app')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Header -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Sold Car Details</h1>
                        <p class="text-gray-600 mt-1">
                            Detailed information about the sold car.
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('sold-cars.index') }}" class="kt-btn kt-btn-secondary">
                            <i class="ki-filled ki-arrow-left"></i>
                            Back to Sold Cars
                        </a>
                        <a href="{{ route('cars.show', $soldCar->car) }}" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-eye"></i>
                            View Car Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sold Car Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-7.5">
            <!-- Car Information -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Car Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Model</span>
                            <span class="text-gray-900">{{ $soldCar->car->model }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Manufacturing Year</span>
                            <span class="text-gray-900">{{ $soldCar->car->manufacturing_year }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Color</span>
                            <span class="text-gray-900">{{ $soldCar->car->color ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Purchase Price</span>
                            <span class="text-gray-900">${{ number_format($soldCar->car->purchase_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Expected Sale Price</span>
                            <span class="text-gray-900">${{ number_format($soldCar->car->expected_sale_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Information -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Sale Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Sale Price</span>
                            <span class="text-green-600 font-bold text-lg">${{ number_format($soldCar->sale_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Payment Method</span>
                            <span class="kt-badge kt-badge-info">{{ ucfirst($soldCar->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Sold By</span>
                            <span class="text-gray-900">{{ $soldCar->soldByUser->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Sale Date</span>
                            <span class="text-gray-900">{{ $soldCar->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @if($soldCar->payment_method === 'separated')
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Paid Amount</span>
                            <span class="text-gray-900">${{ number_format($soldCar->paid_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Remaining Amount</span>
                            <span class="text-gray-900">${{ number_format($soldCar->remaining_amount ?? 0, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Profit</span>
                            @php
                                $profit = $soldCar->sale_price - $soldCar->car->purchase_price;
                                $profitClass = $profit >= 0 ? 'text-green-600' : 'text-red-600';
                            @endphp
                            <span class="font-bold text-lg {{ $profitClass }}">
                                {{ $profit >= 0 ? '+' : '' }}${{ number_format($profit, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachment Section -->
        @if($soldCar->attachment)
        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Sale Attachment</h3>
            </div>
            <div class="kt-card-content">
                <div class="flex items-center space-x-4">
                    <i class="ki-filled ki-document text-2xl text-blue-500"></i>
                    <div>
                        <p class="text-sm text-gray-600">Sale document attached</p>
                        <a href="{{ Storage::url($soldCar->attachment) }}" 
                           target="_blank" 
                           class="text-blue-600 hover:text-blue-700 text-sm">
                            View Attachment
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 
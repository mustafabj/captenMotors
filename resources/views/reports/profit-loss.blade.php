@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Profit & Loss Report</h3>
        </div>
    </div>
    <div class="kt-card-body p-5">
        <!-- Date Range Filter -->
        <div class="mb-5">
            <form method="GET" class="flex gap-4 items-end">
                <div>
                    <label class="kt-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="kt-input">
                </div>
                <div>
                    <label class="kt-label">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="kt-input">
                </div>
                <button type="submit" class="kt-btn kt-btn-primary">Filter</button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="kt-card bg-blue-50 border border-blue-200">
                <div class="kt-card-body p-4">
                    <div class="text-blue-600 text-sm font-medium">Cash Capital</div>
                    <div class="text-2xl font-bold text-blue-800">${{ number_format($totalCapital, 2) }}</div>
                    <div class="text-xs text-blue-600 mt-1">Money deposited</div>
                </div>
            </div>
            <div class="kt-card bg-purple-50 border border-purple-200">
                <div class="kt-card-body p-4">
                    <div class="text-purple-600 text-sm font-medium">Car Investments</div>
                    <div class="text-2xl font-bold text-purple-800">${{ number_format($totalPurchaseCost, 2) }}</div>
                    <div class="text-xs text-purple-600 mt-1">Total car purchases</div>
                </div>
            </div>
            <div class="kt-card bg-green-50 border border-green-200">
                <div class="kt-card-body p-4">
                    <div class="text-green-600 text-sm font-medium">Total Sales Revenue</div>
                    <div class="text-2xl font-bold text-green-800">${{ number_format($totalSalesRevenue, 2) }}</div>
                    <div class="text-xs text-green-600 mt-1">From sold cars</div>
                </div>
            </div>
            <div class="kt-card {{ $netProfit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                <div class="kt-card-body p-4">
                    <div class="{{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} text-sm font-medium">Net Profit/Loss</div>
                    <div class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-800' : 'text-red-800' }}">${{ number_format($netProfit, 2) }}</div>
                    <div class="text-xs {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">Sales - Total Costs</div>
                </div>
            </div>
        </div>

        <!-- Total Available Capital Card -->
        <div class="mb-8">
            <div class="kt-card bg-indigo-50 border border-indigo-200">
                <div class="kt-card-body p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-indigo-600 text-lg font-semibold">Total Available Capital</div>
                            <div class="text-indigo-700 text-sm">Cash Capital + Car Investments</div>
                        </div>
                        <div class="text-3xl font-bold text-indigo-800">${{ number_format($totalAvailableCapital, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Breakdown -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Capital & Revenue Breakdown</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Cash Capital (Deposited):</span>
                            <span class="font-semibold">${{ number_format($totalCapital, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Car Investments (Purchases):</span>
                            <span class="font-semibold">${{ number_format($totalPurchaseCost, 2) }}</span>
                        </div>
                        <div class="flex justify-between bg-indigo-50 px-2 py-1 rounded">
                            <span class="font-medium">Total Available Capital:</span>
                            <span class="font-bold text-indigo-800">${{ number_format($totalAvailableCapital, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between">
                            <span>Sales Revenue:</span>
                            <span class="font-semibold">${{ number_format($totalSalesRevenue, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total Capital + Revenue:</span>
                            <span>${{ number_format($totalAvailableCapital + $totalSalesRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Cost Breakdown</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Car Purchase Costs:</span>
                            <span class="font-semibold">${{ number_format($totalPurchaseCost, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Equipment Costs:</span>
                            <span class="font-semibold">${{ number_format($totalEquipmentCosts, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Other Costs:</span>
                            <span class="font-semibold">${{ number_format($totalOtherCosts, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between font-bold">
                            <span>Total Costs:</span>
                            <span>${{ number_format($totalCosts, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sold Cars Table -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h4 class="text-md font-semibold">Sold Cars ({{ $soldCars->count() }})</h4>
            </div>
            <div class="kt-card-body p-4">
                <div class="overflow-x-auto">
                    <table class="kt-table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Car Model</th>
                                <th class="text-left">Sale Date</th>
                                <th class="text-right">Purchase Price</th>
                                <th class="text-right">Sale Price</th>
                                <th class="text-right">Profit/Loss</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($soldCars as $soldCar)
                                <tr>
                                    <td>{{ $soldCar->car->model }}</td>
                                    <td>{{ $soldCar->created_at->format('M d, Y') }}</td>
                                    <td class="text-right">${{ number_format($soldCar->car->purchase_price, 2) }}</td>
                                    <td class="text-right">${{ number_format($soldCar->sale_price, 2) }}</td>
                                    <td class="text-right font-semibold {{ ($soldCar->sale_price - $soldCar->car->purchase_price) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        ${{ number_format($soldCar->sale_price - $soldCar->car->purchase_price, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">No sold cars found for the selected date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
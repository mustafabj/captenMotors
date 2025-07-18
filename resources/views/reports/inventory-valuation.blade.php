@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Inventory Valuation Report</h3>
        </div>
    </div>
    <div class="kt-card-body p-5">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="kt-card bg-blue-50 border border-blue-200">
                <div class="kt-card-body p-4">
                    <div class="text-blue-600 text-sm font-medium">Current Inventory Value</div>
                    <div class="text-2xl font-bold text-blue-800">${{ number_format($totalPurchaseValue, 2) }}</div>
                    <div class="text-xs text-blue-600 mt-1">Purchase cost of unsold cars</div>
                </div>
            </div>
            <div class="kt-card bg-green-50 border border-green-200">
                <div class="kt-card-body p-4">
                    <div class="text-green-600 text-sm font-medium">Expected Sale Value</div>
                    <div class="text-2xl font-bold text-green-800">${{ number_format($totalExpectedSaleValue, 2) }}</div>
                    <div class="text-xs text-green-600 mt-1">If all current cars sold</div>
                </div>
            </div>
            <div class="kt-card bg-orange-50 border border-orange-200">
                <div class="kt-card-body p-4">
                    <div class="text-orange-600 text-sm font-medium">Total Current Value</div>
                    <div class="text-2xl font-bold text-orange-800">${{ number_format($totalCurrentValue, 2) }}</div>
                    <div class="text-xs text-orange-600 mt-1">Purchase + costs</div>
                </div>
            </div>
            <div class="kt-card {{ $potentialProfit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                <div class="kt-card-body p-4">
                    <div class="{{ $potentialProfit >= 0 ? 'text-green-600' : 'text-red-600' }} text-sm font-medium">Potential Profit</div>
                    <div class="text-2xl font-bold {{ $potentialProfit >= 0 ? 'text-green-800' : 'text-red-800' }}">${{ number_format($potentialProfit, 2) }}</div>
                    <div class="text-xs {{ $potentialProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">From current inventory</div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Cost Breakdown</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Purchase Value:</span>
                            <span class="font-semibold">${{ number_format($totalPurchaseValue, 2) }}</span>
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
                            <span>Total Current Value:</span>
                            <span>${{ number_format($totalCurrentValue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Profit Analysis</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Expected Sale Value:</span>
                            <span class="font-semibold">${{ number_format($totalExpectedSaleValue, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Current Value:</span>
                            <span class="font-semibold">${{ number_format($totalCurrentValue, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between font-bold {{ $potentialProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            <span>Potential Profit:</span>
                            <span>${{ number_format($potentialProfit, 2) }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Profit margin: {{ $totalCurrentValue > 0 ? number_format(($potentialProfit / $totalCurrentValue) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Inventory Table -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h4 class="text-md font-semibold">Current Inventory ({{ $currentInventory->count() }} cars)</h4>
            </div>
            <div class="kt-card-body p-4">
                <div class="overflow-x-auto">
                    <table class="kt-table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Car Model</th>
                                <th class="text-left">Status</th>
                                <th class="text-right">Purchase Price</th>
                                <th class="text-right">Expected Sale</th>
                                <th class="text-right">Equipment Costs</th>
                                <th class="text-right">Other Costs</th>
                                <th class="text-right">Total Value</th>
                                <th class="text-right">Potential Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currentInventory as $car)
                                @php
                                    $equipmentCosts = $car->equipmentCosts->where('status', 'approved')->sum('amount');
                                    $otherCosts = $car->otherCosts->sum('amount');
                                    $totalValue = $car->purchase_price + $equipmentCosts + $otherCosts;
                                    $potentialProfit = $car->expected_sale_price - $totalValue;
                                @endphp
                                <tr>
                                    <td>{{ $car->model }}</td>
                                    <td>
                                        <span class="kt-badge kt-badge-sm kt-badge-outline kt-badge-{{ $car->status == 'ready' ? 'success' : ($car->status == 'sold' ? 'danger' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $car->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-right">${{ number_format($car->purchase_price, 2) }}</td>
                                    <td class="text-right">${{ number_format($car->expected_sale_price, 2) }}</td>
                                    <td class="text-right">${{ number_format($equipmentCosts, 2) }}</td>
                                    <td class="text-right">${{ number_format($otherCosts, 2) }}</td>
                                    <td class="text-right font-semibold">${{ number_format($totalValue, 2) }}</td>
                                    <td class="text-right font-semibold {{ $potentialProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                        ${{ number_format($potentialProfit, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-gray-500">No cars in current inventory.</td>
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
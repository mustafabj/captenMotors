@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Equipment Cost Summary Report</h3>
        </div>
    </div>
    <div class="kt-card-body p-5">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="kt-card bg-blue-50 border border-blue-200">
                <div class="kt-card-body p-4">
                    <div class="text-blue-600 text-sm font-medium">Total All Costs</div>
                    <div class="text-2xl font-bold text-blue-800">${{ number_format($totalAll, 2) }}</div>
                </div>
            </div>
            <div class="kt-card bg-yellow-50 border border-yellow-200">
                <div class="kt-card-body p-4">
                    <div class="text-yellow-600 text-sm font-medium">Pending</div>
                    <div class="text-2xl font-bold text-yellow-800">${{ number_format($totalPending, 2) }}</div>
                </div>
            </div>
            <div class="kt-card bg-green-50 border border-green-200">
                <div class="kt-card-body p-4">
                    <div class="text-green-600 text-sm font-medium">Approved</div>
                    <div class="text-2xl font-bold text-green-800">${{ number_format($totalApproved, 2) }}</div>
                </div>
            </div>
            <div class="kt-card bg-red-50 border border-red-200">
                <div class="kt-card-body p-4">
                    <div class="text-red-600 text-sm font-medium">Rejected</div>
                    <div class="text-2xl font-bold text-red-800">${{ number_format($totalRejected, 2) }}</div>
                </div>
            </div>
            <div class="kt-card bg-purple-50 border border-purple-200">
                <div class="kt-card-body p-4">
                    <div class="text-purple-600 text-sm font-medium">Transferred</div>
                    <div class="text-2xl font-bold text-purple-800">${{ number_format($totalTransferred, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Status Breakdown</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                Pending
                            </span>
                            <span class="font-semibold">${{ number_format($totalPending, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Approved
                            </span>
                            <span class="font-semibold">${{ number_format($totalApproved, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Rejected
                            </span>
                            <span class="font-semibold">${{ number_format($totalRejected, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center">
                                <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                Transferred
                            </span>
                            <span class="font-semibold">${{ number_format($totalTransferred, 2) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between font-bold">
                            <span>Total:</span>
                            <span>${{ number_format($totalAll, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h4 class="text-md font-semibold">Summary by Car</h4>
                </div>
                <div class="kt-card-body p-4">
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($costsByCar as $carId => $costs)
                            @php
                                $car = $costs->first()->car;
                                $totalCarCost = $costs->sum('amount');
                                $pendingCarCost = $costs->where('status', 'pending')->sum('amount');
                                $approvedCarCost = $costs->where('status', 'approved')->sum('amount');
                            @endphp
                            <div class="border-b border-gray-200 pb-2">
                                <div class="font-medium text-sm">{{ $car->model }}</div>
                                <div class="text-xs text-gray-600 space-x-2">
                                    <span>Total: ${{ number_format($totalCarCost, 2) }}</span>
                                    @if($pendingCarCost > 0)
                                        <span class="text-yellow-600">Pending: ${{ number_format($pendingCarCost, 2) }}</span>
                                    @endif
                                    @if($approvedCarCost > 0)
                                        <span class="text-green-600">Approved: ${{ number_format($approvedCarCost, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">No equipment costs found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Equipment Costs Table -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h4 class="text-md font-semibold">Detailed Equipment Costs ({{ $equipmentCosts->count() }} items)</h4>
            </div>
            <div class="kt-card-body p-4">
                <div class="overflow-x-auto">
                    <table class="kt-table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Car Model</th>
                                <th class="text-left">Description</th>
                                <th class="text-right">Amount</th>
                                <th class="text-left">Status</th>
                                <th class="text-left">Added By</th>
                                <th class="text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipmentCosts as $cost)
                                <tr>
                                    <td>
                                        <a href="{{ route('cars.show', $cost->car) }}" class="text-primary hover:underline">
                                            {{ $cost->car->model }}
                                        </a>
                                    </td>
                                    <td>{{ $cost->description }}</td>
                                    <td class="text-right font-semibold">${{ number_format($cost->amount, 2) }}</td>
                                    <td>
                                        <span class="kt-badge kt-badge-sm kt-badge-outline {{ $cost->getStatusBadgeClass() }}">
                                            {{ $cost->getStatusText() }}
                                        </span>
                                    </td>
                                    <td>{{ $cost->user->name ?? 'N/A' }}</td>
                                    <td>{{ $cost->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">No equipment costs found.</td>
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
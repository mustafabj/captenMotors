@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">         
        <h3 class="text-lg font-semibold">Store Capital Management</h3>
        </div>
        <div class="kt-card-toolbar">
            <a href="{{ route('store-capital.create') }}" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-plus"></i>
                Add Capital Transaction
            </a>
        </div>
    </div>
    <div class="kt-card-body p-4">
        <!-- Current Capital Summary -->
        <div class="mb-6 p-4 gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200 mb-5">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800">Total Capital</h4>
                    <p class="text-sm text-gray-600">Sum of all capital transactions</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-green-700">${{ number_format($total, 2) }}</div>
                    <div class="text-sm text-gray-500">{{ $history->count() }} transaction(s)</div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="kt-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Amount</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Added By</th>
                        <th class="text-left">Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $capital)
                        <tr>
                            <td>
                                <span class="font-semibold @if($capital->amount >0) text-green-700 @elseif($capital->amount <0) text-red-700 @else text-gray-700 @endif">
                                    ${{ number_format($capital->amount, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($capital->description)
                                    {{ $capital->description }}
                                @else
                                    <span class="text-gray-500 italic">No description</span>
                                @endif
                            </td>
                            <td>{{ $capital->creator->name ?? 'N/A' }}</td>
                            <td>{{ $capital->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-center">
                                <div class="kt-flex kt-items-center kt-justify-center kt-gap-2">
                                    <span class="kt-badge kt-badge-sm @if($capital->amount > 0) kt-badge-success @elseif($capital->amount < 0) kt-badge-danger @else kt-badge-secondary @endif">
                                        @if($capital->amount > 0)
                                            <i class="ki-filled ki-arrow-up"></i> Added
                                        @elseif($capital->amount < 0)
                                            <i class="ki-filled ki-arrow-down"></i> Withdrawn
                                        @else
                                            <i class="ki-filled ki-minus"></i> Zero
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center kt-py-8 kt-text-gray-500">
                                <i class="ki-filled ki-information-5 kt-text-4xl kt-mb-4">
                                    <p>No capital transactions found.</p>
                                </i>
                                <a href="{{ route('store-capital.create') }}" class="kt-btn kt-btn-sm kt-btn-primary kt-mt-2">
                                    Add Your First Transaction
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
@extends('layouts.app')
@section('title', 'Description History')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Header -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Description History</h1>
                        <p class="text-gray-600 mt-1">
                            Track changes to equipment cost descriptions.
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('equipment-cost-notifications.index') }}" class="kt-btn kt-btn-secondary">
                            <i class="ki-filled ki-arrow-left"></i>
                            Back to Approvals
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description History -->
        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Description Changes for: {{ $equipmentCost->description }}</h3>
                <div class="text-sm text-gray-500">
                    Car: {{ $equipmentCost->car->model }} ({{ $equipmentCost->car->manufacturing_year }})
                </div>
            </div>
            <div class="kt-card-content p-0">
                @if($descriptionHistories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="kt-table w-full">
                        <thead class="kt-table-head">
                            <tr>
                                <th class="kt-table-cell">Date</th>
                                <th class="kt-table-cell">Changed By</th>
                                <th class="kt-table-cell">Old Description</th>
                                <th class="kt-table-cell">New Description</th>
                                <th class="kt-table-cell">Reason for Change</th>
                            </tr>
                        </thead>
                        <tbody class="kt-table-body">
                            @foreach($descriptionHistories as $history)
                            <tr class="kt-table-row">
                                <td class="kt-table-cell">
                                    <div>
                                        <p class="text-sm text-gray-900">{{ $history->created_at->format('M j, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $history->created_at->format('g:i A') }}</p>
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <span class="text-sm text-gray-900">{{ $history->user_name }}</span>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border">{{ $history->old_description }}</p>
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 bg-green-50 p-2 rounded border border-green-200">{{ $history->new_description }}</p>
                                    </div>
                                </td>
                                <td class="kt-table-cell">
                                    <div class="max-w-xs">
                                        @if($history->change_reason)
                                            <p class="text-sm text-gray-600 bg-blue-50 p-2 rounded border border-blue-200">{{ $history->change_reason }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No reason provided</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-filled ki-document text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Description Changes</h3>
                    <p class="text-gray-500">
                        This equipment cost description has not been modified yet.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
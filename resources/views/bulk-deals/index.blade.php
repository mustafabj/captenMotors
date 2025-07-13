@extends('layouts.app')

@section('content')
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Bulk Deals</h1>
            <p class="text-sm text-gray-500">Home - Bulk Deal Management</p>
        </div>
        <div>
            <a href="{{ route('bulk-deals.create') }}" class="kt-btn kt-btn-primary">
                <i class="fas fa-plus mr-2"></i>Create Bulk Deal
            </a>
        </div>
    </div>

    <!-- Bulk Deals Table -->
    <div class="kt-card">
        <div class="kt-card-content p-0">
            @if($bulkDeals->count() > 0)
                <div class="kt-table-wrapper">
                    <table class="kt-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Total Value</th>
                                <th>Cars</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bulkDeals as $deal)
                                <tr>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">{{ $deal->name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ $deal->description }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-medium text-gray-900">
                                            ${{ number_format($deal->calculated_total_value, 2) }}
                                        </div>
                                        @if($deal->total_value !== null)
                                            <div class="text-xs text-gray-500">
                                                Manual: ${{ number_format($deal->total_value, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $deal->cars_count }} cars
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-blue-100 text-blue-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$deal->status] }}">
                                            {{ ucfirst($deal->status) }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-500">
                                        {{ $deal->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('bulk-deals.show', $deal) }}"
                                               class="inline-flex items-center px-2 py-1 rounded hover:bg-blue-50 text-blue-600"
                                               title="View">
                                                <span class="ki-duotone ki-eye text-blue-500 mr-1" style="font-size: 1.25rem;"></span>
                                            </a>
                                            <a href="{{ route('bulk-deals.edit', $deal) }}"
                                               class="inline-flex items-center px-2 py-1 rounded hover:bg-indigo-50 text-indigo-600"
                                               title="Edit">
                                                <span class="ki-duotone ki-pencil text-indigo-500 mr-1" style="font-size: 1.25rem;color: #6366f1;"></span>
                                            </a>
                                            <form action="{{ route('bulk-deals.destroy', $deal) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this bulk deal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1 rounded hover:bg-red-50 text-red-600"
                                                        title="Delete">
                                                    <span class="ki-duotone ki-trash text-red-500 mr-1" style="font-size: 1.25rem; color: red;"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($bulkDeals->count() > 12)
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $bulkDeals->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-folder-open text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bulk deals found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first bulk deal.</p>
                    <a href="{{ route('bulk-deals.create') }}" class="kt-btn kt-btn-primary">
                        Create Bulk Deal
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection 
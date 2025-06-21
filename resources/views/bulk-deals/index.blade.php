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
        <div class="kt-card-header">
            <h3 class="text-lg font-semibold">All Bulk Deals</h3>
        </div>
        <div class="kt-card-content p-0">
            @if($bulkDeals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cars</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bulkDeals as $deal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $deal->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ Str::limit($deal->description, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            ${{ number_format($deal->calculated_total_value, 2) }}
                                        </div>
                                        @if($deal->total_value !== null)
                                            <div class="text-xs text-gray-500">
                                                Manual: ${{ number_format($deal->total_value, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $deal->cars_count }} cars
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $deal->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('bulk-deals.show', $deal) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('bulk-deals.edit', $deal) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('bulk-deals.destroy', $deal) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this bulk deal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bulkDeals->links() }}
                </div>
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
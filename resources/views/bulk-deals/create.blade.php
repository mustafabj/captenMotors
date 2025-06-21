@extends('layouts.app')

@section('content')
    <form action="{{ route('bulk-deals.store') }}" method="POST">
        @csrf
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Create Bulk Deal</h1>
                <p class="text-sm text-gray-500">Home - Bulk Deal Management - Create</p>
            </div>
            <div>
                <a href="{{ route('bulk-deals.index') }}" class="kt-btn kt-btn-outline">
                    Cancel
                </a>
                <button type="submit" class="kt-btn kt-btn-primary ml-2">
                    Create Bulk Deal
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Basic Information -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold">Basic Information</h3>
                </div>
                <div class="kt-card-content p-6 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Deal Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="kt-input w-full" placeholder="e.g., Luxury Sedan Deal 2024">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" 
                            class="kt-input w-full" placeholder="Describe the bulk deal...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Deal Details -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="text-lg font-semibold">Deal Details</h3>
                </div>
                <div class="kt-card-content p-6 space-y-6">
                    <div>
                        <label for="total_value" class="block text-sm font-medium text-gray-700 mb-2">Total Value (Optional)</label>
                        <input type="number" name="total_value" id="total_value" value="{{ old('total_value') }}" 
                            step="0.01" min="0" class="kt-input w-full" placeholder="0.00">
                        <p class="text-sm text-gray-500 mt-2">Leave empty to automatically calculate from associated cars' purchase prices.</p>
                        @error('total_value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required class="kt-select w-full">
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection 
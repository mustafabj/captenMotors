@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <div class="kt-card">
            <div class="kt-card-header">
                <h2 class="text-lg font-semibold">Add Capital Transaction</h2>
            </div>
            <div class="kt-card-content p-6">
                <form action="{{ route('store-capital.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                        <input type="number" name="amount" id="amount" step="0.01" required class="kt-input w-full" value="{{ old('amount') }}">
                        <div class="text-xs text-gray-500 mt-1">Enter a positive amount to add money, negative to withdraw.</div>
                        @error('amount')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (source or reason)</label>
                        <input type="text" name="description" id="description" class="kt-input w-full" value="{{ old('description') }}">
                        @error('description')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="kt-btn kt-btn-primary">Save Transaction</button>
                        <a href="{{ route('store-capital.index') }}" class="kt-btn kt-btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 
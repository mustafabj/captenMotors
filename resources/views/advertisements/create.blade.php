@extends('layouts.app')

@section('content')
<div class="grid w-full space-y-5">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Advertisement</h1>
            <p class="text-sm text-gray-500">Create a new advertisement for a car</p>
        </div>
        <div>
            <a href="{{ route('advertisements.index') }}" class="kt-btn kt-btn-outline">
                Cancel
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="kt-card">
        <div class="kt-card-header">
            <h3 class="text-lg font-semibold text-gray-900">Advertisement Information</h3>
        </div>
        <div class="kt-card-content p-5">
            <form method="POST" action="{{ route('advertisements.store') }}" class="space-y-5">
                @csrf

                <!-- General Error Messages -->
                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="ki-filled ki-information-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div>
                        <label for="car_id" class="block text-sm font-medium text-gray-700 mb-2">Car *</label>
                        <select name="car_id" id="car_id" class="kt-select w-full @error('car_id') border-red-500 @enderror" data-kt-select="true" data-kt-select-placeholder="Select a car" required>
                            <option value="">Select a car</option>
                            @foreach($availableCars as $car)
                                <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                    {{ $car->model }} - {{ $car->plate_number }} ({{ $car->manufacturing_year }})
                                </option>
                            @endforeach
                        </select>
                        @error('car_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Only cars that are not sold and not already advertised are shown.</p>
                    </div>

                    <div>
                        <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date *</label>
                        <input type="date" name="expiration_date" id="expiration_date" 
                               class="kt-input w-full @error('expiration_date') border-red-500 @enderror" 
                               value="{{ old('expiration_date') }}" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        @error('expiration_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="offer_price" class="block text-sm font-medium text-gray-700 mb-2">Offer Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="offer_price" id="offer_price" 
                                   class="kt-input w-full pl-8 @error('offer_price') border-red-500 @enderror" 
                                   value="{{ old('offer_price') }}" 
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        @error('offer_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">The price you paid for the car.</p>
                    </div>

                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Sale Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="sale_price" id="sale_price" 
                                   class="kt-input w-full pl-8 @error('sale_price') border-red-500 @enderror" 
                                   value="{{ old('sale_price') }}" 
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">The price you want to sell the car for.</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="kt-input w-full @error('description') border-red-500 @enderror" 
                              placeholder="Optional description about the advertisement...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Advertisement Details Preview -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ki-filled ki-information-5 text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Advertisement Details:</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p id="profit-display">Profit: $0.00 (0.0%)</p>
                                <p id="duration-display">Duration: 0 days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
                    <a href="{{ route('advertisements.index') }}" class="kt-btn kt-btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-save"></i>
                        Create Advertisement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const offerPriceInput = document.getElementById('offer_price');
    const salePriceInput = document.getElementById('sale_price');
    const expirationDateInput = document.getElementById('expiration_date');
    const profitDisplay = document.getElementById('profit-display');
    const durationDisplay = document.getElementById('duration-display');

    function updateCalculations() {
        const offerPrice = parseFloat(offerPriceInput.value) || 0;
        const salePrice = parseFloat(salePriceInput.value) || 0;
        const expirationDate = expirationDateInput.value;

        // Calculate profit
        const profit = salePrice - offerPrice;
        const profitPercentage = offerPrice > 0 ? (profit / offerPrice) * 100 : 0;
        
        profitDisplay.textContent = `Profit: $${profit.toFixed(2)} (${profitPercentage.toFixed(1)}%)`;

        // Calculate duration
        if (expirationDate) {
            const today = new Date();
            const expiry = new Date(expirationDate);
            const diffTime = expiry - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            durationDisplay.textContent = `Duration: ${diffDays} days`;
        }
    }

    offerPriceInput.addEventListener('input', updateCalculations);
    salePriceInput.addEventListener('input', updateCalculations);
    expirationDateInput.addEventListener('input', updateCalculations);

    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    expirationDateInput.min = tomorrow.toISOString().split('T')[0];
});
</script>
@endpush
@endsection 
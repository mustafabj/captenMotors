@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        {{-- <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Add Other Cost</h3>
        </div> --}}
        <div class="kt-card-toolbar">
            <a href="{{ route('other-costs.index') }}" class="kt-btn kt-btn-outline">
                <i class="ki-filled ki-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>
    <div class="kt-card-body">
        <form action="{{ route('other-costs.store') }}" method="POST" class="space-y-6 p-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Car Selection -->
                <div>
                    <label for="car_id" class="kt-label">Car *</label>
                    <select name="car_id" id="car_id" class="kt-select w-full @error('car_id') kt-input-error @enderror" required>
                        <option value="">Select a car</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->model }} - {{ $car->plate_number ?: 'No Plate' }}
                            </option>
                        @endforeach
                    </select>
                    @error('car_id')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="kt-label">Category *</label>
                    <select name="category" id="category" class="kt-input w-full @error('category') kt-input-error @enderror" required>
                        <option value="">Select category</option>
                        <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="repair" {{ old('category') == 'repair' ? 'selected' : '' }}>Repair</option>
                        <option value="insurance" {{ old('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="registration" {{ old('category') == 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="fuel" {{ old('category') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="kt-label">Description *</label>
                    <input type="text" name="description" id="description" 
                           value="{{ old('description') }}" 
                           class="kt-input w-full @error('description') kt-input-error @enderror" 
                           placeholder="Enter cost description" required>
                    @error('description')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="kt-label">Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="amount" id="amount" 
                               value="{{ old('amount') }}" 
                               step="0.01" min="0"
                               class="kt-input w-full pl-8 @error('amount') kt-input-error @enderror" 
                               placeholder="0.00" required>
                    </div>
                    @error('amount')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cost Date -->
                <div>
                    <label for="cost_date" class="kt-label">Cost Date *</label>
                    <input type="date" name="cost_date" id="cost_date" 
                           value="{{ old('cost_date') }}" 
                           class="kt-input w-full @error('cost_date') kt-input-error @enderror" required>
                    @error('cost_date')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="kt-label">Notes</label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="kt-input w-full @error('notes') kt-input-error @enderror" 
                              placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="kt-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('other-costs.index') }}" class="kt-btn kt-btn-outline">
                    Cancel
                </a>
                <button type="submit" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-check"></i>
                    Add Cost
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Set default date to today
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('cost_date').value) {
        document.getElementById('cost_date').value = new Date().toISOString().split('T')[0];
    }
});
</script>
@endsection 
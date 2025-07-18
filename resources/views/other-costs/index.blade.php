@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Other Costs</h3>
        </div>
        <div class="kt-card-toolbar">
            <a href="{{ route('other-costs.create') }}" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-plus"></i>
                Add Other Cost
            </a>
        </div>
    </div>
    <div class="kt-card-body">
        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('other-costs.index') }}" method="GET" class="flex gap-4 p-2">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by description or car model..." 
                           class="kt-input w-full">
                </div>
                <div class="w-48">
                    <select name="category" class="kt-input w-full">
                        <option value="">All Categories</option>
                        <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="repair" {{ request('category') == 'repair' ? 'selected' : '' }}>Repair</option>
                        <option value="insurance" {{ request('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="registration" {{ request('category') == 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="fuel" {{ request('category') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <button type="submit" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-search"></i>
                    Search
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('other-costs.index') }}" class="kt-btn kt-btn-outline">
                        <i class="ki-filled ki-cross"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Costs Table -->
        <div class="overflow-x-auto">
            <table class="kt-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Car</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Category</th>
                        <th class="text-right">Amount</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Added By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($otherCosts as $cost)
                        <tr>
                            <td>
                                <a href="{{ route('cars.show', $cost->car) }}" class="text-primary hover:underline">
                                    {{ $cost->car->model }}
                                </a>
                            </td>
                            <td>{{ $cost->description }}</td>
                            <td>
                                <span class="kt-badge kt-badge-sm kt-badge-outline kt-badge-{{ $cost->category == 'maintenance' ? 'primary' : ($cost->category == 'repair' ? 'warning' : ($cost->category == 'insurance' ? 'info' : ($cost->category == 'registration' ? 'success' : ($cost->category == 'fuel' ? 'danger' : 'secondary')))) }}">
                                    {{ ucfirst($cost->category) }}
                                </span>
                            </td>
                            <td class="text-right font-semibold">${{ number_format($cost->amount, 2) }}</td>
                            <td>{{ $cost->cost_date->format('M d, Y') }}</td>
                            <td>{{ $cost->user->name }}</td>
                            <td class="text-center">
                                <div class="kt-flex kt-items-center kt-justify-center kt-gap-2">
                                    <a href="{{ route('other-costs.show', $cost) }}" 
                                       class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" 
                                       title="View">
                                        <i class="ki-filled ki-eye"></i>
                                    </a>
                                    <a href="{{ route('other-costs.edit', $cost) }}" 
                                       class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" 
                                       title="Edit">
                                        <i class="ki-filled ki-pencil"></i>
                                    </a>
                                    <button onclick="deleteCost({{ $cost->id }})" 
                                            class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline kt-btn-danger" 
                                            title="Delete">
                                        <i class="ki-filled ki-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center kt-py-8 kt-text-gray-500">
                                <i class="ki-filled ki-information-5 kt-text-4xl kt-mb-4 kt-block"></i>
                                <p>No other costs found.</p>
                                <a href="{{ route('other-costs.create') }}" class="kt-btn kt-btn-sm kt-btn-primary kt-mt-2">
                                    Add Your First Cost
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($otherCosts->hasPages())
            <div class="kt-mt-6">
                {{ $otherCosts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="kt-modal hidden">
    <div class="kt-modal-dialog">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Confirm Delete</h3>
            <button type="button" class="kt-modal-close" onclick="closeDeleteModal()">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="kt-modal-body">
            <p>Are you sure you want to delete this cost? This action cannot be undone.</p>
        </div>
        <div class="kt-modal-footer">
            <button type="button" class="kt-btn kt-btn-outline" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="kt-btn kt-btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<script>
let costToDelete = null;

function deleteCost(costId) {
    costToDelete = costId;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    costToDelete = null;
}

function confirmDelete() {
    if (!costToDelete) return;
    
    fetch(`/other-costs/${costToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error deleting cost: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting cost');
    })
    .finally(() => {
        closeDeleteModal();
    });
}
</script>
@endsection 
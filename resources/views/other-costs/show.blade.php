@extends('layouts.app')

@section('content')
<div class="kt-card">
    <div class="kt-card-header">
        <div class="kt-card-title">
            <h3 class="text-lg font-semibold">Other Cost Details</h3>
        </div>
        <div class="kt-card-toolbar">
            <div class="flex items-center gap-2">
                <a href="{{ route('other-costs.edit', $otherCost) }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-pencil"></i>
                    Edit
                </a>
                <a href="{{ route('other-costs.index') }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-arrow-left"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
    <div class="kt-card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Cost Information -->
            <div class="space-y-6 p-4">
                <div>
                    <h4 class="text-lg font-semibold mb-4">Cost Information</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="kt-label text-sm text-gray-600">Description</label>
                            <p class="text-lg font-medium">{{ $otherCost->description }}</p>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Category</label>
                            <div class="mt-1">
                                <span class="kt-badge kt-badge-sm kt-badge-outline kt-badge-{{ $otherCost->category == 'maintenance' ? 'primary' : ($otherCost->category == 'repair' ? 'warning' : ($otherCost->category == 'insurance' ? 'info' : ($otherCost->category == 'registration' ? 'success' : ($otherCost->category == 'fuel' ? 'danger' : 'secondary')))) }}">
                                    {{ ucfirst($otherCost->category) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Amount</label>
                            <p class="text-2xl font-bold text-primary">${{ number_format($otherCost->amount, 2) }}</p>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Cost Date</label>
                            <p class="text-lg">{{ $otherCost->cost_date->format('F d, Y') }}</p>
                        </div>
                        
                        @if($otherCost->notes)
                            <div>
                                <label class="kt-label text-sm text-gray-600">Notes</label>
                                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $otherCost->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Information -->
            <div class="space-y-6 p-4">
                <div>
                    <h4 class="text-lg font-semibold mb-4">Related Information</h4>
                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="kt-label text-sm text-gray-600">Car</label>
                            <div class="mt-1">
                                <a href="{{ route('cars.show', $otherCost->car) }}" class="text-primary hover:underline font-medium">
                                    {{ $otherCost->car->model }}
                                </a>
                                @if($otherCost->car->plate_number)
                                    <span class="text-gray-600 ml-2">({{ $otherCost->car->plate_number }})</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Added By</label>
                            <p class="text-lg">{{ $otherCost->user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Created</label>
                            <p class="text-lg">{{ $otherCost->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        
                        <div>
                            <label class="kt-label text-sm text-gray-600">Last Updated</label>
                            <p class="text-lg">{{ $otherCost->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="{{ route('cars.show', $otherCost->car) }}" class="kt-btn kt-btn-outline w-full justify-start">
                            <i class="ki-filled ki-car"></i>
                            View Car Details
                        </a>
                        <a href="{{ route('other-costs.edit', $otherCost) }}" class="kt-btn kt-btn-outline w-full justify-start">
                            <i class="ki-filled ki-pencil"></i>
                            Edit This Cost
                        </a>
                        <button onclick="deleteCost({{ $otherCost->id }})" class="kt-btn kt-btn-outline kt-btn-danger w-full justify-start">
                            <i class="ki-filled ki-trash"></i>
                            Delete This Cost
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
            window.location.href = '{{ route("other-costs.index") }}';
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
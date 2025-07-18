@extends('layouts.app')
@section('title', 'Pending Approvals')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Header -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Pending Approvals</h1>
                        <p class="text-gray-600 mt-1">
                            Review and approve equipment cost requests from team members.
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @php
                            $pendingCount = \App\Models\CarEquipmentCost::where('status', 'pending')->count();
                        @endphp
                        <div class="text-right">
                            <div class="text-2xl font-bold text-orange-600">{{ $pendingCount }}</div>
                            <div class="text-sm text-gray-500">Pending Requests</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <form method="GET" action="{{ route('equipment-cost-notifications.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by description, car model..."
                               class="kt-input w-full">
                    </div>
                    
                    <div class="min-w-[150px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="kt-select w-full">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-magnifier"></i>
                            Search
                        </button>
                        <a href="{{ route('equipment-cost-notifications.index') }}" class="kt-btn kt-btn-secondary">
                            <i class="ki-filled ki-cross"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pending Requests -->
        @php
            $query = \App\Models\CarEquipmentCost::with(['car', 'user'])
                ->when(request('search'), function($q) {
                    $search = request('search');
                    $q->where('description', 'like', '%' . $search . '%')
                      ->orWhereHas('car', function($carQuery) use ($search) {
                          $carQuery->where('model', 'like', '%' . $search . '%');
                      });
                })
                ->when(request('status'), function($q) {
                    $q->where('status', request('status'));
                })
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc');
            
            $equipmentCosts = $query->paginate(20);
        @endphp

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Equipment Cost Requests</h3>
                <div class="flex items-center gap-2">
                    @if($pendingCount > 0)
                    <button type="button" onclick="approveAllPending()" class="kt-btn kt-btn-success kt-btn-sm">
                        <i class="ki-filled ki-check-circle"></i>
                        Approve All Selected
                    </button>
                    @endif
                    {{-- <button type="button" onclick="markAllAsRead()" class="kt-btn kt-btn-secondary kt-btn-sm">
                        <i class="ki-filled ki-check"></i>
                        Mark All Read
                    </button> --}}
                </div>
            </div>
            
            <div class="kt-card-content p-0">
                @if($equipmentCosts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="kt-table kt-table-border">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left">
                                    <input type="checkbox" id="selectAll" class="kt-checkbox">
                                </th>
                                <th class="p-4 text-left font-medium text-gray-900">Request Details</th>
                                <th class="p-4 text-left font-medium text-gray-900">Car</th>
                                <th class="p-4 text-left font-medium text-gray-900">Amount</th>
                                <th class="p-4 text-left font-medium text-gray-900">Status</th>
                                <th class="p-4 text-left font-medium text-gray-900">Date</th>
                                <th class="p-4 text-left font-medium text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipmentCosts as $cost)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $cost->status === 'pending' ? 'bg-yellow-50' : '' }}" 
                                data-cost-id="{{ $cost->id }}">
                                <td class="p-4">
                                    @if($cost->status === 'pending')
                                    <input type="checkbox" class="kt-checkbox cost-checkbox" value="{{ $cost->id }}">
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900">{{ $cost->description }}</p>
                                            <p class="text-sm text-gray-500">
                                                Requested by {{ $cost->user->name }}
                                            </p>
                                            @if($cost->status === 'pending')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="ki-filled ki-time mr-1"></i>
                                                    Awaiting Approval
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $cost->car->model }}</p>
                                        <p class="text-sm text-gray-500">{{ $cost->car->manufacturing_year }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-lg font-bold text-gray-900">
                                        ${{ number_format($cost->amount, 0) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="kt-badge {{ $cost->getStatusBadgeClass() }}">
                                        {{ $cost->getStatusText() }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div>
                                        <p class="text-sm text-gray-900">{{ $cost->cost_date->format('M j, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $cost->created_at->diffForHumans() }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($cost->status === 'pending')
                                    <div class="flex items-center space-x-2">
                                        <button type="button" 
                                                onclick="approveRequest({{ $cost->id }})"
                                                class="kt-btn kt-btn-success kt-btn-sm">
                                            <i class="ki-filled ki-check"></i>
                                            Approve
                                        </button>
                                        <button type="button" 
                                                onclick="showRejectModal({{ $cost->id }})"
                                                class="kt-btn kt-btn-danger kt-btn-sm">
                                            <i class="ki-filled ki-cross"></i>
                                            Reject
                                        </button>
                                        <button type="button" 
                                                onclick="showTransferModal({{ $cost->id }})"
                                                class="kt-btn kt-btn-info kt-btn-sm">
                                            <i class="ki-filled ki-arrow-right"></i>
                                            Transfer
                                        </button>
                                    </div>
                                    @else
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('cars.show', $cost->car) }}" 
                                           class="kt-btn kt-btn-secondary kt-btn-sm">
                                            <i class="ki-filled ki-eye"></i>
                                            View Car
                                        </a>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $equipmentCosts->appends(request()->query())->links() }}
                </div>
                @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ki-filled ki-check-circle text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Equipment Cost Requests</h3>
                    <p class="text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            No requests match your current filters.
                        @else
                            There are no equipment cost requests at the moment.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status']))
                    <div class="mt-4">
                        <a href="{{ route('equipment-cost-notifications.index') }}" class="kt-btn kt-btn-primary">
                            Clear Filters
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="kt-modal" data-kt-modal="true" id="rejectModal">
    <div class="kt-modal-content max-w-[500px] top-[5%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Reject Equipment Cost Request</h3>
            <button type="button" class="kt-modal-close" aria-label="Close modal"
                data-kt-modal-dismiss="#rejectModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="kt-modal-body">
            <form id="rejectForm">
                <input type="hidden" id="rejectCostId">
                <div class="kt-form-item">
                    <label for="rejectReason" class="kt-form-label">
                        Reason for Rejection (Optional)
                    </label>
                    <div class="kt-form-control">
                        <textarea id="rejectReason" 
                                  class="kt-textarea w-full" 
                                  rows="3" 
                                  placeholder="Provide a reason for rejecting this request..."></textarea>
                        <div class="kt-form-message"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="kt-modal-footer">
            <div></div>
            <div class="flex gap-4">
                <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#rejectModal">
                    Cancel
                </button>
                <button type="button" onclick="confirmReject()" class="kt-btn kt-btn-danger">
                    <i class="ki-filled ki-cross"></i>
                    Reject Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="kt-modal" data-kt-modal="true" id="transferModal">
    <div class="kt-modal-content max-w-[500px] top-[5%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Transfer to Other Costs</h3>
            <button type="button" class="kt-modal-close" aria-label="Close modal"
                data-kt-modal-dismiss="#transferModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="kt-modal-body">
            <form id="transferForm">
                <input type="hidden" id="transferCostId">
                <div class="space-y-5">
                    <div class="kt-form-item">
                        <label for="transferCategory" class="kt-form-label">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <div class="kt-form-control">
                            <select id="transferCategory" class="kt-select w-full" required>
                                <option value="">Select Category</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="repair">Repair</option>
                                <option value="insurance">Insurance</option>
                                <option value="registration">Registration</option>
                                <option value="fuel">Fuel</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="kt-form-message"></div>
                        </div>
                    </div>
                    <div class="kt-form-item">
                        <label for="transferReason" class="kt-form-label">
                            Transfer Notes (Optional)
                        </label>
                        <div class="kt-form-control">
                            <textarea id="transferReason" 
                                      class="kt-textarea w-full" 
                                      rows="3" 
                                      placeholder="Add notes about this transfer..."></textarea>
                            <div class="kt-form-message"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="kt-modal-footer">
            <div></div>
            <div class="flex gap-4">
                <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#transferModal">
                    Cancel
                </button>
                <button type="button" onclick="confirmTransfer()" class="kt-btn kt-btn-info">
                    <i class="ki-filled ki-arrow-right"></i>
                    Transfer to Other Costs
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.cost-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

// Approve single request
function approveRequest(costId) {
    if (!confirm('Are you sure you want to approve this equipment cost request?')) {
        return;
    }

    fetch(`/equipment-cost-notifications/equipment-cost/${costId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Equipment cost approved successfully!', 'success');
            location.reload();
        } else {
            showToast('Error approving equipment cost: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while approving the request.', 'error');
    });
}

// Show reject modal
function showRejectModal(costId) {
    document.getElementById('rejectCostId').value = costId;
    document.getElementById('rejectReason').value = '';
    
    // Use KtUI modal methods
    const modal = document.getElementById('rejectModal');
    if (window.KTModal) {
        const modalInstance = KTModal.getInstance(modal);
        if (modalInstance) {
            modalInstance.show();
        }
    }
}

// Confirm reject
function confirmReject() {
    const costId = document.getElementById('rejectCostId').value;
    const reason = document.getElementById('rejectReason').value;

    fetch(`/equipment-cost-notifications/equipment-cost/${costId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Equipment cost rejected successfully!', 'success');
            // Close modal using KtUI
            const modalEl = document.querySelector('#rejectModal');
            const modal = KTModal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
            location.reload();
        } else {
            showToast('Error rejecting equipment cost: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while rejecting the request.', 'error');
    });
}

// Show transfer modal
function showTransferModal(costId) {
    document.getElementById('transferCostId').value = costId;
    document.getElementById('transferCategory').value = '';
    document.getElementById('transferReason').value = '';
    
    // Use KtUI modal methods
    const modal = document.getElementById('transferModal');
    if (window.KTModal) {
        const modalInstance = KTModal.getInstance(modal);
        if (modalInstance) {
            modalInstance.show();
        }
    }
}

// Confirm transfer
function confirmTransfer() {
    const costId = document.getElementById('transferCostId').value;
    const category = document.getElementById('transferCategory').value;
    const reason = document.getElementById('transferReason').value;

    if (!category) {
        showToast('Please select a category for the transfer.', 'error');
        return;
    }

    fetch(`/equipment-cost-notifications/equipment-cost/${costId}/transfer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            category: category,
            transfer_reason: reason 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Equipment cost transferred successfully!', 'success');
            // Close modal using KtUI
            const modalEl = document.querySelector('#transferModal');
            const modal = KTModal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
            location.reload();
        } else {
            showToast('Error transferring equipment cost: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while transferring the request.', 'error');
    });
}

// Approve all pending
function approveAllPending() {
    const selectedCheckboxes = document.querySelectorAll('.cost-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        showToast('Please select at least one request to approve.', 'warning');
        return;
    }

    if (!confirm(`Are you sure you want to approve ${selectedCheckboxes.length} equipment cost request(s)?`)) {
        return;
    }

    const costIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
    
    Promise.all(costIds.map(costId => 
        fetch(`/equipment-cost-notifications/equipment-cost/${costId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
    ))
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(results => {
        const successCount = results.filter(r => r.success).length;
        showToast(`${successCount} equipment cost request(s) approved successfully!`, 'success');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while approving requests.', 'error');
    });
}

// Mark all as read
function markAllAsRead() {
    fetch('/equipment-cost-notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('All notifications marked as read!', 'success');
            location.reload();
        } else {
            showToast('Error marking notifications as read.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while marking notifications as read.', 'error');
    });
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <i class="ki-filled ki-cross text-sm"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endpush 
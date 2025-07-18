@extends('layouts.app')
@section('title', 'Equipment Cost Request Details')

@section('content')
<div class="kt-container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('equipment-cost-notifications.index') }}" class="hover:text-primary">Pending Approvals</a>
            <i class="ki-filled ki-right text-xs"></i>
            <span class="text-gray-900">Request Details</span>
        </div>

        <!-- Request Overview -->
        <div class="kt-card">
            <div class="kt-card-content p-7.5">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="ki-filled ki-wrench text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $notification->carEquipmentCost->description }}</h1>
                            <p class="text-gray-600 mt-1">
                                Equipment cost request for {{ $notification->car->model }}
                            </p>
                            <div class="flex items-center gap-4 mt-3">
                                <span class="kt-badge {{ $notification->carEquipmentCost->getStatusBadgeClass() }} kt-badge-lg">
                                    {{ $notification->carEquipmentCost->getStatusText() }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Requested {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($notification->carEquipmentCost->status === 'pending')
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                onclick="approveRequest({{ $notification->carEquipmentCost->id }})"
                                class="kt-btn kt-btn-success">
                            <i class="ki-filled ki-check"></i>
                            Approve Request
                        </button>
                        <button type="button" 
                                onclick="showRejectModal()"
                                class="kt-btn kt-btn-danger">
                            <i class="ki-filled ki-cross"></i>
                            Reject Request
                        </button>
                        <button type="button" 
                                onclick="showTransferModal()"
                                class="kt-btn kt-btn-info">
                            <i class="ki-filled ki-arrow-right"></i>
                            Transfer to Other Costs
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5">
            <!-- Request Details -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Cost Information -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Cost Information</h3>
                    </div>
                    <div class="kt-card-content p-7.5">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                <div class="text-3xl font-bold text-gray-900">
                                    ${{ number_format($notification->carEquipmentCost->amount, 2) }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Date</label>
                                <div class="text-lg text-gray-900">
                                    {{ $notification->carEquipmentCost->cost_date->format('F j, Y') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-900">{{ $notification->carEquipmentCost->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Car Information -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Car Information</h3>
                        <a href="{{ route('cars.show', $notification->car) }}" class="kt-btn kt-btn-secondary kt-btn-sm">
                            <i class="ki-filled ki-eye"></i>
                            View Car Details
                        </a>
                    </div>
                    <div class="kt-card-content p-7.5">
                        <div class="flex items-start space-x-4">
                            @if($notification->car->getFirstMediaUrl('car_images'))
                            <img src="{{ $notification->car->getFirstMediaUrl('car_images') }}" 
                                 alt="{{ $notification->car->model }}"
                                 class="w-20 h-20 object-cover rounded-lg">
                            @else
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="ki-filled ki-car text-gray-400 text-2xl"></i>
                            </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $notification->car->model }}</h4>
                                <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                                    <div>
                                        <span class="text-gray-500">Year:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $notification->car->manufacturing_year }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Engine:</span>
                                        <span class="text-gray-900 font-medium ml-2">{{ $notification->car->engine_capacity }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Purchase Price:</span>
                                        <span class="text-gray-900 font-medium ml-2">${{ number_format($notification->car->purchase_price, 0) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Expected Sale:</span>
                                        <span class="text-gray-900 font-medium ml-2">${{ number_format($notification->car->expected_sale_price, 0) }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    $statusConfig = [
                                        'not_received' => ['class' => 'kt-badge-warning', 'text' => 'Not Received'],
                                        'paint' => ['class' => 'kt-badge-info', 'text' => 'Paint'],
                                        'upholstery' => ['class' => 'kt-badge-primary', 'text' => 'Upholstery'],
                                        'mechanic' => ['class' => 'kt-badge-warning', 'text' => 'Mechanic'],
                                        'electrical' => ['class' => 'kt-badge-warning', 'text' => 'Electrical'],
                                        'agency' => ['class' => 'kt-badge-info', 'text' => 'Agency'],
                                        'polish' => ['class' => 'kt-badge-primary', 'text' => 'Polish'],
                                        'ready' => ['class' => 'kt-badge-success', 'text' => 'Ready'],
                                        'sold' => ['class' => 'kt-badge-secondary', 'text' => 'Sold'],
                                    ];
                                    $status = $statusConfig[$notification->car->status] ?? [
                                        'class' => 'kt-badge-secondary',
                                        'text' => ucfirst(str_replace('_', ' ', $notification->car->status)),
                                    ];
                                @endphp
                                
                                <div class="mt-3">
                                    <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost History for this Car -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Related Equipment Costs</h3>
                    </div>
                    <div class="kt-card-content p-7.5">
                        @php
                            $relatedCosts = $notification->car->equipmentCosts()
                                ->where('id', '!=', $notification->carEquipmentCost->id)
                                ->with('user')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @if($relatedCosts->count() > 0)
                        <div class="space-y-4">
                            @foreach($relatedCosts as $cost)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $cost->description }}</p>
                                    <p class="text-sm text-gray-500">
                                        By {{ $cost->user->name }} • {{ $cost->cost_date->format('M j, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">${{ number_format($cost->amount, 0) }}</p>
                                    <span class="kt-badge {{ $cost->getStatusBadgeClass() }} kt-badge-sm">
                                        {{ $cost->getStatusText() }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($notification->car->equipmentCosts->count() > 6)
                            <div class="text-center">
                                <a href="{{ route('cars.show', $notification->car) }}" class="text-primary hover:text-primary-dark text-sm font-medium">
                                    View all {{ $notification->car->equipmentCosts->count() }} equipment costs →
                                </a>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-6">
                            <i class="ki-filled ki-information-2 text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500">No other equipment costs for this car</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-5">
                <!-- Requester Information -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Requester Information</h3>
                    </div>
                    <div class="kt-card-content p-7.5">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <i class="ki-filled ki-user text-primary text-xl"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $notification->requestedByUser->name }}</p>
                                <p class="text-sm text-gray-500">{{ $notification->requestedByUser->email }}</p>
                            </div>
                        </div>
                        
                        @php
                            $requesterStats = \App\Models\CarEquipmentCost::where('user_id', $notification->requestedByUser->id)
                                ->selectRaw('
                                    COUNT(*) as total_requests,
                                    SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
                                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                                    SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as approved_amount
                                ')
                                ->first();
                        @endphp
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Requests</span>
                                <span class="font-medium text-gray-900">{{ $requesterStats->total_requests }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Approved</span>
                                <span class="font-medium text-green-600">{{ $requesterStats->approved_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Pending</span>
                                <span class="font-medium text-orange-600">{{ $requesterStats->pending_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Rejected</span>
                                <span class="font-medium text-red-600">{{ $requesterStats->rejected_count }}</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Approved Amount</span>
                                <span class="font-bold text-gray-900">${{ number_format($requesterStats->approved_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Impact -->
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Financial Impact</h3>
                    </div>
                    <div class="kt-card-content p-7.5">
                        @php
                            $carTotalCosts = $notification->car->equipmentCosts->where('status', 'approved')->sum('amount') + 
                                           $notification->car->otherCosts->sum('amount');
                            $carCurrentValue = $notification->car->purchase_price + $carTotalCosts;
                            $potentialProfit = $notification->car->expected_sale_price - $carCurrentValue;
                            $newTotalCosts = $carTotalCosts + $notification->carEquipmentCost->amount;
                            $newCurrentValue = $notification->car->purchase_price + $newTotalCosts;
                            $newPotentialProfit = $notification->car->expected_sale_price - $newCurrentValue;
                        @endphp
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500">Current Car Value</span>
                                    <span class="font-medium text-gray-900">${{ number_format($carCurrentValue, 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500">If Approved</span>
                                    <span class="font-medium text-gray-900">${{ number_format($newCurrentValue, 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Difference</span>
                                    <span class="font-bold text-orange-600">+${{ number_format($notification->carEquipmentCost->amount, 0) }}</span>
                                </div>
                            </div>
                            
                            <hr class="border-gray-200">
                            
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500">Current Potential Profit</span>
                                    <span class="font-medium {{ $potentialProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $potentialProfit >= 0 ? '+' : '' }}${{ number_format($potentialProfit, 0) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">If Approved</span>
                                    <span class="font-medium {{ $newPotentialProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $newPotentialProfit >= 0 ? '+' : '' }}${{ number_format($newPotentialProfit, 0) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($potentialProfit != $newPotentialProfit)
                            <div class="p-3 {{ $newPotentialProfit >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                                <p class="text-xs {{ $newPotentialProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                    Impact: {{ $newPotentialProfit - $potentialProfit >= 0 ? '+' : '' }}${{ number_format($newPotentialProfit - $potentialProfit, 0) }} profit change
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if($notification->carEquipmentCost->status === 'pending')
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Quick Actions</h3>
                    </div>
                    <div class="kt-card-content p-7.5">
                        <div class="space-y-3">
                            <button type="button" 
                                    onclick="approveRequest({{ $notification->carEquipmentCost->id }})"
                                    class="kt-btn kt-btn-success w-full">
                                <i class="ki-filled ki-check"></i>
                                Approve This Request
                            </button>
                            <button type="button" 
                                    onclick="showRejectModal()"
                                    class="kt-btn kt-btn-danger w-full">
                                <i class="ki-filled ki-cross"></i>
                                Reject This Request
                            </button>
                            <button type="button" 
                                    onclick="showTransferModal()"
                                    class="kt-btn kt-btn-info w-full">
                                <i class="ki-filled ki-arrow-right"></i>
                                Transfer to Other Costs
                            </button>
                        </div>
                    </div>
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
// Approve request
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
            setTimeout(() => {
                window.location.href = '{{ route("equipment-cost-notifications.index") }}';
            }, 1500);
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
function showRejectModal() {
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
    const reason = document.getElementById('rejectReason').value;
    const costId = {{ $notification->carEquipmentCost->id }};

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
            setTimeout(() => {
                window.location.href = '{{ route("equipment-cost-notifications.index") }}';
            }, 1500);
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
function showTransferModal() {
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
    const category = document.getElementById('transferCategory').value;
    const reason = document.getElementById('transferReason').value;
    const costId = {{ $notification->carEquipmentCost->id }};

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
            setTimeout(() => {
                window.location.href = '{{ route("equipment-cost-notifications.index") }}';
            }, 1500);
        } else {
            showToast('Error transferring equipment cost: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while transferring the request.', 'error');
    });
}

// Show toast notification
function showToast(message, type = 'info') {
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
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endpush 
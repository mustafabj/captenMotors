<!-- Cars Grid View -->
<div id="grid-view" class="view-container">
    @if ($cars->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($cars as $car)
                <div class="kt-card group hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                    <!-- Card Header with Status -->
                    <div class="kt-card-header p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between w-full">
                            <!-- Actions Dropdown -->
                            <div data-kt-dropdown="true" data-kt-dropdown-trigger="click">
                                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline"
                                    data-kt-dropdown-toggle="true">
                                    <i class="ki-filled ki-dots-square"></i>
                                </button>
                                <div class="kt-dropdown-menu w-52" data-kt-dropdown-menu="true">
                                    <ul class="kt-dropdown-menu-sub">
                                        <li>
                                            <a href="{{ route('cars.show', $car) }}" class="kt-dropdown-menu-link">
                                                <i class="ki-filled ki-search-list"></i>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="kt-dropdown-menu-link w-full text-left"
                                                    data-kt-modal-toggle="#addEquipmentModal"
                                                    onclick="openAddEquipmentModal({{ $car->id }}, '{{ $car->model }}')">
                                                <i class="ki-filled ki-plus"></i>
                                                Add Equipment
                                            </button>
                                        </li>
                                        @if(auth()->user()->hasRole('admin'))
                                        <li>
                                            <form action="{{ route('cars.destroy', $car) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this car?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="kt-dropdown-menu-link w-full text-left text-red-600">
                                                    <i class="ki-filled ki-trash"></i>
                                                    Remove
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('insurance.test-notification', $car) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="kt-dropdown-menu-link w-full text-left">
                                                    <i class="ki-filled ki-shield-tick"></i>
                                                    Test Insurance Notification
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
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
                                ];
                                $status = $statusConfig[$car->status] ?? [
                                    'class' => 'kt-badge-secondary',
                                    'text' => ucfirst(str_replace('_', ' ', $car->status)),
                                ];
                            @endphp
                            <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="kt-card-body p-4">
                        <!-- Car Image Placeholder -->
                        @if ($car->getFirstMedia('car_images'))
                            <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                <img alt="Car Image" class="h-full w-full object-cover rounded"
                                    src="{{ $car->getFirstMedia('car_images')->getUrl() }}">
                            </div>
                        @else
                            <div class="w-full h-48 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                                <i class="ki-filled ki-car text-4xl text-gray-400"></i>
                            </div>
                        @endif

                        <!-- Car Details -->
                        <div class="space-y-3">
                            <!-- Model and Year -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $car->model }}</h3>
                                <p class="text-sm text-gray-500">{{ $car->manufacturing_year }} •
                                    {{ $car->engine_capacity }}
                                    @if($car->color)
                                        • {{ $car->color }}
                                    @endif
                                    @if($car->mileage)
                                        • {{ number_format($car->mileage) }} km
                                    @endif
                                </p>
                            </div>

                            <!-- Chassis and Plate -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-tag text-gray-400"></i>
                                    <span class="text-sm">{{ $car->plate_number ?? '—' }}</span>
                                </div>
                            </div>

                            <!-- Keys and Price -->
                            <div class="flex items-center justify-between pt-2 border-t  border-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-key text-gray-400"></i>
                                    <span class="text-sm">{{ $car->number_of_keys ?? 0 }} keys</span>
                                </div>
                                @if(auth()->user()->hasRole('admin'))
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">
                                        ${{ number_format($car->expected_sale_price, 2) }}</div>
                                </div>
                                @else
                                <div class="text-right">
                                    <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        {{-- @if(!$car->isSold())
                            <button class="kt-btn kt-btn-sm kt-btn-success" onclick="openSellModal({{ $car->id }}, '{{ $car->model }}')">
                                <i class="ki-filled ki-dollar"></i> Mark as Sold
                            </button>
                        @endif --}}
                    </div>

                    <!-- Card Footer -->
                    <div class="kt-card-footer p-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex flex-col justify-start items-start gap-[11px] text-sm">
                            <div class="flex items-center gap-2">
                                <i class="ki-filled ki-calendar text-gray-400"></i>
                                <span class="text-gray-600">Purchased:
                                    {{ $car->purchase_date->format('M j, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $insuranceStatus = $car->getInsuranceStatus();
                                @endphp
                                <i class="ki-filled ki-shield-tick {{ $insuranceStatus['status'] === 'valid' ? 'text-green-500' : ($insuranceStatus['status'] === 'not_set' ? 'text-gray-400' : 'text-red-500') }}"></i>
                                <span class="text-gray-600">Insurance:
                                    @if($car->insurance_expiry_date)
                                        {{ $car->insurance_expiry_date->format('M j, Y') }}
                                        @if($insuranceStatus['status'] !== 'valid' && $insuranceStatus['status'] !== 'not_set')
                                            <span class="kt-badge {{ $insuranceStatus['class'] }} ml-1">
                                                {{ $insuranceStatus['text'] }}
                                                @if($insuranceStatus['days'])
                                                    ({{ number_format($insuranceStatus['days'])}} days)
                                                @endif
                                            </span>
                                        @endif
                                    @else
                                        Not set
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="kt-card">
            <div class="kt-card-body">
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No cars found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search terms or filters.</p>
                    <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add New Car
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Cars List View -->
<div id="list-view" class="view-container hidden">
    @if ($cars->count() > 0)
        <div class="grid grid-cols-1 gap-5">
            @foreach ($cars as $car)
                <div class="kt-card">
                    <div
                        class="kt-card-content flex flex-col sm:flex-row items-center flex-wrap justify-between p-2 pe-5 gap-4.5">
                        <!-- Image -->
                        <div
                            class="kt-card flex items-center justify-center bg-accent/50 h-[70px] w-[90px] shadow-none">
                            @if ($car->getFirstMedia('car_images'))
                                <img alt="img" class="h-[70px] w-[90px] object-cover rounded"
                                    src="{{ $car->getFirstMedia('car_images')->getUrl() }}">
                            @else
                                <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                            @endif
                        </div>
                        <!-- Details -->
                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 -mt-1">
                                <a class="hover:text-primary text-sm font-medium text-mono leading-5.5 truncate"
                                    href="{{ route('cars.show', $car) }}">
                                    {{ $car->model }}
                                </a>
                            </div>
                            <div class="flex items-center flex-wrap gap-3">
                                @php
                                    $statusConfig = [
                                        'not_received' => [
                                            'class' => 'kt-badge-warning',
                                            'text' => 'Not Received',
                                        ],
                                        'paint' => ['class' => 'kt-badge-info', 'text' => 'Paint'],
                                        'upholstery' => ['class' => 'kt-badge-primary', 'text' => 'Upholstery'],
                                        'mechanic' => ['class' => 'kt-badge-warning', 'text' => 'Mechanic'],
                                        'electrical' => ['class' => 'kt-badge-warning', 'text' => 'Electrical'],
                                        'agency' => ['class' => 'kt-badge-info', 'text' => 'Agency'],
                                        'polish' => ['class' => 'kt-badge-primary', 'text' => 'Polish'],
                                        'ready' => ['class' => 'kt-badge-success', 'text' => 'Ready'],
                                    ];
                                    $status = $statusConfig[$car->status] ?? [
                                        'class' => 'kt-badge-secondary',
                                        'text' => ucfirst(str_replace('_', ' ', $car->status)),
                                    ];
                                @endphp
                                <span class="kt-badge {{ $status['class'] }}">{{ $status['text'] }}</span>

                                <div class="flex items-center flex-wrap gap-2 lg:gap-4">
                                    @if ($car->plate_number)
                                        <span class="text-xs font-normal text-secondary-foreground">
                                            Plate:
                                            <span class="text-xs font-medium text-foreground">
                                                {{ $car->plate_number }}
                                            </span>
                                        </span>
                                    @endif
                                    <span class="text-xs font-normal text-secondary-foreground">
                                        Year:
                                        <span class="text-xs font-medium text-foreground">
                                            {{ $car->manufacturing_year }}
                                        </span>
                                    </span>
                                    @if ($car->color)
                                        <span class="text-xs font-normal text-secondary-foreground">
                                            Color:
                                            <span class="text-xs font-medium text-foreground">
                                                {{ $car->color }}
                                            </span>
                                        </span>
                                    @endif
                                    @if ($car->mileage)
                                        <span class="text-xs font-normal text-secondary-foreground">
                                            Mileage:
                                            <span class="text-xs font-medium text-foreground">
                                                {{ number_format($car->mileage) }} km
                                            </span>
                                        </span>
                                    @endif
                                    <span class="text-xs font-normal text-secondary-foreground">
                                        Keys:
                                        <span class="text-xs font-medium text-foreground">
                                            {{ $car->number_of_keys ?? 0 }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Price & Action -->
                        <div class="flex flex-col items-end gap-2 min-w-[120px]">
                            <span class="text-sm font-medium text-mono">
                                ${{ number_format($car->expected_sale_price, 2) }}
                            </span>
                            <!-- Actions Dropdown -->
                            <div data-kt-dropdown="true" data-kt-dropdown-trigger="click"
                                data-kt-dropdown-placement="left-start">
                                <button class="kt-btn kt-btn-sm kt-btn-outline" data-kt-dropdown-toggle="true">
                                    Actions
                                </button>
                                <div class="kt-dropdown-menu w-52" data-kt-dropdown-menu="true">
                                    <ul class="kt-dropdown-menu-sub">
                                        <li>
                                            <a href="{{ route('cars.show', $car) }}"
                                                class="kt-dropdown-menu-link">
                                                <i class="ki-filled ki-search-list"></i>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('cars.destroy', $car) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this car?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="kt-dropdown-menu-link w-full text-left text-red-600">
                                                    <i class="ki-filled ki-trash"></i>
                                                    Remove
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            {{-- @if($car->isSold())
                                <button class="kt-btn kt-btn-sm kt-btn-success" onclick="openSellModal({{ $car->id }}, '{{ $car->model }}')">
                                    <i class="ki-filled ki-dollar"></i> Mark as Sold
                                </button>
                            @endif --}}
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State for List View -->
        <div class="kt-card">
            <div class="kt-card-body">
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ki-filled ki-car text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No cars found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search terms or filters.</p>
                    <a href="{{ route('cars.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add New Car
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
<div id="sellCarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="closeSellModal()">&times;</button>
        <h3 class="text-lg font-bold mb-4">Sell Car: <span id="sellCarModel"></span></h3>
        <form id="sellCarForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="car_id" id="sellCarId">
            <div class="mb-3">
                <label for="sale_price" class="kt-label">Sale Price *</label>
                <input type="number" name="sale_price" id="sale_price" class="kt-input w-full" required min="0" step="0.01">
            </div>
            <div class="mb-3">
                <label for="payment_method" class="kt-label">Payment Method *</label>
                <select name="payment_method" id="payment_method" class="kt-select w-full" required onchange="togglePaymentFields()">
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                    <option value="separated">Separated</option>
                </select>
            </div>
            <div class="mb-3 hidden" id="separatedFields">
                <label for="paid_amount" class="kt-label">Paid Amount</label>
                <input type="number" name="paid_amount" id="paid_amount" class="kt-input w-full" min="0" step="0.01">
                <label for="remaining_amount" class="kt-label mt-2">Remaining Amount</label>
                <input type="number" name="remaining_amount" id="remaining_amount" class="kt-input w-full" min="0" step="0.01">
            </div>
            <div class="mb-3">
                <label for="attachment" class="kt-label">Attachment</label>
                <input type="file" name="attachment" id="attachment" class="kt-input w-full">
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="kt-btn kt-btn-outline" onclick="closeSellModal()">Cancel</button>
                <button type="submit" class="kt-btn kt-btn-primary">Confirm Sale</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Equipment Cost Modal -->
<div class="kt-modal" data-kt-modal="true" id="addEquipmentModal">
    <div class="kt-modal-content max-w-[500px] top-[5%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Add Equipment Cost - <span id="equipmentCarModel"></span></h3>
            <button type="button" class="kt-modal-close" aria-label="Close modal"
                data-kt-modal-dismiss="#addEquipmentModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="kt-modal-body">
            <form method="POST" id="addEquipmentForm">
                @csrf
                <div class="space-y-5">
                    <!-- Date Field -->
                    <div class="kt-form-item">
                        <label for="equipment_cost_date" class="kt-form-label">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <div class="kt-form-control">
                            <input type="date" name="cost_date" id="equipment_cost_date" required class="kt-input w-full">
                            <div class="kt-form-message"></div>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="kt-form-item">
                        <label for="equipment_description" class="kt-form-label">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <div class="kt-form-control">
                            <textarea name="description" id="equipment_description" rows="4" required class="kt-textarea w-full"
                                placeholder="e.g., Oil change, Tire replacement"></textarea>
                            <div class="kt-form-message"></div>
                        </div>
                    </div>

                    <!-- Amount Field -->
                    <div class="kt-form-item">
                        <label for="equipment_cost_amount" class="kt-form-label">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="kt-form-control">
                            <input type="number" name="amount" id="equipment_cost_amount" required min="0"
                                step="0.01" class="kt-input w-full pl-8" placeholder="0.00">
                            <div class="kt-form-message"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="kt-modal-footer">
            <div></div>
            <div class="flex gap-4">
                <button class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="#addEquipmentModal">
                    Cancel
                </button>
                <button type="submit" class="kt-btn kt-btn-primary" form="addEquipmentForm" id="submitEquipmentBtn">
                    <span class="submit-text">Add Cost</span>
                    <span class="loading-text hidden">
                        <i class="ki-duotone ki-spinner fs-2 rotate"></i>
                        Adding...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openSellModal(carId, carModel) {
    document.getElementById('sellCarId').value = carId;
    document.getElementById('sellCarModel').textContent = carModel;
    document.getElementById('sellCarForm').action = '/cars/' + carId + '/sell';
    document.getElementById('sellCarModal').classList.remove('hidden');
}
function closeSellModal() {
    document.getElementById('sellCarModal').classList.add('hidden');
}
function togglePaymentFields() {
    var method = document.getElementById('payment_method').value;
    document.getElementById('separatedFields').classList.toggle('hidden', method !== 'separated');
}

// Add Equipment Modal Functions
function openAddEquipmentModal(carId, carModel) {
    document.getElementById('equipmentCarModel').textContent = carModel;
    document.getElementById('addEquipmentForm').action = '/cars/' + carId + '/equipment-costs';
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('equipment_cost_date').value = today;
    
    // Clear form
    document.getElementById('addEquipmentForm').reset();
    document.getElementById('equipment_cost_date').value = today;
    
    // Clear any previous errors
    clearEquipmentFormErrors();
    
    // Use KtUI modal methods
    const modal = document.getElementById('addEquipmentModal');
    if (window.KTModal) {
        const modalInstance = KTModal.getInstance(modal);
        if (modalInstance) {
            modalInstance.show();
        }
    }
}

// Handle equipment cost form submission
document.addEventListener('DOMContentLoaded', function() {
    const equipmentForm = document.getElementById('addEquipmentForm');
    if (equipmentForm) {
        equipmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitEquipmentBtn');
            const submitText = submitBtn.querySelector('.submit-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            
            // Clear previous errors
            clearEquipmentFormErrors();
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loadingText.classList.remove('hidden');
            
            // Prepare form data
            const formData = new FormData(equipmentForm);
            
            // Submit via AJAX
            fetch(equipmentForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success - show toast and close modal
                    showToast('Equipment cost added successfully!', 'success');
                    
                    // Close modal
                    const modalEl = document.querySelector('#addEquipmentModal');
                    const modal = KTModal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Reset form
                    equipmentForm.reset();
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('equipment_cost_date').value = today;
                } else {
                    // Show validation errors
                    showEquipmentFormErrors(data.errors);
                    showToast('Please correct the errors below.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the cost.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                loadingText.classList.add('hidden');
            });
        });
    }
});

// Clear equipment form errors
function clearEquipmentFormErrors() {
    const form = document.getElementById('addEquipmentForm');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea');
    const messages = form.querySelectorAll('.kt-form-message');
    
    inputs.forEach(input => {
        input.removeAttribute('aria-invalid');
        input.classList.remove('is-invalid');
    });
    
    messages.forEach(message => {
        message.innerHTML = '';
        message.style.display = 'none';
    });
}

// Show equipment form errors
function showEquipmentFormErrors(errors) {
    if (!errors || typeof errors !== 'object') {
        console.warn('No errors object provided to showEquipmentFormErrors');
        return;
    }
    
    const form = document.getElementById('addEquipmentForm');
    if (!form) {
        console.warn('Equipment form not found');
        return;
    }
    
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        const messageDiv = field ? field.closest('.kt-form-control')?.querySelector('.kt-form-message') : null;
        
        if (field && messageDiv) {
            // Add error styling to field
            field.setAttribute('aria-invalid', 'true');
            field.classList.add('is-invalid');
            
            // Show error message
            messageDiv.innerHTML = `<div class="text-danger">${errors[fieldName][0]}</div>`;
            messageDiv.style.display = 'block';
        }
    });
}

// Simple toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500');
            break;
        case 'error':
            toast.classList.add('bg-red-500');
            break;
        default:
            toast.classList.add('bg-blue-500');
    }
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script> 
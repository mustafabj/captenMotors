/**
 * Car Show Page JavaScript
 * Handles inline editing functionality for car details
 */

App.pages.carsShow = {
    // Configuration
    config: {
        updateUrl: null,
        csrfToken: null,
        carId: null,
        isEditMode: false,
        originalValues: {},
        // URLs for different sections
        optionsUpdateUrl: null,
        inspectionUpdateUrl: null,
        financialUpdateUrl: null,
        imagesUpdateUrl: null
    },

    // Initialize the car show functionality
    init: function(carId, updateUrl) {
        this.config.carId = carId;
        this.config.updateUrl = updateUrl;
        this.config.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Set up URLs for different sections
        this.config.optionsUpdateUrl = `/cars/${carId}/update-options`;
        this.config.inspectionUpdateUrl = `/cars/${carId}/update-inspection`;
        this.config.financialUpdateUrl = `/cars/${carId}/update-financial`;
        this.config.imagesUpdateUrl = `/cars/${carId}/update-images`;
        
        this.storeOriginalValues();
        this.bindEvents();
    },

    // Store original values for reset functionality
    storeOriginalValues: function() {
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            const fieldName = field.getAttribute('name');
            const originalValue = field.getAttribute('data-original');
            this.config.originalValues[fieldName] = originalValue;
        });
    },

    // Bind event listeners
    bindEvents: function() {
        // Edit button click
        const editBtn = document.getElementById('edit-btn');
        if (editBtn) {
            editBtn.addEventListener('click', () => this.enableEditMode());
        }

        // Save button click
        const saveBtn = document.getElementById('save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveChanges());
        }

        // Cancel button click
        const cancelBtn = document.getElementById('cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelEdit());
        }

        // Listen for Escape key to cancel edit
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.config.isEditMode) {
                this.cancelEdit();
            }
        });
    },

    // Enable edit mode
    enableEditMode: function() {
        this.config.isEditMode = true;
        
        // Hide view actions, show edit actions
        document.getElementById('view-actions').classList.add('hidden');
        document.getElementById('edit-actions').classList.remove('hidden');
        
        // Show edit mode indicator
        this.showEditModeIndicator();
        
        // Make all editable fields editable
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.removeAttribute('readonly');
            field.classList.add('edit-mode');
            
            // Show select fields, hide status display
            if (field.name === 'status') {
                field.classList.remove('hidden');
                document.querySelector('.status-display').classList.add('hidden');
            }
        });
        
        // Enable edit mode for all tabs
        this.enableAllTabEditModes();
        

    },

    // Enable edit mode for all tabs
    enableAllTabEditModes: function() {
        // Options tab
        const optionsViewMode = document.getElementById('options-view-mode');
        const optionsEditMode = document.getElementById('options-edit-mode');
        if (optionsViewMode && optionsEditMode) {
            optionsViewMode.classList.add('hidden');
            optionsEditMode.classList.remove('hidden');
        }

        // Inspection tab
        const inspectionViewMode = document.getElementById('inspection-view-mode');
        const inspectionEditMode = document.getElementById('inspection-edit-mode');
        if (inspectionViewMode && inspectionEditMode) {
            inspectionViewMode.classList.add('hidden');
            inspectionEditMode.classList.remove('hidden');
        }

        // Financial tab
        const financialViewMode = document.getElementById('financial-view-mode');
        const financialEditMode = document.getElementById('financial-edit-mode');
        if (financialViewMode && financialEditMode) {
            financialViewMode.classList.add('hidden');
            financialEditMode.classList.remove('hidden');
        }

        // Images tab
        const imagesViewMode = document.getElementById('images-view-mode');
        const imagesEditMode = document.getElementById('images-edit-mode');
        if (imagesViewMode && imagesEditMode) {
            imagesViewMode.classList.add('hidden');
            imagesEditMode.classList.remove('hidden');
        }
    },

    // Disable edit mode for all tabs
    disableAllTabEditModes: function() {
        // Options tab
        const optionsViewMode = document.getElementById('options-view-mode');
        const optionsEditMode = document.getElementById('options-edit-mode');
        if (optionsViewMode && optionsEditMode) {
            optionsViewMode.classList.remove('hidden');
            optionsEditMode.classList.add('hidden');
        }

        // Inspection tab
        const inspectionViewMode = document.getElementById('inspection-view-mode');
        const inspectionEditMode = document.getElementById('inspection-edit-mode');
        if (inspectionViewMode && inspectionEditMode) {
            inspectionViewMode.classList.remove('hidden');
            inspectionEditMode.classList.add('hidden');
        }

        // Financial tab
        const financialViewMode = document.getElementById('financial-view-mode');
        const financialEditMode = document.getElementById('financial-edit-mode');
        if (financialViewMode && financialEditMode) {
            financialViewMode.classList.remove('hidden');
            financialEditMode.classList.add('hidden');
        }

        // Images tab
        const imagesViewMode = document.getElementById('images-view-mode');
        const imagesEditMode = document.getElementById('images-edit-mode');
        if (imagesViewMode && imagesEditMode) {
            imagesViewMode.classList.remove('hidden');
            imagesEditMode.classList.add('hidden');
        }
    },

    // Save changes
    saveChanges: function() {
        const saveBtn = document.getElementById('save-btn');
        const originalText = saveBtn.innerHTML;
        
        // Show loading state
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="ki-filled ki-loading animate-spin"></i> Saving...';
        
        // Save all sections
        Promise.all([
            this.saveMainDetails(),
            this.saveOptions(),
            this.saveInspection(),
            this.saveFinancial(),
            this.saveImages()
        ])
        .then(results => {
            const allSuccess = results.every(result => result.success);
            if (allSuccess) {
                this.showNotification('All changes saved successfully!', 'success');
                this.disableEditMode();
            } else {
                this.showNotification('Some changes could not be saved. Please check the errors.', 'error');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            this.showNotification('An error occurred while saving. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    },

    // Save main details
    saveMainDetails: function() {
        const formData = new FormData();
        const editableFields = document.querySelectorAll('.editable-field');
        
        editableFields.forEach(field => {
            const fieldName = field.getAttribute('name');
                    const value = field.value;
        formData.append(fieldName, value);
    });

    return fetch(this.config.updateUrl, {
        method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json());
    },

    // Handle successful save
    handleSaveSuccess: function(data) {
        // Update original values
        Object.keys(data.car).forEach(key => {
            this.config.originalValues[key] = data.car[key];
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                field.setAttribute('data-original', data.car[key]);
            }
        });
        
        // Update status display if status changed
        if (data.car.status) {
            this.updateStatusDisplay(data.car.status);
        }
        
        // Exit edit mode
        this.disableEditMode();
        
        // Show success message
        this.showNotification(data.message, 'success');
        

    },

    // Handle save error
    handleSaveError: function(data) {
        if (data.errors) {
            // Show field-specific errors
            Object.keys(data.errors).forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.add('border-red-500');
                    // Remove error styling after 3 seconds
                    setTimeout(() => {
                        field.classList.remove('border-red-500');
                    }, 3000);
                }
            });
        }
        
        this.showNotification(data.message || 'Please correct the errors and try again.', 'error');
    },

    // Cancel edit mode
    cancelEdit: function() {
        // Reset all fields to original values
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            const fieldName = field.getAttribute('name');
            const originalValue = this.config.originalValues[fieldName];
            field.value = originalValue;
        });
        
        // Exit edit mode
        this.disableEditMode();
        

    },

    // Disable edit mode
    disableEditMode: function() {
        this.config.isEditMode = false;
        
        // Show view actions, hide edit actions
        document.getElementById('view-actions').classList.remove('hidden');
        document.getElementById('edit-actions').classList.add('hidden');
        
        // Hide edit mode indicator
        this.hideEditModeIndicator();
        
        // Make all editable fields readonly
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.setAttribute('readonly', 'readonly');
            field.classList.remove('edit-mode', 'border-red-500');
            
            // Hide select fields, show status display
            if (field.name === 'status') {
                field.classList.add('hidden');
                document.querySelector('.status-display').classList.remove('hidden');
            }
        });
        
        // Disable edit mode for all tabs
        this.disableAllTabEditModes();
    },

    // Update status display
    updateStatusDisplay: function(status) {
        const statusConfig = {
            'not_received': { class: 'kt-badge-warning', text: 'Not Received' },
            'paint': { class: 'kt-badge-info', text: 'Paint' },
            'upholstery': { class: 'kt-badge-primary', text: 'Upholstery' },
            'mechanic': { class: 'kt-badge-warning', text: 'Mechanic' },
            'electrical': { class: 'kt-badge-warning', text: 'Electrical' },
            'agency': { class: 'kt-badge-info', text: 'Agency' },
            'polish': { class: 'kt-badge-primary', text: 'Polish' },
            'ready': { class: 'kt-badge-success', text: 'Ready' }
        };
        
        const statusBadge = document.querySelector('.status-display .kt-badge');
        const config = statusConfig[status] || { class: 'kt-badge-secondary', text: 'Unknown' };
        
        if (statusBadge) {
            statusBadge.className = `kt-badge ${config.class}`;
            statusBadge.textContent = config.text;
        }
    },

    // Show edit mode indicator
    showEditModeIndicator: function() {
        const indicator = document.createElement('div');
        indicator.id = 'edit-mode-indicator';
        indicator.className = 'edit-mode-indicator';
        indicator.innerHTML = `
            <div class="flex items-center">
                <i class="ki-filled ki-pencil mr-2"></i>
                <span>Edit Mode - Press ESC to cancel</span>
            </div>
        `;
        document.body.appendChild(indicator);
    },

    // Hide edit mode indicator
    hideEditModeIndicator: function() {
        const indicator = document.getElementById('edit-mode-indicator');
        if (indicator) {
            indicator.remove();
        }
    },

    // Show notification
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        const bgClass = type === 'error' ? 'bg-red-500' : 
                       type === 'success' ? 'bg-green-500' : 
                       'bg-blue-500';
        
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${bgClass} text-white max-w-md`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="ki-filled ki-${type === 'error' ? 'cross-circle' : type === 'success' ? 'check-circle' : 'information-5'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    },

    // ===== OPTIONS EDITING =====
    
    // Enable options edit mode
    enableOptionsEdit: function() {
        document.getElementById('options-view-mode').classList.add('hidden');
        document.getElementById('options-edit-mode').classList.remove('hidden');
        document.querySelector('.options-edit-btn').classList.add('hidden');
    },

    // Save options
    saveOptions: function() {
        const options = [];
        document.querySelectorAll('.option-input').forEach(input => {
            const value = input.value.trim();
            if (value) {
                options.push(value);
            }
        });

        const formData = new FormData();
        formData.append('options', JSON.stringify(options));

        return fetch(this.config.optionsUpdateUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateOptionsView(data.options);
            }
            return data;
        });
    },

    // Cancel options edit
    cancelOptionsEdit: function() {
        document.getElementById('options-view-mode').classList.remove('hidden');
        document.getElementById('options-edit-mode').classList.add('hidden');
        document.querySelector('.options-edit-btn').classList.remove('hidden');
    },

    // Update options view
    updateOptionsView: function(options) {
        const viewMode = document.getElementById('options-view-mode');
        
        if (options.length > 0) {
            const optionsHtml = options.map(option => `
                <div class="kt-card bg-green-50 border border-green-200">
                    <div class="kt-card-body p-4">
                        <div class="flex items-center">
                            <i class="ki-filled ki-check-circle text-green-500 text-xl me-3"></i>
                            <span class="text-gray-800 font-semibold">${option}</span>
                        </div>
                    </div>
                </div>
            `).join('');
            
            viewMode.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">${optionsHtml}</div>`;
        } else {
            viewMode.innerHTML = `
                <div class="text-center py-12">
                    <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">No options</h4>
                    <p class="text-gray-600">No options have been added to this car yet.</p>
                </div>
            `;
        }
    },

    // ===== INSPECTION EDITING =====
    
    // Enable inspection edit mode
    enableInspectionEdit: function() {
        document.getElementById('inspection-view-mode').classList.add('hidden');
        document.getElementById('inspection-edit-mode').classList.remove('hidden');
        document.querySelector('.inspection-edit-btn').classList.add('hidden');
    },

    // Save inspection
    saveInspection: function() {
        const formData = new FormData();
        document.querySelectorAll('.inspection-field').forEach(field => {
            formData.append(field.name, field.value);
        });

        return fetch(this.config.inspectionUpdateUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateInspectionView(data.inspection);
            }
            return data;
        });
    },

    // Cancel inspection edit
    cancelInspectionEdit: function() {
        document.getElementById('inspection-view-mode').classList.remove('hidden');
        document.getElementById('inspection-edit-mode').classList.add('hidden');
        document.querySelector('.inspection-edit-btn').classList.remove('hidden');
    },

    // Update inspection view
    updateInspectionView: function(inspection) {
        const viewMode = document.getElementById('inspection-view-mode');
        
        if (inspection) {
            const chassisHtml = inspection.chassis_inspection ? 
                `<div class="py-2">
                    <span class="text-gray-600 block mb-2">Chassis Inspection</span>
                    <div class="bg-gray-50 p-3 rounded border">${inspection.chassis_inspection}</div>
                </div>` : 
                `<div class="py-2 text-gray-500 italic">No chassis inspection data available</div>`;

            const bodyNotesHtml = inspection.body_notes ? 
                `<div class="mt-6">
                    <h6 class="text-sm font-semibold mb-2">Body Notes</h6>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded">${inspection.body_notes}</p>
                </div>` : '';

            viewMode.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h5 class="text-md font-semibold mb-4">Chassis Inspection</h5>
                        <div class="space-y-3">${chassisHtml}</div>
                    </div>
                    <div>
                        <h5 class="text-md font-semibold mb-4">Mechanical Inspection</h5>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-gray-600">Transmission Condition</span>
                                <span class="text-gray-800">${inspection.transmission || 'Not specified'}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="text-gray-600">Motor Condition</span>
                                <span class="text-gray-800">${inspection.motor || 'Not specified'}</span>
                            </div>
                        </div>${bodyNotesHtml}
                    </div>
                </div>
            `;
        } else {
            viewMode.innerHTML = `
                <div class="text-center py-12">
                    <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">No inspection data</h4>
                    <p class="text-gray-600">No inspection information has been recorded for this car.</p>
                </div>
            `;
        }
    },

    // ===== FINANCIAL EDITING =====
    
    // Enable financial edit mode
    enableFinancialEdit: function() {
        document.getElementById('financial-view-mode').classList.add('hidden');
        document.getElementById('financial-edit-mode').classList.remove('hidden');
        document.querySelector('.financial-edit-btn').classList.add('hidden');
    },

    // Save financial
    saveFinancial: function() {
        const formData = new FormData();
        document.querySelectorAll('.financial-field').forEach(field => {
            formData.append(field.name, field.value);
        });

        return fetch(this.config.financialUpdateUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateFinancialView(data.financial);
            }
            return data;
        });
    },

    // Cancel financial edit
    cancelFinancialEdit: function() {
        document.getElementById('financial-view-mode').classList.remove('hidden');
        document.getElementById('financial-edit-mode').classList.add('hidden');
        document.querySelector('.financial-edit-btn').classList.remove('hidden');
    },

    // Update financial view
    updateFinancialView: function(financial) {
        const viewMode = document.getElementById('financial-view-mode');
        const purchasePrice = parseFloat(financial.purchase_price);
        const expectedSalePrice = parseFloat(financial.expected_sale_price);
        
        // Get total costs from equipment costs table
        const costRows = document.querySelectorAll('#equipment-costs-table tbody tr');
        let totalCosts = 0;
        costRows.forEach(row => {
            const amountCell = row.querySelector('td:nth-child(2)');
            if (amountCell) {
                const amountText = amountCell.textContent.replace('$', '').replace(',', '');
                totalCosts += parseFloat(amountText) || 0;
            }
        });
        
        const profit = expectedSalePrice - purchasePrice - totalCosts;
        const gridCols = purchasePrice ? 'grid-cols-4' : 'grid-cols-2';
        
        const purchasePriceHtml = purchasePrice ? 
            `<div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">$${purchasePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                <div class="text-sm text-gray-600">Purchase Price</div>
            </div>` : '';

        const profitHtml = purchasePrice ? 
            `<div class="text-center p-4 ${profit >= 0 ? 'bg-green-50' : 'bg-red-50'} rounded-lg">
                <div class="text-2xl font-bold ${profit >= 0 ? 'text-green-600' : 'text-red-600'}">$${profit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                <div class="text-sm text-gray-600">Estimated Profit</div>
            </div>` : '';

        viewMode.innerHTML = `
            <div class="grid ${gridCols} gap-6">
                ${purchasePriceHtml}
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">$${expectedSalePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    <div class="text-sm text-gray-600">Expected Sale Price</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">$${totalCosts.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    <div class="text-sm text-gray-600">Total Equipment Costs</div>
                </div>
                ${profitHtml}
            </div>
        `;
    },

    // ===== IMAGES EDITING =====
    
    // Enable images edit mode
    enableImagesEdit: function() {
        document.getElementById('images-view-mode').classList.add('hidden');
        document.getElementById('images-edit-mode').classList.remove('hidden');
        document.querySelector('.images-edit-btn').classList.add('hidden');
    },

    // Save images
    saveImages: function() {
        const formData = new FormData();
        const licenseFile = document.querySelector('input[name="car_license"]').files[0];
        const imageFiles = document.querySelector('input[name="car_images[]"]').files;
        
        if (licenseFile) {
            formData.append('car_license', licenseFile);
        }
        
        for (let i = 0; i < imageFiles.length; i++) {
            formData.append('car_images[]', imageFiles[i]);
        }

        fetch(this.config.imagesUpdateUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.cancelImagesEdit();
                this.showNotification(data.message, 'success');
                // Reload the page to show updated images
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showNotification(data.message || 'Error updating images', 'error');
            }
        })
        .catch(error => {
            console.error('Images save error:', error);
            this.showNotification('An error occurred while saving images', 'error');
        });
    },

    // Cancel images edit
    cancelImagesEdit: function() {
        document.getElementById('images-view-mode').classList.remove('hidden');
        document.getElementById('images-edit-mode').classList.add('hidden');
        document.querySelector('.images-edit-btn').classList.remove('hidden');
        
        // Clear file inputs
        document.querySelector('input[name="car_license"]').value = '';
        document.querySelector('input[name="car_images[]"]').value = '';
    }
};

// Global functions for inline event handlers
window.enableEditMode = function() {
    App.pages.carsShow.enableEditMode();
};

window.saveChanges = function() {
    App.pages.carsShow.saveChanges();
};

window.cancelEdit = function() {
    App.pages.carsShow.cancelEdit();
};

// Options functions
window.enableOptionsEdit = function() {
    App.pages.carsShow.enableOptionsEdit();
};

window.saveOptions = function() {
    App.pages.carsShow.saveOptions();
};

window.cancelOptionsEdit = function() {
    App.pages.carsShow.cancelOptionsEdit();
};

window.addOptionField = function() {
    const container = document.querySelector('.options-container');
    const newOption = document.createElement('div');
    newOption.className = 'option-item flex items-center gap-2 mb-3';
    newOption.innerHTML = `
        <input type="text" class="kt-input flex-1 option-input" placeholder="Enter option name">
        <button type="button" class="kt-btn kt-btn-sm kt-btn-danger remove-option-btn" onclick="removeOption(this)">
            <i class="ki-filled ki-trash"></i>
        </button>
    `;
    container.appendChild(newOption);
};

window.removeOption = function(button) {
    const optionItem = button.closest('.option-item');
    const container = document.querySelector('.options-container');
    
    // Don't remove if it's the last option item
    if (container.children.length > 1) {
        optionItem.remove();
    } else {
        // Clear the input instead of removing
        optionItem.querySelector('.option-input').value = '';
    }
};

// Inspection functions
window.enableInspectionEdit = function() {
    App.pages.carsShow.enableInspectionEdit();
};

window.saveInspection = function() {
    App.pages.carsShow.saveInspection();
};

window.cancelInspectionEdit = function() {
    App.pages.carsShow.cancelInspectionEdit();
};

// Financial functions
window.enableFinancialEdit = function() {
    App.pages.carsShow.enableFinancialEdit();
};

window.saveFinancial = function() {
    App.pages.carsShow.saveFinancial();
};

window.cancelFinancialEdit = function() {
    App.pages.carsShow.cancelFinancialEdit();
};

// Images functions
window.enableImagesEdit = function() {
    App.pages.carsShow.enableImagesEdit();
};

window.saveImages = function() {
    App.pages.carsShow.saveImages();
};

window.cancelImagesEdit = function() {
    App.pages.carsShow.cancelImagesEdit();
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the cars show page
    const carId = document.querySelector('[data-car-id]')?.getAttribute('data-car-id');
    const updateUrl = document.querySelector('[data-update-url]')?.getAttribute('data-update-url');
    
    if (carId && updateUrl) {
        App.pages.carsShow.init(carId, updateUrl);
    }
}); 
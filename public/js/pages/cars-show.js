/**
 * Car Show Page JavaScript
 * Handles inline editing functionality for car details
 */





    // Initialize the cars show functionality
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
                    this.initTabs();
        this.initLightGallery();
        this.initEquipmentCostForm();
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

        // Initialize tab functionality
        initTabs: function() {
            const tabToggles = document.querySelectorAll('[data-kt-tab-toggle]');
            const tabContents = document.querySelectorAll('[id^="tab_1_"]');

            tabToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-kt-tab-toggle');

                    // Remove active class from all toggles and hide all contents
                    tabToggles.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Add active class to clicked toggle and show target content
                    this.classList.add('active');
                    document.querySelector(targetId).classList.remove('hidden');
                    
                    // Preserve edit mode if it's enabled globally
                    if (document.body.classList.contains('global-edit-mode')) {
                        // Re-enable edit mode for the newly visible tab
                        const tabId = targetId.replace('#tab_1_', '');
                        
                        // Map tab IDs to edit mode element names
                        let editModeName = '';
                        switch(tabId) {
                            case '2': editModeName = 'options'; break;
                            case '3': editModeName = 'inspection'; break;
                            case '4': editModeName = 'financial'; break;
                            case '7': editModeName = 'images'; break;
                        }
                        
                        if (editModeName) {
                            const viewMode = document.getElementById(`${editModeName}-view-mode`);
                            const editMode = document.getElementById(`${editModeName}-edit-mode`);
                            
                            if (viewMode && editMode) {
                                viewMode.classList.add('hidden');
                                editMode.classList.remove('hidden');
                            }
                        }
                    }
                });
            });
        },

        // Initialize LightGallery
        initLightGallery: function() {
            // Initialize lightGallery for car images
            if (document.getElementById('car-images-gallery')) {
                lightGallery(document.getElementById('car-images-gallery'), {
                    plugins: [lgThumbnail, lgAutoplay, lgFullscreen],
                    speed: 500,
                    download: true,
                    counter: true,
                    thumbnail: true,
                    autoplay: true,
                    autoplayControls: true,
                    fullscreen: true
                });
            }

            // Initialize lightGallery for car license
            if (document.getElementById('car-license-gallery')) {
                lightGallery(document.getElementById('car-license-gallery'), {
                    plugins: [lgThumbnail, lgAutoplay, lgFullscreen],
                    speed: 500,
                    download: true,
                    counter: true,
                    thumbnail: true,
                    autoplay: true,
                    autoplayControls: true,
                    fullscreen: true
                });
            }
        },

        // Initialize equipment cost form
        initEquipmentCostForm: function() {
            const form = document.getElementById('addCostForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = document.getElementById('submitCostBtn');
                const submitText = submitBtn.querySelector('.submit-text');
                const loadingText = submitBtn.querySelector('.loading-text');
                
                // Clear previous errors
                App.pages.carsShow.clearFormErrors();
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                
                // Prepare form data
                const formData = new FormData(form);
                
                // Submit via AJAX
                fetch(App.pages.carsShow.config.equipmentCostUrl, {
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
                        // Success - add new row to table and close modal
                        App.utils.showToast('Equipment cost added successfully!', 'success');
                        
                        // Add new row to the equipment costs table
                        App.pages.carsShow.addEquipmentCostRow(data.cost);
                        
                        // Close modal
                        const modalEl = document.querySelector('#addCostModal');
                        const modal = KTModal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Reset form
                        form.reset();
                    } else {
                        // Show validation errors
                        App.pages.carsShow.showFormErrors(data.errors);
                        App.utils.showToast('Please correct the errors below.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    App.utils.showToast('An error occurred while adding the cost.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                });
            });
        },

        // Bind event listeners
        bindEvents: function() {
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
            const viewActions = document.getElementById('view-actions');
            const editActions = document.getElementById('edit-actions');
            
            if (viewActions) viewActions.classList.add('hidden');
            if (editActions) editActions.classList.remove('hidden');
        
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
                    const statusDisplay = document.querySelector('.status-display');
                    if (statusDisplay) statusDisplay.classList.add('hidden');
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
                    App.utils.showToast('All changes saved successfully!', 'success');
                this.disableEditMode();
            } else {
                    App.utils.showToast('Some changes could not be saved. Please check the errors.', 'error');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
                App.utils.showToast('An error occurred while saving. Please try again.', 'error');
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
        },

        // Update status display
        updateStatusDisplay: function(newStatus) {
            const statusConfig = {
                'not_received': ['kt-badge-warning', 'Not Received'],
                'paint': ['kt-badge-info', 'Paint'],
                'upholstery': ['kt-badge-primary', 'Upholstery'],
                'mechanic': ['kt-badge-warning', 'Mechanic'],
                'electrical': ['kt-badge-warning', 'Electrical'],
                'agency': ['kt-badge-info', 'Agency'],
                'polish': ['kt-badge-primary', 'Polish'],
                'ready': ['kt-badge-success', 'Ready'],
            };
            
            const [badgeClass, badgeText] = statusConfig[newStatus] || ['kt-badge-secondary', 'Unknown'];
            const statusDisplay = document.querySelector('.status-display');
            if (statusDisplay) {
                statusDisplay.innerHTML = `<span class="kt-badge ${badgeClass}">${badgeText}</span>`;
            }
        },

        // Save options
        saveOptions: function() {
            const options = [];
            const optionInputs = document.querySelectorAll('#options-edit-mode .option-input');
            
            optionInputs.forEach(input => {
                const value = input.value.trim();
                if (value) {
                    options.push(value);
                }
            });

            return fetch(this.config.optionsUpdateUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ options: options })
            })
            .then(response => response.json());
        },

        // Save inspection
        saveInspection: function() {
            const formData = new FormData();
            const inspectionFields = document.querySelectorAll('#inspection-edit-mode .inspection-field');
            
            inspectionFields.forEach(field => {
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
            .then(response => response.json());
        },

        // Save financial
        saveFinancial: function() {
            const formData = new FormData();
            const financialFields = document.querySelectorAll('#financial-edit-mode .financial-field');
            
            financialFields.forEach(field => {
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
            .then(response => response.json());
        },

        // Save images
        saveImages: function() {
            const formData = new FormData();
            const imageFields = document.querySelectorAll('#images-edit-mode .images-field');
            
            imageFields.forEach(field => {
                if (field.files.length > 0) {
                    if (field.name === 'car_images[]') {
                        // Multiple files
                        for (let i = 0; i < field.files.length; i++) {
                            formData.append('car_images[]', field.files[i]);
                        }
                    } else {
                        // Single file
                        formData.append(field.name, field.files[0]);
                    }
                }
            });

            return fetch(this.config.imagesUpdateUrl, {
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

    // Cancel edit mode
    cancelEdit: function() {
            this.config.isEditMode = false;
            
            // Hide edit actions, show view actions
            document.getElementById('view-actions').classList.remove('hidden');
            document.getElementById('edit-actions').classList.add('hidden');
            
            // Hide edit mode indicator
            this.hideEditModeIndicator();
            
            // Reset all editable fields to original values
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            const fieldName = field.getAttribute('name');
            const originalValue = this.config.originalValues[fieldName];
                
                if (originalValue !== undefined) {
            field.value = originalValue;
                }
                
                field.setAttribute('readonly', 'readonly');
                field.classList.remove('edit-mode');
                
                // Hide select fields, show status display
                if (field.name === 'status') {
                    field.classList.add('hidden');
                    document.querySelector('.status-display').classList.remove('hidden');
                }
            });
            
            // Disable edit mode for all tabs
            this.disableAllTabEditModes();
    },

    // Disable edit mode
    disableEditMode: function() {
        this.config.isEditMode = false;
        
            // Hide edit actions, show view actions
        document.getElementById('view-actions').classList.remove('hidden');
        document.getElementById('edit-actions').classList.add('hidden');
        
        // Hide edit mode indicator
        this.hideEditModeIndicator();
        
        // Make all editable fields readonly
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.setAttribute('readonly', 'readonly');
                field.classList.remove('edit-mode');
            
            // Hide select fields, show status display
            if (field.name === 'status') {
                field.classList.add('hidden');
                document.querySelector('.status-display').classList.remove('hidden');
            }
        });
        
        // Disable edit mode for all tabs
        this.disableAllTabEditModes();
    },

    // Show edit mode indicator
    showEditModeIndicator: function() {
        const indicator = document.createElement('div');
        indicator.className = 'edit-mode-indicator';
            indicator.textContent = 'Edit Mode Active';
            indicator.id = 'edit-mode-indicator';
        document.body.appendChild(indicator);
    },

    // Hide edit mode indicator
    hideEditModeIndicator: function() {
        const indicator = document.getElementById('edit-mode-indicator');
        if (indicator) {
            indicator.remove();
        }
    },

        // Clear form errors
        clearFormErrors: function() {
            const form = document.getElementById('addCostForm');
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
    },

        // Show form errors
        showFormErrors: function(errors) {
            if (!errors || typeof errors !== 'object') {
                console.warn('No errors object provided to showFormErrors');
                return;
            }
            
            const form = document.getElementById('addCostForm');
            if (!form) {
                console.warn('Form not found');
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
    },

        // Add equipment cost row to table
        addEquipmentCostRow: function(costData) {
            let tableBody = document.querySelector('#equipment-costs-table tbody');
            const emptyState = document.querySelector('.equipment .kt-card-content .text-center');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }

            // Check if table body exists, if not create the table structure
            if (!tableBody) {
                const table = document.createElement('table');
                table.id = 'equipment-costs-table';
                table.className = 'w-full';
                table.innerHTML = `
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold">Description</th>
                            <th class="text-left py-3 px-4 font-semibold">Amount</th>
                            <th class="text-left py-3 px-4 font-semibold">Date</th>
                            <th class="text-left py-3 px-4 font-semibold">Added By</th>
                            <th class="text-left py-3 px-4 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `;
                document.querySelector('.equipment .kt-card-content').appendChild(table);
                tableBody = table.querySelector('tbody');
            }
            
            // Create new row
            const newRow = document.createElement('tr');
            newRow.className = 'border-b border-gray-200';
            newRow.innerHTML = `
                <td class="py-3 px-4">${costData.description}</td>
                <td class="py-3 px-4 font-semibold">$${parseFloat(costData.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-3 px-4">${new Date(costData.cost_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                <td class="py-3 px-4 text-gray-600">${costData.user_name}</td>
                <td class="py-3 px-4">
                    <span class="kt-badge kt-badge-warning">Pending</span>
                </td>
            `;
            
            // Add row to the beginning of the table (most recent first)
            tableBody.insertBefore(newRow, tableBody.firstChild);
    }
};

// Global functions for inline event handlers
window.enableEditMode = function() {
        if (App.pages.carsShow) {
    App.pages.carsShow.enableEditMode();
        }
};

window.saveChanges = function() {
        if (App.pages.carsShow) {
    App.pages.carsShow.saveChanges();
        }
};

window.cancelEdit = function() {
        if (App.pages.carsShow) {
    App.pages.carsShow.cancelEdit();
        }
};

// Options functions
window.addOptionField = function() {
    const container = document.querySelector('.options-container');
        if (!container) return;
        
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
        
        if (!optionItem || !container) return;
    
    // Don't remove if it's the last option item
    if (container.children.length > 1) {
        optionItem.remove();
    } else {
        // Clear the input instead of removing
        optionItem.querySelector('.option-input').value = '';
    }
};

    // Options save/cancel functions
    window.saveOptions = function() {
        if (App.pages.carsShow) {
            App.pages.carsShow.saveOptions()
                .then(data => {
                    if (data.success) {
                        App.utils.showToast('Options saved successfully!', 'success');
                        // Refresh the page to show updated options
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        App.utils.showToast('Error saving options', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    App.utils.showToast('An error occurred while saving options', 'error');
                });
        }
    };

    window.cancelOptionsEdit = function() {
        document.getElementById('options-view-mode').classList.remove('hidden');
        document.getElementById('options-edit-mode').classList.add('hidden');
    };

    // Inspection save/cancel functions
window.saveInspection = function() {
        if (App.pages.carsShow) {
            App.pages.carsShow.saveInspection()
                .then(data => {
                    if (data.success) {
                        App.utils.showToast('Inspection saved successfully!', 'success');
                        // Refresh the page to show updated inspection
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        App.utils.showToast('Error saving inspection', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    App.utils.showToast('An error occurred while saving inspection', 'error');
                });
        }
};

window.cancelInspectionEdit = function() {
        document.getElementById('inspection-view-mode').classList.remove('hidden');
        document.getElementById('inspection-edit-mode').classList.add('hidden');
    };

    // Financial save/cancel functions
window.saveFinancial = function() {
        if (App.pages.carsShow) {
            App.pages.carsShow.saveFinancial()
                .then(data => {
                    if (data.success) {
                        App.utils.showToast('Financial information saved successfully!', 'success');
                        // Refresh the page to show updated financial info
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        App.utils.showToast('Error saving financial information', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    App.utils.showToast('An error occurred while saving financial information', 'error');
                });
        }
};

window.cancelFinancialEdit = function() {
        document.getElementById('financial-view-mode').classList.remove('hidden');
        document.getElementById('financial-edit-mode').classList.add('hidden');
    };

    // Images save/cancel functions
window.saveImages = function() {
        if (App.pages.carsShow) {
            App.pages.carsShow.saveImages()
                .then(data => {
                    if (data.success) {
                        App.utils.showToast('Images saved successfully!', 'success');
                        // Refresh the page to show updated images
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        App.utils.showToast('Error saving images', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    App.utils.showToast('An error occurred while saving images', 'error');
                });
        }
};

window.cancelImagesEdit = function() {
        document.getElementById('images-view-mode').classList.remove('hidden');
        document.getElementById('images-edit-mode').classList.add('hidden');
        
        // Clear file inputs
        const fileInputs = document.querySelectorAll('#images-edit-mode input[type="file"]');
        fileInputs.forEach(input => {
            input.value = '';
        });
    };

    // Event delegation for all buttons
    document.addEventListener('click', function(e) {
        // Handle data-action buttons
        const actionBtn = e.target.closest('[data-action]');
        if (actionBtn) {
            const action = actionBtn.getAttribute('data-action');
            switch (action) {
                case 'add-option-field':
                    window.addOptionField();
                    break;
                case 'remove-option':
                    window.removeOption(actionBtn);
                    break;
                case 'save-options':
                    window.saveOptions();
                    break;
                case 'cancel-options-edit':
                    window.cancelOptionsEdit();
                    break;
                case 'save-inspection':
                    window.saveInspection();
                    break;
                case 'cancel-inspection-edit':
                    window.cancelInspectionEdit();
                    break;
                case 'save-financial':
                    window.saveFinancial();
                    break;
                case 'cancel-financial-edit':
                    window.cancelFinancialEdit();
                    break;
                case 'save-images':
                    window.saveImages();
                    break;
                case 'cancel-images-edit':
                    window.cancelImagesEdit();
                    break;
            }
            return;
        }

        // Handle edit mode buttons
        const editBtn = e.target.closest('#edit-btn');
        if (editBtn && App.pages.carsShow) {
            App.pages.carsShow.enableEditMode();
            return;
        }

        const saveBtn = e.target.closest('#save-btn');
        if (saveBtn && App.pages.carsShow) {
            App.pages.carsShow.saveChanges();
            return;
        }

        const cancelBtn = e.target.closest('#cancel-btn');
        if (cancelBtn && App.pages.carsShow) {
            App.pages.carsShow.cancelEdit();
            return;
        }
    });

        // Initialize if we have the required data
    const carDataElement = document.getElementById('car-data');
    
    if (carDataElement) {
        const carId = carDataElement.getAttribute('data-car-id');
        const updateUrl = carDataElement.getAttribute('data-update-url');
        const equipmentCostUrl = carDataElement.getAttribute('data-equipment-cost-url');
    
    if (carId && updateUrl) {
        App.pages.carsShow.init(carId, updateUrl);
            // Set the equipment cost URL in the config
            if (equipmentCostUrl) {
                App.pages.carsShow.config.equipmentCostUrl = equipmentCostUrl;
    }
        }
    } 
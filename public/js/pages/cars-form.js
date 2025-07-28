/**
 * Cars Form Page JavaScript (Create/Edit)
 * Handles multi-step wizard, validation, and file uploads for car forms
 */

App.pages.carsForm = {
    // Private variables
    _currentStep: 1,
    _totalSteps: 6,
    _form: null,
    _licensePond: null,
    _imagesPond: null,
    _validationTimeouts: {},
    _validationCache: {},
    _isValidating: false,
    _selectedOptions: new Set(), // Track selected options
    
    // Step field mappings - must match CarStepRequest validation rules
    _stepFields: {
        1: ['model', 'vehicle_category', 'plate_number', 'purchase_date', 'insurance_expiry_date'], // Basic Info
        2: ['manufacturing_year', 'engine_capacity', 'number_of_keys', 'place_of_manufacture'], // Specifications
        3: ['options', 'all_options'], // Options
        4: ['purchase_price', 'expected_sale_price', 'status', 'bulk_deal_id'], // Pricing & Status
        5: ['chassis_inspection', 'transmission', 'motor', 'body_notes', 'hood', 'front_right_fender', 'front_left_fender', 'rear_right_fender', 'rear_left_fender', 'trunk_door', 'front_right_door', 'rear_right_door', 'front_left_door', 'rear_left_door'], // Inspection
        6: ['car_license', 'car_images'] // Images
    },

    // Required fields per step
    _requiredFields: {
        1: ['model', 'purchase_date'],
        2: ['manufacturing_year'],
        3: [],
        4: ['purchase_price', 'expected_sale_price', 'status'],
        5: [],
        6: []
    },

    // Initialize the form wizard
    init: function() {
        this._form = document.getElementById('car-form');
        
        // Only initialize if we're on the cars form page
        if (!this._form) {
            return;
        }

        this._initializeFilePond();
        this._bindEvents();
        this._initializeValidation();
        this._initializeProfitCalculator();
        this._initializeOptions();
        this._updateStepDisplay();
        this._initializeResponsiveHandlers();
        

    },

    // Initialize FilePond for file uploads
    _initializeFilePond: function() {
        // Check if FilePond is available
        if (typeof FilePond === 'undefined') {
            console.warn('FilePond not loaded - file uploads will not work');
            return;
        }

        // Register FilePond plugins
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        // Initialize license upload
        const licenseElement = document.querySelector('#license-filepond');
        if (licenseElement) {
            this._licensePond = FilePond.create(licenseElement, {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                maxFiles: 1,
                labelIdle: `Drag & Drop your license image or <span class="filepond--label-action">Browse</span><br><small>JPG, PNG (Max 2MB)</small>`,
                allowReplace: false,
                allowRevert: false,
                instantUpload: false,
                server: null,
                onaddfile: (error, file) => {
                    if (!error) {
                
                        this._validateFileStep();
                    }
                },
                onremovefile: () => {
                    this._validateFileStep();
                }
            });
        }

        // Initialize car images upload
        const imagesElement = document.querySelector('#images-filepond');
        if (imagesElement) {
            this._imagesPond = FilePond.create(imagesElement, {
                acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
                maxFileSize: '2MB',
                maxFiles: 10,
                allowMultiple: true,
                allowReorder: true,
                labelIdle: `Drag & Drop your car images or <span class="filepond--label-action">Browse</span><br><small>JPG, PNG (Max 2MB each, up to 10 images)</small>`,
                allowReplace: false,
                allowRevert: false,
                instantUpload: false,
                server: null,
                onaddfile: (error, file) => {
                    if (!error) {
                
                        this._validateFileStep();
                    }
                },
                onremovefile: () => {
                    this._validateFileStep();
                }
            });
        }
    },

    // Bind all form events
    _bindEvents: function() {
        // Step navigation
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this._handleNextStep());
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this._handlePrevStep());
        }

        // Form submission
        this._form.addEventListener('submit', (e) => this._handleFormSubmit(e));
        
        // Fallback submit button
        const fallbackBtn = document.getElementById('submit-normal-btn');
        if (fallbackBtn) {
            fallbackBtn.addEventListener('click', () => {
                // Disable AJAX and submit normally
                this._form.removeEventListener('submit', this._handleFormSubmit);
                this._form.submit();
            });
        }

        // Step indicator clicks
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            indicator.addEventListener('click', () => this._goToStep(index + 1));
        });
    },

    // Initialize real-time validation with improved approach
    _initializeValidation: function() {
        const inputs = this._form.querySelectorAll('input:not([type="file"]), select, textarea');
        
        inputs.forEach(input => {
            // Skip file inputs as they're handled by FilePond
            if (input.type === 'file') return;

            // Different validation strategies based on input type
            if (input.type === 'email' || input.type === 'url') {
                // Validate on blur and after typing stops
                input.addEventListener('blur', () => this._validateField(input));
                input.addEventListener('input', App.utils.debounce(() => this._validateField(input), 800));
            } else if (input.type === 'number' || input.type === 'date') {
                // Validate immediately on change
                input.addEventListener('change', () => this._validateField(input));
                input.addEventListener('input', App.utils.debounce(() => this._validateField(input), 500));
            } else if (input.tagName.toLowerCase() === 'select') {
                // Validate immediately on change
                input.addEventListener('change', () => this._validateField(input));
            } else {
                // Default: validate on blur and after typing stops
                input.addEventListener('blur', () => this._validateField(input));
                input.addEventListener('input', App.utils.debounce(() => this._validateField(input), 600));
            }

            // Clear validation state when user starts typing
            input.addEventListener('focus', () => this._clearFieldValidation(input));
        });

        // Special handling for date fields with interdependencies
        this._setupDateValidation();
    },

    // Setup special date validation logic
    _setupDateValidation: function() {
        const purchaseDate = document.getElementById('purchase_date');
        const insuranceDate = document.getElementById('insurance_expiry_date');

        if (purchaseDate && insuranceDate) {
            // Validate insurance date when purchase date changes (only if insurance has a value)
            purchaseDate.addEventListener('change', () => {
                if (insuranceDate.value) {
                    setTimeout(() => {
                        this._validateField(insuranceDate);
                    }, 100);
                }
            });

            // Validate purchase date when insurance date changes (only if purchase has a value)
            insuranceDate.addEventListener('change', () => {
                if (purchaseDate.value) {
                    setTimeout(() => {
                        this._validateField(purchaseDate);
                    }, 100);
                }
            });
        }
    },

    // Initialize profit calculator
    _initializeProfitCalculator: function() {
        const purchasePrice = document.getElementById('purchase_price');
        const expectedPrice = document.getElementById('expected_sale_price');
        const profitMargin = document.getElementById('profit-margin');

        if (!purchasePrice || !expectedPrice || !profitMargin) return;

        const calculateProfit = () => {
            const purchase = parseFloat(purchasePrice.value) || 0;
            const expected = parseFloat(expectedPrice.value) || 0;

            if (purchase > 0 && expected > 0) {
                const profit = expected - purchase;
                const margin = (profit / purchase) * 100;
                profitMargin.textContent = margin.toFixed(1) + '%';

                // Color coding with CSS classes
                profitMargin.className = 'font-semibold ' + (
                    margin > 20 ? 'text-green-600' :
                    margin > 10 ? 'text-yellow-600' :
                    'text-red-600'
                );
            } else {
                profitMargin.textContent = '0%';
                profitMargin.className = 'text-gray-500';
            }
        };

        purchasePrice.addEventListener('input', App.utils.debounce(calculateProfit, 300));
        expectedPrice.addEventListener('input', App.utils.debounce(calculateProfit, 300));
        
        // Calculate on page load
        calculateProfit();
    },

    // Initialize options functionality
    _initializeOptions: function() {
        const customInput = document.getElementById('custom-option-input');
        const addButton = document.getElementById('add-custom-option');
        const predefinedButtonsContainer = document.getElementById('predefined-options-buttons');
        const allOptionsInput = document.getElementById('all-options-input');

        if (!customInput || !addButton || !predefinedButtonsContainer || !allOptionsInput) {
            console.warn('Options elements not found');
            return;
        }

        // Load existing options from hidden input
        const existingOptions = allOptionsInput.value;
        if (existingOptions) {
            try {
                const options = JSON.parse(existingOptions);
                options.forEach(option => this._addOptionToList(option));
            } catch (e) {
                console.error('Error parsing existing options:', e);
            }
        }

        // Handle custom option addition
        addButton.addEventListener('click', () => this._addCustomOption());
        customInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this._addCustomOption();
            }
        });

        // Handle predefined options selection via buttons
        predefinedButtonsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('predefined-option-btn')) {
                const option = e.target.getAttribute('data-option');
                this._moveOptionToSelected(option, e.target);
            }
        });

        // Update hidden input when options change
        this._updateAllOptionsInput();
    },

    // Move option from predefined to selected list
    _moveOptionToSelected: function(option, buttonElement) {
        if (this._selectedOptions.has(option)) return;

        // Hide the button from predefined list
        buttonElement.style.display = 'none';
        
        // Add to selected options
        this._addOptionToList(option);
    },

    // Return option from selected to predefined list
    _returnOptionToPredefined: function(option) {
        const predefinedButtonsContainer = document.getElementById('predefined-options-buttons');
        const button = predefinedButtonsContainer.querySelector(`[data-option="${option.replace(/"/g, '\"')}"]`);
        
        if (button) {
            // Show the button again
            button.style.display = 'inline-block';
        }
    },

    // Add custom option
    _addCustomOption: function() {
        const customInput = document.getElementById('custom-option-input');
        const optionText = customInput.value.trim();

        if (!optionText) {
            this._showNotification('Please enter a feature name', 'warning');
            return;
        }

        if (this._selectedOptions.has(optionText)) {
            this._showNotification('This feature is already added', 'warning');
            return;
        }

        this._addOptionToList(optionText);
        customInput.value = '';
        customInput.focus();
    },

    // Add option to the selected list
    _addOptionToList: function(optionText) {
        if (this._selectedOptions.has(optionText)) return;

        this._selectedOptions.add(optionText);
        this._updateSelectedOptionsDisplay();
        this._updateAllOptionsInput();
    },

    // Remove option from the selected list
    _removeOptionFromList: function(optionText) {
        this._selectedOptions.delete(optionText);
        this._updateSelectedOptionsDisplay();
        this._updateAllOptionsInput();
        
        // Return the option to predefined list if it was originally from there
        this._returnOptionToPredefined(optionText);
    },

    // Update the selected options display
    _updateSelectedOptionsDisplay: function() {
        const container = document.getElementById('selected-options-container');
        const list = document.getElementById('selected-options-list');
        const message = document.getElementById('no-options-message');

        if (!container || !list || !message) return;

        if (this._selectedOptions.size === 0) {
            list.style.display = 'none';
            message.style.display = 'block';
        } else {
            list.style.display = 'block';
            message.style.display = 'none';

            // Clear existing list
            list.innerHTML = '';

            // Add each option
            this._selectedOptions.forEach(option => {
                const optionElement = document.createElement('div');
                optionElement.className = 'flex items-center justify-between bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm';
                optionElement.innerHTML = `
                    <span class="text-sm text-gray-700">${option}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 ml-2 cursor-pointer" 
                            onclick="App.pages.carsForm._removeOptionFromList('${option.replace(/'/g, "\\'")}')">
                        <i class="ki-duotone ki-cross fs-2"></i>
                    </button>
                `;
                list.appendChild(optionElement);
            });
        }
    },

    // Update the hidden input with all selected options
    _updateAllOptionsInput: function() {
        const allOptionsInput = document.getElementById('all-options-input');
        if (allOptionsInput) {
            allOptionsInput.value = JSON.stringify(Array.from(this._selectedOptions));
        }
    },

    // Validate file step (Step 4)
    _validateFileStep: function() {
        if (this._currentStep !== 4) return;

        // For file validation, we just check if files are properly loaded
        // Actual file validation happens on server side
        const licenseFiles = this._licensePond ? this._licensePond.getFiles() : [];
        const imageFiles = this._imagesPond ? this._imagesPond.getFiles() : [];

        // Files are optional, so step 4 is always considered valid
        return Promise.resolve(true);
    },

    // Validate options step (Step 6)
    _validateOptionsStep: function() {
        if (this._currentStep !== 6) return;

        // Options are optional, so step 6 is always considered valid
        // But we can add some basic validation if needed

        return Promise.resolve(true);
    },

    // Handle next step
    _handleNextStep: function() {
        if (this._isValidating) {
            return;
        }

        this._validateCurrentStep().then(isValid => {
            if (isValid) {
                this._goToStep(this._currentStep + 1);
            } else {
                this._showNotification('Please fix the errors before proceeding', 'error');
            }
        });
    },

    // Handle previous step
    _handlePrevStep: function() {
        if (this._currentStep > 1) {
            this._goToStep(this._currentStep - 1);
        }
    },

    // Navigate to specific step
    _goToStep: function(step) {
        if (step < 1 || step > this._totalSteps) return;

        // Hide current step
        const currentStepElement = document.querySelector(`.step-content[data-step="${this._currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('active');
        }

        // Show new step
        const newStepElement = document.querySelector(`.step-content[data-step="${step}"]`);
        if (newStepElement) {
            newStepElement.classList.add('active');
        }

        this._currentStep = step;
        this._updateStepDisplay();
    },

    // Update step indicators and navigation
    _updateStepDisplay: function() {
        // Update progress bar
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            const progress = (this._currentStep / this._totalSteps) * 100;
            progressBar.style.width = progress + '%';
        }

        // Update step indicators
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const stepNum = index + 1;
            const stepNumberEl = indicator.querySelector('.step-number');
            const stepLabelEl = indicator.querySelector('.step-label');
            
            indicator.classList.remove('active', 'completed');
            
            if (stepNum < this._currentStep) {
                indicator.classList.add('completed');
                if (stepNumberEl) {
                    stepNumberEl.className = 'step-number bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold';
                }
                if (stepLabelEl) {
                    stepLabelEl.className = 'step-label ml-2 text-sm font-medium text-green-600 hidden sm:block';
                }
            } else if (stepNum === this._currentStep) {
                indicator.classList.add('active');
                if (stepNumberEl) {
                    stepNumberEl.className = 'step-number bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold';
                }
                if (stepLabelEl) {
                    stepLabelEl.className = 'step-label ml-2 text-sm font-medium text-blue-600 hidden sm:block';
                }
            } else {
                if (stepNumberEl) {
                    stepNumberEl.className = 'step-number bg-gray-300 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold';
                }
                if (stepLabelEl) {
                    stepLabelEl.className = 'step-label ml-2 text-sm font-medium text-gray-500 hidden sm:block';
                }
            }
        });

        // Update current step display
        const currentStepDisplay = document.getElementById('current-step');
        if (currentStepDisplay) {
            currentStepDisplay.textContent = this._currentStep;
        }

        // Update navigation buttons
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.querySelector('button[type="submit"]');

        if (prevBtn) {
            prevBtn.disabled = this._currentStep === 1;
        }

        if (nextBtn) {
            nextBtn.style.display = this._currentStep === this._totalSteps ? 'none' : 'inline-flex';
        }

        if (submitBtn) {
            submitBtn.style.display = this._currentStep === this._totalSteps ? 'inline-flex' : 'none';
        }
    },

    // Validate single field with improved error handling
    _validateField: function(field) {
        if (!field || !field.name) return;

        const fieldName = field.name;
        const fieldValue = field.value;
        const step = this._getFieldStep(fieldName);

        // Skip validation for file fields or fields not in current step
        if (!step || field.type === 'file') return;

        // Check if field is in current step's fields
        if (!this._stepFields[step].includes(fieldName)) return;

        // Clear previous timeout for this field only
        if (this._validationTimeouts[fieldName]) {
            clearTimeout(this._validationTimeouts[fieldName]);
        }

        // Set new timeout for debounced validation - only for this field
        this._validationTimeouts[fieldName] = setTimeout(() => {
            this._validateFieldAjax(fieldName, fieldValue, step);
        }, 200);
    },

    // Clear field validation state
    _clearFieldValidation: function(field) {
        if (!field || !field.name) return;

        const fieldName = field.name;
        
        // Clear timeout for this field only
        if (this._validationTimeouts[fieldName]) {
            clearTimeout(this._validationTimeouts[fieldName]);
            delete this._validationTimeouts[fieldName];
        }

        // Remove visual validation states only for this field
        field.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50', 'validating');
        
        // Remove validation messages more thoroughly - only for this field
        this._removeValidationMessages(fieldName);
    },

    // Remove all validation messages for a specific field only
    _removeValidationMessages: function(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.parentNode) return;

        // Remove all validation messages for this specific field only
        const parentContainer = field.parentNode;
        const allMessages = parentContainer.querySelectorAll(`[data-field="${fieldName}"].field-error, [data-field="${fieldName}"].field-success`);
        allMessages.forEach(message => {
            message.remove();
        });

        // Also check for messages in broader containers - but only for this field
        const fieldContainer = field.closest('.kt-card-content, .step-content, .form-group');
        if (fieldContainer) {
            const siblingMessages = fieldContainer.querySelectorAll(`[data-field="${fieldName}"].field-error, [data-field="${fieldName}"].field-success`);
            siblingMessages.forEach(message => {
                message.remove();
            });
        }
    },

    // Check if current message is the same as the new message
    _hasCurrentMessage: function(fieldName, messageText, messageType) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.parentNode) return false;

        const existingMessage = field.parentNode.querySelector(`[data-field="${fieldName}"][data-message-type="${messageType}"]`);
        if (!existingMessage) return false;

        // Extract text content (excluding the SVG icon)
        const textContent = existingMessage.textContent.trim();
        return textContent === messageText || textContent === 'Valid';
    },

    // Get step number for field
    _getFieldStep: function(fieldName) {
        for (const [step, fields] of Object.entries(this._stepFields)) {
            if (fields.includes(fieldName)) {
                return parseInt(step);
            }
        }
        return null;
    },

    // AJAX field validation with improved error handling and message checking
    _validateFieldAjax: function(fieldName, fieldValue, step) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        // Check cache first
        const cacheKey = `${fieldName}_${fieldValue}_${step}`;
        if (this._validationCache[cacheKey]) {
            const cachedResult = this._validationCache[cacheKey];
            if (cachedResult.success) {
                // Only show success message if it's not already there
                if (!this._hasCurrentMessage(fieldName, 'Valid', 'success')) {
                    this._showFieldSuccess(fieldName);
                }
            } else {
                // Only show error message if it's different from current
                if (!this._hasCurrentMessage(fieldName, cachedResult.message, 'error')) {
                    this._showFieldError(fieldName, cachedResult.message);
                }
            }
            return;
        }

        // Add loading state only to this field
        field.classList.add('validating');
        this._isValidating = true;

        const formData = new FormData();
        formData.append('step', step);
        formData.append('single_field', fieldName);
        formData.append(fieldName, fieldValue);

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]') || 
                         document.querySelector('input[name="_token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.content || csrfToken.value);
        }

        const validateUrl = this._form.getAttribute('data-validate-url') || '/cars/validate-step';

        fetch(validateUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json();
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Remove loading state from this field only
            field.classList.remove('validating');
            this._isValidating = false;

            if (data.success) {
                // Only show success message if it's not already there
                if (!this._hasCurrentMessage(fieldName, 'Valid', 'success')) {
                    this._showFieldSuccess(fieldName);
                }
                // Cache successful validation
                this._validationCache[cacheKey] = { success: true };
            } else {
                const errorMessage = data.errors && data.errors[fieldName] ? 
                                   data.errors[fieldName][0] : 
                                   'Validation failed';
                
                // Only show error message if it's different from current
                if (!this._hasCurrentMessage(fieldName, errorMessage, 'error')) {
                    this._showFieldError(fieldName, errorMessage);
                }
                // Cache failed validation
                this._validationCache[cacheKey] = { success: false, message: errorMessage };
            }
        })
        .catch(error => {
            // Remove loading state from this field only
            field.classList.remove('validating');
            this._isValidating = false;

            console.error('Validation error:', error);
            
            // Show user-friendly error message only if different
            let errorMessage;
            if (error.message.includes('404')) {
                errorMessage = 'Validation service not available';
            } else if (error.message.includes('500')) {
                errorMessage = 'Server error during validation';
            } else {
                errorMessage = 'Unable to validate field';
            }

            if (!this._hasCurrentMessage(fieldName, errorMessage, 'error')) {
                this._showFieldError(fieldName, errorMessage);
            }
        });
    },

    // Validate current step with improved logic
    _validateCurrentStep: function() {
        // Handle file step separately
        if (this._currentStep === 4) {
            return this._validateFileStep();
        }

        // Handle options step separately
        if (this._currentStep === 6) {
            return this._validateOptionsStep();
        }

        const currentStepElement = document.querySelector(`.step-content[data-step="${this._currentStep}"]`);
        if (!currentStepElement) return Promise.resolve(false);

        const fields = currentStepElement.querySelectorAll('input:not([type="file"]), select, textarea');
        const formData = new FormData();

        // Add all field values to form data
        fields.forEach(field => {
            if (field.name) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    if (field.checked) {
                        formData.append(field.name, field.value);
                    }
                } else {
                    formData.append(field.name, field.value);
                }
            }
        });

        formData.append('step', this._currentStep);

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]') || 
                         document.querySelector('input[name="_token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.content || csrfToken.value);
        }

        const validateUrl = this._form.getAttribute('data-validate-url') || '/cars/validate-step';

        return fetch(validateUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json();
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Clear any existing field errors and show success for all fields
                fields.forEach(field => {
                    if (field.name) {
                        field.classList.remove('border-red-500', 'bg-red-50');
                        this._removeValidationMessages(field.name);
                    }
                });
                return true;
            } else {
                // Show errors only for fields that have different error messages
                Object.keys(data.errors).forEach(fieldName => {
                    const errorMessage = data.errors[fieldName][0];
                    if (!this._hasCurrentMessage(fieldName, errorMessage, 'error')) {
                        this._showFieldError(fieldName, errorMessage);
                    }
                });
                return false;
            }
        })
        .catch(error => {
            console.error('Step validation error:', error);
            this._showNotification('Unable to validate form. Please check your connection.', 'error');
            return false;
        });
    },

    // Show field error with improved styling and unique ID
    _showFieldError: function(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        // Remove any existing messages for this field only
        this._removeValidationMessages(fieldName);

        // Remove success state
        field.classList.remove('border-green-500', 'bg-green-50');
        field.classList.add('border-red-500', 'bg-red-50');

        // Add error message with unique identifier
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-600 text-sm mt-1 flex items-center animate-slide-in mt-2';
        errorDiv.setAttribute('data-field', fieldName);
        errorDiv.setAttribute('data-message-type', 'error');
        errorDiv.innerHTML = `
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        `;
        
        field.parentNode.appendChild(errorDiv);
    },

    // Show field success with improved styling and unique ID
    _showFieldSuccess: function(fieldName) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        // Remove any existing messages for this field only
        this._removeValidationMessages(fieldName);

        // Remove error state
        field.classList.remove('border-red-500', 'bg-red-50');
        field.classList.add('border-green-500', 'bg-green-50');

        // Add success message with unique identifier
        const successDiv = document.createElement('div');
        successDiv.className = 'field-success text-green-600 text-sm mt-1 flex items-center animate-slide-in mt-2';
        successDiv.setAttribute('data-field', fieldName);
        successDiv.setAttribute('data-message-type', 'success');
        successDiv.innerHTML = `
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Valid
        `;
        
        field.parentNode.appendChild(successDiv);
    },

    // Sync FilePond files with form
    _syncFilePondFiles: function() {
        if (this._licensePond) {
            const licenseFiles = this._licensePond.getFiles();
            const licenseHiddenInput = document.querySelector('#license-hidden');

            if (licenseFiles.length > 0 && licenseHiddenInput) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(licenseFiles[0].file);
                    licenseHiddenInput.files = dt.files;
            
                } catch (error) {
                    console.error('Error syncing license file:', error);
                }
            }
        }

        if (this._imagesPond) {
            const imageFiles = this._imagesPond.getFiles();
            const imageHiddenInput = document.querySelector('#images-hidden');

            if (imageFiles.length > 0 && imageHiddenInput) {
                try {
                    const dt = new DataTransfer();
                    imageFiles.forEach(file => dt.items.add(file.file));
                    imageHiddenInput.files = dt.files;
            
                } catch (error) {
                    console.error('Error syncing image files:', error);
                }
            }
        }
    },

    // Handle form submission with improved validation
    _handleFormSubmit: function(e) {
        e.preventDefault();

        if (this._isValidating) {
            this._showNotification('Please wait for validation to complete', 'info');
            return;
        }

        this._validateCurrentStep().then(isValid => {
            if (isValid) {
                // Sync FilePond files
                this._syncFilePondFiles();

                // Update options before submission
                this._updateAllOptionsInput();

                // Submit form with files
                const formData = new FormData(this._form);
                
                fetch(this._form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.success) {
                            this._showNotification('Car saved successfully!', 'success');
                        }
                    } else {
                        // Handle validation errors
                        if (response.status === 422 && data.errors) {
                            this._displayValidationErrors(data.errors);
                            this._showNotification('Please correct the validation errors.', 'error');
                        } else {
                            this._showNotification(data.message || 'Error saving car. Please try again.', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);
                    this._showNotification('Network error. You can try the fallback submit button below.', 'error');
                    
                    // Show fallback button
                    const fallbackBtn = document.getElementById('submit-normal-btn');
                    if (fallbackBtn) {
                        fallbackBtn.style.display = 'inline-block';
                    }
                });
            } else {
                this._showNotification('Please correct all errors before submitting', 'error');
            }
        });
    },

    // Display validation errors in the form
    _displayValidationErrors: function(errors) {
        // Clear existing error displays
        this._form.querySelectorAll('.error-message').forEach(el => el.remove());
        this._form.querySelectorAll('.kt-input, .kt-select, .kt-textarea').forEach(el => {
            el.classList.remove('border-red-500');
        });

        // Display each error
        Object.keys(errors).forEach(fieldName => {
            const field = this._form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('border-red-500');
                
                // Create error message element
                const errorEl = document.createElement('p');
                errorEl.className = 'error-message text-red-500 text-sm mt-1';
                errorEl.textContent = errors[fieldName][0]; // First error message
                
                // Insert after the field
                field.parentNode.insertBefore(errorEl, field.nextSibling);
            }
        });
    },

    // Show notification with improved styling
    _showNotification: function(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification ${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md mt-2`;
        
        const colors = {
            'error': 'bg-red-500 text-white',
            'success': 'bg-green-500 text-white',
            'info': 'bg-blue-500 text-white',
            'warning': 'bg-yellow-500 text-black'
        };
        
        notification.className += ` ${colors[type] || colors.info}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    },

    // Public methods
    goToStep: function(step) {
        this._goToStep(step);
    },

    getCurrentStep: function() {
        return this._currentStep;
    },

    validateStep: function(step = null) {
        if (step && step !== this._currentStep) {
            this._goToStep(step);
        }
        return this._validateCurrentStep();
    },

    clearValidationCache: function() {
        this._validationCache = {};
    },

    // Initialize responsive handlers
    _initializeResponsiveHandlers: function() {
        // Handle window resize events
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this._handleResponsiveLayout();
            }, 250);
        });

        // Initial responsive setup
        this._handleResponsiveLayout();
    },

    // Handle responsive layout changes
    _handleResponsiveLayout: function() {
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        
        // Adjust step indicators for mobile
        const stepIndicators = document.querySelectorAll('.step-indicator');
        stepIndicators.forEach(indicator => {
            if (isMobile) {
                indicator.style.fontSize = '0.75rem';
            } else {
                indicator.style.fontSize = '';
            }
        });

        // Adjust form spacing for mobile
        const cards = document.querySelectorAll('.kt-card-content');
        cards.forEach(card => {
            if (isMobile) {
                card.style.padding = '1rem';
            } else if (isTablet) {
                card.style.padding = '1.25rem';
            } else {
                card.style.padding = '';
            }
        });

        // Adjust button sizes for mobile
        const buttons = document.querySelectorAll('.kt-btn');
        buttons.forEach(button => {
            if (isMobile) {
                button.style.minHeight = '44px';
                button.style.padding = '0.75rem 1rem';
            } else {
                button.style.minHeight = '';
                button.style.padding = '';
            }
        });

        // Adjust textarea heights for mobile
        const textareas = document.querySelectorAll('.kt-textarea');
        textareas.forEach(textarea => {
            if (isMobile) {
                textarea.style.minHeight = '80px';
            } else {
                textarea.style.minHeight = '';
            }
        });

        // Handle step navigation visibility on mobile
        const stepNavigation = document.getElementById('step-navigation');
        if (stepNavigation) {
            if (isMobile) {
                // On mobile, make navigation more compact
                const prevBtn = stepNavigation.querySelector('#prev-btn');
                const nextBtn = stepNavigation.querySelector('#next-btn');
                
                if (prevBtn) {
                    prevBtn.innerHTML = '<i class="ki-duotone ki-arrow-left fs-2"></i>';
                }
                if (nextBtn) {
                    nextBtn.innerHTML = '<i class="ki-duotone ki-arrow-right fs-2"></i>';
                }
            }
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    App.pages.carsForm.init();
});

// Make some functions globally accessible for onclick handlers if needed
window.goToStep = function(step) {
    App.pages.carsForm.goToStep(step);
};

window.validateCurrentStep = function() {
    return App.pages.carsForm.validateStep();
}; 
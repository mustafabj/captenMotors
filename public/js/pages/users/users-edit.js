/**
 * Users Edit Page JavaScript
 * Handles user editing form
 */

App.pages.usersEdit = {
    // Private variables
    _form: null,
    _userId: null,

    // Initialize the form
    init: function() {
        this._form = document.getElementById('user-form');
        
        // Only initialize if we're on the user edit page
        if (!this._form) {
            return;
        }

        // Get user ID from form action or data attribute
        this._userId = this._form.getAttribute('data-user-id') || 
                      this._form.action.split('/').pop();

        this._setupForm();
        this._bindEvents();
    },

    // Setup form functionality
    _setupForm: function() {
        // Clear any saved form data
        if (App.utils.storage) {
            App.utils.storage.remove('user-edit-form');
        }
    },

    // Bind form events
    _bindEvents: function() {
        // Handle form submission
        this._form.addEventListener('submit', (e) => this._handleFormSubmit(e));
        
        // Clear errors when user starts typing
        this._form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                if (input.classList.contains('border-red-500')) {
                    input.classList.remove('border-red-500', 'bg-red-50');
                    const errorMessage = input.parentNode.querySelector(`[data-field="${input.name}"].field-error`);
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });
        });
    },

    // Handle form submission
    _handleFormSubmit: function(e) {
        e.preventDefault();

        // Show button loading state
        this._showButtonLoading();

        // Submit form via AJAX
        const formData = new FormData(this._form);
        
        fetch(this._form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            this._hideButtonLoading();
            if (data.success) {
                this._handleSuccess(data, this._form);
            } else {
                this._handleError({ status: 422, errors: data.errors }, this._form);
            }
        })
        .catch(error => {
            this._hideButtonLoading();
            this._handleError(error, this._form);
        });
    },

    // Handle successful submission
    _handleSuccess: function(data, form) {
        // Show success message
        this._showNotification('User updated successfully!', 'success');
        
        // Redirect to users index
        setTimeout(() => {
            window.location.href = '/users';
        }, 1000);
    },

    // Handle submission errors
    _handleError: function(error, form) {
        if (error.status === 422) {
            // Display validation errors
            this._displayValidationErrors(error.errors);
            this._showNotification('Please correct the errors below.', 'error');
        } else {
            this._showNotification('An error occurred while updating the user.', 'error');
        }
    },

    // Display validation errors on form fields
    _displayValidationErrors: function(errors) {
        // Clear previous errors
        this._clearValidationErrors();
        
        // Display new errors
        Object.keys(errors).forEach(field => {
            const input = this._form.querySelector(`[name="${field}"]`);
            if (input) {
                // Remove success state
                input.classList.remove('border-green-500', 'bg-green-50');
                // Add error class to input
                input.classList.add('border-red-500', 'bg-red-50');
                
                // Create error message element with same styling as cars form
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error text-red-600 text-sm mt-1 flex items-center animate-slide-in mt-2';
                errorDiv.setAttribute('data-field', field);
                errorDiv.setAttribute('data-message-type', 'error');
                errorDiv.innerHTML = `
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    ${errors[field][0]}
                `;
                
                // Insert after input
                input.parentNode.appendChild(errorDiv);
            }
        });
    },

    // Clear validation errors
    _clearValidationErrors: function() {
        // Remove error classes from inputs
        this._form.querySelectorAll('.border-red-500, .bg-red-50').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
        });
        
        // Remove error messages using the same selectors as cars form
        this._form.querySelectorAll('[data-field].field-error, [data-field].field-success').forEach(el => {
            el.remove();
        });
    },

    // Show button loading state
    _showButtonLoading: function() {
        const submitBtn = this._form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            }
        }
    },

    // Hide button loading state
    _hideButtonLoading: function() {
        const submitBtn = this._form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        }
    },

    // Show notification
    _showNotification: function(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification ${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md`;
        
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

    // Get current user ID
    getUserId: function() {
        return this._userId;
    }
};

// Initialize immediately (in case DOM is already ready)
if (document.readyState === 'loading') {
    // DOM is still loading, wait for DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        App.pages.usersEdit.init();
    });
} else {
    // DOM is already ready, initialize immediately
    App.pages.usersEdit.init();
} 
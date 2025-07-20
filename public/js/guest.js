/**
 * Guest Layout JavaScript
 * Minimal setup for authentication pages
 */

// Initialize Alpine.js when it's available
document.addEventListener('DOMContentLoaded', function() {
    if (window.Alpine) {
        // Alpine is already loaded, start it
        window.Alpine.start();
    } else {
        // Wait for Alpine to be loaded from CDN
        window.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized for guest layout');
        });
    }
});

// Set up axios defaults if available
if (window.axios) {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    
    // Add CSRF token to all requests
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
}

// Basic form helpers for guest pages
window.GuestApp = {
    // Show/hide password toggle
    togglePassword: function(inputId, toggleButton) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            input.type = 'password';
            toggleButton.textContent = 'Show';
        }
    },

    // Form submission with loading state
    submitForm: function(form, button) {
        if (button) {
            button.disabled = true;
            button.textContent = 'Please wait...';
        }
        
        // Re-enable button after 5 seconds to prevent permanent disable
        setTimeout(() => {
            if (button) {
                button.disabled = false;
                button.textContent = button.getAttribute('data-original-text') || 'Submit';
            }
        }, 5000);
    },

    // Simple form validation
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Show validation errors
    showError: function(field, message) {
        // Remove existing error
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Add new error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-600 text-sm mt-1';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    },

    // Clear validation errors
    clearErrors: function(form) {
        const errors = form.querySelectorAll('.error-message');
        errors.forEach(error => error.remove());
    }
}; 
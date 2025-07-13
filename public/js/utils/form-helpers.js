/**
 * Form Helper Utilities
 * Common form handling functions
 */

App.utils.forms = {
    /**
     * Serialize form data to JSON
     */
    serializeToJson: function(form) {
        const formData = new FormData(form);
        const json = {};
        
        for (let [key, value] of formData.entries()) {
            if (json[key]) {
                // Handle multiple values (like checkboxes)
                if (Array.isArray(json[key])) {
                    json[key].push(value);
                } else {
                    json[key] = [json[key], value];
                }
            } else {
                json[key] = value;
            }
        }
        
        return json;
    },

    /**
     * Display form validation errors
     */
    displayErrors: function(errors, form) {
        // Clear previous errors
        this.clearErrors(form);
        
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            
            if (input) {
                input.classList.add('error');
                
                // Create error message element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                errorDiv.textContent = messages[0];
                
                // Insert after input
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
        });
    },

    /**
     * Clear form validation errors
     */
    clearErrors: function(form) {
        // Remove error classes
        form.querySelectorAll('.error').forEach(el => {
            el.classList.remove('error');
        });
        
        // Remove error messages
        form.querySelectorAll('.error-message').forEach(el => {
            el.remove();
        });
    },

    /**
     * Submit form via AJAX
     */
    submitAjax: function(form, options = {}) {
        const defaults = {
            method: form.method || 'POST',
            showLoading: true,
            onSuccess: null,
            onError: null,
            onComplete: null
        };
        
        const settings = { ...defaults, ...options };
        
        return new Promise((resolve, reject) => {
            const formData = new FormData(form);
            
            if (settings.showLoading) {
                App.utils.showLoading(form);
            }
            
            App.utils.ajax(form.action, {
                method: settings.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw response;
                }
            })
            .then(data => {
                this.clearErrors(form);
                if (settings.onSuccess) {
                    settings.onSuccess(data, form);
                }
                resolve(data);
            })
            .catch(async (error) => {
                if (error.status === 422) {
                    // Validation errors
                    const data = await error.json();
                    this.displayErrors(data.errors, form);
                } else {
                    console.error('Form submission error:', error);
                }
                
                if (settings.onError) {
                    settings.onError(error, form);
                }
                reject(error);
            })
            .finally(() => {
                if (settings.showLoading) {
                    App.utils.hideLoading(form);
                }
                if (settings.onComplete) {
                    settings.onComplete(form);
                }
            });
        });
    },

    /**
     * Auto-save form data to localStorage
     */
    autoSave: function(form, key) {
        const saveData = () => {
            const data = this.serializeToJson(form);
            App.utils.storage.set(key, JSON.stringify(data));
        };
        
        // Save on input change
        form.addEventListener('input', App.utils.debounce(saveData, 1000));
        form.addEventListener('change', saveData);
    },

    /**
     * Restore form data from localStorage
     */
    restoreData: function(form, key) {
        const savedData = App.utils.storage.get(key);
        if (!savedData) return;
        
        try {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input && input.type !== 'file') {
                    input.value = data[field];
                }
            });
        } catch (e) {
            console.warn('Failed to restore form data:', e);
        }
    },

    /**
     * Show button loading state
     */
    showButtonLoading: function(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            }
        }
    },

    /**
     * Hide button loading state
     */
    hideButtonLoading: function(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        }
    }
}; 
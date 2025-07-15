/**
 * Main Application JavaScript
 * Common functionality shared across all pages
 */

window.App = {
    // Configuration
    config: {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        baseUrl: window.location.origin,
        debug: false
    },

    // Utility functions
    utils: {
        /**
         * Make AJAX requests with proper headers
         */
        ajax: function(url, options = {}) {
            const defaults = {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            };

            // Add CSRF token for non-GET requests
            if (options.method && options.method.toUpperCase() !== 'GET') {
                defaults.headers['X-CSRF-TOKEN'] = App.config.csrfToken;
            }

            return fetch(url, { ...defaults, ...options });
        },

        /**
         * Debounce function for performance
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Show loading state
         */
        showLoading: function(element) {
            if (element) {
                element.style.pointerEvents = 'none';
            }
        },

        /**
         * Hide loading state
         */
        hideLoading: function(element) {
            if (element) {
                element.style.pointerEvents = 'auto';
            }
        },

        /**
         * Local storage helper
         */
        storage: {
            get: function(key) {
                try {
                    return localStorage.getItem(key);
                } catch (e) {
                    console.warn('localStorage not available');
                    return null;
                }
            },
            set: function(key, value) {
                try {
                    localStorage.setItem(key, value);
                } catch (e) {
                    console.warn('localStorage not available');
                }
            },
            remove: function(key) {
                try {
                    localStorage.removeItem(key);
                } catch (e) {
                    console.warn('localStorage not available');
                }
            }
        }
    },

    // Page-specific modules
    pages: {},

    // Components
    components: {},

    // Initialize app
    init: function() {
        this.bindCommonEvents();
        
        // Initialize loader after app is ready
        if (window.App && App.config && App.config.loader) {
            App.config.loader.init();
        }
    },

    // Bind common events
    bindCommonEvents: function() {
        // Common dropdown toggles, modals, etc.
        // Add any global event listeners here
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    App.init();
});

// Export for modules
window.App = App; 
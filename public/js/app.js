/**
 * Main Application JavaScript
 * Common functionality shared across all pages
 */

window.App = {
    // Configuration
    config: {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        baseUrl: window.location.origin,
        debug: false,
        loader: null // Will be set by loader.js
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
        },

        /**
         * Show toast notification
         */
        showToast: function(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'success' ? 'bg-green-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 5000);
        },

        /**
         * Format date for notifications
         */
        formatDate: function(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) {
                return 'Just now';
            } else if (diffInMinutes < 60) {
                return `${diffInMinutes}m ago`;
            } else if (diffInMinutes < 1440) {
                const hours = Math.floor(diffInMinutes / 60);
                return `${hours}h ago`;
            } else {
                return date.toLocaleDateString();
            }
        }
    },

    // Page-specific modules
    pages: {},

    // Components
    components: {
        notifications: null
    },

    // Initialize app
    init: function() {
        this.bindCommonEvents();
        this.initComponents();
        
        // Initialize loader after app is ready
        if (this.config && this.config.loader) {
            this.config.loader.init();
        }
    },

    // Initialize components
    initComponents: function() {
        // Initialize notifications component if it exists
        if (typeof NotificationsComponent !== 'undefined') {
            this.components.notifications = new NotificationsComponent();
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
/**
 * JavaScript File Loader Configuration
 * Defines which JS files to load for specific pages or routes
 */

App.config.loader = {
    // Define page-specific JS files
    pages: {
        'cars.index': ['pages/cars-index.js'],
        'cars.create': ['pages/cars-form.js', 'utils/form-helpers.js'],
        'cars.edit': ['pages/cars-form.js', 'utils/form-helpers.js'],
        'cars.show': ['pages/cars-show.js'],
        'users.index': ['pages/users/users-index.js'],
        'users.create': ['pages/users/users-create.js', 'utils/form-helpers.js'],
        'users.edit': ['pages/users/users-edit.js', 'utils/form-helpers.js'],
        'bulk-deals.index': ['pages/bulk-deals-index.js'],
        'bulk-deals.create': ['pages/bulk-deals-form.js', 'utils/form-helpers.js'],
        'dashboard': ['pages/dashboard.js'],
        'profile.edit': ['pages/profile-form.js', 'utils/form-helpers.js']
    },

    // Define component JS files (always loaded)
    components: [
        'components/modal.js',
        'components/dropdown.js',
        'components/toast.js'
    ],

    // Define utility JS files (loaded when needed)
    utils: [
        'utils/form-helpers.js',
        'utils/date-helpers.js',
        'utils/validation.js'
    ],

                // Load page-specific files
            loadPageFiles: function(routeName) {
                const files = this.pages[routeName];
                if (!files) {
                    console.warn(`No JS files defined for route: ${routeName}`);
                    return;
                }

                files.forEach(file => {
                    // Use global version from Laravel config instead of individual file versions
                    const globalVersion = document.body.getAttribute('data-asset-version') || '1.0.0';
                    const versionedPath = `${file}?v=${globalVersion}`;
                    this.loadScript(`/js/${versionedPath}`);
                });
            },

    // Load a script dynamically
    loadScript: function(src) {
        return new Promise((resolve, reject) => {
            // Check if script already exists
            if (document.querySelector(`script[src="${src}"]`)) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    },

    // Load multiple scripts
    loadScripts: function(scripts) {
        return Promise.all(scripts.map(script => this.loadScript(script)));
    },

    // Get current route name (to be implemented based on your routing)
    getCurrentRoute: function() {
        // This should be set by your Laravel blade template
        return document.body.getAttribute('data-route') || 'unknown';
    },

    // Initialize loader
    init: function() {
        const currentRoute = this.getCurrentRoute();
        
        // Load page-specific files
        this.loadPageFiles(currentRoute);
    }
};

// Note: Loader is initialized by App.init() in app.js 
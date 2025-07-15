/**
 * Asset Version Configuration
 * Manages version numbers for cache busting
 */

App.config.version = {
    // Current version - update this when you deploy new assets
    current: '1.0.0',
    
    // Asset versions - can be overridden for specific files
    assets: {
        // Core files
        'app.js': '1.0.0',
        'components/modal.js': '1.0.0',
        
        // User pages
        'pages/users/users-index.js': '1.0.0',
        'pages/users/users-create.js': '1.0.0',
        'pages/users/users-edit.js': '1.0.0',
        
        // Car pages
        'pages/cars-index.js': '1.0.0',
        'pages/cars-form.js': '1.0.0',
        'pages/cars-show.js': '1.0.0',
        
        // Utils
        'utils/form-helpers.js': '1.0.0',
        'utils/date-helpers.js': '1.0.0',
        'utils/validation.js': '1.0.0',
        
        // Config
        'config/loader.js': '1.0.0',
        'config/version.js': '1.0.0'
    },

    /**
     * Get version for a specific asset
     */
    getVersion: function(assetPath) {
        return this.assets[assetPath] || this.current;
    },

    /**
     * Add version to asset URL
     */
    addVersion: function(assetPath) {
        // Always use global version for consistency
        return `${assetPath}?v=${this.current}`;
    },

    /**
     * Update version for specific assets
     */
    updateVersion: function(assetPath, newVersion) {
        this.assets[assetPath] = newVersion;
    },

    /**
     * Update all versions to a new version
     */
    updateAllVersions: function(newVersion) {
        this.current = newVersion;
        Object.keys(this.assets).forEach(asset => {
            this.assets[asset] = newVersion;
        });
    },

    /**
     * Generate version from timestamp (for development)
     */
    generateTimestampVersion: function() {
        return Date.now().toString();
    }
}; 
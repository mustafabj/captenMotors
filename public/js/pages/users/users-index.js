/**
 * Users Index Page JavaScript
 * Handles search, filtering, and pagination for the users listing
 */

App.pages.usersIndex = {
    searchInput: null,
    roleFilter: null,
    usersContainer: null,
    paginationContainer: null,
    searchUrl: null,
    debounceTimer: null,
    currentPage: 1,

    init() {
        this.searchInput = document.getElementById('search-input');
        this.roleFilter = document.getElementById('role-filter');
        this.usersContainer = document.getElementById('users-container');
        this.paginationContainer = document.getElementById('pagination-container');
        
        // Get search URL from data attribute or fallback to current page
        this.searchUrl = this.usersContainer?.dataset.searchUrl || window.location.pathname;
        
        this.bindEvents();
        this.setupSearch();
    },

    bindEvents() {
        // Search input event
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                this.debounceSearch();
            });
        }

        // Role filter event
        if (this.roleFilter) {
            this.roleFilter.addEventListener('change', () => {
                this.performSearch();
            });
        }

        // Pagination clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-page]')) {
                e.preventDefault();
                this.currentPage = parseInt(e.target.dataset.page);
                this.performSearch();
            }
        });
    },

    setupSearch() {
        // Add loading state to search input
        if (this.searchInput) {
            this.searchInput.addEventListener('focus', () => {
                this.searchInput.classList.add('loading');
            });
        }
    },

    debounceSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.performSearch();
        }, 300);
    },

    async performSearch() {
        try {
            this.showLoading();
            
            const params = new URLSearchParams();
            
            if (this.searchInput && this.searchInput.value.trim()) {
                params.append('search', this.searchInput.value.trim());
            }
            
            if (this.roleFilter && this.roleFilter.value) {
                params.append('role', this.roleFilter.value);
            }
            
            if (this.currentPage > 1) {
                params.append('page', this.currentPage);
            }

            // Reset to page 1 when searching
            if (this.searchInput?.value.trim() || this.roleFilter?.value) {
                this.currentPage = 1;
                params.set('page', '1');
            }

            const url = `${this.searchUrl}?${params.toString()}`;
    

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`Search request failed: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();
            
            this.updateResults(data);
            
        } catch (error) {
            console.error('Search error:', error);
            this.showError('An error occurred while searching. Please try again.');
        } finally {
            this.hideLoading();
        }
    },

    updateResults(data) {
        if (data.html && this.usersContainer) {
            this.usersContainer.innerHTML = data.html;
        }
        
        if (data.pagination && this.paginationContainer) {
            this.paginationContainer.innerHTML = data.pagination;
        }
    },

    showLoading() {
        if (this.usersContainer) {
            this.usersContainer.style.pointerEvents = 'none';
        }
        
        if (this.searchInput) {
            this.searchInput.classList.add('loading');
        }
    },

    hideLoading() {
        if (this.usersContainer) {
            this.usersContainer.style.pointerEvents = 'auto';
        }
        
        if (this.searchInput) {
            this.searchInput.classList.remove('loading');
        }
    },

    showError(message) {
        // You can implement a toast notification here
        console.error(message);
        
        // Show a simple alert for now
        if (typeof alert !== 'undefined') {
            alert(message);
        }
    },

    // User management functions
    async deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user? This action can be undone.')) {
            return;
        }

        try {
            const response = await App.utils.ajax(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('User deleted successfully');
                this.performSearch(); // Refresh the list
            } else {
                this.showError('Error deleting user: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showError('Error deleting user');
        }
    },

    async restoreUser(userId) {
        if (!confirm('Are you sure you want to restore this user?')) {
            return;
        }

        try {
            const response = await App.utils.ajax(`/users/${userId}/restore`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('User restored successfully');
                this.performSearch(); // Refresh the list
            } else {
                this.showError('Error restoring user: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error restoring user:', error);
            this.showError('Error restoring user');
        }
    },

    async forceDeleteUser(userId) {
        if (!confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await App.utils.ajax(`/users/${userId}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('User permanently deleted');
                this.performSearch(); // Refresh the list
            } else {
                this.showError('Error permanently deleting user: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error permanently deleting user:', error);
            this.showError('Error permanently deleting user');
        }
    },

    showSuccess(message) {
        // You can implement a toast notification here

        
        // Show a simple alert for now
        if (typeof alert !== 'undefined') {
            alert(message);
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (typeof App !== 'undefined' && App.pages && App.pages.usersIndex) {
        App.pages.usersIndex.init();
    }
});

// Global functions for inline event handlers
window.deleteUser = function(userId) {
    App.pages.usersIndex.deleteUser(userId);
};

window.restoreUser = function(userId) {
    App.pages.usersIndex.restoreUser(userId);
};

window.forceDeleteUser = function(userId) {
    App.pages.usersIndex.forceDeleteUser(userId);
}; 
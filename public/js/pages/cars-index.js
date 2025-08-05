/**
 * Cars Index Page JavaScript
 * Handles search, filtering, and pagination for the cars index page
 */

App.pages.carsIndex = {
    // Private variables
    _searchTimeout: null,
    _currentPage: 1,
    _container: null,
    _searchUrl: null,

    // Initialize the page
    init: function() {
        this._container = document.getElementById('cars-container');
        
        // Only initialize if we're on the cars index page
        if (!this._container) {
            return;
        }

        this._searchUrl = this._container.getAttribute('data-search-url');
        this._bindEvents();
        this._loadSavedView();
        this._attachPaginationHandlers();
    },

    // Bind page events
    _bindEvents: function() {
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const yearFilter = document.getElementById('year-filter');
        const perPageFilter = document.getElementById('per-page-filter');
        
        if (searchInput) {
            // Search input with debounce
            searchInput.addEventListener('input', App.utils.debounce(() => {
                this._currentPage = 1;
                this._performSearch(this._currentPage);
            }, 300));
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this._currentPage = 1;
                this._performSearch(this._currentPage);
            });
        }
        
        if (yearFilter) {
            yearFilter.addEventListener('change', () => {
                this._currentPage = 1;
                this._performSearch(this._currentPage);
            });
        }
        
        if (perPageFilter) {
            perPageFilter.addEventListener('change', () => {
                this._currentPage = 1;
                // Save the per_page preference
                App.utils.storage.set('carPerPage', perPageFilter.value);
                this._performSearch(this._currentPage);
            });
        }
    },

    // Perform search
    _performSearch: function(page = 1) {
        const search = document.getElementById('search-input').value;
        const status = document.getElementById('status-filter').value;
        const year = document.getElementById('year-filter').value;
        const perPage = document.getElementById('per-page-filter').value;
        
        App.utils.showLoading(this._container);
        
        const params = new URLSearchParams({
            search: search,
            status: status,
            year: year,
            per_page: perPage,
            page: page
        });

        App.utils.ajax(`${this._searchUrl}?${params}`)
            .then(response => response.json())
            .then(data => {
                this._container.innerHTML = data.html;
                document.getElementById('pagination-container').innerHTML = data.pagination;
                
                // Reapply view preference
                const savedView = App.utils.storage.get('carViewType') || 'grid';
                this._switchView(savedView);
                
                // Reattach pagination click handlers
                this._attachPaginationHandlers();
                
                // Reinitialize KT components for the new content
                this._reinitializeComponents();
            })
            .catch(error => {
                console.error('Search error:', error);
            })
            .finally(() => {
                App.utils.hideLoading(this._container);
            });
    },

    // Attach pagination handlers
    _attachPaginationHandlers: function() {
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = link.getAttribute('data-page');
                if (page && page !== 'null') {
                    this._currentPage = parseInt(page);
                    this._performSearch(this._currentPage);
                }
            });
        });
    },

    // Switch between grid and list view
    _switchView: function(viewType) {
        const gridView = document.getElementById('grid-view');
        const listView = document.getElementById('list-view');
        const gridBtn = document.getElementById('grid-view-btn');
        const listBtn = document.getElementById('list-view-btn');

        if (gridView && listView) {
            if (viewType === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                gridBtn?.classList.add('active');
                listBtn?.classList.remove('active');
                App.utils.storage.set('carViewType', 'grid');
            } else {
                listView.classList.remove('hidden');
                gridView.classList.add('hidden');
                listBtn?.classList.add('active');
                gridBtn?.classList.remove('active');
                App.utils.storage.set('carViewType', 'list');
            }
        }
    },

    // Load saved view preference
    _loadSavedView: function() {
        const savedView = App.utils.storage.get('carViewType') || 'grid';
        this._switchView(savedView);
        
        // Load saved per_page preference
        const savedPerPage = App.utils.storage.get('carPerPage');
        if (savedPerPage) {
            const perPageFilter = document.getElementById('per-page-filter');
            if (perPageFilter) {
                perPageFilter.value = savedPerPage;
            }
        }
    },

    // Reinitialize KT components after content update
    _reinitializeComponents: function() {
        // Reinitialize dropdowns
        if (typeof KTApp !== 'undefined' && KTApp.initDropdown) {
            KTApp.initDropdown();
        }
        
        // Reinitialize selects
        if (typeof KTApp !== 'undefined' && KTApp.initSelect) {
            KTApp.initSelect();
        }
    },

    // Public methods
    switchView: function(viewType) {
        this._switchView(viewType);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    App.pages.carsIndex.init();
});

// Make view switcher globally accessible for onclick handlers
window.switchView = function(viewType) {
    App.pages.carsIndex.switchView(viewType);
}; 
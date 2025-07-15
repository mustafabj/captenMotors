/**
 * Modal Component
 * Reusable modal functionality that can be used across different pages
 */

App.components.modal = {
    // Private variables
    _activeModal: null,
    _openModals: [],

    // Initialize all modals
    init: function() {
        this._bindEvents();
    },

    // Bind modal events
    _bindEvents: function() {
        // Listen for modal triggers
        document.addEventListener('click', (e) => {
            if (e.target.hasAttribute('data-modal-trigger')) {
                e.preventDefault();
                const modalId = e.target.getAttribute('data-modal-trigger');
                this.open(modalId);
            }
            
            if (e.target.hasAttribute('data-modal-close')) {
                e.preventDefault();
                this.close();
            }
        });

        // Close modal on overlay click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                this.close();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this._activeModal) {
                this.close();
            }
        });
    },

    // Open modal
    open: function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.warn(`Modal with ID "${modalId}" not found`);
            return;
        }

        // Close any existing modal
        this.close();

        // Show modal
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.classList.add('modal-open');
        
        this._activeModal = modal;
        this._openModals.push(modal);

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:opened', {
            detail: { modalId: modalId }
        }));
    },

    // Close modal
    close: function() {
        if (!this._activeModal) {
            return;
        }

        const modal = this._activeModal;
        const modalId = modal.id;

        // Hide modal
        modal.style.display = 'none';
        modal.classList.remove('active');
        document.body.classList.remove('modal-open');

        // Remove from open modals
        this._openModals = this._openModals.filter(m => m !== modal);
        this._activeModal = this._openModals.length > 0 ? this._openModals[this._openModals.length - 1] : null;

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:closed', {
            detail: { modalId: modalId }
        }));
    },

    // Check if modal is open
    isOpen: function(modalId) {
        const modal = document.getElementById(modalId);
        return modal && modal.classList.contains('active');
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    App.components.modal.init();
}); 
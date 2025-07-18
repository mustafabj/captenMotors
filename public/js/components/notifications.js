/**
 * Real-time Notifications Component with Pusher
 * Handles notification display and interactions with real-time updates
 */
window.NotificationsComponent = class NotificationsComponent {
    constructor() {
        // Prevent multiple initializations
        if (window.notificationsComponentInstance) {
            return window.notificationsComponentInstance;
        }
        
        this.notificationsList = document.querySelector('#notifications-list');
        this.notificationCount = document.querySelector('#notification-count');
        this.markAllReadBtn = document.querySelector('#mark-all-read-btn');
        this.notificationDrawer = document.querySelector('#notifications_drawer');
        
        // Pusher configuration
        this.pusher = null;
        this.channel = null;
        this.userId = null;
        
        // Pagination configuration
        this.currentPage = 1;
        this.perPage = 15;
        this.hasMoreNotifications = true;
        this.isLoading = false;
        
        this.init();
        
        // Store instance to prevent multiple initializations
        window.notificationsComponentInstance = this;
    }

    init() {
        if (!this.notificationsList) return;
        
        this.setupEventListeners();
        this.loadNotifications();
        this.updateNotificationCount();
        this.initPusher();
        this.setupScrollListener();
    }

    setupEventListeners() {
        // Mark all as read button
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Notification drawer toggle
        const notificationToggle = document.querySelector('[data-kt-drawer-toggle="#notifications_drawer"]');
        if (notificationToggle) {
            notificationToggle.addEventListener('click', () => {
                this.loadNotifications();
            });
        }

        // Delegate event listeners for dynamic content
        if (this.notificationsList) {
            this.notificationsList.addEventListener('click', (e) => {
                // Approve cost button
                if (e.target.closest('.approve-cost-btn')) {
                    const costId = e.target.closest('.approve-cost-btn').dataset.costId;
                    this.approveEquipmentCost(costId);
                }
                
                // Reject cost button
                if (e.target.closest('.reject-cost-btn')) {
                    const costId = e.target.closest('.reject-cost-btn').dataset.costId;
                    this.rejectEquipmentCost(costId);
                }
                
                // Transfer cost button
                if (e.target.closest('.transfer-cost-btn')) {
                    const costId = e.target.closest('.transfer-cost-btn').dataset.costId;
                    this.transferEquipmentCost(costId);
                }
                
                // Mark as read when clicking on notification
                if (e.target.closest('.notification-item')) {
                    const notificationId = e.target.closest('.notification-item').dataset.notificationId;
                    if (notificationId && !notificationId.startsWith('new-')) {
                        this.markAsRead(notificationId);
                    }
                }
            });
        }
    }

    setupScrollListener() {
        if (!this.notificationsList) return;
        
        // Find the scrollable parent container (the div with overflow-y: auto)
        const scrollableContainer = this.notificationsList.closest('[style*="overflow-y: auto"]') || 
                                   this.notificationsList.parentElement;
        
        if (!scrollableContainer) return;
        
        // Add scroll event listener to the scrollable container
        scrollableContainer.addEventListener('scroll', (e) => {
            const { scrollTop, scrollHeight, clientHeight } = e.target;
            
            // Check if we're near the bottom (within 50px)
            if (scrollHeight - scrollTop - clientHeight < 50 && !this.isLoading && this.hasMoreNotifications) {
                this.loadMoreNotifications();
            }
        });
    }

    async loadMoreNotifications() {
        if (this.isLoading || !this.hasMoreNotifications) return;
        
        this.showLoadMoreLoading();
        const nextPage = this.currentPage + 1;
        await this.loadNotifications(nextPage, true);
        this.hideLoadMoreLoading();
    }

    initPusher() {
        // Check if Pusher is available
        if (typeof Pusher === 'undefined') {
            console.warn('Pusher library not loaded. Real-time notifications will not work.');
            this.initFallbackPolling();
            return;
        }

        // Get Pusher configuration from meta tags or window object
        const pusherKey = document.querySelector('meta[name="pusher-key"]')?.getAttribute('content') || 
                         window.PUSHER_KEY || 
                         null;
        
        const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content') || 
                             window.PUSHER_CLUSTER || 
                             'mt1'; // Default cluster
        
        const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || 
                      window.USER_ID;

        if (!pusherKey || pusherKey === 'null' || pusherKey === 'your-pusher-key') {
            console.warn('Pusher not configured. Real-time notifications will not work. Check your .env file.');
            this.initFallbackPolling();
            return;
        }

        if (!userId) {
            console.warn('User ID not found. Real-time notifications will not work.');
            this.initFallbackPolling();
            return;
        }

        this.userId = userId;

        try {
            // Initialize Pusher
            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            });

            // Subscribe to user's private channel
            this.channel = this.pusher.subscribe(`private-user.${userId}.equipment-cost-notifications`);

            // Listen for equipment cost notifications
            this.channel.bind('equipment-cost-notification', (data) => {
                console.log('Received real-time notification:', data);
                this.handleRealTimeNotification(data);
            });

            // Handle connection events
            this.pusher.connection.bind('connected', () => {
                console.log('Connected to Pusher for real-time notifications');
            });

            this.pusher.connection.bind('error', (error) => {
                console.error('Pusher connection error:', error);
                this.initFallbackPolling();
            });

        } catch (error) {
            console.error('Error initializing Pusher:', error);
            this.initFallbackPolling();
        }
    }

    initFallbackPolling() {
        console.log('Using fallback polling for notifications (every 30 seconds)');
        
        // Poll for new notifications every 30 seconds
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    handleRealTimeNotification(data) {
        // Add new notification to the top of the list
        const notificationHTML = this.createNotificationHTML(data.notification);
        
        this.notificationsList.insertAdjacentHTML('afterbegin', notificationHTML);
        
        // Update notification count
        this.updateNotificationCount(this.getCurrentUnreadCount() + 1);
        
        // Show toast notification
        this.showToast(data.notification.message, 'info');
        
        // Play notification sound (optional)
        this.playNotificationSound();
        
        // Re-attach event listeners to the new notification
        this.attachEventListenersToNewNotification();
    }

    attachEventListenersToNewNotification() {
        // Find the newly added notification (first one in the list)
        const newNotification = this.notificationsList.querySelector('.notification-item');
        if (newNotification) {
            // Add click event listener for mark as read
            newNotification.addEventListener('click', (e) => {
                const notificationId = newNotification.dataset.notificationId;
                if (notificationId && !notificationId.startsWith('new-')) {
                    this.markAsRead(notificationId);
                }
            });
        }
    }

    playNotificationSound() {
        // Create a simple notification sound
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    }

    async loadNotifications(page = 1, append = false) {
        if (this.isLoading || (!append && !this.hasMoreNotifications)) return;
        
        try {
            this.isLoading = true;
            
            if (!append) {
                this.showLoading();
                this.currentPage = 1;
            }
            
            const response = await fetch(`/notifications?page=${page}&per_page=${this.perPage}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.notifications) {
                if (append) {
                    this.appendNotifications(data.notifications);
                } else {
                    this.renderNotifications(data.notifications);
                }
                this.updateNotificationCount(data.unread_count);
                
                // Update pagination state
                this.hasMoreNotifications = data.notifications.length === this.perPage;
                this.currentPage = page;
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            if (!append) {
                this.showEmpty();
            }
        } finally {
            this.isLoading = false;
            if (!append) {
                this.hideLoading();
            }
        }
    }

    renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideLoading();
        this.hideEmpty();

        this.notificationsList.innerHTML = notifications.map(notification => 
            this.createNotificationHTML(notification)
        ).join('');
    }

    appendNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            return;
        }

        const notificationsHTML = notifications.map(notification => 
            this.createNotificationHTML(notification)
        ).join('');
        
        this.notificationsList.insertAdjacentHTML('beforeend', notificationsHTML);
        
        // If we got fewer notifications than requested, we've reached the end
        if (notifications.length < this.perPage) {
            this.hasMoreNotifications = false;
            this.showNoMoreNotifications();
        }
    }

    showNoMoreNotifications() {
        const noMoreDiv = document.createElement('div');
        noMoreDiv.className = 'text-center py-4 text-gray-500 text-sm';
        noMoreDiv.innerHTML = 'No more notifications to load';
        noMoreDiv.id = 'no-more-notifications';
        this.notificationsList.appendChild(noMoreDiv);
    }

    createNotificationHTML(notification) {
        // Handle both regular notifications and equipment cost notifications
        const isRead = notification.read_at !== null || notification.status === 'read';
        const readClass = isRead ? 'opacity-60' : '';
        const readIcon = isRead ? '' : '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>';
        
        let actionButtons = '';
        let title = '';
        let message = '';
        
        // Handle equipment cost notifications
        if (notification.notification_type) {
            // This is an equipment cost notification
            title = this.getEquipmentCostNotificationTitle(notification);
            message = notification.message;
            
            // Add action buttons for equipment cost approval requests
            // For real-time notifications, we should show buttons for new approval requests
            const shouldShowButtons = notification.notification_type === 'approval_requested' && 
                                    (notification.status === null || notification.status === 'unread' || !isRead);
            
            if (shouldShowButtons) {
                actionButtons = `
                    <div class="flex gap-2 mt-2">
                        <button class="kt-btn kt-btn-sm kt-btn-success approve-cost-btn" 
                                data-cost-id="${notification.car_equipment_cost_id}">
                            <i class="ki-filled ki-check"></i>
                            Approve
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-danger reject-cost-btn" 
                                data-cost-id="${notification.car_equipment_cost_id}">
                            <i class="ki-filled ki-cross"></i>
                            Reject
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-warning transfer-cost-btn" 
                                data-cost-id="${notification.car_equipment_cost_id}">
                            <i class="ki-filled ki-arrow-right"></i>
                            Transfer
                        </button>
                    </div>
                `;
            }
        } else {
            // This is a regular notification
            title = notification.title;
            message = notification.message;
            
            // Add action buttons for equipment cost approval requests (old format)
            if (notification.type === 'equipment_cost_approval_requested' && !isRead) {
                actionButtons = `
                    <div class="flex gap-2 mt-2">
                        <button class="kt-btn kt-btn-sm kt-btn-success approve-cost-btn" 
                                data-cost-id="${notification.data?.cost_id}">
                            <i class="ki-filled ki-check"></i>
                            Approve
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-danger reject-cost-btn" 
                                data-cost-id="${notification.data?.cost_id}">
                            <i class="ki-filled ki-cross"></i>
                            Reject
                        </button>
                    </div>
                `;
            }
        }

        return `
            <div class="notification-item p-3 border border-gray-200 rounded-lg ${readClass}" 
                 data-notification-id="${notification.id}">
                <div class="flex items-start gap-3">
                    ${readIcon}
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">${title}</h4>
                            <span class="text-xs text-gray-500">${this.formatDate(notification.created_at)}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${message}</p>
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
    }

    getEquipmentCostNotificationTitle(notification) {
        switch (notification.notification_type) {
            case 'approval_requested':
                return 'Equipment Cost Approval Requested';
            case 'approved':
                return 'Equipment Cost Approved';
            case 'rejected':
                return 'Equipment Cost Rejected';
            case 'transferred':
                return 'Equipment Cost Transferred';
            default:
                return 'Equipment Cost Notification';
        }
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationCount(data.unread_count);
                this.markNotificationAsRead(notificationId);
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationCount(0);
                this.markAllNotificationsAsRead();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    async approveEquipmentCost(costId) {
        try {
            const response = await fetch(`/equipment-cost-notifications/equipment-cost/${costId}/approve`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('Equipment cost approved successfully!', 'success');
                this.loadNotifications(); // Reload to update the list
            } else {
                this.showToast(data.message || 'Error approving equipment cost', 'error');
            }
        } catch (error) {
            console.error('Error approving equipment cost:', error);
            this.showToast('Error approving equipment cost', 'error');
        }
    }

    async rejectEquipmentCost(costId) {
        const reason = prompt('Please provide a reason for rejection:');
        if (!reason) return;

        try {
            const response = await fetch(`/equipment-cost-notifications/equipment-cost/${costId}/reject`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('Equipment cost rejected successfully!', 'success');
                this.loadNotifications(); // Reload to update the list
            } else {
                this.showToast(data.message || 'Error rejecting equipment cost', 'error');
            }
        } catch (error) {
            console.error('Error rejecting equipment cost:', error);
            this.showToast('Error rejecting equipment cost', 'error');
        }
    }

    async transferEquipmentCost(costId) {
        const category = prompt('Please select a category (maintenance, repair, insurance, registration, fuel, other):');
        if (!category) return;

        const validCategories = ['maintenance', 'repair', 'insurance', 'registration', 'fuel', 'other'];
        if (!validCategories.includes(category.toLowerCase())) {
            this.showToast('Invalid category. Please select from: maintenance, repair, insurance, registration, fuel, other', 'error');
            return;
        }

        const notes = prompt('Please provide notes (optional):');

        try {
            const response = await fetch(`/other-costs/transfer-from-equipment-cost/${costId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    category: category.toLowerCase(),
                    notes: notes || null
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('Equipment cost transferred to other costs successfully!', 'success');
                this.loadNotifications(); // Reload to update the list
            } else {
                this.showToast(data.message || 'Error transferring equipment cost', 'error');
            }
        } catch (error) {
            console.error('Error transferring equipment cost:', error);
            this.showToast('Error transferring equipment cost', 'error');
        }
    }

    handleNewNotification(data) {
        // Add new notification to the top of the list
        const notificationHTML = this.createNotificationHTML(data);
        this.notificationsList.insertAdjacentHTML('afterbegin', notificationHTML);
        
        // Update notification count
        this.updateNotificationCount(this.getCurrentUnreadCount() + 1);
        
        // Show toast notification
        this.showToast(data.message, 'info');
    }

    updateNotificationCount(count) {
        if (this.notificationCount) {
            this.notificationCount.textContent = count;
            this.notificationCount.style.display = count > 0 ? 'block' : 'none';
        }
    }

    getCurrentUnreadCount() {
        return parseInt(this.notificationCount?.textContent || '0');
    }

    markNotificationAsRead(notificationId) {
        const notification = this.notificationsList.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notification) {
            notification.classList.add('opacity-60');
            const readIcon = notification.querySelector('.w-2.h-2.bg-blue-500');
            if (readIcon) {
                readIcon.remove();
            }
        }
    }

    markAllNotificationsAsRead() {
        const notifications = this.notificationsList.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
            notification.classList.add('opacity-60');
            const readIcon = notification.querySelector('.w-2.h-2.bg-blue-500');
            if (readIcon) {
                readIcon.remove();
            }
        });
    }

    showLoading() {
        if (this.notificationsList) {
            this.notificationsList.innerHTML = '<div class="text-center py-8 text-gray-600">Loading...</div>';
        }
    }

    showLoadMoreLoading() {
        if (this.notificationsList) {
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'load-more-loading';
            loadingDiv.className = 'text-center py-4 text-gray-600';
            loadingDiv.innerHTML = 'Loading...';
            this.notificationsList.appendChild(loadingDiv);
        }
    }

    hideLoadMoreLoading() {
        const loadingDiv = this.notificationsList?.querySelector('#load-more-loading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    hideLoading() {
        // Loading is hidden when content is rendered
    }

    showEmpty() {
        if (this.notificationsList) {
            this.notificationsList.innerHTML = `
                <div class="text-center py-12">
                    <i class="ki-filled ki-information-5 text-4xl text-gray-400 mb-4"></i>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">No notifications</h4>
                    <p class="text-gray-600">You don't have any notifications yet.</p>
                </div>
            `;
        }
    }

    hideEmpty() {
        // Empty state is hidden when content is rendered
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
        if (diffInMinutes < 43200) return `${Math.floor(diffInMinutes / 1440)}d ago`;
        
        return date.toLocaleDateString();
    }

    showToast(message, type = 'primary') {
        // Map our types to KTToast variants
        let variant = 'primary';
        switch (type) {
            case 'success':
                variant = 'success';
                break;
            case 'error':
                variant = 'destructive';
                break;
            case 'warning':
                variant = 'warning';
                break;
            default:
                variant = 'primary';
        }
        
        // Use KTToast if available, fallback to console
        if (typeof KTToast !== 'undefined') {
            KTToast.show({
                message: message,
                variant: variant,
            });
        } else {
            console.log(`[${variant.toUpperCase()}] ${message}`);
        }
    }
};

 
/**
 * Notifications Component
 * Handles real-time notifications using Pusher
 */
window.NotificationsComponent = class NotificationsComponent {
    constructor() {
        this.notificationsList = document.getElementById('notifications-list');
        this.notificationsLoading = document.getElementById('notifications-loading');
        this.notificationsEmpty = document.getElementById('notifications-empty');
        this.notificationCount = document.getElementById('notification-count');
        this.markAllReadBtn = document.getElementById('mark-all-read-btn');
        this.pusher = null;
        this.channel = null;
        
        this.init();
    }

    init() {
        this.loadNotifications();
        this.setupEventListeners();
        this.initPusher();
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

    initPusher() {
        // Initialize Pusher if available
        if (typeof Pusher !== 'undefined') {
            // Get Pusher configuration from meta tags or use defaults
            const pusherKey = document.querySelector('meta[name="pusher-key"]')?.content || 'your-pusher-key';
            const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || 'mt1';
            
            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            });

            // Subscribe to user's private channel
            const userId = document.querySelector('meta[name="user-id"]')?.content;
            if (userId) {
                try {
                    this.channel = this.pusher.subscribe(`private-notifications.${userId}`);
                    
                    // Listen for equipment cost approval requests
                    this.channel.bind('equipment.cost.approval.requested', (data) => {
                        console.log('Received equipment cost approval request:', data);
                        this.handleNewNotification(data);
                    });

                    // Listen for equipment cost approved
                    this.channel.bind('equipment.cost.approved', (data) => {
                        console.log('Received equipment cost approved:', data);
                        this.handleNewNotification(data);
                    });

                    // Listen for equipment cost rejected
                    this.channel.bind('equipment.cost.rejected', (data) => {
                        console.log('Received equipment cost rejected:', data);
                        this.handleNewNotification(data);
                    });

                    console.log('Pusher initialized successfully for user:', userId);
                } catch (error) {
                    console.error('Error subscribing to Pusher channel:', error);
                }
            } else {
                console.warn('No user ID found for Pusher subscription');
            }
        } else {
            console.warn('Pusher not available');
        }
    }

    async loadNotifications() {
        try {
            this.showLoading();
            
            const response = window.App && App.utils && App.utils.ajax ? 
                await App.utils.ajax('/notifications') :
                await fetch('/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

            const data = await response.json();
            
            if (data.notifications) {
                this.renderNotifications(data.notifications);
                this.updateNotificationCount(data.unread_count);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showEmpty();
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

    createNotificationHTML(notification) {
        const isRead = notification.read_at !== null;
        const readClass = isRead ? 'opacity-60' : '';
        const readIcon = isRead ? '' : '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>';
        
        let actionButtons = '';
        
        // Add action buttons for equipment cost approval requests
        if (notification.type === 'equipment_cost_approval_requested' && !isRead) {
            actionButtons = `
                <div class="flex gap-2 mt-2">
                    <button class="kt-btn kt-btn-sm kt-btn-success approve-cost-btn" 
                            data-cost-id="${notification.data.cost_id}">
                        <i class="ki-filled ki-check"></i>
                        Approve
                    </button>
                    <button class="kt-btn kt-btn-sm kt-btn-danger reject-cost-btn" 
                            data-cost-id="${notification.data.cost_id}">
                        <i class="ki-filled ki-cross"></i>
                        Reject
                    </button>
                </div>
            `;
        }

        return `
            <div class="notification-item p-3 border border-gray-200 rounded-lg ${readClass}" 
                 data-notification-id="${notification.id}">
                <div class="flex items-start gap-3">
                    ${readIcon}
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">${notification.title}</h4>
                            <span class="text-xs text-gray-500">${this.formatDate(notification.created_at)}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
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
            const response = await fetch(`/notifications/equipment-cost/${costId}/approve`, {
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
            const response = await fetch(`/notifications/equipment-cost/${costId}/reject`, {
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

    handleNewNotification(data) {
        // Add new notification to the top of the list
        const notificationHTML = this.createNotificationHTML({
            id: 'new-' + Date.now(),
            title: data.title,
            message: data.message,
            type: data.type,
            data: data.data,
            created_at: new Date().toISOString(),
            read_at: null
        });

        this.notificationsList.insertAdjacentHTML('afterbegin', notificationHTML);
        
        // Update notification count
        this.updateNotificationCount(this.getCurrentUnreadCount() + 1);
        
        // Show toast notification
        this.showToast(data.message, 'info');
    }

    updateNotificationCount(count) {
        if (count > 0) {
            this.notificationCount.textContent = count;
            this.notificationCount.classList.remove('hidden');
        } else {
            this.notificationCount.classList.add('hidden');
        }
    }

    getCurrentUnreadCount() {
        const unreadItems = this.notificationsList.querySelectorAll('.notification-item:not(.opacity-60)');
        return unreadItems.length;
    }

    markNotificationAsRead(notificationId) {
        const notificationItem = this.notificationsList.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.classList.add('opacity-60');
            const readIcon = notificationItem.querySelector('.w-2.h-2.bg-blue-500');
            if (readIcon) {
                readIcon.remove();
            }
        }
    }

    markAllNotificationsAsRead() {
        const notificationItems = this.notificationsList.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.classList.add('opacity-60');
            const readIcon = item.querySelector('.w-2.h-2.bg-blue-500');
            if (readIcon) {
                readIcon.remove();
            }
        });
    }

    showLoading() {
        this.notificationsLoading.classList.remove('hidden');
        this.notificationsEmpty.classList.add('hidden');
    }

    hideLoading() {
        this.notificationsLoading.classList.add('hidden');
    }

    showEmpty() {
        this.notificationsEmpty.classList.remove('hidden');
        this.notificationsLoading.classList.add('hidden');
    }

    hideEmpty() {
        this.notificationsEmpty.classList.add('hidden');
    }

    formatDate(dateString) {
        if (window.App && App.utils && App.utils.formatDate) {
            return App.utils.formatDate(dateString);
        } else {
            // Fallback if App is not available
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
    }

    showToast(message, type = 'info') {
        if (window.App && App.utils && App.utils.showToast) {
            App.utils.showToast(message, type);
        } else {
            // Fallback if App is not available
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
        }
    }
}

// The component will be initialized by App.initComponents() 
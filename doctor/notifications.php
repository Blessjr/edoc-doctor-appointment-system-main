<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-bg: #f8f9fc;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .notification-card {
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        
        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .notification-unread {
            background-color: #e8f1ff;
        }
        
        .notification-appointment {
            border-left-color: var(--primary-color);
        }
        
        .notification-prescription {
            border-left-color: var(--info-color);
        }
        
        .notification-medical_note {
            border-left-color: var(--success-color);
        }
        
        .notification-reminder {
            border-left-color: var(--warning-color);
        }
        
        .notification-system {
            border-left-color: var(--secondary-color);
        }
        
        .priority-high {
            border-left-width: 6px;
            border-left-color: var(--danger-color) !important;
        }
        
        .notification-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .badge-priority {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
        
        .filter-active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .mark-all-read {
            cursor: pointer;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>
<body>
    <div class="toast-container"></div>
    
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-gray-800"><i class="fas fa-bell me-2"></i>Notification Center</h1>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" id="markAllRead">
                            <i class="fas fa-check-double me-1"></i> Mark all as read
                        </button>
                        <button class="btn btn-outline-secondary btn-sm ms-2" id="refreshNotifications">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Filter by type:</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">All</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="appointment">Appointment</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="prescription">Prescription</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="medical_note">Medical Note</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="reminder">Reminder</button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="system">System</button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Filter by status:</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary status-filter-btn active" data-status="all">All</button>
                                    <button type="button" class="btn btn-outline-primary status-filter-btn" data-status="unread">Unread</button>
                                    <button type="button" class="btn btn-outline-primary status-filter-btn" data-status="read">Read</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications List -->
                <div class="card shadow">
                    <div class="card-body">
                        <div id="notifications-container">
                            <div class="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading notifications...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let notifications = [];
            let currentUserId = 1; // This should be set based on the logged-in user
            let currentUserType = 'patient'; // This should be set based on the logged-in user

            // Show toast notification
            function showToast(message, type = 'info') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();
                
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.id = toastId;
                
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                
                toastContainer.appendChild(toastEl);
                
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
                
                // Remove toast from DOM after it's hidden
                toastEl.addEventListener('hidden.bs.toast', function() {
                    toastEl.remove();
                });
            }

            // Fetch notifications from the server
            function fetchNotifications() {
                document.getElementById('notifications-container').innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading notifications...</span>
                        </div>
                    </div>
                `;
                
                // In a real implementation, this would be an AJAX call to your server
                fetch('get_notifications.php?user_id=' + currentUserId + '&user_type=' + currentUserType)
                    .then(response => response.json())
                    .then(data => {
                        notifications = data;
                        renderNotifications();
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        document.getElementById('notifications-container').innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h4>Error Loading Notifications</h4>
                                <p>Unable to load notifications. Please try again later.</p>
                                <button class="btn btn-primary mt-2" onclick="fetchNotifications()">Retry</button>
                            </div>
                        `;
                    });
            }

            // Render notifications
            function renderNotifications(filter = 'all', status = 'all') {
                const container = document.getElementById('notifications-container');
                let filteredNotifications = notifications;
                
                // Apply type filter
                if (filter !== 'all') {
                    filteredNotifications = filteredNotifications.filter(notification => notification.type === filter);
                }
                
                // Apply status filter
                if (status !== 'all') {
                    const readStatus = status === 'read' ? 1 : 0;
                    filteredNotifications = filteredNotifications.filter(notification => notification.is_read === readStatus);
                }
                
                // Clear container
                container.innerHTML = '';
                
                // Check if there are notifications to display
                if (filteredNotifications.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h4>No notifications found</h4>
                            <p>There are no notifications matching your current filters.</p>
                        </div>
                    `;
                    return;
                }
                
                // Add notifications to container
                filteredNotifications.forEach(notification => {
                    const notificationElement = createNotificationElement(notification);
                    container.appendChild(notificationElement);
                });
            }
            
            // Create notification HTML element
            function createNotificationElement(notification) {
                const div = document.createElement('div');
                div.className = `notification-card card mb-3 notification-${notification.type} ${notification.priority === 'high' ? 'priority-high' : ''} ${!notification.is_read ? 'notification-unread' : ''}`;
                
                // Format the date
                const date = new Date(notification.created_at);
                const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                div.innerHTML = `
                    <div class="card-body py-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title mb-1">${notification.title}</h5>
                                    <div>
                                        ${notification.priority !== 'low' ? `<span class="badge bg-${notification.priority === 'high' ? 'danger' : 'warning'} badge-priority">${notification.priority}</span>` : ''}
                                        ${!notification.is_read ? '<span class="badge bg-primary badge-priority">Unread</span>' : ''}
                                    </div>
                                </div>
                                <p class="card-text">${notification.message}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="notification-time"><i class="far fa-clock me-1"></i> ${formattedDate}</small>
                                    <div>
                                        ${notification.related_id ? `<button class="btn btn-sm btn-outline-primary view-related" data-id="${notification.related_id}">View Details</button>` : ''}
                                        ${!notification.is_read ? `<button class="btn btn-sm btn-outline-success mark-read" data-id="${notification.notification_id}">Mark as Read</button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                return div;
            }
            
            // Mark a notification as read
            function markNotificationAsRead(notificationId) {
                fetch('mark_notification_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ notification_id: notificationId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update local data
                        const notification = notifications.find(n => n.notification_id == notificationId);
                        if (notification) {
                            notification.is_read = 1;
                        }
                        
                        // Re-render notifications
                        renderNotifications(
                            document.querySelector('.filter-btn.active').getAttribute('data-filter'),
                            document.querySelector('.status-filter-btn.active').getAttribute('data-status')
                        );
                        
                        showToast('Notification marked as read', 'success');
                    } else {
                        showToast('Error marking notification as read: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error marking notification as read', 'danger');
                });
            }
            
            // Mark all notifications as read
            function markAllNotificationsAsRead() {
                const unreadNotifications = notifications.filter(n => !n.is_read);
                
                if (unreadNotifications.length === 0) {
                    showToast('All notifications are already marked as read', 'info');
                    return;
                }
                
                const unreadIds = unreadNotifications.map(n => n.notification_id);
                
                fetch('mark_notifications_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ notification_ids: unreadIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update local data
                        notifications.forEach(notification => {
                            if (!notification.is_read) {
                                notification.is_read = 1;
                            }
                        });
                        
                        // Re-render notifications
                        renderNotifications(
                            document.querySelector('.filter-btn.active').getAttribute('data-filter'),
                            document.querySelector('.status-filter-btn.active').getAttribute('data-status')
                        );
                        
                        showToast(`Marked ${unreadNotifications.length} notifications as read`, 'success');
                    } else {
                        showToast('Error marking notifications as read: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error marking notifications as read', 'danger');
                });
            }
            
            // Filter buttons event listeners
            document.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    const filter = this.getAttribute('data-filter');
                    const statusFilter = document.querySelector('.status-filter-btn.active').getAttribute('data-status');
                    renderNotifications(filter, statusFilter);
                });
            });
            
            // Status filter buttons event listeners
            document.querySelectorAll('.status-filter-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.status-filter-btn').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    const status = this.getAttribute('data-status');
                    const typeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
                    renderNotifications(typeFilter, status);
                });
            });
            
            // Mark all as read button
            document.getElementById('markAllRead').addEventListener('click', function() {
                if (confirm('Are you sure you want to mark all notifications as read?')) {
                    markAllNotificationsAsRead();
                }
            });
            
            // Refresh button
            document.getElementById('refreshNotifications').addEventListener('click', function() {
                fetchNotifications();
                showToast('Refreshing notifications...', 'info');
            });
            
            // Event delegation for dynamic buttons
            document.getElementById('notifications-container').addEventListener('click', function(e) {
                // Mark as read button
                if (e.target.classList.contains('mark-read')) {
                    const id = parseInt(e.target.getAttribute('data-id'));
                    markNotificationAsRead(id);
                }
                
                // View related button
                if (e.target.classList.contains('view-related')) {
                    const relatedId = e.target.getAttribute('data-id');
                    alert(`In a real implementation, this would take you to the details page for ID: ${relatedId}`);
                }
            });
            
            // Initial fetch of notifications
            fetchNotifications();
        });
    </script>
</body>
</html>
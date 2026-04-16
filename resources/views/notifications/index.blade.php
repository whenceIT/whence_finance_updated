@extends('layouts.master')

@section('title')
    My Notifications
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Notifications</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="markAllAsRead()">
                            <i class="fas fa-check"></i> Mark All as Read
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($notifications) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <li class="list-group-item notification-item {{ $notification['read'] ?? false ? '' : 'unread' }}"
                                    onclick="handleNotificationClick('{{ $notification['id'] }}')"
                                    data-type="{{ $notification['type'] }}"
                                    data-link-to="{{ $notification['link_to'] }}"
                                    data-created-date="{{ $notification['created_date'] }}">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon mr-3">
                                            <i class="@php
                                                switch($notification['type']) {
                                                    case 'loan_created':
                                                        echo 'fas fa-money-bill-wave';
                                                        break;
                                                    case 'loan_transaction_approval':
                                                        echo 'fas fa-check-circle';
                                                        break;
                                                    case 'user_anniversary_3_months':
                                                    case 'user_anniversary_6_months':
                                                        echo 'fas fa-birthday-cake';
                                                        break;
                                                    case 'anniversary_summary':
                                                        echo 'fas fa-users';
                                                        break;
                                                    default:
                                                        echo 'fas fa-bell';
                                                        break;
                                                }
                                            @endphp"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="notification-message">
                                                {!! $notification['message'] !!}
                                            </div>
                                            <small class="text-muted notification-time">
                                                {{ $notification['time_ago'] }}
                                            </small>
                                        </div>
                                        @if(!isset($notification['read']) || !$notification['read'])
                                            <span class="badge badge-primary">New</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications</h5>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="notificationDetails">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="markAsReadBtn" onclick="markCurrentNotificationAsRead()">Mark as Read</button>
                <button type="button" class="btn btn-success" id="goToLinkBtn" onclick="goToNotificationLink()">Go to Link</button>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-item {
        cursor: pointer;
        transition: background-color 0.2s;
        border-left: 4px solid transparent;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.unread {
        background-color: #e8f4fd;
        border-left-color: #007bff;
    }

    .notification-icon {
        color: #007bff;
        font-size: 1.2em;
        margin-top: 2px;
    }

    .notification-message {
        margin-bottom: 5px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.875em;
    }

    .notification-detail-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .notification-detail-card code {
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.875em;
    }
</style>

<script>
    let currentNotificationId = null;
    let currentNotificationLink = null;

    function handleNotificationClick(notificationId) {
        // Store current notification details
        currentNotificationId = notificationId;

        // Find the notification data from the DOM (we need to get the details from the clicked element)
        const notificationItem = event.currentTarget;
        const message = notificationItem.querySelector('.notification-message').innerHTML;
        const timeAgo = notificationItem.querySelector('.notification-time').textContent;
        const type = notificationItem.getAttribute('data-type') || 'general';
        const linkTo = notificationItem.getAttribute('data-link-to') || '#';
        const createdDate = notificationItem.getAttribute('data-created-date') || '';

        // Store link for later use
        currentNotificationLink = linkTo;

        // Get icon class
        const iconClass = getIconClass(type);

        // Populate modal with full information
        const detailsHtml = `
            <div class="notification-detail-card">
                <div class="d-flex align-items-start mb-3">
                    <div class="mr-3">
                        <i class="${iconClass}" style="font-size: 2.5em; color: #007bff;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-2"><strong>Type:</strong> ${formatType(type)}</h6>
                        <div class="mb-3">
                            <strong>Message:</strong><br>
                            <div class="mt-2">${message}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Created:</strong><br>
                                <small class="text-muted">${createdDate}</small>
                            </div>
                            <div class="col-md-6">
                                <strong>Time Ago:</strong><br>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                        </div>
                        ${linkTo !== '#' ? `<div class="mt-3"><strong>Link:</strong> <code>${linkTo}</code></div>` : ''}
                    </div>
                </div>
            </div>
        `;

        document.getElementById('notificationDetails').innerHTML = detailsHtml;

        // Show modal
        $('#notificationModal').modal('show');
    }

    function getIconClass(type) {
        switch(type) {
            case 'loan_created':
                return 'fas fa-money-bill-wave';
            case 'loan_transaction_approval':
                return 'fas fa-check-circle';
            case 'user_anniversary_3_months':
            case 'user_anniversary_6_months':
                return 'fas fa-birthday-cake';
            case 'anniversary_summary':
                return 'fas fa-users';
            default:
                return 'fas fa-bell';
        }
    }

    function formatType(type) {
        return type.split('_').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    function markCurrentNotificationAsRead() {
        if (!currentNotificationId) return;

        fetch('/notifications/' + currentNotificationId + '/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI to mark as read
                const notificationItem = document.querySelector(`[onclick*="handleNotificationClick('${currentNotificationId}'"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    const badge = notificationItem.querySelector('.badge');
                    if (badge) badge.remove();
                }
                $('#notificationModal').modal('hide');
            } else {
                alert('Failed to mark notification as read.');
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            alert('Error occurred while marking notification as read.');
        });
    }

    function goToNotificationLink() {
        if (currentNotificationLink) {
            window.location.href = currentNotificationLink;
        }
    }

    function markAllAsRead() {
        if (!confirm('Are you sure you want to mark all notifications as read?')) {
            return;
        }

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    var badge = item.querySelector('.badge');
                    if (badge) badge.remove();
                });
                alert('All notifications marked as read.');
            } else {
                alert('Failed to mark notifications as read.');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            alert('Error occurred while marking notifications as read.');
        });
    }
</script>
@endsection
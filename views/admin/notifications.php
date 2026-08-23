
# views/admin/notifications.php - Send bulk notifications
admin_notifications = '''<?php
/**
 * Admin Bulk Notifications View
 */
$title = 'Send Notifications';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0"><i class="fas fa-bell text-primary me-2"></i>Send Bulk Notifications</h1>
            <p class="text-muted mb-0">Send announcements to all users or specific groups</p>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Compose Notification</h4>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/notifications/send" method="POST" id="notificationForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Audience</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check card p-3 border">
                                        <input class="form-check-input" type="radio" name="target" id="target_all" value="all" checked>
                                        <label class="form-check-label" for="target_all">
                                            <strong>All Users</strong>
                                            <small class="text-muted d-block">Send to every registered user</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check card p-3 border">
                                        <input class="form-check-input" type="radio" name="target" id="target_active" value="active">
                                        <label class="form-check-label" for="target_active">
                                            <strong>Active Users Only</strong>
                                            <small class="text-muted d-block">Exclude suspended accounts</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Notification Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="announcement">Announcement</option>
                                <option value="update">Platform Update</option>
                                <option value="maintenance">Maintenance Notice</option>
                                <option value="feature">New Feature</option>
                                <option value="warning">Important Warning</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                required maxlength="200" placeholder="e.g., New Feature: Video Chat">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Message *</label>
                            <textarea name="message" id="message" rows="5" class="form-control" 
                                required maxlength="1000" placeholder="Write your notification message..."></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Keep it concise and clear.</span>
                                <span id="charCount">0 / 1000</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="link" class="form-label fw-bold">Link <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="url" name="link" id="link" class="form-control" 
                                placeholder="https://example.com/page">
                            <div class="form-text">Users can click this link from the notification.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" 
                                onclick="return confirm('Send this notification to all targeted users?')">
                                <i class="fas fa-paper-plane me-2"></i>Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Notifications</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    <?php foreach ($recentNotifications ?? [] as $notif): ?>
                    <div class="list-group-item py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?= e($notif['title']) ?></h6>
                                <p class="mb-0 small text-muted"><?= e(truncate($notif['message'], 60)) ?></p>
                            </div>
                            <span class="badge bg-secondary"><?= $notif['recipient_count'] ?? 0 ?></span>
                        </div>
                        <small class="text-muted"><?= timeAgo($notif['created_at']) ?></small>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentNotifications ?? [])): ?>
                    <div class="list-group-item text-center text-muted py-4">No notifications sent yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('message').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length + ' / 1000';
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>



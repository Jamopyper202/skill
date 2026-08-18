<?php
/**
 * All Notifications View
 */
$title = 'Notifications';
$activeTab = 'notifications';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><i class="fas fa-bell text-primary me-2"></i>Notifications</h1>
            <p class="text-muted mb-0">All your notifications in one place</p>
        </div>
        <div class="col-md-4 text-md-end">
            <form action="/notifications/mark-all-read" method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-1"></i>Mark All Read
                </button>
            </form>
            <form action="/notifications/clear-all" method="POST" class="d-inline"
                  onsubmit="return confirm('Clear all notifications?')">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-1"></i>Clear All
                </button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <?php if (empty($notifications)): ?>
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                <h3 class="h5 text-muted">No notifications</h3>
                <p class="text-muted">You're all caught up!</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow border-0">
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item d-flex align-items-center py-3 <?= empty($notif['is_read']) ? 'bg-light' : '' ?>">
                    <div class="flex-shrink-0 me-3">
                        <span class="badge bg-<?= $notif['type_color'] ?? 'primary' ?> rounded-circle p-2">
                            <i class="fas fa-<?= $notif['type_icon'] ?? 'bell' ?>"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 <?= empty($notif['is_read']) ? 'fw-bold' : '' ?>"><?= e($notif['message']) ?></p>
                        <small class="text-muted"><?= timeAgo($notif['created_at']) ?></small>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <?php if (!empty($notif['link'])): ?>
                        <a href="<?= e($notif['link']) ?>" class="btn btn-sm btn-outline-primary me-1">View</a>
                        <?php endif; ?>
                        <?php if (empty($notif['is_read'])): ?>
                        <form action="/notifications/mark-read/<?= $notif['id'] ?>" method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-link text-muted" title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        <form action="/notifications/delete/<?= $notif['id'] ?>" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this notification?')">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-link text-danger" title="Delete">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPage ?? 1) == $i ? 'active' : '' ?>">
                    <a class="page-link" href="/notifications?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
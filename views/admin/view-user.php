<?php
/**
 * Admin View Single User
 */
$title = 'User: ' . ($user['name'] ?? 'Unknown');
$activeTab = 'admin';
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
            <li class="breadcrumb-item active"><?= e($user['name'] ?? 'User') ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow border-0 text-center">
                <div class="card-body">
                    <img src="<?= getAvatarUrl($user['avatar'] ?? null) ?>" class="rounded-circle mb-3" width="120" height="120">
                    <h3 class="mb-1"><?= e($user['name'] ?? 'Unknown') ?></h3>
                    <p class="text-muted mb-2">@<?= e($user['username'] ?? 'unknown') ?></p>
                    <p class="text-muted"><?= e($user['email'] ?? '') ?></p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-<?= ($user['is_active'] ?? 0) ? 'success' : 'secondary' ?>">
                            <?= ($user['is_active'] ?? 0) ? 'Active' : 'Inactive' ?>
                        </span>
                        <span class="badge bg-<?= ($user['is_admin'] ?? 0) ? 'danger' : 'info' ?>">
                            <?= ($user['is_admin'] ?? 0) ? 'Admin' : 'User' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">User ID</dt>
                        <dd class="col-sm-9"><?= $user['id'] ?? 'N/A' ?></dd>
                        <dt class="col-sm-3">Joined</dt>
                        <dd class="col-sm-9"><?= formatDate($user['created_at'] ?? null, 'F j, Y g:i A') ?></dd>
                        <dt class="col-sm-3">Last Login</dt>
                        <dd class="col-sm-9"><?= !empty($user['last_login']) ? timeAgo($user['last_login']) : 'Never' ?></dd>
                        <dt class="col-sm-3">Bio</dt>
                        <dd class="col-sm-9"><?= e($user['bio'] ?? 'No bio provided') ?></dd>
                    </dl>
                    <hr>
                    <div class="d-flex gap-2">
                        <form action="/admin/users/toggle/<?= $user['id'] ?>" method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-<?= ($user['is_active'] ?? 0) ? 'warning' : 'success' ?>"
                                onclick="return confirm('Are you sure?')">
                                <i class="fas fa-<?= ($user['is_active'] ?? 0) ? 'ban' : 'check' ?> me-1"></i>
                                <?= ($user['is_active'] ?? 0) ? 'Deactivate' : 'Activate' ?> Account
                            </button>
                        </form>
                        <a href="/profile/view/<?= $user['id'] ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>View Public Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
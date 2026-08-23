<?php
/**
 * Admin Users Management View
 */
$title = 'Manage Users';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><i class="fas fa-users text-primary me-2"></i>Manage Users</h1>
        </div>
        <div class="col-md-4">
            <form action="/admin/users" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control" placeholder="Search users..." 
                    value="<?= e($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Exchanges</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users ?? [] as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= getAvatarUrl($user['avatar']) ?>" class="rounded-circle me-2" width="36" height="36">
                                <div>
                                    <div class="fw-bold"><?= e($user['name']) ?></div>
                                    <small class="text-muted">@<?= e($user['username']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= e($user['email']) ?></td>
                        <td><small><?= formatDate($user['created_at'], 'M j, Y') ?></small></td>
                        <td><?= $user['exchange_count'] ?? 0 ?></td>
                        <td>
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['is_admin'] ? 'danger' : 'info' ?>">
                                <?= $user['is_admin'] ? 'Admin' : 'User' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/admin/users/view/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="/admin/users/toggle/<?= $user['id'] ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $user['is_active'] ? 'warning' : 'success' ?>" 
                                        title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>"
                                        onclick="return confirm('<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?> this user?')">
                                        <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($currentPage ?? 1) == $i ? 'active' : '' ?>">
                <a class="page-link" href="/admin/users?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
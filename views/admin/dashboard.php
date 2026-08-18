<?php
/**
 * Admin Dashboard View
 */
$title = 'Admin Dashboard';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0"><i class="fas fa-cog text-primary me-2"></i>Admin Dashboard</h1>
            <p class="text-muted mb-0">Overview of your skill exchange platform</p>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0"><?= $stats['total_skills'] ?? 0 ?></h3>
                        <p class="mb-0">Skills Listed</p>
                    </div>
                    <i class="fas fa-lightbulb fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0"><?= $stats['total_exchanges'] ?? 0 ?></h3>
                        <p class="mb-0">Exchanges</p>
                    </div>
                    <i class="fas fa-exchange-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mb-0"><?= $stats['pending_reports'] ?? 0 ?></h3>
                        <p class="mb-0">Pending Reports</p>
                    </div>
                    <i class="fas fa-flag fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Users</h5>
                    <a href="/admin/users" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentUsers ?? [] as $user): ?>
                    <div class="list-group-item d-flex align-items-center">
                        <img src="<?= getAvatarUrl($user['avatar']) ?>" class="rounded-circle me-3" width="40" height="40">
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?= e($user['name']) ?></h6>
                            <small class="text-muted">@<?= e($user['username']) ?> • Joined <?= timeAgo($user['created_at']) ?></small>
                        </div>
                        <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Recent Reports</h5>
                    <a href="/admin/reports" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentReports ?? [] as $report): ?>
                    <div class="list-group-item d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?= e($report['reporter_name']) ?> reported <?= e($report['reported_user_name']) ?></h6>
                            <small class="text-muted"><?= ucwords(str_replace('_', ' ', $report['reason'])) ?> • <?= timeAgo($report['created_at']) ?></small>
                        </div>
                        <span class="badge bg-warning text-dark">Pending</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentReports)): ?>
                    <div class="list-group-item text-center text-muted py-4">No pending reports</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
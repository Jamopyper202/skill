<?php
// views/admin/reports.php
$title = 'Manage Reports';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4"><i class="fas fa-flag text-danger me-2"></i>Manage Reports</h1>
    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Reporter</th>
                        <th>Reported</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports ?? [] as $report): ?>
                    <tr class="<?= $report['status'] === 'pending' ? 'table-warning' : '' ?>">
                        <td>#<?= $report['id'] ?></td>
                        <td><?= e($report['reporter_name']) ?></td>
                        <td><?= e($report['reported_user_name']) ?></td>
                        <td><?= ucwords(str_replace('_', ' ', $report['reason'])) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $report['status'] === 'pending' ? 'warning text-dark' : 
                                ($report['status'] === 'investigating' ? 'info' : 
                                ($report['status'] === 'resolved' ? 'success' : 'secondary')) 
                            ?>">
                                <?= ucfirst($report['status']) ?>
                            </span>
                        </td>
                        <td><small><?= timeAgo($report['created_at']) ?></small></td>
                        <td>
                            <a href="/admin/reports/view/<?= $report['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
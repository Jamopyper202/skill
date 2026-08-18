<?php
/**
 * My Reports View
 */
$title = 'My Reports';
$activeTab = 'reports';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0"><i class="fas fa-flag text-danger me-2"></i>My Reports</h1>
            <p class="text-muted mb-0">Track the status of your submitted reports</p>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <?php if (empty($reports)): ?>
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                <h3 class="h5 text-muted">No reports submitted</h3>
                <p class="text-muted">You haven't submitted any reports.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Reported User</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= getAvatarUrl($report['reported_user_avatar']) ?>" 
                                         alt="" class="rounded-circle me-2" width="32" height="32">
                                    <span><?= e($report['reported_user_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= ucwords(str_replace('_', ' ', $report['reason'])) ?></span>
                            </td>
                            <td>
                                <?php if ($report['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php elseif ($report['status'] === 'investigating'): ?>
                                    <span class="badge bg-info"><i class="fas fa-search me-1"></i>Investigating</span>
                                <?php elseif ($report['status'] === 'resolved'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Resolved</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Dismissed</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= timeAgo($report['created_at']) ?></small></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                    data-bs-target="#reportModal<?= $report['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Report Detail Modals -->
        <?php foreach ($reports as $report): ?>
        <div class="modal fade" id="reportModal<?= $report['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Report #<?= $report['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Reported User:</strong> <?= e($report['reported_user_name']) ?></p>
                        <p><strong>Reason:</strong> <?= ucwords(str_replace('_', ' ', $report['reason'])) ?></p>
                        <p><strong>Description:</strong></p>
                        <div class="bg-light p-3 rounded"><p class="mb-0"><?= nl2br(e($report['description'])) ?></p></div>
                        <?php if (!empty($report['admin_notes'])): ?>
                        <p class="mt-3"><strong>Admin Response:</strong></p>
                        <div class="alert alert-info mb-0"><p class="mb-0"><?= nl2br(e($report['admin_notes'])) ?></p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
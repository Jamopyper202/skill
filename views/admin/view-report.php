<?php
$title = 'Report #' . ($report['id'] ?? '?');
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/reports">Reports</a></li>
            <li class="breadcrumb-item active">Report #<?= $report['id'] ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h4 class="mb-0">Report Details</h4>
                    <span class="badge bg-<?= 
                        $report['status'] === 'pending' ? 'warning text-dark' : 
                        ($report['status'] === 'investigating' ? 'info' : 
                        ($report['status'] === 'resolved' ? 'success' : 'secondary')) 
                    ?>"><?= ucfirst($report['status']) ?></span>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Reporter</dt>
                        <dd class="col-sm-9"><?= e($report['reporter_name']) ?> (@<?= e($report['reporter_username']) ?>)</dd>
                        <dt class="col-sm-3">Reported User</dt>
                        <dd class="col-sm-9"><?= e($report['reported_user_name']) ?> (@<?= e($report['reported_user_username']) ?>)</dd>
                        <dt class="col-sm-3">Reason</dt>
                        <dd class="col-sm-9"><span class="badge bg-secondary"><?= ucwords(str_replace('_', ' ', $report['reason'])) ?></span></dd>
                        <dt class="col-sm-3">Submitted</dt>
                        <dd class="col-sm-9"><?= formatDate($report['created_at'], 'F j, Y g:i A') ?></dd>
                    </dl>
                    <hr>
                    <h6>Description</h6>
                    <div class="bg-light p-3 rounded mb-4">
                        <p class="mb-0"><?= nl2br(e($report['description'])) ?></p>
                    </div>

                    <?php if (!empty($report['exchange_id'])): ?>
                    <h6>Related Exchange</h6>
                    <p><a href="/admin/exchanges/view/<?= $report['exchange_id'] ?>">Exchange #<?= $report['exchange_id'] ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/reports/update/<?= $report['id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="investigating" <?= $report['status'] === 'investigating' ? 'selected' : '' ?>>Investigating</option>
                                <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="dismissed" <?= $report['status'] === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Notes</label>
                            <textarea name="admin_notes" rows="4" class="form-control" placeholder="Internal notes about this report..."><?= e($report['admin_notes'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i>Update Report
                        </button>
                    </form>

                    <hr>

                    <a href="/admin/users/view/<?
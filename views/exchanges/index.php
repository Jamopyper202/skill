<?php

/**
 * Exchanges List View
 * Shows all active exchanges for the current user
 */

$title = 'My Exchanges';
$activeTab = 'exchanges';

// Status badge colors
$statusColors = [
    'pending' => 'warning',
    'accepted' => 'info',
    'rejected' => 'danger',
    'completed' => 'success',
    'cancelled' => 'secondary'
];

// Status labels
$statusLabels = [
    'pending' => 'Pending',
    'accepted' => 'In Progress',
    'rejected' => 'Rejected',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0">
                <i class="fas fa-exchange-alt text-primary me-2"></i>My Exchanges
            </h1>
            <p class="text-muted mb-0">Manage your skill exchange requests</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo url('Exchange', 'history'); ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-history me-1"></i>History
            </a>
            <a href="<?php echo url('Match', 'index'); ?>" class="btn btn-primary">
                <i class="fas fa-search me-1"></i>Find Matches
            </a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4" id="exchangeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                All <span class="badge bg-secondary ms-1"><?= count($exchanges ?? []) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                Pending <span class="badge bg-warning text-dark ms-1"><?= count(array_filter($exchanges ?? [], fn($e) => $e['status'] === 'pending')) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                Active <span class="badge bg-info ms-1"><?= count(array_filter($exchanges ?? [], fn($e) => $e['status'] === 'accepted')) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                Completed <span class="badge bg-success ms-1"><?= count(array_filter($exchanges ?? [], fn($e) => $e['status'] === 'completed')) ?></span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="exchangeTabsContent">
        <!-- All Exchanges -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <?php if (empty($exchanges)): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-exchange-alt fa-4x text-muted"></i>
                    </div>
                    <h3 class="h5 text-muted">No exchanges yet</h3>
                    <p class="text-muted">Start by finding a skill match and sending an exchange request.</p>
                    <a href="<?php echo url('Exchange', 'history'); ?>" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-search me-1"></i>Find Matches
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($exchanges as $exchange): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0 hover-lift">
                                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?= $statusColors[$exchange['status']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$exchange['status']] ?? ucfirst($exchange['status']) ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i><?= timeAgo($exchange['created_at']) ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= e(uploadUrl($exchange['other_user_avatar'] ?? 'download.png')) ?>"
                                            alt="<?= e($exchange['other_user_name'] ?? 'User') ?>"
                                            class="rounded-circle me-3" width="48" height="48">
                                        <div>
                                            <h5 class="card-title mb-0">
                                                <a href="<?php echo url('Profile', 'view', ['id' => $exchange['other_user_id']]); ?>"
                                                    class="text-decoration-none">
                                                    <?= e($exchange['other_user_name'] ?? 'User') ?>
                                                </a>
                                            </h5>

                                            <small class="text-muted">
                                                Exchange Partner
                                            </small>
                                        </div>
                                    </div>


                                    <!-- <div class="exchange-skills mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary me-2">You offer</span>
                                            <span class="small"><?= e($exchange['offered_skill_name']) ?></span>
                                        </div>
                                        <div class="text-center my-1">
                                            <i class="fas fa-arrows-alt-v text-muted"></i>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2">You learn</span>
                                            <span class="small"><?= e($exchange['requested_skill_name']) ?></span>
                                        </div>
                                    </div> -->
                                    <?php
                                    $isInitiator = ($exchange['direction'] ?? '') === 'sent';
                                    ?>

                                    <div class="exchange-skills mb-3">

                                        <div class="d-flex align-items-center mb-2">

                                            <span class="badge bg-primary me-2">
                                                <?php echo $isInitiator ? 'You offer' : 'They offer'; ?>
                                            </span>

                                            <span class="small">
                                                <?php echo e(
                                                    $exchange['offered_skill_name'] ?? 'Unknown Skill'
                                                ); ?>
                                            </span>

                                        </div>


                                        <div class="text-center my-1">

                                            <i class="bi bi-arrow-down-up text-muted"></i>

                                        </div>


                                        <div class="d-flex align-items-center">

                                            <span class="badge bg-success me-2">
                                                <?php echo $isInitiator ? 'You learn' : 'They learn'; ?>
                                            </span>

                                            <span class="small">
                                                <?php echo e(
                                                    $exchange['requested_skill_name'] ?? 'Unknown Skill'
                                                ); ?>
                                            </span>

                                        </div>

                                    </div>
                                    <?php if (!empty($exchange['message'])): ?>
                                        <p class="card-text small text-muted fst-italic">
                                            "<?= e(truncate($exchange['message'], 80)) ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="<?php echo url('Exchange', 'view', [
                                                    'id' => $exchange['id']
                                                ]); ?>" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Tab -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            <?php
            $pendingExchanges = array_filter($exchanges ?? [], fn($e) => $e['status'] === 'pending');
            if (empty($pendingExchanges)):
            ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No pending exchanges</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($pendingExchanges as $exchange): ?>
                        <?php include __DIR__ . '/_exchange-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Tab -->
        <div class="tab-pane fade" id="active" role="tabpanel">
            <?php
            $activeExchanges = array_filter($exchanges ?? [], fn($e) => $e['status'] === 'accepted');
            if (empty($activeExchanges)):
            ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No active exchanges</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($activeExchanges as $exchange): ?>
                        <?php include __DIR__ . '/_exchange-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Tab -->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            <?php
            $completedExchanges = array_filter($exchanges ?? [], fn($e) => $e['status'] === 'completed');
            if (empty($completedExchanges)):
            ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No completed exchanges</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($completedExchanges as $exchange): ?>
                        <?php include __DIR__ . '/_exchange-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
'''
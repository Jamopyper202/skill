<?php

/**
 * ============================================================================
 * Matches Index View
 * ============================================================================
 * 
 * Displays all matches for the logged-in user with status filters.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * @var array $matches
 * @var array $total
 * @var array $totalPages
 * @var array $status
 * ============================================================================
 */

// Variables available from MatchController::index():
// $matches, $total, $totalPages, $status

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-people-fill me-2 text-primary"></i>My Matches</h2>
            <p class="text-muted">Users matched based on your skills and interests</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo url('Match', 'find'); ?>" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Find New Matches
            </a>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="btn-group mb-4" role="group">
        <a href="<?php echo url('Match', 'index'); ?>" class="btn <?php echo empty($status) ? 'btn-primary' : 'btn-outline-primary'; ?>">
            All
        </a>
        <a href="<?php echo url('Match', 'index') . '&status=pending'; ?>" class="btn <?php echo $status === 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Pending
        </a>
        <a href="<?php echo url('Match', 'index') . '&status=accepted'; ?>" class="btn <?php echo $status === 'accepted' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Accepted
        </a>
        <a href="<?php echo url('Match', 'index') . '&status=declined'; ?>" class="btn <?php echo $status === 'declined' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Declined
        </a>
    </div>

    <!-- Matches List -->
    <?php if (empty($matches)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-people fs-1 text-muted mb-3"></i>
                <h4>No matches found</h4>
                <p class="text-muted">
                    <?php echo empty($status) ? 'Click "Find New Matches" to discover compatible users!' : 'No matches with this status.'; ?>
                </p>
                <?php if (empty($status)): ?>
                    <a href="<?php echo url('Match', 'find'); ?>" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Find Matches
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($matches as $match): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <img src="<?php echo uploadUrl($match['other_user_picture']); ?>"
                                    alt="<?php echo e($match['other_user_name']); ?>"
                                    class="avatar-lg">
                                <div class="match-score-circle <?php
                                                                echo $match['match_score'] >= 70 ? 'match-high' : ($match['match_score'] >= 50 ? 'match-medium' : 'match-low');
                                                                ?>">
                                    <?php echo $match['match_score']; ?>%
                                </div>
                            </div>

                            <h5 class="mb-1"><?php echo e($match['other_user_name']); ?></h5>
                            <p class="text-muted small mb-2">
                                <?php echo e($match['other_user_experience'] ?? 'Beginner'); ?>
                            </p>

                            <?php if (!empty($match['match_reason'])): ?>
                                <p class="small text-muted mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <?php echo e(truncate($match['match_reason'], 80)); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($match['matched_skill_name'])): ?>
                                <span class="badge bg-light text-dark mb-3">
                                    <i class="bi bi-link me-1"></i><?php echo e($match['matched_skill_name']); ?>
                                </span>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge <?php
                                                    echo $match['status'] === 'accepted' ? 'bg-success' : ($match['status'] === 'declined' ? 'bg-danger' : 'bg-warning text-dark');
                                                    ?>">
                                    <?php echo ucfirst($match['status']); ?>
                                </span>

                                <?php if ($match['my_response'] === 'pending' && $match['status'] === 'pending'): ?>
                                    <div class="btn-group">
                                        <a href="<?php echo url('Match', 'accept', ['id' => $match['id']]); ?>"
                                            class="btn btn-sm btn-success" title="Accept">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="<?php echo url('Match', 'decline', ['id' => $match['id']]); ?>"
                                            class="btn btn-sm btn-danger" title="Decline"
                                            data-confirm="Are you sure you want to decline this match?">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo url('Match', 'view', ['id' => $match['id']]); ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            <i class="bi bi-clock me-1"></i><?php echo timeAgo($match['created_at']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        // Pagination
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $status = is_string($status ?? null) ? $status : '';
        $totalPages = (int) ($totalPages ?? 1);

        $baseUrl = url('Match', 'index');

        if ($status !== '') {
            $baseUrl .= '&status=' . urlencode($status);
        }

        echo pagination(
            $currentPage,
            $totalPages,
            $baseUrl
        );
        ?>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
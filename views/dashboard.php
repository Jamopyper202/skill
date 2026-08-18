<?php

/**
 * ============================================================================
 * Dashboard View
 * ============================================================================
 * 
 * Main user dashboard with statistics, matches, exchanges, and activity.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from DashboardController:
// $profile, $stats, $topMatches, $pendingExchanges, $recentMessages,
// $unreadNotifications, $recentNotifications, $exchangeStats, $recentReviews, $avgRating

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Welcome, <?php echo e($_SESSION['user_name'] ?? 'User'); ?>!</h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1"></i>
                        <?php echo date('l, F j, Y'); ?>
                    </p>
                </div>
                <a href="<?php echo url('Match', 'find'); ?>" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>Find Matches
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card primary h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <h4 class="mb-0"><?php echo $stats['skills_offered'] ?? 0; ?></h4>
                        <small class="text-muted">Skills Offered</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card warning h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <div>
                        <h4 class="mb-0"><?php echo $stats['skills_wanted'] ?? 0; ?></h4>
                        <small class="text-muted">Skills Wanted</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card success h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h4 class="mb-0"><?php echo $stats['matches'] ?? 0; ?></h4>
                        <small class="text-muted">Matches</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card info h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <h4 class="mb-0"><?php echo ($avgRating ?? 0) > 0 ? ($avgRating ?? 0) : 'N/A'; ?></h4>
                        <small class="text-muted">Avg Rating</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Top Matches -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Recommended Matches</h5>
                    <a href="<?php echo url('Match', 'index'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($topMatches)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-search"></i>
                            <h5>No matches yet</h5>
                            <p class="text-muted">Add more skills to find better matches!</p>
                            <a href="<?php echo url('Skill', 'index'); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>Add Skills
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($topMatches as $match): ?>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <img src="<?php echo uploadUrl($match['other_user_picture']); ?>"
                                                    alt="<?php echo e($match['other_user_name']); ?>"
                                                    class="avatar-lg me-3">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo e($match['other_user_name']); ?></h6>
                                                            <small class="text-muted">
                                                                <?php echo e($match['other_user_experience'] ?? 'Beginner'); ?>
                                                            </small>
                                                        </div>
                                                        <div class="match-score-circle <?php
                                                                                        echo $match['match_score'] >= 70 ? 'match-high' : ($match['match_score'] >= 50 ? 'match-medium' : 'match-low');
                                                                                        ?>">
                                                            <?php echo $match['match_score']; ?>%
                                                        </div>
                                                    </div>
                                                    <p class="small text-muted mt-2 mb-2">
                                                        <?php echo e(truncate($match['match_reason'], 80)); ?>
                                                    </p>
                                                    <div class="d-flex gap-2">
                                                        <a href="<?php echo url('Match', 'view', ['id' => $match['id']]); ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a>
                                                        <a href="<?php echo url('Exchange', 'send', [
                                                                        'match_id' => $match['id'],
                                                                        'user_id' => ($match['user_id_1'] == $_SESSION['user_id'])
                                                                            ? $match['user_id_2']
                                                                            : $match['user_id_1']
                                                                    ]); ?>" class="btn btn-sm btn-primary">
                                                            Exchange
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Exchange Statistics -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Exchange Activity</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="text-primary mb-1"><?php echo $exchangeStats['total'] ?? 0; ?></h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-success mb-1"><?php echo $exchangeStats['completed'] ?? 0; ?></h3>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning mb-1"><?php echo $exchangeStats['pending'] ?? 0; ?></h3>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Pending Exchange Requests -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bell me-2 text-warning"></i>Pending Requests</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($pendingExchanges)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2"></i>
                            <p class="mb-0 small">No pending requests</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($pendingExchanges as $exchange): ?>

                                <div class="card">
                                    <div class="card-body">

                                        <h5>
                                            <?php echo e($exchange['requester_name']); ?>
                                        </h5>

                                        <p>
                                            Wants to learn:
                                            <strong>
                                                <?php echo e($exchange['requested_skill_name']); ?>
                                            </strong>
                                        </p>

                                        <p>
                                            Offers:
                                            <strong>
                                                <?php echo e($exchange['offered_skill_name']); ?>
                                            </strong>
                                        </p>

                                        <a href="<?php echo url('Exchange', 'view', [
                                                        'id' => $exchange['id']
                                                    ]); ?>" class="btn btn-primary">
                                            View Request
                                        </a>

                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star me-2 text-warning"></i>Recent Reviews</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentReviews)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-star fs-2"></i>
                            <p class="mb-0 small">No reviews yet</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentReviews as $review): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="mb-1"><?php echo starRating($review['rating']); ?></div>
                                            <p class="mb-0 small text-muted">
                                                "<?php echo e(truncate($review['comment'], 60)); ?>"
                                            </p>
                                            <small class="text-muted">by <?php echo e($review['reviewer_name']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-primary"></i>Quick Links</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo url('Skill', 'index'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-tools me-2"></i>Manage Skills
                    </a>
                    <a href="<?php echo url('Skill', 'browse'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-search me-2"></i>Browse Skills
                    </a>
                    <a href="<?php echo url('Message', 'index'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-chat-dots me-2"></i>Messages
                        <?php if (($stats['unread_messages'] ?? 0) > 0): ?>
                            <span class="badge bg-danger rounded-pill ms-2">
                                <?php echo $stats['unread_messages'] ?? 0; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo url('Portfolio', 'index'); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-briefcase me-2"></i>My Portfolio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
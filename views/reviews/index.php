<?php
/**
 * My Reviews View
 * Shows all reviews received by the current user
 */

$title = 'My Reviews';
$activeTab = 'reviews';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0">
                <i class="fas fa-star text-warning me-2"></i>My Reviews
            </h1>
            <p class="text-muted mb-0">What others are saying about your skill exchanges</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/exchanges/history" class="btn btn-primary">
                <i class="fas fa-pen me-1"></i>Write a Review
            </a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <!-- Rating Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow border-0 text-center">
                <div class="card-body py-4">
                    <h2 class="display-4 fw-bold text-primary mb-0">
                        <?= number_format($averageRating ?? 0, 1) ?>
                    </h2>
                    <div class="text-warning my-2">
                        <?php $avg = round($averageRating ?? 0); ?>
                        <?= str_repeat('<i class="fas fa-star fa-lg"></i>', $avg) ?>
                        <?= str_repeat('<i class="far fa-star fa-lg"></i>', 5 - $avg) ?>
                    </div>
                    <p class="text-muted mb-0"><?= $totalReviews ?? 0 ?> review<?= ($totalReviews ?? 0) !== 1 ? 's' : '' ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="mb-3">Rating Breakdown</h5>
                    <?php for ($i = 5; $i >= 1; $i--): 
                        $count = $ratingBreakdown[$i] ?? 0;
                        $percentage = ($totalReviews ?? 0) > 0 ? round(($count / $totalReviews) * 100) : 0;
                    ?>
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2" style="width: 60px;"><?= $i ?> stars</span>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        <span class="ms-2 text-muted small" style="width: 40px;"><?= $count ?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <?php if (empty($reviews)): ?>
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-star fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="h5 text-muted">No reviews yet</h3>
                <p class="text-muted">Complete some skill exchanges to start receiving reviews.</p>
                <a href="/matches" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Find Exchanges
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($reviews as $review): ?>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <img src="<?= getAvatarUrl($review['reviewer_avatar']) ?>" 
                                     alt="<?= e($review['reviewer_name']) ?>" 
                                     class="rounded-circle me-3" width="48" height="48">
                                <div>
                                    <h6 class="mb-0">
                                        <a href="/profile/view/<?= $review['reviewer_id'] ?>" class="text-decoration-none">
                                            <?= e($review['reviewer_name']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">@<?= e($review['reviewer_username']) ?></small>
                                </div>
                            </div>
                            <div class="text-warning">
                                <?= str_repeat('<i class="fas fa-star"></i>', $review['rating']) ?>
                                <?= str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']) ?>
                            </div>
                        </div>
                        
                        <p class="card-text"><?= e($review['comment']) ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-exchange-alt me-1"></i>
                                <?= e($review['exchange_skill_name'] ?? 'Skill Exchange') ?>
                            </small>
                            <small class="text-muted"><?= timeAgo($review['created_at']) ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPage ?? 1) == $i ? 'active' : '' ?>">
                    <a class="page-link" href="/reviews?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

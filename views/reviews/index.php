<?php
/**
 * My Reviews View
 * Shows all reviews received by the current user
 */

$title = 'My Reviews';
$activeTab = 'reviews';

$avgRating = (float) ($avgRating ?? 0);
$total = (int) ($total ?? 0);
$totalPages = (int) ($totalPages ?? 1);
$currentPage = (int) ($_GET['page'] ?? 1);
$ratingBreakdown = $ratingBreakdown ?? [];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Page Header -->
    <div class="row mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>
                My Reviews
            </h1>

            <p class="text-muted mb-0">
                What others are saying about your skill exchanges
            </p>

        </div>


        <div class="col-md-4 text-md-end">

            <a
                href="<?php echo url('Exchange', 'history'); ?>"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Write a Review
            </a>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- Rating Summary -->
    <div class="row g-4 mb-4">

        <!-- Average Rating -->
        <div class="col-md-4">

            <div class="card shadow border-0 text-center">

                <div class="card-body py-4">

                    <h2 class="display-4 fw-bold text-primary mb-0">
                        <?php echo number_format($avgRating, 1); ?>
                    </h2>


                    <div class="text-warning my-2">

                        <?php
                        $roundedRating = (int) round($avgRating);
                        ?>

                        <?php for ($i = 1; $i <= 5; $i++): ?>

                            <?php if ($i <= $roundedRating): ?>

                                <i class="bi bi-star-fill fs-5"></i>

                            <?php else: ?>

                                <i class="bi bi-star fs-5"></i>

                            <?php endif; ?>

                        <?php endfor; ?>

                    </div>


                    <p class="text-muted mb-0">

                        <?php echo $total; ?>

                        review<?php echo $total !== 1 ? 's' : ''; ?>

                    </p>

                </div>

            </div>

        </div>


        <!-- Rating Breakdown -->
        <div class="col-md-8">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h5 class="mb-3">
                        Rating Breakdown
                    </h5>


                    <?php for ($i = 5; $i >= 1; $i--): ?>

                        <?php

                        $count = (int) ($ratingBreakdown[$i] ?? 0);

                        $percentage = $total > 0
                            ? round(($count / $total) * 100)
                            : 0;

                        ?>

                        <div class="d-flex align-items-center mb-2">

                            <span
                                class="me-2"
                                style="width: 60px;"
                            >
                                <?php echo $i; ?> stars
                            </span>


                            <div class="flex-grow-1">

                                <div
                                    class="progress"
                                    style="height: 8px;"
                                >

                                    <div
                                        class="progress-bar bg-warning"
                                        style="width: <?php echo $percentage; ?>%;"
                                    ></div>

                                </div>

                            </div>


                            <span
                                class="ms-2 text-muted small"
                                style="width: 40px;"
                            >
                                <?php echo $count; ?>
                            </span>

                        </div>

                    <?php endfor; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- Reviews -->
    <?php if (empty($reviews)): ?>

        <div class="card shadow border-0">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i class="bi bi-star fs-1 text-muted opacity-25"></i>

                </div>


                <h3 class="h5 text-muted">
                    No reviews yet
                </h3>


                <p class="text-muted">
                    Complete some skill exchanges to start receiving reviews.
                </p>


                <a
                    href="<?php echo url('Match', 'index'); ?>"
                    class="btn btn-primary"
                >
                    <i class="bi bi-search me-1"></i>
                    Find Exchanges
                </a>

            </div>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($reviews as $review): ?>

                <?php

                $reviewerId = (int) ($review['reviewer_id'] ?? 0);

                $reviewerName =
                    $review['reviewer_name']
                    ?? 'Unknown User';

                $reviewerPicture =
                    $review['reviewer_picture']
                    ?? 'download.png';

                $rating =
                    (int) ($review['rating'] ?? 0);

                $comment =
                    $review['comment'] ?? '';

                ?>

                <div class="col-md-6">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body">

                            <!-- Reviewer + Rating -->
                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div class="d-flex align-items-center">

                                    <img
                                        src="<?php echo e(
                                            uploadUrl($reviewerPicture)
                                        ); ?>"
                                        alt="<?php echo e($reviewerName); ?>"
                                        class="rounded-circle me-3"
                                        width="48"
                                        height="48"
                                        style="object-fit: cover;"
                                    >


                                    <div>

                                        <h6 class="mb-0">

                                            <a
                                                href="<?php echo url(
                                                    'Profile',
                                                    'view',
                                                    ['id' => $reviewerId]
                                                ); ?>"
                                                class="text-decoration-none"
                                            >
                                                <?php echo e($reviewerName); ?>
                                            </a>

                                        </h6>


                                        <small class="text-muted">
                                            SkillSwap User
                                        </small>

                                    </div>

                                </div>


                                <!-- Rating -->
                                <div class="text-warning">

                                    <?php for ($i = 1; $i <= 5; $i++): ?>

                                        <?php if ($i <= $rating): ?>

                                            <i class="bi bi-star-fill"></i>

                                        <?php else: ?>

                                            <i class="bi bi-star"></i>

                                        <?php endif; ?>

                                    <?php endfor; ?>

                                </div>

                            </div>


                            <!-- Comment -->
                            <p class="card-text">
                                <?php echo e($comment); ?>
                            </p>


                            <!-- Exchange / Date -->
                            <div
                                class="d-flex justify-content-between align-items-center mt-3"
                            >

                                <small class="text-muted">

                                    <i class="bi bi-arrow-left-right me-1"></i>

                                    <?php
                                    $skillName =
                                        $review['offered_skill_name']
                                        ?? $review['requested_skill_name']
                                        ?? 'Skill Exchange';
                                    ?>

                                    <?php echo e($skillName); ?>

                                </small>


                                <?php if (!empty($review['created_at'])): ?>

                                    <small class="text-muted">

                                        <?php echo timeAgo(
                                            $review['created_at']
                                        ); ?>

                                    </small>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>

            <nav class="mt-4">

                <ul class="pagination justify-content-center">

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                        <li
                            class="page-item
                            <?php echo $currentPage === $i
                                ? 'active'
                                : ''; ?>"
                        >

                            <a
                                class="page-link"
                                href="<?php echo url(
                                    'Review',
                                    'index'
                                ) . '&page=' . $i; ?>"
                            >
                                <?php echo $i; ?>
                            </a>

                        </li>

                    <?php endfor; ?>

                </ul>

            </nav>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
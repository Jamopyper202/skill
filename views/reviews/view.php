<?php
/**
 * Review Detail View
 */

$title = 'Review Details';
$activeTab = 'reviews';

$currentUserId = getCurrentUserId();

$reviewId = (int) ($review['id'] ?? 0);

$reviewerId = (int) ($review['reviewer_id'] ?? 0);
$revieweeId = (int) ($review['reviewee_id'] ?? 0);

$reviewerName = $review['reviewer_name'] ?? 'Unknown User';
$revieweeName = $review['reviewee_name'] ?? 'Unknown User';

$reviewerPicture = $review['reviewer_picture'] ?? 'download.png';

$rating = (int) ($review['rating'] ?? 0);
$comment = $review['comment'] ?? '';

$exchangeId = (int) (
    $review['exchange_id']
    ?? $review['exchange_request_id']
    ?? 0
);

$createdAt = $review['created_at'] ?? null;
$updatedAt = $review['updated_at'] ?? null;
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="<?php echo url('Dashboard', 'index'); ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="<?php echo url('Review', 'index'); ?>">
                            Reviews
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Review #<?php echo $reviewId; ?>
                    </li>

                </ol>

            </nav>


            <div class="card shadow border-0">

                <div class="card-body p-4">

                    <!-- Reviewer / Rating -->
                    <div
                        class="d-flex justify-content-between align-items-start mb-4"
                    >

                        <!-- Reviewer -->
                        <div class="d-flex align-items-center">

                            <img
                                src="<?php echo e(
                                    uploadUrl($reviewerPicture)
                                ); ?>"
                                alt="<?php echo e($reviewerName); ?>"
                                class="rounded-circle me-3"
                                width="64"
                                height="64"
                                style="object-fit: cover;"
                            >


                            <div>

                                <h4 class="mb-1">

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

                                </h4>


                                <p class="text-muted mb-0">
                                    reviewed
                                </p>


                                <h5 class="mb-0">

                                    <a
                                        href="<?php echo url(
                                            'Profile',
                                            'view',
                                            ['id' => $revieweeId]
                                        ); ?>"
                                        class="text-decoration-none"
                                    >
                                        <?php echo e($revieweeName); ?>
                                    </a>

                                </h5>

                            </div>

                        </div>


                        <!-- Rating -->
                        <div class="text-warning text-center">

                            <div class="display-4 mb-0">
                                <?php echo $rating; ?>
                            </div>


                            <div>

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <?php if ($i <= $rating): ?>

                                        <i class="bi bi-star-fill"></i>

                                    <?php else: ?>

                                        <i class="bi bi-star"></i>

                                    <?php endif; ?>

                                <?php endfor; ?>

                            </div>

                        </div>

                    </div>


                    <!-- Review Comment -->
                    <div class="bg-light p-4 rounded mb-4">

                        <p class="lead mb-0">
                            &ldquo;<?php echo e($comment); ?>&rdquo;
                        </p>

                    </div>


                    <!-- Review Metadata -->
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >

                        <div>

                            <?php if ($exchangeId > 0): ?>

                                <small class="text-muted">

                                    <i class="bi bi-arrow-left-right me-1"></i>

                                    Exchange #<?php echo $exchangeId; ?>

                                </small>

                            <?php endif; ?>


                            <?php if (!empty($createdAt)): ?>

                                <small class="text-muted ms-2">

                                    <?php echo formatDate(
                                        $createdAt,
                                        'F j, Y'
                                    ); ?>

                                </small>

                            <?php endif; ?>


                            <?php if (
                                !empty($createdAt)
                                && !empty($updatedAt)
                                && $createdAt !== $updatedAt
                            ): ?>

                                <small class="text-muted d-block mt-1">

                                    <i class="bi bi-pencil me-1"></i>

                                    Edited
                                    <?php echo timeAgo($updatedAt); ?>

                                </small>

                            <?php endif; ?>

                        </div>


                        <!-- Edit -->
                        <div>

                            <?php if (
                                $reviewerId === $currentUserId
                            ): ?>

                                <a
                                    href="<?php echo url(
                                        'Review',
                                        'edit',
                                        ['id' => $reviewId]
                                    ); ?>"
                                    class="btn btn-outline-warning btn-sm"
                                >

                                    <i class="bi bi-pencil me-1"></i>
                                    Edit

                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
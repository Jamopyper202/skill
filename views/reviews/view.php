<?php
/**
 * Review Detail View
 */
$title = 'Review Details';
$activeTab = 'reviews';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/reviews">Reviews</a></li>
                    <li class="breadcrumb-item active">Review #<?= $review['id'] ?></li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center">
                            <img src="<?= getAvatarUrl($review['reviewer_avatar']) ?>" 
                                 alt="" class="rounded-circle me-3" width="64" height="64">
                            <div>
                                <h4 class="mb-1">
                                    <a href="/profile/view/<?= $review['reviewer_id'] ?>" class="text-decoration-none">
                                        <?= e($review['reviewer_name']) ?>
                                    </a>
                                </h4>
                                <p class="text-muted mb-0">reviewed</p>
                                <h5 class="mb-0">
                                    <a href="/profile/view/<?= $review['reviewee_id'] ?>" class="text-decoration-none">
                                        <?= e($review['reviewee_name']) ?>
                                    </a>
                                </h5>
                            </div>
                        </div>
                        <div class="text-warning text-center">
                            <div class="display-4 mb-0"><?= $review['rating'] ?></div>
                            <div>
                                <?= str_repeat('<i class="fas fa-star"></i>', $review['rating']) ?>
                                <?= str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-4 rounded mb-4">
                        <p class="lead mb-0">"<?= e($review['comment']) ?>"</p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-exchange-alt me-1"></i>
                                Exchange #<?= $review['exchange_id'] ?> • 
                                <?= formatDate($review['created_at'], 'F j, Y') ?>
                            </small>
                            <?php if ($review['created_at'] != $review['updated_at']): ?>
                                <small class="text-muted d-block">
                                    <i class="fas fa-edit me-1"></i>Edited <?= timeAgo($review['updated_at']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (($review['reviewer_id'] ?? 0) == ($currentUser['id'] ?? 0)): ?>
                                <a href="/reviews/edit/<?= $review['id'] ?>" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
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
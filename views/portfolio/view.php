<?php

/**
 * Public Portfolio View
 * /**
 * Public Portfolio View
 *
 * Variables provided by PortfolioController::view():
 * @var array $portfolioUser
 * @var array $portfolioItems
 */


// Controller provides:
// $user  - portfolio owner's user information
// $items - portfolio items

$title = e(($user['full_name'] ?? 'User')) . "'s Portfolio";
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <?php
    $profilePicture = $portfolioProfile['profile_picture'] ?? '';

    if (!empty($profilePicture)) {
        $avatarUrl = uploadUrl($profilePicture);
    } else {
        $avatarUrl = asset('images/download.png');
    }
    ?>
    <!-- User Header -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img
                    src="<?= uploadUrl($portfolioProfile['profile_picture'] ?? 'download.png') ?>"
                    alt="<?= e($portfolioUser['full_name'] ?? 'User') ?>"
                    class="rounded-circle me-4"
                    width="80"
                    height="80"
                    style="object-fit: cover;">

                <div>
                    <h1 class="h3 mb-1">
                        <?= e($portfolioUser['full_name'] ?? 'Unknown User') ?>'s Portfolio
                    </h1>

                    <?php if (!empty($portfolioUser['username'])): ?>
                        <p class="text-muted mb-2">
                            @<?= e($portfolioUser['username']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="d-flex gap-2">

                        <a
                            href="<?= url('Profile', 'view', ['id' => $portfolioUser['id']]) ?>"
                            class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person me-1"></i>
                            View Profile
                        </a>

                        <a
                            href="<?= url('Message', 'conversation', ['user_id' => $portfolioUser['id']]) ?>"
                            class="btn btn-primary btn-sm">
                            <i class="bi bi-chat-dots me-1"></i>
                            Contact
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Portfolio Items -->

    <?php if (empty($items)): ?>

        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">

                <i class="bi bi-briefcase fs-1 text-muted mb-3"></i>

                <h3 class="h5 text-muted">
                    No portfolio items yet
                </h3>

                <p class="text-muted mb-0">
                    This user has not added any portfolio projects yet.
                </p>

            </div>
        </div>

    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($items as $item): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm border-0 h-100">

                        <!-- Project Image -->

                        <?php if (!empty($item['image'])): ?>

                            <img
                                src="<?= e($item['image']) ?>"
                                alt="<?= e($item['title'] ?? 'Portfolio Project') ?>"
                                class="card-img-top"
                                style="height: 220px; object-fit: cover;">

                        <?php else: ?>

                            <div
                                class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                style="height: 220px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>

                        <?php endif; ?>


                        <!-- Project Details -->

                        <div class="card-body">

                            <h5 class="card-title">
                                <?= e($item['title'] ?? 'Untitled Project') ?>
                            </h5>

                            <?php if (!empty($item['description'])): ?>

                                <p class="card-text text-muted">
                                    <?= nl2br(e($item['description'])) ?>
                                </p>

                            <?php else: ?>

                                <p class="card-text text-muted">
                                    No description provided.
                                </p>

                            <?php endif; ?>


                            <!-- Skill -->

                            <?php if (!empty($item['skill_name'])): ?>

                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-code-slash me-1"></i>
                                    <?= e($item['skill_name']) ?>
                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- Project Link -->

                        <?php if (!empty($item['project_url'])): ?>

                            <div class="card-footer bg-transparent border-0 pb-3">

                                <a
                                    href="<?= e($item['project_url']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    View Project
                                </a>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
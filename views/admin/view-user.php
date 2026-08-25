<?php

/**
 * Admin View Single User
 */

$title = 'User: ' . ($user['name'] ?? 'Unknown');
$activeTab = 'admin';

$userId = (int) ($user['id'] ?? 0);
$isActive = !empty($user['is_active']);
$isAdmin = !empty($user['is_admin']);
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'index') ?>">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'users') ?>">
                    Users
                </a>
            </li>

            <li class="breadcrumb-item active">
                <?= e($user['name'] ?? 'User') ?>
            </li>

        </ol>

    </nav>


    <div class="row g-4">


        <!-- =====================================================
             USER PROFILE CARD
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <img
                        src="<?= !empty($user['avatar'])
                                    ? uploadUrl($user['avatar'])
                                    : BASE_URL . '/assets/images/download.png' ?>"
                        alt="<?= e($user['name'] ?? 'User') ?>"
                        class="rounded-circle mb-3"
                        width="120"
                        height="120">

                    <h3 class="mb-1">
                        <?= e($user['name'] ?? 'Unknown') ?>
                    </h3>

                    <p class="text-muted mb-2">
                        @<?= e($user['username'] ?? 'unknown') ?>
                    </p>

                    <p class="text-muted">
                        <?= e($user['email'] ?? '') ?>
                    </p>


                    <!-- Status -->
                    <div class="d-flex justify-content-center gap-2">

                        <?php if ($isActive): ?>

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Active
                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Inactive
                            </span>

                        <?php endif; ?>


                        <!-- Role -->

                        <?php if ($isAdmin): ?>

                            <span class="badge bg-danger">
                                <i class="bi bi-shield-check me-1"></i>
                                Admin
                            </span>

                        <?php else: ?>

                            <span class="badge bg-info">
                                <i class="bi bi-person me-1"></i>
                                User
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>



        <!-- =====================================================
             USER DETAILS
        ====================================================== -->

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0">
                        <i class="bi bi-person-vcard me-2"></i>
                        User Details
                    </h5>

                </div>


                <div class="card-body">

                    <dl class="row mb-0">


                        <!-- User ID -->

                        <dt class="col-sm-3">
                            User ID
                        </dt>

                        <dd class="col-sm-9">
                            <?= $userId ?: 'N/A' ?>
                        </dd>


                        <!-- Joined -->

                        <dt class="col-sm-3">
                            Joined
                        </dt>

                        <dd class="col-sm-9">

                            <?= !empty($user['created_at'])
                                ? formatDate(
                                    $user['created_at'],
                                    'F j, Y g:i A'
                                )
                                : 'N/A'
                            ?>

                        </dd>


                        <!-- Last Login -->

                        <dt class="col-sm-3">
                            Last Login
                        </dt>

                        <dd class="col-sm-9">

                            <?php if (!empty($user['last_login'])): ?>

                                <?= timeAgo($user['last_login']) ?>

                            <?php else: ?>

                                <span class="text-muted">
                                    Never
                                </span>

                            <?php endif; ?>

                        </dd>


                        <!-- Bio -->

                        <dt class="col-sm-3">
                            Bio
                        </dt>

                        <dd class="col-sm-9">

                            <?= !empty($user['bio'])
                                ? nl2br(e($user['bio']))
                                : '<span class="text-muted">No bio provided</span>'
                            ?>

                        </dd>

                    </dl>


                    <hr class="my-4">


                    <!-- =================================================
                         ACTIONS
                    ================================================== -->

                    <div class="d-flex flex-wrap gap-2">


                        <!-- Activate / Deactivate -->

                        <?php if ($userId > 0): ?>

                            <?php

                            $buttonClass = $isActive
                                ? 'warning'
                                : 'success';

                            $buttonTitle = $isActive
                                ? 'Deactivate User'
                                : 'Activate User';

                            $actionText = $isActive
                                ? 'Deactivate'
                                : 'Activate';

                            ?>

                            <form
                                action="<?= url(
                                            'Admin',
                                            'toggleUser'
                                        ) ?>"
                                method="POST"
                                class="d-inline">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $userId ?>">
                                <button
                                    type="submit"
                                    class="btn btn-<?= $isActive ? 'warning' : 'success' ?>"
                                    onclick="return confirm(
            '<?= $isActive ? 'Deactivate' : 'Activate' ?> this user?'
        );">

                                    <?php if ($isActive): ?>

                                        <i class="bi bi-person-x me-1"></i>
                                        Deactivate Account

                                    <?php else: ?>

                                        <i class="bi bi-person-check me-1"></i>
                                        Activate Account

                                    <?php endif; ?>

                                </button>

                            </form>

                        <?php endif; ?>


                        <!-- Public Profile -->

                        <?php if ($userId > 0): ?>

                            <a
                                href="<?= url(
                                            'Profile',
                                            'view',
                                            ['id' => $userId]
                                        ) ?>"
                                class="btn btn-outline-primary"
                                target="_blank">

                                <i class="bi bi-box-arrow-up-right me-1"></i>

                                View Public Profile

                            </a>

                        <?php endif; ?>


                        <!-- Back -->

                        <a
                            href="<?= url('Admin', 'users') ?>"
                            class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-left me-1"></i>

                            Back to Users

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
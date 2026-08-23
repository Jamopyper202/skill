<?php

/**
 * Admin Dashboard View
 */

$title = 'Admin Dashboard';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>


<div class="container-fluid py-4">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <div class="row mb-4">

        <div class="col">

            <h1 class="h2 mb-0">

                <i class="fas fa-tachometer-alt text-primary me-2"></i>

                Admin Dashboard

            </h1>

            <p class="text-muted mb-0">
                Overview of your SkillSwap platform
            </p>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         STATISTICS
    ========================================================== -->

    <div class="row g-4 mb-4">


        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">

            <div class="card bg-primary text-white border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="flex-grow-1">

                        <h3 class="mb-0">
                            <?= (int) ($stats['total_users'] ?? 0) ?>
                        </h3>

                        <p class="mb-0">
                            Total Users
                        </p>

                    </div>

                    <i class="fas fa-users fa-2x opacity-50"></i>

                </div>

            </div>

        </div>


        <!-- Skills -->
        <div class="col-xl-3 col-md-6">

            <div class="card bg-success text-white border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="flex-grow-1">

                        <h3 class="mb-0">
                            <?= (int) ($stats['total_skills'] ?? 0) ?>
                        </h3>

                        <p class="mb-0">
                            Skills Listed
                        </p>

                    </div>

                    <i class="fas fa-lightbulb fa-2x opacity-50"></i>

                </div>

            </div>

        </div>


        <!-- Exchanges -->
        <div class="col-xl-3 col-md-6">

            <div class="card bg-info text-white border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="flex-grow-1">

                        <h3 class="mb-0">
                            <?= (int) ($stats['total_exchanges'] ?? 0) ?>
                        </h3>

                        <p class="mb-0">
                            Exchanges
                        </p>

                    </div>

                    <i class="fas fa-exchange-alt fa-2x opacity-50"></i>

                </div>

            </div>

        </div>


        <!-- Pending Reports -->
        <div class="col-xl-3 col-md-6">

            <div class="card bg-warning text-dark border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="flex-grow-1">

                        <h3 class="mb-0">
                            <?= (int) ($stats['pending_reports'] ?? 0) ?>
                        </h3>

                        <p class="mb-0">
                            Pending Reports
                        </p>

                    </div>

                    <i class="fas fa-flag fa-2x opacity-50"></i>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         SECONDARY STATISTICS
    ========================================================== -->

    <div class="row g-4 mb-4">


        <!-- Active Users -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-success bg-opacity-10
                                    text-success p-3 me-3">

                            <i class="fas fa-user-check fa-lg"></i>

                        </div>

                        <div>

                            <h4 class="mb-0">
                                <?= (int) ($stats['active_users'] ?? 0) ?>
                            </h4>

                            <small class="text-muted">
                                Active Users
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Completed Exchanges -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-primary bg-opacity-10
                                    text-primary p-3 me-3">

                            <i class="fas fa-check-circle fa-lg"></i>

                        </div>

                        <div>

                            <h4 class="mb-0">
                                <?= (int) ($stats['completed_exchanges'] ?? 0) ?>
                            </h4>

                            <small class="text-muted">
                                Completed Exchanges
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Reviews -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-warning bg-opacity-10
                                    text-warning p-3 me-3">

                            <i class="fas fa-star fa-lg"></i>

                        </div>

                        <div>

                            <h4 class="mb-0">

                                <?= (int) ($stats['total_reviews'] ?? 0) ?>

                            </h4>

                            <small class="text-muted">
                                Reviews
                                <?php if (!empty($stats['avg_rating'])): ?>
                                    · <?= e($stats['avg_rating']) ?>/5
                                <?php endif; ?>
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Matches -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-info bg-opacity-10
                                    text-info p-3 me-3">

                            <i class="fas fa-users-cog fa-lg"></i>

                        </div>

                        <div>

                            <h4 class="mb-0">

                                <?= (int) ($stats['total_matches'] ?? 0) ?>

                            </h4>

                            <small class="text-muted">
                                Matches
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         RECENT USERS + PENDING REPORTS
    ========================================================== -->

    <div class="row g-4">


        <!-- =====================================================
             RECENT USERS
        ====================================================== -->

        <div class="col-lg-6">

            <div class="card shadow border-0 h-100">

                <div
                    class="card-header bg-white d-flex
                           justify-content-between align-items-center">

                    <div>

                        <h5 class="mb-0">

                            <i class="fas fa-users me-2"></i>

                            Recent Users

                        </h5>

                    </div>

                    <a
                        href="<?= url('Admin', 'users') ?>"
                        class="btn btn-sm btn-outline-primary">

                        View All

                    </a>

                </div>


                <div class="list-group list-group-flush">

                    <?php if (!empty($recentUsers)): ?>


                        <?php foreach ($recentUsers as $user): ?>

                            <?php
                            $userName =
                                $user['full_name']
                                ?? 'Unknown User';

                            $avatar =
                                $user['profile_picture']
                                ?? '';

                            $userId =
                                (int) ($user['id'] ?? 0);
                            ?>


                            <div
                                class="list-group-item
                                       d-flex align-items-center">


                                <!-- Avatar -->

                                <img
                                    src="<?= e(
                                                uploadUrl($avatar)
                                            ) ?>"
                                    alt="<?= e($userName) ?>"
                                    class="rounded-circle me-3"
                                    width="40"
                                    height="40"
                                    style="object-fit: cover;">


                                <!-- User Information -->

                                <div class="flex-grow-1">

                                    <h6 class="mb-0">

                                        <?= e($userName) ?>

                                    </h6>

                                    <small class="text-muted">

                                        <?php if (!empty($user['email'])): ?>

                                            <?= e($user['email']) ?>

                                        <?php endif; ?>

                                        <?php if (!empty($user['created_at'])): ?>

                                            · Joined
                                            <?= e(
                                                timeAgo(
                                                    $user['created_at']
                                                )
                                            ) ?>

                                        <?php endif; ?>

                                    </small>

                                </div>


                                <!-- Status -->

                                <?php if (!empty($user['is_active'])): ?>

                                    <span class="badge bg-success">

                                        Active

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        Inactive

                                    </span>

                                <?php endif; ?>


                            </div>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <div
                            class="list-group-item
                                   text-center text-muted py-5">

                            <i
                                class="fas fa-users fa-2x mb-2 opacity-50">
                            </i>

                            <p class="mb-0">
                                No users found.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- =====================================================
             PENDING REPORTS
        ====================================================== -->

        <div class="col-lg-6">

            <div class="card shadow border-0 h-100">


                <div
                    class="card-header bg-white d-flex
                           justify-content-between align-items-center">

                    <h5 class="mb-0">

                        <i class="fas fa-flag me-2 text-danger"></i>

                        Pending Reports

                    </h5>


                    <a
                        href="<?= url('Admin', 'reports') ?>"
                        class="btn btn-sm btn-outline-primary">

                        View All

                    </a>

                </div>


                <div class="list-group list-group-flush">


                    <?php if (!empty($pendingReports)): ?>


                        <?php foreach ($pendingReports as $report): ?>

                            <?php
                            $reportId =
                                (int) ($report['id'] ?? 0);

                            $reporterName =
                                $report['reporter_name']
                                ?? 'Unknown User';

                            $reportedName =
                                $report['reported_name']
                                ?? 'Unknown User';

                            $reason =
                                $report['reason']
                                ?? 'other';

                            $reasonLabel = ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $reason
                                )
                            );
                            ?>


                            <div
                                class="list-group-item
                                       d-flex align-items-center">


                                <div class="flex-grow-1">

                                    <h6 class="mb-1">

                                        <?= e($reporterName) ?>

                                        <span class="text-muted">
                                            reported
                                        </span>

                                        <?= e($reportedName) ?>

                                    </h6>


                                    <small class="text-muted">

                                        <?= e($reasonLabel) ?>

                                        <?php if (!empty($report['created_at'])): ?>

                                            ·

                                            <?= e(
                                                timeAgo(
                                                    $report['created_at']
                                                )
                                            ) ?>

                                        <?php endif; ?>

                                    </small>

                                </div>


                                <div class="ms-2">

                                    <span
                                        class="badge bg-warning text-dark">

                                        Pending

                                    </span>

                                </div>

                            </div>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <div
                            class="list-group-item
                                   text-center text-muted py-5">

                            <i
                                class="fas fa-check-circle fa-2x
                                       text-success mb-2">
                            </i>

                            <p class="mb-0">

                                No pending reports.

                            </p>

                        </div>


                    <?php endif; ?>


                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
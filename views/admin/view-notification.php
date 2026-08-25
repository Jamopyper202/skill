<?php
/**
 * Admin View Notification
 */

$title = 'Notification Details';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         BREADCRUMB
    ========================================================== -->

    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'index') ?>">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="<?= url('Admin', 'notifications') ?>">
                    Notifications
                </a>
            </li>

            <li class="breadcrumb-item active">
                View Notification
            </li>

        </ol>

    </nav>


    <div class="row g-4">

        <!-- =====================================================
             NOTIFICATION DETAILS
        ====================================================== -->

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">

                    <h4 class="mb-0">

                        <i class="bi bi-bell-fill me-2"></i>

                        Notification Details

                    </h4>

                </div>


                <div class="card-body p-4">

                    <div class="mb-4">

                        <h2 class="h4 mb-2">

                            <?= e(
                                $notification['title']
                                ?? 'Notification'
                            ) ?>

                        </h2>

                        <div class="d-flex gap-2">

                            <span class="badge bg-primary">
                                System
                            </span>

                            <span class="text-muted">
                                <?= !empty(
                                    $notification['created_at']
                                )
                                    ? formatDate(
                                        $notification['created_at'],
                                        'F j, Y g:i A'
                                    )
                                    : 'Unknown date'
                                ?>
                            </span>

                        </div>

                    </div>


                    <hr>


                    <!-- MESSAGE -->

                    <div class="mb-4">

                        <h6 class="fw-bold mb-2">
                            Message
                        </h6>

                        <div class="bg-light rounded p-4">

                            <p class="mb-0">

                                <?= nl2br(
                                    e(
                                        $notification['message']
                                        ?? ''
                                    )
                                ) ?>

                            </p>

                        </div>

                    </div>


                    <!-- NOTIFICATION TYPE -->

                    <div>

                        <h6 class="fw-bold">
                            Notification Type
                        </h6>

                        <span class="badge bg-secondary">

                            <?= e(
                                ucfirst(
                                    $notification['type']
                                    ?? 'system'
                                )
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        <i class="bi bi-bar-chart me-2"></i>

                        Delivery Statistics

                    </h5>

                </div>


                <div class="card-body">

                    <div class="mb-4">

                        <small class="text-muted d-block">
                            Total Recipients
                        </small>

                        <h2 class="mb-0">

                            <?= (int) (
                                $notificationStats[
                                    'recipient_count'
                                ] ?? 0
                            ) ?>

                        </h2>

                    </div>


                    <div class="row g-3">

                        <div class="col-6">

                            <div
                                class="border rounded p-3 text-center"
                            >

                                <div
                                    class="text-success
                                           fs-4"
                                >

                                    <?= (int) (
                                        $notificationStats[
                                            'read_count'
                                        ] ?? 0
                                    ) ?>

                                </div>

                                <small class="text-muted">
                                    Read
                                </small>

                            </div>

                        </div>


                        <div class="col-6">

                            <div
                                class="border rounded p-3 text-center"
                            >

                                <div
                                    class="text-warning
                                           fs-4"
                                >

                                    <?= (int) (
                                        $notificationStats[
                                            'unread_count'
                                        ] ?? 0
                                    ) ?>

                                </div>

                                <small class="text-muted">
                                    Unread
                                </small>

                            </div>

                        </div>

                    </div>


                    <hr>


                    <a
                        href="<?= url(
                            'Admin',
                            'notifications'
                        ) ?>"
                        class="btn btn-outline-secondary w-100"
                    >

                        <i
                            class="bi bi-arrow-left me-1"
                        ></i>

                        Back to Notifications

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
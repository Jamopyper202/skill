<?php

/**
 * Admin View Single Report
 */

$title = 'Report #' . ($report['id'] ?? '?');
$activeTab = 'admin';

$reportId = (int) ($report['id'] ?? 0);

$status = $report['status'] ?? 'pending';

$statusClass = match ($status) {

    'pending' =>
    'warning text-dark',

    'investigating' =>
    'info',

    'resolved' =>
    'success',

    default =>
    'secondary'
};

$statusText = ucfirst(
    str_replace(
        '_',
        ' ',
        $status
    )
);

$reason = ucwords(
    str_replace(
        '_',
        ' ',
        $report['reason'] ?? ''
    )
);
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         BREADCRUMB
    ========================================================== -->

    <nav
        aria-label="breadcrumb"
        class="mb-4">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?= url('Admin', 'index') ?>">
                    Dashboard
                </a>

            </li>

            <li class="breadcrumb-item">

                <a href="<?= url('Admin', 'reports') ?>">
                    Reports
                </a>

            </li>

            <li class="breadcrumb-item active">

                Report #<?= $reportId ?>

            </li>

        </ol>

    </nav>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <div class="row g-4">

        <!-- =====================================================
             REPORT DETAILS
        ====================================================== -->

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div
                    class="card-header bg-white
                           d-flex justify-content-between
                           align-items-center">

                    <h4 class="mb-0">
                        Report Details
                    </h4>


                    <span
                        class="badge bg-<?= $statusClass ?>">
                        <?= e($statusText) ?>
                    </span>

                </div>


                <div class="card-body">

                    <dl class="row">

                        <!-- Reporter -->

                        <dt class="col-sm-3">Reporter</dt>

                        <dd class="col-sm-9">

                            <?= e($report['reporter_name'] ?? 'Unknown') ?>

                            <?php if (!empty($report['reporter_email'])): ?>

                                <span class="text-muted">
                                    (<?= e($report['reporter_email']) ?>)
                                </span>

                            <?php endif; ?>

                        </dd>


                        <!-- Reported User -->

                        <dt class="col-sm-3">Reported User</dt>

                        <dd class="col-sm-9">

                            <?= e($report['reported_user_name'] ?? 'Unknown') ?>

                            <?php if (!empty($report['reported_user_email'])): ?>

                                <span class="text-muted">
                                    (<?= e($report['reported_user_email']) ?>)
                                </span>

                            <?php endif; ?>

                        </dd>


                        <!-- Reason -->

                        <dt class="col-sm-3">
                            Reason
                        </dt>

                        <dd class="col-sm-9">

                            <span class="badge bg-secondary">

                                <?= e($reason) ?>

                            </span>

                        </dd>


                        <!-- Submitted -->

                        <dt class="col-sm-3">
                            Submitted
                        </dt>

                        <dd class="col-sm-9">

                            <?= !empty($report['created_at'])
                                ? formatDate(
                                    $report['created_at'],
                                    'F j, Y g:i A'
                                )
                                : 'N/A'
                            ?>

                        </dd>

                    </dl>


                    <hr>


                    <!-- Description -->

                    <h6>
                        Description
                    </h6>

                    <div class="bg-light p-3 rounded mb-4">

                        <p class="mb-0">

                            <?= nl2br(
                                e(
                                    $report['description']
                                        ?? 'No description provided.'
                                )
                            ) ?>

                        </p>

                    </div>


                    <!-- Related Exchange -->

                    <?php if (!empty($report['exchange_id'])): ?>

                        <h6>
                            Related Exchange
                        </h6>

                        <p>

                            <a
                                href="<?= url(
                                            'Admin',
                                            'viewExchange',
                                            [
                                                'id' =>
                                                (int) $report['exchange_id']
                                            ]
                                        ) ?>">

                                Exchange
                                #<?= (int) $report['exchange_id'] ?>

                            </a>

                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ACTIONS
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0">
                        Actions
                    </h5>

                </div>


                <div class="card-body">

                    <form
                        action="<?= url(
                                    'Admin',
                                    'updateReport'
                                ) ?>"
                        method="POST">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $reportId ?>">


                        <!-- Status -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-semibold"
                                for="status">
                                Update Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select">

                                <option
                                    value="pending"
                                    <?= $status === 'pending'
                                        ? 'selected'
                                        : '' ?>>
                                    Pending
                                </option>

                                <option
                                    value="reviewed"
                                    <?= $status === 'reviewed'
                                        ? 'selected'
                                        : '' ?>>
                                    Reviewed
                                </option>

                                <option
                                    value="resolved"
                                    <?= $status === 'resolved'
                                        ? 'selected'
                                        : '' ?>>
                                    Resolved
                                </option>

                                <option
                                    value="dismissed"
                                    <?= $status === 'dismissed'
                                        ? 'selected'
                                        : '' ?>>
                                    Dismissed
                                </option>

                            </select>

                        </div>


                        <!-- Admin Notes -->

                        <div class="mb-3">

                            <label
                                for="admin_notes"
                                class="form-label fw-semibold">
                                Admin Notes
                            </label>

                            <textarea
                                name="admin_notes"
                                id="admin_notes"
                                rows="4"
                                class="form-control"
                                placeholder="Internal notes about this report..."><?= e(
                                                                                        $report['admin_notes']
                                                                                            ?? ''
                                                                                    ) ?></textarea>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            <i class="bi bi-save me-1"></i>

                            Update Report

                        </button>

                    </form>


                    <hr>


                    <!-- Back -->

                    <a
                        href="<?= url(
                                    'Admin',
                                    'reports'
                                ) ?>"
                        class="btn btn-outline-secondary w-100">

                        <i class="bi bi-arrow-left me-1"></i>

                        Back to Reports

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * My Reports View
 */

$title = 'My Reports';
$activeTab = 'reports';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>


<div class="container py-5">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->
    <div class="row mb-4">

        <div class="col">

            <h1 class="h2 mb-0">
                <i class="fas fa-flag text-danger me-2"></i>
                My Reports
            </h1>

            <p class="text-muted mb-0">
                Track the status of your submitted reports
            </p>

        </div>

    </div>


    <!-- =========================================================
         FLASH MESSAGES
    ========================================================== -->
    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         NO REPORTS
    ========================================================== -->
    <?php if (empty($reports)): ?>

        <div class="card shadow border-0">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i
                        class="fas fa-clipboard-check fa-4x text-muted opacity-50">
                    </i>

                </div>

                <h3 class="h5 text-muted">
                    No reports submitted
                </h3>

                <p class="text-muted mb-0">
                    You haven't submitted any reports.
                </p>

            </div>

        </div>


    <?php else: ?>


        <!-- =====================================================
             REPORTS TABLE
        ====================================================== -->
        <div class="card shadow border-0">

            <div class="card-header bg-white border-0 py-3">

                <div class="d-flex align-items-center justify-content-between">

                    <div>

                        <h5 class="mb-0">
                            <i class="fas fa-list me-2 text-danger"></i>
                            Submitted Reports
                        </h5>

                        <small class="text-muted">
                            <?= count($reports) ?>
                            report<?= count($reports) !== 1 ? 's' : '' ?>
                        </small>

                    </div>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Reported User
                            </th>

                            <th>
                                Reason
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Submitted
                            </th>

                            <th class="text-center">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($reports as $report): ?>

                            <?php
                            $reportId = (int) ($report['id'] ?? 0);

                            $reportedUserName =
                                $report['reported_user_name']
                                ?? 'Unknown User';

                            $reportedUserAvatar =
                                $report['reported_user_avatar']
                                ?? null;

                            $reason =
                                $report['reason']
                                ?? 'other';

                            $status =
                                $report['status']
                                ?? 'pending';

                            $createdAt =
                                $report['created_at']
                                ?? '';

                            $reasonLabel = ucwords(
                                str_replace('_', ' ', $reason)
                            );
                            ?>

                            <tr>


                                <!-- =================================
                                     REPORTED USER
                                ================================== -->
                                <td>

                                    <div class="d-flex align-items-center">

                                        <img
                                            src="<?= e(
                                                uploadUrl(
                                                    $reportedUserAvatar
                                                )
                                            ) ?>"
                                            alt="<?= e(
                                                $reportedUserName
                                            ) ?>"
                                            class="rounded-circle me-2"
                                            width="36"
                                            height="36"
                                            style="object-fit: cover;">

                                        <span>
                                            <?= e(
                                                $reportedUserName
                                            ) ?>
                                        </span>

                                    </div>

                                </td>


                                <!-- =================================
                                     REASON
                                ================================== -->
                                <td>

                                    <span class="badge bg-secondary">

                                        <?= e($reasonLabel) ?>

                                    </span>

                                </td>


                                <!-- =================================
                                     STATUS
                                ================================== -->
                                <td>

                                    <?php if ($status === 'pending'): ?>

                                        <span
                                            class="badge bg-warning text-dark">

                                            <i
                                                class="fas fa-clock me-1">
                                            </i>

                                            Pending

                                        </span>


                                    <?php elseif ($status === 'reviewed'): ?>

                                        <span class="badge bg-info">

                                            <i
                                                class="fas fa-search me-1">
                                            </i>

                                            Under Review

                                        </span>


                                    <?php elseif ($status === 'resolved'): ?>

                                        <span class="badge bg-success">

                                            <i
                                                class="fas fa-check me-1">
                                            </i>

                                            Resolved

                                        </span>


                                    <?php elseif ($status === 'dismissed'): ?>

                                        <span class="badge bg-secondary">

                                            <i
                                                class="fas fa-times me-1">
                                            </i>

                                            Dismissed

                                        </span>


                                    <?php else: ?>

                                        <span class="badge bg-secondary">

                                            <?= e(
                                                ucfirst($status)
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     SUBMITTED TIME
                                ================================== -->
                                <td>

                                    <?php if (!empty($createdAt)): ?>

                                        <small class="text-muted">

                                            <?= e(
                                                timeAgo($createdAt)
                                            ) ?>

                                        </small>

                                    <?php else: ?>

                                        <small class="text-muted">
                                            N/A
                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     ACTIONS
                                ================================== -->
                                <td class="text-center">

                                    <?php if ($reportId > 0): ?>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reportModal<?= $reportId ?>"
                                            title="View Report">

                                            <i class="fas fa-eye"></i>

                                        </button>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- =====================================================
             REPORT DETAIL MODALS
        ====================================================== -->

        <?php foreach ($reports as $report): ?>

            <?php
            $reportId = (int) ($report['id'] ?? 0);

            $reportedUserName =
                $report['reported_user_name']
                ?? 'Unknown User';

            $reason =
                $report['reason']
                ?? 'other';

            $description =
                $report['description']
                ?? '';

            $status =
                $report['status']
                ?? 'pending';

            /*
             * The model/database may use either admin_notes
             * or admin_response depending on your implementation.
             */
            $adminResponse =
                $report['admin_notes']
                ?? $report['admin_response']
                ?? '';
            ?>

            <?php if ($reportId > 0): ?>

                <div
                    class="modal fade"
                    id="reportModal<?= $reportId ?>"
                    tabindex="-1"
                    aria-labelledby="reportModalLabel<?= $reportId ?>"
                    aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">


                            <!-- ==============================
                                 MODAL HEADER
                            =============================== -->
                            <div class="modal-header">

                                <h5
                                    class="modal-title"
                                    id="reportModalLabel<?= $reportId ?>">

                                    <i
                                        class="fas fa-flag text-danger me-2">
                                    </i>

                                    Report #<?= $reportId ?>

                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">
                                </button>

                            </div>


                            <!-- ==============================
                                 MODAL BODY
                            =============================== -->
                            <div class="modal-body">


                                <!-- Reported User -->
                                <div class="mb-3">

                                    <strong>
                                        Reported User:
                                    </strong>

                                    <div class="mt-1">

                                        <?= e(
                                            $reportedUserName
                                        ) ?>

                                    </div>

                                </div>


                                <!-- Reason -->
                                <div class="mb-3">

                                    <strong>
                                        Reason:
                                    </strong>

                                    <div class="mt-1">

                                        <span class="badge bg-secondary">

                                            <?= e(
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $reason
                                                    )
                                                )
                                            ) ?>

                                        </span>

                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="mb-3">

                                    <strong>
                                        Status:
                                    </strong>

                                    <div class="mt-1">

                                        <?php if ($status === 'pending'): ?>

                                            <span
                                                class="badge bg-warning text-dark">

                                                Pending

                                            </span>


                                        <?php elseif ($status === 'reviewed'): ?>

                                            <span
                                                class="badge bg-info">

                                                Under Review

                                            </span>


                                        <?php elseif ($status === 'resolved'): ?>

                                            <span
                                                class="badge bg-success">

                                                Resolved

                                            </span>


                                        <?php elseif ($status === 'dismissed'): ?>

                                            <span
                                                class="badge bg-secondary">

                                                Dismissed

                                            </span>


                                        <?php else: ?>

                                            <span
                                                class="badge bg-secondary">

                                                <?= e(
                                                    ucfirst($status)
                                                ) ?>

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- Description -->
                                <div class="mb-3">

                                    <strong>
                                        Description:
                                    </strong>

                                    <div
                                        class="bg-light p-3 rounded mt-2">

                                        <p class="mb-0">

                                            <?= nl2br(
                                                e($description)
                                            ) ?>

                                        </p>

                                    </div>

                                </div>


                                <!-- Admin Response -->
                                <?php if (!empty($adminResponse)): ?>

                                    <div class="mt-3">

                                        <strong>
                                            Admin Response:
                                        </strong>

                                        <div
                                            class="alert alert-info mt-2 mb-0">

                                            <p class="mb-0">

                                                <?= nl2br(
                                                    e($adminResponse)
                                                ) ?>

                                            </p>

                                        </div>

                                    </div>

                                <?php endif; ?>


                            </div>


                            <!-- ==============================
                                 MODAL FOOTER
                            =============================== -->
                            <div class="modal-footer">

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                    Close

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        <?php endforeach; ?>


    <?php endif; ?>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
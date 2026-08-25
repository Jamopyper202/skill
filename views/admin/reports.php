<?php
/**
 * Admin Reports Management
 */

$title = 'Manage Reports';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="row align-items-center mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-1">

                <i class="bi bi-flag-fill text-danger me-2"></i>

                Manage Reports

            </h1>

            <p class="text-muted mb-0">
                Review and manage reports submitted by users.
            </p>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         REPORTS TABLE
    ========================================================== -->

    <div class="card shadow-sm border-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>

                        <th class="text-center">
                            S/N
                        </th>

                        <th>
                            ID
                        </th>

                        <th>
                            Reporter
                        </th>

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
                            Date
                        </th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($reports)): ?>

                        <?php foreach ($reports as $index => $report): ?>

                            <?php
                            $reportId = (int) (
                                $report['id'] ?? 0
                            );

                            $status =
                                $report['status']
                                ?? 'pending';

                            $reporterName =
                                $report['reporter_name']
                                ?? 'Unknown';

                            $reportedUserName =
                                $report['reported_user_name']
                                ?? 'Unknown';

                            $reason =
                                $report['reason']
                                ?? 'Unknown';


                            // Status badge
                            switch ($status) {

                                case 'pending':

                                    $statusClass =
                                        'warning text-dark';

                                    break;

                                case 'investigating':

                                    $statusClass =
                                        'info';

                                    break;

                                case 'resolved':

                                    $statusClass =
                                        'success';

                                    break;

                                default:

                                    $statusClass =
                                        'secondary';

                                    break;
                            }


                            $statusText =
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $status
                                    )
                                );


                            $reasonText =
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $reason
                                    )
                                );
                            ?>


                            <tr
                                class="<?= $status === 'pending'
                                    ? 'table-warning'
                                    : '' ?>"
                            >

                                <!-- S/N -->

                                <td class="text-center">

                                    <?= ($offset ?? 0)
                                        + $index
                                        + 1 ?>

                                </td>


                                <!-- ID -->

                                <td>

                                    <span class="fw-semibold">

                                        #<?= $reportId ?>

                                    </span>

                                </td>


                                <!-- Reporter -->

                                <td>

                                    <?= e(
                                        $reporterName
                                    ) ?>

                                </td>


                                <!-- Reported User -->

                                <td>

                                    <?= e(
                                        $reportedUserName
                                    ) ?>

                                </td>


                                <!-- Reason -->

                                <td>

                                    <?= e(
                                        $reasonText
                                    ) ?>

                                </td>


                                <!-- Status -->

                                <td>

                                    <span
                                        class="badge bg-<?= $statusClass ?>"
                                    >

                                        <?= e(
                                            $statusText
                                        ) ?>

                                    </span>

                                </td>


                                <!-- Date -->

                                <td>

                                    <small class="text-muted">

                                        <?= !empty(
                                            $report['created_at']
                                        )
                                            ? timeAgo(
                                                $report['created_at']
                                            )
                                            : 'N/A' ?>

                                    </small>

                                </td>


                                <!-- Actions -->

                                <td class="text-center">

                                    <?php if ($reportId > 0): ?>

                                        <a
                                            href="<?= url(
                                                'Admin',
                                                'viewReport',
                                                [
                                                    'id' =>
                                                        $reportId
                                                ]
                                            ) ?>"
                                            class="btn btn-sm
                                                   btn-outline-primary"
                                            title="View Report"
                                        >

                                            <i
                                                class="bi bi-eye"
                                            ></i>

                                        </a>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <!-- =================================================
                             NO REPORTS
                        ================================================== -->

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-flag text-muted
                                           fs-1 d-block mb-3"
                                ></i>

                                <h5 class="text-muted">
                                    No reports found
                                </h5>

                                <p class="text-muted mb-0">
                                    There are currently no reports to review.
                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================================================
         PAGINATION
    ========================================================== -->

    <?php if (($totalPages ?? 1) > 1): ?>

        <nav
            class="mt-4"
            aria-label="Reports pagination"
        >

            <ul class="pagination justify-content-center">

                <!-- Previous -->

                <?php if (($currentPage ?? 1) > 1): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reports',
                                [
                                    'page' =>
                                        ($currentPage ?? 1) - 1
                                ]
                            ) ?>"
                        >

                            <i
                                class="bi bi-chevron-left"
                            ></i>

                        </a>

                    </li>

                <?php endif; ?>


                <!-- Page Numbers -->

                <?php for (
                    $i = 1;
                    $i <= ($totalPages ?? 1);
                    $i++
                ): ?>

                    <li
                        class="page-item
                            <?= ($currentPage ?? 1) == $i
                                ? 'active'
                                : '' ?>"
                    >

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reports',
                                [
                                    'page' => $i
                                ]
                            ) ?>"
                        >

                            <?= $i ?>

                        </a>

                    </li>

                <?php endfor; ?>


                <!-- Next -->

                <?php if (
                    ($currentPage ?? 1)
                    < ($totalPages ?? 1)
                ): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reports',
                                [
                                    'page' =>
                                        ($currentPage ?? 1) + 1
                                ]
                            ) ?>"
                        >

                            <i
                                class="bi bi-chevron-right"
                            ></i>

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </nav>

    <?php endif; ?>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
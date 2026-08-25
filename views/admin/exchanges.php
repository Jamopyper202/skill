<?php
/**
 * Admin Exchange Management
//  * @var array $page
 */
$title = 'Manage Exchanges';
$activeTab = 'admin';


/**
 * Admin Exchange Management
 */
$title = 'Manage Exchanges';
$activeTab = 'admin';

$page = isset($page) ? (int) $page : 1;
$limit = isset($limit) ? (int) $limit : (int) ITEMS_PER_PAGE;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$exchanges = $exchanges ?? [];
?>


<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <div class="row mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-0">

                <i class="fas fa-exchange-alt text-primary me-2"></i>

                Manage Exchanges

            </h1>

            <p class="text-muted mb-0">
                Monitor and manage skill exchange requests.
            </p>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- STATUS FILTERS -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap gap-2">

                <a
                    href="<?= url('Admin', 'exchanges') ?>"
                    class="btn btn-sm
                    <?= empty($_GET['status'])
                        ? 'btn-primary'
                        : 'btn-outline-primary' ?>"
                >
                    All
                </a>


                <a
                    href="<?= url('Admin', 'exchanges', ['status' => 'pending']) ?>"
                    class="btn btn-sm
                    <?= ($_GET['status'] ?? '') === 'pending'
                        ? 'btn-warning'
                        : 'btn-outline-warning' ?>"
                >
                    Pending
                </a>


                <a
                    href="<?= url('Admin', 'exchanges', ['status' => 'accepted']) ?>"
                    class="btn btn-sm
                    <?= ($_GET['status'] ?? '') === 'accepted'
                        ? 'btn-success'
                        : 'btn-outline-success' ?>"
                >
                    Accepted
                </a>


                <a
                    href="<?= url('Admin', 'exchanges', ['status' => 'in_progress']) ?>"
                    class="btn btn-sm
                    <?= ($_GET['status'] ?? '') === 'in_progress'
                        ? 'btn-info'
                        : 'btn-outline-info' ?>"
                >
                    In Progress
                </a>


                <a
                    href="<?= url('Admin', 'exchanges', ['status' => 'completed']) ?>"
                    class="btn btn-sm
                    <?= ($_GET['status'] ?? '') === 'completed'
                        ? 'btn-primary'
                        : 'btn-outline-primary' ?>"
                >
                    Completed
                </a>


                <a
                    href="<?= url('Admin', 'exchanges', ['status' => 'declined']) ?>"
                    class="btn btn-sm
                    <?= ($_GET['status'] ?? '') === 'declined'
                        ? 'btn-danger'
                        : 'btn-outline-danger' ?>"
                >
                    Declined
                </a>

            </div>

        </div>

    </div>


    <!-- EXCHANGE TABLE -->

    <div class="card shadow border-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>S/N</th>

                        <th>Requester</th>

                        <th>Receiver</th>

                        <th>Offered Skill</th>

                        <th>Requested Skill</th>

                        <th>Status</th>

                        <th>Date</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (!empty($exchanges)): ?>

                    <?php
                    $serialNumber =
                        (($page - 1) * $limit) + 1;
                    ?>


                    <?php foreach ($exchanges as $exchange): ?>

                        <tr>

                            <td>
                                <?= $serialNumber++ ?>
                            </td>


                            <td>
                                <?= e(
                                    $exchange['requester_name']
                                ) ?>
                            </td>


                            <td>
                                <?= e(
                                    $exchange['receiver_name']
                                ) ?>
                            </td>


                            <td>
                                <span class="badge bg-secondary">
                                    <?= e(
                                        $exchange['offered_skill_name']
                                    ) ?>
                                </span>
                            </td>


                            <td>
                                <span class="badge bg-primary">
                                    <?= e(
                                        $exchange['requested_skill_name']
                                    ) ?>
                                </span>
                            </td>


                            <td>

                                <?php
                                $status = $exchange['status'];

                                $badge = match ($status) {

                                    'pending'
                                        => 'warning text-dark',

                                    'accepted'
                                        => 'success',

                                    'in_progress'
                                        => 'info',

                                    'completed'
                                        => 'primary',

                                    'declined'
                                        => 'danger',

                                    default
                                        => 'secondary'
                                };
                                ?>

                                <span
                                    class="badge bg-<?= $badge ?>"
                                >
                                    <?= e(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $status
                                            )
                                        )
                                    ) ?>
                                </span>

                            </td>


                            <td>

                                <small>
                                    <?= !empty(
                                        $exchange['created_at']
                                    )
                                        ? timeAgo(
                                            $exchange['created_at']
                                        )
                                        : ''
                                    ?>
                                </small>

                            </td>


                            <td>

                                <a
                                    href="<?= url(
                                        'Admin',
                                        'viewExchange',
                                        [
                                            'id' =>
                                                $exchange['id']
                                        ]
                                    ) ?>"
                                    class="btn btn-sm
                                           btn-outline-primary"
                                    title="View Exchange"
                                >

                                    <i class="bi bi-eye"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5 text-muted"
                        >

                            <i
                                class="fas fa-exchange-alt
                                       fa-2x mb-3 d-block"
                            ></i>

                            No exchange requests found.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->

        <?php if ($totalPages > 1): ?>

            <div class="card-footer bg-white">

                <?php
                $currentPage = $page;
                ?>

                <nav aria-label="Exchange pagination">

                    <ul class="pagination
                               justify-content-center
                               mb-0">

                        <?php for (
                            $i = 1;
                            $i <= $totalPages;
                            $i++
                        ): ?>

                            <li
                                class="page-item
                                <?= $i === $currentPage
                                    ? 'active'
                                    : '' ?>"
                            >

                                <a
                                    class="page-link"
                                    href="<?= url(
                                        'Admin',
                                        'exchanges',
                                        array_filter([
                                            'page' => $i,
                                            'status' =>
                                                $_GET['status']
                                                    ?? ''
                                        ])
                                    ) ?>"
                                >

                                    <?= $i ?>

                                </a>

                            </li>

                        <?php endfor; ?>

                    </ul>

                </nav>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
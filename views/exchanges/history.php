<?php
/**
 * ============================================================================
 * Exchange History View
 * ============================================================================
 * Shows completed and archived skill exchanges.
 * Compatible with the current ExchangeController and Exchange model.
 * ============================================================================
 */

/**
 * @var array $exchanges
 */

$title = 'Exchange History';
$activeTab = 'exchanges';

$exchanges = $exchanges ?? [];

/*
 * Status badge colors
 */
$statusColors = [
    'completed' => 'success',
    'rejected'  => 'danger',
    'declined'  => 'danger',
    'cancelled' => 'secondary'
];

/*
 * Status labels
 */
$statusLabels = [
    'completed' => 'Completed',
    'rejected'  => 'Declined',
    'declined'  => 'Declined',
    'cancelled' => 'Cancelled'
];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Page Header -->
    <div class="row mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-0">
                <i class="fas fa-history text-primary me-2"></i>
                Exchange History
            </h1>

            <p class="text-muted mb-0">
                Your completed and archived skill exchanges
            </p>

        </div>

        <div class="col-md-4 text-md-end">

            <a href="<?php echo url('Exchange', 'index'); ?>"
               class="btn btn-outline-primary">

                <i class="fas fa-exchange-alt me-1"></i>
                Active Exchanges

            </a>

        </div>

    </div>


    <!-- Flash Messages -->
    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <?php if (empty($exchanges)): ?>

        <!-- Empty State -->

        <div class="card shadow border-0">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i class="fas fa-history fa-4x text-muted"></i>

                </div>

                <h3 class="h5 text-muted">
                    No exchange history yet
                </h3>

                <p class="text-muted">
                    Completed exchanges will appear here.
                </p>

                <a href="<?php echo url('Match', 'index'); ?>"
                   class="btn btn-primary">

                    <i class="fas fa-search me-1"></i>
                    Find a Match

                </a>

            </div>

        </div>


    <?php else: ?>

        <!-- ================================================================
             STATS SUMMARY
             ================================================================ -->

        <div class="row g-4 mb-4">

            <!-- Completed -->
            <div class="col-md-3">

                <div class="card bg-success text-white border-0">

                    <div class="card-body text-center">

                        <h3 class="mb-0">

                            <?php
                            echo count(
                                array_filter(
                                    $exchanges,
                                    fn($e) => ($e['status'] ?? '') === 'completed'
                                )
                            );
                            ?>

                        </h3>

                        <small>Completed</small>

                    </div>

                </div>

            </div>


            <!-- Declined -->
            <div class="col-md-3">

                <div class="card bg-danger text-white border-0">

                    <div class="card-body text-center">

                        <h3 class="mb-0">

                            <?php
                            echo count(
                                array_filter(
                                    $exchanges,
                                    fn($e) =>
                                        in_array(
                                            $e['status'] ?? '',
                                            ['rejected', 'declined'],
                                            true
                                        )
                                )
                            );
                            ?>

                        </h3>

                        <small>Declined</small>

                    </div>

                </div>

            </div>


            <!-- Cancelled -->
            <div class="col-md-3">

                <div class="card bg-secondary text-white border-0">

                    <div class="card-body text-center">

                        <h3 class="mb-0">

                            <?php
                            echo count(
                                array_filter(
                                    $exchanges,
                                    fn($e) =>
                                        ($e['status'] ?? '') === 'cancelled'
                                )
                            );
                            ?>

                        </h3>

                        <small>Cancelled</small>

                    </div>

                </div>

            </div>


            <!-- Total -->
            <div class="col-md-3">

                <div class="card bg-info text-white border-0">

                    <div class="card-body text-center">

                        <h3 class="mb-0">
                            <?php echo count($exchanges); ?>
                        </h3>

                        <small>Total</small>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================================================================
             HISTORY TABLE
             ================================================================ -->

        <div class="card shadow border-0">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0 align-middle">

                        <thead class="table-light">

                            <tr>

                                <th>Exchange</th>
                                <th>Partner</th>
                                <th>Status</th>
                                <th>Completed</th>
                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($exchanges as $exchange): ?>

                            <?php
                            $status = $exchange['status'] ?? 'cancelled';

                            $statusColor =
                                $statusColors[$status] ?? 'secondary';

                            $statusLabel =
                                $statusLabels[$status]
                                ?? ucfirst($status);
                            ?>

                            <tr>

                                <!-- =================================================
                                     SKILLS
                                     ================================================= -->

                                <td>

                                    <div class="d-flex flex-column">

                                        <span class="small">

                                            <span class="badge bg-primary">

                                                <?php echo e(
                                                    $exchange['offered_skill_name']
                                                    ?? 'Unknown'
                                                ); ?>

                                            </span>

                                            <i class="fas fa-arrows-alt-h text-muted mx-1"></i>

                                            <span class="badge bg-success">

                                                <?php echo e(
                                                    $exchange['requested_skill_name']
                                                    ?? 'Unknown'
                                                ); ?>

                                            </span>

                                        </span>

                                    </div>

                                </td>


                                <!-- =================================================
                                     PARTNER
                                     ================================================= -->

                                <td>

                                    <div class="d-flex align-items-center">

                                        <?php
                                        $avatar = uploadUrl(
                                            $exchange['other_user_avatar']
                                            ?? 'download.png'
                                        );
                                        ?>

                                        <img
                                            src="<?php echo e($avatar); ?>"
                                            alt="<?php echo e(
                                                $exchange['other_user_name']
                                                ?? 'User'
                                            ); ?>"
                                            class="rounded-circle me-2"
                                            width="32"
                                            height="32"
                                        >

                                        <a
                                            href="<?php echo url('Profile', 'view', [
                                                'id' => $exchange['other_user_id'] ?? 0
                                            ]); ?>"
                                            class="text-decoration-none"
                                        >

                                            <?php echo e(
                                                $exchange['other_user_name']
                                                ?? 'Unknown'
                                            ); ?>

                                        </a>

                                    </div>

                                </td>


                                <!-- =================================================
                                     STATUS
                                     ================================================= -->

                                <td>

                                    <span class="badge bg-<?php echo $statusColor; ?>">

                                        <?php if ($status === 'completed'): ?>

                                            <i class="fas fa-check me-1"></i>

                                        <?php elseif (
                                            in_array(
                                                $status,
                                                ['rejected', 'declined'],
                                                true
                                            )
                                        ): ?>

                                            <i class="fas fa-times me-1"></i>

                                        <?php else: ?>

                                            <i class="fas fa-ban me-1"></i>

                                        <?php endif; ?>

                                        <?php echo e($statusLabel); ?>

                                    </span>

                                </td>


                                <!-- =================================================
                                     DATE
                                     ================================================= -->

                                <td>

                                    <small class="text-muted">

                                        <?php
                                        echo !empty($exchange['updated_at'])
                                            ? formatDate(
                                                $exchange['updated_at'],
                                                'M j, Y'
                                            )
                                            : 'N/A';
                                        ?>

                                    </small>

                                </td>


                                <!-- =================================================
                                     ACTIONS
                                     ================================================= -->

                                <td>

                                    <div class="btn-group">

                                        <!-- View -->
                                        <a
                                            href="<?php echo url('Exchange', 'view', [
                                                'id' => $exchange['id']
                                            ]); ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Details"
                                        >

                                            <i class="fas fa-eye"></i>

                                        </a>


                                        <!-- Review -->
                                        <?php if (
                                            $status === 'completed'
                                            && empty($exchange['my_review'])
                                            && !empty($exchange['other_user_id'])
                                        ): ?>

                                            <a
                                                href="<?php echo url('Review', 'create', [
                                                    'exchange_id' => $exchange['id'],
                                                    'user_id' => $exchange['other_user_id']
                                                ]); ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Write Review"
                                            >

                                                <i class="fas fa-star"></i>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
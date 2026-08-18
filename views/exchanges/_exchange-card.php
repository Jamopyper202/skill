<?php
/**
 * Reusable Exchange Card
 *
 * @var array $exchange
 */

$exchange = $exchange ?? [];

$statusColors = [
    'pending'   => 'warning',
    'accepted'  => 'info',
    'rejected'  => 'danger',
    'completed' => 'success',
    'cancelled' => 'secondary'
];

$statusLabels = [
    'pending'   => 'Pending',
    'accepted'  => 'In Progress',
    'rejected'  => 'Rejected',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
];

$isInitiator = ($exchange['direction'] ?? '') === 'sent';
?>
<div class="col-md-6 col-lg-4">

    <div class="card h-100 shadow-sm border-0">

        <!-- Header -->
        <div class="card-header bg-transparent border-0 pt-3 pb-0">

            <div class="d-flex justify-content-between align-items-center">

                <span class="badge bg-<?php
                    echo $statusColors[$exchange['status']] ?? 'secondary';
                ?>">
                    <?php
                    echo e(
                        $statusLabels[$exchange['status']]
                        ?? ucfirst($exchange['status'] ?? 'Unknown')
                    );
                    ?>
                </span>

                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>

                    <?php
                    echo !empty($exchange['created_at'])
                        ? timeAgo($exchange['created_at'])
                        : '';
                    ?>
                </small>

            </div>

        </div>


        <!-- Body -->
        <div class="card-body">

            <!-- Exchange Partner -->
            <div class="d-flex align-items-center mb-3">

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
                    class="rounded-circle me-3"
                    width="48"
                    height="48"
                >

                <div>

                    <h5 class="card-title mb-0">

                        <a
                            href="<?php echo url('Profile', 'view', [
                                'id' => $exchange['other_user_id'] ?? 0
                            ]); ?>"
                            class="text-decoration-none"
                        >
                            <?php
                            echo e(
                                $exchange['other_user_name']
                                ?? 'User'
                            );
                            ?>
                        </a>

                    </h5>

                    <small class="text-muted">
                        Exchange Partner
                    </small>

                </div>

            </div>


            <!-- Skills -->
            <div class="exchange-skills mb-3">

                <!-- Offered Skill -->
                <div class="d-flex align-items-center mb-2">

                    <span class="badge bg-primary me-2">

                        <?php
                        echo $isInitiator
                            ? 'You offer'
                            : 'They offer';
                        ?>

                    </span>

                    <span class="small">

                        <?php
                        echo e(
                            $exchange['offered_skill_name']
                            ?? 'Unknown Skill'
                        );
                        ?>

                    </span>

                </div>


                <!-- Exchange Icon -->
                <div class="text-center my-1">

                    <i class="bi bi-arrow-down-up text-muted"></i>

                </div>


                <!-- Requested Skill -->
                <div class="d-flex align-items-center">

                    <span class="badge bg-success me-2">

                        <?php
                        echo $isInitiator
                            ? 'You learn'
                            : 'They learn';
                        ?>

                    </span>

                    <span class="small">

                        <?php
                        echo e(
                            $exchange['requested_skill_name']
                            ?? 'Unknown Skill'
                        );
                        ?>

                    </span>

                </div>

            </div>


            <!-- Message -->
            <?php if (!empty($exchange['message'])): ?>

                <p class="card-text small text-muted fst-italic">

                    "<?php
                    echo e(
                        truncate($exchange['message'], 80)
                    );
                    ?>"

                </p>

            <?php endif; ?>

        </div>


        <!-- Footer -->
        <div class="card-footer bg-transparent border-0">

            <a
                href="<?php echo url('Exchange', 'view', [
                    'id' => $exchange['id']
                ]); ?>"
                class="btn btn-outline-primary btn-sm w-100"
            >

                <i class="bi bi-eye me-1"></i>

                View Details

            </a>

        </div>

    </div>

</div>
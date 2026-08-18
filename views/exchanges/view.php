<?php

/**
 * ============================================================================
 * Exchange Detail View
 * ============================================================================
 * Displays details of a single skill exchange request.
 * Compatible with ExchangeController and Exchange model.
 * ============================================================================
 */
/**
 * @var array $exchange
 * @var array $messages
 */
$title = 'Exchange Details';
$activeTab = 'exchanges';

// $currentUserId = getCurrentUserId();

/*
 * Determine whether the logged-in user sent the request.
 */
// $isInitiator = ((int)($exchange['requester_id'] ?? 0) === (int)$currentUserId);
// 

// $isInitiator = (int)$exchange['requester_id'] === (int)getCurrentUserId();

// if ($isInitiator) {
//     $otherUserName = $exchange['receiver_name'];
//     $otherUserAvatar = $exchange['receiver_avatar'];
// } else {
//     $otherUserName = $exchange['requester_name'];
//     $otherUserAvatar = $exchange['requester_avatar'];
// }

// $otherUserAvatar = uploadUrl(
//     $otherUserAvatar ?: 'download.png'
// );

$currentUserId = getCurrentUserId();

$isInitiator = (int)$exchange['requester_id'] === (int)$currentUserId;

if ($isInitiator) {
    $otherUserId = (int)$exchange['receiver_id'];
    $otherUserName = $exchange['receiver_name'] ?? 'User';
    $otherUserAvatar = $exchange['receiver_avatar'] ?? 'download.png';
} else {
    $otherUserId = (int)$exchange['requester_id'];
    $otherUserName = $exchange['requester_name'] ?? 'User';
    $otherUserAvatar = $exchange['requester_avatar'] ?? 'download.png';
}

$otherUserAvatar = uploadUrl($otherUserAvatar);

/*
 * Get the other user's ID.
 */
// $otherUserId = $isInitiator
//     ? (int)($exchange['receiver_id'] ?? 0)
//     : (int)($exchange['requester_id'] ?? 0);

/*
 * Status colors and labels.
 */
$statusColors = [
    'pending'    => 'warning',
    'accepted'   => 'info',
    'in_progress' => 'primary',
    'rejected'   => 'danger',
    'declined'   => 'danger',
    'completed'  => 'success',
    'cancelled'  => 'secondary'
];

$statusLabels = [
    'pending'     => 'Pending',
    'accepted'    => 'Accepted',
    'in_progress' => 'In Progress',
    'rejected'    => 'Rejected',
    'declined'    => 'Declined',
    'completed'   => 'Completed',
    'cancelled'   => 'Cancelled'
];

$status = $exchange['status'] ?? 'pending';
?>

<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo url('Dashboard', 'index'); ?>">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="<?php echo url('Exchange', 'index'); ?>">
                    My Exchanges
                </a>
            </li>

            <li class="breadcrumb-item active">
                Exchange #<?php echo e((string)($exchange['id'] ?? '?')); ?>
            </li>
        </ol>
    </nav>

    <!-- Flash Messages -->
    <?php require_once BASE_PATH . '/views/layouts/flash-messages.php'; ?>

    <div class="row g-4">

        <!-- Main Content -->
        <div class="col-lg-8">

            <!-- Status -->
            <div class="alert alert-<?php echo $statusColors[$status] ?? 'secondary'; ?> d-flex align-items-center mb-4">

                <i class="fas fa-info-circle fa-lg me-3"></i>

                <div>
                    <strong>
                        Status:
                        <?php echo e($statusLabels[$status] ?? ucfirst($status)); ?>
                    </strong>

                    <p class="mb-0 small">

                        <?php if ($status === 'pending'): ?>

                            <?php if ($isInitiator): ?>

                                Waiting for the other user to respond to your exchange request.

                            <?php else: ?>

                                <?php echo e($exchange['requester_name'] ?? 'A user'); ?>
                                sent you an exchange request. Please respond.

                            <?php endif; ?>

                        <?php elseif ($status === 'accepted'): ?>

                            This exchange has been accepted. You can now begin your skill exchange.

                        <?php elseif ($status === 'in_progress'): ?>

                            This exchange is currently in progress.

                        <?php elseif ($status === 'completed'): ?>

                            This exchange has been completed.

                        <?php elseif ($status === 'rejected' || $status === 'declined'): ?>

                            This exchange request was declined.

                        <?php elseif ($status === 'cancelled'): ?>

                            This exchange was cancelled.

                        <?php endif; ?>

                    </p>
                </div>

            </div>


            <!-- Exchange Details Card -->
            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-white py-3">

                    <h2 class="h4 mb-0">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>
                        Exchange Details
                    </h2>

                </div>

                <div class="card-body p-4">

                    <!-- Users and Skills -->
                    <div class="row align-items-center mb-4">

                        <!-- Requester -->
                        <div class="col-md-5">

                            <div class="d-flex align-items-center">

                                <div>
                                    <small class="text-muted d-block">
                                        <?php echo $isInitiator ? 'You offer' : 'They offer'; ?>
                                    </small>

                                    <strong>
                                        <?php echo e(
                                            $exchange['offered_skill_name']
                                                ?? 'Unknown Skill'
                                        ); ?>
                                    </strong>

                                    <div class="small mt-1">

                                        <?php if ($isInitiator): ?>

                                            <span class="text-muted">
                                                You
                                            </span>

                                        <?php else: ?>

                                            <?php echo e(
                                                $exchange['requester_name']
                                                    ?? 'Unknown User'
                                            ); ?>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- Arrow -->
                        <div class="col-md-2 text-center">

                            <i class="fas fa-arrows-alt-h fa-2x text-muted"></i>

                        </div>


                        <!-- Requested Skill -->
                        <div class="col-md-5">

                            <div class="d-flex align-items-center justify-content-md-end">

                                <div class="text-md-end">

                                    <small class="text-muted d-block">
                                        <?php echo $isInitiator ? 'You learn' : 'They learn'; ?>
                                    </small>

                                    <strong>
                                        <?php echo e(
                                            $exchange['requested_skill_name']
                                                ?? 'Unknown Skill'
                                        ); ?>
                                    </strong>

                                    <div class="small mt-1">

                                        <?php if ($isInitiator): ?>

                                            <?php echo e(
                                                $exchange['receiver_name']
                                                    ?? 'Unknown User'
                                            ); ?>

                                        <?php else: ?>

                                            You

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <hr>


                    <!-- Exchange Information -->
                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <h6 class="text-muted mb-2">
                                <i class="far fa-calendar me-1"></i>
                                Requested On
                            </h6>

                            <p class="mb-0">
                                <?php
                                echo !empty($exchange['created_at'])
                                    ? formatDateTime($exchange['created_at'])
                                    : 'N/A';
                                ?>
                            </p>

                        </div>


                        <div class="col-md-6">

                            <h6 class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>
                                Last Updated
                            </h6>

                            <p class="mb-0">

                                <?php
                                echo !empty($exchange['updated_at'])
                                    ? timeAgo($exchange['updated_at'])
                                    : 'N/A';
                                ?>

                            </p>

                        </div>

                    </div>


                    <!-- Message -->
                    <?php if (!empty($exchange['message'])): ?>

                        <div class="bg-light p-3 rounded mb-4">

                            <h6 class="text-muted mb-2">

                                <i class="fas fa-comment-alt me-1"></i>

                                Message from
                                <?php echo e(
                                    $exchange['requester_name']
                                        ?? 'User'
                                ); ?>

                            </h6>

                            <p class="mb-0 fst-italic">

                                "<?php echo nl2br(
                                        e($exchange['message'])
                                    ); ?>"

                            </p>

                        </div>

                    <?php endif; ?>


                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2">

                        <!-- ACCEPT -->
                        <?php if (
                            $status === 'pending'
                            && !$isInitiator
                        ): ?>

                            <a
                                href="<?php echo url('Exchange', 'accept', [
                                            'id' => $exchange['id']
                                        ]); ?>"
                                class="btn btn-success"
                                onclick="return confirm('Accept this exchange request?');">

                                <i class="fas fa-check me-1"></i>

                                Accept Request

                            </a>


                            <!-- REJECT -->
                            <a
                                href="<?php echo url('Exchange', 'reject', [
                                            'id' => $exchange['id']
                                        ]); ?>"
                                class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to decline this exchange request?');">

                                <i class="fas fa-times me-1"></i>

                                Decline

                            </a>

                        <?php endif; ?>


                        <!-- START -->
                        <?php if ($status === 'accepted'): ?>

                            <a
                                href="<?php echo url('Exchange', 'start', [
                                            'id' => $exchange['id']
                                        ]); ?>"
                                class="btn btn-primary">

                                <i class="fas fa-play me-1"></i>

                                Start Exchange

                            </a>

                        <?php endif; ?>


                        <!-- COMPLETE -->
                        <?php if ($status === 'in_progress'): ?>

                            <a
                                href="<?php echo url('Exchange', 'complete', [
                                            'id' => $exchange['id']
                                        ]); ?>"
                                class="btn btn-success"
                                onclick="return confirm('Mark this exchange as completed?');">

                                <i class="fas fa-check-double me-1"></i>

                                Mark Complete

                            </a>


                            <!-- CANCEL -->
                            <a
                                href="<?php echo url('Exchange', 'cancel', [
                                            'id' => $exchange['id']
                                        ]); ?>"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Cancel this exchange?');">

                                <i class="fas fa-ban me-1"></i>

                                Cancel Exchange

                            </a>

                        <?php endif; ?>


                        <!-- MESSAGE -->
                        <?php if ($otherUserId > 0): ?>

                            <a
                                href="<?php echo url('Message', 'conversation', [
                                            'user_id' => $otherUserId
                                        ]); ?>"
                                class="btn btn-outline-primary">

                                <i class="fas fa-comment me-1"></i>

                                Message

                            </a>

                        <?php endif; ?>


                        <!-- BACK -->
                        <a
                            href="<?php echo url('Exchange', 'index'); ?>"
                            class="btn btn-outline-secondary">

                            <i class="fas fa-arrow-left me-1"></i>

                            Back to Exchanges

                        </a>

                    </div>

                </div>

            </div>


            <!-- Related Messages -->
            <?php if (!empty($messages)): ?>

                <div class="card shadow border-0">

                    <div class="card-header bg-white py-3">

                        <h3 class="h5 mb-0">

                            <i class="fas fa-comments text-primary me-2"></i>

                            Exchange Messages

                        </h3>

                    </div>

                    <div class="card-body">

                        <?php foreach ($messages as $message): ?>

                            <div class="border-bottom pb-3 mb-3">

                                <div class="d-flex justify-content-between">

                                    <strong>
                                        <?php echo e(
                                            $message['sender_name']
                                                ?? 'User'
                                        ); ?>
                                    </strong>

                                    <small class="text-muted">

                                        <?php echo !empty($message['created_at'])
                                            ? timeAgo($message['created_at'])
                                            : '';
                                        ?>

                                    </small>

                                </div>

                                <p class="mb-0 mt-2">

                                    <?php echo nl2br(
                                        e($message['message'] ?? '')
                                    ); ?>

                                </p>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>

        </div>


        <!-- Sidebar -->
        <div class="col-lg-4">

            <div class="card shadow border-0 mb-4">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <img
                            src="<?php echo e($otherUserAvatar); ?>"
                            alt="<?php echo e($otherUserName); ?>"
                            class="rounded-circle mb-3"
                            width="64"
                            height="64">

                    </div>

                    <h4 class="mb-1">

                        <?php echo e(
                            $isInitiator
                                ? ($exchange['receiver_name'] ?? 'User')
                                : ($exchange['requester_name'] ?? 'User')
                        ); ?>

                    </h4>

                    <p class="text-muted mb-3">
                        Exchange Partner
                    </p>

                    <?php if ($otherUserId > 0): ?>

                        <a
                            href="<?php echo url('Profile', 'view', [
                                        'id' => $otherUserId
                                    ]); ?>"
                            class="btn btn-outline-primary btn-sm w-100 mb-2">

                            <i class="fas fa-user me-1"></i>

                            View Profile

                        </a>

                        <a
                            href="<?php echo url('Message', 'conversation', [
                                        'user_id' => $otherUserId
                                    ]); ?>"
                            class="btn btn-primary btn-sm w-100">

                            <i class="fas fa-comment me-1"></i>

                            Send Message

                        </a>

                    <?php endif; ?>

                </div>

            </div>


            <!-- Timeline -->
            <div class="card shadow border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0">

                        <i class="fas fa-history me-2"></i>

                        Timeline

                    </h5>

                </div>

                <div class="card-body">

                    <ul class="list-unstyled mb-0">

                        <li class="d-flex mb-3">

                            <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-send-fill"></i>
                            </span>

                            <div class="ms-3">
                                <strong>Request Sent</strong>

                                <br>

                                <small class="text-muted">
                                    <?php echo !empty($exchange['created_at'])
                                        ? timeAgo($exchange['created_at'])
                                        : ''; ?>
                                </small>
                            </div>

                        </li>


                        <?php if ($status !== 'pending'): ?>

                            <li class="d-flex">

                                <span class="badge bg-<?php
                                                        echo in_array(
                                                            $status,
                                                            ['rejected', 'declined', 'cancelled'],
                                                            true
                                                        )
                                                            ? 'danger'
                                                            : 'success';
                                                        ?> rounded-circle p-2">

                                    <i class="fas fa-<?php
                                                        echo in_array(
                                                            $status,
                                                            ['rejected', 'declined', 'cancelled'],
                                                            true
                                                        )
                                                            ? 'times'
                                                            : 'check';
                                                        ?>"></i>

                                </span>

                                <div class="ms-3">

                                    <strong>
                                        <?php echo e(
                                            $statusLabels[$status]
                                                ?? ucfirst($status)
                                        ); ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        <?php
                                        echo !empty($exchange['updated_at'])
                                            ? timeAgo($exchange['updated_at'])
                                            : '';
                                        ?>

                                    </small>

                                </div>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
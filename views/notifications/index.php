<?php
/**
 * All Notifications View
 * 
 */


$title = 'Notifications';
$activeTab = 'notifications';

$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$totalPages = (int) ($totalPages ?? 1);
$unreadCount = (int) ($unreadCount ?? 0);
$readCount = (int) ($readCount ?? 0);
$filter = $filter ?? 'all';
$total = (int) ($total ?? 0);

// Notification model instance for icon/color/link helpers
$notificationHelper = new Notification();
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Page Header -->
    <div class="row mb-4">

        <div class="col-md-7">

            <h1 class="h2 mb-0">
                <i class="bi bi-bell-fill text-primary me-2"></i>
                Notifications
            </h1>

            <p class="text-muted mb-0">
                All your notifications in one place
            </p>

        </div>


        <div class="col-md-5 text-md-end">

            <?php if ($unreadCount > 0): ?>

                <a
                    href="<?php echo url('Notification', 'markAllRead'); ?>"
                    class="btn btn-outline-primary"
                >
                    <i class="bi bi-check2-all me-1"></i>
                    Mark All Read
                </a>

            <?php endif; ?>


            <?php if ($readCount > 0): ?>

                <a
                    href="<?php echo url('Notification', 'deleteAllRead'); ?>"
                    class="btn btn-outline-danger"
                    onclick="return confirm('Delete all read notifications?');"
                >
                    <i class="bi bi-trash me-1"></i>
                    Clear Read
                </a>

            <?php endif; ?>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- Filter Tabs -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body py-2">

            <div class="d-flex flex-wrap gap-2">

                <a
                    href="<?php echo url('Notification', 'index'); ?>"
                    class="btn btn-sm <?php echo $filter === 'all'
                        ? 'btn-primary'
                        : 'btn-outline-primary'; ?>"
                >
                    All

                    <span class="badge bg-secondary ms-1">
                        <?php echo $total; ?>
                    </span>
                </a>


                <a
                    href="<?php echo url(
                        'Notification',
                        'index'
                    ) . '&filter=unread'; ?>"
                    class="btn btn-sm <?php echo $filter === 'unread'
                        ? 'btn-primary'
                        : 'btn-outline-primary'; ?>"
                >
                    Unread

                    <?php if ($unreadCount > 0): ?>

                        <span class="badge bg-danger ms-1">
                            <?php echo $unreadCount; ?>
                        </span>

                    <?php endif; ?>

                </a>


                <a
                    href="<?php echo url(
                        'Notification',
                        'index'
                    ) . '&filter=read'; ?>"
                    class="btn btn-sm <?php echo $filter === 'read'
                        ? 'btn-primary'
                        : 'btn-outline-primary'; ?>"
                >
                    Read

                    <span class="badge bg-secondary ms-1">
                        <?php echo $readCount; ?>
                    </span>
                </a>

            </div>

        </div>

    </div>


    <!-- Notifications -->
    <?php if (empty($notifications)): ?>

        <div class="card shadow border-0">

            <div class="card-body text-center py-5">

                <div class="mb-3">

                    <i class="bi bi-bell-slash fs-1 text-muted"></i>

                </div>


                <h3 class="h5 text-muted">
                    No notifications
                </h3>


                <p class="text-muted mb-0">

                    <?php if ($filter === 'unread'): ?>

                        You're all caught up!

                    <?php elseif ($filter === 'read'): ?>

                        You don't have any read notifications.

                    <?php else: ?>

                        You don't have any notifications yet.

                    <?php endif; ?>

                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow border-0">

            <div class="list-group list-group-flush">

                <?php foreach ($notifications as $notif): ?>

                    <?php

                    $notificationId =
                        (int) ($notif['id'] ?? 0);

                    $type =
                        $notif['type'] ?? 'system';

                    $icon =
                        $notificationHelper->getIcon($type);

                    $color =
                        $notificationHelper->getColor($type);

                    $link =
                        $notificationHelper->getLink($notif);

                    $isUnread =
                        empty($notif['is_read']);

                    ?>

                    <div
                        class="list-group-item py-3
                        <?php echo $isUnread
                            ? 'bg-light'
                            : ''; ?>"
                    >

                        <div class="d-flex align-items-center">

                            <!-- Notification Icon -->
                            <div class="flex-shrink-0 me-3">

                                <span
                                    class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                    style="
                                        width: 48px;
                                        height: 48px;
                                    "
                                >

                                    <i
                                        class="bi <?php echo e($icon); ?> fs-5 <?php echo e($color); ?>"
                                    ></i>

                                </span>

                            </div>


                            <!-- Notification Content -->
                            <div class="flex-grow-1">

                                <div
                                    class="d-flex justify-content-between align-items-start"
                                >

                                    <div>

                                        <h6
                                            class="mb-1
                                            <?php echo $isUnread
                                                ? 'fw-bold'
                                                : ''; ?>"
                                        >
                                            <?php echo e(
                                                $notif['title']
                                                ?? 'Notification'
                                            ); ?>
                                        </h6>


                                        <p class="mb-1 text-muted">

                                            <?php echo e(
                                                $notif['message']
                                                ?? ''
                                            ); ?>

                                        </p>


                                        <small class="text-muted">

                                            <i class="bi bi-clock me-1"></i>

                                            <?php echo timeAgo(
                                                $notif['created_at']
                                                ?? ''
                                            ); ?>

                                        </small>

                                    </div>


                                    <!-- Unread Indicator -->
                                    <?php if ($isUnread): ?>

                                        <span
                                            class="badge bg-primary rounded-pill ms-2"
                                        >
                                            New
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <!-- Actions -->
                            <div class="flex-shrink-0 ms-3">

                                <?php if ($link): ?>

                                    <a
                                        href="<?php echo e($link); ?>"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>

                                <?php endif; ?>


                                <?php if ($isUnread): ?>

                                    <a
                                        href="<?php echo url(
                                            'Notification',
                                            'markRead',
                                            [
                                                'id' => $notificationId
                                            ]
                                        ); ?>"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Mark as read"
                                    >
                                        <i class="bi bi-check-lg"></i>
                                    </a>

                                <?php endif; ?>


                                <a
                                    href="<?php echo url(
                                        'Notification',
                                        'delete',
                                        [
                                            'id' => $notificationId
                                        ]
                                    ); ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete"
                                    onclick="return confirm('Delete this notification?');"
                                >
                                    <i class="bi bi-trash"></i>
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>

            <nav class="mt-4">

                <ul class="pagination justify-content-center">

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                        <?php

                        $pageUrl =
                            url('Notification', 'index')
                            . '&page=' . $i;

                        if ($filter !== 'all') {
                            $pageUrl .=
                                '&filter=' . urlencode($filter);
                        }

                        ?>

                        <li
                            class="page-item
                            <?php echo $currentPage === $i
                                ? 'active'
                                : ''; ?>"
                        >

                            <a
                                class="page-link"
                                href="<?php echo e($pageUrl); ?>"
                            >
                                <?php echo $i; ?>
                            </a>

                        </li>

                    <?php endfor; ?>

                </ul>

            </nav>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
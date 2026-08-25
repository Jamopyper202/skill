<?php

/**
 * Admin Bulk Notifications View
 */

$title = 'Send Notifications';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="row mb-4">

        <div class="col">

            <h1 class="h2 mb-0">

                <i class="bi bi-bell-fill text-primary me-2"></i>

                Send Bulk Notifications

            </h1>

            <p class="text-muted mb-0">

                Send announcements to all users
                or active users.

            </p>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <div class="row g-4">

        <!-- =====================================================
             COMPOSE
        ====================================================== -->

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h4 class="mb-0">

                        <i class="bi bi-send me-2"></i>

                        Compose Notification

                    </h4>

                </div>


                <div class="card-body p-4">

                    <form
                        action="<?= url(
                                    'Admin',
                                    'notifications'
                                ) ?>"
                        method="POST"
                        id="notificationForm">



                        <!-- =================================================
                             TARGET AUDIENCE
                        ================================================== -->

                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                Target Audience

                            </label>


                            <div class="row g-2">

                                <div class="col-md-6">

                                    <div
                                        class="form-check card p-3 border">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="target"
                                            id="target_all"
                                            value="all"
                                            checked>

                                        <label
                                            class="form-check-label"
                                            for="target_all">

                                            <strong>
                                                All Users
                                            </strong>

                                            <small
                                                class="text-muted d-block">
                                                Send to every registered user.
                                            </small>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div
                                        class="form-check card p-3 border">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="target"
                                            id="target_active"
                                            value="active">

                                        <label
                                            class="form-check-label"
                                            for="target_active">

                                            <strong>
                                                Active Users Only
                                            </strong>

                                            <small
                                                class="text-muted d-block">
                                                Exclude inactive accounts.
                                            </small>

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             TYPE
                        ================================================== -->

                        <div class="mb-3">
                            <div class="mb-3">

                                <label
                                    for="type"
                                    class="form-label fw-bold">
                                    Notification Type
                                </label>

                                <select
                                    name="type"
                                    id="type"
                                    class="form-select"
                                    disabled>

                                    <option value="system">
                                        System Notification
                                    </option>

                                </select>

                                <div class="form-text">
                                    Admin announcements are sent as system notifications.
                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             TITLE
                        ================================================== -->

                        <div class="mb-3">

                            <label
                                for="title"
                                class="form-label fw-bold">
                                Title
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                required
                                maxlength="200"
                                placeholder="e.g. New Feature: Video Chat">

                        </div>


                        <!-- =================================================
                             MESSAGE
                        ================================================== -->

                        <div class="mb-3">

                            <label
                                for="message"
                                class="form-label fw-bold">
                                Message
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                class="form-control"
                                required
                                maxlength="1000"
                                placeholder="Write your notification message..."></textarea>


                            <div
                                class="form-text
                                       d-flex
                                       justify-content-between">

                                <span>
                                    Keep it concise and clear.
                                </span>

                                <span id="charCount">
                                    0 / 1000
                                </span>

                            </div>

                        </div>


                        <!-- =================================================
                             LINK
                        ================================================== -->

                        <!-- <div class="mb-4">

                            <label
                                for="link"
                                class="form-label fw-bold"
                            >

                                Link

                                <span
                                    class="text-muted fw-normal"
                                >
                                    (Optional)
                                </span>

                            </label>

                            <input
                                type="url"
                                name="link"
                                id="link"
                                class="form-control"
                                placeholder="https://example.com/page"
                            >

                            <div class="form-text">

                                Users can click this link
                                from the notification.

                            </div>

                        </div> -->


                        <!-- =================================================
                             BUTTONS
                        ================================================== -->

                        <div
                            class="d-flex
                                   justify-content-between
                                   gap-2">

                            <a
                                href="<?= url(
                                            'Admin',
                                            'index'
                                        ) ?>"
                                class="btn btn-outline-secondary">

                                <i
                                    class="bi bi-arrow-left me-1"></i>

                                Back to Dashboard

                            </a>


                            <button
                                type="submit"
                                class="btn btn-primary btn-lg"
                                onclick="return confirm(
                                    'Send this notification to all targeted users?'
                                )">

                                <i
                                    class="bi bi-send me-2"></i>

                                Send Notification

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =====================================================
             RECENT NOTIFICATIONS
        ====================================================== -->

        <div class="col-lg-4">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0">

                        <i
                            class="bi bi-clock-history me-2"></i>

                        Recent Notifications

                    </h5>

                </div>


                <div
                    class="list-group list-group-flush"
                    style="
                        max-height: 500px;
                        overflow-y: auto;
                    ">

                    <?php if (!empty($recentNotifications)): ?>

                        <?php foreach (
                            $recentNotifications
                            as $notif
                        ): ?>

                            <div
                                class="list-group-item py-3">

                                <div class="d-flex justify-content-between align-items-center mt-2">

                                    <small class="text-muted">
                                        <?= !empty($notif['created_at'])
                                            ? timeAgo($notif['created_at'])
                                            : ''
                                        ?>
                                    </small>

                                    <a
                                        href="<?= url(
                                                    'Admin',
                                                    'viewNotification',
                                                    [
                                                        'id' => (int) $notif['id']
                                                    ]
                                                ) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <div
                            class="list-group-item
                                   text-center
                                   text-muted
                                   py-4">

                            <i
                                class="bi bi-bell-slash
                                       fs-3 d-block mb-2"></i>

                            No notifications sent yet.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
    const messageField =
        document.getElementById('message');

    const charCount =
        document.getElementById('charCount');


    if (messageField && charCount) {

        messageField.addEventListener(
            'input',
            function() {

                charCount.textContent =
                    this.value.length + ' / 1000';

            }
        );

    }
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
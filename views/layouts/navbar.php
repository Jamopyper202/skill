<?php
/**
 * ============================================================================
 * SkillSwap Navigation Bar
 * ============================================================================
 */

$unreadNotifications = 0;
$unreadMessages = 0;

if (isLoggedIn()) {

    $notifModel = new Notification();
    $unreadNotifications = (int)
        $notifModel->countUnread(getCurrentUserId());

    $msgModel = new Message();
    $unreadMessages = (int)
        $msgModel->countUnread(getCurrentUserId());
}


/*
|--------------------------------------------------------------------------
| User Avatar
|--------------------------------------------------------------------------
*/

$userAvatar = asset('images/download.png');

if (
    isLoggedIn()
    && !empty($_SESSION['user_picture'])
    && $_SESSION['user_picture'] !== 'download.png'
) {
    $userAvatar = uploadUrl($_SESSION['user_picture']);
}
?>

<nav class="navbar navbar-expand-lg sticky-top">

    <div class="container">

        <!-- =========================================================
             BRAND
             ========================================================= -->

        <a
            class="navbar-brand d-flex align-items-center"
            href="<?php echo url('Dashboard', 'index'); ?>"
        >

            <span
                class="d-flex align-items-center justify-content-center me-2"
                style="
                    width: 38px;
                    height: 38px;
                    border-radius: 10px;
                    background: var(--primary-color);
                    color: white;
                "
            >
                <i class="bi bi-arrow-left-right"></i>
            </span>

            <span>
                <?php echo e(APP_NAME); ?>
            </span>

        </a>


        <!-- =========================================================
             MOBILE TOGGLE
             ========================================================= -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <i class="bi bi-list fs-4"></i>

        </button>


        <!-- =========================================================
             NAVIGATION
             ========================================================= -->

        <div
            class="collapse navbar-collapse"
            id="navbarNav"
        >

            <?php if (isLoggedIn()): ?>

                <!-- Main Navigation -->

                <ul class="navbar-nav me-auto ms-lg-3">

                    <!-- Dashboard -->
                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo isActive('Dashboard'); ?>"
                            href="<?php echo url('Dashboard', 'index'); ?>"
                        >
                            <i class="bi bi-grid-1x2-fill me-1"></i>
                            Dashboard
                        </a>

                    </li>


                    <!-- Skills -->
                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo isActive('Skill'); ?>"
                            href="<?php echo url('Skill', 'index'); ?>"
                        >
                            <i class="bi bi-lightning-charge-fill me-1"></i>
                            My Skills
                        </a>

                    </li>


                    <!-- Matches -->
                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo isActive('Match'); ?>"
                            href="<?php echo url('Match', 'index'); ?>"
                        >
                            <i class="bi bi-people-fill me-1"></i>
                            Matches
                        </a>

                    </li>


                    <!-- Exchanges -->
                    <li class="nav-item">

                        <a
                            class="nav-link <?php echo isActive('Exchange'); ?>"
                            href="<?php echo url('Exchange', 'index'); ?>"
                        >
                            <i class="bi bi-arrow-left-right me-1"></i>
                            Exchanges
                        </a>

                    </li>


                    <!-- Messages -->
                    <li class="nav-item">

                        <a
                            class="nav-link position-relative <?php echo isActive('Message'); ?>"
                            href="<?php echo url('Message', 'index'); ?>"
                        >

                            <i class="bi bi-chat-dots-fill me-1"></i>
                            Messages

                            <?php if ($unreadMessages > 0): ?>

                                <span
                                    class="badge rounded-pill bg-danger ms-1"
                                >
                                    <?php echo $unreadMessages; ?>
                                </span>

                            <?php endif; ?>

                        </a>

                    </li>

                </ul>


                <!-- =====================================================
                     RIGHT SIDE
                     ===================================================== -->

                <ul class="navbar-nav align-items-lg-center">


                    <!-- =================================================
                         NOTIFICATIONS
                         ================================================= -->

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link position-relative px-3"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Notifications"
                        >

                            <i class="bi bi-bell-fill fs-5"></i>

                            <?php if ($unreadNotifications > 0): ?>

                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: .6rem;"
                                >
                                    <?php echo $unreadNotifications; ?>
                                </span>

                            <?php endif; ?>

                        </a>


                        <!-- Notification Dropdown -->

                        <div
                            class="dropdown-menu dropdown-menu-end notification-dropdown p-0 mt-2"
                        >

                            <div
                                class="dropdown-header d-flex justify-content-between align-items-center p-3"
                            >

                                <div>

                                    <h6 class="mb-0 fw-bold">
                                        Notifications
                                    </h6>

                                    <?php if ($unreadNotifications > 0): ?>

                                        <small class="text-muted">
                                            <?php echo $unreadNotifications; ?>
                                            unread
                                        </small>

                                    <?php endif; ?>

                                </div>


                                <?php if ($unreadNotifications > 0): ?>

                                    <a
                                        href="<?php echo url(
                                            'Notification',
                                            'markAllRead'
                                        ); ?>"
                                        class="small text-decoration-none"
                                    >
                                        Mark all read
                                    </a>

                                <?php endif; ?>

                            </div>


                            <div class="notification-list">

                                <?php

                                $recentNotifs = [];

                                $notifModel = new Notification();

                                $recentNotifs =
                                    $notifModel->getRecent(
                                        getCurrentUserId(),
                                        5
                                    );

                                ?>


                                <?php if (empty($recentNotifs)): ?>

                                    <div
                                        class="text-center py-4 text-muted"
                                    >

                                        <i
                                            class="bi bi-bell-slash fs-2 d-block mb-2"
                                        ></i>

                                        <small>
                                            No notifications
                                        </small>

                                    </div>

                                <?php else: ?>

                                    <?php foreach ($recentNotifs as $notif): ?>

                                        <?php

                                        $notifLink =
                                            $notifModel->getLink($notif);

                                        $notifIcon =
                                            $notifModel->getIcon(
                                                $notif['type']
                                            );

                                        $notifColor =
                                            $notifModel->getColor(
                                                $notif['type']
                                            );

                                        ?>

                                        <a
                                            href="<?php echo e(
                                                url(
                                                    'Notification',
                                                    'markRead',
                                                    [
                                                        'id' =>
                                                            $notif['id'],
                                                        'redirect' => 1
                                                    ]
                                                )
                                            ); ?>"
                                            class="text-decoration-none text-dark"
                                        >

                                            <div
                                                class="notification-item
                                                <?php echo empty(
                                                    $notif['is_read']
                                                )
                                                    ? 'unread'
                                                    : ''; ?>"
                                            >

                                                <div
                                                    class="d-flex align-items-start"
                                                >

                                                    <div
                                                        class="flex-shrink-0 me-2"
                                                    >

                                                        <span
                                                            class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                                            style="
                                                                width: 36px;
                                                                height: 36px;
                                                            "
                                                        >

                                                            <i
                                                                class="bi <?php echo e($notifIcon); ?> text-<?php echo e($notifColor); ?>"
                                                            ></i>

                                                        </span>

                                                    </div>


                                                    <div
                                                        class="flex-grow-1"
                                                    >

                                                        <div
                                                            class="small fw-bold"
                                                        >
                                                            <?php echo e(
                                                                $notif['title']
                                                            ); ?>
                                                        </div>


                                                        <div
                                                            class="small text-muted text-truncate"
                                                            style="
                                                                max-width: 220px;
                                                            "
                                                        >
                                                            <?php echo e(
                                                                $notif['message']
                                                            ); ?>
                                                        </div>


                                                        <div
                                                            class="small text-muted mt-1"
                                                        >
                                                            <i
                                                                class="bi bi-clock me-1"
                                                            ></i>

                                                            <?php echo timeAgo(
                                                                $notif['created_at']
                                                            ); ?>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </a>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </div>


                            <div class="dropdown-divider m-0"></div>


                            <a
                                class="dropdown-item text-center py-3 fw-semibold"
                                href="<?php echo url(
                                    'Notification',
                                    'index'
                                ); ?>"
                            >
                                View All Notifications
                                <i
                                    class="bi bi-arrow-right ms-1"
                                ></i>
                            </a>

                        </div>

                    </li>


                    <!-- =================================================
                         USER PROFILE
                         ================================================= -->

                    <li class="nav-item dropdown ms-lg-2">

                        <a
                            class="nav-link dropdown-toggle d-flex align-items-center"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >

                            <img
                                src="<?php echo e($userAvatar); ?>"
                                alt="Profile"
                                class="avatar-sm me-2"
                            >

                            <span class="d-none d-lg-inline fw-semibold">

                                <?php echo e(
                                    $_SESSION['user_name'] ?? 'User'
                                ); ?>

                            </span>

                        </a>


                        <ul
                            class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                        >

                            <!-- Profile Header -->

                            <li>

                                <div class="px-3 py-2">

                                    <small class="text-muted">
                                        Signed in as
                                    </small>

                                    <div class="fw-bold">
                                        <?php echo e(
                                            $_SESSION['user_name'] ?? 'User'
                                        ); ?>
                                    </div>

                                </div>

                            </li>


                            <li>
                                <hr class="dropdown-divider">
                            </li>


                            <!-- Profile -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?php echo url(
                                        'Profile',
                                        'index'
                                    ); ?>"
                                >

                                    <i
                                        class="bi bi-person-circle me-2"
                                    ></i>

                                    My Profile

                                </a>

                            </li>


                            <!-- Portfolio -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?php echo url(
                                        'Portfolio',
                                        'index'
                                    ); ?>"
                                >

                                    <i
                                        class="bi bi-briefcase me-2"
                                    ></i>

                                    Portfolio

                                </a>

                            </li>


                            <!-- Reviews -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?php echo url(
                                        'Review',
                                        'index'
                                    ); ?>"
                                >

                                    <i
                                        class="bi bi-star-fill me-2"
                                    ></i>

                                    My Reviews

                                </a>

                            </li>


                            <?php if (isAdmin()): ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                <li>

                                    <a
                                        class="dropdown-item text-primary"
                                        href="<?php echo url(
                                            'Admin',
                                            'index'
                                        ); ?>"
                                    >

                                        <i
                                            class="bi bi-shield-lock-fill me-2"
                                        ></i>

                                        Admin Panel

                                    </a>

                                </li>

                            <?php endif; ?>


                            <li>
                                <hr class="dropdown-divider">
                            </li>


                            <!-- Logout -->

                            <li>

                                <a
                                    class="dropdown-item text-danger"
                                    href="<?php echo url(
                                        'Auth',
                                        'logout'
                                    ); ?>"
                                >

                                    <i
                                        class="bi bi-box-arrow-right me-2"
                                    ></i>

                                    Logout

                                </a>

                            </li>

                        </ul>

                    </li>

                </ul>


            <?php else: ?>

                <!-- =====================================================
                     GUEST NAVIGATION
                     ===================================================== -->

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?php echo url('Auth', 'login'); ?>"
                        >

                            <i
                                class="bi bi-box-arrow-in-right me-1"
                            ></i>

                            Login

                        </a>

                    </li>


                    <li class="nav-item ms-lg-2">

                        <a
                            class="btn btn-primary px-4"
                            href="<?php echo url('Auth', 'register'); ?>"
                        >

                            <i
                                class="bi bi-person-plus me-1"
                            ></i>

                            Get Started

                        </a>

                    </li>

                </ul>

            <?php endif; ?>

        </div>

    </div>

</nav>

<?php
/**
 * Check whether a controller is currently active.
 */
function isActive(string $controller): string
{
    $currentController =
        $_GET['controller'] ?? 'Dashboard';

    return strtolower($currentController)
        === strtolower($controller)
        ? 'active'
        : '';
}
?>
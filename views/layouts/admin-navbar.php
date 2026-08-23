<?php
/**
 * ============================================================================
 * SkillSwap Admin Navigation Bar
 * ============================================================================
 */

$unreadNotifications = 0;

if (isLoggedIn()) {

    $notifModel = new Notification();

    $unreadNotifications =
        (int) $notifModel->countUnread(
            getCurrentUserId()
        );
}


/*
|--------------------------------------------------------------------------
| Admin Avatar
|--------------------------------------------------------------------------
*/

$adminAvatar = asset('images/download.png');

if (
    isLoggedIn()
    && !empty($_SESSION['user_picture'])
    && $_SESSION['user_picture'] !== 'download.png'
) {
    $adminAvatar = uploadUrl(
        $_SESSION['user_picture']
    );
}
?>


<nav class="navbar navbar-expand-lg sticky-top">

    <div class="container-fluid px-4">


        <!-- =========================================================
             BRAND
        ========================================================== -->

        <a
            class="navbar-brand d-flex align-items-center"
            href="<?= url('Admin', 'index') ?>">

            <span
                class="d-flex align-items-center justify-content-center me-2"
                style="
                    width: 38px;
                    height: 38px;
                    border-radius: 10px;
                    background: var(--primary-color);
                    color: white;
                ">

                <i class="bi bi-shield-lock-fill"></i>

            </span>


            <span>

                <?= e(APP_NAME) ?>

                <small
                    class="text-primary ms-1"
                    style="font-size: .7rem;">

                    ADMIN

                </small>

            </span>

        </a>


        <!-- =========================================================
             MOBILE TOGGLE
        ========================================================== -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#adminNavbarNav"
            aria-controls="adminNavbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <i class="bi bi-list fs-4"></i>

        </button>


        <!-- =========================================================
             NAVIGATION
        ========================================================== -->

        <div
            class="collapse navbar-collapse"
            id="adminNavbarNav">


            <ul class="navbar-nav me-auto ms-lg-3">


                <!-- Dashboard -->

                <li class="nav-item">

                    <a
                        class="nav-link <?= isActive('Admin') ?>"
                        href="<?= url('Admin', 'index') ?>">

                        <i class="bi bi-speedometer2 me-1"></i>

                        Dashboard

                    </a>

                </li>


                <!-- Users -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= url('Admin', 'users') ?>">

                        <i class="bi bi-people-fill me-1"></i>

                        Users

                    </a>

                </li>


                <!-- Skills -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= url('Admin', 'skills') ?>">

                        <i class="bi bi-lightbulb-fill me-1"></i>

                        Skills

                    </a>

                </li>


                <!-- Categories -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= url('Admin', 'categories') ?>">

                        <i class="bi bi-tags-fill me-1"></i>

                        Categories

                    </a>

                </li>


                <!-- Exchanges -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= url('Admin', 'exchanges') ?>">

                        <i class="bi bi-arrow-left-right me-1"></i>

                        Exchanges

                    </a>

                </li>


                <!-- Reviews -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= url('Admin', 'reviews') ?>">

                        <i class="bi bi-star-fill me-1"></i>

                        Reviews

                    </a>

                </li>


                <!-- Reports -->

                <li class="nav-item">

                    <a
                        class="nav-link position-relative"
                        href="<?= url('Admin', 'reports') ?>">

                        <i class="bi bi-flag-fill me-1"></i>

                        Reports

                    </a>

                </li>

            </ul>


            <!-- =====================================================
                 RIGHT SIDE
            ====================================================== -->

            <ul class="navbar-nav align-items-lg-center">


                <!-- Notifications -->

                <li class="nav-item dropdown">

                    <a
                        class="nav-link position-relative px-3"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="Notifications">

                        <i class="bi bi-bell-fill fs-5"></i>


                        <?php if ($unreadNotifications > 0): ?>

                            <span
                                class="position-absolute top-0 start-100
                                       translate-middle badge rounded-pill
                                       bg-danger"
                                style="font-size: .6rem;">

                                <?= $unreadNotifications ?>

                            </span>

                        <?php endif; ?>

                    </a>


                    <div
                        class="dropdown-menu dropdown-menu-end
                               notification-dropdown p-0 mt-2">

                        <div class="dropdown-header p-3">

                            <strong>
                                Notifications
                            </strong>

                            <?php if ($unreadNotifications > 0): ?>

                                <small class="text-muted d-block">

                                    <?= $unreadNotifications ?>
                                    unread

                                </small>

                            <?php endif; ?>

                        </div>


                        <div class="dropdown-divider m-0"></div>


                        <a
                            class="dropdown-item text-center py-3"
                            href="<?= url(
                                'Notification',
                                'index'
                            ) ?>">

                            View All Notifications

                            <i
                                class="bi bi-arrow-right ms-1">
                            </i>

                        </a>

                    </div>

                </li>


                <!-- =================================================
                     ADMIN PROFILE
                ================================================== -->

                <li class="nav-item dropdown ms-lg-2">

                    <a
                        class="nav-link dropdown-toggle d-flex
                               align-items-center"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">


                        <img
                            src="<?= e($adminAvatar) ?>"
                            alt="Admin"
                            class="avatar-sm me-2">


                        <span
                            class="d-none d-lg-inline fw-semibold">

                            <?= e(
                                $_SESSION['user_name']
                                ?? 'Administrator'
                            ) ?>

                        </span>

                    </a>


                    <ul
                        class="dropdown-menu dropdown-menu-end
                               shadow border-0 mt-2">


                        <!-- Admin Header -->

                        <li>

                            <div class="px-3 py-2">

                                <small class="text-muted">
                                    Signed in as
                                </small>

                                <div class="fw-bold">

                                    <?= e(
                                        $_SESSION['user_name']
                                        ?? 'Administrator'
                                    ) ?>

                                </div>

                                <span
                                    class="badge bg-primary mt-1">

                                    Administrator

                                </span>

                            </div>

                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <!-- Admin Dashboard -->

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= url(
                                    'Admin',
                                    'index'
                                ) ?>">

                                <i
                                    class="bi bi-speedometer2 me-2">
                                </i>

                                Admin Dashboard

                            </a>

                        </li>


                        <!-- User Profile -->

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= url(
                                    'Profile',
                                    'index'
                                ) ?>">

                                <i
                                    class="bi bi-person-circle me-2">
                                </i>

                                My Profile

                            </a>

                        </li>


                        <!-- View Website -->

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= url(
                                    'Dashboard',
                                    'index'
                                ) ?>">

                                <i
                                    class="bi bi-globe me-2">
                                </i>

                                View Website

                            </a>

                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <!-- Logout -->

                        <li>

                            <a
                                class="dropdown-item text-danger"
                                href="<?= url(
                                    'Auth',
                                    'logout'
                                ) ?>">

                                <i
                                    class="bi bi-box-arrow-right me-2">
                                </i>

                                Logout

                            </a>

                        </li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>
<?php

/**
 * ============================================================================
 * Layout Header
 * ============================================================================
 * 
 * Common HTML head section included at the top of every page.
 * Loads Bootstrap 5, Bootstrap Icons, jQuery, and custom assets.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Ensure page title is set
$pageTitle = $GLOBALS['page_title'] ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e(APP_DESCRIPTION); ?>">

    <title><?php echo e($pageTitle); ?> | <?php echo e(APP_NAME); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/custom.css'); ?>">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;

            --success-color: #16a34a;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
            --info-color: #0891b2;

            --body-bg: #f6f7fb;
            --card-bg: #ffffff;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;

            --dark-bg: #111827;

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, .08);
            --shadow-lg: 0 16px 40px rgba(0, 0, 0, .10);

            --radius-sm: .5rem;
            --radius-md: .75rem;
            --radius-lg: 1rem;
        }


        /* =========================================================
       GLOBAL
       ========================================================= */

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--body-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        main {
            flex: 1;
        }

        a {
            transition: all .2s ease;
        }


        /* =========================================================
       NAVBAR
       ========================================================= */

        .navbar {
            background: rgba(255, 255, 255, .96) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(12px);
        }

        .navbar-brand {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--primary-color) !important;
            letter-spacing: -.5px;
        }

        .navbar-brand i {
            font-size: 1.25rem;
        }

        .navbar .nav-link {
            color: #4b5563;
            font-weight: 600;
            padding: .65rem .8rem !important;
            border-radius: var(--radius-sm);
            margin: 0 .1rem;
        }

        .navbar .nav-link:hover {
            color: var(--primary-color);
            background: var(--primary-light);
        }

        .navbar .nav-link.active {
            color: var(--primary-color) !important;
            background: var(--primary-light);
        }

        .navbar-toggler {
            border: 1px solid var(--border-color);
            padding: .45rem .65rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(79, 70, 229, .15);
        }


        /* =========================================================
       BUTTONS
       ========================================================= */

        .btn {
            border-radius: .6rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }


        /* =========================================================
       CARDS
       ========================================================= */

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            border-bottom: 1px solid var(--border-color);
        }

        .card-footer {
            border-top: 1px solid var(--border-color);
        }


        /* =========================================================
       STAT CARDS
       ========================================================= */

        .stat-card {
            border: 0;
            border-left: 4px solid var(--primary-color);
            overflow: hidden;
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.info {
            border-left-color: var(--info-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }

        .stat-card.danger {
            border-left-color: var(--danger-color);
        }


        /* =========================================================
       AVATARS
       ========================================================= */

        .avatar,
        .avatar-sm,
        .avatar-lg {
            object-fit: cover;
            border-radius: 50%;
            background: #eef0f4;
        }

        .avatar {
            width: 40px;
            height: 40px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .avatar-lg {
            width: 120px;
            height: 120px;
        }


        /* =========================================================
       SKILLS
       ========================================================= */

        .skill-badge {
            display: inline-flex;
            align-items: center;
            padding: .4rem .7rem;
            margin: .2rem;
            font-size: .8rem;
            font-weight: 600;
            border-radius: 999px;
            background: var(--primary-light);
            color: var(--primary-color);
        }


        /* =========================================================
       MATCH SCORE
       ========================================================= */

        .match-score-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            color: white;
            flex-shrink: 0;
        }

        .match-high {
            background: var(--success-color);
        }

        .match-medium {
            background: var(--warning-color);
        }

        .match-low {
            background: #6b7280;
        }


        /* =========================================================
       CHAT
       ========================================================= */

        .chat-message {
            max-width: 75%;
            padding: .75rem 1rem;
            border-radius: 1rem;
            margin-bottom: .6rem;
            line-height: 1.5;
        }

        .chat-message.sent {
            background: var(--primary-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: .25rem;
        }

        .chat-message.received {
            background: #eef0f4;
            color: var(--text-dark);
            margin-right: auto;
            border-bottom-left-radius: .25rem;
        }

        .chat-time {
            font-size: .72rem;
            opacity: .7;
        }


        /* =========================================================
       NOTIFICATIONS
       ========================================================= */

        .notification-dropdown {
            width: 340px;
            max-height: 420px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
        }

        .notification-item {
            padding: .85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: background .2s ease;
        }

        .notification-item:hover {
            background: #f9fafb;
        }

        .notification-item.unread {
            background: var(--primary-light);
        }


        /* =========================================================
       REVIEWS
       ========================================================= */

        .star-rating i {
            margin-right: 2px;
        }

        .star-rating .star-btn {
            text-decoration: none;
        }


        /* =========================================================
       PROGRESS
       ========================================================= */

        .progress {
            background: #e5e7eb;
            border-radius: 999px;
        }

        .progress-match {
            height: 8px;
            border-radius: 999px;
        }


        /* =========================================================
       EMPTY STATES
       ========================================================= */

        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #c4c8d0;
            margin-bottom: 1rem;
        }


        /* =========================================================
       ADMIN SIDEBAR
       ========================================================= */

        .admin-sidebar {
            min-height: calc(100vh - 70px);
            background: var(--dark-bg);
        }

        .admin-sidebar .nav-link {
            color: rgba(255, 255, 255, .7);
            padding: .75rem 1rem;
            border-radius: .6rem;
            margin: .2rem 0;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, .1);
        }

        .admin-sidebar .nav-link i {
            margin-right: .5rem;
        }


        /* =========================================================
       FOOTER
       ========================================================= */

        .footer {
            background: #111827;
            color: #d1d5db;
            padding: 3.5rem 0 1.5rem;
            margin-top: 4rem;
        }

        .footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer p {
            color: #9ca3af;
        }

        .footer a {
            color: #9ca3af;
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin-top: 2.5rem;
            padding-top: 1.25rem;
        }


        /* =========================================================
       BADGES
       ========================================================= */

        .badge {
            font-weight: 600;
        }


        /* =========================================================
       BREADCRUMBS
       ========================================================= */

        .breadcrumb {
            font-size: .9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }


        /* =========================================================
       MOBILE
       ========================================================= */

        @media (max-width: 768px) {

            .navbar .nav-link {
                margin: .15rem 0;
            }

            .match-score-circle {
                width: 50px;
                height: 50px;
                font-size: .85rem;
            }

            .notification-dropdown {
                width: calc(100vw - 2rem);
                max-width: 340px;
            }

            .chat-message {
                max-width: 88%;
            }

            .footer {
                text-align: center;
            }

        }
    </style>
</head>

<body>

    <!-- Flash Messages -->
    <?php require_once BASE_PATH . '/views/layouts/flash-messages.php'; ?>

    <!-- Navigation -->
    <?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <main class="py-4">
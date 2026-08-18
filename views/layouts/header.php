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
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
        
        body {
            background-color: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.info { border-left-color: var(--info-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        
        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .avatar-lg {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .avatar-sm {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .skill-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            margin: 0.2em;
            font-size: 0.875em;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
            background-color: #e9ecef;
            color: #495057;
        }
        
        .match-score-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
        }
        
        .match-high { background-color: var(--success-color); }
        .match-medium { background-color: var(--warning-color); }
        .match-low { background-color: var(--secondary-color); }
        
        .chat-message {
            max-width: 75%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .chat-message.sent {
            background-color: var(--primary-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0.25rem;
        }
        
        .chat-message.received {
            background-color: #e9ecef;
            color: #212529;
            margin-right: auto;
            border-bottom-left-radius: 0.25rem;
        }
        
        .chat-time {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        
        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e7f1ff;
        }
        
        .footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .admin-sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--dark-bg);
        }
        
        .admin-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0;
        }
        
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .star-rating i {
            margin-right: 2px;
        }
        
        .progress-match {
            height: 8px;
            border-radius: 4px;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .match-score-circle {
                width: 50px;
                height: 50px;
                font-size: 0.9rem;
            }
            
            .notification-dropdown {
                width: 100%;
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
    <main class="py-4"></main>
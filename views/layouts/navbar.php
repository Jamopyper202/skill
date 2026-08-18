<?php
/**
 * ============================================================================
 * Navigation Bar Layout
 * ============================================================================
 * 
 * Responsive Bootstrap navbar included on every page.
 * Shows different links based on user authentication status and role.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Get unread notification count if logged in
$unreadNotifications = 0;
$unreadMessages = 0;
if (isLoggedIn()) {
    $notifModel = new Notification();
    $unreadNotifications = $notifModel->countUnread(getCurrentUserId());
    
    $msgModel = new Message();
    $unreadMessages = $msgModel->countUnread(getCurrentUserId());
}

// Get user profile picture
$userAvatar = asset('images/download.png');
if (isLoggedIn() && !empty($_SESSION['user_picture']) && $_SESSION['user_picture'] !== 'download.png') {
    $userAvatar = uploadUrl($_SESSION['user_picture']);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="<?php echo url('Dashboard', 'index'); ?>">
            <i class="bi bi-arrow-left-right me-2"></i>
            <?php echo e(APP_NAME); ?>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isLoggedIn()): ?>
                    <!-- User Navigation -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('Dashboard'); ?>" href="<?php echo url('Dashboard', 'index'); ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('Skill'); ?>" href="<?php echo url('Skill', 'index'); ?>">
                            <i class="bi bi-tools me-1"></i>My Skills
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('Match'); ?>" href="<?php echo url('Match', 'index'); ?>">
                            <i class="bi bi-people me-1"></i>Matches
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('Exchange'); ?>" href="<?php echo url('Exchange', 'index'); ?>">
                            <i class="bi bi-arrow-left-right me-1"></i>Exchanges
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('Message'); ?>" href="<?php echo url('Message', 'index'); ?>">
                            <i class="bi bi-chat-dots me-1"></i>Messages
                            <?php if ($unreadMessages > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?php echo $unreadMessages; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('Auth', 'login'); ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('Auth', 'register'); ?>">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <?php if (isLoggedIn()): ?>
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <?php if ($unreadNotifications > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $unreadNotifications; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                            <div class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="mb-0">Notifications</h6>
                                <?php if ($unreadNotifications > 0): ?>
                                    <a href="<?php echo url('Notification', 'markAllRead'); ?>" class="small text-decoration-none">Mark all read</a>
                                <?php endif; ?>
                            </div>
                            <div class="notification-list">
                                <?php
                                $recentNotifs = [];
                                if (isLoggedIn()) {
                                    $notifModel = new Notification();
                                    $recentNotifs = $notifModel->getRecent(getCurrentUserId(), 5);
                                }
                                
                                if (empty($recentNotifs)):
                                ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="bi bi-bell-slash fs-4"></i>
                                        <p class="mb-0 small">No notifications</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recentNotifs as $notif): ?>
                                        <a href="<?php echo $notifModel->getLink($notif); ?>" class="text-decoration-none text-dark">
                                            <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                                                <div class="d-flex align-items-start">
                                                    <i class="bi <?php echo $notifModel->getIcon($notif['type']); ?> text-<?php echo $notifModel->getColor($notif['type']); ?> me-2 mt-1"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="small fw-bold"><?php echo e($notif['title']); ?></div>
                                                        <div class="small text-muted text-truncate" style="max-width: 220px;"><?php echo e($notif['message']); ?></div>
                                                        <div class="small text-muted"><?php echo timeAgo($notif['created_at']); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="dropdown-divider m-0"></div>
                            <a class="dropdown-item text-center py-2" href="<?php echo url('Notification', 'index'); ?>">
                                View All Notifications
                            </a>
                        </div>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo $userAvatar; ?>" alt="Avatar" class="avatar-sm me-2">
                            <span class="d-none d-lg-inline"><?php echo e($_SESSION['user_name'] ?? 'User'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo url('Profile', 'index'); ?>">
                                    <i class="bi bi-person me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo url('Portfolio', 'index'); ?>">
                                    <i class="bi bi-briefcase me-2"></i>Portfolio
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo url('Review', 'index'); ?>">
                                    <i class="bi bi-star me-2"></i>My Reviews
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (isAdmin()): ?>
                                <li>
                                    <a class="dropdown-item text-primary" href="<?php echo url('Admin', 'index'); ?>">
                                        <i class="bi bi-shield-lock me-2"></i>Admin Panel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo url('Auth', 'logout'); ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php
/**
 * Helper function to check if a controller is active
 */
function isActive(string $controller): string {
    $currentController = $_GET['controller'] ?? 'Dashboard';
    return strtolower($currentController) === strtolower($controller) ? 'active' : '';
}
?>
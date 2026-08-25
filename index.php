<?php

/**
 * ============================================================================
 * SkillSwap - Front Controller (index.php)
 * ============================================================================
 * 
 * This is the single entry point for the entire application.
 * All HTTP requests are routed through this file.
 * 
 * URL Format: index.php?controller=Name&action=method&param=value
 * Pretty URLs (with .htaccess): /controller/action/param
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Include the bootstrap file to initialize the application
require_once __DIR__ . '/bootstrap.php';



// ============================================================================
// ROUTING SETUP
// ============================================================================

/**
 * Get the controller name from URL parameter or default to 'Auth'
 * 
 * Available Controllers:
 * - Auth        : Login, register, logout
 * - Dashboard   : User and admin dashboards
 * - Profile     : View and edit user profiles
 * - Skill       : Manage offered and wanted skills
 * - Match       : Intelligent matching system
 * - Message     : Communication between users
 * - Notification: User notifications
 * - Exchange    : Skill exchange requests
 * - Review      : User reviews and ratings
 * - Portfolio   : User portfolio items
 * - Admin       : Admin panel management
 * - Report      : User reporting system
 */

$controller = isset($_GET['controller'])
    ? trim($_GET['controller'])
    : 'Auth';

$controller = ucfirst(
    strtolower($controller)
) . 'Controller';

$action = isset($_GET['action'])
    ? trim($_GET['action'])
    : 'login';


// ============================================================================
// AJAX ROUTING
// ============================================================================

/**
 * Check if this is an AJAX request
 * AJAX requests are handled by files in the /ajax/ directory
 */
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    // Build the AJAX handler file path
    $ajaxFile = BASE_PATH . '/ajax/' . strtolower(str_replace('Controller', '', $controller)) . '.php';

    // Check if the AJAX handler exists
    if (file_exists($ajaxFile)) {
        // Include the AJAX handler and exit
        require_once $ajaxFile;
        exit;
    } else {
        // Return JSON error for missing AJAX handler
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'AJAX handler not found.']);
        exit;
    }
}

// ============================================================================
// ADMIN ROUTE PROTECTION
// ============================================================================


$adminControllers = [
    'AdminController'
];

$isAdminRoute = in_array(
    $controller,
    $adminControllers,
    true
);


if ($isAdminRoute) {

    // Must be logged in
    if (!isLoggedIn()) {

        flash(
            'Please login to access the admin panel.',
            'warning'
        );

        redirect(
            url('Auth', 'login')
        );

        exit;
    }


    // Must be administrator
    if (!isAdmin()) {

        flash(
            'You do not have permission to access the admin panel.',
            'danger'
        );

        redirect(
            url('Dashboard', 'index')
        );

        exit;
    }
}

// ============================================================================
// AUTHENTICATION ROUTE PROTECTION
// ============================================================================

/**
 * List of controllers that require user authentication
 * AuthController is excluded as it handles login/register
 */
$protectedControllers = [
    'DashboardController',
    'ProfileController',
    'SkillController',
    'MatchController',
    'MessageController',
    'NotificationController',
    'ExchangeController',
    'ReviewController',
    'PortfolioController',
    'ReportController'
];

/**
 * Check if the requested controller requires authentication
 */
$isProtectedRoute = in_array($controller, $protectedControllers);

/**
 * If accessing protected route, verify user authentication
 */
if ($isProtectedRoute) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Save the intended URL for redirect after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

        // Redirect to login page
        flash('Please login to access this page.', 'warning');
        redirect('index.php?controller=Auth&action=login');
        exit;
    }

    // Check if user account is active
    if (isset($_SESSION['user_active']) && $_SESSION['user_active'] != 1) {
        // Destroy session and redirect
        session_destroy();
        flash('Your account has been deactivated. Please contact support.', 'danger');
        redirect('index.php?controller=Auth&action=login');
        exit;
    }
}

// ============================================================================
// CONTROLLER DISPATCH
// ============================================================================

try {

    if (!class_exists($controller)) {
        throw new Exception(
            "Controller '$controller' not found."
        );
    }

    $controllerInstance = new $controller();

    if (!method_exists($controllerInstance, $action)) {
        throw new Exception(
            "Action '$action' not found in controller '$controller'."
        );
    }

    call_user_func([
        $controllerInstance,
        $action
    ]);
} catch (Exception $e) {

    error_log(
        "Routing Error: " . $e->getMessage()
    );

    http_response_code(404);

    render404($e->getMessage());
}

// ============================================================================
// 404 ERROR PAGE FUNCTION
// ============================================================================

/**
 * Render the 404 Not Found page
 * 
 * @param string $message Optional error message to display
 * @return void
 */
function render404(string $message = ''): void
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Page Not Found | <?php echo APP_NAME; ?></title>
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body {
                background-color: #f8f9fa;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .error-container {
                text-align: center;
                padding: 2rem;
            }

            .error-code {
                font-size: 8rem;
                font-weight: 700;
                color: #0d6efd;
                line-height: 1;
            }

            .error-icon {
                font-size: 5rem;
                color: #6c757d;
                margin-bottom: 1rem;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="error-container">
                        <div class="error-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="error-code">404</div>
                        <h2 class="mb-3">Page Not Found</h2>
                        <p class="text-muted mb-4">
                            <?php echo !empty($message) ? htmlspecialchars($message) : 'The page you are looking for does not exist or has been moved.'; ?>
                        </p>
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="<?php echo BASE_URL; ?>/index.php?controller=Dashboard&action=index" class="btn btn-primary btn-lg">
                                <i class="bi bi-house-door me-2"></i>Go Home
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}
?>
<?php
/**
 * ============================================================================
 * SkillSwap - Application Bootstrap File
 * ============================================================================
 * 
 * This is the main bootstrap file that initializes the entire application.
 * It is included at the top of index.php and handles:
 * - Database connection setup
 * - Configuration loading
 * - Constant definitions
 * - Class autoloading for Models and Controllers
 * - Session management
 * - Error reporting configuration
 * - Helper file inclusion
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// ============================================================================
// ERROR REPORTING CONFIGURATION
// ============================================================================
// Enable error reporting for development environment
// Disable in production by setting display_errors to 0
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// ============================================================================
// TIMEZONE CONFIGURATION
// ============================================================================
// Set the default timezone for all date/time functions
date_default_timezone_set('Africa/Lagos');

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================
// Start session if not already started
// Sessions are used for user authentication and flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// DEFINE BASE PATH CONSTANT
// ============================================================================
// BASE_PATH points to the root directory of the application
// Used for including files and generating URLs
define('BASE_PATH', __DIR__);

// ============================================================================
// DEFINE URL CONSTANT
// ============================================================================
// BASE_URL is used for generating absolute URLs in views
// Change this to match your local server configuration
define('BASE_URL', 'http://localhost/skillswap');

// ============================================================================
// DEFINE UPLOAD PATHS
// ============================================================================
// Directory paths for uploaded files (profile pictures, portfolio images)
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// ============================================================================
// DEFINE ASSET PATHS
// ============================================================================
// Paths for CSS, JS, and image assets
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');



// ============================================================================
// APPLICATION SETTINGS CONSTANTS
// ============================================================================
// General application configuration values
define('APP_NAME', 'SkillSwap');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Digital Skill Marketplace Without Monetary Transactions');

// Pagination settings
define('ITEMS_PER_PAGE', 10);

// Match scoring threshold (minimum score to display a match)
define('MATCH_THRESHOLD', 30);

// Maximum file upload size in bytes (2MB)
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);

// Allowed image upload types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);



// ============================================================================
// CLASS AUTOLOADER
// ============================================================================
/**
 * Autoload function for Models and Controllers
 * 
 * This function automatically includes the required class file when a class
 * is instantiated. It looks for:
 * - Model classes in the /models/ directory
 * - Controller classes in the /controllers/ directory
 * 
 * Naming convention: Class name must match the filename (e.g., User.php for User class)
 * 
 * @param string $className The name of the class to load
 * @return void
 */
spl_autoload_register(function (string $className): void {
    // Define the directories to search for classes
    $directories = [
        BASE_PATH . '/models/',      // Search models directory first
        BASE_PATH . '/controllers/'  // Then search controllers directory
    ];

    // Loop through each directory to find the class file
    foreach ($directories as $directory) {
        // Build the full file path
        $filePath = $directory . $className . '.php';

        // Check if the file exists
        if (file_exists($filePath)) {
            // Include the file and stop searching
            require_once $filePath;
            return;
        }
    }

    // If class file is not found, log the error
    // Note: We don't throw an exception here to allow other autoloaders to try
    error_log("Autoload failed: Class '$className' not found in models/ or controllers/");
});

// ============================================================================
// LOAD HELPER FUNCTIONS
// ============================================================================
// Include the helper functions file which contains reusable utility functions
// These include: redirect(), flash(), old(), sanitize(), etc.
$helperFile = BASE_PATH . '/helpers/functions.php';
if (file_exists($helperFile)) {
    require_once $helperFile;
} else {
    // Log error if helper file is missing but don't stop execution
    error_log("Helper file not found: " . $helperFile);
}

// ============================================================================
// LOAD CONFIGURATION (Optional - if using a config file)
// ============================================================================
// Load additional configuration from config file if it exists
$configFile = BASE_PATH . '/config/database.php';
if (file_exists($configFile)) {
    // The config file can override default constants if needed
    require_once $configFile;
}

// ============================================================================
// SECURITY HEADERS (Optional - for additional security)
// ============================================================================
// Prevent clickjacking attacks
header('X-Frame-Options: DENY');

// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Enable XSS protection in browsers
header('X-XSS-Protection: 1; mode=block');

// ============================================================================
// BOOTSTRAP COMPLETE
// ============================================================================
// The application is now fully initialized and ready to handle requests
// index.php will proceed to route the incoming request to the appropriate controller
?>
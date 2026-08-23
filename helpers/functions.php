<?php

/**
 * ============================================================================
 * Helper Functions
 * ============================================================================
 * 
 * Reusable utility functions used throughout the application.
 * These functions handle common tasks like redirects, flash messages,
 * input sanitization, session management, and URL generation.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

/**
 * =========================================================================
 * REDIRECT
 * =========================================================================
 * 
 * Redirect to a URL and optionally exit script execution.
 * 
 * @param string $url    URL to redirect to
 * @param bool   $exit   Whether to exit after redirect
 * @return void
 */
function redirect(string $url, bool $exit = true): void
{
    header("Location: " . $url);
    if ($exit) {
        exit;
    }
}

/**
 * =========================================================================
 * FLASH MESSAGE
 * =========================================================================
 * 
 * Set a flash message in session to display on next page load.
 * 
 * @param string $message  Message text
 * @param string $type     Bootstrap alert type: success, danger, warning, info
 * @return void
 */
function flash(string $message, string $type = 'info'): void
{
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type'    => $type
    ];
}

/**
 * =========================================================================
 * GET AND CLEAR FLASH MESSAGES
 * =========================================================================
 * 
 * Retrieve all flash messages and clear them from session.
 * 
 * @return array  Array of flash messages
 */
function getFlashMessages(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

/**
 * =========================================================================
 * SANITIZE INPUT
 * =========================================================================
 * 
 * Clean user input to prevent XSS and remove harmful characters.
 * 
 * @param string $input  Raw input string
 * @return string        Sanitized string
 */
function sanitize(string $input): string
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * =========================================================================
 * SANITIZE ARRAY
 * =========================================================================
 * 
 * Apply sanitize() to all string values in an array.
 * 
 * @param array $data  Input array
 * @return array       Sanitized array
 */
function sanitizeArray(array $data): array
{
    $clean = [];
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $clean[$key] = sanitize($value);
        } elseif (is_array($value)) {
            $clean[$key] = sanitizeArray($value);
        } else {
            $clean[$key] = $value;
        }
    }
    return $clean;
}

/**
 * =========================================================================
 * OLD INPUT
 * =========================================================================
 * 
 * Retrieve old form input from session (used to repopulate forms after error).
 * 
 * @param string $key      Form field name
 * @param string $default  Default value if not found
 * @return string          Old input value or default
 */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['old_input'][$key] ?? $default;
    unset($_SESSION['old_input'][$key]);
    return sanitize((string) $value);
}

/**
 * =========================================================================
 * STORE OLD INPUT
 * =========================================================================
 * 
 * Store form input in session for repopulation after validation error.
 * 
 * @param array $input  Form data array (typically $_POST)
 * @return void
 */
function storeOldInput(array $input): void
{
    $_SESSION['old_input'] = $input;
}

/**
 * =========================================================================
 * GENERATE URL
 * =========================================================================
 * 
 * Generate an application URL with query parameters.
 * 
 * @param string $controller  Controller name
 * @param string $action      Action name
 * @param array  $params      Additional parameters
 * @return string             Generated URL
 */
function url(string $controller = 'Dashboard', string $action = 'index', array $params = []): string
{
    $url = BASE_URL . '/index.php?controller=' . $controller . '&action=' . $action;

    foreach ($params as $key => $value) {
        $url .= '&' . $key . '=' . urlencode($value);
    }

    return $url;
}

/**
 * =========================================================================
 * ASSET URL
 * =========================================================================
 * 
 * Generate URL for static assets (CSS, JS, images).
 * 
 * @param string $path  Asset path relative to assets folder
 * @return string       Full asset URL
 */
function asset(string $path): string
{
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * =========================================================================
 * UPLOAD URL
 * =========================================================================
 * 
 * Generate URL for uploaded files.
 * 
 * @param string $filename  Uploaded file name
 * @return string           Full upload URL
 */
function uploadUrl(string $filename): string
{
    if (empty($filename) || $filename === 'download.png') {
        return asset('images/download.png');
    }
    return UPLOAD_URL . '/' . $filename;
}

/**
 * =========================================================================
 * FORMAT DATE
 * =========================================================================
 * 
 * Format a date string into a human-readable format.
 * 
 * @param string $date    Date string
 * @param string $format  PHP date format
 * @return string         Formatted date
 */
// function formatDate(string $date, string $format = 'M d, Y'): string
// {
//     if (empty($date) || $date === '0000-00-00 00:00:00') {
//         return 'N/A';
//     }
//     return date($format, strtotime($date));
// }
function formatDate($date, $format = 'M d, Y'): string
{
    if (empty($date)) {
        return '';
    }

    try {
        $timezone = new DateTimeZone('America/Los_Angeles');

        $datetime = new DateTime($date, $timezone);

        return $datetime->format($format);
    } catch (Exception $e) {
        error_log('formatDate error: ' . $e->getMessage());
        return '';
    }
}
/**
 * =========================================================================
 * FORMAT DATETIME
 * =========================================================================
 * 
 * Format a datetime string with time.
 * 
 * @param string $date    Datetime string
 * @return string         Formatted datetime
 */
function formatDateTime(string $date): string
{
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date('M d, Y \a\t h:i A', strtotime($date));
}

/**
 * =========================================================================
 * TIME AGO
 * =========================================================================
 * 
 * Convert a date to "time ago" format (e.g., "2 hours ago").
 * 
 * @param string $date  Date string
 * @return string       Time ago text
 */
function timeAgo(string $date): string
{
    if (empty($date)) {
        return 'N/A';
    }

    try {
        // MySQL is currently running at UTC-08:00
        $mysqlTimezone = new DateTimeZone('-08:00');

        $messageTime = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $date,
            $mysqlTimezone
        );

        if (!$messageTime) {
            return 'N/A';
        }

        // Current MySQL-equivalent time
        $now = new DateTime('now', $mysqlTimezone);

        $difference =
            $now->getTimestamp() -
            $messageTime->getTimestamp();

        if ($difference < 60) {
            return 'Just now';
        }

        if ($difference < 3600) {
            $minutes = floor($difference / 60);

            return $minutes === 1
                ? '1 min ago'
                : $minutes . ' mins ago';
        }

        if ($difference < 86400) {
            $hours = floor($difference / 3600);

            return $hours === 1
                ? '1 hr ago'
                : $hours . ' hrs ago';
        }

        if ($difference < 172800) {
            return 'Yesterday';
        }

        if ($difference < 604800) {
            $days = floor($difference / 86400);

            return $days === 1
                ? '1 day ago'
                : $days . ' days ago';
        }

        if ($difference < 2592000) {
            $weeks = floor($difference / 604800);

            return $weeks === 1
                ? '1 week ago'
                : $weeks . ' weeks ago';
        }

        return $messageTime->format('M j, Y');

    } catch (Exception $e) {
        return 'N/A';
    }
}

/**
 * =========================================================================
 * TRUNCATE TEXT
 * =========================================================================
 * 
 * Truncate text to a specified length with ellipsis.
 * 
 * @param string $text    Text to truncate
 * @param int    $length  Maximum length
 * @return string         Truncated text
 */
function truncate(string $text, int $length = 100): string
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * =========================================================================
 * GENERATE PAGINATION
 * =========================================================================
 * 
 * Generate Bootstrap pagination HTML.
 * 
 * @param int    $currentPage  Current page number
 * @param int    $totalPages   Total number of pages
 * @param string $baseUrl      Base URL with query params
 * @return string              Pagination HTML
 */
function pagination(int $currentPage, int $totalPages, string $baseUrl): string
{
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous button
    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $prevUrl = $currentPage > 1 ? $baseUrl . '&page=' . ($currentPage - 1) : '#';
    $html .= '<li class="page-item' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '">Previous</a></li>';

    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }

    // Next button
    $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
    $nextUrl = $currentPage < $totalPages ? $baseUrl . '&page=' . ($currentPage + 1) : '#';
    $html .= '<li class="page-item' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '">Next</a></li>';

    $html .= '</ul></nav>';

    return $html;
}


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

/**
 * =========================================================================
 * IS LOGGED IN
 * =========================================================================
 * 
 * Check if a user is currently logged in.
 * 
 * @return bool  True if logged in, false otherwise
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * =========================================================================
 * IS ADMIN
 * =========================================================================
 * 
 * Check if the logged-in user is an admin.
 * 
 * @return bool  True if admin, false otherwise
 */
function isAdmin(): bool
{
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * =========================================================================
 * GET CURRENT USER ID
 * =========================================================================
 * 
 * Get the ID of the currently logged-in user.
 * 
 * @return int|null  User ID or null if not logged in
 */
function getCurrentUserId(): ?int
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

/**
 * =========================================================================
 * REQUIRE LOGIN
 * =========================================================================
 * 
 * Redirect to login page if user is not logged in.
 * 
 * @return void
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('Please login to access this page.', 'warning');
        redirect(url('Auth', 'login'));
    }
}

/**
 * =========================================================================
 * REQUIRE ADMIN
 * =========================================================================
 * 
 * Redirect if user is not an admin.
 * 
 * @return void
 */
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        flash('You do not have permission to access this page.', 'danger');
        redirect(url('Dashboard', 'index'));
    }
}

/**
 * =========================================================================
 * GENERATE CSRF TOKEN
 * =========================================================================
 * 
 * Generate and store a CSRF token in session.
 * 
 * @return string  CSRF token
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * =========================================================================
 * VERIFY CSRF TOKEN
 * =========================================================================
 * 
 * Verify a submitted CSRF token against the session token.
 * 
 * @param string $token  Submitted token
 * @return bool          True if valid, false otherwise
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * =========================================================================
 * DISPLAY ERRORS
 * =========================================================================
 * 
 * Display validation errors in Bootstrap format.
 * 
 * @param array $errors  Array of error messages
 * @return string        HTML error display
 */
function displayErrors(array $errors): string
{
    if (empty($errors)) {
        return '';
    }

    $html = '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $html .= '</ul></div>';

    return $html;
}

/**
 * =========================================================================
 * VALIDATE EMAIL
 * =========================================================================
 * 
 * Validate an email address format.
 * 
 * @param string $email  Email to validate
 * @return bool          True if valid, false otherwise
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * =========================================================================
 * VALIDATE REQUIRED
 * =========================================================================
 * 
 * Check if a value is not empty.
 * 
 * @param mixed $value  Value to check
 * @return bool         True if not empty, false otherwise
 */
function isRequired($value): bool
{
    return !empty(trim((string) $value));
}

/**
 * =========================================================================
 * SLUGIFY
 * =========================================================================
 * 
 * Convert a string to a URL-friendly slug.
 * 
 * @param string $text  Text to slugify
 * @return string       URL-friendly slug
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/**
 * =========================================================================
 * RANDOM STRING
 * =========================================================================
 * 
 * Generate a random alphanumeric string.
 * 
 * @param int $length  String length
 * @return string      Random string
 */
function randomString(int $length = 10): string
{
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

/**
 * =========================================================================
 * FORMAT NUMBER
 * =========================================================================
 * 
 * Format a number with commas.
 * 
 * @param int|float $number  Number to format
 * @return string            Formatted number
 */
function formatNumber($number): string
{
    return number_format((float) $number);
}

/**
 * =========================================================================
 * STAR RATING HTML
 * =========================================================================
 * 
 * Generate Bootstrap star rating HTML.
 * 
 * @param float $rating  Rating value (0-5)
 * @param int   $size    Star size in pixels
 * @return string        HTML star rating
 */
function starRating(float $rating, int $size = 16): string
{
    $html = '<span class="star-rating">';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="bi bi-star-fill text-warning" style="font-size:' . $size . 'px"></i>';
    }

    if ($halfStar) {
        $html .= '<i class="bi bi-star-half text-warning" style="font-size:' . $size . 'px"></i>';
    }

    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="bi bi-star text-warning" style="font-size:' . $size . 'px"></i>';
    }

    $html .= '</span>';
    return $html;
}

/**
 * =========================================================================
 * EXPERIENCE LEVEL BADGE
 * =========================================================================
 * 
 * Generate a Bootstrap badge for experience level.
 * 
 * @param string $level  Experience level
 * @return string        HTML badge
 */
function experienceBadge(string $level): string
{
    $colors = [
        'Beginner'     => 'bg-success',
        'Intermediate' => 'bg-info',
        'Advanced'     => 'bg-warning',
        'Expert'       => 'bg-danger'
    ];

    $color = $colors[$level] ?? 'bg-secondary';
    return '<span class="badge ' . $color . '">' . $level . '</span>';
}

/**
 * =========================================================================
 * EXCHANGE STATUS BADGE
 * =========================================================================
 * 
 * Generate a Bootstrap badge for exchange status.
 * 
 * @param string $status  Exchange status
 * @return string         HTML badge
 */
function exchangeStatusBadge(string $status): string
{
    $colors = [
        'pending'     => 'bg-warning',
        'accepted'    => 'bg-info',
        'in_progress' => 'bg-primary',
        'completed'   => 'bg-success',
        'declined'    => 'bg-danger',
        'cancelled'   => 'bg-secondary'
    ];

    $color = $colors[$status] ?? 'bg-secondary';
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="badge ' . $color . '">' . $label . '</span>';
}

/**
 * =========================================================================
 * MATCH SCORE BADGE
 * =========================================================================
 * 
 * Generate a Bootstrap badge for match score.
 * 
 * @param int $score  Match score (0-100)
 * @return string     HTML badge
 */
function matchScoreBadge(int $score): string
{
    if ($score >= 80) {
        $color = 'bg-success';
    } elseif ($score >= 60) {
        $color = 'bg-info';
    } elseif ($score >= 40) {
        $color = 'bg-warning';
    } else {
        $color = 'bg-secondary';
    }

    return '<span class="badge ' . $color . '">' . $score . '% Match</span>';
}

/**
 * =========================================================================
 * NOTIFICATION ICON
 * =========================================================================
 * 
 * Get Bootstrap icon for notification type.
 * 
 * @param string $type  Notification type
 * @return string       Icon class
 */
function notificationIcon(string $type): string
{
    $icons = [
        'match'             => 'bi-people-fill',
        'message'           => 'bi-chat-dots-fill',
        'exchange_request'  => 'bi-arrow-left-right',
        'exchange_accepted' => 'bi-check-circle-fill',
        'exchange_declined' => 'bi-x-circle-fill',
        'exchange_completed' => 'bi-trophy-fill',
        'review'            => 'bi-star-fill',
        'system'            => 'bi-bell-fill'
    ];

    return $icons[$type] ?? 'bi-bell-fill';
}

/**
 * =========================================================================
 * NOTIFICATION COLOR
 * =========================================================================
 * 
 * Get Bootstrap color class for notification type.
 * 
 * @param string $type  Notification type
 * @return string       Color class
 */
function notificationColor(string $type): string
{
    $colors = [
        'match'             => 'primary',
        'message'           => 'info',
        'exchange_request'  => 'warning',
        'exchange_accepted' => 'success',
        'exchange_declined' => 'danger',
        'exchange_completed' => 'success',
        'review'            => 'warning',
        'system'            => 'secondary'
    ];

    return $colors[$type] ?? 'secondary';
}

/**
 * =========================================================================
 * ESCAPE OUTPUT
 * =========================================================================
 * 
 * Safely escape output for HTML display.
 * 
 * @param string $text  Text to escape
 * @return string       Escaped text
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * =========================================================================
 * GET SETTING
 * =========================================================================
 * 
 * Get a setting value from the database.
 * 
 * @param string $key      Setting key
 * @param string $default  Default value
 * @return string          Setting value
 */
function getSetting(string $key, string $default = ''): string
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * =========================================================================
 * SET PAGE TITLE
 * =========================================================================
 * 
 * Set the page title for the current view.
 * 
 * @param string $title  Page title
 * @return void
 */
function setPageTitle(string $title): void
{
    $GLOBALS['page_title'] = $title;
}

/**
 * =========================================================================
 * GET PAGE TITLE
 * =========================================================================
 * 
 * Get the current page title.
 * 
 * @return string  Page title
 */
function getPageTitle(): string
{
    return $GLOBALS['page_title'] ?? APP_NAME;
}

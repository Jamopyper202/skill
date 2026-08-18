<?php
/**
 * ============================================================================
 * AJAX Notification Handler
 * ============================================================================
 * 
 * Handles AJAX requests for notification functionality:
 * - Get unread count
 * - Mark as read
 * - Mark all as read
 * - Get recent notifications
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Verify user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = getCurrentUserId();
$action = $_GET['action'] ?? '';

$notificationModel = new Notification();

switch ($action) {
    // =========================================================================
    // GET UNREAD COUNT
    // =========================================================================
    case 'unread_count':
        $count = $notificationModel->countUnread($userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    // =========================================================================
    // MARK AS READ
    // =========================================================================
    case 'mark_read':
        $notificationId = (int) ($_POST['id'] ?? 0);

        if ($notificationId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification']);
            exit;
        }

        $result = $notificationModel->markAsRead($notificationId, $userId);
        echo json_encode(['success' => $result]);
        break;

    // =========================================================================
    // MARK ALL AS READ
    // =========================================================================
    case 'mark_all_read':
        $count = $notificationModel->markAllAsRead($userId);
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        break;

    // =========================================================================
    // GET RECENT NOTIFICATIONS
    // =========================================================================
    case 'get_recent':
        $limit = (int) ($_GET['limit'] ?? 5);
        $notifications = $notificationModel->getRecent($userId, $limit);

        $formatted = [];
        foreach ($notifications as $notif) {
            $formatted[] = [
                'id' => $notif['id'],
                'type' => $notif['type'],
                'title' => e($notif['title']),
                'message' => e($notif['message']),
                'is_read' => (bool) $notif['is_read'],
                'created_at' => timeAgo($notif['created_at']),
                'icon' => $notificationModel->getIcon($notif['type']),
                'color' => $notificationModel->getColor($notif['type']),
                'link' => $notificationModel->getLink($notif)
            ];
        }

        echo json_encode([
            'success' => true,
            'notifications' => $formatted
        ]);
        break;

    // =========================================================================
    // DEFAULT
    // =========================================================================
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
<?php
/**
 * ============================================================================
 * Notification Controller
 * ============================================================================
 * 
 * Handles user notifications: view list, mark as read, delete,
 * and mark all as read.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class NotificationController {
    /**
     * Notification model instance
     * @var Notification
     */
    private Notification $notificationModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * =========================================================================
     * LIST NOTIFICATIONS
     * =========================================================================
     * 
     * Display all notifications for the logged-in user.
     */
    public function index(): void {
        $userId = getCurrentUserId();

        $filter = $_GET['filter'] ?? 'all';
        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get notifications
        $notifications = $this->notificationModel->getForUser($userId, $filter, $limit, $offset);

        // Get total counts
        $total = $this->notificationModel->countTotal($userId, $filter);
        $totalPages = ceil($total / $limit);

        $unreadCount = $this->notificationModel->countUnread($userId);
        $readCount = $this->notificationModel->countTotal($userId, 'read');

        setPageTitle('Notifications');
        require_once BASE_PATH . '/views/notifications/index.php';
    }

    /**
     * =========================================================================
     * MARK AS READ
     * =========================================================================
     * 
     * Mark a single notification as read.
     */
    public function markRead(): void {
        $userId = getCurrentUserId();
        $notificationId = (int) ($_GET['id'] ?? 0);

        $notification = $this->notificationModel->getById($notificationId, $userId);

        if ($notification) {
            $this->notificationModel->markAsRead($notificationId, $userId);

            // Redirect to the notification's target if provided
            if (isset($_GET['redirect']) && $_GET['redirect'] == '1') {
                $link = $this->notificationModel->getLink($notification);
                redirect($link);
                return;
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        flash('Notification marked as read.', 'success');
        redirect(url('Notification', 'index'));
    }

    /**
     * =========================================================================
     * MARK ALL AS READ
     * =========================================================================
     * 
     * Mark all notifications for the user as read.
     */
    public function markAllRead(): void {
        $userId = getCurrentUserId();

        $count = $this->notificationModel->markAllAsRead($userId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            exit;
        }

        flash($count > 0 ? "{$count} notification(s) marked as read." : "No unread notifications.", 'success');
        redirect(url('Notification', 'index'));
    }

    /**
     * =========================================================================
     * DELETE NOTIFICATION
     * =========================================================================
     * 
     * Delete a single notification.
     */
    public function delete(): void {
        $userId = getCurrentUserId();
        $notificationId = (int) ($_GET['id'] ?? 0);

        if ($this->notificationModel->delete($notificationId, $userId)) {
            flash('Notification deleted.', 'success');
        } else {
            flash('Failed to delete notification.', 'danger');
        }

        redirect(url('Notification', 'index'));
    }

    /**
     * =========================================================================
     * DELETE ALL READ
     * =========================================================================
     * 
     * Delete all read notifications.
     */
    public function deleteAllRead(): void {
        $userId = getCurrentUserId();

        $count = $this->notificationModel->deleteAllRead($userId);

        flash($count > 0 ? "{$count} notification(s) deleted." : "No read notifications to delete.", 'success');
        redirect(url('Notification', 'index'));
    }

    /**
     * =========================================================================
     * GET UNREAD COUNT (AJAX)
     * =========================================================================
     * 
     * Return unread notification count for navbar badge.
     */
    public function unreadCount(): void {
        header('Content-Type: application/json');

        $userId = getCurrentUserId();
        $count = $this->notificationModel->countUnread($userId);

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        exit;
    }
}
?>
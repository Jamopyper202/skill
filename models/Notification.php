<?php
/**
 * ============================================================================
 * Notification Model
 * ============================================================================
 * 
 * Handles all database operations related to user notifications.
 * Includes: creating, retrieving, marking as read, and deleting notifications.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Notification {
    /**
     * Database connection instance
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor - initialize database connection
     */
    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * =========================================================================
     * CREATE NOTIFICATION
     * =========================================================================
     * 
     * Create a new notification for a user.
     * 
     * @param int    $userId      User ID to notify
     * @param string $type        Notification type: match, message, exchange_request, exchange_accepted, exchange_declined, exchange_completed, review, system
     * @param int    $referenceId Related record ID (exchange_id, message_id, etc.)
     * @param string $title       Short notification title
     * @param string $message     Notification message body
     * @return int|false          New notification ID or false
     */
    public function create(int $userId, string $type, int $referenceId, string $title, string $message): int|false {
        try {
            // Validate notification type
            $validTypes = ['match', 'message', 'exchange_request', 'exchange_accepted', 'exchange_declined', 'exchange_completed', 'review', 'system'];
            if (!in_array($type, $validTypes)) {
                $type = 'system';
            }

            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, type, reference_id, title, message, is_read, created_at)
                VALUES (:user_id, :type, :reference_id, :title, :message, 0, NOW())
            ");

            $stmt->execute([
                ':user_id'      => $userId,
                ':type'         => $type,
                ':reference_id' => $referenceId,
                ':title'        => $title,
                ':message'      => $message
            ]);

            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Create Notification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET NOTIFICATION BY ID
     * =========================================================================
     * 
     * Retrieve a single notification by ID.
     * 
     * @param int $notificationId  Notification ID
     * @param int $userId          User ID (for verification)
     * @return array|false
     */
    public function getById(int $notificationId, int $userId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications 
                WHERE id = :id AND user_id = :user_id
                LIMIT 1
            ");

            $stmt->execute([
                ':id'      => $notificationId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get Notification By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET ALL NOTIFICATIONS FOR USER
     * =========================================================================
     * 
     * Get all notifications for a user with optional read filter.
     * 
     * @param int    $userId   User ID
     * @param string $filter   'all', 'read', or 'unread'
     * @param int    $limit    Results limit
     * @param int    $offset   Pagination offset
     * @return array
     */
    public function getForUser(int $userId, string $filter = 'all', int $limit = 20, int $offset = 0): array {
        try {
            $sql = "SELECT * FROM notifications WHERE user_id = :user_id";
            $params = [':user_id' => $userId];

            if ($filter === 'unread') {
                $sql .= " AND is_read = 0";
            } elseif ($filter === 'read') {
                $sql .= " AND is_read = 1";
            }

            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Notifications For User Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET RECENT NOTIFICATIONS
     * =========================================================================
     * 
     * Get recent unread notifications for navbar display.
     * 
     * @param int $userId  User ID
     * @param int $limit   Number of notifications
     * @return array
     */
    public function getRecent(int $userId, int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Recent Notifications Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET UNREAD NOTIFICATIONS
     * =========================================================================
     * 
     * Get only unread notifications for a user.
     * 
     * @param int $userId  User ID
     * @param int $limit   Maximum results
     * @return array
     */
    public function getUnread(int $userId, int $limit = 10): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications 
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Unread Notifications Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * MARK AS READ
     * =========================================================================
     * 
     * Mark a single notification as read.
     * 
     * @param int $notificationId  Notification ID
     * @param int $userId          User ID (for verification)
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 1 
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':id'      => $notificationId,
                ':user_id' => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Mark As Read Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * MARK ALL AS READ
     * =========================================================================
     * 
     * Mark all notifications for a user as read.
     * 
     * @param int $userId  User ID
     * @return int          Number of notifications marked as read
     */
    public function markAllAsRead(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0
            ");

            $stmt->execute([':user_id' => $userId]);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log("Mark All As Read Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * DELETE NOTIFICATION
     * =========================================================================
     * 
     * Delete a single notification.
     * 
     * @param int $notificationId  Notification ID
     * @param int $userId          User ID (for verification)
     * @return bool
     */
    public function delete(int $notificationId, int $userId): bool {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':id'      => $notificationId,
                ':user_id' => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Delete Notification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE ALL READ NOTIFICATIONS
     * =========================================================================
     * 
     * Delete all read notifications for a user.
     * 
     * @param int $userId  User ID
     * @return int          Number of deleted notifications
     */
    public function deleteAllRead(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE user_id = :user_id AND is_read = 1
            ");

            $stmt->execute([':user_id' => $userId]);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log("Delete All Read Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * COUNT UNREAD
     * =========================================================================
     * 
     * Count total unread notifications for a user.
     * 
     * @param int $userId  User ID
     * @return int
     */
    public function countUnread(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM notifications 
                WHERE user_id = :user_id AND is_read = 0
            ");

            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Count Unread Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * COUNT TOTAL
     * =========================================================================
     * 
     * Count total notifications for a user.
     * 
     * @param int    $userId  User ID
     * @param string $filter  'all', 'read', 'unread'
     * @return int
     */
    public function countTotal(int $userId, string $filter = 'all'): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = :user_id";
            $params = [':user_id' => $userId];

            if ($filter === 'unread') {
                $sql .= " AND is_read = 0";
            } elseif ($filter === 'read') {
                $sql .= " AND is_read = 1";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Count Total Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * GET NOTIFICATION LINK
     * =========================================================================
     * 
     * Generate the appropriate URL based on notification type.
     * 
     * @param array $notification  Notification data array
     * @return string              URL to redirect to
     */
    public function getLink(array $notification): string {
        $baseUrl = BASE_URL . '/index.php';

        switch ($notification['type']) {
            case 'match':
                return $baseUrl . '?controller=Match&action=index';
            
            case 'message':
                return $baseUrl . '?controller=Message&action=conversation&user_id=' . $notification['reference_id'];
            
            case 'exchange_request':
            case 'exchange_accepted':
            case 'exchange_declined':
            case 'exchange_completed':
                return $baseUrl . '?controller=Exchange&action=view&id=' . $notification['reference_id'];
            
            case 'review':
                return $baseUrl . '?controller=Review&action=view&id=' . $notification['reference_id'];
            
            case 'system':
            default:
                return $baseUrl . '?controller=Dashboard&action=index';
        }
    }

    /**
     * =========================================================================
     * GET NOTIFICATION ICON
     * =========================================================================
     * 
     * Get the Bootstrap icon class for a notification type.
     * 
     * @param string $type  Notification type
     * @return string       Bootstrap icon class
     */
    public function getIcon(string $type): string {
        $icons = [
            'match'             => 'bi-people-fill',
            'message'           => 'bi-chat-dots-fill',
            'exchange_request'  => 'bi-arrow-left-right',
            'exchange_accepted' => 'bi-check-circle-fill',
            'exchange_declined' => 'bi-x-circle-fill',
            'exchange_completed'=> 'bi-trophy-fill',
            'review'            => 'bi-star-fill',
            'system'            => 'bi-bell-fill'
        ];

        return $icons[$type] ?? 'bi-bell-fill';
    }

    /**
     * =========================================================================
     * GET NOTIFICATION COLOR
     * =========================================================================
     * 
     * Get the Bootstrap color class for a notification type.
     * 
     * @param string $type  Notification type
     * @return string       Bootstrap text color class
     */
    public function getColor(string $type): string {
        $colors = [
            'match'             => 'text-primary',
            'message'           => 'text-info',
            'exchange_request'  => 'text-warning',
            'exchange_accepted' => 'text-success',
            'exchange_declined' => 'text-danger',
            'exchange_completed'=> 'text-success',
            'review'            => 'text-warning',
            'system'            => 'text-secondary'
        ];

        return $colors[$type] ?? 'text-secondary';
    }

    /**
     * =========================================================================
     * CREATE EXCHANGE REQUEST NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify receiver of new exchange request.
     * 
     * @param int    $receiverId    User receiving the notification
     * @param int    $exchangeId    Exchange request ID
     * @param string $requesterName Name of user who sent request
     * @return int|false
     */
    public function notifyExchangeRequest(int $receiverId, int $exchangeId, string $requesterName): int|false {
        return $this->create(
            $receiverId,
            'exchange_request',
            $exchangeId,
            'New Exchange Request',
            $requesterName . ' has sent you a skill exchange request.'
        );
    }

    /**
     * =========================================================================
     * CREATE EXCHANGE ACCEPTED NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify requester that exchange was accepted.
     * 
     * @param int    $requesterId  User who sent the request
     * @param int    $exchangeId   Exchange request ID
     * @param string $receiverName Name of user who accepted
     * @return int|false
     */
    public function notifyExchangeAccepted(int $requesterId, int $exchangeId, string $receiverName): int|false {
        return $this->create(
            $requesterId,
            'exchange_accepted',
            $exchangeId,
            'Exchange Accepted',
            $receiverName . ' has accepted your skill exchange request!'
        );
    }

    /**
     * =========================================================================
     * CREATE EXCHANGE DECLINED NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify requester that exchange was declined.
     * 
     * @param int    $requesterId  User who sent the request
     * @param int    $exchangeId   Exchange request ID
     * @param string $receiverName Name of user who declined
     * @return int|false
     */
    public function notifyExchangeDeclined(int $requesterId, int $exchangeId, string $receiverName): int|false {
        return $this->create(
            $requesterId,
            'exchange_declined',
            $exchangeId,
            'Exchange Declined',
            $receiverName . ' has declined your skill exchange request.'
        );
    }

    /**
     * =========================================================================
     * CREATE EXCHANGE COMPLETED NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify both users that exchange is completed.
     * 
     * @param int    $userId       User to notify
     * @param int    $exchangeId   Exchange request ID
     * @param string $partnerName  Name of exchange partner
     * @return int|false
     */
    public function notifyExchangeCompleted(int $userId, int $exchangeId, string $partnerName): int|false {
        return $this->create(
            $userId,
            'exchange_completed',
            $exchangeId,
            'Exchange Completed',
            'Your skill exchange with ' . $partnerName . ' has been marked as completed.'
        );
    }

    /**
     * =========================================================================
     * CREATE NEW MESSAGE NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify user of new message.
     * 
     * @param int    $receiverId   User receiving the message
     * @param int    $senderId     User who sent the message
     * @param string $senderName   Name of sender
     * @return int|false
     */
    public function notifyNewMessage(int $receiverId, int $senderId, string $senderName): int|false {
        return $this->create(
            $receiverId,
            'message',
            $senderId,
            'New Message',
            'You have a new message from ' . $senderName . '.'
        );
    }

    /**
     * =========================================================================
     * CREATE NEW REVIEW NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify user of new review.
     * 
     * @param int    $revieweeId   User being reviewed
     * @param int    $reviewId     Review ID
     * @param string $reviewerName Name of reviewer
     * @return int|false
     */
    public function notifyNewReview(int $revieweeId, int $reviewId, string $reviewerName): int|false {
        return $this->create(
            $revieweeId,
            'review',
            $reviewId,
            'New Review',
            $reviewerName . ' has left you a review.'
        );
    }

    /**
     * =========================================================================
     * CREATE MATCH NOTIFICATION
     * =========================================================================
     * 
     * Helper method to notify user of new match.
     * 
     * @param int    $userId      User to notify
     * @param int    $matchId     Match ID
     * @param string $matchName   Name of matched user
     * @param int    $matchScore  Match percentage score
     * @return int|false
     */
    public function notifyMatch(int $userId, int $matchId, string $matchName, int $matchScore): int|false {
        return $this->create(
            $userId,
            'match',
            $matchId,
            'New Match Found!',
            'You have been matched with ' . $matchName . '! Match score: ' . $matchScore . '%'
        );
    }

    /**
     * =========================================================================
     * CLEAR OLD NOTIFICATIONS
    * =========================================================================
     * 
     * Delete notifications older than specified days.
     * 
     * @param int $days  Delete notifications older than this many days
     * @return int       Number of deleted notifications
     */
    public function clearOld(int $days = 30): int {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE is_read = 1 
                AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
            ");

            $stmt->execute([':days' => $days]);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log("Clear Old Notifications Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
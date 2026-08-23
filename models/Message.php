<?php

/**
 * ============================================================================
 * Message Model
 * ============================================================================
 * 
 * Handles all database operations related to messaging.
 * Includes: sending, retrieving, marking as read, deleting messages.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Message
{
    /**
     * Database connection instance
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor - initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * =========================================================================
     * SEND MESSAGE
     * =========================================================================
     * 
     * Create a new message from sender to receiver.
     * 
     * @param int    $senderId    User ID of sender
     * @param int    $receiverId  User ID of receiver
     * @param string $content     Message content
     * @param int    $exchangeId  Optional exchange request ID for context
     * @return int|false          New message ID or false
     */
    public function send(int $senderId, int $receiverId, string $content, int $exchangeId = 0): int|false
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO messages (sender_id, receiver_id, exchange_request_id, content, is_read, created_at)
                VALUES (:sender_id, :receiver_id, :exchange_request_id, :content, 0, NOW())
            ");

            $stmt->execute([
                ':sender_id'           => $senderId,
                ':receiver_id'         => $receiverId,
                ':exchange_request_id' => $exchangeId > 0 ? $exchangeId : null,
                ':content'             => trim($content)
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Send Message Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET MESSAGE BY ID
     * =========================================================================
     * 
     * Retrieve a single message by ID.
     * 
     * @param int $messageId  Message ID
     * @return array|false
     */
    public function getById(int $messageId): array|false
    {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*,
                    s.full_name as sender_name,
                    r.full_name as receiver_name,
                    s_p.profile_picture as sender_picture,
                    r_p.profile_picture as receiver_picture
                FROM messages m
                JOIN users s ON m.sender_id = s.id
                JOIN users r ON m.receiver_id = r.id
                LEFT JOIN profiles s_p ON s.id = s_p.user_id
                LEFT JOIN profiles r_p ON r.id = r_p.user_id
                WHERE m.id = :id
                LIMIT 1
            ");

            $stmt->execute([':id' => $messageId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Message By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET CONVERSATION BETWEEN TWO USERS
     * =========================================================================
     * 
     * Get all messages between two users, ordered chronologically.
     * 
     * @param int $userId1  First user ID
     * @param int $userId2  Second user ID
     * @param int $limit    Maximum messages to retrieve
     * @param int $offset   Pagination offset
     * @return array
     */
    public function getConversation(
        int $userId1,
        int $userId2,
        int $limit = 50,
        int $offset = 0
    ): array {
        try {
            $stmt = $this->db->prepare("
            SELECT
                m.*,
                s.full_name AS sender_name,
                r.full_name AS receiver_name,
                s_p.profile_picture AS sender_picture,
                r_p.profile_picture AS receiver_picture

            FROM messages m

            JOIN users s
                ON m.sender_id = s.id

            JOIN users r
                ON m.receiver_id = r.id

            LEFT JOIN profiles s_p
                ON s.id = s_p.user_id

            LEFT JOIN profiles r_p
                ON r.id = r_p.user_id

            WHERE
                (
                    m.sender_id = :user_id_1
                    AND m.receiver_id = :user_id_2
                )
                OR
                (
                    m.sender_id = :user_id_3
                    AND m.receiver_id = :user_id_4
                )

            ORDER BY m.created_at ASC

            LIMIT :limit
            OFFSET :offset
        ");

            $stmt->bindValue(':user_id_1', $userId1, PDO::PARAM_INT);
            $stmt->bindValue(':user_id_2', $userId2, PDO::PARAM_INT);
            $stmt->bindValue(':user_id_3', $userId2, PDO::PARAM_INT);
            $stmt->bindValue(':user_id_4', $userId1, PDO::PARAM_INT);

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Conversation Error: " . $e->getMessage());
            return [];
        }
    }


    /**
     * =========================================================================
     * GET RECENT CONVERSATIONS
     * =========================================================================
     * 
     * Get list of recent conversations for a user.
     * Returns one message per conversation (the most recent).
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getRecentConversations(int $userId): array
    {
        try {

            $stmt = $this->db->prepare("
            SELECT
                m.*,

                CASE
                    WHEN m.sender_id = :uid1
                    THEN m.receiver_id
                    ELSE m.sender_id
                END AS other_user_id,

                CASE
                    WHEN m.sender_id = :uid2
                    THEN r.full_name
                    ELSE s.full_name
                END AS other_user_name,

                CASE
                    WHEN m.sender_id = :uid3
                    THEN r_p.profile_picture
                    ELSE s_p.profile_picture
                END AS other_user_picture,

                CASE
                    WHEN m.sender_id = :uid4
                    THEN 'sent'
                    ELSE 'received'
                END AS direction,

                (
                    SELECT COUNT(*)
                    FROM messages unread
                    WHERE
                        unread.sender_id =
                            CASE
                                WHEN m.sender_id = :uid5
                                THEN m.receiver_id
                                ELSE m.sender_id
                            END
                        AND unread.receiver_id = :uid6
                        AND unread.is_read = 0
                ) AS unread_count

            FROM messages m

            JOIN users s
                ON m.sender_id = s.id

            JOIN users r
                ON m.receiver_id = r.id

            LEFT JOIN profiles s_p
                ON s.id = s_p.user_id

            LEFT JOIN profiles r_p
                ON r.id = r_p.user_id

            WHERE m.id IN (

                SELECT MAX(id)
                FROM messages

                WHERE
                    sender_id = :uid7
                    OR receiver_id = :uid8

                GROUP BY
                    LEAST(sender_id, receiver_id),
                    GREATEST(sender_id, receiver_id)
            )

            ORDER BY m.created_at DESC
        ");

            $stmt->execute([
                ':uid1' => $userId,
                ':uid2' => $userId,
                ':uid3' => $userId,
                ':uid4' => $userId,
                ':uid5' => $userId,
                ':uid6' => $userId,
                ':uid7' => $userId,
                ':uid8' => $userId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(
                "Get Recent Conversations Error: "
                    . $e->getMessage()
            );

            return [];
        }
    }

    /**
     * =========================================================================
     * GET NEW MESSAGES
     * =========================================================================
     * 
     * Get messages sent after a specific message ID.
     * Used for AJAX chat refresh.
     * 
     * @param int $userId1    Current user ID
     * @param int $userId2    Other user ID
     * @param int $lastMsgId  Last message ID already displayed
     * @return array
     */

    public function getNewMessages(
        int $userId1,
        int $userId2,
        int $lastMsgId
    ): array {
        try {

            $stmt = $this->db->prepare("
            SELECT
                m.*,
                s.full_name AS sender_name,
                s_p.profile_picture AS sender_picture

            FROM messages m

            JOIN users s
                ON m.sender_id = s.id

            LEFT JOIN profiles s_p
                ON s.id = s_p.user_id

            WHERE
                m.id > :last_id

                AND
                (
                    (
                        m.sender_id = :uid1
                        AND m.receiver_id = :uid2
                    )
                    OR
                    (
                        m.sender_id = :uid3
                        AND m.receiver_id = :uid4
                    )
                )

            ORDER BY m.created_at ASC
        ");

            $stmt->execute([
                ':last_id' => $lastMsgId,
                ':uid1' => $userId1,
                ':uid2' => $userId2,
                ':uid3' => $userId2,
                ':uid4' => $userId1
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get New Messages Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * MARK AS READ
     * =========================================================================
     * 
     * Mark all messages from sender to receiver as read.
     * 
     * @param int $senderId    Sender user ID
     * @param int $receiverId  Receiver user ID (current user)
     * @return bool
     */
    public function markAsRead(int $senderId, int $receiverId): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE messages 
                SET is_read = 1 
                WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND is_read = 0
            ");

            return $stmt->execute([
                ':sender_id'   => $senderId,
                ':receiver_id' => $receiverId
            ]);
        } catch (PDOException $e) {
            error_log("Mark As Read Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE MESSAGE
     * =========================================================================
     * 
     * Soft delete a message (mark as deleted).
     * In this simple implementation, we physically delete the message.
     * 
     * @param int $messageId  Message ID
     * @param int $userId     User ID (must be sender or receiver)
     * @return bool
     */

    public function delete(int $messageId, int $userId): bool
    {
        try {

            $stmt = $this->db->prepare("
            DELETE FROM messages
            WHERE id = :id
            AND (
                sender_id = :sender_id
                OR receiver_id = :receiver_id
            )
        ");

            return $stmt->execute([
                ':id' => $messageId,
                ':sender_id' => $userId,
                ':receiver_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Delete Message Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COUNT UNREAD MESSAGES
     * =========================================================================
     * 
     * Count total unread messages for a user.
     * 
     * @param int $userId  User ID
     * @return int
     */
    public function countUnread(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM messages 
                WHERE receiver_id = :user_id AND is_read = 0
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
     * COUNT UNREAD FROM USER
     * =========================================================================
     * 
     * Count unread messages from a specific sender.
     * 
     * @param int $senderId    Sender user ID
     * @param int $receiverId  Receiver user ID
     * @return int
     */
    public function countUnreadFromUser(int $senderId, int $receiverId): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM messages 
                WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND is_read = 0
            ");

            $stmt->execute([
                ':sender_id'   => $senderId,
                ':receiver_id' => $receiverId
            ]);
            $result = $stmt->fetch();
            return (int) $result['total'];
        } catch (PDOException $e) {
            error_log("Count Unread From User Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * GET MESSAGES BY EXCHANGE
     * =========================================================================
     * 
     * Get all messages related to an exchange request.
     * 
     * @param int $exchangeId  Exchange request ID
     * @return array
     */
    public function getByExchange(int $exchangeId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*,
                    s.full_name as sender_name,
                    s_p.profile_picture as sender_picture
                FROM messages m
                JOIN users s ON m.sender_id = s.id
                LEFT JOIN profiles s_p ON s.id = s_p.user_id
                WHERE m.exchange_request_id = :exchange_id
                ORDER BY m.created_at ASC
            ");

            $stmt->execute([':exchange_id' => $exchangeId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Messages By Exchange Error: " . $e->getMessage());
            return [];
        }
    }
}

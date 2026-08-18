<?php
/**
 * ============================================================================
 * Exchange Model
 * ============================================================================
 * 
 * Handles all database operations related to skill exchanges.
 * Includes: creating requests, managing status, exchange history.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Exchange {
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
     * CREATE EXCHANGE REQUEST
     * =========================================================================
     * 
     * Send a new skill exchange request from one user to another.
     * 
     * @param int    $matchId          The match ID that initiated this exchange
     * @param int    $requesterId      User sending the request
     * @param int    $receiverId       User receiving the request
     * @param int    $offeredSkillId   Skill the requester will teach
     * @param int    $requestedSkillId Skill the requester wants to learn
     * @param string $message          Optional message with the request
     * @return int|false               New exchange request ID or false
     */
    public function create(int $matchId, int $requesterId, int $receiverId, int $offeredSkillId, int $requestedSkillId, string $message = ''): int|false {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO exchange_requests 
                (match_id, requester_id, receiver_id, offered_skill_id, requested_skill_id, message, status, created_at, updated_at)
                VALUES 
                (:match_id, :requester_id, :receiver_id, :offered_skill_id, :requested_skill_id, :message, 'pending', NOW(), NOW())
            ");

            $stmt->execute([
                ':match_id'          => $matchId,
                ':requester_id'      => $requesterId,
                ':receiver_id'       => $receiverId,
                ':offered_skill_id'  => $offeredSkillId,
                ':requested_skill_id'=> $requestedSkillId,
                ':message'           => $message
            ]);

            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Create Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET EXCHANGE BY ID
     * =========================================================================
     * 
     * Retrieve a single exchange request with full details.
     * 
     * @param int $exchangeId  Exchange request ID
     * @return array|false
     */
    public function getById(int $exchangeId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT er.*,
                    m.match_score,
                    req.full_name as requester_name,
                    rec.full_name as receiver_name,
                    req_p.profile_picture as requester_picture,
                    rec_p.profile_picture as receiver_picture,
                    os.name as offered_skill_name,
                    rs.name as requested_skill_name,
                    oc.name as offered_category,
                    rc.name as requested_category
                FROM exchange_requests er
                JOIN matches m ON er.match_id = m.id
                JOIN users req ON er.requester_id = req.id
                JOIN users rec ON er.receiver_id = rec.id
                LEFT JOIN profiles req_p ON req.id = req_p.user_id
                LEFT JOIN profiles rec_p ON rec.id = rec_p.user_id
                JOIN skills os ON er.offered_skill_id = os.id
                JOIN skills rs ON er.requested_skill_id = rs.id
                JOIN categories oc ON os.category_id = oc.id
                JOIN categories rc ON rs.category_id = rc.id
                WHERE er.id = :id
                LIMIT 1
            ");

            $stmt->execute([':id' => $exchangeId]);
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get Exchange By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET EXCHANGE INVOLVING USER
     * =========================================================================
     * 
     * Get a single exchange and verify the user is part of it.
     * 
     * @param int $exchangeId  Exchange ID
     * @param int $userId      User ID (for verification)
     * @return array|false
     */
    public function getForUser(int $exchangeId, int $userId): array|false
{
    try {
        $sql = "
            SELECT
                er.*,

                req.full_name AS requester_name,
                req_profile.profile_picture AS requester_avatar,

                rec.full_name AS receiver_name,
                rec_profile.profile_picture AS receiver_avatar,

                os.name AS offered_skill_name,
                rs.name AS requested_skill_name

            FROM exchange_requests er

            LEFT JOIN users req
                ON er.requester_id = req.id

            LEFT JOIN profiles req_profile
                ON req.id = req_profile.user_id

            LEFT JOIN users rec
                ON er.receiver_id = rec.id

            LEFT JOIN profiles rec_profile
                ON rec.id = rec_profile.user_id

            LEFT JOIN skills os
                ON er.offered_skill_id = os.id

            LEFT JOIN skills rs
                ON er.requested_skill_id = rs.id

            WHERE er.id = ?
            AND (
                er.requester_id = ?
                OR er.receiver_id = ?
            )

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $exchangeId,
            $userId,
            $userId
        ]);

        $exchange = $stmt->fetch();

        return $exchange ?: false;

    } catch (PDOException $e) {
        error_log("Get Exchange Error: " . $e->getMessage());
        return false;
    }
}

    /**
     * =========================================================================
     * GET ALL EXCHANGES FOR A USER
     * =========================================================================
     * 
     * Get exchange requests where user is either requester or receiver.
     * 
     * @param int    $userId  User ID
     * @param string $status  Filter by status (empty = all)
     * @param int    $limit   Results per page
     * @param int    $offset  Pagination offset
     * @return array
     */
    // public function getUserExchanges(int $userId, string $status = '', int $limit = 10, int $offset = 0): array {
    //     try {
    //         $sql = "
    //             SELECT er.*,
    //                 req.full_name as requester_name,
    //                 rec.full_name as receiver_name,
    //                 req_p.profile_picture as requester_picture,
    //                 rec_p.profile_picture as receiver_picture,
    //                 os.name as offered_skill_name,
    //                 rs.name as requested_skill_name,
    //                 CASE 
    //                     WHEN er.requester_id = :user_id THEN 'sent'
    //                     ELSE 'received'
    //                 END as direction
    //             FROM exchange_requests er
    //             JOIN users req ON er.requester_id = req.id
    //             JOIN users rec ON er.receiver_id = rec.id
    //             LEFT JOIN profiles req_p ON req.id = req_p.user_id
    //             LEFT JOIN profiles rec_p ON rec.id = rec_p.user_id
    //             JOIN skills os ON er.offered_skill_id = os.id
    //             JOIN skills rs ON er.requested_skill_id = rs.id
    //             WHERE er.requester_id = :user_id OR er.receiver_id = :user_id
    //         ";

    //         $params = [':user_id' => $userId];

    //         if (!empty($status)) {
    //             $sql .= " AND er.status = :status";
    //             $params[':status'] = $status;
    //         }

    //         $sql .= " ORDER BY er.created_at DESC LIMIT :limit OFFSET :offset";

    //         $stmt = $this->db->prepare($sql);

    //         foreach ($params as $key => $value) {
    //             $stmt->bindValue($key, $value);
    //         }

    //         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    //         $stmt->execute();
    //         return $stmt->fetchAll();

    //     } catch (PDOException $e) {
    //         error_log("Get User Exchanges Error: " . $e->getMessage());
    //         return [];
    //     }
    // }
    public function getUserExchanges(
    int $userId,
    string $status = '',
    int $limit = 10,
    int $offset = 0
): array {
    try {

        $sql = "
            SELECT
                er.*,

                req.full_name AS requester_name,
                rec.full_name AS receiver_name,

                req_p.profile_picture AS requester_picture,
                rec_p.profile_picture AS receiver_picture,

                os.name AS offered_skill_name,
                rs.name AS requested_skill_name,

                CASE
                    WHEN er.requester_id = ? THEN 'sent'
                    ELSE 'received'
                END AS direction,

                CASE
                    WHEN er.requester_id = ? THEN er.receiver_id
                    ELSE er.requester_id
                END AS other_user_id,

                CASE
                    WHEN er.requester_id = ? THEN rec.full_name
                    ELSE req.full_name
                END AS other_user_name,

                CASE
                    WHEN er.requester_id = ? THEN rec_p.profile_picture
                    ELSE req_p.profile_picture
                END AS other_user_avatar

            FROM exchange_requests er

            JOIN users req
                ON er.requester_id = req.id

            JOIN users rec
                ON er.receiver_id = rec.id

            LEFT JOIN profiles req_p
                ON req.id = req_p.user_id

            LEFT JOIN profiles rec_p
                ON rec.id = rec_p.user_id

            JOIN skills os
                ON er.offered_skill_id = os.id

            JOIN skills rs
                ON er.requested_skill_id = rs.id

            WHERE (
                er.requester_id = ?
                OR er.receiver_id = ?
            )
        ";

        /*
         * Parameters for the six ? placeholders above:
         *
         * 1. direction
         * 2. other_user_id
         * 3. other_user_name
         * 4. other_user_avatar
         * 5. requester condition
         * 6. receiver condition
         */
        $params = [
            $userId,
            $userId,
            $userId,
            $userId,
            $userId,
            $userId
        ];

        /*
         * Optional status filter
         */
        if (!empty($status)) {
            $sql .= " AND er.status = ?";
            $params[] = $status;
        }

        $sql .= "
            ORDER BY er.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);

        /*
         * Bind normal parameters
         */
        foreach ($params as $index => $value) {

            $parameterNumber = $index + 1;

            if (
                $parameterNumber === count($params) - 1 ||
                $parameterNumber === count($params)
            ) {
                $stmt->bindValue(
                    $parameterNumber,
                    (int)$value,
                    PDO::PARAM_INT
                );
            } else {
                $stmt->bindValue(
                    $parameterNumber,
                    $value
                );
            }
        }

        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {

        error_log(
            "Get User Exchanges Error: " . $e->getMessage()
        );

        return [];
    }
}

    /**
     * =========================================================================
     * GET PENDING REQUESTS RECEIVED
     * =========================================================================
     * 
     * Get exchange requests received by a user that are still pending.
     * 
     * @param int $userId  User ID
     * @param int $limit   Results limit
     * @return array
     */
    public function getPendingReceived(int $userId, int $limit = 5): array {
        try {
            $stmt = $this->db->prepare("
                SELECT er.*,
                    req.full_name as requester_name,
                    req_p.profile_picture as requester_picture,
                    os.name as offered_skill_name,
                    rs.name as requested_skill_name
                FROM exchange_requests er
                JOIN users req ON er.requester_id = req.id
                LEFT JOIN profiles req_p ON req.id = req_p.user_id
                JOIN skills os ON er.offered_skill_id = os.id
                JOIN skills rs ON er.requested_skill_id = rs.id
                WHERE er.receiver_id = :user_id AND er.status = 'pending'
                ORDER BY er.created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Pending Received Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * UPDATE EXCHANGE STATUS
     * =========================================================================
     * 
     * Update the status of an exchange request.
     * 
     * @param int    $exchangeId  Exchange ID
     * @param string $status      New status
     * @return bool
     */
    public function updateStatus(int $exchangeId, string $status): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = :status,
                    updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute([
                ':status' => $status,
                ':id'     => $exchangeId
            ]);

        } catch (PDOException $e) {
            error_log("Update Exchange Status Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * ACCEPT EXCHANGE REQUEST
     * =========================================================================
     * 
     * Accept a pending exchange request and set start date.
     * 
     * @param int $exchangeId  Exchange ID
     * @return bool
     */
    public function accept(int $exchangeId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = 'accepted',
                    start_date = CURDATE(),
                    updated_at = NOW()
                WHERE id = :id AND status = 'pending'
            ");

            return $stmt->execute([':id' => $exchangeId]);

        } catch (PDOException $e) {
            error_log("Accept Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * REJECT EXCHANGE REQUEST
     * =========================================================================
     * 
     * Decline a pending exchange request.
     * 
     * @param int $exchangeId  Exchange ID
     * @return bool
     */
    public function reject(int $exchangeId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = 'declined',
                    updated_at = NOW()
                WHERE id = :id AND status = 'pending'
            ");

            return $stmt->execute([':id' => $exchangeId]);

        } catch (PDOException $e) {
            error_log("Reject Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * START EXCHANGE
     * =========================================================================
     * 
     * Mark an accepted exchange as in progress.
     * 
     * @param int $exchangeId  Exchange ID
     * @return bool
     */
    public function start(int $exchangeId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = 'in_progress',
                    updated_at = NOW()
                WHERE id = :id AND status = 'accepted'
            ");

            return $stmt->execute([':id' => $exchangeId]);

        } catch (PDOException $e) {
            error_log("Start Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COMPLETE EXCHANGE
     * =========================================================================
     * 
     * Mark an exchange as completed and set end date.
     * 
     * @param int $exchangeId  Exchange ID
     * @return bool
     */
    public function complete(int $exchangeId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = 'completed',
                    end_date = CURDATE(),
                    updated_at = NOW()
                WHERE id = :id AND status = 'in_progress'
            ");

            return $stmt->execute([':id' => $exchangeId]);

        } catch (PDOException $e) {
            error_log("Complete Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * CANCEL EXCHANGE
     * =========================================================================
     * 
     * Cancel an exchange that is not yet completed.
     * 
     * @param int $exchangeId  Exchange ID
     * @return bool
     */
    public function cancel(int $exchangeId): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE exchange_requests 
                SET status = 'cancelled',
                    updated_at = NOW()
                WHERE id = :id AND status IN ('pending', 'accepted', 'in_progress')
            ");

            return $stmt->execute([':id' => $exchangeId]);

        } catch (PDOException $e) {
            error_log("Cancel Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET EXCHANGE HISTORY
     * =========================================================================
     * 
     * Get completed exchanges for a user.
     * 
     * @param int $userId  User ID
     * @param int $limit   Results limit
     * @return array
     */
    public function getHistory(int $userId, int $limit = 20): array {
        try {
            $stmt = $this->db->prepare("
                SELECT er.*,
                    req.full_name as requester_name,
                    rec.full_name as receiver_name,
                    req_p.profile_picture as requester_picture,
                    rec_p.profile_picture as receiver_picture,
                    os.name as offered_skill_name,
                    rs.name as requested_skill_name,
                    DATEDIFF(er.end_date, er.start_date) as duration_days
                FROM exchange_requests er
                JOIN users req ON er.requester_id = req.id
                JOIN users rec ON er.receiver_id = rec.id
                LEFT JOIN profiles req_p ON req.id = req_p.user_id
                LEFT JOIN profiles rec_p ON rec.id = rec_p.user_id
                JOIN skills os ON er.offered_skill_id = os.id
                JOIN skills rs ON er.requested_skill_id = rs.id
                WHERE (er.requester_id = :user_id OR er.receiver_id = :user_id)
                AND er.status = 'completed'
                ORDER BY er.end_date DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Exchange History Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * COUNT USER EXCHANGES
     * =========================================================================
     * 
     * Count total exchanges for a user.
     * 
     * @param int    $userId  User ID
     * @param string $status  Filter by status
     * @return int
     */
    public function countUserExchanges(int $userId, string $status = ''): int {
        try {
            $sql = "
                SELECT COUNT(*) as total 
                FROM exchange_requests 
                WHERE (requester_id = :user_id OR receiver_id = :user_id)
            ";
            $params = [':user_id' => $userId];

            if (!empty($status)) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Count User Exchanges Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * CHECK IF USERS HAVE ACTIVE EXCHANGE
     * =========================================================================
     * 
     * Check if two users already have an active exchange.
     * 
     * @param int $userId1  First user ID
     * @param int $userId2  Second user ID
     * @return bool
     */
    public function hasActiveExchange(int $userId1, int $userId2): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM exchange_requests 
                WHERE ((requester_id = :id1 AND receiver_id = :id2) 
                   OR (requester_id = :id2 AND receiver_id = :id1))
                AND status IN ('pending', 'accepted', 'in_progress')
            ");

            $stmt->execute([
                ':id1' => $userId1,
                ':id2' => $userId2
            ]);

            $result = $stmt->fetch();
            return $result['total'] > 0;

        } catch (PDOException $e) {
            error_log("Has Active Exchange Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET EXCHANGE STATS
     * =========================================================================
     * 
     * Get exchange statistics for dashboard.
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getStats(int $userId): array {
        try {
            $stats = [];

            // Total exchanges
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined
                FROM exchange_requests 
                WHERE requester_id = :user_id OR receiver_id = :user_id
            ");
            $stmt->execute([':user_id' => $userId]);
            $stats = $stmt->fetch();

            // Exchanges as teacher (offered skill)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM exchange_requests 
                WHERE requester_id = :user_id AND status = 'completed'
            ");
            $stmt->execute([':user_id' => $userId]);
            $stats['as_teacher'] = (int) $stmt->fetch()['total'];

            // Exchanges as learner (requested skill)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM exchange_requests 
                WHERE receiver_id = :user_id AND status = 'completed'
            ");
            $stmt->execute([':user_id' => $userId]);
            $stats['as_learner'] = (int) $stmt->fetch()['total'];

            return $stats;

        } catch (PDOException $e) {
            error_log("Get Exchange Stats Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
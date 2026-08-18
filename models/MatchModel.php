<?php

/**
 * ============================================================================
 * Match Model
 * ============================================================================
 * 
 * Handles all database operations related to skill matching.
 * Stores match results and manages match status between users.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class MatchModel
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
     * CREATE OR UPDATE MATCH
     * =========================================================================
     * 
     * Save a match result between two users.
     * If match already exists, update the score.
     * 
     * @param int    $userId1      First user ID
     * @param int    $userId2      Second user ID
     * @param int    $matchScore   Calculated match score (0-100)
     * @param int    $skillId      The skill that created the match (optional)
     * @param string $notes        Match explanation notes
     * @return int|false           Match ID or false
     */
    public function save(int $userId1, int $userId2, int $matchScore, int $skillId = 0, string $notes = ''): int|false
    {
        try {
            // Ensure user_id_1 is always the smaller ID for consistency
            $id1 = min($userId1, $userId2);
            $id2 = max($userId1, $userId2);

            // Check if match already exists
            $existing = $this->getMatch($id1, $id2);

            if ($existing) {
                // Update existing match if new score is higher
                if ($matchScore > $existing['match_score']) {
                    $stmt = $this->db->prepare("
                        UPDATE matches 
                        SET match_score = :match_score,
                            matched_skill_id = :skill_id,
                            notes = :notes,
                            updated_at = NOW()
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':match_score' => $matchScore,
                        ':skill_id'    => $skillId,
                        ':notes'       => $notes,
                        ':id'          => $existing['id']
                    ]);
                }
                return $existing['id'];
            } else {
                // Create new match
                $stmt = $this->db->prepare("
                    INSERT INTO matches (user_id_1, user_id_2, match_score, status, user_1_response, user_2_response, matched_skill_id, notes, created_at, updated_at)
                    VALUES (:user_id_1, :user_id_2, :match_score, 'pending', 'pending', 'pending', :skill_id, :notes, NOW(), NOW())
                ");
                $stmt->execute([
                    ':user_id_1'   => $id1,
                    ':user_id_2'   => $id2,
                    ':match_score' => $matchScore,
                    ':skill_id'    => $skillId,
                    ':notes'       => $notes
                ]);
                return (int) $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Save Match Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET MATCH BETWEEN TWO USERS
     * =========================================================================
     * 
     * Find an existing match record between two users.
     * 
     * @param int $userId1  First user ID
     * @param int $userId2  Second user ID
     * @return array|false   Match data or false
     */
    public function getMatch(int $userId1, int $userId2): array|false
    {
        try {
            $id1 = min($userId1, $userId2);
            $id2 = max($userId1, $userId2);

            $stmt = $this->db->prepare("
                SELECT * FROM matches 
                WHERE user_id_1 = :id1 AND user_id_2 = :id2
                LIMIT 1
            ");
            $stmt->execute([
                ':id1' => $id1,
                ':id2' => $id2
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Match Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET MATCH BY ID
     * =========================================================================
     * 
     * Retrieve a specific match by its ID.
     * 
     * @param int $matchId  Match ID
     * @return array|false
     */
    public function getById(int $matchId): array|false
    {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, 
                    u1.full_name as user_1_name, u2.full_name as user_2_name,
                    s.name as matched_skill_name
                FROM matches m
                JOIN users u1 ON m.user_id_1 = u1.id
                JOIN users u2 ON m.user_id_2 = u2.id
                LEFT JOIN skills s ON m.matched_skill_id = s.id
                WHERE m.id = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $matchId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Match By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET ALL MATCHES FOR A USER
     * =========================================================================
     * 
     * Get all matches where the user is involved.
     * 
     * @param int    $userId  User ID
     * @param string $status  Filter by status (empty = all)
     * @param int    $limit   Results limit
     * @param int    $offset  Pagination offset
     * @return array
     */
    // public function getUserMatches(int $userId, string $status = '', int $limit = 10, int $offset = 0): array {
    //     try {
    //         $sql = "
    //             SELECT m.*, 
    //                 CASE 
    //                     WHEN m.user_id_1 = :user_id THEN u2.full_name 
    //                     ELSE u1.full_name 
    //                 END as other_user_name,
    //                 CASE 
    //                     WHEN m.user_id_1 = :user_id THEN p2.profile_picture 
    //                     ELSE p1.profile_picture 
    //                 END as other_user_picture,
    //                 CASE 
    //                     WHEN m.user_id_1 = :user_id THEN p2.experience_level 
    //                     ELSE p1.experience_level 
    //                 END as other_user_experience,
    //                 s.name as matched_skill_name,
    //                 CASE 
    //                     WHEN m.user_id_1 = :user_id THEN m.user_1_response 
    //                     ELSE m.user_2_response 
    //                 END as my_response,
    //                 CASE 
    //                     WHEN m.user_id_1 = :user_id THEN m.user_2_response 
    //                     ELSE m.user_1_response 
    //                 END as their_response
    //             FROM matches m
    //             JOIN users u1 ON m.user_id_1 = u1.id
    //             JOIN users u2 ON m.user_id_2 = u2.id
    //             LEFT JOIN profiles p1 ON u1.id = p1.user_id
    //             LEFT JOIN profiles p2 ON u2.id = p2.user_id
    //             LEFT JOIN skills s ON m.matched_skill_id = s.id
    //             WHERE (m.user_id_1 = :user_id OR m.user_id_2 = :user_id)
    //         ";

    //         $params = [':user_id' => $userId];

    //         if (!empty($status)) {
    //             $sql .= " AND m.status = :status";
    //             $params[':status'] = $status;
    //         }

    //         $sql .= " ORDER BY m.match_score DESC, m.created_at DESC LIMIT :limit OFFSET :offset";

    //         $stmt = $this->db->prepare($sql);

    //         foreach ($params as $key => $value) {
    //             $stmt->bindValue($key, $value);
    //         }

    //         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    //         $stmt->execute();
    //         return $stmt->fetchAll();

    //     } catch (PDOException $e) {
    //         error_log("Get User Matches Error: " . $e->getMessage());
    //         return [];
    //     }
    // }
    public function getUserMatches(
        int $userId,
        string $status = '',
        int $limit = 10,
        int $offset = 0
    ): array {
        try {
            $sql = "
            SELECT m.*,

                CASE
                    WHEN m.user_id_1 = :uid1
                    THEN u2.full_name
                    ELSE u1.full_name
                END AS other_user_name,

                CASE
                    WHEN m.user_id_1 = :uid2
                    THEN p2.profile_picture
                    ELSE p1.profile_picture
                END AS other_user_picture,

                CASE
                    WHEN m.user_id_1 = :uid3
                    THEN p2.experience_level
                    ELSE p1.experience_level
                END AS other_user_experience,

                s.name AS matched_skill_name,

                CASE
                    WHEN m.user_id_1 = :uid4
                    THEN m.user_1_response
                    ELSE m.user_2_response
                END AS my_response,

                CASE
                    WHEN m.user_id_1 = :uid5
                    THEN m.user_2_response
                    ELSE m.user_1_response
                END AS their_response

            FROM matches m

            JOIN users u1
                ON m.user_id_1 = u1.id

            JOIN users u2
                ON m.user_id_2 = u2.id

            LEFT JOIN profiles p1
                ON u1.id = p1.user_id

            LEFT JOIN profiles p2
                ON u2.id = p2.user_id

            LEFT JOIN skills s
                ON m.matched_skill_id = s.id

            WHERE (
                m.user_id_1 = :uid6
                OR m.user_id_2 = :uid7
            )
        ";

            $params = [
                ':uid1' => $userId,
                ':uid2' => $userId,
                ':uid3' => $userId,
                ':uid4' => $userId,
                ':uid5' => $userId,
                ':uid6' => $userId,
                ':uid7' => $userId
            ];

            if (!empty($status)) {
                $sql .= " AND m.status = :status";
                $params[':status'] = $status;
            }

            $sql .= "
            ORDER BY m.match_score DESC, m.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            error_log("Get User Matches Error: " . $e->getMessage());

            // TEMPORARY: show the actual error while debugging
            die("Get User Matches Error: " . $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * GET TOP MATCHES FOR A USER
     * =========================================================================
     * 
     * Get the highest scoring matches for a user.
     * Used for dashboard "Recommended Users" section.
     * 
     * @param int    $userId     User ID
     * @param int    $minScore   Minimum match score threshold
     * @param int    $limit      Number of matches to return
     * @return array
     */
    public function getTopMatches(int $userId, int $minScore = 30, int $limit = 5): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, 
                    CASE 
                        WHEN m.user_id_1 = :user_id THEN u2.full_name 
                        ELSE u1.full_name 
                    END as other_user_name,
                    CASE 
                        WHEN m.user_id_1 = :user_id THEN p2.profile_picture 
                        ELSE p1.profile_picture 
                    END as other_user_picture,
                    CASE 
                        WHEN m.user_id_1 = :user_id THEN p2.experience_level 
                        ELSE p1.experience_level 
                    END as other_user_experience,
                    CASE 
                        WHEN m.user_id_1 = :user_id THEN p2.bio 
                        ELSE p1.bio 
                    END as other_user_bio,
                    s.name as matched_skill_name,
                    m.notes as match_reason
                FROM matches m
                JOIN users u1 ON m.user_id_1 = u1.id
                JOIN users u2 ON m.user_id_2 = u2.id
                LEFT JOIN profiles p1 ON u1.id = p1.user_id
                LEFT JOIN profiles p2 ON u2.id = p2.user_id
                LEFT JOIN skills s ON m.matched_skill_id = s.id
                WHERE (m.user_id_1 = :user_id OR m.user_id_2 = :user_id)
                    AND m.status = 'pending'
                    AND m.match_score >= :min_score
                ORDER BY m.match_score DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':min_score', $minScore, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Top Matches Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * UPDATE MATCH RESPONSE
     * =========================================================================
     * 
     * Update a user's response to a match (accept or decline).
     * 
     * @param int    $matchId   Match ID
     * @param int    $userId    User ID (who is responding)
     * @param string $response  'accepted' or 'declined'
     * @return bool
     */
    public function updateResponse(int $matchId, int $userId, string $response): bool
    {
        try {
            // First get the match to determine which user is responding
            $match = $this->getById($matchId);

            if (!$match) {
                return false;
            }

            // Determine which response field to update
            $responseField = ($match['user_id_1'] == $userId) ? 'user_1_response' : 'user_2_response';

            $stmt = $this->db->prepare("
                UPDATE matches 
                SET {$responseField} = :response,
                    updated_at = NOW()
                WHERE id = :id
            ");

            $result = $stmt->execute([
                ':response' => $response,
                ':id'       => $matchId
            ]);

            if ($result) {
                // Check if both users have responded
                $this->updateMatchStatus($matchId);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Update Match Response Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPDATE MATCH STATUS
     * =========================================================================
     * 
     * Check both user responses and update overall match status.
     * Called automatically after a response is updated.
     * 
     * @param int $matchId  Match ID
     * @return bool
     */
    private function updateMatchStatus(int $matchId): bool
    {
        try {
            $match = $this->getById($matchId);

            if (!$match) {
                return false;
            }

            $newStatus = 'pending';

            // If either user declined, match is declined
            if ($match['user_1_response'] === 'declined' || $match['user_2_response'] === 'declined') {
                $newStatus = 'declined';
            }
            // If both accepted, match is accepted
            elseif ($match['user_1_response'] === 'accepted' && $match['user_2_response'] === 'accepted') {
                $newStatus = 'accepted';
            }

            $stmt = $this->db->prepare("
                UPDATE matches 
                SET status = :status,
                    updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute([
                ':status' => $newStatus,
                ':id'     => $matchId
            ]);
        } catch (PDOException $e) {
            error_log("Update Match Status Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE MATCH
     * =========================================================================
     * 
     * Remove a match record.
     * 
     * @param int $matchId  Match ID
     * @return bool
     */
    public function delete(int $matchId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM matches WHERE id = :id");
            return $stmt->execute([':id' => $matchId]);
        } catch (PDOException $e) {
            error_log("Delete Match Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COUNT USER MATCHES
     * =========================================================================
     * 
     * Count total matches for a user.
     * 
     * @param int    $userId  User ID
     * @param string $status  Filter by status
     * @return int
     */
    public function countUserMatches(int $userId, string $status = ''): int
    {
        try {
            $sql = "
    SELECT COUNT(*) AS total
    FROM matches
    WHERE (
        user_id_1 = :user_id_1
        OR user_id_2 = :user_id_2
    )
";

            $params = [
                ':user_id_1' => $userId,
                ':user_id_2' => $userId
            ];

            if (!empty($status)) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':user_id_1', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id_2', $userId, PDO::PARAM_INT);

            if (!empty($status)) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            }

            $stmt->execute();

            $result = $stmt->fetch();

            return (int) $result['total'];
        } catch (PDOException $e) {
            error_log("Count User Matches Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * CLEAR OLD MATCHES
     * =========================================================================
     * 
     * Remove matches older than a certain date to keep database clean.
     * 
     * @param int $days  Delete matches older than this many days
     * @return int       Number of deleted records
     */
    public function clearOldMatches(int $days = 30): int
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM matches 
                WHERE status = 'declined' 
                AND updated_at < DATE_SUB(NOW(), INTERVAL :days DAY)
            ");
            $stmt->execute([':days' => $days]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Clear Old Matches Error: " . $e->getMessage());
            return 0;
        }
    }
}

<?php

/**
 * ============================================================================
 * Review Model
 * ============================================================================
 * 
 * Handles all database operations related t
 * o user reviews and ratings.
 * Includes: creating reviews, calculating averages, retrieving reviews.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Review
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
     * CREATE REVIEW
     * =========================================================================
     * 
     * Create a new review for a user after a completed exchange.
     * 
     * @param int    $exchangeId  The completed exchange request ID
     * @param int    $reviewerId  User writing the review
     * @param int    $revieweeId  User being reviewed
     * @param int    $rating      Rating from 1 to 5
     * @param string $comment     Review text/comment
     * @return int|false          New review ID or false
     */
    public function create(int $exchangeId, int $reviewerId, int $revieweeId, int $rating, string $comment): int|false
    {
        try {
            // Validate rating range
            if ($rating < 1 || $rating > 5) {
                return false;
            }

            $stmt = $this->db->prepare("
                INSERT INTO reviews (exchange_request_id, reviewer_id, reviewee_id, rating, comment, created_at)
                VALUES (:exchange_id, :reviewer_id, :reviewee_id, :rating, :comment, NOW())
            ");

            $stmt->execute([
                ':exchange_id' => $exchangeId,
                ':reviewer_id' => $reviewerId,
                ':reviewee_id' => $revieweeId,
                ':rating'      => $rating,
                ':comment'     => trim($comment)
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Review Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET REVIEW BY ID
     * =========================================================================
     * 
     * Retrieve a single review by ID.
     * 
     * @param int $reviewId  Review ID
     * @return array|false
     */
    public function getById(int $reviewId): array|false
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rev.full_name as reviewer_name,
                    rev_p.profile_picture as reviewer_picture,
                    rec.full_name as reviewee_name,
                    rec_p.profile_picture as reviewee_picture,
                    er.status as exchange_status
                FROM reviews r
                JOIN users rev ON r.reviewer_id = rev.id
                JOIN users rec ON r.reviewee_id = rec.id
                LEFT JOIN profiles rev_p ON rev.id = rev_p.user_id
                LEFT JOIN profiles rec_p ON rec.id = rec_p.user_id
                JOIN exchange_requests er ON r.exchange_request_id = er.id
                WHERE r.id = :id
                LIMIT 1
            ");

            $stmt->execute([':id' => $reviewId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Review By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET REVIEWS FOR A USER
     * =========================================================================
     * 
     * Get all reviews received by a specific user.
     * 
     * @param int $userId  User ID (reviewee)
     * @param int $limit   Maximum results
     * @param int $offset  Pagination offset
     * @return array
     */
    public function getForUser(int $userId, int $limit = 10, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rev.full_name as reviewer_name,
                    rev_p.profile_picture as reviewer_picture,
                    er.offered_skill_id,
                    er.requested_skill_id,
                    os.name as offered_skill_name,
                    rs.name as requested_skill_name
                FROM reviews r
                JOIN users rev ON r.reviewer_id = rev.id
                LEFT JOIN profiles rev_p ON rev.id = rev_p.user_id
                JOIN exchange_requests er ON r.exchange_request_id = er.id
                JOIN skills os ON er.offered_skill_id = os.id
                JOIN skills rs ON er.requested_skill_id = rs.id
                WHERE r.reviewee_id = :user_id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Reviews For User Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET REVIEWS BY EXCHANGE
     * =========================================================================
     * 
     * Get reviews for a specific exchange request.
     * 
     * @param int $exchangeId  Exchange request ID
     * @return array
     */
    public function getByExchange(int $exchangeId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rev.full_name as reviewer_name,
                    rev_p.profile_picture as reviewer_picture,
                    rec.full_name as reviewee_name
                FROM reviews r
                JOIN users rev ON r.reviewer_id = rev.id
                JOIN users rec ON r.reviewee_id = rec.id
                LEFT JOIN profiles rev_p ON rev.id = rev_p.user_id
                WHERE r.exchange_request_id = :exchange_id
                ORDER BY r.created_at DESC
            ");

            $stmt->execute([':exchange_id' => $exchangeId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Reviews By Exchange Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET AVERAGE RATING
     * =========================================================================
     * 
     * Calculate the average rating for a user.
     * 
     * @param int $userId  User ID
     * @return float       Average rating (0.0 to 5.0)
     */
    public function getAverageRating(int $userId): float
    {
        try {
            $stmt = $this->db->prepare("
                SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                FROM reviews
                WHERE reviewee_id = :user_id
            ");

            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();

            return $result['avg_rating'] ? round((float) $result['avg_rating'], 1) : 0.0;
        } catch (PDOException $e) {
            error_log("Get Average Rating Error: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * =========================================================================
     * GET RATING BREAKDOWN
     * =========================================================================
     * 
     * Get count of each star rating (1-5) for a user.
     * 
     * @param int $userId  User ID
     * @return array       Array with rating counts
     */
    public function getRatingBreakdown(int $userId): array
    {
        try {
            $breakdown = [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0
            ];

            $stmt = $this->db->prepare("
                SELECT rating, COUNT(*) as count
                FROM reviews
                WHERE reviewee_id = :user_id
                GROUP BY rating
                ORDER BY rating
            ");

            $stmt->execute([':user_id' => $userId]);

            while ($row = $stmt->fetch()) {
                $breakdown[(int) $row['rating']] = (int) $row['count'];
            }

            return $breakdown;
        } catch (PDOException $e) {
            error_log("Get Rating Breakdown Error: " . $e->getMessage());
            return [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        }
    }

    /**
     * =========================================================================
     * CHECK IF USER CAN REVIEW
     * =========================================================================
     * 
     * Check if a user has already reviewed an exchange or if exchange is completed.
     * 
     * @param int $exchangeId  Exchange request ID
     * @param int $reviewerId  User who wants to write review
     * @return array           ['can_review' => bool, 'reason' => string]
     */
    public function canReview(int $exchangeId, int $reviewerId): array
    {
        try {

            // Check if exchange exists, is completed,
            // and belongs to the current user.
            $stmt = $this->db->prepare("
            SELECT *
            FROM exchange_requests
            WHERE id = :exchange_id
              AND status = 'completed'
              AND (
                    requester_id = :requester_id
                    OR receiver_id = :receiver_id
              )
            LIMIT 1
        ");

            $stmt->execute([
                ':exchange_id' => $exchangeId,
                ':requester_id' => $reviewerId,
                ':receiver_id' => $reviewerId
            ]);

            $exchange = $stmt->fetch();

            if (!$exchange) {
                return [
                    'can_review' => false,
                    'reason' => 'Exchange not found or not completed yet.'
                ];
            }

            // Determine the other user.
            $revieweeId = (
                (int) $exchange['requester_id'] === $reviewerId
            )
                ? (int) $exchange['receiver_id']
                : (int) $exchange['requester_id'];

            // Check whether this user already reviewed this exchange.
            $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count
            FROM reviews
            WHERE exchange_request_id = :exchange_id
              AND reviewer_id = :reviewer_id
        ");

            $stmt->execute([
                ':exchange_id' => $exchangeId,
                ':reviewer_id' => $reviewerId
            ]);

            $result = $stmt->fetch();

            if ((int) $result['count'] > 0) {
                return [
                    'can_review' => false,
                    'reason' => 'You have already reviewed this exchange.'
                ];
            }

            return [
                'can_review' => true,
                'reason' => '',
                'reviewee_id' => $revieweeId
            ];
        } catch (PDOException $e) {

            error_log("Can Review Error: " . $e->getMessage());

            return [
                'can_review' => false,
                'reason' => 'An error occurred. Please try again.'
            ];
        }
    }

    /**
     * =========================================================================
     * CHECK IF ALREADY REVIEWED
     * =========================================================================
     * 
     * Simple check if user already reviewed an exchange.
     * 
     * @param int $exchangeId  Exchange ID
     * @param int $reviewerId  Reviewer user ID
     * @return bool
     */
    public function hasReviewed(int $exchangeId, int $reviewerId): bool
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM reviews
                WHERE exchange_request_id = :exchange_id AND reviewer_id = :reviewer_id
            ");

            $stmt->execute([
                ':exchange_id' => $exchangeId,
                ':reviewer_id' => $reviewerId
            ]);

            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Has Reviewed Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPDATE REVIEW
     * =========================================================================
     * 
     * Update an existing review (within 24 hours of creation).
     * 
     * @param int    $reviewId    Review ID
     * @param int    $reviewerId  Reviewer ID (for verification)
     * @param int    $rating      New rating
     * @param string $comment     New comment
     * @return bool
     */
    public function update(int $reviewId, int $reviewerId, int $rating, string $comment): bool
    {
        try {
            // Validate rating
            if ($rating < 1 || $rating > 5) {
                return false;
            }

            // Only allow update within 24 hours
            $stmt = $this->db->prepare("
                UPDATE reviews 
                SET rating = :rating, comment = :comment
                WHERE id = :id AND reviewer_id = :reviewer_id
                AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");

            return $stmt->execute([
                ':rating'     => $rating,
                ':comment'    => trim($comment),
                ':id'         => $reviewId,
                ':reviewer_id' => $reviewerId
            ]);
        } catch (PDOException $e) {
            error_log("Update Review Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE REVIEW
     * =========================================================================
     * 
     * Delete a review (only by the reviewer or admin).
     * 
     * @param int $reviewId    Review ID
     * @param int $reviewerId  Reviewer ID
     * @return bool
     */
    public function delete(int $reviewId, int $reviewerId): bool
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM reviews 
                WHERE id = :id AND reviewer_id = :reviewer_id
            ");

            return $stmt->execute([
                ':id'         => $reviewId,
                ':reviewer_id' => $reviewerId
            ]);
        } catch (PDOException $e) {
            error_log("Delete Review Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COUNT REVIEWS FOR USER
     * =========================================================================
     * 
     * Count total reviews received by a user.
     * 
     * @param int $userId  User ID
     * @return int
     */
    public function countForUser(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM reviews WHERE reviewee_id = :user_id
            ");

            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();
            return (int) $result['total'];
        } catch (PDOException $e) {
            error_log("Count Reviews Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * GET RECENT REVIEWS
     * =========================================================================
     * 
     * Get recent reviews across the platform.
     * 
     * @param int $limit  Number of reviews
     * @return array
     */
    public function getRecent(int $limit = 5): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rev.full_name as reviewer_name,
                    rev_p.profile_picture as reviewer_picture,
                    rec.full_name as reviewee_name,
                    rec_p.profile_picture as reviewee_picture
                FROM reviews r
                JOIN users rev ON r.reviewer_id = rev.id
                JOIN users rec ON r.reviewee_id = rec.id
                LEFT JOIN profiles rev_p ON rev.id = rev_p.user_id
                LEFT JOIN profiles rec_p ON rec.id = rec_p.user_id
                ORDER BY r.created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Recent Reviews Error: " . $e->getMessage());
            return [];
        }
    }
}

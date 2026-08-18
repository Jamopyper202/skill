<?php
/**
 * ============================================================================
 * Report Model
 * ============================================================================
 * 
 * Handles all database operations related to user reports.
 * Includes: create, read, update status, and manage reports.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Report {
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
     * CREATE REPORT
     * =========================================================================
     * 
     * Create a new user report.
     * 
     * @param int    $reporterId  User filing the report
     * @param int    $reportedId  User being reported
     * @param string $reason      Report reason enum
     * @param string $description Detailed description
     * @return int|false          New report ID or false
     */
    public function create(int $reporterId, int $reportedId, string $reason, string $description): int|false {
        try {
            $validReasons = ['spam', 'harassment', 'fake_profile', 'inappropriate_content', 'other'];
            if (!in_array($reason, $validReasons)) {
                $reason = 'other';
            }

            $stmt = $this->db->prepare("
                INSERT INTO reports (reporter_id, reported_id, reason, description, status, created_at, updated_at)
                VALUES (:reporter_id, :reported_id, :reason, :description, 'pending', NOW(), NOW())
            ");

            $stmt->execute([
                ':reporter_id' => $reporterId,
                ':reported_id' => $reportedId,
                ':reason'      => $reason,
                ':description' => $description
            ]);

            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Report Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET REPORT BY ID
     * =========================================================================
     * 
     * Retrieve a single report with user details.
     * 
     * @param int $reportId  Report ID
     * @return array|false
     */
    public function getById(int $reportId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rep.full_name as reporter_name,
                    rep.email as reporter_email,
                    rec.full_name as reported_name,
                    rec.email as reported_email
                FROM reports r
                JOIN users rep ON r.reporter_id = rep.id
                JOIN users rec ON r.reported_id = rec.id
                WHERE r.id = :id
                LIMIT 1
            ");

            $stmt->execute([':id' => $reportId]);
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Report Get By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET ALL REPORTS
     * =========================================================================
     * 
     * Get all reports with optional status filter.
     * 
     * @param string $status  Filter by status
     * @param int    $limit   Results limit
     * @param int    $offset  Pagination offset
     * @return array
     */
    public function getAll(string $status = '', int $limit = 10, int $offset = 0): array {
        try {
            $sql = "
                SELECT r.*,
                    rep.full_name as reporter_name,
                    rec.full_name as reported_name
                FROM reports r
                JOIN users rep ON r.reporter_id = rep.id
                JOIN users rec ON r.reported_id = rec.id
                WHERE 1=1
            ";
            $params = [];

            if (!empty($status)) {
                $sql .= " AND r.status = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Report Get All Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET REPORTS BY REPORTER
     * =========================================================================
     * 
     * Get reports filed by a specific user.
     * 
     * @param int $reporterId  Reporter user ID
     * @return array
     */
    public function getByReporter(int $reporterId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rec.full_name as reported_name
                FROM reports r
                JOIN users rec ON r.reported_id = rec.id
                WHERE r.reporter_id = :reporter_id
                ORDER BY r.created_at DESC
            ");

            $stmt->execute([':reporter_id' => $reporterId]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Report Get By Reporter Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET REPORTS AGAINST USER
     * =========================================================================
     * 
     * Get reports filed against a specific user.
     * 
     * @param int $reportedId  Reported user ID
     * @return array
     */
    public function getAgainstUser(int $reportedId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*,
                    rep.full_name as reporter_name
                FROM reports r
                JOIN users rep ON r.reporter_id = rep.id
                WHERE r.reported_id = :reported_id
                ORDER BY r.created_at DESC
            ");

            $stmt->execute([':reported_id' => $reportedId]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Report Get Against User Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * UPDATE STATUS
     * =========================================================================
     * 
     * Update report status and admin notes.
     * 
     * @param int    $reportId   Report ID
     * @param string $status     New status
     * @param string $adminNotes Admin notes
     * @return bool
     */
    public function updateStatus(int $reportId, string $status, string $adminNotes = ''): bool {
        try {
            $validStatuses = ['pending', 'reviewed', 'resolved', 'dismissed'];
            if (!in_array($status, $validStatuses)) {
                return false;
            }

            $stmt = $this->db->prepare("
                UPDATE reports 
                SET status = :status, admin_notes = :admin_notes, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute([
                ':status'      => $status,
                ':admin_notes' => $adminNotes,
                ':id'          => $reportId
            ]);

        } catch (PDOException $e) {
            error_log("Report Update Status Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE REPORT
     * =========================================================================
     * 
     * Delete a report.
     * 
     * @param int $reportId  Report ID
     * @return bool
     */
    public function delete(int $reportId): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM reports WHERE id = :id");
            return $stmt->execute([':id' => $reportId]);

        } catch (PDOException $e) {
            error_log("Report Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COUNT REPORTS
     * =========================================================================
     * 
     * Count total reports with optional status filter.
     * 
     * @param string $status  Filter by status
     * @return int
     */
    public function count(string $status = ''): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM reports WHERE 1=1";
            $params = [];

            if (!empty($status)) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Report Count Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * HAS REPORTED
     * =========================================================================
     * 
     * Check if a user has already reported another user.
     * 
     * @param int $reporterId  Reporter user ID
     * @param int $reportedId  Reported user ID
     * @return bool
     */
    public function hasReported(int $reporterId, int $reportedId): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM reports 
                WHERE reporter_id = :reporter_id AND reported_id = :reported_id
                AND status != 'dismissed'
            ");

            $stmt->execute([
                ':reporter_id' => $reporterId,
                ':reported_id' => $reportedId
            ]);

            $result = $stmt->fetch();
            return $result['total'] > 0;

        } catch (PDOException $e) {
            error_log("Report Has Reported Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
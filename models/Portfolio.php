<?php
/**
 * ============================================================================
 * Portfolio Model
 * ============================================================================
 * 
 * Handles all database operations related to user portfolio items.
 * Includes: create, read, update, delete portfolio entries.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Portfolio {
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
     * CREATE PORTFOLIO ITEM
     * =========================================================================
     * 
     * Add a new portfolio item for a user.
     * 
     * @param int    $userId      User ID
     * @param string $title       Project title
     * @param string $description Project description
     * @param string $projectUrl  External project URL
     * @param string $image       Image filename
     * @return int|false          New portfolio ID or false
     */
    public function create(int $userId, string $title, string $description = '', string $projectUrl = '', string $image = ''): int|false {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO portfolio (user_id, title, description, project_url, image, created_at, updated_at)
                VALUES (:user_id, :title, :description, :project_url, :image, NOW(), NOW())
            ");

            $stmt->execute([
                ':user_id'     => $userId,
                ':title'       => $title,
                ':description' => $description,
                ':project_url' => $projectUrl,
                ':image'       => $image
            ]);

            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Portfolio Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET PORTFOLIO ITEM BY ID
     * =========================================================================
     * 
     * Retrieve a single portfolio item.
     * 
     * @param int $portfolioId  Portfolio item ID
     * @param int $userId       User ID (for verification, 0 = skip check)
     * @return array|false
     */
    public function getById(int $portfolioId, int $userId = 0): array|false {
        try {
            $sql = "
                SELECT p.*, u.full_name as user_name
                FROM portfolio p
                JOIN users u ON p.user_id = u.id
                WHERE p.id = :id
            ";
            $params = [':id' => $portfolioId];

            if ($userId > 0) {
                $sql .= " AND p.user_id = :user_id";
                $params[':user_id'] = $userId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Portfolio Get By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET USER PORTFOLIO
     * =========================================================================
     * 
     * Get all portfolio items for a user.
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getByUserId(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM portfolio 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
            ");

            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Portfolio Get By User ID Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * UPDATE PORTFOLIO ITEM
     * =========================================================================
     * 
     * Update an existing portfolio item.
     * 
     * @param int    $portfolioId  Portfolio item ID
     * @param int    $userId       User ID (for verification)
     * @param string $title        New title
     * @param string $description  New description
     * @param string $projectUrl   New project URL
     * @param string $image        New image filename
     * @return bool
     */
    public function update(int $portfolioId, int $userId, string $title, string $description = '', string $projectUrl = '', string $image = ''): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE portfolio 
                SET title = :title,
                    description = :description,
                    project_url = :project_url,
                    image = :image,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':title'       => $title,
                ':description' => $description,
                ':project_url' => $projectUrl,
                ':image'       => $image,
                ':id'          => $portfolioId,
                ':user_id'     => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Portfolio Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * DELETE PORTFOLIO ITEM
     * =========================================================================
     * 
     * Delete a portfolio item and its image.
     * 
     * @param int $portfolioId  Portfolio item ID
     * @param int $userId       User ID (for verification)
     * @return bool
     */
    public function delete(int $portfolioId, int $userId): bool {
        try {
            // Get image filename before deleting
            $item = $this->getById($portfolioId, $userId);
            if ($item && !empty($item['image'])) {
                $this->deleteImage($item['image']);
            }

            $stmt = $this->db->prepare("
                DELETE FROM portfolio 
                WHERE id = :id AND user_id = :user_id
            ");

            return $stmt->execute([
                ':id'      => $portfolioId,
                ':user_id' => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Portfolio Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPLOAD PORTFOLIO IMAGE
     * =========================================================================
     * 
     * Handle portfolio image file upload.
     * 
     * @param array $file  $_FILES array element
     * @return string|false Filename on success, false on failure
     */
    public function uploadImage(array $file): string|false {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log("Portfolio Upload Error Code: " . $file['error']);
            return false;
        }

        // Validate file size (max 2MB)
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            error_log("Portfolio File too large: " . $file['size']);
            return false;
        }

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            error_log("Portfolio Invalid file type: " . $mimeType);
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('portfolio_') . '_' . time() . '.' . strtolower($extension);

        // Ensure upload directory exists
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        // Move uploaded file
        $destination = UPLOAD_PATH . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return false;
    }

    /**
     * =========================================================================
     * DELETE PORTFOLIO IMAGE
     * =========================================================================
     * 
     * Remove portfolio image file from server.
     * 
     * @param string $filename  Filename to delete
     * @return void
     */
    public function deleteImage(string $filename): void {
        if (!empty($filename) && file_exists(UPLOAD_PATH . '/' . $filename)) {
            unlink(UPLOAD_PATH . '/' . $filename);
        }
    }

    /**
     * =========================================================================
     * COUNT PORTFOLIO ITEMS
     * =========================================================================
     * 
     * Count total portfolio items for a user.
     * 
     * @param int $userId  User ID
     * @return int
     */
    public function count(int $userId): int {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM portfolio WHERE user_id = :user_id
            ");

            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Portfolio Count Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
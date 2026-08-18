<?php
/**
 * ============================================================================
 * Profile Model
 * ============================================================================
 * 
 * Handles all database operations related to user profiles.
 * Includes: profile CRUD, profile picture upload, education, experience.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Profile {
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
     * CREATE PROFILE
     * =========================================================================
     * 
     * Create a new profile for a user (called after registration).
     * 
     * @param int    $userId          User ID
     * @param string $bio             User bio/description
     * @param string $location        User location
     * @param string $phone           Phone number
     * @param string $experienceLevel Experience level enum
     * @param string $availability    Availability enum
     * @return int|false              Returns profile ID on success, false on failure
     */
    public function create(int $userId, string $bio = '', string $location = '', string $phone = '', string $experienceLevel = 'Beginner', string $availability = 'Flexible'): int|false {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO profiles (user_id, bio, location, phone, experience_level, availability, created_at, updated_at)
                VALUES (:user_id, :bio, :location, :phone, :experience_level, :availability, NOW(), NOW())
            ");

            $stmt->execute([
                ':user_id'          => $userId,
                ':bio'              => $bio,
                ':location'         => $location,
                ':phone'            => $phone,
                ':experience_level' => $experienceLevel,
                ':availability'     => $availability
            ]);

            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("Profile Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * GET PROFILE BY USER ID
     * =========================================================================
     * 
     * Retrieve a user's complete profile with user data.
     * 
     * @param int $userId  User ID
     * @return array|false  Profile data array or false if not found
     */
    public function getByUserId(int $userId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.full_name, u.email, u.role, u.is_active, u.created_at as user_created_at,
                    p.id as profile_id, p.bio, p.location, p.phone, p.profile_picture,
                    p.experience_level, p.availability, p.linkedin_url, p.github_url, p.website_url,
                    p.created_at, p.updated_at
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE u.id = :user_id
                LIMIT 1
            ");

            $stmt->execute([':user_id' => $userId]);

            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get Profile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPDATE PROFILE
     * =========================================================================
     * 
     * Update a user's profile information.
     * 
     * @param int    $userId          User ID
     * @param array  $data            Associative array of fields to update
     * @return bool                   True on success, false on failure
     */
    public function update(int $userId, array $data): bool {
        try {
            // Build dynamic update query
            $allowedFields = [
                'bio', 'location', 'phone', 'experience_level', 
                'availability', 'linkedin_url', 'github_url', 'website_url'
            ];

            $fields = [];
            $params = [':user_id' => $userId];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            // If no valid fields to update, return true
            if (empty($fields)) {
                return true;
            }

            $sql = "UPDATE profiles SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPDATE PROFILE PICTURE
     * =========================================================================
     * 
     * Update the user's profile picture filename.
     * 
     * @param int    $userId    User ID
     * @param string $filename  New profile picture filename
     * @return bool             True on success, false on failure
     */
    public function updateProfilePicture(int $userId, string $filename): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE profiles 
                SET profile_picture = :profile_picture, updated_at = NOW() 
                WHERE user_id = :user_id
            ");

            return $stmt->execute([
                ':profile_picture' => $filename,
                ':user_id'         => $userId
            ]);

        } catch (PDOException $e) {
            error_log("Update Profile Picture Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPLOAD PROFILE PICTURE
     * =========================================================================
     * 
     * Handle profile picture file upload with validation.
     * 
     * @param array $file  $_FILES array element
     * @return string|false Filename on success, false on failure
     */
    public function uploadProfilePicture(array $file): string|false {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log("Upload Error Code: " . $file['error']);
            return false;
        }

        // Validate file size (max 2MB)
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            error_log("File too large: " . $file['size']);
            return false;
        }

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            error_log("Invalid file type: " . $mimeType);
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '_' . time() . '.' . strtolower($extension);

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
     * DELETE OLD PROFILE PICTURE
     * =========================================================================
     * 
     * Remove old profile picture file from server.
     * 
     * @param string $filename  Filename to delete
     * @return void
     */
    public function deleteOldPicture(string $filename): void {
        if ($filename !== 'download.png' && file_exists(UPLOAD_PATH . '/' . $filename)) {
            unlink(UPLOAD_PATH . '/' . $filename);
        }
    }

    /**
     * =========================================================================
     * GET PROFILE STATS
     * =========================================================================
     * 
     * Get profile statistics for dashboard display.
     * 
     * @param int $userId  User ID
     * @return array       Statistics array
     */
   public function getStats(int $userId): array
{
    try {
        $stats = [];

        // Count skills offered
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM user_skills
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $stats['skills_offered'] = (int) $stmt->fetch()['count'];

        // Count skills wanted
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM wanted_skills
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $stats['skills_wanted'] = (int) $stmt->fetch()['count'];

        // Count matches
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM matches
            WHERE (user_id_1 = :user_id_1 OR user_id_2 = :user_id_2)
            AND status = 'accepted'
        ");
        $stmt->execute([
            ':user_id_1' => $userId,
            ':user_id_2' => $userId
        ]);
        $stats['matches'] = (int) $stmt->fetch()['count'];

        // Count unread messages
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM messages
            WHERE receiver_id = :user_id
            AND is_read = 0
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $stats['unread_messages'] = (int) $stmt->fetch()['count'];

        // Count reviews received
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM reviews
            WHERE reviewee_id = :user_id
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $stats['reviews'] = (int) $stmt->fetch()['count'];

        // Average rating
        $stmt = $this->db->prepare("
            SELECT AVG(rating) as avg_rating
            FROM reviews
            WHERE reviewee_id = :user_id
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $result = $stmt->fetch();

        $stats['avg_rating'] = !empty($result['avg_rating'])
            ? round((float) $result['avg_rating'], 1)
            : 0;

        // Count portfolio items
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM portfolio
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':user_id' => $userId
        ]);
        $stats['portfolio_items'] = (int) $stmt->fetch()['count'];

        return $stats;

    }catch (PDOException $e) {
    error_log("Get Profile Stats Error: " . $e->getMessage());
    return [];
}
}

    /**
     * =========================================================================
     * SEARCH PROFILES
     * =========================================================================
     * 
     * Search for user profiles by name, location, or skills.
     * 
     * @param string $query     Search query
     * @param int    $limit     Maximum results
     * @param int    $offset    Pagination offset
     * @return array            Array of matching profiles
     */
    public function search(string $query = '', int $limit = 10, int $offset = 0): array {
        try {
            $sql = "
                SELECT DISTINCT u.id, u.full_name, u.email, u.created_at,
                    p.bio, p.location, p.profile_picture, p.experience_level, p.availability
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                LEFT JOIN user_skills us ON u.id = us.user_id
                LEFT JOIN skills s ON us.skill_id = s.id
                WHERE u.is_active = 1 AND u.role = 'user'
            ";
            
            $params = [];

            if (!empty($query)) {
                $sql .= " AND (
                    u.full_name LIKE :query 
                    OR p.location LIKE :query 
                    OR p.bio LIKE :query
                    OR s.name LIKE :query
                )";
                $params[':query'] = '%' . $query . '%';
            }

            $sql .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Search Profiles Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * GET RECENT PROFILES
     * =========================================================================
     * 
     * Get recently joined users for display on homepage.
     * 
     * @param int $limit  Number of profiles to retrieve
     * @return array      Array of user profiles
     */
    public function getRecent(int $limit = 6): array {
        try {
            $stmt = $this->db->prepare("
                SELECT u.id, u.full_name, u.created_at,
                    p.bio, p.location, p.profile_picture, p.experience_level
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE u.is_active = 1 AND u.role = 'user'
                ORDER BY u.created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get Recent Profiles Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
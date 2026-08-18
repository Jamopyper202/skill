<?php
/**
 * ============================================================================
 * Skill Model
 * ============================================================================
 * 
 * Handles all database operations related to skills.
 * Includes: master skill list, user skills (offered), wanted skills,
 * categories, searching, and filtering.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class Skill {
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

    // =========================================================================
    // MASTER SKILLS (skills table)
    // =========================================================================

    /**
     * Get all master skills
     * 
     * @return array  All skills with category info
     */
    public function getAllMaster(): array {
        try {
            $stmt = $this->db->query("
                SELECT s.*, c.name as category_name, c.icon as category_icon
                FROM skills s
                JOIN categories c ON s.category_id = c.id
                WHERE s.is_active = 1
                ORDER BY c.name, s.name
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Master Skills Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get master skill by ID
     * 
     * @param int $id  Skill ID
     * @return array|false
     */
    public function getMasterById(int $id): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, c.name as category_name
                FROM skills s
                JOIN categories c ON s.category_id = c.id
                WHERE s.id = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Master Skill By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get skills by category
     * 
     * @param int $categoryId  Category ID
     * @return array
     */
    public function getByCategory(int $categoryId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM skills 
                WHERE category_id = :category_id AND is_active = 1
                ORDER BY name
            ");
            $stmt->execute([':category_id' => $categoryId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Skills By Category Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search master skills
     * 
     * @param string $query  Search term
     * @return array
     */
    public function searchMaster(string $query): array {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, c.name as category_name
                FROM skills s
                JOIN categories c ON s.category_id = c.id
                WHERE s.is_active = 1 AND s.name LIKE :query
                ORDER BY s.name
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Search Master Skills Error: " . $e->getMessage());
            return [];
        }
    }

    // =========================================================================
    // CATEGORIES
    // =========================================================================

    /**
     * Get all categories
     * 
     * @return array  All categories
     */
    public function getAllCategories(): array {
        try {
            $stmt = $this->db->query("
                SELECT * FROM categories 
                WHERE is_active = 1 
                ORDER BY name
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Categories Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get category by ID
     * 
     * @param int $id  Category ID
     * @return array|false
     */
    public function getCategoryById(int $id): array|false {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Category By ID Error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // USER SKILLS (OFFERED)
    // =========================================================================

    /**
     * Add a skill that user offers
     * 
     * @param int    $userId          User ID
     * @param int    $skillId         Master skill ID
     * @param string $experienceLevel Experience level
     * @param string $description     User's description of their skill
     * @param int    $yearsExperience Years of experience
     * @return int|false              New user_skill ID or false
     */
    public function addUserSkill(int $userId, int $skillId, string $experienceLevel, string $description = '', int $yearsExperience = 0): int|false {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_skills (user_id, skill_id, experience_level, description, years_of_experience, is_active, created_at, updated_at)
                VALUES (:user_id, :skill_id, :experience_level, :description, :years_of_experience, 1, NOW(), NOW())
            ");
            $stmt->execute([
                ':user_id'           => $userId,
                ':skill_id'          => $skillId,
                ':experience_level'  => $experienceLevel,
                ':description'       => $description,
                ':years_of_experience' => $yearsExperience
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Add User Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's offered skill
     * 
     * @param int    $userSkillId     User skill record ID
     * @param int    $userId          User ID (for verification)
     * @param string $experienceLevel New experience level
     * @param string $description     New description
     * @param int    $yearsExperience New years of experience
     * @return bool
     */
    public function updateUserSkill(int $userSkillId, int $userId, string $experienceLevel, string $description = '', int $yearsExperience = 0): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE user_skills 
                SET experience_level = :experience_level, 
                    description = :description, 
                    years_of_experience = :years_of_experience,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':experience_level'    => $experienceLevel,
                ':description'         => $description,
                ':years_of_experience' => $yearsExperience,
                ':id'                  => $userSkillId,
                ':user_id'             => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Update User Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user's offered skill
     * 
     * @param int $userSkillId  User skill record ID
     * @param int $userId       User ID (for verification)
     * @return bool
     */
    public function deleteUserSkill(int $userSkillId, int $userId): bool {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM user_skills 
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id'      => $userSkillId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Delete User Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all skills offered by a user
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getUserSkills(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT us.*, s.name as skill_name, s.description as skill_description,s.category_id AS category_id,
                       c.name as category_name, c.icon as category_icon
                FROM user_skills us
                JOIN skills s ON us.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE us.user_id = :user_id AND us.is_active = 1
                ORDER BY c.name, s.name
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get User Skills Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single user skill by ID
     * 
     * @param int $userSkillId  User skill record ID
     * @param int $userId       User ID (for verification)
     * @return array|false
     */
    public function getUserSkillById(int $userSkillId, int $userId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT us.*, s.name as skill_name, c.name as category_name
                FROM user_skills us
                JOIN skills s ON us.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE us.id = :id AND us.user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute([
                ':id'      => $userSkillId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get User Skill By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user already has this skill
     * 
     * @param int $userId   User ID
     * @param int $skillId  Skill ID
     * @return bool
     */
    public function userHasSkill(int $userId, int $skillId): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM user_skills 
                WHERE user_id = :user_id AND skill_id = :skill_id
            ");
            $stmt->execute([
                ':user_id'  => $userId,
                ':skill_id' => $skillId
            ]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("User Has Skill Error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // WANTED SKILLS
    // =========================================================================

    /**
     * Add a skill that user wants to learn
     * 
     * @param int    $userId          User ID
     * @param int    $skillId         Master skill ID
     * @param string $experienceLevel Desired experience level
     * @param string $description     Why user wants to learn this
     * @param string $urgency         Urgency level
     * @return int|false
     */
    public function addWantedSkill(int $userId, int $skillId, string $experienceLevel, string $description = '', string $urgency = 'Medium'): int|false {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO wanted_skills (user_id, skill_id, experience_level, description, urgency, is_active, created_at, updated_at)
                VALUES (:user_id, :skill_id, :experience_level, :description, :urgency, 1, NOW(), NOW())
            ");
            $stmt->execute([
                ':user_id'          => $userId,
                ':skill_id'         => $skillId,
                ':experience_level' => $experienceLevel,
                ':description'      => $description,
                ':urgency'          => $urgency
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Add Wanted Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's wanted skill
     * 
     * @param int    $wantedSkillId   Wanted skill record ID
     * @param int    $userId          User ID
     * @param string $experienceLevel New experience level
     * @param string $description     New description
     * @param string $urgency         New urgency
     * @return bool
     */
    public function updateWantedSkill(int $wantedSkillId, int $userId, string $experienceLevel, string $description = '', string $urgency = 'Medium'): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE wanted_skills 
                SET experience_level = :experience_level, 
                    description = :description, 
                    urgency = :urgency,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':experience_level' => $experienceLevel,
                ':description'      => $description,
                ':urgency'          => $urgency,
                ':id'               => $wantedSkillId,
                ':user_id'          => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Update Wanted Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user's wanted skill
     * 
     * @param int $wantedSkillId  Wanted skill record ID
     * @param int $userId         User ID
     * @return bool
     */
    public function deleteWantedSkill(int $wantedSkillId, int $userId): bool {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM wanted_skills 
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id'      => $wantedSkillId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Delete Wanted Skill Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all skills wanted by a user
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getWantedSkills(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT ws.*, s.name as skill_name, s.description as skill_description,s.category_id AS category_id,
                       c.name as category_name, c.icon as category_icon
                FROM wanted_skills ws
                JOIN skills s ON ws.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE ws.user_id = :user_id AND ws.is_active = 1
                ORDER BY ws.urgency DESC, c.name, s.name
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Wanted Skills Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single wanted skill by ID
     * 
     * @param int $wantedSkillId  Wanted skill record ID
     * @param int $userId         User ID
     * @return array|false
     */
    public function getWantedSkillById(int $wantedSkillId, int $userId): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT ws.*, s.name as skill_name, c.name as category_name
                FROM wanted_skills ws
                JOIN skills s ON ws.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE ws.id = :id AND ws.user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute([
                ':id'      => $wantedSkillId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Wanted Skill By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user already wants this skill
     * 
     * @param int $userId   User ID
     * @param int $skillId  Skill ID
     * @return bool
     */
    public function userWantsSkill(int $userId, int $skillId): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM wanted_skills 
                WHERE user_id = :user_id AND skill_id = :skill_id
            ");
            $stmt->execute([
                ':user_id'  => $userId,
                ':skill_id' => $skillId
            ]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("User Wants Skill Error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // BROWSE / SEARCH SKILLS
    // =========================================================================

    /**
     * Browse all offered skills with filters
     * 
     * @param int    $categoryId  Filter by category (0 = all)
     * @param string $search      Search term
     * @param string $experience  Filter by experience level
     * @param int    $limit       Results per page
     * @param int    $offset      Pagination offset
     * @return array
     */
    public function browseOffered(int $categoryId = 0, string $search = '', string $experience = '', int $limit = 10, int $offset = 0): array {
        try {
            $sql = "
                SELECT us.*, u.full_name, u.email, u.created_at as user_joined,
                       s.name as skill_name, s.description as skill_description,
                       c.name as category_name, c.icon as category_icon,
                       p.profile_picture, p.location, p.experience_level as user_experience
                FROM user_skills us
                JOIN users u ON us.user_id = u.id
                JOIN skills s ON us.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE us.is_active = 1 AND u.is_active = 1
            ";
            
            $params = [];

            if ($categoryId > 0) {
                $sql .= " AND c.id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            if (!empty($experience)) {
                $sql .= " AND us.experience_level = :experience";
                $params[':experience'] = $experience;
            }

            if (!empty($search)) {
                $sql .= " AND (s.name LIKE :search OR us.description LIKE :search OR u.full_name LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            $sql .= " ORDER BY us.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Browse Offered Skills Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total offered skills for pagination
     * 
     * @param int    $categoryId  Filter by category
     * @param string $search      Search term
     * @param string $experience  Filter by experience
     * @return int
     */
    public function countOffered(int $categoryId = 0, string $search = '', string $experience = ''): int {
        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM user_skills us
                JOIN users u ON us.user_id = u.id
                JOIN skills s ON us.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE us.is_active = 1 AND u.is_active = 1
            ";
            
            $params = [];

            if ($categoryId > 0) {
                $sql .= " AND c.id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            if (!empty($experience)) {
                $sql .= " AND us.experience_level = :experience";
                $params[':experience'] = $experience;
            }

            if (!empty($search)) {
                $sql .= " AND (s.name LIKE :search OR us.description LIKE :search OR u.full_name LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Count Offered Skills Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get skills offered by a specific user (public view)
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getUserSkillsPublic(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT us.*, s.name as skill_name, s.description as skill_description,
                       c.name as category_name, c.icon as category_icon
                FROM user_skills us
                JOIN skills s ON us.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE us.user_id = :user_id AND us.is_active = 1
                ORDER BY c.name, s.name
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get User Skills Public Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get skills wanted by a specific user (public view)
     * 
     * @param int $userId  User ID
     * @return array
     */
    public function getWantedSkillsPublic(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT ws.*, s.name as skill_name, s.description as skill_description,s.category_id AS category_id,
                       c.name as category_name, c.icon as category_icon
                FROM wanted_skills ws
                JOIN skills s ON ws.skill_id = s.id
                JOIN categories c ON s.category_id = c.id
                WHERE ws.user_id = :user_id AND ws.is_active = 1
                ORDER BY ws.urgency DESC, c.name, s.name
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Wanted Skills Public Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
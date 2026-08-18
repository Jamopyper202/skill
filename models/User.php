<?php
/**
 * ============================================================================
 * User Model
 * ============================================================================
 * 
 * Handles all database operations related to users.
 * Includes: registration, login, profile management, password reset.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class User {
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
     * CREATE USER (Registration)
     * =========================================================================
     * 
     * Register a new user with hashed password.
     * 
     * @param string $fullName     User's full name
     * @param string $email        User's email address
     * @param string $password     Plain text password (will be hashed)
     * @param string $role         User role (default: 'user')
     * @return int|false           Returns new user ID on success, false on failure
     */
    public function create(string $fullName, string $email, string $password, string $role = 'user'): int|false {
        try {
            // Hash the password using bcrypt algorithm
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Prepare insert statement
            $stmt = $this->db->prepare("
                INSERT INTO users (full_name, email, password, role, is_active, is_verified, created_at)
                VALUES (:full_name, :email, :password, :role, 1, 1, NOW())
            ");

            // Execute with bound parameters
            $stmt->execute([
                ':full_name' => $fullName,
                ':email'     => $email,
                ':password'  => $hashedPassword,
                ':role'      => $role
            ]);

            // Return the ID of the newly created user
            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            // Log error and return false
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * FIND USER BY EMAIL
     * =========================================================================
     * 
     * Retrieve a user by their email address.
     * Used for login and checking if email already exists.
     * 
     * @param string $email  Email address to search for
     * @return array|false   User data array or false if not found
     */
    public function findByEmail(string $email): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, p.profile_picture, p.experience_level, p.bio, p.location
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE u.email = :email
                LIMIT 1
            ");

            $stmt->execute([':email' => $email]);

            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Find By Email Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * FIND USER BY ID
     * =========================================================================
     * 
     * Retrieve a user by their ID.
     * 
     * @param int $id  User ID
     * @return array|false  User data array or false if not found
     */
    public function findById(int $id): array|false {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, p.profile_picture, p.experience_level, p.bio, p.location, p.phone
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE u.id = :id
                LIMIT 1
            ");

            $stmt->execute([':id' => $id]);

            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Find By ID Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * VERIFY PASSWORD
     * =========================================================================
     * 
     * Verify a plain text password against a hashed password.
     * Uses PHP's password_verify() function.
     * 
     * @param string $plainPassword     The password entered by user
     * @param string $hashedPassword    The stored hashed password
     * @return bool                     True if password matches, false otherwise
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * =========================================================================
     * UPDATE LAST LOGIN
     * =========================================================================
     * 
     * Update the last_login timestamp when user logs in.
     * 
     * @param int $id  User ID
     * @return bool    True on success, false on failure
     */
    public function updateLastLogin(int $id): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET last_login = NOW() 
                WHERE id = :id
            ");

            return $stmt->execute([':id' => $id]);

        } catch (PDOException $e) {
            error_log("Update Last Login Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * UPDATE PASSWORD
     * =========================================================================
     * 
     * Update a user's password (used in password reset).
     * 
     * @param int    $id          User ID
     * @param string $newPassword New plain text password (will be hashed)
     * @return bool               True on success, false on failure
     */
    public function updatePassword(int $id, string $newPassword): bool {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("
                UPDATE users 
                SET password = :password 
                WHERE id = :id
            ");

            return $stmt->execute([
                ':password' => $hashedPassword,
                ':id'       => $id
            ]);

        } catch (PDOException $e) {
            error_log("Update Password Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * CHECK IF EMAIL EXISTS
     * =========================================================================
     * 
     * Check if an email address is already registered.
     * Used during registration to prevent duplicate accounts.
     * 
     * @param string $email  Email address to check
     * @return bool          True if email exists, false otherwise
     */
    public function emailExists(string $email): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE email = :email
            ");

            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch();

            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Email Exists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * CREATE PASSWORD RESET TOKEN
     * =========================================================================
     * 
     * Generate a unique token for password reset.
     * In a simple implementation, we store the token in the session.
     * 
     * @param int $id  User ID
     * @return string  The generated reset token
     */
    public function createResetToken(int $id): string {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        
        // Store token in session with expiration (1 hour)
        $_SESSION['password_reset'] = [
            'user_id'    => $id,
            'token'      => $token,
            'expires_at' => time() + 3600  // 1 hour expiration
        ];

        return $token;
    }

    /**
     * =========================================================================
     * VERIFY RESET TOKEN
     * =========================================================================
     * 
     * Verify if a password reset token is valid.
     * 
     * @param string $token  The reset token to verify
     * @return int|false     User ID if valid, false if invalid or expired
     */
    public function verifyResetToken(string $token): int|false {
        // Check if reset session exists
        if (!isset($_SESSION['password_reset'])) {
            return false;
        }

        $resetData = $_SESSION['password_reset'];

        // Check if token matches and is not expired
        if ($resetData['token'] === $token && $resetData['expires_at'] > time()) {
            return $resetData['user_id'];
        }

        // Token is invalid or expired - clear it
        unset($_SESSION['password_reset']);
        return false;
    }

    /**
     * =========================================================================
     * CLEAR RESET TOKEN
     * =========================================================================
     * 
     * Remove the password reset token from session after successful reset.
     * 
     * @return void
     */
    public function clearResetToken(): void {
        unset($_SESSION['password_reset']);
    }

    /**
     * =========================================================================
     * GET ALL USERS (for admin)
     * =========================================================================
     * 
     * Retrieve all users with optional filtering.
     * 
     * @param string $search Optional search term
     * @param string $role   Optional role filter
     * @return array         Array of user records
     */
    public function getAll(string $search = '', string $role = ''): array {
        try {
            $sql = "
                SELECT u.*, p.profile_picture, p.experience_level
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE 1=1
            ";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            if (!empty($role)) {
                $sql .= " AND u.role = :role";
                $params[':role'] = $role;
            }

            $sql .= " ORDER BY u.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get All Users Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * =========================================================================
     * UPDATE USER STATUS
     * =========================================================================
     * 
     * Activate or deactivate a user account.
     * 
     * @param int  $id       User ID
     * @param bool $isActive Active status (1 = active, 0 = inactive)
     * @return bool          True on success, false on failure
     */
    public function updateStatus(int $id, bool $isActive): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET is_active = :is_active 
                WHERE id = :id
            ");

            return $stmt->execute([
                ':is_active' => $isActive ? 1 : 0,
                ':id'        => $id
            ]);

        } catch (PDOException $e) {
            error_log("Update Status Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =========================================================================
     * COUNT USERS
     * =========================================================================
     * 
     * Get total count of users.
     * 
     * @return int  Total number of users
     */
    public function count(): int {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch();
            return (int) $result['total'];

        } catch (PDOException $e) {
            error_log("Count Users Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
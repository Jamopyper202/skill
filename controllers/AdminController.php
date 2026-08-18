<?php
/**
 * ============================================================================
 * Admin Controller
 * ============================================================================
 * 
 * Handles all admin panel functionality.
 * Includes: dashboard, user management, skill management, category management,
 * review management, report management, notifications, and settings.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class AdminController {
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
     * ADMIN DASHBOARD
     * =========================================================================
     * 
     * Main admin dashboard with statistics and overview.
     */
    public function index(): void {
        // Get statistics
        $stats = $this->getDashboardStats();

        // Get recent users
        $recentUsers = $this->getRecentUsers(5);

        // Get recent exchanges
        $recentExchanges = $this->getRecentExchanges(5);

        // Get pending reports
        $pendingReports = $this->getPendingReports(5);

        // Load the admin dashboard view
        require_once BASE_PATH . '/views/admin/dashboard.php';
    }

    /**
     * =========================================================================
     * MANAGE USERS
     * =========================================================================
     * 
     * List all users with search and filter options.
     */
    public function users(): void {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT u.*, p.profile_picture, p.experience_level, p.location FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE 1=1";
        $countSql = "SELECT COUNT(*) as total FROM users u WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search)";
            $countSql .= " AND (u.full_name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($role)) {
            $sql .= " AND u.role = :role";
            $countSql .= " AND u.role = :role";
            $params[':role'] = $role;
        }

        if ($status !== '') {
            $sql .= " AND u.is_active = :status";
            $countSql .= " AND u.is_active = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

        // Get users
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Get total count
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = (int) $stmt->fetch()['total'];
        $totalPages = ceil($total / $limit);

        require_once BASE_PATH . '/views/admin/users.php';
    }

    /**
     * =========================================================================
     * VIEW USER
     * =========================================================================
     * 
     * View detailed information about a specific user.
     */
    public function viewUser(): void {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id === 0) {
            flash('Invalid user ID.', 'danger');
            redirect('index.php?controller=Admin&action=users');
            return;
        }

        // Get user details
        $stmt = $this->db->prepare("
            SELECT u.*, p.* 
            FROM users u 
            LEFT JOIN profiles p ON u.id = p.user_id 
            WHERE u.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            flash('User not found.', 'danger');
            redirect('index.php?controller=Admin&action=users');
            return;
        }

        // Get user skills
        $stmt = $this->db->prepare("
            SELECT us.*, s.name as skill_name, c.name as category_name
            FROM user_skills us
            JOIN skills s ON us.skill_id = s.id
            JOIN categories c ON s.category_id = c.id
            WHERE us.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $id]);
        $skills = $stmt->fetchAll();

        // Get wanted skills
        $stmt = $this->db->prepare("
            SELECT ws.*, s.name as skill_name, c.name as category_name
            FROM wanted_skills ws
            JOIN skills s ON ws.skill_id = s.id
            JOIN categories c ON s.category_id = c.id
            WHERE ws.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $id]);
        $wantedSkills = $stmt->fetchAll();

        // Get exchanges
        $stmt = $this->db->prepare("
            SELECT er.*, 
                req.full_name as requester_name,
                rec.full_name as receiver_name,
                os.name as offered_skill,
                rs.name as requested_skill
            FROM exchange_requests er
            JOIN users req ON er.requester_id = req.id
            JOIN users rec ON er.receiver_id = rec.id
            JOIN skills os ON er.offered_skill_id = os.id
            JOIN skills rs ON er.requested_skill_id = rs.id
            WHERE er.requester_id = :user_id OR er.receiver_id = :user_id
            ORDER BY er.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([':user_id' => $id]);
        $exchanges = $stmt->fetchAll();

        // Get reviews
        $stmt = $this->db->prepare("
            SELECT r.*, rev.full_name as reviewer_name
            FROM reviews r
            JOIN users rev ON r.reviewer_id = rev.id
            WHERE r.reviewee_id = :user_id
            ORDER BY r.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([':user_id' => $id]);
        $reviews = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/view-user.php';
    }

    /**
     * =========================================================================
     * TOGGLE USER STATUS
     * =========================================================================
     * 
     * Activate or deactivate a user account.
     */
    public function toggleUser(): void {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id === 0 || $id === $_SESSION['user_id']) {
            flash('Invalid operation.', 'danger');
            redirect('index.php?controller=Admin&action=users');
            return;
        }

        // Get current status
        $stmt = $this->db->prepare("SELECT is_active FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            flash('User not found.', 'danger');
            redirect('index.php?controller=Admin&action=users');
            return;
        }

        // Toggle status
        $newStatus = $user['is_active'] ? 0 : 1;
        $stmt = $this->db->prepare("UPDATE users SET is_active = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $id]);

        $statusText = $newStatus ? 'activated' : 'deactivated';
        flash("User has been {$statusText} successfully.", 'success');
        redirect('index.php?controller=Admin&action=users');
    }

    /**
     * =========================================================================
     * MANAGE SKILLS
     * =========================================================================
     * 
     * List all master skills with management options.
     */
    public function skills(): void {
        $search = $_GET['search'] ?? '';
        $categoryId = (int) ($_GET['category'] ?? 0);

        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Build query
        $sql = "SELECT s.*, c.name as category_name FROM skills s JOIN categories c ON s.category_id = c.id WHERE 1=1";
        $countSql = "SELECT COUNT(*) as total FROM skills s WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND s.name LIKE :search";
            $countSql .= " AND s.name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $sql .= " AND s.category_id = :category_id";
            $countSql .= " AND s.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $sql .= " ORDER BY c.name, s.name LIMIT :limit OFFSET :offset";

        // Get skills
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $skills = $stmt->fetchAll();

        // Get total count
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = (int) $stmt->fetch()['total'];
        $totalPages = ceil($total / $limit);

        // Get categories for filter
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
        $categories = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/skills.php';
    }

    /**
     * =========================================================================
     * ADD SKILL
     * =========================================================================
     * 
     * Add a new master skill to the system.
     */
    public function addSkill(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || $categoryId === 0) {
                flash('Skill name and category are required.', 'danger');
                redirect('index.php?controller=Admin&action=addSkill');
                return;
            }

            // Check if skill already exists in this category
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM skills WHERE name = :name AND category_id = :category_id");
            $stmt->execute([':name' => $name, ':category_id' => $categoryId]);
            if ($stmt->fetch()['count'] > 0) {
                flash('This skill already exists in the selected category.', 'warning');
                redirect('index.php?controller=Admin&action=addSkill');
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO skills (category_id, name, description, is_active, created_at) VALUES (:category_id, :name, :description, 1, NOW())");
            $stmt->execute([
                ':category_id' => $categoryId,
                ':name' => $name,
                ':description' => $description
            ]);

            flash('Skill added successfully.', 'success');
            redirect('index.php?controller=Admin&action=skills');
            return;
        }

        // Get categories for dropdown
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
        $categories = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/add-skill.php';
    }

    /**
     * =========================================================================
     * EDIT SKILL
     * =========================================================================
     * 
     * Edit an existing master skill.
     */
    public function editSkill(): void {
        $id = (int) ($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (empty($name) || $categoryId === 0) {
                flash('Skill name and category are required.', 'danger');
                redirect('index.php?controller=Admin&action=editSkill&id=' . $id);
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE skills 
                SET category_id = :category_id, name = :name, description = :description, is_active = :is_active
                WHERE id = :id
            ");
            $stmt->execute([
                ':category_id' => $categoryId,
                ':name' => $name,
                ':description' => $description,
                ':is_active' => $isActive,
                ':id' => $id
            ]);

            flash('Skill updated successfully.', 'success');
            redirect('index.php?controller=Admin&action=skills');
            return;
        }

        // Get skill
        $stmt = $this->db->prepare("SELECT * FROM skills WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $skill = $stmt->fetch();

        if (!$skill) {
            flash('Skill not found.', 'danger');
            redirect('index.php?controller=Admin&action=skills');
            return;
        }

        // Get categories
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
        $categories = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/edit-skill.php';
    }

    /**
     * =========================================================================
     * DELETE SKILL
     * =========================================================================
     * 
     * Delete a master skill (soft delete by deactivating).
     */
    public function deleteSkill(): void {
        $id = (int) ($_GET['id'] ?? 0);

        // Soft delete - just deactivate
        $stmt = $this->db->prepare("UPDATE skills SET is_active = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        flash('Skill has been deactivated.', 'success');
        redirect('index.php?controller=Admin&action=skills');
    }

    /**
     * =========================================================================
     * MANAGE CATEGORIES
     * =========================================================================
     * 
     * List all categories with management options.
     */
    public function categories(): void {
        $stmt = $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM skills WHERE category_id = c.id) as skill_count FROM categories c ORDER BY c.name");
        $categories = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/categories.php';
    }

    /**
     * =========================================================================
     * ADD CATEGORY
     * =========================================================================
     */
    public function addCategory(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'bi-grid');

            if (empty($name)) {
                flash('Category name is required.', 'danger');
                redirect('index.php?controller=Admin&action=addCategory');
                return;
            }

            // Check if exists
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM categories WHERE name = :name");
            $stmt->execute([':name' => $name]);
            if ($stmt->fetch()['count'] > 0) {
                flash('Category already exists.', 'warning');
                redirect('index.php?controller=Admin&action=addCategory');
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO categories (name, description, icon, is_active) VALUES (:name, :description, :icon, 1)");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':icon' => $icon
            ]);

            flash('Category added successfully.', 'success');
            redirect('index.php?controller=Admin&action=categories');
            return;
        }

        require_once BASE_PATH . '/views/admin/add-category.php';
    }

    /**
     * =========================================================================
     * EDIT CATEGORY
     * =========================================================================
     */
    public function editCategory(): void {
        $id = (int) ($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'bi-grid');
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            $stmt = $this->db->prepare("
                UPDATE categories 
                SET name = :name, description = :description, icon = :icon, is_active = :is_active
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':icon' => $icon,
                ':is_active' => $isActive,
                ':id' => $id
            ]);

            flash('Category updated successfully.', 'success');
            redirect('index.php?controller=Admin&action=categories');
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch();

        if (!$category) {
            flash('Category not found.', 'danger');
            redirect('index.php?controller=Admin&action=categories');
            return;
        }

        require_once BASE_PATH . '/views/admin/edit-category.php';
    }

    /**
     * =========================================================================
     * DELETE CATEGORY
     * =========================================================================
     */
    public function deleteCategory(): void {
        $id = (int) ($_GET['id'] ?? 0);

        // Check if category has skills
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM skills WHERE category_id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetch()['count'] > 0) {
            flash('Cannot delete category with existing skills.', 'warning');
            redirect('index.php?controller=Admin&action=categories');
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);

        flash('Category deleted successfully.', 'success');
        redirect('index.php?controller=Admin&action=categories');
    }

    /**
     * =========================================================================
     * MANAGE REVIEWS
     * =========================================================================
     * 
     * List all reviews with management options.
     */
    public function reviews(): void {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT r.*, 
                rev.full_name as reviewer_name,
                rec.full_name as reviewee_name,
                er.status as exchange_status
            FROM reviews r
            JOIN users rev ON r.reviewer_id = rev.id
            JOIN users rec ON r.reviewee_id = rec.id
            JOIN exchange_requests er ON r.exchange_request_id = er.id
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $reviews = $stmt->fetchAll();

        // Get total
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM reviews");
        $total = (int) $stmt->fetch()['total'];
        $totalPages = ceil($total / $limit);

        require_once BASE_PATH . '/views/admin/reviews.php';
    }

    /**
     * =========================================================================
     * DELETE REVIEW
     * =========================================================================
     */
    public function deleteReview(): void {
        $id = (int) ($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $id]);

        flash('Review deleted successfully.', 'success');
        redirect('index.php?controller=Admin&action=reviews');
    }

    /**
     * =========================================================================
     * MANAGE REPORTS
     * =========================================================================
     * 
     * List all user reports with management options.
     */
    public function reports(): void {
        $status = $_GET['status'] ?? '';

        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

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
        $reports = $stmt->fetchAll();

        // Get total
        $countSql = "SELECT COUNT(*) as total FROM reports WHERE 1=1";
        if (!empty($status)) {
            $countSql .= " AND status = :status";
        }
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = (int) $stmt->fetch()['total'];
        $totalPages = ceil($total / $limit);

        require_once BASE_PATH . '/views/admin/reports.php';
    }

    /**
     * =========================================================================
     * VIEW REPORT
     * =========================================================================
     */
    public function viewReport(): void {
        $id = (int) ($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT r.*, 
                rep.full_name as reporter_name,
                rec.full_name as reported_name
            FROM reports r
            JOIN users rep ON r.reporter_id = rep.id
            JOIN users rec ON r.reported_id = rec.id
            WHERE r.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $report = $stmt->fetch();

        if (!$report) {
            flash('Report not found.', 'danger');
            redirect('index.php?controller=Admin&action=reports');
            return;
        }

        require_once BASE_PATH . '/views/admin/view-report.php';
    }

    /**
     * =========================================================================
     * UPDATE REPORT STATUS
     * =========================================================================
     */
    public function updateReport(): void {
        $id = (int) ($_GET['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $adminNotes = trim($_POST['admin_notes'] ?? '');

        if (!in_array($status, ['pending', 'reviewed', 'resolved', 'dismissed'])) {
            flash('Invalid status.', 'danger');
            redirect('index.php?controller=Admin&action=reports');
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE reports 
            SET status = :status, admin_notes = :admin_notes, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':status' => $status,
            ':admin_notes' => $adminNotes,
            ':id' => $id
        ]);

        flash('Report status updated successfully.', 'success');
        redirect('index.php?controller=Admin&action=reports');
    }

    /**
     * =========================================================================
     * MANAGE NOTIFICATIONS
     * =========================================================================
     * 
     * Send bulk notifications to users.
     */
    public function notifications(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $userType = $_POST['user_type'] ?? 'all'; // all, active, inactive

            if (empty($title) || empty($message)) {
                flash('Title and message are required.', 'danger');
                redirect('index.php?controller=Admin&action=notifications');
                return;
            }

            // Get target users
            if ($userType === 'all') {
                $stmt = $this->db->query("SELECT id FROM users WHERE role = 'user'");
            } else {
                $isActive = $userType === 'active' ? 1 : 0;
                $stmt = $this->db->prepare("SELECT id FROM users WHERE role = 'user' AND is_active = :is_active");
                $stmt->execute([':is_active' => $isActive]);
            }

            $users = $stmt->fetchAll();
            $count = 0;

            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, type, reference_id, title, message, is_read, created_at)
                VALUES (:user_id, 'system', 0, :title, :message, 0, NOW())
            ");

            foreach ($users as $user) {
                $notifStmt->execute([
                    ':user_id' => $user['id'],
                    ':title' => $title,
                    ':message' => $message
                ]);
                $count++;
            }

            flash("Notification sent to {$count} users.", 'success');
            redirect('index.php?controller=Admin&action=notifications');
            return;
        }

        // Get notification stats
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread
            FROM notifications
        ");
        $stats = $stmt->fetch();

        require_once BASE_PATH . '/views/admin/notifications.php';
    }

    /**
     * =========================================================================
     * SETTINGS
     * =========================================================================
     * 
     * Manage application settings.
     */
    public function settings(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'site_name' => trim($_POST['site_name'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
                'items_per_page' => (int) ($_POST['items_per_page'] ?? 10),
                'match_threshold' => (int) ($_POST['match_threshold'] ?? 30),
                'enable_registration' => isset($_POST['enable_registration']) ? '1' : '0',
                'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0'
            ];

            $stmt = $this->db->prepare("
                UPDATE settings 
                SET setting_value = :value, updated_at = NOW()
                WHERE setting_key = :key
            ");

            foreach ($settings as $key => $value) {
                $stmt->execute([
                    ':value' => $value,
                    ':key' => $key
                ]);
            }

            flash('Settings updated successfully.', 'success');
            redirect('index.php?controller=Admin&action=settings');
            return;
        }

        // Get all settings
        $stmt = $this->db->query("SELECT * FROM settings");
        $settingsList = $stmt->fetchAll();
        $settings = [];
        foreach ($settingsList as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }

        require_once BASE_PATH . '/views/admin/settings.php';
    }

    /**
     * =========================================================================
     * HELPER METHODS
     * =========================================================================
     */

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array {
        $stats = [];

        // Total users
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
        $stats['total_users'] = (int) $stmt->fetch()['total'];

        // Active users
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user' AND is_active = 1");
        $stats['active_users'] = (int) $stmt->fetch()['total'];

        // Total skills
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM skills WHERE is_active = 1");
        $stats['total_skills'] = (int) $stmt->fetch()['total'];

        // Total categories
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM categories WHERE is_active = 1");
        $stats['total_categories'] = (int) $stmt->fetch()['total'];

        // Total exchanges
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM exchange_requests");
        $stats['total_exchanges'] = (int) $stmt->fetch()['total'];

        // Completed exchanges
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM exchange_requests WHERE status = 'completed'");
        $stats['completed_exchanges'] = (int) $stmt->fetch()['total'];

        // Pending exchanges
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM exchange_requests WHERE status = 'pending'");
        $stats['pending_exchanges'] = (int) $stmt->fetch()['total'];

        // Total reviews
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM reviews");
        $stats['total_reviews'] = (int) $stmt->fetch()['total'];

        // Average rating
        $stmt = $this->db->query("SELECT AVG(rating) as avg FROM reviews");
        $result = $stmt->fetch();
        $stats['avg_rating'] = $result['avg'] ? round((float) $result['avg'], 1) : 0;

        // Total matches
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM matches");
        $stats['total_matches'] = (int) $stmt->fetch()['total'];

        // Pending reports
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM reports WHERE status = 'pending'");
        $stats['pending_reports'] = (int) $stmt->fetch()['total'];

        // New users today
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
        $stats['new_users_today'] = (int) $stmt->fetch()['total'];

        return $stats;
    }

    /**
     * Get recent users
     */
    private function getRecentUsers(int $limit): array {
        $stmt = $this->db->prepare("
            SELECT u.*, p.profile_picture 
            FROM users u 
            LEFT JOIN profiles p ON u.id = p.user_id 
            WHERE u.role = 'user'
            ORDER BY u.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get recent exchanges
     */
    private function getRecentExchanges(int $limit): array {
        $stmt = $this->db->prepare("
            SELECT er.*, 
                req.full_name as requester_name,
                rec.full_name as receiver_name,
                os.name as offered_skill,
                rs.name as requested_skill
            FROM exchange_requests er
            JOIN users req ON er.requester_id = req.id
            JOIN users rec ON er.receiver_id = rec.id
            JOIN skills os ON er.offered_skill_id = os.id
            JOIN skills rs ON er.requested_skill_id = rs.id
            ORDER BY er.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get pending reports
     */
    private function getPendingReports(int $limit): array {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                rep.full_name as reporter_name,
                rec.full_name as reported_name
            FROM reports r
            JOIN users rep ON r.reporter_id = rep.id
            JOIN users rec ON r.reported_id = rec.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
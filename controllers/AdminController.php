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

class AdminController
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
     * ADMIN DASHBOARD
     * =========================================================================
     * 
     * Main admin dashboard with statistics and overview.
     */
    public function index(): void
    {
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
    public function users(): void
    {
        $search = trim($_GET['search'] ?? '');
        $role   = trim($_GET['role'] ?? '');
        $status = $_GET['status'] ?? '';

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $limit = (int) ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;


        // =========================================================
        // MAIN QUERY
        // =========================================================

        $sql = "
        SELECT
            u.*,
            p.profile_picture,
            p.experience_level,
            p.location
        FROM users u
        LEFT JOIN profiles p
            ON u.id = p.user_id
        WHERE 1 = 1
    ";

        $queryParams = [];


        // =========================================================
        // SEARCH
        // =========================================================

        if ($search !== '') {

            $sql .= "
            AND (
                u.full_name LIKE :user_search
                OR u.email LIKE :user_search_email
            )
        ";

            $queryParams[':user_search'] =
                '%' . $search . '%';

            $queryParams[':user_search_email'] =
                '%' . $search . '%';
        }


        // =========================================================
        // ROLE
        // =========================================================

        if ($role !== '') {

            $sql .= "
            AND u.role = :user_role
        ";

            $queryParams[':user_role'] = $role;
        }


        // =========================================================
        // STATUS
        // =========================================================

        if ($status !== '') {

            $sql .= "
            AND u.is_active = :user_status
        ";

            $queryParams[':user_status'] = $status;
        }


        // =========================================================
        // ORDER + PAGINATION
        // =========================================================

        $sql .= "
        ORDER BY u.created_at ASC, u.id ASC
        LIMIT :user_limit
        OFFSET :user_offset
    ";


        // =========================================================
        // EXECUTE MAIN QUERY
        // =========================================================

        $stmt = $this->db->prepare($sql);


        foreach ($queryParams as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        $stmt->bindValue(
            ':user_limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':user_offset',
            $offset,
            PDO::PARAM_INT
        );


        $stmt->execute();

        $users = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // COUNT QUERY
        // =========================================================

        $countSql = "
        SELECT COUNT(*) AS total
        FROM users u
        WHERE 1 = 1
    ";

        $countParams = [];


        // =========================================================
        // COUNT SEARCH
        // =========================================================

        if ($search !== '') {

            $countSql .= "
            AND (
                u.full_name LIKE :count_search
                OR u.email LIKE :count_search_email
            )
        ";

            $countParams[':count_search'] =
                '%' . $search . '%';

            $countParams[':count_search_email'] =
                '%' . $search . '%';
        }


        // =========================================================
        // COUNT ROLE
        // =========================================================

        if ($role !== '') {

            $countSql .= "
            AND u.role = :count_role
        ";

            $countParams[':count_role'] = $role;
        }


        // =========================================================
        // COUNT STATUS
        // =========================================================

        if ($status !== '') {

            $countSql .= "
            AND u.is_active = :count_status
        ";

            $countParams[':count_status'] = $status;
        }


        // =========================================================
        // EXECUTE COUNT QUERY
        // =========================================================

        $stmt = $this->db->prepare($countSql);


        foreach ($countParams as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = (int) ($row['total'] ?? 0);

        $totalUsers = $total;

        $totalPages = $limit > 0
            ? (int) ceil($total / $limit)
            : 1;

        $currentPage = (int)$page;

        // =========================================================
        // VIEW
        // =========================================================
        $viewData = [
            'users'       => $users,
            'page'        => $page,
            'limit'       => $limit,
            'offset'      => $offset,
            'totalUsers'  => $totalUsers,
            'totalPages'  => $totalPages,
            'currentPage' => $currentPage,
        ];

        extract($viewData);

        require_once BASE_PATH . '/views/admin/users.php';
    }


    /**
     * =========================================================================
     * VIEW USER
     * =========================================================================
     * 
     * View detailed information about a specific user.
     */
    public function viewUser(): void
    {
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
        req.full_name AS requester_name,
        rec.full_name AS receiver_name,
        os.name AS offered_skill,
        rs.name AS requested_skill
    FROM exchange_requests er
    JOIN users req
        ON er.requester_id = req.id
    JOIN users rec
        ON er.receiver_id = rec.id
    JOIN skills os
        ON er.offered_skill_id = os.id
    JOIN skills rs
        ON er.requested_skill_id = rs.id
    WHERE er.requester_id = :requester_id
       OR er.receiver_id = :receiver_id
    ORDER BY er.created_at DESC
    LIMIT 10
");

        $stmt->execute([
            ':requester_id' => $id,
            ':receiver_id'  => $id
        ]);

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
    public function toggleUser(): void
    {
        // Get user ID from POST first, then GET as fallback
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        // Prevent invalid ID and prevent admin from disabling themselves
        if ($id <= 0 || $id === (int) getCurrentUserId()) {

            flash(
                'Invalid operation.',
                'danger'
            );

            redirect(
                url('Admin', 'users')
            );

            return;
        }


        // =========================================================
        // GET CURRENT USER STATUS
        // =========================================================

        $stmt = $this->db->prepare("
        SELECT id, full_name, is_active
        FROM users
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $user = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        // User doesn't exist
        if (!$user) {

            flash(
                'User not found.',
                'danger'
            );

            redirect(
                url('Admin', 'users')
            );

            return;
        }


        // =========================================================
        // TOGGLE STATUS
        // =========================================================

        $newStatus = ((int) $user['is_active'] === 1)
            ? 0
            : 1;


        $stmt = $this->db->prepare("
        UPDATE users
        SET is_active = :status
        WHERE id = :id
    ");

        $stmt->execute([
            ':status' => $newStatus,
            ':id'     => $id
        ]);


        // =========================================================
        // SUCCESS MESSAGE
        // =========================================================

        $statusText = $newStatus === 1
            ? 'activated'
            : 'deactivated';


        flash(
            'User has been ' . $statusText . ' successfully.',
            'success'
        );


        // Return to Admin Users
        redirect(
            url('Admin', 'users')
        );

        return;
    }
    /**
     * =========================================================================
     * MANAGE SKILLS
     * =========================================================================
     * 
     * List all master skills with management options.
     */

    public function skills(): void
    {
        $search = trim($_GET['search'] ?? '');
        $categoryId = (int) ($_GET['category'] ?? 0);

        // =========================================================
        // PAGINATION
        // =========================================================

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $limit = (int) ITEMS_PER_PAGE;

        $offset = ($page - 1) * $limit;


        // =========================================================
        // MAIN QUERY
        // =========================================================

        $sql = "
        SELECT
            s.*,
            c.name AS category_name,
            (
                SELECT COUNT(*)
                FROM user_skills us
                WHERE us.skill_id = s.id
            ) AS user_count
        FROM skills s
        JOIN categories c
            ON s.category_id = c.id
        WHERE 1 = 1
        ";

        $params = [];


        // =========================================================
        // SEARCH
        // =========================================================

        if ($search !== '') {

            $sql .= "
            AND s.name LIKE :search
        ";

            $params[':search'] =
                '%' . $search . '%';
        }


        // =========================================================
        // CATEGORY
        // =========================================================

        if ($categoryId > 0) {

            $sql .= "
            AND s.category_id = :category_id
        ";

            $params[':category_id'] =
                $categoryId;
        }


        // =========================================================
        // ORDER + PAGINATION
        // =========================================================

        $sql .= "
        ORDER BY c.name ASC, s.name ASC
        LIMIT :limit
        OFFSET :offset
        ";


        // =========================================================
        // GET SKILLS
        // =========================================================

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }

        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $skills = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // COUNT
        // =========================================================

        $countSql = "
        SELECT COUNT(*) AS total
        FROM skills s
        WHERE 1 = 1
        ";

        $countParams = [];


        if ($search !== '') {

            $countSql .= "
            AND s.name LIKE :search
        ";

            $countParams[':search'] =
                '%' . $search . '%';
        }


        if ($categoryId > 0) {

            $countSql .= "
            AND s.category_id = :category_id
        ";

            $countParams[':category_id'] =
                $categoryId;
        }


        $stmt = $this->db->prepare($countSql);

        foreach ($countParams as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }

        $stmt->execute();

        $row = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        $total = (int) (
            $row['total'] ?? 0
        );


        $totalPages = $limit > 0
            ? (int) ceil($total / $limit)
            : 1;
        $currentPage = $page;

        // =========================================================
        // CATEGORIES
        // =========================================================

        $stmt = $this->db->query("
        SELECT *
        FROM categories
        WHERE is_active = 1
        ORDER BY name
        ");

        $categories = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/skills.php';
    }
    /**
     * =========================================================================
     * ADD SKILL
     * =========================================================================
     * 
     * Add a new master skill to the system.
     */
    public function addSkill(): void
    {
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
    public function editSkill(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('Invalid skill.', 'danger');
            redirect(url('Admin', 'skills'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['name'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');

            if ($name === '' || $categoryId <= 0) {

                flash(
                    'Skill name and category are required.',
                    'danger'
                );

                redirect(
                    url('Admin', 'editSkill', ['id' => $id])
                );

                return;
            }


            // Check that the skill exists
            $stmt = $this->db->prepare("
            SELECT id
            FROM skills
            WHERE id = :id
            LIMIT 1
        ");

            $stmt->execute([
                ':id' => $id
            ]);

            if (!$stmt->fetch()) {

                flash(
                    'Skill not found.',
                    'danger'
                );

                redirect(
                    url('Admin', 'skills')
                );

                return;
            }


            // Check duplicate skill in same category
            $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM skills
            WHERE name = :name
              AND category_id = :category_id
              AND id != :skill_id
        ");

            $stmt->execute([
                ':name'        => $name,
                ':category_id' => $categoryId,
                ':skill_id'    => $id
            ]);

            $duplicate = (int) (
                $stmt->fetch()['total'] ?? 0
            );


            if ($duplicate > 0) {

                flash(
                    'This skill already exists in the selected category.',
                    'warning'
                );

                redirect(
                    url('Admin', 'editSkill', ['id' => $id])
                );

                return;
            }


            // Update skill
            $stmt = $this->db->prepare("
            UPDATE skills
            SET
                category_id = :category_id,
                name = :name,
                description = :description
            WHERE id = :id
        ");

            $stmt->execute([
                ':category_id' => $categoryId,
                ':name'        => $name,
                ':description' => $description,
                ':id'          => $id
            ]);


            flash(
                'Skill updated successfully.',
                'success'
            );

            redirect(
                url('Admin', 'skills')
            );

            return;
        }


        // =========================================================
        // GET SKILL
        // =========================================================

        $stmt = $this->db->prepare("
        SELECT *
        FROM skills
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $skill = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$skill) {

            flash(
                'Skill not found.',
                'danger'
            );

            redirect(
                url('Admin', 'skills')
            );

            return;
        }


        // =========================================================
        // CATEGORIES
        // =========================================================

        $stmt = $this->db->query("
        SELECT *
        FROM categories
        WHERE is_active = 1
        ORDER BY name
    ");

        $categories = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        require_once BASE_PATH .
            '/views/admin/edit-skill.php';
    }
    /**
     * =========================================================================
     * TOGGLE SKILL STATUS
     * =========================================================================
     */
    public function toggleSkill(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {

            flash(
                'Invalid skill.',
                'danger'
            );

            redirect(
                url('Admin', 'skills')
            );

            return;
        }


        // Get current status
        $stmt = $this->db->prepare("
        SELECT id, name, is_active
        FROM skills
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $skill = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$skill) {

            flash(
                'Skill not found.',
                'danger'
            );

            redirect(
                url('Admin', 'skills')
            );

            return;
        }


        // Toggle status
        $newStatus = ((int) $skill['is_active'] === 1)
            ? 0
            : 1;


        $stmt = $this->db->prepare("
        UPDATE skills
        SET is_active = :status
        WHERE id = :id
    ");

        $stmt->execute([
            ':status' => $newStatus,
            ':id'     => $id
        ]);


        $statusText = $newStatus === 1
            ? 'activated'
            : 'deactivated';


        flash(
            'Skill "' . $skill['name'] . '" has been '
                . $statusText . ' successfully.',
            'success'
        );


        redirect(
            url('Admin', 'skills')
        );

        return;
    }

    /**
     * =========================================================================
     * DELETE SKILL
     * =========================================================================
     *
     * Soft-delete a skill by marking it inactive.
     */
    public function deleteSkill(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('Invalid skill.', 'danger');
            redirect(url('Admin', 'skills'));
            return;
        }

        // Check if skill exists
        $stmt = $this->db->prepare("
        SELECT id, name
        FROM skills
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $skill = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$skill) {
            flash('Skill not found.', 'danger');
            redirect(url('Admin', 'skills'));
            return;
        }

        // Soft delete
        $stmt = $this->db->prepare("
        UPDATE skills
        SET is_active = 0
        WHERE id = :id
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        flash(
            'Skill "' . $skill['name'] . '" has been deleted successfully.',
            'success'
        );

        redirect(url('Admin', 'skills'));
        return;
    }

    /**
     * =========================================================================
     * MANAGE CATEGORIES
     * =========================================================================
     * 
     * List all categories with management options.
     */
    public function categories(): void
    {
        $stmt = $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM skills WHERE category_id = c.id) as skill_count FROM categories c ORDER BY c.name");
        $categories = $stmt->fetchAll();

        require_once BASE_PATH . '/views/admin/categories.php';
    }

    /**
     * =========================================================================
     * ADD CATEGORY
     * =========================================================================
     */
    public function addCategory(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'bi-grid');
            // $color = trim($_POST['color'] ?? '#0d6efd');

            if ($name === '') {

                flash(
                    'Category name is required.',
                    'danger'
                );

                redirect(
                    url('Admin', 'addCategory')
                );

                return;
            }


            // Check duplicate
            $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count
            FROM categories
            WHERE name = :name
        ");

            $stmt->execute([
                ':name' => $name
            ]);

            if ((int) $stmt->fetch()['count'] > 0) {

                flash(
                    'Category already exists.',
                    'warning'
                );

                redirect(
                    url('Admin', 'addCategory')
                );

                return;
            }


            // Insert category
            //     $stmt = $this->db->prepare("
            //     INSERT INTO categories
            //     (
            //         name,
            //         description,
            //         icon,
            //         color,
            //         is_active
            //     )
            //     VALUES
            //     (
            //         :name,
            //         :description,
            //         :icon,
            //         :color,
            //         1
            //     )
            // ");

            //     $stmt->execute([
            //         ':name'        => $name,
            //         ':description' => $description,
            //         ':icon'        => $icon,
            //         ':color'       => $color
            //     ]);
            $stmt = $this->db->prepare("
                INSERT INTO categories
                (
                    name,
                    description,
                    icon,
                    is_active
                )
                VALUES
                (
                    :name,
                    :description,
                    :icon,
                    1
                )
            ");

            $stmt->execute([
                ':name'        => $name,
                ':description' => $description,
                ':icon'        => $icon
            ]);


            flash(
                'Category added successfully.',
                'success'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        require_once BASE_PATH .
            '/views/admin/add-category.php';
    }

    /**
     * =========================================================================
     * EDIT CATEGORY
     * =========================================================================
     */
    public function editCategory(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {

            flash(
                'Invalid category.',
                'danger'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'bi-grid');
            // $color = trim($_POST['color'] ?? '#0d6efd');


            if ($name === '') {

                flash(
                    'Category name is required.',
                    'danger'
                );

                redirect(
                    url('Admin', 'editCategory', ['id' => $id])
                );

                return;
            }


            // Check duplicate
            $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count
            FROM categories
            WHERE name = :name
              AND id != :id
        ");

            $stmt->execute([
                ':name' => $name,
                ':id'   => $id
            ]);

            if ((int) $stmt->fetch()['count'] > 0) {

                flash(
                    'Another category with this name already exists.',
                    'warning'
                );

                redirect(
                    url('Admin', 'editCategory', ['id' => $id])
                );

                return;
            }


            // Update category
            //     $stmt = $this->db->prepare("
            //     UPDATE categories
            //     SET
            //         name = :name,
            //         description = :description,
            //         icon = :icon,
            //         color = :color
            //     WHERE id = :id
            // ");

            //     $stmt->execute([
            //         ':name'        => $name,
            //         ':description' => $description,
            //         ':icon'        => $icon,
            //         ':color'       => $color,
            //         ':id'          => $id
            //     ]);
            $stmt = $this->db->prepare("
                UPDATE categories
                SET
                    name = :name,
                    description = :description,
                    icon = :icon
                WHERE id = :id
            ");
            $stmt->execute([
                ':name'        => $name,
                ':description' => $description,
                ':icon'        => $icon,
                ':id'          => $id
            ]);


            flash(
                'Category updated successfully.',
                'success'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        // Get category
        $stmt = $this->db->prepare("
        SELECT *
        FROM categories
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $category = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$category) {

            flash(
                'Category not found.',
                'danger'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        require_once BASE_PATH .
            '/views/admin/edit-category.php';
    }

    /**
     * =========================================================================
     * TOGGLE CATEGORY STATUS
     * =========================================================================
     */
    public function toggleCategory(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('Invalid category.', 'danger');
            redirect(url('Admin', 'categories'));
            return;
        }

        // Get current category
        $stmt = $this->db->prepare("
        SELECT id, name, is_active
        FROM categories
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            flash('Category not found.', 'danger');
            redirect(url('Admin', 'categories'));
            return;
        }

        // Toggle status
        $newStatus = ((int) $category['is_active'] === 1)
            ? 0
            : 1;

        $stmt = $this->db->prepare("
        UPDATE categories
        SET is_active = :status
        WHERE id = :id
    ");

        $stmt->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);

        $statusText = $newStatus === 1
            ? 'activated'
            : 'deactivated';

        flash(
            'Category "' . $category['name'] . '" has been '
                . $statusText . ' successfully.',
            'success'
        );

        redirect(url('Admin', 'categories'));
        return;
    }
    /**
     * =========================================================================
     * DELETE CATEGORY
     * =========================================================================
     */
    public function deleteCategory(): void
    {
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {

            flash(
                'Invalid category.',
                'danger'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        // Check if category exists
        $stmt = $this->db->prepare("
        SELECT id, name
        FROM categories
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $category = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$category) {

            flash(
                'Category not found.',
                'danger'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        // Check if category has skills
        $stmt = $this->db->prepare("
        SELECT COUNT(*) AS count
        FROM skills
        WHERE category_id = :id
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $skillCount = (int) (
            $stmt->fetch()['count'] ?? 0
        );


        if ($skillCount > 0) {

            flash(
                'Cannot delete category with existing skills.',
                'warning'
            );

            redirect(
                url('Admin', 'categories')
            );

            return;
        }


        // Delete category
        $stmt = $this->db->prepare("
        DELETE FROM categories
        WHERE id = :id
    ");

        $stmt->execute([
            ':id' => $id
        ]);


        flash(
            'Category deleted successfully.',
            'success'
        );

        redirect(
            url('Admin', 'categories')
        );

        return;
    }
    /**
     * =========================================================================
     * MANAGE REVIEWS
     * =========================================================================
     * 
     * List all reviews with management options.
     */
    public function reviews(): void
    {
        // =========================================================
        // PAGINATION
        // =========================================================

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $limit = (int) ITEMS_PER_PAGE;

        $offset = ($page - 1) * $limit;


        // =========================================================
        // GET REVIEWS
        // =========================================================

        $stmt = $this->db->prepare("
        SELECT
            r.*,
            rev.full_name AS reviewer_name,
            rec.full_name AS reviewee_name,
            er.status AS exchange_status
        FROM reviews r
        JOIN users rev
            ON r.reviewer_id = rev.id
        JOIN users rec
            ON r.reviewee_id = rec.id
        JOIN exchange_requests er
            ON r.exchange_request_id = er.id
        ORDER BY r.created_at DESC
        LIMIT :limit
        OFFSET :offset
    ");

        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $reviews = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // TOTAL REVIEWS
        // =========================================================

        $stmt = $this->db->query("
        SELECT COUNT(*) AS total
        FROM reviews
    ");

        $row = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        $total = (int) (
            $row['total'] ?? 0
        );


        // =========================================================
        // PAGINATION VALUES
        // =========================================================

        $totalPages = $limit > 0
            ? (int) ceil($total / $limit)
            : 1;

        $currentPage = $page;


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/reviews.php';
    }


    /**
     * =========================================================================
     * DELETE REVIEW
     * =========================================================================
     */
    public function deleteReview(): void
    {
        $id = (int) (
            $_POST['id']
            ?? $_GET['id']
            ?? 0
        );


        if ($id <= 0) {

            flash(
                'Invalid review.',
                'danger'
            );

            redirect(
                url('Admin', 'reviews')
            );

            return;
        }


        // Check review exists
        $stmt = $this->db->prepare("
        SELECT id
        FROM reviews
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);


        if (!$stmt->fetch()) {

            flash(
                'Review not found.',
                'danger'
            );

            redirect(
                url('Admin', 'reviews')
            );

            return;
        }


        // Delete review
        $stmt = $this->db->prepare("
        DELETE FROM reviews
        WHERE id = :id
    ");

        $stmt->execute([
            ':id' => $id
        ]);


        flash(
            'Review deleted successfully.',
            'success'
        );


        redirect(
            url('Admin', 'reviews')
        );

        return;
    }

    /**
     * =========================================================================
     * MANAGE REPORTS
     * =========================================================================
     * 
     * List all user reports with management options.
     */
    public function reports(): void
    {
        // =========================================================
        // FILTER
        // =========================================================

        $status = trim($_GET['status'] ?? '');


        // =========================================================
        // PAGINATION
        // =========================================================

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $limit = (int) ITEMS_PER_PAGE;

        $offset = ($page - 1) * $limit;


        // =========================================================
        // MAIN QUERY
        // =========================================================

        $sql = "
            SELECT
                r.*,
                rep.full_name AS reporter_name,
                rec.full_name AS reported_user_name
                
            FROM reports r
            JOIN users rep
                ON r.reporter_id = rep.id
            JOIN users rec
                ON r.reported_id = rec.id
            WHERE 1 = 1
        ";

        $params = [];


        // =========================================================
        // STATUS FILTER
        // =========================================================

        if ($status !== '') {

            $sql .= "
                AND r.status = :status
            ";

            $params[':status'] = $status;
        }


        // =========================================================
        // ORDER + PAGINATION
        // =========================================================

        $sql .= "
            ORDER BY r.created_at DESC
            LIMIT :limit
            OFFSET :offset
        ";


        // =========================================================
        // EXECUTE MAIN QUERY
        // =========================================================

        $stmt = $this->db->prepare($sql);


        foreach ($params as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );


        $stmt->execute();

        $reports = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // COUNT
        // =========================================================

        $countSql = "
            SELECT COUNT(*) AS total
            FROM reports
            WHERE 1 = 1
        ";

        $countParams = [];


        if ($status !== '') {

            $countSql .= "
                AND status = :status
            ";

            $countParams[':status'] = $status;
        }


        $stmt = $this->db->prepare($countSql);


        foreach ($countParams as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }


        $stmt->execute();

        $row = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        $total = (int) (
            $row['total'] ?? 0
        );


        // =========================================================
        // PAGINATION VALUES
        // =========================================================

        $totalPages = $limit > 0
            ? (int) ceil($total / $limit)
            : 1;

        $currentPage = $page;


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/reports.php';
    }

    public function viewReport(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('Invalid report.', 'danger');
            redirect(url('Admin', 'reports'));
            return;
        }

        $stmt = $this->db->prepare("
        SELECT
            r.*,

            rep.full_name AS reporter_name,
            rep.email AS reporter_email,

            rec.full_name AS reported_user_name,
            rec.email AS reported_user_email

        FROM reports r

        JOIN users rep
            ON r.reporter_id = rep.id

        JOIN users rec
            ON r.reported_id = rec.id

        WHERE r.id = :id

        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$report) {
            flash('Report not found.', 'danger');
            redirect(url('Admin', 'reports'));
            return;
        }

        require_once BASE_PATH .
            '/views/admin/view-report.php';
    }

    public function updateReport(): void
    {
        $id = (int) (
            $_POST['id']
            ?? $_GET['id']
            ?? 0
        );

        $status = trim(
            $_POST['status'] ?? ''
        );

        $adminNotes = trim(
            $_POST['admin_notes'] ?? ''
        );


        if ($id <= 0) {

            flash(
                'Invalid report.',
                'danger'
            );

            redirect(
                url('Admin', 'reports')
            );

            return;
        }


        // These are the statuses YOUR controller currently supports.
        $allowedStatuses = [
            'pending',
            'reviewed',
            'resolved',
            'dismissed'
        ];


        if (!in_array(
            $status,
            $allowedStatuses,
            true
        )) {

            flash(
                'Invalid status.',
                'danger'
            );

            redirect(
                url(
                    'Admin',
                    'viewReport',
                    ['id' => $id]
                )
            );

            return;
        }


        $stmt = $this->db->prepare("
        UPDATE reports
        SET
            status = :status,
            admin_notes = :admin_notes,
            updated_at = NOW()
        WHERE id = :id
    ");


        $stmt->execute([
            ':status' => $status,
            ':admin_notes' => $adminNotes,
            ':id' => $id
        ]);


        flash(
            'Report status updated successfully.',
            'success'
        );


        redirect(
            url(
                'Admin',
                'viewReport',
                ['id' => $id]
            )
        );

        return;
    }

    /**
     * =========================================================================
     * MANAGE NOTIFICATIONS
     * =========================================================================
     *
     * Send bulk system notifications to users.
     */
    public function notifications(): void
    {
        // =========================================================
        // SEND NOTIFICATION
        // =========================================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $title = trim(
                $_POST['title'] ?? ''
            );

            $message = trim(
                $_POST['message'] ?? ''
            );

            $target = $_POST['target'] ?? 'all';


            // =====================================================
            // VALIDATION
            // =====================================================

            if ($title === '' || $message === '') {

                flash(
                    'Title and message are required.',
                    'danger'
                );

                redirect(
                    'index.php?controller=Admin&action=notifications'
                );

                return;
            }


            // =====================================================
            // VALID TARGET
            // =====================================================

            if (!in_array(
                $target,
                ['all', 'active'],
                true
            )) {

                $target = 'all';
            }


            // =====================================================
            // GET TARGET USERS
            // =====================================================

            if ($target === 'active') {

                $stmt = $this->db->query("
                SELECT id
                FROM users
                WHERE role = 'user'
                AND is_active = 1
            ");
            } else {

                $stmt = $this->db->query("
                SELECT id
                FROM users
                WHERE role = 'user'
            ");
            }


            $users = $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


            // =====================================================
            // PREPARE NOTIFICATION INSERT
            // =====================================================

            $notifStmt = $this->db->prepare("
            INSERT INTO notifications
            (
                user_id,
                type,
                reference_id,
                title,
                message,
                is_read,
                created_at
            )
            VALUES
            (
                :user_id,
                'system',
                0,
                :title,
                :message,
                0,
                NOW()
            )
        ");


            // =====================================================
            // SEND TO USERS
            // =====================================================

            $count = 0;

            foreach ($users as $user) {

                $notifStmt->execute([
                    ':user_id' => $user['id'],
                    ':title' => $title,
                    ':message' => $message
                ]);

                $count++;
            }


            // =====================================================
            // SUCCESS
            // =====================================================

            flash(
                "Notification sent to {$count} users.",
                'success'
            );

            redirect(
                'index.php?controller=Admin&action=notifications'
            );

            return;
        }


        // =========================================================
        // ADMIN NOTIFICATION STATISTICS
        // =========================================================

        $stmt = $this->db->query("
        SELECT
            COUNT(*) AS total,
            SUM(
                CASE
                    WHEN is_read = 0
                    THEN 1
                    ELSE 0
                END
            ) AS unread
        FROM notifications
    ");

        $stats = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // RECENT BULK NOTIFICATIONS
        // =========================================================

        /*
     * Notifications are stored individually for each user.
     *
     * Therefore identical bulk notifications are grouped together
     * so the admin sees one notification with its recipient count.
     */

        //     $stmt = $this->db->query("
        //     SELECT
        //         title,
        //         message,
        //         type,
        //         COUNT(*) AS recipient_count,
        //         MAX(created_at) AS created_at
        //     FROM notifications
        //     WHERE type = 'system'
        //     GROUP BY
        //         title,
        //         message,
        //         type
        //     ORDER BY created_at DESC
        //     LIMIT 10
        // ");

        //     $recentNotifications = $stmt->fetchAll(
        //         PDO::FETCH_ASSOC
        //     );
        $stmt = $this->db->query("
    SELECT
        MIN(id) AS id,
        title,
        message,
        type,
        COUNT(*) AS recipient_count,
        MAX(created_at) AS created_at
    FROM notifications
    WHERE type = 'system'
    GROUP BY
        title,
        message,
        type
    ORDER BY created_at DESC
    LIMIT 10
");

        $recentNotifications = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/notifications.php';
    }

    /**
     * =========================================================================
     * VIEW NOTIFICATION
     * =========================================================================
     *
     * View details of a bulk system notification.
     */
    public function viewNotification(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {

            flash(
                'Invalid notification.',
                'danger'
            );

            redirect(
                'index.php?controller=Admin&action=notifications'
            );

            return;
        }


        // =========================================================
        // GET NOTIFICATION
        // =========================================================

        $stmt = $this->db->prepare("
        SELECT
            id,
            title,
            message,
            type,
            created_at
        FROM notifications
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->execute([
            ':id' => $id
        ]);

        $notification = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        if (!$notification) {

            flash(
                'Notification not found.',
                'danger'
            );

            redirect(
                'index.php?controller=Admin&action=notifications'
            );

            return;
        }


        // =========================================================
        // GET RECIPIENT STATISTICS
        // =========================================================

        /*
     * Because bulk notifications are stored individually,
     * identify the whole bulk notification by title, message,
     * type and created_at.
     */

        $stmt = $this->db->prepare("
        SELECT
            COUNT(*) AS recipient_count,

            SUM(
                CASE
                    WHEN is_read = 1
                    THEN 1
                    ELSE 0
                END
            ) AS read_count,

            SUM(
                CASE
                    WHEN is_read = 0
                    THEN 1
                    ELSE 0
                END
            ) AS unread_count

        FROM notifications

        WHERE title = :title
        AND message = :message
        AND type = :type
        AND created_at = :created_at
    ");

        $stmt->execute([
            ':title' => $notification['title'],
            ':message' => $notification['message'],
            ':type' => $notification['type'],
            ':created_at' => $notification['created_at']
        ]);

        $notificationStats = $stmt->fetch(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/view-notification.php';
    }
    /**
     * =========================================================================
     * SETTINGS
     * =========================================================================
     * 
     * Manage application settings.
     */
    /**
     * =========================================================================
     * APP SETTINGS
     * =========================================================================
     *
     * Manage platform-wide application settings.
     */
    public function settings(): void
    {
        // =========================================================
        // SAVE SETTINGS
        // =========================================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $settingsToSave = [

                // General
                'site_name' =>
                trim($_POST['site_name'] ?? 'SkillSwap'),

                'site_description' =>
                trim($_POST['site_description'] ?? ''),

                'site_tagline' =>
                trim($_POST['site_tagline'] ?? ''),

                'contact_email' =>
                trim($_POST['contact_email'] ?? ''),


                // Pagination / Matching
                'items_per_page' =>
                max(
                    1,
                    min(
                        100,
                        (int) ($_POST['items_per_page'] ?? 10)
                    )
                ),

                'match_threshold' =>
                max(
                    0,
                    min(
                        100,
                        (int) ($_POST['match_threshold'] ?? 30)
                    )
                ),


                // User settings
                'max_skills_per_user' =>
                max(
                    1,
                    min(
                        50,
                        (int) ($_POST['max_skills_per_user'] ?? 10)
                    )
                ),

                'max_portfolio_items' =>
                max(
                    1,
                    min(
                        50,
                        (int) ($_POST['max_portfolio_items'] ?? 10)
                    )
                ),

                'enable_registration' =>
                isset($_POST['enable_registration']) ? '1' : '0',

                'require_email_verification' =>
                isset($_POST['require_email_verification']) ? '1' : '0',


                // Exchange settings
                'max_active_exchanges' =>
                max(
                    1,
                    min(
                        20,
                        (int) ($_POST['max_active_exchanges'] ?? 5)
                    )
                ),

                'exchange_expiry_days' =>
                max(
                    1,
                    min(
                        30,
                        (int) ($_POST['exchange_expiry_days'] ?? 7)
                    )
                ),


                // Moderation
                'enable_reviews' =>
                isset($_POST['enable_reviews']) ? '1' : '0',

                'enable_reports' =>
                isset($_POST['enable_reports']) ? '1' : '0',

                'moderation_keywords' =>
                trim($_POST['moderation_keywords'] ?? ''),


                // System
                'maintenance_mode' =>
                isset($_POST['maintenance_mode']) ? '1' : '0'
            ];


            // =========================================================
            // VALIDATE CONTACT EMAIL
            // =========================================================

            if (
                $settingsToSave['contact_email'] !== '' &&
                !filter_var(
                    $settingsToSave['contact_email'],
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                flash(
                    'Please enter a valid contact email address.',
                    'danger'
                );

                redirect(
                    'index.php?controller=Admin&action=settings'
                );

                return;
            }


            // =========================================================
            // SAVE SETTINGS
            // =========================================================

            $stmt = $this->db->prepare("
            INSERT INTO settings
            (
                setting_key,
                setting_value,
                description
            )
            VALUES
            (
                :setting_key,
                :setting_value,
                :description
            )
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value)
        ");


            foreach ($settingsToSave as $key => $value) {

                $stmt->execute([
                    ':setting_key' => $key,
                    ':setting_value' => (string) $value,
                    ':description' => $key
                ]);
            }


            flash(
                'Application settings saved successfully.',
                'success'
            );


            redirect(
                'index.php?controller=Admin&action=settings'
            );

            return;
        }


        // =========================================================
        // LOAD SETTINGS
        // =========================================================

        $stmt = $this->db->query("
        SELECT
            setting_key,
            setting_value
        FROM settings
        ORDER BY id ASC
    ");

        $settingsRows = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        $settings = [];

        foreach ($settingsRows as $row) {

            $settings[$row['setting_key']] = $row['setting_value'];
        }


        // =========================================================
        // PLATFORM STATISTICS
        // =========================================================

        $stmt = $this->db->query("
        SELECT
            COUNT(*) AS total_users
        FROM users
    ");

        $totalUsers =
            (int) (
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                )['total_users'] ?? 0
            );


        $stmt = $this->db->query("
        SELECT
            COUNT(*) AS total_exchanges
        FROM exchange_requests
    ");

        $totalExchanges =
            (int) (
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                )['total_exchanges'] ?? 0
            );


        $stats = [
            'total_users' =>
            $totalUsers,

            'total_exchanges' =>
            $totalExchanges
        ];


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/settings.php';
    }
    /**
     * =========================================================================
     * MANAGE EXCHANGES
     * =========================================================================
     */
    public function exchanges(): void
    {
        $status = trim($_GET['status'] ?? '');

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $limit = (int) ITEMS_PER_PAGE;

        if ($limit <= 0) {
            $limit = 10;
        }

        $offset = ($page - 1) * $limit;


        // =========================================================
        // MAIN QUERY
        // =========================================================

        $sql = "
        SELECT
            er.*,

            requester.full_name AS requester_name,

            receiver.full_name AS receiver_name,

            offered.name AS offered_skill_name,

            requested.name AS requested_skill_name

        FROM exchange_requests er

        INNER JOIN users requester
            ON er.requester_id = requester.id

        INNER JOIN users receiver
            ON er.receiver_id = receiver.id

        INNER JOIN skills offered
            ON er.offered_skill_id = offered.id

        INNER JOIN skills requested
            ON er.requested_skill_id = requested.id

        WHERE 1 = 1
    ";

        $params = [];


        // =========================================================
        // STATUS FILTER
        // =========================================================

        $allowedStatuses = [
            'pending',
            'accepted',
            'in_progress',
            'completed',
            'declined'
        ];

        if (
            $status !== '' &&
            in_array($status, $allowedStatuses, true)
        ) {

            $sql .= "
            AND er.status = :status
        ";

            $params[':status'] = $status;
        }


        // =========================================================
        // ORDER + PAGINATION
        // =========================================================

        $sql .= "
        ORDER BY er.created_at DESC
        LIMIT :limit
        OFFSET :offset
    ";


        // =========================================================
        // EXECUTE
        // =========================================================

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }

        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $exchanges = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


        // =========================================================
        // COUNT
        // =========================================================

        $countSql = "
        SELECT COUNT(*) AS total
        FROM exchange_requests er
        WHERE 1 = 1
    ";

        $countParams = [];

        if (
            $status !== '' &&
            in_array($status, $allowedStatuses, true)
        ) {

            $countSql .= "
            AND er.status = :status
        ";

            $countParams[':status'] = $status;
        }


        $stmt = $this->db->prepare($countSql);

        foreach ($countParams as $key => $value) {

            $stmt->bindValue(
                $key,
                $value,
                PDO::PARAM_STR
            );
        }

        $stmt->execute();

        $row = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        $total = (int) (
            $row['total'] ?? 0
        );

        $totalPages = $limit > 0
            ? (int) ceil($total / $limit)
            : 1;


        // =========================================================
        // VIEW
        // =========================================================

        require_once BASE_PATH .
            '/views/admin/exchanges.php';
    }

    /**
 * =========================================================================
 * VIEW EXCHANGE
 * =========================================================================
 */
public function viewExchange(): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        flash('Invalid exchange.', 'danger');

        redirect(
            'index.php?controller=Admin&action=exchanges'
        );

        return;
    }


    // =========================================================
    // GET EXCHANGE DETAILS
    // =========================================================

    $stmt = $this->db->prepare("
        SELECT
            er.*,

            requester.full_name AS requester_name,
            requester.email AS requester_email,

            receiver.full_name AS receiver_name,
            receiver.email AS receiver_email,

            offered.name AS offered_skill_name,
            offered.description AS offered_skill_description,

            requested.name AS requested_skill_name,
            requested.description AS requested_skill_description

        FROM exchange_requests er

        INNER JOIN users requester
            ON er.requester_id = requester.id

        INNER JOIN users receiver
            ON er.receiver_id = receiver.id

        INNER JOIN skills offered
            ON er.offered_skill_id = offered.id

        INNER JOIN skills requested
            ON er.requested_skill_id = requested.id

        WHERE er.id = :id

        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $exchange = $stmt->fetch(
        PDO::FETCH_ASSOC
    );


    // =========================================================
    // CHECK EXISTS
    // =========================================================

    if (!$exchange) {

        flash(
            'Exchange request not found.',
            'danger'
        );

        redirect(
            'index.php?controller=Admin&action=exchanges'
        );

        return;
    }


    // =========================================================
    // VIEW
    // =========================================================

    require_once BASE_PATH .
        '/views/admin/view-exchange.php';
}

/**
 * =========================================================================
 * UPDATE EXCHANGE STATUS
 * =========================================================================
 */
public function updateExchangeStatus(): void
{
    $id = (int) ($_GET['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($id <= 0) {
        flash('Invalid exchange.', 'danger');
        redirect('index.php?controller=Admin&action=exchanges');
        return;
    }

    $allowedStatuses = [
        'pending',
        'accepted',
        'in_progress',
        'completed',
        'declined'
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        flash('Invalid exchange status.', 'danger');
        redirect(
            'index.php?controller=Admin&action=viewExchange&id=' . $id
        );
        return;
    }

    // Check that the exchange exists
    $stmt = $this->db->prepare("
        SELECT id, status
        FROM exchange_requests
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $exchange = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exchange) {
        flash('Exchange not found.', 'danger');
        redirect('index.php?controller=Admin&action=exchanges');
        return;
    }

    // Update status
    $stmt = $this->db->prepare("
        UPDATE exchange_requests
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);

    flash(
        'Exchange status updated to ' .
        ucwords(str_replace('_', ' ', $status)) .
        '.',
        'success'
    );

    redirect(
        'index.php?controller=Admin&action=viewExchange&id=' . $id
    );
}
    /**
     * =========================================================================
     * HELPER METHODS
     * =========================================================================
     */

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
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
    private function getRecentUsers(int $limit): array
    {
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
    private function getRecentExchanges(int $limit): array
    {
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
    private function getPendingReports(int $limit): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                rep.full_name as reporter_user_name,
                rec.full_name as reported_user_name
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

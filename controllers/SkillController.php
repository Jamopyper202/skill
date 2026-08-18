<?php
/**
 * ============================================================================
 * Skill Controller
 * ============================================================================
 * 
 * Handles skill management: adding, editing, deleting skills,
 * browsing skills, and managing wanted skills.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class SkillController {
    /**
     * Skill model instance
     * @var Skill
     */
    private Skill $skillModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->skillModel = new Skill();
    }

    /**
     * =========================================================================
     * LIST USER'S SKILLS
     * =========================================================================
     * 
     * Display all skills offered and wanted by the logged-in user.
     */
    public function index(): void {
        $userId = getCurrentUserId();

        // Get user's offered skills
        $userSkills = $this->skillModel->getUserSkills($userId);

        // Get user's wanted skills
        $wantedSkills = $this->skillModel->getWantedSkills($userId);

        // Get all categories for forms
        $categories = $this->skillModel->getAllCategories();

        // Get all master skills for dropdowns
        $masterSkills = $this->skillModel->getAllMaster();

        setPageTitle('My Skills');
        require_once BASE_PATH . '/views/skills/index.php';
    }

    /**
     * =========================================================================
     * ADD OFFERED SKILL
     * =========================================================================
     * 
     * Handle form to add a skill the user offers.
     */
    public function add(): void {
        $userId = getCurrentUserId();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $skillId = (int) ($_POST['skill_id'] ?? 0);
            $experienceLevel = $_POST['experience_level'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $yearsExperience = (int) ($_POST['years_of_experience'] ?? 0);

            // Validation
            if ($skillId === 0) {
                $errors[] = 'Please select a skill.';
            } elseif ($this->skillModel->userHasSkill($userId, $skillId)) {
                $errors[] = 'You have already added this skill.';
            }

            if (empty($experienceLevel)) {
                $errors[] = 'Please select your experience level.';
            }

            if (empty($errors)) {
                if ($this->skillModel->addUserSkill($userId, $skillId, $experienceLevel, $description, $yearsExperience)) {
                    flash('Skill added successfully.', 'success');
                    redirect(url('Skill', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to add skill. Please try again.';
                }
            }

            storeOldInput($_POST);
        }

        // Get categories and skills for form
        $categories = $this->skillModel->getAllCategories();
        $masterSkills = $this->skillModel->getAllMaster();

        setPageTitle('Add Skill');
        require_once BASE_PATH . '/views/skills/create.php';
    }

    /**
     * =========================================================================
     * EDIT OFFERED SKILL
     * =========================================================================
     * 
     * Handle form to edit an existing offered skill.
     */
    public function edit(): void {
        $userId = getCurrentUserId();
        $userSkillId = (int) ($_GET['id'] ?? 0);

        // Get the user skill
        $userSkill = $this->skillModel->getUserSkillById($userSkillId, $userId);

        if (!$userSkill) {
            flash('Skill not found.', 'danger');
            redirect(url('Skill', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $experienceLevel = $_POST['experience_level'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $yearsExperience = (int) ($_POST['years_of_experience'] ?? 0);

            // Validation
            if (empty($experienceLevel)) {
                $errors[] = 'Please select your experience level.';
            }

            if (empty($errors)) {
                if ($this->skillModel->updateUserSkill($userSkillId, $userId, $experienceLevel, $description, $yearsExperience)) {
                    flash('Skill updated successfully.', 'success');
                    redirect(url('Skill', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to update skill. Please try again.';
                }
            }
        }

        setPageTitle('Edit Skill');
        require_once BASE_PATH . '/views/skills/edit.php';
    }

    /**
     * =========================================================================
     * DELETE OFFERED SKILL
     * =========================================================================
     * 
     * Delete a skill the user offers.
     */
    public function delete(): void {
        $userId = getCurrentUserId();
        $userSkillId = (int) ($_GET['id'] ?? 0);

        if ($this->skillModel->deleteUserSkill($userSkillId, $userId)) {
            flash('Skill deleted successfully.', 'success');
        } else {
            flash('Failed to delete skill.', 'danger');
        }

        redirect(url('Skill', 'index'));
    }

    /**
     * =========================================================================
     * ADD WANTED SKILL
     * =========================================================================
     * 
     * Handle form to add a skill the user wants to learn.
     */
    public function addWanted(): void {
        $userId = getCurrentUserId();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $skillId = (int) ($_POST['skill_id'] ?? 0);
            $experienceLevel = $_POST['experience_level'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $urgency = $_POST['urgency'] ?? 'Medium';

            // Validation
            if ($skillId === 0) {
                $errors[] = 'Please select a skill.';
            } elseif ($this->skillModel->userWantsSkill($userId, $skillId)) {
                $errors[] = 'You have already added this skill to your wanted list.';
            }

            if (empty($experienceLevel)) {
                $errors[] = 'Please select your desired experience level.';
            }

            if (empty($errors)) {
                if ($this->skillModel->addWantedSkill($userId, $skillId, $experienceLevel, $description, $urgency)) {
                    flash('Wanted skill added successfully.', 'success');
                    redirect(url('Skill', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to add wanted skill. Please try again.';
                }
            }

            storeOldInput($_POST);
        }

        // Get categories and skills for form
        $categories = $this->skillModel->getAllCategories();
        $masterSkills = $this->skillModel->getAllMaster();

        setPageTitle('Add Wanted Skill');
        require_once BASE_PATH . '/views/skills/create-wanted.php';
    }

    /**
     * =========================================================================
     * EDIT WANTED SKILL
     * =========================================================================
     * 
     * Handle form to edit an existing wanted skill.
     */
    public function editWanted(): void {
        $userId = getCurrentUserId();
        $wantedSkillId = (int) ($_GET['id'] ?? 0);

        // Get the wanted skill
        $wantedSkill = $this->skillModel->getWantedSkillById($wantedSkillId, $userId);

        if (!$wantedSkill) {
            flash('Skill not found.', 'danger');
            redirect(url('Skill', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $experienceLevel = $_POST['experience_level'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $urgency = $_POST['urgency'] ?? 'Medium';

            // Validation
            if (empty($experienceLevel)) {
                $errors[] = 'Please select your desired experience level.';
            }

            if (empty($errors)) {
                if ($this->skillModel->updateWantedSkill($wantedSkillId, $userId, $experienceLevel, $description, $urgency)) {
                    flash('Wanted skill updated successfully.', 'success');
                    redirect(url('Skill', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to update wanted skill. Please try again.';
                }
            }
        }

        setPageTitle('Edit Wanted Skill');
        require_once BASE_PATH . '/views/skills/edit-wanted.php';
    }

    /**
     * =========================================================================
     * DELETE WANTED SKILL
     * =========================================================================
     * 
     * Delete a skill from the user's wanted list.
     */
    public function deleteWanted(): void {
        $userId = getCurrentUserId();
        $wantedSkillId = (int) ($_GET['id'] ?? 0);

        if ($this->skillModel->deleteWantedSkill($wantedSkillId, $userId)) {
            flash('Wanted skill deleted successfully.', 'success');
        } else {
            flash('Failed to delete wanted skill.', 'danger');
        }

        redirect(url('Skill', 'index'));
    }

    /**
     * =========================================================================
     * BROWSE ALL SKILLS
     * =========================================================================
     * 
     * Public page to browse all offered skills with filters.
     */
    public function browse(): void {
        $categoryId = (int) ($_GET['category'] ?? 0);
        $search = trim($_GET['search'] ?? '');
        $experience = $_GET['experience'] ?? '';

        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get filtered skills
        $skills = $this->skillModel->browseOffered($categoryId, $search, $experience, $limit, $offset);

        // Get total for pagination
        $total = $this->skillModel->countOffered($categoryId, $search, $experience);
        $totalPages = ceil($total / $limit);

        // Get categories for filter
        $categories = $this->skillModel->getAllCategories();

        setPageTitle('Browse Skills');
        require_once BASE_PATH . '/views/skills/browse.php';
    }

    /**
     * =========================================================================
     * GET SKILLS BY CATEGORY (AJAX)
     * =========================================================================
     * 
     * Return skills for a category as JSON (used in forms).
     */
    public function getByCategory(): void {
        header('Content-Type: application/json');

        $categoryId = (int) ($_GET['category_id'] ?? 0);

        if ($categoryId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            return;
        }

        $skills = $this->skillModel->getByCategory($categoryId);

        echo json_encode([
            'success' => true,
            'skills' => $skills
        ]);
    }
}
?>
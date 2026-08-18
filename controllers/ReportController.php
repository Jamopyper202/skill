<?php
/**
 * ============================================================================
 * Report Controller
 * ============================================================================
 * 
 * Handles user reporting system: create report, view reports.
 * Admin functions are handled by AdminController.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class ReportController {
    /**
     * Report model instance
     * @var Report
     */
    private Report $reportModel;

    /**
     * User model instance
     * @var User
     */
    private User $userModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->reportModel = new Report();
        $this->userModel = new User();
    }

    /**
     * =========================================================================
     * CREATE REPORT
     * =========================================================================
     * 
     * Display and handle report creation form.
     */
    public function create(): void {
        $userId = getCurrentUserId();
        $errors = [];

        $reportedId = (int) ($_GET['user_id'] ?? 0);

        if ($reportedId === 0 || $reportedId === $userId) {
            flash('Invalid user.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Get reported user info
        $reportedUser = $this->userModel->findById($reportedId);

        if (!$reportedUser || !$reportedUser['is_active']) {
            flash('User not found or account is inactive.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Check if already reported
        if ($this->reportModel->hasReported($userId, $reportedId)) {
            flash('You have already reported this user. Your report is being reviewed.', 'warning');
            redirect(url('Profile', 'view', ['id' => $reportedId]));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = $_POST['reason'] ?? '';
            $description = trim($_POST['description'] ?? '');

            // Validation
            $validReasons = ['spam', 'harassment', 'fake_profile', 'inappropriate_content', 'other'];
            if (!in_array($reason, $validReasons)) {
                $errors[] = 'Please select a valid reason.';
            }

            if (!isRequired($description)) {
                $errors[] = 'Please provide a description of the issue.';
            } elseif (strlen($description) < 20) {
                $errors[] = 'Description must be at least 20 characters.';
            }

            if (empty($errors)) {
                $reportId = $this->reportModel->create($userId, $reportedId, $reason, $description);

                if ($reportId) {
                    flash('Report submitted successfully. Thank you for helping keep our community safe.', 'success');
                    redirect(url('Profile', 'view', ['id' => $reportedId]));
                    return;
                } else {
                    $errors[] = 'Failed to submit report. Please try again.';
                }
            }

            storeOldInput(['reason' => $reason, 'description' => $description]);
        }

        setPageTitle('Report User');
        require_once BASE_PATH . '/views/reports/create.php';
    }

    /**
     * =========================================================================
     * MY REPORTS
     * =========================================================================
     * 
     * View reports filed by the logged-in user.
     */
    public function index(): void {
        $userId = getCurrentUserId();

        $reports = $this->reportModel->getByReporter($userId);

        setPageTitle('My Reports');
        require_once BASE_PATH . '/views/reports/index.php';
    }
}
?>
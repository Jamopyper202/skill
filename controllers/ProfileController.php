<?php
/**
 * ============================================================================
 * Profile Controller
 * ============================================================================
 * 
 * Handles user profile management: view, edit, upload picture,
 * and public profile viewing.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class ProfileController {
    /**
     * Profile model instance
     * @var Profile
     */
    private Profile $profileModel;

    /**
     * User model instance
     * @var User
     */
    private User $userModel;

    /**
     * Skill model instance
     * @var Skill
     */
    private Skill $skillModel;

    /**
     * Review model instance
     * @var Review
     */
    private Review $reviewModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->profileModel = new Profile();
        $this->userModel = new User();
        $this->skillModel = new Skill();
        $this->reviewModel = new Review();
    }

    /**
     * =========================================================================
     * VIEW OWN PROFILE
     * =========================================================================
     * 
     * Display the logged-in user's profile.
     */
    public function index(): void {
        $userId = getCurrentUserId();

        // Get profile data
        $profile = $this->profileModel->getByUserId($userId);

        if (!$profile) {
            flash('Profile not found.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Get user's offered skills
        $skills = $this->skillModel->getUserSkills($userId);

        // Get user's wanted skills
        $wantedSkills = $this->skillModel->getWantedSkills($userId);

        // Get profile statistics
        $stats = $this->profileModel->getStats($userId);

        // Get average rating
        $avgRating = $this->reviewModel->getAverageRating($userId);

        // Get rating breakdown
        $ratingBreakdown = $this->reviewModel->getRatingBreakdown($userId);

        // Get recent reviews
        $reviews = $this->reviewModel->getForUser($userId, 5);

        setPageTitle('My Profile');
        require_once BASE_PATH . '/views/profile/index.php';
    }

    /**
     * =========================================================================
     * EDIT PROFILE
     * =========================================================================
     * 
     * Display and handle profile edit form.
     */
    public function edit(): void {
        $userId = getCurrentUserId();

        // Get current profile
        $profile = $this->profileModel->getByUserId($userId);

        if (!$profile) {
            flash('Profile not found.', 'danger');
            redirect(url('Profile', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $data = sanitizeArray($_POST);

            // Validate
            if (!empty($data['phone']) && !preg_match('/^[0-9\s\-\+\(\)]+$/', $data['phone'])) {
                $errors[] = 'Please enter a valid phone number.';
            }

            if (!empty($data['linkedin_url']) && !filter_var($data['linkedin_url'], FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid LinkedIn URL.';
            }

            if (!empty($data['github_url']) && !filter_var($data['github_url'], FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid GitHub URL.';
            }

            if (!empty($data['website_url']) && !filter_var($data['website_url'], FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid website URL.';
            }

            if (empty($errors)) {
                // Prepare update data
                $updateData = [
                    'bio'              => $data['bio'] ?? '',
                    'location'         => $data['location'] ?? '',
                    'phone'            => $data['phone'] ?? '',
                    'experience_level' => $data['experience_level'] ?? 'Beginner',
                    'availability'     => $data['availability'] ?? 'Flexible',
                    'linkedin_url'     => $data['linkedin_url'] ?? '',
                    'github_url'       => $data['github_url'] ?? '',
                    'website_url'      => $data['website_url'] ?? ''
                ];

                if ($this->profileModel->update($userId, $updateData)) {
                    // Update session name if changed
                    if (!empty($data['full_name'])) {
                        $this->userModel->update($userId, ['full_name' => $data['full_name']]);
                        $_SESSION['user_name'] = $data['full_name'];
                    }

                    flash('Profile updated successfully.', 'success');
                    redirect(url('Profile', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to update profile. Please try again.';
                }
            }

            storeOldInput($data);
        }

        setPageTitle('Edit Profile');
        require_once BASE_PATH . '/views/profile/edit.php';
    }

    /**
     * =========================================================================
     * UPLOAD PROFILE PICTURE
     * =========================================================================
     * 
     * Handle profile picture upload.
     */
    public function uploadPicture(): void {
        $userId = getCurrentUserId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['profile_picture'])) {
            flash('No file uploaded.', 'warning');
            redirect(url('Profile', 'edit'));
            return;
        }

        $file = $_FILES['profile_picture'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            flash('File upload failed. Please try again.', 'danger');
            redirect(url('Profile', 'edit'));
            return;
        }

        // Upload file
        $filename = $this->profileModel->uploadProfilePicture($file);

        if ($filename) {
            // Delete old picture
            $profile = $this->profileModel->getByUserId($userId);
            if ($profile && !empty($profile['profile_picture'])) {
                $this->profileModel->deleteOldPicture($profile['profile_picture']);
            }

            // Update database
            if ($this->profileModel->updateProfilePicture($userId, $filename)) {
                // Update session
                $_SESSION['user_picture'] = $filename;
                flash('Profile picture updated successfully.', 'success');
            } else {
                // Delete uploaded file if DB update fails
                if (file_exists(UPLOAD_PATH . '/' . $filename)) {
                    unlink(UPLOAD_PATH . '/' . $filename);
                }
                flash('Failed to update profile picture.', 'danger');
            }
        } else {
            flash('Invalid file. Please upload a valid image (JPG, PNG, GIF) under 2MB.', 'danger');
        }

        redirect(url('Profile', 'edit'));
    }

    /**
     * =========================================================================
     * VIEW PUBLIC PROFILE
     * =========================================================================
     * 
     * Display another user's public profile.
     */
    public function view(): void {
        $userId = (int) ($_GET['id'] ?? 0);

        if ($userId === 0) {
            flash('Invalid user ID.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Don't allow viewing own profile through this method
        if ($userId === getCurrentUserId()) {
            redirect(url('Profile', 'index'));
            return;
        }

        // Get user data
        $profile = $this->profileModel->getByUserId($userId);

        if (!$profile || !$profile['is_active']) {
            flash('User not found or account is inactive.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Get user's offered skills
        $skills = $this->skillModel->getUserSkillsPublic($userId);

        // Get user's wanted skills
        $wantedSkills = $this->skillModel->getWantedSkillsPublic($userId);

        // Get average rating
        $avgRating = $this->reviewModel->getAverageRating($userId);

        // Get rating breakdown
        $ratingBreakdown = $this->reviewModel->getRatingBreakdown($userId);

        // Get reviews
        $reviews = $this->reviewModel->getForUser($userId, 10);

        // Get total review count
        $reviewCount = $this->reviewModel->countForUser($userId);

        setPageTitle($profile['full_name'] . "'s Profile");
        require_once BASE_PATH . '/views/profile/view.php';
    }
}
?>
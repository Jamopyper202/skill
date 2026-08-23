<?php

/**
 * ============================================================================
 * Portfolio Controller
 * ============================================================================
 * 
 * Handles user portfolio management: add, edit, delete, view items.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class PortfolioController
{
    /**
     * Portfolio model instance
     * @var Portfolio
     */
    private Portfolio $portfolioModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->portfolioModel = new Portfolio();
    }

    /**
     * =========================================================================
     * LIST PORTFOLIO ITEMS
     * =========================================================================
     * 
     * Display all portfolio items for the logged-in user.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        // Get user's portfolio items
        $items = $this->portfolioModel->getByUserId($userId);

        setPageTitle('My Portfolio');
        require_once BASE_PATH . '/views/portfolio/index.php';
    }

    /**
     * =========================================================================
     * ADD PORTFOLIO ITEM
     * =========================================================================
     * 
     * Handle form to add a new portfolio item.
     */
    public function add(): void
    {
        $userId = getCurrentUserId();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $projectUrl = trim($_POST['project_url'] ?? '');

            // Validation
            if (!isRequired($title)) {
                $errors[] = 'Project title is required.';
            } elseif (strlen($title) < 3) {
                $errors[] = 'Title must be at least 3 characters.';
            }

            if (!empty($projectUrl) && !filter_var($projectUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid project URL.';
            }

            // Handle image upload
            $imageFilename = '';
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageFilename = $this->portfolioModel->uploadImage($_FILES['image']);
                if (!$imageFilename) {
                    $errors[] = 'Failed to upload image. Please use JPG, PNG, or GIF under 2MB.';
                }
            }

            if (empty($errors)) {
                $portfolioId = $this->portfolioModel->create(
                    $userId,
                    $title,
                    $description,
                    $projectUrl,
                    $imageFilename
                );

                if ($portfolioId) {
                    flash('Portfolio item added successfully.', 'success');
                    redirect(url('Portfolio', 'index'));
                    return;
                } else {
                    // Delete uploaded image if DB insert failed
                    if (!empty($imageFilename)) {
                        $this->portfolioModel->deleteImage($imageFilename);
                    }
                    $errors[] = 'Failed to add portfolio item. Please try again.';
                }
            }

            storeOldInput($_POST);
        }

        setPageTitle('Add Portfolio Item');
        require_once BASE_PATH . '/views/portfolio/create.php';
    }

    /**
     * =========================================================================
     * EDIT PORTFOLIO ITEM
     * =========================================================================
     * 
     * Handle form to edit an existing portfolio item.
     */
    public function edit(): void
    {
        $userId = getCurrentUserId();
        $portfolioId = (int) ($_GET['id'] ?? 0);

        // Get the portfolio item
        $item = $this->portfolioModel->getById($portfolioId, $userId);

        if (!$item) {
            flash('Portfolio item not found.', 'danger');
            redirect(url('Portfolio', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $projectUrl = trim($_POST['project_url'] ?? '');
            $removeImage = isset($_POST['remove_image']);

            // Validation
            if (!isRequired($title)) {
                $errors[] = 'Project title is required.';
            }

            if (!empty($projectUrl) && !filter_var($projectUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid project URL.';
            }

            // Handle image
            $imageFilename = $item['image'];

            if ($removeImage) {
                // Delete old image
                if (!empty($imageFilename)) {
                    $this->portfolioModel->deleteImage($imageFilename);
                }
                $imageFilename = '';
            }

            // Upload new image if provided
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImage = $this->portfolioModel->uploadImage($_FILES['image']);
                if ($newImage) {
                    // Delete old image if exists
                    if (!empty($imageFilename) && $imageFilename !== $newImage) {
                        $this->portfolioModel->deleteImage($imageFilename);
                    }
                    $imageFilename = $newImage;
                } else {
                    $errors[] = 'Failed to upload image. Please use JPG, PNG, or GIF under 2MB.';
                }
            }

            if (empty($errors)) {
                if ($this->portfolioModel->update($portfolioId, $userId, $title, $description, $projectUrl, $imageFilename)) {
                    flash('Portfolio item updated successfully.', 'success');
                    redirect(url('Portfolio', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to update portfolio item. Please try again.';
                }
            }
        }

        setPageTitle('Edit Portfolio Item');
        require_once BASE_PATH . '/views/portfolio/edit.php';
    }

    /**
     * =========================================================================
     * DELETE PORTFOLIO ITEM
     * =========================================================================
     * 
     * Delete a portfolio item.
     */
    public function delete(): void
    {
        $userId = getCurrentUserId();
        $portfolioId = (int) ($_GET['id'] ?? 0);

        if ($this->portfolioModel->delete($portfolioId, $userId)) {
            flash('Portfolio item deleted successfully.', 'success');
        } else {
            flash('Failed to delete portfolio item.', 'danger');
        }

        redirect(url('Portfolio', 'index'));
    }

    /**
     * =========================================================================
     * VIEW PUBLIC PORTFOLIO
     * =========================================================================
     * 
     * View another user's portfolio.
     */
    public function view(): void
    {
        $userId = (int) ($_GET['user_id'] ?? 0);

        if ($userId <= 0) {
            flash('Invalid user.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Get portfolio items
        $portfolioItems = $this->portfolioModel->getByUserId($userId);

        // Get user information
        $userModel = new User();
        $portfolioUser = $userModel->findById($userId);

        if (!$portfolioUser || empty($portfolioUser['is_active'])) {
            flash('User not found.', 'danger');
            redirect(url('Dashboard', 'index'));
            return;
        }

        // Get profile information
        $profileModel = new Profile();
        $portfolioProfile = $profileModel->getByUserId($userId);

        setPageTitle(
            ($portfolioUser['full_name'] ?? 'User') . "'s Portfolio"
        );

        require_once BASE_PATH . '/views/portfolio/view.php';
    }
}

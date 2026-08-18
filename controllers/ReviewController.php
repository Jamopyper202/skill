<?php

/**
 * ============================================================================
 * Review Controller
 * ============================================================================
 * 
 * Handles user reviews and ratings: create, view, update, delete.
 * Users can only review after a completed exchange.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class ReviewController
{
    /**
     * Review model instance
     * @var Review
     */
    private Review $reviewModel;

    /**
     * Exchange model instance
     * @var Exchange
     */
    private Exchange $exchangeModel;

    /**
     * User model instance
     * @var User
     */
    private User $userModel;

    /**
     * Notification model instance
     * @var Notification
     */
    private Notification $notificationModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->exchangeModel = new Exchange();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * =========================================================================
     * LIST MY REVIEWS
     * =========================================================================
     * 
     * Display reviews received by the logged-in user.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get reviews received
        $reviews = $this->reviewModel->getForUser($userId, $limit, $offset);

        // Get total count
        $total = $this->reviewModel->countForUser($userId);
        $totalPages = (int) ceil($total / $limit);

        // Get average rating
        $avgRating = $this->reviewModel->getAverageRating($userId);

        // Get rating breakdown
        $ratingBreakdown = $this->reviewModel->getRatingBreakdown($userId);

        setPageTitle('My Reviews');
        require_once BASE_PATH . '/views/reviews/index.php';
    }

    /**
     * =========================================================================
     * CREATE REVIEW
     * =========================================================================
     * 
     * Display and handle review creation form.
     */
    public function create(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) (
            $_GET['exchange_id']
            ?? $_POST['exchange_id']
            ?? 0
        );

        if ($exchangeId === 0) {
            flash('Invalid exchange.', 'danger');
            redirect(url('Exchange', 'history'));
            return;
        }

        // Check if user can review this exchange
        $canReview = $this->reviewModel->canReview($exchangeId, $userId);

        if (!$canReview['can_review']) {
            flash($canReview['reason'], 'warning');
            redirect(url('Exchange', 'history'));
            return;
        }

        $revieweeId = $canReview['reviewee_id'];

        // Get exchange details
        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);

        // Get reviewee info
        $reviewee = $this->userModel->findById($revieweeId);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = (int) ($_POST['rating'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            // Validation
            if ($rating < 1 || $rating > 5) {
                $errors[] = 'Please select a rating from 1 to 5 stars.';
            }

            if (!isRequired($comment)) {
                $errors[] = 'Please write a review comment.';
            } elseif (strlen($comment) < 10) {
                $errors[] = 'Comment must be at least 10 characters.';
            }

            if (empty($errors)) {
                $reviewId = $this->reviewModel->create($exchangeId, $userId, $revieweeId, $rating, $comment);

                if ($reviewId) {
                    // Notify reviewee
                    $reviewer = $this->userModel->findById($userId);
                    if ($reviewer) {
                        $this->notificationModel->notifyNewReview(
                            $revieweeId,
                            $reviewId,
                            $reviewer['full_name']
                        );
                    }

                    flash('Review submitted successfully!', 'success');
                    redirect(url('Exchange', 'history'));
                    return;
                } else {
                    $errors[] = 'Failed to submit review. Please try again.';
                }
            }

            storeOldInput(['rating' => $rating, 'comment' => $comment]);
        }

        setPageTitle('Write a Review');
        require_once BASE_PATH . '/views/reviews/create.php';
    }

    /**
     * =========================================================================
     * VIEW REVIEW
     * =========================================================================
     * 
     * View a specific review.
     */
    public function view(): void
    {
        $reviewId = (int) ($_GET['id'] ?? 0);

        $review = $this->reviewModel->getById($reviewId);

        if (!$review) {
            flash('Review not found.', 'danger');
            redirect(url('Review', 'index'));
            return;
        }

        setPageTitle('Review Details');
        require_once BASE_PATH . '/views/reviews/view.php';
    }

    /**
     * =========================================================================
     * EDIT REVIEW
     * =========================================================================
     * 
     * Edit an existing review (within 24 hours).
     */
    public function edit(): void
    {
        $userId = getCurrentUserId();
        $reviewId = (int) ($_GET['id'] ?? 0);

        $review = $this->reviewModel->getById($reviewId);

        if (!$review || (int)$review['reviewer_id'] !== $userId) {
            flash('Review not found or you do not have permission.', 'danger');
            redirect(url('Review', 'index'));
            return;
        }

        // Check if within 24 hours
        $reviewTime = strtotime($review['created_at']);
        if (time() - $reviewTime > 86400) {
            flash('Reviews can only be edited within 24 hours of creation.', 'warning');
            redirect(url('Review', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = (int) ($_POST['rating'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');

            // Validation
            if ($rating < 1 || $rating > 5) {
                $errors[] = 'Please select a rating from 1 to 5 stars.';
            }

            if (!isRequired($comment)) {
                $errors[] = 'Please write a review comment.';
            }

            if (empty($errors)) {
                if ($this->reviewModel->update($reviewId, $userId, $rating, $comment)) {
                    flash('Review updated successfully.', 'success');
                    redirect(url('Review', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to update review. It may be older than 24 hours.';
                }
            }
        }

        setPageTitle('Edit Review');
        require_once BASE_PATH . '/views/reviews/edit.php';
    }

    /**
     * =========================================================================
     * DELETE REVIEW
     * =========================================================================
     * 
     * Delete a review written by the current user.
     */
    public function delete(): void
    {
        $userId = getCurrentUserId();
        $reviewId = (int) ($_GET['id'] ?? 0);

        if ($this->reviewModel->delete($reviewId, $userId)) {
            flash('Review deleted successfully.', 'success');
        } else {
            flash('Failed to delete review.', 'danger');
        }

        redirect(url('Review', 'index'));
    }
}

<?php

/**
 * ============================================================================
 * Dashboard Controller
 * ============================================================================
 * 
 * Handles user and admin dashboard views.
 * Displays statistics, recent activity, matches, and notifications.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class DashboardController
{
    /**
     * Profile model instance
     * @var Profile
     */
    private Profile $profileModel;

    /**
     * Skill model instance
     * @var Skill
     */
    private Skill $skillModel;

    /**
     * Match model instance
     * @var MatchModel
     */
    private MatchModel $matchModel;

    /**
     * Exchange model instance
     * @var Exchange
     */
    private Exchange $exchangeModel;

    /**
     * Message model instance
     * @var Message
     */
    private Message $messageModel;

    /**
     * Notification model instance
     * @var Notification
     */
    private Notification $notificationModel;

    /**
     * Review model instance
     * @var Review
     */
    private Review $reviewModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profileModel = new Profile();
        $this->skillModel = new Skill();
        $this->matchModel = new MatchModel();
        $this->exchangeModel = new Exchange();
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
        $this->reviewModel = new Review();
    }

    /**
     * =========================================================================
     * USER DASHBOARD
     * =========================================================================
     * 
     * Main dashboard for logged-in users.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        // Get user profile
        $profile = $this->profileModel->getByUserId($userId);

        // Get profile statistics
        $stats = $this->profileModel->getStats($userId);


        // Get top matches
        $topMatches = $this->matchModel->getTopMatches($userId, MATCH_THRESHOLD, 5);

        // Get pending exchange requests
        $pendingExchanges = $this->exchangeModel->getPendingReceived($userId, 5);

        // Get recent messages
        $recentMessages = $this->messageModel->getRecentConversations($userId);

        // Get unread message count
        $stats['unread_messages'] = $this->messageModel->countUnread($userId);

        // Get unread notification count
        $unreadNotifications = $this->notificationModel->countUnread($userId);


        // Get recent notifications
        $recentNotifications = $this->notificationModel->getRecent($userId, 5);

        // Get exchange statistics
        $exchangeStats = $this->exchangeModel->getStats($userId);

        // Get recent reviews
        $recentReviews = $this->reviewModel->getForUser($userId, 3);

        // Get average rating
        $avgRating = $this->reviewModel->getAverageRating($userId);


        setPageTitle('Dashboard');
        require_once BASE_PATH . '/views/dashboard.php';
    }

    /**
     * =========================================================================
     * HOME PAGE (GUEST)
     * =========================================================================
     * 
     * Landing page for non-logged-in users.
     */
    public function home(): void
    {
        // Get recent users
        $recentUsers = $this->profileModel->getRecent(6);

        // Get skill categories
        $categories = $this->skillModel->getAllCategories();

        // Get total statistics for display
        $db = Database::getConnection();

        $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user' AND is_active = 1");
        $totalUsers = (int) $stmt->fetch()['total'];

        $stmt = $db->query("SELECT COUNT(*) as total FROM user_skills WHERE is_active = 1");
        $totalSkills = (int) $stmt->fetch()['total'];

        $stmt = $db->query("SELECT COUNT(*) as total FROM exchange_requests WHERE status = 'completed'");
        $totalExchanges = (int) $stmt->fetch()['total'];

        $stmt = $db->query("SELECT COUNT(*) as total FROM matches WHERE status = 'accepted'");
        $totalMatches = (int) $stmt->fetch()['total'];

        setPageTitle('Welcome');
        require_once BASE_PATH . '/views/home.php';
    }
}

<?php

/**
 * ============================================================================
 * Exchange Controller
 * ============================================================================
 * 
 * Handles skill exchange requests: send, accept, reject, complete,
 * view history, and manage exchange status.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class ExchangeController
{
    /**
     * Exchange model instance
     * @var Exchange
     */
    private Exchange $exchangeModel;

    /**
     * Match model instance
     * @var MatchModel
     */
    private MatchModel $matchModel;

    /**
     * Skill model instance
     * @var Skill
     */
    private Skill $skillModel;

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
        $this->exchangeModel = new Exchange();
        $this->matchModel = new MatchModel();
        $this->skillModel = new Skill();
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }

    /**
     * =========================================================================
     * LIST EXCHANGES
     * =========================================================================
     * 
     * Display all exchanges for the logged-in user.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        $status = $_GET['status'] ?? '';
        $page = (int) ($_GET['page'] ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get user's exchanges
        $exchanges = $this->exchangeModel->getUserExchanges($userId, $status, $limit, $offset);

        // Get total count
        $total = $this->exchangeModel->countUserExchanges($userId, $status);
        $totalPages = ceil($total / $limit);

        // Get exchange statistics
        $stats = $this->exchangeModel->getStats($userId);

        setPageTitle('My Exchanges');
        require_once BASE_PATH . '/views/exchanges/index.php';
    }

    /**
     * =========================================================================
     * SEND EXCHANGE REQUEST
     * =========================================================================
     * 
     * Create a new exchange request based on a match.
     */
    public function send(): void
    {
        $userId = getCurrentUserId();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matchId = (int) ($_POST['match_id'] ?? 0);
            $receiverId = (int) ($_POST['receiver_id'] ?? 0);
            $offeredSkillId = (int) ($_POST['offered_skill_id'] ?? 0);
            $requestedSkillId = (int) ($_POST['requested_skill_id'] ?? 0);
            $message = trim($_POST['message'] ?? '');

            // Validation
            if ($matchId === 0) {
                $errors[] = 'Invalid match.';
            }

            if ($receiverId === 0) {
                $errors[] = 'Invalid receiver.';
            }

            if ($offeredSkillId === 0) {
                $errors[] = 'Please select a skill you want to teach.';
            }

            if ($requestedSkillId === 0) {
                $errors[] = 'Please select a skill you want to learn.';
            }

            if ($offeredSkillId === $requestedSkillId) {
                $errors[] = 'You cannot exchange the same skill.';
            }

            // Check if users already have an active exchange
            if (empty($errors) && $this->exchangeModel->hasActiveExchange($userId, $receiverId)) {
                $errors[] = 'You already have an active exchange with this user.';
            }

            if (empty($errors)) {
                $exchangeId = $this->exchangeModel->create(
                    $matchId,
                    $userId,
                    $receiverId,
                    $offeredSkillId,
                    $requestedSkillId,
                    $message
                );

                if ($exchangeId) {
                    // Get receiver info for notification
                    $receiver = $this->userModel->findById($receiverId);
                    $requester = $this->userModel->findById($userId);

                    // Notify receiver
                    if ($receiver && $requester) {
                        $this->notificationModel->notifyExchangeRequest(
                            $receiverId,
                            $exchangeId,
                            $requester['full_name']
                        );
                    }

                    flash('Exchange request sent successfully!', 'success');
                    redirect(url('Exchange', 'index'));
                    return;
                } else {
                    $errors[] = 'Failed to send exchange request. Please try again.';
                }
            }

            storeOldInput($_POST);
        }

        // Get match details if provided
        $matchId = (int) ($_GET['match_id'] ?? 0);
        $match = null;
        $receiverId = (int) ($_GET['user_id'] ?? 0);

        if ($matchId > 0) {
            $match = $this->matchModel->getById($matchId);
            if ($match) {
                $receiverId = ($match['user_id_1'] == $userId) ? $match['user_id_2'] : $match['user_id_1'];
            }
        }

        // Get my offered skills
        $mySkills = $this->skillModel->getUserSkills($userId);

        // Get receiver's offered skills (what I can request)
        $receiverSkills = [];
        if ($receiverId > 0) {
            $receiverSkills = $this->skillModel->getUserSkillsPublic($receiverId);
        }

        // Get receiver info
        $receiver = null;
        if ($receiverId > 0) {
            $receiver = $this->userModel->findById($receiverId);
        }

        setPageTitle('Send Exchange Request');
        require_once BASE_PATH . '/views/exchanges/create.php';
    }

    /**
     * =========================================================================
     * VIEW EXCHANGE
     * =========================================================================
     * 
     * View details of a specific exchange request.
     */
    // public function view(): void
    // {
    //     $userId = getCurrentUserId();

    //     $exchangeId = (int) ($_GET['id'] ?? 0);

    //     $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);



    //     if (!$exchange) {
    //         flash('Exchange not found.', 'danger');
    //         redirect(url('Exchange', 'index'));
    //         return;
    //     }

    //     // Get messages related to this exchange
    //     $messageModel = new Message();
    //     $messages = $messageModel->getByExchange($exchangeId);

    //     setPageTitle('Exchange Details');
    //     require_once BASE_PATH . '/views/exchanges/view.php';
    // }
    public function view(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        if ($exchangeId <= 0) {
            flash('Invalid exchange ID.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        $exchange = $this->exchangeModel->getForUser(
            $exchangeId,
            $userId
        );

        if (!$exchange) {
            flash('Exchange not found.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        $messageModel = new Message();
        $messages = $messageModel->getByExchange($exchangeId);

        setPageTitle('Exchange Details');

        require_once BASE_PATH . '/views/exchanges/view.php';
    }

    /**
     * =========================================================================
     * ACCEPT EXCHANGE
     * =========================================================================
     * 
     * Accept a pending exchange request.
     */
    public function accept(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);

        if (!$exchange || $exchange['receiver_id'] != $userId) {
            flash('Exchange not found or you do not have permission.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        if ($exchange['status'] !== 'pending') {
            flash('This exchange is no longer pending.', 'warning');
            redirect(url('Exchange', 'view', ['id' => $exchangeId]));
            return;
        }

        if ($this->exchangeModel->accept($exchangeId)) {
            // Notify requester
            $requester = $this->userModel->findById($exchange['requester_id']);
            $receiver = $this->userModel->findById($userId);

            if ($requester && $receiver) {
                $this->notificationModel->notifyExchangeAccepted(
                    $exchange['requester_id'],
                    $exchangeId,
                    $receiver['full_name']
                );
            }

            flash('Exchange request accepted! You can now start the skill exchange.', 'success');
        } else {
            flash('Failed to accept exchange request.', 'danger');
        }

        redirect(url('Exchange', 'view', ['id' => $exchangeId]));
    }

    /**
     * =========================================================================
     * REJECT EXCHANGE
     * =========================================================================
     * 
     * Decline a pending exchange request.
     */
    public function reject(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);

        if (!$exchange || $exchange['receiver_id'] != $userId) {
            flash('Exchange not found or you do not have permission.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        if ($exchange['status'] !== 'pending') {
            flash('This exchange is no longer pending.', 'warning');
            redirect(url('Exchange', 'view', ['id' => $exchangeId]));
            return;
        }

        if ($this->exchangeModel->reject($exchangeId)) {
            // Notify requester
            $requester = $this->userModel->findById($exchange['requester_id']);
            $receiver = $this->userModel->findById($userId);

            if ($requester && $receiver) {
                $this->notificationModel->notifyExchangeDeclined(
                    $exchange['requester_id'],
                    $exchangeId,
                    $receiver['full_name']
                );
            }

            flash('Exchange request declined.', 'info');
        } else {
            flash('Failed to decline exchange request.', 'danger');
        }

        redirect(url('Exchange', 'index'));
    }

    /**
     * =========================================================================
     * START EXCHANGE
     * =========================================================================
     * 
     * Mark an accepted exchange as in progress.
     */
    public function start(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);

        if (!$exchange) {
            flash('Exchange not found.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        if ($exchange['status'] !== 'accepted') {
            flash('Exchange must be accepted before starting.', 'warning');
            redirect(url('Exchange', 'view', ['id' => $exchangeId]));
            return;
        }

        if ($this->exchangeModel->start($exchangeId)) {
            flash('Exchange started! Begin your skill sharing journey.', 'success');
        } else {
            flash('Failed to start exchange.', 'danger');
        }

        redirect(url('Exchange', 'view', ['id' => $exchangeId]));
    }

    /**
     * =========================================================================
     * COMPLETE EXCHANGE
     * =========================================================================
     * 
     * Mark an in-progress exchange as completed.
     */
    public function complete(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);

        if (!$exchange) {
            flash('Exchange not found.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        if ($exchange['status'] !== 'in_progress') {
            flash('Exchange must be in progress before completing.', 'warning');
            redirect(url('Exchange', 'view', ['id' => $exchangeId]));
            return;
        }

        if ($this->exchangeModel->complete($exchangeId)) {
            // Notify both users
            $requester = $this->userModel->findById($exchange['requester_id']);
            $receiver = $this->userModel->findById($exchange['receiver_id']);

            if ($requester && $receiver) {
                $this->notificationModel->notifyExchangeCompleted(
                    $exchange['requester_id'],
                    $exchangeId,
                    $receiver['full_name']
                );

                $this->notificationModel->notifyExchangeCompleted(
                    $exchange['receiver_id'],
                    $exchangeId,
                    $requester['full_name']
                );
            }

            flash('Exchange completed! You can now leave a review.', 'success');
            redirect(url('Exchange', 'view', ['id' => $exchangeId]));
            return;
        } else {
            flash('Failed to complete exchange.', 'danger');
        }

        redirect(url('Exchange', 'view', ['id' => $exchangeId]));
    }

    /**
     * =========================================================================
     * CANCEL EXCHANGE
     * =========================================================================
     * 
     * Cancel an active exchange.
     */
    public function cancel(): void
    {
        $userId = getCurrentUserId();
        $exchangeId = (int) ($_GET['id'] ?? 0);

        $exchange = $this->exchangeModel->getForUser($exchangeId, $userId);


        if (!$exchange) {
            flash('Exchange not found.', 'danger');
            redirect(url('Exchange', 'index'));
            return;
        }

        if ($this->exchangeModel->cancel($exchangeId)) {
            flash('Exchange cancelled.', 'info');
        } else {
            flash('Failed to cancel exchange.', 'danger');
        }

        redirect(url('Exchange', 'index'));
    }

    /**
     * =========================================================================
     * EXCHANGE HISTORY
     * =========================================================================
     * 
     * View completed exchange history.
     */
    public function history(): void
    {
        $userId = getCurrentUserId();

        $exchanges = $this->exchangeModel->getHistory($userId, 20);

        setPageTitle('Exchange History');
        require_once BASE_PATH . '/views/exchanges/history.php';
    }
}

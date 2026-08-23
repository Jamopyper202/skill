<?php

/**
 * ============================================================================
 * Message Controller
 * ============================================================================
 * 
 * Handles messaging between users: send, view conversations,
 * list conversations, and delete messages.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class MessageController
{
    /**
     * Message model instance
     * @var Message
     */
    private Message $messageModel;

    /**
     * User model instance
     * @var User
     */
    private User $userModel;

    /**
     * Profile model instance
     * @var Profile
     */
    private Profile $profileModel;

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
        $this->messageModel = new Message();
        $this->userModel = new User();
        $this->profileModel = new Profile();
        $this->notificationModel = new Notification();
    }

    /**
     * =========================================================================
     * LIST CONVERSATIONS
     * =========================================================================
     * 
     * Display all recent conversations for the logged-in user.
     */
    public function index(): void
    {
        $userId = getCurrentUserId();

        // Get recent conversations
        $conversations = $this->messageModel->getRecentConversations($userId);

        // Mark all as read when viewing inbox
        // (Individual conversation unread counts are handled separately)

        setPageTitle('Messages');
        require_once BASE_PATH . '/views/messages/index.php';
    }

    /**
     * =========================================================================
     * VIEW CONVERSATION
     * =========================================================================
     * 
     * Display chat history with a specific user.
     */
    public function conversation(): void
    {
        $userId = getCurrentUserId();
        $otherUserId = (int) ($_GET['user_id'] ?? 0);

        if ($otherUserId === 0 || $otherUserId === $userId) {
            flash('Invalid user.', 'danger');
            redirect(url('Message', 'index'));
            return;
        }

        // Get other user details
        $otherUser = $this->userModel->findById($otherUserId);

        if (!$otherUser || !$otherUser['is_active']) {
            flash('User not found or account is inactive.', 'danger');
            redirect(url('Message', 'index'));
            return;
        }

        // Get conversation messages
        $messages = $this->messageModel->getConversation($userId, $otherUserId, 100);

        // Mark messages from other user as read
        $this->messageModel->markAsRead($otherUserId, $userId);

        $conversations = $this->messageModel->getRecentConversations($userId);


        // Get last message ID for AJAX polling
        $lastMessageId = 0;

        if (!empty($messages)) {
            $lastMessage = end($messages);
            $lastMessageId = $lastMessage['id'];
        }

        // Variables for the view
        $conversationUser = $otherUser;
        $conversationUserId = $otherUserId;

        setPageTitle('Chat with ' . $otherUser['full_name']);

        require_once BASE_PATH . '/views/messages/conversation.php';
        
    }

    /**
     * =========================================================================
     * SEND MESSAGE
     * =========================================================================
     * 
     * Handle sending a new message.
     */
    public function send(): void
    {
        $userId = getCurrentUserId();
        $receiverId = 0;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receiverId = (int) ($_POST['receiver_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            $exchangeId = (int) ($_POST['exchange_id'] ?? 0);

            // Validation
            if ($receiverId === 0) {
                $errors[] = 'Invalid recipient.';
            } elseif ($receiverId === $userId) {
                $errors[] = 'You cannot message yourself.';
            }

            if (!isRequired($content)) {
                $errors[] = 'Message cannot be empty.';
            } elseif (strlen($content) > 2000) {
                $errors[] = 'Message is too long (max 2000 characters).';
            }

            if (empty($errors)) {
                $messageId = $this->messageModel->send($userId, $receiverId, $content, $exchangeId);

                if ($messageId) {
                    // Get sender info for notification
                    $sender = $this->userModel->findById($userId);

                    if ($sender) {
                        $this->notificationModel->notifyNewMessage(
                            $receiverId,
                            $userId,
                            $sender['full_name']
                        );
                    }

                    // If AJAX request, return JSON
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Message sent',
                            'message_id' => $messageId
                        ]);
                        exit;
                    }

                    flash('Message sent.', 'success');
                } else {
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
                        exit;
                    }
                    $errors[] = 'Failed to send message.';
                }
            }

            // Handle AJAX errors
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $errors[0]]);
                exit;
            }

            storeOldInput(['content' => $content]);
        }

        // redirect(url('Message', 'conversation', ['user_id' => $receiverId]));
        if ($receiverId > 0) {
            redirect(url('Message', 'conversation', [
                'user_id' => $receiverId
            ]));
        } else {
            redirect(url('Message', 'index'));
        }
    }

    /**
     * =========================================================================
     * DELETE MESSAGE
     * =========================================================================
     * 
     * Delete a specific message.
     */
    public function delete(): void
    {
        $userId = getCurrentUserId();
        $messageId = (int) ($_GET['id'] ?? 0);

        if ($this->messageModel->delete($messageId, $userId)) {
            flash('Message deleted.', 'success');
        } else {
            flash('Failed to delete message.', 'danger');
        }

        // Redirect back to conversation if user_id provided
        $otherUserId = (int) ($_GET['user_id'] ?? 0);
        if ($otherUserId > 0) {
            redirect(url('Message', 'conversation', ['user_id' => $otherUserId]));
        } else {
            redirect(url('Message', 'index'));
        }
    }

    /**
     * =========================================================================
     * GET NEW MESSAGES (AJAX)
     * =========================================================================
     * 
     * Poll for new messages in a conversation.
     */
    public function poll(): void
    {
        header('Content-Type: application/json');

        $userId = getCurrentUserId();
        $otherUserId = (int) ($_GET['user_id'] ?? 0);
        $lastMessageId = (int) ($_GET['last_id'] ?? 0);

        if ($otherUserId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user']);
            exit;
        }

        // Get new messages
        $newMessages = $this->messageModel->getNewMessages($userId, $otherUserId, $lastMessageId);

        // Mark as read
        if (!empty($newMessages)) {
            $this->messageModel->markAsRead($otherUserId, $userId);
        }

        // Format messages for JSON
        $formattedMessages = [];
        foreach ($newMessages as $msg) {
            $formattedMessages[] = [
                'id' => $msg['id'],
                'sender_id' => $msg['sender_id'],
                'content' => nl2br(e($msg['content'])),
                'created_at' => $msg['created_at'],
                'is_me' => $msg['sender_id'] == $userId
            ];
        }

        echo json_encode([
            'success' => true,
            'messages' => $formattedMessages,
            'count' => count($formattedMessages)
        ]);
        exit;
    }

    /**
     * =========================================================================
     * GET UNREAD COUNT (AJAX)
     * =========================================================================
     * 
     * Get total unread message count for navbar badge.
     */
    public function unreadCount(): void
    {
        header('Content-Type: application/json');

        $userId = getCurrentUserId();
        $count = $this->messageModel->countUnread($userId);

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        exit;
    }
}

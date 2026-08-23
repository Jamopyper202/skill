<?php

/**
 * ============================================================================
 * AJAX Message Handler
 * ============================================================================
 * 
 * Handles AJAX requests for messaging functionality:
 * - Send message
 * - Get new messages (polling)
 * - Get unread count
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Verify user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = getCurrentUserId();
$action = $_GET['action'] ?? '';

$messageModel = new Message();
$notificationModel = new Notification();
$userModel = new User();

switch ($action) {
    // =========================================================================
    // SEND MESSAGE
    // =========================================================================
    case 'send':
        $receiverId = (int) ($_POST['receiver_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $exchangeId = (int) ($_POST['exchange_id'] ?? 0);

        if ($receiverId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid recipient']);
            exit;
        }

        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
            exit;
        }

        if (strlen($content) > 2000) {
            echo json_encode(['success' => false, 'message' => 'Message too long']);
            exit;
        }

        $messageId = $messageModel->send($userId, $receiverId, $content, $exchangeId);

        if ($messageId) {
            // Send notification
            $sender = $userModel->findById($userId);
            if ($sender) {
                $notificationModel->notifyNewMessage($receiverId, $userId, $sender['full_name']);
            }
            $newMessage = $messageModel->getById($messageId);

            echo json_encode([
                'success' => true,
                'message' => 'Message sent',
                'message_id' => $messageId,
                'created_at' => $newMessage['created_at'] ?? null
            ]);

            // echo json_encode([
            //     'success' => true,
            //     'message' => 'Message sent',
            //     'message_id' => $messageId,
            //     'created_at' => date('g:i A')
            // ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        break;

    // =========================================================================
    // GET NEW MESSAGES (POLLING)
    // =========================================================================
    case 'poll':
        $otherUserId = (int) ($_GET['user_id'] ?? 0);
        $lastMessageId = (int) ($_GET['last_id'] ?? 0);

        if ($otherUserId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user']);
            exit;
        }

        $newMessages = $messageModel->getNewMessages($userId, $otherUserId, $lastMessageId);

        // Mark as read
        if (!empty($newMessages)) {
            $messageModel->markAsRead($otherUserId, $userId);
        }

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
        break;

    // =========================================================================
    // GET UNREAD COUNT
    // =========================================================================
    case 'unread_count':
        $count = $messageModel->countUnread($userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    // =========================================================================
    // GET UNREAD FROM USER
    // =========================================================================
    case 'unread_from':
        $otherUserId = (int) ($_GET['user_id'] ?? 0);
        $count = $messageModel->countUnreadFromUser($otherUserId, $userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    // =========================================================================
    // DEFAULT
    // =========================================================================
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

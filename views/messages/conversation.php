<?php
/**
 * Conversation / Chat Interface View
 * Real-time messaging with another user
 */

$title = 'Conversation with ' . ($otherUser['name'] ?? 'User');
$activeTab = 'messages';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid py-0" style="height: calc(100vh - 76px);">
    <div class="row g-0 h-100">
        <!-- Conversations Sidebar -->
        <div class="col-lg-3 col-md-4 border-end bg-white d-none d-md-block" style="height: 100%; overflow-y: auto;">
            <div class="p-3 border-bottom">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control bg-light border-start-0" 
                        id="conversationSearch" placeholder="Search...">
                </div>
            </div>
            <div class="list-group list-group-flush" id="conversationsList">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted small">No conversations</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): 
                        $isActive = ($otherUser['id'] ?? 0) == $conv['user_id'];
                    ?>
                    <a href="/messages/conversation/<?= $conv['user_id'] ?>" 
                       class="list-group-item list-group-item-action <?= $isActive ? 'active' : '' ?> py-3"
                       data-name="<?= e(strtolower($conv['name'])) ?>"
                       data-username="<?= e(strtolower($conv['username'])) ?>">
                        <div class="d-flex align-items-center">
                            <div class="position-relative">
                                <img src="<?= getAvatarUrl($conv['avatar']) ?>" 
                                     alt="<?= e($conv['name']) ?>" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <?php if ($conv['unread_count'] > 0 && !$isActive): ?>
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?= $conv['unread_count'] ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate small <?= $isActive ? '' : 'text-dark' ?>" style="max-width: 120px;">
                                        <?= e($conv['name']) ?>
                                    </h6>
                                    <small class="<?= $isActive ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.7rem;">
                                        <?= timeAgo($conv['last_message_time']) ?>
                                    </small>
                                </div>
                                <p class="mb-0 text-truncate small <?= $isActive ? 'text-white-50' : ($conv['unread_count'] > 0 ? 'fw-bold text-dark' : 'text-muted') ?>" style="max-width: 160px;">
                                    <?= e(truncate($conv['last_message'], 30)) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-lg-9 col-md-8 bg-light d-flex flex-column" style="height: 100%;">
            <!-- Chat Header -->
            <div class="bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="/messages" class="btn btn-sm btn-link text-dark d-md-none me-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <img src="<?= getAvatarUrl($otherUser['avatar'] ?? null) ?>" 
                         alt="<?= e($otherUser['name'] ?? 'User') ?>" 
                         class="rounded-circle me-3" width="40" height="40">
                    <div>
                        <h5 class="mb-0 h6">
                            <a href="/profile/view/<?= $otherUser['id'] ?? 0 ?>" class="text-decoration-none text-dark">
                                <?= e($otherUser['name'] ?? 'Unknown User') ?>
                            </a>
                            <?php if (!empty($otherUser['is_online'])): ?>
                                <span class="badge bg-success rounded-pill ms-1" style="font-size: 0.5rem;">●</span>
                            <?php endif; ?>
                        </h5>
                        <small class="text-muted">
                            @<?= e($otherUser['username'] ?? 'unknown') ?>
                            <?php if (!empty($otherUser['average_rating'])): ?>
                                <span class="text-warning ms-1">
                                    <i class="fas fa-star fa-xs"></i> <?= number_format($otherUser['average_rating'], 1) ?>
                                </span>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/profile/view/<?= $otherUser['id'] ?? 0 ?>">
                            <i class="fas fa-user me-2"></i>View Profile
                        </a></li>
                        <li><a class="dropdown-item" href="/exchanges/create?user_id=<?= $otherUser['id'] ?? 0 ?>">
                            <i class="fas fa-exchange-alt me-2"></i>Start Exchange
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/messages/delete-conversation/<?= $otherUser['id'] ?? 0 ?>" method="POST" 
                                  onsubmit="return confirm('Delete this entire conversation? This cannot be undone.')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i>Delete Conversation
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-grow-1 overflow-auto p-3" id="messagesContainer" style="scroll-behavior: smooth;">
                <?php if (empty($messages)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-comment-dots fa-3x text-muted opacity-25"></i>
                        </div>
                        <p class="text-muted">No messages yet. Say hello!</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $lastDate = null;
                    foreach ($messages as $msg): 
                        $msgDate = date('Y-m-d', strtotime($msg['created_at']));
                        $showDate = $msgDate !== $lastDate;
                        $lastDate = $msgDate;
                        $isMine = ($msg['from_user_id'] ?? 0) == ($currentUser['id'] ?? 0);
                    ?>
                        <?php if ($showDate): ?>
                        <div class="text-center my-3">
                            <span class="badge bg-secondary bg-opacity-10 text-muted">
                                <?= formatDate($msg['created_at'], 'F j, Y') ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex mb-3 <?= $isMine ? 'justify-content-end' : 'justify-content-start' ?>" data-message-id="<?= $msg['id'] ?>">
                            <?php if (!$isMine): ?>
                            <img src="<?= getAvatarUrl($otherUser['avatar'] ?? null) ?>" 
                                 alt="" class="rounded-circle me-2 align-self-end" width="32" height="32" style="margin-bottom: 4px;">
                            <?php endif; ?>
                            
                            <div class="message-bubble <?= $isMine ? 'bg-primary text-white' : 'bg-white border' ?> rounded-3 px-3 py-2" 
                                 style="max-width: 70%;">
                                <p class="mb-1"><?= nl2br(e($msg['content'])) ?></p>
                                <div class="d-flex justify-content-end align-items-center gap-1">
                                    <small class="<?= $isMine ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.7rem;">
                                        <?= formatDate($msg['created_at'], 'g:i A') ?>
                                    </small>
                                    <?php if ($isMine): ?>
                                        <?php if ($msg['is_read']): ?>
                                            <i class="fas fa-check-double text-white-50" style="font-size: 0.7rem;"></i>
                                        <?php else: ?>
                                            <i class="fas fa-check text-white-50" style="font-size: 0.7rem;"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Typing Indicator -->
            <div id="typingIndicator" class="px-3 py-1 d-none">
                <small class="text-muted">
                    <i class="fas fa-ellipsis fa-beat"></i> <?= e($otherUser['first_name'] ?? 'User') ?> is typing...
                </small>
            </div>

            <!-- Message Input -->
            <div class="bg-white border-top p-3">
                <form id="messageForm" action="/messages/send" method="POST" class="d-flex align-items-end gap-2">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="to_user_id" value="<?= $otherUser['id'] ?? 0 ?>">
                    
                    <div class="flex-grow-1">
                        <textarea name="content" id="messageInput" rows="1" class="form-control" 
                            placeholder="Type a message..." required maxlength="2000"
                            style="resize: none; overflow-y: hidden;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-resize textarea
const messageInput = document.getElementById('messageInput');
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Scroll to bottom on load
const messagesContainer = document.getElementById('messagesContainer');
messagesContainer.scrollTop = messagesContainer.scrollHeight;

// Handle form submission via AJAX
let lastMessageId = <?= !empty($messages) ? max(array_column($messages, 'id')) : 0 ?>;
const otherUserId = <?= $otherUser['id'] ?? 0 ?>;

document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const content = messageInput.value.trim();
    if (!content) return;

    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;

    // Append message immediately (optimistic)
    appendMessage(content, true);
    messageInput.value = '';
    messageInput.style.height = 'auto';

    // Send via AJAX
    fetch('/ajax/messages.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(form))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            lastMessageId = data.message_id;
        } else {
            alert('Failed to send message: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
    })
    .finally(() => {
        sendBtn.disabled = false;
    });
});

function appendMessage(content, isMine) {
    const div = document.createElement('div');
    div.className = `d-flex mb-3 ${isMine ? 'justify-content-end' : 'justify-content-start'}`;
    div.innerHTML = `
        <div class="message-bubble ${isMine ? 'bg-primary text-white' : 'bg-white border'} rounded-3 px-3 py-2" style="max-width: 70%;">
            <p class="mb-1">${content.replace(/\\n/g, '<br>')}</p>
            <div class="d-flex justify-content-end align-items-center gap-1">
                <small class="${isMine ? 'text-white-50' : 'text-muted'}" style="font-size: 0.7rem;">Just now</small>
                ${isMine ? '<i class="fas fa-check text-white-50" style="font-size: 0.7rem;"></i>' : ''}
            </div>
        </div>
    `;
    messagesContainer.appendChild(div);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Poll for new messages
function pollMessages() {
    fetch(`/ajax/messages.php?action=poll&user_id=${otherUserId}&last_id=${lastMessageId}`)
        .then(r => r.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    appendMessage(msg.content, false);
                    lastMessageId = msg.id;
                });
            }
        })
        .catch(err => console.error('Poll error:', err))
        .finally(() => {
            setTimeout(pollMessages, 3000);
        });
}

// Start polling
setTimeout(pollMessages, 3000);

// Search conversations
document.getElementById('conversationSearch')?.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('#conversationsList .list-group-item').forEach(item => {
        const name = item.dataset.name;
        const username = item.dataset.username;
        item.style.display = (name.includes(query) || username.includes(query)) ? '' : 'none';
    });
});

// Enter to send (Shift+Enter for new line)
messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('messageForm').dispatchEvent(new Event('submit'));
    }
});
</script>

<style>
.message-bubble {
    word-wrap: break-word;
    line-height: 1.4;
}
#messagesContainer::-webkit-scrollbar {
    width: 6px;
}
#messagesContainer::-webkit-scrollbar-track {
    background: transparent;
}
#messagesContainer::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

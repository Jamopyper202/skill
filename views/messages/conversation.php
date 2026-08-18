<?php

/**
 * Conversation / Chat Interface View
 * Real-time messaging with another user
 */

$title = 'Conversation with ' . ($otherUser['full_name'] ?? 'User');
$activeTab = 'messages';

$currentUserId = getCurrentUserId();

$otherUserId = (int) ($otherUser['id'] ?? 0);
$otherUserName = $otherUser['full_name'] ?? 'Unknown User';
$otherUserAvatar = $otherUser['profile_picture'] ?? 'download.png';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid py-0" style="height: calc(100vh - 76px);">

    <div class="row g-0 h-100">

        <!-- Conversations Sidebar -->
        <!-- Conversations Sidebar -->
        <div
            class="col-lg-3 col-md-4 border-end bg-white d-none d-md-block"
            style="height: 100%; overflow-y: auto;">

            <div class="p-3 border-bottom">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots text-primary me-2"></i>
                        Messages
                    </h5>

                    <a
                        href="<?php echo url('Message', 'index'); ?>"
                        class="btn btn-sm btn-outline-secondary"
                        title="Back to Messages">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                </div>

                <div class="input-group">

                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        class="form-control bg-light border-start-0"
                        id="conversationSearch"
                        placeholder="Search...">

                </div>

            </div>


            <div
                class="list-group list-group-flush"
                id="conversationsList">

                <?php if (empty($conversations)): ?>

                    <div class="text-center py-5 px-3">

                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>

                        <p class="text-muted mb-3">
                            No conversations yet
                        </p>

                        <a
                            href="<?php echo url('Match', 'index'); ?>"
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Find People
                        </a>

                    </div>

                <?php else: ?>

                    <?php foreach ($conversations as $conv): ?>

                        <?php
                        $convUserId = (int) ($conv['other_user_id'] ?? 0);
                        $convName = $conv['other_user_name'] ?? 'Unknown User';
                        $convPicture = $conv['other_user_picture'] ?? 'download.png';
                        $convUnread = (int) ($conv['unread_count'] ?? 0);
                        $convMessage = $conv['content'] ?? '';
                        $convTime = $conv['created_at'] ?? '';

                        $isActive = ($convUserId === $otherUserId);
                        ?>

                        <a
                            href="<?php echo url('Message', 'conversation', [
                                        'user_id' => $convUserId
                                    ]); ?>"
                            class="list-group-item list-group-item-action py-3
                    <?php echo $isActive ? 'active' : ''; ?>"
                            data-name="<?php echo e(strtolower($convName)); ?>">

                            <div class="d-flex align-items-center">

                                <!-- Avatar -->
                                <div class="position-relative flex-shrink-0">

                                    <img
                                        src="<?php echo e(uploadUrl($convPicture)); ?>"
                                        alt="<?php echo e($convName); ?>"
                                        class="rounded-circle me-3"
                                        width="48"
                                        height="48"
                                        style="object-fit: cover;">

                                    <?php if ($convUnread > 0 && !$isActive): ?>

                                        <span
                                            class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                                            <?php echo $convUnread; ?>
                                        </span>

                                    <?php endif; ?>

                                </div>


                                <!-- Conversation Info -->
                                <div class="flex-grow-1 min-width-0">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <h6
                                            class="mb-0 text-truncate
                                    <?php echo $isActive ? '' : 'text-dark'; ?>"
                                            style="max-width: 130px;">
                                            <?php echo e($convName); ?>
                                        </h6>

                                        <?php if (!empty($convTime)): ?>

                                            <small
                                                class="<?php echo $isActive
                                                            ? 'text-white-50'
                                                            : 'text-muted'; ?>">
                                                <?php echo timeAgo($convTime); ?>
                                            </small>

                                        <?php endif; ?>

                                    </div>


                                    <p
                                        class="mb-0 text-truncate small
                                <?php echo $isActive
                                    ? 'text-white-50'
                                    : ($convUnread > 0
                                        ? 'fw-bold text-dark'
                                        : 'text-muted'); ?>"
                                        style="max-width: 180px;">

                                        <?php if (($conv['direction'] ?? '') === 'sent'): ?>

                                            <i class="bi bi-reply-fill me-1"></i>

                                        <?php endif; ?>

                                        <?php echo e(truncate($convMessage, 35)); ?>

                                    </p>

                                </div>

                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>
        <!-- <div
            class="col-lg-3 col-md-4 border-end bg-white d-none d-md-block"
            style="height: 100%; overflow-y: auto;">

            <div class="p-3 border-bottom">

                <div class="d-flex align-items-center justify-content-between mb-3">

                    <h5 class="mb-0">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Messages
                    </h5>

                    <a
                        href="<?php echo url('Message', 'index'); ?>"
                        class="btn btn-sm btn-outline-secondary">
                       <i class="bi bi-arrow-left"></i>
                    </a>

                </div>

                <div class="input-group">

                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        class="form-control bg-light border-start-0"
                        id="conversationSearch"
                        placeholder="Search...">

                </div>

            </div>


            <div
                class="list-group list-group-flush"
                id="conversationsList">

                <?php if (empty($conversations)): ?>

                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">
                            No conversations
                        </p>
                    </div>

                <?php else: ?>

                    <?php foreach ($conversations as $conv): ?>

                        <?php
                        $convUserId = (int) ($conv['other_user_id'] ?? 0);
                        $convName = $conv['other_user_name'] ?? 'Unknown User';
                        $convAvatar = $conv['other_user_picture'] ?? 'download.png';
                        $convUnread = (int) ($conv['unread_count'] ?? 0);
                        $convContent = $conv['content'] ?? '';
                        $convCreatedAt = $conv['created_at'] ?? '';

                        $isActive = $otherUserId === $convUserId;
                        ?>

                        <a
                            href="<?php echo url('Message', 'conversation', [
                                        'user_id' => $convUserId
                                    ]); ?>"
                            class="list-group-item list-group-item-action
                            <?php echo $isActive ? 'active' : ''; ?> py-3"
                            data-name="<?php echo e(strtolower($convName)); ?>">

                            <div class="d-flex align-items-center">

                                <div class="position-relative flex-shrink-0">

                                    <img
                                        src="<?php echo e(
                                                    uploadUrl($convAvatar)
                                                ); ?>"
                                        alt="<?php echo e($convName); ?>"
                                        class="rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        style="object-fit: cover;">

                                    <?php if ($convUnread > 0 && !$isActive): ?>

                                        <span
                                            class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                                            style="font-size: 0.6rem;">
                                            <?php echo $convUnread; ?>
                                        </span>

                                    <?php endif; ?>

                                </div>


                                <div class="flex-grow-1 min-width-0">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <h6
                                            class="mb-0 text-truncate small
                                            <?php echo $isActive ? '' : 'text-dark'; ?>"
                                            style="max-width: 120px;">
                                            <?php echo e($convName); ?>
                                        </h6>

                                        <?php if (!empty($convCreatedAt)): ?>

                                            <small
                                                class="<?php echo $isActive
                                                            ? 'text-white-50'
                                                            : 'text-muted'; ?>"
                                                style="font-size: 0.7rem;">
                                                <?php echo timeAgo($convCreatedAt); ?>
                                            </small>

                                        <?php endif; ?>

                                    </div>


                                    <p
                                        class="mb-0 text-truncate small
                                        <?php
                                        echo $isActive
                                            ? 'text-white-50'
                                            : ($convUnread > 0
                                                ? 'fw-bold text-dark'
                                                : 'text-muted');
                                        ?>"
                                        style="max-width: 160px;">
                                        <?php echo e(
                                            truncate($convContent, 30)
                                        ); ?>
                                    </p>

                                </div>

                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div> -->


        <!-- Chat Area -->
        <div
            class="col-lg-9 col-md-8 bg-light d-flex flex-column"
            style="height: 100%;">

            <!-- Chat Header -->
            <div
                class="bg-white border-bottom p-3 d-flex align-items-center justify-content-between">

                <div class="d-flex align-items-center">

                    <a
                        href="<?php echo url('Message', 'index'); ?>"
                        class="btn btn-sm btn-link text-dark d-md-none me-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>


                    <img
                        src="<?php echo e(
                                    uploadUrl($otherUserAvatar)
                                ); ?>"
                        alt="<?php echo e($otherUserName); ?>"
                        class="rounded-circle me-3"
                        width="40"
                        height="40"
                        style="object-fit: cover;">


                    <div>

                        <h5 class="mb-0 h6">

                            <a
                                href="<?php echo url('Profile', 'view', [
                                            'id' => $otherUserId
                                        ]); ?>"
                                class="text-decoration-none text-dark">
                                <?php echo e($otherUserName); ?>
                            </a>

                        </h5>

                        <small class="text-muted">
                            SkillSwap Partner
                        </small>

                    </div>

                </div>


                <div class="dropdown">

                    <button
                        class="btn btn-link text-dark"
                        type="button"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a
                                class="dropdown-item"
                                href="<?php echo url('Profile', 'view', [
                                            'id' => $otherUserId
                                        ]); ?>">
                                <i class="fas fa-user me-2"></i>
                                View Profile
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="<?php echo url('Exchange', 'create', [
                                            'user_id' => $otherUserId
                                        ]); ?>">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Start Exchange
                            </a>
                        </li>

                    </ul>

                </div>

            </div>


            <!-- Messages Area -->
            <div
                class="flex-grow-1 overflow-auto p-3"
                id="messagesContainer"
                style="scroll-behavior: smooth;">

                <?php if (empty($messages)): ?>

                    <div class="text-center py-5">

                        <div class="mb-3">
                            <i class="fas fa-comment-dots fa-3x text-muted opacity-25"></i>
                        </div>

                        <p class="text-muted">
                            No messages yet. Say hello!
                        </p>

                    </div>

                <?php else: ?>

                    <?php
                    $lastDate = null;
                    ?>

                    <?php foreach ($messages as $msg): ?>

                        <?php
                        $msgDate = date(
                            'Y-m-d',
                            strtotime($msg['created_at'])
                        );

                        $showDate = $msgDate !== $lastDate;
                        $lastDate = $msgDate;

                        $isMine =
                            (int) ($msg['sender_id'] ?? 0)
                            === $currentUserId;
                        ?>

                        <?php if ($showDate): ?>

                            <div class="text-center my-3">

                                <span class="badge bg-secondary bg-opacity-10 text-muted">

                                    <?php echo formatDate(
                                        $msg['created_at'],
                                        'F j, Y'
                                    ); ?>

                                </span>

                            </div>

                        <?php endif; ?>


                        <div
                            class="d-flex mb-3
                            <?php echo $isMine
                                ? 'justify-content-end'
                                : 'justify-content-start'; ?>"
                            data-message-id="<?php echo (int) $msg['id']; ?>">

                            <?php if (!$isMine): ?>

                                <img
                                    src="<?php echo e(
                                                uploadUrl($otherUserAvatar)
                                            ); ?>"
                                    alt="<?php echo e($otherUserName); ?>"
                                    class="rounded-circle me-2 align-self-end"
                                    width="32"
                                    height="32"
                                    style="margin-bottom: 4px; object-fit: cover;">

                            <?php endif; ?>


                            <div
                                class="message-bubble
                                <?php echo $isMine
                                    ? 'bg-primary text-white'
                                    : 'bg-white border'; ?>
                                rounded-3 px-3 py-2"
                                style="max-width: 70%;">

                                <p class="mb-1">
                                    <?php echo nl2br(
                                        e($msg['content'] ?? '')
                                    ); ?>
                                </p>


                                <div
                                    class="d-flex justify-content-end align-items-center gap-1">

                                    <small
                                        class="<?php echo $isMine
                                                    ? 'text-white-50'
                                                    : 'text-muted'; ?>"
                                        style="font-size: 0.7rem;">
                                        <?php echo formatDate(
                                            $msg['created_at'],
                                            'g:i A'
                                        ); ?>
                                    </small>


                                    <?php if ($isMine): ?>

                                        <?php if (!empty($msg['is_read'])): ?>

                                            <i
                                                class="fas fa-check-double text-white-50"
                                                style="font-size: 0.7rem;"></i>

                                        <?php else: ?>

                                            <i
                                                class="fas fa-check text-white-50"
                                                style="font-size: 0.7rem;"></i>

                                        <?php endif; ?>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- Message Input -->
            <div class="bg-white border-top p-3">

                <form
                    id="messageForm"
                    action="<?php echo url('Message', 'send'); ?>"
                    method="POST"
                    class="d-flex align-items-end gap-2">



                    <input
                        type="hidden"
                        name="receiver_id"
                        value="<?php echo $otherUserId; ?>">

                    <div class="flex-grow-1">

                        <textarea
                            name="content"
                            id="messageInput"
                            rows="1"
                            class="form-control"
                            placeholder="Type a message..."
                            required
                            maxlength="2000"
                            style="resize: none; overflow-y: hidden;"></textarea>

                    </div>

                    <button type="submit" class="btn btn-primary" id="sendBtn">
                        <i class="bi bi-send-fill"></i>
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<script>
    const messageInput =
        document.getElementById('messageInput');

    const messagesContainer =
        document.getElementById('messagesContainer');

    const otherUserId =
        <?php echo $otherUserId; ?>;

    let lastMessageId =
        <?php echo !empty($messages)
            ? (int) end($messages)['id']
            : 0; ?>;


    /* Auto resize */
    if (messageInput) {

        messageInput.addEventListener('input', function() {

            this.style.height = 'auto';

            this.style.height =
                Math.min(this.scrollHeight, 120) + 'px';

        });

    }


    /* Scroll to bottom */
    if (messagesContainer) {

        messagesContainer.scrollTop =
            messagesContainer.scrollHeight;

    }


    /* Send message */
    document
        .getElementById('messageForm')
        .addEventListener('submit', function(event) {

            event.preventDefault();

            const form = this;

            const content =
                messageInput.value.trim();

            if (!content) {
                return;
            }

            const sendBtn =
                document.getElementById('sendBtn');

            sendBtn.disabled = true;


            fetch(
                    '<?php echo url('Message', 'send'); ?>', {
                        method: 'POST',

                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },

                        body: new URLSearchParams(
                            new FormData(form)
                        )
                    }
                )

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        appendMessage(
                            content,
                            true,
                            data.message_id
                        );

                        messageInput.value = '';
                        messageInput.style.height = 'auto';

                        lastMessageId =
                            data.message_id;

                    } else {

                        alert(
                            data.message ||
                            'Failed to send message.'
                        );

                    }

                })

                .catch(error => {

                    console.error(
                        'Send message error:',
                        error
                    );

                    alert(
                        'Failed to send message.'
                    );

                })

                .finally(() => {

                    sendBtn.disabled = false;

                    messageInput.focus();

                });

        });


    /* Append new message */
    function appendMessage(
        content,
        isMine,
        messageId = 0
    ) {

        const div =
            document.createElement('div');

        div.className =
            `d-flex mb-3 ${
            isMine
                ? 'justify-content-end'
                : 'justify-content-start'
        }`;

        const bubble =
            document.createElement('div');

        bubble.className =
            `message-bubble ${
            isMine
                ? 'bg-primary text-white'
                : 'bg-white border'
        } rounded-3 px-3 py-2`;

        bubble.style.maxWidth = '70%';

        const paragraph =
            document.createElement('p');

        paragraph.className = 'mb-1';

        paragraph.textContent = content;

        bubble.appendChild(paragraph);


        const footer =
            document.createElement('div');

        footer.className =
            'd-flex justify-content-end align-items-center gap-1';


        const time =
            document.createElement('small');

        time.className =
            isMine ?
            'text-white-50' :
            'text-muted';

        time.style.fontSize = '0.7rem';

        time.textContent = 'Just now';

        footer.appendChild(time);


        if (isMine) {

            const check =
                document.createElement('i');

            check.className =
                'fas fa-check text-white-50';

            check.style.fontSize =
                '0.7rem';

            footer.appendChild(check);

        }


        bubble.appendChild(footer);

        div.appendChild(bubble);

        if (messagesContainer) {

            messagesContainer.appendChild(div);

            messagesContainer.scrollTop =
                messagesContainer.scrollHeight;

        }

    }


    /* Poll for new messages */
    function pollMessages() {

        fetch(
                '<?php echo url('Message', 'poll'); ?>' +
                '&user_id=' +
                encodeURIComponent(otherUserId) +
                '&last_id=' +
                encodeURIComponent(lastMessageId)
            )

            .then(response => response.json())

            .then(data => {

                if (
                    data.success &&
                    data.messages &&
                    data.messages.length > 0
                ) {

                    data.messages.forEach(
                        function(msg) {

                            /*
                             * Don't append our own message
                             * twice.
                             */
                            if (
                                Number(msg.id) >
                                Number(lastMessageId)
                            ) {

                                appendMessage(
                                    msg.content,
                                    false,
                                    msg.id
                                );

                                lastMessageId =
                                    msg.id;

                            }

                        }
                    );

                }

            })

            .catch(error => {

                console.error(
                    'Polling error:',
                    error
                );

            })

            .finally(() => {

                setTimeout(
                    pollMessages,
                    3000
                );

            });

    }


    /* Start polling */
    setTimeout(
        pollMessages,
        3000
    );


    /* Search conversations */
    const searchInput = document.getElementById('conversationSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {

            const query = this.value.toLowerCase().trim();

            document
                .querySelectorAll('#conversationsList .list-group-item')
                .forEach(function(item) {

                    const name = item.dataset.name || '';

                    item.style.display =
                        name.includes(query) ? '' : 'none';
                });
        });
    }
    // const searchInput =
    //     document.getElementById(
    //         'conversationSearch'
    //     );

    // if (searchInput) {

    //     searchInput.addEventListener(
    //         'input',
    //         function() {

    //             const query =
    //                 this.value
    //                 .toLowerCase()
    //                 .trim();

    //             document
    //                 .querySelectorAll(
    //                     '#conversationsList .list-group-item'
    //                 )
    //                 .forEach(function(item) {

    //                     const name =
    //                         item.dataset.name || '';

    //                     item.style.display =
    //                         name.includes(query) ?
    //                         '' :
    //                         'none';

    //                 });

    //         }
    //     );

    // }


    /* Enter to send */
    if (messageInput) {

        messageInput.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Enter' &&
                    !event.shiftKey
                ) {

                    event.preventDefault();

                    document
                        .getElementById('messageForm')
                        .dispatchEvent(
                            new Event('submit')
                        );

                }

            }
        );

    }
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
        border-radius: 3px;
    }
</style>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
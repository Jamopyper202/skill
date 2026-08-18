<?php
/**
 * Messages / Conversations List View
 * Shows all conversations for the current user
 */

$title = 'Messages';
$activeTab = 'messages';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <!-- Page Header -->
    <div class="row mb-4">

        <div class="col-md-8">
            <h1 class="h2 mb-0">
                <i class="fas fa-comments text-primary me-2"></i>
                Messages
            </h1>

            <p class="text-muted mb-0">
                Your conversations with other skill exchangers
            </p>
        </div>

        <div class="col-md-4 text-md-end">
            <a href="<?php echo url('Match', 'index'); ?>"
               class="btn btn-primary">

                <i class="fas fa-search me-1"></i>
                Find People

            </a>
        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <div class="row g-4">

        <!-- Conversations Sidebar -->
        <div class="col-lg-4">

            <div class="card shadow border-0">

                <div class="card-header bg-white py-3">

                    <div class="input-group">

                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control bg-light border-start-0"
                            id="conversationSearch"
                            placeholder="Search conversations..."
                        >

                    </div>

                </div>


                <div
                    class="list-group list-group-flush"
                    id="conversationsList"
                    style="max-height: 600px; overflow-y: auto;"
                >

                    <?php if (empty($conversations)): ?>

                        <div class="text-center py-5">

                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>

                            <p class="text-muted">
                                No conversations yet
                            </p>

                            <a
                                href="<?php echo url('Match', 'index'); ?>"
                                class="btn btn-sm btn-primary"
                            >
                                Start a conversation
                            </a>

                        </div>

                    <?php else: ?>

                        <?php foreach ($conversations as $conv): ?>

                            <?php
                            $otherUserId = (int) ($conv['other_user_id'] ?? 0);

                            $otherUserName = $conv['other_user_name'] ?? 'Unknown User';

                            $otherUserPicture = $conv['other_user_picture'] ?? 'download.png';

                            $unreadCount = (int) ($conv['unread_count'] ?? 0);

                            $lastMessage = $conv['content'] ?? '';

                            $lastMessageTime = $conv['created_at'] ?? '';

                            $isFromMe = ($conv['direction'] ?? '') === 'sent';

                            $isActive = (
                                isset($activeConversationId)
                                && (int) $activeConversationId === $otherUserId
                            );
                            ?>

                            <a
                                href="<?php echo url('Message', 'conversation', [
                                    'user_id' => $otherUserId
                                ]); ?>"
                                class="list-group-item list-group-item-action
                                    <?php echo $isActive ? 'active' : ''; ?> py-3"
                                data-name="<?php echo e(strtolower($otherUserName)); ?>"
                            >

                                <div class="d-flex align-items-center">

                                    <!-- Avatar -->
                                    <div class="position-relative flex-shrink-0">

                                        <img
                                            src="<?php echo e(
                                                uploadUrl($otherUserPicture)
                                            ); ?>"
                                            alt="<?php echo e($otherUserName); ?>"
                                            class="rounded-circle me-3"
                                            width="48"
                                            height="48"
                                            style="object-fit: cover;"
                                        >

                                        <?php if ($unreadCount > 0): ?>

                                            <span
                                                class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                                            >
                                                <?php echo $unreadCount; ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>


                                    <!-- Conversation Details -->
                                    <div class="flex-grow-1 min-width-0">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <h6
                                                class="mb-0 text-truncate
                                                <?php echo $isActive ? '' : 'text-dark'; ?>"
                                                style="max-width: 150px;"
                                            >
                                                <?php echo e($otherUserName); ?>
                                            </h6>


                                            <?php if (!empty($lastMessageTime)): ?>

                                                <small
                                                    class="<?php echo $isActive
                                                        ? 'text-white-50'
                                                        : 'text-muted'; ?>"
                                                >
                                                    <?php echo timeAgo($lastMessageTime); ?>
                                                </small>

                                            <?php endif; ?>

                                        </div>


                                        <!-- Last Message -->
                                        <p
                                            class="mb-0 text-truncate small
                                            <?php
                                            echo $isActive
                                                ? 'text-white-50'
                                                : ($unreadCount > 0
                                                    ? 'fw-bold text-dark'
                                                    : 'text-muted');
                                            ?>"
                                            style="max-width: 200px;"
                                        >

                                            <?php if ($isFromMe): ?>

                                                <i class="fas fa-reply fa-xs me-1"></i>

                                            <?php endif; ?>

                                            <?php echo e(
                                                truncate($lastMessage, 40)
                                            ); ?>

                                        </p>

                                    </div>

                                </div>

                            </a>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- Empty State / Conversation Preview -->
        <div class="col-lg-8">

            <div class="card shadow border-0 h-100">

                <div
                    class="card-body d-flex flex-column justify-content-center align-items-center text-center py-5"
                >

                    <div class="mb-4">
                        <i class="fas fa-comments fa-5x text-muted opacity-25"></i>
                    </div>

                    <h3 class="h4 text-muted mb-2">
                        Select a conversation
                    </h3>

                    <p class="text-muted mb-4">
                        Choose a conversation from the list to start chatting,
                        or find new people to connect with.
                    </p>

                    <a
                        href="<?php echo url('Match', 'index'); ?>"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-search me-1"></i>
                        Find Skill Partners
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('conversationSearch');
    const conversationItems =
        document.querySelectorAll('#conversationsList .list-group-item');

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener('input', function () {

        const query = this.value.toLowerCase().trim();

        conversationItems.forEach(function (item) {

            const name = item.dataset.name || '';

            if (name.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }

        });

    });

});
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
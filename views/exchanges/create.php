# views/exchanges/create.php - Send exchange request
exchanges_create = '''<?php
                        /**
                         * Create Exchange Request View
                         * Form to send a skill exchange request to another user
                         */

                        $title = 'Send Exchange Request';
                        $activeTab = 'exchanges';
                        ?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <!-- <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <a href="<?php echo url('Match', 'index'); ?>">Matches</a>
                    <li class="breadcrumb-item active" aria-current="page">Send Request</li>
                </ol>
            </nav> -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="<?php echo url('Dashboard', 'index'); ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="<?php echo url('Match', 'index'); ?>">
                            Matches
                        </a>
                    </li>

                    <li class="breadcrumb-item active" aria-current="page">
                        Send Request
                    </li>

                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Send Exchange Request
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <!-- User Info Card -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img
                                    src="<?php echo e(uploadUrl($matchUser['avatar'] ?? 'download.png')); ?>"
                                    alt="<?php echo e($matchUser['name'] ?? 'User'); ?>"
                                    class="rounded-circle me-3"
                                    width="64"
                                    height="64">
                                <div>
                                    <h5 class="mb-1">
                                        <a href="<?php echo url('Profile', 'view', [
                                                        'id' => $matchUser['id'] ?? 0
                                                    ]); ?>" class="text-decoration-none">
                                            <?= e($matchUser['name'] ?? 'Unknown User') ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-0">@<?= e($matchUser['username'] ?? 'unknown') ?></p>
                                    <?php if (!empty($matchUser['bio'])): ?>
                                        <p class="small text-muted mb-0 mt-1"><?= e(truncate($matchUser['bio'], 100)) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo url('Exchange', 'send'); ?>" method="POST" id="exchangeForm">
                        <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                        <input type="hidden" name="match_id" value="<?php echo e($matchId ?? ''); ?>">
                        <input type="hidden" name="receiver_id" value="<?php echo e($matchUser['id'] ?? 0); ?>">

                        <!-- Skill Selection -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hand-holding-heart text-primary me-1"></i>
                                    Skill You Offer
                                </label>
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <select name="offered_skill_id" class="form-select" required>
                                            <option value="">Select a skill you can teach...</option>
                                            <?php foreach ($myOfferedSkills ?? [] as $skill): ?>
                                                <option value="<?= $skill['id'] ?>"
                                                    <?= (isset($preselectedOffered) && $preselectedOffered == $skill['id']) ? 'selected' : '' ?>>
                                                    <?= e($skill['name']) ?>
                                                    <?php if (!empty($skill['category_name'])): ?>
                                                        <small class="text-muted">(<?= e($skill['category_name']) ?>)</small>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($myOfferedSkills ?? [])): ?>
                                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                                <small><i class="fas fa-exclamation-triangle me-1"></i>
                                                    You haven't added any skills you can teach.
                                                    <a href="<?php echo url('Skill', 'create'); ?>">Add one now</a>.</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-graduation-cap text-success me-1"></i>
                                    Skill You Want to Learn
                                </label>
                                <div class="card border-success">
                                    <div class="card-body">
                                        <select name="requested_skill_id" class="form-select" required>
                                            <option value="">Select a skill you want to learn...</option>
                                            <?php foreach ($matchUserSkills ?? [] as $skill): ?>
                                                <option value="<?= $skill['id'] ?>"
                                                    <?= (isset($preselectedWanted) && $preselectedWanted == $skill['id']) ? 'selected' : '' ?>>
                                                    <?= e($skill['name']) ?>
                                                    <?php if (!empty($skill['category_name'])): ?>
                                                        <small class="text-muted">(<?= e($skill['category_name']) ?>)</small>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($matchUserSkills ?? [])): ?>
                                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                                <small><i class="fas fa-exclamation-triangle me-1"></i>
                                                    This user hasn't listed any teachable skills.</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exchange Details -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold">
                                <i class="fas fa-comment-alt text-info me-1"></i>
                                Personal Message
                            </label>
                            <textarea name="message" id="message" rows="4" class="form-control"
                                placeholder="Introduce yourself and explain what you'd like to learn. Be friendly and specific about your goals and availability..."
                                maxlength="1000"></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>This helps the other user understand your request better.</span>
                                <span id="charCount">0 / 1000</span>
                            </div>
                        </div>

                        <!-- Proposed Schedule -->
                        <div class="mb-4">
                            <label for="proposed_schedule" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-warning me-1"></i>
                                Proposed Schedule <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <input type="text" name="proposed_schedule" id="proposed_schedule"
                                class="form-control"
                                placeholder="e.g., Weekends, Tuesday evenings, Flexible"
                                maxlength="200">
                            <div class="form-text">Suggest when you'd be available for the exchange sessions.</div>
                        </div>

                        <!-- Exchange Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-cogs text-secondary me-1"></i>
                                Exchange Format
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check card p-3 h-100">
                                        <input class="form-check-input" type="radio" name="exchange_format" id="format_online" value="online" checked>
                                        <label class="form-check-label w-100" for="format_online">
                                            <i class="fas fa-laptop fa-2x text-primary d-block mb-2"></i>
                                            <strong>Online</strong>
                                            <small class="text-muted d-block">Video calls, screen sharing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card p-3 h-100">
                                        <input class="form-check-input" type="radio" name="exchange_format" id="format_in_person" value="in_person">
                                        <label class="form-check-label w-100" for="format_in_person">
                                            <i class="fas fa-map-marker-alt fa-2x text-success d-block mb-2"></i>
                                            <strong>In Person</strong>
                                            <small class="text-muted d-block">Meet locally</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card p-3 h-100">
                                        <input class="form-check-input" type="radio" name="exchange_format" id="format_hybrid" value="hybrid">
                                        <label class="form-check-label w-100" for="format_hybrid">
                                            <i class="fas fa-random fa-2x text-info d-block mb-2"></i>
                                            <strong>Hybrid</strong>
                                            <small class="text-muted d-block">Mix of both</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms Agreement -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_agreement" required>
                                <label class="form-check-label" for="terms_agreement">
                                    I agree to be respectful and committed to this skill exchange.
                                    I understand that both parties should communicate openly about expectations and schedules.
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Match', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Matches
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn"
                                <?= (empty($myOfferedSkills ?? []) || empty($matchUserSkills ?? [])) ? 'disabled' : '' ?>>
                                <i class="fas fa-paper-plane me-2"></i>Send Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('message').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length + ' / 1000';
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
'''

with open('/mnt/agents/output/skill-exchange-views/views/exchanges/create.php', 'w') as f:
f.write(exchanges_create)

print("✅ views/exchanges/create.php created")
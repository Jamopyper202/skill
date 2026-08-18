<?php

/**
 * ============================================================================
 * Match Details View
 * ============================================================================
 * 
 * Displays detailed information about a specific match.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year
 * @var array $otherProfile,$match   
 * @var array $match
 * @var array $otherSkills
 * @var array $otherWanted
 * @var array $mySkills 
 * @var array $myWanted
 *  Final Year Project
 * ============================================================================
 */

// Variables available from MatchController::view():
// $match, $otherProfile, $otherSkills, $otherWanted, $mySkills, $myWanted

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container py-4">
        <!-- Back Button -->
    <div class="mb-3">
        <a href="<?php echo url('Match', 'index'); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Matches
        </a>
    </div>
    <!-- Match Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <img
                        src="<?php echo e(uploadUrl($otherProfile['profile_picture'] ?? 'download.png')); ?>"
                        alt="<?php echo e($otherProfile['full_name'] ?? 'User'); ?>"
                        class="avatar-lg">
                </div>
                <div class="col-md-7">
                    <h3 class="mb-1"><?php echo e($otherProfile['full_name']); ?></h3>
                    <p class="text-muted mb-2">
                        <?php echo e($otherProfile['experience_level'] ?? 'Beginner'); ?>
                        <?php if (!empty($otherProfile['location'])): ?>
                            <span class="mx-2">|</span>
                            <i class="bi bi-geo-alt me-1"></i><?php echo e($otherProfile['location']); ?>
                        <?php endif; ?>
                    </p>
                    <p class="mb-0"><?php echo e(truncate($otherProfile['bio'] ?? '', 150)); ?></p>
                </div>
                <div class="col-md-3 text-md-end">
                    <div class="match-score-circle <?php
                                                    echo $match['match_score'] >= 70 ? 'match-high' : ($match['match_score'] >= 50 ? 'match-medium' : 'match-low');
                                                    ?> mx-auto mb-2" style="width: 80px; height: 80px; font-size: 1.5rem;">
                        <?php echo $match['match_score']; ?>%
                    </div>
                    <p class="text-muted small mb-0">Match Score</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Why You Match -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb me-2 text-warning"></i>Why You Match</h5>
                </div>
                <div class="card-body">
                    <p><?php echo e($match['notes']); ?></p>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3"><i class="bi bi-tools me-2"></i>You Offer / They Want</h6>
                            <?php
                            $forwardMatches = [];
                            foreach ($mySkills as $mySkill) {
                                foreach ($otherWanted as $theirWant) {
                                    if ($mySkill['skill_id'] == $theirWant['skill_id']) {
                                        $forwardMatches[] = $mySkill;
                                    }
                                }
                            }
                            ?>
                            <?php if (empty($forwardMatches)): ?>
                                <p class="text-muted small">No direct skill matches found.</p>
                            <?php else: ?>
                                <?php foreach ($forwardMatches as $skill): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span><?php echo e($skill['skill_name']); ?> (<?php echo e($skill['experience_level']); ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info mb-3"><i class="bi bi-book me-2"></i>They Offer / You Want</h6>
                            <?php
                            $reverseMatches = [];
                            foreach ($otherSkills as $theirSkill) {
                                foreach ($myWanted as $myWant) {
                                    if ($theirSkill['skill_id'] == $myWant['skill_id']) {
                                        $reverseMatches[] = $theirSkill;
                                    }
                                }
                            }
                            ?>
                            <?php if (empty($reverseMatches)): ?>
                                <p class="text-muted small">No direct skill matches found.</p>
                            <?php else: ?>
                                <?php foreach ($reverseMatches as $skill): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle-fill text-info me-2"></i>
                                        <span><?php echo e($skill['skill_name']); ?> (<?php echo e($skill['experience_level']); ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Their Skills -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-tools me-2 text-success"></i><?php echo e($otherProfile['full_name']); ?>'s Skills</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($otherSkills)): ?>
                        <p class="text-muted mb-0">No skills listed yet.</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($otherSkills as $skill): ?>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo e($skill['skill_name']); ?></span>
                                                <?php echo experienceBadge($skill['experience_level']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Their Wanted Skills -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-book me-2 text-warning"></i>Skills They Want to Learn</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($otherWanted)): ?>
                        <p class="text-muted mb-0">No wanted skills listed yet.</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($otherWanted as $skill): ?>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo e($skill['skill_name']); ?></span>
                                                <span class="badge <?php
                                                                    echo $skill['urgency'] === 'High' ? 'bg-danger' : ($skill['urgency'] === 'Medium' ? 'bg-warning text-dark' : 'bg-info');
                                                                    ?>"><?php echo $skill['urgency']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-primary"></i>Actions</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if ($match['status'] === 'pending'): ?>
                        <?php if ($match['my_response'] === 'pending'): ?>
                            <a href="<?php echo url('Match', 'accept', ['id' => $match['id']]); ?>" class="list-group-item list-group-item-action text-success">
                                <i class="bi bi-check-circle me-2"></i>Accept Match
                            </a>
                            <a href="<?php echo url('Match', 'decline', ['id' => $match['id']]); ?>" class="list-group-item list-group-item-action text-danger"
                                data-confirm="Are you sure you want to decline this match?">
                                <i class="bi bi-x-circle me-2"></i>Decline Match
                            </a>
                        <?php else: ?>
                            <div class="list-group-item text-muted">
                                <i class="bi bi-clock me-2"></i>Waiting for their response
                            </div>
                        <?php endif; ?>
                    <?php elseif ($match['status'] === 'accepted'): ?>
                        <div class="list-group-item text-success">
                            <i class="bi bi-check-circle-fill me-2"></i>Match Accepted!
                        </div>
                        <a href="<?php echo url('Exchange', 'send', [
                                        'match_id' => $match['id'],
                                        'user_id' => ($match['user_id_1'] == getCurrentUserId() ? $match['user_id_2'] : $match['user_id_1'])
                                    ]); ?>" class="list-group-item list-group-item-action text-primary">
                            <i class="bi bi-arrow-left-right me-2"></i>Send Exchange Request
                        </a>
                    <?php else: ?>
                        <div class="list-group-item text-danger">
                            <i class="bi bi-x-circle-fill me-2"></i>Match Declined
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo url('Message', 'conversation', [
                                    'user_id' => ($match['user_id_1'] == getCurrentUserId() ? $match['user_id_2'] : $match['user_id_1'])
                                ]); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-chat-dots me-2"></i>Send Message
                    </a>

                    <a href="<?php echo url('Profile', 'view', [
                                    'id' => ($match['user_id_1'] == getCurrentUserId() ? $match['user_id_2'] : $match['user_id_1'])
                                ]); ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i>View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
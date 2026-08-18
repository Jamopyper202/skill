<?php
/**
 * ============================================================================
 * Public Profile View
 * ============================================================================
 * 
 * Displays another user's public profile with skills and reviews.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from ProfileController::view():
// $profile, $skills, $wantedSkills, $avgRating, $ratingBreakdown, $reviews, $reviewCount

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <!-- Profile Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="<?php echo uploadUrl($profile['profile_picture']); ?>" 
                        alt="<?php echo e($profile['full_name']); ?>" 
                        class="avatar-lg">
                </div>
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h3 class="mb-1"><?php echo e($profile['full_name']); ?></h3>
                            <?php if (!empty($profile['location'])): ?>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($profile['location']); ?>
                                </p>
                            <?php endif; ?>
                            <p class="text-muted mb-2">
                                <i class="bi bi-clock me-1"></i>Available: <?php echo e($profile['availability'] ?? 'Flexible'); ?>
                            </p>
                        </div>
                        <div class="text-md-end">
                            <?php echo experienceBadge($profile['experience_level'] ?? 'Beginner'); ?>
                            <div class="mt-2">
                                <?php if ($avgRating > 0): ?>
                                    <?php echo starRating($avgRating); ?>
                                    <span class="ms-1">(<?php echo $avgRating; ?> / <?php echo $reviewCount; ?> reviews)</span>
                                <?php else: ?>
                                    <span class="text-muted small">No ratings yet</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($profile['bio'])): ?>
                        <p class="mt-3"><?php echo nl2br(e($profile['bio'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <?php if (!empty($profile['linkedin_url'])): ?>
                            <a href="<?php echo e($profile['linkedin_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-linkedin me-1"></i>LinkedIn
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['github_url'])): ?>
                            <a href="<?php echo e($profile['github_url']); ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-github me-1"></i>GitHub
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['website_url'])): ?>
                            <a href="<?php echo e($profile['website_url']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-globe me-1"></i>Website
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo url('Message', 'conversation', ['user_id' => $profile['id']]); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-chat-dots me-1"></i>Message
                        </a>
                        
                        <a href="<?php echo url('Portfolio', 'view', ['user_id' => $profile['id']]); ?>" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-briefcase me-1"></i>Portfolio
                        </a>
                        
                        <a href="<?php echo url('Report', 'create', ['user_id' => $profile['id']]); ?>" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-flag me-1"></i>Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Skills Offered -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-tools me-2 text-success"></i>Skills Offered</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($skills)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-tools fs-2"></i>
                            <p class="mb-0">No skills listed yet</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($skills as $skill): ?>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($skill['skill_name']); ?></h6>
                                                    <small class="text-muted"><?php echo e($skill['category_name']); ?></small>
                                                </div>
                                                <?php echo experienceBadge($skill['experience_level']); ?>
                                            </div>
                                            <?php if (!empty($skill['description'])): ?>
                                                <p class="small text-muted mb-0 mt-1"><?php echo e(truncate($skill['description'], 80)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Skills Wanted -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-book me-2 text-warning"></i>Skills Wanted</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($wantedSkills)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-book fs-2"></i>
                            <p class="mb-0">No wanted skills listed yet</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($wantedSkills as $skill): ?>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?php echo e($skill['skill_name']); ?></h6>
                                                    <small class="text-muted"><?php echo e($skill['category_name']); ?></small>
                                                </div>
                                                <span class="badge <?php 
                                                    echo $skill['urgency'] === 'High' ? 'bg-danger' : 
                                                        ($skill['urgency'] === 'Medium' ? 'bg-warning text-dark' : 'bg-info'); 
                                                ?>">
                                                    <?php echo $skill['urgency']; ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($skill['description'])): ?>
                                                <p class="small text-muted mb-0 mt-1"><?php echo e(truncate($skill['description'], 80)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-star me-2 text-warning"></i>Reviews (<?php echo $reviewCount; ?>)</h5>
            <?php if ($avgRating > 0): ?>
                <div><?php echo starRating($avgRating); ?> <span class="ms-1"><?php echo $avgRating; ?>/5</span></div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($reviews)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-star fs-2"></i>
                    <p class="mb-0">No reviews yet</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo uploadUrl($review['reviewer_picture'] ?? 'default-avatar.png'); ?>" 
                                                class="avatar-sm me-2">
                                            <div>
                                                <h6 class="mb-0 small"><?php echo e($review['reviewer_name']); ?></h6>
                                                <small class="text-muted"><?php echo timeAgo($review['created_at']); ?></small>
                                            </div>
                                        </div>
                                        <?php echo starRating($review['rating'], 14); ?>
                                    </div>
                                    <p class="mb-0 small">"<?php echo e($review['comment']); ?>"</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
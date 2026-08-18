<?php
/**
 * ============================================================================
 * Profile Index View
 * ============================================================================
 * 
 * Displays the logged-in user's profile with skills, stats, and reviews.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from ProfileController::index():
// $profile, $skills, $wantedSkills, $stats, $avgRating, $ratingBreakdown, $reviews

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
                        class="avatar-lg mb-2">
                    <div class="mt-2">
                        <a href="<?php echo url('Profile', 'edit'); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h3 class="mb-1"><?php echo e($profile['full_name']); ?></h3>
                            <p class="text-muted mb-2">
                                <i class="bi bi-envelope me-1"></i><?php echo e($profile['email']); ?>
                            </p>
                            <?php if (!empty($profile['location'])): ?>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($profile['location']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="text-md-end">
                            <?php echo experienceBadge($profile['experience_level'] ?? 'Beginner'); ?>
                            <div class="mt-2">
                                <?php if ($avgRating > 0): ?>
                                    <?php echo starRating($avgRating); ?>
                                    <span class="ms-1">(<?php echo $avgRating; ?>)</span>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Skills -->
        <div class="col-lg-8">
            <!-- Skills Offered -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-tools me-2 text-success"></i>Skills I Offer</h5>
                    <a href="<?php echo url('Skill', 'add'); ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i>Add Skill
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($skills)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-tools"></i>
                            <h5>No skills added yet</h5>
                            <p class="text-muted">Add skills you can teach others</p>
                            <a href="<?php echo url('Skill', 'add'); ?>" class="btn btn-success">
                                <i class="bi bi-plus-lg me-2"></i>Add Skill
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($skills as $skill): ?>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo e($skill['skill_name']); ?></h6>
                                                    <span class="badge bg-secondary"><?php echo e($skill['category_name']); ?></span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-dark" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="<?php echo url('Skill', 'edit', ['id' => $skill['id']]); ?>">
                                                                <i class="bi bi-pencil me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="<?php echo url('Skill', 'delete', ['id' => $skill['id']]); ?>" 
                                                                data-confirm="Are you sure you want to remove this skill?">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="small text-muted mt-2 mb-2">
                                                <?php echo e(truncate($skill['description'], 100)); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <?php echo experienceBadge($skill['experience_level']); ?>
                                                <?php if ($skill['years_of_experience'] > 0): ?>
                                                    <small class="text-muted"><?php echo $skill['years_of_experience']; ?> years exp.</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Skills Wanted -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-book me-2 text-warning"></i>Skills I Want to Learn</h5>
                    <a href="<?php echo url('Skill', 'addWanted'); ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-plus-lg me-1"></i>Add Wanted Skill
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($wantedSkills)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-book"></i>
                            <h5>No wanted skills yet</h5>
                            <p class="text-muted">Add skills you want to learn from others</p>
                            <a href="<?php echo url('Skill', 'addWanted'); ?>" class="btn btn-warning">
                                <i class="bi bi-plus-lg me-2"></i>Add Wanted Skill
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($wantedSkills as $skill): ?>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo e($skill['skill_name']); ?></h6>
                                                    <span class="badge bg-secondary"><?php echo e($skill['category_name']); ?></span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-dark" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="<?php echo url('Skill', 'editWanted', ['id' => $skill['id']]); ?>">
                                                                <i class="bi bi-pencil me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="<?php echo url('Skill', 'deleteWanted', ['id' => $skill['id']]); ?>" 
                                                                data-confirm="Are you sure you want to remove this wanted skill?">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="small text-muted mt-2 mb-2">
                                                <?php echo e(truncate($skill['description'], 100)); ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <?php echo experienceBadge($skill['experience_level']); ?>
                                                <span class="badge <?php 
                                                    echo $skill['urgency'] === 'High' ? 'bg-danger' : 
                                                        ($skill['urgency'] === 'Medium' ? 'bg-warning text-dark' : 'bg-info'); 
                                                ?>">
                                                    <?php echo $skill['urgency']; ?> Priority
                                                </span>
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

        <!-- Right Column: Stats & Reviews -->
        <div class="col-lg-4">
            <!-- Stats -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Statistics</h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-tools me-2 text-success"></i>Skills Offered</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['skills_offered']; ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-book me-2 text-warning"></i>Skills Wanted</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['skills_wanted']; ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-people me-2 text-info"></i>Matches</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['matches']; ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-briefcase me-2 text-secondary"></i>Portfolio Items</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['portfolio_items']; ?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-star me-2 text-warning"></i>Reviews Received</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['reviews']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Rating Breakdown -->
            <?php if ($avgRating > 0): ?>
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-star-fill me-2 text-warning"></i>Rating Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <?php for ($i = 5; $i >= 1; $i--): 
                            $count = $ratingBreakdown[$i] ?? 0;
                            $total = array_sum($ratingBreakdown);
                            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                        ?>
                            <div class="d-flex align-items-center mb-2">
                                <span class="me-2" style="width: 60px;"><?php echo $i; ?> stars</span>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                </div>
                                <span class="ms-2 text-muted small" style="width: 40px;"><?php echo $count; ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text me-2 text-primary"></i>Recent Reviews</h5>
                    <a href="<?php echo url('Review', 'index'); ?>" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <p class="text-muted text-center mb-0">No reviews yet</p>
                    <?php else: ?>
                        <?php foreach (array_slice($reviews, 0, 3) as $review): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="mb-1"><?php echo starRating($review['rating'], 14); ?></div>
                                <p class="small mb-1">"<?php echo e(truncate($review['comment'], 80)); ?>"</p>
                                <small class="text-muted">by <?php echo e($review['reviewer_name']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
<?php
/**
 * ============================================================================
 * Browse Skills View
 * ============================================================================
 * 
 * Public page to browse all offered skills with search and filters.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::browse():
// $skills, $total, $totalPages, $categories, $categoryId, $search, $experience

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-search me-2 text-primary"></i>Browse Skills</h2>
            <p class="text-muted">Discover skills offered by our community members</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo url('Skill', 'browse'); ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" 
                                value="<?php echo e($search); ?>" 
                                placeholder="Search skills or users...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="0">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Experience Level</label>
                        <select class="form-select" name="experience">
                            <option value="">All Levels</option>
                            <option value="Beginner" <?php echo $experience === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="Intermediate" <?php echo $experience === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="Advanced" <?php echo $experience === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                            <option value="Expert" <?php echo $experience === 'Expert' ? 'selected' : ''; ?>>Expert</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($skills)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3"></i>
                <h4>No skills found</h4>
                <p class="text-muted">Try adjusting your search criteria or browse all skills.</p>
                <a href="<?php echo url('Skill', 'browse'); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($skills as $skill): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 user-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <img src="<?php echo uploadUrl($skill['profile_picture']); ?>" 
                                    alt="<?php echo e($skill['full_name']); ?>" 
                                    class="avatar me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="<?php echo url('Profile', 'view', ['id' => $skill['user_id']]); ?>" class="text-decoration-none">
                                            <?php echo e($skill['full_name']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo e($skill['location'] ?? 'Location not set'); ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h5 class="mb-1"><?php echo e($skill['skill_name']); ?></h5>
                                <span class="badge bg-secondary">
                                    <i class="bi <?php echo e($skill['category_icon'] ?? 'bi-grid'); ?> me-1"></i>
                                    <?php echo e($skill['category_name']); ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($skill['description'])): ?>
                                <p class="small text-muted mb-3">
                                    <?php echo e(truncate($skill['description'], 100)); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <?php echo experienceBadge($skill['experience_level']); ?>
                                <?php if ($skill['years_of_experience'] > 0): ?>
                                    <small class="text-muted"><?php echo $skill['years_of_experience']; ?> years</small>
                                <?php endif; ?>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="d-flex gap-2">
                                <a href="<?php echo url('Profile', 'view', ['id' => $skill['user_id']]); ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="bi bi-person me-1"></i>View Profile
                                </a>
                                <?php if (isLoggedIn() && $skill['user_id'] != getCurrentUserId()): ?>
                                    <a href="<?php echo url('Message', 'conversation', ['user_id' => $skill['user_id']]); ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            <i class="bi bi-clock me-1"></i>Member since <?php echo formatDate($skill['user_joined'], 'M Y'); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php 
        $baseUrl = url('Skill', 'browse') . '&search=' . urlencode($search) . '&category=' . $categoryId . '&experience=' . urlencode($experience);
        echo pagination((int)($_GET['page'] ?? 1), $totalPages, $baseUrl); 
        ?>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
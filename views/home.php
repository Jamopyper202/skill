<?php
/**
 * ============================================================================
 * Home View (Landing Page)
 * ============================================================================
 * 
 * Public landing page for non-logged-in users.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 * 
 */

// Variables available from DashboardController::home():
// $recentUsers, $categories, $totalUsers, $totalSkills, $totalExchanges, $totalMatches

require_once BASE_PATH . '/views/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Exchange Skills,<br>
                    <span class="text-warning">No Money Needed</span>
                </h1>
                <p class="lead mb-4">
                    Join our community where you can teach what you know and learn what you want.
                    Connect with others through intelligent matching and start exchanging skills today.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo url('Auth', 'register'); ?>" class="btn btn-warning btn-lg px-4">
                        <i class="bi bi-person-plus me-2"></i>Get Started
                    </a>
                    <a href="<?php echo url('Auth', 'login'); ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="text-primary fw-bold"><?php echo formatNumber($totalUsers); ?></h2>
                    <p class="text-muted mb-0">Active Users</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="text-primary fw-bold"><?php echo formatNumber($totalSkills); ?></h2>
                    <p class="text-muted mb-0">Skills Listed</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="text-primary fw-bold"><?php echo formatNumber($totalExchanges); ?></h2>
                    <p class="text-muted mb-0">Exchanges Done</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <h2 class="text-primary fw-bold"><?php echo formatNumber($totalMatches); ?></h2>
                    <p class="text-muted mb-0">Successful Matches</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4>1. Create Profile</h4>
                    <p class="text-muted">Sign up and list the skills you can teach and the skills you want to learn.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4>2. Get Matched</h4>
                    <p class="text-muted">Our intelligent matching system finds users with complementary skills.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-arrow-left-right text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h4>3. Start Exchanging</h4>
                    <p class="text-muted">Connect, communicate, and exchange skills without any monetary transactions.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Popular Categories</h2>
        <div class="row g-3">
            <?php 
            $displayCategories = array_slice($categories, 0, 8);
            foreach ($displayCategories as $category): 
            ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 text-center p-3 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi <?php echo e($category['icon']); ?> text-primary mb-3" style="font-size: 2.5rem;"></i>
                            <h5 class="card-title"><?php echo e($category['name']); ?></h5>
                            <p class="card-text small text-muted">
                                <?php echo e(truncate($category['description'], 60)); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Recent Users -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Recently Joined Users</h2>
        <div class="row g-4">
            <?php foreach ($recentUsers as $user): ?>
                <div class="col-md-4 col-lg-2">
                    <div class="card user-card h-100 text-center">
                        <div class="card-body">
                            <img src="<?php echo uploadUrl($user['profile_picture']); ?>" 
                                alt="<?php echo e($user['full_name']); ?>" 
                                class="avatar-lg mb-3">
                            <h6 class="mb-1"><?php echo e($user['full_name']); ?></h6>
                            <p class="small text-muted mb-2">
                                <?php echo e($user['experience_level'] ?? 'Beginner'); ?>
                            </p>
                            <?php if (!empty($user['location'])): ?>
                                <p class="small text-muted mb-0">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($user['location']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="mb-3">Ready to Start Exchanging Skills?</h2>
        <p class="lead mb-4">Join thousands of users already learning and teaching on <?php echo e(APP_NAME); ?>.</p>
        <a href="<?php echo url('Auth', 'register'); ?>" class="btn btn-warning btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>Create Free Account
        </a>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
<?php
/**
 * Public Portfolio View
 */
$title = e($portfolioUser['name'] ?? 'User') . "'s Portfolio";
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <!-- User Header -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="<?= getAvatarUrl($portfolioUser['avatar'] ?? null) ?>" 
                     alt="" class="rounded-circle me-4" width="80" height="80">
                <div>
                    <h1 class="h3 mb-1"><?= e($portfolioUser['name'] ?? 'Unknown User') ?>'s Portfolio</h1>
                    <p class="text-muted mb-2">@<?= e($portfolioUser['username'] ?? 'unknown') ?></p>
                    <a href="/profile/view/<?= $portfolioUser['id'] ?? 0 ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user me-1"></i>View Profile
                    </a>
                    <a href="/messages/conversation/<?= $portfolioUser['id'] ?? 0 ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-comment me-1"></i>Contact
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($portfolioItems)): ?>
        <div class="text-center py-5">
            <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
            <h3 class="h5 text-muted">No portfolio items yet</h3>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($portfolioItems as $item): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <?php if (!empty($item['image'])): ?>
                    <img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" 
                         class="card-img-top" style="height: 220px; object-fit: cover;">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= e($item['title']) ?></h5>
                        <p class="card-text text-muted"><?= nl2br(e($item['description'])) ?></p>
                        <?php if (!empty($item['skill_name'])): ?>
                        <span class="badge bg-info"><?= e($item['skill_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($item['project_url'])): ?>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= e($item['project_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-1"></i>View Project
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
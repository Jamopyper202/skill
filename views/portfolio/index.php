<?php
/**
 * My Portfolio View
 */
$title = 'My Portfolio';
$activeTab = 'portfolio';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><i class="fas fa-briefcase text-primary me-2"></i>My Portfolio</h1>
            <p class="text-muted mb-0">Showcase your work and projects</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/portfolio/create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Project
            </a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <?php if (empty($portfolioItems)): ?>
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                <h3 class="h5 text-muted">Your portfolio is empty</h3>
                <p class="text-muted">Add projects to showcase your skills and attract exchange partners.</p>
                <a href="/portfolio/create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Project
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($portfolioItems as $item): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 h-100 hover-lift">
                    <?php if (!empty($item['image'])): ?>
                    <img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" 
                         class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= e($item['title']) ?></h5>
                        <p class="card-text text-muted small"><?= e(truncate($item['description'], 100)) ?></p>
                        <?php if (!empty($item['skill_name'])): ?>
                        <span class="badge bg-info"><?= e($item['skill_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="btn-group w-100">
                            <a href="/portfolio/view/<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="/portfolio/edit/<?= $item['id'] ?>" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form action="/portfolio/delete/<?= $item['id'] ?>" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this portfolio item?')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-start-0">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
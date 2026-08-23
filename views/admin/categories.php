<?php
// views/admin/categories.php
$title = 'Manage Categories';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><i class="fas fa-tags text-primary me-2"></i>Manage Categories</h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/admin/categories/add" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Category</a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="row g-4">
        <?php foreach ($categories ?? [] as $category): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><?= e($category['name']) ?></h5>
                    <p class="card-text text-muted small"><?= $category['skill_count'] ?? 0 ?> skills</p>
                    <div class="btn-group w-100">
                        <a href="/admin/categories/edit/<?= $category['id'] ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="/admin/categories/delete/<?= $category['id'] ?>" method="POST" class="d-inline flex-grow-1"
                              onsubmit="return confirm('Delete this category? Skills will become uncategorized.')">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-start-0">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * Admin Skills Management
 */
$title = 'Manage Skills';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><i class="fas fa-lightbulb text-primary me-2"></i>Manage Skills</h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/admin/skills/add" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Skill</a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Skill</th>
                        <th>Category</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($skills ?? [] as $skill): ?>
                    <tr>
                        <td><?= e($skill['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($skill['category_name'] ?? 'Uncategorized') ?></span></td>
                        <td><?= $skill['user_count'] ?? 0 ?></td>
                        <td>
                            <span class="badge bg-<?= ($skill['is_active'] ?? 1) ? 'success' : 'secondary' ?>">
                                <?= ($skill['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/admin/skills/edit/<?= $skill['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/admin/skills/toggle/<?= $skill['id'] ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
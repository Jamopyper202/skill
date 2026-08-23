# views/admin/add-category.php
add_category = '''<?php
/**
 * Admin Add Category View
 */
$title = 'Add Category';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/categories">Categories</a></li>
                    <li class="breadcrumb-item active">Add Category</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-plus me-2"></i>Add Category</h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <form action="/admin/categories/add" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Category Name *</label>
                            <input type="text" name="name" id="name" class="form-control" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" maxlength="500"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label fw-bold">Icon Class <span class="text-muted fw-normal">(Font Awesome)</span></label>
                            <input type="text" name="icon" id="icon" class="form-control" 
                                placeholder="e.g., fa-code, fa-paint-brush" maxlength="50">
                            <div class="form-text">Use Font Awesome icon class without the "fas" prefix.</div>
                        </div>

                        <div class="mb-4">
                            <label for="color" class="form-label fw-bold">Color</label>
                            <input type="color" name="color" id="color" class="form-control form-control-color" 
                                value="#0d6efd" title="Choose category color">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/categories" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
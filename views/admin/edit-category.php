<?php
/**
 * Admin Edit Category View
 */

$title = 'Edit Category';
$activeTab = 'admin';

$categoryId = (int) ($category['id'] ?? 0);

$categoryName = $category['name'] ?? '';

$description = $category['description'] ?? '';

$icon = $category['icon'] ?? 'bi-grid';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         BREADCRUMB
    ========================================================== -->

    <nav aria-label="breadcrumb" class="mb-4">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?= url('Admin', 'index') ?>">
                    Dashboard
                </a>

            </li>

            <li class="breadcrumb-item">

                <a href="<?= url('Admin', 'categories') ?>">
                    Categories
                </a>

            </li>

            <li class="breadcrumb-item active">
                Edit Category
            </li>

        </ol>

    </nav>


    <div class="row justify-content-center">

        <div class="col-lg-7 col-xl-6">

            <div class="card shadow-sm border-0">

                <!-- =================================================
                     HEADER
                ================================================== -->

                <div class="card-header bg-warning text-dark py-3">

                    <h2 class="h5 mb-0">

                        <i class="bi bi-pencil-square me-2"></i>

                        Edit Category

                    </h2>

                </div>


                <!-- =================================================
                     BODY
                ================================================== -->

                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <?php if ($categoryId <= 0): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-triangle me-2"></i>

                            Invalid category.

                        </div>

                    <?php else: ?>


                        <form
                            action="<?= url(
                                'Admin',
                                'editCategory',
                                ['id' => $categoryId]
                            ) ?>"
                            method="POST"
                        >

                          


                            <!-- Category ID -->

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $categoryId ?>"
                            >


                            <!-- =================================================
                                 CATEGORY NAME
                            ================================================== -->

                            <div class="mb-4">

                                <label
                                    for="name"
                                    class="form-label fw-semibold"
                                >
                                    Category Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    value="<?= e($categoryName) ?>"
                                    required
                                    maxlength="100"
                                    placeholder="Enter category name"
                                >

                            </div>


                            <!-- =================================================
                                 DESCRIPTION
                            ================================================== -->

                            <div class="mb-4">

                                <label
                                    for="description"
                                    class="form-label fw-semibold"
                                >
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    id="description"
                                    rows="4"
                                    class="form-control"
                                    maxlength="500"
                                    placeholder="Describe this category..."
                                ><?= e($description) ?></textarea>

                            </div>


                            <!-- =================================================
                                 ICON
                            ================================================== -->

                            <div class="mb-4">

                                <label
                                    for="icon"
                                    class="form-label fw-semibold"
                                >
                                    Icon Class
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i
                                            class="bi <?= e($icon) ?>"
                                            id="iconPreview"
                                        ></i>

                                    </span>

                                    <input
                                        type="text"
                                        name="icon"
                                        id="icon"
                                        class="form-control"
                                        value="<?= e($icon) ?>"
                                        maxlength="50"
                                        placeholder="e.g. bi-code-slash"
                                    >

                                </div>

                                <div class="form-text">
                                    Example:
                                    <code>bi-code-slash</code>,
                                    <code>bi-palette</code>,
                                    <code>bi-laptop</code>
                                </div>

                            </div>


                            <!-- =================================================
                                 ACTIONS
                            ================================================== -->

                            <div
                                class="d-flex
                                       justify-content-between
                                       align-items-center
                                       gap-2"
                            >

                                <a
                                    href="<?= url('Admin', 'categories') ?>"
                                    class="btn btn-outline-secondary"
                                >

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Cancel

                                </a>


                                <button
                                    type="submit"
                                    class="btn btn-warning"
                                >

                                    <i class="bi bi-check-lg me-1"></i>

                                    Update Category

                                </button>

                            </div>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
const iconInput = document.getElementById('icon');
const iconPreview = document.getElementById('iconPreview');

if (iconInput && iconPreview) {

    iconInput.addEventListener('input', function () {

        iconPreview.className =
            'bi ' + this.value.trim();

    });

}
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
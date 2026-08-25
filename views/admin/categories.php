<?php

/**
 * Admin Categories Management
 */

$title = 'Manage Categories';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <div class="row align-items-center mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-1">
                <i class="bi bi-tags text-primary me-2"></i>
                Manage Categories
            </h1>

            <p class="text-muted mb-0">
                Manage skill categories available on the SkillSwap platform.
            </p>

        </div>


        <div class="col-md-4 text-md-end mt-3 mt-md-0">

            <a
                href="<?= url('Admin', 'addCategory') ?>"
                class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Category
            </a>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         CATEGORY CARDS
    ========================================================== -->

    <div class="row g-4">

        <?php if (!empty($categories)): ?>

            <?php foreach ($categories as $category): ?>

                <?php
                $categoryId =
                    (int) ($category['id'] ?? 0);

                $categoryName =
                    $category['name']
                    ?? 'Unnamed Category';

                $skillCount =
                    (int) (
                        $category['skill_count']
                        ?? 0
                    );
                ?>


                <div class="col-md-6 col-lg-4 col-xl-3">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body d-flex flex-column">

                            <!-- Category icon -->

                            <div class="mb-3">

                                <div
                                    class="rounded-circle bg-primary bg-opacity-10
                                           d-inline-flex align-items-center
                                           justify-content-center"
                                    style="width: 50px; height: 50px;">

                                    <i class="bi <?= e($category['icon'] ?? 'bi-grid') ?>"></i>

                                </div>

                            </div>


                            <!-- Category name -->

                            <h5 class="card-title mb-2">

                                <?= e($categoryName) ?>

                            </h5>


                            <!-- Skill count -->

                            <p class="card-text text-muted small mb-4">

                                <i class="bi bi-lightbulb me-1"></i>

                                <?= $skillCount ?>

                                skill<?= $skillCount !== 1 ? 's' : '' ?>

                            </p>


                            <!-- Actions -->

                            <?php if ($categoryId > 0): ?>

                                <div class="btn-group w-100 mt-auto">

                                    <!-- Edit -->

                                    <a
                                        href="<?= url(
                                                    'Admin',
                                                    'editCategory',
                                                    ['id' => $categoryId]
                                                ) ?>"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Edit Category">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>
                                    

                                    <?php
                                    $isActive = !empty($category['is_active']);

                                    $buttonClass = $isActive
                                        ? 'warning'
                                        : 'success';

                                    $buttonTitle = $isActive
                                        ? 'Deactivate Category'
                                        : 'Activate Category';

                                    $actionText = $isActive
                                        ? 'Deactivate'
                                        : 'Activate';

                                    $buttonIcon = $isActive
                                        ? 'toggle-off'
                                        : 'toggle-on';
                                    ?>


                                    <!-- Activate / Deactivate -->

                                    <form
                                        action="<?= url('Admin', 'toggleCategory') ?>"
                                        method="POST"
                                        class="d-inline">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $categoryId ?>">


                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-<?= $buttonClass ?>"
                                            title="<?= e($buttonTitle) ?>"
                                            onclick="return confirm(
                '<?= e($actionText) ?> this category?'
            );">

                                            <i class="bi bi-<?= $buttonIcon ?>"></i>

                                        </button>

                                    </form>



                                    <!-- Delete -->

                                    <form
                                        action="<?= url(
                                                    'Admin',
                                                    'deleteCategory'
                                                ) ?>"
                                        method="POST"
                                        class="flex-grow-1"
                                        onsubmit="return confirm(
                                            'Delete this category? Skills will become uncategorized.'
                                        );">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $categoryId ?>">




                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger
                                                   w-100 rounded-start-0"
                                            title="Delete Category">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>


        <?php else: ?>

            <!-- =================================================
                 NO CATEGORIES
            ================================================== -->

            <div class="col-12">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center py-5">

                        <i
                            class="bi bi-tags text-muted fs-1 d-block mb-3"></i>

                        <h5 class="text-muted">
                            No categories found
                        </h5>

                        <p class="text-muted mb-3">
                            There are currently no skill categories.
                        </p>

                        <a
                            href="<?= url('Admin', 'addCategory') ?>"
                            class="btn btn-primary">

                            <i class="bi bi-plus-lg me-1"></i>

                            Add Category

                        </a>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
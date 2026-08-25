<?php
/**
 * Admin Add Category View
 */

$title = 'Add Category';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->
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
                Add Category
            </li>

        </ol>

    </nav>


    <div class="row justify-content-center">

        <div class="col-lg-7 col-xl-6">

            <div class="card shadow-sm border-0">

                <!-- Header -->

                <div class="card-header bg-primary text-white py-3">

                    <h2 class="h5 mb-0">

                        <i class="bi bi-plus-circle me-2"></i>

                        Add Category

                    </h2>

                </div>


                <!-- Body -->

                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <form
                        action="<?= url('Admin', 'addCategory') ?>"
                        method="POST"
                    >




                        <!-- Category Name -->

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
                                maxlength="100"
                                required
                                value="<?= e(
                                    $_POST['name'] ?? ''
                                ) ?>"
                                placeholder="e.g. Programming"
                            >

                        </div>


                        <!-- Description -->

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
                                placeholder="Briefly describe this category..."
                            ><?= e(
                                $_POST['description'] ?? ''
                            ) ?></textarea>

                        </div>


                        <!-- Icon -->

                        <div class="mb-4">

                            <label
                                for="icon"
                                class="form-label fw-semibold"
                            >
                                Icon Class
                                <span class="text-muted fw-normal">
                                    (Optional)
                                </span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>

                                <input
                                    type="text"
                                    name="icon"
                                    id="icon"
                                    class="form-control"
                                    maxlength="50"
                                    value="<?= e(
                                        $_POST['icon'] ?? ''
                                    ) ?>"
                                    placeholder="e.g. bi-code-slash"
                                >

                            </div>

                            <div class="form-text">
                                Enter the Bootstrap Icons class without
                                the <code>bi</code> prefix if your system
                                stores only the icon name.
                            </div>

                        </div>


                        <!-- Color -->

                        <!-- <div class="mb-4">

                            <label
                                for="color"
                                class="form-label fw-semibold"
                            >
                                Category Color
                            </label>

                            <div class="d-flex align-items-center gap-3">

                                <input
                                    type="color"
                                    name="color"
                                    id="color"
                                    class="form-control form-control-color"
                                    value="/**< e(
                                        $_POST['color'] ?? '#0d6efd'
                                    ) ?>"
                                    title="Choose category color"
                                >

                                <span
                                    id="colorValue"
                                    class="text-muted small"
                                >
                                    < e(
                                        $_POST['color'] ?? '#0d6efd'
                                    ) ?>
                                </span>

                            </div>

                        </div> -->


                        <!-- Actions -->

                        <div
                            class="d-flex justify-content-between
                                   align-items-center gap-2"
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
                                class="btn btn-primary"
                            >

                                <i class="bi bi-check-lg me-1"></i>

                                Save Category

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
document.getElementById('color').addEventListener('input', function () {
    document.getElementById('colorValue').textContent = this.value;
});
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * Admin Add Skill View
 */

$title = 'Add Skill';
$activeTab = 'admin';
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

                <a href="<?= url('Admin', 'skills') ?>">
                    Skills
                </a>

            </li>

            <li class="breadcrumb-item active">
                Add Skill
            </li>

        </ol>

    </nav>


    <div class="row justify-content-center">

        <div class="col-lg-7 col-xl-6">

            <div class="card shadow-sm border-0">

                <!-- =================================================
                     HEADER
                ================================================== -->

                <div class="card-header bg-primary text-white py-3">

                    <h2 class="h5 mb-0">

                        <i class="bi bi-plus-circle me-2"></i>

                        Add Master Skill

                    </h2>

                </div>


                <!-- =================================================
                     BODY
                ================================================== -->

                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <form
                        action="<?= url('Admin', 'addSkill') ?>"
                        method="POST"
                    >

                        <!-- =================================================
                             SKILL NAME
                        ================================================== -->

                        <div class="mb-4">

                            <label
                                for="name"
                                class="form-label fw-semibold"
                            >
                                Skill Name
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
                                placeholder="e.g. Web Development"
                            >

                            <div class="form-text">
                                Enter the name of the skill you want
                                to make available on SkillSwap.
                            </div>

                        </div>


                        <!-- =================================================
                             CATEGORY
                        ================================================== -->

                        <div class="mb-4">

                            <label
                                for="category_id"
                                class="form-label fw-semibold"
                            >
                                Category
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="category_id"
                                id="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Select Category --
                                </option>

                                <?php foreach ($categories ?? [] as $cat): ?>

                                    <option
                                        value="<?= (int) $cat['id'] ?>"
                                        <?= (
                                            (string) (
                                                $_POST['category_id'] ?? ''
                                            )
                                            ===
                                            (string) $cat['id']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= e($cat['name']) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

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
                                placeholder="Briefly describe this skill..."
                            ><?= e($_POST['description'] ?? '') ?></textarea>

                            <div class="form-text">
                                Maximum 500 characters.
                            </div>

                        </div>


                        <!-- =================================================
                             ACTIONS
                        ================================================== -->

                        <div
                            class="d-flex justify-content-between
                                   align-items-center gap-2"
                        >

                            <a
                                href="<?= url('Admin', 'skills') ?>"
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

                                Save Skill

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
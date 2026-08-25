<?php
/**
 * Admin Edit Skill View
 */

$title = 'Edit Skill';
$activeTab = 'admin';

$skillId = (int) ($skill['id'] ?? 0);

$skillName = $skill['name'] ?? '';
$categoryId = (int) ($skill['category_id'] ?? 0);
$description = $skill['description'] ?? '';
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
                <a href="<?= url('Admin', 'skills') ?>">
                    Skills
                </a>
            </li>

            <li class="breadcrumb-item active">
                Edit Skill
            </li>

        </ol>

    </nav>


    <div class="row justify-content-center">

        <div class="col-lg-7 col-xl-6">

            <div class="card shadow-sm border-0">

                <!-- Header -->

                <div class="card-header bg-primary text-white py-3">

                    <h2 class="h5 mb-0">

                        <i class="bi bi-pencil-square me-2"></i>

                        Edit Skill

                    </h2>

                </div>


                <!-- Body -->

                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <?php if ($skillId <= 0): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-triangle me-2"></i>

                            Invalid skill.

                        </div>

                    <?php else: ?>


                        <form
                            action="<?= url(
                                'Admin',
                                'editSkill',
                                ['id' => $skillId]
                            ) ?>"
                            method="POST"
                        >


                            <input
                                type="hidden"
                                name="id"
                                value="<?= $skillId ?>"
                            >


                            <!-- Skill Name -->

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
                                    value="<?= e($skillName) ?>"
                                >

                            </div>


                            <!-- Category -->

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

                                        <?php
                                        $catId = (int) ($cat['id'] ?? 0);
                                        ?>

                                        <option
                                            value="<?= $catId ?>"
                                            <?= $categoryId === $catId
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($cat['name']) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

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
                                ><?= e($description) ?></textarea>

                            </div>


                            <!-- Actions -->

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

                                    Update Skill

                                </button>

                            </div>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
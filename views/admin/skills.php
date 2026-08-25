<?php

/**
 * Admin Skills Management
 *  @var array $offset 
 * @var array
 * 
 */

$title = 'Manage Skills';
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
                <i class="bi bi-lightbulb text-primary me-2"></i>
                Manage Skills
            </h1>

            <p class="text-muted mb-0">
                Manage skills available on the SkillSwap platform.
            </p>

        </div>


        <div class="col-md-4 text-md-end mt-3 mt-md-0">

            <a
                href="<?= url('Admin', 'addSkill') ?>"
                class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Skill
            </a>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         SKILLS TABLE
    ========================================================== -->

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        All Skills
                    </h5>

                    <?php if (isset($skills)): ?>

                        <small class="text-muted">
                            <?= count($skills) ?>
                            skill<?= count($skills) !== 1 ? 's' : '' ?>
                        </small>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>

                        <th class="text-center">
                            S/N
                        </th>

                        <th>
                            Skill
                        </th>

                        <th>
                            Category
                        </th>

                        <th class="text-center">
                            Users
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($skills)): ?>

                        <?php foreach ($skills as $index => $skill): ?>

                            <?php

                            $skillId = (int) ($skill['id'] ?? 0);

                            $skillName =
                                $skill['name']
                                ?? 'Unknown Skill';

                            $categoryName =
                                $skill['category_name']
                                ?? 'Uncategorized';

                            $userCount =
                                (int) (
                                    $skill['user_count']
                                    ?? 0
                                );

                            $isActive =
                                !empty($skill['is_active']);

                            ?>


                            <tr>

                                <!-- S/N -->
                                <td class="text-center">
                                    <?= $offset + $index + 1 ?>
                                </td>


                                <!-- Skill -->

                                <td>

                                    <div class="fw-semibold">

                                        <?= e($skillName) ?>

                                    </div>

                                </td>


                                <!-- Category -->

                                <td>

                                    <span class="badge bg-secondary">

                                        <?= e($categoryName) ?>

                                    </span>

                                </td>


                                <!-- Users -->

                                <td class="text-center">

                                    <span class="fw-semibold">

                                        <?= $userCount ?>

                                    </span>

                                </td>


                                <!-- Status -->

                                <td class="text-center">

                                    <?php if ($isActive): ?>

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Active

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Inactive

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- Actions -->

                                <td class="text-center">

                                    <div
                                        class="btn-group"
                                        role="group">

                                        <!-- Edit -->

                                        <a
                                            href="<?= url(
                                                        'Admin',
                                                        'editSkill',
                                                        ['id' => $skillId]
                                                    ) ?>"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Edit Skill">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>


                                        <!-- Activate / Deactivate -->

                                        <?php if ($skillId > 0): ?>
                                            <form
                                                action="<?= url('Admin', 'toggleSkill') ?>"
                                                method="POST"
                                                class="d-inline">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= $skillId ?>">

                                                <?php
                                                $buttonClass = $isActive
                                                    ? 'warning'
                                                    : 'success';

                                                $buttonTitle = $isActive
                                                    ? 'Deactivate Skill'
                                                    : 'Activate Skill';

                                                $actionText = $isActive
                                                    ? 'Deactivate'
                                                    : 'Activate';

                                                $buttonIcon = $isActive
                                                    ? 'toggle-off'
                                                    : 'toggle-on';
                                                ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-<?= $buttonClass ?>"
                                                    title="<?= e($buttonTitle) ?>"
                                                    onclick="return confirm('<?= e($actionText) ?> this skill?');">

                                                    <i class="bi bi-<?= $buttonIcon ?>"></i>

                                                </button>

                                            </form>
                                            <!-- Delete -->
                                            <form
                                                action="<?= url('Admin', 'deleteSkill') ?>"
                                                method="POST"
                                                class="d-inline">
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= $skillId ?>">

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Skill"
                                                    onclick="return confirm(
            'Are you sure you want to delete this skill? This will make the skill inactive.'
        );">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <!-- No Skills -->

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="bi bi-lightbulb fs-1 d-block mb-3"></i>

                                    <h5>
                                        No skills found
                                    </h5>

                                    <p class="mb-0">
                                        There are currently no skills
                                        available.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>
            <?php if ($totalPages > 1): ?>

                <nav
                    class="mt-4 text-white"
                    aria-label="Skills pagination ">

                    <ul class="pagination justify-content-center">

                        <!-- Previous -->

                        <?php if ($currentPage > 1): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="<?= url(
                                                'Admin',
                                                'skills',
                                                [
                                                    'page' => $currentPage - 1,
                                                    'search' => $search ?? '',
                                                    'category' => $categoryId ?? 0
                                                ]
                                            ) ?>"
                                    aria-label="Previous">

                                    <i class="bi bi-chevron-left"></i>

                                </a>

                            </li>

                        <?php endif; ?>


                        <!-- Page Numbers -->

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li
                                class="page-item  <?= $currentPage == $i
                                                        ? 'active'
                                                        : '' ?>">

                                <a
                                    class="page-link text-dark fw-semibold"
                                    href="<?= url(
                                                'Admin',
                                                'skills',
                                                [
                                                    'page' => $i,
                                                    'search' => $search ?? '',
                                                    'category' => $categoryId ?? 0
                                                ]
                                            ) ?>">
                                    <?= $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>


                        <!-- Next -->

                        <?php if ($currentPage < $totalPages): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="<?= url(
                                                'Admin',
                                                'skills',
                                                [
                                                    'page' => $currentPage + 1,
                                                    'search' => $search ?? '',
                                                    'category' => $categoryId ?? 0
                                                ]
                                            ) ?>"
                                    aria-label="Next">

                                    <i class="bi bi-chevron-right"></i>

                                </a>

                            </li>

                        <?php endif; ?>

                    </ul>

                </nav>

            <?php endif; ?>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * ============================================================================
 * Skills Index View
 * ============================================================================
 * 
 * Displays user's offered and wanted skills with management options.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::index():
// $userSkills, $wantedSkills, $categories, $masterSkills

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-tools me-2 text-primary"></i>My Skills</h2>
        <div>
            <a href="<?php echo url('Skill', 'addWanted'); ?>" class="btn btn-warning">
                <i class="bi bi-plus-lg me-2"></i>Add Wanted
            </a>
            <a href="<?php echo url('Skill', 'add'); ?>" class="btn btn-success">
                <i class="bi bi-plus-lg me-2"></i>Add Skill
            </a>
        </div>
    </div>

    <!-- Skills Offered -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-tools me-2 text-success"></i>Skills I Offer</h5>
        </div>
        <div class="card-body">
            <?php if (empty($userSkills)): ?>
                <div class="empty-state py-5">
                    <i class="bi bi-tools"></i>
                    <h5>No skills added yet</h5>
                    <p class="text-muted">Add skills you can teach others and get matched!</p>
                    <a href="<?php echo url('Skill', 'add'); ?>" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Add Your First Skill
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Skill</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Experience</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userSkills as $skill): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($skill['skill_name']); ?></strong>
                                        <?php if (!empty($skill['description'])): ?>
                                            <br><small class="text-muted"><?php echo e(truncate($skill['description'], 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi <?php echo e($skill['category_icon'] ?? 'bi-grid'); ?> me-1"></i>
                                            <?php echo e($skill['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo experienceBadge($skill['experience_level']); ?></td>
                                    <td>
                                        <?php if ($skill['years_of_experience'] > 0): ?>
                                            <?php echo $skill['years_of_experience']; ?> years
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo url('Skill', 'edit', ['id' => $skill['id']]); ?>" 
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo url('Skill', 'delete', ['id' => $skill['id']]); ?>" 
                                                class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-confirm="Are you sure you want to delete this skill?">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Skills Wanted -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-book me-2 text-warning"></i>Skills I Want to Learn</h5>
        </div>
        <div class="card-body">
            <?php if (empty($wantedSkills)): ?>
                <div class="empty-state py-5">
                    <i class="bi bi-book"></i>
                    <h5>No wanted skills yet</h5>
                    <p class="text-muted">Add skills you want to learn to find better matches!</p>
                    <a href="<?php echo url('Skill', 'addWanted'); ?>" class="btn btn-warning">
                        <i class="bi bi-plus-lg me-2"></i>Add Wanted Skill
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Skill</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Urgency</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wantedSkills as $skill): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($skill['skill_name']); ?></strong>
                                        <?php if (!empty($skill['description'])): ?>
                                            <br><small class="text-muted"><?php echo e(truncate($skill['description'], 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi <?php echo e($skill['category_icon'] ?? 'bi-grid'); ?> me-1"></i>
                                            <?php echo e($skill['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo experienceBadge($skill['experience_level']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $skill['urgency'] === 'High' ? 'bg-danger' : 
                                                ($skill['urgency'] === 'Medium' ? 'bg-warning text-dark' : 'bg-info'); 
                                        ?>">
                                            <?php echo $skill['urgency']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo url('Skill', 'editWanted', ['id' => $skill['id']]); ?>" 
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo url('Skill', 'deleteWanted', ['id' => $skill['id']]); ?>" 
                                                class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-confirm="Are you sure you want to delete this wanted skill?">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
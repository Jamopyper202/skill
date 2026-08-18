<?php
/**
 * ============================================================================
 * Edit Skill View (Offered)
 * ============================================================================
 * 
 * Form for editing an existing offered skill.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::edit():
// $userSkill, $errors

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Skill: <?php echo e($userSkill['skill_name']); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Skill', 'edit', ['id' => $userSkill['id']]); ?>">
                        <div class="mb-3">
                            <label class="form-label">Skill</label>
                            <input type="text" class="form-control" value="<?php echo e($userSkill['skill_name']); ?>" disabled>
                            <small class="text-muted">Skill name cannot be changed. Delete and re-add if needed.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" value="<?php echo e($userSkill['category_name']); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Your Experience Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="experience_level" name="experience_level" required>
                                <option value="Beginner" <?php echo ($userSkill['experience_level'] === 'Beginner') ? 'selected' : ''; ?>>Beginner - Basic understanding</option>
                                <option value="Intermediate" <?php echo ($userSkill['experience_level'] === 'Intermediate') ? 'selected' : ''; ?>>Intermediate - Some practical experience</option>
                                <option value="Advanced" <?php echo ($userSkill['experience_level'] === 'Advanced') ? 'selected' : ''; ?>>Advanced - Extensive experience</option>
                                <option value="Expert" <?php echo ($userSkill['experience_level'] === 'Expert') ? 'selected' : ''; ?>>Expert - Can teach others professionally</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="years_of_experience" class="form-label">Years of Experience</label>
                            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" 
                                value="<?php echo e(old('years_of_experience', $userSkill['years_of_experience'])); ?>" min="0" max="50">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                placeholder="Describe your experience with this skill..."
                                data-max-length="500" data-counter="#desc-counter"><?php echo e(old('description', $userSkill['description'])); ?></textarea>
                            <small class="text-muted" id="desc-counter">0 / 500</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Skill', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
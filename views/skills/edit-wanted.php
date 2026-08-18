<?php
/**
 * ============================================================================
 * Edit Wanted Skill View
 * ============================================================================
 * 
 * Form for editing an existing wanted skill.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::editWanted():
// $wantedSkill, $errors

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Wanted Skill: <?php echo e($wantedSkill['skill_name']); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Skill', 'editWanted', ['id' => $wantedSkill['id']]); ?>">
                        <div class="mb-3">
                            <label class="form-label">Skill</label>
                            <input type="text" class="form-control" value="<?php echo e($wantedSkill['skill_name']); ?>" disabled>
                            <small class="text-muted">Skill name cannot be changed. Delete and re-add if needed.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" value="<?php echo e($wantedSkill['category_name']); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Desired Experience Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="experience_level" name="experience_level" required>
                                <option value="Beginner" <?php echo ($wantedSkill['experience_level'] === 'Beginner') ? 'selected' : ''; ?>>Beginner - Just starting out</option>
                                <option value="Intermediate" <?php echo ($wantedSkill['experience_level'] === 'Intermediate') ? 'selected' : ''; ?>>Intermediate - Some knowledge</option>
                                <option value="Advanced" <?php echo ($wantedSkill['experience_level'] === 'Advanced') ? 'selected' : ''; ?>>Advanced - Want to master</option>
                                <option value="Expert" <?php echo ($wantedSkill['experience_level'] === 'Expert') ? 'selected' : ''; ?>>Expert - Professional level</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="urgency" class="form-label">Urgency</label>
                            <select class="form-select" id="urgency" name="urgency">
                                <option value="Low" <?php echo ($wantedSkill['urgency'] === 'Low') ? 'selected' : ''; ?>>Low - Whenever possible</option>
                                <option value="Medium" <?php echo ($wantedSkill['urgency'] === 'Medium') ? 'selected' : ''; ?>>Medium - Within a few weeks</option>
                                <option value="High" <?php echo ($wantedSkill['urgency'] === 'High') ? 'selected' : ''; ?>>High - As soon as possible</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Why do you want to learn this?</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                placeholder="Explain why you want to learn this skill..."
                                data-max-length="500" data-counter="#desc-counter"><?php echo e(old('description', $wantedSkill['description'])); ?></textarea>
                            <small class="text-muted" id="desc-counter">0 / 500</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Skill', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
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
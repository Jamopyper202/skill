<?php
/**
 * ============================================================================
 * Add Wanted Skill View
 * ============================================================================
 * 
 * Form for adding a new skill that the user wants to learn.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::addWanted():
// $errors, $categories, $masterSkills

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2 text-warning"></i>Add Skill You Want to Learn</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Skill', 'addWanted'); ?>">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select a category...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo old('category_id') == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="skill_id" class="form-label">Skill <span class="text-danger">*</span></label>
                            <select class="form-select" id="skill_id" name="skill_id" required disabled>
                                <option value="">Select category first</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Desired Experience Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="experience_level" name="experience_level" required>
                                <option value="">Select desired level...</option>
                                <option value="Beginner" <?php echo old('experience_level') === 'Beginner' ? 'selected' : ''; ?>>Beginner - Just starting out</option>
                                <option value="Intermediate" <?php echo old('experience_level') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate - Some knowledge</option>
                                <option value="Advanced" <?php echo old('experience_level') === 'Advanced' ? 'selected' : ''; ?>>Advanced - Want to master</option>
                                <option value="Expert" <?php echo old('experience_level') === 'Expert' ? 'selected' : ''; ?>>Expert - Professional level</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="urgency" class="form-label">Urgency</label>
                            <select class="form-select" id="urgency" name="urgency">
                                <option value="Low" <?php echo old('urgency') === 'Low' ? 'selected' : ''; ?>>Low - Whenever possible</option>
                                <option value="Medium" <?php echo old('urgency', 'Medium') === 'Medium' ? 'selected' : ''; ?>>Medium - Within a few weeks</option>
                                <option value="High" <?php echo old('urgency') === 'High' ? 'selected' : ''; ?>>High - As soon as possible</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Why do you want to learn this?</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                placeholder="Explain why you want to learn this skill and what you hope to achieve..."
                                data-max-length="500" data-counter="#desc-counter"><?php echo old('description'); ?></textarea>
                            <small class="text-muted" id="desc-counter">0 / 500</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Skill', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-2"></i>Add Wanted Skill
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const skillSelect = $('#skill_id');
        
        if (!categoryId) {
            skillSelect.html('<option value="">Select category first</option>').prop('disabled', true);
            return;
        }
        
        skillSelect.prop('disabled', true).html('<option>Loading...</option>');
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>/ajax/skills.php',
            type: 'GET',
            data: {
                action: 'get_by_category',
                category_id: categoryId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Skill</option>';
                    response.skills.forEach(function(skill) {
                        options += `<option value="${skill.id}">${skill.name}</option>`;
                    });
                    skillSelect.html(options).prop('disabled', false);
                } else {
                    skillSelect.html('<option value="">No skills found</option>').prop('disabled', true);
                }
            },
            error: function() {
                skillSelect.html('<option value="">Error loading skills</option>').prop('disabled', true);
            }
        });
    });
});
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
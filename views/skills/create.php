<?php
/**
 * ============================================================================
 * Add Skill View (Offered)
 * ============================================================================
 * 
 * Form for adding a new skill that the user offers to teach.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from SkillController::add():
// $errors, $categories, $masterSkills

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>Add Skill You Offer</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Skill', 'add'); ?>">
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
                            <small class="text-muted">Choose a skill from our list or select "Other" to suggest a new one</small>
                        </div>

                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Your Experience Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="experience_level" name="experience_level" required>
                                <option value="">Select your level...</option>
                                <option value="Beginner" <?php echo old('experience_level') === 'Beginner' ? 'selected' : ''; ?>>Beginner - Basic understanding</option>
                                <option value="Intermediate" <?php echo old('experience_level') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate - Some practical experience</option>
                                <option value="Advanced" <?php echo old('experience_level') === 'Advanced' ? 'selected' : ''; ?>>Advanced - Extensive experience</option>
                                <option value="Expert" <?php echo old('experience_level') === 'Expert' ? 'selected' : ''; ?>>Expert - Can teach others professionally</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="years_of_experience" class="form-label">Years of Experience</label>
                            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" 
                                value="<?php echo old('years_of_experience', '0'); ?>" min="0" max="50">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                placeholder="Describe your experience with this skill, projects you've worked on, etc..."
                                data-max-length="500" data-counter="#desc-counter"><?php echo old('description'); ?></textarea>
                            <small class="text-muted" id="desc-counter">0 / 500</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Skill', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-2"></i>Add Skill
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
    // Load skills when category changes
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
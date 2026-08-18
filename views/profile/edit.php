<?php
/**
 * ============================================================================
 * Profile Edit View
 * ============================================================================
 * 
 * Form for editing user profile information and uploading profile picture.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// Variables available from ProfileController::edit():
// $profile, $errors

require_once BASE_PATH . '/views/layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Profile', 'edit'); ?>" enctype="multipart/form-data">
                        <!-- Profile Picture -->
                        <div class="text-center mb-4">
                            <img src="<?php echo uploadUrl($profile['profile_picture']); ?>" 
                                alt="Current Profile Picture" 
                                class="avatar-lg mb-3"
                                id="picture-preview">
                            <div>
                                <label for="profile_picture" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-camera me-1"></i>Change Picture
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" 
                                    class="d-none" accept="image/jpeg,image/png,image/gif"
                                    data-preview="#picture-preview"
                                    onchange="document.getElementById('upload-form').submit();">
                            </div>
                            <small class="text-muted d-block mt-1">Max 2MB (JPG, PNG, GIF)</small>
                        </div>

                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                    value="<?php echo e(old('full_name', $profile['full_name'])); ?>" required>
                            </div>

                            <!-- Email (Read-only) -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                    value="<?php echo e($profile['email']); ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="<?php echo e(old('location', $profile['location'] ?? '')); ?>"
                                    placeholder="City, Country">
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                    value="<?php echo e(old('phone', $profile['phone'] ?? '')); ?>"
                                    placeholder="+1 234 567 8900">
                            </div>

                            <!-- Experience Level -->
                            <div class="col-md-6">
                                <label for="experience_level" class="form-label">Experience Level</label>
                                <select class="form-select" id="experience_level" name="experience_level" required>
                                    <option value="Beginner" <?php echo (old('experience_level', $profile['experience_level'] ?? '') === 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="Intermediate" <?php echo (old('experience_level', $profile['experience_level'] ?? '') === 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="Advanced" <?php echo (old('experience_level', $profile['experience_level'] ?? '') === 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                                    <option value="Expert" <?php echo (old('experience_level', $profile['experience_level'] ?? '') === 'Expert') ? 'selected' : ''; ?>>Expert</option>
                                </select>
                            </div>

                            <!-- Availability -->
                            <div class="col-md-6">
                                <label for="availability" class="form-label">Availability</label>
                                <select class="form-select" id="availability" name="availability" required>
                                    <option value="Full-time" <?php echo (old('availability', $profile['availability'] ?? '') === 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                                    <option value="Part-time" <?php echo (old('availability', $profile['availability'] ?? '') === 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                                    <option value="Weekends only" <?php echo (old('availability', $profile['availability'] ?? '') === 'Weekends only') ? 'selected' : ''; ?>>Weekends only</option>
                                    <option value="Flexible" <?php echo (old('availability', $profile['availability'] ?? '') === 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                                </select>
                            </div>

                            <!-- Bio -->
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" 
                                    placeholder="Tell others about yourself, your background, and what you're looking for..."
                                    data-max-length="500" data-counter="#bio-counter"><?php echo e(old('bio', $profile['bio'] ?? '')); ?></textarea>
                                <small class="text-muted" id="bio-counter">0 / 500</small>
                            </div>

                            <!-- Social Links -->
                            <div class="col-md-4">
                                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-linkedin"></i></span>
                                    <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                        value="<?php echo e(old('linkedin_url', $profile['linkedin_url'] ?? '')); ?>"
                                        placeholder="https://linkedin.com/in/...">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="github_url" class="form-label">GitHub URL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-github"></i></span>
                                    <input type="url" class="form-control" id="github_url" name="github_url" 
                                        value="<?php echo e(old('github_url', $profile['github_url'] ?? '')); ?>"
                                        placeholder="https://github.com/...">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="website_url" class="form-label">Website URL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="url" class="form-control" id="website_url" name="website_url" 
                                        value="<?php echo e(old('website_url', $profile['website_url'] ?? '')); ?>"
                                        placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('Profile', 'index'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>

                    <!-- Hidden form for picture upload -->
                    <form id="upload-form" method="POST" action="<?php echo url('Profile', 'uploadPicture'); ?>" 
                        enctype="multipart/form-data" class="d-none">
                        <input type="file" name="profile_picture" id="hidden-picture-input" 
                            onchange="this.form.submit();">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
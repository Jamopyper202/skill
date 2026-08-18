<?php
/**
 * ============================================================================
 * Register View
 * ============================================================================
 * 
 * User registration page with form validation.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// This view is loaded by AuthController::register()
// Variables available: $errors (array)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?php echo e(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .register-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
        }
        .register-logo {
            font-size: 3rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card register-card p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus register-logo"></i>
                        <h3 class="mt-3 mb-1">Create Account</h3>
                        <p class="text-muted small">Join <?php echo e(APP_NAME); ?> today</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <?php echo displayErrors($errors); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo url('Auth', 'register'); ?>">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                    value="<?php echo old('full_name'); ?>" 
                                    placeholder="Enter your full name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?php echo old('email'); ?>" 
                                    placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="experience_level" class="form-label">Current Experience Level</label>
                            <select class="form-select" id="experience_level" name="experience_level" required>
                                <option value="">Select your level...</option>
                                <option value="Beginner" <?php echo old('experience_level') === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                                <option value="Intermediate" <?php echo old('experience_level') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="Advanced" <?php echo old('experience_level') === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                                <option value="Expert" <?php echo old('experience_level') === 'Expert' ? 'selected' : ''; ?>>Expert</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                    placeholder="Minimum 6 characters" required minlength="6">
                            </div>
                            <div class="form-text">Must be at least 6 characters long.</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                    placeholder="Confirm your password" required>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Already have an account? 
                                <a href="<?php echo url('Auth', 'login'); ?>" class="text-decoration-none fw-bold">
                                    Sign In
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-3 text-white">
                    <p class="small mb-0">
                        <i class="bi bi-shield-check me-1"></i>
                        Passwords are securely hashed with bcrypt
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
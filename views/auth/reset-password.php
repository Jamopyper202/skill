<?php
/**
 * ============================================================================
 * Reset Password View
 * ============================================================================
 * 
 * Password reset confirmation page.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// This view is loaded by AuthController::resetPassword()
// Variables available: $errors (array), $success (bool), $token (string)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?php echo e(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
        }
        .reset-icon {
            font-size: 3rem;
            color: #198754;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card reset-card p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock reset-icon"></i>
                        <h3 class="mt-3 mb-1">Reset Password</h3>
                        <p class="text-muted small">Enter your new password</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Your password has been reset successfully!
                        </div>
                        <div class="d-grid">
                            <a href="<?php echo url('Auth', 'login'); ?>" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
                            </a>
                        </div>
                    <?php else: ?>

                        <?php if (!empty($errors)): ?>
                            <?php echo displayErrors($errors); ?>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo url('Auth', 'resetPassword') . '&token=' . urlencode($token); ?>">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                        placeholder="Minimum 6 characters" required minlength="6" autofocus>
                                </div>
                                <div class="form-text">Must be at least 6 characters long.</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                        placeholder="Confirm your password" required>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-lg me-2"></i>Reset Password
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="<?php echo url('Auth', 'login'); ?>" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Login
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
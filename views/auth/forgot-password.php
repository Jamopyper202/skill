<?php
/**
 * ============================================================================
 * Forgot Password View
 * ============================================================================
 * 
 * Password reset request page.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

// This view is loaded by AuthController::forgotPassword()
// Variables available: $errors (array), $success (bool)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | <?php echo e(APP_NAME); ?></title>
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
        .forgot-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
        }
        .forgot-icon {
            font-size: 3rem;
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card forgot-card p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-key forgot-icon"></i>
                        <h3 class="mt-3 mb-1">Forgot Password?</h3>
                        <p class="text-muted small">Enter your email to reset your password</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            If an account exists with that email, you will receive password reset instructions.
                        </div>

                        <?php if (isset($_SESSION['reset_link'])): ?>
                            <div class="alert alert-info">
                                <p class="mb-2"><strong>Development Mode:</strong></p>
                                <p class="small mb-2">In production, an email would be sent. For this demo, use the link below:</p>
                                <a href="<?php echo e($_SESSION['reset_link']); ?>" class="btn btn-sm btn-outline-primary w-100">
                                    Reset Password Link
                                </a>
                                <?php 
                                unset($_SESSION['reset_link']);
                                unset($_SESSION['reset_email']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="<?php echo url('Auth', 'login'); ?>" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    <?php else: ?>

                        <?php if (!empty($errors)): ?>
                            <?php echo displayErrors($errors); ?>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo url('Auth', 'forgotPassword'); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="<?php echo old('email'); ?>" 
                                        placeholder="Enter your email" required autofocus>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-send me-2"></i>Send Reset Link
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
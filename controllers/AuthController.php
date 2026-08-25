<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Profile.php';
/**
 * ============================================================================
 * Auth Controller
 * ============================================================================
 * 
 * Handles user authentication: login, register, logout, forgot password,
 * and reset password functionality.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */

class AuthController {
    /**
     * User model instance
     * @var User
     */
    private User $userModel;

    /**
     * Profile model instance
     * @var Profile
     */
    private Profile $profileModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->userModel = new User();
        $this->profileModel = new Profile();
    }

    /**
     * =========================================================================
     * LOGIN PAGE
     * =========================================================================
     * 
     * Display login form and handle login submission.
     */
    
  public function login(): void
{
    // Redirect if already logged in
    if (isLoggedIn()) {

        if (($_SESSION['user_role'] ?? 'user') === 'admin') {
            redirect(url('Admin', 'index'));
        } else {
            redirect(url('Dashboard', 'index'));
        }

        return;
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);


        // =========================================================
        // VALIDATION
        // =========================================================

        if (!isRequired($email)) {

            $errors[] = 'Email is required.';

        } elseif (!isValidEmail($email)) {

            $errors[] = 'Please enter a valid email address.';
        }


        if (!isRequired($password)) {

            $errors[] = 'Password is required.';
        }


        // =========================================================
        // LOGIN
        // =========================================================

        if (empty($errors)) {

            $user = $this->userModel->findByEmail($email);


            if (
                $user &&
                $this->userModel->verifyPassword(
                    $password,
                    $user['password']
                )
            ) {

                // =====================================================
                // CHECK ACCOUNT STATUS
                // =====================================================

                if (!$user['is_active']) {

                    $errors[] =
                        'Your account has been deactivated. Please contact support.';

                } else {

                    // =================================================
                    // SET SESSION
                    // =================================================

                    $_SESSION['user_id'] =
                        $user['id'];

                    $_SESSION['user_name'] =
                        $user['full_name'];

                    $_SESSION['user_email'] =
                        $user['email'];

                    $_SESSION['user_role'] =
                        $user['role'];

                    $_SESSION['user_picture'] =
                        $user['profile_picture']
                        ?? 'download.png';

                    $_SESSION['user_active'] =
                        $user['is_active'];


                    // =================================================
                    // UPDATE LAST LOGIN
                    // =================================================

                    $this->userModel->updateLastLogin(
                        $user['id']
                    );


                    // =================================================
                    // WELCOME MESSAGE
                    // =================================================

                    flash(
                        'Welcome back, ' .
                        $user['full_name'] .
                        '!',
                        'success'
                    );


                    // =================================================
                    // ROLE-BASED REDIRECT
                    // =================================================

                    if ($user['role'] === 'admin') {

                        // Admin → Admin Dashboard
                        $redirect = url(
                            'Admin',
                            'index'
                        );

                    } else {

                        // Normal User → User Dashboard
                        $redirect = $_SESSION[
                            'redirect_after_login'
                        ] ?? url(
                            'Dashboard',
                            'index'
                        );
                    }


                    // Remove intended redirect
                    unset(
                        $_SESSION['redirect_after_login']
                    );


                    redirect($redirect);

                    return;
                }

            } else {

                $errors[] =
                    'Invalid email or password.';
            }
        }


        // =========================================================
        // STORE OLD INPUT
        // =========================================================

        storeOldInput([
            'email' => $email
        ]);
    }


    setPageTitle('Login');

    require_once BASE_PATH .
        '/views/auth/login.php';
}
    /**
     * =========================================================================
     * REGISTER PAGE
     * =========================================================================
     * 
     * Display registration form and handle registration submission.
     */
    public function register(): void {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect(url('Dashboard', 'index'));
            return;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $experienceLevel = $_POST['experience_level'] ?? 'Beginner';

            // Validation
            if (!isRequired($fullName)) {
                $errors[] = 'Full name is required.';
            } elseif (strlen($fullName) < 3) {
                $errors[] = 'Full name must be at least 3 characters.';
            }

            if (!isRequired($email)) {
                $errors[] = 'Email is required.';
            } elseif (!isValidEmail($email)) {
                $errors[] = 'Please enter a valid email address.';
            } elseif ($this->userModel->emailExists($email)) {
                $errors[] = 'This email is already registered.';
            }

            if (!isRequired($password)) {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                // Create user
                $userId = $this->userModel->create($fullName, $email, $password);

                if ($userId) {
                    // Create profile
                    $this->profileModel->create(
                        $userId,
                        '',
                        '',
                        '',
                        $experienceLevel,
                        'Flexible'
                    );

                    flash('Registration successful! Please login.', 'success');
                    redirect(url('Auth', 'login'));
                    return;
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }

            // Store old input on error
            storeOldInput([
                'full_name' => $fullName,
                'email' => $email,
                'experience_level' => $experienceLevel
            ]);
        }

        setPageTitle('Register');
        require_once BASE_PATH . '/views/auth/register.php';
    }

    /**
     * =========================================================================
     * LOGOUT
     * =========================================================================
     * 
     * Destroy session and redirect to login.
     */
    public function logout(): void {
        // Clear all session data
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        flash('You have been logged out successfully.', 'info');
        redirect(url('Auth', 'login'));
    }

    /**
     * =========================================================================
     * FORGOT PASSWORD
     * =========================================================================
     * 
     * Display forgot password form and handle submission.
     */
    public function forgotPassword(): void {
        if (isLoggedIn()) {
            redirect(url('Dashboard', 'index'));
            return;
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (!isRequired($email)) {
                $errors[] = 'Email is required.';
            } elseif (!isValidEmail($email)) {
                $errors[] = 'Please enter a valid email address.';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if ($user) {
                    // Generate reset token
                    $token = $this->userModel->createResetToken($user['id']);
                    
                    // In a real application, you would send an email here
                    // For this school project, we display the reset link
                    $resetLink = url('Auth', 'resetPassword') . '&token=' . $token;
                    
                    // Store reset link in session for display
                    $_SESSION['reset_link'] = $resetLink;
                    $_SESSION['reset_email'] = $email;
                    
                    $success = true;
                } else {
                    // Don't reveal if email exists for security
                    $success = true;
                }
            }
        }

        setPageTitle('Forgot Password');
        require_once BASE_PATH . '/views/auth/forgot-password.php';
    }

    /**
     * =========================================================================
     * RESET PASSWORD
     * =========================================================================
     * 
     * Display reset password form and handle submission.
     */
    public function resetPassword(): void {
        if (isLoggedIn()) {
            redirect(url('Dashboard', 'index'));
            return;
        }

        $token = $_GET['token'] ?? '';
        $errors = [];
        $success = false;

        // Verify token
        $userId = $this->userModel->verifyResetToken($token);

        if (!$userId) {
            flash('Invalid or expired reset link. Please request a new one.', 'danger');
            redirect(url('Auth', 'forgotPassword'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!isRequired($password)) {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                if ($this->userModel->updatePassword($userId, $password)) {
                    // Clear reset token
                    $this->userModel->clearResetToken();
                    
                    $success = true;
                    flash('Password reset successful! Please login with your new password.', 'success');
                } else {
                    $errors[] = 'Failed to reset password. Please try again.';
                }
            }
        }

        setPageTitle('Reset Password');
        require_once BASE_PATH . '/views/auth/reset-password.php';
    }
}
?>
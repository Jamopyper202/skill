<?php
/**
 * ============================================================================
 * Layout Footer
 * ============================================================================
 * 
 * Common footer section included at the bottom of every page.
 * Closes the main content, loads Bootstrap JS, and includes custom scripts.
 * 
 * @author     B.Sc Computer Science Student
 * @project    SkillSwap - Digital Skill Marketplace
 * @year       Final Year Project
 * ============================================================================
 */
?>
    </main><!-- End of main content -->

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="mb-3">
                        <i class="bi bi-arrow-left-right me-2"></i>
                        <?php echo e(APP_NAME); ?>
                    </h5>
                    <p class="text-muted small">
                        <?php echo e(APP_DESCRIPTION); ?>
                    </p>
                    <p class="text-muted small mb-0">
                        Version <?php echo e(APP_VERSION); ?>
                    </p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <?php if (isLoggedIn()): ?>
                            <li><a href="<?php echo url('Dashboard', 'index'); ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li><a href="<?php echo url('Skill', 'browse'); ?>" class="text-decoration-none text-muted">Browse Skills</a></li>
                            <li><a href="<?php echo url('Match', 'index'); ?>" class="text-decoration-none text-muted">Find Matches</a></li>
                            <li><a href="<?php echo url('Profile', 'index'); ?>" class="text-decoration-none text-muted">My Profile</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo url('Auth', 'login'); ?>" class="text-decoration-none text-muted">Login</a></li>
                            <li><a href="<?php echo url('Auth', 'register'); ?>" class="text-decoration-none text-muted">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="mb-3">Contact</h6>
                    <ul class="list-unstyled small text-muted">
                        <li><i class="bi bi-envelope me-2"></i>support@skillswap.com</li>
                        <li><i class="bi bi-geo-alt me-2"></i>University Campus</li>
                        <li><i class="bi bi-clock me-2"></i>Mon - Fri, 9AM - 5PM</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-3">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo e(APP_NAME); ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small text-muted mb-0">
                        B.Sc Computer Science Final Year Project
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    
    <!-- Auto-hide flash messages after 5 seconds -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow', function() {
                    $(this).alert('close');
                });
            }, 5000);
        });
    </script>
</body>
</html>
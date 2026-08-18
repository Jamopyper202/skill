<?php

/**
 * ============================================================================
 * SkillSwap Layout Footer
 * ============================================================================
 */
?>

</main><!-- End of main content -->


<!-- ========================================================================
     FOOTER
     ======================================================================== -->

<footer class="footer">

    <div class="container">

        <div class="row g-4">


            <!-- ==============================================================
                 BRAND
                 ============================================================== -->

            <div class="col-lg-5 col-md-6">

                <a
                    href="<?php echo url('Dashboard', 'index'); ?>"
                    class="text-decoration-none d-inline-flex align-items-center mb-3">

                    <span
                        class="d-flex align-items-center justify-content-center me-2"
                        style="
                            width: 42px;
                            height: 42px;
                            border-radius: 11px;
                            background: var(--primary-color);
                            color: white;
                        ">
                        <i class="bi bi-arrow-left-right fs-5"></i>
                    </span>

                    <span
                        class="fw-bold fs-4 text-white">
                        <?php echo e(APP_NAME); ?>
                    </span>

                </a>


                <p class="mb-3" style="max-width: 430px;">
                    <?php echo e(APP_DESCRIPTION); ?>
                </p>


                <!-- Social / Platform Icons -->

                <div class="d-flex gap-2">

                    <a
                        href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle"
                        style="
                            width: 38px;
                            height: 38px;
                            background: rgba(255,255,255,.08);
                        "
                        aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a
                        href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle"
                        style="
                            width: 38px;
                            height: 38px;
                            background: rgba(255,255,255,.08);
                        "
                        aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a
                        href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle"
                        style="
                            width: 38px;
                            height: 38px;
                            background: rgba(255,255,255,.08);
                        "
                        aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>

                    <a
                        href="#"
                        class="d-flex align-items-center justify-content-center rounded-circle"
                        style="
                            width: 38px;
                            height: 38px;
                            background: rgba(255,255,255,.08);
                        "
                        aria-label="GitHub">
                        <i class="bi bi-github"></i>
                    </a>

                </div>

            </div>


            <!-- ==============================================================
                 QUICK LINKS
                 ============================================================== -->

            <div class="col-lg-2 col-md-6">

                <h5>Quick Links</h5>

                <ul class="list-unstyled mb-0">

                    <?php if (isLoggedIn()): ?>

                        <li class="mb-2">
                            <a href="<?php echo url('Dashboard', 'index'); ?>">
                                Dashboard
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Skill', 'browse'); ?>">
                                Browse Skills
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Match', 'index'); ?>">
                                Find Matches
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Exchange', 'index'); ?>">
                                Exchanges
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="mb-2">
                            <a href="<?php echo url('Auth', 'login'); ?>">
                                Login
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Auth', 'register'); ?>">
                                Create Account
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>

            </div>


            <!-- ==============================================================
                 ACCOUNT
                 ============================================================== -->

            <div class="col-lg-2 col-md-6">

                <h5>Account</h5>

                <ul class="list-unstyled mb-0">

                    <?php if (isLoggedIn()): ?>

                        <li class="mb-2">
                            <a href="<?php echo url('Profile', 'index'); ?>">
                                My Profile
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Message', 'index'); ?>">
                                Messages
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Review', 'index'); ?>">
                                My Reviews
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Notification', 'index'); ?>">
                                Notifications
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="mb-2">
                            <a href="<?php echo url('Auth', 'login'); ?>">
                                Sign In
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="<?php echo url('Auth', 'register'); ?>">
                                Get Started
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>

            </div>


            <!-- ==============================================================
                 CONTACT
                 ============================================================== -->

            <div class="col-lg-3 col-md-6">

                <h5>Get in Touch</h5>

                <ul class="list-unstyled mb-0">

                    <li class="d-flex align-items-start mb-3">

                        <i class="bi bi-envelope me-3 mt-1"></i>

                        <div>
                            <small class="d-block text-white">
                                Email
                            </small>

                            <a href="mailto:programminglanguage293@gmail.com" class="text-decoration-none">
                                support@skillswap.com
                            </a>
                        </div>

                    </li>


                    <li class="d-flex align-items-start mb-3">

                        <i class="bi bi-geo-alt me-3 mt-1"></i>

                        <div>
                            <small class="d-block text-white">
                                Location
                            </small>

                            <span>
                            Skill Street, Knowledge City, KS 12345
                            </span>
                        </div>

                    </li>


                    <li class="d-flex align-items-start">

                        <i class="bi bi-clock me-3 mt-1"></i>

                        <div>
                            <small class="d-block text-white">
                                Support Hours
                            </small>

                            <span>
                                Mon - Fri, 9AM - 5PM
                            </span>
                        </div>

                    </li>

                </ul>

            </div>

        </div>


        <!-- ==================================================================
             FOOTER BOTTOM
             ================================================================== -->

        <div class="footer-bottom">

            <div class="row align-items-center">

                <div class="col-md-6 text-center text-md-start">

                    <p class="small mb-0">

                        &copy;
                        <?php echo date('Y'); ?>

                        <?php echo e(APP_NAME); ?>.

                        All rights reserved.

                    </p>

                </div>


                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">

                    <p class="small mb-0">

                        Built with
                        <i class="bi bi-heart-fill text-danger mx-1"></i>

                        for skill sharing

                    </p>

                </div>

            </div>

        </div>


        <!-- Version -->

        <div class="text-center mt-3">

            <small style="color: #6b7280;">
                Version <?php echo e(APP_VERSION); ?>
            </small>

        </div>

    </div>

</footer>


<!-- ========================================================================
     JAVASCRIPT
     ======================================================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo asset('js/main.js'); ?>"></script>


<!-- Auto-hide flash messages -->

<script>
    $(document).ready(function() {

        setTimeout(function() {

            $('.alert-dismissible').fadeOut(
                'slow',
                function() {

                    $(this).alert('close');

                }
            );

        }, 5000);

    });
</script>

</body>

</html>
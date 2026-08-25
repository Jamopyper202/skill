<?php
                    /**
                     * Admin Settings View
                     */
                    $title = 'App Settings';
                    $activeTab = 'admin';
                    ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0"><i class="fas fa-cog text-primary me-2"></i>App Settings</h1>
            <p class="text-muted mb-0">Configure platform-wide settings</p>
        </div>

    </div>

    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="row g-4">

        <div class="col-lg-8">
            <form action="url('Admin', 'settings')" method="POST">


                <!-- General Settings -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0"><i class="fas fa-sliders-h text-primary me-2"></i>General Settings</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="site_name" class="form-label fw-bold">Site Name</label>
                            <input type="text" name="site_name" id="site_name" class="form-control"
                                value="<?= e($settings['site_name'] ?? 'Skill Exchange') ?>" required>
                        </div>
                        <div class="mb-3">

                            <label
                                for="site_description"
                                class="form-label fw-bold">
                                Site Description
                            </label>

                            <textarea
                                name="site_description"
                                id="site_description"
                                rows="3"
                                class="form-control"><?= e(
                                                            $settings['site_description'] ?? ''
                                                        ) ?></textarea>

                        </div>
                        <div class="mb-3">
                            <label for="site_tagline" class="form-label fw-bold">Tagline</label>
                            <input type="text" name="site_tagline" id="site_tagline" class="form-control"
                                value="<?= e($settings['site_tagline'] ?? 'Learn. Share. Grow.') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="contact_email" class="form-label fw-bold">Contact Email</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control"
                                value="<?= e($settings['contact_email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <!-- Items Per Page -->
                <div class="mb-3">

                    <label
                        for="items_per_page"
                        class="form-label fw-bold">
                        Items Per Page
                    </label>

                    <input
                        type="number"
                        name="items_per_page"
                        id="items_per_page"
                        class="form-control"
                        value="<?= (int) (
                                    $settings['items_per_page'] ?? 10
                                ) ?>"
                        min="1"
                        max="100">

                    <div class="form-text">
                        Number of records displayed per page in admin lists.
                    </div>

                </div>
                <!-- User Settings -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0"><i class="fas fa-users text-success me-2"></i>User Settings</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="max_skills_per_user" class="form-label fw-bold">Max Skills Per User</label>
                            <input type="number" name="max_skills_per_user" id="max_skills_per_user" class="form-control"
                                value="<?= $settings['max_skills_per_user'] ?? 10 ?>" min="1" max="50">
                        </div>
                        <div class="mb-3">
                            <label for="max_portfolio_items" class="form-label fw-bold">Max Portfolio Items</label>
                            <input type="number" name="max_portfolio_items" id="max_portfolio_items" class="form-control"
                                value="<?= $settings['max_portfolio_items'] ?? 10 ?>" min="1" max="50">
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_registration" id="allow_registration"
                                value="1" <?= ($settings['allow_registration'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="allow_registration">Allow New Registrations</label>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="require_email_verification" id="require_email_verification"
                                value="1" <?= ($settings['require_email_verification'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="require_email_verification">Require Email Verification</label>
                        </div>
                    </div>
                </div>

                <!-- Exchange Settings -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0"><i class="fas fa-exchange-alt text-info me-2"></i>Exchange Settings</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="max_active_exchanges" class="form-label fw-bold">Max Active Exchanges Per User</label>
                            <input type="number" name="max_active_exchanges" id="max_active_exchanges" class="form-control"
                                value="<?= $settings['max_active_exchanges'] ?? 5 ?>" min="1" max="20">
                        </div>
                        <div class="mb-3">
                            <label for="exchange_expiry_days" class="form-label fw-bold">Exchange Request Expiry (Days)</label>
                            <input type="number" name="exchange_expiry_days" id="exchange_expiry_days" class="form-control"
                                value="<?= $settings['exchange_expiry_days'] ?? 7 ?>" min="1" max="30">
                            <div class="form-text">Pending exchange requests will expire after this many days.</div>
                        </div>
                    </div>
                </div>
                <!-- EXCHANGE -->
                <div class="mb-3">

                    <label
                        for="match_threshold"
                        class="form-label fw-bold">
                        Match Threshold
                    </label>

                    <input
                        type="number"
                        name="match_threshold"
                        id="match_threshold"
                        class="form-control"
                        value="<?= (int) (
                                    $settings['match_threshold'] ?? 30
                                ) ?>"
                        min="0"
                        max="100">

                    <div class="form-text">
                        Minimum matching score required to display a skill match.
                    </div>

                </div>

                <!-- Content Settings -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0"><i class="fas fa-shield-alt text-warning me-2"></i>Content & Moderation</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="enable_reviews" id="enable_reviews"
                                value="1" <?= ($settings['enable_reviews'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_reviews">Enable Reviews & Ratings</label>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="enable_reports" id="enable_reports"
                                value="1" <?= ($settings['enable_reports'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="enable_reports">Enable User Reports</label>
                        </div>
                        <div class="mb-3">
                            <label for="moderation_keywords" class="form-label fw-bold">Moderation Keywords</label>
                            <textarea name="moderation_keywords" id="moderation_keywords" rows="3" class="form-control"
                                placeholder="spam, scam, offensive words..."><?= e($settings['moderation_keywords'] ?? '') ?></textarea>
                            <div class="form-text">Comma-separated list of words to flag for moderation.</div>
                        </div>
                    </div>
                </div>
                <!-- Maintenance Mode -->
                <div class="mb-3 form-check form-switch">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="maintenance_mode"
                        id="maintenance_mode"
                        value="1"
                        <?= ($settings['maintenance_mode'] ?? 0)
                            ? 'checked'
                            : '' ?>>

                    <label
                        class="form-check-label"
                        for="maintenance_mode">
                        Enable Maintenance Mode
                    </label>

                    <div class="form-text">
                        Prevent normal users from accessing the platform
                        while maintenance is being performed.
                    </div>

                </div>

                <div class="d-flex justify-content-between">
                    <a href="/admin" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Platform Info</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">PHP Version</dt>
                        <dd class="col-sm-6"><?= phpversion() ?></dd>
                        <dt class="col-sm-6">Database</dt>
                        <dd class="col-sm-6">MySQL</dd>
                        <dt class="col-sm-6">Total Users</dt>
                        <dd class="col-sm-6"><?= $stats['total_users'] ?? 0 ?></dd>
                        <dt class="col-sm-6">Total Exchanges</dt>
                        <dd class="col-sm-6"><?= $stats['total_exchanges'] ?? 0 ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * Create Report View
 * @var array $reportedUser 
 * 
 */

$title = 'Report User';
$activeTab = 'reports';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="<?= url('Dashboard', 'index') ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Report User
                    </li>

                </ol>

            </nav>


            <div class="card shadow border-0">

                <!-- Header -->
                <div class="card-header bg-danger text-white">

                    <h2 class="h4 mb-0">
                        <i class="fas fa-flag me-2"></i>
                        Report a User
                    </h2>

                </div>


                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <!-- Warning -->
                    <div class="alert alert-warning">

                        <i class="fas fa-exclamation-triangle me-2"></i>

                        <strong>Please use this responsibly.</strong>

                        Reports are reviewed by our moderation team.
                        False reports may result in account suspension.

                    </div>


                    <!-- Reported User -->
                    <div class="card bg-light border-0 mb-4">

                        <div class="card-body d-flex align-items-center">

                            <img
                                src="<?= e(uploadUrl($reportedUser['avatar'] ?? '')) ?>"
                                alt="<?= e($reportedUser['full_name'] ?? 'User') ?>"
                                class="rounded-circle me-3"
                                width="56"
                                height="56"
                                style="object-fit: cover;">

                            <div>

                                <h5 class="mb-1">

                                    Reporting:

                                    <?= e(
                                        $reportedUser['full_name']
                                        ?? 'Unknown User'
                                    ) ?>

                                </h5>

                                <?php if (!empty($reportedUser['username'])): ?>

                                    <p class="text-muted mb-0">
                                        @<?= e($reportedUser['username']) ?>
                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>


                    <!-- Report Form -->
                    <form
                        action="<?= url('Report', 'create', [
                            'user_id' => $reportedUser['id']
                        ]) ?>"
                        method="POST">

                      


                        <!-- Reason -->
                        <div class="mb-3">

                            <label
                                for="reason"
                                class="form-label fw-bold">

                                Reason for Report *

                            </label>

                            <select
                                name="reason"
                                id="reason"
                                class="form-select"
                                required>

                                <option value="">
                                    -- Select a reason --
                                </option>

                                <option value="harassment">
                                    Harassment or Bullying
                                </option>

                                <option value="inappropriate_content">
                                    Inappropriate Content
                                </option>

                                <option value="spam">
                                    Spam or Scam
                                </option>

                                <option value="fake_profile">
                                    Fake Profile
                                </option>

                                <option value="no_show">
                                    Did Not Show Up / Abandoned Exchange
                                </option>

                                <option value="misrepresentation">
                                    Misrepresented Skills
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- Description -->
                        <div class="mb-4">

                            <label
                                for="description"
                                class="form-label fw-bold">

                                Description *

                            </label>

                            <textarea
                                name="description"
                                id="description"
                                rows="5"
                                class="form-control"
                                required
                                minlength="20"
                                maxlength="2000"
                                placeholder="Please provide specific details about the incident. Include dates, messages, or any evidence that supports your report."><?= e(old('description') ?? '') ?></textarea>

                            <div class="form-text">

                                The more details you provide,
                                the better we can investigate.

                            </div>

                        </div>


                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">

                            <a
                                href="javascript:history.back()"
                                class="btn btn-outline-secondary">

                                <i class="fas fa-arrow-left me-1"></i>
                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="btn btn-danger btn-lg">

                                <i class="fas fa-flag me-2"></i>
                                Submit Report

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
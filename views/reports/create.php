<?php
/**
 * Create Report View
 */
$title = 'Report User';
$activeTab = 'reports';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Report User</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-danger text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-flag me-2"></i>Report a User</h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please use this responsibly.</strong> Reports are reviewed by our moderation team. False reports may result in account suspension.
                    </div>

                    <!-- Reported User Info -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body d-flex align-items-center">
                            <img src="<?= getAvatarUrl($reportedUser['avatar'] ?? null) ?>" 
                                 alt="" class="rounded-circle me-3" width="56" height="56">
                            <div>
                                <h5 class="mb-1">Reporting: <?= e($reportedUser['name'] ?? 'Unknown User') ?></h5>
                                <p class="text-muted mb-0">@<?= e($reportedUser['username'] ?? 'unknown') ?></p>
                            </div>
                        </div>
                    </div>

                    <form action="/reports/create" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="reported_user_id" value="<?= $reportedUser['id'] ?? 0 ?>">

                        <div class="mb-3">
                            <label for="reason" class="form-label fw-bold">Reason for Report *</label>
                            <select name="reason" id="reason" class="form-select" required>
                                <option value="">-- Select a reason --</option>
                                <option value="harassment">Harassment or Bullying</option>
                                <option value="inappropriate_content">Inappropriate Content</option>
                                <option value="spam">Spam or Scam</option>
                                <option value="fake_profile">Fake Profile</option>
                                <option value="no_show">Did Not Show Up / Abandoned Exchange</option>
                                <option value="misrepresentation">Misrepresented Skills</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description *</label>
                            <textarea name="description" id="description" rows="5" class="form-control" 
                                required maxlength="2000" placeholder="Please provide specific details about the incident. Include dates, messages, or any evidence that supports your report."></textarea>
                            <div class="form-text">The more details you provide, the better we can investigate.</div>
                        </div>

                        <div class="mb-4">
                            <label for="exchange_id" class="form-label fw-bold">Related Exchange <span class="text-muted fw-normal">(Optional)</span></label>
                            <select name="exchange_id" id="exchange_id" class="form-select">
                                <option value="">-- Select an exchange --</option>
                                <?php foreach ($myExchanges ?? [] as $ex): ?>
                                <option value="<?= $ex['id'] ?>">
                                    #<?= $ex['id'] ?> - <?= e($ex['offered_skill_name']) ?> ↔ <?= e($ex['wanted_skill_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-flag me-2"></i>Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * Create Review View
 * Form to write a review for a completed exchange
 */

$title = 'Write a Review';
$activeTab = 'reviews';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/exchanges/history">History</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Write Review</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-star me-2"></i>Write a Review
                    </h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <!-- Exchange Summary -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="<?= getAvatarUrl($reviewee['avatar'] ?? null) ?>" 
                                     alt="<?= e($reviewee['name'] ?? 'User') ?>" 
                                     class="rounded-circle me-3" width="64" height="64">
                                <div>
                                    <h5 class="mb-1">Reviewing: <?= e($reviewee['name'] ?? 'Unknown User') ?></h5>
                                    <p class="text-muted mb-1">@<?= e($reviewee['username'] ?? 'unknown') ?></p>
                                    <?php if (!empty($exchange)): ?>
                                    <p class="small text-muted mb-0">
                                        <span class="badge bg-primary"><?= e($exchange['offered_skill_name'] ?? 'Skill') ?></span>
                                        <i class="fas fa-arrows-alt-h mx-1"></i>
                                        <span class="badge bg-success"><?= e($exchange['wanted_skill_name'] ?? 'Skill') ?></span>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="/reviews/create" method="POST" id="reviewForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="exchange_id" value="<?= $exchangeId ?? '' ?>">
                        <input type="hidden" name="reviewee_id" value="<?= $revieweeId ?? '' ?>">

                        <!-- Star Rating -->
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold d-block mb-3">How would you rate this exchange?</label>
                            <div class="star-rating" id="starRating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" class="btn btn-link p-1 star-btn" data-rating="<?= $i ?>" 
                                    style="font-size: 2.5rem; color: #ddd; transition: color 0.2s;">
                                    <i class="fas fa-star"></i>
                                </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="0" required>
                            <div id="ratingLabel" class="text-muted mt-2">Click a star to rate</div>
                        </div>

                        <!-- Review Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">
                                <i class="fas fa-comment-alt me-1"></i>Your Review
                            </label>
                            <textarea name="comment" id="comment" rows="5" class="form-control" 
                                placeholder="Share your experience with this skill exchange. What did you learn? How was the teaching? Would you recommend this person?"
                                required maxlength="1000"></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Be honest and constructive. Your feedback helps the community.</span>
                                <span id="charCount">0 / 1000</span>
                            </div>
                        </div>

                        <!-- Quick Tags -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tags me-1"></i>Quick Tags <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $tags = ['Knowledgeable', 'Patient', 'Punctual', 'Friendly', 'Professional', 'Clear Communicator', 'Well Prepared', 'Encouraging']; ?>
                                <?php foreach ($tags as $tag): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm tag-btn" data-tag="<?= e($tag) ?>">
                                    <?= e($tag) ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="tags" id="tagsInput" value="">
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="/exchanges/history" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-paper-plane me-2"></i>Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Star rating
const starBtns = document.querySelectorAll('.star-btn');
const ratingInput = document.getElementById('ratingInput');
const ratingLabel = document.getElementById('ratingLabel');
const submitBtn = document.getElementById('submitBtn');
const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

starBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        ratingInput.value = rating;
        ratingLabel.textContent = labels[rating];
        ratingLabel.className = 'mt-2 fw-bold text-primary';
        submitBtn.disabled = false;
        
        starBtns.forEach((b, i) => {
            if (i < rating) {
                b.style.color = '#ffc107';
                b.querySelector('i').classList.remove('far');
                b.querySelector('i').classList.add('fas');
            } else {
                b.style.color = '#ddd';
                b.querySelector('i').classList.remove('fas');
                b.querySelector('i').classList.add('far');
            }
        });
    });
    
    btn.addEventListener('mouseenter', function() {
        const hoverRating = parseInt(this.dataset.rating);
        starBtns.forEach((b, i) => {
            b.style.color = i < hoverRating ? '#ffc107' : '#ddd';
        });
    });
});

document.getElementById('starRating').addEventListener('mouseleave', function() {
    const currentRating = parseInt(ratingInput.value) || 0;
    starBtns.forEach((b, i) => {
        b.style.color = i < currentRating ? '#ffc107' : '#ddd';
    });
});

// Character count
document.getElementById('comment').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length + ' / 1000';
});

// Tag selection
const selectedTags = new Set();
document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.dataset.tag;
        if (selectedTags.has(tag)) {
            selectedTags.delete(tag);
            this.classList.remove('btn-secondary');
            this.classList.add('btn-outline-secondary');
        } else {
            selectedTags.add(tag);
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-secondary');
        }
        document.getElementById('tagsInput').value = Array.from(selectedTags).join(',');
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

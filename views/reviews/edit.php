<?php
/**
 * Edit Review View
 */
$title = 'Edit Review';
$activeTab = 'reviews';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/reviews">My Reviews</a></li>
                    <li class="breadcrumb-item active">Edit Review</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0"><i class="fas fa-edit me-2"></i>Edit Review</h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <form action="/reviews/edit/<?= $review['id'] ?>" method="POST" id="reviewForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold d-block mb-3">Your Rating</label>
                            <div class="star-rating" id="starRating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" class="btn btn-link p-1 star-btn" data-rating="<?= $i ?>" 
                                    style="font-size: 2.5rem; color: <?= $i <= ($review['rating'] ?? 0) ? '#ffc107' : '#ddd' ?>;">
                                    <i class="fas fa-star"></i>
                                </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="<?= $review['rating'] ?? 0 ?>" required>
                            <div id="ratingLabel" class="mt-2 fw-bold text-primary">
                                <?= ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][$review['rating'] ?? 0] ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">Your Review</label>
                            <textarea name="comment" id="comment" rows="5" class="form-control" 
                                required maxlength="1000"><?= e($review['comment'] ?? '') ?></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Be honest and constructive.</span>
                                <span id="charCount"><?= strlen($review['comment'] ?? '') ?> / 1000</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/reviews" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Update Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const starBtns = document.querySelectorAll('.star-btn');
const ratingInput = document.getElementById('ratingInput');
const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

starBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        ratingInput.value = rating;
        document.getElementById('ratingLabel').textContent = labels[rating];
        starBtns.forEach((b, i) => {
            b.style.color = i < rating ? '#ffc107' : '#ddd';
        });
    });
});

document.getElementById('comment').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length + ' / 1000';
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
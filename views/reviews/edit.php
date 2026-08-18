<?php
/**
 * Edit Review View
 *  * @var array $otherProfile,$match   
 * @var array $review
 */

$title = 'Edit Review';
$activeTab = 'reviews';

$currentRating = (int) ($review['rating'] ?? 0);

$ratingLabels = [
    0 => 'Select a rating',
    1 => 'Poor',
    2 => 'Fair',
    3 => 'Good',
    4 => 'Very Good',
    5 => 'Excellent'
];

$currentRatingLabel = $ratingLabels[$currentRating] ?? 'Select a rating';
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
                        <a href="<?php echo url('Dashboard', 'index'); ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="<?php echo url('Review', 'index'); ?>">
                            My Reviews
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Edit Review
                    </li>

                </ol>

            </nav>


            <div class="card shadow border-0">

                <!-- Header -->
                <div class="card-header bg-warning text-dark">

                    <h2 class="h4 mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Review
                    </h2>

                </div>


                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <form
                        action="<?php echo url('Review', 'edit', [
                            'id' => $review['id']
                        ]); ?>"
                        method="POST"
                        id="reviewForm"
                    >

                        <!-- Rating -->
                        <div class="mb-4 text-center">

                            <label class="form-label fw-bold d-block mb-3">
                                Your Rating
                            </label>


                            <div
                                class="star-rating"
                                id="starRating"
                            >

                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                    <button
                                        type="button"
                                        class="btn btn-link p-1 star-btn"
                                        data-rating="<?php echo $i; ?>"
                                        style="
                                            font-size: 2.5rem;
                                            color: <?php echo $i <= $currentRating
                                                ? '#ffc107'
                                                : '#ddd'; ?>;
                                        "
                                    >

                                        <i
                                            class="bi <?php echo $i <= $currentRating
                                                ? 'bi-star-fill'
                                                : 'bi-star'; ?>"
                                        ></i>

                                    </button>

                                <?php endfor; ?>

                            </div>


                            <input
                                type="hidden"
                                name="rating"
                                id="ratingInput"
                                value="<?php echo $currentRating; ?>"
                                required
                            >


                            <div
                                id="ratingLabel"
                                class="mt-2 fw-bold text-primary"
                            >
                                <?php echo e($currentRatingLabel); ?>
                            </div>

                        </div>


                        <!-- Comment -->
                        <div class="mb-4">

                            <label
                                for="comment"
                                class="form-label fw-bold"
                            >
                                <i class="bi bi-chat-text me-1"></i>
                                Your Review
                            </label>


                            <textarea
                                name="comment"
                                id="comment"
                                rows="5"
                                class="form-control"
                                required
                                minlength="10"
                                maxlength="1000"
                            ><?php echo e($review['comment'] ?? ''); ?></textarea>


                            <div class="form-text d-flex justify-content-between">

                                <span>
                                    Be honest and constructive.
                                </span>

                                <span id="charCount">
                                    <?php echo strlen($review['comment'] ?? ''); ?>
                                    / 1000
                                </span>

                            </div>

                        </div>


                        <!-- Actions -->
                        <div class="d-flex justify-content-between">

                            <a
                                href="<?php echo url('Review', 'index'); ?>"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancel
                            </a>


                            <button
                                type="submit"
                                class="btn btn-warning btn-lg"
                            >
                                <i class="bi bi-save me-2"></i>
                                Update Review
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Star Rating
|--------------------------------------------------------------------------
*/

const starBtns =
    document.querySelectorAll('.star-btn');

const ratingInput =
    document.getElementById('ratingInput');

const ratingLabel =
    document.getElementById('ratingLabel');

const labels = [
    'Select a rating',
    'Poor',
    'Fair',
    'Good',
    'Very Good',
    'Excellent'
];


starBtns.forEach(function (btn) {

    btn.addEventListener('click', function () {

        const rating =
            parseInt(this.dataset.rating);

        ratingInput.value = rating;

        ratingLabel.textContent =
            labels[rating];


        starBtns.forEach(function (star, index) {

            const icon =
                star.querySelector('i');

            if (index < rating) {

                star.style.color = '#ffc107';

                icon.classList.remove('bi-star');

                icon.classList.add('bi-star-fill');

            } else {

                star.style.color = '#ddd';

                icon.classList.remove('bi-star-fill');

                icon.classList.add('bi-star');

            }

        });

    });


    /*
     * Hover effect
     */
    btn.addEventListener('mouseenter', function () {

        const hoverRating =
            parseInt(this.dataset.rating);

        starBtns.forEach(function (star, index) {

            star.style.color =
                index < hoverRating
                    ? '#ffc107'
                    : '#ddd';

        });

    });

});


/*
|--------------------------------------------------------------------------
| Restore selected rating after mouse leaves
|--------------------------------------------------------------------------
*/

document
    .getElementById('starRating')
    .addEventListener('mouseleave', function () {

        const rating =
            parseInt(ratingInput.value) || 0;

        starBtns.forEach(function (star, index) {

            star.style.color =
                index < rating
                    ? '#ffc107'
                    : '#ddd';

        });

    });


/*
|--------------------------------------------------------------------------
| Character Counter
|--------------------------------------------------------------------------
*/

document
    .getElementById('comment')
    .addEventListener('input', function () {

        document.getElementById('charCount').textContent =
            this.value.length + ' / 1000';

    });

</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
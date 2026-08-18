<?php
/**
 * Create Review
 * Form to write a review for a completed exchange
 */

$title = 'Write a Review';
$activeTab = 'reviews';

// Safe values
$exchangeId = (int) ($exchangeId ?? ($_GET['exchange_id'] ?? 0));
$revieweeId = (int) ($revieweeId ?? 0);

$revieweeName = $reviewee['full_name'] ?? 'Unknown User';
$revieweePicture = $reviewee['profile_picture'] ?? 'download.png';
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
                        <a href="<?php echo url('Exchange', 'history'); ?>">
                            History
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Write Review
                    </li>

                </ol>

            </nav>


            <div class="card shadow border-0">

                <!-- Header -->
                <div class="card-header bg-warning text-dark">

                    <h2 class="h4 mb-0">
                        <i class="bi bi-star-fill me-2"></i>
                        Write a Review
                    </h2>

                </div>


                <div class="card-body p-4">

                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


                    <!-- Reviewee / Exchange Summary -->
                    <div class="card bg-light border-0 mb-4">

                        <div class="card-body">

                            <div class="d-flex align-items-center">

                                <!-- User Avatar -->
                                <img
                                    src="<?php echo e(uploadUrl($revieweePicture)); ?>"
                                    alt="<?php echo e($revieweeName); ?>"
                                    class="rounded-circle me-3"
                                    width="64"
                                    height="64"
                                    style="object-fit: cover;"
                                >


                                <div>

                                    <h5 class="mb-1">
                                        Reviewing:
                                        <?php echo e($revieweeName); ?>
                                    </h5>

                                    <p class="text-muted mb-1">
                                        Your skill exchange partner
                                    </p>


                                    <?php if (!empty($exchange)): ?>

                                        <p class="small text-muted mb-0">

                                            <?php if (!empty($exchange['offered_skill_name'])): ?>

                                                <span class="badge bg-primary">
                                                    <?php echo e(
                                                        $exchange['offered_skill_name']
                                                    ); ?>
                                                </span>

                                            <?php endif; ?>


                                            <i class="bi bi-arrow-left-right mx-1"></i>


                                            <?php if (!empty($exchange['requested_skill_name'])): ?>

                                                <span class="badge bg-success">
                                                    <?php echo e(
                                                        $exchange['requested_skill_name']
                                                    ); ?>
                                                </span>

                                            <?php endif; ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Review Form -->
                    <form
                        action="<?php echo url('Review', 'create', [
                            'exchange_id' => $exchangeId
                        ]); ?>"
                        method="POST"
                        id="reviewForm"
                    >

                        <!-- Exchange ID -->
                        <input
                            type="hidden"
                            name="exchange_id"
                            value="<?php echo $exchangeId; ?>"
                        >

                        <!-- Reviewee ID -->
                        <input
                            type="hidden"
                            name="reviewee_id"
                            value="<?php echo $revieweeId; ?>"
                        >


                        <!-- Rating -->
                        <div class="mb-4 text-center">

                            <label class="form-label fw-bold d-block mb-3">
                                How would you rate this exchange?
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
                                            color: #ddd;
                                            transition: color .2s;
                                        "
                                    >
                                        <i class="bi bi-star-fill"></i>
                                    </button>

                                <?php endfor; ?>

                            </div>


                            <input
                                type="hidden"
                                name="rating"
                                id="ratingInput"
                                value="0"
                                required
                            >


                            <div
                                id="ratingLabel"
                                class="text-muted mt-2"
                            >
                                Click a star to rate
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
                                placeholder="Share your experience with this skill exchange. What did you learn? How was the teaching? Would you recommend this person?"
                                required
                                minlength="10"
                                maxlength="1000"
                            ></textarea>


                            <div class="form-text d-flex justify-content-between">

                                <span>
                                    Be honest and constructive.
                                </span>

                                <span id="charCount">
                                    0 / 1000
                                </span>

                            </div>

                        </div>


                        <!-- Quick Tags -->
                        <!--
                            Tags are kept in the interface for now.
                            The current Review model does not save tags
                            to the database.
                        -->
                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                <i class="bi bi-tags me-1"></i>

                                Quick Tags

                                <span class="text-muted fw-normal">
                                    (Optional)
                                </span>

                            </label>


                            <div class="d-flex flex-wrap gap-2">

                                <?php
                                $tags = [
                                    'Knowledgeable',
                                    'Patient',
                                    'Punctual',
                                    'Friendly',
                                    'Professional',
                                    'Clear Communicator',
                                    'Well Prepared',
                                    'Encouraging'
                                ];
                                ?>


                                <?php foreach ($tags as $tag): ?>

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm tag-btn"
                                        data-tag="<?php echo e($tag); ?>"
                                    >
                                        <?php echo e($tag); ?>
                                    </button>

                                <?php endforeach; ?>

                            </div>


                            <input
                                type="hidden"
                                name="tags"
                                id="tagsInput"
                                value=""
                            >

                        </div>


                        <!-- Actions -->
                        <div class="d-flex justify-content-between">

                            <a
                                href="<?php echo url('Exchange', 'history'); ?>"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancel
                            </a>


                            <button
                                type="submit"
                                class="btn btn-warning btn-lg"
                                id="submitBtn"
                                disabled
                            >
                                <i class="bi bi-send-fill me-2"></i>
                                Submit Review
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

const submitBtn =
    document.getElementById('submitBtn');

const labels = [
    '',
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

        ratingLabel.className =
            'mt-2 fw-bold text-primary';

        submitBtn.disabled = false;


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


document
    .getElementById('starRating')
    .addEventListener('mouseleave', function () {

        const currentRating =
            parseInt(ratingInput.value) || 0;

        starBtns.forEach(function (star, index) {

            star.style.color =
                index < currentRating
                    ? '#ffc107'
                    : '#ddd';

        });

    });


/*
|--------------------------------------------------------------------------
| Character Counter
|--------------------------------------------------------------------------
*/

const comment =
    document.getElementById('comment');

const charCount =
    document.getElementById('charCount');


comment.addEventListener('input', function () {

    charCount.textContent =
        this.value.length + ' / 1000';

});


/*
|--------------------------------------------------------------------------
| Quick Tags
|--------------------------------------------------------------------------
*/

const selectedTags = new Set();

document
    .querySelectorAll('.tag-btn')
    .forEach(function (btn) {

        btn.addEventListener('click', function () {

            const tag =
                this.dataset.tag;

            if (selectedTags.has(tag)) {

                selectedTags.delete(tag);

                this.classList.remove(
                    'btn-secondary'
                );

                this.classList.add(
                    'btn-outline-secondary'
                );

            } else {

                selectedTags.add(tag);

                this.classList.remove(
                    'btn-outline-secondary'
                );

                this.classList.add(
                    'btn-secondary'
                );

            }


            document.getElementById('tagsInput').value =
                Array.from(selectedTags).join(',');

        });

    });

</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
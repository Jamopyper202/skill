<?php
/**
 * Admin Reviews Management
 */

$title = 'Manage Reviews';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/admin-navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="row align-items-center mb-4">

        <div class="col-md-8">

            <h1 class="h2 mb-1">

                <i class="bi bi-star-fill text-warning me-2"></i>

                Manage Reviews

            </h1>

            <p class="text-muted mb-0">
                View and manage reviews submitted by SkillSwap users.
            </p>

        </div>

    </div>


    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>


    <!-- =========================================================
         REVIEWS TABLE
    ========================================================== -->

    <div class="card shadow-sm border-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>

                        <th class="text-center">
                            S/N
                        </th>

                        <th>
                            Reviewer
                        </th>

                        <th>
                            Reviewee
                        </th>

                        <th>
                            Rating
                        </th>

                        <th>
                            Comment
                        </th>

                        <th>
                            Date
                        </th>

                        <th class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($reviews)): ?>

                        <?php foreach ($reviews as $index => $review): ?>

                            <?php
                            $reviewId = (int) (
                                $review['id'] ?? 0
                            );

                            $rating = max(
                                0,
                                min(
                                    5,
                                    (int) (
                                        $review['rating']
                                        ?? 0
                                    )
                                )
                            );

                            $reviewerName =
                                $review['reviewer_name']
                                ?? 'Unknown';

                            $revieweeName =
                                $review['reviewee_name']
                                ?? 'Unknown';

                            $comment =
                                $review['comment']
                                ?? '';

                            $createdAt =
                                $review['created_at']
                                ?? null;
                            ?>


                            <tr>

                                <!-- S/N -->

                                <td class="text-center">

                                    <?= ($offset ?? 0) + $index + 1 ?>

                                </td>


                                <!-- Reviewer -->

                                <td>

                                    <span class="fw-semibold">

                                        <?= e($reviewerName) ?>

                                    </span>

                                </td>


                                <!-- Reviewee -->

                                <td>

                                    <?= e($revieweeName) ?>

                                </td>


                                <!-- Rating -->

                                <td>

                                    <span
                                        class="text-warning"
                                        aria-label="<?= $rating ?> out of 5"
                                    >
                                        <?= str_repeat(
                                            '★',
                                            $rating
                                        ) ?>
                                    </span>

                                    <span
                                        class="text-muted"
                                    >
                                        <?= str_repeat(
                                            '★',
                                            5 - $rating
                                        ) ?>
                                    </span>

                                </td>


                                <!-- Comment -->

                                <td>

                                    <?= e(
                                        truncate(
                                            $comment,
                                            50
                                        )
                                    ) ?>

                                </td>


                                <!-- Date -->

                                <td>

                                    <small class="text-muted">

                                        <?= $createdAt
                                            ? timeAgo($createdAt)
                                            : 'N/A' ?>

                                    </small>

                                </td>


                                <!-- Actions -->

                                <td class="text-center">

                                    <?php if ($reviewId > 0): ?>

                                        <form
                                            action="<?= url(
                                                'Admin',
                                                'deleteReview'
                                            ) ?>"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                'Delete this review? This action cannot be undone.'
                                            );"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $reviewId ?>"
                                            >

                                          

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Review"
                                            >

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <!-- =================================================
                             NO REVIEWS
                        ================================================== -->

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-star text-muted fs-1 d-block mb-3"
                                ></i>

                                <h5 class="text-muted">
                                    No reviews found
                                </h5>

                                <p class="text-muted mb-0">
                                    There are currently no reviews to display.
                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================================================
         PAGINATION
    ========================================================== -->

    <?php if (($totalPages ?? 1) > 1): ?>

        <nav
            class="mt-4"
            aria-label="Reviews pagination"
        >

            <ul class="pagination justify-content-center">

                <?php if (($currentPage ?? 1) > 1): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reviews',
                                [
                                    'page' =>
                                        ($currentPage ?? 1) - 1
                                ]
                            ) ?>"
                        >

                            <i class="bi bi-chevron-left"></i>

                        </a>

                    </li>

                <?php endif; ?>


                <?php for (
                    $i = 1;
                    $i <= ($totalPages ?? 1);
                    $i++
                ): ?>

                    <li
                        class="page-item
                            <?= ($currentPage ?? 1) == $i
                                ? 'active'
                                : '' ?>"
                    >

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reviews',
                                ['page' => $i]
                            ) ?>"
                        >
                            <?= $i ?>
                        </a>

                    </li>

                <?php endfor; ?>


                <?php if (
                    ($currentPage ?? 1)
                    < ($totalPages ?? 1)
                ): ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="<?= url(
                                'Admin',
                                'reviews',
                                [
                                    'page' =>
                                        ($currentPage ?? 1) + 1
                                ]
                            ) ?>"
                        >

                            <i class="bi bi-chevron-right"></i>

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </nav>

    <?php endif; ?>

</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
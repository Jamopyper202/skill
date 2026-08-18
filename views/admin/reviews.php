<?php
$title = 'Manage Reviews';
$activeTab = 'admin';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4"><i class="fas fa-star text-warning me-2"></i>Manage Reviews</h1>
    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reviewer</th>
                        <th>Reviewee</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews ?? [] as $review): ?>
                    <tr>
                        <td><?= e($review['reviewer_name']) ?></td>
                        <td><?= e($review['reviewee_name']) ?></td>
                        <td>
                            <span class="text-warning"><?= str_repeat('★', $review['rating']) ?></span>
                            <span class="text-muted"><?= str_repeat('★', 5 - $review['rating']) ?></span>
                        </td>
                        <td><?= e(truncate($review['comment'], 50)) ?></td>
                        <td><small><?= timeAgo($review['created_at']) ?></small></td>
                        <td>
                            <form action="/admin/reviews/delete/<?= $review['id'] ?>" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this review?')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php
/**
 * Edit Portfolio Item View
 */
$title = 'Edit Portfolio Project';
$activeTab = 'portfolio';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/portfolio">Portfolio</a></li>
                    <li class="breadcrumb-item active">Edit Project</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h4 mb-0"><i class="fas fa-edit me-2"></i>Edit Project</h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <form action="/portfolio/edit/<?= $item['id'] ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Project Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                value="<?= e($item['title']) ?>" required maxlength="200">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description *</label>
                            <textarea name="description" id="description" rows="4" class="form-control" 
                                required maxlength="2000"><?= e($item['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="skill_id" class="form-label fw-bold">Related Skill</label>
                            <select name="skill_id" id="skill_id" class="form-select">
                                <option value="">-- Select a skill --</option>
                                <?php foreach ($mySkills ?? [] as $skill): ?>
                                <option value="<?= $skill['id'] ?>" <?= ($item['skill_id'] ?? '') == $skill['id'] ? 'selected' : '' ?>>
                                    <?= e($skill['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="project_url" class="form-label fw-bold">Project URL</label>
                            <input type="url" name="project_url" id="project_url" class="form-control" 
                                value="<?= e($item['project_url'] ?? '') ?>" placeholder="https://example.com/project">
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-bold">Project Image</label>
                            <?php if (!empty($item['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= e($item['image']) ?>" alt="Current" class="img-thumbnail" style="max-height: 150px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                    <label class="form-check-label text-danger" for="remove_image">Remove current image</label>
                                </div>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="image" id="image" class="form-control" 
                                accept="image/jpeg,image/png,image/gif,image/webp">
                            <div class="form-text">Upload new image to replace. Max 5MB.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/portfolio" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save me-2"></i>Update Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
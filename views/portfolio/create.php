<?php
/**
 * Create Portfolio Item View
 */
$title = 'Add Portfolio Project';
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
                    <li class="breadcrumb-item active">Add Project</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-plus me-2"></i>Add Portfolio Project</h2>
                </div>
                <div class="card-body p-4">
                    <?php require_once __DIR__ . '/../layouts/flash-messages.php'; ?>

                    <form action="/portfolio/create" method="POST" enctype="multipart/form-data" id="portfolioForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Project Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                required maxlength="200" placeholder="e.g., E-commerce Website Redesign">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description *</label>
                            <textarea name="description" id="description" rows="4" class="form-control" 
                                required maxlength="2000" placeholder="Describe the project, your role, technologies used, and outcomes..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="skill_id" class="form-label fw-bold">Related Skill</label>
                            <select name="skill_id" id="skill_id" class="form-select">
                                <option value="">-- Select a skill --</option>
                                <?php foreach ($mySkills ?? [] as $skill): ?>
                                <option value="<?= $skill['id'] ?>"><?= e($skill['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Link this project to one of your skills.</div>
                        </div>

                        <div class="mb-3">
                            <label for="project_url" class="form-label fw-bold">Project URL</label>
                            <input type="url" name="project_url" id="project_url" class="form-control" 
                                placeholder="https://example.com/project">
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-bold">Project Image</label>
                            <input type="file" name="image" id="image" class="form-control" 
                                accept="image/jpeg,image/png,image/gif,image/webp">
                            <div class="form-text">Max 5MB. JPG, PNG, GIF, or WebP.</div>
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/portfolio" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
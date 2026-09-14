<?php
// Add News Article Form
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-plus-circle text-primary me-2"></i>Add News Article</h1>
    <a href="index.php?action=newsAdmin" class="btn btn-outline-secondary rounded-pill px-3">&larr; Back to News List</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <form action="index.php?action=newsAddSave" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Article Headline <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter article headline..." required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="picture" class="form-label fw-semibold">Image (JPEG/PNG) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="text" class="form-label fw-semibold">Article Body <span class="text-danger">*</span></label>
                <textarea class="form-control" id="text" name="text" rows="8" placeholder="Full article body content..." required><?= htmlspecialchars($_POST['text'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php?action=news" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Publish Article
                </button>
            </div>
        </form>
    </div>
</div>

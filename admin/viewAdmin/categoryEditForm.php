<?php
// Форма редактирования категории
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-folder-symlink text-primary me-2"></i>Редактировать категорию #<?= (int)$category['id'] ?></h1>
    <a href="index.php?action=categoryAdmin" class="btn btn-outline-secondary rounded-pill px-3">&larr; К списку категорий</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4" style="max-width: 540px;">
    <div class="card-body p-4">
        <form action="index.php?action=categoryEditSave&id=<?= (int)$category['id'] ?>" method="POST">
            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">Название категории <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? $category['name']) ?>" required>
                <div class="form-text">Название должно быть уникальным.</div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="index.php?action=categoryAdmin" class="btn btn-secondary rounded-pill px-4">Отмена</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

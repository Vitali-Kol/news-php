<?php
// Форма редактирования новости
$currentImg = !empty($news['picture']) ? 'data:image/jpeg;base64,' . base64_encode($news['picture']) : '';
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Редактировать новость #<?= (int)$news['id'] ?></h1>
        <small class="text-muted"><?= htmlspecialchars(mb_substr($news['title'], 0, 60, 'UTF-8')) ?>...</small>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=newsDetail&id=<?= (int)$news['id'] ?>" class="btn btn-outline-info rounded-pill px-3">
            <i class="bi bi-eye me-1"></i> Просмотр
        </a>
        <a href="index.php?action=newsAdmin" class="btn btn-outline-secondary rounded-pill px-3">&larr; К списку</a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Изменения успешно сохранены!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Форма редактирования -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form action="index.php?action=newsEditSave&id=<?= (int)$news['id'] ?>" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Заголовок новости <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="<?= htmlspecialchars($_POST['title'] ?? $news['title']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Категория <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"
                                    <?= ((int)$cat['id'] === (int)($_POST['category_id'] ?? $news['category_id'])) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="text" class="form-label fw-semibold">Текст новости <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="text" name="text" rows="10" required><?= htmlspecialchars($_POST['text'] ?? $news['text']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?action=newsAdmin" class="btn btn-secondary rounded-pill px-4">Отмена</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Правая панель: изображение -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-semibold py-3"><i class="bi bi-image me-2 text-primary"></i>Изображение</div>
            <div class="card-body p-3">
                <?php if (!empty($currentImg)): ?>
                    <img src="<?= $currentImg ?>" alt="Текущее изображение"
                         class="img-fluid rounded shadow-sm mb-3" style="width: 100%; max-height: 220px; object-fit: cover;">
                <?php else: ?>
                    <div class="text-center text-muted py-4"><i class="bi bi-image fs-1"></i><br><small>Нет изображения</small></div>
                <?php endif; ?>

                <form action="index.php?action=newsEditSave&id=<?= (int)$news['id'] ?>" method="POST" enctype="multipart/form-data" id="imgForm">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($news['title']) ?>">
                    <input type="hidden" name="category_id" value="<?= (int)$news['category_id'] ?>">
                    <input type="hidden" name="text" value="<?= htmlspecialchars($news['text']) ?>">
                    <label for="picture" class="form-label fw-semibold small">Загрузить новое изображение:</label>
                    <input type="file" class="form-control form-control-sm" id="picture" name="picture" accept="image/*">
                    <div class="form-text">Оставьте пустым, чтобы сохранить текущее.</div>
                </form>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-grid gap-2">
                <a href="index.php?action=newsDetail&id=<?= (int)$news['id'] ?>" class="btn btn-outline-info rounded-pill btn-sm">
                    <i class="bi bi-eye me-1"></i> Детальный просмотр
                </a>
                <a href="../index.php?action=read&id=<?= (int)$news['id'] ?>" target="_blank" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Открыть на сайте
                </a>
                <a href="index.php?action=newsDeleteForm&id=<?= (int)$news['id'] ?>" class="btn btn-outline-danger rounded-pill btn-sm">
                    <i class="bi bi-trash me-1"></i> Удалить новость
                </a>
            </div>
        </div>
    </div>
</div>

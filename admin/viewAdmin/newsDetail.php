<?php
// Детальный просмотр новости в панели управления
$currentImg = !empty($news['picture']) ? 'data:image/jpeg;base64,' . base64_encode($news['picture']) : '';
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1"><i class="bi bi-file-text text-primary me-2"></i>Просмотр новости #<?= (int)$news['id'] ?></h1>
        <span class="badge bg-secondary"><?= htmlspecialchars($news['category_name'] ?? 'Без категории') ?></span>
        <span class="badge bg-info text-dark ms-1"><i class="bi bi-person me-1"></i><?= htmlspecialchars($news['author'] ?? 'Неизвестно') ?></span>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=newsEdit&id=<?= (int)$news['id'] ?>" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-pencil me-1"></i> Редактировать
        </a>
        <a href="index.php?action=newsAdmin" class="btn btn-outline-secondary rounded-pill px-3">&larr; К списку</a>
    </div>
</div>

<div class="row g-4">
    <!-- Основной контент -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-3"><?= htmlspecialchars($news['title']) ?></h2>
                <div class="text-muted" style="line-height: 1.8; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($news['text'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Боковая панель с деталями -->
    <div class="col-lg-4">
        <!-- Изображение -->
        <?php if (!empty($currentImg)): ?>
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-semibold py-3"><i class="bi bi-image me-2 text-primary"></i>Изображение</div>
            <div class="card-body p-3 text-center">
                <img src="<?= $currentImg ?>" alt="<?= htmlspecialchars($news['title']) ?>"
                     class="img-fluid rounded shadow-sm" style="max-height: 260px; width: 100%; object-fit: cover;">
            </div>
        </div>
        <?php endif; ?>

        <!-- Мета-информация -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-semibold py-3"><i class="bi bi-info-circle me-2 text-primary"></i>Сведения о публикации</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">ID</span>
                    <strong>#<?= (int)$news['id'] ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Категория</span>
                    <span class="badge bg-secondary"><?= htmlspecialchars($news['category_name'] ?? '—') ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Автор</span>
                    <strong><?= htmlspecialchars($news['author'] ?? '—') ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Символов в тексте</span>
                    <strong><?= number_format(mb_strlen($news['text'] ?? '', 'UTF-8')) ?></strong>
                </li>
            </ul>
        </div>

        <!-- Кнопки действий -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-grid gap-2">
                <a href="index.php?action=newsEdit&id=<?= (int)$news['id'] ?>" class="btn btn-primary rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Редактировать новость
                </a>
                <a href="../index.php?action=read&id=<?= (int)$news['id'] ?>" target="_blank" class="btn btn-outline-info rounded-pill">
                    <i class="bi bi-eye me-1"></i> Просмотр на сайте
                </a>
                <a href="index.php?action=newsDeleteForm&id=<?= (int)$news['id'] ?>" class="btn btn-outline-danger rounded-pill">
                    <i class="bi bi-trash me-1"></i> Удалить новость
                </a>
            </div>
        </div>
    </div>
</div>

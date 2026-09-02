<?php
// Страница подтверждения удаления новости
$currentImg = !empty($news['picture']) ? 'data:image/jpeg;base64,' . base64_encode($news['picture']) : '';
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-danger mb-0">
            <i class="bi bi-trash me-2"></i>Удаление новости #<?= (int)$news['id'] ?>
        </h1>
        <small class="text-muted">Это действие нельзя отменить</small>
    </div>
    <a href="index.php?action=newsAdmin" class="btn btn-outline-secondary rounded-pill px-3">&larr; К списку</a>
</div>

<!-- Предупреждение -->
<div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
    <div>
        <h6 class="fw-bold mb-1">Внимание! Вы собираетесь удалить новость.</h6>
        <p class="mb-0">
            Вместе с новостью будут удалены все связанные
            <strong><?= $commentCount ?> <?= $commentCount === 1 ? 'комментарий' : ($commentCount >= 2 && $commentCount <= 4 ? 'комментария' : 'комментариев') ?></strong>.
            Восстановление данных невозможно.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Карточка с деталями удаляемой новости -->
    <div class="col-lg-8">
        <div class="card border-danger border-2 shadow-sm mb-4">
            <div class="card-header bg-danger bg-opacity-10 text-danger fw-semibold py-3">
                <i class="bi bi-newspaper me-2"></i>Данные удаляемой новости
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 140px;">ID</td>
                            <td><strong>#<?= (int)$news['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Заголовок</td>
                            <td><strong><?= htmlspecialchars($news['title']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Категория</td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($news['category_name'] ?? '—') ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Автор</td>
                            <td><?= htmlspecialchars($news['author'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Комментарии</td>
                            <td>
                                <span class="badge <?= $commentCount > 0 ? 'bg-danger' : 'bg-secondary' ?> rounded-pill">
                                    <?= $commentCount ?> комм.
                                </span>
                                <?php if ($commentCount > 0): ?>
                                    <small class="text-danger ms-2">— будут удалены</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold align-top">Текст (начало)</td>
                            <td class="text-muted small">
                                <?= htmlspecialchars(mb_substr($news['text'], 0, 200, 'UTF-8')) ?>...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Форма подтверждения удаления (POST) -->
        <form action="index.php?action=newsDelete&id=<?= (int)$news['id'] ?>" method="POST">
            <input type="hidden" name="news_id" value="<?= (int)$news['id'] ?>">
            <div class="d-flex gap-3">
                <a href="index.php?action=newsDetail&id=<?= (int)$news['id'] ?>"
                   class="btn btn-secondary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-x-lg me-1"></i> Отмена
                </a>
                <button type="submit" class="btn btn-danger rounded-pill px-5 fw-semibold shadow-sm">
                    <i class="bi bi-trash me-1"></i> Да, удалить новость
                </button>
            </div>
        </form>
    </div>

    <!-- Правая панель: изображение -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-image me-2 text-muted"></i>Изображение новости
            </div>
            <div class="card-body p-3 text-center">
                <?php if (!empty($currentImg)): ?>
                    <img src="<?= $currentImg ?>" alt="<?= htmlspecialchars($news['title']) ?>"
                         class="img-fluid rounded shadow-sm" style="width: 100%; max-height: 260px; object-fit: cover; opacity: 0.7;">
                    <p class="text-muted small mt-2 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Изображение будет удалено вместе с новостью
                    </p>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-image fs-1"></i><br>
                        <small>Нет изображения</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

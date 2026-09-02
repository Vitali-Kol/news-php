<?php
// Список всех новостей в админ-панели
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1">Управление новостями</h1>
        <p class="text-muted small mb-0">Всего публикаций: <?= count($newsList) ?></p>
    </div>
    <a href="index.php?action=newsAdd" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Добавить новость
    </a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Новость успешно добавлена!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Новость успешно обновлена!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i> Новость удалена.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 100px;">Фото</th>
                    <th>Заголовок</th>
                    <th>Категория</th>
                    <th>Автор</th>
                    <th class="text-end" style="width: 160px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($newsList)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Новостей пока нет.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($newsList as $item): 
                        $imgSrc = !empty($item['picture']) ? 'data:image/jpeg;base64,' . base64_encode($item['picture']) : 'https://via.placeholder.com/80x50?text=No+Img';
                    ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= (int)$item['id'] ?></td>
                            <td>
                                <img src="<?= $imgSrc ?>" alt="img" class="rounded" style="width: 70px; height: 46px; object-fit: cover;">
                            </td>
                            <td>
                                <strong class="text-dark d-block"><?= htmlspecialchars($item['title']) ?></strong>
                                <small class="text-muted"><?= htmlspecialchars(mb_substr(strip_tags($item['text']), 0, 90, 'UTF-8')) ?>...</small>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? 'Общее') ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($item['author'] ?? 'Админ') ?></td>
                            <td class="text-end">
                                <a href="index.php?action=newsDetail&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="Детальный просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?action=newsEdit&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=newsDeleteForm&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

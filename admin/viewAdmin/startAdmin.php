<?php
// Главная страница админ-панели (Дашборд)
$stats = isset($stats) ? $stats : modelAdmin::getStats();
$recentNews = isset($recentNews) ? $recentNews : modelAdminNews::getNewsList();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1">Панель управления</h1>
        <p class="text-muted small mb-0">Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Администратор') ?></strong>! Роль: <span class="badge bg-primary"><?= htmlspecialchars($_SESSION['status'] ?? 'admin') ?></span></p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=newsAdd" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Добавить новость
        </a>
    </div>
</div>

<!-- Статистические карточки -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3 fs-3">
                    <i class="bi bi-newspaper"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Всего новостей</h6>
                    <h3 class="fw-bold mb-0"><?= (int)$stats['news'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3 fs-3">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Категорий</h6>
                    <h3 class="fw-bold mb-0"><?= (int)$stats['categories'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3 fs-3">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Комментариев</h6>
                    <h3 class="fw-bold mb-0"><?= (int)$stats['comments'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3 fs-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Пользователей</h6>
                    <h3 class="fw-bold mb-0"><?= (int)$stats['users'] ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Быстрые действия и последние новости -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Последние публикации</h5>
        <a href="index.php?action=newsAdmin" class="btn btn-sm btn-outline-primary rounded-pill px-3">Все новости &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Заголовок</th>
                    <th>Категория</th>
                    <th>Автор</th>
                    <th class="text-end" style="width: 160px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentNews)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Новостей пока нет.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $previewList = array_slice($recentNews, 0, 5);
                    foreach ($previewList as $item): 
                    ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= (int)$item['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item['title']) ?></strong>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? 'Общее') ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($item['author'] ?? 'Админ') ?></td>
                            <td class="text-end">
                                <a href="index.php?action=newsEdit&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=newsDelete&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Удалить" onclick="return confirm('Вы уверены, что хотите удалить эту новость?');">
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

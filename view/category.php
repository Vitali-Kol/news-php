<?php
// Вывод списка категорий
$categories = isset($categories) ? $categories : Category::getAllCategories();
$currentCatId = isset($currentCatId) ? (int)$currentCatId : (isset($_GET['id']) && ($_GET['action'] ?? '') === 'category' ? (int)$_GET['id'] : 0);
?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-grid-fill me-2"></i> Категории
    </div>
    <div class="list-group list-group-flush">
        <a href="index.php?action=allnews" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($currentCatId === 0 && ($_GET['action'] ?? '') === 'allnews') ? 'active' : '' ?>">
            <span><i class="bi bi-newspaper me-2"></i> Все новости</span>
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?action=category&id=<?= (int)$cat['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($currentCatId === (int)$cat['id']) ? 'active' : '' ?>">
                <span><i class="bi bi-folder2 me-2"></i> <?= htmlspecialchars($cat['name']) ?></span>
                <i class="bi bi-chevron-right small text-muted"></i>
            </a>
        <?php endforeach; ?>
    </div>
</div>

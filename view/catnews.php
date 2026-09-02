<?php
// Вывод новостей по выбранной категории
$categoryTitle = isset($category['name']) ? htmlspecialchars($category['name']) : 'Категория';
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h2 class="h4 fw-bold text-dark mb-0">
        <i class="bi bi-tag text-primary me-2"></i>Категория: <span class="text-primary"><?= $categoryTitle ?></span>
    </h2>
    <span class="badge bg-primary rounded-pill px-3 py-2">
        Новостей: <?= count($arr) ?>
    </span>
</div>

<?php
if (empty($arr)) {
    echo '<div class="alert alert-info py-4 text-center">
            <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
            В данной категории пока нет опубликованных новостей.
          </div>';
} else {
    ViewNews::newsByCategory($arr);
}
?>

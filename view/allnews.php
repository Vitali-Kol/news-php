<?php
// All News Catalog
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h2 class="h4 fw-bold text-dark mb-0">
        <i class="bi bi-collection text-primary me-2"></i>All News
    </h2>
    <span class="badge bg-secondary rounded-pill px-3 py-2">
        Total: <?= count($arr) ?>
    </span>
</div>

<?php
ViewNews::allNews($arr);
?>

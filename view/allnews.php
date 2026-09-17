<?php
// All News Catalog & Search Results
$isSearch = isset($isSearch) && $isSearch;
$searchQuery = $searchQuery ?? '';
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
    <div>
        <?php if ($isSearch): ?>
            <h2 class="h4 fw-bold mb-1">
                <i class="bi bi-search text-primary me-2"></i>Search Results
            </h2>
            <p class="text-body-secondary small mb-0">Showing articles matching: <mark class="px-2 rounded"><?= htmlspecialchars($searchQuery) ?></mark></p>
        <?php else: ?>
            <h2 class="h4 fw-bold mb-0">
                <i class="bi bi-collection text-primary me-2"></i>All News
            </h2>
        <?php endif; ?>
    </div>
    <span class="badge bg-primary rounded-pill px-3 py-2">
        <?= count($arr) ?> <?= count($arr) === 1 ? 'Article' : 'Articles' ?>
    </span>
</div>

<?php if ($isSearch && empty($arr)): ?>
    <div class="card shadow-sm border-0 p-5 text-center my-4">
        <div class="mb-3 text-muted">
            <i class="bi bi-search fs-1"></i>
        </div>
        <h4 class="fw-bold">No articles found</h4>
        <p class="text-muted">No publications match your search keyword "<strong><?= htmlspecialchars($searchQuery) ?></strong>".</p>
        <div class="mt-2">
            <a href="index.php?action=allnews" class="btn btn-outline-primary rounded-pill px-4">Browse All Articles</a>
        </div>
    </div>
<?php else: ?>
    <?php ViewNews::allNews($arr); ?>
<?php endif; ?>


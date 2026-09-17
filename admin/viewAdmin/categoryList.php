<?php
// Categories Management List
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1"><i class="bi bi-folder2-open text-success me-2"></i>Category Management</h1>
        <p class="text-muted small mb-0">Total categories: <?= count($categoryList) ?></p>
    </div>
    <a href="index.php?action=categoryAdd" class="btn btn-success rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Category added successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Category updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i> Category deleted.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="catFilterInput" class="form-control" placeholder="Quick filter categories...">
                </div>
            </div>
            <div class="col-md-6 text-md-end text-muted small mt-2 mt-md-0">
                Showing <span id="visibleCatCount"><?= count($categoryList) ?></span> of <?= count($categoryList) ?> categories
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="categoryTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Category Name</th>
                    <th>Articles Count</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categoryList)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No categories created yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categoryList as $cat): 
                        $db = new db();
                        $count = (int)($db->getOne("SELECT COUNT(*) AS c FROM publications WHERE rubric_ref_id = :id", ['id' => (int)$cat['id']])['c'] ?? 0);
                    ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= (int)$cat['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($cat['name']) ?></strong>
                            </td>
                            <td>
                                <a href="../index.php?action=category&id=<?= (int)$cat['id'] ?>" target="_blank" class="text-decoration-none">
                                    <span class="badge bg-primary rounded-pill"><?= $count ?> articles</span>
                                </a>
                            </td>
                            <td class="text-end">
                                <a href="index.php?action=categoryEdit&id=<?= (int)$cat['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=categoryDelete&id=<?= (int)$cat['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete category «<?= htmlspecialchars(addslashes($cat['name'])) ?>»? This is blocked if it contains articles.');">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('catFilterInput');
    const table = document.getElementById('categoryTable');
    const countEl = document.getElementById('visibleCatCount');
    if (!input || !table) return;

    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length <= 1) return;
            const text = row.innerText.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (countEl) countEl.textContent = visibleCount;
    });
});
</script>

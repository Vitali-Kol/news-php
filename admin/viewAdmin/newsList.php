<?php
// News Management Table
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1">News Management</h1>
        <p class="text-muted small mb-0">Total publications: <?= count($newsList) ?></p>
    </div>
    <a href="index.php?action=newsAdd" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add News
    </a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> News article published successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> News article updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i> News article deleted.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-light border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="newsFilterInput" class="form-control" placeholder="Quick filter by title, author, category...">
                </div>
            </div>
            <div class="col-md-6 text-md-end text-muted small mt-2 mt-md-0">
                Showing <span id="visibleNewsCount"><?= count($newsList) ?></span> of <?= count($newsList) ?> items
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="newsTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 100px;">Photo</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th class="text-end" style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($newsList)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No news articles yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($newsList as $item): 
                        $pic = $item['picture'] ?? null;
                        if (!empty($pic) && is_string($pic) && (strpos($pic, 'http://') === 0 || strpos($pic, 'https://') === 0 || strpos($pic, 'data:') === 0)) {
                            $imgSrc = htmlspecialchars($pic);
                        } elseif (!empty($pic)) {
                            $imgSrc = 'data:image/jpeg;base64,' . base64_encode($pic);
                        } else {
                            $imgSrc = 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=150&auto=format&fit=crop&q=80';
                        }
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
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? 'General') ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($item['author'] ?? 'Admin') ?></td>
                            <td class="text-end">
                                <a href="index.php?action=newsDetail&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?action=newsEdit&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?action=newsDeleteForm&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete">
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
    const input = document.getElementById('newsFilterInput');
    const table = document.getElementById('newsTable');
    const countEl = document.getElementById('visibleNewsCount');
    if (!input || !table) return;

    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length <= 1) return; // skip empty state row
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

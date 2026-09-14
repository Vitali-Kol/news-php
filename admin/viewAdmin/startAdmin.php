<?php
// Admin Dashboard
$stats = isset($stats) ? $stats : modelAdmin::getStats();
$recentNews = isset($recentNews) ? $recentNews : modelAdminNews::getNewsList();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold mb-1">Admin Dashboard</h1>
        <p class="text-muted small mb-0">Welcome, <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Administrator') ?></strong>! Role: <span class="badge bg-primary"><?= htmlspecialchars($_SESSION['status'] ?? 'admin') ?></span></p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=newsAdd" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add News
        </a>
    </div>
</div>

<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3 fs-3">
                    <i class="bi bi-newspaper"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Total Articles</h6>
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
                    <h6 class="text-muted small mb-1">Categories</h6>
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
                    <h6 class="text-muted small mb-1">Comments</h6>
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
                    <h6 class="text-muted small mb-1">Users</h6>
                    <h3 class="fw-bold mb-0"><?= (int)$stats['users'] ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Posts Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Publications</h5>
        <a href="index.php?action=newsAdmin" class="btn btn-sm btn-outline-primary rounded-pill px-3">All News &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th class="text-end" style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentNews)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No news articles yet.</td>
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
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? 'General') ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($item['author'] ?? 'Admin') ?></td>
                            <td class="text-end">
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

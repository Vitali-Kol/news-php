<?php
// Home page: Top 3 latest published news
?>
<div class="hero-banner shadow-sm">
    <h1 class="display-6 fw-bold mb-2">Current Events & Breaking News</h1>
    <p class="lead mb-0 text-light opacity-75">Top headlines and fresh stories every day.</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h2 class="h4 fw-bold text-dark mb-0">
        <i class="bi bi-clock-history text-primary me-2"></i>Latest News
    </h2>
    <a href="index.php?action=allnews" class="btn btn-outline-primary btn-sm rounded-pill px-3">
        All News &rarr;
    </a>
</div>

<?php
ViewNews::newsList($arr);
?>

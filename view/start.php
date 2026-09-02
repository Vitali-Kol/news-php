<?php
// Стартовая страница: вывод 3 последних новостей
?>
<div class="hero-banner shadow-sm">
    <h1 class="display-6 fw-bold mb-2">Актуальные события и факты</h1>
    <p class="lead mb-0 text-light opacity-75">Главные новости и свежая информация каждый день.</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h2 class="h4 fw-bold text-dark mb-0">
        <i class="bi bi-clock-history text-primary me-2"></i>Последние новости
    </h2>
    <a href="index.php?action=allnews" class="btn btn-outline-primary btn-sm rounded-pill px-3">
        Все новости &rarr;
    </a>
</div>

<?php
ViewNews::newsList($arr);
?>

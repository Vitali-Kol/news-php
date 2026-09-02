<?php
class ViewNews {
    // Вспомогательный метод для получения Data URI изображения
    public static function getImageSrc($blob) {
        if (!empty($blob)) {
            return 'data:image/jpeg;base64,' . base64_encode($blob);
        }
        return 'https://via.placeholder.com/600x400?text=No+Image';
    }

    // Рендеринг отдельной карточки новости
    public static function renderCard($item) {
        $imgSrc = self::getImageSrc($item['picture'] ?? null);
        $title = htmlspecialchars($item['title'] ?? '');
        $categoryName = htmlspecialchars($item['category_name'] ?? 'Общее');
        $author = htmlspecialchars($item['author'] ?? 'Редакция');
        $id = (int)($item['id'] ?? 0);
        $catId = (int)($item['category_id'] ?? 0);
        $commentCount = Comments::getCommentCountByNewsID($id);

        // Обрезка текста для превью
        $fullText = strip_tags($item['text'] ?? '');
        if (mb_strlen($fullText, 'UTF-8') > 160) {
            $previewText = mb_substr($fullText, 0, 160, 'UTF-8') . '...';
        } else {
            $previewText = $fullText;
        }

        echo '
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm news-card border-0">
                <div class="position-relative">
                    <img src="' . $imgSrc . '" class="card-img-top news-img" alt="' . $title . '">
                    <span class="badge bg-primary position-absolute top-0 start-0 m-2 px-2 py-1">
                        <a href="index.php?action=category&id=' . $catId . '" class="text-white text-decoration-none">' . $categoryName . '</a>
                    </span>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-truncate-2">
                        <a href="index.php?action=read&id=' . $id . '" class="text-dark text-decoration-none fw-bold">' . $title . '</a>
                    </h5>
                    <p class="card-text text-muted small flex-grow-1">' . htmlspecialchars($previewText) . '</p>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <span class="small text-secondary"><i class="bi bi-person"></i> ' . $author . '</span>
                        <a href="index.php?action=read&id=' . $id . '#comments" class="text-decoration-none text-muted small me-2" title="Комментарии">
                            <i class="bi bi-chat-dots-fill text-primary"></i> ' . $commentCount . '
                        </a>
                        <a href="index.php?action=read&id=' . $id . '" class="btn btn-sm btn-outline-primary rounded-pill px-3">Подробнее &rarr;</a>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Вывод списка новостей (сетка)
    public static function newsList($newsList) {
        if (empty($newsList)) {
            echo '<div class="alert alert-info" role="alert">Новостей пока нет.</div>';
            return;
        }
        echo '<div class="row">';
        foreach ($newsList as $item) {
            self::renderCard($item);
        }
        echo '</div>';
    }

    // Вывод всех новостей
    public static function allNews($arr) {
        self::newsList($arr);
    }

    // Вывод новостей по категории
    public static function newsByCategory($arr) {
        self::newsList($arr);
    }

    // Вывод детальной страницы новости
    public static function readNews($item) {
        if (empty($item)) {
            echo '<div class="alert alert-warning">Новость не найдена.</div>';
            return;
        }

        $imgSrc = self::getImageSrc($item['picture'] ?? null);
        $title = htmlspecialchars($item['title'] ?? '');
        $categoryName = htmlspecialchars($item['category_name'] ?? 'Общее');
        $author = htmlspecialchars($item['author'] ?? 'Редакция');
        $catId = (int)($item['category_id'] ?? 0);
        $id = (int)($item['id'] ?? 0);
        $commentCount = Comments::getCommentCountByNewsID($id);
        $text = nl2br(htmlspecialchars($item['text'] ?? ''));

        echo '
        <article class="news-detail card shadow-sm border-0 p-4 mb-4">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=allnews">Все новости</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=category&id=' . $catId . '">' . $categoryName . '</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Чтение новости</li>
                </ol>
            </nav>

            <h1 class="news-title fw-bold mb-3">' . $title . '</h1>

            <div class="news-meta d-flex flex-wrap align-items-center text-muted mb-4 pb-2 border-bottom">
                <span class="badge bg-primary me-3 py-2 px-3">' . $categoryName . '</span>
                <span class="me-3"><i class="bi bi-person-fill"></i> Автор: <strong>' . $author . '</strong></span>
                <span class="me-3"><i class="bi bi-chat-dots-fill text-primary"></i> Комментариев: <strong>' . $commentCount . '</strong></span>
            </div>

            <div class="news-detail-image-wrapper mb-4 text-center">
                <img src="' . $imgSrc . '" class="img-fluid rounded shadow-sm" alt="' . $title . '" style="max-height: 480px; width: 100%; object-fit: cover;">
            </div>

            <div class="news-content fs-5 lh-lg text-dark mb-4">
                ' . $text . '
            </div>

            <div class="d-flex justify-content-between pt-3 border-top">
                <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">&larr; Вернуться назад</a>
                <a href="index.php?action=allnews" class="btn btn-primary rounded-pill px-4">Все новости</a>
            </div>
        </article>';
    }
}
?>

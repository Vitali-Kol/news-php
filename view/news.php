<?php
class ViewNews {
    // Helper method for image source (handles URLs and Base64 Data URI)
    public static function getImageSrc($picture) {
        if (empty($picture)) {
            return 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=800&auto=format&fit=crop&q=80';
        }
        if (is_string($picture) && (strpos($picture, 'http://') === 0 || strpos($picture, 'https://') === 0 || strpos($picture, 'data:') === 0)) {
            return htmlspecialchars($picture);
        }
        return 'data:image/jpeg;base64,' . base64_encode($picture);
    }

    // Render single news card
    public static function renderCard($item) {
        $imgSrc = self::getImageSrc($item['picture'] ?? null);
        $title = htmlspecialchars($item['title'] ?? '');
        $categoryName = htmlspecialchars($item['category_name'] ?? 'General');
        $author = htmlspecialchars($item['author'] ?? 'Editorial Staff');
        $id = (int)($item['id'] ?? 0);
        $catId = (int)($item['category_id'] ?? 0);
        $commentCount = Comments::getCommentCountByNewsID($id);

        // Preview text truncation
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
                        <a href="index.php?action=read&id=' . $id . '#comments" class="text-decoration-none text-muted small me-2" title="Comments">
                            <i class="bi bi-chat-dots-fill text-primary"></i> ' . $commentCount . '
                        </a>
                        <a href="index.php?action=read&id=' . $id . '" class="btn btn-sm btn-outline-primary rounded-pill px-3">Read more &rarr;</a>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Render list of cards (grid)
    public static function newsList($newsList) {
        if (empty($newsList)) {
            echo '<div class="alert alert-info" role="alert">No news articles available yet.</div>';
            return;
        }
        echo '<div class="row">';
        foreach ($newsList as $item) {
            self::renderCard($item);
        }
        echo '</div>';
    }

    // Render all news
    public static function allNews($arr) {
        self::newsList($arr);
    }

    // Render news by category
    public static function newsByCategory($arr) {
        self::newsList($arr);
    }

    // Render full single news detail
    public static function readNews($item) {
        if (empty($item)) {
            echo '<div class="alert alert-warning">Article not found.</div>';
            return;
        }

        $imgSrc = self::getImageSrc($item['picture'] ?? null);
        $title = htmlspecialchars($item['title'] ?? '');
        $categoryName = htmlspecialchars($item['category_name'] ?? 'General');
        $author = htmlspecialchars($item['author'] ?? 'Editorial Staff');
        $catId = (int)($item['category_id'] ?? 0);
        $id = (int)($item['id'] ?? 0);
        $commentCount = Comments::getCommentCountByNewsID($id);
        $text = nl2br(htmlspecialchars($item['text'] ?? ''));

        echo '
        <article class="news-detail card shadow-sm border-0 p-4 mb-4">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=allnews">All News</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=category&id=' . $catId . '">' . $categoryName . '</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Read Article</li>
                </ol>
            </nav>

            <h1 class="news-title fw-bold mb-3">' . $title . '</h1>

            <div class="news-meta d-flex flex-wrap align-items-center text-muted mb-4 pb-2 border-bottom">
                <span class="badge bg-primary me-3 py-2 px-3">' . $categoryName . '</span>
                <span class="me-3"><i class="bi bi-person-fill"></i> Author: <strong>' . $author . '</strong></span>
                <span class="me-3"><i class="bi bi-chat-dots-fill text-primary"></i> Comments: <strong>' . $commentCount . '</strong></span>
            </div>

            <div class="news-detail-image-wrapper mb-4 text-center">
                <img src="' . $imgSrc . '" class="img-fluid rounded shadow-sm" alt="' . $title . '" style="max-height: 480px; width: 100%; object-fit: cover;">
            </div>

            <div class="news-content fs-5 lh-lg text-dark mb-4">
                ' . $text . '
            </div>

            <div class="d-flex justify-content-between pt-3 border-top">
                <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4">&larr; Go Back</a>
                <a href="index.php?action=allnews" class="btn btn-primary rounded-pill px-4">All News</a>
            </div>
        </article>';
    }
}
?>

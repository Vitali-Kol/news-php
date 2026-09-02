<?php
class ViewComments {
    // Вывод формы для добавления комментария
    public static function CommentsForm($newsId) {
        $id = (int)$newsId;
        echo '
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-header bg-light fw-bold py-3">
                <i class="bi bi-chat-left-text me-2"></i> Оставить комментарий
            </div>
            <div class="card-body">
                <form action="index.php?action=insertcomment&id=' . $id . '" method="POST">
                    <div class="mb-3">
                        <label for="commentText" class="form-label text-muted small">Ваш комментарий:</label>
                        <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Напишите ваш комментарий..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-send me-1"></i> Отправить комментарий
                    </button>
                </form>
            </div>
        </div>';
    }

    // Вывод списка комментариев для новости
    public static function CommentsByNews($arr) {
        $count = count($arr);
        echo '<div class="comments-section mt-4 mb-4">';
        echo '<h4 class="fw-bold mb-3"><i class="bi bi-chat-square-text text-primary me-2"></i>Комментарии (' . $count . ')</h4>';

        if (empty($arr)) {
            echo '<div class="alert alert-light border text-muted py-3">Комментариев пока нет. Будьте первым!</div>';
        } else {
            echo '<div class="comments-list">';
            foreach ($arr as $comment) {
                $author = htmlspecialchars($comment['author'] ?? 'Аноним');
                $text = nl2br(htmlspecialchars($comment['text'] ?? ''));
                $date = !empty($comment['date']) ? date('d.m.Y H:i', strtotime($comment['date'])) : '';

                echo '
                <div class="card border-0 shadow-sm mb-3 bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <strong class="text-dark">' . $author . '</strong>
                            </div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>' . $date . '</small>
                        </div>
                        <p class="card-text text-secondary mb-0">' . $text . '</p>
                    </div>
                </div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    // Вывод количества комментариев со значком
    public static function CommentsCount($count) {
        $count = (int)$count;
        echo '<span class="badge bg-secondary text-white rounded-pill px-2 py-1">
                <i class="bi bi-chat-dots me-1"></i> ' . $count . '
              </span>';
    }
}
?>

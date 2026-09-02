<?php
class Comments {
    // Добавление комментария к новости ($c - текст комментария, $id - ID новости)
    public static function insertComment($c, $id) {
        $db = new db();
        $userId = $_SESSION['userId'] ?? 2; // По умолчанию анонимный пользователь (ID: 2)
        $query = "INSERT INTO comments (user_id, news_id, text, date) VALUES (:user_id, :news_id, :text, NOW())";
        return $db->execute($query, [
            'user_id' => $userId,
            'news_id' => (int)$id,
            'text'    => $c
        ]);
    }

    // Получение списка всех комментариев для новости по ID
    public static function getCommentByNewsID($id) {
        $db = new db();
        $query = "SELECT comments.*, users.username AS author 
                  FROM comments 
                  LEFT JOIN users ON comments.user_id = users.id 
                  WHERE comments.news_id = :id 
                  ORDER BY comments.id DESC";
        return $db->getAll($query, ['id' => (int)$id]);
    }

    // Получение количества комментариев для новости по ID
    public static function getCommentCountByNewsID($id) {
        $db = new db();
        $query = "SELECT COUNT(*) AS count FROM comments WHERE news_id = :id";
        $res = $db->getOne($query, ['id' => (int)$id]);
        return $res ? (int)$res['count'] : 0;
    }
}
?>

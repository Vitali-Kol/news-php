<?php
class Comments {
    // Добавление комментария к новости ($c - текст комментария, $id - ID новости)
    public static function insertComment($c, $id) {
        $db = new db();
        $userId = $_SESSION['userId'] ?? 2; // По умолчанию анонимный пользователь (ID: 2)
        $query = "INSERT INTO discussions (author_ref_id, pub_ref_id, commentary, posted_at) VALUES (:user_id, :news_id, :text, NOW())";
        return $db->execute($query, [
            'user_id' => $userId,
            'news_id' => (int)$id,
            'text'    => $c
        ]);
    }

    // Получение списка всех комментариев для новости по ID
    public static function getCommentByNewsID($id) {
        $db = new db();
        $query = "SELECT d.discussion_id AS id, d.author_ref_id AS user_id, d.pub_ref_id AS news_id, 
                         d.commentary AS text, d.posted_at AS date, a.full_name AS author 
                  FROM discussions d 
                  LEFT JOIN accounts a ON d.author_ref_id = a.account_id 
                  WHERE d.pub_ref_id = :id 
                  ORDER BY d.discussion_id DESC";
        return $db->getAll($query, ['id' => (int)$id]);
    }

    // Получение количества комментариев для новости по ID
    public static function getCommentCountByNewsID($id) {
        $db = new db();
        $query = "SELECT COUNT(*) AS count FROM discussions WHERE pub_ref_id = :id";
        $res = $db->getOne($query, ['id' => (int)$id]);
        return $res ? (int)$res['count'] : 0;
    }
}
?>

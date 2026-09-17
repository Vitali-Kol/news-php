<?php
class News {
    // Получение 3-х последних опубликованных новостей
    public static function getLast3News() {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  ORDER BY p.pub_id DESC 
                  LIMIT 3";
        return $db->getAll($query);
    }

    // Получение всех новостей
    public static function getAllNews() {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  ORDER BY p.pub_id DESC";
        return $db->getAll($query);
    }

    // Получение новостей конкретной категории по её ID
    public static function getNewsByCategory($id) {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  WHERE p.rubric_ref_id = :id 
                  ORDER BY p.pub_id DESC";
        return $db->getAll($query, ['id' => (int)$id]);
    }

    // Получение детальной информации об одной новости по её ID
    public static function getNewsById($id) {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  WHERE p.pub_id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }
}
?>

<?php
class News {
    // Получение 3-х последних опубликованных новостей
    public static function getLast3News() {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  ORDER BY news.id DESC 
                  LIMIT 3";
        return $db->getAll($query);
    }

    // Получение всех новостей
    public static function getAllNews() {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  ORDER BY news.id DESC";
        return $db->getAll($query);
    }

    // Получение новостей конкретной категории по её ID
    public static function getNewsByCategory($id) {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  WHERE news.category_id = :id 
                  ORDER BY news.id DESC";
        return $db->getAll($query, ['id' => $id]);
    }

    // Получение детальной информации об одной новости по её ID
    public static function getNewsById($id) {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  WHERE news.id = :id";
        return $db->getOne($query, ['id' => $id]);
    }
}
?>

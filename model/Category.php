<?php
class Category {
    // Получение списка всех категорий
    public static function getAllCategories() {
        $db = new db();
        $query = "SELECT * FROM category ORDER BY id ASC";
        return $db->getAll($query);
    }

    // Получение категории по ID
    public static function getCategoryById($id) {
        $db = new db();
        $query = "SELECT * FROM category WHERE id = :id";
        return $db->getOne($query, ['id' => $id]);
    }
}
?>

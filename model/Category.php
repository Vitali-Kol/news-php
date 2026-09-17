<?php
class Category {
    // Получение списка всех категорий
    public static function getAllCategories() {
        $db = new db();
        $query = "SELECT rubric_id AS id, rubric_label AS name, rubric_slug, rubric_summary FROM rubrics ORDER BY rubric_id ASC";
        return $db->getAll($query);
    }

    // Получение категории по ID
    public static function getCategoryById($id) {
        $db = new db();
        $query = "SELECT rubric_id AS id, rubric_label AS name, rubric_slug, rubric_summary FROM rubrics WHERE rubric_id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }
}
?>

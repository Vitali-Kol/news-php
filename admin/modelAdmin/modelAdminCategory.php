<?php
class modelAdminCategory {

    // Get all categories
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Get single category by ID
    public static function getCategoryById($id) {
        $db = new db();
        return $db->getOne("SELECT * FROM category WHERE id = :id", ['id' => (int)$id]);
    }

    // Add new category
    public static function categoryAdd() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $result['message'] = 'Please enter a category name!';
                return $result;
            }

            $db = new db();
            $exists = $db->getOne("SELECT id FROM category WHERE name = :name", ['name' => $name]);
            if ($exists) {
                $result['message'] = 'A category with this name already exists!';
                return $result;
            }

            $insert = $db->execute("INSERT INTO category (name) VALUES (:name)", ['name' => $name]);
            if ($insert) {
                $result['result'] = true;
                $result['message'] = 'Category added successfully!';
            } else {
                $result['message'] = 'Error adding category to database!';
            }
        }
        return $result;
    }

    // Edit category
    public static function categoryEdit($id) {
        $result = ['result' => false, 'message' => ''];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $result['message'] = 'Please enter a category name!';
                return $result;
            }

            $db = new db();
            $exists = $db->getOne("SELECT id FROM category WHERE name = :name AND id != :id", ['name' => $name, 'id' => $id]);
            if ($exists) {
                $result['message'] = 'A category with this name already exists!';
                return $result;
            }

            $update = $db->execute("UPDATE category SET name = :name WHERE id = :id", ['name' => $name, 'id' => $id]);
            if ($update) {
                $result['result'] = true;
                $result['message'] = 'Category updated successfully!';
            } else {
                $result['message'] = 'Error updating category!';
            }
        }
        return $result;
    }

    // Delete category with guard against non-empty categories
    public static function categoryDelete($id) {
        $id = (int)$id;
        $db = new db();

        $newsCount = $db->getOne("SELECT COUNT(*) AS c FROM news WHERE category_id = :id", ['id' => $id]);
        $count = (int)($newsCount['c'] ?? 0);

        if ($count > 0) {
            return ['result' => false, 'message' => "Cannot delete category: it contains $count news articles!"];
        }

        $delete = $db->execute("DELETE FROM category WHERE id = :id", ['id' => $id]);
        return [
            'result' => $delete,
            'message' => $delete ? 'Category deleted.' : 'Error deleting category!'
        ];
    }
}
?>

<?php
class modelAdminCategory {

    // Get all categories
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT rubric_id AS id, rubric_label AS name, rubric_slug, rubric_summary FROM rubrics ORDER BY rubric_label ASC");
    }

    // Get single category by ID
    public static function getCategoryById($id) {
        $db = new db();
        return $db->getOne("SELECT rubric_id AS id, rubric_label AS name, rubric_slug, rubric_summary FROM rubrics WHERE rubric_id = :id", ['id' => (int)$id]);
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
            $exists = $db->getOne("SELECT rubric_id AS id FROM rubrics WHERE rubric_label = :name", ['name' => $name]);
            if ($exists) {
                $result['message'] = 'A category with this name already exists!';
                return $result;
            }

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
            if (empty($slug)) {
                $slug = 'rubric-' . time();
            }

            $insert = $db->execute("INSERT INTO rubrics (rubric_label, rubric_slug) VALUES (:name, :slug)", [
                'name' => $name,
                'slug' => $slug
            ]);
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
            $exists = $db->getOne("SELECT rubric_id AS id FROM rubrics WHERE rubric_label = :name AND rubric_id != :id", ['name' => $name, 'id' => $id]);
            if ($exists) {
                $result['message'] = 'A category with this name already exists!';
                return $result;
            }

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
            if (empty($slug)) {
                $slug = 'rubric-' . $id;
            }

            $update = $db->execute("UPDATE rubrics SET rubric_label = :name, rubric_slug = :slug WHERE rubric_id = :id", [
                'name' => $name,
                'slug' => $slug,
                'id'   => $id
            ]);
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

        $newsCount = $db->getOne("SELECT COUNT(*) AS c FROM publications WHERE rubric_ref_id = :id", ['id' => $id]);
        $count = (int)($newsCount['c'] ?? 0);

        if ($count > 0) {
            return ['result' => false, 'message' => "Cannot delete category: it contains $count news articles!"];
        }

        $delete = $db->execute("DELETE FROM rubrics WHERE rubric_id = :id", ['id' => $id]);
        return [
            'result' => $delete,
            'message' => $delete ? 'Category deleted.' : 'Error deleting category!'
        ];
    }
}
?>

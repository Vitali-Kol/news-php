<?php
class modelAdminCategory {

    // Получение списка всех категорий
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Получение одной категории по ID
    public static function getCategoryById($id) {
        $db = new db();
        return $db->getOne("SELECT * FROM category WHERE id = :id", ['id' => (int)$id]);
    }

    // Добавление новой категории
    public static function categoryAdd() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $result['message'] = 'Введите название категории!';
                return $result;
            }

            $db = new db();
            // Проверка уникальности
            $exists = $db->getOne("SELECT id FROM category WHERE name = :name", ['name' => $name]);
            if ($exists) {
                $result['message'] = 'Категория с таким названием уже существует!';
                return $result;
            }

            $insert = $db->execute("INSERT INTO category (name) VALUES (:name)", ['name' => $name]);
            if ($insert) {
                $result['result'] = true;
                $result['message'] = 'Категория успешно добавлена!';
            } else {
                $result['message'] = 'Ошибка при добавлении категории!';
            }
        }
        return $result;
    }

    // Редактирование категории
    public static function categoryEdit($id) {
        $result = ['result' => false, 'message' => ''];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $result['message'] = 'Введите название категории!';
                return $result;
            }

            $db = new db();
            // Проверка уникальности (исключая текущую)
            $exists = $db->getOne("SELECT id FROM category WHERE name = :name AND id != :id", ['name' => $name, 'id' => $id]);
            if ($exists) {
                $result['message'] = 'Категория с таким названием уже существует!';
                return $result;
            }

            $update = $db->execute("UPDATE category SET name = :name WHERE id = :id", ['name' => $name, 'id' => $id]);
            if ($update) {
                $result['result'] = true;
                $result['message'] = 'Категория успешно обновлена!';
            } else {
                $result['message'] = 'Ошибка при обновлении категории!';
            }
        }
        return $result;
    }

    // Удаление категории (с проверкой наличия новостей)
    public static function categoryDelete($id) {
        $id = (int)$id;
        $db = new db();

        // Проверка: есть ли новости в этой категории
        $newsCount = $db->getOne("SELECT COUNT(*) AS c FROM news WHERE category_id = :id", ['id' => $id]);
        $count = (int)($newsCount['c'] ?? 0);

        if ($count > 0) {
            return ['result' => false, 'message' => "Невозможно удалить категорию: в ней есть $count публикаций!"];
        }

        $delete = $db->execute("DELETE FROM category WHERE id = :id", ['id' => $id]);
        return [
            'result' => $delete,
            'message' => $delete ? 'Категория удалена.' : 'Ошибка при удалении категории!'
        ];
    }
}
?>

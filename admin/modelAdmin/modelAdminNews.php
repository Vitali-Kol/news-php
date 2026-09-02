<?php
class modelAdminNews {
    // Получение списка всех новостей
    public static function getNewsList() {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  ORDER BY news.id DESC";
        return $db->getAll($query);
    }

    // Получение одной новости по её ID
    public static function getNewsByID($id) {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  WHERE news.id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Получение списка категорий для выпадающего списка
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Добавление новой новости
    public static function getNewsAdd() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $userId = (int)($_SESSION['userId'] ?? 1);

            if (empty($title) || empty($text) || empty($categoryId)) {
                $result['message'] = 'Заполните все обязательные поля формы!';
                return $result;
            }

            // Проверка и чтение файла изображения
            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            if (empty($pictureBlob)) {
                $result['message'] = 'Необходимо выбрать изображение для новости!';
                return $result;
            }

            $db = new db();
            $query = "INSERT INTO news (title, text, picture, category_id, user_id) 
                      VALUES (:title, :text, :picture, :category_id, :user_id)";
            
            $insert = $db->execute($query, [
                'title'       => $title,
                'text'        => $text,
                'picture'     => $pictureBlob,
                'category_id' => $categoryId,
                'user_id'     => $userId
            ]);

            if ($insert) {
                $result['result'] = true;
                $result['message'] = 'Новость успешно опубликована!';
            } else {
                $result['message'] = 'Ошибка при добавлении новости в базу данных!';
            }
        }
        return $result;
    }

    // Редактирование существующей новости
    public static function getNewsEdit($id) {
        $result = ['result' => false, 'message' => ''];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);

            if (empty($title) || empty($text) || empty($categoryId)) {
                $result['message'] = 'Заполните все обязательные поля формы!';
                return $result;
            }

            $db = new db();
            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            if (!empty($pictureBlob)) {
                $query = "UPDATE news 
                          SET title = :title, text = :text, picture = :picture, category_id = :category_id 
                          WHERE id = :id";
                $update = $db->execute($query, [
                    'title'       => $title,
                    'text'        => $text,
                    'picture'     => $pictureBlob,
                    'category_id' => $categoryId,
                    'id'          => $id
                ]);
            } else {
                $query = "UPDATE news 
                          SET title = :title, text = :text, category_id = :category_id 
                          WHERE id = :id";
                $update = $db->execute($query, [
                    'title'       => $title,
                    'text'        => $text,
                    'category_id' => $categoryId,
                    'id'          => $id
                ]);
            }

            if ($update) {
                $result['result'] = true;
                $result['message'] = 'Новость успешно обновлена!';
            } else {
                $result['message'] = 'Ошибка при обновлении новости в базе данных!';
            }
        }
        return $result;
    }

    // Удаление новости и связанных комментариев
    public static function getNewsDelete($id) {
        $id = (int)$id;
        $db = new db();
        // Удаляем комментарии
        $db->execute("DELETE FROM comments WHERE news_id = :id", ['id' => $id]);
        // Удаляем новость
        $res = $db->execute("DELETE FROM news WHERE id = :id", ['id' => $id]);
        return ['result' => $res, 'message' => $res ? 'Новость удалена.' : 'Ошибка удаления новости.'];
    }
}
?>

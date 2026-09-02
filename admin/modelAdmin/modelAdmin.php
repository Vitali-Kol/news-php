<?php
class modelAdmin {
    // Авторизация пользователя по email и паролю
    public static function userLogin($email, $password) {
        $db = new db();
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $user = $db->getOne($query, ['email' => trim($email)]);

        if ($user) {
            // Проверка хеша пароля
            if (password_verify($password, $user['password']) || $password === $user['pass']) {
                // Если статус не admin, можно либо разрешить базовый доступ, либо только для admin
                $_SESSION['userId'] = (int)$user['id'];
                $_SESSION['sessionId'] = session_id();
                $_SESSION['name'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['status'] = $user['status'];
                return ['success' => true, 'user' => $user];
            }
        }
        return ['success' => false, 'message' => 'Неверный E-mail или пароль'];
    }

    // Выход из системы
    public static function userLogout() {
        unset($_SESSION['userId']);
        unset($_SESSION['sessionId']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['status']);
        session_destroy();
    }

    // Получение данных пользователя по ID
    public static function getUserById($id) {
        $db = new db();
        $query = "SELECT * FROM users WHERE id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Обновление профиля администратора (имя и/или пароль)
    public static function updateProfile($userId, $username, $newPassword = null) {
        $db = new db();
        $username = trim($username);

        if (!empty($newPassword)) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE users SET username = :username, password = :password, pass = :pass WHERE id = :id";
            $res = $db->execute($query, [
                'username' => $username,
                'password' => $hash,
                'pass'     => $newPassword,
                'id'       => (int)$userId
            ]);
        } else {
            $query = "UPDATE users SET username = :username WHERE id = :id";
            $res = $db->execute($query, [
                'username' => $username,
                'id'       => (int)$userId
            ]);
        }

        if ($res) {
            $_SESSION['name'] = $username;
        }
        return $res;
    }

    // Получение списка всех новостей для таблицы админки
    public static function getNewsList() {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  ORDER BY news.id DESC";
        return $db->getAll($query);
    }

    // Получение одной новости по ID
    public static function getNewsById($id) {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  WHERE news.id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Добавление новой новости
    public static function newsAdd($title, $text, $pictureBlob, $categoryId, $userId) {
        $db = new db();
        $query = "INSERT INTO news (title, text, picture, category_id, user_id) 
                  VALUES (:title, :text, :picture, :category_id, :user_id)";
        return $db->execute($query, [
            'title'       => trim($title),
            'text'        => trim($text),
            'picture'     => $pictureBlob,
            'category_id' => (int)$categoryId,
            'user_id'     => (int)$userId
        ]);
    }

    // Редактирование новости
    public static function newsEdit($id, $title, $text, $pictureBlob = null, $categoryId = null) {
        $db = new db();
        if ($pictureBlob !== null && $pictureBlob !== '') {
            $query = "UPDATE news 
                      SET title = :title, text = :text, picture = :picture, category_id = :category_id 
                      WHERE id = :id";
            return $db->execute($query, [
                'title'       => trim($title),
                'text'        => trim($text),
                'picture'     => $pictureBlob,
                'category_id' => (int)$categoryId,
                'id'          => (int)$id
            ]);
        } else {
            $query = "UPDATE news 
                      SET title = :title, text = :text, category_id = :category_id 
                      WHERE id = :id";
            return $db->execute($query, [
                'title'       => trim($title),
                'text'        => trim($text),
                'category_id' => (int)$categoryId,
                'id'          => (int)$id
            ]);
        }
    }

    // Удаление новости и связанных с ней комментариев
    public static function newsDelete($id) {
        $db = new db();
        $id = (int)$id;
        // Удаляем сначала комментарии
        $db->execute("DELETE FROM comments WHERE news_id = :id", ['id' => $id]);
        // Затем саму новость
        return $db->execute("DELETE FROM news WHERE id = :id", ['id' => $id]);
    }

    // Получение списка всех категорий для форм выбора
    public static function getAllCategories() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Получение статистики для дашборда
    public static function getStats() {
        $db = new db();
        $newsCount = $db->getOne("SELECT COUNT(*) AS c FROM news")['c'] ?? 0;
        $catCount = $db->getOne("SELECT COUNT(*) AS c FROM category")['c'] ?? 0;
        $comCount = $db->getOne("SELECT COUNT(*) AS c FROM comments")['c'] ?? 0;
        $userCount = $db->getOne("SELECT COUNT(*) AS c FROM users")['c'] ?? 0;

        return [
            'news'       => (int)$newsCount,
            'categories' => (int)$catCount,
            'comments'   => (int)$comCount,
            'users'      => (int)$userCount
        ];
    }
}
?>

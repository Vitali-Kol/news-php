<?php
class modelAdmin {
    // User login authentication
    public static function userLogin($email, $password) {
        $db = new db();
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $user = $db->getOne($query, ['email' => trim($email)]);

        if ($user) {
            if (password_verify($password, $user['password']) || $password === $user['pass']) {
                $_SESSION['userId'] = (int)$user['id'];
                $_SESSION['sessionId'] = session_id();
                $_SESSION['name'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['status'] = $user['status'];
                return ['success' => true, 'user' => $user];
            }
        }
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    // User logout
    public static function userLogout() {
        unset($_SESSION['userId']);
        unset($_SESSION['sessionId']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['status']);
        session_destroy();
    }

    // Get user by ID
    public static function getUserById($id) {
        $db = new db();
        $query = "SELECT * FROM users WHERE id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Update admin profile
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
            return true;
        }
        return false;
    }

    // Get dashboard stats
    public static function getStats() {
        $db = new db();
        $newsCount = $db->getOne("SELECT COUNT(*) AS c FROM news")['c'] ?? 0;
        $catCount = $db->getOne("SELECT COUNT(*) AS c FROM category")['c'] ?? 0;
        $commCount = $db->getOne("SELECT COUNT(*) AS c FROM comments")['c'] ?? 0;
        $userCount = $db->getOne("SELECT COUNT(*) AS c FROM users")['c'] ?? 0;

        return [
            'news'       => (int)$newsCount,
            'categories' => (int)$catCount,
            'comments'   => (int)$commCount,
            'users'      => (int)$userCount
        ];
    }

    // Get all categories
    public static function getAllCategories() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Get news list
    public static function getNewsList() {
        return modelAdminNews::getNewsList();
    }

    // Get news by ID
    public static function getNewsById($id) {
        return modelAdminNews::getNewsByID($id);
    }

    // Add news
    public static function newsAdd($title, $text, $pictureBlob, $categoryId, $userId) {
        $db = new db();
        $query = "INSERT INTO news (title, text, picture, category_id, user_id) VALUES (:title, :text, :picture, :category_id, :user_id)";
        return $db->execute($query, [
            'title'       => $title,
            'text'        => $text,
            'picture'     => $pictureBlob,
            'category_id' => $categoryId,
            'user_id'     => $userId
        ]);
    }

    // Edit news
    public static function newsEdit($id, $title, $text, $pictureBlob, $categoryId) {
        $db = new db();
        if (!empty($pictureBlob)) {
            $query = "UPDATE news SET title = :title, text = :text, picture = :picture, category_id = :category_id WHERE id = :id";
            return $db->execute($query, [
                'title'       => $title,
                'text'        => $text,
                'picture'     => $pictureBlob,
                'category_id' => $categoryId,
                'id'          => (int)$id
            ]);
        } else {
            $query = "UPDATE news SET title = :title, text = :text, category_id = :category_id WHERE id = :id";
            return $db->execute($query, [
                'title'       => $title,
                'text'        => $text,
                'category_id' => $categoryId,
                'id'          => (int)$id
            ]);
        }
    }

    // Delete news
    public static function newsDelete($id) {
        $db = new db();
        $db->execute("DELETE FROM comments WHERE news_id = :id", ['id' => (int)$id]);
        return $db->execute("DELETE FROM news WHERE id = :id", ['id' => (int)$id]);
    }
}
?>

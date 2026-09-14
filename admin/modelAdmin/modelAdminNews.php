<?php
class modelAdminNews {
    // Get list of all news
    public static function getNewsList() {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  ORDER BY news.id DESC";
        return $db->getAll($query);
    }

    // Get single news by ID
    public static function getNewsByID($id) {
        $db = new db();
        $query = "SELECT news.*, category.name AS category_name, users.username AS author 
                  FROM news 
                  LEFT JOIN category ON news.category_id = category.id 
                  LEFT JOIN users ON news.user_id = users.id 
                  WHERE news.id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Get category list
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT * FROM category ORDER BY name ASC");
    }

    // Add news
    public static function getNewsAdd() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $userId = (int)($_SESSION['userId'] ?? 1);

            if (empty($title) || empty($text) || empty($categoryId)) {
                $result['message'] = 'Please fill in all required form fields!';
                return $result;
            }

            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            if (empty($pictureBlob)) {
                $result['message'] = 'An image file must be selected for the news article!';
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
                $result['message'] = 'News article published successfully!';
            } else {
                $result['message'] = 'Error inserting news article into database!';
            }
        }
        return $result;
    }

    // Edit news
    public static function getNewsEdit($id) {
        $result = ['result' => false, 'message' => ''];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $text = trim($_POST['text'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);

            if (empty($title) || empty($text) || empty($categoryId)) {
                $result['message'] = 'Please fill in all required form fields!';
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
                $result['message'] = 'News article updated successfully!';
            } else {
                $result['message'] = 'Error updating news article in database!';
            }
        }
        return $result;
    }

    // Get comments count
    public static function getCommentCount($newsId) {
        $db = new db();
        $row = $db->getOne("SELECT COUNT(*) AS c FROM comments WHERE news_id = :id", ['id' => (int)$newsId]);
        return (int)($row['c'] ?? 0);
    }

    // Delete news and cascading comments
    public static function getNewsDelete($id) {
        $id = (int)$id;
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirmId = (int)($_POST['news_id'] ?? 0);
            if ($confirmId !== $id) {
                $result['message'] = 'Invalid delete request!';
                return $result;
            }

            $db = new db();
            $db->execute("DELETE FROM comments WHERE news_id = :id", ['id' => $id]);
            $res = $db->execute("DELETE FROM news WHERE id = :id", ['id' => $id]);

            $result['result'] = $res;
            $result['message'] = $res ? 'News article deleted successfully.' : 'Error deleting news article!';
        }
        return $result;
    }
}
?>

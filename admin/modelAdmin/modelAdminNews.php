<?php
class modelAdminNews {
    // Get list of all news
    public static function getNewsList() {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  ORDER BY p.pub_id DESC";
        return $db->getAll($query);
    }

    // Get single news by ID
    public static function getNewsByID($id) {
        $db = new db();
        $query = "SELECT p.pub_id AS id, p.headline AS title, p.content_body AS text, p.cover_binary AS picture, 
                         p.rubric_ref_id AS category_id, p.author_ref_id AS user_id, 
                         r.rubric_label AS category_name, a.full_name AS author 
                  FROM publications p 
                  LEFT JOIN rubrics r ON p.rubric_ref_id = r.rubric_id 
                  LEFT JOIN accounts a ON p.author_ref_id = a.account_id 
                  WHERE p.pub_id = :id";
        return $db->getOne($query, ['id' => (int)$id]);
    }

    // Get category list
    public static function getCategoryList() {
        $db = new db();
        return $db->getAll("SELECT rubric_id AS id, rubric_label AS name, rubric_slug, rubric_summary FROM rubrics ORDER BY rubric_label ASC");
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
            $query = "INSERT INTO publications (headline, content_body, cover_binary, rubric_ref_id, author_ref_id) 
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
                $query = "UPDATE publications 
                          SET headline = :title, content_body = :text, cover_binary = :picture, rubric_ref_id = :category_id 
                          WHERE pub_id = :id";
                $update = $db->execute($query, [
                    'title'       => $title,
                    'text'        => $text,
                    'picture'     => $pictureBlob,
                    'category_id' => $categoryId,
                    'id'          => $id
                ]);
            } else {
                $query = "UPDATE publications 
                          SET headline = :title, content_body = :text, rubric_ref_id = :category_id 
                          WHERE pub_id = :id";
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
        $row = $db->getOne("SELECT COUNT(*) AS c FROM discussions WHERE pub_ref_id = :id", ['id' => (int)$newsId]);
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
            $db->execute("DELETE FROM discussions WHERE pub_ref_id = :id", ['id' => $id]);
            $res = $db->execute("DELETE FROM publications WHERE pub_id = :id", ['id' => $id]);

            $result['result'] = $res;
            $result['message'] = $res ? 'News article deleted successfully.' : 'Error deleting news article!';
        }
        return $result;
    }
}
?>

<?php
class controllerAdmin {
    // Show login form
    public static function formLogin($error = null) {
        $pageTitle = 'Admin Sign In';
        include 'viewAdmin/formLogin.php';
    }

    // Process admin login
    public static function loginAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                self::formLogin('Please fill in all required fields!');
                return;
            }

            $res = modelAdmin::userLogin($email, $password);
            if ($res['success']) {
                header('Location: index.php?action=start');
                exit();
            } else {
                self::formLogin($res['message'] ?? 'Invalid email or password');
                return;
            }
        }
        self::formLogin();
    }

    // Admin logout
    public static function logoutAction() {
        modelAdmin::userLogout();
        header('Location: index.php');
        exit();
    }

    // Admin Dashboard
    public static function startAdmin() {
        $pageTitle = 'Dashboard';
        $stats = modelAdmin::getStats();
        $recentNews = modelAdmin::getNewsList();

        ob_start();
        include 'viewAdmin/startAdmin.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // News list
    public static function newsList() {
        $pageTitle = 'News Management';
        $newsList = modelAdmin::getNewsList();

        ob_start();
        include 'viewAdmin/newsList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Add news form
    public static function newsAddForm($error = null) {
        $pageTitle = 'Add News';
        $categories = modelAdmin::getAllCategories();

        ob_start();
        include 'viewAdmin/newsAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save added news
    public static function newsAddSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $text = $_POST['text'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $userId = (int)($_SESSION['userId'] ?? 1);

            if (empty($title) || empty($text) || empty($categoryId)) {
                self::newsAddForm('Please fill in all required fields!');
                return;
            }

            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            if (empty($pictureBlob)) {
                self::newsAddForm('An image must be uploaded for the news article!');
                return;
            }

            $res = modelAdmin::newsAdd($title, $text, $pictureBlob, $categoryId, $userId);
            if ($res) {
                header('Location: index.php?action=news&msg=added');
                exit();
            } else {
                self::newsAddForm('Error saving article to the database!');
                return;
            }
        }
        self::newsList();
    }

    // Edit news form
    public static function newsEditForm($id, $error = null) {
        $news = modelAdmin::getNewsById($id);
        if (!$news) {
            self::error404();
            return;
        }

        $pageTitle = 'Edit Article #' . $id;
        $categories = modelAdmin::getAllCategories();

        ob_start();
        include 'viewAdmin/newsEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save edited news
    public static function newsEditSave($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $text = $_POST['text'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);

            if (empty($title) || empty($text) || empty($categoryId)) {
                self::newsEditForm($id, 'Please fill in all required fields!');
                return;
            }

            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            $res = modelAdmin::newsEdit($id, $title, $text, $pictureBlob, $categoryId);
            if ($res) {
                header('Location: index.php?action=news&msg=updated');
                exit();
            } else {
                self::newsEditForm($id, 'Error updating news article!');
                return;
            }
        }
        self::newsList();
    }

    // Delete news
    public static function newsDelete($id) {
        modelAdmin::newsDelete($id);
        header('Location: index.php?action=news&msg=deleted');
        exit();
    }

    // Account settings profile form
    public static function profileForm($error = null) {
        $userId = $_SESSION['userId'] ?? 0;
        $user = modelAdmin::getUserById($userId);
        if (!$user) {
            self::error404();
            return;
        }

        $pageTitle = 'Account Settings';

        ob_start();
        include 'viewAdmin/profileForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save profile changes
    public static function profileSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['userId'] ?? 0;
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                self::profileForm('Full Name cannot be empty!');
                return;
            }

            if (!empty($password) && mb_strlen($password, 'UTF-8') < 6) {
                self::profileForm('Password must contain at least 6 characters!');
                return;
            }

            $res = modelAdmin::updateProfile($userId, $username, !empty($password) ? $password : null);
            if ($res) {
                header('Location: index.php?action=profile&msg=saved');
                exit();
            } else {
                self::profileForm('Error saving profile changes!');
                return;
            }
        }
        self::profileForm();
    }

    // Admin 404 Error page
    public static function error404() {
        http_response_code(404);
        $pageTitle = 'Error 404 - Page Not Found';

        ob_start();
        include 'viewAdmin/error404.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }
}
?>

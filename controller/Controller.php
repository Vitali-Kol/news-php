<?php
class Controller {
    // Home page: Top 3 news
    public static function StartSite() {
        $arr = News::getLast3News();
        $pageTitle = 'Home - Latest News';

        ob_start();
        include 'view/start.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // All News page
    public static function AllNews() {
        $arr = News::getAllNews();
        $pageTitle = 'All News';

        ob_start();
        include 'view/allnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // News by category
    public static function NewsByCategory($id) {
        $category = Category::getCategoryById($id);
        if (!$category) {
            self::error404();
            return;
        }

        $arr = News::getNewsByCategory($id);
        $currentCatId = (int)$id;
        $pageTitle = 'Category: ' . ($category['name'] ?? '');

        ob_start();
        include 'view/catnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Read single news article
    public static function ReadNews($id) {
        $n = News::getNewsById($id);
        if (!$n) {
            self::error404();
            return;
        }

        $comments = Comments::getCommentByNewsID($id);
        $commentsCount = Comments::getCommentCountByNewsID($id);
        $pageTitle = $n['title'] ?? 'Read Article';

        ob_start();
        include 'view/readnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Submit a comment
    public static function InsertComment($id) {
        $id = (int)$id;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
            $c = trim($_POST['comment']);
            if (!empty($c) && $id > 0) {
                Comments::insertComment($c, $id);
            }
        }
        header('Location: index.php?action=read&id=' . $id);
        exit();
    }

    // Public login form
    public static function loginForm($error = null) {
        if (isset($_SESSION['userId'])) {
            header('Location: index.php');
            exit();
        }

        $pageTitle = 'Sign In';

        ob_start();
        include 'view/formLogin.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Process user login
    public static function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                self::loginForm('Please fill in all required fields!');
                return;
            }

            $db = new db();
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $user = $db->getOne($query, ['email' => $email]);

            if ($user && (password_verify($password, $user['password']) || $password === $user['pass'])) {
                $_SESSION['userId'] = (int)$user['id'];
                $_SESSION['sessionId'] = session_id();
                $_SESSION['name'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['status'] = $user['status'];

                header('Location: index.php?msg=login_success');
                exit();
            } else {
                self::loginForm('Invalid email or password!');
                return;
            }
        }

        self::loginForm();
    }

    // User logout
    public static function logoutUser() {
        unset($_SESSION['userId']);
        unset($_SESSION['sessionId']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['status']);
        session_destroy();

        header('Location: index.php?msg=logout_success');
        exit();
    }

    // Registration form
    public static function registerForm() {
        $pageTitle = 'Register Account';

        ob_start();
        include 'view/formRegister.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Process registration
    public static function registerUser() {
        $result = Register::registerUser();
        $pageTitle = 'Registration Result';

        ob_start();
        include 'view/answerRegister.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // 404 Error page
    public static function error404() {
        http_response_code(404);
        $pageTitle = 'Error 404 - Page Not Found';

        ob_start();
        include 'view/error404.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }
}
?>

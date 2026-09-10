<?php
class Controller {
    // Стартовая страница: вывод 3 последних новостей
    public static function StartSite() {
        $arr = News::getLast3News();
        $pageTitle = 'Главная - Последние новости';

        ob_start();
        include 'view/start.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Страница всех новостей
    public static function AllNews() {
        $arr = News::getAllNews();
        $pageTitle = 'Все новости';

        ob_start();
        include 'view/allnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Страница новостей по выбранной категории
    public static function NewsByCategory($id) {
        $category = Category::getCategoryById($id);
        if (!$category) {
            self::error404();
            return;
        }

        $arr = News::getNewsByCategory($id);
        $currentCatId = (int)$id;
        $pageTitle = 'Категория: ' . ($category['name'] ?? '');

        ob_start();
        include 'view/catnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Детальный просмотр отдельной новости для чтения
    public static function ReadNews($id) {
        $n = News::getNewsById($id);
        if (!$n) {
            self::error404();
            return;
        }

        $comments = Comments::getCommentByNewsID($id);
        $commentsCount = Comments::getCommentCountByNewsID($id);
        $pageTitle = $n['title'] ?? 'Чтение новости';

        ob_start();
        include 'view/readnews.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Обработка отправки формы добавления комментария
    public static function InsertComment($id) {
        $id = (int)$id;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
            $c = trim($_POST['comment']);
            if (!empty($c) && $id > 0) {
                Comments::insertComment($c, $id);
            }
        }
        // Перенаправляем обратно на страницу чтения этой новости
        header('Location: index.php?action=read&id=' . $id);
        exit();
    }

    // Форма входа для обычных пользователей
    public static function loginForm($error = null) {
        if (isset($_SESSION['userId'])) {
            header('Location: index.php');
            exit();
        }

        $pageTitle = 'Вход на сайт';

        ob_start();
        include 'view/formLogin.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Обработка авторизации пользователя
    public static function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                self::loginForm('Заполните все обязательные поля!');
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
                self::loginForm('Неверный E-mail или пароль!');
                return;
            }
        }

        self::loginForm();
    }

    // Выход пользователя из аккаунта
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

    // Форма регистрации нового пользователя
    public static function registerForm() {
        $pageTitle = 'Регистрация нового пользователя';

        ob_start();
        include 'view/formRegister.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Обработка данных формы регистрации и вывод ответа
    public static function registerUser() {
        $result = Register::registerUser();
        $pageTitle = 'Результат регистрации';

        ob_start();
        include 'view/answerRegister.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }

    // Страница ошибки 404
    public static function error404() {
        http_response_code(404);
        $pageTitle = 'Ошибка 404 - Страница не найдена';

        ob_start();
        include 'view/error404.php';
        $content = ob_get_clean();
        include 'view/layout.php';
    }
}
?>
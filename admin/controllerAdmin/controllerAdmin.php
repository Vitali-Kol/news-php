<?php
class controllerAdmin {
    // Отображение формы авторизации
    public static function formLogin($error = null) {
        $pageTitle = 'Вход в панель управления';
        include 'viewAdmin/formLogin.php';
    }

    // Обработка попытки входа
    public static function loginAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                self::formLogin('Заполните все поля!');
                return;
            }

            $res = modelAdmin::userLogin($email, $password);
            if ($res['success']) {
                header('Location: index.php?action=start');
                exit();
            } else {
                self::formLogin($res['message'] ?? 'Неверный логин или пароль');
                return;
            }
        }
        self::formLogin();
    }

    // Выход из системы
    public static function logoutAction() {
        modelAdmin::userLogout();
        header('Location: index.php');
        exit();
    }

    // Главная страница админ-панели (Дашборд)
    public static function startAdmin() {
        $pageTitle = 'Дашборд';
        $stats = modelAdmin::getStats();
        $recentNews = modelAdmin::getNewsList();

        ob_start();
        include 'viewAdmin/startAdmin.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Список новостей
    public static function newsList() {
        $pageTitle = 'Управление новостями';
        $newsList = modelAdmin::getNewsList();

        ob_start();
        include 'viewAdmin/newsList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Форма добавления новости
    public static function newsAddForm($error = null) {
        $pageTitle = 'Добавить новость';
        $categories = modelAdmin::getAllCategories();

        ob_start();
        include 'viewAdmin/newsAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение новой новости
    public static function newsAddSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $text = $_POST['text'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $userId = (int)($_SESSION['userId'] ?? 1);

            if (empty($title) || empty($text) || empty($categoryId)) {
                self::newsAddForm('Пожалуйста, заполните все обязательные поля!');
                return;
            }

            // Обработка загруженного файла картинки
            $pictureBlob = null;
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $pictureBlob = file_get_contents($_FILES['picture']['tmp_name']);
            }

            if (empty($pictureBlob)) {
                self::newsAddForm('Необходимо прикрепить изображение для новости!');
                return;
            }

            $res = modelAdmin::newsAdd($title, $text, $pictureBlob, $categoryId, $userId);
            if ($res) {
                header('Location: index.php?action=news&msg=added');
                exit();
            } else {
                self::newsAddForm('Ошибка при сохранении новости в базу данных!');
                return;
            }
        }
        self::newsList();
    }

    // Форма редактирования новости
    public static function newsEditForm($id, $error = null) {
        $news = modelAdmin::getNewsById($id);
        if (!$news) {
            self::error404();
            return;
        }

        $pageTitle = 'Редактировать новость #' . $id;
        $categories = modelAdmin::getAllCategories();

        ob_start();
        include 'viewAdmin/newsEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение изменений новости
    public static function newsEditSave($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $text = $_POST['text'] ?? '';
            $categoryId = (int)($_POST['category_id'] ?? 0);

            if (empty($title) || empty($text) || empty($categoryId)) {
                self::newsEditForm($id, 'Пожалуйста, заполните все обязательные поля!');
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
                self::newsEditForm($id, 'Ошибка при обновлении новости!');
                return;
            }
        }
        self::newsList();
    }

    // Удаление новости
    public static function newsDelete($id) {
        modelAdmin::newsDelete($id);
        header('Location: index.php?action=news&msg=deleted');
        exit();
    }

    // Форма управления аккаунтом
    public static function profileForm($error = null) {
        $userId = $_SESSION['userId'] ?? 0;
        $user = modelAdmin::getUserById($userId);
        if (!$user) {
            self::error404();
            return;
        }

        $pageTitle = 'Управление аккаунтом';

        ob_start();
        include 'viewAdmin/profileForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение изменений профиля
    public static function profileSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['userId'] ?? 0;
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                self::profileForm('Имя пользователя не может быть пустым!');
                return;
            }

            if (!empty($password) && mb_strlen($password, 'UTF-8') < 6) {
                self::profileForm('Пароль должен содержать минимум 6 символов!');
                return;
            }

            $res = modelAdmin::updateProfile($userId, $username, !empty($password) ? $password : null);
            if ($res) {
                header('Location: index.php?action=profile&msg=saved');
                exit();
            } else {
                self::profileForm('Ошибка при сохранении профиля!');
                return;
            }
        }
        self::profileForm();
    }

    // Страница 404 ошибки
    public static function error404() {
        http_response_code(404);
        $pageTitle = 'Ошибка 404 - Страница не найдена';

        ob_start();
        include 'viewAdmin/error404.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }
}
?>

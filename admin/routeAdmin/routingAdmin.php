<?php
// Маршрутизатор панели управления (admin/routeAdmin/routingAdmin.php)

$action = isset($_GET['action']) ? $_GET['action'] : 'start';

// Проверяем авторизацию пользователя через сессию
$isAuth = isset($_SESSION['userId']) && isset($_SESSION['sessionId']);

if (!$isAuth) {
    // Неавторизованный пользователь может только пытаться войти или видеть форму логина
    switch ($action) {
        case 'login':
            controllerAdmin::loginAction();
            break;
        default:
            controllerAdmin::formLogin();
            break;
    }
} else {
    // Авторизованный пользователь (администратор)
    switch ($action) {
        case 'start':
            controllerAdmin::startAdmin();
            break;
        case 'login':
            header('Location: index.php?action=start');
            exit();
            break;
        case 'logout':
            controllerAdmin::logoutAction();
            break;
        case 'news':
            controllerAdmin::newsList();
            break;
        case 'newsAdd':
            controllerAdmin::newsAddForm();
            break;
        case 'newsAddSave':
            controllerAdmin::newsAddSave();
            break;
        case 'newsEdit':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdmin::newsEditForm($id);
            break;
        case 'newsEditSave':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdmin::newsEditSave($id);
            break;
        case 'newsDelete':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdmin::newsDelete($id);
            break;
        case 'profile':
            controllerAdmin::profileForm();
            break;
        case 'profileSave':
            controllerAdmin::profileSave();
            break;
        default:
            controllerAdmin::error404();
            break;
    }
}
?>

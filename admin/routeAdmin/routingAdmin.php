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

        // Управление новостями через controllerAdminNews
        case 'newsAdmin':
        case 'news':
            controllerAdminNews::newsList();
            break;
        case 'newsDetail':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminNews::newsDetail($id);
            break;
        case 'newsAdd':
            controllerAdminNews::newsAddForm();
            break;
        case 'newsAddSave':
            controllerAdminNews::newsAddSave();
            break;
        case 'newsEdit':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminNews::newsEditForm($id);
            break;
        case 'newsEditSave':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminNews::newsEditSave($id);
            break;
        case 'newsDelete':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminNews::newsDelete($id);
            break;

        // Управление категориями через controllerAdminCategory
        case 'categoryAdmin':
            controllerAdminCategory::categoryList();
            break;
        case 'categoryAdd':
            controllerAdminCategory::categoryAddForm();
            break;
        case 'categoryAddSave':
            controllerAdminCategory::categoryAddSave();
            break;
        case 'categoryEdit':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminCategory::categoryEditForm($id);
            break;
        case 'categoryEditSave':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminCategory::categoryEditSave($id);
            break;
        case 'categoryDelete':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            controllerAdminCategory::categoryDelete($id);
            break;

        // Управление профилем
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

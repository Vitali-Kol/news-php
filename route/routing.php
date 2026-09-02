<?php
// Маршрутизатор (Router)

// Получаем действие из URL, по умолчанию - start (стартовая страница)
$action = isset($_GET['action']) ? $_GET['action'] : 'start';

// Маршрутизация по действиям
switch ($action) {
    case 'start':
        Controller::StartSite();
        break;
    case 'allnews':
        Controller::AllNews();
        break;
    case 'category':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        Controller::NewsByCategory($id);
        break;
    case 'read':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        Controller::ReadNews($id);
        break;
    case 'insertcomment':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        Controller::InsertComment($id);
        break;
    case 'registerForm':
        Controller::registerForm();
        break;
    case 'registerAnswer':
        Controller::registerUser();
        break;
    default:
        Controller::error404();
}
?>
<?php
// Точка входа в панель управления (admin/index.php)

// Стартуем сессию для работы авторизации
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключаем класс базы данных
require_once '../inc/db.php';

// Подключаем модель админ-панели
require_once 'modelAdmin/modelAdmin.php';

// Подключаем контроллер админ-панели
require_once 'controllerAdmin/controllerAdmin.php';

// Подключаем маршрутизатор админ-панели
require_once 'routeAdmin/routingAdmin.php';
?>

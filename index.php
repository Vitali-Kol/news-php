<?php
// Главный файл проекта (точка входа)

// Стартуем сессию при необходимости
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключаем класс базы данных
require_once 'inc/db.php';

// Подключаем классы моделей
require_once 'model/News.php';
require_once 'model/Category.php';

// Подключаем класс представлений ViewNews
require_once 'view/news.php';

// Подключаем контроллер
require_once 'controller/Controller.php';

// Подключаем маршрутизатор
require_once 'route/routing.php';
?>
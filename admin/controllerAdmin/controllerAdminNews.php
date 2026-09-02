<?php
class controllerAdminNews {
    // Вывод списка новостей
    public static function newsList() {
        $pageTitle = 'Список новостей';
        $newsList = modelAdminNews::getNewsList();

        ob_start();
        include 'viewAdmin/newsList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Детальный просмотр одной новости
    public static function newsDetail($id) {
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Просмотр: ' . mb_substr($news['title'], 0, 40, 'UTF-8') . '...';

        ob_start();
        include 'viewAdmin/newsDetail.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Форма добавления новости
    public static function newsAddForm($error = null) {
        $pageTitle = 'Добавить новость';
        $categories = modelAdminNews::getCategoryList();

        ob_start();
        include 'viewAdmin/newsAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение новой новости
    public static function newsAddSave() {
        $res = modelAdminNews::getNewsAdd();
        if ($res['result']) {
            header('Location: index.php?action=newsAdmin&msg=added');
            exit();
        } else {
            self::newsAddForm($res['message']);
        }
    }

    // Форма редактирования новости
    public static function newsEditForm($id, $error = null) {
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Редактировать новость #' . (int)$id;
        $categories = modelAdminNews::getCategoryList();

        ob_start();
        include 'viewAdmin/newsEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение изменений новости (обработка формы)
    public static function newsEditSave($id) {
        $id = (int)$id;

        // Ещё раз проверяем, что новость существует
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $res = modelAdminNews::getNewsEdit($id);
        if ($res['result']) {
            header('Location: index.php?action=newsDetail&id=' . $id . '&msg=updated');
            exit();
        } else {
            // При ошибке — возвращаем форму с сообщением и сохранёнными данными
            self::newsEditForm($id, $res['message']);
        }
    }

    // Удаление новости
    public static function newsDelete($id) {
        modelAdminNews::getNewsDelete($id);
        header('Location: index.php?action=newsAdmin&msg=deleted');
        exit();
    }
}
?>

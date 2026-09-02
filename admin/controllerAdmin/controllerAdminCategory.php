<?php
class controllerAdminCategory {

    // Список категорий
    public static function categoryList() {
        $pageTitle = 'Управление категориями';
        $categoryList = modelAdminCategory::getCategoryList();

        ob_start();
        include 'viewAdmin/categoryList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Форма добавления категории
    public static function categoryAddForm($error = null) {
        $pageTitle = 'Добавить категорию';

        ob_start();
        include 'viewAdmin/categoryAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение новой категории
    public static function categoryAddSave() {
        $res = modelAdminCategory::categoryAdd();
        if ($res['result']) {
            header('Location: index.php?action=categoryAdmin&msg=added');
            exit();
        } else {
            self::categoryAddForm($res['message']);
        }
    }

    // Форма редактирования категории
    public static function categoryEditForm($id, $error = null) {
        $category = modelAdminCategory::getCategoryById($id);
        if (!$category) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Редактировать категорию #' . (int)$id;

        ob_start();
        include 'viewAdmin/categoryEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Сохранение изменений категории
    public static function categoryEditSave($id) {
        $res = modelAdminCategory::categoryEdit($id);
        if ($res['result']) {
            header('Location: index.php?action=categoryAdmin&msg=updated');
            exit();
        } else {
            self::categoryEditForm($id, $res['message']);
        }
    }

    // Удаление категории
    public static function categoryDelete($id) {
        $res = modelAdminCategory::categoryDelete($id);
        if ($res['result']) {
            header('Location: index.php?action=categoryAdmin&msg=deleted');
        } else {
            header('Location: index.php?action=categoryAdmin&error=' . urlencode($res['message']));
        }
        exit();
    }
}
?>

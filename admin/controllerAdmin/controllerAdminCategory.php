<?php
class controllerAdminCategory {

    // Category list
    public static function categoryList() {
        $pageTitle = 'Category Management';
        $categoryList = modelAdminCategory::getCategoryList();

        ob_start();
        include 'viewAdmin/categoryList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Add category form
    public static function categoryAddForm($error = null) {
        $pageTitle = 'Add Category';

        ob_start();
        include 'viewAdmin/categoryAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save added category
    public static function categoryAddSave() {
        $res = modelAdminCategory::categoryAdd();
        if ($res['result']) {
            header('Location: index.php?action=categoryAdmin&msg=added');
            exit();
        } else {
            self::categoryAddForm($res['message']);
        }
    }

    // Edit category form
    public static function categoryEditForm($id, $error = null) {
        $category = modelAdminCategory::getCategoryById($id);
        if (!$category) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Edit Category #' . (int)$id;

        ob_start();
        include 'viewAdmin/categoryEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save edited category
    public static function categoryEditSave($id) {
        $res = modelAdminCategory::categoryEdit($id);
        if ($res['result']) {
            header('Location: index.php?action=categoryAdmin&msg=updated');
            exit();
        } else {
            self::categoryEditForm($id, $res['message']);
        }
    }

    // Delete category
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

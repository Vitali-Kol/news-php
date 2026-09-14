<?php
class controllerAdminNews {
    // List news
    public static function newsList() {
        $pageTitle = 'News Articles';
        $newsList = modelAdminNews::getNewsList();

        ob_start();
        include 'viewAdmin/newsList.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Detail view
    public static function newsDetail($id) {
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Article: ' . mb_substr($news['title'], 0, 40, 'UTF-8') . '...';

        ob_start();
        include 'viewAdmin/newsDetail.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Add form
    public static function newsAddForm($error = null) {
        $pageTitle = 'Add News Article';
        $categories = modelAdminNews::getCategoryList();

        ob_start();
        include 'viewAdmin/newsAddForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save new article
    public static function newsAddSave() {
        $res = modelAdminNews::getNewsAdd();
        if ($res['result']) {
            header('Location: index.php?action=newsAdmin&msg=added');
            exit();
        } else {
            self::newsAddForm($res['message']);
        }
    }

    // Edit form
    public static function newsEditForm($id, $error = null) {
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Edit Article #' . (int)$id;
        $categories = modelAdminNews::getCategoryList();

        ob_start();
        include 'viewAdmin/newsEditForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Save edited article
    public static function newsEditSave($id) {
        $id = (int)$id;
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
            self::newsEditForm($id, $res['message']);
        }
    }

    // Delete confirmation form
    public static function newsDeleteForm($id) {
        $news = modelAdminNews::getNewsByID($id);
        if (!$news) {
            controllerAdmin::error404();
            return;
        }

        $pageTitle = 'Delete Article #' . (int)$id;
        $commentCount = modelAdminNews::getCommentCount($id);

        ob_start();
        include 'viewAdmin/newsDeleteForm.php';
        $content = ob_get_clean();
        include 'viewAdmin/templates/layout.php';
    }

    // Execute deletion (POST)
    public static function newsDelete($id) {
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::newsDeleteForm($id);
            return;
        }

        $res = modelAdminNews::getNewsDelete($id);
        if ($res['result']) {
            header('Location: index.php?action=newsAdmin&msg=deleted');
        } else {
            header('Location: index.php?action=newsAdmin&error=' . urlencode($res['message']));
        }
        exit();
    }
}
?>

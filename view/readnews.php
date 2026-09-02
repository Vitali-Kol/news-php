<?php
// Вывод выбранной новости для чтения
ViewNews::readNews($n);

// Форма добавления комментария
ViewComments::CommentsForm($n['id']);

// Блок вывода комментариев к новости
echo '<div id="comments">';
ViewComments::CommentsByNews($comments ?? []);
echo '</div>';
?>

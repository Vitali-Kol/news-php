<?php
// Single article view
ViewNews::readNews($n);

// Comment Submission Form
ViewComments::CommentsForm($n['id']);

// Comments List Section
echo '<div id="comments">';
ViewComments::CommentsByNews($comments ?? []);
echo '</div>';
?>

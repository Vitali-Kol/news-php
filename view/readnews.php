<?php
// Single article view
ViewNews::readNews($n);

// Related Articles Section (if available)
if (!empty($relatedNews)) {
    echo '<div class="related-news-section mt-4 mb-5">';
    echo '<h4 class="fw-bold mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Related Articles in ' . htmlspecialchars($n['category_name'] ?? 'this category') . '</h4>';
    echo '<div class="row">';
    foreach ($relatedNews as $rel) {
        ViewNews::renderCard($rel);
    }
    echo '</div>';
    echo '</div>';
}

// Comment Submission Feedback Alert
if (isset($_GET['msg']) && $_GET['msg'] === 'comment_added') {
    echo '
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>Your comment has been posted successfully!</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Comment Submission Form
ViewComments::CommentsForm($n['id']);

// Comments List Section
echo '<div id="comments">';
ViewComments::CommentsByNews($comments ?? []);
echo '</div>';
?>

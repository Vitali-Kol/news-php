<?php
// Страница ответа после попытки регистрации
$isSuccess = isset($result['result']) && $result['result'] === true;
$message = isset($result['message']) ? htmlspecialchars($result['message']) : '';
?>
<div class="card shadow-sm border-0 text-center py-5 px-4 my-4 mx-auto" style="max-width: 600px;">
    <div class="card-body">
        <?php if ($isSuccess): ?>
            <div class="display-3 text-success mb-3">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="h3 fw-bold text-dark mb-3">Регистрация завершена!</h2>
            <p class="text-muted fs-5 mb-4">
                <?= $message ?>
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="admin/index.php" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Войти в систему
                </a>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                    <i class="bi bi-house me-1"></i> На главную
                </a>
            </div>
        <?php else: ?>
            <div class="display-3 text-danger mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h2 class="h3 fw-bold text-danger mb-3">Ошибка регистрации</h2>
            <p class="text-muted fs-5 mb-4">
                <?= $message ?>
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="index.php?action=registerForm" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="bi bi-arrow-repeat me-1"></i> Попробовать снова
                </a>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                    <i class="bi bi-house me-1"></i> На главную
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

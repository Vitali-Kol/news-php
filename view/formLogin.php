<?php
// Форма авторизации обычного пользователя на основном сайте
?>
<div class="card shadow-sm border-0 my-4 mx-auto" style="max-width: 480px; border-radius: 16px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-box-arrow-in-right fs-2"></i>
            </div>
            <h1 class="h3 fw-bold mb-1">Вход на сайт</h1>
            <p class="text-muted small mb-0">Введите ваш E-mail и пароль для входа в аккаунт</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="index.php?action=loginAction" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">E-mail адрес</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Пароль</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ваш пароль" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Войти
            </button>

            <div class="text-center small text-muted mb-4">
                Нет аккаунта? <a href="index.php?action=registerForm" class="text-primary text-decoration-none fw-semibold">Зарегистрироваться</a>
            </div>

            <hr class="my-4">

            <div class="text-center">
                <p class="small text-muted mb-2">Вы администратор?</p>
                <a href="admin/index.php" class="btn btn-outline-dark btn-sm rounded-pill px-4">
                    <i class="bi bi-shield-lock me-1"></i> Вход в панель администратора
                </a>
            </div>
        </form>
    </div>
</div>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в панель управления | NewsPortal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <div class="container p-3">
        <div class="card login-card mx-auto border-0">
            <div class="card-header bg-dark text-white text-center py-4 border-0">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 54px; height: 54px;">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">Вход в Админ-панель</h4>
                <p class="text-secondary small mb-0 mt-1">Авторизация для управления новостями</p>
            </div>

            <div class="card-body p-4 bg-white">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=login" method="POST">
                    <div class="mb-3">
                        <label for="emailInput" class="form-label text-dark fw-semibold small">E-mail адрес</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="emailInput" name="email" placeholder="admin@newsportal.ee" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="passwordInput" class="form-label text-dark fw-semibold small">Пароль</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Введите пароль" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill shadow-sm mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Войти в систему
                    </button>
                </form>

                <div class="text-center pt-2 border-top">
                    <a href="../index.php" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Вернуться на сайт
                    </a>
                </div>

                <div class="alert alert-light border small text-muted mt-3 mb-0">
                    <strong>Тестовые аккаунты:</strong><br>
                    • Администратор: <code>admin@newsportal.ee</code> / <code>123456</code><br>
                    • Пользователь: <code>user@newsportal.ee</code> / <code>111111</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

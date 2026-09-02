<?php
// Форма регистрации пользователя
?>
<div class="card shadow-sm border-0 mx-auto my-3" style="max-width: 600px;">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="card-title fw-bold mb-0">
            <i class="bi bi-person-plus-fill me-2"></i> Регистрация нового пользователя
        </h4>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-4">
            Заполните форму ниже для создания учетной записи на сайте новостей. Все поля обязательны для заполнения.
        </p>

        <form action="index.php?action=registerAnswer" method="POST">
            <div class="mb-3">
                <label for="regUsername" class="form-label fw-semibold">Имя пользователя <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="regUsername" name="username" placeholder="Иван Иванов" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="regEmail" class="form-label fw-semibold">E-mail адрес <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="regEmail" name="email" placeholder="example@mail.ee" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-text">E-mail используется в качестве логина и должен быть уникальным.</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="regPassword" class="form-label fw-semibold">Пароль <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="regPassword" name="password" placeholder="Минимум 6 символов" required minlength="6">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="regPasswordConfirm" class="form-label fw-semibold">Повторите пароль <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                        <input type="password" class="form-control" id="regPasswordConfirm" name="passwordConfirm" placeholder="Повторите пароль" required minlength="6">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Зарегистрироваться
                </button>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top small text-muted">
            Уже есть аккаунт? <a href="admin/index.php" class="text-primary text-decoration-none fw-semibold">Войти в систему</a>
        </div>
    </div>
</div>

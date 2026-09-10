<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Сайт Новостей</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .main-container {
            flex: 1 0 auto;
        }
        .news-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .news-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
            border-radius: 12px;
        }
        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12) !important;
        }
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .footer {
            flex-shrink: 0;
            background-color: #212529;
            color: #adb5bd;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .admin-btn {
            background-color: #2c3e50;
            border-color: #4b6584;
            color: #f1f2f6;
            transition: all 0.2s;
        }
        .admin-btn:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- ================= HEADER: Раздел меню ================= -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <i class="bi bi-newspaper fs-3 text-primary me-2"></i>
                    <span>News<span class="text-primary">Portal</span></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] === 'start') ? 'active fw-bold' : '' ?>" href="index.php">
                                <i class="bi bi-house-door me-1"></i> Главная
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] === 'allnews') ? 'active fw-bold' : '' ?>" href="index.php?action=allnews">
                                <i class="bi bi-collection me-1"></i> Все новости
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= (isset($_GET['action']) && $_GET['action'] === 'category') ? 'active fw-bold' : '' ?>" href="#" id="navbarDropdownCat" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tags me-1"></i> Категории
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="navbarDropdownCat">
                                <?php
                                $menuCategories = Category::getAllCategories();
                                foreach ($menuCategories as $menuCat):
                                ?>
                                    <li>
                                        <a class="dropdown-item" href="index.php?action=category&id=<?= (int)$menuCat['id'] ?>">
                                            <?= htmlspecialchars($menuCat['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>

                    <!-- Правый блок: Авторизация пользователей и отдельная кнопка Админки -->
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <?php if (isset($_SESSION['userId'])): ?>
                            <!-- Авторизованный пользователь -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1 fs-6"></i>
                                    <span><?= htmlspecialchars($_SESSION['name'] ?? 'Пользователь') ?></span>
                                    <span class="badge <?= ($_SESSION['status'] ?? '') === 'admin' ? 'bg-danger' : 'bg-primary' ?> ms-2 small"><?= htmlspecialchars($_SESSION['status'] ?? 'user') ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><h6 class="dropdown-header"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></h6></li>
                                    <?php if (($_SESSION['status'] ?? '') === 'admin'): ?>
                                        <li><a class="dropdown-item text-primary" href="admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Панель администратора</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item text-danger" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                                </ul>
                            </div>

                            <!-- Отдельная кнопка быстрого перехода в админку для админа -->
                            <?php if (($_SESSION['status'] ?? '') === 'admin'): ?>
                                <a href="admin/index.php" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" title="Перейти в панель администратора">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Панель Admin
                                </a>
                            <?php endif; ?>

                            <a href="index.php?action=logout" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Выйти из аккаунта">
                                <i class="bi bi-box-arrow-right me-1"></i> Выход
                            </a>

                        <?php else: ?>
                            <!-- Неавторизованный посетитель -->
                            <!-- 1. Основной вход для обычных пользователей -->
                            <a href="index.php?action=login" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Вход
                            </a>

                            <!-- 2. Регистрация нового пользователя -->
                            <a href="index.php?action=registerForm" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="bi bi-person-plus me-1"></i> Регистрация
                            </a>

                            <div class="vr bg-secondary mx-1 d-none d-lg-block" style="height: 24px;"></div>

                            <!-- 3. Отдельная кнопка для входа в панель администратора -->
                            <a href="admin/index.php" class="btn btn-sm admin-btn rounded-pill px-3" title="Вход для администратора">
                                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Панель Admin
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- ================= MAIN: Раздел содержания ================= -->
    <main class="main-container py-4">
        <div class="container">
            <!-- Уведомления об успешном входе / выходе -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'login_success'): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['name'] ?? '') ?></strong>! Вы успешно авторизовались на сайте.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'logout_success'): ?>
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <div>Вы успешно вышли из системы.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Основная колонка контента -->
                <div class="col-lg-8 col-md-7">
                    <?= $content ?? '' ?>
                </div>

                <!-- Боковая колонка (сайдбар с категориями и информацией) -->
                <div class="col-lg-4 col-md-5">
                    <?php include 'view/category.php'; ?>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white fw-bold py-3">
                            <i class="bi bi-info-circle me-2"></i> О портале
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted small mb-0">
                                Добро пожаловать на новостной портал! Здесь вы найдете самые свежие и актуальные новости, сгруппированные по категориям.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="footer py-4 mt-auto">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-3 mb-md-0">
                    <h5 class="text-white mb-1"><i class="bi bi-newspaper text-primary me-2"></i>NewsPortal</h5>
                    <p class="small text-muted mb-0">&copy; <?= date('Y') ?> Сайт Новостей. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="index.php" class="text-secondary text-decoration-none me-3 small">Главная</a>
                    <a href="index.php?action=allnews" class="text-secondary text-decoration-none me-3 small">Все новости</a>
                    <a href="index.php?action=login" class="text-secondary text-decoration-none me-3 small">Вход</a>
                    <a href="index.php?action=registerForm" class="text-secondary text-decoration-none me-3 small">Регистрация</a>
                    <a href="admin/index.php" class="text-secondary text-decoration-none small"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Панель Admin</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

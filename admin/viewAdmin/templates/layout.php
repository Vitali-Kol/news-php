<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Панель управления</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-sidebar {
            min-height: calc(100vh - 60px);
            background-color: #1e293b;
        }
        .admin-sidebar .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }
        .admin-sidebar .nav-link:hover {
            color: #ffffff;
            background-color: #334155;
        }
        .admin-sidebar .nav-link.active {
            color: #ffffff;
            background-color: #0d6efd;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        .stat-card {
            border-radius: 12px;
            border: none;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .table th {
            font-weight: 600;
            background-color: #f8fafc;
        }
        .footer-admin {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Верхняя панель (Navbar) -->
    <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top shadow-sm px-3" style="height: 60px;">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="bi bi-shield-lock-fill text-primary me-2 fs-4"></i>
            <span>News<span class="text-primary">Admin</span></span>
        </a>
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item me-3">
                <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="../index.php" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Перейти на сайт
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span><?= htmlspecialchars($_SESSION['name'] ?? 'Администратор') ?></span>
                    <span class="badge bg-danger ms-2"><?= htmlspecialchars($_SESSION['status'] ?? 'admin') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="index.php?action=profile"><i class="bi bi-person-gear me-2"></i> Профиль / Смена пароля</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i> Выход</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Главная область: Сайдбар + Контент -->
    <div class="container-fluid content-wrapper">
        <div class="row">
            <!-- Боковая панель (Sidebar) -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar py-4 px-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] === 'start') ? 'active' : '' ?>" href="index.php?action=start">
                            <i class="bi bi-speedometer2 me-2"></i> Главная (Дашборд)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['action']) && ($_GET['action'] === 'newsAdmin' || $_GET['action'] === 'news')) ? 'active' : '' ?>" href="index.php?action=newsAdmin">
                            <i class="bi bi-newspaper me-2"></i> Список новостей
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] === 'newsAdd') ? 'active' : '' ?>" href="index.php?action=newsAdd">
                            <i class="bi bi-plus-circle me-2"></i> Добавить новость
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['action']) && in_array($_GET['action'], ['categoryAdmin','categoryAdd','categoryEdit'])) ? 'active' : '' ?>" href="index.php?action=categoryAdmin">
                            <i class="bi bi-folder2-open me-2"></i> Категории
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] === 'profile') ? 'active' : '' ?>" href="index.php?action=profile">
                            <i class="bi bi-person-gear me-2"></i> Управление аккаунтом
                        </a>
                    </li>
                    <li class="nav-item mt-4 pt-3 border-top border-secondary">
                        <a class="nav-link text-danger" href="index.php?action=logout">
                            <i class="bi bi-box-arrow-right me-2"></i> Выйти из системы
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Основной контент -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Подвал -->
    <footer class="footer-admin py-3 mt-auto text-center small">
        <div class="container-fluid">
            &copy; <?= date('Y') ?> Панель управления Новостным Порталом.
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

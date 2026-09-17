<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>NewsPortal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --app-bg: #f8f9fa;
            --app-card-bg: #ffffff;
            --app-text: #212529;
            --app-border: #e9ecef;
        }
        [data-bs-theme="dark"] {
            --app-bg: #0f172a;
            --app-card-bg: #1e293b;
            --app-text: #f8fafc;
            --app-border: #334155;
        }
        body {
            background-color: var(--app-bg);
            color: var(--app-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s, color 0.3s;
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
            background-color: var(--app-card-bg);
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
            background-color: #0f172a;
            color: #94a3b8;
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
        #readingProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            z-index: 9999;
            width: 0%;
            transition: width 0.1s;
        }
        .theme-toggle-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
        }
    </style>
</head>
<body>
    <div id="readingProgress"></div>

    <!-- ================= HEADER ================= -->
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
                                <i class="bi bi-house-door me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['action']) && $_GET['action'] === 'allnews') ? 'active fw-bold' : '' ?>" href="index.php?action=allnews">
                                <i class="bi bi-collection me-1"></i> All News
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= (isset($_GET['action']) && $_GET['action'] === 'category') ? 'active fw-bold' : '' ?>" href="#" id="navbarDropdownCat" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tags me-1"></i> Categories
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

                    <!-- Search Bar Form -->
                    <form class="d-flex me-lg-3 my-2 my-lg-0" role="search" action="index.php" method="GET">
                        <input type="hidden" name="action" value="search">
                        <div class="input-group input-group-sm">
                            <input class="form-control rounded-start-pill px-3" type="search" name="q" placeholder="Search news..." aria-label="Search" required value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                            <button class="btn btn-primary rounded-end-pill px-3" type="submit" title="Search">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Right Header Block: Dark Mode + Auth & Admin Button -->
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <!-- Dark / Light Mode Switcher Button -->
                        <button class="btn btn-sm btn-outline-secondary theme-toggle-btn text-warning me-1" id="themeToggle" type="button" title="Toggle Dark/Light Mode">
                            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                        </button>

                        <?php if (isset($_SESSION['userId'])): ?>
                            <!-- Authenticated User -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1 fs-6"></i>
                                    <span><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
                                    <span class="badge <?= ($_SESSION['status'] ?? '') === 'admin' ? 'bg-danger' : 'bg-primary' ?> ms-2 small"><?= htmlspecialchars($_SESSION['status'] ?? 'user') ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><h6 class="dropdown-header"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></h6></li>
                                    <?php if (($_SESSION['status'] ?? '') === 'admin'): ?>
                                        <li><a class="dropdown-item text-primary" href="admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item text-danger" href="index.php?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                                </ul>
                            </div>

                            <!-- Direct Admin Panel Button for Admin -->
                            <?php if (($_SESSION['status'] ?? '') === 'admin'): ?>
                                <a href="admin/index.php" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" title="Go to Admin Panel">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Admin Panel
                                </a>
                            <?php endif; ?>

                            <a href="index.php?action=logout" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Log out">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a>

                        <?php else: ?>
                            <!-- Anonymous Guest -->
                            <!-- 1. Public User Login -->
                            <a href="index.php?action=login" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>

                            <!-- 2. User Registration -->
                            <a href="index.php?action=registerForm" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>

                            <div class="vr bg-secondary mx-1 d-none d-lg-block" style="height: 24px;"></div>

                            <!-- 3. Direct Admin Gateway Button -->
                            <a href="admin/index.php" class="btn btn-sm admin-btn rounded-pill px-3" title="Administrator Login">
                                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Admin Panel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-container py-4">
        <div class="container">
            <!-- Flash Notification Alerts -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'login_success'): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>Welcome, <strong><?= htmlspecialchars($_SESSION['name'] ?? '') ?></strong>! You have successfully logged in.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'logout_success'): ?>
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <div>You have successfully logged out.</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-8 col-md-7">
                    <?= $content ?? '' ?>
                </div>

                <!-- Sidebar Column -->
                <div class="col-lg-4 col-md-5">
                    <?php include 'view/category.php'; ?>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white fw-bold py-3">
                            <i class="bi bi-info-circle me-2"></i> About Portal
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted small mb-0">
                                Welcome to NewsPortal! Here you will find the latest and most relevant news articles organized by category.
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
                    <p class="small text-muted mb-0">&copy; <?= date('Y') ?> NewsPortal. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="index.php" class="text-secondary text-decoration-none me-3 small">Home</a>
                    <a href="index.php?action=allnews" class="text-secondary text-decoration-none me-3 small">All News</a>
                    <a href="index.php?action=login" class="text-secondary text-decoration-none me-3 small">Login</a>
                    <a href="index.php?action=registerForm" class="text-secondary text-decoration-none me-3 small">Register</a>
                    <a href="admin/index.php" class="text-secondary text-decoration-none small"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Admin Panel</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Switcher Logic
        (function() {
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const htmlElement = document.documentElement;

            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme') || 'light';
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                    localStorage.setItem('theme', newTheme);
                });
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.classList.remove('bi-moon-stars-fill');
                        themeIcon.classList.add('bi-sun-fill');
                    } else {
                        themeIcon.classList.remove('bi-sun-fill');
                        themeIcon.classList.add('bi-moon-stars-fill');
                    }
                }
            }
        })();

        // Reading Progress Indicator
        window.addEventListener('scroll', () => {
            const progressBar = document.getElementById('readingProgress');
            if (progressBar) {
                const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
                if (totalHeight > 0) {
                    const progress = (window.scrollY / totalHeight) * 100;
                    progressBar.style.width = Math.min(100, Math.max(0, progress)) + '%';
                }
            }
        });
    </script>
</body>
</html>

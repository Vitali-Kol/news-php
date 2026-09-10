# News Portal (NewsPortal) — PHP MVC

A web application for a news portal featuring a comprehensive administration panel and user registration, developed in pure PHP using the **MVC** (Model-View-Controller) architectural pattern and MySQL database.

---

## Table of Contents
1. [Technical Specification (ТЗ)](SPECIFICATION.md)
2. [Key Features](#key-features)
3. [User Guide / How to Use the Website](#user-guide--how-to-use-the-website)
   - [Public Website (Regular Users & Visitors)](#1-public-website-regular-users--visitors)
   - [Administration Panel (Administrators)](#2-administration-panel-administrators)
4. [Test Accounts](#test-accounts)
5. [Project Structure](#project-structure)
6. [Installation & Setup](#installation--setup)

---

## Key Features
- **MVC Architecture**: Strict separation of business logic, controllers, and views.
- **Home Page**: Displays the 3 latest published news articles.
- **All News**: Catalog of all articles with a total publication counter.
- **Categories**: Filter and browse news by topic/category.
- **Detailed View**: Full article page with metadata, author details, full-size image, and comments.
- **Article Comments**: Add comments via form, view list of comments, and display comment counters on cards and article pages.
- **User Registration**:
  - Registration with validation for email uniqueness, password confirmation, and length requirements.
  - Automatic assignment of the `user` role to new accounts (only the administrator holds the `admin` role).
  - Secure password hashing (`password_hash`).
- **User & Admin Authentication**:
  - Main user login on the public site and separate direct entrance to the Admin Panel.
  - Session-based authentication (`$_SESSION['userId']`, `$_SESSION['status']`).
  - Role-based access control (`admin` / `user`).
- **Admin Panel (`admin/`)**:
  - Interactive dashboard with summary metrics (total news, categories, comments, users).
  - News management (CRUD: create with image upload, read, update, delete with confirmation).
  - Category management (CRUD: create, edit, delete with protection against deleting non-empty categories).
  - Account management (update profile name and change password).
- **Image Storage**: Support for storing image files in MySQL as BLOB data with Base64 Data URI rendering.
- **Error Handling**: Custom 404 error pages for both the public website and the admin panel.
- **Responsive Design**: Modern, responsive UI built with Bootstrap 5.

---

## User Guide / How to Use the Website

### 1. Public Website (Regular Users & Visitors)

#### 🏠 Browsing News
- **Home Page (`index.php`)**: When you open the website, the 3 newest published articles are displayed on the main page.
- **All News (`index.php?action=allnews`)**: Click on **"Все новости" (All News)** in the top navigation bar to view the complete list of all publications with publication count.
- **Category Filter (`index.php?action=category&id=X`)**: Click on **"Категории" (Categories)** in the top menu or select a category from the right sidebar to view articles belonging to a specific topic.

#### 📖 Reading Articles & Leaving Comments
- Click on any article title or the **"Читать далее" (Read more)** button to open the full article page (`index.php?action=read&id=X`).
- On the article page, you will see the complete text, category badge, author name, publication date, and full-size image.
- Scroll down to the **Comments section**:
  - View all previously posted comments with author and timestamps.
  - Submit your own comment using the **"Добавить комментарий" (Add comment)** form.

#### 👤 User Registration & Login
- **Register a New Account (`index.php?action=registerForm`)**:
  1. Click **"Регистрация" (Register)** in the top navigation bar.
  2. Fill in your **Name**, **E-mail**, and **Password** (min. 6 characters), then confirm the password.
  3. Click **"Зарегистрироваться" (Register)**. Upon success, an account with the `user` role will be created.
- **Log In (`index.php?action=login`)**:
  1. Click **"Вход" (Login)** in the top header.
  2. Enter your registered email and password.
  3. After successful login, your name and `user` badge will appear in the top right corner.
- **Log Out (`index.php?action=logout`)**:
  - Click the **"Выход" (Logout)** button in the header at any time to end your session.

---

### 2. Administration Panel (Administrators)

#### 🛡️ Accessing the Admin Panel
- In the top header of the public website, click the dark **"🛡️ Панель Admin"** button (or navigate directly to `http://localhost/projekt/admin/`).
- If not logged in as an administrator, the **Admin Login Form** will be shown.
- Enter admin credentials: `admin@newsportal.ee` / `123456`.

#### 📊 Dashboard (`admin/index.php?action=start`)
- View real-time system metrics: Total News, Categories, Comments, and Registered Users.
- Review the recent publications table and use quick-action buttons.

#### 📰 News Management (`admin/index.php?action=newsAdmin`)
- **View All News**: Click **"Список новостей" (News List)** in the sidebar to see the table of all articles with preview thumbnails, categories, and authors.
- **Detailed Preview (`action=newsDetail&id=X`)**: Click the 👁 icon to view full article content, image, and metadata inside the admin panel.
- **Add New Article (`action=newsAdd`)**:
  1. Click **"Добавить новость" (Add News)**.
  2. Enter the **Title**, select a **Category**, upload an **Image** (JPEG/PNG), and enter the **Text**.
  3. Click **"Опубликовать новость" (Publish)**.
- **Edit Article (`action=newsEdit&id=X`)**:
  1. Click the ✏️ icon on any article.
  2. Modify title, category, or text. Optionally select a new image file (or leave empty to keep the current one).
  3. Click **"Сохранить изменения" (Save Changes)**.
- **Delete Article (`action=newsDeleteForm&id=X`)**:
  1. Click the 🗑 icon on any article.
  2. The confirmation page will display the article details, thumbnail, and the number of attached comments that will be removed.
  3. Click **"Да, удалить новость" (Confirm Delete)**.

#### 📁 Category Management (`admin/index.php?action=categoryAdmin`)
- **View Categories**: Click **"Категории" (Categories)** in the sidebar to view all categories and see how many articles belong to each category.
- **Add Category (`action=categoryAdd`)**: Click **"Добавить категорию" (Add Category)**, enter a unique name, and save.
- **Edit Category (`action=categoryEdit&id=X`)**: Click ✏️ next to a category to rename it.
- **Delete Category (`action=categoryDelete&id=X`)**: Click 🗑 next to a category. *Note: The system protects data integrity and prevents deleting categories that currently contain articles.*

#### ⚙️ Profile & Password Management (`admin/index.php?action=profile`)
- Click **"Управление аккаунтом" (Account Settings)** in the sidebar.
- Update your display name and change your administrator password securely.

---

## Test Accounts
| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **Administrator** | `admin@newsportal.ee` | `123456` | Full access: Public website + Admin Panel (CRUD News, Categories, Users) |
| **Regular User** | `user@newsportal.ee` | `111111` | Public website: Reading articles, browsing categories, adding comments |

---

## Project Structure
```
projekt/
├── admin/                             # Administrator Control Panel
│   ├── controllerAdmin/
│   │   ├── controllerAdmin.php         # Admin controller (auth, dashboard, profile)
│   │   ├── controllerAdminNews.php     # News management controller (CRUD, view)
│   │   └── controllerAdminCategory.php # Category management controller (CRUD)
│   ├── modelAdmin/
│   │   ├── modelAdmin.php              # Admin model (login, logout, metrics, password change)
│   │   ├── modelAdminNews.php          # News admin model (CRUD queries)
│   │   └── modelAdminCategory.php      # Category admin model (CRUD queries)
│   ├── routeAdmin/
│   │   └── routingAdmin.php            # Admin panel router (newsAdmin, categoryAdmin, etc.)
│   ├── viewAdmin/
│   │   ├── templates/
│   │   │   └── layout.php              # Admin panel base layout (Bootstrap 5)
│   │   ├── formLogin.php               # Admin login form
│   │   ├── startAdmin.php              # Dashboard with statistics and recent posts
│   │   ├── newsList.php                # News management table
│   │   ├── newsDetail.php              # Detailed single news view in admin
│   │   ├── newsAddForm.php             # Add new article form (with file upload)
│   │   ├── newsEditForm.php            # Edit article form
│   │   ├── newsDeleteForm.php          # Delete confirmation page
│   │   ├── categoryList.php            # Categories management table
│   │   ├── categoryAddForm.php         # Add category form
│   │   ├── categoryEditForm.php        # Edit category form
│   │   ├── profileForm.php             # Account settings & password change form
│   │   └── error404.php                # 404 error page for admin
│   └── index.php                       # Admin panel entry point
├── controller/
│   └── Controller.php                 # Public website controller (articles, comments, login, registration)
├── inc/
│   └── db.php                         # Database connection & query handler (PDO)
├── model/
│   ├── News.php                       # Public news model
│   ├── Category.php                   # Public category model
│   ├── Comments.php                   # Comments model
│   └── Register.php                   # User registration model
├── route/
│   └── routing.php                    # Public router (start, allnews, category, read, login, register)
├── view/
│   ├── layout.php                     # Public base layout (navigation, auth buttons, footer)
│   ├── start.php                      # Home page (top 3 latest articles)
│   ├── allnews.php                    # All articles catalog
│   ├── catnews.php                    # Articles by selected category
│   ├── readnews.php                   # Single article reading page (article + comments)
│   ├── category.php                   # Categories sidebar widget / menu
│   ├── formLogin.php                  # Public user login form
│   ├── formRegister.php               # User registration form
│   ├── answerRegister.php             # Registration result view (success / error)
│   ├── news.php                       # ViewNews helper class (BLOB image rendering)
│   ├── comments.php                   # ViewComments helper class
│   └── error404.php                   # 404 error page
├── index.php                          # Public website entry point
└── newsportal.sql                     # MySQL database dump
```

---

## Installation & Setup

1. Clone the repository into your web server directory (e.g., `xampp/htdocs/projekt`):
   ```bash
   git clone https://github.com/Vitali-Kol/news-php.git
   ```
2. Import the `newsportal.sql` database dump into MySQL:
   - Via phpMyAdmin: create a database named `newsportal` and import `newsportal.sql`.
   - Or via command line:
     ```bash
     mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS newsportal;"
     mysql -u root -p newsportal < newsportal.sql
     ```
3. If necessary, adjust your database connection settings in `inc/db.php`.
4. Open the website in your browser:
   - Public Website: `http://localhost/projekt/`
   - User Login: `http://localhost/projekt/index.php?action=login`
   - Registration: `http://localhost/projekt/index.php?action=registerForm`
   - Admin Panel: `http://localhost/projekt/admin/`

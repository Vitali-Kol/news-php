# News Portal (NewsPortal) — PHP MVC

A web application for a news portal featuring a comprehensive administration panel and user registration, developed in pure PHP using the **MVC** (Model-View-Controller) architectural pattern and MySQL database.

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

## Test Accounts
- **Administrator:** `admin@newsportal.ee` / `123456` (full access to admin panel and management)
- **Regular User:** `user@newsportal.ee` / `111111` (public website access and comments)

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

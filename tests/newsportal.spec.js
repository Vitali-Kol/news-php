// @ts-check
const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const BASE = 'http://localhost/projekt';

// Visual highlight on element before clicking
async function visualClick(locator) {
  try {
    await locator.evaluate((el) => {
      el.style.outline = '3px solid #ef4444';
      el.style.boxShadow = '0 0 15px rgba(239, 68, 68, 0.8)';
    });
  } catch (e) {}
  await locator.page().waitForTimeout(200);
  await locator.click();
}

test.describe('NewsPortal (PHP MVC) — Playwright Visual E2E Tests', () => {

  // =========================================================================
  // SCENARIO 1: Visitor Journey
  // =========================================================================
  test('1. Visitor Journey: Home, Catalog, Category Filter, Article & Comment', async ({ page }) => {
    // 1.1 Home page
    await page.goto(`${BASE}/index.php`);
    await expect(page.locator('.navbar-brand')).toContainText('NewsPortal');
    await expect(page.locator('body')).toContainText(/TOP 3 NEWS|Home|Latest News|Главная|Последние публикации/i);

    // 1.2 All News catalog
    const allNewsLink = page.locator('nav a[href*="allnews"]');
    await visualClick(allNewsLink);
    await expect(page.locator('body')).toContainText(/All News|Все новости|Kõik uudised/i);

    // 1.3 Category Filter
    const catDropdown = page.locator('#navbarDropdownCat');
    await visualClick(catDropdown);
    const firstCatItem = page.locator('.dropdown-menu .dropdown-item').first();
    await visualClick(firstCatItem);
    await expect(page.locator('body')).toContainText(/Category|Категория/i);

    // 1.4 Open single article
    const readBtn = page.locator('.news-card a.btn, a[href*="action=read"]').first();
    await visualClick(readBtn);
    await expect(page.locator('body')).toContainText(/Comments|Комментарии/i);

    // 1.5 Submit a comment
    const commentInput = page.locator('textarea[name="comment"]');
    const commentText = `Playwright E2E Comment [${Date.now()}]`;
    await commentInput.fill(commentText);
    const submitCommentBtn = page.locator('button[type="submit"]:has-text("Comment"), button[type="submit"]:has-text("комментарий"), button[type="submit"]').first();
    await visualClick(submitCommentBtn);

    // Verify comment appears in feed
    await expect(page.locator('body')).toContainText(commentText);
  });

  // =========================================================================
  // SCENARIO 2: User Registration & Public Login Journey
  // =========================================================================
  test('2. User Journey: Registration Validation, Login, Session Badge, & Logout', async ({ page }) => {
    const uniqueEmail = `playwright_${Date.now()}@newsportal.ee`;
    const password = 'securePassword123';

    // 2.1 Registration form
    await page.goto(`${BASE}/index.php?action=registerForm`);
    await expect(page.locator('body')).toContainText(/Register|Create|Регистрация/i);

    // 2.2 Validation: Mismatched password
    await page.locator('#regUsername').fill('Playwright Tester');
    await page.locator('#regEmail').fill(uniqueEmail);
    await page.locator('#regPassword').fill(password);
    await page.locator('#regPasswordConfirm').fill('differentPassword');
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/do not match|не совпадают|Failed|Ошибка/i);

    // 2.3 Successful registration
    await page.goto(`${BASE}/index.php?action=registerForm`);
    await page.locator('#regUsername').fill('Playwright Tester');
    await page.locator('#regEmail').fill(uniqueEmail);
    await page.locator('#regPassword').fill(password);
    await page.locator('#regPasswordConfirm').fill(password);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/Successful|Complete|Регистрация завершена|успешно/i);

    // 2.4 Login as registered user
    await page.goto(`${BASE}/index.php?action=login`);
    await page.locator('#email').fill(uniqueEmail);
    await page.locator('#password').fill(password);
    await visualClick(page.locator('form button[type="submit"]'));

    // 2.5 Verify logged in state in navbar
    await expect(page.locator('.navbar')).toContainText('Playwright Tester');
    await expect(page.locator('.navbar')).toContainText('user');

    // 2.6 Logout
    const logoutBtn = page.locator('a.btn-outline-danger[href*="action=logout"], a[href*="action=logout"]:visible').first();
    await visualClick(logoutBtn);
    await expect(page.locator('.navbar')).toContainText(/Login|Sign In|Вход/i);
  });

  // =========================================================================
  // SCENARIO 3: Administration Panel Journey (Full CRUD)
  // =========================================================================
  test('3. Admin Journey: Dashboard, Category CRUD, News CRUD with Image, & Logout', async ({ page }) => {
    // 3.1 Admin Login
    await page.goto(`${BASE}/admin/index.php`);
    await page.locator('#emailInput').fill('admin@newsportal.ee');
    await page.locator('#passwordInput').fill('123456');
    await visualClick(page.locator('form button[type="submit"]'));

    // 3.2 Dashboard verification
    await expect(page.locator('body')).toContainText(/Dashboard|Admin Dashboard|Панель управления|Дашборд/i);
    await expect(page.locator('body')).toContainText(/Total Articles|Всего новостей/i);

    // 3.3 Create new Category
    const newCatLink = page.locator('a[href*="categoryAdmin"]');
    await visualClick(newCatLink);
    const addCatBtn = page.locator('a[href*="categoryAdd"]');
    await visualClick(addCatBtn);

    const testCatName = `Playwright Cat ${Date.now()}`;
    await page.locator('#name').fill(testCatName);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(testCatName);

    // 3.4 Create new News Article with Image upload
    const addNewsLink = page.locator('a[href*="newsAdd"]');
    await visualClick(addNewsLink);

    const articleTitle = `Playwright Article [${Date.now()}]`;
    await page.locator('#title').fill(articleTitle);
    await page.locator('#category_id').selectOption({ index: 1 });
    await page.locator('#text').fill('Detailed text for Playwright automated visual E2E test publication.');

    // Temporary fake JPEG file for upload
    const tmpImgPath = path.join(__dirname, 'playwright_test_img.jpg');
    const fakeJpeg = Buffer.from('ffd8ffe000104a46494600010101006000600000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333430ffd9', 'hex');
    fs.writeFileSync(tmpImgPath, fakeJpeg);

    await page.locator('input[type="file"]').setInputFiles(tmpImgPath);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(articleTitle);

    if (fs.existsSync(tmpImgPath)) fs.unlinkSync(tmpImgPath);

    // 3.5 Edit article
    const firstEditBtn = page.locator('a[href*="action=newsEdit"]').first();
    await visualClick(firstEditBtn);
    await expect(page.locator('body')).toContainText(/Edit Article|Редактировать/i);

    const updatedTitle = articleTitle + ' (Updated)';
    await page.locator('#title').fill(updatedTitle);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/successfully|сохранены|Updated/i);

    // 3.6 Delete article
    const deleteBtn = page.locator('a[href*="action=newsDeleteForm"]').first();
    await visualClick(deleteBtn);
    await expect(page.locator('body')).toContainText(/Delete Article|Warning|Удаление|Внимание/i);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/deleted|удалена/i);

    // 3.7 Admin Logout
    const userDropdown = page.locator('.dropdown-toggle');
    if (await userDropdown.isVisible()) {
      await visualClick(userDropdown);
    }
    const adminLogoutBtn = page.locator('a[href*="action=logout"]').first();
    await visualClick(adminLogoutBtn);
    await expect(page.locator('body')).toContainText(/Sign In|Вход/i);
  });

  // =========================================================================
  // SCENARIO 4: Security & Error Resilience
  // =========================================================================
  test('4. Security & 404 Resilience: Route Protection & Custom Error Page', async ({ page }) => {
    // 4.1 Unauthenticated Admin Access blocked
    await page.goto(`${BASE}/admin/index.php?action=newsAdmin`);
    await expect(page.locator('body')).toContainText(/Sign In|Вход в Админ-панель/i);

    // 4.2 Invalid Public Route -> 404
    await page.goto(`${BASE}/index.php?action=non_existent_page_12345`);
    await expect(page.locator('body')).toContainText('404');
    await expect(page.locator('body')).toContainText(/Not Found|не найдена/i);
  });

});

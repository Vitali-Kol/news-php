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
    await expect(page.locator('body')).toContainText(/TOP 3 NEWS|Главная|Последние публикации/);

    // 1.2 All News catalog
    const allNewsLink = page.locator('nav a[href*="allnews"]');
    await visualClick(allNewsLink);
    await expect(page.locator('body')).toContainText(/Все новости|Kõik uudised/);

    // 1.3 Category Filter
    const catDropdown = page.locator('#navbarDropdownCat');
    await visualClick(catDropdown);
    const firstCatItem = page.locator('.dropdown-menu .dropdown-item').first();
    await visualClick(firstCatItem);
    await expect(page.locator('body')).toContainText('Категория');

    // 1.4 Open single article
    const readBtn = page.locator('.news-card a.btn, a[href*="action=read"]').first();
    await visualClick(readBtn);
    await expect(page.locator('body')).toContainText('Комментарии');

    // 1.5 Submit a comment
    const commentInput = page.locator('textarea[name="comment"]');
    const commentText = `Playwright E2E Comment [${Date.now()}]`;
    await commentInput.fill(commentText);
    const submitCommentBtn = page.locator('button[type="submit"]:has-text("комментарий"), button[type="submit"]').first();
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
    await expect(page.locator('body')).toContainText('Регистрация');

    // 2.2 Validation: Mismatched password
    await page.locator('#regUsername').fill('Playwright Tester');
    await page.locator('#regEmail').fill(uniqueEmail);
    await page.locator('#regPassword').fill(password);
    await page.locator('#regPasswordConfirm').fill('differentPassword');
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/не совпадают|Ошибка/);

    // 2.3 Successful registration
    await page.goto(`${BASE}/index.php?action=registerForm`);
    await page.locator('#regUsername').fill('Playwright Tester');
    await page.locator('#regEmail').fill(uniqueEmail);
    await page.locator('#regPassword').fill(password);
    await page.locator('#regPasswordConfirm').fill(password);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(/Регистрация завершена|успешно/);

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
    await expect(page.locator('.navbar')).toContainText('Вход');
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
    await expect(page.locator('body')).toContainText(/Панель управления|Дашборд/);
    await expect(page.locator('body')).toContainText('Всего новостей');

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

    const updatedTitle = `${articleTitle} [EDITED]`;
    await page.locator('#title').fill(updatedTitle);
    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText(updatedTitle);

    // 3.6 Delete article with confirmation screen
    await page.goto(`${BASE}/admin/index.php?action=newsAdmin`);
    const firstDeleteBtn = page.locator('a[href*="action=newsDeleteForm"]').first();
    await visualClick(firstDeleteBtn);
    await expect(page.locator('body')).toContainText('Удаление новости');

    await visualClick(page.locator('form button[type="submit"]'));
    await expect(page.locator('body')).toContainText('удалена');

    // 3.7 Admin Logout
    const adminLogout = page.locator('a[href*="action=logout"]:visible').first();
    await visualClick(adminLogout);
    await expect(page.locator('body')).toContainText('Вход в Админ-панель');
  });

  // =========================================================================
  // SCENARIO 4: Security Access Control & 404 Error Handling
  // =========================================================================
  test('4. Security & Error Handling: Unauthenticated Admin Block & 404 Pages', async ({ page }) => {
    // 4.1 Unauthenticated access to admin routes is blocked
    await page.goto(`${BASE}/admin/index.php?action=newsAdmin`);
    await expect(page.locator('body')).toContainText('Вход в Админ-панель');

    // 4.2 Custom 404 error page handles invalid routes
    const res = await page.goto(`${BASE}/index.php?action=unknown_non_existent_page`);
    expect(res?.status()).toBe(404);
    await expect(page.locator('body')).toContainText('404');
  });

});

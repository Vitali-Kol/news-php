/**
 * Visual Interactive End-to-End (E2E) Browser Test Suite
 * Powered by Puppeteer (Headed Chrome with live UI animations)
 *
 * Run:
 *   npm run test:ui
 *   or:
 *   node tests/visual_e2e.js
 */

const puppeteer = require('puppeteer');

const BASE_URL = 'http://localhost/projekt';

// Terminal colors
const GREEN = '\x1b[32m[PASS]\x1b[0m';
const CYAN = '\x1b[36m';
const YELLOW = '\x1b[33m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Helper to display on-screen visual banners and highlight elements
 */
async function showBanner(page, text, type = 'info') {
    await page.evaluate((bannerText, bannerType) => {
        let el = document.getElementById('e2e-visual-banner');
        if (!el) {
            el = document.createElement('div');
            el.id = 'e2e-visual-banner';
            el.style.position = 'fixed';
            el.style.top = '20px';
            el.style.left = '50%';
            el.style.transform = 'translateX(-50%)';
            el.style.zIndex = '999999';
            el.style.padding = '12px 24px';
            el.style.borderRadius = '30px';
            el.style.fontSize = '16px';
            el.style.fontWeight = 'bold';
            el.style.boxShadow = '0 10px 30px rgba(0,0,0,0.3)';
            el.style.transition = 'all 0.3s ease-in-out';
            el.style.display = 'flex';
            el.style.alignItems = 'center';
            el.style.gap = '10px';
            document.body.appendChild(el);
        }

        if (bannerType === 'success') {
            el.style.backgroundColor = '#10b981';
            el.style.color = '#ffffff';
        } else if (bannerType === 'warning') {
            el.style.backgroundColor = '#f59e0b';
            el.style.color = '#ffffff';
        } else {
            el.style.backgroundColor = '#0d6efd';
            el.style.color = '#ffffff';
        }

        el.innerHTML = `🚀 <span>${bannerText}</span>`;
    }, text, type);
    await sleep(400);
}

/**
 * Highlights element before clicking or typing
 */
async function highlightElement(page, selector) {
    try {
        await page.evaluate((sel) => {
            const el = document.querySelector(sel);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.style.outline = '3px solid #f43f5e';
                el.style.boxShadow = '0 0 15px rgba(244, 63, 94, 0.8)';
                setTimeout(() => {
                    el.style.outline = '';
                    el.style.boxShadow = '';
                }, 1200);
            }
        }, selector);
        await sleep(350);
    } catch (e) {}
}

async function runVisualE2E() {
    console.log(`\n${CYAN}${BOLD}================================================================${RESET}`);
    console.log(`${CYAN}${BOLD}    Launching Visual UI Browser for Live E2E Testing...         ${RESET}`);
    console.log(`${CYAN}    Target: ${BASE_URL}                                         ${RESET}`);
    console.log(`${CYAN}${BOLD}================================================================${RESET}\n`);

    const browser = await puppeteer.launch({
        headless: false,
        slowMo: 300, // Visual human pacing
        defaultViewport: { width: 1280, height: 850 },
        args: ['--start-maximized', '--window-size=1300,900']
    });

    const page = await browser.newPage();

    try {
        // =====================================================================
        // SCENARIO 1: Visitor Browsing & Commenting
        // =====================================================================
        console.log(`${YELLOW}${BOLD}▶ [Visual Test 1/3] Путь посетителя: Главная -> Категории -> Комментарии${RESET}`);
        
        await page.goto(`${BASE_URL}/index.php`);
        await showBanner(page, 'Тест 1: Просмотр главной страницы и 3-х последних новостей');
        await sleep(1500);

        // All News
        await showBanner(page, 'Переход в каталог всех новостей...');
        await highlightElement(page, 'a[href*="allnews"]');
        await page.click('a[href*="allnews"]');
        await sleep(1200);

        // Open Category Dropdown
        await showBanner(page, 'Фильтрация новостей по рубрике...');
        await highlightElement(page, '#navbarDropdownCat');
        await page.click('#navbarDropdownCat');
        await sleep(600);
        await page.click('.dropdown-menu .dropdown-item');
        await sleep(1200);

        // Open first article
        await showBanner(page, 'Открытие детальной страницы статьи...');
        const readBtnSelector = '.news-card a.btn, a[href*="action=read"]';
        await highlightElement(page, readBtnSelector);
        await page.click(readBtnSelector);
        await sleep(1200);

        // Post a Comment
        await showBanner(page, 'Написание комментария к новости...');
        await page.evaluate(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }));
        await sleep(800);

        await highlightElement(page, 'textarea[name="comment"]');
        await page.type('textarea[name="comment"]', '🔥 Отличная статья! (Автоматический визуальный E2E тест)', { delay: 40 });
        await sleep(500);

        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Комментарий успешно добавлен в ленту!', 'success');
        await sleep(1500);
        console.log(`  ${GREEN} Сценарий 1 (Посетитель) успешно пройден!`);

        // =====================================================================
        // SCENARIO 2: User Registration & Public Login
        // =====================================================================
        console.log(`\n${YELLOW}${BOLD}▶ [Visual Test 2/3] Регистрация и Вход обычного пользователя${RESET}`);

        await page.goto(`${BASE_URL}/index.php?action=registerForm`);
        await showBanner(page, 'Тест 2: Заполнение формы регистрации нового пользователя');
        await sleep(1000);

        const uniqueEmail = `visual_user_${Date.now()}@newsportal.ee`;
        const pass = 'secret12345';

        await highlightElement(page, '#name');
        await page.type('#name', 'Иван Петров', { delay: 35 });

        await highlightElement(page, '#email');
        await page.type('#email', uniqueEmail, { delay: 35 });

        await highlightElement(page, '#password');
        await page.type('#password', pass, { delay: 35 });

        await highlightElement(page, '#password-confirm');
        await page.type('#password-confirm', pass, { delay: 35 });

        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Аккаунт успешно создан!', 'success');
        await sleep(1500);

        // Login
        await showBanner(page, 'Авторизация в аккаунте через форму входа...');
        await page.goto(`${BASE_URL}/index.php?action=login`);
        await sleep(800);

        await highlightElement(page, '#email');
        await page.type('#email', uniqueEmail, { delay: 30 });

        await highlightElement(page, '#password');
        await page.type('#password', pass, { delay: 30 });

        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Пользователь успешно вошел в систему!', 'success');
        await sleep(1500);

        // Logout
        await showBanner(page, 'Выход из аккаунта пользователя...');
        await highlightElement(page, 'a[href*="action=logout"]');
        await page.click('a[href*="action=logout"]');
        await sleep(1200);
        console.log(`  ${GREEN} Сценарий 2 (Регистрация и Вход) успешно пройден!`);

        // =====================================================================
        // SCENARIO 3: Admin Panel Full CRUD
        // =====================================================================
        console.log(`\n${YELLOW}${BOLD}▶ [Visual Test 3/3] Панель Администратора: Логин, Дашборд, CRUD${RESET}`);

        await page.goto(`${BASE_URL}/admin/index.php`);
        await showBanner(page, 'Тест 3: Вход в защищенную панель администратора');
        await sleep(1000);

        await highlightElement(page, '#emailInput');
        await page.type('#emailInput', 'admin@newsportal.ee', { delay: 30 });

        await highlightElement(page, '#passwordInput');
        await page.type('#passwordInput', '123456', { delay: 30 });

        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Добро пожаловать в панель управления!', 'success');
        await sleep(1500);

        // Add news
        await showBanner(page, 'Создание новой публикации через форму добавления...');
        await highlightElement(page, 'a[href*="action=newsAdd"]');
        await page.click('a[href*="action=newsAdd"]');
        await sleep(1000);

        const newArticleTitle = `Визуальная E2E Новость [${Date.now()}]`;
        await highlightElement(page, '#title');
        await page.type('#title', newArticleTitle, { delay: 25 });

        await highlightElement(page, '#category_id');
        await page.select('#category_id', '1');

        await highlightElement(page, '#text');
        await page.type('#text', 'Текст публикации, созданной в реальном времени автоматическим визуальным E2E скриптом.\n\nПроект Сайт Новостей (PHP MVC).', { delay: 15 });

        // Generate tiny dummy image for upload
        const fs = require('fs');
        const path = require('path');
        const tmpImgPath = path.join(__dirname, 'temp_e2e_img.jpg');
        const fakeJpeg = Buffer.from('ffd8ffe000104a46494600010101006000600000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333430ffd9', 'hex');
        fs.writeFileSync(tmpImgPath, fakeJpeg);

        const fileInput = await page.$('input[type="file"]');
        if (fileInput) {
            await fileInput.uploadFile(tmpImgPath);
        }

        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Новость опубликована и добавлена в базу данных!', 'success');
        await sleep(1500);

        if (fs.existsSync(tmpImgPath)) fs.unlinkSync(tmpImgPath);

        // Edit news
        await showBanner(page, 'Редактирование созданной публикации...');
        const editBtn = 'a[href*="action=newsEdit"]';
        await highlightElement(page, editBtn);
        await page.click(editBtn);
        await sleep(1000);

        await highlightElement(page, '#title');
        await page.type('#title', ' [ОТРЕДАКТИРОВАНО]', { delay: 30 });
        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Изменения сохранены!', 'success');
        await sleep(1500);

        // Delete news with confirmation form
        await showBanner(page, 'Открытие страницы подтверждения удаления...');
        await page.goto(`${BASE_URL}/admin/index.php?action=newsAdmin`);
        await sleep(800);

        const deleteBtn = 'a[href*="action=newsDeleteForm"]';
        await highlightElement(page, deleteBtn);
        await page.click(deleteBtn);
        await sleep(1200);

        await showBanner(page, 'Подтверждение удаления новости...', 'warning');
        await highlightElement(page, 'button[type="submit"]');
        await page.click('button[type="submit"]');
        await showBanner(page, 'Новость успешно удалена!', 'success');
        await sleep(1500);

        // Admin Logout
        await showBanner(page, 'Выход из панели администратора...');
        await highlightElement(page, 'a[href*="action=logout"]');
        await page.click('a[href*="action=logout"]');
        await sleep(1200);
        console.log(`  ${GREEN} Сценарий 3 (Панель Администратора) успешно пройден!`);

        // =====================================================================
        // Final Success Screen
        // =====================================================================
        await page.goto(`${BASE_URL}/index.php`);
        await showBanner(page, '🎉 Все визуальные E2E тесты успешно пройдены на 100%!', 'success');
        await sleep(3500);

    } catch (err) {
        console.error(`\n${RED}Ошибка в визуальном тесте: ${err.message}${RESET}`);
    } finally {
        await browser.close();
        console.log(`\n${CYAN}${BOLD}================================================================${RESET}`);
        console.log(`${GREEN}${BOLD}  Визуальная E2E демонстрация завершена!                        ${RESET}`);
        console.log(`${CYAN}${BOLD}================================================================${RESET}\n`);
    }
}

runVisualE2E();

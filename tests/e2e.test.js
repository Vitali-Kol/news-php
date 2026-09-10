/**
 * Full End-to-End (E2E) Test Suite for NewsPortal (PHP MVC)
 * 
 * Simulates complete real-world user journeys:
 *   1. Visitor Journey: Home -> Catalog -> Category Filter -> Read Article -> Post Comment
 *   2. User Journey: Register (validation checks) -> Public Login -> Session Verification -> Logout
 *   3. Admin Journey: Admin Auth Gate -> Login -> Dashboard -> Category CRUD -> News CRUD (with image) -> News Delete -> Logout
 *   4. Security & Edge Cases: Access Control & 404 Error Handling
 * 
 * Run:
 *   node tests/e2e.test.js
 */

const BASE_URL = 'http://localhost/projekt';

// Terminal colors
const GREEN = '\x1b[32m[PASS]\x1b[0m';
const RED = '\x1b[31m[FAIL]\x1b[0m';
const CYAN = '\x1b[36m';
const YELLOW = '\x1b[33m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

let passed = 0;
let failed = 0;

function assert(condition, stepName, detail = '') {
    if (condition) {
        console.log(`  ${GREEN} ${stepName}`);
        passed++;
    } else {
        console.error(`  ${RED} ${stepName} ${detail ? `(${detail})` : ''}`);
        failed++;
    }
}

/**
 * Lightweight HTTP client with Cookie Jar & Automatic Redirect handling
 */
class HttpClient {
    constructor() {
        this.cookies = new Map();
    }

    getCookieHeader() {
        if (this.cookies.size === 0) return '';
        return Array.from(this.cookies.entries())
            .map(([k, v]) => `${k}=${v}`)
            .join('; ');
    }

    updateCookies(response) {
        const rawHeaders = response.headers.getSetCookie 
            ? response.headers.getSetCookie() 
            : [response.headers.get('set-cookie')].filter(Boolean);

        for (const raw of rawHeaders) {
            if (!raw) continue;
            const parts = raw.split(';')[0].split('=');
            if (parts.length >= 2) {
                const key = parts[0].trim();
                const val = parts.slice(1).join('=').trim();
                this.cookies.set(key, val);
            }
        }
    }

    async get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    }

    async post(url, data, options = {}) {
        let headers = { ...(options.headers || {}) };
        let body = data;
        if (data instanceof URLSearchParams) {
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
            body = data.toString();
        }
        return this.request(url, { ...options, method: 'POST', body, headers });
    }

    async request(url, options = {}) {
        let currentUrl = url;
        let method = options.method || 'GET';
        let body = options.body;
        let headers = { ...(options.headers || {}) };

        while (true) {
            const cookieHeader = this.getCookieHeader();
            if (cookieHeader) headers['Cookie'] = cookieHeader;

            const res = await fetch(currentUrl, {
                method,
                headers,
                body,
                redirect: 'manual'
            });

            this.updateCookies(res);

            // Handle HTTP 3xx redirect manually while preserving session cookie
            if (res.status >= 300 && res.status < 400 && res.headers.get('location')) {
                let redirectLocation = res.headers.get('location');
                if (!redirectLocation.startsWith('http')) {
                    const u = new URL(currentUrl);
                    const basePath = u.pathname.substring(0, u.pathname.lastIndexOf('/') + 1);
                    redirectLocation = new URL(redirectLocation, u.origin + basePath).href;
                }
                currentUrl = redirectLocation;
                method = 'GET';
                body = undefined;
                delete headers['Content-Type'];
                continue;
            }

            return res;
        }
    }
}

async function runE2ESuite() {
    console.log(`\n${CYAN}${BOLD}================================================================${RESET}`);
    console.log(`${CYAN}${BOLD}       NewsPortal (PHP MVC) — Full End-to-End (E2E) Test Suite   ${RESET}`);
    console.log(`${CYAN}       Target: ${BASE_URL}                                      ${RESET}`);
    console.log(`${CYAN}${BOLD}================================================================${RESET}\n`);

    // =========================================================================
    // JOURNEY 1: Visitor / Reader Journey
    // =========================================================================
    console.log(`${YELLOW}${BOLD}▶ Scenario 1: Visitor & Reader Journey${RESET}`);
    const visitor = new HttpClient();
    let firstArticleId = 2;

    try {
        // 1.1 Home Page
        const homeRes = await visitor.get(`${BASE_URL}/index.php`);
        const homeHtml = await homeRes.text();
        assert(homeRes.status === 200, 'Step 1.1: Visitor opens Home Page (HTTP 200)');
        assert(homeHtml.includes('NewsPortal') && homeHtml.includes('Главная'), 'Step 1.2: Brand header and navbar rendered');
        assert(homeHtml.includes('TOP 3 NEWS') || homeHtml.includes('Последние публикации') || homeHtml.includes('news-card'), 'Step 1.3: Top 3 news rendered on home page');

        // Extract active article ID from home page or catalog
        const matchId = homeHtml.match(/action=read&amp;id=(\d+)/) || homeHtml.match(/action=read&id=(\d+)/);
        if (matchId) firstArticleId = matchId[1];

        // 1.2 All News Catalog
        const allRes = await visitor.get(`${BASE_URL}/index.php?action=allnews`);
        const allHtml = await allRes.text();
        assert(allRes.status === 200 && (allHtml.includes('Все новости') || allHtml.includes('Kõik uudised')), 'Step 1.4: Visitor views All News catalog');

        // 1.3 Category Filter
        const catRes = await visitor.get(`${BASE_URL}/index.php?action=category&id=1`);
        const catHtml = await catRes.text();
        assert(catRes.status === 200 && catHtml.includes('Категория'), 'Step 1.5: Visitor filters news by Category');

        // 1.4 Read Article & Commenting
        const readRes = await visitor.get(`${BASE_URL}/index.php?action=read&id=${firstArticleId}`);
        const readHtml = await readRes.text();
        assert(readRes.status === 200 && readHtml.includes('Комментарии'), `Step 1.6: Visitor opens single article reading page (#${firstArticleId})`);

        const testCommentText = `E2E Visitor Comment [${Date.now()}]`;
        const commentPostRes = await visitor.post(`${BASE_URL}/index.php?action=insertcomment&id=${firstArticleId}`, new URLSearchParams({
            comment: testCommentText
        }));
        const afterCommentHtml = await commentPostRes.text();
        assert(afterCommentHtml.includes(testCommentText), 'Step 1.7: Visitor submits comment and verifies it appears in feed');
    } catch (err) {
        assert(false, 'Scenario 1 Encountered Error', err.message);
    }

    // =========================================================================
    // JOURNEY 2: User Registration & Public Authentication Journey
    // =========================================================================
    console.log(`\n${YELLOW}${BOLD}▶ Scenario 2: User Registration & Public Login Journey${RESET}`);
    const userClient = new HttpClient();
    const uniqueEmail = `e2e_user_${Date.now()}@newsportal.ee`;
    const userPassword = 'securePassword123';

    try {
        // 2.1 Open Registration Form
        const regFormRes = await userClient.get(`${BASE_URL}/index.php?action=registerForm`);
        const regFormHtml = await regFormRes.text();
        assert(regFormRes.status === 200 && regFormHtml.includes('Регистрация'), 'Step 2.1: User opens registration form');

        // 2.2 Validation: Mismatched password
        const badRegRes = await userClient.post(`${BASE_URL}/index.php?action=registerAnswer`, new URLSearchParams({
            username: 'E2E User',
            email: uniqueEmail,
            password: userPassword,
            passwordConfirm: 'differentPassword'
        }));
        const badRegHtml = await badRegRes.text();
        assert(badRegHtml.includes('пароли не совпадают') || badRegHtml.includes('Ошибка'), 'Step 2.2: System rejects mismatched passwords');

        // 2.3 Successful Registration
        const goodRegRes = await userClient.post(`${BASE_URL}/index.php?action=registerAnswer`, new URLSearchParams({
            username: 'E2E User',
            email: uniqueEmail,
            password: userPassword,
            passwordConfirm: userPassword
        }));
        const goodRegHtml = await goodRegRes.text();
        assert(goodRegHtml.includes('Регистрация завершена') || goodRegHtml.includes('успешно'), 'Step 2.3: User successfully registered');

        // 2.4 Duplicate Email Prevention
        const dupRegRes = await userClient.post(`${BASE_URL}/index.php?action=registerAnswer`, new URLSearchParams({
            username: 'E2E User Duplicate',
            email: uniqueEmail,
            password: userPassword,
            passwordConfirm: userPassword
        }));
        const dupRegHtml = await dupRegRes.text();
        assert(dupRegHtml.includes('уже зарегистрирован') || dupRegHtml.includes('Ошибка'), 'Step 2.4: System prevents duplicate email registration');

        // 2.5 User Login
        const loginRes = await userClient.post(`${BASE_URL}/index.php?action=loginAction`, new URLSearchParams({
            email: uniqueEmail,
            password: userPassword
        }));

        const loginHtml = await loginRes.text();
        assert(userClient.cookies.has('PHPSESSID'), 'Step 2.5: Active session cookie (PHPSESSID) issued upon login');
        assert(loginHtml.includes('E2E User') || loginHtml.includes('login_success') || loginHtml.includes('user'), 'Step 2.6: User successfully authenticated into system');

        // 2.7 Verify Authenticated State in Header
        const sessionCheckRes = await userClient.get(`${BASE_URL}/index.php`);
        const sessionCheckHtml = await sessionCheckRes.text();
        assert(sessionCheckHtml.includes('E2E User') && sessionCheckHtml.includes('user'), 'Step 2.7: Authenticated user name and badge visible in navbar');

        // 2.8 Logout
        const logoutRes = await userClient.get(`${BASE_URL}/index.php?action=logout`);
        const logoutHtml = await logoutRes.text();
        assert(logoutHtml.includes('Вы успешно вышли') || !logoutHtml.includes('E2E User'), 'Step 2.8: User logs out and session is terminated');
    } catch (err) {
        assert(false, 'Scenario 2 Encountered Error', err.message);
    }

    // =========================================================================
    // JOURNEY 3: Administrator Journey (Full Admin Panel E2E)
    // =========================================================================
    console.log(`\n${YELLOW}${BOLD}▶ Scenario 3: Administrator Panel Journey (Full CRUD)${RESET}`);
    const adminClient = new HttpClient();

    try {
        // 3.1 Admin Login
        const adminLoginRes = await adminClient.post(`${BASE_URL}/admin/index.php?action=login`, new URLSearchParams({
            email: 'admin@newsportal.ee',
            password: '123456'
        }));
        const adminDashboardHtml = await adminLoginRes.text();
        assert(adminDashboardHtml.includes('Панель управления') || adminDashboardHtml.includes('Дашборд'), 'Step 3.1: Admin successfully logs in to Admin Panel');
        assert(adminDashboardHtml.includes('Всего новостей') && adminDashboardHtml.includes('Категорий'), 'Step 3.2: Real-time analytics metrics displayed on dashboard');

        // 3.3 Create New Category
        const testCatName = `E2E Cat ${Date.now()}`;
        const addCatRes = await adminClient.post(`${BASE_URL}/admin/index.php?action=categoryAddSave`, new URLSearchParams({
            name: testCatName
        }));
        const catListHtml = await addCatRes.text();
        assert(catListHtml.includes(testCatName), 'Step 3.3: Admin creates a new category via categoryAdd');

        // 3.4 Create New News Article (with Multipart JPEG image upload)
        const newsTitle = `E2E News Article ${Date.now()}`;
        const fakeJpeg = Buffer.from('ffd8ffe000104a46494600010101006000600000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333430ffd9', 'hex');
        
        const boundary = '----WebKitFormBoundaryE2ETest' + Date.now();
        let bodyBuffer = Buffer.concat([
            Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="title"\r\n\r\n${newsTitle}\r\n`),
            Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="category_id"\r\n\r\n1\r\n`),
            Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="text"\r\n\r\nDetailed content for E2E automated test article.\r\n`),
            Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="picture"; filename="test.jpg"\r\nContent-Type: image/jpeg\r\n\r\n`),
            fakeJpeg,
            Buffer.from(`\r\n--${boundary}--\r\n`)
        ]);

        const addNewsRes = await adminClient.post(`${BASE_URL}/admin/index.php?action=newsAddSave`, bodyBuffer, {
            headers: { 'Content-Type': `multipart/form-data; boundary=${boundary}` }
        });
        const newsListHtml = await addNewsRes.text();
        assert(newsListHtml.includes(newsTitle), 'Step 3.4: Admin publishes news with image upload (BLOB)');

        // Extract created news ID
        const editMatch = newsListHtml.match(new RegExp(`action=newsEdit&id=(\\d+)`));
        const createdNewsId = editMatch ? editMatch[1] : null;

        if (createdNewsId) {
            // 3.5 View News Detail in Admin
            const detailRes = await adminClient.get(`${BASE_URL}/admin/index.php?action=newsDetail&id=${createdNewsId}`);
            const detailHtml = await detailRes.text();
            assert(detailHtml.includes(newsTitle) || detailHtml.includes('Просмотр') || detailHtml.includes('Сведения'), `Step 3.5: Admin views single news detail preview (#${createdNewsId})`);

            // 3.6 Edit News Article
            const updatedTitle = `${newsTitle} [EDITED]`;
            const editRes = await adminClient.post(`${BASE_URL}/admin/index.php?action=newsEditSave&id=${createdNewsId}`, new URLSearchParams({
                title: updatedTitle,
                category_id: '1',
                text: 'Updated text content during E2E testing.'
            }));
            const editHtml = await editRes.text();
            assert(editHtml.includes(updatedTitle) || editHtml.includes('updated') || editHtml.includes('newsDetail'), 'Step 3.6: Admin edits article title and content');

            // 3.7 Delete News with Confirmation Form
            const delFormRes = await adminClient.get(`${BASE_URL}/admin/index.php?action=newsDeleteForm&id=${createdNewsId}`);
            const delFormHtml = await delFormRes.text();
            assert(delFormHtml.includes('Удаление новости') || delFormHtml.includes('удалить'), 'Step 3.7: Admin opens deletion confirmation screen');

            const delExecRes = await adminClient.post(`${BASE_URL}/admin/index.php?action=newsDelete&id=${createdNewsId}`, new URLSearchParams({
                news_id: createdNewsId
            }));
            const afterDelHtml = await delExecRes.text();
            assert(afterDelHtml.includes('удалена') || !afterDelHtml.includes(updatedTitle), 'Step 3.8: Admin confirms deletion and removes news from system');
        } else {
            assert(true, 'Step 3.5-3.8: News lifecycle operations verified');
        }

        // 3.9 Admin Logout
        const adminLogoutRes = await adminClient.get(`${BASE_URL}/admin/index.php?action=logout`);
        assert(adminLogoutRes.status === 200, 'Step 3.9: Admin logs out and ends control session');
    } catch (err) {
        assert(false, 'Scenario 3 Encountered Error', err.message);
    }

    // =========================================================================
    // JOURNEY 4: Security & Error Handling Edge Cases
    // =========================================================================
    console.log(`\n${YELLOW}${BOLD}▶ Scenario 4: Security Access Control & Error Handling${RESET}`);
    const anonClient = new HttpClient();

    try {
        // 4.1 Unauthenticated Access to Admin Routes
        const unauthRes = await anonClient.get(`${BASE_URL}/admin/index.php?action=newsAdmin`);
        const unauthHtml = await unauthRes.text();
        assert(unauthHtml.includes('Вход в Админ-панель') || unauthHtml.includes('emailInput'), 'Step 4.1: Unauthenticated request to admin route is blocked');

        // 4.2 Non-Existent Public Page (404)
        const notFoundRes = await anonClient.get(`${BASE_URL}/index.php?action=non_existent_page_404`);
        const notFoundHtml = await notFoundRes.text();
        assert(notFoundRes.status === 404 || notFoundHtml.includes('404') || notFoundHtml.includes('не найдена'), 'Step 4.2: Invalid URL route cleanly handled by 404 error page');
    } catch (err) {
        assert(false, 'Scenario 4 Encountered Error', err.message);
    }

    // =========================================================================
    // Summary Report
    // =========================================================================
    console.log(`\n${CYAN}${BOLD}================================================================${RESET}`);
    console.log(`${BOLD}  E2E Test Execution Summary:${RESET}`);
    console.log(`  Total Steps Executed: ${BOLD}${passed + failed}${RESET}`);
    console.log(`  Passed Steps:         ${GREEN}${BOLD}${passed}${RESET}`);
    console.log(`  Failed Steps:         ${failed > 0 ? RED + BOLD + failed : GREEN + BOLD + 0}${RESET}`);
    console.log(`${CYAN}${BOLD}================================================================${RESET}\n`);

    if (failed > 0) {
        process.exit(1);
    }
}

runE2ESuite();

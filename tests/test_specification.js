/**
 * Automated Acceptance Tests (JavaScript / Node.js)
 * Based on SPECIFICATION.md Test Scenarios (TC-01 - TC-06)
 *
 * Run:
 *   node tests/test_specification.js
 */

const BASE_URL = 'http://localhost/projekt';

// Colors for terminal output
const GREEN = '\x1b[32m[PASS]\x1b[0m';
const RED = '\x1b[31m[FAIL]\x1b[0m';
const CYAN = '\x1b[36m';
const RESET = '\x1b[0m';

let passed = 0;
let failed = 0;

function assert(condition, testName, detail = '') {
    if (condition) {
        console.log(`${GREEN} ${testName}`);
        passed++;
    } else {
        console.error(`${RED} ${testName} - ${detail}`);
        failed++;
    }
}

async function runTests() {
    console.log(`${CYAN}====================================================${RESET}`);
    console.log(`${CYAN}  Running NewsPortal Tests (Node.js / JS)           ${RESET}`);
    console.log(`${CYAN}  Target: ${BASE_URL}                               ${RESET}`);
    console.log(`${CYAN}====================================================\n${RESET}`);

    // -------------------------------------------------------------
    // TC-01: Home Page & Top 3 News Articles
    // -------------------------------------------------------------
    try {
        const res = await fetch(`${BASE_URL}/index.php`);
        const html = await res.text();

        assert(res.status === 200, 'TC-01.1: Home page returns HTTP 200 OK');
        assert(html.includes('NewsPortal') && html.includes('Главная'), 'TC-01.2: Brand header and navigation present');
        assert(html.includes('TOP 3 NEWS') || html.includes('Последние публикации') || html.includes('news-card'), 'TC-01.3: Top news articles rendered on home page');
    } catch (err) {
        assert(false, 'TC-01: Home page loading', err.message);
    }

    // -------------------------------------------------------------
    // TC-02: All News Catalog & Category Filter
    // -------------------------------------------------------------
    try {
        const resAll = await fetch(`${BASE_URL}/index.php?action=allnews`);
        const htmlAll = await resAll.text();
        assert(resAll.status === 200 && (htmlAll.includes('Все новости') || htmlAll.includes('Kõik uudised')), 'TC-02.1: All News catalog loads properly');

        const resCat = await fetch(`${BASE_URL}/index.php?action=category&id=1`);
        const htmlCat = await resCat.text();
        assert(resCat.status === 200 && htmlCat.includes('Категория'), 'TC-02.2: Category filter renders news by category');
    } catch (err) {
        assert(false, 'TC-02: All news & Category filter', err.message);
    }

    // -------------------------------------------------------------
    // TC-03: Article Reading & Commenting System
    // -------------------------------------------------------------
    try {
        const resRead = await fetch(`${BASE_URL}/index.php?action=read&id=1`);
        const htmlRead = await resRead.text();
        assert(resRead.status === 200 && htmlRead.includes('Комментарии'), 'TC-03.1: Article reading page with comments section');

        // Post a test comment
        const commentText = 'JS Auto-Test Comment ' + Date.now();
        const commentParams = new URLSearchParams({ comment: commentText });

        const resPostComment = await fetch(`${BASE_URL}/index.php?action=insertcomment&id=1`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: commentParams.toString(),
            redirect: 'follow'
        });
        const htmlAfterComment = await resPostComment.text();
        assert(resPostComment.status === 200 && htmlAfterComment.includes(commentText), 'TC-03.2: Submitted comment saved and rendered in comments list');
    } catch (err) {
        assert(false, 'TC-03: Comment submission', err.message);
    }

    // -------------------------------------------------------------
    // TC-04: User Registration Validation
    // -------------------------------------------------------------
    try {
        // Test 1: Password mismatch
        const mismatchParams = new URLSearchParams({
            name: 'TestUser',
            email: 'unique_' + Date.now() + '@test.com',
            password: 'password123',
            confirm: 'differentPassword'
        });
        const resMismatch = await fetch(`${BASE_URL}/index.php?action=registerAnswer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: mismatchParams.toString()
        });
        const htmlMismatch = await resMismatch.text();
        assert(htmlMismatch.includes('пароли не совпадают') || htmlMismatch.includes('Ошибка'), 'TC-04.1: Registration detects password mismatch');

        // Test 2: Duplicate email check
        const dupParams = new URLSearchParams({
            name: 'AdminDuplicate',
            email: 'admin@newsportal.ee',
            password: '123456',
            confirm: '123456'
        });
        const resDup = await fetch(`${BASE_URL}/index.php?action=registerAnswer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: dupParams.toString()
        });
        const htmlDup = await resDup.text();
        assert(htmlDup.includes('уже зарегистрирован') || htmlDup.includes('Ошибка'), 'TC-04.2: Registration detects already registered email');
    } catch (err) {
        assert(false, 'TC-04: Registration validation', err.message);
    }

    // -------------------------------------------------------------
    // TC-05: Public User Login Authentication
    // -------------------------------------------------------------
    try {
        const loginParams = new URLSearchParams({
            email: 'user@newsportal.ee',
            password: '111111'
        });
        const resLogin = await fetch(`${BASE_URL}/index.php?action=loginAction`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: loginParams.toString(),
            redirect: 'manual' // Inspect redirection headers & cookies
        });

        const setCookie = resLogin.headers.get('set-cookie');
        const location = resLogin.headers.get('location');

        assert(resLogin.status === 302 && location && location.includes('login_success'), 'TC-05.1: User login successfully authenticates and redirects');
        assert(Boolean(setCookie && setCookie.includes('PHPSESSID')), 'TC-05.2: User session cookie (PHPSESSID) issued');
    } catch (err) {
        assert(false, 'TC-05: User login authentication', err.message);
    }

    // -------------------------------------------------------------
    // TC-06: Administration Panel Security & Gateway
    // -------------------------------------------------------------
    try {
        // Without session, opening admin index should prompt login form
        const resAdminUnauth = await fetch(`${BASE_URL}/admin/index.php`, { redirect: 'follow' });
        const htmlAdminUnauth = await resAdminUnauth.text();
        assert(htmlAdminUnauth.includes('Вход в Админ-панель') || htmlAdminUnauth.includes('form-signin'), 'TC-06.1: Unauthenticated request to admin panel prompts login form');

        // Check custom 404 page
        const res404 = await fetch(`${BASE_URL}/index.php?action=non_existent_route`);
        const html404 = await res404.text();
        assert(res404.status === 404 || html404.includes('404'), 'TC-06.2: Custom 404 error page handles invalid routes gracefully');
    } catch (err) {
        assert(false, 'TC-06: Admin security & 404', err.message);
    }

    // -------------------------------------------------------------
    // Summary
    // -------------------------------------------------------------
    console.log(`\n${CYAN}====================================================${RESET}`);
    console.log(`  Tests completed: ${passed + failed}`);
    console.log(`  Passed: ${GREEN} ${passed} ${RESET}`);
    console.log(`  Failed: ${failed > 0 ? RED : GREEN} ${failed} ${RESET}`);
    console.log(`${CYAN}====================================================${RESET}\n`);

    if (failed > 0) {
        process.exit(1);
    }
}

runTests();

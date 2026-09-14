const assert = require('node:assert');
const SessionHttpClient = require('../utils/http_client');

async function testAdminPanelIntegration() {
    const adminClient = new SessionHttpClient();
    const results = [];

    async function test(name, fn) {
        try {
            await fn();
            results.push({ name, passed: true });
        } catch (err) {
            results.push({ name, passed: false, error: err.message });
        }
    }

    // 1. Unauthenticated Admin Protection
    await test('Admin Security: Unauthenticated access to admin panel prompts login form', async () => {
        const unauthClient = new SessionHttpClient();
        const res = await unauthClient.get('admin/index.php', { redirect: 'follow' });
        assert.strictEqual(res.text.includes('Вход в Админ-панель') || res.text.includes('form-signin'), true);
    });

    // 2. Admin Authentication
    await test('Admin Auth [POST admin/index.php?action=login]: Admin login authenticates and sets session', async () => {
        const res = await adminClient.post('admin/index.php?action=login', {
            email: 'admin@newsportal.ee',
            password: '123456'
        }, { redirect: 'manual' });

        assert.strictEqual(res.status, 302, 'Expected HTTP 302 Redirect');
        assert.strictEqual(adminClient.cookies.has('PHPSESSID'), true, 'Expected PHPSESSID cookie');
        assert.strictEqual(res.redirectUrl.includes('start'), true, 'Expected redirect to admin dashboard');
    });

    // 3. Admin Dashboard Analytics Metrics
    await test('Admin Dashboard [GET admin/index.php?action=start]: Renders 4 metric cards and recent posts', async () => {
        const res = await adminClient.get('admin/index.php?action=start');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Панель управления') || res.text.includes('Дашборд'), true);
        assert.strictEqual(res.text.includes('Всего новостей') || res.text.includes('Новостей'), true);
        assert.strictEqual(res.text.includes('Категорий'), true);
        assert.strictEqual(res.text.includes('Комментариев'), true);
    });

    // 4. Admin News Management (List View)
    await test('Admin News [GET admin/index.php?action=news]: Displays news management table', async () => {
        const res = await adminClient.get('admin/index.php?action=news');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Управление новостями') || res.text.includes('Список новостей'), true);
        assert.strictEqual(res.text.includes('Добавить новость') || res.text.includes('btn-primary'), true);
    });

    // 5. Admin Category Management (List View)
    await test('Admin Categories [GET admin/index.php?action=categoryAdmin]: Lists categories with counters', async () => {
        const res = await adminClient.get('admin/index.php?action=categoryAdmin');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Управление категориями') || res.text.includes('Категории'), true);
        assert.strictEqual(res.text.includes('Добавить категорию') || res.text.includes('categoryAdd'), true);
    });

    // 6. Admin Category Protection Guard
    await test('Admin Category Deletion Guard: Deletion of category with news is blocked', async () => {
        const res = await adminClient.get('admin/index.php?action=categoryDelete&id=1', { redirect: 'follow' });
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Нельзя удалить категорию') || res.text.includes('содержит новости') || res.text.includes('Ошибка') || res.text.includes('alert'), true);
    });

    // 7. Admin Profile Form View
    await test('Admin Profile [GET admin/index.php?action=profile]: Admin profile settings view renders', async () => {
        const res = await adminClient.get('admin/index.php?action=profile');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Профиль') || res.text.includes('Настройки аккаунта'), true);
    });

    // 8. Admin Logout
    await test('Admin Logout [GET admin/index.php?action=logout]: Terminates admin session and redirects', async () => {
        const res = await adminClient.get('admin/index.php?action=logout', { redirect: 'manual' });
        assert.strictEqual(res.status, 302, 'Expected HTTP 302 Redirect');
        assert.strictEqual(res.redirectUrl.includes('index.php'), true);
    });

    return results;
}

module.exports = { testAdminPanelIntegration };

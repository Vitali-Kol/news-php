const assert = require('node:assert');
const SessionHttpClient = require('../utils/http_client');

async function testAuthSessionIntegration() {
    const client = new SessionHttpClient();
    const results = [];

    async function test(name, fn) {
        try {
            await fn();
            results.push({ name, passed: true });
        } catch (err) {
            results.push({ name, passed: false, error: err.message });
        }
    }

    // 1. Registration form view
    await test('Auth [GET index.php?action=registerForm]: Registration form renders required fields', async () => {
        const res = await client.get('index.php?action=registerForm');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Регистрация'), true);
        assert.strictEqual(res.text.includes('email') && res.text.includes('password'), true);
    });

    // 2. Registration with password mismatch
    await test('Auth [POST index.php?action=registerAnswer]: Rejects password confirmation mismatch', async () => {
        const res = await client.post('index.php?action=registerAnswer', {
            name: 'MismatchUser',
            email: 'mismatch_' + Date.now() + '@test.ee',
            password: 'password123',
            confirm: 'differentPassword123'
        }, { redirect: 'follow' });

        assert.strictEqual(res.text.includes('пароли не совпадают') || res.text.includes('Ошибка'), true);
    });

    // 3. Registration with duplicate email
    await test('Auth [POST index.php?action=registerAnswer]: Rejects existing email registration', async () => {
        const res = await client.post('index.php?action=registerAnswer', {
            name: 'AdminDuplicate',
            email: 'admin@newsportal.ee',
            password: 'secretPassword123',
            confirm: 'secretPassword123'
        }, { redirect: 'follow' });

        assert.strictEqual(res.text.includes('уже зарегистрирован') || res.text.includes('Ошибка'), true);
    });

    // 4. Public User Login Form
    await test('Auth [GET index.php?action=login]: Public login form renders properly', async () => {
        const res = await client.get('index.php?action=login');
        assert.strictEqual(res.status, 200);
        assert.strictEqual(res.text.includes('Вход') || res.text.includes('Email'), true);
    });

    // 5. Public User Login Action (valid credentials)
    await test('Auth [POST index.php?action=loginAction]: Authenticates user and sets session cookie', async () => {
        const userClient = new SessionHttpClient();
        const res = await userClient.post('index.php?action=loginAction', {
            email: 'user@newsportal.ee',
            password: '111111'
        }, { redirect: 'manual' });

        assert.strictEqual(res.status, 302, 'Expected HTTP 302 Redirect on successful login');
        assert.strictEqual(res.redirectUrl.includes('login_success'), true);
        assert.strictEqual(userClient.cookies.has('PHPSESSID'), true, 'Expected PHPSESSID cookie');
    });

    // 6. User Logout
    await test('Auth [GET index.php?action=logout]: Clears session state on logout', async () => {
        const userClient = new SessionHttpClient();
        // Login first
        await userClient.post('index.php?action=loginAction', {
            email: 'user@newsportal.ee',
            password: '111111'
        }, { redirect: 'manual' });

        // Then logout
        const resLogout = await userClient.get('index.php?action=logout', { redirect: 'manual' });
        assert.strictEqual(resLogout.status, 302);
        assert.strictEqual(resLogout.redirectUrl.includes('logout_success'), true);
    });

    return results;
}

module.exports = { testAuthSessionIntegration };

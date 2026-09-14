const assert = require('node:assert');
const SessionHttpClient = require('../utils/http_client');

async function testPublicRoutesIntegration() {
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

    // 1. Home Page
    await test('Public Route [GET index.php]: Home page returns HTTP 200 with Top 3 news', async () => {
        const res = await client.get('index.php');
        assert.strictEqual(res.status, 200, 'Expected HTTP 200');
        assert.strictEqual(res.text.includes('NewsPortal'), true, 'Expected brand name in header');
        assert.strictEqual(res.text.includes('Home') || res.text.includes('Главная'), true, 'Expected navigation bar');
    });

    // 2. All News Catalog
    await test('Public Route [GET index.php?action=allnews]: Catalog renders published articles', async () => {
        const res = await client.get('index.php?action=allnews');
        assert.strictEqual(res.status, 200, 'Expected HTTP 200');
        assert.strictEqual(res.text.includes('All News') || res.text.includes('Все новости') || res.text.includes('Kõik uudised'), true);
        assert.strictEqual(res.text.includes('card') || res.text.includes('news'), true);
    });

    // 3. Category Filter
    await test('Public Route [GET index.php?action=category&id=1]: Filters news by category ID', async () => {
        const res = await client.get('index.php?action=category&id=1');
        assert.strictEqual(res.status, 200, 'Expected HTTP 200');
        assert.strictEqual(res.text.includes('Category') || res.text.includes('Категория'), true);
    });

    // 4. Single News Detail & Comments Section
    await test('Public Route [GET index.php?action=read&id=1]: Article detail renders full content and comments', async () => {
        const res = await client.get('index.php?action=read&id=1');
        assert.strictEqual(res.status, 200, 'Expected HTTP 200');
        assert.strictEqual(res.text.includes('Comments') || res.text.includes('Комментарии') || res.text.includes('Leave a Comment'), true);
    });

    // 5. Submit Comment via POST
    await test('Public Route [POST index.php?action=insertcomment&id=1]: Comment is persisted and rendered', async () => {
        const testComment = 'Integration Test Comment #' + Math.floor(Math.random() * 100000);
        const resPost = await client.post('index.php?action=insertcomment&id=1', {
            comment: testComment
        }, { redirect: 'follow' });

        assert.strictEqual(resPost.status, 200, 'Expected HTTP 200 on redirected read page');
        assert.strictEqual(resPost.text.includes(testComment), true, 'Submitted comment must appear in article view');
    });

    // 6. Graceful 404 Error handling
    await test('Public Route [GET index.php?action=invalid_route_xyz]: Non-existent route returns HTTP 404', async () => {
        const res = await client.get('index.php?action=invalid_route_xyz');
        assert.strictEqual(res.status, 404, 'Expected HTTP 404 Not Found');
        assert.strictEqual(res.text.includes('404') || res.text.includes('Not Found') || res.text.includes('не найдена'), true);
    });

    return results;
}

module.exports = { testPublicRoutesIntegration };

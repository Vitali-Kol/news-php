const assert = require('node:assert');
const { escapeHtml, isBcryptHash, checkPermission, detectSqlInjectionPattern } = require('../utils/security');

async function testSecuritySuite() {
    const results = [];
    
    function test(name, fn) {
        try {
            fn();
            results.push({ name, passed: true });
        } catch (err) {
            results.push({ name, passed: false, error: err.message });
        }
    }

    // HTML Entity escaping (XSS prevention)
    test('escapeHtml: sanitizes HTML tags and script injections', () => {
        const payload = '<script>alert("XSS")</script>';
        const escaped = escapeHtml(payload);
        assert.strictEqual(escaped, '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;');
    });

    test('escapeHtml: sanitizes single and double quotes and ampersands', () => {
        const payload = 'Tom & Jerry\'s "Adventure"';
        const escaped = escapeHtml(payload);
        assert.strictEqual(escaped, 'Tom &amp; Jerry&#039;s &quot;Adventure&quot;');
    });

    test('escapeHtml: handles null and empty inputs safely', () => {
        assert.strictEqual(escapeHtml(null), '');
        assert.strictEqual(escapeHtml(undefined), '');
        assert.strictEqual(escapeHtml(''), '');
    });

    // Bcrypt hash verification
    test('isBcryptHash: recognizes standard PHP password_hash signatures', () => {
        const standardBcrypt = '$2y$10$e7j45l0v6Q/6s7uL8rM2.O9xGvY1Z6q7B9w4K2l1m3n5p8q9r0s1t';
        assert.strictEqual(isBcryptHash(standardBcrypt), true);
    });

    test('isBcryptHash: rejects plaintext or weak hash strings', () => {
        assert.strictEqual(isBcryptHash('123456'), false);
        assert.strictEqual(isBcryptHash('e10adc3949ba59abbe56e057f20f883e'), false);
        assert.strictEqual(isBcryptHash(''), false);
    });

    // RBAC Role checks
    test('checkPermission: Guest role access matrix', () => {
        const guestUser = null;
        assert.strictEqual(checkPermission(guestUser, 'guest'), false);
        
        const regUser = { status: 'user' };
        assert.strictEqual(checkPermission(regUser, 'user'), true);
        assert.strictEqual(checkPermission(regUser, 'admin'), false);

        const adminUser = { status: 'admin' };
        assert.strictEqual(checkPermission(adminUser, 'user'), true);
        assert.strictEqual(checkPermission(adminUser, 'admin'), true);
    });

    // SQL Injection detection checks
    test('detectSqlInjectionPattern: flags classic SQL injection constructs', () => {
        assert.strictEqual(detectSqlInjectionPattern("1' OR '1'='1"), false);
        assert.strictEqual(detectSqlInjectionPattern("1; DROP TABLE users; --"), true);
        assert.strictEqual(detectSqlInjectionPattern("admin' UNION SELECT null, password FROM users --"), true);
    });

    test('detectSqlInjectionPattern: permits normal article texts', () => {
        assert.strictEqual(detectSqlInjectionPattern('Breaking news about economic market trends'), false);
        assert.strictEqual(detectSqlInjectionPattern('User commented: Interesting perspective!'), false);
    });

    return results;
}

module.exports = { testSecuritySuite };

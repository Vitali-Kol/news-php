const assert = require('node:assert');
const { validateEmail, validatePassword, validateRequiredFields, validateCategory, validateNewsInput } = require('../utils/validator');

async function testValidationSuite() {
    const results = [];
    
    function test(name, fn) {
        try {
            fn();
            results.push({ name, passed: true });
        } catch (err) {
            results.push({ name, passed: false, error: err.message });
        }
    }

    // Email validation unit tests
    test('validateEmail: valid standard email', () => {
        assert.strictEqual(validateEmail('user@newsportal.ee'), true);
        assert.strictEqual(validateEmail('admin.test@sub.domain.com'), true);
        assert.strictEqual(validateEmail('vitali+tag@domain.org'), true);
    });

    test('validateEmail: invalid email format rejection', () => {
        assert.strictEqual(validateEmail('plainaddress'), false);
        assert.strictEqual(validateEmail('@missingusername.com'), false);
        assert.strictEqual(validateEmail('user@.com'), false);
        assert.strictEqual(validateEmail(''), false);
        assert.strictEqual(validateEmail(null), false);
    });

    // Password validation unit tests
    test('validatePassword: valid password with matching confirmation', () => {
        const res = validatePassword('123456', '123456');
        assert.strictEqual(res.valid, true);
    });

    test('validatePassword: reject password shorter than 6 characters', () => {
        const res = validatePassword('12345', '12345');
        assert.strictEqual(res.valid, false);
        assert.match(res.message, /6 characters|символов/i);
    });

    test('validatePassword: reject password mismatch', () => {
        const res = validatePassword('securePass123', 'differentPass');
        assert.strictEqual(res.valid, false);
        assert.match(res.message, /do not match|не совпадают/i);
    });

    // Required fields validator
    test('validateRequiredFields: all fields present', () => {
        const res = validateRequiredFields({ username: 'Alex', email: 'alex@mail.ee', password: 'secretPassword' });
        assert.strictEqual(res.valid, true);
    });

    test('validateRequiredFields: detects missing or empty field', () => {
        const res = validateRequiredFields({ username: 'Alex', email: '   ', password: 'secretPassword' });
        assert.strictEqual(res.valid, false);
        assert.strictEqual(res.missingField, 'email');
    });

    // Category validation
    test('validateCategory: valid category name length', () => {
        assert.strictEqual(validateCategory('Sports'), true);
        assert.strictEqual(validateCategory('IT & Technology'), true);
    });

    test('validateCategory: reject empty or single character category', () => {
        assert.strictEqual(validateCategory(''), false);
        assert.strictEqual(validateCategory('A'), false);
        assert.strictEqual(validateCategory(null), false);
    });

    // News input validation
    test('validateNewsInput: validates complete news payload', () => {
        const res = validateNewsInput({ title: 'New Launch', text: 'Full article body', category_id: 2 });
        assert.strictEqual(res.valid, true);
    });

    test('validateNewsInput: rejects missing title or category', () => {
        const res1 = validateNewsInput({ title: '', text: 'Body', category_id: 1 });
        assert.strictEqual(res1.valid, false);

        const res2 = validateNewsInput({ title: 'Title', text: 'Body', category_id: 0 });
        assert.strictEqual(res2.valid, false);
    });

    return results;
}

module.exports = { testValidationSuite };

const assert = require('node:assert');
const { formatDataUri, generateExcerpt, parseQueryParams } = require('../utils/helpers');

async function testHelpersSuite() {
    const results = [];
    
    function test(name, fn) {
        try {
            fn();
            results.push({ name, passed: true });
        } catch (err) {
            results.push({ name, passed: false, error: err.message });
        }
    }

    // Data URI formatter
    test('formatDataUri: formats Base64 JPEG data URI correctly', () => {
        const dummyBase64 = '/9j/4AAQSkZJRgABAQEASABIAAD';
        const uri = formatDataUri(dummyBase64, 'image/jpeg');
        assert.strictEqual(uri, 'data:image/jpeg;base64,' + dummyBase64);
    });

    test('formatDataUri: returns null on empty input', () => {
        assert.strictEqual(formatDataUri(null), null);
        assert.strictEqual(formatDataUri(''), null);
    });

    // Text Excerpt generator
    test('generateExcerpt: keeps short text untouched', () => {
        const shortText = 'Short news headline text';
        assert.strictEqual(generateExcerpt(shortText, 50), shortText);
    });

    test('generateExcerpt: truncates long text and appends ellipsis', () => {
        const longText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        const excerpt = generateExcerpt(longText, 30);
        assert.strictEqual(excerpt.length, 33);
        assert.strictEqual(excerpt.endsWith('...'), true);
    });

    test('generateExcerpt: strips HTML tags from excerpt', () => {
        const htmlText = '<p><strong>Important:</strong> First paragraph.</p>';
        const excerpt = generateExcerpt(htmlText, 50);
        assert.strictEqual(excerpt, 'Important: First paragraph.');
    });

    // Query parameters parser
    test('parseQueryParams: parses URL search query strings accurately', () => {
        const query = '?action=category&id=3&msg=success';
        const parsed = parseQueryParams(query);
        assert.strictEqual(parsed.action, 'category');
        assert.strictEqual(parsed.id, '3');
        assert.strictEqual(parsed.msg, 'success');
    });

    test('parseQueryParams: handles empty or raw strings safely', () => {
        assert.deepStrictEqual(parseQueryParams(''), {});
        assert.deepStrictEqual(parseQueryParams(null), {});
    });

    return results;
}

module.exports = { testHelpersSuite };

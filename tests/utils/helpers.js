/**
 * Helper & Formatting utilities
 */
function formatDataUri(base64Data, mimeType = 'image/jpeg') {
    if (!base64Data) return null;
    return `data:${mimeType};base64,${base64Data}`;
}

function generateExcerpt(text, maxLength = 150) {
    if (!text || typeof text !== 'string') return '';
    const plain = text.replace(/<[^>]*>/g, '').trim();
    if (plain.length <= maxLength) return plain;
    return plain.substring(0, maxLength) + '...';
}

function parseQueryParams(queryString) {
    const params = {};
    if (!queryString) return params;
    const clean = queryString.startsWith('?') ? queryString.slice(1) : queryString;
    const pairs = clean.split('&');
    for (const pair of pairs) {
        const [k, v] = pair.split('=');
        if (k) params[decodeURIComponent(k)] = v ? decodeURIComponent(v) : '';
    }
    return params;
}

module.exports = {
    formatDataUri,
    generateExcerpt,
    parseQueryParams
};

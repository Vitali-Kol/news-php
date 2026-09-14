/**
 * Security & Sanitization utilities matching PHP htmlspecialchars and RBAC
 */
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function isBcryptHash(hash) {
    if (!hash || typeof hash !== 'string') return false;
    return /^\$2[ayb]\$\d{2}\$[A-Za-z0-9./]{53}$/.test(hash);
}

function checkPermission(user, requiredRole) {
    if (!user || !user.status) return false;
    if (requiredRole === 'guest') return true;
    if (requiredRole === 'user') return user.status === 'user' || user.status === 'admin';
    if (requiredRole === 'admin') return user.status === 'admin';
    return false;
}

function detectSqlInjectionPattern(input) {
    if (!input || typeof input !== 'string') return false;
    const patterns = [
        /(\b(UNION(\s+ALL)?|SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|EXEC|TRUNCATE)\b)/i,
        /(--|#|\/\*)/,
        /(';\s*--)/
    ];
    return patterns.some(pattern => pattern.test(input));
}

module.exports = {
    escapeHtml,
    isBcryptHash,
    checkPermission,
    detectSqlInjectionPattern
};

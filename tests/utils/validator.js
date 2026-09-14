/**
 * Core validation utilities matching NewsPortal business logic
 */
function validateEmail(email) {
    if (!email || typeof email !== 'string') return false;
    const trimmed = email.trim();
    if (trimmed.length === 0 || trimmed.length > 255) return false;
    const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
    return emailRegex.test(trimmed);
}

function validatePassword(password, confirmPassword = null) {
    if (!password || typeof password !== 'string') {
        return { valid: false, message: 'Password is required' };
    }
    if (password.length < 6) {
        return { valid: false, message: 'Password must contain at least 6 characters' };
    }
    if (confirmPassword !== null && password !== confirmPassword) {
        return { valid: false, message: 'Entered passwords do not match' };
    }
    return { valid: true, message: 'OK' };
}

function validateRequiredFields(fields) {
    for (const [key, value] of Object.entries(fields)) {
        if (value === undefined || value === null || (typeof value === 'string' && value.trim() === '')) {
            return { valid: false, missingField: key };
        }
    }
    return { valid: true };
}

function validateCategory(name) {
    if (!name || typeof name !== 'string') return false;
    const trimmed = name.trim();
    return trimmed.length >= 2 && trimmed.length <= 100;
}

function validateNewsInput({ title, text, category_id }) {
    if (!title || typeof title !== 'string' || title.trim().length === 0) {
        return { valid: false, message: 'Title is required' };
    }
    if (!text || typeof text !== 'string' || text.trim().length === 0) {
        return { valid: false, message: 'Text content is required' };
    }
    const catId = Number(category_id);
    if (!catId || isNaN(catId) || catId <= 0) {
        return { valid: false, message: 'Valid category ID is required' };
    }
    return { valid: true, message: 'OK' };
}

module.exports = {
    validateEmail,
    validatePassword,
    validateRequiredFields,
    validateCategory,
    validateNewsInput
};

/**
 * Session-aware HTTP Client for Integration Testing
 */
class SessionHttpClient {
    constructor(baseUrl = 'http://localhost/projekt') {
        this.baseUrl = baseUrl;
        this.cookies = new Map();
    }

    getCookieHeader() {
        if (this.cookies.size === 0) return '';
        return Array.from(this.cookies.entries())
            .map(([k, v]) => `${k}=${v}`)
            .join('; ');
    }

    saveCookies(res) {
        const setCookieHeaders = res.headers.getSetCookie 
            ? res.headers.getSetCookie() 
            : [res.headers.get('set-cookie')].filter(Boolean);

        for (const header of setCookieHeaders) {
            const parts = header.split(';')[0].split('=');
            if (parts.length >= 2) {
                const key = parts[0].trim();
                const value = parts.slice(1).join('=').trim();
                if (key) {
                    this.cookies.set(key, value);
                }
            }
        }
    }

    clearCookies() {
        this.cookies.clear();
    }

    async get(path, options = {}) {
        const url = path.startsWith('http') ? path : `${this.baseUrl}/${path.replace(/^\//, '')}`;
        const headers = { ...(options.headers || {}) };
        const cookieHeader = this.getCookieHeader();
        if (cookieHeader) headers['Cookie'] = cookieHeader;

        const res = await fetch(url, {
            method: 'GET',
            headers,
            redirect: options.redirect || 'follow'
        });

        this.saveCookies(res);
        const text = await res.text();
        return {
            status: res.status,
            headers: res.headers,
            text,
            redirectUrl: res.headers.get('location'),
            cookies: this.cookies
        };
    }

    async post(path, bodyParams = {}, options = {}) {
        const url = path.startsWith('http') ? path : `${this.baseUrl}/${path.replace(/^\//, '')}`;
        const headers = { 
            'Content-Type': 'application/x-www-form-urlencoded',
            ...(options.headers || {}) 
        };
        const cookieHeader = this.getCookieHeader();
        if (cookieHeader) headers['Cookie'] = cookieHeader;

        let body = '';
        if (bodyParams instanceof URLSearchParams) {
            body = bodyParams.toString();
        } else if (typeof bodyParams === 'object') {
            body = new URLSearchParams(bodyParams).toString();
        } else {
            body = String(bodyParams);
        }

        const res = await fetch(url, {
            method: 'POST',
            headers,
            body,
            redirect: options.redirect || 'manual'
        });

        this.saveCookies(res);
        const text = await res.text();
        return {
            status: res.status,
            headers: res.headers,
            text,
            redirectUrl: res.headers.get('location'),
            cookies: this.cookies
        };
    }
}

module.exports = SessionHttpClient;

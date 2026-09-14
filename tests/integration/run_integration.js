const { testPublicRoutesIntegration } = require('./public_routes.integration.test');
const { testAuthSessionIntegration } = require('./auth_session.integration.test');
const { testAdminPanelIntegration } = require('./admin_panel.integration.test');

const GREEN = '\x1b[32m[PASS]\x1b[0m';
const RED = '\x1b[31m[FAIL]\x1b[0m';
const CYAN = '\x1b[36m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

async function runIntegrationTests() {
    console.log(`${CYAN}${BOLD}====================================================${RESET}`);
    console.log(`${CYAN}${BOLD}  🔗 NEWS PORTAL — INTEGRATION TEST SUITE           ${RESET}`);
    console.log(`${CYAN}${BOLD}  Target: http://localhost/projekt                  ${RESET}`);
    console.log(`${CYAN}${BOLD}====================================================${RESET}\n`);

    const startTime = Date.now();
    let totalPassed = 0;
    let totalFailed = 0;

    const suites = [
        { name: '1. Public HTTP Endpoints Integration (Home, AllNews, Category, Read, Comment, 404)', fn: testPublicRoutesIntegration },
        { name: '2. User Authentication & Session Lifecycle (Registration, Login, Logout)', fn: testAuthSessionIntegration },
        { name: '3. Admin Panel & Back-Office CRUD Integration (Auth, Dashboard, News, Categories, Profile)', fn: testAdminPanelIntegration }
    ];

    for (const suite of suites) {
        console.log(`${BOLD}► ${suite.name}${RESET}`);
        const results = await suite.fn();
        for (const res of results) {
            if (res.passed) {
                console.log(`  ${GREEN} ${res.name}`);
                totalPassed++;
            } else {
                console.error(`  ${RED} ${res.name} — Error: ${res.error}`);
                totalFailed++;
            }
        }
        console.log('');
    }

    const duration = ((Date.now() - startTime) / 1000).toFixed(2);
    console.log(`${CYAN}----------------------------------------------------${RESET}`);
    console.log(`  Integration Tests Summary: ${totalPassed + totalFailed} tests executed in ${duration}s`);
    console.log(`  Passed: ${GREEN} ${totalPassed} ${RESET}`);
    console.log(`  Failed: ${totalFailed > 0 ? RED : GREEN} ${totalFailed} ${RESET}`);
    console.log(`${CYAN}${BOLD}====================================================${RESET}\n`);

    if (totalFailed > 0) {
        process.exit(1);
    }
}

if (require.main === module) {
    runIntegrationTests();
}

module.exports = { runIntegrationTests };

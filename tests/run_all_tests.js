const { testValidationSuite } = require('./unit/validation.unit.test');
const { testSecuritySuite } = require('./unit/security.unit.test');
const { testHelpersSuite } = require('./unit/helpers.unit.test');
const { testPublicRoutesIntegration } = require('./integration/public_routes.integration.test');
const { testAuthSessionIntegration } = require('./integration/auth_session.integration.test');
const { testAdminPanelIntegration } = require('./integration/admin_panel.integration.test');

const GREEN = '\x1b[32m[PASS]\x1b[0m';
const RED = '\x1b[31m[FAIL]\x1b[0m';
const YELLOW = '\x1b[33m';
const CYAN = '\x1b[36m';
const MAGENTA = '\x1b[35m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

async function main() {
    console.log(`\n${CYAN}${BOLD}╔═══════════════════════════════════════════════════════════════════╗${RESET}`);
    console.log(`${CYAN}${BOLD}║    🚀 NEWSPORTAL TEST RUNNER: UNIT & INTEGRATION SUITES           ║${RESET}`);
    console.log(`${CYAN}${BOLD}║    Command: npm run test2                                         ║${RESET}`);
    console.log(`${CYAN}${BOLD}╚═══════════════════════════════════════════════════════════════════╝${RESET}\n`);

    const overallStartTime = Date.now();
    let grandPassed = 0;
    let grandFailed = 0;

    // ---------------------------------------------------------
    // SECTION 1: UNIT TESTS
    // ---------------------------------------------------------
    console.log(`${MAGENTA}${BOLD}=====================================================================${RESET}`);
    console.log(`${MAGENTA}${BOLD}  🧪 SECTION 1: UNIT TESTS (Validation, Security, Helpers, Business) ${RESET}`);
    console.log(`${MAGENTA}${BOLD}=====================================================================${RESET}\n`);

    const unitSuites = [
        { name: 'Validation Rules (Email RFC, Password Length/Match, Required Fields, Categories)', fn: testValidationSuite },
        { name: 'Security & Sanitization (XSS Entities, Bcrypt Hash Format, RBAC Matrix, SQLi)', fn: testSecuritySuite },
        { name: 'Helpers & Formatters (Base64 BLOB URI, Excerpt Generation, Query Parser)', fn: testHelpersSuite }
    ];

    for (const suite of unitSuites) {
        console.log(`${BOLD}► [UNIT] ${suite.name}${RESET}`);
        const results = await suite.fn();
        for (const res of results) {
            if (res.passed) {
                console.log(`  ${GREEN} ${res.name}`);
                grandPassed++;
            } else {
                console.error(`  ${RED} ${res.name} — Error: ${res.error}`);
                grandFailed++;
            }
        }
        console.log('');
    }

    // ---------------------------------------------------------
    // SECTION 2: INTEGRATION TESTS
    // ---------------------------------------------------------
    console.log(`${MAGENTA}${BOLD}=====================================================================${RESET}`);
    console.log(`${MAGENTA}${BOLD}  🔗 SECTION 2: INTEGRATION TESTS (HTTP Routes, Sessions, Admin CRUD)${RESET}`);
    console.log(`${MAGENTA}${BOLD}=====================================================================${RESET}\n`);

    const integrationSuites = [
        { name: 'Public Portal Endpoints (Start, AllNews, Category Filter, Read, Post Comment, 404)', fn: testPublicRoutesIntegration },
        { name: 'User Authentication & Registration Lifecycle (Validation, Duplicate Email, Login, Logout)', fn: testAuthSessionIntegration },
        { name: 'Admin Back-Office Integration (Route Guards, Admin Login, Metrics, News CRUD, Categories, Profile)', fn: testAdminPanelIntegration }
    ];

    for (const suite of integrationSuites) {
        console.log(`${BOLD}► [INTEGRATION] ${suite.name}${RESET}`);
        const results = await suite.fn();
        for (const res of results) {
            if (res.passed) {
                console.log(`  ${GREEN} ${res.name}`);
                grandPassed++;
            } else {
                console.error(`  ${RED} ${res.name} — Error: ${res.error}`);
                grandFailed++;
            }
        }
        console.log('');
    }

    // ---------------------------------------------------------
    // OVERALL SUMMARY
    // ---------------------------------------------------------
    const totalDuration = ((Date.now() - overallStartTime) / 1000).toFixed(2);
    const totalTests = grandPassed + grandFailed;

    console.log(`${CYAN}${BOLD}=====================================================================${RESET}`);
    console.log(`${CYAN}${BOLD}  📊 ALL TESTS EXECUTION SUMMARY                                     ${RESET}`);
    console.log(`${CYAN}${BOLD}=====================================================================${RESET}`);
    console.log(`  Total Suites Run:      6`);
    console.log(`  Total Tests Executed:  ${totalTests}`);
    console.log(`  Passed:                ${GREEN} ${grandPassed} ${RESET}`);
    console.log(`  Failed:                ${grandFailed > 0 ? RED : GREEN} ${grandFailed} ${RESET}`);
    console.log(`  Execution Time:        ${YELLOW}${totalDuration}s${RESET}`);
    console.log(`${CYAN}${BOLD}=====================================================================${RESET}\n`);

    if (grandFailed > 0) {
        console.error(`${RED}${BOLD}❌ Test run failed with ${grandFailed} error(s).${RESET}\n`);
        process.exit(1);
    } else {
        console.log(`${GREEN}${BOLD}✅ All Unit and Integration tests passed successfully!${RESET}\n`);
        process.exit(0);
    }
}

main().catch(err => {
    console.error('Unexpected error running tests:', err);
    process.exit(1);
});

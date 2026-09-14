const { testValidationSuite } = require('./validation.unit.test');
const { testSecuritySuite } = require('./security.unit.test');
const { testHelpersSuite } = require('./helpers.unit.test');

const GREEN = '\x1b[32m[PASS]\x1b[0m';
const RED = '\x1b[31m[FAIL]\x1b[0m';
const CYAN = '\x1b[36m';
const BOLD = '\x1b[1m';
const RESET = '\x1b[0m';

async function runUnitTests() {
    console.log(`${CYAN}${BOLD}====================================================${RESET}`);
    console.log(`${CYAN}${BOLD}  🧪 NEWS PORTAL — UNIT TEST SUITE                  ${RESET}`);
    console.log(`${CYAN}${BOLD}====================================================${RESET}\n`);

    const startTime = Date.now();
    let totalPassed = 0;
    let totalFailed = 0;

    const suites = [
        { name: '1. Validation Unit Suite (Email, Password, Required Fields, Categories)', fn: testValidationSuite },
        { name: '2. Security Unit Suite (XSS escaping, Bcrypt Hash, RBAC, SQLi detection)', fn: testSecuritySuite },
        { name: '3. Helpers Unit Suite (Data URI, Excerpt, Query Parser)', fn: testHelpersSuite }
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
    console.log(`  Unit Tests Summary: ${totalPassed + totalFailed} tests executed in ${duration}s`);
    console.log(`  Passed: ${GREEN} ${totalPassed} ${RESET}`);
    console.log(`  Failed: ${totalFailed > 0 ? RED : GREEN} ${totalFailed} ${RESET}`);
    console.log(`${CYAN}${BOLD}====================================================${RESET}\n`);

    if (totalFailed > 0) {
        process.exit(1);
    }
}

if (require.main === module) {
    runUnitTests();
}

module.exports = { runUnitTests };

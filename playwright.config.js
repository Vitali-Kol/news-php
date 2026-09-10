// @ts-check
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  testMatch: '**/*.spec.js',
  timeout: 45000,
  fullyParallel: false,
  workers: 1,
  reporter: [['list']],
  use: {
    baseURL: 'http://localhost/projekt',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    headless: false,
    launchOptions: {
      slowMo: 200
    },
    viewport: { width: 1280, height: 820 }
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});

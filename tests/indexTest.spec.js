const { test, expect } = require('@playwright/test');

test.describe('Login Form Tests', () => {

    test('should successfully log in with valid credentials', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/login.php');
        const username = 'johnsmith';
        const password = 'password';
        await page.fill('input[name="username"]', username);
        await page.fill('input[name="password"]', password);
        await page.click('button[type="submit"]');
        await page.waitForURL('http://localhost/blogAppPHPCA4/public/user_dashboard.php');
        expect(page.url()).toBe('http://localhost/blogAppPHPCA4/public/user_dashboard.php');
    });

    test('should show an error if invalid credentials are provided', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/login.php');
        const username = 'invaliduser';
        const password = 'invalidpassword';
        await page.fill('input[name="username"]', username);
        await page.fill('input[name="password"]', password);
        await page.click('button[type="submit"]');
        const errorMessage = await page.locator('.error');
        await expect(errorMessage).toHaveText('Invalid username or password.');
    });

    test('should show an error if username or password is missing', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/index.php');
        await page.fill('input[name="username"]', '');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        const errorMessage = await page.locator('.error');
        await expect(errorMessage).toHaveText('Username and password are required.');
    });
});

const { test, expect } = require('@playwright/test');

test.describe('Registration Form Tests', () => {

    test('should successfully register a new user', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/register.php');

        const username = 'newuser1234';
        const password = 'newpassword1234';
        const confirmPassword = 'newpassword123';
        await page.fill('input[name="username"]', username);
        await page.fill('input[name="password"]', password);
        await page.fill('input[name="confirm_password"]', confirmPassword);
        await page.click('button[type="submit"]');
        await page.waitForURL('http://localhost/blogAppPHPCA4/public/login.php');
        expect(page.url()).toBe('http://localhost/blogAppPHPCA4/public/login.php');
    });

    test('should show an error if passwords do not match', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/register.php');
        await page.fill('input[name="username"]', 'newuser');
        await page.fill('input[name="password"]', 'password123');
        await page.fill('input[name="confirm_password"]', 'password456');
        await page.click('button[type="submit"]');
        const errorMessage = await page.locator('.error-message');
        await expect(errorMessage).toHaveText('Passwords do not match.');
    });

    test('should show an error if username is already taken', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/register.php');
        const username = 'johnsmith';
        const password = 'password';
        const confirmPassword = 'password';
        await page.fill('input[name="username"]', username);
        await page.fill('input[name="password"]', password);
        await page.fill('input[name="confirm_password"]', confirmPassword);

        await page.click('button[type="submit"]');

        const errorMessage = await page.locator('.error-message');
        await expect(errorMessage).toHaveText('Username is already taken.');
    });
});

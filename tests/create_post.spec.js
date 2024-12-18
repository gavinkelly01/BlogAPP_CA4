const { test, expect } = require('@playwright/test');

test.describe('Create Post Form', () => {

    test('should show an alert when submitting with empty fields', async ({ page }) => {
        await page.goto('http://localhost/blogAppPHPCA4/public/create_post.php');
        page.on('dialog', async (dialog) => {
            if (dialog.type() === 'alert') {
                expect(dialog.message()).toContain('Please fill out this field');
                await dialog.accept();
            }
        });
        await page.click('button[type="submit"]');
        await page.waitForTimeout(500);
    });

});

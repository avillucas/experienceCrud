const { test, expect } = require('@playwright/test');

test.describe('Experience CRUD Blocks', () => {
    test('should display the experience list block', async ({ page }) => {
        // This assumes a page with the block exists or we are in the editor
        // For now, we just check if the wrapper exists on a given URL
        await page.goto('/experiencias/'); 
        const wrapper = page.locator('.ec-experience-list-wrapper');
        await expect(wrapper).toBeVisible();
    });

    test('should open modal on click', async ({ page }) => {
        await page.goto('/experiencias/');
        const firstCardButton = page.locator('.experience-card__button').first();
        await firstCardButton.click();
        
        const modal = page.locator('dialog.experience-modal');
        await expect(modal).toBeVisible();
    });
});

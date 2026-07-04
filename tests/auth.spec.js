const { test, expect } = require('@playwright/test');

const BASE = 'http://localhost/simple_board/public';

test.describe('회원가입', () => {
  test('성공 - 정상 가입 후 로그인 페이지 이동', async ({ page }) => {
    await page.goto(`${BASE}/signup`);
    await page.fill('#username', 'playwright01');
    await page.fill('#password', 'password123');
    await page.fill('#passwordConfirm', 'password123');
    await page.fill('#nickname', '플레이라이트');
    await page.click('#btnSignup');
    await expect(page).toHaveURL(/login/);
  });

  test('실패 - 비밀번호 불일치 시 에러 메시지 출력', async ({ page }) => {
    await page.goto(`${BASE}/signup`);
    await page.fill('#username', 'playwright02');
    await page.fill('#password', 'password123');
    await page.fill('#passwordConfirm', 'wrongpass!!');
    await page.fill('#nickname', '플레이라이트2');
    await page.click('#btnSignup');
    await expect(page.locator('#alert')).toBeVisible();
  });
});

test.describe('로그인', () => {
  test('성공 - 게시글 목록 이동', async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.fill('#username', 'playwright01');
    await page.fill('#password', 'password123');
    await page.click('#btnLogin');
    await expect(page).toHaveURL(/posts/);
  });

  test('실패 - 잘못된 비밀번호 시 에러 메시지 출력', async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.fill('#username', 'playwright01');
    await page.fill('#password', 'wrongpass');
    await page.click('#btnLogin');
    await expect(page.locator('#alert')).toContainText('일치하지 않습니다');
  });
});
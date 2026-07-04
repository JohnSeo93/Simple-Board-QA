const { test, expect } = require('@playwright/test');

const BASE = 'http://localhost/simple_board/public';

// 각 테스트 전 로그인
test.beforeEach(async ({ page }) => {
  await page.goto(`${BASE}/login`);
  await page.fill('#username', 'playwright01');
  await page.fill('#password', 'password123');
  await page.click('#btnLogin');
  await expect(page).toHaveURL(/posts/);
});

test.describe('게시글 작성', () => {
  test('성공 - 정상 게시글 작성 후 상세 페이지 이동', async ({ page }) => {
    await page.click('text=글쓰기');
    await page.fill('#title', 'Playwright 테스트 게시글');
    await page.fill('#content', 'Playwright로 작성한 테스트 내용입니다.');
    await page.click('#btnSubmit');
    await expect(page).toHaveURL(/posts\/\d+/);
  });

  test('실패 - 제목 누락 시 에러 메시지 출력', async ({ page }) => {
    await page.click('text=글쓰기');
    await page.fill('#title', '');
    await page.fill('#content', '내용만 있고 제목 없음');
    await page.click('#btnSubmit');
    await expect(page.locator('#alert')).toBeVisible();
  });

  test('실패 - 내용 누락 시 에러 메시지 출력', async ({ page }) => {
    await page.click('text=글쓰기');
    await page.fill('#title', '제목만 있고 내용 없음');
    await page.fill('#content', '');
    await page.click('#btnSubmit');
    await expect(page.locator('#alert')).toBeVisible();
  });
});

test.describe('게시글 목록', () => {
  test('성공 - 게시글 목록 화면 표시', async ({ page }) => {
    await expect(page.locator('text=게시글 목록')).toBeVisible();
    await expect(page.locator('text=글쓰기')).toBeVisible();
  });

  test('성공 - 게시글 클릭 시 상세 페이지 이동', async ({ page }) => {
    const firstPost = page.locator('.post-title-link').first();
    await firstPost.click();
    await expect(page).toHaveURL(/posts\/\d+/);
  });
});

test.describe('게시글 상세', () => {
  test('성공 - 본인 글 수정/삭제 버튼 노출', async ({ page }) => {
    await page.click('text=글쓰기');
    await page.fill('#title', '버튼 노출 확인용 글');
    await page.fill('#content', '본인 글 테스트');
    await page.click('#btnSubmit');
    await expect(page).toHaveURL(/posts\/\d+/);
    await expect(page.locator('text=수정')).toBeVisible();
    await expect(page.locator('text=삭제')).toBeVisible();
  });
});

test.describe('댓글 작성', () => {
  test('성공 - 댓글 작성 후 화면에 표시', async ({ page }) => {
    const firstPost = page.locator('.post-title-link').first();
    await firstPost.click();
    await page.fill('#commentContent', 'Playwright 테스트 댓글');
    await page.click('#btnComment');
    await expect(page.locator('text=Playwright 테스트 댓글')).toBeVisible();
  });

  test('실패 - 빈 댓글 작성 시 alert 출력', async ({ page }) => {
    const firstPost = page.locator('.post-title-link').first();
    await firstPost.click();
    await page.fill('#commentContent', '');
    page.on('dialog', async dialog => {
      expect(dialog.message()).toContain('댓글');
      await dialog.accept();
    });
    await page.click('#btnComment');
  });
});

test.describe('로그아웃', () => {
  test('성공 - 로그아웃 후 로그인 페이지 이동', async ({ page }) => {
    await page.click('#btnLogout');
    await expect(page).toHaveURL(/login/);
  });
});
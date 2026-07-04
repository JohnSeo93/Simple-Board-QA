# 📋 Simple Board - QA 포트폴리오 프로젝트

> **XAMPP + PHP + MySQL 기반 게시판 서비스**  
> Postman · Selenium · Playwright 자동화 테스트 학습용

---

## 📁 프로젝트 구조

```
simple_board/
├── api/                          # REST API 엔드포인트
│   ├── signup.php                # POST /api/signup
│   ├── login.php                 # POST /api/login
│   ├── logout.php                # POST /api/logout
│   ├── posts_list.php            # GET  /api/posts
│   ├── posts_create.php          # POST /api/posts
│   ├── posts_detail.php          # GET  /api/posts/{id}
│   ├── posts_update.php          # PUT  /api/posts/{id}
│   ├── posts_delete.php          # DELETE /api/posts/{id}
│   └── comments_create.php       # POST /api/comments
├── config/
│   ├── database.php              # DB 접속 설정
│   └── schema.sql                # DB 스키마 (초기화 SQL)
├── includes/
│   └── helpers.php               # 공통 유틸 함수
├── public/
│   ├── index.php                 # 라우터 (Front Controller)
│   ├── .htaccess                 # URL 라우팅 설정
│   ├── signup.php                # 회원가입 화면
│   ├── login.php                 # 로그인 화면
│   ├── posts.php                 # 게시글 목록 화면
│   ├── post_write.php            # 게시글 작성 화면
│   ├── post_detail.php           # 게시글 상세 화면
│   ├── post_edit.php             # 게시글 수정 화면
│   └── css/
│       └── style.css             # 전역 스타일
├── Simple_Board_API.postman_collection.json
└── README.md
```

---

## ⚙️ XAMPP 설치 및 설정

### 1단계 – 파일 배치

```
C:\xampp\htdocs\simple_board\   (Windows)
/Applications/XAMPP/htdocs/simple_board/   (macOS)
```

폴더째로 복사 후, **Apache**와 **MySQL**을 XAMPP Control Panel에서 Start.

### 2단계 – Apache mod_rewrite 활성화

`C:\xampp\apache\conf\httpd.conf` 에서 아래 줄 주석 해제:

```
LoadModule rewrite_module modules/mod_rewrite.so
```

그리고 `<Directory "C:/xampp/htdocs">` 블록에서:

```
AllowOverride All
```

### 3단계 – 데이터베이스 초기화

phpMyAdmin(`http://localhost/phpmyadmin`) 접속 후  
**SQL 탭**에서 `config/schema.sql` 파일 내용을 붙여넣고 실행.

또는 MySQL CLI:

```bash
mysql -u root -p < config/schema.sql
```

### 4단계 – 접속 확인

| URL | 설명 |
|-----|------|
| `http://localhost/simple_board` | 메인 (→ /posts 리다이렉트) |
| `http://localhost/simple_board/signup` | 회원가입 |
| `http://localhost/simple_board/login` | 로그인 |
| `http://localhost/simple_board/posts` | 게시글 목록 |

---

## 🔌 API 명세

| Method | Endpoint | 인증 | 설명 |
|--------|----------|------|------|
| POST | `/api/signup` | ❌ | 회원가입 |
| POST | `/api/login` | ❌ | 로그인 |
| POST | `/api/logout` | ✅ | 로그아웃 |
| GET | `/api/posts` | ✅ | 게시글 목록 |
| POST | `/api/posts` | ✅ | 게시글 작성 |
| GET | `/api/posts/{id}` | ✅ | 게시글 상세 + 조회수 증가 |
| PUT | `/api/posts/{id}` | ✅ 본인 | 게시글 수정 |
| DELETE | `/api/posts/{id}` | ✅ 본인 | 게시글 삭제 (논리 삭제) |
| POST | `/api/comments` | ✅ | 댓글 작성 |

### 공통 응답 형식

**성공**
```json
{ "message": "성공 메시지", "data": {} }
```

**실패**
```json
{ "message": "에러 메시지" }
```

### HTTP 상태 코드

| 코드 | 의미 |
|------|------|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request (유효성 오류) |
| 401 | Unauthorized (미인증) |
| 403 | Forbidden (권한 없음) |
| 404 | Not Found |
| 409 | Conflict (중복) |

---

## 🤖 Postman 자동화 테스트

### 준비

1. Postman 실행
2. `Simple_Board_API.postman_collection.json` Import
3. Variables 탭에서 `baseUrl` 확인 (`http://localhost/simple_board`)

### 실행 순서 (중요)

로그인 세션 쿠키가 필요하므로 **순서대로** 실행:

1. 🔐 인증 > [성공] 회원가입
2. 🔐 인증 > [성공] 로그인  ← 세션 쿠키 생성
3. 📝 게시글 > 나머지 테스트
4. 💬 댓글 > 나머지 테스트
5. 🔓 로그아웃

### Collection Runner 전체 실행

`Run Collection` → Delay 500ms 설정 후 실행

---

## 🎭 Playwright 테스트 구조 (참고)

```javascript
// tests/auth.spec.js
import { test, expect } from '@playwright/test';

const BASE = 'http://localhost/simple_board';

test.describe('회원가입', () => {
  test('성공 - 정상 가입 후 로그인 페이지 이동', async ({ page }) => {
    await page.goto(`${BASE}/signup`);
    await page.fill('#username', 'playwright01');
    await page.fill('#password', 'password123');
    await page.fill('#passwordConfirm', 'password123');
    await page.fill('#nickname', '테스터');
    await page.click('#btnSignup');
    await expect(page).toHaveURL(/login/);
  });

  test('실패 - 비밀번호 불일치', async ({ page }) => {
    await page.goto(`${BASE}/signup`);
    await page.fill('#username', 'playwright02');
    await page.fill('#password', 'password123');
    await page.fill('#passwordConfirm', 'different!!');
    await page.fill('#nickname', '테스터2');
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

  test('실패 - 오류 메시지 출력', async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.fill('#username', 'playwright01');
    await page.fill('#password', 'wrongpass');
    await page.click('#btnLogin');
    await expect(page.locator('#alert')).toContainText('일치하지 않습니다');
  });
});
```

### Playwright 설치 및 실행

```bash
npm init -y
npm install -D @playwright/test
npx playwright install chromium

npx playwright test
npx playwright test --headed      # 브라우저 보이며 실행
npx playwright test --ui          # UI 모드
```

---

## 🐍 Selenium (Python) 테스트 구조 (참고)

```python
# tests/test_auth.py
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

BASE = "http://localhost/simple_board"

@pytest.fixture
def driver():
    options = webdriver.ChromeOptions()
    # options.add_argument("--headless")
    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(5)
    yield driver
    driver.quit()

class TestSignup:
    def test_success(self, driver):
        driver.get(f"{BASE}/signup")
        driver.find_element(By.ID, "username").send_keys("selenium01")
        driver.find_element(By.ID, "password").send_keys("password123")
        driver.find_element(By.ID, "passwordConfirm").send_keys("password123")
        driver.find_element(By.ID, "nickname").send_keys("셀레니움유저")
        driver.find_element(By.ID, "btnSignup").click()
        WebDriverWait(driver, 5).until(EC.url_contains("login"))
        assert "login" in driver.current_url

    def test_password_mismatch(self, driver):
        driver.get(f"{BASE}/signup")
        driver.find_element(By.ID, "username").send_keys("selenium02")
        driver.find_element(By.ID, "password").send_keys("password123")
        driver.find_element(By.ID, "passwordConfirm").send_keys("mismatch!")
        driver.find_element(By.ID, "nickname").send_keys("닉네임")
        driver.find_element(By.ID, "btnSignup").click()
        alert = WebDriverWait(driver, 5).until(
            EC.visibility_of_element_located((By.ID, "alert"))
        )
        assert alert.is_displayed()

class TestLogin:
    def test_success(self, driver):
        driver.get(f"{BASE}/login")
        driver.find_element(By.ID, "username").send_keys("selenium01")
        driver.find_element(By.ID, "password").send_keys("password123")
        driver.find_element(By.ID, "btnLogin").click()
        WebDriverWait(driver, 5).until(EC.url_contains("posts"))
        assert "posts" in driver.current_url

    def test_wrong_password(self, driver):
        driver.get(f"{BASE}/login")
        driver.find_element(By.ID, "username").send_keys("selenium01")
        driver.find_element(By.ID, "password").send_keys("wrongpass")
        driver.find_element(By.ID, "btnLogin").click()
        alert = WebDriverWait(driver, 5).until(
            EC.visibility_of_element_located((By.ID, "alert"))
        )
        assert "일치하지 않습니다" in alert.text
```

### Selenium 설치

```bash
pip install selenium pytest
# ChromeDriver는 Chrome 버전에 맞게 자동 설치
pip install webdriver-manager
```

---

## ✅ 테스트 시나리오 체크리스트

### 회원가입
- [ ] 정상 가입 → 로그인 페이지 이동
- [ ] 아이디 중복 → 409 에러
- [ ] 비밀번호 불일치 → 400 에러
- [ ] 필수값 누락 → 400 에러
- [ ] 아이디 3자 이하 → 400 에러
- [ ] 비밀번호 7자 이하 → 400 에러

### 로그인
- [ ] 정상 로그인 → /posts 이동
- [ ] 틀린 비밀번호 → 401 + 메시지 확인
- [ ] 없는 아이디 → 401 + 메시지 확인
- [ ] 미로그인 → /posts 접근 시 /login 리다이렉트

### 게시글
- [ ] 목록 조회 → 최신순 정렬
- [ ] 작성 → 상세 페이지 이동
- [ ] 제목 누락 작성 → 400 에러
- [ ] 상세 조회 → 조회수 1 증가
- [ ] 본인 글 수정 → 성공
- [ ] 타인 글 수정 시도 → 403 에러
- [ ] 본인 글 삭제 → 목록에서 미노출 (논리 삭제)
- [ ] 타인 글 삭제 시도 → 403 에러
- [ ] 수정/삭제 버튼 → 본인 글만 노출

### 댓글
- [ ] 정상 댓글 작성 → 페이지 갱신 후 노출
- [ ] 내용 누락 → 400 에러
- [ ] 미인증 댓글 시도 → 401 에러

### 로그아웃
- [ ] 로그아웃 → 세션 종료 → /login 이동
- [ ] 로그아웃 후 /posts 접근 → /login 리다이렉트

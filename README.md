# 📋 Simple Board - QA Automation Portfolio

> **QA 8년차의 자동화 테스트 포트폴리오**  
> XAMPP + PHP + MySQL 기반 게시판 서비스를 직접 구축하고  
> Postman · Playwright를 활용한 자동화 테스트를 수행했습니다.

---

## 🎯 프로젝트 목적

게임/앱/콘솔 도메인 QA 경력을 바탕으로  
**API 자동화 테스트 · E2E 자동화 테스트** 역량을 증명하기 위해 제작했습니다.

단순히 툴을 사용하는 것에서 나아가  
**환경 구성 → 이슈 발견 → 원인 분석 → 해결** 전 과정을 직접 경험했습니다.

---

## 🛠 기술 스택

| 분류 | 기술 |
|------|------|
| 서버 | XAMPP (Apache + MySQL) |
| 백엔드 | PHP 8.2 |
| DB | MariaDB |
| API 테스트 | Postman |
| E2E 테스트 | Playwright (JavaScript) |

---

## 📁 프로젝트 구조

```
simple_board/
├── api/                  # REST API 엔드포인트
├── config/               # DB 설정 및 스키마
├── includes/             # 공통 헬퍼 함수
├── public/               # 프론트엔드 페이지
├── tests/
│   ├── auth.spec.js      # 회원가입/로그인 E2E 테스트
│   └── posts.spec.js     # 게시글/댓글 E2E 테스트
└── Simple_Board_API.postman_collection.json
```

---

## 🔌 API 명세

| Method | Endpoint | 설명 |
|--------|----------|------|
| POST | `/api/signup` | 회원가입 |
| POST | `/api/login` | 로그인 |
| POST | `/api/logout` | 로그아웃 |
| GET | `/api/posts` | 게시글 목록 |
| POST | `/api/posts` | 게시글 작성 |
| GET | `/api/posts/{id}` | 게시글 상세 |
| PUT | `/api/posts/{id}` | 게시글 수정 |
| DELETE | `/api/posts/{id}` | 게시글 삭제 |
| POST | `/api/comments` | 댓글 작성 |

---

## ✅ 테스트 결과

### Postman API 테스트
- 총 15개 테스트 케이스 작성
- 정상/비정상 시나리오 포함
- Collection Runner로 전체 자동 실행

### Playwright E2E 테스트
```
auth.spec.js  - 4개 passed
posts.spec.js - 9개 passed
총 13개 passed
```

---

## 🐛 버그 리포트

### BUG-001 | 로그아웃 후 댓글 작성 API 인증 미처리

**심각도:** High

**발견 방법:** Postman Collection Runner

**재현 순서:**
1. POST /api/login 로그인
2. POST /api/logout 로그아웃
3. POST /api/comments 댓글 작성 시도

**기대 결과:** 401 Unauthorized

**실제 결과:** 201 Created (댓글 작성 성공)

**원인:** 로그아웃 후 Postman 세션 쿠키가 유지되어 서버가 인증된 상태로 처리

---

## 🔥 트러블슈팅 기록

> 단순 테스트 실행이 아닌, 환경 구성부터 직접 부딪히며 해결한 과정입니다.

### 1. XAMPP 서브디렉토리 라우팅 문제
**증상:** localhost/simple_board/public/ 접속 시 전체 404 발생

**원인 분석:**
- Apache AllowOverride None 설정으로 .htaccess 미적용
- PHP 리다이렉트 경로에 베이스 경로 누락
- URI 매칭 로직의 경로 불일치

**해결:**
- httpd.conf 에서 AllowOverride All 설정
- BASE_PATH 상수화로 경로 일관성 확보
- index.php URI 파싱 로직 수정

**인사이트:**
> 개발/운영 환경 차이로 인한 경로 이슈는 환경변수나 상수로 베이스 경로를 관리해야 한다.
> 실무에서도 로컬에선 되는데 서버에서 안 되는 패턴과 동일한 케이스.

---

### 2. MySQL DB 파일 손상
**증상:** MySQL 시작 후 즉시 종료

**에러 로그:**
```
Fatal error: Can't open and lock privilege tables: 
Incorrect file format 'roles_mapping'
```

**해결:** backup 폴더로 data 초기화 후 재시작

**인사이트:**
> 에러 로그를 읽고 원인을 추적하는 능력이 QA에게도 필요하다.
> 개발자에게 "안 돼요" 가 아니라 "이 에러 때문에 안 됩니다" 로 소통 가능.

---

### 3. Playwright 테스트 셀렉터 충돌
**증상:** locator('text=수정') 이 2개 요소를 찾아 Fail

**원인:** 게시글 제목에 '수정' 단어가 포함되어 버튼과 충돌

**해결:** 테스트용 데이터에 셀렉터와 겹치는 단어 사용 금지

**인사이트:**
> 테스트 데이터 설계도 테스트 설계의 일부다.
> 테스트 코드가 Fail 났을 때 버그인지 테스트 코드 문제인지 구분하는 것이 핵심.

---

## 💡 QA 관점 인사이트

8년간 게임/앱/콘솔 도메인에서 쌓은 경험을 자동화 툴과 결합하면서 느낀 점:

- **로그를 읽는 능력** → 에러 원인을 스스로 추적 가능
- **버그 재현 능력** → 자동화 시나리오 설계에 그대로 활용
- **도메인 경험** → 어떤 케이스가 버그 날지 예측 가능
- **AI 활용** → 코드 생성은 AI, 판단과 검증은 QA

> 자동화 툴은 도구일 뿐, QA의 핵심은 무엇을 테스트할지 아는 것이다.

---

## 🚀 실행 방법

### 환경 설정
1. XAMPP 설치 후 Apache, MySQL 시작
2. config/schema.sql 을 phpMyAdmin에서 실행
3. http://localhost/simple_board/public/ 접속

### Playwright 테스트 실행
```bash
cd tests
npm install
npx playwright install chromium
npx playwright test --headed
```

### Postman 테스트
1. Simple_Board_API.postman_collection.json Import
2. Environment에 baseUrl = http://localhost/simple_board/public 설정
3. Collection Runner 실행

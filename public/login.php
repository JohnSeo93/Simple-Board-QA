<?php
require_once __DIR__ . '/../includes/helpers.php';
requireGuest();
$base = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>로그인 - Simple Board</title>
  <link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <h1>📋 Simple Board</h1>
    <div id="alert" class="alert alert-danger" style="display:none"></div>
    <div class="form-group">
      <label for="username">아이디</label>
      <input id="username" type="text" class="form-control" placeholder="아이디 입력" autocomplete="username">
    </div>
    <div class="form-group">
      <label for="password">비밀번호</label>
      <input id="password" type="password" class="form-control" placeholder="비밀번호 입력" autocomplete="current-password">
    </div>
    <button id="btnLogin" class="btn btn-primary btn-block" style="margin-top:8px">로그인</button>
    <p class="auth-link">계정이 없으신가요? <a href="<?= $base ?>/signup">회원가입</a></p>
  </div>
</div>
<script>
  const BASE = '<?= $base ?>';
  const alertEl = document.getElementById('alert');
  function showError(msg) {
    alertEl.textContent = msg;
    alertEl.style.display = 'block';
  }

  document.getElementById('btnLogin').addEventListener('click', async () => {
    alertEl.style.display = 'none';
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    const res = await fetch(BASE + '/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password }),
    });
    const data = await res.json();

    if (res.ok) {
      location.href = BASE + '/posts';
    } else {
      showError(data.message || '로그인 실패');
    }
  });

  document.querySelectorAll('.form-control').forEach(el => {
    el.addEventListener('keydown', e => {
      if (e.key === 'Enter') document.getElementById('btnLogin').click();
    });
  });
</script>
</body>
</html>

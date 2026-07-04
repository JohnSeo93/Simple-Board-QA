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
  <title>회원가입 - Simple Board</title>
  <link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <h1>📋 Simple Board</h1>
    <div id="alert" class="alert alert-danger" style="display:none"></div>
    <div class="form-group">
      <label for="username">아이디</label>
      <input id="username" type="text" class="form-control" placeholder="4~20자" maxlength="20" autocomplete="username">
    </div>
    <div class="form-group">
      <label for="password">비밀번호</label>
      <input id="password" type="password" class="form-control" placeholder="8~20자" maxlength="20" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="passwordConfirm">비밀번호 확인</label>
      <input id="passwordConfirm" type="password" class="form-control" placeholder="비밀번호 재입력" maxlength="20" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="nickname">닉네임</label>
      <input id="nickname" type="text" class="form-control" placeholder="2~20자" maxlength="20">
    </div>
    <button id="btnSignup" class="btn btn-primary btn-block" style="margin-top:8px">회원가입</button>
    <p class="auth-link">이미 계정이 있으신가요? <a href="<?= $base ?>/login">로그인</a></p>
  </div>
</div>
<script>
  const BASE = '<?= $base ?>';
  const alertEl = document.getElementById('alert');
  function showError(msg) {
    alertEl.textContent = msg;
    alertEl.style.display = 'block';
  }

  document.getElementById('btnSignup').addEventListener('click', async () => {
    alertEl.style.display = 'none';
    const username        = document.getElementById('username').value.trim();
    const password        = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('passwordConfirm').value;
    const nickname        = document.getElementById('nickname').value.trim();

    const res = await fetch(BASE + '/api/signup', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password, password_confirm: passwordConfirm, nickname }),
    });
    const data = await res.json();

    if (res.ok) {
      location.href = BASE + '/login';
    } else {
      showError(data.message || '회원가입 실패');
    }
  });

  document.querySelectorAll('.form-control').forEach(el => {
    el.addEventListener('keydown', e => {
      if (e.key === 'Enter') document.getElementById('btnSignup').click();
    });
  });
</script>
</body>
</html>

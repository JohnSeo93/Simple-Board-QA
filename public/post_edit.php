<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();
$user   = getCurrentUser();
$postId = (int)($_GET['id'] ?? 0);
$base   = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>게시글 수정 - Simple Board</title>
  <link rel="stylesheet" href="<?= $base ?>/css/style.css">
</head>
<body>
<nav class="nav">
  <span class="nav__brand"><a href="<?= $base ?>/posts">📋 Simple Board</a></span>
  <div class="nav__actions">
    <span class="nav__user"><?= htmlspecialchars($user['nickname']) ?>님</span>
    <button id="btnLogout" class="btn btn-outline btn-sm">로그아웃</button>
  </div>
</nav>

<div class="container">
  <p class="page-title">✏️ 게시글 수정</p>
  <div class="form-card">
    <div id="alert" class="alert alert-danger" style="display:none"></div>
    <div class="form-group">
      <label for="title">제목 <span style="color:var(--danger)">*</span></label>
      <input id="title" type="text" class="form-control">
    </div>
    <div class="form-group">
      <label for="content">내용 <span style="color:var(--danger)">*</span></label>
      <textarea id="content" class="form-control"></textarea>
    </div>
    <div class="form-actions">
      <a href="<?= $base ?>/posts/<?= $postId ?>" class="btn btn-outline">취소</a>
      <button id="btnSubmit" class="btn btn-primary">수정 완료</button>
    </div>
  </div>
</div>

<script>
  const BASE        = '<?= $base ?>';
  const POST_ID     = <?= $postId ?>;
  const CURRENT_UID = <?= (int)$user['id'] ?>;

  async function loadPost() {
    const res  = await fetch(`${BASE}/api/posts/${POST_ID}`);
    const data = await res.json();
    if (!res.ok) { alert('게시글을 불러올 수 없습니다.'); location.href = BASE + '/posts'; return; }
    const p = data.post;
    if (parseInt(p.user_id) !== CURRENT_UID) {
      alert('수정 권한이 없습니다.');
      location.href = `${BASE}/posts/${POST_ID}`;
      return;
    }
    document.getElementById('title').value   = p.title;
    document.getElementById('content').value = p.content;
  }

  document.getElementById('btnSubmit').addEventListener('click', async () => {
    const alertEl = document.getElementById('alert');
    alertEl.style.display = 'none';
    const title   = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();

    const res = await fetch(`${BASE}/api/posts/${POST_ID}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title, content }),
    });
    const data = await res.json();

    if (res.ok) {
      location.href = `${BASE}/posts/${POST_ID}`;
    } else {
      alertEl.textContent = data.message || '수정 실패';
      alertEl.style.display = 'block';
    }
  });

  document.getElementById('btnLogout').addEventListener('click', async () => {
    await fetch(BASE + '/api/logout', { method: 'POST' });
    location.href = BASE + '/login';
  });

  loadPost();
</script>
</body>
</html>

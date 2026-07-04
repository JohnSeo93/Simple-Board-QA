<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();
$user = getCurrentUser();
$base = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>게시글 목록 - Simple Board</title>
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
  <div class="card">
    <div class="card-header">
      <h2>게시글 목록</h2>
      <a href="<?= $base ?>/posts/write" class="btn btn-primary btn-sm">✏️ 글쓰기</a>
    </div>
    <table id="postsTable">
      <thead>
        <tr>
          <th style="width:60px">번호</th>
          <th>제목</th>
          <th style="width:110px">작성자</th>
          <th style="width:130px">작성일</th>
          <th style="width:70px">조회수</th>
        </tr>
      </thead>
      <tbody id="postsList">
        <tr><td colspan="5" class="muted" style="text-align:center;padding:32px">불러오는 중...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  const BASE = '<?= $base ?>';

  async function loadPosts() {
    const res  = await fetch(BASE + '/api/posts');
    const data = await res.json();
    const tbody = document.getElementById('postsList');

    if (!res.ok || !data.posts || data.posts.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="muted" style="text-align:center;padding:32px">게시글이 없습니다.</td></tr>';
      return;
    }

    tbody.innerHTML = data.posts.map((p, i) => `
      <tr>
        <td class="muted">${data.posts.length - i}</td>
        <td><a class="post-title-link" href="${BASE}/posts/${p.id}">${escHtml(p.title)}</a></td>
        <td class="muted">${escHtml(p.author)}</td>
        <td class="muted">${formatDate(p.created_at)}</td>
        <td class="muted">${p.view_count}</td>
      </tr>
    `).join('');
  }

  function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function formatDate(dt) {
    return dt ? dt.slice(0,10) : '';
  }

  loadPosts();

  document.getElementById('btnLogout').addEventListener('click', async () => {
    await fetch(BASE + '/api/logout', { method: 'POST' });
    location.href = BASE + '/login';
  });
</script>
</body>
</html>

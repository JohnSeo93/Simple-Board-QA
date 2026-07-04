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
  <title>게시글 - Simple Board</title>
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
  <div id="postWrap" class="card" style="margin-bottom:24px">
    <div style="text-align:center;padding:48px;color:var(--muted)">불러오는 중...</div>
  </div>

  <div class="comments-section">
    <h3 id="commentCount">댓글</h3>
    <div id="commentsList"></div>
    <div class="comment-form">
      <textarea id="commentContent" class="form-control" placeholder="댓글을 입력하세요"></textarea>
      <div style="display:flex;justify-content:flex-end">
        <button id="btnComment" class="btn btn-primary btn-sm">댓글 등록</button>
      </div>
    </div>
  </div>
</div>

<script>
  const BASE       = '<?= $base ?>';
  const POST_ID    = <?= $postId ?>;
  const CURRENT_UID = <?= (int)$user['id'] ?>;

  function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function formatDate(dt) { return dt ? dt.replace('T',' ').slice(0,16) : ''; }

  async function loadPost() {
    const res  = await fetch(`${BASE}/api/posts/${POST_ID}`);
    if (!res.ok) {
      document.getElementById('postWrap').innerHTML = '<div style="padding:48px;text-align:center;color:var(--danger)">게시글을 불러올 수 없습니다.</div>';
      return;
    }
    const data = await res.json();
    const p    = data.post;
    const isOwner = CURRENT_UID === parseInt(p.user_id);

    document.getElementById('postWrap').innerHTML = `
      <div class="post-header">
        <h1>${esc(p.title)}</h1>
        <div class="post-meta">
          <span>✍️ ${esc(p.author)}</span>
          <span>🕐 ${formatDate(p.created_at)}</span>
          <span>👁 ${p.view_count}</span>
        </div>
      </div>
      <div class="post-body">${esc(p.content)}</div>
      <div class="post-actions">
        <a href="${BASE}/posts" class="btn btn-outline btn-sm">목록으로</a>
        ${isOwner ? `<a href="${BASE}/posts/${POST_ID}/edit" class="btn btn-outline btn-sm">수정</a>
        <button id="btnDelete" class="btn btn-danger btn-sm">삭제</button>` : ''}
      </div>
    `;

    if (isOwner) {
      document.getElementById('btnDelete').addEventListener('click', async () => {
        if (!confirm('정말 삭제하시겠습니까?')) return;
        const r = await fetch(`${BASE}/api/posts/${POST_ID}`, { method: 'DELETE' });
        if (r.ok) { location.href = BASE + '/posts'; }
        else { alert('삭제 실패'); }
      });
    }

    renderComments(data.comments);
  }

  function renderComments(comments) {
    document.getElementById('commentCount').textContent = `댓글 ${comments.length}개`;
    if (comments.length === 0) {
      document.getElementById('commentsList').innerHTML = '<p style="color:var(--muted);font-size:.88rem">아직 댓글이 없습니다.</p>';
      return;
    }
    document.getElementById('commentsList').innerHTML = comments.map(c => `
      <div class="comment-item">
        <div class="comment-author">${esc(c.author)}</div>
        <div class="comment-date">${formatDate(c.created_at)}</div>
        <div class="comment-content">${esc(c.content)}</div>
      </div>
    `).join('');
  }

  document.getElementById('btnComment').addEventListener('click', async () => {
    const content = document.getElementById('commentContent').value.trim();
    if (!content) { alert('댓글 내용을 입력해주세요.'); return; }
    const res = await fetch(BASE + '/api/comments', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ post_id: POST_ID, content }),
    });
    if (res.ok) {
      document.getElementById('commentContent').value = '';
      loadPost();
    } else {
      const data = await res.json();
      alert(data.message || '댓글 등록 실패');
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

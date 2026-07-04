<?php
require_once __DIR__ . '/../includes/helpers.php';

startSession();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']
    );
}
session_destroy();

jsonResponse(200, ['message' => '로그아웃 되었습니다.']);

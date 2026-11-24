<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim((string) input('login_id')); $pw = (string) input('password');
    $u = $id !== '' ? q_one('select * from users where login_id=?', [$id]) : null;
    if (!$u || !password_verify($pw, $u['password'])) { flash('error', '아이디 또는 비밀번호가 올바르지 않습니다.'); redirect('/auth/login'); }
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => (int) $u['id'], 'login_id' => $u['login_id'], 'role' => $u['role']];
    flash('ok', '로그인되었습니다.');
    redirect('/');
}
render('auth/login', ['title' => '로그인']);

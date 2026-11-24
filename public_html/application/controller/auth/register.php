<?php
include_once_path('application/core/member');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = preg_replace('/\s+/', '', (string) input('login_id')); $pw = (string) input('password');
    $errs = validate_member($id, $pw, true);
    if ($pw !== (string) input('password_confirmation')) $errs[] = '비밀번호 확인이 일치하지 않습니다.';
    if ($errs) { flash('errors', implode("\n", $errs)); redirect('/auth/register'); }
    $n = now_str();
    $uid = q_insert('users', ['login_id' => $id, 'password' => password_hash($pw, PASSWORD_DEFAULT), 'role' => 'USER', 'created_at' => $n, 'updated_at' => $n]);
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => $uid, 'login_id' => $id, 'role' => 'USER'];
    flash('ok', '회원가입이 완료되었습니다.');
    redirect('/');
}
render('auth/register', ['title' => '회원가입']);

<?php include_once_path('application/core/admin');
include_once_path('application/core/member');
$id = (int) input('id', 0); $login = preg_replace('/\s+/', '', (string) input('login_id')); $pw = (string) input('password'); $role = input('role') === 'SUPER' ? 'SUPER' : 'USER';
$errs = validate_member($login, $pw, !$id, $id ?: null);    // 회원가입과 동일한 검증 재사용
if ($errs) back_with('errors', implode("\n", $errs));
$row = ['login_id' => $login, 'role' => $role, 'updated_at' => now_str()];
if ($pw !== '') $row['password'] = password_hash($pw, PASSWORD_DEFAULT);
if ($id) {
    if ($id === user()['id'] && $role !== 'SUPER') back_with('error', '본인의 슈퍼관리자 권한은 변경할 수 없습니다.');
    q_update('users', $row, 'id=?', [$id]);
} else q_insert('users', $row + ['created_at' => now_str()]);
back_with('ok', $id ? '회원이 수정되었습니다.' : '회원이 등록되었습니다.', '/admin/members');

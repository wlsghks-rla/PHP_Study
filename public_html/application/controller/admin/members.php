<?php include_once_path('application/core/admin');
$kw = trim((string) input('kw', ''));
$sizes = page_sizes(); $size = (int) input('size', 10); if (!in_array($size, $sizes)) $size = 10;
$where = $kw !== '' ? " where login_id like ? escape '\\'" : '';
$users = paginate("select * from users$where order by id desc", $kw !== '' ? [like($kw)] : [], (int) input('page', 1), $size);
render('admin/members', ['title' => '회원 관리', 'users' => $users, 'kw' => $kw, 'size' => $size, 'sizes' => $sizes]);

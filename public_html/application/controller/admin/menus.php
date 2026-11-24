<?php include_once_path('application/core/admin');
$menus = q_all('select * from menus where deleted=0 order by sort, id');
$perms = [];
foreach (q_all('select * from menu_permissions') as $p) $perms[$p['menu_id']][] = $p['access_level'];
render('admin/menus', ['title' => '메뉴 관리', 'menus' => $menus, 'perms' => $perms]);

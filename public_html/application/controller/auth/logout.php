<?php
$_SESSION = [];
session_destroy();
session_start();
$_SESSION['csrf'] = bin2hex(random_bytes(16));
flash('ok', '로그아웃되었습니다.');
redirect('/');

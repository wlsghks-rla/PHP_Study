<?php
$level = level(); $menus = visible_menus(); $cur = $curMenu ?? null; $u = user();
?><!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf()) ?>">
<title><?= e($title ?? 'PHP Study 게시판') ?></title>
<link rel="stylesheet" href="/css/app.css">
<?= $head ?? '' ?>
</head>
<body>
<header class="top">
  <div class="wrap row">
    <a class="logo" href="/">PHP STUDY</a>
    <form class="sr" action="/search/index" method="get"><input type="text" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="통합 검색 (제목)" maxlength="100"><button>검색</button></form>
    <div class="auth">
      <?php if ($u): ?>
        <span><?= e($u['login_id']) ?>님 <?php if (is_super()): ?><small>(슈퍼관리자)</small><?php endif ?></span>
        <form method="post" action="/auth/logout"><input type="hidden" name="_token" value="<?= e(csrf()) ?>"><button>로그아웃</button></form>
      <?php else: ?><a href="/auth/login">로그인</a><a href="/auth/register">회원가입</a><?php endif ?>
    </div>
  </div>
  <nav class="menu"><div class="wrap">
    <?php foreach ($menus as $m): ?>
      <?php if ($m['type'] === 'LINK'): ?>
        <a href="/menu/go?menu=<?= $m['id'] ?>" <?= $m['new_window'] ? 'target="_blank" rel="noopener"' : '' ?>><?= e($m['name']) ?></a>
      <?php else: ?>
        <a href="/board/list?menu=<?= $m['id'] ?>" class="<?= $cur && $cur['id'] == $m['id'] ? 'on' : '' ?>"><?= e($m['name']) ?></a>
      <?php endif ?>
    <?php endforeach ?>
    <?php if ($level === 'SUPER'): ?>
      <span class="sep"></span>
      <a class="adm" href="/admin/members">회원관리</a><a class="adm" href="/admin/menus">메뉴관리</a><a class="adm" href="/admin/surveys">설문관리</a>
      <a class="adm" href="/admin/codes">공통코드</a><a class="adm" href="/admin/stats_menu">메뉴통계</a><a class="adm" href="/admin/stats_views">조회통계</a>
    <?php endif ?>
  </div></nav>
</header>
<main><div class="wrap">
<?php if ($m = flash('ok')): ?><div class="flash ok"><?= e($m) ?></div><?php endif ?>
<?php if ($m = flash('error')): ?><div class="flash err"><?= e($m) ?></div><?php endif ?>
<?php if ($errs = flash('errors')): foreach (explode("\n", $errs) as $m): ?><div class="flash err"><?= e($m) ?></div><?php endforeach; endif ?>

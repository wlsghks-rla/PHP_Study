<div class="card"><h2><?= e($s['title']) ?></h2>
<div class="mut">참여기간 <?= e(substr($s['start_dt'], 0, 16)) ?> ~ <?= e(substr($s['end_dt'], 0, 16)) ?> · <?= $s['login_required'] ? '로그인 필요' : '비회원 참여 가능' ?> · <?= $s['allow_duplicate'] ? '중복참여 가능' : '1회만 참여 가능' ?></div>
<div style="white-space:pre-line;margin:16px 0;line-height:1.7"><?= e($s['description']) ?></div>
<?php if ($block): ?><div class="flash err"><?= e($block) ?></div><?php endif ?>
<p style="text-align:center"><a href="/survey/list" class="btn gray">목록</a> <a class="btn" href="/survey/page?id=<?= $s['id'] ?>&n=1" <?= $block ? 'onclick="alert(' . e(json_encode($block, JSON_UNESCAPED_UNICODE)) . ');return false;"' : '' ?>>설문 시작</a></p></div>

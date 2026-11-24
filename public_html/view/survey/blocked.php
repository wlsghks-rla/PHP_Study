<div class="card"><h2><?= e($s['title']) ?></h2><div class="flash err"><?= e($msg) ?></div>
<?php if (!user()): ?><a class="btn" href="/auth/login">로그인</a><?php endif ?> <a class="btn gray" href="/survey/list">목록으로</a></div>

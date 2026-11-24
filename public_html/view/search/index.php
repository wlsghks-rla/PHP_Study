<h2>통합 검색 <?php if ($q !== ''): ?><small class="mut">"<?= e($q) ?>"</small><?php endif ?></h2>
<?php if ($q === ''): ?><div class="card">검색어를 입력하세요.</div>
<?php else: ?>
<div class="card"><h3>게시글 (<?= $posts['total'] ?>)</h3>
  <?php foreach ($posts['rows'] as $b): ?>
    <div style="margin:8px 0"><?php if ($b['allowed']): ?><a href="/board/list?menu=<?= $b['menu_id'] ?>&open=<?= $b['id'] ?>"><?= e($b['title']) ?></a><?php else: ?><span class="mut">🔒 <?= e($b['title']) ?> (열람 권한 없음)</span><?php endif ?>
    <span class="mut">· <?= e($b['menu_name']) ?> · <?= e($b['writer_name']) ?> · <?= e(substr($b['created_at'], 0, 10)) ?></span></div>
  <?php endforeach; if (!$posts['rows']): ?><div class="mut">검색 결과가 없습니다.</div><?php endif ?>
  <?= pager($posts, 'pp') ?>
</div>
<div class="card"><h3>설문조사 (<?= $surveys['total'] ?>)</h3>
  <?php foreach ($surveys['rows'] as $s): ?><div style="margin:8px 0"><a href="/survey/intro?id=<?= $s['id'] ?>"><?= e($s['title']) ?></a> <span class="mut"><?= e(substr($s['start_dt'], 0, 10)) ?> ~ <?= e(substr($s['end_dt'], 0, 10)) ?></span></div>
  <?php endforeach; if (!$surveys['rows']): ?><div class="mut">검색 결과가 없습니다.</div><?php endif ?>
  <?= pager($surveys, 'sp') ?>
</div>
<?php endif ?>

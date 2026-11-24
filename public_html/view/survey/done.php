<div class="card"><h2><?= e($s['title']) ?> <small class="mut">— 응답 완료</small></h2>
<div class="flash ok">설문에 참여해 주셔서 감사합니다. 제출한 응답은 아래와 같습니다.</div>
<?php foreach ($all as $q): $a = $ans[$q['id']] ?? []; $os = []; foreach ($opts[$q['id']] ?? [] as $o) $os[$o['id']] = $o; ?>
  <div class="q"><div class="qt"><?= $q['no'] ?>. <?= e($q['text']) ?></div>
    <?php if ($q['type'] === 'TEXT'): ?><div><?= e($a[0]['answer_text'] ?? '(응답 없음)') ?></div>
    <?php else: foreach ($a as $x): ?><div>✔ <?= e($os[$x['option_id']]['text'] ?? '') ?> <?php if ($x['answer_text']): ?><span class="mut">(<?= e($x['answer_text']) ?>)</span><?php endif ?></div>
    <?php endforeach; if (!$a): ?><div class="mut">(응답 없음)</div><?php endif; endif ?>
  </div>
<?php endforeach ?>
<p style="text-align:center"><a class="btn gray" href="/survey/list">목록</a>
<?php if ($editable): ?><form method="post" action="/survey/reopen?id=<?= $s['id'] ?>" style="display:inline"><input type="hidden" name="_token" value="<?= e(csrf()) ?>"><button class="btn line">참여 기간 내 수정</button></form><?php endif ?></p></div>

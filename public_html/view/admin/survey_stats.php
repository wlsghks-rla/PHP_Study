<h2>설문 통계 — <?= e($s['title']) ?></h2><p><a href="/admin/surveys">← 설문 목록</a></p>
<div class="card">전체 참여자수 <b><?= $participants ?></b>명 (회원 <?= $members ?>명)</div>
<div class="card"><strong>분류별 선택 개수</strong><table><thead><tr><th>분류</th><th>선택 개수</th></tr></thead><tbody><?php foreach ($byCat as $k => $v): ?><tr><td><?= e($k) ?></td><td><?= $v ?></td></tr><?php endforeach ?></tbody></table></div>
<?php foreach ($qs as $i => $q): ?>
<div class="card"><strong><?= $i + 1 ?>. <?= e($q['text']) ?></strong> <span class="mut">[<?= e($q['category_name'] ?? '미분류') ?>]</span>
<?php if ($q['type'] === 'TEXT'): ?><div class="mut">서술형 응답 <?= array_sum($counts[$q['id']] ?? []) ?>건</div>
<?php else: ?><table><thead><tr><th>선택지</th><th width="100">선택 수</th></tr></thead><tbody>
<?php foreach ($opts[$q['id']] ?? [] as $o): ?><tr><td><?= e($o['text']) ?></td><td><?= $counts[$q['id']][(int) $o['id']] ?? 0 ?></td></tr><?php endforeach ?></tbody></table><?php endif ?></div>
<?php endforeach ?>

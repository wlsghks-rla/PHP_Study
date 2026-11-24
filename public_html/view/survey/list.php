<h2>설문조사</h2>
<table><thead><tr><th>설문조사</th><th width="260">참여기간</th><th width="90">상태</th></tr></thead><tbody>
<?php $now = now_str(); foreach ($surveys as $s): ?>
<tr><td><a href="/survey/intro?id=<?= $s['id'] ?>"><?= e($s['title']) ?></a></td><td><?= e(substr($s['start_dt'], 0, 16)) ?> ~ <?= e(substr($s['end_dt'], 0, 16)) ?></td>
<td><?= $now < $s['start_dt'] ? '진행 예정' : ($now > $s['end_dt'] ? '종료' : '진행중') ?></td></tr>
<?php endforeach; if (!$surveys): ?><tr><td colspan="3">등록된 설문조사가 없습니다.</td></tr><?php endif ?>
</tbody></table>

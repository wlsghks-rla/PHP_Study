<h2>문항 관리 — <?= e($s['title']) ?></h2><p><a href="/admin/surveys">← 설문 목록</a></p>
<?php
$catSel = function ($cur = null) use ($cats) { $h = '<select name="category_id"><option value="">분류 없음</option>'; foreach ($cats as $c) $h .= '<option value="' . $c['id'] . '" ' . sel($cur == $c['id']) . '>' . e($c['name']) . '</option>'; return $h . '</select>'; };
?>
<div class="card"><strong>문항 추가</strong>
<form method="post" action="/admin/question_save" style="margin-top:8px"><?= token_field() ?><input type="hidden" name="survey_id" value="<?= $s['id'] ?>">
 <textarea name="text" rows="2" placeholder="문항 질문" required></textarea>
 <div class="form-row" style="margin:6px 0"><select name="type"><option value="CHOICE">선택형</option><option value="TEXT">서술형</option></select>
 <?= $catSel() ?>
 순번 <input type="number" name="sort" value="<?= count($qs) + 1 ?>" style="width:70px"> 최대선택 <input type="number" name="max_count" value="1" min="1" style="width:60px">
 <label><input type="checkbox" name="required" value="1" checked> 필수</label></div>
 <textarea name="options" rows="4" placeholder="선택지를 줄바꿈으로 구분. 앞에 [없음] / [기타] 를 붙이면 특수 선택지"></textarea><button class="btn" style="margin-top:6px">추가</button></form></div>
<?php foreach ($qs as $q): ?>
<div class="card"><form method="post" action="/admin/question_save"><?= token_field() ?><input type="hidden" name="survey_id" value="<?= $s['id'] ?>"><input type="hidden" name="qid" value="<?= $q['id'] ?>">
 <textarea name="text" rows="2"><?= e($q['text']) ?></textarea>
 <div class="form-row" style="margin:6px 0"><select name="type"><option value="CHOICE" <?= sel($q['type'] === 'CHOICE') ?>>선택형</option><option value="TEXT" <?= sel($q['type'] === 'TEXT') ?>>서술형</option></select>
 <?= $catSel($q['category_id']) ?>
 순번 <input type="number" name="sort" value="<?= $q['sort'] ?>" style="width:70px"> 최대선택 <input type="number" name="max_count" value="<?= $q['max_count'] ?>" min="1" style="width:60px">
 <label><input type="checkbox" name="required" value="1" <?= chk($q['required']) ?>> 필수</label><label><input type="checkbox" name="use_yn" value="1" <?= chk($q['use_yn']) ?>> 사용</label></div>
 <textarea name="options" rows="4"><?= e(implode("\n", array_map(fn ($o) => ($o['is_none'] ? '[없음]' : ($o['is_etc'] ? '[기타]' : '')) . $o['text'], $opts[$q['id']] ?? []))) ?></textarea>
 <button class="btn sm" style="margin-top:6px">저장</button></form>
 <form method="post" action="/admin/question_delete" onsubmit="return confirm('문항과 응답이 삭제됩니다.')" style="margin-top:6px"><?= token_field() ?><input type="hidden" name="survey_id" value="<?= $s['id'] ?>"><input type="hidden" name="qid" value="<?= $q['id'] ?>"><button class="btn sm danger">삭제</button></form></div>
<?php endforeach ?>

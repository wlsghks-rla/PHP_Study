<div class="card" style="position:sticky;top:0;z-index:5">
  <strong><?= e($s['title']) ?></strong> <span class="mut"><?= $n ?> / <?= $pages ?> 페이지</span>
  <div class="bar" style="margin:8px 0 4px"><i id="barFill" style="width:0%"></i></div>
  <div class="mut">진행률 <b id="barTxt">0</b>% (필수 문항 기준)</div>
</div>
<form id="sv" data-id="<?= $s['id'] ?>" data-n="<?= $n ?>" data-req-total="<?= $reqTotal ?>" data-req-other="<?= $reqDoneOther ?>" onsubmit="return false">
<?php foreach ($qs as $q):
  $a = $ans[$q['id']] ?? []; $sel = array_filter(array_column($a, 'option_id')); $etc = ''; foreach ($a as $x) if ($x['answer_text'] !== null && $x['answer_text'] !== '') $etc = $x['answer_text']; ?>
  <div class="q" data-qid="<?= $q['id'] ?>" data-type="<?= e($q['type']) ?>" data-req="<?= $q['required'] ? 1 : 0 ?>" data-max="<?= $q['max_count'] ?>" data-no="<?= $q['no'] ?>">
    <div class="qt"><?= $q['no'] ?>. <?= e($q['text']) ?> <?= $q['required'] ? '<span style="color:#d93a3a">*</span>' : '<span class="mut">(선택)</span>' ?>
      <?php if ($q['category_name']): ?><span class="mut">[<?= e($q['category_name']) ?>]</span><?php endif ?></div>
    <?php if ($q['type'] === 'TEXT'): ?>
      <textarea rows="3" maxlength="1000" data-text><?= e($etc) ?></textarea>
    <?php else: $hasEtc = false; foreach ($opts[$q['id']] ?? [] as $o): if ($o['is_etc']) $hasEtc = true; ?>
      <label class="opt"><input type="<?= $q['max_count'] > 1 ? 'checkbox' : 'radio' ?>" name="q<?= $q['id'] ?>" value="<?= $o['id'] ?>" data-none="<?= $o['is_none'] ? 1 : 0 ?>" data-etc="<?= $o['is_etc'] ? 1 : 0 ?>" <?= in_array($o['id'], $sel) ? 'checked' : '' ?>> <?= e($o['text']) ?></label>
    <?php endforeach; if ($hasEtc): ?><input type="text" data-text maxlength="255" placeholder="기타 내용 입력" value="<?= e($etc) ?>" style="width:60%"><?php endif; endif ?>
  </div>
<?php endforeach ?>
</form>
<p style="text-align:center">
  <?php if ($n > 1): ?><button class="btn gray" id="btnPrev">이전</button><?php endif ?>
  <?php if ($n < $pages): ?><button class="btn" id="btnNext">다음</button><?php else: ?><button class="btn" id="btnDone">설문완료</button><?php endif ?>
</p>

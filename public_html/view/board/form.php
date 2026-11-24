<?php $gallery = $m['type'] === 'GALLERY'; ?>
<button class="x" data-close>×</button>
<h3><?= $b ? '글 수정' : ($parent ? '답글 작성' : '글 작성') ?> <span class="mut">(<?= e($m['name']) ?>)</span></h3>
<form id="postForm" enctype="multipart/form-data" data-action="/board/save?menu=<?= $m['id'] ?>" data-id="<?= $b['id'] ?? '' ?>" data-ext="<?= e(implode(',', $cfg['ext'])) ?>" data-mb="<?= $cfg['mb'] ?>" data-gallery="<?= $gallery ? 1 : 0 ?>" novalidate>
  <?php if ($parent): ?><input type="hidden" name="parent_id" value="<?= $parent['id'] ?>"><div class="mut">원글: <?= e($parent['title']) ?></div><?php endif ?>
  <label class="f">제목 <span class="mut">(100자 이내)</span></label>
  <input type="text" name="title" maxlength="100" style="width:100%" value="<?= e($b['title'] ?? ($parent ? 'RE: ' . $parent['title'] : '')) ?>">
  <label class="f">내용 <span class="mut">(4000자 이내)</span></label>
  <div id="editor" style="height:240px"><?= $b['content'] ?? '' ?></div>
  <label class="f">해시태그 <span class="mut">(최대 5개, 공백/쉼표로 구분, 특수문자 불가)</span></label>
  <input type="text" name="hashtags" style="width:100%" value="<?= e(implode(' ', array_map(fn ($t) => '#' . $t, $tags))) ?>" placeholder="#여행 #맛집">
  <label class="f"><?= e($cfg['label']) ?> 첨부 <span class="mut">(<?= strtoupper(e(implode(', ', $cfg['ext']))) ?> / 개당 <?= $cfg['mb'] ?>MB / 최대 5개)</span></label>
  <input type="file" id="fileInput" multiple accept="<?= e('.' . implode(',.', $cfg['ext'])) ?>">
  <div id="fileList" style="margin-top:8px">
    <?php foreach ($files as $f): ?>
      <div class="exist" data-id="<?= $f['id'] ?>" style="display:flex;gap:8px;align-items:center;margin:4px 0">
        <?php if ($gallery): ?><img src="/board/thumb?f=<?= e($f['stored_name']) ?>" style="width:60px;height:46px;object-fit:cover;border-radius:4px"><label><input type="radio" name="thumb_pick" value="e<?= $f['id'] ?>" <?= $f['is_thumbnail'] ? 'checked' : '' ?>> 대표</label><?php endif ?>
        <span><?= e($f['origin_name']) ?></span><button type="button" class="btn sm gray rm-exist">삭제</button>
      </div>
    <?php endforeach ?>
  </div>
  <?php if ($gallery): ?><div class="mut">대표 썸네일을 선택하지 않으면 첫 번째 이미지가 썸네일이 됩니다.</div><?php endif ?>
  <?php if (is_super() && $m['type'] === 'BOARD' && !$parent): ?>
    <label class="f"><input type="checkbox" name="is_notice" value="1" id="isNotice" <?= $b && $b['is_notice'] ? 'checked' : '' ?>> 공지사항 (목록 최상단 / 메인 레이어 팝업 노출)</label>
    <div class="form-row" id="noticeDates" style="<?= $b && $b['is_notice'] ? '' : 'display:none' ?>">
      시작 <input type="datetime-local" name="notice_start" value="<?= e($b && $b['notice_start'] ? str_replace(' ', 'T', substr($b['notice_start'], 0, 16)) : date('Y-m-d\TH:i')) ?>">
      종료 <input type="datetime-local" name="notice_end" value="<?= e($b && $b['notice_end'] ? str_replace(' ', 'T', substr($b['notice_end'], 0, 16)) : date('Y-m-d\TH:i', strtotime('+7 days'))) ?>">
    </div>
  <?php endif ?>
  <p style="text-align:right"><button type="button" class="btn gray" data-close>취소</button> <button type="submit" class="btn"><?= $b ? '수정 저장' : '등록' ?></button></p>
</form>

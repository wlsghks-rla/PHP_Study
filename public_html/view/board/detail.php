<?php $gallery = $m['type'] === 'GALLERY'; ?>
<button class="x" data-close>×</button>
<h3><?php if ($b['is_notice']): ?><span class="badge">공지</span><?php endif ?><?= e($b['title']) ?></h3>
<div class="mut">작성자 <?= e($b['writer_name']) ?> · <?= e($b['created_at']) ?> · 조회 <?= $b['view_count'] ?></div>
<div style="margin:8px 0"><?php foreach ($tags as $t): ?><button type="button" class="tag" data-tag="<?= e($t) ?>">#<?= e($t) ?></button><?php endforeach ?></div>
<?php if ($gallery && $files): ?>
  <div class="thumbs"><?php foreach ($files as $f): ?>
    <div class="th"><a href="/board/file?f=<?= e($f['stored_name']) ?>" title="클릭하면 원본 다운로드"><img src="/board/thumb?f=<?= e($f['stored_name']) ?>" alt="<?= e($f['origin_name']) ?>"></a><?php if ($f['is_thumbnail']): ?><div class="mut">대표 썸네일</div><?php endif ?></div>
  <?php endforeach ?></div>
<?php endif ?>
<div class="content"><?= $b['content'] ?></div>
<?php if ($files): ?>
<strong>첨부파일</strong>
<ul class="files"><?php foreach ($files as $f): ?>
  <li><a href="/board/file?f=<?= e($f['stored_name']) ?>">📎 <?= e($f['origin_name']) ?></a><span class="mut">(<?= number_format($f['file_size'] / 1024, 1) ?>KB)</span><span class="mut" style="margin-left:auto">다운로드 <?= $f['downloads'] ?>회</span></li>
<?php endforeach ?></ul>
<?php endif ?>
<div class="toolbar" style="margin-top:12px">
  <button class="btn line" id="btnLike"><?= $liked ? '♥' : '♡' ?> 좋아요 <b><?= $likes ?></b></button><span class="sp"></span>
  <?php if (user() && $m['type'] === 'BOARD'): ?><button class="btn gray" id="btnReply">답글</button><?php endif ?>
  <?php if ($canEdit): ?><button class="btn gray" id="btnEdit">수정</button><button class="btn danger" id="btnDelOne">삭제</button><?php endif ?>
</div>
<h4>댓글 <?= count(array_filter($comments, fn ($c) => !$c['deleted'])) ?></h4>
<div id="comments">
<?php foreach ($comments as $c): ?>
  <div class="cm <?= $c['deleted'] ? 'del' : '' ?>" style="margin-left:<?= min($c['depth'], 6) * 22 ?>px" data-id="<?= $c['id'] ?>">
    <?php if ($c['deleted']): ?>삭제된 댓글입니다.
    <?php else: ?>
      <div class="meta"><?= e($c['writer_name']) ?> · <?= e($c['created_at']) ?></div>
      <div class="ctext"><?= e($c['content']) ?></div>
      <div>
        <?php if (user()): ?><a href="#" class="c-reply">답글</a><?php endif ?>
        <?php if (user() && (is_super() || user()['id'] === (int) $c['writer'])): ?> · <a href="#" class="c-edit">수정</a> · <a href="#" class="c-del">삭제</a><?php endif ?>
      </div>
    <?php endif ?>
  </div>
<?php endforeach; if (!$comments): ?><div class="mut" style="padding:8px 0">첫 댓글을 남겨 보세요.</div><?php endif ?>
</div>
<?php if (user()): ?>
<div style="margin-top:10px"><textarea id="newComment" rows="2" maxlength="500" placeholder="댓글 입력 (500자 이내)"></textarea><button class="btn" id="btnComment" style="margin-top:6px">댓글 등록</button></div>
<?php else: ?><p class="mut">댓글은 로그인 후 작성할 수 있습니다.</p><?php endif ?>

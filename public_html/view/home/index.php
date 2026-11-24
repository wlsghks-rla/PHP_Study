<div class="card"><h2>환영합니다</h2>
<p>상단 메뉴에서 게시판, 갤러리, 설문조사를 이용하세요.</p>
</div>
<?php if ($popup): ?>
<div class="overlay" id="noticePop"><div class="modal">
  <button class="x" onclick="document.getElementById('noticePop').remove()">×</button>
  <h3>📢 공지사항</h3>
  <ul><?php foreach ($popup as $n): ?><li style="margin:8px 0"><a href="/board/list?menu=<?= $n['menu_id'] ?>&open=<?= $n['id'] ?>"><?= e($n['title']) ?></a> <span class="mut">~ <?= e(substr($n['notice_end'], 0, 16)) ?></span></li><?php endforeach ?></ul>
  <button class="btn gray" onclick="document.getElementById('noticePop').remove()">닫기</button>
</div></div>
<?php endif ?>

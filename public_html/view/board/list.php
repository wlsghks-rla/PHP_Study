<?php $base = '/board/list?menu=' . $menu['id']; ?>
<h2><?= e($menu['name']) ?> <?php if ($tag !== ''): ?><span class="tag" onclick="location.href='<?= $base ?>'">#<?= e($tag) ?> ✕</span><?php endif ?></h2>
<div id="board" data-menu="<?= $menu['id'] ?>" data-type="<?= e($menu['type']) ?>" data-open="<?= e($_GET['open'] ?? '') ?>">
<form class="toolbar" method="get" action="/board/list">
  <input type="hidden" name="menu" value="<?= $menu['id'] ?>">
  <select name="field"><?php foreach (['all' => '전체', 'title' => '제목', 'content' => '내용', 'writer' => '작성자'] as $k => $v): ?><option value="<?= $k ?>" <?= $field === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach ?></select>
  <input type="text" name="kw" value="<?= e($kw) ?>" placeholder="검색어" maxlength="50">
  <?php if ($tag !== ''): ?><input type="hidden" name="tag" value="<?= e($tag) ?>"><?php endif ?>
  <select name="size" onchange="this.form.submit()"><?php foreach ($sizes as $s): ?><option value="<?= $s ?>" <?= $size === $s ? 'selected' : '' ?>><?= $s ?>개씩</option><?php endforeach ?></select>
  <select name="sort" onchange="this.form.submit()"><option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>최신순</option><option value="likes" <?= $sort === 'likes' ? 'selected' : '' ?>>좋아요순</option></select>
  <?php if (user()): ?><label><input type="checkbox" name="mine" value="1" <?= $mine ? 'checked' : '' ?> onchange="this.form.submit()"> 내가 쓴 글</label><?php endif ?>
  <button class="btn">검색</button><span class="sp"></span>
  <?php if (user()): ?><button type="button" class="btn" id="btnWrite">글쓰기</button><button type="button" class="btn danger" id="btnDel">선택 삭제</button><?php endif ?>
</form>

<?php if ($gallery): ?>
  <div class="grid">
  <?php foreach ($posts['rows'] as $p): ?>
    <div class="gitem" data-id="<?= $p['id'] ?>">
      <?php if (user()): ?><input type="checkbox" class="ck chk" value="<?= $p['id'] ?>"><?php endif ?>
      <div class="im" style="background-image:url('<?= $p['thumb'] ? '/board/thumb?f=' . e($p['thumb']) : '' ?>')"></div>
      <div class="tx"><div class="t"><?= mark($p['title'], in_array($field, ['all', 'title']) ? $kw : null) ?></div>
        <div class="mut"><?= e($p['writer_name']) ?> · <?= e(substr($p['created_at'], 0, 10)) ?></div>
        <div class="mut">♥ <?= $p['likes'] ?> · 👁 <?= $p['view_count'] ?> · 💬 <?= $p['comment_cnt'] ?> · 🖼 <?= $p['img_cnt'] ?></div>
        <div><?php foreach ($tags[$p['id']] ?? [] as $t): ?><button type="button" class="tag" data-tag="<?= e($t) ?>">#<?= e($t) ?></button><?php endforeach ?></div>
      </div>
    </div>
  <?php endforeach; if (!$posts['rows']): ?><div class="card">등록된 게시글이 없습니다.</div><?php endif ?>
  </div>
<?php else: ?>
  <table>
    <thead><tr><?php if (user()): ?><th width="30"></th><?php endif ?><th width="60">번호</th><th>제목</th><th width="110">작성자</th><th width="100">작성일</th><th width="60">조회</th><th width="60">좋아요</th></tr></thead>
    <tbody>
    <?php foreach ($notices as $p): ?>
      <tr class="notice" data-id="<?= $p['id'] ?>"><?php if (user()): ?><td><input type="checkbox" class="chk" value="<?= $p['id'] ?>"></td><?php endif ?>
        <td><span class="badge">공지</span></td><td class="ti"><a href="#" class="open"><?= e($p['title']) ?></a></td><td><?= e($p['writer_name']) ?></td><td><?= e(substr($p['created_at'], 0, 10)) ?></td><td><?= $p['view_count'] ?></td><td><?= $p['likes'] ?></td></tr>
    <?php endforeach ?>
    <?php foreach ($posts['rows'] as $p): ?>
      <tr data-id="<?= $p['id'] ?>"><?php if (user()): ?><td><input type="checkbox" class="chk" value="<?= $p['id'] ?>"></td><?php endif ?>
        <td><?= $p['id'] ?></td>
        <td class="ti" style="padding-left:<?= 8 + $p['depth'] * 20 ?>px"><?= $p['depth'] ? '↳ ' : '' ?><a href="#" class="open"><?= mark($p['title'], in_array($field, ['all', 'title']) ? $kw : null) ?></a>
          <?= $p['file_cnt'] ? '📎' : '' ?> <?php if ($p['comment_cnt']): ?><span class="mut">[<?= $p['comment_cnt'] ?>]</span><?php endif ?>
          <?php foreach ($tags[$p['id']] ?? [] as $t): ?><button type="button" class="tag" data-tag="<?= e($t) ?>">#<?= e($t) ?></button><?php endforeach ?></td>
        <td><?= mark($p['writer_name'], in_array($field, ['all', 'writer']) ? $kw : null) ?></td><td><?= e(substr($p['created_at'], 0, 10)) ?></td><td><?= $p['view_count'] ?></td><td><?= $p['likes'] ?></td></tr>
    <?php endforeach; if (!$posts['rows'] && !$notices): ?><tr><td colspan="7" style="text-align:center;padding:30px">등록된 게시글이 없습니다.</td></tr><?php endif ?>
    </tbody>
  </table>
<?php endif ?>
<?= pager($posts) ?>
</div>

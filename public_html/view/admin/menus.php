<h2>메뉴 관리</h2>
<p class="mut">일반형/갤러리형을 추가하면 새 게시판이 생성되고, 링크형은 내부(/intro/index) 또는 외부(https://naver.com) 주소로 이동합니다. 권한: 체크한 대상만 메뉴를 볼 수 있습니다.</p>
<div class="card"><strong>메뉴 추가</strong>
<form method="post" action="/admin/menu_save" class="form-row" style="margin-top:8px"><?= token_field() ?>
  <input type="text" name="name" placeholder="메뉴명" maxlength="50" required>
  <select name="type"><option value="BOARD">일반형 게시판</option><option value="GALLERY">갤러리형 게시판</option><option value="LINK">링크형</option></select>
  <input type="text" name="url" placeholder="링크 주소(링크형)" style="width:220px">
  <input type="number" name="sort" value="0" style="width:70px" title="정렬순서">
  <label><input type="checkbox" name="new_window" value="1"> 새창</label>
  <label><input type="checkbox" name="levels[]" value="GUEST" checked> 비회원</label><label><input type="checkbox" name="levels[]" value="USER" checked> 일반회원</label>
  <button class="btn">추가</button></form></div>
<table><thead><tr><th>ID</th><th>메뉴명</th><th>유형</th><th>링크</th><th>순서</th><th>새창</th><th>사용</th><th>권한</th><th>관리</th></tr></thead><tbody>
<?php foreach ($menus as $m): $lv = $perms[$m['id']] ?? []; ?>
<tr><form method="post" action="/admin/menu_save"><?= token_field() ?><input type="hidden" name="id" value="<?= $m['id'] ?>">
 <td><?= $m['id'] ?></td><td><input type="text" name="name" value="<?= e($m['name']) ?>" maxlength="50" style="width:130px"></td>
 <td><select name="type"><?php foreach (['BOARD' => '일반형', 'GALLERY' => '갤러리형', 'LINK' => '링크형'] as $k => $v): ?><option value="<?= $k ?>" <?= sel($m['type'] === $k) ?>><?= $v ?></option><?php endforeach ?></select></td>
 <td><input type="text" name="url" value="<?= e($m['url']) ?>" style="width:150px"></td><td><input type="number" name="sort" value="<?= $m['sort'] ?>" style="width:60px"></td>
 <td><input type="checkbox" name="new_window" value="1" <?= chk($m['new_window']) ?>></td><td><input type="checkbox" name="use_yn" value="1" <?= chk($m['use_yn']) ?>></td>
 <td><label><input type="checkbox" name="levels[]" value="GUEST" <?= chk(in_array('GUEST', $lv)) ?>>비회원</label> <label><input type="checkbox" name="levels[]" value="USER" <?= chk(in_array('USER', $lv)) ?>>일반</label></td>
 <td><button class="btn sm">저장</button></form>
 <form method="post" action="/admin/menu_delete" style="display:inline" onsubmit="return confirm('메뉴를 삭제하시겠습니까?')"><?= token_field() ?><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn sm danger">삭제</button></form></td></tr>
<?php endforeach ?></tbody></table>

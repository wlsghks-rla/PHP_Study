<h2>회원 관리</h2>
<form class="toolbar" method="get"><input type="text" name="kw" value="<?= e($kw) ?>" placeholder="아이디 검색"><select name="size" onchange="this.form.submit()"><?php foreach ($sizes as $s): ?><option <?= sel($size === $s) ?>><?= $s ?></option><?php endforeach ?></select><button class="btn">검색</button></form>
<div class="card"><strong>회원 등록</strong>
<form method="post" action="/admin/member_save" class="form-row" style="margin-top:8px"><?= token_field() ?>
  <input type="text" name="login_id" placeholder="아이디(영문·숫자 4~20)" maxlength="20" required>
  <input type="password" name="password" placeholder="비밀번호(4~50)" maxlength="50" required>
  <select name="role"><option value="USER">일반회원</option><option value="SUPER">슈퍼관리자</option></select><button class="btn">등록</button></form></div>
<table><thead><tr><th>번호</th><th>아이디</th><th>비밀번호(변경 시)</th><th>권한</th><th>가입일</th><th width="140">관리</th></tr></thead><tbody>
<?php foreach ($users['rows'] as $u): ?>
<tr><form method="post" action="/admin/member_save"><?= token_field() ?><input type="hidden" name="id" value="<?= $u['id'] ?>">
  <td><?= $u['id'] ?></td><td><input type="text" name="login_id" value="<?= e($u['login_id']) ?>" maxlength="20"></td>
  <td><input type="password" name="password" placeholder="변경 시에만 입력" maxlength="50"></td>
  <td><select name="role"><option value="USER" <?= sel($u['role'] === 'USER') ?>>일반회원</option><option value="SUPER" <?= sel($u['role'] === 'SUPER') ?>>슈퍼관리자</option></select></td>
  <td><?= e(substr($u['created_at'], 0, 10)) ?></td>
  <td><button class="btn sm">수정</button></form>
  <form method="post" action="/admin/member_delete" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')"><?= token_field() ?><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="btn sm danger">삭제</button></form></td></tr>
<?php endforeach ?>
</tbody></table>
<?= pager($users) ?>

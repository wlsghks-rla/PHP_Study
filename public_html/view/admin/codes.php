<h2>공통코드 관리</h2>
<p class="mut">PAGE_SIZE: 목록 페이지 크기 선택지 / TOP_N: 조회수 TOP N의 N값(가장 작은 코드값 1개 사용)</p>
<div class="card"><form method="post" action="/admin/code_save" class="form-row"><?= token_field() ?>
 <input type="text" name="code_kind" placeholder="분류(PAGE_SIZE)" required><input type="text" name="code_key" placeholder="코드값" required><input type="text" name="code_name" placeholder="코드명" required><button class="btn">추가</button></form></div>
<table><thead><tr><th>분류</th><th>코드값</th><th>코드명</th><th>관리</th></tr></thead><tbody>
<?php foreach ($codes as $c): ?><tr><form method="post" action="/admin/code_save"><?= token_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>">
 <td><input type="text" name="code_kind" value="<?= e($c['code_kind']) ?>"></td><td><input type="text" name="code_key" value="<?= e($c['code_key']) ?>"></td><td><input type="text" name="code_name" value="<?= e($c['code_name']) ?>"></td>
 <td><button class="btn sm">수정</button></form><form method="post" action="/admin/code_delete" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')"><?= token_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn sm danger">삭제</button></form></td></tr><?php endforeach ?>
</tbody></table>

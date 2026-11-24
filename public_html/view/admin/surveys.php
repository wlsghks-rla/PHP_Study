<h2>설문조사 관리</h2>
<div class="card"><strong>설문조사 등록</strong>
<form method="post" action="/admin/survey_save" style="margin-top:8px"><?= token_field() ?>
 <div class="form-row"><input type="text" name="title" placeholder="설문조사명" maxlength="200" style="width:300px" required>
 <input type="datetime-local" name="start_dt" value="<?= date('Y-m-d\T00:00') ?>" required> ~ <input type="datetime-local" name="end_dt" value="<?= date('Y-m-d\T23:59', strtotime('+1 month')) ?>" required>
 한 페이지 <input type="number" name="per_page" value="5" min="1" max="50" style="width:60px">문항
 <label><input type="checkbox" name="login_required" value="1" checked> 로그인 필요</label><label><input type="checkbox" name="allow_duplicate" value="1"> 중복참여 가능</label></div>
 <textarea name="description" rows="2" placeholder="안내문" style="margin-top:8px"></textarea><button class="btn" style="margin-top:6px">등록</button></form></div>
<?php foreach ($surveys as $s): ?>
<div class="card"><form method="post" action="/admin/survey_save"><?= token_field() ?><input type="hidden" name="id" value="<?= $s['id'] ?>">
 <div class="form-row"><b>#<?= $s['id'] ?></b><input type="text" name="title" value="<?= e($s['title']) ?>" maxlength="200" style="width:260px">
 <input type="datetime-local" name="start_dt" value="<?= e(dt($s['start_dt'])) ?>"> ~ <input type="datetime-local" name="end_dt" value="<?= e(dt($s['end_dt'])) ?>">
 <input type="number" name="per_page" value="<?= $s['per_page'] ?>" min="1" max="50" style="width:60px">문항/페이지
 <label><input type="checkbox" name="login_required" value="1" <?= chk($s['login_required']) ?>> 로그인</label><label><input type="checkbox" name="allow_duplicate" value="1" <?= chk($s['allow_duplicate']) ?>> 중복참여</label><label><input type="checkbox" name="use_yn" value="1" <?= chk($s['use_yn']) ?>> 사용</label></div>
 <textarea name="description" rows="2" style="margin-top:6px"><?= e($s['description']) ?></textarea>
 <div style="margin-top:6px"><button class="btn sm">저장</button> <a class="btn sm line" href="/admin/questions?id=<?= $s['id'] ?>">문항 관리</a> <a class="btn sm line" href="/admin/survey_stats?id=<?= $s['id'] ?>">통계</a></div></form>
 <form method="post" action="/admin/survey_delete" onsubmit="return confirm('설문과 응답이 모두 삭제됩니다. 계속할까요?')" style="margin-top:6px"><?= token_field() ?><input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn sm danger">삭제</button></form></div>
<?php endforeach ?>
<div class="card"><strong>문항 분류(카테고리)</strong>
<form method="post" action="/admin/category_save" class="form-row" style="margin:8px 0"><?= token_field() ?><input type="text" name="name" placeholder="분류명" maxlength="50" required><button class="btn sm">추가</button></form>
<?php foreach ($cats as $c): ?><form method="post" action="/admin/category_save" class="form-row" style="margin:4px 0"><?= token_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><input type="text" name="name" value="<?= e($c['name']) ?>" maxlength="50"><button class="btn sm">수정</button></form>
<form method="post" action="/admin/category_delete" style="display:inline" onsubmit="return confirm('삭제하시겠습니까?')"><?= token_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn sm danger">삭제</button></form><?php endforeach ?></div>

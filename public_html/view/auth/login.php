<div class="card" style="max-width:420px;margin:30px auto">
<h2>로그인</h2>
<form method="post" action="/auth/login"><input type="hidden" name="_token" value="<?= e(csrf()) ?>">
  <label class="f">아이디</label><input type="text" name="login_id" style="width:100%" required autofocus>
  <label class="f">비밀번호</label><input type="password" name="password" style="width:100%" required>
  <p><button class="btn">로그인</button> <a href="/auth/register">회원가입</a></p>
</form></div>

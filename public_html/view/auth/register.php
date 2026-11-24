<div class="card" style="max-width:420px;margin:30px auto">
<h2>회원가입</h2>
<form method="post" action="/auth/register" id="regForm"><input type="hidden" name="_token" value="<?= e(csrf()) ?>">
  <label class="f">아이디 <span class="mut">(영문·숫자 4~20자, 특수문자/공백 불가)</span></label>
  <div class="form-row"><input type="text" name="login_id" id="loginId" maxlength="20" style="flex:1" required>
  <button type="button" class="btn line" id="chkBtn">중복확인</button></div>
  <div id="idMsg"></div>
  <label class="f">비밀번호 <span class="mut">(4~50자)</span></label><input type="password" name="password" id="pw" maxlength="50" style="width:100%" required>
  <label class="f">비밀번호 확인</label><input type="password" name="password_confirmation" id="pw2" maxlength="50" style="width:100%" required>
  <p><button class="btn">가입하기</button></p>
</form></div>
<?php $scripts = <<<'JS'
<script>
let idOk=false;
const idEl=document.getElementById('loginId'), msg=document.getElementById('idMsg');
idEl.addEventListener('input',()=>{idEl.value=idEl.value.replace(/\s+/g,'');idOk=false;msg.textContent='';});
async function chk(){
  const r=await (await fetch('/auth/check_id?login_id='+encodeURIComponent(idEl.value))).json();
  msg.className=r.ok?'okm':'err'; msg.textContent=r.msg; idOk=r.ok; return r.ok;
}
document.getElementById('chkBtn').onclick=chk;
document.getElementById('regForm').addEventListener('submit',async e=>{
  e.preventDefault();
  if(!idEl.value){alert('아이디를 입력하세요.');return;}
  if(!idOk && !(await chk())){alert('아이디 중복확인을 해주세요.\n'+msg.textContent);return;}
  const a=document.getElementById('pw').value,b=document.getElementById('pw2').value;
  if(a.length<4||a.length>50){alert('비밀번호는 4~50자로 입력하세요.');return;}
  if(a!==b){alert('비밀번호 확인이 일치하지 않습니다.');return;}
  e.target.submit();
});
</script>
JS; ?>

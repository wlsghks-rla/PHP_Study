<h2>메뉴 접근 통계</h2>
<div class="tabs" id="tabs"><button data-t="year" class="on">연도별</button><button data-t="month">월별</button><button data-t="day">일별</button><button data-t="hour">시간대별</button></div>
<div class="toolbar" id="opts"><label>연도 <input type="number" id="yr" value="<?= date('Y') ?>" style="width:90px"></label><label id="mwrap">월 <select id="mo"><?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>" <?= sel($i == date('n')) ?>><?= $i ?></option><?php endfor ?></select></label>
<label id="dwrap"><input type="checkbox" id="dedupe"> 중복 제거(동일 세션 1회)</label><button class="btn" id="go">조회</button></div>
<div class="card scroll-x"><table id="tbl"></table></div>
<?php $scripts = <<<'JS'
<script>
let type='year';
const $=id=>document.getElementById(id);
function sync(){ $('yr').parentElement.style.display=type==='year'?'none':''; $('mwrap').style.display=(type==='day'||type==='hour')?'':'none'; $('dwrap').style.display=type==='hour'?'':'none'; }
async function load(){
  const q=new URLSearchParams({type,year:$('yr').value,month:$('mo').value,dedupe:$('dedupe').checked?1:0});
  const j=await (await fetch('/admin/stats_menu_data?'+q,{headers:{Accept:'application/json'}})).json();
  const head='<thead><tr><th>메뉴</th>'+j.columns.map(c=>'<th>'+c+(type==='hour'?'시':type==='day'?'일':type==='month'?'월':'년')+'</th>').join('')+'<th>합계</th></tr></thead>';
  const body='<tbody>'+j.rows.map(r=>{const v=j.columns.map(c=>r[c]);return '<tr><td>'+esc(r.menu)+'</td>'+v.map(x=>'<td>'+x+'</td>').join('')+'<td><b>'+v.reduce((a,b)=>a+b,0)+'</b></td></tr>'}).join('')+'</tbody>';
  $('tbl').innerHTML=head+body;
}
const esc=s=>s.replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
document.querySelectorAll('#tabs button').forEach(b=>b.onclick=()=>{document.querySelectorAll('#tabs button').forEach(x=>x.classList.remove('on'));b.classList.add('on');type=b.dataset.t;sync();load();});
$('go').onclick=load; sync(); load();
</script>
JS; ?>

<link href="{VAR:baseurl}/automatweb/css/iaw.css" rel="stylesheet" type="text/css" />

<div id="iaw">
<div id="tab1">
<table class="panes">
<tr>
<!-- left-pane -->
<td id="left_pane" class="left_pane ui-sortable">
<!-- kast -->
<dl class="box">
<dt>
	<span class="left">Minu töölaud</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<table>
<tr>
<td class="aar punane">Kõned</td>
<td class="punane">&nbsp;&nbsp;1</td>
</tr>
<tr>
<td class="aar">Kohtumised</td>
<td>&nbsp;&nbsp;1</td>
</tr>
<tr>
<td class="aar">Tegevused</td>
<td>&nbsp;&nbsp;9</td>
</tr>
<tr>
<td class="aar">Kirjad</td>
<td>&nbsp;&nbsp;2 <img src="/automatweb/images/aw06/ikoon_ymbrik.gif" alt="messages" width="15" height="12" /></td>
</tr>
</table>
</dd>
</dl>
<!-- kast -->
<dl class="box">
<dt>
	<span class="left">Kalender</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<div id="cal">
	<h1></h1>
	
	<div class="minical_table">
<form method="GET" name="mininavigator">
<table>
<tr>
<td><select>
<option value="mida_teeb_1" selected="selected">Sander</option>
<option value="mida_teeb_2">Erki</option>
<option value="mida_teeb_3">Marko</option>
<option value="mida_teeb_4">Hannes</option>
</select></td>
<td>
<select id="minimon" name="minimon" class="formSelect" onChange="mini_navigate()"><option  value='1'>jaanuar</option>
<option  value='2'>veebruar</option>
<option  value='3'>m&auml;rts</option>
<option  value='4'>aprill</option>
<option  value='5'>mai</option>
<option  value='6'>juuni</option>
<option  value='7'>juuli</option>
<option  value='8'>august</option>
<option  value='9'>september</option>
<option  value='10'>oktoober</option>
<option  selected  value='11'>november</option>
<option  value='12'>detsember</option>
</select>
</td>
<td align="right">
<select id="miniyear" name="miniyear" class="formSelect" onChange="mini_navigate()"><option  value='2003'>2003</option>
<option  value='2004'>2004</option>
<option  value='2005'>2005</option>
<option  value='2006'>2006</option>
<option  selected  value='2007'>2007</option>
<option  value='2008'>2008</option>
<option  value='2009'>2009</option>
<option  value='2010'>2010</option>
</select>
</td>
</tr>
</table>
<script type="text/javascript">
function mini_navigate()
{
	var m = document.getElementById('minimon').value;
	var y = document.getElementById('miniyear').value;
	var newurl = document.getElementById('mininaviurl').value + "&date=" + m + "-" + y;
	window.location.href = newurl;
}
</script>


</form>
	<table border=0 cellspacing=0 cellpadding=0 width='100%'>
<tr>
<td class="minical_table">
<table width="100%" cellspacing="0" cellpadding="0">
<thead>
<tr>
<td>E</td>
<td>T</td>
<td>K</td>
<td>N</td>
<td>R</td>
<td>L</td>
<td>P</td>
</tr>

</thead>
<tr>
<td class="day minical_cell_deact">
<div>29</div>
</td>
<td class="day minical_cell_deact">
<div>30</div>
</td>
<td class="day minical_cell_deact">
<div>31</div>
</td>
<td class="day minical_cell">
1
</td>
<td class="day minical_cell">
2
</td>
<td class="day minical_cell">
3
</td>
<td class="day minical_cell">
4
</td>
</tr>
<tr>
<td class="day minical_cell">
5
</td>
<td class="day minical_cell">
6
</td>
<td class="day minical_cell">
7
</td>
<td class="day minical_cell">
8
</td>
<td class="day minical_cell">
9
</td>
<td class="day minical_cell">
10
</td>
<td class="day minical_cell">
11
</td>
</tr>
<tr>
<td class="day minical_cellact">
<a href="JavaScript: void(0)" onclick="$('.kalendri_valitud_p2evad').slideToggle(200);">12</a>
</td>
<td class="day minical_cell">
13
</td>
<td class="day minical_cellselected">
14
</td>
<td class="day minical_cellact">
15
</td>
<td class="day minical_cellact">
<a href="JavaScript: void(0)" onclick="$('.kalendri_valitud_p2evad').slideToggle(200);">16</a>
</td>
<td class="day minical_cellact">
<a href="JavaScript: void(0)" onclick="$('.kalendri_valitud_p2evad').slideToggle(200);">17</a>
</td>
<td class="day minical_cell">
18
</td>
</tr>
<tr>
<td class="day minical_cell">
19
</td>
<td class="day minical_cell">
20
</td>
<td class="day minical_cell">
21
</td>
<td class="day minical_cellact">
<a href="JavaScript: void(0)" onclick="$('.kalendri_valitud_p2evad').slideToggle(200);">22</a>
</td>
<td class="day minical_cellact">
<a href="JavaScript: void(0)" onclick="$('.kalendri_valitud_p2evad').slideToggle(200);">23</a>
</td>
<td class="day minical_cell">
24
</td>
<td class="day minical_cell">
25
</td>
</tr>
<tr>
<td class="day minical_cell">
26
</td>
<td class="day minical_cell">
27
</td>
<td class="day minical_cell">
28
</td>
<td class="day minical_cell">
29
</td>
<td class="day minical_cell">
30
</td>
<td class="day minical_cell_deact">
1
</td>
<td class="day minical_cell_deact">
2
</td>
</tr>
</table>
</td>
</tr>
</table>



</div><!-- minical_table -->
</div>
<div class="kalendri_valitud_p2evad" style="display: none">		

<div class="aw04kalendertextevent"><div class="aw04kalendersubevent" style="width:100%">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="aw04kalendersubevent">
<td>
<table cellspacing="0" cellpadding="0">
<tr>
<td style="width:27px;"><img src="http://register.automatweb.com/automatweb/images/icons/class_224_done.gif" alt="" border="0" align="middle"></td>
<td><a href="/automatweb/orb.aw?class=crm_meeting&action=change&return_url=http%3A%2F%2Fintranet.automatweb.com%2Fautomatweb%2Forb.aw%3Fclass%3Dplanner%26action%3Dmy_calendar&id=209741" title="Lisas [erki] 28.09.07 /  Muutis [erki] 28.09.07" alt="Lisas [erki] 28.09.07 /  Muutis [erki] 28.09.07">Firma üldkoosolek</a>
</td></tr></table>
</td>
</tr>
</table>
</div>

</div>
<div class="aw04kalendertextevent"><div class="aw04kalendersubevent" style="width:100%">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="aw04kalendersubevent">
<td>
<table cellspacing="0" cellpadding="0">
<tr>
<td style="width:27px;"><img src="http://register.automatweb.com/automatweb/images/icons/class_868.gif" alt="" border="0" align="middle"></td>
<td><a href="/automatweb/orb.aw?class=bug&action=change&return_url=http%3A%2F%2Fintranet.automatweb.com%2Fautomatweb%2Forb.aw%3Fclass%3Dplanner%26action%3Dmy_calendar&id=210675" title="Lisas [raivo] 08.10.07 /  Muutis [hannes] 08.10.07" alt="Lisas [raivo] 08.10.07 /  Muutis [hannes] 08.10.07">Amoris dokumendi templatess kommenteerimise võimalus</a>
</td></tr></table>
</td>
</tr>
</table>
</div>

</div>

</div><!-- kalendri_valitud_p2evad -->
</dd>
</dl>
<!-- kast -->
<dl class="box">
<dt>
	<span class="left">Poll</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<form action="#" method="post">
<p><span class="pollkysimus">Kuidas meeldib intraneti esileht?</span></p>

<p>
<a href="javascript:window.location.href='/?section=78948&amp;poll_id=210177&amp;c_set_answer_id=101&amp;section=78948'" class="pollanswer">Vinge</a>
<a href="javascript:window.location.href='/?section=78948&amp;poll_id=210177&amp;c_set_answer_id=102&amp;section=78948'" class="pollanswer">Teeksin parema</a>
<a href="javascript:window.location.href='/?section=78948&amp;poll_id=210177&amp;c_set_answer_id=102&amp;section=78948'" class="pollanswer">Sinine ei meeldi</a>
</p>
<p style="margin-top: 6px;">
<a href="#" class="nokk">Vaata tulemusi</a>
</p>
</form>
</dd>
</dl>
<!-- kast -->
<dl class="box">
<dt>
	<span class="left">Lähenevad sünnipäevad</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<table class="birthday">
<tr class="row">
<td><img src="/automatweb/images/temp/hannes.gif" alt="" /></td>
<td class="text">
"Tahan jõuda elus kaugele!"
<br />
<a href="">Hannes Kirsman (23+1</a><br />
29. Aprill 2008<br />
</td>
</tr>
<tr class="row row_end">
<td><img src="/automatweb/images/temp/ahto.jpeg" alt="" /></td>
<td class="text">
"Tahan jõuda elus kaugele!"
<br />
<a href="">Ahto Reinaru (30+1)</a><br />
28. August 2008
</td>
</tr>
</table>
</dd>
</dl>
<!-- kast -->
<dl class="box">
<dt>
	<span class="left">AutomatWeb</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<a class="nokk" href="#">Muuda salasõna</a><br />
<a class="nokk" href="#">Send email</a><br />
<a class="nokk" href="#">Sisene AutomatWeb'i</a>
</dd>
</dl>
</td>
<!-- mid-pane -->
<td id="mid_pane" class="mid_pane ui-sortable">
<!-- box -->
<dl class="box">
<dt class="green">
	<span class="left">Siseteated ja uudised</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<p>
<a href="">Nüüd avatud merendushuviliste portaal marine24.ee</a><br/>
<span class="kp">21. august 2007</span><br/>
</p>

<p>
<a href="">Uus kultuurisündmuste portaal kultuur.info</a><br/>
<span class="kp">26. juuli 2007</span><br/>
</p>

<p>
<a href="">OTTO veebipõhine kataloogikaubamaja avatud nüüd ka Soomes</a><br/>
<span class="kp">30. juuli 2007</span><br/>
</p>

<p>
<a href="">TTÜ Soojustehnika instituudi interaktiivne kaart</a><br/>
<span class="kp">25. juuli 2007</span>
</p>
</dd>
</dl>
<!-- box -->
<dl class="box">
<dt>
	<span class="left">RSS</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
12:54 <a href="">Fotod: Reaali Poiss tähistas juubelit </a><br />
12:45 <a href="">Ringmaa nõutud keemiakatse kohtus jäi ära (1 </a>)<br />
12:27 <a href="">Vene kindralmajor: me võime raketid Valgevenesse viia (2) </a><br />
12:19 <a href="">Gruusia peaprokuratuur taotleb Okruašvili vahistamist </a><br />
</dd>
</dl>
<!-- box -->
<dl class="box">
<dt>
	<span class="left">bla-bla-bla-board</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<p>
<strong>&lt;Marko&gt;</strong> umm,... et siis seal koodile update ei tohi teha?<br/>
<strong>&lt;sander85&gt;</strong> korras<br/>
<strong>&lt;terryf&gt;</strong>sander, nyyd ok?<br/>
<strong>&lt;Marko&gt;</strong> ok<br/>
<strong>&lt;terryf&gt;</strong> EI TOHI SEAL K2KKIDA!<br/>
<strong>&lt;terry&gt;</strong> GRR!<br/>
</p><div>
<table width="100%">
<tbody><tr>
	<td><input type="text" style="width: 100%;"/></td>
	<td width="50"><input type="button" id="button" value="Lisa"/></td>
</tr>
</tbody></table>

</div>
</dd>
</dl>	
</td>
<!-- right_pane -->
<td id="right_pane" class="right_pane ui-sortable">
<dl class="box">
<dt>
	<span class="left">Sisemised lingid</span>
	<span class="right">
	<a class="settings" title="seaded" href="JavaScript:void(0);"></a>
	<a class="minimize" title="tee väikseks" href="JavaScript:void(0);"></a>
	<a class="close" title="sulge" href="JavaScript:void(0);"></a></span><br class="clear"/></dt>
<dd>
<a href="#" class="nokk">Neti.ee</a><br />
<a href="#" class="nokk">Google</a>
</dd>
</dl>
</td>
</tr>
</table>
</div>
</div>

<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/ui.js"></script>
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/ui.tabs.js"></script>
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/iaw_tabs.js"></script>
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/jquery/plugins/iaw_drag_n_drop.js"></script>
<script type="text/javascript" src="{VAR:baseurl}/automatweb/js/iaw_init.js"></script>
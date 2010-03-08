<script language="javascript">
var stops = new Array();
</script>
<span class="awmenuedittabletext">
<b><font size="4">{VAR:stop_str}</font></b><br>
<!-- SUB: STOPPER -->
<hr>
{VAR:task_type}: {VAR:task_name}<br>
{VAR:start_str}: {VAR:time} | {VAR:el_str}: 
<!-- SUB: RUNNER -->
<span id="st{VAR:number}">{VAR:elapsed}</span><br>
<!-- END SUB: RUNNER -->

<!-- SUB: PAUSER -->
{VAR:elapsed}<br>
<!-- END SUB: PAUSER -->

<script language="javascript">
stops[{VAR:number}] = new Array({VAR:el_hr}, {VAR:el_min}, {VAR:el_sec});
</script>
<!-- SUB: PAUSE -->
<a href='{VAR:pause_url}'>{VAR:p_str}</a>
<!-- END SUB: PAUSE -->

<!-- SUB: START -->
<a href='{VAR:start_url}'>{VAR:s_str}</a>
<!-- END SUB: START -->

 | <a href='#' onClick='desc=prompt("Kirjeldus", "{VAR:task_name_esc}");if (desc != null) {window.opener.document.location.reload(true); window.location="{VAR:stop_url}&desc="+desc;}else{return false;}'>{VAR:e_str}</a> | <a href='{VAR:del_url}'>{VAR:d_str}</a><br>
<!-- END SUB: STOPPER -->

<script language="javascript">
function forward_this_sh__(desc)
{
	window.location="{VAR:stop_url}&desc="+desc;
}


function update_stoppers()
{
	num = stops.length;
	for(i = 0; i < num; i++)
	{
		el = document.getElementById('st'+i);
		if(!el)
		{
			continue;
		}
		tm = stops[i];
		// add a sec
		tm[2]++;
		if (tm[2] > 60)
		{
			tm[2] = 0;
			tm[1]++;
		}
		if (tm[1] > 60)
		{
			tm[1] = 0;
			tm[0]++;
		}
		hr = tm[0];
		if (hr < 10)
		{
			hr = "0" + hr;
		}
		mn = tm[1];
		if (mn < 10)
		{
			mn = "0" + mn;
		}
		sc = tm[2];
		if (sc < 10)
		{
			sc = "0" + sc;
		}
		el.innerHTML = hr+":"+mn+":"+sc;
	}
	setTimeout("update_stoppers()", 990);
}

setTimeout("update_stoppers()", 990);
</script>
</span>

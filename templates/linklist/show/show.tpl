<i>object: {VAR:name}<br>
{VAR:comment}</i>
{VAR:css}
<center>
	<table width=500 border=1 cellpadding=0 cellspacing=0>
				<!-- SUB: YAHBAR -->
	<tr>

		<td colspan=3>
			<i>
			<b>
				<!-- SUB: YAH -->
				&nbsp;/&nbsp;<a href={VAR:link}>{VAR:name}</a>
				<!-- END SUB: YAH -->
			</b>
			</i> <small><small>{VAR:total}</small></small>
		</td>
	</tr>
				<!-- END SUB: YAHBAR -->
	<tr>
			<!-- SUB: tulp -->
			<td valign=top>
			<!-- SUB: dir -->
			&nbsp;&nbsp;&nbsp;<b><a href={VAR:link}>{VAR:name}</a></b>
			<!-- SUB: sub_count -->
			<small>[{VAR:count}]</small>
			<!-- END SUB: sub_count -->			
			<small><i>hits {VAR:hits}</i></small><br />
			<!-- END SUB: dir -->

			</td>
			<!-- END SUB: tulp -->
	</tr>
	<table width=100% border=1><tr>
		{VAR:links}
	</tr></table>

	<tr>
		<td colspan=3>
			<hr />statusbar:  
			&nbsp;linke kataloogis:	{VAR:total2}
			  // abix:{VAR:abix}
		</td>
	</tr>
	</table>
</center>

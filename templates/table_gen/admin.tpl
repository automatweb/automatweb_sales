{VAR:menu}
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr><td class="tableborder">

<table border=0 cellpadding=2 bgcolor="#FFFFFF" cellspacing=1>

<tr>
	<td class="aste01">



<form action='reforb.{VAR:ext}' method=post NAME='q'>
<input type='submit' NAME='save_table' VALUE='{VAR:LC_TABLE_SAVE}' class="formbutton">
<table border=0 cellspacing=1 cellpadding=2>
<tr>
<!-- SUB: DC -->
<td bgcolor="#FFFFFF">
<!-- SUB: FIRST_C -->
<a href='{VAR:add_col}'><img alt="{VAR:LC_TABLE_ADD_COL}" src='/automatweb/images/rohe_nool_alla.gif' border=0></a>
<!-- END SUB: FIRST_C -->
<input type='checkbox' NAME='dc_{VAR:col}' value=1>&nbsp;<a href="javascript:box2('{VAR:LC_TABLE_SURE_DEL_COLUMN}?','{VAR:del_col}')"><img alt="{VAR:LC_TABLE_DEL_COL}" src='/automatweb/images/puna_nool_alla.gif' border=0></a>
<a href='{VAR:add_col}'><img alt="{VAR:LC_TABLE_ADD_COL}" src='/automatweb/images/rohe_nool_alla.gif' border=0></a>
</td>
<!-- END SUB: DC -->
<td bgcolor=#FFFFFF>&nbsp;</td>
</tr>
<!-- SUB: LINE -->
<tr>
<!-- SUB: COL -->
<td bgcolor=#ffffff rowspan={VAR:rowspan} colspan={VAR:colspan} class="celltext"><b>T:</b> {VAR:text}<br>R:<input type='text'  class="formtext" size=3 NAME='rows[{VAR:row}][{VAR:col}]' VALUE='{VAR:rows}'><br>V:<input type='text'  class="formtext" size=3 NAME='cols[{VAR:row}][{VAR:col}]' VALUE='{VAR:cols}'></td>
<!-- END SUB: COL -->
<td bgcolor=#ffffff valign=bottom align=left>
<!-- SUB: FIRST_R -->
<a href='{VAR:add_row}'><img alt="{VAR:LC_TABLE_ADD_ROW}" src='/automatweb/images/rohe_nool_vasakule.gif' BORDER=0></a><br>
<!-- END SUB: FIRST_R -->
<a href="javascript:box2('{VAR:LC_TABLE_SURE_DEL_ROW}?','{VAR:del_row}')"><img src='/automatweb/images/puna_nool_vasakule.gif' alt="{VAR:LC_TABLE_DEL_ROW}" BORDER=0></a><Br><input type='checkbox' NAME='dr_{VAR:row}' value=1><br>
<a href='{VAR:add_row}'><img alt="{VAR:LC_TABLE_ADD_ROW}" src='/automatweb/images/rohe_nool_vasakule.gif' BORDER=0></a>
</td>
</tr>
<!-- END SUB: LINE -->
</table>
<input type='submit' NAME='save_table' VALUE='{VAR:LC_TABLE_SAVE}' class="formbutton">
{VAR:reforb}
</form>
<br>
<br>
<table hspace=0 vspace=0 cellpadding=3>
	<tr>
	<form action='orb.{VAR:ext}' method=get name='lv'>
		<td class="celltext"><b>
			
				<input type='hidden' NAME='class' VALUE='table'>
				<input type='hidden' NAME='action' VALUE='add_col'>
				<input type='hidden' NAME='id' VALUE='{VAR:table_id}'>
				<input type='hidden' NAME='after' VALUE='0'>
				<a href='javascript:document.lv.submit();'>{VAR:LC_TABLE_ADD} </a><input type='text' NAME='num' size=2 class="formtext"> <a href='javascript:document.lv.submit();'>{VAR:LC_TABLE_COL}</a>
			
			</b>
		</td>
		</form>
		<td class="celltext">|</td>

			<form action='orb.{VAR:ext}' method=get name='lr'>
		<td class="celltext">
		<b>

				<input type='hidden' NAME='class' VALUE='table'>
				<input type='hidden' NAME='action' VALUE='add_row'>
				<input type='hidden' NAME='id' VALUE='{VAR:table_id}'>
				<input type='hidden' NAME='after' VALUE='0'>
					<a href='javascript:document.lr.submit();'>{VAR:LC_TABLE_ADD} </a><input type='text' NAME='num' size=2 class="formtext"> <a href='javascript:document.lr.submit();'>{VAR:LC_TABLE_ROW}</a>
			
			</b>
		</td>
		</form>
	</tr>
</table>


</td>
</tr>
</table>


</td>
</tr>
</table>
<br>
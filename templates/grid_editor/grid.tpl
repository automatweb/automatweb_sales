<script language="javascript">

function add_col(after, num)
{
	document.changeform.ge_action.value = "action=add_col;after="+after+";num="+num;
	document.changeform.submit();
}

function del_col(col, num)
{
	document.changeform.ge_action.value = "action=del_col;col="+col+";num="+num;
	document.changeform.submit();
}

function add_row(after, num)
{
	document.changeform.ge_action.value = "action=add_row;after="+after+";num="+num;
	document.changeform.submit();
}

function del_row(row, num)
{
	document.changeform.ge_action.value = "action=del_row;row="+row+";num="+num;
	document.changeform.submit();
}

function exp_down(row, col)
{
	document.changeform.ge_action.value = "action=exp_down;row="+row+";col="+col+";cnt="+document.changeform.exp_count.value;
	document.changeform.submit();
}

function exp_up(row, col)
{
	document.changeform.ge_action.value = "action=exp_up;row="+row+";col="+col+";cnt="+document.changeform.exp_count.value;
	document.changeform.submit();
}

function exp_left(row, col)
{
	document.changeform.ge_action.value = "action=exp_left;row="+row+";col="+col+";cnt="+document.changeform.exp_count.value;
	document.changeform.submit();
}

function exp_right(row, col)
{
	document.changeform.ge_action.value = "action=exp_right;row="+row+";col="+col+";cnt="+document.changeform.exp_count.value;
	document.changeform.submit();
}

function split_ver(row, col)
{
	document.changeform.ge_action.value = "action=split_ver;row="+row+";col="+col;
	document.changeform.submit();
}

function split_hor(row, col)
{
	document.changeform.ge_action.value = "action=split_hor;row="+row+";col="+col;
	document.changeform.submit();
}

function pick_style()
{
	// here we gotta go over all elements/rows/columns and add them to the end of the picker url

	rows = "rows=";
	cols = "cols=";
	cells = "cells="; 

	len = document.changeform.elements.length;
	for (i = 0; i < len; i++)
	{
		el = document.changeform.elements[i];
		if (el.name.indexOf("sel_") != -1 && el.checked)
		{
			cells +=el.name;
		}
		else
		if (el.name.indexOf("dc_") != -1 && el.checked)
		{
			cols += el.name;
		}
		else
		if (el.name.indexOf("dr_") != -1 && el.checked)
		{
			rows += el.name;
		}
	}
	remote("no", 400, 400,"{VAR:selstyle}&"+rows+"&"+cols+"&"+cells+"&oid={VAR:oid}");
}

function exec_cmd(cmd)
{
	rows = "rows=";
	cols = "cols=";
	cells = "cells="; 

	len = document.changeform.elements.length;
	for (i = 0; i < len; i++)
	{
		el = document.changeform.elements[i];
		if (el.name.indexOf("sel_") != -1 && el.checked)
		{
			cells +=el.name;
		}
		else
		if (el.name.indexOf("dc_") != -1 && el.checked)
		{
			cols += el.name;
		}
		else
		if (el.name.indexOf("dr_") != -1 && el.checked)
		{
			rows += el.name;
		}
	}

	document.changeform.ge_action.value = "action="+cmd+";"+cells+";"+cols+";"+rows+";cnt="+document.changeform.exp_count.value;
	document.changeform.submit();
}

</script>

{VAR:toolbar}
<table border=1 cellspacing=1 cellpadding=2>
	<tr>
		<td bgcolor="#FFFFFF" colspan=2 class="celltext">{VAR:cell_count}</td><td bgcolor="#FFFFFF" colspan=100>
			<input type='text' name='exp_count' value=1 size=2 class="formtext">
		</td>
	</tr>
	<tr>
		<!-- SUB: DC -->
			<td bgcolor="#FFFFFF" class="celltext">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" hspace="0" vspace="0">
					<tr>
						<td valign="bottom">
							<!-- SUB: FIRST_C -->
								<a href="javascript:add_col({VAR:after},1);"><img alt="{VAR:add_col_caption}" title="{VAR:add_col_caption}" src='{VAR:baseurl}/automatweb/images/rohe_nool_alla.gif' border=0></a>
							<!-- END SUB: FIRST_C -->
						</td>
						<td valign="bottom" align="middle"><input type='checkbox' NAME='dc_{VAR:col}' value=1><br><a href="#" onClick="if (confirm('{VAR:confirm_col_del}')) { del_col({VAR:col},1); return true;} else { return false;} "><img alt="{VAR:del_col}" title="{VAR:del_col}" src='{VAR:baseurl}/automatweb/images/puna_nool_alla.gif' border=0></a></td>
						<td valign="bottom"><a href='javascript:add_col({VAR:after}, 1)'><img alt="{VAR:add_col}" title="{VAR:add_col}" src='{VAR:baseurl}/automatweb/images/rohe_nool_alla.gif' border=0></a></td>
					</tr>
				</table>
			</td>
		<!-- END SUB: DC -->
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<!-- SUB: LINE -->
	<tr>
		<!-- SUB: COL -->
		<td bgcolor=#FFFFFF rowspan={VAR:rowspan} colspan={VAR:colspan} class="celltext">
			<!-- SUB: COL_CONTENT -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td width="1" height="1"><img src='{baseurl}/automatweb/images/trans.gif' width='1' height='1'></td>
					<td colspan="2" width="100%" align="left" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="left">{VAR:EXP_UP}</td>
								<td width="100%" align="right">{VAR:SPLIT_HORIZONTAL}{VAR:SPLIT_VERTICAL}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="1" height="1" valign="top" align="left">{VAR:EXP_LEFT}</td>
					<td width="100%" align="center" valign="center"><input type="checkbox" name="sel_row={VAR:row};col={VAR:col}"> <span class="celltext">{VAR:content_text}</span></td>
					<td width="1" valign="bottom" align="right">{VAR:EXP_RIGHT}</td>
				</tr>
				<tr>
					<td height="1"><img src='{baseurl}/automatweb/images/trans.gif' width='1' height='1'></td>
					<td width="100%" align="right" valign="bottom">{VAR:EXP_DOWN}</td>
					<td width="1"><img src='{baseurl}/automatweb/images/trans.gif' width='1' height='1'></td>
				</tr>
			</table>
			<!-- END SUB: COL_CONTENT -->
		</td>
		<!-- END SUB: COL -->

		<td bgcolor=#ffffff valign=bottom align=left>
			<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" hspace="0" vspace="0">
				<tr>
					<td align="left">
						<!-- SUB: FIRST_R -->
							<a href='javascript:add_row({VAR:after},1)'><img alt="{VAR:add_row}" title="{VAR:add_row}" src='{VAR:baseurl}/automatweb/images/rohe_nool_vasakule.gif' BORDER=0></a>
						<!-- END SUB: FIRST_R -->
					</td>
				</tr>
				<tr>
					<td><a href="#" onClick="if (confirm('{VAR:confirm_row_del}')) { del_row({VAR:row},1); return true; } else {return false;} "><img src='{VAR:baseurl}/automatweb/images/puna_nool_vasakule.gif' alt="{VAR:del_row}" title="{VAR:del_row}" BORDER=0></a><input type='checkbox' NAME='dr_{VAR:row}' value=1></td>
				</tr>
				<tr>
					<td><a href='javascript:add_row({VAR:after},1)'><img alt="{VAR:add_row}" title="{VAR:add_row}" src='{VAR:baseurl}/automatweb/images/rohe_nool_vasakule.gif' BORDER=0></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END SUB: LINE -->
</table>
<input type="hidden" name="ge_action" value="">

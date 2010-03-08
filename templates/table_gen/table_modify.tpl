{VAR:menu}
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr><td class="tableborder">

<table border=0 cellpadding=2 bgcolor="#FFFFFF" cellspacing=1 width="100%" >

<tr>
	<td class="aste01" width="100%" >



<form action='reforb.{VAR:ext}' method=post>
<input type='submit' NAME='save_table' VALUE='{VAR:LC_TABLE_SAVE}' class="formbutton">
<table border=0 cellspacing=2 cellpadding=2 width="100%" >
<!-- SUB: extdata -->
<tr>
<td colspan=1 class="celltext">Aliased:</td>
<td colspan=101 class="celltext" width="100%" >
{VAR:extdata}
</td>
</tr>
<!-- END SUB: extdata -->

<!-- SUB: LINE -->
<tr>
<!-- SUB: COL -->
<td colspan={VAR:colspan} rowspan={VAR:rowspan}>
<!-- SUB: H_HEADER-->
<!-- <b>{VAR:text2}</b> -->
<!-- END SUB: H_HEADER-->
<!-- SUB: AREA -->
<textarea class='formtext' name="text[{VAR:row}][{VAR:col}]" cols="{VAR:num_cols}" rows="{VAR:num_rows}">{VAR:text}</textarea>
<!-- END SUB: AREA -->
<!-- SUB: BOX -->
<input type='text' class='formtext' SIZE='{VAR:num_cols}' NAME='text[{VAR:row}][{VAR:col}]' VALUE="{VAR:text}">
<!-- END SUB: BOX -->
</td>
<!-- END SUB: COL -->
</tr>
<!-- END SUB: LINE -->

</table>
<input type='submit' NAME='save_table' VALUE='{VAR:LC_TABLE_SAVE}' class="formbutton">
{VAR:reforb}
</form>
<!-- SUB: aliases -->
<iframe width="100%" height="800" frameborder="0" src="{VAR:aliasmgr_link}">
</iframe>
<!-- END SUB: aliases -->



</td>
</tr>
</table>


</td>
</tr>
</table>
<br>
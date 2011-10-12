{VAR:menu}

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr><td class="tableborder">

<table border=0 cellpadding=2 bgcolor="#FFFFFF" cellspacing=1>

<tr>
	<td class="aste01">


<table width="100%" border=0 cellspacing=0 cellpadding=0>
<tr><td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>


<form action = 'reforb{VAR:ext}' method=post enctype='multipart/form-data'>
<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='1000000'>


<td class="celltext"><b>{VAR:LC_TABLE_TABLE}: {VAR:name}</b></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_TABLE_UPLOAD_FILE_CVS}.</td>
</tr>
<tr>
<td class="celltext"><input type='file' NAME='fail'class="formfile"></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_TABLE_REMOVE_EMPTY_ROWS}? <input type='checkbox' name='trim' value="1" checked></td>
</tr>
<tr>
<td class="celltext">{VAR:LC_TABLE_WHAT_SIGN_COL}? <input type='text' name='separator' value=";" size=1 class="formtext"></td>
</tr>
<tr>
<td><input type='submit' VALUE='{VAR:LC_TABLE_IMPORT}' class="formbutton"></td>
</tr>
</table>
{VAR:reforb}
</form>


</td></tr></table>


</td>
</tr>
</table>


</td>
</tr>
</table>
<br>
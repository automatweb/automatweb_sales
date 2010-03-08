<form action='reforb.{VAR:ext}' method=post enctype="multipart/form-data">
<input type='hidden' name='MAX_FILE_SIZE' value='10000000'>


<table border=0 cellspacing=0 cellpadding=1>
<tr>
<td bgcolor="#FFFFFF">

<table border=0 cellspacing=0 cellpadding=5>
<tr>
<td class="aste01">

<table border=0 cellspacing=0 cellpadding=2>
<tr>


<td class="celltext">{VAR:LC_FORMS_FILE} (.csv):</td>
<td class="celltext"><input type='file' NAME='file' class="formfile"></td>
</tr>
<tr>
<td></td>
<td class="celltext"><input class='formbutton' type='submit' VALUE='Uploadi'></td>
</tr>
</table>
</td></tr></table>
</td></tr></table>
{VAR:reforb}
</form>

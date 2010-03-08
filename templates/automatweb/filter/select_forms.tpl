<a href="{VAR:ln_use_existing}" class="fgtext">Kasuta olemasolevat formitabelit</a>
<form action = 'reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fgtitle">Vali formid:</td>
<td class="fgtext"><select class='small_button' NAME='selected_forms[]' multiple>{VAR:form_list}</select></td>
</tr>

<tr>
<td class="fgtitle" colspan="3" align="right"><input class='small_button' type='submit' VALUE='Edasi >>'></td>
</tr>
</table>
{VAR:reforb}
</form>

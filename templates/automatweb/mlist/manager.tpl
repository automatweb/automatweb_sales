<form action="reforb{VAR:ext}" method=post name='foo'>
<!-- SUB: MGR -->
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>

<tr><td class="ftitle2">Liikmete form:</td><td class="fform"><select type="text" class="small_button" NAME="form">{VAR:form}</select>
</td></tr>

<tr><td class="ftitle2">Liikmete Otsinguform:</td><td class="fform"><select type="text" class="small_button" NAME="searchform">{VAR:searchform}</select>
</td></tr>

<tr><td class="ftitle2">Meiliaadressi element:</td><td class="fform"><select type="text" class="small_button" NAME="mailel">{VAR:mailel}</select>
</td></tr>

<tr><td class="ftitle2" colspan="2" align="right">
<input class='small_button' type='submit' VALUE='Salvesta'>
</td></tr>
</table>
<br>
<!-- END SUB: MGR -->
{VAR:reforb}
<script language="javascript">
function Do(what)
{
foo.action.value=what;
foo.submit();
};
</script>
{VAR:QUEUE}
</form>
<select name='applystyle' onChange='applystyle(this)'>
<option>vali stiil
<option value="styl1">roheline Verdana
<option value="styl2">sinine ja suur
<option value="styl3">punane tekst, sinine kast
</select>
<script type="text/javascript">
function applystyle(grr)
{
	parent.contentarea.surroundHTML("<span class='" + grr.options[grr.selectedIndex].value + "'>","</span>");
	grr.selectedIndex = 0;
}
</script>

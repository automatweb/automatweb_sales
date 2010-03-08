{VAR:toolbar}
<table border=0 cellspacing=1 cellpadding=2>
<!-- SUB: field -->
<tr>
	<td class='chformleftcol' width='160' nowrap>
	{VAR:caption}
	</td>
	<td class='chformrightcol'>
	{VAR:element}
	</td>
</tr>
<!-- END SUB: field -->
<!-- SUB: hidden -->
	{VAR:element}
<!-- END SUB: hidden -->
<!-- SUB: getoptions -->
<script language= "javascript">
function GetOptions(from, tu)
{
	if (tu.type == 'select-multiple')
	{
		var defaults = new Array();

		{VAR:selected}

		len = tu.options.length;
		for (i=0; i < len; i++)
		{
			tu.options[0] = null
		}
		var j = 0;
		len = from.options.length;
		for (var i=0; i < len; i++)
		{
			if ((from.options[i].value != 'capt_new_object') && (from.options[i].value != '0'))
			{
//					tu.options[j] = new Option(from.options[i].text, from.options[i].value, false, seld[from.options[i].value]);
tu.options[j] = new Option(from.options[i].text, from.options[i].value, false, ((len == 1) ? true : (defaults[from.options[i].value] ? defaults[from.options[i].value] : false)));
				j = j + 1;
			}
		}
	}
	else
	{
		if (from.value == 'capt_new_object')
		{

			len = tu.value = '';
			len = from.options.length;
			for (var i=0; i < len; i++)
			{
				if ((from.options[i].value != 'capt_new_object') && (from.options[i].value != '0'))
				{
					tu.value = tu.value + ',' + from.options[i].value;
				}

			}
		}
		else
		{
			tu.value = from.value;
		}

	}
	document.searchform.elements['s[name]'].focus();
}

if (document.forms['searchform'].elements['{VAR:element}'])
{
	GetOptions(document.forms[0].elements['aselect'],document.forms['searchform'].elements['{VAR:element}']);
}
</script>
<!-- END SUB: getoptions -->


</table>

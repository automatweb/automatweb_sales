<script>
	function dropDownChanged(obj)
	{
		if(obj.id=="otsitunnus")
		{
			tmp=document.getElementById('tootekood');
			tmp.selectedIndex=obj.selectedIndex;
			window.opener.document.getElementById('tootekood').value=tmp.value;
			window.opener.document.getElementById('otsitunnus').value='';
		}
		else if(obj.id=="tootekood")
		{
			tmp=document.getElementById('otsitunnus');
			tmp.selectedIndex=obj.selectedIndex;
			window.opener.document.getElementById('otsitunnus').value=tmp.value;
			window.opener.document.getElementById('tootekood').value='';
		}
	}

</script>
<table>
	<tr>
		<td>
			<select name='otsitunnus' id='otsitunnus' onChange="dropDownChanged(this)">
				<!-- SUB: OTSITUNNUS -->
				<option value='{VAR:otsitunnus}'>{VAR:otsitunnus}</option>
				<!-- END SUB: OTSITUNNUS -->
				{VAR:otsitunnused}
			</select>
		</td>
		<td>
			<select name='tootekood' id='tootekood' onChange="dropDownChanged(this)">
				<!-- SUB: TOOTEKOODSUB -->
				<option value='{VAR:tootekood}'>{VAR:tootekood}</option>
				<!-- END SUB: TOOTEKOODSUB -->
				{VAR:tootekoodid}
			</select>
		</td>
	</tr>
</table>
{VAR:reforb}

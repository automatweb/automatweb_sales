<form method='POST' name='changeform' enctype='multipart/form-data' >

<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='100000000'>

<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		<a href='javascript:void(0)'   alt='Vabas vormis tekst objekti kohta' title='Vabas vormis tekst objekti kohta'  >K</a>ommentaar
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="comment" name="comment" size="40" value="" maxlength=""/>

        </td>
</tr>

<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		<a href='javascript:void(0)'   alt='Kas objekt on aktiivne ' title='Kas objekt on aktiivne '  >A</a>ktiivne
		</td>
        <td class='aw04contentcellright'>
		<input type='radio' name='status' value='2' CHECKED onClick=''/>
 Jah<input type='radio' name='status' value='1'  onClick=''/>
 Ei
        </td>

</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		Toote nimetus
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="name" name="name" size="40" value="2333333333" maxlength=""/>

        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>

		Kood
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="product_code" name="product_code" size="40" value="" maxlength=""/>

        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		Värvus
		</td>

        <td class='aw04contentcellright'>
		<input type="text" id="product_color" name="product_color" size="40" value="" maxlength=""/>

        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		Suurus
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="product_size" name="product_size" size="40" value="" maxlength=""/>

        </td>
</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		Kogus
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="product_count" name="product_count" size="40" value="" maxlength=""/>

        </td>

</tr>
<tr>
        <td class='aw04contentcellleft' width='80' nowrap>
		Hind
		</td>
        <td class='aw04contentcellright'>
		<input type="text" id="product_price" name="product_price" size="40" value="" maxlength=""/>

        </td>
</tr>
</table>
<br />
<input type='submit' name='' value='Salvesta' class='aw04formbutton' accesskey="s" onClick='submit_changeform(""); return false;'>
<br />
<br />

<input type='hidden' name='class' value='orders_item' />
<input type='hidden' name='action' value='submit' />
<input type='hidden' name='reforb' value='1' />
<input type='hidden' name='id' value='51888' />
<input type='hidden' name='group' value='general' />
<input type='hidden' name='parent' value='' />
<input type='hidden' name='section' value='' />
<input type='hidden' name='period' value='' />
<input type='hidden' name='alias_to' value='' />
<input type='hidden' name='reltype' value='' />
<input type='hidden' name='cfgform' value='' />
<input type='hidden' name='return_url' value='' />
<input type='hidden' name='subgroup' value='' />

<script type="text/javascript">
function submit_changeform(action)
{
	
	if (typeof action == "string" && action.length>0)
	{
		document.changeform.action.value = action;
	};
	document.changeform.submit();
}

</script>
</form>
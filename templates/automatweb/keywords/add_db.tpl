<form method="POST" action="reforb.{VAR:ext}">

<table border="0" cellspacing="0" cellpadding="0" width="300">
	<tr>
		<td bgcolor="#CCCCCC">
			<table border="0" cellspacing="1" cellpadding="0" width="100%">
				<tr>
					<td class="fgtext">&nbsp;Nimi:&nbsp;</td>
					<td class="fgtext"><input type="text" size="50" name="name" value="{VAR:name}"><td>
				</tr>
				<tr>
					<td colspan="2" class="fgtext">&nbsp;Vali kataloogid, mille alt keywordid kuuluvad sellesse baasi:&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" class="fgtext">&nbsp;<select name='keyw_cats[]' class="small_button" size=20 multiple>{VAR:keyw_cats}</select></td>
				</tr>
				<tr>
					<td colspan="2" class="fgtext">&nbsp;Vali kataloogid, mille alla saab selle baasi keyworde vennastada:&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" class="fgtext">&nbsp;<select size=20 name='bro_cats[]' class="small_button" multiple>{VAR:bro_cats}</select></td>
				</tr>
				<tr>
					<td class="fgtext" colspan="2" align="center"><input type="submit" value="Salvesta"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
{VAR:reforb}
</form>

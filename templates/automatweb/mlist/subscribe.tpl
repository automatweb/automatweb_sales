<form method="post">
	<table>
		<tr>
			<td>
				<span class="text">{VAR:LC_MLIST_NAME}&nbsp;</span>
			</td>
			<td>
				<input type="text" name="name" size="12">
			</td>
		</tr>
		<tr>
			<td nowrap>
				<span class=text>{VAR:LC_MLIST_EMAIL}&nbsp;</span>
			</td>
			<td>
				<input type="text" name="email" size="12">
			</td>
		</tr>
		<!-- SUB: FOLDER -->
			<tr>
				<td colspan="2">
					<input type="checkbox" name="subscr_folder[{VAR:folder_id}]" value="1" />&nbsp;<span class=text>{VAR:folder_name}</span>
				</td>
			</tr>
		<!-- END SUB: FOLDER -->
		<tr>
			<td></td>
		</tr>
		<!-- SUB: LANGFOLDER -->
			<tr>
				<td colspan="2">
					<input type="checkbox" name="subscr_lang[{VAR:lang_id}]" value="1" />&nbsp;<span class=text>{VAR:lang_name}</span>
				</td>
			</tr>
		<!-- END SUB: LANGFOLDER -->
		<tr>
			<td colspan="2">
				<input type="submit" value="{VAR:LC_MLIST_JOIN}">
			</td>
		</tr>
		<input type="hidden" name="op" value="1">
		{VAR:reforb}
	</form>
</table>
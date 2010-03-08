<form method="post">
	<input type="hidden" name="op" value="2">
	{VAR:reforb}
	<table>
		<tr>
			<td nowrap="nowrap">
				<span class=text>{VAR:LC_MLIST_EMAIL}&nbsp;</span>
			</td>
			<td>
				<input type="text" name="email" size="30">
			</td>
		</tr>
		<!-- SUB: FOLDER -->
			<tr>
				<td colspan="2">
					<input type="checkbox" name="subscr_folder[{VAR:folder_id}]" value="1" />&nbsp;<span class=text>{VAR:folder_name}</span></td>
				</tr>
		<!-- END SUB: FOLDER -->
		<tr>
			<td></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="{VAR:LC_MLIST_LEAVE}">
			</td>
		</tr>
	</table>
</form>
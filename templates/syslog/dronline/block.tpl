<table border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
<tr>
<td>
	<form action="reforb.{VAR:ext}" method="POST">
	<table border="0" width="100%" cellspacing="1" cellpadding="3">
	<tr class="awmenuedittablerow">
		<td class="awmenuedittablehead" align="center"><strong>IP</strong></center></td>
		<td class="awmenuedittablehead" align="center"><strong>Aktiivne</strong></center></td>
	</tr>
	<!-- SUB: line -->
	<tr class="awmenuedittablerow">
		<td class="awmenuedittabletext">{VAR:ip}</td>
		<td class="awmenuedittabletext" align="center"><input type="checkbox" name="check[{VAR:id}]" value="1" {VAR:checked}></td>
	</tr>
	<!-- END SUB: line -->
	<tr class="awmenuedittablerow">
		<td class="awmenuedittabletext" align="center" colspan="2">
		<input type="text" name="new" size="20"><input type="submit" value="Lisa/Salvesta">
		{VAR:reforb}
		</td>
	</tr>
	</table>
	</form>
</td>
</tr>
</table>

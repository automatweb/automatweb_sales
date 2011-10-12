<table width="100%" cellpadding=1 cellspacing=0 border=0>
	<form action='reforb{VAR:ext}' method=post>
	<tr>
		<td bgcolor="#FFFFFF">
			<table width="100%" border=0 cellspacing=0 cellpadding=5>
				<tr>
					<td class="aste01">
						<table cellpadding=3 cellspacing=1 border=0>
							<tr>
								<td class="celltext" colspan="5"><input class='formbutton' type='submit' VALUE='Salvesta'></td>
							</tr>
							<tr>
								<td class="celltext">Mis formist</td>
								<td class="celltext">Mis formi</td>
								<td class="celltext">Mis elemendist</td>
								<td class="celltext">Mis elementi</td>
								<td class="celltext">J&auml;ta v&auml;lja</td>
							</tr>
							<!-- SUB: LINE -->
							<tr>
								<td class="celltext"><a href='{VAR:from_change}'>{VAR:from_form}</a></td>
								<td class="celltext"><a href='{VAR:to_change}'>{VAR:to_form}</a></td>
								<td class="celltext">{VAR:from_el}</td>
								<td class="celltext">{VAR:to_el}</td>
								<td class="celltext"><input type="checkbox" name="no_join[]" value="{VAR:relid}" {VAR:checked}></td>
							</tr>
							<!-- END SUB: LINE -->
							<tr>
								<td class="celltext" colspan="5"><input class='formbutton' type='submit' VALUE='Salvesta'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
{VAR:reforb}
</form>

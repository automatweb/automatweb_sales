<form action="reforb.{VAR:ext}" method="POST">
	<table>
		<tr>
			<td>Kataloogihind:</td>
			<td><input type="text" name="sum_core" id="sum_core" value="{VAR:sum_core}" /></td>
		</tr>
		<tr>
			<td>J&auml;relmaksuperiood:</td>
			<td>
				<select name="rent_period" id="rent_period">
					<!-- SUB: RENT_PERIOD_OPTION -->
						<option value="{VAR:rent_period_value}">{VAR:rent_period_value}</option>
					<!-- END SUB: RENT_PERIOD_OPTION -->
					<!-- SUB: RENT_PERIOD_OPTION_SELECTED -->
						<option value="{VAR:rent_period_value}" selected="selected">{VAR:rent_period_value}</option>
					<!-- END SUB: RENT_PERIOD_OPTION_SELECTED -->
				</select> kuu(d)
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Arvuta" /></td>
		</tr>
	</table>
	<input type="hidden" name="id" value="{VAR:id}" />
	<input type="hidden" name="single_payment" value="{VAR:single_payment}" />
	<input type="hidden" name="prepayment" value="{VAR:prepayment}" />
	<input type="hidden" name="class" value="shop_rent_calculator" />
	<input type="hidden" name="action" value="calculate" />
</form>

<!-- SUB: RESULT -->
<table cellspacing="0" cellpadding="3" border="0">
	<tbody>
		<tr>
			<td><b>Kataloogihind</b></td>
			<td><b>Sissemakse</b></td>
			<td><b>Osamakseid</b></td>
			<td><b>Osamakse suurus</b></td>
			<td><b>Hind j&auml;relmaksuga</b></td>
		</tr>
		<tr>
			<td>{VAR:sum_core}</td>
			<td>{VAR:prepayment}</td>
			<td>{VAR:rent_period}</td>
			<td>{VAR:single_payment}</td>
			<td align="right">{VAR:sum_rent} EEK</td>
		</tr>
	</tbody>
</table>
<!-- END SUB: RESULT -->

<!-- SUB: ERROR -->
<div style="color:#ff0000">{VAR:error}</div>
<!-- END SUB: ERROR -->
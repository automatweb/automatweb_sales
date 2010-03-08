<span class="textpealkiri">Kataloogiotsing</span><br>

						<br>
						<table>
						<form action="{VAR:baseurl}" method="GET">
						<tr>
							<td class="text" align="right">Nimetus: </td>
							<td><input type="text" size="20" name="prod_name" value="{VAR:s_prod_name}"></td>
						</tr>
						<tr>

							<td class="text" align="right">Värv: </td>
							<td><input type="text" size="20" name="prod_color" value="{VAR:s_prod_color}"></td>
						</tr>
						<tr>
							<td class="text" align="right">Hind: </td>
							<td class="text">
						<select name="price_from">{VAR:s_price_from}</select>
						kuni
						<select name="price_to">{VAR:s_price_to}</select> krooni
						</td>
						</tr>
						<tr>
							<td colspan="2" class="text">
								<!-- SUB: SEARCH_FLD -->
								<input type="checkbox" value="{VAR:fld}" name="search_fld[]" {VAR:checked}> {VAR:fld_name}
								<!-- END SUB: SEARCH_FLD -->
							</td>
						</tr>
						<tr><td></td>
						<td><input type="submit" value="otsi" class="formbutton"></td>

						</tr>
				{VAR:reforb}
						</form>
						</table>
						
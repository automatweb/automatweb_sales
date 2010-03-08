<form action="{VAR:baseurl}/index.{VAR:ext}" method="POST">
<div class="textpealkiri">Ostukorv</div>

<table border="0" width="100%" cellpadding="0" cellspacing="1">
<tr>
	<td class="prodcarthead">Toote nimetus</td>
	<td class="prodcarthead" align="center">Kood</td>
	<td class="prodcarthead" align="center">Värvus</td>
	<td class="prodcarthead" align="center">Suurus</td>
	<td class="prodcarthead" align="center">Kogus</td>
	<td class="prodcarthead" align="center">Hind</td>
	<td class="prodcarthead" align="right">Summa</td>
</tr>

<!-- SUB: PROD -->
{VAR:prod_html}
<!-- END SUB: PROD -->
<tr>
	<td class="prodcartitem">Postikulu</td>
	<td class="prodcartitem" align="center">&nbsp;</td>
	<td class="prodcartitem" align="center">&nbsp;</td>
	<td class="prodcartitem" align="center">&nbsp;</td>
	<td class="prodcartitem" align="center">&nbsp;</td>
	<td class="prodcartitem" align="center">&nbsp;{VAR:postal_price} EEK</td>
	<td class="prodcartitem" align="center">&nbsp;{VAR:postal_price} EEK</td>
</tr>

</table>

<table border="0" width="100%" cellpadding="0" cellspacing="1">
	{VAR:user_data_form}

<!-- SUB: ACC_ERROR -->
<tr>
	<td colspan="2" class="text" align="left"><font color="red">Palume Teil nõustuda Otto kataloogi tellimistingimustega!</font></a></td>
</tr>
<!-- END SUB: ACC_ERROR -->

<tr>
	<td class="text" align="left">Nõustun Otto kataloogi <a href='{VAR:baseurl}/856' target="_blank"> tellimistingimustega</a></td>
	<td><input type="checkbox" value="1" name="order_cond_ok" {VAR:order_cond_ok}></td>
</tr>
<tr>
	<td colspan="2" class="text">Palume Teil täita * tähistatud väljad. Samuti palume Teil täita vähemalt üks telefoninumbri väli.</td>
</tr>
<tr>
	<td colspan="2" align="left">
	<input type="submit" name="final_confirm_order" value="Kinnita tellimus" class="formbutton">
	</td>
</tr>
</table>

{VAR:reforb}
</form>

<span class="text"><a href='javascript:history.back()'>Soovin muuta tellimust</a></span>





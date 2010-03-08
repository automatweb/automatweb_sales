{VAR:show_thanku}
<!-- SUB: shop_table -->
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<tr>
	<td class="prodcarthead">Toote nimetus</td>
	<td class="prodcarthead">Kood</td>
	<td class="prodcarthead">Värvus</td>
	<td class="prodcarthead">Suurus</td>
	<td class="prodcarthead">Kogus</td>
	<td class="prodcarthead">Lehekülg</td>
	<td class="prodcarthead">Pilt</td>
	<td class="prodcarthead">Hind</td>
	<td class="prodcarthead">Summa</td>
	<td class="prodcarthead">&nbsp;</td>
	<td class="prodcarthead">&nbsp;</td>
</tr>
<!-- SUB: shop_cart_table -->
	<tr class="prodcartitem">
		<td>{VAR:name}</td>
		<td>{VAR:product_code}</td>
		<td>{VAR:product_color}</td>
		<td>{VAR:product_size}</td>
		<td>{VAR:product_count}</td>
		<td>{VAR:product_page}</td>
		<td>{VAR:product_image}</td>
		<td>{VAR:product_price}</td>
		<td>{VAR:product_sum} EEK</td>
		<td><a href="{VAR:editurl}">Muuda</a></td>
		<td><a href="{VAR:delete_url}">Kustuta</a></td>
	</tr>
<!-- END SUB: shop_cart_table -->
<tr class="prodcartitem">
	<td colspan="8"><b>Postikulu</b></td>
	<td colspan="3">{VAR:postal_fee} EEK</td>
</tr>
<tr class="prodcartitem">
	<td colspan="8"><b>Kokku</b></td>
	<td colspan="3">{VAR:totalsum} EEK</td>
</tr>
</table>
<!-- END SUB: shop_table -->
{VAR:add_items}
<!-- SUB: forward_link -->
<form method="GET">
	<input onClick="parent.location='{VAR:forwardurl}'" class="formbutton" type='button' name='' value='Kinnita tellimus' accesskey="s">
</form>
<!-- END SUB: forward_link -->
{VAR:add_persondata}
{VAR:show_confirm}
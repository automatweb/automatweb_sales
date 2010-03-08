<div id="applications-shop-shop_order-show">
<pre>
Tellimus nr: {VAR:id}
Tellija    : {VAR:person_name} / {VAR:company_name}
</pre>

<table class="cart_table">
	<tr>
		<th>Toode</th>
		<th>Kogus</th>
		<th>Hind</th>
	</tr>
	<!-- SUB: PROD -->
	<tr>
		<td>{VAR:name}</td>
		<td>{VAR:quant}</td>
		<td>{VAR:price}</td>
	</tr>
	<!-- END SUB: PROD -->
	<tr class="total">
		<td>&nbsp;</td>
		<td align="right">Kokku:&nbsp;</td>
		<td>{VAR:total}</td>
	</tr>
</table>
</div>
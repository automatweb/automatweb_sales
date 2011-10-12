<style type="text/css">.st238 {
	font-family: Verdana,Helvetica,sans-serif;
	font-family: 0;
	text-align: left;
	vertical-align: middle;
font-size: 11px;}
.st239 {
	font-family: Verdana,Helvetica,sans-serif;
	font-family: 0;
	font-size: 11px;
	text-align: left;
	vertical-align: middle;
BORDER-RIGHT: #d20007 1px solid; BORDER-TOP: #d20007 1px solid;  BORDER-LEFT: #d20007 1px solid; 
COLOR: #000000; 
BORDER-BOTTOM: #d20007 1px solid; }

.shoppingcarttext {
font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
font-size: 10px;
color: #737373;
line-height: 10px;
}
</style>

<form action='/reforb{VAR:ext}' method='POST' name='changeform' enctype='multipart/form-data'>
<span class="textred11">
	{VAR:product_name_error}
	{VAR:product_code_error}
	{VAR:product_price_error}
	{VAR:product_count_error}
</span>
<table style="font-family: Verdana, Arial, sans-serif;font-size: 10px;" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>Toote nimetus</td>
	<td>Kood</td>
	<td>Värvus</td>
	<td>Suurus</td>
	<td>Kogus</td>
	<td>Lehekülg</td>
	<td>Pilt</td>
	<td>Hind</td>
</tr>
<tr>
	<td>
	<input class="st239" type="text" id="name" name="orders[name]" size="15" value="{VAR:product_name_value}" >
	</td>
	<td>	
		<input class="st239" type="text" name="orders[product_code]" size="7" value="{VAR:product_code_value}" />
	</td>
	
	<td>
	<input class="st239" type="text" id="product_color" name="orders[product_color]" size="6" value="{VAR:product_color_value}" />
	</td>
	
		<td>
	<input class="st239" type="text" id="product_size" name="orders[product_size]" size="4" value="{VAR:product_size_value}" />	
	</td>
	
	<td>
	<input class="st239" type="text" id="product_count" name="orders[product_count]" size="3" value="{VAR:product_count_value}" />
	</td>
	
	<td>
		<input class="st239" type="text"  name="orders[product_page]" size="2" value="{VAR:product_page_value}" />	
	</td>
	<td>	
		<input class="st239" type="text" id="product_image" name="orders[product_image]" size="2" value="{VAR:product_image_value}" />
	</td>

	<td>
	<input class="st239" type="text" id="product_price" name="orders[product_price]" size="5" value="{VAR:product_price_value}" /> EEK
	</td>
</tr>
</table>

<br />
	<input class="formbutton" type='submit' name='' value='{VAR:add_change_caption}' class='aw04formbutton' accesskey="s">
<br /><br />
<input type='hidden' name='class' value='orders_order' />
<input type='hidden' name='action' value='add_to_cart' />
<input type='hidden' name='reforb' value='1' />
<input type='hidden' name='id' value='{VAR:id}' />
<input type='hidden' name='group' value='orderitems' />

</form>


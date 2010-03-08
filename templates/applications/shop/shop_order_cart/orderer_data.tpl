<form>
{VAR:customer_no_caption} : {VAR:customer_no} <br>
{VAR:birthday_caption} : {VAR:birthday} <br>
{VAR:lastname_caption} : {VAR:lastname} <br>
{VAR:firstname_caption} : {VAR:firstname} <br>
{VAR:address_caption} : {VAR:address} <br>
{VAR:index_caption} : {VAR:index} <br>
{VAR:city_caption} : {VAR:city} <br>
{VAR:email_caption} : {VAR:email} <br>
{VAR:homephone_caption} : {VAR:homephone} <br>
{VAR:workphone_caption} : {VAR:workphone} <br>
{VAR:mobilephone_caption} : {VAR:mobilephone} <br>
{VAR:work_caption} : {VAR:work} <br>
{VAR:workexperience_caption} : {VAR:workexperience} <br>
{VAR:profession_caption} : {VAR:profession} <br>
{VAR:personalcode_caption} : {VAR:personalcode} <br>


<form action="{VAR:baseurl}/index.aw" method="POST">
<input type="hidden" name="action" value="submit_order_data">
<input type="hidden" name="next_action" value="order_data">
<input type="hidden" name="class" value="shop_order_cart">
<input type="hidden" name="cart" value="{VAR:cart}">
<input type="submit" value="Edasi">


</form>
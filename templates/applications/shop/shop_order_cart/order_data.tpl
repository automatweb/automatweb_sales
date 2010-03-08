<form>
Kauba kättesaamiskoht 
<hannes>    SmartPOSTi pakiautomaat, 59.00 EEK
<hannes>       elukohajärgne postiasutus, 79.00 EEK 
<hannes> vali SmartPOSTi pakiautomaat
<hannes>  Makseviis 


<form action="{VAR:baseurl}/index.aw" method="POST">
<input type="hidden" name="action" value="submit_order_data">
<input type="hidden" name="class" value="shop_order_cart">
<input type="hidden" name="cart" value="{VAR:cart}">
<input type="submit" value="Edasi">


</form>
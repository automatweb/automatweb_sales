<html>
<head>

	<title>{VAR:LC_RFP_CONFIRMATION}</title>

<style>
	body { font-size: 9; }
	table { width: 100%; border: 1px solid white; border-collapse: collapse; }
	h1 { font-size: 14; }
	.wrapper { margin: 20mm 5mm 13mm 35mm; }
	.header { margin-bottom: 20px; }
	.header td { vertical-align: middle; }
	.data td.heading { font-weight: bold; text-align: center; }
	.data td { border: 1px solid white; }
	.contactdata td.heading { font-weight: bold; text-align: center; width: 50%; }
	.contactdata td { border: 1px solid white; width: 50%; }
	.price { font-size: 10; }
	.price-total { font-size: 12; }
</style>
</head>

<body>

<script type="text/php">
if(isset($pdf))
{
	$font = Font_Metrics::get_font("arial");
	$size = 9;
	$color = array("00", "00", "00");
	$text_height = Font_Metrics::get_font_height($font, $size);
	$w = $pdf->get_width();
	$h = $pdf->get_height();
	$y = $h - $text_height - 24;
	$text = "{PAGE_NUM}/{PAGE_COUNT}";
	$width = Font_Metrics::get_text_width("1/2", $font, $size);
	$x = $w / 2 - $width / 2;
	$pdf->page_text($x/10, $y, "{VAR:LC_RFP_CONFIRMATION} - {VAR:data_gen_function_name}", $font, $size, $color);
	$pdf->page_text($x, $y, $text, $font, $size, $color);
}
</script>

<div class="wrapper">

<table class="header">
<tr>
	<td><img src="{VAR:baseurl}/img/aw_logo_gray.gif" alt="Reval Hotels"></td>
	<td>
		<!-- rekvisiidid -->
	</td>
</tr>
<tr>
	<td colspan="2">
		<!-- SUB: CONFIRMATION_ONLY -->
			<h1>{VAR:LC_RFP_CONFIRMATION}</h1>
		<!-- END SUB: CONFIRMATION_ONLY -->
		<!-- SUB: OFFER_ONLY -->
			<h1>{VAR:LC_RFP_OFFER}</h1><br>
			<strong>{VAR:offer_preface}</strong>
			<br><br>
		<!-- END SUB: OFFER_ONLY -->

		<strong>{VAR:data_gen_function_name}</strong><br>
		{VAR:LC_RFP_ORG}: <strong>{VAR:data_company}</strong><br>
		{VAR:LC_RFP_CONTACT_PERSON}: <strong>{VAR:data_contact}</strong><br>
		{VAR:LC_RFP_PHONE}: <strong>{VAR:data_phone}</strong><br>
		{VAR:LC_RFP_DATE}: <strong>   {VAR:data_gen_arrival_date} - {VAR:data_gen_departure_date}</strong><br>
		<!-- SUB: HAS_ADDITIONAL_INFORMATION -->
		{VAR:LC_RFP_ADDINFO}: <strong>{VAR:additional_information}</strong><br>
		<!-- END SUB: HAS_ADDITIONAL_INFORMATION -->
		<table cellspacing="0" cellpadding="0"><tr>
		<td width="1%" style="vertical-align: top;"><span style="padding-right: 5px;">{VAR:LC_RFP_SIGNS}:</span></td>
		<td width="99%" style="vertical-align: top;"><strong>{VAR:pointer_text}</strong></td>
		</tr></table>
	</td>
</tr>
</table>

<!-- SUB: RESERVATIONS -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="{VAR:colspan}"><!--{VAR:title}-->{VAR:LC_RFP_CONFERENCE}</td>
</tr>
<tr bgcolor="#dddddd">
	<!-- SUB: HEADERS_PACKAGE -->
	<td>{VAR:LC_RFP_DATE_TIME}</td>
	<td>{VAR:LC_RFP_PACKAGE}</td>
	<td>{VAR:LC_RFP_ROOM_MEETING}</td>
	<td>{VAR:LC_RFP_SETUP}</td>
	<td>{VAR:LC_RFP_PERSONS}</td>
	<td>{VAR:LC_RFP_PRICE_PERSON}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
	<!-- END SUB: HEADERS_PACKAGE -->
	<!-- SUB: HEADERS_NO_PACKAGE -->
	<td>{VAR:LC_RFP_DATE_TIME}</td>
	<td>{VAR:LC_RFP_ROOM}</td>
	<td>{VAR:LC_RFP_SETUP}</td>
	<td>{VAR:LC_RFP_PERSONS}</td>
	<td>{VAR:LC_RFP_PRICE_DAY}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
	<!-- END SUB: HEADERS_NO_PACKAGE -->
</tr>
<!-- SUB: BRON -->
<tr>
	<!-- SUB: VALUES_PACKAGE -->
	<td>{VAR:datefrom} {VAR:timefrom} - {VAR:timeto}</td>
	<td>{VAR:package}</td>
	<td>{VAR:room}</td>
	<td>{VAR:tables}</td>
	<td>{VAR:people}</td>
	<td>{VAR:unitprice}</td>
	<td>{VAR:discount}</td>
	<td>{VAR:price}</td>
	<!-- END SUB: VALUES_PACKAGE -->
	<!-- SUB: VALUES_NO_PACKAGE -->
	<td>{VAR:datefrom} {VAR:timefrom} - {VAR:timeto}</td>
	<td>{VAR:room}</td>
	<td>{VAR:tables}</td>
	<td>{VAR:people}</td>
	<td>{VAR:unitprice}</td>
	<td>{VAR:discount}</td>
	<td>{VAR:price}</td>
	<!-- END SUB: VALUES_NO_PACKAGE -->
</tr>
<!-- END SUB: BRON -->
<tr>
	<td colspan="{VAR:total_colspan}" align="right"><strong>{VAR:LC_RFP_TOTAL}:</strong></td>
	<td class="price"><strong>{VAR:bron_totalprice}</strong> {VAR:data_currency}</td>
</tr>
<!-- SUB: HAS_ADDITIONAL_ROOM_INFORMATION -->
<tr>
	<td colspan="{VAR:bron_colspan}" align="left">
		<strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_room_information}
	</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_ROOM_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: RESERVATIONS -->

<!-- SUB: RESOURCES -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="6">{VAR:LC_RFP_TECH}</td>
</tr>
<tr bgcolor="#dddddd">
	<td>{VAR:LC_RFP_TIME}</td>
	<td>{VAR:LC_RFP_TECH}</td>
	<td>{VAR:LC_RFP_AMOUNT}</td>
	<td>{VAR:LC_RFP_PRICE_PER_AMOUNT}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
</tr>
<!-- SUB: RESOURCE_RESERVATION -->
<tr>
	<td colspan="7"><strong>{VAR:reservation_name}</strong></td>
</tr>
<!-- SUB: RESOURCE -->
<tr>
	<td>{VAR:res_from_hour}:{VAR:res_from_minute} - {VAR:res_to_hour}:{VAR:res_to_minute}</td>
	<td>{VAR:res_name} <em>{VAR:res_comment}</em></td>
	<td>{VAR:res_count}</td>
	<td>{VAR:res_price}</td>
	<td>{VAR:res_discount}</td>
	<td>{VAR:res_total}</td>
</tr>
<!-- END SUB: RESOURCE -->
<!-- END SUB: RESOURCE_RESERVATION -->
<tr>
	<td colspan="5" align="right"><strong>{VAR:LC_RFP_TOTAL}:</strong></td>
	<td class="price"><strong>{VAR:res_total}</strong> {VAR:data_currency}</td>
</tr>
<!-- SUB: HAS_ADDITIONAL_RESOURCE_INFORMATION -->
<tr>
	<td colspan="6" align="left"><strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_resource_information}</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_RESOURCE_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: RESOURCES -->

<!-- SUB: PRODUCTS_NO_PACKAGE -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="8">{VAR:LC_RFP_CATERING}</td>
</tr>
<tr bgcolor="#dddddd">
	<td>{VAR:LC_RFP_DATE_TIME}</td>
	<td>{VAR:LC_RFP_EVENT_PLACE}</td>
	<td>{VAR:LC_RFP_AMOUNT}</td>
	<td>{VAR:LC_RFP_MENU}</td>
	<td>{VAR:LC_RFP_COMMENT_CATERING}</td>
	<td>{VAR:LC_RFP_PCS_PRICE}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
</tr>
<!-- SUB: PRODUCTS_RESERVATION_NO_PACKAGE -->
<tr>
	<td colspan="8"><strong>{VAR:reservation_name}</strong></td>
</tr>
<!-- SUB: PRODUCT_NO_PACKAGE -->
<tr>
	<td><!--{VAR:prod_from_date}-->{VAR:prod_from_hour}:{VAR:prod_from_minute} - {VAR:prod_to_hour}:{VAR:prod_to_minute}</td>
	<td>{VAR:prod_event_and_room}</td>
	<td>{VAR:prod_count}</td>
	<td>{VAR:prod_prod}</td>
	<td>
	<!-- SUB: PROD_USERTA1 -->
		{VAR:prod_userta1}<br>
	<!-- END SUB: PROD_USERTA1 -->
		{VAR:prod_comment}
	</td>
	<td>{VAR:prod_price}</td>
	<td>{VAR:prod_discount}</td>
	<td>{VAR:prod_sum}</td>
</tr>
<!-- END SUB: PRODUCT_NO_PACKAGE -->
<!-- END SUB: PRODUCTS_RESERVATION_NO_PACKAGE -->
<tr>
	<td colspan="7" align="right"><strong>{VAR:LC_RFP_TOTAL}:</strong></td>
	<td class="price"><strong>{VAR:prod_total}</strong> {VAR:data_currency}</td>
</tr>
<!-- SUB: HAS_ADDITIONAL_CATERING_INFORMATION -->
<tr>
	<td colspan="8" align="left"><strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_catering_information}</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_CATERING_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: PRODUCTS_NO_PACKAGE -->

<!-- SUB: PRODUCTS_PACKAGE -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="5">{VAR:LC_RFP_CATERING}</td>
</tr>
<tr bgcolor="#dddddd">
	<td>{VAR:LC_RFP_DATE_TIME}</td>
	<td>{VAR:LC_RFP_EVENT_PLACE}</td>
	<td>{VAR:LC_RFP_AMOUNT}</td>
	<td>{VAR:LC_RFP_MENU}</td>
	<td>{VAR:LC_RFP_COMMENT_CATERING}</td>
</tr>
<!-- SUB: PRODUCTS_RESERVATION -->
<tr>
	<td colspan="5"><strong>{VAR:reservation_name}</strong></td>
</tr>
<!-- SUB: PRODUCT_PACKAGE -->
<tr>
	<td><!--{VAR:prod_from_date}-->{VAR:prod_from_hour}:{VAR:prod_from_minute} - {VAR:prod_to_hour}:{VAR:prod_to_minute}</td>
	<td>{VAR:prod_event_and_room}</td>
	<td>{VAR:prod_count}</td>
	<td>{VAR:prod_prod}</td>
	<td>
	<!-- SUB: PROD_USERTA1 -->
		{VAR:prod_userta1}
	<!-- END SUB: PROD_USERTA1 -->
		<br>{VAR:prod_comment}
	</td>
</tr>
<!-- END SUB: PRODUCT_PACKAGE -->
<!-- END SUB: PRODUCTS_RESERVATION -->
<!-- SUB: HAS_ADDITIONAL_CATERING_INFORMATION -->
<tr>
	<td colspan="{VAR:colspan}" align="left"><strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_catering_information}</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_CATERING_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: PRODUCTS_PACKAGE -->

<!-- SUB: HOUSING -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="8">{VAR:LC_RFP_ACOMMODATION}</td>
</tr>
<tr bgcolor="#dddddd">
	<td>{VAR:LC_RFP_FROM}</td>
	<td>{VAR:LC_RFP_TO}</td>
	<td>{VAR:LC_RFP_ROOMTYPE}</td>
	<td>{VAR:LC_RFP_ROOMS_NUMBER}</td>
	<td>{VAR:LC_RFP_PERSONS}</td>
	<td>{VAR:LC_RFP_PRICE_ROOM}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
</tr>
<!-- SUB: ROOMS -->
<tr>
	<td>{VAR:hs_from}</td>
	<td>{VAR:hs_to}</td>
	<td>{VAR:hs_type} <em>{VAR:hs_comment}</em></td>
	<td>{VAR:hs_rooms}</td>
	<td>{VAR:hs_people}</td>
	<td>{VAR:hs_price}</td>
	<td>{VAR:hs_discount}</td>
	<td>{VAR:hs_sum}</td>
</tr>
<!-- END SUB: ROOMS -->
<tr>
	<td colspan="7" align="right"><strong>{VAR:LC_RFP_TOTAL}:</strong></td>
	<td class="price"><strong>{VAR:hs_total}</strong> {VAR:data_currency}</td>
</tr>
<!-- SUB: HAS_ADDITIONAL_HOUSING_INFORMATION -->
<tr>
	<td colspan="8" align="left"><strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_housing_information}</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_HOUSING_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: HOUSING -->

<!-- SUB: ADDITIONAL_SERVICES -->
<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="7">{VAR:LC_RFP_ADD_SERVICES}</td>
</tr>
<tr bgcolor="#dddddd">
	<td>{VAR:LC_RFP_DATE_TIME}</td>
	<td>{VAR:LC_RFP_SERVICE}</td>
	<td>{VAR:LC_RFP_AMOUNT}</td>
	<td>{VAR:LC_RFP_PRICE}</td>
	<td>{VAR:LC_RFP_DISC}</td>
	<td>{VAR:LC_RFP_TOTAL_PRICE}</td>
	<td>{VAR:LC_RFP_COMMENT}</td>
</tr>
<!-- SUB: SERVICE -->
<tr>
	<td>{VAR:as_date} {VAR:as_time}</td>
	<td>{VAR:service}</td>
	<td>{VAR:amount}</td>
	<td>{VAR:price}</td>
	<td>{VAR:discount}</td>
	<td>{VAR:sum}</td>
	<td>{VAR:comment}</td>
</tr>
<!-- END SUB: SERVICE -->
<tr>
	<td colspan="5" align="right"><strong>{VAR:LC_RFP_TOTAL}:</strong></td>
	<td colspan="2" class="price"><strong>{VAR:as_total}</strong> {VAR:data_currency}</td>
</tr>
<!-- SUB: HAS_ADDITIONAL_SERVICES_INFORMATION -->
<tr>
	<td colspan="7" align="left"><strong>{VAR:LC_RFP_ADDINFO}:</strong> {VAR:additional_services_information}</td>
</tr>
<!-- END SUB: HAS_ADDITIONAL_SERVICES_INFORMATION -->
</table>
</div>

<br><br>
<!-- END SUB: ADDITIONAL_SERVICES -->

<div style="page-break-inside: avoid;">
<table class="data" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#dddddd">
	<td class="heading" colspan="3">{VAR:LC_RFP_ACCOUNT_INFO}</td>
</tr>
<tr>
	<td width="33%">{VAR:LC_RFP_ORG}: <strong>{VAR:data_billing_company}</strong></td>
	<td colspan="2" width="67%">{VAR:LC_RFP_CONTACT_PERSON}: <strong>{VAR:data_billing_contact}</strong></td>
</tr>
<tr>
	<td width="33%">{VAR:LC_RFP_STREET}: <strong>  {VAR:data_billing_street}</strong></td>
	<td width="33%">{VAR:LC_RFP_CITY}: <strong>{VAR:data_billing_city}</strong></td>
	<td width="34%">{VAR:LC_RFP_INDEX}: <strong>{VAR:data_billing_zip}</strong></td>
</tr>
<tr>
	<td width="33%">{VAR:LC_RFP_STATE}:<strong> {VAR:data_billing_state}</strong></td>
	<td colspan="2" width="67%">{VAR:LC_RFP_COUNTRY}: <strong>{VAR:data_billing_country}</strong></td>
</tr>
<tr>
	<td width="33%">{VAR:LC_RFP_PHONE}: <strong>{VAR:data_billing_phone}</strong></td>
	<td width="33%">{VAR:LC_RFP_FAX}: <strong>{VAR:data_billing_fax}</strong></td>
	<td width="34%">{VAR:LC_RFP_EMAIL}: <strong>{VAR:data_billing_email}</strong></td>
</tr>
<tr>
	<td width="33%">{VAR:LC_RFP_PAYMENT_TYPE}: <strong>{VAR:payment_method}</strong></td>
	<td colspan="2" width="67%">{VAR:LC_RFP_REMARKS}: <strong>   {VAR:data_billing_comment}</strong></td>
</tr>
<tr>
	<td bgcolor="#888888" class="price-total" colspan="3"><font color="#ffffff">{VAR:LC_RFP_ORIENT_PRICE}: <strong>{VAR:totalprice}</strong> &nbsp; {VAR:data_currency} ({VAR:LC_RFP_VAT})</font></td>
</tr>
</table>
</div>

<!-- SUB: OFFER_ONLY_2 -->
	<p>{VAR:offer_price_comment}</p>
	<p>{VAR:LC_RFP_VALID}: {VAR:offer_expire_date}</p>
<!-- END SUB: OFFER_ONLY_2 -->

<table width="95%">
<tr>
	<td><h2>{VAR:LC_RFP_CONDITIONS}</h2></td>
</tr>
<tr>
	<td><strong>{VAR:LC_RFP_CONDITIONS2}</strong></td>
</tr>
<tr>
	<td>{VAR:accomondation_terms}</td>
</tr>
<tr>
	<td>{VAR:cancel_and_payment_terms}</td>
</tr>
</table>

<div style="page-break-inside: avoid;">
<table class="contactdata" width="100%" cellspacing="0" cellpadding="5" bgcolor="#efefef">
<tr bgcolor="#aaaaaa">
	<td class="heading" colspan="2">{VAR:LC_RFP_CONTACT_INFO}</td>
</tr>
<tr>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_CUSTOMER_CONTACT}</td>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_HOTEL_CONTACT_DATA}</td>
</tr>
<tr>
	<td>{VAR:LC_RFP_ORG}: <strong>{VAR:data_company}</strong></td>
	<td>{VAR:LC_RFP_HOTEL}: <strong>O&Uuml; Struktuur Meedia</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_STREET}: <strong>   {VAR:data_street}</strong></td>
	<td>{VAR:LC_RFP_STREET}: <strong>   Narva mnt 158b</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_CITY}: <strong>{VAR:data_city}</strong></td>
	<td>{VAR:LC_RFP_CITY}: <strong>Tallinn</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_INDEX}: <strong>{VAR:data_zip}</strong></td>
	<td>{VAR:LC_RFP_INDEX}: <strong>11317</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_COUNTRY}: <strong>{VAR:data_country}</strong></td>
	<td>{VAR:LC_RFP_COUNTRY}: <strong>Eesti</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_PHONE}: <strong>{VAR:data_phone}</strong></td>
	<td>{VAR:LC_RFP_PHONE}: <strong>+372 6 558 334</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_FAX}: <strong>{VAR:data_fax}</strong></td>
	<td>{VAR:LC_RFP_FAX}: <strong></strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_EMAIL}: <strong>{VAR:data_email}</strong></td>
	<td>{VAR:LC_RFP_EMAIL}: <strong>{VAR:current_email}</strong></td>
</tr>
<tr>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_CUSTOMER_NAME_SIGN}</td>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_HOTEL_NAME_SIGN}</td>
</tr>
<tr>
	<td>{VAR:LC_RFP_CONTACT_PERSON}: <strong>{VAR:data_contact}</strong></td>
	<td>{VAR:LC_RFP_HOTEL_CONTACT}: <strong>{VAR:contactperson}</strong></td>
</tr>
<tr>
	<td>{VAR:LC_RFP_SIG}:</td>
	<td>{VAR:LC_RFP_SIG}:</td>
</tr>
<tr>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_DATE}</td>
	<td class="heading" bgcolor="#dddddd">{VAR:LC_RFP_DATE}</td>
</tr>
<tr>
	<td>{VAR:LC_RFP_CONFIRMATION_RECEIVED}: </td>
	<td>{VAR:LC_RFP_CONFIRMATION_SENT}: <strong>{VAR:send_date}</strong></td>
</tr>
</table>
</div>

</div>

</body>
</html>

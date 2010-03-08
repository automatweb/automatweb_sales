<body id="print">
<script type="text/javascript">
window.onload = function ()
{
	window.print();
}
</script>


<div class="navLink">
<a class="navLink" href="{VAR:return_url}">Tagasi</a>
</div>

<hr width="100%" size="1" noshade class="hr_printview">


<!-- SUB: property_cell -->
<td width="50%" class="txt11px">{VAR:prop_caption}: <strong>{VAR:prop_value}</strong> {VAR:prop_suffix}</td>
<!-- END SUB: property_cell -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<!-- SUB: property_row -->
<tr valign="top">
	{VAR:property_cells}
</tr>
<!-- END SUB: property_row -->
</table>


	



<table id="detailotsing_printview">
<tr class="pealkiri">
	<td>
	<table style="margin-bottom: 5px;">
	<tr>
		<td id="pealkiri"><strong>{VAR:address}</strong></td>
		<td id="id">ID: {VAR:city24_object_id}</td>
	</tr>
	</table>
	</td>
</tr><!-- pealkiri -->

<tr class="alapealkiri">
	<td>
	<table>
	<tr>
		<td class="pealkiri" style="padding-right: 10px;">Objekti andmed</td>
		<td class="joon"><hr size="1" noshade></td>
	</tr>
	</table>
	</td>
</tr><!-- alapealkiri -->

<tr class="sisu">
	<td>
	<table>
	<tr>
		<td style="padding-right: 30px;">
		<!-- SUB: re_transaction_type -->
		{VAR:caption}: <strong>{VAR:value}, </strong>  
		<!-- END SUB: re_transaction_type -->
		<strong>{VAR:class_name}</strong><br>
		
		<!-- SUB: re_floor -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: re_floor -->

		<!-- SUB: re_usage_purpose -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: re_usage_purpose -->

		<!-- SUB: re_condition -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: re_condition -->

		<!-- SUB: re_total_floor_area -->
		{VAR:caption}: <strong>{VAR:value}</strong> m<sup>2</sup><br>
		<!-- END SUB: re_total_floor_area -->
		</td>
		

		<td style="padding-right: 30px;">
		
		<!-- SUB: re_number_of_storeys -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: re_number_of_storeys -->

		<!-- SUB: re_year_built -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: re_year_built -->
		
		<!-- SUB: re_transaction_price -->
		{VAR:caption}: <strong>{VAR:value}</strong> kr<br>
		<!-- END SUB: re_transaction_price -->
		</td>
	</tr>
	</table>
	<br><br>

	<!-- SUB: extras -->
	<strong>Lisaandmed:</strong> {VAR:value}<br>
	<br>
	<!-- END SUB: extras -->
	
	<table class="pildid_joondusega_alla">
	<tr>
	<!-- SUB: pictures -->
	<td><img src="{VAR:picture_url}" width="280"></td>
	<!-- END SUB: pictures -->
	</tr>
	</table><!-- pildid_joondusega_alla -->
	
	<table class="kaart_ja_ikoon">
	<tr>
	<td class="esimene">
	<!-- SUB: re_picture_icon -->
	<img src="{VAR:value}" alt="" width="118" height="88" border="0">
	<!-- END SUB: re_picture_icon -->
	</td>
		
	<td class="teine">
	<!-- SUB: re_map_url -->
	<img src="{VAR:value}" id="kaart" style="width: 118px" border="0">
	<!-- END SUB: re_map_url -->
	</td>
	</tr>
	</table><!-- kaart_ja_ikoon -->

	
	</td>
</tr><!-- sisu -->

<tr class="alapealkiri">
	<td>
	<table>
	<tr>
		<td class="pealkiri" style="padding-right: 10px; ">Maaklerid</td>
		<td class="joon"><hr size="1" noshade></td>
	</tr>
	</table>
	</td>
</tr><!-- alapealkiri -->


<tr class="sisu">
	<td>
	<table style="width: 100%;">
	<tr>
		<!-- SUB: re_agent_picture_url -->
		<td class="foto"><img src="{VAR:value}" alt="" border="0"></td>
		<!-- END SUB: re_agent_picture_url -->
		<td class="isiku_kontakt" style="padding: 12px 11px;">

		<!-- SUB: re_agent_name -->
		<strong>{VAR:value}</strong><br> 
		<!-- END SUB: re_agent_name -->

		<!-- SUB: re_agent_rank -->
		<strong>{VAR:value}</strong><br> 
		<!-- END SUB: re_agent_rank -->

		<br>
		
		<!-- SUB: re_agent_phone -->
		Telefon: <span class="strong">{VAR:value}</span><br> 
		<!-- END SUB: re_agent_phone -->

		<!-- SUB: re_agent_email -->
		E-post: <span class="strong">{VAR:value}</span><br> 
		<!-- END SUB: re_agent_email -->
		</td><!-- isiku_kontakt -->

	
		<td id="logo"><div><img src="{VAR:company_logo_url}" alt="{VAR:company_logo_alt}" border="0"></div></td>
	</tr>
	</table>
	</td>
</tr><!-- sisu -->


<tr class="sisu">
	<!-- SUB: re_agent2_picture_url -->
		<td class="foto"><img src="{VAR:value}" alt=""></td>
		<!-- END SUB: re_agent2_picture_url -->
		
		<td class="isiku_kontakt" style="padding: 12px 11px;">
		<!-- SUB: re_agent2_name -->
		<strong>{VAR:value}</strong><br>
		<!-- END SUB: re_agent2_name -->

		<!-- SUB: re_agent2_rank -->
		<strong>{VAR:value}</strong><br> 
		<!-- END SUB: re_agent2_rank -->
		<br>
		
		<!-- SUB: re_agent2_phone -->
		Telefon: <strong>{VAR:value}</strong><br> 
		<!-- END SUB: re_agent2_phone -->

		<!-- SUB: re_agent2_email -->
		E-post: <strong>{VAR:value}</strong><br> 
		<!-- END SUB: re_agent2_email -->
		</td><!-- isiku_kontakt -->
</tr><!-- sisu --><!-- sisu -->


</table><!-- detailotsing -->


</body>
<table id="objekt101">
<tr class="sisu">
	<td class="vasak_tulp" style="padding-right: 12px;"><a target="_blank" href="{VAR:open_pictureview_url}">
	<!-- SUB: picture_icon -->
	<img src="{VAR:value}" alt="" width="118" height="88" border="0">
	<!-- END SUB: picture_icon -->
	</a><br>
	<span class="pilte_kokku"><a href="{VAR:open_pictureview_url}" target="_blank">Pilte kokku: {VAR:picture_count}</a> </span>
	<br><a href="javascript:void(0);">
	<!-- SUB: map_url -->
	<img src="{VAR:value}" id="kaart" style="width: 118px" border="0">
	<!-- END SUB: map_url -->
	</a>
	</td><!-- vasak_tulp -->
	<td class="parem_tulp">
	
	<table>
	<tr>
		<td style="padding-right: 20px;">
		<!-- SUB: transaction_type -->
		{VAR:caption}: <strong>{VAR:value}, </strong>  
		<!-- END SUB: transaction_type -->
		<strong>{VAR:class_name}</strong><br>
		
		
		<!-- SUB: land_use -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: land_use -->
		
		<!-- SUB: transaction_price -->
		{VAR:caption}: <strong>{VAR:value} kr</strong>
		<!-- END SUB: transaction_price -->
		</td>
		<td>
		<!-- SUB: legal_status -->
		{VAR:caption}: <strong>{VAR:value}</strong><br>
		<!-- END SUB: legal_status -->
		
		<!-- SUB: property_area -->
		{VAR:caption}: <strong>{VAR:value}</strong> m<sup>2</sup><br>
		<!-- END SUB: property_area -->
		
		<!-- SUB: transaction_price2 -->
		{VAR:caption}: <strong>{VAR:value}</strong> kr<br>
		<!-- END SUB: transaction_price2 -->
		</td>
	</tr>
	</table>
	<br>
	<strong>Lisaandmed:</strong> {VAR:extras} <br><br>
	 
	<!-- SUB: additional_info_et -->
	<strong>Info:</strong> {VAR:value} <br><br>
	<!-- END SUB: additional_info_et -->
	 
	<strong>Kontakt:</strong>
	
	<!-- SUB: agent_name -->
	{VAR:value},
	<!-- END SUB: agent_name -->
	
	<!-- SUB: agent_phone -->
	{VAR:value},
	<!-- END SUB: agent_phone -->
	
	<!-- SUB: agent_email -->
	<a href="mailto:{VAR:value}">{VAR:value}</a>
	<!-- END SUB: agent_email -->
	<br><br>
	 
	<span class="teised_majad"><a href="{VAR:show_agent_properties_url}">N�ita ka selle maakleri teisi objekte</a></span>  <br><br>
	 
	<!-- SUB: city24_object_id -->
	ID: {VAR:value} <br><br>
	<!-- END SUB: city24_object_id -->
		
	
	
	</td><!-- parem_tulp -->
</tr><!-- sisu -->
<tr>
	<td class="tagasi"><a href="#"><img src="img/tagasi.gif" alt="" width="56" height="17" border="0"></a></td>
	<td><img src="img/objekt101_joon3.gif" alt="" width="1" height="17" border="0"></td>
</tr>
<tr>
	<td style="background: url(img/objekt101_joon2.gif) no-repeat top right; "></td>
	<td style="background: url(img/objekt101_joon.gif) no-repeat top;">
	<a href="{VAR:open_printview_url}&print=1" target="_blank"><img src="img/tryki.gif" alt="" width="57" height="18" border="0"></a><a href="{VAR:baseurl}/?class=document&action=send&section={VAR:docid}"><img src="img/saada_s6brale.gif" alt="" width="114" height="18" border="0"></a></td>
</tr>
</table>

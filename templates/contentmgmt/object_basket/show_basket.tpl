
<table width="630" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding-bottom: 30px;" align="left" valign="bottom"><font class="bigyello">Kalendermärkmik</font><br><font class="svmain">Lisatud <strong>{VAR:total_count}</strong> kalendrikirjet

</td>
    <td style="padding-bottom: 30px;" align="right" valign="middle"><a href="{VAR:ical_url}">[ anna iCal ]</a><a href="{VAR:remove_all_url}" target="_self">[ eemalda kõik ]</a><a href="{VAR:print_url}" target="_blank">[ prindi kõik ]</a></td>
  </tr>
<!-- SUB: HAS_LINES -->
  <!-- SUB: LINE -->
  <tr>
    <td colspan="2" bgcolor="#000000"><img src="/img/pix.gif" height="1" width="630"></td>
  </tr>
<tr>
    <td style="padding-left: 0px; padding-right: 0px; padding-top: 10px; padding-bottom: 0px;">			 	<font class="bigblak">{VAR:CL_CALENDAR_EVENT.name}</font>

 <table border="0" cellspacing="0" cellpadding="0" style="margin-top: 5px;">
  <tr>
    <td valign="top" style="padding-right: 15px; padding-top: 1px;"><font class="vmain"><strong>Aeg:</strong> </td>
    <td valign="top" style="padding-top: 1px;"><font class="vmain"><?php
$d = {VAR:CL_CALENDAR_EVENT.start1};
echo date('d.m.Y, H:i',$d);
?></td>
  </tr>
</table><br>
	</td>
    <td style="padding-left: 0px; padding-right: 0px; padding-top: 1px; padding-bottom: 1px;" valign="top" align="right"><a href="{VAR:remove_single_url}" target="_self"><img src="/img/est.butt.eemalda.gif" border="0" alt="[eemalda märkmikust]"></a></td>
  </tr>
  <!-- END SUB: LINE -->
<!-- END SUB: HAS_LINES -->
<!-- SUB: NO_LINES -->
<!-- END SUB: NO_LINES -->

</table>

	<!-- TABS -->
	<table border="0" cellspacing="1" cellpadding="0" style="margin-top: 10px; margin-bottom: 20px;" >
  <tr>
	<!-- SUB: SEL_PAGE -->
    <td style="padding: 4px;" valign="middle" bgcolor="#DFDFDF"><font class="svmain"><strong>{VAR:page_from}-{VAR:page_to}</strong></font></td>
	<!-- END SUB: SEL_PAGE -->

	<!-- SUB: PAGE -->
    <td style="padding: 4px;" valign="middle" bgcolor="#008FE8"><a href="{VAR:page_link}" target="_self" class="whit"><strong>{VAR:page_from}-{VAR:page_to}</strong></a></td>
	<!-- END SUB: PAGE -->

  </tr>
</table>
	<!-- TABS END -->

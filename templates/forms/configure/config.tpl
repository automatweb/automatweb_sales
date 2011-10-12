<form name="aa" action="reforb{VAR:ext}" method="POST">
<!--tabelraam-->
<table width="100%" cellspacing="0" cellpadding="1">
	<tr>
		<td class="tableborder">
			<!--tabelshadow-->
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="1" class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
					<td class="tableshadow"><IMG SRC="{VAR:baseurl}/automatweb/images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
						<!--tabelsisu-->
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="tableinside">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="2"><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""></td>
											<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="javascript:this.document.aa.submit();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="{VAR:LC_MENUEDIT_SAVE}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>











<table border=0 cellpadding=1 cellspacing=1>
<!-- SUB: TYPE -->
	<tr>
		<td colspan="2" align=left class="celltext">{VAR:type_name}</td>
		<td><input type='checkbox' NAME='types[{VAR:type}]' VALUE='1' {VAR:type_check}></td>
	</tr>
	<!-- SUB: SUBTYPE -->
	<tr>
		<td class="celltext">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="celltext">{VAR:subtype_name}</td>
		<td><input type='checkbox' NAME='subtypes[{VAR:type}][{VAR:subtype}]' VALUE='1' {VAR:subtype_check}></td>
	</tr>
	<!-- END SUB: SUBTYPE -->

<!-- END SUB: TYPE -->
</table>
{VAR:reforb}
</form>
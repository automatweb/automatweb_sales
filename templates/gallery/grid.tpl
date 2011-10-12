
<form action='reforb{VAR:ext}' METHOD=POST enctype='multipart/form-data' name="gallery">

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
											<td><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""><br><a href="javascript:this.document.gallery.submit();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('save','','{VAR:baseurl}/automatweb/images/blue/awicons/save_over.gif',1)"><img name="save" alt="{VAR:LC_MENUEDIT_SAVE}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/save.gif" width="25" height="25"></a><img SRC="{VAR:baseurl}/automatweb/images/blue/awicons/seperator.gif" width="6" height="25"><a href="{VAR:add_page}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('new','','{VAR:baseurl}/automatweb/images/blue/awicons/new_over.gif',1)"><img name="new" alt="{VAR:LC_GALLERY_ADD}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/new.gif" width="25" height="25"></a><a href="{VAR:del_page}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('delete','','{VAR:baseurl}/automatweb/images/blue/awicons/delete_over.gif',1)"><img name="delete" alt="{VAR:LC_GALLERY_DEL_PAGE}" border="0" SRC="{VAR:baseurl}/automatweb/images/blue/awicons/delete.gif" width="25" height="25"></a><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="4" HEIGHT="1" BORDER=0 ALT=""><br><IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="2" HEIGHT="2" BORDER=0 ALT=""></td>
											<td valign="bottom">
												<table border=0 cellpadding=0 cellspacing=0>
													<tr>
														<!-- SUB: PAGE -->
														<td class="tab"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tab" valign="bottom"><a href='{VAR:to_page}'>{VAR:page}</a></td><td class="tab"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
														<!-- END SUB: PAGE -->

														<!-- SUB: SEL_PAGE -->
														<td class="tabsel"><IMG SRC="images/blue/tab_left_begin.gif" WIDTH="8" HEIGHT="20" BORDER=0 ALT=""></td>
														<td nowrap background="{VAR:baseurl}/automatweb/images/blue/tab_taust.gif" class="tabsel" valign="bottom">{VAR:page}</td><td class="tabsel"><IMG SRC="images/blue/tab_right.gif" WIDTH="6" HEIGHT="20" BORDER=0 ALT=""></td>
														<!-- END SUB: SEL_PAGE -->
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
		</td>
	</tr>
</table>

<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr><td class="tableborder">



<span class="celltext">


<input type='checkbox' name='is_slideshow' value='1' {VAR:is_slideshow}> {VAR:LC_GALLERY_IS_GALLERY_SLIDESHOW}
 <input type='checkbox' name='is_automatic_slideshow' value='1' {VAR:is_automatic_slideshow}> {VAR:LC_GALLERY_IS_SLIDESHOW_AUTO}<br>
<IMG SRC="{VAR:baseurl}/images/trans.gif" WIDTH="1" HEIGHT="7" BORDER=0 ALT=""><br>
<input type='hidden' NAME='MAX_FILE_SIZE' VALUE='1000000'>

</span>

<table border=0 cellpadding=2 bgcolor="#FFFFFF" cellspacing=1>

<!-- SUB: LINE -->
<tr>
	<!-- SUB: CELL -->
	<td align=center class="aste01">
		<table border=0 cellpadding=1 cellspacing=1>
			<!-- SUB: HAS_IMG -->
			<tr>
				<td colspan=2 align=center class="celltext"><img src='{VAR:imgurl}'><input type='checkbox' name='erase_{VAR:row}_{VAR:col}' value=1>{VAR:LC_GALLERY_DELETE}</td>
			</tr>
			<!-- END SUB: HAS_IMG -->

			<!-- SUB: BIG -->
			<tr>
				<td colspan=2 align=center class="celltext"><a href='{VAR:bigurl}'>{VAR:LC_GALLERY_IMAGE}</a></td>
			</tr>
			<!-- END SUB: BIG -->
			<tr>
				<td align=right class="celltext">{VAR:LC_GALLERY_SIGNATURE}:</td>
				<td><input type='text' NAME='caption_{VAR:row}_{VAR:col}' VALUE='{VAR:caption}' size="28" class="formtext"></td>
			</tr>
			<tr>
				<td align=right class="celltext">{VAR:LC_GALLERY_DATE}:</td>
				<td><input type='text' NAME='date_{VAR:row}_{VAR:col}' VALUE='{VAR:date}' size=10 class="formtext"></td>
			</tr>
			<tr>
				<td align=right class="celltext">V&auml;ike pilt:</td>
				<td><input type='file' NAME='tn_{VAR:row}_{VAR:col}' class="formfile"></td>
			</tr>
			<tr>
				<td align=right class="celltext">Tekstilink:</td>
				<td><input type="checkbox" name="has_textlink_{VAR:row}_{VAR:col}" {VAR:has_textlink}><input type='text' NAME='textlink_{VAR:row}_{VAR:col}' class="formtext" value="{VAR:textlink}" size="26"></td>
			</tr>
			<tr>
				<td align=right class="celltext">{VAR:LC_GALLERY_IMAGE}:</td>
				<td><input type='file' NAME='im_{VAR:row}_{VAR:col}' class="formfile"></td>
			</tr>
			<tr>
				<td align=right class="celltext">Jrk:</td>
				<td><input type='text' NAME='ord_{VAR:row}_{VAR:col}' class="formtext" size="3" value='{VAR:ord}'></td>
			</tr>
			<!-- SUB: IS_AUTOMATIC_GAL -->
			<tr>
				<td align=right class="celltext">Link:</td>
				<td><input type='text' NAME='glink_{VAR:row}_{VAR:col}' class="formtext" value='{VAR:glink}'></td>
			</tr>
			<!-- END SUB: IS_AUTOMATIC_GAL -->
		</table>
	</td>
	<!-- END SUB: CELL -->
</tr>
<!-- END SUB: LINE -->
</table>
<!--<input type='submit' VALUE='Save' class="formbutton">-->
{VAR:reforb}
</form>
<table border=0 cellpadding=0 cellspacing=3>
<tr>
<Td class="celltext">
<form action='orb{VAR:ext}' METHOD=GET>
<input type='submit' VALUE='Add' class="formbutton"> <input type='text' NAME='rows' SIZE=2 class="formtext"> {VAR:LC_GALLERY_ROWS}.
<input type='hidden' NAME='action' VALUE='add_row'>
<input type='hidden' NAME='class' VALUE='gallery'>
<input type='hidden' NAME='id' VALUE='{VAR:id}'>
<input type='hidden' NAME='page' VALUE='{VAR:page}'>
</form>
</td>
<Td class="celltext">
<form action='orb{VAR:ext}' METHOD=GET>
<input type='submit' VALUE='Add' class="formbutton"> <input type='text' NAME='cols' SIZE=2 class="formtext"> {VAR:LC_GALLERY_COLUMNS}.
<input type='hidden' NAME='action' VALUE='add_col'>
<input type='hidden' NAME='class' VALUE='gallery'>
<input type='hidden' NAME='id' VALUE='{VAR:id}'>
<input type='hidden' NAME='page' VALUE='{VAR:page}'>
</form>
</td>
</tr>
<tr>
<td>

</td>
<td>

</td>
</tr>
</table>

<span class="celltext"><b>

<a href='{VAR:del_row}'>{VAR:LC_GALLERY_DEL_ROW}</a> |

<a href='{VAR:del_col}'>{VAR:LC_GALLERY_DEL_COL}</a>

</b></span>

</td></tr></table>

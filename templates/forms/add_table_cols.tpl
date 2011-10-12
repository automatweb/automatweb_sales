<form action='reforb{VAR:ext}' method=post name="add">
	<!--tabelraam-->
	<table width="100%" cellspacing="0" cellpadding="1">
		<tr>
			<td class="tableborder">
				<!--tabelshadow-->
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="1" class="tableshadow"><IMG SRC="images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""></td>
						<td class="tableshadow"><IMG SRC="images/trans.gif" WIDTH="1" HEIGHT="1" BORDER=0 ALT=""><br>
							<!--tabelsisu-->
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td class="tableinside" height="29">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="5"><IMG SRC="images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>
												<td>
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td class="icontext" align="center"><input type='image' src="{VAR:baseurl}/automatweb/images/blue/big_save.gif" width="32" height="32" border="0" VALUE='submit' CLASS="small_button"><br><a href="javascript:document.add.submit()">Salvesta</a></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<br>
										<table class="aste01" cellpadding=3 cellspacing=1 border=0>
											<tr>
												<td class="celltext" colspan="2">{VAR:menu}</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">Tulpade arv: <input type='text' NAME='num_cols' VALUE='{VAR:num_cols}' class="formtext"></td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">Tulbad:</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">
													<table border="0">
														<!-- SUB: ROW -->
														<tr>
															<!-- SUB: TD -->
															<td class="celltext" valign="top">{VAR:content}</td>
															<td class="celltext"><img src='{VAR:baseurl}/automatweb/images/1px_black.gif' width="1" height="100%"></td>
															<!-- END SUB: TD -->
														</tr>
														<!-- END SUB: ROW -->
													</table>
												</td>
											</tr>

											<!-- SUB: COL_HEADER -->
											Tulba pealkiri: <br><input type="text" name="cols[{VAR:col_id}][lang_title][{VAR:lang_id}]" VALUE="{VAR:lang_title}" class="formtext"><br>
											J‰rjekorranumber: <br><input type="text" name="cols[{VAR:col_id}][ord]" class="formtext" VALUE="{VAR:ord}" size="3">
											<!-- END SUB: COL_HEADER -->

											<!-- SUB: SEL_ELS -->
											Vali elemendid:<Br>
											<select class="formselect" name="cols[{VAR:col_id}][els][]" multiple size="10">{VAR:els}</select><br>
											<!-- END SUB: SEL_ELS -->

											<!-- SUB: SEL_ORDER_FORM -->
											Vali tellimise url:<Br>
											<input type="text" class="formtext" name="cols[{VAR:col_id}][order_form]" value="{VAR:order_form}">
											<!-- END SUB: SEL_ORDER_FORM -->
									
											<!-- SUB: IS_TYPE_TEXT -->
											Tekst:<Br>
											<input type="text" class="formtext" name="cols[{VAR:col_id}][is_type_text]" value="{VAR:is_type_text}">
											<!-- END SUB: IS_TYPE_TEXT -->

											<!-- SUB: SEL_ALIAS -->
											Vali alias:<Br>
											<select class="formselect" name="cols[{VAR:col_id}][alias][]" multiple size="5">{VAR:aliases}</select>
											<!-- END SUB: SEL_ALIAS -->

											<!-- SUB: SEL_GRPS -->
											Vali grupid:<Br>
											<select class="formselect" name="cols[{VAR:col_id}][grps][]" multiple size="10">{VAR:grps}</select>
											<!-- END SUB: SEL_GRPS -->

											<!-- SUB: SEL_LINK -->
											Vali link:<Br>
											<select class="formselect" name="cols[{VAR:col_id}][link_el]" size="10">{VAR:link}</select>
											<!-- END SUB: SEL_LINK -->

											<!-- SUB: SEL_FORMEL -->
											Vali element:<Br>
											<select class="formselect" multiple name="cols[{VAR:col_id}][formel][]" size="10">{VAR:formels}</select>
											<!-- END SUB: SEL_FORMEL -->

											<!-- SUB: SEL_BASKET -->
											Vali ostukorv ({VAR:fel_name}):<Br>
											<select class="formselect" name="cols[{VAR:col_id}][basket][{VAR:fel_id}]">{VAR:baskets}</select><br>
											Vali kontroller:<Br>
											<select class="formselect" multiple name="cols[{VAR:col_id}][basket_controller][{VAR:fel_id}][]">{VAR:basket_controller}</select><br>
											<!-- SUB: EL_IS_SUBMIT --> 
											Korvi URL:<Br>
											<input class="formtext" name="cols[{VAR:col_id}][basket_url][{VAR:fel_id}]" value="{VAR:basket_url}"><br>
											Koguse element:<Br>
											<select class="formselect" name="cols[{VAR:col_id}][basket_add_count_el][{VAR:fel_id}]">{VAR:bcount_el}</select>
											<!-- END SUB: EL_IS_SUBMIT --> 

											<!-- END SUB: SEL_BASKET -->

											<!-- SUB: SEL_SETTINGS -->
											Tulba m‰‰rangud:<br>
											Tulp mitteaktiivne: <input type="checkbox" name="cols[{VAR:col_id}][not_active]" value="1" {VAR:col_not_active}><Br>
											Elementide eraldaja: <input type="text" name="cols[{VAR:col_id}][col_el_sep]" value="{VAR:col_el_sep}" size="3" class="formtext"><Br>
											<table border="0">
												<tr>
													<td class="celltext">Jrk.</td>
													<td class="celltext">Pre.</td>
													<td class="celltext">Post.</td>
													<td class="celltext">N&auml;ita</td>
													<!-- SUB: IS_EDITABLE -->
													<td class="celltext">Muudetav</td>
													<!-- END SUB: IS_EDITABLE -->
													<td class="celltext">Tulba nimeks</td>
													<td class="celltext">&Auml;ra n&auml;ita<br> rida kui t&uuml;hi</td>
												</tr>
												<!-- SUB: SEL_EL -->
												<tr>
													<td class="celltext" colspan="4">{VAR:el_name}</td>
												</tr>
												<tr>
													<td class="celltext"><input class="formtext" type="text" name="cols[{VAR:col_id}][el_ord][{VAR:el_id}]" value='{VAR:el_ord}' size="2"></td>
													<td class="celltext"><input class="formtext" type="text" name="cols[{VAR:col_id}][el_sep_pre][{VAR:el_id}]" value='{VAR:el_sep_pre}' size="2"></td>
													<td class="celltext"><input class="formtext" type="text" name="cols[{VAR:col_id}][el_sep][{VAR:el_id}]" value='{VAR:el_sep}' size="2"></td>
													<td class="celltext"><input class="formcheck" type="checkbox" name="cols[{VAR:col_id}][el_show][{VAR:el_id}]" value="1" {VAR:el_show}></td>
													<!-- SUB: IS_EDITABLE2 -->
													<td class="celltext"><input class="formcheck" type="checkbox" name="cols[{VAR:col_id}][el_edit][{VAR:el_id}]" value="1" {VAR:el_edit}></td>
													<!-- END SUB: IS_EDITABLE2 -->

													<td class="celltext"><input class="formcheck" type="checkbox" name="cols[{VAR:col_id}][el_set_colname][{VAR:el_id}]" value="1" {VAR:el_set_colname}></td>
												
													<td class="celltext"><input class="formcheck" type="checkbox" name="cols[{VAR:col_id}][el_no_row_if_empty][{VAR:el_id}]" value="1" {VAR:el_no_row_if_empty}></td>
												</tr>
												<!-- END SUB: SEL_EL -->
											</table>
											<!-- SUB: HAS_FTABLE_ALIASES -->
											Otsing elementi<Br>
											<select name="cols[{VAR:col_id}][search_el]" class="formselect"><option value=''>{VAR:search_el}</select> <Br>
											Otsingu v&auml;&auml;rtus elemendist:<Br>
											<select name="cols[{VAR:col_id}][search_map]" class="formselect"><option value=''>{VAR:search_map}</select><br>
											Kas jagada otsing s&otilde;nadeks: <input type='checkbox' name='cols[{VAR:col_id}][split_col_search]' value='1' {VAR:split_col_search}><br>
											S&otilde;nade eraldaja: <input type='text' name='cols[{VAR:col_id}][split_col_search_splitter]' value='{VAR:split_col_search_splitter}' class='formtext' size="2">
											<!-- END SUB: HAS_FTABLE_ALIASES -->

											<!-- END SUB: SEL_SETTINGS -->

											<!-- SUB: SEL_SETINGS2 -->
											<input type="checkbox" name="cols[{VAR:col_id}][sortable]" value="1" {VAR:col_sortable}> Sorditav <br>
											<input type="checkbox" name="cols[{VAR:col_id}][is_edit]" value="1" {VAR:is_edit}> Muudetav <br>
											<input type="checkbox" name="cols[{VAR:col_id}][is_email]" value="1" {VAR:col_email}> E-mail <br>
											<input type="checkbox" name="cols[{VAR:col_id}][clicksearch]" value="1" {VAR:col_clicksearch}> Klikkides tehakse otsing <br>
											<input type="checkbox" name="cols[{VAR:col_id}][link]" value="1" {VAR:col_link}> Link <br>
											<input type="checkbox" name="cols[{VAR:col_id}][no_show_empty]" value="1" {VAR:no_show_empty}> &Auml;ra n&auml;ita t&uuml;hjalt <br>
											Vali tulba kontrollerid:<br>
											<select name="cols[{VAR:col_id}][controllers][]" multiple class="formselect">{VAR:controllers}</select><Br>
											
											Vali v&auml;&auml;rtuse kontroller:<br>
											<select name="cols[{VAR:col_id}][val_controller]" class="formselect">{VAR:val_controller}</select><Br>

											Sektsioon, kuhu link viitab <input type="textbox" size="5"  name="cols[{VAR:col_id}][link_section]" value="{VAR:link_section}"><br>
											<input type="checkbox" name="cols[{VAR:col_id}][link_new_window]" value="1" {VAR:col_link_new_window}> Uues aknas <br><br>

											<input type="checkbox" name="cols[{VAR:col_id}][link_popup]" value="1" {VAR:col_link_popup}> Popup aken <br>
											Erineva stiiliga: <input class="formcheck" type="checkbox" name="cols[{VAR:col_id}][has_col_style]" value="1" {VAR:has_col_style}><Br>

											<!-- SUB: HAS_STYLE -->
											Tavaline pealkirja stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][header_normal]">{VAR:header_normal_styles}</select><br>
											Tavalise pealkirja lingi stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][header_link]">{VAR:header_link_styles}</select><br>
											Sorditav pealkirja stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][header_sortable]">{VAR:header_sortable_styles}</select><br>
											Sorditud pealkirja stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][header_sorted]">{VAR:header_sorted_styles}</select><br>
											Sortimislinkide stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][header_sortable_link]">{VAR:header_sortable_link_styles}</select><br>
											1 celli stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][content_style1]">{VAR:content_style1_styles}</select><br>
											2 celli stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][content_style2]">{VAR:content_style2_styles}</select><br>
											1 sorditud celli stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][content_sorted_style1]">{VAR:content_sorted_style1_styles}</select><br>
											2 sorditud celli stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][content_sorted_style2]">{VAR:content_sorted_style2_styles}</select><br>
											1 celli linkide stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][link_style1]">{VAR:link_style1_styles}</select><br>
											2 celli linkide stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][link_style2]">{VAR:link_style2_styles}</select><br>
											Grupi rea stiil:<br>
											<select class="formselect" name="cols[{VAR:col_id}][styles][group_style]">{VAR:group_style_styles}</select><br>
											<!-- END SUB: HAS_STYLE -->

											<!-- END SUB: SEL_SETINGS2 -->

											<!-- SUB: SEL_POPUP -->
											Akna mııtmed: <br>
											L <input type="text" size="3" class="formtext" name="cols[{VAR:col_id}][link_popup_width]" value="{VAR:popup_width}"> K <input type="text" size="3" class="formtext" name="cols[{VAR:col_id}][link_popup_height]" value="{VAR:popup_height}"> <br>
											<input type="checkbox" name="cols[{VAR:col_id}][link_popup_scrollbars]" value="1" {VAR:scrollbars}> kerimisribad <br>
											<input type="checkbox" name="cols[{VAR:col_id}][link_popup_fixed]" value="1" {VAR:fixed}> fikseeritud suurus <br>
											<input type="checkbox" name="cols[{VAR:col_id}][link_popup_toolbar]" value="1" {VAR:toolbar}> toolbar <br>
											<input type="checkbox" name="cols[{VAR:col_id}][link_popup_addressbar]" value="1" {VAR:addressbar}> aaddressbar <br>
											<!-- END SUB: SEL_POPUP -->

											<!-- SUB: SEL_IMAGE -->
											Pildi korral n‰ita: <Br>
											<input type="radio" name="cols[{VAR:col_id}][image_type]" value="img" {VAR:img_type_img}> Pilt <Br>
											<input type="radio" name="cols[{VAR:col_id}][image_type]" value="tximg" {VAR:img_type_tximg}> Tekst ja pilt <Br>
											<input type="radio" name="cols[{VAR:col_id}][image_type]" value="imgtx" {VAR:img_type_imgtx}> Pilt ja tekst<Br>

											<input type="textbox" class="formtext" size="3" name="cols[{VAR:col_id}][thousands_sep]" value="{VAR:thousands_sep}"> Tuhandete eraldaja<Br>
											<!-- END SUB: SEL_IMAGE -->

											<!-- SUB: SEL_DATEFORMAT -->
											Kuup&auml;eva formaat: <br>
											<input type="text" class="formtext" name="cols[{VAR:col_id}][dateformat]" value="{VAR:dateformat}"> <br>
											<!-- END SUB: SEL_DATEFORMAT -->
										</table>

										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="5"><IMG SRC="images/trans.gif" WIDTH="5" HEIGHT="1" BORDER=0 ALT=""></td>
												<td>
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td class="icontext" align="center"><input type='image' src="{VAR:baseurl}/automatweb/images/blue/big_save.gif" width="32" height="32" border="0" VALUE='submit' CLASS="small_button"><br><a href="javascript:document.add.submit()">Salvesta</a></td>
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
	{VAR:reforb}
</form>

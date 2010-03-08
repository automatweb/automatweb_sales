<form action='reforb.{VAR:ext}' method=post name="add" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
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
												<td class="celltext">Nimi:</td><td class="celltext"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
											</tr>
											<tr>
												<td class="celltext">Kommentaar:</td><td class="celltext"><textarea class="formtext" name="comment">{VAR:comment}</textarea></td>
											</tr>
											<tr>
												<td class="celltext">Keeled:</td><td class="celltext"><select class="formselect" NAME='settings[languages][]' multiple>{VAR:languages}</select></td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Vormid, kust v?etakse elemendid:</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><select size="20" class="formselect" NAME='settings[forms][]' multiple>{VAR:forms}</select></td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Kataloogid, kuhu saab sisestusi liigutada:</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><select class="formselect" NAME='settings[folders][]' size="20" multiple>{VAR:folders}</select></td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" name="settings[has_ftbl_extlinks]" value="1" class="formcheck" {VAR:has_ftbl_extlinks}> Formi tabeli aliaste lingid viitavad dokumendist v&auml;lja</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" name="settings[has_yah]" value="1" class="formcheck" {VAR:has_yah}> YAH riba</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" name="settings[has_aliasmgr]" value="1" class="formcheck" {VAR:has_aliasmgr}> Aliastehaldur</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" name="settings[select_default]" value="1" class="formcheck" {VAR:select_default}> _Vali_ tulba default</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" name="settings[has_textels]" value="1" class="formcheck" {VAR:has_textels}> N?ita tekst t??pi elemente</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input {VAR:has_groupacl} type="checkbox" name="settings[has_groupacl]" value="1" class="formcheck"> ?igused tulpadele piiratud gruppide kaupa</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" {VAR:no_titlebar} name="settings[no_titlebar]" value="1" class="formcheck">&Auml;ra n&auml;ita tulpade pealkirju</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" {VAR:has_grpnames} name="settings[has_grpnames]" value="1" class="formcheck">N?ita tulpade nimesid iga grupeerimiselemendi all</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><input type="checkbox" {VAR:has_print_button} name="settings[print_button]" value="1" class="formcheck">Prindi nupp &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Browse ikoon&nbsp;<input type="file" class="formfile" name="settings_print_button_file"> {VAR:print_button_file}</td>
											</tr>
<!-- SUB: CHANGE -->
											<tr>
												<td colspan="2" class="celltext">N?itamise m&auml;&auml;rangud</td>
											</tr>

											<tr>
												<td colspan="2" class="celltext">Vaatamine:</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><select size="10" class="formselect" NAME='settings[view_cols][]' multiple>{VAR:view_cols}</select></td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Muutmine:</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext"><select size="10" class="formselect" NAME='settings[change_cols][]' multiple>{VAR:change_cols}</select></td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">
													<table border="0">
														<tr>
															<td colspan="3" class="celltext">Vaikimisi sorteeritakse:</td>
														</tr>
														<tr>
															<td class="celltext">Jrk:</td>
															<td class="celltext">Element:</td>
															<td class="celltext">Asc/Desc:</td>
														</tr>
														<!-- SUB: DEFSORT_LINE -->
														<tr>
															<td class="celltext"><input type="text" name="defsort[{VAR:ds_nr}][ord]" value="{VAR:ds_ord}" class="formtext" size="4"></td>
															<td class="celltext"><select class="formselect" name="defsort[{VAR:ds_nr}][el]"><option value=''>{VAR:ds_els}</select></td>
															<td class="celltext"><input type="radio" class="formradio" name="defsort[{VAR:ds_nr}][type]" value="asc" {VAR:ds_asc}> Asc. <input type="radio" name="defsort[{VAR:ds_nr}][type]" value="desc" {VAR:ds_desc}> Desc.</td>
														</tr>
														<!-- END SUB: DEFSORT_LINE -->
													</table>
												</td>
											</tr>

											<tr>
												<td colspan="2" class="celltext">
													<table border="0">
														<tr>
															<td colspan="4" class="celltext">J?ta v?lja mitmekordsed:</td>
														</tr>
														<tr>
															<td class="celltext">Element:</td>
															<td class="celltext">Koondatud tulp:</td>
															<td class="celltext">Eraldaja:</td>
															<td class="celltext">J?rjekorraelement:</td>
														</tr>
														<!-- SUB: GRPLINE -->
														<tr>
															<td class="celltext"><SELECT CLASS="formselect" name="grps[{VAR:grp_nr}][gp_el]"><option value=''>{VAR:gp_els}</select></td>
															<td class="celltext"><SELECT CLASS="formselect" name="grps[{VAR:grp_nr}][collect_el]"><option value=''>{VAR:collect_els}</select></td>
															<td class="celltext"><input type="text" class="formtext" name="grps[{VAR:grp_nr}][sep]" value="{VAR:gp_sep}" size="4"></td>
															<td class="celltext"><SELECT CLASS="formselect" name="grps[{VAR:grp_nr}][ord_el]"><option value=''>{VAR:ord_els}</select></td>
														</tr>
														<!-- END SUB: GRPLINE -->
													</table>
												</td>
											</tr>

											<tr>
												<td colspan="2" class="celltext">
													<table border="0">
														<tr>
															<td colspan="3" class="celltext">Grupeerimise elemendid:</td>
														</tr>
														<tr>
															<td class="celltext">Jrk:</td>
															<td class="celltext">Enne:</td>
															<td class="celltext">Element:</td>
															<td class="celltext">Vertikaalne:</td>
															<td class="celltext">Eraldaja enne</td>
															<td class="celltext">Eraldaja p&auml;rast</td>
															<td class="celltext">Eraldaja elementide vahel</td>
															<td class="celltext">Muu info elemendid:</td>
															<td class="celltext">Muu info eraldajad:</td>
															<td class="celltext">Tulba pealkiri(vertikaalse grupi)</td>

														</tr>
														<!-- SUB: GRP2LINE -->
														<tr>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][ord]" value="{VAR:gp_ord}" size="4"></td>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][real_sep_before]" value="{VAR:gp_real_sep_before}" size="4"></td>
															<td class="celltext">N&auml;itamise:<br>
															<SELECT CLASS="formselect" name="rgrps[{VAR:grp_nr}][el]"><option value=''>{VAR:els}</select><br>
															Sortimise:<br>
															<SELECT CLASS="formselect" name="rgrps[{VAR:grp_nr}][sort_el]"><option value=''>{VAR:sort_els}</select><br>
															Sortimise j&auml;rjekord:<br>
															<SELECT CLASS="formselect" name="rgrps[{VAR:grp_nr}][sort_order]">{VAR:sort_order}</select><br>
															Otsingu v&auml;&auml;rtus:<br>
															<SELECT CLASS="formselect" name="rgrps[{VAR:grp_nr}][search_val_el]"><option value=''>{VAR:search_val_els}</select><br>
															Otsing elementi:<br>
															<SELECT CLASS="formselect" name="rgrps[{VAR:grp_nr}][search_to_el]"><option value=''>{VAR:search_to_els}</select><br>
															</td>
															<td class="celltext"><input type="checkbox" class="formcheck" name="rgrps[{VAR:grp_nr}][vertical]" value="1" {VAR:gp_vertical}></td>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][pre_sep]" value="{VAR:pre_sep}" size="4"></td>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][after_sep]" value="{VAR:after_sep}" size="4"></td>
															<td class="celltext"><select class="formselect" name="rgrps[{VAR:grp_nr}][data_els][]" multiple size="10">{VAR:data_els}</select></td>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][mid_sep]" value="{VAR:mid_sep}" size="4"></td>

															<td class="celltext">
																<table border="0">
																	<tr>
																		<td class="celltext">Element</td>
																		<td class="celltext">Enne</td>
																		<td class="celltext">P&auml;rast</td>
																	</tr>
																	<!-- SUB: DATEL -->
																	<tr>
																		<td class="celltext">{VAR:del_name}</td>
																		<td class="celltext"><input type="text" name="rgrps[{VAR:grp_nr}][data_els_seps][{VAR:del}]" class="formtext" size="4" value="{VAR:del_sep}"></td>
																		<td class="celltext"><input type="text" name="rgrps[{VAR:grp_nr}][data_els_seps_after][{VAR:del}]" class="formtext" size="4" value="{VAR:del_sep_after}"></td>
																	</tr>
																	<!-- END SUB: DATEL -->
																</table>
															</td>
															<td class="celltext"><input type="text" class="formtext" name="rgrps[{VAR:grp_nr}][row_title]" value="{VAR:gp_row_title}" size="14"></td>
														</tr>
														<!-- END SUB: GRP2LINE -->
													</table>
												</td>
											</tr>

											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" name="settings[has_pages]" value="1" {VAR:has_pages}> Kirjeid n?idatakse lehek?lgede kaupa</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="text" class="formtext" name="settings[records_per_page]" value="{VAR:records_per_page}" size="5"> kirjet lehel</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="radio" name="settings[has_pages_type]" value="text" {VAR:has_pages_text}> Tekstip?hine lehek?ljevalik</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="radio" name="settings[has_pages_type]" value="lb" {VAR:has_pages_lb}> Dropdown lehek?ljevalik</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[skip_one_liners]" value="1" {VAR:skip_one_liners}> Kui tabelis on ainult yks rida mis on lingitud, siis suuna sinna edasi</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><select class="formselect" name="settings[skip_one_liners_col]">{VAR:skip_one_liners_col}</select> Millises tulbas on edasi suunamise link</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[doc_title_is_search]" value="1" {VAR:doc_title_is_search}> Dokumendi pealkirjas viimane otsing</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[doc_title_is_search_upper]" value="1" {VAR:doc_title_is_search_upper}> Dokumendi pealkirja viimane otsing suurte t&auml;htedega</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[doc_title_is_yah]" value="1" {VAR:doc_title_is_yah}> Dokumendi pealkirjas YAH riba</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[doc_title_is_yah_nolast]" value="1" {VAR:doc_title_is_yah_nolast}> &Auml;ra n&auml;ita viimast dokumendi pealkirja YAH'i isa</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[doc_title_is_yah_upper]" value="1" {VAR:doc_title_is_yah_upper}> Dokumendi pealkirja YAH Suurte t&auml;htedega</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="text" class="formtext" name="settings[doc_title_is_yah_sep]" value="{VAR:doc_title_is_yah_sep}" size="3"> Dokumendi pealkirja yah eraldaja</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">
													<table border="0">
														<tr>
															<td class="celltext">Tabeli p&auml;isesse n&auml;ita: </td>
															<td class="celltext"><select class="formselect" name="settings[table_header_aliases][]" multiple size="5">{VAR:table_header_aliases}</select> </td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[no_show_empty]" value="1" {VAR:no_show_empty}> &Auml;ra n&auml;ita t&uuml;hja tabelit</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="textbox" class="formtext" name="settings[empty_table_text]" value="{VAR:empty_table_text}"> T&uuml;hja tabeli asemel tekst</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><select class="formselect" name="settings[empty_table_alias][]" multiple><option value=''>{VAR:empty_table_alias}</select> T&uuml;hja tabeli asemel alias</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[no_grpels_in_restrict]" value="1" {VAR:no_grpels_in_restrict}> &Auml;ra pane grupeerimislemente uude otsingusse</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2"><input type="checkbox" class="formcheck" name="settings[show_second_table]" value="1" {VAR:show_second_table}> Tabeli <input type='radio' name='settings[show_second_table_where]' value='below' {VAR:second_table_below}>all <input type='radio' name='settings[show_second_table_where]' value='above' {VAR:second_table_above}>&uuml;leval on teine tabel</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Tabelite vahel: <select class="formselect" name="settings[show_second_table_tables_sep][]" multiple size="5">{VAR:show_second_table_tables_sep}</select> </td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">
													<table border="0">
														<tr>
															<td class="celltext">Teise tabeli n&auml;itamisel kasutatavad aliased: </td>
															<td class="celltext"><select class="formselect" multiple name="settings[show_second_table_aliases][]" size="5">{VAR:show_second_table_aliases}</select> </td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Teise tabeli otsingu element: <select class="formselect" name="settings[show_second_table_search_el]"><option value="">{VAR:second_table_search_el}</select> </td>
											</tr>
											<tr>
												<td colspan="2" class="celltext">Teise tabeli otsingu elemendi v&auml;&auml;rtus: <select class="formselect" name="settings[show_second_table_search_val_el]"><option value="">{VAR:second_table_search_val_el}</select> </td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">
													<table border="0">
														<tr>
															<td class="celltext"><input type="checkbox" name="settings[user_entries]" value="1" {VAR:has_user_entries}> N?ita ainult Useri enda sisestusi </td>
															<td class="celltext">N?ita k?iki sisestusi gruppidele:<br>
																<select name="user_entries_except_grps[]" multiple  size="10" class="formselect">{VAR:uee_grps}</select>
															</td>
															<td class="celltext">Gruppide kaupa m&auml;&auml;rangud: 
																<input type="checkbox" name="settings[has_grpsettings]" value="1" {VAR:has_grpsettings}>
															</td>
														</tr>
													</table>
												</td>
											</tr>

											<tr>
												<td class="celltext" colspan="2">
													<table border="0">
														<tr>
															<td class="celltext">Nupud:</td>
															<td class="celltext">Nupu tekst:</td>
															<td class="celltext">Jrk.:</td>
															<td class="celltext">&Uuml;leval/all:</td>
															<td class="celltext">Pilt:</td>
															<td class="celltext">Kustuta pilt</td>
														</tr>
														<!-- SUB: BUTTON -->
														<tr>
															<td class="celltext"><input type="checkbox" name="buttons[{VAR:bt_id}][check]" value="1" {VAR:button_check}> {VAR:bt_name}</td>
															<td class="celltext"><input class="formtext" type="text" name="buttons[{VAR:bt_id}][text]" value="{VAR:button_text}" ></td>
															<td class="celltext"><input class="formtext"  size="4" type="text" name="buttons[{VAR:bt_id}][ord]" value="{VAR:button_ord}"></td>
															<td class="celltext"><input type="checkbox" name="buttons[{VAR:bt_id}][pos][up]" value="1" {VAR:button_up}> &Uuml;leval <input type="checkbox" name="buttons[{VAR:bt_id}][pos][down]" value="1" {VAR:button_down}> All</td>
															<td class="celltext">{VAR:image}<input class="formfile" type="file" name="buttons_{VAR:bt_id}_image"></td>
															<td class="celltext"><input type="checkbox" name="buttons[{VAR:bt_id}][delimage]" value="1" ></td>
														<!-- END SUB: BUTTON -->
													</table>
												</td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">Aliased: #lk# - lehekylgede valimine<br>Header:<br><textarea class="formtext" name="settings[header]" cols="50" rows="5">{VAR:header}</textarea></td>
											</tr>
											<tr>
												<td class="celltext" colspan="2">Footer:<br><textarea class="formtext" name="settings[footer]" cols="50" rows="5">{VAR:footer}</textarea></td>
											</tr>
<!-- END SUB: CHANGE -->

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



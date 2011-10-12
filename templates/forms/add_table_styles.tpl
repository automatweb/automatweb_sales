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
												<td class="celltext">Tabeli stiil:</td><td class="celltext"><select name="styles[table_style]" class="formselect">{VAR:tablestyles}</select></td>
											</tr>
											<tr>
												<td class="celltext">Tavaline pealkirja stiil:</td><td class="celltext"><select name="styles[header_normal]" class="formselect">{VAR:header_normal}</select></td>
											</tr>
											<tr>
												<td class="celltext">Tavalise pealkirja lingi stiil:</td><td class="celltext"><select name="styles[header_link]" class="formselect">{VAR:header_link}</select></td>
											</tr>
											<tr>
												<td class="celltext">Sorditav pealkirja stiil:</td><td class="celltext"><select name="styles[header_sortable]" class="formselect">{VAR:header_sortable}</select></td>
											</tr>
											<tr>
												<td class="celltext">Sorditud pealkirja stiil:</td><td class="celltext"><select name="styles[header_sorted]" class="formselect">{VAR:header_sorted}</select></td>
											</tr>
											<tr>
												<td class="celltext">Sortimislinkide stiil:</td><td class="celltext"><select name="styles[header_sortable_link]" class="formselect">{VAR:header_sortable_link}</select></td>
											</tr>
											<tr>
												<td class="celltext">1 celli stiil:</td><td class="celltext"><select name="styles[content_style1]" class="formselect">{VAR:content_style1}</select></td>
											</tr>
											<tr>
												<td class="celltext">2 celli stiil:</td><td class="celltext"><select name="styles[content_style2]" class="formselect">{VAR:content_style2}</select></td>
											</tr>
											<tr>
												<td class="celltext">1 sorditud celli stiil:</td><td class="celltext"><select name="styles[content_sorted_style1]" class="formselect">{VAR:content_sorted_style1}</select></td>
											</tr>
											<tr>
												<td class="celltext">2 sorditud celli stiil:</td><td class="celltext"><select name="styles[content_sorted_style2]" class="formselect">{VAR:content_sorted_style2}</select></td>
											</tr>
											<tr>
												<td class="celltext">1 celli linkide stiil:</td><td class="celltext"><select name="styles[link_style1]" class="formselect">{VAR:link_style1}</select></td>
											</tr>
											<tr>
												<td class="celltext">2 celli linkide stiil:</td><td class="celltext"><select name="styles[link_style2]" class="formselect">{VAR:link_style2}</select></td>
											</tr>
											<tr>
												<td class="celltext">Grupi rea stiil:</td><td class="celltext"><select name="styles[group_style]" class="formselect">{VAR:group_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Grupi rea lingi stiil:</td><td class="celltext"><select name="styles[group_link_style]" class="formselect">{VAR:group_link_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Kirjete summeerimise stiil:</td><td class="celltext"><select name="styles[sum_style]" class="formselect">{VAR:sum_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Tekst lehek&uuml;ljevaliku stiil:</td><td class="celltext"><select name="styles[pg_text_style]" class="formselect">{VAR:pg_text_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Tekst lehek&uuml;ljevaliku lingi stiil:</td><td class="celltext"><select name="styles[pg_text_style_link]" class="formselect">{VAR:pg_text_style_link}</select></td>
											</tr>
											<tr>
												<td class="celltext">Listboks lehek&uuml;ljevaliku stiil:</td><td class="celltext"><select name="styles[pg_lb_style]" class="formselect">{VAR:pg_lb_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Header ja footer teksti stiil:</td><td class="celltext"><select name="styles[text_style]" class="formselect">{VAR:text_style}</select></td>
											</tr>
											<tr>
												<td class="celltext">Header ja footer teksti linkide stiil:</td><td class="celltext"><select name="styles[text_style_link]" class="formselect">{VAR:text_style_link}</select></td>
											</tr>
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

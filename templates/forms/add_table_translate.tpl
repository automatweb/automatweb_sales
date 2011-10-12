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
												<td class="celltext" colspan="20">{VAR:menu}</td>
											</tr>
											<tr>
											<!-- SUB: LANG_H -->
												<td class="celltext">{VAR:lang_name}</td>
											<!-- END SUB: LANG_H -->
											</tr>
											<!-- SUB: COL -->
											<tr>
												<!-- SUB: LANG -->
												<td class="celltext"><input type="text" name="langs[{VAR:col_id}][{VAR:lang_id}]" value="{VAR:title}" class="formtext"></td>
												<!-- END SUB: LANG -->
											</tr>
											<!-- END SUB: COL -->

											<tr>
												<td colspan="20" class="celltext">T&otilde;lgi meta-elementide tekste:</td>
											</tr>

											<tr>
												<td class="celltext">Element</td>
												<!-- SUB: CLANG_H -->
												<td class="celltext">Tulba tekst ({VAR:lang_name})</td>
												<!-- END SUB: CLANG_H -->
											</tr>
											<!-- SUB: COL_TEXT -->
											<tr>
												<td class="celltext">{VAR:eltype}</td>
												<!-- SUB: CLANG -->
												<td class="celltext"><input type='text' class='small_button' name='texts[{VAR:eltype}][{VAR:lang_id}]' VALUE='{VAR:t_name}'></td>
												<!-- END SUB: CLANG -->
											</tr>
											<!-- END SUB: COL_TEXT -->

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
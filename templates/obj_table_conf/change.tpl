<form action='reforb.{VAR:ext}' method=post name="add">
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
														<td class="icontext" align="center"><input type='image' src="{VAR:baseurl}/automatweb/images/blue/big_save.gif" width="32" height="32" border="0" VALUE='submit' CLASS="small_button"><br>
														<a href="javascript:document.add.submit()">Salvesta</a></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<br>




									<table class="aste01" cellpadding=3 cellspacing=1 border=0>
										<tr>
											<td class="celltext">Nimi:</td>
											<td class="celltext" colspan="2"><input type='text' NAME='name' VALUE='{VAR:name}' class="formtext"></td>
										</tr>
										<tr>
											<td class="celltext">Eraldaja &uuml;he tulba elementide vahel:</td>
											<td class="celltext" colspan="2"><input type='text' NAME='sep' VALUE='{VAR:sep}' class="formtext"></td>
										</tr>
										<tr>
											<td class="celltext" colspan="2">
												<table border=0 cellpadding=0 cellspacing=3>
													<tr>
														<td class="celltext">Pealkiri</td>
														<td class="celltext">Jrk</td>
														<td class="celltext">Sorditav</td>
													</tr>
													<!-- SUB: COLUMN -->
													<tr>
														<td class="celltext"><input type="text" class="formtext" name="cols[{VAR:col_id}][title]" value="{VAR:title}"></td>
														<td class="celltext"><input type="text" class="formtext" name="cols[{VAR:col_id}][ord]" value="{VAR:ord}" size="3"></td>
														<td class="celltext"><input type="checkbox" class="formcheck" name="cols[{VAR:col_id}][sortable]" value="1" {VAR:sortable}></td>
														<td class="celltext">
															<!-- SUB: EL -->
															<select name="cols[{VAR:col_id}][col][{VAR:idx}]" class="formselect">{VAR:cols}</select><BR>
															<!-- END SUB: EL -->
														</td>
													</tr>
													<tr>
														<td class="celltext" colspan="3">&nbsp;</td>
													</tr>
													<!-- END SUB: COLUMN -->
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



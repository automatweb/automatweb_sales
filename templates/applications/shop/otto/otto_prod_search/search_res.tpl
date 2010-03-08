<!-- SUB: PAGE_SEP -->
|
<!-- END SUB: PAGE_SEP -->

<table border="0" width="100%">
	<tr>
		<td class="text">Leiti {VAR:total} toodet.  </td>
		<td class="text" align="right">Lehek&uuml;lg: 
			<!-- SUB: PREV -->
			<!--<a href='{VAR:link}'>prev</a>-->
			<!-- END SUB: PREV -->

			<!-- SUB: PAGE -->
				<A href='{VAR:link}'>{VAR:p_nr}</a> 
			<!-- END SUB: PAGE -->

			<!-- SUB: SEL_PAGE -->
				{VAR:p_nr}
			<!-- END SUB: SEL_PAGE -->

			<!-- SUB: NEXT -->
			<!-- <a href='{VAR:link}'>next</a>-->
			<!-- END SUB: NEXT -->
		</td>	
	</tr>
</table>

<table border="0" width="100%">
	<!-- SUB: LINE -->
		<tr>
			<!-- SUB: PROD -->
			<td width="50%"	class="text" valign="top">
				<table border="0" width="100%" height="250">
					<tr>
						<td valign="top" width="1">{VAR:pimg}</td>
						<td valign="top" class="text">
							<table border="0" height="250">
								<tr height="100%">
									<td class="text"  height="100%" valign="top">
										<a href='{VAR:prod_link}'>{VAR:prod_name}</a><br>
										{VAR:prod_desc}<br><br>
										Hind alates {VAR:prod_price} EEK<br>	
									</td>
								</tr>
								<tr height="1">
									<td class="text" height="1">
										{VAR:path}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<!-- END SUB: PROD -->
		</tr>
	<!-- END SUB: LINE -->
</table>

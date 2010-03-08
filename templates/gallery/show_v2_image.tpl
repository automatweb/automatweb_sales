<html>
<head>
	<title>{VAR:name}</title>
	<link rel="stylesheet" href="{VAR:baseurl}/css/styles.css">
</head>

<body bgcolor="#FFFFFF" topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
			<td bgcolor="#FFFFFF"><span class="text">Hinne: {VAR:avg_rating}</span></td>
			<td bgcolor="#FFFFFF" align="right">
				<table border="0" cellpadding="1" cellspacing="0">
					<tr>
						<td bgcolor="#FFFFFF" class="text"><a
						href='{VAR:print_link}'><img SRC="{VAR:baseurl}/img/print.gif"
						BORDER=0 ALT="Print" title="Print"></a><img
						SRC="{VAR:baseurl}/img/trans.gif"  width="1"
						height="1" BORDER=0 ALT="Print" title="Print"><a
						href='{VAR:email_link}'><img
						SRC="{VAR:baseurl}/img/icon/post.gif" BORDER=0 ALT="Saada sõbrale"
						title="Saada sõbrale"></a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td><a href="javascript:close();">{VAR:image}</a></td>
		</tr>
	</table>

	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td><span class="text">&nbsp;Vaatamisi: <b>{VAR:views}</b></span></td>
			<!-- SUB: HAS_RATING_SCALE -->
			<td  align="right">
				<table border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td class="text">Hinda:&nbsp;</td>
						<!-- SUB: RATING_SCALE_ITEM -->
							<td bgcolor="#FFFFFF" width="25" class="textpealkiri"><a href='{VAR:rate_link}'>{VAR:scale_value}</a></td>
						<!-- END SUB: RATING_SCALE_ITEM -->
					</tr>
				</table>
			</td>
			<!-- END SUB: HAS_RATING_SCALE -->
		</tr>
	</table>
</body>

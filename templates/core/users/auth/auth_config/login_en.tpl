<center>

<style>
.logintable {
	font-family:  Arial, sans-serif;
	font-size: 11px;
	color: #000000;
	text-decoration: none;
	background-color: #ffffff;
	border: 0px;
}
.logintable a {
	color: #0467a0;
	text-decoration:none;
	font-size: 11px;
}
.logintable a:hover {
	color: #0075B8;
	text-decoration:underline;
	font-size: 11px;
}
.logintable .loginbt {
	font-family:  Verdana, Arial, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: #FFFFFF;
	text-decoration: none;
	background: #0075B8;
	border-color: #79B3D5;
}
.logintable .note {
	color: #c3162f;
	font-weight: bold;
	text-align: center;
	padding-bottom: 13px;
	font-size: 13px;
}
.logintable .caption {
	font-weight: bold;
	text-align: right;
}
.logintable .element {
	font-weight: bold;
	text-align: left;
}
.logintable .lingid {
	font-size: 11px;
	font-weight: bold;
	text-align: right;
}
.logintable .logo {
	text-align: left;
	padding-bottom: 23px;
}
.logintable .footer {
	text-align: center;
	border-top: 2px solid #0075B8;
	padding-top: 7px;
}
.logintable .textbox {
	background-color: #FFFFFF;
	font-family:  Verdana, Arial, sans-serif;
	border: 1px solid #0075B8;
	padding: 2px 5px 2px 5px;
	margin: 0 0 0 0;
	width: 250px;
}
.logintable .select {
	background-color: #FFFFFF;
	font-family:  Verdana, Arial, sans-serif;
	font-size: 11px;
	border: 1px solid #0075B8;
	margin: 0 0 0 0;
}
</style>

<table cellspacing="0" cellpadding="13" style="border: 2px solid #0075B8; margin-top: 50px;">
<tr><td>

<table border="0" cellspacing="1" cellpadding="2" class="logintable">
<form name=login method="POST" action="{VAR:baseurl}/index.{VAR:ext}">
	<tr>
		<td colspan="2" class="logo">
			<img src='http://www.struktuur.ee/img/aw_logo.gif' border='0'>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="note">
			<b>You must be logged in to use this resource!</b>
		</td>
	</tr>

	<!-- SUB: SERVER_PICKER -->
	<tr>
                            <td  class="caption">Server:</td>
                            <td class="element"><select name="server" class="select">{VAR:servers}</select></td>
	</tr>
	<!-- END SUB: SERVER_PICKER -->

	<tr>
		<td class="caption">
			Username:
		</td>
		<td class="element">
			<input type="text" name="uid" size="40" class="textbox">
		</td>
	</tr>

	<tr>
		<td class="caption">
			Password:
		</td>
		<td class="element">
			<input type="password" name="password" size="40" class="textbox">
		</td>
	</tr>

	<tr>
		<td class="caption">
		</td>
		<td align="left">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td  width="50%">
						{VAR:reforb}
						<input type="submit" value="Login" class="loginbt">
						<script language="Javascript">
							document.login.uid.focus();
						</script>
					</td>
					<td class="lingid" width="50%" align="right">
						<a href="{VAR:baseurl}?class=users&action=send_hash">Forgot password?</a><br>
						<a target="new" href="http://support.automatweb.com">Help</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="43">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td class="footer" colspan="2">
			OÜ Struktuur Varahaldus<br>Address: Pärnu mnt. 158/1, 11317, Tallinn<br>Phone: +372 5558 0898<br>E-mail: <a href="mailto:info@automatweb.com">info@automatweb.com</a>
		</td>
	</tr>

</form>
</table>

</td></tr></table>
</center>

<dl class="news"><dt>Registreerusite n&otilde;ustajaga kokkusaamisele!</dt></dl>

<form action="{VAR:baseurl}/reforb{VAR:ext}" method="POST">
<table border="0"> 
	<tr>
		<td><b>N&otilde;ustaja andmed:</b></td>
	</tr>
	<tr>
		<td>{VAR:person} {VAR:person_rank} {VAR:person_mail} {VAR:person_phone} {VAR:person_address}</td>
	</tr>
	<tr>
		<td><br><br><b>Kellaaeg:</b></td>
	</tr>
	<tr>
		<td><b>{VAR:date} {VAR:time_from} - {VAR:time_to}</b></td>
	</tr>
	<tr>
		<td><br><br><b>Teie andmed:</b></td>
	</tr>
	<tr>
		<td>{VAR:content}</td>
	</tr>
</table>
{VAR:reforb}
</form>
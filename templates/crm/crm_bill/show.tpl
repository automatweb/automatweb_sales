<table border="1">
	<tr>
		<td valign="top" align="left">
			Tellija andmed:<br>
			{VAR:orderer_name}<br>
			{VAR:orderer_addr}<br>
			KMK nr {VAR:orderer_kmk_nr}
		</td>
		<td>
			{VAR:impl_logo}<br>
			<font size="+1">Arve nr {VAR:bill_no}</font><br>
			<br>
			arve kuup&auml;ev:<br>
			{DATE:bill_date|d.m.y}<br>
			<br>
			makset&auml;htaeg:<br>
			{VAR:payment_due_days} p&auml;eva<br>
			{DATE:bill_due|d.m.y}<br>
			<br>
			tellija kontaktisik:<br>
			{VAR:orderer_contact}<br>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Selgitus:<br>
			{VAR:comment}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table border="0" width="100%">
				<tr>
					<td>&uuml;hik</td>
					<td>kogus</td>
					<td>hind</td>
					<td>summa</td>
					<td>selgitus</td>
				</tr>
				<!-- SUB: ROW -->
				<tr>
					<td>{VAR:unit}</td>
					<td>{VAR:amt}</td>
					<td>{VAR:price}</td>
					<td>{VAR:sum}</td>
					<td>{VAR:desc}</td>
				</tr>
				<!-- END SUB: ROW -->
				<tr>
					<td colspan="3" align="right">kokku:</td><td colspan="2">{VAR:total_wo_tax}</td>
				</tr>
				<tr>
					<td colspan="3" align="right">k&auml;ibemaks 18%:</td><td colspan="2">{VAR:tax}</td>
				</tr>
				<tr>
					<td colspan="3" align="right">summa:</td><td colspan="2">{VAR:total}</td>
				</tr>
				<tr>
					<td colspan="3" align="right">summa s&otilde;nadega:</td><td colspan="2">{VAR:total_text}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			T&auml;itja andmed:<br>
			{VAR:impl_name}<br>
			{VAR:impl_address}<br>
			Reg nr: {VAR:impl_reg_nr}<br>
			KMK nr: {VAR:impl_kmk_nr}<br>
			Telefon: {VAR:impl_phone}<br>
			Faks: {VAR:impl_fax}<br>
			E-post: {VAR:impl_email}<br>
			Kodulehek&uuml;lg: {VAR:impl_url}<br>
			<!-- SUB: BANK_ACCOUNT -->
			A/A {VAR:bank_name} {VAR:acct_no}<br>
			IBAN: {VAR:bank_iban}<br>
			<!-- END SUB: BANK_ACCOUNT -->
		</td>
	</tr>
</table>


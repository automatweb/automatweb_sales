<!-- SUB: PURVEYOR -->
<div>
	<a href="{VAR:logo.url}" title="{VAR:logo.comment}"><img src="{VAR:logo.url}" alt="{VAR:logo.comment}" /></a>
	<h3><a href="#">{VAR:title}</a></h3>
	<div class="address">{VAR:address} [ <a href="#">Show on map</a> ]</div>
	<div class="address">{LC:Phone:} {VAR:phones_comma_separated}</div>
	<div class="address">{LC:E-mail:} {VAR:emails_comma_separated}</div>
	<!-- SUB: PHONES -->
	<ul>
		<!-- SUB: PHONE -->
		<li>{VAR:phone}</li>
		<!-- END SUB: PHONE -->
	</ul>
	<!-- END SUB: PHONES -->
	<!-- SUB: EMAILS -->
	<ul>
		<!-- SUB: EMAIL -->
		<li>{VAR:email}</li>
		<!-- END SUB: EMAIL -->
	</ul>
	<!-- END SUB: EMAILS -->
	<p>{VAR:comment}</p>
</div>
<!-- END SUB: PURVEYOR -->

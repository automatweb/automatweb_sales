<!-- SUB: hits -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
<tr>
<td>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
	<tr>
		<td colspan="3" class="fgtitle">{VAR:lefttitle}</td>
	</tr>

	<tr>
		<td class="fgtitle">Periood</td>
		<td class="fgtitle">Vaatamisi</td>
		<td class="fgtitle">&nbsp;</td>
	</tr>
	<!-- SUB: hits_line -->
	<tr>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:period}
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:hits}
	</td>
	<td class="{VAR:style}" width="80%">
		<img src="{VAR:baseurl}/images/bar.gif" width="{VAR:width}" height="5">
	</td>
	</tr>
	<!-- END SUB: hits_line -->
	<tr>
		<td class="fgtext">
		<strong>Kokku:</strong>
		</td>
		<td class="fgtext" colspan="2">
		<strong>{VAR:total}</strong>
		</td>
	</tr>
	<tr>
		<td class="fgtext" colspan="3" align="center">
		<img src="{VAR:self}?class=stat&action=graph&id={VAR:uniqid}">
		</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!-- END SUB: hits -->
<!-- SUB: logins -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
<tr>
<td>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
	<tr>
		<td colspan="3" class="fgtitle">{VAR:title}</td>
	</tr>

	<tr>
		<td class="fgtitle">Kasutaja</td>
		<td class="fgtitle">Logimisi</td>
		<td class="fgtitle">&nbsp;</td>
	</tr>
	<!-- SUB: login_line -->
	<tr>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:uid}
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:logins}
	</td>
	<td class="{VAR:style}" width="80%">
		<img src="{VAR:baseurl}/images/bar.gif" width="{VAR:width}" height="5">
	</td>
	</tr>
	<!-- END SUB: login_line -->
	<tr>
		<td class="fgtext">
		<strong>Kokku:</strong>
		</td>
		<td class="fgtext" colspan="2">
		<strong>{VAR:total}</strong>
		</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!-- END SUB: logins -->
<!-- SUB: hosts -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
<tr>
<td>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
	<tr>
		<td colspan="4" class="fgtitle">Top 30 Vaatajat aadresside järgi</td>
	</tr>

	<tr>
		<td class="fgtitle">#</td>
		<td class="fgtitle">IP</td>
		<td class="fgtitle">Hitte</td>
		<td class="fgtitle">&nbsp;</td>
	</tr>
	<!-- SUB: hosts_line -->
	<tr>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:cnt}
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		<a href="javascript:ipexplorer('{VAR:ip}')">{VAR:ip}</a>
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:hits}
	</td>
	<td class="{VAR:style}" width="80%">
		<img src="{VAR:baseurl}/images/bar.gif" width="{VAR:width}" height="5">
	</td>
	</tr>
	<!-- END SUB: hosts_line -->
	<tr>
	<td class="fgtext" colspan="2">
	<strong>Kokku:</strong>
	</td>
	<td class="fgtext" colspan="2">
	<strong>{VAR:total}</strong>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!-- END SUB: hosts -->
<!-- SUB: menus -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
<tr>
<td>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
	<tr>
		<td colspan="4" class="fgtitle">Top muudetud menüüd</td>
	</tr>

	<tr>
		<td class="fgtitle">#</td>
		<td class="fgtitle">Menüü</td>
		<td class="fgtitle">Muutmisi</td>
		<td class="fgtitle">&nbsp;</td>
	</tr>
	<!-- SUB: menus_line -->
	<tr>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:cnt}
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:menu}
	</td>
	<td class="{VAR:style}" width="10%" nowrap>
		{VAR:changes}
	</td>
	<td class="{VAR:style}" width="80%">
		<img src="{VAR:baseurl}/images/bar.gif" width="{VAR:width}" height="5">
	</td>
	</tr>
	<!-- END SUB: menus_line -->
	<tr>
	<td class="fgtext" colspan="2">
	<strong>Kokku:</strong>
	</td>
	<td class="fgtext" colspan="2">
	<strong>{VAR:total}</strong>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!-- END SUB: menus -->
<!-- SUB: objects -->
<table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#EEEEEE">
<tr>
<td>
	<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#FFFFFF">
	<tr>
		<td colspan="5" class="fgtitle">
		Top
		<select name="count">
		<option value="50">50</option>
		</select>
		<select name="type">
		<option value="pageview">Vaatamist</option>
		</select> selles perioodis
		</td>
	</tr>

	<tr>
		<td class="fgtitle">#</td>
		<td class="fgtitle">Oid</td>
		<td class="fgtitle">Nimi</td>
		<td class="fgtitle">Hitte</td>
		<td class="fgtitle">&nbsp;</td>
	</tr>
	<!-- SUB: objects_line -->
	<tr>
		<td class="{VAR:style}">{VAR:cnt}</td>
		<td class="{VAR:style}" nowrap align="right">{VAR:oid}</td>
		<td class="{VAR:style}" nowrap><a target="new" href="{VAR:baseurl}/?section={VAR:oid}">{VAR:name}</a>&nbsp;</td>
		<td class="{VAR:style}">{VAR:hits}</td>
		<td class="{VAR:style}">
			<img src="{VAR:baseurl}/images/bar.gif" width="{VAR:width}" height="5">
		</td>
	</tr>
	<!-- END SUB: objects_line -->
	<tr>
	<td class="fgtext" colspan="3">
	<strong>Kokku:</strong>
	</td>
	<td class="fgtext" colspan="2">
	<strong>{VAR:total}</strong>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!-- END SUB: objects -->
<!-- SUB: selectors -->
<table border=1 cellpadding=0 cellspacing=1 width=100%>

<tr>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='auth' {VAR:auth_sel}>Login
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='mlist' {VAR:mlist_sel}>Listid
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='document' {VAR:document_sel}>Dokumendid
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='form' {VAR:form_sel}>Formid
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='user' {VAR:user_sel}>Kasutajad
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='error' {VAR:alias_sel}>Vead
</small></td>
</tr>

<tr>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='auth' {VAR:auth_sel}>Logout
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='ml_var' {VAR:ml_var_sel}>Muutujad
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='image' {VAR:image_sel}>Pildid
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='style' {VAR:style_sel}>Stiilid
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='group' {VAR:group_sel}>Grupid
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='bug' {VAR:bug_sel}>Bugid
</small></td>
</tr>

<tr>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='pageview' {VAR:pageview_sel}>Vaatamine
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='e-mail' {VAR:e-mail_sel}>Meilid
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='alias' {VAR:alias_sel}>Aliased
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='menuedit' {VAR:menuedit_sel}>Men&uuml;&uuml;editor
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='object' {VAR:object_sel}>Objektid
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' name='types[]' value='msgboard' {VAR:msgboard_sel}>Foorumid
</small>
</td>
</tr>

<tr>
<td bgcolor=#f8f8f8 class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='link' {VAR:link_sel}>Lingile klikk
</small></td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='mliki' {VAR:mliki_sel}>Meili klikk
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small>&nbsp;</td>
<td bgcolor=#ffffff class="plain"><small>
<input type='checkbox' NAME='types[]' VALUE='promo' {VAR:promo_sel}>Promo kastid
</small></td>
<td bgcolor=#f8f8f8 class="plain"><small><input type='checkbox' NAME='types[]' VALUE='search' {VAR:search_sel}><input type='hidden' NAME='types[]' VALUE='dummy'>Otsing</small></td>
<td bgcolor=#ffffff class="plain"><small>&nbsp;</td>
</tr></table>
<!-- END SUB: selectors -->

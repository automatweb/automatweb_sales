			<table width="383" border="0" cellpadding="0" cellspacing="0">
			<!-- begin doc -->
			<tr>
			<td width="283" valign="top" class="textmoodul">

				<a href="{VAR:baseurl}/{VAR:docid}"><b>{VAR:title}</b></a>
	<!-- SUB: FORUM_ADD_SUB -->
	(<a href="{VAR:comm_link}" class="textrubriikcomm">{VAR:num_comments}</a>)
	<!-- END SUB: FORUM_ADD_SUB -->
	<br>
				<IMG SRC="{VAR:baseurl}/img/trans.gif" WIDTH="1" HEIGHT="5" BORDER="0" ALT=""><br>
				{VAR:text}

			</td>
			</tr>
			<!-- end doc -->
			</table>





<!-- SUB ablock
<span class="textauthor">by {VAR:author} {VAR:modified}</span><br>
END SUB ablock -->

<!-- SUB: image -->
<table border=0 cellpadding=0 cellspacing=0 {VAR:align}>
	<tr>
		<td><img src='{VAR:imgref}'></td>
	</tr>
	<tr>
		<td class="text">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image -->

<!-- SUB: image_linked -->
<table border=0 cellpadding=0 cellspacing=0 {VAR:align}>
	<tr>
		<td><a href='{VAR:plink}'><img border=0 src='{VAR:imgref}'></a></td>
	</tr>
	<tr>
		<td class="text">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_linked -->

<!-- SUB: link -->
<a {VAR:target} href='{VAR:url}'>{VAR:caption}</a>
<!-- END SUB: link -->


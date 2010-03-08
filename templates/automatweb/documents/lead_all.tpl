<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td><span class="text">
<!-- SUB: SHOW_TITLE -->
<b><font size=3><a href='{VAR:baseurl}/{VAR:docid}'>{VAR:title}</a></font></b>
<!-- END SUB: SHOW_TITLE -->

<!-- SUB: FORUM_ADD_SUB -->
<span class="text">(<a href="{VAR:comm_link}"><font color="red">{VAR:num_comments}</font></a>)</span><br>
<!-- END SUB: FORUM_ADD_SUB --> 

</span></td></tr>
<tr><td><span class="text">{VAR:text}<p></span><br>

<!-- SUB: SHOW_MODIFIED -->
<span class="text">{VAR:act_per_name}</span><br>
<!-- END SUB: SHOW_MODIFIED -->


<!-- SUB: ablock -->
<div align="right"><span class="text">Autor: {VAR:author}</span></div>
<div align="right"><span class="text">{VAR:photos}</span></div><br>
<!-- END SUB: ablock -->

</td></tr>

</table>
<!-- SUB: image -->
<table border=0 cellpadding=5 cellspacing=0 {VAR:align} width="{VAR:width}">
	<tr>
		<td><img src='{VAR:imgref}' alt="{VAR:alt}" title="{VAR:alt}"></td>
	</tr>
	<tr>
		<td class="pildiallkiri">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image -->

<!-- SUB: image_has_big -->
<table border=0 cellpadding=5 cellspacing=0 {VAR:align} width="{VAR:width}">
	<tr>
		<td><a href='javascript:void(0)' onClick="{VAR:bi_link}"><img border="0" src='{VAR:imgref}' alt="{VAR:alt}" title="{VAR:alt}"></a></td>
	</tr>
	<tr>
		<td class="pildiallkiri">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_has_big -->

<!-- SUB: image_big_linked -->
<table border=0 cellpadding=5 cellspacing=0 {VAR:align} width="{VAR:width}">
	<tr>
		<td><a href='javascript:void(0)' onClick="{VAR:bi_link}"><img border="0" src='{VAR:imgref}' alt="{VAR:alt}" title="{VAR:alt}"></a></td>
	</tr>
	<tr>
		<td class="pildiallkiri">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_big_linked -->


<!-- SUB: image_linked -->
<table border=0 cellpadding=5 cellspacing=0 {VAR:align} width="{VAR:width}">
	<tr>
		<td><a {VAR:target} href='{VAR:plink}'><img border=0 src='{VAR:imgref}' alt="{VAR:alt}" title="{VAR:alt}"></a></td>
	</tr>
	<tr>
		<td class="pildiallkiri">{VAR:imgcaption}</td>
	</tr>
</table>
<!-- END SUB: image_linked -->

<!-- SUB: link -->
<a {VAR:target} href='{VAR:url}'>{VAR:caption}</a>
<!-- END SUB: link -->


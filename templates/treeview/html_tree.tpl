<style type='text/css'>
.fgtext_bad {
font-family: Arial, Helvetica, sans-serif;
color: #002E73;
font-size: 12px;
text-decoration: none;
}

.fgtext_bad a {
color: #002E73; text-decoration: underline;
}

.fgtext_bad a:hover {
color: #002E73; text-decoration: underline;
}

</style>


<!-- SUB: MENU -->
<span class="text"><a href='{VAR:link}'>{VAR:name}</a></span>
<!-- END SUB: MENU -->

<!-- SUB: MENU_NOSUBS -->
<span class="text"><a href='{VAR:link}'>{VAR:name}</a></span>
<!-- END SUB: MENU_NOSUBS -->

<!-- SUB: MENU_SEL -->
<span class="text"><a href='{VAR:link}'><b>{VAR:name}</b></a></span>
<!-- END SUB: MENU_SEL -->

<!-- SUB: MENU_NOSUBS_SEL -->
<span class="text"><a href='{VAR:link}'><b>{VAR:name}</b></a></span>
<!-- END SUB: MENU_NOSUBS_SEL -->


<!-- SUB: INFO -->

<!-- END SUB: INFO -->

<!-- SUB: FTV_BLANK -->
<td><img HSPACE='0' VSPACE='0' src='{VAR:baseurl}/automatweb/images/ftv2blank.gif'></td>
<!-- END SUB: FTV_BLANK -->


<!-- SUB: FTV_VERTLINE -->
<td><img HSPACE='0' VSPACE='0' src='{VAR:baseurl}/automatweb/images/ftv2vertline.gif'></td>
<!-- END SUB: FTV_VERTLINE -->

<!-- SUB: FTV_PLASTNODE -->
<td><a href='{VAR:link}'><img HSPACE='0' VSPACE='0' border='0' src='{VAR:baseurl}/images/automatweb/ftv2plastnode.gif'></a></td>
<!-- END SUB: FTV_PLASTNODE -->

<!-- SUB: FTV_MNODE -->
<td><img HSPACE='0' VSPACE='0' src='{VAR:baseurl}/automatweb/images/ftv2mnode.gif'></td>
<!-- END SUB: FTV_MNODE -->

<!-- SUB: FTV_PNODE -->
<td><a href='{VAR:link}'><img HSPACE='0' VSPACE='0' border='0' src='{VAR:baseurl}/automatweb/images/ftv2pnode.gif'></a></td>
<!-- END SUB: FTV_PNODE -->

<!-- SUB: FTV_LASTNODE -->
<td><img HSPACE='0' VSPACE='0' src='{VAR:baseurl}/automatweb/images/ftv2lastnode.gif'></td>
<!-- END SUB: FTV_LASTNODE -->

<!-- SUB: FTV_NODE -->
<td><img HSPACE='0' VSPACE='0' src='{VAR:baseurl}/automatweb/images/ftv2node.gif'></td>
<!-- END SUB: FTV_NODE -->

<!-- SUB: FTV_ITEM -->
<tr>
	{VAR:str}
	<td colspan='{VAR:colspan}' class="text">{VAR:ms}</td>
	<td class="text">{VAR:changed}</td>
</tr>
<!-- END SUB: FTV_ITEM -->

<!-- SUB: TREE_BEGIN -->
<table border='0' cellpadding='0' cellspacing='0'>
<tr>
	<td colspan="{VAR:colspan}" class="text">&nbsp;</td>
	<td class="text">Muudetud</td>
</tr>
<!-- END SUB: TREE_BEGIN -->

<!-- SUB: TREE_END -->
</table>
<!-- END SUB: TREE_END -->

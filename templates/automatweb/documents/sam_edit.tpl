<script language="Javascript">
function iremote(oid) {
 var windowprops = "toolbar=0,location=1,directories=0,status=0, "+
"menubar=0,scrollbars=1,resizable=1,width=400,height=500";

OpenWindow = window.open("images{VAR:ext}?type=list&parent=" + oid, "remote", windowprops);
}
function remote2(url) {
OpenWindow = window.open(url);
}

var sel_link = 0;

function ch_link()
{
	if (!sel_link)
	{
		alert('Vali link, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=links&action=change&parent={VAR:id}&id="+sel_link;
	return true;
}

function del_link()
{
	if (!sel_link)
	{
		alert('Vali link, mida kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=links&action=delete&parent={VAR:id}&id="+sel_link;
	return true;
}

var sel_image = 0;

function ch_image()
{
	if (!sel_image)
	{
		alert('Vali pilt, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=images&action=change&id="+sel_image;
	return true;
}

function del_image()
{
	if (!sel_image)
	{
		alert('Vali pilt, mida kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=images&action=delete&docid={VAR:id}&id="+sel_image;
	return true;
}

var sel_table = 0;

function ch_table()
{
	if (!sel_table)
	{
		alert('Vali tabel, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=table&action=change&id="+sel_table;
	return true;
}

function del_table()
{
	if (!sel_table)
	{
		alert('Vali tabel, mille aliast kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=document&action=delete_alias&docid={VAR:id}&id="+sel_table;
	return true;
}

var sel_form = 0;

function ch_form()
{
	if (!sel_form)
	{
		alert('Vali form, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=form&action=change&id="+sel_form;
	return true;
}

function del_form()
{
	if (!sel_form)
	{
		alert('Vali form, mille aliast kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=document&action=delete_alias&docid={VAR:id}&id="+sel_form;
	return true;
}

var sel_file = 0;

function ch_file()
{
	if (!sel_file)
	{
		alert('Vali fail, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=file&action=change&doc={VAR:id}&id="+sel_file;
	return true;
}

function del_file()
{
	if (!sel_file)
	{
		alert('Vali fail, mille aliast kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=document&action=delete_alias&docid={VAR:id}&id="+sel_file;
	return true;
}

var sel_graph = 0;

function ch_graph()
{
	if (!sel_graph)
	{
		alert('Vali graafik, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=graph&action=change&doc={VAR:id}&id="+sel_graph;
	return true;
}

function del_graph()
{
	if (!sel_graph)
	{
		alert('Vali graafik, mille aliast kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=document&action=delete_alias&docid={VAR:id}&id="+sel_graph;
	return true;
}

var sel_gallery = 0;

function ch_gallery()
{
	if (!sel_gallery)
	{
		alert('Vali galerii, mida muuta soovid!');
		window.location="#";
		return true;
	}

	window.location="galerii{VAR:ext}?type=content&id="+sel_gallery;
	return true;
}

function del_gallery()
{
	if (!sel_gallery)
	{
		alert('Vali galerii, mille aliast kustutada soovid!');
		window.location="#";
		return true;
	}

	window.location="orb{VAR:ext}?class=document&action=delete_alias&docid={VAR:id}&id="+sel_gallery;
	return true;
}

</script>
<form method="POST" action="reforb{VAR:ext}" name="doc">
<br>
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="hele_hall_taust" colspan="2">
<input type="submit" class='doc_button' value="Salvesta"> <input class='doc_button' type="submit" value="Eelvaade" onClick="window.location.href='{VAR:preview}';return false;"> <input type="submit" class='doc_button' value="Sektsioonid" onClick="window.location.href='{VAR:menurl}';return false;"> <input type="submit" class='doc_button' value="Webile" onClick="window.location.href='{VAR:baseurl}/parem{VAR:ext}?section={VAR:id}';return false;"> <input type="submit" class='doc_button' value="Teised keeled" onClick="window.location.href='{VAR:lburl}';return false"> 
</td>
</tr>
<!-- SUB: DOC_BROS -->
<tr>
<td class="hele_hall_taust">{VAR:lang_name}</td>
<td class="hele_hall_taust"><a href='{VAR:chbrourl}'>{VAR:bro_name}</a></td>
</tr>
<!-- END SUB: DOC_BROS -->
<tr>
<td class="hele_hall_taust" COLSPAN=2>
<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td class="fcaption2_nt"><img src='{VAR:baseurl}/images/transa.gif' width=113 height=1 border=0><br><B>&nbsp;M‰‰rangud&nbsp;</b></td>
		<td class="fcaption2_nt" bgcolor="#CCCCCC"><img src='{VAR:baseurl}/images/transa.gif' height=10 width=1></td>
		<td class="fcaption2_nt">&nbsp;<i>&nbsp;</i></td>
	</tr>
</table>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td><img src='{VAR:baseurl}/images/transa.gif' width=113 height=1 border=0></td>
		<td width=100%><img src='{VAR:baseurl}/images/transa.gif' width=100 height=1></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td bgcolor="#CCCCCC"><img src='{VAR:baseurl}/images/transa.gif' width=2 height=1></td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="hele_hall_taust">Aktiivne:
						<select name="status" class='tekstikast'>
						{VAR:status}
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</td>
</tr>
<tr>
<td class="hele_hall_taust"><img src='{VAR:baseurl}/images/transa.gif' width=110 height=1><Br><B>&nbsp;Pealkiri&nbsp;</b></td>
<td class="hele_hall_taust"><input class='tekstikast' type="text" name="title" size="80" value="{VAR:title}"></td>
</tr>
<tr>
<td class="hele_hall_taust"><img src='{VAR:baseurl}/images/transa.gif' width=110 height=1><Br><B>&nbsp;Link&nbsp;</b></td>
<td class="hele_hall_taust"><input class='tekstikast' type="text" name="link_text" size="80" value="{VAR:link_text}"></td>
</tr>
<tr>
<td class="hele_hall_taust" valign="top"><b>&nbsp;Lead&nbsp;</b></td>
<td class="hele_hall_taust">
<textarea name="lead" cols="100" rows="5" class='tekstikast'>{VAR:lead}</textarea>
</td>
</tr>
<tr>
<td class="hele_hall_taust" valign="top"><b>&nbsp;Sisu&nbsp;</b></td>
<td class="hele_hall_taust">
<textarea name="content" cols="100" rows="30" class='tekstikast'>{VAR:content}</textarea>
</td>
</tr>
<tr>
<td class="hele_hall_taust" colspan=2><b>&nbsp;Objektid&nbsp;</b> <a href='pickobject{VAR:ext}?docid={VAR:id}'>Lisa uus &gt;&gt;&gt;</a><br>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td><img src='{VAR:baseurl}/images/transa.gif' width=113 height=1 border=0></td>
		<td width=100%><img src='{VAR:baseurl}/images/transa.gif' width=100 height=1></td>
	</tr>

	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Lingid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=8><a href="{VAR:addlink}">Lisa uus</a> | <a href="javascript:ch_link()">Muuda</a> | <a href="javascript:del_link()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&link_sortby=name&link_order={VAR:link_order}'>Nimi {VAR:link_name_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&link_sortby=comment&link_order={VAR:link_order}'>Kirjeldus {VAR:link_comment_img}</a></td>
					<td align="center" class="fcaption5_hele">Aadress</td>
					<td align="center" class="fcaption5_hele">Alias</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&link_sortby=modifiedby&link_order={VAR:link_order}'>Muutja {VAR:link_modifiedby_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&link_sortby=modified&link_order={VAR:link_order}'>Muudetud {VAR:link_modified_img}</a></td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: LINK_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_link}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onClick='this.select()'  onBlur='this.value="{VAR:address}";' type='text' value='{VAR:address}' size=30 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onClick='this.select()' type='text' value='{VAR:alias}' onBlur='this.value="{VAR:alias}";' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='links' value='{VAR:id}' onClick="sel_link={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='links_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: LINK_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Pildid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:add_img}">Lisa uus</a> | <a href="javascript:ch_image()">Muuda</a> | <a href="javascript:del_image();">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&pic_sortby=name&pic_order={VAR:pic_order}'>Nimi {VAR:pic_name_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&pic_sortby=comment&pic_order={VAR:pic_order}'>Kirjeldus {VAR:pic_comment_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&pic_sortby=alias&pic_order={VAR:pic_order}'>Alias {VAR:pic_alias_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&pic_sortby=modifiedby&pic_order={VAR:pic_order}'>Muutja {VAR:pic_modifiedby_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&pic_sortby=modified&pic_order={VAR:pic_order}'>Muudetud {VAR:pic_modified_img}</a></td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: IMG_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_img}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onBlur='this.value="{VAR:alias}";' onClick='this.select()' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='images' value='{VAR:id}' onClick="sel_image={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='images_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: IMG_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Tabelid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:addtable}">Lisa uus</a> | <a href="javascript:ch_table()">Muuda</a> | <a href="javascript:del_table()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&table_sortby=name&table_order={VAR:table_order}'>Nimi {VAR:table_name_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&table_sortby=comment&table_order={VAR:table_order}'>Kirjeldus {VAR:table_comment_img}</a></td>
					<td align="center" class="fcaption5_hele">Alias</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&table_sortby=modifiedby&table_order={VAR:table_order}'>Muutja {VAR:table_modifiedby_img}</a></td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&table_sortby=modified&table_order={VAR:table_order}'>Muudetud {VAR:table_modified_img}</a></td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: TABLE_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_table}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onClick='this.select()' onBlur='this.value="{VAR:alias}";' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='tables' value='{VAR:id}' onClick="sel_table={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='tables_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: TABLE_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Vormid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:addform}">Lisa uus</a> | <a href="javascript:ch_form()">Muuda</a> | <a href="javascript:del_form()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&form_sortby=name&form_order={VAR:form_order}'>Nimi</a> {VAR:form_name_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&form_sortby=comment&form_order={VAR:form_order}'>Kirjeldus</a> {VAR:form_comment_img}</td>
					<td align="center" class="fcaption5_hele">Alias</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&form_sortby=modifiedby&form_order={VAR:form_order}'>Muutja</a> {VAR:form_modifiedby_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&form_sortby=modified&form_order={VAR:form_order}'>Muudetud</a> {VAR:form_modified_img}</td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: FORM_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_form}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onBlur='this.value="{VAR:alias}";' onClick='this.select()' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='forms' value='{VAR:id}' onClick="sel_form={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='forms_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: FORM_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Failid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:addfile}">Lisa uus</a> | <a href="javascript:ch_file()">Muuda</a> | <a href="javascript:del_file()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&file_sortby=name&file_order={VAR:file_order}'>Nimi</a> {VAR:file_name_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&file_sortby=comment&file_order={VAR:file_order}'>Kirjeldus</a> {VAR:file_comment_img}</td>
					<td align="center" class="fcaption5_hele">Alias</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&file_sortby=modifiedby&file_order={VAR:file_order}'>Muutja</a>, {VAR:file_modifiedby_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&file_sortby=modified&file_order={VAR:file_order}'>Muudetud</a> {VAR:file_modified_img}</td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: FILE_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_file}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onBlur='this.value="{VAR:alias}";'  onClick='this.select()' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='files' value='{VAR:id}' onClick="sel_file={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='files_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: FILE_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Graafikud</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:addgraph}">Lisa uus</a> | <a href="javascript:ch_graph()">Muuda</a> | <a href="javascript:del_graph()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&graph_sortby=name&graph_order={VAR:graph_order}'>Nimi</a> {VAR:graph_name_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&graph_sortby=comment&graph_order={VAR:graph_order}'>Kirjeldus</a> {VAR:graph_comment_img}</td>
					<td align="center" class="fcaption5_hele">Alias</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&graph_sortby=modifiedby&graph_order={VAR:graph_order}'>Muutja</a> {VAR:graph_modifiedby_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&graph_sortby=modified&graph_order={VAR:graph_order}'>Muudetud</a> {VAR:graph_modified_img}</td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: GRAPH_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_graph}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onBlur='this.value="{VAR:alias}";'  onClick='this.select()' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='graphs' value='{VAR:id}' onClick="sel_graph={VAR:id}"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='graphs_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: GRAPH_LINE -->
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=2><img src='{VAR:baseurl}/images/transa.gif' width=10 height=16></td>
	</tr>
	<tr>
		<td valign=top>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title">Galeriid</td>
				</tr>
			</table>
		</td>
		<td>
			<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC" width="100%">
				<tr>
					<td class="title" colspan=7><a href="{VAR:addgallery}">Lisa uus</a> | <a href="javascript:ch_gallery()">Muuda</a> | <a href="javascript:del_gallery()">Kustuta</a></td>
				</tr>
				<tr>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&gallery_sortby=name&gallery_order={VAR:gallery_order}'>Nimi</a> {VAR:gallery_name_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&gallery_sortby=comment&gallery_order={VAR:gallery_order}'>Kirjeldus</a> {VAR:gallery_comment_img}</td>
					<td align="center" class="fcaption5_hele">Alias</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&gallery_sortby=modifiedby&gallery_order={VAR:gallery_order}'>Muutja</a> {VAR:gallery_modifiedby_img}</td>
					<td align="center" class="fcaption5_hele"><a href='{VAR:url}&gallery_sortby=modified&gallery_order={VAR:gallery_order}'>Muudetud</a> {VAR:gallery_modified_img}</td>
					<td class="title" align="center" colspan=2>Vali</td>
				</tr>
				<!-- SUB: GALLERY_LINE -->
				<tr>
					<td align="center" class="fcaption5_hele_taust"><a href='{VAR:ch_gallery}'>{VAR:name}</a></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:comment}</td>
					<td align="center" class="fcaption5_hele_taust"><input class='tekstikast_n'  onClick='this.select()' onBlur='this.value="{VAR:alias}";' type='text' value='{VAR:alias}' size=5 class='small_button'></td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modifiedby}</td>
					<td align="center" class="fcaption5_hele_taust">{VAR:modified}</td>
					<td align="center" class="title" width=1><input type='radio' name='galleries' value='{VAR:id}' onClick="sel_gallery={VAR:id};"></td>
					<td align="center" class="title" width=1><input type='checkbox' name='galleries_c[{VAR:id}]' value=1></td>
				</tr>
				<!-- END SUB: GALLERY_LINE -->
			</table>
		</td>
	</tr>

</table>
</td>
</tr>
<tr>
<td class="hele_hall_taust" colspan="2">
<input type="submit" class='doc_button' value="Salvesta"> <input class='doc_button' type="submit" value="Eelvaade" onClick="window.location.href='{VAR:preview}';return false;"> <input type="submit" class='doc_button' value="Sektsioonid" onClick="window.location.href='{VAR:menurl}';return false;"> <input type="submit" class='doc_button' value="Webile" onClick="remote2('{VAR:weburl}')"> <input type="submit" class='doc_button' value="Teised keeled" onClick="window.location.href='{VAR:lburl}';return false"> 
</td>
</tr>
</table>
{VAR:reforb}
</form>

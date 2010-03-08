<div class="forum">
<table>
<tr>
<td class="{VAR:style_forum_yah}">
<strong>{VAR:path}</strong>
</td>
<td nowrap class="{VAR:style_caption}">
<!-- SUB: PAGER -->
<!-- SUB: active_page -->
 <strong>[ {VAR:num} ]</strong>
<!-- END SUB: active_page -->
<!-- SUB: page -->
 <a href="{VAR:url}">{VAR:num}</a> 
<!-- END SUB: page -->
<!-- END SUB: PAGER -->
</td>
</tr>
</table>

<table class="thread">
<tr class="creator_post">
	<td class="creator {VAR:style_comment_creator}"><div class="user {VAR:style_comment_user}">{VAR:author_name}</div>
	<!-- SUB: AVATAR -->
	<img src="{VAR:avatar}" /><br />
	<!-- END SUB: AVATAR -->
	<!-- SUB: AGE -->
	<br />Vanus:<br />{VAR:age}
	<!-- END SUB: AGE -->
	<!-- SUB: LOCATION -->
	<br />Asukoht:<br />{VAR:location}
	<!-- END SUB: LOCATION -->
	{VAR:date}
	<!-- SUB: ADMIN_TOPIC -->
	(admin)
	<!-- END SUB: ADMIN_TOPIC -->
	<!-- SUB: IMAGE -->
	<br />
	<img src="{VAR:image_url}" alt="Administraatori pilt" title="Administraatori pilt" />
	<!-- END SUB: IMAGE -->
</td>
	<td class="content {VAR:style_comment_count}"><strong>{VAR:name}</strong><p>{VAR:comment}{VAR:topic_image1}</td>
</tr>
<!-- SUB: COMMENT -->
<tr>
	<td class="creator {VAR:style_comment_time}"><div class="style_comment_user {VAR:style_comment_user}">{VAR:uname} -- {VAR:uemail}</div>
	<!-- SUB: CAVATAR -->
	<img src="{VAR:avatar}" />
	<!-- END SUB: CAVATAR -->
	<!-- SUB: CAGE -->
	<br />Vanus:<br />{VAR:age}
	<!-- END SUB: CAGE -->
	<!-- SUB: CLOCATION -->
	<br />Asukoht:<br />{VAR:location}
	<!-- END SUB: CLOCATION -->
	<div class="">{VAR:date}</div>
	<!-- SUB: ADMIN_POST -->
	(admin)
	<!-- END SUB: ADMIN_POST -->
	<!-- SUB: IMAGE -->
	<img src="{VAR:image_url}" alt="Administraatori pilt" title="Administraatori pilt" /> 
	<!-- END SUB: IMAGE -->
</td>
	<td class="content {VAR:style_comment_text}">
		<!-- SUB: ADMIN_BLOCK -->
		<div align="right">
		<strong>IP: {VAR:ip}</strong><br />
		<input type="checkbox" name="del[]" value="{VAR:id}" />
		</div>
		<!-- END SUB: ADMIN_BLOCK -->
	<a name="c{VAR:id}"></a>
	<strong>{VAR:name}</strong> 
		<!-- SUB: CHANGE_LINK -->
		<a href="{VAR:change_url}">Muuda</a>
		<!-- END SUB: CHANGE_LINK -->
	<p>{VAR:commtext}{VAR:comment_image1}
	</td>
</tr>
<!-- END SUB: COMMENT -->
</table>
<!-- SUB: DELETE_ACTION -->
<input type="button" name="delete_comments" value="Kustuta valitud kommentaarid" onClick="if(confirm('Kustutada?')){document.changeform.action.value='delete_comments';document.changeform.submit();}" />
<!-- END SUB: DELETE_ACTION -->
</div>
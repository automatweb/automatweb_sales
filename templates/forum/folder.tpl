<div class="foorum forum-folder">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td colspan="2" height="20" class="{VAR:style_new_topic_row}" colspan="2"><a href="{VAR:add_topic_url}"><img src="{VAR:baseurl}automatweb/images/forum/forum_add_new.gif" align="absmiddle" border="0" alt="Lisa uus teema"></a> <a href="{VAR:add_topic_url}">Lisa uus teema</a>
</td>
</tr>
<tr>
<!-- SUB: SHOW_PATH -->
<td class="{VAR:style_forum_yah}">
<strong>{VAR:path}</strong>
</td>
<!-- END SUB: SHOW_PATH -->
<td>
<!-- SUB: PAGER -->
<!-- SUB: active_page -->
[{VAR:num}]
<!-- END SUB: active_page -->
<!-- SUB: page -->
 <a href="{VAR:url}">{VAR:num}</a>
<!-- END SUB: page -->
<!-- END SUB: PAGER -->
</td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="3" width="100%" style="border-collapse: collapse; margin-top: 4px;" >
<tr>
	<th colspan=2 class="{VAR:style_caption} pealkiri">Teemad</th>
	<th class="{VAR:style_caption} pealkiri">Vastuseid</th>
	<th class="{VAR:style_caption} pealkiri">Autor</th>
	<th class="{VAR:style_caption} pealkiri">Viimane vastus</th>
</tr>

<!-- SUB: SUBTOPIC -->
<tr class="">
	<td class="{VAR:style_topic_caption} column"><center><img src="{VAR:baseurl}automatweb/images/forum_arrow_sm.gif"></center></td>
	<td class="{VAR:style_topic_caption} column">
	<!-- SUB: ADMIN_BLOCK -->
	<input type="checkbox" name="sel_topic[{VAR:topic_id}]" value="1" /><a href="{VAR:add_faq_url}">[ lisa KKK ]</a>
	<!-- END SUB: ADMIN_BLOCK -->
	<a href="{VAR:open_topic_url}">{VAR:name}</a>
	</td>
	<td class="{VAR:style_topic_replies} column">{VAR:comment_count}</td>
	<td class="{VAR:style_topic_author} column">{VAR:author}</td>
	<td class="{VAR:style_topic_last_post} column">{VAR:last_date} {VAR:last_createdby}</td>
</tr>

<!-- END SUB: SUBTOPIC -->
</table>
<!-- SUB: DELETE_ACTION -->
<input type="submit" name="delete_selected_topics" value="Kustuta valitud teemad">
<!-- END SUB: DELETE_ACTION -->


</div>

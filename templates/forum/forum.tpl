<table>
<tr>
	<td colspan="3" class="{VAR:style_caption}" align="center">Foorum</td>
	<td class="{VAR:style_caption}" align="center">Teemasid</td>
	<td class="{VAR:style_caption}" align="center">Postitusi</td>
	<td class="{VAR:style_caption}" align="center">Viimane</td>
</tr>
{VAR:forum_contents}
<!-- SUB: FOLDER -->
<tr>
<td colspan="6" class="{VAR:style_l1_folder}">{VAR:spacer}<a href="{VAR:open_l1_url}">{VAR:name}</a></td>
</tr>
<!-- END SUB: FOLDER -->

<!-- SUB: LAST_LEVEL -->
<tr>
	<td class="{VAR:style_folder_caption}">{VAR:spacer}</td>
	<td class="{VAR:style_folder_caption}"><img src="{VAR:baseurl}/automatweb/images/forum/forum_arrow_sm.gif"><center></center></td>
	<td class="{VAR:style_folder_caption}"><a href="{VAR:open_topic_url}">{VAR:name}</a><br>{VAR:comment}</td>
	<td class="{VAR:style_folder_topic_count}" align="center">{VAR:topic_count}</td>
	<td class="{VAR:style_folder_comment_count}" align="center">{VAR:comment_count}</td>
	<td class="{VAR:style_folder_last_post}" align="center">{VAR:last_createdby}<br>{VAR:last_date}</td>
</tr>
<!-- END SUB: LAST_LEVEL -->
</table>

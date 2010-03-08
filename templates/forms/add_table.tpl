<span class="fform">
<a href="{VAR:aliasmgr_link}">Alias manager</a>
</span>
<form action='reforb.{VAR:ext}' method=post>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
<td class="fform">{VAR:LC_FORMS_NAME}:</td><td class="fform"><input type='text' NAME='name' VALUE='{VAR:name}'></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_COMMENT}:</td><td class="fform"><textarea NAME='comment' cols=50 rows=5>{VAR:comment}</textarea></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_HOW_MANY_COLUMNS}:</td><td class="fform"><input type='text' NAME='num_cols' VALUE='{VAR:num_cols}' size=3></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_TABLE_STYLE}:</td><td class="fform"><select name='tablestyle'>{VAR:tablestyles}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_TABLE_STYLE_ORDINARY}:</td><td class="fform"><select name='header_normal'>{VAR:header_normal}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_TABLE_STYLE_POSS_SORT}:</td><td class="fform"><select name='header_sortable'>{VAR:header_sortable}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_TABLE_STYLE_SORTED}:</td><td class="fform"><select name='header_sorted'>{VAR:header_sorted}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_CELL_STYLE_1}:</td><td class="fform"><select name='content_style1'>{VAR:content_style1}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_CELL_STYLE_2}:</td><td class="fform"><select name='content_style2'>{VAR:content_style2}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_SORTED_CELL_SYLE_1}:</td><td class="fform"><select name='content_sorted_style1'>{VAR:content_sorted_style1}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_SORTED_CELL_SYLE_2}:</td><td class="fform"><select name='content_sorted_style2'>{VAR:content_sorted_style2}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_LINK_STYLE}:</td><td class="fform"><select name='link_style'>{VAR:link_style}</select></td>
</tr>
<tr>
<td class="fform">Grupi rea stiil:</td><td class="fform"><select name='group_style'>{VAR:group_style}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_PRINT_BUTTON}</td><td class="fform"><input type="checkbox" name="print_button" value="1" {VAR:print_button}></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_WHACH_LINK_POP}:</td>
<td class="fform">
	<input type="checkbox" name="view_new_win" value="1" {VAR:view_new_win}>
</td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_POPUP_WIN_PARA}:</td><td class="fform"><input type="text" name="new_win_x" value="{VAR:new_win_x}" size="3">x<input type="text" name="new_win_y" value="{VAR:new_win_y}" size="3">
	{VAR:LC_FORMS_SCROLLBAR}: <input type="checkbox" name="new_win_scroll" {VAR:new_win_scroll} value="1">
	{VAR:LC_FORMS_FIX_SIZE}: <input type="checkbox" name="new_win_fixedsize" {VAR:new_win_fixedsize} value="1">
</td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_CHOOSE_FORMS_WHERE_ELEMENTS_TAKEN}:</td><td class="fform"><select class='small_button' name='forms[]' multiple size=7>{VAR:forms}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_CHOOSE_CATALOGUES_WHERE_ENTRIES}:</td><td class="fform"><select class='small_button' name='moveto[]' size=10 multiple>{VAR:moveto}</select></td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_SUBSMIT_BUTTON}</td><td class="fform">{VAR:LC_FORMS_TEXT}: <input type='text' name='submit_text' value='{VAR:submit_text}'> Jrk: <input type='text' class='small_button' size=3 value='{VAR:submit_jrk}'>{VAR:LC_FORMS_UP}  <input type='checkbox' name='submit_top' value='1' {VAR:top_checked}>{VAR:LC_FORMS_DOWN}  <input type='checkbox' name='submit_bottom' value='1' {VAR:bottom_checked}> </td>
</tr>
<tr>
<td class="fform">{VAR:LC_FORMS_ADD_BUTTON}:</td><td class="fform">{VAR:LC_FORMS_TEXT}: <input type='text' name='user_button_text' value='{VAR:user_button_text}'> Jrk: <input type='text' class='small_button' size=3 value='{VAR:but_jrk}'> {VAR:LC_FORMS_UP} <input type='checkbox' name='user_button_top' value='1' {VAR:user_button_top}>{VAR:LC_FORMS_DOWN} <input type='checkbox' name='user_button_bottom' value='1' {VAR:user_button_bottom}>  &nbsp;{VAR:LC_FORMS_ADDRESS}:<input type='text' name='user_button_url' value='{VAR:user_button_url}'> </td>
</tr>
<tr>
<td class="fform">Vali checkboxi default:</td><td class="fform"><input type='checkbox' name='sel_def' value='1' {VAR:sel_def}></td>
</tr>
<!-- SUB: CHANGE -->
<tr>
<td class="fform" colspan=2>{VAR:LC_FORMS_CHOOSE_WHICH_COUMN_ELEMENT}:</td>
</tr>
<!-- SUB: COL -->
<tr>
<td class="fform" colspan=2>
<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
<tr>
	<td class="fform" colspan=10>{VAR:LC_FORMS_COLUMN} {VAR:column}:</td>
</tr>
<tr>
	<!-- SUB: LANG_H -->
	<td class="fform">{VAR:LC_FORMS_COLUMN_TITLE} ({VAR:lang_name})</td>
	<!-- END SUB: LANG_H -->
</tr>
<tr>
	<!-- SUB: LANG -->
	<td class="fform"><input type='text' class='small_button' name='names[{VAR:column}][{VAR:lang_id}]' VALUE='{VAR:c_name}'></td>
	<!-- END SUB: LANG -->
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_CHOOSE_ELEMENTS}:</td>
	<td class="fform" ><select class="small_button" size=7 name='columns[{VAR:column}][]' multiple>{VAR:elements}</select></td>

	<td class="fform">Vali alias:</td>
	<td class="fform"><select class="small_button" size=7 name='aliases[{VAR:column}]'><option value=''>{VAR:aliases}</select></td>

	<!-- SUB: NOT_ORDER -->
	<td class="fform">Vali lingi element:</td>
	<td class="fform"><select class="small_button" size=7 name='link_columns[{VAR:column}]'><option value=''>{VAR:link_elements}</select></td>
	<!-- END SUB: NOT_ORDER -->

	<!-- SUB: ORDER -->
	<td class="fform">Vali tellimis url:</td>
	<td class="fform"><input class="small_button" type='text' name='order_forms[{VAR:column}]' size='20' value='{VAR:order_forms}'></td>
	<!-- END SUB: ORDER -->

	<td class="fform">Vali mis gruppidele n&auml;idatakse:</td>
	<td class="fform" colspan=8><select class="small_button" multiple size=7 name='show_grps[{VAR:column}][]'><option value=''>{VAR:show_grps}</select></td>
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_SORTABLE}:&nbsp;<input type="checkbox" name="sortable[{VAR:column}]" value="1" {VAR:sortable}> E-mail?&nbsp;<input type="checkbox" name="is_email[{VAR:column}]" value="1" {VAR:is_email}></td>
	<td class="fform" colspan=2>&nbsp;&nbsp;<a href='{VAR:add_col}'>{VAR:LC_FORMS_ADD_COLU}</a>&nbsp;&nbsp;<a href='{VAR:del_col}'>{VAR:LC_FORMS_DEL_COLU}</a> <input type='checkbox' name='todelete[{VAR:column}]' value='1'></td>
	<td class="fform" colspan="8">Tulbale klikkides tehakse otsing: <input type="checkbox" name='doelsearch[{VAR:column}]' value='1' {VAR:doelsearch}> Link: <input type="checkbox" name='linkels[{VAR:column}]' value='1' {VAR:linkel}></td>
</tr>
</table>
</td>
</tr>
<!-- END SUB: COL -->

<tr>
	<td class="fform" colspan="2">
		<table bgcolor="#CCCCCC" cellpadding=3 cellspacing=1 border=0>
		<tr>
			<td class="fform">Element</td>
			<!-- SUB: CLANG_H -->
			<td class="fform">Tulba tekst ({VAR:lang_name})</td>
			<!-- END SUB: CLANG_H -->
		</tr>
		<!-- SUB: COL_TEXT -->
		<tr>
			<td class="fform">{VAR:eltype}</td>
			<!-- SUB: CLANG -->
			<td class="fform"><input type='text' class='small_button' name='texts[{VAR:eltype}][{VAR:lang_id}]' VALUE='{VAR:t_name}'></td>
			<!-- END SUB: CLANG -->
		</tr>
		<!-- END SUB: COL_TEXT -->
		</table>
	</td>
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_CHOOSE_LOOK_EL}:</td>
	<td class="fform"><select name="viewcol">{VAR:v_elements}</select></td>
</tr>
<tr>
	<td class="fform">{VAR:LC_FORMS_CHOOSE_CHANGE_EL}:</td>
	<td class="fform"><select name="changecol">{VAR:c_elements}</select></td>
</tr>
<tr>
	<td class="fform">Vaikimisi on sorditud:</td>
	<td class="fform"><select name="defaultsort">{VAR:ds_elements}</select></td>
</tr>
<tr>
	<td class="fform">J&auml;ta v&auml;lja mitmekordsed:</td>
	<td class="fform"><select name="group_el">{VAR:g_elements}</select></td>
</tr>
<tr>
	<td class="fform">Grupeeri:</td>
	<td class="fform"><select name="rgroup_el">{VAR:rg_elements}</select></td>
</tr>
<!-- END SUB: CHANGE -->
<tr>
<td class="fform" colspan=2><input class='small_button' type='submit' VALUE='{VAR:LC_FORMS_SAVE}'></td>
</tr>
</table>
{VAR:reforb}
</form>



<style>
table.cfgform_layout_tbl
{
	font-size: 11px;
	border-collapse: collapse;
	border-color: #CCC;
}

.cfgform_layout_tbl td
{
	vertical-align: top;
}

.cfgform_layout_tbl td input
{
	border: 1px solid #CCC;
	padding: 2px;
	background-color: #FCFCEC;
}
</style>

<script type="text/javascript">
function cfgformToggleOpts(id)
{
	el = document.getElementById("cfgformPrpOpts" + id);
	im = document.getElementById("cfgformOptsBtn" + id);

	if (el.style.display=="none")
	{
		el.style.display="block";
		im.src="{VAR:baseurl}/automatweb/images/aw06/closer_up.gif";
	}
	else
	{
		el.style.display="none";
		im.src="{VAR:baseurl}/automatweb/images/aw06/closer_down.gif";
	}
}

function cfgformToggleSelectProps(grpId)
{
	var inputElems = document.body.getElementsByTagName("input");
	var el = null;
	var prevState = null;

	for (i in inputElems)
	{
		el = inputElems[i];

		if ("checkbox" == el.type && el.className == ("prpGrp" + grpId))
		{
			prevState = el.checked;
			el.checked = !prevState;
		}
	}
}
</script>

<fieldset style="border: 1px solid blue; -moz-border-radius: 0.5em;">
	<legend>{VAR:capt_legend_tbl}</legend>
	<table cellpadding="2" class="cfgform_layout_tbl">
	<tr>
		<td width="50">{VAR:capt_prp_order}</td>
		<td width="100">{VAR:capt_prp_key}</td>
		<td width="150">{VAR:capt_prp_caption}</td>
		<td width="100">{VAR:capt_prp_type}</td>
		<td width="30"></td>
	</tr>
	</table>
</fieldset>

<!-- SUB: group -->
<fieldset style="border: 1px solid #AAA; -moz-border-radius: 0.5em;">
<legend>{VAR:grp_caption}</legend>
	<table cellpadding="2" class="cfgform_layout_tbl">
	<!-- SUB: select_toggle -->
	<tr>
		<td colspan="6" style="text-align: right;"><a href="javascript:cfgformToggleSelectProps('{VAR:grp_id}')">{VAR:capt_prp_mark}</a></td>
	</tr>
	<!-- END SUB: select_toggle -->
	<!-- SUB: layout -->
	<tr>
		<td colspan="6" style="color: green">Layout: {VAR:layout_name} [{VAR:layout_type}]</td>
	</tr>
	<tr>
		<td colspan="6" style="background-color:#">
			<table cellpadding="2" class="cfgform_layout_tbl" style="border: 1px solid green">
				{VAR:layout_props}
			</table>
		</td>
	</tr>
	<!-- END SUB: layout -->
	<!-- SUB: property -->
	<tr>
		<td width="50" bgcolor="{VAR:bgcolor}"><input type="text" name="prpconfig[{VAR:prp_key}][ord]" value="{VAR:prp_order}" size="2"/></td>
		<td width="100" bgcolor="{VAR:bgcolor}">{VAR:prp_key}</td>
		<td width="150" bgcolor="{VAR:bgcolor}"><input type="text" name="prpconfig[{VAR:prp_key}][caption]" value="{VAR:prp_caption}"/></td>
		<td width="100" bgcolor="{VAR:bgcolor}">{VAR:prp_type}</td>
		<td width="250" bgcolor="{VAR:bgcolor}">
		<!-- SUB: options -->
			<div class="closer">{VAR:prp_opts_caption} <a href="javascript:cfgformToggleOpts({VAR:tmp_id})"><img src="{VAR:baseurl}/automatweb/images/aw06/closer_down.gif" id="cfgformOptsBtn{VAR:tmp_id}" width="20" height="15" border="0"></a></div>
			<div id="cfgformPrpOpts{VAR:tmp_id}" style="display: none;">
				{VAR:comment_caption} <textarea name="prpconfig[{VAR:prp_key}][comment]" cols="25" rows="3">{VAR:comment}</textarea><br/>
				{VAR:comment_style_text_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][comment_style]" value="text"{VAR:comment_style_text_ch}/><br/>
				{VAR:comment_style_popup_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][comment_style]" value="popup"{VAR:comment_style_popup_ch}/><br/>
				{VAR:no_caption_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][no_caption]" value="1"{VAR:no_caption_checked}/>
				<input type="hidden" name="xconfig[{VAR:prp_key}][no_caption]" value="{VAR:no_caption}"/><br/>
				{VAR:disabled_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][disabled]" value="1"{VAR:disabled_checked}/>
				<input type="hidden" name="xconfig[{VAR:prp_key}][disabled]" value="{VAR:disabled}"/><br/>
				{VAR:captionside_l_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][captionside]" value="left"{VAR:captionside_l_ch}/><br/>
				{VAR:captionside_t_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][captionside]" value="top"{VAR:captionside_t_ch}/><br/>
				{VAR:textsize_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][textsize]" value="{VAR:textsize}"/><br/>
{VAR:prp_options}
				<!-- SUB: emb_tbl -->
				{VAR:show_in_emb_tbl_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][show_in_emb_tbl]" value="1"{VAR:show_in_emb_tbl_checked}/>
				<input type="hidden" name="xconfig[{VAR:prp_key}][show_in_emb_tbl]" value="{VAR:show_in_emb_tbl}"/><br/>
				{VAR:emb_tbl_controller_caption} <input type="text" size="7" name="prpconfig[{VAR:prp_key}][emb_tbl_controller]" value="{VAR:emb_tbl_controller}"/><br/>
				{VAR:emb_tbl_caption_caption} <input type="text" size="30" name="prpconfig[{VAR:prp_key}][emb_tbl_caption]" value="{VAR:emb_tbl_caption}"/><br/>
				{VAR:emb_tbl_col_num_caption} <input type="text" size="3" name="prpconfig[{VAR:prp_key}][emb_tbl_col_num]" value="{VAR:emb_tbl_col_num}"/>
				{VAR:emb_tbl_col_sep_caption} <input type="text" size="3" name="prpconfig[{VAR:prp_key}][emb_tbl_col_sep]" value="{VAR:emb_tbl_col_sep}"/>
				<!-- END SUB: emb_tbl -->
			</div>
		<!-- END SUB: options -->
		</td>
		<td width="30" align="center" bgcolor="{VAR:bgcolor}"><input type="checkbox" class="prpGrp{VAR:grp_id}" name="mark[{VAR:prp_mark_key}]" value="1" style="border: 3px solid blue;"/></td>
	</tr>
	<!-- END SUB: property -->
	<!-- SUB: property_disabled -->
	<tr>
		<td width="50" bgcolor="{VAR:bgcolor}">{VAR:prp_order}</td>
		<td width="100" bgcolor="{VAR:bgcolor}">{VAR:prp_key}</td>
		<td width="150" bgcolor="{VAR:bgcolor}">{VAR:prp_caption}</td>
		<td width="100" bgcolor="{VAR:bgcolor}">{VAR:prp_type}</td>
		<td width="250" bgcolor="{VAR:bgcolor}"></td>
		<td width="30" align="center" bgcolor="{VAR:bgcolor}"><input type="checkbox" class="prpGrp{VAR:grp_id}" name="mark[{VAR:prp_mark_key}]" value="1" style="border: 3px solid blue;"/></td>
	</tr>
	<!-- END SUB: property_disabled -->
	</table>
</fieldset>
<!-- END SUB: group -->

<!-- SUB: image_verification_options -->
			{VAR:width_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][width]" value="{VAR:width}"/><br/>
			{VAR:height_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][height]" value="{VAR:height}"/><br/>
			{VAR:text_color_caption} <input type="text" size="6" name="prpconfig[{VAR:prp_key}][text_color]" value="{VAR:text_color}"/><br/>
			{VAR:background_color_caption} <input type="text" size="6" name="prpconfig[{VAR:prp_key}][background_color]" value="{VAR:background_color}"/><br/>
			{VAR:font_size_caption} <input type="text" size="6" name="prpconfig[{VAR:prp_key}][font_size]" value="{VAR:font_size}"/><br/>
			{VAR:sidetop_caption} <input type="radio" size="2" name="prpconfig[{VAR:prp_key}][side]" value="top"{VAR:sidetop_ch}/><br/>
			{VAR:sidebottom_caption} <input type="radio" size="2" name="prpconfig[{VAR:prp_key}][side]" value="bottom"{VAR:sidebottom_ch}/><br/>
			{VAR:sideleft_caption} <input type="radio" size="2" name="prpconfig[{VAR:prp_key}][side]" value="left"{VAR:sideleft_ch}/><br/>
			{VAR:sideright_caption} <input type="radio" size="2" name="prpconfig[{VAR:prp_key}][side]" value="right"{VAR:sideright_ch}/><br/>
			{VAR:textbox_size_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][textbox_size]" value="{VAR:textbox_size}"/><br/>
<!-- END SUB: image_verification_options -->

<!-- SUB: textarea_options -->
			{VAR:richtext_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][richtext]" value="1"{VAR:richtext_checked}/>
			<input type="hidden" name="xconfig[{VAR:prp_key}][richtext]" value="{VAR:richtext}"/><br/>
			{VAR:rows_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][rows]" value="{VAR:rows}"/><br/>
			{VAR:cols_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][cols]" value="{VAR:cols}"/><br/>
			{VAR:maxlength_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][maxlength]" value="{VAR:maxlength}"/><br/>
<!-- END SUB: textarea_options -->

<!-- SUB: textbox_options -->
			{VAR:size_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][size]" value="{VAR:size}"/><br/>
			{VAR:maxlength_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][maxlength]" value="{VAR:maxlength}"/><br/>
<!-- END SUB: textbox_options -->

<!-- SUB: relpicker_options -->
			{VAR:no_edit_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][no_edit]" value="1"{VAR:no_edit_checked}/>
			<input type="hidden" name="xconfig[{VAR:prp_key}][no_edit]" value="{VAR:no_edit}"/><br/>
			{VAR:no_search_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][no_search]" value="1"{VAR:no_search_checked}/>
			<input type="hidden" name="xconfig[{VAR:prp_key}][no_search]" value="{VAR:no_search}"/><br/>
			{VAR:displayradio_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][display]" value="radio"{VAR:displayradio_ch}/>
			{VAR:displayselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][display]" value="select"{VAR:displayselect_ch}/><br/>
			{VAR:stylenormal_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][mode]" value=""{VAR:stylenormal_ch}/>
			{VAR:styleac_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][mode]" value="autocomplete"{VAR:styleac_ch}/><br/>
			<!-- SUB: rlp_ops_oit -->
			{VAR:oit_caption} <input type="checkbox" name="prpconfig[{VAR:prp_key}][option_is_tuple]" value="1"{VAR:option_is_tuple_checked}/><input type="hidden" name="xconfig[{VAR:prp_key}][option_is_tuple]" value="{VAR:option_is_tuple}"/><br/>
			<!-- END SUB: rlp_ops_oit -->
			<!-- SUB: rlp_ops_mult -->
			{VAR:multiple_caption} <input type="checkbox" name="prpconfig[{VAR:prp_key}][multiple]" value="1"{VAR:multiple_checked}/><input type="hidden" name="xconfig[{VAR:prp_key}][multiple]" value="{VAR:multiple}"/><br/>
			<!-- END SUB: rlp_ops_mult -->
			{VAR:size_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][size]" value="{VAR:size}"/><br/>
<!-- END SUB: relpicker_options -->

<!-- SUB: releditor_options -->
			{VAR:cfgform_id_caption} <input type="text" size="10" name="prpconfig[{VAR:prp_key}][cfgform_id]" value="{VAR:cfgform_id}"/><br/>
			{VAR:obj_parent_caption} <input type="text" size="10" name="prpconfig[{VAR:prp_key}][obj_parent]" value="{VAR:obj_parent}"/><br/>
			use_form <input type="text" size="10" name="prpconfig[{VAR:prp_key}][use_form]" value="{VAR:use_form}"/><br/>
			rel_id <input type="text" size="10" name="prpconfig[{VAR:prp_key}][rel_id]" value="{VAR:rel_id}"/><br/>
			mode <input type="text" size="10" name="prpconfig[{VAR:prp_key}][mode]" value="{VAR:mode}"/><br/>
<!-- END SUB: releditor_options -->

<!-- SUB: select_options -->
			{VAR:size_caption} <input type="text" size="2" name="prpconfig[{VAR:prp_key}][size]" value="{VAR:size}"/><br/>
<!-- END SUB: select_options -->

<!-- SUB: classificator_options -->
			{VAR:sort_callback_caption} <input type="text" size="10" name="prpconfig[{VAR:prp_key}][sort_callback]" value="{VAR:sort_callback}"/><br/>
<!-- END SUB: classificator_options -->

<!-- SUB: table_options -->
			{VAR:configurable_caption}<input type="checkbox" name="prpconfig[{VAR:prp_key}][configurable]" value="1"{VAR:configurable_checked}/><br/>
<!-- END SUB: table_options -->

<!-- SUB: date_select_options -->
			{VAR:buttons_show_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][buttons]" value="1"{VAR:buttons_show_ch}/>
			{VAR:buttons_hide_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][buttons]" value="0"{VAR:buttons_hide_ch}/>
			{VAR:buttons_default_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][buttons]" value=""{VAR:buttons_default_ch}/><br/>
			<b>{VAR:format_caption}</b>
			<br />
			{VAR:format_dayselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][day]" value="day"{VAR:format_dayselect_ch}/>
			{VAR:format_daytext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][day]" value="day_textbox"{VAR:format_daytext_ch}/>
			{VAR:format_daynone_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][day]" value=""{VAR:format_daynone_ch}/><br/>
			{VAR:format_monthselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][month]" value="month"{VAR:format_monthselect_ch}/>
			{VAR:format_monthtext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][month]" value="month_textbox"{VAR:format_monthtext_ch}/>
			{VAR:format_monthnone_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][month]" value=""{VAR:format_monthnone_ch}/><br/>
			{VAR:format_yearselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][year]" value="year"{VAR:format_yearselect_ch}/>
			{VAR:format_yeartext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][year]" value="year_textbox"{VAR:format_yeartext_ch}/>
			{VAR:format_yearnone_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][year]" value=""{VAR:format_yearnone_ch}/><br/>
			{VAR:format_hourselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][hour]" value="hour"{VAR:format_hourselect_ch}/>
			{VAR:format_hourtext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][hour]" value="hour_textbox"{VAR:format_hourtext_ch}/>
			{VAR:format_hournone_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][hour]" value=""{VAR:format_hournone_ch}/><br/>
			{VAR:format_minuteselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][minute]" value="minute"{VAR:format_minuteselect_ch}/>
			{VAR:format_minutetext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][minute]" value="minute_textbox"{VAR:format_minutetext_ch}/>
			{VAR:format_minutenone_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][format][minute]" value=""{VAR:format_minutenone_ch}/><br/>
<!-- END SUB: date_select_options -->

<!-- SUB: datetime_select_options -->
			{VAR:dayselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][day]" value="select"{VAR:dayselect_ch}/>
			{VAR:daytext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][day]" value="text"{VAR:daytext_ch}/><br/>
			{VAR:monthselect_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][month]" value="select"{VAR:monthselect_ch}/>
			{VAR:monthtext_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][month]" value="text"{VAR:monthtext_ch}/><br/>
<!-- END SUB: datetime_select_options -->

<!-- SUB: chooser_options -->
			{VAR:orienth_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][orient]" value="horizontal"{VAR:orienth_ch}/>
			{VAR:orientv_caption} <input type="radio" name="prpconfig[{VAR:prp_key}][orient]" value="vertical"{VAR:orientv_ch}/><br/>
<!-- END SUB: chooser_options -->

<!-- SUB: keyword_selector_options -->
			{VAR:no_folder_names_caption} <input type="checkbox" name="prpconfig[{VAR:prp_key}][no_folder_names]" value="1"{VAR:no_folder_names_checked}/><br/>
			{VAR:no_header_caption} <input type="checkbox" name="prpconfig[{VAR:prp_key}][no_header]" value="1"{VAR:no_header_checked}/><br/>
			{VAR:hide_selected_caption} <input type="checkbox" name="prpconfig[{VAR:prp_key}][hide_selected]" value="1"{VAR:hide_selected_checked}/><br/>
			{VAR:keyword_per_row_caption} <input type="text" size="10" name="prpconfig[{VAR:prp_key}][keyword_per_row]" value="{VAR:keyword_per_row}"/>
<!-- END SUB: keyword_selector_options -->

<!-- SUB: multifile_upload_options -->
			{VAR:max_files_caption} <input type="text" size="10" name="prpconfig[{VAR:prp_key}][max_files]" value="{VAR:max_files}"/>
<!-- END SUB: multifile_upload_options -->


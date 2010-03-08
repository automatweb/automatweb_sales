<!-- SUB: ERROR -->
<tr>
	<td colspan="2">
	{VAR:caption}
	</td>
</tr>
<!-- END SUB: ERROR -->

<!-- SUB: TEXTBOX -->
<tr>
	<th><label for="iField11">{VAR:caption}:</label></th>
	<td>
		<input type="text" value="{VAR:value}" name="elem[{VAR:view_no}][{VAR:element}]" onfocus="this.style.background='#fffeed'" onblur="this.style.background='white'" />
	</td>
</tr>
<!-- END SUB: TEXTBOX -->

<!-- SUB: DATE_TEXTBOX -->
<tr>
	<th><label for="iField11">{VAR:caption}:</label></th>
	<td>
		<input type="text" id="{VAR:date_textbox_id}" value="{VAR:value}" name="elem[{VAR:view_no}][{VAR:element}]" onfocus="this.style.background='#fffeed'" onblur="this.style.background='white'"/>
		<a href="javascript:;" tabindex="222"><img border="0" src="{VAR:calendar_icon_url}" id="{VAR:date_textbox_link}" title="Open calendar" alt="Open calendar"/></a>
			<script type="text/javascript">
			Calendar.setup({
			inputField : "{VAR:date_textbox_id}", // id of the input field
			ifFormat : "%d.%m.%Y", // format of the input field
			showsTime : false, // will display a time selector
			button : "{VAR:date_textbox_link}", // trigger for the calendar (button ID)
			singleClick : true, // double-click mode
			step : 1, // show all years in drop-down boxes (instead of every other year as default)
			firstDay : 1
		});
		</script>
	</td>
</tr>
<!-- END SUB: DATE_TEXTBOX -->


<!-- SUB: DATETIME_TEXTBOX -->
<!-- END SUB: DATETIME_TEXTBOX -->


<!-- SUB: CHECKBOX -->
<tr>

	<th><label for="iField31">{VAR:caption}:</label></th>
	<td>
		<input id="{VAR:wid}" onClick="{VAR:onClick}" type="checkbox" {VAR:checked} name="elem[{VAR:view_no}][{VAR:element}]"/>
	</td>
</tr>
<!-- END SUB: CHECKBOX -->


<!-- SUB: TEXTAREA -->
<tr id="{VAR:wid_out}">
	<th><label for="iField32">{VAR:caption}:</label></th>
	<td>
		<textarea id="{VAR:wid}" name="elem[{VAR:view_no}][{VAR:element}]" onfocus="this.style.background='#fffeed'" onblur="this.style.background='white'" >{VAR:value}</textarea>
	</td>
</tr>
<!-- END SUB: TEXTAREA -->


<!-- SUB: RADIO_CHOOSER -->
<!-- END SUB: RADIO_CHOOSER -->


<!-- SUB: MEETING_PATTEN -->
<!-- END SUB: MEETING_PATTEN -->


<!-- SUB: SELECT -->
<tr id="{VAR:wid_out}">
	<th><label>{VAR:caption}:</label></th>
	<td>
		{VAR:pre_element_append}
		<select id="{VAR:wid}" name="elem[{VAR:view_no}][{VAR:element}]" onChange="{VAR:onChange}">
			<!-- SUB: OPTION -->
			<option value='{VAR:value}' {VAR:selected}>{VAR:caption}</option>
			<!-- END SUB: OPTION -->
		</select>
		{VAR:post_element_append}
	</td>
</tr>
<!-- END SUB: SELECT -->

<!-- SUB: EVENT_TYPE -->
<tr id="{VAR:wid_out}">
	<th valign="top"><label>{VAR:caption}:</label></th>
	<td>
		<input type="radio" name="elem[{VAR:view_no}][{VAR:element}][radio]" value="1" {VAR:radio_1}/><select id="{VAR:wid}" name="elem[{VAR:view_no}][{VAR:element}][select]" onChange="{VAR:onChange}">
			<!-- SUB: EVENT_TYPE_OPTION -->
			<option value='{VAR:value}' {VAR:selected}>{VAR:caption}</option>
			<!-- END SUB: EVENT_TYPE_OPTION -->
		</select>
		<br/>
		<input type="radio" name="elem[{VAR:view_no}][{VAR:element}][radio]" value="2" {VAR:radio_2}/><input name="elem[{VAR:view_no}][{VAR:element}][text]" value="{VAR:text}" type="textbox"/>
	</td>
</tr>
<!-- END SUB: EVENT_TYPE -->

<!-- SUB: TABLE -->
<tr>
	<td colspan="2">
		<table border="1">
			<!-- SUB: HEADER -->
			<tr>
				<!-- SUB: HEADER_COL -->
				<td>
					{VAR:caption}
				</td>
				<!-- END SUB: HEADER_COL -->
			</tr>
			<!-- END SUB: HEADER -->
			<!-- SUB: ROW -->
			<tr>
				<!-- SUB: ROW_COL -->
				<td>
					{VAR:caption}
				</td>
				<!-- END SUB: ROW_COL -->
			</tr>
			<!-- END SUB: ROW -->
		</table>
	</th>
</tr>
<!-- END SUB: TABLE -->


<!-- SUB: TECHNICAL_EQUIPMENT -->
<!-- END SUB: TECHNICAL_EQUIPMENT -->


<!-- SUB: SEARCH_RESULT -->
<!-- END SUB: SEARCH_RESULT -->

<!-- SUB: SEPARATOR -->
<tr class="subheading">
	<th colspan="2">{VAR:caption}</th>
</tr>
<!-- END SUB: SEPARATOR -->

<!-- SUB: TEXT -->
<tr id="{VAR:wid_out}">
	<th><label>{VAR:caption}:</label></th>
	<td style="font-size:11px;font-family:verdana;color:black;">{VAR:value}</td>
</tr>
<!-- END SUB: TEXT -->

<!-- SUB: TEXT_NO_CAPTION -->
<tr id="{VAR:wid_out}">
	<td colspan="2" style="font-size:11px;font-family:verdana;color:black;">{VAR:value}</td>
</tr>
<!-- END SUB: TEXT_NO_CAPTION -->

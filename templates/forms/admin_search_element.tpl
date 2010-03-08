<!-- SUB: SCRIPT -->
<STYLE TYPE="text/css">
			.type { 
				position: relative;};
			.textbox {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.textarea {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.checkbox {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.radiobutton {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.listbox {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.multiple {
				position: absolute;
				top: 0;
				left: 0;
				visibility: hidden;};
			.empty {
				position: absolute;
				top: 0;
				left: 0;
				visibility: visible; };
</STYLE>
<script language="javascript">

<!-- SUB: ELDEFS -->
var form_{VAR:form_id}_el_{VAR:el_el_id}_value = "{VAR:el_value}";
var form_{VAR:form_id}_el_{VAR:el_el_id}_text = "{VAR:el_text}";
var form_{VAR:form_id}_el_{VAR:el_el_id}_type = "{VAR:el_type}";
<!-- END SUB: ELDEFS -->

	function ch_type_var(tp,suf)
	{
		if (document.layers)		// netscape
		{
			document.type.document.textbox.visibility = "hidden";
			document.type.document.textarea.visibility = "hidden";
			document.type.document.checkbox.visibility = "hidden";
			document.type.document.radiobutton.visibility = "hidden";
			document.type.document.listbox.visibility = "hidden";
			document.type.document.multiple.visibility = "hidden";
			document.type.document.empty.visibility = "hidden";
			if (tp == "textbox")
				document.type.document.textbox.visibility = "visible";
			else
			if (tp == "textarea")
				document.type.document.textarea.visibility = "visib;e";
			else
			if (tp == "checkbox")
				document.type.document.checkbox.visibility = "visible";
			else
			if (tp == "radiobutton")
				document.type.document.radiobutton.visibility = "visible";
			else
			if (tp == "listbox")
				document.type.document.listbox.visibility = "visible";
			else
			if (tp == "multiple")
				document.type.document.multiple.visibility = "visible";
			else
				document.type.document.empty.visibility = "visible";
		}
		else										// IE
		{
			eval ('document.all.textbox_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.textarea_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.checkbox_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.radiobutton_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.listbox_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.multiple_'+suf+'.style.visibility = "hidden"');
			eval ('document.all.empty_'+suf+'.style.visibility = "hidden"');
			if (tp == "textbox")
				eval('document.all.textbox_'+suf+'.style.visibility = "visible"');
			else
			if (tp == "textarea")
				eval('document.all.textarea_'+suf+'.style.visibility = "visible"');
			else
			if (tp == "checkbox")
				eval('document.all.checkbox_'+suf+'.style.visibility = "visible"');
			else
			if (tp == "radiobutton")
				eval('document.all.radiobutton_'+suf+'.style.visibility = "visible"');
			else
			if (tp == "listbox")
				eval('document.all.listbox_'+suf+'.style.visibility = "visible"');
			else
			if (tp == "multiple")
				eval('document.all.multiple_'+suf+'.style.visibility = "visible"');
			else
				eval('document.all.empty_'+suf+'.style.visibility = "visible"');
		}
	}

	function ch_type(el,f_el,suf)
	{
		var sf = f_el.options[f_el.selectedIndex].value;
		var v = "form_"+sf+"_el_"+el.selectedIndex+"_type";
		var tp=0;
		if (eval("typeof("+v+")") != "undefined")
			if (eval(v).length > 0 )
				eval("tp = "+v);
		ch_type_var(tp,suf);
	}

	function ch(el, f_el,suf)
	{
		var sf = f_el.options[f_el.selectedIndex].value;
		for (i=0; i < el.options.length; i++)
		{
			v = "form_"+sf+"_el_"+i+"_text";
			if (eval("typeof("+v+")") != "undefined")
			{
				eval("el.options[i].text="+v);
			}
			else
			{
				el.options[i].text="";
			}

			v= "form_"+sf+"_el_"+i+"_value";
			if (eval("typeof("+v+")") != "undefined")
				eval("el.options[i].value="+v);	
			else
				el.options[i].value="";
		}
		ch_type(el,f_el,suf);
	}

function search(search_el,lb_el)
{
	val = search_el.value;
	for (i=0; i < lb_el.options.length; i++)
	{
		if (lb_el.options[i].text == val)
		{
			lb_el.selectedIndex=i;
			lb_el.options[i].selected = true;
			break;
		}
	}
}

</script>
<!-- END SUB: SCRIPT -->
<table border=0>
<tr>
<td bgcolor=#BADBAD>
<table border=0 cellspacing=1 cellpadding=2 bgcolor="#CCCCCC">
<tr>
<td class="fcaption">{VAR:LC_FORMS_TEXT}:</td>
<td class="fform"><input type='text' NAME='{VAR:el_id}_text' VALUE='{VAR:el_text}'></td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_ELEMENT_FROM_FORM}:</td>
<td class="fform"><select NAME='{VAR:el_id}_form' onChange="ch(document.f1.{VAR:el_id}_element, this,'{VAR:el_id}')">
<!-- SUB: FORMSEL -->
<option {VAR:sel_form_active} VALUE='{VAR:sel_form_value}'>{VAR:sel_form_name}
<!-- END SUB: FORMSEL -->
</select>
</td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_WHICH_OF_SELECTED_FORM}:</td>
<td class="fform"><input type='text' name='{VAR:el_id}_search' onKeyDown="setTimeout('search(this,document.f1.{VAR:el_id}_element)',10);"><br><select NAME='{VAR:el_id}_element' onChange='ch_type(this,document.f1.{VAR:el_id}_form,"{VAR:el_id}")'>
<!-- SUB: ELSEL -->
<option {VAR:sel_el_active} VALUE='{VAR:sel_el_value}'>{VAR:sel_el_name}
<!-- END SUB: ELSEL -->
</select>
</td>
</tr>
<tr>
<td class="fcaption">{VAR:LC_FORMS_CHOOSEN_ELEMENT_TYPE}:</td>
<td class="fform">
<SPAN ID='type_{VAR:el_id}' CLASS='type'><SPAN ID='textbox_{VAR:el_id}' CLASS='textbox'>{VAR:LC_FORMS_TEXT_BOX}</SPAN>
<SPAN ID='textarea_{VAR:el_id}' CLASS='textarea'>{VAR:LC_FORMS_MULTILINE_TEXT</SPAN>
<SPAN ID='checkbox_{VAR:el_id}' CLASS='checkbox'>Checkbox</SPAN>
<SPAN ID='radiobutton_{VAR:el_id}' CLASS='radiobutton'>Radiobutton</SPAN>
<SPAN ID='listbox_{VAR:el_id}' CLASS='listbox'>Listbox</SPAN>
<SPAN ID='multiple_{VAR:el_id}' CLASS='multiple'>Multiple listbox</SPAN>
<SPAN ID='empty_{VAR:el_id}' CLASS='empty'></SPAN>
</SPAN>&nbsp;</td>
</tr>
<tr>
<td class="fform" colspan=2>{VAR:LC_FORMS_DELETE_THIS_ELEMENT}? <input type='checkbox' name='{VAR:el_id}_del' value='1'>
</tr>
</table>
</td>
</tr>
</table>

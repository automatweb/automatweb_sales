jQuery.aw_releditor = function(arr) {
	var i_releditor_form_index = arr["start_from_index"];
	var i_releditor_edit_index = false; // if being edited
	var i_releditor_edit_index_last_edit = false; // for multiple change button clicks if not saved between
	var is_edit_mode = false; // edit or new data
	var s_alert_on_delete;
	jQuery.get("/orb.aw?class=releditor&action=js_get_delete_confirmation_text", function(data){
		s_alert_on_delete = data;
	});

	jQuery(document).ready(function() {
   		_handle_events();
 	});

	function _handle_events()
	{
		// add/change btn events
		jQuery("input[name="+arr["releditor_name"]+"]").click(function() {
			if (is_edit_mode)
			{
				// change btn name to 'add'
				change_btn_name = jQuery.get("/orb.aw?class=releditor&action=js_get_button_name", function(change_btn_name){
					btn = jQuery("input[name="+arr["releditor_name"]+"]");
					btn.attr("value", change_btn_name);
				});
			}
			_renew_and_save_form();
			return false;
		});

		handle_change_links();
		handle_delete_links();
	}

	/*
		gets data to be edited to form
	*/
	function do_edit()
	{
		data = jQuery("#"+arr["releditor_name"]+"_data").serialize();

		// change button name when editing not adding
		jQuery.get("/orb.aw?class=releditor&action=js_get_button_name&is_edit=1", function(change_btn_name){
			btn = jQuery("input[name="+arr["releditor_name"]+"]");
			btn.attr("value", change_btn_name);
		});

		jQuery.ajax({
			type: "POST",
			url: "/orb.aw?class=releditor&action=js_change_data&releditor_name="+arr["releditor_name"]+"&edit_index="+i_releditor_edit_index+"&main_clid="+arr["main_clid"],
			data: data,
			success: function(msg){
				eval(msg);
				do_edit_fill_form(edit_data)
			}
		});
	}

	/*
		fills the form for editing
	*/
	function do_edit_fill_form(edit_data)
	{
		//name = arr["releditor_name"]+"["+i_releditor_form_index+"]["+key;
		//alert("[name^="+name+"][type!=submit]");
		if (is_edit_mode)
		{
			current_index = i_releditor_edit_index_last_edit;
		}
		else
		{
			current_index = i_releditor_form_index;
		}
		form = jQuery("form [name^="+arr["releditor_name"]+"\["+current_index+"][type!=submit]").not("a");
		form.each(function(){
			jQuery(this).reset({
				skip_select : true
			});
			s_prop_name = _get_prop_name(jQuery(this).attr("name"));
			if (jQuery(this).attr("multiple"))
			{
				jQuery("option", jQuery(this)).each(function(){
					for (key in edit_data[s_prop_name])
					{
						if (jQuery(this).val() == edit_data[s_prop_name][key])
						{
							jQuery(this).attr("selected", true)
						}
					}

				});
			}
			else if (jQuery(this).attr("type") == "checkbox")
			{
				if (edit_data[s_prop_name] == 1)
				{
					this.checked = true;
					jQuery(this).attr("value", 1);
				}
			}
			else if (date_selectbox_name = is_aw_date_selectbox(jQuery(this).attr("name")) )
			{
				jQuery(this).attr("value", edit_data[date_selectbox_name[0]][date_selectbox_name[1]]*1.0);
			}
			else
			{
				jQuery(this).attr("value", edit_data[s_prop_name]);
			}
			jQuery(this).attr("name", arr["releditor_name"]+"["+i_releditor_edit_index+"]"+s_prop_name);
		});
		i_releditor_edit_index_last_edit = i_releditor_edit_index;
		is_edit_mode = true;
	}

	/*
		adds delete events to delete links
	*/
	function handle_delete_links()
	{
		jQuery("a[name^="+arr["releditor_name"]+"_delete_]").click(function() {
			if(confirm(s_alert_on_delete))
			{
				//delete_data = jQuery("input[name^="+arr["releditor_name"]+"_delete_][type=checkbox][checked]").serialize();
				s_form_extension = jQuery("#"+arr["releditor_name"]+"_data").serialize();
				s_form_extension_class_info = jQuery("form [name^="+arr["releditor_name"]+"_reled_data][type=hidden]").serialize();
				data = s_form_extension+"&"+s_form_extension_class_info+"&"+arr["releditor_name"]+"_delete_index="+_get_delete_index(jQuery(this).attr("name"));
				s_form_cfgform = jQuery("form [name^=cfgform][type=hidden]").serialize();
	                        data = s_form_cfgform.length>0 ? data+"&"+s_form_cfgform : data;
				data += "&id="+arr["id"];
				jQuery.ajax({
					type: "POST",
					url: "/orb.aw?class=releditor&action=js_delete_rows",
					data: data,
					success: function(msg){
						jQuery("#releditor_"+arr["releditor_name"]+"_table_wrapper").html(msg);
						handle_change_links();
						handle_delete_links();
					}
				});
			}
			return false;
		});
	}

	function handle_change_links()
	{
		// and add edit btn events
		jQuery("a[name^="+arr["releditor_name"]+"_edit]").click(function() {
			i_releditor_edit_index = _get_form_index_from_edit_button(jQuery(this).attr("name"));
			do_edit();
			return false;
		});
	}

	/*
		send data to be edited
	*/
	function _renew_and_save_form()
	{
		var a_elements = new Array(); //XXX: pole kasutusel?
		if (is_edit_mode)
		{
			tmp_index = i_releditor_form_index;
			i_releditor_form_index = i_releditor_edit_index
			// class info
		}
		form = jQuery("form [name^="+arr["releditor_name"]+"\["+i_releditor_form_index+"][type!=button]").not("a");
		s_form = form.serialize();
		s_form_extension = jQuery("#"+arr["releditor_name"]+"_data").serialize();
		data = s_form_extension.length>0 ? s_form+"&"+s_form_extension : s_form;
		s_form_extension_class_info = jQuery("form [name^="+arr["releditor_name"]+"_reled_data][type=hidden]").serialize();
		s_form_cfgform = jQuery("form [name^=cfgform][type=hidden]").serialize();
		data = s_form_extension_class_info.length>0 ? data+"&"+s_form_extension_class_info : data;
		data = s_form_cfgform.length>0 ? data+"&"+s_form_cfgform : data;
		data += "&id="+arr["id"];
		data += "&use_clid="+arr["use_clid"];
		data += "&start_from_index="+arr["start_from_index"];

		jQuery.ajax({
			type: "POST",
			url: "/orb.aw?class=releditor&action=handle_js_submit",
			data: data,
			success: function(msg){
				try {
					eval(msg);
					if (error)
					{
						jQuery(".jquery_aw_releditor_error").remove();
						_handle_errors(error);
					}
				} catch(e) {
					jQuery(".jquery_aw_releditor_error").remove();
					jQuery("#releditor_"+arr["releditor_name"]+"_table_wrapper").html(msg);
					handle_change_links();
					handle_delete_links();
					location.href="#"+arr["releditor_name"];

					if (is_edit_mode)
					{
						i_releditor_form_index = tmp_index;
					}

					form.each(function()
					{
						s_prop_name = _get_prop_name(jQuery(this).attr("name"));
						if (is_edit_mode)
						{
							next_index = i_releditor_form_index;
						}
						else
						{
							next_index = i_releditor_form_index*1.0+1;
						}
						jQuery(this).attr("name", arr["releditor_name"]+"["+next_index+"]"+s_prop_name);
						jQuery(this).reset();
					});

					if (is_edit_mode)
					{
						is_edit_mode = false;
					}
					else
					{
						i_releditor_form_index++
					}

				}
			}
		});
	}

	function _handle_errors(error)
	{
		for ( key in error )
		{
			jQuery("#"+key).after(" <span class='jquery_aw_releditor_error' style='color: red'>"+error[key]+"</span>");
		}
	}

	/*
		gets last part of name element
	*/
	function _get_delete_index(s_input_name)
	{
		// i don't undrestand why I had to doublescape: \\[
		var re  =  new RegExp(".*_(.*)$", "g").exec(s_input_name);
		return re[1];
	}


	/*
		gets last part of name element
	*/
	function _get_prop_name(s_input_name)
	{
		// i don't undrestand why I had to doublescape: \\[
		var re  =  new RegExp("^.+\\[[0-9]+\\](.*)$", "g").exec(s_input_name);
		return re[1];
	}

	/*
		gets the last part (form index) from releditors edit link name
	*/
	function _get_form_index_from_edit_button(s_name)
	{
		// i don't undrestand why I had to doublescape: \\[
		var re  =  new RegExp("^.*_.*_(.*)$", "g").exec(s_name);
		return re[1];
	}

	/*
		checks if selectbox is part of date: event_time_edit[1][end][day]
	*/
	function is_aw_date_selectbox(s_name)
	{
		out = new Array()
		var re  =  new RegExp("^.+\\[[0-9]+\\](\\[.*?\\])\\[(.*?)\\]$", "g").exec(s_name);
		if (re)
		{
			if (re[2])
			{
				out[0] = re[1];
				out[1] = re[2];
				return out;
			}
		}
		return false;
	}
};

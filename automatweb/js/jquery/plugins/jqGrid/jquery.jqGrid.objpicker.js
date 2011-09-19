$.extend($.fn.fmatter, {
	objpicker: function(value, options, data) {
		var input = value.split("::");
		if (input.length > 1) {
			var id = input[0],
				caption = input.splice(1).join("::");
		} else {
			var id = value,
				caption =  $.jgrid.getAccessor(data, options.colModel.editoptions.data_index);
		}
		return caption + "<input type='hidden' value='" + id + "'>";
	}
});
$.extend($.fn.fmatter.objpicker, {
	unformat: function(text, options, cell) {
		return cell.find("input[type=hidden]").val() + "::" + text;
	}
});

$.extend($.fn.jqGrid, {
	createObjPicker: function (value, options) {
		var input = value.split("::"),
			id = input[0],
			caption = input.splice(1).join("::"),
			name = (options.id + "_objpicker").replace(".", "_"),
			script = '(function(){ if (!$(this).hasClass("objpicker")) { $(this).addClass("objpicker"); new AutoSuggest("' + name + '", {script: "' + options.url + '&", varname: "typed_text", minchars: 2, timeout: 10000, delay: 200, json: true, shownoresults: false, callback: function(obj){ $("#' + name + '__autocompleteTextbox").attr("value", obj.id)}}); } })()';

		var element = $("<span><input type='text' id='" + name + "' name='" + name + "' value='" + caption + "' onFocus='" + script + "' /><input type='hidden' id='" + name + "__autocompleteTextbox' value='" + id + "' /></span>").get(0);

		return element;
	},
	destroyObjPicker: function (element, action, value) {
		if ("get" === action) {
			return element.find("input[type=hidden]").val() + "::" + element.find("input[type=text]").val();
		} else if ("set" === action) {
			return element.find("input[type=hidden]").val();
		}
		return "";
	}
});

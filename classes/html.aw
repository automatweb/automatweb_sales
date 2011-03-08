<?php

class html
{
	/**
	@attrib api=1 params=name

	@param name optional type=string
		selection name
	@param id optional type=string
		element id
	@param options optional type=array
		selection options array(value => text)
	@param disabled_options optional type=array
		array of values disabled
	@param selected optional type=int
		already selected options
	@param onchange optional type=string
		action starts if selection changes
	@param disabled optional type=bool
		if true, selection is disabled
	@param textsize optional type=string
		font size . examples: "10px", "0.7em", "smaller"
	@param width optional type=int
		selectbox size in pixels
	@param tabindex optional type=string
		tab inde
	@param multiple optional type=bool
		if true, multiple selection abled
	@param size optional type=int
		the size of the selection visible
	@param onblur optional type=string
		If set, then onblur=$onblur.
	@param empty_option type=mixed default=null
		Add empty selection as first option to list. Value of this parameter will be set as the value of the empty selection

	@returns string / html select

	@comment creates html select
	**/
	public static function select($args = array())
	{
		$post_append_text = "";
		extract($args);
		$disabled = (!empty($disabled) ? ' disabled="disabled"' : "");
		$sz = $mz = $onc = $cl = $w = $ts = "";
		// things that make one go humm.. -- duke

		// style attributes
		if (!empty($width))
		{
			$w = " width: {$width}px;";
		}

		/*
		if (!empty($post_append_text))
		{
			$post_append_text = "";
		}
		*/

		if (!empty($textsize))
		{
			$ts = " font-size: {$textsize};";
		}

		$style = ($ts or $w) ? " style=\"{$ts}{$w}\"" : "";

		//
		if (empty($selected) && isset($value))
		{
			$selected = $value;
		}

		$ti = isset($tabindex) ? " tabindex=\"{$tabindex}\"" : "";

		if (!empty($size))
		{
			$sz = " size=\"{$size}\" ";
		}

		if (!empty($class))
		{
			$cl = " class=\"{$class}\"";
		}

		if (empty($id))
		{
			$id = str_replace(array("[", "]"), "_", $name);
		}

		if (!empty($multiple))
		{
			$mz = ' multiple="multiple" ';
			$name .= "[]";
		}

		if (!empty($onchange))
		{
			$onc = " onchange=\"{$onchange}\"";
		}

		$onblur = isset($onblur) ? " onblur=\"{$onblur}\"" : '';

		// build options
		$optstr = "";
		$disabled_options = (!empty($args["disabled_options"]) and is_array($args["disabled_options"])) ? $args["disabled_options"] : array();
		$empty_option = isset($args["empty_option"]) ? self::get_empty_option($args["empty_option"]) : array();

		if (isset($selected) && is_array($selected))
		{
			$sel_array = $selected;
		}
		elseif (isset($selected) && is_scalar($selected) && $selected !== false)
		{
			$sel_array = array($selected);
		}
		else
		{
			$sel_array = array();
		}

		$sel_array = array_flip($sel_array);

		$options = isset($options) ? $empty_option + safe_array($options) : $empty_option;
		foreach($options as $k => $v)
		{
			$selected = isset($sel_array[$k]) ? ' selected="selected"' : "";
			$d = in_array($k, $disabled_options) ? " disabled=\"disabled\"" : "";
			$optstr .= "<option{$selected} value=\"{$k}\"{$d}>{$v}</option>\n";
		}

		// implementing a thing called optgroup -- ahz
		if (!empty($optgroup) and is_array($optgroup))
		{
			foreach($optgroup as $key => $val)
			{
				$optstr .= "<optgroup label=\"{$optgnames[$key]}\">\n";
				foreach(safe_array($val) as $key2 => $val2)
				{
					$selected = isset($sel_array[$key2]) ? " selected=\"selected\" " : "";
					$optstr .= "<option{$selected} value=\"{$key2}\">{$val2}</option>\n";
				}
				$optstr .= "</optgroup>\n";
			}
		}
		/*
		if (!empty($post_append_text))
		{
			$post_append_text = "";
		}
		*/

		//
		return "<select name=\"{$name}\" id=\"{$id}\"{$cl}{$sz}{$mz}{$onc}{$disabled}{$onblur}{$ti}{$style}>\n{$optstr}</select>{$post_append_text}\n";
	}

	/** Returns empty option array (with one element) to be prepended to select element option arrays
		@attrib api=1 params=pos
		@param value type=mixed default=""
			value to be set as the empty/no selection value
		@comment
		@returns array
		@errors
	**/
	public static function get_empty_option($value = "")
	{
		return array($value => t("-- vali --"));
	}

	/**
	@attrib api=1 params=name

	@param name optional type=string
		textbox name
	@param value optional type=string
		textbox value
	@param content optional type=string
		text visible to user when $option_is_tuple is set to TRUE.
	@param size optional type=int
		textbox size
	@param maxlength optional type=int
		maximum lenght
	@param disabled optional type=bool
		if true, textbox is disabled
	@param textsize optional type=string
		font size . examples: "10px", "0.7em", "smaller"

	@param onkeypress optional type=string
		If set, then onkeypress=$onkeypress. Not allowed if autocomplete used.
	@param onFocus optional type=string
		If set, then onFocus=$onFocus.
	@param onblur optional type=string
		If set, then onblur=$onblur.

	@param autocomplete_source optional type=string
		Relative (to web root -- it seems that certain browsers don't allow javascript http connections to absolute paths) http URL that refers to source of autocomplete options. Response expected in JSON format (http://www.json.org/)(classes/protocols/data/aw_json). Response is an array:
		array(
			"error" => boolean,// recommended
			"errorstring" => error string description,// optional
			"options" => array(value1 => text1, ...),// required
			"limited" => boolean,// whether option count limiting applied or not. applicable only for real time autocomplete.
		)

	@param autocomplete_source_method optional type=string
		Alternative to $autocomplete_source parameter. AW ORB method to be called to get options.

	@param autocomplete_source_class optional type=string
		AW class to look for autocomplete_source_method. default is the class that requests the textbox.

	@param autocomplete_params optional type=array
		Array of form element names whose values will be arguments to autocomplete_source. If self form element name included, real time autocomplete options retrieving enabled (i.e. for each key typed, if not in cache).

	@param autocomplete_limit optional type=int
		Number of options autocomplete can show (-1: no limit). Default 20.

	@param autocomplete_match_anywhere optional type=bool
		Should the auto complete match input with options from first char or anywhere in an option? Default FALSE.

	@param autocomplete_delimiters optional type=array
		Delimiter strings for multiple part autocomplete (autocomplete options given again after typing text after a delimiter string). Default empty.

	@param autocomplete_class_id optional type=array
		Classes to search with autocomlete. If set, no other autocomplete params needed

	@param ac_filter optional type=array
		Extra params for autocomplete search filter (array("parent" => 123))

	@param options optional type=array
		Initial autocomplete options. If $option_is_tuple then associative. Default empty.

	@param content optional type=string
		Text visible to user when $option_is_tuple is set to TRUE.

	@param selected optional type=array
		Initially selected option (or options when multiple part autocomplete) if $option_is_tuple is set to TRUE. Array($selected_key => $value, $selected_key2 => ...);

	@param option_is_tuple optional type=bool
		Indicates whether autocomplete options are values (FALSE) or names associated with values (TRUE) iow autocomplete options are key/value pairs. If set to TRUE, $content should be set to what the user will see in the textbox. If set to TRUE then the value returned by POST request under property name is $key if an autocomplete option was selected, $value if new value was entered. Note that user may type an option without selecting it from autocomplete list in which case posted value will not be $key.

	@param tabindex optional type=string
		tab index

	@param onkeyup optional type=string
		if set, onkeyup=$onkeyup.

	@param onload optional type=string
		if set, onload=$onload.

	@param class optional type=string
		textbox's css class

	@returns string / html textbox

	@comment creates html textbox
	**/

	public static function textbox($args = array())
	{
		extract($args);
		$disabled = (!empty($disabled) ? ' disabled="disabled"' : "");
		$post_append_text = (!empty($post_append_text) ? $post_append_text : "");
		$textsize = (!empty($textsize) ? " style=\"font-size: {$textsize};\"" : "");
		$size = isset($size) ? $size : 40;
		$maxlength = isset($maxlength) ? " maxlength=\"{$maxlength}\"" : "";
		$id = str_replace(array("[", "]"),"_",$name);
		$value = isset($value) ? $value : "";
		$value = str_replace('"' , '&quot;',$value);
		settype ($option_is_tuple, "boolean");
		$onkeypress = isset($onkeypress) ? " onkeypress=\"{$onkeypress}\"" : "";
		$onload = isset($onload) ? " onload=\"{$onload}\"" : '';
		$onFocus = isset($onFocus) ? " onfocus=\"{$onFocus}\"" : '';
		$onblur = isset($onblur) ? " onblur=\"{$onblur}\"" : '';
		$ti = isset($tabindex) ? " tabindex=\"{$tabindex}\"" : '';
		$autocomplete = "";
		$js_name = str_replace(array("[", "]", "-"), "_", $name);
		$onchange = !empty($onChange) ? " onchange=\"{$onChange}\"" : "";
		$onkeyup = isset($onkeyup) ? " onkeyup=\"{$onkeyup}\"" : '';
		$style = isset($style) ? " style=\"{$style}\"":"";
		$class = isset($class) ? " class=\"{$class}\"":"";

		if(is_admin() && !empty($autocomplete_class_id))
		{
			$params = array(
				"id" => automatweb::$request->arg("id"),
				"class_ids" => $autocomplete_class_id,
				"param" => $name,
			);

			if(is_array($ac_filter))
			{
				$params += $ac_filter;
			}

			$class = automatweb::$request->class_name();
			$core = new core();
			$autocomplete_source = $core->mk_my_orb("object_name_autocomplete_source", $params, $class , false, true);
			$autocomplete_params = array($name);
		}

		### compose autocompletes source url
		if (is_admin() && (!empty($autocomplete_source) or !empty($options) and is_array($options) or !empty($autocomplete_source_method)))
		{
			if (!defined("AW_AUTOCOMPLETE_INITIALIZED"))
			{
				/*$baseurl = aw_ini_get("baseurl") . "/automatweb/js/";
				$autocomplete = '<script type="text/javascript" src="' . $baseurl . 'autocomplete_lib.js"></script><script type="text/javascript" src="' . $baseurl . 'autocomplete.js"></script>';*/
				load_javascript("autocomplete.js");
				load_javascript("autocomplete_lib.js");
				define("AW_AUTOCOMPLETE_INITIALIZED", 1);
			}

			$autocomplete .= '<script type="text/javascript">';

			if (!empty($option_is_tuple))
			{
				$autocomplete .= "var awAc_{$js_name} = new awActb(document.getElementsByName('{$name}_awAutoCompleteTextbox')[0], document.getElementsByName('{$name}')[0]);\n";
			}
			else
			{
				$autocomplete .= "var awAc_{$js_name} = new awActb(document.getElementsByName('{$name}')[0]);\n";
			}

			if (isset($options) && is_array($options))
			{
				$autocomplete .= "var awAc_{$js_name}Opts = new Array();\n";

				foreach ($options as $key => $value)
				{
					$autocomplete .= "awAc_{$js_name}Opts['{$key}'] = '" . str_replace("'", "\\'", $value) . "';\n";
				}

				$autocomplete .= "awAc_{$js_name}.actb_setOptions(awAc_{$js_name}Opts);\n";
			}
			else
			{
				if (!empty($autocomplete_source_method))
				{
					$autocomplete_source_class = $autocomplete_source_class ? $autocomplete_source_class : $_GET["class"];
					$params = array(
						"id" => automatweb::$request->arg("id"),
					);
					$core = new core();
					$autocomplete_source = $core->mk_my_orb($autocomplete_source_method, $params, $autocomplete_source_class, false, true);
					$autocomplete_source = parse_url ($autocomplete_source);
					$autocomplete_source = $autocomplete_source["path"] . "?" . $autocomplete_source["query"];
				}

				$autocomplete .= "awAc_{$js_name}.actb_setOptionUrl('{$autocomplete_source}');\n";
				$autocomplete .= "awAc_{$js_name}.actb_setParams(" . (count ($autocomplete_params) ? "new Array ('" . implode ("','", $autocomplete_params) . "')" : "new Array ()") . ");\n";
			}

			if (!empty($textsize))
			{
				$autocomplete .= "awAc_{$js_name}.actb_fontSize = '{$textsize}';\n";
			}

			if (!empty($autocomplete_limit))
			{
				$autocomplete .= "awAc_{$js_name}.actb_lim = {$autocomplete_limit};\n";
			}

			if (!empty($autocomplete_match_anywhere))
			{
				$autocomplete .= "awAc_{$js_name}.actb_firstText = false;\n";
			}

			if (isset($autocomplete_delimiters) && is_array($autocomplete_delimiters) and count($autocomplete_delimiters))
			{
				$autocomplete .= "awAc_{$js_name}.actb_delimiter = new Array ('" . implode ("','", $autocomplete_delimiters) . "');\n";
			}

			$autocomplete .= '</script>';
		}

		$value_elem = "";
		$ac_off = "";

		if ($autocomplete)
		{
			$onkeypress = "";
			$ac_off = " autocomplete=\"off\"";
		}

		if ($option_is_tuple)
		{
			$hidden_value = $value;

			if (isset($selected) && is_array($selected))
			{
				$content = "";

				if (isset($autocomplete_delimiters) && is_array($autocomplete_delimiters))
				{
					$delimiter = reset($autocomplete_delimiters);
					$content = implode($delimiter, $selected);
					$hidden_value = implode($delimiter, array_keys($selected));
				}
				else
				{
					$content = reset($selected);
					$hidden_value = key($selected);
				}
			}

			$onkeyup = $autocomplete ? $onkeyup : "onkeyup=\"$('#{$id}').val(this.value); ".$onkeyup."\"";

			$value_elem = "<input type=\"hidden\" id=\"{$id}\" name=\"{$name}\" value=\"{$hidden_value}\">\n";
			$id .= "AWAutoCompleteTextbox";
			$name .= "_awAutoCompleteTextbox";
			$value = isset($content) ? $content : "";
		}

		return "<input type=\"text\" id=\"{$id}\" name=\"{$name}\" size=\"{$size}\" value=\"{$value}\"{$maxlength}{$style}{$onkeypress}{$onkeyup}{$onload}{$onFocus}{$onblur}{$disabled}{$textsize}{$ti}{$ac_off}{$onchange}{$class} />{$post_append_text}\n{$value_elem}{$autocomplete}";
	}

	/**
	@attrib api=1 params=name

	@param name optional type=string
		textarea name
	@param value optional type=string
		textarea value
	@param cols optional type=int default=60
		number of columns
	@param rows optional type=int default=40
		number of rows
	@param disabled optional type=bool
		if true, textarea is disabled
	@param textsize optional type=string
		font size . examples: "10px", "0.7em", "smaller"
	@param maxlength optional type=int
		maximum lenght
	@param style optional type=string
		textarea style
	@param onFocus optional type=string
		if set, onFocus=$onFocus.
	@param onblur optional type=string
		if set, onblur=$onblur.
	@param onkeyup optional type=string
		if set, onkeyup=$onkeyup.
	@param onchange optional type=string
		if set, onchange=$onchange.

	@returns string / html textarea

	@comment creates html textarea
	**/
	public static function textarea($args = array())
	{
		extract($args);

		if(empty($richtext) && isset($maxlength) && is_numeric($maxlength) && $maxlength > 0)
		{
			$onkeyup = isset($onkeyup) ? " ".$onkeyup : "";
			$onkeyup = "if(this.value.length>{$maxlength}){this.value=this.value.substr(0,{$maxlength});}".$onkeyup;
		}

		$cols = !empty($cols) ? $cols : 60;
		$rows = !empty($rows) ? $rows : 40;
		$value = isset($value) ? $value : "";
		$onFocus = isset($onFocus) ? " onfocus=\"{$onFocus}\"" : "";
		$onblur = isset($onblur) ? " onblur=\"{$onblur}\"" : "";
		$onkeyup = isset($onkeyup) ? " onkeyup=\"{$onkeyup}\"" : "";
		$onchange = !empty($onchange) ? " onchange=\"{$onchange}\"" : "";

		if (strpos($value, "<") !== false)
		{
			$value = htmlspecialchars($value);
		}

		$textsize = (empty($textsize) ? "" : " style=\"font-size: {$textsize};\"");
		$id = str_replace(array("[", "]"), "_", $name);
		// now, the browser detection is best done in javascript

		if (!empty($richtext))
		{
			if($rte_type == 2)
			{
				$retval .= "<textarea id=\"{$id}\" name=\"{$name}\" cols=\"{$cols}\" rows=\"{$rows}\"{$textsize}{$onkeyup}{$onchange}>{$value}</textarea>\n";
			}
			elseif($rte_type == 3)
			{
				$hval = $value;
				if(strpos($value, "\"") !== false)
				{
					$hval = htmlspecialchars($hval);
				}
				$retval = "<textarea class=\"codepress php\" id=\"{$id}\" name=\"{$name}\" cols=\"{$cols}\" rows=\"{$rows}\" wrap=\"off\" >{$value}</textarea>
				<br />
				<input type=\"checkbox\" onClick=\"{$name}.toggleAutoComplete();\" checked /> AutoComplete\n";
			}
			else
			{
				$args["type"] = "richtext";
				$args["width"] = $cols;
				$args["height"] = $rows;
				$args["value"] = str_replace("\"" , "&quot;",$args["value"]); //"
				$rte = get_instance("vcl/rte");
				$retval = $rte->draw_editor($args);
			}
		}
		else
		{
			$disabled = (empty($disabled) ? "" : ' disabled="disabled"');
			$retval = "<textarea ".(empty($style) ? "" : "style=\"".$style."\"")." id=\"{$id}\" name=\"{$name}\" cols=\"{$cols}\" rows=\"{$rows}\"{$disabled}{$textsize}{$onkeyup}{$onFocus}{$onblur}{$onchange}>{$value}</textarea>\n";
		}

		return $retval;
	}

	/**
	@attrib api=1 params=name

	@param caption optional type=string
		legend
	@param content optional type=string
		html content
	@returns string

	@comment draws nice border around html content and put cute label on it, not all browsers support this
	**/
	public static function fieldset($args = array())
	{
		extract($args);
		$caption = isset($caption) ? '<legend>'.$caption.'</legend>' : '';
		return '<fieldset>'.$caption.$content.'</fieldset>';
	}

	/**
	@attrib api=1 params=name

	@param name optional type=string
		iframe name
	@param width optional type=integer default=300
		iframe width
	@param height optional type=integer default=200
		iframe height
	@param src optional type=string
		url

	@returns string/html iframe

	@comment draws html iframe
	**/
	public static function iframe($args = array())
	{
		extract($args);
		$width = isset($width) ? $width : '300';
		$height = isset($height) ? $height : '200';
		return "<iframe src=\"{$src}\" name=\"{$name}\" width=\"{$width}\" height=\"{$height}\"></iframe>\n";
	}

	/**
	@attrib api=1 params=name

	@param name optional type=string
		password input name
	@param value optional type=string
		password input value
	@param size optional type=int
		number of html password input
	@param textsize optional type=string
		font size . examples: "10px", "0.7em", "smaller"

	@returns string /html password input

	@comment creates html password input
	**/
	public static function password($args = array())
	{
		extract($args);
		$textsize = !empty($textsize) ? " style=\"font-size: {$textsize};\"" : "";
		$size = isset($size) ? $size : "40";
		$maxlength = isset($maxlength) ? " maxlength=\"{$maxlength}\"" : "";
		$value = isset($value) ? $value : '';
		return "<input type=\"password\" id=\"{$name}\" name=\"{$name}\" size=\"{$size}\" value=\"{$value}\"{$maxlength}{$textsize} />\n";
	}

	/**Simple text
	@attrib api=1 params=name

	@param value required type=string
		text
	@param textsize optional type=string
		text size - examples: "10px", "0.7em", "smaller".

	@param class optional type=string
		text element container dom class

	@returns string/html text

	@comment draws simple html text with given textsize
	**/
	public static function text($args = array())
	{
		$style = empty($args["textsize"]) ? "" : " style=\"font-size: {$args["textsize"]};\"";
		$class = empty($args["class"]) ? "" : " class=\"{$args["class"]}\"";
		$value = isset($args["value"]) ? $args["value"] : "";
		$element = "<span{$class}{$style}>{$value}</span>";
		return $element;
	}

	/**Hidden field
	@attrib api=1 params=name

	@param name optional type=string
		hidden field name
	@param value optional type=string
		hidden field value
	@returns string/html Hidden field
	**/
	public static function hidden($args = array())
	{
		extract($args);
		$value = isset($value) ? str_replace('"', '\"', $value) : '';
		$id = str_replace(array("[", "]"), "_", $name);
		return "<input type=\"hidden\" id=\"{$id}\" name=\"{$name}\" value=\"{$value}\" />\n";
	}

	/**File upload
	@attrib api=1 params=name

	@param name optional type=string
		fileupload name
	@param textsize optional type=string
		examples: "10px", "0.7em", "smaller".
	@param disabled optional type=bool
		if set, fileupload is disabled
	@param value optional type=string
		if set, then all that stuff appears last row before the file upload
	@returns strng/html fileupload
	**/
	public static function fileupload($args = array())
	{
		extract($args);
		$textsize = isset($textsize) && $textsize ? " style=\"font-size: {$textsize};\"" : "";
		$disabled = (!empty($disabled) ? ' disabled="disabled"' : "");
		$rv = "";
		if (!empty($value))
		{
			$rv = $value . "<br />";
		}
		return $rv . "<input type=\"file\" id=\"{$name}\" name=\"{$name}\"{$disabled}{$textsize} />\n";
	}

	/**Checkbox
	@attrib api=1 params=name

	@param name optional type=string
		Checkbox name
	@param value optional type=string
		Checkbox value
	@param checked optional type=bool
		If set, the checkbox is checked
	@param disabled optional type=bool
		If set, the checkbox is disabled
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller"
	@param label optional type=string
		Checkbox label
	@param caption optional type=string
		Checkbox caption
	@param onclick optional type=string
		stuff what will happen if you click on checkbox - javascript
	@param title optional
		title
	@param nbsp optional type=bool default=false
		If set, the caption is separated from checbox with non-breaking spaces.
	@param span optional type=bool

	@returns string/html checkbox
	**/
	public static function checkbox($args = array())
	{
		extract($args);
		$post_append_text = (!empty($post_append_text) ? $post_append_text : "");
		$checked = isset($checked) ? checked($checked) : '';
		$disabled = (!empty($disabled) ? ' disabled="disabled"' : "");
		$span = !empty($span) ? "<span>" : "";
		$span_ = !empty($span) ? "</span>" : "";
		$capt = $onc = $title = '';
		$nbsp = isset($args["nbsp"]) ? (bool) $args["nbsp"] : false;

		if (empty($value))
		{
			$value = 1;
		}

		if (isset($label))
		{
			$capt .= $label;
		}

		if(empty($caption) && !empty($orig_caption))
		{
			$caption = $orig_caption;
		}

		if (isset($caption))
		{
			$capt .= (isset($nbsp) && $nbsp ? "&nbsp;" : " ") . $caption;
		}

		if (!empty($textsize) and !empty($capt))
		{
			$capt = '<span style="font-size: ' . $textsize . ';">' . $capt . '</span>';
		}

		if(isset($title))
		{
			$title = " title=\"{$title}\"";
		}

		if (isset($onclick))
		{
			$onclick = str_replace("\"", "\\\"", $onclick);
			$onc = " onclick=\"{$onclick}\"";
		}

		$id = str_replace(array("[", "]"), "_", $name);

		$onblur = isset($onblur) ? " onblur=\"{$onblur}\"" : '';


		//$tpl = get_instance("cfg/htmlclient");//ma ei tea, yle 1.5 sekundi v6idab m6nest vaatest selle v6lja kommenteerimisega n2iteks
		//$tpl->read_template("default.tpl");
		if(false and $tpl->is_template("CHECKBOX"))
		{
			$tpl->vars(array(
				"name" => $name,
				"value" => $value,
				"onblur" => $onblur,
				"onclick" => $onc,
				"checked" => $checked,
				"disabled" => $disabled,
				"caption" => $capt,
				"post_append_text" => $post_append_text
			));
			$rv = $tpl->parse("CHECKBOX");
		}
		else
		{
			$rv = "$span<input class=\"checkbox\" type=\"checkbox\" id=\"{$id}\" name=\"{$name}\" value=\"{$value}\"{$onblur}{$title}{$onc}{$checked}{$disabled} />{$capt}{$span_}{$post_append_text}\n";
		}
		return $rv;
	}

	/**Radiobutton
	@attrib api=1 params=name

	@param name optional type=string
		button's name
	@param value optional type=string
		button's value
	@param id optional type=string
		field id
	@param checked optional type=bool
		If set, the radiobutton is checked
	@param disabled optional type=bool
		If set, the radiobutton is disabled
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller"
	@param caption optional type=string
		button's caption
	@param onclick optional type=string
		stuff what will happen if you click the radiobutton - javascript
	@returns string/html radiobutton
	**/
	public static function radiobutton($args = array())
	{
		extract($args);
		$checked = isset($checked) ? checked($checked) : '';
		$disabled = (!empty($disabled) ? ' disabled="disabled"' : "");
		$onc = empty($onclick) ? "" : " onclick=\"{$onclick}\"";

		if(!isset($caption))
		{
			$caption = "";
		}

		if (!empty($textsize) && !empty($caption))
		{
			$caption = '<span style="font-size: ' . $textsize . ';">' . $caption . '</span>';
		}

		if(!isset($id) || !$id)
		{
			$id = $name."_".$value;
		}

		/*$tpl = get_instance("htmlclient");
		$tpl->read_template("default.tpl");
		if($tpl->is_template("RADIOBUTTON"))
		{
			$tpl->vars(array(
				"name" => $name,
				"id" => $id,
				"value" => $value,
				"onclick" => $onc,
				"checked" => $checked,
				"disabled" => $disabled,
				"caption" => $caption,
			));
			$rv = $tpl->parse("RADIOBUTTON");
		}
		else
		{*/
			$rv = "<input class=\"radiobutton\" type=\"radio\" name=\"{$name}\" id=\"{$id}\" value=\"{$value}\"{$onc}{$checked}{$disabled} />\n {$caption}";
//		}
		return $rv;
	}

	/**Submit button
	@attrib api=1 params=name

	@param name optional type=string
		button name
	@param value optional type=string
		button value
	@param class optional type=string
		style class name
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller"
	@param onclick optional type=string
		stuff what will happen if you click the button - javascript
	@returns string/html submit button
	**/
	public static function submit($args = array())
	{
		$name = isset($args["name"]) ? $args["name"] : "";
		$value = isset($args["value"]) ? $args["value"] : "";
		$textsize = isset($args["textsize"]) && !empty($args["textsize"]) ? " style=\"font-size: ".$args["textsize"].";\"" : "";
		$class = isset($args["class"]) && !empty($args["class"]) ? " class=\"".$args["class"]."\"" : "";
		$onclick = isset($args["onclick"]) && !empty($args["onclick"]) ? " onclick=\"".$args["onclick"]."; return this.value;\"" : "";

		return "<input id=\"cbsubmit\" type=\"submit\" name=\"{$name}\" value='{$value}'{$class}{$onclick}{$textsize} />\n";
	}

	/**Simple button
	@attrib api=1 params=name

	@param name optional type=string
		button name

	@param type optional type=string
		button type

	@param value type=string default=""
		button value

	@param class optional type=string
		style class name

	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller"

	@param disabled optional type=bool
		If set, the button is disabled

	@param onclick optional type=string
		stuff what will happen if you click the button - javascript

	@param id optional type=string
		button id

	@param image type=string default=""
		button image url

	@param post_append_text optional type=string
		any text or html code you want to see after button

	@param reload optional type=array
		array of parameters for reloading properties
		@param reload.submit optional type=string
			@param reload.submit.url optional type=string default=orb.aw
				The URL to post the data to.
			@param reload.submit.forms optional type=array
				The inputs from given forms will be posted (using $("FORM_ID").serialize())
			@param reload.submit.props optional type=array
				The given prioperties will be posted.
		@param reload.layouts optional type=array
		@param reload.props optional type=array
		@param reload.params optional type=array

	@returns string/html submit button/image
	**/
	public static function button($args = array())
	{
		$post_append_text = empty($args["post_append_text"]) ? "" : $args["post_append_text"];
		$disabled = empty($args["disabled"]) ? "" : ' disabled="disabled"';
		$name = empty($args["name"]) ? "" : " name=\"{$args["name"]}\"";
		$value = isset($args["value"]) ? " value='{$args["value"]}'" : "";
		$class = empty($args["class"]) ? "" : " class=\"{$args["class"]}\"";
		$style = empty($args["style"]) ? "" : " style=\"{$args["style"]}\"";
		$id = empty($args["id"]) ? "" : " id=\"{$args["id"]}\"";

		if(!empty($args["image"]))
		{
			$image_url = new aw_uri($args["image"]);
			$type = "image";
			$src = ' src="' . $image_url->get() . '"';
			$textsize = "";
			$alt = empty($args["value"]) ? "" : " alt=\"{$args["value"]}\" title=\"{$args["value"]}\"";
		}
		else
		{
			$src = $alt = $onclick_ret = "";
			$textsize = !empty($textsize) ? " style=\"font-size: {$textsize};\"" : "";
			$type = empty($args["type"]) ? "button" : $args["type"];
		}

		$onclick = empty($args["onclick"]) ? "" : " onclick=\"{$args["onclick"]}\"";

		return "<input type=\"{$type}\"{$name}{$src}{$alt}{$value}{$id}{$onclick}{$class}{$disabled}{$textsize}{$style} />{$post_append_text}\n";
	}

	/**Time selector
	@attrib api=1 params=name

	@param name optional type=string
		Time selector name
	@param minute_step optional type=int default=1
		Time selector minute step
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller"
	@param disabled optional type=bool
		If set, the time selector is disabled
	@param value optional type=array
		array("hour" - the number of the hour, "minute" - the number of the minute)
	@returns string/html time selector

	@comments
		draws several selectboxes , can be used for selecting time
	**/
	public static function time_select($args = array())
	{
		load_vcl("date_edit");
		$selector = new date_edit($args["name"]);
		$selector->set("minute_step", (empty($args["minute_step"]) ? 1 : $args["minute_step"]));
		$selector->configure(array("hour" => 1, "minute" => 1));
		list($d,$m,$y, $h, $i) = explode("-", date("d-m-Y-H-i"));
		$h = isset($args["value"]["hour"]) ? (int) $args["value"]["hour"] : $h;
		$i = isset($args["value"]["minute"]) ? (int) $args["value"]["minute"] : $i;
		$val = mktime($h, $i, 0, $m, $d, $y);

		if (!empty($args["disabled"]) or !empty($args["textsize"]))
		{
			$name = array ("name" => $args["name"]);

			if (!empty($args["disabled"]))
			{
				$name["disabled"] = true;
			}

			if (!empty($args["textsize"]))
			{
				$name["textsize"] = $args["textsize"];
			}
		}
		else
		{
			$name = $args["name"];
		}
		return $selector->gen_edit_form($name, $val,false,false,false,false,true);
	}

	/**Date & time selector
	@attrib api=1 params=name

	@param name optional type=string
		Datetime selector name
	@param minute_step optional type=int default=1
		Datetime selector minute step
	@param day optional type=string
		if day = "text" then day is shown as textbox, not selectbox
	@param month optional type=string
		if month = "text" then month is shown as textbox, not selectbox
	@param value optional type=array/int/string
		array("hour" - the number of the hour, "minute" - the number of the minute, "month" - the number of the month, "year"  - The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069 and 70-100 to 1970-2000. On systems where time_t is a 32bit signed integer, as most common today, the valid range for year is somewhere between 1901 and 2038)
		if not an array , value should be Unix timestamp or if value = "+24h" or "+48h", 24 or 48 hours will be add to timestamp
	@param disabled optional type=bool
		If set, the datetime selector is disabled
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller". If set, the datetime selector is disabled
	@param year_from optional type=int default=current year - 5
		the number where year counting starts
	@param year_to optional type=int default=current year + 5
		the number where year counting ends

	@returns string/html datetime selector

	@comments
		draws several selectboxes (with textboxes) , can be used for selecting time and date
	**/
	public static function datetime_select($args = array())
	{
		$selector = new date_edit($args["name"]);
		$selector->set("minute_step", (isset($args["minute_step"]) ? $args["minute_step"] : 1));
		$set = array();
		if (!empty($args["day"]) && $args["day"] === "text")
		{
			$set["day_textbox"] = 1;
		}
		else
		{
			$set["day"] = 1;
		}

		if (!empty($args["month"]) && $args["month"] === "text")
		{
			$set["month_textbox"] = 1;
		}
		else
		{
			$set["month"] = 1;
		}

		if (!empty($args["year"]) && $args["year"] === "text")
		{
			$set["year_textbox"] = 1;
		}
		else
		{
			$set["year"] = 1;
		};
		$set["hour"] = 1;
		$set["minute"] = 1;

		$selector->configure($set);
		if (isset($args['value']))
		{
			if (is_array($args['value']))
			{
				$val = mktime((int)$args["value"]["hour"], (int)$args["value"]["minute"], 0, (int)$args["value"]["month"], (int)$args["value"]["day"], (int)$args["value"]["year"]);
			}
			else
			{
				$val = (int) $args['value'];
			}
		}
		else
		{
			$val = 0;
		}

		if (isset($args["disabled"]) or isset($args["textsize"]))
		{
			$name = array ("name" => $args["name"]);

			if ($args["disabled"])
			{
				$name["disabled"] = true;
			}

			if ($args["textsize"])
			{
				$name["textsize"] = $args["textsize"];
			}
		}
		else
		{
			$name = $args["name"];
		}

		$year_from = isset($args["year_from"]) ? $args["year_from"] : date("Y") - 5;
		$year_to = isset($args["year_to"]) ? $args["year_to"] : date("Y") + 5;

		$add_empty = (isset($args["add_empty"]) && in_array($args["add_empty"], array("false", "no", "0"))) ? false : true;

		return $selector->gen_edit_form($name, $val, $year_from, $year_to, $add_empty);
	}

	/**Date selector
	@attrib api=1 params=name

	@param name optional type=string
		Date selector name
	@param format optional type=array
		Via this you can configure, which parts of dateselect will be shown and how it is drawn
		Possible array elements are:
			day, month, year, hour, minute (displayed as selects)
			day_textbox, month_textbox, year_textbox, hour_textbox, minute_textbox (displayed as textbox)
	@param mon_for optional type=string
		if set, 0 appears before every month's signifier witch has value < 10
	@param value optional type=array/int/string
		array("day" - the number of the day, "month" - the number of the month, "year"  - The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069 and 70-100 to 1970-2000. On systems where time_t is a 32bit signed integer, as most common today, the valid range for year is somewhere between 1901 and 2038)
		if not an array , value should be Unix timestamp or if value = "+24h" or "+48h", 24 or 48 hours will be add to timestamp
	@param default optional type=int
		if value is not set, timestamp = default // it is meaningless
	@param year_from optional type=int default=current year - 5
		the number where year counting starts
	@param year_to optional type=int default=current year + 5
		the number where year counting ends
	@param post_append_text optional type=string
		any text or html code you want to see after date selector // meaningless
	@param disabled optional type=bool
		If set, the date selector is disabled
	@param textsize optional type=string
		Examples: "10px", "0.7em", "smaller". If set, the datetime selector is disabled
	@param buttons optional type=bool
		Enables the "popup calendar" and "clear date select" buttons to the end of the select.
	@param month optional type=string
		if month = "text" then month is shown as textbox, not selectbox
	@param day optional type=string
		if day = "text" then day is shown as textbox, not selectbox
	@param year optional type=string
		if year = "text" then year is shown as textbox, not selectbox
	@param month_as_numbers type=bool default=false
		if set to true, monthnames are replaced with numbers
	@returns string/html date selector

	@comments
		draws several selectboxes and/or textboxes, can be used for selecting time and date
	**/
	public static function date_select($args = array())
	{
		$selector = new date_edit($args["name"]);

		$set = array();

		if (!empty($args['buttons']))
		{
			$buttons = true;
		}
		elseif (is_admin() and !isset($args['buttons']))
		{
			$buttons = true;
		}
		else
		{
			$buttons = false;
		}

		if (!empty($args["day"]) && $args["day"] === "text")
		{
			$set["day_textbox"] = 1;
		}
		else
		{
			$set["day"] = 1;
		}

		if (!empty($args["month"]) && $args["month"] === "text")
		{
			$set["month_textbox"] = 1;
		}
		else
		{
			$set["month"] = 1;
		};

		if (!empty($args["year"]) && $args["year"] === "text")
		{
			$set["year_textbox"] = 1;
		}
		else
		{
			$set["year"] = 1;
		}

		if (!empty($args["format"]) && is_array($args["format"]))
		{
			$a = array();
			foreach($args["format"] as $fldn)
			{
				$a[$fldn] = 1;
			}
			$selector->configure($a);
		}
		else
		{
			$selector->configure($set);
		}

		if(!empty($args["mon_for"]))
		{
			$selector->set("mon_for", $args["mon_for"]);
		}

		if (isset($args["value"]) and is_array($args["value"]) and is_numeric($args["value"]["month"]) and is_numeric($args["value"]["day"]) and is_numeric($args["value"]["year"]))
		{
			$val = mktime(0, 0, 0, $args["value"]["month"], $args["value"]["day"], $args["value"]["year"]);
		}
		elseif(isset($args["value"]) and is_scalar($args["value"]))
		{
			$val = $args["value"];
		}
		elseif(isset($args["default"]))
		{
			$val = $args["default"];
		}
		else
		{
			$val = time();
		}

		$year_from = isset($args["year_from"]) ? $args["year_from"] : date("Y") - 5;
		$year_to = isset($args["year_to"]) ? $args["year_to"] : date("Y") + 5;

		if (isset($args["disabled"]) && $args["disabled"] or isset($args["textsize"]) && $args["textsize"])
		{
			$name = array ("name" => $args["name"]);

			if (isset($args["disabled"]) && $args["disabled"])
			{
				$name["disabled"] = true;
			}

			if (isset($args["textsize"]) && $args["textsize"])
			{
				$name["textsize"] = $args["textsize"];
			}
		}
		else
		{
			$name = $args["name"];
		}

		$months_as_numbers = isset($args["month_as_numbers"]) && $args["month_as_numbers"] ? true : false;
		$res = $selector->gen_edit_form($name, $val, $year_from, $year_to, true, $buttons, !$buttons, $months_as_numbers);
		if(isset($args["post_append_text"]))
		{
			$res .= $args["post_append_text"];
		}
		return $res;
	}

	/**Image
	@attrib api=1 params=name

	@param url optional type=string
		image url
	@param width optional type=int
		image width
	@param height optional type=int
		image height
	@param border optional type=int
		border size
	@param alt optional type=string
		text you can see when you scroll over the image
	@param title optional type=string
		image title
	@param class optional type=string
		style class name
	@param id optional type=string
		image id

	@returns string/html image

	@comments
		draws html image tag
	**/
	public static function img($args = array())
	{
		$xhtml_slash = "";
		if (aw_ini_get("content.doctype") === "xhtml")
		{
			$xhtml_slash = " /";
		}

		extract($args);
		$ret = "<img src='$url'";
		if (isset($width))
		{
			$ret.=" width='$width'";
		}
		if (isset($height))
		{
			$ret.=" height='$height'";
		}
		if (isset($border))
		{
			$ret.=" border='$border'";
		}
		if(isset($alt))
		{
			$ret.=" alt='$alt'";
		}
		if(isset($title))
		{
			$ret.=" title='$title'";
		}
		if(isset($class))
		{
			$ret.=" class='$class'";
		}
		if(isset($id))
		{
			$ret.=" id='$id'";
		}
		return $ret.$xhtml_slash.">";
	}

	/**Link
	@attrib api=1 params=name

	@param url optional type=string
		link's url. May contain single quotes but not double quotes
	@param style optional type=string
	@param rel optional type=string
	@param onmouseover optional type=string
	@param onmouseout optional type=string
	@param onclick optional type=string
		stuff that will happen , if you press the link - javascript
	@param textsize optional type=string
		examples: "10px", "0.7em", "smaller"
	@param target optional type=string
		frame name where stuff should open
	@param title optional type=string
		you can see this text when scrolling over the link
	@param class optional type=string
		style class name
	@param tabindex optional type=string
		tab index
	@param name optional type=string
		element name
	@param id optional type=string
		element id (for css mainly)
	@param caption optional type=string
		the text user can see
	@param reload optional type=array

	@returns string/html href

	@comments
		draws html href tag
	**/
	public static function href($args = array())
	{
		extract($args);
		if (!isset($onClick) && isset($onclick))
		{
			$onClick = $onclick;
		}
		$textsize = isset($textsize) ? " style=\"font-size: {$textsize};\"" : "";
		$target = isset($target) ? " target=\"{$target}\"" : "";
		$onClick = isset($onClick) ? " onclick='{$onClick}'" : "";
		$title = isset($title) ? str_replace("'", "&#39;", $title) : NULL;	// Cuz alt='$title'
		$title = isset($title) ? " alt='{$title}' title='{$title}'" : "";
		$class = isset($class) ? " class='{$class}'" : "";
		$onMouseOver = isset($onmouseover) ? " onmouseover='{$onmouseover}'":"";
		$onMouseOut = isset($onmouseout) ? " onmouseout='{$onmouseout}'":"";
		$ti = isset($tabindex) ? " tabindex='{$tabindex}'" : "";
		$id = isset($id) ? " id='{$id}'" : "";
		$name = isset($name) ? " name='{$name}'" : "";
		$rel = isset($rel) ? " rel='{$rel}'" : "";
		$url = isset($url) ? $url : "";
		$style = isset($style) ? " style='{$style}'" : "";

		if (!isset($args["caption"]))
		{
			$caption = $url;
		}
		elseif (!strlen($args["caption"]))
		{
			$caption = "<i>" . t("[link]") . "</i>";
		}

		if (empty($url) && !empty($args["reload"]))
		{
			$onclick = self::handle_reload($args["reload"]);
			$url = "javascript:void(0)";
			$onClick = " onclick=\"{$onclick}\"";
		}
		// We use double quotes in HTML, so we need to escape those in the url.
		$url = str_replace("\"", "\\\"", $url);

		return "<a href=\"{$url}\"{$target}{$title}{$onClick}{$onMouseOver}{$onMouseOut}{$ti}{$textsize}{$class}{$id}{$name}{$rel}{$style}>{$caption}</a>";
	}

	/**Popup
	@attrib api=1 params=name

	@param quote optional type=string default = '"'
		Quotation mark
	@param url optional type=string
		A string containing the URL of the document to open in the new window. If no URL is specified, an empty window will be created
	@param target optional type=string
		A string containing the name of the new window. This can be used as the 'target' attribute of a <FORM> or <A> tag to point to the new window.
	@param toolbar optional type=string default=no
		When set to yes the new window will have the standard browser tool bar (Back, Forward, etc.).
	@param directories optional type=string default = no
		When set to yes, the new browser window has the standard directory buttons.
	@param status optional type=string  type=string default = no
		When set to yes, the new window will have the standard browser status bar at the bottom.
	@param location optional type=string  type=string default = no
		When set to yes, this creates the standard Location entry feild in the new browser window.
	@param resizable optional type=string  type=string default = no
		When set to yes this allows the resizing of the new window by the user.
	@param scrollbars  optional type=string default = no
		When set to yes the new window is created with the standard horizontal and vertical scrollbars, where needed
	@param menubar optional  type=string default = no
		When set to yes, this creates a new browser window with the standard menu bar (File, Edit, View, etc.).
	@param height optional type=int default=400
		This sets the height of the new window in pixels.
	@param width optional type=int default=400
		This sets the width of the new window in pixels.
	@param no_link optional type=bool
		If set, returns javascritp text instead of the href html tag
	@returns string/html popup

	@comments
		draws html pupup link href tag or javascript text
	**/
	public static function popup($arr = array())
	{
		extract($arr);

		if (empty($url))
		{
			throw new awex_html_param("url can't be empty");
		}

		$quote = isset($arr["quote"]) ? $arr["quote"] : "\"";
		$arr["onClick"] = 'javascript:window.open('.$quote.''.$url.''.$quote.', '.$quote.''.(isset($target) ? $target : "").
		''.$quote.', '.$quote.'toolbar='.(!empty($toolbar) ? "yes" : "no").
		',directories='.(!empty($directories) ? "yes" : "no").
		',status='.(!empty($status) ? "yes" : "no").
		',location='.(!empty($location) ? "yes" : "no").
		',resizable='.(!empty($resizable) ? "yes" : "no").
		',scrollbars='.(!empty($scrollbars) ? "yes" : "no").
		',menubar='.(!empty($menubar) ? "yes" : "no").
		',height='.(isset($height) ? $height : 400).
		',width='.(isset($width) ? $width : 400).
		''.$quote.');'.(empty($arr["no_return"]) ? 'return false;' : '');
		if(!empty($no_link))
		{
			return $arr["onClick"];
		}
		return html::href($arr);
	}

	/**HTML form
	@attrib api=1 params=name

	@param action optional type=string default = '"'
		form action
	@param method optional type=string
		form method
	@param name optional type=int default=400
		form name
	@param content optional type=bool
		html to insert between form tags
	@returns string/html form
	**/
	public static function form($args = array())
	{
		$action = isset($args["action"]) ? $args["action"] : "";
		$method = isset($args["method"]) ? $args["method"] : "";
		$name = isset($args["name"]) ? $args["name"] : "";
		$content = isset($args["content"]) ? $args["content"] : "";

		return '<form action="'.$action.'" method="'.$method.'" name="'.$name.'">'.$content.'</form>';
	}

	/**Link
	@attrib api=1 params=name

	@param id optional type=string
		id of the span tag
	@param class optional type=string
		style class name
	@param textsize optional type=string
		examples: "10px", "0.7em", "smaller"
	@param fontweight optional type=string
		examples: "bold", "normal"
	@param color optional type=string
		examples: "red", "#CCBBAA"
	@param content optional type=string
		html to insert between span tags
	@param nowrap type=bool default=false
		allow no white space wrapping if TRUE
	@returns string/html

	@comments
		draws <span class='$class'>$content</span>
	**/
	public static function span($args = array())
	{
		extract($args);
		$textsize = (!empty($textsize) ? 'font-size:' . $textsize . ';' : "");
		$fontweight = (!empty($fontweight) ? 'font-weight:' . $fontweight . ';' : "");
		$color = (!empty($color) ? "color: {$color};" : "");
		$nowrap = empty($args["nowrap"]) ? "" : "white-space:nowrap; display: block;";
		$style = (empty($textsize) and empty($fontweight) and empty($color) and empty($nowrap)) ? "" : " style=\"{$textsize}{$fontweight}{$color}{$nowrap}\"";
		$class = (!empty($class) ? ' class="' . $class . '"' : "");
		$id = (!empty($id) ? " id=\"{$id}\"" : "");
		$content = isset($content) ? $content : "";
		return "<span{$class}{$style}{$id}>{$content}</span>";
	}

	/**Link
	@attrib api=1 params=name

	@param class optional type=string
		style class name
	@param textsize optional type=string
		examples: "10px", "0.7em", "smaller"
	@param fontweight optional type=string
		examples: "bold", "normal"
	@param content optional type=string
		html to insert between div tags
	@param id optional type=string
		div id
	@param border optional type=string
	@param padding optional type=string


	@returns string/html

	@comments
		draws <div class='$class'>$content</div>
	**/
	public static function div($args = array())
	{
		extract($args);
		$style_props = array(
			"border" => "border",
			"textsize" => "font-size",
			"fontweight" => "font-weight",
			"padding" => "padding",
		);

		$style = "";
		$styles = array();
		foreach($args as $key => $val)
		{
			if(!empty($style_props[$key]))
			{
				$styles[] = $style_props[$key].": " . $val . ";";

			}
		}

		if(sizeof($styles))
		{
			$style=" style=\" ".join(" " , $styles)."\"";
		}

		$class = (!empty($class) ? ' class="' . $class . '"' : "");
		$id = ($id ? " id=\"{$id}\"" : "");
		$content = isset($content) ? $content : "";
		return "<div{$class}{$style}{$id}>{$content}</div>";
	}

	/**	HTML of line break(s)
		@attrib api=1 params=pos
		@param n optional type=int default=1
			The number of line breaks to be returned.
		@returns string/html
	**/
	public static function linebreak($n = 1)
	{
		return str_repeat("<br />\n", $n);
	}

	/**	HTML of non-breaking space
		@attrib api=1 params=pos
		@param n optional type=int default=1
			The number of space chars to be returned.
		@returns string/html
	**/
	public static function space($n = 1)
	{
		return str_repeat("&nbsp;", $n);
	}

	/**Bold text
	@attrib api=1 params=pos
	@param content required type=string
		html to insert between div tags
	@returns string/html
	@comments
		draws <b>$content</b>
	**/
	public static function bold($content)
	{
		return "<b>{$content}</b>";
	}

	/**	Italic text
	@attrib api=1 params=pos
	@param content required type=string
		html to insert between div tags
	@returns string/html
	@comments
		draws <i>$content</i>
	**/
	public static function italic($content)
	{
		return "<i>{$content}</i>";
	}

	/**
	@attrib api=1 params=pos

	@param o required type=object
		object to be changed
	@param caption optional type=string
		the text user can see,(objects name, or "(nimetu)" if the object has no name) if set, returns html href tags.
	@param prms optional type=array
		parameters to be added in the url
	@returns string/url or string/html href

	@comments
		returns the url where can change the given object in AW
	@example
		$url = html::obj_change_url($object);
	**/
	public static function obj_change_url($o, $caption = NULL, $prms = array())
	{
		if (is_array($o))
		{
			$res = array();
			foreach($o as $id)
			{
				$res[] = html::obj_change_url($id);
			}
			return join(", ", $res);
		}

		if (!is_object($o))
		{
			if (object_loader::can("view", $o))
			{
				$o = obj($o);
			}
			else
			{
				return "";
			}
		}
		$prms = array_merge(array("return_url" => get_ru()), safe_array($prms));
		return html::get_change_url($o->id(), $prms, $caption === null ? parse_obj_name($o->name()) : $caption);
	}

	/**
	@attrib api=1 params=pos

	@param o required type=object
		object to be changed
	@param caption optional type=string
		the text user can see,(objects name, or "(nimetu)" if the object has no name) if set, returns html href tags.
	@returns string/url or string/html href

	@comments
		returns the url where can change the given object in AW
	@example
		$url = html::obj_view_url($object);
	**/
	public static function obj_view_url($o, $caption = NULL)
	{
		if (is_array($o))
		{
			$res = array();
			foreach($o as $id)
			{
				$res[] = html::obj_change_url($id);
			}
			return join(", ", $res);
		}

		if (!is_object($o))
		{
			$inst = new acl_base();
			if ($inst->can("view", $o))
			{
				$o = obj($o);
			}
			else
			{
				return "";
			}
		}
		return html::get_change_url($o->id(), array("action" => "view", "return_url" => get_ru()), $caption === null ? parse_obj_name($o->name()) : $caption);
	}


	/**
	@attrib api=1 params=pos

	@param oid required type=oid
		objects oid witch is going to be changed
	@param params optional type=array
		url parameters: array("parameter name" - "parameter value", ...)
	@param caption optional type=string
		the text user can see, if set, returns html href tags
	@param title optional type=string
		you can see this text when scrolling over the link
	@returns string/url or string/html href

	@comments
		returns the url where can change the given object in AW
	@example
		$url = html::get_change_url($val["oid"], array("return_url" => get_ru()), $val["name"];
	**/
	public static function get_change_url($oid, $params = array(), $caption = false, $title=null)
	{
		static $core;
		if (!$core)
		{
			$core = new core();
		}

		$obj = obj($oid);
		$params["id"] = $obj->id();
		$retval = $core->mk_my_orb("change", $params, $obj->class_id());

		if($caption)
		{
			$retval = html::href(array(
				"url" => $retval,
				"caption" => $caption,
				"title" => $title
			));
		}

		return $retval;
	}

	/**
	@attrib api=1 params=pos

	@param class_id required type=clid
		new object class id
	@param parent optional type=oid
		new object parent oid
	@param caption optional type=bool
		the text user can see, if caption is set, returns html href
	@param params optional type=array
		url parameters: array("parameter name" - "parameter value", ...)
	@returns string/url or string/html href

	@comments
		returns the url where can make a new object with given class_id
	@example
		$url = html::get_change_url($arr["class_id"] , $arr["parent_id"] , array("do" => "die" , "message" => "RIP")));
	**/
	public static function get_new_url($class_id, $parent, $params = array(), $caption = false)
	{
		$params = array("parent" => $parent) + $params;

		if (isset($_GET["section"]) && is_oid($_GET["section"]) and !isset($params["section"]))
		{
			$params["section"] = $_GET["section"];
		}

		$core = new core();
		$retval =  $core->mk_my_orb("new", $params, $class_id);
		if($caption)
		{
			$retval = html::href(array(
				"url" => $retval,
				"caption" => $caption
			));
		}
		return $retval;
	}

	public static function strong($str)
	{
		return "<b>{$str}</b>";
	}

	/**
		@attrib params=name api=1

		@param params optional type=array
			Array of key-value pairs. See the example.
		@param props optional type=array
			An array of property names.
		@param layouts optional type=array
			An array of layout names.
		@param submit optional type=string
			@param submit.url optional type=string default=orb.aw
				The URL to post the data to.
			@param submit.params optional type=string default=orb.aw
				The params to post.
			@param submit.forms optional type=array
				The inputs from given forms will be posted (using $("FORM_ID").serialize())
			@param submit.props optional type=array
				The given prioperties will be posted.
		@returns
			The JS for the onclick attribute of HTML tags.
		@example
			$onclick = html::handle_reload(array(
				"params" => array(
					"foo" => "bar",
					"moo" => "cow",
				),
				"layouts" => array("layout_name_1", "layout_name_2", "layout_name_3"),
				"props" => array("property_name"),
			));
			echo "onc = ".$onclick;
			// onc = reload_property(['property_name'],{'foo':'bar','moo':'cow'});reload_layout(['layout_name_1','layout_name_2','layout_name_3'],{'foo':'bar','moo':'cow'});

			$onclick = html::handle_reload(array(
				"submit" => array(
					"props" => array("moo", "cow"),
					"forms" => array("foo_bar"),
				),
				"layouts" => array("layout_name_1", "layout_name_2", "layout_name_3"),
				"props" => array("property_name"),
			));
			echo "onc = ".$onclick;
			// onc = reload_property(['property_name'],{'foo':'bar','moo':'cow'});reload_layout(['layout_name_1','layout_name_2','layout_name_3'],{'foo':'bar','moo':'cow'});
	**/
	public static function handle_reload($arr)
	{
		load_javascript("reload_properties_layouts.js");
		$onclick = "";

		$reload_params = array();
		if(isset($arr["params"]) and is_array($arr["params"]))
		{
			foreach($arr["params"] as $pkey => $pval)
			{
				$reload_params[] = "'".$pkey."':'".$pval."'";
			}
		}
		$params = "{".implode(",", $reload_params)."}";

		if(!empty($arr["props"]))
		{
			$props = "['".implode("','", (array)$arr["props"])."']";
			$onclick .= "reload_property(".$props.",".$params.");";
		}
		if(!empty($arr["layouts"]))
		{
			$layouts = "['".implode("','", (array)$arr["layouts"])."']";
			$onclick .= "reload_layout(".$layouts.",".$params.");";
		}

		/*
		if(isset($arr["submit"]))
		{
			$post_url = !empty($arr["submit"]["url"]) ? $arr["submit"]["forms"] : "orb.aw";
			$post_params
			if(!empty($arr["submit"]["forms"]))
			{
				$post_params =
			}
			if(!empty($arr["submit"]["props"]))
			{
			}
			$post_params = "{".implode(",", $post_params)."}";
			$onclick = "$.post('".$post_url."}',{".$post_params.",function(){".$onclick."});";
		}
		*/

		return $onclick;
	}

	/** Constructs a javascript type SCRIPT html element
		@attrib api=1 params=pos
		@param script type=string
			Javascript code
		@comment
		@returns
		@errors
	**/
	public static function script($script)
	{
		$r = <<<ENDJAVASCRIPT
<script type="text/javascript">{$script}</script>
ENDJAVASCRIPT;
	}

	/** Escapes a string to be used with single quotes in javascript
		@attrib api=1 params=pos
		@param string type=string
		@comment
		@returns string
		@errors
	**/
	public static function quote_js($string)
	{
		return addcslashes ($string, "'\\");
	}
}

/* Generic html error condition */
class awex_html extends aw_exception {}

/* Method parameter errors */
class awex_html_param extends awex_html {}

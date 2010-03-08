<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_treeview/otv_data_filter.aw,v 1.10 2008/01/31 13:52:37 kristo Exp $
// otv_data_filter.aw - Andmeallika andmete muundaja 
/*

@classinfo syslog_type=ST_OTV_DATA_FILTER relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@default group=str_replace

	@property str_replace type=table no_caption=1

@default group=char_replace

	@property char_replace type=table no_caption=1

@default group=controller

	@property controller type=textarea rows=30 cols=60 no_caption=1 field=meta method=serialize

@groupinfo str_replace caption="Teksti asendused"
@groupinfo char_replace caption="T&auml;htede asendused"
@groupinfo controller caption="Kontroller"

*/

class otv_data_filter extends class_base
{
	function otv_data_filter()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_treeview/otv_data_filter",
			"clid" => CL_OTV_DATA_FILTER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "str_replace":
				$this->_str_replace($arr);
				break;

			case "char_replace":
				$this->_char_replace($arr);
				break;
			case "controller":
				$prop["value"] = htmlspecialchars($prop["value"]);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "str_replace":
				$this->_save_str_replace($arr);
				break;

			case "char_replace":
				$this->_save_char_replace($arr);
				break;
		}
		return $retval;
	}	

	function _init_str_replace_t(&$t)
	{
		$t->define_field(array(
			"name" => "from",
			"caption" => t("Mis asendada"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "to",
			"caption" => t("Millega asendada"),
			"align" => "center"
		));
	}

	function _str_replace($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_str_replace_t($t);

		$replaces = safe_array($arr["obj_inst"]->meta("str_replace"));
		$replaces[] = array();
		foreach($replaces as $idx => $row)
		{
			$t->define_data(array(
				"from" => html::textbox(array(
					"name" => "replaces[$idx][from]",
					"value" => htmlspecialchars($replaces[$idx]["from"])
				)),
				"to" => html::textbox(array(
					"name" => "replaces[$idx][to]",
					"value" => htmlspecialchars($replaces[$idx]["to"])
				)),
			));
		}

		$t->set_sortable(false);
	}

	function _save_str_replace($arr)
	{
		$sr = array();
		foreach(safe_array($arr["request"]["replaces"]) as $row)
		{
			if ($row["from"] != "" && $row["to"] != "")
			{
				$sr[] = $row;
			}
		}
		$arr["obj_inst"]->set_meta("str_replace", $sr);
	}

	function _init_char_replace_t(&$t)
	{
		$t->define_field(array(
			"name" => "from",
			"caption" => t("T&auml;hekoodid, mis asendada"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "to",
			"caption" => t("T&auml;hekoodid, millega asendada"),
			"align" => "center"
		));
	}

	function _char_replace($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_char_replace_t($t);

		$replaces = safe_array($arr["obj_inst"]->meta("char_replace"));
		$replaces[] = array();
		foreach($replaces as $idx => $row)
		{
			$t->define_data(array(
				"from" => html::textbox(array(
					"name" => "replaces[$idx][from]",
					"value" => htmlspecialchars($replaces[$idx]["from"])
				)),
				"to" => html::textbox(array(
					"name" => "replaces[$idx][to]",
					"value" => htmlspecialchars($replaces[$idx]["to"])
				)),
			));
		}

		$t->set_sortable(false);
	}

	function _save_char_replace($arr)
	{
		$sr = array();
		foreach(safe_array($arr["request"]["replaces"]) as $row)
		{
			if ($row["from"] != "" && $row["to"] != "")
			{
				$sr[] = $row;
			}
		}
		$arr["obj_inst"]->set_meta("char_replace", $sr);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		// compile the replaces to code looping over a data array :)
		$code = "foreach(\$data as \$k => \$v) {";

		$repl = "";
		$dat = safe_array($arr["obj_inst"]->meta("str_replace"));
		foreach($dat as $row)
		{
			$repl .= "str_replace(\"".$this->_code_escape($row["from"])."\", \"".$this->_code_escape($row["to"])."\","; 
		}

		$dat2 = safe_array($arr["obj_inst"]->meta("char_replace"));
		foreach($dat2 as $row)
		{
			$from = join(".", map("chr(%s)", explode(",", $row["from"])));
			if ($row["to_str"] != "")
			{
				$to = $this->_code_escape($row["to_str"]);
			}
			else
			{
				$to = join(".", map("chr(%s)", explode(",", $row["to"])));
			}
			$repl .= "str_replace($from, $to,"; 
		}

		$code2 = "";

		if (($num = (count($dat) + count($dat2))))
		{
			$code .= "\$data[\$k] = ".$repl."\$v".str_repeat(")", $num).";";
			$code2 = "\$data = ".$repl."\$data".str_repeat(")", $num).";";
		}

		$code .= "}";

		if ($arr["obj_inst"]->prop("controller") != "")
		{
			$code = $code2 = $arr["obj_inst"]->prop("controller");
		}

		$arr["obj_inst"]->set_meta("code", $code);
		$arr["obj_inst"]->set_meta("code2", $code2);
	}

	function _code_escape($str)
	{
		return str_replace("\"", "\\\"", $str);
	}

	/** transforms the data given according to the rules

		@attrib api=1

		@comment
	
			$o - transformer object to use
			$data - data to transform
			$row_data - all row fields
	**/
	function transform($o, &$data, $row_data)
	{
		if (is_array($data))
		{
			$code = $o->meta("code");
			if ($code != "")
			{
				eval($code);
			}
		}
		else
		{
			eval($o->meta("code2"));
		}
	}
}
?>

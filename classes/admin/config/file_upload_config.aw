<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_FILE_UPLOAD_CONFIG relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

	@property blacklist_exts type=textbox field=meta method=serialize
	@caption Keelatud faililaiendid

	@property max_file_size type=textbox size=5 field=meta method=serialize
	@caption Maksimaalne faili suurus MB
	
	@property apply_file type=checkbox ch_value=1 field=meta method=serialize
	@caption Kehtib ka failiobjektidele


@default group=menus

	@property menus_tb type=toolbar store=no no_caption=1

	@property menus_table type=table store=no no_caption=1

@groupinfo menus caption="Kaustad"

@reltype MENU value=1 clid=CL_MENU
@caption Kaust

*/

class file_upload_config extends class_base
{
	const AW_CLID = 1329;

	function file_upload_config()
	{
		$this->init(array(
			"tpldir" => "admin/config/file_upload_config",
			"clid" => CL_FILE_UPLOAD_CONFIG
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["set_menu"] = "0";
	}

	function _get_menus_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "set_menu",
			"multiple" => 1,
			"clid" => CL_MENU
		));

		$tb->add_delete_rels_button();
	}

	private function _init_menus_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kaust"),
			"align" => "left"
		));
		$t->define_field(array(
			"name" => "subs",
			"caption" => t("Kaasaarvatud alammen&uuml;&uuml;d"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_menus_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_menus_table($t);

		$subs = $arr["obj_inst"]->meta("subs");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_MENU")) as $c)
		{
			$m = $c->to();
			$t->define_data(array(
				"name" => $m->path_str(),
				"subs" => html::checkbox(array(
					"name" => "subs[".$m->id()."]",
					"value" => 1,
					"checked" => $subs[$m->id()]
				)),
				"oid" => $m->id()
			));
		}
	}

	function _set_menus_table($arr)
	{
		$arr["obj_inst"]->set_meta("subs", $arr["request"]["subs"]);
	}

	function callback_post_save($arr)
	{
		$ps = new popup_search();
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["set_menu"], "RELTYPE_MENU");
	}

	/** Checks if the user is allowed to upload the given file 
		@attrib api=1 params=name

		@param folder required type=oid
			The folder for the file object

		@param file_name required type=string
			The name of the file

		@param file_size required type=int
			The size of the file in bytes

		@param is_file_object required type=bool
			If the file is being uploaded from a separate file object. In this case only configs that apply to file objects as well, are used.

		@returns true/false, based on the file upload conf settings
	**/
	function can_upload_file($arr)
	{
		$conf = $this->get_conf_for_folder($arr["folder"], $arr["is_file_object"]);
		if (!$conf)
		{
			return true;
		}

		$pi = pathinfo($arr["file_name"]);
		$ext = $pathinfo["extension"];

		$bls = $this->make_keys(explode(";", trim($conf->prop("blacklist_exts"))));
		if (isset($bls[$ext]))
		{
			return false;
		}

		if (($conf->prop("max_file_size")*1024*1024) < $arr["file_size"])
		{
			return false;
		}
		return true;
	}

	/** Returns the config object that applies to the given folder
		@attrib api=1 params=pos
		
		@param folder required type=oid
			The folder to check

		@param is_file_object optional type=bool
			If the config should apply to file objects

	**/
	function get_conf_for_folder($folder, $is_file_object = false)
	{
		$ol = new object_list(array(
			"class_id" => CL_FILE_UPLOAD_CONFIG,
			"lang_id" => array(),
			"site_id" => array()
		));

		foreach($ol->arr() as $o)
		{
			if ($is_file_object && !$o->prop("apply_file"))
			{
				continue;
			}

			$subs = $o->meta("subs");
			foreach($o->connections_from(array("type" => "RELTYPE_MENU")) as $c)
			{
				if ($subs[$c->prop("to")])
				{
					$to = $c->to();
					$in_path = false;
					foreach($to->path() as $pi)
					{
						if ($pi->id() == $folder)
						{
							$in_path = true;
						}
					}
					if ($in_path)
					{
						return $o;
					}
				}
				else
				{
					if ($c->prop("to") == $folder)
					{
						return $o;
					}
				}
			}
		}
		return false;
	}
}
?>

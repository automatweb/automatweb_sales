<?php

// aw_help.aw - Abitekst
/*

@classinfo relationmgr=yes

@default table=objects
@default group=general

@property hclass type=hidden store=no group=help_ui,general

@property about type=textarea cols=80 rows=10 group=help_ui parent=help_hbox_table store=no
@caption Klassi kirjeldus

@layout help_hbox_toolbar type=hbox group=help_ui
@layout help_tree_table type=hbox group=help_ui width=30%:70%
@layout help_hbox_tree type=vbox group=help_ui parent=help_tree_table
@layout help_hbox_table type=vbox group=help_ui parent=help_tree_table

@property help_tb type=toolbar parent=help_hbox_toolbar store=no group=help_ui no_caption=1

@property htab type=hidden store=no group=help_ui
@property classname type=hidden store=no group=help_ui

@property tab_tree type=treeview store=no no_caption=1 group=help_ui parent=help_hbox_tree
@property group_props type=callback store=no callback=gen_selected_tab_props group=help_ui parent=help_hbox_table


@groupinfo help_ui caption="Omadused" submit=no

*/

class aw_help extends class_base
{
	function aw_help()
	{
		$this->hide_general = true;
		$this->hide_relationmgr = true;

		$this->init(array(
			"tpldir" => "core/aw_help",
			"clid" => CL_AW_HELP
		));
		/*
		if(!($_GET["hclass"].$_POST["hclass"]))
		{
			echo t("Klass on määramata");
			die();
		}

		if(!$_GET["htab"])
		{
			$url = aw_url_change_var(array(
				"htab" => "general",
			));
			header("Location: $url");
		}*/
	}


	function gen_selected_tab_props($arr)
	{
		if($arr["request"]["htab"] == "help_root")
		{
			return;
		}
		$comp_props = $this->get_property_group(
			array(
				"clid" => $this->get_class_id_from_string(
					array(
						"cl_str" => $arr["request"]["hclass"],
					)
				),
				"group" => $arr["request"]["htab"],
			)
		);


		$data = $this->get_data_from_help_file($arr["request"]["hclass"]);

		$data = $data[$arr["request"]["htab"]];

		$retval = array();

		$retval["about_tab"] = array(
			"type" => "textarea",
			"name" => "about_tab",
			"caption" => t("Abiinfo tabi kohta"),
			"parent" => "help_hbox_table",
			"value" => $data["about_tab"],
		);

		$retval["long_alt_label"] = array(
			"type" => "text",
			"subtitle" => 1,
			"store" => "no",
			"value" => t("Klassi omaduste kirjeldused"),
			"parent" => "help_hbox_table",
		);


		foreach ($comp_props as $key => $prop)
		{
			if($prop["caption"])
			{
				$caption = $prop["caption"];
			}
			else
			{
				$caption = $key."(".$prop["type"].")";
			}

			$lvalue = $prop["caption"]."(". $prop["type"].") - $key";

			$retval["property_label_$key"] = array(
				"type" => "text",
				"subtitle" => 1,
				"store" => "no",
				"value" => $lvalue,
				"parent" => "help_hbox_table",
			);

			$retval["short_alt[$key]"] = array(
	       		"type" => "textbox",
	        	"name" => "short_alt[$key]",
	        	"size " => 100,
	        	"caption" => t("Lühike kirjeldus"),
	        	"parent" => "help_hbox_table",
	        	"value" => $data["short_alt"][$key],
    		);

			$retval["long_alt[$key]"] = array(
	       		"type" => "textarea",
	        	"name" => "long_alt[$key]",
	        	"caption" => t("Pikem kirjeldus"),
	        	"parent" => "help_hbox_table",
	        	"value" => $data["long_alt"][$key],
	        	"cols " => 100,
	        	"rows" => 10,
    		);
		}
    	return $retval;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];

		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "classname":
				$prop["value"] = $arr["request"]["hclass"];
			break;

			case "hclass":
				$prop["value"] = $arr["request"]["hclass"];
			break;

			case "about":
				if($arr["request"]["htab"] != "help_root")
				{
					return PROP_IGNORE;
				}
				$data = $this->get_data_from_help_file($arr["request"]["hclass"]);
				$prop["value"] = $data["about_class"];
				//arr($data);
			break;

			case "tab_tree":
				$tree = &$prop["vcl_inst"];
				$this->gen_tabs_tree($tree,
				$this->get_class_id_from_string(
						array(
							"cl_str" => $arr["request"]["hclass"]
						)), $arr);
			break;

			case "help_tb":
				$this->do_help_toolbar($arr);
			break;

			case "htab":
					$prop["value"] = $arr["request"]["htab"];
			break;
		};
		return $retval;
	}

	function gen_tabs_tree(&$tree, $clid, $arr)
	{
		$ob_inst = get_instance($clid);
		$ob_inst->load_defaults();
		$groups = $ob_inst->groupinfo();

		$classes = aw_ini_get("classes");

		$tree->start_tree(array(
			"type" => TREE_DHTML,
			"root_name" => $classes[$clid]["name"],
			"root_url" => aw_url_change_var(array("htab" => "help_root")),
			"root_icon" => "images/aw_ikoon.gif",
			"has_root" => true,
			"tree_id" => $arr["hclass"],
			"persist_state" => true,
		));

		foreach ($groups as $key => $group)
		{
			if($key == $arr["request"]["htab"])
			{
				$caption = "<b>".$group["caption"]."</b>";
			}
			else
			{
				$caption = $group["caption"];
			}

			if(!$group["parent"])
			{
				$group["parent"] = 0;
			}

			$tree->add_item($group["parent"],array(
    			"id" => $key,
    			"name" => $caption,
    			"url" => aw_url_change_var(array("htab" => $key)),
			));
			//}
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			/*case "hclass":
				if(!$prop["value"])
				{
					$prop["error"] = t("Klass on määramata!");
					return PROP_FATAL_ERROR;
				}
			break;*/

		}
		return $prop;
	}

	/**
		@attrib name=show all_args=1
	**/
	function show($arr)
	{

		$this->read_template("show.tpl");
		$classes = aw_ini_get("classes");
		$data = $this->get_data_from_help_file($arr["hclass"]);

		$clid = $this->get_class_id_from_string(array(
			"cl_str" => $arr["hclass"],
		));

		$comp_props = $this->get_property_group(
			array(
				"clid" => $clid,
				"group" => $arr["htab"],
			)
		);

		$hclass_inst = get_instance($clid);
		$hclass_inst->load_defaults();
		$groups = $hclass_inst->groupinfo();

		foreach ($comp_props as $key => $value)
		{
			$this->vars(array(
				"property" => $key,
				"property_desc" => $data[$_GET["htab"]]["long_alt"][$key],
			));
			$tmp_props.= $this->parse("PROPERTY_TYPES");
		}

		foreach ($groups as $key => $group)
		{
			$link = html::href(array(
				"url" => aw_url_change_var(array("htab" => $key)),
				"caption" => $group["caption"],
			));

			$this->vars(array(
				"tablink" => $link,
			));
			$tmp_grps.= $this->parse("TABS_LIST");
		}

			$change_link = html::href(array(
				"url" => $this->mk_my_orb("new",
					array(
						"hclass" => $arr["hclass"],
						"htab" => $arr["htab"],
					)
				, CL_AW_HELP),
				"caption" => t("Muuda"),
			));


		$this->vars(array(
			"class_name" => $classes[$this->get_class_id_from_string(array("cl_str" => $arr["hclass"]))]["name"],
			"about_class" => $data["about_class"],
			"about_tab" => $data[$arr["htab"]]["about_tab"],
			"properties" => $tmp_props,
			"change_help_link" => $change_link,
			"tabname" => $groups[$arr["htab"]]["caption"],
			"list_tabs" => $tmp_grps,
		));
		return $this->parse();
	}


	function can_edit_help_files()
	{
		$gidlist = aw_global_get("gidlist_oid");
		if(array_search(aw_ini_get("htmlclient.can_edit_help_files") , $gidlist))
		{
			return true;
		}
		return false;
	}

	/**
		@attrib name=write_to_help_file all_args=1
	**/
	function write_to_help_file($arr)
	{
		$to_file = $this->get_data_from_help_file($arr["hclass"]);

		if($arr["htab"] === "help_root")
		{
			$to_file["about_class"] = $arr["about"];
		}
		else
		{
			$to_file[$arr["htab"]]["about_tab"] = $arr["about_tab"];
			$to_file[$arr["htab"]]["short_alt"] = $arr["short_alt"];
			$to_file[$arr["htab"]]["long_alt"] = $arr["long_alt"];
		}
		$serial_str = serialize($to_file);
		$filename = "CL_".strtoupper($arr["hclass"]);
		$fh = fopen(AW_DIR."docs/help/ET/$filename", "w");
		fwrite($fh, $serial_str);

		return $this->mk_my_orb("change", array(
			"hclass" => $arr["hclass"],
			"htab" => $arr["htab"],
		), CL_AW_HELP);
	}

	function get_data_from_help_file($class_str)
	{
		$filename = "CL_".strtoupper($class_str);
		$file_path = AW_DIR."docs/help/ET/$filename";
		if(file_exists($file_path))
		{
			$fh = fopen(AW_DIR."docs/help/ET/$filename", "r");
			$data_str = fread($fh, filesize(AW_DIR."docs/help/ET/$filename"));
			$data = unserialize($data_str);
		}
		return $data;
	}

	/**
		@attrib name=show_help all_args=1
	**/
	function show_help($arr)
	{
		if($arr["hclass"])
		{
			return $this->show($arr);
		}
		$this->read_template("frames.tpl");
		echo $this->parse();
	}

	/**
		@attrib name=show_prop_help all_args=1
	**/
	function show_prop_help($arr)
	{

		$data = $this->get_data_from_help_file($arr["hclass"]);
		$data[$arr["htab"]]["long_alt"][$arr["hprop"]];
		$this->read_template("prop_show.tpl");

		$clid = $this->get_class_id_from_string(array(
			"cl_str" => $arr["hclass"],
		));

		$comp_props = $this->get_property_group(
			array(
				"clid" => $clid,
				"group" => $arr["htab"],
			)
		);
		$this->vars(array(
			"description" => $data[$arr["htab"]]["long_alt"][$arr["hprop"]],
			"property" => $comp_props[$arr["hprop"]]["caption"],
		));
		echo $this->parse();
	}

	/* This is fucking stupid */
	function get_class_id_from_string($arr)
	{
		$classes = aw_ini_get("classes");
		foreach ($classes as $key => $value)
		{
			if($arr["cl_str"] == end(split("/", $value["file"])))
			{
				return $key;
			}
		}
		return false;
	}

	function do_help_toolbar($arr)
	{
		$tb = &$arr["prop"]["toolbar"];
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => "write_to_help_file",
		));

		$tb->add_button(array(
			"name" => "pereview",
			"img" => "preview.gif",
			"tooltip" => t("Salvesta"),
			"url" => $this->mk_my_orb("show", array(
				"hclass" => $arr["request"]["hclass"],
				"htab" => $arr["request"]["htab"]
			), CL_AW_HELP),
		));

	}



	/**
		@attrib name=classes_tree all_args=1
	**/
	function classes_tree($arr)
	{
		$tree = get_instance("vcl/treeview");

		$tree->start_tree(array(
			"tree_id" => "classes_tree",
			"persist_state" => true,
  			"type" => TREE_DHTML,
  			"root_name" => t("AutomatWeb"),
  			"root_url" => $this->mk_my_orb("root_action",array()),
  			"url_target" => "list",
		));

		foreach(aw_ini_get("classfolders") as $key => $node)
		{
			$tree->add_item($node["parent"],array(
				"id" => $key,
    			"name" => "</a>".$node["name"]."<a>",
    		));
		}

		foreach (aw_ini_get("classes") as $key => $node)
		{
			get_instance("core/icons");
			$tree->add_item($node["parents"],array(
				"id" => 10000 + $key,
    			"name" => $node["name"],
    			"url" => $this->mk_my_orb("show_help", array(
    					"hclass" => end(split("/", $node["file"])),
    				)),
    			"iconurl" => icons::get_icon_url($key),
			));
		}
		echo $tree->finalize_tree();
	}



	function change($params)
	{
			$params["cb_part"] = 1;
			$params["group"] = "help_ui";
			return parent::change($params);
	}
}

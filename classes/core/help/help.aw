<?php
// $Header: /home/cvs/automatweb_dev/classes/core/help/help.aw,v 1.11 2008/01/31 13:53:04 kristo Exp $

// more ideas --- I might want to keep the help open when switching between tabs... for this I need to 
// set a cookie
/*
@classinfo  maintainer=kristo
*/
class help extends aw_template
{
	function help()
	{
		$this->tpl_init("help");
	}

	/** shows a help browser for a class
		@attrib name=browser default="1"
		@param clid required
		@param group optional
	**/
	function browser($arr)
	{
		$this->read_template("browser.tpl");

		$cfgu = get_instance("cfg/cfgutils");
		
		// checks what's current root
		$parent = trim($arr["clid"]);
		$parent_is_folder = false;
		if ("fld_" == substr($parent,0,4))
		{
			$arr["clid"] = substr($parent,4);
			$parent_is_folder = true;
		}

/*
		if (!$cfgu->has_properties(array("clid" => $arr["clid"])))
		{
			die(t("Selle klassil puudub abiinfo"));
		};
*/
		$atc_inst = get_instance("admin/add_tree_conf");
		$atc_id = $atc_inst->get_current_conf();

		$props = $cfgu->load_properties(array(
			"clid" => $arr["clid"],
		));

		$clinf = aw_ini_get("classes");
		$clfinf = aw_ini_get("classfolders");
		
		$classdat = $clinf[$arr["clid"]];

		$atc = get_instance("admin/add_tree_conf");
		$tree = $atc->get_class_tree();

		// get path
		if(!$parent_is_folder)
		{
			$current = $clinf[$arr["clid"]]["parents"];
			$link[] = "<a style=\"color:white;\" href=\"orb.aw?class=help&action=browser&clid=".$arr["clid"]."\">".$clinf[$arr["clid"]]["name"].".aw</a>";
		}
		else
		{
			$current = $arr["clid"];
		}
		while(true)
		{	
			//$path[] = $cur_point;
			$link[] = "<a style=\"color:white;\" href=\"orb.aw?class=help&action=browser&clid=fld_".$current."\">".$clfinf[$current]["name"]."</a>";
			//calc parent
			$current = $clfinf[$current]["parent"];
			if(!$current)
			{
				break;
			}
		}
		$link[] = "<a style=\"color:white;\" href=\"orb.aw?class=help&action=browser&clid=fld_0\">".t("root")."</a>";

		foreach(array_reverse($link) as $el)
		{
			$path_string .= " / ".$el;
		}

		$class_tree = $tree;
		$groups = $cfgu->get_groupinfo();

		$tree = get_instance("vcl/treeview");
		$tree->start_tree (array (
			"type" => TREE_DHTML,
			"open_path" => "", // here should be a dynamically generated path which the tree should open automatically!!
			"root_name" => t("AW KLASSIDE ABI"),
			"url_target" => "helpcontent",
			"get_branch_func" => $this->mk_my_orb("get_node",array("clid" => $arr["clid"], "parent" => " ")),
			"has_root" => 1,
		));

		classload("core/icons");
		//me
		//orb.aw?date=01-02-2006&class=help&action=browser&clid=[classid] link from "rohkem infot"

		if($arr["clid"] && is_numeric($arr["clid"]) && !$parent_is_folder)
		{
			$tree->add_item(0,array(
				"name" => $classdat["name"],
				"id" => t("root"),
				"url" => $this->mk_my_orb("classhelp", array("clid" => $arr["clid"])),
				"is_open" => 1,
				"iconurl" => icons::get_icon_url($arr["clid"]),
				"url_target" => "helpcontent",
			));

			// get & display groups
			$target_class = obj();
			$target_class->set_class_id($arr["clid"]);
			$target_groups = $target_class->get_group_list();

			foreach($target_groups as $group_key => $group_data)
			{
				$parent = isset($group_data["parent"]) ? $group_data["parent"] : t("root");
				$tree->add_item($parent ,array(
					"name" => $group_data["caption"],
					"id" => $group_key,
					"url" => $this->mk_my_orb("grouphelp",array(
						"clid" => $arr["clid"],
						"grpid" => $group_key,
					)), 
					"is_open" => 1,
					"iconurl" => "images/icons/help_topic.gif",
				));
			}
		}
		if(strlen($arr["clid"]) && is_numeric($arr["clid"]) && $parent_is_folder)
		{
			// the class_tree that has been generated by admin_menu does not contain enough information 
			// for me
			$tcnt = 0;
			
			foreach($class_tree as $item_id => $item_collection)
			{
				if($arr["clid"] == substr($item_id,4))
				{
					//arr($item_collection);
					foreach($item_collection as $el_id => $el_data)
					{
						$parnt = is_numeric($item_id) && $item_id == 0 ? t("root") : $item_id;
						$tcnt++;

						$tree->add_item(0,array(
							"name" => $el_data["name"],
							"id" => $el_data["id"],
							"url" => $this->mk_my_orb("classhelp", array("clid" => $el_data["id"])),
							"is_open" => 0,
							"iconurl" => empty($el_data["clid"]) ? "" : icons::get_icon_url($el_data["clid"]),
						));

						if ($el_data["clid"])
						{
							$tree->add_item($el_data["id"],array(
								"name" => "fafa",
								"id" => $el_data["id"] + 10000,
							));
						}
						else
						{
							$tree->add_item($el_data["id"],array(
								"name" => "fafa",
								"id" => $el_data["id"] + 10000,
							));


						}
							
					}
				}
			}
		}

		$help_orb_name = strlen($arr["group"])?"grouphelp":"classhelp";
		$this->vars(array(
			// do not use the thing passed in from the URL
			"help_caption" => $path_string, //sprintf(t("Klassi '%s' abiinfo"),$classdat["name"]),
			"help_content_tree" => $tree->finalize_tree(),
			"retrieve_help_func" => $this->mk_my_orb("grouphelp",array(),"help"),
			"browser_caption" => t("AW abiinfo"),
			"help_content" => $this->mk_my_orb($help_orb_name, array(
				"clid" => $arr["clid"],
				"grpid" => $arr["group"],
			)),
		));
		return $this->parse();
	}

	/** 
		@attrib name=get_node all_args=1

	**/
	function get_node($arr)
	{
		$this->read_template("browser.tpl");

		$cfgu = get_instance("cfg/cfgutils");
		/*
		if (!$cfgu->has_properties(array("clid" => $arr["clid"])))
		{
			die(t("Selle klassil puudub abiinfo"));
		};
		*/

		$atc_inst = get_instance("admin/add_tree_conf");
		$atc_id = $atc_inst->get_current_conf();


		$clinf = aw_ini_get("classes");
		$classdat = $clinf[$arr["clid"]];

		$atc = get_instance("admin/add_tree_conf");
		$tree = $atc->get_class_tree();



		$class_tree = $tree;

		$groups = $cfgu->get_groupinfo();

		$tree = get_instance("vcl/treeview");
		$tree->start_tree (array (
			"type" => TREE_DHTML,
			"url_target" => "helpcontent",
			// vbla peaks see get_branc_func olema igal pool node juures .. oh geez.
		));

		classload("core/icons");

		// the class_tree that has been generated by admin_menu does not contain enough information 
		// for me
		$tcnt = 0;

		$parent = trim($arr["parent"]);
		$parent_folder = 0;
		if ("fld_" == substr($parent,0,4))
		{
			$parent_folder = substr($parent,4);
			//print "pf = $parent_folder<br>";
			

		};

		if (is_numeric($parent))
		{
			$props = $cfgu->load_properties(array(
				"clid" => $parent,
			));
			$groups = $cfgu->get_groupinfo();
		};

		foreach($class_tree as $item_id => $item_collection)
		{
			if (isset($parent) && $item_id != $parent)
			{
				continue;
			};

			foreach($item_collection as $el_id => $el_data)
			{

				$parnt = is_numeric($item_id) && $item_id == 0 ? t("root") : $item_id;
				$tcnt++;
				$tree->add_item(0,array(
					"name" => $el_data["name"],
					"id" => $el_data["id"],
					"url" => $this->mk_my_orb("classhelp",array(
						"clid" => $el_data["id"],
					)), 
					"is_open" => 0,
					"iconurl" => empty($el_data["clid"]) ? "" : icons::get_icon_url($el_data["clid"]),
				));

				if ($el_data["clid"])
				{
					$tree->add_item($el_data["id"],array(
						"name" => "fafa",
						"id" => $el_data["id"] + 10000,
					));
				}
				else
				{
					$tree->add_item($el_data["id"],array(
						"name" => "fafa",
						"id" => $el_data["id"] + 10000,
					));


				};
					
			};
		};

		// nii. kuidas ma saan selle puu nii t��le, et oksi laetakse on-demand ja samal ajal saaksid
		// m�ned oksad lahti ka olla?

		// ja teisest k�ljest - see get_branch_func tagastab HTMLi ja mitte puustruktuuri mulle vajalikul
		// kujul. Which is NOT what I want. Fuckety fuck k�ll. teisest k�ljest j�llegi, puud peavadki keerulid olema, siin pole midagi teha


		// get_branch_func peab ilmselt backwards compatiblity jaoks alles j��ma
		// teen �he uue lisa funktsiooni - get_branch() - mis siis annab �he konkreetse
		// node sisu .. ja seda kutsutakse v�lja nii siis, kui on vaja teha konkreetne
		// puu .. kui ka siis, kui on vaja m�ni oks on-demand laadida. Does that make sense?
		// hell yes .. it does

		// ja siis see asi et . see get_branch funktsioon peab ju kuidagi asju m�lus hoidma
		// eeldusel, et k�ik vajalik inff ei ole korraga saadaval. Oh fuck k�ll.

		// also, reading information about all groups of all classes is _really_ slow. 
		if (is_array($groups))
		{
			foreach($groups as $gkey => $gdata)
			{
				$parent = isset($gdata["parent"]) ? $gdata["parent"] : 0;
				$tree->add_item($parent,array(
					"name" => $gdata["caption"],
					"id" => $gkey,
					"url" => $this->mk_my_orb("grouphelp",array(
						"clid" => trim($arr["parent"]),
						"grpid" => $gkey,
					)), 
					"is_open" => 1,
					"iconurl" => "images/icons/help_topic.gif",
				));
			};
		};

		die($tree->finalize_tree());

		$this->vars(array(
			// do not use the thing passed in from the URL
			"help_caption" => sprintf(t("Klassi '%s' abiinfo"),$classdat["name"]),
			"help_content_tree" => $tree->finalize_tree(),
			"retrieve_help_func" => $this->mk_my_orb("grouphelp",array(),"help"),
			"browser_caption" => t("AW abiinfo"),
		));
		die($this->parse());
		print "<pre>";
		print_r($arr);
		print "</pre>";


	}


	/** shows help for a single class or classfolder
		@attrib name=classhelp
		@param clid required
	**/
	function classhelp($arr)
	{
		$obj_is_folder = false;
		if(substr($arr["clid"],0,4) == "fld_")
		{
			$obj_is_folder = true;
			$arr["clid"] = substr($arr["clid"],4);
		}
		$this->read_template("grouphelp.tpl");
		$this->sub_merge = 1;

		$pot_scanner = get_instance("core/trans/pot_scanner");

		if(!$obj_is_folder)
		{
			$cls = aw_ini_get("classes");					
			$line_start = "Klassi ".$cls[$arr["clid"]]["name"]." (".$arr["clid"].")";
		}
		else
		{
			$clsfld = aw_ini_get("classfolders");
			$line_start = "Klassi kataloogi ".$clsfld[$arr["clid"]]["name"]." (".$arr["clid"].")";
		}
		$ini_po_loc = "../lang/trans/".aw_global_get("LC")."/po/aw.ini.po";
		$ini_po_file = $pot_scanner->parse_po_file($ini_po_loc);
		foreach($ini_po_file as $key => $thingie)
		{
			//arr($thingie["msgid"].":".$line_start);
			switch($thingie["msgid"])
			{
				case ($line_start." nimi"):
					$cls_inf["nimi"] = $thingie["msgstr"];
					break;
				case ($line_start." comment"):
					$cls_inf["comment"] = $thingie["msgstr"];
					break;
				case ($line_start." help"):
					$cls_inf["help"] = $thingie["msgstr"];
					break;
			}
		}

		$this->vars(array(
			"property_name" => $cls_inf["nimi"],
			"property_comment" => $cls_inf["comment"],
			"property_help" => $cls_inf["help"],
		));
		$this->parse("PROPERTY_HELP");
		
		$nimi = $obj_is_folder?$clsfld[$arr["clid"]]["name"]:$cls[$arr["clid"]]["name"];
		
		$gr_name = ($obj_is_folder?"Kausta":"Klassi")." '".$nimi."' abiinfo.";

		$this->vars(array(
			"groupname" => $gr_name,
		));

		return $this->parse();
	}

	/** shows help for a single group
		@attrib name=grouphelp
		@param clid required type=int
		@param grpid required
	**/
	function grouphelp($arr)
	{
		$this->read_template("grouphelp.tpl");
		$this->sub_merge = 1;
		$cfgu = get_instance("cfg/cfgutils");
		$cls = aw_ini_get("classes");
		$pot_scanner = get_instance("core/trans/pot_scanner");
		if (!$cfgu->has_properties(array("clid" => $arr["clid"])))
		{
			die(t("Selle klassil puudub abiinfo"));
		};

		$props = $cfgu->load_properties(array(
			"clid" => $arr["clid"],
		));
		$groups = $cfgu->get_groupinfo();
		if (empty($groups[$arr["grpid"]]))
		{
			die(t("Sellist gruppi pole"));
		};

		$po_loc = aw_ini_get("basedir")."/lang/trans/".aw_global_get("LC")."/po/".basename($cls[trim($arr["clid"])]["file"],".aw").".po";
		$po_file = $pot_scanner->parse_po_file($po_loc);
		$msgid["caption"] = "Grupi ".$groups[$arr["grpid"]]["caption"]." (".$arr["grpid"].") pealkiri";
		$msgid["comment"] = "Grupi ".$groups[$arr["grpid"]]["caption"]." (".$arr["grpid"].") comment";
		$msgid["help"] = "Grupi ".$groups[$arr["grpid"]]["caption"]." (".$arr["grpid"].") help";
		foreach($po_file as $entry)
		{
			foreach($msgid as $k => $v)
			{
				if($entry["msgid"] == $v)
				{
					$contents[$k] = $entry["msgstr"];
				}
			}
		}
		$this->vars(array(
			"group_name" => strlen($contents["caption"])?$contents["caption"]:""/*t("t&otilde;lgitud nimi puudub")*/,
			"group_comment" => strlen($contents["comment"])?$contents["comment"]:""/*t("kommentaar puudub")*/,
			"group_help" => strlen($contents["help"])?$contents["help"]:""/*t("abitekst puudub")*/,
		));
		$this->parse("GROUP_HELP");

		foreach($props as $pkey => $pval)
		{
			if(!is_array($pval["group"]))
			{
				$pval["group"] = array($pval["group"]);
			}
			//arr($arr["grpid"]);
			//arr($pval["group"]);
			if (!in_array($arr["grpid"], $pval["group"]))
			{
				continue;
			};
			
			$msgid["caption"] = "Omaduse ".$pval["caption"]." (".$pkey.") caption";
			$msgid["kommentaar"] = "Omaduse ".$pval["caption"]." (".$pkey.") kommentaar";
			$msgid["help"] = "Omaduse ".$pval["caption"]." (".$pkey.") help";
			foreach($po_file as $entry)
			{
				foreach($msgid as $k => $v)
				{
					if($entry["msgid"] == $msgid[$k])
					{
						$pval[$k] = strlen($entry["msgstr"])?$entry["msgstr"]:$pval[$k];
					}
				}
			}
			$this->vars(array(
				"property_name" => strlen($pval["caption"])?$pval["caption"]: "" /*t("t�lgitud nimi puudub")*/,
				"property_comment" => strlen($pval["kommentaar"])?$pval["kommentaar"]: "" /*t("kommentaar puudub")*/,
				"property_help" => strlen($pval["help"])?$pval["help"]:"" /*("abitekst puudub")*/,
			));

			if (!strlen($pval["kommentaar"]) && !strlen($pval["help"]))
			{
				continue;
			}
			$this->parse("PROPERTY_HELP");
		};


		$this->vars(array(
			"groupname" => $groups[$arr["grpid"]]["caption"],
			"subs" => t("Omadused"),
		));
		die($this->parse());
	}

};
?>

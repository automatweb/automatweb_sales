<?php

// metamgr.aw - Muutujate haldus

/*

@classinfo relationmgr=yes
@default table=objects

@property transyes type=checkbox group=general field=meta method=serialize
@caption Vajab t&otilde;lget

@default group=manager


@groupinfo manager caption="Muutujad" submit=no
@property mgrtoolbar type=toolbar no_caption=1 store=no
@caption Toolbar

@property mupload type=fileupload store=no
@caption Vali muutujatega fail


@layout mlist type=hbox width=20%:80%
	@property treeview type=treeview store=no parent=mlist no_caption=1
	@property metalist type=table store=no parent=mlist no_caption=1
@property meta type=hidden group=manager,translate store=no
@caption Metainfo


*/


class metamgr extends class_base
{
	function metamgr()
	{
		$this->init(array(
			"clid" => CL_METAMGR
		));
	}

	function callback_pre_edit($arr)
	{
		$meta_tree = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_META
		));
		$olist = $meta_tree->to_list();
		$rw_tree = array();
		for ($o = $olist->begin(); !$olist->end(); $o = $olist->next())
		{
			$rw_tree[$o->parent()][$o->id()] = (int)$o->prop("ord");
		};

		foreach($rw_tree as $parent => $items)
		{
			asort($rw_tree[$parent]);
		};

		$this->rw_tree = $rw_tree;

	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = class_base::PROP_OK;
		switch($data["name"])
		{
			case "mupload":
				if(empty($arr["request"]["do_import"]))
				{
					return class_base::PROP_IGNORE;
				}
				break;

			case "treeview":
				$this->do_meta_tree($arr);
				break;
			case "metalist":
				$this->do_table($arr);
				break;
			case "mgrtoolbar":
				$this->do_toolbar($arr);
				break;
			case "meta":
				$data["value"] = $arr["request"]["meta"];
				break;
		}
		return $retval;
	}

	function do_meta_tree($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$obj = $arr["obj_inst"];
		$object_name = $obj->name();
		$tree->add_item(0, array(
			"name" => $object_name,
			"id" => $obj->id(),
			"url" => $this->mk_my_orb("change", array(
				"id" => $obj->id(),
				"group" => $arr["prop"]["group"],
			)),
		));

		foreach($this->rw_tree as $parent => $items)
		{
			foreach($items as $obj_id => $ord)
			{
				$o = new object($obj_id);
				$tree->add_item($o->parent(),array(
					"name" => $o->name(),
					"id" => $o->id(),
					"url" => aw_url_change_var(array("meta" => $o->id())),
				));
			};
		};

		$tree->set_selected_item($arr["request"]["meta"]);

		// hm .. now I also need to create an object_tree, eh?
		//$arr["prop"]["value"] = $tree->finalize_tree();
	}

	function do_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"callback" => array($this, "callb_name"),
			"callb_pass_row" => true,
		));
		$transyes = $arr["obj_inst"]->prop("transyes");
		$langdata = array();
		if($transyes == 1)
		{
			$langdata = languages::get_list();

			foreach($langdata as $id => $lang)
			{
				if($arr["obj_inst"]->lang_id() != $id)
				{
					$t->define_field(array(
						"name" => $id,
						"lang_id" => $id,
						"caption" => t($lang),
					));
				}
			}
		}

		$langdata_full = languages::get_list(array("all_data" => true));

		$t->define_field(array(
			"name" => "value",
			"caption" => t("V&auml;&auml;rtus"),
			"callback" => array($this, "callb_value"),
			"callb_pass_row" => true,
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk"),
			"sortable" => 1,
			"callback" => array($this, "callb_ord"),
			"callb_pass_row" => true,
		));

		$t->define_chooser(array(
			"field" => "id",
			"name"  => "sel",
		));

		$this->mt_parent = false;

		if (!empty($arr["request"]["meta"]))
		{
			$root_obj = new object($arr["request"]["meta"]);
			$this->mt_parent = $root_obj->id();
		}
		else
		{
			$root_obj = $arr["obj_inst"];
		}

		$olist = new object_list(array(
			"parent" => $root_obj->id(),
			"class_id" => CL_META,
			"sort_by" => "objects.jrk",
		));

		$new_data = array(
			"id" => "new",
			"is_new" => 1,
			"name" => "",
			"ord" => "",
		);

		foreach($langdata as $lid => $lang)
		{
			 $new_data[$lid] = html::textbox(array(
				"name" => "submeta[new][tolge][".$lid."][name]",
				"size" => 15,
				"value" => "",
			));
		}

		$t->define_data($new_data);
		foreach($olist->arr() as $o)
		{
			$id = $o->id();
			if($transyes) $var_name = iconv(aw_global_get("charset"), "UTF-8", $o->name());
			else $var_name = $o->name();
			$draw_text = $draw_text.

			$trans = array(
				"is_new" => 0,
				"id" => $id,
				"name" => $var_name,
				"value" => $o->comment(),
				"ord" => $o->ord(),
			);

			$tr = $o->meta("translations");
			if(!is_array($tr))
			{
				$tr = array();
			}
			foreach($langdata as $lid => $lang)
			{
				 $trans[$lid] = html::textbox(array(
					"name" => "submeta[".$id."][tolge][".$lid."][name]",
					"size" => 15,
					"value" => iconv($langdata_full[$lid]["charset"], "UTF-8",$tr[$lid]["name"]),
				));
			}

			$t ->define_data($trans);
		}

		// now add the textbox thingies to allow adding of new data
		$obj_name = $arr["obj_inst"]->name();
		$pathstr[] = html::href(array(
			"url" => aw_url_change_var(array(
				"meta" => "",
			)),
			"caption" => $obj_name,
		));

		// I need to calculate the path
		if ($arr["request"]["meta"])
		{
			$ox = new object($arr["request"]["meta"]);
			$stop = $arr["obj_inst"]->id();
			$path = $ox->path(array("to" => $stop));
			foreach($path as $po)
			{
				if ($po->id() != $stop)
				{
					if($transyes) $po_name = iconv(aw_global_get("charset"), "UTF-8", $po->name());
					else $po_name = $po->name();
					$pathstr[] = html::href(array(
						"url" => aw_url_change_var(array(
							"meta" => $po->id(),
						)),
						"caption" => $po_name,
					));
				};
			};
		}

		$t->set_sortable(false);
		$t->set_header("<small>" . join(" &gt; ",$pathstr) . "</small>");

		if (!empty($arr["request"]["do_export"]))
		{
			$file_name = $root_obj->id().".txt";
			header("Content-type: text/plain");
			header("Content-disposition: inline; filename=".t("$file_name").";");
			$draw_text = "";
			foreach($olist->arr() as $o)
			{
				$draw_text = $draw_text.$arr["obj_inst"]->lang_id().":";
				$draw_text = $draw_text.$o->name();
				foreach($langdata as $id => $lang)
				{
					$tr = $o->meta("translations");
					if(($arr["obj_inst"]->lang_id() != $id)&&(strlen($tr[$id])>0))
					{
						$draw_text = $draw_text."\t".$id.":".$tr[$id]["name"];
					}
				}
				if(strlen($o->comment())>0)
				{
 					$draw_text = $draw_text."\tcomm:".$o->comment();
				}
				if(strlen($o->prop("ord"))>0)
				{
					$draw_text = $draw_text."\tord:".$o->prop("ord");
				}
				$draw_text = $draw_text."\r\n";
			}

			die($draw_text);
		}
	}

	function callb_name($arr)
	{
		return html::textbox(array(
			"name" => "submeta[" . $arr["id"] . "][name]",
			"size" => 40,
			"value" => $arr["name"],
		));
	}

	function callb_value($arr)
	{
		return html::textbox(array(
			"name" => "submeta[" . $arr["id"] . "][value]",
			"size" => 10,
			"value" => $arr["value"],
		));
	}

	function callb_ord($arr)
	{
		return html::textbox(array(
			"name" => "submeta[" . $arr["id"] . "][ord]",
			"size" => 4,
			"value" => $arr["ord"],
		));
	}

	function submit_meta($arr = array())
	{
		$transyes = $arr["obj_inst"]->prop("transyes");
		//echo iconv("ISO-8859-1", "UTF-8", "This is a test.");
		if($transyes)//juhul kui utf'ist salvestamine
		{
			foreach($arr["request"]["submeta"] as $key => $val)
			{
				$arr["request"]["submeta"][$key]["name"] = iconv("UTF-8", aw_global_get("charset"),  $val["name"]);
			}
		}

		$obj = $arr["obj_inst"];
		$new = $arr["request"]["submeta"]["new"];
		if ($new["name"])
		{
			// now I need to create a new object under this object
			$parent = $obj->id();
			if ($arr["request"]["meta"])
			{
				$parent = $arr["request"]["meta"];
			};
			$no = new object;
			$no->set_class_id(CL_META);
			$no->set_status(STAT_ACTIVE);
			$no->set_parent($parent);
			$no->set_comment($new["value"]);
			$no->set_name($new["name"]);
			$no->set_prop("ord",(int)$new["ord"]);
			if($transyes) $no->set_meta("translations",$new["tolge"]);
			$no->save();
		};
		$submeta = $arr["request"]["submeta"];
		unset($submeta["new"]);
		if (is_array($submeta))
		{
			$this->_init_save_trans();
			foreach($submeta as $skey => $sval)
			{
				$so = new object($skey);
				$so->set_name($sval["name"]);
				$so->set_comment($sval["value"]);
				$so->set_ord($sval["ord"]);
				if($transyes)
				{
					$this->_save_trans($so, $sval["tolge"]);
				}
				$so->save();
			};
		};
	}

	function _init_save_trans()
	{
		$this->ll = languages::get_list(array(
			"all_data" => true,
			"set_for_user" => true
		));
		$this->repls = array(
			chr(197).chr(161) => "&scaron;",
			chr(197).chr(160) => "&Scaron;",
			chr(197).chr(190) => "&#158;",
			chr(197).chr(189) => "&#142;",
			chr(195).chr(182) => "&ouml;",
			chr(195).chr(164) => "&auml;",
			chr(195).chr(188) => "&uuml;",
			chr(195).chr(181) => "&otilde;",
			chr(195).chr(156) => "&Uuml;",
			chr(195).chr(149) => "&Otilde;",
			chr(195).chr(150) => "&Ouml;",
			chr(195).chr(132) => "&Auml;",
			chr(196).chr(171) => "&#299;",
			chr(196).chr(129) => "&#257;",
			chr(196).chr(147) => "&#275;",
			chr(197).chr(179) => "&#371;",
			chr(196).chr(141) => "&#269;",
			chr(197).chr(171) => "&#363;"
		);
	}

	function _save_trans($obj, $data)
	{
		foreach($this->ll as $lid => $lang)
		{
			if (!$data[$lid])
			{
				continue;
			}

			$nm = "name";
			$str = $data[$lid][$nm];

			// replace estonian chars in other languages with entities
			if ($lang["acceptlang"] != "et")
			{
				foreach($this->repls as $r1 => $r2)
				{
					$str = str_replace($r1, $r2, $str);
				}
			}

			$str = str_replace(chr(226).chr(128).chr(147), "-", $str);
			$nv = iconv("UTF-8", $lang["charset"]."//IGNORE", $str);

			$all_vals[$lid][$nm] = $nv;
			$obj->set_meta("trans_".$lid."_status", 1);
		}
		$obj->set_meta("translations", $all_vals);
		$obj->save();
	}

	function do_toolbar($arr = array())
	{
		$toolbar = $arr["prop"]["toolbar"];
		$toolbar->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "",
			"img" => "save.gif",
		));
		$toolbar->add_separator();

		$toolbar->add_menu_button(array(
			"name" => "move",
			"tooltip" => t("Liiguta valitud omadused gruppi"),
			"img" => "import.gif",
		));

		foreach($this->rw_tree as $parent => $items)
		{
			foreach($items as $obj_id => $ord)
			{
				$o = new object($obj_id);
				if ($o->parent() == $arr["obj_inst"]->id())
				{
					$parent = "move";
				}
				else
				{
					$parent = "mn_" . $o->parent();
				};
				$id = $o->id();
				if($arr["obj_inst"]->prop("transyes")) $object_name = iconv(aw_global_get("charset"), "UTF-8", $o->name());
				else $object_name = $o->name();
				if ($this->rw_tree[$id])
				{
					$toolbar->add_sub_menu(array(
						"name" => "mn_" . $id,
						"parent" => $parent,
						"text" => $object_name,
						"disabled" => ($id == $arr["request"]["meta"]),
					));

					$toolbar->add_menu_item(array(
						"name" => "mnx_" . $id,
						"parent" => "mn_" . $id,
						"text" =>
									"<b>".$object_name."</b>",
						"url" => "javascript:document.changeform.meta.value='$id';document.changeform.action.value='move_items';document.changeform.submit()",
						"disabled" => ($id == $arr["request"]["meta"]),
					));
				}
				else
				{
					$toolbar->add_menu_item(array(
						"name" => "mn_" . $id,
						"parent" => $parent,
						"text" => $object_name,
						"url" => "javascript:document.changeform.meta.value='$id';document.changeform.action.value='move_items';document.changeform.submit()",
						"disabled" => ($o->parent() == $arr["request"]["meta"]),
					));

				};
			};
		};

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "export",
			"tooltip" => t("Export"),
			"img" => "export.gif",
			"url" => aw_url_change_var("do_export", 1)
			));

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "import",
			"tooltip" => t("Import"),
			"img" => "import.gif",
			"url" => aw_url_change_var("do_import", 1)
			));

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitud muutujad"),
			"confirm" => t("Kustuta valitud muutujad?"),
			"action" => "delete_marked",
			"img" => "delete.gif",
		));
	}

	/**
		@attrib name=move_items

	**/
	function move_items($arr)
	{
		$new_parent = $arr["meta"];
		$items_to_move = $arr["sel"];
		if (is_array($items_to_move))
		{
			foreach($items_to_move as $item)
			{
				$o = new object($item);
				$o->set_parent($new_parent);
				$o->save();
			};
		};
		return $this->mk_my_orb("change",array(
			"group" => $arr["group"],
			"meta" => $arr["meta"],
			"id" => $arr["id"],
		),$this->clid);
	}

	/**
		@attrib name=delete_marked
	**/
	function delete_marked($arr)
	{
		$items_to_remove = $arr["sel"];
		if (is_array($items_to_remove))
		{
			foreach($items_to_remove as $item)
			{
				if (!is_oid($item))
				{
					continue;
				};

				$o = new object($item);
				$o->delete();

			};
		};
		return $this->mk_my_orb("change",array(
			"group" => $arr["group"],
			"meta" => $arr["meta"],
			"id" => $arr["id"],
		),$this->clid);
	}


	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
          {
			case "metalist":
				$this->submit_meta($arr);
				break;

			case "mupload":
				if (!empty($_FILES["mupload"]))
				{
					$this->import_variables_from_file($arr);
				};
				break;
		}
		return $retval;
	}

	function import_variables_from_file($arr)
	{
		$filedat = $_FILES["mupload"];
		$handle = @fopen($filedat["tmp_name"],"r");
		if (!$handle)
		{
			return false;
		};
		$parent = $arr["request"]["meta"];
		if(!is_oid($parent) || !$this->can("view", $parent))
		{
			$parent = $arr["obj_inst"]->id();
		}
		while ($data = fgetcsv($handle, 1000, "\t"))
		{
			$no = new object;
			$no->set_class_id(CL_META);
			$no->set_status(STAT_ACTIVE);
			$no->set_parent($parent);
			$trans=array();
			foreach($data as $id => $obj_data)
			{
				if(strpos($obj_data, ":") !== false)
				{
					$prop = explode(":", $obj_data);
					switch($prop[0])
					{
						case "comm":
						$no->set_comment($prop[1]);
						break;

						case $arr["obj_inst"]->lang_id():
						$no->set_name($prop[1]);
						break;

						case "ord":
						$no->set_ord($prop[1]);
						break;

						default:
						$trans[$prop[0]]["name"] = $prop[1];
					}
					$no->set_meta("translations", $trans);
				}
			}
			$no->save();

		}
		fclose($handle);
	}

	function callback_mod_retval($arr)
	{
		if ($arr["request"]["meta"])
		{
			$arr["args"]["meta"] = $arr["request"]["meta"];
		}
	}
}

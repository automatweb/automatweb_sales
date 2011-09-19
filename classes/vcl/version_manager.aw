<?php
/*
@classinfo  maintainer=kristo
*/

class version_manager extends class_base
{
	function version_manager($arr)
	{
		$this->init("vcl/version_manager");
	}

	function init_vcl_property($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return array();
		}
		$tp = $arr["prop"];
		$tp["type"] = "text";

		$tp["value"] = $this->_get_version_table($arr["obj_inst"]);

		$tb = $arr["prop"];
		$tb["type"] = "text";
		$tb["name"] .= "_toolbar";
		$tb["value"] = $this->_get_toolbar($arr["obj_inst"]);

		return array(
			$tp["name"]."_toolbar" => $tb,
			$tp["name"] => $tp
		);
	}

	function _init_version_table(&$t)
	{	
		$t->define_field(array(
			"name" => "vers_crea",
			"caption" => t("Versioon loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "vers_crea_by",
			"caption" => t("Versiooni looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "view_diff",
			"caption" => t("Vaata erinevusi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "set_act",
			"caption" => t("M&auml;&auml;ra aktiivseks"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "version_id"
		));
	}

	function _get_version_table($o)
	{
		
		$t = new vcl_table;
		$this->_init_version_table($t);

		$tn = reset(array_keys($o->get_tableinfo()))."_versions";
		$idx = reset($o->get_tableinfo());
		$idx = $idx["index"];
		$this->db_query("SELECT version_id, vers_crea, vers_crea_by FROM $tn WHERE $idx = '".$o->id()."'");
		while ($row = $this->db_next())
		{
			$row["set_act"] = html::radiobutton(array(
				"name" => "set_act_ver",
				"value" => $row["version_id"]
			));
			$row["view_diff"] = html::popup(array(
				"url" => $this->mk_my_orb("view_diff", array("id" => $o->id(), "vers" => $row["version_id"])),
				"caption" => t("Vaata erinevusi"),
				"width" => 400,
				"height" => 400
			));
			$t->define_data($row);
		}
		$t->set_default_sortby("vers_crea");
		$t->set_default_sorder("desc");
		$t->sort_by();

		return $t->draw();
	}

	function _get_toolbar($o)
	{
		$tb = get_instance("vcl/toolbar");

		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('M&auml;&auml;ra aktiivseks'),
			'action' => 'vm_set_act_ver',
		));
		$tb->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta versioonid'),
			"confirm" => t("Oled kindel et soovid valitud versioonid kustutada?"),
			'action' => 'vm_delete_versions',
		));
		
		return $tb->get_toolbar();
	}

	function vm_set_act_ver($arr)
	{
		$id = $arr["id"];
		$o = obj($arr["id"]);
		$tn_o = reset(array_keys($o->get_tableinfo()));
		$tn = reset(array_keys($o->get_tableinfo()))."_versions";
		$idx = reset(array_values($o->get_tableinfo()));
		$idx = $idx["index"];


		$sav = $arr["set_act_ver"];
		$this->quote(&$sav);
		$data = $this->db_fetch_row("SELECT * FROM $tn WHERE $idx = '".$o->id()."' AND version_id = '$sav'");
		if ($data)
		{
			$old_o = $this->db_fetch_row("SELECT * FROM objects WHERE oid = '".$o->id()."'");
			$old_d = $this->db_fetch_row("SELECT * FROM $tn_o WHERE $idx = '".$o->id()."'");



			// write version to objtable
			$this->quote(&$data);
			$id = $o->id();
			$this->db_query("DESCRIBE $tn_o");
			$sets = array();
			while ($row = $this->db_next())
			{
				$sets[$row["Field"]] = $data[$row["Field"]];
			}
			
			$q = "UPDATE objects SET name = '$data[o_name]',modified = '$data[vers_crea]', modifiedby = '$data[vers_crea_by]', metadata = '$data[o_metadata]'  WHERE oid = $id";
			$this->db_query($q);
			$q = "UPDATE $tn_o SET ".join(",", map2("`%s` = '%s'", $sets))."  WHERE $idx = $id";
			$this->db_query($q);
			$this->db_query("DELETE FROM $tn WHERE $idx = '".$o->id()."' AND version_id = '$sav'");

				
			$c = get_instance("cache");
			$c->file_clear_pt("storage_object_data");
			$c->file_clear_pt("storage_search");
			$c->file_clear_pt("html");
		}
		return $arr["post_ru"];
	}

	function vm_delete_versions($arr)
	{
		$o = obj($arr["id"]);
		$tn = reset(array_keys($o->get_tableinfo()))."_versions";
		$idx = reset(array_values($o->get_tableinfo()));
		$idx = $idx["index"];

		foreach(safe_array($arr["sel"]) as $v_id)
		{
			$this->db_query("DELETE FROM `$tn` WHERE version_id = '$v_id'");
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=view_diff
		@param id required type=int
		@param vers required
	**/
	function view_diff($arr)
	{
		
		$t = new vcl_table;
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "cur",
			"caption" => t("Aktiivne versioon"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ver",
			"caption" => t("Valitud versioon"),
			"align" => "center"
		));

		$o = obj($arr["id"]);
		$tn_o = reset(array_keys($o->get_tableinfo()));
		$tn = reset(array_keys($o->get_tableinfo()))."_versions";
		$idx = reset(array_values($o->get_tableinfo()));
		$idx = $idx["index"];
		$data = $this->db_fetch_row("SELECT * FROM $tn WHERE $idx = '".$o->id()."' AND version_id = '$arr[vers]'");
		foreach($o->get_property_list() as $pn => $pd)
		{
			$cur_v = $o->prop_str($pn);
			$tmp = $o->prop($pn);
			if ($pd["table"] != "objects")
			{
				$ver_v = $data[$pd["field"]];
			}
			else
			if ($pd["field"] == "meta")
			{
				$dat = aw_unserialize($data["o_metadata"]);
				$ver_v = $dat[$pd["name"]];
			}
			else
			{
				$ver_v = $data["o_".$pd["field"]];
			}
			
			$tmp = $o->set_prop($pn, $ver_v);
			$ver_v = $o->prop_str($pn);
			$o->set_prop($pn, $tmp);

			if ($ver_v != $cur_v)
			{
				if ($pd["type"] == "date_select")
				{
					if ($cur_v < 300)
					{
						$cur_v = "";
					}
					else
					{
						$cur_v = date("d.m.Y", $cur_v);
					}
					if ($ver_v < 300)
					{
						$ver_v = "";
					}
					else
					{
						$ver_v = date("d.m.Y", $ver_v);
					}
		
				}
				if ($pd["type"] == "datetime_select")
				{
					if ($cur_v < 300)
					{
						$cur_v = "";
					}
					else
					{
						$cur_v = date("d.m.Y H:i", $cur_v);
					}
					if ($ver_v < 300)
					{
						$ver_v = "";
					}
					else
					{
						$ver_v = date("d.m.Y H:i", $ver_v);
					}
				}
				$t->define_data(array(
					"prop" => $pd["caption"],
					"cur" => $cur_v,
					"ver" => $ver_v
				));
			}
		}
		return $t->draw();
	}
}
?>

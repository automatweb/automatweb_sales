<?php
/*
	@tableinfo kliendibaas_tegevusala index=oid master_table=objects master_index=oid

	@default table=objects
	@default group=general

	@property tegevusala type=textbox size=55 table=kliendibaas_tegevusala
	@caption Tegevusala nimetus

	@property alias type=textbox size=55 table=objects field=alias
	@caption Alias

	@property link type=textbox size=60 table=objects field=meta method=serialize
	@caption Link

	@property ord type=textbox size=5 table=objects field=jrk
	@caption J&auml;rjekord

	@property parent_sector type=relpicker reltype=RELTYPE_PARENT store=no
	@caption Parent

	@property tegevusala_en type=textbox size=55 table=kliendibaas_tegevusala
	@caption Inglisekeelne nimetus

	@property comment type=textarea field=comment
	@caption Kirjeldus

	@property kood type=textbox size=8 table=kliendibaas_tegevusala
	@caption Tegevusala kood

	@property emtak_2008 type=textbox size=8 table=kliendibaas_tegevusala field=aw_emtak_2008
	@caption EMTAK2008 kood

	@property image type=relpicker reltype=RELTYPE_IMAGE rel_id=first use_form=emb field=meta method=serialize
	@caption Pilt

	@property active_image type=relpicker reltype=RELTYPE_IMAGE rel_id=first use_form=emb field=meta method=serialize
	@caption Aktiivse men&uuml;&uuml; pilt

	@property info_document type=relpicker reltype=RELTYPE_DOCUMENT field=meta method=serialize
	@caption Tutvustus

	@property ext_id type=hidden table=kliendibaas_tegevusala field=ext_id
	@caption V&auml;line ID


@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@reltype IMAGE value=1 clid=CL_IMAGE
@caption Pilt

@reltype DOCUMENT value=2 clid=CL_DOCUMENT
@caption Dokument

@reltype PARENT value=3
@caption Parent
*/

/*

CREATE TABLE `kliendibaas_tegevusala` (
  `oid` int(11) NOT NULL default '0',
  `kood` varchar(30) default NULL,
  `tegevusala` text,
  `tegevusala_en` text,
  `kirjeldus` text,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`),
  KEY `kood_i` (`kood`)
) TYPE=MyISAM;
*/

class crm_sector extends class_base
{
	function crm_sector()
	{
		$this->init(array(
			'clid' => CL_CRM_SECTOR,
		));

		$this->trans_props = array(
			"alias", "link", "tegevusala", "comment"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr['prop'];
		$retval = PROP_OK;
		$this_o = $arr["obj_inst"];

		switch($prop["name"])
		{
			case 'name':
				$retval = PROP_IGNORE;
				break;

			case 'tegevusala':
				if(empty($prop["value"]))
				{
					$prop["value"] = $arr["obj_inst"]->name();
				}
				break;

			case 'parent_sector':
				if (is_oid($this_o->id()))
				{
					$prop["value"] = $this_o->parent();
					$parent = new object($this_o->parent());
				}
				elseif (is_oid($arr["request"]["parent"]))
				{
					$prop["value"] = $arr["request"]["parent"];
					$parent = new object($arr["request"]["parent"]);
				}
				else
				{
					$retval = PROP_ERROR;
				}

				$prop["options"] = safe_array(ifset($prop, "options")) + array($parent->id() => $parent->name());
				unset($prop["options"][0]);
				/*

				while ($parent->is_a(CL_CRM_SECTOR))
				{
					if ($this->can("view", $parent->parent()))
					{
						$parent = new object($parent->parent());
					}
				}

				$sectors = new object_tree(array(
					"parent" => $parent,
				));
				arr($sectors->ids(), true);
				$sectors = $sectors->to_list();

				if ($sectors->count() > 0)
				{
					$sector = $sectors->begin();

					do
					{
						if (CL_CRM_SECTOR === ((int) $sector->class_id()) and $sector->id() !== $this_o->id())
						{
							$prop["options"][$sector->id()] = $sector->prop("tegevusala");
						}
					}
					while ($sector = $sectors->next());
				}
				*/
				break;
		}

		return  $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$form = &$arr["request"];
		$this_o = $arr["obj_inst"];

		switch($prop["name"])
		{
			case "transl":
				$this->write_trans_aliases($arr);
				$this->trans_save($arr, $this->trans_props);
				break;

			case 'kood':
				$arr["obj_inst"]->set_name(($form['kood'] ? ''.$form['kood'].' ' : '').$form['tegevusala']);
				break;

			case 'parent_sector':
				if (is_oid($prop["value"]))
				{
					$this_o->set_parent($prop["value"]);
				}
				break;
		}

		return $retval;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_pre_save($arr)
	{
		$imgs = array(
			"0" => array("image_id" => $arr["obj_inst"]->prop("image"))
		);
		$arr["obj_inst"]->set_meta("menu_images", $imgs);
		$aimgs = array(
			"0" => array("image_id" => $arr["obj_inst"]->prop("active_image"))
		);
		$arr["obj_inst"]->set_meta("active_menu_images", $aimgs);
	}

	function request_execute($o)
	{
		static $done;
		if ($done)
		{
			return "";
		}
		$done = 1;
		$ccw = get_instance(CL_CRM_COMPANY_WEBVIEW);
		return $ccw->show_sect(array(
			"section" => $o->id(),
			"wv" => 26371
		));
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "kliendibaas_tegevusala" && $field == "")
		{
			$this->db_query("
				CREATE TABLE `kliendibaas_tegevusala` (
				  `oid` int(11) NOT NULL default '0',
				  `kood` varchar(30) default NULL,
				  `ext_id` varchar(50) default NULL,
				  `tegevusala` text,
				  `tegevusala_en` text,
				  `kirjeldus` text,
				  PRIMARY KEY  (`oid`),
				  UNIQUE KEY `oid` (`oid`),
				  KEY `kood_i` (`kood`)
				) TYPE=MyISAM;
			");
			return true;
		}

		switch($field)
		{
			case "aw_emtak_2008":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int"
				));
				return true;

			case "ext_id":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(50)"
				));
				return true;
		}

		return false;
	}

	public function write_trans_aliases($arr)
	{
		return get_instance("menu")->write_trans_aliases($arr);
	}
}

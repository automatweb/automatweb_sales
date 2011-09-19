<?php

// crm_deal.aw - Tehing
/*

@classinfo relationmgr=yes no_status=1 prop_cb=1

@default table=objects
@default group=general

@tableinfo aw_crm_deal index=aw_oid master_index=brother_of master_table=objects

@default group=general

	@property document type=relpicker reltype=RELTYPE_DOCUMENT table=aw_crm_deal field=aw_document
	@caption Sisudokument

	@property comment type=textarea rows=5 cols=50 table=objects field=comment
	@caption Kirjeldus

	@property sides type=relpicker store=connect multiple=1 reltype=RELTYPE_SIDE
	@caption Osapooled

	@property reg_date type=date_select table=aw_crm_deal field=aw_reg_date
	@caption Allkirjastamise kuup&auml;ev

@default group=files

	@property files_toolbar type=toolbar no_caption=1 store=no

	@property files_table type=table no_caption=1 store=no

@groupinfo files caption="Failid" submit=no

// RELTYPES
@reltype FILE value=1 clid=CL_FILE
@caption Fail

@reltype DOCUMENT value=2 clid=CL_DOCUMENT
@caption Sisudokument

@reltype SIDE value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Osapool
*/

class crm_deal extends class_base
{
	public function __construct()
	{
		$this->init(array(
			"clid" => CL_CRM_DEAL
		));
	}

	public function _get_reg_date(&$arr)
	{
		$arr["prop"]["year_from"] = 1990;
		$arr["prop"]["year_to"] = date("Y")+10;
	}

	public function _get_files_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"tooltip" => t("Lisa fail"),
			"img" => "new.gif",
			"url" => $this->mk_my_orb("add_file", array("id" => $arr["obj_inst"]->id(), "ru" => get_ru()))
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta failid"),
			"confirm" => t("Oled kindel et soovid failid kustutada?"),
			"action" => "delete_files"
		));
	}

	/**
		@attrib name=delete_files
	**/
	function delete_files($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=add_file all_args=1
	**/
	function add_file($arr)
	{
		$deal = obj($arr["id"]);

		$file = obj();
		$file->set_parent($arr["id"]);
		$file->set_class_id(CL_FILE);
		$file->save();

		$deal->connect(array(
			"to" => $file->id(),
			"type" => "RELTYPE_FILE"
		));
		return $this->mk_my_orb("change", array("id" => $file->id(), "return_url" => $arr["ru"], "group" => "general"), CL_FILE);
	}

	/**
		@attrib name=save_files
	**/
	function save_files($arr)
	{
		die();
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	public function _get_files_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_files_table($t);
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_FILE",
		));
		$file_inst = get_instance(CL_FILE);
		
		foreach($conns as $c)
		{
			$f = $c->to();
			$fu = $f->prop("file");
			$name = basename($fu);
			$fname = $file_inst->check_file_path($f->prop("file"));
			$t->define_data(array(
/*				"name" => //html::textbox(array("size" => 70,"value" => //html::href(array(
					//"url" =>
				//	$file_inst->get_url($f->id(), $f->name()),
				//	"caption" => $f->name(),))
				//)),
				$f->name(),*/
				"oid" => $f->id(),
				"change" => html::obj_change_url($f->id(),t("Muuda")),
				"changed" => date("d.m.Y h:i" , $f->prop("modified")),
				"changer" => $f->modifiedby(),

				"name" => html::href(array(
					"url" => $file_inst->get_url($f->id(), $f->name()),
					"caption" => $f->name(),
					"target" => "_blank",
	//				"alt" => $fname,
	//				"title" => $fname
				)),
			));
		}

		$t->set_caption(t("Lepingu failid"));
	}

	function _init_files_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
/*		$t->define_field(array(
			"name" => "file",
			"caption" => t("Fail"),
		));*/
		$t->define_field(array(
			"name" => "changed",
			"caption" => t("Muudetud"),
		));
		$t->define_field(array(
			"name" => "changer",
			"caption" => t("Muutja"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
	}

	public function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	public function do_db_upgrade($t, $f)
	{
		if ("aw_crm_deal" === $t and $f === "")
		{
			$this->db_query("CREATE TABLE aw_crm_deal(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_document":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;
		}
	}
}

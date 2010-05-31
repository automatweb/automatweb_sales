<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PRACTICES relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo aw_practices master_index=brother_of master_table=objects index=aw_oid

@default table=aw_practices
@default group=general

@property data_folder type=relpicker reltype=RELTYPE_DATA_FOLDER
@caption Andmete kaust

@groupinfo practices caption="Praktikad"
@default group=practices

	
	@property toolbar type=toolbar no_caption=1
	@caption Tootjate t&ouml;&ouml;riistariba

	@layout frame type=hbox width=20%:80%

		@property categories_tree parent=frame type=treeview store=no no_caption=1
		@caption Kategooriad

		@property table type=table no_caption=1 parent=frame
		@caption Praktikad

@reltype DATA_FOLDER value=1 clid=CL_MENU
@caption Andmete kaust

*/

class practices extends class_base
{
	const AW_CLID = 1525;

	function practices()
	{
		$this->init(array(
			"tpldir" => "applications/practices/practices",
			"clid" => CL_PRACTICES
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function _get_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus Praktika'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->get_data_folder($arr),
				'return_url' => get_ru()
			), CL_PRACTICE),
		));

		$t->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'action' => '_save_objects',
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		return PROP_OK;
	}

	function _get_categories_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 0,
			"tree_id" => "categories_tree",
			"persist_state" => 1,
		));

		$ol = $arr['obj_inst']->get_categories();

		foreach ($ol->arr() as $oid => $o)
		{
			$t->add_item(0, array(
				'id' => $oid,
				'name' => ($oid == $cat) ? html::strong($o->name()) : $o->name(),
				'iconurl' => icons::get_icon_url(CL_MENU),
				'url' => aw_url_change_var(array(
						'cat' => $oid
					))
			));
			
		}

		return PROP_OK;
	}

	function _get_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select',
			'width' => '10%'
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			'width' => '80%'
		));
		$t->define_field(array(
			'name' => 'type',
			'caption' => t('T&uuml;&uuml;p'),
			'width' => '10%'
		));

		$parent = $arr['obj_inst']->get_data_folder($arr);

		$ol = $arr['obj_inst']->get_practices($parent);

		foreach ($ol->arr() as $oid => $o)
		{
			$t->define_data(array(
				'select' => $oid,
				'name' => html::href(array(
					'caption' => $o->name(),
					'url' => $this->mk_my_orb('change', array(
						'id' => $oid,
						'return_url' => get_ru()
					), CL_PRACTICE),
				)),
				'type' => '???'
			));
		}

		return PROP_OK;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_practices(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "data_folder":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>

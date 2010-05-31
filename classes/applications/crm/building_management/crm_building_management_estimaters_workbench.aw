<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/building_management/crm_building_management_estimaters_workbench.aw,v 1.4 2007/12/06 14:33:21 kristo Exp $
// crm_building_management_estimaters_workbench.aw - Eelarvestaja t&ouml;&ouml;laud 
/*

@classinfo syslog_type=ST_CRM_BUILDING_MANAGEMENT_ESTIMATERS_WORKBENCH relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_building_management_management_estimaters_workbench index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property owner type=relpicker reltype=RELTYPE_OWNER table=crm_building_management_management_estimaters_workbench
@caption Omanik



@groupinfo suppliers caption="Hankijad"

	@property suppliers_toolbar type=toolbar no_caption=1 group=suppliers
	@caption Hankijate t&ouml;&ouml;riistariba

	@property suppliers_tree type=treeview no_caption=1 group=suppliers
	@caption Hankijate puu

	@property suppliers_table type=table no_caption=1 group=suppliers
	@caption Hankijate tabel

@reltype OWNER value=1 clid=CL_CRM_COMPANY
@caption Omanik

@reltype SUPPLIER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Hankija
*/

class crm_building_management_estimaters_workbench extends class_base
{
	const AW_CLID = 1103;

	function crm_building_management_estimaters_workbench()
	{
		$this->init(array(
			"tpldir" => "applications/crm/building_management/crm_building_management_estimaters_workbench",
			"clid" => CL_CRM_BUILDING_MANAGEMENT_ESTIMATERS_WORKBENCH
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function _get_suppliers_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_menu_button(array(
			'name' => 'new',
			'tooltip' => t('Lisa uus hankija'),
			'img' => 'new.gif'
		));
		$t->add_menu_item(array(
			'parent' => 'new',
			'text' => t('Organisatsioon'),
			'title' => t('Organisatsioon'),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 2, // RELTYPE_SUPPLIER
				'return_url' => get_ru()
			), CL_CRM_COMPANY),
		));
		$t->add_menu_item(array(
			'parent' => 'new',
			'text' => t('Isik'),
			'title' => t('Isik'),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 2, // RELTYPE_SUPPLIER
				'return_url' => get_ru()
			), CL_CRM_PERSON),
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta hankija'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud hankijad kustutada?')
		));
		return PROP_OK;
	}

	function _get_suppliers_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'address',
			'caption' => t('Aadress')
		));
		$t->define_field(array(
			'name' => 'contact',
			'caption' => t('Kontaktandmed')
		));
		$t->define_field(array(
			'name' => 'elect',
			'caption' => t('Vali'),
			'width' => '5%',
			'align' => 'center'
		));

		$suppliers_ol = new object_list(array(
			'class_id' => array(CL_CRM_COMPANY, CL_CRM_PERSON)
		));
		foreach ($suppliers_ol->arr() as $supplier_obj)
		{
			switch ($supplier_obj->class_id())
			{
				// siin peaks siis toimuma see aadressi kysimine, crm_personil ja crm_company'l on see nats erinevates propertites kirjas
			}
			$supplier_oid = $supplier_obj->id();
			$t->define_data(array(
				'name' => $supplier_obj->name(),
				'address' => 'aadressa',
				'contact' => 'fafa',
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$supplier_oid.']',
					'value' => $supplier_oid
				))
			));
		}
		return PROP_OK;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete();
			}
		}
		return $arr['post_ru'];
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'owner':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
                }

		return false;
	}
}
?>

<?php
/*
@classinfo no_status=1 no_comment=1 maintainer=kristo prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property db_base type=relpicker reltype=RELTYPE_DB_LOGIN
	@caption Vali andmebaas

	@property db_table type=select 
	@caption Vali tabel

@default group=columns

	@property column_toolbar type=toolbar store=no no_caption=1
	@property column_table type=table store=no no_caption=1

@default group=indexes

	@property index_toolbar type=toolbar store=no no_caption=1
	@property index_table type=table store=no no_caption=1

@groupinfo columns caption="Tulbad" submit=no
@groupinfo indexes caption="Indeksid" submit=no


@reltype DB_LOGIN value=1 clid=CL_DB_LOGIN
@caption DB Login
*/

class db_table_admin extends class_base
{
	function db_table_admin()
	{
		$this->init(array(
			'tpldir' => 'awmyadmin/db_table_admin',
			'clid' => CL_DB_TABLE_ADMIN
		));
	}

	function get_property($args)
	{
		switch($args['prop']['name'])
		{
			case 'db_table':
				$tbls = array(-1 => 'Lisa uus');
				$base = get_instance(CL_DB_LOGIN);
				if ($base->login_as($args["obj_inst"]->meta('db_base')))
				{
					$base->db_list_tables();
					while ($tbl = $base->db_next_table())
					{
						$tbls[$tbl] = $tbl;
					}
				}
				$args['prop']['options'] = $tbls;
				break;
		}
		return PROP_OK;
	}

	/** 
		@attrib name=admin_col params=name 
		
		@param id required
		@param field optional
		@param return_url optional 
	**/
	function admin_col($arr)
	{
		$ob = obj($arr["id"]);
		$this->mk_path($arr["return_url"] ? null : $ob->parent(), $arr["return_url"] ? html::href(array("url" => $arr["return_url"], "caption" => t("Tagasi"))) : html::href(array(
				'url' => $this->mk_my_orb('change', array('id' => $arr["id"], "group" => 'columns')),
				'caption' => t('Tulbad')
			)).' / '.html::href(array(
				'url' => $this->mk_my_orb('admin_col', array('id' => $arr["id"],'field' => $arr["field"])),
				'caption' => ($arr["field"] == '' ? 'Lisa tulp' : "Muuda tulpa $field")
			))
		);

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->meta('db_base'));
		$tbl = $db->db_get_table($ob->meta('db_table'));

		$hc = get_instance("cfg/htmlclient", array(
			"tabs" => true,
		));
		$field = $arr["field"];
		$hc->add_tab(array(
			"active" => true,
			"caption" => !empty($arr["field"]) ? t("Muuda tulpa") : t("Lisa tulp"),
		));
		$hc->start_output();
		$hc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("Nimi"),
			"value" => $arr["field"]
		));
		$hc->add_property(array(
			"name" => "type",
			"type" => "select",
			"caption" => t("T&uuml;&uuml;p"),
			"options" => $db->db_list_field_types(),
			"value" => strtoupper($tbl['fields'][$field]['type'])
		));

		$hc->add_property(array(
			"name" => "length",
			"type" => "textbox",
			"size" => 5,
			"caption" => t("Pikkus"),
			"value" => $tbl['fields'][$field]['length']
		));
		$hc->add_property(array(
			"name" => "null",
			"type" => "checkbox",
			"ch_value" => "YES",
			"caption" => t("NULL"),
			"value" => $tbl['fields'][$field]['null']
		));
		$hc->add_property(array(
			"name" => "default",
			"type" => "textbox",
			"caption" => t("Vaikimisi"),
			"value" => $tbl['fields'][$field]['default']
		));
		$hc->add_property(array(
			"name" => "extra",
			"type" => "select",
			"caption" => t("Extra"),
			"options" => $db->db_list_flags(),
			"value" => strtoupper($tbl['fields'][$field]['flags'])
		));
		$hc->add_property(array(
			"name" => "submit_override",
			"type" => "submit",
			"caption" => !empty($arr["field"]) ? t("Muuda tulpa") : t("Lisa tulp"),
		));
		$hc->finish_output(array(
			"data" => array(
				"action" => "submit_admin_col",
				"id" => $arr["id"],
				"field" => $arr["field"],
				"orb_class" => "db_table_admin",
			),
		));

		return $hc->get_result(array());
	}

	/**  
		@attrib name=submit_admin_col params=name 
	**/
	function submit_admin_col($arr)
	{
		extract($arr);
		$ob = obj($id);
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->meta('db_base'));
		if ($field == '')
		{
			// add
			$db->db_add_col($ob->meta('db_table'), array(
				'name' => $name,
				'type' => $type,
				'length' => $length,
				'null' => ($null ? 'NULL' : 'NOT NULL'),
				'default' => $default,
				'extra' => $extra
			));
			$field = $name;
		}
		else
		{
			// change
			$db->db_change_col($ob->meta('db_table'), $field, array(
				'name' => $name,
				'type' => $type,
				'length' => $length,
				'null' => ($null ? 'NULL' : 'NOT NULL'),
				'default' => $default,
				'extra' => $extra
			));
		}
		return $this->mk_my_orb('admin_col', array('id' => $id, 'field' => $field));
	}

	/**  
		
		@attrib name=admin_index params=name 
		
		@param id required
		@param index optional
		
		@returns
		
		
		@comment

	**/
	function admin_index($arr)
	{
		$ob = obj($arr["id"]);
		$this->mk_path($ob->parent(), html::href(array(
				'url' => $this->mk_my_orb('change', array('id' => $arr["id"], "group" => "indexes")),
				'caption' => t('Muuda')
			)).' / '.html::href(array(
				'url' => $this->mk_my_orb('admin_index', array('id' => $arr["id"],'index' => $arr["index"])),
				'caption' => ($arr["index"] == '' ? t('Lisa indeks') : t("Muuda indeksit"))
			))
		);

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->db_base);

		$db->db_list_indexes($ob->db_table);
		while($idx = $db->db_next_index())
		{
			if ($idx['index_name'] == $arr["index"])
			{
				break;
			}
		}

		$tbl = $db->db_get_table($ob->db_table);
		$fields = $this->make_keys(array_keys($tbl['fields']));

		$hc = get_instance("cfg/htmlclient", array(
			"tabs" => true,
		));
		$field = $arr["field"];
		$hc->add_tab(array(
			"active" => true,
			"caption" => !empty($arr["index"]) ? t("Muuda indeksit") : t("Lisa indeks"),
		));
		$hc->start_output();
		$hc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("Nimi"),
			"value" => $idx['index_name']
		));
		$hc->add_property(array(
			"name" => "field",
			"type" => "select",
			"options" => $fields,
			"value" => $idx['col_name'],
			"caption" => t("Tulbale")
		));

		$hc->add_property(array(
			"name" => "submit_override",
			"type" => "submit",
			"caption" => !empty($arr["index"]) ? t("Muuda indeksit") : t("Lisa indeks"),
		));
		$hc->finish_output(array(
			"data" => array(
				"action" => "submit_admin_index",
				"id" => $arr["id"],
				"index" => $arr["index"],
				"orb_class" => "db_table_admin",
			),
		));

		return $hc->get_result(array());
	}

	/**  
		@attrib name=submit_admin_index params=name 
	**/
	function submit_admin_index($arr)
	{
		extract($arr);

		$ob = obj($id);
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->db_base);

		if ($index != "")
		{
			// change = drop && add
			$db->db_drop_index($ob->db_table, $index);
		}

		// add
		$db->db_add_index($ob->db_table, array(
			'name' => $name,
			'col' => $field
		));
		$index = $name;

		return $this->mk_my_orb("admin_index", array("id" => $id, "index" => $index));
	}

	function _get_column_toolbar($arr)
	{
		if ($arr["request"]["db_id"] && $arr["obj_inst"]->db_base != $arr["request"]["db_id"])
		{
			$arr["obj_inst"]->set_prop("db_base", $arr["request"]["db_id"]);
			$arr["obj_inst"]->save();
		}
		if ($arr["request"]["table"] && $arr["obj_inst"]->db_table != $arr["request"]["table"])
		{
			$arr["obj_inst"]->set_prop("db_table", $arr["request"]["table"]);
			$arr["obj_inst"]->save();
		}

		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => $this->mk_my_orb("admin_col", array("id" => $arr["obj_inst"]->id())),
			"tooltip" => t("Lisa uus tulp")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "",
			"confirm" => t("Oled kindel et soovid valitud tulpi kustutada?"),
			"tooltip" => t("Kustuta valitud tulbad")
		));
	}

	function _get_column_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'type',
			'caption' => t('T&uuml;&uuml;p'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'flags',
			'caption' => t('Attribuudid'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'null',
			'caption' => t('NULL'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'default',
			'caption' => t('Default'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'change',
			'caption' => t('Muuda')
		));
		$t->define_field(array(
			'name' => 'sel',
			'caption' => t('Vali')
		));

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["obj_inst"]->db_base);
		$tbl = $db->db_get_table($arr["obj_inst"]->db_table);
		foreach($tbl['fields'] as $fid => $fdat)
		{
			$fdat['type'] .= '('.$fdat['length'].')';
			$fdat['change'] = html::href(array(
				'url' => $this->mk_my_orb('admin_col', array('id' => $arr["obj_inst"]->id(), 'field' => $fdat['name'])),
				'caption' => 'Muuda'
			));
			$fdat['sel'] = html::checkbox(array(
					'name' => 'sel[]',
				'value' => $fdat['name']
			));

			$t->define_data($fdat);
		}
		$t->set_default_sortby('name');
	}

	function _set_column_table($arr)
	{
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["obj_inst"]->db_base);

		$sel = new aw_array($arr["request"]["sel"]);
		foreach($sel->get() as $secol)
		{
			$db->db_drop_col($arr["obj_inst"]->db_table,$secol);
		}
	}

	function _get_index_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			'url' => $this->mk_my_orb('admin_index', array('id' => $arr["obj_inst"]->id())),
			"tooltip" => t("Lisa uus indeks")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "",
			"confirm" => t("Oled kindel et soovid valitud indekseid kustutada?"),
			"tooltip" => t("Kustuta valitud indeksid")
		));
	}

	function _get_index_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			'name' => 'index_name',
			'caption' => t('Nimi'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'col_name',
			'caption' => t('Tulba nimi'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'unique',
			'caption' => t('Unikaalne'),
			'sortable' => 1
		));
		$t->define_field(array(
			'name' => 'change',
			'caption' => t('Muuda')
		));
		$t->define_field(array(
			'name' => 'sel',
			'caption' => t('Vali')
		));

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["obj_inst"]->db_base);
		$db->db_list_indexes($arr["obj_inst"]->db_table);
		while ($idx = $db->db_next_index())
		{
			$idx['change'] = html::href(array(
				'url' => $this->mk_my_orb('admin_index', array('id' => $arr["obj_inst"]->id, 'index' => $idx['index_name'])),
				'caption' => t('Muuda')
			));
			$idx['sel'] = html::checkbox(array(
				'name' => 'sel[]',
				'value' => $idx['index_name']
			));

			$t->define_data($idx);
		}
		$t->set_default_sortby('index_name');
	}

	function _set_index_table($arr)
	{
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["obj_inst"]->db_base);

		$ar = new aw_array($arr["request"]["sel"]);
		foreach($ar->get() as $idxname)
		{
			$db->db_drop_index($arr["obj_inst"]->db_table, $idxname);
		}
	}
}
?>
<?php
/*
@classinfo syslog_type=ST_DB_SERVER_EXPLORER relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property conf type=relpicker reltype=RELTYPE_CONF automatic=1
	@caption Vali konfiguratsioon

@groupinfo explorer caption="Explorer"
@default group=explorer

	@property ex_toolbar type=toolbar no_caption=1 store=no

	@layout fs type=hbox group=explorer width=20%:80%
		@layout tree_box type=vbox closeable=1 area_caption=Serverid parent=fs
			@property tree type=treeview parent=tree_box group=explorer store=no no_caption=1

		@property content type=table parent=fs group=explorer store=no no_caption=1
	

@reltype CONF value=1 clid=CL_DB_VIEW_CONF
@caption konfiguratsioon

@reltype SQL value=2 clid=CL_DB_SQL_QUERY
@caption p&auml;ringute objekt

@reltype ADMIN value=3 clid=CL_DB_TABLE_ADMIN
@caption administreerimise objekt
*/

class db_server_explorer extends class_base
{
	function db_server_explorer()
	{
		$this->class_base();
		$this->init(array(
			'tpldir' => 'awmyadmin/db_server_explorer',
			'clid' => CL_DB_VIEW
		));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "tree":
				$prop["value"] = $this->do_tree($arr);
				break;

			case "content":
				$prop["value"] = $this->do_content($arr);
				break;

			case "ex_toolbar":
				return $this->_get_ex_toolbar($arr);
		}

		return PROP_OK;
	}

	private function _get_ex_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		if (!$arr["request"]["db_id"])
		{
			return PROP_IGNORE;
		}
		$tb->add_button(array(
			'name' => 'new',
			'tooltip' => 'Lisa',
			'url' => $this->mk_my_orb("add_table", array("id" => $arr["obj_inst"]->id(), "return_url" => get_ru())),
			'img' => 'new.gif'
		));
		$tb->add_button(array(
			'name' => 'delete',
			'tooltip' => 'Kustuta',
			'action' => 'delete_tables',
			'img' => 'delete.gif'
		));
	}

	private function do_tree($arr)
	{
		$ob = $arr["obj_inst"];

		// build the tree of servers.
		$tree = $arr["prop"]["vcl_inst"];
		$tree->start_tree(array(
			'root_name' => t('Konfiguratsioon'),
			'root_url' => $this->mk_my_orb('change', array('id' => $arr['id'])),
			'root_icon' => $this->cfg['baseurl'].'/automatweb/images/icon_aw.gif',
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "dbview"
		));
		
		$db_inst = get_instance(CL_DB_LOGIN);
		$cfg_inst = get_instance(CL_DB_VIEW_CONF);
		if (!$this->can("view", $ob->prop("conf")))
		{
			return;
		}
		$servers = $cfg_inst->get_servers($ob->prop('conf'));
		$databases = $cfg_inst->get_databases_by_server($ob->prop('conf'));
		foreach($servers as $serv_id => $server)
		{
			if (!$arr["request"]["server_id"])
			{
				$arr["request"]["server_id"] = $serv_id;
			}
			$tree->add_item(0,array(
				'id' => $serv_id,
				'name' => $arr["request"]["server_id"] == $serv_id ? "<b>".$server."</b>" : $server,
				'url' => aw_url_change_var("db_id", null, aw_url_change_var("table", null, aw_url_change_var('server_id',$serv_id))),
				'icon' => $this->cfg['baseurl'].'/automatweb/images/icon_aw.gif',
			));

			// add the databases for the server
			$ar = new aw_array($databases[$serv_id]);
			foreach($ar->get() as $dbid => $dbname)
			{
				$tree->add_item($serv_id,array(
					'id' => $dbid,
					'name' => $arr["request"]["db_id"] == $dbid ? "<b>".$dbname."</b>" : $dbname,
					'url' => aw_url_change_var("server_id", $serv_id, aw_url_change_var('db_id',$dbid)),
					'icon' => $this->cfg['baseurl'].'/automatweb/images/icon_aw.gif',
				));

				// add all the tables for the datbase
				if ($db_inst->login_as($dbid))
				{
					$db_inst->db_list_tables();
					while ($tbl = $db_inst->db_next_table())
					{
						$tree->add_item($dbid,array(
							'id' => $dbid.$tbl,
							'name' => $arr["request"]["table"] == $tbl ? "<b>".$tbl."</b>" : $tbl,
							'url' => aw_url_change_var('table', $tbl, aw_url_change_var('db_id',$dbid,aw_url_change_var('server_id',$serv_id))),
							'icon_url' => $this->cfg['baseurl'].'/automatweb/images/icon_aw.gif',
						));
					}
				}
			}
		}
	}

	private function do_content($arr)
	{
		if ($arr["request"]["table"])
		{
			return $this->show_table($arr);
		}
		else
		if ($arr["request"]["db_id"])
		{
			return $this->show_database($arr);
		}
		else
		{
			if (!$arr["request"]["server_id"])
			{
				$cfg_inst = get_instance(CL_DB_VIEW_CONF);
				$arr["request"]["server_id"] = reset(array_keys($cfg_inst->get_servers($arr["obj_inst"]->prop('conf'))));
			}

			return $this->show_server($arr);
		}
	}

	private function show_server($arr)
	{
		$server_id = $arr["request"]["server_id"];
		$s_o = obj($server_id);

		$server = get_instance(CL_DB_SERVER_LOGIN);
		$server->login_as($server_id);
		
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			'name' => 'var',
			'caption' => t('Muutuja'),
			'sortable' => 1,
		));
		$t->define_field(array(
			'name' => 'val',
			'caption' => t('V&auml;&auml;rtus'),
		));

		$stat = $server->db_server_status();
		foreach($stat as $k => $v)
		{
			$t->define_data(array(
				'var' => $k, 
				'val' => $v
			));
		}
		$t->set_default_sortby('var');
		$t->set_caption(sprintf(t("Serveri %s info"), parse_obj_name($s_o->name())));
	}

	private function show_database($arr)
	{
		$server_id = $arr["request"]["server_id"];
		$db_id = $arr["request"]["db_id"];

		$s_o = obj($server_id);
		$db_o = obj($db_id);

		$server = get_instance(CL_DB_LOGIN);
		$server->login_as($db_id);

		$t = $arr["prop"]["vcl_inst"];
		$fields = false;
		$fields_tbl = '';
		$tbldat = array();
		$server->db_list_tables();
		while ($tbl = $server->db_next_table())
		{
			$server->save_handle();
			$tbldat[$tbl] = $server->db_get_table_info($tbl);
			if (!is_array($fields))
			{
				$fields = array_keys($tbldat[$tbl]);
				$fields_tbl = $tbl;
			}
			$server->restore_handle();
		}

		$field_h = '';
		$ar = new aw_array($fields);
		foreach($ar->get() as $fname)
		{
			$t->define_field(array(
				'name' => $fname,
				'caption' => $fname,
				'sortable' => 1,
				'numeric' => (is_numeric($tbldat[$fields_tbl][$fname]))
			));
		}
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "Name"
		));

		foreach($tbldat as $tbl => $td)
		{
			$t->define_data($td);
		}

		$t->set_default_sortby('Name');
		$t->set_caption(sprintf(t("Andmebaasi %s\\%s tabelite info"), $s_o->name(), $db_o->name()));
	}

	private function show_table($arr)
	{
		switch($arr["request"]["type"])
		{
			case 'admin_indexes':
				$this->_sht_do_admin_indexes($arr);
				break;

			case 'content':
				$this->_sht_do_content($arr);
				break;

			case 'query':
				$this->_sht_do_query($arr);
				break;

			case 'admin':
			default:
				$this->_sht_do_admin($arr);
				break;
		}

		$arr["prop"]["vcl_inst"]->set_caption(
			$arr["prop"]["vcl_inst"]->get_caption()." ".
			sprintf(t("| vaata: %s / %s / %s / %s / %s"), 
				html::href(array(
					"url" => aw_url_change_var("type", "admin"),
					"caption" => t("Struktuur")
				)),
				html::href(array(
					"url" => aw_url_change_var("type", "admin_indexes"),
					"caption" => t("Indeksid")
				)),
				html::href(array(
					"url" => aw_url_change_var("type", "content"),
					"caption" => t("Sisu")
				)),
				html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $this->_find_sql_id($arr["obj_inst"]), 
						"return_url" => get_ru(),
						"db_base" => $arr["request"]["db_id"],
						"table" => $arr["request"]["table"]
					), CL_DB_SQL_QUERY),
					"caption" => t("SQL")
				)),
				html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $this->_find_tbl_admin_id($arr["obj_inst"]), 
						"return_url" => get_ru(),
						"db_base" => $arr["request"]["db_id"],
						"table" => $arr["request"]["table"],
						"group" => "columns"
					), CL_DB_TABLE_ADMIN),
					"caption" => t("Administreeri")
				))
			)
		);
	}

	private function _find_sql_id(object $o)
	{
		$res = $o->get_first_obj_by_reltype("RELTYPE_SQL");
		if (!$res)
		{
			$res = obj();
			$res->set_parent($o->id());
			$res->set_class_id(CL_DB_SQL_QUERY);
			$res->save();
			$o->connect(array("to" => $res->id(), "type" => "RELTYPE_SQL"));
		}
		return $res->id();
	}

	private function _find_tbl_admin_id(object $o)
	{
		$res = $o->get_first_obj_by_reltype("RELTYPE_DMIN");
		if (!$res)
		{
			$res = obj();
			$res->set_parent($o->id());
			$res->set_class_id(CL_DB_TABLE_ADMIN);
			$res->save();
			$o->connect(array("to" => $res->id(), "type" => "RELTYPE_ADMIN"));
		}
		return $res->id();
	}

	private function _sht_do_admin($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array('name' => 'name','caption' => t('Nimi'),'sortable' => 1));
		$t->define_field(array('name' => 'type','caption' => t('T&uuml;&uuml;p'),'sortable' => 1));
		$t->define_field(array('name' => 'flags','caption' => t('Attribuudid'),'sortable' => 1));
		$t->define_field(array('name' => 'null','caption' => t('NULL'),'sortable' => 1));
		$t->define_field(array('name' => 'default','caption' => t('Default'),'sortable' => 1));
		$t->define_field(array('name' => 'change','caption' => t('Muuda')));
		$t->define_field(array('name' => 'sel','caption' => t('Vali')));

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["request"]["db_id"]);
		$tbl = $db->db_get_table($arr["request"]["table"]);

		foreach($tbl['fields'] as $fid => $fdat)
		{
			$fdat['type'] .= '('.$fdat['length'].')';
			$tarr = $arr["request"];
			$tarr["type"] = "admin_col";
			$tarr['field'] = $fdat['name'];

			$t->define_data($fdat);
		}
		$t->set_default_sortby('name');
		$t->set_caption(sprintf(
			t("Tabeli %s\\%s\\%s struktuur"), 
			obj($arr["request"]["server_id"])->name(),
			obj($arr["request"]["db_id"])->name(),
			$arr["request"]["table"]
		));
	}

	private function _sht_do_content($arr)
	{
		$dtc = get_instance(CL_DB_SQL_QUERY);
		$nr = 0;
		$dtc->show_query_results($arr["request"]['db_id'],'SELECT * FROM '.$arr["request"]['table'],$nr, $arr["prop"]["vcl_inst"]);

		$arr["prop"]["vcl_inst"]->set_caption(sprintf(
			t("Tabeli %s\\%s\\%s sisu"), 
			obj($arr["request"]["server_id"])->name(),
			obj($arr["request"]["db_id"])->name(),
			$arr["request"]["table"]
		));
	}

	private function _sht_do_admin_indexes($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array('name' => 'index_name','caption' => t('Nimi'),'sortable' => 1));
		$t->define_field(array('name' => 'col_name','caption' => t('Tulba nimi'),'sortable' => 1));
		$t->define_field(array('name' => 'unique','caption' => t('Unikaalne'),'sortable' => 1));

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($arr["request"]["db_id"]);
		$db->db_list_indexes($arr["request"]["table"]);
		while ($idx = $db->db_next_index())
		{
			$tar = $arr;
			$tar['index'] = $idx['index_name'];
			$t->define_data($idx);
		}
		$t->set_default_sortby('index_name');
		$arr["prop"]["vcl_inst"]->set_caption(sprintf(
			t("Tabeli %s\\%s\\%s indeksid"), 
			obj($arr["request"]["server_id"])->name(),
			obj($arr["request"]["db_id"])->name(),
			$arr["request"]["table"]
		));
	}

	/**
		@attrib name=add_table
		@param id required type=int
		@param return_url optional
	**/
	function add_table($arr)
	{
		$ob = obj($arr["id"]);
		$this->mk_path(
			null,
			html::href(array(
				"url" => $arr["return_url"], 
				"caption" => t("Tagasi")
			))
		);

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->meta('db_base'));

		$hc = get_instance("cfg/htmlclient", array(
			"tabs" => true,
		));
		$hc->add_tab(array(
			"active" => true,
			"caption" => t("Lisa tabel"),
		));
		$hc->start_output();

		$hc->add_property(array(
			"name" => "tbl_capt",
			"subtitle" => 1,
			"type" => "text",
			"caption" => t("Tabeli andmed"),
		));
		$hc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("Tabeli nimi"),
			"value" => $arr["field"]
		));

		$hc->add_property(array(
			"name" => "pk_capt",
			"subtitle" => 1,
			"type" => "text",
			"caption" => t("Primaarv&otilde;tme andmed"),
		));

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->meta('db_base'));
		$tbl = $db->db_get_table($ob->meta('db_table'));

		$hc->add_property(array(
			"name" => "pk_name",
			"type" => "textbox",
			"caption" => t("Nimi"),
		));
		$hc->add_property(array(
			"name" => "type",
			"type" => "select",
			"caption" => t("T&uuml;&uuml;p"),
			"options" => $db->db_list_field_types(),
		));

		$hc->add_property(array(
			"name" => "length",
			"type" => "textbox",
			"size" => 5,
			"caption" => t("Pikkus"),
		));
		$hc->add_property(array(
			"name" => "null",
			"type" => "checkbox",
			"ch_value" => "YES",
			"caption" => t("NULL"),
		));
		$hc->add_property(array(
			"name" => "default",
			"type" => "textbox",
			"caption" => t("Vaikimisi"),
		));
		$hc->add_property(array(
			"name" => "extra",
			"type" => "select",
			"caption" => t("Extra"),
			"options" => $db->db_list_flags(),
		));

		$hc->add_property(array(
			"name" => "submit_override",
			"type" => "submit",
			"caption" => t("Lisa tabel"),
		));
		$hc->finish_output(array(
			"data" => array(
				"action" => "submit_add_table",
				"id" => $arr["id"],
				"orb_class" => "db_server_explorer",
				"return_url" => $arr["return_url"]
			),
		));

		return $hc->get_result(array());
	}

	/**
		@attrib name=submit_add_table
	**/
	function submit_add_table($arr)
	{
		if ($arr["name"] != "")
		{
			$ob = obj($arr["id"]);
			$db = get_instance(CL_DB_LOGIN);
			$db->login_as($ob->meta('db_base'));
			$db->db_create_table($arr["name"], array(
					$arr["pk_name"] => $arr
				), $arr["pk_name"]);
		}
		return $arr["return_url"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**
		@attrib name=delete_tables
	**/
	function delete_tables($arr)
	{
		$ob = obj($arr["id"]);
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->meta('db_base'));
		foreach(safe_array($arr["sel"]) as $table)
		{
			$this->quote($table);
			$db->db_drop_table($table);
		}
		return $arr["post_ru"];
	}
}
?>

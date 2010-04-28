<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_DB_TABLE_CONTENTS relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property db_base type=relpicker reltype=RELTYPE_DB_LOGIN automatic=1
	@caption Andmebaas

	@property db_table type=select 
	@caption Tabel

	@property sproc_params type=textbox
	@caption Protseduuri parameetrid

	@property per_page type=textbox size=5
	@caption Mitu rida lehel

@groupinfo content caption="Sisu" submit=no

	@property content_pager type=text store=no group=content no_caption=1
	@property content type=table store=no group=content no_caption=1

@groupinfo admcontent caption="Muuda sisu" submit=no

	@property admcontent_toolbar type=toolbar store=no group=admcontent no_caption=1
	@property admcontent_pager type=text store=no group=admcontent no_caption=1
	@property admcontent type=table store=no group=admcontent no_caption=1
	@property admcontent_addr type=table store=no group=admcontent no_caption=1

@reltype DB_LOGIN value=1 clid=CL_DB_LOGIN 
@caption andmebaasi login

@reltype TRANSFORM value=2 clid=CL_OTV_DATA_FILTER
@caption andmete muundaja

*/

class db_table_contents extends class_base
{
	const AW_CLID = 153;

	var $numeric_types = array('int','tinyint','smallint','mediumint','bigint','float','double');

	function db_table_contents()
	{
		$this->init(array(
			'tpldir' => 'awmyadmin/db_table_contents',
			'clid' => CL_DB_TABLE_CONTENTS
		));
	}

	function get_property($args)
	{
		switch($args['prop']['name'])
		{
			case 'db_table':
				if (!$args['obj_inst']->prop('db_base'))
				{
					return PROP_IGNORE;
				}
				$tbls = array();
				$base = get_instance(CL_DB_LOGIN);
				if ($base->login_as($args['obj_inst']->prop('db_base')))
				{
					$base->db_list_tables();
					while ($tbl = $base->db_next_table())
					{
						$tbls[$tbl] = $tbl;
					}
					asort($tbls);
				}
				$args['prop']['options'] = $tbls;
				break;

			case "sproc_params":
				$base = get_instance(CL_DB_LOGIN);
				if (!is_oid($args['obj_inst']->prop('db_base')) || !$this->can("view", $args['obj_inst']->prop('db_base')))
				{
					return PROP_IGNORE;
				}
				if ($base->login_as($args['obj_inst']->prop('db_base')))
				{
					$table_type = $base->db_get_table_type($args["obj_inst"]->prop("db_table"));
					if ($table_type != DB_TABLE_TYPE_STORED_PROC)
					{
						return PROP_IGNORE;
					}
				}
				break;

			case "content":
				$this->do_content_tbl($args);
				break;

			case "admcontent":
				$this->do_adm_content_tbl($args);
				break;

			case "admcontent_addr":
				$this->do_adm_content_tbl_addr($args);
				break;

			case "admcontent_toolbar":
				$this->do_adm_content_toolbar($args);
				break;

			case "content_pager":
			case "admcontent_pager":
				$args["prop"]["value"] = $this->do_content_pager($args);
				break;
		}
		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "admcontent":
				$this->submit_admin_content($arr);
				break;

		}
		return PROP_OK;
	}

	private function do_content_tbl($arr)
	{
		$ob = $arr["obj_inst"];
		$t =& $arr["prop"]["vcl_inst"];
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->prop('db_base'));

		$t->set_caption(sprintf(t("Tabeli %s sisu serverist %s"), $ob->prop('db_table'), $ob->prop('db_base.name')));
		if ($db->db_get_table_type($ob->prop('db_table')) == DB_TABLE_TYPE_STORED_PROC)
		{
			$q = $ob->prop('db_table')." ".$ob->prop("sproc_params");
			$db->db_query($q);
			$first = true;
			while ($row = $db->db_next())
			{
				if ($first)
				{
					foreach($row as $fn => $fd)
					{
						$t->define_field(array(
							'name' => $fn,
							'caption' => $fn,
							'sortable' => 1,
						));
					}
				}
				$first = false;

				$t->define_data($row);
			}
			$t->sort_by();
		}
		else
		{
			$tbl = $db->db_get_table($ob->prop('db_table'));

			foreach($tbl['fields'] as $fn => $fd)
			{
				$t->define_field(array(
					'name' => $fn,
					'caption' => $fn,
					'sortable' => 1,
					'numeric' => (in_array(strtolower($fd['type']),$this->numeric_types) ? true : false)
				));
			}

			$per_page = $ob->prop('per_page');
			$page = $arr["request"]["page"];
			$q = 'SELECT * FROM '.$ob->prop('db_table');
			$db->db_query_lim($q, ($page*$per_page),((int)$per_page));
			while ($row = $db->db_next())
			{
				$t->define_data($row);
			}
			$t->sort_by();
		}
	}

	private function do_content_pager($arr)
	{
		$ob = $arr["obj_inst"];
		$t =& $arr["prop"]["vcl_inst"];

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->prop('db_base'));
		if ($db->db_get_table_type($ob->prop('db_table')) == DB_TABLE_TYPE_STORED_PROC)
		{
			return;
		}
		$num_rows = $db->db_fetch_field('SELECT count(*) AS cnt FROM '.$ob->prop('db_table'),'cnt');
		$per_page = $ob->prop('per_page');
		return $this->get_pager($ob, $num_rows, $arr["request"]["page"], $per_page);
	}

	private function do_adm_content_toolbar($arr)
	{
		$tb =& $arr["prop"]["toolbar"];
		$tb->add_button(array(
			'name' => 'save',
			'tooltip' => t('Salvesta'),
			'url' => 'javascript:document.changeform.submit()',
			'img' => 'save.gif'
		));
		$tb->add_button(array(
			'name' => 'delete',
			'tooltip' => 'Kustuta',
			'url' => 'javascript:document.changeform.submit()',
			'img' => 'delete.gif'
		));
	}

	private function do_adm_content_tbl($arr)
	{
		$ob = $arr["obj_inst"];
		$t =& $arr["prop"]["vcl_inst"];

		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->prop('db_base'));

		$tbl = $db->db_get_table($ob->prop('db_table'));

		foreach($tbl['fields'] as $fn => $fd)
		{
			$t->define_field(array(
				'name' => $fn,
				'caption' => $fn,
				'sortable' => 1,
				'numeric' => (in_array(strtolower($fd['type']),$this->numeric_types) ? true : false)
			));
		}

		$t->define_field(array(
			'name' => 'sel',
			'caption' => html::href(array(
				'caption' => 'Vali',
				'url' => 'javascript:selall()'
			))
		));

		$t->set_caption('Muuda olemasolevaid ridu');

		$num_rows = $db->db_fetch_field('SELECT count(*) AS cnt FROM '.$ob->prop("db_table"),'cnt');
		$per_page = $ob->prop('per_page');
		$page = $arr["request"]["page"];

		$keys = array();
		$db->db_query_lim('SELECT * FROM '.$ob->prop('db_table'), ($page*$per_page),((int)$per_page));
		$rc = 0;
		while ($row = $db->db_next())
		{
			$rc++;
			// put together the where part for this row for the update
			$wherepts = array();
			// now make all cells into textboxes
			foreach($tbl['fields'] as $fn => $fd)
			{
				$wherepts[] = "$fn = '".$row[$fn]."'";
				$row[$fn] = html::textbox(array(
					'name' => "values[$rc][$fn]",
					'value' => $row[$fn],
					'size' => min($fd['length'],50)
				));
			}
			$row['sel'] = html::checkbox(array(
				'name' => 'sel[]',
				'value' => $rc,
			));
			$t->define_data($row);
			$keys[$rc] = join(" AND ", $wherepts);
		}
		$t->sort_by();
		return;
	}

	private function do_adm_content_tbl_addr($arr)
	{
		$ob = $arr["obj_inst"];
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->prop('db_base'));
		$tbl = $db->db_get_table($ob->prop('db_table'));

		$t =& $arr["prop"]["vcl_inst"];
		$t->set_caption('Lisa uus rida');

		$row = array();
		foreach($tbl['fields'] as $fn => $fd)
		{
			$t->define_field(array(
				'name' => $fn,
				'caption' => $fn,
				'sortable' => 1,
				'numeric' => (in_array(strtolower($fd['type']),$this->numeric_types) ? true : false)
			));

			$row[$fn] = html::textbox(array(
				'name' => 'values[new]['.$fn.']',
				'value' => '',
				'size' => min($fd['length'],50)
			));
		}
		$row['sel'] = '&nbsp;';
		$t->define_data($row);
	}

	private function get_pager($ob, $num_rows, $page, $per_page)
	{
		$ret = array();
		$num_pages = ($per_page > 0 ? $num_rows / $per_page : 1);
		for($i = 0; $i < $num_pages; $i++)
		{
			$cpt = ($i * $per_page).' - '.min((($i+1) * $per_page), $num_rows);
			if ($page == $i)
			{
				$ret []= $cpt;
			}
			else
			{
				$ret []= html::href(array(
					"url" => $this->mk_my_orb('change', array("group" => "content", 'id' => $ob->id(), 'page' => $i)),
					"caption" => $cpt
				));
			}
		}

		return join(" | ", $ret);
	}

	private function submit_admin_content($arr)
	{
		$ob = $arr["obj_inst"];
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($ob->prop('db_base'));

		$page = (int)$page;
		$per_page = $ob->prop('per_page');

		$tbl = $db->db_get_table($ob->prop('db_table'));

		$sela = new aw_array($arr["request"]["sel"]);
		$values = $arr["request"]["values"];

		$keys = array();
		$db->db_query_lim('SELECT * FROM '.$ob->prop('db_table'), ($page*$per_page),((int)$per_page));
		$rc = 0;
		while ($row = $db->db_next())
		{
			$rc++;
			$wherepts = array();
			foreach($tbl['fields'] as $fn => $fd)
			{
				$wherepts[] = "$fn = '".$row[$fn]."'";
			}
			$keys[$rc] = join(" AND ", $wherepts);
		}

		// now go over all rows that were shown and for each check if the data has changed
		// and if it has, write the data
		$q = 'SELECT * FROM '.$ob->prop('db_table').' LIMIT '.($page*$per_page).','.((int)$per_page);
		$db->db_query($q);
		$rc = 0;
		while ($row = $db->db_next())
		{
			$rc++;
			$tochange = array();
			foreach($tbl['fields'] as $fn => $fd)
			{
				if ($row[$fn] != $values[$rc][$fn])
				{
					$tochange[] = "$fn = '".$values[$rc][$fn]."'";
				}
			}
			$tochangestr = join(" , ", $tochange);
			if ($tochangestr != "")
			{
				// check that we didn't mark this row to be deleted, because then we must not change it's content, because
				// then the where part will break
				if (!in_array($rc, $sela->get()))
				{
					$q = "UPDATE ".$ob->prop('db_table')." SET $tochangestr WHERE ".$keys[$rc];
					//echo "q = $q <br />";
					$db->save_handle();
					$db->db_query($q);
					$db->restore_handle();
				}
			}
		}

		if (count($sela->get()))
		{
			foreach($sela->get() as $k)
			{
				$q = "DELETE FROM ".$ob->prop('db_table')." WHERE ".$keys[$k];
				echo "q = $q <br />";
				die();
				$db->save_handle();
				$db->db_query($q);
				$db->restore_handle();
			}
		}

		// check if we should add new
		$add = false;
		foreach($tbl['fields'] as $fn => $fd)
		{
			if ($values['new'][$fn] != '')
			{
				$add = true;
			}
		}
		
		if ($add)
		{
			$cols = new aw_array();
			$vals = new aw_array();
			foreach($values['new'] as $col => $val)
			{
				$cols->set($col);
				$vals->set( "'".$val."'");
			}

			$q = "INSERT INTO ".$ob->prop('db_table')."(".join(", ", $cols->get()).") VALUES(".join(", ", $vals->get()).")";
			$db->db_query($q);
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "admcontent")
		{
			$db = get_instance(CL_DB_LOGIN);
			$db->login_as($arr["obj_inst"]->prop('db_base'));

			if ($db->db_get_table_type($arr["obj_inst"]->prop('db_table')) == DB_TABLE_TYPE_STORED_PROC)
			{
				return false;
			}
		}
		return true;
	}

	////////////////////////////////////////////////////////////////////
	// datasource interface methods
	////////////////////////////////////////////////////////////////////

	function get_fields($o, $params = array())
	{
		if (!$o->prop("db_base"))
		{
			return array();
		}
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($o->prop('db_base'));

		$ret = array();
		if ($db->db_get_table_type($o->prop('db_table')) == DB_TABLE_TYPE_STORED_PROC)
		{
			$q = $o->prop('db_table')." ".$o->prop("sproc_params");
			$row = $db->db_fetch_row($q);
			foreach($row as $fn => $fd)
			{
				$ret[$fn] = $fn;
			}
		}
		else
		{
			$tbl = $db->db_get_table($o->prop('db_table'));

			foreach($tbl['fields'] as $fn => $fd)
			{
				$ret[$fn] = $fn;
			}
		}

		foreach($o->connections_from(array("type" => "RELTYPE_TRANSFORM")) as $c)
		{
			$tr = $c->to();
			$tr_i = $tr->instance();
			$tr_i->transform($tr, $ret);
		}

		return $ret;
	}

	function get_objects($o, $fld = NULL, $tv_sel = NULL, $params = array())
	{
		if (!$o->prop("db_base"))
		{
			return array();
		}

		enter_function("db_table_contents::get_objects");
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($o->prop('db_base'));

		enter_function("db_table_contents::get_objects::query");
		if ($db->db_get_table_type($o->prop('db_table')) == DB_TABLE_TYPE_STORED_PROC)
		{
			$q = $o->prop('db_table')." ".(!isset($params["sproc_params"]) ? $o->prop("sproc_params") : $params["sproc_params"]);
			$db->db_query($q);
		}
		else
		{
			$q = 'SELECT * FROM '.$o->prop('db_table');
			$db->db_query($q);
		}
		exit_function("db_table_contents::get_objects::query");

		$ret = array();
		while ($row = $db->db_next())
		{
			$ret[] = $row;
		}

		foreach($o->connections_from(array("type" => "RELTYPE_TRANSFORM")) as $c)
		{
			$tr = $c->to();
			$tr_i = $tr->instance();
			$tr_i->transform($tr, $ret);
		}

		exit_function("db_table_contents::get_objects");
		return $ret;
	}

	function get_folders($o)
	{
		return array();
	}

	function check_acl()
	{
		return array();
	}

	/** saves editable fields (given in $ef) to object $id, data is in $data

		@attrib api=1

		
	**/
	function update_object($ef, $id, $data)
	{
		return;
	}
}
?>
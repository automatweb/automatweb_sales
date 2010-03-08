<?php
/*
@classinfo no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property db_base type=relpicker reltype=RELTYPE_DB automatic=1
	@caption Andmebaas

	@property sql type=textarea cols=50 rows=5
	@caption SQL

	@property res type=table no_caption=1 store=no
	@caption Tulemused

@reltype DB value=1 clid=CL_DB_LOGIN
@caption Andmebaas
*/

class db_sql_query extends class_base
{
	function db_sql_query()
	{
		$this->init(array(
			'clid' => CL_DB_SQL_QUERY
		));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "name":
				if ($arr["request"]["table"])
				{
					$prop["value"] = $arr["request"]["table"];
				}
				break;

			case "res":
				$this->_get_results($arr);
				return PROP_OK;

			case "db_base":
				if ($arr["request"]["db_base"])
				{
					$prop["value"] = $arr["request"]["db_base"];
					$arr["obj_inst"]->set_prop("db_base", $prop["value"]);
				}
				break;

			case "sql":
				if ($arr["request"]["table"])
				{
					$sql = "SELECT * FROM ".$arr["request"]["table"]." LIMIT 10";
					$prop["value"] = $sql;
					$arr["obj_inst"]->set_prop("sql", $prop["value"]);
				}
				break;
		}
	}

	function _get_results($arr)
	{
		// do the query and display results
		$num_rows = 0;
		$qres = $this->show_query_results($arr["obj_inst"]->meta('db_base'), $arr["obj_inst"]->meta('sql'), &$num_rows, $arr["prop"]["vcl_inst"]);
	}

	/** Takes a table instance and puts results from the query into that
		@attrib api=1 params=pos

		@param db_base required type=oid
			The database login object to use

		@param sql required type=string
			The sql query to execute

		@param num_rows required type=int
			The number of rows in the result is returned in this parameter

		@param t required type=vcl_table
			The table to inser data in

		@returns table html
	**/
	function show_query_results($db_base, $sql, &$num_rows, &$t)
	{
		$db = get_instance(CL_DB_LOGIN);
		$db->login_as($db_base);

		$num_rows = 0;

		$t->parse_xml_def($this->cfg['basedir'] . '/xml/generic_table.xml');

		$rows_defined = false;
		if (!$db->db_query($sql,false))
		{
			return $this->error_table(&$t,&$db);
		}
		while ($row = $db->db_next())
		{
			if (!$rows_defined)
			{
				foreach($row as $rn => $rv)
				{
					$t->define_field(array(
						'name' => $rn,
						'caption' => $rn,
						'sortable' => 1,
						'numeric' => is_numeric($rv)	// this will probably fail most of the time, but I see no other way
					));
				}
				$rows_defined = true;
			}
			$t->define_data($row);
			$num_rows++;
		}

		$t->set_caption(t("P&auml;ringu tulemused"));
		$t->sort_by();
		return $t->draw();
	}

	private function error_table(&$t,&$db)
	{
		$t->define_field(array(
			'name' => 'name',
			'caption' => 'Nimi!'
		));
		$t->define_field(array(
			'name' => 'error',
			'caption' => 'Viga!'
		));

		$errdat = $db->db_get_last_error();
		$t->define_data(array(
			'name' => 'error_cmd',
			'error' => $errdat['error_cmd']
		));
		$t->define_data(array(
			'name' => 'error_code',
			'error' => $errdat['error_code']
		));
		$t->define_data(array(
			'name' => 'error_string',
			'error' => $errdat['error_string']
		));
		return $t->draw();
	}
}
?>
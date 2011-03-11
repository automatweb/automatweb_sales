<?php

/** MySQL driver using PDO **/

class mysql_pdo
{
	var $dbh; #database handle
	var $db_base; #name of the database
	var $qID; # query ID
	var $errmsg; # where we keep our error messages
	var $rec_count;

	const SLOW_QUERY_THRESHOLD = 10; // seconds

	private $db_proc_error_last_fn = "";

	function db_init()
	{
	}

	function db_connect($server = "localhost", $base = "", $username = "", $password = "", $cid = db_connector::DEFAULT_CID_STR)
	{
		if ($base and $username)
		{
			$dsn = "mysql:host={$server};dbname={$base}";
		}
		elseif (db_connector::DEFAULT_CID_STR === $cid)
		{
			$dsn = "mysql:host=" . aw_ini_get("db.host") . ";dbname=" . aw_ini_get("db.base");
			$username = aw_ini_get("db.user");
			$password = aw_ini_get("db.pass");
		}
		else
		{
			try
			{
				$dsn = "mysql:host=" . aw_ini_isset("db.connections.{$cid}.host") ? aw_ini_get("db.connections.{$cid}.host") : "localhost" . ";dbname=" . aw_ini_get("db.connections.{$cid}.base");
				$username = aw_ini_get("db.connections.{$cid}.user");
				$password = aw_ini_get("db.connections.{$cid}.pass");
			}
			catch (awex_cfg_key $e)
			{
				throw new awex_db_connection_param("Incomplete connection parameters for {$cid}: " . $e->getMessage());
			}
		}

		try
		{
			$this->dbh = new PDO($dsn, $username, $password);
		}
		catch (Exception $e)
		{
			throw new awex_db_connection("Database connection failed: " . $e->getMessage());
		}

		$this->db_base = $base;
	}

	function db_query_lim($qt, $limit, $count)
	{
		return $this->db_query($qt." LIMIT ".$limit.($count > 0 ? ",".$count : ""));
	}

	function db_query($qtext, $errors = true)
	{
		if (not($this->dbh))
		{
			// try to acquire the database handle
			$this->db_init();
			// if still not available, raise error. well, ok, we could try to re-connect to the db as well, but
			// if we couldn't do this the first time around, we probably won't be able to this time either
			if (not($this->dbh))
			{
				$eri = new class_base;
				$eri->init();
				$eri->raise_error("ERR_DB_NOTCONNECTED", "I'm not connected to the database, cannot perform the requested query. Please report this to site administrator immediately", true, false);
			}
		}
		elseif ($this->qID)
		{
			// $this->db_free_result();//TODO: make it happen. can't right away because of save_handle/restore_handle
		}

		$this->qID = $this->dbh->query($qtext);
		// $this->log_query($qtext);

		if (!$this->qID )
		{
			if ($errors)
			{
				$e_cnt = 0;
				while (!$this->qID && $this->_proc_error($qtext, $error_info = $this->dbh->errorInfo()) && $e_cnt < 200)
				{
					$this->qID = $this->dbh->query($qtext);
					$e_cnt++;
				}
			}

			if ($this->dbh->errorCode() == 2013)
			{
				// retry with connection
				$this->db_connect(aw_ini_get("db.host"), aw_ini_get("db.base"), aw_ini_get("db.user"), aw_ini_get("db.pass"));
				$this->qID = $this->dbh->query($qtext);
			}

			if (!$this->qID)
			{
				$err = $this->dbh->errorInfo();
				$err_str = $err[2];
				$err_code = $this->dbh->errorCode();
				if (!$errors)
				{
					$this->db_last_error = array(
						'error_cmd' => $qtext,
						'error_code' => $err_code,
						'error_string' => $err_str
					);
					return false;
				}
				$eri = new class_base;
				$eri->init();
				$eri->raise_error("ERR_DB_QUERY",LC_MYSQL_ERROR_QUERY."\n".$qtext."\n".$err_code."\n".$err_str,true,false);
			}
		}
		else
		{
			if ($this->qID instanceof PDOStatement)
			{
				$this->num_rows = $this->qID->rowCount();
				$this->num_fields = $this->qID->columnCount();
			}
			else
			{
				$this->num_rows = 0;
				$this->num_fields = 0;
			}
		}

		$this->rec_count = 0;
		return true;
	}

	////
	// !saves query handle in the internal stack
	// it's your task to make sure you call those functions in correct
	// order, otherwise weird things could happen
	function save_handle()
	{
		if (empty($this->qhandles) || !is_array($this->qhandles))
		{
			$this->qhandles = array();
		}

		array_push($this->qhandles, $this->qID);
	}

	////
	// !restores query handle from internal check
	function restore_handle()
	{
		if (is_array($this->qhandles))
		{
			$this->qID = array_pop($this->qhandles);
		}
	}


	function db_next()
	{
		# this function cannot be called before a query is made
		// don't need numeric indices
		$res = array();
		if ($this->qID)
		{
			$res = $this->qID->fetch(PDO::FETCH_ASSOC);
			if ($res)
			{
				$this->rec_count++;
			}
		}
		return $res;
	}

	function db_last_insert_id()
	{
		$res = $this->dbh->lastInsertId();
		return $res;
	}

	function db_affected_rows()
	{
		return $this->qID->rowCount();
	}

	function db_fetch_row($sql = '')
	{
		if ($sql != '')
		{
			$this->db_query($sql);
		}
		return $this->db_next();
	}

	# seda voib kasutada, kui on vaja teada saada mingit kindlat v2lja
	# a 'la cval tabelist config
	# $cval = db_fetch_field("SELECT cval FROM config WHERE ckey = '$ckey'","cval")
	function db_fetch_field($qtext,$field, $errors = true)
	{
		$this->db_query($qtext, $errors);
		$row = $this->db_next();
		return $row[$field];
	}

	////
	// ! fetch all rows from db_query result
	// qtext - if not set tries to fetch from previous db_query !!
	function db_fetch_array($qtext='')
	{
		if ($qtext)
		{
			$this->db_query($qtext);
		}
		$arr = array();
		while ($row=$this->db_next())
		{
			$arr[]=$row;
		}
		return $arr;
	}



	# need 2 funktsiooni oskavad k2ituda nii array-de kui ka stringidega
	function quote(&$arr)
	{
		if (is_array($arr))
		{
			while(list($k,$v) = each($arr))
			{
				if (is_array($arr[$k]))
				{
					// do nothing
				}
				else
				{
					$arr[$k] = addslashes($arr[$k]);
				}
			}
			reset($arr);
		}
		else
		{
			$arr = addslashes($arr);
			return $arr;
		}
	}

	function dequote(&$arr)
	{
		if (is_array($arr))
		{
			foreach($arr as $k => $v)
			{
				if (is_array($arr[$k]))
				{
					$this->dequote($arr[$k]);
				}
				else
				{
					$arr[$k] = stripslashes($arr[$k]);
				}
			}
		}
		else
		{
			$arr = stripslashes($arr);
		}
	}

	function num_rows()
	{
		return $this->num_rows;
	}

	function db_list_tables()
	{
		$this->tID = mysql_list_tables($this->db_base);
		$this->tablecount = mysql_num_rows($this->tID);
		$this->db_next_table(true);
	}

	function db_next_table($reset = false)
	{
		static $cnt = 0;
		if ($reset == true)
		{
			$cnt = 0;
			return;
		}
		$res = ($cnt < $this->tablecount) ? mysql_tablename($this->tID,$cnt) : false;
		$cnt++;
		return $res;
	}

	////
	// !returns the properties of table $name or false if it doesn't exist
	// properties are returned as array $tablename => $tableprops
	// where $tableprops is an array("name" => $table_name, "fields" => $fieldprops)
	// where $fieldprops is an array of $fieldname => $cur_props
	// where $cur_props is an array("name" => $field_name, "length" => $field_length, "type" => $field_type, "flags" => $field_flags)
	// example: CREATE TABLE tbl (id int, content text)
	// returns: array("name" => "tbl",
	//								"fields" => array("id" => array("name" => "id", "length" => 10, "type" => "int", "flags" => ""),
	//																	"content" => array("name" => "content", "length" => "65535", "type" => "text", "flags" => "")
	//																	)
	//								)
	function db_get_table($name)
	{
		$ret = array('name' => $name,'fields' => array());
		$fID = @mysql_list_fields($this->db_base, $name, $this->dbh);
		if (!$fID)
		{
			return false;
		}

		$numfields = mysql_num_fields($fID);
		for ($i=0; $i < $numfields; $i++)
		{
			$_name = mysql_field_name($fID,$i);
			$type = mysql_field_type($fID,$i);
			$len =  mysql_field_len($fID,$i);
			$flags = mysql_field_flags($fID,$i);
			$ret['fields'][$_name] = array('name' => $_name, 'length' => $len, 'type' => $type, 'flags' => '');
		}

		$this->db_query("DESCRIBE $name");
		while ($row = $this->db_next())
		{
			$ret['fields'][$row['Field']]['name'] = $row['Field'];
			if (strpos($row['Type'],'(') === false)
			{
				$ret['fields'][$row['Field']]['length'] = $row['Type'] == 'text' ? 65535 : ($row['Type'] == 'mediumtext' ? 1024*1024*16 : 0 );
				$ret['fields'][$row['Field']]['type'] = $row['Type'];
			}
			else
			{
				preg_match('/(.*)\((.*)\)/', $row['Type'], $mt);
				$ret['fields'][$row['Field']]['length'] = $mt[2];
				$ret['fields'][$row['Field']]['type'] = $mt[1];
			}
			$ret['fields'][$row['Field']]['null'] = $row['Null'];
			$ret['fields'][$row['Field']]['default'] = $row['Default'];
			$ret['fields'][$row['Field']]['flags'] = $row['Extra'];
		}

		// indeksid
		return $ret;
	}

	////
	// !this returns the sql for creating the field
	function mk_field_len($type,$length)
	{
		$type=strtoupper($type);
		switch ($type)
		{
			CASE 'TINYINT':
			CASE 'SMALLINT':
			CASE 'MEDIUMINT':
			CASE 'INT':
			CASE 'INTEGER':
			CASE 'BIGINT':
			CASE 'CHAR':
			CASE 'VARCHAR':
				return $type.'('.$length.')';
			default:
				return $type;
		}
	}

	////
	// !Reads and returns the structure of the database
	function db_get_struct()
	{
		$this->db_query('SHOW TABLES');
		$tables = array();
		while($row = $this->db_next())
		{
			list($key,$val) = each($row);
			$row[0] = $val;
			// form entry tables are ignored
			if (not(preg_match('/form_(\d+?)_entries/',$row[0])))
			{
				$name = $row[0];
				$this->save_handle();
				$this->db_query("DESCRIBE $name");
				while($row = $this->db_next())
				{
					$flags = array();
					list($type,$extra) = explode(' ',$row['Type']);
					if ($extra)
					{
						$flags[] = $extra;
					};

					if (not($row['Null'] == 'YES'))
					{
						$flags[] = 'NOT NULL';
					};

					if ($row['Extra'])
					{
						$flags[] = $row['Extra'];
					};

					$tables[$name][$row['Field']] = array(
						'type' => $type,
						'flags' => $flags,
						'key' => $row['Key'],
					);

				};
				$this->restore_handle();
			}
		}
		return $tables;
	}

	function db_create_table($name, $field_data, $primary)
	{
		$field_dfns = $indexed_fields = array();
		$types = $this->db_list_field_types();

		foreach ($field_data as $field_name => $data)
		{
			$length = $default = $null = "";
			$type = strtoupper($data["type"]);

			if (empty($data["type"]) or !in_array($type, $types))
			{
				return false;
			}

			if (false === $data["null"])
			{
				$null = 'NOT NULL';
			}

			if (!empty($data["length"]))
			{
				$type = $this->mk_field_len($type, ((int) $data["length"]));
			}

			if ($field_name === $primary)
			{
				$default = "DEFAULT '0'";
			}

			if (!empty($data["default"]))
			{
				$default = "DEFAULT '" . $data["default"] . "'";
			}

			$field_dfns[] = "`$field_name` $type $default $null";
		}

		$field_dfns = implode(",", $field_dfns);
		$index = count($indexed_fields) ? ", (`" . implode("`,`", $indexed_fields) . "`)" : "";
		if ($field_dfns != "")
		{
			$q = "CREATE TABLE `$name` ($field_dfns, PRIMARY KEY(`$primary`)" . $index . ")";
		}
		else
		{
			$q = "CREATE TABLE `$name`";
		}
		return $this->db_query($q);
	}

	function db_list_databases()
	{
		return $this->db_query('SHOW DATABASES');
	}

	function db_next_database()
	{
		$tmp = $this->db_next();
		return $tmp === false ? false : array('name' => $tmp['Database']);
	}

	function db_create_database($args)
	{
		extract($args);
		$this->db_query("CREATE DATABASE $name");
		$this->db_query('GRANT ALL PRIVILEGES on '.$name.'.* TO '.$user.'@'.$host." IDENTIFIED BY '".$pass."'");
	}

	function db_server_status()
	{
		$ret = array();
		$this->db_query('SHOW STATUS');
		$ret['Server_version'] = mysql_get_server_info($this->dbh);
		$ret['Protocol_version'] = mysql_get_proto_info($this->dbh);
		$ret['Host_info'] = mysql_get_host_info($this->dbh);
		while ($row = $this->db_next())
		{
			$ret[$row['Variable_name']] = $row['Value'];
		}
		$ret['Queries_per_sec'] = (int)($ret['Questions'] / $ret['Uptime']);
		return $ret;
	}

	function db_get_table_info($tbl)
	{
		return $this->db_fetch_row("SHOW TABLE STATUS LIKE '$tbl'");
	}

	function db_list_field_types()
	{
		return array('' => '',
		'VARCHAR' => 'VARCHAR',
		'TINYINT' => 'TINYINT',
		'TEXT' => 'TEXT',
		'DATE' => 'DATE',
		'SMALLINT' => 'SMALLINT',
		'MEDIUMINT' => 'MEDIUMINT',
		'INT' => 'INT',
		'BIGINT' => 'BIGINT',
		'FLOAT' => 'FLOAT',
		'DOUBLE' => 'DOUBLE',
		'DECIMAL' => 'DECIMAL',
		'DATETIME' => 'DATETIME',
		'TIMESTAMP' => 'TIMESTAMP',
		'TIME' => 'TIME',
		'YEAR' => 'YEAR',
		'CHAR' => 'CHAR',
		'TINYBLOB' => 'TINYBLOB',
		'TINYTEXT' => 'TINYTEXT',
		'BLOB' => 'BLOB',
		'MEDIUMBLOB' => 'MEDIUMBLOB',
		'MEDIUMTEXT' => 'MEDIUMTEXT',
		'LONGBLOB' => 'LONGBLOB',
		'LONGTEXT' => 'LONGTEXT',
		'ENUM' => 'ENUM',
		'SET' => 'SET');
	}

	function db_list_flags()
	{
		return array('' => '',
			'AUTO_INCREMENT' => 'AUTO_INCREMENT'
		);
	}

	function db_drop_col($tbl,$col)
	{
		$q = "ALTER TABLE $tbl DROP $col";
		$this->db_query($q);
	}

	function db_add_col($tbl,$coldat)
	{
		extract($coldat);

		if (isset($extra) and $extra === 'AUTO_INCREMENT')
		{
			$extra = 'PRIMARY KEY AUTO_INCREMENT';
		}
		else
		{
			$extra = empty($extra) ? "" : " {$extra}";
		}

		if (!empty($length))
		{
			$len = '('.$length.')';
		}
		else
		{
			$len = "";
		}

		$default = empty($default) ? "" : " default '{$default}'";
		$null = empty($null) ? "" : " {$null}";

		// quote column name
		$col_name = explode(".", $name);
		foreach ($col_name as $key => $name_part)
		{
			$col_name[$key] = "`" . $name_part . "`";
		}
		$name = implode(".", $col_name);

		// exec q
		$q = "ALTER TABLE {$tbl} ADD {$name} {$type}{$len}{$null}{$default}{$extra}";
		$this->db_query($q);
	}

	function db_change_col($tbl, $col, $newdat)
	{
		extract($newdat);
		$q = "ALTER TABLE $tbl CHANGE $name $name $type($length) $null ".($default == "" ? "" : "default '$default'")." $extra ";
		$this->db_query($q);
	}

	function db_list_indexes($tbl)
	{
		$this->db_query("SHOW INDEX FROM $tbl");
	}

	function db_next_index()
	{
		$dat = $this->db_next();
		if ($dat)
		{
			return array(
				'index_name' => $dat['Key_name'],
				'col_name' => $dat['Column_name'],
				'unique' => !$dat['Non_unique']
			);
		}
		return false;
	}

	function db_add_index($tbl, $idx_dat)
	{
		extract($idx_dat);
		$q = "ALTER TABLE $tbl ADD INDEX $name($col)";
		$this->db_query($q);
	}

	function db_drop_index($tbl, $name)
	{
		$q = "ALTER TABLE $tbl DROP INDEX $name";
		$this->db_query($q);
	}

	function db_get_last_error()
	{
		return $this->db_last_error;
	}

	// logs the query, if user has a cookie named log_query
	function log_query($msg)
	{
		if (isset($GLOBALS["HTTP_COOKIE_VARS"]["log_query"])) //TODO: control this from somewhere securer
		{
			$uid = aw_global_get("uid");
			$logfile = "/www/log/mysql-" . $uid . ".log";
			$fp = fopen($logfile,"a");
			fwrite($fp,$msg . "\n\n-----------------------------------\n\n");
			fclose($fp);
		}
	}

	function db_table_exists($tbl)
	{
		$q = "DESCRIBE $tbl";
		if (mysql_query($q, $this->dbh))
		{
			return true;
		}
		return false;
	}

	function _dbg_backtrace()
	{
		$msg = "";
		if (function_exists("debug_backtrace"))
		{
			$bt = debug_backtrace();
			for ($i = count($bt)-1; $i > 0; $i--)
			{
				if (!empty($bt[$i+1]["class"]))
				{
					$fnm = $bt[$i+1]["class"]."::".$bt[$i+1]["function"];
				}
				else
				if (!empty($bt[$i+1]["function"]))
				{
					if ($bt[$i+1]["function"] !== "include")
					{
						$fnm = $bt[$i+1]["function"];
					}
					else
					{
						$fnm = "";
					}
				}
				else
				{
					$fnm = "";
				}

				$msg .= $fnm.":".$bt[$i]["line"]."->";
			}
		}

		return $msg;
	}

	function db_fn($fn)
	{
		return $fn;
	}

	function db_get_table_type($tbl)
	{
		return DB_TABLE_TYPE_TABLE;
	}

	function _proc_error($q, $error_info)
	{
		$errstr = $error_info[2];

		if (strpos($errstr, "Unknown column") !== false)
		{
			if (!preg_match("/Unknown column '(.*)\.(.*)'/imsU" , $errstr, $mt))
			{
				if (preg_match("/Unknown column '(.*)' in 'order clause'/imsU" , $errstr, $mt))
				{
					$mt = array(1 => "", 2 => $mt[1]);
				}
				else
				if (preg_match("/Unknown column '(.*)' in 'field list'/imsU" , $errstr, $mt))
				{
					if (!preg_match("/UPDATE (.*) SET/imsU", $q, $mt_a))
					{
						preg_match("/INSERT INTO (.+) \(/imsU", $q, $mt_a);
					}
					$mt[2] = $mt[1];
					$mt[1] = $mt_a[1];
				}
			}

			if ($this->db_proc_error_last_fn == $mt[2] && $this->db_proc_error_last_fn)
			{
				return false; // if we get the same error as last time, the upgrader did not create the correct field, so error out
			}

			$this->db_proc_error_last_fn = $mt[2];
			// find the table from property list. oh this is gonna be slooooooow
			$clss = aw_ini_get("classes");
			$upgrade_result = null;

			if (empty($mt[1]) && !empty($mt[2]))
			{
				// find property with field given and table in the error query
				foreach($clss as $clid => $inf)
				{
					$o = obj();
					$o->set_class_id($clid);
					$pl = $o->get_property_list();
					foreach($pl as $prop_item)
					{
						if ($prop_item["field"] == $mt[2] && strpos($q, $prop_item["table"]) !== false)
						{
							$mt[1] = $prop_item["table"];
							break;
						}
					}
				}
			}

			foreach($clss as $clid => $inf)
			{
				if (!is_class_id($clid))
				{
					continue;
				}
				$o = obj();
				$o->set_class_id($clid);
				$ti = $o->get_tableinfo();

				foreach(safe_array($ti) as $tn => $td)
				{
					if (isset($mt[1]) && $mt[1] === $tn)
					{
						// got our class
						$i = $o->instance();
						if (method_exists($i, "do_db_upgrade"))
						{
							$upgrade_result = $upgrade_result | $i->do_db_upgrade($tn, $mt[2], $q, $errstr);// classes exist that use same db table. collect upgrades from all of them.
						}
					}
				}
			}

			if (!isset($upgrade_result))
			{
				// if not found, then call the static upgrader
				$cv = new converters();
				$upgrade_result = $cv->do_db_upgrade($mt[1], $mt[2], $q, $errstr);
			}

			return $upgrade_result;
		}

		if (strpos($errstr, "doesn't exist") !== false)
		{
			preg_match("/Table '(.*)\.(.*)' doesn't exist/imsU" , $errstr, $mt);

			if ($this->db_proc_error_last_fn == $mt[2])
			{
				return false; // if we get the same error as last time, the upgrader did not create the correct field, so error out
			}
			$this->db_proc_error_last_fn = $mt[2];
			// find the table from property list. oh this is gonna be slooooooow
			$clss = aw_ini_get("classes");
			foreach($clss as $clid => $inf)
			{
				if (!is_class_id($clid))
				{
					continue;
				}
				$o = obj();
				$o->set_class_id($clid);
				$ti = $o->get_tableinfo();
				foreach($ti as $tn => $td)
				{
					if ($mt[2] === $tn)
					{
						// got our class
						$i = $o->instance();
						if (method_exists($i, "do_db_upgrade"))
						{
							return $i->do_db_upgrade($tn, "", $q, $errstr);
						}
					}
				}
			}

			// if not found, then call the static upgrader
			$cv = new converters();
			return $cv->do_db_upgrade($mt[2], "", $q, $errstr);
		}

		return false;
	}

	function db_drop_table($table)
	{
		$this->db_query("DROP TABLE `$table`");
	}

	function db_free_result()
	{
		$this->qID = null;
	}
}

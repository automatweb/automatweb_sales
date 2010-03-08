<?php
// mssql.aw - MSSQL draiver
/*
@classinfo  maintainer=kristo
*/
class mssql
{
	var $dbh; #database handle
	var $db_base; #name of the database
	var $qID; # query ID
	var $errmsg; # where we keep our error messages
	var $rec_count;

	function db_init()
	{
		lc_load('definition');
	}


	////
	// !We need to be able to create multiple connections
	// even better, connections might go to different databases
	function db_connect($server,$base,$username,$password)
	{
		global $DEBUG;
		$this->dbh =  mssql_connect($server,$username,$password);
		if (!$this->dbh)
		{
			echo "Can't connect to database";
			print '<br />';
			print mssql_get_last_message();
			exit;
		};
		if (not(mssql_select_db($base,$this->dbh)))
		{
			echo "Can't connect to database";
			print '<br />';
			print mssql_get_last_message();
			exit;
		};
		$this->db_base = $base;
	}

	function db_query_lim($qt, $limit = 0, $count = 0)
	{
		$sb = substr(trim($qt), 0, 6);
		if (strtolower(trim($sb)) == "select" && ($limit + $count) > 0)
		{
			$qt = "SELECT TOP ".($limit+$count)." ".substr($qt, 7);
			$this->db_query($qt);
			if ($limit > 0)
			{
				$cnt = $limit;
				while ($cnt)
				{
					$cnt--;
					$this->db_next();
				}
			}
		}
		else
		{
			return $this->db_query($qt);
		}
	}

	function db_query($qtext,$errors = true)
	{
		enter_function("mssql::db_query");
		global $DUKE, $INTENSE_DUKE;
		//$qtext = preg_replace("/`([^`]+)`/i","[\${1}]",$qtext);

		if ((aw_ini_get("debug_mode") != 0) && $DUKE)
		{
			print '<pre>';
			print_r(preg_replace("/\t/","",$qtext));
			print '</pre>';
			list($micro,$sec) = split(' ',microtime());
			$ts_s = $sec + $micro;
		};
		if ($INTENSE_DUKE == 1)
		{
			$path = $this->_dbg_backtrace();
			print '<pre>';
			print $path."\n";
			print_r(preg_replace("/\t/","",$qtext));
			print '</pre>';
			list($micro,$sec) = split(' ',microtime());
			$ts_s = $sec + $micro;
		}
		aw_global_set('qcount',aw_global_get('qcount')+1);

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
				$eri->raise_error(ERR_DB_NOTCONNECTED, "I'm not connected to the database, cannot perform the requested query. Please report this to site administratorimmediately", true, false);
			}
		};
		$this->qID = mssql_query($qtext, $this->dbh);
		$this->log_query($qtext);
		if (!$this->qID )
		{
			echo dbg::short_backtrace();
			echo mssql_get_last_message()."<br>";
			echo "sql was ".$qtext." <br>";
			if (!$errors)
			{
				$this->db_last_error = array(
					'error_cmd' => $qtext,
				);
				return false;
			}
			$eri = new class_base;
			$eri->init();
			$eri->raise_error(ERR_DB_QUERY,LC_MYSQL_ERROR_QUERY."\n".$qtext."\n".mysql_errno($this->dbh)."\n".mysql_error($this->dbh),true,false);
		}
		else
		{
			$this->num_rows = @mssql_num_fields($this->qID);
			$this->num_fields = @mssql_num_fields($this->qID);
		};
		$this->rec_count = 0;
		if ( (aw_ini_get("debug_mode") != 0) && $DUKE)
		{
			list($micro,$sec) = split(' ',microtime());
			$ts_e = $sec + $micro;
			$tm = sprintf("%0.04f",$ts_e - $ts_s);
			echo "query took $tm seconds <br />";
		}
		exit_function("mssql::db_query");
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
		};

		array_push($this->qhandles,$this->qID);
	}

	////
	// !restores query handle from internal check
	function restore_handle()
	{
		if (is_array($this->qhandles))
		{
			$this->qID = array_pop($this->qhandles);
		};
	}


	function db_next()
	{
		# this function cannot be called before a query is made
		// don't need numeric indices
		$res = @mssql_fetch_array($this->qID,MSSQL_ASSOC);
		if ($res)
		{
			$this->rec_count++;
		};
		return $res;
	}

	function db_last_insert_id()
	{
		$res = mysql_insert_id($this->dbh);
		return $res;
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
	function db_fetch_field($qtext,$field)
	{
		$this->db_query($qtext);
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
					$arr[$k] = str_replace("'", "''", $arr[$k]); //addslashes($arr[$k]);
				};
			};
			reset($arr);
		}
		else
		{
			$arr = str_replace("'", "''", $arr); //addslashes($arr);
			return $arr;
		};
	}

	function dequote(&$arr)
	{
		if (is_array($arr))
		{
			while(list($k,$v) = each($arr))
			{
				if (is_array($arr[$k]))
				{
					$this->dequote(&$arr[$k]);
				}
				else
				{
					$arr[$k] = str_replace("''", "'", $arr[$k]); //stripslashes($arr[$k]);
				};
			};
			reset($arr);
		}
		else
		{
			$arr = str_replace("''", "'", $arr); //stripslashes($arr);
		};
	}

	function num_rows()
	{
		return $this->num_rows;//($this->qID);
	}

	function db_list_tables()
	{
		$this->db_query("SELECT TABLE_SCHEMA,TABLE_NAME, OBJECTPROPERTY(object_id(TABLE_NAME), N'IsUserTable') AS type 
 FROM INFORMATION_SCHEMA.TABLES");
	}

	function db_next_table()
	{
		$ret = $this->db_next();
		if (!$ret)
		{
			return false;
		}
die(dbg::dump($ret));
		return $ret["name"];
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

		$this->db_query_lim("SELECT syscolumns.name as name FROM sysobjects LEFT JOIN syscolumns ON syscolumns.id = sysobjects.id WHERE sysobjects.name = '".$name."' ");
		while ($row = $this->db_next())
		{
			$_name = $row["name"];
			$type = "";
			$len =  $row["length"];
			$flags = "";
			$ret['fields'][$_name] = array(
				'name' => $_name,
				'length' => $len,
				'type' => $type,
				'flags' => '',
				'default' => $row['cdefault']
			);
		}

		return $ret;
	}

	function db_create_table($name, $field_data, $primary)
	{ //!!! untested!!!
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

			$field_dfns[] = "$field_name $type $default $null";
		}

		$field_dfns = implode(",", $field_dfns);
		$index = count($indexed_fields) ? ", (" . implode(",", $indexed_fields) . ")" : "";
		$q = "CREATE TABLE $name ($field_dfns, PRIMARY KEY($primary)" . $index . ")";
		return $this->db_query($q);
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
			};
		};
		return $tables;
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
		if ($extra == 'AUTO_INCREMENT')
		{
			$extra = 'PRIMARY KEY AUTO_INCREMENT';
		}
		if ($length)
		{
			$len = '('.$length.')';
		}

		// quote column name
		$col_name = explode(".", $name);
		foreach ($col_name as $key => $name_part)
		{
			$col_name[$key] = "[" . $name_part . "]";
		}
		$name = implode(".", $col_name);

		// exec q
		$q = "ALTER TABLE $tbl ADD $name $type $len $null ".($default == '' ? '' : "default '$default'")." $extra ";
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
		$uid = aw_global_get("uid");
		$logfile = "/www/log/mysql-" . $uid . ".log";

		if (isset($GLOBALS["HTTP_COOKIE_VARS"]["log_query"]))
		{
			$fp = fopen($logfile,"a");
			flock($fp, LOCK_EX);
			fwrite($fp,$msg . "\n\n-----------------------------------\n\n");
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}

	function db_table_exists($tbl)
	{
		$q = "DESCRIBE $tbl";
		if (@mysql_query($q, $this->dbh))
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
				if ($bt[$i+1]["class"] != "")
				{
					$fnm = $bt[$i+1]["class"]."::".$bt[$i+1]["function"];
				}
				else
				if ($bt[$i+1]["function"] != "")
				{
					if ($bt[$i+1]["function"] != "include")
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

				/*if ($bt[$i]["class"] != "")
				{
					$fnm2 = $bt[$i]["class"]."::".$bt[$i]["function"];
				}
				else
				if ($bt[$i]["function"] != "")
				{
					$fnm2 = $bt[$i]["function"];
				}
				else
				{
					$fnm2 = "";
				}

				$msg .= $fnm2;*/
			}
		}

		return $msg;
	}

	function db_tnm($table)
	{
		return $table;" [BEETA\strmeed].[$table] $table ";
	}

	function db_tn($table)
	{
		return $table;" [BEETA\strmeed].[$table] ";
	}

	function db_fn($fn)
	{
		if (strpos($fn,".") !== false)
		{
			list($tn, $fn) = explode(".", $fn);
			return "[".$tn."].[".$fn."]";
		}
		else
		{
			return "[".$fn."]";
		}
	}

	function db_get_table_type($tbl)
	{
		$inf = $this->db_fetch_row("SELECT * FROM sysobjects WHERE name = '$tbl'");
		$typestr = trim(strtolower($inf["type"]));
		if ($typestr == "p")
		{
			return DB_TABLE_TYPE_STORED_PROC;
		}
		return DB_TABLE_TYPE_TABLE;
	}
};
?>

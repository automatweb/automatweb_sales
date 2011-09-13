<?php

abstract class aw_db_connector
{
	const DEFAULT_CID_STR = "DBMAIN";

	protected static $open_database_connections = array(); // store for all open connections

	protected $connection_id = self::DEFAULT_CID_STR;
	protected $dbh; // data base connection handle resource


	//deprecated
	var $db_base; #name of the database
	var $qID; # query ID
	var $errmsg; # where we keep our error messages
	var $rec_count;
	protected $dc = array(); // connections used in this instance
	protected $default_cid = "";
	///////////////////

	public function __construct()
	{
	}

	/** All derived classes should call this before using anything. Connects to the default database
		@attrib api=1 params=name

		@param no_db optional type=bool
			If set to true, no database connection will be created

		@comment
			Initializes the class and makes sure that a database connection exists, unless the no_db parameter is given or the aw global no_db_connection is set

	**/
	function init($args = array())
	{ //XXX: kas see on n8. legacy meetod?
		if ((is_array($args) && isset($args["no_db"])) || aw_global_get("no_db_connection"))
		{
			return;
		}

		// if no connection id is set, pretend that this is the primary data source
		if (empty(self::$open_database_connections[$this->connection_id]))
		{
			$this->db_connect();
		}
		else
		{
			$this->dbh = self::$open_database_connections[$this->connection_id];
		}
	}

	/* deprecated - do not use, use init() instead */
	function db_init($args = array()) { $this->init($args); }

	/** Connects to the database
		@attrib api=1 params=name

		@param cid type=string default=aw_db_connector::DEFAULT_CID_STR
			Connection identifier. Loads connection data from aw configuration setting db.connections.$cid.*
			This is the recommended way. Optionally all variables including plain password string can be specified
			Default means the one found in aw.ini defined by db.* variables is loaded

		@param driver type=string default=""
			the type of the SQL driver to use. Required if $cid not given
		@param server type=string default="localhost"
			SQL server location
		@param base type=string default=""
			Database name. Required if $cid not given
		@param username type=string default=""
			Required if $cid not given
		@param password type=string default=""
			Required if $cid not given. No checking performed if empty password given, assumed that other means of authentication used

		@errors
			throws awex_db_connection if connection data not found for $cid
			throws awex_db_driver if specified driver not supported
			throws awex_db_connection_param if connection parameters $driver, $base or $username are not valid

		@returns db connection object

		@comment
			Creates a connection to a data source

		@examples
			$db->db_connect();
	**/
	function db_connect($args = array())
	{
		if (!empty($args["cid"]) or empty($args["driver"]))
		{
			if (empty($args["cid"]))
			{
				$cid = self::DEFAULT_CID_STR;
			}
			elseif (aw_ini_isset("db.connections.{$args["cid"]}"))
			{
				$cid = (string) $args["cid"];
			}
			else
			{
				throw new awex_db_connection("Database connection data identified by '{$args["cid"]}' not found");
			}

			if (self::DEFAULT_CID_STR === $cid)
			{
				$driver = aw_ini_get("db.driver");
			}
			else
			{
				$driver = aw_ini_isset("db.connections.{$cid}.driver") ? aw_ini_get("db.connections.{$cid}.driver") : "mysql";
			}

			if (!in_array($driver, self::$supported_drivers))
			{
				throw new awex_db_driver("Database driver '{$driver}' for cid '{$cid}' not supported");
			}

			$dc = new $driver();
			$dc->db_connect("", "", "", "", $cid);
		}
		else
		{
			if (empty($args["driver"]) or empty($args["base"]) or empty($args["username"]))
			{
				if (isset($args["password"])) unset($args["password"]);
				throw new awex_db_connection_param("Data connection parameters not correctly specified (password removed from dump): " . var_export($args, true));
			}

			$server = empty($args["server"]) ? "localhost" : $args["server"];

			$driver = $args["driver"];
			if (!in_array($driver, self::$supported_drivers))
			{
				throw new awex_db_driver("Database driver '{$driver}' not supported");
			}

			$dc = new $driver();
			$dc->db_connect($server, $args["base"], $args["username"], $args["password"], "");
		}


		self::$open_database_connections[$cid] = $dc;
		$this->dbh = $dc;
		return $dc;
	}

	abstract protected function _get_dsn_string($user, $pw, $db, $server);

	/** Does a db query
		@attrib api=1 params=pos

		@param qtext required type=string
			SQL query

		@param errors optional type=bool default=true
			if you dont want to see errors, then it should be false

		@returns true - if query was successful, else returns DB error

		@examples
			$q = "UPDATE ml_queue SET status = 0 WHERE qid = '$qid'";
			$this->db_query($q);
	**/
	function db_query($qtext,$errors = true)
	{
		$retval = $this->dc[$this->default_cid]->db_query($qtext,$errors);

		if (!$retval)
		{
			$this->db_last_error = $this->dc[$this->default_cid]->db_last_error;
		}

		if (aw_global_get("debug.db_query"))
		{
			echo "<hr><b>QUERYING DATABASE:</b><br />" . nl2br($qtext);
		}

		return $retval;
	}

	/** Does a database query, but limits the result count by the given parameter
		@attrib api=1 params=pos

		@param qtext required type=string
			SQL query

		@param limit required type=int default=0
			Retrieve rows starting limit

		@param count optional type=int default=0
			if > 0 ,Retrieve that many rows

		@returns true - if query was successful, else returns DB error

		@examples
			$per_page = 100;
			$page = 12;
			$q = 'SELECT * FROM '.$db_table.');
			$db->db_query_lim($q, ($page*$per_page),($per_page));
	**/
	function db_query_lim($qtext,$limit,$count = 0)
	{
		$retval = $this->dc[$this->default_cid]->db_query_lim($qtext,$limit, $count);
		if (!$retval)
		{
			$this->db_last_error = $this->dc[$this->default_cid]->db_last_error;
		};
		return $retval;
	}

	/** returns next row of a query result
		@attrib api=1 params=pos

		@returns
			Associative array containing the next row of data from the active query result set

		@examples
			$q = "SELECT * FROM table";
			$data = array();
			$this->db_query($q);
			while($w = $this->db_next())
			{
				$data[] = $w["column"];
			}
	**/
	function db_next()
	{
		return $this->dc[$this->default_cid]->db_next();
	}

	/** Returns the last identifier generated by a sequence in a table
		@attrib api=1

		@examples
			$oid = $this->db_last_insert_id();
	**/
	function db_last_insert_id()
	{
		return $this->dc[$this->default_cid]->db_last_insert_id();
	}

	/** Returns the last update, insert or delete query affected rows
		@attrib api=1
		@examples
			$int_no_of_rows = $this->db_affected_rows();
	**/
	function db_affected_rows()
	{
		return $this->dc[$this->default_cid]->db_affected_rows();
	}

	/** Performs a query and returns the first row of the result set
		@attrib params=pos api=1

		@param sql optional type=string default=""
			SQL query

		@examples
			$row = $this->db_fetch_row("SELECT * FROM my_table WHERE status = "kopp ees");
	**/
	function db_fetch_row($sql = "")
	{
		return $this->dc[$this->default_cid]->db_fetch_row($sql);
	}

	/** Performs a SQL query and returns the value for the given column in the first row of the result
		@attrib params=pos api=1

		@param qtext optional type=string default=""
			QSL query

		@param field optional type=string default=""
			field name

		@examples
			$id = $this->db_fetch_field("SELECT id FROM forms WHERE id = '$row[oid]'", "id");
	**/
	function db_fetch_field($qtext,$field, $errors = true)
	{
		return $this->dc[$this->default_cid]->db_fetch_field($qtext,$field, $errors);
	}

	/** Performs a query and returns all the rows as an array of arrays
		@attrib params=pos api=1

		@param $qtext optional type=string default=""
			SQL query... if not set, tries to fetch from previous db_query

		@examples
			$arr = $this->db_fetch_array('select id , name , parent from users');
	**/
	function db_fetch_array($qtext="")
	{
		return $this->dc[$this->default_cid]->db_fetch_array($qtext);
	}

	/** Quote string or stings in array with slashes
		@attrib params=pos api=1

		@param arr required type=string/array
			string/array of strings , you want to quote

		@returns string/array

		@comment
			Quote string or stings in array with slashes

		@examples
			$str = "a'b";
			$this->quote($str);
			echo $str; // echoes a\'b if the driver is mysql
	**/
	function quote(&$arr)
	{
		return $this->dc[$this->default_cid]->quote($arr);
	}

	/** Removes quote() added quotes from a string
		@attrib params=pos api=1

		@param arr required type=string/array
			string/array of strings , you want to unquote

		@returns string/array

		@examples
			$str = "a'b";
			$this->quote($str);
			$this->unquote($str);
			echo $str; // echoes a'b
	**/
	function dequote(&$arr)
	{
		return $this->dc[$this->default_cid]->dequote($arr);
	}

	/** Returns the number of rows in the last query result
		@attrib api=1

		@returns int , number of rows
	**/
	function num_rows()
	{
		return $this->dc[$this->default_cid]->num_rows();
	}

	/** Lists tables in the database, names can be fetched with db_next_table
		@attrib api=1

		@comment
			Retrieves a list of table names from a database.($this->tID)
			Retrieves the number of rows from a result set ($this->tablecount)

		@examples
			$this->db_list_tables();
			while ($t = $this->db_next_table())
			{
				echo "table = ".dbg::dump($t);
			}
	**/
	function db_list_tables()
	{
		return $this->dc[$this->default_cid]->db_list_tables();
	}

	/** Returns the next table in the query done by db_list_tables
		@attrib api=1

		@returns String , table name
	**/
	function db_next_table()
	{
		return $this->dc[$this->default_cid]->db_next_table();
	}

	/** Retrieves information about a table
		@attrib params=pos api=1

		@param $name required type=string
			table name

		@returns
			array - the properties of table $name or false if it doesn't exist
			properties are returned as array $tablename => $tableprops
			where $tableprops is an array("name" => $table_name, "fields" => $fieldprops)
			where $fieldprops is an array of $fieldname => $cur_props
			where $cur_props is an array("name" => $field_name, "length" => $field_length, "type" => $field_type, "flags" => $field_flags)

		@examples
			CREATE TABLE tbl (id int, content text)
			db_get_table("tbl") returns:
			array("name" => "tbl",
				"fields" => array("id" => array("name" => "id", "length" => 10, "type" => "int", "flags" => ""),
				"content" => array("name" => "content", "length" => "65535", "type" => "text", "flags" => "")
				))
	**/
	function db_get_table($name)
	{
		return $this->dc[$this->default_cid]->db_get_table($name);
	}

	/** Creates a new database table
		@attrib params=pos api=1

		@param $name required type=string
			table name to create

		@param $field_data required type=array
			initial field definitions. Format:
			array(
				"field_name1" => array(
					"type" => "INT",
					"null" => false,
					"default" => 0
				),
				"field_name2" => array(
					"type" => "CHAR",
					"length" => 15,
					"index" => true // whether to index column
				),
			);

		@param $primary required type=string
			primary key field name

		@returns TRUE on success, FALSE on failure

		@examples
			 $this->db_create_table("crm_insurance", $field_data, "oid");
	**/
	function db_create_table($name, $field_data, $primary)
	{
		return $this->dc[$this->default_cid]->db_create_table($name, $field_data, $primary);
	}

	/** Deletes a database table
		@attrib params=pos api=1

		@param $table required type=string
			table name to drop

		@examples
			 $this->db_drop_table("crm_insurance");
	**/
	function db_drop_table($table)
	{
		return $this->dc[$this->default_cid]->db_drop_table($table);
	}

	/** Returns sql for field type with length
		@attrib params=pos api=1

		@param $type required type=string
			type of field you want to qreate

		@param $length required type=int
			length of field you want to create

		@comment
			this returns the sql for creating the field

		@examples
			if driver is mysql
			$str = $this->mk_field_len(varchar , 100);
			($str = "VARCHAR(100)")
	**/
	function mk_field_len($type,$length)
	{
		return $this->dc[$this->default_cid]->mk_field_len($type,$length);
	}

	/** Reads and returns the structure of the database
		@attrib api=1

		@returns
			array { table_name => array { field_name => array{ type => field type, flags => array { field extra data }, key => if field has a key, key name } } }
	**/
	function db_get_struct()
	{
		return $this->dc[$this->default_cid]->db_get_struct();
	}

	/** saves query handle in the internal stack
		@attrib api=1

		@comment
			The idea behind save_handle/restore_handle is, that you can iterate over the results of another query, while you are iterating over the results of some other query.
			it's your task to make sure you call those functions in correct
			order, otherwise weird things could happen

		@examples
			$this->db_query("SELECT * FROM foo");
			while ($row = $this->db_next())
			{
				$this->save_handle();	// if you didn't have these here, then the loop would only get done once, because the update query would replace the internal result set iterator
				$this->db_query("UPDATE foo SET b=1 WHERE c = $row[a]");
				$this->restore_handle();
			}
	**/
	function save_handle()
	{
		return $this->dc[$this->default_cid]->save_handle();
	}

	/** restores query handle from internal stack
		@attrib api=1

		@examples
			${save_handle}
	**/
	function restore_handle()
	{
		return $this->dc[$this->default_cid]->restore_handle();
	}

	/** fetch record from table
		@attrib params=pos api=1

		@param table required type=string
			table name

		@param field required type=string
			field name - the one you want to check

		@param selector required type=string
			field value - value , you are looking for

		@param fields optional type=array default="*"
			fields you want to see in query result

		@returns
			array containing the requested fields or all fields in the table

	**/
	function get_record($table,$field,$selector,$fields = array())
	{
		if (sizeof($fields) > 0)
		{
			$fields = join(",",$fields);
		}
		else
		{
			$fields = "*";
		};
		$q = "SELECT $fields FROM $table WHERE $field = '$selector'";
		$this->db_query($q);
		return $this->db_fetch_row();
	}

	/** selects a list of databases from the server, the list can be retrieved by calling db_next_database()
		@attrib api=1

		@examples
			$dbi->db_list_databases();
			while ($db = $dbi->db_next_database())
			{
				echo $db['name']
			}
	**/
	function db_list_databases()
	{
		return $this->dc[$this->default_cid]->db_list_databases();
	}

	/** Retrieves the next database in the current list
		@attrib api=1

		@returns
			the next database from the list created by db_list_databases()
			array {name => database name }

		@examples ${db_list_databases}
	**/
	function db_next_database()
	{
		return $this->dc[$this->default_cid]->db_next_database();
	}

	/** createa a database in the server
		@attrib params=name api=1

		@param name required type=string
			the name of the database

		@param user required type=string
			the user that will get access to the database

		@param host required type=string
			the host from where the access will be granted

		@param pass required type=string
			the password for the database

	**/
	function db_create_database($arr)
	{
		return $this->dc[$this->default_cid]->db_create_database($arr);
	}

	/** returns a list of all available database drivers on the system
		@attrib api=1

		@returns array(driver_class => driver_class)

		@examples
			$args['prop']['options'] = $this->list_db_drivers();
	**/
	function list_db_drivers()
	{
		$ret = array();
		if ($dir = @opendir($this->cfg["classdir"]."/db_drivers"))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (substr($file, strlen($file) - (strlen($this->cfg["ext"])+1)) == ".".$this->cfg["ext"])
				{
					$cln = basename($file,".".$this->cfg["ext"]);
					$ret[$cln] = $cln;
				}
			}
			closedir($dir);
		}
		asort($ret);
		return $ret;
	}

	/** returns server status - the fields returned are server-specific
		@attrib api=1

		@returns
			array('Server_version' => .. , 'Protocol_version' => .. , 'Host_info' => .. , Variables => values , 'Queries_per_sec' => ..)

		@examples
			$stat = $server->db_server_status();
	**/
	function db_server_status()
	{
		return $this->dc[$this->default_cid]->db_server_status();
	}

	/** returns information about the specified table - the fields returned are server specific
		@attrib params=pos api=1

		@param tbl required type=string
			the name of the table

		@returns array of status fields
	**/
	function db_get_table_info($tbl)
	{
		return $this->dc[$this->default_cid]->db_get_table_info($tbl);
	}

	/** Returns a list of database specific flags that you can assign to a field
		@attrib api=1

		@returns
			array('' => '', 'AUTO_INCREMENT' => 'AUTO_INCREMENT');

	**/
	function db_list_flags()
	{
		return $this->dc[$this->default_cid]->db_list_flags();
	}

	/** Returns a database-specific list of field types
		@attrib api=1

		@returns array(type_name => type_name)

	**/
	function db_list_field_types()
	{
		return $this->dc[$this->default_cid]->db_list_field_types();
	}

	/** adds a column to table $tbl
		@attrib params=pos api=1

		@param tbl required type=string
			the table to add to

		@param coldat required type=array
			coldat - new column properties array(name, type, length, null, default, extra)

	**/
	function db_add_col($tbl,$coldat)
	{
		return $this->dc[$this->default_cid]->db_add_col($tbl,$coldat);
	}

	/** change a column in table
		@attrib params=pos api=1

		@param tbl required type=string
			the table where the column is

		@param col required type=string
			the column to change

		@param newdat required type=array
			new column properties array(name, type, length, null, default, extra)

	**/
	function db_change_col($tbl, $col, $newdat)
	{
		return $this->dc[$this->default_cid]->db_change_col($tbl, $col, $newdat);
	}

	/** drops column $col from table $tbl
		@attrib params=pos api=1

		@param tbl required type=string
			the table where the column is

		@param col required type=string
			the column to drop
	**/
	function db_drop_col($tbl,$col)
	{
		return $this->dc[$this->default_cid]->db_drop_col($tbl,$col);
	}

	/** lists indexes for table $tbl, you can retriev the list by calling db_nect_index
		@attrib params=pos api=1

		@param tbl required type=string
			the table name

	**/
	function db_list_indexes($tbl)
	{
		return $this->dc[$this->default_cid]->db_list_indexes($tbl);
	}

	/** fetches next index from list created by db_list_indexes
		@attrib api=1

		@returns
			array -
				index_name - the name of the index
				col_name - the name of the column that the index is created on
				unique - if true, values in index must be unique
	**/
	function db_next_index()
	{
		return $this->dc[$this->default_cid]->db_next_index();
	}

	/** adds an index to table $tbl
		@attrib params=pos api=1

		@param tbl required type=string
			the table name

		@param $idx_dat required type=array
			an array that defines index properties -
				name - the name of the index
				col - the column on what to create the index
	**/
	function db_add_index($tbl, $idx_dat)
	{
		return $this->dc[$this->default_cid]->db_add_index($tbl, $idx_dat);
	}

	/** drops index $name from table $tbl
		@attrib params=pos api=1

		@param tbl required type=string
			the table name

		@param name required type=string
			index name
	**/
	function db_drop_index($tbl, $name)
	{
		return $this->dc[$this->default_cid]->db_drop_index($tbl, $name);
	}

	/** returns last occurred error
		@attrib api=1

		@returns false - if no error has occurred
			otherwise - array -
				error_cmd - the query that produced the error
				error_code - the db-specific error code
				error_string - the error string returned by the database
	**/
	function db_get_last_error()
	{
		return $this->dc[$this->default_cid]->db_get_last_error();
	}

	/** checks if the table exists
		@attrib params=pos api=1

		@param tbl required type=string
			the table name

		@returns
			true if the specified table exists in the database, false if not
	**/
	function db_table_exists($tbl)
	{
		return $this->dc[$this->default_cid]->db_table_exists($tbl);
	}

	/** escapes a database table field name based on the db driver
		@attrib params=pos api=1

		@param fn required type=string
			database table field name

		@returns string

		@examples
			 $q = "SELECT ".$this->db_fn("objects.oid")." AS id,".$this->db_fn("objects.brother_of").",".$this->db_fn("objects.name").",".$this->db_fn("planner.start").",".$this->db_fn("planner.end")....
	**/
	function db_fn($fn)
	{
		return $this->dc[$this->default_cid]->db_fn($fn);
	}

	/** checks if the table given is a stored procedure or a real table
		@attrib params=pos api=1

		@param tbl required type=string
			the table name

		@returns type of the table, as one of the DB_TABLE_TYPE constants
	**/
	function db_get_table_type($tbl)
	{
		return $this->dc[$this->default_cid]->db_get_table_type($tbl);
	}

	function db_free_result()
	{
		$this->dc[$this->default_cid]->db_free_result();
	}
}

/** Generic database provider error **/
class awex_db extends aw_exception {}

/** Database connection error **/
class awex_db_connection extends awex_db {}

/** Database connection parameters error **/
class awex_db_connection_param extends awex_db_connection {}

/** Database driver error **/
class awex_db_driver extends awex_db {}

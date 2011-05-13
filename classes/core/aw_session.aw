<?php

class aw_session
{
	var $ses_table = "aw_sessions";

	function _open($path, $name)
	{
		$db = $GLOBALS["cfg"]["db"];

		$mysql_connect = @mysql_pconnect ($db['host'], $db['user'], $db['pass']);
		$mysql_db = @mysql_select_db ($db['base']);

		if (!$mysql_connect || !$mysql_db)
		{
            		return FALSE;
		}
		else
		{
            		return TRUE;
		}
	}

	function _close()
	{
		/* This is used for a manual call of the
		session gc function */
		$this->_gc(0);
		return TRUE;
	}

	function _read($ses_id)
	{
		$session_sql = "SELECT * FROM " . $this->ses_table . " WHERE session_id = '$ses_id'";
		$session_res = @mysql_query($session_sql);
		if (!$session_res) {
			return '';
		}

		$session_num = @mysql_num_rows ($session_res);
		if ($session_num > 0) {
			$session_row = mysql_fetch_assoc ($session_res);
			$ses_data = $session_row["session_data"];
			return $ses_data;
		} else {
			return '';
		}
        }

	function _write($ses_id, $data)
	{
		$uid = aw_global_get("uid");
		$host = aw_global_get("REMOTE_ADDR");
		$session_sql = "UPDATE " . $this->ses_table
		     . " SET session_time='" . time()
		     . "', session_data='$data',host='$host',uid='$uid' WHERE session_id='$ses_id'";
		$unser = unserialize($data);


		$session_res = @mysql_query ($session_sql);
		if (!$session_res) {
			return FALSE;
		}
		if (mysql_affected_rows ()) {
		return TRUE;
		}

		// possible features:
		// 1 - limit concurrent logins
		// 2 - track every possible URL
		// 3 - limit login time
		// 4 - log user out
		// 5 - whatever else you can think of

		$session_sql = "INSERT INTO " . $this->ses_table
			     . " (session_id, session_time, session_start, session_data,host,uid)"
			     . " VALUES ('$ses_id', '" . time()
			     . "', '" . time() . "', '$data','$host','$uid')";
		$session_res = @mysql_query ($session_sql);
		if (!$session_res) {
		    return FALSE;
		}         else {
		    return TRUE;
		}
    	}

	function _destroy($ses_id) {
		$session_sql = "DELETE FROM " . $this->ses_table
		     . " WHERE session_id = '$ses_id'";
		$session_res = @mysql_query ($session_sql);
		if (!$session_res) {
		return FALSE;
		}         else {
            	return TRUE;
        	}
    	}

	function _gc($life)
	{
		$ses_life = strtotime("-5 minutes");

		$session_sql = "DELETE FROM " . $this->ses_table
		     . " WHERE session_time < $ses_life";
		$session_res = @mysql_query ($session_sql);


		if (!$session_res) {
		    return FALSE;
		}         else {
		    return TRUE;
		}
    }

	/**
		@attrib api=1 params=pos
		@param name type=string
			Variable name to set
		@param value type=mixed
			Value to assign. Array or scalar. Scalar values are converted to strings.
		@comment
		@returns void
		@errors
	**/
	public static function set($name, $value)
	{
		aw_session_set($name, $value);
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
			Variable name whose value to get
		@comment
		@returns mixed
		@errors
	**/
	public static function get($name)
	{
		return aw_global_get($name);
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
			Variable name to delete
		@comment
		@returns void
		@errors
	**/
	public static function unset($name)
	{
		aw_session_del($name);
	}
}

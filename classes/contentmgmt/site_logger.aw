<?php

class site_logger extends core
{
	function site_logger()
	{
		$this->init();
		aw_config_init_class($this);
	}

	////
	// !writes a pageview event to the aw log
	function add($arr)
	{
		if (aw_ini_get("syslog.log_pageviews") != 1)
		{
			return false;
		}

		$this->_log(
			16 /*ST_MENUEDIT*/,
			19 /*SA_PAGEVIEW*/,
			$this->get_log_message(),
			aw_global_get("section")
		);
	}

	function get_log_message()
	{
		$sec_id = aw_global_get("section");
		if (!$sec_id)
		{
			$sec_id = aw_ini_get("frontpage");
		}

		if (is_oid($sec_id) && $this->can("view", $sec_id))
		{
			$sec_o = obj($sec_id);
			$path_str = $sec_o->path_str();
		}

		// now also, if we are in some fg incremental search,
		// log the "address" of that as well.
		if (!empty($GLOBALS["tbl_sk"]))
		{
			$names = array();
			$tbld = aw_global_get("fg_table_sessions");
			$ar = new aw_array($tbld[$GLOBALS["tbl_sk"]]);
			foreach($ar->get() as $url)
			{
				preg_match("/restrict_search_val=([^&$]*)/",$url,$mt);
				$names[] = urldecode($mt[1]);
			}
			$path_str .= "/".join("/",$names);
		}

		// evil e-mail link tracking code
		global $artid,$sid,$mlxuid;
		if ($artid)	// tyyp tuli meilist, vaja kirja panna
		{
			if (is_numeric($artid))
			{
				$sid = (int)$sid;
				$ml_msg = $this->db_fetch_row("SELECT * FROM ml_mails WHERE id = '$sid'");

				$this->db_query("SELECT ml_users.*,objects.name as name FROM ml_users LEFT JOIN objects ON objects.oid = ml_users.id WHERE id = '$artid'");
				if (($ml_user = $this->db_next()))
				{
					$msg = $ml_user["name"]." (".$ml_user["mail"].") tuli lehele $path_str meilist ".$ml_msg["subj"];

					// and also remember the guy
					// set a cookie, that expires in 3 years
					setcookie("mlxuid",$artid,time()+3600*24*1000,"/");
				}
			}
		}
		else
		if ($mlxuid)
		{
			$this->db_query("SELECT ml_users.*,objects.name as name FROM ml_users LEFT JOIN objects ON objects.oid = ml_users.id WHERE id = '$mlxuid'");
			if (($ml_user = $this->db_next()))
			{
				$msg = $ml_user["name"]." (".$ml_user["mail"].") vaatas lehte $path_str";
			}
		}
		else
		if(!empty($_REQUEST["t"]))
		{
			$q = "UPDATE ml_sent_mails SET vars = '1' WHERE vars = '".$_REQUEST["t"]."'";
			$this->db_query($q);
		}
		else
		{
			$msg = $path_str;
		}
		return $msg;
	}
}

<?php
// syslog.aw - syslog management
// syslogi vaatamine ja analüüs

class db_syslog extends aw_template
{
	function db_syslog()
	{
		$this->init("syslog");
		lc_load("definition");
		$this->lc_load("syslog","lc_syslog");
		$this->syslog_site_id = (aw_global_get("syslog_site_id")) ? aw_global_get("syslog_site_id") : $this->cfg["site_id"];
	}

	function display_sites()
	{
		$this->read_adm_template("sites.tpl");
		$old = aw_unserialize($this->get_cval("syslog_sites"));
		$c = "";
		$q = "SELECT distinct(site_id) FROM syslog";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			if ($row["site_id"] > 0)
			{
				$this->vars(array(
					"id" => $row["site_id"],
					"name" => $old[$row["site_id"]],
					"active" => checked($row["site_id"] == $this->syslog_site_id),
				));
				$c .= $this->parse("line");
			};
		};
		$this->vars(array(
			"active" => checked(-1 == $this->syslog_site_id),
		));
		$c .= $this->parse("line");
		$this->vars(array(
			"line" => $c,
			"reforb" => $this->mk_reforb("save_sites")
		));
		$retval = $this->parse();
		return $retval;
	}

	function save_sites($args = array())
	{
		$names = $args["name"];
		$name_ser = aw_serialize($names,SERIALIZE_NATIVE);
		$conf = get_instance("config");
		$conf->set_simple_config("syslog_sites",$name_ser);
		global $syslog_site_id;
		$syslog_site_id = ($args["syslog_site_id"]) ? $args["syslog_site_id"] : $this->cfg["site_id"];
		session_register("syslog_site_id");
		return $this->mk_my_orb("site_id", array(), "syslog", false,true);
	}


};

class syslog extends db_syslog
{
	function syslog()
	{
		$this->db_syslog();
	}

	/**

		@attrib name=block params=name default="0"


		@returns


		@comment

	**/
	function block($arr)
	{
		extract($arr);
		$this->read_adm_template("block.tpl");
		$old = utf_unserialize($this->get_cval("blockedip"));
		$c = "";
		while(list($k,$v) = each($old))
		{
			$this->vars(array(
				"ip" => $v,
				"id" => $k,
				"checked" => "checked",
			));
			$c .= $this->parse("line");
		};
		$this->vars(array(
			"line" => $c,
			"reforb" => $this->mk_reforb("saveblock")
		));
		return $this->parse();
	}

	/**

		@attrib name=saveblock params=name default="0"


		@returns


		@comment

	**/
	function saveblock($arr)
	{
		extract($arr);
		$old = utf_unserialize($this->get_cval("blockedip"));
		$store = array();
		if (is_array($check))
		{
			while(list($k,$v) = each($check))
			{
				$store[] = $old[$k];
			};
		};
		if (inet::is_ip($new))
		{
			$store[] = $new;
		};
		$old_s = serialize($store);
		$this->quote($old_s);
		$q = "UPDATE config SET content = '$old_s' WHERE ckey = 'blockedip'";
		$this->db_query($q);
		return $this->mk_my_orb("block",array(),"syslog",false,true);
	}


	/**

		@attrib name=convert_syslog params=name default="0"


		@returns


		@comment

	**/
	function convert_syslog()
	{
		// fills the site_id field in syslog table
		$q = "SELECT oid FROM syslog GROUP BY (oid)";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->save_handle();
			$q = "SELECT site_id FROM objects WHERE oid = '$row[oid]'";
			$this->db_query($q);
			$row2 = $this->db_next();
			$site_id = (int)$site_id;
			// just in case, avoid writing to the table while we are reading
			// from it.
			$ids[$row["oid"]] = (int)$row2["site_id"];
			$this->restore_handle();
		};

		if (is_array($ids))
		{
			foreach($ids as $key => $val)
			{
				$q = "UPDATE syslog SET site_id = '$val' WHERE oid = '$key'";
				print $q;
				print "<br />";
			};
		};
	}
}

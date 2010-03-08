<?php
/*
@classinfo  maintainer=kristo
*/

class stats_model extends core
{
	function stats_model()
	{
		$this->init();
		classload("core/date/date_calc");
		if (!is_dir(aw_ini_get("stats_model.folder")))
		{
			mkdir(aw_ini_get("stats_model.folder"));
			chmod(aw_ini_get("stats_model.folder"), 0777);
		}

		$this->countries = array(
			"AD" => "Andorra",
			"AE" => "United Arab Emirates",
			"AF" => "Afghanistan",
			"AG" => "Antigua and Barbuda",
			"AI" => "Anguilla",
			"AL" => "Albania",
			"AM" => "Armenia",
			"AN" => "Netherlands Antilles",
			"AO" => "Angola",
			"AQ" => "Antarctica",
			"AR" => "Argentina",
			"AS" => "American Samoa",
			"AT" => "Austria",
			"AU" => "Australia",
			"AW" => "Aruba",
			"AZ" => "Azerbaijan",
			"BA" => "Bosnia and Herzegovina",
			"BB" => "Barbados",
			"BD" => "Bangladesh",
			"BE" => "Belgium",
			"BF" => "Burkina Faso",
			"BG" => "Bulgaria",
			"BH" => "Bahrain",
			"BI" => "Burundi",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BN" => "Brunei Darussalam",
			"BO" => "Bolivia",
			"BR" => "Brazil",
			"BS" => "Bahamas",
			"BT" => "Bhutan",
			"BV" => "Bouvet Island",
			"BW" => "Botswana",
			"BY" => "Belarus",
			"BZ" => "Belize",
			"CA" => "Canada",
			"CC" => "Cocos (Keeling) Islands",
			"CF" => "Central African Republic",
			"CG" => "Congo",
			"CH" => "Switzerland",
			"CI" => "Cote D'Ivoire (Ivory Coast)",
			"CK" => "Cook Islands",
			"CL" => "Chile",
			"CM" => "Cameroon",
			"CN" => "China",
			"CO" => "Colombia",
			"CR" => "Costa Rica",
			"CS" => "Czechoslovakia (former)",
			"CU" => "Cuba",
			"CV" => "Cape Verde",
			"CX" => "Christmas Island",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DE" => "Germany",
			"DJ" => "Djibouti",
			"DK" => "Denmark",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"DZ" => "Algeria",
			"EC" => "Ecuador",
			"EE" => "Estonia",
			"EG" => "Egypt",
			"EH" => "Western Sahara",
			"ER" => "Eritrea",
			"ES" => "Spain",
			"ET" => "Ethiopia",
			"FI" => "Finland",
			"FJ" => "Fiji",
			"FK" => "Falkland Islands (Malvinas)",
			"FM" => "Micronesia",
			"FO" => "Faroe Islands",
			"FR" => "France",
			"FX" => "France, Metropolitan",
			"GA" => "Gabon",
			"GB" => "Great Britain (UK)",
			"GD" => "Grenada",
			"GE" => "Georgia",
			"GF" => "French Guiana",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GL" => "Greenland",
			"GM" => "Gambia",
			"GN" => "Guinea",
			"GP" => "Guadeloupe",
			"GQ" => "Equatorial Guinea",
			"GR" => "Greece",
			"GS" => "S. Georgia and S. Sandwich Isls.",
			"GT" => "Guatemala",
			"GU" => "Guam",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HK" => "Hong Kong",
			"HM" => "Heard and McDonald Islands",
			"HN" => "Honduras",
			"HR" => "Croatia (Hrvatska)",
			"HT" => "Haiti",
			"HU" => "Hungary",
			"ID" => "Indonesia",
			"IE" => "Ireland",
			"IL" => "Israel",
			"IN" => "India",
			"IO" => "British Indian Ocean Territory",
			"IQ" => "Iraq",
			"IR" => "Iran",
			"IS" => "Iceland",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JO" => "Jordan",
			"JP" => "Japan",
			"KE" => "Kenya",
			"KG" => "Kyrgyzstan",
			"KH" => "Cambodia",
			"KI" => "Kiribati",
			"KM" => "Comoros",
			"KN" => "Saint Kitts and Nevis",
			"KP" => "Korea (North)",
			"KR" => "Korea (South)",
			"KW" => "Kuwait",
			"KY" => "Cayman Islands",
			"KZ" => "Kazakhstan",
			"LA" => "Laos",
			"LB" => "Lebanon",
			"LC" => "Saint Lucia",
			"LI" => "Liechtenstein",
			"LK" => "Sri Lanka",
			"LR" => "Liberia",
			"LS" => "Lesotho",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"LV" => "Latvia",
			"LY" => "Libya",
			"MA" => "Morocco",
			"MC" => "Monaco",
			"MD" => "Moldova",
			"MG" => "Madagascar",
			"MH" => "Marshall Islands",
			"MK" => "Macedonia",
			"ML" => "Mali",
			"MM" => "Myanmar",
			"MN" => "Mongolia",
			"MO" => "Macau",
			"MP" => "Northern Mariana Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MS" => "Montserrat",
			"MT" => "Malta",
			"MU" => "Mauritius",
			"MV" => "Maldives",
			"MW" => "Malawi",
			"MX" => "Mexico",
			"MY" => "Malaysia",
			"MZ" => "Mozambique",
			"NA" => "Namibia",
			"NC" => "New Caledonia",
			"NE" => "Niger",
			"NF" => "Norfolk Island",
			"NG" => "Nigeria",
			"NI" => "Nicaragua",
			"NL" => "Netherlands",
			"NO" => "Norway",
			"NP" => "Nepal",
			"NR" => "Nauru",
			"NT" => "Neutral Zone",
			"NU" => "Niue",
			"NZ" => "New Zealand (Aotearoa)",
			"OM" => "Oman",
			"PA" => "Panama",
			"PE" => "Peru",
			"PF" => "French Polynesia",
			"PG" => "Papua New Guinea",
			"PH" => "Philippines",
			"PK" => "Pakistan",
			"PL" => "Poland",
			"PM" => "St. Pierre and Miquelon",
			"PN" => "Pitcairn",
			"PR" => "Puerto Rico",
			"PT" => "Portugal",
			"PW" => "Palau",
			"PY" => "Paraguay",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"SA" => "Saudi Arabia",
			"Sb" => "Solomon Islands",
			"SC" => "Seychelles",
			"SD" => "Sudan",
			"SE" => "Sweden",
			"SG" => "Singapore",
			"SH" => "St. Helena",
			"SI" => "Slovenia",
			"SJ" => "Svalbard and Jan Mayen Islands",
			"SK" => "Slovak Republic",
			"SL" => "Sierra Leone",
			"SM" => "San Marino",
			"SN" => "Senegal",
			"SO" => "Somalia",
			"SR" => "Suriname",
			"ST" => "Sao Tome and Principe",
			"SU" => "USSR (former)",
			"SV" => "El Salvador",
			"SY" => "Syria",
			"SZ" => "Swaziland",
			"TC" => "Turks and Caicos Islands",
			"TD" => "Chad",
			"TF" => "French Southern Territories",
			"TG" => "Togo",
			"TH" => "Thailand",
			"TJ" => "Tajikistan",
			"TK" => "Tokelau",
			"TM" => "Turkmenistan",
			"TN" => "Tunisia",
			"TO" => "Tonga",
			"TP" => "East Timor",
			"TR" => "Turkey",
			"TT" => "Trinidad and Tobago",
			"TV" => "Tuvalu",
			"TW" => "Taiwan",
			"TZ" => "Tanzania",
			"UA" => "Ukraine",
			"UG" => "Uganda",
			"UK" => "United Kingdom",
			"UM" => "US Minor Outlying Islands",
			"US" => "United States",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VA" => "Vatican City State (Holy See)",
			"VC" => "Saint Vincent and the Grenadines",
			"VE" => "Venezuela",
			"VG" => "Virgin Islands (British)",
			"VI" => "Virgin Islands (U.S.)",
			"VN" => "Viet Nam",
			"VU" => "Vanuatu",
			"WF" => "Wallis and Futuna Islands",
			"WS" => "Samoa",
			"YE" => "Yemen",
			"YT" => "Mayotte",
			"YU" => "Yugoslavia",
			"ZA" => "South Africa",
			"ZM" => "Zambia",
			"ZR" => "Zaire",
			"ZW" => "Zimbabwe",
			"COM" => "US Commercial",
			"EDU" => "US Educational",
			"GOV" => "US Government",
			"INT" => "International",
			"MIL" => "US Military",
			"NET" => "Network",
			"ORG" => "Non-Profit Organization",
			"ARPA" => "Old style Arpanet",
			"NATO" => "Nato field"
		);
	}

	/** Returns info on available statistics periods
		@attrib api=1 params=name

		@returns 
			Array of data about periods. array contains:
				index is period identification
				array(
					"from" => timestamp for start of period
					"to" => timestamp for end of period
					"status" => either "online" or "archived"
				)

	**/
	function get_available_periods()
	{
		$stat = $this->_init_status();
		$statuses = $stat->meta("statuses");
		$fs = $this->get_directory(array("dir" => aw_ini_get("stats_model.folder")));
		$ret = array();

		$ret["__current"] = array(
			"from" => get_month_start(),
			"to" => get_day_start(),
			"status" => "online"
		);
		foreach($fs as $fn)
		{
			$bfn = basename($fn, ".sql.gz");
			list($from_d, $to_d) = explode("_", $bfn);
			list($f_d, $f_m, $f_y) = explode(".", $from_d);
			list($t_d, $t_m, $t_y) = explode(".", $to_d);
			$from = mktime(0,0,0, $f_m, $f_d, $f_y);
			$to = mktime(0,0,0, $t_m, $t_d, $t_y);

			$ret[$from] = array(
				"from" => $from,
				"to" => $to,
				"status" => $statuses[$from] == "" ? "archived" : $statuses[$from]
			);
		}
		return $ret;
	}

	/** Unpack the given period to the database from archive
		@attrib api=1 params=name

		@param period_id required type=string
			The id of the period to unpack 

		@returns 
			none
	**/
	function bring_period_online($arr)
	{
		if (empty($arr["period_id"]))
		{
			error::raise(array(
				"id" => "ERR_NO_PER_ID",
				"msg" => t("stats_model::bring_period_online(): no period if given!"),
			));
		}

		chdir(aw_ini_get("stats_model.folder"));
		$fs = glob(aw_ini_get("stats_model.folder")."/".date("d.m.Y", $arr["period_id"])."*");
		$fn = reset($fs);
		$f2 = aw_ini_get("stats_model.folder")."/".basename($fn, ".sql.gz").".sql";

		echo "fn = $fn<br>f2 = $f2 <br>";
		// unpack file from gz to sql
		$cmd = aw_ini_get("server.gunzip_path")." ".$fn;
echo "cmd = $cmd <br>";
		$res = `$cmd`;
		// import sql
		$cmd = aw_ini_get("server.mysql_path")." -u ".aw_ini_get("db.user")." -h ".aw_ini_get("db.host")." --password=".aw_ini_get("db.pass")." ".aw_ini_get("db.base")." < ".$f2;
echo "cmd2 = $cmd <br>";
		$res = `$cmd`;

		$cmd = aw_ini_get("server.gzip_path")." ".$f2;
		$res = `$cmd`;

		$stat = $this->_init_status();
		$statuses = $stat->meta("statuses");
		$statuses[$arr["period_id"]] = "online";
		$stat->set_meta("statuses", $statuses);
		$stat->save();
	}

	/** Deletes period that was brought online from the archive
		@attrib api=1 params=name

		@param period_id required type=string
			The id of the period to delete from the database
	**/
	function delete_online_period($arr)
	{
		if (empty($arr["period_id"]))
		{
			error::raise(array(
				"id" => "ERR_NO_PER_ID",
				"msg" => t("stats_model::bring_period_online(): no period if given!"),
			));
		}

		// find the period file and get the end date
		chdir(aw_ini_get("stats_model.folder"));
		$fs = glob(aw_ini_get("stats_model.folder")."/".date("d.m.Y", $arr["period_id"])."*");
		$fn = reset($fs);

		$bfn = basename($fn, ".sql.gz");
		list($from_d, $to_d) = explode("_", $bfn);
		list($f_y, $f_m, $f_d) = explode(".", $from_d);
		list($t_y, $t_m, $t_d) = explode(".", $to_d);

		$from = mktime(0,0,0, $f_m, $f_d, $f_y);
		$to = mktime(0,0,0, $t_m, $t_d, $t_y);

		// delete records from the database
		$this->db_query("DELETE FROM syslog_archive WHERE tm >= $from AND tm <= $to");

		$stat = $this->_init_status();
		$statuses = $stat->meta("statuses");
		$statuses[$arr["period_id"]] = "archived";
		$stat->set_meta("statuses", $statuses);
		$stat->save();
	}

	function _init_status()
	{
		$ol = new object_list(array(
			"class_id" => CL_STATS_ARCH_STATUS,
			"lang_id" => array(),
			"site_id" => array()
		));
		if (!$ol->count())
		{
			$o = obj();
			$o->set_class_id(CL_STATS_ARCH_STATUS);
			$o->set_name(t("Syslogi arhiivi staatus"));
			$o->set_parent(aw_ini_get("amenustart"));
			aw_disable_acl();
			$o->save();
			aw_restore_acl();
			return $o;
		}
		return $ol->begin();
	}

	function delete_all_online_periods()
	{
		// delete all periods that have been brought online during the day
		$stat = $this->_init_status();
		$statuses = $stat->meta("statuses");
		foreach($statuses as $per_id => $state)
		{
			if ($state == "online" && $per_id != "__current")
			{
				echo "deleting online period $per_id <br>\n";
				flush();
				$this->delete_online_period(array("period_id" => $per_id));
			}
		}
	}

	/**
		@attrib name=nightly_move nologin=1
	**/
	function nightly_move()
	{
die();
		$inp = $this->get_cval("night_moves_in_progress");
		if ((time() - $inp) < 3600*10)
		{
			die("already in progress");
		}
		$this->set_cval("night_moves_in_progress", time());
		$this->delete_all_online_periods();

		echo "moving data from syuslog to _archive <Br>\n";
		flush();

		$to = get_day_start();
		$from = $to-24*3600;

                // select all entries from syslog that are already in archive and delete those
                $this->db_query("SELECT id FROM syslog");
                $sys_ids = array();
                while ($row = $this->db_next())
                {
                        $sys_ids[$row["id"]] = $row["id"];
                }
echo "got ".count($sys_ids)." ids from syslog <br>\n";
flush();

                $ex_ids = array();
                $awa = new aw_array($sys_ids);
                $this->db_query("SELECT id FROM syslog_archive WHERE id IN (".$awa->to_sql().")");
                while ($row = $this->db_next())
                {
                        $ex_ids[$row["id"]] = $row["id"];
                }
echo "from those, ".count($ex_ids)." are already in the archive <br>\n";
flush();
                if (count($ex_ids))
                {
                        $awa = new aw_array($ex_ids);
echo "so we will delete those now. <br>";
                        $this->db_query("DELETE FROM syslog WHERE id IN (".$awa->to_sql().")");
                }

		$q = "INSERT INTO syslog_archive(id,tm,uid,type,action,ip,oid,site_id,act_id,referer,lang_id,object_name,mail_id,session_id) SELECT id,tm,uid,type,action,ip,oid,site_id,act_id,referer,lang_id,object_name,mail_id,session_id FROM syslog ";
		echo $q." <br>";
		$this->db_query($q);
		$q = "DELETE FROM syslog ";
		echo $q." <br>";
		$this->db_query($q);

		echo "resolving ips and countries <br>\n";
		flush();
		// resolve ip adresses
		$this->db_query("SELECT distinct(ip) as ip FROM syslog_archive WHERE ip_resolved is null or ip_resolved = '' and ip != ''");
		while ($row = $this->db_next())
		{
			$adr = gethostbyaddr($row["ip"]);
			$this->quote(&$adr);
			$this->save_handle();
			// get country from resolved ip adr
			$bits = array_reverse(explode(".", $adr));
                        $tld = strtoupper(reset($bits));
                        $country = $this->countries[$tld];

			$this->quote(&$country);
			$this->db_query("UPDATE syslog_archive SET ip_resolved = '$adr', country = '$country' WHERE ip = '$row[ip]'");
			echo $row["ip"]." => ".$adr." <br>\n";
			flush();
			$this->restore_handle();
		}

		//create hour/week/dayofweek numbers, 
		echo "creating day/week/month/dayofweek/year entries<Br>\n";
		flush();
		$this->db_query("UPDATE syslog_archive SET 
			created_hour = HOUR(FROM_UNIXTIME(tm)), 
			created_day = DAYOFMONTH(FROM_UNIXTIME(tm)),
			created_week = WEEK(FROM_UNIXTIME(tm)),
			created_month = MONTH(FROM_UNIXTIME(tm)),
			created_year = YEAR(FROM_UNIXTIME(tm)),
			created_wd = DAYOFWEEK(FROM_UNIXTIME(tm))

			WHERE created_hour IS NULL OR 
				created_day IS NULL OR
				created_week IS NULL OR
				created_month IS NULL OR
				created_year IS NULL OR
				created_wd IS NULL
		");

		// write group membership
		echo "writing group memberships <br>\n";
		flush();
		$gm = array();
		$this->db_query("SELECT uid,groups.oid as oid ,groups.priority as pri FROM groupmembers LEFT JOIN groups on groups.gid = groupmembers.gid WHERE groups.type != 1");
		while($row = $this->db_next())
		{
			if ($gm[$row["uid"]]["pri"] < $row["pri"])
			{
				$gm[$row["uid"]] = $row;
			}
		}
		$this->db_query("SELECT distinct(uid) as uid FROM syslog_archive WHERE g_oid IS NULL");
		while($row = $this->db_next())
		{
			$this->save_handle();
			$gp = $gm[$row["uid"]]["oid"];
			echo "$row[uid] => $gp <br>\n";
			flush();
			$this->db_query("UPDATE syslog_archive SET g_oid = '$gp' WHERE uid = '$row[uid]'");
			$this->restore_handle();
		}

		echo "creating entry & exit pages summaries <br>\n";
		flush();
		$pgs = array();
		$this->db_query("SELECT a.* FROM syslog_archive a LEFT JOIN syslog_archive_sessions s ON a.session_id = s.session_id WHERE s.session_id is null limit 300000");
		while ($row = $this->db_next())
		{
			if (!isset($pgs[$row["session_id"]]))
			{
				$pgs[$row["session_id"]] = array(
					"entry_page" => $row["oid"],
					"exit_page" => $row["oid"],
					"tm_s" => $row["tm"],
					"tm_e" => $row["tm"]
				);
			}
			else
			{
				$pgs[$row["session_id"]]["exit_page"] = $row["oid"];
				if ($row["tm"] > $pgs[$row["session_id"]]["tm_e"])
				{
					$pgs[$row["session_id"]]["tm_e"] = $row["tm"];
				}
				if ($row["tm"] < $pgs[$row["session_id"]]["tm_s"])
				{
					$pgs[$row["session_id"]]["tm_s"] = $row["tm"];
				}
			}
		}
		echo "session count = ".count($pgs)." <br>";
		$cnt = 0;
		foreach($pgs as $session => $inf)
		{
			if ((++$cnt % 100) == 2)
			{
				echo "count = $cnt <br>\n";
				flush();
			}
			$this->db_query("INSERT INTO syslog_archive_sessions(session_id, entry_page, exit_page, tm_s, tm_e) values('$session','$inf[entry_page]', '$inf[exit_page]',$inf[tm_s], $inf[tm_e])");
		}
		$this->set_cval("night_moves_in_progress", 0);
		die("all done");
	}


	/**
		@attrib name=monthly_archive nologin=1
	**/
	function monthly_archive()
	{
		$this->delete_all_online_periods();

		$start = mktime(0,0,0, date("m")-1, 1, date("Y"));
		$end = mktime(0,0,0, date("m"), 1, date("Y"));

		chdir(aw_ini_get("stats_model.folder"));
		$fn = aw_ini_get("stats_model.folder")."/".date("d.m.Y", $start)."_".date("d.m.Y", $end).".sql";
		$fnz = $fn.".gz";
		if (file_exists($fnz))
		{
			die("file already exists");
		}
echo "fn = $fn <br>";
		// archive entries
		$cmd = aw_ini_get("server.mysqldump_path")." --skip-add-drop-table -n -t --skip-add-locks -u ".aw_ini_get("db.user")." -h ".aw_ini_get("db.host")." --password=".aw_ini_get("db.pass")." ".aw_ini_get("db.base")." syslog_archive > $fn";
		echo "cmd = $cmd <br>";
		$res = `$cmd`;

		$cmd = aw_ini_get("server.gzip_path")." ".$fn;
		$res = `$cmd`;
		echo "cmd = $cmd <br>";

		// delete the entries from last month in syslog_archive, since this should be called on the 1st of every month
		$q = "DELETE FROM syslog_archive ";
		echo $q." <br>";
		$this->db_query($q);
		die("all done");
	}

	/** Returns a list of tld => country_name pairs
		@attrib api=1 params=name

		@returns array of top-level-domain-name => country name
	**/	
	function get_country_list()
	{
		return $this->countries;
	}

	function get_simple_count_for_obj($oid, $from = null, $to = null)
	{
		$whb = array();
		if ($from !== null && $from > -1)
		{
			$whb[] = " tm >= $from ";
		}
		if ($to !== null && $to > -1)
		{
			$whb[] = " tm <= $to ";
		}
		$where = "";
		if (count($whb))
		{
			$where = " AND ".join(" AND ", $whb);
		}
		$rv = $this->db_fetch_field("SELECT count(*) as cnt FROM syslog WHERE oid = $oid $where", "cnt");
		$rv += $this->db_fetch_field("SELECT count(*) as cnt FROM syslog_archive WHERE oid = $oid $where", "cnt");
		return $rv;
	}
}
?>

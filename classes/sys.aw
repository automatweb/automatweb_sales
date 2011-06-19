<?php

/** various system management methods **/
class sys extends aw_template implements orb_public_interface
{
	function sys($args = array())
	{
		$this->init("automatweb");
		$this->lc_load("syslog","lc_syslog");
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/** Generates xml database structure
		@attrib name=gen_db_struct params=name default="0"
	**/
	function gen_db_struct($args = array())
	{
		$tables = $this->db_get_struct();
		$ser = aw_serialize($tables, SERIALIZE_XML, array("ctag" => "tabledefs"));
		header("Content-Type: text/xml");
		header("Content-length: " . strlen($ser));
		header("Content-Disposition: filename=awtables.xml");
		print $ser;
		exit;
	}

	/** Displays a table with the sise of the site's database tables
		@attrib name=show_table_sizes
	**/
	function show_table_sizes()
	{
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Tabeli nimi"),
		));

		$t->define_field(array(
			"name" => "size",
			"caption" => t("Suurus"),
		));

		$t->define_field(array(
			"name" => "perc",
			"caption" => "%",
		));

		$dbs = 0;
		$result = mysql_query('SHOW TABLE STATUS');

		$res = array();
		while($row = mysql_fetch_array($result))
		{
			$dbs += $row["Data_length"] + $row["index_length"];
			$res[$row["Name"]] = $row["Data_length"] + $row["index_length"];
		}
		arsort($res);
		foreach($res as $name => $size)
		{
			if($size)$t->define_data(array(
				"name" => $name,
				"size" => $size,
				"perc" => number_format(($size/$dbs * 100), 2). " %",
			));
		}
		$t->define_data(array(
			"name" => t("Total").":",
			"size" => $dbs,
			"perc" => "100 %",
		));
		return $t->draw();
	}


	/** Generates SQL statements that create a correct aw database structure
		@attrib name=gen_create_tbl params=name
	**/
	function gen_create_tbl($args = array())
	{
		$ret = array();
		$tables = $this->db_get_struct();
		foreach($tables as $tblname => $tbldat)
		{
			$this->db_query("SHOW CREATE TABLE $tblname");
			$row = $this->db_next();
			$ret[$tblname] = $row["Create Table"];
		}
		$ser = aw_serialize($ret, SERIALIZE_XML);
		header("Content-Type: text/xml");
		header("Content-length: " . strlen($ser));
		header("Content-Disposition: filename=awtables.xml");
		print $ser;
		exit;
	}

	/** Lets the user select the site to sync databases with
		@attrib name=dbsync params=name
	**/
	function db_compare_choose_donor($args = array())
	{
		$files = array(
			"www.just.ee" => "www.just.ee",
			"envir.struktuur.ee" => "envir.struktuur.ee",
			"tarvo.dev.struktuur.ee" => "tarvo.dev.struktuur.ee",
			"sander.dev.struktuur.ee" => "sander.dev.struktuur.ee",
			"voldemar.dev.struktuur.ee" => "voldemar.dev.struktuur.ee",
			"terryf.dev.struktuur.ee" => "terryf.dev.struktuur.ee",
			"seppik.struktuur.ee" => "seppik.struktuur.ee",
			"www2.just.ee" => "www2.just.ee",
			"work.struktuur.ee" => "work.struktuur.ee",
			"aw.struktuur.ee" => "aw.struktuur.ee",
			"idaviru.struktuur.ee" => "idaviru.struktuur.ee",
			"awvibe.struktuur.ee" => "awvibe.struktuur.ee",
			"horizon.struktuur.ee" => "horizon.struktuur.ee",
			"star.automatweb.com" => "star.automatweb.com",
			"arin.struktuur.ee" => "arin.struktuur.ee",
			"www.ut.ee" => "www.ut.ee",
			"intranet.automatweb.com" => "intranet.automatweb.com",
			"www.notar.ee" => "www.notar.ee",
			"koolitus.automatweb.com" => "koolitus.automatweb.com",
			"sven.dev.struktuur.ee" => "sven.dev.struktuur.ee",
			"otto.struktuur.ee" => "otto.struktuur.ee",
			"rate.automatweb.com" => "rate.automatweb.com",
			"www.kiosk.ee" => "www.kiosk.ee",
			"prisma.struktuur.ee" => "prisma.struktuur.ee",
			"linnaehitus.struktuur.ee" => "linnaehitus.struktuur.ee",
			"envir.struktuur.ee" => "envir.struktuur.ee",
			"bbraun.struktuur.ee" => "bbraun.struktuur.ee",
			"www.kalender.ee" => "www.kalender.ee",
			"dragut.dev.struktuur.ee" => "dragut.dev.struktuur.ee",
			"mail.prismaprint.ee" => "mail.prismaprint.ee",
			"ee.struktuur.ee" => "ee.struktuur.ee"
		);

		$this->read_template("compare_db_step1.tpl");

		$this->vars(array(
			"donors" => $this->picker(-1,$files),
			"reforb" => $this->mk_reforb("db_compare_dbs",array()),
		));
		return $this->parse();
	}

	/** Compares database structures created by gen_db_struct and lets the user merge them
		@attrib name=db_compare_dbs params=name

		@comment
			On the left, the external definition and on the right the local database, the checkboxes are chechked where the local database needs to be added to.
	**/
	function _db_compare_dbs($args)
	{
		extract($args);
		$right = $this->db_get_struct();
		$h = new http();
		$block = $h->get("http://".$donor."/?class=sys&action=gen_db_struct");
		$dsource = aw_unserialize($block);
		$donor_struct = $dsource;
		$_SESSION['donor_struct'] = $donor_struct;
		$all_keys = array_merge(array_flip(array_keys($dsource)),array_flip(array_keys($right)));
		ksort($all_keys);
		$this->read_template("compare_db.tpl");
		$c = "";
		foreach($all_keys as $key => $value)
		{
			$c .= $this->_db_compare_tables($key,$dsource[$key],$right[$key]);
		};
		$this->vars(array(
			"block" => $c,
			"reforb" => $this->mk_reforb("submit_compare_db",array()),
		));
		print $this->parse();
		exit;
	}

	/** Is called from _db_compare_db-s for each invidual table **/
	private function _db_compare_tables($name,$arg1,$arg2)
	{
		// koigepealt leiame siis molema tabelidefinitsiooni v2ljade nimed, ning
		// moodustame neist yhise array
		if (is_array($arg1) && is_array($arg2))
		{
			$all_keys = array_merge(array_flip(array_keys($arg1)),array_flip(array_keys($arg2)));
		}
		elseif (is_array($arg1))
		{
			$all_keys = array_flip(array_keys($arg1));
		}
		else
		{
			$all_keys = array_flip(array_keys($arg2));
		};
		ksort($all_keys);
		global $left,$right;
		$gproblems = 0;
		global $problems;
		$problems = 0;
		$c = "";
		$this->vars(array("name" => $name));

		foreach($all_keys as $key => $val)
		{
			list($typematch,$flagmatch,$keymatch) = $this->_db_compare_fields($arg1[$key],$arg2[$key]);
			$color1 = ($typematch) ? "#FFFFFF" : "#FFCCCC";
			$color2 = ($keymatch)  ? "#FFFFFF" : "#FFCCCC";
			$color3 = ($flagmatch) ? "#FFFFFF" : "#FFCCCC";
			$flags1 = is_array($arg1[$key]["flags"]) ? join(" ",$arg1[$key]["flags"]) : "";
			$flags2 = is_array($arg2[$key]["flags"]) ? join(" ",$arg2[$key]["flags"]) : "";

			// kui koik matchivad, siis pole checkboxi vaja kuvada
			if ($typematch && $flagmatch && $keymatch)
			{
				$check = "";
			}
			else
			{
				// ehk siis, kui doonoris vastav v2li olemas on, siis teeme selle operatsiooni.
				if ($arg1[$key]["type"])
				{
					$check = "checked";
				}
				else
				{
					$check = "";
				}
			}

			$this->vars(array(
				"key" => $key,
				"color1" => $color1,
				"color2" => $color2,
				"color3" => $color3,
				"name" => $name,
				"type1" => $arg1[$key]["type"],
				"key1" => $arg1[$key]["key"],
				"flags1" => $flags1,
				"type2" => $arg2[$key]["type"],
				"key2" => $arg2[$key]["key"],
				"flags2" => $flags2,
				"checked" => $check,
			));
			$c .= $this->parse("block.line");
		};
		$this->vars(array(
			"line" => $c,
		));
		return $this->parse("block");
	}

	/** Is called from _db_compare_tables for each invidual field **/
	private function _db_compare_fields($field1,$field2)
	{
		global $problems;
		if ($field1["type"] == $field2["type"])
		{
			$res1 = true;
		}
		else
		{
			$problems++;
			$res1 = false;
		};

		$flags1 = (is_array($field1["flags"])) ? join(",",$field1["flags"]) : "";
		$flags2 = (is_array($field2["flags"])) ? join(",",$field2["flags"]) : "";
		$res2 = ( $flags1 == $flags2 );
		if (not($res2))
		{
			$problems++;
		};

		if ( isset($field1["key"]) == isset($field2["key"]) )
		{
			$res3 = ( $field1["key"] == $field2["key"] );
		}
		else
		{
			$problems++;
			$res3 = false;
		};

		return array($res1,$res2,$res3);
	}

	/** Processes the selected fields from the dbsync compare databases display
		@attrib name=submit_compare_db params=name default="0"
	**/
	function submit_compare_db($args = array())
	{
		$donor_struct = $_SESSION['donor_struct'];
		$orig = $this->db_get_struct();
		extract($args);
		if (is_array($check))
		{
			foreach($check as $table => $fields)
			{
				if ($table === "syslog")
				{
					continue;
				}
				foreach($fields as $key => $val)
				{
					$dr = $donor_struct[$table][$key];
					$og = $orig[$table][$key];
					if (is_array($dr["flags"]))
					{
						$flags = join(" ",$dr["flags"]);
					}
					else
					{
						$flags = "";
					};

					$prim_key_added = false;

					if ($og["type"])
					{
						// kui lokaalsel koopial on index ja remote pole,
						// siis igal juhul lisame me lokaasele NOT NULL votme
						if ($og["key"])
						{
							if (strpos($flags,"NOT NULL") === false)
							{
								$flags .= " NOT NULL";
							};
						};
						$line = "ALTER TABLE `$table` CHANGE `$key` `$key` $dr[type] $flags";
					}
					else
					{
						if (is_array($dr["flags"]) && in_array("auto_increment",$dr["flags"]))
						{
							$flags = str_replace("auto_increment","",$flags);
							// primary keys NEED not null
							if (not(in_array("NOT NULL",$dr["flags"])))
							{
								$flags .= " NOT NULL";
							};
							$autoinc = " PRIMARY KEY";
							$prim_key_added = true;
						}
						else
						{
							$autoinc = "";
						};

						if (not(is_array($orig[$table])))
						{
							$line = "CREATE table `$table` (`$key` $dr[type] $flags $autoinc)";
							echo "line = $line <br />";
							$orig[$table] = array();
						}
						else
						{
							$line = "ALTER TABLE `$table` ADD `$key` $dr[type] $flags $autoinc";
						}
					}

					print "Q1: $line<br />";
					aw_global_set("__from_raise_error",1);
					$this->db_query($line);
					$line = "";
					if ( ($dr["key"] == "PRI") && ($prim_key_added == false))
					{
						$line = "ALTER TABLE `$table` ADD PRIMARY KEY (`$key`)";
					}
					elseif ($dr["key"] == "MUL")
					{
						$line = "ALTER TABLE `$table` ADD KEY (`$key`)";
					};
					if ($line)
					{
						print "Q2: $line<br />";
						$this->db_query($line);
					};
					//print "updating field $key of table $table<br />";
					//print "donor value is <pre>";
					//print_r($donor_struct[$table][$key]);
					//print "</pre>";

				}
			}
		}
		print "all done<br />";
		if (!$args["no_exit"])
		{
			exit;
		}
	}

	function on_site_init(&$dbi, $site, $ini_opts, &$log)
	{
		// no need to dbsync if we are not creating a new site
		if (!$site['site_obj']['use_existing_database'])
		{
			// do a dbsync from intranet
			$h = new http();
			$block = $h->get("http://intranet.automatweb.com/?class=sys&action=gen_create_tbl");
			$tbls = aw_unserialize($block);

			foreach($tbls as $tbl => $sql)
			{
				$dbi->db_query($sql);
			}

			$q = "ALTER TABLE static_content ADD FULLTEXT content(content)";
			$dbi->db_query($q);
			$q = "ALTER TABLE static_content ADD FULLTEXT tit_cont(title,content)";
			$dbi->db_query($q);

			$log->add_line(array(
				"uid" => aw_global_get("uid"),
				"msg" => t("L&otilde;i saidi andmebaasi tabelid"),
				"comment" => "",
				"result" => "OK"
			));

			// set default error page
			$dbi->db_query("INSERT INTO config (ckey,content) values('error_redirect','/error')");
		}
	}

	/** Checks if the database tables have correct indexes and if not, creates them
		@attrib name=check_indexes
	**/
	function do_check_indexes($arr)
	{
		$indexes = array(
			"objects" => array(
				"oid","class_id","status","site_id","lang_id","jrk","modified","created"
			),
			"aliases" => array(
				"source","target", "reltype"
			),
			"acl" => array(
				"gid", "oid"
			),
			"files" => array(
				"showal"
			)
		);

		echo "checking indexes.. <br>\n";
		flush();

		foreach($indexes as $tbl => $td)
		{
			echo ".. table $tbl <br>\n";
			flush();
			$has_idx = array();
			$this->db_list_indexes($tbl);
			while($idd = $this->db_next_index())
			{
				$has_idx[$idd["col_name"]] = $idd;
			}

			foreach($td as $field)
			{
				if (!isset($has_idx[$field]))
				{
					echo "missing index for table $tbl field $field, create stmt:<br>";
					$this->db_add_index($tbl, array(
						"name" => $field,
						"col" => $field
					));
				}
			}
		}

		die(t("all done! "));
	}

	/** checks if any objects of the given class exist in the current database
		@attrib name=has_objects

		@param clid required type=int
			The class id to check for

		@comment
			can be used (with foreach_site) to check if a class can be safely removed
	**/
	function has_objects($arr)
	{
		$ol = new object_list(array(
			"class_id" => $arr["clid"]
		));
		if ($ol->count())
		{
			echo "<font color='red' size='7'>site ".aw_ini_get("baseurl")." HAS ".$ol->count()." objects!!!</font><br><br>";
		}
		else
		{
			echo "NEIN!!.";
		}
	}

	/** Displays a list of 1400 objects with the latest modification dates
		@attrib name=last_mod
	**/
	function last_mod()
	{
		$t = new vcl_table(array(
			"layout" => "generic"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("Klass"),
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muutmise aeg"),
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i",
			"sortable" => 1
		));
		$ol = new object_list(array(
			"modified" => new obj_predicate_compare(OBJ_COMP_GREATER, time()-3600*24*3),
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.modified DESC",
			"limit" => 1400
		));
		$clss = aw_ini_get("classes");
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::get_change_url($o->id(), array(), parse_obj_name($o->name())),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"class_id" => $clss[$o->class_id()]["name"]
			));
		}
		$t->set_default_sortby("modified");
		$t->set_default_sorder("desc");
		$t->sort_by();
		return $t->draw();
	}

	/** Analyzes aw access logs and displays stats
		@attrib name=perf
	**/
	function perf()
	{
		$data = array();
		$this->db_query("show status");
		while ($row = $this->db_next())
		{
			$data[$row["Variable_name"]] = $row["Value"];
		}

		echo "key cache hit rate: ".number_format((100 - (($data["Key_reads"]  / $data["Key_read_requests"]) * 100)),2)."%<br>";
		echo "open tables: ".$data["Open_tables"]." vs opened tables: ".$data["Opened_tables"]." <br>";
		echo "------------------AW <br>";
		echo "class count = ".count(aw_ini_get("classes"))." <br>";
		echo "------------------STATS <br>\n";
		flush();

		// slurp in files, count by date and site
		for($i = 0; $i < 30; $i++)
		{
			$date = mktime(0,0,0, date("m"), date("d")-$i, date("Y"));
			$fn = "/www/automatweb_new/files/logs/".date("Y-m-d", $date).".log";
			if (!file_exists($fn))
			{
				continue;
			}

			echo "<B>".date("d.m.Y", $date)."</b><br>\n";
			flush();
			$lines = file($fn);
			$sites = array();
			$urls = array();
			$sid2url = array();
			$total = count($lines);
			$total_time = 0;
			$times = array();
			$page_times = array();
			$tot_page_times = array();
			$promo_time = array();

			foreach($lines as $line)
			{
				list($dp, $tm, $sid, $bu, $url, $time, $p_time) = explode(" ", $line);
				$sites[$sid]++;
				$sid2url[$sid] = $bu;
				$urls[$bu.$url]++;
				$time = (float)$time;
				$times[$sid] += $time;
				$total_time += $time;
				$page_times[] = $time;
				$page_t2p[] = $bu.$url;
				$tot_page_times[$bu.$url] += $time;
				$promo_time[$sid] += $p_time;
				$promo_time_cnt[$sid] += ($p_time > 0 ? 1 : 0);
			}

			$avg_page_times = array();
			foreach($tot_page_times as $pg => $time)
			{
				$avg_page_times[$pg] = $time / $urls[$pg];
			}

			arsort($sites);
			arsort($urls);
			arsort($page_times);
			arsort($avg_page_times);

			echo "total pageviews: $total<Br>total time taken: $total_time seconds <br>top sites: <br>";
			$num = 0;
			foreach($sites as $site => $cnt)
			{
				echo "site ".$sid2url[$site]." got $cnt pageviews and took a total of ".$times[$site]." seconds, average pv is ".($times[$site] / $cnt)." promo display took ".$promo_time[$site]." avg = ".($promo_time[$site] / $promo_time_cnt[$site])." <Br>";
				/*if (++$num > 30)
				{
					break;
				}*/
			}
			echo "<br>total number of sites touched: ".count($sites)."<br>top urls: <br>";
			$num = 0;
			foreach($urls as $url => $cnt)
			{
				echo "url <a href='$url'>$url</a> got $cnt pageviews <Br>";
				if (++$num > 10)
				{
					break;
				}
			}

			echo "<br>top 20 longest pages by longest time: <br>";
			$num = 0;
			foreach($page_times as $idx => $time)
			{
				echo "page ".$page_t2p[$idx]." took $time seconds <br>";
				if (++$num > 20)
				{
					break;
				}
			}

			echo "<br>top 20 longest pages by average, cnt > 1: <br>";
			$num = 0;
			foreach($avg_page_times as $url => $time)
			{
				if ($urls[$url] < 2)
				{
					continue;
				}
				echo "page $url took $time seconds on average (count = ".$urls[$url].")<br>";
				if (++$num > 20)
				{
					break;
				}
			}

			echo "------------------------------------<br>";
		}
		die();
	}

	/** tests database by adding all possible types of objects
		@attrib name=test_object_types

		@param parent required acl=view;add
	**/
	function test_object_types($arr)
	{
		$clss = aw_ini_get("classes");
		foreach($clss as $clid => $cldata)
		{
			echo "clid = $clid , name = $cldata[name] <br>\n";
			flush();
			$o = obj();
			$o->set_parent($arr["parent"]);
			$o->set_class_id($clid);
			$o->set_name($cldata["name"]);
			$o->save();
		}
		die(t("all done!! database seems to be relatively ok!"));
	}

	/** tests sites in site list. will only be called from register site

		@attrib name=test_sites
	**/
	function test_sites($arr)
	{
		ob_end_clean();
		aw_set_exec_time(AW_LONG_PROCESS);
		echo "testing sites ... <br>\n";
		flush();

		$cnt = $this->db_fetch_field("SELECT count(*) as cnt FROM aw_site_list WHERE site_used = 1 AND last_update > ".(time() - 24*3600*30), "cnt");

		$errs = array();

		$num = 1;
		$this->db_query("SELECT * FROM aw_site_list WHERE site_used = 1 AND last_update > ".(time() - 24*3600*30));
		while ($row = $this->db_next())
		{
			echo sprintf("%03d/%03d", $num, $cnt)." ".$row["url"]." .... \n";
			flush();

			ob_start();
			$fc = strtolower(file_get_contents($row["url"]));
			$ct = ob_get_contents();
			ob_end_clean();

			if (strpos($ct, "401") !== false)
			{
				// auth req, assume site is ok
				$fc = "<html";
				$ar = " (auth required) ";
			}
			else
			{
				echo $ct;
				$ar = "";
			}

			if (strpos($fc, "<html") !== false || strpos($fc, "<head") !== false || $fc == "")
			{
				echo " <font color=green>Success</font> $ar<br>\n";
				flush();
			}
			else
			{
				echo " <font color=red>Failed</font><br>\n";
				echo "<pre>".htmlentities($fc)."</pre>";
				flush();
				$errs[] = "sait $row[url] tundub maas olevat, esilehe sisu: \n".$fc."\n\n";
			}
			$num++;
		}

		if (count($errs) > 0)
		{
			send_mail("dev@struktuur.ee", "SAIT MAAS!!", join("\n", $errs), "From: big@brother.ee");
		}
		die(t("All done"));
	}

	/**
		@attrib name=do_dump
	**/
	function db_dump($arr)
	{
		$fld = aw_ini_get("site_basedir")."/files/dumper/";
		@mkdir($fld, 0777);

		$fn = $fld."dump.sql";
		@unlink($fn);


		$u = aw_ini_get("db.user");
		$h = aw_ini_get("db.host");
		$p = aw_ini_get("db.pass");
		$db = aw_ini_get("db.base");
		$dump = $fld."db.sql";

		$res = `/usr/local/bin/mysqldump --add-drop-table --quick -u $u -h $h --password=$p $db > $dump`;
		echo file_get_contents($dump);
		unlink($dump);
		die();
	}

	/**
		@attrib name=site_gzip
	**/
	function site_gzip($arr)
	{
		$fld = aw_ini_get("site_basedir")."/files/dumper/";
		mkdir($fld, 0777);


		$fn = $fld."dump.tar.gz";
		unlink($fn);

		$base = aw_ini_get("basedir")."/";

		$res = `cp -r $base/archive $fld`;
		$res = `cp -r $base/aw.ini $fld`;
		$res = `cp -r $base/files $fld`;
		$res = `cp -r $base/img $fld`;
		$res = `cp -r $base/lang $fld`;
		$res = `cp -r $base/public $fld`;
		$res = `cp -r $base/templates $fld`;

		$res = `/usr/bin/tar cvfz $fn $fld/*`;

		echo file_get_contents($fn);
		die();
	}

	/**
		@attrib name=do_test_dump

	**/
	function do_test_dump($arr)
	{
		$fld = aw_ini_get("site_basedir")."/files/dumper/";
		mkdir($fld, 0777);


		$fn = $fld."dump.tar.gz";
		unlink($fn);

		$base = aw_ini_get("basedir")."/";

		$res = `cp -r $base/archive $fld`;
		$res = `cp -r $base/aw.ini $fld`;
		$res = `cp -r $base/files $fld`;
		$res = `cp -r $base/img $fld`;
		$res = `cp -r $base/lang $fld`;
		$res = `cp -r $base/public $fld`;
		$res = `cp -r $base/templates $fld`;

		$u = aw_ini_get("db.user");
		$h = aw_ini_get("db.host");
		$p = aw_ini_get("db.pass");
		$db = aw_ini_get("db.base");
		$dump = $fld."db.sql";

		$res = `/usr/local/bin/mysqldump --add-drop-table --quick -u $u -h $h --password=$p $db > $dump`;

		$res = `/usr/bin/tar cvfz $fn $fld/*`;

		die(t("$fn"));
	}

/**
	@attrib name=make_prop
	@param classes optional type=string
		comma separated list of classes to make properties for.
**/
	public function make_property_definitions($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		if (isset($arr["classes"]) && preg_match("/[^a-z,_0-9]/Ui", $arr["classes"]))
		{
			throw new awex_sys("Invalid argument");
		}

		$classes = isset($arr["classes"]) ? explode(",", $arr["classes"]) : array();
		$this->_make_property_definitions($classes);
		exit; //TODO: tmp, move header from admin_footer to cb or htmlc
	}

	private function _make_property_definitions($classes = array())
	{
		$collector = new propcollector();

		try
		{
			$www = ("sys" === automatweb::$request->arg("class"));
		}
		catch (Exception $e)
		{
			$www = false;
		}

		if (count($classes))
		{
			$failed = array();

			foreach ($classes as $name)
			{
				try
				{
					$collector->parse_class($name);
				}
				catch (awex_propcollector $e)
				{
					$failed[] = $name;
				}
			}

			if (count($failed))
			{
				$e = new awex_sys_mkprop_cl("Couldn't make properties for '" . implode("','", $failed) . "'");
				$e->failed_classes = $failed;
				throw $e;
			}
		}
		else
		{
			if ($www)
			{
				echo "<pre>";
			}

			$collector->run();

			if ($www)
			{
				echo "</pre>";
			}
		}
	}

/**
	@attrib name=make_orb
	@param classes optional type=string
		comma separated list of classes to make orb definitions for.
**/
	function make_orb_definitions($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$classes = isset($arr["classes"]) ? explode(",", $arr["classes"]) : array();
		$this->_make_orb_definitions($classes);
		exit; //TODO: tmp, move header from admin_footer to cb or htmlc
	}

	private function _make_orb_definitions($classes = array())
	{
		aw_global_set("no_db_connection", 1);
		$scanner = new orb_gen();
		echo "<pre>";
		$scanner->make_orb_defs_from_doc_comments();
		echo "</pre>";
	}

/**
	@attrib name=make_msg
**/
	function make_message_maps()
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$this->_make_message_maps();
		exit; //TODO: tmp, move header from admin_footer to cb or htmlc
	}

	private function _make_message_maps()
	{
		aw_global_set("no_db_connection", 1);
		$scanner = get_instance("core/msg/msg_scanner");

		echo "<pre>";
		$scanner->scan();
		echo "</pre>";
	}

	// DEPRECATED. separate ini files not used anymore
	function make_ini_file()
	{ return;
		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$_GET["in_popup"] = 1;
		$this->_make_ini_file();
	}

	// DEPRECATED. separate ini files not used anymore
	function _make_ini_file()
	{ return;
		$basedir = aw_ini_get("basedir");
		$input_file = $basedir . "/aw.ini.root";
		$output_file = $basedir . "/aw.ini";

		if (!file_exists($input_file))
		{
			throw new awex_sys_ini("File not found.");
		}

		require($basedir . "/scripts/ini/parse_config_to_ini.aw");
		$res = parse_config_to_ini($input_file);

		if ($res === false)
		{
			throw new awex_sys_ini("No config data returned from parser");
		}
		else
		{
			$fp = fopen ($output_file, "w");
			fwrite ($fp, $res, strlen($res));
			fclose ($fp);
			echo "<pre>";
			echo "aw.ini successfully written.";
			echo "</pre>";
		}
	}

/**
	@attrib name=make_trans
**/
	function make_translations()
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		if (!headers_sent())
		{
			header ("Content-Type: text/plain");
		}

		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$this->_make_translations();
		exit; //TODO: tmp, move header from admin_footer to cb or htmlc
	}

	private function _make_translations()
	{
		$i = get_instance("core/trans/pot_scanner");
		$i->full_scan();
		flush();
		$i->make_aw();
	}

/**
	@attrib name=list_untrans
	@param lang optional
	@param in_class optional
**/
	function list_missing_translations($arr = array())
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		if (!headers_sent())
		{
			header ("Content-Type: text/plain");
		}

		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}
		$this->_list_missing_translations($arr["lang"], $arr["in_class"]);
	}

	private function _list_missing_translations($lang = "", $class = "")
	{
		$i = get_instance("core/trans/pot_scanner");
		$i->list_untrans_strings($lang, $class);
	}

/**
	@attrib name=make_class
**/
	public function make_class()
	{
		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$this->show_class_form();
		exit; //TODO: tmp, move header from admin_footer to cb or htmlc
	}

/**
	@attrib name=save_class
**/
	public function save_class($arr)
	{
		if (!aw_ini_get("enable_web_maintenance"))
		{
			throw new awex_sys_webmaintenance("Web maintenance is turned off");
		}

		$this->_save_class($arr);
		exit;
	}

	private function show_class_form()
	{
		echo <<<ENDCLASSFORM
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Make class</title>
</head>
<body>

<form method="POST" action="orb.aw">
Folder where the class file is (created under AWROOT/classes):<br/>
<input type="text" name="mkcl_folder" size="50"/><br/>

Class file (foo_bar):<br/>
<input type="text" name="mkcl_file" size="50"/><br/>

Class name, users see this, so be nice (Foo bar):<br/>
<input type="text" name="mkcl_name" size="50"/><br/>

Can the user add this class? (1/0):<br/>
<select name="mkcl_can_add">
<option value="1">Yes</option>
<option value="0">No</option>
</select><br/>

Class parent folder id(s) (from classfolders.ini):<br/>
<input type="text" name="mkcl_parents" size="50"/><br/>

Alias (if you leave this empty, then the class can"t be added as an alias. Multiple aliases comma separated.):<br/>
<input type="text" name="mkcl_alias" size="50"/><br/>

Class is remoted? (1/0):<br/>
<select name="mkcl_is_remoted">
<option value="0">No</option>
<option value="1">Yes</option>
</select><br/>

Default server to remote to (http://www.foo.ee) (fill only if is remoted):<br/>
<input type="text" name="mkcl_default_remote_server" size="50"/><br/>

<input type="hidden" name="class" value="sys" />
<input type="hidden" name="action" value="save_class" />
<input type="hidden" name="reforb" value="1" />
<input type="hidden" name="ret_to_orb" value="1" />
<input type="submit">
</form>

</body>
</html>
ENDCLASSFORM;
	}

	private function _save_class($args)
	{
		echo "<pre>";
		include(aw_ini_get("basedir") . "/scripts/mk_class/mk_class.aw");
		echo "</pre>";
	}

	/**
		@attrib name=consolidate_template_logs
	**/
	public function consolidate_template_logs($arr)
	{
		// make a list of all files available
		// analyze and remove all past months logs
		foreach(glob(aw_ini_get("site_basedir")."/files/template_log_*") as $fn)
		{
			echo "fn = $fn <br>";
			list(,,$y, $m) = explode("_", basename($fn, ".log"));
			echo "y = $y , m = $m <br>";
			if (($y == date("Y") && $m < date("m")) || $y < date("Y"))
			{
				$this->_process_single_template_log($fn, (int)$y, (int)$m);
			}
		}
		die("all done");
	}

	private function _process_single_template_log($fn, $y, $m)
	{
		$data = array();
		foreach(file($fn) as $line)
		{
			list($tm, $url, $tpl) = explode("|", trim($line));
			$data[$tpl][$url]++;
		}

		// sort the template list so that for each template we get the url it is most referenced in
		$s_data = array();
		foreach($data as $tpl => $d)
		{
			arsort($d);
			reset($d);
			if (strpos($tpl, aw_ini_get("site_basedir")) !== false)
			{
				$tpl = str_replace(aw_ini_get("site_basedir"), "", realpath($tpl));
				$s_data[$tpl] = key($d);
			}
		}

		// now send it to register and remove the log file
		$rv = $this->do_orb_method_call(array(
			"class" => "template_logger",
			"action" => "recieve_log_data",
			"method" => "xmlrpc",
			"server" => "http://register.automatweb.com",
			"params" => array(
				"site_id" => aw_ini_get("site_id"),
				"data" => $s_data,
				"y" => $y,
				"m" => $m
			),
		));
		if ($rv == 1)
		{
			unlink($fn);
		}
	}

	/**
		@attrib name=prop_stats
	**/
	function prop_stats()
	{
		$rv = array();
		// go over all classes and all their props and for each in a separate table query that to fetch count
		$clss = aw_ini_get("classes");
		ob_end_clean();
		foreach($clss as $clid => $cld)
		{
			$o = obj();
			$o->set_class_id($clid);
			$total = $this->db_fetch_field("SELECT count(*) as cnt FROM objects WHERE class_id = $clid", "cnt");
			foreach($o->get_property_list() as $pn => $pd)
			{
				if ($pd["store"] == "no" || $pd["table"] == "objects" || $pd["method"] == "serialize" || $pd["table"] == "" || $pd["field"] == "" || $pd["store"] == "connect")
				{
					continue;
				}

				$q = "SELECT count(*) as cnt from ".$pd["table"]." WHERE `".$pd["field"]."` != '".$pd["default"]."'";
				$cnt = min($total, (int)$this->db_fetch_field($q, "cnt", false));

				$rv[] = array(
					"class_id" => $clid,
					"prop" => $pn,
					"site_id" => aw_ini_get("site_id"),
					"set_objs" => $cnt,
					"total_objs" => $total
				);
			}
		}
		return $rv;
	}

	/**
		@attrib name=clid_stats
	**/
	function clid_stats()
	{
		$rv = array();
		//$this->db_query("DELETE FROM aw_site_object_stats WHERE site_id = ".aw_ini_get("site_id"));
		$this->db_query("SELECT count(*) as cnt, class_id FROM objects GROUP BY class_id");
		while ($row = $this->db_next())
		{
			/*$this->save_handle();
			$this->db_query("INSERT INTO aw_site_object_stats(site_id, class_id, count) values(".aw_ini_get("site_id").", $row[class_id], $row[cnt])");
			$this->restore_handle();*/
			$rv[] = array(
				"site_id" => aw_ini_get("site_id"),
				"class_id" => $row["class_id"],
				"count" => $row["cnt"]
			);
		}
		return $rv;
	}
}

/* Generic sys class exception */
class awex_sys extends aw_exception {}

/* Configuration parser errors */
class awex_sys_ini extends awex_sys {}

/* Maintenance request from www while it is not turned on in configuration settings */
class awex_sys_webmaintenance extends awex_sys {}

/* Error generating property definitions for class */
class awex_sys_mkprop_cl extends awex_sys
{
	public $failed_classes = array();
}

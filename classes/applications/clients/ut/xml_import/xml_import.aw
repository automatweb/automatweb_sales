<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/ut/xml_import/xml_import.aw,v 1.15 2008/07/15 12:30:15 markop Exp $
/*
        @default table=objects
        @default group=general

        @property datasource type=objpicker clid=CL_DATASOURCE field=meta method=serialize
        @caption XML datasource
        
	@property import_function type=select field=meta method=serialize
        @caption Impordifunktsioon
        
	@property run_import type=text editonly=1 store=no
        @caption K&auml;ivita import

	@classinfo no_status=1 syslog_type=ST_UT_XML_IMPORT maintainer=kristo
                                                                                                                            
*/
class xml_import extends class_base
{

	function xml_import($args = array())
	{
		$this->init(array(
			"clid" => CL_XML_IMPORT,
		));

		$this->methods = array(
			"import_tudengid" => "import_tudengid",
			"import_struktuurid" => "import_struktuurid",
			"import_tootajad" => "import_tootajad",
			"import_oppekava" => "import_oppekava",
			"import_oppeasted" => "import_oppeasted",
			"import_oppevormid" => "import_oppevormid",
			"import_oppeained" => "import_oppeained"
		);
		aw_set_exec_time(AW_SHORT_PROCESS);
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		switch($data["name"])
		{
			case "import_function":
				$data["options"] = $this->methods;
				break;
																								
			case "run_import":
				classload("html");
				$id	= $args["obj_inst"]->id();
				$url = $this->mk_my_orb("invoke",array("id" => $id),"xml_import",0,1);
				$data["value"] = html::href(array("url" => $url,"caption" => t("K&auml;ivita import"),"target" => "_blank"));
				break;
		};
	}

	/**
		@attrib name=invoke params=name nologin=1
		@param id required
		@returns
		@comment
	**/
	function invoke($args = array())
	{
		extract($args);
		$obj = new object($id);
		/*
		if (not($obj))
		{
			return false;
		};
		*/
		if ($obj->class_id() != $this->clid)
		{
			return false;
		};

		print "Retrieving data:<br />";
		flush();
		// retrieve data
		$method = $obj->prop("import_function");
		$ds = get_instance("applications/clients/ut/xml_import/datasource");
		$src_data = $ds->retrieve(array("id" => $obj->prop("datasource")));
echo "ds = ".$obj->prop("datasource")." src = <pre>".htmlentities($src_data)."</pre><br>";

		print "Got " . strlen($src_data) . " bytes of data<br />";
		flush();
		if (strlen($src_data) < 100)
		{
			print "Didn't got enough data from the datasource<br />";
			exit;
		};
		/*
		print "<pre>";
		print htmlspecialchars($src_data);
		print "</pre>";
		*/
		print "Invoking import function<br />";
		flush();
		$this->$method(array("source" => $src_data));
		print "Finished!!!<br />";
		flush();
		exit;
	}

	/**  
		
		@attrib name=import_tudengid params=name nologin="1" 
		
		
		@returns
		
		
		@comment

	**/
	function import_tudengid($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};
		// R.I.P. parser
		xml_parser_free($parser);
		//$q = "DELETE FROM ut_tudengid";
		//$this->db_query($q);
		$count = 0;
		$table = $this->create_temp_table("ut_tudengid");
		foreach($values as $key => $val)
		{
			if ( ($val["tag"]  == "tudeng") && $val["type"] == "complete" )
			{
				$attr = $val["attributes"];		
				$enimi = $this->convert_charset($attr["enimi"]);
				$pnimi = $this->convert_charset($attr["pnimi"]);
				$id = $attr["id"];
				$struktuur = $attr["struktuur"];
				$oppekava = $attr["oppekava"];
				$oppeaste = $attr["oppeaste"];
				$oppevorm = $attr["oppevorm"];
				$oppekava = $this->convert_charset($oppekava);
				$oppeaste = $this->convert_charset($oppeaste);
				$oppevorm = $this->convert_charset($oppevorm);
				$nimi = $enimi . " " . $pnimi;
				$aasta = $attr["aasta"];

				$this->quote($nimi);
				$this->quote($enimi);
				$this->quote($pnimi);
				$q = "INSERT INTO $table (id,enimi,pnimi,struktuur,oppekava,oppeaste,oppevorm,aasta,nimi)
					VALUES('$id','$enimi','$pnimi','$struktuur','$oppekava','$oppeaste','$oppevorm','$aasta','$nimi')";
				print $q;
				print "<br />";
				$this->db_query($q);
				$count++;
			};


		}

		if ($count)
		{
			$this->sync_with_temp("ut_tudengid","temp_ut_tudengid");
		}
		else
		{
			$this->db_query("DROP TABLE $table");
		}
	}
	
	/**  
		
		@attrib name=import_struktuurid params=name nologin="1" 
		
		
		@returns
		
		
		@comment

	**/
	function import_struktuurid($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};
		// xml data arraysse
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		// R.I.P. parser
		xml_parser_free($parser);
		//$q = "DELETE FROM ut_struktuurid";
		//$this->db_query($q);
		$table = $this->create_temp_table("ut_struktuurid");	
		$lastlevel = 0;
		$ylem_list = array();
		$ylem_ilist = array();
		$str_level = 1;
		$counter = 0;
		foreach($values as $key => $val)
		{
			/*
			print "lastlevel = $lastlevel<br />";
			print "<pre>";
			print_r($val);
			print "</pre>";
			*/

			if ($val["tag"] == "struktuur")
			{
					
				if ($val["type"]  ==  "open")
				{
					if ($val["level"] == 2)
					{
						$osakond = $this->convert_charset($val["attributes"]["nimetus"]);
						$ylem_id = $val["attributes"]["id"];
						$attr = $val["attributes"];		
						$ylem_name = $this->convert_charset($attr["nimetus"]);
						$ylem_jrk = $val["attributes"]["jrk"];
						$this->quote($osakond);
					};
				};

				if ( ($val["type"] == "close"))
				{
					$str_level--;
					array_pop($ylem_list);
					array_pop($ylem_ilist);
				};

				if ( ($val["type"] == "open") || ($val["type"]  == "complete") )
				{
					$attr = $val["attributes"];		
					$this->quote($attr);
					$nimetus = $this->convert_charset($attr["nimetus"]);
					$id = $attr["id"];
					$kood = $attr["kood"];
					$aadress = $this->convert_charset($attr["aadress"]);
					$email = $attr["email"];
					$veeb = $attr["veeb"];
					$telefon = $attr["telefon"];
					$faks = $attr["faks"];
					$jrk = $attr["jrk"];

					$real_ylem_name = $ylem_list[sizeof($ylem_list) - 1];
					$real_ylem_id = $ylem_ilist[sizeof($ylem_list) - 1];

					if (not($real_ylem_id))
					{
						if (preg_match("/teaduskond/",$nimetus))
						{
							$real_ylem_id = 0;
						}
						else
						{
							$real_ylem_id = -1;
						};
					};
						

					$jrknimetus = sprintf("%02d%s",$jrk,$nimetus);
					//if ($str_level >= 3)
					if ($str_level > 3)
					{
						$t3_ylem = $t3taseme_ylem_id;
						print "t3<br />";
					}
					else
					{
						$t3_ylem = $real_ylem_id;
						print "real_ylem<br />";
					};

					if ($str_level < 4)
					{
						$t3sort = $id . $str_level . sprintf("%02d",$jrk) . $nimetus;
					}
					else
					{
						$t3sort = $t3taseme_id . $str_level . sprintf("%02d",$jrk) . $nimetus;
					};
						
					$q = "INSERT INTO $table (id,kood,nimetus,aadress,email,veeb,telefon,faks,osakond,ylem_id,ylem_jrk,ylemyksus,jrk,jrknimetus,3taseme_ylem_id,3taseme_sort)
							VALUES('$id','$kood','$nimetus','$aadress','$email','$veeb','$telefon','$faks','$osakond','$real_ylem_id','$ylem_jrk','$real_ylem_name','$jrk','$jrknimetus','$t3_ylem','$t3sort')";
					$counter++;
					if ($str_level == 3)
					{
						$t3taseme_ylem_id = $real_ylem_id;
						$t3taseme_id = $id;
					};
					print "id = $id, kood = $kood, nimetus = $nimetus, aadress = $aadress, email=$email, veeb = $veeb, telefon = $telefon, faks = $faks<br />";
					print "osakond = $osakond, real_ylem_id = $real_ylem_id, real_ylem_name = $real_ylem_name<br />";
					print "jrk = $jrk, jrknimetus = $jrknimetus, t3_ylem = $t3_ylem, t3_sort = $t3_sort<br />";
					#print $q;
					print "<h1>$str_level</h1>";
					
					$this->db_query($q);
					print "<br />";
					$lastlevel = $val["level"];
				};

				if ($val["type"]  ==  "open")
				{
					$attr = $val["attributes"];		
					$ylem_name = $this->convert_charset($attr["nimetus"]);
					$yid = $val["attributes"]["id"];
					/*
					print "<b>pushing</b><br />";
					*/
					$str_level++;
					array_push($ylem_list,$ylem_name);
					array_push($ylem_ilist,$yid);
				};
			} 
		}
		print "<h1>$counter</h1>";
		if ($counter > 0)
		{
			$this->sync_with_temp("ut_struktuurid","temp_ut_struktuurid");
		}
		else
		{
			$this->db_query("DROP TABLE $table");
		}
	}
	
	/**  
		
		@attrib name=import_tootajad params=name nologin="1"
		
		
		@returns
		
		
		@comment

	**/
	function import_tootajad($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		print "<pre>";
		print_r(htmlspecialchars($contents));
		print "</pre>";
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};
	
		// R.I.P. parser
		xml_parser_free($parser);
		$tootajad_table = $this->create_temp_table("ut_tootajad");	
		$ametid_table = $this->create_temp_table("ut_ametid");	
		$tootajad_view_table = $this->create_temp_table("tootajad_view");	
		/*
		$q = "DELETE FROM ut_tootajad";
		$this->db_query($q);
		$q = "DELETE FROM ut_ametid";
		$this->db_query($q);
		$q = "DELETE FROM tootajad_view";
		$this->db_query($q);
		*/
		foreach($values as $token)
		{
			if ( ($token["tag"] == "tootaja") && ($token["type"] == "open") )
			{
				$t_attr = $token["attributes"];
				// lisame uue t88taja baasi
				$this->quote($t_attr);
				// collect the data for later use
				$enimi = $this->convert_charset($t_attr["enimi"]);
				$pnimi = $this->convert_charset($t_attr["pnimi"]);
				$tid = $t_attr["id"];
				$veeb = $t_attr["veeb"];
				$koht = "";
				$ruum = $t_attr["ruum"];
				$email = $t_attr["email"];
				$markus = $this->convert_charset($t_attr["markus"]);
				$mobiil = $t_attr["mobiil"];
				$sisetel = $t_attr["sisetel"];
				$sisetel_nospaces = str_replace(" ", "", $sisetel);
				$pritel = $t_attr["pritel"];
				$tootajad_view = array();
				$amt_data = array();
				$ruum_data = array();
				$pcode = $t_attr["isikukood"];
				$born_year = "19".$pcode[1].$pcode[2];
				$born_month = $pcode[3].$pcode[4];
				$born_day = $pcode[5].$pcode[6];
				$uname = $t_attr["kasutajatunnus"];
//echo "byear = $born_year , bm = $born_month , bd = $born_day <br>";
			}

			if ( ($token["tag"] == "amet") && ($token["type"] == "complete") )
			{
				$attr = $token["attributes"];
				$this->quote($attr);
				$nimi = $this->convert_charset($attr["nimi"]);
				$ysid = $attr["ysid"];
				$eriala = $this->convert_charset($attr["eriala"]);
				$markus = $this->convert_charset($attr["markus"]);
				$koht = "";
				if (strlen($attr["koht"]) > 0)
				{	
					$koht = $this->convert_charset($attr["koht"]);

					$koht = preg_replace("/\s$/","&nbsp;",$koht);
				};
				$eriala = preg_replace("/\s$/","&nbsp;",$eriala);

				/*
				Lisaks tuleb koormuse import ymber teha selliselt, et kui koormus on 1,
				 siis j2etakse koormus_view lahter tyhjaks. kui koormus on
				midagi muud, kui 1, siis kirjutatakse sama v22rtus nii tulpa koormus kui
				koormus_view, koormus_view lahtrisse lisatakse veel ka
				tyhik ja t2ht "k".
				*/
				$koormus = (float)$attr["koormus"];
				if ($koormus == 1)
				{
					$koormus_view = "";
				}
				else
				{
					$koormus_view = " " . $koormus . " k";
				};
				$st_id = $attr["struktuur"] . $tid;
				$pikknimi = $eriala . $nimi . $koormus_view;
                                $this->quote($pikknimi);

				$q = "INSERT INTO $ametid_table (struktuur_id,nimi,koormus,jrk,markus,tootaja_id,eriala,tel,koht,koormus_view,ysid,st_id)
					VALUES ('$attr[struktuur]','$nimi','$attr[koormus]','$attr[jrk]',
						'$markus','$tid','$eriala','$attr[tel]','$koht','$koormus_view','$ysid','$st_id')";
				print $q;
				$this->db_query($q);
				// first check, whether we have anything already
				if (!empty($amt_data[$st_id][$attr["jrk"]]))
				{
					$amt_data[$st_id][$attr["jrk"]] .= ", " . $pikknimi;
				}
				else
				{
					$amt_data[$st_id][$attr["jrk"]] = $pikknimi;
				};
	
				if (!empty($koht))
				{
					if (!empty($ruum_data[$st_id][$attr["jrk"]]))
					{
						$ruum_data[$st_id][$attr["jrk"]] .= ", " . $koht;
					}
					else
					{
						$ruum_data[$st_id][$attr["jrk"]] = $koht;
					};
				};
				$tootajad_view[$attr[struktuur]][] = array(
					"tootaja_id" => $tid,
					"info" => $eriala . $nimi . $koormus_view,
					"tel" => $attr["tel"],
					"ruum" => $koht,
					"ysid" => $ysid,
					"jrk" => $attr["jrk"],
					"struktuur_id" => $attr["struktuur"],
					"markus" => $markus,
				);
					
			}

			if ( ($token["tag"] == "kraad") && ($token["type"] == "complete") )
			{
				$attr = $token["attributes"];
				$_haru = $this->convert_charset($attr["haru"]);
				$_kraad = $this->convert_charset($attr["kraad"]);
				if ($_haru)
				{
					$kraad[] = "$_kraad ($_haru)";
				}
				else
				{
					$kraad[] = "$_kraad";
				};

			}

			if ( ($token["tag"] == "tootaja") && ($token["type"] == "close") )
			{
				$q = "SELECT * FROM $tootajad_table WHERE id = '$tid'";
				$this->db_query($q);
				$row = $this->db_next();
				$row = false;
				if (is_array($kraad))
				{
					$realkraad = join(", ",$kraad);
				}
				else
				{
					$realkraad = "";
				};
				if (!$row)
				{
					$this->quote(&$realkraad);
					$q = "INSERT INTO $tootajad_table (id,enimi,pnimi,email,veeb,ruum,markus,mobiil,sisetel,pritel,kraad,born_year, born_month, born_day, sisetel_nospaces,username) 
						VALUES ('$tid','$enimi','$pnimi','$email','$veeb','$ruum','$markus','$mobiil','$sisetel','$pritel','$realkraad','$born_year', '$born_month', '$born_day', '$sisetel_nospaces','$uname')";
					print $q;
					print "<br />";
					$this->db_query($q);
				};
				$kraad = array();

				foreach($amt_data as $str_id => $items)
                                {
                                        $q = "";
					ksort($items);
					list($low_id,) = each($items);
                                        if (sizeof($items) > 0)
                                        {
                                                $amts = join(", ",$items);
                                                $q = "UPDATE $ametid_table SET nimi_pikk = '$amts',jrk='$low_id' WHERE st_id = '$str_id'";
                                        }
                                        if (!empty($q))
                                        {
                                                print $q;
                                                print "<br />";
                                                $this->db_query($q);
                                        };
                                };
			
				if (is_array($ruum_data))
				{	
					foreach($ruum_data as $str_id => $items)
					{
						$q = "";
						//ksort($items);
						//list($low_id,) = each($items);
						if (sizeof($items) > 0)
						{
							$amts = join(", ",$items);
							if (strlen($amts) > 0)
							{
								$q = "UPDATE $ametid_table SET koht = '$amts' WHERE st_id = '$str_id'";
							};
						}
						if (!empty($q))
						{
							print $q;
							print "<br />";
							$this->db_query($q);
						};
					};
				};


				if (is_array($tootajad_view))
				{
					foreach($tootajad_view as $str_id => $items)
					{
							if (sizeof($items) == 1)
							{
								// just write it out
								$fieldnames = join(",",array_keys($items[0]));
								$fieldvalues = join(",",map("'%s'",array_values($items[0])));
								$q = "INSERT INTO $tootajad_view_table ($fieldnames) VALUES ($fieldvalues)";
								$this->db_query($q);
								print $q;
								print "<br />";
								flush();
							}
							else
							if (sizeof($items) > 1)
							{
								usort($items, create_function('$a,$b','if ($a["jrk"] > $b["jrk"]) return 1; if ($a["jrk"] < $b["jrk"]) return -1; return 0;'));
								$tmp = $items[0];
								$info = $ruum = array();
								array_walk($items,create_function('$val,$key,$info','$info[] = $val["info"];'),&$info);
								array_walk($items,create_function('$val,$key,$ruum','if (strlen($val["ruum"])) { $ruum[] = $val["ruum"];};'),&$ruum);
								$tmp["info"] = join(", ",$info);
								$ruum = array_unique($ruum);
								$tmp["ruum"] = join(", ",$ruum);
								//$tmp["ruum"] = $ruum;
								$fieldnames = join(",",array_keys($tmp));
								$fieldvalues = join(",",map("'%s'",array_values($tmp)));
								$q = "INSERT INTO $tootajad_view_table ($fieldnames) VALUES ($fieldvalues)";
								$this->db_query($q);
								print $q;
								print "<br />";
								flush();
							}
					};
				}
			}
			print "<br />";

		}
		// kahjuks ma ei saa mysql-ist k2tte ainult neid, millel count=0, niet ma teen
		// tsykli
		$q = "SELECT ut_struktuurid.id,nimetus,COUNT(nimi) AS cnt FROM ut_struktuurid
			LEFT OUTER JOIN $ametid_table ON (ut_struktuurid.id = ${ametid_table}.struktuur_id)
			GROUP BY ut_struktuurid.id ORDER BY cnt";
		$this->db_query($q);
		$queries = array();
		while($row = $this->db_next())
		{
			if ($row["cnt"] == 0)
			{
				$q = "INSERT INTO $ametid_table (struktuur_id,st_id) 
					VALUES ('$row[id]','$row[id]')";
				$queries[] = $q;
			};
		}
		foreach($queries as $query)
		{
			$this->db_query($query);
		};
		$this->sync_with_temp("ut_ametid","temp_ut_ametid");
		$this->sync_with_temp("ut_tootajad","temp_ut_tootajad");
		$this->sync_with_temp("tootajad_view","temp_tootajad_view");
		print "all done!<br />";

	}

	function convert_charset($source)
	{
		// iso-8859-4 -> iso-8859-15 according to specification
		$retval = $source;
		// suur katusega S
		$retval = str_replace(chr(0xA9),chr(0xA6),$retval);
		// v2ike katusega s
		$retval = str_replace(chr(0xB9),chr(0xA8),$retval);
		// suur katusega Z
		$retval = str_replace(chr(0xAE),chr(0xB4),$retval);
		// vaike katusega Z
		$retval = str_replace(chr(0xBE),chr(0xB8),$retval);

		return $retval;
	}
	
	function import_oppekava($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};

		$table = $this->create_temp_table("ut_oppekavad");


		print "<pre>";
		print_r(htmlspecialchars($contents));
		print "</pre>";
		// R.I.P. parser
		xml_parser_free($parser);
		//$q = "DELETE FROM ut_oppekavad";
		//$this->db_query($q);
		$oppekava_url = $oppeaasta_url = "";
		$aasta_urls = array();
		$st_id = array();
		$cnt = 0;
		foreach($values as $key => $val)
		{
			if ( ($val["tag"] == "struktuur") && ($val["type"] == "complete") )
			{
				$st_id[] = $val["attributes"]["id"];
			};
			if ( ($val["tag"] == "oppekava_url") && ($val["type"] == "complete") )
			{
				$oppekava_url = $val["value"];
			};
			if ( ($val["tag"] == "oppeaasta_url") && ($val["type"] == "complete") )
			{
				$oppeaasta_url = $val["value"];
			};
			if ( ($val["tag"] == "aasta") && ($val["type"] == "complete"))
			{
				$aasta = $val["value"];
				$aasta_id = $val["attributes"]["id"];
				$aasta_url = str_replace("[oppekava_id]",$id,$oppeaasta_url);
				$aasta_url = str_replace("[oppeaasta_id]",$aasta_id,$aasta_url);
				$aasta_url = str_replace("[oppeaasta]",$aasta,$aasta_url);
				$aasta_urls[$aasta] = "<a href='$aasta_url'>$aasta a</a>";
			};
			if ( ($val["tag"] == "oppekava")  && ( ($val["type"] == "complete") || ($val["type"] == "open")) )
			//if ( ($val["tag"] == "oppekava")  && ($val["type"] == "open") )
			{
				$aasta_urls = array();
				$attr = $val["attributes"];		
				$nimetus = $this->convert_charset($attr["nimetus"]);
				$nimetus_en = $attr["nimetus_en"];
				$oppeaste = $attr["oppeaste"];
				$id = $attr["id"];
				$kood = $attr["kood"];
			};

			if ( ($val["tag"] == "oppekava") && ( ($val["type"] == "complete") || ($val["type"] == "close") ))
			{
				if (strlen($nimetus) == 0)
				{
					$attr = $val["attributes"];		
					$nimetus = $this->convert_charset($attr["nimetus"]);
					$nimetus_en = $attr["nimetus_en"];
					$oppeaste = $attr["oppeaste"];
					$id = $attr["id"];
					$kood = $attr["kood"];
				};
				$this->quote($nimetus);
				$this->quote($nimetus_en);
				$kava_url = str_replace("[oppekava_id]",$id,$oppekava_url);
				$this->quote($kava_url);
				ksort($aasta_urls);
				$aasta_url_str = join(", ",$aasta_urls);
				$this->quote($aasta_url_str);
				$st_id_str = join(",",$st_id);
				$st_id = array();
				$q = "INSERT INTO $table (id,kood,nimetus,nimetus_en,oppekava_url,oppeaasta_url,oppeaste,st_id)
					VALUES('$id','$kood','$nimetus','$nimetus_en','$kava_url','$aasta_url_str','$oppeaste','$st_id_str')";				
				print $q;
				print "<br />";
				$nimetus = $nimetus_en = $oppeaste = $id = $kood = "";
				$this->db_query($q);
				$cnt++;
			};



		}

		if ($cnt > 0)
		{
			$this->sync_with_temp("ut_oppekavad","temp_ut_oppekavad");
		}
		else
		{
			$this->db_query("DROP TABLE $table");
		}
	}
	
	function import_oppeasted($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};
		// R.I.P. parser
		xml_parser_free($parser);
		$table = $this->create_temp_table("ut_oppeasted");	
		/*
		$q = "DELETE FROM ut_oppeasted";
		$this->db_query($q);
		*/
		$cnt = 0;
		foreach($values as $key => $val)
		{
			if ( ($val["tag"] == "oppeaste")  && ($val["type"] == "complete") )
			{
				$attr = $val["attributes"];		
				$nimetus = $this->convert_charset($attr["nimetus"]);
				$id = $attr["id"];
				$jrk = $attr["jrk"];
				$this->quote($nimetus);
				$q = "INSERT INTO $table (id,nimetus,jrk)
					VALUES('$id','$nimetus','$jrk')";
				print $q;
				print "<br />";
				$this->db_query($q);
				$cnt++;
			};



		}

		if ($cnt > 0)
		{
			$this->sync_with_temp("ut_oppeasted","temp_ut_oppeasted");
		}
		else
		{
			$this->db_query("DROP TABLE $table");
		}
	}

	function import_oppekavad($args  = array())
	{
                       $fn = "https://www.is.ut.ee/pls/xml/oppeained.xml";
                        $this->db_query("DROP TABLE IF EXISTS imporditud_oppekavad");
                        $this->db_query("CREATE TABLE imporditud_oppekavad(id int primary key auto_increment, ainekood varch
ar(255), maht varchar(10), nimetus varchar(255), annotatsioon text, eesmark text)");

                        $parser = xml_parser_create();
                        xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
                        xml_parse_into_struct($parser,file_get_contents($fn),&$keys,&$values);
                        xml_parser_free($parser);

                        echo "parsed <Br>\n";
                        flush();

                        //echo dbg::dump($keys);
                        foreach($keys as $entry)
                        {
                                if ($entry["tag"] == "aine" && $entry["type"] == "complete")
                                {
                                        $ainekood = $entry["attributes"]["ainekood"];
                                        $this->quote(&$ainekood);

                                        $maht = $entry["attributes"]["maht"];
                                        $this->quote(&$maht);

                                        $nimetus = $entry["attributes"]["nimetus"];
                                        $this->quote(&$nimetus);

                                        $anno = $entry["attributes"]["annotatsioon"];
                                        $this->quote(&$anno);

                                        $eesm = $entry["attributes"]["eesmark"];
                                        $this->quote(&$eesm);

                                        $this->db_query("INSERT INTO imporditud_oppekavad(ainekood, maht, nimetus, annotatsi
oon, eesmark) values('$ainekood', '$maht', '$nimetus', '$anno', '$eesm')");
                                        echo $nimetus." <Br>";
                                }
                        }
	
	}
	
	function import_oppevormid($args = array())
	{
		$contents = $args["source"];
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		xml_parse_into_struct($parser,$contents,&$values,&$tags);
		if (xml_get_error_code($parser))
		{
			$this->bitch_and_die($parser,$contents);
		};
		// R.I.P. parser
		xml_parser_free($parser);
		$table = $this->create_temp_table("ut_oppevormid");	
		/*
		$q = "DELETE FROM ut_oppevormid";
		$this->db_query($q);
		*/
		$cnt = 0;
		foreach($values as $key => $val)
		{
			if ( ($val["tag"] == "oppevorm")  && ($val["type"] == "complete") )
			{
				$attr = $val["attributes"];		
				$nimetus = $this->convert_charset($attr["nimetus"]);
				$id = $attr["id"];
				$jrk = $attr["jrk"];
				$this->quote($nimetus);
				$q = "INSERT INTO $table (id,nimetus,jrk)
					VALUES('$id','$nimetus','$jrk')";
				print $q;
				print "<br />";
				$this->db_query($q);
				$cnt++;
			};



		}
		if ($cnt > 0)
		{
			$this->sync_with_temp("ut_oppevormid","temp_ut_oppevormid");
		}
		else
		{
			$this->db_query("DROP TABLE $table");
		}
	}

	function bitch_and_die(&$parser,&$contents)
	{
		$err = xml_error_string(xml_get_error_code($parser));
		print "Viga l&auml;hteandmetes<br />"; 
		print "<font color='red'><strong>$err</strong></font><br />";
		$b_idx = xml_get_current_byte_index($parser);
		$frag = substr($contents,$b_idx - 100, 200);
		$pref = htmlspecialchars(substr($frag,0,100));
		$suf = htmlspecialchars(substr($frag,101));
		$offender = htmlspecialchars(substr($frag,100,1));
		print "Tekstifragment: <pre>" .  $pref . "<font color='red'><strong> ---&gt;&gt;$offender&lt;&lt;---</strong></font>$suf" . "</pre>";
		die();
	}

	function create_temp_table($table)
	{
		$q = "SHOW CREATE TABLE $table";
		$this->db_query($q);
		$row = $this->db_next();
		$dt = $row["Create Table"];
		preg_match("/CREATE TABLE `(\w*)` \(/",$dt,$m);
		$tablename = $m[1];
		$tempname = "temp_" . $table;
		$q = "DROP TABLE IF EXISTS $tempname";
		$this->db_query($q);
		$tempstruct = preg_replace("/(CREATE TABLE `)(\w*)(` \()/","\\1temp_\\2\\3",$dt);
		print $tempstruct;
		$this->db_query($tempstruct);
		return "temp_" . $table;
	}

	function sync_with_temp($table,$temptable)
	{
		$q = "DELETE FROM $table";
		$this->db_query($q);
		$q = "INSERT INTO $table SELECT * FROM $temptable"; 
		$this->db_query($q);
		$q = "DROP TABLE $temptable";
		$this->db_query($q);
	}
}
?>

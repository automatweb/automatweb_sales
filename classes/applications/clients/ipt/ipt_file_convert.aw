<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/ipt/ipt_file_convert.aw,v 1.5 2007/12/06 14:33:06 kristo Exp $
// ipt_file_convert.aw - IPT Failide konvertimine 
/*

@classinfo syslog_type=ST_IPT_FILE_CONVERT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property file type=fileupload 
	@caption Fail

	@property res type=text
	@caption Tulemus

@default group=files

	@property files type=table store=no no_caption=1


@groupinfo files caption="Failid"


@reltype FILE value=1 clid=CL_FILE
@caption Fail
*/

class ipt_file_convert extends class_base
{
	const AW_CLID = 1061;

	function ipt_file_convert()
	{
		$this->init(array(
			"tpldir" => "applications/clients/ipt/ipt_file_convert",
			"clid" => CL_IPT_FILE_CONVERT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "res":
				if (!is_oid($arr["obj_inst"]->id()) || !is_array($arr["obj_inst"]->meta("cur_data")))
				{
					return PROP_IGNORE;
				}
				if ($arr["request"]["gzf"] == 1)
				{
					$this->_proc($arr["obj_inst"]);
				}
				$prop["value"] = html::href(array(
					"url" => aw_url_change_var("gzf", 1),
					"caption" => t("Downloadi tulemus")
				));
				break;

			case "files":
				$this->_files($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_post_save($arr)
	{	
		if (is_uploaded_file($_FILES["file"]["tmp_name"]))
		{
			$this->_fup($arr, $_FILES["file"]["tmp_name"]);
			$arr["obj_inst"]->save();
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _fup($arr, $f)
	{
		// slurp
		$ls = file($f);
		// parse
		$data = array();
		$cur_test_id = 0;
		foreach($ls as $l)
		{
			if (trim($l) == "")
			{
				continue;
			}
			if (substr($l, 0, 2) == "NN")
			{
				if ($in_test)
				{
					// finish prev
					$data[$cur_test_id] = $cur_test;
				}
				// test starts
				$in_test = true;
				$cur_test = array();	
				//$cur_test_id++;
			}
			else
			if ($in_test)
			{
				$id = substr($l, 0, 2);
				$rem = substr($l, 2);
				switch($id)
				{
					case "XY":
						$line_pts = preg_split("/\s+/", trim($rem));
						$cur_test_id = $line_pts[4];

					case "TY":
					case "PK":
					case "TT":
					case "LN":
					case "AL":
					case "LA":
					case "-1":
						$cur_test[$id] = preg_split("/\s+/", trim($rem));
						break;

					case "  ":
						$cur_test["data"][] = preg_split("/\s+/", trim($l));
						break;

					default:
						die(sprintf(t("error, unrecognized line %s"), $l));
				}
			}
		}

		if ($in_test)
		{
			$data[$cur_test_id] = $cur_test;
		}
		$arr["obj_inst"]->set_meta("cur_data", $data);

		// attach file to list as well
		$f = new file();
		$file_oid = $f->save_file(array(
			"parent" => $arr["obj_inst"]->id(),
			"content" => $this->_proc($arr["obj_inst"], true),
			"name" => "ipt.zip",
			"type" => "application/zip"
		));
		$fo = obj($file_oid);
		$fo->set_meta("orig_file", join("", $ls));
		$fo->set_meta("orig_file_name", $_FILES["file"]["name"]);
		$fo->save();

		$arr["obj_inst"]->connect(array(
			"to" => $fo->id(),
			"type" => "RELTYPE_FILE"
		));
	}

	function _proc($o, $ret = false)
	{
		$d = $o->meta("cur_data");

		$files = array();
		foreach($d as $nr => $inf)
		{
			// rewrite date
			$d = $inf["XY"][3][0].$inf["XY"][3][1];
			$m = $inf["XY"][3][2].$inf["XY"][3][3];
			$y = $inf["XY"][3][6].$inf["XY"][3][7];
			$inf["XY"][3] = $d."/".$m."/".$y;
			$file = array(
				"fld" => $inf["TY"][0],
				"lines" => array()
			);
			switch($inf["TT"][0])
			{
				case "PA":
					$file["name"] = "SWT".sprintf("%03d", $nr);
					$file["lines"][0] = array("013");
					$file["lines"][1] = array(
						3,
						$inf["XY"][0],
						$inf["XY"][1],
						$inf["XY"][2],
						"-",
						"-",
						"-",
						$inf["XY"][3]
					);
					$file["lines"][2] = array(
						"001", "047", "046"
					);
					break;

				case "HE":
					$file["name"] = "DPT".sprintf("%03d", $nr);
					$file["lines"][0] = array("005");
					$file["lines"][1] = array(
						4,
						$inf["XY"][0],
						$inf["XY"][1],
						$inf["XY"][2],
						"-",
						"-",
						"-",
						$inf["XY"][3]
					);
					$file["lines"][2] = array(
						"001", "113", "016", "013"
					);
					break;

				case "HP":
					$file["name"] = "SLP".sprintf("%03d", $nr);
					$file["lines"][0] = array("020");
					$file["lines"][1] = array(
						6,
						$inf["XY"][0],
						$inf["XY"][1],
						$inf["XY"][2],
						"-",
						"-",
						"-",
						$inf["XY"][3]
					);
					$file["lines"][2] = array(
						"001", "005", "072", "113", "016", "013"
					);
					break;

				default:
					$file["name"] = "PA".sprintf("%03d", $nr);
					$file["lines"][0] = array("014");
					$file["lines"][1] = array(
						1,
						$inf["XY"][0],
						$inf["XY"][1],
						$inf["XY"][2],
						"-",
						"-",
						"-",
						$inf["XY"][3]
					);
					$file["lines"][2] = array(
						"001"
					);
					break;
			}

				$d = array();
			foreach($inf["data"] as $idx => $dat_row)
			{
				$j = ceil((double)$dat_row[0])+1.0;
				$parand = 1.0-(0.015*($j-1));
				$calc = (((63.5+(0.56*0.56*6.0*$j))/(63.5+$j*6.0))*(((double)$dat_row[1])/(0.2*100.0))*(63.5*0.5*100.0)/16.0)/9.81;
				$d = "";
				switch($inf["TT"][0])
				{
					case "PA":
						$d = array(
							number_format(round($dat_row[0], 2), 2, ".", ""),
							$dat_row[2],
							$dat_row[1]
						);

						/*if (count($file["lines"])-1 == 2)
						{
							$d[0] = "0.00";
						}
						else
						{
							$file["lines"][count($file["lines"])-1][0] = (count($file["lines"])-1) == 3 ? "0.00" : number_format(round($dat_row[0], 2), 2, ".", "");
							$file["lines"][count($file["lines"])-1][1] = $dat_row[2];
							$file["lines"][count($file["lines"])-1][2] = $dat_row[1];
						}*/
						// 1st row is 0, first row data
						if ((count($file["lines"])-1) == 2)
						{
							$d[0] = "0.00";
							$d[1] = $dat_row[2];
							$d[2] = $dat_row[1];
						}						
						else
						{
							// prev row depth, cur row data
							$d[0] = number_format(round($inf["data"][$idx-1][0], 2), 2, ".", "");
							$d[1] = $dat_row[2];
							$d[2] = $dat_row[1];
						}
						break;		

					case "HE":
						$d = array(
							number_format(round($dat_row[0], 2), 2, ".", ""),
							floor(((double)$dat_row[1])*$parand),
							round($calc, 1),
							$dat_row[1]
						);
						if (count($file["lines"])-1 == 2)
						{
							$d[0] = "0.00";
						}
						else
						{
							$file["lines"][count($file["lines"])-1][1] = number_format(round($dat_row[0], 2), 2, ".", "");
							$file["lines"][count($file["lines"])-1][1] = floor(((double)$dat_row[1])*$parand);
							$file["lines"][count($file["lines"])-1][2] = round($calc, 1);
							$file["lines"][count($file["lines"])-1][3] = $dat_row[1];
						}
						break;		

					case "HP":
						if ($dat_row[3] == "H")
						{
							$d = array(
								number_format(round($dat_row[0], 2), 2, ".", ""),
								"-",
								$dat_row[2],
								floor(((double)$dat_row[1])*$parand),
								number_format(round($calc, 1), 1, ".", ""),
								$dat_row[1],
							);
							$file["lines"][count($file["lines"])-1][3] = floor(((double)$dat_row[1])*$parand);
							$file["lines"][count($file["lines"])-1][4] = number_format(round($calc, 1), 1, ".", "");
							$file["lines"][count($file["lines"])-1][5] = $dat_row[1];
						}
						else
						if ($dat_row[3] == "P")
						{
							$d = array(
								number_format(round($dat_row[0], 2), 2, ".", ""),
								number_format(round($dat_row[1], 2), 2, ".", ""),
								$dat_row[2],
								"-",
								"-",
								"-",
							);
						}
						else
						{
							die(sprintf(t("Viga faili parsimisel, andmete neljas tulp peaks olema kas H v6i P, hetkel on %s"), $dat_row[3]));
						}
						break;		

					default:
						$d = array(
							number_format(round($dat_row[0],2), 2, ".", "")
						);
						break;
				}
				$file["lines"][] = $d;
			}
			if ($inf["TT"][0] == "PA")
			{
				$d[0] = number_format(round($inf["data"][$idx][0], 2), 2, ".", "");
				$file["lines"][] = $d;
			}
			$files[] = $file;
		}

		// create zip file

		// make temp dir
		$fld = aw_ini_get("server.tmpdir")."/ipt_".gen_uniq_id();
		mkdir($fld);
		//echo "crea $fld <Br>";
		foreach($files as $file)
		{
			$pt = $fld."/".$file["fld"];
			if (!is_dir($pt))
			{
				mkdir($pt);
				//echo "created folder $pt <br>";
			}

			$str = "";
			foreach($file["lines"] as $line)
			{
				$str.=join(" ", $line)."\r\n";
			}
			$fn = $pt."/".$file["name"];
			//echo "creating file $fn <br>";
			$f = fopen($fn ,"w");
			fwrite($f, $str);
			fclose($f);
		}		

		// zip
		chdir($fld);
		$cmd = aw_ini_get("server.zip_path")." -r ipt.zip *";
		$res = `$cmd`;
		$fc = $this->get_file(array("file" => "ipt.zip"));
		// clean up
		foreach($files as $file)
		{
			$pt = $fld."/".$file["fld"];
			$fn = $pt."/".$file["name"];
			unlink($fn);
		}		

		foreach($files as $file)
		{
			$pt = $fld."/".$file["fld"];
			if (is_dir($pt))
			{
				rmdir($pt);
			}
		}
		unlink("ipt.zip");
		rmdir($fld);

		if ($ret)
		{
			return $fc;
		}
		header("Content-type: application/zip");
		header("Content-Disposition: filename=ipt.zip");
		die($fc);
	}

	function _init_files_t(&$t)
	{
		$t->define_field(array(
			"name" => "orig",
			"caption" => t("Originaalfail"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "res",
			"caption" => t("Tulemusfailid"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kes"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
	}
	
	function _files($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_files_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FILE")) as $c)
		{
			$fo = $c->to();
			$fi = $fo->instance();
			$t->define_data(array(
				"orig" => html::href(array(
					"url" => $this->mk_my_orb("get_orig_file", array("file" => $c->prop("to"))),
					"caption" => $fo->meta("orig_file_name")
				)),
				"res" => html::href(array(
					"url" => $fi->get_url($fo->id(), $fo->name()),	
					"caption" => t("ipt.zip")
				)),
				"who" => $fo->createdby(),
				"when" => $fo->created()
			));
		}

		$t->set_default_sortby("when");
		$t->set_default_sorder("desc");
		$t->sort_by();
	}

	/**
		@attrib name=get_orig_file
		@param file required type=int
	**/
	function get_orig_file($arr)
	{
		$fo = obj($arr["file"]);
		header("Content-type: text/plain");
		die($fo->meta("orig_file"));
	}
}
?>

<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/geoinfo/geoinfo_manager.aw,v 1.8 2008/02/15 10:30:49 robert Exp $
// geoinfo_manager.aw - Geoinfo haldus 
/*

@classinfo syslog_type=ST_GEOINFO_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

	@property general_tb type=toolbar store=no no_caption=1

	@property name type=textbox table=objects
	@caption Nimi

	@property xml_source type=chooser field=meta method=serialize
	@caption XML-i allikas

	@property xml_url type=textbox field=meta method=serialize
	@caption XML faili url

	@property xml_file type=relpicker reltype=RELTYPE_XML field=meta method=serialize store=connect
	@caption XML fail

	@property xml_schema type=relpicker reltype=RELTYPE_SCHEMA field=meta method=serialize store=connect
	@caption XML-i skeem

	@property xml_type type=chooser field=meta method=serialize
	@caption XML-i t&uuml;&uuml;p

	@property xml_unique type=select field=meta method=serialize
	@caption XML-i unikaalne väli

@default group=rels_mgr

	@property rels_mgr_tb type=toolbar store=no no_caption=1

	@property rels_mgr_table type=table store=no no_caption=1

@default group=data_mgr

	@property data_mgr_tb type=toolbar store=no no_caption=1

	@property data_mgr_table type=table store=no no_caption=1

@groupinfo rels_mgr caption=V&auml;ljad
@groupinfo data_mgr caption=Andmed

@reltype XML value=1 clid=CL_FILE
@caption XML sisend

@reltype SCHEMA value=2 clid=CL_FILE
@caption XML skeem
*/

class geoinfo_manager extends class_base
{
	const AW_CLID = 1373;

	function geoinfo_manager()
	{
		$this->init(array(
			"tpldir" => "applications/geoinfo",
			"clid" => CL_GEOINFO_MANAGER
		));
	}

	function get_data_clids($arr)
	{
		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_GEOINFO_DATA,
		));
		$clids = array();
		foreach($ol->arr() as $o)
		{
			if(is_oid($o->prop("obj_oid")))
			{
				$obj = obj($o->prop("obj_oid"));
				$clid = $obj->class_id();
				$clids[$clid] = $clid;
			}
		}
		return $clids;
	}

	/**
	@attrib name=export all_args=1 nologin=1
	**/
	function export_kmz($arr)
	{
		$arr["obj_inst"] = obj($arr["id"]);
		$args["tmp"] = "";
		$args["sttmp"] = "";
		$this->read_template('kml.tpl');
		if(count($arr["sel"]))
		{
			foreach($arr["sel"] as $oid)
			{
				$args["oid"] = $oid;
				$args = $this->parse_data_to_kml($args);
			}
			
		}
		elseif($arr["url"])
		{
			$oids = $this->import_from_xml($arr);
			foreach($oids as $oid)
			{
				$args["oid"] = $oid;
				$args = $this->parse_data_to_kml($args);
			}
		}
		else
		{
			die();
		}
		extract($args);
		$this->vars(array(
			"styles" => $sttmp,
			"placemarks" => $tmp,
			"filename" => "data.kmz",
		));
		//header('Content-type: application/vnd.google-earth.kml+xml; charset=UTF-8');
		//header('Content-Disposition: attachment; filename="data.kml"');
		$fc = iconv(aw_global_get("charset"), "UTF-8", $this->parse());
		//die($fc);
		$fld = aw_ini_get("server.tmpdir")."/kmz_".gen_uniq_id();
		mkdir($fld);
		$fn = $fld."/data.kml";
		$f = fopen($fn ,"w");
		fwrite($f, $fc);
		fclose($f);	
		// zip
		chdir($fld);
		$cmd = aw_ini_get("server.zip_path")." -r data.kmz *";
		$res = `$cmd`;
		$fc = $this->get_file(array("file" => "data.kmz"));
		// clean up
		unlink($fn);	
		unlink("ipt.zip");
		rmdir($fld);
		header("Content-type: application/zip");
		header("Content-Disposition: filename=data.kmz");
		die($fc);
	}

	function parse_data_to_kml($arr)
	{
		extract($arr);
		$o = obj($oid);
		if($obj_oid = $o->prop("obj_oid"))
		{
			$prop_list = $o->get_property_list();
			foreach($prop_list as $prop)
			{
				$p = obj($o->parent());
				if($p->class_id() == CL_GEOINFO_MANAGER && is_oid($obj_oid))
				{
					$o2 = obj($obj_oid);
					$clid = $o2->class_id();
					$rels = $p->meta("rels");

					if($field = $rels["cl".$clid]["props"][$prop["name"]])
					{
						$pr_list = $o2->get_property_list();
						$ptype = $pr_list[$field]["type"];
						$objprop = $o2->prop($field);
						if($ptype == "chooser" || $ptype == "select")
						{
							$i = get_instance($o->class_id());
							$pr = array("name" => $field);
							$i->get_property(array("request" => array(), "obj_inst" => $o2, "prop" => &$pr));
							$objprop = $pr["options"][$objprop];
						}
						elseif(is_oid($objprop) && !strlen(strpos($ptype,"text")))
						{
							$propobj = obj($objprop);
							$objprop = $propobj->name();
						}
						$o->set_prop($prop["name"], $objprop);
					}
				}
			}
		}
		$style = false;
		$i_style = $o->prop("icon_style");
		$i_color = $o->prop("icon_color");
		$i_size = $o->prop("icon_size");
		$l_color = $o->prop("label_color");
		$l_size = $o->prop("label_size");
		$i_transp = $o->prop("icon_transp");
		$l_transp = $o->prop("label_transp");
		if($i_style || $i_color!="ffffff" || $i_size!="1" || $l_color!="ffffff" || $l_size!="1" || $l_transp != "255" || $i_transp != "255")
		{
			if($i_style)
			{
				$ii = get_instance(CL_IMAGE);
				$i_style = $ii->get_url_by_id($i_style);
			}
			else
			{
				$i_style = "http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png";
			}
			$this->vars(array(
				"icon_url" => $i_style,
				"icon_color" => $this->colorfix($i_color),
				"icon_size" => $i_size,
				"icon_transp" => dechex($i_transp),
				"label_color" => $this->colorfix($l_color),
				"label_size" => $l_size,
				"label_transp" => dechex($l_transp),
				"id" => "style".$oid
			));
			$sttmp .= $this->parse("styles");
			$style = true;
		}
		$this->vars(array(
			"name" => $o->name(),
			"coord_x" => $o->prop("coord_x"),
			"coord_y" => $o->prop("coord_y"),
			"address" => $o->prop("address"),
			"desc1" => $o->prop("desc1"),
			"desc2" => $o->prop("desc2"),
			"view_range" => strlen($o->prop("view_range"))?$o->prop("view_range"):0,
			"view_heading" => strlen($o->prop("view_heading"))?$o->prop("view_heading"):0,
			"view_tilt" => strlen($o->prop("view_tilt"))?$o->prop("view_tilt"):0,
			"icon_height" => strlen($o->prop("icon_height"))?$o->prop("icon_height"):0,
		));
		if($style)
		{
			$this->vars(array(
				"style" => "style".$oid
			));
		}
		else
		{
			$this->vars(array(
				"style" => "",
			));
		}
		for($i=1;$i<=10;$i++)
		{
			$this->vars(array(
				"userta".$i => $o->prop("userta".$i),
				"usertf".$i => $o->prop("usertf".$i),
			));
		}
		$tmp .= $this->parse("placemarks");
		$arr["tmp"] = $tmp;
		$arr["sttmp"] = $sttmp;
		return $arr;
	}

	function colorfix($c)
	{
		$fixed = strtolower(substr($c,4,2).substr($c,2,2).substr($c,0,2));
		return $fixed;
	}

	/**
	@attrib name=import all_args=1
	**/
	function import_from_xml($arr)
	{
		$arr["obj_inst"] = obj($arr["id"]);
		$oids = array();
		if($arr["url"])
		{
			$xmldata = @file_get_contents($arr["url"]);
		}
		elseif($fname = $arr["obj_inst"]->prop("xml_source") == "url")
		{
			$xmldata = @file_get_contents($fname);
		}
		elseif($fid = $arr["obj_inst"]->prop("xml_file"))
		{
			$fo = obj($fid);
			$tmp = $this->get_file(array("file" => $fo->prop("file")));
			if ($tmp !== false)
			{
				$xmldata = $tmp;
			}
		}
		if($xmldata)
		{
			$xt = $arr["obj_inst"]->prop("xml_type");
			$x = xml_parser_create();
			xml_parse_into_struct($x, $xmldata, $vals, $index);
			xml_parser_free($x);
			$allvars = array();
			$rels = $arr["obj_inst"]->meta("rels");
			$arr["t"] = "xml";
			$curvars = array();
			foreach($vals as $val)
			{
				if($xt == "tags")
				{
					if($val["level"] == 2 && $val["type"] == "complete")
					{
						if($tmpvar = $rels["xml"]["fields"][$val["tag"]])
						{
							foreach($tmpvar as $tmp)
							{
								$allvars[$tmp] = iconv("UTF-8",aw_global_get("charset"),trim($val["value"]));
							}
						}
					}
					elseif($val["level"] == 3 && $val["type"] == "complete")
					{
						if($tmpvar = $rels["xml"]["fields"][$val["tag"]])
						{
							foreach($tmpvar as $tmp)
							{
								$curvars[$tmp] = iconv("UTF-8",aw_global_get("charset"), trim($val["value"]));
							}
						}
					}
					elseif($val["level"] == 2 && $val["type"] == "close")
					{
						$unique = $arr["obj_inst"]->prop("xml_unique");
						$uprops = $rels["xml"]["fields"][$unique];
						$c = 0;
						foreach($uprops as $uprop)
						{
							$ol = new object_list(array(
								"class_id" => CL_GEOINFO_DATA,
								$uprop => $curvars[$uprop]
							));
							if(count($ol->arr()))
							{
								foreach($ol->ids() as $oid)
								{
									$oids[$oid] = $oid;
								}
								$curvars = array();
								$c = 1;
							}
						}
						if($c)
						{
							continue;
						}
						$o = obj();
						$o->set_class_id(CL_GEOINFO_DATA);
						$o->set_parent($arr["obj_inst"]->id());
						foreach($allvars as $avr => $avl)
						{
							$o->set_prop($avr, $avl);
						}
						foreach($curvars as $cvr => $cvl)
						{
							$o->set_prop($cvr, $cvl);
						}
						$o->save();
						$oids[$o->id()] = $o->id();
						unset($o);
						$curvars = array();
					}
				}
				else
				{
					if($val["level"] == 3 && count($val["attributes"]))
					{
						$curvars = array();
						foreach($val["attributes"] as $attrib => $v)
						{
							if($tmpvar = $rels["xml"]["fields"][$attrib])
							{
								foreach($tmpvar as $tmp)
								{
									$curvars[$tmp] = iconv("UTF-8",aw_global_get("charset"), trim($v));
								}
							}
						}
						$unique = $arr["obj_inst"]->prop("xml_unique");
						$uprops = $rels["xml"]["fields"][$unique];
						$c = 0;
						foreach($uprops as $uprop)
						{
							$ol = new object_list(array(
								"class_id" => CL_GEOINFO_DATA,
								$uprop => $curvars[$uprop]
							));
							if(count($ol->arr()))
							{
								foreach($ol->ids() as $oid)
								{
									$oids[$oid] = $oid;
								}
								$curvars = array();
								$c = 1;
							}
						}
						if($c)
						{
							continue;
						}
						$o = obj();
						$o->set_class_id(CL_GEOINFO_DATA);
						$o->set_parent($arr["obj_inst"]->id());
						foreach($curvars as $cvr=>$cvl)
						{
							$o->set_prop($cvr, $cvl);
						}
						$o->save();
						$oids[$o->id()] = $o->id();
						unset($o);
					}
				}
			}
		}
		if($arr["url"])
		{
			return $oids;
		}
		else
		{
			return $arr["ru"];
		}
	}

	/**
	@attrib name=get_address all_args=1 nologin=1
	**/
	function ajax_get_address($arr)
	{
		$url = "http://maps.google.com/maps/geo?q=";
		$url .= str_replace(" ", "+", $arr["adr"]);
		$url .= "&output=csv&key=ABQIAAAADTjnybLqawGMIE6x59CllBSx4X0DfXMRq8wFp_lyFFw5FRz33BTLfotmWEiUFWfMheqe3OIbITzp2A";
		$data = @file_get_contents($url);
		die($data.",".$arr["num"]);
	}

	function xml_get_fields($arr)
	{
		if($fid = $arr["obj_inst"]->prop("xml_schema"))
		{
			$xt = $arr["obj_inst"]->prop("xml_type");
			$fo = obj($fid);
			$tmp = $this->get_file(array("file" => $fo->prop("file")));
			if ($tmp !== false)
			{
				$xmldata = $tmp;
			}
			$x = xml_parser_create();
			xml_parse_into_struct($x, $xmldata, $vals, $index);
			xml_parser_free($x);
			$fields = array(0=>"");
			foreach($vals as $val)
			{
				if($xt == "tags")
				{
					if($val["type"] == "complete")
					{
						$fields[$val["tag"]] = $val["tag"];
					}
				}
				else
				{
					if($val["level"] == 3 && count($val["attributes"]))
					{
						foreach($val["attributes"] as $attrib=>$v)
						{
							$fields[$attrib] = $attrib;
						}
					}
				}
			}
			return $fields;
		}
		else
		{
			return array();
		}
	}

	function _get_general_tb($arr)
	{
		foreach($vals as $val)
		{
			if($val["type"] == "complete")
			{
				echo $val["tag"].'<br />';
			}
		}
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "submit",
			"img" => "import.gif",
			"tooltip" => t("Impordi XML-ist"),
			"url" => $this->mk_my_orb("import",array(
				"id" => $arr["obj_inst"]->id(),
				"ru" => get_ru()
			))
		));
	}

	function _get_rels_mgr_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_save_button();
	}

	function _set_rels_mgr_table($arr)
	{
		$tmp = array();
		foreach($arr["request"] as $var => $val)
		{
			$tmpvar = explode('--', $var);
			if(strlen($tmpvar[1]))
			{
				$tmp[$tmpvar[1]]["fields"][$val][] = $tmpvar[0];
				$tmp[$tmpvar[1]]["props"][$tmpvar[0]] = $val;
			}
		}
		$arr["obj_inst"]->set_meta("rels", $tmp);
	}

	function init_rels_mgr_table(&$t, $arr)
	{
		$t->set_caption(t("Väljade seostamine"));
		/*$t->define_field(array(
			"name" => "id",
			"caption" => "ID",
			"numeric" => 1,
			"align" => "center",
		));*/
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Väli"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "xml",
			"caption" => "XML",
			"align" => "center",
		));
		$cldata = aw_ini_get("classes");
		$clids = $this->get_data_clids($arr);
		foreach($clids as $clid)
		{
			$cl_name = $cldata[$clid]["name"];
			$t->define_field(array(
				"name" => "cl".$clid,
				"caption" => t($cl_name),
				"align" => "center",
			));
		}
		$t->sort_by();
		$t->set_default_sortby("field");
	}

	function get_rels_mgr_fields($arr)
	{
		extract($arr);
		$rels = $obj_inst->meta("rels");
		foreach($prop as $pn=>$pd)
		{
			//$data["id"] = $id;
			$data["field"] = $pd;
			$data["xml"] = html::select(array(
				"name" => $pn."--xml",
				"options" => $xmlfields,
				"value" => $rels["xml"]["props"][$pn],
			));
			foreach($clids as $clid)
			{
				$data["cl".$clid] = html::select(array(
					"name" => $pn."--cl".$clid,
					"options" => $cl_proplist[$clid],
					"value" => $rels["cl".$clid]["props"][$pn]
				));
			}
		}
		return $data;
	}

	function _get_rels_mgr_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->init_rels_mgr_table($t, $arr);

		$args["clids"] = $this->get_data_clids($arr);
		$cfi = new cfgform();
		foreach($args["clids"] as $clid)
		{
			$args["cl_proplist"][$clid] = $cfi->get_property_list($clid);
		}
		$args["xmlfields"] = $this->xml_get_fields($arr);
		$list = $cfi->get_property_list(CL_GEOINFO_DATA);
		unset($list[0]);
		$args["obj_inst"] = $arr["obj_inst"];
		foreach($list as $pn=>$pd)
		{
			$args["prop"] = array($pn => $pd);
			$data = $this->get_rels_mgr_fields($args);
			$t->define_data($data);
		}
	}

	function _get_data_mgr_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_GEOINFO_DATA), $arr["obj_inst"]->id(), '', array());
		$tb->add_search_button(array(
			"pn" => "add_data",
			"multiple" => 1,
			"clid" => CL_GEOINFO_DATA
		));
		$tb->add_delete_button();
		$tb->add_save_button();
		$tb->add_button(array(
			"name" => "export",
			"img" => "nool1.gif",
			"tooltip" => t("Ekspordi KMZ"),
			"url" => "#",
			"onClick" => "document.forms.changeform.action = '".$this->mk_my_orb("export",array(
				"id" => $arr["obj_inst"]->id(),
			))."';document.forms.changeform.submit()",
		));
		$tb->add_button(array(
			"name" => "getcoords",
			"img" => "import.gif",
			"tooltip" => t("Impordi koordinaadid"),
			"url" => "javascript:getcoords()"
		));
	}

	function init_data_mgr_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "coord_x",
			"caption" => t("X koordinaat"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "coord_y",
			"caption" => t("Y koordinaat"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
		$t->set_caption(t("Andmeobjektid"));
		$t->sort_by();
		$t->set_default_sortby("name");
	}

	function _get_data_mgr_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->init_data_mgr_table($t);
		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_GEOINFO_DATA
		));
		foreach($ol->arr() as $o)
		{
			$address = $o->prop("address");
			$fixedaddr = str_replace(".","",str_replace(" ","+",str_replace(",","",$address)));
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => html::obj_change_url($o->id()),
				"coord_x" => html::textbox(array(
					"name" => "coord_x-".$o->id(),
					"value" => $o->prop("coord_x"),
					"size" => 20
				)),
				"coord_y" => html::textbox(array(
					"name" => "coord_y-".$o->id(),
					"value" => $o->prop("coord_y"),
					"size" => 20
				)),
				"address" => '<div id="adr'.$o->id().'" style="display:none">'.$fixedaddr.'</div>'.$address,
			));
		}
	}

	function _set_data_mgr_table($arr)
	{
		if($arr["request"]["add_data"])
		{
			$ids = explode(",",$arr["request"]["add_data"]);
			foreach($ids as $oid)
			{
				$data = obj($oid);
				$data->set_parent($arr["obj_inst"]->id());
				$data->save();
			}
		}
		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_GEOINFO_DATA
		));
		foreach($ol->arr() as $o)
		{
			$cx = $arr["request"]["coord_x-".$o->id()];
			$cy = $arr["request"]["coord_y-".$o->id()];
			$o->set_prop("coord_x", $cx);
			$o->set_prop("coord_y", $cy);
			$o->save();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "xml_source":
				$prop["options"] = array(
					"file" => "Uploaditud fail",
					"url" => "Url"
				);
				if(!$prop["value"])
					$prop["value"] = "file";
				break;
			case "xml_unique":
				$prop["options"] = $this->xml_get_fields($arr);
				break;
			case "xml_type":
				$prop["options"] = array(
					"tags" => t('V&auml;&auml;rtused on tagides &lt;coord_x&gt;23.409&lt;/coord_x&gt;')."<br />",
					"props" => t('V&auml;&auml;rtused on omadustes &lt;item coord_x="23.409" /&gt;')
				);
				if(!$prop["value"])
					$prop["value"] = "tags";
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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_data"] = "0";
	}

	function callback_generate_scripts($arr)
	{
		$this->read_template("scripts.tpl");
		$this->vars(array(
			"obj_id" => $arr["obj_inst"]->id(),
			"query_url" => aw_ini_get("baseurl")
		));
		return $this->parse();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>

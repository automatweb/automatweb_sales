<?php
// $Header: /home/cvs/automatweb_dev/classes/import/taavi_import.aw,v 1.7 2008/01/31 13:54:39 kristo Exp $
// taavi_import.aw - Taavi import 
/*

@classinfo syslog_type=ST_TAAVI_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta 
@default method=serialize

@property url type=textbox
@caption Url

@property port type=textbox size=4
@caption Port

@property impordi type=text
@caption Impordi



*/

class taavi_import extends class_base
{
	function taavi_import()
	{
		$this->init(array(
			"tpldir" => "import/taavi_import",
			"clid" => CL_TAAVI_IMPORT
		));
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "impordi":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb(
						"import",
						array(
							"id" => $arr["obj_inst"]->id(),
						),
						"taavi_import"),
					"caption" => t("Impordi"),
				));
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**	
		@attrib name=import params=name default=0
		@param id required
		@returns
		@comment

	**/
	function import($arr)
	{
		$var = $this->export_xml($arr["id"]);
		header("Content-type: text/xml; encoding='UTF-8';");
		print $var;
		die();
	}

	/**	
		@attrib name=get_raw params=name
		@param id required
		@returns
		@comment

	**/
	function get_raw($arr)
	{
		include(aw_ini_get("basedir")."/classes/protocols/xmlrpc/xmlrpc_lib.aw");
		$import_obj = obj($arr["id"]);
		$port = $import_obj->prop("port");
		$url = $import_obj->prop("url");
		$remove = array("http://" , "ftp://");
		$url = str_replace($remove, "", $url);
		$pos = strpos($url, '/');
		$domain = substr($url , 0 , $pos);
		$path = substr($url , $pos ,strlen($url));
		$client = new IXR_Client($domain, $path, $port);
		$client->query("server.getinfo");
		$data = $client->getResponse();
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		die();
	}

	function find_asula($arr)
	{
		$ret = ''; $asula = '';
		$prop = explode(" " , $arr["data"]);
		foreach($prop as $txt)
		{
			$txt = trim($txt, ",");
			if(substr_count($txt, $arr["asula"]) > 0)
			{
				$ret = $asula;
			}
			else
			{
				$asula = $txt;
			}
		}
		if($ret != '')
		{
			$ret = ucwords(strtolower($ret)).' '.$arr["asula"];
		}
		return $ret;
	}

	function export_xml($id)
	{
		include(aw_ini_get("basedir")."/classes/protocols/xmlrpc/xmlrpc_lib.aw");
		$import_obj = obj($id);
		$port = $import_obj->prop("port");
		$url = $import_obj->prop("url");
		$remove = array("http://" , "ftp://");
		$url = str_replace($remove, "", $url);
		$pos = strpos($url, '/');
		$domain = substr($url , 0 , $pos);
		$path = substr($url , $pos ,strlen($url));
		$client = new IXR_Client($domain, $path, $port);
		$client->query("server.getinfo");
		$data = $client->getResponse();
		$vars = array("eesnimi","perekonnanimi","synniaeg","aadress","ia_tanav","ia_maja","ia_korter","ia_talu","ia_pindeks","ia_linn","ia_asula","ia_vald","ia_maakond","ia_riik","haridustase","eriala","oppeasutus","telefon","mobiiltelefon","lyhinumber","e_post","ametikoht_nimetus","ametijuhend_viit","ruum","palgaaste","asutus","allasutus","yksus_nimetus","yksus_id","prioriteet","on_peatumine","peatumine_pohjus","toole_tulek_kp","on_asendaja","asendamine_tookoht");
	
		$t = localtime();
		$t[4] = $t[4] + 1;
		foreach($t as $key => $xdata)
		{
			if($xdata < 10)
			{
				$t[$key] = "0".$data;
			}
		}
		$all_xml = '<tootajad><ekspordi_aeg>'.($t[5] + 1900).$t[4].$t[3].'T'.$t[2].':'.$t[1].':'.$t[0].'</ekspordi_aeg>';
		foreach($data as $skey => $val)
		{
			$all_xml = $all_xml.'<tootaja><tootaja_id>'.($skey).'</tootaja_id>';
		//	$struct["tootajad"]["tootaja"]["tootaja_id"]=$skey;
			foreach($vars as $tag)	
			{
				switch($tag)
				{
					case "eesnimi":
						$struct["tootajad"]["tootaja"][$tag] =  ucwords(strtolower($val["EESNIMI"]));
						break;
						
					case "perekonnanimi":
						$struct["tootajad"]["tootaja"][$tag] = ucwords(strtolower($val["PERENIMI"]));
						break;
						
					case "synniaeg":
						$day = $val["SYNNIPAEV"];
						$struct["tootajad"]["tootaja"][$tag] = 
						substr($day, 6, 7).'.'.substr($day, 4, 2).'.'.substr($day, 0, 4);
						break;
						
					case "aadress":
						if(strlen($val["AADRESS"]) > 3)
						{
							$struct["tootajad"]["tootaja"][$tag] = $val["AADRESS"];
						}
						else
						{
							$struct["tootajad"]["tootaja"][$tag] = $val["AADRESS1"];
						}
						break;
						
					case "ia_tanav":
						$prop = explode(" ",$val["AADRESS1"]);
						$ret = '';
						foreach($prop as $txt)
						{	
							if(!($txt[0] > 0) || ($txt[2] == '.'))
							{
								if(strlen($ret > 0))
								{
									$ret = $ret.' ';
								}
								$ret = $ret.$txt;
							}
							else
							{
								break;
							}
							if(($txt == 'küla') || ($txt == 'talu'))
							{
								$ret = null;
								break;
							}							
							if($txt[strlen($txt)-1] == ',')
							{
								break;
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;

					case "ia_maja":
						$prop = explode(" ",$val["AADRESS1"]);
						$ret = null;
						foreach($prop as $txt)
						{
							if($txt[0] > 0)
							{
								$maja = explode("-",$txt);
								if(strlen($maja[0])>0)
								{
									$ret = $maja[0];
								}
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;

					case "ia_korter":
						$prop = explode(" ",$val["AADRESS1"]);
						$ret = null;
						foreach($prop as $txt)
						{
							if($txt[0] > 0)
							{
								$dir = substr(strrchr($txt, "-"), 1 ,  strlen(strrchr($txt, "-"))-1);
								if(strlen($dir)>0)
								{
									$ret = $dir;
								}
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;

					case "ia_talu":
						$ret = $this->find_asula(array(
							"data" => $val["AADRESS1"],
							"asula" => 'talu'
						));
						if($ret == '')
						{
							$ret = $this->find_asula(array(
								"data" => $val["AADRESS"],
								"asula" => 'talu'
							));
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;
					
					case "ia_pindeks":
						$prop = explode(" ",$val["INDEKS1"]);
						$ret = null;
						foreach($prop as $txt)
						{
							if((strlen($txt) == 6) && ($txt[5] == ','))
							{
								$txt = substr($txt , 0 , 5);
							}
							if((strlen($txt) == 5)
							&& ($txt > 10000) && ($txt < 100000)
							)
							{
								$ret = $txt;
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;
					
					case "ia_linn":
						$prop = explode(" ",$val["INDEKS1"]);
						$ret = null;
						foreach($prop as $txt)
						{
							if(
							(substr_count($val["INDEKS1"], 'mk') == 0)
							&& (substr_count($val["INDEKS1"], 'maakond') == 0)
							&& (substr_count($val["INDEKS1"], 'vald') == 0)
							&& (substr_count($val["INDEKS1"], 'maa') == 0)
							)
							{							
								$txt = trim($txt, ",");
								if((strlen($txt) == 6) && ($txt[5] == ','))
								{
									$txt = substr($txt , 0 , 5);
								}
								if(	
									!($txt[0] > 0)
									&& (substr_count($txt, 'vald') == 0)
									&& (substr_count($txt, 'maa') == 0)
									&& (substr_count($txt, 'mk') == 0)
									&& (substr_count($txt, 'maakond') == 0)
									&& (strlen($txt) > 2)
								)
								{
									$ret = $txt;
								}
								if(
									(substr_count($txt, 'vald') > 0)
									|| (substr_count($txt, 'mk') > 0)
									|| (substr_count($txt, 'maakond') > 0)
								)
								{
									$ret = '';
								}
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;

					
					case "ia_asula":
						$ret = null;						
						if(
						   (substr_count($val["INDEKS1"], 'mk') > 0)
						|| (substr_count($val["INDEKS1"], 'maakond') > 0)
						|| (substr_count($val["INDEKS1"], 'vald') > 0)
						|| (substr_count($val["INDEKS1"], 'maa') > 0)
						)
						{
							$prop = explode(" ",$val["INDEKS1"]);

							foreach($prop as $txt)
							{
								$txt = trim($txt, ",");
								if((strlen($txt) == 6) && ($txt[5] == ','))
								{
									$txt = substr($txt , 0 , 5);
								}
								if(	
									!($txt[0] > 0)
									&& (substr_count($txt, 'vald') == 0)
									&& (substr_count($txt, 'maa') == 0)
									&& (substr_count($txt, 'mk') == 0)
									&& (substr_count($txt, 'maakond') == 0)
									&& (strlen($txt) > 2)
								)
								{
									$ret = $txt;
								}
								if(
									(substr_count($txt, 'vald') > 0)
									|| (substr_count($txt, 'mk') > 0)
									|| (substr_count($txt, 'maakond') > 0)
								)
								{
									$ret = '';
								}
							}
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;
					
					case "ia_vald":
						$ret = $this->find_asula(array(
							"data" => $val["INDEKS1"],
							"asula" => 'vald'
						));
						if($ret == '')
						{
							$ret = $this->find_asula(array(
								"data" => $val["AADRESS1"],
								"asula" => 'vald'
							));
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;
						
					case "ia_maakond":
						$ret = $this->find_asula(array(
							"data" => $val["INDEKS1"],
							"asula" => 'maakond'
						));
						
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;					

					case "ia_riik":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "haridustase": //annab moment numbrites
						$struct["tootajad"]["tootaja"][$tag]=$val["HARIDUS"];
						break;
						
					case "eriala":
						$struct["tootajad"]["tootaja"][$tag]=$val["ERIALA"];
						break;
						
					case "oppeasutus":
						$struct["tootajad"]["tootaja"][$tag] = $val["KOOL"];
						break;
						
					case "telefon":
						$struct["tootajad"]["tootaja"][$tag] = $val["TELEFON"];
						break;
	
					case "mobiiltelefon":
						$struct["tootajad"]["tootaja"][$tag] = $val["TELEFON2"];
						break;

					case "lyhinumber":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "e_post":
						$struct["tootajad"]["tootaja"][$tag] = strtolower($val["EMAIL"]);
						break;
						
					case "ametikoht_nimetus":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "ametijuhend_viit":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "ruum":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "palgaaste"://kui see summa on 0, siis "TARIIF" on miski arv
						$struct["tootajad"]["tootaja"][$tag] = $val["SUMMA"];
						if($val["SUMMA"] == 0)
						{
							$struct["tootajad"]["tootaja"][$tag] = $val["TARIIF"];
						}
						break;
						
					case "asutus"://tühi väli
						$struct["tootajad"]["tootaja"][$tag] = $val["ASUTUS"];
						break;
						
					case "allasutus":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "yksus_nimetus":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "yksus_id":
						$struct["tootajad"]["tootaja"][$tag] = $val["ALLYKSUS"];
						break;

					case "prioriteet":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "on_peatumine":
						$struct["tootajad"]["tootaja"][$tag] = 0;
						break;
						
					case "peatumine_pohjus":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
						
					case "toole_tulek_kp":
						$ret = null;
						if(strlen($val["MEILE_TOOL"]) > 1)
						{
							$ret = $val["MEILE_TOOL"].'T00:00:00';
						}
						$struct["tootajad"]["tootaja"][$tag] = $ret;
						break;
						
					case "on_asendaja":
						$struct["tootajad"]["tootaja"][$tag] = 0;
						break;
						
					case "asendamine_tookoht":
						$struct["tootajad"]["tootaja"][$tag] = null;
						break;
				}					
			}
			foreach($struct["tootajad"]["tootaja"] as $key => $val)
			{	
				if(strlen($val) > 0)
				{
					$all_xml = $all_xml.'<'.$key.'>'.$val.'</'.$key.'>';
				}
				else
				{
					$all_xml = $all_xml.'<'.$key.'/>';
				}
			}
			$all_xml = $all_xml.'</tootaja>';
		}
		$all_xml = $all_xml.'</tootajad>';
		$all_xml = iconv("ISO-8859-4","UTF-8", $all_xml);
		return $all_xml;
	}
}
?>

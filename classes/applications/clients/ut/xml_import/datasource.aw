<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/ut/xml_import/datasource.aw,v 1.6 2007/12/12 12:50:47 kristo Exp $
// type of the data, I'm storing it in the subclass field of the objects table
// so that I can retrieve all sources with the same type with one query
define("DS_XML",1);

/*
	@default table=objects
	@default field=meta
	@default group=general

	@property subclass type=select field=subclass 
	@caption Andmete tüüp
	
	@default method=serialize

	@property type type=select 
	@caption Source tüüp

	@property fullpath type=textbox editonly=1
	@caption Faili asukoht

	@property url type=textbox editonly=1 size=60
	@caption Faili url

	@classinfo no_status=1 syslog_type=ST_DATASOURCE maintainer=kristo
*/

class datasource extends class_base
{
	function datasource($args = array())
	{
		$this->init(array(
			"clid" => CL_DATASOURCE,
		));
	}

	function get_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "type":
				$data["options"] = array(
					"0" => t("lokaalne fail (serveris)"),
					"1" => t("http"),
					"2" => t("https"),
				);
				break;

			case "subclass":
				$data["options"] = array(
					DS_XML => t("XML"),
				);
				break;

			case "fullpath":
				if ($args["obj_inst"]->prop("type") != 0)
				{
					$retval = PROP_IGNORE;
				};
				break;
			
			case "url":
				if ($args["obj_inst"]->prop("type") == 0)
				{
					$retval = PROP_IGNORE;
				};
				break;
			
		};
		return $retval;
	}

	////
	// !Retrieves data from a datasource - at the moment works with 
	// http(s) only.
	function retrieve($args = array())
	{
		extract($args);
		$obj = new object($id);
		$c = get_instance("cache");
                classload("core/date/date_calc");
                if ($ct = $c->file_get_ts("xml_datasource_$id", mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"))))
                {
			return $ct;
                }
	
		$type = $obj->prop("type");
		$url = escapeshellarg($obj->prop("url"));
		if (($type == 2) || ($type == 1))
		{
			$read = "";
			$curl = $this->cfg["curl_path"];
			/*
			if (!file_exists($curl))
			{
				 error::raise(array(
					"id" => "ERR_DS_NO_AGENT",
					"msg" => sprintf(t("%s not found"), $curl)
				));
			};
			*/
			$fp = popen ("$curl $url", "r");
			while(!feof($fp))
			{
				$read.= fread($fp,16384);
			}
			pclose($fp);
			$c->file_set("xml_datasource_$id", $read);
			return $read;
		}
	}

	/** Raw interface for accessing the data from a source. Mainly for debugging 
		
		@attrib name=fetch params=name default="0"
		
		@param id required type=int
		
		@returns
		
		
		@comment
		purposes.

	**/
	function fetch($args = array())
	{
		$read = $this->retrieve($args);
		header("Content-Type: text/xml");
		print $read;
		exit;
	}

}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/archive/document_archive.aw,v 1.6 2008/02/14 22:07:35 kristo Exp $
// document_archive.aw - Dokumendiarhiiv 
/*

@classinfo syslog_type=ST_DOCUMENT_ARCHIVE relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property date_format type=chooser orient=vertical default=1
@caption Kuupäeva formaat

@property contents type=callback callback=callback_get_contents group=test
@caption Test

@groupinfo test caption=Test

@reltype SOURCE value=1 clid=CL_MENU
@caption Võta dokumente
*/

class document_archive extends class_base
{
	function document_archive()
	{
		$this->init(array(
			"clid" => CL_DOCUMENT_ARCHIVE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "date_format":
				$prop["options"] = array(
					LC_DATE_FORMAT_SHORT => "pp.kk.aa",
					LC_DATE_FORMAT_SHORT_FULLYEAR => "pp.kk.aaaa",
					LC_DATE_FORMAT_LONG => "p. kkkkk aa",
					LC_DATE_FORMAT_LONG_FULLYEAR => "p. kkkkk aaaa",
				);
				break;
		};
		return $retval;
	}
	
	//// create a list of events that should be used
	function get_event_sources($id)
	{
		$o = new object($id);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_SOURCE",
		));
		$sources = array();
		foreach($conns as $conn)
		{
			$sources[$conn->prop("to")] = $conn->prop("to");
		}
		return $sources;
	}

	//// create a list of events that should be used
	function get_events($arr)
	{
		$o = new object($arr["id"]);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_SOURCE",
		));
		$parents = array();
		foreach($conns as $conn)
		{
			$parents[] = $conn->prop("to");
		};

		$rv = array();

		return $rv;

		if (!empty($parents))
		{
			// I need limit this list

			// start and end are in $arr[range] .. so how do I do the limiting
			// the field that I shall modify is on prop->doc_modified
			$doclist = new object_list(array(
				"parent" => $parents,
				"class_id" => array(CL_DOCUMENT,CL_PERIODIC_SECTION),
				"site_id" => array(),
				"lang_id" => array(),
				/*
				new object_list_filter(array(
					"logic" => ">=",
					"conditions" => array(
						"doc_modified" => $arr["range"]["start"],
					)
				))
				*/
			));

			foreach($doclist->ids() as $id)
			{
				$doc_obj = new object($id);
				$rv[] = array(
					"start" => $doc_obj->prop("doc_modified"),
					"url" => aw_ini_get("baseurl") . "/" . $o->id(),
					"name" => $doc_obj->name(),
				);
			};
		};

		return $rv;


	}

	function get_days_with_events($arr)
	{
		$o = new object($arr["id"]);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_SOURCE",
		));
		$parents = array();
		foreach($conns as $conn)
		{
			$parents[] = $conn->prop("to");
		};

		$rv = array();

		if (!empty($parents))
		{
			$doclist = new object_list(array(
				"parent" => $parents,
				"class_id" => array(CL_DOCUMENT,CL_PERIODIC_SECTION),
				"site_id" => array(),
				"lang_id" => array(),
				"doc_modified" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["start"]),
			));

			$base = aw_ini_get("baseurl");

			foreach($doclist->ids() as $id)
			{
				$doc_obj = new object($id);
				$rv[] = array(
					"start" => $doc_obj->prop("doc_modified"),
					"url" => $base . "/" . $o->id(),
				);
			};
		};
		return $rv;
	}

	function callback_get_contents($arr)
	{
		// list 
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_SOURCE",
		));
		$parents = array();
		foreach($conns as $conn)
		{
			$parents[] = $conn->prop("to");
		};
		if (!empty($parents))
		{
			// okey, fine, I get a list of documents. and by default I'll have
			// to show things in a range

			// 1. calculate the date range
			//	1.1 - if no range is given in the url, show default (which is last week)
			//	1.2 - if range is given, then show articles from that day only	

			// I can't interfere with the usual thing, I need to put the document archive inside
			// a document and then show it.
				
			// 2. show articles in the range

			// 3. implement commenting as well, but that really is a completely different topic
			$doclist = new object_list(array(
				"parent" => $parents,
				"class_id" => array(CL_DOCUMENT,CL_PERIODIC_SECTION),
				"site_id" => array(),
				"lang_id" => array(),
			));
			print "<pre>";
			print_r($doclist);
			print "</pre>";
			print sizeof($doclist->ids());
		};

	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		$date = aw_global_get("date");
		list($d,$m,$y) = explode("-",$date);
		$start = mktime(0,0,0,$m,$d,$y);
		$end = mktime(23,59,59,$m,$d,$y);
		// now I need a list of documents on that day
		$o = new object($arr["alias"]["to"]);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_SOURCE",
		));
		$parents = array();
		foreach($conns as $conn)
		{
			$parents[] = $conn->prop("to");
		};
		$_date = $start;
		$date_est = date("d", $_date).". ".aw_locale::get_lc_month(date("m", $_date))." ".date("Y", $_date);
		$d = new document();
		$rv = "<h2>$date_est</h2>";
		if (!empty($parents))
		{
			$pstr = join(",",$parents);
			$q = "SELECT objects.oid AS oid FROM objects LEFT JOIN documents ON objects.oid = documents.docid WHERE class_id IN (7,29) AND parent IN ($pstr) AND status != 0 AND documents.modified >= $start AND documents.modified <= $end";
			$this->db_query($q);
			$row = $this->db_next();
			$rv .= $d->gen_preview(array(
				"docid" => $row["oid"],	
				"tpl_auto" => 1,
			));
		};
		return $rv;
	}

	function request_execute($obj)
	{
		$arx = array();
		$arx["alias"]["to"] = $obj->id();
		return $this->parse_alias($arx);
	}


}
?>

<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_treeview/otv_ds_roadinfo.aw,v 1.1 2008/02/03 22:44:29 dragut Exp $
// otv_ds_roadinfo.aw - Maanteeinfo DS 
/*

@classinfo syslog_type=ST_OTV_DS_ROADINFO relationmgr=yes no_comment=1 no_status=1

@default table=objects
@default group=general

@property xml_file_url type=textbox size=100 field=meta method=serialize
@caption XML faili asukoht

@property update_cache type=text store=no
@caption

@groupinfo content caption="Sisu"
@default group=content

@property content_table type=table no_caption=1
@Caption Sisu

reltype SEARCH_ROADINFO_FORM value=1 clid=CL_SEARCH_ROADINFO
caption Maanteeinfo otsing
*/

class otv_ds_roadinfo extends class_base
{
	const AW_CLID = 655;

/////////////////////////////////////////////////////////////////////////
// Here i define all fields, that can be in the table
/////////////////////////////////////////////////////////////////////////
	var $all_fields = array(
		"tl_id" => "id",
		"tl_type" => "type",
		"tl_date_from" => "date_from",
		"tl_date_to" => "date_to",
		"tl_time_from" => "time_from",
		"tl_time_to" => "time_to",
		"tl_timestamp_from" => "timestamp_from",
		"tl_timestamp_to" => "timestamp_to",
		"tl_county" => "county",
		"tl_road_number" => "road_number",
		"tl_road_name" => "road_name",
		"tl_km_from" => "km_from",
		"tl_km_to" => "km_to",
		"tl_place_from" => "place_from",
		"tl_place_to" => "place_to",
		"tl_limit_name" => "limit_name",
		"tl_limit_size" => "limit_size",
		"tl_limit_description" => "limit_description",
		"tl_work_name" => "work_name",
		"tl_work_description" => "work_description",
		"tl_map_op" => "map_op",
	);
	var $search_params = array();
//	var $content = array();

	function otv_ds_roadinfo()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/roadinfo/otv_ds_roadinfo",
			"clid" => CL_OTV_DS_ROADINFO
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "content_table":
				$this->create_content_table($arr);
				break;

			case "update_cache":
				$prop['value'] = html::href(array(
					"url" => $this->mk_my_orb("update_cache", array(
								"id" => $arr['obj_inst']->id(),
							)),
					"caption" => "Uuenda cache"
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
	

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
	
/////////////////////////////////////////////////////////////////////////
// this is used by object_treeview_v2 to display the selectable columns
// on object_treeview_v2 "tulbad" tab
/////////////////////////////////////////////////////////////////////////
	function get_fields()
	{
		return $this->all_fields;
	}
/////////////////////////////////////////////////////////////////////////
// this is used by object_treeview_v2 to show the table.
// here i put in the table the trafficlimits
/////////////////////////////////////////////////////////////////////////
	function get_objects($o, $fld = NULL, $parent = NULL)
	{
//		return $this->parse_xml($o);
		return $this->create_content(array(
			"object" => $o,
		));
	}
	
/////////////////////////////////////////////////////////////////////////
// this is used by object_treeview_v2 to show the folders part.
// here i dont have any folders to show, or something that looks
// nice displayd as folders, so i return empty array, othervise it
// gives an error
//
// and for the record, as i remember, i didnt get it to work anyway
// but as i said, i didn't need it so i didn't spend much time to
// fool around with it.
/////////////////////////////////////////////////////////////////////////
	function get_folders($o)
	{
		return array();
	}
	
/////////////////////////////////////////////////////////////////////////
// this is also used by object_treeview_v2, obviously it makes some
// acl checks, but here it isn't used, and i don't know how to use it.
/////////////////////////////////////////////////////////////////////////
	function check_acl()
	{
		return true;
	}
	
/////////////////////////////////////////////////////////////////////////
// here i generate the content to whatever function needs it.
// For example, the content shown on the site comes from here and
// also comes the otv_ds_roadinfo "sisu" tab content from here
/////////////////////////////////////////////////////////////////////////
	function create_content($arr)
	{
		$search_obj_inst = get_instance(CL_SEARCH_ROADINFO);

		$params = $search_obj_inst->parse_get_params(array("type" => "sql_params"));
	
		return $this->db_fetch_array("select * from aw_otv_ds_roadinfo_cache".$params);
	}

/////////////////////////////////////////////////////////////////////////
// this creates the table header for the table showing all
// content of the xml file
// the fields are described in an array called "all_fields", which
// is initialized at the beginning of the class
/////////////////////////////////////////////////////////////////////////
	function _init_content_table(&$t)
	{
		foreach($this->all_fields as $key => $value)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $value
			));
		}
	}

/////////////////////////////////////////////////////////////////////////
// putting data into table, which is shown on datasource Sisu tab
// the content comes from create_content() function
/////////////////////////////////////////////////////////////////////////
	function create_content_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];

		$this->_init_content_table($t);

// setting sortable false, just to keep the
// order of table content
		$t->set_sortable(false);

//		$table_rows = $this->parse_xml($arr['obj_inst']);
		$table_rows = $this->create_content(array(
			"object" => $arr['obj_inst'],
		));
		
		foreach($table_rows as $row)
		{
			$t->define_data($row);
		}
		
//		echo $this->db_fetch_field("select * from aw_otv_ds_roadinfo_cache", "aw_id");
//		arr($this->db_fetch_array("select * from aw_otv_ds_roadinfo_cache"));
	}

/////////////////////////////////////////////////////////////////////////
// parses the xml file and returns an array, which contains the
// data from xml file
/////////////////////////////////////////////////////////////////////////
	function parse_xml($o)
	{
		$data_file_path = $o->prop("xml_file_url");

/*
		$data_file_content = $this->get_file(array(
			"file" => $data_file_path,
		));
*/

// kas $this->get_file()  kuskil urli pealt tuleva faili sisu ei võtagi ???
// ei võtagi ja pole kindel kas peakski tegema

		$data_file_content = file_get_contents($data_file_path);

		if($data_file_content == false)
		{
			die("faili ei &otilde;nnestunud leida!");
		}

		$xml_file_content = parse_xml_def(array("xml" => $data_file_content));
		$values = $xml_file_content[0];

		$trafficlimit = array();
		$trafficlimits = array();

		foreach($values as $key => $value)
		{
			if($value['tag'] == "trafficlimit" && $value['type'] == "open")
			{
				$trafficlimit['tl_id'] = $value['attributes']['id'];
			}
			if($value['tag'] == "property")
			{
// i can use this construction, because i defined the table fieldnames
// same as they are in xml file property attributenames :)
// i just added "tl_" prefix.
// and thats why i use aw_otv_ds_roadinfo_cache table id fieldname
// as tl_id not aw_id
				$trafficlimit['tl_'.$value['attributes']['name']] = $value['value'];
			}

			if($value['tag'] == "trafficlimit" && $value[type] == "close")
			{
				array_push($trafficlimits, $trafficlimit);
				$trafficlimit = array();
			}
		}

		return $trafficlimits;
	}

	/**
		@attrib name=update_cache nologin=1
		@param id required type=int acl=view
	**/
	function update_cache($arr)
	{

		
		ini_set("memory_limit", "1000M");

		$o = new object($arr['id']);

		$this->db_query("DELETE FROM aw_otv_ds_roadinfo_cache");

		$xml_content = $this->parse_xml($o);

//		$conf = $o->meta("saved_conf");

		foreach($xml_content as $tl)
		{
			$sql_command = "INSERT INTO aw_otv_ds_roadinfo_cache SET ";

//			$timestamp_from = "";
//			$timestamp_to = "";

//			$sql_insert_data = array();

//			$sql_insert_data['tl_id'] .= $tl['tl_id'];
//			$sql_insert_data['tl_map_op'] .= $tl['tl_map_op'];

			$tl['tl_km_to'] = "".(float)$tl['tl_km_to'];
			$tl['tl_km_from'] = "".(float)$tl['tl_km_from'];

			if(((int)$tl['tl_date_to'] == 0) || ((int)$tl['tl_date_to'] < 1970))
			{
				$tl['tl_date_to'] = "2038-01-01";
			}

			$tl['tl_timestamp_from'] = strtotime($tl['tl_date_from']);
			$tl['tl_timestamp_to'] = strtotime($tl['tl_date_to']);

			$tl['tl_date_from'] = date("d.m.Y", $tl['tl_timestamp_from']);
			$tl['tl_date_to'] = date("d.m.Y", $tl['tl_timestamp_to']);

//			arr($tl);

			foreach($tl as $key => $value)
			{
				$sql_command .= " ".$key."='".trim($value)."', ";
			}
			$sql_command = substr($sql_command, 0, (strlen($sql_command)-2));
//			echo $sql_command."<br>";
			$this->db_query($sql_command);
/*
			foreach($tl as $key => $value)

			{
/////////////////////////////////////////////////////////////////////////////////////////
// some modifications are made with the data which comes from xml before i but it
// in a db table
/////////////////////////////////////////////////////////////////////////////////////////

				switch($key)
				{

					case "tl_id":
						$sql_insert_data['tl_id'] = $value;
						break;

					case "tl_map_op":
						$sql_insert_data['tl_map_op'] = $value;
						break;

					case "tl_date_from":
						$timestamp = strtotime($value);

						$sql_insert_data['tl_timestamp_from'] = $timestamp;
						$sql_insert_data['tl_date_from'] .= $conf['tl_date_from']['encloser']['left'].date("d.m.Y", $timestamp).$conf['tla_date_from']['encloser']['right'].$conf['tl_date_from']['sep']." ";
						break;

					case "tl_date_to":
						if(((int)$value == 0) || ((int)$value < 1970))
						{
							$value = "2038-01-01";
						}
						$timestamp = strtotime($value);

						$sql_insert_data['tl_timestamp_to'] = $timestamp;
						$sql_insert_data['tl_date_to'] .= $conf['tl_date_to']['encloser']['left'].date("d.m.Y", $timestamp).$conf['tla_date_to']['encloser']['right'].$conf['tl_date_to']['sep']." ";
						break;
					default:
						$sql_insert_data[$conf[$key]['f_name']] .= $conf[$key]['encloser']['left'].$value.$conf[$key]['encloser']['right'].$conf[$key]['sep']." ";
				}
			}
*/
/*
			foreach($conf as $key => $value)

			{
/////////////////////////////////////////////////////////////////////////////////////////
// some modifications are made with the data which comes from xml before i but it
// in a db table
/////////////////////////////////////////////////////////////////////////////////////////

				$sep = $value['sep'];
				$enc_left = $value['encloser']['left'];
				$enc_right = $value['encloser']['right'];

				if(empty($tl[$key]))
				{
					$sep = "";
					$enc_left = "";
					$enc_right = "";
				}

				switch($key)
				{

					case "tl_date_from":
						$timestamp = strtotime($tl[$key]);

						$sql_insert_data['tl_timestamp_from'] = $timestamp;
						$sql_insert_data['tl_date_from'] .= $sep." ".$enc_left.date("d.m.Y", $timestamp).$enc_right;
						break;

					case "tl_date_to":
						if(((int)$tl[$key] == 0) || ((int)$tl[$key] < 1970))
						{
							$tl[$key] = "2038-01-01";
						}
						$timestamp = strtotime($tl[$key]);

						$sql_insert_data['tl_timestamp_to'] = $timestamp;
						$sql_insert_data['tl_date_to'] .= $sep." ".$enc_left.date("d.m.Y", $timestamp).$enc_right;
						break;
					default:
						$sql_insert_data[$value['f_name']] .= $sep." ".$enc_left.$tl[$key].$enc_right;
				}
			}
*/

//			foreach($sql_insert_data as $field_name => $field_value)
//			{
//				$sql_command .= " ".$field_name."='".trim($field_value)."', ";
//			}
			
//			$sql_command = substr($sql_command, 0, (strlen($sql_command)-2));
//			arr($sql_command);
//			$this->db_query($sql_command);
		}
		
		return $this->mk_my_orb("change", array("id" => $o->id()), $o->class_id());

	}
}
?>

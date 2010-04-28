<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_treeview/search_roadinfo.aw,v 1.1 2008/02/03 22:44:29 dragut Exp $
// search_roadinfo.aw - Maanteeinfo otsing 
/*

@classinfo syslog_type=ST_SEARCH_ROADINFO relationmgr=yes

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@default store=no

@groupinfo search_elements caption="Otsingu elemendid"
@default group=search_elements

@property MI type=checkbox ch_value=1
@caption Suurendatud piirkiirusega teel&otilde;igud

@property MT type=checkbox ch_value=1
@caption Massipiiranguga teel&otilde;igud

@property MM type=checkbox ch_value=1
@caption Liikluspiirangutega teel&otilde;igud

@property county type=select
@caption Maakond

@property date_from type=date_select year_from=1990 year_to=2010
@caption alates

@property date_to type=date_select year_from=1990 year_to=2010
@caption kuni

@groupinfo search_results caption="Otsingu tulemused"
@default group=search_results

property search_results_table type=table
caption Otsingu tulemuste tabel

@reltype ROADINFO_DS value=2 clid=CL_OTV_DS_ROADINFO
@caption Maanteeinfo andmeallikas

@reltype RELTYPE_OTV value=3 clid=CL_OBJECT_TREEVIEW_V2
@caption Objektinimekiri v2
*/

class search_roadinfo extends class_base
{
	const AW_CLID = 806;


	var $county_list = array(
		"" => "Kõik",
		"Harju" => "Harju",
	 	"Hiiu" => "Hiiu",
	 	"Ida-Viru" => "Ida-Viru",
	 	"Jõgeva" => "Jõgeva",
	 	"Järva" => "Järva",
	 	"Lääne" => "Lääne",
	 	"Lääne-Viru" => "Lääne-Viru",
	 	"Põlva" => "Põlva",
	 	"Pärnu" => "Pärnu",
	 	"Rapla" => "Rapla",
	 	"Saare" => "Saare",
	 	"Tartu" => "Tartu",
	 	"Valga" => "Valga",
	 	"Viljandi" => "Viljandi",
		"Võru" => "Võru"
	);

	function search_roadinfo()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/roadinfo/search_roadinfo",
			"clid" => CL_SEARCH_ROADINFO
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

			case "county":
			
				$prop['options'] = $this->county_list;

			    break;
		};
		return $retval;
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

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
	//	arr($arr);
		return $this->show(array(
			"id" => $arr["alias"]["target"],
			"doc_id" => $arr['oid'],
		));
		
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		
		$tx = "Otsi";

		$o = $ob->get_first_obj_by_reltype("RELTYPE_OTV");
		$otv = get_instance(CL_OBJECT_TREEVIEW_V2);
		$result_table_html = $otv->show(array("id" => $o->id()));
		
		$this->vars(array(
			"name" => $ob->prop("name"),
			"button_text" => $tx,
			"section" => $arr['doc_id'],
			"form" => $this->get_form_from_obj(array(
				"id" => $arr['id']
			)),
			"result_table" => $result_table_html,
		));
		
		


		
		
		return $this->parse();
	}
/////////////////////////////////////////////////////////////////////////
// the function to show the admin form in the document, it uses
// -- cfg/cfgutils to load properties from an class
// -- cfg/htmlclient to show loaded properties in a document
//    using classbase templates
/////////////////////////////////////////////////////////////////////////
	function get_form_from_obj($arr)
	{
		$config_util = get_instance("cfg/cfgutils");

		//	arr($config_util->load_class_properties(array("clid" => CL_SEARCH_ROADINFO)));
		//	arr($config_util->load_properties(array("clid" => CL_SEARCH_ROADINFO)));

		$props = $config_util->load_class_properties(array("clid" => CL_SEARCH_ROADINFO));

		$htmlclient = get_instance("cfg/htmlclient");
		
	    
/*
		$county_from_db = $this->db_fetch_array("select tl_county from aw_otv_ds_roadinfo_cache group by tl_county");
		$county_opts = array();
		$county_opts[''] = "";
		foreach($county_from_db as $county)
		{
			$county_opts[$county['tl_county']] = $county['tl_county'];
		}
*/
		
		$htmlclient->start_output();

		$form_values = $this->parse_get_params(array("type" => "form_values"));

		foreach($props as $prop)
		{
		
//			$search_obj_inst = get_instance(CL_SEARCH_ROADINFO);




			if ($prop['type']=="select")
			{
				$prop['options'] = $this->county_list;
				$prop['selected'] = $form_values['county'];
			}

			if($prop['type'] == "checkbox" && $form_values[$prop['name']] == 1)
			{
				$prop['value'] = 1;

			}

			if($prop['type'] == "date_select")
			{
			
				if(($prop['name'] == "date_from") && isset($form_values['date_from']))
				{
					$prop['value'] = $form_values['date_from'];
				}
				elseif (($prop['name'] == "date_to") && isset($form_values['date_to']))
				{
					$prop['value'] = $form_values['date_to'];
				}
				else
				{
			
					$prop['value'] = array(
							"month" => "---",
							"day" => "---",
							"year" => "---",
						);
				}

			}
			
			$htmlclient->add_property($prop);
		
		}
		$htmlclient->finish_output();

		return $htmlclient->get_result(array(
			"raw_output" => 1
		));
	}

	function parse_get_params($arr)
	{
		if(!empty($_GET))
		{
		
			$cur_date = array(
					'month' => date("n"),
					'day' => date("j"),
					'year' => date("Y"),
				);
			$date = array();

			if(isset($_GET['date_from']) || isset($_GET['date_to']))
			{
				$search_by_date = false;
				
				if(is_array($_GET['date_from']))
				{
					foreach($_GET['date_from'] as $key => $value)
					{
						if($value != "---")
						{
							$search_by_date = true;
  							$date['from'][$key] = $value;
						}
						else
						{
							$date['from'][$key] = $cur_date[$key];
						}
					}
					$date['from']['timestamp'] = mktime(0, 0, 0, $date['from']['month'], $date['from']['day'], $date['from']['year']);
					
					
				}
				if(is_array($_GET['date_to']))
				{
					foreach($_GET['date_to'] as $key => $value)
					{
						if($value != "---")
						{
							$search_by_date = true;
							$date['to'][$key] = $value;
						}
						else
						{
							$date['to'][$key] = $cur_date[$key];
							
						}
					}
					$date['to']['timestamp'] = mktime(0, 0, 0, $date['to']['month'], $date['to']['day'], $date['to']['year']);
					
				}

				if($search_by_date)
				{
					if($date['from']['timestamp'] > $date['to']['timestamp'])
					{
						$date['from'] = $date['to'];
						
					}

				}

			}
			
// kui tahan sql parameetrite stringi saada:

			if($arr['type'] == "sql_params")
			{
		
//		   		$tl_id = array("MI", "MT", "MM");
 		
				$where = false;
				$sql_params = "";
				if($_GET['county'] != "")
				{
					$sql_params = " WHERE tl_county='".$_GET['county']."'";
					$where = true;
				}

				if(isset($_GET['date_from']) || isset($_GET['date_to']))
				{
					$sql_params .= ($where) ? " AND " : " WHERE ";
					$where = true;
					$sql_params .= "(tl_timestamp_to >= ".$date['from']['timestamp'].") AND (tl_timestamp_from <= ".$date['to']['timestamp'].")";
				}

				if(($_GET["MI"] == 1) || ($_GET["MT"] == 1) || ($_GET['MM'] == 1))
				{
					$sql_params .= ($where) ? " AND " : " WHERE ";
					$first_param = true;
					if($_GET['MI'] == 1)
					{
						$sql_params .= ($first_param) ? "(tl_id LIKE 'MI%'" : " OR tl_id LIKE 'MI%'";
						$first_param = false;
					}
					if($_GET['MT'] == 1)
						{
						$sql_params .= ($first_param) ? "(tl_id LIKE 'MT%'" : " OR tl_id LIKE 'MT%'";
						$first_param = false;
					}
					if($_GET['MM'] == 1)
					{
						$sql_params .= ($first_param) ? "(tl_id LIKE 'MM%'" : " OR tl_id LIKE 'MM%'";
						$first_param = false;
					}
					$sql_params .= ")";
				}
				

				
				return $sql_params;

			}
			
// kui tahan otsingu vormile väärtusi saada:

			elseif($arr['type'] == "form_values")
			{
                $form_values = array();
				
				if($_GET['county'] != "")
				{
					$form_values['county'] = $_GET['county'];
				}
				
				if($_GET['MI'] == 1)
				{
					$form_values['MI'] = 1;
				}
				if($_GET['MT'] == 1)
				{
					$form_values['MT'] = 1;
				}
				if($_GET['MM'] == 1)
				{
					$form_values['MM'] = 1;
				}
				
				if(isset($_GET['date_from']) || isset($_GET['date_to']))
				{
					$form_values['date_from'] = $date['from'];
					$form_values['date_to'] = $date['to'];
					
				}
				
				return $form_values;
			}
			else
			{
				return false;
			}
		}
		else
		{
/////////////////////////////////////////////////////////////////////////////////////////////////
// siia see kraam kui URL-i pealt mingeid parameetreid ei tule
// ilmselt peab ka siin tsekkima, et mis parameetrid välja kutsumisel kaasa pandi
/////////////////////////////////////////////////////////////////////////////////////////////////
		    $timestamp = mktime(0,0,0, date("n"), date("j"), date("Y"));
	//		$sql_params = " WHERE (tl_timestamp_from <= ".$timestamp.") AND (tl_timestamp_to >= ".$timestamp.")";
	        if($arr['type'] == "sql_params")
	        {
	        	return "";
			}
			if($arr['type'] == "form_values")
			{

			}
		}
	}
	
	

}
?>

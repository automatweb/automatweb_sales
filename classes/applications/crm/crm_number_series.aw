<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_number_series.aw,v 1.13 2008/07/28 11:28:41 markop Exp $
// crm_number_series.aw - CRM Numbriseeria 
/*

@classinfo syslog_type=ST_CRM_NUMBER_SERIES relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects

@default group=general

	@property series type=table no_caption=1 store=no
*/

class crm_number_series extends class_base
{
	function crm_number_series()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_number_series",
			"clid" => CL_CRM_NUMBER_SERIES
		));

		$clss = aw_ini_get("classes");
		$this->classes = array(
			CL_CRM_BILL => $clss[CL_CRM_BILL]["name"],
			CL_PATENT => $clss[CL_PATENT]["name"]
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "series":
				$this->_series($arr);
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
			case "series":
				$this->_save_series($arr);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_series_t(&$t)
	{
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "from",
			"caption" => t("Alates"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "to",
			"caption" => t("Kuni"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "start",
			"caption" => t("Seeria esimene number"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "end",
			"caption" => t("Seeria viimane number"),
			"align" => "center"
		));
	}

	function _series($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_series_t($t);

		$ser = safe_array($arr["obj_inst"]->meta("series"));
		$ser[] = array();
		foreach($ser as $idx => $row)
		{
			$date_sel = "<A HREF='#'  onClick=\"var cal=new CalendarPopup();cal.select(aw_get_el('ser[$idx][from]'),'anchorf".$idx."','dd/MM/yy'); return false;\"
						   NAME='anchorf".$idx."' ID='anchorf".$idx."'>".t("vali")."</A>";
			$date_sel2 = "<A HREF='#'  onClick=\"var cal=new CalendarPopup();cal.select(aw_get_el('ser[$idx][to]'),'anchort".$idx."','dd/MM/yy'); return false;\"
						   NAME='anchort".$idx."' ID='anchort".$idx."'>".t("vali")."</A>";

			$t->define_data(array(
				"class" => html::select(array(
					"options" => $this->classes,
					"value" => $row["class"],
					"name" => "ser[$idx][class]"
				)),
				"from" => html::textbox(array(
					"name" => "ser[$idx][from]",
					"value" => $row["from"] > 100 ? date("d/m/y",$row["from"]) : "",
					"size" => 7
				)).$date_sel,
				"to" => html::textbox(array(
					"name" => "ser[$idx][to]",
					"value" => $row["to"] > 100 ? date("d/m/y",$row["to"]) : "",
					"size" => 7
				)).$date_sel2,
				"start" => html::textbox(array(
					"name" => "ser[$idx][start]",
					"value" => $row["start"],
					"size" => 10
				)),
				"end" => html::textbox(array(
					"name" => "ser[$idx][end]",
					"value" => $row["end"],
					"size" => 10
				)),
			));
		}

		$t->set_sortable(false);
	}

	function _save_series($arr)
	{
		$val = array();
		foreach(safe_array($arr["request"]["ser"]) as $row)
		{
			if ($row["class"] && $row["from"] != "" && $row["to"] != "")
			{
				list($d, $m, $y) = explode("/", $row["from"]);
				$row["from"] = mktime(0,0,0, $m, $d, $y);

				list($d, $m, $y) = explode("/", $row["to"]);
				$row["to"] = mktime(0,0,0, $m, $d, $y);

				$val[] = $row;
			}
		}
		$arr["obj_inst"]->set_meta("series", $val);
	}

	/////////////// public interface

	/** returns the next number in the given series for the given class

		@param series - series object 
		@param class - class to return number for
		@param time - time for series
	**/
	function get_next_in_series($series, $class, $time)
	{
		// get all series
		$ser = safe_array($series->meta("series"));
		$nums = safe_array($series->meta("ser_vals"));
		// filter by class and time
		$nr = 0;
		foreach($ser as $idx => $row)
		{
			if($row["class"] == $class && (!$time || ($time >= $row["from"] && $time <= $row["to"])))
			{
				$num = $nums[$idx];
				if ($num > $row["end"])
				{
					$num = 0;
				}
				if ($num < $row["start"])
				{
					$num = $row["start"];
				}
				else
				{
					$num++;
				}

				// actually, just list all bills and get max number+1 for bills
				$filter = array(					
					"class_id" => $class,					
					"lang_id" => array(),					
					"site_id" => array(),					
					//"limit" => 1,
				);
				if($class == CL_CRM_BILL)
				{
					$filter["sort_by"] = "CAST(aw_crm_bill.aw_bill_no as signed) DESC";
					if($time)
					{
						$filter["bill_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, 	(int)($row["start"]) , (int)($row["end"]) , "int");
					}
					else
					{
						$filter["bill_no"] = new obj_predicate_compare(OBJ_COMP_GREATER, 0);
					}//arr($filter);
				}


				$ol = new object_list($filter);

				if($class == CL_CRM_BILL)
				{
					if ($ol->count())
					{
						$o = $ol->begin();
						$num = $o->prop("bill_no") + 1;
					}
					else
					{
						$num = $row["start"];//ma ei tea mis sest kogu eelnevast systeemist kasu on... niikuinii on vaja algusest alustada ju kui ei ole neid
					}
	
					//siia teeb nyyd eriti r2ige kirvemeetodi
					//kui mingil x p6hjusel peaks tahtma olemasolevat numbrit anda, siis tsykkel k2ib v6i maailmal6puni... v6i v2hemalt niikaua kuni leiab numbri mis pole kasutuses v6i tuleb miski muu piirang peale ja on niisama p...
					//see on siin v2ga halb, kuid t666tab... niiet 
					while(true)//eisteks kontrollib seda numbrit mille sai, et ega see olemas ole
					{
						$ol2 = new object_list(array(
							"class_id" => $class,
							"bill_no" => $num,
							"lang_id" => array(),
							"site_id" => array(),
						));//if(aw_global_get("uid") == "Teddi.Rull") {arr($nums[$idx]);arr($ser);}
						if (!$ol2->count())
						{
							break;
						}
						$num++;
					}
				}
				$nums[$idx] = $num;
				$series->set_meta("ser_vals", $nums);
				$series->save();

				/*while(true)//ja teiseks, 2kki on eespoolt 2ra kustutatud juba m6ni, selleks peab veenduma, et eelmine on olemas
				{
					$ol2 = new object_list(array(
						"class_id" => $class,
						"bill_no" => $num,
						"lang_id" => array(),
						"site_id" => array(),
					));//if(aw_global_get("uid") == "Teddi.Rull") {arr($nums[$idx]);arr($ser);}
					if ($ol2->count())
					{
						return $num+1;
					}
					if($num <= $row["start"])
					{
						return $num;
					}
					$num--;
				}*/
				return $num;
			}
		}


		if($class == CL_CRM_BILL)
		{
			// actually, just list all bills and get max number+1 for bills
			$ol = new object_list(array(
				"class_id" => $class,
				"lang_id" => array(),
				"site_id" => array(),
				"sort_by" => "CAST(aw_crm_bill.aw_bill_no as signed) DESC",
				"limit" => 1,
				"bill_no" => new obj_predicate_compare(OBJ_COMP_GREATER, 0)
			));
			if ($ol->count())
			{
				$o = $ol->begin();
				$num = $o->prop("bill_no") + 1;
			}
				
			//siia teeb nyyd eriti r2ige kirvemeetodi
			//kui mingil x p6hjusel peaks tahtma olemasolevat numbrit anda, siis tsykkel k2ib v6i maailmal6puni... v6i v2hemalt niikaua kuni leiab numbri mis pole kasutuses v6i tuleb miski muu piirang peale ja on niisama p...
			while(true)
			{
				$ol2 = new object_list(array(
					"class_id" => $class,
					"bill_no" => $num,
					"lang_id" => array(),
					"site_id" => array(),
				));//if(aw_global_get("uid") == "Teddi.Rull") {arr($nums[$idx]);arr($ser);}
				if (!$ol2->count())
				{
					return $num;
				}
			}
		}
		return $num;
		return NULL;
	}

	/** finds the current company and from that the series	and returns next number in series**/
	function find_series_and_get_next($class, $n, $time)
	{
		if(is_oid($n) && $this->can("view" , $n))
		{
			$ser = obj($n);
		}
		else
		{
			$u = get_instance(CL_USER);
			$co = obj($u->get_current_company());
			$ser = $co->get_first_obj_by_reltype("RELTYPE_NUMBER_SERIES");
		}
		if (!$ser)
		{
			return NULL;
		}

		return $this->get_next_in_series($ser, $class, $time);
	}

	function number_is_in_series($class, $num)
	{
		$u = get_instance(CL_USER);
		$co = obj($u->get_current_company());
		$series = $co->get_first_obj_by_reltype("RELTYPE_NUMBER_SERIES");

		if (!$series)
		{
			return false;
		}

		// get all series
		$ser = safe_array($series->meta("series"));
		$nums = safe_array($series->meta("ser_vals"));

		// filter by class and time
		foreach($ser as $idx => $row)
		{
			if ($row["class"] == $class && $row["from"] <= time() && $row["to"] > time())
			{
				if ($num <= $row["end"] && $num >= $row["start"])
				{
					return true;
				}
			}
		}
		return false;
	}
}
?>

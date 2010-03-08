<?php
/*
@classinfo  maintainer=markop
*/
classload("vcl/popup_search");
class crm_project_search extends popup_search
{
	function crm_participant_search()
	{
		$this->popup_search();
	}

	function _insert_form_props(&$htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);
		$htmlc->add_property(array(
			"name" => "s[search_co]",
			"type" => "textbox",
			"value" => $arr["s"]["search_co"],
			"caption" => t("Organisatsioon"),
		));
		$opts = array();
		$opts["cur_co"] = t("Meie firma");
		$opts["my_cust"] = t("Minu kliendid");


		$htmlc->add_property(array(
			"name" => "s[show_vals]",
			"type" => "chooser",
			"value" => isset($_GET["MAX_FILE_SIZE"]) ? $arr["s"]["show_vals"] : $def,
			"caption" => t("N&auml;ita"),
			"multiple" => 1,
			"orient" => "vertical",
			"options" => $opts
		));
	}

	function _process_reforb_args(&$data)
	{
		$data["s"] = array(
			"co" => $_GET["s"]["co"]
		);
	}

	function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);
		
		$cur = get_current_company();
		if (is_array($arr["s"]["co"]))
		{
			foreach($arr["s"]["co"] as $co)
			{
				$show = 0;
				if($co == $cur->id() && $arr["s"]["show_vals"]["cur_co"])
				{
					unset($arr["s"]["show_vals"]["cur_co"]);
					$show = 1;
				}
				elseif($arr["s"]["show_vals"]["my_cust"])
				{
					$show = 1;
				}
				if ($this->can("view", $co) && $show)
				{
					$arr["s"]["show_vals"][$co] = $co;
				}
			}
			unset($arr["s"]["show_vals"]["my_cust"]);
		}

		if (is_array($arr["s"]["show_vals"]))
		{
			//$ol = new object_list
			
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"parent" => $arr["s"]["show_vals"],
				"site_id" => array(),
			));
			foreach($ol->list as $oid)
			{
				$filter["oid"][] = $oid;
			}
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"site_id" => array(),
			));
			foreach($ol->list as $oid)
			{
				$filter["oid"][] = $oid;
			}
		}

		if ($arr["s"]["search_co"] != "")
		{
			$filter["CL_PROJECT.RELTYPE_PARTICIPANT.name"] = "%".$arr["s"]["search_co"]."%";
		}

		if (is_array($filter["oid"]) && !count($filter["oid"]))
		{
			$filter["oid"] = -1;
		}
//		die(dbg::dump($filter["oid"]));
	}
}

?>

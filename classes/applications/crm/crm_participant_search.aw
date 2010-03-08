<?php
/*
@classinfo maintainer=markop
*/
classload("vcl/popup_search");
class crm_participant_search extends popup_search
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
		$cur_co = get_current_company();
		$opts = array();
		$def = array("cur_co" => 1, $cur_co->id() => 1);
		if (is_array($arr["s"]["co"]))
		{
			foreach($arr["s"]["co"] as $co)
			{
				if ($this->can("view", $co))
				{
					$coo = obj($co);
					$opts[$co] = $coo->name();
					$def[$co] = $co;
					if ($coo->id() == $cur_co->id())
					{
						$has_cur = true;
					}
				}
			}
		}

		if (!$has_cur)
		{
			$opts["cur_co"] = t("Meie firma");
		}
		$opts["my_cust"] = t("Minu kliendid");
		$opts["imp"] = t("Olulised");
	//	$opts["def"] = t("Esimesed kolmk&uuml;mmend");


		$htmlc->add_property(array(
			"name" => "s[show_vals]",
			"type" => "chooser",
			"value" => isset($_GET["MAX_FILE_SIZE"]) ? $arr["s"]["show_vals"] : $def,
			"caption" => t("N&auml;ita"),
			"multiple" => 1,
			"orient" => "vertical",
			"options" => $opts
		));
		if($arr["s"]["show_vals"])
		{
			$tmp = $arr["s"]["show_vals"];
			if($tmp["my_cust"])
			{
				$my_cust = 1;
				unset($tmp["my_cust"]);
			}
			if($tmp["imp"])
			{
				$imp = 1;
				unset($tmp["imp"]);
			}
			if(count($tmp))
			{
				$cos = array();
				foreach($tmp as $co)
				{
					if ($this->can("view", $co))
					{
						$o = obj($co);
						$cos[] = $o->name();
					}
				}
			}
			$text = array();
			if($my_cust)
			{
				$cos[] = t("minu klientide");
			}
			$text = implode(t(' v&otilde;i '), $cos);
			if($text)
			{
				$text .= t(" t&ouml;&ouml;tajaid");
			}
			if($imp)
			{
				$text = t("olulisi ").$text;
			}
			$htmlc->add_property(array(
				"name" => "info",
				"type" => "text",
				"caption" => t("Info"),
				"value" => $text
			));
		}
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

		if (!$_GET["MAX_FILE_SIZE"])
		{
			$arr["s"]["show_vals"]["cur_co"] = 1;
			if (is_array($arr["s"]["co"]))
			{
				foreach($arr["s"]["co"] as $co)
				{
					if ($this->can("view", $co))
					{
						$arr["s"]["show_vals"][$co] = $co;
					}
				}
			}
		}
		if ($arr["s"]["search_co"] != "")
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_WORK.name" => map("%%%s%%", array_filter(explode(",", $arr["s"]["search_co"]), create_function('$a','return $a != "";'))),
					"CL_CRM_PERSON.CURRENT_JOB.org.name" => map("%%%s%%", array_filter(explode(",", $arr["s"]["search_co"]), create_function('$a','return $a != "";'))),
				))
			);
			//$filter["CL_CRM_PERSON.RELTYPE_WORK.name"] = map("%%%s%%", array_filter(explode(",", $arr["s"]["search_co"]), create_function('$a','return $a != "";')));
		}

		if (is_array($arr["s"]["show_vals"]) && !$arr["s"]["search_co"])
		{
			$c = get_instance(CL_CRM_COMPANY);
			if ($arr["s"]["show_vals"]["cur_co"])
			{
				$u = get_instance(CL_USER);
				$filter["oid"] = array_keys($c->get_employee_picker(obj($u->get_current_company()),false,($arr["s"]["show_vals"]["imp"]?true:false)));
			}

			if ($arr["s"]["show_vals"]["my_cust"])
			{
				$i = get_instance(CL_CRM_COMPANY);
				$my_c = $i->get_my_customers();
				if (!count($my_c) && !is_array($filter["oid"]))
				{
					$filter["oid"] = -1;
				}
				else
				if (count($my_c))
				{
					foreach($my_c as $oid)
					{
						$arr["s"]["show_vals"][$oid] = $oid;
					}
				}
			}

			if ($arr["s"]["show_vals"]["imp"] && $do_imp)
			{
				$u = get_instance(CL_USER);

				$tmp = array();
				foreach(array_keys($c->get_employee_picker(obj($u->get_current_company()), false, true)) as $_id)
				{
					$tmp[$_id] = $_id;
				}

				if (!is_array($filter["oid"]))
				{
					$filter["oid"] = $tmp;
				}
				else
				{
					$filter["oid"] = array_intersect($filter["oid"], $tmp);
				}
			}

			if ($arr["s"]["show_vals"]["def"])
			{
				$ol = new object_list(array("class_id" => CL_CRM_PERSON, "lang_id" => array(), "site_id" => array(), "limit" => 30));

				$tmp = array();			
				foreach($ol->ids() as $_id)
				{
					$tmp[$_id] = $_id;
				}

				if (!is_array($filter["oid"]))
				{
					$filter["oid"] = $tmp;
				}
				else
				{
					$filter["oid"] = array_intersect($filter["oid"], $tmp);
				}
			}

			if (is_array($arr["s"]["show_vals"]))
			{
				foreach($arr["s"]["show_vals"] as $k => $v)
				{
					if (is_oid($k) && $v)
					{
						$tmp = array_keys($c->get_employee_picker(obj($k),false,($arr["s"]["show_vals"]["imp"]?true:false)));

						if (!is_array($filter["oid"]))
						{
							$filter["oid"] = $tmp;
						}
						else
						{
							$filter["oid"] = array_merge($filter["oid"], $tmp);
						}
					}
				}
			}
		}

		if (is_array($filter["oid"]) && !count($filter["oid"]))
		{
			$filter["oid"] = -1;
		}
//		die(dbg::dump($filter["oid"]));
	}
}

?>

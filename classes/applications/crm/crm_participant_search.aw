<?php

class crm_participant_search extends popup_search
{
	function _insert_form_props($htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);
		$htmlc->add_property(array(
			"name" => "s[search_co]",
			"type" => "textbox",
			"value" => isset($arr["s"]["search_co"]) ? $arr["s"]["search_co"] : "",
			"caption" => t("Organisatsioon"),
		));
		$cur_co = get_current_company();
		$opts = array();
		$def = $cur_co ? array("cur_co" => 1, $cur_co->id() => 1) : array("cur_co" => 0);
		$has_cur = false;
		if (isset($arr["s"]["co"]) and is_array($arr["s"]["co"]))
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

		if(!empty($arr["s"]["show_vals"]))
		{
			$my_cust = $imp = 0;
			$cos = array();
			$tmp = $arr["s"]["show_vals"];
			if(!empty($tmp["my_cust"]))
			{
				$my_cust = 1;
				unset($tmp["my_cust"]);
			}

			if(!empty($tmp["imp"]))
			{
				$imp = 1;
				unset($tmp["imp"]);
			}

			if(count($tmp))
			{
				foreach($tmp as $co)
				{
					if ($this->can("view", $co))
					{
						$o = obj($co);
						$cos[] = $o->name();
					}
				}
			}

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
		if (isset($_GET["s"]["co"]))
		{
			$data["s"] = array(
				"co" => $_GET["s"]["co"]
			);
		}
		parent::_process_reforb_args($data);
	}

	function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);

		if (empty($_GET["MAX_FILE_SIZE"]))
		{
			$arr["s"]["show_vals"]["cur_co"] = 1;
			if (isset($arr["s"]["co"]) and is_array($arr["s"]["co"]))
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

		if (!empty($arr["s"]["search_co"]))
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

		if (isset($arr["s"]["show_vals"]) && is_array($arr["s"]["show_vals"]) && empty($arr["s"]["search_co"]))
		{
			$c = get_instance(CL_CRM_COMPANY);
			$u = get_instance(CL_USER);
			$cur_co_oid = $u->get_current_company();
			if (!empty($arr["s"]["show_vals"]["cur_co"]) and $cur_co_oid)
			{
				$filter["oid"] = array_keys($c->get_employee_picker(obj($cur_co_oid), false, !empty($arr["s"]["show_vals"]["imp"])));
			}

			if (!empty($arr["s"]["show_vals"]["my_cust"]))
			{
				$i = new crm_company();
				$my_c = $i->get_my_customers();
				if (!count($my_c) && !is_array($filter["oid"]))
				{
					$filter["oid"] = -1;
				}
				elseif (count($my_c))
				{
					foreach($my_c as $oid)
					{
						$arr["s"]["show_vals"][$oid] = $oid;
					}
				}
			}

			if (!empty($arr["s"]["show_vals"]["imp"]) && $do_imp && $cur_co_oid)
			{
				$tmp = array();
				foreach(array_keys($c->get_employee_picker(obj($cur_co_oid), false, true)) as $_id)
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

			if (!empty($arr["s"]["show_vals"]["def"]))
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

			if (isset($arr["s"]["show_vals"]) and is_array($arr["s"]["show_vals"]))
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
	}
}

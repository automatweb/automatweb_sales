<?php
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_org_search.aw,v 1.18 2008/02/28 11:50:56 kristo Exp $
// crm_org_search.aw - kliendibaasi otsing
/*
@classinfo  maintainer=markop

@default group=general
@default form=crm_search

@property name type=textbox
@caption Nimi

@property reg_nr type=textbox
@caption Reg. Nr.

@property address type=textbox
@caption Aadress

@property ceo type=textbox
@caption Firmajuht

@property ettevotlusvorm type=objpicker clid=CL_CRM_CORPFORM
@caption Ettevõtlusvorm

@property city type=objpicker clid=CL_CRM_CITY
@caption Linn

@property county type=objpicker clid=CL_CRM_COUNTY
@caption Maakond

@property search_button type=submit value=Otsi
@caption Otsi

@property search_results type=table no_caption=1
@caption Otsingutulemused

@property no_reforb type=hidden value=1

@forminfo crm_search onload=init_search onsubmit=test method=get

*/

class crm_org_search extends class_base
{
	function crm_org_search()
	{
		$this->init(array(
			"clid" => CL_CRM_ORG_SEARCH
		));
	}

	function init_search($arr)
	{
		// search only, if this is set
		// and get_property sets it, once it figures out that there is something
		// to search for
		$this->valid_search = false;
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$rv = PROP_OK;
		if (($data["type"] == "textbox" || $data["type"] == "objpicker") && !empty($arr["request"][$data["name"]]))
		{
			$this->valid_search = true;
			$data["value"] = $arr["request"][$data["name"]];
		};
		switch($data["name"])
		{
			case "search_results":
				$this->do_search($arr);
				break;
		};
		return $rv;
	}

	/**
		@attrib name=test all_args="1"

	**/
	function test($arr)
	{
		$arr["form"] = "crm_search";
		return $this->change($arr);
	}

	function do_search($arr, $xfilter=array())
	{
		$tf = $arr["prop"]["vcl_inst"];
		$tf->define_field(array(
			"name" => "name",
			"caption" => t("Organisatsioon"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "corpform",
			"caption" => t("Õiguslik vorm"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "email",
			"caption" => t("E-post"),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "url",
			"caption" => t("WWW"),
			"sortable" => 1,
		));
		$tf->define_field(array(
			"name" => "phone",
			"caption" => t('Telefon'),
			"sortable" => 1,
		));

		$tf->define_field(array(
			"name" => "ceo",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
		));

		$tf->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));


		if (!$this->valid_search && !sizeof($xfilter))
		{
			return false;
		};

		$req = $arr["request"];

		array_walk($req, create_function('&$v,$k', '$v = trim($v);'));

		if (!empty($req["name"]))
		{
			$filter["name"] = "%" . $req["name"] . "%";
		};

		if (!empty($req["reg_nr"]))
		{
			$filter["reg_nr"] = "%" . $req["reg_nr"] . "%";
		};

		if (!empty($req["ettevotlusvorm"]))
		{
			$filter["ettevotlusvorm"] = $req["ettevotlusvorm"];
		};
		if (!empty($req["ceo"]) || !empty($xfilter['firmajuht']))
		{
			// search by ceo name? first create a list of all crm_persons
			// that match the search criteria and after that create a list
			// of crm_companies that have one of the results as a ceo
			$ceo_filter = array(
				"class_id" => CL_CRM_PERSON,
				"limit" => 1000,
				"name" => "%" . $req["ceo"] . "%",
			);
			if(sizeof($xfilter['firmajuht']))
			{
				$ceo_filter['name'] = $xfilter['firmajuht'];
			}
			$ceo_list = new object_list($ceo_filter);
			if (sizeof($ceo_list->ids()) > 0)
			{
				$filter["firmajuht"] = $ceo_list->ids();
				if(sizeof($xfilter['firmajuht']))
				{
					$xfilter['firmajuht'] = &$filter['firmajuht'];
				}
			};
		};
		$addr_filter = array();

		if (!empty($req["city"]))
		{
			$addr_filter["linn"] = $req["city"];
		};

		if (!empty($req["county"]))
		{
			$addr_filter["maakond"] = $req["county"];
		};

		if (!empty($req["address"]))
		{
			$addr_filter["name"] = "%" . $req["address"] . "%";
		};

		$addr_xfilter = array();
		$no_results = false;

		if(sizeof($xfilter['linn']))
		{
			$city_list = new object_list(array(
				'class_id'=>CL_CRM_CITY,
				'limit' => 1000,
				'name' => $xfilter['linn'],
			));
			if(sizeof($city_list->ids()))
			{
				$addr_xfilter['linn'] = $city_list->ids();
			}
			else
			{
				$no_results = true;
			}
			unset($xfilter['linn']);
		}

		if(sizeof($xfilter['maakond']))
		{
			$county_list = new object_list(array(
				'class_id' => CL_CRM_COUNTY,
				'limit' => 1000,
				'name' => $xfilter['maakond']
			));
			if(sizeof($county_list->ids()))
			{
				$addr_xfilter['maakond'] = $county_list->ids();
			}
			else
			{
				$no_results = true;
			}
			unset($xfilter['maakond']);
		}

		if(sizeof($xfilter['address']))
		{
			$addr_xfilter['name'] = &$xfilter['address'];
			unset($xfilter['address']);
		}

		if (sizeof($addr_filter) > 0 || sizeof($addr_xfilter)>0)
		{
			if(sizeof($addr_xfilter))
			{
				$addr_filter = $addr_xfilter;
			}

			$addr_filter["class_id"] = CL_CRM_ADDRESS;
			$addr_filter["limit"] = 1000;

			$addr_list = new object_list($addr_filter);
			if (sizeof($addr_list->ids()) > 0)
			{
				$filter["contact"] = $addr_list->ids();
				if(sizeof($addr_xfilter))
				{
					$xfilter['contact'] = &$filter['contact'];
				}
			}
			else
			{
				$no_results=true;
			}
		};

		if(sizeof($xfilter))
		{
			$filter = $xfilter;
		}

		$filter['class_id'] = CL_CRM_COMPANY;
		$filter['limit'] = 100;
		$filter["site_id"] = array();
		$filter["lang_id"] = array();
		$results = new object_list($filter);
		$count = 0;

		for ($o = $results->begin(); !$results->end(); $o = $results->next())
		{
			if($no_results)
			{
				break;
			}
			$count++;
			// aga ülejäänud on kõik seosed!
			$vorm = $tegevus = $contact = $juht = $juht_id = $phone = $url = $mail = "";
			if (is_oid($o->prop("ettevotlusvorm")) && $this->can("view", $o->prop("ettevotlusvorm")))
			{
				$tmp = new object($o->prop("ettevotlusvorm"));
				$vorm = $tmp->name();
			};

			/*if (is_oid($o->prop("pohitegevus")))
			{
				$tmp = new object($o->prop("pohitegevus"));
				$tegevus = $tmp->name();
			};*/

			if (is_oid($o->prop("contact")))
			{
				$tmp = new object($o->prop("contact"));
				$contact = $tmp->name();
			};

			if (is_oid($o->prop("firmajuht")))
			{
				$juht_obj = new object($o->prop("firmajuht"));
				$juht = $juht_obj->name();
				$juht_id = $juht_obj->id();
			};

			if ($this->can("view", $o->prop("phone_id")))
			{
				$ph_obj = new object($o->prop("phone_id"));
				$phone = $ph_obj->name();
			};


			if ($this->can("view", $o->prop("url_id")))
			{
				$url_obj = new object($o->prop("url_id"));
				$url = $url_obj->prop("url");
				// I dunno, sometimes people write url into the name field and expect this to work
				if (empty($url))
				{
					$url = $url_obj->name();
				};
			};

			if ($this->can("view", $o->prop("email_id")))
			{
				$mail_obj = new object($o->prop("email_id"));
				$mail = html::href(array(
					"url" => "mailto:" . $mail_obj->prop("mail"),
					"caption" => $mail_obj->prop("mail"),
				));

			};

			$tf->define_data(array(
				"id" => $o->id(),
				"name" => html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $o->id(),
					),$o->class_id()),
					"caption" => $o->name(),
				)),
				"reg_nr" => $o->prop("reg_nr"),
				//"pohitegevus" => $tegevus,
				"corpform" => $vorm,
				"address" => $contact,
				"ceo" => html::href(array(
					"url" => $this->mk_my_orb("change",array(
						"id" => $juht_id,
					),CL_CRM_PERSON),
					"caption" => $juht,
				)),
				"phone" => $phone,
				"url" => html::href(array(
					"url" => $url,
					"caption" => $url,
				)),
				"email" => $mail,
			));
		}

		if ($count == 0)
		{
			$tf->set_header(t("Otsing ei leidnud ühtegi objekti"));
		}
	}
}
?>

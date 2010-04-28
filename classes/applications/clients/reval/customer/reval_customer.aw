<?php

namespace automatweb;
// reval_customer.aw - Reval Klienditsoon
/*

@classinfo syslog_type=ST_REVAL_CUSTOMER relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

*/

class reval_customer extends class_base
{
	const AW_CLID = 1411;

	private $fc_level_names = array(
		"1" => "NO First Client",
		"2" => "First Client",
		"3" => "Silver",
		"4" => "Gold",
		"5" => "Diamond"
	);

	function reval_customer()
	{//if(aw_global_get("uid") == "testinimene@struktuur.ee") arr($_SESSION["reval_fc"]);
		$this->init(array(
			"tpldir" => "applications/clients/reval/customer/reval_customer",
			"clid" => CL_REVAL_CUSTOMER
		));

		if ($v = $_SESSION["go_to_lang"])
		{
			unset($_SESSION["go_to_lang"]);
			header("Location: ".aw_ini_get("baseurl")."/$v/firstclient-zone");
			die();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function show($arr)
	{
		if (!$this->get_cust_id())
		{
			if ($_GET["do"] == "verifyForgotPassword")
			{
				$nr = str_replace("http://www.revalhotels.com/firstclient-zone?do=verifyForgotPassword", "https://clients.revalhotels.com/clients/?do=verifyForgotPassword", get_ru());
				//die($nr);
				header("Location: ".$nr);
				die();
			}
			return $this->_disp_login();
		}
		$ob = new object($arr["id"]);
		$this->read_template("view1.tpl");
		lc_site_load("reval_customer", $this);

		$this->_insert_cust_data();
		$this->_insert_web_bookings(5, array("id" => $arr["id"]));
		$this->_insert_subscribed_categories();
		$this->_insert_edit_links($ob->id());
		$this->_insert_free_nights();
		return $this->parse();
	}

	function _insert_free_nights()
	{

/*if ($_SESSION["reval_fc"]["id"] != 63073)
{
	return;
}*/
		$return = $this->do_orb_method_call(array(
			"action" => "GetCustomerComplimentaryNightStatistics",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array(
				"customerId" => $_SESSION["reval_fc"]["id"]
			),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));
		if ($return["GetCustomerComplimentaryNightStatisticsResult"]["PendingNights"] > 0 ||
			$return["GetCustomerComplimentaryNightStatisticsResult"]["UnusedNights"] > 0 ||
			$return["GetCustomerComplimentaryNightStatisticsResult"]["ExpiredNights"] > 0 ||
			$return["GetCustomerComplimentaryNightStatisticsResult"]["UsedNights"] > 0)
		{
			$return2 = $this->do_orb_method_call(array(
				"action" => "GetCustomerUnusedComplimentaryNights",
				"class" => "http://markus.ee/RevalServices/Customers/",
				"params" => array(
					"customerId" => $_SESSION["reval_fc"]["id"]
				),
				"method" => "soap",
				"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
			));
//die(dbg::dump($return2));
			if (is_array($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]) && is_array($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"]) && !is_array($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"][0]) && !empty($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"]["NumberOfNights"]))
			{
				$return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"] = array($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"]);
			}
			$s = "";
			foreach($return2["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"] as $e)
			{
				$this->vars(array(
					"freee_avail_count" => $e["NumberOfNights"],
					"freee_avail_date" => date("F Y", $this->_parse_date($e["ExpiryDate"])),
				));
				$s .= $this->parse("AVAILABLE_FREE_NIGHT");
			}

			$return2 = $this->do_orb_method_call(array(
				"action" => "GetCustomerUsedComplimentaryNights",
				"class" => "http://markus.ee/RevalServices/Customers/",
				"params" => array(
					"customerId" => $_SESSION["reval_fc"]["id"]
				),
				"method" => "soap",
				"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
			));
			$s2 = "";
			if (is_array($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]) && is_array($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"]) && !is_array($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"][0]) && !empty($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"]["UsageDate"]))
			{
				$return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"] = array($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"]);
			}

			foreach($return2["GetCustomerUsedComplimentaryNightsResult"]["UsedComplimentaryNights"]["ComplimentaryNightUsage"] as $e)
			{
				$this->vars(array(
					"num_free_used" => 1, //$e["NumberOfNights"],
					"date_free_used" => date("F Y", $this->_parse_date($e["UsageDate"])),
				));
				$s2 .= $this->parse("USED_FREE_NIGHT");
			}
			
			$this->vars(array(
				"USED_FREE_NIGHT" => $s2,
				"AVAILABLE_FREE_NIGHT" => $s,
				"num_late_expired" => $return["GetCustomerComplimentaryNightStatisticsResult"]["ExpiredNights"],
			));
			if ($s2 != "")
			{
				$this->vars(array(
					"HAS_USED_FREE_NIGHT" => $this->parse("HAS_USED_FREE_NIGHT")
				));
			}
			if ($s != "")
			{
				$this->vars(array(
					"HAS_AVAIL_NIGHTS" => $this->parse("HAS_AVAIL_NIGHTS")
				));
			}

			if ($return["GetCustomerComplimentaryNightStatisticsResult"]["ExpiredNights"])
			{
				$this->vars(array(
					"HAS_LATELY_EXPIRED" => $this->parse("HAS_LATELY_EXPIRED")
				));
			}
			$this->vars(array("HAS_FREE_NIGHTS" => $this->parse("HAS_FREE_NIGHTS")));
		}
	}

	function _disp_login()
	{
		$_SESSION["request_uri_before_auth"] = aw_global_get("REQUEST_URI");

		$this->read_template("need_login.tpl");
		lc_site_load("reval_customer", $this);

		$this->vars(array(
				"reforb" => $this->mk_reforb("login", array("fail_return" => get_ru()), "users")
		));
		return $this->parse();
	}

	/**
		@attrib name=fc_login nologin="1"
	**/
	function fc_login($arr)
	{
		$params = array(
			"login" => $arr["fc_uid"],
			"password" => $arr["fc_pwd"]
		);

		$return = $this->do_orb_method_call(array(
			"action" => "ValidateCustomerByLoginAndPassword",
			"class" => "http://markus.ee/RevalServices/Security/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/SecurityService.asmx" // REPL
		));

		if ($return["ValidateCustomerByLoginAndPasswordResult"]["ValidationStatus"] != "Success")
		{
			return aw_url_change_var("error", 1, $arr["ru"]);
		}
		$_SESSION["reval_fc"]["id"] = $return["ValidateCustomerByLoginAndPasswordResult"]["CustomerId"];
		$_SESSION["reval_fc"]["data"] = $return["ValidateCustomerByLoginAndPasswordResult"];

		return $arr["ru"];
	}

	private function _insert_edit_links($id)
	{
		$this->vars(array(
			"edit_profile_link" => $this->mk_my_orb("edit_profile", array("id" => $id, "section" => aw_global_get("section"))),
			"all_bookings_link" => $this->mk_my_orb("all_bookings", array("id" => $id, "section" => aw_global_get("section"))),
		));
	}

	private function _insert_subscribed_categories()
	{
		$cat_data = $this->do_call("GetCustomerSubscribedCategories", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");
		$cats = "";
		foreach($cat_data["Categories"]["SubscriptionCategory"] as $cat_data)
		{
			$this->vars(array(
				"category_name" => $this->_f($cat_data["Name"])
			));
			$cats .= $this->parse("SUBSCRIBED_CATEGORY");
		}
		$this->vars(array(
			"SUBSCRIBED_CATEGORY" => $cats
		));
	}

	private function _insert_cust_data()
	{
		$cust_data = $this->do_call("GetCustomerProfile", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");
//die(dbg::dump($cust_data));
		$lang_opts = $this->_get_language_options();
		$country_opts = crm_address::get_country_list();
		$room_pref_options = $this->_get_room_preference_options();
		$floor_pref_opts = $this->_get_floor_preference_options();
		$this->vars(array(
			"firstname" => $this->_f($cust_data["FirstName"]),
			"lastname" => $this->_f($cust_data["LastName"]),
			"level" => $this->_f($cust_data["CurrentLevelId"]),
			"level_name" => $this->fc_level_names[$this->_f($cust_data["CurrentLevelId"])],
			"level_status" => "__undefined__",
			"num_nights_next" => $this->_f($cust_data["NightsTillNextLevel"]),
			"next_level_name" => $this->fc_level_names[$this->_f($cust_data["NextLevelId"])],
			"avail_free_nights" => $this->_f($cust_data["AvailableComplimentaryNights"]),
			"customer_since" => date("d.m.Y", $this->_parse_date($cust_data["RegistrationDate"])),
			"dob" => date("d.m.Y", $this->_parse_date($cust_data["Birthday"])),
			"company_name" => $this->_f($cust_data["CompanyName"]),
			"business_title" => $this->_f($cust_data["BusinessTitle"]),
			//"position" => $this->_f($cust_data["OccupationName"]),
			"business_field" => $this->_f($cust_data["FieldOfBusinessName"]),
			"business_phone" => $this->_f($cust_data["BusinessPhone"]),
			"home_phone" => $this->_f($cust_data["HomePhone"]),
			"mobile_phone" => $this->_f($cust_data["MobilePhone"]),
			"comm_types" => $this->_get_cust_comm_types($cust_data),
			"email" => $this->_f($cust_data["Login"]),
			"pref_language" => $lang_opts[$this->_f($cust_data["PreferredLanguageId"])],
			"city" => $this->_f($cust_data["CityName"]),
			"country" => $country_opts[trim($this->_f($cust_data["CountryCode"]))],
			"smoking_pref" => $room_pref_options[(int)$this->_f($cust_data["RoomSmokingPreferenceId"])],
			"floor_pref" => $floor_pref_opts[$this->_f($cust_data["FloorPreferenceId"])],
			"currency_pref" => $this->_f($cust_data["DefaultWebCurrencyLabel"]),
			"Last2YearVisits" => $this->_f($cust_data["Last2YearVisits"]),
			"last_visit" => date("d.m.Y H:i:s", $this->_parse_date($cust_data["LastWebLoginDateTime"]))
		));

		if ($cust_data["CurrentLevelId"] == $cust_data["NextLevelId"])
		{
			$this->vars(array(
				"DIAMOND" => $this->parse("DIAMOND")
			));
		}
		else
		{
			$this->vars(array(
				"NOT_DIAMOND" => $this->parse("NOT_DIAMOND")
			));
		}

		if ($cust_data["NextLevelId"] && $cust_data["NightsTillNextLevel"] != -1)
		{
			$this->vars(array(
				"HAS_NEXT_LEVEL" => $this->parse("HAS_NEXT_LEVEL")
			));
		}
	}

	private function _get_cust_comm_types($cust_data)
	{
		$rv = array();
		if ($cust_data["IsEmailCommunication"])
		{
			$rv[] = "Email";
		}
		else
		/*if ($cust_data["IsMailCommunication"])
		{
			$rv[] = "Mail";
		}
		else*/
		if ($cust_data["IsSmsCommunication"])
		{
			$rv[] = "Sms";
		}
		return join(", ", $rv);
	}

	public static function get_cust_id()
	{
//return 74678;
	//return 24419;
		return (int)$_SESSION["reval_fc"]["id"];
	}

	private function _insert_web_bookings($limit = null, $arr)
	{
//		$book_data = $this->do_call("GetCustomerLastTwoYearBookings", array(
		$book_data = $this->do_call("GetCustomerBookings", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");
		$bs = "";
		if (isset($book_data["BookingDetails"]["BookingDetails"]["BookingId"]))
		{
			$var = array($book_data["BookingDetails"]["BookingDetails"]);
		}
		else
		{
			$var = $book_data["BookingDetails"]["BookingDetails"];
		}
		$bron_inst = get_instance("applications/ows_bron/ows_bron");
		foreach($var as $booking)
		{
				$this->vars(array(
					"view_booking" => $this->mk_my_orb("display_final_page", array("ows_rvs_id" => $booking["ConfirmationCode"], "section" => 107220 /*aw_global_get("section")*/), CL_OWS_BRON),
				));
			
			$this->vars(array(
				"booking_id" => $this->_f($booking["ConfirmationCode"]),
				"booking_status" => $this->_f($booking["Status"]),
				"booking_from" => date("d.m.Y", $this->_parse_date($booking["ArrivalDate"])),
				"booking_to" => date("d.m.Y", $this->_parse_date($booking["DepartureDate"])),
				"num_nights" => $this->_f($booking["LengthOfStay"]),
				//"hotel_name" => $this->_f($booking["HotelName"]), webservice gives wrong hotel name
				"hotel_name" => $bron_inst->hotel_list[$this->_f($booking["HotelId"])],
				"checkin_url" => $this->mk_my_orb("do_checkin", array("ows_rvs_id" => $booking["ConfirmationCode"], "section" => /*aw_global_get("section")*/ 176784 , "id" => $arr["id"]))
			));
			if (++$i % 2 == 1)
			{
				$bs .= $this->parse("WEB_BOOKING");
			}
			else
			{
				$bs .= $this->parse("WEB_BOOKING_ODD");
			}

			if ($limit !== null && ++$count > $limit)
			{
				break;
			}
		}
		$this->vars(array(
			"WEB_BOOKING" => $bs,
			"WEB_BOOKING_ODD" => ""
		));
	}

	private function _get_web_language_id()
	{
		$lc = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC");
		return get_instance(CL_OWS_BRON)->get_web_language_id($lc);
	}

	function do_call($action, $params, $ns = "Booking", $full_res = false)
	{
		enter_function("reval_customer::ws_call::$action");
		if ($ns == "Booking")
		{
			$fn = "BookingService";
		}
		else
		if ($ns == "Customers")
		{
			$fn = "CustomerService";
		}
		else
		if ($ns == "Security")
		{
			$fn = "SecurityService";
		}
		$return = $this->do_orb_method_call(array(
			"action" => $action,
			"class" => "http://markus.ee/RevalServices/$ns/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/".$fn.".asmx" // REPL
		));
		exit_function("reval_customer::ws_call::$action");
		return $full_res ? $return : $return[$action.'Result'];
	}

	public function _f($str)
	{
		$str = iconv("utf-8", self::_charset(), $str);
		return $str;
	}

	function _parse_date($str)
	{
		list($date_part, $time_part) = explode("T", $str);
		list($y, $m, $d) = explode("-", $date_part);
		list($h, $min, $sec) = explode(":", $time_part);
		return mktime($h, $min, $sec, $m, $d, $y);
	}

	private function _charset()
	{
		return aw_global_get("charset")."//IGNORE";
	}

	/**
		@attrib name=edit_profile nologin="1"
		@param id required type=int acl=view
	**/
	function edit_profile($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$o = obj($arr["id"]);
		$this->read_template("edit_profile.tpl");
		lc_site_load("reval_customer", $this);

		$this->_ep_insert_tabs($o->id());
		$this->_ep_insert_cust_data();
		$this->_ep_do_sub_cats();
		$this->_do_errors();
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_edit_profile", array("section" => aw_global_get("section"), "id" => $arr["id"]))
		));
		return $this->parse();
	}

	private function _do_errors()
	{
		if ($_GET["error"] > 0)
		{
			$this->vars(array(
				"ERR_".$_GET["error"] => $this->parse("ERR_".$_GET["error"])
 			));
		}
	}

	private function _ep_insert_cust_data()
	{
		$cust_data = $this->do_call("GetCustomerProfile", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers"); //arr($cust_data);
//die(dbg::dump($cust_data));
		$this->vars(array(
			"firstname" => $this->_f($cust_data["FirstName"]),
			"lastname" => $this->_f($cust_data["LastName"]),
			"country_select" => $this->_ep_do_country_select($this->_req("CountryCode", $cust_data["CountryCode"])),
			"county_name" => $this->_req("StateOrTerritory", $cust_data["StateOrTerritory"]),
			"personal_code" => $this->_req("PersonalCode", $cust_data["PersonalCode"]),
			"company_name" => $this->_req("CompanyName", $cust_data["CompanyName"]),
			"city_select" => $this->_ep_do_city_select($this->_req("City", $cust_data["CityId"])),
			"city_name" => $this->_req("CityName", $cust_data["CityName"]),
			"gender_select" => $this->_ep_do_gender_select($this->_req("Gender", $cust_data["GenderId"])),
			"address" => $this->_req("Address", $cust_data["AddressLine1"]),
			"dob" => $this->_req("CustomerBirthday", date("d.m.Y", $this->_parse_date($cust_data["Birthday"]))),
			"postal_code" => $this->_req("PostalCode", $cust_data["PostalCode"]),
			//"marital_status_select" => $this->_ep_do_marital_status_select($this->_req("MaritalStatus", $cust_data["MaritalStatusId"])),
			"field_of_business_select" => $this->_ep_do_field_of_business_select($this->_req("FieldOfBusiness", $cust_data["FieldOfBusinessId"])),
			"business_phone" => $this->_req("BusinessPhone", $cust_data["BusinessPhone"]),
			"business_title" => $this->_req("BusinessTitle", $cust_data["BusinessTitle"]),
			//"occupation_select" => $this->_ep_do_occupation_select($this->_req("Occupation", $cust_data["OccupationId"])),
			"mobile_phone" => $this->_req("MobilePhone", $cust_data["MobilePhone"]),
			"room_preference_select" => $this->_ep_do_room_pref_select($this->_req("RoomPreference", $cust_data["RoomSmokingPreferenceId"])),
			"home_phone" => $this->_req("HomePhone", $cust_data["HomePhone"]),
			"floor_preference_select" => $this->_ep_do_floor_preference_select($this->_req("FloorPreference", $cust_data["FloorPreferenceId"])),
			"preferred_language_select" => $this->_ep_do_pref_language_select($this->_req("PreferredLanguage", $cust_data["PreferredLanguageId"])),
			"is_allergic_sel" => checked($this->_req("IsAllergic", $cust_data["IsAllergic"]) == "true"),
			"is_handicapped_sel" => checked($this->_req("IsHandicapped", $cust_data["IsHandicapped"]) == "true"),
			//"is_mail_comm_sel" => checked($this->_req("IsMailCommunication", $cust_data["IsMailCommunication"]) == "true"),
			"is_email_comm_sel" => checked($this->_req("IsEmailCommunication", $cust_data["IsEmailCommunication"]) == "true"),
			"is_sms_comm_sel" => checked($this->_req("IsSMSCommunication", $cust_data["IsSmsCommunication"]) == "true"),
			"pref_currency_select" => $this->_ep_do_pref_currency_select($this->_req("PreferredCurrency", $cust_data["DefaultWebCurrencyLabel"]))
		));
	}

	private function _ep_insert_tabs($id)
	{
		$this->vars(array(
			"edit_profile" => $this->mk_my_orb("edit_profile", array("id" => $id, "section" => aw_global_get("section"))),
			"edit_email" => $this->mk_my_orb("edit_email", array("id" => $id, "section" => aw_global_get("section"))),
			"edit_password" => $this->mk_my_orb("edit_password", array("id" => $id, "section" => aw_global_get("section"))),
		));
	}

	private function _ep_do_country_select($country_code)
	{
		return $this->picker(trim($country_code), crm_address::get_country_list());
	}

	private function _ep_do_sub_cats()
	{
		$all_cat_list = $this->do_call("GetSubscriptionCategories", array(
			"languageId" => $this->_get_web_language_id()
		), "Customers");

		$sub_cat_list = $this->do_call("GetCustomerSubscribedCategories", array(
			"languageId" => $this->_get_web_language_id(),
			"customerId" => $this->get_cust_id()
		), "Customers");
		$sel_ids = array();
		foreach($sub_cat_list["Categories"]["SubscriptionCategory"] as $sub_row)
		{
			$sel_ids[$sub_row["ID"]] = 1;
		}

		foreach($all_cat_list["Categories"]["SubscriptionCategory"] as $cat_row)
		{
			$this->vars(array(
				"checked" => checked(isset($sel_ids[$cat_row["ID"]]) || $_REQUEST["subscriptionCategory".$cat_row["ID"]] == "true"),
				"sub_cat_num" => $cat_row["ID"],
				"sub_cat_name" => $cat_row["Name"]
			));
			$sc .= $this->parse("SUB_CATEGORY");
		}
		$this->vars(array(
			"SUB_CATEGORY" => $sc
		));
	}

	private function _get_currency_options()
	{
		return array(
			"AUD" => "AUD",
			"CAD"=>"CAD",
			"CHF"=>"CHF",
			"CYP"=>"CYP",
			"CZK"=>"CZK",
			"DKK"=>"DKK",
			"EEK"=>"EEK",
			"EUR"=>"EUR",
			"GBP"=>"GBP",
			"HUF"=>"HUF",
			"JPY"=>"JPY",
			"LTL"=>"LTL",
			"LVL"=>"LVL",
			"NOK"=>"NOK",
			"PLN"=>"PLN",
			"RUB"=>"RUB",
			"SEK"=>"SEK",
			"USD"=>"USD"
		);
	}

	private function _ep_do_pref_currency_select($fb_id)
	{
		return $this->picker($fb_id, $this->_get_currency_options());
	}

	private function _get_language_options()
	{
		return array(
			"" => t("--vali--"), 
			1 => t("Eesti"), 
			2 => t("L&auml;ti"), 
			3 => t("Leedu"), 
			6 => t("Inglise"), 
			7 => t("Vene"), 
			8 => t("Saksa"),
			9 => t("Soome")
		);
	}

	private function _ep_do_pref_language_select($fb_id)
	{
		return $this->picker($fb_id, $this->_get_language_options());
	}

	private function _get_floor_preference_options()
	{
		return array("1" => t("Ei ole eelistust"), 2 => t("&Uuml;lemine korrus"), 3 => t("Alumine korrus"));
	}

	private function _ep_do_floor_preference_select($fb_id)
	{
		return $this->picker($fb_id, $this->_get_floor_preference_options());
	}

	private function _get_room_preference_options()
	{
		return array(/*"1" => t("Ei ole eelistust"), */ 2 => t("Suitsuvaba"), 3 => t("Suitsetav"));
	}

	private function _ep_do_room_pref_select($fb_id)
	{
		return $this->picker($fb_id, $this->_get_room_preference_options());
	}

	private function _ep_do_occupation_select($fb_id)
	{
		$fb_list = $this->do_call("GetOccupations", array(
			"languageId" => $this->_get_web_language_id()
		), "Customers");
		$d = array("" => t("--vali--"));
		foreach($fb_list["Items"]["TranslatedIdentifier"] as $fb_entry)
		{
			$d[$fb_entry["ID"]] = $fb_entry["TranslatedName"];
		}
		return $this->picker($fb_id, $d);
	}

	private function _ep_do_field_of_business_select($fb_id)
	{
		$fb_list = $this->do_call("GetFieldsOfBusiness", array(
			"languageId" => $this->_get_web_language_id()
		), "Customers");
		$d = array("" => t("--vali--"));
		foreach($fb_list["Items"]["TranslatedIdentifier"] as $fb_entry)
		{
			$d[$fb_entry["ID"]] = $fb_entry["TranslatedName"];
		}
		return $this->picker($fb_id, $d);
	}

	private function _ep_do_marital_status_select($ms_id)
	{
		return $this->picker($ms_id, array("1" => t("Ei soovi avaldada"), 3 => t("Abielus"), 4 => t("Lahutatud"), 2 => t("Vallaline")));
	}

	private function _ep_do_gender_select($gender_id)
	{
		return $this->picker($gender_id, array("1" => t("Ei soovi avaldada"), 2 => t("Mees"), 3 => t("Naine")));
	}

	private function _ep_do_city_select($city_id)
	{
		$ce = "";
		$c = get_instance("cache");
		if (($ct = $c->file_get("ws_city_list")) !== false)
		{
			//$d = unserialize($ct);
			$ce = $ct;
		}
		else
		{
			// list cities and let user select one	
			$city_list = $this->do_call("GetCities", array(
				"languageId" => $this->_get_web_language_id()
			), "Customers");
			$d = array("" => t("--vali--"));
			foreach($city_list["Cities"]["City"] as $city_entry)
			{
				$d[$city_entry["ID"]] = $city_entry["Name"];
				$this->vars(array(
					"country" => trim($city_entry["CountryCode"]),
					"city_id" => $city_entry["ID"],
					"city_name" => iconv("utf-8", aw_global_get("charset"), $city_entry["Name"])
				));
				$ce .= $this->parse("CITY_ENTRY");
			}

			$c->file_set("ws_city_list", /*serialize($d)*/ $ce);
		}

		$this->vars(array("CITY_ENTRY" => $ce));

		return $this->picker($city_id, $d);
	}

	public function _ef($str)
	{
		$r = iconv("utf-8", aw_global_get("charset")."//IGNORE", $str);
		return self::_efc($r);
	}

	private function _efc($str)
	{
		// strip all tags and quote quotes
		$r = strip_tags($str);
		$r = htmlspecialchars($r);
		return $r;
	}

	/**
		@attrib name=submit_edit_profile nologin="1"
	**/
	function submit_edit_profile($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$cust_data = $this->do_call("GetCustomerProfile", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");

		list($d, $m, $y) = explode(".", $arr["CustomerBirthday"]);
		if ($d < 1 || $d > 31 || $m < 1 || $m > 12 || $y < 1900 || $y > date("Y"))
		{
				return $this->_err_ret("edit_profile", $arr, 1);
		}

		if (empty($arr["HomePhone"]) && empty($arr["BusinessPhone"]) && empty($arr["MobilePhone"]))
		{
				return $this->_err_ret("edit_profile", $arr, 2);
		}
		if (empty($arr["PreferredLanguage"]))
		{
				return $this->_err_ret("edit_profile", $arr, 3);
		}
		if (/*empty($arr["IsMailCommunication"]) && */empty($arr["IsEmailCommunication"]) && empty($arr["IsSMSCommunication"]))
		{
				return $this->_err_ret("edit_profile", $arr, 4);
		}
//die(dbg::Dump($arr));
		if ($arr["IsSMSCommunication"] == "on" && trim($this->_w($arr["MobilePhone"])) == "")
		{
				return $this->_err_ret("edit_profile", $arr, 5);
		}

		$dob = mktime(1,1,1, $m, $d, $y);

		$d = array(
      "customerId" => $this->get_cust_id(),
      "firstName" => $cust_data["FirstName"],
      "lastName" => $cust_data["LastName"],
      //"maritalStatusId" => (int)$arr["MaritalStatus"],
      "genderId" => (int)$arr["Gender"],
      "birthday" => date("Y-m-d", $dob)."T00:00:00",
      "personalCode" => $this->_w($arr["PersonalCode"]),
      //"hasChildren" => false,	__undefined__
      //"occupationId" => (int)$this->_w($arr["Occupation"]),
      "fieldOfBusinessId" => (int)$this->_w($arr["FieldOfBusiness"]),
      "businessTitle" => $arr["BusinessTitle"],
      "businessPhone" => $this->_w($arr["BusinessPhone"]),
      "CompanyName" => $this->_w($arr["CompanyName"]),
      "homePhone" => $this->_w($arr["HomePhone"]),
      "mobilePhone" => $this->_w($arr["MobilePhone"]),
      "addressLine1" => $this->_w($arr["Address"]),
      //"addressLine2" => $arr["__undefined__"],
      "cityId" => (int)$this->_w($arr["City"]),
      "cityName" => $this->_w($arr["CityName"]),
      "stateOrTerritory" => $this->_w($arr["StateOrTerritory"]),
      "postalCode" => $this->_w($arr["PostalCode"]),
      "countryCode" => $this->_w($arr["CountryCode"]),
      //"isBusinessAddress" => $arr["__undefined__"],
      //"isMailCommunication" => $arr["IsMailCommunication"] == "true",
      "isEmailCommunication" => $arr["IsEmailCommunication"] == "on",
      "isSmsCommunication" => $arr["IsSMSCommunication"] == "on",
      "preferredLanguageId" => (int)$this->_w($arr["PreferredLanguage"]),
      //"homepage" => $arr["__undefined__"],
      "smokingPreferenceId" => (int)$this->_w($arr["RoomPreference"]),
      "floorPreferenceId" => (int)$this->_w($arr["FloorPreference"]),
      "isAllergic" => $arr["IsAllergic"] == "on",
      "isHandicapped" => $arr["IsHandicapped"] == "on",
      "defaultWebCurrencyCode" => $this->_w($arr["PreferredCurrency"])
		);
//echo dbg::dump($d);
//die();
//if (aw_global_get("uid") == "diamond@hotmail.com")
//{
	//aw_global_set("soap_debug", 1);
//}
		$rv = $this->do_call("UpdateCustomerProfile", $d, "Customers", true);
//echo dbg::dump($rv);
//die();
//echo "<hr>";

/*		if($rv["UpdateCustomerProfileResult"]["ResultCode"] == "Success" && $u->get_current_person())
		{
			$u = new user();
			$person = obj($u->get_current_person());
			$person ->set_prop("firstname" , $cust_data["FirstName"]);
			$person ->set_prop("lastname" , $cust_data["LastName"]);
			$person ->set_name(join(" " , array($cust_data["FirstName"] , $cust_data["LastName"])));
		aw_disable_acl();
			$person -> save();
		aw_restore_acl();

		}

*/

		// set sub cats
		$all_cat_list = $this->do_call("GetSubscriptionCategories", array(
			"languageId" => $this->_get_web_language_id()
		), "Customers");

//echo dbg::dump($arr);
		$sel_ids = array();
		foreach($all_cat_list["Categories"]["SubscriptionCategory"] as $sub_row)
		{
			if ($arr["subscriptionCategory".$sub_row["ID"]] == "true")
			{
				$sel_ids[$sub_row["ID"]] = $sub_row["ID"];
			}
		}

		$rv2 = $this->do_call("SetCustomerSubscribedCategories", array(
			"customerId" => $this->get_cust_id(),
			"subscribedCategoryIds" => $sel_ids
		), "Customers");
//echo dbg::dump($rv2);

/*
if (aw_global_get("uid") == "diamond@hotmail.com")
{
	arr($d); arr($rv); arr($rv2);arr($arr);
}
*/

		return $this->_u($this->mk_my_orb("edit_profile", array("error" => 100, "section" => $arr["section"], "id" => $arr["id"], "reval_customer", false, false, "&", false)));
	}

	private function _req($request, $ows)
	{
		//echo "enter req $request (".$_REQUEST[$request].") , ows $ows <br>";
		if (!empty($_REQUEST[$request]))
		{
			//echo "ret req ".$this->_efc($_REQUEST[$request])." <br>";
			return $this->_efc($_REQUEST[$request]);
		}
		//echo "ret ows ".$this->_ef($ows)." <br>";
		return $this->_ef($ows);
	}

	/**
		@attrib name=edit_email nologin="1"
		@param id required type=int
		@param done optional type=int
	**/
	function edit_email($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$o = obj($arr["id"]);
		$this->read_template("edit_email.tpl");
		lc_site_load("reval_customer", $this);

		$this->_ep_insert_tabs($o->id());
		$this->_ee_do_data();
		$this->_do_errors();
		if($arr["done"])
		{
			$this->vars(array("DONE" => $this->parse("DONE")));
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_edit_email", array("section" => aw_global_get("section"), "id" => $arr["id"]))
		));
		return $this->parse();
	}

	private function _ee_do_data()
	{
		$cust_data = $this->do_call("GetCustomerProfile", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");
		$this->vars(array(
			"cur_email" => $cust_data["Email"],
			"new_email" => $this->_req("NewEmail", "")
		));
	}

	/**
		@attrib name=submit_edit_email nologin="1"
	**/
	function submit_edit_email($arr)
	{

		$u = new user();

		$user = obj($u->get_current_user());
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		if (!is_email($arr["NewEmail"]))
		{
				return $this->_err_ret("edit_email", $arr, 1);
		}
//aw_global_set("soap_debug",1);
		$rv = $this->do_call("SetCustomerEmail", array(
			"customerId" => $this->get_cust_id(),
			"email" => $this->_w($arr["NewEmail"])
		), "Customers", true);

		if ($rv["SetCustomerEmailResult"]["ResultCode"] != "Success")
		{
				return $this->_err_ret("edit_email", $arr, 2);
		}

/*		if($this->can("edit" ,$user->id()))
		{
			$user->set_prop("uid" , $arr["NewEmail"]);
			$user->set_name($arr["NewEmail"]);
			$user->save();
		}*/
		return $this->_u($this->mk_my_orb("edit_email", array("id" => $arr["id"], "section" => $arr["section"], "done" => 1), "reval_customer", false, false, "&", false));
	}

	/**
		@attrib name=edit_password nologin="1"
		@param id required type=int
		@param done optional type=int
                @param show_username optional type=int
	**/
	function edit_password($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$o = obj($arr["id"]);
		$this->read_template("edit_password.tpl");
		lc_site_load("reval_customer", $this);

		$this->_ep_insert_tabs($o->id());
		$this->_do_errors();
		if($arr["done"])
		{
			$this->vars(array("DONE" => $this->parse("DONE")));
		}
		
		$user = "";
		if(aw_global_get("uid"))
		{
			$user = aw_global_get("uid");
		}
		elseif($this->can("view" , aw_global_get("uid_oid")))
		{
			$uo = obj(aw_global_get("uid_oid"));
			$user = $uo->prop("uid");
		}

		$this->vars(array("username" => $user));
		$this->vars(array("USERNAME" => aw_global_get("uid")));


		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_edit_password", array("section" => aw_global_get("section"), "id" => $arr["id"]))
		));

		if($arr["show_username"])
		{
			$this->vars(array("NEWUSER" => $this->parse("NEWUSER")));
		}
		else
		{
			$this->vars(array("OLDUSER" => $this->parse("OLDUSER")));
		}

		return $this->parse();
	}

	/**
		@attrib name=submit_edit_password nologin="1"
	**/
	function submit_edit_password($arr)
	{	
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		if (empty($arr["NewPass1"]))
		{
				return $this->_err_ret("edit_password", $arr, 1);
		}
		if ($arr["NewPass1"] != $arr["NewPass2"])
		{
				return $this->_err_ret("edit_password", $arr, 2);
		}
		if (!is_valid("password", $arr["NewPass1"]))
		{
				return $this->_err_ret("edit_password", $arr, 3);
		}

		$rv = $this->do_call("SetCustomerPassword", array(
			"customerId" => $this->get_cust_id(),
			"password" => $arr["NewPass1"]
		), "Customers", true);
		if ($rv["SetCustomerPasswordResult"]["ResultCode"] != "Success")
		{
				return $this->_err_ret("edit_password", $arr, 4);
		}
		return $this->_u($this->mk_my_orb("edit_password", array("id" => $arr["id"], "section" => $arr["section"]), "reval_customer", false, false, "&", false));
	}

	private function _err_ret($act, $data, $err_no)
	{
		$data["error"] = $err_no;
		unset($data["class"]);
		unset($data["action"]);
		unset($data["reforb"]);
		return $this->_u($this->mk_my_orb($act, $data, "reval_customer", false, false, "&", false));
	}

	/**
		@attrib name=all_bookings nologin="1"
		@param id required type=int
	**/
	function all_bookings($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$this->read_template("all_bookings.tpl");
		lc_site_load("reval_customer", $this);

		$this->_insert_web_bookings(null,array("id" => $arr["id"]));
		return $this->parse().$this->display_visits();
	}

	/**
		@attrib name=do_checkin nologin="1"
		@param ows_rvs_id required type=int
		@param id required type=int
	**/
	function do_checkin($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$this->read_template("checkin.tpl");
		lc_site_load("reval_customer", $this);

		$rvs_data = $this->do_call("GetBookingDetails",array(
			"webLanguageId" => $this->_get_web_language_id(),
			"confirmationCode" => $arr["ows_rvs_id"]
		));
		if (!$rvs_data["Booking"]["ConfirmationCode"])
		{
			return t("No such booking found!");
		}

		$cust_data = $this->do_call("GetCustomerProfile", array(
			"customerId" => $this->get_cust_id(),
			"webLanguageId" => $this->_get_web_language_id()
		), "Customers");

		$this->_ci_data($rvs_data["Booking"], $cust_data);

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_checkin", array("ows_rvs_id" => $arr["ows_rvs_id"], "id" => $arr["id"], "section" => aw_global_get("section")))
		));
		$this->_do_errors();

		return $this->parse();
	}		

	private function _ci_data($booking, $customer)
	{
		$this->vars(array(
			"confirmation_number" => $this->_f($booking["ConfirmationCode"]),
			"hotel_name" => $this->_f($booking["HotelName"]),
			"lastname" => $this->_f($booking["GuestLastName"]),
			"car_no" => $this->_req("car_no", ""),
			"firstname" => $this->_f($booking["GuestFirstName"]),
			"room_no" => "__undefined__",
			"dob" => $this->_req("CustomerBirthday", date("d.m.Y", $this->_parse_date($this->_f($customer["Birthday"])))),
			"arrival_date" => date("d.m.Y", $this->_parse_date($this->_f($booking["ArrivalDate"]))),
			"departure" => date("d.m.Y", $this->_parse_date($this->_f($booking["DepartureDate"]))),
			"home_address" => checked($this->_req("adr_type", "") == 1),
			"company_address" => checked($this->_req("adr_type", "") == 2),
			"check_in_time" => $this->_req("CheckInTime", ""),
			"address" => $this->_req("Address", ""),
			"flight_no" => $this->_req("FlightNo", ""),
			"country_select" => $this->_ep_do_country_select($this->_req("CountryCode", $customer["CountryCode"])),
			"email" => $this->_req("Email", $customer["Login"]),
			"is_eu" => checked($this->_req("IsEU", "") == 1),
			"non_eu" => checked($this->_req("IsEU", "") == 2),
			"phone" => $this->_req("Phone", $customer["HomePhone"]),
			"document_options" => $this->_ci_do_document_select($this->_req("Document", "")),
			"non_smoking" => checked($this->_req("RoomType", $customer["RoomSmokingPreferenceId"]) == 2),
			"smoking" => checked($this->_req("RoomType", $customer["RoomSmokingPreferenceId"]) == 3),
			"document_no" => $this->_req("DocumentNo", ""),
			"way_of_payment" => $this->_ci_do_payment_way_select($this->_req("WayPayment", "")),
			"valid_through" => $this->_req("ValidThru", ""),
			"date_of_issue" => $this->_req("DateIssue", ""),
			"address2" => $this->_req("Address2", ""),
			"exp_date" => $this->_req("ExpDate", ""),
			"issuing_office" => $this->_req("IssuingOffice", ""),
			"card_no" => $this->_req("CardNo", ""),
		));
	}

	/**
		@attrib name=submit_checkin nologin="1"
	**/
	function submit_checkin($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		list($d, $m, $y) = explode(".", $arr["CustomerBirthday"]);
		if ($d < 1 || $d > 31 || $m < 1 || $m > 12 || $y < 1900 || $y > date("Y"))
		{
				return $this->_err_ret("do_checkin", $arr, 1);
		}
		list($d, $m, $y) = explode(".", $arr["DateIssue"]);
		if ($d < 1 || $d > 31 || $m < 1 || $m > 12 || $y < 1900 || $y > date("Y"))
		{
				return $this->_err_ret("do_checkin", $arr, 2);
		}
		list($d, $m, $y) = explode(".", $arr["ExpDate"]);
		if ($d < 1 || $d > 31 || $m < 1 || $m > 12 || $y < 1900 || $y > date("Y"))
		{
				return $this->_err_ret("do_checkin", $arr, 3);
		}

		echo "don't know what to do now! <br><hr>data:<br>";
		die(dbg::dump($arr));
	}

	private function _get_document_options()
	{
		return array(
			2 => "Passport",
			3 => "ID Card",
			4 => "Driving License"
		);
	}

	private function _ci_do_document_select($doc_type)
	{
		return $this->picker($doc_type, $this->_get_document_options());
	}

	private function _get_payment_way_options()
	{
		return array(
			1 => "Cash",
			2 => "Voucher",
			11 => "Bank Transfer",
			16 => "Credit Card"
		);
	}

	private function _ci_do_payment_way_select($pw_way)
	{
		return $this->picker($pw_way, $this->_get_payment_way_options());
	}

	private function _w($str)
	{
		return iconv(aw_global_get("charset"), "utf-8//IGNORE", strip_tags($str));
	}

	function _u($url)
	{
		return str_replace("/orb.aw", "/?", $url);
	}

	/**
		@attrib name=display_visits
	**/
	function display_visits($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$this->read_template("all_visits.tpl");
		lc_site_load("reval_customer", $this);

		$return = $this->do_orb_method_call(array(
			"action" => "GetCustomerLastTwoYearVisits",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array("customerId" => $this->get_cust_id()),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		$v = "";
		$sum = 0;
		if (is_array($return["GetCustomerLastTwoYearVisitsResult"]["Visits"]["HotelVisit"]) && !is_array($return["GetCustomerLastTwoYearVisitsResult"]["Visits"]["HotelVisit"][0]))
		{
			$return["GetCustomerLastTwoYearVisitsResult"]["Visits"]["HotelVisit"] = array($return["GetCustomerLastTwoYearVisitsResult"]["Visits"]["HotelVisit"]);
		}
		foreach($return["GetCustomerLastTwoYearVisitsResult"]["Visits"]["HotelVisit"] as $visit)
		{
			$this->vars(array(
				"hotel" => $visit["Hotel"],
				"arrival" => date("d.m.Y", $this->_parse_date($visit["ArrivalDate"])),
				"departure" => date("d.m.Y", $this->_parse_date($visit["DepartureDate"])),
				"num_nights" => $visit["RoomNights"]
			));
			$sum += $visit["RoomNights"];
			$v .= $this->parse("VISIT");
		}
		$this->vars(array(
			"VISIT" => $v,
			"sum_nights" => $sum
		));
		return $this->parse();
	}

	/**
		@attrib name=register_as_customer nologin="1"
		@param card_number optional
		@param bd optional
	**/
	function register_as_customer($arr)
	{
		$this->read_template("register_as_customer.tpl");
		lc_site_load("reval_customer", $this);
		$this->_do_errors();

		$this->vars(array(
			"card_number" => htmlspecialchars($arr["card_number"]),
			"bd" => htmlspecialchars($arr["bd"]),
			"reforb" => $this->mk_reforb("submit_register_as_customer", array("section" => aw_global_get("section"), "id" => 1))
		));

		return $this->parse();
	}

	/**
		@attrib name=submit_register_as_customer nologin="1"
	**/
	function submit_register_as_customer($arr)
	{
		if (empty($arr["card_number"]))
		{
			return $this->_err_ret("register_as_customer", $arr, 1);
		}
		list($d,$m, $y) = explode(".", $arr["bd"]);
		if (empty($arr["bd"]) || $y < 1800 || $y > date("Y") || $m < 1 || $m > 12 || $d < 1 || $d > 31)
		{
			return $this->_err_ret("register_as_customer", $arr, 2);
		}
		$rv = $this->do_call("ValidateCustomerByCardNumberAndBirthday", array(
			"cardNumber" => $arr["card_number"],
			"birthday" => sprintf("%04d", $y).'-'.sprintf("%02d", $m).'-'.sprintf("%02d", $d).'T00:00:00'
		), "Security");
		if ($rv["ValidationStatus"] != "Success")
		{
			return $this->_err_ret("register_as_customer", $arr, 3);
		}

		// write data to session, so that the person can edit their profile
		$_SESSION["reval_fc"]["id"] = $rv["CustomerId"];
                return $this->mk_my_orb("edit_password", array("section" => $arr["section"], "id" => $arr["id"],"show_username" => 1), "reval_customer", false, false, "&", false);
		// create temp hash and send e-mail
		$hash = gen_uniq_id();
		//$this->_insert_temp_hash($hash, $rv["CustomerId"]);

		//$this->_send_temp_email($hash, $rv);
		$_SESSION["reval"]["temp_mail_adr"] = $rv["Email"];

		return $this->mk_my_orb("register_as_customer_median", array("section" => $arr["section"]), "reval_customer", false, false, "&", false);
	}

	private function _insert_temp_hash($hash, $cust_id)
	{
		$this->db_query("INSERT INTO reval_customer_temp_hashes(hash, customer, tm) values('$hash','$cust_id',".time().")");
	}

	private function _send_temp_email($hash, $rv)
	{
			$content = "hash: $hash \nlink: ".$this->mk_my_orb("validate", array("hash" => $hash));

			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
							"froma" => "info@revalhotels.com",
							"fromn" => "Reval Hotels",
							"subject" => t("Your registration on revalhotels.com"),
							"to" => "kristo@struktuur.ee", //$email,
							"body" => strip_tags($content),
			));
			$awm->htmlbodyattach(array(
							"data" => $content,
			));
			$awm->gen_mail();
	}

	/**
		@attrib name=register_as_customer_median nologin="1"
	**/
	function register_as_customer_median($arr)
	{
		$this->read_template("register_as_customer_intermediate.tpl");
		lc_site_load("reval_customer", $this);

		$this->vars(array(
			"email" => $_SESSION["reval"]["temp_mail_adr"]
		));
		unset($_SESSION["reval"]["temp_mail_adr"]);

		return $this->parse();
	}

	/**
		@attrib name=validate nologin="1"
		@param hash required
	**/
	function validate($arr)
	{
		$cust = $this->_get_cust_by_temp_hash($arr["hash"]);
		if (!$cust)
		{
			$this->read_template("validate_invalid.tpl");
			lc_site_load("reval_customer", $this);
			return $this->parse();
		}

		$this->read_template("validate.tpl");
		lc_site_load("reval_customer", $this);

		$this->vars(array(
			"reforb" => $this->mk_reforb("final_validate_customer", array("hash" => $arr["hash"], "section" => $arr["section"]))
		));

		return $this->parse();
	}

	private function _get_cust_by_temp_hash($hash)
	{
		$rv = $this->db_fetch_row("SELECT * FROM reval_customer_temp_hashes WHERE hash = '$hash' AND tm > ".(time() - 24*3600));
		return $rv["customer"];
	}

	/**
		@attrib name=final_validate_customer nologin="1"
	**/
	function final_validate_customer($arr)
	{
		// change emailand whatever to what's needed
	}

	/**
		@attrib name=forgot_password nologin="1"
	**/
	function forgot_password($arr)
	{
			if ($_GET["message"])
			{
				$_GET["error"] = $_GET["message"];
			}
			$this->read_template("forgot_password.tpl");
			lc_site_load("reval_customer", $this);
			$this->_do_errors();
		
			$this->vars(array(
				"reforb" => $this->mk_reforb("submit_forgot_password", array("section" => aw_global_get("section")))
			));
			return $this->parse();
	}

	/**
		@attrib name=submit_forgot_password nologin="1"
	**/
	function submit_forgot_password($arr)
	{
		if (empty($arr["first_name"]))
		{
			return $this->_err_ret("forgot_password", $arr, 1);
		}
		if (empty($arr["last_name"]))
		{
			return $this->_err_ret("forgot_password", $arr, 2);
		}
		if (empty($arr["email"]))
		{
			return $this->_err_ret("forgot_password", $arr, 3);
		}
		$rv = $this->do_call("ValidateCustomerByFirstNameLastNameAndEmail", array(
			"firstName" => $arr["first_name"],
			"lastName" => $arr["last_name"],
			"email" => $arr["email"],
		), "Security");

		if ($rv["ValidationStatus"] != "Success")
		{
			return $this->_err_ret("forgot_password", $arr, 4);
		}

		// generate new password, set it to the user and mail them about it
		$password = generate_password();

		$rv2 = $this->do_call("SetCustomerPassword", array(
			"customerId" => $rv["CustomerId"],
			"password" => $password
		), "Customers", true);
		if ($rv2["SetCustomerPasswordResult"]["ResultCode"] != "Success")
		{
				return $this->_err_ret("forgot_password", $arr, 5);
		}

		$this->_mail_customer_password($rv, $password);
		return str_replace("orb.aw", "", $this->mk_my_orb("forgot_password", array("section" => $arr["section"], "message" => 100)));
	}

	private function _mail_customer_password($rv, $password)
	{
			$this->read_template("password_remind_mail.tpl");
			lc_site_load("reval_customer", $this);
		
			$this->vars(array(
				"first_name" => $rv["FirstName"],
				"last_name" => $rv["LastName"],
				"password" => $password,
			));
			$subject = $this->parse("SUBJECT");
			$content = $this->parse("CONTENT");

			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
							"froma" => "firstclient.estonia@revalhotels.com",
							"fromn" => "Reval Hotels",
							"subject" => trim($subject),
							"to" => $rv["Email"],
							"body" => strip_tags($content),
			));
			$awm->htmlbodyattach(array(
							"data" => $content,
			));
			$awm->gen_mail();
	}

	/**
		@attrib name=complimentary_nights_page nologin=1
	**/
	function complimentary_nights_page($arr)
	{
		if (!$this->get_cust_id())
		{
			return $this->_disp_login();
		}

		$this->read_template("complimentary_nights.tpl");
		lc_site_load("reval_customer", $this);

		$return = $this->do_orb_method_call(array(
			"action" => "GetCustomerComplimentaryNightStatistics",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array("customerId" => $this->get_cust_id()),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		$this->vars(array(
			"active" => $return["GetCustomerComplimentaryNightStatisticsResult"]["UnusedNights"],
			"expired" => $return["GetCustomerComplimentaryNightStatisticsResult"]["ExpiredNights"],
			"used" => $return["GetCustomerComplimentaryNightStatisticsResult"]["UsedNights"],
		));

		$return = $this->do_orb_method_call(array(
			"action" => "GetCustomerUnusedComplimentaryNights",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array("customerId" => $this->get_cust_id()),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		if (is_array($return["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"]) && 
				count($return["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"]))
		{
			foreach($return["GetCustomerUnusedComplimentaryNightsResult"]["ComplimentaryNights"]["ComplimentaryNightCount"] as $row)
			{
				$this->vars(array(
					"number" => $row["NumberOfNights"],
					"date" => date("d.m.Y", $this->_parse_date($row["ExpiryDate"]))
				));
				$s .= $this->parse("NIGHT_LINE");
			}
		}

		$this->vars(array(
			"NIGHT_LINE" => $s
		));

		return $this->parse();
	}
}

?>

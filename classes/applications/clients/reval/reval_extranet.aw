<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_REVAL_EXTRANET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_reval_extranet master_index=brother_of master_table=objects index=aw_oid

@default table=aw_reval_extranet
@default group=general

*/

class reval_extranet extends class_base
{
	const AW_CLID = 1485;

	function reval_extranet()
	{
		$this->init(array(
			"tpldir" => "applications/clients/reval/reval_extranet",
			"clid" => CL_REVAL_EXTRANET
		));
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _get_user_id()
	{
		return (int)$_SESSION["reval_extranet"]["id"];
	}

	function display_login()
	{
		$_SESSION["request_uri_before_auth"] = aw_global_get("REQUEST_URI");

		$this->read_template("need_login.tpl");
		lc_site_load("reval_extranet", $this);

		$this->vars(array(
			"reforb" => $this->mk_reforb("login", array("fail_return" => get_ru()), "users"),
			"IS_ERROR" => ($_GET["flogin"] ? $this->parse("IS_ERROR") : ""),
			"fail_url" => aw_url_change_var("flogin", 1)
		));
		return $this->parse();
	}

	/**
		@attrib name=show_tab1 nologin="1"
	**/
	function show($arr)
	{
		if (!$this->_get_user_id())
		{
			return $this->display_login();
		}
		$this->read_template("show.tpl");
		lc_site_load("reval_extranet", $this);
		$this->vars(array(
			"cru1" => substr(aw_url_change_var("mgr_id", "1"), 0, -1),
			"cru2" => substr(aw_url_change_var("emgr_id", "1"), 0, -1),
		));
		$this->_disp_company_profile_edit($arr["id"]);
		$this->_disp_acct_mgr($arr["id"]);
		$this->_disp_event_mgr($arr["id"]);
		$this->_insert_tabs($arr["id"]);
		return $this->parse();
	}

	/**
		@attrib name=show_tab2 nologin="1"
	**/
	function show_tab2($arr)
	{
		if (!$this->_get_user_id())
		{
			return $this->display_login();
		}
		$this->read_template("visits.tpl");
 lc_site_load("reval_extranet", $this);
		$return = $this->do_orb_method_call(array(
                        "action" => "GetCompanyVisits",
                        "class" => "http://markus.ee/RevalServices/Customers/",
                        "params" => array(
                                "companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
                        ),
                        "method" => "soap",
                        "server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
                ));
//die(dbg::dump($return));
//echo dbg::dump($_SESSION["reval_extranet"]["data"]["CompanyId"]).dbg::dump($return);
		$s = "";
		$sum = 0;
		$sumv = 0;
	        if (isset($return["GetCompanyVisitsResult"]["Visits"]["VisitID"]))
                {
                        $var = array($return["GetCompanyVisitsResult"]["Visits"]);
                }
                else
                {
                        $var = $return["GetCompanyVisitsResult"]["Visits"];
                }	
		foreach($var as $visit)
		{
			$visit["ArrivalDate"] =  date("d.m.Y", reval_customer::_parse_date($visit["ArrivalDate"]));
			$visit["DepartureDate"] = date("d.m.Y", reval_customer::_parse_date($visit["DepartureDate"]));
 			$sumv += $visit["RoomNights"];
			$this->vars($visit);
			$s .= $this->parse("VISIT");
		}	


		$return = $this->do_orb_method_call(array(
                        "action" => "GetAllCompanyBookings",
                        "class" => "http://markus.ee/RevalServices/Customers/",
                        "params" => array(
                                "companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
				"languageId" => $this->_get_web_language_id()
                        ),
                        "method" => "soap",
                        "server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
                ));
//echo dbg::dump(array(
                                //"companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
				//"languageId" => $this->_get_web_language_id()
                       // ));
//die(dbg::dump($return));
//echo dbg::dump($_SESSION["reval_extranet"]["data"]["CompanyId"]).dbg::dump($return);
		//$s = "";
		//$sum = 0;
	        if (isset($return["GetAllCompanyBookingsResult"]["BookingDetails"]["BookingDetails"]["BookingId"]))
                {
                        $var = array($return["GetAllCompanyBookingsResult"]["BookingDetails"]["BookingDetails"]);
                }
                else
                {
                        $var = $return["GetAllCompanyBookingsResult"]["BookingDetails"]["BookingDetails"];
                }	
		$bs = "";
		foreach($var as $visit)
		{
			$visit["ArrivalDate"] =  date("d.m.Y", reval_customer::_parse_date($visit["ArrivalDate"]));
			$visit["DepartureDate"] = date("d.m.Y", reval_customer::_parse_date($visit["DepartureDate"]));
                        $visit["Hotel"] = iconv("utf-8", aw_global_get("charset"), $visit["HotelName"]);
                        $visit["BookingCompanyName"] = iconv("utf-8", aw_global_get("charset"), $visit["BookingCompanyName"]);
                        $visit["BookerName"] = iconv("utf-8", aw_global_get("charset"), $visit["BookerName"]);
                        $visit["RoomNights"] = (int)$visit["LengthOfStay"];
			$sum += $visit["LengthOfStay"];
			$this->vars($visit);
			$bs .= $this->parse("BOOKING");
		}	

		/*$return = $this->do_orb_method_call(array(
                        "action" => "GetActiveCompanyBookings",
                        "class" => "http://markus.ee/RevalServices/Customers/",
                        "params" => array(
                                "companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
				"languageId" => $this->_get_web_language_id()
                        ),
                        "method" => "soap",
                        "server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
                ));
//die(dbg::dump($return));
//echo dbg::dump($_SESSION["reval_extranet"]["data"]["CompanyId"]).dbg::dump($return);
		//$s = "";
		//$sum = 0;
	        if (isset($return["GetActiveCompanyBookingsResult"]["BookingDetails"]["BookingDetails"]["BookingId"]))
                {
                        $var = array($return["GetActiveCompanyBookingsResult"]["BookingDetails"]["BookingDetails"]);
                }
                else
                {
                        $var = $return["GetActiveCompanyBookingsResult"]["BookingDetails"]["BookingDetails"];
                }	
		foreach($var as $visit)
		{
			$visit["ArrivalDate"] =  date("d.m.Y", reval_customer::_parse_date($visit["ArrivalDate"]));
			$visit["DepartureDate"] = date("d.m.Y", reval_customer::_parse_date($visit["DepartureDate"]));
 			$visit["Hotel"] = iconv("utf-8", aw_global_get("charset"), $visit["HotelName"]);
			$visit["RoomNights"] = (int)$visit["LengthOfStay"];
			$sum += $visit["LengthOfStay"];
			$this->vars($visit);
			$bs .= $this->parse("BOOKING");
		}*/	


		$this->vars(array(
			"VISIT" => $s,
			"BOOKING" => $bs,
			"sum_nights" => $sum,
			"sum_nights_v" => $sumv
		));
                $this->_disp_acct_mgr($arr["id"]);
                $this->_disp_event_mgr($arr["id"]);
                $this->_insert_tabs($arr["id"]);
	
		return $this->parse();
		$this->read_template("show_tab2.tpl");
		lc_site_load("reval_extranet", $this);
		$this->vars(array(
			"cru" => $_SERVER["REQUEST_URI"]
		));
		$this->_insert_tabs($arr["id"]);
		$this->_disp_acct_mgr($arr["id"]);
		$this->_disp_event_mgr($arr["id"]);
		return $this->parse();
	}

	private function _disp_acct_mgr($id)
	{
		$return = $this->do_orb_method_call(array(
			"action" => "GetCompanyAccountManagers",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array(
				"companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
				"languageId" => $this->_get_web_language_id()
			),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		if ($return["GetCompanyAccountManagersResult"]["ResultCode"] != "Success")
		{
			return $this->display_login();
		}

		if (!is_array($return["GetCompanyAccountManagersResult"]["AccountManagers"]))
		{
			return;
		}
		$ctry_list = array();
		foreach($return["GetCompanyAccountManagersResult"]["AccountManagers"]["AccountManager"] as $mgr)
		{
			$ctry_list[$mgr["ID"]] = $mgr["CountryName"]." - ".$mgr["CategoryName"];
		}
		foreach($return["GetCompanyAccountManagersResult"]["AccountManagers"]["AccountManager"] as $mgr)
		{
			if (!$_GET["mgr_id"] || $mgr["ID"] == $_GET["mgr_id"])
			{
				$this->vars(array(
					"mgr_fn" => reval_customer::_f($mgr["FirstName"]),
					"mgr_ln" => reval_customer::_f($mgr["LastName"]),
					"mgr_email" => reval_customer::_f($mgr["Email"]),
					"mgr_phone" => reval_customer::_f($mgr["Phone"]),
					"mgr_mobile" => reval_customer::_f($mgr["MobilPhone"]),
					"mgr_skype" => reval_customer::_f($mgr["SkypeID"]),
					"acct_mgr_img" => $this->mk_my_orb("disp_evmgr", array("id" => $mgr["ID"])),
				));
				break;
			}
		}
		$this->vars(array(
			"acct_mgr_ctry_select" => $this->picker($_GET["mgr_id"], $ctry_list)
		));
	}

	/**
		@attrib name=disp_evmgr nologin="1"
		@param id required
	**/
	public function disp_evmgr($arr)
	{
		$return = $this->do_orb_method_call(array(
			"action" => "GetSystemUserPicture",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array(
				"systemUserId" => $arr["id"],
			),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));
		header("Content-type: image/jpeg");
		die(base64_decode($return["GetSystemUserPictureResult"]));
	}

	private function _disp_event_mgr($id)
	{
		$return = $this->do_orb_method_call(array(
			"action" => "GetCompanyEventManagers",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array(
				"companyId" => $_SESSION["reval_extranet"]["data"]["CompanyId"],
				"languageId" => $this->_get_web_language_id()
			),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		if ($return["GetCompanyEventManagersResult"]["ResultCode"] != "Success")
		{
			return $this->display_login();
		}

		if (!is_array($return["GetCompanyEventManagersResult"]["EventManagers"]))
		{
			return;
		}
		if (!is_array($return["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"]))
		{
			return;
		}
		$ctry_list = array();
		if (!is_array($return["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"][0]))
		{
			$tmp = $return;
			$return["GetCompanyEventManagersResult"]["EventManagers"] = array();
			$return["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"] = array(0 => $tmp["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"]);
		}

		foreach($return["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"] as $mgr)
		{
			$ctry_list[$mgr["ID"]] = $mgr["CountryName"]." - ".$mgr["CategoryName"];
		}
		foreach($return["GetCompanyEventManagersResult"]["EventManagers"]["EventManager"] as $mgr)
		{
			if (!$_GET["emgr_id"] || $mgr["ID"] == $_GET["emgr_id"])
			{
				$this->vars(array(
					"emgr_fn" => reval_customer::_f($mgr["FirstName"]),
					"emgr_ln" => reval_customer::_f($mgr["LastName"]),
					"emgr_email" => reval_customer::_f($mgr["Email"]),
					"emgr_phone" => reval_customer::_f($mgr["Phone"]),
					"emgr_mobile" => reval_customer::_f($mgr["MobilPhone"]),
					"emgr_skype" => reval_customer::_f($mgr["SkypeID"]),
					"event_mgr_img" => $this->mk_my_orb("disp_evmgr", array("id" => $mgr["ID"])),
				));
				break;
			}
		}
		$this->vars(array(
			"event_mgr_ctry_select" => $this->picker($_GET["emgr_id"], $ctry_list)
		));
	}

	private function _insert_tabs($id)
	{
		$this->vars(array(
			"tab1_url" => $this->mk_my_orb("show_tab1", array("id" => $id, "section" => aw_global_get("section"))),
			"tab2_url" => $this->mk_my_orb("show_tab2", array("id" => $id, "section" => aw_global_get("section"))),
		));
	}

	public static function get_company_id()
	{	
		return $_SESSION["reval_extranet"]["data"]["CompanyId"];
	}

	private function _disp_company_profile_edit($id)
	{
		$return = $this->do_orb_method_call(array(
			"action" => "GetCompanyProfile",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => array(
				"companyId" => self::get_company_id(),
				"languageId" => $this->_get_web_language_id()
			),
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));

		if ($return["GetCompanyProfileResult"]["ResultCode"] != "Success")
		{
			return $this->display_login();
		}

		$d = $return["GetCompanyProfileResult"]["Profile"];
		list($ct_fn, $ct_ln) = explode(" ", reval_customer::_ef($d["ContactName"]), 2);
		$this->vars(array(
			"company_name_local" => reval_customer::_f($d["CompanyName"]),
			"company_name_eng" => reval_customer::_f($d["CompanyNameInEnglish"]),
			"company_reg_no" => reval_customer::_f($d["CompanyRegisterNr"]),
			"company_contract_no" => "__undefined__",//reval_customer::_f($d[""]),
			"company_vat_no" => reval_customer::_f($d["CompanyVatNumber"]),
			"adr1" => reval_customer::_ef($d["CompanyBusinessAddressLine1"]),
			"adr2" => reval_customer::_ef($d["CompanyBusinessAddressLine2"]),
			"city" => reval_customer::_ef($d["CompanyBusinessCityName"]),
			"zip" => reval_customer::_ef($d["CompanyBusinessPostalCode"]),
			"ct_firstname" => $ct_fn,
			"ct_lastname" => $ct_ln,
			"ct_email" => reval_customer::_ef($d["ContactEmail"]),
			"ct_phone" => reval_customer::_ef($d["ContactPhone"]),
			"ct_phone" => reval_customer::_ef($d["ContactPhone"]),
			"ct_mobile" => reval_customer::_ef($d["ContactMobile"]),
			"ct_business_title" => reval_customer::_ef($d["ContactBusinessTitle"]),
			"reforb" => $this->mk_reforb("submit_view1", array("id" => $id, "ru" => get_ru()))
		));
	}

	function _get_web_language_id()
	{
		$lc = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC");
		return get_instance(CL_OWS_BRON)->get_web_language_id($lc);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_reval_extranet(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}

	/**
		@attrib name=submit_view1 nologin=1
	**/
	public function submit_view1($arr)
	{
		// validate

		$params = array(
			"companyId" => self::get_company_id(),
			"userCustomerId" => $_SESSION["reval_extranet"]["data"]["CustomerId"],
			"businessAddressLine1" => $this->_st($arr["adr1"]),
			"businessAddressLine2" => $this->_st($arr["adr2"]),
			"businessCityName" => $this->_st($arr["city"]),
			"businessPostalCode" => $this->_st($arr["zip"]),
			"contactBusinessTitle" => $this->_st($arr["ct_business_title"]),
			"contactName" => $this->_st($arr["ct_firstname"])." ".$this->_st($arr["ct_lastname"]),
			"contactEmail" => $this->_st($arr["ct_email"]),
			"contactPhone" => $this->_st($arr["ct_phone"]),
			"contactMobile" => $this->_st($arr["ct_mobile"]),
			"languageId" => $this->_get_web_language_id()
		);

		// service
		$return = $this->do_orb_method_call(array(
			"action" => "UpdateCompanyProfile",
			"class" => "http://markus.ee/RevalServices/Customers/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/CustomerService.asmx" // REPL
		));
		return $arr["ru"];
	}

	public function _st($str)
	{
		return iconv(aw_global_get("charset"), "utf-8", strip_tags($str));
	}

	public static function get_cust_id()
	{
		//if ($rv = reval_customer::get_cust_id())
		//{
		//	return $rv;
		//}
		return $_SESSION["reval_extranet"]["data"]["CustomerId"];
	}
}

?>

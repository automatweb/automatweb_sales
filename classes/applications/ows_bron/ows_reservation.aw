<?php
// ows_reservation.aw - OWS Broneering
/*

@classinfo syslog_type=ST_OWS_RESERVATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_ows_reservations index=aw_oid master_table=objects master_index=brother_of

@default table=aw_ows_reservations
@default group=general

@property is_confirmed type=checkbox ch_value=1 field=aw_is_confirmed
@caption Kinnitatud

@default group=cust_data

@property ows_bron type=relpicker field=meta method=serialize reltype=RELTYPE_OWS_BRON table=objects
@caption Broneeringukeskus

@property hotel_id type=textbox field=aw_hotel_id
@caption Hotell

@property rate_id type=textbox field=aw_rate_id
@caption Rate

@property arrival_date type=date_select field=aw_arrival
@caption Saabumine

@property departure_date type=date_select field=aw_departure
@caption Lahkumine

@property num_rooms type=textbox field=aw_num_rooms
@caption Tube

@property adults_per_room type=textbox field=aw_adults_per_room
@caption Adults per room

@property child_per_room type=textbox field=aw_child_per_room
@caption Children per room

@property promo_code type=textbox field=aw_promo_code
@caption Promo kood

@property currency type=textbox field=aw_currency
@caption Valuuta

@property guest_title type=textbox field=aw_guest_title
@caption Guest title

@property guest_firstname type=textbox field=aw_first_name
@caption Guest first name

@property guest_lastname type=textbox field=aw_last_name
@caption Guest last name

@property guest_country type=textbox field=aw_country
@caption Guest country

@property guest_state type=textbox field=aw_state
@caption Guest state

@property guest_city type=textbox field=aw_city
@caption Guest city

@property guest_postal_code type=textbox field=aw_postal_code
@caption Guest postal code

@property guest_adr_1 type=textbox field=aw_adr_1
@caption Guest address line 1

@property guest_adr_2 type=textbox field=aw_adr_2
@caption Guest address line 2

@property guest_phone type=textbox field=aw_phone
@caption Guest phone

@property guest_email type=textbox field=aw_email
@caption Guest email

@property guest_comments type=textbox field=aw_comments
@caption Guest comments

@property guest_bd type=date_select field=aw_bd
@caption S&uuml;nnip&auml;ev

@property smoking type=checkbox ch_value=1 field=aw_smoking
@caption Smoking

@property high_floor type=checkbox ch_value=1 field=aw_high_floor
@caption High floor

@property low_floor type=checkbox ch_value=1 field=aw_low_floor
@caption Low floor

@property is_allergic type=checkbox ch_value=1 field=aw_is_allergic
@caption Allergic

@property is_handicapped type=checkbox ch_value=1 field=aw_is_handicapped
@caption Handicapped

@property guarantee_type type=textbox field=aw_guarantee_type
@caption Cuarantee type

@property guarantee_cc_type type=textbox field=aw_guarantee_cc_type
@caption Cuarantee CC type

@property guarantee_cc_holder_name type=textbox field=aw_guarantee_cc_holder_name
@caption Cuarantee CC holder name

@property guarantee_cc_num type=textbox field=aw_guarantee_cc_num
@caption Cuarantee CC number

@property guarantee_cc_exp_date type=date_select field=aw_guarantee_cc_exp_date
@caption Cuarantee CC exp date

@property payment_type type=textbox field=aw_payment_type
@caption Payment type

@property rate_title type=textbox field=aw_rate_title
@caption Rate title

@property rate_long_note type=textarea rows=10 cols=80 field=aw_rate_long_note
@caption Rate long note

@property rate_room_type_code type=textbox field=aw_rate_room_type_code
@caption Rate room type code

@default group=bron_data

	@property confirmation_code type=textbox field=aw_confirmation_code
	@caption Confirmation code

	@property booking_id type=textbox field=aw_booking_id
	@caption booking id

	@property cancel_deadline type=datetime_select field=aw_cancel_deadline
	@caption Cancel deadline

	@property total_room_charge type=textbox field=aw_total_room_charge
	@caption Total room charge

	@property total_tax_charge type=textbox field=aw_total_tax_charge
	@caption Total tax charge

	@property total_charge type=textbox field=aw_total_charge
	@caption Total charge

	@property charge_currency type=textbox field=aw_charge_currency
	@caption Charge currency

@default group=cancel_data

	@property cancel_type type=textbox field=aw_cancel_type
	@caption T&uuml;histamise p&otilde;hjus

	@property cancel_other type=textbox field=aw_cancel_other
	@caption Muu p&otilde;hjus

@groupinfo cust_data caption="Sisestatud andmed"
@groupinfo bron_data caption="Reserveeringu andmed"
@groupinfo cancel_data caption="T&uuml;histamise p&otilde;hjus"

@reltype OWS_BRON value=1 clid=CL_OWS_BRON
@caption Reserveeringukeskus

*/

class ows_reservation extends class_base
{
	function ows_reservation()
	{
		$this->init(array(
			"tpldir" => "applications/ows_bron/ows_reservation",
			"clid" => CL_OWS_RESERVATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

//	arr($arr["obj_inst"]->meta());
switch($prop["name"])
		{
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_cancel_type":
			case "aw_cancel_other":
			case "cancel_other":
							$this->db_add_col($t, array("name" => $f, "type" => "varchar(255)"));
							return true;

			case "aw_smoking":
			case "aw_high_floor":
			case "aw_low_floor":
			case "aw_is_allergic":
			case "aw_is_handicapped":
			case "aw_bd":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;

			case "aw_rate_title":
			case "aw_rate_long_note":
			case "aw_rate_room_type_code":
				$this->db_add_col($t, array("name" => $f, "type" => "text"));
				return true;
		}
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_ows_reservations (aw_oid int primary key,
				aw_is_confirmed int, aw_hotel_id int, aw_rate_id int,
				aw_arrival int, aw_departure int, aw_num_rooms int,
				aw_adults_per_room int,aw_child_per_room int, aw_promo_code varchar(255),
				aw_currency char(3), aw_guest_title varchar(255), aw_first_name varchar(255),
				aw_last_name varchar(255), aw_country varchar(255), aw_state varchar(255),
				aw_city varchar(255), aw_postal_code varchar(255), aw_adr_1 varchar(255),
				aw_adr_2 varchar(255), aw_phone varchar(255), aw_email varchar(255),
				aw_comments varchar(255), aw_guarantee_type varchar(255),
				aw_guarantee_cc_type varchar(255), aw_guarantee_cc_holder_name varchar(255), aw_guarantee_cc_num varchar(255),
				aw_guarantee_cc_exp_date int, aw_payment_type varchar(255),
				aw_confirmation_code varchar(255), aw_booking_id int, aw_cancel_deadline int,
				aw_total_room_charge double, aw_total_tax_charge double, aw_total_charge double,
				aw_charge_currency varchar(255)
			)");
			return true;
		}
	}

	function bank_fail($arr)
	{
		$o = obj($arr["id"]);
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		$data = $bank_inst->get_payment_info();

//makse kohta info ka eba6nnestunud makse puhul info saata
		$params = array(
  	 		"dttmPayment" => date("Y-m-d" , $data["time"])."T".date("h:m:s", $data["time"]),//?????????????
      			"paymentMethodSubtypeId" => 2,//--------------------
      			"paymentLogPaymentResultId" => 2,//eba6nnestunud makse
      			"paymentCurrency" => $data["curr"]?$data["curr"]:"EEK",
      			"paymentReceived" => $data["sum"]?$data["sum"]:0,
      			"remoteTransactionIdentifier" => $data["ref"]?$data["ref"]:time(),//suvaline tehingu identifikaator
      			"remoteReference" => $data["receipt_no"]?$data["receipt_no"]:time(),//misiganes asi, mille abil on meil v6imalik maksele v6i maksel meie tehingule viidata
      			"resultMessage" =>  $data["msg"]?$data["msg"]:"tekst",//kogu makes kohta tagatstatav info, mida tuleks salvestada
      			"description" => $data["all"]?$data["all"]:"tekst",//k6ik muu tekst, mis eelmiste hulka ei mahu
      			"customerId" => $o->meta("customer_id") ? $o->meta("customer_id") : reval_customer::get_cust_id(),
      			"onlineBookingId" => $o->meta("booking_id") ? $o->meta("booking_id") : 0,
      			"partnerWebsiteGuid" => $o->meta("partnerWebsiteGuid") ? $o->meta("partnerWebsiteGuid") : 0,
      			"partnerWebsiteDomain" => $o->meta("partnerWebsiteDomain") ? $o->meta("partnerWebsiteDomain") : 0,
			"userInfo" => $data["payer"]?$data["payer"]:" ",
   			"hotelId" => $o->prop("hotel_id")?$o->prop("hotel_id"):0,
		);
		$return = $this->do_orb_method_call(array(
			"action" => "RecordPaymentLogEntry",
			"class" => "http://markus.ee/RevalServices/Booking/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServicesTest/BookingService.asmx"
//			"server" => "http://195.250.171.36/RevalServices/BookingService.asmx"
		));

		return $arr["url"];
	}

	function bank_return($arr)
	{
$f = fopen(aw_ini_get("site_basedir")."/files/ows.log", "a");
fwrite($f, date("d.m.Y H:i:s").": ".dbg::dump($arr));

if (!is_oid($arr["id"]))
{
fwrite($f, "die error\n");
	die("error!");
}

		$o = obj($arr["id"]);
		$bank_inst = get_instance(CL_BANK_PAYMENT);
		$data = $bank_inst->get_payment_info();

	//makse kohta info 2ra saata v6i nii

		$params = array(
  	 		"dttmPayment" => date("Y-m-d" , $data["time"])."T".date("h:m:s", $data["time"]),//?????????????
     			"paymentMethodSubtypeId" => 2,//--------------------
      			"paymentLogPaymentResultId" => 1,//edukas makse,... praegu igatahes siia funktsiooni muud ei j6uagi
      			"paymentCurrency" => $data["curr"]?$data["curr"]:"EEK",
      			"paymentReceived" => $data["sum"]?$data["sum"]:0,
      			"remoteTransactionIdentifier" => $data["ref"]?$data["ref"]:time(),//suvaline tehingu identifikaator
      			"remoteReference" => $data["receipt_no"]?$data["receipt_no"]:time(),//misiganes asi, mille abil on meil v6imalik maksele v6i maksel meie tehingule viidata
      			"resultMessage" =>  $data["msg"]?$data["msg"]:"tekst",//kogu makes kohta tagatstatav info, mida tuleks salvestada
      			"description" => $data["all"]?$data["all"]:"tekst",//k6ik muu tekst, mis eelmiste hulka ei mahu
      			"customerId" => $o->meta("customer_id") ? $o->meta("customer_id") : reval_customer::get_cust_id(),
      			"onlineBookingId" => $o->meta("booking_id") ? $o->meta("booking_id") : 0,
      			"partnerWebsiteGuid" => $o->meta("partnerWebsiteGuid") ? $o->meta("partnerWebsiteGuid") : 0,
      			"partnerWebsiteDomain" => $o->meta("partnerWebsiteDomain") ? $o->meta("partnerWebsiteDomain") : 0,
			"userInfo" => $data["payer"]?$data["payer"]:" ",
   			"hotelId" => $o->prop("hotel_id")?$o->prop("hotel_id"):0,
		);
		$return = $this->do_orb_method_call(array(
			"action" => "RecordPaymentLogEntry",
			"class" => "http://markus.ee/RevalServices/Booking/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServicesTest/BookingService.asmx"
//			"server" => "http://195.250.171.36/RevalServices/BookingService.asmx"
		));
//if(aw_global_get("uid") == "struktuur"){		arr($params); arr($return);}
fwrite($f, date("d.m.Y H:i:s").": ".dbg::dump($return)."\n");
			if ($o->prop("is_confirmed") == 1)
			{
				fwrite($f, "return is conf\n");
					return;
			}



	//makse kohta info 2ra saata v6i nii

		$params = array(
  	 		"dttmPayment" => date("Y-m-d" , $data["time"])."T".date("h:m:s", $data["time"]),//?????????????
     			"paymentMethodSubtypeId" => 2,//--------------------
      			"paymentLogPaymentResultId" => 1,//edukas makse,... praegu igatahes siia funktsiooni muud ei j6uagi
      			"paymentCurrency" => $data["curr"]?$data["curr"]:"EEK",
      			"paymentReceived" => $data["sum"]?$data["sum"]:0,
      			"remoteTransactionIdentifier" => $data["ref"]?$data["ref"]:time(),//suvaline tehingu identifikaator
      			"remoteReference" => $data["receipt_no"]?$data["receipt_no"]:time(),//misiganes asi, mille abil on meil v6imalik maksele v6i maksel meie tehingule viidata
      			"resultMessage" =>  $data["msg"]?$data["msg"]:"tekst",//kogu makes kohta tagatstatav info, mida tuleks salvestada
      			"description" => $data["all"]?$data["all"]:"tekst",//k6ik muu tekst, mis eelmiste hulka ei mahu
      			"customerId" => $o->meta("customer_id") ? $o->meta("customer_id") : reval_customer::get_cust_id(),
      			"onlineBookingId" => $o->meta("booking_id") ? $o->meta("booking_id") : 0,
      			"partnerWebsiteGuid" => $o->meta("partnerWebsiteGuid") ? $o->meta("partnerWebsiteGuid") : 0,
      			"partnerWebsiteDomain" => $o->meta("partnerWebsiteDomain") ? $o->meta("partnerWebsiteDomain") : 0,
			"userInfo" => $data["payer"]?$data["payer"]:" ",
   			"hotelId" => $o->prop("hotel_id")?$o->prop("hotel_id"):0,
 "bookingId" => $o->meta("booking_id"),
		);
		$return = $this->do_orb_method_call(array(
			"action" => "RecordPaymentLogEntry",
			"class" => "http://markus.ee/RevalServices/Booking/",
			"params" => $params,
			"method" => "soap",
			"server" => "http://195.250.171.36/RevalServices/BookingService.asmx" // REPL
//			"server" => "http://195.250.171.36/RevalServices/BookingService.asmx"
		));
if(aw_global_get("uid") == "struktuur"){		arr($params); arr($return);}
fwrite($f, date("d.m.Y H:i:s").": ".dbg::dump($return)."\n");

			$checkin = date("Y", $o->prop("arrival_date")).'-'.date("m", $o->prop("arrival_date")).'-'.date("d", $o->prop("arrival_date")).'T00:00:00';

			$checkout = date("Y", $o->prop("departure_date")).'-'.date("m", $o->prop("departure_date")).'-'.date("d", $o->prop("departure_date")).'T23:59:00';

			$l = get_instance("languages");
			$owb = get_instance(CL_OWS_BRON);
			$lang = $owb->get_web_language_id($l->get_langid($o->lang_id()));

			$bd = date("Y-m-d", $o->prop("guest_bd"))."T00:00:00";

			$params = array(
   			"hotelId" => $o->prop("hotel_id"),
      	"rateId" => $o->prop("rate_id"),
      	"arrivalDate" => $checkin,
      	"departureDate" => $checkout,
      	"numberOfRooms" => (int)$o->prop("num_rooms"),
      	"numberOfAdultsPerRoom" => (int)$o->prop("adults_per_room"),
      	"numberOfChildrenPerRoom" => (int)$o->prop("child_per_room"),
      	"promotionCode" => $o->prop("promo_code")." ",
      /*<partnerWebsiteGuid>string</partnerWebsiteGuid>
      <partnerWebsiteDomain>string</partnerWebsiteDomain>
      <corporateCode>string</corporateCode>
      <iataCode>string</iataCode>*/
      	"webLanguageId" => $lang,
      	"customCurrencyCode" => $o->prop("currency"),
				"guestTitle" => $o->prop("guest_title"),
      	"guestFirstName" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_firstname")),
      	"guestLastName" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_lastname")),
      	"guestCountryCode" => $o->prop("guest_country"),
      	"guestStateOrProvince" => $o->prop("guest_state"),
      	"guestCity" => $o->prop("guest_city"),
      	"guestPostalCode" => $o->prop("guest_postal_code"),
      	"guestAddress1" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_adr_1")),
      	"guestAddress2" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_adr_2")),
      	"guestPhone" => $o->prop("guest_phone"),
      	"guestEmail" => $o->prop("guest_email"),
      	"guestComments" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_comments")),
      	"roomSmokingPreferenceId" => (int)$o->prop("smoking") ? 3 : 2,
      	"floorPreferenceId" => ((int)$o->prop("high_floor")) ? 2 : (((int)$o->prop("low_floor")) ? 3 : 1),
      	"isAllergic" => (bool)$o->prop("is_allergic"),
        "isHandicapped" => (bool)$o->prop("is_handicapped"),
                                "guaranteeType" => "Deposit",
                //      "partnerWebsiteGuid" => $o->meta("partnerWebsiteGuid") ? $o->meta("partnerWebsiteGuid") : 0,
                //      "partnerWebsiteDomain" => $o->meta("partnerWebsiteDomain") ? $o->meta("partnerWebsiteDomain") : 0,

        "paymentType" => "NoPayment",
                                "guestBirthday" => $bd,
                                "guaranteeReferenceInfo" => iconv(aw_global_get("charset"), "utf-8", $o->prop("guest_comments")),
                                "customerId" => $o->meta("customer_id") ? $o->meta("customer_id") : reval_customer::get_cust_id(),
                                "bookingID" => $o->meta("booking_id"),
                        );
if($o->meta("partnerWebsiteGuid"))
{
        $params["partnerWebsiteGuid"] = $o->meta("partnerWebsiteGuid");
}

if($o->meta("partnerWebsiteDomain"))
{
 $params["partnerWebsiteDomain"] = $o->meta("partnerWebsiteDomain");
}

			if ($data["bank_id"] == "credit_card")
			{
				$params["guaranteeReferenceInfo"] = "CreditCard Payment";
//				$params["guaranteeReferenceInfo"] .= "\nCreditCard Payment";
			}

//die(dbg::dump($params));
			$return = $this->do_orb_method_call(array(
	//			"action" => "MakeBookingExWithBirthdayAndBookingID",
				"action" => "MakeBookingExWithBirthday",
				"class" => "http://markus.ee/RevalServices/Booking/",
				"params" => $params,
				"method" => "soap",
				"server" => "http://195.250.171.36/RevalServices/BookingService.asmx",
	//			"server" => "http://195.250.171.36/RevalServicesTest/BookingService.asmx"
			));

/*			$return2 = $this->do_orb_method_call(array(
				"action" => "MakeBookingExWithBirthdayAndBookingID",
	//			"action" => "MakeBookingExWithBirthday",
				"class" => "http://markus.ee/RevalServices/Booking/",
				"params" => $params,
				"method" => "soap",
				"server" => "http://195.250.171.36/RevalServices/BookingService.asmx",
//				"server" => "http://195.250.171.36/RevalServicesTest/BookingService.asmx"
			));
*/
//if(aw_global_get("uid") == "struktuur"){			arr($return);arr($return2);}
fwrite($f, date("d.m.Y H:i:s").": ".dbg::dump($params).dbg::dump($return)."\n\n\n\n");
	//echo dbg::dump($return);
			if ($return["MakeBookingExWithBirthdayAndBookingIDResult"]["ResultCode"] != "Success")
			{
				$o->set_meta("query", $params);
				$o->set_meta("result", $return);
				aw_disable_acl();
				$o->save();
				aw_restore_acl();
				$this->proc_ws_error($params, $return);
			}
			//echo "HOIATUS!!! Broneeringud kirjutatakse live systeemi, niiet kindlasti tuleb need 2ra tyhistada!!!! <br><br><br>";
			//echo("makebooking with params: ".dbg::dump($params)." retval = ".dbg::dump($return));

			//$o->set_parent(aw_ini_get("ows.bron_folder"));
			//$o->set_class_id(CL_OWS_RESERVATION);
			if ($this->can("view", $o->prop("ows_bron.confirmed_rvs_folder")))
			{
				$o->set_parent($o->prop("ows_bron.confirmed_rvs_folder"));
			}
			else
			{
				$o->set_parent(aw_ini_get("ows.bron_folder"));
			}
			$o->set_prop("is_confirmed", 1);
			if ($data["bank_id"] == "credit_card")
			{
				$o->set_prop("payment_type", "CreditCard");
			}
			else
			{
				$o->set_prop("payment_type", $params["guaranteeType"]);
			}

			$o->set_prop("confirmation_code", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["ConfirmationCode"]);
			$o->set_prop("booking_id", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["BookingId"]);
			$o->set_prop("cancel_deadline", $owb->parse_date_int($return["MakeBookingExWithBirthdayAndBookingIDResult"]["CancellationDeadline"]));
			$o->set_prop("total_room_charge", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["TotalRoomAndPackageCharges"]);
			$o->set_prop("total_tax_charge", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["TotalTaxAndFeeCharges"]);
			$o->set_prop("total_charge", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["TotalCharges"]);
			$o->set_prop("charge_currency", $return["MakeBookingExWithBirthdayAndBookingIDResult"]["ChargeCurrencyCode"]);

			if (!$o->prop("ows_bron"))
			{
				$o->set_prop("ows_bron", 107222);
			}

			$o->set_meta("query", $params);
				$o->set_meta("result", $return);
			aw_disable_acl();
			$o->save();
			aw_restore_acl();
			$i = get_instance(CL_OWS_BRON);
			$i->send_mail_from_bron($o, true);

                        $orderValue = $return["MakeBookingExWithBirthdayAndBookingIDResult"]["TotalRoomAndPackageCharges"];
                        $orderNumber = $return["MakeBookingExWithBirthdayAndBookingIDResult"]["BookingId"];
                        include_once(aw_ini_get("site_basedir")."/public/vv_td/TD_tracking_booking.php");


			$url = $this->mk_my_orb("display_final_page", array("ows_rvs_id" => $o->prop("confirmation_code"), "section" => 107221), "ows_bron");
			$url = str_replace("automatweb/", "", $url);
			$url = str_replace("/orb.aw?", "/?", $url);
fwrite($f, "redir to $url \n\n\n");
fclose($f);
			header("Location: ".$url);
			die("<script language=javascript>window.location.href='".$url."';</script>");
	}

	function proc_ws_error($parameters, $return)
	{
		//mail("vead@struktu);
		error::raise(array(
			"id" => "ERR_OWS",
			"msg" => "rv = ".dbg::dump($return)." params = ".dbg::dump($parameters)
		));
		//die("ws error ".dbg::dump($return));
header("Location: http://www.revalhotels.com");
die();
	}

	function bank_get_payment_info($o)
	{
		if ($o->prop("confirmation_code"))
		{
			return $o->prop("guest_firstname")." ".$o->prop("guest_lastname")." / ".html::href(array(
					"caption" => $o->prop("confirmation_code"),
					"url" => str_replace("automatweb/orb.aw", "", $this->mk_my_orb("display_final_page", array("ows_rvs_id" => $o->prop("confirmation_code"), "section" => 107220), CL_OWS_BRON)),
			))."<br>".(get_instance(CL_OWS_BRON)->hotel_list[$o->prop("hotel_id")]);
		}
		else
		{
			return $o->prop("guest_firstname")." ".$o->prop("guest_lastname");
		}
	}
}
?>

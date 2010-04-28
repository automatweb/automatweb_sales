<?php

namespace automatweb;
// reval_customer_mailer.aw - Revali klientide meilisaatjs
/*

@classinfo syslog_type=ST_REVAL_CUSTOMER_MAILER relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

*/

class reval_customer_mailer extends class_base
{
	const AW_CLID = 1423;

	public $hotel_lut;

	function reval_customer_mailer()
	{
		$this->init(array(
			"tpldir" => "applications/clients/reval/reval_customer_mailer",
			"clid" => CL_REVAL_CUSTOMER_MAILER
		));

		$this->hotel_lut = array(
			"CENT" => t("Reval Hotel Central"),
			"ELIZ" => t("Reval Hotel Elizabete"),
			"EXP" => t("Reval Inn Tallinn"),
			"LAT" => t("Reval Hotel Latvija"),
			"LIET" => t("Reval Hotel Lietuva"),
			"NER" => t("Reval Hotel Neris"),
			"OLY" => t("Reval Hotel Ol&uuml;mpia"),
			"PARK" => t("Reval Park Hotel & Casino"),
			"RIDZ" => t("Reval Hotel Ridzene"),
		);
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

	function _format_date($tm)
	{
		return date("Y-m-d", $tm)."T00:00:00";
	}

	private function do_call($action, $params, $ns = "Booking", $full_res = false)
	{
		if ($ns == "Booking")
		{
			$fn = "BookingService";
		}
		else
		if ($ns == "Customers")
		{
			$fn = "CustomerService";
		}
		aw_global_set("soap_ns_end", "/");
		$return = $this->do_orb_method_call(array(
			"action" => $action,
			"class" => "http://revalhotels.com/ORS/webservices/",
			"params" => $params,
			"method" => "soap",
			"server" => "https://195.250.171.36/RevalORSService/RRCServices.asmx"
		));
		return $return;
	}

	/**
		@attrib name=daily_check nologin="1"
	**/
	function daily_check($arr)
	{
/*	  $entry = $this->db_fetch_row("SELECT * FROM reval_daily_bookings WHERE conf_no = 2402230");
	  list($entry["First"], $entry["Last"]) = explode(" ", $entry["name"]);
	  $entry["Confirmation_No"] = $entry["conf_no"];
	  $entry["Resort"] = $entry["hotel"];
	  $entry["EMail"] = $entry["email"];
			$html = $this->_gen_mail_ct($entry);
			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
							"froma" => "feedback@revalhotels.com",
							"fromn" => "Reval Hotels",
							"subject" => "Your visit",
							"to" => "kristo@struktuur.ee", //$entry["EMail"],//"erki@struktuur.ee", //$arr["sendto"],
							"body" => strip_tags($html),
			));
			$awm->htmlbodyattach(array(
							"data" => $html,
			));
			$awm->gen_mail();

die("done");*/
		aw_set_exec_time(AW_LONG_PROCESS);
		$rv = $this->do_call("GetGuestsWithEmailByCODate", array(
			"CODate" => date("Y-m-d", time() - 24*3600)
		));
		echo "<html><body>"; //<form action='/orb.aw' method=POST><table><Tr><td>Name</td><td>E-mail</td><td>Hotel</td><td>From</td><td>To</td><td>Confirmation no</td><td>&nbsp;</td></tr>";
		foreach($rv["GetGuestsWithEmailByCODateResult"]["GuestEMailsClass"] as $entry)
		{
			$row = $this->db_fetch_row("SELECT * FROM reval_daily_bookings WHERE conf_no = $entry[Confirmation_No]");
			if (!$row)
			{
				$this->quote(&$entry);
				$this->db_query("INSERT INTO reval_daily_bookings(conf_no, hotel, name, email, tm) values($entry[Confirmation_No], '$entry[Resort]','".$entry["First"]." ".$entry["Last"]."', '$entry[EMail]',".time().")");
			}
			else
			{
				continue;
			}
/*			echo "<tr><td>".$entry["First"]." ".$entry["Last"]."</td>";
			echo "<td>".$entry["EMail"]."</td>";
			echo "<td>".$entry["Resort"]."</td>";
			echo "<td>".$entry["Reserv_Begin_Date"]."</td>";
			echo "<td>".$entry["Reserv_End_Date"]."</td>";
			echo "<td>".$entry["Confirmation_No"]."</td>";
			echo "<td><input name=sel[] type=checkbox value=".$entry["Confirmation_No"]." ></td>";
			echo "</tr>\n";*/

			if ($entry["Resort"] == "EXP")
			{
				continue;
			}

			$html = $this->_gen_mail_ct($entry);
			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
							"froma" => "feedback@revalhotels.com",
							"fromn" => "Reval Hotels",
							"subject" => "Thank you for your stay",
							"to" => $entry["EMail"],//"erki@struktuur.ee", //$arr["sendto"],
							"body" => strip_tags($html),
			));
			$awm->htmlbodyattach(array(
							"data" => $html,
			));
			$awm->gen_mail();
			echo "sent to $entry[EMail] for $entry[Confirmation_No] <br>\n";
			flush();

		}
		die("all done");
		die("</table><input type=hidden name=class value=reval_customer_mailer> <input type=hidden name=action value=sbt_daily_check><input type=submit value=Salvesta></form></body>");
		//die(str_replace("@", "", dbg::dump($rv)));
	}

	/**
		@attrib name=sbt_daily_check
	**/
	function sbt_daily_check($arr)
	{
		$rv = $this->do_call("GetGuestsWithEmailByCODate", array(
			"CODate" => date("Y-m-d", time() - 24*3600)
		));

		$rvs = array();
		foreach(safe_array($arr["sel"]) as $conf_no)
		{
			foreach($rv["GetGuestsWithEmailByCODateResult"]["GuestEMailsClass"] as $entry)
			{
				if ($entry["Confirmation_No"] == $conf_no)
				{
					$rvs[$conf_no] = $entry;
				}
			}
		}

		echo "<html><body><form action='/orb.aw' method=POST>";
		foreach($rvs as $entry)
		{
			echo "<hr><input name=sel[] type=checkbox value=".$entry["Confirmation_No"]." checked><br>\n";		
			echo $this->_gen_mail_ct($entry);
		}

		die("<input type=textbox name=sendto><input type=hidden name=class value=reval_customer_mailer> <input type=hidden name=action value=sbt_daily_confirm><input type=submit value=Salvesta></form>");
	}

	/**
		@attrib name=sbt_daily_confirm
	**/
	function sbt_daily_confirm($arr)
	{
		$rv = $this->do_call("GetGuestsWithEmailByCODate", array(
			"CODate" => date("Y-m-d", time() - 24*3600)
		));

		$rvs = array();
		foreach(safe_array($arr["sel"]) as $conf_no)
		{
			foreach($rv["GetGuestsWithEmailByCODateResult"]["GuestEMailsClass"] as $entry)
			{
				if ($entry["Confirmation_No"] == $conf_no)
				{
					$rvs[$conf_no] = $entry;
				}
			}
		}

		echo "<html><body>";

		foreach($rvs as $entry)
		{
			$html = $this->_gen_mail_ct($entry);
			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
							"froma" => "feedback@revalhotels.com",
							"fromn" => "Reval Hotels",
							"subject" => "Thank you for your stay",
							"to" => $arr["sendto"],
							"body" => strip_tags($html),
			));
			$awm->htmlbodyattach(array(
							"data" => $html,
			));
			$awm->gen_mail();
			echo "sent to $arr[sendto] for $entry[Confirmation_No] <br>\n";
			flush();
		}

		die("all done");
	}

	private function _gen_mail_ct($e)
	{
		$name = iconv("utf-8", aw_global_get("charset")."//IGNORE", $e["First"]." ".$e["Last"]);
		$hotel = $this->hotel_lut[$e["Resort"]];
		$date = date("Y-m-d", time() - 24*3600);
		$cs = aw_global_get("charset");
$html = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <title> Reval Hotels </title>
        <meta http-equiv="Content-Type" content="text/html; charset=$cs" />
        <meta name="generator" content="editplus" />
        <meta name="author" content="" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />
</head>

<body bgcolor="#f3f3f3" style="background: #f3f3f3; padding: 0;" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
If this doesn't display correctly, please visit: http://www.revalhotels.com/en/184345
<div align="center" style="background: #f3f3f3; padding: 20px;">
        <table width="550" cellspacing="0" cellpadding="0">
                <tr>
                        <td><div><a href="http://www.revalhotels.com" target="_new"><img src="http://www.revalhotels.com/img/m2/gfx/rm01_tk01_1_eng.gif" alt="" width="275" height="80" border="0" /></a><a href="http://www.revalinn.com" target="_new"><img src="http://www.revalhotels.com/img/m2/gfx/rm01_tk01_2_eng.gif" alt="" width="275" height="80" border="0" /></a><br /></div></td>
                </tr>
                <tr>
                        <td style="border-left: 1px solid #dbdbdb; border-right: 1px solid #dbdbdb; background: white; padding-top: 1px;">
                                <table cellspacing="0" cellpadding="19" width="548">

                                        <tr>
                                                <td align="left" style="font-family: Arial, Helvetica, sans-serif; padding: 19px; line-height: 18px;">

                                                        <p style="color: #de101f; font-family: Arial, Helvetica, sans-serif; font-size: 18px;">Dear $name,</p>

                                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666;">Thank you for your recent stay at $hotel on $date</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666;">With the goal of constantly trying to improve our service and product quality, we would be grateful if you could take a moment to share your experience with us. The survey will not take more than 1 minute.</p>
                                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666;"><a href="http://www.revalhotels.com/en/	
184345?hotel=$e[Resort]&c=$e[Confirmation_No]"><img src="http://www.revalhotels.com/img/m2/gfx/rm01_btn1.gif" alt="Take the Survey" border="0" /></a></p>
                                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666;">In hope of welcoming you back soon,</p>

                                                        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #666666;"><b>Kind regards,<br />Your Reval Hotels & $hotel Hospitality Team</b></p>
                                                </td>
                                        </tr>
                                </table>
                        </td>
                </tr>
                <tr>

                        <td>
                                <div style="padding: 0; margin: 0; height: 5px; overflow: hidden; font-size: 1px;"><img src="http://www.revalhotels.com/img/m2/gfx/rm01_tk04.gif" alt="" width="550" height="5" /><br /></div>
                        </td>
                </tr>
        </table>
</div>
</body>
</html>

EOT;
		return $html;
	}
}

?>

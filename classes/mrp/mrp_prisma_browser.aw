<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/mrp/mrp_prisma_browser.aw,v 1.8 2008/01/31 13:54:53 kristo Exp $
// mrp_prisma_browser.aw - Reusneri andmete sirvimine 
/*

@classinfo syslog_type=ST_MRP_PRISMA_BROWSER no_comment=1 no_status=1 prop_cb=1 maintainer=voldemar

@default table=objects
@default group=general

@default group=hinnap

	@property s_cust_n type=textbox store=no
	@caption Kliendi nimi

	@property s_ord_num type=textbox store=no
	@caption Number

	@property s_date_from type=date_select store=no default=-1
	@caption Kuup&auml;ev alates

	@property s_date_to type=date_select store=no default=-1
	@caption Kuup&auml;ev kuni

	@property s_salesp_name type=textbox store=no
	@caption M&uuml;&uuml;gimehe nimi

	@property s_btn type=submit caption=Otsi store=no
	@caption Otsi

	@property s_res type=table no_caption=1 store=no

@default group=tellim

	@property ts_cust_n type=textbox store=no
	@caption Kliendi nimi

	@property ts_ord_num type=textbox store=no
	@caption Number

	@property ts_date_from type=date_select store=no default=-1
	@caption Kuup&auml;ev alates

	@property ts_date_to type=date_select store=no default=-1
	@caption Kuup&auml;ev kuni

	@property ts_salesp_name type=textbox store=no
	@caption M&uuml;&uuml;gimehe nimi

	@property ts_btn type=submit caption=Otsi store=no
	@caption Otsi

	@property ts_res type=table no_caption=1 store=no


@default group=view_hp

	@property view_hp type=text store=no

@default group=view_tm

	@property view_tm type=text store=no

@groupinfo hinnap caption="Hinnapakkumised" submit_method=get
@groupinfo tellim caption="Tellimused" submit_method=get

@groupinfo view_hp caption="Vaata hinnapakkumist" submit_method=get
@groupinfo view_tm caption="Vaata tellimust" submit_method=get

*/

class mrp_prisma_browser extends class_base
{
	const AW_CLID = 1176;

	function mrp_prisma_browser()
	{
		$this->init(array(
			"tpldir" => "mrp/mrp_prisma_browser",
			"clid" => CL_MRP_PRISMA_BROWSER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "s_cust_n":
			case "s_ord_num":
			case "s_date_from":
			case "s_date_to":
			case "s_salesp_name":
			case "ts_cust_n":
			case "ts_ord_num":
			case "ts_date_from":
			case "ts_date_to":
			case "ts_salesp_name":
				$this->dequote(&$arr["request"][$prop["name"]]);
				$prop["value"] = $arr["request"][$prop["name"]];
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
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		aw_register_header_text_cb(array(&$this, "make_aw_header"));
	//	$arr["post_ru"] = post_ru();
	}

	function make_aw_header()
        {
                // current user name, logout link
                $us = new user();

                $p_id = $us->get_current_person();
                if (!$p_id)
                {
                        return "";
                }

                $person = obj($p_id);
                $hdr = "<span style=\"font-size: 18px; color: red;\">".$person->prop("name")." | ".html::href(array(
                                "url" => $this->mk_my_orb("logout", array(), "users"),
                                "caption" => t("Logi v&auml;lja")
                        ))."  </span>";

                return $hdr;
        }

	function _init_s_res(&$t)
	{
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("hinnapakkumise number"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
		/*	"type" => "time",
			"format" => "d.m.Y H:i",
Fdd
			"numeric" => 1*/
		));

		$t->define_field(array(
			"name" => "custn",
			"caption" => t("Kliendi nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "jobn",
			"caption" => t("T&ouml;&ouml; nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "salesman",
			"caption" => t("M&uuml;&uuml;gimees"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _get_s_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_s_res($t);

		$sr = array("1=1");

		if ($arr["request"]["s_cust_n"] != "")
		{
			$sr[] = " cust.KliendiNimi LIKE '%".trim($arr["request"]["s_cust_n"])."%' ";
		}

		if ($arr["request"]["s_ord_num"] != "")
		{
			$sr[] = " h.`HINNAPAKKUMINE NR` = '".trim($arr["request"]["s_ord_num"])."' ";
		}

		$df = date_edit::get_timestamp($arr["request"]["s_date_from"]);
		if ($df > -1)
		{
			$sr[] = " h.`KUUPÄEV` >= FROM_UNIXTIME($df) ";
		}

		$dt = date_edit::get_timestamp($arr["request"]["s_date_to"]);
		if ($dt > -1)
		{
			$sr[] = " h.`KUUPÄEV` <= FROM_UNIXTIME($dt) ";
		}

		if ($arr["request"]["s_salesp_name"] != "")
		{
			$sr[] = " salesp.`MüügimeheNimi` LIKE '%".trim($arr["request"]["s_salesp_name"])."%' ";
		}

		if (count($sr) == 1)
		{
			return;
		}
		$sr = join(" AND ", $sr);
		$q ="
			SELECT h.`HINNAPAKKUMINE NR` as nr,
				h.`KUUPÄEV` as date,
				cust.KliendiNimi as custn,
				h.`TÖÖ NIMI` as jobn,
				salesp.`M\xfc\xfcgimeheNimi` as salesman 
			FROM hinnapakkumine h 
			LEFT JOIN kliendid cust ON cust.KliendiID = h.KliendiID
			LEFT JOIN muugimehed salesp ON salesp.`MüügimeheID` = h.`MüügimeheID`
			WHERE $sr
			order by h.`KUUP\xc4EV` desc
			LIMIT 300
		";
		//echo "q = $q <br>";

		$i = get_instance("mrp/mrp_prisma_import");
		$db = $i->_get_conn();

		$db->db_query($q);
		while($row = $db->db_next())
		{
			$row["nr"] = html::href(array(
				"url" => aw_url_change_var(array("group" => "view_hp", "hp_id" => $row["nr"])),
				"caption" => $row["nr"]
			));
			$t->define_data($row);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("nr");
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "general")
		{
			return false;
		}
		if ($arr["id"] == "view_hp" && $_GET["group"] != "view_hp")
		{
			return false;
		}
		if ($arr["id"] == "view_tm" && $_GET["group"] != "view_tm")
		{
			return false;
		}
		return true;
	}

	function _get_view_hp($arr)
	{
	
		$i = get_instance("mrp/mrp_prisma_import");
		$db = $i->_get_conn();
		$d = $db->db_fetch_row("SELECT * FROM hinnapakkumine WHERE `HINNAPAKKUMINE NR` = ".$arr["request"]["hp_id"]);

		$v = "<table width=100% border=2><tr><td width=50% rowspan=2 valign=top><b>Hinnapakkumise andmed:</b><br><br>";

		$v .= "<table border=1>
			<tr><td>&nbsp;</td><td>A</td><td>B</td><td>C</td><td>D</td></tr>
			<tr>
				<td>TIRAAZH</td><td>".$d["a)TIRAAZH"]."</td><td>".$d["b)TIRAAZH"]."</td><td>".$b["c)TIRAAZH"]."</td><td>".$d["d)TIRAAZH"]."</td>
			</tr>
			<tr>
				<td>KÜLJENDUS</td><td>".$d["a)KÜLJENDUS"]."</td><td>".$d["b)KÜLJENDUS"]."</td><td>".$b["c)KÜLJENDUS"]."</td><td>".$d["d)KÜLJENDUS"]."</td>
			</tr>
			<tr>
				<td>SKANEERIMINE</td><td>".$d["a)SKANEERIMINE"]."</td><td>".$d["b)SKANEERIMINE"]."</td><td>".$b["c)SKANEERIMINE"]."</td><td>".$d["d)SKANEERIMINE"]."</td>
			</tr>
			<tr>
				<td>boonus</td><td>".$d["a)boonus"]."</td><td>".$d["b)boonus"]."</td><td>".$b["c)boonus"]."</td><td>".$d["d)boonus"]."</td>
			</tr>
			<tr>
				<td>hind</td><td>".$d["A_hind"]."</td><td>".$d["B_hind"]."</td><td>".$b["C_hind"]."</td><td>".$d["D_hind"]."</td>
			</tr>
			<tr>
				<td>SalesDiscount</td><td>".$d["SalesDiscountA"]."</td><td>".$d["SalesDiscountB"]."</td><td>".$b["SalesDiscountC"]."</td><td>".$d["SalesDiscountD"]."</td>
			</tr>
		</table>";
		foreach($d as $k => $duh)
		{
			if (strpos($k, ")") !== false || strpos($k, "SalesDiscount") !== false || strpos($k, "_hind") !== false)
			{
				continue;
			}
			$v .= "<b>$k</b>: $duh<br>";
		}

		$cust_data = "";
		$cust = $db->db_fetch_row("SELECT * FROM kliendid WHERE KliendiID = $d[KliendiID]");
		foreach($cust as $k => $cd)
		{
			$cust_data .= "<b>".$k."</b>: ".$cd." <br>";
		}

		$cust_cont = "";
		$db->db_query("SELECT * FROM `kliendi kontaktisikud` WHERE kliendiID = $d[KliendiID]");
		while ($row = $db->db_next())
		{
			$cust_cont .= "<b>".$row["eesnimi"]." ".$row["perekonnanimi"]."</b> Mob:".$row["mobiil"]." e-mail:".$row["e-mail"]." Telefon:".$row["telefon"]." Fax:".$row["fax"]." M&auml;rkused:" .$row["märkused"]."<br><br>";
		}
		$v .= "</td><td width=50% valign=top><b>Kliendi andmed:</b><br><br>$cust_data</td></tr><tr><td width=50%><b>Kontaktisikud:</b><br><br> $cust_cont</td></tr></table>";
		$arr["prop"]["value"] = $v;
	}

	function _init_ts_res(&$t)
	{
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Tellimuse number"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
		/*	"type" => "time",
			"format" => "d.m.Y H:i",
Fdd
			"numeric" => 1*/
		));

		$t->define_field(array(
			"name" => "salesman",
			"caption" => t("M&uuml;&uuml;gimees/naine"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "custn",
			"caption" => t("Tellija"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "jobn",
			"caption" => t("T&ouml;&ouml; nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "job_start",
			"caption" => t("T&ouml;&ouml; algus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "job_dead",
			"caption" => t("Tellimuse t&auml;htaeg"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _get_ts_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ts_res($t);

		$sr = array("1=1");

		if ($arr["request"]["ts_cust_n"] != "")
		{
			$sr[] = " cust.KliendiNimi LIKE '%".trim($arr["request"]["ts_cust_n"])."%' ";
		}

		if ($arr["request"]["ts_ord_num"] != "")
		{
			$sr[] = " h.TellimuseNr = '".trim($arr["request"]["ts_ord_num"])."' ";
		}

		$df = date_edit::get_timestamp($arr["request"]["ts_date_from"]);
		if ($df > -1)
		{
			$sr[] = " h.TellimuseKuup >= FROM_UNIXTIME($df) ";
		}

		$dt = date_edit::get_timestamp($arr["request"]["ts_date_to"]);
		if ($dt > -1)
		{
			$sr[] = " h.TellimuseKuup <= FROM_UNIXTIME($dt) ";
		}

		if ($arr["request"]["ts_salesp_name"] != "")
		{
			$sr[] = " salesp.`MüügimeheNimi` LIKE '%".trim($arr["request"]["ts_salesp_name"])."%' ";
		}
		
		if (count($sr) == 1)
		{
			return;
		}
		$sr = join(" AND ", $sr);
		$q ="
			SELECT h.TellimuseNr as nr,
				h.TellimuseKuup as date,
				cust.KliendiNimi as custn,
				h.`TööNimetus` as jobn,
				salesp.`M\xfc\xfcgimeheNimi` as salesman ,
				h.`TööAlgus` as job_start,
				h.`TellimuseTähtaeg` as job_dead
			FROM tellimused h 
			LEFT JOIN kliendid cust ON cust.KliendiID = h.Tellija
			LEFT JOIN muugimehed salesp ON salesp.`MüügimeheID` = h.`Müügimees/naine`
			WHERE $sr
			order by h.`TellimuseKuup` desc
			LIMIT 300
		";
		//echo "q = $q <br>";

		$i = get_instance("mrp/mrp_prisma_import");
		$db = $i->_get_conn();

		$db->db_query($q);
		while($row = $db->db_next())
		{
			$row["nr"] = html::href(array(
				"url" => aw_url_change_var(array("group" => "view_tm", "hp_id" => $row["nr"])),
				"caption" => $row["nr"]
			));
			$t->define_data($row);
		}
		$t->set_default_sorder("desc");
		$t->set_default_sortby("nr");
	}

	function _get_view_tm($arr)
	{
	
		$i = get_instance("mrp/mrp_prisma_import");
		$db = $i->_get_conn();
		$d = $db->db_fetch_row("SELECT * FROM tellimused WHERE TellimuseNr = ".$arr["request"]["hp_id"]);

		$v = "<table width=100% border=2><tr><td width=50% valign=top rowspan=2><b>Tellimuse andmed:</b><br><br>";

		foreach($d as $k => $duh)
		{
			$v .= "<b>$k</b>: $duh<br>";
		}

		$cust_data = "";
		$cust = $db->db_fetch_row("SELECT * FROM kliendid WHERE KliendiID = $d[Tellija]");
		foreach($cust as $k => $cd)
		{
			$cust_data .= "<b>".$k."</b>: ".$cd." <br>";
		}

		$cust_cont = "";
		$db->db_query("SELECT * FROM `kliendi kontaktisikud` WHERE kliendiID = $d[Tellija]");
		while ($row = $db->db_next())
		{
			$cust_cont .= "<b>".$row["eesnimi"]." ".$row["perekonnanimi"]."</b> Mob:".$row["mobiil"]." e-mail:".$row["e-mail"]." Telefon:".$row["telefon"]." Fax:".$row["fax"]." M&auml;rkused:" .$row["märkused"]."<br><br>";
		}
		$v .= "</td><td width=50% valign=top><b>Kliendi andmed:<br><br><br>$cust_data</td></tr><tr><td width=50% valign=top><b>Kontaktisikud</b><br><br><br>$cust_cont</td></tr></table>";
		$arr["prop"]["value"] = $v;
	}
}

?>

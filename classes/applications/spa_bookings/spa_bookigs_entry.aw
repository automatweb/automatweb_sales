<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/spa_bookings/spa_bookigs_entry.aw,v 1.87 2008/11/14 11:36:31 markop Exp $
// spa_bookigs_entry.aw - SPA Reisib&uuml;roo liides 
/*

@classinfo syslog_type=ST_SPA_BOOKIGS_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general_sub

	@property name type=textbox field=name
	@caption Nimi

	@property owner type=relpicker field=meta method=serialize reltype=RELTYPE_OWNER
	@caption Omanik kasutaja

	@property packet_folder_list type=relpicker reltype=RELTYPE_PFL_FOLDER multiple=1 field=meta method=serialize
	@caption Tootepakettide kaustad

	@property user_group type=relpicker reltype=RELTYPE_GROUP field=meta method=serialize
	@caption Kasutajagrupp, millesse pannakse selle liidese kaudu sisestatud kasutajad

	@property persons_folder type=relpicker reltype=RELTYPE_PERSONS_FOLDER field=meta method=serialize
	@caption Isikute asukoht

	@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE field=meta method=serialize
	@caption Ladu

	@property products_folder_list type=select multiple=1 field=meta method=serialize
	@caption Toodete kaustad

	@property print_view_ctr type=relpicker reltype=RELTYPE_PRINT_CTR field=meta method=serialize
	@caption Printvaate andmdete valideerimise kontroller

	@property if_section type=relpicker reltype=RELTYPE_IF_SECT field=meta method=serialize
	@caption Saidipoolne kaust

@default group=settings_mail

	@property b_send_mail_to_user type=checkbox ch_value=1 field=meta method=serialize
	@caption Saata kasutajale meil broneeringu tegemisel?

	@property b_mail_from_name type=textbox field=meta method=serialize
	@caption Meili from nimi

	@property b_mail_from_addr type=textbox field=meta method=serialize
	@caption Meili from aadress

	@property b_mail_subject type=textbox field=meta method=serialize
	@caption Meili subjekt

	@property b_mail_content type=textarea rows=10 cols=50 field=meta method=serialize
	@caption Meili sisu

	@property b_ex_mail_content type=textarea rows=10 cols=50 field=meta method=serialize
	@caption Meili sisu olemasolevale kasutajale
	
	@property b_mail_legend type=text store=no 
	@caption Meili sisu legend


@default group=ppl_entry

	@property cust_entry_fb type=text store=no no_caption=1

	@property cust_entry type=table store=no no_caption=1


@default group=cust,all_bookings

	@layout ver_split type=hbox width=10%:90%

		@layout search type=vbox area_caption=Otsing parent=ver_split closeable=1

			@property s_fn type=textbox captionside=top store=no parent=search size=23
			@caption Eesnimi

			@property s_ln type=textbox captionside=top store=no parent=search size=23
			@caption Perenimi

			@property s_tb type=textbox captionside=top store=no parent=search size=23
			@caption Reisib&uuml;roo

			@property s_date_from type=date_select captionside=top store=no parent=search format=day_textbox,month_textbox,year_textbox
			@caption Alates

			@property s_date_to type=date_select captionside=top store=no parent=search format=day_textbox,month_textbox,year_textbox
			@caption Kuni

			@property s_date_not_set type=checkbox ch_value=1 captionside=top store=no parent=search no_caption=1
			@caption Ajad m&auml;&auml;ramata

			@property s_package type=select captionside=top store=no parent=search
			@caption Pakett

			@property s_btn type=submit store=no parent=search no_caption=1
			@caption Otsi

		@property s_res type=table no_caption=1 store=no parent=ver_split


@default group=my_bookings,my_bookings_agent

	@property my_bookings type=table store=no no_caption=1


@groupinfo general_sub caption="&Uuml;ldine" parent=general
@groupinfo settings_mail caption="Meiliseaded" parent=general

@groupinfo ppl_entry caption="Isikud"
@groupinfo cust caption="Kliendid" submit=no
@groupinfo my_bookings caption="Minu broneeringud" 
@groupinfo my_bookings_agent caption="Klientide broneeringud" 
@groupinfo all_bookings caption="K&otilde;ik" 

@groupinfo transl caption=T&otilde;lgi
@default group=transl
	
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@reltype PFL_FOLDER value=1 clid=CL_MENU
@caption Tootepakettide kaust

@reltype GROUP value=2 clid=CL_GROUP
@caption Kasutajagrupp

@reltype PERSONS_FOLDER value=3 clid=CL_MENU
@caption Isikute asukoht

@reltype WAREHOUSE value=4 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype PRINT_CTR value=5 clid=CL_FORM_CONTROLLER
@caption Kontroller

@reltype OWNER value=6 clid=CL_USER
@caption Omanik

@reltype IF_SECT value=7 clid=CL_MENU
@caption Kaust
*/

class spa_bookigs_entry extends class_base
{
	function spa_bookigs_entry()
	{
		$this->init(array(
			"tpldir" => "applications/spa_bookings/spa_bookings_entry",
			"clid" => CL_SPA_BOOKIGS_ENTRY
		));
		$this->trans_props = array(
			"b_mail_content" , "b_ex_mail_content", "b_mail_subject"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "products_folder_list":
				if(!is_oid($arr["obj_inst"]->prop("warehouse")))
				{
					return PROP_IGNORE;
				}
				$sw = get_instance(CL_SHOP_WAREHOUSE);
				$w = obj($arr["obj_inst"]->prop("warehouse"));
				$conf = obj($w->prop("conf"));
				$tmp = $conf->prop("prod_fld");
				
				$ot = new object_tree(array(
					"parent" => $tmp,
					"lang_id" => array(),
					"site_id" => array(),
					"class_id" => CL_MENU,
				));
					
				//$rv = array($o->prop("prod_folders"));
				foreach($ot->ids() as $id)
				{
					$o = obj($id);
					$prop["options"][$id] = $o->name();
				}
				
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
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["s_fn"] = $arr["request"]["s_fn"];
		$arr["args"]["s_ln"] = $arr["request"]["s_ln"];
		$arr["args"]["s_date_from"] = $arr["request"]["s_date_from"];
		$arr["args"]["s_date_to"] = $arr["request"]["s_date_to"];
		$arr["args"]["s_package"] = $arr["request"]["s_package"];
		$arr["args"]["s_date_not_set"] = $arr["request"]["s_date_not_set"];
		$arr["args"]["s_tb"] = $arr["request"]["s_tb"];
	}	

	function _init_cust_entry_t(&$t)
	{
		$t->define_field(array(
			"name" => "firstname",
			"caption" => t("Eesnimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "lastname",
			"caption" => t("Perenimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-post"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "package",
			"caption" => t("Pakett"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "password",
			"caption" => t("Parool"),
			"align" => "center",
		));
	}

	function _get_cust_entry($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cust_entry_t($t);

		for($i = 0; $i < 5; $i++)
		{
			$t->define_data(array(
				"firstname" => html::textbox(array(
					"name" => "d[$i][fn]",
					"size" => 10
				)),
				"lastname" => html::textbox(array(
					"name" => "d[$i][ln]",
					"size" => 10
				)),
				"email" => html::textbox(array(
					"name" => "d[$i][email]",
					"size" => 10
				)),
				"start" => html::date_select(array(
					"name" => "d[$i][start]",
					"format" => array("day_textbox", "month_textbox", "year_textbox")
				)),
				"end" => html::date_select(array(
					"name" => "d[$i][end]",
					"format" => array("day_textbox", "month_textbox", "year_textbox")
				)),
				"package" => html::select(array(
					"name" => "d[$i][package]",
					"options" => $this->_get_pk_list($arr["obj_inst"])
				)),
				"password" => html::textbox(array(
					"name" => "d[$i][pass]",
					"size" => 10,
					"value" => generate_password(array("length" => 6, "chars" => "1234567890"))
				)),
			));
		}
		$t->set_sortable(false);
	}

	function _set_cust_entry($arr)
	{
		//xml_rpc teenuse jaoks
		$_SESSION["add_package_service"] = array();
//die(dbg::dump($arr["request"]));
		classload("core/date/date_calc");
		for($i = 0; $i < 15; $i++)
		{
			$d = $arr["request"]["d"][$i];
			if ($d["fn"] != "" && $d["ln"] != "" && $d["pass"] != "")
			{
				$start = date_edit::get_timestamp($d["start"]);
				$end = date_edit::get_timestamp($d["end"]);
				if ($end < 100 && $this->can("view", $d["package"]))
				{
					$pko = obj($d["package"]);
					list($len, $wd_start) = explode(";", $pko->comment());
					$end = $start + 24*3600*($len-1);
					if ($wd_start > 0)
					{
						if (convert_wday(date("w", $start)) != $wd_start)
						{
							$arr["prop"]["error"] = sprintf(t("Reserveering peab algama %s"), aw_locale::get_lc_weekday($wd_start));
							return PROP_FATAL_ERROR;
						}
					}
				}
			}
		}

		//selline asi et saaks juhul kui teise tulba p2is pole t2idetud, siis teises tulbas oleva info esimesse
		if(!$arr["request"]["d"][1]["start"]["day"]){
			$arr["request"]["d"][0]["ppl"] =  array_merge($arr["request"]["d"][0]["ppl"], $arr["request"]["d"][1]["ppl"]);
			unset($arr["request"]["d"][1]["ppl"]);
		}
		//uuendus, et siis need mis erineva mailiaadressiga, need l2heks omaette broneeringuks
		for($i = 0; $i < 15; $i++)
		{
			$d = $arr["request"]["d"][$i];
			foreach($d["ppl"] as $key=> $s)
			{
				if($s["email"] && $s["email"] != $d["email"])
				{
					$arr["request"]["d"][] = array(
						"fn" => $s["fn"],
                        "ln" => $s["ln"],
                        "email" => $s["email"],
						"gender" => $s["gender"],
						"birthday" => $s["end"],

						"pass" => $d["pass"],
						"start" => $d["start"],
						"package" =>  $d["package"],
					);
					unset($arr["request"]["d"][$i]["ppl"][$key]);
				}
			}
		}

		$feedback = "";
		
		//if(aw_global_get("uid") == "reiskar"){arr($arr);}
		
		//see nyyd selleks, et kui esimeses tulbas on infot ja teises mitte
	/*	$default_data = array();
		
		if($d["start"]["day"])
		{
			$default_data = array(
				"start" => $d["start"],
				"end" => $d["end"],
				"package" => $d["package"],
				"email" => $d["email"],
				"fn" => $d["fn"],
				"ln" => $d["ln"],
				"birthday" => $d["birthday"],
				"gender" => $d["gender"],
				"pass" => $d["pass"],
			);
		}
		else
		{
			foreach($default_data as $key => $val)
			{
				$d[$key] = $val;
			}
		}
	*/	
		
		for($i = 0; $i < 15; $i++)
		{
			$d = $arr["request"]["d"][$i];
			if ($d["fn"] != "" && $d["ln"] != "" && $d["pass"] != "")
			{
				enter_function("sbe::begin");
				$start = date_edit::get_timestamp($d["start"]);
				$end = date_edit::get_timestamp($d["end"]);
				if ($end < 100 && $this->can("view", $d["package"]))
				{
					$pko = obj($d["package"]);
					list($len, $wd_start) = explode(";", $pko->comment());
					$end = ($start + 24*3600*($len-1));
				}
//die("start = ".date("d.m.Y H:i", $start)." end = ".date("d.m.Y H:i", $end)." len = $len <br>");
				//$bd = date_edit::get_timestamp($d["birthday"]);
				// create person, user, booking

				// check if person exists
				$ol = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"lang_id" => array(),
					"site_id" => array(),
					"CL_CRM_PERSON.RELTYPE_EMAIL.mail" => $d["email"]
				));
				$existing_user = false;
				if ($d["email"] != "" && $ol->count())
				{
					$u = get_instance("users");
					foreach($ol->arr() as $p)
					{
						$p_i = $p->instance();
						$user = $p_i->has_user($p);
						if(is_object($user) && array_key_exists($arr["obj_inst"]->prop("user_group") , $user->get_groups_for_user()))
						{
							$existing_user = true;
							break;
						}
					}
/*					$p = $ol->begin();
					$p_i = $p->instance();
					$user = $p_i->has_user($p);
					$existing_user = true;
*/
				}
				exit_function("sbe::begin");
				if (!$existing_user)
				{
					$existing_user = false;
					$eml = obj();
					$eml->set_class_id(CL_ML_MEMBER);
					$eml->set_name($d["fn"]." ".$d["ln"]." <".$d["email"].">");
					$eml->set_prop("name", $d["fn"]." ".$d["ln"]);
					$eml->set_prop("mail", $d["email"]);
					$eml->set_parent($arr["obj_inst"]->prop("persons_folder"));
					$eml->save();
	
					$p = obj();
					$p->set_class_id(CL_CRM_PERSON);
					$p->set_parent($arr["obj_inst"]->prop("persons_folder"));
					$p->set_name(trim($d["fn"])." ".trim($d["ln"]));
					$p->set_prop("firstname", trim($d["fn"]));
					$p->set_prop("lastname", trim($d["ln"]));
					$p->set_prop("email", $eml->id());
					$p->set_prop("birthday", sprintf("%04d-%02d-%02d", $d["birthday"]["year"], $d["birthday"]["month"], $d["birthday"]["day"]));
					$p->set_prop("gender", $d["gender"]);
					$p->save();
					$p->connect(array(
						"to" => $eml->id(),
						"type" => "RELTYPE_EMAIL"
					));

					$cu = get_instance("crm/crm_user_creator");
					$uid = $cu->get_uid_for_person($p, false, true);
					$u = get_instance(CL_USER);
					$user = $u->add_user(array(
						"uid" => $uid,
						"email" => $d["email"],
						"password" => $d["pass"],
						"real_name" => $d["fn"]." ".$d["ln"]
					));

					$user->connect(array(
						"to" => $p->id(),
						"type" => "RELTYPE_PERSON"
					));
					$user->set_prop("email", $d["email"]);
					$user->set_prop("after_login_redir", $this->mk_my_orb("change", array(
							"id" => $arr["obj_inst"]->id(), "group" => "my_bookings", "section" => "3169"
						), CL_SPA_BOOKIGS_ENTRY));  
					$user->save();

					if ($arr["obj_inst"]->prop("user_group"))
					{
						$gr = get_instance(CL_GROUP);
						$gr->add_user_to_group($user, obj($arr["obj_inst"]->prop("user_group")));
					}
				}

				$booking = obj();
				$booking->set_parent($arr["obj_inst"]->prop("persons_folder"));
				$booking->set_name(sprintf("Broneering %s %s - %s", $d["fn"]." ".$d["ln"], date("d.m.Y", $start), date("d.m.Y", $end)));
				$booking->set_class_id(CL_SPA_BOOKING);
				$booking->set_prop("person", $p->id());
				$booking->set_prop("start", $start);
				$booking->set_prop("end", $end);
				$booking->set_prop("package", $d["package"]);
				$booking->save();
//echo "booking = ".$booking->id()." <br>";
				$this->created_booking = $booking->id();
				// for this booking, create empty reservations for all products so we can search by them
				$booking_inst = $booking->instance();
				$booking_inst->check_reservation_conns($booking);
				$po = obj($d["packet"]);
				if (is_admin())
				{
					$feedback .= sprintf(t("Lisasin kasutaja %s, isiku %s ja <a href='%s'>broneeringu</a> paketile %s algusega %s ja l&otilde;puga %s<br>"), 
						is_admin() ? html::obj_change_url($user->id()) : $user->name(),
						is_admin() ? html::obj_change_url($p->id()) : $p->name(),
						is_admin() ? html::get_change_url($booking->id(), array("return_url" => $arr["request"]["post_ru"])) : "#",
						is_admin() ? html::obj_change_url($po->id()) : $po->name(),
						date("d.m.Y", $start), 
						date("d.m.Y", $end)
					);
				}
				else
				{
					$feedback .= sprintf(t("Lisasin kasutaja %s, isiku %s ja broneeringu paketile %s algusega %s ja l&otilde;puga %s<br>"), 
						$user->name(),
						$p->name(),
						$po->name(),
						date("d.m.Y", $start), 
						date("d.m.Y", $end)
					);
				}
				$_SESSION["add_package_service"]["user"] = $user->prop("uid");
				$_SESSION["add_package_service"]["password"] =  $d["pass"];
				$_SESSION["add_package_service"]["reservation_id"] = $booking->id();
				// if other ppl were entered, then create reservations for them and connect those to the same booking so that one user can view it
				if (is_array($d["ppl"]) && count($d["ppl"]))
				{
					$feedback .= $this->_add_ppl_entry($d, $booking);
				}
				enter_function("sbe::mail");
				if(!$not_send_email)
				{
					if (is_email($d["email"]))
					{
						if ($arr["obj_inst"]->prop("b_send_mail_to_user") && !$existing_user)
						{
							send_mail(
								$d["email"], 
								$arr["obj_inst"]->trans_get_val("b_mail_subject"), 
								str_replace(array("#uid#", "#pwd#", "#login_url#"), array($user->prop("uid"), $d["pass"], aw_ini_get("baseurl")."/login.aw"), $arr["obj_inst"]->trans_get_val("b_mail_content")),
								"From: ".$this->_get_from_addr($arr["obj_inst"])
							);
						}
						if($arr["obj_inst"]->prop("b_ex_mail_content") && $existing_user)
						{
							$us = get_instance("users");
							send_mail(
								$d["email"],
								$arr["obj_inst"]->trans_get_val("b_mail_subject"), 
								str_replace(array("#uid#", "#pwd_hash_link#", "#login_url#"), array($user->prop("uid"),$us->get_change_pwd_hash_link($user->id()), aw_ini_get("baseurl")."/login.aw"), $arr["obj_inst"]->trans_get_val("b_ex_mail_content")),
								"From: ".$this->_get_from_addr($arr["obj_inst"])
							);
						}
					}
				}
				exit_function("sbe::mail");
			}
		}
		$_SESSION["spa_bookings_entry_fb"] = $feedback;
//		if(aw_global_get("uid") == "struktuur") {aw_shutdown(); die();}
	}

	function _get_cust_entry_fb($arr)
	{
		if ($_SESSION["spa_bookings_entry_fb"] == "")
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["value"] = $_SESSION["spa_bookings_entry_fb"];
		unset($_SESSION["spa_bookings_entry_fb"]);
	}

	function _get_s_res($arr)
	{
		return $this->_get_my_bookings($arr);
	}

	function _get_pk_list($o)
	{
		static $pk_list;
		if (!is_array($pk_list))
		{
			$ot = new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => reset($o->prop("packet_folder_list")),
				"lang_id" => array(),
				"site_id" => array()
			));
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PACKET,
				"parent" => $ot->ids(),
				"lang_id" => array(),
				"site_id" => array()
			));
			$pk_list = array();
			foreach($ol->arr() as $o)
			{
				$pk_list[$o->id()] = $o->trans_get_val("name");
			}
		}
		return $pk_list;
	}

	function get_search_results($r, $o)
	{
		$d = array(
			"class_id" => CL_SPA_BOOKING,
			"lang_id" => array(),
			"site_id" => array(),
		);

		$cnt = 3;
		if ($r["group"] == "cust")
		{
			$d["createdby"] = aw_global_get("uid");
			$cnt = 4;
		}

		if ($r["s_tb"] && !isset($d["createdby"]))
		{
			$d["createdby"] = "%".$r["s_tb"]."%";
		}

		if ($r["s_date_not_set"])
		{
			// we need to list all bookings that the person has not set times for
			$d[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_SPA_BOOKING.RELTYPE_ROOM_BRON.start1" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
					"CL_SPA_BOOKING.RELTYPE_ROOM_BRON.end" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
				)
			));
		}

		if ($r["s_fn"] != "")
		{
			$d["CL_SPA_BOOKING.person.firstname"] = "%".$r["s_fn"]."%";
		}
		if ($r["s_ln"] != "")
		{
			$d["CL_SPA_BOOKING.person.lastname"] = "%".$r["s_ln"]."%";
		}
		$from = date_edit::get_timestamp($r["s_date_from"]);
		$to = date_edit::get_timestamp($r["s_date_to"]);
		if ($from > 100 && $to > 100)
		{
			$d[] = new obj_predicate_compare(OBJ_COMP_IN_TIMESPAN, array("start", "end"), array($from, $to));
		}
		else
		if ($from > 100)
		{
			$d["start"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		else
		if ($to > 100)
		{
			$d["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
		}

		if ($r["s_package"])
		{
			$d["package"] = $r["s_package"];
		}

		if (count($d) > $cnt)
		{
			$ol = new object_list($d);
			return $ol->arr();
		}
		return array();
	}

	function _get_s_fn($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_s_tb($arr)
	{
		if ($arr["request"]["group"] == "cust")
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_s_date_not_set($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_s_ln($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_s_date_from($arr)
	{
		$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
	}

	function _get_s_package($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
		$arr["prop"]["options"] = array("" => t("--vali--")) +  $this->_get_pk_list($arr["obj_inst"]);
	}

	function _get_s_date_to($arr)
	{
		$arr["prop"]["value"] = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
	}

	function _init_my_bookings(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Toode"),
			"align" => "right"
		));
/*		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "center"
		));
*/	}

	function _get_my_bookings($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_my_bookings($t);
		$t->set_sortable(false);
		// get bookings for my person
		$p = get_current_person();
		if ($arr["request"]["group"] == "my_bookings")
		{
			$ol = new object_list(array(
				"class_id" => CL_SPA_BOOKING,
				"lang_id" => array(),
				"site_id" => array(),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"person" => $p->id(),
						"CL_SPA_BOOKING.RELTYPE_MAIN_PERSON" => $p->id()
					)
				)),
				"sort_by" => "objects.oid DESC",
			));

		}
		else
		if ($arr["request"]["group"] == "cust" || $arr["request"]["group"] == "all_bookings")
		{
			$ol = new object_list();
			$sr = $this->get_search_results($arr["request"], $arr["obj_inst"]);
			$ol->add($sr);

		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_SPA_BOOKING,
				"lang_id" => array(),
				"site_id" => array(),
				"createdby" => aw_global_get("uid"),
				"start" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
				"sort_by" => "objects.oid DESC",
			));
		}
		$payment_info = "";
		$total_payment_amt = 0;
		$extra_stuff=array();
		foreach($ol->arr() as $o)
		{
			classload("vcl/table");
			$ot = new aw_table(array(
				"layout" => "generic"
			));
			$ot->define_field(array(
				"name" => "name",
	//			"caption" => t("Toode"),
				"align" => "right"
			));
			$ot->define_field(array(
				"name" => "when",
	//			"caption" => t("Millal"),
				"align" => "center"
			));
			// bookingul has package
			// package has products
			// rooms have products
			// so, list all the products in the package and for each product let the user select from all the rooms that have that package
			if (!$this->can("view", $o->prop("package")))
			{
				continue;
			}
			$package = obj($o->prop("package"));//if(aw_global_get("uid") == "test.test")arr($o->name());
			$pk = $package->instance();
			$dates = $this->get_booking_data_from_booking($o);

			$booking_str = sprintf(t("Broneering %s, %s - %s, pakett %s"), is_admin() ? html::href(array(
					"url" => "mailto:".$o->prop("person.email.mail"),
					"caption" => $o->prop("person.name")
				)) : $o->prop("person.name"),
				date("d.m.Y", $o->prop("start")),
				date("d.m.Y", $o->prop("end")),
				$package->trans_get_val("name")
			);
			$booking_str2 = html::href(array(
				"caption" => $booking_str,
				"url" => "javascript:void(0)",
				"onclick" => 'el=document.getElementById("bk'.$o->id().'");
					el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
					el=document.getElementById("bka'.$o->id().'");
					el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
				',
			));
			
			$booking_str = html::href(array(
				"caption" => $booking_str,
				"url" => "javascript:void(0)",
				"onclick" => 'el=document.getElementById("bk'.$o->id().'");
					el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
					el=document.getElementById("bka'.$o->id().'");
					el.style.display == "none" ? el.style.display = "block" : el.style.display = "none";
				',
			));
			
			if (is_admin())
			{
				$booking_str .= " / ".html::popup(array(
				//	"url" => $this->mk_my_orb("add_pkt", array("id" => $o->id(), "r" => get_ru())),
					"url" => $this->mk_my_orb("add_prod_to_bron", array("bron" => $o->id(), "wb" => $arr["obj_inst"]->id())),
					"caption" => t("Lisa teenus"),
					"width" => 600,
					"height" => 400,
					"scrollbars" => 1,
					"resizable" => 1
				));
			}
			else
			{
				$booking_str .= " / ".html::href(array(
		//			"url" => $this->mk_my_orb("add_pkt", array("id" => $o->id(), "r" => get_ru())),
					"url" => $this->mk_my_orb("add_prod_to_bron", array("bron" => $o->id(), "id" => 11150, "r" => get_ru()), "spa_customer_interface"), 
//					"url" => $this->mk_my_orb("add_prod_to_bron", array("bron" => $o->id(), "wb" => $arr["obj_inst"]->id())),
					"caption" => t("Lisa teenus"),
/*					"width" => 600,
					"height" => 400,
					"scrollbars" => 1,
					"resizable" => 1*/
				));
			}
			$booking_str .= " / ".html::href(array(
				"url" => $this->mk_my_orb("print_booking", array("id" => $o->id(), "wb" => $arr["obj_inst"]->id())),
				"caption" => t("Prindi"),
				"target" => "_blank"
			));
		
			if (!is_admin())
			{
				$has_times = count($o->meta("extra_prods"));
				foreach(safe_array($o->meta("extra_prods")) as $extra_item_entry)
				{
					$rb = obj($extra_item_entry["reservation"]);
					if ($rb->prop("start1") < 100 || $rb->prop("verified"))
					{
						$has_times = $has_times - 1;
					}
				}
				if ($has_times)
				{
					$booking_str .= " / ".html::href(array(
						"caption" => t("Maksa"),
						"url" => $this->mk_my_orb("pay", array(
							"id" => $o->id(),
							"r" => get_ru(),
							"bank_payment" => 13574,
							"section" => aw_global_get("section"),
						), "spa_customer_interface")
					));
					$sci = get_instance(CL_SPA_CUSTOMER_INTERFACE);
					$tmp_sum = $sci->get_extra_prods_sum($o->id());
					$total_payment_amt += $tmp_sum;
					$payment_info .= "<br>".sprintf(t("%s lisateenused %s EEK"),
						$o->prop("person.name"),
						$tmp_sum
					);
				}
			}
			if ($arr["request"]["group"] == "cust")
			{
				$booking_str .= " ".html::get_change_url($o->id(), array("return_url" => get_ru()), t("Muuda"));
			}

			$fd = array();
			$has_unc = false;
			$prod_list = $pk->get_products_for_package($package);
			$grp_list = $pk->get_group_list($package);
			foreach(safe_array($o->meta("extra_prods")) as $extra_item_entry)
			{
				$grp_list[] = "__ei|".$extra_item_entry["prod"];
				$extra_stuff[$extra_item_entry["prod"]] = $extra_item_entry["prod"];
			}
			foreach($grp_list as $prod_group)
			{
				// repeat group by the count of the first product in the group
				$prods_in_group = $pk->get_products_in_group($package, $prod_group);
				if (substr($prod_group, 0, 4) == "__ei")
				{
					list(, $prod_id) = explode("|", $prod_group);
					$prods_in_group = array($prod_id);
				}
				$first_item_count = max(1,$prod_list[reset($prods_in_group)]);
				for ($i = 0; $i < $first_item_count; $i++)
				{
					$prod_str = array();
					$date = "";
					$date_booking_id = null;
					$prod2room = array();
					$prod2tm = array();
					foreach($prods_in_group as $prod_id)
					{
						if (!$this->can("view", $prod_id))
						{
							continue;	
						}
						$prod = obj($prod_id);
						foreach($dates as $_prod_id => $nums)
						{
							if ($_prod_id == $prod_id && isset($nums[$i]) && $nums[$i]["from"] > 1)
							{
								$sets = $nums[$i];
								$room = obj($sets["room"]);
								$prod2room[$_prod_id] = $room->id();
								$prod2tm[$_prod_id] = $sets["from"];
								$date .= sprintf(t("Ruum %s, ajal %s - %s"), $room->name(), date("d.m.Y H:i", $sets["from"]), date("d.m.Y H:i", $sets["to"]));
								$date_booking_id = $sets["reservation_id"];
							}
						}
					}

					foreach($prods_in_group as $prod_id)
					{
						if (!$this->can("view", $prod_id))
						{
							continue;	
						}
						$prod = obj($prod_id);
						if ($date == "")
						{
							$prod_str[] = html::popup(array(
								"url" => $this->mk_my_orb("select_room_booking", array("booking" => $o->id(), "prod" => $prod_id, "prod_num" => "".$i, "section" => "3169", "pkt" => $package->id())),
								"caption" => $prod->trans_get_val("name"),
								"height" => 500,
								"width" => 750,
								"scrollbars" => 1,
								"resizable" => 1
							));
						}
						else
						{
							$prod_str[] = $prod->trans_get_val("name");
						}
					}

					if ($date != "")
					{
						$ri = get_instance(CL_ROOM);
						$settings = $ri->get_settings_for_room(obj($prod2room[$prod_id]));
						if (true || $ri->group_can_do_bron($settings, $prod2tm[$prod_id]))
						{
							$date .= " ".html::href(array(
								"url" => $this->mk_my_orb("clear_booking", array("return_url" => get_ru(), "booking" => $date_booking_id)),
								"caption" => t("T&uuml;hista")
							));
						}
					}
					else
					{
						$has_unc = true;
					}
					
					if (substr($prod_group, 0, 5) == "__ei|")
					{
						$fd[] = array(
							"booking" => $booking_str,
							"name" => t("----Lisateenused"),
							"when" => ""
						);
					}
					
					if(!$lt_line_set && true && in_array(reset($prods_in_group), $extra_stuff))
					{//$prod_str[] = "<b>".t("Lisateenused:")."<b>";
						$ot->define_data(array(
							"booking" => "",
							"name" => '<table  width="100%" style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px dotted black; line-height: 25px;"><tr><td><b>'.t("Lisateenused:").'<b></td></tr></table>',
							"when" => "",
						));
						$lt_line_set = 1;
					}

					$fd[] = (array(
						"booking" => $booking_str,
						"name" => join("<br>", $prod_str),
						"when" => $date
					));
					
					$ot->define_data(array("name" => '<table  width="100%" style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px dotted black; line-height: 25px;"><tr><td>'.join("<br>", $prod_str).'</td></tr></table>',
						"when" => $date));
				}
			}
			if ($arr["request"]["s_date_not_set"] && !$has_unc)
			{
				continue;
			}

//			foreach($fd as $row)
//			{
//				$row["_int_index"] = ++$_int_index;
//				$t->define_data($row);
//			}


/*			if (!$_GET["notimes"] || $has_dates)
			{
				$tpl->vars(array(
					"BOOK_LINE" => $book_line,
					"disp_main" => $o->modified() > (time() - 300) ? "block" : "none",
					"disp_short" => $o->modified() > (time() - 300) ? "none" : "block"
				));
				$bookings .= $tpl->parse("BOOKING");
			}
*/
 			$t->define_data(array(
// 				"booking" => $booking_str,
 //				"when" => $when,
 				"name" => 
 					"
					<div  id='bka".$o->id()."' style='display: ".($o->modified() > (time() - 300) ? "none" : "block")."'>".
						$booking_str.
					"</div>

					<div  id='bk".$o->id()."' style='display: ".($o->modified() > (time() - 300) ? "block" : "none")."'>".
						'<table border="0" width="100%" style="border: 1px solid black;">
						<tr>
							<td width="100%" height="26" align="left" style="background-image:url(http://www.kalevspa.ee/img/taust_pealkiri.gif)" class="bronpealingid">
							'.$booking_str.'
							</td>
						</tr>
						<tr><td>'.$ot->draw().'</td></tr>
						</table>'
					."</div>",	
			));
		}
		$t->set_sortable(false);
		$t->set_rgroupby(array("booking" => "booking"));
//		$t->set_default_sortby(array("_int_index"));

		if (!is_admin() && $total_payment_amt > 0)
		{
			$payment_info .= "<br>".sprintf(t("Kokku: %s EEK"), $total_payment_amt)." ".html::href(array(
				"url" => $this->mk_my_orb("pay", array(
                                                        "id" => $ol->ids(),
                                                        "r" => get_ru(),
                                                        "bank_payment" => 13574,
                                                        "section" => aw_global_get("section"),
                                                ), "spa_customer_interface"),
				"caption" => t("Maksma")
			));
			$t->set_caption($payment_info);
		}
	}

	/**
		@attrib name=select_room_booking
		@param booking required type=int
		@param prod required type=int
		@param prod_num required type=int
		@param pkt optional type=int
		@param _not_verified optional
		@param rooms optional 
		@param retf optional 
	**/
	function select_room_booking($arr)
	{
		classload("core/date/date_calc");
		$html = "";

		// get date range from booking
		$b = obj($arr["booking"]);
		$from = $b->prop("start");
		$to = $b->prop("end");
		if ($from < 100 || $to < 100)
		{
			$from = get_day_start();
			$to = get_day_start() + 24*3600*7 - 1;//see -1 sellep2rast et kui kella 00st v6tab, siis on juba uus p2ev ja v6tab j2rgmise p2eva ka sisse
		}
		$range_from = $from;
		$range_to = $to;
		// split into weeks, and if more than 1, let the user select range
		$rs = get_week_start($from) + 24*7*3600;
		// now, draw table for the active range
		classload("vcl/table");
		$t = new aw_table();//if(aw_global_get("uid") == "st88rl2206"){ arr(date("H:i d.m.Y" , $range_to)); arr(date("H:i d.m.Y" , $range_from));}
		$num_days = floor(($range_to - $range_from) / (24*3600)+1);
		for ($i = 0; $i < $num_days; $i++)
		{
			$s = $range_from + ($i * 24 * 3600);
			if ($s < $from || $s > $to)
			{
				continue;
			}
			$t->define_field(array(
				"name" => "aa".$i,
				"caption" => date("d.m.Y", $range_from+($i*24*3600)),
				"align" => "center"
			));
		}

		$p_rooms = $this->get_rooms_for_product($arr["prod"]);
		if (is_array($arr["rooms"]) && count($arr["rooms"]))
		{
			$arr["rooms"] = $this->make_keys($arr["rooms"]);
			foreach($p_rooms as $_id => $d)
			{
				if (!isset($arr["rooms"][$_id]))
				{
					unset($p_rooms[$_id]);
				}
			}
		}

		if (count($p_rooms) == 0)
		{
			die(t("Seda toodet ei ole v&otilde;imalik broneerida &uuml;htegi ruumi!"));
		}

		$room_inst = get_instance(CL_ROOM);
		$room2inst = array();
		$room2settings = array();
		foreach($p_rooms as $room_id => $room_obj)
		{
			$room2inst[$room_id] = clone $room_inst;
			$room2inst[$room_id]->generate_res_table($room_obj, $range_from, $range_to);
			$room2inst[$room_id]->pauses = $room2inst[$room_id]->get_current_pauses_for_room($room_obj);
			$room2settings[$room_id] = $room_inst->get_settings_for_room($room_obj);
		}

		$reserved_days = $this->get_reserved_days_for_pkt($arr["pkt"], $range_from, $range_to, $arr["booking"], $current_booking);

		// get the current booking for this prod so we can ignore it in the taken checks
		$book_dates = $this->get_booking_data_from_booking($b);
		$current_booking = null;
		if (isset($book_dates[$arr["prod"]][$arr["prod_num"]]))
		{
			$current_booking = $book_dates[$arr["prod"]][$arr["prod_num"]]["reservation_id"];
		}

		// get reservation length from product
		$prod_obj = obj($arr["prod"]);
		$prod_inst = get_instance(CL_SHOP_PRODUCT);
		$time_step = $reservation_length = $prod_inst->get_reservation_length($prod_obj);
		// check if buffers are shorter than the min rvs len
		// then time steps are buffer length, but rvs len is rvs len still
		$tmp = $prod_inst->get_pre_buffer($prod_obj);
		$tmp2 = $prod_inst->get_post_buffer($prod_obj);		
		if ($tmp > 0 && $tmp2 > 0 )
		{
			$tmp = min($tmp, $tmp2);
			if ($tmp < $time_step)
			{
				$time_step = $tmp;
			}
		}
		else
		if ($tmp > 0)
		{
			$time_step = min($tmp, $time_step);
		}
		else
		if ($tmp2 > 0)
		{
			$time_step = min($tmp2, $time_step);
		}

		// what we actually need to do, is to get the time steps from all the rooms and get the smallest one
		$time_step = 24*3600;
		foreach($p_rooms as $room_id => $room_obj)
                {
			$tmp = $room_obj->prop("time_step") * ($room_obj->prop("time_unit") == 1 ? 60 : ($room_obj->prop("time_unit") == 2 ? 3600 : 3600*24));
			$time_step = min($time_step, $tmp);
                }


		//$time_step = 15*60;
		if ($time_step == 0)
		{
			die(sprintf(t("Tootele %s pole m&auml;&auml;ratud broneeringu pikkust!"), html::obj_change_url($prod_obj)));
		}
		$num_steps = (24*3600) / $time_step;

		$p = get_current_person();
		$settings_inst = get_instance(CL_ROOM_SETTINGS);
		$data = array();
		$oh_i = get_instance(CL_OPENHOURS);
		$room2oh = array();
		foreach($p_rooms as $room)
		{
			$oharr = $room_inst->get_current_openhours_for_room($room);
			$oh = null;
			foreach($oharr as $oh)
			{
				if ($oh->prop("date_from") > 100 && $to < $oh->prop("date_from"))
				{
					$oh = null;
					continue;
				}
				if ($oh->prop("date_to") > 100 && $from > $oh->prop("date_to"))
				{
					$oh = null;
					continue;
				}
			}
			if (is_object($oh))
			{
				$room2oh[$room->id()] = $oh;
			}
		}
		
		$o_range_from = $range_from;
		for ($h = 0; $h < $num_steps; $h++)
		{
			$d = array();
			for ($i = 0; $i < $num_days; $i++)
			{
				$s = $range_from + ($i * 24 * 3600);
				
				if ($s < $from || $s > $to)
				{
					continue;
				}
				
				$d_from = 24*3600;
				$d_to = 0;
				$for_people = 0;
				$allow_multiple = 0;
				$tmd = $h*$time_step;
				$tmd2 = min(3600*24, $tmd + $reservation_length);
				$cur_step_start = $range_from+($i*24*3600)+$h*$time_step;
				$cur_step_end = $cur_step_start + $reservation_length;
				$avail = false;
				foreach($p_rooms as $room)
				{
					if($room->prop("allow_multiple")) // idee selles, et kui yhes ruumis on lubatud mitmele, siis loeb k6igil kokku palju vaba ruumi on... kui m6nel ruumil pole m22ratud, siis see annab juurde v22rtuse 1
					{
						$allow_multiple = 1;
					}
					$room2inst[$room->id()]->check_for_people = 1;
					if ($room2oh[$room->id()])
					{
						list($d_start, $d_end) = $oh_i->get_times_for_date($room2oh[$room->id()], $range_from+($i*24*3600)+$h*$time_step);
						if (date("I", $range_from+(($i*24*3600)+$h*$time_step)) == 1 && date("I", $range_from+((($i)*24*3600))) == 1 && date("I", $range_from) == 0)
						{
							$d_end += 3600;
						}
						if (date("I", $range_from+$i*24*3600) == 0 && date("I", $range_from) == 1)
						{
							$d_end -= $time_step;
						}
						$d_from = min($d_from, $d_start);
						$d_to = max($d_to, $d_end);
					}
					if (($cp = $room2inst[$room->id()]->check_if_available(array("room" => $room->id(), "start" => $cur_step_start, "end" => ($cur_step_end+$tmp2)))) && !$room2inst[$room->id()]->is_buffer)
					{
						if ($room2inst[$room->id()]->group_can_do_bron($room2settings[$room->id()], $cur_step_start))
						{
							if (!$room2inst[$room->id()]->is_paused($cur_step_start, $cur_step_end))
							{
								$avail = true;
								$for_people+= $cp;
							}
						}
					}
				}

				foreach($book_dates as $_book_prod => $_prod_nums)
				{
					foreach($_prod_nums as $_book_time)
					{
						if ($_book_time["from"] > 1 && timespans_overlap($cur_step_start, $cur_step_end, $_book_time["from"]-30*60, $_book_time["to"]+30*60))
						{
							$avail = false;
						}
					}
				}

				$date_str = date("d.m.Y", $range_from+($i*24*3600));
				if ($reserved_days[$date_str])
				{
					$avail = false;
				}
				$tmp_to = $cur_step_end - get_day_start($cur_step_end);
				if ($h*$time_step < $d_from || $h*$time_step + $tmp2 >= $d_to || ($tmp_to + $tmp2) > $d_to)
				{
					continue;
				}
				$url = $this->mk_my_orb("make_reservation",array(
					"start" => $cur_step_start,
					"end" => $cur_step_end,
					"prod" => $arr["prod"],
					"prod_num" => $arr["prod_num"],
					"booking" => $arr["booking"],
					"_not_verified" => (int)$arr["_not_verified"],
					"retf" => $arr["retf"]
				), get_class($this), false, false, "&amp;");
				if (!$avail)
				{
					$d["aa".$i] = t("Broneeritud");
				}
				else
				{
					$tmd_h = floor($tmd / 3600);
					$tmd2_h = floor($tmd2 / 3600);
					$d["aa".$i] = html::href(array(
						"url" => $url,
						"caption" => sprintf("%02d:%02d-%02d:%02d", $tmd_h, floor(($tmd - $tmd_h*3600) / 60), $tmd2_h, floor(($tmd2 - $tmd2_h*3600) / 60))
					));
					if($allow_multiple)
					{
						$d["aa".$i].=" (".$for_people.")";
					}
				}
			}
			if (count($d))
			{
				$data["".($tmd/3600).""] = $d;
				//$t->define_data($d);
			}
		}
		
		foreach($p_rooms as $room)
		{
			$oh = $room_inst->get_current_openhours_for_room($room);

			if (is_object($oh))
			{
				list($d_start, $d_end) = $oh_i->get_times_for_date($oh, $range_from+($i*24*3600)+$h*3600);
				$d_from = min($d_from, $d_start);
				$d_to = max($d_to, $d_end);
				//arr(date("G:i" , $room_midday));
			
				if(!$room_midday)
				{
					$room_midday = $oh_i->get_midday($oh,$range_from+($i*24*3600)+$h*3600);
				}
				if(!$settings)
				{
					$settings = $settings_inst->get_current_settings($room->id());
				}
				
			}
		}


		if(is_object($settings))
		{
			$available_for_user = $settings->prop("max_times_per_day");
		}
		if($available_for_user)
		{
			//sellesse lisab ainult vajaliku arvu vabu aegu
			$data2 = array();
			$available_before = (int)($available_for_user/2);
			$available_after = $available_for_user-$available_before;
			$midday_h = date("G" ,$room_midday);
			$booked_in_day = array(0,0,0,0,0,0,0);
			//otsib pooled vabad ajad peale keskp2eva
			foreach($data as $key => $dat)
			{
				foreach($dat as $day => $val)
				{
					if($key >= $midday_h && $booked_in_day[(int)$day[2]] < $available_after)
					{
						if(substr_count($val,"href"))
						{
							$booked_in_day[(int)$day[2]]++;
						}
						$data2[$key][$day] = $val;
					}
				}
			}
			//otsib ylej22nud vabad ajad enne keskp2eva
			krsort($data);
			foreach($data as $key => $dat)
			{
				foreach($dat as $day => $val)
				{
					if($key < $midday_h && $booked_in_day[(int)$day[2]] < $available_for_user)
					{
						if(substr_count($val,"href"))
						{
							$booked_in_day[(int)$day[2]]++;
						}
						$data2[$key][$day] = $val;
					}
				}
			}
			//juhul kui vabu aegu ei saand enne keskp2eva t2is, siis vaatab igaks juhuks , 2kki on peale l6unat veel vabu aegu
			ksort($data);
			foreach($data as $key => $dat)
			{
				foreach($dat as $day => $val)
				{
					if($key > $midday_h && $booked_in_day[(int)$day[2]] < $available_for_user)
					{
						if(substr_count($val,"href"))
						{
							$booked_in_day[(int)$day[2]]++;
						}
						$data2[$key][$day] = $val;
					}
				}
			}
			ksort($data2);
			$data = $data2;
			//arr($data2);
		}
		foreach($data as $d)
		{
			// skip all rows that either have all days empty or all days booked
			$has_free = false;
			foreach($d as $v)
			{
				if (strpos($v, "href") !== false)
				{
					$has_free = true;
				}
			}

			if ($has_free)
			{
		 		$t->define_data($d);
			}
		}
		
		$html .= $t->draw();
		return $html;
	}

	/**
		@attrib name=make_reservation
		@param start required type=int
		@param end required type=int
		@param prod required type=int
		@param prod_num required type=int
		@param booking required type=int
		@param _not_verified optional type=int
		@param retf optional 
	**/
	function make_reservation($arr)
	{
		$arr["prod_num"] = (int)$arr["prod_num"];
		$p_rooms = $this->get_rooms_for_product($arr["prod"]);
		if (count($p_rooms) == 0)
		{
			die(t("Seda toodet ei ole v&otilde;imalik broneerida &uuml;htegi ruumi!"));
		}

		$bron = obj($arr["booking"]);

		// if there is a previous booking for the same package for the same product, then we need to remove that one first
		$cur_bookings = $this->get_booking_data_from_booking($bron);
		$current_booking = null;
		if (isset($cur_bookings[$arr["prod"]][$arr["prod_num"]]) && $this->can("view", $cur_bookings[$arr["prod"]][$arr["prod_num"]]["reservation_id"]))
		{
			$current_booking = $cur_bookings[$arr["prod"]][$arr["prod_num"]]["reservation_id"];
		}

		if (!$this->can("view", $bron->prop("package")))
		{
			$package = obj();
			$package->set_class_id(CL_SHOP_PACKET);
			$def_pkgs = array($arr["prod"] => $arr["prod"]);
		}
		else
		{
			$package = obj($bron->prop("package"));
			$p_i = $package->instance();
			$def_pkgs = $p_i->get_default_packagings_in_packet($package);
		}
		
		if ($arr["prod"])
		{
			$_prod = obj($arr["prod"]);
			if ($_prod->class_id() != CL_SHOP_PRODUCT)
			{
				$def_pkgs = array($arr["prod"] => $arr["prod"]);
			}
		}
		$p_i = $package->instance();
		// go over all rooms and the first one that is available, we book
		foreach($p_rooms as $room)
		{
			$room_inst = $room->instance();
			$room_inst->pauses = $room_inst->get_current_pauses_for_room($room);
			if($room_inst->is_paused($arr["start"], $arr["end"])) continue;
			if ($room_inst->check_if_available(array("room" => $room->id(), "start" => $arr["start"], "end" => $arr["end"])))
			{
				$rv_id = $room_inst->make_reservation(array(
					"id" => $room->id(),
					"res_id" => $current_booking,
					"data" => array(
						"start" => $arr["start"],
						"end" => $arr["end"],
						"customer" => $bron->prop("person"),
						"verified" => 1,
						"products" => array($def_pkgs[$arr["prod"]] => 1)
					),
					"meta" => array(
						"product_for_bron" => $arr["prod"],
						"product_count_for_bron" => $arr["prod_num"]
					),
					"_not_verified" => $arr["_not_verified"]
				));
				$rvo = obj($rv_id);
				$bron->connect(array(
					"to" => $rv_id,
					"type" => "RELTYPE_ROOM_BRON"
				));//if(aw_global_get("uid") == "struktuur")arr($bron);
				$bron->save();
				if ($arr["retf"] != "")
				{
					die("<script language=javascript>
						window.opener.location.href='".$arr["retf"]."';
						window.close();
					</script>");
				}
				return aw_ini_get("baseurl")."/automatweb/closewin.html";

			}
		}
		die(t("Vahepeal on valitud aeg broneeritud!"));
	}

	/**
		@attrib name=proforma
		@param id required
		@param wb required
	**/
	function proforma($arr)
	{
		$b = obj($arr["id"]);
		$ol = new object_list(array("class_id" => CL_SPA_BOOKING , "parent" => $arr["id"]));
		$sum = 0;
		$wb = obj($arr["wb"]);
		$this->read_site_template("booking_proforma.tpl");
		lc_site_load("spa_bookigs_entry", &$this);

		list($y, $m, $d) = explode("-", $b->prop("person.birthday"));

		$us = get_instance(CL_USER);
		$this->users_person = $us->get_person_for_uid($b->createdby());

		$this->vars(array(
			"bureau" => $this->users_person->name(),//$b->createdby(),
			"person" => $b->trans_get_val_str("person"),
			"package" => $b->trans_get_val_str("package"),
			"from" => date("d.m.Y", $b->prop("start")),
			"to" => date("d.m.Y", $b->prop("end")),
			"person_comment" => $b->prop("person.comment"),
			"person_name" => $b->prop("person.name"),
			"person_birthday" => $y > 0 ? sprintf("%02d.%02d.%04d", $d, $m, $y) : "",
			"person_ext_id" => $b->prop("person.ext_id_alphanumeric"),
			"person_gender" => $b->prop("person.gender") == 1 ? t("Mees") : ($b->prop("person.gender") === "2" ? t("Naine") : ""),
			"proforma_id" => $b->id(),
		));

		// now, list all bookings for rooms 
		$items = array();
//		foreach ($ol->arr() as $o)
//		{
			$dates = $this->get_booking_data_from_booking($b);
			$books = "";
			
			foreach($dates as $prod => $entries)
			{
				foreach($entries as $entry)
				{
					$items[] = $entry;
				}
			}
//		}

		$all_items = "";
		$packet_services = "";
		$additional_services = "";
		usort($items, create_function('$a,$b', 'return $a["from"] - $b["from"];'));
		$sum = array();
		foreach($items as $entry)
		{
			if (!$entry["is_extra"] || !($entry["from"] > 100))
			{
				continue;
			}
			$ro = obj($entry["room"]);
			$rvs = obj($entry["reservation_id"]);
			$prod_obj = obj($rvs->meta("product_for_bron"));
			$this->vars(array(
				"r_from" => date("d.m.Y H:i", $entry["from"]),
				"r_to" =>  date("d.m.Y H:i", $entry["to"]),
				"r_room" => $ro->trans_get_val("name"),
				"r_prod" => $prod_obj->trans_get_val("name"),
				"start_time" => $entry["from"],
				"end_time" => $entry["to"],
				"price" => join (" / " , $prod_obj->meta("cur_prices")),//$prod_obj->prop("price")
			));
			foreach($prod_obj->meta("cur_prices") as $cur => $price)
			{
				$sum[$cur] = $sum[$cur] + $price;
				$this->vars(array("sum_".$cur => $price));
			}
			
			
			$books .= $this->parse("BOOKING");

			$all_items .= $this->parse("ALL_ITEMS");
			if ($entry["is_extra"] == 1)
			{
				$additional_services .= $this->parse("ADDITIONAL_SERVICES");
			}
			else
			{
				$packet_services .= $this->parse("PACKET_SERVICES");
			}//if(aw_global_get("uid") == "struktuur")arr($prod_obj->meta());
			//$sum = $sum + $prod_obj->prop("price");
		}

		$currencys = array();
		foreach($sum as $key => $val)
		{
			if(is_oid($key) && $this->can("view" , $key))
			{
				$c_o = obj($key);
				$currencys[] = $c_o->name();
			}
			$this->vars(array("curr_".$key => $val));
		}

		$this->vars(array(
			"BOOKING" => $books,
			"ADDITIONAL_SERVICES" => $additional_services,
			"PACKET_SERVICES" => $packet_services,
			"ALL_ITEMS" => $all_items,
			"SUM" => join (" / " , $sum),
			"currencys" => join (" / " ,$currencys),
		));
		$this->vars(array(
			"HAS_PACKET_SERVICES" => $packet_services != "" ? $this->parse("HAS_PACKET_SERVICES") : "",
			"HAS_ADDITIONAL_SERVICES" => $packet_services != "" ? $this->parse("HAS_ADDITIONAL_SERVICES") : "",
		));

		if ($this->can("view", $wb->prop("print_view_ctr")))
		{
			$fc = get_instance(CL_FORM_CONTROLLER);
			$fc->eval_controller($wb->prop("print_view_ctr"), $arr);
		}
		die($this->parse());
	}

	/**
		@attrib name=print_booking
		@param id required
		@param wb required
	**/
	function print_booking($arr)
	{
		$b = obj($arr["id"]);
		$wb = obj($arr["wb"]);
		$this->read_site_template("booking.tpl");
		lc_site_load("spa_bookigs_entry", &$this);

		list($y, $m, $d) = explode("-", $b->prop("person.birthday"));

		$us = get_instance(CL_USER);
		$this->users_person = $us->get_person_for_uid($b->createdby());

		$this->vars(array(
			"bureau" => $this->users_person->name(),//$b->createdby(),
			"person" => $b->trans_get_val_str("person"),
			"package" => $b->trans_get_val_str("package"),
			"from" => date("d.m.Y", $b->prop("start")),
			"to" => date("d.m.Y", $b->prop("end")),
			"person_comment" => $b->prop("person.comment"),
			"person_name" => $b->prop("person.name"),
			"person_birthday" => $y > 0 ? sprintf("%02d.%02d.%04d", $d, $m, $y) : "",
			"person_ext_id" => $b->prop("person.ext_id_alphanumeric"),
			"person_gender" => $b->prop("person.gender") == 1 ? t("Mees") : ($b->prop("person.gender") === "2" ? t("Naine") : "")
		));

		// now, list all bookings for rooms 
		$dates = $this->get_booking_data_from_booking($b);
		$books = "";
		$items = array();
		foreach($dates as $prod => $entries)
		{
			foreach($entries as $entry)
			{
				$items[] = $entry;
			}
		}

		$all_items = "";
		$packet_services = "";
		$additional_services = "";

		usort($items, create_function('$a,$b', 'return $a["from"] - $b["from"];'));
		foreach($items as $entry)
		{
			if ($entry["from"] < 1)
			{
				continue;
			}
			$ro = obj($entry["room"]);
			$rvs = obj($entry["reservation_id"]);
			$prod_obj = obj($rvs->meta("product_for_bron"));
			$this->vars(array(
				"r_from" => date("d.m.Y H:i", $entry["from"]),
				"r_to" =>  date("d.m.Y H:i", $entry["to"]),
				"r_room" => $ro->trans_get_val("name"),
				"r_prod" => $prod_obj->trans_get_val("name"),
				"start_time" => $entry["from"],
				"end_time" => $entry["to"],
				"price" => $prod_obj->prop("price")
			));
			$books .= $this->parse("BOOKING");

			$all_items .= $this->parse("ALL_ITEMS");
			if ($entry["is_extra"] == 1)
			{
				$additional_services .= $this->parse("ADDITIONAL_SERVICES");
			}
			else
			{
				$packet_services .= $this->parse("PACKET_SERVICES");
			}
		}


		$this->vars(array(
			"BOOKING" => $books,
			"ADDITIONAL_SERVICES" => $additional_services,
			"PACKET_SERVICES" => $packet_services,
			"ALL_ITEMS" => $all_items
		));
		$this->vars(array(
			"HAS_PACKET_SERVICES" => $packet_services != "" ? $this->parse("HAS_PACKET_SERVICES") : "",
			"HAS_ADDITIONAL_SERVICES" => $packet_services != "" ? $this->parse("HAS_ADDITIONAL_SERVICES") : "",
		));

		if ($this->can("view", $wb->prop("print_view_ctr")))
		{
			$fc = get_instance(CL_FORM_CONTROLLER);
			$fc->eval_controller($wb->prop("print_view_ctr"), $arr);
		}
		die($this->parse());
	}

	function _get_b_mail_legend($arr)
	{
		$arr["prop"]["value"] = t("Meili sisus kasutatavad muutujad:<br>#uid# - kasutajanimi<br>#pwd# - parool<br>#pwd_hash_link# - parooli vahetamise link<br> #login_url# - sisse logimise aadress<br>");
	}

	function _get_from_addr($o)
	{
		if ($o->prop("b_mail_from_name") != "")
		{
			return $o->prop("b_mail_from_name")." <".$o->prop("b_mail_from_addr").">";
		}
		return $o->prop("b_mail_from_addr");
	}

	function get_booking_data_from_booking($o)
	{
		$dates = array();
		foreach($o->connections_from(array("type" => "RELTYPE_ROOM_BRON")) as $conn)
		{
			$room_bron = $conn->to();
			if ($room_bron->meta("product_for_bron"))
			{
				$dates[$room_bron->meta("product_for_bron")][$room_bron->meta("product_count_for_bron")] = array(
					"room" => $room_bron->prop("resource"),
					"from" => $room_bron->prop("start1"),
					"to" => $room_bron->prop("end"),
					"reservation_id" => $room_bron->id()
				);
			}
		}

		$extra_prods = safe_array($o->meta("extra_prods"));
		foreach($dates as $prod => $items)
		{
			$is_extra = false;
			foreach($extra_prods as $exp)
			{
				if ($exp["prod"] == $prod)
				{
					$is_extra = true;
				}
			}

			if ($is_extra)
			{
				foreach($items as $count => $data)
				{
					$dates[$prod][$count]["is_extra"] = true;
				}
			}
		}
		return $dates;
	}

	function get_rooms_for_product($prod)
	{
		static $cache;
		if (isset($cache[$prod]))
		{
			return $cache[$prod];
		}
		$po = obj($prod);
		if ($po->class_id() == CL_SHOP_PRODUCT_PACKAGING)
		{
			$prod_con = reset($po->connections_to(array("from.class_id" => CL_SHOP_PRODUCT)));
		}
		// list all rooms and find the ones for this product
		$p_rooms = array();
		// cache room list in var
		if (!empty($this->pd_data_list_cache))
		{
			$pd_data_list = $this->pd_data_list_cache;
		}
		else
		{
			$rooms = new object_list(array(
				"class_id" => CL_ROOM,
				"lang_id" => array(),
				"site_id" => array(),
				"sort_by" => "objects.jrk"
			));
			$ri = get_instance(CL_ROOM);
			$pd_data_list = array();
			foreach($rooms->arr() as $room)
			{
				//$pd = $ri->get_active_items($room);
				$pd = $ri->get_prod_data_for_room($room);
				$pd_data_list[$room->id()] = $pd;
			}

			$this->pd_data_list_cache = $pd_data_list;
		}

		foreach($pd_data_list as $room_id => $pd_data)
		{
			if($pd_data[$prod]["active"])
			{
				$p_rooms[$room_id] = obj($room_id);
			}
		}
		$cache[$prod] = $p_rooms;
		return $p_rooms;
	}

	function get_reserved_days_for_pkt($pkt, $range_from, $range_to, $booking, $current_booking = null)
	{
		$bo = obj($booking);
		$conn_ol = new object_list($bo->connections_from(array("type" => "RELTYPE_ROOM_BRON")));
		$rv_ids = $this->make_keys($conn_ol->ids());
		if (!count($rv_ids))
		{
			return array();
		}
		
		$pkt = obj($pkt);
		$reserved_days = array();
		if ($pkt->prop("max_usage_in_time") > 0)
		{
			// get reservations in the selected timespan. 
			// if on some days the count is over the edge
			// block that day
			$filt = array(
				"class_id" => CL_RESERVATION,
				"oid" => $rv_ids,
				"lang_id" => array(),
				"site_id" => array(),
				"start1" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ,$range_from),
				"end" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ,$range_to),
			);
			if ($current_booking)
			{
				unset($filt["oid"][$current_booking]);
			}
			$rvs = new object_list($filt);
			$count_by_day = array();
			foreach($rvs->arr() as $rv)
			{
				$count_by_day[date("d.m.Y", $rv->prop("start1"))]++;
			}

			foreach($count_by_day as $date => $count)
			{
				if ($count >= $pkt->prop("max_usage_in_time"))
				{
					$reserved_days[$date] = 1;
				}
			}
		}
		return $reserved_days;
	}

	/**
		@attrib name=clear_booking
		@param return_url required
		@param booking required type=int 
	**/
	function clear_booking($arr)
	{
		$b = obj($arr["booking"]);
		$b->set_prop("start1", -1);
		$b->set_prop("end", -1);
		$b->save();
		return $arr["return_url"];
	}

	/**
		@attrib name=delete_booking
		@param return_url required
		@param booking required type=int 
		@param spa_bron required type=int 
	**/
	function delete_booking($arr)
	{
		if ($this->can("delete", $arr["booking"]))
		{
			$b = obj($arr["booking"]);
			$b->delete();
		}
		$sb = obj($arr["spa_bron"]);
		$ep = $sb->meta("extra_prods");
		foreach($ep as $ei_key => $ei_entry)
		{
			if ($ei_entry["reservation"] == $arr["booking"])
			{
				unset($ep[$ei_key]);
			}
		}
		$sb->set_meta("extra_prods", $ep);
		$sb->save();
		if ($sb->is_connected_to(array("to" => $arr["booking"])))
		{
			$sb->disconnect(array(
				"from" => $arr["booking"]
			));
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=add_prod_to_bron
		@param bron required type=int acl=edit
		@param wb required type=int acl=edit
	**/
	function add_prod_to_bron($arr)
	{
		$this->read_template("treetable.tpl");

		$this->vars(array(
			"tree" => $this->_get_prod_fld_tree($arr),
			"list" => $this->_get_prod_list_tbl($arr)
		));
		return $this->parse();
	}

	function _get_prod_fld_tree($arr)
	{
		$o = obj($arr["wb"]);
		$wh = obj($o->prop("warehouse"));
		$wh_i = $wh->instance();
		$p = array(
			"obj_inst" => $wh
		);
		$wh_i->_init_view($p);
		return $wh_i->_prod_list_tree($p);
	}

	function _get_prod_list_tbl($arr)
	{
		classload("vcl/table");
		$t = new aw_table();

		if (!$_GET["tree_filter"])
		{
			$ot = new object_list();
		}
		else
		{
			$ot = new object_list(array(
				"parent" => $_GET["tree_filter"],
				"class_id" => array(CL_MENU,CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING),
				"status" => array(STAT_ACTIVE, STAT_NOTACTIVE)
			));
		}

		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Vali toode"),
			"align" => "center"
		));
		foreach($ot->arr() as $o)
		{
			$arr["prod"] = $o->id();
			$t->define_data(array(
				"prod" => html::href(array(
					"caption" => parse_obj_name($o->name()),
					"url" => $this->mk_my_orb("fin_add_prod_to_bron", $arr)
				)),
				"ord" => $o->ord()
			));
		}
		$t->set_default_sortby("ord");
		$t->sort_by();
		return $t->draw();
	}

	/**
		@attrib name=fin_add_prod_to_bron
		@param bron required type=int acl=edit
		@param wb required type=int acl=edit
		@param prod required type=int acl=view
	**/
	function fin_add_prod_to_bron($arr)
	{
		$bron = obj($arr["bron"]);
		
		$bron->connect(array(
			"to" => $arr["prod"],
			"type" => "RELTYPE_EXTRA_PROD"
		));

		// akso make a new room reservation object for the extra thingie
		$rooms = $this->get_rooms_for_product($arr["prod"]);
		if (count($rooms))
		{
			$room_inst = get_instance(CL_ROOM);
			$p = array(
				"id" => reset(array_keys($rooms)),
				"data" => array(
					"customer" => $bron->prop("person"),
					"products" => array($arr["prod"] => 1)
				),
				"meta" => array(
					"product_for_bron" => $arr["prod"],
					"product_count_for_bron" => 0
				)
			);
			if ($arr["_not_verified"])
			{
				$p["_not_verified"] = 1;
			}
			$rv_id = $room_inst->make_reservation($p);
			$bron->connect(array(
				"to" => $rv_id,
				"type" => "RELTYPE_ROOM_BRON"
			));

			$extra_prods = safe_array($bron->meta("extra_prods"));
			$extra_prods[] = array(
				"prod" => $arr["prod"],
				"reservation" => $rv_id
			);
			$bron->set_meta("extra_prods", $extra_prods);
			$bron->save();
		}
		return aw_ini_get("baseurl")."/automatweb/closewin.html";
	}

	/**
		@attrib name=enter_cust_data_pop
		@param bron required type=int acl=edit
		@param props optional
	**/
	function enter_cust_data_pop($arr)
	{
		classload("cfg/htmlclient");
		$htmlc = new htmlclient(array(
			'template' => "default",
		));
		$htmlc->start_output();
		$htmlc->add_property(array(
			"caption" => t("Sisesta kasutaja puuduvad andmed"),
		));

		$tmp = obj();
		$tmp->set_class_id(CL_CRM_PERSON);
		$propl = $tmp->get_property_list();

		// get system default cfgform for person
		$si = get_instance(CL_CFGFORM);
		$sysd = $si->get_sysdefault(array("clid" => CL_CRM_PERSON));
		if ($sysd)
		{
			$propl = $si->get_props_from_cfgform(array("id" => $sysd));
		}
	
		$bron = obj($arr["bron"]);
		foreach(safe_array($arr["props"]) as $propertyn)
		{
			$capt = $propl[$propertyn]["caption"];
			switch($propertyn)
			{
				case "phone":
					$capt = t("Telefon");
					break;
			}
			if ($bron->class_id() == CL_CRM_PERSON)
			{
				$val = $bron->prop($propertyn.".name");
				if ($val == "")
				{
					$val = $bron->prop($propertyn);
				}
			}
			else
			{
				$val = $bron->prop("person.".$propertyn.".name");
				if ($val == "")
				{
					$val = $bron->prop("person.".$propertyn);
				}
			}

			$year_from = 1900;
			$year_to = date("Y");
			$type = "textbox";
			$opts = null;
			switch($propl[$propertyn]["type"])
			{
				case "date_select":
					$type="date_select";
					$propl[$propertyn]["year_to"] = date("Y")+1;
					break;

				case "chooser":
					$type="chooser";
					$i = get_instance(CL_CRM_PERSON);
					$p = array(
						"obj_inst" => obj($bron->prop("person")),
						"prop" => &$propl[$propertyn]
					);
					$i->get_property($p);
					$opts = $p["prop"]["options"];
					break;
			}

			switch($propertyn)
			{
				case "pk_name":
					$type = "select";
					$capt = t("Paketi nimi");
					$val = "";
					$ol = new object_list(array(
						"class_id" => CL_SHOP_PACKET,
						"lang_id" => array(),
						"site_id" => array()
					));
					$pk_list = array("" => t("--vali--"));
					foreach($ol->arr() as $o)
					{
						$pk_list[$o->id()] = $o->trans_get_val("name");
					}
					$opts = $pk_list;
					break;

				case "pk_arrival":
					$capt = t("Saabumine");

				case "pk_leave":
					$type="date_select";
					$capt = $capt != "" ? $capt : t("Lahkumine");
					$val = -1;
					$year_from = date("Y");
					$year_to = date("Y")+3;
					break;

				case "pk_tb_name":
					foreach($_GET["rvs"] as $rvid)
					{
						if($this->can("view" , $rvid))
						{
							$rvo = obj($rvid);
							$c = new connection();
							$conns = $c->find(array(
								"from.class_id" => CL_SPA_BOOKING,
								"to" => $rvo->id()
							));
							if (count($conns))
							{
								$con = reset($conns);
								$rvo = obj($con["from"]);
								$val = $rvo->prop("seller");
							}
						}
					}
					$type = "select";
					$capt = t("Reisib&uuml;roo nimi");
					$opts = array("" => t("--vali--"));
					$o = obj($_GET["center"]);
					foreach(safe_array($o->prop("groups")) as $g_oid)
					{
						$gi = get_instance(CL_GROUP);
						$co_list = array(); 
						foreach($gi->get_group_members(obj($g_oid)) as $user)
						{
							foreach($user->connections_from(array("type" => "RELTYPE_PERSON")) as $c)
							{
								$person = $c->to();
								$co_list = $co_list + $person ->get_org_selection();
/*								$ci = new connection();
								$conns = $ci->find(array(
									"from.class_id" => CL_CRM_COMPANY,
									"type" => "RELTYPE_WORKERS",
									"to" => $c->prop("to")
								));
								foreach($conns as $con)
								{
									$opts[$con["from"]] = $con["from.name"];
								}*/
								foreach($co_list as $co => $name)
								{
									$opts[$co] = $name;
								}
							}
						}
					}
					break;
			}

			$htmlc->add_property(array(
				"name" => "ud[$propertyn]",
				"type" => $type,
				"caption" => $capt,
				"value" => $val,
				"options" => $opts,
				"year_from" => $year_from,
				"year_to" => date("Y")+1
			));
		}

		foreach(safe_array($_GET["rvs"]) as $rv_id)
		{
			if (!$this->can("view", $rv_id))
			{
				continue;
			}
			$rvo = obj($rv_id);
			$amt = $rvo->meta("amount");
			$prod = reset(array_keys($amt));
			$po = obj($prod);
			$htmlc->add_property(array(
                                "name" => "ud[rv_$rv_id]",
                                "type" => "textbox",
                                "caption" => sprintf(t("%s %s-%s"), 
					$po->name(), 
					date("d.m.Y H:i", $rvo->prop("start1")),
					date("H:i", $rvo->prop("end"))
				),
                                "value" => $rvo->comment()
                        ));

			//
			$htmlc->add_property(array(
                                "name" => "ud[remove][".$rv_id."]",
                                "type" => "checkbox",
                                "caption" => sprintf(t("%s %s"),
					t("Eemalda"),
					$po->name()
				),
                                "value" => 0
                        ));
		}

		$htmlc->add_property(array(
			"name" => "s[submit]",
			"type" => "submit",
			"value" => "Salvesta",
			"class" => "sbtbutton"
		));

		$htmlc->finish_output(array(
			"action" => "save_cust_data_pop",
			"method" => "POST",
			"data" => array(
				"id" => $arr["id"],
				"orb_class" => "spa_bookigs_entry",
				"reforb" => 0,
				"props" => $arr["props"],
				"bron" => $arr["bron"],
				"center" => $_GET["center"],
				"out_arr" => $_GET["out_arr"],
				"rvs" => $_GET["rvs"]
			)
		));

		return $htmlc->get_result();
	}

	/**
		@attrib name=save_cust_data_pop all_args=1
	**/
	function save_cust_data_pop($arr)
	{
		$arr = $_POST;
		$bron = obj($arr["bron"]);
		if ($bron->class_id() == CL_CRM_PERSON)
		{
			$cust = $bron;
		}
		else
		if (!$this->can("view", $bron->prop("person")))
		{
			$cust = obj();
			$cust->set_class_id(CL_CRM_PERSON);
			$cust->set_parent($bron->id());
			$cust->save();
			$bron->set_prop("person", $cust->id());
			$bron->save();
		}
		else
		{
			$cust = obj($bron->prop("person"));
		}

		$tmp = obj();
		$tmp->set_class_id(CL_CRM_PERSON);
		$propl = $tmp->get_property_list();

		foreach(safe_array($arr["props"]) as $pn)
		{
			if ($propl[$pn]["type"] == "date_select")
			{
				if ($arr["ud"][$pn]["year"] < 100)
				{
					$arr["ud"][$pn] = "";
				}
				else
				{
					$arr["ud"][$pn] = sprintf("%04d-%02d-%02d", $arr["ud"][$pn]["year"], $arr["ud"][$pn]["month"], $arr["ud"][$pn]["day"]);
				}
			}
			switch($pn)
			{
				case "name":
					$cust->set_name($arr["ud"][$pn]);
					break;

				case "phone":
					if ($this->can("view", $cust->prop("phone")))
					{
						$ph = obj($cust->prop("phone"));
					}
					else
					{
						$ph = obj();
						$ph->set_parent($cust->id());
						$ph->set_class_id(CL_CRM_PHONE);
					}
					$ph->set_name($arr["ud"][$pn]);
					$ph->save();
					if (!$this->can("view", $cust->prop("phone")))
					{
						$cust->connect(array(
							"to" => $ph->id(),
							"type" => "RELTYPE_PHONE"
						));
						$cust->set_prop("phone", $ph->id());
					}
					break;

				default:	
					if ($cust->is_property($pn))
					{
						$cust->set_prop($pn, $arr["ud"][$pn]);
					}
					break;
			}
		}
		$cust->save();

		foreach(safe_array($arr["rvs"]) as $rvs_id)
		{
			if ($this->can("view", $rvs_id))
			{
				$rvo = obj($rvs_id);
				$rvo->set_comment($arr["ud"]["rv_".$rvs_id]);
				$rvo->save();
			}
		}

		foreach(safe_array($arr["ud"]["remove"]) as $remove_id => $val)
		{
			if ($this->can("view", $remove_id))
			{
				$rvo = obj($remove_id);
	//			$rvo -> delete();
			}
		}
		$set = $arr["ud"]["pk_tb_name"] || $arr["ud"]["pk_name"] || date_edit::get_timestamp($arr["ud"]["pk_arrival"]) || date_edit::get_timestamp($arr["ud"]["pk_leave"]);

		// if package is set in the submit, then do create spa booking
		if ($set && is_array($arr["out_arr"]) && count($arr["out_arr"]))
		{
			$b = obj();
			$b->set_class_id(CL_SPA_BOOKING);
			$b->set_parent($arr["center"]);
			$b->set_prop("person", $cust->id());
			$b->set_prop("start", date_edit::get_timestamp($arr["ud"]["pk_arrival"]));
			$b->set_prop("end", date_edit::get_timestamp($arr["ud"]["pk_leave"]));
			$b->set_prop("package", $arr["ud"]["pk_name"]);
			$b->set_prop("seller", $arr["ud"]["pk_tb_name"]);
			$b->save();
			foreach($arr["out_arr"] as $r_id)
			{
				$b->connect(array(
					"to" => $r_id,
					"type" => "RELTYPE_ROOM_BRON"
				));
			}
		}
		else
		if ($set)
		{
			// then we should just change the package set methinks
			foreach(safe_array($arr["rvs"]) as $rv_id)
			{
				$rvo = obj($rv_id);
				$c = new connection();
				$conns = $c->find(array(
					"from.class_id" => CL_SPA_BOOKING,
					"to" => $rvo->id()
				));
				if (count($conns))
				{
					$con = reset($conns);
					$rvo = obj($con["from"]);
					$_from = $rvo->prop("start");
					$_to = $rvo->prop("end");
				}
				if ($arr["ud"]["pk_name"] && $rvo->prop("package") != $arr["ud"]["pk_name"])
				{
					$rvo->set_prop("package", $arr["ud"]["pk_name"]);
				}
				if ($arr["ud"]["pk_tb_name"] && $rvo->prop("seller") != $arr["ud"]["pk_tb_name"])
				{
					$rvo->set_prop("seller", $arr["ud"]["pk_tb_name"]);
				}

				if (($pka = date_edit::get_timestamp($arr["ud"]["pk_arrival"])) != -1 && $rvo->prop("start") != $pka)
				{
					$rvo->set_prop("start", $pka);
				}
				if (($pke = date_edit::get_timestamp($arr["ud"]["pk_leave"])) != -1 && $rvo->prop("end") != $pke)
				{
					$rvo->set_prop("end", $pke);
				}
				$rvo->save();
			}
		}
		return aw_ini_get("baseurl")."/automatweb/closewin.html";
	}

	function _add_ppl_entry($d, $orig_booking)
	{
		$orig_person = obj($orig_booking->prop("person"));
		foreach($d["ppl"] as $ppl_entry)
		{
			if ($ppl_entry["fn"] == "" && $ppl_entry["ln"] == "")
			{
				continue;
			}
			$p = obj();
			$p->set_class_id(CL_CRM_PERSON);
			$p->set_parent($orig_person->parent());
			$p->set_name(trim($ppl_entry["fn"])." ".trim($ppl_entry["ln"]));
			$p->set_prop("firstname", trim($ppl_entry["fn"]));
			$p->set_prop("lastname", trim($ppl_entry["ln"]));
			$p->set_prop("birthday", sprintf("%04d-%02d-%02d", $ppl_entry["end"]["year"], $ppl_entry["end"]["month"], $ppl_entry["end"]["day"]));
			$p->set_prop("gender", $ppl_entry["gender"]);
			$p->save();

			$booking = obj();
			$booking->set_parent($orig_booking->parent());
			$booking->set_name(
				sprintf("Broneering %s %s - %s", 
					$ppl_entry["fn"]." ".$ppl_entry["ln"], 
					date("d.m.Y", $orig_booking->prop("start")), 
					date("d.m.Y", $orig_booking->prop("end"))
				)
			);
			$booking->set_class_id(CL_SPA_BOOKING);
			$booking->set_prop("person", $p->id());
			$booking->set_prop("start", $orig_booking->prop("start"));
			$booking->set_prop("end", $orig_booking->prop("end"));
			$booking->set_prop("package", $d["package"]);
			$booking->save();
			// for this booking, create empty reservations for all products so we can search by them
			$booking_inst = $booking->instance();
			$booking_inst->check_reservation_conns($booking);

			$booking->connect(array(
				"to" => $orig_booking->prop("person"),
				"type" => "RELTYPE_MAIN_PERSON"
			));
		}
	}
	
	
/*	firstname required type=string
lastname required type=string
gender required type=string // M/W
birthday required type=int dd.mm.YYYY
email required type=string
start required type=int
start time - timestamp // unix
packet_id required type=int // Identificator for a package in Kalev SPA, for example: 1394 = Health packet 8 days (Su-Su)
agency_id required type=int // Identificator for agency, not same as username
send_email optional type=boolean // if set, sends username and password with e-mail to final customer
send_email_agency optional type=boolean // if set, sends username and password with e-mail to agency
pass - password, string

returns:
error type=string // if there are errors
reservation_id type=int // reservation object id
user type=string // username
password type=string
*/

	/**
		@attrib name=add_package_service all_args=1 public=1 nologin=1
	**/
	function add_package_service($arr)
	{
		extract($arr);
		$errors = "";
		if(!$firstname) $errors.= t("Eesnimi puudu")."\n<br>";
		if(!$lastname) $errors.= t("Perenimi puudu")."\n<br>";
		if(!$gender) $errors.= t("Sugu m&auml;&auml;ramata")."\n<br>";
		if(!$birthday) $errors.= t("S&uuml;nniaeg puudu")."\n<br>";
		if(!$email) $errors.= t("E-post puudu")."\n<br>";
		if(!$start) $errors.= t("Algus puudu")."\n<br>";
		if(!$packet_id) $errors.= t("Paketi id puudu")."\n<br>";
		if(!$agency_id) $errors.= t("B&uuml;roo puudu")."\n<br>";
		//if(!$firstname) $errors.= t("Eesnimi puudu")."\n<br>";
		
		//arr($arr);
		
		$ol = new object_list(array(
			"class_id" => CL_SPA_BOOKIGS_ENTRY,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_SPA_BOOKIGS_ENTRY.RELTYPE_OWNER.id" => $agency_id,
		));
		
		if(!(is_oid($agency_id) && $this->can("view" , $agency_id)))
		{
			if(!$agency_id) $errors.= t("B&uuml;roo puudu")."\n<br>";
		}
		if(!(sizeof($ol->arr())))
		{
			$errors.= t("Reisib&uuml;roo pole &uuml;hegi SPA reisib&uuml;roo liidese omanik")."\n<br>";
		}
		$u = obj($agency_id);
		
		
		$auth = get_instance(CL_AUTH_CONFIG);
		if ($do_auth && ($auth_id = $auth->has_config()))
		{
			list($success, $msg) = $auth->check_auth($auth_id, array(
				"uid" => $u->prop("uid"),
				"password" => $password,
				"server" => $server
			));
		}
		else
		{
			$auth = get_instance(CL_AUTH_SERVER_LOCAL);
			list($success, $msg) = $auth->check_auth(NULL, array(
				"uid" => $u->prop("uid"),
				"password" => $password
			));
		}
		if (!empty($server))
		{
			$uid .= ".".$server;
		}

		if(!$success)
		{
			if(!$msg) $msg = t("Kasutaja ja parool ei klapi");
			$errors.= $msg."\n<br>";
		}
		if($errors) return array("error" => $errors);
		
		$entry = reset($ol->arr());

		$bd = explode("." , $birthday);
		$password = generate_password();
		$this->_set_cust_entry(array("obj_inst" => $entry,"request" => array("d" => array("0" => array(
			"fn" => $firstname,
			"ln" => $lastname,
			"email" => $email,
			"birthday" => array
			(
				"day" => $bd[0],"month" => $bd[1],"year" => $bd[2],
			),
			"gender" => ($gender == "W")?2:1,
			"start" => array
			(
				"day" => date("d" , $start),"month" => date("m" , $start),"year" => date("Y" , $start),
			),
			"package" => $packet_id,
			"pass" => $password,
			"not_send_email" => !$send_email,
		),),),));
		return $_SESSION["add_package_service"];
	}
	
	/**
		@attrib name=packet_list_service all_args=1 public=1 nologin=1
	**/
	function packet_list_service($arr)
	{
		$ol = new object_list(array(
			"class_id"=> CL_SHOP_PACKET,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if($_POST["show"])
		{
			arr($ol->names());
		}
		return $ol->names();
	}
	
	/**
		@attrib name=add_package_service_example all_args=1 public=1 nologin=1
	**/
	function add_package_service_example($arr)
	{
		if($_POST)
		{
			$result = $this->do_orb_method_call(array(
				"action" => "add_package_service",
				"class" => "spa_bookigs_entry",
				"params" => $_POST,
				"method" => "xmlrpc",
				"server" => "kalevspa.struktuur.ee"
			));
			arr($result);
		}
		
		die('<form name="postform2" id="postform2" method="post" action=http://kalevspa.struktuur.ee/orb.aw?class=spa_bookigs_entry&action=add_package_service_example>
			Eesnimi : <input type="textbox" name=firstname value="Eesnimega"><br>
			Perenimi : <input type="textbox" name=lastname value="Inimene"><br>
			Sugu : <input type="textbox" name=gender value="M"><br>
			S&uuml;nnip&auml;ev : <input type="textbox" name=birthday value="11.11.1980"><br>
			E-mail : <input type="textbox" name=email value="email"><br>
			Start <input type="textbox" name=start value="23888"><br>
			paketi id <input type="textbox" name=packet_id value="1394"><br>
			Parool : <input type="password" name=password value="spauto"><br>
			B&uuml;roo : <input type="textbox" name=agency_id value="63"><br>
			
			Saada mail ? <input type="checkbox" name=send_email value="1"><br>
			<input type=submit value="tee pakett"><br>
			</form>
			<form name="postform3" id="postform3" method="post" action=http://kalevspa.struktuur.ee/orb.aw?class=spa_bookigs_entry&action=packet_list_service>
			<input type="hidden" name=show value="1"><br>
			<input type=submit value="list"><br>
		');
	}
}
?>

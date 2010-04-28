<?php

namespace automatweb;
// vastuv6tt_keskkond.aw - Sisseastujate haldus
/*

@classinfo syslog_type=ST_VASTUV6TT_KESKKOND relationmgr=yes maintainer=voldemar

@groupinfo grp_sisseastuja caption="Sisseastujate lisamine"
@groupinfo grp_katse caption="Katsetulemuste sisestus"
@groupinfo grp_ylevaade_otsing caption="Sisseastujate nimekirjad"
	@groupinfo grp_ylevaade_otsing_b caption="Bakalaureuse&otilde;pe" parent=grp_ylevaade_otsing
	@groupinfo grp_ylevaade_otsing_m caption="Magistri&otilde;pe (4+2)" parent=grp_ylevaade_otsing
	@groupinfo grp_ylevaade_otsing_a caption="Magistri&otilde;pe (3+2)" parent=grp_ylevaade_otsing
	@groupinfo grp_ylevaade_otsing_d caption="Doktori&otilde;pe" parent=grp_ylevaade_otsing
	@groupinfo grp_ylevaade_otsing_o caption="&otilde;petajakoolitus" parent=grp_ylevaade_otsing
@groupinfo grp_v2ljund caption="&Uuml;levaated"
@groupinfo grp_seaded caption="Seaded"


@default table=objects
@default group=general
	@property vastuv6tukvoodid_r type=classificator no_caption=1 store=no
	@property vastuv6tukvoodid_l type=classificator no_caption=1 store=no
	@property vastuv6tukvoodid_k type=classificator no_caption=1 store=no


@default group=grp_sisseastuja
	@property sisseastuja_edit_title type=text store=no subtitle=1

	@property sisseastuja_edit_isikukood type=textbox store=no size=20
	@caption Isikukood

	@property sisseastuja_edit_oppetase type=select store=no
	@caption &otilde;ppetase

	// @property sisseastuja_edit_oppetase_b type=select store=no
	// @caption &otilde;ppetase

	@property sisseastuja_edit_submit_btn type=submit store=no
	@caption Otsi olemasolevat/Lisa uus



@default group=grp_katse
	@property katse_kood type=select store=no
	@caption Sisseastumiskatse

	@property katsetulem_title type=text store=no subtitle=1
	@caption Katse tulemused

	@property katsetulemused type=callback callback=callback_katse no_caption=1 parent=katsetulem_title store=no


@default group=grp_ylevaade_otsing
	@property sisseastuja_nimekiri_toolbar type=toolbar store=no no_caption=1


@default group=grp_ylevaade_otsing_b
	@property sisseastuja_nimekiri_table_b type=table store=no no_caption=1


@default group=grp_ylevaade_otsing_m
	@property sisseastuja_nimekiri_table_m type=table store=no no_caption=1


@default group=grp_ylevaade_otsing_a
	@property sisseastuja_nimekiri_table_a type=table store=no no_caption=1


@default group=grp_ylevaade_otsing_d
	@property sisseastuja_nimekiri_table_d type=table store=no no_caption=1


@default group=grp_ylevaade_otsing_o
	@property sisseastuja_nimekiri_table_o type=table store=no no_caption=1


@default group=grp_v2ljund
	@property v2ljund_type type=select store=no
	@caption Soovitud vaade

	@property v2ljund_oppetase type=select store=no
	@caption &otilde;ppetase

	@property v2ljund_eriala type=select store=no
	@caption Eriala

	@property v2ljund_oppevorm type=select store=no
	@caption &otilde;ppevorm

	@property ylevaade_table type=text no_caption=1 store=no wrapchildren=1

	@property v2ljund_submit type=submit store=no
	@caption Näita


@default table=objects
@default field=meta
@default method=serialize
@default group=grp_seaded
	@property sisseastujate_kaust type=relpicker reltype=RELTYPE_KAUST clid=CL_MENU
	@caption Sisseastujate kaust

	@property isikuandmete_kaust type=relpicker reltype=RELTYPE_KAUST clid=CL_MENU
	@caption Isikuandmete kaust

	@property avalduste_kaust type=relpicker reltype=RELTYPE_KAUST clid=CL_MENU
	@caption Avalduste kaust

	@property v2ljund_kaust type=textbox
	@caption Veebiv2ljundi HTML failide kaust (absolute filesystem path)

	@property bakalaureuse_seaded type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Bakalaureuse&otilde;ppe sisseastuja seadetehaldur

	@property bakalaureuse_seaded_piiratud type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Bakalaureuse&otilde;ppe sisseastuja piiratud &otilde;igustega seadetehaldur

	@property magistri_seaded type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Magistri&otilde;ppe (4+2) sisseastuja seadetehaldur

	@property magistri_seaded_piiratud type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Magistri&otilde;ppe (4+2) sisseastuja piiratud &otilde;igustega seadetehaldur

	@property magistri_seaded32 type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Magistri&otilde;ppe (3+2) sisseastuja seadetehaldur

	@property magistri_seaded32_piiratud type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Magistri&otilde;ppe (3+2) sisseastuja piiratud &otilde;igustega seadetehaldur

	@property doktori_seaded type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Doktori&otilde;ppe sisseastuja seadetehaldur

	@property doktori_seaded_piiratud type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption Doktori&otilde;ppe sisseastuja piiratud &otilde;igustega seadetehaldur

	@property opetaja_seaded type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption &Otilde;petajakoolituse sisseastuja seadetehaldur

	@property opetaja_seaded_piiratud type=relpicker reltype=RELTYPE_CFGMGR clid=CL_CFGMANAGER
	@caption &Otilde;petajakoolituse sisseastuja piiratud &otilde;igustega seadetehaldur

	@property keskkonna_seaded type=relpicker reltype=RELTYPE_CFGMGR_KESKKOND clid=CL_CFGMANAGER
	@caption Keskkonna seadetehaldur

	@property katsetulem_title type=text store=no subtitle=1
	@caption Sisseastumiskatsete toimumisajad ja kohad

	@property katse_ek_aegkoht type=textarea cols=65 rows=3
	@caption Eesti keele test

	@property katse_vk_aegkoht type=textarea cols=65 rows=3
	@caption Maastikuarhitektuuri erialatest

	@property katse_kk_aegkoht type=textarea cols=65 rows=3
	@caption Maastikuarhitektuuri joonistuseksam

	@property katse_vm_aegkoht type=textarea cols=65 rows=3
	@caption Maastikukaitse- ja hoolduse erialavestlus


//------------------ RELTYPES ----------------------

@reltype KAUST value=4 clid=CL_MENU
@caption Kaust

@reltype CFGMGR value=2 clid=CL_CFGMANAGER
@caption Sisseastuja seadete haldur

@reltype CFGMGR_KESKKOND value=5 clid=CL_CFGMANAGER
@caption Keskkonna seadete haldur

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isikuandmed

@reltype SISSEASTUJA value=3 clid=CL_VASTUV6TT_SISSEASTUJA
@caption Sisseastuja

*/

class vastuv6tt_keskkond extends class_base
{
	const AW_CLID = 336;

	function vastuv6tt_keskkond()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "vastuv6tt",
			"clid" => CL_VASTUV6TT_KESKKOND
		));
	}

	function callback_on_load($arr)
	{
		$keskkond = obj ($arr["request"]["id"]);
		$seadete_haldur = $keskkond->prop ("keskkonna_seaded");
		$this->cfgmanager = $seadete_haldur;
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		switch($data["name"])
		{
			case "v2ljund_eriala":
				$data["options"] = $this->get_trans("eriala_b");
				break;

			case "v2ljund_oppevorm":
				$data["options"] = $this->get_trans("oppevorm");
			break;

			case "v2ljund_type":
				$data["options"] = $this->get_trans("vaade");
			break;


			case "v2ljund_oppetase":
			case "sisseastuja_edit_oppetase":
				$data["options"] = $this->get_trans("oppetase");
				break;

			case "sisseastuja_edit_oppetase_b":
				$data["options"] = $this->get_trans("oppetase_b");
				break;

			case "sisseastuja_edit_title":
				$data["value"] = "Sisesta isikukood ja vali &otilde;ppetase. (Kui sellise isikukoodiga sisseastuja on andmebaasis, suunatakse tema andmete muutmisele, kui mitte, luuakse uus.)";
				break;

			case "katse_kood":
				$options = array(
					"EK" => "Eesti keele test",
					// "VV" => "Veterinaarmeditsiini eriala vestlus",
					// "VR" => "Rakendush&uuml;drobioloogia eriala vestlus",
					"VM" => "Maastikukaitse- ja hoolduse eriala vestlus",
					// "VL" => "Liha- ja piimatehnoloogia eriala vestlus",
					"KK" => "Maastikuarhitektuuri joonistuseksam",
					"VK" => "Maastikuarhitektuuri erialatest",
				);

				if (aw_global_get("katsetulemused_lastexamcode"))
				{
					$lastexamcode = aw_global_get("katsetulemused_lastexamcode");
					aw_session_del("katsetulemused_lastexamcode");
					$options = array ($lastexamcode => $options[$lastexamcode]);
				}

				$data["options"] = $options;
				break;

			case "ylevaade_table":
				if (aw_global_get("sisseastuja_pingerida_request"))
				{
					$pingerida_valik = aw_global_get('sisseastuja_pingerida_request');
					aw_session_del("sisseastuja_pingerida_request");
					$pingerida_valik = explode ("|", $pingerida_valik);
					$arr["ylevaade_vaade"] = $pingerida_valik["0"];
					$arr["ylevaade_oppetase"] = $pingerida_valik["1"];
					$arr["ylevaade_oppevorm"] = $pingerida_valik["2"];
					$arr["ylevaade_eriala"] = $pingerida_valik["3"];

					$ylevaade = $this->ylevaade ($arr);
					$data["value"] = $ylevaade["caption"] . "<br><br>" . $ylevaade["data"];
				}
				break;


			case "sisseastuja_nimekiri_table_b":
			case "sisseastuja_nimekiri_table_m":
			case "sisseastuja_nimekiri_table_a":
			case "sisseastuja_nimekiri_table_d":
			case "sisseastuja_nimekiri_table_o":
			case "sisseastuja_list_table":
				classload("vcl/table");
				$table = new aw_table(array(
				"layout" => "generic"
					));//& $arr["prop"]["vcl_inst"];

				$table->define_field(array(
					"name" => "modify",
					"caption" => t("Vaata/Muuda"),
				));

				$table->define_field(array(
					"name" => "sisseastuja_nr",
					"caption" => t("Sisseastuja Nr."),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "ik",
					"caption" => t("Isikukood"),
				));

				$table->define_field(array(
					"name" => "added",
					"caption" => t("Loodud"),
					"sortable" => 1
				));

				$table->define_field(array(
					"name" => "modified",
					"caption" => t("Muudetud"),
					"sortable" => 1
				));

				$table->define_chooser(array(
					"name" => "selection",
					"field" => "sisseastuja_id",
				));

				switch ($data["name"])
				{
					case "sisseastuja_nimekiri_table_b":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"oppetase" => "B",
						));
						break;
					case "sisseastuja_nimekiri_table_m":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"oppetase" => "M",
						));
						break;
					case "sisseastuja_nimekiri_table_a":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"oppetase" => "A",
						));
						break;
					case "sisseastuja_nimekiri_table_d":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"oppetase" => "D",
						));
						break;
					case "sisseastuja_nimekiri_table_o":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"oppetase" => "O",
						));
						break;
					case "sisseastuja_list_table":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_SISSEASTUJA,
							"createdby" => aw_global_get('uid'),
						));
						break;
				}

				$sisseastujad = $otsing->arr();

				foreach ($sisseastujad as $sisseastuja)
				{
					$change_url = $this->mk_my_orb("change", array(
						"id" => $sisseastuja->id(),
						"return_url" => urlencode(aw_global_get('REQUEST_URI')),
						"group" => "grp_sisseastuja_avaldused",
					), "vastuv6tt_sisseastuja");

					$table->define_data(array(
						"modify" => html::href(array(
							"caption" => t("Muuda4"),
							"url" => $change_url,
							)
						),
						"name" => $sisseastuja->prop("isik_firstname") . " " . $sisseastuja->prop("isik_lastname"),
						"ik" => $sisseastuja->prop("isik_personal_id"),
						"sisseastuja_nr" => sprintf("%04d", $sisseastuja->prop("sisseastuja_nr")),
						"added" => get_lc_date ($sisseastuja->created(), LC_DATE_FORMAT_SHORT_FULLYEAR ),
						"modified" => get_lc_date ($sisseastuja->modified(), LC_DATE_FORMAT_SHORT_FULLYEAR ),
						"sisseastuja_id" => $sisseastuja->id(),
					));
				}

				$table->set_default_sortby("sisseastuja_nr");
				$table->set_default_sorder("desc");
				$table->sort_by();
				$data["value"] = $table->draw(array(
					"records_per_page" => 100,
					"pageselector" => "text",
					"has_pages" => 1
				));
				break;


			case "sisseastuja_nimekiri_toolbar":
			case "sisseastuja_list_toolbar":
				$toolbar = &$data["toolbar"];
				$toolbar->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta valitud sisseastuja(d)"),
					"confirm" => t("Kustutada k&otilde;ik valitud sisseastuja(te) andmed ja avaldused?"),
					"action" => "mydelete",
				));
				break;

			case "ylevaade_toolbar":
				$toolbar = &$data["toolbar"];
				$toolbar->add_button(array(
					"name" => "search",
					"tooltip" => t("Uus otsing"),
					"url" => $this->mk_my_orb("change", array(
												"sisseastujaotsing" => "1",
												"group" => $arr["request"]["group"],
											)),
					"img" => "search.gif",
				));
				$toolbar->add_button(array(
					"name" => "delete",
					"tooltip" => t("Kustuta valitud sisseastuja(d)"),
					"action" => "mydelete",
					"confirm" => t("Kustutada k&otilde;ik valitud sisseastuja(te) andmed ja avaldused?"),
					"img" => "delete.gif",
				));
				break;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		if (aw_global_get("katsetulemused_data_del"))
		{
			return PROP_IGNORE;
		}

		if ($arr["request"]["v2ljund_type"])
		{
			aw_session_set("sisseastuja_pingerida_request", $arr["request"]["v2ljund_type"] . "|" . $arr["request"]["v2ljund_oppetase"] . "|" . $arr["request"]["v2ljund_oppevorm"] . "|" . $arr["request"]["v2ljund_eriala"]);
		}

		switch($data["name"])
		{
			case "katse_kood":
				$request =& $arr["request"];
				$katse_kood = $data["value"];
				$errors = false;
				$tulemused = array ();
				$previous_nrs = array ();

				foreach ($request as $key => $value)
				{
					$error = "";

					if (substr ($key, 0, 13) == "katsetulemus|")
					{
						$dataname = explode ("|", $key);

						if (($dataname[1] == "punkte") && ($request["katsetulemus|nr|" . $dataname[2]]))
						{
							$sisseastuja_nr = $request["katsetulemus|nr|" . $dataname[2]];
							$otsing = new object_list(array(
								"class_id" => CL_VASTUV6TT_SISSEASTUJA,
								"sisseastuja_nr" => $sisseastuja_nr,
							));

							$otsing = $otsing->ids();
							$sisseastuja_id = current ($otsing);
							$punkte = $request[$key];

							switch ($katse_kood)
							{
								case "EK":
									$valid_range = (($punkte >= 0) && ($punkte <= 100));
									break;

								case "KK":
									$valid_range = (($punkte >= 0) && ($punkte <= 6));
									break;

								case "VK":
									$valid_range = (($punkte >= 0) && ($punkte <= 4));
									break;

								// case "VL":
								case "VM":
								// case "VV":
								// case "VR":
									$valid_range = (($punkte >= 0) && ($punkte <= 5));
									break;
							}


							// error check
							if (in_array ($sisseastuja_nr, $previous_nrs))
							{
								$error .= "Korduv sisseastuja number. ";
							}

							if (!$valid_range)
							{
								$error .= "Punktide arv pole antud katse jaoks ige. ";
							}

							if (is_oid ($sisseastuja_id))
							{
								$sisseastuja = obj ($sisseastuja_id);

								if ($sisseastuja->prop("oppetase") != "B")
								{
									$error .= "Antud numbriga sisseastuja ei kandideeri bakalaureuseppesse. ";
								}

								if ( ($sisseastuja->prop("oppekeel") == "Eesti") && ($katse_kood == "EK") )
								{
									$error .= "T&otilde;enäoliselt on esinenud viga: Eesti keeles keskhariduse omandanul pole Eesti keele testi tarvis teha. ";
								}

								$tulemused[$dataname[2]]["id"] = $sisseastuja->id();
							}
							else
							{
								$error .= "Antud numbriga sisseastujat pole. ";
							}
							// END error check

							if ($error)
							{
								$tulemused[$dataname[2]]["error"] =  "Viga! " . $error;
								$errors = true;
							}

							$tulemused[$dataname[2]]["punkte"] =  $punkte;
							$tulemused[$dataname[2]]["nr"] =  $sisseastuja_nr;
							$previous_nrs[] = $sisseastuja_nr;
						}
					}
				}

				if ($errors)
				{
					aw_session_set("katsetulemused_error", "1");
				}
				else
				{
					foreach ($tulemused as $data)
					{
						$sisseastuja = obj ($data["id"]);
						$sisseastuja->set_prop("tulemus_" . strtolower ($katse_kood), $data["punkte"]);
						$sisseastuja->save();

						$connections = $sisseastuja->connections_from(array ("type" => RELTYPE_AVALDUS, "class_id" => CL_VASTUV6TT_AVALDUS));

						foreach ($connections as $connection)
						{
							$avaldus = $connection->to();
							$this->do_orb_method_call(array(
								"action" => "konkursipunktid",
								"class" => "vastuv6tt_avaldus",
								"params" => array(
									"avaldus_id" => $avaldus->id(),
									"sisseastuja_id" => $sisseastuja->id(),
									"konkursipunktid_final" => 1,
								)
							));
						}
					}
				}

				reset ($tulemused);

				if (!current ($tulemused))
				{
					return PROP_IGNORE;
				}

				krsort ($tulemused);
				$katsetulemused_data = serialize($tulemused);
				aw_session_set("katsetulemused_data", $katsetulemused_data);
				aw_session_set("katsetulemused_lastexamcode", $katse_kood);
				break;
		}
		return $retval;
	}

	function callback_post_save($arr)
	{
		if (aw_global_get("katsetulemused_data_del"))
		{
			aw_session_del("katsetulemused_data_del");
			aw_session_del("katsetulemused_data");
		}
	}


	function callback_mod_reforb($arr)
	{
		$arr["keskkond_return_url"] = urlencode(aw_global_get('REQUEST_URI'));
	}

	function callback_mod_retval($arr)
	{
		// Isikukoodi otsing & sisseastuja redirect
		$isikukood = $arr["request"]["sisseastuja_edit_isikukood"];
		$oppetase = $arr["request"]["sisseastuja_edit_oppetase"];
		if ($arr["request"]["sisseastuja_edit_oppetase_b"])
		{
			$oppetase = $arr["request"]["sisseastuja_edit_oppetase_b"];
		}
		$keskkond = obj ($arr["args"]["id"]);

		if ($isikukood && $oppetase)
		{
			$sisseastujate_kaust = $keskkond->prop ("sisseastujate_kaust");
			$isikuandmete_kaust = $keskkond->prop ("isikuandmete_kaust");

			// olemasoleva sisseastuja otsing
			$otsing =& new object_list(array(
				"class_id" => CL_VASTUV6TT_SISSEASTUJA,
				"parent"=> $sisseastujate_kaust,
				"isik_personal_id" => $isikukood,
			));

			$otsing = $otsing->ids();
			$sisseastuja = current ($otsing);

			if ($sisseastuja)
			{
				// Edit existing
				$sisseastuja = obj ($sisseastuja);

				$arr["args"]["id"] = $sisseastuja->id();
				$arr["args"]["class"] = "vastuv6tt_sisseastuja";
				$arr["args"]["group"] = "grp_sisseastuja_avaldused";
				$arr["args"]["action"] = "change";
			}
			else
			{
				// Make new & start editing
				$sisseastuja =& new object(array(
				   "parent" => $sisseastujate_kaust,
				   "class_id" => CL_VASTUV6TT_SISSEASTUJA,
				));

				$isik =& new object(array(
				   "parent" => $isikuandmete_kaust,
				   "class_id" => CL_CRM_PERSON,
				));


				$isik->save();
				$isik->set_prop("personal_id", $isikukood);
				$isik->save();
				$sisseastuja->save();
				$sisseastuja->set_name("Sisseastuja");
				$sisseastuja->set_prop("isik_personal_id", $isikukood);
				$sisseastuja->set_status(STAT_ACTIVE);
				$sisseastuja->set_prop("oppetase", $oppetase);
				$sisseastuja->connect(array ("to" => $isik, "reltype" => RELTYPE_PERSON));
				$sisseastuja->save();

				$keskkond->connect(array ("to" => $sisseastuja, "reltype" => RELTYPE_SISSEASTUJA));
				$keskkond->save();

				$arr["args"]["id"] = $sisseastuja->id();
				$arr["args"]["class"] = "vastuv6tt_sisseastuja";
				$arr["args"]["group"] = "grp_sisseastuja_avaldused";
				$arr["args"]["action"] = "change";
			}
		}
		// END Isikukoodi otsing & sisseastuja redirect

	}


//CUSTOM FUNCTIONS

	function callback_katse($arr)
	{
		$retval = array();
		$count = 20;

		if (aw_global_get("katsetulemused_data"))
		{
			$data = unserialize (aw_global_get("katsetulemused_data"));

			if (aw_global_get("katsetulemused_error"))
			{
				aw_session_del("katsetulemused_error");
				aw_session_del("katsetulemused_data");

				foreach ($data as $count => $row)
				{
					$change_url = $this->mk_my_orb("change", array(
						"id" => $row["id"],
						"return_url" => urlencode(aw_global_get('REQUEST_URI')),
						"group" => "grp_sisseastuja_andmed",
					), "vastuv6tt_sisseastuja");
					$change_href = html::href(array(
						"caption" => t("[Vaata]"),
						"url" => $change_url,
						)
					);

					$retval["row" . $count] = array(
						"type" => "text",
						"name" => "row" . $count,
						"caption" => (21 - $count) . t(". "),
						"store" => "no",
					);

					$retval["katsetulemus|nr|" . $count] = array(
						"type" => "textbox",
						"size" => 4,
						"name" => "katsetulemus|nr|" . $count,
						"caption" => t("sisseastuja nr."),
						"parent" => "row" . $count,
						"value" => $row["nr"],
					);

					$retval["katsetulemus|punkte|" . $count] = array(
						"type" => "textbox",
						"size" => 3,
						"name" => "katsetulemus|punkte|" . $count,
						"caption" => t("punkte"),
						"parent" => "row" . $count,
						"value" => $row["punkte"],
					);

					$retval["errormsg" . $count] = array(
						"type" => "text",
						"name" => "errormsg" . $count,
						"no_caption" => 1,
						"parent" => "row" . $count,
						"value" => $change_href . " &nbsp;&nbsp;&nbsp; " . $row["error"],
						"store" => "no",
					);
				}
			}
			else
			{
				aw_session_set("katsetulemused_data_del", "1");

				foreach ($data as $count => $row)
				{
					$sisseastuja = obj ($row["id"]);
					$sisseastuja_nimi = $sisseastuja->prop("isik_firstname") . " " . $sisseastuja->prop("isik_lastname");
					$change_url = $this->mk_my_orb("change", array(
						"id" => $row["id"],
						"return_url" => urlencode(aw_global_get('REQUEST_URI')),
						"group" => "grp_sisseastuja_katsetulemused",
					), "vastuv6tt_sisseastuja");
					$change_href = html::href(array(
						"caption" => t("[Muuda]"),
						"url" => $change_url,
						)
					);
					$retval["row" . $count] = array(
						"type" => "text",
						"name" => "row" . $count,
						"caption" => (21 - $count) . ".",
						"store" => "no",
						"value" => $change_href . " &nbsp;&nbsp;&nbsp; Sisseastuja: " . $sisseastuja_nimi . " (" . $row["nr"] . ")  Punkte: " . $row["punkte"],
					);
				}
			}
		}
		else
		{
			while ($count)
			{
				$retval["row" . $count] = array(
					"type" => "text",
					"name" => "row" . $count,
					"caption" => (21 - $count) . ".",
					"store" => "no",
				);

				$retval["katsetulemus|nr|" . $count] = array(
					"type" => "textbox",
					"size" => 4,
					"name" => "katsetulemus|nr|" . $count,
					"caption" => "sisseastuja nr.",
					"parent" => "row" . $count,
				);

				$retval["katsetulemus|punkte|" . $count] = array(
					"type" => "textbox",
					"size" => 3,
					"name" => "katsetulemus|punkte|" . $count,
					"caption" => t("punkte"),
					"parent" => "row" . $count,
				);

				$count--;
			}
		}

		return $retval;
	}


/**
    @attrib name=search
**/
	function sisseastuja_otsing($arr)
	{
	}

/**
    @attrib name=mydelete
**/
	function kustuta_minu_sisseastuja($arr)
	{
		if (is_array ($arr["selection"]))
		{
			foreach ($arr["selection"] as $selected)
			{
				if ($this->can("delete", $selected))
				{
					$sisseastuja = obj($selected);
					$connections = $sisseastuja->connections_from();

					foreach ($connections as $connection)
					{
						$to = $connection->to();
						$connections2 = $to->connections_from();

						$connection->delete();

						if ($to->class_id () == CL_VASTUV6TT_AVALDUS)
						{
							$to->set_prop("kustutatud", 1);
							$to->save();
						}

						$to->delete();
					}

					$sisseastuja->set_prop("kustutatud", 1);
					$sisseastuja->save();
					$sisseastuja->delete();
				}
				else
				{
					echo "&Otilde;igused puuduvad.<br>";
				}
			}
		}

		return urldecode ($arr["keskkond_return_url"]);
	}



	/**

		@attrib name=generate_html

		@returns

	**/
	function veebiv2ljund($arr)
	{
		aw_disable_acl();
		aw_set_exec_time(AW_LONG_PROCESS);
		$keskkond = obj ($arr["id"]);
		$arr["obj_inst"] = $keskkond;
		$save_path = $keskkond->prop ("v2ljund_kaust");
		$oppevormid = $this->get_trans("oppevorm");
		$vaated = $this->get_trans("vaade");
		$erialad = $this->get_trans("eriala_b");
		$oppetasemed = $this->get_trans("oppetase");
		$error = false;

		$mittevajalikud = array ("KM", "KD", "KO");
		$mittevajalikud_b = array ("TX");

		foreach ($vaated as $vaade => $caption1)
		{
			foreach ($oppetasemed as $oppetase => $caption1)
			{
				if (in_array (($vaade . $oppetase), $mittevajalikud))
				{ //kui mittevajalik kombinatsioon siis j2tka algusest
					continue;
				}


				foreach ($oppevormid as $oppevorm => $caption1)
				{
					if (($oppetase ==  "O")  && ($oppevorm == "K"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					if (($vaade ==  "A" ) && ($oppetase ==  "B")  && ($oppevorm == "R"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					if (($vaade ==  "K" ) && ($oppetase ==  "B")  && ($oppevorm == "R"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					if (($vaade ==  "K" ) && ($oppetase ==  "B")  && ($oppevorm == "L"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					if (($vaade ==  "A" ) && ($oppetase ==  "B")  && ($oppevorm == "L"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					if (($vaade ==  "V" ) && ($oppetase ==  "B")  && ($oppevorm == "K"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

					/*if (($vaade ==  "V" ) && ($oppetase ==  "B")  && ($oppevorm == "L"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}
*/
					foreach ($erialad as $eriala => $caption1)
					{

						if (($oppetase ==  "O")  && ($eriala != "AG"))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
							continue;
						}

						if (($oppetase ==  "B")  && in_array (($eriala), $mittevajalikud_b))
						{ //kui mittevajalik kombinatsioon siis j2tka algusest
						continue;
						}

						$arr["ylevaade_vaade"] = $vaade;
						$arr["ylevaade_oppetase"] = $oppetase;
						$arr["ylevaade_oppevorm"] = $oppevorm;
						$arr["ylevaade_eriala"] = $eriala;

						$data = $this->ylevaade($arr);

						if ( (is_array ($data)) && ($data["data"]) )
						{
							$tmp = $save_path . $vaade . "-" . $oppetase . "-" . $oppevorm . "-" . $eriala . ".html";
							$htmldata =
							'<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
							<html>
							<head>
							<title> &Uuml;li&otilde;pilaste vastuv&otilde;tt </title>
							<meta http-equiv="Content-Type" content="text/html; charset=IBM852">
							<style>
							td
							{
								font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
								color: #000000;
								font-size: x-small;
							}
							td.awmenuedittablehead
							{
								color: #000000;
								font-weight : bold;
								text-align: left;
							}
							</style>
							</head>
							<body bgcolor="#FFFFFF"><b>Viimati uuendatud:</b> ' .
							$data["time"] . "<br><br>" .
							$data["data"] .
							'</body>
							</html>';

							$error .= $this->savehtml($tmp, $htmldata);

							/*if (($vaade == "A") && ($oppetase != "B"))*/
							if (($vaade == "A") && (($oppetase == "M") || ($oppetase == "D"))	)
							{
								continue 3;
							}

							if ($vaade == "K")
							{
								continue 2;
							}
						}
					}
				}
			}
		}

		aw_restore_acl();
		if ($error)
		{
			//return $error;
			return false;
		}
		else
		{
			return true;
		}
	}

	function savehtml($location, $data)
	{
		$error = false;
		$fd = fopen ($location, "w");

		if ($fd)
		{
			$write_retval = fwrite ($fd, $data);
			fclose ($fd);

			if ($write_retval == (-1))
			{
				$error .= "Faili ei saand kirjutada. ";
			}
		}
		else
		{
			$error .= "Faili ei saand luua. ";
		}

		return $error;
	}


	function ylevaade($arr)
	{
		enter_function("ylevaade");
		aw_disable_acl();
		$vaade = $arr["ylevaade_vaade"];
		$oppetase = $arr["ylevaade_oppetase"];
		$oppevorm = $arr["ylevaade_oppevorm"];
		$eriala = $arr["ylevaade_eriala"];

		$caption = $this->get_trans("vaade", $vaade) . " (";
		$caption .= $oppetase ? ($this->get_trans("oppetase", $oppetase) . ". ") : "";
		$caption .= $oppevorm ? ($this->get_trans("oppevorm", $oppevorm) . ". ") : "";
		$caption .= $eriala ? ($this->get_trans("eriala_b", $eriala) . ". ") : "";
		$caption .= ")";

		classload("vcl/table");
		$table = new vcl_table(array(
			"layout" => "generic",
		));

		switch ($vaade)
		{
						case "K":
				$keskkond = $arr['obj_inst'];
				$classificator_id = $keskkond->prop("vastuv6tukvoodid");

				switch ($oppetase)
				{
					case "B":
						$table->define_field(array(
							"name" => "eriala",
							"caption" => t("Eriala"),
						));

						$table->define_field(array(
							"name" => "kohti",
							"align" => "center",
							"caption" => t("&otilde;ppekohti"),
						));

						$table->define_field(array(
							"name" => "avaldusi",
							"align" => "center",
							"caption" => t("Esitatud avaldusi"),
						));

						$table->set_default_sortby("eriala");
						$table->set_default_sorder("asc");

						if ($oppevorm == "R")
						{
							$table->define_field(array(
								"name" => "konkurss",
								"align" => "center",
								"caption" => t("Konkurss"),
							));
						$table->set_default_sortby("konkurss_hidden");
						$table->set_default_sorder("desc");

						}


						//kvoodid
						$obj_type = get_instance(CL_OBJECT_TYPE);
						$keskkond_ot = $obj_type->get_obj_for_class(array(
							"clid" => CL_VASTUV6TT_KESKKOND,
						));
						$keskkond_ot = new object($keskkond_ot);
						$classificator = $keskkond_ot->meta("classificator");
						$parent = new object($classificator["vastuv6tukvoodid_" . strtolower($oppevorm)]);
						enter_function("ylevaade::ol");
						$metaobj_list = new object_list(array(
							"parent" => $parent->id(),
							"class_id" => CL_META,
							"lang_id" => array(),
						));
						$metaobj_arr = $metaobj_list->arr();
						exit_function("ylevaade::ol");
						$kvoodid = array ();

						foreach ($metaobj_arr as $metaobj)
						{
							$kvoodid[$metaobj->name()] = $metaobj->comment();
						}
						//END kvoodid

						$erialad = $this->get_trans("eriala_b");

						$avaldusi_kokku = 0;
						$kohti_kokku = 0;

						foreach ($kvoodid as $eriala_kood => $kohti)
						{
							$eriala = $erialad[$eriala_kood];
							enter_function("ylevaade::ol2");
							$avaldused = new object_list(array(
								"class_id" => CL_VASTUV6TT_AVALDUS,
								"oppevorm" => $oppevorm,
								"eriala" => $eriala_kood,
								"oppetase" => $oppetase,
							));
							$avaldusi = $avaldused->count();
							exit_function("ylevaade::ol2");

							if ($avaldusi > 0 && $kohti > 0)
							{
								$konkurss = number_format(round($avaldusi/$kohti, 2),2);
							}
							else
							{
								$konkurss = "0";
							}

							if ($oppevorm == "R")
							{
								$table->define_data(array(
									"eriala" => $eriala,
									"kohti" => $kohti,
									"avaldusi" => $avaldusi,
									"konkurss" => $konkurss,
									"konkurss_hidden" => $konkurss
								));
							}
							else
							{
								$table->define_data(array(
									"eriala" => $eriala,
									"kohti" => $kohti,
									"avaldusi" => $avaldusi,
								));
							}

							$avaldusi_kokku += $avaldusi;
							$kohti_kokku += $kohti;
						}

						if ($avaldusi_kokku > 0 && $kohti_kokku > 0)
						{
							$yldkonkurss = " <b>".round($avaldusi_kokku/$kohti_kokku, 2);
						}
						else
						{
							$yldkonkurss = "0";
						}
						/*
						if ($oppevorm == "R")
						{
							$table->define_data(array(
								"eriala" => '<hr style="height: 1px;">',
								"kohti" => '<hr style="height: 1px;">',
								"avaldusi" => '  <hr style="height: 1px;">',
								"konkurss" => '  <hr style="height: 1px;">',
							));
						}
						else
						{
							$table->define_data(array(
								"eriala" => '<hr style="height: 1px;">',
								"kohti" => '<hr style="height: 1px;">',
								"avaldusi" => '<hr style="height: 1px;">',
							));
						}*/

						if ($oppevorm == "R")
						{
							$table->define_data(array(
								"eriala" => "<b>Kokku:",
								"kohti" => "<b>".$kohti_kokku,
								"avaldusi" => "<b>".$avaldusi_kokku,
								"konkurss" => "<b>".$yldkonkurss,
								"konkurss_hidden" => -10000,
							));
						}
						else
						{
							$table->define_data(array(
								"eriala" => "~~",
								"kohti" => "<b>Avaldusi kokku:",
								"avaldusi" => "<b>".$avaldusi_kokku,
							));
						}
					break;

					case "M":
					case "D":
					case "O":
						return "";
						break;
				}
				break;

			case "A":
				$table->define_field(array(
					"name" => "nimi",
					"caption" => t("Nimi"),
				));

				if ($oppetase != "B")
				{
					$table->define_field(array(
						"name" => "oppevorm",
						"caption" => t("&otilde;ppevorm"),
					));
					$table->define_field(array(
						"name" => "eriala",
						"caption" => t("Eriala"),
					));
				}


				$table->set_default_sortby("nimi");
				$table->set_default_sorder("asc");

				switch ($oppetase)
				{
					case "B":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_AVALDUS,
							"eriala" => $eriala,
							"oppevorm" => $oppevorm,
							"oppetase" => $oppetase,
						));
						$avaldused = $otsing->arr();

						if (count($otsing->ids()) < 1)
						{
								$connections = array();
						}
						else
						{
							$c = new connection();
							$connections = $c->find(array(
								 "from.class_id" => CL_VASTUV6TT_SISSEASTUJA,
								 "to" => $otsing->ids()
							));
						}

						foreach ($connections as $connection)
						{
							$sisseastuja = obj($connection["from"]);
							$table->define_data(array(
								"nimi" => $sisseastuja->prop("isik_lastname") . ", " . $sisseastuja->prop("isik_firstname"),
							));
						}
						break;

						case "M":
						case "A":
						case "D":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_AVALDUS,
//							"eriala" => $eriala,
//							"oppevorm" => $oppevorm,
							"oppetase" => $oppetase,
						));
						$avaldused = $otsing->arr();

						if (count($otsing->ids()) < 1)
						{
								$connections = array();
						}
						else
						{
							$c = new connection();
							$connections = $c->find(array(
								 "from.class_id" => CL_VASTUV6TT_SISSEASTUJA,
								 "to" => $otsing->ids()
							));
						}

						foreach ($connections as $connection)
						{
							$sisseastuja = obj($connection["from"]);
							$avaldus = obj($connection["to"]);
							//$avaldus = $connection->to();
							$table->define_data(array(
								"nimi" => $sisseastuja->prop("isik_lastname") . ", " . $sisseastuja->prop("isik_firstname"),
								"oppevorm" => $this->get_trans("oppevorm", $avaldus->prop("oppevorm")),
								"eriala" => $this->get_trans("eriala_m", $avaldus->prop("eriala")),
							));
						}
						break;
						case "O":
						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_AVALDUS,
	//						"eriala" => $eriala,
							"oppevorm" => $oppevorm,
							"oppetase" => $oppetase,
						));
						$avaldused = $otsing->arr();

						if (count($otsing->ids()) < 1)
						{
								$connections = array();
						}
						else
						{
							$c = new connection();
							$connections = $c->find(array(
								 "from.class_id" => CL_VASTUV6TT_SISSEASTUJA,
								 "to" => $otsing->ids()
							));
						}

						foreach ($connections as $connection)
						{
							$sisseastuja = obj($connection["from"]);
							$avaldus = obj($connection["to"]);
							//$avaldus = $connection->to();
							$table->define_data(array(
								"nimi" => $sisseastuja->prop("isik_lastname") . ", " . $sisseastuja->prop("isik_firstname"),
								"oppevorm" => $this->get_trans("oppevorm", $avaldus->prop("oppevorm")),
								"eriala" => $this->get_trans("eriala_m", $avaldus->prop("eriala")),
							));
						}
						break;
					}
				break;


			case "V":
				switch ($oppetase)
				{
					case "B":
					$table->define_field(array(
							"name" => "jrk_nr",
							"caption" => t("Jrk nr"),
						));


					$table->define_field(array(
							"name" => "sisseastuja_nr",
							"caption" => t("Sisseastuja kood"),
						));

						$table->define_field(array(
							"name" => "konkursipunktid",
							"caption" => t("Konkursipunktid"),
						));

						$table->define_field(array(
							"name" => "otsus",
							"caption" => t("Vastuv&otilde;tukomisjoni otsus"),
						));

						$table->define_field(array(
							"name" => "registreerus",
							"caption" => t("Registreerus"),
						));

						/*$table->define_field(array(
							"name" => "punkte",
							"caption" => t("Punkte"),
						));*/

						$table->set_numeric_field("jrk_nr");
						$table->set_default_sortby("jrk_nr");
						$table->set_default_sorder("asc");

						$otsing = new object_list(array(
							"class_id" => CL_VASTUV6TT_AVALDUS,
							"eriala" => $eriala,
							"oppevorm" => $oppevorm,
							"oppetase" => $oppetase,
						));
						$avaldused = $otsing->arr();

						if (count($otsing->ids()) < 1)
						{
								$connections = array();
						}
						else
						{
							$c = new connection();
							$connections = $c->find(array(
								 "from.class_id" => CL_VASTUV6TT_SISSEASTUJA,
								 "to" => $otsing->ids()
							));
						}

						foreach ($connections as $connection)
						{
							$sisseastuja = obj($connection["from"]);
							$avaldus = obj($connection["to"]);

							if ($sisseastuja->prop("oppetase") != "B")
							{
								continue; // mis siin ikkagi saab?
							}

							$punktid = $this->do_orb_method_call(array(
								"action" => "konkursipunktid",
								"class" => "vastuv6tt_avaldus",
								"params" => array(
									"avaldus_id" => $avaldus->id(),
									"sisseastuja_id" => $sisseastuja->id(),
									"konkursipunktid_final" => 1,
								)
							));

							$table->define_data(array(
							"jrk_nr" => $avaldus->prop("jrk_nr"),
							"sisseastuja_nr" => $avaldus->prop("eriala") . $avaldus->prop("oppevorm") . $sisseastuja->prop("oppetase") . sprintf("%04d",$sisseastuja->prop("sisseastuja_nr")),
							"konkursipunktid" => number_format($avaldus->prop("konkursipunktid"), 2),
							"otsus" => str_replace("ä","&otilde;", $avaldus->prop("komisjoni_otsus")),
							"registreerus" => $avaldus->prop("registreerus"),
								"punkte" => $punktid,
							));
						}
						break;

					case "M":
					case "A":
					case "D":
					case "O":
						return "";
						break;
				}
			break;
		}

		//$time = getdate ();
		$time = date ('\<\i\>\<b\>d.m Y \<\/\b\> H:i\<\/\i\>');
		//$time = "<i><b>" . $time["mday"] . ". " . $time["mon"] . " " . $time["year"] . "</b> " . $time["hours"] //. ":" . $time["minutes"] . "</i>";


		$table->set_numeric_field("konkurss_hidden");
		$table->sort_by();
		$data = $table->get_html();
		$retval = array (
			"time" => $time,
			"caption" => $caption,
			"data" => $data,
		);
		aw_restore_acl();
		exit_function("ylevaade");
		return $retval;
	}


	function get_trans($propname, $code = false)
	{
		switch($propname)
		{
					case "eriala_b":
						$trans = array(
							"AG" => "Agronoomia",
							"AI" => "Aiandus",
							"PS" => "P&otilde;llumajandussaaduste tootmine ja turustamine",
							"LK" => "Loomakasvatus",
							"LP" => "Liha- ja piimatehnoloogia",
							"KA" => "Kalakasvatus",
							"AK" => "Agro&ouml;koloogia",
							"VM" => "Veterinaarmeditsiin",
							"AR" => "Maastikuarhitektuur",
							"MH" => "Maastikukaitse ja -hooldus",
							"KJ" => "Keskkonnamajandus",
							"GE" => "Geodeesia",
							"MK" => "Maakorraldus",
							"EH" => "Maaehitus",
							"VE" => "Veemajandus",
							"KP" => "Kinnisvara planeerimine",
							"EV" => "&Ouml;konoomika ja ettev&otilde;tlus",
							"MF" => "Majandusarvestus ja finantsjuhtimine",
							"ME" => "Metsamajandus",
							"MT" => "Metsat&ouml;&ouml;stus",
							"LV" => "Loodusvarade kasutamine ja kaitse",
							"EG" => "Ergonoomika",
							"EK" => "Energiakasutus",
							"TH" => "P&otilde;llumajandustehnika",
							"ET" => "Ettev&otilde;ttetehnika",
							"RB" => "Rakendush&uuml;drobioloogia",
						);
						break;

					case "eriala_a":
						$trans = array(
							"AG" => "Agronoomia",
							"AK" => "Agro&ouml;koloogia",
							"AI" => "Aiandus",
							"EK" => "Energiakasutus",
							"AR" => "Maastikuarhitektuur",
							"PS" => "P&otilde;llumajandussaaduste tootmine ja turustamine",
							"LK" => "Loomakasvatus",
							"LP" => "Liha- ja piimatehnoloogia",
							"KA" => "Kalakasvatus",
							"MH" => "Maastikukaitse ja -hooldus",
							"KJ" => "Keskkonnamajandus",
							"GE" => "Geodeesia",
							"MK" => "Maakorraldus",
							"KP" => "Kinnisvara planeerimine",
							"EV" => "&Ouml;konoomika ja ettev&otilde;tlus",
							"MF" => "Majandusarvestus ja finantsjuhtimine",
							"ME" => "Metsamajandus",
							"MT" => "Metsat&ouml;&ouml;stus",
							"LV" => "Loodusvarade kasutamine ja kaitse",
							"EG" => "Ergonoomika",
							"TH" => "P&otilde;llumajandustehnika",
							"ET" => "Ettev&otilde;ttetehnika",
							"BD" => "Elustiku mitmekesisuse ja mitmefunktsiooniliste maastike korraldamine",
						);
						break;

					case "eriala_m":
						$trans = array(
							"AI" => "Aiandus",
							"GK" => "Agrokeemia",
							"MV" => "Maaviljelus",
							"RM" => "Rohumaaviljelus ja s&ouml;&ouml;datootmine",
							"TK" => "Taimekaitse",
							"TV" => "Taimekasvatus",
							"MD" => "Mullateadus",
							"AR" => "Maastikuarhitektuur",
							"KK" => "Keskkonnakaitse",
							"PT" => "Piimatehnoloogia",
							"LT" => "Lihatehnoloogia",
							"TG" => "Toiduh&uuml;gieen ja veterinaarkontroll",
							"LK" => "Loomakasvatus",
							"ME" => "Metsamajandus",
							"MT" => "Metsat&ouml;&ouml;stus",
							"EH" => "Maaehitus",
							"MM" => "Maam&otilde;&otilde;tmine",
							"VE" => "Veemajandus",
							"RP" => "Raamatupidamine ja rahandus",
							"EV" => "&Ouml;konoomika ja ettev&otilde;tlus",
							"TU" => "Turundus ja juhtimine",
							"TH" => "P&otilde;llumajandustehnika",
							"PE" => "P&otilde;llumajandusenergeetika",
							"HB" => "H&uuml;drobioloogia",
							"LG" => "Looma&ouml;koloogia",
							"BM" => "Botaanika ja m&uuml;koloogia",
						);
						break;

					case "eriala_d":
						$trans = array(
							"KR" => "Keskkonnateadus ja rakendusbioloogia",
							"PJ" => "P&otilde;llumajandus",
							"VD" => "Veterinaarmeditsiin ja toiduteadus",
							"MN" => "Metsandus",
							"TE" => "Tehnikateadus",
						);
						break;

					case "eriala_o":
						$trans = array(
							"OP" => "&Otilde;petajakoolitus",
						);
						break;

			// case "eriala_b_decline":
				// $trans = array(
					// "AG" => "agronoomia",
					// "AI" => "aianduse",
					// "PS" => "p&otilde;llumajandussaaduste tootmise ja turustamise",
					// "LK" => "loomakasvatuse",
					// "LP" => "liha- ja piimatehnoloogia",
					// "KA" => "kalakasvatuse",
					// "AK" => "agro&ouml;koloogia",
					// "VM" => "veterinaarmeditsiini",
					// "AR" => "maastikuarhitektuuri",
					// "MH" => "maastikukaitse ja -hoolduse",
					// "KJ" => "keskkonnamajanduse",
					// "GE" => "geodeesia",
					// "MK" => "maakorralduse",
					// "EH" => "maaehituse",
					// "VE" => "veemajanduse",
					// "KP" => "kinnisvara planeerimise",
					// "EV" => "&ouml;konoomika ja ettev&otilde;tluse",
					// "MF" => "majandusarvestuse ja finantsjuhtimise",
					// "ME" => "metsamajanduse",
					// "MT" => "metsat&ouml;&ouml;stuse",
					// "LV" => "loodusvarade kasutamise ja kaitse",
					// "EG" => "ergonoomika",
					// "EK" => "energiakasutuse",
					// "TH" => "p&otilde;llumajandustehnika",
					// "ET" => "ettev&otilde;ttetehnika",
					// "RB" => "rakendush&uuml;drobioloogia",
				// );
			// break;

			// case "eriala_m_decline":
				// $trans = array(
					// "AI" => "aianduse",
					// "GK" => "agrokeemia",
					// "MV" => "maaviljeluse",
					// "RM" => "rohumaaviljeluse ja s&ouml;&ouml;datootmise",
					// "TK" => "taimekaitse",
					// "TV" => "taimekasvatuse",
					// "MD" => "mullateaduse",
					// "KM" => "kodumajanduse",
					// "OP" => "&Otilde;petajakoolituse",
					// "AR" => "maastikuarhitektuuri",
					// "KK" => "keskkonnakaitse",
					// "VM" => "veterinaarmeditsiini",
					// "PT" => "piimatehnoloogia",
					// "LT" => "lihatehnoloogia",
					// "TG" => "toiduh&uuml;gieen ja veterinaarkontrolli",
					// "TI" => "toiduteaduse",
					// "LK" => "loomakasvatuse",
					// "KB" => "keemiline bioloogia",
					// "ME" => "metsamajanduse",
					// "MT" => "metsat&ouml;&ouml;stuse",
					// "EH" => "maaehituse",
					// "MM" => "maam&otilde;&otilde;tmise",
					// "VE" => "veemajanduse",
					// "RP" => "raamatupidamise ja rahanduse",
					// "EV" => "&ouml;konoomika ja ettev&otilde;tluse",
					// "TU" => "turunduse ja juhtimise",
					// "OV" => "&ouml;konoomika ja ettev&otilde;tluse (1-aastane &otilde;pe)",
					// "MF" => "majandusarvestuse ja finantsjuhtimise",
					// "TH" => "p&otilde;llumajandustehnika",
					// "PE" => "p&otilde;llumajandusenergeetika",
					// "HB" => "h&uuml;drobioloogia",
					// "LG" => "looma&ouml;koloogia",
					// "BM" => "botaanika ja m&uuml;koloogia",
					// "TF" => "taimef&uuml;sioloogia",
					// "GE" => "geneetika",
					// "BK" => "biokeemia",
				// );
			// break;

					case "eriala_b_decline":
						$trans = array(
							"AG" => "agronoomia",
							"AI" => "aianduse",
							"PS" => "p&otilde;llumajandussaaduste tootmise ja turustamise",
							"LK" => "loomakasvatuse",
							"LP" => "liha- ja piimatehnoloogia",
							"KA" => "kalakasvatuse",
							"AK" => "agro&ouml;koloogia",
							"VM" => "veterinaarmeditsiini",
							"AR" => "maastikuarhitektuuri",
							"MH" => "maastikukaitse ja -hoolduse",
							"KJ" => "keskkonnamajanduse",
							"GE" => "geodeesia",
							"MK" => "maakorralduse",
							"EH" => "maaehituse",
							"VE" => "veemajanduse",
							"KP" => "kinnisvara planeerimise",
							"EV" => "&ouml;konoomika ja ettev&otilde;tluse",
							"MF" => "majandusarvestuse ja finantsjuhtimise",
							"ME" => "metsamajanduse",
							"MT" => "metsat&ouml;&ouml;stuse",
							"LV" => "loodusvarade kasutamise ja kaitse",
							"EG" => "ergonoomika",
							"EK" => "energiakasutuse",
							"TH" => "p&otilde;llumajandustehnika",
							"ET" => "ettev&otilde;ttetehnika",
							"RB" => "rakendush&uuml;drobioloogia",
						);
						break;

					case "eriala_a_decline":
						$trans = array(
							"AG" => "agronoomia",
							"AK" => "agro&ouml;koloogia",
							"AI" => "aianduse",
							"EK" => "energiakasutuse",
							"AR" => "maastikuarhitektuuri",
							"PS" => "p&otilde;llumajandussaaduste tootmise ja turustamise",
							"LK" => "loomakasvatuse",
							"LP" => "liha- ja piimatehnoloogia",
							"KA" => "kalakasvatuse",
							"MH" => "maastikukaitse ja -hoolduse",
							"KJ" => "keskkonnamajanduse",
							"GE" => "geodeesia",
							"MK" => "maakorralduse",
							"KP" => "kinnisvara planeerimise",
							"EV" => "&ouml;konoomika ja ettev&otilde;tluse",
							"MF" => "majandusarvestuse ja finantsjuhtimise",
							"ME" => "metsamajanduse",
							"MT" => "metsat&ouml;&ouml;stuse",
							"LV" => "loodusvarade kasutamise ja kaitse",
							"EG" => "ergonoomika",
							"TH" => "p&otilde;llumajandustehnika",
							"ET" => "ettev&otilde;ttetehnika",
							"BD" => "elustiku mitmekesisuse ja mitmefunktsiooniliste maastike korraldamise",
						);
						break;

					case "eriala_m_decline":
						$trans = array(
							"AI" => "aianduse",
							"GK" => "agrokeemia",
							"MV" => "maaviljeluse",
							"RM" => "rohumaaviljeluse ja s&ouml;&ouml;datootmise",
							"TK" => "taimekaitse",
							"TV" => "taimekasvatuse",
							"MD" => "mullateaduse",
							"AR" => "maastikuarhitektuuri",
							"KK" => "keskkonnakaitse",
							"PT" => "piimatehnoloogia",
							"LT" => "lihatehnoloogia",
							"TG" => "toiduh&uuml;gieeni ja veterinaarkontrolli",
							"LK" => "loomakasvatuse",
							"ME" => "metsamajanduse",
							"MT" => "metsat&ouml;&ouml;stuse",
							"EH" => "maaehituse",
							"MM" => "maam&otilde;&otilde;tmise",
							"VE" => "veemajanduse",
							"RP" => "raamatupidamise ja rahanduse",
							"EV" => "&ouml;konoomika ja ettev&otilde;tluse",
							"TU" => "turunduse ja juhtimise",
							"TH" => "p&otilde;llumajandustehnika",
							"PE" => "p&otilde;llumajandusenergeetika",
							"HB" => "h&uuml;drobioloogia",
							"LG" => "looma&ouml;koloogia",
							"BM" => "botaanika ja m&uuml;koloogia",
						);
						break;

					case "eriala_d_decline":
						$trans = array(
							"KR" => "keskkonnateaduse ja rakendusbioloogia",
							"PJ" => "p&otilde;llumajanduse",
							"VD" => "veterinaarmeditsiini ja toiduteaduse",
							"MN" => "metsanduse",
							"TE" => "tehnikateaduse",
						);
						break;

					case "eriala_o_decline":
						$trans = array(
							"OP" => "&otilde;petajakoolituse",
						);

			case "oppevorm_decline":
				$trans = array(
					"R" => "riigieelarvelisele",
					"L" => "riigieelarvev&auml;lisele",
					"K" => "kaug&otilde;ppe",
				);
			break;

			case "oppevorm":
				$trans = array(
					"R" => "Riigieelarveline",
					"L" => "Riigieelarvev&auml;line",
					"K" => "Kaug&otilde;pe",
				);
				break;

			case "vaade":
				$trans = array(
					"K" => "Konkursid",
					"A" => "Avalduste nimekiri",
					"V" => "Vastuv&otilde;etud",
				);
				break;

			case "oppetase":
				$trans = array(
					"M" => "Magistri&otilde;pe (4+2)",
					"A" => "Magistri&otilde;pe (3+2)",
					"D" => "Doktori&otilde;pe",
					"O" => "&Otilde;petajakoolitus",
					"B" => "Bakalaureuse&otilde;pe",
				);
				break;

			case "oppetase_b":
				$trans = array(
					"B" => "Bakalaureuse&otilde;pe",
				);
				break;

			case "social_status";
				$trans = array (
					"0" => "Vallaline",
					"1" => "Abielus",
					"2" => "Vabaabielus",
				);
				break;

			case "haridus_v2lismaal":
				$trans = array(
					"E" => "Ei",
					"J" => "Jah",
				);
				break;

			case "katse":
				$trans = array(
					"EK" => "Eesti keele test",
					// "VV" => "Veterinaarmeditsiini eriala vestlus",
					// "VR" => "Rakendush&uuml;drobioloogia eriala vestlus",
					"VM" => "Maastikukaitse- ja hoolduse eriala vestlus",
					// "VL" => "Liha- ja piimatehnoloogia eriala vestlus",
					"KK" => "Maastikuarhitektuuri joonistuseksam",
					"VK" => "Maastikuarhitektuuri erialatest",
				);
				break;

			case "haridus_medal":
				$trans = array(
					"E" => "Ei",
					"M" => "Kuld",
					"H" => "H&otilde;be",
					"K" => "Kiitus",
				);
				break;

			case "haridus_kool_tyyp":
				$trans = array(
					"KK" => "Keskkool",
					"G" => "G&uuml;mnaasium",
					"T" => "Tehnikum",
					"KU" => "Kutsekeskkool",
				);
				break;

			case "haridus_kool_6ppevorm":
				$trans = array(
					"P" => "P&auml;evane",
					"O" => "&otilde;htune",
					"K" => "Kaugpe",
					"E" => "Ekstern",
				);
				break;

			case "v66rkeel":
				$trans = array(
					"I" => "Inglise",
					"S" => "Saksa",
					"V" => "Vene",
				);
				break;

			case "elukoht":
				$trans = array(
					"L" => "Linn",
					"M" => "Maa",
				);
				break;

			case "elamisluba":
				$trans = array(
					"-" => "",
					"A" => "Alaline",
					"T" => "T&auml;htajaline",
				);
				break;

			case "gender":
				$trans = array(
					"1" => "mees",
					"2" => "naine",
				);
				break;
		}

		if ($code !== false)
		{
			$trans = $trans[$code];
		}

		return $trans;
	}
}

?>

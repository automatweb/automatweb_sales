<?php

namespace automatweb;
// vastuv6tt_avaldus.aw - Avaldus
/*

@classinfo syslog_type=ST_VASTUV6TT_AVALDUS relationmgr=yes no_status=1 maintainer=voldemar
@tableinfo vastuv6tt_avaldus index=oid master_table=objects master_index=oid

@default table=vastuv6tt_avaldus
@default group=general
	// @property eriala_b type=select store=no
	// @caption Eriala

	// @property eriala_m type=select store=no
	// @caption Eriala

	@property eriala type=select
	@caption Eriala

	// @property eriala type=hidden value=1
	@property sisseastuja_nr type=hidden
	@property sisseastuja_kood type=hidden
	@property konkursipunktid type=hidden
	@property komisjoni_otsus type=hidden
	@property jrk_nr type=hidden
	@property registreerus type=hidden
	@property oppetase type=hidden
	@property isik_firstname type=hidden
	@property isik_lastname type=hidden
	@property kustutatud type=hidden

	@property oppevorm type=select
	@caption &Otilde;ppevorm

	@property eelistus type=chooser
	@caption Kas eelistuseks on antud eriala?

*/

/*

CREATE TABLE `vastuv6tt_avaldus` (
	`oid` int(11) NOT NULL default '0',
	`kustutatud` int(1) NOT NULL default '0',

	`sisseastuja_nr` int(4) ZEROFILL NOT NULL default '0',
	`konkursipunktid` float(6) default NULL,
	`komisjoni_otsus` varchar(60) default NULL,
	`eriala` char(3) default NULL,
	`eelistus` int(1) default NULL,
	`isik_firstname` varchar(50) default NULL,
	`isik_lastname` varchar(50) default NULL,
	`oppevorm` enum ('R','L','K') default 'R',
	`oppetase` enum ('B','M','D','O','A') default 'B',
	PRIMARY KEY  (`oid`),
	UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

class vastuv6tt_avaldus extends class_base
{
	const AW_CLID = 337;

	function vastuv6tt_avaldus()
	{
		$this->init(array(
			"tpldir" => "vastuv6tt",
			"clid" => CL_VASTUV6TT_AVALDUS
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$avaldus = &$arr["obj_inst"];
		$retval = PROP_OK;
		// $eriala = $avaldus->prop("eriala");
		$vastuv6tt_keskkond = get_instance (CL_VASTUV6TT_KESKKOND);

		if (aw_global_get("vastuv6tt_oppetase"))
		{
			$oppetase = aw_global_get("vastuv6tt_oppetase");
		}
		else
		{
			error::raise(array(
				"msg" => t("&Otilde;ppetase määramata."),
				"fatal" => true,
				"show" => true,
			));
		}

		switch($data["name"])
		{
			case "eriala":
				$options = array_merge (array("0" => ""), $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase)));
				$data["options"] = $options;
				break;

			// case "eriala_b":
				// $options = array(
					// "0" => "",
					// "AG" => "Agronoomia",
					// "AI" => "Aiandus",
					// "PS" => "P&otilde;llumajandussaaduste tootmine ja turustamine",
					// "LK" => "Loomakasvatus",
					// "LP" => "Liha- ja piimatehnoloogia",
					// "KA" => "Kalakasvatus",
					// "AK" => "Agro&ouml;koloogia",
					// "VM" => "Veterinaarmeditsiin",
					// "AR" => "Maastikuarhitektuur",
					// "MH" => "Maastikukaitse ja -hooldus",
					// "KJ" => "Keskkonnamajandus",
					// "GE" => "Geodeesia",
					// "MK" => "Maakorraldus",
					// "EH" => "Maaehitus",
					// "VE" => "Veemajandus",
					// "KP" => "Kinnisvara planeerimine",
					// "EV" => "&Ouml;konoomika ja ettev&otilde;tlus",
					// "MF" => "Majandusarvestus ja finantsjuhtimine",
					// "ME" => "Metsamajandus",
					// "MT" => "Metsat&ouml;&ouml;stus",
					// "LV" => "Loodusvarade kasutamine ja kaitse",
					// "EG" => "Ergonoomika",
					// "EK" => "Energiakasutus",
					// "TH" => "P&otilde;llumajandustehnika",
					// "ET" => "Ettev&otilde;ttetehnika",
					// "RB" => "Rakendush&uuml;drobioloogia",
				// );
			// break;

			// case "eriala_m":
				// $options = array(
					// "0" => "",
					// "AI" => "Aiandus",
					// "GK" => "Agrokeemia",
					// "MV" => "Maaviljelus",
					// "RM" => "Rohumaaviljelus ja s&ouml;&ouml;datootmine",
					// "TK" => "Taimekaitse",
					// "TV" => "Taimekasvatus",
					// "MD" => "Mullateadus",
					// "KM" => "Kodumajandus",
					// "OP" => "&Otilde;petajakoolitus",
					// "AR" => "Maastikuarhitektuur",
					// "KK" => "Keskkonnakaitse",
					// "VM" => "Veterinaarmeditsiin",
					// "PT" => "Piimatehnoloogia",
					// "LT" => "Lihatehnoloogia",
					// "TG" => "Toiduh&uuml;gieen ja veterinaarkontroll",
					// "TI" => "Toiduteadus",
					// "LK" => "Loomakasvatus",
					// "KB" => "Keemiline bioloogia",
					// "ME" => "Metsamajandus",
					// "MT" => "Metsat&ouml;&ouml;stus",
					// "EH" => "Maaehitus",
					// "MM" => "Maam&otilde;&otilde;tmine",
					// "VE" => "Veemajandus",
					// "RP" => "Raamatupidamine ja rahandus",
					// "EV" => "&Ouml;konoomika ja ettev&otilde;tlus",
					// "TU" => "Turundus ja juhtimine",
					// "OV" => "&Ouml;konoomika ja ettev&otilde;tlus (1-aastane &otilde;pe)",
					// "MF" => "Majandusarvestus ja finantsjuhtimine",
					// "TH" => "P&otilde;llumajandustehnika",
					// "PE" => "P&otilde;llumajandusenergeetika",
					// "HB" => "H&uuml;drobioloogia",
					// "LG" => "Looma&ouml;koloogia",
					// "BM" => "Botaanika ja m&uuml;koloogia",
					// "TF" => "Taimef&uuml;sioloogia",
					// "GE" => "Geneetika",
					// "BK" => "Biokeemia",
				// );
			// break;

			case "oppevorm":
				switch ($oppetase)
				{
					case "B":
					case "A":
						$options = array(
							"R" => "Riigieelarveline",
							"L" => "Riigieelarvev&auml;line",
							"K" => "Kaug&otilde;pe",
						);
						break;

					case "M":
					case "O":
						$options = array(
							"R" => "Riigieelarveline",
							"L" => "Riigieelarvev&auml;line",
						);
						break;

					case "D":
						$options = array(
							"R" => "Riigieelarveline",
						);
						break;
				}

				$data["options"] = $options;
			break;

			case "eelistus":
				$data["options"] = array(
					"1" => "Jah",
					"0" => "Ei",
				);
			break;

		}

		// if ( ($data["name"] == "eriala_b") || ($data["name"] == "eriala_m") )
		// {
			// if ($eriala)
			// {
				// $options2 = array ();
				// foreach ($options as $key => $value)
				// {
					// if ($key != $eriala)
					// {
						// $options2[$key] = $value;
					// }
					// else
					// {
						// $name = $value;
					// }
				// }

				// $selected_option = array ($eriala => $name);
				// $options = $selected_option + $options2;
			// }

			// $data["options"] = $options;
		// }

		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$avaldus = &$arr["obj_inst"];

		switch($data["name"])
	    {
			case "eriala":
				// if ($arr["request"]["eriala_b"])
				// {
					// $data["value"] = $arr["request"]["eriala_b"];
				// }

				// if ($arr["request"]["eriala_m"])
				// {
					// $data["value"] = $arr["request"]["eriala_m"];
				// }
			break;
		}
		return $retval;
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	// CUSTOM FUNCTIONS

	/**

		@attrib name=konkursipunktid

		@param avaldus_id required
		@param sisseastuja_id required
		@param konkursipunktid_final optional

		@returns

	**/
	function konkursipunktid($arr)
	{
		$avaldus = obj($arr["avaldus_id"]);

		$sisseastuja = obj($arr["sisseastuja_id"]);
		$final = $arr["konkursipunktid_final"];
		$eriala = $avaldus->prop("eriala");
		$oppevorm = $avaldus->prop("oppevorm");
		$punkte = 0;
		$oppekeel = $sisseastuja->prop("oppekeel");

		// Hinded
		$keskhinne  = $sisseastuja->prop("keskhinne");
		$ex_kirjand_aasta  = $sisseastuja->prop("ex_kirjand_aasta");
		$ex_kirjand  = $sisseastuja->prop("ex_kirjand_hinne");
		$ex_ingl  = $sisseastuja->prop("ex_ingl_hinne");
		$ex_sks  = $sisseastuja->prop("ex_sks_hinne");
		$ex_pr  = $sisseastuja->prop("ex_pr_hinne");
		$ex_eesti  = $sisseastuja->prop("ex_eesti_hinne");
		$ex_vene  = $sisseastuja->prop("ex_vene_hinne");
		$ex_yhisk  = $sisseastuja->prop("ex_yhisk_hinne");
		$ex_ajalugu  = $sisseastuja->prop("ex_ajalugu_hinne");
		$ex_bio  = $sisseastuja->prop("ex_bio_hinne");
		$ex_fyysika  = $sisseastuja->prop("ex_fyysika_hinne");
		$ex_keemia  = $sisseastuja->prop("ex_keemia_hinne");
		$ex_mat = $sisseastuja->prop("ex_mat_hinne");
		$ex_geo = $sisseastuja->prop("ex_geo_hinne");

		$ex_EK = $sisseastuja->prop("tulemus_ek");
		$ex_KK = $sisseastuja->prop("tulemus_kk");
		$ex_VK = $sisseastuja->prop("tulemus_vk");
		$ex_VM = $sisseastuja->prop("tulemus_vm");
		// $ex_VL = $sisseastuja->prop("tulemus_vl");
		// $ex_VV = $sisseastuja->prop("tulemus_vv");
		// $ex_VR = $sisseastuja->prop("tulemus_vr");

		$ex_vaba1 = 0;
		$ex_vaba2 = 0;

		// mittebakalaureuse punktid
		if ($sisseastuja->prop("oppetase") != "B")
		{
			if (!$keskhinne)
			{
				if ($oppevorm == "R")
				{
					$retval = 0;
				}
			}
			else
			{
				$retval = $keskhinne;
			}
		}

		// Yldreeglid
		if (($oppekeel != "Eesti") && ($ex_EK < 60) && ($oppevorm == "R") && $final){ return 0; }
		if ($ex_kirjand_aasta > 2000){ $ex_kirjand = $ex_kirjand * 0.1; }

		// Vabalt valitud eksamid
		switch ($eriala)
		{
			case "GE":
			case "KP":
			case "EH":
			case "MK":
			case "VE":
				$ex = array ($ex_ingl, $ex_sks, $ex_pr, $ex_eesti, $ex_vene, $ex_yhisk, $ex_ajalugu, $ex_bio, $ex_fyysika, $ex_keemia, $ex_geo);
				sort ($ex);
				$value = array_pop ($ex);

				if (($value) && (is_numeric($value)))
				{
					$ex_vaba1 = $value;
				}
			break;

			case "EK":
			case "EG":
			case "ET":
			case "TH":
				$ex1 = array ($ex_mat, $ex_fyysika);
				sort ($ex1);
				$value = array_pop ($ex1);

				if (($value) && (is_numeric($value)))
				{
					$ex_vaba2 = $value;
				}

				$ex = array ($ex_ingl, $ex_sks, $ex_pr, $ex_eesti, $ex_vene, $ex_yhisk, $ex_ajalugu, $ex_bio, $ex_keemia, $ex_geo);
				$ex[] = $ex1[0];
				sort ($ex);
				$value = array_pop ($ex);

				if (($value) && (is_numeric($value)))
				{
					$ex_vaba1 = $value;
				}
			break;

			case "AR":
			case "MH":
			case "LP":
			case "AG":
			case "AI":
			case "PS":
			case "LK":
			case "KA":
			case "AK":
			case "EV":
			case "MF":
			case "KJ":
			case "ME":
			case "MT":
			case "LV":
				$ex = array ($ex_ingl, $ex_sks, $ex_pr, $ex_eesti, $ex_vene, $ex_yhisk, $ex_ajalugu, $ex_bio, $ex_fyysika, $ex_keemia, $ex_mat, $ex_geo);
				sort ($ex);
				$value = array_pop ($ex);

				if (($value) && (is_numeric($value)))
				{
					$ex_vaba1 = $value;
				}

				$value = array_pop ($ex);

				if (($value) && (is_numeric($value)))
				{
					$ex_vaba2 = $value;
				}
			break;
		}

		// Punktide arvestus erialati
		switch ($eriala)
		{
			case "AR":
				if (($ex_KK < 3) && ($oppevorm == "R") && $final){ return 0; }
				if (($ex_VK < 2) && ($oppevorm == "R") && $final){ return 0; }
				$punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_vaba2 * 0.1 + $keskhinne * 2 + $ex_KK + $ex_VK;
			break;

			case "MH":
				$punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_vaba2 * 0.1 + $keskhinne * 2 + $ex_VM;
			break;

			// case "LP":
				// $punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_vaba2 * 0.1 + $keskhinne * 2 + $ex_VL;
			// break;

			case "GE":
			case "KP":
			case "EH":
			case "MK":
			case "VE":
				$punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_mat * 0.2 + $keskhinne * 2;
			break;

			case "EK":
			case "EG":
			case "ET":
			case "TH":
				$punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_vaba2 * 0.2 + $keskhinne * 2;
			break;

			case "VM":
				$punkte = $ex_kirjand + $ex_bio * 0.2 + $ex_keemia * 0.2 + $keskhinne * 2;// + $ex_VV;
			break;

			case "RB":
				$punkte = $ex_kirjand + $ex_bio * 0.2 + $ex_keemia * 0.2 + $keskhinne * 2;// + $ex_VR;
			break;

			case "LP":
			case "AG":
			case "AI":
			case "PS":
			case "LK":
			case "KA":
			case "AK":
			case "EV":
			case "MF":
			case "KJ":
			case "ME":
			case "MT":
			case "LV":
				$punkte = $ex_kirjand + $ex_vaba1 * 0.1 + $ex_vaba2 * 0.1 + $keskhinne * 2;
			break;
		}

		if ($sisseastuja->prop("oppetase") == "B")
		{
			$retval = $punkte;
		}
		$avaldus->set_prop ("konkursipunktid", $retval);
		$avaldus->save ();
		return $retval;
	}




	function vajalikud_hinded($eriala, $oppetase = "B", $oppevorm = "R")
	{
		$vajalikud_hinded = array ();
		if ($oppetase == "D")
		{
			return $vajalikud_hinded;
		}

		if ($oppevorm == "R")
		{
			$vajalikud_hinded[] = array (
				"type" => "keskhinne",
				"name" => "Keskmine hinne",
				"error" => "Keskmine hinne arvutamata.<br>",
				"prop" => "keskhinne"
			);
		}

		if ($oppetase != "B")
		{
			return $vajalikud_hinded;
		}

		// Vabalt valitud eksamid
		switch ($eriala)
		{
			case "GE":
			case "KP":
			case "EH":
			case "MK":
			case "VE":
				$vaba = array ("ex_ingl", "ex_sks", "ex_pr", "ex_eesti", "ex_vene", "ex_yhisk", "ex_ajalugu", "ex_bio", "ex_fyysika", "ex_keemia", "ex_geo");
				$nimi = array ("Inglise keel", "Saksa keel", "Prantsuse keel", "Eesti keel teise keelena", "Vene keel", "&uuml;hiskonna&otilde;petus", "Ajalugu", "Bioloogia", "F&uuml;&uuml;sika", "Keemia", "Geograafia");
			break;

			case "EK":
			case "EG":
			case "ET":
			case "TH":
				$vaba = array ("ex_ingl", "ex_sks", "ex_pr", "ex_eesti", "ex_vene", "ex_yhisk", "ex_ajalugu", "ex_bio", "ex_keemia", "ex_geo");
				$nimi = array ("Inglise keel", "Saksa keel", "Prantsuse keel", "Eesti keel teise keelena", "Vene keel", "&uuml;hiskonna&otilde;petus", "Ajalugu", "Bioloogia", "Keemia", "Geograafia");
			break;

			case "AR":
			case "MH":
			case "LP":
			case "AG":
			case "AI":
			case "PS":
			case "LK":
			case "KA":
			case "AK":
			case "EV":
			case "MF":
			case "KJ":
			case "ME":
			case "MT":
			case "LV":
				$vaba = array ("ex_ingl", "ex_sks", "ex_pr", "ex_eesti", "ex_vene", "ex_yhisk", "ex_ajalugu", "ex_bio", "ex_fyysika", "ex_keemia", "ex_mat", "ex_geo");
				$nimi = array ("Inglise keel", "Saksa keel", "Prantsuse keel", "Eesti keel teise keelena", "Vene keel", "&uuml;hiskonna&otilde;petus", "Ajalugu", "Bioloogia", "F&uuml;&uuml;sika", "Keemia", "Matemaatika", "Geograafia");
			break;
		}

		// vajalikud eksamid
		if ($oppevorm == "R")
		{
			$vajalikud_hinded[] = array (
				"type" => "ex",
				"name" => "Kirjand",
				"error" => "Kirjandi tulemus puudub.<br>",
				"prop" => "ex_kirjand",
			);

			switch ($eriala)
			{
				case "GE":
				case "KP":
				case "EH":
				case "MK":
				case "VE":
					$vajalikud_hinded[] = array (
						"type" => "vaba",
						"name" => $nimi,
						"error" => "Vabalt valitud riigieksamitulemus puudub.<br>",
						"prop" => $vaba,
					);
					$vajalikud_hinded[] = array (
						"type" => "ex",
						"name" => "Matemaatika",
						"error" => "Matemaatika riigieksami tulemus puudub.<br>",
						"prop" => "ex_mat",
					);
				break;

				case "EK":
				case "EG":
				case "ET":
				case "TH":
					$vajalikud_hinded[] = array (
						"type" => "vaba",
						"name" => $nimi,
						"error" => "Vabalt valitud riigieksamitulemus puudub.<br>",
						"prop" => $vaba,
					);
					$vajalikud_hinded[] = array (
						"type" => "vaba",
						"name" => array ("Matemaatika", "F&uuml;&uuml;sika"),
						"error" => "Matemaatika v&otilde;i f&uuml;&uuml;sika riigieksami tulemus puudub.<br>",
						"prop" => array ("ex_mat", "ex_fyysika"),
					);
				break;

				case "VM":
				case "RB":
					$vajalikud_hinded[] = array (
						"type" => "ex",
						"name" => "Bioloogia",
						"error" => "Bioloogia riigieksami tulemus puudub.<br>",
						"prop" => "ex_bio",
					);
					$vajalikud_hinded[] = array (
						"type" => "ex",
						"name" => "Keemia",
						"error" => "Keemia riigieksami tulemus puudub.<br>",
						"prop" => "ex_keemia",
					);
				break;

				case "AR":
				case "MH":
				case "LP":
				case "AG":
				case "AI":
				case "PS":
				case "LK":
				case "KA":
				case "AK":
				case "EV":
				case "MF":
				case "KJ":
				case "ME":
				case "MT":
				case "LV":
					$vajalikud_hinded[] = array (
						"type" => "vaba",
						"name" => $nimi,
						"error" => "Esimene vabalt valitud riigieksamitulemus puudub.<br>",
						"prop" => $vaba,
					);

					$vajalikud_hinded[] = array (
						"type" => "vaba",
						"name" => $nimi,
						"error" => "Teine vabalt valitud riigieksamitulemus puudub.<br>",
						"prop" => $vaba,
					);
				break;
			}
		}

		return $vajalikud_hinded;
	}


	/**

		@attrib name=print

		@param sisseastuja_id required type=int
		@param avaldus_id required type=int

		@returns

	**/
	function prindi_t6end($arr)
	{
		// ...
		$sisseastuja = obj($arr["sisseastuja_id"]);
		$prinditav_avaldus = obj($arr["avaldus_id"]);
		$oppetase = $sisseastuja->prop("oppetase");
		$oppevorm = $prinditav_avaldus->prop("oppevorm");
		$eriala = $prinditav_avaldus->prop("eriala");
		$teine_eriala = NULL;
		$error = false;
		$mitu_avaldust = false;
		$vastuv6tt_keskkond = get_instance (CL_VASTUV6TT_KESKKOND);

		// switch ($oppetase)
		// {
			// case "B":
				// $oppetase_l = "b";
			// break;

			// case "M":
			// case "A":
			// case "D":
			// case "O":
				// $oppetase_l = "m";
			// break;
		// }

		foreach ($sisseastuja->connections_from(array ("type" => RELTYPE_AVALDUS)) as $connection)
		{
			$avaldus = $connection->to();

			if ( ($avaldus->prop("oppevorm") == $oppevorm) && ($avaldus->id() != $prinditav_avaldus->id()) )
			{
				$mitu_avaldust = true;
				$teine_eriala = $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase) . "_decline", $avaldus->prop("eriala"));

				if ($avaldus->prop("eelistus"))
				{
					$eelistus = $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase) . "_decline", $avaldus->prop("eriala"));
				}
				elseif ($prinditav_avaldus->prop("eelistus"))
				{
					$eelistus = $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase) . "_decline", $prinditav_avaldus->prop("eriala"));
				}
				else
				{
					$error .= "Eelistatav eriala valimata!<br>";
				}
			}
		}

		if ($mitu_avaldust)
		{
			$template = "avaldus_t6end_" . strtolower($oppetase) . ".html";
		}
		else
		{
			$template = "avaldus_t6end1_" . strtolower($oppetase) . ".html";
		}

		$vajalikud_hinded = $this->vajalikud_hinded($eriala, $oppetase, $oppevorm);
		$konkursipunktid = $this->konkursipunktid($arr);
		$riigieksamid = array ();


// tryki k6ik eksamid
		$eksamid = array (
			"ex_kirjand" => "Kirjand",
			"ex_ingl" => "Inglise keel",
			"ex_sks" => "Saksa keel",
			"ex_pr" => "Prantsuse keel",
			"ex_eesti" => "Eesti keel teise keelena",
			"ex_vene" => "Vene keel",
			"ex_yhisk" => "&uuml;hiskonna&otilde;petus",
			"ex_ajalugu" => "Ajalugu",
			"ex_bio" => "Bioloogia",
			"ex_fyysika" => "F&uuml;&uuml;sika",
			"ex_keemia" => "Keemia",
			"ex_mat" => "Matemaatika",
			"ex_geo" => "Geograafia",
		);

		foreach ($eksamid as $prop_name => $nimi)
		{
			if ($sisseastuja->prop($prop_name . "_hinne"))
			{
				$riigieksamid[$nimi] = $sisseastuja->prop($prop_name . "_hinne");
			}
		}
// END tryki k6ik eksamid

		foreach ($vajalikud_hinded as $key => $vajalik_hinne)
		{
			if ( ($vajalik_hinne["type"] == "ex") && (!($sisseastuja->prop($vajalik_hinne["prop"] . "_hinne"))) )
			{
				$error .= $vajalik_hinne["error"];
			}

			if (($vajalik_hinne["type"] == "keskhinne") && (!$sisseastuja->prop("keskhinne")))
			{
				$error .= $vajalik_hinne["error"];
			}

			if ($vajalik_hinne["type"] == "vaba")
			{
				foreach ($vajalik_hinne["prop"] as $idx => $prop)
				{
					if ($sisseastuja->prop($vajalik_hinne["prop"][$idx] . "_hinne"))
					{
						continue 2;
					}
				}

				$error .= $vajalik_hinne["error"];
			}
		}

		$sisseastuja_return_url = $this->mk_my_orb("change", array(
			"group" => "grp_sisseastuja_avaldused",
			"id" => $arr["sisseastuja_id"],
			), "vastuv6tt_sisseastuja"
		);


		// fill template
		$this->read_template($template);

		foreach ($riigieksamid as $eksam => $punkte)
		{
			$this->vars(array(
				"riigieksam_nimi" => $eksam,
				"riigieksam_punkte" => $punkte,
			));
			$riigieksamid_print .= $this->parse("riigieksamid");
		}

		$this->vars(array(
			"eesnimi" => $sisseastuja->prop("isik_firstname"),
			"perenimi" => $sisseastuja->prop("isik_lastname"),
			"isikukood" => $sisseastuja->prop("isik_personal_id"),
			"sisseastuja_nr" => $prinditav_avaldus->prop("eriala") . $prinditav_avaldus->prop("oppevorm") . $sisseastuja->prop("oppetase") . sprintf("%04d", $sisseastuja->prop("sisseastuja_nr")),
			"eriala" => $vastuv6tt_keskkond->get_trans ("eriala_" . strtolower($oppetase) . "_decline", $eriala),
			"eriala2" => $teine_eriala,
			"eelistus" => $eelistus,
			"oppevorm" => $vastuv6tt_keskkond->get_trans ("oppevorm_decline", $oppevorm),
			"keskhinne" => $sisseastuja->prop("keskhinne"),
			"viisi" => $sisseastuja->prop("kk_hinne_5"),
			"neljasid" => $sisseastuja->prop("kk_hinne_4"),
			"kolmesid" => $sisseastuja->prop("kk_hinne_3"),
			"kahtesid" => $sisseastuja->prop("kk_hinne_2"),
			"ak_viisi" => $sisseastuja->prop("ak_hinne_5"),
			"ak_neljasid" => $sisseastuja->prop("ak_hinne_4"),
			"ak_kolmesid" => $sisseastuja->prop("ak_hinne_3"),
			"ak_kahtesid" => $sisseastuja->prop("ak_hinne_2"),
			"ak_yhtesid" => $sisseastuja->prop("ak_hinne_1"),
			"ak_hinne_l6put88" => $sisseastuja->prop("ak_hinne_l6put88"),
			"hinded_a" => $sisseastuja->prop("ak_hinne_a"),
			"hinded_b" => $sisseastuja->prop("ak_hinne_b"),
			"hinded_c" => $sisseastuja->prop("ak_hinne_c"),
			"hinded_d" => $sisseastuja->prop("ak_hinne_d"),
			"hinded_e" => $sisseastuja->prop("ak_hinne_e"),
			"punktisumma" => round ($konkursipunktid, 3),
			"riigieksamid" => $riigieksamid_print,
			"date" => get_lc_date(time(), LC_DATE_FORMAT_LONG_FULLYEAR),
			"sisseastuja_return_url" => $sisseastuja_return_url,
		));

		if ($error)
		{
			return $error;
		}
		else
		{
			return $this->parse();
		}
	}
}

?>

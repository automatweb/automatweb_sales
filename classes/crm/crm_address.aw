<?php

// DEPRECATED CLASS.
// do not use.

// crm_address.aw - It's not really a physical address but a collection of data required to
// contact a person.
/*
	@classinfo relationmgr=yes
	@tableinfo kliendibaas_address index=oid master_table=objects master_index=oid

	@default table=objects
	@default group=general

	@property name type=text
	@caption Nimi

	@default table=kliendibaas_address

	@property aadress type=textbox size=50 maxlength=100
	@caption T&auml;nav/K&uuml;la

	@property aadress2 type=textbox size=50 maxlength=100
	@caption T&auml;nav/K&uuml;la2

	@property postiindeks type=textbox size=5 maxlength=100
	@caption Postiindeks

	@property linn type=relpicker reltype=RELTYPE_LINN automatic=1
	@caption Linn/Vald/Alev

	@property maakond type=relpicker reltype=RELTYPE_MAAKOND automatic=1
	@caption Maakond

	@property piirkond type=relpicker reltype=RELTYPE_PIIRKOND automatic=1
	@caption Piirkond

	@property riik type=relpicker reltype=RELTYPE_RIIK automatic=1
	@caption Riik

	@property comment type=textarea cols=65 rows=3 table=objects field=comment
	@caption Kommentaar

	@classinfo no_status=1
	@groupinfo settings caption=Seadistused
*/

/*

CREATE TABLE `kliendibaas_address` (
  `oid` int(11) NOT NULL default '0',
  `name` varchar(200) default NULL,
  `tyyp` int(11) default NULL,
  `riik` int(11) default NULL,
  `linn` int(11) default NULL,
  `piirkond` int(11) default NULL,
  `maakond` int(11) default NULL,
  `postiindeks` varchar(5) default NULL,
  `telefon` varchar(20) default NULL,
  `mobiil` varchar(20) default NULL,
  `faks` varchar(20) default NULL,
  `piipar` varchar(20) default NULL,
  `aadress` text,
  `e_mail` varchar(255) default NULL,
  `kodulehekylg` varchar(255) default NULL,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

/*
@reltype LINN value=1 clid=CL_CRM_CITY
@caption Linn

@reltype RIIK value=2 clid=CL_CRM_COUNTRY
@caption Riik

@reltype MAAKOND value=3 clid=CL_CRM_COUNTY
@caption Maakond

@reltype PIIRKOND value=4 clid=CL_CRM_AREA
@caption Piirkond

@reltype BELONGTO value=4 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Seosobjekt
*/

class crm_address extends class_base
{
	function crm_address()
	{
		$this->init(array(
			"tpldir" => "crm/address",
			"clid" => CL_CRM_ADDRESS,
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		switch($data["name"])
		{
			case "postiindeks":
				$oncl = "window.open('http://www.post.ee/?id=1069&op=sihtnumbriotsing&tanav='+document.changeform.aadress.value.replace(/[0-9]+/, '')+'&linn='+document.changeform.linn.options[document.changeform.linn.selectedIndex].text+'&x=30&y=6');";
				$data["post_append_text"] = sprintf(" <a href='#' onClick=\"$oncl\">%s</a>", t("Otsi postiindeksit"));

				break;

			case "linn":
			case "maakond":
			case "piirkond":
			case "riik":
				$pm_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
				if(is_oid($pm_inst->get_sysdefault()))
				{
					$clid = array(
						"linn" => CL_CRM_CITY,
						"maakond" => CL_CRM_COUNTY,
						"piirkond" => CL_CRM_AREA,
						"riik" => CL_CRM_COUNTRY
					);

					$data["options"] = array(0 => t("--vali--")) + safe_array($pm_inst->get_locations($clid[$data["name"]]));
				}
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$form = &$arr["request"];

		switch($data["name"])
		{
			case 'name':
				// generate a name for the object
				$name = array();
				if (!empty($form["aadress"]))
				{
					$name[] = $form['aadress'];
				}

				if (!empty($form["linn"]))
				{
					$city_obj = new object($form["linn"]);
					$name[] = $city_obj->name();
				}

				if (!empty($form["maakond"]))
				{
					$county_obj = new object($form["maakond"]);
					$name[] = $county_obj->name();
				}

				if (count($name) < 1)
				{
					if (!empty($form["email"]))
					{
						$name[] = $form["email"];
					};
				}

				if (count($name) < 1)
				{
					if (!empty($form["telefon"]))
					{
						$name[] = t('tel:').$form["telefon"];
					}
				}

				if (sizeof($name) > 0)
				{
					$arr["obj_inst"]->set_name(join(", ",$name));
				}
				$retval = PROP_IGNORE;
				break;
		}
		return $retval;
	}

	function request_execute($obj)
	{
		$this->read_template("show.tpl");
		$this->vars(array(
			"address" => $obj->prop("aadress"),
			"postiindeks" => $obj->prop("postiindeks"),
			"linn" => $this->_get_name_for_obj($obj->prop("linn")),
			"maakond" => $this->_get_name_for_obj($obj->prop("maakond")),
			"country" => $this->_get_name_for_obj($obj->prop("riik")),
		));
		return $this->parse();
	}

	function _get_name_for_obj($id)
	{
		if (empty($id))
		{
			$rv = "";
		}
		else
		{
			$obj = new object($id);
			$rv = $obj->name();
		}
		return $rv;
	}

	function callback_on_load($arr)
	{
		if ($arr["request"]["action"] === "new")
		{
			$o = obj();
			$o->set_parent($arr["request"]["parent"]);
			$o->set_class_id(CL_CRM_ADDRESS);
			$o->save();

			if ($this->can("view", $arr["request"]["alias_to"]))
			{
				$at = obj($arr["request"]["alias_to"]);
				$reltype = $arr["request"]["reltype"];

				$bt = $this->get_properties_by_type(array(
					"type" => array("relpicker","relmanager", "popup_search"),
					"clid" => $at->class_id(),
				));

				$symname = "";
				// figure out symbolic name for numeric reltype
				foreach($this->relinfo as $key => $val)
				{
					if (substr($key,0,7) == "RELTYPE")
					{
						if ($reltype == $val["value"])
						{
							$symname = $key;
						};
					};
				};

				// figure out which property to check
				foreach($bt as $item_key => $item)
				{
					// double check just in case
					if (!empty($symname) && ($item["type"] == "popup_search" || $item["type"] == "relpicker" || $item["type"] == "relmanager") && ($item["reltype"] == $symname))
					{
						$target_prop = $item_key;
					};
				};


				// now check, whether that property has a value. If not,
				// set it to point to the newly created connection
				if (!empty($symname) && !empty($target_prop))
				{
					$conns = $at->connections_from(array(
						"type" => $symname,
					));
					$conn_count = sizeof($conns);
				};

				// this is after the new connection has been made
				if ($target_prop != "" && ($conn_count == 1 || !$bt[$target_prop]["multiple"] ))
				{
					$at->set_prop($target_prop,$o->id());
					$at->save();
				}

				$at->connect(array(
					"to" => $o->id(),
					"type" => $arr["request"]["reltype"]
				));
			}
			header("Location: ".html::get_change_url($o->id(), array("return_url" => $arr["request"]["return_url"])));
			die();
		}
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'tyyp':
			case 'riik':
			case 'linn':
			case 'piirkond':
			case 'maakond':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
				return true;
			case 'name':
			case 'aadress2':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(200)'
				));
				return true;
			case 'postiindeks':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(5)'
				));
				return true;
			case 'telefon':
			case 'mobiil':
			case 'faks':
			case 'piipar':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(20)'
				));
				return true;
			case 'e_mail':
			case 'kodulehekylg':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
				return true;
			case 'aadress':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
				return true;
                }

		return false;
	}


	function get_name_from_adr($o)
	{
		$name = array();
		if ($o->prop("aadress") != "")
		{
			$name[] = $o->prop("aadress");
		}

		if ($this->can("view", $o->prop("linn")))
		{
			$city_obj = new object($o->prop("linn"));
			$name[] = $city_obj->name();
		};
		if ($this->can("view", $o->prop("maakond")))
		{
			$county_obj = new object($o->prop("maakond"));
			$name[] = $county_obj->name();
		};
		if ($this->can("view", $o->prop("riik")))
		{
			$name[] = $o->prop("riik.name");
		};

		return join(", ",$name);
	}

	function get_country_list()
	{
		$rv = array(
			"AD" => t("Andorra"),
			"AE" => t("United Arab Emirates"),
			"AF" => t("Afghanistan"),
			"AG" => t("Antigua and Barbuda"),
			"AI" => t("Anguilla"),
			"AL" => t("Albania"),
			"AM" => t("Armenia"),
			"AN" => t("Netherlands Antilles"),
			"AO" => t("Angola"),
			"AQ" => t("Antarctica"),
			"AR" => t("Argentina"),
			"AS" => t("American Samoa"),
			"AT" => t("Austria"),
			"AU" => t("Australia"),
			"AW" => t("Aruba"),
			"AZ" => t("Azerbaijan"),
			"BA" => t("Bosnia and Herzegovina"),
			"BB" => t("Barbados"),
			"BD" => t("Bangladesh"),
			"BE" => t("Belgium"),
			"BF" => t("Burkina Faso"),
			"BG" => t("Bulgaria"),
			"BH" => t("Bahrain"),
			"BI" => t("Burundi"),
			"BJ" => t("Benin"),
			"BM" => t("Bermuda"),
			"BN" => t("Brunei Darussalam"),
			"BO" => t("Bolivia"),
			"BR" => t("Brazil"),
			"BS" => t("Bahamas"),
			"BT" => t("Bhutan"),
			"BV" => t("Bouvet Island"),
			"BW" => t("Botswana"),
			"BY" => t("Belarus"),
			"BZ" => t("Belize"),
			"CA" => t("Canada"),
			"CC" => t("Cocos (Keeling) Islands"),
			"CF" => t("Central African Republic"),
			"CG" => t("Congo"),
			"CH" => t("Switzerland"),
			"CI" => t("Cote D'Ivoire (Ivory Coast)"),
			"CK" => t("Cook Islands"),
			"CL" => t("Chile"),
			"CM" => t("Cameroon"),
			"CN" => t("China"),
			"CO" => t("Colombia"),
			"CR" => t("Costa Rica"),
			"CS" => t("Czechoslovakia (former)"),
			"CU" => t("Cuba"),
			"CV" => t("Cape Verde"),
			"CX" => t("Christmas Island"),
			"CY" => t("Cyprus"),
			"CZ" => t("Czech Republic"),
			"DE" => t("Germany"),
			"DJ" => t("Djibouti"),
			"DK" => t("Denmark"),
			"DM" => t("Dominica"),
			"DO" => t("Dominican Republic"),
			"DZ" => t("Algeria"),
			"EC" => t("Ecuador"),
			"EE" => t("Estonia"),
			"EG" => t("Egypt"),
			"EH" => t("Western Sahara"),
			"ER" => t("Eritrea"),
			"ES" => t("Spain"),
			"ET" => t("Ethiopia"),
			"FI" => t("Finland"),
			"FJ" => t("Fiji"),
			"FK" => t("Falkland Islands (Malvinas)"),
			"FM" => t("Micronesia"),
			"FO" => t("Faroe Islands"),
			"FR" => t("France"),
			"FX" => t("France, Metropolitan"),
			"GA" => t("Gabon"),
			"GB" => t("Great Britain (UK)"),
			"GD" => t("Grenada"),
			"GE" => t("Georgia"),
			"GF" => t("French Guiana"),
			"GH" => t("Ghana"),
			"GI" => t("Gibraltar"),
			"GL" => t("Greenland"),
			"GM" => t("Gambia"),
			"GN" => t("Guinea"),
			"GP" => t("Guadeloupe"),
			"GQ" => t("Equatorial Guinea"),
			"GR" => t("Greece"),
			"GS" => t("S. Georgia and S. Sandwich Isls."),
			"GT" => t("Guatemala"),
			"GU" => t("Guam"),
			"GW" => t("Guinea-Bissau"),
			"GY" => t("Guyana"),
			"HK" => t("Hong Kong"),
			"HM" => t("Heard and McDonald Islands"),
			"HN" => t("Honduras"),
			"HR" => t("Croatia (Hrvatska)"),
			"HT" => t("Haiti"),
			"HU" => t("Hungary"),
			"ID" => t("Indonesia"),
			"IE" => t("Ireland"),
			"IL" => t("Israel"),
			"IN" => t("India"),
			"IO" => t("British Indian Ocean Territory"),
			"IQ" => t("Iraq"),
			"IR" => t("Iran"),
			"IS" => t("Iceland"),
			"IT" => t("Italy"),
			"JM" => t("Jamaica"),
			"JO" => t("Jordan"),
			"JP" => t("Japan"),
			"KE" => t("Kenya"),
			"KG" => t("Kyrgyzstan"),
			"KH" => t("Cambodia"),
			"KI" => t("Kiribati"),
			"KM" => t("Comoros"),
			"KN" => t("Saint Kitts and Nevis"),
			"KP" => t("Korea (North)"),
			"KR" => t("Korea (South)"),
			"KS" => t("Kosovo"),
			"KW" => t("Kuwait"),
			"KY" => t("Cayman Islands"),
			"KZ" => t("Kazakhstan"),
			"LA" => t("Laos"),
			"LB" => t("Lebanon"),
			"LC" => t("Saint Lucia"),
			"LI" => t("Liechtenstein"),
			"LK" => t("Sri Lanka"),
			"LR" => t("Liberia"),
			"LS" => t("Lesotho"),
			"LT" => t("Lithuania"),
			"LU" => t("Luxembourg"),
			"LV" => t("Latvia"),
			"LY" => t("Libya"),
			"MA" => t("Morocco"),
			"MC" => t("Monaco"),
			"MD" => t("Moldova"),
			"MG" => t("Madagascar"),
			"MH" => t("Marshall Islands"),
			"MK" => t("Macedonia"),
			"ML" => t("Mali"),
			"MM" => t("Myanmar"),
			"MN" => t("Mongolia"),
			"MO" => t("Macau"),
			"MP" => t("Northern Mariana Islands"),
			"MQ" => t("Martinique"),
			"MR" => t("Mauritania"),
			"MS" => t("Montserrat"),
			"MT" => t("Malta"),
			"MU" => t("Mauritius"),
			"MV" => t("Maldives"),
			"MW" => t("Malawi"),
			"MX" => t("Mexico"),
			"MY" => t("Malaysia"),
			"MZ" => t("Mozambique"),
			"NA" => t("Namibia"),
			"NC" => t("New Caledonia"),
			"NE" => t("Niger"),
			"NF" => t("Norfolk Island"),
			"NG" => t("Nigeria"),
			"NI" => t("Nicaragua"),
			"NL" => t("Netherlands"),
			"NO" => t("Norway"),
			"NP" => t("Nepal"),
			"NR" => t("Nauru"),
			"NT" => t("Neutral Zone"),
			"NU" => t("Niue"),
			"NZ" => t("New Zealand (Aotearoa)"),
			"OM" => t("Oman"),
			"PA" => t("Panama"),
			"PE" => t("Peru"),
			"PF" => t("French Polynesia"),
			"PG" => t("Papua New Guinea"),
			"PH" => t("Philippines"),
			"PK" => t("Pakistan"),
			"PL" => t("Poland"),
			"PM" => t("St. Pierre and Miquelon"),
			"PN" => t("Pitcairn"),
			"PR" => t("Puerto Rico"),
			"PT" => t("Portugal"),
			"PW" => t("Palau"),
			"PY" => t("Paraguay"),
			"QA" => t("Qatar"),
			"RE" => t("Reunion"),
			"RO" => t("Romania"),
			"RS" => t("Serbia"),
			"RU" => t("Russian Federation"),
			"RW" => t("Rwanda"),
			"SA" => t("Saudi Arabia"),
			"Sb" => t("Solomon Islands"),
			"SC" => t("Seychelles"),
			"SD" => t("Sudan"),
			"SE" => t("Sweden"),
			"SG" => t("Singapore"),
			"SH" => t("St. Helena"),
			"SI" => t("Slovenia"),
			"SJ" => t("Svalbard and Jan Mayen Islands"),
			"SK" => t("Slovak Republic"),
			"SL" => t("Sierra Leone"),
			"SM" => t("San Marino"),
			"SN" => t("Senegal"),
			"SO" => t("Somalia"),
			"SR" => t("Suriname"),
			"ST" => t("Sao Tome and Principe"),
			"SU" => t("USSR (former)"),
			"SV" => t("El Salvador"),
			"SY" => t("Syria"),
			"SZ" => t("Swaziland"),
			"TC" => t("Turks and Caicos Islands"),
			"TD" => t("Chad"),
			"TF" => t("French Southern Territories"),
			"TG" => t("Togo"),
			"TH" => t("Thailand"),
			"TJ" => t("Tajikistan"),
			"TK" => t("Tokelau"),
			"TM" => t("Turkmenistan"),
			"TN" => t("Tunisia"),
			"TO" => t("Tonga"),
			"TP" => t("East Timor"),
			"TR" => t("Turkey"),
			"TT" => t("Trinidad and Tobago"),
			"TV" => t("Tuvalu"),
			"TW" => t("Taiwan"),
			"TZ" => t("Tanzania"),
			"UA" => t("Ukraine"),
			"UG" => t("Uganda"),
			"UK" => t("United Kingdom"),
			"UM" => t("US Minor Outlying Islands"),
			"US" => t("United States"),
			"UY" => t("Uruguay"),
			"UZ" => t("Uzbekistan"),
			"VA" => t("Vatican City State (Holy See)"),
			"VC" => t("Saint Vincent and the Grenadines"),
			"VE" => t("Venezuela"),
			"VG" => t("Virgin Islands (British)"),
			"VI" => t("Virgin Islands (U.S.)"),
			"VN" => t("Viet Nam"),
			"VU" => t("Vanuatu"),
			"WF" => t("Wallis and Futuna Islands"),
			"WS" => t("Samoa"),
			"YE" => t("Yemen"),
			"YT" => t("Mayotte"),
			"YU" => t("Yugoslavia"),
			"ZA" => t("South Africa"),
			"ZM" => t("Zambia"),
			"ZR" => t("Zaire"),
			"ZW" => t("Zimbabwe"),
		);
		asort($rv);

		// go over list - if they are all the same as untrans, return untrans
		// if not, then filter out the ones, that are untranslated
		$has_trans = false;
		foreach($this->get_untrans_country_list() as $k => $v)
		{
			if (t2($v) !== null)
			{
				$has_trans = true;
				break;
			}
		}

		if (!$has_trans)
		{
			return $rv;
		}

		$tmp = array();
		foreach($this->get_untrans_country_list() as $k => $v)
		{
			$str = t2($v);
			if ($str !== null)
			{
				$tmp[$k] = $str;
			}
		}
		asort($tmp);
		return $tmp;
	}

	function get_untrans_country_list()
	{
		$rv = array(
			"AD" => ("Andorra"),
			"AE" => ("United Arab Emirates"),
			"AF" => ("Afghanistan"),
			"AG" => ("Antigua and Barbuda"),
			"AI" => ("Anguilla"),
			"AL" => ("Albania"),
			"AM" => ("Armenia"),
			"AN" => ("Netherlands Antilles"),
			"AO" => ("Angola"),
			"AQ" => ("Antarctica"),
			"AR" => ("Argentina"),
			"AS" => ("American Samoa"),
			"AT" => ("Austria"),
			"AU" => ("Australia"),
			"AW" => ("Aruba"),
			"AZ" => ("Azerbaijan"),
			"BA" => ("Bosnia and Herzegovina"),
			"BB" => ("Barbados"),
			"BD" => ("Bangladesh"),
			"BE" => ("Belgium"),
			"BF" => ("Burkina Faso"),
			"BG" => ("Bulgaria"),
			"BH" => ("Bahrain"),
			"BI" => ("Burundi"),
			"BJ" => ("Benin"),
			"BM" => ("Bermuda"),
			"BN" => ("Brunei Darussalam"),
			"BO" => ("Bolivia"),
			"BR" => ("Brazil"),
			"BS" => ("Bahamas"),
			"BT" => ("Bhutan"),
			"BV" => ("Bouvet Island"),
			"BW" => ("Botswana"),
			"BY" => ("Belarus"),
			"BZ" => ("Belize"),
			"CA" => ("Canada"),
			"CC" => ("Cocos (Keeling) Islands"),
			"CF" => ("Central African Republic"),
			"CG" => ("Congo"),
			"CH" => ("Switzerland"),
			"CI" => ("Cote D'Ivoire (Ivory Coast)"),
			"CK" => ("Cook Islands"),
			"CL" => ("Chile"),
			"CM" => ("Cameroon"),
			"CN" => ("China"),
			"CO" => ("Colombia"),
			"CR" => ("Costa Rica"),
			"CS" => ("Czechoslovakia (former)"),
			"CU" => ("Cuba"),
			"CV" => ("Cape Verde"),
			"CX" => ("Christmas Island"),
			"CY" => ("Cyprus"),
			"CZ" => ("Czech Republic"),
			"DE" => ("Germany"),
			"DJ" => ("Djibouti"),
			"DK" => ("Denmark"),
			"DM" => ("Dominica"),
			"DO" => ("Dominican Republic"),
			"DZ" => ("Algeria"),
			"EC" => ("Ecuador"),
			"EE" => ("Estonia"),
			"EG" => ("Egypt"),
			"EH" => ("Western Sahara"),
			"ER" => ("Eritrea"),
			"ES" => ("Spain"),
			"ET" => ("Ethiopia"),
			"FI" => ("Finland"),
			"FJ" => ("Fiji"),
			"FK" => ("Falkland Islands (Malvinas)"),
			"FM" => ("Micronesia"),
			"FO" => ("Faroe Islands"),
			"FR" => ("France"),
			"FX" => ("France, Metropolitan"),
			"GA" => ("Gabon"),
			"GB" => ("Great Britain (UK)"),
			"GD" => ("Grenada"),
			"GE" => ("Georgia"),
			"GF" => ("French Guiana"),
			"GH" => ("Ghana"),
			"GI" => ("Gibraltar"),
			"GL" => ("Greenland"),
			"GM" => ("Gambia"),
			"GN" => ("Guinea"),
			"GP" => ("Guadeloupe"),
			"GQ" => ("Equatorial Guinea"),
			"GR" => ("Greece"),
			"GS" => ("S. Georgia and S. Sandwich Isls."),
			"GT" => ("Guatemala"),
			"GU" => ("Guam"),
			"GW" => ("Guinea-Bissau"),
			"GY" => ("Guyana"),
			"HK" => ("Hong Kong"),
			"HM" => ("Heard and McDonald Islands"),
			"HN" => ("Honduras"),
			"HR" => ("Croatia (Hrvatska)"),
			"HT" => ("Haiti"),
			"HU" => ("Hungary"),
			"ID" => ("Indonesia"),
			"IE" => ("Ireland"),
			"IL" => ("Israel"),
			"IN" => ("India"),
			"IO" => ("British Indian Ocean Territory"),
			"IQ" => ("Iraq"),
			"IR" => ("Iran"),
			"IS" => ("Iceland"),
			"IT" => ("Italy"),
			"JM" => ("Jamaica"),
			"JO" => ("Jordan"),
			"JP" => ("Japan"),
			"KE" => ("Kenya"),
			"KG" => ("Kyrgyzstan"),
			"KH" => ("Cambodia"),
			"KI" => ("Kiribati"),
			"KM" => ("Comoros"),
			"KN" => ("Saint Kitts and Nevis"),
			"KP" => ("Korea (North)"),
			"KR" => ("Korea (South)"),
			"KW" => ("Kuwait"),
			"KY" => ("Cayman Islands"),
			"KZ" => ("Kazakhstan"),
			"LA" => ("Laos"),
			"LB" => ("Lebanon"),
			"LC" => ("Saint Lucia"),
			"LI" => ("Liechtenstein"),
			"LK" => ("Sri Lanka"),
			"LR" => ("Liberia"),
			"LS" => ("Lesotho"),
			"LT" => ("Lithuania"),
			"LU" => ("Luxembourg"),
			"LV" => ("Latvia"),
			"LY" => ("Libya"),
			"MA" => ("Morocco"),
			"MC" => ("Monaco"),
			"MD" => ("Moldova"),
			"MG" => ("Madagascar"),
			"MH" => ("Marshall Islands"),
			"MK" => ("Macedonia"),
			"ML" => ("Mali"),
			"MM" => ("Myanmar"),
			"MN" => ("Mongolia"),
			"MO" => ("Macau"),
			"MP" => ("Northern Mariana Islands"),
			"MQ" => ("Martinique"),
			"MR" => ("Mauritania"),
			"MS" => ("Montserrat"),
			"MT" => ("Malta"),
			"MU" => ("Mauritius"),
			"MV" => ("Maldives"),
			"MW" => ("Malawi"),
			"MX" => ("Mexico"),
			"MY" => ("Malaysia"),
			"MZ" => ("Mozambique"),
			"NA" => ("Namibia"),
			"NC" => ("New Caledonia"),
			"NE" => ("Niger"),
			"NF" => ("Norfolk Island"),
			"NG" => ("Nigeria"),
			"NI" => ("Nicaragua"),
			"NL" => ("Netherlands"),
			"NO" => ("Norway"),
			"NP" => ("Nepal"),
			"NR" => ("Nauru"),
			"NT" => ("Neutral Zone"),
			"NU" => ("Niue"),
			"NZ" => ("New Zealand (Aotearoa)"),
			"OM" => ("Oman"),
			"PA" => ("Panama"),
			"PE" => ("Peru"),
			"PF" => ("French Polynesia"),
			"PG" => ("Papua New Guinea"),
			"PH" => ("Philippines"),
			"PK" => ("Pakistan"),
			"PL" => ("Poland"),
			"PM" => ("St. Pierre and Miquelon"),
			"PN" => ("Pitcairn"),
			"PR" => ("Puerto Rico"),
			"PT" => ("Portugal"),
			"PW" => ("Palau"),
			"PY" => ("Paraguay"),
			"QA" => ("Qatar"),
			"RE" => ("Reunion"),
			"RO" => ("Romania"),
			"RU" => ("Russian Federation"),
			"RW" => ("Rwanda"),
			"SA" => ("Saudi Arabia"),
			"Sb" => ("Solomon Islands"),
			"SC" => ("Seychelles"),
			"SD" => ("Sudan"),
			"SE" => ("Sweden"),
			"SG" => ("Singapore"),
			"SH" => ("St. Helena"),
			"SI" => ("Slovenia"),
			"SJ" => ("Svalbard and Jan Mayen Islands"),
			"SK" => ("Slovak Republic"),
			"SL" => ("Sierra Leone"),
			"SM" => ("San Marino"),
			"SN" => ("Senegal"),
			"SO" => ("Somalia"),
			"SR" => ("Suriname"),
			"ST" => ("Sao Tome and Principe"),
			"SU" => ("USSR (former)"),
			"SV" => ("El Salvador"),
			"SY" => ("Syria"),
			"SZ" => ("Swaziland"),
			"TC" => ("Turks and Caicos Islands"),
			"TD" => ("Chad"),
			"TF" => ("French Southern Territories"),
			"TG" => ("Togo"),
			"TH" => ("Thailand"),
			"TJ" => ("Tajikistan"),
			"TK" => ("Tokelau"),
			"TM" => ("Turkmenistan"),
			"TN" => ("Tunisia"),
			"TO" => ("Tonga"),
			"TP" => ("East Timor"),
			"TR" => ("Turkey"),
			"TT" => ("Trinidad and Tobago"),
			"TV" => ("Tuvalu"),
			"TW" => ("Taiwan"),
			"TZ" => ("Tanzania"),
			"UA" => ("Ukraine"),
			"UG" => ("Uganda"),
			"UK" => ("United Kingdom"),
			"UM" => ("US Minor Outlying Islands"),
			"US" => ("United States"),
			"UY" => ("Uruguay"),
			"UZ" => ("Uzbekistan"),
			"VA" => ("Vatican City State (Holy See)"),
			"VC" => ("Saint Vincent and the Grenadines"),
			"VE" => ("Venezuela"),
			"VG" => ("Virgin Islands (British)"),
			"VI" => ("Virgin Islands (U.S.)"),
			"VN" => ("Viet Nam"),
			"VU" => ("Vanuatu"),
			"WF" => ("Wallis and Futuna Islands"),
			"WS" => ("Samoa"),
			"YE" => ("Yemen"),
			"YT" => ("Mayotte"),
			"YU" => ("Yugoslavia"),
			"ZA" => ("South Africa"),
			"ZM" => ("Zambia"),
			"ZR" => ("Zaire"),
			"ZW" => ("Zimbabwe"),
		);
		asort($rv);
		return $rv;
	}


	/** returns oid, country object id with given $code
		@attrib api=1
		@param code required type=string
			Country code
		@param parent optional type=oid
			if set, makes a new country object if no results
		@param use_ex optional type=bool
			uses existing object with the same name
		@comment return country object id with the given county code. If use_ex is set, searches everywhere
	**/
	function get_country_by_code($code, $parent,$use_ex = null)
	{
		$countries = $this->get_country_list();
		if(isset($countries[$code]))
		{
			$name = $countries[$code];
		}
		else
		{
			return null;
		}

		$filter = array("lang_id" => array(), "class_id" => CL_CRM_COUNTRY, "name" => $name);
		if(!$use_ex)
		{
			$filter["parent"] = $parent;
		}
		$o_l = new object_list($filter);
		if(!sizeof($o_l->arr()))
		{
			if(!is_oid($parent))
			{
				return null;
			}
			$o = new object();
			$o->set_class_id(CL_CRM_COUNTRY);
			$o->set_parent($parent);
			$o->set_name($name);
			$o->save();
		}
		else
		{
			$o = reset($o_l->arr());
		}
		return $o->id();
	}

	/** returns string, country code
		@attrib api=1
		@param o required type=object/oid
			Country object
	**/
	function get_country_code($o)
	{
		if(is_oid($o) && $this->can("view" ,$o))
		{
			$o = obj($o);
		}
		if(!is_object($o))
		{
			return "";
		}
		$countries = $this->get_country_list();
		return array_search($o->name(), $countries);
	}

	function get_phone_ext_list_as_js_array()
	{
		$a_list = $this->get_phone_ext_list();

		$a_list_count = count($a_list);
		$s_out = "a_phone_prefixes = {";
		foreach ($a_list as $key => $val)
		{
			$s_out .= '"'.$key.'" : "'.$a_list[$key].'",'."\n";
		}
		$s_out = substr($s_out, 0, strlen($s_out)-2);
		$s_out .= "};";

		return $s_out;
	}

	function get_phone_ext_list()
	{
		$rv = array(
			"AD" => "+376",
			"AE" => "+971",
			"AF" => "+93",
			"AG" => "+1-268",
			"AI" => "+1-264",
			"AL" => "+355",
			"AM" => "+374",
			"AN" => "+599",
			"AO" => "+244",
			"AQ" => "+672",
			"AR" => "+54",
			"AS" => "+1-684",
			"AT" => "+43",
			"AU" => "+61",
			"AW" => "+297",
			"AZ" => "+994",
			"BA" => "+387",
			"BB" => "+1-246",
			"BD" => "+880",
			"BE" => "+32",
			"BF" => "+226",
			"BG" => "+359",
			"BH" => "+973",
			"BI" => "+257",
			"BJ" => "+229",
			"BM" => "+1-441",
			"BN" => "+673",
			"BO" => "+591",
			"BR" => "+55",
			"BS" => "+1-242",
			"BT" => "+975",
			"BV" => "",
			"BW" => "+267",
			"BY" => "+375",
			"BZ" => "+501",
			"CA" => "+1",
			"CC" => "+61",
			"CF" => "+236",
			"CG" => "+242",
			"CH" => "+41",
			"CI" => "+225",
			"CK" => "+682",
			"CL" => "+56",
			"CM" => "+237",
			"CN" => "+86",
			"CO" => "+57",
			"CR" => "+506",
			"CS" => "+420",
			"CU" => "+53",
			"CV" => "+238",
			"CX" => "+61-8",
			"CY" => "+357",
			"CZ" => "+420",
			"DE" => "+49",
			"DJ" => "+253",
			"DK" => "+45",
			"DM" => "+1-767",
			"DO" => "+1-809",
			"DZ" => "+213",
			"EC" => "+593",
			"EE" => "+372",
			"EG" => "+20",
			"EH" => "",
			"ER" => "+291",
			"ES" => "+34",
			"ET" => "+251",
			"FI" => "+358",
			"FJ" => "+679",
			"FK" => "+500",
			"FM" => "+691",
			"FO" => "+298",
			"FR" => "+33",
			"FX" => "",
			"GA" => "+241",
			"GB" => "+44",
			"GD" => "+1-473",
			"GE" => "+995",
			"GF" => "+594",
			"GH" => "+233",
			"GI" => "+350",
			"GL" => "+299",
			"GM" => "+220",
			"GN" => "+240",
			"GP" => "+590",
			"GQ" => "+240",
			"GR" => "+30",
			"GS" => "",
			"GT" => "+502",
			"GU" => "+1-671",
			"GW" => "+245",
			"GY" => "+592",
			"HK" => "+852",
			"HM" => "",
			"HN" => "+504",
			"HR" => "+385",
			"HT" => "+509",
			"HU" => "+36",
			"ID" => "+62",
			"IE" => "+353",
			"IL" => "+972",
			"IN" => "+91",
			"IO" => "",
			"IQ" => "+964",
			"IR" => "+98",
			"IS" => "+354",
			"IT" => "+39",
			"JM" => "+1-876",
			"JO" => "+962",
			"JP" => "+81",
			"KE" => "+254",
			"KG" => "+996",
			"KH" => "+855",
			"KI" => "+686",
			"KM" => "+269",
			"KN" => "+1-869",
			"KP" => "+850",
			"KR" => "+82",
			"KW" => "+965",
			"KY" => "+1-345",
			"KZ" => "+7",
			"LA" => "+856",
			"LB" => "+961",
			"LC" => "+1-758",
			"LI" => "+423",
			"LK" => "+94",
			"LR" => "+231",
			"LS" => "+266",
			"LT" => "+370",
			"LU" => "+352",
			"LV" => "+371",
			"LY" => "+218",
			"MA" => "+212",
			"MC" => "+377",
			"MD" => "+373",
			"MG" => "+261",
			"MH" => "+692",
			"MK" => "+389",
			"ML" => "+223",
			"MM" => "+95",
			"MN" => "+976",
			"MO" => "+853",
			"MP" => "+1-670",
			"MQ" => "+596",
			"MR" => "+222",
			"MS" => "+1-664",
			"MT" => "+356",
			"MU" => "+230",
			"MV" => "+960",
			"MW" => "+265",
			"MX" => "+52",
			"MY" => "+60",
			"MZ" => "+258",
			"NA" => "+264",
			"NC" => "+687",
			"NE" => "+227",
			"NF" => "+672",
			"NG" => "+234",
			"NI" => "+505",
			"NL" => "+31",
			"NO" => "+47",
			"NP" => "+977",
			"NR" => "+674",
			"NT" => "",
			"NU" => "+683",
			"NZ" => "+64",
			"OM" => "+968",
			"PA" => "+507",
			"PE" => "+51",
			"PF" => "+689",
			"PG" => "+675",
			"PH" => "+63",
			"PK" => "+92",
			"PL" => "+48",
			"PM" => "+508",
			"PN" => "",
			"PR" => "+1-787",
			"PT" => "+351",
			"PW" => "+680",
			"PY" => "+595",
			"QA" => "+974",
			"RE" => "+262",
			"RO" => "+40",
			"RU" => "+7",
			"RW" => "+250",
			"SA" => "+966",
			"Sb" => "+677",
			"SC" => "+248",
			"SD" => "+249",
			"SE" => "+46",
			"SG" => "+65",
			"SH" => "+290",
			"SI" => "+386",
			"SJ" => "",
			"SK" => "+421",
			"SL" => "+232",
			"SM" => "+378",
			"SN" => "+221",
			"SO" => "+252",
			"SR" => "+597",
			"ST" => "+239",
			"SU" => "",
			"SV" => "+503",
			"SY" => "+963",
			"SZ" => "+268",
			"TC" => "+1-649",
			"TD" => "+235",
			"TF" => "",
			"TG" => "+228",
			"TH" => "+66",
			"TJ" => "+992",
			"TK" => "+690",
			"TM" => "+993",
			"TN" => "+216",
			"TO" => "+676",
			"TP" => "+670",
			"TR" => "+90",
			"TT" => "+1-868",
			"TV" => "+688",
			"TW" => "+886",
			"TZ" => "+255",
			"UA" => "+380",
			"UG" => "+256",
			"UK" => "+44",
			"UM" => "",
			"US" => "+1",
			"UY" => "+598",
			"UZ" => "+998",
			"VA" => "+39",
			"VC" => "+1-784",
			"VE" => "+58",
			"VG" => "+1-284",
			"VI" => "+1-340",
			"VN" => "+84",
			"VU" => "+678",
			"WF" => "+681",
			"WS" => "+685",
			"YE" => "+967",
			"YT" => "+269",
			"YU" => "",
			"ZA" => "+27",
			"ZM" => "+260",
			"ZR" => "+243",
			"ZW" => "+263",
		);
		asort($rv);
		return $rv;
	}
}

<?php

class eesti_ehitusturg_obj extends _int_object
{
	const CLID = 1686;

	public function __construct($objdata = array())
	{
		parent::__construct($objdata);
		$this->check_db();
	}

	public function import()
	{
		$i = $this->instance();
		$i->read_template("import.tpl");

		$i->vars(array(
			"url" => "http://eesti-ehitusturg.ee/",
			"save_url" => $i->mk_my_orb("import_save", array("id" => $this->id())),
			"load_html_url" => $i->mk_my_orb("load_html", array("id" => $this->id())),
			"companies" => json_encode($this->get_companies()),
		));

		return $i->parse();
	}

	public function get_html()
	{
		$url = new aw_uri(automatweb::$request->arg_isset("url") ? automatweb::$request->arg("url") : "http://eesti-ehitusturg.ee/");
		$html = $this->handle_html(file_get_contents($url));

		return $html;
	}

	public function save_sectors($new_sectors)
	{
		$sectors = $this->get_sectors();

		foreach($new_sectors as $sector)
		{
			if (!is_array($sector))
			{
				continue;
			}

			if(!isset($sectors[$sector["id"]]) or $sectors[$sector["id"]]["name"] !== $sector["name"])
			{
				$this->save_sector($sector);
			}
		}
	}

	public function save_companies($new_companies)
	{
		$companies = $this->get_companies();

		foreach($new_companies as $company)
		{
			if (!is_array($company))
			{
				continue;
			}

			if(
				!isset($companies[$company["id"]]) or 
				count($company) > 2 and (
					$companies[$company["id"]]["name"] !== $company["name"])
				)
			{
				$this->save_company($company);
			}
		}
	}

	public function wget_companies_html()
	{
		$dir = aw_ini_get("site_basedir")."/files/eesti_ehitusturg/companies_html";
		exec(sprintf("mkdir %s/files/eesti_ehitusturg", aw_ini_get("site_basedir")));
		exec(sprintf("mkdir %s/files/eesti_ehitusturg/companies_html", aw_ini_get("site_basedir")));
		exec(sprintf("mkdir %s/files/eesti_ehitusturg/trash", aw_ini_get("site_basedir")));
		exec(sprintf("mkdir %s/files/eesti_ehitusturg/trash/companies_html", aw_ini_get("site_basedir")));
		foreach($this->get_companies() as $company)
		{
			if (!file_exists($dir."/".$company["id"]))
			{
				exec(sprintf("mkdir %s/%u/; cd %s/%u/; wget \"http://eesti-ehitusturg.ee/index.php?leht=9&rn=%u\"", $dir, $company["id"], $dir, $company["id"], $company["id"], $dir, $company["id"]));
				exec(sprintf("mkdir %s/%u/; cd %s/%u/; wget \"http://eesti-ehitusturg.ee/majandus.php?rn=%u\"", $dir, $company["id"], $dir, $company["id"], $company["id"], $dir, $company["id"]));
				exec(sprintf("mkdir %s/%u/; cd %s/%u/; wget \"http://eesti-ehitusturg.ee/omanik.php?rn=%u\"", $dir, $company["id"], $dir, $company["id"], $company["id"], $dir, $company["id"]));
			}
		}
	}

	public function parse_companies_html()
	{
		$dir = aw_ini_get("site_basedir")."/files/eesti_ehitusturg/companies_html";
		$dir_trash = aw_ini_get("site_basedir")."files/eesti_ehitusturg/trash/companies_html";

		if (false !== ($handle = opendir($dir)))
		{
			while (false !== ($company_dir = readdir($handle)))
			{
				if ($company_dir !== "." and $company_dir !== ".." and is_dir($dir."/".$company_dir))
				{
					$company_id = (int)$company_dir;
					$company_handle = opendir($dir."/".$company_dir);
					while(false !== ($company_file = readdir($company_handle)))
					{
						if ($company_file !== "." and $company_file !== ".." and !is_dir($dir."/".$company_dir."/".$company_file))
						{
							$html = file_get_contents($dir."/".$company_dir."/".$company_file);
							if(strpos($company_file, "index.php") !== false)
							{
								$company = $this->parse_company_details($company_id, $html);
								$this->save_company($company);
							}
							elseif(strpos($company_file, "omanik.php") !== false)
							{
								$owners = $this->parse_company_owners($company_id, $html);
								$this->save_owners($company_id, $owners);
							}
							elseif(strpos($company_file, "majandus.php") !== false)
							{
								$revenues = $this->parse_company_revenue($company_id, $html);
								$this->save_revenue($company_id, $revenues);
							}
						}
					}
					exec("mv ".$dir."/".$company_dir." ".$dir_trash."/".$company_dir);
//					exec("rm -rf ".$dir."/".$company_dir);
				}
			}
			closedir($handle);
		}
	}

	protected function parse_company_details($id, $html)
	{
		$company = array("id" => $id);
		$orig_html = $html;
		// Trim the HTML!
		$html = substr($html, strpos($html, "</form>"));

		// Get name:
		//	<td><h1>Kalev Grupp AS</h1></td>
		$i = strpos($html, "<td><h1>") + 8;
		$company["name"] = trim(substr($html, $i, strpos($html, "</h1>", $i) - $i));

		// Get logo:
		//	<td align="right"><img src="Image/firmad/thumb/Paroc_transparent.gif" border="0" width="183" height="38"></td>
		if (false !== strpos(substr($html, 0, strpos($html, "</table>")), "<img"))
		{
			$i = strpos($html, "<img src=\"") + 10;
			$company["logo_url"] = "http://eesti-ehitusturg.ee/" . trim(substr($html, $i, strpos($html, "\"", $i) - $i));
			$company["logo_name"] = basename($company["logo_url"]);
		}

		// Get the director and his/her profession:
		//	<td width="100%" valign="top"><B>Oliver Kruuda</B> <BR>juhatuse esimees</td>
		if(false !== ($j = strpos($html, "juht:")))
		{
			$i = strpos($html, "<B>", $j) + 3;
			$company["director_name"] = trim(substr($html, $i, strpos($html, "</B>", $i) - $i));
			$i = strpos($html, "<BR>", $i) + 4;
			$company["director_profession"] = trim(substr($html, $i, strpos($html, "</td>", $i) - $i));
		}

		// Get the address:
		//	<td width="100%" valign="top">Järvevana tee  3a&nbsp;&nbsp;<BR> 11314&nbsp; Tallinn<BR></td>
		if(false !== ($j = strpos($html, "Aadress:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$address = substr($html, $i, strpos($html, "</td>", $i) - $i);
			$company["address_street"] = substr($address, 0, strpos($address, "&nbsp;&nbsp;"));
			$company["address_street"] = trim(str_replace("  ", " ", trim($company["address_street"])));
			$i = strpos($address, "<BR>") + 4;
			$company["address_postal_code"] = trim(substr($address, $i, strpos($address, "&nbsp;", $i) - $i));
			$i = strpos($address, "&nbsp;", $i) + 6;
			$company["address_city"] = trim(substr($address, $i, strpos($address, "<BR>", $i) - $i));
		}


		// Get the registry number:
		//	<td width="100%" valign="top"><span style='color:green;'>10000952</span>
		if(false !== ($j = strpos($html, "Registrikood:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["regnr"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get the KMKNR:
		if(false !== ($j = strpos($html, "KMKNR:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["kmknr"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get established date as  timestamp:
		if(false !== ($j = strpos($html, "Asutatud:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["established"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
			$company["established"] = $this->date_to_timestamp($company["established"]);
		}

		// Get 1st phone:
		if(false !== ($j = strpos($html, "Telefon:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["phone"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get 2nd phone:
		if(false !== ($j = strpos($html, "Telefon 2:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["phone2"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get fax:
		if(false !== ($j = strpos($html, "Faks:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["fax"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get e-mail:
		if(false !== ($j = strpos($html, "E-mail:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["email"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get web:
		if(false !== ($j = strpos($html, "Koduleht:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["web"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get sector:
		if(false !== ($j = strpos($html, "Tegevusala:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["sector"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
			$company["sector"] = substr($company["sector"], 0, strpos($company["sector"], " "));
		}

		// Get EMTAK:
		if(false !== ($j = strpos($html, "EMTAK:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$emtak = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
			$company["emtak_id"] = trim(substr($emtak, 0, strpos($emtak, "-")));
			$company["emtak_name"] = trim(substr($emtak, strpos($emtak, "-") + 1));
		}

		// Get additionnal info:
		if(false !== ($j = strpos($html, "Lisainfo:")))
		{
			$i = strpos($html, "=\"top\">", $j) + 7;
			$company["info"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "</td>", $i) - $i))));
		}

		// Get view count, created and modified:
		$i = strpos($html, "Ankeeti vaadatud", $j) + strlen("Ankeeti vaadatud");
		$company["view_count"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, "korda", $i) - $i))));
		
		$i = strpos($html, "korda alates", $j) + strlen("korda alates");
		$company["created"] = trim(str_replace("&nbsp;", "", strip_tags(substr($html, $i, strpos($html, ".<BR>", $i) - $i))));
		$company["created"] = $this->date_to_timestamp($company["created"]);

		return $company;
	}

	public function import_companies_details()
	{
		$currency = obj($this->prop("report_currency", array(), currency_obj::CLID));

		$sectors = $this->import_emtak_sectors();
		$this->persons = array();
		$professions = array();

		$companies = $this->get_companies(true, true, 15);
		$owners = $this->get_owners(array_keys($companies));
		$revenues = $this->get_revenues(array_keys($companies));

		$count = 0;
		foreach($companies as $company)
		{
			if (empty($company["aw_id"]))
			{
				if(!isset($sectors[$company["emtak_id"]]))
				{
					print "Parent not found!<br />";
					continue;
				}

				$o = obj(null, array(), crm_company_obj::CLID);
				$o->set_name($company["name"]);
				$o->set_parent($sectors[$company["emtak_id"]]);
				if (!empty($company["info"]))
				{
					$o->set_comment($company["info"]);
				}
				if (!empty($company["regnr"]))
				{
					$o->set_prop("reg_nr", $company["regnr"]);
				}
				if (!empty($company["kmknr"]))
				{
					$o->set_prop("tax_nr", $company["kmknr"]);
				}
				if (!empty($company["established"]))
				{
					$o->set_prop("year_founded", $company["established"]);
				}

				// TODO: Ettev6tlusvormi peaks nimest v2lja yritama parsida.
//				$o->set_prop("ettevotlusvorm", $company[""]);

				if (!empty($company["web"]))
				{
					$o->set_prop("fake_url", $company["web"]);
				}
				if (!empty($company["email"]))
				{
					$o->set_prop("fake_email", $company["email"]);
				}
				if (!empty($company["phone"]))
				{
					$o->set_prop("fake_phone", $company["phone"]);
				}
				if (!empty($company["phone2"]))
				{
					$o->set_prop("fake_mobile", $company["phone2"]);
				}
				if (!empty($company["fax"]))
				{
					$o->set_prop("fake_fax", $company["fax"]);
				}
				if (!empty($company["address_street"]))
				{
					$o->set_prop("fake_address_address", $company["address_street"]);
				}
				if (!empty($company["address_postal_code"]))
				{
					$o->set_prop("fake_address_postal_code", $company["address_postal_code"]);
				}
				if (!empty($company["address_city"]))
				{
					$o->set_prop("fake_address_city", $company["address_city"]);
				}

				$o->set_meta("eesti_ehitus_views", $company["view_count"]);
				

				$aw_id = $o->save();
				
				$person = $this->get_person($company["director_name"], $aw_id);

				$o->set_prop("firmajuht", $person->id());
				$o->save();

				if(isset($professions[$company["director_profession"]]))
				{
					$profession = $professions[$company["director_profession"]];
				}
				else
				{
					$profession = $o->add_profession(null, ucfirst($company["director_profession"]));
					$professions[$company["director_profession"]] = $profession;
				}
				try
				{
					$o->add_employee($profession, $person);
				}
				catch (awex_redundant_instruction $e)
				{
				}

				// Owners
				if(isset($owners[$company["id"]]) and is_array($owners[$company["id"]]))
				{
					foreach($owners[$company["id"]] as $owner)
					{
						$person = $this->get_person($owner["name"], $aw_id);

						try
						{
							$o->add_owner($person, $owner["share"]);
						}
						catch (awex_redundant_instruction $e)
						{
						}
					}
				}

				// Annual reports
				if(isset($revenues[$company["id"]]) and is_array($revenues[$company["id"]]))
				{
					foreach($revenues[$company["id"]] as $revenue)
					{
						$o->add_annual_report($currency, $revenue["year"], array(
							"value_added_tax" => $revenue["kaibemaks"],
							"social_security_tax" => $revenue["sotsmaks"],
							"assets" => $revenue["varad"],
							"turnover" => $revenue["aritulu"],
							"profit" => $revenue["puhaskasum"],
							"employees" => $revenue["tootajaid"],
							"turnover_per_employee" => $revenue["tootaja_kaive"],
						));
					}
				}

				$this->instance()->db_query("UPDATE aw_eesti_ehitusturg_raw_companies SET aw_id = {$aw_id} WHERE external_id = {$company["id"]};");
				
				if($count++ > 10)
				{
					die("<script type=\"text/javascript\">window.location.reload();</script>");
				}
			}
		}
	}

	protected function get_person($name, $parent)
	{
		if(isset($this->persons[$name]))
		{
			$person = $this->persons[$name];
		}
		else
		{
			$person = obj(null, array(), crm_person_obj::CLID);
			$person->set_parent($parent);
			$person->set_prop("firstname", substr($name, 0, strrpos($name, " ")));
			$person->set_prop("lastname", substr($name, strrpos($name, " ") + 1));
			$person->set_name($name);
			$person->save();

			$this->persons[$name] = $person;
		}

		return $person;
	}

	public function import_emtak_sectors()
	{
		$aw_oids = array();

		$odl = new object_data_list(
			array(
				"class_id" => crm_sector_obj::CLID,
				"parent" => $this->prop("sectors_dir"),
			),
			array(
				crm_sector_obj::CLID => array("emtak_2008")
			)
		);

		foreach($odl->arr() as $oid => $odata)
		{
			$aw_oids[$odata["emtak_2008"]] = $oid;
		}

		$rows = $this->instance()->db_fetch_array("SELECT DISTINCT(emtak_id), emtak_name FROM aw_eesti_ehitusturg_raw_companies WHERE emtak_id IS NOT NULL;");
		$sectors = array();

		foreach($rows as $row)
		{
			$aw_id = isset($aw_oids[$row["emtak_id"]]) ? $aw_oids[$row["emtak_id"]] : null;
			if (empty($aw_oids[$row["emtak_id"]]))
			{
				$o = obj(null, array(), crm_sector_obj::CLID);
				$o->set_parent($this->prop("sectors_dir"));
				$o->set_name($row["emtak_name"]);
				$o->set_prop("tegevusala", $row["emtak_name"]);
				$o->set_prop("emtak_2008", $row["emtak_id"]);
				$aw_id = $o->save();

				$row["emtak_name"] = self::addslashes($row["emtak_name"]);

				$this->instance()->db_query("INSERT INTO aw_eesti_ehitusturg_raw_emtak(external_id, aw_id, name)
				VALUES ('{$row["emtak_id"]}', {$aw_id}, '{$row["emtak_name"]}')
				ON DUPLICATE KEY UPDATE aw_id = {$aw_id}, name = '{$row["emtak_name"]}'
				");
			}
			$sectors[$row["emtak_id"]] = $aw_id;
		}

		return $sectors;
	}

	protected function parse_company_owners($id, $html)
	{
		$owners = array();

		$html = substr($html, strpos($html, "Osaluse"));
		$end = strpos($html, "</table>");

		$i = strpos($html, "<tr>") + 4;
		while ($i < $end)
		{
			$row = substr($html, $i, strpos($html, "</tr>", $i) - $i);

			$j = strpos($row, "<td>") + 4;
			$k = strrpos($row, "<td>") + 4;
			$owners[] = array(
				"id" => $id,
				"name" => trim(str_replace("&nbsp;", "", substr($row, $j, strpos($row, "</td>") - $j))),
				"share" => aw_math_calc::string2float(trim(str_replace("&nbsp;", "", substr($row, $k, strrpos($row, "</td>") - $k)))),
			);
			$i = strpos($html, "<tr>", $i) + 4;
		}

		return $owners;
	}

	protected function parse_company_revenue($id, $html)
	{
		$revenues = array();

		$html = substr($html, strpos($html, "bordercolor=\"#eeeeee\""));
		$end = strpos($html, "</table>");

		$i = strpos($html, "<tr>") + 4;
		//	Skip the caption row!
		$i = strpos($html, "<tr>", $i) + 4;

		// Loop through rows:
		while ($i < $end)
		{
			$revenue = array("id" => $id);

			$row = substr($html, $i, strpos($html, "</tr>", $i) - $i);

			$j = strpos($row, "<td");
			while($j !== false)
			{
				$revenue[] = aw_math_calc::string2float(trim(str_replace("&nbsp;", "", strip_tags(substr($row, $j, strpos($row, "</td>", $j) - $j)))));

				$j = strpos($row, "<td", $j + 3);
			}
			$revenues[] = $revenue;

			$i = strpos($html, "<tr>", $i) + 4;
		}

		return $revenues;
	}

	protected function save_company($company)
	{
		$company = self::addslashes($company);
		!isset($company["parent"]) ? $company["parent"] = "NULL" : "";

		$SET = array(
			"name" => "str",
			"director_name" => "str",
			"director_profession" => "str",
			"address_street" => "str",
			"address_postal_code" => "str",
			"address_city" => "str",
			"regnr" => "str",
			"kmknr" => "str",
			"established" => "int",
			"phone" => "str",
			"phone2" => "str",
			"fax" => "str",
			"email" => "str",
			"web" => "str",
			"sector" => "int",
			"emtak_id" => "str",
			"emtak_name" => "str",
			"info" => "str",
			"created" => "int",
			"view_count" => "int",
		);
		foreach($SET as $key => $type)
		{
			if(isset($company[$key]))
			{
				$val = $company[$key];
				if($type === "arr")
				{
					$val = ",".join(",", $company[$key]).",";
				}
				$tpl = $type === "int" ? "%s = %d" : "%s = '%s'";
				$SET[$key] = sprintf($tpl, $key, $val);
			}
			else
			{
				unset($SET[$key]);
			}
		}
		$SET = count($SET) > 0 ? join(", ", $SET) : "";

		$this->instance()->db_query("INSERT INTO aw_eesti_ehitusturg_raw_companies SET 
			external_id = {$company["id"]}, {$SET}
		ON DUPLICATE KEY UPDATE {$SET};");
	}

	protected function save_owners($id, $owners)
	{
		$this->instance()->db_query(sprintf("DELETE FROM aw_eesti_ehitusturg_raw_owners WHERE company_id = %u", $id));
		foreach($owners as $owner)
		{
			$this->instance()->db_query(sprintf("INSERT INTO aw_eesti_ehitusturg_raw_owners (company_id, name, share)
			VALUES (%u, '%s', %f)", $id, self::addslashes($owner["name"]), $owner["share"]));
		}
	}

	protected function save_revenue($id, $revenues)
	{
		$this->instance()->db_query(sprintf("DELETE FROM aw_eesti_ehitusturg_raw_revenue WHERE company_id = %u", $id));
		foreach($revenues as $revenue)
		{
			$this->instance()->db_query(sprintf("INSERT INTO aw_eesti_ehitusturg_raw_revenue
			(company_id, year, kaibemaks, sotsmaks, varad, aritulu, puhaskasum, tootajaid, tootaja_kaive)
			VALUES (%u, %u, %f, %f, %f, %f, %f, %u, %f)",
				$id,
				$revenue[0],
				$revenue[1] * 1000,
				$revenue[2] * 1000,
				$revenue[3] * 1000,
				$revenue[4] * 1000,
				$revenue[5] * 1000,
				$revenue[6],
				$revenue[7]
			));
		}
	}

	protected function save_sector($sector)
	{
		$sector = self::addslashes($sector);
		!isset($sector["parent"]) ? $sector["parent"] = "NULL" : "";

		$this->instance()->db_query("INSERT INTO aw_eesti_ehitusturg_raw_sectors (external_id, name, external_parent)
		VALUES ({$sector["id"]}, '{$sector["name"]}', {$sector["parent"]})
		ON DUPLICATE KEY UPDATE name = '{{$sector["name"]}', external_parent = {$sector["parent"]};");
	}

	protected function get_companies($aw_id_is_null = false, $name_not_null = false, $limit = null)
	{
		$companies = array();

		$sql = "SELECT * FROM aw_eesti_ehitusturg_raw_companies";
		$where = array();
		if ($aw_id_is_null)
		{
			$where[] = "aw_id IS NULL";
		}
		if ($name_not_null)
		{
			$where[] = "name IS NOT NULL";
		}
		if (count($where) > 0)
		{
			$sql .= " WHERE ".join(" AND ", $where);
		}
		if ($limit !== null)
		{
			$sql .= " LIMIT {$limit}";
		}

		$rows = $this->instance()->db_fetch_array($sql);
		foreach($rows as $row)
		{
			$row["id"] = $row["external_id"];
			unset($row["external_id"]);
			$companies[$row["id"]] = $row;
		}

		return $companies;
	}

	protected function get_owners($companies = null)
	{
		$owners = array();

		$sql = "SELECT * FROM aw_eesti_ehitusturg_raw_owners";
		if ($companies !== null)
		{
			$sql .= " WHERE company_id IN (" . explode(",", $companies) . ")";
		}

		$rows = $this->instance()->db_fetch_array($sql);
		foreach($rows as $row)
		{
			$owners[$row["company_id"]] = isset($owners[$row["company_id"]]) ? $owners[$row["company_id"]] : array();
			$owners[$row["company_id"]][] = $row;
		}

		return $owners;
	}

	protected function get_revenues($companies = null)
	{
		$revenues = array();

		$sql = "SELECT * FROM aw_eesti_ehitusturg_raw_revenue";
		if ($companies !== null)
		{
			$sql .= " WHERE company_id IN (" . implode(",", $companies) . ")";
		}

		$rows = $this->instance()->db_fetch_array($sql);
		foreach($rows as $row)
		{
			$revenues[$row["company_id"]] = isset($revenues[$row["company_id"]]) ? $revenues[$row["company_id"]] : array();
			$revenues[$row["company_id"]][] = $row;
		}

		return $revenues;
	}

	protected function get_sectors()
	{
		$sectors = array();

		$rows = $this->instance()->db_fetch_array("SELECT * FROM aw_eesti_ehitusturg_raw_sectors;");
		foreach($rows as $row)
		{
			$sectors[$row["external_id"]] = $row;
		}

		return $sectors;
	}

	protected function get_javascript_tag()
	{
		$i = $this->instance();
		$i->read_template("import.js");

		$script = $i->parse();

		return sprintf("<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>\n</script><script type=\"text/javascript\">\n%s\n</script>\n", $script);
	}

	protected function handle_html($html)
	{
		$html = str_ireplace("src=\"", "src=\"http://eesti-ehitusturg.ee//", $html);
		$html = str_replace("href=\"style.css\"", "href=\"".$this->handle_url("style.css")."\"", $html);
		$html = str_replace("background=\"", "background=\"".$this->prop("url")."/", $html);

		return $html;
	}

	protected function handle_url($url)
	{
		return substr($url, 0, 7) == "http://" ? $url : "http://eesti-ehitusturg.ee//".$url;
	}

	protected function date_to_timestamp($date)
	{
		$date = explode(".", $date);
		return mktime(0, 0, 0, max($date[1], 1), max($date[0], 1), $date[2]);
	}

	protected function check_db()
	{
		if (count($this->instance()->db_fetch_array("SHOW TABLES LIKE 'aw_eesti_ehitusturg_raw_sectors'")) === 0)
		{
			$this->instance()->db_query("CREATE TABLE aw_eesti_ehitusturg_raw_sectors(
				external_id INT PRIMARY KEY,
				aw_id INT UNIQUE,
				name VARCHAR(100) NOT NULL,
				external_parent INT
			)");
		}

		if (count($this->instance()->db_fetch_array("SHOW TABLES LIKE 'aw_eesti_ehitusturg_raw_emtak'")) === 0)
		{
			$this->instance()->db_query("CREATE TABLE aw_eesti_ehitusturg_raw_emtak(
				external_id INT PRIMARY KEY,
				aw_id INT UNIQUE,
				name VARCHAR(100) NOT NULL,
				external_parent INT
			)");
		}
		
		if (count($this->instance()->db_fetch_array("SHOW TABLES LIKE 'aw_eesti_ehitusturg_raw_companies'")) === 0)
		{
			$this->instance()->db_query("CREATE TABLE aw_eesti_ehitusturg_raw_companies(
				external_id INT PRIMARY KEY,
				aw_id INT UNIQUE,
				name VARCHAR(100),
				director_name VARCHAR(100),
				director_profession VARCHAR(100),
				address_street VARCHAR(200),
				address_postal_code VARCHAR(200),
				address_city VARCHAR(200),
				regnr VARCHAR(20) UNIQUE,
				kmknr VARCHAR(20) UNIQUE,
				established INT,
				phone VARCHAR(40),
				phone2 VARCHAR(40),
				fax VARCHAR(40),
				email VARCHAR(100),
				web VARCHAR(250),
				sector INT,
				emtak_id VARCHAR(40),
				emtak_name VARCHAR(200),
				info TEXT,
				view_count INT,
				created INT
			)");
		}

		if (count($this->instance()->db_fetch_array("SHOW TABLES LIKE 'aw_eesti_ehitusturg_raw_owners'")) === 0)
		{
			$this->instance()->db_query("CREATE TABLE aw_eesti_ehitusturg_raw_owners(
				company_id INT NOT NULL,
				name  VARCHAR(100) NOT NULL,
				share DECIMAL(10,4) NOT NULL
			)");
		}

		if (count($this->instance()->db_fetch_array("SHOW TABLES LIKE 'aw_eesti_ehitusturg_raw_revenue'")) === 0)
		{
			$this->instance()->db_query("CREATE TABLE aw_eesti_ehitusturg_raw_revenue(
				company_id INT NOT NULL,
				year INT NOT NULL,
				kaibemaks DECIMAL(15, 2),
				sotsmaks DECIMAL(15, 2),
				varad DECIMAL(15, 2) NOT NULL,
				aritulu DECIMAL(15, 2) NOT NULL,
				puhaskasum DECIMAL(15, 2) NOT NULL,
				tootajaid INT NOT NULL,
				tootaja_kaive DECIMAL(15, 2) NOT NULL
			)");
		}
	}

	protected static function addslashes($val)
	{
		if (is_array($val))
		{
			foreach($val as $k => $v)
			{
				$val[$k] = self::addslashes($v);
			}
			return $val;
		}
		else
		{
			return str_replace("'", "\'", strip_tags($val));
		}
	}
}

?>

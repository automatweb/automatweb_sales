<?php

class eesti_ehitusturg_obj extends _int_object
{
	const CLID = 1686;

	public function import()
	{
		$this->check_db();

		$i = $this->instance();
		$i->read_template("import.tpl");

		$i->vars(array(
			"url" => $this->prop("url"),
			"save_url" => $i->mk_my_orb("import_save", array("id" => $this->id())),
			"load_html_url" => $i->mk_my_orb("load_html", array("id" => $this->id())),
			"companies" => json_encode($this->get_companies()),
		));

		return $i->parse();
	}

	public function get_html()
	{
		$url = new aw_uri(automatweb::$request->arg_isset("url") ? automatweb::$request->arg("url") : $this->prop("url"));
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
				count(array_diff($companies[$company["id"]]["sectors"], $company["sectors"])) > 0 or 
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
		exec("mkdir %s/files/eesti_ehitusturg", aw_ini_get("site_basedir"));
		exec("mkdir %s/files/eesti_ehitusturg/companies_html", aw_ini_get("site_basedir"));
		foreach($this->get_companies() as $company)
		{
			exec(sprintf("mkdir %s/%u/; cd %s/%u/; wget http://eesti-ehitusturg.ee/index.php?leht=9&rn=%u", $dir, $company["id"], $dir, $company["id"], $company["id"], $dir, $company["id"]));
		}
	}

	public function parse_companies_html()
	{
		$dir = aw_ini_get("site_basedir")."/files/eesti_ehitusturg/companies_html";
	}

	protected function save_company($company)
	{
		$company = self::addslashes($company);
		!isset($company["parent"]) ? $company["parent"] = "NULL" : "";

		$SET = array(
			"name" => "str",
			"director" => "str",
			"address" => "str",
			"regnr" => "str",
			"kmknr" => "str",
			"established" => "int",
			"established_str" => "int",
			"phone" => "str",
			"phone2" => "str",
			"fax" => "str",
			"email" => "str",
			"web" => "str",
			"sectors" => "arr",
			"emtak" => "str",
			"info" => "str",
			"modified" => "int",
			"extra" => "str",
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
				$tpl = $key === "int" ? "%s = %d" : "%s = '%s'";
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

	protected function save_sector($sector)
	{
		$sector = self::addslashes($sector);
		!isset($sector["parent"]) ? $sector["parent"] = "NULL" : "";

		$this->instance()->db_query("INSERT INTO aw_eesti_ehitusturg_raw_sectors (external_id, name, external_parent)
		VALUES ({$sector["id"]}, '{$sector["name"]}', {$sector["parent"]})
		ON DUPLICATE KEY UPDATE name = '{{$sector["name"]}', external_parent = {$sector["parent"]};");
	}

	protected function get_companies()
	{
		$companies = array();

		$rows = $this->instance()->db_fetch_array("SELECT * FROM aw_eesti_ehitusturg_raw_companies LIMIT 0, 3;");
		foreach($rows as $row)
		{
			$row["sectors"] = explode(",", trim($row["sectors"], ","));
			$row["id"] = $row["external_id"];
			unset($row["external_id"]);
			$companies[] = $row;
		}

		return $companies;
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
		$html = str_ireplace("src=\"", "src=\"".$this->prop("url")."/", $html);
		$html = str_replace("href=\"style.css\"", "href=\"".$this->handle_url("style.css")."\"", $html);
		$html = str_replace("background=\"", "background=\"".$this->prop("url")."/", $html);

		return $html;
	}

	protected function handle_url($url)
	{
		return substr($url, 0, 7) == "http://" ? $url : $this->prop("url")."/".$url;
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
				director VARCHAR(100),
				address VARCHAR(200),
				regnr VARCHAR(20) UNIQUE,
				kmknr VARCHAR(20) UNIQUE,
				established INT,
				phone VARCHAR(40),
				phone2 VARCHAR(40),
				fax VARCHAR(40),
				email VARCHAR(40),
				web VARCHAR(40),
				sectors VARCHAR(250),
				emtak VARCHAR(40),
				info TEXT,
				modified INT
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

<?php
/*
@classinfo syslog_type=ST_EESTI_EHITUSTURG relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_eesti_ehitusturg master_index=brother_of master_table=objects index=aw_oid

@default table=aw_eesti_ehitusturg
@default group=general

@property sectors_dir type=objpicker clid=CL_MENU field=aw_sectors_dir
@caption Tegevusalade kaust

@property report_currency type=objpicker clid=CL_CURRENCY field=aw_report_currency
@caption Majandusaruannete valuuta

@property import_sectors type=text store=no
@caption Impordi tegevusalad ja organisatsioonide IDd

@property import_companies_html type=text store=no
@caption Impordi organisatsioonide HTML

@property parse_companies_details type=text store=no
@caption Parsi imporditud HTMLst organisatsioonide andmed

@property import_companies_details type=text store=no
@caption Impordi parsitud andmed AW objektideks

*/

class eesti_ehitusturg extends class_base
{
	public function eesti_ehitusturg()
	{
		$this->init(array(
			"tpldir" => "import/eesti_ehitusturg",
			"clid" => eesti_ehitusturg_obj::CLID
		));
	}

	public function _get_url(&$arr)
	{
		if(empty($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = "http://eesti-ehitusturg.ee/index.php?leht=9";
		}

		return PROP_OK;
	}

	public function _get_import_sectors(&$arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("K&auml;ivita import"),
			"url" => $this->mk_my_orb("import", array("id" => automatweb::$request->arg("id"))),
		));

		return PROP_OK;
	}

	public function _get_import_companies_html(&$arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("K&auml;ivita import"),
			"url" => $this->mk_my_orb("wget_companies_html", array("id" => automatweb::$request->arg("id"))),
		));

		return PROP_OK;
	}

	public function _get_parse_companies_details(&$arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("K&auml;ivita import"),
			"url" => $this->mk_my_orb("parse_companies_html", array("id" => automatweb::$request->arg("id"))),
		));

		return PROP_OK;
	}

	public function _get_import_companies_details(&$arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("K&auml;ivita import"),
			"url" => $this->mk_my_orb("import_companies_details", array("id" => automatweb::$request->arg("id"))),
		));

		return PROP_OK;
	}

	/**
		@attrib name=import all_args=1
		@param id required type=int
	**/
	public function import($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);
		$html = $o->import();
		die($html);
	}

	/**
		@attrib name=wget_companies_html all_args=1
		@param id required type=int
	**/
	public function wget_companies_html($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);
		$o->wget_companies_html();
		die("DONE!");
	}

	/**
		@attrib name=parse_companies_html all_args=1
		@param id required type=int
	**/
	public function parse_companies_html($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);
		$o->parse_companies_html();
		die("DONE!");
	}

	/**
		@attrib name=import_companies_details all_args=1
		@param id required type=int
	**/
	public function import_companies_details($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);
		$o->import_companies_details();
		die("DONE!");
	}

	/**
		@attrib name=load_html all_args=1
		@param id required type=int
	**/
	public function load_html($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);
		$html = $o->get_html();
		die($html);
	}

	/**
		@attrib name=import_save all_args=1
		@param id required type=int
		@param sectors optional type=array
		@param companies optional type=array
	**/
	public function import_save($arr)
	{
		$o = new object($arr["id"], array(), eesti_ehitusturg_obj::CLID);

		if(!empty($arr["sectors"]))
		{
			$o->save_sectors($arr["sectors"]);
		}

		if(!empty($arr["companies"]))
		{
			$o->save_companies($arr["companies"]);
		}

		die("SUCCESS");
	}

	public function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_eesti_ehitusturg(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_report_currency":
			case "aw_sectors_dir":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int default 0"
				));
				return true;
		}
	}
}

?>

<?php
// euro_patent_et_desc.aw - Patent
/*

@classinfo syslog_type=ST_EURO_PATENT_ET_DESC relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@extends applications/clients/patent_office/intellectual_property


@default group=general
	@property applicant type=relpicker reltype=RELTYPE_APPLICANT
	@caption Patendiomanik

	@property signed type=text store=no editonly=1
	@caption Allkirja staatus

	@property signatures type=text store=no editonly=1
	@caption Allkirjastajad

	@property procurator type=relpicker reltype=RELTYPE_PROCURATOR
	@caption Volinik

	@property verified type=checkbox
	@caption Kinnitatud

	@property exported type=checkbox caption=no
	@caption Eksporditud

	@property export_date type=date_select
	@caption Ekspordi kuup&auml;ev

	@property nr type=textbox
	@caption Taotluse number


@groupinfo invention caption="Leiutis"
@default group=invention
	@property epat_nr type=textbox
	@caption Euroopa patendi number

	@property epat_date type=date_select year_from=2002
	@caption Euroopa patenditaotluse esitamise kuup&auml;ev

	@property invention_name_et type=textbox
	@caption Leiutise nimetus (eesti keeles)

 	@property epat_desc_trans type=fileupload reltype=RELTYPE_EPAT_DESC_TRANS form=+emb
	@caption Patendikirjelduse t&otilde;lge

 	@property epat_desc_trans_appl type=fileupload reltype=RELTYPE_EPAT_DESC_TRANS_APPL form=+emb
	@caption Patendikirjelduse t&otilde;lke avaldamise sooviavaldus

 	@property epat_other_file type=fileupload reltype=RELTYPE_EPAT_OTHER_FILE form=+emb
	@caption Muu

@default group=fee
 	@property add_fee type=textbox size=4
	@caption T&auml;iendav l&otilde;iv


// RELTYPES
@reltype EPAT_DESC_TRANS value=100 clid=CL_FILE
@caption Patendikirjelduse t6lge

@reltype EPAT_DESC_TRANS_APPL value=201 clid=CL_FILE
@caption Patendikirjelduse t6lke avaldamise sooviavaldus

@reltype EPAT_OTHER_FILE value=202 clid=CL_FILE
@caption Muu

*/

class euro_patent_et_desc extends intellectual_property
{
	public static $level_index = array(
		0 => 0,
		1 => 12,
		2 => 4,
		3 => 5
	);

	function __construct()
	{
		parent::__construct();
		$this->init(array(
			"tpldir" => "applications/patent",
			"clid" => CL_EURO_PATENT_ET_DESC
		));
		$this->info_levels = array(
			0 => "owner",
			12 => "invention_epat",
			4 => "fee_epat",
			5 => "check_epat"
		);
		$this->pdf_file_name = "EuroopaPatendiT6lkeTaotlus";
		$this->show_template = "show_epat.tpl";
		$this->show_sent_template = "show_sent_epat.tpl";
		$this->date_vars = array_merge($this->date_vars, array("epat_date"));
		$this->file_upload_vars = array_merge($this->file_upload_vars, array("epat_desc_trans", "epat_desc_trans_appl", "epat_other_file"));
		$this->text_vars = array_merge($this->text_vars, array("invention_name_et", "epat_nr"));
		$this->save_fee_vars = array_merge($this->save_fee_vars, array("add_fee"));

		//siia panev miskid muutujad mille iga ringi peal 2ra kustutab... et uuele taotlejale vana info ei j22ks
		$this->datafromobj_del_vars = array("name_value" , "email_value" , "phone_value" , "fax_value" , "code_value" ,"email_value" , "street_value" ,"index_value" ,"country_code_value","city_value","county_value","correspond_street_value", "correspond_index_value" , "correspond_country_code_value" , "correspond_city_value","correspond_county_value", "name");
		$this->datafromobj_vars = array_merge($this->datafromobj_vars, array("invention_name_et", "epat_date", "epat_desc_trans", "epat_desc_trans_appl", "epat_other_file", "epat_nr", "add_fee"));
	}

	protected function save_forms($patent)
	{
		$this->save_applicants($patent);
		$this->save_fee($patent);
		$this->save_invention($patent);
		$this->fileupload_save($patent);
		$this->final_save($patent);
	}

	protected function save_invention($patent)
	{
		$patent->set_prop("invention_name_et" , $_SESSION["patent"]["invention_name_et"]);
		$patent->set_prop("epat_date" , $_SESSION["patent"]["epat_date"]);
		$patent->set_prop("epat_nr" , $_SESSION["patent"]["epat_nr"]);
		$patent->save();
	}

	protected function get_object()
	{
		if(is_oid($_SESSION["patent"]["id"]))
		{
			$patent = obj($_SESSION["patent"]["id"]);
		}
		else
		{
			$patent = new object();
			$patent->set_class_id(CL_EURO_PATENT_ET_DESC);
			$patent->set_parent($_SESSION["patent"]["parent"]);
			$patent->save();
			$patent->set_name(" Kinnitamata taotlus nr [".$patent->id()."]");
		}

		return $patent;
	}

	public function get_payment_sum()
	{
		$sum = $this->get_request_fee();
		return $sum;
	}

	public function get_request_fee()
	{
		$sum = 700;
		return $sum;
	}

	function get_vars($arr)
	{
		$data = parent::get_vars($arr);

		if(sizeof($_SESSION["patent"]["applicants"]) == 1)
		{
			$_SESSION["patent"]["representer"] = reset(array_keys($_SESSION["patent"]["applicants"]));
		}

		return $data;
	}

	function check_fields()
	{
		$err = parent::check_fields();

		if($_POST["data_type"] === "12")
		{
			foreach ($_FILES as $var => $file_data)
			{
				if (is_uploaded_file($file_data["tmp_name"]))
				{
					if ("epat_desc_trans_upload" === $var or "epat_other_file_upload" === $var or "epat_desc_trans_appl_upload" === $var)
					{
						$fp = fopen($file_data["tmp_name"], "r");
						flock($fp, LOCK_SH);
						$sig = fread($fp, 4);
						fclose($fp);
						if("%PDF" !== $sig)
						{
							unset($_FILES[$var]["tmp_name"]);
							$err.= t("Ainult pdf formaadis fail lubatud")."\n<br>";
						}
					}
				}
			}

			$date = mktime(0, 0, 0, $_POST["epat_date"]["month"], $_POST["epat_date"]["day"], $_POST["epat_date"]["year"]);
			if(1025470800 > $date) // July 1st 2002
			{
				$err.= t("Kuup&auml;ev peab olema suurem kui 1. juuli 2002")."\n<br>";
			}

			if(empty($err))
			{
				$_SESSION["patent"]["checked"]["12"] = "12";
			}
			else
			{
				unset($_SESSION["patent"]["checked"]["12"]);
			}
		}

		return $err;
	}

	function fill_session($id)
	{
		parent::fill_session($id);
		$patent = obj($id);
		$_SESSION["patent"]["representer"] = $patent->prop("applicant");
	}

	/**
		@attrib api=1
		@param o required type=object
		@returns
			PHP DOMDocument instance
	**/
	public function get_po_xml(object $o)
	{
		$xml = parent::get_po_xml($o);
		$xpath = new DOMXPath($xml);
		$root = $xpath->query("//BIRTH")->item(0);
		$despg = $xpath->query("//DESPG")->item(0);
		$root->setAttribute("REGREN", trademark_manager::rere($o->prop("epat_nr")));

		//
		$el = $xml->createElement("TITLE");
		$el->setAttribute("TEXT", trademark_manager::rere($o->prop("invention_name_et")));
		$root->insertBefore($el, $despg);

		//
		return $xml;
	}
}

?>

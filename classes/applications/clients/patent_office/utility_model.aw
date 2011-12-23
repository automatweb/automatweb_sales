<?php
// utility_model.aw - Kasulik mudel
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends applications/clients/patent_office/intellectual_property
@tableinfo aw_trademark index=aw_oid master_table=objects master_index=brother_of

@default group=general
	@property applicant_reg type=chooser store=no
	@caption Taotleja on saanud kasuliku mudeli taotlemise &otilde;iguse kui


@groupinfo author caption="Autor"
@default group=author
	@property author type=relpicker reltype=RELTYPE_AUTHOR
	@caption Autor

	@property author_disallow_disclose type=checkbox ch_value=1 store=no
	@caption Mitte avalikustada minu nime autorina

@groupinfo invention caption="Leiutise nimetus"
@default group=invention
	@property invention_name type=textbox
	@caption Leiutise nimetus


@default group=priority
	@property childtitle110 type=text store=no subtitle=1
	@caption Pariisi konventsiooni vm. kokkuleppe taotluse alusel
		@property prio_convention_date type=date_select
		@caption Kuup&auml;ev

		@property prio_convention_country type=textbox
		@caption Riigi kood

		@property prio_convention_nr type=textbox
		@caption Taotluse number

	@property childtitle111 type=text store=no subtitle=1
	@caption Esitatud patenditaotluse p&otilde;hjal
		@property prio_prevapplicationsep_date type=date_select
		@caption Kuup&auml;ev

		@property prio_prevapplicationsep_nr type=textbox
		@caption Taotluse number

	@property childtitle112 type=text store=no subtitle=1
	@caption Varasema taotluse alusel (seaduse &#0167;10 l&otilde;ige 3)
		@property prio_prevapplication_date type=date_select
		@caption Kuup&auml;ev

		@property prio_prevapplication_nr type=textbox
		@caption Taotluse number

@groupinfo attachments caption="Lisad"
@default group=attachments
 	@property attachment_invention_description type=fileupload reltype=RELTYPE_ATTACHMENT_INVENTION_DESCRIPTION form=+emb
	@caption Leiutiskirjeldus

 	@property attachment_demand type=fileupload reltype=RELTYPE_ATTACHMENT_DEMAND form=+emb
	@caption Kasuliku mudeli n&otilde;udlus

 	@property attachment_demand_points type=textbox size=3
	@caption Kasuliku mudeli n&otilde;udlus, n&otilde;udluspunkti

 	@property attachment_summary_et type=fileupload reltype=RELTYPE_ATTACHMENT_SUMMARY_ET form=+emb
	@caption Leiutise olemuse l&uuml;hikokkuv&otilde;te

 	@property attachment_dwgs type=multifile_upload reltype=RELTYPE_ATTACHMENT_DWGS form=+emb
	@caption Illustratsioonid

 	@property attachment_fee type=fileupload reltype=RELTYPE_ATTACHMENT_FEE form=+emb
	@caption Riigil&otilde;ivu tasumist t&otilde;endav dokument

 	@property attachment_warrant type=fileupload reltype=RELTYPE_ATTACHMENT_WARRANT form=+emb
	@caption Volikiri

 	@property attachment_prio type=fileupload reltype=RELTYPE_ATTACHMENT_PRIO form=+emb
	@caption Prioriteedin&otilde;uet t&otilde;endavad dokumendid

 	@property attachment_prio_trans type=fileupload reltype=RELTYPE_ATTACHMENT_PRIO_TRANS form=+emb
	@caption Prioriteedin&otilde;uet t&otilde;endavate dokumentide t&otilde;lked

 	@property attachment_other type=fileupload reltype=RELTYPE_ATTACHMENT_OTHER form=+emb
	@caption Muu


// RELTYPES
@reltype AUTHOR value=17 clid=CL_CRM_PERSON
@caption Autor

@reltype ATTACHMENT_INVENTION_DESCRIPTION value=100 clid=CL_FILE
@caption Lisa kirjeldus

@reltype ATTACHMENT_SEQ value=101 clid=CL_FILE
@caption Lisa jarjestuse loetelu

@reltype ATTACHMENT_DEMAND value=102 clid=CL_FILE
@caption Lisa pat. noudlus

@reltype ATTACHMENT_SUMMARY_ET value=103 clid=CL_FILE
@caption Lisa kokkuvote est

@reltype ATTACHMENT_SUMMARY_EN value=104 clid=CL_FILE
@caption Lisa kokkuvote eng

@reltype ATTACHMENT_DWGS value=105 clid=CL_FILE
@caption Lisa joonised

@reltype ATTACHMENT_FEE value=106 clid=CL_FILE
@caption Lisa tasumisdok

@reltype ATTACHMENT_WARRANT value=107 clid=CL_FILE
@caption Lisa volikiri

@reltype ATTACHMENT_PRIO value=108 clid=CL_FILE
@caption Lisa prioriteeditoend

@reltype ATTACHMENT_PRIO_TRANS value=110 clid=CL_FILE
@caption Lisa prioriteeditoendi tolked

@reltype ATTACHMENT_OTHER value=111 clid=CL_FILE
@caption Lisa muu

*/

class utility_model extends intellectual_property
{
	public static $level_index = array(
		0 => 0,
		1 => 11,
		2 => 12,
		3 => 3,
		4 => 14,
		5 => 4,
		6 => 5
	);

	function __construct()
	{
		parent::__construct();
		$this->init(array(
			"tpldir" => "applications/patent",
			"clid" => CL_UTILITY_MODEL
		));
		$this->info_levels = array(
			0 => "applicant_um",
			11 => "author_um",
			12 => "invention_um",
			3 => "priority_um",
			14 => "attachments_um",
			4 => "fee_um",
			5 => "check_um"
		);
		$this->pdf_file_name = "KasulikuMudeliTaotlus";
		$this->show_template = "show_um.tpl";
		$this->show_sent_template = "show_sent_um.tpl";
		$this->date_vars = array_merge($this->date_vars, array("prio_convention_date", "prio_prevapplicationsep_date", "prio_prevapplication_date"
));
		$this->file_upload_vars = array_merge($this->file_upload_vars, array("attachment_invention_description", "attachment_seq", "attachment_demand", "attachment_summary_et", "attachment_fee", "attachment_warrant", "attachment_prio", "attachment_prio_trans", "attachment_other"));
		$this->text_vars = array_merge($this->text_vars, array("invention_name","prio_convention_country","prio_convention_nr","prio_prevapplicationsep_nr","prio_prevapplication_nr","attachment_demand_points"));
		$this->checkbox_vars = array_merge($this->checkbox_vars, array("author_disallow_disclose"));
		$this->chooser_vars = array_merge($this->chooser_vars, array("applicant_reg"));
		$this->multifile_upload_vars = array_merge($this->multifile_upload_vars, array("attachment_dwgs"));

		$this->datafromobj_vars = array_merge($this->datafromobj_vars, array("invention_name", "prio_convention_date", "prio_convention_country", "prio_convention_nr", "prio_prevapplicationsep_date", "prio_prevapplicationsep_nr", "prio_prevapplication_date", "prio_prevapplication_nr", "attachment_invention_description", "attachment_demand", "attachment_demand_points", "attachment_summary_et", "attachment_dwgs", "attachment_fee", "attachment_warrant", "attachment_prio", "attachment_prio_trans", "applicant_reg", "attachment_other"));
		$this->datafromobj_del_vars = array("name_value" , "email_value" , "phone_value" , "fax_value" , "code_value" ,"email_value" , "street_value" ,"index_value" ,"country_code_value","city_value","county_value","correspond_street_value", "correspond_index_value" , "correspond_country_code_value" , "correspond_county_value","correspond_city_value", "name", "applicant_reg", "author_disallow_disclose");
	}

	public function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "applicant_reg":
				$prop["options"] = $arr["obj_inst"]->get_applicant_reg_options();
				break;

			default:
				$retval = parent::get_property($arr);
		}
		return $retval;
	}

	protected function save_priority($patent)
	{
		$patent->set_prop("prio_convention_date" , $_SESSION["patent"]["prio_convention_date"]);
		$patent->set_prop("prio_convention_country" , $_SESSION["patent"]["prio_convention_country"]);
		$patent->set_prop("prio_convention_nr" , $_SESSION["patent"]["prio_convention_nr"]);
		$patent->set_prop("prio_prevapplicationsep_date" , $_SESSION["patent"]["prio_prevapplicationsep_date"]);
		$patent->set_prop("prio_prevapplicationsep_nr" , $_SESSION["patent"]["prio_prevapplicationsep_nr"]);
		$patent->set_prop("prio_prevapplication_date" , $_SESSION["patent"]["prio_prevapplication_date"]);
		$patent->set_prop("prio_prevapplication_nr" , $_SESSION["patent"]["prio_prevapplication_nr"]);
		$patent->save();
	}

	protected function save_forms($patent)
	{
		$this->save_priority($patent);
		$this->save_fee($patent);
		$this->save_invention($patent);
		$this->save_applicants($patent);
		$this->save_authors($patent);
		$this->fileupload_save($patent);
		$this->multifile_upload_save($patent);
		$this->save_attachments($patent);
		$this->final_save($patent);
	}

	protected function save_invention($patent)
	{
		$patent->set_prop("invention_name" , $_SESSION["patent"]["invention_name"]);
		$patent->save();
	}

	protected function save_attachments($patent)
	{
		$patent->set_prop("attachment_demand_points" , $_SESSION["patent"]["attachment_demand_points"]);
		$patent->save();
	}

	protected function get_object()
	{
		if(!empty($_SESSION["patent"]["id"]))
		{
			$patent = obj($_SESSION["patent"]["id"]);
		}
		else
		{
			$patent = new object();
			$patent->set_class_id(CL_UTILITY_MODEL);
			$patent->set_parent($_SESSION["patent"]["parent"]);
			$patent->save();
			$patent->set_name(" Kinnitamata taotlus nr [".$patent->id()."]");
		}

		return $patent;
	}

	public function get_payment_sum($float = false)
	{
		$sum = $this->get_request_fee(true);
		return $float ? $sum : number_format($sum, 2, ",", "");
	}

	public function get_request_fee($float = false)
	{
		$sum = 0;
		$is_corporate = false;

		if (isset($_SESSION["patent"]["applicants"]))
		{
			foreach(safe_array($_SESSION["patent"]["applicants"]) as $val)
			{
				if($val["applicant_type"])
				{
					$is_corporate = true;
					break;
				}
			}
		}

		$sum = $is_corporate ? 102.25 : 25.56;
		return $float ? $sum : number_format($sum, 2, ",", "");
	}

	function get_vars($arr)
	{
		$data = parent::get_vars($arr);

		if (isset($_SESSION["patent"]["delete_author"]))
		{
			unset($_SESSION["patent"]["authors"][$_SESSION["patent"]["delete_author"]]);
			unset($_SESSION["patent"]["delete_author"]);
		}

		if (!empty($_SESSION["patent"]["add_new_author"]))
		{
			$_SESSION["patent"]["add_new_author"] = null;
			$_SESSION["patent"]["change_author"] = null;
			$_SESSION["patent"]["author_id"] = null;
		}
		elseif (isset($_SESSION["patent"]["author_id"]) and strlen(trim($_SESSION["patent"]["author_id"])))
		{
			$this->_get_author_data();
			$data["change_author"] = $_SESSION["patent"]["author_id"];
			$_SESSION["patent"]["change_author"] = null;
			$_SESSION["patent"]["author_id"] = null;
		}
		else
		{
			$data["author_no"] = isset($_SESSION["patent"]["authors"]) ? count($_SESSION["patent"]["authors"]) + 1 : 1;
		}
		//nendesse ka siis see tingumus, et muuta ei saa

		$data["P_ADDRESS"] = $this->parse("P_ADDRESS");

		$data["add_new_author"] = html::radiobutton(array(
				"value" => 1,
				"checked" => 0,
				"name" => "add_new_author",
		));

		if (isset($_SESSION["patent"]["authors"]) && is_array($_SESSION["patent"]["authors"]) && sizeof($_SESSION["patent"]["authors"]))
		{
			$data["authors_table"] = $this->_get_authors_table();
		}

		return $data;
	}

	function check_fields()
	{
		$err = parent::check_fields();

		if(((int) $_POST["data_type"]) === 14)
		{
			foreach ($_FILES as $var => $file_data)
			{
				if (is_uploaded_file($file_data["tmp_name"]) and "attachment_other_upload" !== $var)
				{
					$fp = fopen($file_data["tmp_name"], "r");
					flock($fp, LOCK_SH);
					$sig = fread($fp, 11);
					fclose($fp);

					$sig1 = substr($sig, 0, 6);
					$sig2 = substr($sig, 0, 4);
					$sig3 = substr($sig, 6, 5);
					$jpg_sig1 = chr(255) . chr(216) . chr(255) . chr(225);
					$jpg_sig2 = "EXIF" . chr(0);
					$jpg_sig4 = chr(255) . chr(216) . chr(255) . chr(224);
					$jpg_sig3 = "JFIF" . chr(0);

					if (
						"GIF87a" !== $sig1 and
						"GIF89a" !== $sig1 and
						($sig2 !== $jpg_sig1 and $sig3 !== $jpg_sig2 or $sig2 === $jpg_sig1 xor $sig3 === $jpg_sig2) and
						($sig2 !== $jpg_sig4 and $sig3 !== $jpg_sig3 or $sig2 === $jpg_sig4 xor $sig3 === $jpg_sig3) and
						"%PDF" !== $sig2
					)
					{
						unset($_FILES[$var]["tmp_name"]);
						$err.= t("Ainult pdf formaadis failid lubatud")."\n<br>";
					}
				}
			}

			if (empty($_SESSION["patent"]["attachment_invention_description"]) and empty($_FILES["attachment_invention_description_upload"]["tmp_name"]))
			{
				$err.= t("Leiutiskirjeldus peab olema lisatud")."\n<br>";
			}

			if (empty($err))
			{
				$_SESSION["patent"]["checked"]["14"] = "14";
			}
			else
			{
				unset($_SESSION["patent"]["checked"]["14"]);
			}
		}

		return $err;
	}

	function fill_session($id)
	{
		$patent = obj($id);
		$address_inst = get_instance(CL_CRM_ADDRESS);
		parent::fill_session($id);
		$author_disallow_disclose = (array) $patent->meta("author_disallow_disclose");

		foreach($patent->connections_from(array("type" => "RELTYPE_AUTHOR")) as $key => $c)
		{
			$o = $c->to();
			$key = $o->id();
			$_SESSION["patent"]["authors"][$key]["name"] = $o->name();
			$_SESSION["patent"]["authors"][$key]["firstname"] = $o->prop("firstname");
			$_SESSION["patent"]["authors"][$key]["lastname"] = $o->prop("lastname");
			$_SESSION["patent"]["authors"][$key]["author_disallow_disclose"] = $author_disallow_disclose[$o->id()];
			$address = $o->prop("address");

			if($this->can("view" , $address))
			{
				$address_obj = obj($address);
				$_SESSION["patent"]["authors"][$key]["street"] = $address_obj->prop("aadress");
				$_SESSION["patent"]["authors"][$key]["index"] = $address_obj->prop("postiindeks");
				if($this->can("view" , $address_obj->prop("linn")))
				{
					$city = obj($address_obj->prop("linn"));
					$_SESSION["patent"]["authors"][$key]["city"] = $city->name();
				}
				if($this->can("view" , $address_obj->prop("maakond")))
				{
					$county = obj($address_obj->prop("maakond"));
					$_SESSION["patent"]["authors"][$key]["county"] = $county->name();
				}
				$_SESSION["patent"]["authors"][$key]["country_code"] = $address_inst->get_country_code($address_obj->prop("riik"));
			}
		}
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
		$holgr_following = $xpath->query("//BIRTH/HOLGR[last()]")->item(0);
		$holgr_following = $xpath->query("following-sibling::REPGR|following-sibling::DESPG", $holgr_following)->item(0);

		// author(s)
		$adr_i = get_instance(CL_CRM_ADDRESS);
		$author_disallow_disclose = (array) $o->meta("author_disallow_disclose");

		foreach($o->connections_from(array("type" => "RELTYPE_AUTHOR")) as $c)
		{
			$author = $c->to();

			if ($this->can("view", $author->id()))
			{
				$author_el = $xml->createElement("INVENTOR");
				$name = $xml->createElement("NAME");
				$addr = $xml->createElement("ADDRESS");
				$root->insertBefore($author_el, $holgr_following);

				// author name
				$name->appendChild(new DOMElement("NAMEL", trademark_manager::rere($author->prop("firstname"))));
				$name->appendChild(new DOMElement("NAMEL", trademark_manager::rere($author->prop("lastname"))));

				// author address
				$addr->appendChild(new DOMElement("ADDRL", trademark_manager::rere($author->prop("address.aadress"))));
				$addr->appendChild(new DOMElement("ADDRL", trademark_manager::rere($author->prop("address.linn.name"))));
				$addr->appendChild(new DOMElement("ADDRL", trademark_manager::rere($author->prop("address.maakond.name"))));
				$addr->appendChild(new DOMElement("ADDRL", trademark_manager::rere($author->prop("address.postiindeks"))));

				if ($this->can("view", $author->prop("address.riik")))
				{
					$addr->appendChild(new DOMElement("COUNTRY", trademark_manager::rere($adr_i->get_country_code(obj($author->prop("address.riik"))))));
				}

				//
				$author_el->appendChild($name);
				$author_el->appendChild($addr);
				$author_el->appendChild(new DOMElement("SECRET", ((string) (int) (bool) $author_disallow_disclose[$author->id()])));
			}
		}

		//
		$el = $xml->createElement("TITLE");
		$el->setAttribute("TEXT", trademark_manager::rere($o->prop("invention_name")));
		$root->insertBefore($el, $despg);

		//
		$types = array(
			patent_patent_obj::APPLICANT_REG_AUTHOR => 1,
			patent_patent_obj::APPLICANT_REG_AUTHOR_SUCCESOR => 2,
			patent_patent_obj::APPLICANT_REG_EMPLOYEE => 3,
			patent_patent_obj::APPLICANT_REG_OTHER_CONTRACT => 4
		);
		$applicant = $o->get_first_obj_by_reltype("RELTYPE_APPLICANT");
		$applicant_reg = (array) $o->meta("applicant_reg");
		$applicant_reg = isset($types[$applicant_reg[$applicant->id()]]) ? $types[$applicant_reg[$applicant->id()]] : 1;
		$el = $xml->createElement("TYPMARI", $applicant_reg);
		$root->insertBefore($el, $despg);

		// priority
		if($o->prop("prio_convention_date") !== "-1" and $o->prop("prio_convention_nr"))
		{ // Pariisi konventsiooni vm. kokkuleppe taotluse alusel
			$el = $xml->createElement("PRIGR");
			$el->appendChild(new DOMElement("PRICP", $o->prop("prio_convention_country")));
			$el->appendChild(new DOMElement("PRIAPPD", date("Ymd",$o->prop("prio_convention_date"))));
			$el->appendChild(new DOMElement("PRIAPPN", $o->prop("prio_convention_nr")));
			$el->appendChild(new DOMElement("PRITYPE", "1"));
			$root->insertBefore($el, $despg);
		}

		if($o->prop("prio_prevapplicationsep_date") !== "-1" and $o->prop("prio_prevapplicationsep_nr"))
		{ // Esitatud patenditaotluse p&otilde;hjal
			$el = $xml->createElement("PRIGR");
			$el->appendChild(new DOMElement("PRIAPPD", date("Ymd",$o->prop("prio_prevapplicationsep_date"))));
			$el->appendChild(new DOMElement("PRIAPPN", $o->prop("prio_prevapplicationsep_nr")));
			$el->appendChild(new DOMElement("PRITYPE", "3"));
			$root->insertBefore($el, $despg);
		}

		if($o->prop("prio_prevapplication_date") !== "-1" and $o->prop("prio_prevapplication_nr"))
		{ // Varasema taotluse alusel (seaduse &#0167;10 l&otilde;ige 3)
			$el = $xml->createElement("PRIGR");
			$el->appendChild(new DOMElement("PRIAPPD", date("Ymd",$o->prop("prio_prevapplication_date"))));
			$el->appendChild(new DOMElement("PRIAPPN", $o->prop("prio_prevapplication_nr")));
			$el->appendChild(new DOMElement("PRITYPE", "2"));
			$root->insertBefore($el, $despg);
		}

		//
		return $xml;
	}
}

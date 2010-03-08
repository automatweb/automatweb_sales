<?php
// intellectual_property.aw - Intellektuaalomand
/*

@classinfo syslog_type=ST_INTELLECTUAL_PROPERTY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_trademark index=aw_oid master_table=objects master_index=brother_of


@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property applicant type=relpicker reltype=RELTYPE_APPLICANT
	@caption Taotleja

	@property signed type=text store=no editonly=1
	@caption Allkirja staatus

	@property signatures type=text store=no editonly=1
	@caption Allkirjastajad

	@property job type=textbox store=no editonly=1
	@caption Allkirjastaja amet

	@property procurator type=relpicker reltype=RELTYPE_PROCURATOR
	@caption Volinik

	@property warrant type=fileupload reltype=RELTYPE_WARRANT form=+emb
	@caption Volikiri

	@property authorized_person type=relpicker reltype=RELTYPE_AUTHORIZED_PERSON
	@caption Volitatud isik

	@property authorized_codes type=textbox table=aw_trademark field=aw_authorized_codes method=null
	@caption Volitatud isikute isikukoodid

	@property additional_info type=textarea
	@caption Lisainfo

	@property verified type=checkbox
	@caption Kinnitatud

	@property exported type=checkbox caption=no
	@caption Eksporditud

	@property export_date type=date_select
	@caption Ekspordi kuup&auml;ev

	@property nr type=textbox
	@caption Taotluse number


@groupinfo priority caption="Prioriteet"


@groupinfo fee caption="Riigil&otilde;iv"
@default group=fee
	@property request_fee type=textbox size=4
	@caption Taotlusl&otilde;iv

	@property fee_sum type=textbox size=4
	@caption Kokku

	@property payer type=textbox
	@caption Maksja nimi

	@property doc_nr type=textbox
	@caption Maksedokumendi number

	@property payment_date type=date_select
	@caption Makse kuup&auml;ev

	@property payment_order type=fileupload reltype=RELTYPE_PAYMENT_ORDER form=+emb
	@caption Maksekorraldus


#RELTYPES
@reltype APPLICANT value=1 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Taotleja

@reltype PROCURATOR value=2 clid=CL_CRM_PERSON
@caption Volinik

@reltype WARRANT value=3 clid=CL_FILE
@caption Volikiri

@reltype PHONE value=4 clid=CL_CRM_PHONE
@caption Telefon

@reltype FAX value=5 clid=CL_CRM_PHONE
@caption Faks

@reltype EMAIL value=6 clid=CL_CRM_EMAIL
@caption E-mail

@reltype PROCURATOR_MENU value=8 clid=CL_MENU
@caption Volinike kaust

@reltype AUTHORIZED_PERSON value=10 clid=CL_CRM_PERSON
@caption Volitatud isik

@reltype BANK_PAYMENT value=11 clid=CL_BANK_PAYMENT
@caption Pangalingi objekt

@reltype PAYMENT_ORDER value=14 clid=CL_FILE
@caption Maksekorraldus

@reltype TRADEMARK_STATUS value=15 clid=CL_TRADEMARK_STATUS
@caption Staatus

*/

abstract class intellectual_property extends class_base
{
	public $ip_classes = array(
		CL_PATENT,
		CL_PATENT_PATENT,
		CL_INDUSTRIAL_DESIGN,
		CL_EURO_PATENT_ET_DESC,
		CL_UTILITY_MODEL
	);

	function __construct()
	{
		parent::__construct();
		$this->text_vars = array("name" , "firstname" , "lastname" ,  "code" , "street", "city" ,"county" ,"index", "country_code" , "phone" , "email" , "fax", "authorized_codes", "request_fee" , "payer" , "doc_nr","authorized_person_firstname", "authorized_person_lastname","authorized_person_code", "correspond_firstname", "correspond_lastname", "correspond_job", "correspond_phone", "correspond_email", "correspond_street", "correspond_city","correspond_county","correspond_index","correspond_country_code", "job", "fee_sum");
		$this->text_area_vars = array("additional_info");
		$this->file_upload_vars = array("warrant" , "payment_order");
		$this->multifile_upload_vars = array(); // property names must be same as reltype names
		$this->date_vars = array("payment_date");
		$this->checkbox_vars = array();
		$this->chooser_vars = array();
		$this->select_vars = array();
		$this->country_popup_link_vars = array("convention_country", "exhibition_country", "country_code");
		$this->save_fee_vars = array("request_fee", "fee_sum");
		$this->author_vars = array("name", "firstname", "lastname", "street", "city", "county", "index", "country_code", "author_disallow_disclose");
		$this->applicant_vars = array("firstname", "lastname", "street", "city", "county", "index", "country_code", "name" , "code" , "phone" , "email" , "fax", "correspond_firstname", "correspond_lastname", "correspond_job", "correspond_phone", "correspond_email", "correspond_street","correspond_city", "correspond_county", "correspond_index","correspond_country_code", "applicant_type", "job", "country", "applicant_reg");
		$this->datafromobj_vars = array("authorized_codes" , "job" , "request_fee" , "fee_sum", "doc_nr", "payer");

		//siia panev miskid muutujad mille iga ringi peal 2ra kustutab... et uuele taotlejale vana info ei j22ks
		$this->datafromobj_del_vars = array("name_value" , "email_value" , "phone_value" , "fax_value" , "code_value" , "street_value" ,"index_value" ,"country_code_value","city_value", "county_value", "correspond_firstname_value", "correspond_lastname_value", "correspond_job_value", "correspond_phone_value", "correspond_email_value", "correspond_street_value", "correspond_index_value" , "correspond_country_code_value" , "correspond_county_value" , "correspond_city_value", "name", "applicant_reg");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "signed":
				if(!aw_ini_get("file.ddoc_support"))
				{
					return PROP_IGNORE;
				}
				$ddoc_inst = get_instance(CL_DDOC);
				$res = $this->is_signed($arr["obj_inst"]->id());
				switch($res["status"])
				{
					case 1:
						$url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $res["ddoc"],
						));
						$ddoc = obj($res["ddoc"]);
						$add_sig = html::href(array(
							"url" => "#",
							"caption" => t("Lisa allkiri"),
							"onclick" => "aw_popup_scroll(\"".$url."\", \"".t("Allkirjastamine")."\", 410, 250);",
						));
						$ddoc_link = html::href(array(
							"url" => $this->mk_my_orb("change", array(
								"id" => $ddoc->id(),
								"return_url" => get_ru(),
							), CL_DDOC),
							"caption" => t("DigiDoc konteinerisse"),
						));
						$prop["value"] = $add_sig." (".$ddoc_link.")";
						break;
					case 0:
						$url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $res["ddoc"],
						));
						$ddoc = obj($res["ddoc"]);
						$add_sig = html::href(array(
							"url" => "#",
							"caption" => t("Allkirjasta"),
							"onClick" => "aw_popup_scroll(\"".$url."\", \"".t("Allkirjastamine")."\", 410, 250);",
						));
						$ddoc_link = html::href(array(
							"url" => $this->mk_my_orb("change", array(
								"id" => $ddoc->id(),
								"return_url" => get_ru(),
							), CL_DDOC),
							"caption" => t("DigiDoc konteiner"),
						));
						$prop["value"] = $add_sig." (".$ddoc_link.")";

						break;
					case -1:
						$url = $ddoc_inst->sIgn_url(array(
							"other_oid" => $arr["obj_inst"]->id(),
						));
						$prop["value"] = html::href(array(
							"url" => "#",
							"caption" => t("Allkirjasta fail"),
							"onClick" => "aw_popup_scroll(\"".$url."\", \"".t("Allkirjastamine")."\", 410, 250);",

						));
						break;
				}
				break;

			case "signatures":
				if(!aw_ini_get("file.ddoc_support"))
				{
					return PROP_IGNORE;
				}
				$re = $this->is_signed($arr["obj_inst"]->id());
				if($re["status"] != 1)
				{
					return PROP_IGNORE;
				}
				$ddoc_inst = get_instance(CL_DDOC);
				$signs = $ddoc_inst->get_signatures($re["ddoc"]);
				foreach($signs as $sig)
				{
					$sig_nice[] = sprintf(t("%s, %s (%s) - %s"), $sig["signer_ln"], $sig["signer_fn"], $sig["signer_pid"], date("H:i d/m/Y", $sig["signing_time"]));
				}
				$prop["value"] = join("<br/>", $sig_nice);
				break;

			case "export_date":
				$status = $this->get_status($arr["obj_inst"]);
				if($status->prop("exported"))
				{
					$prop["type"] = "text";
					$prop["value"] = date("j:m:Y h:i" , $prop["value"]);
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "exported":
				$status = $this->get_status($arr["obj_inst"]);
				if($status->prop("exported"))
				{
					$prop["type"] = "text";
					$prop["value"] = t("Eksporditud");
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}


	/**
		@comment
	**/
	function is_signed($oid)
	{
		if(!$this->can("view", $oid))
		{
			error::raise(array(
				"msg" => t("Vale objekti id!")
			));
		}

		$c = new connection();
		aw_disable_acl();
		$cc = $c->find(array(
			"from.class_id" => CL_DDOC,
			// "from.status" => new obj_predicate_not(object::STAT_DELETED),
			"type" => "RELTYPE_SIGNED_FILE",
			"to" => $oid
		));
		aw_restore_acl();
		$return = $ret = array();

		foreach ($cc as $ret)
		{
			if ($ret["from.status"])
			{
				break;
			}
		}

		if(count($ret) > 1)
		{
			$ret = $ret["from"];
			$inst = get_instance(CL_DDOC);
			aw_disable_acl();
			$tmp = $inst->is_signed($ret);
			aw_restore_acl();
/*
			$classes_w_author = array(CL_UTILITY_MODEL, CL_PATENT_PATENT, CL_INDUSTRIAL_DESIGN);

			if (in_array($o->class_id(), $classes_w_author))
			{ // for classes with author, check if authors not wishing to disclose their name have signed
				$author_disallow_disclose = (array) $o->meta("author_disallow_disclose");
				$sign_req = array();

				foreach($o->connections_from(array("type" => "RELTYPE_AUTHOR")) as $c)
				{
					$author = $c->to();
					if (!empty($author_disallow_disclose[$author->id()]))
					{
						$sign_req[] = $author;
					}
				}

				foreach ($sign_req as $author)
				{
					$c = new connection();
					$signed = $c->find(array(
						"from.class_id" => CL_DDOC,
						"type" => "RELTYPE_SIGNER",
						"from" => $ret,
						"to" => $author->id()
					));

					if (count($signed) < 1)
					{
						$tmp = false;
						break;
					}
				}
			}
 */
			$return["status"] = $tmp?1:0;
			$return["ddoc"] = $ret;
		}
		else
		{
			$return["status"] = -1;
		}
		return $return;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "warrant":
			case "payment_order":
			case "reproduction":
			case "g_statues":
			case "c_statues":
			case "attachment_invention_description":
			case "attachment_seq":
			case "attachment_demand":
			case "attachment_summary_et":
			case "attachment_dwgs":
			case "attachment_fee":
			case "attachment_warrant":
			case "attachment_prio":
			case "attachment_prio_trans":
			case "attachment_summary_en":
			case "attachment_bio":
			case "attachment_other":
				$image_inst = get_instance(CL_IMAGE);
				$file_inst = get_instance(CL_FILE);
				if(array_key_exists($prop["name"] , $_FILES))
				{
					if($_FILES[$prop["name"]]['tmp_name'])
					{
						$id = $file_inst->save_file(array(
							"parent" => $arr["obj_inst"]->id(),
							"content" => $image_inst->get_file(array(
								"file" => $_FILES[$prop["name"]]['tmp_name'],
							)),
							"name" => $_FILES[$prop["name"]]['name'],
							"type" => $_FILES[$prop["name"]]['type'],
						));
						$arr["obj_inst"]->set_prop($prop["name"], $id);
						$arr["obj_inst"]->connect(array("to" => $id, "type" => "RELTYPE_".strtoupper($prop["name"])));
						$arr["obj_inst"]->save();
					}
				}
				return PROP_IGNORE;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function request_execute ($this_object)
	{
		return $this->show (array (
			"id" => $this_object->id(),
		));
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	/** Show trademark applications

		@attrib name=show is_public="1" all_args=1
		@param id required oid
			trademark id

	**/
	function show($arr)
	{
		$tpl = $_GET["sent_form"] ? $this->show_sent_template : $this->show_template;
		$this->read_template($tpl);

		if($this->can("view", $_GET["trademark_id"]))
		{
			$_SESSION["patent"] = null;
			$_SESSION["patent"]["id"] = $_GET["trademark_id"];
			$this->fill_session($_GET["trademark_id"]);
			// $this->check_and_give_rights($_GET["trademark_id"]);
		}

		if($this->can("view" , $arr["id"]))
		{
			$ob = new object($arr["id"]);
			$stat_obj = $this->get_status($ob);
			$this->vars(array(
				"name" => $stat_obj->prop("name"),
			));
			$data = $this->get_data_from_object($arr["id"]);

			if (CL_PATENT === $this->clid)
			{
				$prods = $ob->meta("products");
			}

			$_SESSION["patent"]["id"] = $arr["id"];

			//kui pole 6igust n2ha
			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
			$code = $p->prop("personal_id");
			$ol = new object_list(array(
				"class_id" => CL_TRADEMARK_MANAGER,
				"not_verified_menu" => $ob->parent(),
				"lang_id" => array(),
			));

			if(!sizeof($ol->arr()))
			{
				$ol = new object_list(array(
					"class_id" => CL_TRADEMARK_MANAGER,
					"verified_menu" => $ob->parent(),
					"lang_id" => array(),
				));
			}

			$manager = reset($ol->arr());
			if(is_object($manager) && $this->can("view" , $manager->id()))
			{
				$admins = $manager->prop("admins");
				if(sizeof(array_intersect($admins , array_keys(aw_global_get("gidlist_pri_oid")))))
				{
					$is_admin = 1;
				}
			}

			if(!(aw_global_get("uid") === $ob->createdby() || substr_count($ob->prop("authorized_codes"), $code) || $is_admin))
			{
				return "";
			}
		}
		else
		{
			$data = $this->web_data($arr);
			$ob = obj($_SESSION["patent"]["id"]);

			if (CL_PATENT === $this->clid)
			{
				$prods = $_SESSION["patent"]["products"];
			}
		}

		$stat_obj = $this->get_status($ob);

		if($_POST["send"])
		{
			$this->set_sent(array("add_obj" => $arr["add_obj"], ));
		}

		if($_POST["print"] && !$_POST["send"])
		{
			$ref_url = aw_global_get("REQUEST_URI");
			$data["print"] = "<script language='javascript'>
				window.print();
				setTimeout('window.location.href=\"".$ref_url."\"',5000);
			</script>";
		}
		else
            	{
                        if (html2pdf::can_convert())
                        {
                                if($arr["alias"]["to"])
                                {
                                        $pdfurl = $this->mk_my_orb("pdf", array("print" => 1 , "id" => $_SESSION["patent"]["id"], "add_obj" => $arr["alias"]["to"], "sent_form" => $_GET["sent_form"]));
                                }
                                else
                                {
                                        $pdfurl = $this->mk_my_orb("pdf", array("print" => 1 , "id" => $_SESSION["patent"]["id"] , "sent_form" => $_GET["sent_form"]));
                                }
                                $pdfurl = str_replace("https" , "http" , $pdfurl);
                                $data["pdf"] = "<input type='button' value='Salvesta pdf' class='nupp'  onclick='javascript:window.location.href=\"".$pdfurl."\";'><br />";
                        }

                        $data["print"] = "<input type='button' value='".t("Prindi")."' class='nupp' onclick='javascript:document.changeform.submit();'>";
                }


		if($this->can("view" , $arr["id"]))
		{
			$status = $this->is_signed($arr["id"]);
		}

		if($arr["sign"] && !$_POST["print"])
		{
			$ddoc_inst = get_instance(CL_DDOC);
			if($status["status"] > 0)
			{
				$url = $ddoc_inst->sign_url(array(
					"ddoc_oid" =>$status["ddoc"],
				));
			}
			else
			{
				$url = $ddoc_inst->sign_url(array(
					"other_oid" =>$arr["id"],
				));
			}
			$data["sign"] = "<input type='button' value='".t("Allkirjasta")."' class='nupp' onclick='javascript:window.open(\"".$url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>";
		}

		if($status["status"] > 0 && !$stat_obj->prop("nr") && !$_POST["print"])
		{
			$data["send"] = '<input type="submit" value="'.t("Saadan taotluse").'" class="nupp" onclick="javascript:document.getElementById(\'send\').value=\'1\';
			document.changeform.submit();
			">';
		}

		$data["ref"] = $stat_obj->prop("nr");
		$data["send_date"] = $stat_obj->prop("sent_date");
		if(!$data["ref"])
		{
			$data["ref"] = "";
		}

		if($data["send_date"])
		{
			$data["send_date"] = date("d.m.Y" , $data["send_date"]);
		}
		else
		{
			$data["send_date"] = "";
		}

		if($arr["sign"] && !$_POST["print"])
		{
			$ddoc_inst = get_instance(CL_DDOC);
			$url = $ddoc_inst->sign_url(array(
				"other_oid" =>$arr["id"],
			));
			$data["sign"] = "<input type='button' value='".t("Allkirjasta")."' class='nupp' onclick='javascript:window.open(\"".$url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>";
		}

		if (isset($data["industrial_design_variant_value"]))
		{
			$options = industrial_design_obj::get_industrial_design_variant_options();
			$data["industrial_design_variant_value"] = $options[$data["industrial_design_variant_value"]];
		}

		$this->vars($data);

		if($data["procurator_text"])
		{
			$warrant = empty($data["warrant_value"]) ? "" : $this->parse("WARRANT");
			$this->vars(array(
				"WARRANT" => $warrant
			));
			$this->vars(array(
				"PROCURATOR_TEXT" => $this->parse("PROCURATOR_TEXT")
			));
		}

		if($data["authorized_person_firstname_value"] || $data["authorized_person_lastname_value"] || $data["authorized_person_code_value"])
		{
			$this->vars(array("AUTHORIZED_PERSON" => $this->parse("AUTHORIZED_PERSON")));
		}

		$check_val_props = array("add_fee", "fee_copies");

		foreach($data as $prop => $val)
		{
			if(!is_array($val) and strlen(trim($val)) and (substr_count($prop, 'value') || substr_count($prop, 'text')))
			{
				if ((in_array(str_replace("_value","", $prop), $this->date_vars) and $val < 1) or (in_array($prop, $check_val_props) and !$val))
				{
					continue;
				}
				$str = strtoupper(str_replace("_value","",str_replace("_text","",$prop)));
				$this->vars(array($str => $this->parse($str)));
			}
		}

		// FEE
		if(
			!empty($data["request_fee_value"]) or
			!empty($data["add_fee_value"]) or
			!empty($data["fee_sum_value"]) or
			!empty($data["fee_copies_value"]) or
			!empty($data["payer_value"]) or
			!empty($data["doc_nr_value"]) or
			!empty($data["payment_date_value"]) or
			!empty($data["payment_order_value"]) or
			!empty($data["request_fee_value"])
		)
		{
			$this->vars(array("FEE" => $this->parse("FEE")));
		}
		else
		{
			$this->vars(array("FEE" => ""));
		}

		// patent, ind. design and utility_model common
		if (CL_PATENT_PATENT === $this->clid or CL_UTILITY_MODEL === $this->clid or CL_INDUSTRIAL_DESIGN === $this->clid)
		{
			// PRIORITY
			$priority = false;

			if($data["prio_prevapplicationadd_nr_value"])
			{
				$this->vars(array("PRIO_PREVAPPLICATIONADD" => $this->parse("PRIO_PREVAPPLICATIONADD")));
				$priority = true;
			}

			if($data["prio_prevapplication_nr_value"])
			{
				$this->vars(array("PRIO_PREVAPPLICATION" => $this->parse("PRIO_PREVAPPLICATION")));
				$priority = true;
			}

			if($data["prio_prevapplicationsep_nr_value"])
			{
				$this->vars(array("PRIO_PREVAPPLICATIONSEP" => $this->parse("PRIO_PREVAPPLICATIONSEP")));
				$priority = true;
			}

			if($data["prio_convention_nr_value"])
			{
				$this->vars(array("PRIO_CONVENTION" => $this->parse("PRIO_CONVENTION")));
				$priority = true;
			}

			if ($priority)
			{
				$this->vars(array("PRIORITY" => $this->parse("PRIORITY")));
			}
		}

		// patent and utility_model common
		if (CL_PATENT_PATENT === $this->clid or CL_UTILITY_MODEL === $this->clid or CL_INDUSTRIAL_DESIGN === $this->clid)
		{
			// ATTACHMENTS
			if(
				$data["attachment_invention_description_value"] or
				$data["attachment_demand_value"] or
				$data["attachment_summary_et_value"] or
				$data["attachment_dwgs_value"] or
				$data["attachment_fee_value"] or
				$data["attachment_warrant_value"] or
				$data["attachment_prio_value"] or
				$data["attachment_prio_trans_value"] or
				$data["attachment_seq_value"] or
				$data["attachment_summary_en_value"] or
				$data["attachment_other_value"] or
				$data["attachment_bio_value"]
			)
			{
				$this->vars(array("ATTACHMENTS" => $this->parse("ATTACHMENTS")));
			}
		}

		// class specific
		if (CL_PATENT === $this->clid)
		{
			// PRODUCTS
			$p = "";
			foreach($prods as $key => $product)
			{
				$product = strtolower(str_replace("\r" , "", str_replace("\n",", ",$product)));
				$this->vars(array("product" => $product, "class"=> $key));
				$p.=$this->parse("PRODUCTS");
			}
			$this->vars(array("PRODUCTS" => $p));

			// PRIORITY
			if($data["convention_nr_value"])
			{
				$this->vars(array("CONVENTION" => $this->parse("CONVENTION")));
			}
			if($data["exhibition_name_value"])
			{
				$this->vars(array("EXHIBITION" => $this->parse("EXHIBITION")));
			}
		}
		elseif (CL_PATENT_PATENT === $this->clid)
		{
			// INVENTION_NAME
			if($data["invention_name_et_value"] or $data["invention_name_en_value"])
			{
				$this->vars(array("INVENTION_NAME" => $this->parse("INVENTION_NAME")));
			}

			// OTHER_DATA
			$other_data = false;

			if($data["other_first_application_data_nr_value"])
			{
				$this->vars(array("OTHER_FIRST_APPLICATION_DATA" => $this->parse("OTHER_FIRST_APPLICATION_DATA")));
				$other_data = true;
			}

			if($data["other_bio_nr_value"])
			{
				$this->vars(array("OTHER_BIO" => $this->parse("OTHER_BIO")));
				$other_data = true;
			}

			if($data["other_datapub_date_value"] > 0)
			{
				$this->vars(array("OTHER_DATAPUB" => $this->parse("OTHER_DATAPUB")));
				$other_data = true;
			}

			if ($other_data)
			{
				$this->vars(array("OTHER_DATA" => $this->parse("OTHER_DATA")));
			}
		}
		elseif (CL_INDUSTRIAL_DESIGN === $this->clid)
		{
			// INDUSTRIAL_DESIGN
			if($data["industrial_design_name_value"] or $data["industrial_design_variant_value"] or $data["industrial_design_variant_count_value"])
			{
				$this->vars(array("INDUSTRIAL_DESIGN" => $this->parse("INDUSTRIAL_DESIGN")));
			}

			// PROCESS_POSTPONE_GRP
			if($data["process_postpone_value"])
			{
				$this->vars(array("PROCESS_POSTPONE_GRP" => $this->parse("PROCESS_POSTPONE_GRP")));
			}

			// DOCS
			if($data["doc_repro_value"] or $data["doc_warrant_value"] or $data["doc_description_value"])
			{
				$this->vars(array("DOCS" => $this->parse("DOCS")));
			}
		}

		return $this->parse();
	}

	function get_data_from_object($id)
	{
		$o = obj($id);
		$a = "";
		$correspond_address = "";
		$address_inst = get_instance(CL_CRM_ADDRESS);
		$is_corporate = false;

		/////////////// APPLICANT //////////////
		if($this->is_template("APPLICANT"))
		{
			$i = 0;
			foreach($o->connections_from(array("type" => "RELTYPE_APPLICANT")) as $c)
			{
				foreach($this->datafromobj_del_vars as $del_var)
				{
					unset($this->vars[$del_var]);
				}

				$applicant = $c->to();
				$this->vars(array(
					"name_value" => htmlspecialchars($applicant->name()),
					"email_value" => htmlspecialchars($applicant->prop("email")),
					"phone_value" => htmlspecialchars($applicant->prop("phone")),
					"fax_value" => htmlspecialchars($applicant->prop_str("fax")),
				));
				if($applicant->class_id() == CL_CRM_PERSON)
				{
					$this->vars(array(
						"code_value" => $applicant->prop("personal_id"),
					));
					$address = $applicant->prop("address");
					$this->vars(array(
						"email_value" => htmlspecialchars($applicant->prop("email.mail")),
						"phone_value" => htmlspecialchars($applicant->prop("phone.name")),
						"fax_value" => htmlspecialchars($applicant->prop("fax.name")),
						"name_caption" => t("Nimi"),
						"reg-code_caption" => ("Isikukood"),
						"name_value" => htmlspecialchars($applicant->prop("firstname"). " " . $applicant->prop("lastname")),
						"P_ADDRESS" => $this->parse("P_ADDRESS"),
					));
				}
				else
				{
					$_SESSION["patent"]["applicants"][$i]["applicant_type"] = "1";
					$this->vars(array(
						"email_value" => htmlspecialchars($applicant->prop("email_id.mail")),
						"phone_value" => htmlspecialchars($applicant->prop("phone_id.name")),
						"fax_value" => htmlspecialchars($applicant->prop("telefax_id.name")),
						"code_value" => htmlspecialchars($applicant->prop("reg_nr")),
						"name_caption" => t("Nimetus"),
						"reg-code_caption" => t("Reg.kood"),
						"CO_ADDRESS" => $this->parse("CO_ADDRESS"),
					));

					$address = $applicant->prop("contact");
				}

				if($this->can("view" , $address))
				{
					$address_obj = obj($address);
					$this->vars(array(
						"street_value" => htmlspecialchars($address_obj->prop("aadress")),
						"index_value" => htmlspecialchars($address_obj->prop("postiindeks")),
						"country_code_value" => $address_inst->get_country_code($address_obj->prop("riik")),
						"city_value" => htmlspecialchars($address_obj->prop_str("linn")),
						"county_value" => htmlspecialchars($address_obj->prop_str("maakond"))
					));
				}

				$correspond_address = $applicant->prop("correspond_address");
				if(is_oid($correspond_address))
				{
					$correspond_address_obj = obj($correspond_address);
					$this->vars(array(
						"correspond_street_value" => htmlspecialchars($correspond_address_obj->prop("aadress")),
						"correspond_index_value" => htmlspecialchars($correspond_address_obj->prop("postiindeks")),
						"correspond_country_code_value" => $address_inst->get_country_code($correspond_address_obj->prop("riik")),
						"correspond_city_value" => htmlspecialchars($correspond_address_obj->prop_str("linn")),
						"correspond_county_value" => htmlspecialchars($correspond_address_obj->prop_str("maakond")),

						"correspond_firstname_value" => htmlspecialchars($correspond_address_obj->meta("correspond_firstname")),
						"correspond_lastname_value" => htmlspecialchars($correspond_address_obj->meta("correspond_lastname ")),
						"correspond_job_value" => htmlspecialchars($correspond_address_obj->meta("correspond_job")),
						"correspond_phone_value" => htmlspecialchars($correspond_address_obj->meta("correspond_phone")),
						"correspond_email_value" => htmlspecialchars($correspond_address_obj->meta("correspond_email"))
					));
				}

				foreach($this->datafromobj_del_vars as $var)
				{
					if($this->vars[$var])
					{
						$str = strtoupper(str_replace("_value","",str_replace("_text","",$var)));
						$this->vars(array($str => $this->parse($str)));
					}
				}

				$applicant_reg = (array) $o->meta("applicant_reg");
				if ($applicant_reg[$applicant->id()])
				{
					$applicant_reg_options = $o->get_applicant_reg_options();
					$applicant_reg = $applicant_reg_options[$applicant_reg[$applicant->id()]];
					$str = strtoupper(str_replace("_value","",str_replace("_text","",$var)));
					$this->vars(array("applicant_reg_value" => $applicant_reg));
					$this->vars(array("APPLICANT_REG" => $this->parse("APPLICANT_REG")));
				}

				if($this->vars["street_value"] || $this->vars["city_value"] || $this->vars["index_value"] || $this->vars["country_code_value"] || $this->vars["county_value"])
				{
					$this->vars(array("ADDRESS" => $this->parse("ADDRESS")));
				}

				if($this->vars["correspond_firstname_value"] || $this->vars["correspond_lastname_value"] || $this->vars["correspond_job_value"] || $this->vars["correspond_phone_value"] || $this->vars["correspond_email_value"] || $this->vars["correspond_street_value"] || $this->vars["correspond_street_value"] || $this->vars["correspond_city_value"] || $this->vars["correspond_index_value"] || $this->vars["correspond_country_code_value"] || $this->vars["correspond_county_value"])
				{
					$this->vars(array("CORRESPOND_ADDRESS" => $this->parse("CORRESPOND_ADDRESS")));
				}

				if($this->vars["phone_value"] || $this->vars["email_value"] || $this->vars["fax_value"])
				{
					$this->vars(array("CONTACT" => $this->parse("CONTACT")));
				}
				$a.= $this->parse("APPLICANT");
				++$i;
			}
		}

		$data = array();
		$data["APPLICANT"] = $a;

		/////////////// author //////////////
		$a = "";
		if($this->is_template("AUTHOR"))
		{
			$author_disallow_disclose = (array) $o->meta("author_disallow_disclose");

			foreach($o->connections_from(array("type" => "RELTYPE_AUTHOR")) as $c)
			{
				foreach($this->datafromobj_del_vars as $del_var)
				{
					unset($this->vars["a_" . $del_var]);
				}

				$author = $c->to();
				$this->vars(array(
					"a_name_value" => htmlspecialchars($author->name()),
					"a_author_disallow_disclose" => $author_disallow_disclose[$author->id()]
				));
				$this->vars(array("A_NAME" => $this->parse("A_NAME")));
				$address = $author->prop("address");

				if($this->can("view" , $address))
				{
					$address_obj = obj($address);
					$this->vars(array(
						"a_street_value" => htmlspecialchars($address_obj->prop("aadress")),
						"a_index_value" => htmlspecialchars($address_obj->prop("postiindeks")),
						"a_country_code_value" => $address_inst->get_country_code($address_obj->prop("riik")),
						"a_city_value" => htmlspecialchars($address_obj->prop_str("linn")),
						"a_county_value" => htmlspecialchars($address_obj->prop_str("maakond"))
					));
				}

				foreach($this->datafromobj_del_vars as $var)
				{
					if($this->vars["a_".$var])
					{
						$str = strtoupper(str_replace("_value","",str_replace("_text","",$var)));
						$this->vars(array("A_" . $str => $this->parse("A_".$str)));
					}
				}

				if($this->vars["a_street_value"] || $this->vars["a_city_value"] || $this->vars["a_index_value"] || $this->vars["a_country_code_value"]
 || $this->vars["a_county_value"])
				{
					$this->vars(array("A_ADDRESS" => $this->parse("A_ADDRESS")));
				}

				$a.= $this->parse("AUTHOR");
			}
		}

		$data["AUTHOR"] = $a;

		if(is_oid($o->prop("authorized_person")))
		{
			$ap = obj($o->prop("authorized_person"));
			$data["authorized_person_firstname_value"] = htmlspecialchars($ap->prop("firstname"));
			$data["authorized_person_lastname_value"] = htmlspecialchars($ap->prop("lastname"));
			$data["authorized_person_code_value"] = htmlspecialchars($ap->prop("personal_id"));
		}

		foreach($this->text_area_vars as $prop)
		{
			$data[$prop."_value"] = htmlspecialchars($o->prop($prop));
		}

		foreach($this->select_vars as $var)
		{
			if ("applicant_reg" !== $var)
			{
				$data[$prop."_value"] = htmlspecialchars($o->prop_str($var));
			}
		}

		foreach($this->datafromobj_vars as $prop)
		{
			$data[$prop."_value"] = htmlspecialchars($o->prop_str($prop));

			if ("fee_copies_info" === $prop and $o->prop($prop))
			{
				$data["fee_copies_info_value"] = patent_patent_obj::COPIES_FEE;
			}
		}

		foreach($this->date_vars as $prop)
		{
			$val = $o->prop($prop);

			if(((int) $val) !== -1 and "" !== $val)
			{
				$data[$prop."_value"] = date("j.m.Y" , (int) $val);
			}
			else
			{
				unset($data[$prop."_value"]);
			}
		}

		$file_inst = get_instance(CL_FILE);

		foreach($this->multifile_upload_vars as $prop)
		{
			$ol = new object_list ($o->connections_from(array(
				"type" => "RELTYPE_" . strtoupper($prop),
			)));
			$files = array();
			foreach ($ol->arr() as $file)
			{
				if ($prop === $file->meta("po_prop_name"))
				{
					$files[] = html::href(array(
						"url" => str_replace("https" , "http" , $file_inst->get_url($file->id(), $file->name())),
						"caption" => htmlspecialchars($file->name()),
						"target" => "_blank"
					));
				}
			}
			$data[$prop . "_value"] = implode(", ", $files);
		}

		classload("common/digidoc/ddoc_parser");
		foreach($this->file_upload_vars as $var)
		{
			if(is_oid($o->prop($var)))
			{
				$file = obj($o->prop($var));
				if($var === "reproduction")
				{
					$data[$var."_value"] = str_replace("https" , "http" , $this->get_right_size_image($file->id()));
				}
				elseif($var === "warrant")
				{
					$data[$var."_value"] = html::href(array(
 							"caption" =>  htmlspecialchars($file->name()),
							"target" => "_blank",
							"url" => $this->mk_my_orb("get_file", array(
 							"oid" => $file->id(),
						)),
					));
				}
				else
				{
					$data[$var."_value"] = html::href(array(
						"url" => str_replace("https" , "http" , $file_inst->get_url($file->id(), $file->name())),
						"caption" => htmlspecialchars($file->name()),
						"target" => "_blank",
					));
				}
			}
		}

		$_SESSION["patent"]["products"] = $o->meta("products");
		$data["fee_sum_info_value"] = $this->get_payment_sum();
		$data["request_fee_info_value"] = $this->get_request_fee();

		if (method_exists($this, "get_add_fee"))
		{
			$data["add_fee_info_value"] = $this->get_add_fee();
		}

		$data["procurator_text"] = htmlspecialchars($o->prop_str("procurator"));
		$data["signatures"] = $this->get_signatures($id);
		return $data;
	}

	/**
		@attrib name=show_payment_ordermake  params=name all_args=1 api=1
	**/
	function show_payment_order($arr)
	{
		$file_inst = get_instance(CL_FILE);
		$mm_type="application/octet-stream";
		$fc = $file_inst->get_file_by_id($arr["id"]);
		header("Cache-Control: public, must-revalidate");
		header("Pragma: hack");
		header("Content-Type: " . $mm_type);
		header("Content-Length: " . filesize($fc["properties"]["file"]));
		header('Content-Disposition: attachment; filename="'.$fc["name"].'"');
		header("Content-Transfer-Encoding: binary\n");
		$fp = fopen($fc["properties"]["file"], 'rb');
		$buffer = fread($fp, filesize($fc["properties"]["file"]));
		fclose ($fp);
		header("Content-Length: " .(string)(filesize($fc["properties"]["file"])) );
		print $buffer;
	}

	/**
		@attrib name=get_file params=name all_args=1 api=1
		@comment
			saves the ddoc file (browser save popup)
	**/
	function get_file($arr)
	{
		$file_inst = get_instance(CL_FILE);
		$ddinst = get_instance(CL_DDOC);
		classload("common/digidoc/ddoc_parser");
		$fc = $file_inst->get_file_by_id($arr["oid"]);
		$content = $fc["content"];
		$o = obj($arr["oid"]);
		$ddinst->do_init();
		$name = $o->name();
		ddFile::saveAs($name, $content,'jpg');
	}

	function fill_session($id)
	{
		$_SESSION["patent"] = array();
		$_SESSION["patent"]["id"] = $id;
		$patent = obj($id);

		foreach($this->text_vars as $var)
		{
			$_SESSION["patent"][$var] = $patent->prop($var);
		}

		foreach($this->checkbox_vars as $var)
		{
			if ("author_disallow_disclose" !== $var)
			{
				$_SESSION["patent"][$var] = $patent->prop($var);
			}
		}

		foreach($this->chooser_vars as $var)
		{
			$_SESSION["patent"][$var] = $patent->prop_str($var);
		}

		foreach($this->select_vars as $var)
		{
			if ("applicant_reg" !== $var)
			{
				$fn = "get_" . $var . "_options";
				$options = $patent->$fn();
				$_SESSION["patent"][$var] = $options[$patent->prop($var)];
			}
		}

		foreach($this->text_area_vars as $var)
		{
			$_SESSION["patent"][$var] = $patent->prop($var);
		}

		foreach($this->date_vars as $var)
		{
			$_SESSION["patent"][$var] = $patent->prop($var);
		}

		foreach($this->file_upload_vars as $var)
		{
			$_SESSION["patent"][$var] = $patent->prop($var);
		}

		foreach($this->multifile_upload_vars as $var)
		{
			$ol = new object_list ($patent->connections_from(array(
				"type" => "RELTYPE_" . strtoupper($var),
			)));
			foreach ($ol->arr() as $o)
			{
				if ($var === $o->meta("po_prop_name"))
				{
					$_SESSION["patent"][$var][] = $o->id();
				}
			}
		}

		$address_inst = get_instance(CL_CRM_ADDRESS);
		$_SESSION["patent"]["procurator"] = $patent->prop("procurator");
		$applicant_reg = (array) $patent->meta("applicant_reg");

		foreach($patent->connections_from(array("type" => "RELTYPE_APPLICANT")) as $key => $c)
		{
			$o = $c->to();
			$key = $o->id();
			$_SESSION["patent"]["applicants"][$key]["name"] = $o->name();
			$_SESSION["patent"]["applicants"][$key]["applicant_reg"] = $applicant_reg[$o->id()];

			if($o->class_id() == CL_CRM_COMPANY)
			{
				$_SESSION["patent"]["applicants"][$key]["applicant_type"] = 1;
				$address = $o->prop("contact");
				$_SESSION["patent"]["applicants"][$key]["phone"] = $o->prop("phone_id.name");
				$_SESSION["patent"]["applicants"][$key]["email"] = $o->prop("email_id.mail");
				$_SESSION["patent"]["applicants"][$key]["fax"] = $o->prop("telefax_id.name");
				$_SESSION["patent"]["applicants"][$key]["code"] = $o->prop("reg_nr");
			}
			else
			{
				$_SESSION["patent"]["applicants"][$key]["applicant_type"] = 0;
				$_SESSION["patent"]["applicants"][$key]["firstname"] = $o->prop("firstname");
				$_SESSION["patent"]["applicants"][$key]["lastname"] = $o->prop("lastname");
				$address = $o->prop("address");
				$correspond_address = $o->prop("correspond_address");
				$_SESSION["patent"]["applicants"][$key]["phone"] = $o->prop("phone.name");
				$_SESSION["patent"]["applicants"][$key]["email"] = $o->prop("email.mail");
				$_SESSION["patent"]["applicants"][$key]["fax"] = $o->prop("fax.name");
				$_SESSION["patent"]["applicants"][$key]["code"] = $o->prop("personal_id");
			}

			if($this->can("view" , $address))
			{
				$address_obj = obj($address);
				$_SESSION["patent"]["applicants"][$key]["street"] = $address_obj->prop("aadress");
				$_SESSION["patent"]["applicants"][$key]["index"] = $address_obj->prop("postiindeks");

				if($this->can("view" , $address_obj->prop("linn")))
				{
					$city = obj($address_obj->prop("linn"));
					$_SESSION["patent"]["applicants"][$key]["city"] = $city->name();
				}

				if($this->can("view" , $address_obj->prop("maakond")))
				{
					$county = obj($address_obj->prop("maakond"));
					$_SESSION["patent"]["applicants"][$key]["county"] = $county->name();
				}

				$_SESSION["patent"]["applicants"][$key]["country_code"] = $address_inst->get_country_code($address_obj->prop("riik"));
				if($_SESSION["patent"]["applicants"][$key]["country_code"] == "EE")
				{
					$_SESSION["patent"]["applicants"][$key]["country"] = "0";
				}
				else
				{
					$_SESSION["patent"]["applicants"][$key]["country"] = "1";
				}
			}

			if($this->can("view" , $correspond_address))
			{
				$correspond_address_obj = obj($correspond_address);
				$_SESSION["patent"]["applicants"][$key]["correspond_street"] = $correspond_address_obj->prop("aadress");
				$_SESSION["patent"]["applicants"][$key]["correspond_index"] = $correspond_address_obj->prop("postiindeks");
				$_SESSION["patent"]["applicants"][$key]["correspond_country_code"] = $address_inst->get_country_code($correspond_address_obj->prop("riik"));

				$_SESSION["patent"]["applicants"][$key]["correspond_firstname"] = $correspond_address_obj->meta("firstname");
				$_SESSION["patent"]["applicants"][$key]["correspond_lastname"] = $correspond_address_obj->meta("lastname");
				$_SESSION["patent"]["applicants"][$key]["correspond_job"] = $correspond_address_obj->meta("job");
				$_SESSION["patent"]["applicants"][$key]["correspond_phone"] = $correspond_address_obj->meta("phone");
				$_SESSION["patent"]["applicants"][$key]["correspond_email	"] = $correspond_address_obj->meta("email");

				if($this->can("view" , $correspond_address_obj->prop("linn")))
				{
					$city = obj($correspond_address_obj->prop("linn"));
					$_SESSION["patent"]["applicants"][$key]["correspond_city"] = $city->name();
				}

				if($this->can("view" , $correspond_address_obj->prop("maakond")))
				{
					$county = obj($correspond_address_obj->prop("maakond"));
					$_SESSION["patent"]["applicants"][$key]["correspond_county"] = $county->name();
				}
			}
		}

		if(is_oid($patent->prop("authorized_person")) && $this->can("view" , $patent->prop("authorized_person")))
		{
			$authorized_person = obj($patent->prop("authorized_person"));
			$_SESSION["patent"]["authorized_person_firstname"] = $authorized_person->prop("firstname");
			$_SESSION["patent"]["authorized_person_lastname"] = $authorized_person->prop("lastname");
			$_SESSION["patent"]["authorized_person_code"] = $authorized_person->prop("personal_id");
		}
	}

	function check_and_give_rights($oid)
	{
		$o = obj($oid);
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		$name = $p->name();
		if($name && $name === $o->name())
		{
			$uo = obj(aw_global_get("uid_oid"));
			$grp = obj($uo->get_default_group());
			$o->acl_set($grp, array("can_view" => 1, "can_edit" => 1, "can_delete" => 0));
		}
	}

	/**
		@attrib name=parse_alias is_public="1" caption="Change"
	**/
	function parse_alias($arr)
	{
		enter_function("patent::parse_alias");

		if(!$_SESSION["patent"]["data_type"])
		{
			$_SESSION["patent"]["data_type"] = "0";
		}

		if (!empty($_GET["new_application"]))
		{
			$_SESSION["patent"] = null;
			unset($_GET["new_application"]);
		}

		if(isset($_GET["data_type"]))
		{
			$arr["data_type"] = $_GET["data_type"];
		}
		else
		{
			$arr["data_type"] = $_SESSION["patent"]["data_type"];
		}

		/// remove vars for author view
		if($arr["data_type"] === "11")
		{
			foreach ($this->author_vars as $var)
			{
				$_SESSION["patent"][$var] = null;
			}
		}
		elseif (!$arr["data_type"])
		{
			foreach ($this->applicant_vars as $var)
			{
				$_SESSION["patent"][$var] = null;
			}
		}

		if($arr["data_type"] === "6")
		{
			exit_function("patent::parse_alias");
			return $this->my_patent_list($arr);//$this->mk_my_orb("my_patent_list", array());
		}

		if($arr["data_type"] === "7")
		{
			$arr["unsigned"] = 1;
			exit_function("patent::parse_alias");
			return $this->my_patent_list($arr);//$this->mk_my_orb("my_patent_list", array());
		}

		if($this->can("view", $_GET["trademark_id"]))
		{
			$_SESSION["patent"] = null;
			$_SESSION["patent"]["id"] = $_GET["trademark_id"];
			$this->fill_session($_GET["trademark_id"]);
			$this->check_and_give_rights($_GET["trademark_id"]);
			header("Location:".$_SERVER["SCRIPT_URI"]."?section=".$_GET["section"]."&data_type=0");
			exit_function("patent::parse_alias");
			exit;
		}

		if(is_oid($_SESSION["patent"]["id"]))
		{
			$o = obj($_SESSION["patent"]["id"]);
			$status = $this->get_status($o);
			if($status->prop("nr") || $status->prop("verified"))
			{
				exit_function("patent::parse_alias");
				return $this->show(array(
					"id" => $o->id(),
					"add_obj" => $arr["alias"]["to"],
				));
			}
		}

		$tpl = $this->info_levels[$arr["data_type"]].".tpl";
		$this->read_template($tpl);
		lc_site_load("patent", $this);
		$this->vars($this->web_data($arr));

		// $this->vars(array("form_handler" => $_SERVER["SCRIPT_URI"]));

		$this->vars(array("reforb" => $this->mk_reforb("submit_data",array(
				"data_type"	=> $arr["data_type"],
				"return_url" 	=> post_ru(),
				"add_obj" 	=> $arr["alias"]["to"],
			)),
		));

		//l6petab ja salvestab
		if($arr["data_type"] === "5")
		{
			$this->vars(array("reforb" => $this->mk_reforb("submit_data",array(
					"save" => 1,
					"return_url" 	=> post_ru(),
					"add_obj" 	=> $arr["alias"]["to"],
				)),
			));
		}

		exit_function("patent::parse_alias");
		return $this->parse();
	}

	function _get_applicant_data()
	{
		$n = $_SESSION["patent"]["applicant_id"];
		foreach($_SESSION["patent"]["applicants"][$n] as $var => $val)
		{
			$_SESSION["patent"][$var] = $val;
		}
	}

	function _get_author_data()
	{
		$n = $_SESSION["patent"]["author_id"];
		foreach($_SESSION["patent"]["authors"][$n] as $var => $val)
		{
			$_SESSION["patent"][$var] = $val;
		}
	}

	function get_user_data()
	{
		if(is_array($_SESSION["patent"]["applicants"] && sizeof($_SESSION["patent"]["applicants"])))
		{
			return;
		}
		$us = get_instance(CL_USER);
		$this->users_person = new object($us->get_current_person());
	}

	function get_js($arr)
	{
		$js = "";

		if(!$_GET["data_type"])
		{
			if(!is_oid($_SESSION["patent"]["procurator"]))
			{
				$js.= 'document.getElementById("warrant_row").style.display = "none";';
				$js.= 'document.getElementById("remove_procurator").style.display = "none";';
			}

			if($_SESSION["patent"]["applicant_type"])
			{
				$js.='document.getElementById("lastname_row").style.display = "none";
				document.getElementById("firstname_row").style.display = "none";

				document.getElementById("p_adr").style.display="none";
				document.getElementById("livingplace_type").style.display="none";
				';
			}
			else
			{
				if ("industrial_design" === get_class($this))
				{
					$js .= 'document.getElementById("reg_code").style.display = "none";';
				}
				$js.='
				document.getElementById("name_row").style.display = "none";
				document.getElementById("co_adr").style.display="none";
				document.getElementById("co_livingplace_type").style.display="none";
				';
			}
		}
		return $js;
	}

	/**
		@attrib name=error_popup all_args=1
	**/
	function error_popup($arr)
	{
		die($arr["error"]."\n<br />"."<input type=button value='OK' onclick='javascript:window.close();'>");
	}

	function get_applicant_sub()
	{
		$a = "";
		foreach($_SESSION["patent"]["applicants"] as $key => $val)
		{
			foreach($this->applicant_vars as $var)
			{
				if($var === "name" && !$_SESSION["patent"]["applicants"][$key][$var])
				{
					$_SESSION["patent"]["applicants"][$key][$var] = $_SESSION["patent"]["applicants"][$key]["firstname"]." ".$_SESSION["patent"]["applicants"][$key]["lastname"];
				}

				if("applicant_reg" === $var and $_SESSION["patent"]["applicants"][$key]["applicant_reg"])
				{
					$fn = "return " . get_class($this) . "_obj::get_applicant_reg_options();";
					$options = eval($fn);
					$this->vars(array($var."_value" => $options[$_SESSION["patent"]["applicants"][$key][$var]]));
				}
				else
				{
					$this->vars(array($var."_value" => $_SESSION["patent"]["applicants"][$key][$var]));
				}

				if($_SESSION["patent"]["applicants"][$key]["type"])
				{
					$this->vars(array("name_caption" => t("Nimetus"),
						"reg-code_caption" => t("Reg.kood"),
						"CO_ADDRESS" => $this->parse("CO_ADDRESS"),
						"P_ADDRESS" => "",
					));
				}
				else
				{
					$this->vars(array("name_caption" => t("Nimi"),
						"reg-code_caption" => t("Isikukood"),
						"P_ADDRESS" => $this->parse("P_ADDRESS"),
						"CO_ADDRESS" => "",
					));
				}

				if($_SESSION["patent"]["applicants"][$key][$var])
				{
					$str = strtoupper($var);
					$this->vars(array($str => $this->parse($str)));
				}
			}
 			if($this->vars["street_value"] || $this->vars["city_value"] || $this->vars["index_value"] || $this->vars["country_code_value"]
 || $this->vars["county_value"])
 			{
 				$this->vars(array("ADDRESS" => $this->parse("ADDRESS")));
 			}

 			if($this->vars["correspond_firstname_value"] || $this->vars["correspond_lastname_value"] || $this->vars["correspond_job_value"] || $this->vars["correspond_phone_value"] || $this->vars["correspond_email_value"] || $this->vars["correspond_street_value"] || $this->vars["correspond_city_value"] || $this->vars["correspond_index_value"] || $this->vars["correspond_country_code_value"] || $this->vars["correspond_county_value"])
 			{
 				$this->vars(array("CORRESPOND_ADDRESS" => $this->parse("CORRESPOND_ADDRESS")));
 			}

 			if($this->vars["phone_value"] || $this->vars["email_value"] || $this->vars["fax_value"])
 			{
 				$this->vars(array("CONTACT" => $this->parse("CONTACT")));
 			}
 			$a.= $this->parse("APPLICANT");
		}
		return $a;
	}

	function get_author_sub()
	{
		$author_vars = $this->author_vars;
		$a = "";
		foreach($_SESSION["patent"]["authors"] as $key => $val)
		{
			foreach($this->datafromobj_del_vars as $del_var)
			{
				unset($this->vars["a_".$del_var]);
			}

			foreach($author_vars as $var)
			{
				if($var === "name" && !$_SESSION["patent"]["authors"][$key][$var])
				{
					$_SESSION["patent"]["authors"][$key][$var] = $_SESSION["patent"]["authors"][$key]["firstname"]." ".$_SESSION["patent"]["authors"][$key]["lastname"];
				}
				$this->vars(array("a_{$var}_value" => $_SESSION["patent"]["authors"][$key][$var]));

				$str = "A_" . strtoupper($var);
				if($_SESSION["patent"]["authors"][$key][$var])
				{
					$this->vars(array($str => $this->parse($str)));
				}
				else
				{
					$this->vars(array($str => null));
				}
			}

 			if($this->vars["a_street_value"] || $this->vars["a_city_value"] || $this->vars["a_index_value"] || $this->vars["a_country_code_value"]
 || $this->vars["a_county_value"])
 			{
 				$this->vars(array("A_ADDRESS" => $this->parse("A_ADDRESS")));
 			}
 			$a.= $this->parse("AUTHOR");
		}
		return $a;
	}

	function web_data($arr)
	{
		$data = $this->get_vars($arr);
		$data["data_type"] = $arr["data_type"];
		$data["data_type_name"] = $this->info_levels[$arr["data_type"]];
		$this->get_user_data($arr);
		$htmlclient = new htmlclient();

		if($this->is_template("APPLICANT"))
		{
			$data["APPLICANT"] = $this->get_applicant_sub();
		}

		if($this->is_template("AUTHOR"))
		{
			$data["AUTHOR"] = $this->get_author_sub();
		}

		$data["js"] = $this->get_js();

		foreach ($this->text_vars as $var)
		{
			$data[$var] = html::textbox(array(
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"size" => 40
			));
		}

		foreach ($this->save_fee_vars as $var)
		{
			$data[$var] = html::textbox(array(
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"size" => 4
			));
		}

		foreach($this->text_area_vars as $var)
		{
			$data[$var] = html::textarea(array(
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"cols"=> 40,
				"rows"=> 10
			));
		}

		foreach($this->select_vars as $var)
		{
			$fn = "return " . get_class($this) . "_obj::get_{$var}_options();";
			$options = eval($fn);
			$el_cfg = array(
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"options"=> $options
			);

			// if ("industrial_design_variant" === $var)
			// {
				// $el_cfg["onchange"] = "showVarCountSelect();";
			// }

			$data[$var] = html::select($el_cfg);
		}

		foreach($this->chooser_vars as $var)
		{
			$fn = "return " . get_class($this) . "_obj::get_{$var}_options();";
			$options = eval($fn);

			$data[$var] = $htmlclient->draw_element(array(
				"type" => "chooser",
				"orient" => "vertical",
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"options"=> $options
			));
		}

		foreach ($this->checkbox_vars as $var)
		{
			$el_cfg = array(
				"value" => 1,
				"name" => $var,
				"checked" => $_SESSION["patent"][$var]
			);

			$data[$var] = html::checkbox($el_cfg);
		}

		foreach($_SESSION["patent"] as $key => $val)
		{
			$data[$key."_value"] =  $val;

			if ("fee_copies_info" === $key and $val)
			{
				$data["fee_copies_info_value"] = patent_patent_obj::COPIES_FEE;
			}
		}

		$data["sum_value"] = $this->get_payment_sum();

		$file_inst = get_instance(CL_FILE);

		foreach($this->multifile_upload_vars as $prop)
		{
			$mf_upload = new multifile_upload();
			$args = array(
				"new" => !is_oid($_SESSION["patent"]["id"]),
				"name" => $prop,
				"prop" => array(
					"reltype" => "RELTYPE_" . strtoupper($prop),
					"name" => $prop,
					"type" => "multifile_upload"
				)
			);

			if (is_oid($_SESSION["patent"]["id"]))
			{
				$args["obj_inst"] = new object($_SESSION["patent"]["id"]);
			}

			$res = $mf_upload->init_vcl_property($args);
			$data[$prop] = $htmlclient->draw_element(reset($res));

			$files = array();
			foreach ($_SESSION["patent"][$prop] as $id)
			{
				$file = new object($id);
				if ($prop === $file->meta("po_prop_name"))
				{
					$files[] = html::href(array(
						"url" => $file_inst->get_url($file->id(), $file->name()),
						"caption" => $file->name(),
						"target" => "_blank"
					));
				}
			}
			$data[$prop."_value"] = implode(", ", $files);
		}

		foreach($this->file_upload_vars as $var)
		{
			$data[$var] = html::fileupload(array("name" => $var."_upload"));
			if($this->can("view", $_SESSION["patent"][$var]))
			{
				$file = obj($_SESSION["patent"][$var]);
				if("reproduction" === $var)
				{
					$data[$var."_value"] = $this->get_right_size_image($_SESSION["patent"][$var]);
				}
				elseif($var === "warrant")
				{
					$data[$var."_value"] = html::href(array(
 							"caption" =>  $file->name(),
							"target" => "_blank",
							"url" => $this->mk_my_orb("get_file", array(
 							"oid" => $file->id(),
						)),
					));
				}
				else
				{
					$data[$var."_value"] = html::href(array(
						"url" => $file_inst->get_url($file->id(), $file->name()),
						"caption" => $file->name(),
						"target" => "_blank",
					));
				}
			}
		}

		foreach($this->date_vars as $var)
		{
			if(is_array($_SESSION["patent"][$var]))
			{
				if (in_array("---", $_SESSION["patent"][$var]))
				{
					$_SESSION["patent"][$var] = -1;
				}
				else
				{
					$_SESSION["patent"][$var] = mktime(0,0,0, $_SESSION["patent"][$var]["month"], $_SESSION["patent"][$var]["day"], $_SESSION["patent"][$var]["year"]);
				}
			}

			$args = array(
				"name" => $var,
				"value" => $_SESSION["patent"][$var],
				"default" => -1,
				"buttons" => 1
			);

			if ("epat_date" === $var)
			{
				$args["year_from"] = 2002;
			}
			elseif ("payment_date" === $var)
			{
				$args["year_from"] = date("Y", (time() - 93*86400));
				$args["year_to"] = date("Y");
			}
			else
			{
				$args["year_from"] = 1950;
			}

			$data[$var] = html::date_select($args);

			if(((int)$_SESSION["patent"][$var]) !== -1)
			{
				$data[$var."_value"] = date("j.m.Y", $_SESSION["patent"][$var]);
			}
		}

		//siia siis miski tingimus, et on makstud jne... siis ei tohi muuta saada enam
		if(!is_array($_SESSION["patent"]["payment_date"]) && !($_SESSION["patent"]["payment_date"]>1))
		{
			$data["payment_date_value"] = null;
		}

		if (CL_PATENT === $this->clid)
		{
			if($_SESSION["patent"]["reproduction"])
			{
				$data["image_set"] = 1;
			}
		}

		if($_SESSION["patent"]["errors"])
		{
			$data["error"] = $_SESSION['patent']['errors'];
			$_SESSION["patent"]["errors"] = null;
		}
		$data["signatures"] = $this->get_signatures($_SESSION["patent"]["id"]);
		return $data;
	}

	function get_signatures($id)
	{
		if(!aw_ini_get("file.ddoc_support"))
		{
			return "";
		}
		if(is_oid($id))
		{
			$re = $this->is_signed($id);
		}
		if($re["status"] != 1)
		{
			return "";
		}
		$ddoc_inst = get_instance(CL_DDOC);
		aw_disable_acl();
		$signs = $ddoc_inst->get_signatures($re["ddoc"]);
		aw_restore_acl();
		foreach($signs as $sig)
		{
			$sig_nice[] = sprintf(t("%s, %s  - %s"), $sig["signer_ln"], $sig["signer_fn"], date("H:i d/m/Y", $sig["signing_time"]));
		}
		$prop["value"] = join("<br />", $sig_nice);
		return $prop["value"];
	}

	function get_right_size_image($oid)
	{
		$image_inst = get_instance(CL_IMAGE);
		$image = obj($oid);
		$fl = $image->prop("file");
		if (!empty($fl))
		{
			// rewrite $fl to be correct if site moved
			$fl = basename($fl);
			$fl = $this->cfg["site_basedir"]."/files/".$fl{0}."/".$fl;
			$sz = @getimagesize($fl);
		}
		if($sz[0] > 200)
		{
			$sz[1] = ($sz[1]/($sz[0]/200)) % 200001;
			$sz[0] = 200;
		}
		$ret =  $image_inst->make_img_tag_wl($oid, "", "" , array(
				"height" => $sz[1],
				"width" => $sz[0],
		));
		return $ret;
	}

	function get_vars($arr)
	{
		$data = array();

		////////////// applicants //////////////
		if(!empty($_SESSION["patent"]["delete_applicant"]))
		{
			unset($_SESSION["patent"]["applicants"][$_SESSION["patent"]["delete_applicant"]]);
			unset($_SESSION["patent"]["delete_applicant"]);
		}
		elseif($_SESSION["patent"]["add_new_applicant"])
		{
			$_SESSION["patent"]["add_new_applicant"] = null;
			$_SESSION["patent"]["change_applicant"] = null;
			$_SESSION["patent"]["applicant_id"] = null;
		}
		elseif(strlen(trim(($_SESSION["patent"]["applicant_id"]))))
		{
			$this->_get_applicant_data();
			$data["change_applicant"] = $_SESSION["patent"]["applicant_id"];
			$_SESSION["patent"]["change_applicant"] = null;
			$_SESSION["patent"]["applicant_id"] = null;
		}
		else
		{
			$data["applicant_no"] = sizeof($_SESSION["patent"]["applicants"]) + 1;
		}
		unset($_SESSION["patent"]["delete_applicant"]);
		//nendesse ka siis see tingumus, et muuta ei saa

/*
		if(!$_SESSION["patent"]["payer"])
		{
			if (isset($_SESSION["patent"]["representer"]))
			{
				if($_SESSION["patent"]["applicants"][$_SESSION["patent"]["representer"]]["applicant_type"] == "1")
				{
					$data["payer"] = $_SESSION["patent"]["payer"] = $_SESSION["patent"]["applicants"][$_SESSION["patent"]["representer"]]["name"];
				}
				else
				{
					$data["payer"] =  $_SESSION["patent"]["payer"] = $_SESSION["patent"]["applicants"][$_SESSION["patent"]["representer"]]["firstname"]." ".$_SESSION["patent"]["applicants"][$_SESSION["patent"]["representer"]]["lastname"];
				}
			}
			else
			{
				$payer = reset($_SESSION["patent"]["applicants"]);
				$data["payer"] = $payer["name"];
			}
		}
*/
		$data["country"] = t("Eesti ").html::radiobutton(array(
			"value" => "0",
			"checked" => (!$_SESSION["patent"]["country"] && isset($_SESSION["patent"]["country"])) ? 1 : 0,
			"name" => "country",
			"onclick" => 'document.getElementById("contactPopupLink").style.display="none"; document.getElementById("country_code").value = "EE";',
		)).t("&nbsp;&nbsp;&nbsp;&nbsp;V&auml;lismaa ").html::radiobutton(array(
			"value" => "1",
			"checked" => $_SESSION["patent"]["country"],
			"name" => "country",
			"onclick" => 'document.getElementById("contactPopupLink").style.display=""; document.getElementById("country_code").value = "";',
		));

		$show_reg_code = false;
		if ("patent" === get_class($this))
		{
			$show_reg_code = true;
		}

		$data["applicant_type"] = t("F&uuml;&uuml;siline isik ").html::radiobutton(array(
			"value" => 0,
			"checked" => (!$_SESSION["patent"]["applicant_type"]) ? 1 : 0,
			"name" => "applicant_type",
			"onclick" => 'document.getElementById("firstname_row").style.display = "";
			document.getElementById("lastname_row").style.display = "";
			document.getElementById("name_row").style.display = "none";
			document.getElementById("name").value = "";
			document.getElementById("code").value = "";
			' . ($show_reg_code ? 'document.getElementById("reg_code").style.display="none";' : '') . '
			document.getElementById("p_adr").style.display="";
			document.getElementById("co_adr").style.display="none";
			document.getElementById("co_livingplace_type").style.display="none";
			document.getElementById("livingplace_type").style.display="";
			',
		)).t("&nbsp;&nbsp;&nbsp;&nbsp; Juriidiline isik ").html::radiobutton(array(
			"value" => 1,
			"checked" => $_SESSION["patent"]["applicant_type"],
			"name" => "applicant_type",
			"onclick" => 'document.getElementById("firstname_row").style.display = "none"; document.getElementById("lastname_row").style.display = "none"; document.getElementById("name_row").style.display = "";
			document.getElementById("firstname").value = "";
			document.getElementById("lastname").value = "";
			' . ($show_reg_code ? 'document.getElementById("reg_code").style.display="";' : '') . '
			document.getElementById("p_adr").style.display="none";
			document.getElementById("co_adr").style.display="";
			document.getElementById("livingplace_type").style.display="none";
			document.getElementById("co_livingplace_type").style.display="";
			',
		));

		if($_SESSION["patent"]["applicant_type"])
		{
			$data["CO_ADDRESS"] = $this->parse("CO_ADDRESS");
		}
		else
		{
			$data["P_ADDRESS"] = $this->parse("P_ADDRESS");
		}

		$dummy = obj($arr["alias"]["to"]);
		$_SESSION["patent"]["parent"] = $data["parent"] = $dummy->prop("trademarks_menu");

		if($this->can("view" , $_SESSION["patent"]["procurator"]))
		{
			$procurator = obj($_SESSION["patent"]["procurator"]);
			$procurator_name = $procurator->name();
			$data["procurator_text"] = $procurator_name;
		}

		if (aw_global_get("uid") != "")
		{
			$pop_str = t("Vali");
		}
		else
		{
			$pop_str = "";
		}

		$data["procurator"] = html::hidden(array(
				"name" => "procurator",
				"value" => $_SESSION["patent"]["procurator"],
			))."<span id='procurator_name'> ".$procurator_name." </span>&nbsp;".html::href(array(
			"caption" => $pop_str ,
			"url"=> "javascript:void(0);",
			"onclick" => 'javascript:window.open("'.$this->mk_my_orb("procurator_popup", array("print" => 1 , "parent" => $dummy->prop("procurator_menu"))).'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");'
		));

		$data["remove_procurator"] = html::href(array(
			"caption" => t("Eemalda") ,
			"url"=> "javascript:void(0);",
			"onclick" => 'javascript:
				window.document.getElementById("procurator").value= "";
				window.document.getElementById("procurator_name").innerHTML= "";
				window.document.getElementById("warrant_row").style.display = "none";
				window.document.getElementById("remove_procurator").style.display = "none";'
		));

		$data["add_new_applicant"] = html::radiobutton(array(
				"value" => 1,
				"checked" => 0,
				"name" => "add_new_applicant",
		));

		if(is_array($_SESSION["patent"]["applicants"]) && sizeof($_SESSION["patent"]["applicants"]))
		{
			$data["applicants_table"] = $this->_get_applicants_table();
		}
		////////////// END applicants //////////////

		foreach($this->country_popup_link_vars as $var)
		{
			$data[$var."_popup_link"] = html::href(array(
				"caption" => $pop_str ,
				"url"=> "javascript:void(0);",
				"onclick" => 'javascript:window.open("'.$this->mk_my_orb("country_popup", array("print" => 1 , "var" => $var)).'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
			));
		}

		if(is_oid($dummy->prop("bank_payment")) && $this->can("view" , $dummy->prop("bank_payment")))
		{
			$bank_inst = get_instance("common/bank_payment");
			$data["banks"] = $bank_inst->bank_forms(array("id" => $dummy->prop("bank_payment") , "amount" => $this->get_payment_sum()));
		}

/* 		$data["payer_popup_link"] = html::href(array(
			"caption" => t("Vali") ,
			"url"=> "javascript:void(0);",
			"onclick" => 'javascript:window.open("'.$this->mk_my_orb("payer_popup", array("print" => 1)).'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
		));
 */
		$_SESSION["patent"]["fee_sum_info"] = $this->get_payment_sum();
		$data["fee_sum_info"] = $_SESSION["patent"]["fee_sum_info"];
		$_SESSION["patent"]["request_fee_info"] = $this->get_request_fee();
		$data["request_fee_info"]= $_SESSION["patent"]["request_fee_info"];

		$data["show_link"] = "javascript:window.open('".$this->mk_my_orb("show", array("print" => 1 , "id" => $_SESSION["patent"]["trademark_id"], "add_obj" => $arr["alias"]["to"]))."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=600, width=800')";

		$data["convert_link"] = $this->mk_my_orb("pdf", array("print" => 1 , 	"id" => $_SESSION["patent"]["id"], "add_obj" => $arr["alias"]["to"]) , CL_PATENT);

		if(sizeof($_SESSION["patent"]["applicants"]))
		{
			$data["forward"] = '<input type="submit" value="Edasi"  class="nupp">';
		}

		// allkirjastaja ametinimetus
		// if(!is_oid($_SESSION["patent"]["procurator"]))
		// {
			$job_show = false;
			foreach ($_SESSION["patent"]["applicants"] as $applicant_data)
			{
				if ("1" == $applicant_data["applicant_type"])
				{
					$job_show = true;
				}
			}

			if (isset($_SESSION["patent"]["representer"]) and !($_SESSION["patent"]["applicants"][$_SESSION["patent"]["representer"]]["applicant_type"] == "1"))
			{
				$job_show = false;
			}

			if ($job_show)
			{
				$job = html::textbox(array(
					"name" => "job",
					"size" => 40,
					"value" => $_SESSION["patent"]["job"]
				));
				$this->vars(array("signer_job" => $job));
				$data["SIGNER_JOB_TITLE"] = $this->parse("SIGNER_JOB_TITLE");
			}
		// }

		//
		if(is_oid($_SESSION["patent"]["id"]))
		{
			$ddoc_inst = get_instance(CL_DDOC);
			$status = $this->is_signed($_SESSION["patent"]["id"]);

			if($status["status"] > 0)
			{
				$url = $ddoc_inst->sign_url(array(
					"ddoc_oid" =>$status["ddoc"],
				));
			}
			else
			{
				$url = $ddoc_inst->sign_url(array(
					"other_oid" =>$_SESSION["patent"]["id"],
				));
			}

			if($status["status"] > 0)
			{
				$data["SIGNED"] = $this->parse("SIGNED");
			}
			else
			{
				$data["UNSIGNED"] = $this->parse("UNSIGNED");
			}

			$data["sign_button"] = '<input type="button" value="3. Allkirjasta taotlus" class="nupp" onclick="aw_popup_scroll(\''.$url.'\', \''.t("Allkirjastamine").'\', 410, 250);"><br />';
		}
		else
		{
			$data["UNSIGNED"] = $this->parse("UNSIGNED");
		}
		return $data;
	}

	/**
		@attrib name=pdf all_args=1
	**/
	function pdf($arr)
	{
		$conv = get_instance("core/converters/html2pdf");
		ob_start();
		print $this->show(array(
			"id" => $arr["id"],
		));
		$content = ob_get_contents();
		ob_end_clean();

		$conv->gen_pdf(array(
			"source" => $content,
			"filename" => $this->pdf_file_name,
		));
		exit;
	}

	public abstract function get_payment_sum();
	public abstract function get_request_fee();

	/**
		@attrib name=procurator_popup
		@param parent required type=string
	**/
	function procurator_popup($arr)
	{
		$ret = "";
		$procurator_l = new object_list(array(
			"lang_id" => array(),
			"parent" => $arr["parent"],
			"class_id" => CL_CRM_PERSON,
			"firstname" => "%",
			"sort_by" => "kliendibaas_isik.`lastname`"
		));

		$tpl = "procurator_popup.tpl";
		$is_tpl = $this->read_template($tpl,1);
		$c = " ";

		foreach($procurator_l->arr() as $val)
		{
			$this->vars(array(
				"id"=> $val->id(),
				"name" => $val->name(),
				"code" => $val->prop("code"),
				"onclick" => 'javascript:
					window.opener.document.getElementById("procurator").value= "'.$val->id().'";
					window.opener.document.getElementById("procurator_name").innerHTML= "'.$val->name().'";
					window.opener.document.getElementById("remove_procurator").style.display = "";
					window.opener.document.getElementById("warrant_row").style.display = "";
					window.close()',
			));
			$c .= $this->parse("PROCURATOR");

			$ret .= '<a href="javascript:void(0);" onclick=\'javascript:
				window.opener.document.getElementById("procurator").value= "'.$val->id().'";
				window.opener.document.getElementById("procurator_name").innerHTML= "'.$val->name().'";
				window.opener.document.getElementById("warrant_row").style.display = "";
				window.opener.document.getElementById("remove_procurator").style.display = "";
				window.close()\'>'.$val->name().' </a><br />';
		//	$ret .= "<a href='javascript:void(0)' onClick='javascript:window.opener.changeform.exhibition_country.value=".$key."'>".$val."</a><br>";
		}

		if($is_tpl)
		{
			$this->vars(array(
				"PROCURATOR" => $c,
			));
			return $this->parse();
		}
		return $ret;
	}

	/**
		@attrib name=country_popup
		@param var required type=string
	**/
	function country_popup($arr)
	{
		$address_inst = get_instance(CL_CRM_ADDRESS);
		$ret = "";


		$tpl = "country_popup.tpl";
		$lang = str_replace(array("/", ".", "?", ":", "|", "\\"), "", $GLOBALS["LC"]);
		include($GLOBALS["aw_dir"]."/lang/trans/".$lang."/aw/crm_address.aw");
		$is_tpl = $this->read_template($tpl,1);
		$c = "";

		$c_l = $address_inst->get_country_list();
		asort($c_l);
		foreach($c_l as $key=> $val)
		{
			$this->vars(array(
				"name" => $val,
				"onclick" => 'javascript:window.opener.document.changeform.'.$arr["var"].'.value="'.$key.'";window.close()',
				"code" => $key,
			));
			$c .= $this->parse("COUNTRY");
			$ret .= "<a href='javascript:void(0);' onclick='javascript:window.opener.document.changeform.".$arr["var"].".value=\"".$key."\";window.close()'><span class=\"text\">".$val."</span></a><br />";
		//	$ret .= "<a href='javascript:void(0)' onclick='javascript:window.opener.changeform.exhibition_country.value=".$key."'>".$val."</a><br>";
		}
		if($is_tpl)
		{
			$this->vars(array(
				"COUNTRY" => $c,
				"var" => $arr["var"]
			));
			return $this->parse();
		}
		return $ret;
	}

	/**
		@attrib name=payer_popup
	**/
	function payer_popup()
	{
		$ret = " ";
		foreach($_SESSION["patent"]["applicants"] as $applicant)
		{
			$ret = " ";
			$ret .= "<a href='javascript:void(0)' onclick='javascript:window.opener.document.changeform.payer.value=\"".$applicant["firstname"]." " . $applicant["lastname"]."\";window.close()'>".$applicant["firstname"]." " . $applicant["lastname"]."</a><br />";
		//	$ret .= "<a href='javascript:void(0)' onclick='javascript:window.opener.changeform.exhibition_country.value=".$key."'>".$val."</a><br>";
		}
		return $ret;
	}

	function _get_applicants_table()
	{
		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
			"id" => "patent_requesters_registered",
		));
		$t->set_dom_id("applicant_requesters");

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		if ("applicant" != $this->info_levels[0] and count($_SESSION["patent"]["applicants"]) > 1 and get_class($this) !== "patent_patent" and get_class($this) !== "utility_model")
		{
			$t->define_field(array(
				"name" => "representer",
				"caption" => t("&Uuml;hine esindaja"),
			));
		}

		$t->define_field(array(
			"name" => "change",
			"caption" => t("")
		));

		foreach($_SESSION["patent"]["applicants"] as $key =>$applicant)
		{
			if($applicant["applicant_type"])
			{
				$name = $applicant["name"];
			}
			else
			{
				$name = $applicant["firstname"]." ".$applicant["lastname"];
			}

			$t->define_data(array(
				"name" => $name,
				"code" => $applicant["code"],
				"representer" => html::radiobutton(array(
					"value" => $key,
					"checked" => ($_SESSION["patent"]["representer"] == $key) ? 1 : 0,
					"name" => "representer",
				)),
				"change" => html::href(array(
					"url" => "javascript:document.getElementById(\"applicant_id\").value=".$key.";document.changeform.submit();",
					"caption" => t("Muuda"),
				))." ".html::href(array(
					"url" => "javascript:document.getElementById(\"delete_applicant\").value=".$key.";document.getElementById(\"stay\").value=1;document.changeform.submit();",
					"caption" => t("Kustuta"),
				)),
			));
		}
		return $t->draw();
	}

	function _get_authors_table()
	{
		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
			"id" => "patent_author_registered",
		));
		$t->table_tag_id = "author_requesters";

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t(""),
		));
		foreach($_SESSION["patent"]["authors"] as $key =>$author)
		{
			$name = $author["firstname"]." ".$author["lastname"];
			$t->define_data(array(
				"name" => $name,
				"change" => html::href(array(
					"url" => "javascript:document.getElementById(\"author_id\").value=".$key.";document.getElementById(\"stay\").value=1;document.changeform.submit();",
					"caption" => t("Muuda"),
				))." ".html::href(array(
					"url" => "javascript:document.getElementById(\"delete_author\").value=".$key.";document.getElementById(\"stay\").value=1;document.changeform.submit();",
					"caption" => t("Kustuta"),
				)),
			));
		}
		return $t->draw();
	}

	/**
		@attrib name=submit_data is_public="1" caption="Change" all_args=1
	**/
	function submit_data($arr)
	{
		$_SESSION["patent"]["errors"] = $errs = $this->check_fields();

		foreach($_POST as $name => $val)
		{
			$_SESSION["patent"][$name] = $val;
		}

		if($arr["data_type"] === "1")
		{
			$_SESSION["patent"]["co_trademark"] = $_POST["co_trademark"];
			$_SESSION["patent"]["guaranty_trademark"] = $_POST["guaranty_trademark"];
			//co_trademark 'guaranty_trademark
		}
		//taotleja andmed liiguvad massiivi, et saaks mitu taotlejat sisse lugeda
		//miskeid tyhju taotlejait poleks vaja... niiet:
		if($_POST["code"] || $_POST["name"] || $_POST["firstname"] || $_POST["lastname"])
		{
			if (!$_POST["data_type"])
			{
				$n = $this->submit_applicant();
				if($errs || $_POST["stay"])
				{
					$_SESSION["patent"]["change_applicant"] = $n;
					$_SESSION["patent"]["applicant_id"] = $n;
				}
			}
			elseif ($_POST["data_type"] === "11")
			{
				$n = $this->submit_author();
				if($errs || $_POST["stay"])
				{
					$_SESSION["patent"]["change_author"] = $n;
					$_SESSION["patent"]["author_id"] = $n;
				}
			}
		}

		if (!$errs)
		{
			$this->save_uploads($_FILES);
		}

		if($_POST["save"] && $_POST["stay"])
		{
			$object_id = $this->save_data();
		}
		else
		{
			foreach ($this->checkbox_vars as $name)
			{
				$_SESSION["patent"][$name] = (int)(bool) $_POST[$name];
			}
		}

		if($_POST["data_type"] !== "11" and ($_POST["add_new_applicant"] || !empty($_POST["applicant_id"])))
		{
			if($_POST["add_new_applicant"])
			{
				$_SESSION["patent"]["add_new_applicant"] = 1;
			}
			return aw_url_change_var("new_application" , null , post_ru());
		}
		elseif($_POST["data_type"] === "11" and ($_POST["add_new_author"] || !empty($_POST["author_id"])))
		{
			if($_POST["add_new_author"])
			{
				$_SESSION["patent"]["add_new_author"] = 1;
			}
			elseif ($_POST["author_id"])
			{
				$_SESSION["patent"]["author_id"] = $_POST["author_id"];
			}

			$_SESSION["patent"]["stay"] = null;
			return post_ru();
		}
		//viimasest lehest edasi

		if(!$errs && !$_POST["stay"])
		{
			if($_POST["save"])
			{
				if($_SESSION["patent"]["id"] && $_SESSION["patent"]["authorized_person_code"] && $_GET["data_type"] === "5")
				{
					$status = $this->is_signed($key);
					if($status["status"] > 0)
					{
						$ddoc = obj($status["ddoc"]);
						$codes = array();
						foreach($ddoc->connections_from(array("type" => "RELTYPE_SIGNER")) as $key => $c)
						{
							$person = obj($c->to());
							$codes[] = $person->prop("personal_id");
						}
					}
				}

				if ($_POST["send_patent"])
				{
					$this->set_sent(array("add_obj" => (int) $_POST["add_obj"]));
				}

				$_SESSION["patent"] = null;
			}

			$next_level = false;
			foreach ($this->info_levels as $key => $value)
			{
				if ($next_level)
				{
					$next_level = $key;
					break;
				}
				if (((int) $arr["data_type"]) === $key)
				{
					$next_level = true;
				}
			}
			$return_url = aw_url_change_var("trademark_id" , null , aw_url_change_var("data_type" , $next_level , $arr["return_url"]));
			$return_url = aw_url_change_var("new_application" , null , $return_url);
			return $return_url;
		}
		else
		{
			$_SESSION["patent"]["stay"] = null;
			$return_url = aw_url_change_var("trademark_id" , null , $arr["return_url"]);
			$return_url = aw_url_change_var("new_application" , null , $return_url);
			return $return_url;
		}
	}

	function set_sent($arr)
	{
		$re = $this->is_signed($_SESSION["patent"]["id"]);
		if(!($re["status"] == 1))//et allkirjastamata taotlused saatmisele ei l2heks
		{
			return null;
		}

		$o = obj($_SESSION["patent"]["id"]);

		$ol = new object_list(array(
			"class_id" => CL_TRADEMARK_MANAGER,
			"status" => object::STAT_ACTIVE
		));
		$trademark_manager = $ol->begin();

		if (is_object($trademark_manager))
		{// temporarily. no connection between application objects and trademark_manager where settings are defined.
			$cl = basename(aw_ini_get("classes." . $o->class_id() . ".file"));

			if ("patent" === $cl)
			{
				$propname = "trademark_add";
			}
			elseif ("patent_patent" === $cl)
			{
				$propname = "patent_add";
			}
			else
			{
				$propname = $cl . "_add";
			}

			if (!$this->can("view", $trademark_manager->prop($propname)))
			{
				return null;
			}

			$tmp = obj($trademark_manager->prop($propname));
			$tmp = $tmp->connections_from(array(
				"class_id" => constant("CL_" . strtoupper($cl))
			));
			$tmp = reset($tmp);
			$tmp = $tmp->to();
			$num_ser = $tmp->prop("series");
			$ser = get_instance(CL_CRM_NUMBER_SERIES);
			$tno = $ser->find_series_and_get_next(CL_PATENT, $num_ser);
		}
		else
		{
			$object = obj($arr["add_obj"]);
			$num_ser = $object->prop("series");
			$ser = get_instance(CL_CRM_NUMBER_SERIES);
			$tno = $ser->find_series_and_get_next(CL_PATENT, $num_ser);
		}

		$status = $this->get_status($o);
		$status->set_prop("nr" , $tno);
		$status->set_prop("sent_date" , time());
		aw_disable_acl();
		$status->save();
		aw_restore_acl();
		header("Location:"."19205");
		die();
	}

	function check_fields()
	{
		$err = "";

		if($_POST["data_type"] !== "11" and ($_POST["code"] || $_POST["name"] || $_POST["firstname"] || $_POST["lastname"]))
		{
			if(!isset($_POST["country"]) and "euro_patent_et_desc" !== get_class($this))
			{
				$err.= t("P&auml;ritolumaa peab olema valitud")."\n<br />";
			}

			if(!isset($_POST["applicant_type"]))
			{
				$err.= t("F&uuml;&uuml;siline v&otilde;i juriidiline isik peab olema valitud")."\n<br />";
			}

			if($_POST["applicant_type"])
			{
				if(!$_POST["name"])
				{
					$err.= t("Nimi on kohustuslik")."\n<br />";
				}

			}
			else
			{
				if(!$_POST["firstname"])
				{
					$err.= t("Eesnimi on kohustuslik")."\n<br />";
				}
				if(!$_POST["lastname"])
				{
					$err.= t("Perekonnanimi on kohustuslik")."\n<br />";
				}
			}

			if(!$_POST["city"])
			{
				$err.= t("Linn on kohustuslik")."\n<br />";
			}

			if(!$_POST["county"] and "utility_model" !== get_class($this) and "patent_patent" !== get_class($this))
			{
				$err.= t("Maakond on kohustuslik")."\n<br />";
			}

			if(!$_POST["street"])
			{
				$err.= t("T&auml;nav on kohustuslik")."\n<br />";
			}

			if(!$_POST["country_code"])
			{
				$err.= t("Riik on kohustuslik")."\n<br />";
			}

			if(!$_POST["index"])
			{
				$err.= t("Postiindeks on kohustuslik")."\n<br />";
			}

			if(empty($err))
			{
				$_SESSION["patent"]["checked"][$_GET["data_type"]] = $_GET["data_type"];
			}
		}
		else
		{
			$_SESSION["patent"]["checked"][$_GET["data_type"]] = $_GET["data_type"];
		}

		return $err;
	}

	function submit_applicant()
	{
		if($_SESSION["patent"]["change_applicant"] != "")
		{
			$n = $_SESSION["patent"]["change_applicant"];
		}
		else
		{
			//otsib esimese mitte kasutatava key
			$n = 1;
			while(array_key_exists($n , $_SESSION["patent"]["applicants"]))
			{
				$n++;
				if($n > 25)
				{
					break;
				}
			}
		}

		foreach($this->applicant_vars as $var)
		{
			$_SESSION["patent"]["applicants"][$n][$var] = $_SESSION["patent"][$var];
			$_SESSION["patent"][$var] = null;
		}

		if (isset($_SESSION["patent"]["applicants"][$n]["name"]) and 1 != $_SESSION["patent"]["applicants"][$n]["applicant_type"])
		{
			$_SESSION["patent"]["applicants"][$n]["name"] = $_SESSION["patent"]["applicants"][$n]["firstname"] . " " . $_SESSION["patent"]["applicants"][$n]["lastname"];
		}

		return $n;
	}

	function submit_author()
	{
		if($_SESSION["patent"]["change_author"] != "")
		{
			$n = $_SESSION["patent"]["change_author"];
		}
		else
		{
			// otsib esimese vaba key
			$n = 1;
			while(array_key_exists($n, $_SESSION["patent"]["authors"]))
			{
				$n++;
				if($n > 25)
				{
					break;
				}
			}
		}

		foreach($this->author_vars as $var)
		{
			$_SESSION["patent"]["authors"][$n][$var] = $_SESSION["patent"][$var];
			$_SESSION["patent"][$var] = null;
		}

		return $n;
	}

	function save_uploads($uploads)
	{
		$image_inst = get_instance(CL_IMAGE);
		$file_inst = get_instance(CL_FILE);
		foreach($this->file_upload_vars as $var)
		{
			if(array_key_exists($var."_upload" , $uploads))
			{
				if(!$_FILES[$var."_upload"]['tmp_name'])
				{
					continue;
				}
				$id = $file_inst->save_file(array(
					"parent" => $_SESSION["patent"]["parent"],
					"content" => $image_inst->get_file(array(
						"file" => $_FILES[$var."_upload"]['tmp_name'],
					)),
					"name" => $_FILES[$var."_upload"]['name'],
 					"type" => $_FILES[$var."_upload"]['type'],
				));
				// if there is image uploaded:
				$_SESSION["patent"][$var] = $id;
			}
		}

		foreach($this->multifile_upload_vars as $var)
		{
			if (!empty($_FILES["file"]["tmp_name"]) and is_array($_FILES["file"]["tmp_name"]))
			{
				$files = $file_inst->add_upload_multifile("file", $_SESSION["patent"]["parent"]);
				foreach ($files as $filedata)
				{
					$_SESSION["patent"][$var][] = $filedata["id"];
					$o = new object($filedata["id"]);
					$o->set_meta("po_prop_name", $var);
					$o->save();
				}
			}
		}
	}

	function save_data()
	{
		$patent = $this->get_object();
		$this->save_forms($patent);
		$patent->save();

		$_SESSION["patent"]["id"] = $patent->id();
		$status = $this->get_status($patent);
		return $patent->id();
	}

	protected abstract function save_forms($patent);
	protected abstract function get_object();

	function get_status($patent)
	{
		if(!$this->can("add" , $patent->id())) return $patent;
		$status = $patent->get_first_obj_by_reltype("RELTYPE_TRADEMARK_STATUS");
		if(!is_object($status))
		{
			$status = new object();
			$status->set_class_id(CL_TRADEMARK_STATUS);
			$status->set_parent($patent->id());
			$status->set_name(" Kinnitamata taotlus nr [".$patent->id()."]");
			$status->save();
			$patent->connect(array("to" => $status->id() , "type" => "RELTYPE_TRADEMARK_STATUS"));
		}
		return $status;
	}

	function save_applicants($patent)
	{
		$address_inst = get_instance(CL_CRM_ADDRESS);
		$conns = $patent->connections_from(array(
			"type" => "RELTYPE_APPLICANT",
		));

		foreach($conns as $conn)
		{
			$conn->delete();
		}

		foreach($_SESSION["patent"]["applicants"] as $key => $val)
		{
			if(!$_SESSION["patent"]["representer"] and get_class($this) !== "patent_patent" and get_class($this) !== "utility_model")
			{
				$_SESSION["patent"]["representer"] = $key;
			}
			$applicant = new object();
			$applicant->set_parent($patent->id());
			if($val["applicant_type"])
			{
				$applicant->set_class_id(CL_CRM_COMPANY);
				$type=1;
			}
			else
			{
				$type=0;
				$applicant->set_class_id(CL_CRM_PERSON);
			}
			$applicant->save();

			$address = new object();
			$address->set_class_id(CL_CRM_ADDRESS);
			$address->set_parent($applicant->id());

			$address->set_prop("aadress", $val["street"]);
			$address->set_prop("postiindeks" , $val["index"]);
			$address->set_prop("riik" , $address_inst->get_country_by_code($val["country_code"], $applicant->id()));
			if($val["city"])
			{
				$citys = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_CITY, "name" => $val["city"]));
				if(!is_object($city = reset($citys->arr())))
				{
					$city = new object();
					$city->set_parent($applicant->id());
					$city->set_class_id(CL_CRM_CITY);
					$city->set_name($val["city"]);
					$city->save();

				}
				$address->set_prop("linn" ,$city->id());
			}

			if($val["county"])
			{
				$counties = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_COUNTY, "name" => $val["county"]));
				if(!is_object($county = reset($counties->arr())))
				{
					$county = new object();
					$county->set_parent($applicant->id());
					$county->set_class_id(CL_CRM_COUNTY);
					$county->set_name($val["county"]);
					$county->save();

				}
				$address->set_prop("maakond" ,$county->id());
			}

			$address->save();

			$correspond_address = "";
			if($val["correspond_firstname"] || $val["correspond_lastname"] || $val["correspond_job"] || $val["correspond_phone"] || $val["correspond_email"] || $val["correspond_country_code"] || $val["correspond_street"] || $val["correspond_index"] || $val["correspond_city"] || $val["correspond_county"])
			{
				$correspond_address = new object();
				$correspond_address->set_class_id(CL_CRM_ADDRESS);
				$correspond_address->set_parent($applicant->id());

				$correspond_address->set_meta("correspond_firstname",$val["correspond_firstname"]);
				$correspond_address->set_meta("correspond_lastname",$val["correspond_lastname"]);
				$correspond_address->set_meta("correspond_job",$val["correspond_job"]);
				$correspond_address->set_meta("correspond_phone",$val["correspond_phone"]);
				$correspond_address->set_meta("correspond_email",$val["correspond_email"]);

				$correspond_address->set_prop("aadress", $val["correspond_street"]);
				$correspond_address->set_prop("postiindeks" , $val["correspond_index"]);
				$correspond_address->set_prop("riik" , $address_inst->get_country_by_code($val["correspond_country_code"], $applicant->id()));

				if($val["correspond_city"])
				{
					$citys = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_CITY, "name" => $val["correspond_city"]));
					if(!is_object($city = reset($citys->arr())))
					{
						$city = new object();
						$city->set_parent($applicant->id());
						$city->set_class_id(CL_CRM_CITY);
						$city->set_name($val["correspond_city"]);
						$city->save();
					}
					$correspond_address->set_prop("linn" ,$city->id());
				}

				if($val["correspond_county"])
				{
					$counties = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_COUNTY, "name" => $val["correspond_county"]));
					if(!is_object($county = reset($counties->arr())))
					{
						$county = new object();
						$county->set_parent($applicant->id());
						$county->set_class_id(CL_CRM_COUNTY);
						$county->set_name($val["correspond_county"]);
						$county->save();
					}
					$correspond_address->set_prop("maakond" ,$county->id());
				}
				$correspond_address->save();
			}


			if($type)
			{
				$applicant->set_name($val["name"]);
				$applicant->set_prop("contact" , $address->id());
				$applicant->set_prop("reg_nr",$val["code"]);
//				$applicant->connect(array("to"=> $val["warrant"], "type" => "RELTYPE_PICTURE"));
			}
			else
			{
				$applicant->set_prop("firstname" , $val["firstname"]);
				$applicant->set_prop("lastname" , $val["lastname"]);
				$applicant->set_name($val["firstname"]." ".$val["lastname"]);
				$applicant->set_prop("address" , $address->id());
				$applicant->set_prop("personal_id" , $val["code"]);
			}
			$applicant->connect(array("to"=> $address->id(), "type" => "RELTYPE_ADDRESS"));
			$applicant->save();

			if(is_object($correspond_address))
			{
				$applicant->set_prop("correspond_address" , $correspond_address->id());
				$applicant->connect(array("to"=> $correspond_address->id(), "type" => "RELTYPE_CORRESPOND_ADDRESS"));
			}

			if($val["phone"])
			{
				$phone = new object();
				$phone->set_class_id(CL_CRM_PHONE);
				$phone->set_name($val["phone"]);
				$phone->set_prop("type" , "mobile");
				$phone->set_parent($applicant->id());
				$phone->save();
				$applicant->connect(array("to"=> $phone->id(), "type" => "RELTYPE_PHONE"));
				if(!$type) $applicant->set_prop("phone" , $phone->id());
				else $applicant->set_prop("phone_id" , $phone->id());
			}
			if($val["email"])
			{
				$email = new object();
				$email->set_class_id(CL_ML_MEMBER);
				$email->set_name($val["email"]);
				$email->set_prop("mail" , $val["email"]);
				$email->set_parent($applicant->id());
				$email->save();
				$applicant->connect(array("to"=> $email->id(), "type" => "RELTYPE_EMAIL"));
				if(!$type) $applicant->set_prop("email" , $email->id());
				else $applicant->set_prop("email_id" , $email->id());
			}
			if($val["fax"])
			{
				$phone = new object();
				$phone->set_class_id(CL_CRM_PHONE);
				$phone->set_name($val["fax"]);
				$phone->set_parent($applicant->id());
				$phone->save();
				if($type)
				{
					$applicant->connect(array("to"=> $phone->id(), "type" => "RELTYPE_TELEFAX"));
					$applicant->set_prop("telefax_id" , $phone->id());
				}
				else
				{
					$applicant->connect(array("to"=> $phone->id(), "type" => "RELTYPE_FAX"));
					$applicant->set_prop("fax" , $phone->id());
				}
			}
			$applicant->save();

			$patent->connect(array("to" => $applicant->id(), "type" => "RELTYPE_APPLICANT"));

			if($_SESSION["patent"]["representer"] == $key)
			{
				$patent->set_prop("applicant" , $applicant->id());
			}

			if ($val["applicant_reg"])
			{
				$tmp = (array) $patent->meta("applicant_reg");
				$tmp[$applicant->id()] = $val["applicant_reg"];
				$patent->set_meta("applicant_reg", $tmp);
			}
		}
		//$patent->set_prop("country" , $_SESSION["patent"]["country_code"]);
		$patent->set_prop("procurator", $_SESSION["patent"]["procurator"]);
		$patent->save();
	}

	function save_authors($patent)
	{
		$author_disallow_disclose = (array) $patent->meta("author_disallow_disclose");
		$address_inst = get_instance(CL_CRM_ADDRESS);
		$conns = $patent->connections_from(array(
			"type" => "RELTYPE_AUTHOR",
		));
		foreach($conns as $conn)
		{
			$conn->delete();
		}

		foreach($_SESSION["patent"]["authors"] as $key => $val)
		{
			$author = new object();
			$author->set_parent($patent->id());
			$author->set_class_id(CL_CRM_PERSON);
			$author->save();

			$address = new object();
			$address->set_class_id(CL_CRM_ADDRESS);
			$address->set_parent($author->id());

			$address->set_prop("aadress", $val["street"]);
			$address->set_prop("postiindeks" , $val["index"]);
			$address->set_prop("riik" , $address_inst->get_country_by_code($val["country_code"], $author->id()));
			if($val["city"])
			{
				$citys = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_CITY, "name" => $val["city"]));
				if(!is_object($city = reset($citys->arr())))
				{
					$city = new object();
					$city->set_parent($author->id());
					$city->set_class_id(CL_CRM_CITY);
					$city->set_name($val["city"]);
					$city->save();

				}
				$address->set_prop("linn" ,$city->id());
			}

			if($val["county"])
			{
				$counties = new object_list(array("lang_id" => 1, "class_id" => CL_CRM_COUNTY, "name" => $val["county"]));
				if(!is_object($county = reset($counties->arr())))
				{
					$county = new object();
					$county->set_parent($author->id());
					$county->set_class_id(CL_CRM_COUNTY);
					$county->set_name($val["county"]);
					$county->save();
				}
				$address->set_prop("maakond" ,$county->id());
			}

			$address->save();

			$author->set_prop("firstname" , $val["firstname"]);
			$author->set_prop("lastname" , $val["lastname"]);
			$author->set_name($val["firstname"]." ".$val["lastname"]);
			$author->set_prop("address" , $address->id());
			$author->connect(array("to"=> $address->id(), "type" => "RELTYPE_ADDRESS"));
			$author->save();

			$patent->connect(array("to" => $author->id(), "type" => "RELTYPE_AUTHOR"));
			$author_disallow_disclose[$author->id()] = $val["author_disallow_disclose"];
		}

		$patent->set_meta("author_disallow_disclose", $author_disallow_disclose);
		$patent->save();
	}

	function fileupload_save($patent)
	{
		foreach($this->file_upload_vars as $var)
		{
			if(is_oid($_SESSION["patent"][$var]) && $this->can("view" ,$_SESSION["patent"][$var]))
			{
				$patent->set_prop($var, $_SESSION["patent"][$var]);
				$patent->connect(array("to" => $_SESSION["patent"][$var], "type" => "RELTYPE_".strtoupper($var)));
			}
		}
		$patent->save();
	}

	function multifile_upload_save($patent)
	{
		foreach ($this->multifile_upload_vars as $name)
		{
			$files = $_SESSION["patent"][$name];
			foreach ($files as $id)
			{
				$patent->connect(array("to" => $id, "type" => "RELTYPE_".strtoupper($name)));
			}
		}
	}

	function final_save($patent)
	{
		$patent->set_prop("additional_info" , $_SESSION["patent"]["additional_info"]);
		$patent->set_prop("job" , $_SESSION["patent"]["job"]);
		$patent->set_prop("authorized_codes" , $_SESSION["patent"]["authorized_codes"]);

		if(	$_SESSION["patent"]["authorized_person_firstname"] ||
			$_SESSION["patent"]["authorized_person_person_lastname"] ||
			$_SESSION["patent"]["authorized_person_code"])
		{
			$applicant = new object();
			$applicant->set_parent($patent->id());
			$applicant->set_class_id(CL_CRM_PERSON);
			$applicant->set_prop("firstname" , $_SESSION["patent"]["authorized_person_firstname"]);
			$applicant->set_prop("lastname" , $_SESSION["patent"]["authorized_person_lastname"]);
			$applicant->set_prop("personal_id", $_SESSION["patent"]["authorized_person_code"]);
			$applicant->set_name($_SESSION["patent"]["authorized_person_firstname"]." ".$_SESSION["patent"]["authorized_person_lastname"]);
			$applicant->save();
			$patent->set_prop("authorized_person" , $applicant->id());
			$patent->connect(array("to" => $applicant->id(), "type" => "RELTYPE_AUTHORIZED_PERSON"));
		}
		$patent->save();
	}

	function save_fee($patent)
	{
		$patent->set_prop("payer", $_SESSION["patent"]["payer"]);
		$patent->set_prop("doc_nr", $_SESSION["patent"]["doc_nr"]);

		foreach($this->save_fee_vars as $var)
		{
			if($_SESSION["patent"][$var])
			{
				$patent->set_prop($var, $_SESSION["patent"][$var]);
			}
		}

		if($_SESSION["patent"]["payment_date"] > 0)
		{
			$patent->set_prop("payment_date" , $_SESSION["patent"]["payment_date"]);
		}
		elseif(is_array($_SESSION["patent"]["payment_date"]))
		{
			$val = mktime(0,0,0, $_SESSION["patent"]["payment_date"]["month"], $_SESSION["patent"]["payment_date"]["day"], $_SESSION["patent"]["payment_date"]["year"]);
			$patent->set_prop("payment_date" , $val);
		}

		if (get_class($this) === "patent_patent")
		{
			$patent->set_prop("fee_copies_info", $_SESSION["patent"]["fee_copies_info"]);
		}

		$patent->save();
	}

	private function check_and_set_authorized_codes_user_access($pid)
	{
		aw_disable_acl();
		$ol = new object_list(array(
			"class_id" => $this->ip_classes,
			"lang_id" => array(),
			"authorized_codes" => "%".$pid."%",
			"status" => new obj_predicate_not(object::STAT_DELETED)
		));
		$user =  new object(aw_global_get("uid_oid"));
		$grp = new object($user->get_default_group());

		if ($grp instanceof object)
		{
			foreach ($ol->arr() as $o)
			{
				// access spec
				if ($o->createdby() === aw_global_get("uid"))
				{ // owner
					$general_access = $attachment_access = array(
						"can_add" => 1,
						"can_edit" => 1,
						"can_admin" => 1,
						"can_delete" => 1,
						"can_view" => 1
					);
				}
				else
				{ // authorized codes users
					$general_access = array(
						"can_add" => 1,
						"can_edit" => 1,
						"can_admin" => 0,
						"can_delete" => 0,
						"can_view" => 1
					);
					$attachment_access = array(
						"can_add" => 0,
						"can_edit" => 0,
						"can_admin" => 0,
						"can_delete" => 0,
						"can_view" => 1
					);
				}

				// access to application object
				$o->acl_set($grp, $general_access);

				// access to digidoc object
				$ddc = $o->connections_to(array(
					"from.class_id" => CL_DDOC,
					// "from.status" => new obj_predicate_not(object::STAT_DELETED)
				));

				foreach ($ddc as $c)
				{
					if ($c->prop("from.status") != object::STAT_DELETED)
					{
						$ddo = new object($c->from());
						$ddo->acl_set($grp, $general_access);
						$signers = $ddo->connections_from(array("type" => "RELTYPE_SIGNER"));
						foreach ($signers as $s_c)
						{
							$signer = $s_c->to();
							$signer->acl_set($grp, array(
								"can_add" => 0,
								"can_edit" => 0,
								"can_admin" => 0,
								"can_delete" => 0,
								"can_view" => 1,
							));
						}
					}
				}

				// access to attachments etc.
				$cc = $o->connections_from();

				foreach ($cc as $c)
				{
					$co = new object($c->to());
					$co->acl_set($grp, $attachment_access);
				}
			}
		}
		aw_restore_acl();
	}

	/** Show patents added by user

		@attrib name=my_patent_list is_public="1" caption="Minu patenditaotlused"

	**/
	function my_patent_list($arr)
	{
		$uid = aw_global_get("uid");

		$delete_patent = (int) $_GET["delete_patent"];
		if($this->can("delete", $delete_patent))
		{
			$d = obj($delete_patent);
			$d->delete();
		}

		$tpl = "list.tpl";
		if($arr["unsigned"])
		{
			$tpl = "unsigned_list.tpl";
		}

		$this->read_template($tpl);
		$u = get_instance(CL_USER);

		//!!! ajutine lahendus patendiameti probleemile, kus id-kaardiga sisse logind kasutajale luuakse isik, aga seda get_current_person() vms. miskip2rast ei leia ja loob uue, ilma isikukoodita
		$u_o = obj(aw_global_get("uid_oid"));
		$person_c = $u_o->connections_from(array(
			"type" => "RELTYPE_PERSON"
		));

		$p = false;
		if (count($person_c))
		{
			foreach ($person_c as $person_connection)
			{
				$p = obj($person_connection->prop("to"));

				if ($p->prop("personal_id"))
				{
					break;
				}
			}
		}

		if (false === $p)
		{
			$p = obj($u->get_current_person());
		}
		//!!! end ajutine lahendus

		$code = $p->prop("personal_id");
		$ddoc_inst = get_instance(CL_DDOC);

		// give access rights to authorized_codes specified user
		if ($code)
		{
			$this->check_and_set_authorized_codes_user_access($code);
		}

		/* PATENTS LIST */
		$obj_list = new object_list(array(
			"class_id" => CL_PATENT_PATENT,
			"createdby" => $uid,
			"lang_id" => array(),
		));

		$obj_list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		lc_site_load("patent", $this);
		if($code)
		{
			$persons_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"personal_id" => $code
			));
			$other_list = new object_list(array(
 				"class_id" => CL_PATENT_PATENT,
 				"lang_id" => array(),
 				"authorized_codes" => "%".$code."%",
 			));
			$obj_list->add($other_list);

			foreach($persons_list->ids() as $id)
			{
				$other_list = new object_list(array(
					"class_id" => CL_PATENT_PATENT,
					"applicant" => $id,
					"lang_id" => array(),
				));
				$obj_list->add($other_list);

				$other_list = new object_list(array(
					"class_id" => CL_PATENT_PATENT,
					"lang_id" => array(),
					"authorized_person" => $id,
				));
				$obj_list->add($other_list);
			}
		}

		$objects_array = array();
		foreach($obj_list->arr() as $key => $patent)
		{
			$status = $this->get_status($patent);
			if($status->prop("nr"))
			{
				$objects_array[$status->prop("nr")] = $patent;
			}
			else
			{
				$objects_array[] = $patent;
			}
		}

		if(!$arr["unsigned"])
		{
			krsort($objects_array);
		}

		$send_patent = (int) $_GET["send_patent"];

		if ($this->is_template("PAT_LIST"))
		{
			$obj_count = 0;
			$pat_l = "";
			foreach($objects_array as $key => $patent)
			{
				$patent->check_and_set_authorized_codes_user_access();
				$sent_form = 0;
				$status = $this->get_status($patent);
				$re = $this->is_signed($patent->id());

				if($send_patent == $patent->id() && $re["status"] == 1 && !$status->prop("nr"))
				{
					$_SESSION["patent"]["id"] = $patent->id();
					$asd = $this->set_sent(array("add_obj" => $arr["alias"]["to"]));
				}

				if($arr["unsigned"])
				{
					if($status->prop("nr")) continue;
					$date = date("d.m.Y" , $patent->created());
				}
				else
				{
					if(!$status->prop("nr")) continue;
					if($status->prop("sent_date"))
					{
						$date = date("d.m.Y" , $status->prop("sent_date"));
					}
					else
					{
						$date = date("d.m.Y" , $patent->created());
					}

					if ($status->prop("verified"))
					{
						$sent_form = 1;
					}
				}

				$url = aw_url_change_var("trademark_id", $patent->id());
				$url = aw_url_change_var("data_type", 0 , $url);
				$url = aw_url_change_var("new_application", null , $url);
				$url = aw_url_change_var("delete_patent", null , $url);

				try
				{
					$section = aw_ini_get("clients.patent_office.pat_edit_section_id");
					$url = aw_url_change_var("section", $section, $url);
				}
				catch (Exception $e)
				{
					$url = "";
				}

				if(!$status->prop("verified") &&	!$status->prop("nr"))
				{
					$do_sign = 1;
			        	if($re["status"] == 1)
			        	{
			        		$sign_url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $re["ddoc"]
						));
			        	}
			        	else
			        	{
				        	$sign_url = $ddoc_inst->sign_url(array(
							"other_oid" =>$patent->id()
						));
			          }
			          $sign = "<a href='javascript:void(0);' onclick='javascript:window.open(\"".$sign_url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>Allkirjasta</a>";
				}
				else
				{
					$do_sign = 0;
					$sign = "";
				}

				$view_url = $this->mk_my_orb("show", array(
					"print" => 1,
					"sent_form" => $sent_form,
					"id" => $patent->id(),
					"add_obj" => $arr["alias"]["to"],
					"sign" => $do_sign
				), CL_PATENT_PATENT);

				$change = $del_url = $send_url= '';
				if(!($status->prop("nr") || $status->prop("verified")))
				{
					$del_url = aw_ini_get("baseurl").aw_url_change_var("delete_patent", $patent->id());
					$change = '<a href="' . $url . '">Muuda</a>';
				}

				if(($re["status"] == 1))
				{
					$change = "";
					$url = aw_url_change_var("send_patent", $patent->id());
					$send_url = '<a href="'.$url.'"> Saada</a>';
				}

				//taotlejad komaga eraldatuld
				$applicant_str = $this->get_applicants_str($patent);

				$this->vars(array(
					"date" 		=> $date,
					"nr" 		=> ($status->prop("nr")) ? $status->prop("nr") : "",
					"applicant" 	=> $applicant_str,
					"state" 	=> ($status->prop("verified")) ? t("Vastu v&otilde;etud") : (($status->prop("nr")) ? t("Esitatud") : ""),
					"name" 	 	=> $status->name(),
					"id" 	 	=> $patent->id(),
					"url"  		=> $url,
					"procurator"  	=> $patent->prop_str("procurator"),
					"change"	=> $change,
					"view"		=> $view_url,
					"sign"		=> $sign,
					"delete"	=> $del_url,
					"send"		=> $send_url,
				));
				$pat_l .= $this->parse("PAT_LIST");
				++$obj_count;
			}

			if ($obj_count)
			{
				$this->vars(array(
					"PAT_LIST" => $pat_l
				));
				$pat = $this->parse("PAT");
			}
			else
			{
				$pat = "";
			}
		}
		/* END PATENTS LIST */

		/* TM LIST */
		$obj_list = new object_list(array(
			"class_id" => CL_PATENT,
			"createdby" => $uid,
			"lang_id" => array(),
		));

		$obj_list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		lc_site_load("patent", $this);
		if($code)
		{
			$persons_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"personal_id" => $code
			));
			$other_list = new object_list(array(
 				"class_id" => CL_PATENT,
 				"lang_id" => array(),
 				"authorized_codes" => "%".$code."%",
 			));
			$obj_list->add($other_list);

			foreach($persons_list->ids() as $id)
			{
				$other_list = new object_list(array(
					"class_id" => CL_PATENT,
					"applicant" => $id,
					"lang_id" => array(),
				));
				$obj_list->add($other_list);

				$other_list = new object_list(array(
					"class_id" => CL_PATENT,
					"lang_id" => array(),
					"authorized_person" => $id,
				));
				$obj_list->add($other_list);
			}
		}

		$objects_array = array();
		foreach($obj_list->arr() as $key => $patent)
		{
			$status = $this->get_status($patent);
			if($status->prop("nr"))
			{
				$objects_array[$status->prop("nr")] = $patent;
			}
			else
			{
				$objects_array[] = $patent;
			}
		}
		if(!$arr["unsigned"])
		{
			krsort($objects_array);
		}

		if ($this->is_template("TM_LIST"))
		{
			$obj_count = 0;
			$tm_l = "";
			foreach($objects_array as $key => $patent)
			{
				$patent->check_and_set_authorized_codes_user_access();
				$status = $this->get_status($patent);
				$re = $this->is_signed($patent->id());
				$sent_form = 0;

				if($send_patent == $patent->id() && $re["status"] == 1 && !$status->prop("nr"))
				{
					$_SESSION["patent"]["id"] = $patent->id();
					$asd = $this->set_sent(array("add_obj" => $arr["alias"]["to"]));
				}

				if($arr["unsigned"])
				{
					if($status->prop("nr")) continue;
					$date = date("d.m.Y" , $patent->created());
				}
				else
				{
					if(!$status->prop("nr")) continue;
					if($status->prop("sent_date"))
					{
						$date = date("d.m.Y" , $status->prop("sent_date"));
					}
					else
					{
						$date = date("d.m.Y" , $patent->created());
					}

					if ($status->prop("verified"))
					{
						$sent_form = 1;
					}
				}

				$url = aw_url_change_var("trademark_id", $patent->id());
				$url = aw_url_change_var("data_type", 0 , $url);
				$url = aw_url_change_var("new_application", null , $url);

				try
				{
					$section = aw_ini_get("clients.patent_office.tm_edit_section_id");
					$url = aw_url_change_var("section", $section, $url);
				}
				catch (Exception $e)
				{
					$url = "";
				}

				if(!$status->prop("verified") &&	!$status->prop("nr"))
				{
					$do_sign = 1;
			        	if($re["status"] == 1)
			        	{
			        		$sign_url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $re["ddoc"],
						));
			        	}
			        	else
			        	{
				        	$sign_url = $ddoc_inst->sign_url(array(
							"other_oid" =>$patent->id(),
						));
			                }
			                $sign = "<a href='javascript:void(0);' onclick='javascript:window.open(\"".$sign_url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>Allkirjasta</a>";
				}
				else
				{
					$do_sign = 0;
					$sign = "";
				}

				$view_url = $this->mk_my_orb("show", array(
					"print" => 1,
					"sent_form" => $sent_form,
					"id" => $patent->id(),
					"add_obj" => $arr["alias"]["to"],
					"sign" => $do_sign,
				), "patent");

				$change = $del_url = $send_url= '';
				if(!($status->prop("nr") || $status->prop("verified")))
				{
					$change = '<a href="'.$url.'">Muuda</a>';
					$del_url = aw_ini_get("baseurl").aw_url_change_var("delete_patent", $patent->id());
				}

				if(($re["status"] == 1))
				{
					$change = "";
					$url = aw_url_change_var("send_patent", $patent->id());
					$send_url = '<a href="'.$url.'"> Saada</a>';
				}

				//taotlejad komaga eraldatuld
				$applicant_str = $this->get_applicants_str($patent);


				$this->vars(array(
					"date" 		=> $date,
					"nr" 		=> ($status->prop("nr")) ? $status->prop("nr") : "",
					"applicant" 	=> $applicant_str,
					"type" 		=> $this->types[$patent->prop("type")],
					"state" 	=> ($status->prop("verified")) ? t("Vastu v&otilde;etud") : (($status->prop("nr")) ? t("Esitatud") : ""),
					"name" 	 	=> $status->name(),
					"id" 	 	=> $patent->id(),
					"url"  		=> $url,
					"procurator"  	=> $patent->prop_str("procurator"),
					"change"	=> $change,
					"view"		=> $view_url,
					"sign"		=> $sign,
					"delete"	=> $del_url,
					"send"		=> $send_url,
				));
				$tm_l .= $this->parse("TM_LIST");
				++$obj_count;
			}

			if ($obj_count)
			{
				$this->vars(array(
					"TM_LIST" => $tm_l
				));
				$tm = $this->parse("TM");
			}
			else
			{
				$tm = "";
			}
		}
		/* END TM LIST */

		/* UM LIST */
		$obj_list = new object_list(array(
			"class_id" => CL_UTILITY_MODEL,
			"createdby" => $uid,
			"lang_id" => array(),
		));

		$obj_list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		lc_site_load("utility_model", $this);
		if($code)
		{
			$persons_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"personal_id" => $code
			));
			$other_list = new object_list(array(
 				"class_id" => CL_UTILITY_MODEL,
 				"lang_id" => array(),
 				"authorized_codes" => "%".$code."%",
 			));
			$obj_list->add($other_list);

			foreach($persons_list->ids() as $id)
			{
				$other_list = new object_list(array(
					"class_id" => CL_UTILITY_MODEL,
					"applicant" => $id,
					"lang_id" => array(),
				));
				$obj_list->add($other_list);

				$other_list = new object_list(array(
					"class_id" => CL_UTILITY_MODEL,
					"lang_id" => array(),
					"authorized_person" => $id,
				));
				$obj_list->add($other_list);
			}
		}

		$objects_array = array();
		foreach($obj_list->arr() as $key => $patent)
		{
			$status = $this->get_status($patent);
			if($status->prop("nr"))
			{
				$objects_array[$status->prop("nr")] = $patent;
			}
			else
			{
				$objects_array[] = $patent;
			}
		}
		if(!$arr["unsigned"])
		{
			krsort($objects_array);
		}

		if ($this->is_template("UM_LIST"))
		{
			$obj_count = 0;
			$um_l = "";
			foreach($objects_array as $key => $patent)
			{
				$patent->check_and_set_authorized_codes_user_access();
				$status = $this->get_status($patent);
				$re = $this->is_signed($patent->id());
				$sent_form = 0;

				if($send_patent == $patent->id() && $re["status"] == 1 && !$status->prop("nr"))
				{
					$_SESSION["patent"]["id"] = $patent->id();
					$asd = $this->set_sent(array("add_obj" => $arr["alias"]["to"]));
				}
				if($arr["unsigned"])
				{
					if($status->prop("nr")) continue;
					$date = date("d.m.Y" , $patent->created());
				}
				else
				{
					if(!$status->prop("nr")) continue;
					if($status->prop("sent_date"))
					{
						$date = date("d.m.Y" , $status->prop("sent_date"));
					}
					else
					{
						$date = date("d.m.Y" , $patent->created());
					}

					if ($status->prop("verified"))
					{
						$sent_form = 1;
					}
				}

				$url = aw_url_change_var("trademark_id", $patent->id());
				$url = aw_url_change_var("data_type", 0 , $url);
				$url = aw_url_change_var("new_application", null , $url);

				try
				{
					$section = aw_ini_get("clients.patent_office.um_edit_section_id");
					$url = aw_url_change_var("section", $section, $url);
				}
				catch (Exception $e)
				{
					$url = "";
				}

				if(!$status->prop("verified") &&	!$status->prop("nr"))
				{
					$do_sign = 1;
			        	if($re["status"] == 1)
			        	{
			        		$sign_url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $re["ddoc"],
						));
			        	}
			        	else
			        	{
				        	$sign_url = $ddoc_inst->sign_url(array(
							"other_oid" =>$patent->id(),
						));
			                }
			                $sign = "<a href='javascript:void(0);' onclick='javascript:window.open(\"".$sign_url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>Allkirjasta</a>";
				}
				else
				{
					$do_sign = 0;
					$sign = "";
				}
				$view_url = $this->mk_my_orb("show", array(
					"print" => 1,
					"sent_form" => $sent_form,
					"id" => $patent->id(),
					"add_obj" => $arr["alias"]["to"],
					"sign" => $do_sign,
				), CL_UTILITY_MODEL);

				$change = $del_url = $send_url= '';
				if(!($status->prop("nr") || $status->prop("verified")))
				{
					$change = '<a href="'.$url.'">Muuda</a>';
					$del_url = aw_ini_get("baseurl").aw_url_change_var("delete_patent", $patent->id());
				}

				if(($re["status"] == 1))
				{
					$change = "";
					$url = aw_url_change_var("send_patent", $patent->id());
					$send_url = '<a href="'.$url.'"> Saada</a>';
				}

				//taotlejad komaga eraldatuld
				$applicant_str = $this->get_applicants_str($patent);


				$this->vars(array(
					"date" 		=> $date,
					"nr" 		=> ($status->prop("nr")) ? $status->prop("nr") : "",
					"applicant" 	=> $applicant_str,
					"state" 	=> ($status->prop("verified")) ? t("Vastu v&otilde;etud") : (($status->prop("nr")) ? t("Esitatud") : ""),
					"name" 	 	=> $status->name(),
					"id" 	 	=> $patent->id(),
					"url"  		=> $url,
					"procurator"  	=> $patent->prop_str("procurator"),
					"change"	=> $change,
					"view"		=> $view_url,
					"sign"		=> $sign,
					"delete"	=> $del_url,
					"send"		=> $send_url,
				));
				$um_l .= $this->parse("UM_LIST");
				++$obj_count;
			}

			if ($obj_count)
			{
				$this->vars(array(
					"UM_LIST" => $um_l
				));
				$um = $this->parse("UM");
			}
			else
			{
				$um = "";
			}
		}
		/* END UM LIST */

		/* IND LIST */
		$obj_list = new object_list(array(
			"class_id" => CL_INDUSTRIAL_DESIGN,
			"createdby" => $uid,
			"lang_id" => array(),
		));

		$obj_list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		lc_site_load("patent", $this);
		if($code)
		{
			$persons_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"personal_id" => $code
			));
			$other_list = new object_list(array(
 				"class_id" => CL_INDUSTRIAL_DESIGN,
 				"lang_id" => array(),
 				"authorized_codes" => "%".$code."%",
 			));
			$obj_list->add($other_list);

			foreach($persons_list->ids() as $id)
			{
				$other_list = new object_list(array(
					"class_id" => CL_INDUSTRIAL_DESIGN,
					"applicant" => $id,
					"lang_id" => array(),
				));
				$obj_list->add($other_list);

				$other_list = new object_list(array(
					"class_id" => CL_INDUSTRIAL_DESIGN,
					"lang_id" => array(),
					"authorized_person" => $id,
				));
				$obj_list->add($other_list);
			}
		}

		$objects_array = array();
		foreach($obj_list->arr() as $key => $patent)
		{
			$status = $this->get_status($patent);
			if($status->prop("nr"))
			{
				$objects_array[$status->prop("nr")] = $patent;
			}
			else
			{
				$objects_array[] = $patent;
			}
		}

		if(!$arr["unsigned"])
		{
			krsort($objects_array);
		}

		if ($this->is_template("IND_LIST"))
		{
			$obj_count = 0;
			$ind_l = "";
			foreach($objects_array as $key => $patent)
			{
				$patent->check_and_set_authorized_codes_user_access();
				$status = $this->get_status($patent);
				$re = $this->is_signed($patent->id());
				$sent_form = 0;

				if($send_patent == $patent->id() && $re["status"] == 1 && !$status->prop("nr"))
				{
					$_SESSION["patent"]["id"] = $patent->id();
					$asd = $this->set_sent(array("add_obj" => $arr["alias"]["to"]));
				}

				if($arr["unsigned"])
				{
					if($status->prop("nr")) continue;
					$date = date("d.m.Y" , $patent->created());
				}
				else
				{
					if(!$status->prop("nr")) continue;
					if($status->prop("sent_date"))
					{
						$date = date("d.m.Y" , $status->prop("sent_date"));
					}
					else
					{
						$date = date("d.m.Y" , $patent->created());
					}

					if ($status->prop("verified"))
					{
						$sent_form = 1;
					}
				}

				$url = aw_url_change_var("trademark_id", $patent->id());
				$url = aw_url_change_var("data_type", 0 , $url);
				$url = aw_url_change_var("new_application", null , $url);

				try
				{
					$section = aw_ini_get("clients.patent_office.ind_edit_section_id");
					$url = aw_url_change_var("section", $section, $url);
				}
				catch (Exception $e)
				{
					$url = "";
				}

				if(!$status->prop("verified") &&	!$status->prop("nr"))
				{
					$do_sign = 1;
			        	if($re["status"] == 1)
			        	{
			        		$sign_url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $re["ddoc"],
						));
			        	}
			        	else
			        	{
				        	$sign_url = $ddoc_inst->sign_url(array(
							"other_oid" =>$patent->id(),
						));
			          }
			          $sign = "<a href='javascript:void(0);' onclick='javascript:window.open(\"".$sign_url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>Allkirjasta</a>";
				}
				else
				{
					$do_sign = 0;
					$sign = "";
				}

				$view_url = $this->mk_my_orb("show", array(
					"print" => 1,
					"sent_form" => $sent_form,
					"id" => $patent->id(),
					"add_obj" => $arr["alias"]["to"],
					"sign" => $do_sign
				), CL_INDUSTRIAL_DESIGN);

				$change = $del_url = $send_url= '';
				if(!($status->prop("nr") || $status->prop("verified")))
				{
					$change = '<a href="'.$url.'">Muuda</a>';
					$del_url = aw_ini_get("baseurl").aw_url_change_var("delete_patent", $patent->id());
				}

				if(($re["status"] == 1))
				{
					$change = "";
					$url = aw_url_change_var("send_patent", $patent->id());
					$send_url = '<a href="'.$url.'"> Saada</a>';
				}

				//taotlejad komaga eraldatuld
				$applicant_str = $this->get_applicants_str($patent);

				$this->vars(array(
					"date" 		=> $date,
					"nr" 		=> ($status->prop("nr")) ? $status->prop("nr") : "",
					"applicant" 	=> $applicant_str,
					"state" 	=> ($status->prop("verified")) ? t("Vastu v&otilde;etud") : (($status->prop("nr")) ? t("Esitatud") : ""),
					"name" 	 	=> $status->name(),
					"id" 	 	=> $patent->id(),
					"url"  		=> $url,
					"procurator"  	=> $patent->prop_str("procurator"),
					"change"	=> $change,
					"view"		=> $view_url,
					"sign"		=> $sign,
					"delete"	=> $del_url,
					"send"		=> $send_url,
				));
				$ind_l .= $this->parse("IND_LIST");
				++$obj_count;
			}

			if ($obj_count)
			{
				$this->vars(array(
					"IND_LIST" => $ind_l
				));
				$ind = $this->parse("IND");
			}
			else
			{
				$ind = "";
			}
		}
		/* END IND LIST */

		/* EPAT LIST */
		$obj_list = new object_list(array(
			"class_id" => CL_EURO_PATENT_ET_DESC,
			"createdby" => $uid,
			"lang_id" => array(),
		));

		$obj_list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		lc_site_load("patent", $this);
		if($code)
		{
			$persons_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"personal_id" => $code
			));
			$other_list = new object_list(array(
 				"class_id" => CL_EURO_PATENT_ET_DESC,
 				"lang_id" => array(),
 				"authorized_codes" => "%".$code."%",
 			));
			$obj_list->add($other_list);

			foreach($persons_list->ids() as $id)
			{
				$other_list = new object_list(array(
					"class_id" => CL_EURO_PATENT_ET_DESC,
					"applicant" => $id,
					"lang_id" => array(),
				));
				$obj_list->add($other_list);

				$other_list = new object_list(array(
					"class_id" => CL_EURO_PATENT_ET_DESC,
					"lang_id" => array(),
					"authorized_person" => $id,
				));
				$obj_list->add($other_list);
			}
		}

		$objects_array = array();
		foreach($obj_list->arr() as $key => $patent)
		{
			$status = $this->get_status($patent);
			if($status->prop("nr"))
			{
				$objects_array[$status->prop("nr")] = $patent;
			}
			else
			{
				$objects_array[] = $patent;
			}
		}

		if(!$arr["unsigned"])
		{
			krsort($objects_array);
		}

		if ($this->is_template("EPAT_LIST"))
		{
			$obj_count = 0;
			$epat_l = "";
			foreach($objects_array as $key => $patent)
			{
				$patent->check_and_set_authorized_codes_user_access();
				$status = $this->get_status($patent);
				$re = $this->is_signed($patent->id());
				$sent_form = 0;

				if($send_patent == $patent->id() && $re["status"] == 1 && !$status->prop("nr"))
				{
					$_SESSION["patent"]["id"] = $patent->id();
					$asd = $this->set_sent(array("add_obj" => $arr["alias"]["to"]));
				}

				if($arr["unsigned"])
				{
					if($status->prop("nr")) continue;
					$date = date("d.m.Y" , $patent->created());
				}
				else
				{
					if(!$status->prop("nr")) continue;
					if($status->prop("sent_date"))
					{
						$date = date("d.m.Y" , $status->prop("sent_date"));
					}
					else
					{
						$date = date("d.m.Y" , $patent->created());
					}

					if ($status->prop("verified"))
					{
						$sent_form = 1;
					}
				}

				$url = aw_url_change_var("trademark_id", $patent->id());
				$url = aw_url_change_var("data_type", 0 , $url);
				$url = aw_url_change_var("new_application", null , $url);

				try
				{
					$section = aw_ini_get("clients.patent_office.epat_edit_section_id");
					$url = aw_url_change_var("section", $section, $url);
				}
				catch (Exception $e)
				{
					$url = "";
				}

				if(!$status->prop("verified") &&	!$status->prop("nr"))
				{
					$do_sign = 1;
			        	if($re["status"] == 1)
			        	{
			        		$sign_url = $ddoc_inst->sign_url(array(
							"ddoc_oid" => $re["ddoc"],
						));
			        	}
			        	else
			        	{
				        	$sign_url = $ddoc_inst->sign_url(array(
							"other_oid" =>$patent->id(),
						));
			          }
			          $sign = "<a href='javascript:void(0);' onclick='javascript:window.open(\"".$sign_url."\",\"\", \"toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600\");'>Allkirjasta</a>";
				}
				else
				{
					$do_sign = 0;
					$sign = "";
				}

				$view_url = $this->mk_my_orb("show", array(
					"print" => 1,
					"sent_form" => $sent_form,
					"id" => $patent->id(),
					"add_obj" => $arr["alias"]["to"],
					"sign" => $do_sign
				), CL_EURO_PATENT_ET_DESC);

				$change = $del_url = $send_url= '';
				if(!($status->prop("nr") || $status->prop("verified")))
				{
					$change = '<a href="'.$url.'">Muuda</a>';
					$del_url = aw_ini_get("baseurl").aw_url_change_var("delete_patent", $patent->id());
				}

				if(($re["status"] == 1))
				{
					$change = "";
					$url = aw_url_change_var("send_patent", $patent->id());
					$send_url = '<a href="'.$url.'"> Saada</a>';
				}

				//taotlejad komaga eraldatuld
				$applicant_str = $this->get_applicants_str($patent);

				$this->vars(array(
					"date" 		=> $date,
					"nr" 		=> ($status->prop("nr")) ? $status->prop("nr") : "",
					"applicant" 	=> $applicant_str,
					"state" 	=> ($status->prop("verified")) ? t("Vastu v&otilde;etud") : (($status->prop("nr")) ? t("Esitatud") : ""),
					"name" 	 	=> $status->name(),
					"id" 	 	=> $patent->id(),
					"url"  		=> $url,
					"procurator"  	=> $patent->prop_str("procurator"),
					"change"	=> $change,
					"view"		=> $view_url,
					"sign"		=> $sign,
					"delete"	=> $del_url,
					"send"		=> $send_url,
				));
				$epat_l .= $this->parse("EPAT_LIST");
				++$obj_count;
			}

			if ($obj_count)
			{
				$this->vars(array(
					"EPAT_LIST" => $epat_l
				));
				$epat = $this->parse("EPAT");
			}
			else
			{
				$epat = "";
			}
		}
		/* END EPAT LIST */

		$this->vars(array(
			"PAT" => $pat,
			"TM" => $tm,
			"UM" => $um,
			"IND" => $ind,
			"EPAT" => $epat
		));

		return $this->parse();
	}

	function get_applicants_str($o)
	{
		$aa = array();

		foreach($o->connections_from(array("type" => "RELTYPE_APPLICANT")) as $key => $c)
		{
			$applicant = $c->to();
			$aa[] =$applicant->name();
		}
		return join(", " , $aa);
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "aw_trademark")
		{
			$this->db_query("CREATE TABLE aw_trademark(
				aw_oid int primary key,
				aw_authorized_codes text
			)");
		}
		return true;
	}

	/**
		@attrib api=1
		@param o required type=object
		@returns
			PHP DOMDocument instance
	**/
	public function get_po_xml(object $o)
	{
		$ip_inst = $o->instance();
		$status = $ip_inst->get_status($o);
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= '<BIRTH TRANTYP="ENN" INTREGN="'.sprintf("%08d", $status->prop("nr")).'" OOCD="EE" ORIGLAN="3" REGEDAT="'.date("Ymd", $status->prop("sent_date")).'" INTREGD="'.date("Ymd", $status->prop("modified")).'" DESUNDER="P">';

		// common object xml data
		$adr_i = get_instance(CL_CRM_ADDRESS);

		foreach($o->connections_from(array("type" => "RELTYPE_APPLICANT")) as $c)
		{
			$applicant = $c->to();

			if ($this->can("view", $applicant->id()))
			{
				$xml .= '<HOLGR>';
				$xml .= "<NAME>";

				$appl = $applicant;
				$addr = "<ADDRESS>";
				$tel = array();

				if ($appl->class_id() == CL_CRM_PERSON)
				{
					if ($appl->prop("phone.name"))
					{
						$tel[] = trademark_manager::rere($appl->prop("phone.name"));
					}

					if ($appl->prop("fax.name"))
					{
						$tel[] = trademark_manager::rere($appl->prop("fax.name"));
					}

					$xml .= "<NAMEL>".trademark_manager::rere($applicant->prop("firstname"))."</NAMEL>";
					$xml .= "<NAMEL>".trademark_manager::rere($applicant->prop("lastname"))."</NAMEL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("address.aadress"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("address.linn.name"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("address.maakond.name"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("address.postiindeks"))."</ADDRL>";
					$addr .= "<ADDRL>" . implode(", ", $tel) . "</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("email.mail"))."</ADDRL>";
					if ($this->can("view", $appl->prop("address.riik")))
					{
						$addr .= "<COUNTRY>".trademark_manager::rere($adr_i->get_country_code(obj($appl->prop("address.riik"))))."</COUNTRY>";
					}
					$type = "1";
				}
				else
				{
					if ($appl->prop("phone_id.name"))
					{
						$tel[] = trademark_manager::rere($appl->prop("phone_id.name"));
					}

					if ($appl->prop("telefax_id.name"))
					{
						$tel[] = trademark_manager::rere($appl->prop("telefax_id.name"));
					}

					$xml .= "<NAMEL>".trademark_manager::rere($applicant->name())."</NAMEL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("contact.aadress"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("contact.linn.name"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("contact.maakond.name"))."</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("contact.postiindeks"))."</ADDRL>";
					$addr .= "<ADDRL>" . implode(", ", $tel) . "</ADDRL>";
					$addr .= "<ADDRL>".trademark_manager::rere($appl->prop("email_id.mail"))."</ADDRL>";
					if ($this->can("view", $appl->prop("contact.riik")))
					{
						$addr .= "<COUNTRY>".trademark_manager::rere($adr_i->get_country_code(obj($appl->prop("contact.riik"))))."</COUNTRY>";
					}
					$type = "2";
				}

				if (CL_PATENT !== $o->class_id())// temporarily
				{
					$addr .= "<TYPHOL>{$type}</TYPHOL>";
				}

				$addr .= "</ADDRESS>";

				$xml .= "</NAME>";
				$xml .= $addr;

				$xml .= '<LEGNATU><LEGNATT>'.trademark_manager::rere($appl->prop("ettevotlusvorm.name")).'</LEGNATT></LEGNATU>';
			}

			$xml .= "</HOLGR>";
		}

		if ($this->can("view", $o->prop("procurator")))
		{
			$proc = obj($o->prop("procurator"));
			$xml .= '<REPGR CLID="'.$proc->prop("code").'"><NAME><NAMEL>'.trademark_manager::rere($proc->prop("firstname")).'</NAMEL><NAMEL>'.trademark_manager::rere($proc->prop("lastname")).'</NAMEL></NAME></REPGR>';
		}

		$xml .= '<DESPG><DCPCD>EE</DCPCD></DESPG>';
		$xml .= '</BIRTH>';

		$xml_doc = new DOMDocument();
		$ret = $xml_doc->loadXML($xml);

		if (!$ret)
		{
			throw new aw_exception("Failed to load xml data");
		}

		$xml_doc->formatOutput = true;
		$xml_doc->encoding = trademark_manager::XML_OUT_ENCODING;

		return $xml_doc;
	}
}
?>

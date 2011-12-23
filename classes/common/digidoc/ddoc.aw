<?php
// ddoc.aw - DigiDoc
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@property name type=text field=name
	@caption Nimi

	@property ddoc type=fileupload field=meta method=serialize
	@caption Vali fail

	@property ddoc_location type=hidden field=meta method=serialize
	@caption asukoht

@groupinfo files caption="Failid" submit=no
@default group=files

	@property files_tb type=toolbar no_caption=1
	@property files_tbl type=table no_caption=1
	@property popup_search_res type=hidden name=search_result_file store=no


@groupinfo signatures caption="Allkirjad" submit=no
@default group=signatures

	@property signatures_tb type=toolbar no_caption=1
	@property signatures_tbl type=table no_caption=1

# hidden meta fields for 'cacheing'
@property files type=hidden field=meta method=serialize
@property signatures type=hidden field=meta method=serialize

@reltype SIGNED_FILE value=1 clid=CL_FILE,CL_PATENT,CL_PATENT_PATENT,CL_INDUSTRIAL_DESIGN,CL_EURO_PATENT_ET_DESC,CL_UTILITY_MODEL
@caption Allkirjastatud fail

@reltype SIGNER value=2 clid=CL_CRM_PERSON
@caption Allkirjastaja

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_PATENT, on_save_intellectual_property)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_PATENT_PATENT, on_save_intellectual_property)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_UTILITY_MODEL, on_save_intellectual_property)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_INDUSTRIAL_DESIGN, on_save_intellectual_property)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_EURO_PATENT_ET_DESC, on_save_intellectual_property)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_FILE, on_save_file)

*/

define("USER_DEFAULT_DIR", 2);
define("DEFAULT_DDOC_TYPE", "DIGIDOC-XML");
define("DEFAULT_DDOC_VERSION", "1.3");


class ddoc extends class_base
{
	function ddoc()
	{
		$this->init(array(
			"tpldir" => "common/digidoc",
			"clid" => CL_DDOC
		));
		$this->digidoc = false;

		$this->errors = array(
			"DDOC_ERR_WSDL" => "WSDL::%s failed. Pear error message: '%s'. %s",
			"DDOC_ERR_DDOC" => "ddoc::%s failed. %s",
			"DDOC_ERR_DIGIDOC" => "digidoc::%s failed. %s",

		);
		$this->warns = array(
			"DDOC_WARN_DDOC" => "ddoc::%s warning!. %s",
		);

		if (!defined("AW_DIGIDOC_CONST_INIT_DONE"))
		{
			define("AW_DIGIDOC_CONST_INIT_DONE", 1);

			foreach($this->errors as $k => $v)
			{
				define($k, $v);
			}

			foreach($this->warns as $k => $v)
			{
				define($k, $v);
			}
		}


		/*
		$loc = aw_ini_get("basedir")."/classes/common/digidoc/conf.php";
		include_once($loc);

		include_once(aw_ini_get("basedir")."/classes/common/digidoc/ddoc_parser.aw");
		include_once(aw_ini_get("basedir")."/classes/protocols/file/digidoc.aw");
		digidoc::load_WSDL();
		$this->digidoc = new digidoc(); //get_instance("protocols/file/digidoc");
		*/

	}

	/** Initializes the digidoc class
		@attrib api=1
	**/
	function do_init()
	{
		$loc = aw_ini_get("basedir")."/classes/common/digidoc/conf.php";
		if(!include_once($loc))
		{
			$this->sign_err(DDOC_ERR_DDOC, "do_init", "File '".$loc."' couldn't be opened.");
		}

		$loc = aw_ini_get("basedir")."/classes/common/digidoc/ddoc_parser.aw";
		if(!include_once($loc))
		{
			$this->sign_err(DDOC_ERR_DDOC, "do_init", "File '".$loc."' couldn't be opened.");
		}

		$loc = aw_ini_get("basedir")."/classes/protocols/file/digidoc.aw";
		if(!include_once($loc))
		{
			$this->sign_err(DDOC_ERR_DDOC, "do_init", "File '".$loc."' couldn't be opened.");
		}
		digidoc::load_WSDL();
		$this->digidoc = new digidoc();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "name":
				$prop["value"] = html::href(array(
					"caption" => $prop["value"],
					"url" => $this->mk_my_orb("get_ddoc_file", array(
						"oid" => $arr["obj_inst"]->id(),
					)),
				));
				break;
			case "files_tb":
				$tb = &$prop["vcl_inst"];
				$url = $this->mk_my_orb("upload_new_file", array(
					"ddoc" => $arr["obj_inst"]->id(),
				));
				$s = $this->is_signed($arr["obj_inst"]->id());
				$tb->add_button(array(
					"name" => "add_file",
					"tooltip" => t("Lisa fail"),
					"onClick" => "aw_popup_scroll('".$url."', 'caption', 300, 100);",
					"img" => "new.gif",
					"confirm" => $s?t("Antud DigiDoc on ALLKIRJASTATUD, faili lisamisega eemaldatatakse automaatselt allkirjad! Kas te soovite j".html_entity_decode("&auml;")."tkata?"):NULL,
				));
				$tb->add_button(array(
					"name" => "remove_file",
					"tooltip" => t("Eemalda fail"),
					"action" => "rem_file",
					"img" => "delete.gif",
					"confirm" => $s?t("Antud DigiDoc on ALLKIRJASTATUD, faili(de) eemaldamisega eemaldatakse ka allkirjad! Kas te soovite j".html_entity_decode("&auml;")."tkata?"):NULL,
				));
				$popup_search = get_instance("vcl/popup_search");
				$search_butt = $popup_search->get_popup_search_link(array(
					"pn" => "search_result_file",
					"clid" => CL_FILE,
					"confirm" => $s?t("Antud DigiDoc on ALLKIRJASTATUD, faili lisamisega eemaldatatakse automaatselt allkirjad! Kas te soovite j".html_entity_decode("&auml;")."tkata?"):NULL,
				));
				$tb->add_cdata($search_butt);
				break;
			case "files_tbl":
				$t = &$prop["vcl_inst"];
				$t->define_chooser(array(
					"name" => "sel",
					"field" => "file_id",
				));
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Fail"),
				));
				$t->define_field(array(
					"name" => "type",
					"caption" => t("T&uuml;&uuml;p"),
				));
				$t->define_field(array(
					"name" => "size",
					"caption" => t("Suurus"),
				));
				$files = aw_unserialize($arr["obj_inst"]->prop("files"));
				$file_inst = get_instance(CL_FILE);
				foreach($files as $id => $data)
				{
					$o = obj($data["file"]);
					$cl_id = $o->class_id();
					$cl = aw_ini_get("classes");
					$url = ($cl_id == CL_FILE)?$file_inst->get_url($data["file"], $data["name"]):$this->mk_my_orb("change", array("id" => $data["file"], "return_url" => get_ru()), $cl_id);
					$t->define_data(array(
						"file_id" => $id,
						"name" => html::href(array(
							"caption" => $data["name"]." (".$cl[$cl_id]["name"].")",
							"url" => $url,
						)),
						"type" => $data["type"],
						"size" => ($data["size"] > 1024)?round(($data["size"]/1024),2).t("kB"):$data["size"].t("B"),
					));
				}
				break;
			case "signatures_tb":
				$tb = &$prop["vcl_inst"];

				$sign_url = $this->sign_url(array(
					"ddoc_oid" => $arr["obj_inst"]->id(),
				));
				$tb->add_button(array(
					"name" => "add_signature",
					"tooltip" => t("Lisa allkiri"),
					"img" => "new.gif",
					"url" => "#",
					"onClick" => "aw_popup_scroll('".$sign_url."','Allkirjastamine', 410,250);",
				));
				$tb->add_button(array(
					"name" => "remove_signature",
					"tooltip" => t("Eemalda allkiri"),
					"action" => "rem_sig",
					"img" => "delete.gif",
				));

				break;
			case "signatures_tbl":
				$t = &$prop["vcl_inst"];
				$t->define_chooser(array(
					"name" => "sel",
					"field" => "sig_id",
				));
				$t->define_field(array(
					"name" => "firstname",
					"caption" => t("Eesnimi"),
				));
				$t->define_field(array(
					"name" => "lastname",
					"caption" => t("Perekonnanimi"),
				));
				$t->define_field(array(
					"name" => "pid",
					"caption" => t("Isikukood"),
				));
				$t->define_field(array(
					"name" => "time",
					"caption" => t("Aeg"),
				));
				$t->define_field(array(
					"name" => "role",
					"caption" => t("Roll"),
				));
				$t->define_field(array(
					"name" => "location",
					"caption" => t("Asukoht"),
				));

				$signatures = aw_unserialize($arr["obj_inst"]->prop("signatures"));
				foreach($signatures as $sig_id => $sig)
				{
					$loc = array();
					$loc[] = $sig["signing_town"];
					$loc[] = $sig["signing_state"];
					$loc[] = $sig["signing_index"];
					$loc[] = $sig["signing_country"];
					$t->define_data(array(
						"sig_id" => $sig_id,
						"firstname" => $sig["signer_fn"],
						"lastname" => $sig["signer_ln"],
						"pid" => $sig["signer_pid"],
						"time" => date("d/m/Y H:i:s" ,$sig["signing_time"]),
						"role" => $sig["signing_role"],
						"location" => (strlen($tmp = join(", ", $loc)))?$tmp:t("M&aauml;&aauml;ramata"),
					));
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
			//-- set_property --//
			case "ddoc":

				// actually i should check wheather it is a correct ddoc file
				if (is_array($data["value"]))
				{
					$file = $data["value"]["tmp_name"];
					$file_type = $data["value"]["type"];
					$file_name = $data["value"]["name"];
				}
				else
				{
					$file = $_FILES["ddoc"]["tmp_name"];
					$file_name = $_FILES["ddoc"]["name"];
					$file_type = $_FILES["ddoc"]["type"];
				};

				classload("common/digidoc/ddoc_parser");
				$parser = new ddoc2_parser();
				$parser->setDigiDocFormatAndVersion(file_get_contents($file));
				if(!strlen($parser->format) || !strlen($parser->version))
				{
					return PROP_IGNORE;
				}

				$cl_file = get_instance(CL_FILE);

				if (is_uploaded_file($file))
				{

					$pathinfo = pathinfo($file_name);
					if (empty($file_type))
					{
						$mimeregistry = get_instance("core/aw_mime_types");
						$realtype = $mimeregistry->type_for_ext($pathinfo["extension"]);
						$file_type = $realtype;
					};
					$final_name = $cl_file->generate_file_path(array(
						"type" => $file_type,
					));

					move_uploaded_file($file, $final_name);
					$arr["obj_inst"]->set_name($file_name);
					// vetax vana faili 2ra igaks-juhuks
					unlink($arr["obj_inst"]->prop("ddoc_location"));
					$arr["obj_inst"]->set_prop("ddoc_location", $final_name);
					//$arr["obj_inst"]->set_prop("type", $file_type);
					$this->file_type = $file_type;
				}
				else
				if (is_array($data["value"]) && $data["value"]["content"] != "")
				{
					$final_name = $cl_file->generate_file_path(array(
						"type" => "text/html",
					));
					$fc = fopen($final_name, "w");
					fwrite($fc, $data["value"]["content"]);
					fclose($f);
					// vetax vana faili 2ra igaks-juhuks
					unlink($arr["obj_inst"]->prop("ddoc_location"));
					$arr["obj_inst"]->set_prop("ddoc_location", $final_name);
					$arr["obj_inst"]->set_name($data["value"]["name"]);
					//$arr["obj_inst"]->set_prop("type", "text/html");
					$this->file_type = "text/html";
				}
				else
				{
					return PROP_IGNORE;
					//$retval = PROP_IGNORE;
				};
				$this->_do_reset_ddoc($arr["obj_inst"]->id(), false);
				break;
			case "ddoc_location":
				$retval = PROP_IGNORE;
				break;

		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		if($arr["request"]["search_result_file"])
		{
			$files = stristr($arr["request"]["search_result_file"], ",")?split(",", $arr["request"]["search_result_file"]):array();
			$file_inst = get_instance(CL_FILE);
			// well this loop filters out these files which already have been signed.. this actually sucks a little but what-dha-hek
			foreach($files as $k => $file)
			{
				$res = $file_inst->is_signed($file["file_oid"]);
				if($res["status"] == 1 || $res["status"] == 0)
				{
					unset($files[$k]);
				}
			}
			if($this->is_signed($arr["id"]) && count($files))
			{
				$this->remove_signatures($arr["id"]);
			}
			foreach($files as $file)
			{
				$this->add_file(array(
					"oid" => $arr["id"],
					"file_oid" => $file,
				));
			}
		}
	}

	function callback_post_save($arr)
	{
		// if we are dealing with a new ddoc instance and no ddoc file is set, we make a new empty ddoc container!:)
		if($arr["new"] == 1 && !strlen($arr["obj_inst"]->prop("ddoc_location")))
		{
			$this->create_empty($arr["obj_inst"]->id());
		}
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- msg methods --//

	// these functions remove dha signatures when saving a signed object
	function on_save_intellectual_property($arr)
	{
		$o = new object($arr["oid"]);
		$inst = $o->instance();
		$res = $inst->is_signed($arr["oid"]);
		if($res["status"] == 1)
		{
			if(!$this->remove_signatures($res["ddoc"]))
			{
				error::raise(array(
					"msg" => t("Ei suutnud eemaldada allkirju"),
				));
			}
		}
	}

	function on_save_file($arr)
	{
		$inst = get_instance(CL_FILE);
		$res = $inst->is_signed($arr["oid"]);
		if($res["status"] == 1)
		{
			$this->remove_signatures($res["ddoc"]);
		}
	}

//-- orb methods --//

	/**
		@attrib params=name all_args=1 name=rem_sig
		@comment
			removes signatures selected from signatures table
	**/
	function rem_sig($arr)
	{
		foreach($arr["sel"] as $sig_id)
		{
			$this->remove_signature($sig_id, $arr["id"]);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib params=name all_args=1 name=rem_file
		@commen
			removes files selected from files table
	**/
	function rem_file($arr)
	{
		// remove signatures if signed
		if($this->is_signed($arr["id"]))
		{
			$this->remove_signatures($arr["id"]);
		}
		foreach($arr["sel"] as $file_id)
		{
			// do something
			$this->remove_file($file_id, $arr["id"]);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib params=name name=upload_new_file all_args=1
		@param ddoc required type=oid
		@comment
			genereates new file upload form to ddoc
	**/
	function upload_new_file($arr)
	{

		$tpl = new aw_template();
		$tpl->init(array(
			"tpldir" => "common/digidoc",
		));
		$tpl->read_template("new_file.tpl");

		if(is_array($_FILES["new_file"]))
		{
			if(is_uploaded_file($_FILES["new_file"]["tmp_name"]))
			{
				if($this->is_signed($arr["ddoc"]))
				{
					$this->remove_signatures($arr["ddoc"]);
				}
				if(!$this->add_file(array(
					"oid" => $arr["ddoc"],
					"file" => array(
						"name" => $_FILES["new_file"]["name"],
						"size" => $_FILES["new_file"]["size"],
						"MIME" => $_FILES["new_file"]["type"],
						"error" => $_FILES["new_file"]["error"],
						"content" => file_get_contents($_FILES["new_file"]["tmp_name"]),
					),
				)))
				{
					error::raise(array(
						"msg" => t("Faili lisamine eba&otilde;nnestus!"),
					));
				}

			}
			$contents = $tpl->parse("DONE");
		}
		else
		{
			$tpl->vars(array(
				"ddoc" => $arr["ddoc"],
			));
			$contents = $tpl->parse("UPLOAD");
		}

		die($contents);

	}

//-- methods --//

	/**
		@attrib name=get_ddoc_file params=name all_args=1 api=1
		@comment
			saves the ddoc file (browser save popup)
	**/
	function get_ddoc_file($arr)
	{
		$content = $this->get_ddoc($arr["oid"]);
		$o = obj($arr["oid"]);
		$this->do_init();
		$name = (substr($o->name(), -4, 4) == ".ddoc")?$o->name():$o->name().".ddoc";
		ddFile::saveAs($name, $content);
	}

	/** creates a new empty container.
		@attrib params=pos api=1
		@param oid required type=oid
			ddoc object oid.
		@param name optional type=string
			new ddoc object name(default is:'Digidoc (dd/mm/yyyy hh:mm)')
	**/
	function create_empty($oid, $name = false)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "create_empty", "Parameter oid incorrect.");
			return false;
		}
		$name = $name?$name:"Digidoc (".date("d/m/Y H:i").")";

		$this->_s(false);
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->CreateSignedDoc((int)$_SESSION["scode"], DEFAULT_DDOC_TYPE, DEFAULT_DDOC_VERSION);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "CreateSignedDoc", "Creating new empty ddoc (Type:".DEFAULT_DDOC_TYPE."/Vers:".DEFAULT_DDOC_VERSION.") failed.", $ret->getMessage());
			error::raise(array(
				"msg" => t("Uue DigiDoc konteineri loomine eba&otilde;nnestus: ".$ret->getMessage()),
			));
		}
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", "Getting signed container failed.", $ret->getMessage());
			error::raise(array(
				"msg" => t("Ei saanud k&auml;tte DigiDoc konteinerit:".$ret->getMessage()),
			));
		}
		$p = new ddoc2_parser($ret["SignedDocData"]);
		$content = $p->getDigiDoc();
		if(!$this->set_ddoc($oid, $content, false))
		{
			$cl_file = get_instance(CL_FILE);
			$final_loc = $cl_file->generate_file_path(array(
				"type" => "xml/ddoc",
			));
			$o = obj($oid);
			$o->set_prop("ddoc_location", $final_loc);
			$o->save();
			if(!$this->set_ddoc($oid, $content, false))
			{
				error::raise(array(
					"msg" => t("DigiDoc'i faili sisu seadmine eba&otilde;nnestus"),
				));
			}
		}
		$o = obj($oid);
		$o->set_name($name);
		$o->save();
		$this->_e();
		return true;
	}

	/** Removes file from ddoc container. Given ddoc can't be signed.
		@attrib params=pos api=1
		@param id required type=string
			file id in the ddoc container
		@param oid required type=oid
			ddoc object oid.

		@returns
			true if given file was removed, false otherwise.
		@errors
			If this given ddoc is still signed, error will be raised.
			Error is raised also if params aren't correct.
	**/
	function remove_file($id, $oid)
	{
		if(substr($id,0,1) != "D" || !is_numeric(substr($id,1)))
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_file", "The removable file id(".$id.") isn't correct.");
			error::raise(array(
				"msg" => t("Eemaldatava faili id ei ole korrektne"),
			));
		}
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_file", "The object oid(".$oid.") is incorrect.");
			error::raise(array(
				"msg" => t("Vale objekti id!"),
			));
		}
		if($this->is_signed($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_file", "Can't remove file from signed ddoc.");
			error::raise(array(
				"msg" => t("Ei saa eemaldada faile allkirjastatud DigiDoc'ilt"),
			));
		}
		$this->_s($oid);
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->RemoveDataFile((int)$_SESSION["scode"], $id);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "RemoveDataFile", "Removing file from container failed.", $ret->getMessage());
			$this->_e();
			return false;
		}
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", "Getting new container contents failed.", $ret->getMessage());
			error::raise(array(
				"msg" => t("Ei saanud DigiDoc'i sisu k&auml;tte:".$ret->getMessage()),
			));
		}
		$p = new ddoc2_parser($ret["SignedDocData"]);
		if(!$this->set_ddoc($oid, $p->getDigiDoc(), true))
		{
			error::raise(array(
				"msg" => t("DigiDoc'i faili sisu seadmine eba&otilde;nnestus"),
			));
		}
		$this->_e();
		return true;
	}

	/** finds out if this document is signed or not
		@attrib params=pos api=1
		@param ddoc required type=oid
			the ddoc object oid

		@returns
			true if document is signed, false otherwise
		@errors
			error will be raised if oid is wrong.
	**/
	function is_signed($oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "is_signed", "Parameter oid(".$oid.") incorrect.");
			error::raise(array(
				"msg" => t("Vale objetki id!"),
			));
		}
		$o = obj($oid);
		$signatures = $o->prop("signatures");
		if (strlen($signatures))
		{
			$signatures = aw_unserialize($signatures);
		}

		$signatures_count = is_array($signatures) ? count($signatures) : 0;
		return (bool) $signatures_count;
	}

	/** removes all the signatures from given ddoc.
		@attrib params=pos api=1
		@param oid required type=oid
			the ddoc object oid

		@returns
			true or false depending on the success of the opertaion(true - succeeded)
	**/
	function remove_signatures($oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_signatures", "Parameter oid(".$oid.") incorrect.");
			return false;
		}
		$this->_s($oid);
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->GetSignedDocInfo((int)$_SESSION["scode"]);
		$ret = ddoc2_parser::Parse($this->digidoc->WSDL->xml, 'body');
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "GetSignedDocInfo", "Getting container info failed". $ret->getMessage());
			die("error@_remove_sigantures:getsigneddocinfo:".$ret->getMessage());
		}
		$this->_e();
		if(!is_array($ret2["SignedDocInfo"]["SignatureInfo"][0]))
		{
			$ret["SignedDocInfo"]["SignatureInfo"] = array(0 => $ret["SignedDocInfo"]["SignatureInfo"]);
		}
		foreach($ret["SignedDocInfo"]["SignatureInfo"] as $sign)
		{
			$this->remove_signature($sign["Id"], $oid);
		}
		return true;
	}

	private function raise($msg, $halt = true)
	{
		$this->read_template("error.tpl");
		$msgs = is_array($msg)?$msg:array($msg);
		foreach($msgs as $msg)
		{
			$this->vars(array(
				"msg" => $msg,
			));
			$output .= $this->parse("ERROR");
		}
		if($halt)
		{
			die($output);
		}
		else
		{
			return $this->parse("ERROR");
		}
	}

	private function _s($oid = false)
	{
		if(!$this->digidoc)
		{
			$this->do_init();
		}
		$cont = $oid?$this->get_ddoc($oid):"";
		$p = new ddoc2_parser($cont);
		$ret = $this->digidoc->WSDL->StartSession("", $oid?$p->GetDigiDoc(LOCAL_FILES):"", true, '');
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "startSession", $ret->message);
			switch(trim($ret->message))
			{
				case "curl_exec error 7":
					$msg = t("&Uuml;henduse probleemid. Hetkel ei ole kahjuks v&otilde;imalik allkirjastada!");
					break;
				default:
					$msg = t("Viga sessiooni loomisel!");
					break;
			}
			$this->raise($msg);
		}
		else
		{
			$xml = $p->Parse($this->digidoc->WSDL->xml, 'body');
			$_SESSION["scode"] = $xml["Sesscode"];
			$_SESSION["ddoc_name"] = $oid;
		}
	}

	private function _e()
	{
		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->CloseSession((int)$_SESSION["code"]);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "closeSession", $ret->message);
			return false;
		}
		return true;
	}

	/** Enables you to get ddoc contents if needed
		@attrib api=1 params=pos
		@param oid required type=oid
			CL_DDOC object oid

		@returns
			Retursn ddoc file contents.
	**/
	function get_ddoc($oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "get_ddoc", "Incorrect oid(".$oid.") parametere.");
			return false;
		}
		$o = obj($oid);
		return file_get_contents($o->prop("ddoc_location"));
	}

	/** Sets ddoc object($oid) contents(overwrites). All the cache data is renrewed automagically.
		@attrib api=1 params=pos
		@param oid required type=oid
			CL_DDOC objects oid
		@param contents required type=string
			DDoc file contents

		@returns
			true/false depending if the operation succeeded or not.
	**/
	function set_ddoc($oid, $contents, $reload_ddoc = true)
	{
		if(!is_oid($oid) || !strlen($contents))
		{
			$this->sign_err(DDOC_ERR_DDOC, "set_ddoc", "Incorrect object id or no contents supplied.");
			return false;
		}
		$o = obj($oid);
		$f = $o->prop("ddoc_location");
		if(!($h = @fopen($f,"w")))
		{
			$this->sign_err(DDOC_ERR_DDOC, "set_ddoc", sprintf("File '%s' can't be opened", $f));
			return false;
		}
		if(!fwrite($h, $contents))
		{
			$this->sign_err(DDOC_ERR_DDOC, "set_ddoc", "Can't write to '".$f."'");
			fclose($h);
			return false;
		}
		fclose($h);
		if($reload_ddoc)
		{
			$this->_do_reset_ddoc($oid);
		}
		return true;
	}

	/** adds file to ddoc container.
		@attrib api=1
		@param oid required type=oid
			ddoc object oid
		@param file_oid optional type=oid
		@param name optional type=string
		@param file optional type=array

		@comment
			Basically this adds file to ddoc container.
			Either $file_oid or $file must be set. if $file_oid is set, then corresponding CL_FILE object is taken and added to ddoc, otherwise file with given $name and $content is added, and the CL_FILE object is created automagically.
	**/
	function add_file($arr)
	{
		if(!is_oid($arr["oid"]))
		{
			$this->sign_err(DDOC_ERR_DDOC, "add_file", "Incorrect oid param.");
			return false;
		}
		if(!is_oid($arr["file_oid"]) && !is_oid($arr["other_oid"]) && (!$arr["file"]["name"] || !$arr["file"]["size"] || !$arr["file"]["MIME"] || !$arr["file"]["content"]))
		{
			$this->sign_err(DDOC_ERR_DDOC, "add_file", "Neither file oid, other object oid or uploaded file data wasn't set!");
			return false;
		}
		if($this->is_signed($arr["oid"]))
		{
			$this->sign_err(DDOC_WARN_DDOC, "add_file", "Can't add file to signed object.");
			return false;
		}

		if($arr["file_oid"])
		{
			// aw failiobjekti lisamine
			$f_inst = get_instance(CL_FILE);
			$f2 = $f_inst->get_file_by_id($arr["file_oid"], true);
			$file = array(
				"name" => $f2["properties"]["name"],
				"size" => @filesize($f2["properties"]["file"]),
				"MIME" => $f2["properties"]["type"],
				"content" => $f2["content"],
			);

			$this->_s($arr["oid"]);
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);

			$p = new ddoc2_parser();
			$o = obj($arr["oid"]);
			$_tmp = aw_unserialize($o->prop("files"));
			$n = (!is_array($_tmp) && !strlen($_tmp))?0:count($_tmp);
			$hash = $p->getFileHash($file, "D".$n);

			if(LOCAL_FILES)
			{
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $hash["Filename"], $hash["MimeType"], "HASHCODE", $hash["Size"], "sha1", $hash["DigestValue"], "");
			}
			else
			{
				$f = $file;
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $f["name"], $f["MIME"], "EMBEDDED_BASE64", $f["size"], "", "", chunk_split(base64_encode($f["content"]), "64", "\n"));
			}
			if(PEAR::isError($ret))
			{
				$this->sign_err(DDOC_ERR_WSDL, "addDataFile", $ret->getMessage());
				error::raise(array(
					"msg" => t("Ei saanud lisada faili konteinerisse:".$ret->getMessage()),
				));
			}
			// datafile added now

			// lets get the new and improved ddoc file
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
			if(PEAR::isError($ret))
			{
				$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", $ret->getMessage(), " error getting new & updated ddoc contents");
				error::raise(array(
					"msg" => t("Ei saanud DigiDoc konteinerit k&auml;tte:".$ret->getMessage()),
				));
			}
			$p = new ddoc2_parser($ret["SignedDocData"]);
			$content = $p->getDigiDoc();
			if(!$this->set_ddoc($arr["oid"], $content, false))
			{
				error::raise(array(
					"msg" => t("DigiDoc faili sisu m&auml;&auml;ramine eba&otilde;nnestus!"),
				));
			}
			// we do this manually, so the bastard wouldn't do a new file in reser_ddoc()
			$this->_write_file_metainfo(array(
				"ddoc_id" => "D".$n,
				"oid" => $arr["oid"],
				"file" => $arr["file_oid"],
				"size" => $file["size"],
				"type" => $file["MIME"],
				"name" => $file["name"],
				"hash" => $hash["DigestValue"],
			));

			$this->_e();
		}
		elseif($arr["other_oid"])
		{
			// aw patendi lisamine
			$other_obj = obj($arr["other_oid"]);
			$content = $other_obj->get_xml();
			$file = array(
				"name" => $other_obj->name(), //$f2["properties"]["name"],
				"size" => strlen($content), //@filesize($f2["properties"]["file"]),
				"MIME" => "text/xml", //$f2["properties"]["type"],
				"content" => $content,
			);

			$this->_s($arr["oid"]);
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);

			$p = new ddoc2_parser();
			$o = obj($arr["oid"]);
			$_tmp = aw_unserialize($o->prop("files"));
			$n = (!is_array($_tmp) && !strlen($_tmp))?0:count($_tmp);
			$hash = $p->getFileHash($file, "D".$n);

			if(LOCAL_FILES)
			{
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $hash["Filename"], $hash["MimeType"], "HASHCODE", $hash["Size"], "sha1", $hash["DigestValue"], "");
			}
			else
			{
				$f = $file;
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $f["name"], $f["MIME"], "EMBEDDED_BASE64", $f["size"], "", "", chunk_split(base64_encode($f["content"]), "64", "\n"));
			}
			if(PEAR::isError($ret))
			{
				$this->sign_err(DDOC_ERR_WSDL, "addDataFile", $ret->getMessage());
				error::raise(array(
					"msg" => t("Ei saanud lisada faili konteinerisse:".$ret->getMessage()),
				));
			}
			// datafile added now

			// lets get the new and improved ddoc file
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
			if(PEAR::isError($ret))
			{
				$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", $ret->getMessage());
				error::raise(array(
					"msg" => t("Ei saanud DigiDoc konteinerit k&auml;tte:".$ret->getMessage()),
				));
			}
			$p = new ddoc2_parser($ret["SignedDocData"]);
			$content = $p->getDigiDoc();
			if(!$this->set_ddoc($arr["oid"], $content, false))
			{
				error::raise(array(
					"msg" => t("DigiDoc faili sisu m&auml;&auml;ramine eba&otilde;nnestus!"),
				));
			}
			// we do this manually, so the bastard wouldn't do a new file in reser_ddoc()
			$this->_write_file_metainfo(array(
				"ddoc_id" => "D".$n,
				"oid" => $arr["oid"],
				"file" => $arr["other_oid"],
				"size" => $file["size"],
				"type" => $file["MIME"],
				"name" => $file["name"],
				"hash" => $hash["DigestValue"],
			));

			$this->_e();
		}
		elseif($arr["file"]["name"] || $arr["file"]["size"] || $arr["file"]["MIME"] || $arr["file"]["content"])
		{
			// uploaditud faili lisamine
			$this->_s($arr["oid"]);
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			if(LOCAL_FILES)
			{
				$p = new ddoc2_parser();
				$o = obj($arr["oid"]);
				$n = count(aw_unserialize($o->prop("files")));
				$hash = $p->getFileHash($arr["file"], "D".$n);
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $hash["Filename"], $hash["MimeType"], "HASHCODE", $hash["Size"], "sha1", $hash["DigestValue"], "");
			}
			else
			{
				$f = $arr["file"];
				$ret = $this->digidoc->WSDL->addDataFile((int)$_SESSION["scode"], $f["name"], $f["MIME"], "EMBEDDED_BASE64", $f["size"], "", "", chunk_split(base64_encode($f["content"]), "64", "\n"));
			}
			if(PEAR::isError($ret))
			{
				error::raise(array(
					"msg" => t("Ei saanud lisada faili konteinerisse:".$ret->getMessage()),
				));
			}

			// lets get the new and improved ddoc file
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
			if(PEAR::isError($ret))
			{
				error::raise(array(
					"msg" => t("Ei saanud DigiDoc konteinerit k&auml;tte:".$ret->getMessage()),
				));
			}
			$p = new ddoc2_parser($ret["SignedDocData"]);
			$content = $p->getDigiDoc();
			if(!$this->set_ddoc($arr["oid"], $content))
			{
				error::raise(array(
					"msg" => t("DigiDoc faili sisu m&auml;&auml;ramine eba&otilde;nnestus!"),
				));
			}
			$this->_e();
		}
		else
		{
			// et parameetrid valed siis vist
			return false;
		}
		return true;
	}

	/** Get signers, signing times etc..
		@attrib params=pos api=1
		@param oid optional type=oid
			ddoc object oid

	**/
	function get_signatures($oid)
	{
		if(!is_oid($oid))
		{
			return array();
		}
		$o = obj($oid);
		return aw_unserialize($o->prop("signatures"));
	}

	/**
		@comment
			used internally by _do_reset_ddoc
	**/
	private function _hash_exists($ddoc, $hash)
	{
		$o = obj($ddoc);
		$m = aw_unserialize($o->prop("files"));
		foreach($m as $data)
		{
			if($data["hash"] == $hash)
			{
				return $data;
			}
		}
		return false;
	}

	/**
		@param ddoc_id required type=int
			file id in the ddoc file container
		@param remove optional type=bool
			if this is set to true, file with $ddoc_id is removed from metainfo
		@param oid required type=oid
			aw CL_DDOC object oid
		@param file required type=oid
			aw CL_FILE object oid
		@param size optional type=int
		@param type optional type=string
		@param name optional type=string
		@param hash optional type=string
		@comment
			adds file to files metainfo in ddoc object.
		@returns
			true on success, false otherwise
	**/
	private function _write_file_metainfo($arr)
	{
		if(!is_oid($arr["oid"]) || !strlen($arr["ddoc_id"]) || (!is_oid($arr["file"]) && !$arr["remove"]))
		{
			$this->sign_err(DDOR_ERR_DDOC, "_write_file_metainfo", "parameters incorrect");
			return false;
		}
		$o = obj($arr["oid"]);
		$m = aw_unserialize($o->prop("files"));
		if($arr["remove"])
		{
			unset($m[$arr["ddoc_id"]]);
		}
		else
		{
			$m[$arr["ddoc_id"]] = array(
				"file" => strlen($arr["file"])?$arr["file"]:$m[$arr["ddoc_id"]]["file"],
				"size" => strlen($arr["size"])?$arr["size"]:$m[$arr["ddoc_id"]]["size"],
				"type" => strlen($arr["type"])?$arr["type"]:$m[$arr["ddoc_id"]]["type"],
				"name" => strlen($arr["name"])?$arr["name"]:$m[$arr["ddoc_id"]]["name"],
				"hash" => strlen($arr["hash"])?$arr["hash"]:$m[$arr["ddoc_id"]]["hash"],
			);
		}
		$o->set_prop("files", aw_serialize($m, SERIALIZE_NATIVE));
		$o->save();
		return true;
	}

	/**
		@attrib params=pos
		@param oid required type=oid
			the CL_DDOC object's oid which data is to be resetted.
		@param save optional type=bool
			if set to false, old file objects are ignored and new objects are recreated. this is nessecary if one uploads new ddoc container for example.
		@comment
			resets the ddoc metadata.
	**/
	private function _do_reset_ddoc($oid, $save = true)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDDOC, "_do_reset_ddoc", "Incorrect parameter oid.");
			return false;
		}

		$this->_clear_old_signatures($oid);
		if(!$save)
		{
			$this->_clear_old_signed_files($oid);
		}

		$o = obj($oid);

		$this->_s($oid);

		//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
		$ret =  $this->digidoc->WSDL->GetSignedDocInfo((int)$_SESSION["scode"]);
		if(PEAR::isError($ret))
		{
			$this->sign_err("DDOC_ERR_WSDL", "GetSignedDocInfo", $ret->getMessage());
		}

		$ret2 = ddoc2_parser::Parse($this->digidoc->WSDL->xml, 'body');

		if(!is_array($ret2["SignedDocInfo"]["DataFileInfo"][0]) && isset($ret2["SignedDocInfo"]["DataFileInfo"]))
		{
			$ret2["SignedDocInfo"]["DataFileInfo"] = array(0 => $ret2["SignedDocInfo"]["DataFileInfo"]);
		}
		if(!is_array($ret2["SignedDocInfo"]["SignatureInfo"][0]) && isset($ret2["SignedDocInfo"]["SignatureInfo"]))
		{
			$ret2["SignedDocInfo"]["SignatureInfo"] = array(0 => $ret2["SignedDocInfo"]["SignatureInfo"]);
		}
		// get files
		$p = new ddoc2_parser();

		foreach($ret2["SignedDocInfo"]["DataFileInfo"] as $std_obj)
		{
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			$file = $this->digidoc->WSDL->GetDataFile((int)$_SESSION["scode"], $std_obj["Id"]);
			if(PEAR::isError($file))
			{
				$this->sign_err(DDOC_ERR_WSDL, "getDataFile", $file->getMessage());
			}
			$hash = $p->getFileHash(array(
				"name" => $file["DataFileData"]->Filename,
				"MIME" => $file["DataFileData"]->MimeType,
				"size" => $file["DataFileData"]->Size,
				"content" => base64_decode($file["DataFileData"]->DfData),
			), $file["DataFileData"]->Id);
			$hash = $hash["DigestValue"];

			$files[$file["DataFileData"]->Id] = array(
				"content" => base64_decode($file["DataFileData"]->DfData),
				"name" => $file["DataFileData"]->Filename,
				"type" => $file["DataFileData"]->MimeType,
				"size" => $file["DataFileData"]->Size,
				"hash" => $hash,
			);
		}
		// set files
		$file_inst = get_instance(CL_FILE);
		// i don't use the parser results here because i don't get the file contents from there so easily
		foreach($files as $ddoc_id => $data)
		{
			// anyway.. this checks if there is need to recreate aw file objects($save var),
			// and if given hash exists already in ddoc metadata. if it doesen't aw file object is created
			$_inside_new_ddoc[$ddoc_id] = $data["hash"];
			if(!$save || !($from_hash_exists = $this->_hash_exists($oid, $data["hash"])))
			{
				$id = $file_inst->create_file_from_string(array(
					"parent" => $o->parent(),
					"content" => $data["content"],
					"name" => $data["name"],
					"type" => $data["type"],
				));

				$this->_write_file_metainfo(array(
					"ddoc_id" => $ddoc_id,
					"oid" => $oid,
					"file" => $id,
					"size" => $data["size"],
					"type" => $data["type"],
					"name" => $data["name"],
					"hash" => $data["hash"],
				));
				$o->connect(array(
					"type" => "RELTYPE_SIGNED_FILE",
					"to" => $id,
				));
			}
			else
			{
				// well.. this is for the moment's where connection to aw file object is lost somewhy
				// actually i should do a object check also ..:S
				$o->connect(array(
					"type" => "RELTYPE_SIGNED_FILE",
					"to" => $from_hash_exists["file"],
				));
			}

		}
		// welll.. this is for case when file has been removed from ddoc, and needs to be removed from meta also
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
		$_meta_files = aw_unserialize($o->prop("files"));
		foreach($_meta_files as $ddoc_id => $data)
		{
			if($_inside_new_ddoc[$ddoc_id] != $data["hash"])
			{
				$this->_write_file_metainfo(array(
					"oid" => $oid,
					"ddoc_id" => $ddoc_id,
					"remove" => true,
				));
				// TODO remove at least connection to file.(maybe object itself also.. i should think about it a bit)
			}
		}aw_disable_acl();
		$o->save();
		aw_restore_acl();
		// set signatures
		foreach($ret2["SignedDocInfo"]["SignatureInfo"] as $sign)
		{
			$name = $sign["Signer"]["CommonName"];
			$name = split(",", $name);
			// why the hell do they put the T in the middle???..
			$signing_time = strtotime(str_replace("T", " ",$sign["SigningTime"]));

			$filter = array(
				"class_id" => CL_CRM_PERSON,
				"firstname" => ($fn = mb_convert_encoding(mb_convert_case($name[1], MB_CASE_TITLE, "UTF-8"), "ISO-8859-1", "UTF-8")),
				"lastname" => ($ln = mb_convert_encoding(mb_convert_case($name[0], MB_CASE_TITLE, "UTF-8"), "ISO-8859-1", "UTF-8")),
				"personal_id" => $sign["Signer"]["IDCode"],
			);
			$ol = new object_list($filter);
			if($ol->count())
			{
				$p_obj = $ol->begin();
			}
			else
			{
				$p_obj = new object();
				$p_obj->set_class_id(CL_CRM_PERSON);
				$p_obj->set_parent(USER_DEFAULT_DIR);
				$p_obj->set_name($fn." ".$ln);
				$p_obj->set_prop("firstname", $fn);
				$p_obj->set_prop("lastname", $ln);
				$p_obj->set_prop("personal_id", $sign["Signer"]["IDCode"]);
				$p_obj->save();
			}
			$retval = $this->_write_signature_metainfo(array(
				"ddoc_id" => $sign["Id"],
				"oid" => $oid,
				"signer" => $p_obj->id(),
				"signer_fn" => $fn,
				"signer_ln" => $ln,
				"signer_pid" => $sign["Signer"]["IDCode"],
				"signing_time" => $signing_time,
				"signing_town" => $sign["SignatureProductionPlace"]["City"],
				"signing_state" => $sign["SignatureProductionPlace"]["StateOrProvince"],
				"signing_index" => $sign["SignatureProductionPlace"]["PostalCode"],
				"signing_country" => $sign["SignatureProductionPlace"]["CountryName"],
				"signing_role" => $sign["SignerRole"]["Role"],
			));

			$o->connect(array(
				"type" => "RELTYPE_SIGNER",
				"to" => $p_obj->id(),
			));

			// close the session
			$this->_e();
		}aw_disable_acl();

		$o->save();aw_restore_acl();
	}

/// SIGNING PROCESS

	/** Well, this method gives a link that pop's up the singing window!!
		@attrib api=1
		@param ddoc_oid optional type=oid
			aw CL_DDOC object oid
		@param file_oid optional type=oid
		@param doc_oid optional type=oid
		@param no_refresh optional type=int
		@comment
			Well, this method gives a link that pop's up the singing window!!
			This is the function to use when you want to sign something!!
	**/
	function sign_url($arr)
	{
		if (
			(!isset($arr["ddoc_oid"]) or !$this->can("view", $arr["ddoc_oid"])) &&
			(!isset($arr["file_oid"]) or !$this->can("view", $arr["file_oid"])) &&
			(!isset($arr["other_oid"]) or !$this->can("view", $arr["other_oid"]))
		)
		{
			return t("#");
		}

		return $this->mk_my_orb("sign", $arr, CL_DDOC);
	}

	/** Call this when signing errors out
		@attrib api=1
	**/
	function sign_err()
	{
		if(($argc = func_num_args()) < 1)
		{
			return false;
		}
		$argv = func_get_args();
		$msg = $argv[0];
		unset($argv[0]);

		$no_backtrace = false;
		if($argv[1] == "NO_BACKTRACE")
		{
			$no_backtrace = true;
			unset($argv[1]);
		}
		$backtrace = debug_backtrace();

		$handle = fopen(aw_ini_get("site_basedir")."/files/digidoc_error_dbg", 'a');

		fwrite($handle, "\n---------");
		// ususal error info
		fwrite($handle, "\n[".date("d.m.Y h:i:s", time())."]");
		fwrite($handle, "\n".vsprintf($msg, $argv));

		if(!$no_backtrace)
		{
			$backtrace_len = 4;
			// backtrace
			fwrite($handle, "\nBacktrace (last ".$backtrace_len." calls):");
			for($i = 0; $i < $backtrace_len; $i++)
			{
				foreach(split("\n", print_r($backtrace[$i], true)) as $line)
					fwrite($handle, "\n".$line);
			}
		}

		// user info
		fwrite($handle, "\nUser:'".aw_global_get("uid")."', Request URI:'".$_SERVER["REQUEST_URI"]."', Remote addr(host):'".$_SERVER["REMOTE_ADDR"]."(".$_SERVER["REMOTE_HOST"].")', USER_AGENT:'".$_SERVER["HTTP_USER_AGENT"]."', Referer:'".$_SERVER["HTTP_REFERER"]."'");

		fclose($handle);
		return $msg;
	}

	/**
		@attrib params=name name=sign all_args=1
		@param step optional type=string
			Signing procedure step: PREPARE, FINALIZE or END
		@param ddoc_oid
		@param file_oid
		@param doc_oid
		@param check optional type=array
			array(
				personal_id,
			)
			if these are'nt correct signing will not be allowed
	**/
	function sign($arr)
	{enter_function("ddoc::sign");
		// this do_init() is here because usually does this $this->_s() but in signing _s() is called only in prepare stadium
		$this->do_init();

		/*
			siin peaks siis olema k6igepealt paramite kontroll.
			Siis peaks PREPARE staadiumis (kui on tegemist aw faili v6i aw docuga) tegema ddoc faili.
			ja ylej22nud on nagu ikka vist.. vms..
		*/
		if($arr["step"] != "PREPARE" && $arr["step"] != "FINALIZE" && $arr["step"] != "END")
		{
			$arr["step"] = "PREPARE";
		}

		// TODO
		// OKEI SIIN MA PEAKS SIIS PREPARE STAADIUMIS VAATAMA MIDA TAHEKTASE ALLKIRJASTADA(kas ddoc v6i midagi muud
		/*
			kui keegi m6tleb et miks seda kontrolli ja tyhja digidoc konteineri tegemist ei v6iks varem teha, ning siia j22ks ainult
			allkirjastamise funktsionaalsus, siis lihtsalt siin on asi kindel et l2heb allkirjastamiseks. ehk siis muiduiiufsd
		*/
		if($arr["step"] == "PREPARE")
		{
			if(!isset($arr["ddoc_oid"]))
			{
				// tahetakse faili allkirjastada, ehk peab tegema uue tyhja ddoc konteineri ja faili sinna ennem panema
				if(is_oid($arr["file_oid"]))
				{
					$file_obj = obj($arr["file_oid"]);
					$new_ddoc = obj();
					$new_ddoc->set_class_id(CL_DDOC);
					$new_ddoc->set_parent($file_obj->parent());
					$new_ddoc->save_new();

					if(!$this->create_empty($new_ddoc->id(), $file_obj->name()))
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "Creating new empty ddoc container failed.");
						error::raise(array(
							"msg" => t("Uue t&uuml;hja digidoc konteineri loomine eba&otilde;nnestus"),
						));
					}
					if(!$this->add_file(array(
						"oid" => $new_ddoc->id(),
						"file_oid" => $arr["file_oid"],
					)))
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "Adding file to ddoc container failed.");
						error::raise(array(
							"msg" => t("Faili lisamine digidoc konteinerisse eba&otilde;nnestus"),
						));
					}
					$arr["ddoc_oid"] = $new_ddoc->id();
				}
				elseif(is_oid($arr["other_oid"]))
				{
					$other_obj = obj($arr["other_oid"]);
					if($other_obj->class_id() == CL_FILE )
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "Incorrect object id, nothing to sign.");
						return false;
					}
					$new_ddoc = obj();
					$new_ddoc->set_class_id(CL_DDOC);
					$new_ddoc->set_parent($other_obj->parent());
					$new_ddoc->save_new();

					if(!$this->create_empty($new_ddoc->id(), $other_obj->name()))
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "Creating new empty ddoc container failed.");
						error::raise(array(
							"msg" => t("Uue t&uuml;hja digidoc konteineri loomine eba&otilde;nnestus"),
						));
					}
					if(!$this->add_file(array(
						"oid" => $new_ddoc->id(),
						"other_oid" => $arr["other_oid"],
					)))
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "Adding file to ddoc container failed.");
						error::raise(array(
							"msg" => t("Faili lisamine digidoc konteinerisse eba&otilde;nnestus"),
						));
					}
					$arr["ddoc_oid"] = $new_ddoc->id();

				}
				else
				{
					$this->sign_err(DDOC_ERR_DDOC, "sign", "Incorrect params. No suitable object to sign.");
					return false;
				}
			}
		}
		$ddoc_oid = $arr["ddoc_oid"];


		// lets start session if needed
		if($arr["step"] == "PREPARE")
		{
			$this->_s($ddoc_oid);
		}

		$tpl = new aw_template();
		$tpl->init(array(
			"tpldir" => "common/digidoc",
		));
		if($arr["step"] == "PREPARE" || $arr["step"] == "FINALIZE")
		{
			$browser = ddFile::getBrowser();
			$brow_os = ($browser['OS'] == 'Win' ? 'WIN32' : 'LINUX').'-'.($browser['BROWSER_AGENT']=='IE' ? 'IE' : 'MOZILLA');
			//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
			$ret = $this->digidoc->WSDL->getSignatureModules((int)$_SESSION["scode"], $brow_os, $arr["step"], "HTML");
			if(PEAR::isError($ret))
			{
				$this->sign_err(DDOC_ERR_WSDL, "getSignatureModules", "Getting signature modules failed.", $ret->getMessage());
				die("Failed getting signature modules: ".$ret->getMessage());
			}
			else
			{
				$mod = $ret["Modules"];
				while(list($k, $v) = each($mod))
				{
					$name = $v->Location;
					$mods[$name]["html"] = base64_decode($v->Content);
				}
			}
		}


		// wierd crap

		$rep = array(
			"{0}" => DD_DEF_LANG,
			"{1}" => isset($arr["signCertId"])?$arr["signCertId"]:"",
			"{2}" => "",
			"{3}" => "",
			"{4}" => isset($arr["signCertHex"])?$arr["signCertHex"]:"",
			"{5}" => $arr["step"],
			"{6}" => "",
			"driver_errror.jsp" => "?wtf=error",
			"documents.jsp" => "?wtf=documents",
			// for ff
			"SignApplet_sig.jar,iaikPkcs11Wrapper_sig.jar" => "estid/SignApplet_sig.jar,estid/iaikPkcs11Wrapper_sig.jar",
			// for ie
			"EIDCard.cab" => "estid/EIDCard.cab",
		);
		switch($arr["step"])
		{
			case "PREPARE":
				$tpl->read_template("sign_prepare.tpl");
				$tpl->vars(array(
					"HTML_HEAD_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-HEAD"]["html"]),
					"HTML_FORM_BEGIN_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-FORM-BEGIN"]["html"]),
					"HTML_FORM_END_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-FORM-END"]["html"]),
					"HTML_BODY_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-BODY"]["html"]),
					"reforb" => $this->mk_reforb("sign", array(
						"step" => "FINALIZE",
						"ddoc_oid" => $ddoc_oid,
					)),
				));
				$html = $tpl->parse();
			break;
			case "FINALIZE":
				$tpl->read_template("sign_finalize.tpl");

				// viimati finalize etapi l2binud korra kontroll
				$use_prev = false;
				if(isset($_SESSION["prev_sign_mark"]))
				{
					$use_prev = ((time() - $_SESSION["prev_sign_mark"]) < 3);
				}
				$_SESSION["prev_sign_mark"] = time();
				if(!$use_prev)
				{
					//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
					$ret = $this->digidoc->WSDL->PrepareSignature((int)($_SESSION["scode"]), $arr["signCertHex"], $arr["signCertId"], $arr["Role"], $arr["City"], $arr["State"], $arr["PostalCode"], $arr["Country"], "");
				}
				else
				{
					$ret = aw_unserialize($_SESSION["prev_sign_value"]);
				}

				// chekking preparation results
				if(PEAR::isError($ret))
				{
					$this->sign_err(DDOC_ERR_WSDL, "PrepareSignature", "Preparing signature in FINALIZE part failed.", $ret->getMessage());
					$err = array(
						t("Allkirjastamise ettevalmistusetapp eba&otilde;nnestus! Palun proovige uuesti!"),
						sprintf(t("T&auml;psem veateade: %s"), $ret->getMessage()),
					);
					$this->raise($err);
				}
				else
				{
					$_SESSION["prev_sign_value"] = aw_serialize($ret, SERIALIZE_NATIVE);
					$_SESSION["SignatureId"] = !isset($_SESSION["SignatureId"])?$ret["SignatureId"]:$_SESSION["SignatureId"];
					$_SESSION['SignedInfoDigest'] = !isset($_SESSION['SignedInfoDigest'])?$ret['SignedInfoDigest']:$_SESSION['SignedInfoDigest'];
					$rep['{2}'] = $_SESSION['SignedInfoDigest'];

					$tpl->vars(array(
						"HTML_HEAD_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-HEAD"]["html"]),
						"HTML_FORM_BEGIN_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-FORM-BEGIN"]["html"]),
						"HTML_FORM_END_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-FORM-END"]["html"]),
						"HTML_BODY_HTML" => str_replace(array_keys($rep), array_values($rep), $mods["HTML-BODY"]["html"]),
						"reforb" => $this->mk_reforb("FINALIZE", array(
							"SignatureId" => $_SESSION["SignatureId"],
							"SignedInfoDigest" => $_SESSION["SignedInfoDigest"],
							"step" => "END",
							"ddoc_oid" => $ddoc_oid,
						)),
					));
					$html = $tpl->parse();
				}


			break;
			case "END":
				unset($_SESSION["SignatureId"]);
				unset($_SESSION["SignedInfoDigest"]);
				//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
				$ret = $this->digidoc->WSDL->FinalizeSignature((int)$_SESSION["scode"], $arr["SignatureId"], $arr["signValueHex"]);
				if(PEAR::isError($ret))
				{
					$this->sign_err(DDOC_ERR_WSDL, "FinalizeSignature", "Finalizing signature in end part failed", $ret->getMessage());
					$err = array(
						t("Allkirjastamise l&otilde;puetapp eba&otilde;nnestus! Palun proovige uuesti."),
						sprintf(t("T&auml;psem veateade: %s"), $ret->getMessage()),
					);
					$this->raise($err);
				}
				else
				{
					//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
					$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
					if(PEAR::isError($ret))
					{
						$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", "Signing (end part) failed (getting new ddoc contents).", $ret->getMessage());
						$err = array(
							t("Allkirjastatud digidoc konteineri saamine eba&otilde;nnestus."),
							sprintf(t("T&auml;psem veateade: %s"), $ret->getMessage()),
						);
						$this->raise($err);
					}
					$p = new ddoc2_parser($ret["SignedDocData"]);
					$content = $p->getDigiDoc();
					if(!$this->set_ddoc($ddoc_oid, $content, true))
					{
						$this->sign_err(DDOC_ERR_DDOC, "sign", "End part of signing failed because setting new ddoc contents failed.");


						$err = array(
							t("DigiDoc konteineri sisu m&auml;&auml;ramine eba&otilde;nnestus"),
							sprintf(t("T&auml;psem veateade: %s"), $ret->getMessage()),
						);
						$this->raise($err);
					}

					/*
					tra mai saa aru, closesession ei t66ta, no sess error

					//$this->digidoc->addHeader("SessionCode", $_SESSION["scode"]);
					$ret = $this->digidoc->WSDL->CloseSession((int)$_SESSION["scode"]);
					if(PEAR::isError($ret))
					{
						die("closesssion m2daneb endi juures:".$ret->getMessage());
					}
					*/
					$tpl->read_template("sign_end.tpl");
					$html = $tpl->parse();
					$this->sign_err(DDOC_WARN_DDOC, "NO_BACKTRACE", "sign", "Signature succesfully added.");
				}


			break;
		}//arr($GLOBALS["awt"]);
		exit_function("ddoc::sign");
		die($html);
	}

	/** Removes signature from $oid. Param $id can be either the CL_CRM_PERSON oid attached to the signature, or signature id itself.
		@attrib api=1 params=pos
		@param id required type=string
			Can be CL_CRM_PERSON oid or signature id itself(in ddoc container. Format: 'Sxx' where the x's are numbers).
		@param oid required type=oid
			The CL_DDOC object oid
	**/
	function remove_signature($id, $oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_signature", "Parameter oid incorrect.");
			return false;
		}
		if(substr($id, 0, 1) == "S" && is_numeric(substr($id, 1)))
		{
			$_oid = false;
		}
		elseif(is_oid($id))
		{
			$_oid = true;
		}
		else
		{
			$this->sign_err(DDOC_ERR_DDOC, "remove_signature", "Parameter id incorrect.");
			return false;
		}
		if($_oid)
		{
			$o = obj($oid);
			foreach(aw_unserialize($o->prop("signatures")) as $ddoc_id => $data)
			{
				if($data["signer"] == $id)
				{
					$id = $ddoc_id;
					break;
				}
			}
		}
		// start session
		$this->_s($oid);

		//$this->digidoc->addHeader('SessionCode', $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->RemoveSignature((int)$_SESSION["scode"], $id);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "RemoveSignature", "Removing signature failed.", $ret->getMessage());
			error::raise(array(
				"msg" => t("Ei suutnud eemaldada allkirja digidoc konteinerist:".$ret->getMessage()),
			));
		}
		//$this->digidoc->addHeader('SessionCode', $_SESSION["scode"]);
		$ret = $this->digidoc->WSDL->GetSignedDoc((int)$_SESSION["scode"]);
		if(PEAR::isError($ret))
		{
			$this->sign_err(DDOC_ERR_WSDL, "GetSignedDoc", "Getting new DDoc container failed.", $ret->getMessage());
			error::raise(array(
				"msg" => t("Ei suutnud saada uut digidoc konteinerit:".$ret->getMessage()),
			));
		}
		else
		{
			$ddoc = new ddoc2_parser( $ret['SignedDocData'] );
			$ddcontent = ($ddoc->getDigiDoc());
			if(!$this->set_ddoc($oid, $ddcontent))
			{
				$msg = "Setting new digidoc contents failed.";
				$this->sign_err(DDOC_ERR_DDOC, "remove_signature", $msg);
				die($msg);
			}
		}
		// end session
		$this->_e();
		return true;
	}


	/**
		@param ddoc_id required type=int
			signature's id in the ddoc file container
		@param oid required type=oid
			aw CL_DDOC object oid
		@param signer required type=oid
			aw CL_CRM_PERSON object oid
		@param signer_fn required type=string
			signer firtname
		@param signer_ln required type=string
			signer lastname
		@param signer_pid required type=string
			signer personal id code
		@param signing_time required type=int
			signing time

		@param signing_town optional
		@param signing_county optional
		@param signing_index optional
		@param signing_country optional
		@param signing_role optional

		@comment
			write sigantures metainfo into ddoc object
	**/
	private function _write_signature_metainfo($arr)
	{
		if(!strlen($arr["ddoc_id"]) || !is_oid($arr["oid"]) || !is_oid($arr["signer"]) || !strlen($arr["signer_fn"]) || !strlen($arr["signer_ln"]) || !strlen($arr["signer_pid"]) || !strlen($arr["signing_time"]))
		{
			$this->sign_err(DDOC_ERR_DDOC, "_write_signature_metainfo", "Parameters incorrect.");
			return false;
		}
		$o = obj($arr["oid"]);
		$m = aw_unserialize($o->prop("signatures"));
		$m[$arr["ddoc_id"]] = $arr;
		$o->set_prop("signatures", aw_serialize($m, SERIALIZE_NATIVE));
		aw_disable_acl();
		$o->save();aw_restore_acl();
		return true;
	}

	private function _clear_old_signed_files($oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "_clear_old_signed_files", "Incorrect parameter oid.");
			return false;
		}
		$o = obj($oid);
		// deleting file objects
		foreach(aw_unserialize($o->prop("files")) as $data)
		{
			if(!is_oid($data["file"]))
			{
				continue;
			}
			$to = obj($data["file"]);
			$to->delete(true);
		}
		// clear metadata
		$o->set_prop("files", aw_serialize(array(), SERIALIZE_NATIVE));
		$o->save();
		return true;
	}

	/**
		@comment
			clears all cached data from object. Used internally, when new ddoc file is uploaded, or just the info needs to refreshed shomewhy..
	**/
	private function _clear_old_signatures($oid)
	{
		if(!is_oid($oid))
		{
			$this->sign_err(DDOC_ERR_DDOC, "_clear_old_signatures", "Incorrect parameter oid.");
			return false;
		}
		$o = obj($oid);
		// removing connections to signers
		foreach(aw_unserialize($o->prop("signatures")) as $data)
		{
			$o->disconnect(array(
				"from" => $data["signer"],
			));
		}
		// clear metadata
		$o->set_prop("signatures", aw_serialize(array(), SERIALIZE_NATIVE));
		aw_disable_acl();
		$o->save();aw_restore_acl();
		return true;
	}

}

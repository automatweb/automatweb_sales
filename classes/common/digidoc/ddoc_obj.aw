<?php

class ddoc_obj extends _int_object
{
	const CLID = 1186;

	const ESTEID_DEFAULT_DDOC_FORMAT = "DIGIDOC-XML";
	const ESTEID_DEFAULT_DDOC_VERSION = "1.3";
	const ESTEID_DEFAULT_DDOC_ENCODING = "UTF-8";

	const DATA_FILE_CONTENT_HASHCODE = 1;
	const DATA_FILE_CONTENT_EMBEDDED_BASE64 = 2;

	private $sk_digidoc_service_soap_client = null; // Zend_Soap_Client
	private $sk_session_code = "";
	private $digidoc = null; // DOMDocument
	private $digidoc_hashed = null; // DOMDocument digidoc xml with files in hashcode format
	private $digidoc_file_name = ""; // digidoc xml file name
	private $digidoc_file_previous_version_to_delete = ""; // digidoc xml is saved to a new file every time, old version is removed if transaction successful
	private $data_file_index = array(); // datafile id index array(file name => file id in digidoc xml, ...)
	private $digidoc_changed = false; // keeps track of changes made during a session. if true then on close the changes are retrieved from sk service
	private $signatures = array(); // signer personal id => signature_id
	private $files = array(); // signed object id => datafile id in ddoc container

	private $_new_file_content_cache = "";

	public function __construct(array $objdata = array())
	{
		$r = parent::__construct($objdata);

		if ($this->is_saved())
		{
			$this->sk_session_code = aw_session::get("aw.digidoc.sk_session_code");
			$this->signatures = safe_array($this->meta("signatures"));
			$this->files = safe_array($this->meta("files"));
		}

		return $r;
	}

	public function __destruct()
	{
		if (!$this->is_saved() and $this->prop("ddoc_location"))
		{ // clear file from disk since object wasn't saved but digidoc was created
			unlink($this->prop("ddoc_location"));
		}

		if ($this->digidoc_file_previous_version_to_delete)
		{ // file was saved. clear old version
			unlink($this->digidoc_file_previous_version_to_delete);
		}

		if ($this->sk_session_code)
		{
			aw_session::set("aw.digidoc.sk_session_code", $this->sk_session_code);
		}

		parent::__destruct();
	}

	/** Initiates digidoc service session.
		@attrib api=1 params=pos
		@returns bool
			TRUE if session was started, FALSE if already active
		@comment
			Use if need for multiple operations in one session.
			Session is required and if not manually started then automatically started and also closed for the following methods:
				sk_create_digidoc(),
				save() but only if sk_create_digidoc() not called before,
				sk_sign(),
				sk_add_file(),
				sk_remove_file(),
				sk_remove_signature()
			sk_close_session() is absolutely mandatory to call when finished
		@errors
			throws awex_ddoc_session if session is found for another object
			throws awex_ddoc_wsdl on failure
	**/
	public function sk_start_session()
	{
		if (!$this->is_saved())
		{
			throw new awex_obj_state_new("Not saved");
		}

		$this->_load_soap_client();
		$this->_load_digidoc_from_filesystem();
		$this->digidoc_changed = false;
		$existing_session_oid = (int) aw_session::get("aw.digidoc.sk_session_ddoc_oid");

		if (!$this->sk_session_code)
		{
			$this->_delete_sk_session_data(); // just in case
			$this->_convert_and_move_embedded_digidoc_files_to_hashed();
			$digidoc_xml = $this->digidoc_hashed ? $this->digidoc_hashed->saveXML() : "";
			list($status, $session_code, $null) = array_values($this->sk_digidoc_service_soap_client->StartSession("", $digidoc_xml, true, ""));
			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Error starting session for new digidoc object '%s'. Service status: %s", $this->name(), $status));
			}
			$this->sk_session_code = $session_code;
			aw_session::set("aw.digidoc.sk_session_code", $session_code);
			aw_session::set("aw.digidoc.sk_session_ddoc_oid", $this->id());
			$r = true;
		}
		elseif ($existing_session_oid !== (int) $this->id())
		{
			// check if session matches this object if oid exists
			// error if another object is valid
			try
			{
				$o = obj($existing_session_oid, array(), self::CLID);
				$e = new awex_ddoc_session(sprintf("An SK session already exists for object '%s'. Not starting for '%s'", $existing_session_oid, $this->id()));
				$e->violated_object = $o;
				throw $e;
			}
			catch (Exception $e)
			{
				// forget invalid old session
				$this->_delete_sk_session_data();
			}
		}
		else
		{
			$r = false;
		}

		return $r;
	}

	/** Terminates digidoc service session. Mandatory if sk_start_session() called and intended operations done
		@attrib api=1 params=pos
		@returns void
		@errors
			throws awex_obj_state if session not found
			throws awex_ddoc_wsdl if digidoc retrieval from service failed
			triggers E_USER_WARNING on failure
	**/
	public function sk_close_session()
	{
		if (!$this->sk_session_code)
		{
			throw new awex_obj_state("No session to close");
		}

		// check if session matches this object if oid exists
		$existing_session_oid = (int) aw_session::get("aw.digidoc.sk_session_ddoc_oid");
		if ($existing_session_oid !== (int) $this->id())
		{
			// error if another object is valid
			try
			{
				$o = obj($existing_session_oid, array(), self::CLID);
				$e = awex_ddoc_session(sprintf("Can't close another object '%s' session in '%s'", $existing_session_oid, $this->id()));
				$e->violated_object = $o;
				throw $e;
			}
			catch (Exception $e)
			{
				aw_session::del("aw.digidoc.sk_session_ddoc_oid");
			}
		}

		$this->_load_soap_client();
		$status = $this->sk_digidoc_service_soap_client->CloseSession($this->sk_session_code);

		// forget all session data
		$this->_delete_sk_session_data();

		// for debugging
		if ("OK" !== $status)
		{
			trigger_error(sprintf("Closing digidoc service session failed. Service status: %s", $status), E_USER_WARNING);
		}
	}

	private function _delete_sk_session_data()
	{
		$this->sk_session_code = "";
		aw_session::del("aw.digidoc.sk_session_code");
		aw_session::del("aw.digidoc.sk_session_ddoc_oid");
		aw_session::del("aw.digidoc.sk_session_data.certId");
		aw_session::del("aw.digidoc.sk_session_data.signatureId");
		aw_session::del("aw.digidoc.sk_session_data.hashHex");
	}

	public function sk_create_digidoc()
	{
		$local_session = $this->sk_start_session();

		/// create doc
		$format = $this->awobj_get_digidoc_format();
		$version = $this->awobj_get_digidoc_version();
		list($status, $signed_doc_info) = array_values($this->sk_digidoc_service_soap_client->CreateSignedDoc($this->sk_session_code, $format, $version));
		if ("OK" !== $status)
		{
			throw new awex_ddoc_wsdl(sprintf("Error creating digidoc for new object '%s'. Service status: %s", $this->name(), $status));
		}

		// save digidoc properties
		$this->set_prop("digidoc_format", $signed_doc_info->Format);
		$this->set_prop("digidoc_version", $signed_doc_info->Version);
		$this->set_prop("digidoc_encoding", self::ESTEID_DEFAULT_DDOC_ENCODING);

		/// end session if locally started
		if ($local_session)
		{
			$this->sk_close_session();
		}
	}

	public function sk_sign(ddoc_sk_signature $signature = null)
	{
		if (!($person = get_current_person() and $personal_id = $person->prop("personal_id")))
		{
			throw new awex_ddoc_no_person(sprintf("Can't sign '%s', person not found or no pid '%s'", $this->id(), $personal_id));
		}

		$local_session = $this->sk_start_session();

		if (!$signature)
		{
			$signature = new ddoc_sk_signature();
		}

		// process phase
		$signing_phase = $signature->phase;

		if (ddoc_sk_signature::PHASE_PREPARE === $signing_phase and $signature->signer_certificate and $signature->signer_token_id)
		{
			list($status, $signature_id, $signed_info_digest) = array_values($this->sk_digidoc_service_soap_client->PrepareSignature(
				$this->sk_session_code,
				$signature->signer_certificate,
				$signature->signer_token_id,
				$signature->role,
				$signature->city,
				$signature->state,
				$signature->postal_code,
				$signature->signing_profile
			));

			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Error preparing signature for object '%s'. Service status: %s. Parameters: ", $this->id(), $status));
			}

			$signature->id = $signature_id;
			$signature->signed_info_digest = $signed_info_digest;
			aw_session::set("aw.digidoc.sk_session_data.certId", $signature->signer_token_id);
			aw_session::set("aw.digidoc.sk_session_data.signatureId", $signature->id);
			aw_session::set("aw.digidoc.sk_session_data.hashHex", $signature->signed_info_digest);
			$signature->phase = ddoc_sk_signature::PHASE_FINALIZE;
			$this->digidoc_changed = true;
		}
		elseif (ddoc_sk_signature::PHASE_FINALIZE === $signing_phase and $signature->id and $signature->value)
		{
			list($status, $signed_doc_info) = array_values($this->sk_digidoc_service_soap_client->FinalizeSignature(
					$this->sk_session_code,
					$signature->id,
					$signature->value
				));

			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Error finalizing signature for object '%s'. Service status: %s", $this->id(), $status));
			}

			$this->signatures[$personal_id] = $signature->id;
			$signature->phase = ddoc_sk_signature::PHASE_DONE;
			$this->digidoc_changed = true;
		}
		else
		{
			$signature->input_applets_url = aw_ini_get("digidoc.applets_url");
		}

		/// end session if locally started
		if ($local_session and ddoc_sk_signature::PHASE_DONE === $signature->phase)
		{
			$this->sk_close_session();
		}

		return $signature;
	}

	/** Add file to digidoc container
		@attrib api=1 params=pos
		@param object type=object
			AutomatWeb object associated with added file content.
		@param content type=string
			File content
		@param type type=string
			File content mime type
		@param mode type=int default=self::DATA_FILE_CONTENT_HASHCODE
			File content add mode. One of self::DATA_FILE_CONTENT_... constants.
		@param type type=string default=""
			File name. If not specified, AutomatWeb object name is used
		@comment
		@returns void
		@errors
			throws awex_obj_state if document is already signed and therefore can't be changed
			throws awex_obj_param if file with $name already exists
			throws awex_obj_type with code 1 if $mode not valid
	**/
	public function sk_add_file(object $object, $content, $type, $mode = self::DATA_FILE_CONTENT_HASHCODE, $name = "")
	{
		if ($this->is_signed())
		{
			throw new awex_obj_state("Signed documents can't be changed. File not added");
		}

		$local_session = $this->sk_start_session();

		if (empty($name))
		{
			$name = $object->name();
		}

		if (isset($this->data_file_index[$name]))
		{
			throw new awex_obj_param("File with specified name '{$name}' already exists, duplicate file names not allowed");
		}

		$file_name = $name;
		$file_mime_type = $type;
		$file_size = strlen($content);
		$file_attributes = "";
		$file_id = "D" . (count($this->data_file_index) + 1);

		if (self::DATA_FILE_CONTENT_HASHCODE === $mode)
		{
			$file_content = "";
			$file_content_type = "HASHCODE";
			$file_digest_type = "SHA1";
			$file_digest_value = $this->_generate_datafile_element_sha1_hash($file_name, $file_mime_type, $file_id, $content);
		}
		elseif (self::DATA_FILE_CONTENT_EMBEDDED_BASE64 === $mode)
		{
			$file_digest_type = $file_digest_value = "";
			$file_content_type = "EMBEDDED_BASE64";
			$file_content = base64_encode($content);
		}
		else
		{
			throw new awex_obj_type("Invalid content mode specification '{$mode}'", 1);
		}

		list($status, $signed_doc_info) = array_values($this->sk_digidoc_service_soap_client->AddDataFile(
				$this->sk_session_code,
				$file_name,
				$file_mime_type,
				$file_content_type,
				$file_size,
				$file_digest_type,
				$file_digest_value,
				$file_attributes,
				$file_content
			));

		if ("OK" !== $status)
		{
			throw new awex_ddoc_wsdl(sprintf("Error adding file to digidoc. Oid '%s'. Service status: %s", $this->id(), $status));
		}

		$this->data_file_index[$file_name] = $file_id;
		$this->files[$object->id()] = $file_id;
		$this->digidoc_changed = true;
		$this->_new_file_content_cache = base64_encode($content);

		/// end session if locally started
		if ($local_session)
		{
			$this->sk_close_session();
		}
	}

	/** Removes data file from digidoc container
		@attrib api=1 params=pos
		@param name type=string
			File name to be removed
		@comment
		@returns
		@errors
			throws awex_obj_state if document is signed and can't be altered
			throws awex_obj_param if file not found
	**/
	public function sk_remove_file($name)
	{
		if ($this->is_signed())
		{
			throw new awex_obj_state("Signed documents can't be changed. File not removed");
		}

		if (!isset($this->data_file_index[$name]))
		{
			throw new awex_obj_param("File with specified name '{$name}' doesn't exist in the container");
		}

		$local_session = $this->sk_start_session();

		$file_id = $this->data_file_index[$name];
		list($status, $signed_doc_info) = array_values($this->sk_digidoc_service_soap_client->RemoveDataFile(
				$this->sk_session_code,
				$file_id
			));

		if ("OK" !== $status)
		{
			throw new awex_ddoc_wsdl(sprintf("Error remoing file from digidoc. Oid '%s'. Service status: %s", $this->id(), $status));
		}

		unset($this->data_file_index[$file_name]);
		$this->digidoc_changed = true;

		/// end session if locally started
		if ($local_session)
		{
			$this->sk_close_session();
		}
	}

	public static function sk_error_string($code)
	{
		$errors = array(
			100 => t("Tundmatu viga rakenduses."),
			101 => t("Allkirjastamine ei &otilde;nnestunud rakenduse vea t&otilde;ttu."),
			102 => t("Allkirjastamine ei &otilde;nnestunud rakenduse vea t&otilde;ttu."),
			103 => t("Allkirjastamine ei &otilde;nnestunud, rakendusel puudub teenusele ligip&auml&auml;s."),
			200 => t("Tundmatu viga teenuses."),
			201 => t("Allkirjastamine ei &otilde;nnestunud, sertifikaat puudub."),
			202 => t("Allkirjastamine ei &otilde;nnestunud, Teie sertifikaadi kehtivust ei olnud võimalik kontrollida."),
			300 => t("Allkirjastamine ei &otilde;nnestunud, telefoniga seotud viga."),
			301 => t("Allkirjastamine ei &otilde;nnestunud, Teil puudub Mobiil-ID teenuse kasutamise leping."),
			302 => t("Allkirjastamine ei &otilde;nnestunud, Teie sertifikaat ei kehti."),
			303 => t("Allkirjastamine ei &otilde;nnestunud, Teil pole Mobiil-ID aktiveeritud."),
		);

		return isset($errors[$code]) ? $errors[$code] : t("Tundmatu viga");
	}

	public function is_signed()
	{
		return (bool) count($this->signatures);
	}

	public function is_signed_by($personal_ids = array())
	{
		$signed = true;
		foreach ($personal_ids as $personal_id)
		{
			$signed = ($signed and !empty($this->signatures[$personal_id]));
		}
		return $signed;
	}

	/** Returns this object digidoc format XML file
		@attrib api=1 params=pos
		@returns string
		@errors
	**/
	public function get_digidoc_file_contents()
	{
		return file_exists($this->prop("ddoc_location")) ? file_get_contents($this->prop("ddoc_location")) : "";
	}

	public function awobj_get_digidoc_format()
	{
		return $this->prop("digidoc_format") ? $this->prop("digidoc_format") : self::ESTEID_DEFAULT_DDOC_FORMAT;
	}

	public function awobj_get_digidoc_version()
	{
		return $this->prop("digidoc_version") ? $this->prop("digidoc_version") : self::ESTEID_DEFAULT_DDOC_VERSION;
	}

	public function awobj_get_digidoc_encoding()
	{
		return $this->prop("digidoc_encoding") ? $this->prop("digidoc_encoding") : self::ESTEID_DEFAULT_DDOC_ENCODING;
	}

	public function save($check_state = false)
	{
		// set file name to object but now, to ensure transactional integrity as much as possible
		if ($this->digidoc_file_name)
		{
			$this->set_prop("ddoc_location", $this->digidoc_file_name);
		}

		if ($this->sk_session_code and $this->digidoc_changed)
		{
			/// save received digidoc xml to file
			list($status, $signed_doc_data) = array_values($this->sk_digidoc_service_soap_client->GetSignedDoc($this->sk_session_code));
			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Can't save. Error retrieving digidoc for new object '%s'. Service status: %s", $this->name(), $status));
			}

			$this->digidoc_hashed = new DOMDocument();
			$this->digidoc_hashed->loadXML($signed_doc_data);
			$this->_load_digidoc_from_filesystem();
			$this->_move_hashed_digidoc_to_embedded_replacing_data_files();
			$this->_save_digidoc_to_filesystem();
			$this->set_prop("digidoc_encoding", $this->digidoc->encoding);
			$this->digidoc_changed = false;
		}

		$this->set_meta("signatures", $this->signatures);
		$this->set_meta("files", $this->files);

		foreach ($this->files as $obj_id => $file_id)
		{
			if (!$this->is_connected_to(array("to" => $obj_id, "type" => "RELTYPE_SIGNED_FILE")))
			{
				$this->connect(array("to" => $obj_id, "type" => "RELTYPE_SIGNED_FILE"));
			}
		}

		return parent::save($check_state);
	}

	private function _load_digidoc_from_filesystem()
	{
		if (null === $this->digidoc and file_exists($this->prop("ddoc_location")))
		{
			$this->digidoc = new DOMDocument();
			$this->digidoc->load($this->prop("ddoc_location"));

			// load file index
			$files = $this->digidoc->getElementsByTagName("DataFile");
			foreach ($files as $file)
			{
				$this->data_file_index[$file->getAttribute("Filename")] = $file->getAttribute("Id");
			}

			$r = true;
		}
		else
		{
			$r = null !== $this->digidoc;
		}

		return $r;
	}

	private function _save_digidoc_to_filesystem()
	{
		$this->digidoc_file_previous_version_to_delete = $this->prop("ddoc_location");
		$cl_file = new file();
		$digidoc_file_name = $cl_file->generate_file_path(array(
			"type" => "xml/ddoc"
		));
		$xml = $this->digidoc->saveXML();
		$r = file_put_contents($digidoc_file_name, $xml);

		if (false === $r)
		{
			throw new awex_ddoc_file(sprintf("Couln't write ddoc '%s' xml to '%s'", $this->id(), $digidoc_file_name));
		}

		if ($r !== strlen($xml))
		{
			throw new awex_ddoc_file(sprintf("Error writing ddoc '%s' xml to '%s'", $this->id(), $digidoc_file_name));
		}

		$this->digidoc_file_name = $digidoc_file_name;
	}

	private function _load_soap_client()
	{
		if (!$this->sk_digidoc_service_soap_client)
		{
			$this->sk_digidoc_service_soap_client = new SoapClient(aw_ini_get("digidoc.service_uri"));
		}
	}

	private function _convert_and_move_embedded_digidoc_files_to_hashed()
	{
		if ($this->digidoc)
		{
			$this->digidoc_hashed = new DOMDocument();
			$this->digidoc_hashed->loadXML($this->digidoc->saveXML());
			$datafiles = $this->digidoc_hashed->getElementsByTagName("DataFile");
			foreach ($datafiles as $data_file_embedded)
			{
				$data_file_embedded_xml = $this->digidoc_hashed->saveXML($data_file_embedded);
				$data_file_element_hash = base64_encode(pack("H*", sha1($data_file_embedded_xml)));
				$data_file_hashed = $data_file_embedded->cloneNode(true);
				$data_file_hashed->nodeValue = "";
				$data_file_hashed->setAttribute("ContentType", "HASHCODE");
				$data_file_hashed->setAttribute("DigestType", "sha1");
				$data_file_hashed->setAttribute("DigestValue", $data_file_element_hash);
				$this->digidoc_hashed->replaceChild($data_file_hashed, $data_file_embedded);
			}
		}
	}

	private function _move_hashed_digidoc_to_embedded_replacing_data_files()
	{
		$digidoc_new = new DOMDocument();
		$digidoc_new->loadXML($this->digidoc_hashed->saveXML());

		$datafiles = $digidoc_new->getElementsByTagName("DataFile");

		if ($this->digidoc)
		{
			$xpath_old = new DOMXPath($this->digidoc);
			$xpath_old->registerNamespace("ddoc", "http://www.sk.ee/DigiDoc/v1.3.0#");
		}
		else
		{
			$xpath_old = false;
		}

		foreach ($datafiles as $data_file_hashed)
		{
			$id = $data_file_hashed->getAttribute("Id");
			$data_file_embedded = $xpath_old ? $xpath_old->query("//ddoc:DataFile[@Id='{$id}']")->item(0) : false;
			if ($data_file_embedded)
			{ // convert encoding
				$content = $data_file_embedded->nodeValue;
			}
			else
			{ // add new file node
				$content = $this->_new_file_content_cache;
			}

			$data_file_hashed->nodeValue = $content;
			$data_file_hashed->setAttribute("ContentType", "EMBEDDED_BASE64");
			$data_file_hashed->removeAttribute("DigestType");
			$data_file_hashed->removeAttribute("DigestValue");
		}

		$this->digidoc = $digidoc_new;
	}

	private function _generate_datafile_element_sha1_hash($file_name, $file_mime_type, $file_id, $file_content)
	{
		$file_size = strlen($file_content);
		$file_content	= base64_encode($file_content);
		$namespace = "http://www.sk.ee/DigiDoc/v1.3.0#";
		$data_file_element = <<<ENDDATAFILE
<DataFile xmlns="{$namespace}" ContentType="EMBEDDED_BASE64" Filename="{$file_name}" Id="{$file_id}" MimeType="{$file_mime_type}" Size="{$file_size}">{$file_content}
</DataFile>
ENDDATAFILE;

		$hash = base64_encode(pack("H*", sha1($data_file_element)));
		return $hash;
	}
}


/** Generic DigiDoc Exception **/
class awex_ddoc extends awex_obj {}

/** WSDL service exception **/
class awex_ddoc_wsdl extends awex_ddoc {}

/** Ddoc xml file i/o exception **/
class awex_ddoc_file extends awex_ddoc {}

/** Session violation **/
class awex_ddoc_session extends awex_ddoc
{
	public $violated_object = null;
}

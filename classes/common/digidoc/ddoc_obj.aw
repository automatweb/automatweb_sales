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
			throws awex_ddoc_wsdl on failure
	**/
	public function sk_start_session()
	{
		$this->_load_soap_client();
		$this->_load_digidoc_from_filesystem();
		$this->digidoc_changed = false;

		if (!$this->sk_session_code)
		{
			$this->_convert_and_move_embedded_digidoc_files_to_hashed();
			$digidoc_xml = $this->digidoc_hashed ? $this->digidoc_hashed->saveXML() : "";
			list($status, $session_code, $null) = array_values($this->sk_digidoc_service_soap_client->StartSession("", $digidoc_xml, true, ""));
			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Error starting session for new digidoc object '%s'. Service status: %s", $this->name(), $status));
			}
			$this->sk_session_code = $session_code;
			$r = true;
		}
		else
		{
			$r = false;
		}

		return $r;
	}

	/** Terminates digidoc service session. Mandatory if sk_start_session() called and intended operations done
		@attrib api=1 params=pos obj_save=1
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

		$this->_load_soap_client();

		if ($this->digidoc_changed)
		{
			/// save received digidoc xml to file
			list($status, $signed_doc_data) = array_values($this->sk_digidoc_service_soap_client->GetSignedDoc($this->sk_session_code));
			if ("OK" !== $status)
			{
				throw new awex_ddoc_wsdl(sprintf("Error retrieving digidoc for new object '%s'. Service status: %s", $this->name(), $status));
			}

			$this->digidoc_hashed = new DOMDocument();
			$this->digidoc_hashed->loadXML($signed_doc_data);
			$this->_move_hashed_digidoc_to_embedded_replacing_data_files();
			$this->_save_digidoc_to_filesystem();
			$this->set_prop("digidoc_encoding", $this->digidoc_hashed->encoding);
			$this->save();
		}

		$status = $this->sk_digidoc_service_soap_client->CloseSession($this->sk_session_code);
		$this->sk_session_code = "";
		if ("OK" !== $status)
		{
			trigger_error(sprintf("Closing digidoc service session failed. Service status: %s", $status), E_USER_WARNING);
		}
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
		$local_session = $this->sk_start_session();

		if (!$signature)
		{
			$signature = new ddoc_sk_signature();
		}

		//GetSignatureModules
		$signing_phase = $signature->phase;
		$return_type = "ALL";

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
				throw new awex_ddoc_wsdl(sprintf("Error preparing signature for object '%s'. Service status: %s", $this->id(), $status));
			}

			$signature->id = $signature_id;
			$signature->signed_info_digest = $signed_info_digest;
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
				throw new awex_ddoc_wsdl(sprintf("Error preparing signature for object '%s'. Service status: %s", $this->id(), $status));
			}

			$signature->phase = ddoc_sk_signature::PHASE_DONE;
		}
		else
		{
			$signature->modules = true;
		}

		$this->digidoc_changed = true;

		/// end session if locally started
		if ($local_session and ddoc_sk_signature::PHASE_DONE === $signature->phase)
		{
			$this->sk_close_session();
		}

		return $signature;
	}

	/** Add file to digidoc container
		@attrib api=1 params=pos
		@param name type=string
			File name
		@param content type=string
			File content
		@param type type=string
			File content mime type
		@param mode type=int default=self::DATA_FILE_CONTENT_HASHCODE
			File content add mode. One of self::DATA_FILE_CONTENT_... constants.
		@comment
		@returns void
		@errors
			throws awex_obj_state if document is already signed and therefore can't be changed
			throws awex_obj_param if file with $name already exists
			throws awex_obj_type with code 1 if $mode not valid
			throws awex_obj_type with code 2 if a $name parameter is of incorrect type or empty
	**/
	public function sk_add_file($name, $content, $type, $mode = self::DATA_FILE_CONTENT_HASHCODE)
	{
		if ($this->is_signed())
		{
			throw new awex_obj_state("Signed documents can't be changed. File not added");
		}

		$local_session = $this->sk_start_session();

		if (empty($name))
		{
			throw new awex_obj_type("Incorrect name parameter '{$name}'", 2);
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
		$this->digidoc_changed = true;

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

	public function is_signed()
	{
		return (bool) $this->meta("is_signed");
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
		if (!$this->prop("ddoc_location") and !$this->digidoc_file_name)
		{ // create empty digidoc if not present
			$this->sk_create_digidoc();
		}

		// set file name to object but now, to ensure transactional integrity as much as possible
		if ($this->digidoc_file_name)
		{
			$this->set_prop("ddoc_location", $this->digidoc_file_name);
		}

		if ($this->sk_session_code and $this->digidoc_changed)
		{ // retrieve doc from sk service and save it
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
		$r = file_put_contents($digidoc_file_name, $this->digidoc->saveXML());
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
			$this->digidoc_hashed = $this->digidoc_hashed->loadXML($this->digidoc->saveXML());
			$datafiles = $this->digidoc_hashed->getElementsByTagName("DataFile");
			foreach ($datafiles as $data_file_embedded)
			{
				$data_file_embedded_xml = $this->digidoc_hashed->saveXML($data_file_embedded);
				$data_file_element_hash = base64_encode(pack("H*", sha1($data_file_embedded_xml)));
				$data_file_hashed = $data_file_embedded->cloneNode(true);
				$this->digidoc_hashed->replaceChild($data_file_hashed, $data_file_embedded);
			}
		}
	}

	private function _move_hashed_digidoc_to_embedded_replacing_data_files()
	{
		$digidoc = new DOMDocument();
		$digidoc = $digidoc->loadXML($this->digidoc_hashed->saveXML());
		$datafiles_new = $this->digidoc->getElementsByTagName("DataFile");
		$datafiles_old = $digidoc->getElementsByTagName("DataFile");
		$digidoc->replaceChild($datafiles_new, $datafiles_old);
		$this->digidoc = $digidoc;
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

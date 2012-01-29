<?php

class ddoc_sk_signature
{
	const PHASE_PREPARE = "PREPARE";
	const PHASE_FINALIZE = "FINALIZE";
	const PHASE_DONE = "DONE";

	const CACHE_SUBDIR = "sk_esteid";
	const CACHE_HTML_SUBDIR_PREFIX = "sign_html_";

	private $_client_platform = "";
	private $_phase = self::PHASE_PREPARE; // PREPARE | FINALIZE | DONE
	private $_id = "";
	private $_value = ""; // value of the signature (signed hash) as a HEX string
	private $_signed_info_digest = ""; //The hash to be signed in HEX string format
	/*
	array(
		array(
			o Name – mooduli nimetus, FAIL tüübi moodulite puhul , tuleb moodul sama nimega faili salvestada.
			o Type - määrab, kas tegu on HTML või fail tüüpi mooduliga. HTML mooduli korral parameetri väärtuseks “HTML”, faili puhul “FAIL”.
			o Location – Määrab, kus kohta antud moodul tuleb veebilehel integreerida. Võimalikud variandid:
			- HTML-HEAD
			- HTML-FORM-BEGIN
			- HTML-FORM-END
			- HTML-BODY
			- HTML-SCRIPT
			- LIBDIR fail tuleb salvestada kataloogi, millele HTML lehel viidatakse, vaikimisi sama kataloog, kus script käivitati. Sõltuvalt HTML locationist tuleb moodul HTML lehel õigesse kohta paigutada.
			o ContentType – määrab mis kujul on kodeeritud content väljal olev sisu. Hetkel kasutatakse alati Base64 kodeeringut.
			o Content – mooduli sisu ContentType parameetris määratud kujul.
		),
		...
	)
	*/
	private $_signer_certificate = ""; // signer’s certificate in HEX string format (transformed from binary (DER) format).
	private $_signer_token_id = ""; // identifier of the private key slot on a smartcard. The signing software defines this value when reading the signer’s certificate
	private $_role = "";
	private $_city = "";
	private $_state = "";
	private $_postal_code = "";
	private $_country = "";
	private $_signing_profile = "";
	private $_input_applets_url = ""; // indicates that input for next phase is needed

	private $_signer_first_name = "";
	private $_signer_last_name = "";
	private $_signer_personal_id = "";
	private $_signing_time = 0;

	private $html_module_vars = array();

	public function __construct()
	{
		// $this->_read_client_platform();
	}

	public function __get($name)
	{
		$property = "_{$name}";

		if (!property_exists($this, $property))
		{
			throw new awex_not_available("Public read only property '{$name}' doesn't exist");
		}

		return $this->$property;
	}

	public function __set($name, $value)
	{
		$property = "_{$name}";

		if (!property_exists($this, $property))
		{
			throw new awex_not_available("Public read only property '{$name}' doesn't exist");
		}

		$this->$property = $value;
	}

	public function read_request(aw_request $request)
	{
		$this->_id = $request->arg("signatureId") or $this->_id = aw_session::get("aw.digidoc.sk_session_data.signatureId");
		$this->_value = $request->arg("signatureHex");
		$this->_signed_info_digest = $request->arg("hashHex") or $this->_signed_info_digest = aw_session::get("aw.digidoc.sk_session_data.hashHex");
		$this->_signer_token_id = $request->arg("certId") or $this->_signer_token_id = aw_session::get("aw.digidoc.sk_session_data.certId");
		$this->_signer_certificate = $request->arg("certHex");
		$this->_role = $request->arg("role");
		$this->_city = $request->arg("city");
		$this->_state = $request->arg("state");
		$this->_postal_code = $request->arg("postal_code");
		$this->_country = $request->arg("country");
		$this->_signing_profile = $request->arg("signing_profile");

		$phase = $request->arg("phase");
		$valid_phases = array(self::PHASE_PREPARE, self::PHASE_FINALIZE, self::PHASE_DONE);
		if ($phase and in_array($phase, $valid_phases))
		{
			$this->_phase = $phase;
		}
	}
}

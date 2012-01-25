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
	private $_signer_certificate; // signer’s certificate in HEX string format (transformed from binary (DER) format).
	private $_signer_token_id; // identifier of the private key slot on a smartcard. The signing software defines this value when reading the signer’s certificate
	private $_modules; // software plug in modules for client to use for signing

	private $_role = "";
	private $_city = "";
	private $_state = "";
	private $_postal_code = "";
	private $_country = "";
	private $_signing_profile = "";

	private $_modules_loaded = false; // indicates that software plug in modules for client to use for signing are loaded

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

		if ("modules" === $name)
		{
			if (is_object($value))
			{// old version sign applet module loading
				$applets_directory = aw_ini_get("digidoc.applets_dir") . $this->_get_lib_modules_subdir();
				if (!is_dir($applets_directory))
				{
					mkdir($applets_directory, 0755, true);
				}

				$cache_html_modules = array();
				foreach ($value->Modules as $key => $data)
				{
					$content = "BASE64" === $data->ContentType ? base64_decode($data->Content) : $data->Content;
					if ("LIBDIR" === $data->Location and "FILE" === $data->Type)
					{
						$r = file_put_contents($applets_directory . $data->Name, $content);
						if (!$r)
						{
							throw new awex_ddoc("Writing module '{$applets_directory}{$data->Name}' failed");
						}
					}
					elseif ("HTML" === $data->Type)
					{
						$key = $this->_get_html_modules_key();
						if (isset($cache_html_modules[$key][$data->Location]))
						{
							$cache_html_modules[$key][$data->Location] .= $content;
						}
						else
						{
							$cache_html_modules[$key][$data->Location] = $content;
						}
					}
				}

				foreach ($cache_html_modules as $key => $modules)
				{
					foreach ($modules as $location => $content)
					{
						cache::file_set_pt(self::CACHE_SUBDIR, $key, $location, $content);
					}
				}
			}

			$this->_modules_loaded = true;
		}
		else
		{
			$this->$property = $value;
		}
	}

	public function read_request(aw_request $request)
	{
		$this->_id = $request->arg("signatureId");
		$this->_value = $request->arg("signatureHex");
		$this->_signed_info_digest = $request->arg("hashHex");
		$this->_signer_token_id = $request->arg("certId");
		$this->_signer_certificate = $request->arg("certHex");
		$this->_role = $request->arg("role");
		$this->_city = $request->arg("city");
		$this->_state = $request->arg("state");
		$this->_postal_code = $request->arg("postal_code");
		$this->_country = $request->arg("country");
		$this->_signing_profile = $request->arg("signing_profile");
	}

	/**
		@attrib api=1 params=pos
		@param location type=string
			"HTML-FORM-BEGIN" | "HTML-FORM-END" | "HTML-HEAD" | "HTML-BODY"
		@returns string
	**/
	public function get_html_modules($location)
	{
		$key = $this->_get_html_modules_key();
		return $this->_replace_html_module_vars(cache::file_get_pt(self::CACHE_SUBDIR, $key, $location));
	}

	private function _get_lib_modules_subdir()
	{
		return $this->client_platform . "/" . $this->_phase . "/";
	}

	private function _get_html_modules_key()
	{
		return self::CACHE_HTML_SUBDIR_PREFIX . $this->client_platform . "_" . $this->_phase;
	}

	private function _read_client_platform()
	{
		$client = get_browser();

		// get OS
		$platform = strtolower($client->platform);
		if (false !== strpos($platform, "win"))
		{
			$client_platform = "WIN32-";
		}
		elseif (false !== strpos($platform, "linux") or false !== strpos($platform, "unix") or false !== strpos($platform, "bsd"))
		{
			$client_platform = "LINUX-";
		}
		else
		{
			throw new awex_not_implemented(sprintf("Your platform %s is not supported", $client->platform));
		}

		// get agent
		$browser = strtolower($client->browser);
		if ("ie" === $browser or "msie" === $browser)
		{
			$client_platform .= "IE";
		}
		elseif (false !== strpos($browser, "mozilla") or false !== strpos($browser, "firefox") or false !== strpos($browser, "netscape"))
		{
			$client_platform .= "MOZILLA";
		}
		else
		{
			throw new awex_not_implemented(sprintf("Your browser %s is not supported", $client->browser));
		}

		$this->client_platform = $client_platform;
	}

	private function _replace_html_module_vars($html)
	{
		if (!$this->html_module_vars)
		{
			$applets_url = aw_ini_get("digidoc.applets_url") . $this->_get_lib_modules_subdir();
			$this->html_module_vars = array(
				"{0}" => aw_ini_get("digidoc.default_language"),
				"{1}" => $this->_signer_token_id,
				"{2}" => $this->_signer_certificate,
				"{3}" => "",
				"{4}" => "",
				"{5}" => $this->_phase,
				"{6}" => "",
				"driver_errror.jsp" => "?wtf=error",
				"documents.jsp" => "?wtf=documents",
				"Teie brauser ei toeta Java keskkonda!" => t("Teie internetilehitseja ei toeta Java keskkonda!"),
				// for ff
				"SignApplet_sig.jar" => "{$applets_url}SignApplet_sig.jar",
				"iaikPkcs11Wrapper_sig.jar" => "{$applets_url}iaikPkcs11Wrapper_sig.jar",
				// for ie
				"EIDCard.cab" => "{$applets_url}EIDCard.cab"
			);
		}
		return str_replace(array_keys($this->html_module_vars), array_values($this->html_module_vars), $html);
	}
}

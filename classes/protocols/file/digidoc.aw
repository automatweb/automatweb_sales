<?php

/**
 * DigiDoc klass
 *
 * Klass DigiDoc teenuse kasutamiseks. Sisaldab vajalikke meetodeid
 * infovahetuse pidamiseks DigiDoc teenust pakkuva serveriga.
 *
 * @category	SOAP
 * @package		DigiDoc
 * @version		1.0.0
 * @author		Roomet Kirotarp <Roomet.Kirotarp@hot.ee>
 * @since		2004.05.01
 * @access		public
 */
class digidoc
{
	/**
	 * WSDL classi lokaalse faili ja klassi nimi
	 *
	 * Selles hoitakse WSDL-i alusel genereeritud PHP classi,
	 * et ei peaks iga kord seda serverist uuesti p2rima.
	 * Kui WSDL faili aadressi muuta, tuleb ka see fail 2ra kustutada, kuna
	 * selles hoitakse ka serveri aadressi, mis p2rast muutmist enam ei yhti
	 * 6ige aadressiga!
	 */
	const WSDL_CLASS_NAME = "webservice_digidocservice";

	/**
	 * Soap kliendi yhenduse objekt
	 */
	var $Client;

	/**
	 * WSDL faili p6hjal genereeritud liides
	 */
	public $WSDL;

	/**
	 * Brauseri ja OS-i andmed
	 */
	var $browser;

	/** Loads and compiles WSDL connection class
		@attrib api=1 params=pos
		@comment
			* klassi digidoc::WSDL_CLASS_NAME definitsiooni
			* laadimie _enne_ sessiooni alustamist et oleks v6imalik Base_DigiDoc
			* sessiooni salvestada
		@returns void
		@errors
			throws awex_ddoc_wsdl on wsdl errors
	**/
	public function __construct()
	{
		$wsdl_file = aw_ini_get("digidoc.data_dir") . self::WSDL_CLASS_NAME . AW_FILE_EXT;
		if (is_readable($wsdl_file) && filesize($wsdl_file) > 32)
		{
			require_once $wsdl_file;
		}
		else
		{
			$connection = self::getConnect();
			$wsdl = new SOAP_WSDL(aw_ini_get("digidoc.service_uri"), $connection);
			$wcode = $wsdl->generateProxyCode("", self::WSDL_CLASS_NAME);

			if ($wcode instanceof Pear_Error)
			{
				throw new awex_ddoc_wsdl($wcode->getMessage() . ". Connection: ".print_r($connection, true));
			}
			elseif (!class_exists(self::WSDL_CLASS_NAME, false))
			{
				$r = file_put_contents($wsdl_file, "<?php\n{$wcode}\n");
				require_once $wsdl_file;
			}
		}

		$connection = self::getConnect();
		$this->Client = new SOAP_Client(aw_ini_get("digidoc.service_uri"), TRUE, FALSE, $connection);
		$wsdl_class = self::WSDL_CLASS_NAME;

		if (class_exists($wsdl_class, false))
		{
			$this->WSDL = new $wsdl_class();
			$this->browser = ddFile::getBrowser();
			$this->NS = $this->Client->_wsdl->definition['targetNamespace'];
		}
		else
		{
			throw new awex_ddoc_wsdl("Couldn't find WSDL proxy class.");
		}
	}


	/**
	 * Lisab vastava parameetri ja v22rtuse SOAP headerisse
	 *
	 * Parameetri lisamiseks SOAP serverile saadetavatesse XML p2ringuisse.
	 * Antud juhul enamasti sessiooni koodi lisamiseks, et tuvastada 6ige
	 * digidoc failiga tegelemist.
	 *
	 * <code>
	 * $dd->addHeader('SessionCode', '01223121');
	 * </code>
	 * <code>
	 * $x = array('SessionCode' => '123423423234', 'testVar'=>'muutuja');
	 * $dd->addHeader($x);
	 * </code>
	 *
	 * @param     mixed    $var     P2isesse lisatavad parameetrid
	 * @param     mixed    $value   yhe muutuja lisamisel, selle v22rtus
	 * @access    public
	 * @return    array
	 */
	function addHeader($var, $value=null)
	{
		if(is_array($var))
		{
			while(list($key, $val) = each($var))
			{
				$hr = new SOAP_Header($key, NULL, $val, FALSE, FALSE);
				$hr->namespace = $this->NS;
				if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
				if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
				$this->WSDL->addHeader($hr);
			}
			return TRUE;
		}
		elseif($var && $value)
		{
			$hr = new SOAP_Header($var, NULL, $value, FALSE, FALSE);
			$hr->namespace = $this->NS;
			if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
			if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
			$this->WSDL->addHeader($hr);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * yhenduse/proksi parameetrite vektor
	 *
	 * Detail description
	 * @access    public
	 * @return    array
	 */
	private static function getConnect()
	{
		$ret=array();
		if(aw_ini_get("digidoc.proxy.host")) $ret['proxy_host'] = aw_ini_get("digidoc.proxy.host");
		if(aw_ini_get("digidoc.proxy.port")) $ret['proxy_port'] = aw_ini_get("digidoc.proxy.port");
		if(aw_ini_get("digidoc.proxy.user")) $ret['proxy_user'] = aw_ini_get("digidoc.proxy.user");
		if(aw_ini_get("digidoc.proxy.password")) $ret['proxy_pass'] = aw_ini_get("digidoc.proxy.password");
		if(aw_ini_get("digidoc.connect.timeout")) $ret['timeout'] = aw_ini_get("digidoc.connect.timeout");
		return $ret;
	}
}

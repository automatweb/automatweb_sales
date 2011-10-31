<?php

###########################################################################
###########################################################################
###########################################################################
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
class digidoc {

	/**
	 * Soap kliendi ühenduse objekt
	 */
	var $Client;

	/**
	 * WSDL faili põhjal genereeritud liides
	 */
	var $WSDL;

	/**
	 * Brauseri ja OS-i andmed
	 */
	var $browser;


	/*
	 * funktsioon class WebService_DigiDocService_DigiDocService definitsiooni
	 * laadimiseks _enne_ sessiooni alustamist et oleks võimalik Base_DigiDoc
	 * sessiooni salvestada
	 */
	function load_WSDL()
	{
			if(is_readable( DD_WSDL_FILE ) && filesize( DD_WSDL_FILE ) > 32){
				include_once DD_WSDL_FILE;
			} else {
				$wsdl = new SOAP_WSDL( DD_WSDL, $connection );
				$wcode = $wsdl->generateProxyCode();
				if(!class_exists("webservice_digidocservice_digidocservice"))
				{
					eval( $wcode );
				}
				if(PEAR::isError($wcode))
				{
					$ddoc = get_instance(CL_DDOC);
					$ddoc->sign_err(DDOC_ERR_DIGIDOC, "load_WSDL", "Error in recived code.[wsdl:".$wsdl."][conn:".$connection."]", $this->getMessage());
					return false;
				}
				ddFile::saveLocalFile( DD_WSDL_FILE, "<?php\n".$wcode."\n?".">");
			}
	}

	/**
	 * Constructor
	 */
	function digidoc() {
		//session_start();
		$connection = $this->getConnect();
		$this->Client = new SOAP_Client ( DD_WSDL, TRUE, FALSE, $connection);

		if(!class_exists("WebService_DigiDocService_DigiDocService"))
		{
			return false;
		}
		$this->WSDL = new WebService_DigiDocService_DigiDocService();

		$this->browser = ddFile::getBrowser();

		$this->NS = $this->Client->_wsdl->definition['targetNamespace'];
	} //function


	/**
	 * Lisab vastava parameetri ja väärtuse SOAP headerisse
	 *
	 * Parameetri lisamiseks SOAP serverile saadetavatesse XML päringuisse.
	 * Antud juhul enamasti sessiooni koodi lisamiseks, et tuvastada õige
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
	 * @param     mixed    $var     Päisesse lisatavad parameetrid
	 * @param     mixed    $value   ühe muutuja lisamisel, selle väärtus
	 * @access    public
	 * @return    array
	 */
	function addHeader($var, $value=null){
		if(is_array($var)){
			while(list($key, $val) = each($var)){
				$hr = new SOAP_Header($key, NULL, $val, FALSE, FALSE);
				$hr->namespace = $this->NS;
				if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
				if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
				$this->WSDL->addHeader($hr);
			} //while
			return TRUE;
		} elseif($var && $value) {
			$hr = new SOAP_Header($var, NULL, $value, FALSE, FALSE);
			$hr->namespace = $this->NS;
			if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
			if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
			$this->WSDL->addHeader($hr);
		} else {
			return FALSE;
		} //else
	}


	/**
	 * Tagastab vastuvõetud DigiDoci formaadi ja versiooni
	 * @return	array
	 */
	function getDigiDocArray(){
		$us = new XML_Unserializer();
		$us->unserialize($this->WSDL->xml, FALSE);
		$xml = $us->getUnserializedData();
		return $xml;
	} //function

	/**
	 * Puhastab saadetud kuupäeva ülearustest sümbolitest
	 * @access	private
	 */
	function cleanDateString($date){
		return preg_replace("'[TZ]'"," ",$date);
	} //function


	/**
	 * Sertifikaadi salvestamine
	 */
	function saveCertAs($file){
		$filename = uniqid('certificate').'.cer';
		$content = "-----BEGIN CERTIFICATE-----\n".$file."\n-----END CERTIFICATE-----\n";
		ddFile::SaveAs($filename, $content, 'application/certificate', 'utf-8');
	} //function


	/**
	 * Kehtivuskinnituse salvestamine
	 */
	function saveNotaryAs($file){
		$filename = uniqid('ocsp').'.ocsp';
		$content = base64_decode($file);
		ddFile::SaveAs($filename, $content, 'application/notary-ocsp', 'utf-8');
	} //function


	/**
	 * Tagastab ddociga kaasas olnud andmefailid array-na
	 */
	function getDataFiles($result){
		$res = array();
		return $res;
	} //function



	/**
	 * Tagastab ddociga kaasas olnud allkirjad array-na
	 */
	function getSignatures($result){
		$res = array();
		return $res;
	} //function


	/**
	 * Tagastame brauseri ja OS/i info stringina
	 */
	function getBrowserStr(){
		$browser = $this->browser;
		$os = $browser['OS']=='Win'?'WIN32':'LINUX';
		$br = $browser['BROWSER_AGENT'] == 'IE' ? 'IE' : 'MOZILLA';
		return $os.'-'.$br;
	} //function

	/**
	 * ühenduse/proksi parameetrite vektor
	 *
	 * Detail description
	 * @access    public
	 * @return    array
	 */
	function getConnect(){
		$ret=array();
		if(defined('DD_PROXY_HOST') && DD_PROXY_HOST) $ret['proxy_host'] = DD_PROXY_HOST;
		if(defined('DD_PROXY_PORT') && DD_PROXY_PORT) $ret['proxy_port'] = DD_PROXY_PORT;
		if(defined('DD_PROXY_USER') && DD_PROXY_USER) $ret['proxy_user'] = DD_PROXY_USER;
		if(defined('DD_PROXY_PASS') && DD_PROXY_PASS) $ret['proxy_pass'] = DD_PROXY_PASS;
		if(defined('DD_TIMEOUT') && DD_TIMEOUT) $ret['timeout'] = DD_TIMEOUT;
		return $ret;
	} // end func



} //class

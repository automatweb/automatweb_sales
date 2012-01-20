<?php

/**
 * DigiDoc XML faili parser
 *
 * Loeb DigiDoc faili komponendid. Teisendab ta failidega kujult failideta
 * kujule ja vastupidi.
 * @access       public
 * @package      DigiDoc
 * @todo         Lisada k6ik funktsioonid, mis on seotud ddoc konteineriga
 * ja xml-i t88tlusega selles koos failide lisamise eemaldamise
 * funktsioonidega
 */

class ddoc_parser
{

	/**
	 * DigiDoc XML faili hoidja
	 * @var       string
	 * @access    private
	 */
	var $xml;

	/**
	 * Parsitava faili formaat
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $format;


	/**
	 * Parsitava faili versioon
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $version;


	/**
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $xmlarray;

	/**
	 * K&otilde;ik XML failist leitud datafailide tagid.
	 * @var       array
	 * @access    private
	 */
	var $dataFilesXML;

	/**
	 * T&otilde;&otilde;kaust failide hoidmiseks
	 * @var       string
	 * @access    private
	 */
	private $workPath = "";

	private $doc_id;


	/**
	 * Constructor.
	 * @param      string  $xml       Parsitava DDoc faili XML sisu
	 */
	public function __construct($xml='')
	{
		$this->xml = $xml;
		$this->xmlarray = $xml ? $this->Parse($this->xml) : false;
		$this->setDigiDocFormatAndVersion();
		$this->workPath = aw_ini_get("digidoc.data_dir");
		if (!is_dir($this->workPath)) mkdir($this->workpath, 0755, true);
		$this->doc_id = aw_session::get("doc_id");
	}


	/**
	 * Teisendab XML-i array kujule
	 *
	 * @param     string     $xml
	 * @param     string     $XMLPart  Parsida kas 'body' v6i 'header' v6i ''
	 * @access    public
	 * @return    array
	 */
	public function Parse($xml, $XMLPart='')
	{
		$us = new XML_Unserializer();
		$us->unserialize($xml, FALSE);

		$xml2 = $us->getUnserializedData();

		$body = $xml2['SOAP-ENV:Body'];
		$body = current($body);

		if (isset($body['SignedDocInfo']['format'])) $this->format = $body['SignedDocInfo']['format'];
		if (isset($body['SignedDocInfo']['version'])) $this->version = $body['SignedDocInfo']['version'];

		switch(strtolower($XMLPart))
		{
			case 'body':
				$xml2 = $body;
				break;

			case 'header':
				$xml2 = $xml2['SOAP-ENV:Header'];
				break;
		}

		return $xml2;
	}

	/**
	 * M22rab digidoc-i failiformaadi ja versiooni XML p6hjal.
	 *
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	public function setDigiDocFormatAndVersion($xml='')
	{
		if ($xml=='') $xml=$this->xml;

		if ($xml)
 		{
			preg_match("'(\<SignedDoc.*\/SignedDoc\>)'Us", $xml, $match);
			$content = $match[1];
			preg_match("'format=\"(.*)\"'Us", $content, $match);	$this->format = $match[1];
			preg_match("'version=\"(.*)\"'Us", $content, $match);	$this->version = $match[1];
		}
		else
		{
			$this->format = "";
			$this->version = "";
		}
	}


	/**
	 * Short description.
	 *
	 * Detail description
	 * @param     boolean    $withLocalFiles
	 * @access    public
	 * @return    string
	 */
	public function getDigiDoc()
	{
		$files = $this->_getFilesXML($this->xml);
		$nXML = $this->xml;
		$func = aw_ini_get("digidoc.local_files") ? 'file2hash' : 'hash2file';

		while(list(,$file) = each($files))
		{
			$nXML = str_replace($file, $this->$func($file), $nXML);
		}

		return $nXML;
	}


	/**
	 * Teisendab Datafaile tagi filega kujult hash-koodiga kujule.
	 *
	 * Teisendab DigiDoc failist saadud DataFile tagides oleva faili
	 * hash/koodi sisaldavale kujule ja salvestades saadud faili kohalikule
	 * kettale m22ratud kausta.
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	private function file2hash($xml)
	{
		if (preg_match("'ContentType\=\"HASHCODE\"'s", $xml))
		{ // Meil on hashcode kuju
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);
			$Id = $match[1];
			preg_match("'DigestValue=\"(.*)\"'Us", $xml, $match);
			$oldHash = $match[1];
			$tempfiledata = file_get_contents($this->workPath.$this->doc_id.'_'.$Id);
			$newHash=base64_encode(pack("H*", sha1(str_replace("\r\n","\n",$tempfiledata) ) ) );
			$xml=str_replace($oldHash, $newHash, $xml);
			return $xml;
		}
		else
		{
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);
			$Id = $match[1]; // Saame teada faili identifikaatori
			$file_path = $this->workPath.iconv("UTF-8", "US-ASCII", "{$this->doc_id}_{$Id}");
			file_put_contents($file_path, $xml); // salvestame algfaili
			$hash = base64_encode(pack("H*", sha1(str_replace("\r\n","\n",$xml) ) ) ); // Arvutame andmefaili bloki r2si

			$hashonlyxml = preg_replace('/>((.|\n|\r)*)<\//', ' DigestValue="'.$hash.'"></', $xml); // Moodustame serverisse saadetava andmefaili bloki eemaldades andmefaili sisu
			$hashonlyxml = str_replace('ContentType="EMBEDDED_BASE64"', 'ContentType="HASHCODE"', $hashonlyxml);

			$hashonlyxml=$xml; // Urmo ajutiselt niikauaks kui teenus verifitseerimisel DigestValue v22rtust korralikult ei kontrolli
			return $hashonlyxml;
		}
	}


	/**
	 * Asendab Datafile tagides hash-koodid vastavate failidega
	 *
	 * Asendab antud XML-s hash-koodiga XML-i faili sisaldavaks XML tagiks
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	private function hash2file($xml)
	{
		if( preg_match("'ContentType\=\"HASHCODE\"'s", $xml) )
		{
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);
			$Id = $match[1];
			$nXML = file_get_contents($this->workPath.iconv("UTF-8", "US-ASCII", "{$this->doc_id}_{$Id}"));
			return $nXML;
		}
		else
		{
			return $xml;
		}
	}

	/**
	 * Tagastab faili kohta HASH koodi.
	 *
	 * Genereerib failile vajaliku XML tagi ja leiab selle HASH-koodi.
	 * Saadud faili XML salvestatakse vastavasse sessioonikausta.
	 * @param     array      $file          Yleslaetud faili array
	 * @param     string     $Id            Faili ID DigiDoc-s
	 * @access    public
	 * @return    array
	 */
	public function getFileHash($file, $Id = 'D0')
	{
		$xml = sprintf($this->getXMLtemplate('file'), $file['name'], $Id, $file['MIME'], $file['size'], chunk_split(base64_encode($file['content']), 64, "\n") );
		$sh = base64_encode(pack("H*", sha1( str_replace("\r\n","\n",$xml))));
		$file_path = $this->workPath.iconv("UTF-8", "US-ASCII", "{$this->doc_id}_{$Id}");
		file_put_contents($file_path, $xml);

		$ret['Filename'] = $file['name'];
		$ret['MimeType'] = $file['MIME'];
		$ret['ContentType'] = 'HASHCODE';
		$ret['Size'] = $file['size'];
		$ret['DigestType'] = 'sha1';
		$ret['DigestValue'] = $sh;
		return $ret;
	}

	/**
	 * Tagastab k6ik andmefaili konteinerid antud XML failist.
	 *
	 * @param     string      $xml          Parsitav XML
	 * @access    private
	 * @return    array
	 */
	private function _getFilesXML($xml)
	{
		$x = array();
		$a = $b = -1;

		while(($a=strpos($xml, '<DataFile', $a+1))!==FALSE && ($b=strpos($xml, '/DataFile>', $b+1))!==FALSE)
		{
			$x[] = preg_replace("'/DataFile>.*$'s", "/DataFile>", substr($xml, $a, $b));
		}

		if(!count($x))
		{
			$a = $b = -1;
			while(($a=strpos($xml, '<DataFileInfo', $a+1))!==FALSE && ($b=strpos($xml, '/DataFileInfo>', $b+1))!==FALSE)
			{
				$x[] = preg_replace("'/DataFileInfo>.*$'s", "/DataFileInfo>", substr($xml, $a, $b));
			}
		}
		return $x;
	}

	/**
	 * XML templiidid erinevatele p2ringutele
	 *
	 * @param     string     $type          P2ritava XML-templiidi tüüp
	 * @access    private
	 * @return    string
	 */
	private function getXMLtemplate($type)
	{
		switch($type)
		{
			case 'file':
				return '<DataFile'.($this->version == '1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="EMBEDDED_BASE64" Filename="%s" Id="%s" MimeType="%s" Size="%s"'.($this->format == 'SK-XML'?' DigestType="sha1" DigestValue="%s"':'').'>%s</DataFile>';

			case 'filesha1':
				return '<DataFile'.($this->version=='1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="HASHCODE" Filename="%s" Id="%s" MimeType="%s" Size="%s" DigestType="sha1" DigestValue="%s"></DataFile>';
	    }
	}
}



###########################################################################
###########################################################################
###########################################################################

/**
 * Failide funktsioonid
 *
 * Klass sisaldab k6iki failidega seotud funktsioone, nagu yleslaadimine,
 * salvestamine, nimede genereerimine, kaustade loomine.
 *
 */
class ddFile
{
	/**
	 * Leiab kasutaja brauseri ja Op.systeemi
	 *
	 * Tagastab vektorina info kasutaja Op.systeemi ja brauseri kohta
	 * - OS             : Operatsioonisysteem (Win,Mac,Linux,Unix,OS/2,Other)
	 * - BROWSER_AGENT  : Kasutatav brauser
	 * - BROWSER_VER    : Brauseri versioon
	 * @access    public
	 * @return    array
	 */
	public static function getBrowser()
	{
		if (!empty($_SERVER['HTTP_USER_AGENT']))
		{
			$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		}
		elseif (!isset($HTTP_USER_AGENT))
		{
			$HTTP_USER_AGENT = '';
		}
		$res=array();

		// 1. Platform
		if (strstr($HTTP_USER_AGENT, 'Win'))
		{
			$res['OS'] = 'Win';
		}
		else if (strstr($HTTP_USER_AGENT, 'Mac'))
		{
			$res['OS'] = 'Mac';
		}
		else if (strstr($HTTP_USER_AGENT, 'Linux'))
		{
			$res['OS'] = 'Linux';
		}
		else if (strstr($HTTP_USER_AGENT, 'Unix'))
		{
			$res['OS'] = 'Unix';
		}
		else if (strstr($HTTP_USER_AGENT, 'OS/2'))
		{
			$res['OS'] = 'OS/2';
		}
		else
		{
			$res['OS'] = 'Other';
		}

		// 2. browser and version
		if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version))
		{
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'OPERA';
		}
		else if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'IE';
		}
		else if (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'OMNIWEB';
		//} else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
		// Konqueror 2.2.2 says Konqueror/2.2.2
		// Konqueror 3.0.3 says Konqueror/3
		}
		else if (preg_match('@(Konqueror/)(.*)(;)@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'KONQUEROR';
		}
		else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)
				   && preg_match('@Safari/([0-9]*)@', $HTTP_USER_AGENT, $log_version2)) {
			$res['BROWSER_VER'] = $log_version[1] . '.' . $log_version2[1];
			$res['BROWSER_AGENT'] = 'SAFARI';
		}
		else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'MOZILLA';
		}
		else
		{
			$res['BROWSER_VER'] = 0;
			$res['BROWSER_AGENT'] = 'OTHER';
		}
		return $res;
	}
}

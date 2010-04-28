<?php

namespace automatweb;
/*
@classinfo  maintainer=tarvo
*/
###########################################################################
###########################################################################
###########################################################################
/**
 * DigiDoc XML faili parser
 *
 * Loeb DigiDoc faili komponendid. Teisendab ta failidega kujult failideta
 * kujule ja vastupidi.
 * @access       public
 * @package      DigiDoc
 * @todo         Lisada kõik funktsioonid, mis on seotud ddoc konteineriga
 * ja xml-i töötlusega selles koos failide lisamise eemaldamise 
 * funktsioonidega
 */

//class Parser_DigiDoc{
class ddoc2_parser{

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
	 * Kõik XML failist leitud datafailide tagid.
	 * @var       array
	 * @access    private
	 */
	var $dataFilesXML;
	
	/**
	 * Töökaust failide hoidmiseks
	 * @var       string
	 * @access    private
	 */
	var $_workPath;
	

	/**
	 * Constructor.
	 * @param      string  $xml       Parsitava DDoc faili XML sisu
	 */
	//function Parser_DigiDoc($xml=''){
	function ddoc2_parser($xml=''){
		session_start();

		$this->xml = $xml;
		$this->xmlarray = $xml?$this->Parse($this->xml):false;
		$this->setDigiDocFormatAndVersion();
		$this->workPath = aw_ini_get("basedir").'/classes/common/digidoc/data/';//DD_FILES;//.session_id().'/';
		if (!is_dir($this->workPath))
			if(ddFile::DirMake($this->workPath) != DIR_ERR_OK)
				die('Error accessing workpath:'.$this->workPath);
	} // end func

	
	/**
	 * Teisendab XML-i array kujule
	 *
	 * @param     string     $xml
	 * @param     string     $XMLPart  Parsida kas 'body' või 'header' või ''
	 * @access    public
	 * @return    array
	 */
	function Parse($xml, $XMLPart=''){
		
		$us = new XML_Unserializer();
		$us->unserialize($xml, FALSE);

		$xml2 = $us->getUnserializedData();


		$body = $xml2['SOAP-ENV:Body'];

		$body = @current($body);

		if (isset($body['SignedDocInfo']['format']))
			$this->format = $body['SignedDocInfo']['format'];

		if (isset($body['SignedDocInfo']['version']))
			$this->version = $body['SignedDocInfo']['version'];

		switch(strtolower($XMLPart)){
			case 'body':
				$xml2 = $body;
				break;
			case 'header':
				$xml2 = $xml2['SOAP-ENV:Header'];
				#$xml = current($xml);
				break;
		} //switch
		
		return $xml2;
	
	} // end func

	/**
	 * tagastab ddoc-is olevad andmefailid.
	 *
	 * Tagastab kõik digidoc failis olevad andmefailid arrayna.
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function getFilesInfo($xml){
		$fs = $this->_getFilesXML($xml);

		$us = new XML_Unserializer();

		$ret = array();
		foreach($fs as $key=>$val){
			$us->unserialize($val, FALSE);
			$ret[] = $us->getUnserializedData();
		} //foreach
		return $ret;
	} // end func


	/**
	 * Määrab digidoc-i failiformaadi ja versiooni XML põhjal.
	 *
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function setDigiDocFormatAndVersion($xml='') {
		if ($xml=='')
			$xml=$this->xml;
		if ($xml) {
			preg_match("'(\<SignedDoc.*\/SignedDoc\>)'Us", $xml, $match); 
			$content = $match[1];
			preg_match("'format=\"(.*)\"'Us", $content, $match);	$this->format = $match[1];
			preg_match("'version=\"(.*)\"'Us", $content, $match);	$this->version = $match[1];
		} else {
			$this->format = "";
			$this->version = "";
		}
	}
	

	/**
	 * Tagastab digidoc-s sisalduvad allkirjad
	 *
	 * Tagastab digidoc-s olevad allkirjad arrayna.
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function getSignaturesInfo( $xml ){
		$fs = $this->_getSignsXML( $xml );
		$us = new XML_Unserializer();
		$ret = array();
		foreach($fs as $key=>$val){
			$us->unserialize($val, FALSE);
			$ret[] = $us->getUnserializedData();
		} //foreach
		return $ret;
	} // end func

	
	
	/**
	 * Short description.
	 *
	 * Detail description
	 * @param     boolean    $withLocalFiles
	 * @access    public
	 * @return    string
	 */
	function getDigiDoc( $withLocalFiles = FALSE ){

		$files = $this->_getFilesXML($this->xml);
		$nXML = $this->xml;
		$func = $withLocalFiles ? 'file2hash' : 'hash2file';
	
		while(list(,$file) = each($files)){
			$nXML = str_replace($file, $this->$func($file), $nXML);
		} //while
		#echo '<hr><pre>'.htmlentities($nXML).'</pre><hr>';
		return $nXML;
	} // end func

	
	/**
	 * Teisendab Datafaile tagi filega kujult hash-koodiga kujule.
	 *
	 * Teisendab DigiDoc failist saadud DataFile tagides oleva faili
	 * hash/koodi sisaldavale kujule ja salvestades saadud faili kohalikule
	 * kettale määratud kausta.
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	function file2hash($xml){
		if(preg_match("'ContentType\=\"HASHCODE\"'s",$xml)){ // Meil on hashcode kuju
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);
			$Id = $match[1];
			preg_match("'DigestValue=\"(.*)\"'Us", $xml, $match);
			$oldHash=$match[1];
			$tempfiledata=file_get_contents($this->workPath.$_SESSION['doc_id'].'_'.$Id);
			$newHash=base64_encode(pack("H*", sha1(str_replace("\r\n","\n",$tempfiledata) ) ) );
			$xml=str_replace($oldHash, $newHash, $xml);
			return $xml;
		} else {
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);	$Id = $match[1]; // Saame teada faili identifikaatori
			ddFile::SaveLocalFile( $this->workPath.$_SESSION['doc_id'].'_'.$Id, $xml); // salvestame algfaili
			$hash = base64_encode(pack("H*", sha1(str_replace("\r\n","\n",$xml) ) ) ); // Arvutame andmefaili bloki räsi

			$hashonlyxml = preg_replace('/>((.|\n|\r)*)<\//', ' DigestValue="'.$hash.'"></', $xml); // Moodustame serverisse saadetava andmefaili bloki eemaldades andmefaili sisu
			$hashonlyxml = str_replace('ContentType="EMBEDDED_BASE64"', 'ContentType="HASHCODE"', $hashonlyxml);

			$hashonlyxml=$xml; // Urmo ajutiselt niikauaks kui teenus verifitseerimisel DigestValue väärtust korralikult ei kontrolli
			return $hashonlyxml;
		} //else
	} // end func

	
	/**
	 * Asendab Datafile tagides hash-koodid vastavate failidega
	 *
	 * Asendab antud XML-s hash-koodiga XML-i faili sisaldavaks XML tagiks
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	function hash2file($xml){
		if( preg_match("'ContentType\=\"HASHCODE\"'s", $xml) ){
			 preg_match("'Id=\"(.*)\"'Us", $xml, $match);		$Id = $match[1];
			 $nXML = ddFile::readLocalFile($this->workPath.$_SESSION['doc_id'].'_'.$Id);			 
			return $nXML;
		} else {
			return $xml;
		} //else
	} // end func
	
	
	
	/**
	 * Tagastab faili kohta HASH koodi.
	 *
	 * Genereerib failile vajaliku XML tagi ja leiab selle HASH-koodi. 
	 * Saadud faili XML salvestatakse vastavasse sessioonikausta.
	 * @param     array      $file          üleslaetud faili array
	 * @param     string     $Id            Faili ID DigiDoc-s
	 * @access    public
	 * @return    array
	 */
	function getFileHash($file, $Id='D0'){
		$xml = sprintf($this->getXMLtemplate('file'), $file['name'], $Id, $file['MIME'], $file['size'], chunk_split(base64_encode($file['content']), 64, "\n") );
		$sh = base64_encode(pack("H*", sha1( str_replace("\r\n","\n",$xml))));
		ddFile::SaveLocalFile($this->workPath.$_SESSION['doc_id'].'_'.$Id, $xml);
		//File::SaveLocalFile($this->workPath.$_SESSION['doc_id'].'_'."test1.xml", $xml);
		$ret['Filename'] = $file['name'];
		$ret['MimeType'] = $file['MIME'];
		$ret['ContentType'] = 'HASHCODE';
		$ret['Size'] = $file['size'];
		$ret['DigestType'] = 'sha1';
		$ret['DigestValue'] = $sh;
		return $ret;
	} // end func
	
	/**
	 * Tagastab kõik andmefaili konteinerid antud XML failist.
	 *
	 * @param     string      $xml          Parsitav XML
	 * @access    private
	 * @return    array
	 */
	function _getFilesXML($xml){

		$x = array();
		$a = $b = -1;

		while(($a=strpos(&$xml, '<DataFile', $a+1))!==FALSE && ($b=strpos(&$xml, '/DataFile>', $b+1))!==FALSE){
			$x[] = preg_replace("'/DataFile>.*$'s", "/DataFile>", substr($xml, $a, $b));
		} //while

		if(!count($x)){
			$a = $b = -1;
			while(($a=strpos(&$xml, '<DataFileInfo', $a+1))!==FALSE && ($b=strpos(&$xml, '/DataFileInfo>', $b+1))!==FALSE){
				$x[] = preg_replace("'/DataFileInfo>.*$'s", "/DataFileInfo>", substr($xml, $a, $b));
			} //while
		}
		return $x;
	} // end func


	/**
	 * Tagastab kõik signatuuride konteinerid antud XML failist.
	 *
	 * @param     string      $xml          Parsitav XML
	 * @access    private
	 * @return    array
	 */
	function _getSignsXML($xml){
		if( preg_match_all("'(\<Signature.*\/Signature\>)'Us", $xml, $ret) ){
			return $ret[1];
		} elseif( preg_match_all("'(\<SignatureInfo.*\/SignatureInfo\>)'Us", $xml, $ret) ) {
			return $ret[1];
		} else {
			return array();
		} //else
	} // end func


	/**
	 * XML templiidid erinevatele päringutele
	 *
	 * @param     string     $type          Päritava XML-templiidi tüüp
	 * @access    private
	 * @return    string
	 */
	function getXMLtemplate($type){
		
		switch($type){
		case 'file':
				#File::VarDump('VER:'.$_SESSION['ddoc_version']);
			return '<DataFile'.($this->version == '1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="EMBEDDED_BASE64" Filename="%s" Id="%s" MimeType="%s" Size="%s"'.($this->format == 'SK-XML'?' DigestType="sha1" DigestValue="%s"':'').'>%s</DataFile>';
	    		break;
	    	case 'filesha1':
				#File::VarDump($_SESSION['ddoc_version']);
	    		return '<DataFile'.($this->version=='1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="HASHCODE" Filename="%s" Id="%s" MimeType="%s" Size="%s" DigestType="sha1" DigestValue="%s"></DataFile>';
	    		break;
	    	default:
	    		
	    } //switch
	} // end func


} // end class



###########################################################################
###########################################################################
###########################################################################
/**
 * File::DirMake Status: OK
 */
DEFINE ("DIR_ERR_OK",0);

/**
 * File::DirMake Status:	Path exists but not as directory
 */
DEFINE ("DIR_ERR_NOTDIR",1);

/**
 * File::DirMake Status:	Syntax error in path
 */
DEFINE ("DIR_ERR_SYNTAX",2);

/**
 * File::DirMake Status:	"mkdir" error with no parent
 */
DEFINE ("DIR_ERR_EMKDIR_1",3);

/**
 * File::DirMake Status:	"mkdir" error and parent exists
 */
DEFINE ("DIR_ERR_EMKDIR_2",4);

/**
 * File::DirMake Status:	"mkdir" error after creating parent
 */
DEFINE ("DIR_ERR_EMKDIR_3",5);

/**
 * Failide funktsioonid
 *
 * Klass sisaldab kõiki failidega seotud funktsioone, nagu üleslaadimine, 
 * salvestamine, nimede genereerimine, kaustade loomine.
 *
 * @package      DigiDoc
 */
class ddFile{
	
		
	/**
	 * constructor
	 */
	function ddFile(){
	    return true;
	} // end func
	
	/**
	 * Kaustade/alamkaustade loomiseks
	 *
	 * Loob kausta etteantud kohta, vajadusel ka kogu kaustapuu, kui 
	 * on õigused olemas selleks!
	 * @param     string	$strPath	Kausta nimi
	 * @access    public
	 * @return    integer	Tegevuse staatus
	 */
	function DirMake($strPath){
			   // If path exists nothing else can be done
		if ( file_exists($strPath) )
		   return is_dir($strPath) ? DIR_ERR_OK : DIR_ERR_NOTDIR;
			   // Backwards references are not allowed
		if (ereg("\.\.",$strPath) != 0) return DIR_ERR_SYNTAX;
			   // If it can create the directory that's all. If not then either path
			   // contains several dirs or error such as "permission denied" happened
		if (@mkdir($strPath)) return DIR_ERR_OK;
			   // Gets the parent path. If none then there was a severe error
		$nPos = strrpos($strPath,"/");
		if (!($nPos > 0)) return DIR_ERR_EMKDIR_1;
		$strParent = substr($strPath,0,$nPos);
			   // If parent exists then there was a severe error
		if (file_exists($strParent)) return DIR_ERR_EMKDIR_2;
			   // If it can make the parent
		$nRet = ddFile::DirMake($strParent);
		if ($nRet == DIR_ERR_OK)
		   return mkdir($strPath) ? DIR_ERR_OK : DIR_ERR_EMKDIR_3;
		return $nRet;
	}

	
	/**
	 * Saadab antud faili brauserisse salvestamiseks
	 *
	 * Saadab etteantud faili brauserile salvestamiseks. Sunnib alati 
	 * brauserit avama salvestamise akent, sõltumata saadetava faili
	 * MIME-tüübist.
	 * @param     string      $name      Salvestatava faili nimi
	 * @param     mixed       $content   Salvestatava faili sisu
	 * @param     string      $MIME      Salvestatava faili MIME tüüp
	 * @param     string      $charset   Kasutatav koodileht. Vaikimis Western
	 * @access    public
	 * @return    boolean
	 */
	function saveAs($name, $content, $MIME = 'text/plain', $charset = ''){
		ob_clean();
		$browser = ddFile::getBrowser();
		if ($browser['BROWSER_AGENT'] == 'IE') {		
			$susisevad = array("š","ž","Š","Ž");
			$eisusise = array("sh","zh","Sh","Zh");
			$name = str_replace($susisevad, $eisusise,$name);
			//$name = mb_convert_encoding($name, 'ISO-8859-1','UTF-8');
			$name = iconv("ISO-8859-1", "UTF-8", $name);
		}

		if($charset){
			header( 'Content-Type:' . $MIME . '; charset='.$charset );
		} else {
			header( 'Content-Type:' . $MIME );
		} //else
		header( 'Expires:' . gmdate('D, d M Y H:i:s') . ' GMT' ); #Alati aegunud, et ei loetaks cache-st
		$browser = ddFile::getBrowser();
#		File::VarDump($browser);
		// IE need specific headers
		if ($browser['BROWSER_AGENT'] == 'IE') {		
			header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
			header('Pragma:public');
		} else {

			header('Pragma:no-cache');
		}
			header('Content-Disposition:attachment; filename="'.$name.'"');
			Header("Content-Disposition-type: attachment"); 
		    Header("Content-Transfer-Encoding: binary");
//		echo utf8_decode( $content );
		echo $content;
		exit;

	} // end func

	
	/**
	 * Leiab kasutaja brauseri ja Op.sļæ½steemi
	 *
	 * Tagastab vektorina info kasutaja Op.süsteemi ja brauseri kohta
	 * - OS             : Operatsiooni süsteem (Win,Mac,Linux,Unix,OS/2,Other)
	 * - BROWSER_AGENT  : Kasutatav brauser
	 * - BROWSER_VER    : Brauseri versioon
	 * @access    public
	 * @return    array
	 */
	function getBrowser(){
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		} else if (!isset($HTTP_USER_AGENT)) {
			$HTTP_USER_AGENT = '';
		}
		$res=array();

		// 1. Platform
		if (strstr($HTTP_USER_AGENT, 'Win')) {
			$res['OS'] = 'Win';
		} else if (strstr($HTTP_USER_AGENT, 'Mac')) {
			$res['OS'] = 'Mac';
		} else if (strstr($HTTP_USER_AGENT, 'Linux')) {
			$res['OS'] = 'Linux';
		} else if (strstr($HTTP_USER_AGENT, 'Unix')) {
			$res['OS'] = 'Unix';
		} else if (strstr($HTTP_USER_AGENT, 'OS/2')) {
			$res['OS'] = 'OS/2';
		} else {
			$res['OS'] = 'Other';
		}

		// 2. browser and version
		if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'OPERA';
		} else if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'IE';
		} else if (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'OMNIWEB';
		//} else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
		// Konqueror 2.2.2 says Konqueror/2.2.2
		// Konqueror 3.0.3 says Konqueror/3
		} else if (preg_match('@(Konqueror/)(.*)(;)@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'KONQUEROR';
		} else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)
				   && preg_match('@Safari/([0-9]*)@', $HTTP_USER_AGENT, $log_version2)) {
			$res['BROWSER_VER'] = $log_version[1] . '.' . $log_version2[1];
			$res['BROWSER_AGENT'] = 'SAFARI';
		} else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'MOZILLA';
		} else {
			$res['BROWSER_VER'] = 0;
			$res['BROWSER_AGENT'] = 'OTHER';
		}
		return $res;
	} //function

	
	
	/**
	 * ajutise faili nimi
	 *
	 * tagastab genereeritud ajutise faili nime.
	 * @param     string     $ext      Faili laiend
	 * @access    public
	 * @return    string               Faili nimi
	 */
	function tempFile(){
	    return date('Ymd_His').'$'.substr('000'.rand(0,999), -3).'.'.$ext;
	} // end func

	
	/**
	 * loeb kohalikult ketalt faili sisu
	 *
	 * Loeb kohalikus arvutis oleva faili sisu ja tagastab selle.
	 * @param     string     $name     Faili nimi, mida lugeda
	 * @access    public
	 * @return    mixed
	 */
	function readLocalFile($name){
		$name = ddFile::FixEstFileName($name);
		if(is_readable($name)){
			$content = file_get_contents($name);
			return $content;
		} else {
			return FALSE;
		} //else
	} // end func

	
	/**
	 * Salvestab lokaalseks failiks
	 *
	 * Salvestab antud sisu antud nimega faili, kui ei õnnestu 
	 * tagastatakse FALSE.
	 * @param     string     $name     Failinimi
	 * @param     string     $content  Faili sisu
	 * @access    public
	 * @return    mixed
	 */
	function saveLocalFile($name, $content){
		$name = ddFile::FixEstFileName($name);
		if(touch($name)){
			$fh = fopen($name, 'wb');
			fwrite($fh, $content);
			fclose($fh);
			return TRUE;
		} else {
			return FALSE;
		} //else
	} // end func

	
	/**
	 * Tagastab etteantud nimega väljalt üleslaetud faili
	 *
	 * Tagastab faili, mis saadeti parameetris näidatud nimega formi 
	 * väljalt.
	 * @param     string     $name     Formi välja nimi, millega fail saadeti
	 * @access    public
	 * @return    array
	 */
	function getUploadedFile($name){
		if(isset($_FILES[$name])){
			$ret = array();
			$ret['type'] = $name;

			if (!is_dir(DD_UPLOAD_DIR))
				ddFile::DirMake(DD_UPLOAD_DIR);

//				if(File::DirMake(DD_UPLOAD_DIR) != DIR_ERR_OK)

			if( move_uploaded_file($_FILES[$name]['tmp_name'], DD_UPLOAD_DIR.$_FILES[$name]['name']) ){
					$ret['name'] = $_FILES[$name]['name'];
					$ret['size'] = $_FILES[$name]['size'];
					$ret['MIME'] = $_FILES[$name]['type']!=""?$_FILES[$name]['type']:" ";
					$ret['error'] = $_FILES[$name]['error'];
					$ret['content'] = ddFile::readLocalFile( DD_UPLOAD_DIR.$_FILES[$name]['name'] );
					unlink(DD_UPLOAD_DIR.$_FILES[$name]['name']);
			} else {
				$ret['error'] = '999: Cannot move uploaded file !!!';
			} //else
			return $ret;
		} else {
			return FALSE;
		} //else
	    
	} // end func

	
	/**
	 * Short description.
	 *
	 * Detail description
	 * @param     
	 * @since     1.0
	 * @access    private
	 * @return    void
	 * @throws    
	 */
	function FixEstFileName($name){
		//ļæ½ļæ½ļæ½ļæ½ ļæ½ļæ½ļæ½ļæ½

		$nameX = $name;
		#preg_match("'(.*)([^/\\\\]*)(\.\w+)$'U", $name, $match);
		#$nameX = $match[1].base64_encode($match[2]).$match[3];

		$name = preg_replace("'[^a-z0-9]'", "X", utf8_decode($name));
	    /*$name = str_replace('ļæ½','#otilde;', $name);
	    $name = str_replace('ļæ½','#auml;', $name);
	    $name = str_replace('ļæ½','#ouml;', $name);
	    $name = str_replace('ļæ½','#uuml;', $name);
	    $name = str_replace('ļæ½','#zacut;', $name);
	    $name = str_replace('ļæ½','#sacut;', $name);

	    $name = str_replace('ļæ½','#Otilde;', $name);
	    $name = str_replace('ļæ½','#Auml;', $name);
	    $name = str_replace('ļæ½','#Ouml;', $name);
	    $name = str_replace('ļæ½','#Uuml;', $name);
	    $name = str_replace('ļæ½','#Zacut;', $name);
	    $name = str_replace('ļæ½','#Sacut;', $name);
		if($name==$nameX){
			$name = utf8_decode($name);
			$name = str_replace('ļæ½','#otilde;', $name);
			$name = str_replace('ļæ½','#auml;', $name);
			$name = str_replace('ļæ½','#ouml;', $name);
			$name = str_replace('ļæ½','#uuml;', $name);

			$name = str_replace('ļæ½','#Otilde;', $name);
			$name = str_replace('ļæ½','#Auml;', $name);
			$name = str_replace('ļæ½','#Ouml;', $name);
			$name = str_replace('ļæ½','#Uuml;', $name);
		} 
		*/
		return $nameX;
	} // end func

	/**
	 * Abifunktsioon debug-info väljastamiseks
	 * @param      mixed    $var     Muutuja mille väärtus väljastatakse
	 * @access     public
	 */
	function VarDump( $var ){
		#echo '<pre>';print_r($var);echo '</pre>';
		echo '
<pre>
=================================================================
';
		print_r($var);
		echo '
=================================================================
</pre>
';
	} //function


} // end class
?>

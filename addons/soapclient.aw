<?php
if (!defined("C_SOAPCLIENT") && !class_exists("C_SoapClient", false))
{
define("C_SOAPCLIENT", 1);
class C_SoapClient {
  var $host = "";
  var $port = "";
  var $path = "";
  var $namespace = "";
  var $debug = 0;
  var $paramId = 0;
  var $paramTypes = array();
  var $connRef;
  var $simpleEnvelope = true;
  
  function C_SoapClient($serviceURL="",$namespace="")
  {
	$this->namespace = $namespace;
	if ($serviceURL!="")
	{
	  	$urlData = parse_url($serviceURL);
		$this->port = ($urlData["port"]=="") ? "80" : $urlData["port"];
		$this->host = $urlData["host"];
		$this->path = $urlData["path"];
	}
  }

  function call($Action="",$parameters=array(),$paramTypes=array()) {
    global $_SITE;

    $host = $this->host;
    $port = $this->port;
    $soap_url = $this->path;
    if (substr($this->namespace,-1)!="/") $Action = "/".$Action;

    $soap_action = $this->namespace.$Action;
    $soap_parameter = $parameters;

    $paramTypes["selectedPrices"] = "types:SelectedPrice";
    $paramTypes["quantities"] = "types:SelectedCouponItem";


    $this->paramTypes = $paramTypes;

    $result = $this->soap_operation($host, $port, $soap_url, $soap_action, $soap_parameter);

	$parser = new C_XMLDoc("<document>".$result."</document>");
    $parser->parse();

    $res = $this->xml_doc_to_array($parser->document);

    $parser->destroy_parser();
    $parser = null;


    if (is_array($res["soap:Envelope"])) {
      $res = $res["soap:Envelope"];
    }

    if (is_array($res["soap:Body"])) {
      $res = $res["soap:Body"];
    }

    if (is_array($res["q1:".$Action."Response"])) {
      $res = $res["q1:".$Action."Response"];
    } else {
        $rkeys = array_keys($res);
        if (substr($rkeys[0],-8)=="Response")
        {
      		$res = $res[$rkeys[0]];
      	}
    }


    if (is_array($res["diffgr:diffgram"])) {
      $return = $res["diffgr:diffgram"];
    } else {
      $return = $res;
    }

    return $return;
  }


	function xml_doc_to_array($doc) {
	    $r = array();

		if ($doc->hasChildren())
		{

			$childrenCount = count($doc->children);

			$listMultiple = array();
			$lastName = "";

			$i=0;

			while ($i < $childrenCount)
			{
				$child = $doc->children[$i];
                $lastName = $child->name;

				$val = $this->xml_doc_to_array($child);

				if ($doc->type=="soapenc:Array")
				{
					$r[$child->name][] = $val;
				}
				else if (isset($r[$child->name]))
				{
					if (!isset($listMultiple[$child->name]))
					{
						$oldVal = $r[$child->name];
						$r[$child->name] = array();
						$r[$child->name][0] = $oldVal;
						$listMultiple[$child->name] = 1;
					}
					$r[$child->name][] = $val;
				}
				else
				{
					$r[$child->name] = $val;
				}

				$i++;
			}

			if ($doc->type=="soapenc:Array")
			{
				$r = $r[$lastName];
			}
		}
		else
		{
			$r = $doc->contents;
		}
		return $r;
	}


	function soap_operation($host, $port, $url, $action, $vars) {

	  // define eol that is used by the server
	  // windows webservices seem to need a \r\n
	  $eol = "\r\n";

	  // extract namespace from soap action
	  preg_match('/(https?:\/\/.*\/)(.*)/', $action, $matches) or die("Invalid SOAPAction: '$action'");
	  $soap_ns = $matches[1];
	  $soap_action = $matches[2];

		if (substr($soap_ns, -1)=="/") $soap_ns = substr($soap_ns,0,(strlen($soap_ns)-1));
		// create soap envelope
		if ($this->simpleEnvelope)
		{
			$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".$eol.
			"<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">".$eol.
			"  <soap:Body>".$eol.
			"    <".$soap_action." xmlns=\"".$soap_ns.$this->ns_end."\">".$eol;
		}
		else
		{
			$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".$eol.
			"<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenc=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:types=\"http://markus.ee/MCS/2.0/Web20/WebTicketing/encodedTypes\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">".$eol.
			"  <soap:Body>".$eol.
			"    <q1:".$soap_action." xmlns:q1=\"".$soap_ns."/".$soap_action."Request\">".$eol;
		}
	    $ids = 0;
	    $ADD = "";

	    $params = $this->serialize_parameters($vars);

	    $content .= $params["main"];
	    $ADD = $params["add"];

		if ($this->simpleEnvelope)
		{
//		die("spl");
			$content .=
			"    </".$soap_action.">".$eol.$ADD.
			"  </soap:Body>".$eol.
			"</soap:Envelope>";
		}
		else
		{
			$content .=
			"    </q1:".$soap_action.">".$eol.$ADD.
			"  </soap:Body>".$eol.
			"</soap:Envelope>";
		}

	    $content_length = strlen($content);

	  // create soap header
	  // the connection should be closed right after the response
	  // since HTTP 1.1 connections are Keep-Alive per default
	  // we set "Connection: close"
	    $headers =
	    "POST ".$url." HTTP/1.1".$eol.
	    "Host: ".str_replace("ssl://","",$host)."".$eol.
	    "Connection: close".$eol.
	    "Content-Type: text/xml; charset=UTF-8".$eol.
	    "Content-Length: ".$content_length."".$eol.
	    "SOAPAction: \"".$soap_ns."/".$soap_action."\"".$eol.$eol;

		if ($this->debug==1) {
			print_r($headers);
			print_r($content);
		}

		$data = $this->http_get($headers, $content, $host, $port);
		if ($this->debug==1) {
			print("<hr>");
			print_r($data);
		}

		if (strstr( $data, "&lt;?xml version=&quot;") )
		{
			$data = html_entity_decode( $data, ENT_QUOTES, "UTF-8" );
		}


		// extract the soap result
		if (!stristr($data, $soap_action."Response"))
		{
			if (!preg_match('/<faultstring>(.*)<\/faultstring>/ims', $data, $matches)) {
			    if ($data!="") $ERROR = $data;
				else $ERROR = "Empty Response";
			} else {
				$ERROR = $matches[1];
			}
			return '<'.$soap_action.'>'.$ERROR.'</'.$soap_action.'>';
		} else {
			$data = substr($data, strpos($data, "<soap:Envelope"), strlen($data));//arr($data);
			return $data;
		}
	}

	function http_get($headers, $content, $host, $port) {

 	  	// make connection to server
	    $connRef = fsockopen($host, $port, $errno, $errstr, 50);

        set_time_limit(100);

	    $data = "";

	    if (!$connRef) {
	        $data .= "Connect Failed: ".$errstr." (".$errno.")";
if (aw_global_get("soap_debug") == 1 )
{
echo "fetch data fail= <pre>".htmlentities($data)."</pre><br>";
}
return false;
	    }

	    fputs($connRef, $headers);
	    fputs($connRef, $content);

		stream_set_timeout($connRef, 600);
		set_time_limit(600);

		$data = "";
		$status = socket_get_status($connRef);


		$read = 1;
		$readSize=0;

		while($read>=1 && !feof($connRef) && !$status['timed_out']) {

			$line = "";

			if (feof($connRef))
			{
			    $read = 0;
			}
			else
			{
				$line = fgets($connRef, 1024);
				$data .= $line;
				$status = socket_get_status($connRef);
			}
		}

		@fclose($connRef);
if (aw_global_get("soap_debug") == 1 )
{
echo "fetch data = <pre>".htmlentities($data)."</pre><br>";
}

	return $data;
  }

	function serialize_parameters($paramsArray)
	{
		$this->paramId = 0;
	    $return = array();
	    while (list($key, $value) = each($paramsArray))
		{
   			$result = $this->serialize_value($key,$value);
   			$content .= $result["main"];
   			$ADD .= $result["add"];
		}
	    $return["main"] = $content;
	    $return["add"] = $ADD;
	    return $return;
	}

	function serialize_value($key,$value,$type="",$refType="")
	{
	    $return = array();


		$params = "";
		if ($type!="")
		{
			$params = " xsi:type=\"".$type."\"";
		}
		else if ($this->paramTypes[$key]!="")
		{
		    $type = $this->paramTypes[$key];

		}
    	if (is_array($value) && !is_numeric( implode( array_keys( $value ) ) ))
    	{
			$result = $this->serialize_associative_array($key,$value,$type,$refType);
			$content .= $result["main"];
			$ADD .= $result["add"];
		}
    	else if (is_array($value))
    	{
			$result = $this->serialize_array($key,$value,$type,$refType);
			$content .= $result["main"];
			$ADD .= $result["add"];
			//$content = $result["add"];
		}
      	else if (is_bool($value))
      	{
        	$content.= "      <".$key.">".(($value===true)?"true":"false")."</".$key.">".$eol;
		}
      	else if ($value==="")
      	{
        	$content.= "      <".$key."".$params." />".$eol;
		}
		else
		{
        	$content.= "      <".$key."".$params.">".htmlspecialchars($value)."</".$key.">".$eol;
		}

	    $return["main"] = $content;
	    $return["add"] = $ADD;

	    return $return;
	}


	function serialize_associative_array($key,$value,$type="",$refType="")
	{
	    $return = array();

		$ref = "soapenc:Array";
		if ($refType!="") $ref = $refType;

		$this->paramId++;
	    $ids = $this->paramId;

		if ($type!="")
		{
			$content .= '<'.$key.' href="#id'.$ids.'" />';

			$ADD = "";

			if ($type!="")
			{
				$params = ' soapenc:arrayType="'.$type.'['.count($value).']"';
			}
			$ADD .= '<'.$ref.' id="id'.$ids.'"'.$params.'>';
			$addNew = "";
			foreach ($value AS $k=>$v)
			{
			    $result = $this->serialize_value($k,$v,"",$type);
			    $ADD .= $result["main"];
			    $addNew .= $result["add"];
			}
			$ADD .= '</'.$ref.'>';
			$ADD .= $addNew;

		}
		else
		{

			$content.= "      <".$key."".$params.">".$eol;
			foreach ($value AS $k=>$v)
			{
	        	$result = $this->serialize_value($k,$v);
	        	$content .= $result["main"];
	        	$ADD .= $result["add"];
			}
			$content.= "      </".$key.">".$eol;
		}

	    $return["main"] = $content;
	    $return["add"] = $ADD;

	    return $return;
	}

	function serialize_array($key,$value,$type="",$refType="")
	{
	    $return = array();

		$this->paramId++;
	    $ids = $this->paramId;

		$rv = "<$key>\n";

        if ($type=="") $type = "xsd:int";

        $ref = "soapenc:Array";
		if ($refType!="") $ref = $refType;

		$content .= '<'.$key.' href="#id'.$ids.'" />';

		$ADD = "";

		if ($ref=="soapenc:Array" && $type!="")
		{
			$params = ' soapenc:arrayType="'.$type.'['.count($value).']"';
		}

		$ADD .= '<'.$ref.' id="id'.$ids.'"'.$params.'>';
		$addNew = "";
		foreach ($value AS $k=>$v)
		{
			$rv .= "<int>$v</int>\n";
		    $k="Item";
		    $result = $this->serialize_value($k,$v,"",$type);
		    $ADD .= $result["main"];
		    $addNew .= $result["add"];
		}
		$ADD .= '</'.$ref.'>';
		$ADD .= $addNew;


	    $return["main"] = $content;
	    $return["add"] = $ADD;

		$rv .= "</$key>\n";
		return array("main" => $rv, "add" => "");
		die($rv);

	    return $return;
	}

}

class C_XMLDoc {
	var $parser;			// Object Reference to parser
	var $xml;				// Raw XML code
	var $version;			// XML Version
	var $encoding;			// Encoding type
	var $entities;			// Array (key/value set) of entities
	var $hrefArray;			// Array of object references to XML tags in a DOC (by href's)
	var $xml_index;			// Array of object references to XML tags in a DOC
	var $xml_reference;		// Next available Reference ID for index
	var $document;			// Document tag (type: C_XMLTag)
	var $stack;				// Current object depth (array of index numbers)
	var $stackDataRef;
	var $filename;

	function C_XMLDoc($xml='') {
		$this->xml = $xml;
		$this->version = '1.0';
		$this->encoding = "UTF-8";
		$this->entities = array();
		$this->xml_index = array();
		$this->xml_reference = 0;
		$this->stack = array();
		$this->hrefArray = array();

	}

	function loadFromFile($filename) {
	  $this->xml = "";
	  $this->filename = "";
	  if (file_exists($filename)) {
			$xml = file_get_contents ( $filename );
			$this->filename = $filename;
			if (trim($xml)!="") {
				$this->xml = trim($xml);
			}
		}
	}

	function parse($xml="")
	{
        $this->__parsing = true;

		if ($xml!="") $this->xml = $xml;

		$this->parser = xml_parser_create($this->encoding);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "characterData");
		xml_set_default_handler($this->parser, "defaultHandler");

		if (!xml_parse($this->parser, $this->xml)) {
			// Error while parsing document
			$err_code = xml_get_error_code($this->parser);
			$err_string = xml_error_string($err_code);
			$err_line = xml_get_current_line_number($this->parser);
			$err_col = xml_get_current_column_number($this->parser);
			$err_byte = xml_get_current_byte_index($this->parser);

			print "<p><b>Error Code:</b> $err_code ($err_string)<br><b>File:</b> ".$this->filename.", <b>Line:</b> $err_line, <b>Column:</p> $err_col";
		}

		xml_parser_free($this->parser);
        $this->__parsing = false;
	}

	function destroy_parser()
	{
		if (is_resource($this->parser))
		{
			xml_parse($this->parser, null, 1);
			xml_parser_free($this->parser);
		}
	}

	function stack_location()
	{
		return $this->stack[(count($this->stack) - 1)];
	}

	function startElement($parser, $name, $attrs=array())
	{
		if (count($this->stack) == 0)
		{
			$this->document = new C_XMLTag($this,$name,$attrs);
			$this->document->refID = 0;
			$this->xml_index[0] =& $this->document;
			$this->xml_reference = 1;
			$this->stack[0] = 0;
            $this->stackDataRef = 0;
		}
		else
		{

	   		if (isset($attrs["href"]))
			{
			    $id = $attrs["href"];
			    if (substr($attrs["href"],0,1)=="#") $id = substr($attrs["href"],1,strlen($attrs["href"]));
				$stack = $this->stack;
				$this->hrefArray[$id] = array("stack"=>$stack,"reference"=>$this->xml_reference);
			}

			if (isset($attrs["id"]))
			{
				if (isset($this->hrefArray[$attrs["id"]]))
				{
					$this->stack = $this->hrefArray[$attrs["id"]]["stack"];
					$parent_index = $this->hrefArray[$attrs["id"]]["reference"];
					unset($this->hrefArray[$attrs["id"]]);
					$parent =& $this->xml_index[$parent_index];
					$parent->type = $name;
					$parent->attributes = $attrs;
					$this->xml_index[$this->xml_reference] =& $parent;
					array_push($this->stack,$parent->refID);
				}
            }
            else
            {
				$parent_index = $this->stack_location();
				$parent =& $this->xml_index[$parent_index];
				$childIndex = $parent->addChild($this,$name,$attrs);
				array_push($this->stack,$childIndex);
			}
		}

	}

	function endElement($parser, $name)
	{
		array_pop($this->stack);
	}

	function characterData($parser, $data)
	{
		$cur_index = $this->stack_location();
		$tag =& $this->xml_index[$cur_index];
		$tag->contents .= $data;
	}

	function defaultHandler($parser, $data) {

	}

	function createTag($name, $attrs=array(), $contents='', $parentID = '')
	{
		if ($parentID === '') {
			$this->document = new C_XMLTag($this,$name,$attrs,$contents);
			$this->document->refID = 0;
			$this->xml_index[0] =& $this->document;
			$this->xml_reference = 1;
			return 0;
		} else {
			$parent =& $this->xml_index[$parentID];
			return $parent->addChild($this,$name,$attrs,$contents);
		}
	}

	function getTag($tagID,&$name,&$attributes,&$contents,&$tags)
	{
		$tag =& $this->xml_index[$tagID];
		$name = $tag->name;
		$attributes = $tag->attributes;
		$contents = $tag->contents;
		$tags = $tag->tags;
	}

	function ToArray($obj)
	{
		$return = array();
		if (is_a($obj,"C_XMLTag")) {
			if (count($obj->children)>0) {
				foreach ($obj->children AS $k=>$v) {
					$return[$v->name] = $this->ToArray($v);
				}
			} else {
				$return = $obj->contents;
			}
		}
		return $return;
	}

}


class C_XMLTag {

	var $refID;			// Unique ID number of the tag
	var $name;			// Name of the tag
	var $type;
	var $attributes = array();	// Array (assoc) of attributes for this tag
	var $tags = array();		// An array of refID's for children tags
	var $contents;			// textual (CDATA) contents of a tag
	var $children = array();	// Collection (type: C_XMLTag) of child tag's



	function C_XMLTag(&$document,$tag_name,$tag_attrs=array(),$tag_contents='') {
		// Constructor function for C_XMLTag class


		// Set object variables
		$this->name = $tag_name;
		$this->attributes = $tag_attrs;
		$this->contents = $tag_contents;

		$this->tags = array();			// Initialize children array/collection
		$this->children = array();

	}

	function hasChildren() {
	    if (count($this->children)>0) return true;
	    else return false;
	}

	function addChild (&$document,$tag_name,$tag_attrs=array(),$tag_contents='')
	{
		$this->children[(count($this->children))] = new C_XMLTag($document,$tag_name,$tag_attrs,$tag_contents);

		$document->xml_index[$document->xml_reference] =& $this->children[(count($this->children) - 1)];
		$document->xml_index[$document->xml_reference]->refID = $document->xml_reference;
		$document->xml_reference++;
		return ($document->xml_reference - 1);
	}

}
}
?>

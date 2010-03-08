<?php
// dns.aw - various DNS related functionality, whois queries
// $Header: /home/cvs/automatweb_dev/classes/core/util/dns.aw,v 1.5 2008/01/31 13:54:05 kristo Exp $
/*
@classinfo  maintainer=kristo
*/
class dns extends aw_template
{
	// ns query types
	var $types = array(
		"A" => 1,
		"NS" => 2,
		"MD" => 3,
		"MF" => 4,
		"CNAME" => 5,
		"SOA" => 6,
		"MB" => 7,
		"MG" => 8,
		"MR" => 9,
		"NULL" => 10,
		"WKS" => 11,
		"PTR" => 12,
		"HINFO" => 13,
		"MINFO" => 14,
		"MX" => 15,
		"TXT" => 16
	);

	function dns()
	{
		$this->init("dns");
	}

	/** Draws the form for making whois query  
		
		@attrib name=query_form params=name nologin="1" default="0" api=1
		
		
		@returns
			Form for making whois query
		
		@comment
			Uses enter_whois_query.tpl admin template.
	**/
	function query_form($args = array())
	{
		$this->read_adm_template("enter_whois_query.tpl");
		$this->vars(array(
			"reforb" => $this->mk_reforb("query",array("no_reforb" => 1)),
		));
		return $this->parse();
	}
	
	/** Uses whois to get whois info for the specified domain 
		
		@attrib name=query params=name nologin="1" default="0" api=1
		
		@param domain required type=string
		
		@returns
			Preformatted string with the output of the 'whois' command
		
		@examples
		$i = get_instance('core/util/dns');
		echo $i->query(array('domain' => 'struktuur.ee'));

	**/
	function query($args = array())
	{
		extract($args);
		if (not($domain))
		{
			return "<b>no domain specified</b>";
		}
		else
		{
			if (strlen(preg_replace("/[\w|\d|\.]/","",$domain)) > 0)
			{
				return "<b>domain contains forbidden characters</b>";
			}
			else
			{
				$fp = popen ("/usr/bin/whois $domain", "r");
				if (not($fp))
				{
					return "<b>Couldn't connect to whois client</b>";
				};
				$data = fread($fp, 8192);
				pclose($fp);
				return "<pre>" . $data . "</pre>";
			};
			
		};
	}
	
	/** Draws the form for making DNS query 
		
		@attrib name=dns_form params=name nologin="1" default="0" api=1

		@returns 
			Form for making DNS query
		@comment
			Uses enter_domain_query.tpl admin template.
	**/
	function dns_form($args = array())
	{
		$this->read_adm_template("enter_domain_query.tpl");
		$this->vars(array(
			"reforb" => $this->mk_reforb("dns_query",array("no_reforb" => 1)),
		));
		return $this->parse();
	}
	
	/** Checks if the specified domain name is registered or not 
		
		@attrib name=dns_query params=name nologin="1" default="0" api=1
		
		@param domain required type=string
		
		@returns
			String which tells if the domain is registered or not
		
		@examples
		$i = get_instance('core/util/dns');
		echo $i->dns_query(array('domain'  => 'struktuur.ee'));
	**/
	function dns_query($args = array())
	{
		extract($args);
		if (not($domain))
		{
			return "<b>no domain specified</b>";
		}
		else
		{
			if (not(preg_match("/^([\w|\d]*)\.[org|net|com|ee]/",$domain)))
			{
				return "<b>Invalid domain</b><br />";
			}
			else
			{
				// it's a ee domain, query eenets server
				if (preg_match("/\.ee$/",$domain))
				{
					$retval = join("",file("http://www.eenet.ee/info/index.html?otsi=$domain&formaat=pikk"));
					if (preg_match("/tulemusena leiti <b>0<\/b> kirjet./",$retval))
					{
						return "$domain on registreerimata";
					}
					else
					{
						return "$domain on registreeritud";
					};
				}
				else
				{
					$fp = fsockopen("rs.internic.net", 43, &$errno, &$errstr, 10);
					if (not($fp))
					{
						return "Cannot connect to rs.internic.net";
					};
					fputs($fp,"$domain\r\n");
					$buf = "";
					while(!feof($fp)) 
					{
						$buf .= fgets($fp,128);
					};
					fclose($fp);

					if (preg_match("/No match for/i",$buf))
					{
						return "$domain on registreerimata<br />";
					}
					else
					{
						return "$domain on registreeritud<br />";
					};
				};
			};
		};
	}

	/** Queries nameservers for the specified domain
		
		@attrib name=get_record_NS params=name nologin="1" default="0" api=1
		
		@param domain required type=string
			The domain for which the info is requested
		@returns
			The content of a DNS record, the available record types are in $this->types
		
		@examples
		$i = get_instance('core/util/dns');
		echo $i->get_record_NS(array('domain'  => 'struktuur.ee'));
	**/
	function get_record_NS($arr)
	{
		extract($arr);

		// creating DNS packets is just too damn hard in php, so fuck that, let's just call nslookup and have that do 
		// all the hard work. yeah, I know, this won't work in windows but fuck that right now, cause this shit won't anyway

		// get domain name - last 2 parts
		$dom = $this->get_domain_name_for_url($domain);

		$cmd = aw_ini_get("server.nslookup")." -type=NS $dom -class=IN";
		$op = `$cmd`;
				
		// now scan the op for the needed data
		// basically the needed stuff is in this format:
		// [domain] nameserver = [ns]
		
		$patt = "/$dom\snameserver\s=\s(.*)/";
		preg_match_all($patt,$op, $mt, PREG_PATTERN_ORDER);
		return $mt[1];
	}

	function get_id()
	{
		$lid = aw_global_get("dns::last_query_id");
		$lid++;
		aw_global_set("dns::last_query_id", $lid);
		return (int)$lid;
	}

	/** Gets the domainname for the URL
		
		@attrib name=get_domain_name_for_url params=pos nologin="1" default="0" api=1
		
		@param domain required type=string
			The URL where the domainname will be extracted
		@returns
			The extracted domainname (last 2 parts of the url)
		
		@examples
		$i = get_instance('core/util/dns');
		echo $i->get_domain_name_for_url('http://www.struktuur.ee'); // prints 'struktuur.ee'
	**/
	function get_domain_name_for_url($domain)
	{
		$pts = explode(".", $domain);
		$cnt = count($pts);
		$dom = $pts[$cnt-2].".".$pts[$cnt-1];
		return $dom;
	}

/*		$qid = $this->get_id();
		$this->id2query[$qid] = $arr;

		// packet format: 
		// heaer: ID(16)/STUFF(16)/QDCOUNT(16)/ANCOUNT(16)

		$packet = pack("ssssssCa*Ca*Ca*Css",
			$qid,	// ID
			1,		// STUFF
			1,		// QDCOUNT
			0,		// ANCOUNT
			0,		// NSCOUNT
			0,		// ARCOUNT
			// now packet sections
			2,
			"aw",
			9,
			"struktuur",
			2,
			"ee",
			0,
			$this->types[$type],
			0
		);	
		echo "packet = <pre>", $this->binhex($packet),"</pre> <br />";
		
		// connecting to nameserver
		$fp = fsockopen("udp://212.7.7.6",53,$errno, $errstr,10);
		echo "errno = $errno , errstr = $errstr <br />\n";
		flush();
		fwrite($fp, $packet);
		$cnt = 0;
		while(!feof($fp) || ($cnt++ > 100))
		{
			echo fgets($fp, 10);

		}
		fclose($fp);*/
}
?>

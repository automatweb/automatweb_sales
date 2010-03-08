<?php
/*
@classinfo  maintainer=kristo
*/

class dns_server_manager extends class_base
{
	function dns_server_manager()
	{
		$this->init();
	}

	/**  
		
		@attrib name=can_manage_server params=name nologin="1" default="0"
		
		
		@returns
		
		
		@comment

	**/
	function can_manage_server($arr)
	{
		extract($arr);
		return true;
	}

	/** adds or updates site in the current ns config 
		
		@attrib name=add_or_update_site params=name nologin="1" all_args="1" default="0"
		
		
		@returns
		
		
		@comment
		parameters:
		domain - the domain to add or update
		ip - the ip address for the domain

	**/
	function add_or_update_site($arr)
	{
		extract($arr);
		
		// find the domain for the url
		classload("core/util/dns");
		$dom = dns::get_domain_name_for_url($domain);

		// find the zone file for the domain

		$zone_file = $this->cfg['zone_file_root']."/".$dom;
		$tmp_zone_file = tempnam(aw_ini_get("server.tmpdir"),"aw_dnsmgr_zone_tmp");

		// use su_exec to make a copy of the file, because we might not have access to even read it
		$su = get_instance("install/su_exec");
		$su->add_cmd("copy $zone_file $tmp_zone_file");
		$su->exec();

		// parse the file
		// add or update the domain in the file
		// update the last-modified time in the file
		// write to temp file
		$this->do_parse_file(array(
			"file" => $tmp_zone_file,
			"domain" => $domain,
			"ip" => $ip
		));

		// use su_exec to copy it to the right place	
		// use su exec to execute "rndc reload"
		$su->open_file();
		$su->add_cmd("copy $tmp_zone_file $zone_file");
		$su->add_cmd("rndc reload");
		$su->exec();

		return true;
	}

	////
	// params:
	//   file
	//   domain
	//   ip
	function do_parse_file($arr)
	{
		extract($arr);
		$fc = $this->get_file(array("file" => $file));

		// check if the domain exists in the file
		if (($pos = strpos($fc,$domain)) !== false)
		{
			// it does, replace the entry 
			// assume one address per line
			// format is like this: 
			// domain.somewhere.ee <space> A <space> ip
			// actually it can also be different, but right now just support that
			
			// $pos is beginning of line, find the end
			$end = $pos;
			$len = strlen($fc);
			while (!($fc[$end] == "\r" || $fc[$end] == "\n" || $end >= $len))
			{
				$end++;
			}
			$line = substr($fc, $pos, ($end - $pos));
			// now, replace the ip aaddress
			$line = preg_replace("/$domain.(\s)A(\s)(.*)/", "$domain.\tA\t$ip", $line);
			$fc = substr($fc, 0, $pos).$line.substr($fc,$end);
		}
		else
		{
			// it does not, add it to the end
			$fc.= "\n$domain.\tA\t$ip\n";
		}
	
		// update timestamp
		$lines = explode("\n", $fc);
		// find the line that contains only a string of numbers of the correct length (10)
		foreach($lines as $lineno => $line)
		{
			if (strpos($line, ";") !== false)
			{
				// strip comments
				$line = preg_replace("/(.*);(.*)/","\\1", $line);
			}
			if (strlen(trim($line)) == 10 && strspn(trim($line), "0123456789") == strlen(trim($line)))
			{
				// found line
				$found = true;
				break;
			}
		}
	
		if ($found)
		{
			$snum = 0;
			$dstr = trim($lines[$lineno]);
			if (substr($dstr, 0, 8) == date("Ymd"))
			{
				$snum = (int)(substr($dstr,8));
			}
			$np1 = strlen($snum+1) == 1 ? "0".($snum+1) : $snum+1;
			$lines[$lineno] = "\t\t\t".date("Ymd").$np1;
		}
		$fc = join("\n", $lines);

		$this->put_file(array(
			"file" => $file,
			"content" => $fc
		));
	}
}
?>

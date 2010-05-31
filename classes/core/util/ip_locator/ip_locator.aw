<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/util/ip_locator/ip_locator.aw,v 1.11 2008/10/06 14:23:16 markop Exp $
// ip_locator.aw - IP lokaator 
/*

@classinfo syslog_type=ST_IP_LOCATOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

	@property csv_file_location type=textbox size=100 method=serialize field=meta
	@caption CSV faili asukoht

	@property info type=text store=no
	@caption Info

	@property update_db type=text store=no
	@caption Uuenda andmebaasi

@groupinfo search caption="Otsing"
@default group=search

	@property ip type=textbox store=no
	@caption IP aadress

	@property search_result type=text store=no
	@caption Tulemus

	@property search_ip type=submit store=no
	@caption Otsi 

*/

class ip_locator extends class_base
{
	const AW_CLID = 1177;


	var $db_table_name;

	function ip_locator()
	{
		$this->init(array(
			"tpldir" => "core/util/ip_locator/ip_locator",
			"clid" => CL_IP_LOCATOR
		));

		$this->db_table_name = 'ip2country';
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'info':
				$csv_file = $arr['obj_inst']->prop('csv_file_location');
				if (is_readable($csv_file))
				{
					$prop['value'] = t('Faili viimati muudetud: ').date('d.m.Y', filemtime($csv_file));
				}
				break;
			case 'update_db':
				$prop['value'] = html::href(array(
					'caption' => t('Uuenda baasi'),
					'url' => $this->mk_my_orb('update_db', array(
						'id' => $arr['obj_inst']->id(),
						'debug' => 1
					))
				));
				break;
			case 'ip':
				$prop['value'] = $arr['request']['ip'];
				break;
		};
		return $retval;
	}
/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	
*/

	function _get_search_result($arr)
	{
		$ip = $arr['request']['ip'];
		$country = $this->search($ip);
		
		if (empty($ip))
		{
			return PROP_OK;
		}
		if ($country === false)
		{
			$arr['prop']['value'] = t('Sellist IP-d ei leitud andmebaasist');
		}
		else
		{
			$arr['prop']['value'] .= sprintf(t('Maa: <strong>%s</strong> <br />'), $country['country_name']);
			$arr['prop']['value'] .= sprintf(t('Kahekohaline kood: <strong>%s</strong> <br />'), $country['country_code2']);
			$arr['prop']['value'] .= sprintf(t('Kolmekohaline kood: <strong>%s</strong> <br />'), $country['country_code3']);
			$arr['prop']['value'] .= t('Vastavalt ISO 3166 standardile');
		}
		return PROP_OK;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval($arr)
	{
		$arr['args']['ip'] = $arr['request']['ip'];
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/** Searches the supplied IP address from ip-to-country database
		@attrib name=search api=1 params=pos 

		@param ip required type=string
			IP address to search as dotted string
		@errors none
		@returns array with the data about the IP 
			false if the IP is not correctly formed
			false if the IP isn't found in the database
		@comment
			This search is based on an ip-to-country database provaided by
			http://ip-to-country.webhosting.info/
			It is possible that it isn't 100% accurate, so it is needed to check for newer version of the database when want to use it.
		@examples
			$locator = get_instance('core/util/ip_locator/ip_locator');
			$locator->search('62.65.36.186');
			var_dump($f);

			// prints out:
			// 	array(6) {
			//		["id"]=>
			//		string(6) "362013"
			//		["ip_from"]=>
			//		string(10) "1044455424"
			//		["ip_to"]=>
			//		string(10) "1044463615"
			//		["country_code2"]=>
			//		string(2) "EE"
			//		["country_code3"]=>
			//		string(3) "EST"
			//		["country_name"]=>
			//		string(9) "ESTONIA"
			//	}
	
	**/
	function search($ip)
	{
		// check tix list
/*		$fc = file("http://tix.estpak.ee/networks.txt");
		foreach($fc as $line)
		{
			list(, $ip_range) = explode(" ", $line);
			list($range_adr, $range_len) = explode("/", $ip_range);
			$int_range_adr = ip2long($range_adr);
			$int_ip = ip2long($ip);

			// chop off the given number of bits and compare
			$mask = pow(2, ($range_len+1))-1;
			if (($int_range_adr | $mask) == ($int_ip | $mask))
			{
				return array(
					"country_code3" => "EST"
				);
			}
		}
*/		//		return false;
		$ip_number = ip2long($ip);
		if ($ip_number != -1)
		{
			$sql = "select * from ".$this->db_table_name." where ip_from <= ".$ip_number." and ip_to >= ".$ip_number;
			$data = $this->db_fetch_row($sql);
			if (empty($data))
			{
				// check tix list
				$fc = file("http://tix.estpak.ee/networks.txt");
				foreach($fc as $line)
				{
					list(, $ip_range) = explode(" ", $line);
					list($range_adr, $range_len) = explode("/", $ip_range);
					$int_range_adr = ip2long($range_adr);
					$int_ip = ip2long($ip);
					
					// chop off the given number of bits and compare
					$mask = pow(2, ($range_len+1))-1;
					if (($int_range_adr | $mask) == ($int_ip | $mask))
					{
						return array(
							"country_code2" => "EE",
							"country_code3" => "EST"
						);
					}
				}
				return false;
			}
			else
			{
				return $data;
			}
		}
		else
		{
			return false;
		}
	}

	/**
		@attrib name=update_db
		
		@param id required type=int
		@param debug optional type=int
	**/
	function update_db($arr)
	{
		$debug = $arr['debug'];

		// peaks supportima zip-ist lahti pakkimsit - v6i mis iagnes formaadist seda scv-d saab
	
		// samuti v6iks toetada seda, et annan selle zip-i urli ette ja siis ta t6mbab selle ise alla ja 
		// ja uuendab baasi tabeli 2ra
		
	//	$file = '/www/dev/dragut/site/files/ip-to-country.csv';
//$file = 'http://marko.struktuur.ee/ip-to-country.csv';
		$n = 0;
		if ($this->can('view', $arr['id']))
		{
			$obj_inst = new object($arr['id']);
			$file = $obj_inst->prop('csv_file_location');
		}
		else
		{$n = 2;
			$file = aw_ini_get("classdir")."/core/util/ip_locator/IpToCountry.csv";
		}
		$db_table_name = 'ip2country';
		
		if ($this->db_table_exists($this->db_table_name) === false)
		{
			$this->db_query('create table '.$this->db_table_name.' (
				id int not null primary key auto_increment,
				ip_from double,
				ip_to double,
				country_code2 char(2),
				country_code3 char(3),
				country_name varchar(255)
			)');
		}

		$this->db_query("delete from ".$this->db_table_name);
		$lines = file($file);
		foreach ($lines as $line)
		{
			$line = trim($line);
			if ($line{0} == '#' || empty($line))
			{
				continue;
			}
			$line = str_replace('"', '', $line);
			$fields = explode(',', $line);
			if(!strlen(ip2long($fields[0])) || !strlen(ip2long($fields[1])))
			{
				continue;
			}

/*			$sql = "insert into ".$this->db_table_name." (
					ip_from, 
					ip_to, 
					country_code2, 
					country_code3, 
					country_name
				) values (
					".(double)$fields[0].", 
					".(double)$fields[1].", 
					'".addslashes($fields[4])."', 
					'".addslashes($fields[5])."', 
					'".addslashes($fields[6])."'
			)";*/
			$sql = "insert into ".$this->db_table_name."(
			ip_from,
			ip_to,
			country_code2,
			country_code3,
			country_name
			) values (
			". ip2long($fields[0]).",
					". ip2long($fields[1]).",
			'".$fields[2+$n]."',
			'".$fields[3+$n]."',
			'".addslashes($fields[4+$n])."'
				)";
//arr($sql);
			$this->db_query($sql);

			if ($debug){
				echo $sql."<br />";
			}
		}

		return $this->mk_my_orb('change', array(
			'id' => $arr['id']
		));
	}
}
?>

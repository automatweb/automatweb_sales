<?php

/*

@classinfo syslog_type=ST_IPADDRESS no_status=1  maintainer=kristo

@groupinfo general caption=Üldine

@tableinfo ipaddresses index=id master_table=objects master_index=id

@default table=objects
@default group=general

@property addr type=textbox table=ipaddresses field=ip
@caption IP Aadress

@property range type=textarea table=objects field=meta method=serialize rows=5 cols=50
@caption Vahemik

*/

class ipaddress extends class_base
{
	function ipaddress()
	{
		$this->init(array(
			'tpldir' => 'syslog/IP Aadress',
			'clid' => CL_IPADDRESS
		));
	}

	////
	// !creates or returns the object that corresponds to the specified ip
	// parameters:
	//	ip - the address, required
	//	parent - where to create the object, optional, if not specified, the folrder is read from config
	function get_obj_from_ip($arr)
	{
		extract($arr);
		$ol = new object_list(array(
			"class_id" => CL_IPADDRESS,
			"site_id" => array(),
			"class_id" => array(),
			"ip" => $ip
		));
		if ($ol->count() < 0)
		{
			if (!$parent)
			{
				$parent = $this->get_cval("ipaddresses::default_folder");
				if (!$parent)
				{
					$parent = $this->cfg['rootmenu'];
				}
			}

			$obj = new object();
			$obj->set_parent($parent);	
			$obj->set_name($ip);
			$obj->set_class_id(CL_IPADDRESS);
			$obj->set_prop("ip", $ip);
			$obj->set_meta("ip", $ip);
			$obj->save();
		}
		else
		{
			$obj = $ol->begin();
			$obj->set_meta("ip", $ip);
		}
		return $obj;
	}


	////
	// !returns the ip address associated with the object $oid
	function get_ip_from_obj($oid)
	{
		$o = obj($oid);
		return $o->prop("ip");
	}

	function match_range($range, $adr)
	{
		// silly ass check - iterate over range and check each
		$res = false;
		list($from,$to) = explode("-", trim($range));
		list($f1,$f2,$f3,$f4) = explode(".", $from);
		list($t1,$t2,$t3,$t4) = explode(".", $to);

		for($i = $f4; $i <= $t4; $i++)
		{
			$a = $f1.".".$f2.".".$f3.".".$i;
			$res |= $this->match($a, $adr);
		}

		return $res;
	}

	////
	// !returns true if ip addressesd match. can compare either ip or name addresses, does masks like *.ee, 255.255.*
	// only the first parameter can be a mask, the other must be a complete address ip/name,
	// but you can mix ip/name types like this: match(foo.ee, 1.2.3.4) / match(*.foo.ee, 1.2.3.4) / match(1.2.*, www.ee)
	function match($a1, $a2)
	{
		if ($a1 == "*")
		{
			return true;
		}

		if (inet::is_ip($a1) && inet::is_ip($a2))
		{
			return ($a1 == $a2);
		}

		$a1e = explode(".", $a1);
		$a1_is_num = true;
		$a1_is_num_complete = true;
		$a1_is_string_complete = true;
		foreach($a1e as $pt)
		{
			if (!(is_numeric($pt) || $pt == "*"))
			{
				$a1_is_num = false;
			}
			if (!is_numeric($pt))
			{
				$a1_is_num_complete = false;
			}
			if ($pt == "*")
			{
				$a1_is_string_complete = false;
			}
		}

		$a2e = explode(".", $a2);
		$a2_is_num = true;
		$a2_is_num_complete = true;
		foreach($a2e as $pt)
		{
			if (!(is_numeric($pt) || $pt == "*"))
			{
				$a2_is_num = false;
			}
			if (!is_numeric($pt))
			{
				$a2_is_num_complete = false;
			}
		}

		if (!$a2_is_num_complete)
		{
			error::raise(array(
				"id" => ERR_IP,
				"msg" => sprintf(t("ipaddress::match(%s, %s): the second parameter must be a complete ip address, it can not be a mask!"), $a1, $a2)
			));
		}

		if ($a1_is_num && !$a1_is_num_complete && !$a2_is_num)
		{
			$a2 = inet::name2ip($a2);
		}
	
		if (!$a1_is_num && $a2_is_num)
		{
			list($a2) = inet::gethostbyaddr($a2);
		}

		if ($a1_is_string_complete)
		{
			return (trim($a1) == trim($a2));
		}

		// a1 is mask, a2 is the same type as a1
		$a1pts = explode(".", $a1);
		$a2pts = explode(".", $a2);

		$match = true;
		if (!$a1_is_num)
		{
			// if we are comparing name mask with name, reverse the parts, then ew can compare them
			// as ip address parts
			$a1pts = array_reverse($a1pts);
			$a2pts = array_reverse($a2pts);
		}

		foreach($a1pts as $idx => $pt)
		{
			if ($pt == "*")
			{
				continue;
			}
			if ($pt != $a2pts[$idx])
			{
				$match = false;
			}
		}

		return $match;
	}
}
?>

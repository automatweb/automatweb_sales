<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_users_import.aw,v 1.12 2009/05/21 13:35:53 dragut Exp $
// taket_users_import.aw - Taketi kasutajate import 
/*
@classinfo syslog_type=ST_TAKET_USERS_IMPORT relationmgr=yes

@default table=objects
@default group=general

@groupinfo caption=Impordi
@property import type=text store=no method=Import
*/

class taket_users_import extends class_base implements customer_import_datasource
{
	const AW_CLID = 247;

	function taket_users_import()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_users_import",
			"clid" => CL_TAKET_USERS_IMPORT
		));
	}


	function get_customer_list_xml()
	{
		$gud = file("http://88.196.208.74:8888/xmlrpc/index.php?get_users_data=1");
		$first = true;
		$customers = array();
		foreach($gud as $line)
		{
			if ($first)
			{
				$first = false;
				continue;
			}

			$fields = explode("\t", trim($line));
			$id = trim($fields[0]);
			if (isset($customers[$id]))
			{
				// add category
				$customers[$id]["categories"][] = trim($fields["18"]);
			}
			else
			{
				$currency = null;
				$cur = trim($fields[13]);
				if ($cur != "")
				{
					$currency = currency::find_by_symbol($cur)->id();
				}
				$customers[$id] = array(
					"extern_id" => $id,
					"name" => trim($fields[3]),
					"fake_address_address" => trim($fields[4]),
					"fake_address_postal_code" => trim($fields[6]),
					"fake_email" => trim($fields[7]),
					"fake_phone" => trim($fields[8]),
					"fake_mobile" => trim($fields[11]),
					"fake_url" => trim($fields[12]),
					"currency" => $currency,
					"fake_address_country" => trim($fields[14]),
					"language" => trim($fields[15]),
					"categories" => array(trim($fields["18"]))
				);
			}
		}

		$xml = new SimpleXMLElement("<?xml version='1.0'?><customers></customers>");
		foreach($customers as $cust)
		{
			if (trim($cust["name"]) == "")
			{
				continue;
			}
			$cat = $xml->addChild("customer");
			$cat->addChild("name", $this->_t($cust["name"]));
			$cat->addChild("extern_id", $this->_t($cust["extern_id"]));
			$cat->addChild("fake_address_address", $this->_t($cust["fake_address_address"]));
			$cat->addChild("fake_address_postal_code", $this->_t($cust["fake_address_postal_code"]));
			$cat->addChild("fake_email", $this->_t($cust["fake_email"]));
			$cat->addChild("fake_phone", $this->_t($cust["fake_phone"]));
			$cat->addChild("fake_mobile", $this->_t($cust["fake_mobile"]));
			$cat->addChild("fake_url", $this->_t($cust["fake_url"]));
			$cat->addChild("fake_address_country", $this->_t($cust["fake_address_country"]));
//			$cat->addChild("language", $cust["language"]);

			$cats = $cat->addChild("categories");
			foreach($cust["categories"] as $catx)
			{
				$cats->addChild("category", $this->_t($catx));
			}
		}
		return $xml->asXML();
	}

	function _t($s)
	{
		return str_replace("&", "&amp;", htmlentities($s));
	}

	function get_category_list_xml()
	{
		$gud = file("http://88.196.208.74:8888/xmlrpc/index.php?get_users_data=1");
		$first = true;
		$groups = array();
		foreach($gud as $line)
		{
			if ($first)
			{
				$first = false;
				continue;
			}

			$fields = explode("\t", trim($line));
			$groups[trim($fields[18])] = trim($fields[18]);
		}

		$xml = new SimpleXMLElement("<?xml version='1.0'?><categories></categories>");
		foreach($groups as $group)
		{
			if (trim($group) == "")
			{
				continue;
			}
			$cat = $xml->addChild("category");
			$cat->addChild("name", $group);
			$cat->addChild("extern_id", $group);
		}
		return $xml->asXML();
	}

	function get_person_list_xml()
	{
//		$gud = file("http://88.196.208.74:8888/xmlrpc/index.php?get_users_data=1");
		$gpd = file("http://88.196.208.74:8888/xmlrpc/index.php?get_persons_data=1");

		$first = true;
		$customers = array();
		foreach($gpd as $line)
		{
			if ($first)
			{
				$first = false;
			$fields = explode("\t", trim($line));
//die(dbg::dump($fields));
				continue;
			}

			$fields = explode("\t", trim($line));
			$id = trim($fields[0]);
			if (!isset($customers[$id]))
			{
				$customers[$id] = array(
					"extern_id" => $id,
					"extern_customer_id" => trim($fields[1]),
					"name" => trim($fields[4]),
					"first_name" => trim($fields[2]),
					"last_name" => trim($fields[3]),
					"rank" => trim($fields[5]),
					"fake_phone" => trim($fields[6]),
					"fake_mobile" => trim($fields[7]),
					"fake_fax" => trim($fields[8]),
					"fake_email" => trim($fields[9]),
					"comment" => trim($fields[10]),
				);
			}
		}

		$xml = new SimpleXMLElement("<?xml version='1.0'?><persons></persons>");
		foreach($customers as $cust)
		{
			if (trim($cust["name"]) == "")
			{
				continue;
			}
			$cat = $xml->addChild("person");
			$cat->addChild("name", $this->_t($cust["name"]));
			$cat->addChild("firstname", $this->_t($cust["first_name"]));
			$cat->addChild("lastname", $this->_t($cust["last_name"]));
			$cat->addChild("external_id", $this->_t($cust["extern_id"]));
			$cat->addChild("company_external_id", $this->_t($cust["extern_customer_id"]));

			$cat->addChild("rank", $this->_t($cust["rank"]));
			$cat->addChild("fake_email", $this->_t($cust["fake_email"]));
			$cat->addChild("fake_phone", $this->_t($cust["fake_phone"]));
			$cat->addChild("fake_mobile", $this->_t($cust["fake_mobile"]));
			$cat->addChild("fake_fax", $this->_t($cust["fake_fax"]));
			$cat->addChild("comment", $this->_t($cust["comment"]));
		}
		return $xml->asXML();
	}

	function get_user_list_xml()
	{

	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->import_users(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	/**
		@attrib name=import_users api=1
	**/
	function import_users($arr)
	{
		if(!$this->can('add',aw_ini_get('taket.users_parent')))
		{
			print "ei saa lisada:". aw_ini_get('taket.users_parent');
		//	$this->acl_error();
			die();
		}
		include('IXR_Library.inc.php');
		$this->read_template('import.tpl');	
		$this->sub_merge=1;
		$inst = get_instance("users");

		//change passwords
		if($arr['changed'])
		{
			foreach($arr['password'] as $key=>$value)
			{
				if(strlen($value)>=3 && $arr['username'][$key])
				{
					$row = $this->db_fetch_row('SELECT oid, uid, password FROM users WHERE uid= \''.$arr['username'][$key].'\'');
					if(is_array($row))
					{
						$o = obj($row["oid"]);
						$o->set_password($value);
						$o->save();
					}
				}
			}
		}
		
		$hosts = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get("taket.xmlrpcpath");
		$port = aw_ini_get("taket.xmlrpcport");
		if(!$hosts || !$path || !$port)
		{
			print "ini failis host voi path voi port m&auml;&auml;ramata<br>";
			print "n&auml;ide:<br>
			taket.xmlrpchost[0] = 88.196.208.74<br>
taket.xmlrpcpath[0] = /xmlrpc/index.php<br>
taket.xmlrpcport[0] = 8888<br>";
			die();
		}

		$client = new IXR_Client($hosts[0], $path[0],$port[0]);

		$client->query('server.getUsers',array());
		$dat123 = explode("</struct></value>
<value><struct>" , $client->message->message);

		$datx = array();
		foreach($dat123 as $d)
		{
			$rowdata = array();
			foreach(explode("\n" , $d) as $row)
			{
				$pos1 = strpos($row, '<name>');
				$pos2 = strpos($row, '</name>');
				$pos3 = strpos($row, '<string>');
				$pos4 = strpos($row, '</string>');
				$rowdata[substr($row , ($pos1 + 6) , $pos2 - $pos1 - 6)] = substr($row , ($pos3 + 8) , $pos4 - $pos3 - 8);
			}

			$datx[] = $rowdata;
		}

		$data=$client->getResponse();

		//kuna get_response ei anna antud juhul midagi, siis tegi "k2sitsi" 2ra asja
		$data = $datx;

		$us = new user();
		$gr = get_instance(CL_GROUP);

		foreach($data as $value)
		{
			$value['kasutajanimi']=$value['number'];
			$value['password']=$value['number'];
			
			//delete weirdass old juusers
			if(strlen($value['kasutajanimi'])<3 || strlen($value['password'])<3)
			{
				continue;
			}
			
			if(strlen($value['kasutajanimi'])==1)
			{
				$value['kasutajanimi']='00'.$value['kasutajanimi'];
			}
			else if(strlen($value['kasutajanimi'])==2)
			{
				$value['kasutajanimi']='0'.$value['kasutajanimi'];
			}

			if(strlen($value['password'])==1)
			{
				$value['password']='00'.$value['password'];
			}
			else if(strlen($value['password'])==2)
			{
				$value['password']='0'.$value['password'];
			}
			
				
			$row = $this->db_fetch_row('SELECT uid, password FROM users WHERE uid= \''.$value['kasutajanimi'].'\'');

			if(!is_array($row))
			{
				$user_o = $us->add_user(array(
					"uid" => $value['kasutajanimi'],
					"password" => $value['password'],
					'email' => $value['email'],
				));

				$gr->add_user_to_group($user_o, obj(9));
				$value['password']=md5($value['password']);
			}
			else
			{
				$value['password'] = $row['password'];
			}
			$this->vars($value);	
			$this->parse('klient');	
		}
		$this->vars(array(
			'reforb' => $this->mk_reforb('import_users',array(
				'changed' => 1,
				'no_reforb' => true
				),
				'taket_users_import')
		));
		echo $this->parse();
		die();
	}
	
	//sounds nice, aint so nice
	function generateUserName($arr)
	{
		$rtrn=str_replace(' ','',$arr['firmanimi']);
		$rtrn=str_replace('	','',$rtrn);
		$rtrn=substr($rtrn,0,8);
		return $rtrn;
	}
	
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		$this->import_users($arr);
		return $this->parse();
	}

	function update_user_info($arr)
	{
		include('IXR_Library.inc.php');
		$user_id=users::get_oid_for_uid($arr['uid']);
		//tirib infi windooza servust kasutaja kohta
		//kui juhuslikult on mingi v2li muutunud
		$host = aw_ini_get('taket.xmlrpchost');
		$path = aw_ini_get('taket.xmlrpcpath');
		$port = aw_ini_get('taket.xmlrpcport');
		$client = new IXR_Client($host[0], $path[0], $port[0]);
		//username is the user_id in taket database@windoooza
		$client->query('server.getUsers',array('user_id'=>$arr['uid']));
		$data=$client->getResponse();
		
		$data[0]['eesnimi'] = urldecode($data[0]['eesnimi']);
		$data[0]['perenimi'] = urldecode($data[0]['perenimi']);
		$data[0]['firmanimi'] = urldecode($data[0]['firmanimi']);

		$_SESSION['TAKET']=$data[0];
		$_SESSION['TAKET']['eesperenimi'] = $_SESSION['TAKET']['eesnimi'].' '.$_SESSION['TAKET']['perenimi'];
		//initsialiseerima juba ka selle ebasketi listi
		$_SESSION['TAKET']['ebasket_list'] = array();
		//
		classload('taket/taket_ebasket');
		$ebasket = new taket_ebasket();
		$ebasket = $ebasket->get_users_active_ebasket(aw_global_get('uid'));
		if($ebasket!=null)
		{
	      $ol = new object_list(array(
                  'parent'=>$ebasket->ebasket_item_parent_id,
                  'class_id' => CL_TAKET_EBASKET_ITEM,
                  'ebasket_id' => $ebasket->id(),
                  'lang_id' => array(),
               ));

	      $grouped_basket_items = array();

			for($o=$ol->begin();!$ol->end();$o=$ol->next())
			{
				if(!$o->prop('ebasket_id'))
					continue;
				$ebasket_name = $o->prop('ebasket_name');
				if(!strlen($ebasket_name))
				{
					$ebasket_name = $this->current_ebasket_identificator;
				}
				if(!array_key_exists($ebasket_name,$grouped_basket_items))
				{
					$grouped_basket_items[$ebasket_name] = array();
				}
			}
			$_SESSION['TAKET']['ebasket_list'] = array_keys($grouped_basket_items);
		}
	}
}
?>

<?php

class ml_list_obj extends _int_object
{
	function set_name($v)
	{
		return parent::set_name($v);
	}

	/** Returns mailinglist member sources
		@attrib api=1
		@returns object list
	**/
	public function get_sources()
	{
		$ol = new object_list();
		foreach($this->connections_from(array("type" => "RELTYPE_MEMBER_PARENT")) as $c)
		{
			$ol->add($c->prop("to"));
		}
		return $ol;
	}

	/** Returns all menu sources
		@attrib api=1
		@returns object list
	**/
	public function get_menu_sources()
	{
		$ol = new object_list();
		$souces = array();
		$sources_data = $this->get_sources_data();

		foreach($this->connections_from(array("type" => "RELTYPE_MEMBER_PARENT")) as $c)
		{
			if($c->prop("to.class_id") == CL_MENU)
			{
				$souces[] = $c->prop("to");
			}
		}
		$sources = $this->add_minions($souces);
		$ol->add($sources);
		return $ol;
	}

	/** Returns data about mailinglist sources
		@attrib api=1
		@returns array
	**/
	public function get_sources_data()
	{
		return $this->meta("sources_data");
	}

	public function add_minions($src = array())
	{
		$sub = array();
		$minion_classes = array(CL_MENU , CL_CRM_SECTOR);
		$sources_data = $this->get_sources_data();
		foreach($src as $menu)
		{
			if(!$GLOBALS["object_loader"]->cache->can("view" , $menu))
			{
				continue;
			}
			$o = obj($menu);

			if(in_array($o->class_id() , $minion_classes) && is_array($sources_data) && isset($sources_data[$o->id()]["use_minions"]) &&  $sources_data[$o->id()]["use_minions"])

			{
				$ol = new object_list(array(
					"class_id" => $minion_classes,
					"lang_id" => array(),
					"parent" => $menu,
				));

				foreach($this->add_minions($ol->ids()) as $submenu)
				{
					$sub[] = $submenu;
				}
			}
		}
		foreach($sub as $submenu)
		{
			$src[] = $submenu;
		}
		return $src;
	}

	/** Saves data about mailinglist sources
		@attrib api=1
		@param data optional type=array
	**/
	public function set_sources_data($data)
	{
		$this->set_meta("sources_data" , $data);
	}

	/**
	@attrib api=1 params=name
	@param all optional type=int
		if 1, then return all member dublicates also
	@param sources optional type=array
		List member source, object id's
	@param name optional type=string
		Mailinglist member name for search
	@param mail optional type=string
		Mailinglist member e-mail address for search
	@param from optional type=int
	@param to optional type=int
	
	@returns array
	@comment
		if the source is file, then parent_name is set
		else oid is set
	@examples
		$members = $ml_list_object->get_members();
		//members = Array(
			[0] => Array(
				[parent] => 7375
				[name] => keegi
				[mail] => keegi@normaalne.ee
				[parent_name] => mailinglist.txt
			)
			[1] => Array(
				[oid] => 7500
				[parent] => 580
				[name] => inimene
				[mail] => inimene@mail.ee
		))
	**/
	public function get_members($arr = array())
	{
		$list = get_instance(CL_ML_LIST);
		$ml_list_members = $list->get_members(array(
			"src"	=> $arr["sources"],
			"all"	=> $arr["all"],
			"id" => $this->id(),
			"from"	=> $arr["from"],
			"to"	=> $arr["to"],
		));

		if(strlen($arr["name"]) > 1 || strlen($arr["mail"]) > 1)
		{
			foreach($ml_list_members as $key => $val)
			{
				if((strlen($arr["name"]) > 1) && (substr_count(strtolower($val["name"]), strtolower($arr["name"])) < 1))
				{
					unset($ml_list_members[$key]);
					continue;
				}
					
				if((strlen($arr["mail"]) > 1) && (substr_count($val["mail"], $arr["mail"]) < 1)) 
				{
					unset($ml_list_members[$key]);
				}
			}
		}
		return $ml_list_members;
	}

	private function get_menu_members($id)
	{
		$member_list = array();
		$mem_list = new object_list(array(
			"parent" => $id,
			"class_id" => CL_ML_MEMBER,
		));
		foreach($mem_list->arr() as $mem)
		{
			$member_list[$mem->prop("name")] = $mem->prop("mail");
		}
		return $member_list;
	}

	// !Imports members from a text file / or text block
	// text(string) - member list, comma separated
	// list_id(id) - which list?
	/** subscribes members to list
	@attrib api=1 params=name
	@param menus optional type=array
		menus to subscribe to
	@param text optional type=string
		new members (member , member@asd.com \n member2 , member2@asdasd.net)
	@param debug optional type=boolean
		debugs subscribing info
	@returns boolean
		1 if subscribing successful, else 0
	@examples
		$menus = $ml_list_object->get_menu_sources();
		$text = "name , name@mail.ee
			name2, name2@mail.ee"
		if(!$ml_list_object->mass_subscribe(array(
			"menus" => $menus,
			"text" => $text,	
		)))
		{
			die("damn");
		}
	**/
	public function mass_subscribe($arr)
	{
		aw_global_set("no_cache_flush", 1);
		aw_set_exec_time(AW_LONG_PROCESS);
		$log = array();
		$ml_member = get_instance(CL_ML_MEMBER);
		$lines = explode("\n", $arr["text"]);
		$debug = $arr["debug"];
		$menus = $arr["menus"];

		if(!($menus && sizeof($menus)))
		{
			return 0;
		}

		foreach($menus as $fold)
		{
			if(!is_oid($fold) || !$GLOBALS["object_loader"]->cache->can("add", $fold))
			{
				continue;
			}
			$fld_obj = new object($fold);

			if($fld_obj->class_id() != CL_MENU)
			{
				$log[]= sprintf(t("Liituda saab ainult kaustadesse - %s ei ole kaust"), $fld_obj->name());
				if($debug) print sprintf(t("Liituda saab ainult kaustadesse - %s ei ole kaust"), $fld_obj->name())."<br />";
				continue;
			}

			$members = $this->get_menu_members($fold);
			$log[]= sprintf(t("Impordin kasutajaid kataloogi %s / %s"),$fold , $fld_obj->name());
			if($debug) print sprintf(t("Impordin kasutajaid kataloogi %s / %s"),$fold , $fld_obj->name())."<br />";
			$cnt = 0;

			if (sizeof($lines) > 0)
			{
				foreach($lines as $line)
				{
					$line = trim($line);
					if (strlen($line) == 0)
					{
						continue;
					}
					if (strpos($line,",") !== false)
					{
						list($name,$addr) = explode(",", $line);
					}
					elseif (strpos($line,";") !== false)
					{
						list($name,$addr) = explode(";",$line);
					}
					else
					{
						$name = "";
						$addr = $line;
					}
					$name = trim($name);
					$addr = trim($addr);
	
					if (is_email($addr) && !in_array($addr, $members))
					{
						$log[]= sprintf(t("OK - nimi: %s , aadress:  %s"),$name , $addr);
						if($debug) print sprintf(t("OK - nimi: %s , aadress:  %s"),$name , $addr)."<br />";
						$cnt++;
						$retval = $ml_member->subscribe_member_to_list(array(
							"name" => $name,
							"email" => $addr,
							"list_id" => $this->id(),
							"use_folders" => $fold,
							"confirm_subscribe" => $this->prop("confirm_subscribe"),
							"confirm_message" => $this->prop("confirm_subscribe_msg"),
						));
						$members[] = $addr;
					}
					elseif(in_array($addr, $members))
					{
						$log[]= sprintf(t("Juba olemas listis - nimi: %s , aadress:  %s"),$name , $addr);
						if($debug) print "Juba olemas listis - nimi: $name, aadress: $addr<br />";
						flush();
					}
					else
					{
						$log[]= sprintf(t("Vale aadress - nimi: %s , aadress:  %s"),$name , $addr);
						if($debug) print "Vale aadress - nimi: $name, aadress: $addr<br />";
						flush();
					}
				}
				$c = get_instance("cache");
				$c->file_clear_pt("menu_area_cache");
				$c->file_clear_pt("storage_search");
				$c->file_clear_pt("storage_object_data");
			}
			$log[]= sprintf(t("Importisin %s aadressi"),$cnt);
			if($debug) print "Importisin $cnt aadressi<br>";
		}
		return true;
	}
	
	/** subscribes members to list
	@attrib api=1 params=name
	@param menus optional type=array
		menus to subscribe to
	@param text optional type=string
		members to delete (member@asd.com \n member2@asdasd.net)
	@param debug optional type=boolean
		debugs subscribing info
	@returns boolean
		1 if subscribing successful, else 0
	@examples
		$menus = $ml_list_object->get_menu_sources();
		$text = "name@mail.ee
			name2@mail.ee"
		if(!$ml_list_object->mass_unsubscribe(array(
			"menus" => $menus,
			"text" => $text,	
		)))
		{
			die("damn");
		}
	**/
	public function mass_unsubscribe($arr)
	{
		aw_global_set("no_cache_flush", 1);
		aw_set_exec_time(AW_LONG_PROCESS);
		$log = array();
		$ml_member = get_instance(CL_ML_MEMBER);
		$lines = explode("\n", $arr["text"]);
		$debug = $arr["debug"];
		$menus = $arr["menus"];

		if(!($menus && sizeof($menus)))
		{
			return 0;
		}

		foreach($menus as $fold)
		{
			if(!is_oid($fold) || !$GLOBALS["object_loader"]->cache->can("add", $fold))
			{
				continue;
			}
			$fld_obj = new object($fold);

			if($fld_obj->class_id() != CL_MENU)
			{
				$log[]= sprintf(t("Lahkuda saab ainult kaustadest - %s ei ole kaust"), $fld_obj->name());
				if($debug) print sprintf(t("Lahkuda saab ainult kaustadest - %s ei ole kaust"), $fld_obj->name())."<br />";
				continue;
			}

			$log[]= sprintf(t("Kustutan kasutajad kataloogist %s / %s "),$fold , $fld_obj->name());
			if($debug) print sprintf(t("Kustutan kasutajad kataloogist %s / %s"),$fold , $fld_obj->name())."<br />";

			$cnt = 0;
			if (sizeof($lines) > 0)
			{
				foreach($lines as $line)
				{
					if (strlen($line) < 5)
					{
						continue;
					}
					// no, this is different, no explode. I need to extract an email address from the
					// line
					preg_match("/(\S*@\S*)/",$line,$m);
					$addr = $m[1];
					if (is_email($addr))
					{
						$retval = $ml_member->unsubscribe_member_from_list(array(
							"email" => $addr,
							"list_id" => $this->id(),
							"ret_status" => true,
							"use_folders" => $fold,
						));
						if ($retval)
						{
							$cnt++;
							if($debug) print "OK a:$addr<br />";
						}
						else
						{
							if($debug) print "Ei leidnud $addr<br />";
						}
						flush();
						usleep(500000);
					}
					else
					{
						print "IGN - a:$addr<br />";
						flush();
					}
				}
			}
			$log[]= sprintf(t("Kustutasin %s aadressi"),$cnt);
			if($debug) print "Kustutasin $cnt aadressi<br>";
		}
		return true;
	}

	//teeb fck igasugu urlid mailile kohaseks, et mujal maailmas ka aru saaks kuhu asi viitab
	function make_fck_urls_good($msg)
	{
		$x = 0;
		$regs = array();
		if ($asd = preg_match_all("/\/[0-9]+/", $msg, $regs)) 
		{
			if($x > 100) break;
			foreach($regs[0] as $reg)
			{
				$reg = substr($reg , 1);
				if($GLOBALS["object_loader"]->cache->can("view" , $reg))
				{
					$link = obj($reg);
					if($link->class_id() == CL_EXTLINK)
					{
						$msg = str_replace('href="/'.$link->id().'"', 'href="'.$link->prop("url").'"' , $msg);
						$msg = str_replace("href='/".$link->id()."'", "href='".$link->prop("url")."'" , $msg);
						$msg = str_replace('href="'.aw_ini_get("baseurl").'/'.$link->id().'"', 'href="'.$link->prop("url").'"' , $msg);
						$msg = str_replace("href='".aw_ini_get("baseurl")."/".$link->id()."'", 'href="'.$link->prop("url").'"' , $msg);
						continue;
					}
				}
			}
			if($reg > 1)
			{
				$msg = str_replace('href="/'.$reg.'"', 'href="'.aw_ini_get("baseurl").'/'.$reg.'"' , $msg);
				$msg = str_replace("href='/".$reg."'", "href='".aw_ini_get("baseurl")."/".$reg."'" , $msg);
			}
 			$x++;
		}

		$classes = aw_ini_get("classes");
		list($astr) = explode(",", $classes[CL_IMAGE]["alias"]);
		if ($astr == "")
		{
			list($astr) = explode(",", $classes[CL_IMAGE]["old_alias"]);
		}

		foreach($this->connections_from(array("reltype" => 0,
 			 "to.class_id" => 6)) as $c)
		{
			$pict = $c->to();
			$alias = sprintf("#%s%d#", $astr, $c->prop("idx"));
			if(substr_count($msg , $alias))
			{
				$msg = str_replace($alias, $pict->get_html(), $msg);
			}
		}
		


		return $msg;
	}

}

?>

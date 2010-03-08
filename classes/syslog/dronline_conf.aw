<?php
// $Header: /home/cvs/automatweb_dev/classes/syslog/dronline_conf.aw,v 1.22 2008/01/31 13:55:30 kristo Exp $
/*
@classinfo  maintainer=kristo

@default table=objects
@default field=meta
@default method=serialize
@default group=general

@property from type=date_select 
@caption Alates

@property to type=date_select 
@caption Kuni

@property user type=select
@caption kasutaja

@property address type=textbox
@caption IP Aadress

@property email_uid type=textbox
@caption Listi UID

@property email_email type=textbox
@caption Listi email

@property textfilter type=textbox
@caption Mida tegi 

@property numlines type=textbox size=4
@caption Mitu rida

@property sites type=select multiple=1 size=4
@caption Saidid

@property languages type=select multiple=1 size=4
@caption Keeled

@property use_filter type=checkbox ch_value=1
@caption Kas kasutada Tegevuste filtrit

@property filter_types type=callback callback=get_filter_types group=types
@caption T&uuml;&uuml;bid

@property filter_actions type=callback callback=get_filter_actions group=actions
@caption Tegevused

@property filter_combo type=callback callback=get_filter_combo group=combo
@caption Tegevused

@property ip_save_folder type=objpicker clid=CL_MENU group=ipfilter
@caption Kataloog, kuhu salvestatakse IP aadressid

@property ip_block_folders type=objpicker clid=CL_MENU multiple=1 group=ipfilter
@caption Blokeeritavate aadresside kataloogid

@property ip_block_folders_subs type=checkbox ch_value=1 group=ipfilter
@caption Kas kehtestada ka alamkaustadele

@property ip_allow_folders type=objpicker clid=CL_MENU multiple=1 group=ipfilter
@caption N&auml;idatavate aadresside kataloogid

@property ip_allow_folders_subs type=checkbox ch_value=1 group=ipfilter
@caption Kas n&auml;idata ka alamkataloogid

@groupinfo types caption=T&uuml;&uuml;bid
@groupinfo actions caption=Tegevused
@groupinfo combo caption=Kombineeritud
@groupinfo ipfilter caption=IPfilter

*/

class dronline_conf extends class_base
{
	function dronline_conf()
	{
		$this->init(array(
			'tpldir' => 'syslog/dronline_conf',
			'clid' => CL_DRONLINE_CONF
		));
	}

	function get_property(&$arr)
	{
		$prop = &$arr['prop'];

		if ($prop['name'] == 'user')
		{
			$ol = new object_list(array(
				"class_id" => CL_USER,
				"site_id" => array(),
				"lang_id" => array()
			));
			$prop["options"] = array("" => "");	
			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$prop["options"][$o->prop("uid")] = $o->prop("uid");
			}
		}
		else
		if ($prop['name'] == 'from' && $arr['new'])
		{
			//$prop['value'] = mktime(0,0,0,1,1,date("Y"));
		}
		else
		if ($prop['name'] == 'to' && !$arr['new'])
		{
			//$prop['value'] = time();
		}
		else
		if (($prop['name'] == 'name' || $prop['name'] == 'comment' || $prop['name'] == 'status') && $this->embedded == true)
		{
			return PROP_IGNORE;
		}
		else
		if ($prop['name'] == 'sites')
		{
			if (!aw_ini_get("syslog.has_site_id"))
			{
				return PROP_IGNORE;
			}
			$sl = get_instance("install/site_list");

			$opts = array('' => 'K&otilde;ik saidid');
			$this->db_query("SELECT distinct(site_id) as site_id FROM syslog");
			while ($row = $this->db_next())
			{
				if ($row['site_id'])
				{
					$opts[$row['site_id']] = $sl->get_url_for_site($row['site_id']);
				}
			}
			$prop['options'] = $opts;
		}
		else
		if ($prop['name'] == 'languages')
		{
			if (!aw_ini_get("syslog.has_lang_id"))
			{
				return PROP_IGNORE;
			}
			$opts = array('' => 'K&otilde;ik keeled');
			$la = get_instance("languages");
			$prop['options'] = $la->get_list();
		}

		return PROP_OK;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "filter_types":
				$tps = aw_ini_get("syslog.types");
				foreach($tps as $tpid => $tpd)
				{
					$rt = 'slt_'.$tpid;
					$arr["obj_inst"]->set_meta($rt,isset($arr["request"][$rt]) ? $arr["request"][$rt] : "");
				};
				break;

			case "filter_actions":
				$tps = aw_ini_get("syslog.actions");
				foreach($tps as $tpid => $tpd)
				{
					$rt = 'sla_'.$tpid;
					$arr["obj_inst"]->set_meta($rt,isset($arr["request"][$rt]) ? $arr["request"][$rt] : "");
				};
				break;
			

			case "filter_combo":
				$tps = aw_ini_get("syslog.types");
				$acts = aw_ini_get("syslog.actions");
				foreach($tps as $tpid => $tpd)
				{
					foreach($acts as $acid => $acd)
					{
						// check if this action applies for this type
						$tlist = explode(",", $acd['types']);
						if (in_array($tpid, $tlist))
						{
							$rt = 'slc_'.$tpid.'_'.$acid;
							$arr["obj_inst"]->set_meta($rt,isset($arr["request"][$rt]) ? $arr["request"][$rt] : "");
						};
					};
				};
				break;
		};
		return $retval;
	}


	function get_filter_types($arr)
	{
		// get list of types in syslog
		$types = array();
		$this->db_query("SELECT distinct(type) as t FROM syslog");
		while ($row = $this->db_next())
		{
			$types[$row["t"]] = $row["t"];
		}

		$acts = array();
		$values = $arr["obj_inst"]->meta();
		$tps = aw_ini_get("syslog.types");
		uasort($tps, array(&$this, "__nm_srt"));
		foreach($tps as $tpid => $tpd)
		{
			if (!isset($types[$tpid]))
			{
				continue;
			}

			$rt = 'slt_'.$tpid;

			$acts[$rt] = array(
				'name' => $rt,
				'caption' => $tpd['name'],
				'type' => 'checkbox',
				'ch_value' => 1,
				'value' => $values[$rt],
				'group' => $arr["prop"]["group"],
			);
		}

		return $acts;
	}

	function __nm_srt($a, $b)
	{
		if ($a["name"] == $b["name"])
		{	
			return 0;
		}
		return strcmp($a["name"], $b["name"]);
	}

	function get_filter_actions($arr)
	{
		$acts = array();
		$tps = aw_ini_get("syslog.actions");
		$values = $arr["obj_inst"]->meta();
		foreach($tps as $tpid => $tpd)
		{
			$rt = 'sla_'.$tpid;

			$acts[$rt] = array(
				'name' => $rt,
				'caption' => $tpd['name'],
				'type' => 'checkbox',
				'ch_value' => 1,
				'group' => $arr["prop"]["group"],
				'value' => $values[$rt],
			);
		}

		return $acts;
	}

	function get_filter_combo($arr)
	{
		// get list of types in syslog
		$types = array();
		$this->db_query("SELECT distinct(type) as t FROM syslog");
		while ($row = $this->db_next())
		{
			$types[$row["t"]] = $row["t"];
		}

		$acts = array();
		$values = $arr["obj_inst"]->meta();
		$tps = aw_ini_get("syslog.types");
		$acts = aw_ini_get("syslog.actions");
		uasort($tps, array(&$this, "__nm_srt"));
		foreach($tps as $tpid => $tpd)
		{
			if (!isset($types[$tpid]))
			{
				continue;
			}

			// add type separator
			$rt = 'slc_sep_'.$tpid;
			$acts[$rt] = array(
				'name' => $rt,
				'caption' => "<b>".$tpd['name']."</b>",
				'group' => 'combo'
			);
			foreach($acts as $acid => $acd)
			{
				// check if this action applies for this type
				$tlist = explode(",", $acd['types']);
				if (in_array($tpid, $tlist) || $acid == SA_DELETE)
				{
					$rt = 'slc_'.$tpid.'_'.$acid;
					$acts[$rt] = array(
						'name' => $rt,
						'caption' => $acd['name'],
						'type' => 'checkbox',
						'ch_value' => 1,
						'value' => $values[$rt],
						'group' => $arr["prop"]["group"],
					);
				}
			}
		}

		return $acts;
	}

	function callback_mod_tab($arr)
	{
		if ($this->embedded)
		{
			if ($arr['id'] == 'ipfilter')
			{
				return false;
			}
			$uri = aw_global_get("REQUEST_URI");
			$uri = preg_replace("/dro_c_tab=[^&$]*/","",$uri);
			$uri = preg_replace("/&{2,}/","&",$uri);
			$uri .= "&dro_c_tab=".$arr['id'];
			$arr['link'] = $uri;
		}
		return true;
	}

	/*function submit($arr)
	{
		$ret = parent::submit($arr);
		if ($arr['extraids']['ret_url'] != "")
		{
			$ret = $arr['extraids']['ret_url'];
		}
		return $ret;
	}

	function change($arr)
	{
		if (aw_global_get("action") != "new")
		{
			$arr['extraids']['ret_url'] = aw_global_get("REQUEST_URI");
		};
		return parent::change($arr);
	}*/

	////
	// !creates a clone of object $from under folder $parent with name $name
	function clone_object($arr)
	{
		extract($arr);

		$old = obj($from);

		$new = new object();
		$new->set_parent($parent);
		$new->set_class_id(CL_DRONLINE_CONF);
		$new->set_name($name);
		foreach($old->meta() as $k => $v)
		{
			$new->set_meta($k, $v);
		}

		return $new->save();
	}

}
?>

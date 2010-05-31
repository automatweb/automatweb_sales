<?php

namespace automatweb;
// crm_skill_level.aw - Oskuse tase
/*

@classinfo syslog_type=ST_CRM_SKILL_LEVEL relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo crm_skill_level index=aw_oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property skill type=relpicker reltype=RELTYPE_SKILL store=connect no_edit=1 automatic=1
@caption Oskus

@property level type=relpicker reltype=RELTYPE_LEVEL store=connect no_edit=1 
@caption Tase

@property other type=textbox field=comment
@caption Muu

@property years_of_practice type=textbox size=4 table=crm_skill_level
@caption Kogemus aastates

@property year_of_last_usage type=textbox size=4 table=crm_skill_level
@caption Viimati kasutatud aastal

@property addinfo type=textbox table=crm_skill_level
@caption Lisainfo

@reltype SKILL value=1 clid=CL_CRM_SKILL
@caption Oskus

@reltype LEVEL value=2 clid=CL_META
@caption Oskuse tase

*/

class crm_skill_level extends class_base
{
	const AW_CLID = 1401;

	function crm_skill_level()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_skill_level",
			"clid" => CL_CRM_SKILL_LEVEL
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "skill":
				$by_parent = array();
				foreach($prop["options"] as $opt_id => $opt_capt)
				{
					if (!is_oid($opt_id))
					{
						continue;
					}
					$val = $this->_get_level_in_opts($opt_id, $prop["options"]);
					if ($val == 0)
					{
						$by_parent[0][] = $opt_id;
					}
					else
					{
						$tmp = obj($opt_id);
						$by_parent[$tmp->parent()][] = $opt_id;
					}
				}
				$this->ord_skills($by_parent);
				$this->get_other_opts($by_parent);

				$prop["options"] = array();
				$prop["options"][""] = t("--vali--");
				$prop["disabled_options"] = array();
				$this->_format_opts($prop["options"], 0, $by_parent, $prop["disabled_options"]);

				if (preg_match("/skills_releditor(\d)/imsU", $arr["name_prefix"], $mt))
				{
					// list only items under top-level items with jrk no 1
					$this->_filter_opts_by_level_jrk($prop["options"], $mt[1]);
				}
				$other_ids = $this->other_ids($prop["options"]);
				if(count($other_ids) > 0)
				{
					$vals = "";
					foreach($other_ids as $other_id)
					{
						$vals .= (strlen($vals) > 0) ? " || " : "";
						$vals .= "this.value == '".$other_id."'";
					}
					$prop["onchange"] = "var id = this.id.replace('_skill_', '_other_'); if(".$vals.") { $('#' + id).parent().parent().show(); } else { $('#' + id).parent().parent().hide(); }";
				}
				//$this->ord_skills($prop["options"]);
				break;

			case "level":
				$prop["options"][0] = t("--vali--");
				if(is_oid($arr["obj_inst"]->prop("skill")))
				{
					$skill_id = $arr["obj_inst"]->prop("skill");
				}
				else
				{
					$ol = new object_list(array("class_id" => CL_CRM_SKILL, "lang_id" => array(), "site_id" => array()));
					if ($ol->count())
					{
						foreach($ol->arr() as $tmp)
						{
							if ($tmp->prop("lvl_meta") > 0)
							{
								$skill_id = $tmp->id();
							}
						}
					}
				}
				
				if ($this->can("view", $skill_id))
				{
					$skill_obj = obj($skill_id);
					if(is_oid($skill_obj->prop("lvl_meta")))
					{
						$ol = new object_list(array(
							"class_id" => CL_META,
							"parent" => $skill_obj->prop("lvl_meta"),
							"lang_id" => array(),
							"site_id" => array(),
							"status" => object::STAT_ACTIVE,
							"sort_by" => "jrk",
						));
						$objs = $ol->arr();
						enter_function("uasort");
						uasort($objs, array(get_instance(CL_PERSONNEL_MANAGEMENT, "cmp_function")));
						exit_function("uasort");
						foreach($objs as $o)
						{
							$prop["options"][$o->id()] = $o->trans_get_val("name");
						}
						//$prop["options"] += $ol->names();
					}
				}
				break;
		}

		return $retval;
	}

	function _filter_opts_by_level_jrk(&$opts, $jrk)
	{
		foreach($opts as $k => $v)
		{
			if (!is_oid($k))
			{
				continue;
			}
			$tmp = obj($k);
			if ($this->_get_level_in_opts($k, $opts) == 0 && $tmp->ord() == $jrk)
			{
				$filter_opt = $k;
			}
		}
		if ($filter_opt)
		{
			foreach($opts as $k => $v)
			{
				if(substr($k, 0, 6) == "other_" && !$this->_opt_is_below(substr($k, 6), $filter_opt, $opts) && substr($k, 6) != $filter_opt)
				{
					unset($opts[$k]);
				}
				else
				if (!is_oid($k))
				{
					continue;
				}
				else
				if ($k == $filter_opt || !$this->_opt_is_below($k, $filter_opt, $opts))
				{
					unset($opts[$k]);
				}
			}
		}
		else
		{
			$opts = array("" => t("--vali--"));
		}
	}

	private function _opt_is_below($opt, $filter_opt, $opts)
	{
		$o = obj($opt);
		foreach($o->path() as $path_item)
		{
			if ($path_item->id() == $filter_opt)
			{
				return true;
			}
		}
		return false;
	}

	function _format_opts(&$opts, $parent, $by_parent, &$disabled_opts)
	{
		$this->level++;
		$cnt = 0;
		foreach($by_parent[$parent] as $opt_id)
		{
			if(substr($opt_id, 0, 6) == "other_")
			{
				$tmp = obj($parent);
				$opts[$opt_id] = str_repeat("&nbsp;&nbsp;", $this->level-1).$tmp->trans_get_val("other");
			}
			else
			{
				$tmp = obj($opt_id);
				$opts[$opt_id] = str_repeat("&nbsp;&nbsp;", $this->level-1).$tmp->trans_get_val("name");
				if ($this->_format_opts($opts, $opt_id, $by_parent, $disabled_opts) != 0)
				{
					$disabled_opts[$opt_id] = $opt_id;
				}
			}
			$cnt++;
		}
		$this->level--;
		return $cnt;
	}

	private function _get_level_in_opts($opt_id, $opts)
	{
		if ($this->can("view", $opt_id))
		{
			$o = obj($opt_id);
			if (isset($opts[$o->parent()]))
			{
				return $this->_get_level_in_opts($o->parent(), $opts)+1;
			}
		}
		return 0;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	private function ord_skills(&$arr)
	{
		foreach($arr as $k => $v)
		{
			usort($arr[$k], array($this, "cmp_function"));
		}
	}

	private function cmp_function($a, $b)
	{
		$a_obj = obj($a);
		$b_obj = obj($b);
		return get_instance(CL_PERSONNEL_MANAGEMENT)->cmp_function($a_obj, $b_obj);
	}

	private function get_other_opts(&$opts)
	{
		foreach(array_keys($opts) as $oid)
		{
			if(!is_oid($oid))
			{
				continue;
			}
			$o = obj($oid);
			if($o->trans_get_val("other"))
			{
				$opts[$oid][] = "other_".$oid;
			}
		}
	}

	private function other_ids($opts)
	{
		$ret = array();
		foreach(array_keys($opts) as $k)
		{
			if(substr($k, 0, 6) == "other_")
			{
				$ret[] = $k;
			}
		}
		return $ret;
	}
	
	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "crm_skill_level" && $field == "")
		{
			$this->db_query("create table crm_skill_level (aw_oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "years_of_practice":
			case "year_of_last_usage":
			case "addinfo":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text"
				));
				return true;
		}

		return false;
	}
}

?>

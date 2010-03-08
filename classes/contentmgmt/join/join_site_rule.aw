<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/join/join_site_rule.aw,v 1.8 2008/01/31 13:52:32 kristo Exp $
// join_site_rule.aw - Liitumise Reegel 
/*

@classinfo syslog_type=ST_JOIN_SITE_RULE relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property join_conf type=relpicker reltype=RELTYPE_JOIN_CONF field=meta method=serialize 
@caption Liitumise konff

@property rules_add type=text store=no 
@caption Reegel

@property rule_to_grp type=objpicker clid=CL_GROUP field=meta method=serialize
@caption Vali grupp, kuhu reegel kasutaja paneb

@reltype JOIN_CONF value=1 clid=CL_JOIN_SITE
@caption liitumise konfiguratsioon
*/

class join_site_rule extends class_base
{
	function join_site_rule()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/join/join_site_rule",
			"clid" => CL_JOIN_SITE_RULE
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "rules_add":
				if (is_oid($arr["obj_inst"]->id()))
				{
					$data["value"] = $this->_do_change_rule($arr);
				}
				break;
		};
		return $retval;
	}

	function _do_change_rule($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->prop("join_conf")))
		{
			return "";
		}
		$js = get_instance(CL_JOIN_SITE);

		$js->read_template("add_rule.tpl");
		$js->vars(array(
			"form" => $js->get_form_from_obj(array(
				"id" => $arr["obj_inst"]->prop("join_conf"),
				"add_empty_vals" => true
			), $arr["obj_inst"]->meta("rule_data"))
		));
		return $js->parse();
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "rules_add":
				$data["value"] = $this->_do_save_rule($arr);
				break;
		}
		return $retval;
	}

	function _do_save_rule($arr)
	{
		$js = get_instance(CL_JOIN_SITE);
		$ruled = $js->_update_sess_data($arr["request"], true);
		$arr["obj_inst"]->set_meta("rule_data", $ruled);
	}

	/** tries to match the user $user's data to the rule $rule (storage objects)

		@returns true/false based on whether the rule matches or not
	**/
	function match_rule_to_user($rule, $user)
	{
		$rd = $rule->meta("rule_data");

		// get the person object from the user
		$personid = reset($user->connections_from(array("type" => "RELTYPE_PERSON" /* from user */)));
		$person = obj($personid->prop("to"));

		$match = true;

		// go over the rule data 
		$awrd = new aw_array($rd);
		foreach($awrd->get() as $tp => $d)
		{
			list(,$clid) = explode("_", $tp);

			if ($clid == CL_USER)
			{
				$data = $user;
			}
			else
			if ($clid == CL_CRM_PERSON)
			{
				$data = $person;
			}
			else
			{
				// now get the data object from the person obj
				$dataid = reset($person->connections_from(array("type" => "RELTYPE_USER_DATA" /* from crm_person */)));
				if ($dataid)
				{
					$data = obj($dataid->prop("to"));
				}
			}

			$awd = new aw_array($d);
			foreach($awd->get() as $propn => $propv)
			{
				if ($propv != "")
				{
					if ($propn == "uid_entry")
					{
						$propn = "uid";
					}

					if ($propv == "%")
					{
					}
					else
					if (strpos($propv, "%") !== false)
					{
						if (strpos($data->prop($propn), str_replace("%", "", $propv)) === false)
						{
							$match = false;
						}
					}
					else
					{
						if ($data->prop($propn) != $propv)
						{
							$match = false;
						}
					}
				}
			}
		}

		return $match;
	}
}
?>

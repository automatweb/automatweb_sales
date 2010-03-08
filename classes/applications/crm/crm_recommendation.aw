<?php
// crm_recommendation.aw - Soovitus
/*

@classinfo syslog_type=ST_CRM_RECOMMENDATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 no_name=1

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PERSON, on_connect_person_to_recommendation)

@default table=objects
@default group=general

@property person type=relpicker reltype=RELTYPE_PERSON store=connect field=meta method=serialize
@caption Soovitav isik

@property relation type=classificator reltype=RELTYPE_RELATION store=connect
@caption Suhe soovitajaga

@property phones type=textbox store=no
@caption Telefon

@property emails type=textbox store=no
@caption E-postiaadress

@property org type=textbox store=no
@caption T&ouml;&ouml;koht

@property profession type=textbox store=no
@caption Amet

@property contact_lang type=relpicker reltype=RELTYPE_CONTACT_LANGUAGE no_edit=1 store=connect
@caption Soovitaja poole p&ouml;&ouml;rduda

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption Soovitav isik

@reltype RELATION value=3 clid=CL_META
@caption Suhe soovitajaga

@reltype CONTACT_LANGUAGE value=4 clid=CL_LANGUAGE
@caption Soovitaja poole p&ouml;&ouml;rduda

*/

class crm_recommendation extends class_base
{
	function crm_recommendation()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_recommendation",
			"clid" => CL_CRM_RECOMMENDATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "contact_lang":
				$ol_prms = array(
					"class_id" => CL_LANGUAGE,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
				);
				$oid = get_instance(CL_PERSONNEL_MANAGEMENT)->get_rec_lang_conf();
				if(is_array($oid) && count($oid) > 0)
				{
					$ol_prms["oid"] = array_keys($oid);
				}
				$ol = new object_list($ol_prms);
				//$prop["options"] = $ol->names();
				foreach($ol->arr() as $o)
				{
					$prop["options"][$o->id()] = $o->trans_get_val("name");
				}
				break;

			case "phones":
			case "emails":
				if($this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$prop["type"] = "text";
					$p = obj($arr["obj_inst"]->prop("person"));
					$ol = $p->$prop["name"]();
					$val = "";
					foreach($ol->arr() as $o)
					{
						if(strlen($val) > 0)
						{
							$val .= ", ";
						}
						$c = ($prop["name"] == "phones") ? $o->name : $o->mail;
						$val .= html::obj_change_url($o, $c);
					}
					$prop["value"] = $val;
				}
				break;

			case "person":
				if(!$prop["value"])
				{
					$prop["post_append_text"] = "";
					$prop["type"] = "textbox";
					/*
					$prop["autocomplete_source"] = $this->mk_my_orb("autocomp");
					$prop["autocomplete_params"] = array();
					*/
				}
				break;

			case "org":
			case "profession":
				if($this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$prop["type"] = "text";
					$p = obj($arr["obj_inst"]->prop("person"));
					foreach($p->connections_from(array("type" => "RELTYPE_ORG_RELATION", "to.class_id" => CL_CRM_PERSON_WORK_RELATION)) as $cn)
					{
						$wr = $cn->to();
						$prop["value"] = html::obj_change_url($wr->prop($prop["name"]));
						break;
					}
				}
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "person":
				if(!is_oid($prop["value"]) && strlen($prop["value"]) > 0)
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"lang_id" => array(),
						"site_id" => array(),
						"parent" => array(),
					));
					$rev_nms = array_flip($ol->names());
					if(array_key_exists($prop["value"], $rev_nms))
					{
						$arr["obj_inst"]->set_prop("person", $rev_nms[$prop["value"]]);
					}
					else
					{
						$new_p = new object;
						$new_p->set_class_id(CL_CRM_PERSON);
						$new_p->set_parent($arr["obj_inst"]->parent());
						$new_p->set_name($prop["value"]);
						$new_p->save();
						$arr["obj_inst"]->set_prop("person", $new_p->id());
					}
					return PROP_IGNORE;
				}
				break;

			case "phones":
				$number = str_replace(array(" ", "-", "(", ")") , "", $arr["request"]["phones"]);
				if(strlen($number) > 0 && substr($arr["request"]["phones"], 0, 9) != "< a href=" && $this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$p_obj = obj($arr["obj_inst"]->prop("person"));
/*					$odl = new object_data_list(
						array(
							"class_id" => CL_CRM_PHONE,
							"lang_id" => array(),
							"site_id" => array(),
							"parent" => array(),
						),
						array(
							CL_CRM_PHONE => array("clean_number" => "number"),
						)
					);
					foreach($odl->arr() as $k => $v)
					{
						$nms[$v["number"]] = $k;
					}
					if(array_key_exists($number, $nms))
					{
						$p_obj->connect(array(
							"to" => $nms[$number],
							"reltype" => "RELTYPE_PHONE",
						));
					}
					else
					{*/
						$ph = new object;
						$ph->set_class_id(CL_CRM_PHONE);
						$ph->set_parent($p_obj->id());
						$ph->set_prop("name", $number);
						$ph->save();
						$p_obj->connect(array(
							"to" => $ph->id(),
							"reltype" => "RELTYPE_PHONE",
						));
//					}
				}
				return PROP_IGNORE;

			case "emails":
				if(strlen($arr["request"]["emails"]) > 0 && substr($arr["request"]["emails"], 0, 9) != "< a href=" && $this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$p_obj = obj($arr["obj_inst"]->prop("person"));
					$odl = new object_data_list(
						array(
							"class_id" => CL_ML_MEMBER,
							"lang_id" => array(),
							"site_id" => array(),
							"parent" => array(),
						),
						array(
							CL_ML_MEMBER => array("mail" => "mail"),
						)
					);
					foreach($odl->arr() as $k => $v)
					{
						$nms[$v["mail"]] = $k;
					}
					if(array_key_exists($arr["request"]["emails"], $nms))
					{
						$p_obj->connect(array(
							"to" => $nms[$arr["request"]["emails"]],
							"reltype" => "RELTYPE_EMAIL",
						));
					}
					else
					{
						$em = new object;
						$em->set_class_id(CL_ML_MEMBER);
						$em->set_parent($p_obj->id());
						$em->set_prop("mail", $arr["request"]["emails"]);
						$em->save();
						$p_obj->connect(array(
							"to" => $em->id(),
							"reltype" => "RELTYPE_EMAIL",
						));
					}
				}
				return PROP_IGNORE;

			case "org":
				if(strlen($arr["request"]["org"]) > 0 && substr($arr["request"]["org"], 0, 9) != "< a href=" && $this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$p_obj = obj($arr["obj_inst"]->prop("person"));
					$ol = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"lang_id" => array(),
						"site_id" => array(),
						"parent" => array(),
					));
					$nms = array_flip($ol->names());
					if(!array_key_exists($arr["request"]["org"], $nms))
					{
						$o = new object;
						$o->set_class_id(CL_CRM_COMPANY);
						$o->set_parent($p_obj->id());
						$o->set_name($arr["request"]["org"]);
						$o->save();
						$nms[$o->name] = $o->id();
					}
					$ok = false;
					foreach($p_obj->connections_from(array("type" => "RELTYPE_ORG_RELATION", "to.class_id" => CL_CRM_PERSON_WORK_RELATION)) as $conn)
					{
						$to = $conn->to();
						if($to->prop("org.name") !=	$arr["request"]["org"] && is_oid($to->org))
						{
							continue;
						}
						else
						if($to->prop("org.name") ==	$arr["request"]["org"] && ($to->prop("profession.name") == $arr["request"]["profession"] || strlen($arr["request"]["profession"]) == 0 || substr($arr["request"]["profession"], 0, 9) == "< a href="))
						{
							$ok = true;
							break;
						}
						else
						{
							$to->org = $nms[$arr["request"]["org"]];
							$to->save();
							$ok = true;
							break;
						}
					}
					if(!$ok)
					{
						$wr = obj();
						$wr->class_id = CL_CRM_PERSON_WORK_RELATION;
						$wr->parent = $p_obj->id;
						$wr->org = $nms[$arr["request"]["org"]];
						$wr->save();
						$p_obj->connect(array(
							"to" => $wr->id,
							"reltype" => "RELTYPE_ORG_RELATION",
						));
					}
				}
				return PROP_IGNORE;

			case "profession":
				if(strlen($arr["request"]["profession"]) > 0 && substr($arr["request"]["profession"], 0, 9) != "< a href=" && $this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$p_obj = obj($arr["obj_inst"]->prop("person"));
					$ol = new object_list(array(
						"class_id" => CL_CRM_PROFESSION,
						"lang_id" => array(),
						"site_id" => array(),
						"parent" => array(),
					));
					$nms = array_flip($ol->names());
					if(!array_key_exists($arr["request"]["profession"], $nms))
					{
						$o = new object;
						$o->set_class_id(CL_CRM_PROFESSION);
						$o->set_parent($p_obj->id());
						$o->set_name($arr["request"]["profession"]);
						$o->save();
						$nms[$o->name] = $o->id();
					}
					$ok = false;
					foreach($p_obj->connections_from(array("type" => "RELTYPE_ORG_RELATION", "to.class_id" => CL_CRM_PERSON_WORK_RELATION)) as $conn)
					{
						$to = $conn->to();
						if($to->prop("profession.name") !=	$arr["request"]["profession"] && is_oid($to->profession))
						{
							continue;
						}
						else
						if($to->prop("profession.name") ==	$arr["request"]["profession"] && ($to->prop("org.name") == $arr["request"]["org"] || strlen($arr["request"]["org"]) == 0 || substr($arr["request"]["org"], 0, 9) == "< a href="))
						{
							$ok = true;
							break;
						}
						else
						{
							$to->profession = $nms[$arr["request"]["profession"]];
							$to->save();
							$ok = true;
							break;
						}
					}
					if(!$ok)
					{
						$wr = obj();
						$wr->class_id = CL_CRM_PERSON_WORK_RELATION;
						$wr->parent = $p_obj->id;
						$wr->profession = $nms[$arr["request"]["profession"]];
						$wr->save();
						$p_obj->connect(array(
							"to" => $wr->id,
							"reltype" => "RELTYPE_ORG_RELATION",
						));
					}
				}
				return PROP_IGNORE;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
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

	/**
		@attrib name=autocomp all_args=1
	**/
	function autocomp($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$clids = array(
			"person" => CL_CRM_PERSON,
			"phones" => CL_CRM_PHONE,
			"emails" => CL_ML_MEMBER,
			"org" => CL_CRM_COMPANY,
			"profession" => CL_CRM_PROFESSION,
		);
		preg_match("/.*(person|phones|emails|org|profession).*/U", $arr["requester"], $clid);
		$clid = $clid[1];

		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		if (!array_key_exists($clid, $clids))
		{
			$errorstring = t("Vigane argument '" . $arr["requester"] . "'");
			$error = true;
		}

		$clid = $clids[$clid];
		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => $clid,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 500,
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}

		$autocomplete_options = array_unique($autocomplete_options);
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	function on_connect_person_to_recommendation($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if($target_obj->class_id() == CL_CRM_RECOMMENDATION)
		{
			$from = $conn->from();
			$target_obj->name = $target_obj->prop("person.name").t(" soovitus isikule ").$from->name;
			$target_obj->save();
		}
	}
}

?>

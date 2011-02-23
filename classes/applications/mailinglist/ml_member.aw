<?php

// ml_member.aw - Mailing list member

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PERSON_WORK_RELATION, on_connect_work_relation_to_email)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_PERSON_WORK_RELATION, on_disconnect_work_relation_from_email)

	@default table=objects
	@default group=general

	@property conn_id type=hidden

	@property name type=textbox table=ml_users
	@caption Nimi

	@property mail type=textbox table=ml_users
	@caption E-post

	@classinfo syslog_type=ST_MAILINGLIST_MEMBER
	@classinfo no_status=1 no_comment=1

	@default group=udef_fields

	@property udef_txbox1 type=textbox field=meta method=serialize
	@caption Textbox 1

	@property udef_txbox2 type=textbox field=meta method=serialize
	@caption Textbox 2

	@property udef_txbox3 type=textbox field=meta method=serialize
	@caption Textbox 3

	@property udef_txbox4 type=textbox field=meta method=serialize
	@caption Textbox 4

	@property udef_txbox5 type=textbox field=meta method=serialize
	@caption Textbox 5

	@property udef_txbox6 type=textbox field=meta method=serialize
	@caption Textbox 6

	@property udef_txbox7 type=textbox field=meta method=serialize
	@caption Textbox 7

	@property udef_txbox8 type=textbox field=meta method=serialize
	@caption Textbox 8

	@property udef_txbox9 type=textbox field=meta method=serialize
	@caption Textbox 9

	@property udef_txbox10 type=textbox field=meta method=serialize
	@caption Textbox 10

	@property udef_txarea1 type=textarea field=meta method=serialize
	@caption Textarea 1

	@property udef_txarea2 type=textarea field=meta method=serialize
	@caption Textarea 2

	@property udef_txarea3 type=textarea field=meta method=serialize
	@caption Textarea 3

	@property udef_txarea4 type=textarea field=meta method=serialize
	@caption Textarea 4

	@property udef_txarea5 type=textarea field=meta method=serialize
	@caption Textarea 5

	@property udef_checkbox1 type=checkbox ch_value=1 field=meta method=serialize
	@caption Checkbox 1

	@property udef_checkbox2 type=checkbox ch_value=1 field=meta method=serialize
	@caption Checkbox 2

	@property udef_checkbox3 type=checkbox ch_value=1 field=meta method=serialize
	@caption Checkbox 3

	@property udef_checkbox4 type=checkbox ch_value=1 field=meta method=serialize
	@caption Checkbox 4

	@property udef_checkbox5 type=checkbox ch_value=1 field=meta method=serialize
	@caption Checkbox 5

	@property udef_classificator1 type=classificator field=meta method=serialize
	@caption Klassifikaator 1

	@property udef_classificator2 type=classificator field=meta method=serialize
	@caption Klassifikaator 2

	@property udef_classificator3 type=classificator field=meta method=serialize
	@caption Klassifikaator 3

	@property udef_classificator4 type=classificator field=meta method=serialize
	@caption Klassifikaator 4

	@property udef_classificator5 type=classificator field=meta method=serialize
	@caption Klassifikaator 5

	@property udef_date1 type=date_select field=meta method=serialize year_from=1930 default=-1
	@caption Kuup&auml;ev 1

	@property udef_date2 type=date_select field=meta method=serialize year_from=1930 default=-1
	@caption Kuup&auml;ev 2

	@property contact_type type=select table=ml_users
	@caption Aadressi t&uuml;&uuml;p

	@groupinfo udef_fields caption="Muud v&auml;ljad"

	@tableinfo ml_users index=id master_table=objects master_index=oid

@reltype BELONGTO value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON,CL_CRM_PERSON_WORK_RELATION
@caption E-postiaadressiga seotud objekt
*/

class ml_member extends class_base
{
	function ml_member()
	{
		$this->init(array(
			"clid" => CL_ML_MEMBER,
		));
		lc_load("definition");
		$this->users = get_instance("users");
		$this->types = array(
			"" => "",
			"0" => t("&Uuml;ldkontakt"),
			"1" => t("E-mail arvete saatmiseks"),
			"2" => t("Projektijuhtimise kontakt"),
		);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;

		switch($data["name"])
		{
			case "contact_type":
				$data["options"] = $this->types;
				break;
		}
	}

	////
	// email(string) - email addy
	// folder(string) - id of the folder to check
	function check_member($args = array())
	{
		$this->quote($args);
		extract($args);
		$ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"mail" => $email,
			"parent" => $folder,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$rv = $ol->count() > 0 ? $ol->begin() : false;
		return $rv;
	}

	function get_member_by_id($id)
	{
		$id = (int)$id;
		$row = new object($id);
		return $row->prop("name") . " " . $row->prop("email");
	}

	////
	// !Creates a new subscribe, the other version, deals with members with no config form
	// name - name of the subscriber
	// email - email addy
	// list_id - id of the list to use for subscribing
	function subscribe_member_to_list($args = array())
	{
		// it would be _really_, _really_ nice if I could init
		// the data from the class_base, but it's not yet possible
		$this->quote($args);
		$name = $args["name"];
		$email = $args["email"];
		$list_id = $args["list_id"];
		if($args["firstname"] || $args["lastname"])
		{
			$name = $args["firstname"]." ".$args["lastname"];
		}

		$list_obj = new object($list_id);
		$user_folder = $list_obj->prop("choose_menu");

		$section = aw_global_get("section");

		if (empty($user_folder))
		{
			return $this->cfg["baseurl"] . "/" . $section;
		};

		$status = 2;

		// I need to validate that stuff as well
		if ($list_obj->prop("confirm_subscribe") != "")
		{
			// generate the confirm code
			$status = 1;
			$ts = time();
			$hash = substr(gen_uniq_id(),0,15);
			// now I need to generate the confirm url
			$url = $this->mk_my_orb("confirmsub",array("hash" => $hash,"addr" => $email));
		};

		$objname = $name . " <" . $email . ">";

		if (sizeof($args["use_folders"]) > 0)
		{
			$user_folders = $args["use_folders"];
		}
		else
		{
			$user_folders = array($user_folder);
		};

		$added = false;
		if (!is_array($user_folders) && is_oid($user_folders))
		{
			$user_folders = array($user_folders);
		}
		foreach($user_folders as $user_folder)
		{
			if(is_array($user_folder))
			{
				$user_folder = reset($user_folder);
			}
			if (!$this->check_member(array("email" => $email, "folder" => $user_folder)))
			{
				$xobjname = htmlspecialchars($objname);
				// Why do we duplicate name and email in object metadata?
				$member_obj = new object();
				$member_obj->set_class_id($this->clid);
				$member_obj->set_parent($user_folder);
				$member_obj->set_status($status);
				$member_obj->set_name($xobjname);

				$member_obj->set_prop("name",$name);
				$member_obj->set_prop("mail",$email);


				$member_obj->set_meta("name",$name);
				$member_obj->set_meta("email",$email);
				$member_obj->set_meta("hash",$hash);
				$member_obj->set_meta("time",$ts);

				if(is_array($args["udef_fields"]["textboxes"]))
				{
					foreach($args["udef_fields"]["textboxes"] as $key => $value)
					{
						$member_obj->set_prop("udef_txbox$key", $value);
					}
				}

				if(is_array($args["udef_fields"]["textareas"]))
				{
					foreach($args["udef_fields"]["textareas"] as $key => $value)
					{
						$member_obj->set_prop("udef_txarea$key", $value);
					}
				}

				if(is_array($args["udef_fields"]["checkboxes"]))
				{
					foreach($args["udef_fields"]["checkboxes"] as $key => $value)
					{
						$member_obj->set_prop("udef_checkbox$key", $value);
					}
				}

				if(is_array($args["udef_fields"]["classificators"]))
				{
					foreach($args["udef_fields"]["classificators"] as $key => $value)
					{
						$member_obj->set_prop("udef_classificator$key", $value);
					}
				}

				$tmp_time = strtotime($args["udef_fields"]["date1"]["year"]."-".$args["udef_fields"]["date1"]["month"]."-".$args["udef_fields"]["date1"]["day"]);
				$member_obj->set_prop("udef_date1", $tmp_time);
				$member_obj->save();

				$added = true;
			}
		}

		$confirm_subscribe = $list_obj->prop("confirm_subscribe");
		$confirm_subscribe_msg = $list_obj->prop("confirm_subscribe_msg");

		if ($added && $confirm_subscribe_msg > 0)
		{
			// now generate and send the bloody message
			$msg = get_instance(CL_MESSAGE);
			$msg->process_and_deliver(array(
				"confirm_mail" => 1,
				"id" => $confirm_subscribe_msg,
				"to" => $objname,
				"replacements" => array(
					"#list#" => parse_obj_name($list_obj->name()),
					"#url#" => $url,
				),
			));
		}
		return $this->cfg["baseurl"] . "/" . $section;
	}

	////
	// !Removes a member from list
	// email - email addy
	// list_id - id of the list to unsubscribe from
	// use_folders - removes user from folders...
	// if not an array - removes member from one folder
	// if not defined , removes from all folders in the mailinglist
	function unsubscribe_member_from_list($args = array())
	{
		$this->quote($args);
		extract($args);
		$deleted = 0;
		$section = aw_global_get("section");
		if((!$use_folders) && is_oid($list_id))
		{
			$list_obj = new object($list_id);
			$use_folders = $list_obj->prop("choose_menu");
		}
		if(is_array($use_folders))
		{
			if (empty($use_folders))
			{
				return $this->cfg["baseurl"] . "/" . $section;
			};
			foreach($use_folders as $folder)
			{
				$check = $this->check_member(array(
					"email" => $email,
					"folder" => $folder,
				));
				if (is_object($check))
				{
					$check->delete();
					$deleted = 1;
				};
			}
		}
		else
		{
			$check = $this->check_member(array(
				"email" => $email,
				"folder" => $use_folders,
			));
			if (is_object($check))
			{
				$check->delete();
				$deleted = 1;
			};
		}

		if ($deleted && $confirm_subscribe_msg > 0)
		{
			// now generate and send the bloody message
			$msg = get_instance(CL_MESSAGE);
			$msg->process_and_deliver(array(
				"confirm_mail" => 1,
				"id" => $confirm_subscribe_msg,
				"to" => $email,
			));
		}



		return isset($args["ret_status"]) ? $check : $this->cfg["baseurl"] . "/" . $section;
	}

	////
	// !Returns member information (e-mail address and variables)
	// lid (int) - list id
	// member (int) - member id
	function get_member_information($args = array())
	{
		extract($args);

		$memberdata = array();
		$mailto = "";

		$list_obj = new object($lid);

		$m = new object($member);
		$mailto = $m->prop("mail");
		$memberdata["name"] = $m->prop("name");
		$memberdata["id"] = $m->id();
		//$memberdata["joined"] = get_lc_date($m->created(), 7);
		$memberdata["joined"] = $m->created();
		if($from_user)
		{
			if($usr = reset($m->connections_to(array(
				"type" => 6, //RELTYPE_EMAIL
				"from.class_id" => CL_USER,
			))))
			{
				$uo = $usr->from();
				$memberdata["name"] = $uo->prop("real_name");
			}
		}
		return array($mailto,$memberdata);
	}

	function callback_pre_save($arr)
	{
		$request = $arr["request"];
		if ($request["name"] != "")
		{
			$arr["obj_inst"]->set_name($request["name"] . " &lt;" .$request["mail"] . "&gt;");
		}
		else
		{
			$arr["obj_inst"]->set_name($request["mail"]);
		}
	}

	function parse_alias($arr = array())
	{
		$o = obj($arr["alias"]["target"]);
		return html::href(array(
			"url" => "mailto:".$o->prop("mail"),
			"caption" => $o->prop("mail")
		));
	}

	function on_connect_work_relation_to_email($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_ML_MEMBER)
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 1,		// RELTYPE_BELONGTO
			));
		}
	}

	function on_disconnect_work_relation_from_email($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_ML_MEMBER)
		{
			if($target_obj->is_connected_to(array('from' => $conn->prop('from'))))
			{
				$target_obj->disconnect(array(
					"from" => $conn->prop("from"),
					"errors" => false
				));
			}
		}
	}


	function do_db_upgrade($tbl, $field, $q, $err)
	{
		switch($field)
		{
			case "contact_type":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				return true;

		}
		return false;
	}


}

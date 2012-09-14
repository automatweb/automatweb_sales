<?php

// mail_rule.aw - Maili ruul
/*
@classinfo relationmgr=yes

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property rule_from type=textbox
@caption From

@property rule_subject type=textbox
@caption Subject

@property target_folder type=select
@caption Liiguta folderisse

@property on_server type=checkbox ch_value=1 default=1
@caption Salvesta serverisse
*/

class mail_rule extends class_base
{
	function mail_rule()
	{
		$this->init(array(
			"clid" => CL_MAIL_RULE
		));
	}

	////
	// !Returns the owner (messenger) of the current rule object
	function get_owner($arr)
	{
		$from = aw_global_get("from_obj");
		if (!empty($from))
		{
			$msgr_id = $from;
		}
		else
		{
			$msgr = $arr["obj_inst"]->connections_to(array(
				"from.class_id" => CL_MESSENGER_V2,
			));
			$msgr_id = false;
			foreach($msgr as $item)
			{
				$msgr_id = $item->prop("from");
			};
		};
		return $msgr_id;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "target_folder":
				$tmp = array();
				$folders = array();
				if(!$arr["new"])
				{
					$msgr_id = $this->get_owner(array(
						"obj_inst" => $arr["obj_inst"],
					));
				}
				if (!empty($msgr_id))
				{
					$msgr_obj = new object($msgr_id);
					if ($msgr_obj->class_id() == CL_MESSENGER_V2)
					{
						$msgr = $msgr_obj->instance();
						$msgr->_connect_server(array("msgr_id" => $msgr_obj->id()));
						$tmp = $msgr->drv_inst->list_folders();
						foreach($tmp as $item)
						{
							$folders[$item["fullname"]] = $item;
						};
					};
				};
				foreach($folders as $key => $fld)
				{
					$tmp[$key] = $fld["name"];
				};
				$prop["options"] = $tmp;
				break;

			case "on_server":
				$ftp = get_instance(CL_FTP_LOGIN);
				if (!$ftp->is_available())
				{
					$prop["error"] = t("FTP extension is required for this feature");
					$retval = PROP_ERROR;
				}
				break;

		};
		return $retval;
	}

	function callback_pre_save($arr)
	{
		$name = $arr["obj_inst"]->name();
		$subj = $arr["obj_inst"]->prop("rule_subject");
		$from = $arr["obj_inst"]->prop("rule_from");
		$name_parts = array();
		if (empty($name))
		{
			if (!empty($subj))
			{
				$name_parts[] = "Subject: $subj";
			};

			if (!empty($from))
			{
				$name_parts[] = "From: $from";
			};
		};
		if (empty($name) && count($name_parts) > 0)
		{
			$arr["obj_inst"]->set_name(join(", ",$name_parts));
		};
	}

	function callback_post_save($arr)
	{
		$o = $arr["obj_inst"];
		if (1 == $o->prop("on_server"))
		{
			$match_from = $o->prop("rule_from");
			$match_subject = $o->prop("rule_subject");

			$rule_id = "# AW rule " . $o->id();
			$rule_start = $rule_id . " start\n";
			$rule = $rule_start;
			$rule .= ":0:\n";
			if ($match_from)
			{
				$rule .= "* ^From.*" . $match_from . "\n";
			}
			else
			{
				$rule .= "* ^Subject.*" . $match_subject . "\n";
			};
			$target_folder = $o->prop("target_folder");
			$target_folder = str_replace(" ","\\ ",$target_folder);
			$rule .= '$MAILDIR/.' . $target_folder . "/\n";
			$rule_end = $rule_id . " end\n";
			$rule .= $rule_end;
			$msgr_id = $this->get_owner(array(
				"obj_inst" => $arr["obj_inst"],
			));

			if (!empty($msgr_id))
			{
				$msgr_obj = new object($msgr_id);
				$mailbox = $msgr_obj->get_first_obj_by_reltype(RELTYPE_MAIL_SOURCE);
				$server = $mailbox->prop("server");
				$user = $mailbox->prop("user");
				$password = $mailbox->prop("password");
				$t = get_instance(CL_FTP_LOGIN);
				$conn = $t->connect(array(
					"host" => $server,
					"user" => $user,
					"pass" => $password
				));

				$rulefilename = ".procmailrc";
				$fdat = $t->get_file($rulefilename);

				if (!$fdat)
				{
					$fdat = "";
					$fdat = "MAILDIR=\$HOME/Mail\n";
					$fdat .= "DEFAULT=\$MAILDIR/\n";
					$fdat .= "LOGFILE=\$MAILDIR/proclog\n";

					$new_rule_file = $fdat . $rule;
				}
				else
				{
					$begin = strpos($fdat,$rule_start);
					$end = strpos($fdat,$rule_end);

					if ($begin && $end)
					{
						$before = substr($fdat,0,$begin);
						$after = substr($fdat,$end - 1 + strlen($rule_end));

						$new_rule_file = $before . $rule . $after;


					}
					else
					{
						$new_rule_file = $fdat . $rule;
					}
				}

				$t->put_file($rulefilename,$new_rule_file);
			}
		}
	}
}

<?php

// quickmessage.aw - Kiirteade
/*

@classinfo syslog_type=ST_QUICKMESSAGE no_status=1 no_comment=1 maintainer=voldemar prop_cb=1
@tableinfo quickmessages index=id master_table=objects master_index=brother_of

@default table=quickmessages
@default group=general
	@property name type=text table=objects

	@property to type=hidden
	@property from type=hidden
	@property box type=hidden
	@property html type=hidden
	@property url type=hidden

	@property from_display type=text store=no editonly=1
	@caption From

	@property to_display type=textbox store=no option_is_tuple=1 newonly=1
	@caption To

	@property msg type=textarea newonly=1 rows=10
	@caption Message

	@property msg_display type=text store=no editonly=1 field=msg
	@caption Message

	@property send type=submit store=no newonly=1
	@caption Send

*/

class quickmessage extends class_base
{
	function quickmessage()
	{
		$this->init(array(
			"clid" => CL_QUICKMESSAGE
		));
	}

	function _get_to_display($arr)
	{
		$arr["prop"]["autocomplete_delimiters"] = array(",", ";");
		$arr["prop"]["options"] = $arr["obj_inst"]->get_to_options();
		return PROP_OK;
	}

	function _get_msg_display($arr)
	{
		$arr["prop"]["value"] = htmlspecialchars($arr["prop"]["value"]);
		return PROP_OK;
	}

	function _get_from($arr)
	{
		if($arr["new"])
		{
			$arr["prop"]["value"] = aw_global_get("uid_oid");
		}
	}

	function _get_box($arr)
	{
		$status = PROP_OK;
		$box = $arr["request"]["box"];

		if (!is_oid($box))
		{
			$status = PROP_ERROR;
			$arr["prop"]["error"] = t("Specified messagebox id is invalid.");
		}
		else
		{
			$arr["prop"]["value"] = (int) $box;
		}

		return $status;
	}

	function _set_to($arr)
	{
		$arr["obj_inst"]->set_prop("to", explode(",", $arr["request"]["to_display"]));
		return PROP_IGNORE;
	}

	function _set_msg($arr)
	{
		$status = PROP_OK;
		$max_len = aw_ini_get("quickmessaging.msg_max_len");
		if ($max_len and $max_len < strlen($arr["prop"]["value"]))
		{
			$arr["prop"]["error"] = sprintf(t("Message too long. Only %s characters allowed."), $max_len);
			$status = PROP_ERROR;
		}
		return $status;
	}

	function submit($arr)
	{
		try
		{
			$ret = parent::submit($arr);

			try
			{
				$return_url = new aw_uri($arr["return_url"]);

				if ("quickmessagebox" === $return_url->arg("class"))
				{
					$ret = $return_url->get();
				}
			}
			catch (Exception $e)
			{
			}
		}
		catch (awex_qmsg_param $e)
		{
			$args = array(
				"id" => 1,
				"group" => 1,
				"box" => 1,
				"parent" => 1,
				"return_url" => 1,
				"section" => 1,
			);
			$args = array_intersect_key($arr, $args);

			if ($this->new)
			{
				$action = "new";
				unset($args["id"]);
				$propvalues["to_display"]["error"] = sprintf(t("Can't post to '%s'. %s"), $arr["to_display"], $e->getMessage());
				aw_session_set("cb_values", $propvalues);
			}
			else
			{
				$action = "change";
			}

			$ret = $this->mk_my_orb($action, $args, $arr["class"], false, (bool) $arr["ret_to_orb"], "&", false);
		}

		return $ret;
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("quickmessages" === $table)
		{
			switch($field)
			{
				case "from":
				case "box":
				case "html":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "int"
					));
					break;

				case "to":
				case "msg":
				case "url":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "text"
					));
					break;
			}
		}
	}
}
?>

<?php

namespace automatweb;
// realestate_client_selection.aw - Klientide valim
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_REALESTATE_CLIENT_SELECTION, on_create)

@classinfo syslog_type=ST_REALESTATE_CLIENT_SELECTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=voldemar

@default table=objects
@default group=general
@default field=meta
@default method=serialize
	@property client_ids type=hidden

// --------------- RELATION TYPES ---------------------

@reltype MAILINGLIST value=1 clid=CL_ML_LIST
@caption Valimi meililist

*/

class realestate_client_selection extends class_base
{
	const AW_CLID = 1026;

	function realestate_client_selection()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_client_selection",
			"clid" => CL_REALESTATE_CLIENT_SELECTION
		));

		$this->realestate_classes = array (
			CL_REALESTATE_HOUSE,
			CL_REALESTATE_ROWHOUSE,
			CL_REALESTATE_COTTAGE,
			CL_REALESTATE_HOUSEPART,
			CL_REALESTATE_APARTMENT,
			CL_REALESTATE_COMMERCIAL,
			CL_REALESTATE_GARAGE,
			CL_REALESTATE_LAND,
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
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

	// @attrib name=get_clients
	// @param this required
	function &get_clients ($arr)
	{
		if (is_object ($arr["this"]))
		{
			$this_object = $arr["this"];
		}
		elseif ($this->can ("view", $arr["this"]))
		{
			$this_object = obj ($arr["this"]);
		}
		else
		{
			return false;
		}

		if (!is_object ($this->cl_user))
		{
			$this->cl_user = get_instance (CL_USER);
		}

		if (!is_object ($this->cl_users))
		{
			$this->cl_users = get_instance ("users");
		}

		$this->manager = obj ($this_object->parent ());
		$client_ids = $this_object->prop ("client_ids");

		if (is_array ($client_ids)  and count ($client_ids))
		{
			$clients_by_id = new object_list (array (
				"class_id" => CL_CRM_PERSON,
				"parent" => $this->manager->prop ("clients_folder"),
				"oid" => $client_ids,
			));
		}

		$filter_appreciation_after = $this_object->meta ("realestate_clientlist_filter_appreciationafter");
		$filter_appreciation_before = $this_object->meta ("realestate_clientlist_filter_appreciationbefore");
		$filter_address = $this_object->meta ("realestate_clientlist_filter_address");
		$filter_appreciation_type = $this_object->meta ("realestate_clientlist_filter_appreciationtype");
		$filter_name = $this_object->meta ("realestate_clientlist_filter_name");
		$filter_agent = $this_object->meta ("realestate_clientlist_filter_agent");

		$clients_by_constraints = new object_list (array (
			"class_id" => CL_CRM_PERSON,
			"parent" => array ($this->manager->prop ("clients_folder")),
		));
		$clients = $clients_by_constraints->arr ();

		foreach ($clients as $client)
		{
			$client_in_selection = true;

			### filter by client name
			if (isset ($filter_name))
			{
				$filter_name = trim ($filter_name);

				if ($filter_name)
				{
					$search = explode (" ", strtolower ($filter_name));
					$found = 0;
					$words = 0;
					$client_name = $client->name ();

					foreach ($search as $word)
					{
						$word = trim ($word);

						if ($word)
						{
							$words++;
							$pos = strpos (strtolower ($client_name), trim ($word));

							if ($pos !== false)
							{
								$found++;
							}
						}
					}

					if ($words != $found)
					{
						$client_in_selection = false;
					}
				}
			}

			### filter by client's address
			if (isset ($filter_address))
			{
				$filter_address = trim ($filter_address);

				if ($filter_address)
				{
					$search = explode (" ", strtolower ($filter_address));
					$found = 0;
					$words = 0;
					$client_address = $client->prop ("comment");

					foreach ($search as $word)
					{
						$word = trim ($word);

						if ($word)
						{
							$words++;
							$pos = strpos (strtolower ($client_address), trim ($word));

							if ($pos !== false)
							{
								$found++;
							}
						}
					}

					if ($words != $found)
					{
						$client_in_selection = false;
					}
				}
			}

			### get appreciation note data
			$connections = $client->connections_to ();
			$properties = array ();

			foreach ($connections as $connection)
			{
				if (in_array ($connection->prop ("from.class_id"), $this->realestate_classes) and $this->can ("view", $connection->prop ("from")))
				{
					$properties[$connection->prop ("from")] = obj ($connection->prop ("from"));
				}
			}

			$last_appreciation_sent = NULL;

			foreach ($properties as $property)
			{
				if ($property->prop ("appreciation_note_date") > $last_appreciation_sent)
				{
					$last_appreciation_sent = $property->prop ("appreciation_note_date");
					$last_appreciation_type_oid = $property->prop ("appreciation_note_type");
				}
			}

			### filter by appreciation note sent
			if (0 < $last_appreciation_sent and $last_appreciation_sent < $filter_appreciation_after)
			{
				$client_in_selection = false;
			}

			if ($last_appreciation_sent > $filter_appreciation_before)
			{
				$client_in_selection = false;
			}

			### filter by appreciation note type
			if (is_array ($filter_appreciation_type) and !in_array ($last_appreciation_type_oid, $filter_appreciation_type))
			{
				$client_in_selection = false;
			}

			### get agent
			if ($this->manager->prop ("almightyuser"))
			{
				$uid = $client->createdby ();

				if (!isset ($this->realestate_agent_data[$uid]))
				{
					$oid = $this->cl_users->get_oid_for_uid ($uid);
					aw_switch_user (array ("uid" => $this->manager->prop ("almightyuser")));
					$user = obj ($oid);
					$this->realestate_agent_data[$uid]["agent_oid"] = $this->cl_user->get_person_for_user ($user);
					aw_restore_user ();
				}
			}
			else
			{
				echo t('"Kõik lubatud" kasutaja keskkonna seadetes määramata');
				return;
			}

			### filter by agent
			if (is_array ($filter_agent) and !in_array ($this->realestate_agent_data[$uid]["agent_oid"], $filter_agent))
			{
				$client_in_selection = false;
			}

			### remove from list if client not in selection
			if (!$client_in_selection)
			{
				$clients_by_constraints->remove ($client);
			}
		}

		if (is_object ($clients_by_id))
		{
			$clients_by_constraints->add ($clients_by_id);
		}

		return $clients_by_constraints;
	}

	function on_create ($arr)
	{
		if (is_oid ($arr["oid"]))
		{
			$this_object = obj ($arr["oid"]);
		}
		else
		{
			error::raise(array(
				"msg" => t("Uue kliendivalimi loomisel ei antud argumendina kaasa loodud objekti id-d."),
				"fatal" => true,
				"show" => true,
			));
		}

		$mailinglist = new object ();
		$mailinglist->set_class_id (CL_ML_LIST);
		$mailinglist->set_parent ($this_object->id ());
		$mailinglist->set_prop ("def_user_folder", $this_object->id ());
		$mailinglist->set_name (t("Kliendivalimi meilinglist"));
		$mailinglist->save ();

		$this_object->connect (array (
			"to" => $mailinglist,
			"reltype" => "RELTYPE_MAILINGLIST",
		));
		$mailinglist->connect (array (
			"to" => $this_object,
			"reltype" => "RELTYPE_MEMBER_PARENT",
		));
	}
}
?>

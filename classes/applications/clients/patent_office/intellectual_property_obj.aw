<?php

class intellectual_property_obj extends _int_object
{
	const CLID = 1441;

	protected static $ip_classes = array(
		CL_PATENT,
		CL_PATENT_PATENT,
		CL_INDUSTRIAL_DESIGN,
		CL_EURO_PATENT_ET_DESC,
		CL_UTILITY_MODEL
	);

	public function awobj_get_authorized_codes()
	{
		$this->check_and_set_authorized_codes_user_access();
		return parent::prop("authorized_codes");
	}

	public function check_and_set_authorized_codes_user_access()
	{
		$pid = aw_global_get("uid_oid");
		$already_set = $this->meta("set_authorized_codes_user_access4");
		if (!$pid or empty($already_set[$pid]) or $already_set[$pid] < $this->modified())
		{
			// set access for persons in authorized_codes
			$user =  new object($pid);
			$grp = new object($user->get_default_group());

			if ($grp instanceof object)
			{
				// access spec
				if ($this->createdby() === aw_global_get("uid"))
				{ // owner
					$general_access = $attachment_access = array(
						"can_add" => 1,
						"can_edit" => 1,
						"can_admin" => 1,
						"can_delete" => 1,
						"can_view" => 1
					);
				}
				else
				{ // authorized codes users
					$general_access = array(
						"can_add" => 1,
						"can_edit" => 1,
						"can_admin" => 0,
						"can_delete" => 0,
						"can_view" => 1
					);
					$attachment_access = array(
						"can_add" => 0,
						"can_edit" => 0,
						"can_admin" => 0,
						"can_delete" => 0,
						"can_view" => 1
					);
				}

				// access to application object
				$this->acl_set($grp, $general_access);

				// access to digidoc object
				$ddc = $this->connections_to(array(
					"from.class_id" => CL_DDOC
				));

				foreach ($ddc as $c)
				{
					if ($c->prop("from.status") != object::STAT_DELETED)
					{
						$ddo = new object($c->from());
						$ddo->acl_set($grp, $general_access);
						$signers = $ddo->connections_from(array("type" => "RELTYPE_SIGNER"));
						foreach ($signers as $s_c)
						{
							$signer = $s_c->to();
							$signer->acl_set($grp, array(
								"can_add" => 0,
								"can_edit" => 0,
								"can_admin" => 0,
								"can_delete" => 0,
								"can_view" => 1
							));
						}
					}
				}

				// access to attachments etc.
				$cc = $this->connections_from();

				foreach ($cc as $c)
				{
					$co = new object($c->to());
					$co->acl_set($grp, $attachment_access);
				}
			}

			$already_set[$pid] = time();
			$this->set_meta("set_authorized_codes_user_access4", $already_set);
			if (is_oid($this->id()))
			{
				$this->save();
			}
		}
	}

	/** Returns associated digidoc object if found, NULL if not
		@attrib api=1 params=pos obj_save=conditional
		@param create type=bool default=FALSE
		@comment
			Saves object if $create=true
		@returns CL_DDOC|NULL
		@errors
	**/
	public function get_digidoc($create = false)
	{
		if (acl_base::can("", $this->meta("digidoc_oid")))
		{
			$digidoc = obj($this->meta("digidoc_oid"), array(), ddoc_obj::CLID);
		}
		else
		{ // legacy
			$c = new connection();
			$cc = $c->find(array(
				"from.class_id" => ddoc_obj::CLID,
				"type" => "RELTYPE_SIGNED_FILE",
				"to" => $this->id()
			));

			$digidoc = reset($cc);
			if ($digidoc)
			{
				$digidoc = obj($digidoc["from"], array(), ddoc_obj::CLID);
				$this->set_meta("digidoc_oid", $digidoc->id());
				$this->save();
			}
			else
			{
				$digidoc = null;
			}
		}

		if ($create and !$digidoc)
		{
			$digidoc = obj(null, array(), CL_DDOC);
			$digidoc->set_parent($this->id());
			$digidoc->set_name(sprintf("DigiDoc '%s'", $this->name()));
			$digidoc->save();
			$this->set_meta("digidoc_oid", $digidoc->id());
			$this->save();
		}

		return $digidoc;
	}
}

class awex_po extends aw_exception {}
class awex_po_xml extends awex_po {}

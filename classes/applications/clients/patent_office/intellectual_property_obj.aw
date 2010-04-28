<?php

namespace automatweb;


class intellectual_property_obj extends _int_object
{
	const AW_CLID = 1441;

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
		$u = new user();
		$p = obj($u->get_current_person()); //!!! ei t88ta id kaardiga?
		$pid = $p->prop("personal_id");
		$already_set = $this->meta("set_authorized_codes_user_access2");
		if (true and empty($already_set[$pid]))
		{
			aw_disable_acl();
			// set access for persons in authorized_codes
			$user =  new object(aw_global_get("uid_oid"));
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
					"from.class_id" => CL_DDOC,
					// "from.status" => new obj_predicate_not(object::STAT_DELETED)
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
			aw_restore_acl();
			$already_set[$pid] = 1;
			$this->set_meta("set_authorized_codes_user_access2", $already_set);
		}
	}
}

class awex_po extends aw_exception {}

?>

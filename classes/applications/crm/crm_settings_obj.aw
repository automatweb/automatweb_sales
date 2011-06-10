<?php

class crm_settings_obj extends _int_object
{
	const CLID = 1036;

	const DEFAULT_BILL_OVERDUE_INTEREST = "0.15";
	const DEFAULT_BILL_DUE_DAYS = "14";

	private static $instance_cache = false;

	/** Finds and returns currently active/applicable crm settings object
		@attrib api=1 params=pos
		@returns CL_CRM_SETTINGS
		@qc date=20101117 standard=aw3
	**/
	public static function get_active_instance()
	{
		if (false === self::$instance_cache)
		{
			$u = new user();
			$curp = $u->get_current_person();
			$curco = $u->get_current_company();
			$cd = new crm_data();
			$cursec = $cd->get_current_section();
			$curprof = $cd->get_current_profession();

			$ol = new object_list(array(
				"class_id" => CL_CRM_SETTINGS,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_SETTINGS.RELTYPE_USER" => aw_global_get("uid_oid"),
						"CL_CRM_SETTINGS.RELTYPE_PERSON" => $curp,
						"CL_CRM_SETTINGS.RELTYPE_COMPANY" => $curco,
						"CL_CRM_SETTINGS.RELTYPE_SECTION" => $cursec,
						"CL_CRM_SETTINGS.RELTYPE_PROFESSION" => $curprof,
						"CL_CRM_SETTINGS.everyone" => 1
					)
				))
			));

			if ($ol->count() > 1)
			{
				// the most accurate setting SHALL Prevail!
				$has_co = $has_p = $has_u = $has_all = $has_sec = $has_prof = false;
				foreach($ol->arr() as $o)
				{
					if ($cursec && $o->is_connected_to(array("to" => $cursec)))
					{
						$has_sec = $o;
					}

					if ($curprof && $o->is_connected_to(array("to" => $curprof)))
					{
						$has_prof = $o;
					}

					if ($o->is_connected_to(array("to" => $curco)))
					{
						$has_co = $o;
					}

					if ($o->is_connected_to(array("to" => $curp)))
					{
						$has_p = $o;
					}

					if ($o->is_connected_to(array("to" => aw_global_get("uid_oid"))))
					{
						$has_u = $o;
					}

					if ($o->prop("everyone"))
					{
						$has_all = $o;
					}
				}

				if ($has_u)
				{
					$rv = $has_u;
				}
				elseif ($has_p)
				{
					$rv = $has_p;
				}
				elseif ($has_prof)
				{
					$rv = $has_prof;
				}
				elseif ($has_sec)
				{
					$rv = $has_sec;
				}
				elseif ($has_co)
				{
					$rv = $has_co;
				}
				elseif ($has_all)
				{
					$rv = $has_all;
				}
			}
			elseif ($ol->count())
			{
				$rv = $ol->begin();
			}
			else
			{
				$rv = null;
			}

			self::$instance_cache = $rv;
		}
		else
		{
			$rv = self::$instance_cache;
		}

		return $rv;
	}
}

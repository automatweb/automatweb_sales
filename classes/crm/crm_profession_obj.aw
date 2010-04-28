<?php

namespace automatweb;


class crm_profession_obj extends _int_object
{
	const AW_CLID = 220;

	/** returns all workers with this profession
		@attrib api=1
		@returns object_list of CL_CRM_PERSON
	**/
	public function get_workers($section = null, $bc = true, $only_active = true)
	{
		if(is_oid($section))
		{
			return $this->get_workers_for_section($section);
		}

		$person_list = new object_list();

		$rel_list = new object_list(array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"profession" => $this->id()
		));

		if(sizeof($rel_list->ids()))
		{
			$person_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"CL_CRM_PERSON.RELTYPE_CURRENT_JOB" => $rel_list->ids()
			));

			if (!$only_active)
			{
				$person_list->add(new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"CL_CRM_PERSON.RELTYPE_WORK_RELATION" => $rel_list->ids()
				)));
			}
		}

		return $person_list;
	}


	public function get_workers_for_section($section)
	{
		$ol = new object_list();

		$rel_list = new object_list(array(
			"class_id" => CL_CRM_PERSON_WORK_RELATION,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_CRM_PERSON_WORK_RELATION.RELTYPE_SECTION" => $section,
			"profession" => $this->id()
		));

		if(sizeof($rel_list->ids()))
		{
			$person_list = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"site_id" => array(),
				"class_id" => array(),
				"CL_CRM_PERSON.RELTYPE_CURRENT_JOB" => $rel_list->ids()
			));
			return $person_list;
		}
		else
		{
			return $ol;
		}
	}
}

?>

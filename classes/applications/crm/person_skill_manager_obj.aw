<?php

class person_skill_manager_obj extends _int_object
{

	function get_all_skills($parent = 0)
	{
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_PERSON_SKILL,
		);
		if($parent)
		{
			$filter["parent"] = $parent;
		}
		$ol = new object_list($filter);
		return $ol;
	}

	function get_root_skills()
	{
		$filter = array(
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_PERSON_SKILL,
//			"parent.class_id" => new obj_predicate_not(CL_PERSON_SKILL),
		);
		$ol = new object_list($filter);
		foreach($ol->arr() as $o)//see kuramuse filter yleval ei funka
		{
			if($o->prop("parent.class_id") == CL_PERSON_SKILL)
			{
				$ol -> remove($o->id());
			}
		}
		return $ol;
	}

}

?>

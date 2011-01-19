<?php

class work_load_manager_obj extends _int_object
{
	public function get_professions()
	{
		return new object_list(array(
			"class_id" => CL_STUDY_ORGANISATION_PROFESSION,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}

	public function get_competences()
	{
		return new object_list(array(
			"class_id" => CL_STUDY_ORGANISATION_COMPETENCE,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}

	public function get_research_groups()
	{
		return new object_list(array(
			"class_id" => CL_STUDY_ORGANISATION_RESEARCH_GROUP,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}
	
	public function get_publication_categories()
	{
		return new object_list(array(
			"class_id" => CL_STUDY_ORGANISATION_PUBLICATION_CATEGORY,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}
}

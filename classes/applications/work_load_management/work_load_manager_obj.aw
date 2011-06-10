<?php

class work_load_manager_obj extends _int_object
{
	const CLID = 1774;

	public function get_entries()
	{
		$parents = $ol = new object_list(array(
			"class_id" => CL_WORK_LOAD_DECLARATION,
			"manager" => $this->id(),
		));

		return $parents->count() > 0 ? new object_list(array(
			"class_id" => CL_WORK_LOAD_DECLARATION_ENTRY,
			"parent" => $parents->ids(),
		)) : new object_list();;
	}

	public function get_professions($args = array())
	{
		return new object_list(array_merge(array(
			"class_id" => CL_STUDY_ORGANISATION_PROFESSION,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		), $args));
	}

	public function get_rate_applicables()
	{
		return new object_list(array(
			"class_id" => array(CL_STUDY_ORGANISATION_PROFESSION, CL_STUDY_ORGANISATION_COMPETENCE),
			"parent" => $this->id(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_STUDY_ORGANISATION_PROFESSION.rated" => true,
					"CL_STUDY_ORGANISATION_COMPETENCE.rated" => true,
				),
			)),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}

	public function get_rates()
	{
		return new object_list(array(
			"class_id" => CL_STUDY_ORGANISATION_RATE,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		));
	}

	public function get_competences($args = array())
	{
		return new object_list(array_merge(array(
			"class_id" => CL_STUDY_ORGANISATION_COMPETENCE,
			"parent" => $this->id(),
			new obj_predicate_sort(array(
				"jrk" => obj_predicate_sort::ASC,
				"name" => obj_predicate_sort::ASC
			))
		), $args));
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

<?php

class crm_person_education_obj extends _int_object
{
	function set_name($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_name($v);
	}

	function set_comment($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_comment($v);
	}

	function prop($k)
	{
		if($k == "degree" && !is_numeric(parent::prop($k)))
		{
			$degree_opts = array(
				"pohiharidus" => 1,
				"keskharidus" => 2,
				"keskeriharidus" => 4,
				"diplom" => 8,
				"bakalaureus" => 9,
				"magister" => 10,
				"doktor" => 11,
				"teadustekandidaat" => 12,
			);
			return $degree_opts[parent::prop($k)];
		}
		return parent::prop($k);
	}

	function set_prop($k, $v)
	{
		$html_allowed = array();
		if($k == "degree" && !is_numeric($v))
		{
			$degree_opts = array(
				"pohiharidus" => 1,
				"keskharidus" => 2,
				"keskeriharidus" => 4,
				"diplom" => 8,
				"bakalaureus" => 9,
				"magister" => 10,
				"doktor" => 11,
				"teadustekandidaat" => 12,
			);
			$v = $degree_opts[$v];
		}
		if(!in_array($k, $html_allowed))
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}

	public function on_connect_person_to_edu($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_EDUCATION)
		{
			if($conn->prop('reltype') == 92 || $conn->prop('reltype') == 23)
			{
				$target_obj->connect(array(
					"to" => $conn->prop("from"),
					"reltype" => "RELTYPE_PERSON",
				));
			}
		}
	}

	public function on_disconnect_person_from_edu($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_EDUCATION)
		{
			if($conn->prop('reltype') == 92 || $conn->prop('reltype') == 23)
			{
				if($target_obj->is_connected_to(array(
						'to' => $conn->prop('from'),
						'type' => "RELTYPE_PERSON")))
				{
					$target_obj->disconnect(array(
						"from" => $conn->prop("from"),
						'reltype' => "RELTYPE_PERSON",
					));
				}
			}
		}
	}

	public function on_disconnect_edu_from_person($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->from();
		if ($target_obj->class_id() == CL_CRM_PERSON_EDUCATION)
		{
			if($conn->prop('reltype') == 4)
			{
				if($target_obj->is_connected_to(array(
						'to' => $conn->prop('to'),
						'type' => "RELTYPE_EDUCATION_2")))
				{
					$target_obj->disconnect(array(
						"from" => $conn->prop("to"),
						'reltype' => "RELTYPE_EDUCATION_2",
					));
				}
				if($target_obj->is_connected_to(array(
						'to' => $conn->prop('to'),
						'type' => "RELTYPE_EDUCATION")))
				{
					$target_obj->disconnect(array(
						"from" => $conn->prop("to"),
						'reltype' => "RELTYPE_EDUCATION",
					));
				}
			}
		}
	}
}

?>

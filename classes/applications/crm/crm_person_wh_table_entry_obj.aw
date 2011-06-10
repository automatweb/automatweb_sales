<?php

class crm_person_wh_table_entry_obj extends _int_object
{
	const CLID = 1510;

	public function get_entry_data()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON_WH_TABLE_ENTRY_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"wh_table_entry" => $this->id()
		));
		$rv = array();
		foreach($ol->arr() as $d)
		{
			$rv[$d->person] = $d;
		}
		return $rv;
	}
}

?>

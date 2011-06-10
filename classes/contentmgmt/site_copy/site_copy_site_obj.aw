<?php

class site_copy_site_obj extends _int_object
{
	const CLID = 1489;

	function delete($full_delete = false)
	{
		if($this->meta("delete"))
		{
			return parent::delete($full_delete);
		}
		else
		{
			$this->add_delete_todo();
		}
	}

	public function add_delete_todo()
	{	
		get_instance(CL_SITE_COPY);
		// Lisame kustutamise todo listi
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_TODO,
			"lang_id" => array(),
			"site_id" => array(),
			"url" => $this->prop("url"),
			"sc_status" => site_copy_todo::STAT_DELETE,
		));
		if($ol->count() == 0)
		{
			$o = obj();
			$o->set_class_id(CL_SITE_COPY_TODO);
			$o->set_parent(get_instance(CL_SITE_COPY)->get_obj_inst()->id());
			$o->url = $this->prop("url");
			$o->sc_status = site_copy_todo::STAT_DELETE;
			$o->save();
		}
	}
}

?>

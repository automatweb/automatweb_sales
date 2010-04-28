<?php

namespace automatweb;


class generic_xml_ds_obj extends _int_object implements object_import_ds_interface
{
	const AW_CLID = 1391;

	public function get_objects($params = array())
	{
	}

	public function get_folders()
	{
	}

	public function get_fields($ds_o, $is_file = false)
	{
	}

	private function do_import()
	{
		$rss = file_get_contents($this->prop("location"));
	}
}

?>
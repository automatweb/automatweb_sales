<?php

namespace automatweb;


class practices_obj extends _int_object
{
	const AW_CLID = 1525;

	public function get_data_folder($arr)
	{
		// Maybe I should check here, if the cat is actually somewhere under the
		// data folder, but i'll leave this for laters
		if ( isset($arr['request']['cat']) && !empty($arr['request']['cat']) )
		{
			if ($this->can('view', $arr['request']['cat']))
			{
				return $arr['request']['cat'];
			}
		}
		return $this->prop('data_folder');
		
	}

	public function get_practices($parent)
	{
		$ol = new object_list(array(
			'class_id' => CL_PRACTICE,
			'parent' => $parent	
		));

		return $ol;
	}

	public function get_categories()
	{
		$ot = new object_tree(array(
			'class_id' => CL_MENU,
			'parent' => $this->prop('data_folder')
		));

		return $ot->to_list();
	}
}

?>

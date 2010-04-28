<?php

namespace automatweb;

class menu_tree_obj extends _int_object
{
	const AW_CLID = 93;

	/** Returns object list for menus under root menu set for current menu_tree

	 **/
	public function sitemap_menulist()
	{
		$cs = $this->connections_from(array(
			"type" => "RELTYPE_ROOT_MENU",
		));
		foreach($cs as $c)
		{
			$menus[] = $c->to()->id();
		}
		$inst = get_instance(CL_MENU_TREE);
		$inst->mt_obj = $this;
		$list = new object_list();
		foreach($menus as $menu_id)
		{
			$inst->gen_rec_list(array(
				"start_from" => $menu_id,
			));
			$list->add($this->_scan_rec_list($inst->object_list));
		}
		return $list;
	}

	private function _scan_rec_list($arr)
	{
		$ol = new object_list();
		foreach($arr as $p => $obj)
		{
			$ol->add(is_array($obj)?$this->_scan_rec_list($obj):$obj);
		}
		return $ol;
	}
}	
?>

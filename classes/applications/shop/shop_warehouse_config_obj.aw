<?php

class shop_warehouse_config_obj extends _int_object
{
	public function awobj_get_prod_tree_clids()
	{
		$v = parent::prop("prod_tree_clids");
		if(empty($v))
		{
			return array(CL_MENU);
		}
		else
		{
			return $v;
		}
	}
}

?>
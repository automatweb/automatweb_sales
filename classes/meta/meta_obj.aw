<?php
/*
@classinfo maintainer=robert
*/
class meta_obj extends _int_object
{
	function meta($var = false)
	{
		if($var == "translations")
		{
			$trans = parent::meta("translations");
			if(!strlen($trans["name"]))
			{
				$tolge = parent::meta("tolge");
				if(count($tolge))
				{
					$this->fix_translations($tolge);
				}
			}
			return parent::meta($var);
		}
		else
		{
			return parent::meta($var);
		}
	}

	function trans_get_val($prop, $lang_id = false, $ignore_status = false)
	{
		if($prop == "name")
		{
			$this->meta("translations");
		}
		return parent::trans_get_val($prop, $lang_id, $ignore_status);
	}

	function fix_translations($arr)
	{
		$tmp = array();
		foreach($arr as $langid=>$name)
		{
			$tmp[$langid]["name"] = $name;
		}
		parent::set_meta("tolge", array());
		$mmi = get_instance(CL_METAMGR);
		$o = obj(parent::id());
		$mmi->_init_save_trans();
		$mmi->_save_trans(&$this, $tmp);
	}
}
?>

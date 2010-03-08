<?php
/*
@classinfo  maintainer=markop
*/

class crm_sector_obj extends _int_object
{
	public function trans_get_val($prop, $lang_id = false, $ignore_status = false)
	{
		return parent::trans_get_val($prop == "name" ? "tegevusala" : $prop, $lang_id, $ignore_status);
	}

	function meta($k = false)
	{
		if($k === "menu_images" || $k === "active_menu_images")
		{
			foreach(safe_array(parent::meta($k)) as $item)
			{
				if(is_oid($item["image_id"]))
				{
					return parent::meta($k);
				}
			}
			if($this->can("view", $this->prop("parent_sector")))
			{
				return obj($this->prop("parent_sector"))->meta($k);
			}
		}

		return parent::meta($k);
	}
}

?>

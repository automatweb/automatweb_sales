<?php

namespace automatweb;


class content_package_price_conditions_obj extends _int_object
{
	const AW_CLID = 1543;

	function save($exclusive = false, $previous_state = null)
	{
		if(is_oid($this->id()))
		{

			if(!$this->can("view", $this->prop("cp_spp")) || !is_oid($this->prop("cp_spp")))
			{
				// We'll make a shop product packaging for this content package price conditions. There has to be one for every content package price conditions.
				$spp = obj();
				$spp->set_class_id(CL_SHOP_PRODUCT_PACKAGING);
				$spp->set_parent($this->id());
			}
			else
			{
				$spp = obj($this->prop("cp_spp"));
			}
			// The name and price of the shop product packaging are always the same as the content package's.
			$spp->set_name($this->name());
			$spp->set_prop("content_package_price_condition", $this->id());
			$spp->set_prop("price", $this->prop("price"));
			$this->set_prop("cp_spp", $spp->save());

			$ol = new object_list(array(
				"class_id" => CL_CONTENT_PACKAGE,
				"CL_CONTENT_PACKAGE.RELTYPE_PRICE_CONDITIONS" => $this->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				$cp = $ol->begin();
				$sp = obj($cp->prop("cp_sp"));
				$sp->connect(array(
					"to" => $spp->id(),
					"type" => "RELTYPE_PACKAGING",
				));
			}
			else
			{
				die("Fuck! No content package!");
			}
		}

		return parent::save($exclusive, $previous_state);
	}
}

?>

<?php

namespace automatweb;


class shop_brand_obj extends _int_object
{
	const AW_CLID = 1443;


	public function get_logo_html()
	{
		$pic = $this->get_first_obj_by_reltype("RELTYPE_LOGO");
		if(is_object($pic))
		{
			return $pic->get_html();

		}
		else
		{
			return "";
		}
	}


}

?>

<?php

class doc_obj extends _int_object
{
	const CLID = 7;

	public function is_visible_to()
	{
		//dokumentide mitte n2itamine yleliigsetest riikidest tulevatele p2ringutele
		if(is_oid($this->id()) && strlen($this->prop("show_to_country")) > 1)
		{
			$aproved_countries = explode("," , $this->prop("show_to_country"));
			if(!in_array(detect_country() , $aproved_countries))
			{
				return false;
			}
		}
		return true;
	}
}

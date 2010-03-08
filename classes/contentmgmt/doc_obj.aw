<?php

class doc_obj extends _int_object
{

	function prop($n)
	{
		switch($n)
		{
			/*case "show_to_country":
				$inst = $this->instance();
				$tbl = $inst->db_get_table("documents");
				if (!isset($tbl["fields"]["show_to_country"]))
				{
					$this->instance()->db_add_col("documents", array(
						"name" => "show_to_country",
						"type" => "varchar(255)"
					));
				}
			break;*/
		}
		return parent::prop($n);
	}

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

?>

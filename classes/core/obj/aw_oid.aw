<?php

namespace automatweb;

class aw_oid
{
	private $oid = "";

	public function __construct($oid = array("new"))
	{
		if (array("new") === $oid)
		{
			$string_oid = "";
		}
		else
		{
			$string_oid = (string) $oid;
			if (empty($oid) or $oid !== $string_oid)
			{
				throw new awex_oid("Invalid object id ".var_export($oid, true));
			}
		}

		$this->oid = $string_oid;
	}

	public function get_string()
	{
		return $this->oid;
	}
}

class awex_oid extends awex_obj {}

?>

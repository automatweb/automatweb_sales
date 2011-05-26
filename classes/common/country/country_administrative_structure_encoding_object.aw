<?php
/*
@classinfo  maintainer=voldemar
*/
require_once(aw_ini_get("basedir") . "/classes/common/address/as_header.aw");

class country_administrative_structure_encoding_object extends _int_object
{
	const CLID = 1033;

	var $as_unit_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT,
	);
	var $as_address_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT,
		CL_ADDRESS_STREET,
	);

	function prop ($param)
	{
		if (is_array ($param))
		{
			$name = $param["prop"];

			switch ($name)
			{
				case "encoding_by_unit":
					return $this->as_get_unit_encoding ($param);
			}
		}
		else
		{
			switch ($param)
			{
				default:
					return parent::prop ($param);
			}
		}
	}

	function set_prop ($name, $param)
	{
		switch ($name)
		{
			case "encoding_by_unit":
				return;

			default:
				return parent::set_prop ($name, $param);
		}
	}

    // @attrib name=as_get_unit_encoding
	// @param unit required
	// @returns Encoded value for unit.
	private function as_get_unit_encoding ($arr)
	{
		### validate unit object
		if (is_object ($arr["unit"]))
		{
			$unit =& $arr["unit"];
		}
		elseif (is_oid ($arr["unit"]) and $this->can ("view", $arr["unit"]))
		{
			$unit = obj ($arr["unit"]);
		}
		else
		{
/* dbg */ if ($_GET[ADDRESS_DBG_FLAG]) { echo "adminstructureencoding::as_get_unit_encoding: unit not defined or not visible [{$arr["unit"]}]".AS_NEWLINE; }
			return false;
		}

		$encoded_value = $unit->meta (
			"admin_structure_enc-" . $this->obj["oid"] . "-value1"
		);
		return $encoded_value;
	}
}

?>

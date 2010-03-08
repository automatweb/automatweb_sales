<?php
/*
@classinfo  maintainer=voldemar
*/

class country_obj extends _int_object
{
	/** Returns currently active administrative structure for this country
	@attrib api=1 params=pos
	@errors
		throws awex_as_country_admin_structure when not defined
	**/
	public function get_current_admin_structure()
	{
		$administrative_structure = $this->prop("administrative_structure");
		if (is_oid($administrative_structure))
		{
			$administrative_structure = new object($administrative_structure);
		}
		else
		{
			$list = new object_list(array(
				"class_id" => CL_COUNTRY_ADMINISTRATIVE_STRUCTURE,
				"CL_COUNTRY_ADMINISTRATIVE_STRUCTURE.RELTYPE_COUNTRY" => $this->id()
			));

			if ($list->count() < 1)
			{
				throw new awex_as_country_admin_structure("Administrative structure not defined for this country");
			}
			///!!! teha midagi kui rohkem kui yks on
			$administrative_structure = $list->begin();
		}

		return $administrative_structure;
	}
}

/** Generic country_obj exception **/
class awex_as_country extends awex_as {}

/** Country administrative error **/
class awex_as_country_admin_structure extends awex_as_country {}


?>

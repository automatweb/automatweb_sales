<?php
/*
@classinfo  maintainer=kristo
*/

/*

HANDLE_MESSAGE(MSG_STORAGE_ALIAS_DELETE, on_delete_alias)
HANDLE_MESSAGE(MSG_STORAGE_ALIAS_ADD, on_add_alias)


*/

class brother_support
{
	function on_delete_alias($arr)
	{
		// now, get the alias. if it has reltype of RELTYPE_BROTHER, then get the object it points TO
		// then find any brothers of the object the relation comes from and delete them
		if ($arr["connection"]->prop("reltype") == RELTYPE_BROTHER)
		{
			$ol = new object_list(array(
				"parent" => $arr["connection"]->prop("from"),
				"brother_of" => $arr["connection"]->prop("to")
			));
			for($o =& $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$o->delete();
			}
		}
	}

	function on_add_alias($arr)
	{
		// now, get the alias. if it has reltype of RELTYPE_BROTHER, then add a brother below the folder
		// the connection points to
		if ($arr["connection"]->prop("reltype") == RELTYPE_BROTHER)
		{
			$to = $arr["connection"]->to();
			$from = $arr["connection"]->from();
			$to->create_brother($from->id());
		}
	}
}
?>

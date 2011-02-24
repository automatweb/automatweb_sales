<?php

class menu_obj extends _int_object
{
	const CLID = 1;

	const TYPE_CLIENT = 69;
	// sisurubriik
	const TYPE_CONTENT = 70;
	// adminni ylemine menyy
	const TYPE_ADMIN1 = 71;
	const TYPE_PROMO_BOX = 73;
	const TYPE_HOME_FOLDER = 74;
	// kodukataloogi alla tehtud kataloog, et sharetud katalooge olex lihtsam n2idata
	const TYPE_HOME_FOLDER_SUB = 75;
	// formi element, mis on samas ka menyy
	const TYPE_FORM_ELEMENT = 76;
	// public method
	const TYPE_PMETHOD = 77;

	/** Recusively sets status for this and all subfolders. Saves object if status parameter given
		@attrib api=1 params=pos
		@param value type=int default=NULL
			One of object::STAT_* constants. If value is null, current status value will be set for all subfolders and object will not be saved.
		@returns bool
		@errors
			throws awex_obj_state if object not saved
	**/
	public function set_status_recursive($value = null)
	{
		$this->require_state("saved");
		$r = true;

		// determine value to be set and whether to modify and save this object
		if (null === $value)
		{
			$dont_save_this = true;
			$value = $this->status();
		}
		else
		{
			$dont_save_this = false;
		}

		try
		{
			$subfolders = new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $this->id()
			));

			if ($dont_save_this)
			{ // convert to list and remove this object
				$subfolders = $subfolders->to_list();
				$subfolders->remove($this->id());
			}

			// objtree and objlist have the same interface
			$subfolders->foreach_o(array(
				"func" => "set_status",
				"params" => array($value),
				"save" => true
			));
		}
		catch (Exception $e)
		{
			$r = false;
		}

		return $r;
	}
}

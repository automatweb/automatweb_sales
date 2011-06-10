<?php
/*

@classinfo maintainer=markop

*/
class budgeting_account_obj extends _int_object
{
	const CLID = 1202;

	function get_account_balance()
	{
		return $this->prop("balance");
	}

	function get_account_taxes()
	{
		$ol = new object_list(array(
			"class_id" => CL_BUDGETING_TAX_FOLDER_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"folder" => "%_".$this->id()."%"
		));
		$taxes = new object_list();

		foreach($ol->arr() as $o)
		{
			$taxes -> add($o->prop("tax"));
		}

		return $taxes;
	}

}
?>

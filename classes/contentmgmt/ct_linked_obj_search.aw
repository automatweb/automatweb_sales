<?php
/*
@classinfo  maintainer=kristo
*/

classload("vcl/popup_search");
/** Linked object (link property) search for menu and container. popup_search extender **/
class ct_linked_obj_search extends popup_search
{
	function ct_linked_obj_search()
	{
		$this->popup_search();
	}

	protected function _insert_form_props(&$htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);

		$def = array(CL_DOCUMENT => 1, CL_MENU => 1);
		$htmlc->add_property(array(
			"name" => "s[op_clid]",
			"type" => "chooser",
			"value" => isset($_GET["MAX_FILE_SIZE"]) ? $arr["s"]["op_clid"] : $def,
			"caption" => t("Leia"),
			"multiple" => 1,
			"orient" => "vertical",
			"options" => array(
				CL_DOCUMENT => t("Dokumente"), CL_MENU => t("Kaustu"), -1 => t("K&otilde;iki")
			)
		));
	}

	protected function _process_reforb_args(&$data)
	{
		$data["s"] = array(
			"co" => $_GET["s"]["co"]
		);
	}

	protected function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);

		if (!$_GET["MAX_FILE_SIZE"])
		{
			$arr["s"]["op_clid"] = array();
			$arr["s"]["op_clid"][CL_DOCUMENT] = 1;
			$arr["s"]["op_clid"][CL_MENU] = 1;
		}

		if (is_array($arr["s"]["op_clid"]) && !$arr["s"]["op_clid"][-1])
		{
			$filter["class_id"] = $arr["s"]["op_clid"];
		}
	}
}

?>
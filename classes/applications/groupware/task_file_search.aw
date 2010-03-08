<?php
/*
@classinfo maintainer=markop
*/
classload("vcl/popup_search");
class task_file_search extends popup_search
{
	function task_file_search()
	{
		$this->popup_search();
	}

	function _insert_form_props(&$htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);

		$cur_co = get_current_company();
		$opts = array();

		$clids = array(
			CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER,CL_MENU
		);

		$clss = aw_ini_get("classes");
		$def = array();
		foreach($clids as $clid)
		{
			$opts[$clid] = $clss[$clid]["name"];
			if ($clid != CL_MENU)
			{
				$def[$clid] = 1;
			}
		}
		$htmlc->add_property(array(
			"name" => "koop",
			"type" => "chooser",
			"value" => isset($_GET["MAX_FILE_SIZE"]) ? $_GET["koop"] : $def,
			"caption" => t("Mida otsida"),
			"multiple" => 1,
			"orient" => "vertical",
			"options" => $opts
		));
	}

	function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);

		if (is_array($_GET["koop"]) && count($_GET["koop"]))
		{
			$filter["class_id"] = $_GET["koop"];
		}
		else
		{
			$filter["class_id"] = array(CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER);
		}
	}
}

?>

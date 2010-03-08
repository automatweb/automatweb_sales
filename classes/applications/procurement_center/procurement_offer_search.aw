<?php
/*
@classinfo maintainer=markop
*/
classload("vcl/popup_search");
class procurement_offer_search extends popup_search
{
	function procurement_offer_search()
	{
		$this->popup_search();
	}

	function _insert_form_props(&$htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);

		$opts = array();
		if (is_array($arr["s"]["offerer"]))
		{
			$opts["offerer"] = $arr["s"]["offerer"];
		}

		$htmlc->add_property(array(
			"name" => "s[offerer]",
			"type" => "textbox",
			"value" =>  $_GET["s"]["offerer"],
			"caption" => t("Pakkuja"),
			"multiple" => 1,
			"orient" => "vertical",
			"options" => $opts
		));
	}

	function _process_reforb_args(&$data)
	{
	}

	function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);

		$filter["offerer"] = null;

		if ($arr["s"]["offerer"] != "")
		{
			$filter["CL_PROCUREMENT_OFFER.offerer.name"] = "%".$arr["s"]["offerer"]."%";
		}
	}
}

?>

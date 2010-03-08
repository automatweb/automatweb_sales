<?php
// $Header: /home/cvs/automatweb_dev/classes/vcl/codepress.aw,v 1.1 2008/08/29 11:25:33 robert Exp $
// fck_editor.aw - FCKeditor
/*
@classinfo  maintainer=hannes
*/
class codepress extends aw_template
{
	function codepress()
	{
		$this->init (array (
			"tpldir" => "vcl/codepress",
		));
	}
	
	function get_rte_toolbar($arr)
	{
		if (!is_object($arr["toolbar"]))
		{
			return;
		}
		$toolbar = &$arr["toolbar"];
		$toolbar->add_separator();
		if($arr["no_rte"] == 1)
		{
			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("RTE"),
				"target" => "_self",
				"url" => aw_url_change_var("no_rte", ""),
			));
		}
		else
		{
			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("HTML"),
				"target" => "_self",
				"url" => "javascript:oldurl=window.location.href;window.location.href=oldurl + '&no_rte=1';",
			));
		}
	}
	
	function draw_editor($arr)
	{
		load_javascript("codepress/codepress.js");
	}
}
?>

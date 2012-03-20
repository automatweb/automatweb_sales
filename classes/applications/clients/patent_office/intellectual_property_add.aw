<?php

class intellectual_property_add extends class_base
{
	private static $ip_class_index = array(
		"trademark_add" => "patent",
		"patent_add" => "patent_patent",
		"industrial_design_add" => "industrial_design",
		"utility_model_add" => "utility_model",
		"euro_patent_et_desc_add" => "euro_patent_et_desc"
	);

	public function make_menu_link($o, $ref = null)
	{
		/* epa hack. parent object given before menu level child objects. occurs only with trademark submenu. distinguishable by whether $ref defined */
		if (is_object($ref))
		{
			return;
		}
		/* end epa hack */

		$this_ip_class = self::$ip_class_index[get_class($this)];

		if(isset($_SESSION["patent"]["id"]) and acl_base::can("view", $_SESSION["patent"]["id"]))
		{
			$tr_inst = new $this_ip_class();
			$res = $tr_inst->is_signed($_SESSION["patent"]["id"]);
			if($res["status"] == 1)
			{
				return aw_url_change_var("")."#";
			}

			$intellectual_property_object = obj($_SESSION["patent"]["id"]);
			 $saved = $intellectual_property_object->is_saved();
		}
		else
		{
			$saved = false;
		}

		static $jrk;

		if (empty($jrk))
		{
			$jrk = 0;
		}

		$item = $this_ip_class::$level_index[$jrk];
		$url = automatweb::$request->get_uri();
		$url->unset_arg("new_application");

		if ($jrk === 0)
		{
			$url->set_arg("data_type", "0");
			if (!empty($_GET["section"]))
			{
				$url->set_arg("section", $_GET["section"]);
			}
		}
		elseif (
			$saved or
			isset($_SESSION["patent"]["checked"]) and is_array($_SESSION["patent"]["checked"]) and in_array($item, $_SESSION["patent"]["checked"]) or
			$item == $url->arg("data_type")
		)
		{
			$url->set_arg("data_type", $item);
		}

		++$jrk;
		return $url->get();
	}
}

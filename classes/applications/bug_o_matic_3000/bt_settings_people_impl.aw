<?php
/*
@classinfo  maintainer=robert
*/
class bt_settings_people_impl extends core
{
	function bt_settings_people_impl()
	{
		$this->init();
	}

	function _get_sp_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "add_s_res_to_p_list",
			"img" => "save.gif",
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "remove_p_from_l_list",
		));
	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-mail"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_sp_table($arr)
	{	
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		$bt = get_instance(CL_BUG_TRACKER);
		foreach($bt->get_people_list($arr["obj_inst"]) as $p_id => $p_nm)
		{
			$p = obj($p_id);
			$t->define_data(array(
				"name" => html::obj_change_url($p),
				"co" => html::obj_change_url($p->company_id()),
				"phone" => $p->prop("phone.name"),
				"email" => $p->prop("email.name"),
				"oid" => $p->id()
			));
		}
	}

	function _get_sp_s_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		if ($arr["request"]["sp_p_name"] != "" || $arr["request"]["sp_p_co"] != "")
		{
			$param = array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$arr["request"]["sp_p_name"]."%"
			);
			if ($arr["request"]["sp_p_co"] != "")
			{
				$param["CL_CRM_PERSON.RELTYPE_CURRENT_JOB.org.name"] = "%".$arr["request"]["sp_p_co"]."%";
			}
			$ol = new object_list($param);
			foreach($ol->arr() as $p)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($p),
					"co" => html::obj_change_url($p->company_id()),
					"phone" => $p->prop("phone.name"),
					"email" => html::href(array("url" => "mailto:".$p->prop("email.mail"),"caption" => $p->prop("email.mail"))),
					"oid" => $p->id()
				));
			}
		}
	}
}
?>

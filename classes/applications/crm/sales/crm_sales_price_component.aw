<?php
/*
@classinfo syslog_type=ST_CRM_SALES_PRICE_COMPONENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel
@tableinfo aw_crm_sales_price_component master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_sales_price_component
@default group=general

	@property toolbar type=toolbar_standard_obj no_caption=1 store=no

	@layout general_vsplit type=hbox width=50%:50%

		@layout general_left type=vbox parent=general_vsplit

			@layout general_left_top type=vbox parent=general_left area_caption=Hinnakomponendi&nbsp;&uuml;ldandmed

				@property application type=hidden field=aw_application
				@caption Rakendus

				@property value type=hidden field=aw_value
				@caption V&auml;&auml;rtus

				@property is_ratio type=hidden field=aw_is_ratio
				@caption V&auml;&auml;rtus on protsentuaalne

				@property name type=textbox table=objects field=name parent=general_left_top
				@caption Nimi

				@property type type=select field=aw_type parent=general_left_top
				@caption T&uuml;&uuml;p

				@property category type=select field=aw_category parent=general_left_top
				@caption Kategooria

				@property value_absolute type=textbox store=no maxlength=20 parent=general_left_top
				@caption Summa

				@property value_ratio type=textbox store=no maxlength=20 parent=general_left_top
				@caption Protsent

			@layout general_left_bottom type=vbox parent=general_left no_padding=1 area_caption=Tolerantsid&nbsp;ja&nbsp;kohustuslikkus

				@property restrictions_add type=hidden store=no editonly=1

				@property restrictions_toolbar type=toolbar store=no no_caption=1 editonly=1 parent=general_left_bottom

				@property restrictions_table type=table store=no no_caption=1 editonly=1 parent=general_left_bottom


		@layout general_right type=vbox parent=general_vsplit

			@layout general_right_top type=vbox parent=general_right area_caption=Hinnakomponendi&nbsp;eelduskomponendid

				@property prerequisites type=relpicker reltype=RELTYPE_PREREQUISITE multiple=1 store=connect no_edit=1 no_search=1 width=350 parent=general_right_top
				@caption Eelduskomponent

			@layout general_right_bottom type=vbox parent=general_right no_padding=1 area_caption=Objektid&#44;&nbsp;mis&nbsp;on&nbsp;hinnakomponendi&nbsp;rakendamise&nbsp;eelduseks

				@property applicables type=relpicker reltype=RELTYPE_APPLICABLE store=connect multiple=1 parent=general_right_bottom

				@property applicables_add type=hidden store=no editonly=1

				@property applicables_toolbar type=toolbar store=no no_caption=1 editonly=1 parent=general_right_bottom

				@property applicables_table type=table store=no no_caption=1 editonly=1 parent=general_right_bottom

#### RELTYPES

@reltype PREREQUISITE value=1 clid=CL_CRM_SALES_PRICE_COMPONENT
@caption Eelduskomponent

@reltype APPLICABLE value=2
@caption Rakendumise eeldusobjekt

*/

class crm_sales_price_component extends class_base
{
	public function crm_sales_price_component()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_sales_price_component",
			"clid" => CL_CRM_SALES_PRICE_COMPONENT
		));

		$this->type_options = array(
			crm_sales_price_component_obj::TYPE_UNIT => t("Rakendub &uuml;hikule"),
			crm_sales_price_component_obj::TYPE_ROW => t("Rakendub reale"),
			crm_sales_price_component_obj::TYPE_TOTAL => t("Rakendub kogusummale"),
		);

		$this->no_application_error_text = t("Rakendus m&auml;&auml;ramata. Hinnakomponente peab olema lisatud l&auml;bi rakenduse (m&uuml;&uuml;gikeskkond).");
	}

	public function _get_applicables($arr)
	{
		return PROP_IGNORE;
	}

	public function _set_applicables($arr)
	{
		return PROP_IGNORE;
	}

	public function _set_value($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_category($arr)
	{
		if(is_object($this->application))
		{
			$options = array(t("--Vali--"));
			$price_components = $this->application->get_price_component_category_list();
			$options += $price_components->names();
			$arr["prop"]["options"] = $options;
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	public function _get_value_ratio($arr)
	{
		$arr["prop"]["value"] = $arr["obj_inst"]->prop("is_ratio") ? $arr["obj_inst"]->prop("value") : NULL;
		return PROP_OK;
	}

	public function _set_value_ratio($arr)
	{
		if(automatweb::$request->arg("is_ratio"))
		{
			$arr["obj_inst"]->set_prop("value", $arr["prop"]["value"]);
		}
		return PROP_IGNORE;
	}

	public function _get_value_absolute($arr)
	{
		$arr["prop"]["value"] = !$arr["obj_inst"]->prop("is_ratio") ? $arr["obj_inst"]->prop("value") : NULL;
		return PROP_OK;
	}

	public function _set_value_absolute($arr)
	{
		if(!automatweb::$request->arg("is_ratio"))
		{
			$arr["obj_inst"]->set_prop("value", $arr["prop"]["value"]);
		}
		return PROP_IGNORE;
	}

	public function _get_restrictions_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$applicable_clids = array(CL_CRM_SECTION, CL_CRM_PERSON_WORK_RELATION, CL_CRM_PROFESSION);
		$t->add_search_button(array(
			"name" => "restrictions_search",
			"pn" => "restrictions_add",
			"clid" => $applicable_clids
		));
		$t->add_button(array(
			"name" => "remove_restrictions",
			"img" => "delete.gif",
			"action" => "remove_restrictions",
		));
	}

	protected function define_restrictions_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "class",
			"caption" => t("T&uuml;&uuml;p"),
		));
		$t->define_field(array(
			"name" => "compulsory",
			"caption" => t("Kohustuslik"),
			"tooltip" => t("See hinnakomponent on valitud &uuml;ksusele/rollile/t&ouml;&ouml;suhtele kohustuslik"),
			"callback" => array($this, "callback_restrictions_table_compulsory"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "lower_tolerance",
			"caption" => t("Alumine tolerants (%)"),
			"tooltip" => t("Summa/protsent peab olema v&auml;hemalt ... % vaikimisi m&auml;&auml;ratud summast/protsendist"),
			"callback" => array($this, "callback_restrictions_table_lower_tolerance"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "upper_tolerance",
			"caption" => t("&Uuml;lemine tolerants (%)"),
			"tooltip" => t("Summa/protsent v&otilde;ib olla maksimaalselt ... % vaikimisi m&auml;&auml;ratud summast/protsendist"),
			"callback" => array($this, "callback_restrictions_table_upper_tolerance"),
			"callb_pass_row" => true,
		));
	}

	public function _get_restrictions_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->define_restrictions_table_header($arr);

		$restrictions = $arr["obj_inst"]->get_restrictions();
		foreach($restrictions->arr() as $restriction)
		{
			$t->define_data(array(
				"restriction" => $restriction,
				"oid" => $restriction->id(),
				"name" => $restriction->prop("subject.name"),
				"class" => $restriction->subject()->class_title(),
				"compulsory" => $restriction->compulsory,
				"lower_tolerance" => $restriction->lower_tolerance,
				"upper_tolerance" => $restriction->upper_tolerance,
			));
		}
	}

	public function callback_restrictions_table_compulsory($row)
	{
		return html::checkbox(array(
			"name" => "restrictions_table[{$row["restriction"]->id()}][compulsory]",
			"checked" => $row["restriction"]->compulsory,
		));
	}

	public function callback_restrictions_table_lower_tolerance($row)
	{
		return html::textbox(array(
			"name" => "restrictions_table[{$row["restriction"]->id()}][lower_tolerance]",
			"value" => $row["lower_tolerance"],
			"size" => 20,
			"maxlength" => 20
		));
	}

	public function callback_restrictions_table_upper_tolerance($row)
	{
		return html::textbox(array(
			"name" => "restrictions_table[{$row["restriction"]->id()}][upper_tolerance]",
			"value" => $row["upper_tolerance"],
			"size" => 20,
			"maxlength" => 20
		));
	}

	public function _set_restrictions_table($arr)
	{
		if(is_array($arr["prop"]["value"]))
		{
			foreach($arr["prop"]["value"] as $restriction_id => $restriction_data)
			{
				$restriction = obj($restriction_id);
				$restriction->set_prop("compulsory", !empty($restriction_data["compulsory"]));
				$restriction->set_prop("lower_tolerance", $restriction_data["lower_tolerance"]);
				$restriction->set_prop("upper_tolerance", $restriction_data["upper_tolerance"]);
				$restriction->save();
			}
		}
	}

	public function _set_restrictions_add($arr)
	{
		$restrictions = explode(",", $arr["prop"]["value"]);
		foreach($restrictions as $restriction)
		{
			if(is_oid($restriction))
			{
				$arr["obj_inst"]->add_restriction($restriction);
			}
		}
	}

	/**
		@attrib name=remove_restrictions params=name
		@param id required type=int
			The OID of the price component to remove the restriction objects from
		@param sel required type=int[]
			An array of OIDs of the restriction objects to remove from the price component
		@param post_ru required type=string
	**/
	public function remove_restrictions($arr)
	{
		if(is_array($arr["sel"]))
		{
			$o = obj($arr["id"]);
			foreach($arr["sel"] as $restriction)
			{
				$o->remove_restriction($restriction);
			}
		}
		return $arr["post_ru"];
	}

	public function _get_applicables_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$applicable_clids = crm_sales_price_component_obj::get_applicable_clids();
		$t->add_search_button(array(
			"name" => "search_applicables",
			"pn" => "applicables_add",
			"clid" => $applicable_clids
		));
		$t->add_button(array(
			"name" => "remove_applicables",
			"img" => "delete.gif",
			"action" => "remove_applicables",
		));
	}

	public function _get_prerequisites($arr)
	{
		$options = array(
			"net_value" => t("Juurhind"),
		);

		if(is_object($this->application))
		{
			$price_components = $this->application->get_price_component_list();
			$options += $price_components->names();
		}

		if($arr["obj_inst"]->is_saved() && isset($options[$arr["obj_inst"]->id()]))
		{
			unset($options[$arr["obj_inst"]->id()]);
		}

		$arr["prop"]["options"] = $options;

		if(empty($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = array("net_value");
		}
	}

	public function _set_prerequisites($arr)
	{
		if(empty($arr["prop"]["value"]))
		{
			$arr["prop"]["error"] = t("Eelduskomponendi/-komponentide valimine on kohustuslik! Vaikimisi eelduskomponent on 'Juurhind'.");
			return PROP_FATAL_ERROR;
		}
		else
		{
			//	Check for cycle. crm_sales_price_component_obj::check_prerequisites_cycle() will return the cycle details someday)
			$cycle = crm_sales_price_component_obj::check_prerequisites_cycle($arr["obj_inst"]->id(), $arr["prop"]["value"]);
			if($cycle !== false)
			{
				$arr["prop"]["error"] = sprintf(t("Teie eelduskomponentide valik p&otilde;hjustab ts&uuml;kli!"));
				return PROP_FATAL_ERROR;
			}
		}
		return PROP_OK;
	}

	public function _get_type($arr)
	{
		$arr["prop"]["options"] = $this->type_options;
	}

	protected function define_applicables_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "class",
			"caption" => t("T&uuml;&uuml;p"),
		));
	}

	public function _get_applicables_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$this->define_applicables_table_header($arr);

		$applicables = $arr["obj_inst"]->get_applicables();
		foreach($applicables->arr() as $applicable)
		{
			$t->define_data(array(
				"oid" => $applicable->id(),
				"name" => html::obj_change_url($applicable),
				"class" => $applicable->class_title(),
			));
		}
	}

	public function _get_application($arr)
	{
		if(!empty($arr["new"]) && is_object($this->application))
		{
			$arr["prop"]["value"] = $this->application->id();
		}

		return PROP_OK;
	}

	public function _set_application($arr)
	{
		if(!is_oid($arr["prop"]["value"]))
		{
			$this->show_error_text($this->no_application_error_text);
			return PROP_FATAL_ERROR;
		}

		return PROP_OK;
	}

	public function _set_applicables_add($arr)
	{
		$applicables = explode(",", $arr["prop"]["value"]);
		foreach($applicables as $applicable)
		{
			if(is_oid($applicable))
			{
				$arr["obj_inst"]->add_applicable($applicable);
			}
		}
	}

	/**
		@attrib name=remove_applicables params=name
		@param id required type=int
			The OID of the price component to remove the applicable objects from
		@param sel required type=int[]
			An array of OIDs of the applicable objects to remove from the price component
		@param post_ru required type=string
	**/
	public function remove_applicables($arr)
	{
		if(is_array($arr["sel"]))
		{
			$o = obj($arr["id"]);
			foreach($arr["sel"] as $applicable)
			{
				$o->remove_applicable($applicable);
			}
		}
		return $arr["post_ru"];
	}

	public function callback_on_load($arr)
	{
		$oid = automatweb::$request->arg("id");
		if(!is_oid($oid))
		{
			if(is_oid($application = automatweb::$request->arg("application")))
			{
				$this->application = obj($application);
			}
			else
			{
				$this->show_error_text($this->no_application_error_text);
				$this->application = false;
			}
		}
		elseif(is_oid($application = obj($oid)->prop("application")))
		{
			$this->application = obj($application);
		}
		else
		{
			$this->show_error_text($this->no_application_error_text);
			$this->application = false;
		}
	}

	public function callback_generate_scripts($arr)
	{
		return file_get_contents(AW_DIR . "classes/applications/crm/sales/crm_sales_price_component.js");
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_sales_price_component(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_value":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "decimal(19,4)"
				));
				return true;

			case "aw_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "tinyint unsigned"
				));
				return true;

			case "aw_is_ratio":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "boolean"
				));
				return true;

			case "aw_category":
			case "aw_application":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;
		}
	}
}

?>

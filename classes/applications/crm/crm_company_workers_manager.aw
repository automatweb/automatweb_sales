<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_company_workers_manager master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default method=serialize
@default field=meta
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY
@caption Ettev&otilde;te

@default group=employees_management
	@property add_existing_employee_oid type=hidden datatype=int store=no
	@property add_existing_profession_oid type=hidden datatype=int store=no
	@property es_c type=hidden store=request

	@property hrm_toolbar type=toolbar no_caption=1 store=no
	@caption T&ouml;&ouml;tajatehalduse tegevused

	@layout hrm_main_container type=hbox width=20%:80%
		@layout hrm_query_container type=vbox parent=hrm_main_container

			@layout hrm_tree_container type=vbox parent=hrm_query_container closeable=1 area_caption=Organisatsiooni&nbsp;struktuur
				@property organization_structure_tree type=treeview store=no parent=hrm_tree_container no_caption=1
				@caption Organisatsiooni struktuur

			@layout hrm_search_container type=vbox parent=hrm_query_container closeable=1 area_caption=Otsi&nbsp;t&ouml;&ouml;tajat
				@layout search_params_container type=vbox parent=hrm_search_container

					@property es_n type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption T&ouml;&ouml;taja nimi

					@property es_code type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption T&ouml;&ouml;taja isikukood

					@property es_citizenship type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption T&ouml;&ouml;taja kodakondsus

					@property es_s type=chooser store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;suhte staatus

					@property es_county type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Maakond

					@property es_city type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Linn/vald

					@property es_index type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Postiindeks

					@property es_a type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Aadress
					@property es_e type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption E-Post

					@property es_age type=text store=request parent=search_params_container captionside=top
					@caption Vanus

					@property es_agefrom type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus alates

					@property es_ageto type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus kuni

					@property es_g type=chooser store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;taja sugu

				@layout search_submit_container type=hbox parent=hrm_search_container

					@property es_sbt type=submit size=15 store=no parent=search_submit_container no_caption=1
					@caption Otsi t&ouml;&ouml;tajat

		@layout hrm_information_container type=vbox parent=hrm_main_container
			@layout unit_list_container type=vbox parent=hrm_information_container closeable=1 area_caption=&Uuml;ksused no_padding=1 default_state=closed
				@property organizational_units_table type=table store=no no_caption=1 parent=unit_list_container
				@caption &Uuml;ksused

			@layout profession_list_container type=vbox parent=hrm_information_container closeable=1 area_caption=Ametid no_padding=1 default_state=closed
				@property professions_table type=table store=no no_caption=1 parent=profession_list_container
				@caption Ametid

			@layout employees_list_container type=vbox parent=hrm_information_container area_caption=T&ouml;&ouml;tajad closeable=1 no_padding=1
				@property employees_table type=table store=no parent=employees_list_container no_caption=1
				@caption T&ouml;&ouml;tajad

	@groupinfo employees_management caption="T&ouml;&ouml;tajad"

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Ettev&otilde;te
*/

class crm_company_workers_manager extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_workers_manager",
			"clid" => crm_company_workers_manager_obj::CLID
		));
		$this->search_props = array("es_n","es_s","es_g","es_a","es_e","es_agefrom","es_ageto", "es_c", "es_county",  "es_city", "es_index", "es_citizenship", "es_code");
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_company_workers_manager" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_company_workers_manager` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_company_workers_manager", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}

	function get_property(&$arr)
	{
		$retval = PROP_OK;
		$data = &$arr['prop'];
		$arr["use_group"] = $this->use_group;

		if(in_array($data["name"] , $this->search_props))
		{
			if(isset($arr["request"][$data["name"]]))
			{
				$data["value"] = $arr["request"][$data["name"]];
			}
			else
			{
				$data["value"] = "";
			}
		}

		if ("employees_management" === $this->use_group)
		{
			static $employees_view;
			if (!$employees_view)
			{
				$employees_view = new crm_company_employees_view();
//				$this->req->id = $arr["obj_inst"]->prop("company");

				$employees_view->set_request($this->req);
			}

			$fn = "_get_{$data["name"]}";
			if (method_exists($employees_view, $fn))
			{//arr($data["name"]);
				$params = array();
				$params["obj_inst"] = obj($arr["obj_inst"]->prop("company"));
				$params["prop"] =&$arr["prop"];
				$params["request"] =&$arr["request"];
	//			arr($params["prop"]);
				return $employees_view->$fn($params);
			}
		}

		switch($data["name"])
		{
			case "es_age":
				$data["value"] = '
				<input type="text" value="'.(empty($arr["request"]["es_agefrom"]) ? "" : $arr["request"]["es_agefrom"]).'" size="4" name="es_agefrom" id="es_agefrom"> - 
				<input type="text" value="'.(empty($arr["request"]["es_ageto"]) ? "" : $arr["request"]["es_ageto"]).'" size="4" name="es_ageto" id="es_ageto">';
				break;
			case "es_agefrom":
			case "es_ageto":
				return PROP_IGNORE;
		}
		return $retval;
	}

	function callback_get_default_group($arr)
	{
		$o = obj($arr["request"]["id"]);
		if($o->prop("company"))
		{
			return "employees_management";
		}
		else
		{
			return "general";
		}
	}

	function callback_mod_retval(&$arr)
	{//arr($arr['request']);die();
		foreach($this->search_props as $prop)
		{
			if(!empty($arr['request'][$prop]))
			{
				$arr['args'][$prop] = $arr['request'][$prop];
			}
			else
			{
				$arr['args'][$prop] = "";
			}
		}
	}

	function set_property($arr)
	{
		$data = &$arr['prop'];

		if ("employees_management" === $this->use_group)
		{
			static $employees_view;
			if (!$employees_view)
			{
				$employees_view = new crm_company_employees_view();
				$employees_view->set_request($this->req);
			}

			$fn = "_set_{$data["name"]}";
			if (method_exists($employees_view, $fn))
			{
				$params = array();
				$params["obj_inst"] = obj($arr["obj_inst"]->prop("company"));
				$params["prop"] =&$arr["prop"];
				$params["request"] =&$arr["request"];
				return $employees_view->$fn($params);
			}
		}
		return PROP_OK;
	}


	/**
		@attrib name=cut
	**/
	function cut($arr)
	{//arr($arr);die();
		$employees_view = new crm_company_employees_view();
		$employees_view->set_request($this->req);
		$r = $employees_view->cut($arr);
		return $r;
	}

	/**
		@attrib name=paste all_args=1
	**/
	function paste($arr)
	{
		$employees_view = new crm_company_employees_view();
		$employees_view->set_request($this->req);
		$manager = obj($arr["id"]);
		$arr["id"] = $manager->prop("company");
		$r = $employees_view->paste($arr);
		return $r;
	}

	/**
		Ends selected work relations, if
		@attrib name=submit_delete_relations
		@param id required type=int acl=view
		@param post_ru required type=string
		@param cat optional type=int
			Profession oid. Delete only relations with that profession
		@param check optional type=array
			Array of person object id-s
	**/
	function submit_delete_relations($arr)
	{
		try
		{
			$manager = obj($arr['id']);
			$this_o = obj($manager->prop("company"), array(), CL_CRM_COMPANY);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
			return $arr["post_ru"];
		}

		if (isset($arr["check"]) and is_array($arr["check"]))
		{
			$failed_person_oids = array();
			$profession = null;
			foreach($arr['check'] as $person_oid)
			{
				try
				{
					$person = obj($person_oid, array(), crm_person_obj::CLID);
					if (!empty($arr["cat"]) and is_oid($arr["cat"])) $profession = obj($arr["cat"], array(), CL_CRM_PROFESSION);

					$work_relations = crm_person_work_relation_obj::find($person, $profession, $this_o);
					if($work_relations->count())
					{
						$work_relation = $work_relations->begin();

						do
						{
							if (!$work_relation->is_finished())
							{
								$this_o->finish_work_relation($work_relation);
							}
						}
						while ($work_relation = $work_relations->next());
					}
				}
				catch (Exception $e)
				{
					/*~AWdbg*/ if (aw_ini_get("debug_mode")) { echo nl2br($e); exit; }
					$failed_person_oids[] = $person_oid;
				}
			}

			if (count($failed_person_oids))
			{
				$this->show_error_text(t("Osa valitud isikuid polnud loetavad."));
			}
			else
			{
				$this->show_completed_text(t("Valitud isikutega antud ameti all t&ouml;&ouml;suhted l&otilde;petatud."));
			}
		}

		return $arr["post_ru"];
	}

	function callback_mod_layout(&$arr)
	{
		if ($arr["name"] == "unit_list_container")
		{
			if($arr["obj_inst"]->prop("company"))
			{
				$company = obj($arr["obj_inst"]->prop("company"));


				$arr["area_caption"] = sprintf(t('Organisatsiooni "%s" üksused'), $company->name());
			}
		}
		return true;
	}
}

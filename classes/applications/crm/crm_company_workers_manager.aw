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
	@property es_c type=hidden store=request

	@property hrm_toolbar type=toolbar no_caption=1 store=no
	@caption T&ouml;&ouml;tajatehalduse tegevused

	@layout hrm_main_container type=hbox width=20%:80%
		@layout hrm_query_container type=vbox parent=hrm_main_container

			@layout hrm_tree_container type=vbox parent=hrm_query_container closeable=1 area_caption=Organisatsiooni&nbsp;struktuur
				@property organization_structure_tree type=treeview store=no parent=hrm_tree_container no_caption=1
				@caption Organisatsiooni struktuur

			@layout hrm_search_container type=vbox parent=hrm_query_container closeable=1 area_caption=Otsing
				@layout search_params_container type=vbox parent=hrm_search_container

					@property es_n type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Nimi

					@property es_s type=chooser orient=vertical store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;suhte staatus


					@property es_a type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Aadress

					@property es_e type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption E-Post

					@property es_agefrom type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus alates

					@property es_ageto type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus kuni

					@property es_g type=chooser orient=vertical store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;taja sugu

				@layout search_submit_container type=hbox parent=hrm_search_container

					@property es_sbt type=submit size=15 store=no parent=search_submit_container no_caption=1
					@caption Otsi

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
		$this->search_props = array("es_n","es_s","es_g","es_a","es_e","es_agefrom","es_ageto");
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
	{
		$employees_view = new crm_company_employees_view();
		$employees_view->set_request($this->req);
		$r = $employees_view->cut($arr);
		return $r;
	}

	/**
		@attrib name=paste all_args=1
	**/
	function paste($arr)
	{arr($arr);die();
		$employees_view = new crm_company_employees_view();
		$employees_view->set_request($this->req);
		$manager = obj($arr["id"]);
		$arr["id"] = $manager->prop("company");
		$r = $employees_view->paste($arr);
		return $r;
	}

}

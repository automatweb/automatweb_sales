<?php
// trademark_manager.aw - Kaubam&auml;rgitaotluse keskkond
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize
#GENERAL
	@property not_verified_menu type=relpicker reltype=RELTYPE_NOT_VERIFIED_MENU
	@caption Kinnitamata taotluste kaust

	@property verified_menu type=relpicker reltype=RELTYPE_VERIFIED_MENU
	@caption Kinnitatud taotluste kaust

	@property patent_add type=relpicker reltype=RELTYPE_ADD
	@caption Patenditaotluste lisamine

	@property utility_model_add type=relpicker reltype=RELTYPE_ADD
	@caption Kasuliku mudeli taotluste lisamine

	@property industrial_design_add type=relpicker reltype=RELTYPE_ADD
	@caption T&ouml;&ouml;stusdisaini taotluste lisamine

	@property euro_patent_et_desc_add type=relpicker reltype=RELTYPE_ADD
	@caption EP patendi taotluste lisamine

	@property trademark_add type=relpicker reltype=RELTYPE_ADD
	@caption Kaubam&auml;rgitaotluste lisamine

	@property admins type=relpicker reltype=RELTYPE_ADMIN multiple=1
	@caption Halduskeskkonna administraatorid

	@property procurators_folder type=relpicker reltype=RELTYPE_PROCURATORS_FOLDER
	@caption Volinike kaust


#TAOTLUSED
@groupinfo name=applications caption=Taotlused
@default group=applications

	@property objects_tb type=toolbar no_caption=1 store=no

	@layout objects_lay type=hbox width=20%:80%

		@layout objects_l type=vbox parent=objects_lay

			@layout trademark_tr_l type=vbox parent=objects_l closeable=1 area_caption=Taotluste&nbsp;puu
				@property trademark_tr type=treeview no_caption=1 store=no parent=trademark_tr_l
			@layout objects_find_params type=vbox parent=objects_l closeable=1 area_caption=Objektide&nbsp;otsing
				@property trademark_find_applicant_name type=textbox store=no parent=objects_find_params captionside=top size=30
				@caption Esitaja nimi

				@property trademark_find_procurator_name type=textbox store=no size=30 parent=objects_find_params captionside=top
				@caption Voliniku nimi

				@property trademark_find_start type=date_select store=no parent=objects_find_params captionside=top
				@caption Alates

				@property trademark_find_end type=date_select store=no parent=objects_find_params captionside=top
				@caption Kuni

				@property do_find_applications type=submit store=no parent=objects_find_params captionside=top no_caption=1
				@caption Otsi
		@property objects_tbl type=table no_caption=1 store=no parent=objects_lay


#EKSPORT
@groupinfo name=export caption=Eksport
@default group=export

	@property exp_dest type=textbox
	@caption Ekspordifaili asukoht serveris

	@property exp_link type=text
	@caption Ekspordi

#VOLINIKUD
@groupinfo name=procurators caption=Volinikud
@default group=procurators
	@property procurators_toolbar type=toolbar no_caption=1
	@caption Volinike t&ouml;&ouml;riistariba

	@property procurators_table type=table no_caption=1
	@caption Volinike tabel

#RELTYPES

	@reltype NOT_VERIFIED_MENU clid=CL_MENU value=1
	@caption Kinnitamata taotluste kaust

	@reltype VERIFIED_MENU clid=CL_MENU value=2
	@caption Kinnitatud taotluste kaust

	@reltype PROCURATORS_FOLDER clid=CL_MENU value=6
	@caption Volinike kaust

	@reltype ADD clid=CL_DOCUMENT value=4
	@caption Taotluste lisamine

	@reltype ADMIN clid=CL_GROUP value=5
	@caption Adminn

*/

class trademark_manager extends class_base
{
	const XML_OUT_ENCODING = "ISO-8859-1";
	const XML_IN_ENCODING = "ISO-8859-4";

	public $ip_classes = array(); // intellectual property classes. class_id => human readable name

	private $ip_index = array( // intellectual property class_id => folder prop name
			CL_PATENT => "trademark_add",
			CL_PATENT_PATENT => "patent_add",
			CL_UTILITY_MODEL => "utility_model_add",
			CL_INDUSTRIAL_DESIGN => "industrial_design_add",
			CL_EURO_PATENT_ET_DESC => "euro_patent_et_desc_add"
		);

	function trademark_manager()
	{
		$this->init(array(
			"tpldir" => "applications/patent",
			"clid" => CL_TRADEMARK_MANAGER
		));

		$this->ip_classes = array(
			CL_PATENT => t("Kaubam&auml;rk"),
			CL_PATENT_PATENT => t("Patent"),
			CL_UTILITY_MODEL => t("Kasulik mudel"),
			CL_INDUSTRIAL_DESIGN => t("T&ouml;&ouml;stusdisain"),
			CL_EURO_PATENT_ET_DESC => t("EP patent")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "objects_tb":
				$this->_objects_tb($arr);
				break;
			case "objects_tbl":
				$this->_objects_tbl($arr);
				break;

			case "trademark_find_applicant_name":
			case "trademark_find_procurator_name":
			case "trademark_find_start":
			case "trademark_find_end":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = isset($search_data[$prop["name"]]) ? $search_data[$prop["name"]] : "";
				break;
			case "exp_link":
				$prop["value"] = html::href(array(
					"url" =>  $this->mk_my_orb("nightly_export", array(), "trademark_manager"),
					"caption" => t("EKSPORDI!")
				));
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "trademark_find_applicant_name":
				$arr["obj_inst"]->set_meta("search_data" , $arr["request"]);
			break;
		}
		return $retval;
	}

	public function callback_on_load($arr)
	{
		if (isset($arr["request"]["group"]) and $arr["request"]["group"] === "procurators" and acl_base::can("view", $arr["request"]["id"]))
		{
			$o = new object($arr["request"]["id"]);
			$folder = $o->prop("procurators_folder");

			foreach ($this->ip_index as $clid => $value)
			{
				try
				{
					$brother_folder = $this->get_procurator_folder_oid($o, $clid);

					if ($folder === $brother_folder)
					{
						exit(t("Main procurators' folder can't be same as procurators' folder for specific application type"));
					}
				}
				catch (Exception $e)
				{
				}
			}
		}
	}

	public function _get_procurators_toolbar($arr)
	{
		$return = PROP_OK;
		$tb = $arr["prop"]["vcl_inst"];

		$procurators_folder = $arr["obj_inst"]->prop("procurators_folder");
		if (acl_base::can("view", $procurators_folder))
		{
			$add_procurator_url = $this->mk_my_orb("new",array(
				"parent" => $procurators_folder,
				"return_url" => get_ru()
			), "crm_person");
			$tb->add_button(array(
				'name' => 'add',
				'img' => 'new.gif',
				'tooltip' => t('Lisa volinik'),
				'url' => $add_procurator_url
			));
		}

		$tb->add_button(array(
			'name' => 'save',
			'action' => 'submit',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta')
		));

		$tb->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => 'delete_procurators',
			'confirm' => t("Kas oled kindel, et soovid valitud volinikud s&uuml;steemist kustudada?")
		));
		return $return;
	}

	public function _get_procurators_table($arr)
	{
		$return = PROP_OK;
		$procurators_folder = $arr["obj_inst"]->prop("procurators_folder");

		if (!acl_base::can("view", $procurators_folder))
		{
			$arr["prop"]["error"] = t("Volinike kaust m&auml;&auml;ramata v&otilde;i puudub &otilde;igus seda vaadata");
			$return = PROP_ERROR;
			return $return;
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->init_procurators_table($t);

		$procurators = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"parent" => $procurators_folder,
			"site_id" => array(),
			"lang_id" => array()
		));

		$procurators_data = array();
		$folders = array();

		foreach ($this->ip_classes as $clid => $name)
		{
			try
			{
				$folders[$clid] = $this->get_procurator_folder_oid($arr["obj_inst"], $clid);

				// get procurators for this ip type
				$procurators_data[$clid] = array();
				$procurators_tmp = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"parent" => $folders[$clid],
					"site_id" => array(),
					"lang_id" => array()
				));
				$procurators_tmp = $procurators_tmp->arr();
				foreach ($procurators_tmp as $oid => $o)
				{
					$procurators_data[$clid][] = $o->brother_of();
				}
			}
			catch (Exception $e)
			{
			}
		}

		// fill table
		foreach ($procurators->arr() as $oid => $procurator)
		{
			$args = array(
				"name" => $procurator->name(),
				"oid" => $oid
			);

			foreach ($this->ip_classes as $clid => $name)
			{ // checkboxes for all ip types
				$is_applicable = in_array($oid, $procurators_data[$clid]);

				if ($folders[$clid])
				{
					$args["class" . $clid] = html::checkbox(array(
						"name" => "pat_procurator_{$clid}[" . $oid . "]",
						"value" => 1,
						"checked" => $is_applicable,
					));
				}
			}

			$t->define_data($args);
		}
		return $return;
	}

	private function init_procurators_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Volinik"),
			"sortable" => 1
		));

		foreach ($this->ip_classes as $clid => $name)
		{
			$t->define_field(array(
				"name" => "class" . $clid,
				"caption" => "<a href='javascript:selall(\"pat_procurator_{$clid}\")'>{$name}</a>",
				"align" => "center"
			));
		}

		$t->define_chooser(array(
			"caption" => t("Vali"),
			"field" => "oid",
			"name" => "sel"
		));
	}

	public function _set_procurators_table($arr)
	{
		$return = PROP_OK;

		foreach ($this->ip_classes as $clid => $cl_name)
		{
			try
			{
				$folder = $this->get_procurator_folder_oid($arr["obj_inst"], $clid);
				$procurator_brothers = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"parent" => $folder,
					"site_id" => array(),
					"lang_id" => array()
				));
				$procurator_brothers = $procurator_brothers->arr();

				// remove procurator from this ip type
				foreach ($procurator_brothers as $procurator_brother_oid => $procurator_brother)
				{
					$procurator = $procurator_brother->get_original();
					if (!array_key_exists($procurator->id(), $arr["request"]["pat_procurator_{$clid}"]))
					{
						$procurator_brother->delete();
					}
				}

				// add
				foreach ($arr["request"]["pat_procurator_{$clid}"] as $procurator_oid => $value)
				{
					if (acl_base::can("view", $procurator_oid))
					{
						$procurator = new object($procurator_oid);
						$procurator->create_brother($folder);
					}
				}
			}
			catch (Exception $e)
			{
			}
		}

		return $return;
	}

/*
- vasakus puus: Kinnitamata taotlused, Kinnitatud taotlused

*/
	function _get_trademark_tr($arr)
	{


		$arr["prop"]["vcl_inst"]->start_tree (array (
			"type" => TREE_DHTML,
			"has_root" => 1,
			"tree_id" => "offers_tree",
			"persist_state" => 1,
			"root_name" => t("Taotlused"),
			"root_url" => "#",
//			"get_branch_func" => $this->mk_my_orb("get_tree_stuff",array(
//				"clid" => $arr["clid"],
//				"group" => $arr["request"]["group"],
//				"oid" => $arr["obj_inst"]->id(),
//				"set_retu" => get_ru(),
//				"parent" => " ",
//			)),
		));

		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 1,
			"name" => t('Kinnitatud'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => 11,
			"name" => t('Kaubam&auml;rk'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
				"p_cl" => "tm"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => 12,
			"name" => t('Patent'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
				"p_cl" => "pat"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => 13,
			"name" => t('Kasulik mudel'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
				"p_cl" => "um"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => 14,
			"name" => t('T&ouml;&ouml;stusdisain'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
				"p_cl" => "ind"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => 15,
			"name" => t('EP patent'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "verified",
				"p_cl" => "epat"
			), "trademark_manager"),
		));

		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 2,
			"name" => t('Arhiiv'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => 21,
			"name" => t('Kaubam&auml;rk'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
				"p_cl" => "tm"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => 22,
			"name" => t('Patent'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
				"p_cl" => "pat"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => 23,
			"name" => t('Kasulik mudel'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
				"p_cl" => "um"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => 24,
			"name" => t('T&ouml;&ouml;stusdisain'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
				"p_cl" => "ind"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => 25,
			"name" => t('EP patent'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "archive",
				"p_cl" => "epat"
			), "trademark_manager"),
		));
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 3,
			"name" => t('Kinnitamata'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "applications",
				"p_id" => "not_verified",
			), "trademark_manager"),
		));

		$sel = "0";

		if (!empty($arr["request"]["p_id"]))
		{
			if ("verified" === $arr["request"]["p_id"])
			{
				$sel = "1";
			}
			elseif ("archive" === $arr["request"]["p_id"])
			{
				$sel = "2";
			}
			elseif ("not_verified" === $arr["request"]["p_id"])
			{
				$sel = "3";
			}
		}

		if (!empty($arr["request"]["p_cl"]))
		{
			if ("tm" === $arr["request"]["p_cl"])
			{
				$sel .= "1";
			}
			elseif ("pat" === $arr["request"]["p_cl"])
			{
				$sel .= "2";
			}
			elseif ("um" === $arr["request"]["p_cl"])
			{
				$sel .= "3";
			}
			elseif ("ind" === $arr["request"]["p_cl"])
			{
				$sel .= "4";
			}
			elseif ("epat" === $arr["request"]["p_cl"])
			{
				$sel .= "5";
			}
		}

		$arr["prop"]["vcl_inst"]->set_selected_item((int) $sel);
	}

	function search_applications($this_obj)
	{
		$data = $this_obj->meta("search_data");
		$ol = new object_list();
		$applicant_name = empty($data["trademark_find_applicant_name"]) ? null : "%".$data["trademark_find_applicant_name"]."%";
		$procurator_name = empty($data["trademark_find_procurator_name"]) ? null : "%".$data["trademark_find_procurator_name"]."%";
		$filter = array(
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array (
							"class_id" => array(CL_PATENT_PATENT),
							"CL_PATENT_PATENT.RELTYPE_APPLICANT.name" => $applicant_name,
							"CL_PATENT_PATENT.RELTYPE_PROCURATOR.name" => $procurator_name,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array (
							"class_id" => array(CL_UTILITY_MODEL),
							"CL_UTILITY_MODEL.RELTYPE_APPLICANT.name" => $applicant_name,
							"CL_UTILITY_MODEL.RELTYPE_PROCURATOR.name" => $procurator_name,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array (
							"class_id" => array(CL_PATENT),
							"CL_PATENT.RELTYPE_APPLICANT.name" => $applicant_name,
							"CL_PATENT.RELTYPE_PROCURATOR.name" => $procurator_name,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array (
							"class_id" => array(CL_INDUSTRIAL_DESIGN),
							"CL_INDUSTRIAL_DESIGN.RELTYPE_APPLICANT.name" => $applicant_name,
							"CL_INDUSTRIAL_DESIGN.RELTYPE_PROCURATOR.name" => $procurator_name,
						)
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array (
							"class_id" => array(CL_EURO_PATENT_ET_DESC),
							"CL_EURO_PATENT_ET_DESC.RELTYPE_APPLICANT.name" => $applicant_name,
							"CL_EURO_PATENT_ET_DESC.RELTYPE_PROCURATOR.name" => $procurator_name,
						)
					)),
				)
			)),
			"lang_id" => array(),
			"site_id" => array()
		);

 		if((date_edit::get_timestamp($data["trademark_find_start"]) > 1)|| (date_edit::get_timestamp($data["trademark_find_end"]) > 1))
 		{
 			if(date_edit::get_timestamp($data["trademark_find_start"]) > 1)
 			{
 				$from = date_edit::get_timestamp($data["trademark_find_start"]);
 			}
 			else
 			{
 				$from = 1;
 			}
 			if(date_edit::get_timestamp($data["trademark_find_end"]) > 1)
 			{
 				$to = date_edit::get_timestamp($data["trademark_find_end"])+(24*3600);
 			}
 			else
 			{
 				$to = time()*66;
 			}
 		 	$filter["created"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, ($from - 1), ($to + 1));
 		}
		$ol = new object_list($filter);
		return $ol;
	}

	function _objects_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_objects_tbl($t);

		$p_id = isset($arr["request"]["p_id"]) ? $arr["request"]["p_id"] : "";
		$verified = ($p_id === "verified") ? 1 : null;
		$cl = isset($arr["request"]["p_cl"]) ? $arr["request"]["p_cl"] : "";
		$archive_age = time() - 3*30*86400;

		if ($p_id === "archive")
		{ // applications verified before spec time or verified and having no verifying time set
			$date_constraint1 = new obj_predicate_compare(obj_predicate_compare::LESS, $archive_age);
			$date_constraint2 = new obj_predicate_compare(obj_predicate_compare::IS_NULL);
			$sent_constraint = null;
			$verified = 1;
		}
		elseif ("verified" === $p_id)
		{ // recently verified
			$date_constraint1 = new obj_predicate_compare(obj_predicate_compare::GREATER_OR_EQ, $archive_age);
			$date_constraint2 = null;
			$sent_constraint = null;
			$verified = 1;
		}
		elseif ("not_verified" === $p_id)
		{ // only sent applications. those that have a number
			$date_constraint1 = null;
			$date_constraint2 = null;
			$sent_constraint = new obj_predicate_compare(obj_predicate_compare::GREATER, 1);
			$verified = new obj_predicate_compare(obj_predicate_compare::IS_EMPTY);
		}
		else
		{
			return;
		}

		if ("tm" === $cl)
		{
			$filter = array(
				"class_id" => array(CL_PATENT),
				"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
							)
						)) ,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
							)
						))
					)
				))
			);
		}
		elseif ("pat" === $cl)
		{
			$filter = array(
				"class_id" => array(CL_PATENT_PATENT),
				"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
							)
						)) ,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
							)
						))
					)
				))
			);
		}
		elseif ("um" === $cl)
		{
			$filter = array(
				"class_id" => array(CL_UTILITY_MODEL),
				"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
							)
						)) ,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
							)
						))
					)
				))
			);
		}
		elseif ("ind" === $cl)
		{
			$filter = array(
				"class_id" => array(CL_INDUSTRIAL_DESIGN),
				"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
							)
						)) ,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
							)
						))
					)
				))
			);
		}
		elseif ("epat" === $cl)
		{
			$filter = array(
				"class_id" => array(CL_EURO_PATENT_ET_DESC),
				"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
							)
						)) ,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
							)
						))
					)
				))
			);
		}
		else
		{
			$filter = array(
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"class_id" => array(CL_PATENT_PATENT),
								"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.nr" => $sent_constraint,
								"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array (
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
											)
										)) ,
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_PATENT_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
											)
										))
									)
								)) ,
							)
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"class_id" => array(CL_UTILITY_MODEL),
								"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
								"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.nr" => $sent_constraint,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array (
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
											)
										)) ,
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_UTILITY_MODEL.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
											)
										))
									)
								)) ,
							)
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"class_id" => array(CL_PATENT),
								"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
								"CL_PATENT.RELTYPE_TRADEMARK_STATUS.nr" => $sent_constraint,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array (
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
											)
										)) ,
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_PATENT.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
											)
										))
									)
								)) ,
							)
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"class_id" => array(CL_INDUSTRIAL_DESIGN),
								"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
								"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.nr" => $sent_constraint,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array (
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
											)
										)) ,
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_INDUSTRIAL_DESIGN.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
											)
										))
									)
								)) ,
							)
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array (
								"class_id" => array(CL_EURO_PATENT_ET_DESC),
								"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified" => $verified,
								"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.nr" => $sent_constraint,
								new object_list_filter(array(
									"logic" => "OR",
									"conditions" => array (
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint1,
											)
										)) ,
										new object_list_filter(array(
											"logic" => "AND",
											"conditions" => array (
												"CL_EURO_PATENT_ET_DESC.RELTYPE_TRADEMARK_STATUS.verified_date" => $date_constraint2
											)
										))
									)
								)) ,
							)
						)),
					)
				)),
				"sort_by" => "objects.created DESC"
			);
		}

		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_applications($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
			$ol->sort_by(array(
				"prop" => "created",
				"order" => "desc"
			));
		}
		else
		{
			$ol = new object_list($filter);
		}


		$trademark_inst = get_instance(CL_PATENT);
		$person_inst = get_instance(CL_CRM_PERSON);
		$types = $trademark_inst->types;

		foreach($ol->arr() as $o)
		{
			$re = $trademark_inst->is_signed($o->id());
			$status = $trademark_inst->get_status($o);

			if($p_id === "not_verified" && ($status->prop("verified") || (!($re["status"] == 1))))
			{
				continue;
			}

			$procurator = $type = $nr = $applicant_name = $applicant_data = $applicant = "";
			$procurator = $o->prop_str("procurator");

			if(acl_base::can("" , $o->prop("warrant")))
			{
				$file_inst = get_instance(CL_FILE);
				$procurator = html::href(array(
					"caption" => $procurator,
					"url" => "#",//html::get_change_url($o->id(), array("return_url" => $arr["post_ru"])),
					"onclick" => 'javascript:window.open("'.$file_inst->get_url($o->prop("warrant")).'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
				));
			}

			if (CL_PATENT === $o->class_id())
			{
				$type = $types[$o->prop("type")];
				if($o->prop("type") == 0 && $o->prop("word_mark"))
				{
					$type.= " (".$o->prop("word_mark").")";
				}
			}

			$nr_str = t("Number puudub");
			if($status->prop("nr"))
			{
				$nr_str = $status->prop("nr");
			}

			$class = basename(aw_ini_get("classes.". $o->class_id().".file"));
			$nr = html::href(array(
				"caption" => $nr_str,
				"url" => "#",//html::get_change_url($o->id(), array("return_url" => $arr["post_ru"])),
				// "onclick" => 'javascript:window.open("'.aw_ini_get("baseurl").'/?class='.$class.'&action=show&print=1&sent_form=1&id='.$o->id(). '","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
				"onclick" => 'javascript:window.open("'.aw_ini_get("baseurl").$o->id(). '?print=1","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
			));

			$applicant = $o->get_first_obj_by_reltype("RELTYPE_APPLICANT");

			if(is_object($applicant))
			{
				$applicant_name = $trademark_inst->get_applicants_str($o);//$applicant->name();
				$applicant_data = "";
				if($applicant->class_id() == CL_CRM_PERSON)
				{
					$applicant_data = $person_inst->get_short_description($applicant->id());
				}
				else
				{
					$stuff = array();
					$stuff[] = html::obj_change_url($applicant);
					if(is_object($a_phone = $applicant->get_first_obj_by_reltype("RELTYPE_PHONE")))
					{
						$stuff[] = $a_phone->name();
					}

					if(is_object($a_mail = $applicant->get_first_obj_by_reltype("RELTYPE_EMAIL")))
					{
						$stuff[] = $a_mail->name();
					}
					$applicant_data = join("," , $stuff);
				}
			}

			if($status->prop("sent_date"))
			{
				$date = $status->prop("sent_date");
			}
			else
			{
				$date = $o->created();
			}

			$retval = "";
			if($re["status"] == 1)
			{
				$signatures_url = $this->mk_my_orb("change", array("group" => "signatures", "id" => $re["ddoc"]), CL_DDOC);
				$retval = html::href(array(
					"url" => $signatures_url,
					"target" => "new window",
					//"url" => "#",
					"caption" => t("Allkirjad"),
					// "title" => $title,
					//"onclick" => 'javascript:window.open("'.$signatures_url.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
				));
			}

			try
			{
				$class = aw_ini_get("classes." . $o->class_id() . ".name");
			}
			catch (Exception $e)
			{
				$class = "N/A";
			}

			$t->define_data(array(
				"class" => $class,
				"procurator" => $procurator,
				"nr" => $nr,
				"type" => $type,
				"applicant_name" => $applicant_name,
				"applicant_data" => $applicant_data,
				"date" => $date,
				"oid" => $o->id(),
				"signatures" => $retval,
				"verify" => ($status->prop("verified")) ? "" : html::href(array(
					"caption" => t("Kinnita"),
					"url" => "#",
					"onclick" => 'javascript:window.open("'.
						$this->mk_my_orb("verify",array(
							"popup" => 1,
							"sel" => array($o->id() => $o->id()),
							"id" => $arr["obj_inst"]->id(),
						), "trademark_manager")
					.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
				)),
			));
		}
	}

/*
- paremal tabelis: M2rgi tyyp (s5nam2rk, kujutism2rk jne, kui s6nam2rk, siis vastava tekstiv2lja sisu ka sulgudes), Taotluse number (sellel klikkides avaneb ka taotluse sisestusvorm, kui number puudub, siis on klikitav tekst Number puudub), Esitaja nimi, Esitaja kontaktandmed (k6ik yhes v2ljas komaga eraldatult, aadressi pole vaja), voliniku nimi, Esitamise kuup2ev, Vali tulp.
*/
	function _init_objects_tbl($t)
	{
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Taotluse t&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1,
			"filter" => empty($_GET["p_cl"]) ? "automatic" : null
		));

		$t->define_field(array(
			"name" => "type",
			"caption" => t("M&auml;rgi t&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Taotluse number"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "applicant_name",
			"caption" => t("Esitaja nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "applicant_data",
			"caption" => t("Esitaja kontaktandmed"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "procurator",
			"caption" => t("Voliniku nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Esitamise kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
		));

		$t->define_field(array(
			"name" => "signatures",
			"caption" => t("Allkirjad"),
			"align" => "center",
//			"sortable" => 1
		));

		$t->define_chooser(array(
			"caption" => t("Vali"),
			"field" => "oid",
			"name" => "sel"
		));

		if(!isset($_GET["p_id"]) or $_GET["p_id"] !== "verified")
		{
			$t->define_field(array(
				"name" => "verify",
				"caption" => t("Kinnita"),
			));
		}

		$t->define_pageselector (array (
			"type" => "lbtxt",
			"records_per_page" => 25,
		));
	}

	function _objects_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "add_item",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus")
		));

		if (is_oid($arr["obj_inst"] ->prop("trademark_add")))
		{
			$add_trademark_url = aw_ini_get("baseurl").$arr["obj_inst"] ->prop("trademark_add");
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text" => t("Kaubam&auml;rgitaotlus"),
				"link" => $add_trademark_url,
				"target" => "_blank"
			));
		}

		if (is_oid($arr["obj_inst"] ->prop("patent_add")))
		{
			$add_patent_url = aw_ini_get("baseurl").$arr["obj_inst"] ->prop("patent_add");
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text" => t("Patenditaotlus"),
				"link" => $add_patent_url,
				"target" => "_blank"
			));
		}

		if (is_oid($arr["obj_inst"] ->prop("utility_model_add")))
		{
			$add_utility_model_url = aw_ini_get("baseurl").$arr["obj_inst"] ->prop("utility_model_add");
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text" => t("Kasuliku mudeli taotlus"),
				"link" => $add_utility_model_url,
				"target" => "_blank"
			));
		}

		if (is_oid($arr["obj_inst"] ->prop("industrial_design_add")))
		{
			$add_industrial_design_url = aw_ini_get("baseurl").$arr["obj_inst"] ->prop("industrial_design_add");
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text" => t("T&ouml;&ouml;stusdisaini taotlus"),
				"link" => $add_industrial_design_url,
				"target" => "_blank"
			));
		}

		if (is_oid($arr["obj_inst"] ->prop("euro_patent_et_desc_add")))
		{
			$add_euro_patent_et_desc_url = aw_ini_get("baseurl").$arr["obj_inst"] ->prop("euro_patent_et_desc_add");
			$tb->add_menu_item(array(
				"parent" => "add_item",
				"text" => t("EP patendi taotlus"),
				"link" => $add_euro_patent_et_desc_url,
				"target" => "_blank"
			));
		}

		$tb->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'url' => "",
	//		'action' => 'delete_procurements',
	//		'confirm' => t(""),
		));
		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => 'delete_applications',
			'confirm' => t("Kas oled kindel et soovid valitud taotlused kustudada?"),
		));
		$tb->add_button(array(
			'name' => 'refresh',
			'img' => 'refresh.gif',
			'tooltip' => t('V&auml;rskenda'),
			'url' => "",
		//	'action' => 'delete_procurements',
		//	'confirm' => t(""),
		));
		$tb->add_button(array(
			'name' => 'verify',
			'img' => 'restore.gif',
			'tooltip' => t('Kinnita'),
			'url' => "",
			'action' => 'verify',
		//	'confirm' => t(""),
		));
	}

	/**
		@attrib name=delete_procurators
	**/
	function delete_procurators($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_applications
	**/
	function delete_applications($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=verify all_args=1
	**/
	function verify($arr)
	{
		$trademark_inst = get_instance(CL_PATENT);

		if (isset($arr["sel"]) and is_array($arr["sel"]))
		{
			foreach($arr["sel"] as $id)
			{
				$o = obj($id);
				$status = $trademark_inst->get_status($o);
				$status->set_prop("verified", 1);
				$status->set_prop("verified_date", time());
				$status->set_name(t("Taotlus nr: ".$status->prop("nr")));
				$status->save();
			}
		}

		if(!empty($arr["popup"]))
		{
			die('<script type="text/javascript">
				window.opener.location.reload();
				window.close();
				</script>'
			);
		}
		else
		{
			return $arr["post_ru"];
		}
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	function __application_sorter($a, $b)
	{
		$as = $a->get_first_obj_by_reltype("RELTYPE_TRADEMARK_STATUS");
		$bs = $b->get_first_obj_by_reltype("RELTYPE_TRADEMARK_STATUS");
		if(is_object($as) && is_object($bs))
		{
			return  $as->prop("nr") - $bs->prop("nr");
		}
		else
		{
			return  $a->id() - $b->id();
		}
	}

	/**
		@attrib name=nightly_export nologin="1"
		@param from optional type=int
			Unix timestamp. Time of modification from when to include objects. Default previous day start.
		@param to optional type=int
			Unix timestamp. Time of modification until to include objects. Default current day start
		@param test_id optional type=int acl=view
			Application object id to test exporting that object only.
	**/
	function nightly_export($arr)
	{
		$time = gmdate("Y M d H:i:s");
		echo <<<HEADER



=============================================================
Starting export at {$time}

HEADER;

		$xml_data = array(); // array of DOMDocuments grouped by aw class id

		if (empty($arr["test_id"]))
		{
			$clidx = array(
				CL_PATENT => "kaubam2rgid_",
				CL_PATENT_PATENT => "patendid_",
				CL_UTILITY_MODEL => "kasulikudmudelid_",
				CL_INDUSTRIAL_DESIGN => "t88stusdisainid_",
				CL_EURO_PATENT_ET_DESC => "europatendid_"
			);
			$clidx2 = array(
				CL_PATENT => "CL_PATENT",
				CL_PATENT_PATENT => "CL_PATENT_PATENT",
				CL_UTILITY_MODEL => "CL_UTILITY_MODEL",
				CL_INDUSTRIAL_DESIGN => "CL_INDUSTRIAL_DESIGN",
				CL_EURO_PATENT_ET_DESC => "CL_EURO_PATENT_ET_DESC"
			);

			$from = !empty($arr["from"]) ? (int) $arr["from"] : (date_calc::get_day_start()-(24*3600));
			$to = !empty($arr["to"]) ? (int) $arr["to"] : date_calc::get_day_start();

			if ($from >= $to)
			{
				throw new awex_po("Invalid arguments. Timespan end less than start.");
			}

			// list all intellectual prop objs created yesterday
			$date_constraint = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$us = get_instance("users");
			$us->login(array ("uid" => "struktuur", "password" => "autojuurutus"));//FIXME: !!!

			// parse objs
			foreach ($clidx as $clid => $value)
			{
				$filter = array(
					"class_id" => $clid,
					$clidx2[$clid] . ".RELTYPE_TRADEMARK_STATUS.verified" => 1,
					$clidx2[$clid] . ".RELTYPE_TRADEMARK_STATUS.exported" => new obj_predicate_not(1)
					// $clidx2[$clid] . ".RELTYPE_TRADEMARK_STATUS.modified" => $date_constraint
				);

				$ol = new object_list($filter);
				$ol->sort_by_cb(array($this, "__application_sorter"));

				if ($ol->count())
				{
					$o = $ol->begin();
					$xml_data[$clid] = array(
						"data" => "",
						"count" => 0
					);

					do
					{
						// get xml from ip obj
						$inst = $o->instance();
						$xml_data[$clid]["data"] .= str_replace("<?xml version=\"1.0\" encoding=\"" . self::XML_OUT_ENCODING . "\"?>", "", $inst->get_po_xml($o)->saveXML());
						$xml_data[$clid]["count"] += 1;

						// indicate that object has been exported
						$status = $inst->get_status($o);
						$status->set_no_modify(true);
						$status->set_prop("exported", 1);
						$status->set_prop("export_date", time());
						$o->set_no_modify(true);
						aw_disable_messages();
						$status->save();
						aw_restore_messages();
					}
					while ($o = $ol->next());
				}
			}
		}
		else
		{
			// export requested object to testfile
			$o = new object($arr["test_id"]);
			$inst = $o->instance();
			$clid = $o->class_id();
			$xml_data[$clid]["data"] = str_replace("<?xml version=\"1.0\" encoding=\"" . self::XML_OUT_ENCODING . "\"?>", "", $inst->get_po_xml($o)->saveXML());
			$xml_data[$clid]["count"] = 1;
		}

		foreach ($xml_data as $clid => $data)
		{
			// xml header and contents
			$xml = "<?xml version=\"1.0\" encoding=\"" . self::XML_OUT_ENCODING . "\"?>\n";
			$xml .= '<ENOTIF BIRTHCOUNT="'.$data["count"].'" CPCD="EE" WEEKNO="'.date("W").'" NOTDATE="'.date("Ymd").'">' . "\n";
			$xml .= $data["data"];
			$xml .= "</ENOTIF>\n";

			// write file
			$cl = empty($arr["test_id"]) ? $clidx[$clid] : "test_tmp_";// file name prefix
			$date = empty($arr["to"]) ? date("Ymd") : ("_tmp" . date("Ymd", (date_calc::get_day_start($to)+(30*3600))));
			$fn = aw_ini_get("site_basedir")."patent_xml/" . $cl . $date . ".xml";

			$f = fopen($fn, "w");

			if (!is_resource($f))
			{
				echo "couldn't open {$fn}\n";
			}

			$b = fwrite($f, $xml);
			fclose($f);

			if (false === $b)
			{
				echo "error writing to {$fn}\n";
			}
			else
			{
				echo "wrote {$fn}\n";
			}
		}

		$time = gmdate("Y M d H:i:s");
		exit("{$time} Done, exiting.");
	}

	//replace reserved characters
	public static function rere($string)
	{
		$string = str_replace("&" , "&amp;" , $string);
		$string = str_replace("<" , "&lt;" , $string);
		$string = str_replace(">" , "&gt;" , $string);
		$string = str_replace("%" , "&#37;" , $string);
		$string = str_replace('"' , " &quot;" , $string);
		$string = str_replace("'" , "&apos;" , $string);
		$string = iconv(self::XML_IN_ENCODING, "UTF-8", $string);
		return $string;
	}

	public function get_procurator_folder_oid($o, $clid)
	{
		// find procurator parent folder for this ip type
		$tmp = $o->prop($this->ip_index[$clid]);

		if (!acl_base::can("view", $tmp))
		{
			throw new aw_exception("No add document defined");
		}

		$tmp = new object($tmp);
		$tmp = $tmp->connections_from(array(
			"class_id" => constant("CL_" . strtoupper($this->ip_index[$clid]))
		));

		if (!count($tmp))
		{
			throw new aw_exception("No add object defined");
		}

		$tmp = reset($tmp);
		$tmp = $tmp->to();
		$folder = $tmp->prop("procurator_menu");

		if (!is_oid($folder))
		{
			throw new aw_exception("No folder defined");
		}

		return $folder;
	}
}

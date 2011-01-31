<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_study_organisation_rate master_index=brother_of master_table=objects index=aw_oid

@default table=aw_study_organisation_rate
@default group=general

	@property ord type=textbox size=4 table=objects field=jrk
	@caption Jrk

	@property type type=chooser orient=vertical field=aw_type
	@caption Akadeemilise t&ouml;&ouml; t&uuml;&uuml;p

	@property category type=chooser orient=vertical field=aw_category
	@caption Arvestusm&auml;&auml;ra kategooria
	
	@property applicables type=hidden table=objects field=meta method=serialize

	@property publication_categories type=relpicker reltype=RELTYPE_PUBLICATION_CATEGORY multiple=1 store=connect no_edit=1
	@caption Publikatsioonide kategooriad

	@property thesis_categories type=select multiple=1 table=objects field=meta method=serialize
	@caption Oponeeritud/kaitstud tööde kategooriad

	@property years type=select multiple=1 table=objects field=meta method=serialize
	@caption Aastad

@reltype PUBLICATION_CATEGORY value=1 clid=CL_STUDY_ORGANISATION_PUBLICATION_CATEGORY
@caption Publikatsioonide kategooria

*/

class study_organisation_rate extends class_base
{
	const TEACHING = 1;
	const RESEARCH = 2;

	const CONTACT_LEARNING = 1;
	const E_LEARNING = 2;
	const THESIS_SUPERVISED = 3;
	const THESIS_OPPOSING = 4;
	const PUBLICATIONS = 5;
	const MEMBERSHIPS = 6;

	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/study_organisation/study_organisation_rate",
			"clid" => CL_STUDY_ORGANISATION_RATE
		));
		$this->type_options = array(
			self::TEACHING => t("&Otilde;ppet&ouml;&ouml;"),
			self::RESEARCH => t("Teadust&ouml;&ouml;"),
		);
		$this->category_options = array(
			self::CONTACT_LEARNING => t("Kontakt&otilde;pe"),
			self::E_LEARNING => t("E-&otilde;pe"),
			self::THESIS_SUPERVISED => t("Tööde juhendamine"),
			self::THESIS_OPPOSING => t("Tööde oponeerimine"),
			self::PUBLICATIONS => t("Publikatsioonid"),
			self::MEMBERSHIPS => t("Komisjonide liikmelisus"),
		);
		$this->thesis_category_options = array(
			"seminaritöö", "BA", "MA", "PhD"
		);
		$this->years_options = array();
		for($i = 2004; $i <= date("Y"); $i++)
		{
			$this->years_options[$i] = $i;
		}
	}

	function _get_years($arr)
	{
		$arr["prop"]["options"] = $this->years_options;
		return PROP_OK;
	}

	function _get_thesis_categories($arr)
	{
		$arr["prop"]["options"] = $this->thesis_category_options;
		return PROP_OK;
	}

	function _get_publication_categories($arr)
	{
		if($arr["obj_inst"]->is_saved())
		{
			$manager = obj($arr["obj_inst"]->parent());
		}
		else
		{
			$manager = obj(automatweb::$request->arg("parent"));
		}

		$arr["prop"]["options"] = $manager->get_publication_categories()->names();
		return PROP_OK;
	}

	function _get_type($arr)
	{
		$arr["prop"]["options"] = $this->type_options;
		return PROP_OK;
	}

	function _get_category($arr)
	{
		$arr["prop"]["options"] = $this->category_options;
		return PROP_OK;
	}

	function _get_applicables()
	{
		return PROP_IGNORE;
	}

	function _set_applicables()
	{
		return PROP_IGNORE;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_study_organisation_rate" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_study_organisation_rate` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif (in_array($field, array("aw_category", "aw_type")))
			{
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "tinyint unsigned"
				));
				$r = true;
			}
		}

		return $r;
	}
}

<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/personnel_management/personnel_management_cv_search.aw,v 1.11 2008/06/08 11:52:09 instrumental Exp $
// personnel_management_cv_search.aw - CV Otsing 
/*

@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_CV_SEARCH relationmgr=yes r2=yes no_comment=1 no_status=1 maintainer=instrumental

@default table=objects
@default group=general
@default form=cv_search
@default store=no

@property cv_name type=textbox size=18
@caption Nimi

@property cv_company type=textbox size=18
@caption Ettev&otilde;te

@property cv_job type=textbox size=18
@caption Ametinimetus

@layout pay type=hbox

@property cv_paywish type=textbox parent=pay size=6
@caption Palk alates

@property cv_paywish2 type=textbox parent=pay size=6
@caption Palk kuni

@property cv_field type=classificator multiple=1 orient=vertical
@caption Tegevusala

@property cv_type type=classificator multiple=1
@caption T&ouml;&ouml; liik

@property cv_location type=relpicker multiple=1 orient=vertical
@caption T&ouml;&ouml;tamise piirkond

@property cv_load type=classificator multiple=1 orient=vertical
@caption T&ouml;&ouml;koormus

@property cv_personality type=textbox size=18
@caption Isikuomadused

@property cv_comments type=textbox size=18
@caption Kommentaarid

@property cv_recommenders type=textbox size=18
@caption Soovitajad

@property cv_mother_tongue type=classificator
@caption Emakeel

@property cv_lang_exp type=classificator multiple=1
@caption Keeleoskus

@property cv_pc_exp type=textbox size=18
@caption Arvutioskus

@property cv_gender type=textbox size=18
@caption Sugu

@layout cv_age type=hbox

	@property cv_age_from type=textbox parent=cv_age size=4
	@caption Vanus alates

	@property cv_age_to type=textbox parent=cv_age size=4
	@caption Vanus kuni

@property cv_previous_rank type=textbox size=18
@caption T&ouml;&ouml;kogemuse ametinimetus

@property cv_driving_licence type=textbox size=18
@caption Juhiload

@property cv_search_button type=submit value=Otsi
@caption Otsi

#@property cv_search_results type=table no_caption=1
#@caption Otsingutulemused 

@property no_reforb type=hidden value=1

@forminfo cv_search onload=init_search onsubmit=do_search method=get

*/

class personnel_management_cv_search extends class_base
{	
	function personnel_management_cv_search()
	{
		$this->init(array(
			"tpldir" => "applications/personnel_management/personnel_management_cv_search",
			"clid" => CL_PERSONNEL_MANAGEMENT_CV_SEARCH
		));
	}

	function test($arr)
	{
	}

	function init_search($arr)
	{
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cv_name":
			case "cv_company":
			case "cv_job":
			case "cv_paywish":
			case "cv_paywish2":
			case "cv_personality":
			case "cv_comments":
			case "cv_recommenders":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "cv_load":
				//$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "cv_location":
				//$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "cv_type":
				//$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "cv_field":
				//$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "cv_search_results":
				$this->do_sres_tbl($arr);
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}
	
	function do_sres_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_sres_tbl($t);
		$t->define_data(array(
			"name" => html::href(array(
				"url" => "je",
				"caption" => t("je"),
			))
		));
	}

	function _init_sres_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));
	}
}
?>

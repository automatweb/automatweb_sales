<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/scm_admin.aw,v 1.5 2007/12/06 14:34:06 kristo Exp $
// scm_admin.aw - Spordiv&otilde;istluste haldus 
/*

@classinfo syslog_type=ST_SCM_ADMIN relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize


@groupinfo contestants caption="V&otilde;istlejad" submit=no
	
	@default group=contestants

	@property cont_tb type=toolbar no_caption=1
	@caption Osalejate t66tiistariba

	@property cont_tbl type=table no_caption=1
	@caption Osalejate nimekiri

@groupinfo organizers caption="Korraldajad" submit=no

	@default group=organizers
	
	@property org_tb type=toolbar no_caption=1
	@caption Korraldajate t66riistariba

	@property org_tbl type=table no_caption=1
	@caption Korraldajate nimekiri
*/

class scm_admin extends class_base
{
	const AW_CLID = 1090;

	function scm_admin()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_admin",
			"clid" => CL_SCM_ADMIN
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "cont_tb":
				$tb = &$prop["vcl_inst"];
				
				$url = $this->mk_my_orb("gen_new_contestant_sheet",array(
					"parent" => $arr["obj_inst"]->id(),
					"do_not_register" => true,
				),CL_SCM_CONTESTANT);

				$tb->add_button(array(
					"name" => "new_contestant",
					"tooltip" => t("Lisa uus v&otilde;istleja"),
					"img" => "new.gif",
					"url" => "javascript:aw_popup_scroll('".$url."', 'title', 500,400);",
					/*
					"url" => $this->mk_my_orb("new",array(
						"class" => "scm_contestant",
						"parent" => $arr["obj_inst"]->parent(),
						"return_url" => post_ru(),
					)),
					*/
				));

			break;
			case "cont_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_tbl(&$t);
				$cont = get_instance(CL_SCM_CONTESTANT);
				foreach($cont->get_contestants() as $oid => $obj)
				{
					$comp = ($s = $cont->get_contestant_company(array("contestant" => $oid)))?obj($s):false;
					$pers = ($cont->get_contestant_person(array("contestant" => $oid)))?true:false;
					$cont_url = $this->mk_my_orb("change" ,array(
						"class" => "scm_contestant",
						"id" => $oid,
						"return_url" => get_ru(),
					));
					$link = html::href(array(
						"caption" => 
							"%s",
						"url" => "%s",
					));
					$t->define_data(array(
						"name" => sprintf($link, $cont_url, ($pers?$obj->name():t("isik m&auml;&auml;ramata"))),
						"company" => ($comp)?$comp->name():t("firma m&auml;&auml;ramata"),
					));
				}
			break;

			case "org_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "new_organizer",
					"tooltip" => t("Lisa uus korraldaja"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("new",array(
						"class" => "scm_organizer",
						"parent" => $arr["obj_inst"]->parent(),
						"return_url" => post_ru(),
					)),
				));

			break;
			case "org_tbl":
				$t = &$prop["vcl_inst"];
				$this->_gen_tbl(&$t);
				$org = get_instance(CL_SCM_ORGANIZER);
				foreach($org->get_organizers() as $oid => $obj)
				{
					$pers = ($org->get_organizer_person(array("organizer" => $oid)))?true:false;
					$comp = ($s = $org->get_organizer_company(array("organizer" => $oid)))?obj($s):false;
					
					$link = html::href(array(
						"caption" =>
							"%s",
						"url" => "%s",
					));
					$pers_url = $this->mk_my_orb("change" ,array(
						"class" => "scm_organizer",
						"id" => $oid,
						"return_url" => get_ru(),
					));
					$t->define_data(array(
						"name" => sprintf($link, $pers_url, ($pers?$obj->name():t("isik m&auml;&auml;ramata"))),
						"company" => ($comp)?$comp->name():t("M&auml;&auml;ramata"),
					));
				}
			break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

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

	function _gen_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Firma"),
			"sortable" => true,
		));
	}
}
?>

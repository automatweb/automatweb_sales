<?php
/*
@classinfo syslog_type=ST_CRM_COMPANY_CUSTOMER_DATA_GENERATOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_crm_company_customer_data_generator master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_company_customer_data_generator
@default group=general

	@property dirs type=relpicker reltype=RELTYPE_DIR store=connect multiple=1
	@caption Organisatsioonide kaustad

	@property subdirs type=checkbox ch_value=1 field=aw_subdirs
	@caption Kasuta alamkaustasid

	@property sample_object type=relpicker reltype=RELTYPE_SAMPLE_OBJECT store=connect
	@caption N&auml;idisobjekt

	@property seller_vs_buyer type=chooser field=aw_seller_vs_buyer
	@caption Organisatsioon kaustast on

	@property start_generating type=text store=no
	@caption Kliendisuhete genereerimine

#
#	Maybe using sample object is better idea?
#
#	@property use_cff type=relpicker reltype=RELTYPE_CFGFORM store=connect
#	@caption Seadete vorm
#
#@groupinfo default_values caption="Kliendisuhte omaduste vaikmisi v&auml;&auml;rtused"
#@default group=default_values

	#@property default_values type=callback callback=gen_default_values no_caption=1

## RELTYPES ##

@reltype DIR value=1 clid=CL_MENU
@caption Organisatsioonide kaust

@reltype CFGFORM value=2 clid=CL_CFGFORM
@caption Seadete vorm

@reltype SAMPLE_OBJECT value=3 clid=CL_CRM_COMPANY_CUSTOMER_DATA
@caption N&auml;idisobjekt

*/

class crm_company_customer_data_generator extends class_base
{
	function crm_company_customer_data_generator()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_customer_data_generator",
			"clid" => CL_CRM_COMPANY_CUSTOMER_DATA_GENERATOR
		));
	}

	public function gen_default_values($arr)
	{
		$retval = array();
		$meta = safe_array($arr["obj_inst"]->meta("default_values"));

		$cff = $arr["obj_inst"]->use_cff;
		$cff_inst = get_instance(CL_CFGFORM);

		$o = obj();
		$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);

		$props = $this->can("view", $cff) && obj($cff)->class_id() == CL_CRM_COMPANY_CUSTOMER_DATA ? $cff_inst->get_cfg_proplist($cff) : $o->get_property_list();

		foreach($props as $prop)
		{
			$prop["name"] = "default_values[".$prop["name"]."]";
			if(isset($meta[$prop["name"]]))
			{
				$prop["value"] = $meta[$prop["name"]];
			}
			$retval[] = $prop;
		}

		return $retval;
	}

	public function _get_seller_vs_buyer($arr)
	{
		$arr["prop"]["options"] = array(
			"seller" => t("M&uuml;&uuml;ja"),
			"buyer" => t("Ostja"),
		);
	}

	public function _get_start_generating($arr)
	{
		$cnt = $arr["obj_inst"]->get_orgs()->ids();
		$cnt_ = $arr["obj_inst"]->get_done_orgs();
		$done = array_intersect($cnt, $cnt_);

		$s = count($done) == 0 ? t("Alusta") : t("J&auml;tka");
		$caption = sprintf(t("%s kliendisuhete genereerimist %u organisatsioonile"), $s, count($cnt));
		$caption .= count($done) > 0 ? sprintf(t(" (tehtud %u)"), $done) : "";
		$caption .= count($cnt) - count($done) > 200 ? sprintf(t(" (korraga genereeritakse %u)"), 200) : "";

		$arr["prop"]["value"] = html::href(array(
			"url" => $this->mk_my_orb("generate", array("id" => $arr["obj_inst"]->id(), "return_url" => get_ru())),
			"caption" => $caption,
		));
	}

	public function _set_default_values($arr)
	{
		arr($arr, true);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_company_customer_data_generator(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_subdirs":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_seller_vs_buyer":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(10)"
				));
				return true;
		}
	}

	/**
		@attrib name=generate params=name

		@param id required type=int acl=view

		@param return_url required type=string

	**/
	public function generate($arr)
	{
		return obj($arr["id"])->generate($arr);
	}
}

?>

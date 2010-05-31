<?php

namespace automatweb;
// procurement_requirement_solution.aw - N&otilde;ude lahendus
/*

@classinfo syslog_type=ST_PROCUREMENT_REQUIREMENT_SOLUTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=aw_procurement_requirement_solution
@tableinfo aw_procurement_requirement_solution index=aw_oid master_index=brother_of master_table=objects

@default group=general

	@property readyness type=select field=aw_readyness
	@caption Valmidus

	@property price type=textbox size=5 field=aw_price
	@caption Hind

	@property time_to_install type=textbox size=5 field=aw_time_to_install
	@caption Seadistamise aeg

	@property solution type=textarea rows=5 cols=40 field=aw_solution
	@caption Lahendus

	@property offerer_co type=relpicker reltype=RELTYPE_CO field=aw_offerer_co
	@caption Pakkuja organisatsioon

	@property offerer_p type=relpicker reltype=RELTYPE_P field=aw_offerer_p
	@caption Pakkuja isik

	@property completion_date type=date_select field=aw_completion_date default=-1
	@caption Valmimiskuup&auml;ev

	@property requirement type=text field=aw_requirement_id
	@caption N&otilde;ue

	@property offer type=text field=aw_offer_id
	@caption Pakkumine

@default group=ppl

	@property team_tb type=table store=no no_caption=1

@groupinfo ppl caption="Isikud"

@reltype CO value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype P value=2 clid=CL_CRM_PERSON
@caption Isik

*/

define("PO_IN_BASE", 1);
define("PO_NEEDS_INSTALL", 2);
define("PO_NEEDS_DEVELOPMENT", 3);

class procurement_requirement_solution extends class_base
{
	const AW_CLID = 1072;

	function procurement_requirement_solution()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_requirement_solution",
			"clid" => CL_PROCUREMENT_REQUIREMENT_SOLUTION
		));

		$this->readyness_states = array(
			PO_IN_BASE => t("Kohe olemas"),
			PO_NEEDS_INSTALL => t("Vajab seadistamist"),
			PO_NEEDS_DEVELOPMENT => t("Uus arendus")
		);
		$this->model = get_instance("applications/procurement_center/procurements_model");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "team_tb":
				$this->_team_tb($arr);
				break;

			case "offerer_co":
				if (!$prop["value"])
				{
					$cc = get_current_company();
					$prop["value"] = $cc->id();
				}
				if (!isset($prop["options"][$prop["value"]]) && $prop["value"])
				{
					$po = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $po->name();
				}
				break;

			case "offerer_p":
				if (!$prop["value"])
				{
					$cc = get_current_person();
					$prop["value"] = $cc->id();
				}
				if (!isset($prop["options"][$prop["value"]]) && $prop["value"])
				{
					$po = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $po->name();
				}
				break;

			case "readyness":
				$prop["options"] = $this->readyness_states;
				break;

			case "requirement":
				if ($arr["request"]["set_requirement"])
				{
					$tmp = obj($arr["request"]["set_requirement"]);
					$prop["value"] = $tmp->name();
				}
				else
				if ($this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["value"] = $tmp->name();
				}
				break;

			case "offer":
				if ($arr["request"]["set_offer"])
				{
					$tmp = obj($arr["request"]["set_offer"]);
					$prop["value"] = $tmp->name();
				}
				else
				if ($this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["value"] = $tmp->name();
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
			case "team_tb":
				$arr["obj_inst"]->set_meta("hrs", $arr["request"]["hrs"]);
				break;

			case "requirement":
				if ($arr["request"]["set_requirement"])
				{
					$arr["obj_inst"]->set_prop("requirement", $arr["request"]["set_requirement"]);
				}
				break;

			case "offer":
				if ($arr["request"]["set_offer"])
				{
					$arr["obj_inst"]->set_prop("offer", $arr["request"]["set_offer"]);
				}
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["set_requirement"] = $_GET["set_requirement"];
		$arr["set_offer"] = $_GET["set_offer"];
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("
				CREATE TABLE aw_procurement_requirement_solution (aw_oid int primary key, aw_readyness int, aw_price double,
				aw_time_to_install double, aw_solution text)
			");
			return true;
		}

		switch($f)
		{
			case "aw_offerer_co":
			case "aw_offerer_p":
			case "aw_completion_date":
			case "aw_requirement_id":
			case "aw_offer_id":
				$this->db_add_col($t, array("name" => $f, "type" => "int"));
				return true;
		}
	}

	function _init_team_tb(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
	        	'name' => 'phone',
			'caption' => t('Telefon'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'email',
			'caption' => t('E-post'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'section',
			'caption' => t('&Uuml;ksus'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'rank',
			'caption' => t('Ametinimetus'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'price',
			'caption' => t('Tunnihind'),
			'sortable' => '1',
		));
		$t->define_field(array(
			'name' => 'hrs',
			'caption' => t('T&ouml;&ouml;tunnid'),
			'sortable' => '1',
		));
	}

	function _team_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_team_tb($t);

		$offer = obj($arr["obj_inst"]->prop("offer"));
		if (!$this->can("view", $offer->prop("offerer")))
		{
			return;
		}
		$offerer = obj($offer->prop("offerer"));

		// get center from offerer and team from that
		$center = $this->model->get_impl_center_for_co($offerer);
		if (!$center)
		{
			return;
		}

		$hrs = $arr["obj_inst"]->meta("hrs");

		$team = $this->model->get_team_from_center($center);
		foreach($team as $member_id => $price)
		{
			$p = obj($member_id);
			$section = $rank = "";

			$conns = $p->connections_to(array(
				"from.class_id" => CL_CRM_SECTION,
				"from" => $sections
			));
			if (count($conns))
			{
				$con = reset($conns);
				$section = $con->prop("from");
			}

			$t->define_data(array(
				"name" => html::obj_change_url($p),
				"phone" => html::obj_change_url($p->prop("phone")),
				"email" => html::obj_change_url($p->prop("email")),
				"section" => html::obj_change_url($section),
				"rank" => html::obj_change_url($p->get_first_obj_by_reltype("RELTYPE_RANK")),
				"id" => $p->id(),
				"hrs" => html::textbox(array(
					"name" => "hrs[".$p->id()."]",
					"value" => $hrs[$p->id()],
					"size" => 5
				)),
				"price" => $price
			));

		}
	}
}
?>

<?php
/*
@classinfo syslog_type=ST_COUNTRY_ADMINISTRATIVE_STRUCTURE relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@groupinfo grp_administrative_structure caption="Haldusjaotuse struktuur"


@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property country type=relpicker reltype=RELTYPE_COUNTRY clid=CL_COUNTRY automatic=1
	@comment Riik, mille haldusjaotuse struktuuri määratakse.
	@caption Riik

	@property address_admin type=textbox
	@comment Kasutaja kellel on 6igused k6igele ja k6igeks aadressisysteemi objektidel. Kasutatakse p6hiliselt programmaatiliselt systeemi haldamiseks t88 k2igus.
	@caption Aadresside administraatori kasutaja uid

	@property external_system_1_name type=textbox
	@comment Kui haldus&uuml;ksustes defineeritakse v&auml;lise s&uuml;steemi 1 identifikaatorid siis see on selle s&uuml;steemi nimetus
	@caption V&auml;lise s&uuml;steemi nimi (1)

@default group=grp_administrative_structure
	@property administrative_structure type=releditor reltype=RELTYPE_ADMINISTRATIVE_DIVISION mode=manager props=name,type,parent_division,division,jrk table_fields=jrk,name,parent_division editonly=1
	@caption Haldusjaotuse struktuur

	@property administrative_structure_data type=hidden

	// meta unit_hierarchy_index structure: array(unit_oid => parent_oid, ...)


// --------------- RELATION TYPES ---------------------

@reltype ADMINISTRATIVE_DIVISION value=1 clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
@caption Haldusüksus

@reltype COUNTRY value=2 clid=CL_COUNTRY
@caption Riik

*/

require_once(AW_DIR . "classes/common/address/as_header.aw");

class country_administrative_structure extends class_base
{
	function country_administrative_structure ($arr = array ())
	{
		$this->init (array (
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY_ADMINISTRATIVE_STRUCTURE
		));
	}

/* classbase methods */
	function callback_on_load ($arr)
	{
		aw_global_set ("address_system_administrative_structure", 1);

		if (!empty($arr["request"]["id"]))
		{
			$this_object = obj ($arr["request"]["id"], array(), CL_COUNTRY_ADMINISTRATIVE_STRUCTURE);

			### prepare unit parent selection list for unit releditor
			$country = $this_object->get_first_obj_by_reltype("RELTYPE_COUNTRY");

			if (is_object ($country))
			{
				$divisions = array ();
				$divisions[$country->id ()] = $country->name ();

				foreach ($this_object->connections_from (array ("type" => "RELTYPE_ADMINISTRATIVE_DIVISION")) as $connection)
				{
					$division = $connection->to ();

					if (!isset($arr["request"]["administrative_structure"]) or $arr["request"]["administrative_structure"] != $division->id ())
					{
						$divisions[$division->id ()] = $division->name ();
					}
				}

				aw_global_set ("address_system_parent_select_divisions", $divisions);
			}
		}
	}

	function get_property ($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		if (isset($prop["group"]) and "grp_administrative_structure" === $prop["group"] and !$this_object->get_first_obj_by_reltype("RELTYPE_COUNTRY"))
		{
			$retval = PROP_FATAL_ERROR;
			$prop["error"] = t("Riik valimata");
		}

		switch($prop["name"])
		{
			case "administrative_structure":
				$addresses_using_this = "";

				if ($addresses_using_this > 0)
				{
					$prop["error"] = sprintf (t("%s aadressi kasutab seda haldusjaotust! Muudatuste salvestamisel tekivad neis aadressides vead."), $addresses_using_this);
						//!!! vead tekivad ainult siis kui midagi kustutatakse vahelt, mis on mingi aadressi parentiks. muidu muutub ainult pealisstruktuur aadress ise aga j22b selle parenti alla mille all ta ennegi oli ilma ylevalpoolset muudatust "tajumata". v6ibolla v6iks muutmisel k6igi nende aadresside sissekirjutatud asju apdeitida. kui yritatakse teha muudatust, mis tooks kaasa jamasid olemasolevate aadressidega, siis tuleb kasutajat teavitada jms. sarnane kontroll peaks olema ka admin division ja admin unit klassides. struktuuri muutmisel peab ka olemasolevad halduspiirkonnad ymber t6stma kui v5imalik.
				}
				break;
		}

		return $retval;
	}

	function callback_post_save ($arr)
	{
		$this_object = $arr["obj_inst"];
		$divisions = array ();

		foreach ($this_object->connections_from (array ("type" => "RELTYPE_ADMINISTRATIVE_DIVISION")) as $connection)
		{
			$division = $connection->to ();
			$divisions[] = $division;
		}

		usort ($divisions, array ($this, "sort_by_ord"));

		foreach ($divisions as $key => $division)
		{
			### set this_object oid for all divisions under this structure. for easier maintenance.
			$division->set_prop ("administrative_structure", $this_object->id ());

			### set corrected order nr
			$division->set_ord ($key + 1);

			### ...
			$division->save ();
		}
	}
/* END classbase methods */

	function sort_by_ord ($a, $b)
	{
		if ($a->ord () > $b->ord ())
		{
			$result = 1;
		}
		elseif ($a->ord () < $b->ord ())
		{
			$result = -1;
		}
		else
		{
			$result = 0;
		}

		return $result;
	}
}

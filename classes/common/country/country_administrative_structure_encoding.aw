<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_COUNTRY_ADMINISTRATIVE_STRUCTURE_ENCODING relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=voldemar

@groupinfo grp_encodings caption="Vasted"


@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property administrative_structure type=relpicker reltype=RELTYPE_ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE automatic=1
	@caption Haldusjaotus


@default group=grp_encodings
	@property administrative_division type=select store=no
	@caption Vali haldusüksus

	@property encodings_list type=table store=no no_caption=1


// --------------- RELATION TYPES ---------------------

@reltype ADMINISTRATIVE_STRUCTURE value=1 clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE
@caption Haldusjaotus

*/

require_once(aw_ini_get("basedir") . "/classes/common/address/as_header.aw");

class country_administrative_structure_encoding extends class_base
{
	const AW_CLID = 1033;

	var $unit_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT,
	);

	var $division_selection_var = "as_adminstructure_encoding_unit";

	function country_administrative_structure_encoding()
	{
		$this->init(array(
			"tpldir" => "common/country/country_administrative_structure_encoding",
			"clid" => CL_COUNTRY_ADMINISTRATIVE_STRUCTURE_ENCODING
		));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch($prop["name"])
		{
			case "administrative_division":
				$administrative_structure = $this_object->get_first_obj_by_reltype ("RELTYPE_ADMINISTRATIVE_STRUCTURE");

				if (is_object ($administrative_structure))
				{
					$manager = obj ($this_object->prop("realestate_mgr"));
					$list = new object_list ($administrative_structure->connections_from(array(
						"type" => "RELTYPE_ADMINISTRATIVE_DIVISION",
						"class_id" => CL_COUNTRY_ADMINISTRATIVE_DIVISION,
					)));
					$var = $this->division_selection_var;
					$options = $list->names ();
					$selected = $arr["request"][$var];

					if ($selected)
					{
						$first = array("" => $options[$selected]);
						unset ($options[$selected]);
						aw_session_set ($var, $selected);
					}
					elseif (aw_global_get ($var))
					{
						$first = array("" => $options[aw_global_get ($var)]);
						unset ($options[$selected]);
					}
					else
					{
						$first = array("" => "");
						aw_session_del ($var);
					}

					$uri = aw_url_change_var ($var, "NA");
					$prop["onchange"] = "uri = '{$uri}'; window.location=uri.replace(/{$var}=NA/, '{$var}=' + this.value);";
					$prop["options"] = $first + $options;
				}
				else
				{
					$prop["error"] = t("Haldusjaotus valimata");
				}
				break;

			case "encodings_list":
				$this->_encodings_list ($arr);
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop =& $arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "encodings_list":
				$this->_save_encodings ($arr);
				break;
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	private function _encodings_list ($arr)
	{
		$division = aw_global_get ($this->division_selection_var);

		if (!is_oid ($division))
		{
			$division = $arr["request"][$this->division_selection_var];

			if (!is_oid ($division))
			{
				return;
			}
		}

		$this_object =& $arr["obj_inst"];
		$table =& $arr["prop"]["vcl_inst"];
		$table->name = "encodings_list";
		$this->_init_encodings_list ($arr);
		$list = new object_list (array (
			"class_id" => $this->unit_classes,
			"subclass" => $division,
		));
		$units = $list->arr ();

		foreach ($units as $unit)
		{
			$data = array (
				"name" => $unit->name (),
				"value" => html::textbox (array(
					"name" => "enc_value-" . $unit->id (),
					"size" => "20",
					"value" => $unit->meta ("admin_structure_enc-" . $this_object->id () . "-value1"),
				)),
			);

			$table->define_data ($data);
		}
	}

	private function _init_encodings_list ($arr)
	{
		$table =& $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$table->define_field(array(
			"name" => "value",
			"caption" => t("Väärtus/Vaste"),
		));
		$table->set_default_sortby ("name");
		$table->set_default_sorder ("asc");
	}

	private function _save_encodings ($arr)
	{
		$this_object =& $arr["obj_inst"];

		foreach ($arr["request"] as $name => $value)
		{
			$tag = explode ("-", $name);

			if ($tag[0] == "enc_value")
			{
				if (is_oid ($tag[1]))
				{
					$unit = obj ($tag[1]);
					$unit->set_meta ("admin_structure_enc-" . $this_object->id () . "-value1", $value);
					$unit->save ();
				}
			}
		}
	}
}
?>

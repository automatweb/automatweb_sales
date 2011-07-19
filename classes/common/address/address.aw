<?php
/*
@classinfo syslog_type=ST_ADDRESS relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo aw_address index=aw_oid master_index=brother_of master_table=objects

	@property name type=text table=objects field=name
	@caption Aadress

@default table=aw_address
@default group=general
	@property administrative_structure type=hidden

	@property country type=select
	@caption Riik

	@property location_data type=hidden store=no
	@property location type=textbox store=no
	@caption Asukoht

	@property street type=textbox maxlength=250
	@caption T&auml;nav/asum

	@property house type=textbox maxlength=250
	@caption Maja/number

	@property apartment type=textbox maxlength=50
	@caption Korter/Tuba

	@property postal_code type=textbox maxlength=25
	@caption Postiindeks

	@property po_box type=textbox maxlength=50
	@caption Postkast

*/

require_once(AW_DIR . "classes/common/address/as_header.aw");

class address extends class_base
{
	const AUTOCOMPLETE_OPTIONS_LIMIT = 25;

	function address($arr = array ())
	{
		$this->init(array(
			"tpldir" => "common/address",
			"clid" => CL_ADDRESS
		));
	}

/* classbase methods */
	function _get_location(&$arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];
		$administrative_structure = $this_object->prop("administrative_structure");
		if (is_object($administrative_structure) and $administrative_structure->is_saved())
		{
			$o = new object($this_object->parent());
			$unit_location_text = array($o->prop("complete_name"));
			$ancestors = $administrative_structure->get_ancestor_unit_ids($o->id());
			foreach ($ancestors as $ancestor_oid)
			{
				$ancestor_unit = new object($ancestor_oid);
				$unit_location_text[] = $ancestor_unit->prop("complete_name");
			}
			$prop["value"] = implode(", ", $unit_location_text);
		}

		// location autocomplete
		load_javascript("bsnAutosuggest.js");
		$options_url = $this->mk_my_orb("location_autocomplete", array(), "address");

		if (empty($this->name_prefix))
		{
			$element_id = "location";
			$location_data_name = "location_data";
			$country_id = "country";
		}
		else
		{
			$name_prefix = str_replace(array("[", "]"), "_", $this->name_prefix);
			$element_id = "{$name_prefix}_location_";
			$location_data_name = "{$this->name_prefix}[location_data]";
			$country_id = "{$name_prefix}_country_";
		}

		$js = <<<EOS
<script type="text/javascript">
// ADDRESS LOCATION ELEMENT AUTOCOMPLETE
(function(){

function setLocationData(obj)
{
	$("input[name='{$location_data_name}']").attr("value", obj.id);
}

var options = {
	script: function (input) { return "{$options_url}&typed_text="+input+"&country=" + document.getElementById('{$country_id}').value; },
	varname: "typed_text",
	minchars: 2,
	timeout: 10000,
	delay: 200,
	json: true,
	shownoresults: false,
	callback: setLocationData
};
var nameAS = new AutoSuggest('{$element_id}', options);
})(jQuery);
// END ADDRESS LOCATION ELEMENT AUTOCOMPLETE
</script>
EOS;
		$prop["post_append_text"] = $js;


		return $retval;
	}

	function _get_country (&$arr)
	{
		$this_object = $arr["obj_inst"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		$list = new object_list(array(
			"class_id" => CL_COUNTRY,
			"site_id" => array(),
			"lang_id" => array()
		));
		$prop["options"] = $list->names();

		if (is_object($this_object->prop("administrative_structure")) and $this_object->prop("administrative_structure")->is_saved())
		{
			$prop["value"] = $this_object->prop("administrative_structure")->prop("country");
		}

		return $retval;
	}

	function _set_administrative_structure(&$arr)
	{
		return PROP_IGNORE;
	}

	function _set_country(&$arr)
	{
		$prop =& $arr["prop"];

		if (!$this->can("view", $prop["value"]))
		{
			$prop["error"] = t("Riik peab olema valitud!");
			return PROP_FATAL_ERROR;
		}

		$country = new object($prop["value"], array(), CL_COUNTRY);
		$administrative_structure = $country->get_current_admin_structure();
		$arr["obj_inst"]->set_prop("administrative_structure", $administrative_structure);

		return PROP_OK;
	}

	function callback_pre_save ($arr)
	{
		if (!empty($arr["request"]["location_data"]))
		{
			try
			{
				$this_object = $arr["obj_inst"];
				$this_object->set_location($arr["request"]["location_data"]);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Sisestatud aadressi asukoht vigane"));
			}
		}
	}
/* END classbase methods */

/* public methods */

/**
    @attrib name=location_autocomplete all_args=1
	@param country required type=oid acl=view
	@param typed_text optional type=string
	@returns Array of autocomplete options in JSON format. Void on error.
**/
	function location_autocomplete ($arr)
	{
		$choices = array("results" => array());
		if (isset($arr["typed_text"]) and strlen($arr["typed_text"]) > 1)
		{
			$country = obj($arr["country"], array(), CL_COUNTRY);
			$administrative_structure = $country->get_current_admin_structure();
			$divisions = $administrative_structure->get_divisions()->ids();
			$typed_text = $arr["typed_text"];
			$list = new object_list(array(
				"class_id" => address_object::$unit_classes,
				"name" => "{$typed_text}%",
				"subclass" => $divisions,
				new obj_predicate_limit(address::AUTOCOMPLETE_OPTIONS_LIMIT)
			));

			if ($list->count() > 0)
			{
				$results = array();
				$o = $list->begin();
				do
				{
					$unit_location_text = array($o->prop("complete_name"));
					$ancestors = $administrative_structure->get_ancestor_unit_ids($o->id());
					foreach ($ancestors as $ancestor_oid)
					{
						$ancestor_unit = new object($ancestor_oid);
						$unit_location_text[] = $ancestor_unit->prop("complete_name");
					}
					$results[] = array("id" => $o->id(), "value" => iconv("iso-8859-4", "UTF-8", implode(", ", $unit_location_text)), "info" => "");//!!! in charset mis tegelikult olema peab?
				}
				while ($o = $list->next());
				$choices["results"] = $results;
			}
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		// header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		exit(json_encode($choices));
	}
/* END public methods */

	function do_db_upgrade($table, $field, $query, $error)
	{
		$return_val = false;

		if ("aw_address" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_address` (
					`aw_oid` int(11) UNSIGNED NOT NULL default '0',
					`administrative_structure` int(11) UNSIGNED NOT NULL default '0',
					`country` int(11) UNSIGNED default NULL,
					`postal_code` varchar(25) default NULL,
					`street` varchar(250) default NULL,
					`house` varchar(250) default NULL,
					`apartment` varchar(50) default NULL,
					`po_box` varchar(50) default NULL,
					PRIMARY KEY  (`aw_oid`)
				) ");
				$return_val = true;
			}
			elseif ("street" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "street",
					"type" => "varchar(250)"
				));
			}
			elseif ("house" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "house",
					"type" => "varchar(250)"
				));
			}
		}

		return $return_val;
	}
}

<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_XML_SOURCE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel

@default table=objects
@default group=general

	@groupinfo sub_general caption="&Uuml;ldine" parent=general
	@default group=sub_general

		@property name type=textbox field=name
		@caption Nimi
		@comment Objekti nimi

@default field=meta
@default method=serialize

		@property url type=textbox
		@caption URL
		@comment XML faili URL

		@property tag_event type=select
		@caption Sündmus
		@comment XML väli, kus on kogu imporditav sündmus

		@property tag_id type=select
		@caption ID
		@comment XML väli või argument, kus on imporditava sündmuse ID

		@property encoding type=textbox
		@caption Encoding

	@groupinfo sub_extsys caption="Siduss&uuml;steemid" parent=general
	@default group=sub_extsys

		@property external_system_event type=relpicker reltype=RELTYPE_EXTERNAL_SYSTEM_EVENT
		@caption S&uuml;ndmuste siduss&uuml;steem

		@property external_system_event_time type=relpicker reltype=RELTYPE_EXTERNAL_SYSTEM_EVENT_TIME
		@caption Toimumisaegade siduss&uuml;steem

		@property external_system_location type=relpicker reltype=RELTYPE_EXTERNAL_SYSTEM_LOCATION
		@caption Toimumiskohtade siduss&uuml;steem

		@property external_system_sector type=relpicker reltype=RELTYPE_EXTERNAL_SYSTEM_SECTOR
		@caption Valdkondade siduss&uuml;steem

	@groupinfo sub_lang caption="Keeled" parent=general
	@default group=sub_lang

		@property tag_lang type=select
		@caption Keel
		@comment XML väli või argument, kus on imporditava sündmuse tõlke keel

		@property available_langs type=select multiple=1
		@caption Keeled
		@comment XML väljundi võimalikud keeled

	@groupinfo sub_public caption="Kehtivus" parent=general
	@default group=sub_public

		@property tag_public_event type=select multiple=1 size=4
		@caption Sündmus avaldatud
		@comment XML väli või argument, mis näitab, kas sündmus on avalik.

		@property val_public_event type=textbox
		@caption "Jah"-väärtus
		@comment Väärtus, mille korral sündmus on avalik.

		@property tag_delete_event type=select multiple=1 size=4
		@caption Sündmus kustutatud
		@comment XML väli või argument, mis näitab, kas sündmus on kustutatud.

		@property val_delete_event type=textbox
		@caption "Jah"-väärtus
		@comment Väärtus, mille korral sündmus on kustutatud.

		@property tag_delete_time type=select multiple=1 size=4
		@caption Toimumisaeg kustutatud
		@comment XML väli või argument, mis näitab, kas toimumisaeg on kustutatud.

		@property val_delete_time type=textbox
		@caption "Jah"-väärtus
		@comment Väärtus, mille korral toimumisaeg on kustutatud.

@groupinfo parameters caption="Parameetrid"
@default group=parameters

	@property start_timestamp_unix type=textbox
	@caption Alguse timestamp (UNIX)
	@comment UNIX tüüpi timestamp v&auml;li, mille j&auml;rgi s&uuml;ndmusi p&auml;ritakse

	@property start_timestamp type=textbox
	@caption Alguse timestamp (YYYYMMDDHHMMSS)
	@comment Timestamp v&auml;li, mille j&auml;rgi s&uuml;ndmusi p&auml;ritakse

	@property end_timestamp_unix type=textbox
	@caption Lõpu timestamp (UNIX)
	@comment UNIX tüüpi timestamp v&auml;li, mille j&auml;rgi s&uuml;ndmusi p&auml;ritakse

	@property end_timestamp type=textbox
	@caption Lõpu timestamp (YYYYMMDDHHMMSS)
	@comment Timestamp v&auml;li, mille j&auml;rgi s&uuml;ndmusi p&auml;ritakse

	@property language type=textbox
	@caption Keel
	@comment Mis keeles sündmusi p&auml;ritakse?

@groupinfo tags caption="T&auml;&auml;gid"
@default group=tags

	@property subtag_table type=table no_caption=1

@groupinfo arguements caption="T&auml;&auml;gide atribuudid"
@default group=arguements

	@property arguement_table type=table no_caption=1

@groupinfo values caption="V&auml;&auml;rtused"

	@groupinfo languages caption="Keeled" parent=values
	@default group=languages

		@property language_table type=table no_caption=1

	@groupinfo levels caption="Tasemed" parent=values
	@default group=levels

		@property level_table type=table no_caption=1

@reltype EXTERNAL_SYSTEM_EVENT value=1 clid=CL_EXTERNAL_SYSTEM
@caption Sündmuste sidussüsteem

@reltype EXTERNAL_SYSTEM_SECTOR value=2 clid=CL_EXTERNAL_SYSTEM
@caption Valdkondade sidussüsteem

@reltype EXTERNAL_SYSTEM_LOCATION value=3 clid=CL_EXTERNAL_SYSTEM
@caption Toimumiskohtade sidussüsteem

@reltype EXTERNAL_SYSTEM_EVENT_TIME value=4 clid=CL_EXTERNAL_SYSTEM
@caption Toimumisaegade sidussüsteem

*/

class xml_source extends class_base
{
	const AW_CLID = 1354;

	function xml_source()
	{
		$this->init(array(
			"tpldir" => "datasource/xml_source",
			"clid" => CL_XML_SOURCE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function _get_available_langs($arr)
	{			
		$lg = new languages();
		$arr["prop"]["options"] = $lg->get_list();
	}
	
	function _get_tag_lang($arr)
	{
		// There is just one language and that is Estonian
		$arr["prop"]["options"]["tijolatie"] = t("-- XML väljund on ainult eesti keeles --");
		$lang = $arr["obj_inst"]->prop("language");
		if(!empty($lang))
		{
			// The language is defined by URL parameters
			$arr["prop"]["options"]["tlidbup"] = t("-- Keel defineeritakse URLi parameetri abil --");
		}

		$saved_subtag_table = $arr['obj_inst']->meta("subtag_table");
		$saved_arguement_table = $arr['obj_inst']->meta("arguement_table");
		
		if(!isset($arr["parent_tag_name"]))
		{
			$arr["parent_tag_name"] = "root";
			$arr["parent_tag_caption"] = "root";
		}
		$subtags = $saved_subtag_table[$arr["parent_tag_name"]];
		$subtags = str_replace(" ", "", $subtags);
		$subtags = explode(",", $subtags);
		foreach($subtags as $subtag)
		{		
			if(!empty($subtag))
			{
				$t_ptn = $arr["parent_tag_name"];
				$t_ptc = $arr["parent_tag_caption"];
				if($arr["parent_tag_name"] != "root")
				{
					$arr["parent_tag_name"] .= "_".$subtag;
					$arr["parent_tag_caption"] .= " -> ".$subtag;
				}
				else
				{
					$arr["parent_tag_name"] = $subtag;
					$arr["parent_tag_caption"] = $subtag;
				}

				$arr["prop"]["options"][$arr["parent_tag_name"]] = $arr["parent_tag_caption"];

				$subt_args = $saved_arguement_table[$arr["parent_tag_name"]];
				$subt_args = str_replace(" ", "", $subt_args);
				$subt_args = explode(",", $subt_args);

				foreach($subt_args as $subt_arg)
				{
					if(!empty($subt_arg))
					{						
						$arr["prop"]["options"][$arr["parent_tag_name"]."_args".$subt_arg] = $arr["parent_tag_caption"]." (".$subt_arg.")";
					}
				}
				
				$this->_get_tag_id($arr);
				$arr["parent_tag_name"] = $t_ptn;
				$arr["parent_tag_caption"] = $t_ptc;
			}
		}
	}

	function _get_tag_public_event($arr)
	{
		// Events that are not public are not displayed.
		$this->_get_tag_id($arr);
	}

	function _get_tag_delete_event($arr)
	{
		// Deleted events are not displayed.
		$this->_get_tag_id($arr);
	}

	function _get_tag_delete_time($arr)
	{
		// Deleted event times are not displayed.
		$this->_get_tag_id($arr);
	}
	
	function _get_tag_id($arr)
	{
		$saved_subtag_table = $arr['obj_inst']->meta("subtag_table");
		$saved_arguement_table = $arr['obj_inst']->meta("arguement_table");
		
		if(!isset($arr["parent_tag_name"]))
		{
			$arr["parent_tag_name"] = "root";
			$arr["parent_tag_caption"] = "root";
		}
		$subtags = $saved_subtag_table[$arr["parent_tag_name"]];
		$subtags = str_replace(" ", "", $subtags);
		$subtags = explode(",", $subtags);
		foreach($subtags as $subtag)
		{		
			if(!empty($subtag))
			{
				$t_ptn = $arr["parent_tag_name"];
				$t_ptc = $arr["parent_tag_caption"];
				if($arr["parent_tag_name"] != "root")
				{
					$arr["parent_tag_name"] .= "_".$subtag;
					$arr["parent_tag_caption"] .= " -> ".$subtag;
				}
				else
				{
					$arr["parent_tag_name"] = $subtag;
					$arr["parent_tag_caption"] = $subtag;
				}

				$arr["prop"]["options"][$arr["parent_tag_name"]] = $arr["parent_tag_caption"];

				$subt_args = $saved_arguement_table[$arr["parent_tag_name"]];
				$subt_args = str_replace(" ", "", $subt_args);
				$subt_args = explode(",", $subt_args);

				foreach($subt_args as $subt_arg)
				{
					if(!empty($subt_arg))
					{						
						$arr["prop"]["options"][$arr["parent_tag_name"]."_args".$subt_arg] = $arr["parent_tag_caption"]." (".$subt_arg.")";
					}
				}
				
				$this->_get_tag_id($arr);
				$arr["parent_tag_name"] = $t_ptn;
				$arr["parent_tag_caption"] = $t_ptc;
			}
		}
	}

	function _get_language_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable("false");
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
		));
		$t->define_field(array(
			"name" => "param_value",
			"caption" => t("Parameetri väärtus"),
		));

		$saved_lang_conf = $arr["obj_inst"]->meta("language_table");

		$lg = new languages();
		foreach($lg->get_list() as $id => $caption)
		{
			$t->define_data(array(
				"lang" => t($caption),
				"param_value" => html::textbox(array(
					"name" => "language_table[".$id."]",
					"value" => $saved_lang_conf[$id],
				)),
			));
		}
	}

	function _get_level_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable("false");
		$t->define_field(array(
			"name" => "level",
			"caption" => t("Tase"),
		));
		$t->define_field(array(
			"name" => "param_value",
			"caption" => t("Väärtused allikas (komaga eraldatud)"),
		));

		$saved_lvl_conf = $arr["obj_inst"]->meta("level_table");

		$t->define_data(array(
			"level" => t("&Uuml;leriikliku t&auml;htsusega"),
			"param_value" => html::textbox(array(
				"name" => "level_table[1]",
				"value" => $saved_lvl_conf[1],
			)),
		));
		$t->define_data(array(
			"level" => t("Kohaliku t&auml;htsusega"),
			"param_value" => html::textbox(array(
				"name" => "level_table[2]",
				"value" => $saved_lvl_conf[2],
			)),
		));
		$t->define_data(array(
			"level" => t("V&auml;lismaal toimuv"),
			"param_value" => html::textbox(array(
				"name" => "level_table[3]",
				"value" => $saved_lvl_conf[3],
			)),
		));
	}

	function _get_tag_event($arr)
	{
		$saved_subtag_table = $arr['obj_inst']->meta("subtag_table");
		
		if(!isset($arr["parent_tag_name"]))
		{
			$arr["parent_tag_name"] = "root";
			$arr["parent_tag_caption"] = "root";
		}
		$subtags = $saved_subtag_table[$arr["parent_tag_name"]];
		$subtags = str_replace(" ", "", $subtags);
		$subtags = explode(",", $subtags);
		foreach($subtags as $subtag)
		{		
			if(!empty($subtag))
			{
				$t_ptn = $arr["parent_tag_name"];
				$t_ptc = $arr["parent_tag_caption"];
				if($arr["parent_tag_name"] != "root")
				{
					$arr["parent_tag_name"] .= "_".$subtag;
					$arr["parent_tag_caption"] .= " -> ".$subtag;
				}
				else
				{
					$arr["parent_tag_name"] = $subtag;
					$arr["parent_tag_caption"] = $subtag;
				}

				$arr["prop"]["options"][$arr["parent_tag_name"]] = $arr["parent_tag_caption"];
				
				$this->_get_tag_event($arr);
				$arr["parent_tag_name"] = $t_ptn;
				$arr["parent_tag_caption"] = $t_ptc;
			}
		}
	}
	
	private function subt_subt($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$saved_table["subtag"] = $arr["obj_inst"]->meta("subtag_table");
		$saved_table["arguement"] = $arr["obj_inst"]->meta("arguement_table");
		
		$subtags = $saved_table["subtag"][$arr["parent_tag_name"]];
		$subtags = str_replace(" ", "", $subtags);
		$subtags = explode(",", $subtags);
		foreach($subtags as $subtag)
		{		
			if(!empty($subtag))
			{
				$t_ptn = $arr["parent_tag_name"];
				$t_ptc = $arr["parent_tag_caption"];
				if($arr["parent_tag_name"] != "root")
				{
					$arr["parent_tag_name"] .= "_".$subtag;
					$arr["parent_tag_caption"] .= " -> ".$subtag;
				}
				else
				{
					$arr["parent_tag_name"] = $subtag;
					$arr["parent_tag_caption"] = $subtag;
				}
				$t->define_data(array(
					"tag" => $arr["parent_tag_caption"],
					$arr["table_type"]."s" => html::textbox(array(
						"name" => $arr["table_type"]."_table[".$arr["parent_tag_name"]."]",
						"value" => $saved_table[$arr["table_type"]][$arr["parent_tag_name"]],
					)),
				));
				
				$this->subt_subt($arr);
				$arr["parent_tag_name"] = $t_ptn;
				$arr["parent_tag_caption"] = $t_ptc;
			}
		}
	}

	function _get_subtag_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];		
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "tag",
			"caption" => t("T&auml;&auml;g"),
		));
		$t->define_field(array(
			"name" => "subtags",
			"caption" => t("Subt&auml;&auml;gid"),
		));
		$t->define_field(array(
			"name" => "multiple",
			"caption" => t("Mitu"),
		));

		$saved_subtag_table = $arr['obj_inst']->meta("subtag_table");

		$o = obj($arr["request"]["id"]);

		$t->define_data(array(
			"tag" => "root",
			"subtags" => html::textbox(array(
				"name" => "subtag_table[root]",
				"value" => $saved_subtag_table["root"],
			)),
		));

		$arr["parent_tag_name"] = "root";
		$arr["parent_tag_caption"] = "root";
		$arr["table_type"] = "subtag";
		$this->subt_subt($arr);
	}

	function _get_arguement_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];		
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "tag",
			"caption" => t("T&auml;&auml;g"),
		));
		$t->define_field(array(
			"name" => "arguements",
			"caption" => t("Argumendid"),
		));

		$arr["parent_tag_name"] = "root";
		$arr["parent_tag_caption"] = "root";
		$arr["table_type"] = "arguement";
		$this->subt_subt($arr);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "subtag_table":
				if (!empty($arr['request']["subtag_table"]))
				{
					$arr['obj_inst']->set_meta("subtag_table", $arr['request']['subtag_table']);
				}
				break;
				
			case "arguement_table":
				if (!empty($arr['request']["arguement_table"]))
				{
					$arr['obj_inst']->set_meta("arguement_table", $arr['request']['arguement_table']);
				}
				break;
				
			case "language_table":
				if (!empty($arr['request']["language_table"]))
				{
					$arr['obj_inst']->set_meta("language_table", $arr['request']['language_table']);
				}
				break;
				
			case "level_table":
				if (!empty($arr['request']["level_table"]))
				{
					$arr['obj_inst']->set_meta("level_table", $arr['request']['level_table']);
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
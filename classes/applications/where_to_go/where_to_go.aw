<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/where_to_go/where_to_go.aw,v 1.3 2008/01/31 13:51:58 kristo Exp $
// where_to_go.aw - Kuhu minna 
/*

@classinfo syslog_type=ST_WHERE_TO_GO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects

@default group=general

	@property show_season_from type=date_select field=meta method=serialize
	@caption Kuvatav hooaeg alates

	@property show_season_to type=date_select field=meta method=serialize
	@caption Kuvatav hooaeg kuni

@groupinfo settings caption="Seaded"
@default group=settings

	@property languages type=select multiple=1 field=meta method=serialize 
	@caption Keeled

	@property languages_to_folders type=table
	@caption Keelte seosed kaustadega

	@property organisations_folder type=relpicker reltype=RELTYPE_ORGANISATIONS_FOLDER field=meta method=serialize
	@caption Organisatsioonide kaust

@groupinfo management caption="Haldamine"
@default group=management

	@property management_toolbar type=toolbar no_caption=1 store=no group=genres,places,events,add_item
	@caption Haldamise t&ouml;&ouml;riistariba 

	@groupinfo events caption="&Uuml;ritused" parent=management
	@default group=events

		@layout events_layout type=hbox width=20%:80%
		caption Horisontaalne paigutus puuvaate ja tabeli jaoks

			@property events_tree type=treeview parent=events_layout no_caption=1
			@caption &Uuml;rituste puu

			@property events_table type=table parent=events_layout no_caption=1
			@caption &Uuml;rituste puu

	@groupinfo genres caption="&#142;anrid" parent=management
	@default group=genres
	
		@property genres_table type=table

		@caption &zcaron;anrite tabel

		@property add_genre type=releditor reltype=RELTYPE_GENRE mode=manager props=name
		@caption &#142;anr

	@groupinfo places caption="Toimumiskohad" parent=management
	@default group=places

		@property places_table type=table no_caption=1
		@caption Toimumiskohtade tabel

	@groupinfo organisers caption="Korraldajad" parent=management
	@default group=organisers

		@property organisers type=releditor reltype=RELTYPE_ORGANISER mode=manager props=name,comment
		@caption Korraldajad

	@groupinfo add_item caption="Uus" parent=management
	@default group=add_item

		@property add_item callback=callback_add_item group=add_item
		@caption Uus


@reltype PLACES_FOLDER value=1 clid=CL_MENU
@caption Toimumispaikade kaust

@reltype EVENTS_FOLDER value=2 clid=CL_MENU
@caption &Uuml;rituste kaust

@reltype FORUM value=3 clid=CL_FORUM_V2
@caption Foorum

@reltype ORGANISATIONS_FOLDER value=4 clid=CL_MENU
@caption Organisatsioonide kaust

@reltype ORGANISER value=5 clid=CL_CRM_COMPANY
@caption Korraldaja

@reltype GENRE value=6 clid=CL_WHERE_TO_GO_GENRE
@caption &#142;anr
		
*/

class where_to_go extends class_base
{
	const AW_CLID = 993;

	function where_to_go()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "application/where_to_go/where_to_go",
			"clid" => CL_WHERE_TO_GO
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "show_season_from":
			case "show_season_to":
				// if there is no value set for these properties, then put it -1
				// just to make the date property show '---' in all fields 
				$prop['value'] = "";
				if (empty($prop['value']))
				{
					$prop['value'] = -1;
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

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_languages($arr)
	{
		$languages_inst = new languages();
		$active_languages = $languages_inst->get_list();
		$arr['prop']['options'] = $active_languages;
		return PROP_OK;
	}

	function _get_languages_to_folders($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			"name" => "language",
			"caption" => t("Keel"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "places_folder",
			"caption" => t("Toimumispaigad"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "events_folder",
			"caption" => t("&Uuml;ritused"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "forum",
			"caption" => t("Foorum"),
			"align" => "center",
		));
		// gather info for the table:
		$selected_languages = $arr['obj_inst']->prop("languages");
		
		$languages_inst = new languages();
		$active_languages = $languages_inst->get_list();

		$connections_to_places_folders = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_PLACES_FOLDER",
		));
		foreach($connections_to_places_folders as $connection_to_places_folder)
		{
			$places_folders_list[$connection_to_places_folder->prop("to")] = $connection_to_places_folder->prop("to.name");
		}

		$connections_to_events_folders = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_EVENTS_FOLDER",
		));
		foreach($connections_to_events_folders as $connection_to_events_folder)
		{
			$events_folders_list[$connection_to_events_folder->prop("to")] = $connection_to_events_folder->prop("to.name");
		}

		$connections_to_forums = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_FORUM",
		));
		foreach($connections_to_forums as $connection_to_forum)
		{
			$forums_list[$connection_to_forum->prop("to")] = $connection_to_forum->prop("to.name");
		}

		$saved_languages_to_folders = $arr['obj_inst']->meta("languages_to_folders");
		// fill the table:
		foreach(safe_array($selected_languages) as $selected_language)
		{
			$t->define_data(array(
				"language" => $active_languages[$selected_language],
				"places_folder" => html::select(array(
					"name" => "languages_to_folders[".$selected_language."][places_folder]",
					"options" => $places_folders_list,
					"selected" => $saved_languages_to_folders[$selected_language]['places_folder'],
				)),
				"events_folder" => html::select(array(
					"name" => "languages_to_folders[".$selected_language."][events_folder]",
					"options" => $events_folders_list,
					"selected" => $saved_languages_to_folders[$selected_language]['events_folder'],
				)),
				"forum" => html::select(array(
					"name" => "languages_to_folders[".$selected_language."][forum]",
					"options" => $forums_list,
					"selected" => $saved_languages_to_folders[$selected_language]['forum'],
				)),
			));
		}
		return PROP_OK;
	}

	function _set_languages_to_folders($arr)
	{
		$arr['obj_inst']->set_meta("languages_to_folders", $arr['request']['languages_to_folders']);
	}

	function _get_genres_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$table_columns = array(
			"name" => "Nimi",
			"place" => "Toimumiskoht",
			"change" => "Muuda",
			"delete" => "Kustuta",
			"select" => "Vali",
		);
		foreach($table_columns as $column_name => $column_caption)
		{
			$t->define_field(array(
				"name" => $column_name,
				"caption" => $column_caption,
				"sortable" => 1,
			));
		}
		return PROP_OK;
	}
	
	function _get_places_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$table_columns = array(
			"name" => "Nimi",
			"genres" => "&#142;anrid",
			"description" => "Kirjeldus",
		);
		foreach($table_columns as $column_name => $column_caption)
		{
			$t->define_field(array(
				"name" => $column_name,
				"caption" => $column_caption,
			));
		}
		return PROP_OK;
	}

	function _get_management_toolbar($arr)
	{
		$toolbar = &$arr['prop']['toolbar'];
		
		$oid = $arr['obj_inst']->id();

		// add button (menu)
		$toolbar->add_menu_button(array(
			"name" => "add",
			"img" => "new.gif",
			"tooltip" => t("Lisa"),
		));

		$toolbar->add_menu_item(array(
			"parent" => "add",
			"text" => t("&#142;anr"),
			"link" => $this->mk_my_orb("change", array(
				"id" => $oid,
				"group" => "add_item",
				"clid" => CL_WHERE_TO_GO_GENRE,
			)),
		));
		$toolbar->add_menu_item(array(
			"parent" => "add",
			"text" => t("Toimumiskoht"),
			"link" => $this->mk_my_orb("change", array(
				"id" => $oid,
				"group" => "add_item",
				"clid" => CL_PLACE,
			)),
		));
		$toolbar->add_menu_item(array(
			"parent" => "add",
			"text" => t("Lavastus"),
			"link" => $this->mk_my_orb("change", array(
				"id" => $oid,
				"group" => "add_item",
				"clid" => CL_STAGING,
			)),
		));
		
		// save button
		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
		));

		// delete button
		$toolbar->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
		));
	
		// search button
		$toolbar->add_button(array(
			"name" => "search",
			"img" => "search.gif",
			"tooltip" => t("Otsi"),
		));
		return PROP_OK;
	}

	function _get_events_tree($arr)
	{
		$tree = &$arr['prop']['vcl_inst'];
		$tree->add_item(0,array(
			"name" => "blah",
			"id" => 1,
		));
		return PROP_OK;
	}

	function _get_events_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$table_columns = array(
			"name" => "Nimi",
			"events" => "I etendus - viimane etendus",
			"place" => "Asukoht",
			"genre" => "&#142;anr",
			"description" => "Kirjeldus",
			"status" => "Aktiivsus",
			"order" => "Jrk",
			"change" => "Muuda",
			"delete" => "Kustuta",
			"select" => "Vali",
		);
		foreach($table_columns as $column_name => $column_caption)
		{
			$t->define_field(array(
				"name" => $column_name,
				"caption" => $column_caption
			));
		}
		return PROP_OK;
	}

	function callback_mod_tab($arr)
	{
		if ($arr['activegroup'] != "add_item" && $arr['id'] == "add_item")
		{
			return false;	
		}
	}

	// in this function i try to show the form according to a class_id
	// this should be make possible to add objects from the where_to_go class
	function callback_add_item($arr)
	{

		/**
			so, here i have to check, if the place is writable where i want to put
			the object
			
			and possibly some more checks, so it won't break in any condition and 
			shows some kind of nice error message instead

			and it won't use any configform, which it should do obviously, 
			i think i need here only system configform for an class, no need 
			to set any custom configforms here, or is it needed?
			
		**/

		// class ids which can be added
		$allowed_classes = array(CL_PLACE, CL_WHERE_TO_GO_GENRE, CL_STAGING);

		if (in_array($arr['request']['clid'], $allowed_classes))
		{
			$class_id = $arr['request']['clid'];
		}
		else
		{
			return array(array(
				"type" => "text",
				"value" => t("Sellist klassi ei saa lisada!"),
			));
		}

		$all_classes = aw_ini_get("classes");
		$class_info = $all_classes[$class_id];

		$class_inst = get_instance($class_info['file']);

		$class_inst->init_class_base();
		$class_properties = $class_inst->get_property_group(array(
			"group" => "general",
		));
		$parsed_properties = $class_inst->parse_properties(array(
			"properties" => $class_properties,
			"obj_inst" => new object(), // << some classes need obj_inst
			"name_prefix" => "emb",
		));
		foreach ($parsed_properties as $parsed_property_key => $parsed_property_value)
		{
			$result[$parsed_property_key] = $parsed_property_value;
		}
	
		return $result;
	}

	function _set_add_item($arr)
	{
		/**
			i have to check here if i can write to there where i want
			so how i'm going to do that? i have several classes which should
			go several places

			or do i have to check the write possibility where i show the form/object?
			i don't write there anything - i write here ... but maybe its nice to not 
			let user to submit all the info, and then say that sorry, fuck off ?

			so maybe its nice to create a separate function for checking, if this class
			is writeable to where it should go ... hmm, seems to be a good idea

			but first of all, lets try to save the object, then we can move on ... ;)
		**/

		// hm, so how the hell i know which class where to save ?
		// do i really have to hardcode it ... :/
		// or maybe i can make it konfigurable somehow ...
		

		$emb = $arr['request']['emb'];
		$emb['clid'] = CL_PLACE;
		$emb['parent'] = $arr['obj_inst']->parent();

		// seems that the following code is enough to save a object 
		$class_inst = get_instance("common/place");
		$class_inst->init_class_base();
		$class_inst->submit($emb);
		arr($arr);
		
		return PROP_OK;	
	}

}
?>

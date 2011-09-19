<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/spa_bookings/spa_bookings_overview.aw,v 1.84 2009/06/19 14:59:25 markop Exp $
// spa_bookings_overview.aw - Reserveeringute &uuml;levaade 
/*

@classinfo syslog_type=ST_SPA_BOOKINGS_OVERVIEW relationmgr=yes no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

	@property rooms_folder type=relpicker reltype=RELTYPE_RF field=meta method=serialize
	@caption Ruumide kaust

	@property owner type=relpicker reltype=RELTYPE_OWNER field=meta method=serialize
	@caption Omanik

	@property groups type=relpicker reltype=RELTYPE_GROUP field=meta method=serialize multiple=1
	@caption Kasutajagrupid


@default group=room_management

	@property room_management_tb type=toolbar store=no no_caption=1

	@layout room_management_split type=hbox width=20%:80%

		@layout room_management_left type=vbox parent=room_management_split
			@layout room_management_tree type=vbox closeable=1 area_caption=Kategooriad parent=room_management_left
				@property room_management_tree type=treeview store=no no_caption=1 parent=room_management_tree
			@layout room_management_type_tree type=vbox closeable=1 area_caption=T&uuml;&uuml;bid parent=room_management_left
				@property room_management_type_tree type=treeview store=no no_caption=1 parent=room_management_type_tree
			@layout room_management_capacity_from_tree type=vbox closeable=1 area_caption=mahutavus&nbsp;alates parent=room_management_left
				@property room_management_capacity_from_tree type=treeview store=no no_caption=1 parent=room_management_capacity_from_tree
			@layout room_management_capacity_to_tree type=vbox closeable=1 area_caption=Mahutavus&nbsp;kuni parent=room_management_left
				@property room_management_capacity_to_tree type=treeview store=no no_caption=1 parent=room_management_capacity_to_tree
			@layout room_management_srch type=vbox closeable=1 area_caption=Otsing parent=room_management_left
			
				@property room_management_name type=textbox store=no captionside=top parent=room_management_srch size=22
				@caption Ruumi nimi

				@property room_management_rs_btn type=button store=no parent=room_management_srch no_caption=1
				@caption Otsi

		@property room_management_list type=table store=no no_caption=1 parent=room_management_split



@default group=reservation_management

	@property reservation_management_tb type=toolbar store=no no_caption=1

	@layout reservation_management_split type=hbox width=20%:80%

		@layout reservation_management_left type=vbox parent=reservation_management_split
			@layout reservation_management_tree type=vbox closeable=1 area_caption=Ruumid&nbsp;kategooria&nbsp;kaupa parent=reservation_management_left
				@property reservation_management_tree type=treeview store=no no_caption=1 parent=reservation_management_tree

			@layout reservation_management_time_tree type=vbox closeable=1 area_caption=Vali&nbsp;ajavahemik parent=reservation_management_left
				@property reservation_management_time_tree type=treeview parent=reservation_management_time_tree store=no no_caption=1

			@layout reservation_management_srch type=vbox closeable=1 area_caption=Otsing parent=reservation_management_left
			
				@property reservation_management_name type=textbox store=no captionside=top parent=reservation_management_srch size=22
				@caption Ruumi nimi

				@property reservation_management_rs_btn type=button store=no parent=reservation_management_srch no_caption=1
				@caption Otsi

		@property reservation_management_list type=table store=no no_caption=1 parent=reservation_management_split


@default group=rooms

	@property r_tb type=toolbar store=no no_caption=1

	@layout r_split type=hbox width=20%:80%

		@layout r_left type=vbox parent=r_split

			@layout r_tree type=vbox closeable=1 area_caption=Ruumid parent=r_left
	
				@property rooms_tree type=treeview store=no no_caption=1 parent=r_tree
	
			@layout r_srch type=vbox closeable=1 area_caption=Otsing parent=r_left
			
				@property rs_name type=textbox store=no captionside=top parent=r_srch size=22
				@caption Ruumi nimi

				@property rs_booker_name type=textbox store=no captionside=top parent=r_srch size=22
				@caption Broneerija nimi

				@property rs_booking_from type=date_select store=no captionside=top parent=r_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering alates
			
				@property rs_booking_to type=date_select store=no captionside=top parent=r_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering kuni
		
				@property rs_unconfirmed type=checkbox ch_value=1 store=no parent=r_srch no_caption=1 prop_cb=1
				@caption Kinnitamata

				@property rs_btn type=submit store=no parent=r_srch no_caption=1
				@caption Otsi

		@property r_list type=table store=no no_caption=1 parent=r_split


@default group=stats

	@layout stats_r_split type=hbox width=20%:80%

		@layout stats_r_left type=vbox parent=stats_r_split

			@layout stats_r_srch type=vbox closeable=1 area_caption=Otsing parent=stats_r_left
			
				@property stats_rs_name type=textbox store=no captionside=top parent=stats_r_srch size=22
				@caption Kliendi nimi

				@property stats_rs_booker_name type=textbox store=no captionside=top parent=stats_r_srch size=22
				@caption Reisib&uuml;roo nimi

				@property stats_rs_booking_from type=date_select store=no captionside=top parent=stats_r_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering alates
			
				@property stats_rs_booking_to type=date_select store=no captionside=top parent=stats_r_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering kuni

				@property stats_rs_package type=select store=no captionside=top parent=stats_r_srch 
				@caption Pakett

				@property stats_rs_btn type=submit store=no parent=stats_r_srch no_caption=1
				@caption Otsi

		@property stats_r_list type=table store=no no_caption=1 parent=stats_r_split


@default group=reports

@default group=reports_all

	@property ra_tb type=toolbar store=no no_caption=1

	@layout ra_split type=hbox width=20%:80%

		@layout ra_left type=vbox parent=ra_split

			@layout ra_srch type=vbox closeable=1 area_caption=Otsing parent=ra_left
			
				@property r_ra_name type=select store=no captionside=top parent=ra_srch
				@caption Ruumi nimi

				@property r_ra_booker_name type=textbox store=no captionside=top parent=ra_srch size=22
				@caption Broneerija nimi

				@property r_ra_project type=select store=no captionside=top parent=ra_srch
				@caption Projekt

				@property r_ra_location type=select store=no captionside=top parent=ra_srch
				@caption Asukoht

				@property r_ra_worker type=select store=no captionside=top parent=ra_srch
				@caption T&otilde;&otilde;taja

				@property r_ra_seller type=select store=no captionside=top parent=ra_srch
				@caption Vahendaja

				@property r_ra_booking_from type=date_select store=no captionside=top parent=ra_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering alates
			
				@property r_ra_booking_to type=date_select store=no captionside=top parent=ra_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering kuni

				@property r_ra_only_done type=chooser ch_value=1 store=no parent=ra_srch prop_cb=1 captionside=top
				@caption Kohale ilmunud

				@property r_ra_only_paid type=chooser ch_value=1 store=no parent=ra_srch prop_cb=1 captionside=top
				@caption Maksnud

				@property r_ra_unconfirmed type=checkbox ch_value=1 store=no parent=ra_srch no_caption=1 prop_cb=1
				@caption K.A Kinnitamata

				@property r_ra_res_type type=select store=no captionside=top parent=ra_srch
				@caption Tulemused

				@property r_ra_res_style type=select store=no parent=ra_srch prop_cb=1 captionside=top
				@caption 

				@property r_ra_btn type=submit store=no parent=ra_srch no_caption=1
				@caption Otsi


			@layout ra_rigth type=vbox parent=ra_split
		@property r_ra_list type=table store=no no_caption=1 parent=ra_rigth

		@property r_ra_days_projects_tab type=table store=no no_caption=1 parent=ra_rigth

@default group=spa_reports

@groupinfo  caption=Ruumid

	@property rr_tb type=toolbar store=no no_caption=1

	@layout rr_split type=hbox width=20%:80%

		@layout rr_left type=vbox parent=rr_split

			@layout rr_srch type=vbox closeable=1 area_caption=Otsing parent=rr_left
			
				@property r_rs_name type=textbox store=no captionside=top parent=rr_srch size=22
				@caption Ruumi nimi

				@property r_rs_booker_name type=textbox store=no captionside=top parent=rr_srch size=22
				@caption Broneerija nimi

				@property r_rs_booking_from type=date_select store=no captionside=top parent=rr_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering alates
			
				@property r_rs_booking_to type=date_select store=no captionside=top parent=rr_srch format=day_textbox,month_textbox,year_textbox
				@caption Broneering kuni

				@property r_rs_btn type=submit store=no parent=rr_srch no_caption=1
				@caption Otsi

		@property r_r_list type=table store=no no_caption=1 parent=rr_split


@groupinfo room_management caption=Ruumide&nbsp;haldus submit=no

@groupinfo reservation_management caption=Broneeringute&nbsp;haldus submit=no

@groupinfo rooms caption=Ruumid
@groupinfo stats caption=Statistika
@groupinfo spa_reports caption=SpaAruanded parent=reports
@groupinfo reports caption=Aruanded
@groupinfo reports_all caption=Aruanded parent=reports


@reltype RF value=1 clid=CL_MENU
@caption Ruumide kaust

@reltype OWNER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Omanik

@reltype GROUP value=3 clid=CL_GROUP
@caption Kasutajagrupp

*/

define("DAY_LENGTH", 3600*24);

classload("vcl/date_edit");
class spa_bookings_overview extends class_base
{
	function spa_bookings_overview()
	{
		$this->init(array(
			"tpldir" => "applications/spa_bookings/spa_bookings_overview",
			"clid" => CL_SPA_BOOKINGS_OVERVIEW
		));
		classload("core/date/date_calc");
		
		$this->stats_search_properties = array(
			"r_ra_name",
			"r_ra_booker_name",
			"r_ra_booking_from",
			"r_ra_booking_to",
			"r_ra_only_done",
			"r_ra_only_done2",//seda m6nes kontrolleris saab kasutada, ei osanud mujale panna
			"r_ra_only_paid",
			"r_ra_res_type",
			"r_ra_seller",
			"r_ra_worker",
			"r_ra_project",
			"r_ra_location",
			"r_ra_unconfirmed",
			"r_ra_weekdays",
			"r_ra_res_style",
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "reservation_management_time_tree":
				$this->_get_stats_time_tree($arr);
				break;
			case "room_management_rs_btn":
				$prop["onclick"] = "javascript:update_rooms_table();";//aw_url_change_var("cat", $id),
				break;
			case "r_ra_seller":
				$prop["options"] = array("" => "") + $this->get_all_sellers();
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_worker":
				$prop["options"] = array("" => "") + $this->get_all_workers();
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_only_done":
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["options"] = array(0 => t("K&otilde;ik"), 2 => t("Ei") , 1 => t("Jah"));
				break;
			case "r_ra_only_paid":
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["options"] = array(0 => t("K&otilde;ik"), 2 => t("Ei") , 1 => t("Jah"));
				break;
			case "r_ra_location":
				$prop["options"] =  array("" => "");
				$params = array("obj_inst" => $arr["obj_inst"]);
				$this->_get_rooms_tree(&$params);
				$locs = $params["prop"]["vcl_inst"]->get_item_ids();
				foreach($locs as $loc_id)
				{
					if($this->can("view" , $loc_id))
					{
						$loco = obj($loc_id);
						$prop["options"][$loc_id] = $loco->name();
					}
				}
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_booking_from":
				$prop["value"] = $arr["request"][$prop["name"]];
				if(!$prop["value"])
				{
					$prop["value"] = time() - DAY_LENGTH * 30;
				}
				break;
			case "r_ra_res_type":
				$prop["options"] = array(
					"" => t("Ei grupeeri"),
					"rooms" => t("Ruumide aruanne"),
					"sellers" => t("Vahendajate aruanne"),
					"workers" => t("T&ouml;&ouml;tajate aruanne"),
					"projects" => $this->new_captions["r_ra_project"] ? $this->new_captions["r_ra_project"]. " ".t("aruanne") : t("Projektide aruanne"),
					//"dp" => ($this->new_captions["r_ra_project"] ? $this->new_captions["r_ra_project"]. " ": t("Projektide aruanne"))." ".t("P&auml;evade kaupa"),
				);
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_res_style":
				$prop["options"] = array(
					"stat" => t("Statistika"),
					"weekdays" => t("N&auml;dalap&auml;evade kaupa"),
					"days" => t("P&auml;evade kaupa"),
					"list" => t("Nimekiri"),
				);
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_name":
				if($prop["caption"] != $prop["orig_caption"])
				{
					$this->new_captions[$prop["name"]] = $prop["caption"];
				}
				$prop["options"] = array("" => "") + $this->get_all_room_names();
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_project":
				$prop["options"] = array("" => "") + $this->get_all_project_names();
				if($prop["caption"] != $prop["orig_caption"])
				{
					$this->new_captions[$prop["name"]] = $prop["caption"];
				}
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_weekdays":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_booker_name":
			case "r_ra_seller":
			case "r_ra_worker":
			case "r_ra_booking_to":
			case "r_ra_unconfirmed":
			case "r_ra_res_type":
				if($prop["caption"] != $prop["orig_caption"])
				{
					$this->new_captions[$prop["name"]] = $prop["caption"];
				}
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "r_ra_list":
				$this->_get_report_table($arr);
				break;
			case "r_ra_days_projects_tab":
				return PROP_IGNORE;
				$this->_get_dp_table($arr);
				break;
			case "ra_tb":
				$this->_get_rr_tb($arr);
				break;
		};
		return $retval;
	}

	function get_all_room_names()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.name"
		));
		return $ol->names();
	}

	function get_all_project_names()
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.name"
		));
		return $ol->names();
	}

	function get_all_sellers()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$pro = array();
		foreach($ol->arr() as $room)
		{
			if(is_array($room->prop("seller_professions")))
			{
				$pro = $pro  + $room->prop("seller_professions");
			}
		}
		if(!sizeof($pro))
		{
			return array();
		}
		$ol2 = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $pro,
					"oid" => $pro,
				)
			)),
		));
		return $ol2->names();
	}

	function get_all_workers()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$pro = array();
		foreach($ol->arr() as $room)
		{
			if(is_array($room->prop("professions")))
			{
				$pro = array_merge($pro , $room->prop("professions"));
			}
		}
		if(!sizeof($pro))
		{
			return array();
		}
		$ol2 = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $pro,
					"oid" => $pro,
				)
			)),
		));
		return $ol2->names();
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "rooms_folder":
				if ($arr["request"]["group"] != "general")
				{
					return PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["type"] = "";
		$arr["category"] = "";
		$arr["from"] = "";
		$arr["to"] = "";
		$arr["room"] = "";
		$arr["timespan"] = "";
	}

	function callback_mod_retval($arr)
	{
		$room_s_props = array("to" , "from" , "category" , "type", "room" , "timespan");
		foreach($room_s_props as $prop)
		{
			$arr["args"][$prop] = $arr["request"][$prop];
		}
		
		$arr["args"]["rs_name"] = $arr["request"]["rs_name"];
		$arr["args"]["rs_booker_name"] = $arr["request"]["rs_booker_name"];
		$arr["args"]["rs_booking_from"] = $arr["request"]["rs_booking_from"];
		$arr["args"]["rs_booking_to"] = $arr["request"]["rs_booking_to"];
		$arr["args"]["rs_unconfirmed"] = $arr["request"]["rs_unconfirmed"];

		foreach($this->stats_search_properties as $prop)
		{
			$arr["args"][$prop] = $arr["request"][$prop];
		}
		if($arr["request"]["r_ra_only_done2"])
		{
			$arr["args"]["r_ra_only_done"] = $arr["request"]["r_ra_only_done2"];//porno, a ei osanud mujale
		}
		$arr["args"]["r_rs_name"] = $arr["request"]["r_rs_name"];
		$arr["args"]["r_rs_booker_name"] = $arr["request"]["r_rs_booker_name"];
		$arr["args"]["r_rs_booking_from"] = $arr["request"]["r_rs_booking_from"];
		$arr["args"]["r_rs_booking_to"] = $arr["request"]["r_rs_booking_to"];

		$arr["args"]["stats_rs_name"] = $arr["request"]["stats_rs_name"];
		$arr["args"]["stats_rs_booker_name"] = $arr["request"]["stats_rs_booker_name"];
		$arr["args"]["stats_rs_booking_from"] = $arr["request"]["stats_rs_booking_from"];
		$arr["args"]["stats_rs_booking_to"] = $arr["request"]["stats_rs_booking_to"];
		$arr["args"]["stats_rs_package"] = $arr["request"]["stats_rs_package"];
	}	

	function _get_rooms_tree($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->prop("rooms_folder")))
		{
			mail("kristo@struktuur.ee", "rumide kaust", "valimata ".$arr["obj_inst"]->id());
			die("Tubade kaust on valimata, palun valige see <a href='/automatweb/orb.aw?class=spa_bookings_overview&action=change&id=".$arr["obj_inst"]->id()."'>siit</a>");
		}
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "rooms_ovtr",
				"persist_state" => true,
			),
			"root_item" => obj($arr["obj_inst"]->prop("rooms_folder")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $arr["obj_inst"]->prop("rooms_folder")
			)),
			"var" => "tf"
		));
	}

	function _get_room_management_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "cat";
/*
		$tv = treeview::tree_from_objects(array(
			"tree_opts" => array(
			"type" => TREE_DHTML,
				"tree_id" => "room_cat_tree",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_ROOM_CATEGORY),
				"parent" =>  $arr["obj_inst"]->id(),
			)),
			"var" => "cat"
		));*/

		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "rooms_cat_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik kategooriad"),
			"id" => "all",
			"url" => "javascript:$('[name=category]').val('all'); update_rooms_table();"//aw_url_change_var("cat", $id),

//			"url" => aw_url_change_var($var, "all"),
		));

		foreach($arr["obj_inst"]->get_category_names() as $id => $name)
		{
			$tv->add_item(0,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=category]').val('".$id."'); update_rooms_table();"//aw_url_change_var("cat", $id),
			));
			$this->add_cat_leaf($tv , $id);
		}
	}

	function add_cat_leaf($tv , $parent)
	{
		if(!is_oid($parent))
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => CL_ROOM_CATEGORY,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $parent,
		));
		foreach($ol->names() as $id => $name)
		{
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=category]').val('".$id."'); update_rooms_table();",
			));
			$this->add_cat_leaf($tv , $id);
		}
	}

	function _get_reservation_management_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "room";

		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "rooms_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik kategooriad"),
			"id" => "all",
			"url" => "javascript:$('[name=room]').val('all'); update_bron_table();"//aw_url_change_var("cat", $id),
		));

		foreach($arr["obj_inst"]->get_category_names() as $id => $name)
		{
			$tv->add_item(0,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=room]').val('".$id."'); update_bron_table();",//aw_url_change_var("cat", $id),
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			$this->add_room_leaf($tv , $id);
		}
	}


	function add_room_leaf($tv , $parent)
	{
		if(!is_oid($parent))
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => CL_ROOM_CATEGORY,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $parent,
		));
		foreach($ol->names() as $id => $name)
		{
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=room]').val('".$id."'); update_bron_table();",//aw_url_change_var("cat", $id),
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			$this->add_cat_leaf($tv , $id);
		}
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_ROOM.RELTYPE_CATEGORY" => $parent,
		));
		foreach($ol->names() as $id => $name)
		{
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"iconurl" => icons::get_icon_url(CL_ROOM),
				"url" => "javascript:$('[name=room]').val('".$id."'); update_bron_table();"//aw_url_change_var("cat", $id),
			));
		}
	}

	function _get_room_management_type_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "type";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "all");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "rooms_cat_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik t&uuml;&uuml;bid"),
			"id" => "all",
			"url" => aw_url_change_var($var, "all"),
		));
	}

	function _get_room_management_capacity_from_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "from";
		$tv->set_selected_item(is_numeric($arr["request"][$var]) ? $arr["request"][$var] : "all");
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "rooms_cat_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik"),
			"id" => "all",
			"url" => "javascript:$('[name=from]').val('all'); update_rooms_table();update_from_tb();"//aw_url_change_var("cat", $id),
//			"url" => aw_url_change_var($var, "all"),
		));

		$max_cap = $arr["obj_inst"]->get_max_capacity();

		$x = 1;
		while($x < $max_cap/10+1)
		{
			$y = 1;
			while($y <= 10)
			{
				$id = ($x-1)*10 + $y;
				$tv->add_item($x*10000, array(
					"id" => ($x-1)*10 + $y."",
					"name" => ($x-1)*10 + $y,
					"url" => "javascript:$('[name=from]').val('".$id."'); update_rooms_table();update_from_tb();"//aw_url_change_var("cat", $id),
//					"url" => aw_url_change_var(array(
//						$var => ($x-1)*10 + $y,
//					)),
				));
				$y++;
			}

				
			$tv->add_item(0, array(
				"id" => $x*10000,
				"name" => ($x-1)*10 . " - " . $x*10,
			));
			$x++;
		}
	}

	function _get_room_management_capacity_to_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "to";
		$tv->set_selected_item(is_numeric($arr["request"][$var]) ? $arr["request"][$var] : "all");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "rooms_cat_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik"),
			"id" => "all",
			"url" => "javascript:$('[name=to]').val('".$id."'); update_rooms_table();update_to_tb();"//aw_url_change_var("cat", $id),
//			"url" => aw_url_change_var($var, "all"),
		));
		$max_cap = $arr["obj_inst"]->get_max_capacity();

		$x = 1;
		while($x < $max_cap/10+1)
		{
			$y = 1;
			while($y <= 10)
			{
				$id = ($x-1)*10 + $y;
				$tv->add_item("to".$x*10000, array(
					"id" => ($x-1)*10 + $y."",
					"name" => ($x-1)*10 + $y,
					"url" => "javascript:$('[name=to]').val('".$id."'); update_rooms_table();update_to_tb();"//aw_url_change_var("cat", $id),
//					"url" => aw_url_change_var(array(
//						$var => ($x-1)*10 + $y,
//					)),
				));
				$y++;
			}

			$tv->add_item(0, array(
				"id" => "to".$x*10000,
				"name" => ($x-1)*10 . " - " . $x*10,
			));
			$x++;
		}
	}

	function _get_reservation_management_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Broneeringute nimekiri"));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "left",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "floor",
			"caption" => t("Korrus"),
			"align" => "right",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "corps",
			"caption" => t("Korpus"),
			"align" => "right",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Number"),
			"align" => "right",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "right",
			"sortable" => 1,
		));

 		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));
		$data = array();
		if(isset($arr["request"]["timespan"]))
		{
			$data = $this->get_range($arr["request"]["timespan"]);
		}
		if($this->can("view" , $arr["request"]["room"]))
		{
			$room_object = obj($arr["request"]["room"]);
			if($room_object->class_id() == CL_ROOM)
			{
				$data["room"] = $arr["request"]["room"];
			}
			else
			{
				$data["category"] = $arr["request"]["room"];
			}
		}

		$data["name"] = $arr["request"]["name"];

		$reservations = $arr["obj_inst"]->get_reservations($data);
		$currencies = $arr["obj_inst"]->get_currencies($data);
		foreach($reservations->arr() as $reservation)
		{
			$sumstuff = array();
			foreach($reservation->get_sum() as $cur => $sum)
			{
				$sumstuff[] = $sum." ".$currencies[$cur];
			}
			$t->define_data(array(
				"name" => html::obj_change_url($reservation,null,array("return_url" => $this->mk_my_orb("change" , array("class" => "spa_bookings_overview","id" => $arr["obj_inst"]->id() , "group" => "room_management" )))),
				"id" => $reservation->id(),
				"nr" => $reservation->prop("resource.nr"),
				"floor" => $reservation->prop("resource.floor"),
				"corps" => $reservation->prop("resource.corps"),
				"time" => ($reservation->prop("start1") > 0 ? date("d.m.Y h:i" , $reservation->prop("start1")) : "") . " - " . ($reservation->prop("end") > 0 ? date("d.m.Y h:i" , $reservation->prop("end")) : ""),
				"price" => join("<br>" , $sumstuff),
			));
		}
	}

	function _get_room_management_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "floor",
			"caption" => t("Korrus"),
			"align" => "right",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "corps",
			"caption" => t("Korpus"),
			"align" => "right",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Number"),
			"align" => "right",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "normal",
			"caption" => t("Normaalne mahutavus"),
			"align" => "right",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "max",
			"caption" => t("Maksimaalne mahutavus"),
			"align" => "right",
			"sortable" => 1,
		));
 		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id",
		));
		$data = array("cap_to" => $arr["request"]["to"]);
		$data["cap_from"] = $arr["request"]["from"];
		$data["cat"] = $arr["request"]["cat"];
		$data["name"] = $arr["request"]["name"];

		$rooms = $arr["obj_inst"]->get_rooms($data);
		
		foreach($rooms->arr() as $room)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($room,null,array("return_url" => $this->mk_my_orb("change" , array("class" => "spa_bookings_overview","id" => $arr["obj_inst"]->id() , "group" => "room_management" )))),
				"id" => $room->id(),
				"nr" => $room->prop("nr"),
				"floor" => $room->prop("floor"),
				"corps" => $room->prop("corps"),
				"max" => $room -> prop("max_capacity"),
				"normal" => $room -> prop("normal_capacity")
			));
		}
	}


	function _init_r_list(&$t, $selectah = true)
	{
		$t->define_field(array(
			"name" => "cal",
			"caption" => t("&nbsp;"),
			"align" => "center",
			"sortable" => 1,
			"width" => 1
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "bron",
			"caption" => t("Broneering"),
			"align" => "center",
			"sortable" => 1
		));
//		if ($selectah)
//		{
			$t->define_chooser(array(
				"name" => "sel",
				"field" => "oid"
			));
//		}
	}

	function _get_report_table(&$arr)
	{
		if($arr["request"]["do_print"])
		{
			$this->do_print_results = $arr["request"]["do_print"];
		}
//		if(!$arr["request"]["r_ra_res_type"])
//		{
//			$arr["request"]["r_ra_res_type"] = "rooms";
//		}


		$this->result_data = $arr["request"]["r_ra_res_type"];
		$this->result_type = $arr["request"]["r_ra_res_style"];

		$brons = $this->get_brons($arr);

		$fun_name = "get_".$this->result_data."_table";
		if($this->result_type == "days")
		{
			$fun_name = "get_days_table";
		}
		enter_function("spa_bookings_owverview::report_table");
		$this->$fun_name(array(
			"brons" => $brons,
			"t" => &$arr["prop"]["vcl_inst"],
		));
		exit_function("spa_bookings_owverview::report_table");
		if ($this->do_print_results == 1)
		{
			$i = new aw_template();
			$i->init("automatweb");
			$i->read_template("index.tpl");
			$i->vars(array(
				"content" => $t->draw()
			));
			die($i->parse()."<script language=javascript>window.print();</script>");
		}
		if ($this->do_print_results == 2)
		{
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate");
			$file = "stats". date("ymdhis") . ".csv";
			header("Content-type: text/csv; charset=".aw_global_get('charset'));
			header("Content-disposition: inline; filename=".$file.";");
			die(html_entity_decode($arr["prop"]["vcl_inst"]->get_csv_file()));
		}
	}

	function get_project_caption()
	{
		return $this->new_captions["r_ra_project"] ? $this->new_captions["r_ra_project"] : t("Projekt");
	}

	function get_booker_caption()
	{
		return $this->new_captions["r_ra_booker_name"] ? $this->new_captions["r_ra_booker_name"] : t("Broneerija");
	}

	function get_days_table($arr)
	{
		extract($arr);

		$step = DAY_LENGTH;
		$start = date_edit::get_timestamp($_GET["r_ra_booking_from"]);
		$end = date_edit::get_timestamp($_GET["r_ra_booking_to"]);

		$props = array(
			"" => "class_id",
			"rooms" => "resource",
			"sellers" => "inbetweener",
			"workers" => "people",
			"projects" => "project",
		);

		$t->define_field(array(
			"name" => "p",
			"caption" => t("aeg"),
			"chgbgcolor" => "color",
		));

		if(!$end > 1)
		{
			$end = time();
		}
		if(!($start > 1))
		{
			$start = ($end - 30 * DAY_LENGTH);
		}

		$data = array();
		$proj_sum = array();
		$day_sum = array();
		$sum = 0;
		$projects = array();
		foreach ($brons->arr() as $b)
		{
			$project_id = $b->prop($props[$this->result_data]);
			if(!$project_id)//et tyhjad ja 0 jne eraldi v2ljadesse ei l2heks
			{
				$project_id = "0";
			}
			$projects[$project_id] = $project_id;
			$data[date("Ymd" , $b->prop("start1"))][$project_id]++;
			$proj_sum[$project_id]++;
			$day_sum[date("Ymd" , $b->prop("start1"))]++;
			$sum++;
			$data[date("Ymd" , $b->prop("start1"))]["time"] = $b->prop("start1");
		}
		ksort($data);

		if($this->result_data)
		{
			foreach($projects as $project)
			{
				$p = t("(M&auml;&auml;ramata)");
				if($this->can("view" , $project))
				{
					$po = obj($project);
					$p = $po->name()?$po->name():t("(Nimetu)");
				}
				$t->define_field(array(
					"name" => $project,
					"caption" => $p,
					"chgbgcolor" => "color",
				));
			}
		}
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Kokku"),
			"chgbgcolor" => "color",
		));

		$t->set_sortable(false);
		while($start < $end)
		{
			$val = $data[date("Ymd" , $start)];
			$val["p"] = substr(date("l" , $start) , 0 , 2).date(" d/m/y" , $start);
			$val["sum"] = $day_sum[date("Ymd" , $start)];
			$t->define_data($val);
			$start = $start + DAY_LENGTH;
		}
		
		$proj_sum["p"] = t("Kokku:");
		$proj_sum["sum"] = $sum;
		$t->define_data($proj_sum);
	}

	function get_sellers_table($arr)
	{
		extract($arr);
		$this->_init_brons_table($t, t("Vahendajad"));
		$data_array = $this->get_data_array(array(
			"brons" => $brons,
			"prop" => "inbetweener"
		));
		
		$this->do_stuff_with_table(array(
			"t" => &$t,
			"data" => &$data_array,
			"caption" => t("Vahendajad"),
		));
	}

	function get__table($arr)
	{
		extract($arr);
		$this->_init_brons_table($t, "");
		$data_array = $this->get_data_array(array(
			"brons" => $brons,
			"prop" => "class_id"
		));
		
		$this->do_stuff_with_table(array(
			"t" => &$t,
			"data" => &$data_array,
			"caption" => t("T&ouml;&ouml;tajad"),
		));
	}

	function get_workers_table($arr)
	{
		extract($arr);
		$this->_init_brons_table($t, t("T&ouml;&ouml;tajad"));
		$data_array = $this->get_data_array(array(
			"brons" => $brons,
			"prop" => "people"
		));
		
		$this->do_stuff_with_table(array(
			"t" => &$t,
			"data" => &$data_array,
			"caption" => t("T&ouml;&ouml;tajad"),
		));
	}

	function get_projects_table($arr)
	{
		extract($arr);
		$this->_init_brons_table($t, $this->get_project_caption());
		$data_array = $this->get_data_array(array(
			"brons" => $brons,
			"prop" => "project"
		));
		
		$this->do_stuff_with_table(array(
			"t" => &$t,
			"data" => &$data_array,
			"caption" => t("Projektid"),
		));
	}

	function do_stuff_with_table($arr)
	{
		extract($arr);
		$tm_cnt=0;
		$b_cnt = 0;
		$redec = 0;
		$t->set_sortable(false);

		$sum_array = array();

		foreach($data as $prop => $val)
		{
			$prop_name = "";
			if(is_oid($prop) && $this->can("view" , $prop))
			{
				$r = obj($prop);
				$prop_name = html::obj_change_url($r);
			}

			$def_data = array();
			foreach($val as $key => $v)
			{
				$def_data[$key] = $v;
				$sum_array[$key]+= $v;
			}
			$def_data["param"] = $prop_name;
			$def_data["tm"] = number_format(($val["time"]/3600) , 2);
			$t->define_data($def_data);

			$redec = $redec + $val["redecleared"];
			$tm_cnt = $tm_cnt + $val["time"];
			$b_cnt = $b_cnt + $val["brons"];
		}

		if($this->result_type != "list" && $this->result_data != "")
		{
			$sum_array["param"] = t("Kokku:");
			$sum_array["tm"] = number_format(($sum_array["time"]/3600) , 2);
			$sum_array["color"] = "grey";
			$t->define_data($sum_array);
		}
	}

	function get_data_array($arr)
	{
		extract($arr);
		$bron_array = array();
		$bron_inst = get_instance(CL_RESERVATION);
		$reasons = $bron_inst->reason_list();
		switch($this->result_type)
		{
			case "weekdays":
				foreach($brons->arr() as $b)
				{
					$bron_array[$b->prop($prop)][date("w" , $b->prop("start1"))]++;
					$bron_array[$b->prop($prop)]["total"]++;
				}
				break;
			case "list":
				if($prop) $last = "";
				foreach($brons->arr() as $b)
				{
					if($prop && $last != $b->prop($prop))
					{
						$last = $b->prop($prop);
						if($last) $bron_array[] = array("time" => $b->prop($prop.".name"));
					}
					$bron_array[$b->id()]["room"] = $b->prop("resource.name");
					$bron_array[$b->id()]["person"] = $b->prop("customer.name");
					$bron_array[$b->id()]["not_reason"] = $reasons[$b->prop("client_arrived")];
					$bron_array[$b->id()]["time"] = date("d.m.Y h:i",$b->prop("start1"))." - ".date("d.m.Y h:i",$b->prop("end"));
				}
				break;
			default:
				foreach($brons->arr() as $b)
				{
					$index = $b->prop($prop);
					if(!$index)
					{
						$index = 0;
					}
					if(($b->prop("end") - $b->prop("start1")) > 0)
					{
						$bron_array[$index]["time"] = $bron_array[$index]["time"] + ($b->prop("end") - $b->prop("start1"));
					}
					$bron_array[$index]["brons"]++;
					if($b->meta("redecleared"))
					{
						$bron_array[$index]["redecleared"] ++;
					}
				}
		}
		return $bron_array;
	}

	function _init_brons_table($t,$caption)
	{
		if($this->result_data && $this->result_type != "list")//seda vaja vaid siis kui on mille j2rgi grupeerida ja ei n2idata nimekirja
		{
			$t->define_field(array(
				"name" => "param",
				"caption" => $caption,
				"chgbgcolor" => "color",
			));
		}
		switch($this->result_type)
		{
			case "weekdays":
				$x = 1;
				while($x < 8)
				{
					$t->define_field(array(
						"name" => $x==7?"0":"$x",
						"caption" => aw_locale::get_lc_weekday($x , 1 , 1),
						"chgbgcolor" => "color",
					));
					$x++;
				}
				$t->define_field(array(
					"name" => "total",
					"caption" => t("Kokku"),
					"chgbgcolor" => "color",
				));
				break;
			case "list":
				$t->define_field(array(
					"name" => "time",
					"caption" => t("aeg"),
					"chgbgcolor" => "color",
				));
		
				$t->define_field(array(
					"name" => "room",
					"caption" => t("Ruum"),
					"chgbgcolor" => "color",
				));
		
				$t->define_field(array(
					"name" => "person",
					"caption" => $this->get_booker_caption(),
					"chgbgcolor" => "color",
				));
		
				$t->define_field(array(
					"name" => "not_reason",
					"caption" => t("Mitteilmumise p&otilde;hjus"),
					"chgbgcolor" => "color",
				));
				break;

			default:
				$t->define_field(array(
					"name" => "brons",
					"caption" => t("Broneeringuid"),
					"chgbgcolor" => "color",
				));
				$t->define_field(array(
					"name" => "tm",
					"caption" => t("Aeg"),
					"chgbgcolor" => "color",
				));
				$t->define_field(array(
					"name" => "redecleared",
					"caption" => t("&Uuml;mber registreeritud"),
					"chgbgcolor" => "color",
					//"width" => 100,
				));
				break;
		}
	}

	function get_rooms_table($arr)
	{
		extract($arr);
		$this->_init_brons_table($t, t("Ruumid"));
		$data_array = $this->get_data_array(array(
			"brons" => $brons,
			"prop" => "resource"
		));
		
		$this->do_stuff_with_table(array(
			"t" => &$t,
			"data" => &$data_array,
			"caption" => t("Ruumid"),
		));

/*
		extract($arr);
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "worker",
			"caption" => t("T&ouml;&ouml;taja"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "brons",
			"caption" => t("Broneeringuid"),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "tm",
			"caption" => t("Aeg"),
			"chgbgcolor" => "color",
		));
		
		$bron_array = array();
		
		foreach($brons->arr() as $b)
		{
			if(($b->prop("end") - $b->prop("start1")) > 0) $bron_array[$b->prop("resource")][$b->prop("people")]["time"] =  $bron_array[$b->prop("resource")][$b->prop("people")]["time"] + ($b->prop("end") - $b->prop("start1"));
			$bron_array[$b->prop("resource")][$b->prop("people")]["brons"]++;
		}

		$tm_cnt=0;
		$b_cnt = 0;
		$t->set_sortable(false);
		foreach($bron_array as $room => $data)
		{
			$room_name = "";
			if(is_oid($room) && $this->can("view" , $room))
			{
				$r = obj($room);
				$room_name = html::obj_change_url($r);
			}
			$room_tm_cnt = $room_b_cnt = 0;
			$t->define_data(array(
				"room" => $room_name,
			));
			foreach($data as $person => $data)
			{
				$room_b_cnt = $room_b_cnt + $data["brons"];
				$b_cnt = $b_cnt + $data["brons"];
				$worker = "";
				if(is_oid($person) && $this->can("view" , $person))
				{
					$w = obj($person);
					$worker = html::obj_change_url($w);
				}
				$t->define_data(array(
					"worker" => $worker,
					"tm" => number_format(($data["time"]/3600) , 2),
					//"tm" => ($data["time"] > 1) ? $data["time"] : 0,
					"brons" => $data["brons"],
				));
				if($data["time"] > 1) $room_tm_cnt = $room_tm_cnt + $data["time"];
			}
			$tm_cnt = $tm_cnt + $room_tm_cnt;
			$t->define_data(array(
				"worker" => t("kokku"),
			//	"tm" => $room_tm_cnt,
				"tm" => number_format(($room_tm_cnt/3600) , 2),
				"brons" => $room_b_cnt,
				"color" => "grey",
			));
		}

		$t->define_data(array(
			"room" => t("Kokku"),
			"tm" => number_format(($tm_cnt/3600) , 2),
			"brons" => $b_cnt,
				"color" => "grey",
		));
		if ($this->do_print_results)
		{
			$i = new aw_template();
			$i->init("automatweb");
			$i->read_template("index.tpl");
			$i->vars(array(
				"content" => $t->draw()
			));
			die($i->parse()."<script language=javascript>window.print();</script>");
		}*/
	}

	function get_brons($arr)
	{
		enter_function("spa_bookings_owverview::get_brons");
		extract($arr["request"]);
		$filter = array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "planner.start",
//			"verified" => 1,
		);

		if($this->result_type == "list")
		{
			switch($this->result_data)
			{
				case "rooms":
					$filter["sort_by"] = "aw_room_reservations.aw_resource , planner.start";
					break;
				case "sellers":
					$filter["sort_by"] = "aw_room_reservations.aw_inbetweener , planner.start";
					break;
				case "workers":
					$filter["sort_by"] = "aw_room_reservations.aw_people , planner.start";
					break;
				case "workers":
					$filter["sort_by"] = "planner.project , planner.start";
					break;
				default:
					$filter["sort_by"] = "planner.start";
				break;
			}
		}

		//kinnitamata pole vaja n2ha
		if(!$r_ra_unconfirmed)
		{
			$filter["verified"] = 1;
		}

		//ruumi alusel
		if($r_ra_name)
		{
			//$filter["CL_RESERVATION.RELTYPE_RESOURCE.name"] = "%".$r_ra_name."%";
			$filter["CL_RESERVATION.RELTYPE_RESOURCE.id"] = $r_ra_name;
		}

		//broneerija
		if($r_ra_booker_name)
		{
			$filter["CL_RESERVATION.RELTYPE_CUSTOMER.name"] = "%".$r_ra_booker_name."%";
		}

		//vahendaja
		if($r_ra_seller)
		{
			$filter["inbetweener"] = "%".$r_ra_seller."%";
		}

		//t88taja
		if($r_ra_worker)
		{
			$filter["people"] = "%".$r_ra_worker."%";
		}

		//projekt
		if($r_ra_project)
		{
			//$filter["CL_RESERVATION.RELTYPE_PROJECT.name"] = "%".$r_ra_project."%";
			$filter["CL_RESERVATION.RELTYPE_PROJECT.id"] = $r_ra_project;
		}

		//asukoht
		if($r_ra_location)
		{
			$params["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
				"tree_opts" => array(
				"type" => TREE_DHTML,
					"tree_id" => "rooms_ovtr",
					"persist_state" => true,
				),
				"root_item" => obj($r_ra_location),
				"ot" => new object_tree(array(
					"class_id" => array(CL_MENU),
					"parent" => $r_ra_location
				)),
				"var" => "tf"
			));

			$locs = $params["prop"]["vcl_inst"]->get_item_ids();
			$filter["CL_RESERVATION.RELTYPE_RESOURCE.parent"] = $locs;
		}

		//broneerimis aja j2rgi
		$from = date_edit::get_timestamp($r_ra_booking_from);
		$to = date_edit::get_timestamp($r_ra_booking_to);
		if ($from > 1)
		{
			$filter["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		elseif(!$r_ra_booking_from)
		{
			$filter["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time());
		}
		if ($to > 1)
		{
			$filter["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, get_day_start($to + DAY_LENGTH));
		}

		//saabumise j2rgi
		if($r_ra_only_done == 1)
		{
			$filter["client_arrived"] = 1;
		}
		if($r_ra_only_done == 2)
		{
			$filter["client_arrived"] = 0;
		}
		if($r_ra_only_done > 2)
		{
			$filter["client_arrived"] = $r_ra_only_done;
		}

		//maksmise j"rgi
		if($r_ra_only_paid == 1)
		{
			$filter["paid"] = 1;
		}
		if($r_ra_only_paid == 2)
		{
			$filter["paid"] = 0;
		}
		$ol = new object_list($filter);
		exit_function("spa_bookings_owverview::get_brons");
		return $ol;
	}

	function _get_r_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$ol = new object_list();

		$from = date_edit::get_timestamp($arr["request"]["rs_booking_from"]);
		$to = date_edit::get_timestamp($arr["request"]["rs_booking_to"]);

		$srch = !empty($arr["request"]["rs_name"]) || !empty($arr["request"]["rs_booker_name"]) || $from > 1 || $to > 1 || $arr["request"]["rs_unconfirmed"];	
		$room2booking = array();
		if ($srch)
		{
			$room2booking = array();
			$f = array(
				"class_id" => CL_ROOM,
				"lang_id" => array(),
				"site_id" => array()
			);
			if (!empty($arr["request"]["rs_name"]))
			{
				$f["name"] = "%".$arr["request"]["rs_name"]."%";
			}

			if (!empty($arr["request"]["rs_booker_name"]) || $from > 1 || $to > 1 || $arr["request"]["rs_unconfirmed"])
			{
				// get all bookings for that person
				$bf = array(
					"class_id" => CL_RESERVATION,
					"verified" => 1,			
					"lang_id" => array(),
					"site_id" => array(),
				);
				if (!empty($arr["request"]["rs_booker_name"]))
				{
					$bf["CL_RESERVATION.RELTYPE_CUSTOMER.name"] = "%".$arr["request"]["rs_booker_name"]."%";
				}
				if ($from > 1)
				{
					$bf["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
				}
				else
				{
					$bf["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, 1);
				}

				if ($to > 1)
				{
					$to += DAY_LENGTH - 1;//p2eva l6puni ikka
					$bf["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
				}

				if (!empty($arr["request"]["rs_unconfirmed"]))
				{
					$bf["verified"] = 0;
				}

				$bookings = new object_list($bf);
				$rooms = array();
				foreach($bookings->arr() as $booking)
				{
					$rooms[$booking->prop("resource")] = $booking->prop("resource");
					if (empty($arr["request"]["rs_booker_name"]) ||  substr_count($booking->prop("customer.name"), $arr["request"]["rs_booker_name"]))
					{
						$room2booking[$booking->prop("resource")][] = $booking;
					}
				}
				if (count($rooms))
				{
					$f["oid"] = $rooms;
				}
				else
				{
					$f["oid"] = -1;
				}
			}
			$rf = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_RF");
			$flds = new object_tree(array("parent" => $rf->id(), "class_id" => CL_MENU, "lang_id" => array(), "site_id" => array()));
			$f["parent"] = $this->make_keys($flds->ids());
			$f["parent"][$rf->id()] = $rf->id();
			
			$f["sort_by"] = "objects.jrk";
			$ol = new object_list($f);
		}
		else
		{
			$rf = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_RF");
			$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $rf->id();
			$ol = new object_list(array(
				"class_id" => CL_ROOM,
				"parent" => $pt,
				"lang_id" => array(),
				"site_id" => array(),
				"sort_by" => "objects.jrk"
			));
		}

		if (count($room2booking))
		{
			$this->_init_r_list($t, false);
			// group table by ppl and do several rows for one room if necessary
			$ppl = array();
			$ppl_all = array();
			foreach($room2booking as $room_id => $books)
			{
				foreach($books as $booking)
				{
					foreach($booking->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$_po = $c->to();
						$ppl[$_po->brother_of()][] = $booking;
					}
				}
			}

			$tmp = array();
			foreach($ppl as $cust_id => $bookings)
			{
				$cust_o = obj($cust_id);
				if (isset($tmp[$cust_o->name()]))
				{
					foreach($bookings as $booking)
					{
						$tmp[$cust_o->name()]["bookings"][] = $booking;
					}
				}
				else
				{
					$tmp[$cust_o->name()] = array(
						"id" => $cust_id,
						"bookings" => $bookings
					);
				}
			}

			foreach($tmp as $data) //$cust_id => $bookings)
			{
				$cust_id = $data["id"];
				$bookings = $data["bookings"];
				$rvs_ids = array();
				foreach($bookings as $booking)
				{
					$rvs_ids[] = $booking->id();
				}
				$p_obj = obj($cust_id);
				if ($arr["request"]["rs_booker_name"] != "" && strpos(strtolower($p_obj->name()), strtolower($arr["request"]["rs_booker_name"])) === false)
				{
					continue;
				}
				$ppl_links = array();
				$p_str = html::obj_change_url($p_obj)." / ".html::href(array(
					"url" => $this->mk_my_orb("print_person_chart", array("person" => $cust_id, "rvs" => $rvs_ids, "center" => $arr["obj_inst"]->id())),
					"caption" => t("Prindi"),
					"target" => "_blank"
				));
				foreach($bookings as $booking)
				{
					if($this->can("view" , $booking->prop("resource")))
					{
						$t->define_data(array(
							"cal" => html::get_change_url($booking->prop("resource"), array("return_url" => get_ru(), "group" => "calendar"), icons::get_icon(obj($booking->prop("resource")))),
							"room" => html::get_change_url($booking->prop("resource"), array("return_url" => get_ru(), "group" => "calendar"), $booking->prop("resource.name")),
							"bron" => html::get_change_url($booking->id(), array("return_url" => get_ru()), date("d.m.Y", $booking->prop("start1"))." / ".date("H:i", $booking->prop("start1"))." - ".date("H:i", $booking->prop("end"))),
							"oid" => $booking->id(),//prop("resource"),
							"person" => $p_str
						));
					}
				}
			}
			$t->sort_by(array(
				"rgroupby" => array("person" => "person")
			));
		}
		else
		{
			$this->_init_r_list($t, true);
			foreach($ol->arr() as $o)
			{
				$t->define_data(array(
					"cal" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "calendar"), icons::get_icon($o)),
					"room" => html::get_change_url($o->id(), array("return_url" => get_ru(), "group" => "calendar"), $o->trans_get_val("name")),
					"oid" => $o->id()
				));
			}
		}
		$t->set_sortable(false);
	}

	function _get_rs_name($arr)
	{
		if(!$arr["request"]["tf"])
		{
			$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
		}
	}

	function _get_rs_booker_name($arr)
	{
		if(!$arr["request"]["tf"])
		{
			$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
		}
	}

	function _get_rs_booking_from($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")-1, date("Y"));
		}
		if(!$arr["request"]["tf"])
		{
			$arr["prop"]["value"] = $v;
		}
	}

	function _get_rs_booking_to($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")+7, date("Y"));
		}
		if(!$arr["request"]["tf"])$arr["prop"]["value"] = $v;
	}

	function _get_room_management_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Kategooria"),
			"link" => "javascript:add_cat();"
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Ruum"),
			"link" => "javascript:add_room();"
		));

		$tb->add_button(array(
			"name" => t("Make room copy"),
			"img" => "copy.gif",
			"tooltip" => t("Loo ruumist koopia"),
			"url" => "javascript:copy_room();"
		));

		$tb->add_delete_button();
	}

	function _get_reservation_management_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_delete_button();
	}

	function _get_r_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_ROOM), $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->prop("rooms_folder"));
		$tb->add_delete_button();
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"img" => "cut.gif",
			"action" => "cut_b",
		));
		$tb->add_button(array(
			"name" => "copy",
			"tooltip" => t("Kopeeri"),
			"img" => "copy.gif",
			"action" => "copy_b",
		));
		if (is_array($_SESSION["spa"]["cut"]) && count($_SESSION["spa"]["cut"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"img	" => "paste.gif",
				"action" => "paste_b",
			));
		}
		$tb->add_separator();
		$url = $this->mk_my_orb("show_cals_pop", array(
			"id" => $arr["obj_inst"]->id(),
			"in_popup" => "1",
			"rooms" => "0"//see peab olema massiivis viimane
		));
		$tb->add_button(array(
			"name" => "cal",
			"tooltip" => t("Kalender"),
			"img" => "icon_cal_today.gif",
			"onClick" => "vals='';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('sel') != -1 && f[i].checked) {vals += ','+f[i].value;}};if (vals != '') {aw_popup_scroll('$url'+vals,'mulcal',700,500);} else { alert('".t("Valige palun v&auml;hemalt &uuml;ks ruum!")."');} return false;",
		));

		$tb->add_menu_button(array(
			"name" => "print",
			"img" => "print.gif",
		));

		$rooms = new object_list(array(
			"class_id" => CL_ROOM,
			"lang_id" => array(),
			"name" => "%".$arr["request"]["rs_name"]."%",
		));

		$extra = array("rooms" => join(",",$rooms->ids()));
		if($arr["request"]["rs_unconfirmed"])
		{
			$extra["unverified"] = 1;
		}
		if($arr["request"]["rs_booker_name"])
		{
			$extra["person"] = $arr["request"]["rs_booker_name"];
		}
		$submenu_link = $this->mk_my_orb("room_booking_printer", array(
			"in_popup" => "1",
			"from" => date_edit::get_timestamp($arr["request"]["rs_booking_from"]),
			"to" => date_edit::get_timestamp($arr["request"]["rs_booking_to"]),
			"group" => $grp,
		) + $extra);

		$tb->add_sub_menu(array(
			"parent" => "print",
			"name" => "p_all_g",
			"text" => t("K&otilde;ik"),
//			"link" => "javascript:aw_popup_scroll('".$submenu_link."','mulcal',700,500);",
			"link" => "javascript:goto_overview_url('');",
		
		));

		// add yesterday/today/tomorrow subs
		$this->_add_day_subs($tb, "p_all_g", null);

		foreach(safe_array($arr["obj_inst"]->prop("groups")) as $grp_id)
		{
			if(!$this->can("view" , $grp_id))
			{
				continue;
			}
			$go = obj($grp_id);

			$submenu_link = $this->mk_my_orb("room_booking_printer", array(
				"in_popup" => "1",
				"from" => date_edit::get_timestamp($arr["request"]["rs_booking_from"]),
				"to" => date_edit::get_timestamp($arr["request"]["rs_booking_to"]),
				"group" => $grp_id,
				"rooms" => join(",",$rooms->ids()),
			) + $extra);

			$tb->add_sub_menu(array(
				"parent" => "print",
				"text" => $go->name(),
				"name" => "g_".$grp_id,
//				"link" => "javascript:aw_popup_scroll('".$submenu_link."','mulcal',700,500);",
				"link" => "javascript:goto_overview_url('".$grp_id."');",
			));
			$this->_add_day_subs($tb, "g_".$grp_id , $grp_id);
		}
	}

	/**
		@attrib name=create_new_category params=name all_args=1
	**/
	function create_new_category($arr)
	{
		$o = new object();
		$o->set_parent($arr["cat"] ? $arr["cat"] : $arr["id"]);
		$o->set_name($arr["name"]);
		$o->set_class_id(CL_ROOM_CATEGORY);
		$o->save();
		die();
	}


	/**
		@attrib name=create_new_room params=name all_args=1
	**/
	function create_new_room($arr)
	{
		$obj = obj($arr["id"]);
		$o = new object();
		$o->set_parent($obj->prop("rooms_folder"));
		$o->set_name($arr["name"]);
		$o->set_class_id(CL_ROOM);
		if(is_oid($arr["cat"]))
		{
			$o->set_prop("category" , $arr["cat"]);
		}
		$o->save();
		die();
	}
	/**
		@attrib name=ajax_update_prop all_args=1
		@param id optional type=int
		@param prop optional type=string
	**/
	function ajax_update_prop($arr)
	{
		
		$t = new vcl_table();

		$property = $arr["prop"];
		$arr["prop"] = array("vcl_inst" => $t);
		$arr["obj_inst"] = obj($arr["id"]);
		$arr["request"]["cat"] = $arr["cat"];
		$arr["request"]["from"] = is_numeric($arr["from"]) ? $arr["from"] : "";
		$arr["request"]["to"] = is_numeric($arr["to"]) ? $arr["to"] : "";
		$arr["request"]["name"] = $arr["name"];
		$arr["request"]["room"] = is_numeric($arr["room"]) ? $arr["room"] : "";
		$arr["request"]["timespan"] = $arr["timespan"];
		$arr["request"]["die"] = 1;

		switch($property)
		{
			case "room_management_list":
				$this->_get_room_management_list($arr);
				break;
			case "reservation_management_list":
				$this->_get_reservation_management_list($arr);
				break;
			case "room_management_tree":
				
				$t = new treeview();
				$arr["prop"] = array("vcl_inst" => $t);
				$this->_get_room_management_tree($arr);
//				print iconv(aw_global_get("charset"), "UTF-8", $t->get_toolbar());
//				die();
				break;
			case "reservation_management_time_tree":
				$property = "stats_time_tree";
			case "room_management_capacity_from_tree":
			case "room_management_capacity_to_tree":	
			case "reservation_management_tree":
				
				$t = new treeview();
				$arr["prop"] = array("vcl_inst" => $t);
				$fun = "_get_".$property;
				$this->$fun($arr);
//				print iconv(aw_global_get("charset"), "UTF-8", $t->get_toolbar());
//				die();
				break;
		}
		//$this->_sp_result($arr);
		print iconv(aw_global_get("charset"), "UTF-8", $t->get_html());
		die();
	}


	function callback_generate_scripts($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}

		$js = "";

		if($arr["request"]["group"] == "room_management" || $arr["request"]["group"] == "reservation_management")
		{
			
			$js.= "
				function update_rooms_table()
				{
					$.please_wait_window.show({
						'target': $('[name=room_management_list]')
					});
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
							id: ".$arr["obj_inst"]->id()."
							, prop: 'room_management_list'
							, name: $('[name=room_management_name]').val()
							, cat: $('[name=category]').val()
							, from: $('[name=from]').val()
							, to: $('[name=to]').val()}
							,function(html){
							    $.please_wait_window.hide();
						x=document.getElementsByName('room_management_list');
						x[0].innerHTML = html;
					});
//
//					$.get('/automatweb/orb.aw', {class: 'spa_bookings_overview', action: 'create_new_category', id: '".$arr["obj_inst"]->id()."' , name: my_string, cat: cat}, function (html) {
//						alert(html);	
//						update_cat_tb();
//						}
//					);
				}

				function update_bron_table()
				{
					$.please_wait_window.show({
						'target': $('[name=reservation_management_list]')
					});
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
							id: ".$arr["obj_inst"]->id()."
							, prop: 'reservation_management_list'
							, name: $('[name=reservation_management_name]').val()
							, room: $('[name=room]').val()
							, timespan: $('[name=timespan]').val()}
							,function(html){
							    $.please_wait_window.hide();
						x=document.getElementsByName('reservation_management_list');
						x[0].innerHTML = html;
					});
				}

				function update_room_tree()
				{
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'reservation_management_tree'
						, room: $('[name=room]').val()}
						,function(html){
						x=document.getElementById('reservation_management_tree');
						x.innerHTML = html;
					});
				}

				function update_time_tree()
				{
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'reservation_management_time_tree'
						, timespan: $('[name=timespan]').val()}
						,function(html){
						x=document.getElementById('reservation_management_time_tree');
						x.innerHTML = html;
					});
				}

				function update_cat_tb()
				{
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'room_management_tree'
						, cat: $('[name=category]').val()}
						,function(html){
						x=document.getElementById('room_management_tree');
						x.innerHTML = html;
					});
				}
				function update_from_tb()
				{
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'room_management_capacity_from_tree'
						, from: $('[name=from]').val()}
						,function(html){
						x=document.getElementById('room_management_capacity_from_tree');
						x.innerHTML = html;
					});
				}
				function update_to_tb()
				{
					$.post('/automatweb/orb.aw?class=spa_bookings_overview&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'room_management_capacity_to_tree'
						, to: $('[name=to]').val()}
						,function(html){
						x=document.getElementById('room_management_capacity_to_tree');
						x.innerHTML = html;
					});
				}
				function add_cat()
				{
					var cat = $('[name=category]').val();
					var my_string = prompt('".t("Sisesta kategooria nimi")."');
					$.get('/automatweb/orb.aw', {class: 'spa_bookings_overview', action: 'create_new_category',
						 id: '".$arr["obj_inst"]->id()."' , name: my_string, cat: cat}, function (html) {
							update_cat_tb();
						}
					);
				}
				function add_room()
				{
					var cat = $('[name=category]').val();
					var my_string = prompt('".t("Sisesta kategooria nimi")."');
					$.get('/automatweb/orb.aw', {class: 'spa_bookings_overview', action: 'create_new_room', id: '".$arr["obj_inst"]->id()."' , name: my_string, cat: cat}, function (html) {
		//			alert(html);	
					update_rooms_table();
						}
					);
				}
				function copy_room()
				{
					var result = '';
					$('[name^=sel]').each(function(){
						if(this.checked){
							result =result +  ',' + this.value;
						}
					});

					$.get('/automatweb/orb.aw', {class: 'spa_bookings_overview', action: 'copy_rooms', id: '".$arr["obj_inst"]->id()."' , sel: result}, function (html) {
						alert(html);	
						update_rooms_table();
						}
					);
				}
			";

			return $js;
		}

		return '
			function goto_overview_url(group)
			{
				$.sup("class","spa_bookings_overview");
				$.sup("rs_name",$("#rs_name").val());
				$.sup("person",$("#rs_booker_name").val());
				$.sup("group",group);
				$.sup("in_popup","1");
				unconfirmed = 0;

				$("#rs_unconfirmed").each(function(){
					unconfirmed = this.checked;
					if(unconfirmed)
					{
						unconfirmed = 1;
					}
					else
					{
						unconfirmed = 0;
					}
				});

				$.sup("unverified",unconfirmed);


				$.sup("rs_booking_from[day]",$("input[name=\'rs_booking_from[day]\']").val());
				$.sup("rs_booking_from[month]",$("input[name=\'rs_booking_from[month]\']").val());
				$.sup("rs_booking_from[year]",$("input[name=\'rs_booking_from[year]\']").val());
				$.sup("rs_booking_to[day]",$("input[name=\'rs_booking_to[day]\']").val());
				$.sup("rs_booking_to[month]",$("input[name=\'rs_booking_to[month]\']").val());
				$.sup("rs_booking_to[year]",$("input[name=\'rs_booking_to[year]\']").val());

				aw_popup_scroll($.sup("action","room_booking_printer"),"mulcal",700,500);
			}
		';
	}

	/**
		@attrib name=print_person_chart
		@param person optional
		@param rvs optional 
	**/
	function print_person_chart($arr)
	{
		$this->tpl_init("applications/spa_bookings/spa_bookings_entry");
		$this->read_site_template("booking.tpl");
		lc_site_load("spa_bookigs_entry", &$this);

		$_from = time() + DAY_LENGTH*1000;
		$_to = 0;
		$rv2r = array();
		foreach($arr["rvs"] as $key=> $b_oid)
                {
			if (!$this->can("view", $b_oid))
			{
				unset($arr["rvs"][$key]);
				continue;
			}
                        $rvs = obj($b_oid);
			$rv2r[$b_oid] = $rvs->prop("resource");
			$_from = min($_from, $rvs->prop("start1"));
			$_to = max($_to, $rvs->prop("end"));
		}
		uasort($arr["rvs"], create_function('$a,$b', '$ao = obj($a); $bo = obj($b); return $ao->prop("start1") - $bo->prop("start1");'));
		$b = obj(reset($arr["rvs"]));
		// try to find the spa_booking that is connected to the room reservartion
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_SPA_BOOKING,
			"to" => $b->id()
		));
		if (count($conns))
		{
			$con = reset($conns);
			$b = obj($con["from"]);
			$_from = $b->prop("start");
			$_to = $b->prop("end");
		}

		if (!$_GET["person"])
		{
			$p = obj($b->prop("customer"));
		}
		else
		{
			$p = obj($_GET["person"]);
		}
		list($y, $m, $d) = explode("-", $p->prop("birthday"));

		$us = get_instance(CL_USER);
		$this->users_person = $us->get_person_for_uid($b->createdby());
		$this->vars(array(
			"bureau" => $b->prop("seller")?$b->trans_get_val_str("seller"):$this->users_person->name(),//$b->createdby(),
			"person" => $p->trans_get_val("name"),
			"package" => $b->trans_get_val_str("package"),
			"agency" => $b->trans_get_val_str("seller"),
			"from" => date("d.m.Y", $_from),
			"to" => date("d.m.Y", $_to),
			"person_comment" => $p->prop("comment"),
			"person_name" => $p->prop("name"),
			"person_birthday" => $y > 0 ? sprintf("%02d.%02d.%04d", $d, $m, $y) : "",
			"person_ext_id" => $p->prop("ext_id_alphanumeric"),
			"person_gender" => $p->prop("gender") == 1 ? t("Mees") : ($p->prop("gender") === "2" ? t("Naine") : "")
		));

		$all_items = "";
		$packet_services = "";
		$additional_services = "";

		foreach($arr["rvs"] as $b_oid)
		{
			if (!$this->can("view", $b_oid))
			{
				continue;
			}
			$rvs = obj($b_oid);
			$ro = obj($rv2r[$b_oid]);
			//$prod_obj = obj($rvs->meta("product_for_bron"));
			$prods = safe_array($rvs->meta("amount"));
			
			foreach($prods as $key => $val)
			{
				if($val)
				{
					$prod_obj = obj($key);
					break;
				}
			}
			
			if(!is_object($prod_obj))
			{
				$prod_obj = obj(reset(array_keys($prods)));
			}
			
			$this->vars(array(
				"r_from" => date("d.m.Y H:i", $rvs->prop("start1")),
				"r_to" =>  date("d.m.Y H:i", $rvs->prop("end")),
				"r_room" => $ro->trans_get_val("short_name") ? $ro->trans_get_val("short_name") : $ro->trans_get_val("name"),
				"r_prod" => $prod_obj->trans_get_val("name"),
				"start_time" => $rvs->prop("start1"),
				"end_time" => $rvs->prop("end"),
				"price" => $prod_obj->prop("price"),
				"r_comment" => $rvs->comment(),
				"r_comment_brace" => $rvs->comment() != "" ? "( ".$rvs->comment()." ) " : ""
			));
			$books .= $this->parse("BOOKING");

			$all_items .= $this->parse("ALL_ITEMS");
			if ($entry["is_extra"] == 1)
			{
				$additional_services .= $this->parse("ADDITIONAL_SERVICES");
			}
			else
			{
				$packet_services .= $this->parse("PACKET_SERVICES");
			}
		}


		$this->vars(array(
			"BOOKING" => $books,
			"ADDITIONAL_SERVICES" => $additional_services,
			"PACKET_SERVICES" => $packet_services,
			"ALL_ITEMS" => $all_items
		));
		$this->vars(array(
			"HAS_PACKET_SERVICES" => $packet_services != "" ? $this->parse("HAS_PACKET_SERVICES") : "",
			"HAS_ADDITIONAL_SERVICES" => $packet_services != "" ? $this->parse("HAS_ADDITIONAL_SERVICES") : "",
		));

		$ol = new object_list(array(
			"class_id" => CL_SPA_BOOKIGS_ENTRY,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $wb)
		{
			if ($this->can("view", $wb->prop("print_view_ctr")))
			{
				$fc = get_instance(CL_FORM_CONTROLLER);
				$fc->eval_controller($wb->prop("print_view_ctr"), $arr);
			}
		}
		die($this->parse());
	}

	/**
		@attrib name=show_cals_pop
		@param id optional
		@param rooms optional
		@param title optional
		@param post_msg_after_reservation optional type=array
			Array(
				class_id => CL_WHATEVER,
				method => "some_method",
				another_param => value,
			)
			After creating reservation object, some_mehtod on cl_whatever is called with this same array as param. One parameter is added to array before that:
			array(
				reservation => reservation_obj,
			)
		@param alter_reservation_name optional type=array
			Array(
				class_id => CL_WHATEVER,
				method => "some_method",
				another_param => value,
			)
			When drawing reservations calendar table, and a reservation is going to be drawn, this callback function is called to alter the name which is shown in the calendar. Rervation id and other userful data is added to params list for the callback function.
			array(
				reservation => reservation_obj,
				bron_name => &current_bron_name,
				name_elements => array(
					customer => bron_name_component,
					phone => bron_name_component,
					codes => bron_name_component,
				)
			)
		@param firstname optional type=string
			Firstname for reservation object.
		@param lastname optional type=string
			Lastname for reservation object.
		@param company optional type=string
			Company for reservation object.
		@param phone optional type=string
			Phone number for reservation object.
		@param people_count optional type=string
			People count for reservation object
	**/
	function show_cals_pop($arr)
	{
		$_GET["in_popup"] = 1;
		
		$html = "";
		$this->read_template("room_cals.tpl");
		$cals = "";
		$first = true;
		$roids = array();
		foreach(explode(",", $arr["rooms"]) as $room_id)
		{
			if ($this->can("view", $room_id))
			{
				$roids[] = $room_id;
			}
		}

		$r_ol = new object_list(array(
			"oid" => $roids,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.jrk"
		));
		$start = $_GET["start"];
		$end = $_GET["end"];
		if (!$start)
		{
			classload("core/date/date_calc");
			$start = get_week_start();
			$end = $start+7*DAY_LENGTH;
		}
		$room2tbl = array();
		foreach($r_ol->ids() as $room_id)
		{
			if ($first)
			{
				$ri = get_instance(CL_ROOM);
				$toolbar = get_instance("vcl/toolbar");
				$p = array(
					"vcl_inst" => &$toolbar,
				);
				$ri->_calendar_tb(array(
					"prop" => $p,
					"obj_inst" => obj($room_id)
				));

				$toolbar->add_menu_button(array(
					"name" => "print",
					"img" => "print.gif",
				));
				
				$toolbar->add_menu_item(array(
					"parent" => "print",
					"text" => t("K&otilde;ik"),
					"link" => $this->mk_my_orb("room_booking_printer", array(
						"in_popup" => "1",
						"rooms" => $r_ol->ids(),
						"from" => $start,
						"to" => $end
					))
				));
				if($this->can("view", $arr["id"]))
				{
					$oo = obj($arr["id"]);
					foreach(safe_array($oo->prop("groups")) as $grp_id)
					{
						$go = obj($grp_id);
						$toolbar->add_menu_item(array(
							"parent" => "print",
							"text" => $go->name(),
							"link" => $this->mk_my_orb("room_booking_printer", array(
								"in_popup" => "1",
								"rooms" => $r_ol->ids(),
								"from" => $_GET["start"],
								"to" => $_GET["end"],
								"group" => $grp_id
							))
						));
					}
				}
				
				// generate the post_msg_params
				foreach($arr["post_msg_after_reservation"] as $k => $v)
				{
					$reforb["post_msg_after_reservation[".$k."]"] = $v;
				}
				$reforb["id"] = $room_id;
				$reforb["set_view_dates"] = "0";
				$reforb["post_ru"] = get_ru(); 
				$reforb["firstname"] = $arr["firstname"];
				$reforb["lastname"] = $arr["lastname"];
				$reforb["company"] = $arr["company"];
				$reforb["phone"] = $arr["phone"];
				$reforb["people_count"] = $arr["people_count"];

				$this->vars(array(
					"toolbar" => $toolbar->get_toolbar(),
					"picker" => $ri->_get_calendar_select(array(
						"prop" => array(),
						"request" => array(
							"start" => $_GET["start"] ? $_GET["start"] : time()
						),
						"obj_inst" => obj($room_id)
					)),
					"reforb" => $this->mk_reforb("do_add_reservation", $reforb),
				));
			}

			$first = false;
			// show room cal
			$ro = obj($room_id);
			$ri = $ro->instance();
			$t = new vcl_table;
			$prop = array(
				"vcl_inst" => &$t
			);
			$ri->room_count = $r_ol->count();//et mitu ruumi k6rvuti inimlikult j22ksid n2ha
			$ri->_get_calendar_tbl(array(
				"room" => $room_id,
				"prop" => $prop,
				"request" => array(
					"alter_reservation_name" => $arr["alter_reservation_name"],
				),
			));
			$room2tbl[] = array(
				"t" => $t,
				"room" => $room_id,
				"timespan" => $ri->step_length,
				"popup_menu" => $ri->popup_menu_str,
				"start" => $ri->start
			);
		}

		enter_function("spa_bookings_owverview::join_cals");
		foreach($room2tbl as $idx => $dat)
		{
			$t =& $room2tbl[$idx]["t"];
			$ts = $dat["timespan"];
			$ts_start = $dat["start"];
			$room_id = $room2tbl[$idx]["room"];

			// try to join the table together with the old one
			// if the timespans are the same
			$ro = obj($room_id);

			if ($ts == $prev_ts && $ts_start == $prev_ts_start)
			{
				// add the contents of this table to the previous one
				$prev_td = $prev_t->get_data();
				$cur_td = $t->get_data();
				foreach($prev_td as $prev_idx => $prev_dr)
				{
					$cur_dr = $cur_td[$prev_idx];
					foreach($cur_dr as $cur_k => $cur_v)
					{
						$cur_k = "k".$idx.$cur_k;
						$prev_dr[$cur_k] = $cur_v;
					}
					$prev_t->set_data($prev_idx, $prev_dr);
				}

				$new_fields = $t->get_defined_fields();
				$prev_t->define_field(array(
					"name" => "room".$room_id,
					"caption" => html::href(array(
                                                "url" => $this->mk_my_orb("change", array(
                                                        "id" => $ro->id(),
                                                        "group" => "calendar",
                                                        "return_url" => get_ru()
                                                ), "room"),
                                                "caption" => $ro->name()
                                        ))
				));
				foreach($new_fields as $field_data)
				{
					$field_data["name"] = "k".$idx.$field_data["name"];
					$field_data["chgbgcolor"] = "k".$idx.$field_data["chgbgcolor"];
					$field_data["parent"] = "room".$room_id;
					$field_data["id"] = "k".$idx.$field_data["id"];
					$field_data["onclick"] = "k".$idx.$field_data["onclick"];
					$field_data["rowspan"] = "k".$idx.$field_data["rowspan"];
					$prev_t->define_field($field_data);

				}
				$room2tbl[$prev_main_idx]["popup_menu"] .= $dat["popup_menu"];

				unset($room2tbl[$idx]);
			}
			else
			{
				$new_fields = $t->get_defined_fields();
				$t->define_field(array(
					"name" => "room".$room_id,
					"caption" => html::href(array(
						"url" => $this->mk_my_orb("change", array(
							"id" => $ro->id(),
							"group" => "calendar",
							"return_url" => get_ru()
						), "room"),
						"caption" => $ro->name()
					))
				));
				foreach($new_fields as $field_data)
				{
					$field_data["parent"] = "room".$room_id;
					$t->remove_field($field_data["name"]);
					$t->define_field($field_data);
				}
				$prev_t =& $t;
				$prev_ts = $ts;
				$prev_ts_start = $ts_start;
				$prev_main_idx = $idx;
			}
		}

		exit_function("spa_bookings_owverview::join_cals");
		enter_function("spa_bookings_owverview::draw_cals");

		foreach($room2tbl as $idx => $dat)
		{
			$t =& $dat["t"];
			$ts = $dat["timespan"];
			$room_id = $dat["room"];
			$ro = obj($room_id);
			$t->set_caption(null);
			$this->vars(array(
				"cal" => $t->get_html().$dat["popup_menu"]
			));
			$cals .= $this->parse("CAL");
			$prev_t =& $t;
			$prev_ts = $ts;
		}
		exit_function("spa_bookings_owverview::draw_cals");
		if(substr_count($_GET["title"],'__dates__') > 0)
		{
			$d = array();
			if($start) $d[] = date("d-m-Y" , $start);
			if($end) $d[] = date("d-m-Y" , ($end-61));
			$dates = join (t("kuni") , $d);
			$_GET["title"] = str_replace('__dates__', $dates,$_GET["title"]);
		}
 		aw_global_set("title_action",$_GET["title"]);
		$this->vars(array(
			"CAL" => $cals,
			//"reforb" => $this->mk_reforb("do_add_reservation", array("id" => $arr["id"], "post_ru" => get_ru()), "room")
		));
		return $this->parse();
	}

	/**
		@attrib name=do_add_reservation all_args=1
	**/
	function do_add_reservation($arr)
	{
		if ($arr["set_view_dates"])
		{
			$start = date_edit::get_timestamp($arr["set_d_from"]);
                        if ($arr["set_view_dates"] == 1)
                        {
                                $end = date_edit::get_timestamp($arr["set_d_to"])+DAY_LENGTH;
                        }
                        else
                        if ($arr["set_view_dates"] == 2)
                        {
                                 $end = $start + DAY_LENGTH;
                        }
                        else
                        if ($arr["set_view_dates"] == 3)
                        {
                                $end = $start + DAY_LENGTH*7;
                        }
                        else
                        if ($arr["set_view_dates"] == 4)
                        {
                                $end = $start + DAY_LENGTH*31;
                        }
			return aw_url_change_var("start",$start,aw_url_change_var("end",$end,aw_url_change_var("no_det_info", $arr["no_det_info"], $arr["post_ru"])));
		}
		$ri = get_instance(CL_ROOM);
		return $ri->do_add_reservation($arr);
	}

	/**
		@attrib name=room_booking_printer
		@param rooms optional
		@param rs_name optional
		@param from optional
		@param to optional
		@param group optional
		@param unverified optional
		@param person optional
	**/
	function room_booking_printer($arr)
	{
		$arr = $_GET;
		foreach($arr as $key => $val)
		{
			$arr[str_replace("\\" , "" , $key)] = $val;
		}

		foreach($arr["rs_booking_from"] as $key => $val)
		{
			$arr["rs_booking_from"][str_replace("\\" , "" , $key)] = $val;
		}
		foreach($arr["rs_booking_to"] as $key => $val)
		{
			$arr["rs_booking_to"][str_replace("\\" , "" , $key)] = $val;
		}
//arr($arr); die();
		$this->read_any_template("booking_printer.tpl");
		if(!($arr["to"] > 0 && $arr["from"] > 0))
		{
			$arr["from"] = date_edit::get_timestamp($arr["rs_booking_from"]);
			$arr["to"] = date_edit::get_timestamp($arr["rs_booking_to"]);
		}

		// get all the bookings for the given rooms in the given timespan and group if set
		if($arr["to"])
		{
			$arr["to"] = $arr["to"] + DAY_LENGTH - 1;
		}


		$days = ($arr["to"] - $arr["from"]) / (DAY_LENGTH);

		if(!$arr["rooms"] || $arr["ra_name"])
		{
			$rooms = new object_list(array(
				"class_id" => CL_ROOM,
				"lang_id" => array(),
				"name" => "%".$arr["rs_name"]."%",
			));
			$arr["rooms"] = $rooms->ids();
		}

		if (!is_array($arr["rooms"]))
		{
			$arr["rooms"] = explode(",", $arr["rooms"]);
		}

		$r_inst = get_instance(CL_RESERVATION);
		$rs = "";
		foreach(safe_array($arr["rooms"]) as $room_id)
		{
			if (!$this->can("view", $room_id))
			{
				continue;
			}
			$ro = obj($room_id);
			$day_str = "";
			for($day = 0; $day < $days; $day++)
			{
				$from = $arr["from"] + ($day * DAY_LENGTH);
				$to = $from+(DAY_LENGTH);
				$ft = array(
					"class_id" => CL_RESERVATION,
					"lang_id" => array(),
					"site_id" => array(),
					"resource" => $room_id,
					"verified" => 1,
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"start1" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to),
							"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from),
						)
					))
			//		new obj_predicate_compare(OBJ_COMP_IN_TIMESPAN, array("start1", "end"), array($from, $to)),
				);			
				if (is_oid($arr["group"]))
				{
					// get group members
					$g = get_instance(CL_GROUP);
					$users = $g->get_group_members(obj($arr["group"]));
					$ft["createdby"] = array();
					foreach($users as $user)
					{
						$ft["createdby"][] = $user->prop("uid");
					}
				}
				if ($arr["person"])
				{
					$ft["customer"] = "%".$arr["person"]."%";
				}
				if ($arr["unverified"])
				{
					$ft["verified"] = new obj_predicate_not(1);
				}
				$books = "";
				$reservation_ol = new object_list($ft);
				$reservation_ol->sort_by(array("prop" => "start1"));
				foreach($reservation_ol->arr() as $r)
				{
					$this->vars(array(
						"time_from" => date("H:i", $r->prop("start1")),
						"time_to" => date("H:i", $r->prop("end")),
						"customer" => $r->prop("customer.name"),
						"products" => $r_inst->get_products_text($r, " "),
						"products_wo_amount" => $r_inst->get_products_wo_amount_text($r, " "),
						"cust_arrived" => ($r->prop("client_arrived") == 0 ? "" : ($r->prop("client_arrived") == 1 ? t("Klient saabus") : t("Klient ei saabunud"))),
						"comment" => $r->comment(),
						"content" => $r->prop("content"),
					));
					$books .= $this->parse("BOOKING");
				}
				
				if ($books != "")
				{
					$this->vars(array(
						"BOOKING" => $books,
						"date" => date("d.m.Y", $from)
					));
					$day_str .= $this->parse("DAY");
				}
			}

			$this->vars(array(
				"DAY" => $day_str,
				"room_name" => $ro->trans_get_val("name"),
				"date_from" => date("d.m.Y", $arr["from"]),
				"date_to" => date("d.m.Y", $arr["to"]),
			));
			$rs .= $this->parse("ROOM");
		}

		$this->vars(array(
			"ROOM" => $rs
		));

		return $this->parse();
	}

	function _add_day_subs(&$tb, $pt, $grp)
	{
/*		$link = $this->mk_my_orb("room_booking_printer", array(
			"from" => $this->start,
			"to" => $this->to,
			"group" => $grp
		));
		$tb->add_menu_item(array(
			"parent" => $pt,
			"text" => t("Valitud"),
			"onClick" => "vals='&rooms=';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('sel') != -1 && f[i].checked) {vals += ','+f[i].value;}};aw_popup_scroll('$link'+vals,'mulcal',700,500);return false;",
		));
*/
		$link = $this->mk_my_orb("room_booking_printer", array(
			"in_popup" => "1",
			"from" => get_day_start()-DAY_LENGTH,
			"to" => get_day_start(),
			"group" => $grp
		));
		$tb->add_menu_item(array(
			"parent" => $pt,
			"text" => t("Eile"),
			"onClick" => "vals='&rooms=';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('sel') != -1 && f[i].checked) {vals += ','+f[i].value;}};aw_popup_scroll('$link'+vals,'mulcal',700,500);return false;",
		));

		$link = $this->mk_my_orb("room_booking_printer", array(
			"in_popup" => "1",
			"from" => get_day_start(),
			"to" => get_day_start()+DAY_LENGTH,
			"group" => $grp
		));
		$tb->add_menu_item(array(
			"parent" => $pt,
			"text" => t("T&auml;na"),
			"onClick" => "vals='&rooms=';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('sel') != -1 && f[i].checked) {vals += ','+f[i].value;}};aw_popup_scroll('$link'+vals,'mulcal',700,500);return false;",
		));

		$link = $this->mk_my_orb("room_booking_printer", array(
			"in_popup" => "1",
			"from" => get_day_start()+DAY_LENGTH,
			"to" => get_day_start()+DAY_LENGTH+DAY_LENGTH,
			"group" => $grp
		));
		$tb->add_menu_item(array(
			"parent" => $pt,
			"text" => t("Homme"),
			"onClick" => "vals='&rooms=';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('sel') != -1 && f[i].checked) {vals += ','+f[i].value;}};aw_popup_scroll('$link'+vals,'mulcal',700,500);return false;",
		));
	}

	// stats
	function _get_stats_rs_name($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_stats_rs_package($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PACKET,
			"lang_id" => array(),
			"site_id" => array()
		));
		$pk_list = array();
		foreach($ol->arr() as $o)
		{
			$pk_list[$o->id()] = $o->trans_get_val("name");
		}

		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
		$arr["prop"]["options"] = array("" => t("--vali--")) +  $pk_list;
	}

	function _get_stats_rs_booker_name($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_stats_rs_booking_from($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")-1, date("Y"));
		}
		$arr["prop"]["value"] = $v;
	}

	function _get_stats_rs_booking_to($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")+7, date("Y"));
		}
		$arr["prop"]["value"] = $v;
	}


	function _get_stats_r_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$ol = new object_list();
		$co = get_instance(CL_CRM_COMPANY);
		$room_instance = get_instance(CL_ROOM);
		$pi = get_instance(CL_USER);
		$from = date_edit::get_timestamp($arr["request"]["stats_rs_booking_from"]);
		$to = date_edit::get_timestamp($arr["request"]["stats_rs_booking_to"]);
		$srch = !empty($arr["request"]["stats_rs_name"]) || !empty($arr["request"]["stats_rs_booker_name"]) || $from > 1 || $to > 1 || !empty($arr["request"]["stats_rs_package"]);	
		/*if (!$srch)
		{
			return;
		}
*/


		$f = array(
			"class_id" => CL_SPA_BOOKING,
			"lang_id" => array(),
			"site_id" => array()
		);

		if (!empty($arr["request"]["stats_rs_name"]))
		{
			$f["CL_SPA_BOOKING.person.name"] = "%".$arr["request"]["stats_rs_name"]."%";
		}

		if (!empty($arr["request"]["stats_rs_booker_name"]))
		{
			// list all cos with names like that and get all users from the employees of those
			$ppl = array();
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$arr["request"]["stats_rs_booker_name"]."%"
			));

			foreach($ol->arr() as $o)
			{
				foreach($co->get_employee_picker($o) as $pid => $pnm)
				{
					$ppl[$pid] = $pid;
				}
			}
			// now get users for persons
			$users = new object_list(array(
				"class_id" => CL_USER,
				"lang_id" => array(),	
				"site_id" => array(),
				"CL_USER.RELTYPE_PERSON" => $ppl
			));
			$uds = array();
			foreach($users->arr() as $usr)
			{
				$uds[] = $usr->prop("uid");
			}
			$f["createdby"] = map("%%%s%%", $uds);
		}


		if ($from > 1)
		{
			$f["start"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		elseif(!$arr["request"]["stats_rs_booking_from"])//default tuleks siis kui pole otsitud veel
		{
			$f["start"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time());
		}

		if ($to > 1)
		{
			$f["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
		}

		if (!empty($arr["request"]["stats_rs_package"]))
		{
			$f["package"] = $arr["request"]["stats_rs_package"];
		}
		$ol = new object_list($f);

		$pks = array();
		$d = array();
		enter_function("sbo::_get_stats_r_list");

		foreach($ol->arr() as $o)
		{
			if (!$this->can("view", $o->prop("package")) || $o->createdby() == "")
			{
				continue;
			}
			$pks[$o->prop("package")] = 1;
			$d[$o->createdby()][$o->prop("package")]["count"] ++;

			// add all prices from attached brons
			foreach($o->connections_from(array("type" => "RELTYPE_ROOM_BRON")) as $c)
			{
				$b = $c->to();
				$sum = $b->get_sum();
//				$sum = $room_instance->cal_room_price(array(
//					"room" => $b->prop("resource"),
//					"start" => $b->prop("start1"),
//					"end" => $b->prop("end"),
//					"people" => $b->prop("people_count"),
//					"products" => $b->meta("amount"),
//					"bron" => $b,
//				));
				foreach($sum as $cur => $amt)
				{
					$d[$o->createdby()][$o->prop("package")]["sum"][$cur] += $amt;
				}
			}
		}
		exit_function("sbo::_get_stats_r_list");
		$t->define_field(array(
			"name" => "tb",
			"align" => "center",
			"caption" => t("Reisib&uuml;roo")
		));
		foreach($pks as $pkid => $one)
		{
			$pko = obj($pkid);
			$t->define_field(array(
				"name" => "p".$pkid,
				"align" => "center",
				"caption" => $pko->name()
			));
		}


		foreach($d as $uid => $d1)
		{
			// get person for user and from person get co 
			$p = $pi->get_person_for_uid($uid);
//			$c = new connection();
//			$conns = $c->find(array(
//				"from.class_id" => CL_CRM_COMPANY,
//				"type" => "RELTYPE_WORKERS",
//				"to" => $p->id()
//			));
//			$c = reset($conns);
			$co = $p->company();

			$r = array(
				"tb" => $co->name()
			);
			foreach($d1 as $pk => $d2)
			{
				$sum = array();
				foreach($d2["sum"] as $cur => $amt)
				{
					$curo = obj($cur);
					$sum[] = number_format($amt, 2)." ".$curo->name();
				}
				$sum[] = sprintf(t("%s tk"), $d2["count"]);
				$r["p".$pk] = join(" / ", $sum);
			}
			$t->define_data($r);
		}
		$t->set_sortable(false);
	}

	function _get_r_rs_name($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_r_rs_booker_name($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_rs_unconfirmed($arr)
	{
		$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
	}

	function _get_r_rs_booking_from($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")-1, date("Y"));
		}
		$arr["prop"]["value"] = $v;
	}

	function _get_r_rs_booking_to($arr)
	{
		$v = date_edit::get_timestamp($arr["request"][$arr["prop"]["name"]]);
		if ($v < 1)
		{
			$v = mktime(0,0,0, date("m"), date("d")+7, date("Y"));
		}
		$arr["prop"]["value"] = $v;
	}

	function _init_r_r_list(&$t)
	{
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kellaajad"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "hrs",
			"caption" => t("Tunde"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_r_r_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		enter_function("spa_bookings_owverview::get_rb_objs");
		$ol = new object_list();

		$from = date_edit::get_timestamp($arr["request"]["r_rs_booking_from"]);
		$to = date_edit::get_timestamp($arr["request"]["r_rs_booking_to"]);
		$srch = !empty($arr["request"]["r_rs_name"]) || !empty($arr["request"]["r_rs_booker_name"]) || $from > 1 || $to > 1 ;	
		$room2booking = array();
		if ($srch)
		{
			$room2booking = array();
			$f = array(
				"class_id" => CL_ROOM,
				"lang_id" => array(),
				"site_id" => array()
			);
			if (!empty($arr["request"]["r_rs_name"]))
			{
				$f["name"] = "%".$arr["request"]["r_rs_name"]."%";
			}

			if (!empty($arr["request"]["r_rs_booker_name"]) || $from > 1 || $to > 1)
			{
				// get all bookings for that person
				$bf = array(
					"class_id" => CL_RESERVATION,
					"verified" => 1,
					"lang_id" => array(),
					"site_id" => array(),
				);
				if (!empty($arr["request"]["r_rs_booker_name"]))
				{
					$bf["CL_RESERVATION.RELTYPE_CUSTOMER.name"] = "%".$arr["request"]["r_rs_booker_name"]."%";
				}
				if ($from > 1)
				{
					$bf["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
				}
				elseif(!$arr["request"]["r_rs_booking_from"])
				{
					$bf["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time());
				}

				if ($to > 1)
				{
					$bf["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
				}

				$bookings = new object_list($bf);
				$rooms = array();
				foreach($bookings->arr() as $booking)
				{
					$rooms[$booking->prop("resource")] = $booking->prop("resource");
//					if (!empty($arr["request"]["r_rs_booker_name"]))
//					{
						$room2booking[$booking->prop("resource")][] = $booking;
//					}
				}
				if (count($rooms))
				{
					$f["oid"] = $rooms;
				}
				else
				{
					$f["oid"] = -1;
				}
			}
			$rf = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_RF");
			$flds = new object_tree(array("parent" => $rf->id(), "class_id" => CL_MENU, "lang_id" => array(), "site_id" => array()));
			$f["parent"] = $this->make_keys($flds->ids());
			$f["parent"][$rf->id()] = $rf->id();
			
			$f["sort_by"] = "objects.jrk";
			$ol = new object_list($f);
		}
		else
		{
			return;
		}

		$persons_filter = array();
		if ($arr["request"]["do_print"])
		{
			foreach(safe_array($arr["request"]["persons"]) as $p_id)
			{
				$po = obj($p_id);
				$persons_filter[] = $po->name();
			}
		}
		exit_function("spa_bookings_owverview::get_rb_objs");
		enter_function("spa_bookings_owverview::define_spa_stats_data");
		$ri = get_instance(CL_RESERVATION);
		$price_sum = array();
		if (count($room2booking))
		{
			$this->_init_r_r_list($t, false);
			// group table by ppl and do several rows for one room if necessary
			$ppl = array();
			$ppl_all = array();
			foreach($room2booking as $room_id => $books)
			{
				foreach($books as $booking)
				{	
					foreach($booking->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$_po = $c->to();
						$ppl[$_po->brother_of()][] = $booking;
					}
				}
			}

			$tmp = array();
			foreach($ppl as $cust_id => $bookings)
			{
				$cust_o = obj($cust_id);
				if (isset($tmp[$cust_o->name()]))
				{
					foreach($bookings as $booking)
					{
						$tmp[$cust_o->name()]["bookings"][] = $booking;
					}
				}
				else
				{
					$tmp[$cust_o->name()] = array(
						"id" => $cust_id,
						"bookings" => $bookings
					);
				}
			}


			foreach($tmp as $data) //$cust_id => $bookings)
			{
				$cust_id = $data["id"];
				$bookings = $data["bookings"];
				$rvs_ids = array();
				foreach($bookings as $booking)
				{
					$rvs_ids[] = $booking->id();
				}
				$p_obj = obj($cust_id);
				if ($arr["request"]["r_rs_booker_name"] != "" && strpos(strtolower($p_obj->name()), strtolower($arr["request"]["r_rs_booker_name"])) === false)
				{
					continue;
				}

				if (count($persons_filter))
				{
					$match = false;
					foreach($persons_filter as $filt_p_name)
					{
						if ($filt_p_name == $p_obj->name())
						{
							$match = true;
						}
					}
					if (!$match)
					{
						continue;
					}
				}
				$ppl_links = array();
				$p_str = "";

				if ($arr["request"]["do_print"] != 1)
				{
					$p_str = html::checkbox(array(
						"name" => "persons[]",
						"value" => $p_obj->id()
					));
				}

				$p_str .= html::obj_change_url($p_obj);

				foreach($bookings as $booking)
				{
//					$pr = $ri->get_reservation_price($booking);
					$pr = $booking->get_sum();
					$price_str = array();
					foreach($pr as $cur_id => $cur_val)
					{
						$co = obj($cur_id);
						$price_str[] = number_format($cur_val, 2)." ".$co->name();
						$price_sum[$cur_id] += $cur_val;
					}
					$t->define_data(array(
						"room" => html::get_change_url($booking->prop("resource"), array("return_url" => get_ru(), "group" => "calendar"), $booking->prop("resource.name")),
						"oid" => $booking->prop("resource"),
						"date" => $booking->prop("start1"),
						"time" => sprintf("%s - %s", date("H:i", $booking->prop("start1")), date("H:i", $booking->prop("end"))),
						"hrs" => number_format(($booking->prop("end") - $booking->prop("start1")) / 3600, 2),
						"price" => join(" / ", $price_str),
						"person" => $p_str
					));
					$hrs_sum += ($booking->prop("end") - $booking->prop("start1")) / 3600;
				}
			}
			$t->sort_by(array(
				"rgroupby" => array("person" => "person")
			));
		}
		$t->set_sortable(false);

		$price_str = array();
		foreach($price_sum as $cur_id => $cur_val)
		{
			$co = obj($cur_id);
			$price_str[] = number_format($cur_val, 2)." ".$co->name();
		}

		$t->define_data(array(
			"room" => t("<b>Summa:</b>"),
			"oid" => "",
			"date" => "",
			"time" => "",
			"hrs" => number_format($hrs_sum, 2),
			"price" => join(" / ", $price_str),
			"person" => ""
		));
		exit_function("spa_bookings_owverview::define_spa_stats_data");

		if ($this->do_print_results == 1)
		{
			$i = new aw_template();
			$i->init("automatweb");
			$i->read_template("index.tpl");
			$i->vars(array(
				"content" => $t->draw()
			));
			die($i->parse()."<script language=javascript>window.print();</script>");
		}
		if ($this->do_print_results == 2)
		{
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate");
			$file = "stats". date("ymdhis") . ".csv";
			header("Content-type: text/csv; charset=".aw_global_get('charset'));
			header("Content-disposition: inline; filename=".$file.";");
			die(html_entity_decode($t->get_csv_file()));
		}
	}

	function _get_rr_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "print",
			"img" => "print.gif",
			"link" => "#",
			"onClick" => "ad='';f=document.changeform;for(i = 0; i < f.elements.length; i++) {el = f.elements[i];if (el.name.indexOf('persons') == 0 && el.checked) { ad += '&persons[]='+el.value; } }; aw_popup_scroll('".aw_url_change_var("do_print", 1)."'+ad, 'print', 500,500);"

		));
		$tb->add_button(array(
			"name" => "csv",
//			"img" => "print.gif",
			"tooltip" => "csv",
			"link" => "#",
			"onClick" => "ad='';f=document.changeform;for(i = 0; i < f.elements.length; i++) {el = f.elements[i];if (el.name.indexOf('persons') == 0 && el.checked) { ad += '&persons[]='+el.value; } }; aw_popup_scroll('".aw_url_change_var("do_print", 2)."'+ad, 'print', 500,500);"

		));
	}

	/**
		@attrib name=copy_rooms params=name all_args=1
	**/
	function copy_rooms($arr)
	{
		$selection = explode("," , $arr["sel"]);
		
		foreach($selection as $sel)
		{
			if(is_oid($sel))
			{
				$o = obj($sel);
				$o->create_copy();
			}
		}
		die();
	}

	private function get_range($val)
	{
		switch($val)
		{
			case "period_last_week":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")-1, 8, date("Y")),
				);
			break;
			case "period_week":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 1, date("Y")),
					"to" => mktime(0,0,0, date("m"), 8, date("Y")),
				);
			break;
			case "period_last_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-2, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")-1, 1, date("Y")),
				);
			break;
			case "period_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m"), 1, date("Y")),
				);
			break;
			case "period_current":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 1, date("Y")),
					"to" => mktime(0,0,0, date("m")+1, 1, date("Y")),
				);
			break;
			case "period_next":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")+1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")+2, 1, date("Y")),
				);
			break;
			case "period_year":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")),
					"to" => mktime(0,0,0, 1, 1, date("Y")+1),
				);
			break;
			case "period_lastyear":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")-1),
					"to" => mktime(0,0,0,1 , 1, date("Y")),
				);
			break;
			default :return null;
		}
		return $filt["bill_date_range"];
	}

	function _get_stats_time_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_week");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
			"url" => aw_url_change_var($var, "all_time"),
		));

		$branches = array(
			"last_week" => t("Eelmine n&auml;dal"),
			"period_week" => t("K&auml;esolev n&auml;dal"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
			"period_next" => t("J&auml;rgmine kuu"),
			"period_lastyear" => t("Eelmine aasta"),
			"period_year" => t("K&auml;esolev aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"url" => "javascript:$('[name=timespan]').val('".$id."'); update_bron_table();"//aw_url_change_var("cat", $id),
			));
		}
	}

}
?>

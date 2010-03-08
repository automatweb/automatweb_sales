<?php
/*
@classinfo syslog_type=ST_ROOM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_room index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default field=meta
@default method=serialize

# TAB GENERAL

@groupinfo general caption="&Uuml;ldine"
@default group=general

	@layout general_split type=hbox width=50%:50%

	@layout general_up type=vbox closeable=1 area_caption=&Uuml;ldinfo parent=general_split

		@property name type=textbox field=name method=none parent=general_up
		@caption Nimi

		@property short_name type=textbox parent=general_up
		@caption Nime l&uuml;hend

		@property location type=relpicker reltype=RELTYPE_LOCATION parent=general_up
		@caption Asukoht

		@property owner type=relpicker reltype=RELTYPE_OWNER parent=general_up
		@caption Omanik

		@property warehouse type=relpicker reltype=RELTYPE_SHOP_WAREHOUSE parent=general_up
		@caption Ladu
		
		@property resources_fld type=relpicker reltype=RELTYPE_INVENTORY_FOLDER parent=general_up
		@caption Ressursside kataloog

		@property area type=relpicker reltype=RELTYPE_AREA parent=general_up
		@caption Valdkond

		@property professions type=relpicker reltype=RELTYPE_PROFESSION multiple=1 parent=general_up size=4  delete_rels_popup_button=1
		@caption Ametinimetused

		@property seller_professions type=relpicker reltype=RELTYPE_PROFESSION multiple=1 parent=general_up  delete_rels_popup_button=1 size=4
		@caption M&uuml;&uuml;giesindajate ametinimetused
	
		@property inherit_prods_from type=relpicker reltype=RELTYPE_ROOM parent=general_up
		@caption P&auml;ri tooted ruumist
	
		@property inherit_oh_from type=relpicker reltype=RELTYPE_ROOM parent=general_up
		@caption P&auml;ri avamisajad ruumist

		property reservation_template type=select parent=general_up
		caption Broneeringu template

	@layout general_down type=vbox closeable=1 area_caption=Mahutavus&#44;&nbsp;kasutustingimused parent=general_split
		
		@property conditions type=relpicker reltype=RELTYPE_CONDITIONS parent=general_down
		@caption Kasutustingimused
		
		@property square_meters type=textbox size=5 parent=general_down
		@caption Suurus(ruutmeetrites)

		@property normal_capacity type=textbox size=5 parent=general_down table=aw_room method=none field=normal_capacity
		@caption Normaalne mahutavus

		@property max_capacity type=textbox size=5 parent=general_down table=aw_room method=none field=max_capacity
		@caption Maksimaalne mahutavus

		@layout buffer_before_l type=hbox width=30%:70% parent=general_down

		@property buffer_before type=textbox size=5 parent=buffer_before_l
		@caption Puhveraeg enne
		
		@property buffer_before_unit type=select no_caption=1 parent=general_down
		
		@layout buffer_after_l type=hbox  width=30%:70% parent=general_down
		
		@property buffer_after type=textbox size=5 parent=buffer_after_l
		@caption Puhveraeg p&auml;rast
		
		@property buffer_after_unit type=select no_caption=1 parent=general_down
		
		@property use_product_times type=checkbox parent=general_down no_caption=1
		@caption Kasuta toodetele m&auml;&auml;ratud aegu
		
		@property allow_multiple type=checkbox parent=general_down no_caption=1
		@caption Luba mitu broneeringut &uuml;hele ajale
		
		@property group_product_menu type=checkbox parent=general_down no_caption=1
		@caption Grupeeri tooted kaustadesse		

		@property settings type=relpicker parent=general_down multiple=1 reltype=RELTYPE_SETTINGS
		@caption Seaded

		@property category type=relpicker reltype=RELTYPE_CATEGORY parent=general_down table=aw_room  method=none field=category
		@caption Ruumi kategooria
		
		@property floor type=textbox parent=general_down table=aw_room size=3 method=none field=floor
		@caption Korrus

		@property corps type=textbox parent=general_down table=aw_room size=3 method=none field=corps
		@caption Korpus

		@property nr type=textbox parent=general_down table=aw_room size=3 method=none field=nr
		@caption Number

		layout concurrent type=hbox closeable=1 area_caption=Samaaegsed&nbsp;broneeringud: parent=general_down
		
			property concurrent_brons type=textbox size=5 parent=concurrent no_caption=1
			caption Samaaegsed broneeringuid
		
			property concurrent_type type=chooser size=5 parent=concurrent no_caption=1
			caption inimeste arv / broneeringute arv
		
		
valdkonnanimi (link, mis avab popupi, kuhu saab lisada vastava valdkonnaga seonduva t&auml;iendava info selle valdkonna objektityybi kaudu, nt konverentsid).
- puhveraeg enne (mitu tundi enne reserveeringu algust lisaks bronnitakse ruumide ettevalmistamiseks)
- puhveraeg p2rast (mitu tundi peale reserveeringu l6ppu broneeritakse ruumide korrastamiseks

# TAB CALENDAR

@groupinfo calendar caption="Kalender" submit=no
@default group=calendar
	@property calendar_tb type=toolbar no_caption=1 submit=no
	@property calendar type=calendar no_caption=1 viewtype=relative store=no
	
	@property calendar_select type=text no_caption=1
	@property calendar_tbl type=table no_caption=1

#TAB RESOURCES
@groupinfo resources caption="Ressursid"
@default group=resources

	@property resources_tb type=toolbar no_caption=1
	@property resources_tbl type=table no_caption=1

# TAB IMAGES

@groupinfo images caption="Pildid"
@default group=images
	@property images_tb type=toolbar no_caption=1
	@property images_tbl type=table no_caption=1
	@property images_search type=hidden no_caption=1 store=no

# TAB PRODUCTS
@groupinfo products caption="Tooted"
@default group=products
	@property products_tb type=toolbar no_caption=1 store=no	

	@layout products_l type=hbox width=30%:70%
		
		@layout products_left type=vbox parent=products_l
		
		@layout products_tree type=vbox parent=products_left closeable=1 area_caption=Toodete&nbsp;puu
			@property products_tr type=treeview no_caption=1 store=no parent=products_tree
	
		@layout products_find_params type=vbox parent=products_left closeable=1 area_caption=Toodete&nbsp;otsing
			@property products_find_product_name type=textbox store=no parent=products_find_params captionside=top size=10
			@caption Toote nimetus
			@property do_find_products type=submit store=no parent=products_find_params no_caption=1
			@caption Otsi
	@property products_tbl type=table no_caption=1 store=no parent=products_l

# TAB PRICES

@groupinfo prices caption="Hinnad"
@default group=prices
	
	@groupinfo prices_general caption="&Uuml;ldine" parent=prices
	@default group=prices_general



		@layout top_split type=hbox width=50%:50%

			@layout currency_l type=vbox area_caption=Seaded closeable=1 parent=top_split

				@property currency type=relpicker multiple=1 reltype=RELTYPE_CURRENCY parent=currency_l
				@caption Valuuta

				@property price type=chooser multiple=1 ch_value=1 parent=currency_l
				@caption Hind

				@property prod_discount_loc type=chooser parent=currency_l
				@caption Toodete soodushind v&otilde;etakse: 


				@property prod_web_discount type=textbox size=2 parent=currency_l
				@caption Toodete fikseeritud allahindlus


			@layout add_face type=vbox area_caption=Lisanduv&nbsp;hind&nbsp;inimestele&nbsp;&uuml;le&nbsp;normaalmahutavuse closeable=1 parent=top_split

				@property add_price_per_face type=text no_caption=1  parent=add_face
				@caption Lisanduv hind inimestele &uuml;le normaalmahutavuse


		@layout middle_split type=hbox width=50%:50%

			@layout middle type=vbox area_caption=Ajaseaded closeable=1 parent=middle_split

				@property time_unit type=chooser parent=middle
				@caption Aja&uuml;hik

				@layout time_step type=hbox width=5%:5%:20%:60% parent=middle
				@caption Aja samm

					@property time_from type=textbox size=5 parent=time_step
					@caption Aja samm: alates

					@property time_to type=textbox size=5 parent=time_step
					@caption kuni

					@property time_step type=textbox size=5 parent=time_step
					@caption ,sammuga
			

				@layout selectbox_time_step type=hbox width=5%:5%:20%:60% parent=middle
				@caption Valiku aja samm

					@property selectbox_time_from type=textbox size=5 parent=selectbox_time_step
					@caption Valiku aja samm: alates

					@property selectbox_time_to type=textbox size=5 parent=selectbox_time_step
					@caption kuni

					@property selectbox_time_step type=textbox size=5 parent=selectbox_time_step
					@caption , sammuga


			@layout min_prices type=vbox closeable=1 area_caption=Ruumibroneeringu&nbsp;miinimum-&nbsp;ja&nbsp;maksimumhind
		
				@property minmax_prices type=table parent=min_prices no_caption=1
				@caption Miinimum-Maksimum hinnad

				@property min_prices_props type=callback callback=gen_min_prices_props parent=min_prices

				@property web_min_prod_price type=callback callback=cb_gen_web_min_prices 

	
	@groupinfo prices_price caption="Hinnad" parent=prices
	@default group=prices_price,prices_bargain_price
		@property prices_search type=hidden no_caption=1 store=no
		@property prices_tb type=toolbar no_caption=1
		@property prices_tbl type=table no_caption=1

	@groupinfo prices_bargain_price caption="Soodushinnad" parent=prices
	@default group=prices_bargain_price

@groupinfo open_hrs caption="Avamisajad"
@default group=open_hrs

@groupinfo open_hrs_sub caption="Avamisajad" parent=open_hrs
@default group=open_hrs_sub

	@property oh_tb type=toolbar no_caption=1 store=no

	@layout oh type=vbox closeable=1 area_caption=Avamisajad

		@property oh_t type=table store=no no_caption=1 parent=oh

	@layout ch type=vbox closeable=1 area_caption=Pausid

		@property ch_t type=table store=no no_caption=1 parent=ch

	property openhours type=releditor reltype=RELTYPE_OPENHOURS rel_id=first use_form=emb store=no
	caption Avamisajad

	property pauses type=releditor reltype=RELTYPE_PAUSES rel_id=first use_form=emb store=no
	caption Pausid

@groupinfo people caption="Inimeste t&ouml;&ouml;ajad" parent=open_hrs
@default group=people

@property people type=text submit=no no_caption=1

@groupinfo work_graphs caption="T&ouml;&ouml;graafikud" parent=open_hrs  submit=no
@default  group=work_graphs

@property work_graphs type=text submit=no no_caption=1 

@groupinfo transl caption=T&otilde;lgi
@default group=transl
	
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


# RELTYPES

@reltype LOCATION value=1 clid=CL_LOCATION
@caption Asukoht

@reltype OWNER value=2 clid=CL_CRM_COMPANY
@caption Omanik

@reltype INVENTORY_FOLDER value=3 clid=CL_MENU
@caption Ressursside kataloog

@reltype AREA value=4 clid=CL_CRM_FIELD_CONFERENCE_ROOM
@caption Valdkond

@reltype CONDITIONS value=5 clid=CL_DOCUMENT
@caption Kasutustingimused

@reltype IMAGE value=6 clid=CL_IMAGE
@caption Pilt

@reltype CURRENCY value=7 clid=CL_CURRENCY
@caption Valuuta

@reltype CALENDAR value=8 clid=CL_PLANNER
@caption Kalender

@reltype ROOM_PRICE value=9 clid=CL_ROOM_PRICE
@caption Ruumi hind

@reltype SHOP_WAREHOUSE value=10 clid=CL_SHOP_WAREHOUSE
@caption Ladu

reltype TEMPLATE value=11 clid=CL_SHOP_WAREHOUSE
caption Templeit

@reltype OPENHOURS value=44 clid=CL_OPENHOURS
@caption Avamisajad

@reltype PROFESSION value=12 clid=CL_CRM_PROFESSION,CL_CRM_PERSON
@caption Ametinimetus

@reltype SETTINGS value=13 clid=CL_ROOM_SETTINGS
@caption Seaded

@reltype PAUSES value=45 clid=CL_OPENHOURS
@caption Pausid

@reltype ROOM value=46 clid=CL_ROOM
@caption Ruum

@reltype WORKING_SCENARIO value=47 clid=CL_CRM_WORKING_TIME_SCENARIO
@caption T&ouml;&ouml;aja stsenaariumid

@reltype CATEGORY value=48 clid=CL_ROOM_CATEGORY
@caption Ruumi kategooria
*/

class room extends class_base
{
	function room()
	{
		$this->init(array(
			"tpldir" => "common/room",
			"clid" => CL_ROOM
		));
		classload("core/icons");

		$this->unit_step = array(
			1 => t("minutit"),
			2 => t("tundi"),
			3 => t("p&auml;eva"),
		);
		$this->step_lengths = array(
			"" => 3600, //default
			1 => 60,
			2 => 3600,
			3 => 86400,
		);
		
		$this->time_units = array(
			1 => t("Sekundit"), //default
			60 => t("Minutit"),
			3600 => t("Tundi"),
			86400 => t("P&auml;eva"),
		);

		$this->time_unit_types = array(
			1 => t("minutitites"),
			2 => t("tundides"),
			3 => t("p&auml;evades"),
		);
		$this->weekdays = array(
			t("Sunday") , t("Monday") , t("Tuesday"), t("Wednesday") , t("Thursday") , t("Friday"), t("Saturday")
		);
		$this->weekdays_short = array(
			t("Su") , t("Mo") , t("Tu"), t("We") , t("Th") , t("Fr"), t("Sa")
		);
		
		classload("core/date/date_calc");

		$this->trans_props = array(
			"name"
		);
		$this->ui = get_instance(CL_USER);
	}

	/**
		@attrib name=set_bron_cust_arrived_status
		@param bron required 
		@param status required
	**/
	function set_bron_cust_arrived_status($arr)
	{
		$o = obj($arr["bron"]);
		$o->set_prop("client_arrived", $arr["status"]);
		$o->save();
		die("done");
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			# TAB PRICE
			case "min_prices_props":
			case "web_min_prod_price":
				return PROP_IGNORE;
			case 'people':
				$month = $_GET["month"];
				if(!$month)
				{
					$month = 0;	
				}
				$prop["value"]= $this->up_link($month);
				$arr["month"] = $month;
				$prop["value"].= $this->get_people_table($arr);
				break;
			case "work_graphs":
				$prop["value"] = $this->get_people_work_table($arr);
				if($arr["request"]["scenario"])
				{
					foreach($arr["request"]["scenario"] as $sc => $data)
					{
						$data["person"] = $sc;
						$data["room"] = $arr["obj_inst"]->id();
						$url = $this->mk_my_orb("make_worker_table", $data
							,"applications/crm/crm_working_time_scenario"
						);
						$prop["value"].= '<script language=javascript>Javascript:window.open("'.$url.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720")</script>';
					}
				}
				break;
			case "price":
				$prop["options"] = array(
					1 => t("Inimese kohta"),
					2 => t("Kasutusaja kohta"),
				);
				break;
			case "prices_tb":
				$this->_get_prices_tb($arr);
				break;
			case "prices_tbl":
				$this->_get_prices_tbl($arr);
				break;
			case "buffer_before_unit":
			case "buffer_after_unit":
				$prop["options"] = $this->time_units;
				break;
			case "prod_discount_loc":
				$prop["options"] = array(t("Tellimiskeskkonnast") , t("Ruumi juurest"));
				break;
			case "prod_web_discount":
				if(!$arr["obj_inst"]->prop("prod_discount_loc"))
				{
					return PROP_IGNORE;
				}
				break;
			case "calendar_tb":
				$this->_calendar_tb($arr);
				break;
			case "add_price_per_face":
				$prop["value"] = $this->gen_people_prices($arr);
				break;
			
			case "time_unit":
				
				$prop["options"] = $this->get_time_units();
				break;
			case "concurrent_type":
				$prop["options"] = array(
					0 => t("inimeste arv"),
					1 => t("broneeringute arv"),
				);
				break;
			# TAB CALENDAR
			case "calendar":
				### update schedule
				return PROP_IGNORE;
				$prop["value"] = $this->create_room_calendar ($arr);
				break;
			case "calendar_select":
				$prop["value"] = $this->_get_calendar_select($arr);
				break;
			
			case "concurrent_brons":
				$prop["value"] = ($prop["value"]) ? $prop["value"] : 1; 
				break;
			case "calendar_tbl":
				$this->_get_calendar_tbl($arr);
				break;	

			case "products_tr":
				if(!$this->_get_prod_fld($arr["obj_inst"]))
				{
					$prop["error"] = t("Pole valitud lao toodete kataloogi");
					return PROP_ERROR;
				}
				$this->_products_tr($arr);
				break;	

			case "reservation_template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "common/room"
				));
				if(!sizeof($prop["options"]))
				{//arr($prop);
					$prop["caption"] .= t("\n".$this->site_template_dir."");
	//				$prop["type"] = "text";
	//				$prop["value"] = t("Template fail peab asuma kataloogis :".$this->site_template_dir."");
				}
				break;
			
			case "products_tbl":
				$this->_products_tbl($arr);
				break;

			case "products_tb":
				$this->_products_tb($arr);
				break;		

			case "group_product_menu":
				if(!$arr["obj_inst"]->prop("use_product_times"))
				{
					return PROP_IGNORE;
				}
				break;
		};
		return $retval;
	}

	private function last_reservation_arrived_not_set($room)
	{
		if(!(is_object($room)))
		{
			return false;
		}
		
		$reservations = new object_list(array(
			"class_id" => array(CL_RESERVATION),
			"lang_id" => array(),
			"resource" => $room->id(),
			"end" => new obj_predicate_compare(OBJ_COMP_BETWEEN, time() - 84600, time()),
			"client_arrived" => new obj_predicate_not(1),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"verified" => 1,
					"deadline" => new obj_predicate_compare(OBJ_COMP_GREATER, time())
				)
			))
		));
 		$result = array();
  		foreach($reservations->arr() as $res)
  		{
			//if(($res->prop("client_arrived") == 0)  && ($res->prop("verified") || $res->prop("deadline") > time()))
			//{
				$result[$res->prop("end")] = $res->id();
			//}
  		}
		usort($result);
		if(sizeof($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "work_graphs":
				if($arr["request"]["extend"] > 0)
				{
					$arr["obj_inst"]->extend_work_graph(array(
						"person" => $arr["request"]["extend"],
						"start" => $arr["request"]["scenario"][$arr["request"]["extend"]]["start"],
						"end" => $arr["request"]["scenario"][$arr["request"]["extend"]]["end"],
					));
				}
				break;
			case 'people':
				$wd = $arr['obj_inst']->meta("working_days");
				foreach($arr["request"]["hidden_days"] as $p => $times)
				{
					foreach($times as $time => $val)
					{
						if($arr["request"]["working_days"][$p][$time])
						{
							$wd[$p][$time] = 1;
						}
						else
						{
							if($wd[$p][$time])
							{
								unset($wd[$p][$time]);
							}
						}
					}
				}
				
				$arr['obj_inst']->set_meta('working_days',$wd);
				break;

			case "products_find_product_name":
				
				if($arr["request"]["sel_imp"]);
				if($arr["request"]["products_find_product_name"])
				{
					$arr["obj_inst"]->set_meta("search_data" , $arr["request"]);
				}
				break;

			case "deadline":
				$prop["value"] = time() + 15*60;
				break;

			case "web_min_prod_price":return PROP_IGNORE;
				$arr["obj_inst"]->set_meta("web_min_prod_prices", $arr["request"]["wpm_currency"]);
				break;
				
			case "add_price_per_face":
				$res = array();
				$n = 1;
				foreach($arr["request"]["people_prices"] as $price)
				{
					$tmp = reset($price);
					if($tmp || $tmp == "0")
					{
						$res[$n] =  $price;
						$n++;
					}
				}
				$arr["obj_inst"]->set_meta("people_prices", $res);
				break;
				
			case "min_prices_props":return PROP_IGNORE;
				$arr["obj_inst"]->set_meta("web_room_min_price", $arr["request"]["web_room_min_price"]);
				break;
				
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function callback_mod_retval($arr)
	{
		if($arr["request"]["images_search"])
		{
			$this->_handle_img_search_result(&$arr);
		}
		if($arr["request"]["prices_search"])
		{
			$this->_handle_prc_search_result(&$arr);
		}
		if($arr["request"]["img"])
		{
			$this->_save_img_ord(&$arr);
		}

		if($arr["request"]["add_scenario"])
		{
			$o = obj($arr["request"]["id"]);
			if(substr_count($arr["request"]["add_scenario"],","))
			{
				$csa = explode("," , $arr["request"]["add_scenario"]);
			}
			else
			{
				$csa = array($arr["request"]["add_scenario"]);
			}
			foreach($csa as $cs)
			{
				if(is_oid($cs))
				{
					$o->connect(array(
						"to" => $cs,
						"reltype" => "RELTYPE_WORKING_SCENARIO",
					));
				}
			}
		}

		if ($arr["request"]["set_view_dates"])
		{
			$arr["args"]["start"] = date_edit::get_timestamp($arr["request"]["set_d_from"]);
			if ($arr["request"]["set_view_dates"] == 1)
			{
				$arr["args"]["end"] = date_edit::get_day_end_timestamp($arr["request"]["set_d_to"]);
			}
			else
			if ($arr["request"]["set_view_dates"] == 2)
			{
				 $arr["args"]["end"] = $arr["args"]["start"] + 24*3600 - 1;
			}
			else
			if ($arr["request"]["set_view_dates"] == 3)
			{
				$arr["args"]["end"] = $arr["args"]["start"] + 24*3600*7 - 1;
			}
			else
			if ($arr["request"]["set_view_dates"] == 4)
			{
				$arr["args"]["end"] = $arr["args"]["start"] + 24*3600*31 - 1;
			}
		}
//		if($arr["request"]["submit_scenario"])
//		{
			foreach($arr["request"]["sel"] as $sel)
			{
				$arr["args"]["scenario"][$sel] = $arr["request"]["scenario"][$sel];
			}
//		}
	}

	function callback_pre_edit($arr)
	{
		if(!$this->get_calendar($arr["obj_inst"]->id()))
		{
			$this->_create_calendar($arr["obj_inst"]);
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["set_view_dates"] = " ";
		$arr["set_oh"] = " ";
		$arr["extend"] = " ";
		$arr["set_ps"] = " ";
		$arr["add_scenario"] = " ";
		if($this->can("view" , $arr["id"]))
		{
			$room = obj($arr["id"]);
			if($room->get_group_setting("no_time_check"))
			{
				$arr["bron_can_exceed_limit"] = 1;
			}
			else
			{
				$arr["bron_can_exceed_limit"] = 0;
			}
		}
	}

	/**
		@attrib name=remove_images params=name all_args=1
	**/
	function remove_images($arr)
	{
		$o = obj($arr["id"]);
		if(count($arr["sel"]))
		{
			$o->disconnect(array(
				"from" => $arr["sel"]
			));
		}
		return $arr["post_ru"];
	}

	private function _handle_img_search_result($arr)
	{
		$p = get_instance("vcl/popup_search");
		$p->do_create_rels(obj($arr["args"]["id"]), $arr["request"]["images_search"], 6);
	}
	
	private function _handle_prc_search_result($arr)
	{
		$p = get_instance("vcl/popup_search");
		$p->do_create_rels(obj($arr["args"]["id"]), $arr["request"]["prices_search"], 9);
	}
	
	private function _save_img_ord($arr)
	{
		$o = obj($arr["args"]["id"]);
		$o->set_meta("img_ord", $arr["request"]["img"]);
		$o->save();
	}

	function _get_images_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add_image",
			"tooltip" => t("Lisa pilt"),
			"img" => "new.gif",
			"url" => $this->mk_my_orb("new", array(
				"parent" => $arr["obj_inst"]->id(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 6,
				"return_url" => get_ru(),
			), CL_IMAGE),
		));

		$popup_search = get_instance("vcl/popup_search");
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "images_search",
			"clid" => CL_IMAGE,
		));
		$tb->add_cdata($search_butt);

		$tb->add_button(array(
			"name" => "remove_image",
			"tooltip" => t("Eemalda pildid"),
			"img" => "delete.gif",
			"action" => "remove_images",
		));
		return PROP_OK;
		
	}

	function _get_images_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "sele",
			"width" => "20px",
		));
		$t->define_field(array(
			"name" => "image_ord",
			
			"caption" => t("jrk"),
			"width" => "20px",
		));
		$t->define_field(array(
			"name" => "image_name",
			"caption" => t("Pilt"),
		));
		$t->set_caption(t("Pildid"));

		$imgs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_IMAGE",
		));
		$ord = $arr["obj_inst"]->meta("img_ord");
		foreach($imgs as $c)
		{
			$img = $c->to();
			$t->define_data(array(
				"sele" => $img->id(),
				"image_ord" => html::textbox(array(
					"name" => "img[".$img->id()."]",
					"size" => "3",
					"value" => $ord[$img->id()],
				)),
				"image_name" => $img->name(),
			));
		}
	}

	function _get_prices_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$ba = ($arr["request"]["group"] == "prices_price")?false:true;
		$tb->add_button(array(
			"name" => "new",
			"tooltip" => t("Uus hind"),
			"img" => "new.gif",
			"url" => $this->mk_my_orb("new", array(
				"parent" => $arr["obj_inst"]->id(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 9, 
				"ba" => $ba,
				"return_url" => get_ru(),
			), CL_ROOM_PRICE),
		));

		$popup_search = get_instance("vcl/popup_search");
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "prices_search",
			"clid" => CL_ROOM_PRICE,
		));
		$tb->add_cdata($search_butt);

		$tb->add_button(array(
			"name" => "remove_image",
			"tooltip" => t("Eemalda hinnad"),
			"img" => "delete.gif",
			"action" => "remove_images",
		));
		return PROP_OK;
	}

	function _get_prices_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$ba = ($arr["request"]["group"] == "prices_price")?false:true;
		if($ba)
		{
			$t->define_field(array(
				"name" => "active",
				"caption" => t("Kehtib"),
				"align" => "center",
			));
			$t->define_field(array(
				"name" => "recur",
				"caption" => t("Kordub"),
				"align" => "center",
			));
		}
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "selected",
			"width" => "30px",
		));
		$t->define_field(array(
			"name" => "dates",
			"caption" => t("Kuup&auml;evad"),
		));
		$t->define_field(array(
			"name" => "weekdays",
			"caption" => t("N&auml;dalap&auml;evad"),
		));
		$t->define_field(array(
			"name" => "nr",
			"caption" => t("Mitmes"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kellaaeg"),
		));
		if(!$ba)
		{
			$t->define_field(array(
				"name" => "time_step",
				"caption" => t("Aeg"),
			));
	
			$cur = $arr["obj_inst"]->prop("currency");
			foreach($cur as $c)
			{
				if(!is_oid($c))
				{
					continue;
				}
				$cobj = obj($c);
				$t->define_field(array(
					"name" => "currency_".$cobj->id(),
					"caption" => $cobj->prop("unit_name"),
				));
			}
		}

		$t->define_field(array(
			"name" => "bron_made",
			"caption" => t("Broneering tehtud"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "bron_made_from",
			"caption" => t("Alates"),
			"align" => "center",
			"parent" => "bron_made",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "bron_made_to",
			"caption" => t("Kuni"),
			"align" => "center",
			"parent" => "bron_made",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"width" => "30px",
			"align" => "center",
		));


		$ds = get_instance("vcl/date_edit");
		$ds->configure(array(
			"year" => "year",
			"month" => "month",
			"day" => "day",
		));

		# getting data
		$price_objs = $this->get_prices($arr["obj_inst"]->id(), $ba);
		$price_inst = get_instance(CL_ROOM_PRICE);
		$caption = $this->unit_step[$arr["obj_inst"]->prop("time_unit")];
		foreach($price_objs as $oid => $obj)
		{
			$wd = $obj->prop("weekdays");
			$wds = array();
			foreach($wd as $nr)
			{
				$wds[$nr] = $price_inst->weekdays[$nr];
			}
			$prices = $obj->prop("prices");
			//$prices = $price_inst->get_prices($oid); // someone made the get_prices method private
			$prc = array();
			foreach($prices as $cur_oid => $price)
			{
				$prc["currency_".$cur_oid] = $price;
			}
			$t_from = $obj->prop("time_from");
			$t_to = $obj->prop("time_to");
			$data = array(
				"dates" => date("d/m/Y", $obj->prop("date_from"))." kuni ".date("d/m/Y", $obj->prop("date_to")),
				"time" => str_pad($t_from["hour"], 2, "0", STR_PAD_LEFT).":".str_pad($t_from["minute"], 2, "0", STR_PAD_LEFT)." - ".str_pad($t_to["hour"], 2, "0", STR_PAD_LEFT).":".str_pad($t_to["minute"], 2, "0", STR_PAD_LEFT),
				"weekdays" => join(",", $wds),
				"nr" => $obj->prop("nr"),
				"selected" => $oid,
				"time_step" => $obj->prop("time")." ".$caption,
				"active" => $obj->prop("active")?t("Jah"):t("Ei"),
				"recur" => $obj->prop("recur")?t("Jah"):t("Ei"),
				"bron_made_from" => $obj->prop("bron_made_from"),
				"bron_made_to" => $obj->prop("bron_made_to"),
				"change" => html::href(array(
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif",
						"border" => 0,
						"alt" => t("Muuda"),
					)),
					"url" => html::get_change_url($oid, array(
						"return_url" => get_ru(),
					)),
				)),
			);
			$data = array_merge($data, $prc);
			$t->define_data($data);
		}
	}

	private function get_calendar($oid)
	{
		if(!is_oid($oid))
		{
			return false;
		}
		$o = obj($oid);
		$cal = $o->get_first_obj_by_reltype("RELTYPE_CALENDAR");
		if(is_object($cal))
		{
			return $cal->id();
		}
		else 
		{
			return false;
		}
	}
	
	/** calendar callback **/
	function get_overview($arr = array())
	{
		return $this->overview;
	}

	private function _create_calendar($room)
	{
		$o = obj();
		$o->set_class_id(CL_PLANNER);
		$o->set_parent($room->id());
		$o->set_name("Ruumi '".$room->name()."' kalender");
		$o->save();
		$room->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_CALENDAR",
		));
	}

	/**
		@comment
			returns array of CL_ROOM_PRICE objects
	**/
	private function get_prices($oid, $bargain_prices = false)
	{
		if(!is_oid($oid))
		{
			return false;
		}
		$o = obj($oid);
		$cs = $o->connections_from(array(
			"class_id" => CL_ROOM_PRICE,
			"type" => "RELTYPE_ROOM_PRICE",
		));
		foreach($cs as $c)
		{
			$to = $c->to();
			if(($to->prop("type") == 2 && $bargain_prices) || ($to->prop("type") == 1 && !$bargain_prices))
			{
				$ret[$to->id()] = $to;
			}
		}
		return $ret;
	}
	
	function _calendar_tb($arr)
	{
		$arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CALENDAR");
		if(is_object($arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
		{
			$cal_obj = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CALENDAR");
			$cal = $cal_obj->id();
			$parent = $cal_obj->prop("event_folder");
		}
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new_reservation",
			"img" => "new.gif",
			"tooltip" => t("Broneering"),
			"action" => "do_add_reservation",
			"url" => $this->mk_my_orb(
				"new", 
				array(
					"parent" => $parent,
					"return_url" => get_ru(),
					"calendar" => $cal,
				),
				CL_RESERVATION
			)
		));
	}
	
	/**
		@attrib name=admin_add_bron_popup params=name all_args=1
		@param start1 required
			start , type - int or array
		@param parent required type=oid
			parent
		@param end required
			end , type - int or array
		@param resource required type=int
			room
		@param product optional
			chosen product
		@param return_url optional type=string
			url for opener window
		@param bron_data optional type=array
	**/
	function admin_add_bron_popup($arr)
	{
		extract($arr);
//		arr($arr);
		extract($_POST["bron"]);
		extract($_GET["bron"]);
		
		$room = obj($resource);
		$professions = $room->prop("professions");
		if(is_array($professions) && sizeof($professions))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"CL_CRM_PERSON.RELTYPE_RANK" => $professions,
			));
			$people_opts = array("") + $ol->names();
		}
		if(is_array($_POST["bron"]) || is_array($_GET["bron"]))
		{
			$room_inst = get_instance(CL_ROOM);
			$start1 = mktime($start1["hour"], $start1["minute"], 0, $start1["month"], $start1["day"], $start1["year"]);
			$end = mktime($end["hour"], $end["minute"], 0, $end["month"], $end["day"], $end["year"]);
			
			if(is_oid($product))
			{
				$product_obj = obj($product);
				$end = $start1 + $product_obj->prop("reservation_time")*$product_obj->prop("reservation_time_unit");
			}
			
			if(!($room_inst->check_if_available(array(
				"room" => $resource,
				"start" => $start1,
				"end" => $end,
			)) ||$room->prop("allow_multiple") ) && $room_inst->last_bron_id !=$id)
			{
				$err = t("Sellisele ajale pole broneerida v&otilde;imalik ".date("d.m.Y H:i:s", $start1)." - ".date("d.m.Y H:i:s", $end));
				die($err);
			}
			else
			{
				if(is_oid($id) && $this->can("view" , $id))
				{
					$bron = obj($id);
				}
				else
				{
					$bron = new object();
					$bron->set_class_id(CL_RESERVATION);
					$bron->set_parent($parent);
					$bron->set_prop("resource", $resource);
					$bron->save();
					if($product)
					{
						$bron->set_meta("amount" ,array($product => 1));
					}
				}
				$si = get_instance(CL_ROOM_SETTINGS);
				$sts = $this->get_settings_for_room(obj($resource));
				$bron->set_prop("start1", $start1);
				$bron->set_prop("people_count", $people_count);
				$bron->set_prop("end" ,$end);
				$bron->set_prop("content" , $comment);
				$bron->set_prop("verified" , $si->get_verified_default_for_group($this->get_settings_for_room(obj($resource))));
				$bron->set_prop("deadline" , time()+15*60);
				
				if(is_oid($people) && $this->can("view" , $people))
				{
					$bron->set_prop("people" , $people);
				}
				if(strlen($phone))
				{
					$phones = new object_list(array(
						"lang_id" => array(),
						"class_id" => CL_CRM_PHONE,
						"name" => $phone,
					));
					if(!sizeof($phones->arr()))
					{
						$phone_obj = new object();
						$phone_obj->set_class_id(CL_CRM_PHONE);
						$phone_obj->set_name($phone);
						$phone_obj->set_prop("type" , "mobile");
						$phone_obj->set_parent($parent);
						$phone_obj->save();
					}
					else
					{
						$phone_obj = reset($phones->arr());
					}
				}
				
				if(strlen($firstname) || strlen($lastname))
				{
					$persons = new object_list(array(
						"lang_id" => array(),
						"class_id" => CL_CRM_PERSON,
						"firstname" => $firstname,
						"lastname" => $lastname,
					));
					if(!sizeof($persons->arr()))
					{
						$person = new object();
						$person->set_class_id(CL_CRM_PERSON);
						if(is_oid($sts->id()) && $sts->prop("customer_menu") && $this-> can("add" , $sts->prop("customer_menu")))
						{
							$person->set_parent($sts->prop("customer_menu"));
						}
						else
						{
							$person->set_parent($parent);
						}
						$person->set_name(trim($firstname)." ".trim($lastname));
						$person->set_prop("firstname" , trim($firstname));
						$person->set_prop("lastname" , trim($lastname));
						$person->save();
					}
					else
					{
						$person = reset($persons->arr());
					}
					$bron->set_prop("customer",$person->id());
					$bron->connect(array("to" => $person->id(), "reltype" => 1));
					if(strlen($phone))
					{
						$person->connect(array("to"=> $phone_obj->id(), "type" => "RELTYPE_PHONE"));
						$person->set_prop("phone" , $phone_obj->id());
						$person->save();
					}
				}
				
				if(strlen($company))
				{
					$companys = new object_list(array(
						"lang_id" => array(),
						"class_id" => CL_CRM_Company,
						"name" => $company,
					));
					if(!sizeof($companys->arr()))
					{
						$co = new object();
						$co->set_class_id(CL_CRM_COMPANY);
						if(is_oid($sts->id()) && $sts->prop("customer_menu") && $this-> can("add" , $sts->prop("customer_menu")))
						{
							$co->set_parent($sts->prop("customer_menu"));
						}
						else
						{
							$co->set_parent($parent);
						}
						$co->set_name($company);
						$co->save();
					}
					else
					{
						$co = reset($companys->arr());
					}
					$bron->set_prop("customer",$co->id());
					$bron->connect(array("to" => $co->id(), "reltype" => 1));
					if(strlen($phone))
					{
						$co->connect(array("to"=> $phone_obj->id(), "type" => "RELTYPE_PHONE"));
						$co->set_prop("phone_id" , $phone_obj->id());
						$co->save();
					}
				}
				$bron->set_correct_name();
				$bron->save();

				if(is_array($arr["post_msg_after_reservation"]) && ($inst = get_instance($arr["post_msg_after_reservation"]["class_id"])) && is_callable(array($inst, $arr["post_msg_after_reservation"]["action"])))
				{
					$arr["post_msg_after_reservation"]["reservation"] = $bron;
					$inst->$arr["post_msg_after_reservation"]["action"]($arr["post_msg_after_reservation"]);
				}
				$id = $bron->id();

				if(is_array($other_rooms) || sizeof($other_rooms))
				{
					$bron->make_slave_brons($other_rooms);
				}

				die("<script type='text/javascript'>
					if (window.opener)
					window.opener.location.href='".$arr['return_url']."';
					window.close();
					</script>
				");
			}
		}
		
		if(is_oid($product) && $start1>1)
		{
			$product_obj = obj($product);
			$end = $start1 + $product_obj->prop("reservation_time")*$product_obj->prop("reservation_time_unit");
		}
		$url = html::get_new_url(
			CL_QUICK_RESERVATION,
			$room->id(),
			array(
				"post_msg_after_reservation" => $arr["post_msg_after_reservation"],
				"product" => $product,
				"end" => $end,
				"start1" => $start1,
				"firstname" => $firstname,
				"lastname" => $lastname,
				"company" => $company,
				"phone" => $phone,
				"people_count" => $people_count,
				"comment" => $comment,
				"people" => $people,
				"parent" => $parent,
				"resource" => $resource,
				"error" => $err,
				"return_url" => $return_url,
				"in_popup" => 1,
//				"connect_impl" => $arr["request"]["id"],
			)
		);
		die("<script type='text/javascript'>
			window.location.href='".$url."';
			</script>
		");
	}
	
	/**
	
		@attrib params=name name=admin_add_bron_popup_table all_args=1
	**/
	function admin_add_bron_popup_table($arr)
	{
		extract($arr);
		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));
		
		$t->define_field(array(
			"name" => "caption",
		));
		$t->define_field(array(
			"name" => "value",
		));

		$t->define_data(array(
			"caption" => t("Algusaeg"),
			"value" => html::datetime_select(array(
				"name" => "bron[start1]",
				"value" => $start1,
			)),
		));
		$t->define_data(array(
			"caption" => t("L&otilde;ppaeg"),
			"value" => html::datetime_select(array(
				"name" => "bron[end]",
				"value" => $end,
			)),
		));
			
		$t->define_data(array(
			"caption" => t("Eesnimi"),
			"value" => html::textbox(array(
				"name" => "bron[firstname]",
				"size" => 40,
				"value" => $firstname,
			)),
		));
			
		$t->define_data(array(
			"caption" => t("Perenimi"),
			"value" => html::textbox(array(
				"name" => "bron[lastname]",
				"size" => 40,
				"value" => $lastname,
			)),
		));
			
		$t->define_data(array(
			"caption" => t("Organisatsioon"),
			"value" => html::textbox(array(
				"name" => "bron[company]",
				"size" => 40,
				"value" => $company,
			)),
		));
		
		$t->define_data(array(
			"caption" => t("Telefon"),
			"value" => html::textbox(array(
				"name" => "bron[phone]",
				"size" => 40,
				"value" => $phone,
			)),
		));
			
		$t->define_data(array(
			"caption" => t("M&auml;rkused"),
			"value" => html::textarea(array(
				"name" => "bron[comment]",
				"size" => 40,
				"value" => $comment,
			)),
		));	
			
		$t->define_data(array(
			"caption" => t("Meie esindaja"),
			"value" => html::select(array(
				"name" => "bron[people]",
				"value" => $people,
				"options" => $people_opts,
			)),
		));	
		
		$t->define_data(array(
			"value" => html::submit(array(
				"value" => t("Salvesta"),
			)),
		));	
		
		$t->define_data(array(
			"value" => html::hidden(array(
				"name" => "bron[id]",
				"value" => $id,
			)),
		));
		return $t->draw();
	}
	
	
	function _get_time_select($arr)
	{
		$x=0;
		$options = array();
		$week = date("W" , time());
		$weekstart = get_week_start();
		while($x<20)
		{
			$url = aw_url_change_var("start",$weekstart,get_ru());
			$options[$url] = date("W" , ($weekstart + 3600)) . ". " .date("d.m.Y", ($weekstart + 3600)) . " - " . date("d.m.Y", ($weekstart+604799));

			if($arr["request"]["start"] == $weekstart) $selected = $url;
			$weekstart = $weekstart + 604800;
			$x++;
		};
		
		$ret.= html::select(array(
			"name" => "room_reservation_select",
			"options" => $options,
			"onchange" => " window.location = this.value;",
			"selected" => $selected,
		));
		return $ret;
	}

	function _get_length_select($arr)
	{
		$ret = "";
		if(is_object($arr["obj_inst"]) && !$arr["obj_inst"]->prop("use_product_times"))
		{
			$options = array();
			$time_step = $sb_time_step = $arr["obj_inst"]->prop("time_step");
			$time_from = $arr["obj_inst"]->prop("time_from");
			$time_to = $arr["obj_inst"]->prop("time_to");
			$unit = $this->step_lengths[$arr["obj_inst"]->prop("time_unit")];

			if($arr["obj_inst"]->prop("selectbox_time_step") > 0 || $arr["obj_inst"]->prop("selectbox_time_to") > 0)
			{
				$add = $arr["obj_inst"]->prop("selectbox_time_step")/$time_step;
				$sb_time_step = $arr["obj_inst"]->prop("selectbox_time_step");
				$time_from = $arr["obj_inst"]->prop("selectbox_time_from");
				$time_to = $arr["obj_inst"]->prop("selectbox_time_to");
			}
			else
			{
				$add = 1;
			}

			$end = $time_to/$time_step;
			$x = $time_from/$time_step;

			while($x<=$end || ($x-$end < 0.000001))
			{
				$options["".$x] = ($x * $time_step);
				if($unit > 60)
				{
					$options["".$x] = $options["".$x] %60;

					if(($x * $sb_time_step - ($x * $sb_time_step)%10))
					{
						$small_units = round(($x * $sb_time_step - ($x * $sb_time_step)%10)*60);
						//if(aw_global_get("uid") == "struktuur");
						if($small_units%60 == 0)
						{
							$options["".$x] = $options["".$x] + $small_units/60;
						}
						else
						{
							if($small_units < 10)
							{
								$small_units = "0".$small_units;
							}
							$options["".$x] = $options["".$x] . ":" . $small_units;
						}
					}
				}
				$x = $x + $add;
			}
			$ret.= html::select(array(
				"name" => "room_reservation_length",
				"options" => $options,
				"onchange" => "changeRoomReservationLength(this);",
			));
			$ret.= $this->unit_step[$arr["obj_inst"]->prop("time_unit")];
		}

		//seda hakkaks js siis vajama, et natuke aega juurde liita ajale mida selectitakse
		$after_buffer = "".($arr["obj_inst"]->prop("buffer_after")*$arr["obj_inst"]->prop("buffer_after_unit")/($arr["obj_inst"]->prop("time_step")*$this->step_lengths[$arr["obj_inst"]->prop("time_unit")]));
		$ret.= html::hidden(array("name" => "buffer_after", "id"=>"buffer_after" ,"value"=>$after_buffer));
		$ret.= html::hidden(array("name" => "product", "id"=>"product_id" ,"value"=>""));

		return $ret;
	}

	function _get_hidden_fields($arr)
	{
		$ret = html::hidden(array("name" => "product", "id"=>"product_id" ,"value"=>""));
		$ret.=html::hidden(array("name" => "free_field_value", "id"=>"free_field_value" ,"value"=>($this->settings && $this->settings->prop("available_time_string"))?$this->settings->prop("available_time_string") :t("VABA")));
		$ret.=html::hidden(array("name" => "res_field_value", "id"=>"res_field_value" ,"value"=>($this->settings && $this->settings->prop("reserved_time_string"))?$this->settings->prop("reserved_time_string") :t("BRON")));
		$ret.=html::hidden(array("name" => "do_field_value", "id"=>"do_field_value" ,"value"=>"Broneeri"));
		
		return $ret;
	}

	function _get_calendar_select($arr)
	{
		$this->settings = $settings = $this->get_settings_for_room($arr["obj_inst"]);
		
		$ret = "";
		$ret .= $this->_get_mark_arrived_pop($arr, $settings);

                $x=0;
                $options = array();
                $week = date("W" , time());
                $weekstart = get_week_start();
		$day_start = get_day_start();
                while($x<20)
                {//arr(date("d-m-Y h:i",($weekstart + 8000)));
                        $url = aw_url_change_var("end", null, aw_url_change_var("start",$weekstart,get_ru()));
                        $options[$url] = date("W" , ($weekstart + 3600)) . ". " .date("d.m.Y", ($weekstart + 3600)) . " - " . date("d.m.Y", ($weekstart+604800));//see +4k on selleks, et kellakeeramise jama puhul yle tunni ka mojuks
                        if($arr["request"]["start"] == $weekstart) $selected = $url;
                        $weekstart = $weekstart + 604800;
                        $x++;
                };
		$ret.= $this->_get_hidden_fields($arr);

                $ws = html::select(array(
                        "name" => "room_reservation_select",
                        "options" => $options,
                        "onchange" => " window.location = this.value;",
                        "selected" => $selected,
                ));

		$this->read_template("cal_header.tpl");

		
		$anc = "";
		$opened = $arr["obj_inst"]->get_calendar_visible_time($_GET["start"] , $_GET["end"]);
		$x = $opened["start"];
		while($x < $opened["end"])
		{
			$this->vars(array(
				"alink" => "#time_".date("H" ,  $day_start + $x),
				"acapt" => date("H" ,  $day_start + $x),
			));
			
			$anc.= $this->parse("ANCHOR_LINKS");
			$x += 3600;
		}

		$this->vars(array(
			"pop" => $ret,
			"week_select_caption" => t("Vali n&auml;dal"),
			"date_from_caption" => t("Alates"),
			"date_to_caption" => t("Kuni"),
			"without_detail_information" => t("Ilma detailse infota"),
			"week_select" => $ws,
			"date_from" => html::date_select(array(
                  	      "name" => "set_d_from",
                        	"value" => $_GET["start"]
                	)),
			"ts_buttons" => html::button(array(
	                        "onclick" => "document.changeform.set_view_dates.value=2;submit_changeform();",
        	                "value" => t("P&auml;ev")
                	))." ".html::button(array(
	                        "onclick" => "document.changeform.set_view_dates.value=3;submit_changeform();",
        	                "value" => t("N&auml;dal")
                	))." ".html::button(array(
                        	"onclick" => "document.changeform.set_view_dates.value=4;submit_changeform();",
	                        "value" => t("Kuu")
        	        )),
			"date_to" => html::date_select(array(
	                        "name" => "set_d_to",
        	                "value" => $_GET["end"]
                	)),
			"to_button" => html::button(array(
	                        "onclick" => "document.changeform.set_view_dates.value=1;submit_changeform();",
        	                "value" => t("N&auml;ita vahemikku")
                	)),
			"no_det_info" => $_GET["no_det_info"] ? "checked" : "",
			"ANCHOR_LINKS" => $anc,
		));
		
		if(is_object($arr["obj_inst"]) && !$arr["obj_inst"]->prop("use_product_times"))
		{
			$this->vars(array(
				"length_sel" => t("Vali broneeringu pikkus:").$this->_get_length_select(array("obj_inst" => $arr["obj_inst"]))
			));
		}

		return $arr["prop"]["value"] = $this->parse();
	}

	function _get_minmax_prices($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
//		$t->set_header(t("Miinimum ja maksimum"));
		$t->define_field(array(
			"caption" => t("Valuuta"),
			"name" => "currency",
		));
		$t->define_field(array(
			"caption" => t("Kogu ruumi broneeringu miinimumhind"),
			"name" => "web_room_min_price",
		));
		$t->define_field(array(
			"caption" => t("Toodete miinimumhind"),
			"name" => "web_min_prod_prices",
		));
		$t->define_field(array(
			"caption" => t("Ruumi broneeringu maksimumhind"),
			"name" => "max_room_price",
		));
		
		$web_room_min_price = $arr["obj_inst"]->meta("web_room_min_price");
		$web_min_prod_prices = $arr["obj_inst"]->meta("web_min_prod_prices");
		$max_room_price =  $arr["obj_inst"]->meta("max_room_price");
		foreach($arr["obj_inst"]->get_currency_ol()->arr() as $c)
		{
			$data = array();
			$data["currency"] = $c->name();
			$cur = $c->id();
			$data["web_room_min_price"] = html::textbox(array(
				"name" => "web_room_min_price[".$cur."]",
				"size" => 4,
				"value" => $web_room_min_price[$cur],
			));
			$data["web_min_prod_prices"] = html::textbox(array(
				"name" => "web_min_prod_prices[".$cur."]",
				"size" => 4,
				"value" => $web_min_prod_prices[$cur],
			));
			$data["max_room_price"] = html::textbox(array(
				"name" => "max_room_price[".$cur."]",
				"size" => 4,
				"value" => $max_room_price[$cur],
			));
			$t->define_data($data);
		}
	}

	function _set_minmax_prices($arr)
	{
		$minmax_props = array("web_room_min_price" , "web_min_prod_prices" , "max_room_price");
		foreach($minmax_props as $prop)
		{
			if($arr["request"][$prop])
			{
				$arr["obj_inst"]->set_meta($prop , $arr["request"][$prop]);
			}
		}
	}

	/** updates room calendar table
		@attrib name=update_calendar_table params=name api=1
		@param room required type=oid
			room object id
		@returns html
			calendar table html
	**/
	function update_calendar_table($arr)
	{
		$t = new vcl_table;
		$prop = array(
			"vcl_inst" => &$t
		);
		$this->_get_calendar_tbl(array(
			"room" => $arr["room"],
			"prop" => $prop,
		));
		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die($t->draw());
	}

	function _get_calendar_tbl($arr)
	{
		enter_function("get_calendar_tbl");
		//kkui asi tuleb veebist
		if(is_oid($arr["room"]) && $this->can("view" , $arr["room"]))
		{
			$arr["obj_inst"] = obj($arr["room"]);
			if(!$this->id)
			{
				$this->id = $arr["room"];
			}
			if($_GET["start"])
			{
				$arr["request"]["start"] = $_GET["start"];
			}
		}
		$t = &$arr["prop"]["vcl_inst"];
		$open_inst = $this->open_inst = get_instance(CL_OPENHOURS);
		$this->openhours = $this->get_current_openhours_for_room($arr["obj_inst"]);
		$this->pauses = $this->get_current_pauses_for_room($arr["obj_inst"]);
		$si = get_instance(CL_ROOM_SETTINGS);
		if(is_array($this->openhours))
		{
 			$open = $this->open = $open_inst->get_times_for_date(reset($this->openhours), $time);
		}

		$this->start = $arr["request"]["start"];

		exit_function("get_calendar_tbl");
		//see siis n2itab miskeid valitud muid n2dalaid
		enter_function("get_calendar_tbl::2");
		$start_hour = 0;
		$start_minute = 0;
		$day_end = 86400;
		if(is_array($this->openhours))
		{
			$tmp_start = $arr["request"]["start"];
			if (!$tmp_start)
			{
				$tmp_start = mktime(0, 0, 0, date("n", time()), date("j", time()), date("Y", time()));
			}
			$tmp_end = $arr["request"]["end"];
			if (!$tmp_end)
			{
				$tmp_end = $tmp_start + 24*3600*7;
			}
			$gwo = $this->get_when_opens($tmp_start, $tmp_end);
			extract($gwo);
		}
		
		if($arr["request"]["start"])
		{
			$today_start = $arr["request"]["start"];
			//seda avamise alguse aega peab ka ikka arvestama, muidu v6tab esimese tsykli miskist x kohast
 			if($gwo["start_hour"])
 			{
	 			$this->start = $this->start+3600*$gwo["start_hour"];
 				$today_start = $today_start+3600*$gwo["start_hour"];
				//$day_end -= (3600*$gwo["start_hour"]);
 			}
 			if($gwo["start_minute"])
	 		{
 				$this->start = $this->start+60*$gwo["start_minute"];
 				$today_start = $today_start+60*$gwo["start_minute"];
				//$day_end -= (60*$gwo["start_minute"]);
	 		}
		}
		else
		{
			if($start_hour == 24)
			{
				$start_hour = 0;
			}
			$this->start = $today_start = mktime($start_hour, $start_minute, 0, date("n", time()), date("j", time()), date("Y", time()));
			//$day_end -= (3600*$start_hour + 60*$start_minute);
		}

		$step = 0;
		$step_length = $this->step_lengths[$arr["obj_inst"]->prop("time_unit")];
		exit_function("get_calendar_tbl::2");

		$settings = $this->get_settings_for_room($arr["obj_inst"]);
		classload("core/date/date_calc");
		if (is_oid($settings->id()) && !$arr["request"]["start"])
		{
			if ($settings->prop("cal_from_today"))
			{
				$this->start = $today_start = get_day_start();
			}
			else
			{
				$this->start = $today_start = get_week_start();
			}

			//seda avamise alguse aega peab ka ikka arvestama, muidu v6tab esimese tsykli miskist x kohast
			if($gwo["start_hour"])
			{
				$this->start = $this->start+3600*$gwo["start_hour"];
				$today_start = $today_start+3600*$gwo["start_hour"];
			//	$day_end -= (3600*$gwo["start_hour"]);
			}
			if($gwo["start_minute"])
			{
				$this->start = $this->start+60*$gwo["start_minute"];
				$today_start = $today_start+60*$gwo["start_minute"];
			//	$day_end -= (60*$gwo["start_minute"]);
			}
		}
		
		if (date("I", time()) != 1 && date("I", $today_start) == 1)
		{
			$this->start -= 3600;
			$today_start -= 3600;
		}


		$len = 7;
		if ($_GET["start"] && $_GET["end"])
		{
			$len = floor(($_GET["end"]+1 - $_GET["start"]) / 86400);
		}
		$this->len = $len;
		enter_function("get_calendar_tbl::3::genres");
		$this->other_rooms = array_keys($arr["obj_inst"]->get_other_rooms_selection());
		$this->generate_res_table($arr["obj_inst"], $this->start, $this->start + 24*3600*$len , $settings->prop("show_unverified"));
		$this->multi = $arr["obj_inst"]->prop("allow_multiple");
		
		$period_end = $this->start + 24*3600*$len;
		$this->extra_row = $arr["obj_inst"]->has_extra_row($this->start, $period_end);
		if($this->extra_row)
		{
			$this->gen_extra_res_table($arr["obj_inst"] , $this->start, $period_end);
		}

		$this->show_unverified = $settings->prop("show_unverified");

/*		if(is_array($this->other_rooms) && sizeof($this->other_rooms))
		{
			$other_room_ol = new object_list();
			$other_room_ol->add(array_keys($this->other_rooms));
		}
*/
		$this->_init_calendar_t($t,$this->start, $len);
		exit_function("get_calendar_tbl::3::genres");

		$arr["step_length"] = $step_length * $arr["obj_inst"]->prop("time_step");
		$this->step_length = $arr["step_length"];

		if(is_object($cal_obj = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
		{
			$cal = $cal_obj->id();
			$b_parent = $cal_obj->prop("event_folder");
			if (!$b_parent)
			{
				$b_parent = $cal_obj->id();
			}
		}
	
		$num_rows = 0;
		$time_step = $arr["obj_inst"]->prop("time_step");
		$steps = (int)(86400 - (3600*$gwo["start_hour"] + 60*$gwo["start_minute"]))/($step_length * $time_step);
		// this seems to fuck up in reval room calendar view and only display time to 15:00
		//while($step < floor($steps))
		$room_id = $arr["obj_inst"]->id();
		$col_buffer = $settings->prop("col_buffer");
		$buffer_time_string = $settings->prop("buffer_time_string");
		$use_product_times = $arr["obj_inst"]->prop("use_product_times");
		$real_day_start = get_day_start($today_start);
		$base_add = $today_start - $real_day_start;


		enter_function("get_calendar_tbl::3");
		while($step < $day_end/($step_length * $time_step))
		{
			$d = $col = $ids = $rowspan = $onclick = array();
			if($this->multi || $this->extra_row)//kui mitu bronni v6ib olla, siis lisa v2ljad
			{
				$extra_d = array();
				$extra_col = array();
			}
			$x = 0;
			$start_step = $today_start + (int)($step * $step_length * $time_step);
			$end_step = $start_step + (int)($step_length * $time_step);
			$prev_dst = date("I", $start_step);
			$visible = 0;
			while($x<$len)
			{
				if (date("I", $start_step) > $prev_dst)
				{
					$start_step -= 3600;
					$end_step -= 3600;
					$prev_dst = date("I", $start_step);
				}
				else
				if (date("I", $start_step) < $prev_dst)
				{
					$start_step += 3600;
					$end_step += 3600;
					$prev_dst = date("I", $start_step);
				}
				if(!is_array($this->openhours) || $this->is_open($start_step,$end_step,($base_add + $step * $step_length * $time_step) >= (24*3600)))
				{
					$visible=1;
					$rowspan[$x] = 1;
//					if($this->check_if_available(array(
					if($this->check_if_available(array(
						"room" => $room_id,
						"start" => $start_step,
						"end" => $end_step,
						
					)) || ($this->multi &&  (!is_oid($this->last_bron_id) || (is_object($last_bron = obj($this->last_bron_id))  && $last_bron->prop("time_closed") != 1))))
					{
						if ($this->is_paused($start_step,$end_step))
						{
							if($col_buffer)
							{
								$col[$x] = "#".$col_buffer;
							}
							else
							{
								"#EE6363";
							}
							$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
							$d[$x] .= " <div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div>";
						}
						else
						{
							$arr["timestamp"] = $start_step;
							$prod_menu="";
							if($use_product_times)
							{
								$arr["menu_id"] = "menu_".$start_step."_".$room_id;
								$img_id = 'm_'.$room_id.'_'.$start_step;
								$prod_menu = '<a class="menuButton" href="javascript:void(0)" onclick="bron_disp_popup(\'bron_menu_'.$room_id.'\', '.$start_step.',\''.$img_id.'\');" alt="" title="" id=""><img alt="" title="" border="0" src="'.aw_ini_get("icons.server").'/class_.gif" id="'.$img_id.'" ></a>';
							}
							else
							if ($settings->prop("bron_popup_immediate") && is_admin())
							{
								if ($settings->prop("bron_popup_detailed"))
								{
									$url = html::get_new_url(CL_RESERVATION, $b_parent, array(
										"return_url" => get_ru(),
										"calendar" => $cal,
										"resource" => $room_id,
										"ver" => $si->get_verified_default_for_group($settings),
									));
									$w = 1000;
									$h = 600;
								}
								else
								{
									$url = $this->mk_my_orb("admin_add_bron_popup", array(
										"parent" => $b_parent,
										"calendar" => $cal,
										"resource" => $room_id,
										"return_url" => get_ru(),
									));
									$w = 500;
									$h = 400;
								}
	
								$onclick[$x] = "doBronExec('".$room_id."_".$start_step."', ".($step_length * $time_step).",null,null,'$url', '$w', '$h', 0)";
							}
							else
							{
								$onclick[$x] = "doBron('".$room_id."_".$start_step."' , ".($step_length * $time_step).")";
								//$string = t("VABA");
							}
	
							if (!$this->group_can_do_bron($settings, $start_step))
							{
								$onclick[$x] = "";
								$prod_menu = "";
							}
							$val = 0;
							$string = $settings->prop("available_time_string")?$settings->prop("available_time_string") :t("VABA");
			
							$col[$x] = $arr["obj_inst"]->get_color("available");
/*							if(is_array($this->other_rooms) && sizeof($this->other_rooms) && $settings->prop("col_slave") != "")
							{
								foreach($other_room_ol->arr() as $oro)
								{//arr($oro);arr(date("d.m h:i" , $start_step)); arr(date("d.m h:i" , $end_step));
									if(!$oro->is_available(array(
										"start" => $start_step,
										"end" => $end_step,
									)))
									{
										$col[$x] = "#".$settings->prop("col_slave"); 
									}
								}
							}*/
							if($_SESSION["room_reservation"][$room_id]["start"]<=$start_step && $_SESSION["room_reservation"][$room_id]["end"]>=$end_step)
							{
								//teeb selle kontrolli ka , et 2kki tyybid yltse teist ruumi tahavad juba... et siis l2heks sassi
								if(!$_SESSION["room_reservation"]["room_id"] || $_SESSION["room_reservation"]["room_id"] == $room_id || in_array($room_id, $_SESSION["room_reservation"]["room_id"]))
								{
									$val = 1;
									$col[$x] = "red";
									$string = t("Broneeri");
								}
							}
							$d[$x] = "<span>".$string."</span>".html::hidden(array("name"=>'bron['.$room_id.']['.$start_step.']' , "value" =>$val)). " " . $prod_menu;
						}
					}
					else
					{
						if(is_oid($this->last_bron_id) && !$arr["web"])
						{
							$last_bron = obj($this->last_bron_id);
							$d[$x] = $this->get_bron_cell_html($last_bron, $settings, $start_step);
							$col[$x] = $this->get_bron_cell_color($last_bron , $settings,$start_step);

						//seda praega ei kasuta, kuid 2kki l2heb vaja
/*							if(($last_bron->prop("end") - $start_step) / ($step_length * $time_step) >= 1)
							{
								$rowspan[$x] = (int)((
									$last_bron->prop("end")
									+ $this->get_after_buffer(array("room" => $arr["obj_inst"], "bron" => $last_bron))
									 - $start_step)
									 / ($step_length * $time_step)) ;
								if((($last_bron->prop("end")+$this->get_after_buffer(array("room" => $arr["obj_inst"], "bron" => $last_bron)) - $start_step) % ($step_length * $time_step)))
								{
									$rowspan[$x]++;
								}
							}
*/
							//$d[$x] = "<table border='1' style='width: 100%; height: 100%'><tr><td>".$d[$x]."</td></tr></table>";


						}
						else
						if($this->is_buffer && !$arr["web"])
						{
							if($col_buffer)
							{
								$col[$x] = "#".$col_buffer;
							}
							else
							{
								"#EE6363";
							}
							$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
							$d[$x] .= " <div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div>";	//	if (aw_global_get("uid") == "struktuur") {arr($buffer_time_string);}
						}
						else
						{
							$col[$x] = "#EE6363";
					 		$d[$x] ="<span><font color=#26466D>".$settings->prop("reserved_time_string")?$settings->prop("reserved_time_string") :t("BRON")."</FONT></span>";
						}
						$onclick[$x] = "";
					}
				}
				else
				{
					$d[$x] = "<span>".$settings->prop("closed__time_string")?$settings->prop("closed__time_string") :t("Suletud")."</span>";
				}
				//$ids[$x] = $arr["room"]."_".$start_step;
				$ids[$x] = $room_id."_".$start_step;

				//kui mitu lubatud, hakkab siia lisaks veel panema asju....
				if($this->multi)
				{
					
					enter_function("room::multi_fields");
	//				arr(date("d.m.Y h:i" , $start_step));
					$time_reservations = $arr["obj_inst"]->get_time_reservations($start_step , $end_step);
					foreach($time_reservations as $key => $time_reservation)
					{
						//yritab aru saada millisesse tulpa l2heb asi... ei ikka eelneva alla
						if(isset($time_reservation["col"]))
						{
							$col = $time_reservation["col"];
						}
						else
						{
							$col = $key;
						}
						$extra_d[$x."_".$col] = $this->get_bron_cell_html(obj($time_reservation["id"]), $settings,$start_step);
						$extra_col[$x."_".$col] = $this->get_bron_cell_color(obj($time_reservation["id"]), $settings,$start_step);
					}
					exit_function("room::multi_fields");
				}

				//kole asi, kuid siia tuleb lisaks toitlustuse ja muud tyypi broneeringud
				if($this->extra_row)
				{
					enter_function("room::extra_row");
					$xtr = $this->get_extra_res_from_table($start_step , $end_step);

					if($xtr)
					{
						$this->is_after_buffer = 0;
						$extra_d[$x."_0"] = $this->get_bron_cell_html(obj($xtr), $settings,$start_step);
						$extra_col[$x."_0"] = $this->get_bron_cell_color(obj($xtr), $settings,$start_step);
					}
					exit_function("room::extra_row");
				}

				$x++;
				$start_step += 86400;
				$end_step += 86400;
//				$today_start += 86400;
			}
			if($visible)
			{
				$tmp_row_data = array(
					"time" => '<a name="time_'.date("H" , $today_start+ $step*$step_length*$time_step).'"></a>'.date("G:i" , $today_start+ $step*$step_length*$time_step),
					"time_col" => "#BADBAD"
				);
				for($i = 0; $i < $len; $i++)
				{
					$tmp_row_data["d".$i] = $d[$i];
					$tmp_row_data["id".$i] = $ids[$i];
					$tmp_row_data["onclick".$i] = $onclick[$i];
					$tmp_row_data["col".$i] = $col[$i];
			//		$tmp_row_data["rowspan".$i] = $rowspan[$i];

				}

				if($this->multi || $this->extra_row)
				{
					foreach($extra_d as $key => $val)
					{
						$tmp_row_data["d".$key] = $val;
						$tmp_row_data["col".$key] = $extra_col[$key];
					}
				}

				$t->define_data($tmp_row_data);
				$num_rows++;
			}
			$step = $step + 1;
		}
		if ($num_rows > 5)
		{
			$t->set_lower_titlebar_display(true);
		}
		
		if(!$arr["web"] && $settings->prop("show_workers_in_calander"))
		{
			$t->define_data($this->get_day_workers_row($arr["obj_inst"]));
		}
		if(!$arr["web"] && !$settings->prop("dont_show_day_sum_in_calander"))
		{
			$t->define_data($this->get_sum_row($arr["obj_inst"]));
		}
		exit_function("get_calendar_tbl::3");
		//$t->set_rgroupby(array("group" => "d2"));
		$arr["settings"] = $settings;

		$popup_menu = $this->get_room_prod_menu($arr, ($settings->prop("bron_popup_immediate") && is_admin()));
		$this->popup_menu_str = $popup_menu;
		$t->set_caption(t("Broneerimine").$popup_menu);
	}
	
	private function get_bron_cell_color($last_bron , $settings,$start_step)
	{//if($last_bron->id() == 415994) arr($last_bron->prop("type"));
		enter_function("room::get_bron_cell_color");
		$col = array();
		$x = 1;
		//kui on alambronn... kas siis ruumil mis on seotud teise ruumiga kust broneeritakse, v6i siis ruumil kust broneeritakse juhul kui on broneering m6nes seotud olevas ruumis

		if ($settings->prop("col_recent") != "" && time() < ($last_bron->modified()+30*60))
		{
			$col[$x] = "#".$settings->prop("col_recent"); 
		}
		else
		if ($last_bron->prop("time_closed") == 1)
		{
			$col[$x] = "#".$settings->prop("col_closed");
			$d[$x] .= " ".$last_bron->prop("closed_info");
		}
		else
		if($last_bron->prop("verified"))
		{//arr($last_bron->prop("type") == "food" && $settings->prop("col_food") != "");
			if($settings->prop("col_slave") != ""  && ((is_array($this->other_rooms) && in_array($last_bron->prop("resource") , $this->other_rooms)) || $last_bron->is_lower_bron()))
			{
				$col[$x] = "#".$settings->prop("col_slave"); 
			}
			else
			if($last_bron->prop("type") == "food" && $settings->prop("col_food") != "")
			{
				$col[$x] = "#".$settings->prop("col_food");
			}
			else
			{
				$col[$x] = $this->get_colour_for_bron($last_bron, $settings);
			}
		}
		else
		{
			$rfp_color = "";
			if(is_object($rfp = $last_bron->get_rfp()))
			{
				switch($rfp->prop("confirmed"))
				{
					case 1:
						if($settings->prop("col_slave") != ""  &&  ((in_array($last_bron->prop("resource") , $this->other_rooms)) || $last_bron->is_lower_bron()))
						{
							 $rfp_color = $settings->prop("col_slave"); 
						}
						elseif($settings->prop("col_sent") != "")
						{
							$rfp_color = $settings->prop("col_sent");
						}
						break;
					case 3:
						if($settings->prop("col_slave") != ""  &&  ((is_array($this->other_rooms) && in_array($last_bron->prop("resource") , $this->other_rooms)) || $last_bron->is_lower_bron()))
						{
							 $rfp_color = $settings->prop("col_slave");
						}
						elseif($settings->prop("col_on_hold") != "")
						{
							$rfp_color = $settings->prop("col_on_hold");
						}//arr($last_bron); arr($last_bron->is_lower_bron());
						break;
					case 4:
						if($settings->prop("col_back") != "")
						{
							$rfp_color = $settings->prop("col_back");
						}
						break;
					case 5:
						if($settings->prop("col_unverified") != "")
						{
							$rfp_color = $settings->prop("col_unverified");
						}
						break;
				}

			}

			if($rfp_color)
			{
				$col[$x] = "#".$rfp_color;
			}
			else
			{
				$col[$x] = $settings->prop("col_web_halfling") != "" ? "#".$settings->prop("col_web_halfling") : "#FFE4B5";
			}
		}
		if($this->is_after_buffer)
		{
			if($col_buffer)
			{
				$col[$x] = "#".$col_buffer;
			}
			else
			{
				"#EE6363";
			}
		}
		exit_function("room::get_bron_cell_color");
		return $col[$x];
	}

	private function __bp_sort($a, $b)
	{
		return $a["jrk"] - $b["jrk"];
	}

	private function set_calendar_bron_props($settings)
	{
		if(!is_array($this->calendar_bron_props))
		{
			$this->calendar_bron_props = array();
			$calendar_bron_props = $settings->meta("calendar_bron_props");
			uasort($calendar_bron_props, array(&$this, "__bp_sort"));
			foreach($calendar_bron_props as $prop => $cbp)
			{
				if($cbp["text"])
				{
					$this->calendar_bron_props["text"][$prop] = array(
						"before" => $cbp["before"],
						"after" => $cbp["after"],
					);
				}
				if($cbp["alt"])
				{
					$this->calendar_bron_props["alt"][$prop] = array(
						"before" => $cbp["before"],
						"after" => $cbp["after"],
					);
				}
			}
		}
	}

	private function get_bron_cell_html($last_bron, $settings,$start_step)
	{
		enter_function("room::get_bron_cell_html");
		$d = array();
		$x = 1;
		$cus = $settings->prop("reserved_time_string")?$settings->prop("reserved_time_string") :t("BRON");
		if($last_bron->prop("time_closed") && $settings->prop("closed_time_string"))
		{
			$cus = $settings->prop("closed_time_string");
		}
		$title = $phone = "";
		$imgstr = "";
		$codes = array();

		$this->set_calendar_bron_props($settings);
		if(!$_GET["no_det_info"] && sizeof($this->calendar_bron_props))//nii on uus systeem
		{
			$new_system = 1;
			$cus = "";
			$after = "";
			foreach($this->calendar_bron_props["text"] as $prop => $val)
			{
				$rcp = $last_bron->get_room_calendar_prop($prop , &$settings);
				if($rcp)
				{
					$cus.= $after.$rcp.$val["before"];
					$after= $val["after"];
				}
			}
			foreach($this->calendar_bron_props["alt"] as $prop => $val)
			{
				$rcp = $last_bron->get_room_calendar_prop($prop , &$settings);
				if($rcp)
				{
					$title.= $after.$rcp.$val["before"];
					$after = $val["after"];
				}
			}
		}
		elseif (!$_GET["no_det_info"])
		{
			$last_cust = $last_bron->prop("customer");
			if ($this->can("view", $last_cust))
			{
				$customer = obj($last_cust);
				$cus = array();
				foreach($last_bron->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
				{
					$cus[] = $c->prop("to.name")." ";
				}
				$cus = join(", ", $cus);
				//$cus = $customer->name();
				$phone = $customer->prop("phone.name");
				$phones = array();
				if(!$phone)
				{
					$conns = $customer->connections_from(array(
						"type" => "RELTYPE_PHONE",
					));//arr($customer);
					foreach($conns as $conn)
					{
						$phones[] = $conn->prop("to.name");
					};
					$phone = join (", " , $phones);
				}
				$products = $last_bron->meta("amount");
				$title = $last_bron->prop("content");
				if($last_bron->prop("comment"))
				{
					if(!$settings->prop("comment_pos"))
					{
						$title.=", ".$last_bron->prop("comment");
					}
					if($settings->prop("comment_pos") == 1)
					{
						$cus = join(", ", array($cus," /".$last_bron->prop("comment"). "/"));
					}
				}
				foreach($products as $prod => $val)
				{
					if($val)
					{
						if($this->can("view" , $prod))
						{
							$product = obj($prod);
							if($product->prop("code"))
							{
								$codes[] = $product->prop("code");
							}
							$title .= " ".$product->name();
							if ($settings->prop("cal_show_prod_img"))
							{
								// if this is a packaging, then get the product for it
								if ($product->class_id() == CL_SHOP_PRODUCT_PACKAGING)
								{
									$_conns = $product->connections_to(array("from.class_id" => CL_SHOP_PRODUCT));
									if (count($_conns))
									{
										$_con = reset($_conns);
										$product = $_con->from();
									}
								}
								$cons = $product->connections_from(array(
									"type" => "RELTYPE_IMAGE",
									"to.jrk" => $settings->prop("cal_show_prod_img_ord")
								));
								if (count($cons))
								{
									$con = reset($cons);
									if ($con)
									{
										$ii = get_instance(CL_IMAGE);
										$imgstr .= $ii->make_img_tag_wl($con->prop("to"));
									}
								}
							}
						}
					}
				}
			}
		}

		if($_GET["no_det_info"] || !$new_system)
		{
			$bron_name = $cus . ( $phone ? (" , ".$phone):"" )."</u> " . join($codes , ",");
		}
		else
		{
			$bron_name = $cus;
		}

		if(is_array(($_t = $_GET["alter_reservation_name"])) and is_array(aw_ini_get("classes.".$_t["class_id"])))
		{
			$cb_inst = get_instance($_t["class_id"]);
			if(is_callable(array($cb_inst, $_t["action"])))
			{
				$_t["name_elements"] = array(
					"customer" => $cus,
					"phone" => $phone,
					"codes" => $codes,
				);
				$_t["bron_name"] = &$bron_name;
				$_t["reservation"] = $last_bron;
				$cb_inst->$_t["action"]($_t);
			}
		}
		$dx_p = array(
			"caption" => "<span><font color=#26466D><u>".$bron_name."</FONT></span>",
			"title" => $title,
		);
		if ($settings->prop("cal_show_prods") && !$new_system)
		{
			$dx_p["caption"] .= " <b>".$title."</b>";
		}
		if ($settings->prop("bron_no_popups"))
		{
			$dx_p["url"] = html::get_change_url($last_bron->id(),array("return_url" => get_ru(),));
			$d[$x] = html::href($dx_p);
		}
		else
		{
			$dx_p["width"] = 800;
			$dx_p["height"] = 600;
			$dx_p["scrollbars"] = 1;
			$dx_p["href"] = "#";
			$dx_p["url"] = $this->mk_my_orb("change", array("id" => $last_bron->id(), "return_url" => get_ru()), "reservation");
			$d[$x] = html::popup($dx_p);
		}
//if($last_bron->id() == 155366){ arr($dx_p);arr($d); die();}
		if ($last_bron->prop("send_bill"))
		{
			$d[$x] = html::href(array(
				"url" => "javascript:;",
				"caption" => html::img(array(
				"url" => aw_ini_get("baseurl")."/automatweb/images/icons/create_bill.jpg",
				"border" => 0
				)),
				"title" => t("Saata arve")
			))." ".$d[$x];
		}
		$d[$x] .= " ";
		if ($last_bron->prop("client_arrived") == 1)
		{
			$d[$x] .= html::href(array(
				"caption" => "+",
				"url" => "#",
				"title" => t("Klient saabus"),
				"onClick" => "aw_get_url_contents(\"".$this->mk_my_orb("set_bron_cust_arrived_status", array("bron" => $last_bron->id(), "status" => 2))."\");"
			));
		}
		else
		if ($last_bron->prop("client_arrived") == 2)
		{
			$d[$x] .= html::href(array(
				"caption" => "-",
				"url" => "#",
				"title" => t("Klient ei saabunud"),
				"onClick" => "aw_get_url_contents(\"".$this->mk_my_orb("set_bron_cust_arrived_status", array("bron" => $last_bron->id(), "status" => 1))."\");"
			));
		}
		$b_len = $last_bron->prop("end") - $last_bron->prop("start1");
			if ($col_buffer != "")
			{
				$buf_tm = sprintf("%02d:%02d", floor($b_len / 3600), ($b_len % 3600) / 60);
				$d[$x] .= " ".$buf_tm;
			}

/*
			if ($settings->prop("col_recent") != "" && time() < ($last_bron->modified()+30*60))
			{
			}
			else
			if ($last_bron->prop("time_closed") == 1)
			{
				$d[$x] .= " ".$last_bron->prop("closed_info");
			}
			if ($last_bron->prop("content") != "" || $last_bron->comment() != "")
			{
				$d[$x] .= html::href(array(
					"url" => "#",
					"caption" => "*",
					"title" => $last_bron->prop("content")." ".$last_bron->comment()
				));
			}
			if ($imgstr != "")
			{
				$d[$x] .= "<br>".$imgstr;
			}
			if($this->is_after_buffer)
			{
				$d[$x] = ""; 
			}
			if($this->is_after_buffer)
			{
				$d[$x] = ""; 
			}
			elseif($last_bron->prop("start1") < $start_step)
			{
				if ($settings->prop("bron_no_popups"))
				{
					$d[$x] = html::href(array(
						"url" => $dx_p["url"],
						"caption" => "--//--"
					));
				}
				else
				{
					$dx_p["caption"] = "--//--";
					$d[$x] = html::popup($dx_p);
				}
			}
			if($this->is_before_buffer)
			{
				$d[$x] = ""; 
				if($col_buffer)
				{
					$col[$x] = "#".$col_buffer;
				}
				else
				{
					"#EE6363";
				}
			}
			if ($col_buffer != "")
			{
				$buf = $this->get_before_buffer(array(
					"room" => $arr["obj_inst"],
					"bron" => $last_bron,
				));
				if ($buf > 0 && ($last_bron->prop("start1") > $start_step))
				{
					$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
					$d[$x] = "<div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div><div style='padding-left: 5px; height: 90%'>".$d[$x]."</div>";
				}
				$buf = $this->get_after_buffer(array(
					"room" => $arr["obj_inst"],
					"bron" => $last_bron,
				));
				if ($buf > 0 && ($last_bron->prop("end") < $end_step))
				{
					$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
					$d[$x] .= " <div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div>";
				}
			}
*/
						if ($settings->prop("col_recent") != "" && time() < ($last_bron->modified()+30*60))
							{
							}
							else
							if ($last_bron->prop("time_closed") == 1)
							{
								$d[$x] .= " ".$last_bron->prop("closed_info");
							}
							if ($last_bron->prop("content") != "" || $last_bron->comment() != "")
							{
								$d[$x] .= html::href(array(
									"url" => "#",
									"caption" => "*",
									"title" => $last_bron->prop("content")." ".$last_bron->comment()
								));
							}

							if ($imgstr != "")
							{
								$d[$x] .= "<br>".$imgstr;
							}
							if($this->is_after_buffer)
							{
								$d[$x] = ""; 
							}
							elseif($last_bron->prop("start1") < $start_step)
							{
								if ($settings->prop("bron_no_popups"))
								{
									$d[$x] = html::href(array(
										"url" => $dx_p["url"],
										"caption" => "--//--"
									));
								}
								else
								{
									$dx_p["caption"] = "--//--";
									$d[$x] = html::popup($dx_p);
								}
							}
							if($this->is_before_buffer)
							{
								$d[$x] = ""; 
								if($col_buffer)
								{
									$col[$x] = "#".$col_buffer;
								}
								else
								{
									"#EE6363";
								}
							}

							if ($col_buffer != "")
							{
								$buf = $this->get_before_buffer(array(
									"room" => $arr["obj_inst"],
									"bron" => $last_bron,
								));
								if ($buf > 0 && ($last_bron->prop("start1") > $start_step))
								{
									$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
									$d[$x] = "<div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div><div style='padding-left: 5px; height: 90%'>".$d[$x]."</div>";
								}

								$buf = $this->get_after_buffer(array(
									"room" => $arr["obj_inst"],
									"bron" => $last_bron,
								));
								if ($buf > 0 && ($last_bron->prop("end") < $end_step))
								{
									$buf_tm = sprintf("%02d:%02d", floor($buf / 3600), ($buf % 3600) / 60);
									$d[$x] .= " <div style='position: relative; left: -7px; background: #".$col_buffer."'>".$buffer_time_string." ".$buf_tm."</div>";
								}
							}
		exit_function("room::get_bron_cell_html");
		return $d[$x];
	}

	function get_day_workers_row($o)
	{
		$res = array();
		$x = 0;
		$time = $this->start;
		$res["time"] = t("t&ouml;&ouml;tajad:");
		while($x < $this->len)
		{
			$workers = $this->get_day_workers($o , $time);
			foreach($workers->arr() as $worker)
			{
				$sum = $o->get_person_day_sum($time,$worker->id());
				$result = "";
				foreach($sum as $curr => $s)
				{
					if(is_oid($curr))
					{
						$currency = obj($curr);
						$result.= $currency->name(). ": ".$s."\n<br>";
					}
				}
				$res["d".$x].= $worker->name()."<br>\n".$result;
			}
			$time = $time + 24*60*60;
			$x++;
		}
		return $res;
	}
	
	function get_sum_row($o)
	{
		$res = array();
		$x = 0;
		$time = $this->start;
		$res["time"] = t("Summa kokku:");
		while($x < $this->len)
		{
			$sum = $o->get_day_sum($time);
			$result = "";
			foreach($sum as $curr => $s)
			{
				if(is_oid($curr))
				{
					$currency = obj($curr);
					$result.= $currency->name(). ": ".$s."\n<br>";
				}
			}
			$res["d".$x].= $result;
			if($this->multi)
			{
//				$res["colspan".$x] = $this->calendar_colspan[$x];
			}
			$time = $time + 24*3600;
			$x++;
		}
		return $res;
	}

	function get_room_prod_menu($arr, $immediate = false)
	{
		$menus = $arr["obj_inst"]->meta("group_product_menu");
		$settings = $arr["settings"];

		if(is_object($cal_obj = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
		{
			$cal = $cal_obj->id();
			$b_parent = $cal_obj->prop("event_folder");
			if (!$b_parent)
			{
				$b_parent = $cal_obj->id();
			}
		}


		$last_parent = 0;
		$parents = array();
		$div = "";
		$res = '<div class="menu" id="bron_menu_'.$arr["obj_inst"]->id().'" style="display: none;">';
		if($menus)
		{
			$res = '<div id="bron_menu_'.$arr["obj_inst"]->id().'" class="menu" onmouseover="menuMouseover(event)">';
		}
		$m_oid = $arr["obj_inst"]->id();
		$this->prod_data = $this->get_prod_data_for_room($arr["obj_inst"]);
		$item_list = $this->get_active_items($arr["obj_inst"]->id());
		$item_list->arr();	//optimization to fetc all at once
//		$prod_list = $item_list->names();
		$times = array();
		foreach($item_list->names() as $oid => $pname)
		{
			$times[$oid] = $this->cal_product_reserved_time(array("id" => $m_oid, "oid" => $oid , "room" => $arr["obj_inst"]->id()));
		}
		foreach($item_list->arr() as $product)
		{
			//$product = obj($oid);
			$name = $product->name();
			$parent = $product->parent();
	//		$parents[$parent][] = $oid;

			if($menus && ($last_parent != $parent))
			{
				if($last_parent)
				{
					$div.= '</div>';
				}
				$last_parent = $parent;
				$p = obj($parent);

				$res.= '<a class="menuItem" href="" onclick="return false;" onmouseover="menuItemMouseover(event, '.$parent.');">
					<span class="menuItemText">'.$p->trans_get_val().'</span>
					<span class="menuItemArrow"><img style="border:0px" src="'.aw_ini_get("baseurl").'/automatweb/images/arr.gif" alt=""></span>
					</a>';
				$div.= '<div id="'.$parent.'" class="menu" onmouseover="menuMouseover(event)">';
			}


			if ($settings->prop("bron_popup_detailed"))
			{
				$url = html::get_new_url(CL_RESERVATION, $b_parent, array(
					"return_url" => get_ru(),
					"calendar" => $cal,
					"resource" => $arr["obj_inst"]->id(),
					"product" => $product->id(),
				));
				$w = 1000;
				$h = 600;
			}
			else
			{
				$url = $this->mk_my_orb("admin_add_bron_popup", array(
	                                "parent" => $b_parent,
                                	"calendar" => $cal,
        	                        "resource" => $arr["obj_inst"]->id(),
	                                "return_url" => get_ru(),
                                	"product" => $product->id(),
                        	));
				$w = 500;
				$h = 400;
			}

			$func = ($immediate? "doBronExec" : "doBron");
			$oncl = $func.'(
				\''.$m_oid.'_\'+current_timestamp ,
				'.$arr["step_length"].' ,
				'.$times[$oid].' ,
				'.$oid.
				',\''.$url.'\', '.$w.', '.$h.', '.((int)$settings->prop("bron_no_popups")).');';
			$res .= '<div id="'.$oid.'"><a class="menuItem" href="#"  onClick="'.$oncl.'">'.$name.'</a></div>';
		}
		
		$div.='</div>';
		$res.='</div>';
		if($menus) $res .=$div;
		return $res;
	}
	
	
	function get_colour_for_bron($bron, $settings)
	{
		$gc = $settings->meta("grp_cols");
		if (!is_array($gc) || count($gc) == "")
		{
			return "#EE6363"; //#FFE4B5";
		}

		static $cache;
		if (isset($cache[$bron->createdby()]))
		{
			return $cache[$bron->createdby()];
		}

		$u = get_instance(CL_USER);
		$grp = $u->get_highest_pri_grp_for_user($bron->createdby(), true);
		if ($grp && !empty($gc[$grp->id()]))
		{
			$rv = "#".$gc[$grp->id()];
		}
		else
		{
			$rv = "#EE6363";
		}
		$cache[$bron->createdby()] = $rv;
		return $rv;
	}
	
	function get_after_buffer($arr)
	{
		extract($arr);
		if(!is_object($room))
		{
			return 0;
		}

		static $cache;
		if (isset($cache[$room->id()][$bron->id()]))
		{
			return $cache[$room->id()][$bron->id()];
		}		

		if(is_object($room) && $room->prop("use_product_times") && is_object($bron))
		{
			$rv = $this->get_products_buffer(array("bron" => $bron, "time" => "after"));
		}
		elseif(is_object($room))
		{
			$rv = $room->prop("buffer_after")*$room->prop("buffer_after_unit");
		}
		$cache[$room->id()][$bron->id()] = $rv;
		return $rv;
	}
	
	function get_before_buffer($arr)
	{
		extract($arr);
		if(!is_object($room))
		{
			return 0;
		}
		
		static $cache;
		if (isset($cache[$room->id()][$bron->id()]))
		{
			return $cache[$room->id()][$bron->id()];
		}

		if(is_object($room) && $room->prop("use_product_times") && is_object($bron))
		{
			$rv = $this->get_products_buffer(array("bron" => $bron, "time" => "before"));
		}
		elseif(is_object($room))
		{
			$rv = $room->prop("buffer_before")*$room->prop("buffer_before_unit");
		}
		$cache[$room->id()][$bron->id()] = $rv;
		return $rv;
	}
		
	function get_when_opens($start, $end)
	{
		if(!$this->open_inst)
		{
			$this->open_inst = get_instance(CL_OPENHOURS);
		}
		$start_hour = 24;
		$start_minute = 60;
		foreach($this->openhours as $oh)
		{
			if ($oh->prop("date_from") > 100 && $oh->prop("date_from") > $end)
			{
				continue;
			}
			if ($oh->prop("date_to") > 100 && $oh->prop("date_to") < $start)
			{
				continue;
			}
			$opens = $this->open_inst->get_opening_time($oh);
			$start_hour = min($opens["hour"], $start_hour);
			$start_minute = min($opens["minute"], $start_minute);
		}
//		$opens = $this->open_inst->get_opening_time(reset($this->openhours));
		return array("start_hour" => /*$opens["hour"]*/ $start_hour, "start_minute" => /*$opens["minute"]*/$start_minute);
	}

	function is_paused($start, $end)
	{
		if(!$this->open_inst)
		{
			$this->open_inst = get_instance(CL_OPENHOURS);
		}

		$end_this = (date("H" , $end-1)*3600 + date("i" , $end-1)*60);
		$start_this = (date("H" , $start)*3600 + date("i" , $start)*60);
		
		//kontrollib et tsykli l6pp 2kki l2heb j2rgmisesse p2eva juba... siis oleks l6pp kuidagi varajane ja avatud oleku kontroll l2heks puusse
		if($start_this > $end_this)
		{
			$end_this+=24*3600;
		}
		if ($this->pauses)
		{
			foreach($this->pauses as $pause)
			{
				if ($pause->prop("date_from") > 100 && $start < $pause->prop("date_from"))
				{
					continue;
				}
				if ($pause->prop("date_to") > 100 && $start > $pause->prop("date_to"))
				{
					continue;
				}

				$pauses = $this->open_inst->get_times_for_date($pause, $start);
				if(
					is_array($pauses) && 
					($pauses[0] || $pauses[1]) && 
//					($pauses[1]-1 >= $end_this) && 
//					($pauses[0] <= $start_this)
					(
						($pauses[0] < $end_this && $pauses[1] > $start_this)
//						|| 
//						($pauses[1] < $end_this && $pauses[1] > $start_this)
					)
				)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	function is_open($start,$end, $next_day = false)
	{
		if(!$this->open_inst)
		{
			$this->open_inst = get_instance(CL_OPENHOURS);
		}
		$end_this = (date("H" , $end-1)*3600 + date("i" , $end-1)*60);
		$start_this = (date("H" , $start)*3600 + date("i" , $start)*60);
		
		//kontrollib et tsykli l6pp 2kki l2heb j2rgmisesse p2eva juba... siis oleks l6pp kuidagi varajane ja avatud oleku kontroll l2heks puusse
		if($start_this > $end_this)
		{
			$end_this+=24*3600;
		}
		foreach($this->openhours as $oh)
		{
			if ($oh->prop("date_from") > 100 && $start < $oh->prop("date_from"))
			{
				continue;
			}
			if ($oh->prop("date_to") > 100 && $start > $oh->prop("date_to"))
			{
				continue;
			}
			$open = $this->open_inst->get_times_for_date($oh, $start);

			// this monster here makes sure that open times that cross the midnight (21:00 - 03:00 for example) work 
			if ($next_day &&
				is_array($open) && 
				($open[0] || $open[1]) && 
				($open[1]-1 >= $end_this) &&
				($open[0] > $open[1])
			)
			{
				return true;
			}
			else
			if(
				is_array($open) && 
				($open[0] || $open[1]) && 
				($open[1] > $open[0] ? $open[1]-1 >= $end_this : $open[1]-1 <= $end_this) && 
				($open[0] <= $start_this)
			)
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

	function get_prod_menu($arr, $immediate = false)
	{
		enter_function("get_calendar_tbl::get_prod_menu");
		$m_oid = $arr["obj_inst"]->id();
		static $prod_list;
		static $times;
		if ($prod_list == null)
		{
			$this->prod_data = $this->get_prod_data_for_room($arr["obj_inst"]);
			$item_list = $this->get_active_items($arr["obj_inst"]->id());
			$prod_list = $item_list->names();
			$times = array();
			foreach($prod_list as $oid => $pname)
			{
				$times[$oid] = $this->cal_product_reserved_time(array("id" => $m_oid, "oid" => $oid, "room" => $arr["obj_inst"]->id()));
			}
		}
		$ret = "";
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu($arr["menu_id"]);
		foreach($prod_list as $oid => $name)
		{
			$pm->add_item(array(
				"text" => $name,
				"link" => "javascript:dontExecutedoBron=1;void(0)",
				"onClick" => ($immediate? "doBronExec" : "doBron")."(
					'".$m_oid."_".$arr["timestamp"]."' ,
					".$arr["step_length"]." ,
					".$times[$oid]." ,
					".$oid.");",
			),"CL_ROOM");
		}

		$ret.= $pm->get_menu(array(
			"icon" => icons::get_icon_url($package),
			//"icon" =>aw_ini_get("baseurl")."/automatweb/images/vaba.gif",
		));
		exit_function("get_calendar_tbl::get_prod_menu");
		return $ret;
	}

	function is_open_day($time)
	{
		if(!is_array($this->openhours))
		{
			return true;
		}
		if(!$this->open_inst)
		{
			$this->open_inst = get_instance(CL_OPENHOURS);
		}

		foreach($this->openhours as $oh)
		{
			if ($oh->prop("date_from") > 100 && $time < $oh->prop("date_from"))
			{
				continue;
			}
			if ($oh->prop("date_to") > 100 && $time > $oh->prop("date_to"))
			{
				continue;
			}
			$open = $this->open_inst->get_times_for_date($oh, $time);
			if($open[0] || $open[1])
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}

	function _init_calendar_t(&$t,$time=0, $len = 7)
	{
		if(!$time)
		{
			$time = time();
		}
	
		$op_len = 0;
		for($i = 0; $i < $len; $i++)
		{
			$tm = $time+$i*24*3600;
			if($this->is_open_day($tm))
			{
				$op_len++;
			}
		}
		$pct = floor(100 / ($op_len));

		//kui ruume tuleb vaates palju, siis oleks targem v2iksemaks teha, kuigi noh, juhul kui neid nagu v2ga palju, siis ei kujuta ettegi kuda v2lja hakkab n2gema
		if($this->room_count > 1)
		{
			$pct = (int)($pct / $this->room_count);
		}

		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
//			"width" => $pct."%",
			"chgbgcolor" => "time_col"
		));
		
		for($i = 0; $i < $len; $i++)
		{
			$tm = $time+$i*24*3600;
			if($this->is_open_day($tm))
			{
				$t->define_field(array(
					"name" => "d".$i,
					"caption" => substr(ucwords(aw_locale::get_lc_weekday(date("w",$tm))) , 0 , 1).date(" d/m/y" , $tm),// d/m/Y", $tm)//date("l d/m/Y", $tm),
					"width" => $pct."%",
					"chgbgcolor" => "col".$i,
					"id" => "id".$i,
					"onclick" => "onclick".$i,
					"rowspan" => "rowspan".$i,
	//				"colspan" => "colspan".$i,
				));
				if($this->multi && is_oid($this->id))
				{
					$room = obj($this->id);
					$max = $room->get_max_reservations_atst($tm , $tm + 24*3600 , !($this->show_unverified));
					//$this->calendar_colspan[$i] = $max + 1;
					$x = 0;
					while ($x < $max)
					{
						$t->define_field(array(
							"name" => "d".$i."_".$x,
//							"caption" => substr(ucwords(aw_locale::get_lc_weekday(date("w",$tm))) , 0 , 1).date(" d/m/y" , $tm),// d/m/Y", $tm)//date("l d/m/Y", $tm),
							"width" => $pct."%",
							"chgbgcolor" => "col".$i."_".$x,
							"id" => "id".$i."_".$x,
//							"onclick" => "onclick".$i,
//							"rowspan" => "rowspan".$i,
						));
						$x++;
					}
				}
				elseif($this->extra_row)
				{
					$room = obj($this->id);
					if($room->has_extra_row($tm , $tm + 24*3600))
					{
						$t->define_field(array(
							"name" => "d".$i."_0",
							"width" => $pct."%",
							"chgbgcolor" => "col".$i."_0",
							"id" => "id".$i."_0",
						));
					}
				}
			}
		}
		
		$t->set_caption(t("Broneerimine"));
		$t->set_sortable(false);
	}

	//see ruumi sees tehes, eeldusel, et p2rast liigub edasi reserveerimise objekti vaatesse, kus valib asju... tregelt nyyd juba popup k6igepealt
	/**
		@attrib name=do_add_reservation params=name all_args=1
		@param id optional oid
			room id
		@param bron optional array
			keys are start timestamps
	**/
	function do_add_reservation($arr)
	{
		extract($arr);
		if(is_oid($arr["id"]))
		{
			foreach($bron as $room => $val)
			{
				$times = $this->_get_bron_time(array(
					"bron" => $val,
					"id" => $room,
					"room_reservation_length" => $room_reservation_length,
				));
				if ($times["start"])
				{
					$arr["id"] = $room;
					break;
				}
				if(!$arr["id"])
				{
					$arr["id"] = $room;
				}
			}
			extract($times);

			$room = obj($arr["id"]);
			if(is_object($room->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
			{
					$cal_obj = $room->get_first_obj_by_reltype("RELTYPE_CALENDAR");
					$cal = $cal_obj->id();
					$parent = $cal_obj->prop("event_folder");
					$step = $room->prop("time_step");
					if (!$parent)
					{
						$parent = $cal_obj->id();
					}
					
					
			}
		}
		$product = $arr["product"];

		if (!$parent)
		{
			$parent = $arr["id"];
		}

		$settings = $this->get_settings_for_room($room);
		if ($settings->prop("bron_popup_detailed"))
		{
			$url = html::get_new_url(CL_RESERVATION, $parent, array(
				"post_msg_after_reservation" => $arr["post_msg_after_reservation"],
				"return_url" => $arr["post_ru"],
				"start1" => $start,
				"calendar" => $cal,
				"end" => $end,
				"resource" => $arr["id"],
				"product" => $product,
			));
			$w = 1000;
			$h = 600;
		}
		else
		{
			$url = $this->mk_my_orb("admin_add_bron_popup", array(
				"post_msg_after_reservation" => $arr["post_msg_after_reservation"],
                                "parent" => $parent,
                                "calendar" => $cal,
                                "start1" => $start,
                                "end" => $end,
                                "resource" => $arr["id"],
                                "return_url" => $arr["post_ru"],
                                "product" => $product,
				"firstname" => $arr["firstname"],
				"lastname" => $arr["lastname"],
				"company" => $arr["company"],
				"phone" => $arr["phone"],
				"people_count" => $arr["people_count"],
                        ));
			$w = 500;
			$h = 400;
		}

		if ($settings->prop("bron_no_popups"))
		{
			header("Location: ".$url);
			die();
		}
		else
		{
			die("<script type='text/javascript'>
			window.open('$url','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=$h, width=$w');
			 
				window.location.href='".$arr["post_ru"]."';
			</script>");
		}
		
		return $this->mk_my_orb(
			"new",
			array(
				"parent" => $parent,
				"return_url" => get_ru(),
				"calendar" => $cal,
				"start1" => $start,
				"end" => $end,
				"resource" => $arr["id"],
				"return_url" => $arr["post_ru"],
			),
			CL_RESERVATION
		);
	}

	function get_settings_for_room($room)
	{
		static $cache;
		if (isset($cache[$room->id()]))
		{
			return $cache[$room->id()];
		}
		enter_function("room::get_settings_for_room");
		$si = get_instance(CL_ROOM_SETTINGS);
		$rv = $si->get_current_settings($room);
		if (!is_object($rv))
		{
			$rv = obj();
		}
		$cache[$room->id()] = $rv;
		exit_function("room::get_settings_for_room");
		return $rv;
	}
	
	/** makes a new reservation for room, or changes existing one
		@attrib name=make_reservation params=name all_args=1 api=1
		@param id required oid
			room id
		@param res_id optional oid
			reservationid, if set, changes reservation
		@param data required array
			propertys and stuff 
			products - array(product_id=> amount)
			start - int , reservation starts , timestamp
			end - int , reservation ends , timestamp
			comment - string
			people - int , number of people 
			name - string , contact persons name
			email - string , contact persons email
			phone - string, contact persons phone
			customer - boolean , If the person object already exists, then connect the booking to this person, if given 
			verified - boolean , if true, reservation is marked as verified
		@param not_verified optional type=int
		@param meta optional type=array
			Any key=>value paris given here, will be written to the objects metadata
		@param tpl optional type=string
			verification mail template
		
		@returns Reservation object id , if success
			else ""
	**/
	function make_reservation($arr)
	{
		extract($arr);
		if(is_oid($id) && $this->can("view", $id))
		{
			$room = obj($id);
			if(is_object($room->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
			{
					$cal_obj = $room->get_first_obj_by_reltype("RELTYPE_CALENDAR");
					$cal = $cal_obj->id();
					$parent = $cal_obj->prop("event_folder");
					$step = $room->prop("time_step");
				if (!$parent)
				{
			       		$parent = $cal_obj->id();
			      	}
			}
			else return "";
		}
		else return "";

		if($this->can("view", $res_id))
		{
			$reservation = obj($res_id);
			$reservation->set_name($room->name()." bron ".date("d:m:Y" ,$data["start"]));
			$reservation->set_parent($parent);
			$reservation->set_prop("deadline", (time() + 15*60));
			$reservation->set_prop("resource" , $room->id());
		}
		else
		{
			$reservation = new object();
			$reservation->set_class_id(CL_RESERVATION);
			$reservation->set_name($room->name()." bron ".date("d:m:Y" ,$data["start"]));
			$reservation->set_parent($parent);
			$reservation->set_prop("deadline", (time() + 15*60));
			$reservation->set_prop("resource" , $room->id());
//			$reservation->save();
		}

		if (is_array($arr["meta"]))
		{
			foreach($arr["meta"] as $meta_k => $meta_v)
			{
				$reservation->set_meta($meta_k, $meta_v);
			}
		}

		foreach($data as $prop => $val)
		{
			switch($prop)
			{
				case "products":
					$reservation->set_meta("amount" , $val);
					break;
				case "start":
					$reservation->set_prop("start1" , $val);
					break;
				case "end":
					$reservation->set_prop($prop , $val);
					break;
				case "comment":
					$reservation->set_prop("content" , $val);
					break;
				case "people":
					$reservation->set_prop("people_count" , $val);
					break;
				case "customer":
					$reservation->set_prop("customer", $val);
					break;
				case "verified":
					$reservation->set_prop("verified", $val);
					break;
			}
		}
		if($data["name"])
		{
			$customer = new object();
			$customer->set_class_id(CL_CRM_PERSON);
			$customer->set_name(trim($data["name"]));
			list($fn , $ln) = explode(" ", $data["name"]);
			$customer->set_prop("firstname", trim($fn));
			$customer->set_prop("lastname", trim($ln));
			
			$sts = $this->get_settings_for_room($room);
			if(is_oid($sts->id()) && $sts->prop("customer_menu") && $this-> can("add" , $sts->prop("customer_menu")))
			{
				$customer->set_parent($sts->prop("customer_menu"));
			}
			else
			{
				$customer->set_parent($parent);
			}
		//	$customer->save();
			if($data["phone"])
			{
				$phone = new object();
				$phone->set_class_id(CL_CRM_PHONE);
				$phone->set_name($data["phone"]);
				$phone->set_prop("type" , "mobile");
				$phone->set_parent($parent);
				$phone->save();
				$customer->connect(array("to"=> $phone->id(), "type" => "RELTYPE_PHONE"));
				$customer->set_prop("phone", $phone->id());
			}
			if($data["email"])
			{
				$email = new object();
				$email->set_class_id(CL_ML_MEMBER);
				$email->set_name($data["email"]);
				$email->set_prop("mail" , $data["email"]);
				$email->set_parent($parent);
				$email->save();
				$customer->connect(array("to"=> $email->id(), "type" => "RELTYPE_EMAIL"));
				$customer->set_prop("email", $email->id());
			}
			$customer->save();
			$reservation->set_prop("customer" , $customer->id());
		}
		$reservation->set_name(sprintf(t("%s: %s / %s-%s %s"),
			$reservation->prop("customer.name"),
			date("d.m.Y", $reservation->prop("start1")),
			date("H:i", $reservation->prop("start1")),
                        date("H:i", $reservation->prop("end")),
                        $reservation->prop("resource.name")
		));
		if($arr["not_verified"] || $arr["_not_verified"])
		{
			$reservation->set_prop("verified", 0);
		//	$reservation->save();
		}
		
		if($arr["tpl"])
		{
			$reservation->set_meta("tpl", $arr["tpl"]);
		}
		$lang = aw_global_get("lang_id");
		
		$l = get_instance("languages");
		$_SESSION["ct_lang_lc"] = $l->get_langid($_SESSION["ct_lang_id"]);
		$reservation->set_meta("lang" , $lang);
		$reservation->set_meta("lang_id" , $_SESSION["ct_lang_id"]);
		$reservation->set_meta("lang_lc" , $_SESSION["ct_lang_lc"]);
		$reservation->save();
		return $reservation->id();
	}

	/**
		@attrib name=get_bron_time params=name all_args=1 nologin=1
		@param id required oid
			room id
		@param bron optional array
			keys are start timestamps
		@param room_reservation_length optional double
			length/step
	**/
	function _get_bron_time($arr)
	{
		foreach($arr["bron"] as $key => $val)
		{
			if(!$val) unset($arr["bron"][$key]);
		}
		extract($arr);
		if(is_oid($arr["id"]))
		{
			$room = obj($arr["id"]);
			$length = $this->step_lengths[$room->prop("time_unit")] * $room->prop("time_step") ;
			$end = $arr["bron"][0];
			foreach($arr["bron"] as $bron => $val)
			{
				if(!$start)
				{
					$start = $bron;
					$end = $start + $length;
				}
				if(($end) == $bron)
				{
					$end = $bron + $length;
				}
			}
			if($room_reservation_length > 0)
			{
				$end = $start + $length * $room_reservation_length;
			}
		}
		return array("start" => $start, "end" => $end);
		
	}

	function _get_resources_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		if($arr["obj_inst"]->prop("resources_fld"))
		{
			$tb->add_button(array(
				"name" => "add_resource",
				"tooltip" => t("Lisa ressurss"),
				"url" => $this->mk_my_orb("new", array(
					"mrp_workspace" => $arr["obj_inst"]->id(),
					"mrp_parent" => $arr["obj_inst"]->prop("resources_fld"),
					"return_url" => get_ru(),
					"parent" => $arr["obj_inst"]->prop("resources_fld"),
				), CL_MRP_RESOURCE),
				"img" => "new.gif",
			));
		}
	}

	function _get_resources_tbl($arr)
	{
		if(!$arr["obj_inst"]->prop("resources_fld"))
		{
			$arr["prop"]["value"] = t("Ressursside kataloog m&auml;&auml;ramata");
			return PROP_OK;
		}
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		/*
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		*/

		foreach($this->get_room_resources($arr["obj_inst"]->id()) as $oid => $obj)
		{
			$url = $this->mk_my_orb("change", array(
				"id" => $obj->id(),
				"return_url" => get_ru(),
			), CL_MRP_RESOURCE);
			$t->define_data(array(
				/*
				"name" => html::href(array(
					"caption" => $obj->name(),
					"url" => $url,
				)),
				*/
				"name" => $obj->name(),
				"amount" => is_array($obj->prop("thread_data"))?count($obj->prop("thread_data")):1,
			));
		}
	}

	function get_room_resources($oid)
	{
		if(!is_oid($oid))
		{
			return array();
		}
		$obj = obj($oid);

		$ol = new object_list(array(
			"class_id" => CL_MRP_RESOURCE,
			"parent" => $obj->prop("resources_fld"),
		));
		return $ol->arr();
	}
	
	function search_products($this_obj)
	{
		$ol = new object_list();
		$filter = array("class_id" => array(CL_SHOP_PRODUCT), "lang_id" => array());
		$data = $this_obj->meta("search_data");
		if($data["products_find_product_name"])
		{
			$filter["name"] = "%".$data["products_find_product_name"]."%";
		}
		$ol = new object_list($filter);
		return $ol;
	}	
	
	function _products_tbl(&$arr)
	{
		classload("core/icons");
		$tb =& $arr["prop"]["vcl_inst"];		
		$this->_init_prod_list_list_tbl($tb,$arr["obj_inst"]);

		// get items 
		if (!$_GET["tree_filter"])
		{
			$ot = new object_list();
		}
		else
		{
			$ot = new object_list(array(
				"parent" => $_GET["tree_filter"],
				"class_id" => array(CL_MENU,CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING),
				"status" => array(STAT_ACTIVE, STAT_NOTACTIVE)
			));
		}

		classload("core/icons");

		//$ol = $ot->to_list();
		$ol = $ot->arr();
	
		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_products($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
			$ol = $ol->arr();
		}
		
		$prod_data = $this->get_prod_data_for_room($arr["obj_inst"]);
		foreach($ol as $o)
		{

			if ($o->class_id() == CL_MENU)
			{
				$tp = t("Kaust");
			}
			else
			if (is_oid($o->prop("item_type")))
			{
				$tp = obj($o->prop("item_type"));
				$tp = $tp->name();
			}
			else
			{
				$tp = "";
			}

			$get = "";
			if ($o->prop("item_count") > 0)
			{
				$get = html::href(array(
					"url" => $this->mk_my_orb("create_export", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("V&otilde;ta laost")
				));
			}

			$name = $o->name();
			if ($o->class_id() == CL_MENU)
			{
				$name =  html::href(array(
					"url" => html::get_change_url($o->id()),
					"caption" => $name
				));
			}
			
			//j2rjekorda kui pole, siis v6tab objektist selle j2rjekorra mis on laos jne
			$ord = $o->ord();
			if($prod_data[$o->id()]["ord"])
			{
				$ord = $prod_data[$o->id()]["ord"];
			}
			if($o->class_id() == CL_MENU)
			{
				$ba = $bb = "";
			}
			else
			{
				$bb = html::textbox(array(
						"name" => "bb[".$o->id()."]",
						"value" => $prod_data[$o->id()]["bb"],
						"size" => 5
				));
				$ba = html::textbox(array(
					"name" => "ba[".$o->id()."]",
					"value" => $prod_data[$o->id()]["ba"],
					"size" => 5
				));
			}
			$tb->define_data(array(
				"active" =>  $prod_data[$o->id()]["active"],//html::checkbox(array(
	//				"name" => "sel_imp[".$o->id()."]",
	//				"value" => $o->id(),
	//				"checked" => $prod_data[$o->id()]["active"],
	//			)),
				"oid" => $o->id(),
				"icon" => html::img(array("url" => icons::get_icon_url($o->class_id(), $o->name()))),
				"name" => $name,
				"cnt" => $o->prop("item_count"),
				"item_type" => $tp,
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $o->id(),
						"return_url" => get_ru()
					), $o->class_id()),
					"caption" => t("Muuda")
				)),
				"get" => $get,
				"put" => $put,
				"del" => html::checkbox(array(
					"name" => "sel[]",
					"value" => $o->id()
				)),
				"is_menu" => ($o->class_id() == CL_MENU ? 0 : 1),
				"ord" => html::textbox(array(
					"name" => "set_ord[".$o->id()."]",
					"value" => $ord,
					"size" => 5
				)).html::hidden(array(
					"name" => "old_ord[".$o->id()."]",
					"value" => $o->ord()
				)),
				"bb" => $bb,
				"ba" => $ba,
				"hidden_ord" => $ord
			));
		}

		$tb->set_numeric_field("hidden_ord");				
		$tb->set_default_sortby(array("is_menu", "hidden_ord"));
		$tb->sort_by();

		return $tb->draw(array(
			"pageselector" => "text",
			"records_per_page" => 50,
			"has_pages" => 1
		));
	}

	function _init_prod_list_list_tbl(&$t,$room)
	{
		$t->define_chooser(array(
			"name" => "active",
			"caption" => t("Aktiivne"),
			"field" => "oid",
		));
		
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;"),
			"sortable" => 0,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "item_type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

/*		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt",
			"caption" => t("Kogus laos"),
			"align" => "center",
			"type" => "int"
		));

		$t->define_field(array(
			"name" => "get",
			"caption" => t("V&otilde;ta laost"),
			"align" => "center"
		));
*/
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
		if($room->prop("use_product_times"))
		{
			$t->define_field(array(
				"name" => "bb",
				"caption" => t("Eelpuhver"),
				"align" => "center"
			));
	
			$t->define_field(array(
				"name" => "ba",
				"caption" => t("J&auml;relpuhver"),
				"align" => "center"
			));
		}
		$t->define_field(array(
			"name" => "del",
			"caption" => t("Vali"),
			"align" => "center",
		));
	}

	function _products_tr($arr)
	{
		$arr["prop"]["vcl_inst"] = new object_tree(array(
			"parent" => $this->_get_prod_fld($arr["obj_inst"]),
			"class_id" => CL_MENU,
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"sort_by" => "objects.jrk"
		));
		
		classload("vcl/treeview");
		$tv = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "prods",
				"persist_state" => true,
			),
			"root_item" => obj($this->_get_prod_fld($arr["obj_inst"])),
			"ot" => $arr["prop"]["vcl_inst"],
			"var" => "tree_filter"
		));
		$arr["prop"]["value"] = $tv->finalize_tree();
	}
	
	function _init_products_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("&nbsp;"),
			"sortable" => 0,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "item_type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "cnt",
			"caption" => t("Kogus laos"),
			"align" => "center",
			"type" => "int"
		));

		$t->define_field(array(
			"name" => "get",
			"caption" => t("V&otilde;ta laost"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "put",
			"caption" => t("Vii lattu"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "del",
			"caption" => "<a href='javascript:aw_sel_chb(document.changeform,\"sel\")'>".t("Vali")."</a>",
			"align" => "center",
		));
	}
	
	function _products_tb(&$data)
	{
		$tb =& $data["prop"]["toolbar"];

		$this->prod_type_fld = $this->_get_prod_fld($data["obj_inst"]);
		$this->prod_tree_root = isset($_GET["tree_filter"]) ? $_GET["tree_filter"] : $this->_get_prod_fld($data["obj_inst"]);
			
		$tb->add_menu_button(array(
			"name" => "crt_".$this->prod_type_fld,
			"tooltip" => t("Uus")
		));

		$this->_req_add_itypes($tb, $this->prod_type_fld, $data);

		$tb->add_menu_item(array(
			"parent" => "crt_".$this->prod_type_fld,
			"text" => t("Lisa kaust"),
			"link" => $this->mk_my_orb("new", array(
				"parent" => $this->prod_tree_root,
				"return_url" => get_ru(),
			), CL_MENU)
		));
		
		$tb->add_menu_item(array(
			"parent" => "crt_".$this->prod_type_fld,
			"text" => t("Lisa tootekategooria"),
			"link" => $this->mk_my_orb("new", array(
				"parent" => $this->prod_tree_root,
				"return_url" => get_ru(),
			), CL_SHOP_PRODUCT_TYPE)
		));

		// list all shop product types and add them to the menu
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_TYPE,
			"lang_id" => array(),
			"site_id" => array()
		));
		$tb->add_menu_separator(array("parent" => "crt_".$this->prod_type_fld));
		foreach($ol->arr() as $prod_type)
		{
			$tb->add_menu_item(array(
				"parent" => "crt_".$this->prod_type_fld,
				"text" => $prod_type->name(),
				"link" => $this->mk_my_orb("new", array(
                                                "item_type" => $prod_type->id(),
                                                "parent" => $this->prod_tree_root,
                                                //"alias_to" => $this->warehouse->id(),
                                                "reltype" => 2, //RELTYPE_PRODUCT,
                                                "return_url" => get_ru(),
                                                "cfgform" => $prod_type->prop("sp_cfgform"),
                                                "object_type" => $prod_type->prop("sp_object_type"),
						"pseh" => aw_register_ps_event_handler(
							CL_ROOM,
							"handle_product_add",
							array("id" => $data["obj_inst"]->id()),
							CL_SHOP_PRODUCT
						)
                                        ), CL_SHOP_PRODUCT) 
			));
		}
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Aktiivseks"),
			'action' => 'save_products',
		));

		$tb->add_button(array(
			"name" => "del",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud"),
			'action' => 'delete_cos',
		));
	}

	function handle_product_add($product, $arr)
	{
		$room = obj($arr["id"]);
		$prod_data = $this->get_prod_data_for_room($room);
		$prod_data[$product->id()]["active"] = 1;
		$room->set_meta("prod_data", $prod_data);
		$room->save();
	}

	/**
		@attrib name=save_products params=name all_args=1
	**/
	function save_products($arr)
	{
		$this_obj = obj($arr["id"]);
		if(is_oid($this_obj->prop("inherit_prods_from")) && $this->can("view" , $this_obj->prop("inherit_prods_from")))
		{
			$this_obj = obj($this_obj->prop("inherit_prods_from"));
		}
		$prod_data = $this_obj->meta("prod_data");
		foreach($arr["set_ord"]  as $id => $ord)
		{
			$prod_data[$id]["active"] = $arr["active"][$id];
			$prod_data[$id]["ord"] = $ord;
			$prod_data[$id]["bb"] = $arr["bb"][$id];
			$prod_data[$id]["ba"] = $arr["ba"][$id];
		}
		
		$this_obj->set_meta("prod_data" , $prod_data);
		$this_obj->save();
		return $arr["post_ru"];
	}
	
	function _req_add_itypes(&$tb, $parent, &$data)
	{
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => array(CL_SHOP_PRODUCT_TYPE),
			"lang_id" => array(),
			"site_id" => array()
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if ($o->class_id() != CL_MENU)
			{
				$tb->add_menu_item(array(
					"parent" => "crt_".$parent,
					"text" => $o->name(),
					"link" => $this->mk_my_orb("new", array(
						"item_type" => $o->id(),
						"parent" => $this->prod_tree_root,
						//"alias_to" => $this->warehouse->id(),
						"reltype" => 2, //RELTYPE_PRODUCT,
						"return_url" => get_ru(),
						"cfgform" => $o->prop("sp_cfgform"),
						"object_type" => $o->prop("sp_object_type")
					), CL_SHOP_PRODUCT)
				));
			}
			else
			{
				$tb->add_sub_menu(array(
					"parent" => "crt_".$parent,
					"name" => "crt_".$o->id(),
					"text" => $o->name()
				));
				$this->_req_add_itypes($tb, $o->id(), $data);
			}
		}
	}
	
	function get_prod_tree_ids($o)
	{
		if(is_oid($o))
		{
			$o = obj($o);
		}
		$prod_fld = $this->_get_prod_fld($o);
		if(is_oid($prod_fld) && $this->can("view" , $prod_fld))
		{
			$tree = new object_tree(array(
				"parent" => $prod_fld,
				"class_id" => CL_MENU,
				"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
				"sort_by" => "objects.jrk"
			));
			
			$menu_list = $tree->to_list(array(
				"add_root" => true,
			));
			$parents = $menu_list->ids();
			foreach($parents as $key => $parent)
			{
				if(!$this->prod_data[$parent]["active"] && !($prod_fld == $parent))
				{
					unset($parents[$key]);
				}
			}
			return $parents;
		}
		else
		{
			return "";
		}
	}
	
	function get_folder_items($o,$menu)
	{
		if(!$this->active_items)
		{
			$this->prod_data = $this->get_prod_data_for_room($o);
			$ol = $this->get_active_items($o);
			$this->active_items = $ol->ids();
		}
		return new object_list(array(
			"lang_id" => array(),
			"parent" => $menu,
			"oid" => $this->active_items,
		));
	
	}
	
	//returns active packages
	function get_package_list($o)
	{
		$ol = new object_list();
		if(is_oid($o))
		{
			$o = obj($o);
		}
		if(!is_object($o))
		{
			return new object_list();
		}
		
		if($o->class_id() == CL_SHOP_PRODUCT)
		{
			$packages = $o->connections_from(array(
				"type" => "RELTYPE_PACKAGING",
			));
			foreach($packages as $conn)
			{
				$package = $conn->prop("to");
				if($this->prod_data[$package]["active"])
				{
					$ol->add($package);
				}
			}
		}
		if($o->class_id() == CL_ROOM)
		{
			$this->prod_data = $this->get_prod_data_for_room($o);
			$parents = $this->get_prod_tree_ids($o);
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PACKAGING,
				"lang_id" => array(),
				"parent" => $parents,
			));
			foreach($ol->ids() as $package)
			{
				if(!$this->prod_data[$package]["active"])
				{
					$ol->remove($package);
				}
			}
		
		}
		return $ol;
	}

	//annab vastavalt ruumile siis kas pakendite v6i toodete object listi, mis on aktiivsed
	/** gets active room products or packagings
		@attrib params=pos api=1
		@param o required type=object/oid
			room object
		@returns Object list
		@example
			$room_inst = get_instance(CL_ROOM);
			$product_list = $room_inst->get_active_items($room_object->id());
	**/
	function get_active_items($o)
	{
		$ol = new object_list();
		if(is_oid($o))
		{
			$o = obj($o);
		}
		if(!is_object($o) || !$o->class_id() == CL_ROOM || !is_oid($o->prop("warehouse")) || !$this->can("view" , $o->prop("warehouse")))
		{
			return $ol;
		}
		$warehouse = obj($o->prop("warehouse"));
		if(!is_oid($warehouse->prop("conf")))
		{
			return $ol;
		}
		$conf = obj($warehouse->prop("conf"));
		if($conf->prop("sell_prods"))
		{
			return $this->get_prod_list($o);
		}
		else
		{
			$prods = $this->get_prod_list($o);
/*			$c = new connection();
			$conns = $c->find(array(
				"from" => $prods->ids(),
				"from.class_id" => CL_SHOP_PRODUCT,
				"reltype" => "RELTYPE_PACKAGING"
			));
			$pk_ids = array();
			foreach($conns as $c)
			{
				if($this->prod_data[$c["to"]]["active"]) 
				{
					$pk_ids[] = $c["to"];
				}
			}
*/

			foreach($prods->arr() as $product){
				foreach ($product->connections_from(array("type" => "RELTYPE_PACKAGING")) as $pc)
				{
					if($this->prod_data[$pc->prop("to")]["active"]) 
					{
						$pk_ids[] = $pc->prop("to");
					}
				}
			}
			if (count($pk_ids))
			{
				$ol = new object_list(array(
					"oid" => $pk_ids,
					"lang_id" => array(),
					"site_id" => array(),
					"sort_by" => "objects.jrk",
				));
			}
			/*foreach($prods->arr() as $product)
			{
				$ol->add($this->get_package_list($product));
			}*/
		}
		$this->menus = $o->meta("group_product_menu");
		$ol->sort_by_cb(array($this, "__sort_prod_list"));
		return $ol;
	}
	
	function __sort_prod_list($a , $b)
	{
		if($this->menus)
		{
			if((int)$this->prod_data[$a->parent()]["ord"] - (int)$this->prod_data[$b->parent()]["ord"])
			{
				return (int)$this->prod_data[$a->parent()]["ord"] - (int)$this->prod_data[$b->parent()]["ord"];
			}
			
			if($a->parent() != $b->parent())
			{
				return strcmp($a->prop("parent.name"), $b->prop("parent.name"));
			}
		}
		if((int)$this->prod_data[$a->id()]["ord"] - (int)$this->prod_data[$b->id()]["ord"])
		{
			return (int)$this->prod_data[$a->id()]["ord"] - (int)$this->prod_data[$b->id()]["ord"];
		}
		return strcmp($a->name(), $b->name());
		return 0;
	}
	
	//returns active products
	function get_prod_list($o)
	{
		$ol = new object_list();
		if(is_oid($o))
		{
			$o = obj($o);
		}
		
		if(!is_object($o))
		{
			return new object_list();
		}
		
		if($o->class_id() == CL_MENU)
		{
			return $ol;
			$parents = $o->id();
			
		}
		$this->prod_data = $this->get_prod_data_for_room($o);
		$parents = $this->get_prod_tree_ids($o);
		
		$parents_temp = array();
		foreach($parents as $parent)
		{
			if($this->can("view" , $parent))
			{
				$po = obj($parent);
				if($this->prod_data) 
				{
					$parents_temp[$parent] = $this->prod_data[$parent]["ord"];
				}
				else
				{
					$parents_temp[$parent] = $po->prop("jrk");
				}
			}
		}
		asort($parents_temp, SORT_NUMERIC);
		$parents = $parents_temp;
		

		$prods = array();
		foreach($this->prod_data as $prod => $data)
		{
			if ($data["active"])
			{
				$prods[] = $prod;
			}
		}

		$p_ol = new object_list(array(
                        "class_id" => CL_SHOP_PRODUCT,
                        "lang_id" => array(),
	                "parent" => $prods,
			"sort_by" => "objects.jrk",
                ));
		$p_ol->arr();
		$p_ol->sort_by_cb(array(&$this, "__prod_sorter"));
//		return $p_ol;

		foreach($parents as $key => $jrk)
		{
			$tmp_list = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT,
				"lang_id" => array(),
				"parent" => $key,
				"sort_by" => "jrk ASC",
			));
			if($this->prod_data)
			{
				$prods = array();
				foreach($tmp_list->ids() as $prod)
				{
					if($this->prod_data[$prod]["active"])
					{
						$prods[$prod] = $this->prod_data[$prod]["ord"];
					}
				}//arr($prods);
				asort($prods, SORT_NUMERIC);
				$ol->add(array_keys($prods));
			}
		}
		return $ol;
	}
	
	function __prod_sorter($a, $b)
	{
		return $this->prod_data[$b->id()]["ord"] - $this->prod_data[$a->id()]["ord"];
	}
	
	/**
		@attrib params=pos api=1
		@param prod_list required type=Array
			array of product id's and amount of them
			array(
				prod_oid => amount,
			)

		@param reservation required type=oid
			rervations oid with what these products where ordered
		@param time optional type=int
			the time, when this order needs to be in order :) .. basically this is needed to cover cross-day reservations and different orders for each day..
			If not set, reservation start time is set instead.
		@returns boolean
			true if success
		@example
			$prod_list = array(
				$product => $amount,
			);
			$inst = get_instance(CL_ROOM);
			$inst->order_products($prod_list, $reservation->id(), $day);
	**/
	function order_products($prod_list, $reservation, $time)
	{
		if(!is_array($prod_list) || !is_oid($reservation))
		{
			return false;
		}
		$reservation = obj($reservation);
		$room = $reservation->prop("resource");
		if(is_oid($room))
		{
			$room = obj($room);
		}
		else
		{
			return false;
		}
		$warehouse = $room->prop("warehouse");
		if(is_oid($warehouse))
		{
			$warehouse = obj($warehouse);
		}
		else
		{
			return false;
		}
		$so = get_instance(CL_SHOP_ORDER);
		$so->start_order($warehouse);
		foreach($prod_list as $prod_id => $amount)
		{
			if(!is_oid($prod_id) || $amount < 1)
			{
				continue;
			}
			$so->add_item(array(
				"iid" => $prod_id,
				"item_data" => array(
					"items" => $amount,
				),
			));

		}
		$order_id = $so->finish_order();
		$reserv = get_instance(CL_RESERVATION);
		if(!$reserv->add_order($reservation->id(), $order_id, $time))
		{
			return false;
		}
		return true;
	}

	/** Change the realestate object info.
		
		@attrib name=parse_alias is_public="1" caption="Change"
	
	**/
	function parse_alias($arr)
	{
		enter_function("room::parse_alias");
		$tpl = "kolm.tpl";
		$this->read_template($tpl);
		lc_site_load("room", &$this);
		
		$data = array("joga" => "jogajoga");
		$this->vars($data);
		//property v22rtuse saatmine kujul "property_nimi"_value
		exit_function("room::parse_alias");
		return $this->parse();
	}
	
	function get_room_price_instance()
	{
		if(!$this->room_price_instance)
		{
			$this->room_price_instance = get_instance(CL_ROOM_PRICE);
		}
		return $this->room_price_instance;
	}
	
	function get_user_instance()
	{
		if(!$this->user_instance)
		{
			$this->user_instance = get_instance(CL_USER);
		}
		return $this->user_instance;
	}
	
	/** calculates room price
		@attrib params=name api=1
		@param room required type=oid
			room id
		@param people optional type=int
			number of people
		@param start required type=int
			whenever that stuff , you need room for, starts
		@param end required type=int
			when the event, you need room for, ends
		@param bron optional type=oid
			reservation object id
		@param products optional type=array, -1
			products you want to order with room , -1 if room price without products
		@param detailed_info type=bool default=false
			if set to true, data is returned in detail, separate entries for products and everything
		@returns array
			if detailed_info is not set, returns array(currency1 => sum , currency2 => sum2 ...)
			else array("info_1" => array(currency1 => sum , currency2 => sum2 ...) , "info2" => array(currency1 => sum , currency2 => sum2 ...) , ...)
		@example 
			$room_instance = get_instance(CL_ROOM);
			return $room_instance->cal_room_price(array(
				"room" => $room_object->id(),
				"start" => $reservation->prop("start1"),
				"end" => $reservation->prop("end"),
				"people" => 5,
			));
	**/
	function cal_room_price($arr)
	{
		extract($arr);		
		if(!is_oid($room))
		{
			return 0;
		}
		$room = obj($room);
		$this->bargain_value = array();
		$this->step_length = $this->step_lengths[$room->prop("time_unit")];
		$sum = array();
		$rv = array();	
		$u = $this->get_user_instance();	
		$price_inst = $this->get_room_price_instance();
		$this_price = "";
		$this_prices = array();
		$prices = $room->connections_from(array(
			"class_id" => CL_ROOM_PRICE,
			"type" => "RELTYPE_ROOM_PRICE",
		));

		$bron_made = time();
		$gl = aw_global_get("gidlist_pri_oid");
		asort($gl);
		$gl = array_keys($gl);
		$grp = $gl[1];
		if (count($gl) == 1)
		{
			$grp = $gl[0];
		}
		if (is_object($arr["bron"]))
		{
			$bron_made = $arr["bron"]->created();
			$gro = $u->get_highest_pri_grp_for_user($arr["bron"]->createdby(), true);
			$grp = $gro->id();
		}

		foreach($prices as $conn)
		{
			$price = $conn->to();
			if(($price->prop("date_from") < $start) && $price->prop("date_to") > $end && $price->prop("type") == 1)
			{
				if(in_array((date("w", $start) + 1) , $price->prop("weekdays")))
				{
					if (($price->prop("bron_made_from") < 1 || $bron_made > $price->prop("bron_made_from")) ||
					    ($price->prop("bron_made_to") < 1 || $bron_made < $price->prop("bron_made_to"))
					)
					{
						$groups = $price->prop("apply_groups");
						if (!is_array($groups) || !count($groups) || in_array($grp, $groups))
						{
							$this_price = $price;
							$this_prices[$price->prop("nr")][] = $price;
	//						break;
						}
					}
				}
			}
		}
		$step = 1;
		$time = $end-$start;//+60 seep2rast et oleks nagu t2isminutid ja t2istunnid jne

		if (is_object($arr["bron"]) && $arr["bron"]->prop("special_discount") > 0)
		{
			$rv["room_bargain"] = $special_discount = $arr["bron"]->prop("special_discount") * 0.01;
		}
		else
		{
			$special_discount = 0;
		}

		while($time >= 60)//alla minuti ei ole oluline aeg eriti..
		{
			$price = "";
			if(is_array($this_prices[$step]))
			{
				$price = $this->get_best_time_in_prices(array(
					"time" => $time,
					"prices" => $this_prices[$step],
					"end" => $end,
				));
			}
			if(!is_object($price))//k6vemal tasemel enam ei ole hindu.... laseb vanaga edasi
			{
				$price = $this->get_best_time_in_prices(array(
					"time" => $time,
					"prices" => $this_prices[$step-1],
					"end" => $end,
				));
			}
			else
			{
				$step++;
			}
			//arr($price);
			if(!is_object($price))//juhul kui miski uus aeg vms... hakkab otsast peale
			{
				$price = $this->get_best_time_in_prices(array(
					"time" => $time,
					"prices" => $this_prices[1],
					"end" => $end,
				));
			}

			if(!is_object($price) || !($price->prop("time") > 0) || !$this->step_length)//igaks juhuks... ei taha et asi tsyklisse j22ks
			{
				break;
			}
			//otsib, kas m6ni soodushind kattub, ainult siis kui spetsiaalallahindlust pole 
			if(!$special_discount)
			{
				$bargain = $this->get_bargain(array(
					"price" => $price,
					"room" => $room,
					"time" => $price->prop("time") * $this->step_length,
					"start" => $end-$time,
					"bron_made" => $bron_made,
					"bron" => $arr["bron"]
				));
	
				$rv["room_bargain"] = $bargain;
			}
				foreach($price->meta("prices") as $currency => $hr_price)
				{
					$sum[$currency] += ($hr_price - $bargain*$hr_price);//+1 seep2rast, et l6ppemise t2istunniks v6etakse esialgu ymardatud allapoole tunnid... et siis ajale tuleb yks juurde liita, sest poolik tund l2heb t2is tunnina arvesse
					$this->bargain_value[$currency] = $this->bargain_value[$currency] + $bargain*$hr_price;
				}

			$time = $time - ($price->prop("time") * $this->step_length);
		}
		
		$rv["room_price"] = $sum;
		$max_room_price = $room->meta("max_room_price");
		//kontrollib kas on olemas maksimumhind ruumile
		if(is_array($max_room_price) && sizeof($max_room_price))
		{
			foreach($sum as $curr => $val)
			{
				if($max_room_price[$curr] && $max_room_price[$curr] < $val)
				{
					$sum[$curr] = $max_room_price[$curr];
					$rv["room_price"][$curr] = $max_room_price[$curr];
				}
			}
		}

		//spetsiaalsooduse v6tab siis maha, kui on kogu ruumihind arvutatud
		if($special_discount)
		{
			foreach($sum as $curr => $val)
			{
				$this->bargain_value[$curr] = $special_discount*$val;
				$sum[$curr] = $val * (1 - $special_discount);
				$rv["room_price"][$curr] = $sum[$curr];
			}
		}


		$warehouse = $room->prop("warehouse");
		if(is_object($arr["bron"]))
		{
			$grp = $u->get_highest_pri_grp_for_user($arr["bron"]->createdby(), true);
			$grp = array($grp->id());
		}
		$prod_discount = $this->get_prod_discount(array(
			"start" => $start,
			"end" => $end,
			"room" => $room->id(),
			"group" => $grp,
			"bron" => $bron,
		));

		// special discount does nota pply to products
		/*if (is_object($arr["bron"]) && $arr["bron"]->prop("special_discount") > 0)
		{
			$prod_discount = $arr["bron"]->prop("special_discount");
		}*/
		// and if the user has set a discount for prods separately, then that overrides everything
		if (is_object($arr["bron"]) && $arr["bron"]->prop("products_discount"))
		{
			 $prod_discount = $arr["bron"]->prop("products_discount");
		}
//if(aw_global_get("uid") == "struktuur") {arr($arr["bron"]->prop("products_discount"));arr($arr["bron"]->meta());}
		foreach($room->prop("currency") as $currency)
		{
			if(!$sum[$currency])
			{
				$sum[$currency] = 0;
			}
			if (!$rv["room_price"][$currency])
			{
				$rv["room_price"][$currency] = 0;
			}
			if($people > $room->prop("normal_capacity"))
			{
				$sum[$currency] += $this->cal_people_price(array("room" => $room, "people" => $people, "cur" => $currency , "start" => $start, "end" => $end));//($people-$room->prop("normal_capacity")) * $room->prop("price_per_face_if_too_many"); 
				$this->bargain_value[$currency]+= $this->cal_people_price_discount(array("room" => $room, "people" => $people, "cur" => $currency , "start" => $start, "end" => $end));
				$rv["room_price"][$currency] += $this->cal_people_price(array("room" => $room, "people" => $people, "cur" => $currency, "start" => $start, "end" => $end));//($people-$room->prop("normal_capacity")) * $room->prop("price_per_face_if_too_many");
			}
//			if(is_array($products) && sizeof($products))
			if(!($products == -1))
			{
				$tmp = $this->cal_products_price(array(
					"products" => $products,
					"currency" => $currency,
					"bron" => $bron,
					"prod_discount" => $prod_discount,
					"room" => $room,
					"start" => $start,
					"end" => $end,
				));
				$sum[$currency] += $tmp;
				$rv["prod_price"][$currency] += $tmp;

				// calculate the amount of money saved by the discount back from the discounted price
				$adv = 100 - $prod_discount;//if(aw_global_get("uid") == "struktuur") arr($tmp); a
				if(!$prod_discount)
				{
					$adv = 100 - $this->average_discount_for_products;
				}
				$rv["prod_discount_value"][$currency] = ((100.0 * $tmp) / $adv) - $tmp;
			}
		}
		if($prod_discount)
		{
			$rv["prod_discount"] = $prod_discount;
		}
		else
		{
			$rv["prod_discount"] = $this->average_discount_for_products;
		}
		
		//teeb k6igepealt kontrolli, et kas miinimumhind on olemas yldse, k6hutunnne ytleb, et seadete otsimine v6tab rohkem aega,... niiet paneb selle hilisemaks
		if(is_array($room->meta("web_room_min_price")) && sizeof($room->meta("web_room_min_price")))
		{
			$min = $room->meta("web_room_min_price");
			foreach($sum as $curr => $val)
			{
				if($min[$curr] && $min[$curr] > $val)
				{
					$set = $this->get_settings_for_room($room);
					if($set->prop("min_price_to_all"))
					{
						$sum[$curr] = $min[$curr];
						$rv["room_price"][$curr] = $min[$curr];
					}
				}
			}
		}

		$rv["room_bargain_value"] = $this->bargain_value;
		if ($arr["detailed_info"])
		{
			return $rv;
		}
		else
		{
			return $sum;
		}
	}

	function get_room_discount_objects($room)
	{
		$ol = new object_list();
		$bargain_conns = $room->connections_from(array(
			"class_id" => CL_ROOM_PRICE,
			"type" => "RELTYPE_ROOM_PRICE",
		));
		foreach($bargain_conns as $conn)
		{
			$ol->add($conn->to());
		}
		return $ol;
	}
	
	/**
	@param room required type=oid,object
	@param start required type=int
	@param end required type=int
	@param group optional type=int
	**/
	function get_rnd_discount_in_time($arr)
	{
		if(isset($this->rnd_discount))
		{
			return $this->rnd_discount;
		}
		if(is_array($arr["group"]))
		{
			$grp = $arr["group"];
		}
		else
		{
			$gl = aw_global_get("gidlist_pri_oid");
			asort($gl);
			$gl = array_keys($gl);
			$grp = $gl;
		}
		$ret = 0;
		extract($arr);
		$priority = 0;
		
		
		if(is_oid($room) && $this->can("view" , $room))
		{
			$room = obj($room);
		}
		if(is_object($room))
		{
			if(!$start && $_SESSION["room_reservation"][$room->id()]["start"])
			{
				if($_SESSION["room_reservation"][$room->id()]["start"])
				{
					$start = $_SESSION["room_reservation"][$room->id()]["start"];
				}
				else
				{
					$start = time();
				}
			}
			if(!$end)
			{
				if($_SESSION["room_reservation"][$room->id()]["end"])
				{
					$end = $_SESSION["room_reservation"][$room->id()]["end"];
				}
				else
				{
					$end = time();
				}
			}
			$b_list = $this->get_room_discount_objects($room);//arr($room);
			foreach($b_list->arr() as $bargain)
			{
				if(
					($bargain->prop("active") == 1) &&
					($bargain->prop("type") == 2) &&
					(in_array((date("w", $start) + 1) , $bargain->prop("weekdays"))) && 
					(
						(
							$bargain->prop("date_from") <= ($start) &&
							($bargain->prop("date_to") + 60) >= ($end)
						)||
						(
							$bargain->prop("recur")	&&
							(
								(
									(100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from"))) <= (100*date("n",$start) + date("j",$start)) && 
									(100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) >= (100*date("n",($start+$time)) + date("j",($start+$time)))
								) || 
								(
									(100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from")) >= 100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) &&
										(
											((100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from"))) <= (100*date("n",$start) + date("j",$start)))||
											((100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) >= (100*date("n",($start+$time)) + date("j",($start+$time)))
										)
									)
								)
							)
						)
					)
				)
				{
					
					$groups = $bargain->prop("apply_groups");//arr($grp);
					if (
						(
							($bargain->prop("bron_made_from") < 1 || $bron_made > $bargain->prop("bron_made_from")) ||
						    	($bargain->prop("bron_made_to") < 1 || $bron_made < $bargain->prop("bron_made_to"))
					    	) && 
						(
							!is_array($groups) || !count($groups) || !reset($groups) || sizeof(array_intersect($grp, $groups))
						)
					)
					{//arr(array_intersect($grp, $groups));arr($grp); arr($groups);arr($bargain);
						//kui enne oli ka m6ni , a prioriteet oli suurem, siis see ei l2he
						if(!($priority  > $bargain->prop("priority")))
						{
						//	if(aw_global_get("uid") == "struktuur" )arr($bargain->prop("priority"));
							$ret = $bargain->prop("bargain_percent");
							$priority = $bargain->prop("priority");
						}
					}
				}
			}
		}
		$this->rnd_discount = $ret;
		return $ret;
	}
	
	function cal_people_price_discount($arr)
	{
		$discount = $this->get_rnd_discount_in_time($arr);
		extract($arr);
		if(is_oid($room) && $this->can("view" ,$room))
		{
			$room = obj($room);
		}
		if(!is_object($room))
		{
			return 0;
		}
		$sum = 0;
		$prices = $room->meta("people_prices");
		$price_nt = 1;
		$t_people = $people-$room->prop("normal_capacity");
		$n = 1;
		while($n <= $t_people)
		{
			if($prices[$price_nt] || $prices[$price_nt] == "0")
			{
				$sum+=$prices[$price_nt][$cur];
				$price_nt++;
			}
			else
			{
				$sum+=$prices[$price_nt-1][$cur];
			}
			$n++;
		}
		return (($discount * 0.01)*$sum);
	}
	
	function cal_people_price($arr)
	{
		//if(aw_global_get("uid"))arr($arr);
		$discount = $this->get_rnd_discount_in_time($arr);
		extract($arr);
		if(is_oid($room) && $this->can("view" ,$room))
		{
			$room = obj($room);
		}
		if(!is_object($room))
		{
			return 0;
		}
		$sum = 0;
		$prices = $room->meta("people_prices");
		$price_nt = 1;
		$t_people = $people-$room->prop("normal_capacity");
		$n = 1;
		while($n <= $t_people)
		{
			if($prices[$price_nt] || $prices[$price_nt] == "0")
			{
				$sum+=$prices[$price_nt][$cur];
				$price_nt++;
			}
			else
			{
				$sum+=$prices[$price_nt-1][$cur];
			}
			$n++;
		}
		$this->people_price_discount[$cur] = (($discount * 0.01)*$sum);
		return $sum - (($discount * 0.01)*$sum);
	//array("room" => $room, "people" => $people, "cur" => $currency));	
	}
	
	private function get_user_inst()
	{
		if(!$this->user_inst)
		{
			$this->user_inst = get_instance(CL_USER);
		}
		return $this->user_inst;
	}

	//annab soodustuse juhul kui see t2pselt kattub hinna ajaga v6i kui yks soodustus l6ppeb kas enne aja l6ppu , v6i algab alles poole pealt
	//inimliku lolluse vastu kahjuks see funktsioon ei aita, kui kellelgi on tahtmist mitmeid poolikult kattuvaid soodustusi yhele ajale paigutada... palun v2ga, kuid resultaati ei oska ette ennustada
	function get_bargain($arr)
	{
		enter_function("room::get_bargain");
		extract($arr);
		$priority = 0;
		$ret = 0;
		$gl = aw_global_get("gidlist_pri_oid");
		asort($gl);
		$gl = array_keys($gl);
		$grp = $gl[1];
		if (count($gl) == 1)
		{
			$grp = $gl[0];
		}

		$gi = $this->get_user_inst();
		if (is_object($arr["bron"]))
		{
	                $gro = $gi->get_highest_pri_grp_for_user($arr["bron"]->createdby(), true);
        	        $grp = $gro->id();
			$user_pri = $gi->get_group_pri_for_user($arr["bron"]->createdby());
		}
		else
		{
	                $gro = $gi->get_highest_pri_grp_for_user(aw_global_get("uid"), true);
        	        $grp = $gro->id();
			$user_pri = $gi->get_group_pri_for_user(aw_global_get("uid"));
		}

		$used_priority = 0;
		if(is_object($price) && is_object($room))
		{
			$bargains = array();
			$bargain_conns = $room->connections_from(array(
				"class_id" => CL_ROOM_PRICE,
				"type" => "RELTYPE_ROOM_PRICE",
			));
			$end = $start+$time;
			foreach($bargain_conns as $conn)
			{
				$bargain = $conn->to();//kui j2rgnevas iffis midagi ei t88ta.... siis edu... mulle vist 
				//if($bargain->prop("type") == 2 && $bargain->prop("active") == 1){arr($bargain);arr($bargain->prop("date_from")); arr($bargain->prop("date_to")); arr($start); arr($time);print " - - - - - - " ;}
				if(
					($bargain->prop("active") == 1) &&
					($bargain->prop("type") == 2) &&
					(in_array((date("w", $start) + 1) , $bargain->prop("weekdays"))) && 
					(
						(
							$bargain->prop("date_from") <= ($start+60) &&
							($bargain->prop("date_to") + 86400) >= ($start+$time)//syda88ni
						)||
						(
							$bargain->prop("recur")	&&
							(
								(
									(100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from"))) <= (100*date("n",$start) + date("j",$start)) && 
									(100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) >= (100*date("n",($start+$time)) + date("j",($start+$time)))
								) || 
								(
									(100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from")) >= 100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) &&
										(
											((100*date("n",$bargain->prop("date_from")) + date("j",$bargain->prop("date_from"))) <= (100*date("n",$start) + date("j",$start)))||
											((100*date("n",$bargain->prop("date_to")) + date("j",$bargain->prop("date_to"))) >= (100*date("n",($start+$time)) + date("j",($start+$time)))
										)
									)
								)
							)
						)
					)
				)
				{
					$groups = $bargain->prop("apply_groups");//arr(array_intersect($groups, $gl));arr($gl);arr($groups);
					if (
						(
							($bargain->prop("bron_made_from") < 1 || $bron_made > $bargain->prop("bron_made_from")) ||
						    	($bargain->prop("bron_made_to") < 1 || $bron_made < $bargain->prop("bron_made_to"))
					    	) && 
						(
							!is_array($groups) || !count($groups) || (is_array(array_intersect($groups, $gl)) && sizeof(array_intersect($groups, $gl)))//in_array($grp, $groups)
						)
					)
					{
						if(($priority  > $bargain->prop("priority")))
						{
							continue;
						}
						
					//kui on gruppidele m6juv asi, siis vaatab ega eelmiselt ringilt k6rgema prioriteedi asja ei kasutanud juba
						if(is_array($yhis = array_intersect($groups, $gl)) && sizeof(array_intersect($groups, $gl)))
						{
							$use_group_pri = 0;
							foreach($yhis as $yhisgrp)
							{
								if($user_pri[$yhisgrp] > $use_group_pri)
								{
									$use_group_pri = $user_pri[$yhisgrp];
								}
							}
							if($use_group_pri > $used_priority)
							{
								$used_priority = $use_group_pri;
							}
							else
							{
								continue;
							}
						}
					
						$from = $bargain->prop("time_from");
						$to = $bargain->prop("time_to");//arr(mktime($from["hour"], $from["minute"], 0, date("m",$start), date("d",$start), date("y",$start))); arr(mktime($to["hour"], $to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))); arr($start);arr($end);
						//juhul kui aeg mahub t2pselt soodushinna sisse
						if(mktime($from["hour"], $from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) <=  $start && mktime($to["hour"], $to["minute"], 0, date("m",$end), date("d",$end), date("y",$end)) >=  $end)
						{
							$ret = 0.01*$bargain->prop("bargain_percent");
							$priority = $bargain->prop("priority");
							continue;
						}
						//juhul kui m6ni kattub poolikult... esimene siis , et kui allahindlus algul on,... teine, et allahindlus tuleb poolepealt
						if((mktime($from["hour"], $from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) <=  $start) && (mktime($to["hour"], $to["minute"], 0, date("m",$end), date("d",$end), date("y",$end)) > $start))
						{
							$ret = 0.01*$bargain->prop("bargain_percent")*(mktime($to["hour"], $to["minute"], 0, date("m",$end), date("d",$end), date("y",$end)) - $start)/($end-$start);
							continue;
						}
						if((mktime($to["hour"], $to["minute"], 0, date("m",$end), date("d",$end), date("y",$end)) >=  $end) && (mktime($from["hour"], $from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) < $end))
						{
							$ret = 0.01*$bargain->prop("bargain_percent")*($end - mktime($from["hour"], $from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)))/($end-$start);
							continue;
						}
					}
				}
			}
		}
		exit_function("room::get_bargain");
		return $ret;
	}
	
	/**
		@attrib params=name
		@param prices required type=array
			price objects.... keys are price->prop(time)
		@param time required type=int
			time left ... still without tax
		@param end required type=int
			event ending time
		@return object
			price object... largest of smaller times... or smallest of larger times
	**/
	function get_best_time_in_prices($arr)
	{//arr($arr);
		extract($arr);
		$largest = "";
		$smaller = "";
		$prices_to_use_when_situation_is_hopeless = array();
		$start = $arr["end"] - $time;//arr($start); arr($end);arr()
		//arr($start);arr(date("G:i",$start));
		foreach($prices as $key => $price)
		{//arr($time);
			//jube porno.... testib kas hinna ajastus kattub j2rgneva ajaga
			$time_from = $price->prop("time_from");
			$time_to = $price->prop("time_to");
			$end = $start + $price->prop("time") * $this->step_length;//arr("/");arr($end);arr("\\");
			if(!($time_to == $time_from) && !((mktime($time_from["hour"], $time_from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) <= $start) && 
			     (mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))>= ($start + $price->prop("time") * $this->step_length))
			))
			{
				if((mktime($time_from["hour"], $time_from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) <= $start) || 
					(mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))>= ($start + $price->prop("time") * $this->step_length))
				)//kui miskeid t2is aegu ei ole, siis l2hevad poolikud hiljem kasutusse
				{
					$prices_to_use_when_situation_is_hopeless[] = $price;
				}
				continue; //siia tuleb mingi eriti synge kood, mis peaks hindu ajaliselt tykeldama hakkama ....
			}
	//		if(aw_global_get("uid") == "struktuur"){
	//		arr((mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))>= ($start + $price->prop("time") * $this->step_length)));
	//		arr($price->prop("time")); arr($this->step_length);
	//		arr(date("G:i",mktime($time_from["hour"], $time_from["minute"], 0, date("m",$start), date("d",$start), date("y",$start))));arr(date("G:i",$start)); arr(date("G:i",$start + $price->prop("time") * $this->step_length));  arr(date("G:i",mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))));
	//		arr($price->prop("time") * $this->step_length); arr($time);arr("");
	//		}
			if($time + 60 >= ($price->prop("time") * $this->step_length) && (!$smaller || ($smaller->prop("time") < $price->prop("time"))))
			{
				$smaller = $price;
			}
			if(($time <= ($price->prop("time") * $this->step_length)+ 60) && (!$larger || ($larger->prop("time") > $price->prop("time"))))
			{
				$larger = $price;
			}
		}
		if(is_object($smaller))
		{
			return $smaller;
		}
		elseif(is_object($larger))
		{
			return $larger;
		}
		else
		{
			$arr["prices"] = $prices_to_use_when_situation_is_hopeless;
			return $this->get_half_prices($arr);
		}
	}
	
	//parem 2ra yrita aru saada mis see pooletoobine funktsioon teeb.... loodame lihtsalt, et kunagi seda vaja ei l2he
	function get_half_prices($arr)
	{
		extract($arr);
		$sum = 0;
		$start = $arr["end"] - $time;
		$half_obj = "";
		foreach($arr["prices"] as $price)
		{
			$time_from = $price->prop("time_from");
			$time_to = $price->prop("time_to");
			$end = $start + $price->prop("time") * $this->step_length;
			if(mktime($time_from["hour"], $time_from["minute"], 0, date("m",$start), date("d",$start), date("y",$start)) <= $start)
			{
				//p n2itab kui suur osa summast ja ajast kasutusse l2heb
				$p = (mktime($time_to["hour"], $time_to["minute"], 0, date("m",$start), date("d",$start), date("y",$start))-$start)/($price->prop("time") * $this->step_length);
				$half_obj = new object();
				$half_obj->set_parent($price->id());
				$half_obj->set_class_id(CL_ROOM_PRICE);
				$meta_prices = ($price->meta("prices"));
				$half_obj->set_prop("time", (mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))-$start)/$this->step_length);
				foreach($meta_prices as $curr => $sum)
				{
					$meta_prices[$curr] = $sum * $p;
					$half_obj->set_meta("prices", $meta_prices);
 				}
			}
			if(mktime($time_to["hour"], $time_to["minute"], 0, date("m",$end), date("d",$end), date("y",$end))>= ($start + $price->prop("time") * $this->step_length))
			{
				;//kui loomingulisem hoog peale tuleb, saab siia miskit toredat lisada
			}
		}
		return $half_obj;
	}
	
	/**
		@attrib name=cal_product_reserved_time params=name all_args=1 nologin=1
		@param room required type=oid
			products and their amounts
		@param oid required type=oid
			product
		@return int
			min time to reserve
			0 if product not set or product reservation time is not set
	**/
	function cal_product_reserved_time($arr)
	{
		extract($arr);
		if(is_oid($arr["oid"]) && $this->can("view", $arr["oid"]))
		{
			$product=obj($arr["oid"]);
			$ba = $this->_get_room_prod_after_buffer(array("id" => $arr["oid"] , "room" => $arr["room"]));
			return ($product->prop("reservation_time")*$product->prop("reservation_time_unit")+$ba);
		}
		return 0;
	}
	
	function get_company_currency($room)
	{
		if(!is_oid($room) || !$this->can("view" , $room))
		{
			return null;
		}
		$room_object = obj($room);
		return $room_object->prop("owner.currency");
	}
	
	/**
		@attrib params=name
		@param products required type=array
			products and their amounts
		@param currency optional type=oid
			if you want result in not the same currency the company uses.
		@param prod_discount optional type=int
		@param room optional type=object
			room object
		@param bron optional type=object
			bron object, takes prod price and discount from bron
		@param start optional type=int
			timestamp , reservation starts (needed for product discounts)
		@param end optional type=int
			timestamp , reservation ends (needed for product discounts)
		@return int
			price of all products
		@example
			$room_inst = get_instance(CL_ROOM);
			$currency = $room_inst->get_company_currency($room->id());
			$products = array(10121 => 1, 10124 => 3);
			$tmp = $room_inst->cal_products_price(array(
				"products" => $products,
				"currency" => $currency,
				"prod_discount" => 10,
				"room" => $room,
			));
	**/
	function cal_products_price($arr)
	{
		extract($arr);
		$this->last_discount = 0;
		if(is_array($products) && sizeof($products))//kui tooteid pole, v6iks selle osa vahele j2tta... v6ibolla v6idab paar millisekundit
		{
			$bron_inst = get_instance(CL_RESERVATION);

			if(is_object($room) && !$prod_discount)
			{
				$prod_discount = $this->get_prod_discount(array(
					"room" => $room->id(),
					"start" => $start,
					"end" => $end,
					"bron" => $bron,
				));
			}
			if(!$prod_discount && is_object($bron))
			{
				$discount_array = $bron_inst->get_product_discount($bron->id());
			}

			if(is_object($bron) && ($total_price_set = $bron_inst->get_products_price(array("reservation" => $bron))))
			{
				$sum = $total_price_set;
			}
			else
			{
				$sum = 0;
				foreach($products as $id => $amt)
				{
					if($amt && $this->can("view", $id))
					{
						$product = obj($id);
						if($prod_discount)
						{
							if(is_oid($currency))
							{
								$cur_pr = $product->meta("cur_prices");
								if($cur_pr[$currency])
								{
									$sum += $cur_pr[$currency] *  $amt;
								}
								else $sum += $product->prop("price") * $amt;
							}
						}
						else
						{
							$this_product_doscount = $discount_array[$id];
							$cur_pr = $product->meta("cur_prices");
							$cost = 0;
							if($cur_pr[$currency])
							{
								$cost = $cur_pr[$currency] *  $amt;
							}
							else $cost = $product->prop("price") * $amt;
							$sum += $cost - $this_product_doscount * 0.01 * $cost;
							$this->last_discount+= $cost*0.01*$this_product_doscount;
						}
					}
				}
			}
				//v6tab toote hinnalt toodete allahindluse maha
			if($prod_discount)
			{
				$this->last_discount = $sum*0.01*$prod_discount;
				$sum = $sum-$this->last_discount;
			}
		}
		
		//statistika jaoks arvutaks miski keskmise sooduse toodetele jne
		if(!$prod_discount)
		{
			$this->average_discount_for_products = ($this->last_discount / ($sum + $this->last_discount)) * 100;
		}

		//ja juhul kui j22b alla miinimumi, siis j22b miinimum
		if(is_object($room) && is_oid($currency))
		{
			$min = $room->meta("web_min_prod_prices");
			if($sum < $min[$currency])
			{
				if(!$prod_discount)//tegelt ei tea kas seda tahabki keegi n2ha...a teeb miski keskmise hinnasooduse arvutuse kui l2heb k2iku miinimumhind
				{
					$this->average_discount_for_products = ((($sum + $this->last_discount) - $min[$currency]) / $min[$currency]) * 100;
					if(!($this->average_discount_for_products > 0))
					{
						$this->average_discount_for_products = 0;
					}
				}
				$sum = $min[$currency];
			}
		}

		return $sum;
	}

	function get_prod_discount($arr)
	{
		extract($arr);
		if (is_object($bron) && $bron->prop("products_discount"))
		{
			 return $bron->prop("products_discount");
		}
		if(is_oid($room) && $this->can("view" , $room))
		{
			$o = obj($room);
		}
		if(!is_object($o))
		{
			return 0;
		}
		$prod_discount = 0;
		$warehouse = $o->prop("warehouse");
		if($o->prop("prod_discount_loc"))
		{
			if($o->prop("prod_web_discount"))
			{
				$prod_discount = $o->prop("prod_web_discount");
			}
			else
			{
				$prod_discount = $this->get_rnd_discount_in_time(array("start" => $start, "end" => $end, "room" => $o, "group" => $group));
			}
		}
		else
		{
			if(is_oid($warehouse) && $this->can("view" , $warehouse))
			{
				$w_obj = obj($warehouse);
				$w_cnf = obj($w_obj->prop("conf"));
				if(is_oid($w_obj->prop("order_center")) && $this->can("view" , $w_obj->prop("order_center")))
				{
					$soc = obj($w_obj->prop("order_center"));
					$prod_discount = $soc->prop("web_discount");
				}
			}
		}
		return $prod_discount;
	}
	
//kui ruumile tekitada muurtuja check_for_people , siis annab tulemuseks arvu palju inimesi mahub, juhul kui on ruumile v6imalik mitu broneeringut teha
	function check_from_table($arr)
	{
		enter_function("room::check_from_table");
		$ret = 1;
		if($this->max_capacity && $this->allow_multiple && $this->check_for_people)
		{
			$calc_number = 1;
			$ret = $this->max_capacity;
		}
		foreach($this->res_table as $key => $val)
		{
			if($key > $arr["end"])
			{
				exit_function("room::check_from_table");
				return $ret;
			}
			if($val["end"] > $arr["start"])
			{//arr(date("h:i" , arr($key))); arr(date("h:i", $arr["end"]));arr($val);
				if($key < $arr["end"])
				{
					//on juba sellele ajale kinnitatud broneeringu leidnud
					$this->last_bron_id = $val["id"];
					$this->is_buffer = $val["going_to_be_after_buffer"];	
					if($val["real_end"] <= $arr["start"])
					{
						$this->is_after_buffer = 1;
					}
					else
					{
						$this->is_after_buffer = 0;
					}
					if($val["real_start"] >= $arr["end"])
					{
						$this->is_before_buffer = 1;
					}
					else
					{
						$this->is_before_buffer = 0;
					}
					if($calc_number)
					{
						$ret = $ret - $val["people"];
					}
					else
					{
						if($val["verified"] && !$this->is_after_buffer && !$this->is_before_buffer) // juhul kui pole j2relpuhver ja on kinnitatud, siis pole vaja enam edasi otsida, v6tab eelduseks ka , et pole miski seotud ruumi bronn
						{
							exit_function("room::check_from_table");
							return false;
						}
						else
						{
							$ret = false;
						}
					}
				}
			}
			if($ret < 0 ||  $ret < $this->check_for_people) $ret = false;
		}
		exit_function("room::check_from_table");
		return $ret;
	}


	private function get_extra_res_from_table($start , $end)
	{
		$ret = 0;
		foreach($this->extra_res_table as $key => $val)
		{
			if($key > $end)
			{
				return $ret;
			}
			if($val["end"] > $start)
			{//arr(date("h:i" , arr($key))); arr(date("h:i", $arr["end"]));arr($val);
				if($key < $end)
				{
					if($val["verified"] && !$val["slave"]) // juhul kui pole j2relpuhver ja on kinnitatud, siis pole vaja enam edasi otsida, v6tab eelduseks ka , et pole miski seotud ruumi bronn
					{
						return $val["id"];
					}
					else
					{
						$ret = $val["id"];
					}
				}
			}
		}
		return $ret;
	}

	function generate_res_table($room, $start = 0, $end = 0,$un = 0)
	{
		enter_function("room::generate_res_table");
		$this->max_capacity = $room->prop("max_capacity");
		$this->allow_multiple = $room->prop("allow_multiple");
		if(!$this->start)
		{
			classload("core/date/date_calc");
			$this->start =get_week_start();
		}

		if ($start == 0)
		{
			$start = $this->start;
		}
		if ($end == 0)
		{
			$end = $this->start + (7*24*3600);
		}
		$step_length = $this->step_lengths[$room->prop("time_unit")];

		$room_selection = $room->id();
		if(sizeof($room->get_other_rooms_selection()))
		{
			$other_rooms = array_keys($room->get_other_rooms_selection());
			$room_selection = $other_rooms;
			$room_selection[] = $room->id();
		}

		$filt = array(
			"class_id" => array(CL_RESERVATION),
			"lang_id" => array(),
			"resource" => $room_selection,
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS, $end),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, $start),
		);

		if($un)
		{
			;
		}
		else
		{
			$filt[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"verified" => 1,
					"deadline" => new obj_predicate_compare(OBJ_COMP_GREATER, time())
				)
			));
		}

		$use_prod_times = $room->prop("use_product_times");
		$reservations = new object_list($filt);
		$this->res_table = array();
		$customers = array();
		foreach($reservations->arr() as $res)
		{
			if($res->prop("type"))//miskid eri tyypi bronnide ajad j2tab vabaks
			{
				continue;
			}
			$this_is_slave = 0;
			if(is_array($other_rooms) && in_array($res->prop("resource") , $other_rooms))
			{
				$this_is_slave = 1;
			}

			if($use_prod_times)
			{
				list($before_buf, $after_buf) = $this->get_products_buffer(array("bron" => $res, "time" => "both"));
				$start = $res->prop("start1")-$before_buf;
				$this->res_table[$start]["end"] = $res->prop("end") + $after_buf;
			}
			else
			{
				$start = $orig_start = $res->prop("start1")-$room->prop("buffer_before")*$room->prop("buffer_before_unit");
				$end = $res->prop("end") + $room->prop("buffer_after")*$room->prop("buffer_after_unit");
				if($this->res_table[$start])//kontrollib kas tabelis sama aja peal on juba m6ni kinnitatud broneering... v6ibolla siis pole vaja yldse n2idata
				{
					if($this->res_table[$start]["verified"] || $this_is_slave)
					{
						if($this->res_table[$start]["end"] < $end)
						{
							$start = $this->res_table[$start]["end"];
						}
						else
						{
							continue;
						}
					}
				}
				$this->res_table[$start]["end"] = $end;
				//tekitab eelnevale v6i eelnevatele cellidele n8. broneeringu, mis on lihtsalt broneeritud buffriks
				if($room->prop("buffer_after"))
				{
					$buff_start = $orig_start-$room->prop("buffer_after")*$room->prop("buffer_after_unit");
					if($this->res_table[$buff_start]["verified"])
					{
						if($this->res_table[$buff_start]["end"] < $start)
						{
							$buff_start = $this->res_table[$buff_start]["end"];
						}
						else
						{
							unset($buff_start);
						}
					}
					if($buff_start)
					{
						$this->res_table[$buff_start]["end"] = $start;
						$this->res_table[$buff_start]["going_to_be_after_buffer"] = 1;
					}
				}
			}
			if($res->prop("verified"))
			{
				$this->res_table[$start]["verified"] = 1;
			}
			$this->res_table[$start]["real_end"] = $res->prop("end");
			$this->res_table[$start]["real_start"] = $res->prop("start1");
			$this->res_table[$start]["id"] = $res->id();
			$this->res_table[$start]["people"] = $res->prop("people_count");
			if($this_is_slave)
			{
				$this->res_table[$start]["slave"] = 1;
			}
			$customers[] = $res->prop("customer");
		}
		ksort($this->res_table);//if(aw_global_get("uid") == "markop") arr($this->res_table);
		if (count($customers))
		{
			$cust_ol = new object_list(array(
				"oid" => $customers,
				"lang_id" => array(),
				"site_id" => array()
			));
			$custs = $cust_ol->arr();
		}
		exit_function("room::generate_res_table");
	}


	private function gen_extra_res_table($room, $start = 0, $end = 0,$un = 0)
	{
		enter_function("room::generate_extra_res_table");

		$reservations = $room->get_extra_reservations($start , $end);

		$this->extra_res_table = array();

		foreach($reservations->arr() as $res)
		{
			$start = $orig_start = $res->prop("start1")-$room->prop("buffer_before")*$room->prop("buffer_before_unit");
			$end = $res->prop("end") + $room->prop("buffer_after")*$room->prop("buffer_after_unit");
			if($this->extra_res_table[$start]["verified"])//kontrollib kas tabelis sama aja peal on juba m6ni kinnitatud broneering... v6ibolla siis pole vaja yldse n2idata
			{
				if($this->extra_res_table[$start]["end"] < $end)
				{
					$start = $this->extra_res_table[$start]["end"];
				}
				else
				{
					continue;
				}
			}
			$this->extra_res_table[$start]["end"] = $end;
			//tekitab eelnevale v6i eelnevatele cellidele n8. broneeringu, mis on lihtsalt broneeritud buffriks
			if($room->prop("buffer_after"))
			{
				$buff_start = $orig_start-$room->prop("buffer_after")*$room->prop("buffer_after_unit");
				if($this->extra_res_table[$buff_start]["verified"])
				{
					if($this->extra_res_table[$buff_start]["end"] < $start)
					{
						$buff_start = $this->extra_res_table[$buff_start]["end"];
					}
					else
					{
						unset($buff_start);
					}
				}
				if($buff_start)
				{
					$this->extra_res_table[$buff_start]["end"] = $start;
					$this->extra_res_table[$buff_start]["going_to_be_after_buffer"] = 1;
				}
			}

			if($res->prop("verified"))
			{
				$this->extra_res_table[$start]["verified"] = 1;
			}
			$this->extra_res_table[$start]["real_end"] = $res->prop("end");
			$this->extra_res_table[$start]["real_start"] = $res->prop("start1");
			$this->extra_res_table[$start]["id"] = $res->id();
			$this->extra_res_table[$start]["people"] = $res->prop("people_count");
			if(is_array($other_rooms) && in_array($res->prop("resource") , $other_rooms))
			{
				$this->extra_res_table[$start]["slave"] = 1;
			}
		}
		ksort($this->extra_res_table);//if(aw_global_get("uid") == "struktuur") arr($this->extra_res_table);
//arr(sizeof($this->extra_res_table));
		exit_function("room::generate_extra_res_table");
	}



//seda kasutada kalendrivaatel kontrollimiseks kas miskit n2ha on, kui vaja kontrollida, kas saab salvestada teatud ajale, siis kasutada ruumi objekti juurest is_available funktsiooni
	/** checks if the room is available 
		@attrib params=name api=1
		@param room required type=oid
			room id
		@param start required type=int
		@param end required type=int
		@param ignore_booking optional type=int
			If given, the booking with this id will be ignored in the checking - this can be used for changing booking times for instance
		@param type optional type=string
			reservation type (ignores other types)
		@return boolean
			true if available
			false if not available
	**/
	function check_if_available($arr)
	{


/*if(aw_global_get("uid") == "struktuur")
{

	$ol = new object_list(array("class_id" => CL_RESERVATION, 
		"created" => new obj_predicate_compare(OBJ_COMP_LESS, (time() - (4*3600*24))),
		//"oid" => new obj_predicate_compare(OBJ_COMP_LESS, 1400),
		"resource" => 965,
	));
	foreach($ol->arr() as $o)
	{arr($o->id());
		//$o->delete();
	}
}
*/

		if(is_array($this->res_table))
		{
			return $this->check_from_table($arr);
		}
		extract($arr);
		if(!($start > 1) && !($end > 1))
		{
			return true;
		}
		if(!(is_oid($room) && $this->can("view" , $room)))
		{
			return false;
		}
		$room = obj($room);
		$set = $this->get_settings_for_room($room);
		$tm = 600000;
		if ($set->prop("cal_refresh_time") > 0)
		{
			$tm = $set->prop("cal_refresh_time") * 60000;
		}
		$buff_before = $room->prop("buffer_before")*$room->prop("buffer_before_unit");
		$buff_after = $room->prop("buffer_after")*$room->prop("buffer_after_unit");
	
		//tootep6hisel ruumi broneerimisel
		if($room->prop("use_product_times"))
		{
			$last_bron = $this->get_last_bron(array("room" => $room , "start" => $start));
			$next_bron = $this->get_next_bron(array("room" => $room , "end" => $end));
			$buffer_start = $this->get_products_buffer(array("bron" => $last_bron, "time" => "after"));
			$buffer_end = $this->get_products_buffer(array("bron" => $next_bron, "time" => "before"));
		}
		else
		{
		//	$buffer = $buffer_end = $buffer_start = $buff_before + $buff_after;
			$buffer_end = $buff_before;
			$buffer_start =$buff_after;
		}

		$buffer = $buff_before+$buff_after;
		$filt = array(
			"class_id" => array(CL_RESERVATION),
			"lang_id" => array(),
			"resource" => $room->id(),
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS, ($end+$buffer_end)),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, ($start-$buffer_start)),
		);

		if(!empty($arr["type"]) && strlen($arr["type"]))
		{
			$filt["type"] = $arr["type"];
		}
		else
		{
			$filt["type"] = new obj_predicate_compare(OBJ_COMP_EQUAL, '');
		}

		if (!empty($arr["ignore_booking"]))
		{
			$filt["oid"] = new obj_predicate_not($arr["ignore_booking"]);
		}
		$reservations = new object_list($filt);
		//ueh... filter ei t88ta, niiet .... oehjah
		$verified_reservations = new object_list();
		foreach($reservations->arr() as $res)
		{
			if($res->prop("verified"))
			{
				$verified_reservations->add($res->id());
//				$reservations->remove($res->id());
			}
			elseif(!(is_object($set) && $set->prop("show_unverified")) && !($res->prop("deadline") > time()))
			{
				$reservations->remove($res->id());
			}
	//		$booked[] = array("start" => $res->prop("start1"), "end" => $res->prop("end"));
		}

		if(!sizeof($reservations->arr()))
		{
			return true;
		}
		else
		{
			if(sizeof($verified_reservations->arr()))
			{
				$bron = reset($verified_reservations->arr());
			}
			else
			{
				$bron = reset($reservations->arr());
			}	
			$this->last_bron_id = $bron->id();
			return false;
		}
	}
	
	/** returns int (reservation products buffer time)
		@attrib api=1 params=name
		@param $bron required type=object
			The reservation object
		@param $time optional type=string
			if "before" , calculates before buffer times, if "after", calculates after buffer times, if not set, calculates both, if set to "both" , returns array (before,after)
		@returns Int/Array
	**/
	function get_products_buffer($arr)
	{
		extract($arr);
		if(!is_object($bron))
		{
			return 0;
		}
		static $cache;
		if (isset($cache[$bron->id()][$time]))
		{
			return $cache[$bron->id()][$time];
		}
		$products = $bron->meta("amount");
		$ret = 0;
		$r_b = 0;
		$r_a = 0;
		if(is_array($products))
		{
			foreach($products as $product=> $amount)
			{
				if($amount && $this->can("view" , $product))
				{
					$prod = obj($product);
					if(!$time)
					{
						$ret = $ret + $this->_get_room_prod_before_buffer(array("id" => $prod->id() , "room" => $bron->prop("resource"))) + $this->_get_room_prod_after_buffer(array("id" => $prod->id() , "room" => $bron->prop("resource")));
					}
					else
					if ($time == "both")
					{
						$r_b = $r_b + $this->_get_room_prod_before_buffer(array("id" => $prod->id() , "room" => $bron->prop("resource")));
						$r_a = $r_a + $this->_get_room_prod_after_buffer(array("id" => $prod->id() , "room" => $bron->prop("resource")));// $prod->prop("buffer_time_after")*$prod->prop("buffer_time_unit");
					}
					elseif($time == "before")
					{
						$ret = $ret + $this->_get_room_prod_before_buffer(array("id"=> $prod->id(), "room" => $bron->prop("resource")));//->prop("buffer_time_".$time)*$prod->prop("buffer_time_unit");
					}
					else
					{
						$ret = $ret + $this->_get_room_prod_after_buffer(array("id"=> $prod->id(), "room" => $bron->prop("resource")));//$prod->prop("buffer_time_".$time)*$prod->prop("buffer_time_unit");
					}
				}
			}
		}
		if ($time == "both")
		{
			$ret = array($r_b, $r_a);
		}
		$cache[$bron->id()][$time] = $ret;
		return $ret;
	}
	
	//id , room
	function _get_room_prod_after_buffer($arr)
	{
		extract($arr);
		$product=obj($id);
		if(is_oid($room))
		{
			$prod_data = $this->get_prod_data_for_room($room);
			if(isset($prod_data[$id]["ba"]) && !($prod_data[$id]["ba"] == ""))
			{
				$ba = $prod_data[$id]["ba"];
			}
		}
		if(!isset($ba))
		{
			$ba = $product->prop("buffer_time_after");
		}
		return $product->prop("buffer_time_unit")*$ba;
	}
	
	//id , room
	function _get_room_prod_before_buffer($arr)
	{
		extract($arr);
		$product=obj($id);
		if(is_oid($room))
		{
			$prod_data = $this->get_prod_data_for_room($room);
			if(isset($prod_data[$id]["bb"]))
			{
				$bb = $prod_data[$id]["bb"];
			}
		}
		if(!isset($bb))
		{
			$bb = $product->prop("buffer_time_before");
		}
		return $product->prop("buffer_time_unit")*$bb;
	}
	
	/** returns object (last bron object before start time)
		@attrib api=1 params=name
		@param $room required type=object
			The room object
		@param $start required type=int
			last reservation before that timestamp
		@returns object
	**/
	function get_last_bron($arr)
	{
		extract($arr);
		$ret = ""; $max = $start - 24*3600;
		$reservations = new object_list(array(
			"class_id" => array(CL_RESERVATION),
			"lang_id" => array(),
			"resource" => $room->id(),
			"end" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, ($start - 24*3600) , $start),
			"verified" => 1,
		));
		
		foreach($reservations->arr() as $res)
		{
			if($res->prop("end") > $max)
			{
				$ret = $res; $max = $res->prop("end");
			}
		}
		return $ret;
	}
	
	/** returns object (first reservation object after end time)
		@attrib api=1 params=name
		@param $room required type=object
			The room object
		@param $end required type=int
			first reservation object after that timestamp
		@returns Object
	**/
	function get_next_bron($arr)
	{
		extract($arr);
		$ret = ""; $min = $end + 24*3600;
		$reservations = new object_list(array(
			"class_id" => array(CL_RESERVATION),
			"lang_id" => array(),
			"resource" => $room->id(),
			"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $end, ($end + 24*3600)),
		//	"start1" => new obj_predicate_compare(OBJ_COMP_LESS, ()),//24h hiljem pole ka vaja enam
			"verified" => 1,
		));
		foreach($reservations->ids() as $id)
		{
			$res = obj($id);
			if($res->prop("start1") < $min && $res->prop("start1")>100)
			{
				$ret = $res; $min = $res->prop("start1");
			}
		}
		return $ret;
		
	}
	
	function callback_generate_scripts($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		if ($arr["request"]["group"]=="calendar")
		{
			$out = '
			$.timer(60000, function (timer){
				$.ajax({
					type: "POST",
					url: "/orb.aw?class=room&action=update_calendar_table&room="+$.gup("id"),
					data: "",
					success: function(msg){
						$(".sisu").html(msg);
					}
				});
			});

			';
		}
		$set = $this->get_settings_for_room($arr["obj_inst"]);
		$tm = 600000;
		if ($set->prop("cal_refresh_time") > 0)
		{
			$tm = $set->prop("cal_refresh_time") * 60000;
		}
		return $out . 'doLoad('.$tm.');
			var sURL = unescape(window.location.href);
			function doLoad()
			{
			setTimeout( "refresh()", '.$tm.' );
			}
			function refresh()
			{
				window.location.reload();
			}
	
			function confirm_delete(field,url,change_var)
			{
				fRet=confirm("'.t("Olete kindel et kustutada ").":".'" + document.getElementById(field).options[document.getElementById(field).selectedIndex].text);
				if(fRet)
				{
					window.location.href=url + "&" + change_var + "="+document.getElementById(field).value;
				}
				;
			}
		';
	}

	function cb_gen_web_min_prices($arr)
	{return PROP_IGNORE;
		$cur = $arr["obj_inst"]->prop("currency");
		$prices = $arr["obj_inst"]->meta("web_min_prod_prices");
		foreach(safe_array($cur) as $cur)
		{
			if (!is_oid($cur))
			{
				continue;
			}
			$c = obj($cur);
			$retval["wpm_currency[".$cur."]"] = array(
                               "name" => "wpm_currency[".$cur."]",
                               "type" => "textbox",
                               "caption" => sprintf(t("Min hind toodetele veebis (%s)"), $c->prop("unit_name")),
                               "value" => $prices[$cur],
                               "editonly" => 1,
			       "size" => 5,
//				"parent" => "min_price_prod"
                        );
		}
		return $retval;
	}

	function gen_people_prices($arr)
	{
		$cur = $arr["obj_inst"]->prop("currency");
		$prices = $arr["obj_inst"]->meta("people_prices");
		$prices[] = "";
		$n = 1;
		$rows = array();
		classload("vcl/table");
		$t = new vcl_table(array(
			"layout" => "generic",
		));		
		$t->define_field(array(
			"name" => "curr",
			"caption" => t("valuuta"),
		));
		foreach($prices as $price)
		{
			foreach($cur as $currency)
			{
				if (!is_oid($currency))
				{
					continue;
				}
				
				$rows[$currency][$n] = html::textbox(array(
					"name" => "people_prices[".$n."][".$currency."]",
					"value" => $prices[$n][$currency],
					"size" => 3,
				));
			}
			$t->define_field(array(
				"name" => $n,
				"caption" => $n.t(". lisainimesele"),
			));
			$n++;
		}
		foreach($rows as $curr => $row)
		{
			$c = obj($curr);
			$data = array("curr" => $c->name());
			foreach($row as $num => $value)
			{
				$data[$num] = $value;
			}
			$t->define_data($data);
		}
		return $t->draw();
	}

        /**
                @attrib name=delete_cos
        **/
        function delete_cos($arr)
        {
                object_list::iterate_list($arr["sel"], "delete");
                return $arr["post_ru"];
        }

	function gen_min_prices_props($arr)
	{return PROP_IGNORE;
		$curs = $arr["obj_inst"]->prop("currency");
		$prices = $arr["obj_inst"]->meta("web_room_min_price");
		$retval = array();
		foreach($curs as $cur)
		{
			if(!is_oid($cur))
			{
				continue;
			}
			$c = obj($cur);
			$retval["web_room_min_price[".$cur."]"] = array(
				"name" => "web_room_min_price[".$cur."]",
				"type" => "textbox",
				"size" => 4,
				"caption" => $c->prop("unit_name"),
				"value" => $prices[$cur],
				"editonly" => 1,
				"parent" => "min_prices"
			);
		}
		return $retval;
	}

	/** checks if the group bron time settings allow the bron to be changed/created in that time
		@attrib api=1 params=pos name=group_can_do_bron
		@param s required type=object
			room settings object
		@param tm required type=int
			timestamp
		@returns boolean
		@example 
			$room_inst = get_instance(CL_ROOM);
			$settings = $room_inst->get_settings_for_room($room_object);
			$start_step = time();
			if (!$room_inst->group_can_do_bron($settings, $start_step))
			{
				print("cannotdo...!");
			}
	**/
	function group_can_do_bron($s, $tm)
	{
		$gpt = $s->meta("grp_bron_tm");
		$grp = $this->ui->get_highest_pri_grp_for_user(aw_global_get("uid"), true);
		if (isset($gpt[$grp->id()]))
		{
			$t = $gpt[$grp->id()];
			if (!($t["from"] === null || $t["from"] === ""))
			{
				$from_sec = 0;
				$cur_tm = time();
				switch($t["from_ts"])
				{
					case "min":
						$from_sec = $t["from"] * 60;
						break;

					case "hr":
						$from_sec = $t["from"] * 3600;
						break;

					default:
					case "day":
						$cur_tm = get_day_start();
						$from_sec = $t["from"] * 3600 * 24;
						break;
				}
				$can_bron_to = $cur_tm + $from_sec;
				if ($tm > $can_bron_to)
				{
					return false;
				}
			}

			if (!($t["to"] === null || $t["to"] === ""))
			{
				$to_sec = 0;
				$cur_tm = time();
				switch($t["to_ts"])
				{
					case "min":
						$to_sec = $t["to"] * 60;
						break;

					case "hr":
						$to_sec = $t["to"] * 3600;
						break;

					default:
					case "day":
						$cur_tm = get_day_start();
						$to_sec = $t["to"] * 3600 * 24;
						break;
				}
				$can_bron_from = $cur_tm + $to_sec;
				if ($tm < $can_bron_from)
				{
					return false;
				}
			}
		}
		return true;
	}
	
	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function _get_mark_arrived_pop($arr, $settings)
	{
		if (!($arr["user"] != 1 && !$settings->prop("no_cust_arrived_pop")))
		{
			return "";
		}
		$bron_id = $this->last_reservation_arrived_not_set($arr["obj_inst"]);
		if($arr["user"] != 1 &&  is_array($bron_id) && count($bron_id) && !$settings->prop("no_cust_arrived_pop"))
		{
			$grp_settings = $settings->meta("grp_settings");
			$gl = aw_global_get("gidlist_pri_oid");
			asort($gl);
			$gl = array_keys($gl);
			$grp = $gl[1];
			if (count($gl) == 1)
			{
				$grp = $gl[0];
			}
			if (!$grp_settings[$grp]["ask_cust_arrived"])
			{
				return "";
			}
			$reservaton_inst = get_instance(CL_RESERVATION);
			$ret.="<script name= javascript>window.open('".$reservaton_inst->mk_my_orb("mark_arrived_popup", array("bron" => $bron_id,))."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=".(160*count($bron_id)).", width=300')
			</script>";
		}
		return $ret;
	}

	function _init_oh_t(&$t,$pause)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date_from",
			"caption" => t("Kehtib alates"),
			"align" => "center",
			"width" => "10%",
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "date_to",
			"caption" => t("Kehtib kuni"),
			"align" => "center",
			"width" => "10%",
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "apply_group",
			"caption" => t("Kehtib gruppidele"),
			"align" => "center",
			"width" => "50%"
		));
		$t->define_field(array(
			"name" => "oh",
			"caption" => $pause ? t("Pausid") : t("Avamisajad"),
			"align" => "center",
			"width" => "50%"
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "100"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "20"
		));
	}

	function _get_oh_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_oh_t($t);
		$t->set_caption(t("Avamisajad"));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			$i = $oh->instance();
			$t->define_data(array(
				"name" => $oh->name(),
				"apply_group" => $oh->prop_str("apply_group"),
				"oh" => $i->show(array("id" => $oh->id())),
				"edit" => html::get_change_url($oh->id(), array("return_url" => get_ru()), t("Muuda")),
				"oid" => $oh->id(),
				"date_from" => $oh->prop("date_from"),
				"date_to" => $oh->prop("date_to"),
			));
		}
	}


	function _get_ch_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_oh_t($t,1);
		$t->set_caption(t("Pausid"));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PAUSES")) as $c)
		{
			$oh = $c->to();
			$i = $oh->instance();
			$t->define_data(array(
				"name" => $oh->name(),
				"apply_group" => $oh->prop_str("apply_group"),
				"oh" => $i->show(array("id" => $oh->id())),
				"edit" => html::get_change_url($oh->id(), array("return_url" => get_ru()), t("Muuda")),
				"oid" => $oh->id(),
				"date_from" => $oh->prop("date_from"),
				"date_to" => $oh->prop("date_to"),
			));
		}
	}

	function _get_oh_tb($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif"
		));
		$t->add_menu_item(array(
			"parent" => "new",
			"text" => t("Avamisaeg"),
			"link" => html::get_new_url(CL_OPENHOURS, $arr["obj_inst"]->id(), array("alias_to" => $arr["obj_inst"]->id(), "reltype" => 44, "return_url" => get_ru())),
		));
		$t->add_menu_item(array(
			"parent" => "new",
			"text" => t("Paus"),
			"link" => html::get_new_url(CL_OPENHOURS, $arr["obj_inst"]->id(), array("alias_to" => $arr["obj_inst"]->id(), "reltype" => 45, "return_url" => get_ru())),
		));
		$t->add_menu_button(array(
			"name" => "search",
			"img" => "search.gif"
		));
		$url = $this->mk_my_orb("do_search", array(
			"pn" => "set_oh",
			"clid" => CL_OPENHOURS
		), "popup_search");
		$t->add_menu_item(array(
			"parent" => "search",
			"text" => t("Avamisaeg"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)"
		));
		$url = $this->mk_my_orb("do_search", array(
                        "pn" => "set_ps",
                        "clid" => CL_OPENHOURS
                ), "popup_search");
                $t->add_menu_item(array(
                        "parent" => "search",
                        "text" => t("Paus"),
                        "link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)"
                ));
		$t->add_button(array(
			"name" => "remove_oh",
			"tooltip" => t("Eemalda avamisaeg"),
			"img" => "delete.gif",
			"action" => "remove_images",
		));
	}

	/** Returns the openhours object for the current user, or null if none applies
		@attrib api=1
		@param room required type=object
		@returns Array
	**/
	function get_current_openhours_for_room($room)
	{
		$rv = array();
		$gl = aw_global_get("gidlist_oid");
		if(is_oid($room->prop("inherit_oh_from")) && $this->can("view" , $room->prop("inherit_oh_from")))
		{
			$room = obj($room->prop("inherit_oh_from"));
		}
		foreach($room->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			/*if ($oh->prop("date_from") > 100 && time() < $oh->prop("date_from"))
			{
				continue;
			}
			if ($oh->prop("date_to") > 100 && time() > $oh->prop("date_to"))
			{
				continue;
			}*/
			if (!is_array($oh->prop("apply_group")) || !count($oh->prop("apply_group")) || count(array_intersect($gl, safe_array($oh->prop("apply_group")))))
			{
				$rv[] = $oh;
			}
			//sel juhul kui yks on konkreetselt antud grupile m6juv, siis tagastaks selle ja ei hakkaks yldse edasi vaatamagi
			if (is_array($oh->prop("apply_group")) && count($oh->prop("apply_group")) && count(array_intersect($gl, safe_array($oh->prop("apply_group")))))
			{
				return array($oh);
			}
		}
		return count($rv) ? $rv : null;
	}

	/** Returns the openhours object for the current user, or null if none applies, for pauses in the room's schedule
		@attrib api=1
		@param room required type=object
		@returns Array
	**/
	function get_current_pauses_for_room($room)
	{
		$rv = array();
		$gl = aw_global_get("gidlist_oid");
		foreach($room->connections_from(array("type" => "RELTYPE_PAUSES")) as $c)
		{
			$oh = $c->to();
			/*if ($oh->prop("date_from") > 100 && time() < $oh->prop("date_from"))
			{
				//continue;
			}
			if ($oh->prop("date_to") > 100 && time() > $oh->prop("date_to"))
			{
				//continue;
			}*/
			if (!is_array($oh->prop("apply_group")) || !count($oh->prop("apply_group")) || count(array_intersect($gl, safe_array($oh->prop("apply_group")))))
			{
				$rv[] = $oh;
			}
		}
		return count($rv) ? $rv : null;
	}

	/** returns data about products for room
		@attrib name=get_prod_data_for_room api=1 params=pos
		@param room required type=object
		@returns array
	**/
	function get_prod_data_for_room($room)
	{
		if($this->prod_data_for_room[$room])
		{
			return $this->prod_data_for_room[$room];
		}
		if (!is_object($room))
		{
			return;
		}
		if ($this->can("view", $room->prop("inherit_prods_from")))
		{
			$room = obj($room->prop("inherit_prods_from"));
		}
		$this->prod_data_for_room[$room->id()] = $room->meta("prod_data");
		return $room->meta("prod_data");
	}		

	function callback_post_save($arr)
	{
		//natuke default asju, et saaks kohe ruumi kastuama hakata
		if(!empty($arr['new']))
		{
			$arr["obj_inst"]->set_prop("time_unit" , 2);
			$arr["obj_inst"]->set_prop("time_from" , 2);
			$arr["obj_inst"]->set_prop("time_to" , 5);	
			$arr["obj_inst"]->set_prop("time_step" , 1);
			$arr["obj_inst"]->set_prop("price" , array(2 => 2));
			
			$curr_object_list = new object_list(array(
				"class_id" => CL_CURRENCY,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$arr["obj_inst"]->set_prop("currency" , $curr_object_list->ids());
			$arr["obj_inst"]->set_prop("prod_discount_loc" , 0);
		
			$u = get_instance(CL_USER);
			$arr["obj_inst"]->set_prop("owner" , $u->get_current_company());
			$arr["obj_inst"]->set_prop("max_capacity" , 15);
			$this->_add_price_object(array(
				"room" => $arr["obj_inst"]->id(),
			));
			$this->_add_oh_object(array(
				"room" => $arr["obj_inst"]->id(),
			));
			$arr["obj_inst"]->save();
		}
		$i = get_instance("vcl/popup_search");
		$i->do_create_rels($arr["obj_inst"], $arr["request"]["set_oh"], 44);
		$i->do_create_rels($arr["obj_inst"], $arr["request"]["set_ps"], 45);
	}
	
	function _add_price_object($arr)
	{
		extract($arr);
		$room_obj = obj($room);
		$o = new object();
		$o->set_class_id(CL_OPENHOURS);
		$o->set_parent($room);
		$o->set_prop("date_from" , 100);
		$o->set_prop("date_to" , time()+(366*24*3600*10));
		$o->set_prop("openhours" , array(array("day1" => 1,"day2" => 7, "h1" => 6, "m1" => 0, "h2" => 22, "m2" => 0)));
		$o->save();
		$room_obj->connect(array(
			"to" => $o->id(),
			"reltype" => 44,
		));
	}
	
	function _add_oh_object($arr)
	{
		extract($arr);
		$room_obj = obj($room);
		$o = new object();
		$o->set_class_id(CL_ROOM_PRICE);
		$o->set_parent($room);
		$o->set_prop("type" , 1);
		$o->set_prop("active" , 1);
		$o->set_prop("date_from" , 100);
		$o->set_prop("date_to" , time()+(366*24*3600*10));
		$o->set_prop("weekdays" , Array(1,1,1,1,1,1,1,1));
		$o->set_prop("nr" , 1);
		$o->set_prop("time_to" , array("hour" => 23 , "minute" => 59));
		$o->set_prop("time_from" , array("hour" => 0 , "minute" => 0));
		$o->set_prop("time" , 1);
		$o->save();
		$room_obj->connect(array(
			"to" => $o->id(),
			"reltype" => 9,
		));
	}	
	
	function get_people_for_oh($o)
	{
		if(!sizeof($o->prop("professions")))
		{
			return new object_list();
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $o->prop("professions"),
					"oid" => $o->prop("professions"),
				),
			)),
			//"CL_CRM_PERSON.RELTYPE_RANK" => $o->prop("professions"),
		));
		return $ol;
	}
	
	function get_people_work_table($arr)
	{
		if($this->can("view" , $_GET["delete_scenario"]))
		{
			$del_c = obj($_GET["delete_scenario"]);
			$del_c->delete();
			die(
				'<script type="text/javascript">
				history.go(-1);
				</script>'
			);
		}
		$working_days = $arr["obj_inst"]->meta("working_days");
		classload("vcl/table");
		if($arr["month"])
		{
			$time = mktime(0,0,0,(date("m" , time()) +$arr["month"]) ,date("j" , time()), date("Y" , time()));
		}
		else
		{
			$time = time();
		}
		$t = new vcl_table;
		$x = 1;
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
		));
		$t->define_field(array(
			"name" => "extend",
			"caption" => t("Pikenda kehtivat graafikut"),
		));
		$t->define_field(array(
			"name" => "scenario",
			"caption" => t("Stsenaarium"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "20px",
		));

		$days = date("t" , $time);
		$soptions = array();
		$scenarios = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_WORKING_SCENARIO",
		));
		foreach($scenarios as $sc)
		{
			$soptions[$sc->prop("to")] = $sc->prop("to.name");
		}

		$pl = $this->get_people_for_oh($arr["obj_inst"]);

		$new_sc = html::href(array(
			"url" => html::get_new_url(
				CL_CRM_WORKING_TIME_SCENARIO,
				$arr["obj_inst"]->id(),
				array(
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 47,
					"return_url" => get_ru()
				)
			),
			"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/new.gif' border=0>",
			"title" => t("Lisa uus t&ouml;&ouml;aja stsenaarium"),
		));



		$url = $this->mk_my_orb("do_search", array(
			"pn" => "add_scenario",
			"clid" => CL_CRM_WORKING_TIME_SCENARIO,
		), "popup_search", false, true);

		$search_sc = html::href(array(
			"url" => "javascript:aw_popup_scroll(\"$url\",\"Otsing\",550,500)",
			"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
			"title" => t("Otsi")
		));

		if($arr["obj_inst"]->get_setting("show_only_my_graphs"))
		{
			$user = get_instance(CL_USER);
			$person = $user->get_current_person();	
			$ol = new object_list();
			if(in_array($person , $pl->ids()))
			{
				$ol->add($person);
			}
			$pl = $ol;
		}

		foreach($pl->arr() as $po)
		{
			$person_scenario = $po->meta("last_used_working_scenario");
			if(!$this->can("view" , $person_scenario))
			{
				$person_scenario = reset(array_keys($soptions));
			}

			$edit_sc = html::href(array(
				"url" => html::get_change_url($person_scenario, array("return_url" => get_ru())),
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif' border=0>",
					"title" => t("Muuda")
			));

			$delete_sc = html::href(array(
				"url" => 'javascript:confirm_delete("scenario['.$po->id().'][scenario]","'.aw_url_change_var('return_url' , '').'","delete_scenario")',
				//	window.location.href="'.aw_url_change_var("return_url" , "").'&delete_scenario="+document.getElementById("scenario[420][scenario]").value;',
				"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif' border=0>",
				"title" => t("Kustuta")
			));

			$data = array();
			$data["name"] = $po->name();
			$data["start"] = html::date_select(array(
				"name" => "scenario[".$po->id()."][start]",
				"value" => time(),
			));
			$data["end"] = html::date_select(array(
				"name" => "scenario[".$po->id()."][end]",
				"value" => time() + 24*3600*28,
			));

			$data["extend"] = html::button(array(
				"value" => "Pikenda",
				"onclick" => "document.changeform.extend.value=".$po->id().";submit_changeform();",
			));

			$data["scenario"] = html::select(array(
				"name" => "scenario[".$po->id()."][scenario]",
				"value" => $person_scenario,
				"options" => $soptions,
			)).$new_sc.$search_sc.($person_scenario ? $edit_sc : "").$delete_sc;
			$data["oid"] = $po->id();
			$t->define_data($data);
		}
		
		$submit = html::submit(array(
			"name" => "submit_scenario",
			"value" => t("koosta aegade tabel"),
		));

		return $t->draw().$submit; 
	}


	function get_people_table($arr)
	{
		$working_days = $arr["obj_inst"]->meta("working_days");
		classload("vcl/table");
		if($arr["month"])
		{
			$time = mktime(0,0,0,(date("m" , time()) +$arr["month"]) ,date("j" , time()), date("Y" , time()));
		}
		else
		{
			$time = time();
		}
		$t = new vcl_table;
		$x = 1;
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$days = date("t" , $time);
		while($x <= $days)
		{
			$t->define_field(array(
				"name" => "d".$x,
				"caption" => $x,
			));
			$x++;
		}
		$pl = $this->get_people_for_oh($arr["obj_inst"]);
		foreach($pl->arr() as $po)
		{
			$data = array();
			$data["name"] = $po->name();
			$x = 1;
			while($x <= $days)
			{
				$data["d".$x] = html::checkbox(array(
					"name" => "working_days[".$po->id()."][".date("Y" , $time).date("m" , $time).$x."]",
					"checked" =>  $working_days[$po->id()][date("Y" , $time).date("m" , $time).$x],
				));
				$data["d".$x].=html::hidden(array(
					"name" => "hidden_days[".$po->id()."][".date("Y" , $time).date("m" , $time).$x."]",
					"value" => 1,
				));
				$x++;
			}
			$t->define_data($data);
		}
		return $t->draw(); 
	}

	function up_link($month)
	{
	
		$month_name = t(date("F" , mktime(0,0,0,(date("m" , time()) +$month) ,date("j" , time()), date("Y" , time()))));

		$ret = html::href(array(
			"caption" => "<< " . t("Eelmine"),
			"url" => aw_url_change_var("month" , $month - 1)))." ".
		$month_name." ".
			html::href(array("caption" => t("J&auml;rgmine") . " >> " ,
			"url" => aw_url_change_var("month" , $month + 1)));
		return $ret;
	}
	
	/** returns workers
		@attrib name=get_day_workers params=pos api=1
		@param o required type=object
			room object
		@param time required type=int
			day timestamp
		@returns Object list
		@example
			$room_inst = get_instance(CL_ROOM);
			$worker_list = $room_inst->get_day_workers($room_object,$time());
	**/
	function get_day_workers($o , $time)
	{
		$working_days = $o->meta("working_days");
		$res = new object_list();
		foreach($working_days as $p => $val)
		{
			if(array_key_exists(date("Y" , $time).date("m" , $time).date("j" , $time) , $val))
			{
				$res->add($p);
			}
		}
		return $res;
	}

	function _get_prod_fld($obj)
	{
		$warehouse_id = $obj->prop("warehouse");
		if(is_oid($warehouse_id) && $this->can("view" ,$warehouse_id))
		{
			$warehouse = obj($warehouse_id);
			if(is_oid($warehouse->prop("conf")) && $this->can("view" ,$warehouse->prop("conf")))
			{
				$config = obj($warehouse->prop("conf"));
				$prod_fld = $config->prop("prod_fld");
			}
		}
		if(is_oid($prod_fld))
		{
			return $prod_fld;
		}
		else
		{
			return false;
		}
	}

	/** Returns array of time units
		@attrib api=1
		@comment
			Returns array of time units used by room.
			Array(
				identifier => caption,
			)
	 **/
	public function get_time_units()
	{
		return $this->time_unit_types;
	}


	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "aw_room")
		{
			$this->db_query("CREATE TABLE aw_room(aw_oid int primary key,
				category int
			)");
		}
		else
		{
			switch($f)
			{
				case "meta":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "text"
					));
					break;
				case "floor":
				case "corps":
				case "nr":
				case "normal_capacity":
				case "max_capacity":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "int"
					));
					break;
			}
			return true;
		}
		return false;
	}
}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_center.aw,v 1.45 2008/04/28 13:59:31 kristo Exp $
// procurement_center.aw - Hankekeskkond
/*

@classinfo syslog_type=ST_PROCUREMENT_CENTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects

#GENERAL
@default group=general
@groupinfo general2 caption="&Uuml;ldinfo" parent=general
@default group=general2

	@property name type=textbox
	@caption Nimetus

	@property owner type=text store=no
	@caption Omanik

	@property offerers_folder type=relpicker field=meta reltype=RELTYPE_PROCUREMENT_CENTER_FOLDERS
	@caption Pakkujate kataloog

@groupinfo settings caption="Seaded" parent=general
@default group=settings

@layout settings_l type=hbox

	@property search_date_subtract type=textbox size=3 parent=settings_l field=meta method=serialize
	@caption default kuup&auml;ev otsungus tagasi

	@property search_date_subtract_unit type=select no_caption=1 parent=settings_l field=meta method=serialize

	@property search_date_add type=textbox size=3 parent=settings_l field=meta method=serialize
	@caption default kuup&auml;ev otsungus edasi

	@property search_date_add_unit type=select no_caption=1 parent=settings_l field=meta method=serialize

	@property no_warehouse type=checkbox no_caption=1 field=meta method=serialize
	@caption Arvestab laoseisu

	@property procurement_templates multiple=1 type=relpicker field=meta method=serialize reltype=RELTYPE_PROCUREMENT_TEMPLATE
	@caption Hangete templeidid

	@property procurement_files_menu type=relpicker field=meta method=serialize reltype=RELTYPE_PROCUREMENT_FILES_MENU
	@caption Hangete failide men&uuml;&uuml;


@default group=p

	@property p_tb type=toolbar no_caption=1 store=no

	@layout p_l type=hbox width=30%:70%

		@layout p_left type=vbox parent=p_l

		@layout p_tree type=vbox parent=p_left closeable=1 area_caption=Hangete&nbsp;puu
			@property p_tr type=treeview no_caption=1 store=no parent=p_tree

		@layout p_search type=vbox parent=p_left closeable=1 area_caption=Hangete&nbsp;otsing
			@property procurements_find_name type=textbox store=no parent=p_search size=20 captionside=top
			@caption Nimi
			@property procurements_find_status type=select store=no parent=p_search captionside=top
			@caption Staatus
			@property procurements_find_offerer type=textbox store=no parent=p_search size=20 captionside=top
			@caption Pakkuja
			@property procurements_find_product type=textbox store=no parent=p_search size=20 captionside=top
			@caption Toode
			@property do_find_procurements type=submit store=no parent=p_search no_caption=1
			@caption Otsi
		@property p_tbl type=table no_caption=1 store=no parent=p_l

@default group=team

	@property team_tb type=toolbar store=no no_caption=1

	@property team_table type=table store=no no_caption=1

@groupinfo p caption="Hanked" submit=no
@groupinfo team caption="Meeskond" submit=no


@groupinfo offerers caption="Pakkujad"
@default group=offerers
groupinfo offerers_tree caption="Puuvaates" parent=offerers
default group=offerers_tree

	@property offerers_tb type=toolbar no_caption=1 store=no

	@layout offerers_lay type=hbox width=20%:80%

		@layout offerers_l type=vbox parent=offerers_lay
			@layout offerers_tree_l type=vbox parent=offerers_l closeable=1 area_caption=Pakkujate&nbsp;puu
				@property offerers_tr type=treeview no_caption=1 store=no parent=offerers_tree_l
			@layout offerers_find_params type=vbox parent=offerers_l closeable=1 area_caption=Pakkujate&nbsp;otsing
				@property offerers_find_name type=textbox store=no parent=offerers_find_params captionside=top
				@caption Nimi
				@property offerers_find_address type=textbox store=no parent=offerers_find_params captionside=top
				@caption Aadress
				@property offerers_find_keyword type=textbox store=no parent=offerers_find_params captionside=top
				@caption Keyword
				@property offerers_find_groups type=select store=no parent=offerers_find_params captionside=top
				@caption Hankijagruppid
				@property offerers_find_done type=checkbox store=no parent=offerers_find_params captionside=top no_caption=1
				@caption Teostanud hankeid
				@property offerers_find_start type=date_select store=no parent=offerers_find_params captionside=top
				@caption Alates
				@property offerers_find_end type=date_select store=no parent=offerers_find_params captionside=top
				@caption Kuni
				@property offerers_find_product type=textbox store=no parent=offerers_find_params captionside=top
				@caption pakutud Toode
				@property offerers_find_only_buy type=checkbox store=no parent=offerers_find_params captionside=top no_caption=1
				@caption Ainult ostudega
				@property do_find_offerers type=submit store=no parent=offerers_find_params captionside=top no_caption=1
				@caption Otsi
		@property offerers_tbl type=table no_caption=1 store=no parent=offerers_lay


@groupinfo offers caption="Pakkumised"
@default group=offers

	@property offers_tb type=toolbar no_caption=1 store=no

	@layout offers_l type=hbox width=30%:70%

		@layout offers_left type=vbox parent=offers_l

		@layout offers_tree type=vbox parent=offers_left closeable=1 area_caption=Pakkumiste&nbsp;puu
			@property offers_tr type=treeview no_caption=1 store=no parent=offers_tree

		@layout offers_find_params type=vbox parent=offers_left closeable=1 area_caption=Pakkumiste&nbsp;otsing
			@property offers_find_name type=textbox store=no parent=offers_find_params captionside=top
			@caption Hankija nimetus
			@property offers_find_address type=textbox store=no parent=offers_find_params captionside=top
			@caption Aadress
			@property offers_find_groups type=select store=no parent=offers_find_params captionside=top
			@caption Hankijagruppide valik
			@property offers_find_start type=date_select store=no parent=offers_find_params captionside=top
			@caption Alates
			@property offers_find_end type=date_select store=no parent=offers_find_params captionside=top
			@caption Kuni
			@property offers_find_product type=textbox store=no parent=offers_find_params captionside=top
			@caption Pakutud Toode
			@property offers_find_only_buy type=checkbox store=no parent=offers_find_params no_caption=1 captionside=top
			@caption Ainult ostudega
			@property offers_find_archived type=checkbox store=no parent=offers_find_params no_caption=1 captionside=top
			@caption Sh arhiveeritud
			@property do_find_offers type=submit store=no parent=offers_find_params no_caption=1 captionside=top
			@caption Otsi
	@property offers_tbl type=table no_caption=1 store=no parent=offers_l

@groupinfo buyings caption="Ostud"
@default group=buyings

	@property buyings_tb type=toolbar no_caption=1 store=no

	@layout buyings_l type=hbox width=30%:70%

		@layout buyings_left type=vbox parent=buyings_l

		@layout buyings_tree type=vbox parent=buyings_left closeable=1 area_caption=Ostude&nbsp;puu
			@property buyings_tr type=treeview no_caption=1 store=no parent=buyings_tree

		@layout buyings_find_params type=vbox parent=buyings_left closeable=1 area_caption=Ostude&nbsp;otsing
			@property buyings_find_name type=textbox store=no parent=buyings_find_params captionside=top
			@caption Hankija nimetus
			@property buyings_find_address type=textbox store=no parent=buyings_find_params captionside=top
			@caption Aadress
			@property buyings_find_groups type=select store=no parent=buyings_find_params captionside=top
			@caption Hankijagruppide valik
			@property buyings_find_start type=date_select store=no parent=buyings_find_params captionside=top
			@caption Alates
			@property buyings_find_end type=date_select store=no parent=buyings_find_params captionside=top
			@caption Kuni
			@property buyings_find_product type=textbox store=no parent=buyings_find_params captionside=top
			@caption Pakutud Toode
			@property buyings_find_archived type=checkbox store=no parent=buyings_find_params no_caption=1
			@caption Sh arhiveeritud
			@property do_find_buyings type=submit store=no parent=buyings_find_params no_caption=1
			@caption Otsi
	@property buyings_tbl type=table no_caption=1 store=no parent=buyings_l

@groupinfo products caption="Tooted"
@default group=products
	@property products_tb type=toolbar no_caption=1 store=no

	@layout products_l type=hbox width=30%:70%

		@layout products_left type=vbox parent=products_l

		@layout products_tree type=vbox parent=products_left closeable=1 area_caption=Toodete&nbsp;puu
			@property products_tr type=treeview no_caption=1 store=no parent=products_tree

		@layout products_find_params type=vbox parent=products_left closeable=1 area_caption=Toodete&nbsp;otsing
			@property products_find_product_name type=textbox store=no parent=products_find_params captionside=top
			@caption Toote nimetus
			@property products_find_name type=textbox store=no parent=products_find_params captionside=top
			@caption Hankija
			@property products_find_address type=textbox store=no parent=products_find_params captionside=top
			@caption Aadress
			@property products_find_groups type=select store=no parent=products_find_params captionside=top
			@caption Hankijagrupp
			@property products_find_apply type=checkbox store=no parent=products_find_params no_caption=1
			@caption Ainult kehtivad Ostud
			@property products_find_start type=date_select store=no parent=products_find_params captionside=top
			@caption Pakutud alates
			@property products_find_end type=date_select store=no parent=products_find_params captionside=top
			@caption Pakutud kuni
			@property do_find_products type=submit store=no parent=products_find_params no_caption=1
			@caption Otsi
	@property products_tbl type=table no_caption=1 store=no parent=products_l

@reltype MANAGER_CO value=1 clid=CL_CRM_COMPANY
@caption Haldaja firma

@reltype TEAM_MEMBER value=2 clid=CL_CRM_PERSON
@caption Meeskonna liige

@reltype PROCUREMENT_CENTER_FOLDERS value=3 clid=CL_MENU
@caption Hankekeskkonna kataloog

@reltype PROCUREMENT_TEMPLATE value=4 clid=CL_MESSAGE_TEMPLATE
@caption Hanke templeit

@reltype PROCUREMENT_FILES_MENU value=5 clid=CL_MENU
@caption

*/

class procurement_center extends class_base
{
	function procurement_center()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_center",
			"clid" => CL_PROCUREMENT_CENTER
		));


		//kahepoolsed sidemed pakkumisele ja pakkumise ridadele ... et muudetud kujul tööle hakkaks asi
		//seda peaks igalpool arvestama või nii...
		//et kui uuendus vaja, siis välja kommenteeritud osa käima lasta korra
/*		$offers = new object_list(array(
			"class_id" => CL_PROCUREMENT_OFFER,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($offers->arr() as $offer)
		{
			$conns = $offer->connections_to(array(
				'reltype' => 1,
				'class' => CL_PROCUREMENT_OFFER_ROW,
			));
			foreach($conns as $conn)
			{
				if(is_oid($conn->prop("from")))
				{
					$row = obj($conn->prop("from"));
					$offer->connect(array(
						"to" => $row->id(),
						"type" => "RELTYPE_ROW"
					));
					echo "connected ".$row->prop("product")." with id ".$row->id()." to ".$offer->name()." with id ".$offer->id()."\n";
				}
			}
			$offer->save();
		}*/
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "owner":
				if (is_oid($arr["obj_inst"]->id()))
				{
					$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
					if (!$o)
					{
						return PROP_IGNORE;
					}
					$prop["value"] = html::obj_change_url($o);
				}
				break;
			case "search_date_subtract_unit":
				$prop["options"] = array(
					86400	=> t("P&auml;eva"),
					604800	=> t("N&auml;dalat"),
					2648000	=> t("Kuud"),
					31536000=> t("Aastat"),
				);
				break;
			case "search_date_add_unit":
				$prop["options"] = array(
					86400	=> t("P&auml;eva"),
					604800	=> t("N&auml;dalat"),
					2648000	=> t("Kuud"),
					31536000=> t("Aastat"),
				);
				break;

			case "p_tb":
				$this->_p_tb($arr);
				break;

			case "p_tr":
				$this->_p_tr($arr);
				break;

			case "p_tbl":
				$this->_p_tbl($arr);
				break;

			case "team_tb":
				$i = get_instance(CL_PROCUREMENT_IMPLEMENTOR_CENTER);
				$i->_team_tb($arr);
				break;

			case "team_table":
				$i = get_instance(CL_PROCUREMENT_IMPLEMENTOR_CENTER);
				$i->_team_table($arr);
				break;

			case "offerers_tr":
				$this->_offerers_tr($arr);
				break;

			case "offers_tr":
				$this->_offers_tr($arr);
				break;
			case "buyings_tr":
				$this->_p_tr($arr);
				break;
			case "products_tr":
				$this->_products_tr($arr);
				break;

			case "offerers_find_tbl":
			case "offerers_tbl":
				$this->_offerers_table($arr);
				break;
			case "offerers_find_tb":
			case "offerers_tb":
				$this->_offerers_tb($arr);
				break;

			case "offers_tbl":
			case "offers_find_tbl":
				$this->_offers_tbl($arr);
				break;
			case "offers_tb":
			case "offers_find_tb":
				$this->_offers_tb($arr);
				break;
			case "buyings_tb":
			case "buyings_find_tb":
				$this->_buyings_tb($arr);
				break;
			case "buyings_tbl":
			case "buyings_find_tbl":
				$this->_buyings_tbl($arr);
				break;
			case "products_tbl":
			case "products_find_tbl":
				$this->_products_tbl($arr);
				break;
			case "products_tb":
			case "products_find_tb":
				$this->_products_tb($arr);
				break;
				
			case "offerers_find_product":
			case "offers_find_product":
			case "buyings_find_product":
			case "procurements_find_product":
			case "products_find_product_name":
				$procurement_inst = get_instance(CL_PROCUREMENT);
				$prop["autocomplete_source"] = $procurement_inst->mk_my_orb("product_autocomplete_source", array(), CL_PROCUREMENT, false, true);
				$prop["autocomplete_params"] = array($prop["name"]);
				
//				$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
//				
//				$prop["autocomplete_source"] = 
//				$procurement_inst->mk_my_orb("product_autocomplete_source", array("buyer" =>$o->id()), CL_PROCUREMENT, false, true);
//				$prop["autocomplete_params"] = array($prop["name"]);
////	//			"tabindex" => $x,
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				break;

			case "offerers_find_name":
			case "offerers_find_address":
			case "offerers_find_keyword":
			case "offerers_find_done":

			case "offerers_find_only_buy":
			case "offers_find_name":
			case "offers_find_address":

			case "offers_find_only_buy":
			case "offers_find_archived":
			case "buyings_find_name":
			case "buyings_find_address":

			case "buyings_find_archived":
			case "products_find_name":
			case "products_find_address":
			case "procurements_find_offerer":
			case "procurements_find_name":
			case "products_find_apply":

				
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				break;
			case "buyings_find_start":
			case "products_find_start":
			case "offerers_find_start":
			case "offers_find_start":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				if(!$prop["value"])
				{
					if($arr["obj_inst"]->prop("search_date_subtract"))
					{
						$prop["value"] = time() - $arr["obj_inst"]->prop("search_date_subtract")* $arr["obj_inst"]->prop("search_date_subtract_unit");
					}
					else
					{
						$prop["value"] = time() - 365*24*3600;
					}
				}
				break;
			case "offerers_find_end":
			case "offers_find_end":
			case "buyings_find_end":
			case "products_find_end":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				if(!$prop["value"])
				{
					if($arr["obj_inst"]->prop("search_date_add"))
					{
						$prop["value"] = time() + $arr["obj_inst"]->prop("search_date_add")* $arr["obj_inst"]->prop("search_date_add_unit");
					}
					else
					{
						$prop["value"] = time() + 24*3600;
					}
				}
				break;

			case "do_find_products":
				$prop["action"] = "submit";

				break;
			case "offers_find_groups":
			case "offerers_find_groups":
			case "buyings_find_groups":
			case "products_find_groups":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				$ol = new object_list(array(
				//	"parent" => $arr["obj_inst"]->prop("offerers_folder"),
					"class_id" => array(CL_CRM_CATEGORY),
					"lang_id" => array())
				);
				$prop["options"][""] = "";
				$prop["options"] += $ol->names();

				break;
			case "procurements_find_status":
				$search_data = $arr["obj_inst"]->meta("search_data");
				$prop["value"] = $search_data[$prop["name"]];
				$prop["options"] = array(
					"" => "",
					0 => t("Uus"),
					1 => t("Avaldatud"),
					2 => t("T&ouml;&ouml;s"),
					3 => t("Valmis"),
					4 => t("Suletud")
				);
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
				if ($this->can("view", $arr["request"]["add_member"]))
				{
					$arr["obj_inst"]->connect(array("to" => $arr["request"]["add_member"], "type" => "RELTYPE_TEAM_MEMBER"));
				}
				$arr["obj_inst"]->set_meta("team_prices", $arr["request"]["prices"]);
				break;
			case "offerers_find_name":
			case "offers_find_name":
			case "buyings_find_name":
			case "products_find_name":
			case "procurements_find_product":
				unset($arr["request"]["rawdata"]["rawdata"]);
				$arr["obj_inst"]->set_meta("search_data" , $arr["request"]);
				break;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_member"] = "0";
	}

	function _p_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->id();

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Kataloog'),
			'link'=> html::get_new_url(CL_MENU, $parent, array("return_url" => get_ru()))
		));

		$owner = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Hange'),
			'link'=> html::get_new_url(CL_PROCUREMENT, $parent, array("return_url" => get_ru(), "orderer" => $owner->id()))
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud hanked'),
			'action' => 'delete_procurements',
			'confirm' => t("Kas oled kindel et soovid valitud hanked kustudada?")
		));

		$tb->add_button(array(
			'name' => 'add_procurements_to_session',
			'img' => 'restore.gif',
			'tooltip' => t('Lisa valitud hanked prinditavate hangete hulka'),
			'action' => 'add_procurements_to_session',
		));
	}

	/**
		@attrib name=delete_procurements
	**/
	function delete_procurements($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=add_procurements_to_session
	**/
	function add_procurements_to_session($arr)
	{
		foreach($arr["sel"] as $id)
		{
			$_SESSION["procurement_center"]["print_procurements"][$id] = $id;
		}

		return $arr["post_ru"];
	}

	function _p_tr($arr)
	{
		
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "procurement_center",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $arr["obj_inst"]->id(),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "p_id",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _get_first_cust_cat($o)
	{
		$ol = new object_list($o->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		)));
		$ol->sort_by(array("prop" => "ord"));
		return $ol->begin();
	}

	function _offerers_tr($arr)
	{
		
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "procurement_center_offerers",
			),
			"root_item" => obj($arr["obj_inst"]->prop("offerers_folder")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_MENU),
				"parent" => $arr["obj_inst"]->prop("offerers_folder"),
				"lang_id" => array(),
				"site_id" => array()
			)),
			"var" => "p_id",
			"icon" => icons::get_icon_url(CL_MENU)
		));

		$cat_l = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
			"lang_id" => array(),
			"site_id" => array(),
		));

		foreach($cat_l->arr() as $cat)
		{
			$arr["prop"]["vcl_inst"]->add_item($arr["obj_inst"]->prop("offerers_folder"), array(
				"id" => $cat->id(),
				"name" => $cat->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "offerers",
					"p_id" => $cat->id(),
				)),
			));
		}



/*		$arr["prop"]["vcl_inst"]->set_only_one_level_opened(1);
		$node_id = 0;

		if (!is_oid($arr['request']['category']))
		{
			$f_cat = $this->_get_first_cust_cat($arr["obj_inst"]);
			if ($f_cat)
			{
				$arr['request']['category'] = $f_cat->id();
			}
		}
		$i = get_instance(CL_CRM_COMPANY);
		$i->active_node = (int)$arr['request']['category'];
		$i->generate_tree(array(
			'tree_inst' => &$arr["prop"]["vcl_inst"],
			'obj_inst' => $arr['obj_inst'],
			'node_id' => &$node_id,
			'conn_type' => 'RELTYPE_CATEGORY',
			'skip' => array(CL_CRM_COMPANY),
			'attrib' => 'category',
			'leafs' => false,
			'style' => 'nodetextbuttonlike',
			"edit_mode" => 1
		));
*/

		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 1,
			"name" => t('Asukoht'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offerers",
				"p_id" => 1,
			)),
		));

		$countrys = new object_list(array(
			"class_id" => CL_CRM_COUNTRY,
			"sort_by" => "name",
			"lang_id" => array(),
		));
		foreach($countrys->names() as $id => $name)
		{
			$arr["prop"]["vcl_inst"]->add_item(1, array(
				"id" => $id,
				"name" => $name,
				"url" => $this->mk_my_orb("change",array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "offerers",
					"country" => $id,
					"p_id" => $id,
					)),
			));

		//siit edasi peaks kärpima, kui vaja, et see puu kiiremini lahti tuleks
			$areas = new object_list(array(
				"class_id" => CL_CRM_AREA,
				"sort_by" => "name",
				"lang_id" => array(),
				"country" => $id
			));
			foreach($areas->names() as $area_id => $area_name)
			{
				$arr["prop"]["vcl_inst"]->add_item($id, array(
					"id" => $area_id,
					"name" => $area_name,
					"url" => $this->mk_my_orb("change",array(
						"id" => $arr["obj_inst"]->id(),
						"group" => "offerers",
						"country" => $id,
						"area" => $area_id,
						"p_id" => $area_id,
					)),
				));

				$city = new object_list(array(
					"class_id" => CL_CRM_CITY,
					"sort_by" => "name",
					"lang_id" => array(),
					"area" => $area_id
				));
				foreach($city->names() as $city_id => $city_name)
				{
					$arr["prop"]["vcl_inst"]->add_item($area_id, array(
						"id" => $city_id,
						"name" => $city_name,
						"url" => $this->mk_my_orb("change",array(
							"id" => $arr["obj_inst"]->id(),
							"group" => "offerers",
							"country" => $id,
							"area" => $area_id,
							"city" => $city_id,
							"p_id" => $city_id,
						)),
					));
				}
			}
		}
/*		if(is_oid($arr["request"]["country"]))
		{
			$areas = new object_list(array(
				"class_id" => CL_CRM_AREA,
				"sort_by" => "name",
				"lang_id" => array(),
				"country" => $arr["request"]["country"]
			));
			foreach($areas->names() as $id => $name)
			{
				$arr["prop"]["vcl_inst"]->add_item($arr["request"]["country"], array(
					"id" => $id,
					"name" => $name,
					"url" => $this->mk_my_orb("change",array(
						"id" => $arr["obj_inst"]->id(),
						"group" => "offerers",
						"country" => $arr["request"]["country"],
						"area" => $id,
						"p_id" => $id,
					)),
				));
			}
		}
*/
/*		if(is_oid($arr["request"]["area"]))
		{
			$city = new object_list(array(
				"class_id" => CL_CRM_CITY,
				"sort_by" => "name",
				"lang_id" => array(),
				"area" => $arr["request"]["area"]
			));
			foreach($city->names() as $id => $name)
			{
				$arr["prop"]["vcl_inst"]->add_item($arr["request"]["area"], array(
					"id" => $id,
					"name" => $name,
					"url" => $this->mk_my_orb("change",array(
						"id" => $arr["obj_inst"]->id(),
						"group" => "offerers",
						"country" => $arr["request"]["country"],
						"area" => $arr["request"]["area"],
						"city" => $id,
						"p_id" => $id,
					)),
				));
			}
		}
		*/
	}

	/**
		@attrib name=get_tree_stuff all_args=1
	**/
	function get_tree_stuff($arr)
	{
		extract($_GET); extract($_POST); extract($arr);
		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "offers_tree",
//			"persist_state" => 1,
		));
		if(substr_count($parent , "valid_"))
		{
			$stuff = "valid";
		}
		if(substr_count($parent, "archived_" ))
		{
			$stuff = "archived";
		}

		$parent = str_replace("valid_" , "" , $parent);
		$parent = str_replace("archived_" , "" , $parent);


		if($parent == 1)
		{
			$tree->add_item(0, array(
				"id" => "valid_offerers",
				"name" => t('Pakkujad'),
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"group" => "offers",
					"result" => "valid",
					"return_url" => $arr["set_retu"],
					"p_id" => "valid_offerers",
				)),
			));

			$tree->add_item(0, array(
				"id" => "valid_procurements",
				"name" => t('Hanked'),
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"group" => "offers",
					"result" => "archived",
					"return_url" => $arr["set_retu"],
					"p_id" => "valid_procurements",
				)),
			));
			$tree->add_item("valid_procurements", array(
			));
			$tree->add_item("valid_offerers", array(
			));
		}
		if($parent == 2)
		{
			$tree->add_item(0, array(
				"id" => "archived_offerers",
				"name" => t('Pakkujad'),
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"group" => "offers",
					"result" => "valid",
					"p_id" => "archived_offerers",
				)),
			));
			$tree->add_item(0, array(
				"id" => "archived_procurements",
				"name" => t('Hanked'),
				"url" => $this->mk_my_orb("change",array(
					"id" => $oid,
					"group" => "offers",
					"result" => "archived",
					"p_id" => "archived_procurements",
				)),
			));
				$tree->add_item("archived_procurements", array(
				));
				$tree->add_item("archived_offerers", array(
			));
		}

		if($parent == " procurements")
		{
			$this->add_procurements_to_offers_tree(&$tree,array(
				"vcl" => &$tree,
				"parent" => "procurements",
		//		"result" => "archived",
				"menu" => $oid,
				"obj" => $oid,
				"stuff" => $stuff,
			));
//			$tree->add_item($stuff."_procurements", array(
//			));
		}
		if($parent == " offerers")
		{
			$this->add_offerers_to_offers_tree(&$tree,array(
				"vcl" => &$tree,
				"parent" => 0,
	//			"result" => "archived",
				"menu" => $oid,
				"obj" => $oid,
				"stuff" => $stuff,
			));
//			$tree->add_item($stuff."_offerers", array(
//			));
		}

		if(is_oid($parent) && $this->can("view" , $parent) && $parent > 10)
		{
			$parent_obj = obj($parent);
			if($parent_obj->class_id() == CL_CRM_CATEGORY)
			{
				$this->add_companys_to_tree(&$tree,array(
					"vcl" => &$tree,
					"parent" => 0,
		//			"result" => "archived",
					"menu" => $parent,
					"obj" => $oid,
					"stuff" => $stuff,
				));
				$tree->add_item($stuff."_".$parent, array(
				));
			}
			if($parent_obj->class_id() == CL_MENU)
			{
				$this->add_procurements_to_offers_tree(&$tree,array(
					"vcl" => &$tree,
					"parent" => 0,
		//			"result" => "archived",
					"menu" => $parent,
					"obj" => $oid,
					"stuff" => $stuff,
				));
//				$this->add_procurements_to_offers_tree(&$tree,array(
//					"vcl" => &$tree,
//					"parent" => 0,
			//		"result" => "archived",
//					"menu" => $parent,
//					"obj" => $oid,
//					"stuff" => $stuff,
//				));

				$tree->add_item($stuff."_".$parent, array(
				));
			}

		}

		/*
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => "valid_offerers",
			"name" => t('Pakkujad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "valid",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => "valid_procurements",
			"name" => t('Hanked'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "archived",
			)),
		));

		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => "archived_offerers",
			"name" => t('Pakkujad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "valid",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => "archived_procurements",
			"name" => t('Hanked'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "archived",
			)),
		));

		$this->add_offerers_to_offers_tree(&$arr["prop"]["vcl_inst"],array(
			"vcl" => &$arr["prop"]["vcl_inst"],
			"parent" => "offerers",
//			"result" => "archived",
			"menu" => $arr["obj_inst"]->id(),
			"obj" => $arr["obj_inst"]->id(),
		));

		$this->add_procurements_to_offers_tree(&$arr["prop"]["vcl_inst"],array(
			"vcl" => &$arr["prop"]["vcl_inst"],
			"parent" => "procurements",
//			"result" => "archived",
			"menu" => $arr["obj_inst"]->id(),
			"obj" => $arr["obj_inst"]->id(),
		));




		$tree->add_item($parent,array(
			"name" => $gdata["caption"],
			"id" => $gkey,
			"url" => $this->mk_my_orb("grouphelp",array(
				"clid" => trim($arr["parent"]),
				"grpid" => $gkey,
			)),
			"is_open" => 1,
			"iconurl" => "images/icons/help_topic.gif",
		));*/
		die($tree->finalize_tree());
	}

	function _offers_tr($arr)
	{
		
		//$arr["prop"]["vcl_inst"] = get_instance("vcl/treeview");

		$arr["prop"]["vcl_inst"]->start_tree (array (
			"type" => TREE_DHTML,
			"has_root" => 1,
			"tree_id" => "offers_tree",
			"persist_state" => 1,
			"root_name" => t("Pakkumised"),
			"root_url" => "#",
			"get_branch_func" => $this->mk_my_orb("get_tree_stuff",array(
				"clid" => $arr["clid"],
				"group" => $arr["request"]["group"],
				"oid" => $arr["obj_inst"]->id(),
				"set_retu" => get_ru(),
				"parent" => " ",
			)),
		));

		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 1,
			"name" => t('Kehtivad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "valid",
				"p_id" => "valid",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => 2,
			"name" => t('Arhiveeritud'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "archived",
				"p_id" => "archived",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array());
		$arr["prop"]["vcl_inst"]->add_item(2, array());
/*		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => "valid_offerers",
			"name" => t('Pakkujad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "valid",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(1, array(
			"id" => "valid_procurements",
			"name" => t('Hanked'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "archived",
			)),
		));

		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => "archived_offerers",
			"name" => t('Pakkujad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "valid",
			)),
		));
		$arr["prop"]["vcl_inst"]->add_item(2, array(
			"id" => "archived_procurements",
			"name" => t('Hanked'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "offers",
				"result" => "archived",
			)),
		));

		$this->add_offerers_to_offers_tree(&$arr["prop"]["vcl_inst"],array(
			"vcl" => &$arr["prop"]["vcl_inst"],
			"parent" => "offerers",
//			"result" => "archived",
			"menu" => $arr["obj_inst"]->id(),
			"obj" => $arr["obj_inst"]->id(),
		));

		$this->add_procurements_to_offers_tree(&$arr["prop"]["vcl_inst"],array(
			"vcl" => &$arr["prop"]["vcl_inst"],
			"parent" => "procurements",
//			"result" => "archived",
			"menu" => $arr["obj_inst"]->id(),
			"obj" => $arr["obj_inst"]->id(),
		));
*/	}

	function add_offerers_to_offers_tree(&$vcl,$arr)
	{
		extract($arr);
		
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_CATEGORY),
//			"parent" => $menu,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$ol2 = new object_list();
			foreach($o->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$ol2->add($c->prop("to"));
			}
			$vcl->add_item(0, array(
				"id" => $stuff."_".$o->id(),
				"name" => $o->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => $stuff."_".$o->id(),
					"result" => $stuff,
				)),
			));
			$vcl->add_item( $stuff."_".$o->id(), array());
/*
			$vcl->add_item("archived_".$parent, array(
				"id" => "archived_".$o->id(),
				"name" => $o->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => "archived_".$o->id(),
					"result" => "archived",
				)),
			));
*/
			//pakkujad puusse
/*
			foreach($ol2->arr() as $o2)
			{
				$filter = array(
					"class_id" => array(CL_PROCUREMENT_OFFER),
					"lang_id" => array(),
					"site_id" => array(),
					"CL_PROCUREMENT_OFFER.offerer" => $o2->id(),
				);
				if($stuff == "archived")
				{
					$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, 1, time());
				}

				else
				{
					$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, time(), time()*666);
				}

				$offers_list = new object_list($filter);
				$vcl->add_item($stuff."_".$o->id(), array(
					"id" => $stuff."_".$o2->id(),
					"name" => $o2->name()."(".count($offers_list->ids()).")",
					"url" => $this->mk_my_orb("change",array(
						"id" => $obj,
						"group" => "offers",
						"p_id" => $stuff."_".$o2->id(),
						"result" => $stuff,
					)),
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				));
/*
				$offers_list2 = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER),
					"lang_id" => array(),
					"site_id" => array(),
					"CL_PROCUREMENT_OFFER.offerer" => $o2->id(),
					"accept_date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, time(), 99999999999999 ),
				));

				$vcl->add_item("valid_".$o->id(), array(
					"id" => "valid_".$o2->id(),
					"name" => $o2->name()."(".count($offers_list2->ids()).")",
					"url" => $this->mk_my_orb("change",array(
						"id" => $obj,
						"group" => "offers",
						"p_id" => "valid_".$o2->id(),
						"result" => "valid",
					)),
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				));*/
/*			}
*/		}
	}

	function add_companys_to_tree(&$vcl,$arr)
	{
		
		extract($arr);
		$ol2 = new object_list();
		$o = obj($menu);
		foreach($o->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$ol2->add($c->prop("to"));
		}
		//pakkujad puusse
		foreach($ol2->arr() as $o2)
		{
			$filter = array(
				"class_id" => array(CL_PROCUREMENT_OFFER),
				"lang_id" => array(),
				"site_id" => array(),
				"CL_PROCUREMENT_OFFER.offerer" => $o2->id(),
			);
			if($stuff == "archived")
			{
				$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, 10, time());
			}
			else
			{
				$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
			}

			$offers_list = new object_list($filter);
			$vcl->add_item(0, array(
				"id" => $stuff."_".$o2->id(),
				"name" => $o2->name()."(".count($offers_list->ids()).")",
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => $stuff."_".$o2->id(),
					"result" => $stuff,
				)),
				"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
			));
		}
	}

	function add_procurements_to_offers_tree(&$vcl,$arr)
	{
		
		extract($arr);
		$ol = new object_list(array(
			"class_id" => array(CL_MENU),
			"parent" => $menu,
			"lang_id" => array(),
			"site_id" => array()
		));

		$ol2 = new object_list(array(
			"class_id" => array(CL_PROCUREMENT),
			"parent" => $menu,
			"lang_id" => array(),
			"site_id" => array()
		));

		foreach($ol->arr() as $o)
		{

			$vcl->add_item(0, array(
				"id" => $stuff."_".$o->id(),
				"name" => $o->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => $stuff."_".$o->id(),
					"result" => $stuff,
				)),
			));

			$vcl->add_item($stuff."_".$o->id(), array());

/*
			$vcl->add_item("archived_".$parent, array(
				"id" => "archived_".$o->id(),
				"name" => $o->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => "archived_".$o->id(),
					"result" => "archived",
				)),
			));
*/
			//pakkumised puusse
/*			foreach($ol2->arr() as $o2)
			{
				$offers_list = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER),
					"lang_id" => array(),
					"site_id" => array(),
					"CL_PROCUREMENT_OFFER.RELTYPE_PROCUREMENT.id" => $o2->id(),
					"accept_date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, 1, time()),
				));

				$vcl->add_item(0, array(
					"id" => "archived_".$o2->id(),
					"name" => $o2->name()."(".count($offers_list->ids()).")",
					"url" => $this->mk_my_orb("change",array(
						"id" => $obj,
						"group" => "offers",
						"p_id" => $stuff."_".$o2->id(),
						"result" => $stuff,
					)),
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				));
/*
				$offers_list2 = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER),
					"lang_id" => array(),
					"site_id" => array(),
					"CL_PROCUREMENT_OFFER.RELTYPE_PROCUREMENT.id" => $o2->id(),
					"accept_date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, time(), 99999999999999 ),
				));

				$vcl->add_item("valid_".$o->id(), array(
					"id" => "valid_".$o2->id(),
					"name" => $o2->name()."(".count($offers_list2->ids()).")",
					"url" => $this->mk_my_orb("change",array(
						"id" => $obj,
						"group" => "offers",
						"p_id" => "valid_".$o2->id(),
						"result" => "valid",
					)),
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				));*/
/*			}

//			$this->add_procurements_to_offers_tree(&$vcl,array(
//				"parent" => $o->id(),
//				"menu" => $o->id(),
//				"obj" => $obj,
//			));*/
		}

		foreach($ol2->arr() as $o2)
		{
			$filter = array(
				"class_id" => array(CL_PROCUREMENT_OFFER),
				"lang_id" => array(),
				"site_id" => array(),
				"CL_PROCUREMENT_OFFER.RELTYPE_PROCUREMENT.id" => $o2->id(),
			);
			if($stuff == "archived")
			{
				$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, 10, time());
			}
			else
			{
				$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
			}

			$offers_list = new object_list($filter);

			$vcl->add_item(0, array(
				"id" => "archived_".$o2->id(),
				"name" => $o2->name()."(".count($offers_list->ids()).")",
				"url" => $this->mk_my_orb("change",array(
					"id" => $obj,
					"group" => "offers",
					"p_id" => $stuff."_".$o2->id(),
					"result" => $stuff,
				)),
				"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
			));
		}

	}


	function search_offers($this_obj)
	{
		$ol = new object_list();
		$filter = array("class_id" => array(CL_PROCUREMENT_OFFER));
		$data = $this_obj->meta("search_data");
		if($data["offers_find_name"]) $filter["CL_PROCUREMENT_OFFER.offerer.name"] = "%".$data["offers_find_name"]."%";
//		if($data["offers_find_groups"]) $filter["CL_PROCUREMENT_OFFER.offerer.parent"] = $data["offers_find_groups"];

 		if((date_edit::get_timestamp($data["offers_find_start"]) > 1)|| (date_edit::get_timestamp($data["offers_find_end"]) > 1))
 		{
 			if(date_edit::get_timestamp($data["offers_find_start"]) > 1)
 			{
 				$from = date_edit::get_timestamp($data["offers_find_start"]);
 			}
 			else $from = 1;
 			if(date_edit::get_timestamp($data["offers_find_end"]) > 1)
 			{
 				$to = date_edit::get_timestamp($data["offers_find_end"]);
 			}
 			else $to = time()*66;
 		 	$filter["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($from - 1), ($to + 1));
 		}
		if(!$data["offers_find_archived"]) $filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, 1,  time());

		if($data["offers_find_product"])
		{
			$owner = $this_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if(is_object($owner))
			{
				$row_ol = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER_ROW),
					"lang_id" => array(),
					"CL_PROCUREMENT_OFFER_ROW.product" => "%".$data["offers_find_product"]."%",
				//	"parent" => $this_obj->id(),
				));

				foreach($row_ol->arr() as $row)
				{
					$offer = $row->get_first_obj_by_reltype("RELTYPE_OFFER");
					if(!is_object($offer))
					{
						continue;
					}
					if($data["offers_find_only_buy"])
					{
						$ps = $offer->connections_to(array(
							'reltype' => 2,
							'class' => CL_PURCHASE,
						));
						if(!(sizeof($ps)>0)) continue;
					}
					$filter["oid"][$offer->id] = $offer->id();
				}
			}
			if(!sizeof($filter["oid"]) > 0) return $ol;
		}
		if($data["offers_find_address"] || $data["offers_find_groups"])
		{
			$offerers_filter = array(
				"lang_id" => array(),
				"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
			);

			if($data["offers_find_groups"])
			{
				if(is_oid($data["offers_find_groups"]))
				{
					$p_obj = obj($data["offers_find_groups"]);
					if($p_obj->class_id() == CL_CRM_CATEGORY)
					{
						foreach($p_obj->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
						{
							$offerers_filter["oid"][$c->prop("to")] = $c->prop("to");
						}
					}
				}
				if(!sizeof($offerers_filter["oid"]))
				{
					return $ol;
				}
			}

			if($data["offers_find_address"])
			{
				$offerers_filter[] =
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
						"CL_CRM_COMPANY.contact.name" => "%".$data["offers_find_address"]."%",
						"CL_CRM_PERSON.address.name" => "%".$data["offers_find_address"]."%",
					)
				));
			}
			$offerers = new object_list($offerers_filter);
			$filter["offerer"] = $offerers->ids();
			if(!sizeof($filter["offerer"])) return $ol;
		}
		$ol = new object_list($filter);
		return $ol;
	}

	function _offers_tbl($arr)
	{
		$filter = array(
			"class_id" => array(CL_PROCUREMENT_OFFER),
//			"parent" => $parent,
			"lang_id" => array(),
			"site_id" => array()
		);

		if(substr_count($arr["request"]["p_id"] , "valid"))
		{
			$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_GREATER, time());
		}
		if(substr_count($arr["request"]["p_id"], "archived" ))
		{
			$filter["accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, 10, time());
		}

		//võtab igast jama eest ära... tabelis seda enam vaja pole
		$arr["request"]["p_id"] = str_replace("valid_" , "" , $arr["request"]["p_id"]);
		$arr["request"]["p_id"] = str_replace("archived_" , "" , $arr["request"]["p_id"]);

		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_offers_tbl($t);

		if(!$arr["request"]["p_id"])
		{
			$filter = null;
		}

		if(is_oid($arr["request"]["p_id"]) && $this->can("view", $arr["request"]["p_id"]))
		{
			$p_obj = obj($arr["request"]["p_id"]);
			if($p_obj->class_id() == CL_MENU)
			{
				$offerers = new object_list(array(
					"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => $arr["request"]["p_id"],
				));
				$filter["offerer"] = $offerers->ids();
				if(!sizeof($filter["offerer"])) $filter["offerer"] = array(0);
			}
			if($p_obj->class_id() == CL_CRM_PERSON || $p_obj->class_id() == CL_CRM_COMPANY)
			{
				$filter["offerer"] = $arr["request"]["p_id"];
			}
			if($p_obj->class_id() == CL_PROCUREMENT)
			{
				$filter["procurement"] = $arr["request"]["p_id"];
			}

			if($p_obj->class_id() == CL_CRM_CATEGORY)
			{
				foreach($p_obj->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
				{
					$filter["offerer"][$c->prop("to")] = $c->prop("to");
				}
			}

		}

		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_offers($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
		}
		else
		{
			$ol = new object_list($filter);
		}

		$offer_inst = get_instance(CL_PROCUREMENT_OFFER);
		$statuses = $offer_inst->offer_states;
		$result = $arr["request"]["result"];
		foreach($ol->arr() as $o)
		{
//			$offerer_name = html::obj_change_url($o);
			$offerer_area = "";
			$offerer_discount = "";
			if(is_oid($o->prop("offerer")) && $this->can("view" , $o->prop("offerer")))
			{
				$offerer = obj($o->prop("offerer"));
				$offerer_name = html::obj_change_url($offerer);
				if($offerer->class_id() == CL_CRM_COMPANY) $address_id = $offerer->prop("contact");
				if($offerer->class_id() == CL_CRM_PERSON) $address_id = $offerer->prop("address");
				if(is_oid($address_id) && $this->can("view" , $address_id))
				{
					$address = obj($address_id);
					if(is_oid($address->prop("piirkond")) && $this->can("view" , $address->prop("piirkond")))
					{
						$area = obj($address->prop("piirkond"));
						$offerer_area = $area->name();
					}
				}
			}
			$offerer_discount = 10;
			$files = "";
			$file_ol = new object_list($o->connections_from(array()));
			$file_inst = get_instance(CL_FILE);
			$pm = get_instance("vcl/popup_menu");
			foreach($file_ol->arr() as $file_o)
			{
				if(!(($file_o->class_id() == CL_FILE) || ($file_o->class_id() == CL_CRM_DOCUMENT) || ($file_o->class_id() == CL_CRM_DEAL) || ($file_o->class_id() == CL_CRM_OFFER) || ($file_o->class_id() == CL_CRM_MEMO)))
				{
					continue;
				}

				$pm->begin_menu("sf".$file_o->id());
				if ($file_o->class_id() == CL_FILE)
				{
					$pm->add_item(array(
						"text" => $file_o->name(),
						"link" => file::get_url($file_o->id(), $file_o->name())
					));
				}
				else
				{
					foreach($file_o->connections_from(array("type" => "RELTYPE_FILE")) as $c)
					{
						$pm->add_item(array(
							"text" => $c->prop("to.name"),
							"link" => file::get_url($c->prop("to"), $c->prop("to.name")),
							"target" => 1,
						));
					}
				}
				$files.= $pm->get_menu(array(
					"icon" => icons::get_icon_url($file_o)
				));
			}
			$date = "";
			if($o->prop("accept_date"))
			{
				$date = date("d.m.Y",$o->prop("accept_date"));
			}

			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"date" => $date,//$o->prop("accept_date")),
				"offerer_name" => $offerer_name,
				"area" => $offerer_area,
				"discount" => $offerer_discount,
				"status" => $statuses[$o->prop("state")],
				"sum" => number_format($offer_inst->calculate_price($o), 2),
				"files" => $files,
				"oid" => $o->id(),
			));
		}
	}

	function _init_offers_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Pakkumise kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "offerer_name",
			"caption" => t("Pakkuja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "area",
			"caption" => t("Piirkond"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "files",
			"caption" => t("Pakkumisega seotud failid"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Pakkumise staatus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Pakkumise summa"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _offers_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$arr["request"]["p_id"] = str_replace("valid_" , "" , $arr["request"]["p_id"]);
		$arr["request"]["p_id"] = str_replace("archived_" , "" , $arr["request"]["p_id"]);

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("offerers_folder");

		$proc = $offerer = "";
		if(is_oid($parent))
		{
			$parent_obj = obj($parent);
			if($parent_obj->class_id() == CL_PROCUREMENT)
			{
				$proc = $parent;
			}
			if($parent_obj->class_id() == CL_CRM_COMPANY || $parent_obj->class_id() == CL_CRM_PERSON)
			{
				$offerer = $parent;
			}
		}
		$_SESSION["procurement_offer"]["offerer"] = $offerer;
		$_SESSION["procurement_offer"]["proc"] = $proc;
		$_SESSION["procurement_offer"]["parent"] = $parent;

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Pakkumine'),
//			'link'=> $this->mk_my_orb("insert_offer" , array("return_url" => get_ru(), "parent" => $parent)),
			'action' => "add_procurement_offer"
//			'link'=> html::get_new_url(CL_PROCUREMENT_OFFER, $parent, array(
//				"return_url" => get_ru(),
//				"proc" => $proc,
//				"offerer" => $offerer,
//			))
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Ost'),
//			'link'=> html::get_new_url(CL_PURCHASE, $parent, array("return_url" => get_ru()))
			'action'=> "insert_purchase",
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud hanked'),
			'action' => 'delete_procurements',
			'confirm' => t("Kas oled kindel et soovid valitud pakkumised kustudada?")
		));
	}

	/**
		@attrib name=add_procurement_offer
		@param p_id optional string

	**/
	function add_procurement_offer($arr)
	{
		extract($_SESSION["procurement_offer"]);
		$_SESSION["procurement_offer"]=null;
		if(!$parent)
		{
			$parent = $arr["id"];
		}

		$offer = new object();
		$offer->set_class_id(CL_PROCUREMENT_OFFER);
		$offer->set_parent($parent);

		if(is_oid($offerer) && $this->can("view" , $offerer))
		{
			$offer->set_prop("offerer" , $offerer);
		}
		if(is_oid($proc) && $this->can("view" , $proc))
		{
			$offer->set_prop("procurement" , $proc);
		}
		$offer->save();
		return html::get_change_url($offer->id(), array("return_url" => $arr["post_ru"]));
	}

	function _buyings_tbl($arr)
	{
		$ol = new object_list();
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_buyings_tbl($t);
		$filter = array(
			"class_id" => array(CL_PURCHASE),
//			"parent" => $parent,
			"lang_id" => array(),
			"site_id" => array()
		);

		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_buyings($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
		}
		elseif(is_oid($arr["request"]["p_id"]) && $this->can("view", $arr["request"]["p_id"]))
		{
			$p_obj = obj($arr["request"]["p_id"]);
			if($p_obj->class_id() == CL_MENU)
			{
				$procurements = new object_list(array(
					"class_id" => array(CL_PROCUREMENT),
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => $arr["request"]["p_id"],
				));

				$filter["CL_PURCHASE.RELTYPE_OFFER.procurement"] = $procurements->ids();

				if(sizeof($procurements->ids()))
				{
					$ol = new object_list($filter);
				}
			}
		}
		$buy_inst = get_instance(CL_PURCHASE);
		$statuses = $buy_inst->stats;
		foreach($ol->arr() as $o)
		{

			if((sizeof($offerers_array)) && (!in_array($o->prop("offerer") , $offerers_array)))
			{
				continue;
			}
			$offerer_area = "";
			if(is_oid($o->prop("offerer")) && $this->can("view" , $o->prop("offerer")))
			{
				$offerer = obj($o->prop("offerer"));
				$offerer_name = html::obj_change_url($offerer);
				if($offerer->class_id() == CL_CRM_COMPANY) $address_id = $offerer->prop("contact");
				if($offerer->class_id() == CL_CRM_PERSON) $address_id = $offerer->prop("address");
				if(is_oid($address_id) && $this->can("view" , $address_id))
				{
					$address = obj($address_id);
					if(is_oid($address->prop("piirkond")) && $this->can("view" , $address->prop("piirkond")))
					{
						$area = obj($address->prop("piirkond"));
						$offerer_area = $area->name();
					}
				}
			}

			$t->define_data(array(
				"date" => date("d.m.Y",$o->prop("date")),
				"name" => html::obj_change_url($o),
				"offerer_name" => $offerer_name,
				"area" => $offerer_area,
				"status" => $statuses[$o->prop("stat")],
				"sum" => $buy_inst->get_sum($o),
				"address" => $adress,
				"contacts" => $contacts,
				"oid" => $o->id()
			));
		}
	}
	function search_buyings($this_obj)
	{
		$ol = new object_list();
		$filter = array("class_id" => array(CL_PURCHASE), "lang_id" => array());
		$data = $this_obj->meta("search_data");
		if($data["buyings_find_name"]) $filter["CL_PURCHASE.offerer.name"] = "%".$data["buyings_find_name"]."%";

//		if($data["buyings_find_groups"]) $filter["CL_PURCHASE.offerer.parent"] = $data["buyings_find_groups"];

		if(date_edit::get_timestamp($data["buyings_find_start"]) != date_edit::get_timestamp($data["buyings_find_end"]))
		{
			if((date_edit::get_timestamp($data["buyings_find_start"]) > 1)|| (date_edit::get_timestamp($data["buyings_find_end"]) > 1))
			{
				if(date_edit::get_timestamp($data["buyings_find_start"]) > 1) $from = date_edit::get_timestamp($data["buyings_find_start"]); else $from = 0;
				if(date_edit::get_timestamp($data["buyings_find_end"]) > 1) $to = date_edit::get_timestamp($data["buyings_find_end"]); else $to = time()*666;
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($from - 1), ($to + 1));
			}
		}
		if(!$data["buyings_find_archived"]) $filter["CL_PURCHASE.RELTYPE_OFFER.accept_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, time(),  time()*66);

		if($data["buyings_find_groups"])
		{
			if(is_oid($data["buyings_find_groups"]))
			{
				$p_obj = obj($data["buyings_find_groups"]);
				if($p_obj->class_id() == CL_CRM_CATEGORY)
				{
					foreach($p_obj->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$filter["offerer"][$c->prop("to")] = $c->prop("to");
					}
				}
			}
			if(!sizeof($filter["offerer"]))
			{
				return $ol;
			}
		}

		if($data["buyings_find_address"])
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
			//		"CL_PURCHASE.offerer.contact.name" => "%".$data["buyings_find_address"]."%",
					"CL_PURCHASE.offerer.address.name" => "%".$data["buyings_find_address"]."%",
				)
			));
		}

		if($data["buyings_find_product"])
		{
			$owner = $this_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if(is_object($owner))
			{
				$row_ol = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER_ROW),
					"lang_id" => array(),
					"CL_PROCUREMENT_OFFER_ROW.product" => "%".$data["buyings_find_product"]."%",
				//	"parent" => $this_obj->id(),
				));

				foreach($row_ol->arr() as $row)
				{
					$offer = $row->get_first_obj_by_reltype("RELTYPE_OFFER");
					if(!is_object($offer))
					{
						continue;
					}
					$ps = $offer->connections_to(array(
						'reltype' => 2,
						'class' => CL_PURCHASE,
					));
					foreach($ps as $conn)
					{
						$filter["oid"][$conn->prop("from")] = $conn->prop("from");
					}
				}
			}
			if(!sizeof($filter["oid"]) > 0) return $ol;
		}
		$ol = new object_list($filter);
		return $ol;
	}
	function _buyings_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = $this->can("view" , $arr["request"]["p_id"]) ? $arr["request"]["p_id"] : $arr["obj_inst"]->id();

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Ost'),
			'action' => "insert_purchase",
//			'link'=> html::get_new_url(CL_PURCHASE, $parent, array("return_url" => get_ru()))
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta ost'),
			'action' => 'delete_cos',
			'confirm' => t("Kas oled kindel et soovid valitud ostud kustudada?")
		));
	}

	function _init_buyings_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Ostu nimetus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Ostu kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "offerer_name",
			"caption" => t("Pakkuja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "area",
			"caption" => t("Piirkond"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "offers",
			"caption" => t("Ostuga seotud pakkumised"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Ostu staatus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Ostu summa"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}
	function _buyings_tr($arr)
	{
		

		$arr["prop"]["vcl_inst"]->add_item(0, array(
			"id" => $arr["obj_inst"]->prop("offerers_folder"),
			"name" => t('Pakkujad'),
			"url" => $this->mk_my_orb("change",array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "buyings",
				"p_id" => 1,
			)),
		));

		$menus = new object_list(array(
			"class_id" => CL_MENU,
			"sort_by" => "name",
			"lang_id" => array(),
			"parent" => $arr["obj_inst"]->prop("offerers_folder"),
		));

		foreach($menus->names() as $id => $name)
		{
			$arr["prop"]["vcl_inst"]->add_item($arr["obj_inst"]->prop("offerers_folder"), array(
				"id" => $id,
				"name" => $name,
				"url" => $this->mk_my_orb("change",array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "buyings",
					"p_id" => $id,
					)),
			));

			$offerers = new object_list(array(
				"class_id" => array(CL_CRM_PERSON,CL_CRM_COMPANY),
				"sort_by" => "name",
				"lang_id" => array(),
				"parent" => $id,
			));

			foreach($offerers->arr() as $offerer)
			{
				$arr["prop"]["vcl_inst"]->add_item($id, array(
					"id" => $offerer->id(),
					"name" => $offerer->name(),
					"url" => $this->mk_my_orb("change",array(
						"id" => $arr["obj_inst"]->id(),
						"group" => "buyings",
						"p_id" => $offerer->id(),
						)),
					"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
					));
			}
		}
	}

	function _init_impl_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "contacts",
			"caption" => t("Kontaktandmed"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _offerers_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_impl_tbl($t);

		if(is_oid($arr["request"]["p_id"]) && $this->can("view", $arr["request"]["p_id"]))
		{
			$p_obj = obj($arr["request"]["p_id"]);
			if($p_obj->class_id() == CL_MENU) $parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("impl_folder");
		}
		$ccs = new object_list(array(
			"class_id" => CL_PROCUREMENT_IMPLEMENTOR_CENTER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$cd = array();
		foreach($ccs->arr() as $cc)
		{
			$co = $cc->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if ($co)
			{
				$cd[$co->id()] = $cc;
			}
		}
		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_offerers($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
		}
		else
		{
			$ol = new object_list();
			if(is_oid($arr["request"]["p_id"]))
			{
				$cat_obj = obj($arr["request"]["p_id"]);

				if($cat_obj->class_id() == CL_CRM_CATEGORY)
				{
					foreach($cat_obj->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$ol->add($c->prop("to"));
					}
				}
				if($cat_obj->class_id() == CL_CRM_AREA || $cat_obj->class_id() == CL_CRM_COUNTRY ||$cat_obj->class_id() == CL_CRM_CITY)
				{
					$ol = $this->is_in_area(array("req" => $arr["request"]));
				}
			}


/*			$ol = new object_list(array(
				"class_id" => array(CL_FOLDER, CL_CRM_COMPANY, CL_CRM_PERSON),
				"parent" => $parent,
				"lang_id" => array(),
				));
*///			$cat_l = new object_list(array(
	//			"class_id" => CL_CRM_COMPANY,
	//			"lang_id" => array(),
	//			"site_id" => array(),
	//			"CL_CRM_COMPANY.RELTYPE_CATEGORY.id" => $arr["request"]["p_id"],
	//		));
//			$ol->add($cat_l);
//			arr($cat_l);

		}
		$p_inst = get_instance(CL_CRM_PERSON);
		foreach($ol->arr() as $o)
		{
//			if(($arr["request"]["country"]) && !$this->is_in_area(array("o" => $o, "req" => $arr["request"]))) continue;
			$adress = "";
			$contacts = "";
			$oid = $o->id();
			$name = html::obj_change_url($o);

			if($o->class_id() == CL_CRM_COMPANY)
			{
				if(is_oid($o->prop("contact")) && $this->can("view" , $o->prop("contact")))
				{
					$address_obj = obj($o->prop("contact"));
					$adress = $address_obj->name();
				}
				$contacts = $this->get_company_contacts($o);
				if(is_object($o->get_first_obj_by_reltype("CONTACT_PERSON")))
				{
					$o = $o->get_first_obj_by_reltype("CONTACT_PERSON");
				}
			}

			if($o->class_id() == CL_CRM_PERSON)
			{
				if(!$contacts)
				{
					$contacts = $p_inst->get_short_description($o->id());
				}
				if(is_oid($o->prop("address")) && $this->can("view" , $o->prop("address")) && !$address)
				{
					$address_obj = obj($o->prop("address"));
					$adress = $address_obj->name();
				}
			}

			$t->define_data(array(
				"name" => $name,
				"address" => $adress,
				"contacts" => $contacts,
				"oid" => $oid,
			));
		}
	}

	function search_offerers($this_obj)
	{
		$ol = new object_list();
		$filter = array("class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON));
		$data = $this_obj->meta("search_data");
		if($data["offerers_find_name"]) $filter["name"] = "%".$data["offerers_find_name"]."%";
		if($data["offerers_find_keyword"])
		{
			$filter["class_id"] = array(CL_CRM_COMPANY);
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"activity_keywords" => "%".$data["offerers_find_keyword"]."%",
					"CL_CRM_COMPANY.RELTYPE_KEYWORD.keyword" => "%".$data["offerers_find_keyword"]."%",
				)
			));
			
		}
		 ;//siia uus filter

		if($data["offerers_find_address"]) $filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
			"CL_CRM_COMPANY.contact.name" => "%".$data["offerers_find_address"]."%",
			"CL_CRM_PERSON.address.name" => "%".$data["offerers_find_address"]."%",
			)
		));
//		if($data["offerers_find_groups"]) $filter["parent"] = $data["offerers_find_groups"];
		if($data["offerers_find_done"])
		{
			$owner = $this_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if(is_object($owner))
			{
				$buyings = new object_list(array(
					"class_id" => array(CL_PURCHASE),
		//			"buyer" => $owner->id(),
					"lang_id" => array(),
					"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, date_edit::get_timestamp($data["offerers_find_start"]), date_edit::get_timestamp($data["offerers_find_end"])),
				));
				foreach($buyings->arr() as $buying)
				{
//					if((!((date_edit::get_timestamp( > 1)) && date_edit::get_timestamp($data["offerers_find_start"]) > $buying->prop("date"))) && (!(date_edit::get_timestamp($data["offerers_find_end"]) > 1 && date_edit::get_timestamp($data["offerers_find_end"]) < $buying->prop("date"))))
					$filter["oid"][$buying->prop("offerer")] = $buying->prop("offerer");
				}
				if(!sizeof($filter["oid"]) > 0) return $ol;
			}
		}
		if($data["offerers_find_product"])
		{
			$owner = $this_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
			if(is_object($owner))
			{
				if(sizeof($filter["oid"]) > 0)
				{
					$ids = $filter["oid"];
					$filter["oid"] = null;
				}

				$offers = new object_list(array(
					"class_id" => array(CL_PROCUREMENT_OFFER),
					"CL_PROCUREMENT_OFFER.procurement.orderer" => $owner->id(),
					"lang_id" => array(),
				));
				foreach($offers->arr() as $offer)
				{
					$row_conns = $offer->connections_to(array(
						'reltype' => 1,
						'class' => CL_PROCUREMENT_OFFER_ROW,
					));
					foreach($row_conns as $row_conn)
					{
						if(is_oid($row_conn->prop("from")))
						{
							$row = obj($row_conn->prop("from"));
						}
						else continue;
						if((substr_count(strtolower($row->prop("product")) , strtolower($data["offerers_find_product"])) > 0)
						&&(!(sizeof($ids) && !in_array($offer->prop("offerer") , $ids))))
						{
							//kui pole seotud ühtegi ostu
							$ps = $offer->connections_to(array(
								'reltype' => 2,
								'class' => CL_PURCHASE,
							));
							if($data["offerers_find_only_buy"] && !(sizeof($ps)>0)) continue;
							$filter["oid"][$offer->prop("offerer")] = $offer->prop("offerer");
						}
					}
				}

			}
			if(!sizeof($filter["oid"]) > 0) return $ol;
		}

		if($data["offerers_find_groups"])
		{
			if(sizeof($filter["oid"]) > 0)
			{
				$ids = $filter["oid"];
				$filter["oid"] = null;
			}
			$category = obj($data["offerers_find_groups"]);
			foreach($category->connections_from(array('type' => "RELTYPE_CUSTOMER")) as $c)
			{
				if(!(sizeof($ids) && !in_array($c->prop("to") , $ids)))
				{
					$filter["oid"][$c->prop("to")] = $c->prop("to");
				}
			}
			if(!sizeof($filter["oid"]) > 0) return $ol;
		}

		$ol = new object_list($filter);
		return $ol;
	}

	function is_in_area($args)
	{
		extract($args);
		extract($req);
		$ol = new object_list();
		$filter_person = array(
			"lang_id" => array(),
			"class_id" => array(CL_CRM_PERSON),
		);
		$filter_company = array(
			"lang_id" => array(),
			"class_id" => array(CL_CRM_COMPANY),
		);

		if($city)
		{
			$filter_person["CL_CRM_PERSON.address.linn"] = $city;
			$filter_company["CL_CRM_COMAPNY.contact.linn"] = $city;
		}

		if($area)
		{
			$filter_person["CL_CRM_PERSON.address.piirkond"] = $area;
			$filter_company["CL_CRM_COMAPNY.contact.piirkond"] = $area;
		}

		if($country)
		{
			$filter_person["CL_CRM_PERSON.address.riik"] = $country;
			$filter_company["CL_CRM_COMAPNY.contact.riik"] = $country;
		}
		$persons = new object_list($filter_person);
		$companys =  new object_list($filter_company);
		$ol->add($persons);
		$ol->add($companys);

		/*

		if($o->class_id() == CL_CRM_PERSON && is_oid($o->prop("address")) && $this->can("view", $o->prop("address")))
		{
			$adress = obj($o->prop("address"));
		}
		if($o->class_id() == CL_CRM_COMPANY && is_oid($o->prop("contact")) && $this->can("view", $o->prop("contact")))
		{
			$adress = obj($o->prop("contact"));
		}
		if(!is_object($adress)) return false;
		if(is_oid($city))
		{
			if($adress->prop("linn") == $city) return true;
		}
		elseif(is_oid($area))
		{
			if($adress->prop("piirkond")  == $area) return true;
		}
		elseif(is_oid($country))
		{
			if($adress->prop("riik")  == $country) return true;
		}*/
		return $ol;
	}

	function get_company_contacts($company)
	{
		$ret = "";
		if($this->can("view" , $company->prop("phone_id")))
		{
			$phone = obj($company->prop("phone_id"));
			$ret .= " " . $phone->name();
		}
		if($this->can("view" , $company->prop("email_id")))
		{
			$email = obj($company->prop("email_id"));
			$ret .= " " . $email->name();
		}
		return $ret;
	}



	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _p_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->id();

		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_procurements($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_PROCUREMENT,
				"parent" => $parent,
				"lang_id" => array(),
				"site_id" => array()
			));
		}
		$t->data_from_ol($ol, array("change_col" => "name"));
	}

	function search_procurements($this_obj)
	{
		$ol = new object_list();
		$filter = array("class_id" => array(CL_PROCUREMENT), "lang_id" => array());
		$data = $this_obj->meta("search_data");
		if($data["procurements_find_name"] != "")
		{
			$filter["name"] = "%".$data["procurements_find_name"]."%";
		}
		if($data["procurements_find_status"] != "")
		{
			$filter["state"] = $data["procurements_find_status"];
		}
		if($data["procurements_find_offerer"] != "")
		{
			$filter["CL_PROCUREMENT.RELTYPE_OFFERER.name"] = "%".$data["procurements_find_offerer"]."%";
		}
		$ol = new object_list($filter);
		//tõenäoliselt on see asi ka kiiremaks vaja tea.... kuid hetkel ei tuld paremat pähe... arvatavasti peab hanke ka siduma tootega...
		if($data["procurements_find_product"] != "")
		{
			foreach($ol->arr() as $procurement)
			{
				$products = $procurement->meta("products");
				$there_is_no_that_cool_product = 1;
				foreach($products as $product)
				{
					if(substr_count(strtolower($product["product"]), strtolower($data["procurements_find_product"])))
					{
						$there_is_no_that_cool_product = 0;
						break;
					}
				}
				if($there_is_no_that_cool_product)
				{
					$ol->remove($procurement->id());
					continue;
				}
			}
		}
		return $ol;
	}

	function _offerers_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$parent = $arr["request"]["p_id"] ? $arr["request"]["p_id"] : $arr["obj_inst"]->prop("offerers_folder");

		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		if ($o)
		{
			$co = $o->id();
			$alias = $o->id();
		}
		else $co = $arr["obj_inst"]->id();


		$tb->add_sub_menu(array(
			"parent" => "add_item",
			"name" => "add_cust_co",
			"text" => t("Organisatsioon")
		));
		$link = $this->mk_my_orb('new',array(
				'parent' => $co,
				'alias_to' => "%s",
				'reltype' => 3, // crm_company.CUSTOMER,
				'return_url' => get_ru()
			),
			'crm_company'
		);
		$this->_do_cust_cat_tb_submenus($tb, $link, $arr["obj_inst"], "add_cust_co");

		$link = $this->mk_my_orb('new',array(
				'parent' => $co,
				'alias_to' => "%s",
				'reltype' => 3, // crm_company.CUSTOMER,
				'return_url' => get_ru()
			),
			CL_CRM_PERSON
		);
		$tb->add_sub_menu(array(
			"parent" => "add_item",
			"name" => "add_cust_p",
			"text" => t("Eraisik")
		));
		$this->_do_cust_cat_tb_submenus($tb, $link, $arr["obj_inst"], "add_cust_p");

/*
		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Hankijagrupp'),
			'link'=> html::get_new_url(CL_MENU, $parent, array(
				"return_url" => get_ru(),
				"pseh" => aw_register_ps_event_handler(
						CL_PROCUREMENT_CENTER,
						"handle_impl_submit",
						array("id" => $arr["obj_inst"]->id()),
						CL_CRM_COMPANY
				)
			))
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Pakkuja/Organisatsioon'),
			'link'=> html::get_new_url(CL_CRM_COMPANY, $parent, array(
				"return_url" => get_ru(),
				array("id" => $arr["obj_inst"]->id()),
			)),
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Pakkuja/eraisik'),
			'link'=> html::get_new_url(CL_CRM_PERSON, $parent, array(
				"return_url" => get_ru(),
				"pseh" => aw_register_ps_event_handler(
						CL_PROCUREMENT_CENTER,
						"handle_impl_submit",
						array("id" => $arr["obj_inst"]->id()),
						CL_CRM_PERSON
				)
			))
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> t('Pakkumine'),
			'link'=> html::get_new_url(CL_PROCUREMENT_OFFER, $parent, array(
				"return_url" => get_ru(),
			))
		));
*/

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text' => t('Hankija kategooria'),
			'link' => $this->mk_my_orb('new',array(
					'parent' => $parent,
					'alias_to' => $alias,
					'reltype' => 30, //RELTYPE_CATEGORY
					'return_url' => get_ru()
				),
				'crm_category'
			)
		));

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud pakkujad'),
			'action' => 'delete_cos',
			'confirm' => t("Kas oled kindel et soovid valitud pakkujad kustudada?")
		));

		$tb->add_button(array(
			'name'=>'send_email',
			'tooltip'=> t('Saada kiri'),
			"img" => "mail_send.gif",
			'action' => 'send_mails',
		));

		$tb->add_menu_button(array(
			'name'=>'print',
			'tooltip'=> t('Print'),
			"img" => "print.gif",
		));

		foreach($_SESSION["procurement_center"]["print_procurements"] as $id)
		{
			$o = obj($id);
			$tb->add_menu_item(array(
				'parent'=>'print',
				'text' => $o->name(),
				'link' => "javascript:document.changeform.id.value='".$o->id()."';
					document.changeform.class.value='procurement';
					document.changeform.group.value='print';
					document.changeform.action.value='print_procurements';
					document.changeform.submit();",
			//	'action' => 'print_procurements',
			//	'link' => "#",
				//html::get_change_url($o->id(), array(
			//		"return_url" => get_ru(),
			//		"group" => "print",
			//	)),
			));
		}
	}

	function _do_cust_cat_tb_submenus(&$tb, $link, $p, $p_str, $oncl = null)
	{
		$cnt = 0;

		$cat_l = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
			"lang_id" => array(),
			"site_id" => array(),
		));

		foreach($cat_l->arr() as $cat)
		{
			$cnt++;
			$name = $p_str."_".$cat->id();
//			if ($this->_do_cust_cat_tb_submenus($tb, $link, $cat->id(), $name, $oncl) > 0)
//			{
//				$tb->add_sub_menu(array(
//					'parent'=> $p_str,
//					"name" => $name,
//					'text' => $cat->name(),
//				));
//			}

//			else
//			{
				$parm = array(
					'parent'=>$p_str,
					'text' => $cat->name(),
					'link' => str_replace(urlencode("%s"), $cat->id(), str_replace("%s", $cat->id(), $link)),
//					'url' => $this->mk_my_orb("change",array(
//						"id" => $arr["obj_inst"]->id(),
//						"group" => "offerers",
//						"p_id" => $cat->id(),
//					)),
				);

//				if ($oncl !== NULL)
//				{
//					$parm["onClick"] = str_replace(urlencode("%s"), $cat->id(), str_replace("%s", $cat->id(), $oncl));
//					$parm["link"] = "#";
//				}
				$tb->add_menu_item($parm);
//			}

/*			$arr["prop"]["vcl_inst"]->add_item($arr["obj_inst"]->prop("offerers_folder"), array(
				"id" => $cat->id(),
				"name" => $cat->name(),
				"url" => $this->mk_my_orb("change",array(
					"id" => $arr["obj_inst"]->id(),
					"group" => "offerers",
					"p_id" => $cat->id(),
				)),
			));
*/		}
		return $cnt;
	}

	function search_products($this_obj)
	{
		$ol = new object_list();
		$filter = array(
			"class_id" => array(CL_SHOP_PRODUCT),
			"lang_id" => array()
		);
		$data = $this_obj->meta("search_data");
		if($data["products_find_product_name"])
		{
			$filter["name"] = "%".$data["products_find_product_name"]."%";
		}
		if(
			    $data["products_find_name"]
			 || $data["products_find_address"]
			 || $data["products_find_groups"]
			 || $data["products_find_apply"]
			 || date_edit::get_timestamp($data["products_find_start"]) > 0
			 || date_edit::get_timestamp($data["products_find_end"]) > 0
		)
		{
			$offerer_filter = array(
				"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
				"lang_id" => array()
			);
			if($data["products_find_name"])
			{
				$offerer_filter["name"] = "%".$data["products_find_name"]."%";
			}
			if($data["products_find_address"])
			{
				$offerer_filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_COMPANY.contact.name" => "%".$data["products_find_address"]."%",
						"CL_CRM_PERSON.address.name" => "%".$data["products_find_address"]."%",
					)
				));
			}
			if($data["products_find_groups"])
			{
				if(is_oid($data["products_find_groups"]))
				{
					$p_obj = obj($data["products_find_groups"]);
					if($p_obj->class_id() == CL_CRM_CATEGORY)
					{
						foreach($p_obj->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
						{
							$offerer_filter["oid"][$c->prop("to")] = $c->prop("to");
						}
					}
				}
				if(!sizeof($offerer_filter["oid"]))
				{
					return $ol;
				}
			}
//			if($data["products_find_groups"]) $offerer_filter["parent"] = $data["products_find_groups"];
			$offerer_list = new object_list($offerer_filter);
			$filter["name"] = array($filter["name"]);


			//otsib pakkumiste ridu mis vastaks tingimustele
			$row_filter = array(
				"class_id" => array(CL_PROCUREMENT_OFFER_ROW),
				"lang_id" => array(),
				"CL_PROCUREMENT_OFFER_ROW.RELTYPE_OFFER.offerer" => $offerer_list->ids(),
			);
			if($data["products_find_apply"])
			{
				$row_filter["accept"] = 1;
			}
			if((date_edit::get_timestamp($data["products_find_start"]) > 1)|| (date_edit::get_timestamp($data["products_find_end"]) > 1))
			{
				if(date_edit::get_timestamp($data["products_find_start"]) > 1)
				{
					$from = date_edit::get_timestamp($data["products_find_start"]);
				}
				else $from = 1;
				if(date_edit::get_timestamp($data["products_find_end"]) > 1)
				{
					$to = date_edit::get_timestamp($data["products_find_end"]);
				}
				else $to = time()*66;
				$row_filter["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, ($from - 1), ($to + 1));
			}

			$offer_rows = new object_list($row_filter);
			if(!sizeof($offer_rows->arr()))
			{
				return $ol;
			}
			foreach($offer_rows->arr() as $row)
			{
				if($data["products_find_product_name"] && !(substr_count(strtolower($row->prop("product")) , strtolower($data["products_find_product_name"]))))
				{
					continue;
				}
				$filter["name"][] = $row->prop("product");
			}
			if(!(sizeof($filter["name"])))
			{
				return $ol;
			}
		}
		$ol = new object_list($filter);
		return $ol;
	}

	function _products_tbl(&$arr)
	{
		
		$tb =& $arr["prop"]["vcl_inst"];
		$this->no_warehouse = $arr["obj_inst"]->prop("no_warehouse");
		$this->_init_prod_list_list_tbl($tb);

		// get items
		if (!$_GET["tree_filter"])
		{
			$ot = new object_list();
		}
		else
		{
			$ot = new object_list(array(
				"parent" => $_GET["tree_filter"],
				"class_id" => array(CL_MENU,CL_SHOP_PRODUCT),
				"status" => array(STAT_ACTIVE, STAT_NOTACTIVE)
			));
		}

		

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

			$put = "";
			if ($o->class_id() != CL_MENU)
			{
				$put = html::href(array(
					"url" => $this->mk_my_orb("create_reception", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("Vii lattu")
				));
			}

			$name = $o->name();
			if ($o->class_id() == CL_MENU)
			{
				$name =  html::href(array(
					"url" => html::get_change_url($o->id()),
					"caption" => $name
				));
/*				$name = html::href(array(
					"url" => aw_url_change_var("tree_filter", $o->id()),
					"caption" => $name
				));
*/			}

			$tb->define_data(array(
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
					"value" => $o->ord(),
					"size" => 5
				)).html::hidden(array(
					"name" => "old_ord[".$o->id()."]",
					"value" => $o->ord()
				)),
				"hidden_ord" => $o->ord()
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

	function _init_prod_list_list_tbl(&$t)
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

		if($this->no_warehouse)
		{
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
		}
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

/*	function _products_tbl($arr)
	{
		
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_products_tbl($t);
		$filter = array(
//			"parent" => $_GET["tree_filter"],
			"class_id" => array(CL_SHOP_PRODUCT),
//			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
		);
		if(is_oid($arr["request"]["p_id"]) && $this->can("view", $arr["request"]["p_id"]))
		{
			$p_obj = obj($arr["request"]["p_id"]);
			if($p_obj->class_id() == CL_CRM_PERSON || $p_obj->class_id() == CL_CRM_COMPANY) $filter["offerer"] = $arr["request"]["p_id"];
			if($p_obj->class_id() == CL_MENU)
			{
				$offerers = new object_list(array(
					"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => $arr["request"]["p_id"],
				));
				$filter["offerer"] = $offerers->ids();
			}
		}

		//otsingust
		if(sizeof($arr["obj_inst"]->meta("search_data")) > 1)
		{
			$ol = $this->search_products($arr["obj_inst"]);
			$arr["obj_inst"]->set_meta("search_data", null);
			$arr["obj_inst"]->save();
		}
		else $ol = new object_list($filter);

		foreach($ol->arr() as $o)
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

			$put = "";
			if ($o->class_id() != CL_MENU)
			{
				$put = html::href(array(
					"url" => $this->mk_my_orb("create_reception", array(
						"id" => $arr["obj_inst"]->id(),
						"product" => $o->id()
					)),
					"caption" => t("Vii lattu")
				));
			}

			$t->define_data(array(
				"icon" => html::img(array("url" => icons::get_icon_url($o->class_id(), $o->name()))),
				"name" => $o->name(),
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
					"value" => $o->ord(),
					"size" => 5
				)).html::hidden(array(
					"name" => "old_ord[".$o->id()."]",
					"value" => $o->ord()
				)),
				"hidden_ord" => $o->ord()
			));

		}
	}
*/
	function _products_tr($arr)
	{
		$co = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		$warehouse = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		$warehouse->config = obj($warehouse->prop("conf"));
		$arr["prop"]["vcl_inst"] = new object_tree(array(
			"parent" => $warehouse->config->prop("prod_fld"),
			"class_id" => CL_MENU,
			"status" => array(STAT_ACTIVE, STAT_NOTACTIVE),
			"sort_by" => "objects.jrk"
		));

		
		$tv = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "prods",
				"persist_state" => true,
			),
			"root_item" => obj($warehouse->config->prop("prod_fld")),
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

		$co = $data["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		$warehouse = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		$warehouse->config = obj($warehouse->prop("conf"));
		$this->warehouse = $warehouse;
		$this->prod_type_fld = $warehouse->config->prop("prod_type_fld");
		$this->prod_tree_root = isset($_GET["tree_filter"]) ? $_GET["tree_filter"] : $warehouse->config->prop("prod_fld");

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

		$tb->add_button(array(
			"name" => "del",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud"),
			'action' => 'delete_cos',
		));

/*		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Lisa korvi"),
			"action" => "add_to_cart"
		));
*/
//		aw_global_set("changeform_target",  "_blank");
		$tb->add_button(array(
			"name" => "compare",
			"img" => "rte_table.gif",
			"tooltip" => t("Lisa v&otilde;rdlusesse"),
//			"action" => "add_to_compare",
//			"url" => "javascript:document.changeform.target='_blank';javascript:submit_changeform('add_to_compare');javascript:document.changeform.target='_self';",
			"url" => "javascript: void(0)",
			"onClick" => "document.changeform.target='_blank';submit_changeform('add_to_compare');document.changeform.target='_self'",
		//	"target" => "New window",

		));
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
						"alias_to" => $this->warehouse->id(),
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

	/**
		@attrib name=add_to_compare
		@param id required type=int acl=view
		@param sel optional
		@param group optional
	**/
	function add_to_compare($arr)
	{
		$this->id = $arr["id"];
		$this_obj = obj($arr["id"]);
		$owner = $this_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		$this->buyer = $owner->id();
		
		$ret = "";
		$t = new vcl_table;
//		$this->_init_compare_t($t);

		$ol= new object_list();
		foreach($arr["sel"] as $sel)
		{
		 	if(is_oid($sel) && $this->can("view" , $sel)) $ol->add($sel);
		}
		$categorys = $this->get_categorys($ol);
		foreach($categorys as  $category => $products)
		{
			$offerers = $this->get_offerers($products);
			$ret.= $this->make_category_table(array(
				"products" => $products,
				"category" => $category,
				"offerers" => $offerers
			));
		}

		$sf = new aw_template;
		$sf->db_init();
		$sf->tpl_init("automatweb");
		$sf->read_template("index.tpl");

		classload("core/util/minify_js_and_css");
		$sf->vars(array(
			"content"	=> $ret,
			"uid" => aw_global_get("uid"),
			"charset" => aw_global_get("charset"),
			"MINIFY_JS_AND_CSS" => minify_js_and_css::parse_admin_header($sf->parse("MINIFY_JS_AND_CSS"))
		));
//		die($ret);
		die($sf->parse());
	}

	function get_categorys($ol)
	{
		$categorys = array();
		foreach ($ol->arr() as $o)
		{
			$categorys[$o->prop("item_type")][] = $o;
		}
		return $categorys;
	}

	function get_offerers($products)
	{
		$offers_list = new object_list(array(
			"class_id" => CL_PROCUREMENT_OFFER,
			"lang_id" => array(),
			"CL_PROCUREMENT_OFFER.procurement.orderer" => $this->buyer,
		));
		$products_names = array();
		foreach($products as $product)
		{
			$products_names[$product->name()] = $product->name();
		}
		$offerers_list = new object_list();
		foreach ($offers_list->arr() as $offer)
		{
			$row_list = new object_list(array(
				"class_id" => CL_PROCUREMENT_OFFER_ROW,
				"lang_id" => array(),
//				"product" => $products_names,
				"CL_PROCUREMENT_OFFER_ROW.RELTYPE_OFFER" => $offer->id(),
			));
			foreach($row_list->arr() as $row)
			{
				if(is_oid($offer->prop("offerer")) && $this->can("view" , $offer->prop("offerer")) && in_array($row->prop("product") , $products_names))
				{
					$offerers_list->add($offer->prop("offerer"));
					break;
				}
			}
		}
		return $offerers_list;
	}

	function make_category_table($args)
	{
		extract($args);
//		foreach
		$t_main = new vcl_table;
//		$this->_init_compare_t($t_main);
		//esialgne tabel tuleb lihtsalt ridadena

		$category_name = "";
		if(is_oid($category) && $this->can("view", $category))
		{
			$category_obj = obj($category);
			$category_name = $category_obj->name();
		}
		$t_main->define_field(array("name" => "main_row" ));
		$t_main->define_data(array("main_row" => $category_name));
		$t_main->define_data(array("main_row" => $this->get_offerer_table($args)));
		return $t_main->draw();
	}

	function get_offerer_table($arr)
	{
		extract($arr);
		$t_offerer = new vcl_table;
//		$this->_init_compare_t($t_offerer);

		$t_offerer->define_field(array("name" => "product"));

		foreach($offerers->arr() as $offerer)
		{
			$t_offerer->define_field(array("name" => "offerer".$offerer->id()));
		}
		$data = array("product" => t("Hankija nimi"));

		foreach($offerers->arr() as $offerer)
		{
			$data["offerer".$offerer->id()] = $offerer->name();
		}
		$t_offerer->define_data($data);

		$prod_ids = array();
		foreach($products as $product)
		{
			$prod_ids[] = $product->id();
		}

		//manab k]ikide tegevuse ajaloo lingid
		$data = array("product" => t("Ajalugu"));
		foreach($offerers->arr() as $offerer)
		{
			$data["offerer".$offerer->id()] = html::href(array("url" => html::get_change_url($offerer->id(), array("group" => "sell_offers_grp_offers", "buyer" => $this->buyer , "products" => $prod_ids)), "caption" => t("Ajalugu")));
		}
		$t_offerer->define_data($data);

		//manab k]ikidetegevuse piirkonnad
		$data = array("product" => t("Piirkond"));
		foreach($offerers->arr() as $offerer)
		{
			if($offerer->class_id() == CL_CRM_COMPANY)
			{
				$data["offerer".$offerer->id()] = $offerer->prop("contact.piirkond.name");
			}
			else
			{
				$data["offerer".$offerer->id()] = $offerer->prop("address.piirkond.name");
			}
/*			if(is_oid($address) && $this->can("view" , $address))
			{
				$address_object = obj($address);
			}
			if(is_object($address_object) && is_oid($address_object->prop("piirkond")) && $this->can("view", $address_object->prop("piirkond")))
			{
				$area = obj($address_object->prop("piirkond"));
				$data["offerer".$offerer->id()] = $area->name();
			}
*/		}
		$t_offerer->define_data($data);

		//manab k]ikide soodustused
/*		$data = array("product" => t("Discount"));
		foreach($offerers->arr() as $offerer)
		{
				$data["offerer".$offerer->id()] = "10%";
		}

		$t_offerer->define_data($data);
*/
		$data = array("product" => $this->get_names_table(array("products" => $products, "category" => $category)));
		foreach($offerers->arr() as $offerer)
		{
			$data["offerer".$offerer->id()] = $this->get_products_table(array("offerer" => $offerer , "products" => $products, "category" => $category));
		}
		$t_offerer->define_data($data);

		return $t_offerer->draw();
	}

	function get_names_table($arr)
	{
		extract($arr);
		$t_names = new vcl_table;
//		$this->_init_compare_t($t_offerer);
		$t_names->define_field(array("name" => "name", "caption" => t("Toote nimetus"), "nowrap" => 1,));
		//$t_names->rowdefs["nowrap"] = 1;
		foreach($products as $product)
		{
			//teeb stringi lühemaks, juhul kui on üle 100 tähemärgi
			$name = substr($product->name(), 0, 100);
			if($product->prop("item_type") == $category) $t_names->define_data(array("name" => $name));
		}
		return $t_names->draw();
	}

	function get_products_table($arr)
	{
		extract($arr);
		$t_products = new vcl_table;
//		$this->_init_compare_t($t_offerer);
		$t_products->define_field(array("nowrap" => 1,"align" => "right","name" => "price","caption" => t("Hind") , "chgbgcolor" => "cutcopied"));
		$t_products->define_field(array("align" => "right","name" => "discount","caption" => t("Discount") , "chgbgcolor" => "cutcopied"));
		$t_products->define_field(array("nowrap" => 1,"align" => "right","name" => "project","caption" => t("Project") , "chgbgcolor" => "cutcopied"));
		$t_products->define_field(array("align" => "right","name" => "amount","caption" => t("Kogus") ,"chgbgcolor" => "cutcopied"));
		$t_products->define_field(array("name" => "unit","caption" => t("&Uuml;hik") ,"chgbgcolor" => "cutcopied"));
		$t_products->define_field(array("name" => "date","caption" => t("Kuup&auml;ev") ,"chgbgcolor" => "cutcopied"));
		$file_inst = get_instance(CL_FILE);
		foreach($products as $product)
		{
			if($product->prop("item_type") == $category)
			{
				$date=$amount=$unit=$price=$cutcopied=$project="";
				$offers_list = new object_list(array(
					"class_id" => CL_PROCUREMENT_OFFER,
					"lang_id" => array(),
					"CL_PROCUREMENT_OFFER.procurement.orderer" => $this->buyer,
					"offerer" => $offerer->id(),
				));

				foreach($offers_list->arr() as $offer)
				{
					$project =  $offer->prop("procurement.procurement_nr");
					if(!$project) $project = t("No number");
					$project = html::href(array(
							"caption" => $project,
							"title" =>  $offer->prop("procurement.name"),
							"url" => html::get_change_url($offer->prop("procurement")),
						));
					$discount = $offer->prop("discount");
					if($discount) $discount = $discount . " %";

					$row_list = new object_list(array(
						"class_id" => CL_PROCUREMENT_OFFER_ROW,
						"lang_id" => array(),
						"product" => $product->name(),
						"CL_PROCUREMENT_OFFER_ROW.RELTYPE_OFFER" => $offer->id(),
					));

					if(!sizeof($row_list->ids()))
					{
						continue;
					}

					$document_connections =  $offer->connections_from(array(
						"class" => array(CL_CRM_DOCUMENT, CL_CRM_MEMO, CL_CRM_DEAL, CL_FILE, CL_CRM_OFFER),
						"sort_by" => "id",
						"sort_by_num" => 1,
					));
					$doc_connection = reset($document_connections);
					if($doc_connection)
					{
						$doc = obj($doc_connection->prop("to"));
						$last_offer_file = $doc->name();
						if($doc->class_id() == CL_CRM_MEMO || $doc->class_id() == CL_CRM_DEAL || $doc->class_id() == CL_CRM_OFFER || $doc->class_id() == CL_CRM_DOCUMENT)
						{
							if($doc->get_first_obj_by_reltype("RELTYPE_FILE"))
							{
								$doc = $doc->get_first_obj_by_reltype("RELTYPE_FILE");
							}
						}

						if($doc->class_id() == CL_FILE)
						{
						//	$file = $file_inst->get_file_by_id($doc->id());
							$last_offer_file = $file_inst->get_url($doc->id()).'/'.$doc->name();
						}
						else
						{
							$last_offer_file = html::get_change_url($doc->id());
						}
					}
					else
					{
						$last_offer_file = "";
					}

					foreach($row_list->arr() as $row)
					{
						$date = "";
//						arr($row->prop("product") . " - " . $o->name());
						$amount = $row->prop("amount");
						$price = $row->prop("price");

						$price =  number_format($price, 2);

						if($row->prop("currency.symbol"))
						{
								$price = $row->prop("currency.symbol")." ".$price;
						}
						else
						{
							$price = $price. "" .$row->prop("currency.name");
						}
						$unit_obj = obj($row->prop("unit"));
						$unit = $unit_obj->prop("unit_code");
						$time = $offer->prop("date");
						if(!($time > 0))
						{
							$time = $offer->prop("accept_date");
						}

						if(!($time > 0))
						{
							$time = $row->prop("shipment");
						}
						if($time > 0)
						{
							$date = date("d.m.Y",$time);
						}

						$buyings_list = new object_list(array(
							"class_id" => CL_PURCHASE,
							"lang_id" => array(),
							"CL_PURCHASE.RELTYPE_OFFER.id" => $offer->id(),
						));
						if($row->prop("accept") && sizeof($buyings_list->arr()))
						{
							foreach($buyings_list->arr() as $buying)
							{
							}
							if($buying->prop("date") > 0) $date = date("d.m.Y",$buying->prop("date"));
							$amount = $row->prop("b_amount");
							$price = $row->prop("b_price");
							$cutcopied = "yellow";
						}
						if($last_offer_file)
						{
							$price = html::href(array(
								"caption" => $price,
								"url" => $last_offer_file
							));
						}
					}
				}

				$t_products->define_data(array(
					"price" => $price,
					"amount" => $amount,
					"unit" => $unit,
					"date" => $date,
					"cutcopied" => $cutcopied,
					"project" => $project,
					"discount" => $discount,
				));
			}
		}
		return $t_products->draw();
	}

	function handle_impl_submit($new_obj, $arr)
	{
		// so here we need to set a bunch of stuff for the company to work right
		// people are users, groups and stuff
/*		$new_obj->set_prop("do_create_users", 1);
		$new_obj->save();

		// apply the group creator
			// seems it is applied automatically

		// create a procurement center for it
		$pc = obj();
		$pc->set_parent($new_obj->id());
		$pc->set_class_id(CL_PROCUREMENT_IMPLEMENTOR_CENTER);
		$pc->set_name(sprintf(t("%s pakkumiste keskkond"), $new_obj->name()));
		$pc->save();
		$pc->connect(array(
			"to" => $new_obj->id(),
			"type" => "RELTYPE_MANAGER_CO"
		));

		// define an user redirect url for the company group
		$co_grp = $new_obj->get_first_obj_by_reltype("RELTYPE_GROUP");

		$cfg = get_instance("config");
		$es = $cfg->get_simple_config("login_grp_redirect");
		$this->dequote(&$es);
		$lg = aw_unserialize($es);
		$lg[$co_grp->prop("gid")]["pri"] = 1000000;
		$lg[$co_grp->prop("gid")]["url"] = html::get_change_url($pc->id(), array("group" => "p"));

		$ss = aw_serialize($lg, SERIALIZE_XML);
		$this->quote(&$ss);
		$cfg->set_simple_config("login_grp_redirect", $ss);
	*/
	}

	/**
		@attrib name=insert_offer
	**/
	function insert_offer($arr)
	{
		$o = obj();
		$o->set_parent($_GET["parent"]);
		$o->set_class_id(CL_PROCUREMENT_OFFER);
		$o->set_name(sprintf(t("pakkumine %s"), $o->id()));
		$o->save();
		return html::get_change_url($o->id());
	}

	/**
		@attrib name=insert_purchase
	**/
	function insert_purchase($arr)
	{
		$center_obj = obj($arr["id"]);
		$buyer = $center_obj->get_first_obj_by_reltype("RELTYPE_MANAGER_CO");
		$o = obj();
		$o->set_parent($arr["id"]);
		$o->set_class_id(CL_PURCHASE);
		$o->set_name(sprintf(t("ost: "), $o->id()));
		$o->set_prop("buyer" , $buyer->id());
		$o->set_prop("date" , time());
		$o->save();
		$o->connect(array(
			"to" => $buyer->id(),
			"reltype" => "RELTYPE_BUYER"
		));
		foreach(safe_array($arr["sel"]) as $id)
		{
			$o->connect(array(
				"to" => $id,
				"reltype" => "RELTYPE_OFFER"
			));
			if(is_oid($id))
			{
				$offer = obj($id);
				$o->set_name($o->name() . " " . $offer->name());
				$o->set_prop("offerer" , $offer->prop("offerer"));
				$o->save();
				if(is_oid($offer->prop("offerer")))
				{
					$o->connect(array(
						"to" => $offer->prop("offerer"),
						"reltype" => "RELTYPE_OFFERER",
					));
				}
			}
		}
		return html::get_change_url($o->id(), array("return_url" => $arr["post_ru"]));
	}

	/**
		@attrib name=send_mails
	**/
	function send_mails($arr)
	{
		$send_to = "";
		$mails = array();
		foreach($arr["sel"] as $cust)
		{
			$email = "";
			if($this->can("view" , $cust))
			{
				$customer = obj($cust);
				$email = $customer->get_first_obj_by_reltype("RELTYPE_EMAIL");
				if(is_oid($customer->prop("email")))
				{
					$email = obj($customer->prop("email"));
				}
				if(!is_object($email))
				{
					$email = $customer->get_first_obj_by_reltype("RELTYPE_EMAIL");
				}
				if(is_object($email))
				{
					$mails[] = $email->prop("mail");
				}
			}
		}
		$send_to = join($mails , ", ");
//		$user = aw_global_get("uid");

		$mfrom = aw_global_get("uid_oid");
		$user_obj = obj($mfrom);
		$person = $user_obj->get_first_obj_by_reltype("RELTYPE_PERSON");
		if(is_object($person))
		{
			$mfrom = $person->id();
		}
		return $this->mk_my_orb('new',array(
			'parent' => $arr['id'],
			"return_url" => $arr["post_ru"],
		 	"mto" => $send_to,
		 	"mfrom" => $mfrom,
		 ),CL_MESSAGE);
	}

	/**
		@attrib name=delete_cos
	**/
	function delete_cos($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function _sell_offers_prod_table($arr)
	{
		$arr["request"]["products"] = $this->_get_prod_filter($arr["request"]);
		$offered_products = array();
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array("name" => "name", "caption" => t("Nimetus")));
		if(is_array($arr["request"]["products"]))
		{
			$products = new object_list();
			$products->add($arr["request"]["products"]);
			$prod_names = $products->names();
		}
		$purchase_inst = get_instance(CL_PURCHASE);
		$offer_inst = get_instance(CL_PROCUREMENT_OFFER);

		$offerer = $arr["obj_inst"]->id();

		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if($co == $offerer)
		{
			$offerer = null;
		}

		$filter = array(
			"class_id" => array(CL_PURCHASE),
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol = new object_list($filter);
		$filter2 = array(
			"class_id" => array(CL_PROCUREMENT_OFFER),
			"lang_id" => array(),
			"site_id" => array(),
//			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol2 = new object_list($filter2);
		$ol->add($ol2);

		foreach($ol->arr() as $o)
		{
			$offers = $o->connections_from(array(
				'type' => "RELTYPE_OFFER",
			));

			if($o->class_id() == CL_PURCHASE)
			{
				$offers = $o->connections_from(array(
					'type' => "RELTYPE_OFFER",
				));
				foreach($offers as $offer_conn)
				{
					$offer_obj = obj($offer_conn->prop("to"));
					if($this->can("view" , $offer_obj->prop("procurement")))
					{
						$procurement = obj($offer_obj->prop("procurement"));
						$alt.= $procurement->name();


						$conns = $offer_obj->connections_to(array(
							'reltype' => 1,
							'class' => CL_PROCUREMENT_OFFER_ROW,
						));
						if(!sizeof($conns)) continue;
						foreach($conns as $conn)
						{
							if(is_oid($conn->prop("from")))
							{
								$row = obj($conn->prop("from"));
							}
							else continue;
							if(!$row->prop("accept"))
							{
								continue;
							}
							if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
							{
								$offered_products[$row->prop("product")][] = $o->id();
							}
						}
					}
				}
			}
			else
			{
				if($this->can("view" , $o->prop("procurement")))
				{
					$procurement = obj($o->prop("procurement"));
					$alt = t("Hange:")." ".$procurement->name();

					$conns = $o->connections_to(array(
						'reltype' => 1,
						'class' => CL_PROCUREMENT_OFFER_ROW,
					));
					if(!sizeof($conns)) continue;
					foreach($conns as $conn)
					{
						if(is_oid($conn->prop("from")))
						{
							$row = obj($conn->prop("from"));
						}
						else continue;
						if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
						{
							$offered_products[$row->prop("product")][] = $o->id();
						}
					}
				}
			}
		}

		foreach ($offered_products as $prod => $offers)
		{
			$id = $this->_get_product_id($prod);
			$lister = "<span id='row".$id."' style='display: none;'>";
			$table = new vcl_table;
			$table->name = "rows".$id;

			$this->_make_product_table_offers(&$table, $offers,$prod);

			$lister .= $table->draw();
			$lister .= "</span>";

			$proc_str = html::href(array(
				"url" => "#", //aw_url_change_var("proj", $p),
				"onClick" => "el=document.getElementById(\"row".$id."\"); if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0){if (el.style.display == \"block\") { d = \"none\";} else { d = \"block\";} } else { if (el.style.display == \"table-row\") {  d = \"none\"; } else {d = \"table-row\";} }  el.style.display=d;",
				"caption" => $prod
			));
			$t->define_data(array(
				"name" => $proc_str.$lister,
			));
		}
	}

	function _get_product_id($prod)
	{
		$prod_list = new object_list(array("name" => $prod));
		$prod_obj = reset($prod_list->arr());
		if(is_object($prod_obj))
		{
			return $prod_obj->id();
		}
		else
		{
			return rand(10000000 , 100000000);
		}
	}

	function _make_product_table_offers(&$t , $offers , $product)
	{
		$this->_init_product_table_offers_tbl(&$t);
		foreach($offers as $offer_id)
		{
			$o = obj($offer_id);
			$deal_no = $o->prop("deal_no");
			$prod_inf = $alt = "";
			$offers = $o->connections_from(array(
				'type' => "RELTYPE_OFFER",
			));
			$alt = t("Hange:");
			$product_row = null;
			if($o->class_id() == CL_PURCHASE)
			{
				$type = t("Ost");
				$deal = $o->prop("deal_no");
				$date = $o->prop("date");
				$offers = $o->connections_from(array(
					'type' => "RELTYPE_OFFER",
				));
				$alt = t("Hange:");
				foreach($offers as $offer_conn)
				{
					$offer_obj = obj($offer_conn->prop("to"));
					if($this->can("view" , $offer_obj->prop("procurement")))
					{
						$procurement = obj($offer_obj->prop("procurement"));
						$alt.= $procurement->name();


						$conns = $offer_obj->connections_to(array(
							'reltype' => 1,
							'class' => CL_PROCUREMENT_OFFER_ROW,
						));
						foreach($conns as $conn)
						{
							if(is_oid($conn->prop("from")))
							{
								$row = obj($conn->prop("from"));
							}
							else continue;
							if(!$row->prop("accept"))
							{
								continue;
							}
							if($row->prop("product") == $product)
							{
								$product_row = $row;
							}
						}
					}
				}
			}
			else
			{
				$type = t("Pakkumine");
				$deal = "";
				$date = $o->prop("accept_date");
				if($this->can("view" , $o->prop("procurement")))
				{
					$procurement = obj($o->prop("procurement"));
					$alt = t("Hange:")." ".$procurement->name();

					$conns = $o->connections_to(array(
						'reltype' => 1,
						'class' => CL_PROCUREMENT_OFFER_ROW,
					));
					if(!sizeof($conns)) continue;
					foreach($conns as $conn)
					{
						if(is_oid($conn->prop("from")))
						{
							$row = obj($conn->prop("from"));
						}
						else continue;
						if($row->prop("product") == $product)
						{
							$product_row = $row;
						}
					}
				}
			}
			//tegelt seda ei tohiks vaja olla, sest mingil juhul ei tohiks toode olla siin ilma pakkumise reata... a noh... müstikat juhtub ikka miskipärast
			if(!is_object($product_row))
			{
				continue;
			}
			$t->define_data(array(
				"deal" => $deal_no,
				"procurement" 	=> $proc_str.$lister,
				"amount"	=> number_format(($product_row->prop("b_amount")) ? $product_row->prop("b_amount"):$product_row->prop("amount"), 2),
				'price'		=>  number_format(($product_row->prop("b_price")) ? $product_row->prop("b_price"):$product_row->prop("price"), 2)." ".$product_row->prop("currency.name"),
				'date'		=> date("d.m.Y", $date),
				"name" 		=> html::href(array(
						"url" => html::get_change_url(
							$o->id(),
							array("return_url" => get_ru())),
						"caption" => $o->name(),
						"title" => $alt,
						)),
				'oid'		=> $o->id(),
				"type" 		=> $type,
				"unit"		=> $product_row->prop("unit.name"),
			));
		}
	}

	function _init_product_table_offers_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Liik"),
			"align" => "center",
			"sortable" => 1
		));

		//klikitav, lingi alt tekst on Hange: hanke nimi, millele pakkumine vastab)
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Pakkumise/ostu nimetus"),
			"align" => "center",
			"sortable" => 1
		));

		//Lepingu nr (see mis on märgitud ostu juurde, mitte ID)
		$t->define_field(array(
			"name" => "deal",
			"caption" => t("Leping"),
			"align" => "center",
			"sortable" => 1
		));
		//Kuupäev
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1
		));

		//Hind
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"sortable" => 1
		));

		//Kogus
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array("name" => "unit", "caption" => t("Unit")));

		/*
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));*/
	}

	/*
	function _sell_offers_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_offers_tbl($t);
		if(is_array($arr["request"]["products"]))
		{
			$products = new object_list();
			$products->add($arr["request"]["products"]);
			$prod_names = $products->names();
		}
		$purchase_inst = get_instance(CL_PURCHASE);
		$offer_inst = get_instance(CL_PROCUREMENT_OFFER);

		$offerer = $arr["obj_inst"]->id();

		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if($co == $offerer)
		{
			$offerer = null;
		}

		$filter = array(
			"class_id" => array(CL_PURCHASE),
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol = new object_list($filter);
		$filter2 = array(
			"class_id" => array(CL_PROCUREMENT_OFFER),
			"lang_id" => array(),
			"site_id" => array(),
//			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol2 = new object_list($filter2);
		$ol->add($ol2);

		foreach($ol->arr() as $o)
		{
			$deal_no = $o->prop("deal_no");
			$prod_inf = $alt = "";
			$offers = $o->connections_from(array(
				'type' => "RELTYPE_OFFER",
			));

			$prod_table = new vcl_table();
			$prod_table->define_field(array("name" => "name", "caption" => t("Nimetus")));
			$prod_table->define_field(array("name" => "price", "caption" => t("Hind")));
			$prod_table->define_field(array("name" => "amount", "caption" => t("Kogus")));
			$alt = t("Hange:");

			if($o->class_id() == CL_PURCHASE)
			{
				$type = t("Ost");
				$deal = $o->prop("deal_no");
				$date = $o->prop("date");


				$offers = $o->connections_from(array(
					'type' => "RELTYPE_OFFER",
				));
				$alt = t("Hange:");
				foreach($offers as $offer_conn)
				{
					$offer_obj = obj($offer_conn->prop("to"));
					if($this->can("view" , $offer_obj->prop("procurement")))
					{
						$procurement = obj($offer_obj->prop("procurement"));
						$alt.= $procurement->name();
					}
					$conns = $offer_obj->connections_to(array(
						'reltype' => 1,
						'class' => CL_PROCUREMENT_OFFER_ROW,
					));
					if(!sizeof($conns)) continue;
					foreach($conns as $conn)
					{
						if(is_oid($conn->prop("from")))
						{
							$row = obj($conn->prop("from"));
						}
						else continue;
						if(!$row->prop("accept"))
						{
							continue;
						}
						if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
						{
							$curr = obj($row->prop("currency"));
							$prod_inf.= $row->prop("product")." (".$row->prop("b_price").$curr->name().")\n";
							$prod_table->define_data(array(
								"name" => $row->prop("product"),
								"price" => $row->prop("b_price").$curr->name(),
								"amount" => $row->prop("b_amount"),
							));
						}
					}
				}
			}
			else
			{
				$type = t("Pakkumine");
				$deal = "";
				$date = $o->prop("accept_date");
				if($this->can("view" , $o->prop("procurement")))
				{
					$procurement = obj($o->prop("procurement"));
					$alt = t("Hange:")." ".$procurement->name();
				}
				$conns = $o->connections_to(array(
					'reltype' => 1,
					'class' => CL_PROCUREMENT_OFFER_ROW,
				));
				if(!sizeof($conns)) continue;
				foreach($conns as $conn)
				{
					if(is_oid($conn->prop("from")))
					{
						$row = obj($conn->prop("from"));
					}
					else continue;
					if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
					{
						if(!$this->can("view" , $row->prop("currency"))) continue;
						$curr = obj($row->prop("currency"));
						$prod_inf.= $row->prop("product")." (".$row->prop("price").$curr->name().")\n";
						$prod_table->define_data(array(
							"name" => $row->prop("product"),
							"price" => ($row->prop("b_price")) ? $row->prop("b_price"):$row->prop("price").$curr->name(),
							"amount" => ($row->prop("b_amount")) ? $row->prop("b_amount"):$row->prop("amount"),
						));
					}
				}
			}
			$t->define_data(array(
				"deal" => $deal_no,
				"products"	=> $prod_table->draw(),//$prod_inf,
//				"amount"	=> $row->prop("b_amount"),
//				'price'		=> $row->prop("b_price"),
				'date'		=> date("d.m.Y", $date),
				"name" 		=> html::href(array(
						"url" => html::get_change_url(
							$o->id(),
							array("return_url" => get_ru())),
						"caption" => $o->name(),
						"title" => $alt,
						)),
				'oid'		=> $o->id(),
				"type" 		=> $type,
			));

/*			foreach($offers as $offer_conn)
			{
				$offer_obj = obj($offer_conn->prop("to"));
				$conns = $offer_obj->connections_to(array(
					'reltype' => 1,
					'class' => CL_PROCUREMENT_OFFER_ROW,
				));
				foreach($conns as $conn)
				{
					if(is_oid($conn->prop("from")))$row = obj($conn->prop("from"));
					else continue;
					if(!$row->prop("accept")) continue;
					$unit = ""; $currency = "";
					if(is_oid($row->prop("unit")))
					{
						$unit_obj = obj($row->prop("unit"));
						$unit = $unit_obj->prop("unit_code");
					}
					if(is_oid($row->prop("currency")))
					{
						$currency = obj($row->prop("currency"));
						$currency = $currency->name();
					}
					if(!$row->prop("b_price"))
					{
						$row->set_prop("b_price" ,$row->prop("price"));
					}
					if(!$row->prop("b_amount"))
					{
						$row->set_prop("b_amount" ,$row->prop("amount"));
					}
					$t->define_data(array(
						"deal" => $deal_no,
						"product"	=> $row->prop("product"),
						"amount"	=> $row->prop("b_amount"),
						'price'		=> $row->prop("b_price"),
						'date'		=> date("d.m.Y", $row->prop("shipment")),
						'oid'		=> $row->id(),
					));
				}
			}
		}
	}
*/


	function _init_sell_offers_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "procurement",
			"caption" => t("Hange"),
			"align" => "center",
			"sortable" => 1
		));
		//klikitav, lingi alt tekst on Hange: hanke nimi, millele pakkumine vastab)
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Pakkumise/ostu nimetus"),
			"align" => "center",
			"sortable" => 1
		));

		//Lepingu nr (see mis on märgitud ostu juurde, mitte ID)
		$t->define_field(array(
			"name" => "deal",
			"caption" => t("Leping"),
			"align" => "center",
			"sortable" => 1
		));
		//Kuupäev
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1
		));
	}

	function _get_prod_filter($req)
	{
		if($req["reset_products"])
		{
			$_SESSION["procurement_centre"]["product_filter"] = null;
			return null;
		}
		if(isset($req["products"]))
		{
			$_SESSION["procurement_centre"]["product_filter"] = $req;
		}
		else
		{
			$req = $_SESSION["procurement_centre"]["product_filter"];
		}
		return $req["products"];
	}

	function _sell_offers_table($arr)
	{
		$arr["request"]["products"] = $this->_get_prod_filter($arr["request"]);
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_sell_offers_tbl($t);
		$show_this_offer = 1;
		if(is_array($arr["request"]["products"]))
		{
			$products = new object_list();
			$products->add($arr["request"]["products"]);
			$prod_names = $products->names();
			$show_this_offer = 0;
		}
		$purchase_inst = get_instance(CL_PURCHASE);
		$offer_inst = get_instance(CL_PROCUREMENT_OFFER);

		$offerer = $arr["obj_inst"]->id();

		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if($co == $offerer)
		{
			$offerer = null;
		}

		$filter = array(
			"class_id" => array(CL_PURCHASE),
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol = new object_list($filter);
		$filter2 = array(
			"class_id" => array(CL_PROCUREMENT_OFFER),
			"lang_id" => array(),
			"site_id" => array(),
//			"buyer" => $arr["request"]["buyer"],
			"offerer" => $offerer,
		);
		$ol2 = new object_list($filter2);
		$ol->add($ol2);

		foreach($ol->arr() as $o)
		{
			//juhul kui tooted on määratud, siis kõiki pakkumisi ei tahaks
			if(is_array($prod_names))
			{
				$show_this_offer = 0;
			}

			//teeb toodete tabelid ka
			$lister = "<span id='row".$o->id()."' style='display: none;'>";
			$table = new vcl_table;
			$table->name = "rows".$o->id();
			$this->_get_procurement_rows_table(array("proc" => $o, "t" => &$table));

			$deal_no = $o->prop("deal_no");
			$prod_inf = $alt = "";
			$offers = $o->connections_from(array(
				'type' => "RELTYPE_OFFER",
			));

			$prod_table = new vcl_table();
			$prod_table->define_field(array("name" => "name", "caption" => t("Nimetus")));
			$prod_table->define_field(array("name" => "price", "caption" => t("Hind")));
			$prod_table->define_field(array("name" => "amount", "caption" => t("Kogus")));
			$prod_table->define_field(array("name" => "unit", "caption" => t("Unit")));
			$alt = t("Hange:");

			if($o->class_id() == CL_PURCHASE)
			{
				$type = t("Ost");
				$deal = $o->prop("deal_no");
				$date = $o->prop("date");


				$offers = $o->connections_from(array(
					'type' => "RELTYPE_OFFER",
				));
				$alt = t("Hange:");
				foreach($offers as $offer_conn)
				{
					$offer_obj = obj($offer_conn->prop("to"));
					if($this->can("view" , $offer_obj->prop("procurement")))
					{
						$procurement = obj($offer_obj->prop("procurement"));
						$alt.= $procurement->name();


						$conns = $offer_obj->connections_to(array(
							'reltype' => 1,
							'class' => CL_PROCUREMENT_OFFER_ROW,
						));
						if(!sizeof($conns)) continue;
						foreach($conns as $conn)
						{
							if(is_oid($conn->prop("from")))
							{
								$row = obj($conn->prop("from"));
							}
							else continue;
							if(!$row->prop("accept"))
							{
								continue;
							}
							if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
							{
								$show_this_offer = 1;
								$curr = obj();
								$prod_inf.= $row->prop("product")." (".$row->prop("b_price").$curr->name().")\n";
								$table->define_data(array(
									"name" => $row->prop("product"),
									"price" => number_format($row->prop("b_price"), 2)." ".$row->prop("currency.name"),
									"amount" => number_format($row->prop("b_amount"), 2),
									"unit" => $row->prop("unit.name"),
								));
							}
						}
					}
				}
			}
			else
			{
				$type = t("Pakkumine");
				$deal = "";
				$date = $o->prop("accept_date");
				if($this->can("view" , $o->prop("procurement")))
				{
					$procurement = obj($o->prop("procurement"));
					$alt = t("Hange:")." ".$procurement->name();

					$conns = $o->connections_to(array(
						'reltype' => 1,
						'class' => CL_PROCUREMENT_OFFER_ROW,
					));
					if(!sizeof($conns)) continue;
					foreach($conns as $conn)
					{
						if(is_oid($conn->prop("from")))
						{
							$row = obj($conn->prop("from"));
						}
						else continue;
						if(!is_array($prod_names) || in_array($row->prop("product") , $prod_names))
						{
							if(!$this->can("view" , $row->prop("currency"))) continue;
							$show_this_offer = 1;
							$prod_inf.= $row->prop("product")." (".$row->prop("price").$row->prop("currency.name").")\n";

							$table->define_data(array(
								"name" => $row->prop("product"),
								"price" =>  number_format((($row->prop("b_price")) ? $row->prop("b_price"):$row->prop("price")), 2)." ".$row->prop("currency.name"),
								"amount" => number_format(($row->prop("b_amount")) ? $row->prop("b_amount"):$row->prop("amount"), 2),
								"unit" => $row->prop("unit.name"),
							));
						}
					}
				}
			}

			$lister .= $table->draw();
			$lister .= "</span>";

			//see praagib välja pakkumised, kui neil pole seda toodet , mis sai valitud
			if(!$show_this_offer)
			{
				continue;
			}

			$proc_str = html::href(array(
				"url" => "#", //aw_url_change_var("proj", $p),
				"onClick" => "el=document.getElementById(\"row".$o->id()."\"); if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0){if (el.style.display == \"block\") { d = \"none\";} else { d = \"block\";} } else { if (el.style.display == \"table-row\") {  d = \"none\"; } else {d = \"table-row\";} }  el.style.display=d;",
				"caption" => (is_object($procurement))?$procurement->name():"<".t("Hange M&auml;&auml;ramata").">",
			));

			$t->define_data(array(
				"deal" => $deal_no,
				"products"	=> $prod_table->draw(),//$prod_inf,
				"procurement" 	=> $proc_str.$lister,
				'date'		=> date("d.m.Y", $date),
				"name" 		=> html::href(array(
						"url" => html::get_change_url(
							$o->id(),
							array("return_url" => get_ru())),
						"caption" => $o->name(),
						"title" => $alt,
						)),
				'oid'		=> $o->id(),
				"type" 		=> $type,
				"unit"	=> $unit,
			));
		}
	}

	function _get_procurement_rows_table($arr)
	{
		$t =& $arr["t"];
		$t->define_field(array("name" => "name", "caption" => t("Nimetus")));
		$t->define_field(array("name" => "price", "caption" => t("Hind")));
		$t->define_field(array("name" => "amount", "caption" => t("Kogus")));
		$t->define_field(array("name" => "unit", "caption" => t("Unit")));
	}

	function _see_all_link($arr)
	{
		return html::href(array(
			"caption" => t("Show all"),
			"url" =>html::get_change_url($arr["request"]["id"] , array("group" => $arr["request"]["group"] , "buyer" => $arr["request"]["buyer"], "reset_products" => 1)),
		));
	}

}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management.aw,v 1.3 2007/12/06 14:33:24 kristo Exp $
// transport_management.aw - Veotellimuste haldus 
/*

@classinfo syslog_type=ST_TRANSPORT_MANAGEMENT relationmgr=yes no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property manager_org type=relpicker reltype=RELTYPE_MANAGER_ORG table=crm_transport_management
@caption Haldaja organisatsioon

@property config_manager type=relpicker reltype=RELTYPE_CONFIG_MANAGER table=crm_transport_management
@caption Seadete haldur

@property dispatchers_professions type=relpicker multiple=1 reltype=RELTYPE_DISPATCHER_PROFESSION store=connect
@caption Ekspediitorite ametinimetused

@property leaders_professions type=relpicker multiple=1 reltype=RELTYPE_LEADER_PROFESSION store=connect
@caption Juhtide ametinimetused

@groupinfo orders caption="Tellimused"

	@property orders_toolbar type=toolbar no_caption=1 group=orders_status,orders_routes,orders_clients,orders_dispatchers,my_orders
	@caption Staatuste t&ouml;&ouml;riistariba

	@groupinfo orders_status caption="Staatused" parent=orders

		@layout orders_status_frame type=hbox width=20%:80% group=orders_status

			@layout orders_status_left type=vbox parent=orders_status_frame group=orders_status

				@property orders_status_tree type=treeview store=no parent=orders_status_left captionside=top group=orders_status
				@caption Staatused

				@property orders_status_search_route type=textbox size=25 store=no parent=orders_status_left group=orders_status captionside=top
				@caption Marsruut

				@property orders_status_search_car_trailer type=textbox size=25 store=no parent=orders_status_left group=orders_status captionside=top
				@caption Auto/Treiler

			@layout orders_status_right type=vbox parent=orders_status_frame group=orders_status

				@property orders_status_table type=table parent=orders_status_right no_caption=1 group=orders_status
				@caption Staatuste tabel

	@groupinfo orders_routes caption="Marsruudid" parent=orders

		@layout orders_routes_frame type=hbox width=20%:80% group=orders_routes

			@layout orders_routes_left type=vbox parent=orders_routes_frame group=orders_routes

				@property orders_routes_tree type=treeview store=no parent=orders_routes_left captionside=top group=orders_routes
				@caption Marsruudid

			@layout orders_routes_right type=vbox parent=orders_routes_frame group=orders_routes

				@property orders_routes_table type=table parent=orders_routes_right no_caption=1 group=orders_routes
				@caption Marsruutide tabel

	@groupinfo orders_clients caption="Kliendid" parent=orders

		@layout orders_clients_frame type=hbox width=20%:80% group=orders_clients

			@layout orders_clients_left type=vbox parent=orders_clients_frame group=orders_clients

				@property orders_clients_tree type=treeview store=no parent=orders_clients_left captionside=top group=orders_clients
				@caption Kliendid

			@layout orders_clients_right type=vbox parent=orders_clients_frame group=orders_clients

				@property orders_clients_table type=table parent=orders_clients_right no_caption=1 group=orders_clients
				@caption Klientide tabel

	@groupinfo orders_dispatchers caption="Ekspediitorid" parent=orders

		@layout orders_dispatchers_frame type=hbox width=20%:80% group=orders_dispatchers

			@layout orders_dispatchers_left type=vbox parent=orders_dispatchers_frame group=orders_dispatchers

				@property orders_dispatchers_tree type=treeview store=no parent=orders_dispatchers_left captionside=top group=orders_dispatchers
				@caption Ekspediitorid

			@layout orders_dispatchers_right type=vbox parent=orders_dispatchers_frame group=orders_dispatchers

				@property orders_dispatchers_table type=table parent=orders_dispatchers_right no_caption=1 group=orders_dispatchers
				@caption Ekspediitorite tabel

@groupinfo routes caption="Marsruudid"

	@property routes_toolbar type=toolbar no_caption=1 group=routes
	@caption Marsruutide t&ouml;&ouml;riistariba

	@layout routes_frame type=hbox width=20%:80% group=routes

		@layout routes_frame_right type=vbox parent=routes_frame group=routes
	
			@property routes_search_address type=textbox size=25 store=no parent=routes_frame_right group=routes captionside=top
			@caption Otsi aadressi

		@layout routes_frame_left type=vbox parent=routes_frame group=routes

			@property routes_table type=table no_caption=1 parent=routes_frame_left group=routes
			@caption Marsruutide tabel

@groupinfo carriages caption="Veod"

	@property carriages_toolbar type=toolbar no_caption=1 group=carriages,my_carriages
	@caption Vedude t&ouml;&ouml;riistariba

	@layout carriages_frame type=hbox width=20%:80% group=carriages

		@layout carriages_frame_right type=vbox parent=carriages_frame group=carriages

			@property carriages_search_client type=textbox size=25 store=no parent=carriages_frame_right group=carriages captionside=top
			@caption Otsi kliendi j&auml;rgi

		@layout carriages_frame_left type=vbox parent=carriages_frame group=carriages

			@property carriages_table type=table no_caption=1 parent=carriages_frame_left group=carriages
			@caption Vedude tabel

@groupinfo resources caption="Ressursid"


	@groupinfo resources_trucks caption="Veoautod" parent=resources

		@property resources_trucks_toolbar type=toolbar no_caption=1 group=resources_trucks
		@caption Veoautode t&ouml;&ouml;riistariba

		@property resources_trucks_table type=table no_caption=1 group=resources_trucks
		@caption Veoautode tabel

	@groupinfo resources_trailers caption="Haagised" parent=resources
	
		@property resources_trailers_toolbar type=toolbar no_caption=1 group=resources_trailers
		@caption Haagiste t&ouml;&ouml;riistariba

		@property resources_trailers_table type=table no_caption=1 group=resources_trailers
		@caption Haagiste tabel

@groupinfo my_desktop caption="Minu T&ouml;&ouml;laud"

	@groupinfo my_clients caption="Kliendid" parent=my_desktop

		@property foo type=text store=no group=my_clients
		@caption foo

	@groupinfo my_orders caption="Tellimused" parent=my_desktop

		@layout my_orders_frame type=hbox width=20%:80% group=my_orders

			@layout my_orders_left type=vbox parent=my_orders_frame group=my_orders

				@property my_orders_tree type=treeview store=no parent=my_orders_left captionside=top group=my_orders
				@caption Staatused

				@property my_orders_search_route type=textbox size=25 store=no parent=my_orders_left group=my_orders captionside=top
				@caption Marsruut

				@property my_orders_search_car_trailer type=textbox size=25 store=no parent=my_orders_left group=my_orders captionside=top
				@caption Auto/Treiler

			@layout my_orders_right type=vbox parent=my_orders_frame group=my_orders

				@property my_orders_table type=table parent=my_orders_right no_caption=1 group=my_orders
				@caption Staatuste tabel

	@groupinfo my_carriages caption="Veod" parent=my_desktop

		property my_carriages_toolbar type=toolbar no_caption=1 group=my_carriages
		caption Vedude t&ouml;&ouml;riistariba

		@layout my_carriages_frame type=hbox width=20%:80% group=my_carriages
		
			@layout my_carriages_left type=vbox parent=my_carriages_frame group=my_carriages

				@property my_carriages_search_client type=textbox size=25 store=no parent=my_carriages_left group=my_carriages captionside=top
				@caption Otsi kliendi j&auml;rgi

			@layout my_carriages_right type=vbox parent=my_carriages_frame group=my_carriages

				@property my_carriages_table type=table no_caption=1 parent=my_carriages_right group=my_carriages
				@caption Vedude tabel


@reltype MANAGER_ORG value=1 clid=CL_CRM_COMPANY
@caption Haldaja organisatsioon

@reltype CONFIG_MANAGER value=2 clid=CL_CFGMANAGER
@caption Seadete haldur

@reltype DISPATCHER_PROFESSION value=3 clid=CL_CRM_PROFESSION
@caption Ekpediitori ametinimetus

@reltype LEADER_PROFESSION value=4 clid=CL_CRM_PROFESSION
@caption Juhi ametinimetus

@reltype CARRIAGE_ORDER value=5 clid=CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE_ORDER
@caption Veotellimus

@reltype ROUTE value=6 clid=CL_CRM_TRANSPORT_MANAGEMENT_ROUTE
@caption Marsruut

@reltype CARRIAGE value=7 clid=CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE
@caption Vedu

@reltype TRUCK value=8 clid=CL_CRM_TRANSPORT_MANAGEMENT_TRUCK
@caption Veoauto

@reltype TRAILER value=9 clid=CL_CRM_TRANSPORT_MANAGEMENT_TRAILER
@caption Haagis


*/

define('CARRIAGE_ORDER_STATUS_NEW', 1);
define('CARRIAGE_ORDER_STATUS_PLANNED', 2);
define('CARRIAGE_ORDER_STATUS_ON_THE_ROAD', 3);
define('CARRIAGE_ORDER_STATUS_OVER_DEADLINE', 4);
define('CARRIAGE_ORDER_STATUS_CANCELED', 5);
define('CARRIAGE_ORDER_STATUS_COMPLETED', 6);
define('CARRIAGE_ORDER_STATUS_ARCHIVED', 7);

class crm_transport_management extends class_base
{

	var $status_array = array();

	function crm_transport_management()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT
		));

		$this->status_array = array(
			CARRIAGE_ORDER_STATUS_NEW => t('Uued'),
			CARRIAGE_ORDER_STATUS_PLANNED => t('Planeeritud'),
			CARRIAGE_ORDER_STATUS_ON_THE_ROAD => t('Hetkel vedamisel'),
			CARRIAGE_ORDER_STATUS_OVER_DEADLINE => t('&Uuml;le t&auml;htaja'),
			CARRIAGE_ORDER_STATUS_CANCELED => t('Katkestatud'),
			CARRIAGE_ORDER_STATUS_COMPLETED => t('Valmis'),
			CARRIAGE_ORDER_STATUS_ARCHIVED => t('Arhiveeritud')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function _get_orders_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus tellimus"),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 5, // RELTYPE_CARRIAGE_ORDER
				'return_url' => get_ru()
			), CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE_ORDER),
		));
		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta tellimus'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud tellimused kustutada?')
		));

		$t->add_button(array(
			"name" => "archive",
			"img" => "new.gif",
			"tooltip" => t("Lisa tellimus arhiivi"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		return PROP_OK;
	}

	function _get_orders_status_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'orders_status_tree',
			'root_url' => 'http://www.neti.ee',
		));
		foreach ( $this->status_array as $status_key => $status_value )
		{
			$t->add_item(0,array(
				"id" => $status_key,
				"name" => $status_value,
			//	"iconurl" => "",
				"url" => $this->mk_my_orb("do_something",array()),
			));

		}
		return PROP_OK;
	}

	function _get_orders_status_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_default_sortby('deadline');
		$t->set_default_sorder('asc');

		$t->define_field(array(
			'name' => 'client',
			'caption' => t('Klient'),
		));
		$t->define_field(array(
			'name' => 'project_name',
			'caption' => t('Projekti nimetus'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'deadline',
			'caption' => t('T&auml;htaeg'),
			'sortable' => true,
			'format' => 'd.m.Y',
			'numeric' => 1,
			'type' => 'time'
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));
		$t->define_field(array(
			'name' => 'change',
			'caption' => t('Muuda'),
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'align' => 'center',
			'width' => '5%'
		));

		$orders = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_CARRIAGE_ORDER'
		));

		foreach (safe_array($orders) as $order)
		{
			$order_obj  = $order->to();
			$order_oid = $order->prop('to');

			$client_oid = $order_obj->prop('orderer');
			$client_name = t('puudub');
			if ( $this->can('view', $client_oid) )
			{
				$client_obj = new object($client_oid);
				$client_name = $client_obj->name();
			}

			$start_location_oid = $order_obj->prop('loading_location');
			$start_location = t('puudub');
			if ( $this->can('view', $start_location_oid) )
			{
				$start_location_obj  = new object($start_location_oid);
				$start_location = $start_location_obj->name();
			}

			$end_location_oid = $order_obj->prop('unloading_location');
			$end_location = t('puudub');
			if ( $this->can('view', $end_location_oid) )
			{
				$end_location_obj  = new object($end_location_oid);
				$end_location = $end_location_obj->name();
			}

			$t->define_data(array(
				'client' => $client_name,
				'project_name' => '',
				'start_location' => $start_location,
				'end_location' => $end_location,
				'deadline' => $order_obj->prop('deadline'),
				'amount' => '',
				'change' => html::href(array(
					'caption' => t('Muuda'),
					'url' => $this->mk_my_orb('change', array(
						'id' => $order_oid,
						'return_url' => get_ru()
					), CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE_ORDER)
				)),
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$order_oid.']',
					'value' => $order_oid
				))
			));
		}

		return PROP_OK;
	}

	function _get_orders_routes_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'orders_routes_tree',
		//	'root_url' => 'http://www.neti.ee',
		));

		$routes = new object_list(array(
			'class_id' => CL_CRM_TRANSPORT_MANAGEMENT_ROUTE
		));

		foreach ($routes->arr() as $route)
		{
			$t->add_item(0, array(
				'id' => $route->id(),
				'name' => $route->name()
			));
		}

		return PROP_OK;
	}

	function _get_orders_routes_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'client',
			'caption' => t('Klient'),
		));
		$t->define_field(array(
			'name' => 'project_name',
			'caption' => t('Projekti nimetus'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'deadline',
			'caption' => t('T&auml;htaeg'),
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus'),
		));
		$t->define_field(array(
			'name' => 'carriage',
			'caption' => t('Vedu'),
		));


		$t->define_data(array(
			'client' => '',
			'project_name' => '',
			'start_location' => '',
			'end_location' => '',
			'deadline' => '',
			'amount' => '',
			'status' => '',
			'carriage' => '',
		));

		return PROP_OK;
	}

	function _get_orders_clients_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'orders_clients_tree',
		//	'root_url' => 'http://www.neti.ee',
		));
		$t->add_item(0, array(
			'id' => 'foobar',
			'name' => 'Klientide nimekiri'
		));
/*
		foreach ( $this->status_array as $status_key => $status_value )
		{
			$t->add_item(0,array(
				"id" => $status_key,
				"name" => $status_value,
				"url" => $this->mk_my_orb("do_something",array()),
			));

		}
*/
		return PROP_OK;
	}

	function _get_orders_clients_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'project_name',
			'caption' => t('Projekti nimetus'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'deadline',
			'caption' => t('T&auml;htaeg'),
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus'),
		));
		$t->define_field(array(
			'name' => 'income',
			'caption' => t('Tulu'),
		));


		return PROP_OK;
	}

	function _get_orders_dispatchers_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'orders_dispatchers_tree',
		//	'root_url' => 'http://www.neti.ee',
		));
		$t->add_item(0, array(
			'id' => 'foobar',
			'name' => 'Ekspediitorite nimekiri'
		));
/*
		foreach ( $this->status_array as $status_key => $status_value )
		{
			$t->add_item(0,array(
				"id" => $status_key,
				"name" => $status_value,
				"url" => $this->mk_my_orb("do_something",array()),
			));

		}
*/
		return PROP_OK;
	}

	function _get_orders_dispatchers_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'client',
			'caption' => t('Klient'),
		));
		$t->define_field(array(
			'name' => 'project_name',
			'caption' => t('Projekti nimetus'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'deadline',
			'caption' => t('T&auml;htaeg'),
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus'),
		));
		$t->define_field(array(
			'name' => 'income',
			'caption' => t('Tulu'),
		));


		return PROP_OK;
	}

	function _get_routes_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus marsruut"),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 6, // RELTYPE_ROUTE
				'return_url' => get_ru()
			), CL_CRM_TRANSPORT_MANAGEMENT_ROUTE),
		));
		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta marsruut'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud marsruudid kustutada?')
		));
		return PROP_OK;
	}

	function _get_routes_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_default_sortby('name');
		$t->set_default_sorder('asc');

		$t->define_field(array(
			'name' => 'name',
			'sortable' => true,
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'cars_trailers',
			'caption' => t('Autod/Haagised')
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'align' => 'center',
			'width' => '5%'
		));

		$routes = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_ROUTE'
		));
		
		foreach (safe_array($routes) as $route)
		{
			$route_obj = $route->to();
			$route_oid = $route->prop('to');
			$start_location_oid = $route_obj->prop('start_location');
			$start_location = t('puudub');

			if ($this->can('view', $start_location_oid))
			{
				$start_location_obj = new object($start_location_oid);
				$start_location = $start_location_obj->name();
			}


			$end_location_oid = $route_obj->prop('end_location');
			$end_location = t('puudub');
			if ($this->can('view', $end_location_oid))
			{
				$end_location_obj = new object($end_location_oid);
				$end_location = $end_location_obj->name();
			}

			$t->define_data(array(
				'name' => $route_obj->name(),
				'start_location' => $start_location,
				'end_location' => $end_location,
				'cars_trailers' => '',
				'select' => html::checkbox(array(
					'name' => 'selected_ids['.$route_oid.']',
					'value' => $route_oid
				))
			));

		}
		return PROP_OK;
	}

	function _get_carriages_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus vedu"),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 7, // RELTYPE_CARRIAGE
				'return_url' => get_ru()
			), CL_CRM_TRANSPORT_MANAGEMENT_CARRIAGE),

		));
		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta vedu'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud veod kustutada?')
		));

		return PROP_OK;
	}

	function _get_carriages_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'route',
			'caption' => t('Marsruut')
		));
		$t->define_field(array(
			'name' => 'dispatcher',
			'caption' => t('Ekspediitor')
		));
		$t->define_field(array(
			'name' => 'start_date',
			'caption' => t('Alguskuup&auml;ev'),
		));
		$t->define_field(array(
			'name' => 'end_date',
			'caption' => t('L&otilde;ppkuup&auml;ev'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'cars_trailers',
			'caption' => t('Autod/Haagised')
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus')
		));
		$t->define_field(array(
			'name' => 'income',
			'caption' => t('Tulu')
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali'),
			'align' => 'center',
			'width' => '5%'
		));

		$carriages = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_CARRIAGE'
		));

		foreach (safe_array($carriages) as $carriage)
		{
			$carriage_obj = $carriage->to();
			$carriage_oid = $carriage->prop('to');
		
			$t->define_data(array(
				'route' => '',
				'dispatcher' => '',
				'start_date' => '',
				'end_date' => '',
				'start_location' => '',
				'end_location' => '',
				'cars_trailers' => '',
				'status' => '',
				'income' => '',
				'select'  => html::checkbox(array(
					'name' => 'selected_ids['.$carriage_oid.']',
					'value' => $carriage_oid
				))
			));
		}
		return PROP_OK;
	}


	// xxx ???
	// äkki saab näidata lihtsalt seda orders toolbari siin ka ...
	function _get_my_orders_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus tellimus"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta tellimus"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		$t->add_button(array(
			"name" => "archive",
			"img" => "delete.gif",
			"tooltip" => t("Lisa tellimus arhiivi"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		return PROP_OK;
	}

	function _get_my_orders_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->start_tree(array(
			'type' => TREE_DHTML,
			'root_name' => 'my_orders_tree',
		));
		foreach ( $this->status_array as $status_key => $status_value )
		{
			$t->add_item(0,array(
				"id" => $status_key,
				"name" => $status_value,
			//	"iconurl" => "",
				"url" => $this->mk_my_orb("do_something",array()),
			));

		}
		return PROP_OK;
	}

	function _get_my_orders_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'client',
			'caption' => t('Klient'),
		));
		$t->define_field(array(
			'name' => 'project_name',
			'caption' => t('Projekti nimetus'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'deadline',
			'caption' => t('T&auml;htaeg'),
		));
		$t->define_field(array(
			'name' => 'amount',
			'caption' => t('Kogus'),
		));

		return PROP_OK;
	}
/*
	// xxx --showing the carriages toolbar here
	function _get_my_carriages_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus vedu"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta vedu"),
			"url" => $this->mk_my_orb("do_something",array()),
		));
		return PROP_OK;
	}
*/
	function _get_my_carriages_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'route',
			'caption' => t('Marsruut')
		));
		$t->define_field(array(
			'name' => 'start_date',
			'caption' => t('Alguskuup&auml;ev'),
		));
		$t->define_field(array(
			'name' => 'end_date',
			'caption' => t('L&otilde;ppkuup&auml;ev'),
		));
		$t->define_field(array(
			'name' => 'start_location',
			'caption' => t('L&auml;htekoht'),
		));
		$t->define_field(array(
			'name' => 'end_location',
			'caption' => t('Sihtkoht'),
		));
		$t->define_field(array(
			'name' => 'cars_trailers',
			'caption' => t('Autod/Haagised')
		));
		$t->define_field(array(
			'name' => 'status',
			'caption' => t('Staatus')
		));
		$t->define_field(array(
			'name' => 'income',
			'caption' => t('Tulu')
		));

		return PROP_OK;
	}

	function _get_resources_trucks_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus veoauto"),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 8, // RELTYPE_TRUCK
				'return_url' => get_ru()
			), CL_CRM_TRANSPORT_MANAGEMENT_TRUCK),
		));
		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta veoauto'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud veoautod kustutada?')
		));

		return PROP_OK;
	}

	function _get_resources_trucks_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'nr',
			'caption' => t('Number')
		));
		$t->define_field(array(
			'name' => 'bodytype',
			'caption' => t('Keret&uuml;&uuml;p')
		));
		$t->define_field(array(
			'name' => 'drivers',
			'caption' => t('Autojuhid')
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali')
		));
		return PROP_OK;
	}

	function _get_resources_trailers_toolbar($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus haagis"),
			"url" => $this->mk_my_orb("new",array(
				'alias_to' => $arr['obj_inst']->id(),
				'parent' => $arr['obj_inst']->id(),
				'reltype' => 9, // RELTYPE_TRAILER
				'return_url' => get_ru()
			), CL_CRM_TRANSPORT_MANAGEMENT_TRAILER),
		));
		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta haagis'),
			'action' => '_delete_objects',
			'confirm' => t('Oled kindel et soovid valitud haagised kustutada?')
		));

		return PROP_OK;
	}

	function _get_resources_trailers_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimetus')
		));
		$t->define_field(array(
			'name' => 'nr',
			'caption' => t('Number')
		));
		$t->define_field(array(
			'name' => 'bodytype',
			'caption' => t('Keret&uuml;&uuml;p')
		));
		$t->define_field(array(
			'name' => 'drivers',
			'caption' => t('Autojuhid')
		));
		$t->define_field(array(
			'name' => 'select',
			'caption' => t('Vali')
		));
		return PROP_OK;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (oid INT PRIMARY KEY NOT NULL)');
			return true;
		}

		switch ($field)
		{
			case 'manager_org':
			case 'config_manager':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
/*
			case 'dispatchers_professions':
			case 'leaders_professions':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
*/
                }

		return false;
	}

	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete();
			}
		}
		return $arr['post_ru'];
	}

}
?>

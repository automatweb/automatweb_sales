<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/transport_management/crm_transport_management_route.aw,v 1.4 2007/12/06 14:33:24 kristo Exp $
// route.aw - Marsruut 
/*

@classinfo syslog_type=ST_ROUTE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@tableinfo crm_transport_management_route index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property start_location type=relpicker reltype=RELTYPE_START_LOCATION table=crm_transport_management_route
	@caption Alguspunkt

	@property end_location type=relpicker reltype=RELTYPE_END_LOCATION table=crm_transport_management_route
	@caption Sihtpunkt

	@property route_status type=select table=crm_transport_management_route
	@caption Staatus

@groupinfo route_content caption="Marsruudi sisu"
@default group=route_content

	@layout route_content_frame type=hbox width=20%:80% 
	
		@layout route_content_left type=vbox parent=route_content_frame
		
			@property route_tree type=treeview parent=route_content_left captionside=top
			@caption Puu

		@layout route_content_right type=vbox parent=route_content_frame

			@property route_table type=table parent=route_content_right captionside=top
			@caption Tabel

@reltype START_LOCATION value=1 clid=CL_CRM_CITY
@caption Alguspunkt

@reltype END_LOCATION value=2 clid=CL_CRM_CITY
@caption Sihtpunkt

@reltype ADDRESS value=3 clid=CL_CRM_ADDRESS
@caption Aadress

*/

define('ROUTE_STATUS_ACTIVE', 1);
define('ROUTE_STATUS_ARCHIVED', 2);

class crm_transport_management_route extends class_base
{
	const AW_CLID = 1084;


	var $route_status = array();

	function crm_transport_management_route()
	{
		$this->init(array(
			"tpldir" => "applications/crm/transport_management/crm_transport_management_route",
			"clid" => CL_CRM_TRANSPORT_MANAGEMENT_ROUTE
		));

		$this->route_status = array(
			ROUTE_STATUS_ACTIVE => t('Aktiivne'),
			ROUTE_STATUS_ARCHIVED => t('Arhiveeritud')
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'route_status':
				$prop['options'] = $this->route_status;
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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_post_save($arr)
	{
		$city_ids = explode(',', $arr['request']['route_tree_data']);
		$ord = 10;
		$city_ord = array();
		foreach ($city_ids as $city_id)
		{
			$address = new object();
			$address->set_class_id(CL_CRM_ADDRESS);
			$address->set_parent($arr['id']);
			$address->save();

			$address->connect(array(
				'to' => $city_id,
				'type' => 'RELTYPE_LINN'
			));
			$address->set_prop('linn', $city_id);
			$address->save();
			$arr['obj_inst']->connect(array(
				'to' => $address,
				'type' => 'RELTYPE_ADDRESS'
			));
			$city_ord[$city_id] = $ord;
			$ord += 10;
		}
		$arr['obj_inst']->set_meta('city_ord', $city_ord);
		$arr['obj_inst']->save();
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

	function _get_route_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
                $t->start_tree(array(
			'type' => TREE_DHTML_WITH_BUTTONS,
			'root_name' => 'route_tree',
			'tree_id' => 'routes_tree',
			'persist_state' => true,
			'checkbox_data_var' => 'route_tree_data'
                ));

		$city_ol = new object_list(array(
			'class_id' => CL_CRM_CITY
		));

		foreach ($city_ol->arr() as $city)
		{
			$t->add_item(0, array(
				'id' => $city->id(),
				'name' => $city->name()	,
				"checkbox" => "button"
			));
		}

		return PROP_OK;
	}

	function _get_route_table($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$t->define_field(array(
			'name' => 'country',
			'caption' => t('Riik')
		));
		$t->define_field(array(
			'name' => 'city',
			'caption' => t('Linn')
		));
		$t->define_field(array(
			'name' => 'ord',
			'caption' => t('J&auml;rjekord'),
			'align' => 'center',
			'width' => '10%'
		));

		$addresses = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_ADDRESS'
		));

		$city_ord = $arr['obj_inst']->meta('city_ord');

		foreach (safe_array($addresses) as $address)
		{
			$address_obj = $address->to();
			$country_oid = $address_obj->prop('riik');
			$country = "";
			if ($this->can('view', $country_oid))
			{
				$country_obj = new object($country_oid);
				$country = $country_obj->name();
			}
			$city_oid = $address_obj->prop('linn');
			$city = "";
			if ($this->can('view', $city_oid))
			{
				$city_obj = new object($city_oid);
				$city = $city_obj->name();
			}

			$t->define_data(array(
				'country' => $country,
				'city' => $city,
				'ord' => html::textbox(array(
					'name' => 'ord['.$city_oid.']',
					'value' => $city_ord[$city_oid],
					'size' => 3
				))
			));
		}
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
			case 'start_location':
			case 'end_location':
			case 'route_status':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
                }

		return false;
	}
}
?>

<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/spa_bookings/spa_products_overview.aw,v 1.4 2008/04/08 12:40:26 kristo Exp $
// spa_products_overview.aw - Broneeringute toitlustuse haldus 
/*

@classinfo syslog_type=ST_SPA_PRODUCTS_OVERVIEW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

tableinfo spa_products_overview index=id master_index=oid master_table=objects

@default table=objects
@default group=general


	@property rooms type=relpicker multiple=1 reltype=RELTYPE_ROOM field=meta method=serialize
	@caption Ruumid

@groupinfo reservations caption="Reserveeringud"
@default group=reservations
	
	@layout hsplit type=hbox width=25%:75%
		
		@layout left type=vbox closeable=1 area_caption=Ruumid parent=hsplit
			@property roomtree type=treeview no_caption=1 parent=left

		@layout right type=vbox closeable=1 area_caption=Broneeringud&nbsp;ja&nbsp;toitlustus parent=hsplit
			@property brontable type=table no_caption=1 parent=right


@reltype ROOM value=1 clid=CL_ROOM
@caption Ruum

*/

class spa_products_overview extends class_base
{
	const AW_CLID = 1248;

	function spa_products_overview()
	{
		$this->init(array(
			"tpldir" => "applications/spa_bookings/spa_products_overview",
			"clid" => CL_SPA_PRODUCTS_OVERVIEW
		));
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

	//-- methods --//
	function _get_roomtree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		if(count($rms = $arr["obj_inst"]->prop("rooms")))
		{
			foreach($rms as $rm)
			{
				if($this->can("view", $rm))
				{
					$rm = obj($rm);
					$t->add_item(0, array(
						"id" => $rm->id(),
						"name" => $rm->name(),
					));
				}
			}
		}
		if($t->node_has_children(0))
		{
			$t->add_item(0, array(
				"id" => -1,
				"name" => t("K&otilde;ik toad"),
			));
		}
		else
		{
			warning(t("Ruumid on valimata"), 2);
		}
	}


	function _get_brontable($arr)
	{
		$this->_init_brontable(&$arr);

		$t = &$arr["prop"]["vcl_inst"];
		
		$res = new object_list(array(
			"class_id" => CL_RESERVATION,
			"resource" => $arr["obj_inst"]->prop("rooms"),
		));
		$res_inst = get_instance(CL_RESERVATION);
		foreach($res->arr() as $oid => $obj)
		{
			/*
			$t->define_data(array(
				"product" => $obj->prop("resource.name"),
			));
			*/
			$products = $res_inst->get_products_data(array(
				"reservation" => $oid,
				"products" => array(),
			));
			foreach($products as $prod => $data)
			{
				$prod_data[$obj->prop("resource")][] = array_merge($data, array("product" => $prod, "reservation" => $oid));
				/*
				$t->define_data(array(
					"product" => "",
					"amount" => 23,
					"time" => 12-14,
					"grouper" => "1",
				));
				*/
			}
		}
		foreach($prod_data as $room => $products)
		{
			$room_obj = obj($room);
			foreach($products as $product_data)
			{
				$prd_obj = obj($product_data["product"]);
				$res_obj = obj($product_data["reservation"]);
				$t->define_data(array(
					"product" => html::href(array(
						"caption" => $prd_obj->name(),
						"url" => $this->mk_my_orb("change", array(
							"id" => $prd_obj->id(),
							"return_url" => get_ru(),
						)),
					)),
					"amount" => $product_data["amount"],
					"time" => date("h:i:s d.m.Y", $res_obj->prop("start1"))." - ".date("h:i:s d.m.Y", $res_obj->prop("end")),
					"room" => html::href(array(
						"caption" => $room_obj->name(),
						"url" => $this->mk_my_orb("change", array(
							"id" => $room_obj->id(),
							"return_url" => get_ru(),
						), CL_ROOM),
					)),
				));
			}
		}
	}


	function _init_brontable($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
		));
		$t->set_rgroupby(array("room"));
	}

	function do_db_upgrade($tbl, $f)
	{
		if (empty($f))
		{
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE '.$tbl.' (
				id INT PRIMARY KEY NOT NULL,
				rooms text
			)');
			return true;
		}

		switch($f)
		{
			case "rooms":
				$this->db_add_col($tbl, array(
					"name" => $f,
					"type" => "text",
				));
				return true;
		}

		return false;
	}
}


?>

<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer_products_list.aw,v 1.3 2008/01/31 13:54:13 kristo Exp $
// crm_offer_products_list.aw - Pakkumise toodete nimekiri 
/*

@classinfo syslog_type=ST_CRM_OFFER_PRODUCTS_LIST relationmgr=yes no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general

------- Paketid --------

@default group=packages_show
	@layout vbox_p_others type=hbox group=packages_show width=20%:80%

	@layout vbox_p_tree type=vbox group=packages_show parent=vbox_p_others
	@layout vbox_p_tbl type=vbox group=packages_show parent=vbox_p_others 

	@property pk_toolbar type=toolbar no_caption=1 store=no 

	@property pk_tree type=treeview no_caption=1 store=no parent=vbox_p_tree
	@caption Puu

	@property pk_list type=table store=no no_caption=1 parent=vbox_p_tbl
	@caption Pakkumised

@groupinfo packages_show caption=Paketid submit=no

@reltype PACKET value=1 clid=CL_SHOP_PACKET
@caption pakett

*/

class crm_offer_products_list extends class_base
{
	const AW_CLID = 910;

	function crm_offer_products_list()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_products_list",
			"clid" => CL_CRM_OFFER_PRODUCTS_LIST
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "pk_toolbar":
				$this->_pk_toolbar($arr);
				break;

			case "pk_tree":
				$this->_pk_tree($arr);
				break;

			case "pk_list":
				$this->_pk_list($arr);
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
			case "pk_list":
				$arr["obj_inst"]->set_meta("pk_data", $arr["request"]["od"]);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _pk_toolbar($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "add_to_offer",
			"action" => "add_pk_to_offer",
			"tooltip" => t("Lisa pakkumisse"),
		));

		$t->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"action" => "submit",
			"tooltip" => t("Salvesta"),
		));
	}

	function _pk_tree($arr)
	{
		// get warehouse
		$wh = $this->_get_wh($arr["obj_inst"]);
		if (!$wh)
		{
		die(t("no warhoos"));
		}
		$wh_i = $wh->instance();

		list($root, $pks) = $wh_i->get_packet_folder_list(array(
			"id" => $wh->id(),
		));
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML, 
				"persist_state" => true,
				"tree_id" => "offer_pk_t",
			),
			"root_item" => $root,
			"ot" => $pks,
			"var" => "tf"
		));
	}

	function _init_pk_list(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Orignaalhind"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price_in_offer",
			"caption" => t("Pakutav hind"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "remove_from_offer",
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _pk_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_pk_list($t);

		if (!$arr["request"]["tf"])
		{
			return;
		}

		$wh = $this->_get_wh($arr["obj_inst"]);
		$wh_i = $wh->instance();

		$pks = $wh_i->get_packet_list(array(
			"id" => $wh->id(),
			"parent" => $arr["request"]["tf"],
		));

		$od = $arr["obj_inst"]->meta("pk_data");

		foreach($pks as $pk)
		{
			$rfo = "";
			if ($arr["obj_inst"]->is_connected_to(array("to" => $pk->id(), "type" => "RELTYPE_PACKET")))
			{
				$rfo = html::href(array(
					"url" => $this->mk_my_orb("remove_from_offer", array(
						"id" => $arr["obj_inst"]->id(),
						"pk" => $pk->id(),
						"return_url" => get_ru()
					)),
					"caption" => t("Eemalda pakkumisest")
				));
			}
			$t->define_data(array(
				"oid" => $pk->id(),
				"name" => html::get_change_url($pk->id(), array("return_url" => get_ru()), $pk->name()),
				"price" => $pk->prop("price"),
				"price_in_offer" => html::textbox(array(
					"name" => "od[".$pk->id()."][price]",
					"value" => isset($od[$pk->id()]["price"]) ? $od[$pk->id()]["price"] : $pk->prop("price"),
					"size" => 5
				)),
				"remove_from_offer" => $rfo
			));
		}
	}

	function _get_wh($o)
	{
		// get offer by parent
		$offer = NULL;

		$path = $o->path();
		foreach(array_reverse($path) as $p_o)
		{
			if ($p_o->class_id() == CL_CRM_OFFER)
			{
				$offer = $p_o;
				break;
			}
		}
		if ($offer)
		{
			// get mgr from offer
			$mgr = $offer->get_first_obj_by_reltype("RELTYPE_OFFER_MGR");
			if ($mgr)
			{
				$wh = $mgr->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
				if ($wh)
				{
					return $wh;
				}
			}
		}

		return NULL;
	}

	/**

		@attrib name=add_pk_to_offer

	**/
	function add_pk_to_offer($arr)
	{
		$o = obj($arr["id"]);
		if (is_array($arr["sel"]))
		{
			foreach($arr["sel"] as $oid)
			{
				if (!$o->is_connected_to(array("to" => $oid, "type" => "RELTYPE_PACKET")))
				{
					$o->connect(array(
						"to" => $oid,
						"reltype" => "RELTYPE_PACKET"
					));
				}
			}
		}

		return $arr["post_ru"];
	}

	/**

		@attrib name=remove_from_offer

		@param id required type=int acl=view
		@param pk required type=int acl=view
		@param return_url required

	**/
	function remove_from_offer($arr)
	{
		$o = obj($arr["id"]);
		if ($o->is_connected_to(array("to" => $arr["pk"], "type" => "RELTYPE_PACKET")))
		{
			$o->disconnect(array(
				"from" => $arr["pk"]
			));
		}

		return $arr["return_url"];
	}

	function generate_html($o, $item)
	{
		classload('vcl/table');
		$t = new vcl_table(array("layout" => "generic"));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "left",
			"talign" => "left"
		));

		$ol = new object_list($item->connections_from(array("type" => "RELTYPE_PACKET")));
		$t->data_from_ol($ol);

		return $t->draw();
	}
}
?>

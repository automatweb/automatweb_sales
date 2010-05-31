<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer_mgr.aw,v 1.5 2008/01/31 13:54:13 kristo Exp $
// crm_offer_mgr.aw - Pakkumiste haldus 
/*

@classinfo syslog_type=ST_CRM_OFFER_MGR relationmgr=yes no_status=1 no_comment=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta 
@default method=serialize

@default group=offers

	@layout vbox_others type=hbox group=offers width=20%:80%

	@layout vbox_tree type=vbox group=offers parent=vbox_others
	@layout vbox_tbl type=vbox group=offers parent=vbox_others 

	@property offers_toolbar type=toolbar no_caption=1 store=no 

	@property offers_tree type=treeview no_caption=1 store=no parent=vbox_tree
	@caption Puu

	@property offers_list type=table store=no no_caption=1 parent=vbox_tbl
	@caption Pakkumised


@groupinfo offers caption="Pakkumised" submit=no


@reltype MGR_CO value=1 clid=CL_CRM_COMPANY
@caption haldaja

@reltype WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption ladu

@reltype TYPICAL_COMPONENT value=3 clid=CL_CRM_OFFER_CHAPTER,CL_CRM_OFFER_GOAL,CL_CRM_OFFER_PAYMENT_TERMS,CL_CRM_OFFER_PRODUCTS_LIST,CL_PROJECT
@caption t&uuml;piline komponent

*/

class crm_offer_mgr extends class_base
{
	const AW_CLID = 903;

	function crm_offer_mgr()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_mgr",
			"clid" => CL_CRM_OFFER_MGR
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "offers_list":
				$this->_offers_list($arr);
				break;

			case "offers_tree":
				$this->_offers_tree($arr);
				break;

			case "offers_toolbar":
				$this->_offers_toolbar($arr);
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

	function _init_offers_list_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Kellele"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "is_example",	
			"caption" => t("T&uuml;&uuml;ppakkumine?"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _offers_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_offers_list_t($t);

		$co = $this->_get_co($arr["obj_inst"]);
		if (!$co)
		{
			return;
		}

		$example_offers = $arr["obj_inst"]->meta("example_offers");

		foreach($co->connections_from(array("type" => "RELTYPE_OFFER")) as $c)
		{
			$o = $c->to();

			$to = "";
			if (is_oid($o->prop("orderer")) && $this->can("view", $o->prop("orderer")))
			{
				$or = obj($o->prop("orderer"));
				$to = html::get_change_url($o->prop("orderer"), array("return_url" => get_ru()), $or->name());
			}

			$t->define_data(array(
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($o->name())),
				"to" => $to,
				"when" => $o->prop("start1"),
				"oid" => $o->id(),
				"is_example" => (isset($example_offers[$o->id()]) ? 
					html::href(array(
						"url" => $this->mk_my_orb("remove_example", array(
							"id" => $arr["obj_inst"]->id(),
							"exo" => $o->id(),
							"return_url" => get_ru()
						)),
						"caption" => t("Eemalda t&uuml;&uuml;ppakkumistest")
					))	:
					html::href(array(
						"url" => $this->mk_my_orb("add_example", array(
							"id" => $arr["obj_inst"]->id(),
							"exo" => $o->id(),
							"return_url" => get_ru()
						)),
						"caption" => t("Tee t&uuml;&uuml;ppakkumiseks")
					))
				)
			));
		}
	}

	function _offers_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->add_item(0, array(
			"name" => t("K&otilde;ik pakkumised"),
			"id" => "all_offers",
			"url" => aw_url_change_var("tf", "all_offers")
		));
	}

	function _offers_toolbar($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$co = $this->_get_co($arr["obj_inst"]);
		if (!$co)
		{
			return;
		}		

		$t->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Lisa")
		));

		$clss = aw_ini_get("classes");

		$t->add_menu_item(array(
			"parent" => "new",
			"link" => $this->mk_my_orb("add_new_offer", array("id" => $arr["obj_inst"]->id(), "ru" => get_ru())),
			"text" => t("Lisa pakkumine")
		));

		$example_offers = safe_array($arr["obj_inst"]->meta("example_offers"));
		if (count($example_offers))
		{
			foreach($example_offers as $ofid)
			{
				if (!$this->can("view", $ofid))
				{
					continue;
				}
				$add = true;
				$ofo = obj($ofid);
				$t->add_menu_item(array(
					"parent" => "exo",
					"text" => $ofo->name(),
					"link" => $this->mk_my_orb("offer_from_example", array(
						"ofid" => $ofid,
						"return_url" => get_ru(),
						"id" => $arr["obj_inst"]->id()
					))
				));
				$add = true;
			}

			if ($add)
			{
				$t->add_sub_menu(array(
					"parent" => "new",
					"name" => "exo",
					"text" => t("T&uuml;ppakkumised"),
				));
			}
		}

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_offers",
			"tooltip" => t("Kustuta valitud tellimused"),
			"confirm" => t("Oled kindel et soovid valitud tellimused kustutada?")
		));
	}

	/**
		
		@attrib name=del_offers
	**/
	function del_offers($arr)
	{
		$sel = safe_array($arr["sel"]);
		if (count($sel))
		{
			$ol = new object_list(array("oid" => $sel));
			$ol->delete();
		}
		return $arr["post_ru"];
	}

	function _get_co($o)
	{
		return $o->get_first_obj_by_reltype("RELTYPE_MGR_CO");
	}

	/** add offer to example offers list for this workspace

		@attrib name=add_example

		@param id required type=int acl=view;edit
		@param exo required type=int acl=view
		@param return_url required

	**/
	function add_example($arr)
	{
		$o = obj($arr["id"]);

		$exo = safe_array($o->meta("example_offers"));
		$exo[$arr["exo"]] = $arr["exo"];

		$o->set_meta("example_offers", $exo);
		$o->save();

		return $arr["return_url"];
	}

	/** remove offer from example offers list for this workspace

		@attrib name=remove_example

		@param id required type=int acl=view;edit
		@param exo required type=int acl=view
		@param return_url required

	**/
	function remove_example($arr)
	{
		$o = obj($arr["id"]);

		$exo = safe_array($o->meta("example_offers"));
		unset($exo[$arr["exo"]]);

		$o->set_meta("example_offers", $exo);
		$o->save();

		return $arr["return_url"];
	}

	/**

		@attrib name=offer_from_example

		@param ofid required type=int acl=view
		@param id required type=int acl=view
		@param return_url required

	**/
	function offer_from_example($arr)
	{
		// clone offer object
		$o = obj($arr["ofid"]);
		$n = obj();
		$n->set_parent($arr["id"]);
		$n->set_class_id(CL_CRM_OFFER);
		$n->set_name($o->name());
		$n->set_comment($o->comment());
		foreach($o->properties() as $k => $v)
		{
			if ($n->is_property($k))
			{
				$n->set_prop($k, $v);
			}
		}
		$id = $n->save();

		$mgr = obj($arr["id"]);
		$co = $this->_get_co($mgr);
		$co->connect(array(
			"to" => $id,
			"reltype" => "RELTYPE_OFFER"
		));

		$n->connect(array(
			"to" => $mgr->id(),
			"reltype" => "RELTYPE_PREFORMER"
		));

		// go over connections and do the new one
		foreach($o->connections_from() as $c)
		{
			if (!$n->is_connected_to(array("to" => $c->prop("to"), "type" => $c->prop("reltype"))))
			{
				$n->connect(array(
					"to" => $c->prop("to"),
					"reltype" => $c->prop("reltype")
				));
			}
		}

		// copy subobjects
		$ot = new object_tree(array(
			"parent" => $o->id()
		));
		$this->_rec_copy_objects($o, $n, $ot);

		return html::get_change_url($id, array("return_url" => $arr["return_url"]));
	}

	function _rec_copy_objects($o, $n, $ot)
	{
		$level = $ot->level($o->id());
		foreach($level as $obj)
		{
			$new = $this->_copy_object($obj, $n->id());
			
			// recurse with subtree
			$subtree = $ot->subtree($obj->id());
			$this->_rec_copy_objects($obj, $new, $subtree);
		}
	}

	function _copy_object($old, $parent)
	{
		$o = obj();
		$o->set_class_id($old->class_id());
		$o->set_parent($parent);
		$o->set_comment($old->comment());

		// meta
		foreach($old->meta() as $k => $v)
		{
			$o->set_meta($k, $v);
		}

		// props
		foreach($old->properties() as $k => $v)
		{
			if ($o->is_property($k))
			{
				$o->set_prop($k, $v);
			}
		}
		$o->save();

		// conns
		foreach($old->connections_from() as $c)
		{
			if (!$o->is_connected_to(array("to" => $c->prop("to"), "type" => $c->prop("reltype"))))
			{
				$o->connect(array(
					"to" => $c->prop("to"),
					"reltype" => $c->prop("reltype")
				));
			}
		}

		return $o;
	}

	/** 

		@attrib name=add_new_offer

		@param id required type=int acl=view
		@param ru required
	**/
	function add_new_offer($arr)
	{
		$omgr = obj($arr["id"]);

		$o = obj();
		$o->set_class_id(CL_CRM_OFFER);
		$o->set_parent($omgr->id());

		$o->save();

		$o->connect(array(
			"to" => $omgr->id(),
			"reltype" => "RELTYPE_OFFER_MGR"
		));

		$mgr_o = $omgr->get_first_obj_by_reltype("RELTYPE_MGR_CO");

		$o->connect(array(
			"to" => $mgr_o->id(),
			"reltype" => "RELTYPE_PREFORMER"
		));

		$mgr_o->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_OFFER"
		));

		return html::get_change_url($o->id(), array("return_url" => $arr["ru"]));
	}

	function get_typical_components($o)
	{
		$ol = new object_list($o->connections_from(array("type" => "RELTYPE_TYPICAL_COMPONENT")));
		return $ol->names();
	}
}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_basket.aw,v 1.20 2009/08/09 21:05:13 dragut Exp $
// object_basket.aw - Objektide korv 
/*

@classinfo syslog_type=ST_OBJECT_BASKET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

	@property basket_type type=chooser field=meta method=serialize multiple=1
	@caption Korvi t&uuml;&uuml;p

	@property export type=relpicker field=meta method=serialize reltype=RELTYPE_EXPORT store=connect
	@caption Ekspordiobjekt

	@property max_items type=textbox size=5 field=meta method=serialize
	@caption Maksimaalne kirjete arv

	@property perpage type=textbox size=5 field=meta method=serialize
	@caption Mitu objekti lehel

	@property object_basket type=relpicker store=connect reltype=RELTYPE_OBJECT_BASKET
	@caption Objektide korv

	@property object_shop_product_show type=checkbox field=meta method=serialize default=0 ch_value=1
	@caption Toote puhul kasuta tootekuva

	@property object_shop_product_template type=textbox field=meta method=serialize
	@caption Kasuta toote kuvamisek
	
@reltype EXPORT clid=CL_ICAL_EXPORT value=1
@caption iCal eksport

@reltype OBJECT_BASKET clid=CL_OBJECT_BASKET value=2
@caption Objektide korv
*/

define("OBJ_BASKET_SESSION", 1);
define("OBJ_BASKET_USER", 2);
class object_basket extends class_base
{
	function object_basket()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_basket",
			"clid" => CL_OBJECT_BASKET
		));

		$this->basket_types = array(
			OBJ_BASKET_SESSION => t("Sessioonip&otilde;hine"),
			OBJ_BASKET_USER => t("Kasutajap&otilde;hine")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "object_shop_product_template":
				$prop["value"] = !strlen($prop["value"])?"prod_single.tpl":$prop["value"];
				break;
		};
		return $retval;
	}

	function _get_export($arr)
	{
		$bt = $arr["obj_inst"]->prop("basket_type");
		if($bt[OBJ_BASKET_SESSION])
			return PROP_IGNORE;
	}

	function _get_basket_type($arr)
	{
		$arr["prop"]["options"] = $this->basket_types;
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		aw_global_set("no_cache", 1);
		$basket = obj($arr["id"]);
		$objs = $this->get_basket_content($basket);
		$this->read_template("show_basket.tpl");
		lc_site_load("show_basket", $this);
		// parse vars from sub
		$sub_ct = $this->get_template_string("LINE");
		preg_match_all("/\{VAR\:(.*)\}/imsU", $sub_ct, $mt, PREG_PATTERN_ORDER);

		$per_page = (int)$basket->prop("perpage") > 0 ? (int)$basket->prop("perpage") : 4;
		$cur_page_from = $_GET["bm_page"] * $per_page;
		$cur_page_to = ($_GET["bm_page"]+1) * $per_page;

		$ls = "";
		$counter = 0;
		$cal = 0;
		foreach($objs as $dat)
		{
			if ($counter >= $cur_page_from && $counter < $cur_page_to)
			{
				$v = array(
					"remove_single_url" => $this->mk_my_orb("remove_single", array("basket" => $basket->id(), "item" => $dat["oid"], "ru" => get_ru()))
				);
				$o = obj($dat["oid"]);
				if($o->class_id()==CL_CALENDAR_EVENT)
				{
					$cal = 1;
				}
				// if set in basket and shop_product object appears here, products show is used
				if($o->class_id() == CL_SHOP_PRODUCT && $basket->prop("object_shop_product_show"))
				{

                                        $inst = get_instance(CL_SHOP_PRODUCT);
					$args = array(
						"id" => $o->id(),
					);
					if(strlen($tpl = $basket->prop("object_shop_product_template")))
					{
						$args["template"] = $tpl;
					}

					$inst = get_instance(CL_SHOP_PRODUCT);
					$tmp = $inst->show_prod($args);
					$ls .= $tmp;
					continue;
				}
				foreach($mt[1] as $var_name)
				{
					list($clid, $prop) = explode(".", $var_name, 2);
					if (constant($clid) == $o->class_id() && strlen($prop))
					{
						if ($prop == "id")
						{
							$v[$var_name] = $o->id();
						}//seda kuramust ei saa ju kontrollida kuidagi kas objekti laienduses va meetod eksisteerib... mu meelest....
						elseif(substr_count($prop,"()"))// && method_exists($o,substr($prop, 0 ,-2)))
						{
							$func = substr($prop, 0 ,-2);
							$v[$var_name] = $o->$func();
						}
						else
						{
							$v[$var_name] = is_array($o->trans_get_val($prop)) ? reset($o->trans_get_val($prop)) : $o->trans_get_val($prop);
						}
					}
				}
				$this->vars_safe($v);
				$ls .= $this->parse("LINE");
			}
			$counter++;
		}
		$pgs = "";
		$num_pages = count($objs) / $per_page;
		for($i = 0; $i < $num_pages; $i++)
		{
			$this->vars_safe(array(
				"page_from" => ($i*$per_page)+1,
				"page_to" => min(($i+1)*$per_page, count($objs)),
				"page_link" => aw_url_change_var("bm_page", $i)
			));
			if ($_GET["bm_page"] == $i)
			{
				$pgs .= $this->parse("SEL_PAGE");
			}
			else
			{
				$pgs .= $this->parse("PAGE");
			}
		}
		if(count($objs))
		{
			$this->vars_safe(array(
				"LINE" => $ls,
			));
			$lines["HAS_LINES"] = $this->parse("HAS_LINES");
		}
		else
		{
			$lines["NO_LINES"] = $this->parse("NO_LINES");
		}
		$this->vars_safe($lines);
		$this->vars_safe(array(
			"PAGE" => $pgs,
			"SEL_PAGE" => "",
			"LINE" => $ls,
			"total_count" => count($objs),
			"remove_all_url" => $this->mk_my_orb("remove_all", array("basket" => $basket->id(),"ru" => get_ru())),
			"print_url" => aw_url_change_var("print", 1)
		));
		if($cal && $export = $basket->prop("export"))
		{
			$this->vars_safe(array(
				"ical_url" => $this->mk_my_orb("export", array("id" => $export, "basket" => $basket->id()), CL_ICAL_EXPORT),
			));
		}
		return $this->parse();
	}

	/** Returns the basket content
		@attrib api=1

		@param o required type=object
			The basket object with the configuration

	**/
	function get_basket_content($o, $all = false)
	{
		$rel = $o->connections_to(array(
			"from.class_id" => CL_OBJECT_BASKET,
			"type" => "RELTYPE_OBJECT_BASKET",
		));
		$o_old = null;
		if(count($rel))
		{
			$o_old = $o;
			$c = reset($rel);
			$o = $c->from();
		}
		$bt = $this->make_keys($o->prop("basket_type"));
		if ($bt[OBJ_BASKET_USER] && aw_global_get("uid") != "")
		{
			$rv = safe_array(aw_unserialize($this->get_cval("object_basket_".$o->id()."_".aw_global_get("uid"))));
		}
		elseif ($bt[OBJ_BASKET_SESSION])
		{
			$rv = safe_array($_SESSION["object_basket"][$o->id()]["content"]);
		}
		$o = $o_old?$o_old:$o; // if data comes from another basket, count still has to come from current
		if ($o->max_items && count($rv) > $o->max_items && !$all)
		{
			$rv = array_slice($rv, -$o->max_items);
		}
		return $rv;
	}

	/**
		@attrib name=add_object nologin="1"
		@param oid required type=int acl=view
		@param basket required type=int acl=view
		@param ru required 
	**/
	function add_object($arr)
	{
		$o = obj($arr["basket"]);
		$ct = $this->get_basket_content($o, true);
		$ct[$arr["oid"]]["oid"] = $arr["oid"];
		$bt = $this->make_keys($o->prop("basket_type"));
		if ($bt[OBJ_BASKET_USER] && aw_global_get("uid") != "")
		{
			$tz = aw_serialize($ct);
			$this->quote(&$tz);
			$this->set_cval(
				"object_basket_".$o->id()."_".aw_global_get("uid"),
				$tz
			);
		}
		else
		{
			$_SESSION["object_basket"][$o->id()]["content"] = $ct;
		}
		return htmlspecialchars($arr["ru"], ENT_QUOTES);
	}

	/**
		@attrib name=remove_all nologin="1"
		@param basket required type=int acl=view
		@param ru required 
	**/
	function remove_all($arr)
	{
		$o = obj($arr["basket"]);
		$ct = $this->get_basket_content($o);
		$ct = array();
		$bt = $this->make_keys($o->prop("basket_type"));
		if ($bt[OBJ_BASKET_USER] && aw_global_get("uid") != "")
		{
			$tz = aw_serialize($ct);
			$this->quote(&$tz);
			$this->set_cval(
				"object_basket_".$o->id()."_".aw_global_get("uid"),
				$tz
			);
		}
		else
		{
			$_SESSION["object_basket"][$o->id()]["content"] = $ct;
		}
		return $arr["ru"];
	}

	/**
		@attrib name=remove_single nologin="1"
		@param basket required type=int acl=view
		@param item required type=int acl=view
		@param ru required 
	**/
	function remove_single($arr)
	{
		$o = obj($arr["basket"]);
		$ct = $this->get_basket_content($o);
		unset($ct[$arr["item"]]);
		$bt = $this->make_keys($o->prop("basket_type"));
		if ($bt[OBJ_BASKET_USER] && aw_global_get("uid") != "")
		{
			$tz = aw_serialize($ct);
			$this->quote(&$tz);
			$this->set_cval(
				"object_basket_".$o->id()."_".aw_global_get("uid"),
				$tz
			);
		}
		else
		{
			$_SESSION["object_basket"][$o->id()]["content"] = $ct;
		}
		return $arr["ru"];
	}
}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_packet.aw,v 1.32 2009/09/07 14:56:01 markop Exp $
// shop_packet.aw - Pakett 
/*

@classinfo syslog_type=ST_SHOP_PACKET relationmgr=yes no_status=1  maintainer=kristo
@tableinfo aw_shop_packets index=aw_oid master_table=objects master_index=brother_of

@default table=objects
@default group=general

@property description type=textarea cols=40 rows=5 table=aw_shop_packets 
@caption Kirjeldus
@comment Toote kirjeldus

@property item_count type=hidden table=aw_shop_packets field=aw_count
@caption Mitu laos

@property separate_items type=checkbox ch_value=1 table=aw_shop_packets field=separate_items
@caption Kas tooted on eraldi

@property price type=textbox table=aw_shop_packets field=aw_price
@caption Hind

@property max_usage_in_time type=textbox size=5 table=objects field=meta method=serialize
@caption Maksimaalne toodete kasutamise arv

@property max_usage_in_time_type type=select  table=objects field=meta method=serialize
@caption Aja&uuml;hik


@groupinfo packet caption="Paketi sisu"

	@property packet_tb group=packet field=meta method=serialize type=toolbar no_caption=1
	@property packet group=packet field=meta method=serialize type=table no_caption=1

@groupinfo data caption="Toote info"

@property user1 type=textbox table=aw_shop_packets field=user1 group=data
@caption User-defined 1

@property user2 type=textbox table=aw_shop_packets field=user2 group=data
@caption User-defined 2

@property user3 type=textbox table=aw_shop_packets field=user3 group=data
@caption User-defined 3

@property user4 type=textbox table=aw_shop_packets field=user4 group=data
@caption User-defined 4

@property user5 type=textbox table=aw_shop_packets field=user5 group=data
@caption User-defined 5

@property userta1 type=textarea table=aw_shop_packets field=tauser1 group=data
@caption User-defined ta 1

@property userta2 type=textarea table=aw_shop_packets field=tauser2 group=data
@caption User-defined ta 2

@property userta3 type=textarea table=aw_shop_packets field=tauser3 group=data
@caption User-defined ta 3

@property userta4 type=textarea table=aw_shop_packets field=tauser4 group=data
@caption User-defined ta 4

@property userta5 type=textarea table=aw_shop_packets field=tauser5 group=data
@caption User-defined ta 5

@property uservar1 type=classificator table=aw_shop_packets field=varuser1 group=data
@caption User-defined var 1

@property uservar2 type=classificator table=aw_shop_packets field=varuser2 group=data
@caption User-defined var 2

@property uservar3 type=classificator table=aw_shop_packets field=varuser3 group=data
@caption User-defined var 3

@property uservar4 type=classificator table=aw_shop_packets field=varuser4 group=data
@caption User-defined var 4

@property uservar5 type=classificator table=aw_shop_packets field=varuser5 group=data
@caption User-defined var 5

@groupinfo img caption="Pildid"

@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h group=img table_fields=name,ord table_edit_fields=ord
@caption Pildid

@groupinfo acl caption=&Otilde;igused
@default group=acl
	
	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@groupinfo transl caption=T&otilde;lgi
@default group=transl
	
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption paketi toode

@reltype IMAGE value=2 clid=CL_IMAGE
@caption pilt

@reltype CATEGORY value=3 clid=CL_SHOP_PRODUCT_CATEGORY
@caption Kategooria

@reltype BRAND value=4 clid=CL_SHOP_BRAND
@caption Kaubam&auml;rk

*/

class shop_packet extends class_base
{
	function shop_packet()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_packet",
			"clid" => CL_SHOP_PACKET
		));

		$this->trans_props = array(
			"name","comment"
		);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "max_usage_in_time_type":
				$data["options"] = array(
					"day" => t("P&auml;ev"),
					"week" => t("N&auml;dal"),
					"mon" => t("Kuu")
				);
				break;

			case "packet":
				$this->do_packet_table($arr);
				break;

			case "packet_tb":
				$this->_packet_tb($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "packet":
				$this->save_packet_table($arr);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}	

	function _init_packet_table(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));

		$t->define_field(array(
			"name" => "count",
			"caption" => t("Mitu paketis"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "packaging",
			"caption" => t("Pakend"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupp"),
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function do_packet_table(&$arr)
	{
		$pd = $arr["obj_inst"]->meta("packet_content");
		$pg = $arr["obj_inst"]->meta("packet_groups");
		$pk = $arr["obj_inst"]->meta("packet_def_pkgs");

		$this->_init_packet_table($arr["prop"]["vcl_inst"]);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			if(isset($pd[$c->prop("to")]))
			{
				$value = $pd[$c->prop("to")];
			} 
			else
			{
				$value = 1;
			}
			$arr["prop"]["vcl_inst"]->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"count" => html::textbox(array(
					"name" => "pd[".$c->prop("to")."]",
					"value" => $value,
					"size" => 5
				)),
				"group" => html::textbox(array(
					"name" => "pg[".$c->prop("to")."]",
					"value" => $pg[$c->prop("to")],
					"size" => 5
				)),
				"oid" => $c->prop("to"),
				"packaging" => html::select(array(
					"name" => "pk[".$c->prop("to")."]",
					"value" => $pk[$c->prop("to")],
					"options" => $this->get_package_picker_for_prod($c->to())
				))
			));
		}
	}

	function get_package_picker_for_prod($prod)
	{
		$ret = array("" => t("--vali--"));
		foreach($prod->connections_from(array("type" => "RELTYPE_PACKAGING")) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to.name");
		}
		return $ret;
	}

	function save_packet_table(&$arr)
	{
		$arr["obj_inst"]->set_meta("packet_content", $arr["request"]["pd"]);
		$arr["obj_inst"]->set_meta("packet_groups", $arr["request"]["pg"]);
		$arr["obj_inst"]->set_meta("packet_def_pkgs", $arr["request"]["pk"]);
	}

	function get_price($o)
	{
		return number_format($o->prop("price"), 2);
	}

	/** returns the html for the product

		@comment

			uses the $layout object to draw the product $prod
			from the layout reads the template and inserts correct vars
			optionally you can give the $quantity parameter
	**/
	function do_draw_product($arr)
	{
		extract($arr);

		if (!$oc_obj)
		{
			$oc_obj_id = NULL;
		}
		else
		{
			$oc_obj_id = $oc_obj->id();
		}

		$sct = get_instance(CL_SHOP_ORDER_CART);

		if(!$l_inst)
		{
			$l_inst = $layout->instance();
			$l_inst->read_template($layout->prop("template"));
		}
		$l_inst->vars(array(
			"it" => $it,
			"name" => $prod->name(),
			"price" => $prod->prop("price"),
			"id" => $prod->id(),
			"quantity" => (int)($arr["quantity"]),
			"view_link" => obj_link($prod->id().":".$oc_obj->id())
		));

		$l_inst->vars(array(
			"printlink" => aw_global_get("REQUEST_URI")."&print=1"
		));

		$h_s_p = "";

		$prods = "";
		$pisets = array();
		$first = true;
		$p_cnt = 1;
		$pager = array();
		$sct->get_cart($oc_obj);
		$clssf = get_instance(CL_CLASSIFICATOR);
		$i = get_instance(CL_IMAGE);
		foreach($prod->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			$w = $c->to();
			$w_i = $w->instance();
			$l_inst->vars(array(
				"prod_name" => $w->name(),
				"prod_price" => $w_i->get_price($w),
				"prod_link" => obj_link($prod->id().":".$oc_obj->id()),
				"prod_in_packet_link" => obj_link($prod->id().":".$oc_obj->id())."?prod=".$w->id(),
				"prod_num" => $p_cnt,
			));

			if ($GLOBALS["prod"] == $w->id() || (!$GLOBALS["prod"] && $first))
			{
				$itemd = $sct->get_item_in_cart(array("iid" => $w->id()));
				for ($i = 1; $i < 11; $i++)
				{
					if ($l_inst->template_has_var("sel_prod_uservar".$i."_edit"))
					{
						$html = html::select(array(
							"name" => "order_data[".$w->id()."][uservar".$i."]",
							"options" => $clssf->get_options_for(array(
								"clid" => $w->class_id(),
								"name" => "uservar".$i,
								"obj_inst" => $w,
							)),
							"selected" => $itemd["uservar".$i]
						));
						$l_inst->vars(array(
							"sel_prod_uservar".$i."_edit" => $html
						));
					}
				}

				for ($zi = 1; $iz < 6; $iz++)
				{
					if ($l_inst->template_has_var("sel_prod_uservarm".$iz."_edit"))
					{
						$tmp = $clssf->get_options_for(array(
							"clid" => $w->class_id(),
							"name" => "uservarm".$iz,
							"obj_inst" => $w,
						));
						$options = array();
						$awa = new aw_array($w->prop("uservarm".$iz));
						foreach($awa->get() as $v)
						{
							$options[$v] = $tmp[$v];
						}
						$html = html::select(array(
							"name" => "order_data[".$w->id()."][uservarm".$iz."]",
							"options" => $options,
							"selected" => $itemd["uservarm".$iz]
						));
						$l_inst->vars(array(
							"sel_prod_uservarm".$iz."_edit" => $html,
						));
					}
				}

				$l_inst->vars(array(
					"sel_prod_id" => $w->id(),
					"sel_prod_name" => $w->name(),
					"sel_prod_quantity" => $itemd["items"],
					"sel_prod_price" => $w_i->get_price($w),
					"sel_prod_userta2" => $w->prop("userta2")
				));
			}

			// insert images
			$l_inst->vars($pisets);
			$cnt = 1;
			$imgc = $w->connections_from(array("type" => "RELTYPE_IMAGE"));
			$i = get_instance(CL_IMAGE);
			usort($imgc, create_function('$a,$b', 'return ($a->prop("to.jrk") == $b->prop("to.jrk") ? 0 : ($a->prop("to.jrk") > $b->prop("to.jrk") ? 1 : -1));'));
			foreach($imgc as $c)
			{
				$u = $i->get_url_by_id($c->prop("to"));
				$l_inst->vars(array(
					"prod_image".$cnt => image::make_img_tag($u, $c->prop("to.name")),
					"prod_image".$cnt."_url" => $u,
				));

				if ($GLOBALS["prod"] == $w->id() || (!$GLOBALS["prod"] && $first))
				{
					$l_inst->vars(array(
						"sel_prod_image".$cnt => image::make_img_tag($u, $c->prop("to.name")),
						"sel_prod_image".$cnt."_url" => $u,
						"sel_prod_image".$cnt."_onclick" => image::get_on_click_js($c->prop("to")),
						"sel_prod_more_img_url" => aw_url_change_var("view", 2)
					));
					$tstr = "SEL_PROD_HAS_OVER_".($cnt-1)."_IMAGES";
					$l_inst->vars(array(
						$tstr => $l_inst->parse($tstr)
					));
				}

				if ($u != "")
				{
					$l_inst->vars(array(
						"PROD_HAS_IMAGE_".$cnt => $l_inst->parse("PROD_HAS_IMAGE_".$cnt)
					));
					$pisets["PROD_HAS_IMAGE_".$cnt] = "";
					if ($GLOBALS["prod"] == $w->id() || (!$GLOBALS["prod"] && $first))
					{
						$l_inst->vars(array(
							"SEL_PROD_HAS_IMAGE_".$cnt => $l_inst->parse("SEL_PROD_HAS_IMAGE_".$cnt)
						));
					}
				}
				else
				{
					$l_inst->vars(array(
						"PROD_HAS_IMAGE_".$cnt => ""
					));
					if ($GLOBALS["prod"] == $w->id() || (!$GLOBALS["prod"] && $first))
					{
						$l_inst->vars(array(
							"SEL_PROD_HAS_IMAGE_".$cnt => ""
						));
					}
				}
				$cnt++;
			}

			if ($GLOBALS["prod"] == $w->id() || (!$GLOBALS["prod"] && $first))
			{
				$h_s_p = $l_inst->parse("HAS_SEL_PROD");
				$pager[] = $l_inst->parse("PROD_PAGER_SEL");
			}
			else
			{
				$pager[] = $l_inst->parse("PROD_PAGER");
			}

			$prods .= $l_inst->parse("PRODUCT");
			$first = false;
			$p_cnt++;

		}

		$l_inst->vars(array(
			"PRODUCT" => $prods,
			"reforb" => $this->mk_reforb("submit_add_cart", array("section" => aw_global_get("section"), "oc" => $oc_obj_id, "return_url" => aw_global_get("REQUEST_URI")), "shop_order_cart"),
			"PROD_PAGER" => join($l_inst->parse("PROD_PAGER_SEP"), $pager),
			"PROD_PAGER_SEL" => "",
			"PROD_PAGER_SEP" => ""
		));

		// insert images
		$cnt = 1;
		$imgc = $prod->connections_from(array("type" => "RELTYPE_IMAGE"));
		usort($imgc, create_function('$a,$b', 'return ($a->prop("to.jrk") == $b->prop("to.jrk") ? 0 : ($a->prop("to.jrk") > $b->prop("to.jrk") ? 1 : -1));'));
		foreach($imgc as $c)
		{
			$u = $i->get_url_by_id($c->prop("to"));
			$onc = image::get_on_click_js($c->prop("to"));

			$l_inst->vars(array(
				"image".$cnt => image::make_img_tag_wl($c->prop("to")),
				"image".$cnt."_url" => $u,
				"image".$cnt."_onclick" => $onc
			));
			
			if ($onc != "")
			{
				$l_inst->vars(array(
					"IMAGE".$cnt."_HAS_BIG" => $l_inst->parse("IMAGE".$cnt."_HAS_BIG")
				));
			}

			$l_inst->vars(array(
				"HAS_IMAGE_".$cnt => $l_inst->parse("HAS_IMAGE_".$cnt)
			));
			$cnt++;
		}

		if ($h_s_p != "")
		{
			$l_inst->vars(array(
				"HAS_SEL_PROD" => $h_s_p
			));
		}
		else
		{
			$l_inst->vars(array(
				"NO_SEL_PROD" => $l_inst->parse("NO_SEL_PROD")
			));
		}

		return $l_inst->parse();
	}

	function get_contained_products($o)
	{
		return array($o);
	}

	function request_execute($obj)
	{
		list($prod_id, $oc_id) = explode(":", aw_global_get("section"));
		$prod = obj($prod_id);

		// get layout from soc.
		$soc_o = obj($oc_id);
		$soc_i = $soc_o->instance();

		$layout = $soc_i->get_long_layout_for_prod(array(
			"soc" => $soc_o,
			"prod" => $prod
		));

		return $this->do_draw_product(array(
			"layout" => $layout,
			"prod" => $prod,
			"oc_obj" => $soc_o
		));
	}

	function get_must_order_num($o)
	{
		return 0;
	}

	/** returns an array of products that are in the package
		@attrib api=1 params=pos

		@param o required type=object
		 the packet to get prods for

		 @returns 
			array of product id -> count in packet
	**/
	function get_products_for_package($o)
	{
		static $cache;
		if (isset($cache[$o->id()]))
		{
			return $cache[$o->id()];
		}
		$pd = $o->meta("packet_content");
		$ret = array();
		foreach($o->connections_from(array("type" => "RELTYPE_PRODUCT")) as $c)
		{
			$ret[$c->prop("to")] = max(1, $pd[$c->prop("to")]);
		}
		$cache[$o->id()] = $ret;
		return $ret;
	}

	/**
		@attrib api=1
	**/
	function get_group_list($o)
	{
		$pg = $o->meta("packet_groups");
		return array_unique(array_values(safe_array($pg)));
	}

	/**
		@attrib api=1
	**/
	function get_products_in_group($o, $grp)
	{
		$pg = $o->meta("packet_groups");
		$ret = array();
		foreach($pg as $prod => $p_grp)
		{
			if ($p_grp == $grp)
			{
				$ret[] = $prod;
			}
		}
		return $ret;
	}

	function _packet_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$ps = get_instance("vcl/popup_search");
		$tb->add_cdata(
			$ps->get_popup_search_link(array(
				"pn" => "set_prods",
				"multiple" => 1,
				"clid" => CL_SHOP_PRODUCT
			))
		);
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta tooted paketist"),
			"action" => "del_from_pkt"
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["set_prods"] = "0";
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save($arr)
	{
		$ps = get_instance("vcl/popup_search");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["set_prods"], "RELTYPE_PRODUCT");
	}

	/**
		@attrib name=del_from_pkt
	**/
	function del_from_pkt($arr)
	{
		$o = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $item)
		{
			$o->disconnect(array("from" => $item));
		}
		return $arr["post_ru"];
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

	function get_default_packagings_in_packet($package)
	{
		return safe_array($package->meta("packet_def_pkgs"));
	}

	// DB upgrade
	function do_db_upgrade($t, $f)
	{
		if (empty($f))
		{
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE '.$t.' (
				oid INT PRIMARY KEY NOT NULL)');
		}

		switch($f)
		{
			case "description":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
				break;
		}

	}

	private function get_template()
	{
		if($this->template)
		{
			return $this->template;
		}
		else
		{
			return "show.tpl";
		}
	}

	function show($arr)
	{
		error::raise_if(!$this->can("view" , $arr["oc"], array(
			"id" => ERR_NO_OC,
			"msg" => t("shop_packet::show(): no order center object selected!")
		)));

		$ob = new object($arr["id"]);

		$this->read_template($this->get_template());
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		lc_site_load("shop", &$this);
		//r2ndom miskite sama kategooria pakettide n2itamine
		if($this->is_template("MORE_PRODUCTS"))
		{
			$packets = $ob->get_same_cat_packets();
			$more = "";
			foreach($packets->arr() as $pack)
			{
				$this->vars($pack->get_data());
				$this->vars(array("view_url" => aw_url_change_var("product", $pack->id())));
				$more.= $this->parse("MORE_PRODUCTS");
			}
			$this->vars(array(
				"MORE_PRODUCTS" => $more,
			));
			$this->vars(array(
				"HAS_MORE_PRODUCTS" => $packets->count() ? $this->parse("HAS_MORE_PRODUCTS") : "",
			));

		}

		$data = $ob->get_data();

		if($this->is_template("TAGS"))
		{
			$tags = "";
			foreach($data["cat_comments"] as $key =>  $comment)
			{
				if($comment)
				{
					$this->vars(array(
						"comment" => $comment,
						"image_url" => $data["cat_images"][$key],
					));
					$tags.= $this->parse("TAGS");
				}		
			}
			$this->vars(array("TAGS" => $tags));
		}


		$cart_inst = get_instance(CL_SHOP_ORDER_CART);
 //		$data["submit_url"] = $this->mk_my_orb("submit_add_cart", array(
//			"oc" => $oc->id(),
//			"id" => $oc->prop("cart"),
//		),CL_SHOP_ORDER_CART,false,false,"&amp;");
//
//		if(!substr_count("orb.aw" ,$data["submit_url"] ))
//		{
//			$data["submit_url"] = str_replace(aw_ini_get("baseurl")."/" ,aw_ini_get("baseurl")."/orb.aw" , $data["submit_url"]);
//
//		}
		$oc = obj($arr["oc"]);
		$data["oc"] = $arr["oc"];
		$data["submit"] = html::submit(array(
			"value" => t("Lisa tooted korvi"),
		));
		$data["submit_url"] = $this->mk_my_orb("submit_add_cart", array(
			"oc" => $oc->id(),
			"id" => $oc->prop("cart"),
		),CL_SHOP_ORDER_CART,false,false,"&amp;");


		$prod_params = array();
		$data["COLORS"] = "";
		$szs = 0;//lihtsalt n2itab kas on yhelgi pakendil suurusi
		$clrs = 0;//n2itab kas on yhelgi tootel v2rve suurusi
		
		$purveyances_stuff = $this->get_purveyances($data["packages"]);
		$this->add_purveyances($data["packages"]);
		foreach($data["packages"] as $product => $packages)
		{
			$prod_purveyances_ids = array();
			$prod_sizes = array();
			$prod_prices = array();
			$prod_ids = array();
			$prod_purveyances = array();
			$prod_params[$product] = $product." : { ";

			$prod_params[$product].= " image_url : \"".$data["big_image_urls"][$product]."\",";


			$prod_params[$product].= "sizes : [";
			$this->vars(array(
				"color_key" => $product,
				"color" => $data["colors"][$product],
			));
			if($data["colors"][$product])
			{
				$clrs = 1;
			}
			//arr($data);	
			foreach($packages as $package)
			{
				if($data["prices"][$package])
				{

					$prod_sizes[] = "\"".$data["sizes"][$package]."\"";
					$prod_prices[] = $data["prices"][$package];
					$prod_ids[] = $package;
					$prod_purveyances[] = "'Tarneinfo puudub'";
 					$prod_purveyances_ids[] = $purveyances_stuff[$package] ? "\"".trim($purveyances_stuff[$package])."\"" : "\"\"";
				}
			}

			$prod_params[$product].=join(",",$prod_sizes);
			$prod_params[$product].="], prices : [";
			$prod_params[$product].=join(",",$prod_prices);
			$prod_params[$product].="], ids : [";
			$prod_params[$product].=join(",",$prod_ids);
			$prod_params[$product].="], purveyances : [";
			$prod_params[$product].=join(",",$prod_purveyances);
			$prod_params[$product].="], purveyances_id : [";
			$prod_params[$product].=join(",",$prod_purveyances_ids);
			$prod_params[$product].="]}";
			$data["COLORS"].= $this->parse("COLORS");

		}


		$first_pack = reset($data["packages"]);
		$n = 0;
		$data["SIZES"] = "";
		foreach($first_pack as $pack)
		{
			if($data["sizes"][$pack])
			{
				$szs = 1;
			}
			$this->vars(array(
				"size" => $data["sizes"][$pack],
				"size_key" => $n,
			));
			$data["SIZES"].= $this->parse("SIZES");
			$n++;
		}

		$data["price"] = number_format($data["prices"][reset($first_pack)] , 2);
		
		$data["product_params"] = "productParams = {\n";
		$data["product_params"].= join(",\n" , $prod_params);
//						111111111 : { sizes : [32,34,36], prices : [100,200,300]  },
//						232323231 : { sizes : [34,36], prices : [111,222,333]  }
//					}
//
		$data["product_params"].= "\n}";
		$data["section"] = aw_global_get("section");

		$this->vars($data);
		if($szs)
		{
			$this->vars(array("HAS_SIZE" => $this->parse("HAS_SIZE")));
		}
		if($clrs)
		{
			$this->vars(array("HAS_COLOR" => $this->parse("HAS_COLOR")));
		}
		return $this->parse();
	}

	private function get_purveyances($packagings)
	{
		$ret = array();
		foreach($packagings as $products => $packaging_array)
		{
			foreach($packaging_array as $key => $packaging)
			{
	
				$conns = connection::find(array(
					"to" => $packaging,
					"from.class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
					"type" => "RELTYPE_PACKAGING"
				));
				foreach($conns as $conn)
				{
					$o = obj($conn["from"]);
					$ret[$packaging] = $o->prop("code");
				}				

			}
		}

		return $ret;

	}

	private function add_purveyances($packagings)
	{
		$ret = array();
		$show = 0;
		foreach($packagings as $products => $packaging_array)
		{
			foreach($packaging_array as $key => $packaging)
			{
				$conns = connection::find(array(
					"to" => $packaging,
					"from.class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
					"type" => "RELTYPE_PACKAGING"
				));
				foreach($conns as $conn)
				{
					$o = obj($conn["from"]);
					$this->vars(array(
						"purveyance.comment" => trim($o->comment()),
						"purveyance.name" =>trim($o->name()),
						"purveyance.code" =>trim($o->prop("code")),
					));
					$show = 1;
				}
			}
		}

		if($show)
		{
			$this->vars(array("HAS_PURVEYANCES" => $this->parse("HAS_PURVEYANCES")));
		}
		return $ret;

	}

	/** 
		@attrib name=get_data nologin=1 is_public=1 all_args=1 params=pos api=1
		@param prop required type=string
		@param code required type=string
 	**/
	public function get_data($arr)
	{
		aw_disable_acl();
		$products = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"site_id" => array(),
			"lang_id" => array(),
			"code" => $arr["code"],
			"status" => array(1,2)
		));
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PACKET,
			"site_id" => array(),
			"lang_id" => array(),
			"CL_SHOP_PACKET.RELTYPE_PRODUCT" => $products->ids(),
		));
		$o = $ol->begin();

		if(is_object($o))
		{
			$fun = "get_".$arr["prop"];
			$stuff = $o->$fun(1);
			aw_restore_acl();
			return $stuff; 
		}aw_restore_acl();
		return null;
	}

}
?>

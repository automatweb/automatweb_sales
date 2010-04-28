<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/shop_product_table_layout.aw,v 1.25 2009/07/28 09:38:19 markop Exp $
// shop_product_table_layout.aw - Lao toodete tabeli kujundus 
/*

@classinfo syslog_type=ST_SHOP_PRODUCT_TABLE_LAYOUT relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property columns type=textbox size=5 field=meta method=serialize
@caption Tulpi

@property rows type=textbox size=5 field=meta method=serialize
@caption Ridu

@property per_page type=textbox size=5 field=meta method=serialize
@caption Tooteid lehel

@property template type=select field=meta method=serialize
@caption Template


*/

class shop_product_table_layout extends class_base
{
	const AW_CLID = 316;

	function shop_product_table_layout()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_product_table_layout",
			"clid" => CL_SHOP_PRODUCT_TABLE_LAYOUT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "template":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "applications/shop/shop_product_table_layout"
				));
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

	/** starts drawing a table

		@comment
	
			$t - product_table_layout srtorage object
			$oc - shop_order_center srtorage object
	**/
	function start_table($t, $oc)
	{
		$this->t = $t;
		$this->oc = $oc;
		$this->cnt = 0;
		$tpl = "table.tpl";
		if ($t->prop("template") != "")
		{
			$tpl = $t->prop("template");
		}
		$this->read_template($tpl);
		$this->colrow_or_rowcol();
		lc_site_load("shop_order_center", &$this);
		$soce = new aw_array(aw_global_get("soc_err"));
		$err = "";
		foreach($soce->get() as $prid => $errmsg)
		{
			if (!$errmsg["is_err"])
			{
				continue;
			}

			$this->vars_safe(array(
				"msg" => $errmsg["msg"],
				"prod_name" => $errmsg["prod_name"],
				"prod_id" => $errmsg["prod_id"],
				"must_order_num" => $errmsg["must_order_num"],
				"ordered_num" => $errmsg["ordered_num"]
			));
			$err .= $this->parse("ERROR");
		}

		aw_session_del("soc_err");

		$this->vars_safe(array(
			"ERROR" => $err
		));

		$this->r_template = "ROW";
		$this->r_cnt = 1;
		if ($this->is_template("ROW1"))
		{
			$this->r_template = "ROW1";
		}

		$this->per_page = $this->t->prop("columns") * $this->t->prop("rows");
	}

	function is_on_cur_page()
	{
		if ($this->is_template("PAGE"))
		{
			$from = $this->per_page * (int)$_GET["sptlp"];
			$to = $this->per_page * ((int)$_GET["sptlp"]+1);
			if (!($this->cnt >= $from && $this->cnt < $to))
			{
				return false;
			}
		}
		return true;
	}

	function add_product($p_html)
	{
		$this->product_html[$this->cnt++] = $p_html;
	}

	/** adds a product to the product table
	**/
	function add_product_html($p_html)
	{
		if ($this->is_template("PAGE"))
		{
			$from = $this->per_page * (int)$_GET["sptlp"];
			$to = $this->per_page * ((int)$_GET["sptlp"]+1);

			if (!($this->product_html_cnt >= $from && $this->product_html_cnt < $to))
			{
				$this->product_html_cnt++;
				return;
			}
		}

		if(empty($this->row_inside_of_col))
		{
			if (($this->product_html_cnt % $this->t->prop("columns")) == 0)
			{
				$this->r_template = "ROW";
				if($this->is_template("ROW1"))
				{
					if(($this->r_cnt % 2) == 0)
					{
						$this->r_template = "ROW2";
					}
					else
					{
						$this->r_template = "ROW1";
					}
				}
				
				$this->vars_safe(array(
					"COL" => $this->t_str
				));
				$this->ft_str .= $this->parse($this->r_template);
				$this->t_str = "";
				$this->r_cnt++;
			}
		}
		else
		{
			if ($this->product_html_cnt > 0 && ($this->product_html_cnt % ceil($this->cnt / $this->t->prop("columns"))) == 0)
			{
				$this->r_template = "ROW";
				if($this->is_template("ROW1"))
				{
					if(($this->r_cnt % 2) == 0)
					{
						$this->r_template = "ROW2";
					}
					else
					{
						$this->r_template = "ROW1";
					}
				}
				
				$this->vars_safe(array(
					$this->r_template => $this->t_str
				));
				$this->ft_str .= $this->parse("COL");
				$this->t_str = "";
				$this->r_cnt++;
			}
		}
		
	
		$this->vars_safe(array(
			"product" => $p_html
		));
		if(empty($this->row_inside_of_col))
		{
			$this->t_str .= $this->parse($this->r_template.".COL");
		}
		else
		{
			$this->t_str .= $this->parse("COL.".$this->r_template);
		}
		$this->product_html_cnt++;
	}

	/** returns the html for the product table
	**/
	function finish_table()
	{
		$this->product_html_cnt = 0;
		foreach($this->product_html as $product_html)
		{
			$this->add_product_html($product_html);
		}

		if(empty($this->row_inside_of_col))
		{
			$this->vars_safe(array(
				"COL" => $this->t_str
			));
		}
		else
		{
			$this->r_template = "ROW";
			if($this->is_template("ROW1"))
			{
				if(($this->r_cnt % 2) == 0)
				{
					$this->r_template = "ROW2";
				}
				else
				{
					$this->r_template = "ROW1";
				}
			}
			$this->vars_safe(array(
				$this->r_template => $this->t_str
			));
		}

		$hi = "";
		if ($this->cnt > 0)
		{
			$hi = $this->parse("HAS_ITEMS");
		}

		$so = obj(aw_global_get("section"));
		$prod_tree_clids = count($prod_tree_clids = $this->oc->prop("warehouse.conf.prod_tree_clids")) > 0 ? $prod_tree_clids : array(CL_MENU);
		if ($so->is_a(CL_SHOP_PRODUCT) || $so->is_a(CL_SHOP_PRODUCT_PACKAGING) || !in_array($so->class_id(), $prod_tree_clids))
		{
			$so = obj($so->parent());
		}
		$cart_inst = get_instance(CL_SHOP_ORDER_CART);
		$cart_val = $cart_inst->get_cart_value();
		$sect = aw_global_get("section");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$sect = aw_global_get("ct_lang_lc")."/".$sect;
		}
		if(!empty($this->web_discount))
		{
			$wd = $this->web_discount;
		}
		elseif($this->oc->prop("web_discount"))
		{
			$wd = $this->oc->prop("web_discount");
		}
		else
		{
			$wd = 0;
		}

		if ($this->is_template("PROD_FILTER"))
		{
			$this->_insert_prod_filter($this->oc, $so);
		}

		if(empty($this->row_inside_of_col))
		{
			$this->ft_str .= $this->parse($this->r_template);
			$this->vars_safe(array(
				"ROW" => $this->ft_str,
				"ROW1" => $this->ft_str,
				"ROW2" => "",
			));
		}
		else
		{
			$this->ft_str .= $this->parse("COL");
			$this->vars_safe(array(
				"COL" => $this->ft_str,
			));
		}
		$this->vars_safe(array(
			"reforb" => $this->mk_reforb("submit_add_cart", array("section" => $sect, "oc" => $this->oc->id(), "return_url" => aw_global_get("REQUEST_URI")), "shop_order_cart"),
			"HAS_ITEMS" => $hi,
			"sel_menu_text" => $so->name(),
			"web_discount" => $wd,
			"cart_total" => $cart_val,
			"cart_discount_sum" => $cart_val*($this->oc->prop("web_discount")/100),
			"cart_value_w_disc" => $cart_val - ($cart_val*($this->oc->prop("web_discount")/100))
		));

		if ($this->cnt)
		{
			$this->vars_safe(array(
				"HAS_PRODS" => $this->parse("HAS_PRODS")
			));
		}

		$this->draw_pageselector();
		if ($this->is_template("FILTER_CONTENT"))
		{
			$fc = $this->parse("FILTER_CONTENT");
			if ($_GET["is_ajax"] == 1)
			{
				return $fc;
			}
			$this->vars(array(
				"FILTER_CONTENT" => $fc
			));
		}

		return $this->parse();
	}

	function draw_pageselector()
	{
		if (!$this->t->prop("columns") || !$this->t->prop("rows"))
		{
			return;
		}
		$cur_page = $_GET["sptlp"];
		$num_pages = $this->cnt / $this->per_page;

		$pgs = array();
		for($i = 0; $i < $num_pages;  $i++)
		{
			$this->vars(array(
				"page_link" => aw_url_change_var("is_ajax", null, aw_url_change_var("sptlp", $i)),
				"page_number" => $i+1
			));
			if ($cur_page == $i)
			{
				$pgs[] = $this->parse("PAGE_SEL");
			}
			else
			{
				$pgs[] = $this->parse("PAGE");
			}

			if ($cur_page > 0 && ($cur_page-1) == $i)
			{
				$this->vars(array(
					"PREV_PAGE" => $this->parse("PREV_PAGE")
				));
			}

			if ($cur_page < ($num_pages-1) && ($cur_page+1) == $i)
			{
				$this->vars(array(
					"NEXT_PAGE" => $this->parse("NEXT_PAGE")
				));
			}
		}

		$this->vars(array(
			"PAGE" => join(" ".trim($this->parse("PAGE_SEP"))." ", $pgs),
			"PAGE_SEL" => "",
		));

		if ($num_pages > 1)
		{
			$this->vars(array(
				"HAS_PAGES" => $this->parse("HAS_PAGES"),
				"HAS_PAGES2" => $this->parse("HAS_PAGES2")
			));
		}
	}

	private function _insert_prod_filter($oc, $so)
	{
		$pf = "";
		$active_filter_set = $oc->filter_get_active_by_folder($so->id());
		if (!$this->can("view", $active_filter_set))
		{
			return;
		}
		$active_filter_obj = obj($active_filter_set);
		$fh2 = "";
		$user_captions = $active_filter_obj->filter_get_user_captions();

		foreach($oc->filter_get_fields() as $field_name => $field_caption)
		{
			$vals = $active_filter_obj->filter_get_selected_values($field_name);
			if (count($vals) < 1)
			{
				continue;
			}

			$this->vars(array(
				"filter_caption" => isset($user_captions[$field_name]) ? $user_captions[$field_name] : $field_caption,
				"filter_name" => $field_name
			));
			$fh .= $this->parse("PROD_FILTER_HEADER");
			$fh2 .= $this->parse("PROD_FILTER_HEADER2");

			$v = "";
			foreach($vals as $val => $val_caption)
			{
				$this->vars(array(
					"filter_value" => $val,
					"filter_label" => $val_caption,
					"checked" => checked($_GET["f"][$field_name][$val])
				));
				$v .= $this->parse("PROD_FILTER_VALUE");
			}
			$this->vars(array(
				"PROD_FILTER_VALUE" => $v,
			));
			$pf .= $this->parse("PROD_FILTER");
		}
		$this->vars(array(
			"PROD_FILTER" => $pf,
			"PROD_FILTER_HEADER" => $fh,
			"PROD_FILTER_HEADER2" => $fh2,
		));
	}

	protected function colrow_or_rowcol()
	{
		$this->row_inside_of_col = isset($this->v2_name_map["ROW"]) && substr($this->v2_name_map["ROW"], -7) === "COL.ROW";
	}
}
?>

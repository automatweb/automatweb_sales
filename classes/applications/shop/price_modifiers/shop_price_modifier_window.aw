<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/shop/price_modifiers/shop_price_modifier_window.aw,v 1.6 2007/12/06 14:34:11 kristo Exp $
// shop_price_modifier_window.aw - Akna hinnakujundus 
/*

@classinfo syslog_type=ST_SHOP_PRICE_MODIFIER_WINDOW relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@default group=parts_closed

	@property closed_part_table type=table no_caption=1
	@caption Kinnise osa tabel

@default group=parts_open

	@property open_part_table type=table no_caption=1
	@caption Lahtise osa tabel
	
@default group=parts_other

	@property extra_charge_percentage type=textbox size=5
	@caption Juurdehindlus (%)

	@property open_part_add_price type=textbox size=5
	@caption Lahtise osa lisatasu kaldp&ouml;&ouml;rdavanemise korral 

	@property glass_sqm_add_price_for3layer type=table
	@caption Klaasi ruutmeetri hinnalisa 3-kordse paketi puhul 

	@property one_side_painted_sqm_price type=table
	@caption Ruutmeetri hind ühelt poolt värvitud aknale 

	@property two_side_painted_sqm_price type=table
	@caption Ruutmeetri hind m&otilde;lemalt poolt v&auml;rvitud aknale

	@layout pr_add_twopart_hbox type=hbox
	@caption Hinnamuutus kaheosalise akna puhul

		@property pr_add_twopart type=textbox size=5 parent=pr_add_twopart_hbox no_caption=1
		@caption Hinnamuutus kaheosalise akna puhul

		@property pr_add_twopart_fixed_price type=textbox size=5 parent=pr_add_twopart_hbox
		@caption Fikseeritud hind

		@property pr_add_twopart_sep_post_meter_price type=textbox size=5 parent=pr_add_twopart_hbox
		@caption Vahepostide meetrihind

	@layout pr_add_threepart_hbox type=hbox
	@caption Hinnamuutus kolmeosalise akna puhul

		@property pr_add_threepart type=textbox size=5 parent=pr_add_threepart_hbox no_caption=1
		@caption Hinnamuutus kolmeosalise akna puhul

		@property pr_add_threepart_fixed_price type=textbox size=5 parent=pr_add_threepart_hbox
		@caption Fikseeritud hind

		@property pr_add_threepart_sep_post_meter_price type=textbox size=5 parent=pr_add_threepart_hbox
		@caption Vahepostide meetrihind

	@layout pr_add_tpart_hbox type=hbox
	@caption Hinnamuutus T-kujulise akna puhul

		@property pr_add_tpart type=textbox size=5 parent=pr_add_tpart_hbox no_caption=1
		@caption Hinnamuutus T-kujulise akna puhul

		@property pr_add_tpart_fixed_price type=textbox size=5 parent=pr_add_tpart_hbox
		@caption Fikseeritud hind

		@property pr_add_tpart_sep_post_meter_price type=textbox size=5 parent=pr_add_tpart_hbox 
		@caption Vahepostide meetrihind

@default group=board

	@property board_price_table type=table
	@caption Aknalaua hinnatabel (1 meetri hind aknalaua paksuse kohta, akna laiusele lisatakse 6 cm) 

	@property board_lining_price_table type=table
	@caption Veepleki hinnatabel (meetrihind, täpselt akna laius). 

@default group=install

	@property install_price_per_sqm type=textbox size=5
	@caption Paigalduse hind (akna ruutmeetri kohta) 

	@property install_price_add_for_replace type=textbox size=5
	@caption Paigalduse lisatasu asendamise korral

	@property finish_price_per_sqm type=textbox size=5
	@caption Viimistluse hind (akna ruutmeetri kohta)

	@layout min_pr_l type=hbox width="20%:90%"
	
	@property min_area_for_install type=textbox size=5 parent=min_pr_l
	@caption Kui pindala on alla 

	@property min_area_for_install_price type=textbox size=5 parent=min_pr_l
	@caption siis paigalduse hind on

	@layout min_pr_l_p type=hbox width="20%:90%"
	
	@property min_area_for_finish type=textbox size=5 parent=min_pr_l_p
	@caption Kui pindala on alla 

	@property min_area_for_finish_price type=textbox size=5 parent=min_pr_l_p
	@caption siis viimistluse hind on

@default group=discount

	@property discount_tbl type=table
	@caption Allahindluse tabel

@default group=door

	@property door_full_glass_per_sqm type=textbox size=5
	@caption T&auml;isklaasist

	@property door_closed_shield type=textbox size=5
	@caption Kinnine kilp lisab X EEK

@groupinfo parts caption="Aken"
	@groupinfo parts_closed caption="Kinnise osa tabel" parent=parts
	@groupinfo parts_open caption="Lahtise osa tabel" parent=parts
	@groupinfo parts_other caption="Muud" parent=parts

@groupinfo board caption="Aknalaud"
@groupinfo install caption="Viimistlus ja paigaldus"
@groupinfo discount caption="Allahindlus"
@groupinfo door caption="R&otilde;duuks"
*/

class shop_price_modifier_window extends class_base
{
	const AW_CLID = 967;

	function shop_price_modifier_window()
	{
		$this->init(array(
			"tpldir" => "applications/shop/price_modifiers/shop_price_modifier_window",
			"clid" => CL_SHOP_PRICE_MODIFIER_WINDOW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "open_part_table":
			case "closed_part_table":
				$this->_win_entry_table($arr);
				break;

			case "board_price_table":
				$this->_board_price_table($arr);
				break;

			case "board_lining_price_table":
				$this->_board_lining_price_table($arr);
				break;

			case "discount_tbl":
				$this->_discount_table($arr);
				break;

			case "glass_sqm_add_price_for3layer":
			case "one_side_painted_sqm_price":
			case "two_side_painted_sqm_price":
				$this->_ue_t($arr);
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
			case "open_part_table":
			case "closed_part_table":
				$arr["obj_inst"]->set_meta($arr["prop"]["name"], $arr["request"]["e"]);
				break;

			case "board_price_table":
				$arr["obj_inst"]->set_meta($arr["prop"]["name"], $arr["request"]["board"]);
				break;

			case "board_lining_price_table":
				$dat = array();
				foreach(safe_array($arr["request"]["lining"]) as $entry)
				{
					if ($entry["price"] && $entry["range"] != "")
					{
						$dat[] = $entry;
					}
				}
				$arr["obj_inst"]->set_meta($arr["prop"]["name"], $dat);
				break;

			case "discount_tbl":
				$dat = array();
				foreach(safe_array($arr["request"]["lining"]) as $entry)
				{
					if (($entry["price"] != "" || $entry["price_wood"] != "")  && $entry["range"] != "" )
					{
						$dat[] = $entry;
					}
				}
				$arr["obj_inst"]->set_meta($arr["prop"]["name"], $dat);
				break;

			case "two_side_painted_sqm_price":
			case "one_side_painted_sqm_price":
			case "glass_sqm_add_price_for3layer":
				$dat = array();
				foreach(safe_array($arr["request"][$prop["name"]]) as $entry)
				{
					if (($entry["price"] != "")  && $entry["range"] != "" )
					{
						$dat[] = $entry;
					}
				}
				$arr["obj_inst"]->set_meta($arr["prop"]["name"], $dat);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_win_entry_t(&$t, $arr)
	{
		$t->define_field(array(
			"name" => "w",
			"caption" => t("K&otilde;rgus/Laius"),
			"align" => "center"
		));

		for($i = 100; $i <= 2500; $i+= 100)
		{
			$t->define_field(array(
				"name" => "f".$i,
				"caption" => $i,
				"align" => "center"
			));
		}
	}

	function _win_entry_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_win_entry_t($t, $arr);

		$val = safe_array($arr["obj_inst"]->meta($arr["prop"]["name"]));
		
		for($i = 100; $i <= 2500; $i+= 100)
		{
			$td = array("w" => $i);
			for($a = 100; $a <= 2500; $a+= 100)
			{
				$td["f".$a] = html::textbox(array(
					"name" => "e[$i][$a]",
					"value" => $val[$i][$a],
					"size" => 5
				));
			}

			$t->define_data($td);
		}
		$t->set_sortable(false);
	}

	function _init_board_price_table(&$t)
	{
		$t->define_field(array(
			"name" => "capt",
			"caption" => t("&nbsp;"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "pvc",
			"caption" => t("PVC"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ksl",
			"caption" => t("KSL"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "tp",
			"caption" => t("T&auml;ispuit"),
			"align" => "center"
		));
	}

	function _board_price_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_board_price_table($t);

		$val = safe_array($arr["obj_inst"]->meta($arr["prop"]["name"]));
		
		for($i = 150; $i <= 500; $i+= 50)
		{
			$t->define_data(array(
				"capt" => $i,
				"pvc" => html::textbox(array(
					"name" => "board[pvc][$i]",
					"value" => $val["pvc"][$i],
					"size" => 5
				)),
				"ksl" => html::textbox(array(
					"name" => "board[ksl][$i]",
					"value" => $val["ksl"][$i],
					"size" => 5
				)),
				"tp" => html::textbox(array(
					"name" => "board[tp][$i]",
					"value" => $val["tp"][$i],
					"size" => 5
				))
			));
		}
		$t->set_sortable(false);
	}

	function _init_board_lining_price_table(&$t)
	{
		$t->define_field(array(
			"name" => "capt",
			"caption" => t("Pikkuse vahemik"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center"
		));
	}

	function _board_lining_price_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_board_lining_price_table($t);

		$val = safe_array($arr["obj_inst"]->meta($arr["prop"]["name"]));
		$val[] = "";
				
		$num = 1;
		foreach($val as $entry)
		{
			$t->define_data(array(
				"capt" => html::textbox(array(
					"name" => "lining[$num][range]",
					"value" => $entry["range"],
					"size" => 5
				)),
				"price" => html::textbox(array(
					"name" => "lining[$num][price]",
					"value" => $entry["price"],
					"size" => 5
				))
			));
			$num++;
		}
		$t->set_sortable(false);
	}

	function _init_discount_table(&$t)
	{
		$t->define_field(array(
			"name" => "capt",
			"caption" => t("Aknaid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("Allahindluse % plastikaken"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price_wood",
			"caption" => t("Allahindluse % puitaken"),
			"align" => "center"
		));
	}

	function _discount_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_discount_table($t);

		$val = safe_array($arr["obj_inst"]->meta($arr["prop"]["name"]));
		$val[] = "";
				
		$num = 1;
		foreach($val as $entry)
		{
			$t->define_data(array(
				"capt" => html::textbox(array(
					"name" => "lining[$num][range]",
					"value" => $entry["range"],
					"size" => 5
				)),
				"price" => html::textbox(array(
					"name" => "lining[$num][price]",
					"value" => $entry["price"],
					"size" => 5
				)),
				"price_wood" => html::textbox(array(
					"name" => "lining[$num][price_wood]",
					"value" => $entry["price_wood"],
					"size" => 5
				))
			));
			$num++;
		}
		$t->set_sortable(false);
	}

	function _init_ue_t(&$t)
	{
		$t->define_field(array(
			"name" => "capt",
			"caption" => t("Ruutmeetreid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "price",
			"caption" => t("hind"),
			"align" => "center"
		));
	}

	function _ue_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$this->_init_ue_t($t);

		$pn = $arr["prop"]["name"];
		$val = safe_array($arr["obj_inst"]->meta($pn));
		$val[] = "";
				
		$num = 1;
		foreach($val as $entry)
		{
			$t->define_data(array(
				"capt" => html::textbox(array(
					"name" => $pn."[$num][range]",
					"value" => $entry["range"],
					"size" => 5
				)),
				"price" => html::textbox(array(
					"name" => $pn."[$num][price]",
					"value" => $entry["price"],
					"size" => 5
				)),
			));
			$num++;
		}
		$t->set_sortable(false);
	}
}
?>

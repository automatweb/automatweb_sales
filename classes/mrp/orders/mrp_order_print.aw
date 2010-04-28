<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_MRP_ORDER_PRINT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_print master_index=brother_of master_table=objects index=aw_oid
@extends mrp/orders/mrp_order

@default table=aw_mrp_order_print
@default group=general

	@property amount type=textbox size=5 field=aw_amount group=general,price
	@caption Kogus

	@property page_count type=textbox field=aw_page_count size=5
	@caption Lehtede arv

	@property colour_page_count type=textbox field=aw_colour_page_count size=5
	@caption V&auml;rviliste lehtede arv

	@property deadline type=date_select field=aw_deadline
	@caption T&auml;htaeg

	@property e_format type=relpicker reltype=RELTYPE_FORMAT field=aw_e_format automatic=1 no_edit=1
	@caption Tr&uuml;kise formaat

	@property e_covers type=checkbox ch_value=1 field=aw_e_covers 
	@caption Kaaned?

@default group=grp_case_workflow

	@property workflow_errors type=text store=no no_caption=1 

	@layout vsplitbox type=hbox width=25%:75% 
		@layout left_pane type=vbox parent=vsplitbox 
			@layout general_info type=vbox parent=left_pane area_caption=Projekti&nbsp;&uuml;ldandmed closeable=1 
			@layout resource_tree type=vbox parent=left_pane area_caption=Ressursid&nbsp;kategooriate&nbsp;kaupa closeable=1
				@property resource_tree type=text store=no no_caption=1 parent=resource_tree
		@property workflow_table type=table store=no no_caption=1 parent=vsplitbox


@default group=grp_case_materials

	@layout main_split type=hbox width=20%:80%

		@layout tree_border type=vbox closeable=1 area_caption=Materjalide&nbsp;kaustad parent=main_split

			@property materials_tree type=treeview no_caption=1 store=no parent=tree_border

		@property materials_table type=table store=no no_caption=1 parent=main_split

@default group=price

	@property material_price type=table store=no no_caption=1
	@caption Materjalide hind

	@property resource_price type=table store=no no_caption=1
	@caption Ressursside hind

	@property cover_price type=table store=no no_caption=1
	@caption Katete hind

	@property tot_price type=table store=no no_caption=1
	@caption Koguhind

	@property final_price type=textbox size=10 field=aw_final_price 
	@caption L&otilde;plik hind

@default group=preview_preview

	@property preview_tb type=toolbar store=no no_caption=1

	@property preview type=text store=no no_caption=1

@default group=preview_saved

	@property prev_tb type=toolbar store=no no_caption=1

	@property files_table type=table store=no no_caption=1

	@property pending_table type=table store=no no_caption=1

	@property sent_table type=table store=no no_caption=1

@default group=data

	@property e_main_paper type=relpicker reltype=RELTYPE_MAIN_PAPER field=aw_e_main_paper no_edit=1
	@caption Sisupaber

	@property e_cover_paper type=relpicker reltype=RELTYPE_COVER_PAPER field=aw_e_cover_paper no_edit=1
	@caption Kaanepaber

	@property e_main_colour type=select field=aw_e_main_colour
	@caption Sisu v&auml;rvilisus
	
	@property e_cover_colour type=select field=aw_e_cover_colour
	@caption Kaante v&auml;rvilisus

	@property e_post_processing type=textbox field=aw_e_post_processing
	@caption J&auml;relt&ouml;&ouml;tlus
	
	@property e_binding type=textbox field=aw_e_binding
	@caption K&ouml;ide v&otilde;i kinnitus
	
	@property e_measures type=textbox field=aw_e_measures
	@caption M&otilde;&otilde;dud
	
	@property e_materials type=textbox field=aw_e_materials
	@caption Materjalid
	

	@property e_orderer_co type=textbox field=aw_e_orderer_co
	@caption Tellija organisatsioon
	
	@property e_orderer_person type=textbox field=aw_e_orderer_person
	@caption Tellija isik
	
	@property e_orderer_email type=textbox field=aw_e_orderer_email
	@caption Tellija email
	
	@property e_orderer_phone type=textbox field=aw_e_orderer_phone
	@caption Tellija telefon
	

@groupinfo data caption="Sisestatavad andmed"



@groupinfo grp_case_workflow caption="Ressursid ja t&ouml;&ouml;voog"
@groupinfo grp_case_materials caption="Materjalid"
@groupinfo price caption="Hind"
@groupinfo preview caption="Eelvaade" 
	@groupinfo preview_preview caption="Eelvaade" submit=no parent=preview
	@groupinfo preview_saved caption="Salvestatud" parent=preview

@reltype SEL_COVER value=10 clid=CL_MRP_ORDER_COVER
@caption Kate

@reltype FORMAT value=11 clid=CL_MRP_ORDER_PRINT_FORMAT
@caption Formaat

@reltype MAIN_PAPER value=12 clid=CL_SHOP_PRODUCT
@caption Sisupaber

@reltype COVER_PAPER value=13 clid=CL_SHOP_PRODUCT
@caption Kaanepaber

@reltype SAVED_FILE value=15 clid=CL_FILE
@caption Salvestatud pakkumine

@reltype SENT_OFFER value=16 clid=CL_MRP_ORDER_SENT
@caption Saadetud pakkumine

@reltype REQUESTED_MATERIAL value=14 clid=CL_MRP_ORDER_REQUESTED_MATERIAL
@caption Soovitud materjal

*/

class mrp_order_print extends mrp_order
{
	const AW_CLID = 1520;

	function mrp_order_print()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_print",
			"clid" => CL_MRP_ORDER_PRINT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "e_main_colour":
			case "e_cover_colour":
				$arr["prop"]["options"] = $arr["obj_inst"]->get_colour_options();
				break;
		}

		return $retval;
	}

	function _get_e_main_paper($arr)
	{
		// load options from settings
		if ($this->can("view", $arr["request"]["web_interface_id"]))
		{
			$o = obj($arr["request"]["web_interface_id"]);
			if ($this->can("view", $o->main_paper_folder))
			{
				$ol = new object_list(array(
					"parent" => $o->main_paper_folder,
					"class_id" => CL_SHOP_PRODUCT,
					"lang_id" => array(),
					"site_id" => array()
				));
				$arr["prop"]["options"] = array("" => t("--vali--")) + $ol->names();
			}
		}
	}

	function _get_e_cover_paper($arr)
	{
		// load options from settings
		if ($this->can("view", $arr["request"]["web_interface_id"]))
		{
			$o = obj($arr["request"]["web_interface_id"]);
			if ($this->can("view", $o->cover_paper_folder))
			{
				$ol = new object_list(array(
					"parent" => $o->cover_paper_folder,
					"class_id" => CL_SHOP_PRODUCT,
					"lang_id" => array(),
					"site_id" => array()
				));
				$arr["prop"]["options"] = array("" => t("--vali--")) + $ol->names();
			}
		}
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

	function callback_mod_retval($arr)
	{
		$arr["args"]["tf"] = $arr["request"]["tf"];
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["tf"] = $_GET["tf"];
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_mrp_order_print(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_amount":
			case "aw_tiraazh":
			case "aw_deadline":
			case "aw_e_format":
			case "aw_e_num_pages":
			case "aw_e_covers":
			case "aw_e_main_paper":
			case "aw_e_cover_paper":
			case "aw_e_main_colour":
			case "aw_e_cover_colour":
			case "aw_page_count":
			case "aw_colour_page_count":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_format":
			case "aw_materials":
			case "aw_e_name":
			case "aw_e_post_processing":
			case "aw_e_binding":
			case "aw_e_measures":
			case "aw_e_materials":
			case "aw_e_orderer_co":
			case "aw_e_orderer_person":
			case "aw_e_orderer_email":
			case "aw_e_orderer_phone":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_final_price":
			case "aw_e_amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}

	private function _fwd_case($arr)
	{
		$case = $arr["obj_inst"]->get_case();	
		$tmp = array(
			"prop" => &$arr["prop"],
			"obj_inst" => $case,
			"request" => $arr["request"]
		);
		get_instance(CL_MRP_CASE)->get_property($tmp);
	}

	function _get_workflow_errors($arr)
	{
		$this->_fwd_case($arr);
	}

	function _get_resource_tree($arr)
	{
		$this->_fwd_case($arr);
	}

	function _get_workflow_table($arr)
	{
		$this->_fwd_case($arr);
		$t = $arr["prop"]["vcl_inst"];
		$t->remove_field("minstart");
		$t->remove_field("pre_buffer");
		$t->remove_field("post_buffer");
		$t->remove_field("prerequisites");
		$t->remove_field("comment");
		//$t->remove_field("length");
		$t->define_field(array(
			"name" => "sales_comment",
			"caption" => t("M&uuml;&uuml;gi kommentaar"),
			"tooltip" => t("M&uuml;&uuml;gi kommentaar"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"tooltip" => t("Hind"),
			"align" => "right",
			"numeric" => 1
		));

		foreach($t->get_data() as $id => $item)
		{
			$jo = obj($item["job_id"]);
			$item["sales_comment"] = html::textbox(array(
				"name" => "sc[".$jo->id()."]",
				"value" => $jo->sales_comment
			));
			$item["price"] = number_format($arr["obj_inst"]->get_price_for_job($jo), 2);
			$t->set_data($id, $item);
		}
	}

	private function _fwd_case_set($arr)
	{
		$case = $arr["obj_inst"]->get_case();	
		$tmp = array(
			"prop" => &$arr["prop"],
			"obj_inst" => $case,
			"request" => $arr["request"]
		);
		get_instance(CL_MRP_CASE)->set_property($tmp);
	}

	function _set_workflow_errors($arr)
	{
		$this->_fwd_case_set($arr);
	}

	function _set_resource_price($arr)
	{
		$arr["obj_inst"]->set_meta("sel_covers_job_res", $arr["request"]["sel_covers_job_res"]);
	}

	function _set_material_price($arr)
	{
		$arr["obj_inst"]->set_meta("sel_covers_mat", $arr["request"]["sel_covers_mat"]);
	}

	function _set_resource_tree($arr)
	{
		$this->_fwd_case_set($arr);
	}

	function _set_workflow_table($arr)
	{
/*		$case = $arr["obj_inst"]->get_case();	
		$tmp = array(
			"prop" => &$arr["prop"],
			"obj_inst" => $case,
			"request" => $arr["request"]
		);
		get_instance(CL_MRP_CASE)->set_property($tmp);*/

		foreach(safe_array($arr["request"]["sc"]) as $id => $comm)
		{
			$jo = obj($id);
			if ($jo->sales_comment != $comm || $jo->length != ($arr["request"]["mrp_workflow_job-".$id."-length"]*3600))
			{
				$jo->sales_comment = $comm; 
				$jo->set_prop("length", ($arr["request"]["mrp_workflow_job-".$id."-length"] * 3600));
				$jo->save();
			}
		}

		foreach(safe_array($arr["request"]["selection"]) as $id  => $id2)
		{
			if ($id && $id == $id2)
			{
				obj($id)->delete();
			}
		}
	}

	function callback_pre_save($arr)
	{
		if ($arr["request"]["group"] == "grp_case_workflow")
		{
			$case = $arr["obj_inst"]->get_case();	
			$arr["request"]["mrp_workspace"] = $arr["obj_inst"]->workspace()->mrp_workspace;
			$tmp = array(
				"prop" => &$arr["prop"],
				"obj_inst" => $case,
				"request" => $arr["request"]
			);
			get_instance(CL_MRP_CASE)->callback_pre_save($tmp);
		}
	}

	private function _init_materials_table($t)
	{
		$t->define_field(array(
			"name" => "use_for_covers",
			"caption" => t("Kasuta kaante jaoks"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "material",
			"caption" => t("Materjal"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("V&otilde;imalikud &uuml;hikud"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sales_price",
			"caption" => t("M&uuml;&uuml;gihind"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "purchase_price",
			"caption" => t("Ostuhind"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "pages_with_this",
			"caption" => t("Lehti selle materjaliga"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "calculated_amount",
			"caption" => t("Arvutatud vajalik kogus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "total_purchase_price",
			"caption" => t("Ostuhinna summa"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "total_sales_price",
			"caption" => t("M&uuml;&uuml;gihinna summa"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "amount",
			"caption" => t("L&otilde;plik kogus"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "fin_sales_price",
			"caption" => t("L&otilde;plik m&uuml;&uuml;gihind"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Resurss"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "resource_hours",
			"caption" => t("Ressursi tunde"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "resource_price",
			"caption" => t("Ressursi hind"),
			"align" => "center",
		));
	}

	function _get_materials_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_materials_table($t);

		$cur_list = get_instance(CL_CURRENCY)->get_list(RET_NAME);

		$used_materials = $arr["obj_inst"]->get_used_materials();

		$wh = $arr["obj_inst"]->get_applicable_warehouse()->conf()->prod_fld;
		if (!empty($arr["request"]["tf"]))
		{
			$wh = $arr["request"]["tf"];
		}

		// list all prods from folder and set amounts
		$ol = new object_list(array(
			"parent" => $wh,
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_SHOP_PRODUCT
		));
		$mj = get_instance(CL_MRP_JOB);
		foreach($ol->arr() as $material)
		{
			$amt = 0;
			$unit = 0;
			$mat_amt = $arr["obj_inst"]->page_count;
			$for_covers = 0;
			if (isset($used_materials[$material->id]))
			{
				$amt = $used_materials[$material->id]->amount;
				$mat_amt = $used_materials[$material->id]->pages_with_this;
				$unit = $used_materials[$material->id]->unit;
				$for_covers = $used_materials[$material->id]->for_covers;
			}
			if ($mat_amt < 1)
			{
				$mat_amt = $arr["obj_inst"]->page_count;
			}

			// calculate required paper amount
			$calc_amount = $arr["obj_inst"]->calculate_needed_paper_amount($material, $used_materials[$material->id]);

			$price = array();
			$price_total = array();
			$price_fin = array();
			foreach($cur_list as $cur_id => $cur_name)
			{
				$pr_tmp = ($material->price_get_by_currency(obj($cur_id)));
				$price[] = $pr_tmp." ".$cur_name;
				$price_total[] = number_format($pr_tmp * $calc_amount, 2)." ".$cur_name;
				$price_fin[] = number_format($pr_tmp * $amt, 2)." ".$cur_name;
			}
			$purchase_price = $material->purchase_price;
			$total_purchase_price = $material->purchase_price * $calc_amount;
				
			$res = $arr["obj_inst"]->get_default_resource_for_material($material);
			$resource_hours = "";
			$resource_price = "";
			if ($res)
			{
				$resource_hours = $res->get_hours_per_format_amount($arr["obj_inst"]->e_format(), $mat_amt * $arr["obj_inst"]->amount);

				$resource_price = obj($arr["obj_inst"]->prop("mrp_pricelist"))->get_price_for_resource_and_amount($res, $arr["obj_inst"]->amount);
				$resource_price += obj($arr["obj_inst"]->prop("mrp_pricelist"))->get_price_for_resource_and_time($res, $resource_hours);
				$resource_price = number_format($resource_price, 2);
			}

			$t->define_data(array(
				"use" => html::checkbox(array("name" => "use[".$material->id()."]", "value" => 1, "checked" => isset($used_materials[$material->id()]))),
				"use_for_covers" => html::checkbox(array("name" => "use_for_covers[".$material->id()."]", "value" => 1, "checked" => $for_covers)),
				"material" => html::obj_change_url($material),
				"calculated_amount" => sprintf(t("%s kg"), number_format($calc_amount, 2)),
				"amount" => html::textbox(array("name" => "mat[".$material->id."]", "size" => 5, "value" => $amt)),
				"pages_with_this" => html::textbox(array("name" => "mat_amts[".$material->id."]", "size" => 5, "value" => $mat_amt)),
				"sales_price" => join(" ", $price),
				"total_sales_price" => join(" ", $price_total),
				"fin_sales_price" => join(" ", $price_fin),
				"purchase_price" => number_format($purchase_price, 2),
				"total_purchase_price" => number_format($total_purchase_price, 2),
				"unit" => $mj->get_materials_unitselect($material, $unit, $material->id()),
				"resource" => $res ? html::obj_change_url($res) : "",
				"resource_hours" => $resource_hours,
				"resource_price" => $resource_price
			));
		}
		$t->set_caption(sprintf(t("Materjalid kaustas %s"), obj($wh)->name()));
	}

	function _set_materials_table($arr)
	{
		$used_materials = $arr["obj_inst"]->get_used_materials();

		$wh = $arr["obj_inst"]->get_applicable_warehouse()->conf()->prod_fld;
		if (!empty($arr["request"]["tf"]))
		{
			$wh = $arr["request"]["tf"];
		}

		// list all prods from folder and set amounts
		$ol = new object_list(array(
			"parent" => $wh,
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_SHOP_PRODUCT
		));
		$mj = get_instance(CL_MRP_JOB);
		foreach($ol->arr() as $material)
		{
			if ($arr["request"]["mat"][$material->id()] > 0)
			{
				if (isset($used_materials[$material->id()]))
				{
					$usm = $used_materials[$material->id()];
				}
				else
				{
					$usm = obj();	
					$usm->set_parent($arr["obj_inst"]->id());
					$usm->set_class_id(CL_MRP_ORDER_REQUESTED_MATERIAL);
					$usm->set_prop("material", $material->id());
					$usm->save();
					$arr["obj_inst"]->connect(array("to" => $usm->id(), "type" => "RELTYPE_REQUESTED_MATERIAL"));
				}

				$usm->set_prop("amount", $arr["request"]["mat"][$material->id()]);
				$usm->set_prop("pages_with_this", $arr["request"]["mat_amts"][$material->id()]);
				$usm->set_prop("for_covers", $arr["request"]["use_for_covers"][$material->id()]);

				$res = $arr["obj_inst"]->get_default_resource_for_material($material);
				if ($res)
				{
					$usm->set_prop("resource", $res->id());
					if (!$this->can("view", $usm->prop("connected_job")))
					{
						// need to create a job for this material
						$case = get_instance(CL_MRP_CASE);
						$joid = $case->add_job(array(
							"obj_inst" => $arr["obj_inst"]->get_case(),
							"mrp_new_job_resource" => $res->id()
						));
						$usm->set_prop("connected_job", $joid);
					}

					$job = obj($usm->prop("connected_job"));
					$resource_hours = $res->get_hours_per_format_amount($arr["obj_inst"]->e_format(), $usm->prop("pages_with_this") * $arr["obj_inst"]->amount);
					$job->set_prop("length", $resource_hours * 3600);
					$job->set_prop("sales_comment", sprintf(t("Automaatselt lisatud materjali %s kaudu"), $material->name()));
					$job->save();
				}
				else
				{
					if ($this->can("view", $usm->prop("connected_job")))
					{
						obj($usm->prop("connected_job"))->delete();
					}
					$usm->set_prop("resource", null);
					$usm->set_prop("connected_job", null);
				}
				$usm->save();
			}
			else
			if (isset($used_materials[$material->id()]))
			{
				$used_materials[$material->id()]->delete();
			}
			
		}
		return;
		$mj = get_instance(CL_MRP_JOB);

		// put all jobs in table and for each job list all possible materials and let the user pick some
		foreach($arr["obj_inst"]->get_job_list() as $job)
		{
			$job->save_materials(array(
				"obj_inst" => $job,
				"request" => $arr["request"]["jobs"][$job->id()]
			));
		}
	}

	function _get_material_price($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Materjalide hind vastavalt kogustele"));
		$amt = $arr["obj_inst"]->amount;
		$data = array();
		$fract = ($amt / 10);
		if ($amt > $fract)
		{
			$from = max(-3, (-floor($amt / $fract))+1);
			$to = abs($from);

			$t->define_field(array(
				"name" => "material",
				"caption" => t("Materjal"),
				"align" => "right",
				"width" => "20%"
			));

			$sums = array();
			for($i = $from; $i <= $to; $i++)
			{
				$v = $amt + ($i * $fract);
				$t->define_field(array(
					"name" => $v,
					"caption" => $v,
					"align" => "right",
					"width" => "10%"
				));
				$sums[$v] = array();
			}


				foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_REQUESTED_MATERIAL")) as $c)
				{
					$mo = obj($c->to()->material);
					$data = array("material" => $mo->name());
					$pr_by_v = array();
					for($i = $from; $i <= $to; $i++)
					{
						$v = $amt + ($i * $fract);
						$mp = $this->_get_mat_price_for_amt($arr["obj_inst"], $v, $mo, $c->to());
						foreach($mp as $mp_k => $mp_v)
						{
							$sums[$v][$mp_k] += $mp_v;
							$pr_by_v[$v] = $mp_v;
						}
						$tmp = join(" ", $mp);
						if ($v == $amt)
						{
							$tmp = html::strong($tmp);
						}
						$data[$v] = $tmp;
					}
					$t->define_data($data);

					$sel_covers_mat = $arr["obj_inst"]->meta("sel_covers_mat");

					// get all applicable covers for resource
					foreach($mo->get_all_covers_for_material() as $cover)
					{
						$data = array(
							"material" => "<i>".sprintf(t("Kate: %s:"), $cover->name)."</i>"." ".html::checkbox(array(
								"name" => "sel_covers_mat[".$mo->id()."][".$cover->id()."]",
								"value" => $cover->id(),
								"checked" => isset($sel_covers_mat[$mo->id()][$cover->id()])
							))
						);
						for($i = $from; $i <= $to; $i++)
						{
							$v = $amt + ($i * $fract);
							$tmp = $cover->get_price_for_order_and_amt_and_price($arr["obj_inst"], $v, $pr_by_v[$v]);
							if (isset($sel_covers_mat[$mo->id()][$cover->id()]))
							{
								foreach($sums[$v] as $_sum_k => $_sum_v)
								{
									$sums[$v][$_sum_k] += $tmp;
								}
							}
							if ($v == $amt)
							{
								$tmp = html::strong($tmp);
							}
							$data[$v] = $tmp;
						}
						$t->define_data($data);
					}
			}
			foreach($sums as $k => $v)
			{
				$sums[$k] = join(" ", $v);
			}
			$sums[$amt] = html::strong($sums[$amt]);
			$sums["material"] = html::strong("Kokku");
			$t->define_data($sums);
		}

		$t->set_sortable(false);
	}
	
	private function _get_mat_price_for_amt($o, $amt, $mo, $expense_row)
	{
		$mj = get_instance(CL_MRP_JOB);

		$cur_list = get_instance(CL_CURRENCY)->get_list(RET_NAME);

		$tot_mat_price = array(); 

		// calculate amount for the amount requested approximately
		$per_one = $expense_row->amount / $o->amount;
		$calc_amt = $per_one * $amt;

		foreach($cur_list as $cur_id => $cur_name)
		{
			$tot_mat_price[$cur_id] += ($mo->price_get_by_currency(obj($cur_id)) * $calc_amt);
		}
		return array(reset($tot_mat_price));
		

		// put all jobs in table and for each job list all possible materials and let the user pick some
		foreach($o->get_job_list() as $job)
		{
			$material_expenses = $job->get_material_expense_list();

			// for each job list all possible materials
			$materials = $job->get_resource()->get_possible_materials();
			foreach($materials as $material)
			{
				$amt = 0;
				$unit = 0;
				$price = array();
				$purchase_price = 0;
				$pr_by_amt = array();
				if (isset($material_expenses[$material->product]))
				{
					$amt = $material_expenses[$material->product]->amount;
					$unit = $material_expenses[$material->product]->unit;
			
					// get price for set amount/unit
					
					foreach($cur_list as $cur_id => $cur_name)
					{
						$tmp = ($material->product()->price_get_by_currency(obj($cur_id)) * $amt);
						$tot_mat_price[$cur_id] += $tmp;
						$pr_by_amt[$amt] = $tmp;
					}
				}
			}
		}
		return array(reset($tot_mat_price));
		return $tot_mat_price;
	}

	function _get_resource_price($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Resursside kasutuse hind vastavalt kogustele"));
		$amt = $arr["obj_inst"]->amount;
		$data = array();
		$fract = $amt / 10;
		if ($amt > $fract)
		{
			$t->define_field(array(
				"name" => "resource",
				"caption" => t("Resurss"),
				"align" => "right",
				"width" => "20%"
			));
			$from = max(-3, (-floor($amt / $fract))+1);
			$to = abs($from);
			$sums = array();
			for($i = $from; $i <= $to; $i++)
			{
				$v = $amt + ($i * $fract);
				$t->define_field(array(
					"name" => $v,
					"caption" => $v,
					"align" => "right",
					"width" => "10%"
				));
				$sums[$v] = 0;
			}

			
			foreach($arr["obj_inst"]->get_job_list() as $job)
			{
				$resource = $job->get_resource();
				$data = array("resource" => $resource->name);
				$pr_by_amt = array();
				for($i = $from; $i <= $to; $i++)
				{
					$v = $amt + ($i * $fract);
					$tmp = $this->_get_resource_price_for_amt_and_resource($arr["obj_inst"], $v, $resource, $job);
					$pr_by_amt[$v] = $tmp;
					$sums[$v] += $tmp;
					if ($v == $amt)
					{
						$tmp = html::strong($tmp);
					}
					$data[$v] = $tmp;
				}
				$t->define_data($data);

				$sel_covers_job_res = $arr["obj_inst"]->meta("sel_covers_job_res");

				// get all applicable covers for resource
				foreach($resource->get_all_covers_for_resource() as $cover)
				{
					$data = array(
						"resource" => "<i>".sprintf(t("Kate: %s:"), $cover->name)."</i>"." ".html::checkbox(array(
							"name" => "sel_covers_job_res[".$job->id()."][".$resource->id()."][".$cover->id()."]",
							"value" => $cover->id(),
							"checked" => isset($sel_covers_job_res[$job->id()][$resource->id()][$cover->id()])
						))
					);
					for($i = $from; $i <= $to; $i++)
					{
						$v = $amt + ($i * $fract);
						$tmp = $cover->get_price_for_order_and_amt_and_price($arr["obj_inst"], $v, $pr_by_amt[$v]);
						if (isset($sel_covers_job_res[$job->id()][$resource->id()][$cover->id()]))
						{
							$sums[$v] += $tmp;
						}
						if ($v == $amt)
						{
							$tmp = html::strong($tmp);
						}
						$data[$v] = $tmp;
					}
					$t->define_data($data);
				}
			}
			$sums[$amt] = html::strong($sums[$amt]);
			$sums["resource"] = html::strong("Kokku");
			$t->define_data($sums);
		}
		$t->set_sortable(false);
	}

	private function _get_resource_price_for_amt_and_resource($o, $v, $resource, $job)
	{
		// get pricelist and go over all resources in the job list and calc prices for those
		$pricelist = $o->mrp_pricelist();
		$pr = 0;
		$pr += $pricelist->get_price_for_resource_and_amount($resource, $v);
		if ($job->length > 0)
		{
			$pr += $pricelist->get_price_for_resource_and_time($resource, $job->length / 3600);
		}

		return $pr;
	}

	function _get_cover_price($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Koguhinnale kehtivad katted"));
		$amt = $arr["obj_inst"]->amount;
		$data = array();
		$fract = $amt / 10;
		if ($amt > $fract)
		{
			$from = max(-3, (-floor($amt / $fract))+1);
			$to = abs($from);
			$t->define_field(array(
				"name" => "cover",
				"caption" => t("Kate"),
				"align" => "right",
				"width" => "20%"
			));
			$sums = array();
			for($i = $from; $i <= $to; $i++)
			{
				$v = $amt + ($i * $fract);
				$t->define_field(array(
					"name" => $v,
					"caption" => $v,
					"align" => "right",
					"width" => "10%"
				));
				$sums[$v] = 0;
			}

			$sel_covers = $arr["obj_inst"]->get_selected_covers();

			foreach($arr["obj_inst"]->workspace()->get_all_covers() as $cover)
			{
				$data = array(
					"cover" => $cover->name()." ".html::checkbox(array(
						"name" => "sel_covers[".$cover->id()."]",
						"value" => 1,
						"checked" => isset($sel_covers[$cover->id()])
					))
				);
				for($i = $from; $i <= $to; $i++)
				{
					$v = $amt + ($i * $fract);
					$tmp = $this->_get_cover_price_for_amt($arr["obj_inst"], $v, $cover);
					if (isset($sel_covers[$cover->id()]))
					{
						$sums[$v] += $tmp;
					}
					if ($v == $amt)
					{
						$tmp = html::strong($tmp);
					}
					$data[$v] = $tmp;
				}
				$t->define_data($data);
			}

			$t->set_sortable(false);
			$sums[$amt] = html::strong($sums[$amt]);
			$sums["cover"] = html::strong("Kokku");
			$t->define_data($sums);
		}
	}

	private function _get_cover_price_for_amt($o, $v, $cover)
	{
		return $cover->get_price_for_order_and_amt($o, $v);
	}

	function _set_cover_price($arr)
	{
		$arr["obj_inst"]->set_selected_covers(array_keys($arr["request"]["sel_covers"]));
	}

	function _get_tot_price($arr)
	{	
		$t = $arr["prop"]["vcl_inst"];
		$t->set_caption(t("Kogu hind vastavalt kogustele"));
		$amt = $arr["obj_inst"]->amount;
		$data = array();
		$fract = $amt / 10;
		if ($amt > $fract)
		{
			$from = max(-3, (-floor($amt / $fract))+1);
			$to = abs($from);
			$t->define_field(array(
				"name" => "a",
				"caption" => "",
				"align" => "right",
				"width" => "20%"
			));

			for($i = $from; $i <= $to; $i++)
			{
				$v = $amt + ($i * $fract);
				$t->define_field(array(
					"name" => $v,
					"caption" => $v,
					"align" => "right",
					"width" => "10%"
				));
				$tmp = $this->_get_tot_price_for_amt($arr["obj_inst"], $v);
				if ($v == $amt)
				{
					$tmp = html::strong($tmp);
				}
				$data[$v] = $tmp;
			}
		}
		$t->define_data($data);
	}

	private function _get_tot_price_for_amt($o, $v)
	{
		return $o->get_total_price_for_amt($v, true);
	}

	function _get_preview($arr)
	{
		$arr["prop"]["value"] = $this->generate_preview($arr["obj_inst"]);
//		die($arr["prop"]["value"]);
	}

	public function generate_preview($o)
	{
		$this->read_template("preview.tpl");

		// logo and other data from seller/buyer
		$this->_preview_insert_co_data($o->workspace()->owner_co(), "seller");
		if ($this->can("view", $o->customer))
		{
			$this->_preview_insert_co_data($o->customer(), "orderer");
			$this->vars(array(
				"orderer_address" => $o->customer()->get_address_string(),
				"orderer_phone" => $o->customer()->prop("fake_phone"),
				"orderer_fax" => $o->customer()->prop("fake_fax"),
				"orderer_contact" => $o->get_contact_name(),
			));
		}
		else
		{
			$this->vars(array(
				"orderer_name" => $o->e_orderer_co,
				"orderer_contact" => $o->e_orderer_person,
				"orderer_phone" => $o->e_orderer_phone,
			));
		}

		$this->vars($o->properties());
		$colo = $o->get_colour_options();
		$this->vars(array(
			"price" => $o->get_total_price(),
			"name" => $o->name(),
			"e_format" => $o->e_format()->name(),
			"e_covers" => $o->e_covers ? t("Jah") : t("Ei"),
			"e_cover_colour" => $colo[$o->e_cover_colour],
			"e_main_colour" => $colo[$o->e_main_colour],
			"e_cover_paper" => $o->e_cover_paper()->name(),
			"e_main_paper" => $o->e_main_paper()->name(),
			"amount" => $o->amount,
			"page_count" => $o->page_count,
			"colour_page_count" => $o->colour_page_count,
			"deadline" => date("d.m.Y", $o->deadline),
		));

		$s = "";
		foreach($o->get_used_materials() as $mat_request)
		{
			$this->vars(array(
				"mat_name" => $mat_request->material()->name(),
				"mat_amt" => number_format($mat_request->amount, 2),
				"mat_price" => $mat_request->get_material_price()
			));
			$s .= $this->parse("MATERIAL_LINE");
		}
		$this->vars(array(
			"MATERIAL_LINE" => $s
		));

		$s = "";
		foreach($o->get_job_list() as $job)
		{
			$this->vars(array(
				"resource_name" => $job->resource()->name(),
				"resource_hrs" => number_format($job->length / 3600, 1),
				"resource_price" => number_format($o->get_price_for_job($job), 2)
			));
			$s .= $this->parse("RESOURCE_LINE");
		}
		$this->vars(array(
			"RESOURCE_LINE" => $s
		));

		return $this->parse();
	}

	private function _preview_insert_co_data($o, $prefix)
	{
		$d = array();
		foreach($o->properties() as $k => $v)
		{
			$d[$prefix."_".$k] = $v;
		}
		if ($this->can("view", $o->logo))
		{
			$d[$prefix."_logo"] = html::img(array(
				"url" => obj_link($o->logo)
			));
		}
		$this->vars($d);
	}

	public function _get_preview_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
	}

	public function _set_preview($arr)
	{
		$conv = get_instance("core/converters/html2pdf");
		if (!$conv->can_convert())
		{
			die("no pdf converter available!");
		}
		$id = file::create_file_from_string(array(
			"parent" => $arr["obj_inst"]->id(),
			"content" => $conv->convert(array(
				"source" => $this->generate_preview($arr["obj_inst"])
			)),
			"name" => sprintf(t("Pakkumine %s tellimusele %s (%s).pdf"),
				$arr["obj_inst"]->get_customer_name(),
				$arr["obj_inst"]->name(),
				date("d.m.Y")
			),
			"type" => "application/pdf"
		));

		$arr["obj_inst"]->connect(array(
			"to" => $id,
			"type" => "RELTYPE_SAVED_FILE"
		));
	}

	private function _init_files_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Fail"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sent",
			"caption" => t("Saadetud"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "send",
			"caption" => t("Saada"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	public function _get_files_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_files_table($t);

		foreach($arr["obj_inst"]->get_saved_files() as $item)
		{
			$t->define_data(array(
				"name" => html::href(array(
					"caption" => $item->name(),
					"url" => file::get_url($item->id(), $item->name())
				)),
				"created" => $item->created(),
				"createdby" => get_instance(CL_USER)->get_person_for_uid($item->createdby())->name(),
				"sent" => $arr["obj_inst"]->file_is_sent($item) ? t("Jah") : t("Ei"),
				"send" => html::href(array(
					"caption" => t("Saada"),
					"url" => html::get_new_url(CL_MRP_ORDER_SENT, $arr["obj_inst"]->id(), array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 16,
						"return_url" => get_ru(),
						"oc" => $arr["obj_inst"]->workspace,
						"file" => $item->id()
					))
				)),
				"oid" => $item->id()
			));
		}

		$t->set_caption(t("Salvestatud pakkumised"));
		$t->set_default_sortby("created");
	}

	public function _init_sent_table($t)
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Millal"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "subj",
			"caption" => t("Teema"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	public function _get_sent_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sent_table($t);

		foreach($arr["obj_inst"]->get_sent_offers() as $offer)
		{
			$t->define_data(array(
				"date" => $offer->sent_when,
				"who" => $offer->send_to_name." (".$offer->send_to_mail.")",
				"subj" => $offer->send_subject,
				"view" => html::obj_change_url($offer),
				"oid" => $offer->id()
			));
		}

		$t->set_caption(t("Saadetud pakkumised"));
		$t->set_default_sortby("date");
	}

	public function _init_pending_table($t)
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Loodud"),
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Looja"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	public function _get_pending_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_pending_table($t);

		foreach($arr["obj_inst"]->get_pending_offers() as $offer)
		{
			$t->define_data(array(
				"date" => $offer->created,
				"who" => get_instance(CL_USER)->get_person_for_uid($offer->createdby())->name(),
				"view" => html::obj_change_url($offer),
				"oid" => $offer->id()
			));
		}

		$t->set_caption(t("Toimetamisel pakkumised"));
		$t->set_default_sortby("date");
	}

	public function _get_prev_tb($arr)
	{
		$arr["prop"]["vcl_inst"]->add_delete_button();
	}

	public function _get_materials_tree($arr)
	{
		$wh = $arr["obj_inst"]->get_applicable_warehouse()->conf()->prod_fld;
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"root_item" => obj($wh),
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "procurement_center",
			),
			"ot" => new object_tree(array(
				"class_id" => CL_MENU,
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => $wh
			)),
			"var" => "tf",
		));
	}
}

?>

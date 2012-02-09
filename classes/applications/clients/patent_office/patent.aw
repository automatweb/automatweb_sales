<?php
// patent.aw - Trademark
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends applications/clients/patent_office/intellectual_property
@tableinfo aw_trademark index=aw_oid master_table=objects master_index=brother_of

#TRADEMARK
@groupinfo name=trademark caption=Kaubam&auml;rk
@default group=trademark
	@property type type=select
	@caption T&uuml;&uuml;p

	@property undefended_parts type=textbox
	@caption Mittekaitstavad osad

	@property word_mark type=textbox
	@caption S&otilde;nam&auml;rk

	@property colors type=textarea
	@caption V&auml;rvide loetelu (juhul, kui on v&auml;rviline)

	@property trademark_character type=textarea
	@caption Kaubam&auml;rgi iseloomustus

	@property element_translation type=textarea
	@caption V&otilde;&otilde;rkeelsete elementide t&otilde;lge

	@property reproduction type=fileupload reltype=RELTYPE_REPRODUCTION form=+emb
	@caption Lisa reproduktsioon

	@property g_statues type=fileupload reltype=RELTYPE_G_STATUES form=+emb
	@caption Garantiim&auml;rgi p&otilde;hikiri

	@property c_statues type=fileupload reltype=RELTYPE_C_STATUES form=+emb
	@caption Kollektiivm&auml;rgi p&otilde;hikiri

	@property trademark_type type=select multiple=1
	@caption T&uuml;&uuml;p

#tooted ja teenused
@groupinfo products_and_services caption="Kaupade ja teenuste loetelu"
@default group=products_and_services
	@property products_and_services_tbl type=table
	@caption Kaupade ja teenuste loetelu

@default group=priority
	@property childtitle110 type=text store=no subtitle=1
	@caption Konventsiooniprioriteet
		@property convention_nr type=textbox
		@caption Taotluse number

		@property convention_date type=date_select
		@caption Kuup&auml;ev

		@property convention_country type=textbox
		@caption Riigi kood

	@property childtitle111 type=text store=no subtitle=1
	@caption N&auml;ituseprioriteet
		@property exhibition_name type=textbox
		@caption N&auml;ituse nimi

		@property exhibition_date type=date_select
		@caption Kuup&auml;ev

		@property exhibition_country type=textbox
		@caption Riigi kood

#riigil&otilde;iv
@default group=fee
	@property classes_fee type=text
	@caption Lisaklasside l&otilde;iv


// RELTYPES
@reltype C_STATUES value=12 clid=CL_FILE
@caption Kollektiivp&otilde;hikiri

@reltype G_STATUES value=13 clid=CL_FILE
@caption Garantiip&otilde;hikiri

@reltype REPRODUCTION value=9 clid=CL_FILE
@caption Reproduktsioon


*/

class patent extends intellectual_property
{
	public static $level_index = array(
		0 => 0,
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	);

	function __construct()
	{
		parent::__construct();
		$this->init(array(
			"tpldir" => "applications/patent",
			"clid" => CL_PATENT
		));
		$this->info_levels = array(
			0 => "applicant_tm",
			1 => "trademark",
			2 => "products_and_services",
			3 => "priority_tm",
			4 => "fee_tm",
			5 => "check_tm"
		);

		$this->types = array(
			"" => t("M&auml;&auml;ramata"),
			0 => t("S&otilde;nam&auml;rk"),
			1 => t("Kujutism&auml;rk"),
			2 => t("Kombineeritud m&auml;rk"),
			3 => t("Ruumiline m&auml;rk")
		);

		$this->types_disp = array(
			0 => t("(541) S&otilde;nam&auml;rk"),
			1 => t("(546) Kujutism&auml;rk"),
			2 => t("(546) Kombineeritud m&auml;rk"),
			3 => t("(554) Ruumiline m&auml;rk")
		);

		$this->trademark_types = array(
			0 => t("Kollektiivkaubam&auml;rk"),
			1 => t("Garantiim&auml;rk")
		);

		$this->pdf_file_name = "Kaubamargitaotlus";
		$this->show_template = "show_tm.tpl";
		$this->show_sent_template = "show_sent_tm.tpl";
		$this->date_vars = array_merge($this->date_vars, array("exhibition_date", "convention_date"));
		$this->file_upload_vars = array_merge($this->file_upload_vars, array("reproduction" , "g_statues","c_statues"));
		$this->save_fee_vars = array_merge($this->save_fee_vars, array("classes_fee"));
		// $this->chooser_vars = array("type", "trademark_type");
		$this->text_area_vars = array_merge($this->text_area_vars, array("colors" , "trademark_character", "element_translation"));
		$this->text_vars = array_merge($this->text_vars, array("undefended_parts" , "word_mark", "convention_nr"  , "convention_country", "exhibition_name" , "exhibition_country" , "classes_fee"));
		$this->datafromobj_vars = array_merge($this->datafromobj_vars, array("undefended_parts" , "word_mark", "convention_nr"  , "convention_country", "exhibition_name" , "exhibition_country", "classes_fee"));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $arr["obj_inst"]->get_type_options();
				break;

			case "trademark_type":
				$prop["options"] = $arr["obj_inst"]->get_trademark_type_options();
				break;

			case "products_and_services_tbl":
				$this->_get_products_and_services_tbl($arr);
				break;

			default:
				$retval = parent::get_property($arr);
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "products_and_services_tbl":
				if(is_array($arr["request"]["products"]))
				{
					$arr["obj_inst"] -> set_meta("products" , $arr["request"]["products"]);
				}
				break;
		}
		return $retval;
	}

	function _get_products_and_services_tbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
		));
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Kaup/teenus"),
		));

		if(is_array($arr["obj_inst"]->meta("products")))
		{
			foreach($arr["obj_inst"]->meta("products") as $key=> $val)
			{
				$t->define_data(array(
					"prod" => html::textarea(array("name" => "products[".$key."]" , "value" => $val, )),
					"class" => $key,
				));
			}
		}
		return $t->draw();
	}

	function get_results_table()
	{
		if (!empty($_SESSION["patent"]["delete"]))
		{
			unset($_SESSION['patent']['products'][$_SESSION["patent"]["delete"]]);
			$_SESSION["patent"]["delete"] = null;
		}

		if ((!isset($_SESSION["patent"]["prod_selection"]) or !is_array($_SESSION["patent"]["prod_selection"])) and (!isset($_SESSION["patent"]["products"]) or !is_array($_SESSION["patent"]["products"])))
		{
			return;
		}

		$t = new vcl_table(array(
			"layout" => "generic",
		));

		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
		));
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Kaup/teenus"),
		));
		$t->define_field(array(
			"name" => "delete",
			"caption" => "",
		));


		$classes = array();
		if(isset($_SESSION["patent"]["prod_selection"]) and is_array($_SESSION["patent"]["prod_selection"]))
		{
			foreach($_SESSION["patent"]["prod_selection"] as $prod)
			{
				if(!acl_base::can("view" , $prod))
				{
					continue;
				}

				$product = obj($prod);
				$parent = obj($product->parent());
				$classes[$parent->comment()][$product->id()] = $product->prop("userta1");
			}
			$_SESSION["patent"]["prod_selection"] = null;
		}


		if(isset($_SESSION["patent"]["products"]) and is_array($_SESSION["patent"]["products"]))
		{
			foreach($_SESSION["patent"]["products"] as $key=> $val)
			{
				$classes[$key][] = $val;
			}
		}

		ksort($classes);
		foreach($classes as $class => $prods)
		{
			$t->define_data(array(
				"prod" => html::textarea(array(
					"name" => "products[".$class."]" ,
					"cols" => 40,
					"rows" => 5,
					"value" => join("\n" , $prods)
				)),
				"class" => $class,
				"delete" => html::href(array(
					"url" => "#",
					"onclick" => 'fRet = confirm("'.t("Oled kindel, et soovid valitud klassi kustutada?").'"); if(fRet) {document.getElementById("delete").value="'.$class.'";document.getElementById("stay").value=1;
					document.changeform.submit();} else;',
					"caption" => t("Kustuta"),
				)),

			));
		}
		return $t->draw();
	}

	function get_vars($arr)
	{
		$data = parent::get_vars($arr);
		$_SESSION["patent"]["classes_fee_info"] = $this->get_classes_fee();
		$data["classes_fee_info"] = $_SESSION["patent"]["classes_fee_info"];
		$patent_type = isset($_SESSION["patent"]["type"]) ? (int) $_SESSION["patent"]["type"] : 0;

		if(isset($_SESSION["patent"]["applicants"]) and sizeof($_SESSION["patent"]["applicants"]) == 1)
		{
			$applicant_ids = array_keys($_SESSION["patent"]["applicants"]);
			$_SESSION["patent"]["representer"] = reset($applicant_ids);
		}

		$data["type_text"] = isset($this->types_disp[$patent_type]) ? $this->types_disp[$patent_type] : "";
		//$data["products_value"] = $this->_get_products_and_services_tbl();
		$data["type"] = t("S&otilde;nam&auml;rk ").html::radiobutton(array(
				"value" => 0,
				"checked" => 0 === $patent_type,
				"name" => "type",
				"onclick" => "
					document.getElementById('wordmark_row').style.display = '';
					document.getElementById('reproduction_row').style.display = 'none';
					document.getElementById('color_row').style.display = 'none';
					document.getElementById('colors').value = '';
					document.getElementById('wordmark_caption').innerHTML = '* Kaubam&auml;rk';
					document.getElementById('foreignlangelements_row').style.display = '';
					 ",
			)).t("&nbsp;&nbsp;&nbsp;&nbsp; Kujutism&auml;rk ").html::radiobutton(array(
				"value" => 1,
		 		"checked" => 1 === $patent_type,
				"name" => "type",
				"onclick" => "
					document.getElementById('wordmark_row').style.display = 'none';
					document.getElementById('word_mark').value = '';
					document.getElementById('foreignlangelements_row').style.display = 'none';
					document.getElementById('element_translation').value = '';
					document.getElementById('reproduction_row').style.display = '';
					document.getElementById('color_row').style.display = '';"
			)).t("&nbsp;&nbsp;&nbsp;&nbsp; Kombineeritud m&auml;rk ").html::radiobutton(array(
				"value" => 2,
				"checked" => 2 === $patent_type,
				"name" => "type",
				"onclick" => "
					document.getElementById('color_row').style.display = '';
					document.getElementById('reproduction_row').style.display = '';
					document.getElementById('wordmark_row').style.display = 'none';
					document.getElementById('word_mark').value = '';
					document.getElementById('foreignlangelements_row').style.display = '';",
			)).t("&nbsp;&nbsp;&nbsp;&nbsp; Ruumiline m&auml;rk ").html::radiobutton(array(
				"value" => 3,
				"checked" => 3 === $patent_type,
				"name" => "type",
				"onclick" => "
					document.getElementById('color_row').style.display = '';
					document.getElementById('reproduction_row').style.display = '';
					document.getElementById('wordmark_row').style.display = 'none';
					document.getElementById('word_mark').value = '';
					document.getElementById('foreignlangelements_row').style.display = '';",
			));

		$data["wm_caption"] = $patent_type ? t("S&otilde;naline osa:") : t("Kaubam&auml;rk:");

		$data["trademark_type"] = t("(kui taotlete kollektiivkaubam&auml;rki)").html::checkbox(array(
			"value" => 1,
			"checked" => !empty($_SESSION["patent"]["co_trademark"]),
			"name" => "co_trademark",
			"onclick" => "document.getElementById('c_statues_row').style.display = '';"
			)).'<a href="javascript:;" onClick="MM_openBrWindow(\'16340\',\'\',\'width=720,height=540\')"><img src="/img/lk/ikoon_kysi.gif" border="0" /></a><br />'.

			t("(kui taotlete garantiikaubam&auml;rki)").html::checkbox(array(
				"value" => 1,
				"checked" => !empty($_SESSION["patent"]["guaranty_trademark"]),
				"name" => "guaranty_trademark",
				"onclick" => "document.getElementById('g_statues_row').style.display = '';"
			)).'<a href="javascript:;" onclick="MM_openBrWindow(\'16341\',\'\',\'width=720,height=540\')"><img src="/img/lk/ikoon_kysi.gif" border="0" />';
		$data["trademark_type_text"] = !empty($_SESSION["patent"]["co_trademark"]) ? t("Kollektiivkaubam&auml;rk") : "";
		$data["trademark_type_text"].= " ";
		$data["trademark_type_text"].= !empty($_SESSION["patent"]["guaranty_trademark"]) ? t("Garantiim&auml;rk") : "";

		$data["find_products"] = html::href(array(
			"caption" => t("Sisene klassifikaatorisse") ,
			"url"=> "javascript:void(0);",
			"onclick" => 'javascript:window.open("'.$this->mk_my_orb("find_products", array("ru" => get_ru(), "print" => 1),  "patent").'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=400, width=600");',
		));

		$_SESSION["patent"]["prod_ru"] = get_ru();
		$data["results_table"] = $this->get_results_table();

		$data["show_link"] = "javascript:window.open('".$this->mk_my_orb("show", array("print" => 1 , "id" => isset($_SESSION["patent"]["id"]) ? $_SESSION["patent"]["id"] : "", "add_obj" => $arr["alias"]["to"]),  "patent")."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=600, width=800')";
		$data["convert_link"] = $this->mk_my_orb("pdf", array("print" => 1 , 	"id" => isset($_SESSION["patent"]["id"]) ? $_SESSION["patent"]["id"] : "", "add_obj" => $arr["alias"]["to"]), "patent");
		return $data;
	}

	/**
		@attrib name=find_products nologin=1 all_args=1
		@param ru required type=string
	**/
	function find_products($arr)
	{
		if(!empty($_POST["do_post"]))
		{
			$_SESSION["patent"]["prod_selection"] =  $_POST["oid"];
			die("
				<script type='text/javascript'>
					window.opener.document.getElementById('stay').value=1;
					window.opener.document.changeform.submit();
					window.close();
				</script>"
			);
		}

		$result_list = "";
		if(!empty($arr["product"]) || !empty($arr["prodclass"]))
		{
			if($arr["prodclass"])
			{
				$limit = 1700;
			}
			else
			{
				$limit = 500;
			}
			$tpl = "products_res.tpl";
			$is_tpl = $this->read_template($tpl,1);

			$t = new vcl_table(array(
				"layout" => "generic",
			));

			$t->define_field(array(
				"name" => "class",
				"caption" => t("Klass"),
			));
			$t->define_field(array(
				"name" => "prod",
				"caption" => t("Kaup/teenus"),
			));
			$t->define_chooser(array(
				"name" => "oid",
				"field" => "oid",
				"caption" => t("Vali"),
			));

			$products = new object_list();
			if(strlen($arr["prodclass"]) == 1)
			{
				$arr["prodclass"] = "0".$arr["prodclass"];
			}
			$parents = new object_list(array(
				"comment" => "%".$arr["prodclass"]."%",
				"class_id" => CL_MENU,
				"lang_id" => array(),
				"limit" => $limit,
			));
			$parents->sort_by(array(
				"prop" => "name",
				"order" => "asc"
			));

			foreach ($parents->ids() as $id)
			{
				$prod_list = new object_list(array(
					"userta1" => "%".$arr["product"]."%",
					"parent" => $id,
					"class_id" => CL_SHOP_PRODUCT,
					"lang_id" => array(),
					"limit" => $limit
				));

				foreach($prod_list->arr() as $p)
				{
					if($p->prop("userch10"))
					{
						$prod_list->remove($p->id());
						$products->add($p->id());
					}
				}

				$prod_list->sort_by(array(
					"prop" => "name",
					"order" => "asc"
				));

				$products->add($prod_list);
			}

			if($is_tpl)
			{
				$c = "";
				foreach($products->arr() as $prod)
				{
					$parent = obj($prod->parent());
					if($prod->prop("userch10"))
					{
						$p = html::bold($prod->prop("userta1"));
					}
					else
					{
						$p = $prod->prop("userta1");
					}
					$this->vars(array(
						"prod" => $p,
						"class" => $parent->name(),
						"code" => 132245,
						"oid"	=> $prod->id(),
					));
					$c .= $this->parse("PRODUCT");
				}
				$this->vars(array(
					"PRODUCT" => $c,
					"ru" => $arr["ru"]
				));
				$result_list =  $this->parse();
			}
			else
			{
				foreach($products->arr() as $prod)
				{
					$parent = obj($prod->parent());
					$t->define_data(array(
						"prod" => $prod->name(),
						"class" => $parent->name(),
						"code" => 132245,
						"oid"	=> $prod->id(),
					));
				}
				$result_list =  '<form action="" method="post">' . $t->draw(). '
				<input type="hidden" value="'.$arr["ru"].'" name="ru">
				<input type="hidden" value="1" name="do_post">
				<input type="submit" value="Lisa valitud terminid taotlusse">';
			}
		}

		$tpl = "products.tpl";
		$is_tpl = $this->read_template($tpl);
		if($is_tpl)
		{
			$this->vars(array(
				"result" => $result_list,
				"ru" 	=> $arr["ru"]
			));
			return $this->parse();
		}

		$ret = "<form action='' method='get'>Klassi nr:".
		html::textbox(array("name" => "class"))."<input type='hidden' name='class' value='patent' />
<input type='hidden' name='print' value='1' />
<input type='hidden' name='action' value='find_products' />
<input type='hidden' name='ru' value='".$arr["ru"]."' />
<br /> Kauba/teenuse nimetus".html::textbox(array("name" => "product"))
		.$reforb.'<input type="submit" value="otsi"></form>';

		return $ret . $result_list;
	}

	protected function save_priority($patent)
	{
		$patent->set_prop("convention_nr" , isset($_SESSION["patent"]["convention_nr"]) ? $_SESSION["patent"]["convention_nr"] : "");
		$patent->set_prop("convention_date" , isset($_SESSION["patent"]["convention_date"]) ? $_SESSION["patent"]["convention_date"] : "");
		$patent->set_prop("convention_country" , isset($_SESSION["patent"]["convention_country"]) ? $_SESSION["patent"]["convention_country"] : "");
		$patent->set_prop("exhibition_name" , isset($_SESSION["patent"]["exhibition_name"]) ? $_SESSION["patent"]["exhibition_name"] : "");
		$patent->set_prop("exhibition_date" , isset($_SESSION["patent"]["exhibition_date"]) ? $_SESSION["patent"]["exhibition_date"] : "");
		$patent->set_prop("exhibition_country" , isset($_SESSION["patent"]["exhibition_country"]) ? $_SESSION["patent"]["exhibition_country"] : "");
		$patent->save();
	}

	protected function save_trademark($patent)
	{
		$patent->set_prop("word_mark" , $_SESSION["patent"]["word_mark"]);
		$patent->set_prop("colors" , $_SESSION["patent"]["colors"]);
		$patent->set_prop("trademark_character" , $_SESSION["patent"]["trademark_character"]);
		$patent->set_prop("element_translation" , $_SESSION["patent"]["element_translation"]);
		$patent->set_prop("type" , $_SESSION["patent"]["type"]);
		$patent->set_prop("undefended_parts" , $_SESSION["patent"]["undefended_parts"]);
		$tr_type = array();
		if($_SESSION["patent"]["co_trademark"])
		{
			$tr_type[] = 0;
		}
		if($_SESSION["patent"]["guaranty_trademark"])
		{
			$tr_type[] = 1;
		}
		$patent->set_prop("trademark_type" , $tr_type);
		$patent->save();
	}

	protected function save_forms($patent)
	{
		$this->save_trademark($patent);
		$this->save_priority($patent);
		$this->save_fee($patent);
		$this->save_applicants($patent);
		$this->fileupload_save($patent);
		$this->final_save($patent);
		$patent->set_meta("products" , $_SESSION["patent"]["products"]);
	}

	protected function get_object()
	{
		if(!empty($_SESSION["patent"]["id"]))
		{
			$patent = obj($_SESSION["patent"]["id"]);
		}
		else
		{
			$patent = new object();
			$patent->set_class_id(CL_PATENT);
			$patent->set_parent($_SESSION["patent"]["parent"]);
			$patent->save();
			$patent->set_name(" Kinnitamata taotlus nr [".$patent->id()."]");
		}

		return $patent;
	}

	public function get_payment_sum($float = false)
	{
		$sum = 0;
		if(isset($_SESSION["patent"]["products"]) && is_array($_SESSION["patent"]["products"]) && count($_SESSION["patent"]["products"]))
		{
			$classes_fee = $this->get_classes_fee(true);
			$sum = $this->get_request_fee(true) + $classes_fee;
		}
		return $float ? $sum : number_format($sum, 2, ",", "");
	}

	public function get_request_fee($float = false)
	{
		$sum = 0;
		if (isset($_SESSION["patent"]["products"]) && is_array($_SESSION["patent"]["products"]) && count($_SESSION["patent"]["products"]))
		{
			if (!empty($_SESSION["patent"]["co_trademark"]) || !empty($_SESSION["patent"]["guaranty_trademark"]))
			{
				$sum = 191.73;
			}
			else
			{
				$sum = 140.60;
			}
		}
		return $float ? $sum : number_format($sum, 2, ",", "");
	}

	public function get_classes_fee($float = false)
	{
		$sum = 0;
		if(isset($_SESSION["patent"]["products"]) && is_array($_SESSION["patent"]["products"]) && count($_SESSION["patent"]["products"]))
		{
			$sum = (sizeof($_SESSION["patent"]["products"]) - 1 )*44.73;
		}
		return $float ? $sum : number_format($sum, 2, ",", "");
	}

	function fill_session($id)
	{
		$address_inst = get_instance(CL_CRM_ADDRESS);
		$patent = obj($id);
		parent::fill_session($id);

		if(isset($_SESSION["patent"]["trademark_type"][0]))
		{
			$_SESSION["patent"]["co_trademark"] = 1;
		}

		if(isset($_SESSION["patent"]["trademark_type"][1]))
		{
			$_SESSION["patent"]["guaranty_trademark"] = 1;
		}

		$_SESSION["patent"]["products"] = $patent->meta("products");
		$_SESSION["patent"]["representer"] = $patent->prop("applicant");
		$_SESSION["patent"]["type"] = $patent->prop("type");
		$_SESSION["patent"]["trademark_type"] = $patent->prop("trademark_type");
	}

	function get_data_from_object($id)
	{
		$data = parent::get_data_from_object($id);
		$o = obj($id);
		$tr = $o->prop("trademark_type");
		$data["trademark_type_text"] = !empty($tr[0]) ? $this->trademark_types[0] : "";
		$data["trademark_type_text"].= " ";
		$data["trademark_type_text"].= !empty($tr[1]) ? $this->trademark_types[1] : "";
		$data["type_text"] = $this->types[$o->prop("type")];
		return $data;
	}

	function get_js()
	{
		$js2 = parent::get_js();
		$js = "";

		if (automatweb::$request->arg("data_type") == 1)
		{
			if (empty($_SESSION["patent"]["type"]))
			{
				$js.='document.getElementById("reproduction_row").style.display = "none";';
				$js.='document.getElementById("color_row").style.display = "none";';
      			$js.='document.getElementById("wordmark_row").style.display = "";';
				$js.='document.getElementById("wordmark_caption").innerHTML = "* Kaubam&auml;rk";';
				$js.='document.getElementById("foreignlangelements_row").style.display = "";';
			}
			elseif ($_SESSION["patent"]["type"] == 1)
			{
				$js.='document.getElementById("wordmark_row").style.display = "none";';
				$js.='document.getElementById("foreignlangelements_row").style.display = "none";';
				$js.='document.getElementById("reproduction_row").style.display = "";';
				$js.='document.getElementById("color_row").style.display = "";';
			}
			elseif ($_SESSION["patent"]["type"] == 2)
			{
				$js.='document.getElementById("wordmark_row").style.display = "none";';
				$js.='document.getElementById("color_row").style.display = "";';
				$js.='document.getElementById("reproduction_row").style.display = "";';
				$js.='document.getElementById("foreignlangelements_row").style.display = "";';
     		}
			elseif ($_SESSION["patent"]["type"] == 3)
			{
				$js.='document.getElementById("wordmark_row").style.display = "none";';
				$js.='document.getElementById("color_row").style.display = "";';
				$js.='document.getElementById("reproduction_row").style.display = "";';
				$js.='document.getElementById("foreignlangelements_row").style.display = "";';
			}

			if (empty($_SESSION["patent"]["guaranty_trademark"]))
			{
				$js.='document.getElementById("g_statues_row").style.display = "none";';
			}

			if (empty($_SESSION["patent"]["co_trademark"]))
			{
				$js.='document.getElementById("c_statues_row").style.display = "none";';
			}
		}
		return $js . $js2;
	}

	function check_fields()
	{
		$err = parent::check_fields();
		$requested_data_type = automatweb::$request->arg("data_type");

		if($requested_data_type === "2")
		{
			if(
				(empty($_POST["products"]) or !is_array($_POST["products"])) and
				(empty($_SESSION["patent"]["prod_selection"]) or !is_array($_SESSION["patent"]["prod_selection"])) and
				(empty($_SESSION["patent"]["products"]) or !is_array($_SESSION["patent"]["products"]))
			)
			{
				$err.= t("Kohustuslik v&auml;hemalt &uuml;he klassi lisamine")."\n<br />";
			}
		}

		if($requested_data_type === "1")
		{
			if($_POST["type"] == 0 && !isset($_POST["word_mark"]))
			{
				$err.= t("S&otilde;nam&auml;rgi puhul peab olema s&otilde;naline osa t&auml;idetud")."\n<br />";
			}
			if($_POST["type"] == 1 && !$_FILES["reproduction_upload"]["name"] && !is_oid($_SESSION["patent"]["reproduction"]))
			{
				$err.= t("Peab olema lisatud ka reproduktsioon")."\n<br />";
			}
			if($_POST["type"] == 2 && !isset($_POST["word_mark"]))
			{
				$err.= t("Kombineeritud m&auml;rgi puhul peab olema s&otilde;naline osa t&auml;idetud")."\n<br />";
			}
			if($_POST["type"] == 2 && !$_FILES["reproduction_upload"]["name"] && !is_oid($_SESSION["patent"]["reproduction"]))
			{
				$err.= t("Peab olema lisatud ka reproduktsioon")."\n<br />";
			}
			if($_POST["type"] == 3 && !$_FILES["reproduction_upload"]["name"] && !is_oid($_SESSION["patent"]["reproduction"]))
			{
				$err.= t("Peab olema lisatud ka reproduktsioon")."\n<br />";
			}
		}

		if($requested_data_type === "3" and ($_POST["convention_date"]["day"] || $_POST["exhibition_date"]["day"]))
		{
			$six_months_back = mktime(0, 0, 0, date("m" , time())-6, date("j", time())-5, date("Y" , time()));
			if (
				(
					$_POST["convention_nr"] &&
					mktime(0, 0, 0, (int) $_POST["convention_date"]["month"], (int) $_POST["convention_date"]["day"], (int) $_POST["convention_date"]["year"])
						<
					$six_months_back
				)
				||
				(
					$_POST["exhibition_name"] &&
					mktime(0, 0, 0, (int) $_POST["exhibition_date"]["month"], (int) $_POST["exhibition_date"]["day"], (int) $_POST["exhibition_date"]["year"])
						<
					$six_months_back
				)
			 )
			{
				$err.= t("Prioriteedikuup&auml;ev ei v&otilde;i olla vanem kui 6 kuud")."\n<br />";
			}
		}

		if(empty($err))
		{
			$_SESSION["patent"]["checked"][$requested_data_type] = $requested_data_type;
		}
		else
		{
			unset($_SESSION["patent"]["checked"][$requested_data_type]);
		}

		return $err;
	}

	/**
		@attrib api=1
		@param o required type=object
		@returns
			PHP DOMDocument instance
	**/
	public function get_po_xml(object $o)
	{
		$xml = parent::get_po_xml($o);
		$xpath = new DOMXPath($xml);
		$root = $xpath->query("//BIRTH")->item(0);
		$despg = $xpath->query("//DESPG")->item(0);

		$inst = $o->instance();
		$status = $inst->get_status($o);
		$root->setAttribute("EXPDATE", date("Ymd", $status->prop("modified")));

		$type = "";
		// save image to folder
		if (acl_base::can("", $o->prop("reproduction")))
		{
			$im = obj($o->prop("reproduction"));
			$type = strtoupper(substr($im->name(), strrpos($im->name(), ".")));

			$fld = aw_ini_get("site_basedir")."patent_files/";
			$fn = $fld .sprintf("%08d", $status->prop("nr")).$type;
			echo "saving file {$fn}\n";
			$image_inst = new file();
			$imd = $image_inst->get_file_by_id($im->id(), true);
			$f = fopen($fn ,"w");
			fwrite($f, $imd["content"]);
			fclose($f);
		}//t6stsin seda ettepoole, et ilma reproduktsioonita tahetakse ka tegelikult s6nalist osa n2ha

		$img = $xml->createElement("IMAGE");
		$img->setAttribute("NAME", sprintf("%08d", $status->prop("nr")));
		$img->setAttribute("TEXT", trademark_manager::convert_to_export_xml($o->prop("word_mark")));
		$img->setAttribute("COLOUR", ($o->prop("colors") != "" ? "Y" : "N"));
		$img->setAttribute("TYPE", trademark_manager::convert_to_export_xml($type));
		$root->insertBefore($img, $despg);

		$el = $xml->createElement("MARTRGR");
		$el->appendChild(new DOMElement("MARTREN", trademark_manager::convert_to_export_xml($o->prop("element_translation"))));
		$root->insertBefore($el, $despg);

		$typm = $o->prop("trademark_type");
		$el = $xml->createElement("TYPMARI", (isset($typm["1"]) && $typm["1"] == "1" ? "G" : "").(isset($typm["0"]) && $typm["0"] === "0" ? "C" : ""));
		$root->insertBefore($el, $despg);

		//
		$el = $xml->createElement("MARDESGR");
		$el2 = $xml->createElement("MARDESEN");
		if ($o->prop("trademark_character"))
		{
			$cdata = $xml->createCDATASection(trademark_manager::convert_to_utf($o->prop("trademark_character")));
			$el2->appendChild($cdata);
		}
		$el->appendChild($el2);
		$root->insertBefore($el, $despg);

		//
		$el = $xml->createElement("DISCLAIMGR");
		$el2 = $xml->createElement("DISCLAIMEREN");
		if ($o->prop("undefended_parts"))
		{
			$cdata = $xml->createCDATASection(trademark_manager::convert_to_utf($o->prop("undefended_parts")));
			$el2->appendChild($cdata);
		}
		$el->appendChild($el2);
		$root->insertBefore($el, $despg);

		//
		if ($o->prop("colors"))
		{
			$el = $xml->createElement("MARCOLI");
			$root->insertBefore($el, $despg);
		}

		//
		if ($o->prop("type") == 3)
		{
			$el = $xml->createElement("THRDMAR");
			$root->insertBefore($el, $despg);
		}

		//
		$el = $xml->createElement("COLCLAGR");
		$el2 = $xml->createElement("COLCLAEN");
		if ($o->prop("colors"))
		{
			$cdata = $xml->createCDATASection(trademark_manager::convert_to_utf($o->prop("colors")));
			$el2->appendChild($cdata);
		}
		$el->appendChild($el2);
		$root->insertBefore($el, $despg);

		// products
		$el = $xml->createElement("BASICGS");
		$el->setAttribute("NICEVER", "9");

		foreach(safe_array($o->meta("products")) as $k => $v)
		{
			$el2 = $xml->createElement("GSGR");
			$el2->setAttribute("NICCLAI", trademark_manager::convert_to_export_xml($k));
			$el3 = $xml->createElement("GSTERMEN");
			$cdata = $xml->createCDATASection(mb_strtolower(str_replace(array("\r", "\n"), "", trademark_manager::convert_to_utf($v)), "UTF-8"));
			$el3->appendChild($cdata);
			$el2->appendChild($el3);
			$el->appendChild($el2);
		}

		$root->insertBefore($el, $despg);

		//
		$el = $xml->createElement("BASGR");
		$el2 = $xml->createElement("BASAPPGR");
		$el->appendChild($el2);
		$el2->appendChild(new DOMElement("BASAPPD", date("Ymd", $status->prop("modified"))));
		$el2->appendChild(new DOMElement("BASAPPN", sprintf("%08d", $status->prop("nr"))));
		$root->insertBefore($el, $despg);

		// priority
		$pri_co = $pri_date = $pri_name = "";
		if($o->prop("convention_date") !== "-1")
		{
			$pri_date = date("Ymd",$o->prop("convention_date"));
		}

		if($o->prop("exhibition_date") !== "-1")
		{
			$pri_date = date("Ymd",$o->prop("exhibition_date"));
		}

		if($o->prop("convention_nr"))
		{
			$pri_name = $o->prop("convention_nr");
		}

		if($o->prop("exhibition_name"))
		{
			$pri_name = $o->prop("exhibition_name");
		}

		if($o->prop("convention_nr") or $o->prop("exhibition_name"))
		{
			$pri_co = ($o->prop("convention_country")) ? $o->prop("convention_country") : $o->prop("exhibition_country");
		}

		$el = $xml->createElement("PRIGR");
		$el->appendChild(new DOMElement("PRICP", $pri_co));

		if ($o->prop("convention_date") !== "-1" or $o->prop("exhibition_date") !== "-1")
		{
			$el->appendChild(new DOMElement("PRIAPPD", $pri_date));
		}

		$el->appendChild(new DOMElement("PRIAPPN", trademark_manager::convert_to_export_xml($pri_name)));
		$root->insertBefore($el, $despg);

		//
		return $xml;
	}
}

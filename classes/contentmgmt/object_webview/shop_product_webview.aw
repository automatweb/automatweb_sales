<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends contentmgmt/object_webview/object_webview

@default group=general
@default table=objects
@default field=meta
@default method=serialize

@property object type=objpicker clid=CL_SHOP_PRODUCT
@caption Toode mida kuvada

@property sales_contact_persons type=relpicker multiple=1 reltype=RELTYPE_SALES_CONTACT_PERSON
@caption Kontaktisikud
@comment Isikud, kellega toote vastu huvi tundja saab kontakteeruda


@reltype SALES_CONTACT_PERSON value=1 clid=CL_CRM_PERSON
@caption Kontaktisik

*/

class shop_product_webview extends object_webview
{
	public function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_webview/shop_product_webview",
			"clid" => shop_product_webview_obj::CLID
		));
	}

	public function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["to"]));
	}

	public function show($arr)
	{
		$this->load_storage_object($arr);
		$this_o = $this->awcb_ds_id;

		if (!acl_base::can("view", $this_o->prop("object")))
		{
			return t("Juurdep&auml;&auml;s toote vaatamiseks puudub.");
		}

		$product = new object($this_o->prop("object"));
		$tpl = $this_o->prop("template") . ".tpl";
		$this->read_template($tpl);
		$this->vars($product->properties());
		return $this->parse();
	}
}

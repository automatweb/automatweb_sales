<?php
/*
@classinfo syslog_type=ST_SHOP_ORDERER_DATA_SITE_SHOW_DELIVERY_NOTES relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_shop_orderer_data_site_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_orderer_data_site_show
@default group=general

@property template type=select
@caption Template
*/

class shop_orderer_data_site_show_delivery_notes extends shop_orderer_data_site_show
{
	function shop_orderer_data_site_show_delivery_notes()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_orderer_data_site_show",
			"clid" => CL_SHOP_ORDERER_DATA_SITE_SHOW_DELIVERY_NOTES
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$tpl = "show.tpl";
		if($arr["tpl"])
		{
			$tpl = $arr["tpl"];
		}
		$this->read_template($tpl);

		$vars = array(
			"name" => $ob->prop("name"),
		);
		$bills = new object_list();
		$rows = "";
		$co = get_current_company();
		$co = obj(2818612);
		if(is_object($co))
		{
			$notes = $co->get_delivery_notes();
		}

		$note_inst = get_instance(CL_SHOP_DELIVERY_NOTE);

		foreach($notes->arr() as $note)
		{
			$note_vars = array();
			$note_vars["id"] = $note->id();
			$note_vars["number"] = $note->prop("number");
			$note_vars["currency"] = $note->prop("currency.name");
			$note_vars["date"] = date("d.m.Y" , $note->prop("enter_date"));
			$note_vars["delivery"] = date("d.m.Y" , $note->prop("delivery_date"));
			$note_vars["sum"] = $note->get_sum();
			$note_vars["url"] = "/".$note->id();

			$this->vars($note_vars);
			$rows.=$this->parse("ROW");
		}
		$vars["ROW"] = $rows;
		$this->vars($vars);
		return $this->parse();
	}

	
}

?>

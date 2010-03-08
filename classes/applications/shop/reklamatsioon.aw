<?php
/*
@classinfo syslog_type=ST_REKLAMATSIOON relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_reklamatsioon master_index=brother_of master_table=objects index=aw_oid

@default table=aw_reklamatsioon
@default group=general

	@property buyer type=relpicker reltype=RELTYPE_CUSTOMER
	@caption Klient

	@property seller type=relpicker reltype=RELTYPE_SELLER
	@caption M&uuml;&uuml;ja

	@property type type=select
	@caption t&uuml;&uuml;p

	@property number type=textbox
	@caption Number

	@property date type=date_select
	@caption Kuup&auml;ev

	@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE automatic=1 field=aw_warehouse
	@caption Ladu

@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype SELLER value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption M&uuml;&uuml;ja

@reltype ROW value=3 clid=CL_REKLAMATSIOON_ROW
@caption Rida

@reltype WAREHOUSE value=4 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class reklamatsioon extends class_base
{
	function reklamatsioon()
	{
		$this->init(array(
			"tpldir" => "applications/shop/reklamatsioon",
			"clid" => CL_REKLAMATSIOON
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
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
			$this->db_query("CREATE TABLE aw_reklamatsioon(aw_oid int primary key, number varchar(255), buyer int, date int, warehouse int)");
			return true;
		}
		switch($f)
		{
			case "status":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}
}

?>

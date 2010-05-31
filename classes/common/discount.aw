<?php
/*
@classinfo syslog_type=ST_DISCOUNT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_discount master_index=brother_of master_table=objects index=aw_oid

@default table=aw_discount
@default group=general

@property recur type=checkbox ch_value=1
@caption Kordub

@property object type=hidden
@caption Objekt mille soodusega on tegu

@property class type=hidden
@caption Objekti klass mille soodusega on tegu

@property active type=checkbox ch_value=1
@caption Kehtib

@property date_from type=date_select
@caption Alates

@property date_to type=date_select
@caption Kuni

@property weekdays type=chooser multiple=1 captionside=top
@caption N&auml;dalap&auml;evad

@property apply_groups type=relpicker reltype=RELTYPE_GROUP multiple=1 store=connect
@caption Kehtib gruppidele

@property bron_made_from type=datetime_select default=-1
@caption Objekt tehtud alates

@property bron_made_to type=datetime_select default=-1
@caption Objekt tehtud kuni

@property bargain_percent type=textbox
@caption Soodustuse protsent

@reltype GROUP value=1 clid=CL_GROUP
@caption Kehtib grupile

*/

class discount extends class_base
{
	function discount()
	{
		$this->init(array(
			"tpldir" => "common/discount",
			"clid" => CL_DISCOUNT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "object":
				if(!empty($arr['new']))
				{
					if(!$prop["value"])
					{
						$prop["value"] = $arr["request"]["parent"];
					}
				}
				break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "object":
		}

		return $retval;
	}

	function callback_mod_reforb(&$arr)
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
			$this->db_query("CREATE TABLE aw_discount(aw_oid int primary key)");
			return true;
		}
		switch($f)
		{
			case "recur":
			case "active":
			case "date_from":
			case "date_to":
			case "weekdays":
			case "bron_made_to":
			case "bron_made_from":
			case "bargain_percent":
			case "object":
			case "class":
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

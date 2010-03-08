<?php
/*
@classinfo syslog_type=ST_SHOP_UNIT_FORMULA relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_unit_formula master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_unit_formula
@default group=general

@property from_unit type=relpicker reltype=RELTYPE_FROM_UNIT
@caption L&auml;hte&uuml;hik (A)

@property to_unit type=relpicker reltype=RELTYPE_TO_UNIT
@caption Siht&uuml;hik (B)

@property simple_formula type=textbox datatype=int
@caption Lihtne valem (X = B/A)

@property complex_formula type=relpicker reltype=RELTYPE_FORMULA
@caption Keeruline valem

@property formula_changes type=callback store=no callback=callback_formula_changes
@caption Valemi muudatused

@reltype FROM_UNIT value=1 clid=CL_UNIT
@caption L&auml;hte&uuml;hik

@reltype TO_UNIT value=2 clid=CL_UNIT
@caption Siht&uuml;hik

@reltype FORMULA value=3 clid=CL_CFGCONTROLLER
@caption Valem
*/

class shop_unit_formula extends class_base
{
	function shop_unit_formula()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_unit_formula",
			"clid" => CL_SHOP_UNIT_FORMULA
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "formula_changes":
				if($arr["request"]["action"] == "new")
				{
					return PROP_IGNORE;
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
			case "formula_changes":
				$cid = $arr["obj_inst"]->prop("complex_formula");
				if($cid && $this->can("view", $cid))
				{
					$co = obj($cid);
					$code = $co->prop("formula");
					$res = array();
					$nums = $arr["obj_inst"]->get_nums_from_formula($code);
					foreach($nums as $num)
					{
						$name = "num_".str_replace(".", "_", $num);
						$res[$name] = $arr["request"][$name];
					}
					$arr["obj_inst"]->set_meta("formula_vars", $res);
				}
				break;
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

	function callback_formula_changes($arr)
	{
		$ret = array();
		$cid = $arr["obj_inst"]->prop("complex_formula");
		if($cid && $this->can("view", $cid))
		{
			$set_vals = $arr["obj_inst"]->meta("formula_vars");
			$co = obj($cid);
			$code = $co->prop("formula");
			$nums = $arr["obj_inst"]->get_nums_from_formula($code);
			foreach($nums as $num)
			{
				$name = "num_".str_replace(".", "_", $num);
				$ret[$name] = array(
					"name" => $name,
					"type" => "textbox",
					"caption" => sprintf(t("Valemi arv %s"), $num),
					"value" => ($v = $set_vals[$name])?$v:$num,
				);
			}
		}
		return $ret;
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_unit_formula(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "from_unit":
			case "to_unit":
			case "complex_formula":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ret = true;
				break;
			case "simple_formula":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "float"
				));
				$ret = true;
				break;
			case "formula_changes":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				$ret = true;
				break;
		}

		switch($f)
		{
			case "from_unit":
			case "to_unit":
				$this->db_query("ALTER TABLE aw_shop_unit_formula ADD INDEX(".$f.")");
		}
		return $ret;
	}
}

?>

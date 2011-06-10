<?php

class aw_class_property_obj extends _int_object
{
	const CLID = 1506;

	function prop($k)
	{
		if ($k[0] == "p" && $k[1] == "_")
		{
			list($clid, $pn) = explode("::", $this->name());
			$clid = constant($clid);
			$tmp = obj();
			$tmp->set_class_id($clid);
			$pl = $tmp->get_property_list();

			return ifset($pl[$pn], substr($k, 2));
		}
		else
		if ($k == "c_class")
		{
			list($clid, $pn) = explode("::", $this->name());
			$clid = constant($clid);
			$cls = aw_ini_get("classes");
			return $cls[$clid]["name"];
		}
		return parent::prop($k);
	}

	function prop_str($k, $is_oid = NULL)
	{
		if ($k[0] == "p" && $k[1] == "_")
		{
			list($clid, $pn) = explode("::", $this->name());
			$clid = constant($clid);
			$tmp = obj();
			$tmp->set_class_id($clid);
			$pl = $tmp->get_property_list();

			return ifset($pl[$pn], substr($k, 2));
		}
		else
		if ($k == "c_class")
		{
			list($clid, $pn) = explode("::", $this->name());
			$clid = constant($clid);
			$cls = aw_ini_get("classes");
			return $cls[$clid]["name"];
		}
		return parent::prop_str($k, $is_oid);
	}

	function set_prop($k, $v)
	{
		if ($k[0] == "p" && $k[1] == "_")
		{
			return;
		}
		else
		if ($k == "c_class")
		{
			return;
		}
		return parent::set_prop($k, $v);
	}
}

?>

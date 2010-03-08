<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property.aw,v 1.3 2007/12/06 14:33:03 kristo Exp $
// property.aw - Omadus 
/*

@classinfo syslog_type=ST_PROPERTY relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property int_ref type=checkbox ch_value=1 table=aw_properties
@caption Sisemine seos

@property ext_ref type=checkbox ch_value=1 table=aw_properties
@caption Väline seos

@property property_type type=select table=aw_properties
@caption Omaduse tüüp

@reltype REAL_PROPERTY value=1 clid=CL_PROPERTY_CHECKBOX
@caption Tegelik omadus

@tableinfo aw_properties index=aw_id master_table=objects master_index=brother_of

*/

class property extends class_base
{
	function property()
	{
		$this->init(array(
			"clid" => CL_PROPERTY
		));
	}

	////
	// !class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "property_type":
				$clinf = aw_ini_get("classes");
				$clinst = get_instance(CL_CLASS_DESIGNER);
				$clids = $clinst->elements;
				//$clids = array(CL_PROPERTY_CHECKBOX,CL_PROPERTY_TEXTBOX,CL_PROPERTY_SELECT);
				$prop["options"][0] = t("--vali--");
				foreach($clids as $clid)
				{
					$prop["options"][$clid] = $clinf[$clid]["name"];
				};
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
			case "property_type":
				$parent = $arr["obj_inst"]->id();

				// kõigepealt vaatame, kas mingi objekt juba eksisteerib
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_REAL_PROPERTY",
				));
				$first = reset($conns);
				if ($first)
				{
					$create = false;
					// object exists
					$ob = new object($first->to());
					if ($ob->class_id() == $prop["value"])
					{
						// the job is already done, do nothing
					}
					else
					{
						$ob->delete();
						$create = true;
					};
				}
				else
				{
					$create = true;
				};

				if (empty($prop["value"]))
				{
					$create = false;
				};

				if ($create)
				{
					// object does not exist
					$n = new object();
					$n->set_parent($parent);
					$n->set_class_id($prop["value"]);
					$n->set_name($arr["obj_inst"]->name());
					$n->save();

					$arr["obj_inst"]->connect(array(
						"to" => $n->id(),
						"reltype" => RELTYPE_REAL_PROPERTY,
					));


				}
				// now .. remove the bloody thing
				// now, if a type is chosen, then I have to create an object inside
				// THIS object, with the correct type

				// I need to be able to change the type, and if I do, then I 
				// have to create another object and delete the old one

				break;

		}
		return $retval;
	}	


}
?>

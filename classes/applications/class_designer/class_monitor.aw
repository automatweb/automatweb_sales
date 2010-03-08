<?php
/*
H ANDLE_MESSAGE(MSG_STORAGE_SAVE, foobar)

@classinfo  maintainer=kristo
*/
/*
	this calls monitors name changes in property objects and alters all tables 
	It works like this:

	1. designer keeps a list of all registered property objects in its metainfo
	2. if an property object is altered, then this class catches the "save" message
	3. then it tries to find the correct designer
	4. now the name of the property object and that in the designer is compared ..
	5. if they are different, then an "alter" sql clause will be created and executed

*/
class class_monitor
{

	function class_monitor()
	{
		//print "calling init";
	} 

	function foobar($arr) 
	{
		$o = new object($arr["oid"]);
		if ($o->class_id() != CL_PROPERTY)
		{
			return false;
		};

		$designer = $this->_get_designer($o);
		if (!$designer)
		{
			return false;
		};

		$designer_obj = new object($designer);
		$reg_els = $designer_obj->meta("registered_elements");
		$prop_id = $o->id();
		$alter = false;
		$cfgu = get_instance("cfg/cfgutils");
		$el_name = $this->gen_valid_id($o->name());
		// I'll probably have to check for other changes too
		if (empty($reg_els[$prop_id]) || $reg_els[$prop_id] != $el_name)
		{
			$alter = true;
			$designer_inst = $designer_obj->instance();
			$designer_inst->gen_alter_sql(array("id" => $designer,"change_table" => true));
		};
		//$reg_els[$prop_id] = $o->name();
		//$designer_obj->set_meta("registered_elements",$reg_els);
		//$designer_obj->save();
	}

	function _get_designer($o)
	{
		$pt = $o->path();
		foreach($pt as $p)
		{
			if ($p->class_id() == CL_CLASS_DESIGNER)
			{
				return $p;
			}
		}
		return NULL;
	}



};
?>

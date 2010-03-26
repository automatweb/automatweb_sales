<?php
/*
@classinfo syslog_type=ST_CFGCONTROLLER relationmgr=yes maintainer=kristo allow_rte=3

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property formula richtext=1 type=textarea rows=20 cols=80
@caption Valem

@property errmsg type=textbox
@caption Veateade
@comment Kuvatakse, kui kontroller blokeerib sisestuse

property show_error type=checkbox ch_value=1
caption Kas n&auml;itamise kontroller n&auml;itab elemendi asemel veateadet? 

property only_warn type=checkbox ch_value=1
caption Ainult hoiatus

property error_in_popup type=checkbox ch_value=1
caption Veateade popupis 

@groupinfo transl caption=T&otilde;lgi
@default group=transl
	
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

*/

class cfgcontroller extends class_base
{
	function cfgcontroller()
	{
		$this->init(array(
			"tpldir" => "cfg/cfgcontroller",
			"clid" => CL_CFGCONTROLLER
		));

		$this->trans_props = array(
			"formula", "errmsg"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		
		switch($prop["name"])
		{
			
		};
		return $retval;
	}
	
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function callback_mod_tab($arr)
	{
		$trc = aw_ini_get("user_interface.trans_classes");
		
		if ($arr["id"] == "transl" && (aw_ini_get("user_interface.content_trans") != 1 && !$trc[$this->clid]))
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
	
	/** runs the controller given
		@attrib api=1

		@param controller_oid required type=int
			OID of the controller to run

		@param obj_id required type=int
			OID of the object to run the controller for

		@param prop required type=array
			Data for the property to check

		@param request required type=array
			Array of name=>value pairs that come from the http request currently in progress

		@param entry required type=array
			Data to pass to the controller

		@param obj_inst required type=object
			The object the controller should be run on

		@errors
			error is thrown if the controller object given does not exist
	
		@returns
			the value that the controller sets to the variable $retval

		@examples
			$ctr = obj(59);	
			$object_to_run_on = obj(100);
			$ctr_instance = $crt->instance();
			$prop = array("name" => "whatever");
			echo "the controller said ".$ctr_instance->check_property($ctr->id(), $object_to_run_on->id(), $prop, $_GET, array("a" => "b"), $object_to_run_on);

			// prints whatever the controller assigned to $retval
	**/
	function check_property($controller_oid, $obj_id, &$prop, &$request, &$entry, $obj_inst)
	{
		$retval = PROP_OK;
		if (!is_oid($controller_oid))
		{
			return;
		}
		$controller_inst = &obj($controller_oid);
		eval($controller_inst->trans_get_val("formula"));
		return $retval;
	}
}
?>
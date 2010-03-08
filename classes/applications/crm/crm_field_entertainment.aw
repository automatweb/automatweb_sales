<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_field_entertainment.aw,v 1.3 2007/12/06 14:33:17 kristo Exp $
// crm_field_entertainment.aw - Meelelahutusettev&otilde;te (valdkond) 
/*

@classinfo syslog_type=ST_CRM_FIELD_ENTERTAINMENT no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property num_places type=textbox size=6
	@caption Kohtade arv

@default group=cedit

	@property phone_id type=relmanager reltype=RELTYPE_PHONE props=name override_parent=this
	@caption Telefon

	@property telefax_id type=relmanager reltype=RELTYPE_TELEFAX props=name override_parent=this
	@caption Faks

	@property email_id type=relmanager reltype=RELTYPE_EMAIL props=mail override_parent=this
	@caption E-posti aadressid

@default group=images

	@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h,new_w_big,new_h_big,comment table_fields=name,ord table_edit_fields=ord override_parent=this direct_links=1 
	@caption Pildid

@groupinfo cedit caption="Kontaktandmed" 
@groupinfo images caption="Pildid" submit=no

@reltype EMAIL value=1 clid=CL_ML_MEMBER
@caption E-post

@reltype PHONE value=2 clid=CL_CRM_PHONE
@caption Telefon

@reltype TELEFAX value=3 clid=CL_CRM_PHONE
@caption Fax

@reltype IMAGE value=4 clid=CL_IMAGE
@caption Pilt

*/

class crm_field_entertainment extends class_base
{
	function crm_field_entertainment()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_field_entertainment",
			"clid" => CL_CRM_FIELD_ENTERTAINMENT
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//

		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>

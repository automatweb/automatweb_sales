<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/scm/location.aw,v 1.8 2007/12/06 14:34:06 kristo Exp $
// location.aw - Asukoht 
/*

@classinfo syslog_type=ST_LOCATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property address type=relpicker reltype=RELTYPE_ADDRESS
@caption Aadress

@property owner type=relpicker reltype=RELTYPE_OWNER
@caption Omanik

@property chairman type=relpicker reltype=RELTYPE_CHAIRMAN
@caption Juhataja

@property contact_person type=relpicker reltype=RELTYPE_CONTACT_PERSON
@caption Kontaktisik

@property phone type=relpicker reltype=RELTYPE_PHONE
@caption Telefon

@property fax type=relpicker reltype=RELTYPE_FAX
@caption Faks

@property web type=relpicker reltype=RELTYPE_WEB
@caption Veebiaadress

@property email type=relpicker reltype=RELTYPE_EMAIL
@caption E-posti aadress

@property map type=relpicker reltype=RELTYPE_MAP
@caption Asukohakaart

@property photo type=relpicker reltype=RELTYPE_PHOTO
@caption Foto kohast

@property open_times type=textarea
@caption Lahtioleku ajad

@property prices type=textarea
@caption Hinnad

@property bank_payment type=relpicker reltype=RELTYPE_BANK_PAYMENT
@caption Pangamakse

@property short_description type=textbox
@caption L&uuml;hikirjeldus

@property description type=textarea
@caption Kirjeldus

@property add_text type=textarea
@caption Lisaandmed

@groupinfo images caption="Pildid"
	@property images type=releditor mode=manager no_caption=1 group=images reltype=RELTYPE_IMAGE props=ord,file table_fields=ord,file table_edit_fields=ord
	@caption Pildid

@groupinfo accommondation caption="Majutus"
	@property single_count type=textbox group=accommondation
	@caption &Uuml;hekohaliste arv

	@property double_count type=textbox group=accommondation
	@caption Kahekohaliste arv

	@property suite_count type=textbox group=accommondation
	@caption Sviitide arv

	@property rooms_description type=textarea group=accommondation
	@caption Ruumide kirjeldus

@reltype MAP value=1 clid=CL_IMAGE
@caption Kaart

@reltype PHOTO value=2 clid=CL_IMAGE
@caption Foto

@reltype ADDRESS value=3 clid=CL_CRM_ADDRESS
@caption Aadress

@reltype BANK_PAYMENT value=4 clid=CL_BANK_PAYMENT
@caption Pangalink

@reltype IMAGE value=5 clid=CL_IMAGE
@caption Pilt

@reltype OWNER value=6 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Omanik

@reltype PHONE value=7 clid=CL_CRM_PHONE
@caption Telefon

@reltype FAX value=8 clid=CL_CRM_PHONE
@caption Faks

@reltype WEB value=9 clid=CL_EXTLINK
@caption Veebiaadress

@reltype EMAIL value=10 clid=CL_ML_MEMBER
@caption E-post

@reltype CONTACT_PERSON value=11 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype CHAIRMAN value=12 clid=CL_CRM_PERSON
@caption Juhataja
*/

class location extends class_base
{
	function location()
	{
		$this->init(array(
			"tpldir" => "applications/scm/location",
			"clid" => CL_LOCATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "img_tb":
				$tb = $prop["vcl_inst"];
				$tb->add_button(array(
					"name" => "add_img",
					"tooltip" => t("tooltip"),
					"url" => "",
				));
				break;
			case "img_t":
				$t = $prop["vcl_inst"];
				$t->define_chooser(array(
					"name" => "sel",
					"field" => "img",
				));
				$t->define_field(array(
					"name" => "jrk",
				));
				break;
		};
		return $retval;
	}
	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_locations()
	{
		$list = new object_list(array(
			"class_id" => CL_LOCATION,
		));
		return $list->arr();
	}

	function add_location($arr = array())
	{
		$obj = obj();
		$obj->set_parent($arr["parent"]);
		$obj->set_class_id(CL_LOCATION);
		$obj->set_name($arr["name"]);
		$obj->set_prop("address", $arr["address"]);
		$obj->set_prop("map", $arr["map"]);
		$obj->set_prop("photo", $arr["photo"]);
		$oid = $obj->save_new();
		return $oid;
	}
	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
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

	function get_images($oid)
	{
		if(!is_oid($oid))
		{
			return array();
		}
		$o = obj($oid);
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_IMAGE",
		));
		foreach($conns as $conn)
		{
			$ret[] = $conn->to();
		}
		return $ret;
	}

	function get_add_info($oid)
	{
		if(!$this->can("view", $oid))
		{
			return "";
		}
		$o = obj($oid);
		$i = $o->prop("add_text");
		$ap = get_instance("alias_parser");
		$ap->parse_oo_aliases($oid, &$i);
		return $i;
	}
}
?>

<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/geoinfo/geoinfo_data.aw,v 1.5 2008/02/28 13:53:47 robert Exp $
// geoinfo_data.aw - Geoinfo andmed 
/*

@classinfo syslog_type=ST_GEOINFO_DATA relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=data

	@property name type=textbox table=objects
	@caption Nimi
	
	@property obj_oid type=textbox field=meta method=serialize
	@caption Objekti ID

	@property coord_y type=textbox field=meta method=serialize
	@caption Pikkuskoordinaat

	@property coord_x type=textbox field=meta method=serialize
	@caption Laiuskoordinaat

	@property address type=textbox field=meta method=serialize
	@caption Aadress

@default group=usert

	@property usertf1 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 1

	@property userta1 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 1

	@property usertf2 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 2

	@property userta2 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 2

	@property usertf3 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 3

	@property userta3 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 3

	@property usertf4 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 4

	@property userta4 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 4

	@property usertf5 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 5

	@property userta5 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 5

	@property usertf6 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 6

	@property userta6 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 6

	@property usertf7 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 7

	@property userta7 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 7

	@property usertf8 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 8

	@property userta8 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 8

	@property usertf9 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 9

	@property userta9 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 9

	@property usertf10 type=textbox field=meta method=serialize
	@caption Kasutaja defineeritud v&auml;li 10

	@property userta10 type=textarea cols=46 rows=3 field=meta method=serialize
	@caption Kasutaja defineeritud kast 10

@default group=pm_styles
	
	@property icon_t type=text store=no

	@property noicon type=checkbox ch_value=1 field=meta method=serialize
	@caption Ikooni ei kuvata

	@property icon_style type=relpicker reltype=RELTYPE_ICON store=connect field=meta method=serialize
	@caption Ikooni stiil
	
	@property icon_color type=colorpicker field=meta method=serialize default=ffffff
	@caption Ikooni värv

	@property icon_transp type=textbox size=3 field=meta method=serialize default=255
	@caption Ikooni l&auml;bipaistvus (0-255)

	@property icon_size type=textbox field=meta method=serialize size=3 default=1
	@caption Ikooni suurus

	@property label_t type=text store=no

	@property label_color type=colorpicker field=meta method=serialize default=ffffff
	@caption Sildi värv

	@property label_transp type=textbox size=3 field=meta method=serialize default=255
	@caption Sildi l&auml;bipaistvus (0-255)

	@property label_size type=textbox field=meta method=serialize default=1
	@caption Sildi suurus

@default group=pm_desc

	@property desc1 type=textarea cols=50 rows=20 field=meta method=serialize
	@caption Kirjeldus 1

	@property desc2 type=textarea cols=50 rows=20 field=meta method=serialize
	@caption Kirjeldus 2

@default group=pm_view

	@property view_t type=text store=no

	@property view_range type=textbox field=meta method=serialize size=3 default=0
	@caption Vaataja kaugus maapinnast (m)

	@property view_heading type=textbox field=meta method=serialize size=3 default=0
	@caption Suund (0-360 kraadi)

	@property view_tilt type=textbox field=meta method=serialize size=3 default=0
	@caption Vaatenurk maapinna suhtes (0-360 kraadi)

	@property height_t type=text store=no
	
	@property icon_height type=textbox field=meta method=serialize size=3 default=0
	@caption Ikooni k&otilde;rgus maapinnast (m)

@groupinfo data caption=Andmed parent=general
@groupinfo usert caption="Kasutaja defineeritud" parent=general

@groupinfo placemark caption=Kohapunkt
	@groupinfo pm_styles caption=Stiilid parent=placemark
	@groupinfo pm_desc caption=Kirjeldus parent=placemark
	@groupinfo pm_view caption=Vaade parent=placemark

@reltype ICON value=1 clid=CL_IMAGE
@caption Ikooni stiil

*/

class geoinfo_data extends class_base
{
	const AW_CLID = 1374;

	function geoinfo_data()
	{
		$this->init(array(
			"tpldir" => "applications/geoinfo/geoinfo_data",
			"clid" => CL_GEOINFO_DATA
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "icon_t":
				$prop["value"] = t("Ikoon");
				break;
			case "label_t":
				$prop["value"] = t("Silt");
				break;
			case "height_t":
				$prop["value"] = t("K&otilde;rgus");
				break;
			case "view_t":
				$prop["value"] = t("Vaade");
				break;
		};
		$p = obj($arr["obj_inst"]->parent());
		$oid = $arr["obj_inst"]->prop("obj_oid");
		if($p->class_id() == CL_GEOINFO_MANAGER && is_oid($oid))
		{
			$o = obj($oid);
			$clid = $o->class_id();
			$rels = $p->meta("rels");
			if($field = $rels["cl".$clid]["props"][$prop["name"]])
			{
				$prop_list = $o->get_property_list();
				$ptype = $prop_list[$field]["type"];
				$objprop = $o->prop($field);
				if($ptype == "chooser" || $ptype == "select")
				{
					$i = get_instance($o->class_id());
					$pr = array("name" => $field);
					$i->get_property(array("request" => array(), "obj_inst" => $o, "prop" => &$pr));
					$objprop = $pr["options"][$objprop];
				}
				elseif(is_oid($objprop) && !strlen(strpos($ptype,"text")))
				{
					$propobj = obj($objprop);
					$objprop = $propobj->name();
				}
				$prop["value"] = $objprop;
				$prop["type"] = "text";
			}
		}
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
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
}
?>

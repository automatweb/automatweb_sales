<?php
// scm_location.aw - Toimumiskoht
/*

@classinfo syslog_type=ST_SCM_LOCATION relationmgr=yes no_status=1 prop_cb=1 maintainer=tarvo
@tableinfo scm_location index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

@property comment type=textarea
@caption Kirjeldus

@property loc_path type=textarea table=scm_location field=location_path
@caption Kohale saab

@property address type=relpicker table=scm_location field=address reltype=RELTYPE_ADDRESS
@caption Aadress

@property map_url type=textbox table=scm_location field=map_url
@caption Kaart

//seda peab veel palju muutma
@property map type=relpicker table=scm_location field=map reltype=RELTYPE_MAP
@caption Kaardi pilt

@property photo type=relpicker table=scm_location field=photo reltype=RELTYPE_PHOTO
@caption Foto kohast

@property owner type=relpicker table=scm_location field=owner reltype=RELTYPE_OWNER
@caption Omanik

@property make_copy type=chooser multiple=1 field=meta method=serialize
@caption Tee koopia

@property usercheckbox1 type=checkbox table=scm_location field=usercheckbox1 ch_value=1
@caption User-defined checkbox 1

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi



@reltype MAP value=1 clid=CL_IMAGE
@caption Kaart

@reltype PHOTO value=2 clid=CL_IMAGE
@caption Foto

@reltype ADDRESS value=3 clid=CL_CRM_ADDRESS
@caption Aadress

@reltype OWNER value=4 clid=CL_CRM_COMPANY
@caption Aadress

*/

class scm_location extends class_base
{
	function scm_location()
	{
		$this->init(array(
			"tpldir" => "applications/scm/scm_location",
			"clid" => CL_SCM_LOCATION
		));
		$this->trans_props = array(
			"name", "loc_path", "description"
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
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}


	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
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

	function do_db_upgrade($table, $field, $query, $error)
	{
		if (empty($field))
		{
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,
				address int
			)');
			return true;
		}


		switch ($field)
		{
			case 'address':
			case 'map':
			case 'photo':
			case 'owner':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'map_url':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
			case 'location_path':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;

			case "usercheckbox1":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "int(1)"
				));
				return true;
		}
		return false;
	}

	/**
	@attrib name=autocomplete_location params=name all_args=1
	**/
	function autocomplete_location($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$ol = new object_list(array(
			"class_id" => CL_SCM_LOCATION,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 500,
		));
		$autocomplete_options = $ol->names();
		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}

		$autocomplete_options = array_unique($autocomplete_options);
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	/**
	@attrib name=location_data params=name all_args=1
	**/
	function location_data($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_SCM_LOCATION,
			"name" => $arr["name"],
			"lang_id" => array(),
			"limit" => 1,
		));
		$d = array(
			"country" => "",
			"county" => "",
			"city" => "",
			"address" => "",
		);
		if($ol->count() > 0){
			$o = $ol->begin();
			if($this->can("view", $o->address))
			{
				$address = obj($o->address);
				$d["country"] = $address->riik;
				$d["county"] = $address->maakond;
				$d["city"] = $address->linn;
				$d["address"] = $address->aadress;
			}
		}
		die(json_encode($d));
	}

}
?>

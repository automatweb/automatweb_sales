<?php
/*
@classinfo syslog_type=ST_AW_SERVER_ENTRY relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_server_list master_index=brother_of master_table=objects index=aw_oid

@default table=aw_server_list
@default group=general

	@layout top type=vbox closeable=1 area_caption=Andmed

		@layout top_fix type=hbox parent=top width=50%:50%

			@layout top_data type=vbox parent=top_fix

				@property server_id type=textbox parent=top_data captionside=top field=id
				@caption Serveri ID

				@property name type=textbox parent=top_data captionside=top
				@caption Nimi

				@property ip type=textbox parent=top_data captionside=top
				@caption IP

				@property sl type=text parent=top_data captionside=top store=no
				@caption Saidid

			@layout top_comm type=vbox parent=top_fix

				@property comment type=textarea rows=10 cols=80  parent=top_comm captionside=top
				@caption Kommentaar

	@layout bottom_split type=hbox

		@layout customer type=vbox closeable=1 area_caption=Klient parent=bottom_split

			@property customer type=relpicker reltype=RELTYPE_CUSTOMER field=aw_customer parent=customer
			@caption Klient

			@property customer_contact type=relpicker reltype=RELTYPE_CONTACT field=aw_contact parent=customer
			@caption Kontaktisik

			@property customer_info type=text store=no parent=customer


		@layout manager type=vbox closeable=1 area_caption=Haldaja parent=bottom_split

			@property manager type=relpicker reltype=RELTYPE_MANAGER field=aw_manager parent=manager
			@caption Serveri haldaja

			@property manager_contact type=relpicker reltype=RELTYPE_MANAGER_CONTACT field=aw_manager_contact parent=manager
			@caption Serveri haldaja kontaktisik

			@property manager_info type=text store=no parent=manager


@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype CONTACT value=2 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype MANAGER value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Haldaja

@reltype MANAGER_CONTACT value=4 clid=CL_CRM_PERSON
@caption Haldaja kontaktisik
*/

class aw_server_entry extends class_base
{
	function aw_server_entry()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/aw_server_entry",
			"clid" => CL_AW_SERVER_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_aw_server_entry(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_customer":
			case "aw_contact":
			case "aw_manager":
			case "aw_manager_contact":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	private function _fix_server_oids()
	{
		$this->db_query("SELECT * FROM aw_site_list WHERE site_used > 0");
		while ($row = $this->db_next())
		{
			$this->save_handle();
//echo dbg::dump($row);
			$srv = $row["server_id"];
			$srv_oid = $this->db_fetch_field("SELECT aw_oid FROM aw_server_list WHERE id = $srv", "aw_oid");
			if ($srv_oid != $row["server_oid"])
			{
echo "update $srv => $srv_oid <br>";
				$this->db_query("UPDATE aw_site_list SET server_oid = $srv_oid WHERE server_id = $srv");
			}
			$this->restore_handle();
		}
		die("d");
	}

	function _get_sl($arr)
	{	
		$ol = new object_list(array(
			"class_id" => CL_AW_SITE_ENTRY,
			"server_oid" => $arr["obj_inst"]->id(),
			"site_id" => array(),
			"lang_id" => array()
		));
		$arr["prop"]["value"] = join(", ", array_map(array(&$this, "__format_sl"), $ol->arr()));
	}

	function __format_sl($s)
	{
		return html::obj_change_url($s->id());
	}

	function _get_manager_info($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->manager_contact))
		{
			return PROP_IGNORE;
		}
		$o = obj($arr["obj_inst"]->manager_contact);
		$arr["prop"]["value"] = sprintf(t("Telefon: %s , Email: %s"), $o->prop("phone.name"), $o->prop("email.mail"));
	}

	function _get_customer_info($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->customer_contact))
		{
			return PROP_IGNORE;
		}
		$o = obj($arr["obj_inst"]->customer_contact);
		$arr["prop"]["value"] = sprintf(t("Telefon: %s , Email: %s"), $o->prop("phone.name"), $o->prop("email.mail"));
	}
}

?>

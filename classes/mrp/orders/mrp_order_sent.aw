<?php
/*
@classinfo syslog_type=ST_MRP_ORDER_SENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_mrp_order_sent master_index=brother_of master_table=objects index=aw_oid

@default table=aw_mrp_order_sent
@default group=general

	@property oc type=hidden field=aw_oc
	@caption Tellimiskeskkond

	@property sent_when type=datetime_select field=aw_sent_when default=-1
	@caption Millal saadetud

	@property send_to_mail type=textbox field=aw_send_to_mail
	@caption Kellele aadress

	@property send_to_name type=textbox field=aw_send_to_name
	@caption Kellele nimi

	@property send_to_cc type=textbox field=aw_send_to_cc
	@caption CC

	@property send_subject type=textbox field=aw_send_subject
	@caption Teema

	@property send_from_adr type=textbox field=aw_send_from_adr
	@caption Kellelt aadress

	@property send_from_name type=textbox field=aw_send_from_name
	@caption Kellelt nimi

	@property legend type=text store=no 
	@caption Legend

	@property send_content type=textarea rows=20 cols=50 field=aw_send_content
	@caption Sisu

	@property attached_files type=table store=no 
	@caption Saadetavad failid

	@property do_send type=checkbox ch_value=1 field=aw_do_send
	@caption Saada

@reltype FILE value=1 clid=CL_FILE
@caption Fail
*/

class mrp_order_sent extends class_base
{
	function mrp_order_sent()
	{
		$this->init(array(
			"tpldir" => "mrp/orders/mrp_order_sent",
			"clid" => CL_MRP_ORDER_SENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if ($arr["new"] && $this->can("view", ifset($arr["request"], "oc")) && $prop["name"] != "name")
		{
			$oc = obj($arr["request"]["oc"]);
			if ($this->can("view", $oc->mail_template))
			{
				$mt = obj($oc->mail_template);
				$arr["prop"]["value"] = $mt->prop($prop["name"]);
			}
		}

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
		$arr["oc"] = $_GET["oc"];
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
			$this->db_query("CREATE TABLE aw_mrp_order_sent(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_oc":
			case "aw_sent_when":
			case "aw_do_send":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_send_to_mail":
			case "aw_send_to_cc":
			case "aw_send_to_name":
			case "aw_send_subject":
			case "aw_send_from_adr":
			case "aw_send_from_name":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_send_content":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumtext"
				));
				return true;
		}
	}

	function _get_legend($arr)
	{
		$arr["prop"]["value"] = 
			t("Sisus kasutatavad m&auml;rgid:")."<br>".
			t("#weblink# - veebi url pakkumise vaatamiseks")."<br>".
			t("#klient# - kliendi organisatsiooni nimi")."<br>".
			t("#kontakt# - kliendi kontaktisiku nimi")."<br>";
	}

	function _get_sent_when($arr)
	{
		if ($arr["prop"]["value"] == -1 || !$arr["obj_inst"]->do_send)
		{
			return PROP_IGNORE;
		}
	}

	function _set_oc($arr)
	{
		if (!$arr["prop"]["value"] && !empty($arr["request"]["oc"]))
		{
			$arr["prop"]["value"] = $arr["request"]["oc"];
		}
	}

	private function _init_attached_files_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center"
		));
	}

	function _get_attached_files($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_attached_files_table($t);

		// get file from url
		$files = array();
		if ($arr["new"])
		{
			if ($this->can("view", ifset($arr["request"], "file")))
			{
				$files[] = obj($arr["request"]["file"]);
			}
		}
		else
		{
			$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FILE")));
			$files = $ol->arr();
		}

		$sa = $arr["obj_inst"]->meta("selected_files");

		foreach($files as $file)
		{
			$t->define_data(array(
				"name" => html::href(array(
					"url" => file::get_url($file->id(), $file->name()),
					"caption" => $file->name()
				)),
				"select" => html::checkbox(array(
					"name" => "sel[]",
					"value" => $file->id(),
					"checked" => $arr["new"] ? true : ifset($sa, $file->id())
				))
			));
		}
	}

	function _set_attached_files($arr)
	{
		$sa = array();
		foreach(safe_array($arr["request"]["sel"]) as $item)
		{
			$sa[$item] = $item;
			$arr["obj_inst"]->connect(array(
				"to" => $item,
				"reltype" => "RELTYPE_FILE"
			));
		}
		$arr["obj_inst"]->set_meta("selected_files", $sa);
	}

	function callback_post_save($arr)
	{
		if ($arr["obj_inst"]->do_send && $arr["obj_inst"]->sent_when < 10)
		{
			$this->do_send_offer($arr["obj_inst"]);
		}
	}

	function _get_do_send($arr)
	{
		if ($arr["obj_inst"]->sent_when > 10)
		{
			return PROP_IGNORE;
		}
	}

	public function do_send_offer($o)
	{
		$awm = get_instance("protocols/mail/aw_mail");
		$awm->create_message(array(
			"froma" => $o->send_from_adr,
			"fromn" => $o->send_from_name,
			"subject" => $o->send_subject,
			"To" => $o->send_to_name." <".$o->send_to_mail.">",
			"body" => $this->_format_content($o),
			"cc" => $o->send_to_cc
                ));
		$mimeregistry = get_instance("core/aw_mime_types");
		foreach($o->connections_from(array("type" => "RELTYPE_FILE")) as $f)
		{
			$fo = $f->to();
			$awm->fattach(array(
				"path" => $fo->prop("file"),
				"contenttype"=> $mimeregistry->type_for_file($fo->name()),
				"name" => $fo->name()
			));
		}

		$awm->htmlbodyattach(array(
			"data" => nl2br($this->_format_content($o))
		));	

		$awm->gen_mail();
		$awm->clean();

		$o->sent_when = time();
		$o->save();
	}

	function _get_offer($o)
	{
		$offer = $o->connections_to(array("from.class_id" => CL_MRP_ORDER_PRINT));
		$offer = reset($offer);
		$offer = $offer->from();
		return $offer;
	}

	private function _format_content($o)
	{
		$offer = $this->_get_offer($o);

		$content = $o->send_content;
		$content = str_replace("#klient#", $offer->get_customer_name(), $content);
		$content = str_replace("#kontakt#", $offer->get_contact_name(), $content);
		$content = str_replace("#weblink#", obj_link($o), $content);
		return $content;
	}

	function _get_send_to_mail($arr)
	{
		if ($arr["new"] && $this->can("view", $arr["request"]["alias_to"]))
		{
			$offer = obj($arr["request"]["alias_to"]);
			if ($offer->class_id() == CL_MRP_ORDER_PRINT)
			{
				$arr["prop"]["value"] = $offer->get_contact_mail();
			}
		}
	}

	function _get_send_to_name($arr)
	{
		if ($arr["new"])
		{
			$offer = obj($arr["request"]["alias_to"]);
			if ($offer->class_id() == CL_MRP_ORDER_PRINT)
			{
				$arr["prop"]["value"] = $offer->get_contact_name();
			}
		}
	}

	function _agreed($o)
	{
		$this->read_template("agreed.tpl");
		return $this->parse();
	}

	public function request_execute($o)
	{
		if ($_GET["agreed"] == 1)
		{
			return $this->_agreed($o);
		}
		$this->read_template("web_display.tpl");
		
		$offer = $this->_get_offer($o);
		$this->vars(array(
			"offer_content" => $offer->instance()->generate_preview($offer),
			"reforb" => $this->mk_reforb("agree_to_offer", array("id" => $o->id(), "ru" => aw_url_change_var("agreed", 1)))
		));
		return $this->parse();
	}

	/**
		@attrib name=agree_to_offer
		@param id required
		@param ru required
	**/
	function agree_to_offer($arr)
	{
		$offer = $this->_get_offer(obj($arr["id"]));
		$offer->set_prop("state", 4);	// accepted
		aw_disable_acl();
		$offer->save();
		aw_restore_acl();
		return $arr["ru"];
	}
}

?>

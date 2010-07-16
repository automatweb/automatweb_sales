<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_SENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_offer_sent master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer_sent
@default group=general

	@property offer type=hidden field=aw_offer
	@caption Pakkumine

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

	@property do_send type=checkbox ch_value=1 field=aw_do_send
	@caption Saada

@reltype FILE value=1 clid=CL_FILE
@caption Fail
*/

class crm_offer_sent extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer_sent",
			"clid" => CL_CRM_OFFER_SENT
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _get_legend($arr)
	{
		$arr["prop"]["value"] = 
			t("Sisus kasutatavad m&auml;rgid:")."<br>".
			t("#confirmation_url# - URL pakkumise vaatamiseks ja kinnitamiseks")."<br>".
			t("#client# - kliendi nimi")."<br>";
	}

	function _get_sent_when($arr)
	{
		if ($arr["prop"]["value"] == -1 || !$arr["obj_inst"]->do_send)
		{
			return PROP_IGNORE;
		}
	}

	function _get_offer($arr)
	{
		$arr["prop"]["value"] = automatweb::$request->arg("offer");
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

		$awm->htmlbodyattach(array(
			"data" => nl2br($this->_format_content($o))
		));	

		$awm->gen_mail();
		$awm->clean();

		$o->sent_when = time();
		$o->save();
	}

	private function _format_content($o)
	{
		$offer = $o->offer();

		$content = $o->send_content;
		$content = str_replace("#client#", $offer->prop("customer.name"), $content);
		$content = str_replace("#confirmation_url#", $o->get_confirmation_url(), $content);
		return $content;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_offer_sent" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_offer_sent` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "aw_sent_when":
					case "aw_do_send":
					case "aw_offer":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "int"
						));
						$r = true;
						break;

					case "aw_send_to_mail":
					case "aw_send_to_cc":
					case "aw_send_to_name":
					case "aw_send_subject":
					case "aw_send_from_adr":
					case "aw_send_from_name":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "varchar(255)"
						));
						$r = true;
						break;

					case "aw_send_content":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "mediumtext"
						));
						$r = true;
						break;
				}
			}
		}

		return $r;
	}
}

?>

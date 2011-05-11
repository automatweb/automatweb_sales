<?php
/*
@classinfo syslog_type=ST_EESTI_EHITUSTURG relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_eesti_ehitusturg master_index=brother_of master_table=objects index=aw_oid

@default table=aw_eesti_ehitusturg
@default group=general

@property url type=textbox field=aw_url
@caption URL

@property browser type=text store=no
@caption Sirvi Eesti Ehitusturgu

@property email_templates type=relpicker reltype=RELTYPE_EMAIL_TEMPLATE multiple=1 store=connect
@caption E-kirjamallid

###

@reltype EMAIL_TEMPLATE value=1 clid=CL_MESSAGE_TEMPLATE
@caption E-kirjamall

*/

class eesti_ehitusturg extends class_base
{
	public function eesti_ehitusturg()
	{
		$this->init(array(
			"tpldir" => "applications/first_contact/eesti_ehitusturg",
			"clid" => CL_EESTI_EHITUSTURG
		));
	}

	public function _get_url($arr)
	{
		if(empty($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] = "http://www.eesti-ehitusturg.ee/";
		}
	}

	public function _get_browser($arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("K&auml;ivita brauser"),
			"url" => $this->mk_my_orb("browser", array("id" => automatweb::$request->arg("id"))),
		));
	}

	/**
		@attrib name=browser all_args=1
		@param id required type=int acl=view
	**/
	public function browser($arr)
	{
		$html = obj($arr["id"])->get_browser_html();
		die($html);
	}

	public function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_eesti_ehitusturg(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_url":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}

	/**
		@attrib name=send_spam nologin=1
	**/
	public function send_spam()
	{
		return obj(automatweb::$request->arg("id"))->send_spam();
	}
}

?>

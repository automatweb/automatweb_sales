<?php
/*
@classinfo syslog_type=ST_SITE_DIFF relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_site_diff master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_diff

@default group=general

	@property url_1 type=textbox
	@caption Originaali URL

	@property url_2 type=textbox
	@caption V&otilde;rreldava URL

	@property email type=textbox
	@caption E-posti aadress, kuhu saata kiri, kui v&otilde;rdlemine on l&otilde;ppenud

	@property send_diff_links type=checkbox ch_value=1
	@caption Saada e-postiga ka diff-lingid.

	@property use_hack_diff type=checkbox ch_value=1
	@caption Hack diff

	@property start_diff type=text store=no
	@caption V&otilde;rdle

@groupinfo history caption="Ajalugu" submit=no
@default group=history

	@property histtlb type=toolbar no_caption=1 store=no

	@property histtbl type=table no_caption=1 store=no

*/

class site_diff extends class_base
{
	function site_diff()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_search/site_diff",
			"clid" => CL_SITE_DIFF
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

	private function diffs_oview(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		// Initialize
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "diff_id",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "url_1",
			"caption" => t("Originaal"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "url_2",
			"caption" => t("V&otilde;rreldav"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "count",
			"caption" => t("Erinevuste arv"),
			"align" => "center",
			"sortable" => true,
		));

		// Data
		foreach($arr["obj_inst"]->meta() as $k => $v)
		{
			if(substr($k, 0, 11) != "site_diffs_" || $v == NULL)
			{
				continue;
			}
			$t->define_data(array(
				"time" => get_lc_date(substr($k, 11))." ".date("H:i", substr($k, 11)),
				"url_1" => $v["url_1"],
				"url_2" => $v["url_2"],
				"count" => is_array($v["diffs"]) && count($v["diffs"]) > 0 ? html::href(array(
					"url" => aw_url_change_var("diff_id", $k),
					"caption" => count($v["diffs"]),
				)) : 0,
				"diff_id" => $k,
			));
		}

		$t->set_default_sortby("time");
	}

	function diffs_detail(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		// Initialize
		$t->define_field(array(
			"name" => "urlo",
			"caption" => t("Originaal"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "urlc",
			"caption" => t("V&otilde;rreldav"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("V&otilde;rdlemise aeg"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "diff_link",
			"caption" => t("Vaata erinevusi!"),
			"align" => "center",
			"sortable" => 1,
		));

		$data = $arr["obj_inst"]->meta($arr["request"]["diff_id"]);
		$purl_1 = parse_url($data["url_1"]);
		$hosto = $purl_1["host"];
		$purl_2 = parse_url($data["url_2"]);
		$hostc = $purl_2["host"];

		foreach($data["diffs"] as $v)
		{
			$t->define_data(array(
				"urlo" => html::href(array(
					"url" => $v["urlo"],
					"caption" => $v["urlo"],
				)),
				"urlc" => html::href(array(
					"url" => str_replace($hosto, $hostc, $v["urlo"]),
					"caption" => str_replace($hosto, $hostc, $v["urlo"]),
				)),
				"time" => date("d-m-Y H:i:s", $v["time"]),
				"diff_link" => html::href(array(
					"url" => $this->mk_my_orb("show_diff", array(
						"id" => $arr["obj_inst"]->id(),
						"url" => $data["url_1"],
						"url_diff" => $v["urlo"],
						"time" => $v["time"],
					)),
					"caption" => t("Vaata erinevusi!"),
				)),
			));
		}

		$t->set_default_sortby("urlo");
	}
	
	function _get_histtbl($arr)
	{
		if(strlen(trim($arr["request"]["diff_id"])) > 0)
		{
			return $this->diffs_detail($arr);
		}
		else
		{
			return $this->diffs_oview($arr);
		}
	}

	function _get_histtlb($arr)
	{
		if(strlen(trim($arr["request"]["diff_id"])) > 0)
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta valitud ajalugu"),
			"action" => "delete_history",
			"img" => "delete.gif",
		));
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
			$this->db_query("CREATE TABLE aw_site_diff(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "url_1":
			case "url_2":
			case "email":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "use_hack_diff":
			case "send_diff_links":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(1)"
				));
				return true;

		}
	}

	/**
		@attrib name=diff params=name

		@param id required type=oid
			The OID of the site_diff object,

	**/
	public function diff($arr)
	{
		return obj($arr["id"])->diff($arr);
	}

	/** Shows the result of UNIX diff.
		@attrib name=show_diff

		@param id required type=OID

		@param url required type=string

		@param url_diff required type=string

		@param time required type=timestamp

	**/
	public function show_diff($arr)
	{
		extract($arr);

		$hash = md5($url_diff.$time);
		$path = aw_ini_get("site_basedir")."/files/site_diff_{$hash}.txt";
		$c = file_get_contents($path);
		die(nl2br(htmlspecialchars($c)));
	}

	/**
		@attrib name=delete_history params=name

		@param id required type=id

		@param sel required type=array

	**/
	public function delete_history($arr)
	{
		return obj($arr["id"])->delete_history($arr);
	}
}

?>

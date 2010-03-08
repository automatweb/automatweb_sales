<?php
/*
@classinfo syslog_type=ST_CUSTOMER_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_customer_import master_index=brother_of master_table=objects index=aw_oid

@default table=aw_customer_import
@default group=general

	@property data_source type=select field=aw_data_source
	@caption Andmeallikas

	@property company type=relpicker reltype=RELTYPE_COMPANY field=aw_company
	@caption Organisatsioon kellele kliente importida

@default group=import_timing

	@layout timing_lay type=vbox area_caption=Impordi&nbsp;ajastus closeable=1

		@property timing type=releditor reltype=RELTYPE_REPEATER use_form=emb rel_id=first field=aw_timing parent=timing_lay
		@caption Impordi ajastus


@default group=import_status

	@layout stat_lay type=vbox area_caption=Impordi&nbsp;staatus closeable=1

		@property imp_status type=text store=no no_caption=1 parent=stat_lay


@groupinfo import_timing caption="Impordi ajastus"
@groupinfo import_status caption="Impordi staatus" submit=no


@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype REPEATER value=2 clid=CL_RECURRENCE
@caption Kordaja
*/

class customer_import extends class_base
{
	function customer_import()
	{
		$this->init(array(
			"tpldir" => "applications/customer_import/customer_import",
			"clid" => CL_CUSTOMER_IMPORT
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
			$this->db_query("CREATE TABLE aw_customer_import(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_data_source":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_company":
			case "aw_timing":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_data_source($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--")) + $this->make_keys(class_index::get_classes_by_interface("customer_import_datasource"));
	}

	function _get_imp_status($arr)
	{	
		$arr["prop"]["value"] = $this->_describe_import($arr["obj_inst"], "customer", "RELTYPE_PRICE_LISTS_REPEATER");
	}

	private function _describe_import($o, $type, $rt, $wh_id = null)
	{
		$t = "";
		if (($pid = $o->import_is_running($type, $wh_id)))
		{
			$full_stat = $o->full_import_status($type, $wh_id);
			$t = html::strong(t("Import k&auml;ib!"));
			$t .= "<br/>".sprintf(t("Staatus: %s, protsess: %s, andmeid t&ouml;&ouml;deldud %s andmeid kokku %s, algusaeg %s"), 
				self::name_for_status($full_stat[2]),
				$pid, 
				(int)$full_stat[4],
				(int)$full_stat[5],
				date("d.m.Y H:i:s", $full_stat[0])
			);

			if ($o->need_to_stop_now($type, $wh_id))
			{
				$t .= "<br/>".html::href(array(
					"url" => $this->mk_my_orb("reset_import", array("type" => $type, "wh_id" => $wh_id, "id" => $o->id(), "post_ru" => get_ru())),
					"caption" => t("Reset")
				));
			}
			else
			{
				$t .= "<br/>".html::href(array(
					"url" => $this->mk_my_orb("stop_import", array("type" => $type, "wh_id" => $wh_id, "id" => $o->id(), "post_ru" => get_ru())),
					"caption" => t("Peata kohe")
				));
			}
		}
		else
		{
			$rec = $o->get_first_obj_by_reltype($rt);
			if ($rec)
			{
				$ne = $rec->instance()->get_next_event(array("id" => $rec->id()));
				if ($ne > 10)
				{
					$t = sprintf(t("J&auml;rgmine import algab %s"), date("d.m.Y H:i", $ne));
				}
				else
				{
					$t = t("Impordi kordaja on l&otilde;ppenud!");
				}
			}
			else
			{
				$t = t("Impordile pole automaatset k&auml;ivitust m&auml;&auml;ratud!");
			}

			$t .= "<br/>".html::href(array(
				"url" => $this->mk_my_orb("do_".$type."_import", array("id" => $o->id(), "wh_id" => $wh_id, "post_ru" => get_ru())),
				"caption" => t("K&auml;ivita kohe")
			));
		}

		if (($prev = $o->get_import_log($type, $wh_id)))
		{
			$tb = new vcl_table();
			$tb->define_field(array(
				"caption" => t("Alustati"),
				"name" => "start",
				"align" => "center",
				"type" => "time",
				"format" => "d.m.Y H:i:s",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("L&otilde;petati"),
				"name" => "end",
				"align" => "center",
				"type" => "time",
				"format" => "d.m.Y H:i:s",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("Edukas"),
				"name" => "success",
				"align" => "center",
			));
			$tb->define_field(array(
				"caption" => t("Imporditud andmete arv"),
				"name" => "prod_count",
				"align" => "center",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("Kogu andmete arv"),
				"name" => "total",
				"align" => "center",
				"numeric" => 1
			));
			$tb->define_field(array(
				"caption" => t("L&otilde;petamise p&otilde;hjus"),
				"name" => "reason",
				"align" => "center",
			));

			foreach($prev as $entry)
			{
				$tb->define_data(array(
					"start" => $entry["full_status"][0],
					"end" => $entry["finish_tm"],
					"success" => $entry["success"] ? t("Jah") : t("Ei"),
					"prod_count" => $entry["full_status"][4],
					"total" => $entry["full_status"][5],
					"reason" => $entry["reason"]
				));
			}
			$tb->set_default_sortby("end");
			$tb->set_default_sorder("desc");
			$tb->sort_by();
			$tb->set_caption(t("Eelneva 10 impordi info"));
			$t .= "<br/>".$tb->get_html();
		}

		return $t;
	}

	/**
		@attrib name=reset_import
		@param id required 
		@param type required
		@param wh_id optional
		@param post_ru optional
	**/
	function reset_import($arr)
	{	
		$o = obj($arr["id"]);
		$o->reset_import($arr["type"], $arr["wh_id"]);
		return $arr["post_ru"];
	}

	function run_backgrounded($act, $id, $wh_id = null)
	{
		$url = $this->mk_my_orb("run_backgrounded", array("wh_id" => $wh_id, "act" => $act, "id" => $id));
		$url = str_replace("/automatweb", "", $url);
		$h = new http;
		$h->get($url);
		sleep(1);
	}

	/**
		@attrib name=stop_import
		@param type required
		@param wh_id optional
		@param id required
		@param post_ru optional
	**/
	function stop_import($arr)
	{
		$o = obj($arr["id"]);
		$o->stop_import($arr["type"], $arr["wh_id"]);
		return $arr["post_ru"];
	}

	/**
		@attrib name=run_backgrounded nologin="1"
		@param id required
		@param wh_id optional
		@param act required
	**/
	function do_run_bg($arr)
	{
		session_write_close();
		while(ob_get_level()) { ob_end_clean(); }

		// let the user continue with their business
		ignore_user_abort(1);
                header("Content-Type: image/gif");
                header("Content-Length: 43");
                header("Connection: close");
                echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==")."\n";
                flush();
		aw_set_exec_time(AW_LONG_PROCESS);

		$act = $arr["act"];
		$this->$act($arr["id"], $arr["wh_id"]);
		die("all done!");
	}

	/**
		@attrib name=do_customer_import
		@param id required type=int acl=view
		@param post_ru optional
	**/
	function do_customer_import($arr)
	{
		$this->run_backgrounded("real_customer_import", $arr["id"]);
		return $arr["post_ru"];
	}

	function resume_customer_import($id)
	{
		$o = obj($id);
		$o->resume_from_process_switch();
	}

	function real_customer_import($id)
	{
		$o = obj($id);
		$o->start_customer_import();
	}

	static function name_for_status($stat)
	{
		$lut = array(
			customer_import_datasource::STATE_STARTING => t("Alustamine"),
			customer_import_datasource::STATE_FETCH_CATEGORY_XML => t("Kategooriate XML laadimine"),
			customer_import_datasource::STATE_PROCESS_CATEGORY_XML => t("Kategooriate XML t&ouml;&ouml;tlemine"),
			customer_import_datasource::STATE_FETCH_CUSTOMER_XML => t("Klientide XML laadimine"),
			customer_import_datasource::STATE_PROCESS_CUSTOMER_XML => t("Klientide XML t&ouml;&ouml;tlemine"),
			customer_import_datasource::STATE_FETCH_PERSON_XML => t("Isikute XML laadimine"),
			customer_import_datasource::STATE_PROCESS_PERSON_XML => t("Isikute XML t&ouml;&ouml;tlemine"),
			customer_import_datasource::STATE_FETCH_USER_XML => t("Kasutajate XML laadimine"),
			customer_import_datasource::STATE_PROCESS_USER_XML => t("Kasutajate XML t&ouml;&ouml;tlemine"),
			customer_import_datasource::STATE_FINISHING => t("L&otilde;petamine")
		);
		return $lut[$stat];
	}
}


interface customer_import_datasource
{
	const STATE_STARTING = 0;
	const STATE_FETCH_CATEGORY_XML = 1;
	const STATE_PROCESS_CATEGORY_XML = 2;
	const STATE_FETCH_CUSTOMER_XML = 3;
	const STATE_PROCESS_CUSTOMER_XML = 4;
	const STATE_FETCH_PERSON_XML = 5;
	const STATE_PROCESS_PERSON_XML = 6;
	const STATE_FETCH_USER_XML = 7;
	const STATE_PROCESS_USER_XML = 8;
	const STATE_FINISHING = 9;

	public function get_category_list_xml();
	public function get_customer_list_xml();
	public function get_person_list_xml();
	public function get_user_list_xml();
}
?>

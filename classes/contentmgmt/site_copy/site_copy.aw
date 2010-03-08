<?php
/*
@classinfo syslog_type=ST_SITE_COPY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_site_copy master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_copy
@default group=general

	@property disk_usage type=text subtitle=1 store=no
	@caption Kettakasutus

	@property du_www type=text store=no
	@caption WWW

	@property du_db type=text store=no
	@caption DB

@groupinfo add_site caption="Lisa sait" submit=no
@default group=add_site

	@property url type=textbox store=no
	@caption URL

	@property email type=textbox store=no
	@caption Tellija e-post

	@property local_copy type=checkbox ch_value=1 store=no
	@caption Tehakse lokaalne koopia saidi files kausta
	@comment Saiti ei kopeerita devsaitide serverisse

	@property local_copy_prms type=chooser multiple=1 store=no
	@caption Koopia tehakse j&auml;rgnevast

	@property cvs_copy type=checkbox ch_value=1 store=no
	@caption Tehakse koopia CVS koodile

	@property submit type=submit
	@caption Alusta kopeerimist

@groupinfo todo caption="Pooleliolevad t&ouml;&ouml;d" submit=no
@default group=todo

	@property todo_tlb type=toolbar no_caption=1 store=no

	@property todo_tbl type=table no_caption=1 store=no

@groupinfo sites caption="Kopeeritud saidid" submit=no
@default group=sites

	@property sites_tlb type=toolbar no_caption=1 store=no

	@property sites_tbl type=table no_caption=1 store=no

@groupinfo settings caption="Seaded"
@default group=settings

	@groupinfo settings_general caption="&Uuml;ldised seaded" parent=settings
	@default group=settings_general

		@property default_mail type=textbox
		@caption Deafult e-postiaadress

		@property time_limit type=textbox
		@caption Ajalimiit

		@property apache_logs type=textbox
		@caption Apache'i logide kaust

		@property apache_vhosts type=textbox
		@caption Apache'i vhostide kaust

		@property cvs_user type=textbox
		@caption CVS kasutajanimi

		@property mysql_host type=textbox
		@caption MySQLi host

		@property mysql_root_password type=password
		@caption MySQLi root-kasutaja parool

		@property dir_sites type=relpicker reltype=RELTYPE_DIR_SITES store=connect
		@caption Saidi objektide kaust

	@groupinfo allowed_servers caption="Lubatud serverid" parent=settings
	@default group=allowed_servers

	@property as_tlb type=toolbar store=no no_caption=1

	@property as_tbl type=table store=no no_caption=1

##

@reltype DIR_SITES value=1 clid=CL_MENU
@caption Saidi objektide kaust


*/

class site_copy extends class_base
{
	function site_copy()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_copy/site_copy",
			"clid" => CL_SITE_COPY
		));
	}

	function get_property($arr)
	{
		if($arr["request"]["group"] == "add_site" && $arr["obj_inst"]->get_conf() === false)
		{
			header("Location: ".aw_url_change_var("group", "settings"));
		}

		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "time_limit":
				if(!isset($prop["value"]))
				{
					$prop["value"] = 60000;
				}
				break;

			case "apache_logs":
				if(!isset($prop["value"]))
				{
					$prop["value"] = "/www/logs/";
				}
				break;

			case "apache_vhosts":
				if(!isset($prop["value"]))
				{
					$prop["value"] = "/etc/httpd/vhosts/";
				}
				break;

			case "cvs_user":
				if(!isset($prop["value"]))
				{
					$prop["value"] = "instrumental";
				}
				break;

			case "mysql_host":
				if(!isset($prop["value"]))
				{
					$prop["value"] = "localhost";
				}
				break;

			case "mysql_root_password":
				if(!isset($prop["value"]))
				{
					$prop["value"] = "V33b1ar3ndu5";
				}
				break;

			case "du_www":
				$prop["value"] = shell_exec("df -h | grep 'www' | awk '".'{print $3 "/" $2 " " $5}'."'");
				break;
			
			case "du_db":
				$prop["value"] = shell_exec("df -h | grep '/db' | awk '".'{print $3 "/" $2 " " $5}'."'");
				break;

			case "url":
				if(!isset($prop["value"]))
				{
					$prop["value"] = t("http://");
				}
				break;

			case "local_copy_prms":
				$prop["options"] = array(
					"site" => t("Sait"),
					"code" => t("Kood"),
					"base" => t("Baas"),
				);
				if(!isset($prop["value"]))
				{
					$prop["value"] = $prop["options"];
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
			case "as_tbl":
				foreach($arr["request"]["allowed_servers"] as $ip)
				{					
					if(strlen(trim($ip)) > 0)
					{
						$as[] = $ip;
					}
				}
				$arr["obj_inst"]->set_meta("allowed_servers", $as);
				break;

			case "apache_logs":
			case "apache_vhosts":
				if(!is_dir($prop["value"]))
				{
					$prop["error"] = t("Sellist kausta ei eksisteeri!");
					$retval = PROP_FATAL_ERROR;
				}
				break;

			case "time_limit":
				if((int)$prop["value"] <= 0)
				{
					$prop["error"] = t("Ajalimiit peab olema positiivne arv!");
					$retval = PROP_FATAL_ERROR;
				}
				break;

			case "url":
				$retval = site_copy_obj::check_add_site_submit($arr);
				break;

			case "email":				
				if(!$this->check_email($arr))
				{
					$prop["error"] = t("Saidi kopeerimisel devsaitide serverisse peab olema m&auml;&auml;ratud ka e-postiaadress!");
					$retval = PROP_FATAL_ERROR;
				}
				break;
		}

		return $retval;
	}

	function _get_as_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "ip",
			"caption" => "Serveri IP",
			"align" => "center",
			"sortable" => 1,
			"sorting_field" => "ip_sort"
		));

		$t->define_data(array(
			"oid" => "new",
			"ip" => html::textbox(array(
				"name" => "allowed_servers[new]",
				"value" => "",
				"size" => 30
			)),
		));
		foreach(safe_array($arr["obj_inst"]->meta("allowed_servers")) as $k => $ip)
		{
			$t->define_data(array(
				"oid" => "as_".$k,
				"ip" => html::textbox(array(
					"name" => "allowed_servers[$k]",
					"value" => $ip,
					"size" => 30
				)),
			));
		}
	}

	function _init_sites_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
		));
		$t->define_field(array(
			"name" => "copy_url",
			"caption" => t("Koopia URL"),
		));
		$t->define_field(array(
			"name" => "copy_url_cvs",
			"caption" => t("Koopia URL (CVS koodil)"),
		));
		$t->define_field(array(
			"name" => "site_dir",
			"caption" => t("Saidi kaust kettal"),
		));
		$t->define_field(array(
			"name" => "site_diff",
			"caption" => t("V&otilde;rdluse objekt"),
		));
	}

	function _get_sites_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_delete_button();

		$t->add_button(array(
			"name" => "cvs",
			"tooltip" => t("CVS"),
			"action" => "install_on_cvs",
		));

		$t->add_button(array(
			"name" => "diff",
			"tooltip" => t("DIFF"),
			"action" => "run_site_diff",
		));
	}

	function _get_sites_tbl($arr)
	{
		$this->_init_sites_tbl($arr);

		$t = &$arr["prop"]["vcl_inst"];

		$odl = new object_data_list(
			array(
				"class_id" => CL_SITE_COPY_SITE,
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SITE_COPY_SITE => array("oid", "url", "copy_url", "copy_url_cvs", "site_dir", "site_diff"),
			)
		);

		foreach($odl->arr() as $o)
		{
			$t->define_data(array(
				"oid" => $o["oid"],
				"url" => html::href(array(
					"url" => $o["url"],
					"caption" => $o["url"],
				)),
				"copy_url" => html::href(array(
					"url" => $o["copy_url"],
					"caption" => $o["copy_url"],
				)),
				"copy_url_cvs" => html::href(array(
					"url" => $o["copy_url_cvs"],
					"caption" => $o["copy_url_cvs"],
				)),
				"site_dir" => $o["site_dir"],
				"site_diff" => html::obj_change_url($o["site_diff"]),
			));
		}
	}

	function _get_todo_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "new",
			"tooltip" => t("Lisa sait"),
			"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => "add_site")),
			"img" => "new.gif",
		));
		$t->add_delete_button();
	}

	function _init_todo_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
		));
	}

	function _get_todo_tbl($arr)
	{
		$this->_init_todo_tbl($arr);

		$t = &$arr["prop"]["vcl_inst"];
		$status = get_instance(CL_SITE_COPY_TODO)->sc_status_options;

		$odl = new object_data_list(
			array(
				"class_id" => CL_SITE_COPY_TODO,
				"lang_id" => array(),
				"site_id" => array(),
			), 
			array(
				CL_SITE_COPY_TODO => array("oid", "url", "packets", "packets_total", "sc_status"),
			)
		);
		foreach($odl->arr() as $o)
		{
			$status_tmp = $status[$o["sc_status"]];
			if($o["sc_status"] == site_copy_todo::STAT_TRANSFER)
			{
				$status_tmp .= sprintf(t(" %u/%u"), $o["packets"], $o["packets_total"]);
			}
			$t->define_data(array(
				"oid" => $o["oid"],
				"url" => html::obj_change_url($o["oid"], $o["url"]),
				"status" => $status_tmp,
			));
		}
	}

	function callback_generate_scripts($arr)
	{
		$r = '
		$("#local_copy").click(function(){
			if(this.checked == true)
			{
				$(aw_get_el("local_copy_prms[site]")).parent().parent().show();
			}
			else
			{
				$(aw_get_el("local_copy_prms[site]")).parent().parent().hide();
			}
		});

		if(aw_get_el("local_copy").checked != true)
		{
			$(aw_get_el("local_copy_prms[site]")).parent().parent().hide();
		}';
		return $r;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval($arr)
	{
		if(isset($arr["request"]["local_copy"]))
		{
			$arr["args"]["local_copy"] = $arr["request"]["local_copy"];
		}
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
			$this->db_query("CREATE TABLE aw_site_copy(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "time_limit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "default_mail":
			case "apache_logs":
			case "apache_vhosts":
			case "cvs_user":
			case "mysql_host":
			case "mysql_root_password":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}

	public function callback_just_saved_msg($arr)
	{
		if(isset($arr["group"]) && $arr["group"] == "add_site")
		{
			return t("Sait on lisatud kopeerimise j&auml;rjekorda!");
		}
	}

	/**
		@attrib name=invoke api=1 nologin=1 params=name all_args=1
	**/
	public function invoke($arr = array())
	{
		if(!isset($_SERVER["SERVER_ADDR"]) || !isset($_SERVER["REMOTE_ADDR"]) || ($_SERVER["SERVER_ADDR"] === $_SERVER["REMOTE_ADDR"]))
		{
			$retval = get_instance("site_copy_obj")->invoke($arr);
			return $retval;
		}
		else
		{
			print isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
		}
	}

	/**
		@attrib name=list_allowed_servers api=1 nologin=1 params=name all_args=1
	**/
	public function list_allowed_servers($arr = array())
	{
		$retval = get_instance("site_copy_obj")->list_allowed_servers($arr);
		return $retval;
	}

	/**
		@attrib name=start_transfer api=1 nologin=1 params=name all_args=1
	**/
	public function start_transfer($arr = array())
	{
		$retval = get_instance("site_copy_obj")->start_transfer($arr);
		return $retval;
	}

	/**
		@attrib name=files_count_down api=1 nologin=1 params=name all_args=1
	**/
	public function files_count_down($arr = array())
	{
		$retval = get_instance("site_copy_obj")->files_count_down($arr);
		return $retval;
	}

	public function get_obj_inst()
	{
		$retval = get_instance("site_copy_obj")->get_obj_inst();
		return $retval;
	}

	/**
		@attrib name=install_on_cvs all_args=1
	**/
	public function install_on_cvs($arr)
	{
		$cvs_status = site_copy_todo::STAT_CVS;
		foreach(safe_array($arr["sel"]) as $id)
		{
			if(!is_oid($id))
			{
				continue;
			}
			$site = obj($id);

			// Kas see t88 on juba plaanis?
			$ol = new object_list(array(
				"class_id" => CL_SITE_COPY_TODO,
				"url" => $site->url,
				"sc_status" => $cvs_status,
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				continue;
			}

			// Paneme t88 plaani
			$o = obj();
			$o->set_class_id(CL_SITE_COPY_TODO);
			$o->set_parent($id);
			$o->set_prop("url", $site->url);
			$o->set_prop("sc_status", $cvs_status);
			$o->save();
		}
		return aw_url_change_var("group", "todo", $arr["post_ru"]);
	}

	/**
		@attrib name=run_site_diff all_args=1
	**/
	public function run_site_diff($arr)
	{
		$diff_status = site_copy_todo::STAT_DIFF;
		foreach(safe_array($arr["sel"]) as $id)
		{
			if(!is_oid($id))
			{
				continue;
			}
			$site = obj($id);

			// Kas see t88 on juba plaanis?
			$ol = new object_list(array(
				"class_id" => CL_SITE_COPY_TODO,
				"url" => $site->url,
				"sc_status" => $diff_status,
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				continue;
			}

			// Paneme t88 plaani
			// Selle jaoks peab olema sait ka CVS koodil.
			$this->install_on_cvs(array("sel" => array($id)));
			$o = obj();
			$o->set_class_id(CL_SITE_COPY_TODO);
			$o->set_parent($id);
			$o->set_prop("url", $site->url);
			$o->set_prop("sc_status", $diff_status);
			$o->save();
		}
		return aw_url_change_var("group", "todo", $arr["post_ru"]);
	}

	/**
		@attrib name=add_site api=1 params=name nologin=1 all_args=1

		@param url required type=string

		@param email required type=string

		@param cvs optional type=boolean default=false

	**/
	public function add_site($arr)
	{
		return site_copy_obj::add_site($arr);
	}
	
	/**
		@attrib name=check_site api=1 params=name nologin=1

		@param url required type=string

	**/
	public function check_site($arr)
	{
		$r = site_copy_obj::check_site($arr);
		die(json_encode($r));
	}
}

?>

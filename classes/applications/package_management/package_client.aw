<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PACKAGE_CLIENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_package_client master_index=brother_of master_table=objects index=aw_oid

@default table=aw_package_client
@default group=general

@property packages_server type=textbox table=aw_package_client field=packages_server
@caption Pakiserveri url

@groupinfo packages caption="Saadaval paketid" no_submit=1
@default group=packages

	@property toolbar type=toolbar no_caption=1
	@caption T&ouml;&ouml;riistariba

	@layout packages_frame type=hbox width=20%:80%

		@layout packages_search type=vbox parent=packages_frame  area_caption=Pakkide&nbsp;otsing

			@property search_name type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Nimi

			@property search_version type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Versioon

			@property search_file type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Fail

			@property search_button type=submit no_caption=1 parent=packages_search
			@caption Otsi

		@layout packages_list type=vbox parent=packages_frame  area_caption=&nbsp;

			@property list type=table no_caption=1 parent=packages_list store=no
			@caption Pakkide nimekiri

			@property files_list type=text no_caption=1 parent=packages_list store=no
			@caption Failide nimekiri

@groupinfo made_packages caption="Loodud paketid" no_submit=1
@default group=made_packages

	@property made_toolbar type=toolbar no_caption=1
	@caption T&ouml;&ouml;riistariba

	@layout made_packages_frame type=hbox width=20%:80%

		@layout made_packages_list type=vbox parent=made_packages_frame area_caption=&nbsp;

			@property made_list type=table no_caption=1 parent=packages_list store=no
			@caption Pakkide nimekiri

			@property made_files_list type=text no_caption=1 parent=packages_list store=no
			@caption Failide nimekiri

@groupinfo my_packages caption="Kasutusel paketid" no_submit=1
@default group=my_packages

	@property my_list type=table no_caption=1 store=no
	@caption Pakkide nimekiri

	@property my_files_list type=text no_caption=1 parent=packages_list store=no
	@caption Minu Failide nimekiri

@groupinfo my_files caption="Paigaldatud failid" no_submit=1
@default group=my_files

	@layout files_frame type=hbox width=20%:80%

		@layout files_search type=vbox parent=files_frame area_caption=Failide&nbsp;otsing

			@property search_file_name type=textbox size=20 store=no captionside=top parent=files_search
			@caption Nimi

#			@property search_file_version type=textbox size=20 store=no captionside=top parent=files_search
#			@caption Versioon

#			@property search_file_package type=textbox size=20 store=no captionside=top parent=files_search
#			@caption Pakett

			@property old_files type=checkbox table=objects store=no captionside=top parent=files_search
			@caption Vanad (Mitte t&ouml;&ouml;tavad)

			@property files_search_button type=submit no_caption=1 parent=files_search
			@caption Otsi

		@layout files_list type=vbox parent=files_frame  area_caption=&nbsp;

			@property my_files type=table no_caption=1 store=no parent=files_list
			@caption Failide nimekiri
*/

class package_client extends class_base
{
	const AW_CLID = 1450;

	function package_client()
	{
		$this->init(array(
			"tpldir" => "applications/package_management/package_client",
			"clid" => CL_PACKAGE_CLIENT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case 'old_files':
			case 'search_file_package':
			case 'search_file_version':
			case 'search_file_name':
			case 'search_name':
			case 'search_version':
			case 'search_file':
				if(isset($arr['request'][$prop['name']]))
				{
					$prop['value'] = $arr['request'][$prop['name']];
				}
				break;
			case 'made_toolbar':
				$arr["prop"]["vcl_inst"]->add_new_button(array(CL_PACKAGE),$arr["obj_inst"]->id());
				$arr["prop"]["vcl_inst"]->add_delete_button();
				break;
			case 'my_files_list':
				$this->_get_files_list($arr);
				break;
			case 'made_files_list':
				if($this->can("view" , $arr["request"]["show_files"]))
				{
					$pac = obj($arr["request"]["show_files"]);
					$prop["value"] =  join("<br>" , $pac->get_package_file_names());
				}
				break;
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

	function callback_mod_retval($arr)
	{
		if (!empty($arr['request']['search_button']))
		{
			$arr['args']['search_name'] = $arr['request']['search_name'];
			$arr['args']['search_version'] = $arr['request']['search_version'];
			$arr['args']['search_file'] = $arr['request']['search_file'];
		}
		if (!empty($arr['request']['files_search_button']))
		{
			$arr['args']['search_file_name'] = $arr['request']['search_file_name'];
			$arr['args']['search_file_version'] = $arr['request']['search_file_version'];
			$arr['args']['search_file_package'] = $arr['request']['search_file_package'];
			$arr['args']['old_files'] = $arr['request']['old_files'];
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
			$this->db_query("CREATE TABLE aw_package_client(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "packages_server":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}

	function _get_files_list($arr)
	{
		$ret = t("Failid:")."<br>";
		if($arr["request"]["show_files"])
		{
			$files = $arr["obj_inst"]-> get_files_list($arr["request"]["show_files"]);
			$arr['prop']["value"] = $ret.join("<br>", $files);
			return PROP_OK;
		}
		return PROP_IGNORE;
	}

	/**
	@attrib name=download_package api=1 params=name
		@param client_id required type=oid
			packaging client
		@param package_id required type=oid
			package file id in server
		@param return_url required type=string
			Url to return
	**/
	public function download_package($arr)
	{
		$client = obj($arr["client_id"]);
		$client->download_package($arr["package_id"]);
		return $arr["return_url"];
	}

	/**
	@attrib name=upload_package api=1 params=name
		@param client_id required type=oid
			packaging client
		@param package_id required type=oid
			package file id in server
		@param return_url required type=string
			Url to return
	**/
	public function upload_package($arr)
	{
		$client = obj($arr["client_id"]);
		$client->upload_package($arr["package_id"]);
		return $arr["return_url"];
	}

	/**
	@attrib name=uninstall_package api=1 params=name
		@param package_id optional type=oid
			package object oid
		@param package_name optional type=oid
			package object name
		@param package_version optional type=oid
			package version
		@param client_id required type=oid
			package object oid
		@param return_url required type=string
			Url to return
	**/
	public function uninstall_package($arr)
	{
		$o = obj($arr["client_id"]);
		$o->uninstall_package(array(
			"id" => $arr["package_id"],
			"name" => $arr["package_name"],
			"version" => $arr["package_version"],
		));
		return $arr["return_url"];
	}

	/**
	@attrib name=restore_package api=1 params=name
		@param package_id required type=oid
			package object oid
		@param package_id_server optional type=oid
			package object id in server
		@param package_name optional type=oid
			package object name
		@param package_version optional type=oid
			package version
		@param client_id required type=oid
			package object oid
		@param return_url required type=string
			Url to return
	**/
	public function restore_package($arr)
	{
		$o = obj($arr["client_id"]);
		$o->restore_package(array(
			"server_id" => $arr["package_id_server"],
			"id" => $arr["package_id"],
			"name" => $arr["package_name"],
			"version" => $arr["package_version"],
		));
		return $arr["return_url"];
	}

	function _get_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Pakkide nimekiri'));

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select',
			'width' => '5%',
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'version',
			'caption' => t('Versioon'),
			'width' => '5%',
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'description',
			'caption' => t('Kirjeldus'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'dep',
			'caption' => t('S&otilde;ltuvused'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'down',
			'caption' => t('x'),
			"chgbgcolor" => "color",
		));

		$filter = array(
			'search_name' => (isset($arr['request']['search_name'])) ? $arr['request']['search_name'] : '',
			'search_version' => (isset($arr['request']['search_version'])) ? $arr['request']['search_version'] : '',
			'search_file' => (isset($arr['request']['search_file'])) ? $arr['request']['search_file'] : '',
		);
		$packages = $arr["obj_inst"]->get_packages($filter);

		foreach ($packages as $data)
		{
			$down_url = $this->mk_my_orb("download_package", array(
				"client_id" => $arr["obj_inst"]->id(),
				"package_id" =>  $data["id"],
				"return_url" => get_ru(),
			));
			$t->define_data(array(
				'color' => $_GET["show_files"] == $data["id"] ? "grey":"" ,
				'select' => $data["id"],
				'name' => html::href(array("caption"=> $data["name"] , "url" => aw_url_change_var("show_files" , $data["id"]))),
				'version' => $data["version"],
				'down' => html::href(array("caption"=> t("Download") , "url" => $down_url)),
			));
		}
		return PROP_OK;
	}

	function _get_my_files($arr)
	{
		$data = $arr["obj_inst"]->get_installed_files_data($arr["request"]);
		$t = &$arr['prop']['vcl_inst'];
		$t->set_caption(t('Paigaldatud failide nimekiri'));

		$t->define_field(array(
			'name' => 'file',
			'caption' => t('Fail'),
			"chgbgcolor" => "color",
			"sortable" => true,
		));

		$t->define_field(array(
			'name' => 'version',
			'caption' => t('Versioon'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'package',
			'caption' => t('Pakett'),
			"chgbgcolor" => "color",
			"sortable" => true,
		));

		$t->define_field(array(
			'name' => 'package_version',
			'caption' => t('paketi versioon'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'time',
			'caption' => t('Paigaldamise aeg'),
			"chgbgcolor" => "color",
			"sortable" => true,
		));
		foreach ($data as $d)
		{
			$t->define_data(array(
				"file" => $d["file_location"]."/".$d["file_name"],
				'version' => $d["file_version"] ,
				'package' => $d["package_name"] ,
				"package_version" => $d["package_version"],
				"time" => date("d.m.Y" , $d["installed_date"]),
				"color" => $d["file_exists"] ? "" : "grey",
			));
		}
	}

	function _get_my_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Minu alla t&otilde;mmatud pakettide nimekiri'));

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select',
			'width' => '5%',
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'version',
			'caption' => t('Versioon'),
			'width' => '5%',
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'description',
			'caption' => t('Kirjeldus'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'dep',
			'caption' => t('S&otilde;ltuvused'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'down',
			'caption' => t('Viimane versioon'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'actions',
			'caption' => t('Tegevused'),
			"chgbgcolor" => "color",
		));

		//see tuleks teha optimaalsemaks, praegu loeb k6ik sisse et uuemaid versioone leida
		$available = $arr["obj_inst"]->get_packages($filter);

		$packages = $arr["obj_inst"]->get_downloaded_packages();

		$installed = $arr["obj_inst"]->get_installed_packages();

		foreach ($packages as $data)
		{
			$color = $isinstalled = $actions = null;//arr($installed);arr($data);
			foreach($installed as $i)
			{
				if($data["name"] == $i["package_name"] && $data["version"] == $i["package_version"])
				{
					$isinstalled = 1;
					$p_object_id_in_our_system = $i["package_id"];
					continue;
				}
			}

			$server_data = $arr["obj_inst"]->download_package_properties($data["id"]);

			$last_version = $server_data["version"];
			foreach($available as $av_package)
			{
				if($av_package["name"] == $data["name"])
				{
					if($data["version"] > $last_version)
					{
						$last_version = $data["version"];
					}
				}
			}

			$uninstalllink = $this->mk_my_orb("uninstall_package", array(
				"package_id" =>  $p_object_id_in_our_system,
				"package_name" =>  $data["name"],
				"package_version" =>  $data["version"],
				"client_id" => $arr["obj_inst"]->id(),
				"return_url" => get_ru(),
			));
			$installlink = $this->mk_my_orb("restore_package", array(
				"package_id" =>  $p_object_id_in_our_system,
				"package_name" =>  $data["name"],
				"package_version" =>  $data["version"],
				"package_id_server" =>  $data["id"],
				"client_id" => $arr["obj_inst"]->id(),
				"return_url" => get_ru(),
			));

			$deps = array();
			foreach($server_data["dependencies"] as $file => $version)
			{
				$deps[] = $file." - ".$version;
			}

			if($_GET["show_files"] == $data["id"])
			{
				$color = "grey";
			}
			else
			{
				$color = !$isinstalled ? "silver" :($last_version == $data["version"]?  "#CCFFCC" : "yellow");
			}

			if($isinstalled)
			{
				$actions = html::href(array("caption"=> t("Eemalda") , "url" => $uninstalllink));
			}
			else
			{
				$actions = html::href(array("caption"=> t("Paigalda") , "url" => $installlink));
			}

			$t->define_data(array(
				'select' => $data["id"],
				'color' =>  $color,
				'name' => html::href(array("caption"=> $data["name"] , "url" => aw_url_change_var("show_files" , $data["id"]))),
				'version' => $server_data["version"],
				'dep' => join("<br>" , $deps),
				'description' =>  $server_data["description"],
				'down' => $last_version,//'down' => html::href(array("caption"=> t("Download") , "url" => $down_url)),
				'actions' => $actions,
			));
		}
		return PROP_OK;
	}

	function _get_made_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Tehtud pakettide nimekiri'));

		$t->define_chooser(array(
			'name' => 'sel',
			'field' => 'oid',
			'width' => '5%',
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi'),
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'version',
			'caption' => t('Versioon'),
			'width' => '5%',
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			'name' => 'description',
			'caption' => t('Kirjeldus'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'dep',
			'caption' => t('S&otilde;ltuvused'),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			'name' => 'up',
			'caption' => t('x'),
			"chgbgcolor" => "color",
		));

		$packages = $arr["obj_inst"]->get_made_packages();

		foreach ($packages->arr() as $package)
		{
			$dep = $package->get_dependencies();
			$dd = array();
			foreach($dep->arr() as $d)
			{
				$change_url = $this->mk_my_orb("change", array(
					"id" =>  $d->id(),
					"group" => "change",
					"return_url" => get_ru(),
				) , CL_PACKAGE);
				$dd[] = html::href(array("caption"=> $d->name()." ".$d->prop("version") , "url" => $change_url));
			}
			$up_url = $this->mk_my_orb("upload_package", array(
				"client_id" => $arr["obj_inst"]->id(),
				"package_id" =>  $package->id(),
				"return_url" => get_ru(),
			));
			$change_url = $this->mk_my_orb("change", array(
				"id" =>  $package->id(),
				"group" => "change",
				"return_url" => get_ru(),
			) , CL_PACKAGE);
			$t->define_data(array(
				'dep' => join ("<br>" , $dd),
				'oid' => $package->id(),
				'color' => $_GET["show_files"] == $package->id() ? "grey":"" ,
				'select' => $package->id(),
				'name' => html::href(array("caption"=> $package->name() , "url" => aw_url_change_var("show_files" , $package->id()))),
				'version' => $package->prop("version"),
				'description' => $package->prop("description"),
				'up' => html::href(array("caption"=> t("Muuda") , "url" => $change_url))." , ".html::href(array("caption"=> t("Upload") , "url" => $up_url)),
			));
		}
		return PROP_OK;
	}
}

?>

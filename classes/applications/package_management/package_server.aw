<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/package_management/package_server.aw,v 1.20 2008/12/05 13:15:58 markop Exp $
// package_server.aw - Pakiserver 
/*

@classinfo syslog_type=ST_PACKAGE_SERVER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo aw_package_server index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property packages_folder_aw type=relpicker reltype=RELTYPE_PACKAGES_FOLDER_AW table=aw_package_server field=packages_folder_aw
	@caption Pakkide kaust AW-s

	@property packages_folder_fs type=textbox table=aw_package_server field=packages_folder_fs
	@caption Pakkide kaust failis&uuml;steemis

@groupinfo packages caption="Pakid" no_submit=1
@default group=packages

	@property toolbar type=toolbar no_caption=1
	@caption T&ouml;&ouml;riistariba

	@layout packages_frame type=hbox width=20%:80%

		@layout packages_search type=vbox parent=packages_frame area_caption=Pakkide&nbsp;otsing

			@property search_name type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Nimi

			@property search_version type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Versioon

			@property search_file type=textbox size=20 store=no captionside=top parent=packages_search
			@caption Fail

			@property search_button type=submit no_caption=1 parent=packages_search
			@caption Otsi

		@layout packages_list type=vbox parent=packages_frame area_caption=&nbsp;

			@property list type=table no_caption=1 parent=packages_list
			@caption Pakkide nimekiri

@groupinfo sites caption="Saidid" no_submit=1
@default group=sites

	@layout sites_frame type=hbox width=20%:80%

		@layout sites_search type=vbox parent=sites_frame  area_caption=Saitide&nbsp;otsing

			@property sites_search_id type=textbox size=20 store=no captionside=top parent=sites_search
			@caption Saidi ID

			@property sites_search_url type=textbox size=20 store=no captionside=top parent=sites_search
			@caption Url

			@property sites_search_package type=textbox size=20 store=no captionside=top parent=sites_search
			@caption Pakett

			@property sites_search_file type=textbox size=20 store=no captionside=top parent=sites_search
			@caption Fail

			@property sites_search_button type=submit no_caption=1 parent=sites_search
			@caption Otsi

		@layout sites_list type=vbox parent=sites_frame area_caption=&nbsp;

			@property sites_list type=table no_caption=1 parent=sites_list
			@caption Saitide nimekiri


@reltype PACKAGES_FOLDER_AW value=1 clid=CL_MENU
@caption Pakkide kaust AW-s

*/

class package_server extends class_base
{
	const AW_CLID = 1211;

	var $model;

	function package_server()
	{
		$this->init(array(
			"tpldir" => "applications/package_management/package_server",
			"clid" => CL_PACKAGE_SERVER
		));
	}

	function get_property($arr)
	{
		if(!is_object($this->model))
		{
			$this->model = $arr["obj_inst"];
		}
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'search_name':
			case 'search_version':
			case 'search_file':
			case 'sites_search_url':
			case 'sites_search_id':
			case 'sites_search_package':
			case 'sites_search_file':
				$prop['value'] = $arr['request'][$prop['name']];
				break;
		};
		return $retval;
	}

	function _get_toolbar($arr)
	{
	
		$t = &$arr['prop']['vcl_inst'];
		
		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus pakk'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $this->model->packages_folder_aw(array('obj_inst' => $arr['obj_inst'])),
				'return_url' => get_ru()
			), CL_PACKAGE),
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta'),
			'action' => '_delete_packages',
			'confirm' => t('Oled kindel et soovid valitud objektid kustutada?')
		));

		return PROP_OK;
	}

	function _get_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Pakkide nimekiri'));

		$t->define_chooser(array(
			'name' => 'selected_ids',
			'field' => 'select',
			'width' => '5%'
		));

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
		$t->define_field(array(
			'name' => 'version',
			'caption' => t('Versioon'),
			'width' => '5%'
		));
		$t->define_field(array(
			'name' => 'description',
			'caption' => t('Kirjeldus')
		));

		$t->define_field(array(
			'name' => 'dep',
			'caption' => t('S&otilde;ltuvused'),
		));

		$filter = array(
			'search_name' => $arr['request']['search_name'],
			'search_version' => $arr['request']['search_version'],
			'search_file' => $arr['request']['search_file'],
		);

		$packages = $this->model->packages_list(array(
			'obj_inst' => $arr['obj_inst'],
			'filter' => $filter
		));

		foreach ($packages as $oid => $obj)
		{
			$deps = $obj->get_dependencies();
			$deps_arr = array();
			foreach($deps->arr() as $d)
			{
				$deps_arr[] = html::href(array(
					'caption' => $d->name()." ".$d->prop("version"),
					'url' => $this->mk_my_orb('change', array(
						'id' => $d->id()
					), CL_PACKAGE),
				));
			}


			$t->define_data(array(
				'select' => $oid,
				'name' => html::href(array(
					'caption' => $obj->name(),
					'url' => $this->mk_my_orb('change', array(
						'id' => $oid
					), CL_PACKAGE),
				)),
				'version' => $obj->prop('version'),
				'description' => substr($obj->prop('description'), 0, 500),
				"dep" => join(", " , $deps_arr),
			));
		}

		return PROP_OK;
	}

	function _get_sites_list($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);
		$list = $this->get_site_list();

		if(is_oid($_GET["site"]))
		{
			$t->set_caption($list[$_GET["site"]]["name"]." ".t("saidil kasutatavad paketid"));
	
			$t->define_field(array(
				'name' => 'id',
				'caption' => t('ID')
			));
			$t->define_field(array(
				'name' => 'name',
				'caption' => t('Nimi')
			));

			foreach($this->get_site_packages($_GET["site"]) as $pid)
			{
				$pac = obj($pid);
				$t->define_data(array(
					'id' => $pid,
					'name' => html::obj_change_url($pac->id(),($pac->name()." ".$pac->prop("version")))."<br>\n",
				));
			}
			return PROP_OK;
		}

		$t->set_caption(t('Saitide nimekiri'));

		$t->define_field(array(
			'name' => 'id',
			'caption' => t('ID')
		));
		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
		$t->define_field(array(
			'name' => 'url',
			'caption' => t('Url'),
		));
		$t->define_field(array(
			'name' => 'packages',
			'caption' => t('Paketid')
		));

		$sites = $this->get_sites($arr["request"]);
		foreach ($sites as $sid)
		{
			$p = "";
			foreach($this->get_site_packages($sid) as $pid)
			{
				$pac = obj($pid);
				$p.= html::obj_change_url($pac->id(),($pac->name()." ".$pac->prop("version")))."<br>\n";
			}
			$t->define_data(array(
				'id' => $sid,
				'name' => html::href(array("url" => aw_url_change_var("site" , $sid),"caption" => ($list[$sid]["name"]?$list[$sid]["name"]:t("(Nimetu)")))),
				'url' => $list[$sid]["url"],
				'packages' => $p,
			));
		}

		return PROP_OK;
	}

	private function get_sites($filter)
	{
		$list = $this->get_site_list();	
		$result = array_keys($list);

		$f = array(
			"class_id" => CL_PACKAGE_SITE_RELATION,
			"lang_id" => array(),
		);

		if($filter["sites_search_id"])
		{
			$result = array_intersect($result, array($filter["sites_search_id"]));
		}

		if($filter["sites_search_url"])
		{
			$url_list = array();
			foreach($list as $key => $data)
			{
				if(substr_count($data["url"], $filter["sites_search_url"]))
				{
					$url_list[] = $key;
				}
			}
			$result = array_intersect($result, $url_list);
		}

		if($filter["sites_search_package"] || $filter["sites_search_file"])
		{
			$package_list = array();
			$pf = array(
				"class_id" => CL_PACKAGE,
				"lang_id" => array(),
			);
			if($filter["sites_search_package"])
			{
				$pf["name"] = "%".$filter["sites_search_package"]."%";
			}
			if($filter["sites_search_file"])
			{
				$pf["file_names"] = "%".$filter["sites_search_file"]."%";
			}
			$packages = new object_list($pf);
			foreach($packages->arr() as $package)
			{
				$package_list = $package_list + $package->get_sites_used();
			}
			$result = array_intersect($result, $package_list);
		}
		return $result;
	}

	private function get_site_packages($sid)
	{
		$ol = new object_list(array(
			"class_id" => CL_PACKAGE,
			"lang_id" => array(),
			"CL_PACKAGE.RELTYPE_SITE_RELATION.site" => $sid,
		));
		return $ol->ids();
	}

	private function get_site_packages_names($sid)
	{
		$ol = new object_list(array(
			"class_id" => CL_PACKAGE,
			"lang_id" => array(),
			"CL_PACKAGE.RELTYPE_SITE_RELATION.site" => $sid,
		));
		return $ol->names();
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

	function callback_mod_retval($arr)
	{
//		if (!empty($arr['request']['search_button']) || !empty($arr['request']['sites_search_button']))
//		{
			$arr['args']['search_name'] = $arr['request']['search_name'];
			$arr['args']['search_version'] = $arr['request']['search_version'];
			$arr['args']['search_file'] = $arr['request']['search_file'];
			$arr['args']['sites_search_id'] = $arr['request']['sites_search_id'];
			$arr['args']['sites_search_url'] = $arr['request']['sites_search_url'];
			$arr['args']['sites_search_package'] = $arr['request']['sites_search_package'];
			$arr['args']['sites_search_file'] = $arr['request']['sites_search_file'];
//		}
	}

	function callback_mod_reforb($arr)
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

	/**
		@attrib name=_delete_packages
	**/
	function _delete_packages($arr)
	{
		$this->model->remove_packages($arr['selected_ids']);
		return $arr['post_ru'];
	}

	/** 
		@attrib name=download_package_list nologin=1 is_public=1 all_args=1 params=pos api=1
		@param filter required type=array
			package object list filter
		@return array
 	**/
	public function download_package_list($filter)
	{
		$ol = new object_list(array(
			"class_id" => CL_PACKAGE_SERVER,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$o = reset($ol->arr());
		$packages = $o->packages_list(array("filter" => $filter));
		
		$pa = array();

		foreach($packages as $package)
		{
			$deps = $package->get_dependencies();
			$files = $package->get_package_file_names();
			$pa[] = array(
				"id" => $package->id(),
				"name" => $package->name()?$package->name():t("(Nimetu)"),
				"version" => $package->prop("version"),
			);

		}
		return $pa;
		die();
	}



	/** 
		@attrib name=download_package_properties nologin=1 is_public=1 all_args=1 params=name api=1
		@param id required type=oid
			package object id
		@return array
			package object property values
 	**/
	public function download_package_properties($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_PACKAGE_SERVER,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if($this->can("view" ,$arr["id"]))
		{
			$o = obj($arr["id"]);
		}
		$properties = $o->properties();
		$deps = $o->get_dep_versions();
		$properties["file_versions"] = $o->meta("file_versions");
		if(sizeof($deps))
		{
			$properties["dependencies"] = $deps;
		}
		return $properties;
		die();
	}

	/** 
		@attrib name=download_package_files nologin=1 is_public=1 all_args=1 api=1
		@param id required type=oid
			package object id
		@return array
			package file names
 	**/
	public function download_package_files($arr)
	{
		extract($arr);
		if(!$this->can("view" , $id))
		{
			return "";
		}
		$o = obj($id);

		$files = $o->get_package_file_names();
		return $files;
	}

//-------------neid vist ei kasutata veel... siis parandab kui kasutada vaja
	/** 
		@attrib name=download_package_dependences_list nologin=1 is_public=1 all_args=1 api=1

 	**/
	function download_package_dependences($arr)
	{
		extract($arr);
		if(!$this->can("view" , $id))
		{
			return "";
		}
		$o = obj($id);

		$deps = $o->get_dependencies();
		print(join("," , $deps->ids()));

		die();
	}

	/** 
		@attrib name=download_package_description_list nologin=1 is_public=1 all_args=1 api=1

 	**/
	function download_package_description($arr)
	{
		extract($arr);
		if(!$this->can("view" , $id))
		{
			return "";
		}
		$o = obj($id);

		print($package->prop("descriotion"));
		die();
	}

//------------------------------

	/** 
		@attrib name=download_package nologin=1 is_public=1 all_args=1 params=name api=1
		@param pid required type=oid
			package object id
 	**/
	public function download_package($arr)
	{
		extract($arr);
		$file_manager = get_instance("admin/file_manager");
		$package = obj($pid);
		$files = $package->get_files();
		$arr = array();
		$arr["sel"] = $files->ids();
		$file_manager->compress_submit($arr);
	}
	
	/** uploads package object
		@attrib name=upload_package nologin=1 is_public=1 all_args=1 params=name api=1
		@param id required type=oid
			package object id
		@param site_id required type=int
			site id
		@return array
			package object property values
 	**/
	public function upload_package($arr)
	{
		$sites = $this->get_site_list();
		$client = $sites[$arr["site_id"]]["url"];
		$url = $client."/orb.aw?class=package_server&action=download_package_file&id=".$arr["id"]."&site_id=".$arr["site_id"];

		$contents = file_get_contents($url);
		$fn = aw_ini_get("server.tmpdir")."/".gen_uniq_id().".zip";
		$fp = fopen($fn, 'w');
		fwrite($fp, $contents);
		fclose($fp);
		//nyyd on fail olemas, kuid sellest on vaja teha ka paketiobjekt
		$data = $this->do_orb_method_call(array(
			"class" => "package_server",
			"action" => "download_package_properties",
			"method" => "xmlrpc",
			"server" => $client,
			"no_errors" => true,
			"params" => array(
				'id' => $arr["id"],
			),
		));		
//arr($data); die();
		$server = &$this->get_package_server();
		$server->add_package(array(
			"name" => $data["name"],
			"version" => $data["version"],
			"description" => $data["description"],
			"file" => $fn,
			"file_versions" => $data["file_versions"],
			"dependencies" => $data["dependencies"],
		));

		header("Location: ".$arr["return_url"]);
		die();
		//data alt peaks tulema igast propertite v22rtused ja fail ise on $fn alt k2tte saadav

	}

	/** returns default package server object
		@attrib name=get_package_server api=1
		@return object
			package server object
 	**/
	private function get_package_server()
	{
		if($this->package_server_object)
		{
			return $this->package_server_object;
		}
		$ol = new object_list(array(
			"class_id" => CL_PACKAGE_SERVER,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$this->package_server_object = reset($ol->arr());
		return $this->package_server_object;
	}

	/** 
		@attrib name=download_package_file nologin=1 is_public=1 all_args=1 params=name api=1
		@param id required type=oid
			package object id
		@param site_id required type=int
			site id
 	**/
	function download_package_file($arr)
	{
		extract($arr);
		$package = obj($id);
		$package->add_site($site_id);
		$package->download_package();
	}

	/** 
		@attrib name=get_package_file_size nologin=1 is_public=1 all_args=1 params=name api=1
		@param id required type=oid
			package id
 	**/ 
	function get_package_file_size($arr)
	{
		extract($arr);
		$package = obj($id);
		return $package->get_package_file_size();
	}

	private function get_site_list()
	{
		if(!$this->site_list)
		{
			$listinst = get_instance("install/site_list");
			$this->site_list = $listinst->get_site_list();
		}
		return $this->site_list;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{

		if (empty($field))
		{
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,

				packages_folder_aw int,
				packages_folder_fs varchar(255)

			)');
			return true;
		}

		switch ($field)
		{
			case 'packages_folder_aw':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
			case 'packages_folder_fs':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
                                return true;
		}

		return false;
	}

}
?>

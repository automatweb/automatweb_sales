<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/package_management/package.aw,v 1.12 2009/04/16 11:45:05 markop Exp $
// package.aw - Pakk 
/*

@classinfo syslog_type=ST_PACKAGE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut
@tableinfo aw_packages index=oid master_table=objects master_index=oid

@default table=objects
@default group=general

	@property package_tb type=toolbar store=no no_caption=1

	@property name type=textbox table=objects field=name
	@caption Nimi

	@property version type=textbox table=aw_packages field=version
	@caption Versioon

	@property description type=textarea rows=10 cols=40 table=aw_packages field=description
	@caption Kirjeldus

	@property file_names type=text table=aw_packages field=file_names
	@caption Failid

	@property installed type=hidden table=aw_packages field=installed
	@caption installitud

	@property available type=hidden table=aw_packages field=available
	@caption Valmis alla laadimiseks ja kasutamiseks

@groupinfo package_contents caption="Paki sisu"
@default group=package_contents

	@property contents_tb type=toolbar store=no no_caption=1

	@layout contents_split type=hbox width=20%:80%

		@layout contents_tree_lay type=vbox closeable=1 area_caption=Kaustad parent=contents_split

			@property contents_classes_tree type=treeview store=no no_caption=1 parent=contents_tree_lay

		@layout contents_tbl_lay type=vbox parent=contents_split

		@layout contents_tbl_top type=vbox closeable=1 area_caption=Failid parent=contents_tbl_lay

			@property contents_classes_tbl type=table store=no no_caption=1 parent=contents_tbl_top

		@layout contents_tbl_bottom type=vbox closeable=1 area_caption=Pakk parent=contents_tbl_lay

			@property contents_tbl type=table store=no no_caption=1 parent=contents_tbl_bottom 

@groupinfo dependencies caption="S&otilde;ltuvused" no_submit=1
@default group=dependencies

	@property dep_toolbar type=toolbar no_caption=1
	@caption Seoste toolbar

	@property dependencies type=table no_caption=1
	@caption S&otilde;ltuvused

@groupinfo used caption="Kasutuses" no_submit=1
@default group=used

	@property used_tbl type=table no_caption=1
	@caption Kasutusel tabel

@reltype DEPENDENCY value=1 clid=CL_PACKAGE
@caption S&otilde;ltuvus

@reltype FILE value=2 clid=CL_FILE
@caption Fail

@reltype SITE_RELATION value=3 clid=CL_PACKAGE_SITE_RELATION
@caption Seos saidiga

*/

class package extends class_base
{
	const AW_CLID = 1371;

	function package()
	{
		$this->init(array(
			"tpldir" => "applications/package_management/package",
			"clid" => CL_PACKAGE
		));
	}

	function get_property($arr)
	{
		$i = $arr["obj_inst"]->prop("installed");
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "name":
			case "version":
			case "description":
				if($i)
				{
					$prop["type"] = "text";
				}
				break;
			case "dep_toolbar":
				if($i)
				{
					return PROP_IGNORE;
				}
				break;
			case "package_tb":
				$arr["prop"]["vcl_inst"]->add_button(array(
					"name" => "create_new_package",
					"tooltip" => t("Loo uus pakett"),
					"img" => "new.gif",
					"url" => $this->mk_my_orb("create_new_package", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					), CL_PACKAGE),
				));
				$arr["prop"]["vcl_inst"]->add_button(array(
					"name" => "add_image",
					"tooltip" => t("Uuenda failide nimekiri"),
					//"img" => "new.gif",
					"url" => $this->mk_my_orb("get_file_names", array(
						"id" => $arr["obj_inst"]->id(),
						"return_url" => get_ru(),
					), CL_PACKAGE),
				));
				break;
			case "file_names":
				// this is probably some bigger bug actually, that I can't have a text type property without its value element is set
				if(!isset($prop['value']))
				{
					$prop['value'] = ''; 
				}
				break;
		};
		return $retval;
	}

	function _get_used_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "site_id",
			"caption" => t("Id"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "site_name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "url",
			"caption" => t("Url"),
			"align" => "center",
		));
		$used = $arr["obj_inst"]->get_sites_used();
		$listinst = get_instance("install/site_list");
		$this->site_list = $listinst->get_site_list();
		foreach($used as $u)
		{
			$t->define_data(array(
				"site_id" => $u,
				"site_name" => $this->site_list[$u]["name"],
				"url" => $this->site_list[$u]["url"]
			));
		}
	}

	function _get_contents_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Eemalda klassid'),
			'action' => 'remove_content_class',
			'confirm' => t('Oled kindel et soovid valitud klassid eemaldada?')
		));
		$tb->add_save_button();
		$tb->add_button(array(
			'name' => 'zip',
			'img' => 'archive_small.gif',
			'tooltip' => t('Loo zip-fail'),
			'action' => 'create_content_zip',
		));
	}

	function _get_contents_classes_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		
		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Lisa"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		if($fn = $arr["request"]["tf"])
		{
			$files = $arr["obj_inst"]->get_contents_class_files(aw_ini_get("basedir").$fn);
			foreach($files as $file)
			{
				$t->define_data(array(
					"sel" => html::checkbox(array(
						"name" => "add_file[".$file["id"]."]",
						"value" => $file["name"],
					)),
					"name" => $file["name"],
				));
			}
		}
		
	}

	function _set_contents_classes_tbl($arr)
	{
		$add = $arr["request"]["add_file"];
		$add_others = array();
		if(count($add))
		{
			$files = $arr["obj_inst"]->meta("package_contents");
			foreach($add as $file)
			{
				$fname = $arr["request"]["tf"]."/".$file;
				$others = $arr["obj_inst"]->get_other_files($fname);
				$add_others = array_merge($others, $add_others);
			}
			foreach($add as $file)
			{
				$fname = $arr["request"]["tf"]."/".$file;
				if(array_search($fname, $files) === false)
				{
					$files[] = $fname;
				}
			}
			foreach($add_others as $file)
			{
				if(array_search($file, $files) === false)
				{
					$files[] = $file;
				}
			}
			$arr["obj_inst"]->set_meta("package_contents", $files);
			$arr["obj_inst"]->save();
		}
	}

	function _get_contents_classes_tree($arr)
	{
		$tree = &$arr["prop"]["vcl_inst"];

		$tree->start_tree(array(
			"root_name" => "automatweb_dev",
			"root_url" => aw_url_change_var("tf", 0),
			"has_root" => true,
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "package_classfolders"
		));

		$folders = $arr["obj_inst"]->get_class_folders(aw_ini_get("basedir"));
		$this->insert_contents_class_folders(&$tree, $folders, 0);
	}

	function _get_contents_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
	
		$t->set_caption(t("Paki sisu"));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		if(!$arr["obj_inst"]->prop("installed"))
		{
			$t->define_chooser(array(
				"name" => "sel2",
				"field" => "id",
			));
		}
		$files = $arr["obj_inst"]->meta("package_contents");
		foreach($files as $id=>$file)
		{
			$t->define_data(array(
				"id" => "file_".$id,
				"name" => $file,
			));
		}
		
	}

	function insert_contents_class_folders($tree, $folders, $parent)
	{
		foreach($folders as $folder)
		{
			$tree->add_item($parent, array(
				"id" => $folder["id"],
				"name" => $folder["name"],
				"url" => aw_url_change_var("tf", str_replace(aw_ini_get("basedir"), "", $folder["folder"])),
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			$this->insert_contents_class_folders(&$tree, $folder["level"], $folder["id"]);
		}
	}

	/**
		@attrib name=get_file_names params=name all_args=1
		@param id required type=int
			package object id
		@param return_url required type=string
	**/
	function get_file_names($arr)
	{
		extract($arr);
		if(!$this->can("view" , $id))
		{
			return $return_url;
		}
		$o = obj($id);
		$o->set_package_file_names();
		return $return_url;
	}

	/**
		@attrib name=create_new_package params=name all_args=1
		@param id required type=int
			package object id
		@param return_url required type=string
	**/
	function create_new_package($arr)
	{
		extract($arr);
		if(!$this->can("view" , $id))
		{
			return $return_url;
		}
		$o = obj($id);
		if($id = $o->create_new_package())
		{
			return html::get_new_url(CL_PACKAGE, $id, array("return_url" => $return_url));
		}
		return $return_url;
	}

	/**
		@attrib name=create_content_zip
	**/
	function create_content_zip($arr)
	{
		if(!$this->can("view" , $arr["id"]))
		{
			return $arr["post_ru"];
		}
		$o = obj($arr["id"]);
		$o->create_package_zip($o);
		return $arr["post_ru"];
	}

	function _get_dep_toolbar($arr)
	{
		if($arr["obj_inst"]->prop("installed"))
		{
			return PROP_IGNORE;
		}
		$t = &$arr['prop']['vcl_inst'];
		
		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus pakk'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->parent(),
				'return_url' => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 1,
			), CL_PACKAGE),
		));

		$t->add_search_button(array(
			"pn" => "add_dependency",
			"clid" => CL_PACKAGE,
		));

		$t->add_button(array(
			'name' => 'delete',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta s&otilde;ltuvused'),
			'action' => 'remove_dep_packages',
			'confirm' => t('Oled kindel et soovid valitud s&otilde;ltuvused kustutada?')
		));

		return PROP_OK;
	}

	/**
		@attrib name=remove_content_class
	**/
	function remove_content_class($arr)
	{
		$o = obj($arr["id"]);
		$files = $o->meta("package_contents");
		foreach($arr["sel2"] as $id)
		{
			$id = str_replace("file_", "", $id);
			unset($files[$id]);
		}
		$o->set_meta("package_contents", $files);
		$o->save();
		return $arr["post_ru"];
	}
	/**
		@attrib name=remove_dep_packages
	**/
	function remove_dep_packages($arr)
	{
		$obj = obj($arr["id"]);
		foreach($arr["sel"] as $dep)
		{
			$obj->disconnect(array("from" => $dep));
		}
		return $arr['post_ru'];
	}

	function _get_dependencies($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->set_caption(t('Pakkide nimekiri'));

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
		if(!$arr["obj_inst"]->prop("installed"))
		{
			$t->define_chooser(array(
				"name" => "sel",
				"field" => "oid",
				"width" => "20px",
			));
		}
		$dep_list = $arr["obj_inst"]->get_dependencies();
		foreach($dep_list->arr() as $dep)
		{
			$t->define_data(array(
				"name" => $dep -> name(),
				"version" => $dep -> prop("version"),
				"description" => $dep -> prop("description"),
				"oid" => $dep->id(),
			));
		}
		return PROP_OK;
	}

	function callback_mod_tab($arr)
	{
		if (($arr["id"] == "package_contents" || $arr["id"] == "relationmgr") && $arr["obj_inst"]->prop("installed"))
		{
			return false;
		}
		return true;
	}

	function set_property($arr = array())
	{
		$i = $arr["obj_inst"]->prop("installed");
		if($i)
		{
			return PROP_IGNORE;
		}
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["add_dependency"] = 0;
		$arr["post_ru"] = post_ru();
		$arr["tf"] = $_GET["tf"];
	}

	function callback_mod_retval($arr)
	{
		if (!empty($arr['request']['tf']))
		{
			$arr['args']['tf'] = $arr['request']['tf'];
		}
	}

	function callback_pre_save($arr)
	{
		
	}

	function callback_post_save($arr)
	{
		$arr["obj_inst"]->add_dependency($arr["request"]["add_dependency"]);
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
			// db table doesn't exist, so lets create it:
			$this->db_query('CREATE TABLE '.$table.' (
				oid INT PRIMARY KEY NOT NULL,

				version varchar(255),
				description text
			)');
			return true;
		}

		switch ($field)
		{
			case 'version':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'varchar(255)'
				));
                                return true;
			case 'description':
			case 'file_names':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'text'
				));
                                return true;
			case 'installed':
			case 'available':
				$this->db_add_col($table, array(
					'name' => $field,
					'type' => 'int'
				));
                                return true;
                }
		return false;
	}
}
?>

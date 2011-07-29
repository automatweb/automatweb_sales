<?php
// converters.aw - this is where all kind of converters should live in

class converters extends aw_template
{
	// this will be set to document id if only one document is shown, a document which can be edited
	var $active_doc = false;

	function converters()
	{
		$this->init("");

	}

	/**

		@attrib name=menu_convimages params=name default="0"
		@returns
		@comment

	**/
	function menu_convimages()
	{
		$this->db_query("SELECT objects.*,menu.* FROM objects LEFT JOIN menu on menu.id = objects.oid WHERE class_id = ".menu_obj::CLID." AND status != 0");
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$meta = aw_unserialize($row["metadata"]);

			$cnt = 0;
			$imgar = array();

			$t = get_instance(CL_IMAGE);
			if ($row["img_id"])
			{
				$img = $t->get_img_by_id($row["img_id"]);
				$this->vars(array(
					"image" => "<img src='".$img["url"]."'>",
					"img_ord1" => $meta["img1_ord"]
				));
				$imgar[$cnt]["id"] = $row["img_id"];
				$imgar[$cnt]["url"] = $img["url"];
				$imgar[$cnt]["ord"] = $meta["img1_ord"];
				$cnt++;
			}

			if ($meta["img2_id"])
			{
				$img2 = $t->get_img_by_id($meta["img2_id"]);
				$this->vars(array(
					"image2" => "<img src='".$img2["url"]."'>",
					"img_ord2" => $meta["img2_ord"]
				));
				$imgar[$cnt]["id"] = $meta["img2_id"];
				$imgar[$cnt]["url"] = $img2["url"];
				$imgar[$cnt]["ord"] = $meta["img2_ord"];
				$cnt++;
			}
			if ($meta["img3_id"])
			{
				$img3 = $t->get_img_by_id($meta["img3_id"]);
				$this->vars(array(
					"image3" => "<img src='".$img3["url"]."'>",
					"img_ord3" => $meta["img3_ord"]
				));
				$imgar[$cnt]["id"] = $meta["img3_id"];
				$imgar[$cnt]["url"] = $img3["url"];
				$imgar[$cnt]["ord"] = $meta["img3_ord"];
				$cnt++;
			}
			if ($meta["img4_id"])
			{
				$img4 = $t->get_img_by_id($meta["img4_id"]);
				$this->vars(array(
					"image4" => "<img src='".$img4["url"]."'>",
					"img_ord4" => $meta["img4_ord"]
				));
				$imgar[$cnt]["id"] = $meta["img4_id"];
				$imgar[$cnt]["url"] = $img4["url"];
				$imgar[$cnt]["ord"] = $meta["img4_ord"];
				$cnt++;
			}
			if ($meta["img5_id"])
			{
				$img5 = $t->get_img_by_id($meta["img5_id"]);
				$this->vars(array(
					"image5" => "<img src='".$img5["url"]."'>",
					"img_ord5" => $meta["img5_ord"]
				));
				$imgar[$cnt]["id"] = $meta["img5_id"];
				$imgar[$cnt]["url"] = $img5["url"];
				$imgar[$cnt]["ord"] = $meta["img5_ord"];
				$cnt++;
			}

			usort($imgar,array($this,"_menu_img_cmp"));

			$o = obj($row["oid"]);
			$o->set_meta("menu_images", $imgar);
			$o->save();

			echo "menu $row[oid] <br />\n";
			flush();
			$this->restore_handle();
		}
	}

	/**

		@attrib name=menu_reset_template_sets params=name default="0"
		@returns
		@comment

	**/
	function menu_reset_template_sets()
	{
		$q = "SELECT oid FROM objects WHERE class_id = 1 AND status > 0";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->save_handle();
			aw_disable_acl();
			$tmp = obj($row["oid"]);
			$oldmeta = $tmp->meta();
			if ($oldmeta)
			{
				if (!empty($oldmeta["tpl_dir"]))
				{
					$tmp->set_meta("tpl_dir", "");
					$tmp->save();
				};
			}
			$this->restore_handle();
		}
	}

	/**

		@attrib name=promo_convert params=name default="0"
		@returns
		@comment

	**/
	function promo_convert($args = array())
	{
		$q = sprintf("SELECT oid,name,comment,metadata,menu.sss FROM objects LEFT JOIN menu ON (objects.oid = menu.id) WHERE class_id = %d AND site_id = %d",CL_PROMO,aw_ini_get("site_id"));
		$this->db_query($q);
		// so, basically, if I load a CL_PROMO object and discover that it's
		// comment field is serialized - I will have to convert all promo
		// boxes in the system.

		// menu.sss tuleb ka unserialiseerida, saadud asjad annavad meile
		// last_menus sisu

		// so, how on earth do i make a callback into this class

		$convert = false;

		while($row = $this->db_next())
		{
			print "doing $row[oid]<br />";
			$this->save_handle();
			$meta_add = aw_unserialize($row["comment"]);
			$last_menus = aw_unserialize($row["sss"]);
			$meta = aw_unserialize($row["metadata"]);
			if (is_array($last_menus) || is_array($meta_add))
			{
				$convert = true;
			};
			$meta["last_menus"] = $last_menus;
			$meta["section"] = $meta_add["section"];
			if ($meta_add["right"])
			{
				$meta["type"] = 1;
			}
			elseif ($meta_add["up"])
			{
				$meta["type"] = 2;
			}
			elseif ($meta_add["down"])
			{
				$meta["type"] = 3;
			}
			elseif ($meta_add["scroll"])
			{
				$meta["type"] = "scroll";
			}
			else
			{
				$meta["type"] = 0;
			};
			$meta["all_menus"] = $meta_add["all_menus"];
			$comment = $meta_add["comment"];
			// reset sss field of menu table
			if ($convert)
			{
				$q = "UPDATE menu SET sss = '' WHERE id = '$row[oid]'";
				$this->db_query($q);

				aw_disable_acl();
				$tmp = obj($row["oid"]);
				$tmp->set_comment($comment);
				$awa = new aw_array($meta);
				foreach($awa->get() as $k => $v)
				{
					$tmp->set_meta($k, $v);
				}
				$tmp->save();
			};
			print "<pre>";
			print_r($meta);
			print "</pre>";
			$this->restore_handle();
			print "done<br />";
			sleep(1);
			flush();
		};
	}

	////
	// some nonfunctional code here, that will convert the data stored in object metadata
	// to relations ... thrown out from the main class. I don't think anyone will miss
	// that code, b
	function convert_promo_relations($promo_box_id)
	{
	       // now, check, whether we have to convert the current contents of comment and sss to relation objects
                // we use a flag in object metainfo for that

                // and still, it would be nice if we could convert all the promo boxes at once.
                // then I wouldn't have to check for this shit each fucking time, for each
                // fucking promo box. But maybe it's not as bad as I imagine it
		$obj = new object($promo_box_id);
                if ($obj->meta("uses_relationmgr"))
                {
                        return true;
                };

		$oldaliases = $obj->connections_from(array(
			"class" => menu_obj::CLID,
		));

                $flatlist = array();


		// basically, I have to get a list of menus in $args["object"]["meta"]["section"]
		// and create a relation of type RELTYPE_ASSIGNED_MENU for each of those

                $sections = $args["obj_inst"]->meta("section");
                if ( is_array($sections) && (sizeof($sections) > 0) )
                {
                        foreach($sections as $key => $val)
                        {
                                // beiskli I need to check whether that relation exists, and if so
                                // then I should not create a new one
                                if (!$flatlist[$val])
                                {
									$o = obj($id);
									$o->connect(array(
										"to" => $val,
										"reltype" => "RELTYPE_ASSIGNED_MENU",
									));
                                };
                        };
                }

		               // then I have to get a list of menus in $args["object"]["meta"]["last_menus"] and
                // create a relation of type RELTYPE_DOC_SOURCE for each of those.

                // I also want to keep the old representation around, so that old code keeps working
                $last_menus = $args["obj_inst"]->meta("last_menus");
                if ( is_array($last_menus) && (sizeof($last_menus) > 0) )
                {
                        foreach($last_menus as $key => $val)
                        {
                                if (!$flatlist[$val])
                                {
									$o = obj($id);
									$o->connect(array(
										"to" => $val,
										"reltype" => "RELTYPE_DOC_SOURCE",
									));
                                };
                        };
                }

                // update reltype information, that is only if there is anything to update
                       $args["obj_inst"]->set_meta("uses_relationmgr",1);
                        $args["obj_inst"]->save();

	}

	/**

		@attrib name=convert_aliases params=name default="0"
		@returns
		@comment

	**/
	function convert_aliases()
	{
		$q = "SELECT target,source,type,relobj_id FROM aliases LEFT JOIN objects ON (aliases.relobj_id = objects.oid) WHERE objects.class_id = 179 AND relobj_id != 0";
		$this->db_query($q);
		$updates = array();
		while($row = $this->db_next())
		{
			$updates[] = "UPDATE objects SET subclass = $row[type] WHERE oid = $row[relobj_id]";
		};
		if (is_array($updates))
		{
			foreach($updates as $q)
			{
				print $q;
				print "<br />";
				flush();
				$this->db_query($q);
				sleep(1);
			};
		};
		print "all done!<br />";
	}

	/**

		@attrib name=groups_convert params=name default="0"


		@returns


		@comment

	**/
	function groups_convert()
	{
		aw_set_exec_time(AW_LONG_PROCESS);

		$uroot = aw_ini_get("users.root_folder");
		if (!$uroot)
		{
			$this->raise_error(ERR_NO_USERS_ROOT,"Kasutajate rootketaloog on m&auml;&auml;ramata!", true);
		}


		aw_global_set("__from_raise_error",1);
		$this->db_query("ALTER TABLE users add oid int");
		aw_global_set("__from_raise_error",1);
		$this->db_query("ALTER TABLE users add index oid(oid)");

		// 1st, let's do users
		$q = 'select users.oid, users.uid, objects.parent from users left join objects ON objects.oid = users.oid';
		$arr = $this->db_fetch_array($q);
		foreach($arr as $val)
		{
			if (!is_numeric($val['oid']) || !is_oid($val["oid"]))
			{
				$this->db_query("INSERT INTO
						objects(parent,name,comment,class_id, jrk, status, metadata, createdby, created, modifiedby, modified)
						values('$uroot','$val[uid]','',".CL_USER.",'','2',
						'','".aw_global_get("uid")."','".time()."','".aw_global_get("uid")."','".time()."')
				");
				$oid = $this->db_last_insert_id();

				if (is_numeric($oid))
				{
					$this->db_query('update users set oid='.$oid.' where uid="'.$val['uid'].'"');
				}
				echo "created object for user $val[uid] <br />\n";
				flush();
			}
			else
			if ($val["parent"] != aw_ini_get("users.root_folder"))
			{
				$this->db_query("UPDATE objects SET parent = ".aw_ini_get("users.root_folder")." WHERE oid = '".$val['oid']."'");
				echo "moved object to folder for user $val[uid] <br />\n";
				flush();
			}
		}

		// basically, move all groups objects to some rootmenu and that seems to be it.
		$rootmenu = aw_ini_get("groups.tree_root");
		if (!$rootmenu)
		{
			$this->raise_error(ERR_NO_USERS_ROOT,"Kasutajate rootketaloog on m&auml;&auml;ramata!", true);
		}
		// now, get all top-level groups.
		$this->db_query("SELECT gid,oid,type,search_form FROM groups WHERE (parent IS NULL or parent = 0) AND type IN(".group_obj::TYPE_REGULAR.",".group_obj::TYPE_DYNAMIC.")");
		while($row = $this->db_next())
		{
			$this->save_handle();
			$sql = "UPDATE objects SET parent = $rootmenu WHERE oid = $row[oid]";
			$this->db_query($sql);
			echo "grupp: $row[gid] , oid = $row[oid] <br />\n";
			flush();

			// now we must also create brothers of all the group members below this group
			$u_objs = array();
			$sql = "SELECT oid, brother_of FROM objects WHERE parent = $row[oid] AND class_id = ".CL_USER." AND status != 0";
//			echo "sql = $sql <br />";
			$this->db_query($sql);
			while($urow = $this->db_next())
			{
				if (isset($u_objs[$urow["brother_of"]]))
				{
					// delete duplicates
					$this->save_handle();
					aw_disable_acl();
					$tmp = obj($urow["oid"]);
					$tmp->delete();
					aw_restore_acl();
					$this->restore_handle();
				}
				else
				{
					$u_objs[$urow["brother_of"]] = $urow["oid"];
				}
			}

			// now get oids of group members
			$g_objs = array();
			$sql = "SELECT oid FROM users u LEFT JOIN groupmembers m ON m.uid = u.uid WHERE m.gid = $row[gid] AND oid IS NOT NULL AND oid > 0";
			$this->db_query($sql);
			while($grow = $this->db_next())
			{
				$g_objs[$grow["oid"]] = $grow["oid"];
			}

			// now, remove the ones that are not in the group
			foreach($u_objs as $real => $bro)
			{
				if (!isset($g_objs[$real]))
				{
					aw_disable_acl();
					$tmp = obj($bro);
					$tmp->delete();
					aw_restore_acl();
					$o_uid = $this->db_fetch_field("SELECT uid FROM users WHERE oid = $real", "uid");
					echo "deleted bro for $o_uid (oid = $real) <br />\n";
					flush();
				}
			}

//			echo "u_objs = ".dbg::dump($u_objs)." <br />";
			// and add bros for the ones that are missing
			foreach($g_objs as $real)
			{
//				echo "real = $real <br />\n";
//				flush();
				if (!isset($u_objs[$real]))
				{
					$tmp = obj($real);
					$_t = $tmp->create_brother($row["oid"]);
					echo "lisasin kasutaja venna $o_uid parent = $row[oid] , oid is $_t<br />\n";
					flush();
				}
			}

			// and also create aliases to all the members of the group in the group

			$sql = "SELECT users.uid, users.oid FROM groupmembers left join users on users.uid = groupmembers.uid WHERE groupmembers.gid = ".$row["gid"];
			$this->db_query($sql);
			while ($trow = $this->db_next())
			{
				if (!$trow["oid"])
				{
					continue;
				}
				$this->save_handle();

				// delete old aliases for this user.
				$this->db_query("DELETE FROM aliases WHERE target = $trow[oid] and source = $row[oid]");

				$o = obj($row["oid"]);
				$o->connect(array(
					"to" => $trow["oid"],
					"reltype" => 2
				));
				$this->restore_handle();
			}

			$this->_rec_groups_convert($row["gid"], $row["oid"]);
			$this->restore_handle();
		}
		die("Valmis!");
	}

	function _rec_groups_convert($pgid, $poid)
	{
		$this->db_query("SELECT gid,oid FROM groups WHERE parent = $pgid AND type IN(".group_obj::TYPE_REGULAR.",".group_obj::TYPE_DYNAMIC.")");
		while($row = $this->db_next())
		{
			$this->save_handle();
			$sql = "UPDATE objects SET parent = $poid WHERE oid = $row[oid]";
			$this->db_query($sql);
			echo "grupp $row[gid] <br />\n";
			flush();

			// now we must also create brothers of all the group members below this group
			$u_objs = array();
			$sql = "SELECT oid, brother_of FROM objects WHERE parent = $row[oid] AND class_id = ".CL_USER." AND status != 0";
			$this->db_query($sql);
			while($urow = $this->db_next())
			{
				if (isset($u_objs[$urow["brother_of"]]))
				{
					// delete duplicates
					$this->save_handle();
					aw_disable_acl();
					$tmp = obj($urow["oid"]);
					$tmp->delete();
					aw_restore_acl();
					$this->restore_handle();
				}
				else
				{
					$u_objs[$urow["brother_of"]] = $urow["oid"];
				}
			}

			// now get oids of group members
			$g_objs = array();
			$sql = "SELECT oid FROM users u LEFT JOIN groupmembers m ON m.uid = u.uid WHERE m.gid = $row[gid] AND oid IS NOT NULL AND oid > 0";
			$this->db_query($sql);
			while($grow = $this->db_next())
			{
				$g_objs[$grow["oid"]] = $grow["oid"];
			}

			// now, remove the ones that are not in the group
			foreach($u_objs as $real => $bro)
			{
				if (!isset($g_objs[$real]))
				{
					aw_disable_acl();
					$tmp = obj($bro);
					$tmp->delete();
					aw_restore_acl();
					$o_uid = $this->db_fetch_field("SELECT uid FROM users WHERE oid = $real", "uid");
					echo "deleted bro for $o_uid (oid = $real) <br />\n";
					flush();
				}
			}

			// and add bros for the ones that are missing
			foreach($g_objs as $real)
			{
				if (!isset($u_objs[$real]))
				{
					$o_uid = $this->db_fetch_field("SELECT uid FROM users WHERE oid = $real", "uid");
					$tmp = obj($real);
					$tmp->create_brother($row["oid"]);

					echo "lisasin kasutaja venna $o_uid <br />\n";
					flush();
				}
			}

			// and also create aliases to all the members of the group in the group
			$sql = "SELECT users.uid, users.oid FROM groupmembers left join users on users.uid = groupmembers.uid WHERE groupmembers.gid = ".$row["gid"];
			$this->db_query($sql);
			while ($trow = $this->db_next())
			{
				if (!$trow["oid"])
				{
					continue;
				}
				$this->save_handle();

				// delete old aliases for this user.
				$this->db_query("DELETE FROM aliases WHERE target = $trow[oid] and source = $row[oid]");

				$o = obj($row["oid"]);
				$o->connect(array(
					"to" => $trow["oid"],
					"reltype" => 2
				));
				$this->restore_handle();
			}

			$this->_rec_groups_convert($row["gid"], $row["oid"]);
			$this->restore_handle();
		}
	}

	/**

		@attrib name=convert_fg_tables_deleted params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function convert_fg_tables_deleted()
	{
		$ol = new object_list(array(
			"class_id" => CL_FORM,
			"site_id" => array(),
			"lang_id" => array()
		));

		echo "converting formgen tables! <br /><br />\n";

		foreach($ol->names() as $oid => $_d)
		{
			echo "form $oid <br />\n";
			flush();
			$tbl = "form_".$oid."_entries";
			aw_global_set("__from_raise_error",1);
			$this->db_query("ALTER TABLE $tbl DROP deleted");
			aw_global_set("__from_raise_error",0);
			$this->db_query("ALTER TABLE $tbl ADD deleted int default 0");
			aw_global_set("__from_raise_error",1);
			$this->db_query("ALTER TABLE $tbl ADD index deleted(deleted)");
			aw_global_set("__from_raise_error",0);


			// now, also go oever all the entries and mark the deleted ones as deleted
			$this->db_query("SELECT f.id as id , o.status as status FROM $tbl f left join objects o on o.oid = f.id");
			while($row = $this->db_next())
			{
				if ($row["status"] < 1)
				{
					$this->save_handle();
					$this->db_query("UPDATE $tbl SET deleted = 1 WHERE id = $row[id]");
					$this->restore_handle();
				}
			}
		}
		die();
	}

	/**

		@attrib name=convert_really_old_aliases params=name default="0"


		@returns


		@comment

	**/
	function convert_really_old_aliases()
	{
		echo "converting really old image aliases... <br />\n\n<br />";
		flush();
		$this->db_query("SELECT oid FROM objects WHERE class_id = ".CL_DOCUMENT." AND status != 0");
		while ($row = $this->db_next())
		{
			$id = $row["oid"];
			$this->save_handle();
			$q = "SELECT objects.*,images.*
				FROM objects
				LEFT JOIN images ON (objects.oid = images.id)
				WHERE parent = '$id' AND class_id = '6' AND status = 2
				ORDER BY idx";
			$this->db_query($q);

			while($row = $this->db_next())
			{
				$alias = "#p".$row["idx"]."#";

				// now check if the alias already exists
				$this->save_handle();
				if (!$this->db_fetch_field("SELECT id FROM aliases WHERE source = '$id' AND target = '$row[oid]'", "id"))
				{
					echo "adding alias for image $row[oid] to document $id <br />\n";
					flush();
					$o = obj($id);
					$o->connect(array(
						"to" => $row['oid'],
					));
					$this->db_query("UPDATE aliases SET idx = '$row[idx]' WHERE source = '$id' AND target = '$row[oid]'");

				}
				$this->restore_handle();
			};
			$this->restore_handle();
		}
	}

	/** creates indexes for aliases
		@attrib name=convert_alias_idx
	**/
	function convert_alias_idx()
	{
		$this->db_query("SELECT * FROM aliases WHERE idx = 0");
		while ($row = $this->db_next())
		{
			$lut[$row["source"]][$row["type"]] ++;
			$this->save_handle();
			$this->db_query("UPDATE aliases SET idx = ".$lut[$row["source"]][$row["type"]]." WHERE id = ".$row["id"]);
			echo "updated alias from dooc $row[source] to idx ".$lut[$row["source"]][$row["type"]]." <BR>";
			$this->restore_handle();
		}
	}

	/**

		@attrib name=convert_copy_makes_brother params=name nologin="1" default="0"
		@returns
		@comment

	**/
	function convert_copy_makes_brother()
	{
		$this->_copy_makes_brother_fg();
		$this->_copy_makes_brother_menu();
		die("all done! <br />");
	}

	function _copy_makes_brother_fg()
	{
		$this->db_query("SELECT oid FROM objects WHERE class_id = ".CL_FORM." AND status != 0 AND brother_of != oid");
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$id = $this->db_fetch_field("SELECT id FROM forms WHERE id = '$row[oid]'", "id");
			if ($id)
			{
				$this->db_query("UPDATE objects SET brother_of = oid WHERE oid = '$id'");
				echo "fixed form $id <br />\n";
				flush();
			}
			$this->restore_handle();
		}
	}

	function _copy_makes_brother_menu()
	{
		$this->db_query("SELECT oid FROM objects WHERE class_id = ".menu_obj::CLID." AND status != 0 AND brother_of != oid");
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$id = $this->db_fetch_field("SELECT id FROM menu WHERE id = '$row[oid]'", "id");
			if ($id)
			{
				$this->db_query("UPDATE objects SET brother_of = oid WHERE oid = '$id'");
				echo "fixed menu $id <br />\n";
				flush();
			}
			$this->restore_handle();
		}
	}

	/** creates the active_documents list for each folder in the system. the shitty part about this is, of course that

		@attrib name=convert_active_documents_list params=name nologin="1" default="0"
		@returns
		@comment
		all section modifiers will be fucked.

	**/
	function convert_active_documents_list()
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		echo "creating active document lists! <br>\n";
		flush();
		$ol = new object_list(array(
			"class_id" => array(CL_DOCUMENT, CL_PERIODIC_SECTION)
		));

		$di = get_instance("doc");
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			echo "document ".$o->name()." (".$o->id()." ) <br>\n";
			flush();
			$di->on_save_document(array("oid" => $o->id()));
		}

		die("all done!");
	}

	/**

		@attrib name=convert_doc_templates params=name nologin="1" default="0"
		@param parent required
		@returns
		@comment

	**/
	function convert_doc_templates($arr)
	{
		$parent = $arr["parent"];

		// check for oid column.
		$tbl = $this->db_get_table("template");
		if (!isset($tbl["fields"]["obj_id"]))
		{
			$this->db_query("ALTER TABLE template ADD obj_id int default 0");
		}
		$this->db_query("SELECT * FROM template WHERE obj_id = 0 OR obj_id IS NULL");
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$this->db_query("INSERT INTO
					objects(parent,name,comment,class_id, jrk, status, metadata, createdby, created, modifiedby, modified)
					values('$parent','$row[name]','',".CL_CONFIG_AW_DOCUMENT_TEMPLATE.",'','2',
					'','".aw_global_get("uid")."','".time()."','".aw_global_get("uid")."','".time()."')
			");
			$id = $this->db_last_insert_id();
			$this->db_query("UPDATE template SET obj_id = '$id' WHERE id = '$row[id]'");

			echo "template $row[name] <br>";
			$this->restore_handle();
		}
	}

	/**

		@attrib name=convert_menu_images params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function convert_menu_images($arr)
	{
		echo "converting menu image aliases<br>\n";
		flush();
		$ol = new object_list(array(
			"class_id" => menu_obj::CLID
		));
		echo "got list of all menus (".$ol->count().")<br>\n";
		flush();
		aw_disable_acl();
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			echo "menu ".$o->name()." (".$o->id().")<br>\n";
			flush();

			$t = $o->meta("menu_images");
			$mi = new aw_array($t);
			foreach($mi->get() as $idx => $i)
			{
				if ($i["id"])
				{

					$o->connect(array(
						"to" => $i["id"],
						"reltype" => 14
					));
					$t[$idx]["image_id"] = $i["id"];
				}
			}
			if ($o->parent() && $o->class_id())
			{
				$o->set_meta("menu_images", $t);
				$o->save();
			}
		}

		die("all done!");
	}

	/**

		@attrib name=convert_crm_relations2 nologin="1"


		@returns


		@comment

	**/
	function convert_crm_relations2($arr)
	{
		// see annab mulle k6ik aadressiobjektid, millel on seos URL objektiga
		aw_set_exec_time(AW_LONG_PROCESS);
		// 21 / 6 / 16 is URL
		// 219 / 9 / 17 is phone (but really fax)
		// 219 / 7,8 / 17 , is phone
		//$q = "select aliases.id,aliases.source as oldsource,aliases2.source as newsource,aliases.target as newtarget from aliases,objects,aliases as aliases2,objects as objects2  where aliases.source = objects.oid and aliases2.target = objects.oid and aliases2.source = objects2.oid and objects2.class_id = 129 and aliases.type = 21 and aliases.reltype = 6 and aliases2.reltype = 3 and objects.class_id = 146 and objects.status != 0";
		$q = "select aliases.id,aliases.source as oldsource,aliases2.source as newsource,aliases.target as newtarget from aliases,objects,aliases as aliases2,objects as objects2  where aliases.source = objects.oid and aliases2.target = objects.oid and aliases2.source = objects2.oid and objects2.class_id = 129 and aliases.type = 21 and aliases.reltype = 5 and aliases2.reltype = 3 and objects.class_id = 146 and objects.status != 0";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->save_handle();
			// read old relation, fix it.
			$id = $row["id"];
			$newsource = $row["newsource"];
			//$q = "UPDATE aliases SET source = '$newsource', reltype = 16 WHERE id = '$id'";
			$q = "UPDATE aliases SET source = '$newsource', reltype = 15 WHERE id = '$id'";
			print $q;
			print "<br>";
			$this->db_query($q);

			$this->restore_handle();
		};
		print "all done<br>";
		//  I need to get aliases that are linked to those object and sources from them.


	}



	/**

		@attrib name=convert_crm_relations nologin="1"


		@returns


		@comment

	**/
	function convert_crm_relations($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		$q = "SELECT objects.oid,objects.name,aliases.reltype,aliases.target AS target FROM aliases,objects WHERE aliases.source = objects.oid AND aliases.type = 219;";
		$this->db_query($q);
		$oids = $targets = array();
		while($row = $this->db_next())
		{
			//print "<pre>";
			$oids[] = $row["oid"];
			$targets[$row["oid"]] = $row["target"];
			//print_r($row);
			//print "</pre>";
		};

		//$oids = array(90281,92468);

		$q = "SELECT oid,name,class_id,aliases.target AS target FROM aliases,objects WHERE aliases.source = objects.oid AND aliases.target IN (" . join(",",$oids) . ")";
		$this->db_query($q);

		// now I have to all those ID-s. some are 145, which means isik, others are 129
		// which are companies
		while($row = $this->db_next())
		{
			$this->save_handle();
			// now I need to create the new links
			//if ($row["oid"] != 90281 && $row["oid"] != 92648)
			//{
				//continue;
			//};
			print "<pre>";
			print_r($row);
			print "</pre>";
			flush();
			$tg_phone = new object($targets[$row["target"]]);
			$src_obj = new object($row["oid"]);
			if ($row["class_id"] == 145)
			{
				print "Lingin isiku telefoniga " . $targets[$row["target"]] . "/" . $tg_phone->name() . "<br>";
				$src_obj->connect(array(
					"to" => $tg_phone->id(),
					"reltype" => 13,
				));
				// seose tyyp - 13
			};
			if ($row["class_id"] == 129)
			{
				print "Lingin organisatsiooni telefoniga " . $targets[$row["target"]] . "/" . $tg_phone->name() . "<br>";
				print $tg_phone->name();
				$src_obj->connect(array(
					"to" => $tg_phone->id(),
					"reltype" => 17,
				));
				// seose tyyp - 17
			};
			flush();
			$this->restore_handle();
		};

	}

	/**

	@attrib name=convert_person_org_relations

	**/
	function convert_person_org_relations($arr)
	{
		// list all connections from organizations to persons
		aw_set_exec_time(AW_LONG_PROCESS);
		$q = "SELECT aliases.source,aliases.target FROM aliases,objects WHERE type = 145 AND reltype = 8 AND aliases.source = objects.oid AND objects.class_id = 129 AND objects.status != 0";
		$this->db_query($q);
		$res = array();
		while($row = $this->db_next())
		{
			$this->save_handle();
			$q = "SELECT * FROM aliases WHERE target = '$row[source]' AND source = '$row[target]'";
			$this->db_query($q);
			$row2 = $this->db_next();
			if ($row2)
			{
				//print "org is connected $row[source],$row[target]<bR>";
			}
			else
			{
				/*
				$per_obj = new object($row["target"]);
				$per_obj->connect(array(
					"to" => $row["source"],
					"reltype" => 6,
				));
				*/
				print "person needs to be connected $row[target],$row[source]<br>";
			};
			flush();
			$this->restore_handle();
		};

		print "persons done<br>";

		$q = "SELECT aliases.source,aliases.target FROM aliases,objects WHERE type = 129 AND reltype = 6 AND aliases.source = objects.oid AND objects.class_id = 145 AND objects.status != 0";
		$this->db_query($q);
		$res = array();
		while($row = $this->db_next())
		{
			// there can be more than one .. fucking shit.
			// fucking fuckety fuckety fucking fuckety shit
			//$res[$row["source"]][$row["target"]] = $row["target"];
			$this->save_handle();
			$q = "SELECT * FROM aliases WHERE target = '$row[source]' AND source = '$row[target]'";
			$this->db_query($q);
			$row2 = $this->db_next();
			if ($row2)
			{
				print "org is connected $row[source],$row[target]<bR>";
			}
			else
			{
				/*
				$per_obj = new object($row["target"]);
				$per_obj->connect(array(
					"to" => $row["source"],
					"reltype" => 8,
				));
				*/
				print "person needs to be connected $row[target],$row[source]<br>";
			};
			flush();
			$this->restore_handle();
		};

		print "orgs done<br>";
	}

	/**

		@attrib name=confirm_crm_choices

	*/
	function confirm_crm_choices($arr)
	{
		// go over all objects, figure out the ones that do not have a confirmed relation
		// and if there are any .. then confirm those thingies

		// phone_id / 17
		// url_id / 16
		// email_id / 15
		// telefax_id / 18

		$q = "SELECT oid,target
			FROM kliendibaas_firma,aliases
			WHERE kliendibaas_firma.oid = aliases.source AND email_id = 0 AND aliases.reltype = 15";
		$this->db_query($q);
		$qs = array();
		while($row = $this->db_next())
		{
			$pid = $row["target"];
			$oid = $row["oid"];
			$qs[] = "UPDATE kliendibaas_firma SET email_id = $pid WHERE oid = $oid";
		};

		// phone_id, url_id, email_id, fax_id
		foreach($qs as $q)
		{
			print $q;
			flush();
			$this->db_query($q);
		};
		print "all done<br>";

	}

	/**

		@attrib name=convert_docs_from_menu nologin="1"


		@returns


		@comment

	**/
	function convert_docs_from_menu($arr)
	{
		$ol = new object_list(array(
			"class_id" => menu_obj::CLID
		));
		echo "converting docs from menu relations <br>\n";
		flush();
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			echo "object ".$o->id()." name = ".$o->name()." <br>\n";
			flush();

			$sss = new aw_array($o->meta("sss"));
			foreach($sss->get() as $mnid)
			{
				// 9 - RELTYPE_DOCS_FROM_MENU
				if (!$o->is_connected_to(array("to" => $mnid, "type" => 9 )))
				{
					$o->connect(array(
						"to" => $mnid,
						"reltype" => 9
					));
					echo "connect to $mnid <br>\n";
					flush();
				}
			}
		}
		die("all done! ");
	}

	/**
		@attrib name=convert_crm_links

		@comment some e-mail addresses were originally created as link objects whereas
		they should have been created ml_members (the class that deals with mail
		addresses). This converts them.

	**/
	function convert_crm_links($arr)
	{
		// first I need to create records in ml_users.mail table for each
		//
		// extlinks.url should become ml_users.mail

		// and I should also remove shit from extlinks table

		// and I should change class_id
		$q = "SELECT target,extlinks.url AS url FROM aliases,extlinks
			WHERE aliases.target = extlinks.id AND reltype = 15 AND aliases.type = 21";
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->save_handle();
			$id = $row["target"];
			$mail = $row["url"];
			$sakk = "SELECT * FROM ml_users WHERE id = '$id'";
			$this->db_query($sakk);
			$rx = $this->db_next();
			if (!$rx)
			{
				$q = "INSERT INTO ml_users (id,mail) VALUES ($id,'$mail')";
				print $q;
				$this->db_query($q);
				print "<br>";
			};
			$q = "DELETE FROM extlinks WHERE id = '$id'";
			print $q;
			$this->db_query($q);
			print "<bR>";
			$q = "UPDATE objects SET class_id = 73 WHERE oid = '$id'";
			print $q;
			$this->db_query($q);
			print "<br>";
			flush();
			$this->restore_handle();
		};
		print "all done<br>";


	}


	/** converts acl entries to relations

		@attrib name=convert_acl_rels

	**/
	function convert_acl_rels($arr)
	{
		aw_ini_set("acl.no_check", 1);
		// get list og groups that are not user groups
		$gl = array();
		$this->db_query("select gid FROM groups WHERE type IN (".group_obj::TYPE_REGULAR.",".group_obj::TYPE_DYNAMIC.")");
		while ($row = $this->db_next())
		{
			$gl[] = $row["gid"];
		}

		$us = get_instance("users");

		$gs = join(",", $gl);
		echo "got groups as $gs <br>";
		$this->db_query("SELECT *,".$this->sql_unpack_string()." FROM acl WHERE gid IN ($gs)");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			if (!$this->db_fetch_field("SELECT oid FROM objects WHERE oid = $row[oid] AND status != 0", "oid"))
			{
				$this->restore_handle();
				continue;
			}
			$this->restore_handle();
			echo "oid = $row[oid] gid = $row[gid] <br>\n";
			flush();
			$obj = obj($row["oid"]);
			$g_oid = $this->db_fetch_field("SELECT oid FROM groups WHERE gid = '".$row["gid"]."'", "oid");
			$g_obj = obj($g_oid);

			$goid = $g_obj->id();
			if (is_oid($goid))
			{
				$obj->connect(array(
					"to" => $goid,
					"reltype" => RELTYPE_ACL,
				));
			}
			// we don't need to do more, because the acl is read from the acl table!
		}
		die("all done!");
	}

	/** converts languages to objects
		@attrib name=lang_new_convert

		@param parent required type=int

	**/
	function lang_new_convert($arr)
	{
		$this->db_query("SELECT * FROM languages WHERE oid < 1 or oid is null");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			echo "keel ".$row["name"]." <br>";
			$oid = $this->db_fetch_field("SELECT max(oid) as oid FROM objects", "oid")+1;

			$this->db_query("INSERT INTO
				objects(
					name,				status,			site_id,					lang_id,
					createdby,			created,		modifiedby, 				modified,
					class_id,			parent,			brother_of,					oid,
					alias
				)
				VALUES(
					'$row[name]',	2,				".aw_ini_get("site_id").",	".aw_global_get("lang_id").",
					'".aw_global_get("uid")."',".time().",'".aw_global_get("uid")."',".time().",
					".CL_LANGUAGE.",	$arr[parent],	$oid,	$oid,
					''
				)
			");
			$this->db_query("UPDATE languages SET oid = ".$oid." WHERE id = ".$row["id"]);
			$this->restore_handle();
		}
	}


	/** converts files from db to fs

		@attrib name=conv_files_to_fs

	**/
	function conv_files_to_fs()
	{
		$this->db_query("SELECT * FROM files WHERE file IS NULL");
		while ($row = $this->db_next())
		{
			if (strlen($row["content"]) > 0)
			{
				echo "putting file $row[id] to fs! <br>\n";
				flush();
				$f = get_instance(CL_FILE);
				$fs = $f->_put_fs(array(
					"type" => $row["type"],
					"content" => $row["content"]
				));
				$this->save_handle();
				$this->db_query("UPDATE files SET file = '$fs' WHERE id = '$row[id]'");
				echo "wrote as $fs <br>\n";
				flush();
				$this->restore_handle();
			}
		}
		die("all done");
	}

	/** convert acl to object table

		@attrib name=acl_to_objtbl

	**/
	function acl_to_objtbl($arr)
	{
		$aclids = aw_ini_get("acl.ids");

		$this->db_query("UPDATE objects SET acldata = ''");

		// for all entries in the acl table
		// that are not for the owner of the object
		// write those suckers to the objects table acldata field
		$this->db_query("
			SELECT
				objects.createdby as createdby,
				acl.gid as gid,
				acl.oid as oid,
				acl.acl as acl,
				groups.type as g_type,
				groups.name as g_name,
				groups.oid as g_oid
			FROM
				acl
				LEFT JOIN objects ON objects.oid = acl.oid
				LEFT JOIN groups ON groups.gid = acl.gid
		");
		while ($row = $this->db_next())
		{
			$skip = ($row["g_type"] == group_obj::TYPE_DEFAULT) && (strtolower($row["g_name"]) == strtolower($row["createdby"]) || $row["createdby"] == "");

			if (true || !$skip)
			{
				echo "row ".join(",", map2("%s => %s", $row))." is real, write to objtbl <br>";
				// get prev value
				$this->save_handle();

				$curacl = safe_array(aw_unserialize($this->db_fetch_field("SELECT acldata FROM objects WHERE oid = $row[oid]", "acldata")));
				$curacl[$row["g_oid"]] = array();
				foreach($aclids as $bp => $nm)
				{
					$curacl[$row["g_oid"]][$nm] = (((1 << $bp) & $row["acl"]) ? 1 : 0);
				}

				//echo "got curacl as ".dbg::dump($curacl);
				$ser = aw_serialize($curacl);
				$this->quote($ser);

				$this->db_query("UPDATE objects SET acldata = '$ser' WHERE oid = $row[oid]");
				$this->restore_handle();
			}

			if (((++$cnt) % 500) == 1)
			{
				echo "obj nr $cnt , oid = $row[oid] <br>\n";
				flush();
			}
		}

		if (!$arr["no_die"])
		{
			die("all done");
		}
	}

	/**

		@attrib name=test_acl

	**/
	function test_acl()
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		$aclids = aw_ini_get("acl.ids");
		$this->db_query("SELECT oid FROM objects ");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			foreach($aclids as $nm)
			{
				$nm = str_replace("can_", "", $nm);
				$r1 = $this->can($nm, $row["oid"]);
				//$r1 = $GLOBALS["object_loader"]->can($nm, $row["oid"], true);
				$r2 = $GLOBALS["object_loader"]->can($nm, $row["oid"]);
				if ($r1 != $r2)
				{
					echo "diff in acl! old = $r1 , new = $r2 , oid = $row[oid], nm = $nm <br>\n";
					/*$r2 = $GLOBALS["object_loader"]->can($nm, $row["oid"], true);
					$GLOBALS["acl_dbg"] = 1;
					$r1 = $this->can($nm, $row["oid"]);
					$GLOBALS["acl_dbg"] = 0;
					echo "------------------------------- <br>\n";*/
					flush();
					$cnt++;
					break;
				}
			}
			if (($cnt++ % 500) == 1)
			{
				echo "obj cnt $cnt, oid = $row[oid] <br>\n";
				flush();
			}
			$this->restore_handle();
		}
		die("all done");
	}

	/** adds aliases for form entries for user profiles

		@attrib name=convert_user_fg_prof

	**/
	function convert_user_fg_prof($arr)
	{
		aw_disable_messages();
		$ul = new object_list(array(
			"class_id" => CL_USER,
			"brother_of" => new obj_predicate_prop("id")
		));
		foreach($ul->arr() as $o)
		{
			echo "user ".$o->name()." <br>\n";
			flush();
			$jfe = safe_array(aw_unserialize($o->prop("join_form_entry")));
			foreach($jfe as $eid)
			{
				if (!$o->is_connected_to(array("to" => $eid, "type" => 7)) && $this->can("view", $eid))
				{
					$o->connect(array(
						"to" => $eid,
						"reltype" => 7
					));
				}
			}
		}
		aw_restore_messages();
		die("all done");
	}

	/** convert users mail addresses

		@attrib name=convert_user_mails

	**/
	function convert_user_mails($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_USER,
			"brother_of" => new obj_predicate_prop("id"),
		));
		foreach($ol->arr() as $o)
		{
			echo "check user ".$o->prop("uid")." <br>\n";
			flush();

			$c = $o->connections_from(array(
				"type" =>  "RELTYPE_EMAIL",
			));
			if (!count($c))
			{
				$o->save();
				echo "added mail <br>\n";
				flush();
			}
		}
		die("all done!");
	}

	/**
		@attrib name=conv_task_rows

	**/
	function conv_task_rows($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_TASK,
			"lang_id" => array(),
			"site_id" => array()
		));
		aw_global_set("no_cache_flush", 1);
		foreach($ol->arr() as $o)
		{
			// get all rows from task, convert to objects below task, connect to task and clear rows
			foreach(safe_array($o->meta("rows")) as $row)
			{
//				$ro = obj();
//				$ro->set_parent($o->id());
//				$ro->set_class_id(CL_TASK_ROW);
				$ro = $o->add_row();

				$ro->set_name($row["task"]);
				$ro->set_prop("content", $row["task"]);
				$ro->set_prop("date", $row["date"]);
				$ro->set_prop("impl", $row["impl"]);
				$ro->set_prop("time_guess", $row["time_guess"]);
				$ro->set_prop("time_real", $row["time_real"]);
				$ro->set_prop("time_to_cust", $row["time_to_cust"]);
				$ro->set_prop("done", $row["done"]);
				$ro->set_prop("on_bill", $row["on_bill"]);
				$ro->set_prop("bill_id", $row["bill_id"]);
				$ro->save();

//				$o->connect(array(
//					"to" => $ro->id(),
//					"type" => "RELTYPE_ROW"
//				));

				$o->set_meta("rows", null);
				$o->save();
				echo "converted ".$o->id()."<br>\n";
				flush();
			}
		}
		$c = get_instance("cache");
		$c->full_flush();
		die("all done");
	}

	/**
		@attrib name=convert_bill_rows
	**/
	function convert_bill_rows($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL
		));
		foreach($ol->arr() as $bill)
		{
			$inf = safe_array($bill->meta("bill_inf"));
			$task_i = get_instance(CL_TASK);

			foreach($bill->connections_from(array("type" => "RELTYPE_TASK")) as $c)
			{
				$task = $c->to();
				foreach($task_i->get_task_bill_rows($task, true, $bill->id()) as $id => $row)
				{
					if (!isset($inf[$id]))
					{
						$inf[$id] = $row;
					}

					if ($this->can("view", $inf[$id]["prod"]))
					{
						$prod = obj($inf[$id]["prod"]);
						if ($this->can("view", $prod->prop("tax_rate")))
						{
							$tr = obj($prod->prop("tax_rate"));
							$inf[$id]["km_code"] = $tr->prop("code");
						}
					}

					$r = obj();
					$r->set_class_id(CL_CRM_BILL_ROW);
					$r->set_parent($bill->id());
					$r->set_prop("name", $inf[$id]["name"]);
					$r->set_prop("amt", $inf[$id]["amt"]);
					$r->set_prop("prod", $inf[$id]["prod"]);
					$r->set_prop("price", $inf[$id]["price"]);
					$r->set_prop("unit", $inf[$id]["unit"]);
					$r->set_prop("is_oe", $inf[$id]["is_oe"]);
					$r->set_prop("has_tax", $inf[$id]["has_tax"]);
					$r->set_prop("date", $inf[$id]["date"]);
					echo dbg::dump($r->properties());
					$r->save();

					$r->connect(array(
						"to" => $task->id(),
						"type" => "RELTYPE_TASK"
					));
					if ($inf[$id]["row_oid"])
					{
						$r->connect(array(
							"to" => $inf[$id]["row_oid"],
							"type" => "RELTYPE_TASK_ROW"
						));
					}

					$bill->connect(array(
						"to" => $r->id(),
						"type" => "RELTYPE_ROW"
					));
				}
			}
			$bill->set_meta("bill_inf", null);
			$bill->save();
		}
		die("all done");
	}

	/**
		@attrib name=kw_to_reltype
	**/
	function kw_to_reltype($arr)
	{
		$c = new connection();

		foreach($c->find(array("to.class_id" => CL_KEYWORD, "from.class_id" => array(menu_obj::CLID, CL_DOCUMENT))) as $con)
		{
			$c2 = new connection($con["id"]);
			if ($con["from.class_id"] == menu_obj::CLID)
			{
				$c2->change(array("reltype"  => 23));
			}
			else
			{
				$c2->change(array("reltype"  => 28));
			}
echo "mod ".$con["to.name"]."<br>";
		}
		die("all done");
	}

	/**
		@attrib name=bill_row_to_text
	**/
	function bill_row_to_text($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL_ROW
		));
		foreach($ol->arr() as $o)
		{
			if ($o->prop("date") > 10000000)
			{
				$o->set_prop("date", date("d.m.Y", $o->prop("date")));
				$o->save();
				echo "br ".$o->id()." <br>\n";
				flush();
			}
		}
		die("all done");
	}

	/**
		@attrib name=fie_scan
	**/
	function file_scan($arr)
	{
		$this->db_query("SELECT o.oid,o.name,o.status,f.file FROM files f left join objects o on o.oid = f.id");
		$fs = array();
		while ($row = $this->db_next())
		{
			$row["can_view"] = $this->can("view", $row["oid"]);
			$fs[basename($row["file"])] = $row;
		}
		echo "db has ".count($fs)." files <br>\n";
		flush();
		$dir = aw_ini_get("site_basedir")."/files";
		$lut = array(0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f');
		$sz = 0;
		for($i = 0; $i < 16; $i++)
		{
			$rd = $dir."/".$lut[$i];
			echo "<br>\n<br>\n<br>\nscanning $rd <br>\n<br>\n<br>\n";
			flush();
			$fs = $this->get_directory(array("dir" => $rd));
			foreach($fs as $file)
			{
				$bn = basename($file);
				if (!isset($fs[$bn]))
				{
					echo "file not in db $file <br>";
					$sz += filesize($rd."/".$bn);
					if ($_GET["killswitch"] == 1)
					{
						unlink($rd."/".$bn);
					}
				}
				else
				if ($fs[$bn]["status"] == 0)
				{
					echo "file has stat 0 $file <br>";
					$sz += filesize($rd."/".$bn);
					if ($_GET["killswitch"] == 1)
					{
						unlink($rd."/".$bn);
					}
				}
				else
				if (!$fs[$bn]["can_view"])
				{
					echo "file has no can view  $file <br>";
					$sz += filesize($rd."/".$bn);
					if ($_GET["killswitch_badass"] == 1)
					{
						unlink($rd."/".$bn);
					}
				}
			}
		}
		echo "could save ".number_format($sz)." bytes <br>";
		die("all done");
	}

	function do_db_upgrade($t, $f)
	{
		switch($t)
		{
			case "aw_da_classes":
				$this->db_query("CREATE TABLE `aw_da_classes` (
				  `file` varchar(255) default NULL,
				  `class_name` varchar(255) default NULL,
				  `extends` varchar(255) default NULL,
				  `implements` varchar(255) default NULL,
				  `class_type` varchar(255) default NULL,
				  `has_apis` int(11) default NULL,
				  `maintainer` varchar(255) default NULL
				)");
				return true;

			case "banner_views":
				switch($f)
				{
					case "langid":
						$this->db_query("ALTER TABLE banner_views ADD langid INT");
						return true;
				}
				break;

			case "banner_clicks":
				switch($f)
				{
					case "langid":
						$this->db_query("ALTER TABLE banner_clicks ADD langid INT");
						return true;
				}
				break;

			case "syslog":
				switch($f)
				{
					case "object_name":
						$this->db_query("ALTER TABLE syslog ADD object_name varchar(255)");
						return true;

					case "session_id":
						$this->db_query("ALTER TABLE syslog ADD session_id char(32)");
						return true;

					case "mail_id":
						$this->db_query("ALTER TABLE syslog ADD mail_id int");
						return true;
				}
				break;

			case "syslog_archive":
				switch($f)
				{
					case "":
						$this->db_query("CREATE TABLE `syslog_archive` (  `id` int(11) NOT NULL auto_increment,  `tm` int(11) default NULL,  `uid` varchar(50) default NULL,  `type` varchar(100) default NULL,  `action` varchar(255) default NULL,  `ip` varchar(100) default NULL,  `oid` int(11) default NULL,  `created_hour` int(11) default NULL,  `created_day` int(11) default NULL,  `created_week` int(11) default NULL,  `created_month` int(11) default NULL,  `created_year` int(11) default NULL,  `site_id` int(11) NOT NULL default '0',  `act_id` int(11) default NULL,  `referer` varchar(255) default NULL,  `lang_id` int(11) default '0',  `object_name` varchar(255) default NULL,  `mail_id` int(11) default NULL,  `session_id` varchar(32) default NULL,io_resolved varchar(255), country varchar(255), created_wd int, g_oid int,  PRIMARY KEY  (`id`),  KEY `type` (`type`),  KEY `tm` (`tm`),  KEY `uid` (`uid`),  KEY `ip` (`ip`),  KEY `oid` (`oid`),  KEY `created_hour` (`created_hour`),  KEY `created_day` (`created_day`),  KEY `created_week` (`created_week`),  KEY `created_month` (`created_month`),  KEY `created_year` (`created_year`),  KEY `site_id` (`site_id`),  KEY `site_id_2` (`site_id`))");
						return true;
				}
				break;

			case "syslog_archive_sessions":
				switch($f)
				{
					case "":
						$this->db_query("create table syslog_archive_sessions (id int primary key auto_increment, session_id char(32), entry_page varchar(255), exit_page varchar(255),tm_s int, tm_e int)");
						return true;
				}
				break;

			case "aw_alias_trans":
				switch($f)
				{
					case "":
						$this->db_query("create table aw_alias_trans(menu_id int,lang_id int,alias varchar(255))");
						return true;
				}
				break;

			case "aw_account_balances":
				$i = get_instance(CL_CRM_CATEGORY);
 	                        return $i->do_db_upgrade($tbl, $field);

			case "aw_postal_codes":
				switch($f)
				{
					case "":
						$this->db_query("create table aw_postal_codes(id int not null primary key auto_increment, country varchar(255), state varchar(255), city varchar(255), street varchar(255), house_start varchar(10), house_end varchar(10), zip int)");
						return true;
						break;
					case "type":
					case "area":
						$this->db_query("ALTER TABLE aw_postal_codes ADD `".$f."` varchar(40)");
						return true;
						break;
				}
				break;

			case "mrp_log":
				switch($f)
				{
					case "":
						$this->db_query("CREATE TABLE mrp_log (`id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`))");
						break;

					case "project_id":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "INT"
						));
						break;

					case "job_id":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "INT"
						));
						break;

					case "uid":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "VARCHAR(255)"
						));
						break;

					case "tm":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "INT"
						));
						break;

					case "message":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "TEXT"
						));
						break;

					case "comment":
						$this->db_add_col($t, array(
							"name" => $f,
							"type" => "TEXT"
						));
						break;
				}
				break;
		}
	}

	/**
		@attrib name=rename_file_names
	**/
	function rename_file_names($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_FILE
		));
		$mt = get_instance("core/aw_mime_types");
		$fi = get_instance(CL_FILE);
		foreach($ol->arr() as $o)
		{
			if (!$o->parent() || !$o->class_id())
			{
				continue;
			}
			$fn = $fi->generate_file_path(array(
				"type" => $o->prop("type"),
				"file_name" => $o->name()
			));

			$file = $o->prop("file");
			if (!file_exists($file))
			{
				$file = basename($file);
				$file = aw_ini_get("site_basedir")."/files/".$file[0]."/".$file;
				if (!file_exists($file))
				{
					die("file ".$o->prop("file")." / $file not found!");
				}
			}

			rename($file, $fn);
			echo "$file => $fn <br>\n";
			$o->set_prop("file", $fn);
			$o->save();
			flush();
		}
		die("all done ");

	}

	/**
		@attrib name=fix_memberships
	**/
	function fix_memberships()
	{
		$this->db_query("SELECT * FROM groupmembers ");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			// convert uid to oid
			$u_oid = $this->db_fetch_field("SELECT oid FROM users WHERE uid = '$row[uid]'", "oid");
			// convert gid to oid
			$g_oid = $this->db_fetch_field("SELECT oid FROM groups WHERE gid = '$row[gid]'", "oid");

			if ($u_oid && $g_oid)
			{
				// create rel if not exists
				$target = $this->db_fetch_field("SELECT target FROM aliases WHERE source = $u_oid AND target = $g_oid and reltype=1", "target");
				if (!$target)
				{
					echo "create rel from $u_oid => $g_oid <br>";
					$this->db_query("INSERT INTO aliases (source, target, reltype) values($u_oid, $g_oid, 1)");
				}

				$target = $this->db_fetch_field("SELECT target FROM aliases WHERE source = $g_oid AND target = $u_oid and reltype=2 ", "target");
				if (!$target)
				{
					echo "create rel from $g_oid => $u_oid <br>";

					$this->db_query("INSERT INTO aliases (source, target, reltype) values($g_oid, $u_oid, 2)");
				}
			}
			$this->restore_handle();
		}
		die("all done");
	}

	/**
		@attrib name=convert_br
		@param id optional
	**/
	function convert_br($arr)
	{
		$ol_args = array(
			"class_id" => CL_DOCUMENT,
		);

		if (is_oid($arr["id"]))
		{
			$ol_args["oid"] = $arr["id"];
		};

		$ol = new object_list($ol_args);

		foreach($ol->arr() as $o)
		{
			print "n = " . $o->name() . "<br>";
			print "nobr = ";
			$cbdat = $o->meta("cb_nobreaks");
			$save = false;
			if (empty($cbdat["content"]))
			{
				$o->set_prop("content",str_replace("\n","<br>\n",$o->prop("content")));
				$cbdat["content"] = 1;
				$save = true;
			};
			if (empty($cbdat["lead"]))
			{
				$o->set_prop("lead",str_replace("\n","<br>\n",$o->prop("lead")));
				$cbdat["lead"] = 1;
				$save = true;
			}

			if (empty($cbdat["moreinfo"]))
			{
				$o->set_prop("moreinfo",str_replace("\n","<br>\n",$o->prop("moreinfo")));
				$cbdat["moreinfo"] = 1;
				$save = true;
			}

			if ($save)
			{
				$o->set_meta("cb_nobreaks",$cbdat);
				print "saving";
				$o->save();
			}
			else
			{
				print "not saving";
			}

			print "done";
			print "<hr>";
			flush();
		}
	}

	/**
		@attrib name=convert_sections
	**/
	function convert_sections($arr)
	{
		$objlist_size_limit = aw_ini_get("performance.objlist_size_limit");
		$page = 0;

		do
		{
			$sections = new object_list(array(
				"class_id" => CL_CRM_SECTION,
				new obj_predicate_limit($objlist_size_limit, $page * $objlist_size_limit)
			));
			$countdown = $objlist_size_limit;

			if($sections->count())
			{
				$section = $sections->begin();

				do
				{
					automatweb::$result->sysmsg("Processing section '" . $section->name() . "' [" . $section->id() . "]");
					$connected_sections = $section->connections_to(array("type" => "RELTYPE_SECTION", "from.class_id" => CL_CRM_SECTION));
					if (count($connected_sections))
					{
						$c = reset($connected_sections);
						$parent_section = $c->from();
						$section->set_prop("parent_section", $parent_section->id());
						automatweb::$result->sysmsg("Found parent section '" . $parent_section->name() . "' [" . $parent_section->id() . "]");
					}

					$connected_companies = $section->connections_to(array("type" => "RELTYPE_SECTION", "from.class_id" => CL_CRM_COMPANY));

					if (count($connected_companies))
					{
						$c = reset($connected_companies);
						$organization = $c->from()->id();
					}
					else
					{
						$organization = false;
						$section_tmp = $section;
						do
						{
							$parent = $section_tmp->connections_to(array("from.class_id" => CL_CRM_COMPANY, "reltype" => "RELTYPE_SECTION"));
							if (count($parent))
							{
								$c = reset($parent);
								$section_tmp = $c->from();
								if ($section_tmp->is_a(CL_CRM_COMPANY))
								{
									$organization = $section_tmp->id();
								}
							}
							else
							{
								$parent = $section_tmp->connections_to(array("from.class_id" => CL_CRM_SECTION, "reltype" => "RELTYPE_SECTION"));
								if (count($parent))
								{
									$c = reset($parent);
									$section_tmp = $c->from();
								}
								else
								{
									$organization = 0;
								}
							}
						}
						while (false === $organization);
					}

					automatweb::$result->sysmsg("Found organization [" . $organization . "]");
					$section->set_prop("organization", $organization);
					$section->save();
					automatweb::$result->sysmsg("");
					--$countdown;
				}
				while ($section = $sections->next() and $countdown);
			}

			++$page;
		}
		while ($sections->count() === $objlist_size_limit);
		automatweb::$result->sysmsg("Done");
		exit;
	}

	/**
		@attrib name=convert_professions
	**/
	function convert_professions($arr)
	{
		$objlist_size_limit = aw_ini_get("performance.objlist_size_limit");
		$page = 0;

		do
		{
			$professions = new object_list(array(
				"class_id" => CL_CRM_PROFESSION,
				new obj_predicate_limit($objlist_size_limit, $page * $objlist_size_limit)
			));
			$countdown = $objlist_size_limit;

			if($professions->count())
			{
				$o = $professions->begin();

				do
				{
					$this->process_profession_object($o);
					automatweb::$result->sysmsg("");
					--$countdown;
				}
				while ($o = $professions->next() and $countdown);
			}

			++$page;
		}
		while ($professions->count() === $objlist_size_limit);
		automatweb::$result->sysmsg("Done");
		exit;
	}

	private function process_profession_object(object $profession)
	{
		$connected_sections = $profession->connections_to(array("type" => "RELTYPE_PROFESSIONS", "from.class_id" => CL_CRM_SECTION));
		$connected_companies = $profession->connections_to(array("type" => "RELTYPE_PROFESSIONS", "from.class_id" => CL_CRM_COMPANY));
		$profession_name = $profession->name();
		$profession_parent = $profession->parent();

		automatweb::$result->sysmsg("Processing profession '" . $profession->name() . "' [" . $profession->id() . "]");

		if (!count($connected_sections) and count($connected_companies))
		{
			$done_companies = array();

			// copy profession to all connected organizations
			foreach ($connected_companies as $c)
			{
				$company = $c->from();
				automatweb::$result->sysmsg("Found connected company '" . $company->name() . "' [" . $company->id() . "]");

				if (!in_array($company->id(), $done_companies))
				{
					if (false === $profession)
					{
						$profession = obj(null, array(), CL_CRM_PROFESSION);
						$profession->set_parent($profession_parent);
						automatweb::$result->sysmsg("Created a copy");
					}

					$profession->set_name($profession_name);
					$profession->set_prop("organization", $company->id());
					$profession->save();
					$done_companies[] = $company->id();
					$profession = false;
				}
			}
		}
		elseif (count($connected_sections))
		{
			$done_sections = array();
			foreach ($connected_sections as $c)
			{
				$section = $c->from();
				automatweb::$result->sysmsg("Found connected section '" . $section->name() . "' [" . $section->id() . "]");

				if (!in_array($section->id(), $done_sections))
				{
					if (false === $profession)
					{
						$profession = obj(null, array(), CL_CRM_PROFESSION);
						$profession->set_parent($profession_parent);
						automatweb::$result->sysmsg("Created a copy");
					}
					$profession->set_prop("parent_section", $section->id());

					// find company
					$organization = false;
					do
					{
						$parent = $section->connections_to(array("from.class_id" => CL_CRM_COMPANY, "reltype" => "RELTYPE_SECTION"));
						if (count($parent))
						{
							$c = reset($parent);
							$section = $c->from();
							if ($section->is_a(CL_CRM_COMPANY))
							{
								$organization = $section->id();
							}
						}
						else
						{
							$parent = $section->connections_to(array("from.class_id" => CL_CRM_SECTION, "reltype" => "RELTYPE_SECTION"));
							if (count($parent))
							{
								$c = reset($parent);
								$section = $c->from();
							}
							else
							{
								$organization = 0;
							}
						}
					}
					while (false === $organization);

					automatweb::$result->sysmsg("Found organization [" . $organization . "]");
					$profession->set_name($profession_name);
					$profession->set_prop("organization", $organization);
					$profession->save();
					$done_sections[] = $section->id();
					$profession = false;
				}
			}
		}
	}

	/**
		@attrib name=convert_categories_1
	**/
	function convert_categories_1($arr)
	{
		//TODO: should be run by root only
		$objlist_size_limit = aw_ini_get("performance.objlist_size_limit");
		$page = 0;

		do
		{
			$categories = new object_list(array(
				"class_id" => CL_CRM_CATEGORY,
				new obj_predicate_limit($objlist_size_limit, $page * $objlist_size_limit)
			));
			$countdown = $objlist_size_limit;

			if($categories->count())
			{
				$o = $categories->begin();

				do
				{
					automatweb::$result->sysmsg("Converting category '" . $o->name(). "' id:". $o->id());
					$this->process_category_object($o);
					--$countdown;
				}
				while ($o = $categories->next() and $countdown);
			}

			++$page;
		}
		while ($categories->count() === $objlist_size_limit);
		automatweb::$result->sysmsg("Done");
		exit;
	}

	private function process_category_object(object $category)
	{
		if (!$category->prop("category_type"))
		{
			$category->set_prop("category_type", crm_category_obj::TYPE_GENERIC);
		}

		// parent category
		if (!$category->prop("parent_category"))
		{
			$parent_category = $this->find_category_parent_category($category);
			if ($parent_category)
			{
				$category->set_prop("parent_category", $parent_category->id());
				dbg::d("Setting category parent category", $parent_category->id(), 1);
			}
		}

		// category owner
		$organization = $this->find_category_parent_organization($category);
		if ($organization)
		{
			// set owner org.
			if (!$category->prop("organization"))
			{
				$category->set_prop("organization", $organization->id());
				dbg::d("Setting category organization", $organization->id(), 1);
			}

			/* NOT IMPLEMENTED. legacy customer connections were extremely cluttered and disarranged in some systems. */
			// relocate customers
			// category customers by connection
			// 3 - reltype CUSTOMER defining customers in this category
			// $q = "SELECT o.oid as id FROM objects o RIGHT JOIN aliases a ON o.oid = a.target WHERE a.reltype = 3 AND o.class_id = " . crm_company_obj::CLID . " OR o.class_id = " . crm_person_obj::CLID . " AND a.source = " . $category->id() . " AND o.status > 0";
			// $this->db_query($q);
			// while ($row = $this->db_next())
			// {
				// dbg::d("Found legacy definition customer", $row, 1);
				/*
				$customer = $customers->begin();
				dbg::d("Found legacy definition customer", $customer->id(), 1);

				// find customer relation, create if not found
				// assume that all customers were buyers in old setup. $organization is the 'seller'
				$cro = $organization->get_customer_relation(crm_company_obj::CUSTOMER_TYPE_BUYER, $customer, true);
				// $cro->add_category($category);
				dbg::d("Adding category to customer relation", $cro->id(), 2);*/
			// }
		}

		$category->save();
	}

	private function find_category_parent_category(object $category)
	{
		$parent_category = null;
		try
		{
			// 2 - deprecated reltype CATEGORY defining subcategories
			$q = "SELECT o.oid as id FROM objects o JOIN aliases a ON o.oid = a.source WHERE a.reltype = 2 AND o.class_id = " . crm_category_obj::CLID . " AND a.target = " . $category->id() . " AND o.status > 0 LIMIT 1";
			$parent_category_oid = $this->db_fetch_field($q, "id");
			if ($parent_category_oid)
			{
				$parent_category = obj($parent_category_oid, array(), crm_category_obj::CLID);
			}
		}
		catch (Exception $e)
		{
		}

		return $parent_category;
	}

	private function find_category_parent_organization(object $category)
	{
		$parent_organization	= null;
		do
		{
			try
			{
				// 30 - reltype CATEGORY defining organization's customer categories
				$q = "SELECT o.oid as id FROM objects o JOIN aliases a ON o.oid = a.source WHERE a.reltype = 30 AND o.class_id = " . crm_company_obj::CLID . " AND a.target = " . $category->id() . " AND o.status > 0 LIMIT 1";
				$parent_organization_oid = $this->db_fetch_field($q, "id");
				if ($parent_organization_oid)
				{
					$parent_organization = obj($parent_organization_oid, array(), crm_company_obj::CLID);
				}
			}
			catch (Exception $e)
			{
			}

			$category = $this->find_category_parent_category($category);
		}
		while (!$parent_organization and $category);

		return $parent_organization;
	}
}

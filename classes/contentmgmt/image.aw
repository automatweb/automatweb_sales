<?php

// image.aw - image management
/*
	@classinfo trans=1

	@tableinfo images index=id master_table=objects master_index=oid
	@tableinfo img_d index=aw_oid master_table=objects master_index=oid


@default group=general

	@property subclass type=hidden table=objects

	@property file type=fileupload table=images form=+emb
	@caption Pilt

	@property dimensions type=text group=general,resize store=no
	@caption M&otilde;&otilde;tmed

	@property comment table=objects field=comment type=textbox
	@caption Pildi allkiri

	@property author table=objects field=meta method=serialize type=textbox
	@caption Pildi autor

	@property alt type=textbox table=objects field=meta method=serialize
	@caption Alt

	@property longdesc type=textarea rows=3 cols=30 table=objects field=meta method=serialize user=1
	@caption Pikk kirjeldus

	@property link type=textbox table=images field=link
	@caption Link

	@property date_taken type=datepicker table=images field=aw_date_taken
	@caption Pildistamise aeg

	@property can_comment type=checkbox table=objects field=flags method=bitmask ch_value=1
	@caption K&otilde;ikjal kommenteeritav

	@property no_apply_gal_conf type=checkbox table=objects field=meta method=serialize ch_value=1
	@caption &Auml;ra kasuta galerii seadeid


	/@property file_show type=text store=no editonly=1
	/@caption Eelvaade

@groupinfo show caption="N&auml;itamine"
@groupinfo show_def caption="N&auml;itamine" parent=show
@default group=show_def

	@property show_conditions type=chooser multiple=1 store=no
	@caption Tingimused

	@property newwindow type=checkbox ch_value=1 table=images field=newwindow
	@caption Uues aknas

	@property no_print type=checkbox ch_value=1 table=objects field=meta method=serialize
	@caption &Auml;ra n&auml;ita print-vaates

	@property ord type=textbox size=3 table=objects field=jrk
	@caption J&auml;rjekord

@groupinfo img2 caption="Suur pilt"
@default group=img2

	@property file2 type=fileupload table=objects field=meta method=serialize
	@caption Suur pilt

	@property file2_del type=checkbox ch_value=1 store=no
	@caption Kustuta suur pilt

	@property big_flash type=relpicker reltype=RELTYPE_FLASH table=objects field=meta method=serialize
	@caption Flash

@groupinfo resize caption="Muuda suurust"
@default group=resize

	@property new_w type=textbox field=meta method=serialize size=6 store=no
	@caption Uus laius

	@property new_h type=textbox field=meta method=serialize size=6 store=no
	@caption Uus k&otilde;rgus

	@property do_resize type=submit field=meta method=serialize store=no
	@caption Muuda

@groupinfo resize_big caption="Muuda suure pildi suurust"
@default group=resize_big

	@property dimensions_big type=text store=no
	@caption M&otilde;&otilde;tmed

	@property new_w_big type=textbox field=meta method=serialize size=6 store=no
	@caption Uus laius (suur)

	@property new_h_big type=textbox field=meta method=serialize size=6 store=no
	@caption Uus k&otilde;rgus (suur)


	/@property ord table=objects field=jrk type=text size=5
	/@caption J&auml;rjekord

	/@property file_show2 type=text group=img2 store=no editonly=1
	/@caption Eelvaade

	@property resize_warn type=text store=no
	@caption Info


@groupinfo keywords caption="M&auml;rk&otilde;nad" parent=show
@default group=keywords

	@property grkeywords2 type=keyword_selector field=meta method=serialize group=keywords reltype=RELTYPE_KEYWORD
	@caption AW M&auml;rks&otilde;nad

@groupinfo comments caption="Kommentaarid"
@default group=comments

	@property comments_tb type=toolbar no_caption=1 store=no

	@property comments_tbl type=table no_caption=1 store=no

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@reltype MOD_COMMENT value=1 clid=CL_COMMENT
@caption Moderaatori kommentaar

@reltype FLASH value=2 clid=CL_FLASH
@caption Flash

@reltype KEYWORD value=3 clid=CL_KEYWORD
@caption M&auml;rks&otilde;na
*/

define("FL_IMAGE_CAN_COMMENT", 1);

class image extends class_base
{
	private $_set_dt;
	private $do_resize = false;
	private $new_w = 0;
	private $new_h = 0;
	private $new_h_big = 0;
	private $new_w_big = 0;

	function image()
	{
		$this->init(array(
			"tpldir" => "automatweb/images",
			"clid" => CL_IMAGE
		));

		$this->trans_props = array(
			"comment", "author", "alt", "link"
		);
	}

	/**

		@attrib name=get_image_by_id api=1 params=pos

		@param id required type=int
			id of the image in images database table
		@errors
			none

		@returns
			- array with image data
			- false if the id parameter is array
			- false if the id parameter is not numeric

		@comment
			none

		@examples
			$image_inst = get_instance(CL_IMAGE);
			$image_data = $image_inst->get_image_by_id(1234);

	**/
	function get_image_by_id($id)
	{
		// it shouldn't be, but it is an array, if a period is loaded
		// from a stale cache.
		if (is_array($id) || !is_numeric($id))
		{
			return false;
		}
		if (!($row = aw_cache_get("get_image_by_id",$id)))
		{
			$q = "SELECT objects.*,images.* FROM images
				LEFT JOIN objects ON (objects.oid = images.id)
				WHERE images.id = '$id'";
			if (method_exists($this, "db_query"))
			{
				$this->db_query($q);
				$row = $this->db_fetch_row();
			}

			if ($row)
			{
				array_walk($row ,create_function('&$arr','$arr=trim($arr);'));
				$row["url"] = $this->get_url($row["file"]);
				/*
				if (aw_ini_get("image.imgbaseurl") == "")
				{
				 	//$row["url"] .= "/".$row["name"];
				}
				 */
				// if the image is from another site, then make the url point to that
				if ($row["site_id"] != aw_ini_get("site_id"))
				{
					$sl = new site_list();
					$row["url"] = str_replace(aw_ini_get("baseurl"), $sl->get_url_for_site($row["site_id"]), $row["url"]);
				}
				$row["meta"] = aw_unserialize($row["metadata"]);
				if (!isset($row["meta"]["big_flash"]))
				{
					$row["meta"]["big_flash"] = null;
				}
				$row["can_comment"] = $row["flags"] & FL_IMAGE_CAN_COMMENT;
				$row["big_url"] = null;
				if (!empty($row["meta"]["file2"]))
				{
					$row["big_url"] = $this->get_url($row["meta"]["file2"]);
					$_tmp = basename($row["meta"]["file2"]);
					$f1 = substr($_tmp,0,1);
					$row["meta"]["file2"] = aw_ini_get("file.site_files_dir") . "$f1/" . $_tmp;
					$row['file2'] = &$row['meta']['file2'];
				}
				aw_cache_set("get_image_by_id", $id, $row);
			}

			if ($this->can("view", $id))
			{
				$o = obj($id);
				$row["name"] = $o->trans_get_val("name");
				$row["comment"] = $o->trans_get_val("comment");
				$row["link"] = $o->trans_get_val("link");
				$row["meta"]["author"] = $o->trans_get_val("author");
				$row["meta"]["alt"] = $o->trans_get_val("alt");
				if ($row["meta"]["alt"] == "" && aw_ini_get("image.default_alt_text_is_name"))
				{
					$row["meta"]["alt"] = $row["name"];
				}
			}
		}
		return $row;
	}

	/** fixes image url

		@attrib name=get_url api=1 params=pos

		@param url required type=string
			url to be fixed
		@errors
			none

		@returns
			If url parameter evaluates false (ie. '', 0) then returns empty value.
		@comment
			none

		@examples
			none
	**/
	function get_url($url)
	{
		if ($url)
		{
			if (aw_ini_get("image.imgbaseurl") != "")
			{
				$imgbaseurl = aw_ini_get("image.imgbaseurl");
				$first = substr(basename($url),0,1);
				if (substr($imgbaseurl, 0, 4) === "http")
				{
					$url = $imgbaseurl . $first . "/" . basename($url);
				}
				else
				{
					$url = aw_ini_get("baseurl") . $imgbaseurl . $first . "/" . basename($url);
				}
			}
			else
			{
				$url = $this->mk_my_orb("show", array("fastcall" => 1,"file" => basename($url)),"image",false,true,"/");
			}
			$retval = str_replace("automatweb/", "", $url);
		}
		else
		{
			$retval = "";
		}
		return $retval;
	}

	function _get_fs_path($path)
	{
		if (file_exists($path))
		{
			return $path;
		}
		$tmp = basename($path);
		$tmp = aw_ini_get("file.site_files_dir").$tmp[0]."/".$tmp;
		if (file_exists($tmp))
		{
			return $tmp;
		}
		$tmp = dirname($path);
		$slp = strrpos($tmp, "/");
		$tmp = aw_ini_get("file.site_files_dir").substr($tmp, $slp)."/".basename($path);
		return $tmp;
	}

	///
	// !Kasutatakse ntx dokumendi sees olevate aliaste asendamiseks. Kutsutakse v&auml;lja callbackina
	//  force_comments - shows comment count and links to comment window even if not set in images prop
	function parse_alias($args = array())
	{
		// Defaults
		$xhtml_slash = "";
		if (aw_ini_get("content.doctype") === "xhtml")
		{
			$xhtml_slash = " /";
		}
		$inplace = '';
		$force_comments = false;
		extract($args);//TODO: get rid of this
		$use_style = isset($args["use_style"]) ? $args["use_style"] : "";

		$f = $alias;
		if (!isset($matches))
		{
			$matches = array(4=>null);
		}
		if (!$f["target"])
		{
			// now try and list images by the old way
			$idata = $this->get_img_by_oid($oid,$matches[3]);

			if (!is_array($idata))
			{
				return "";
			}
		}
		else
		{
			$idata = $this->get_image_by_id($f["target"]);
		}
		// show commentlist and popup to if set in property or forced
		$do_comments = (!empty($idata["can_comment"]) || $force_comments);

		if ((!empty($GLOBALS["print"]) || (isset($GLOBALS["class"]) && isset($GLOBALS["action"]) && $GLOBALS["class"] == "document" && $GLOBALS["action"] == "print"))  && $idata["meta"]["no_print"] == 1)
		{
			return "";
		}

		if (!empty($alias["aliaslink"]))
		{
			return html::href(array(
				"url" => $idata["url"],
				"caption" => $idata["name"],
				"target" => ($idata["newwindow"] ? "_blank" : "")
			));
		}
		$replacement = "";
		$align= array("k" => "align=\"center\"", "p" => "align=\"right\"" , "v" => "align=\"left\"" ,"" => "");
		$alstr = array("k" => "center","v" => "left","p" => "right","" => "");

		if(!empty($htmlentities))
		{
			$idata["comment"] = htmlentities($idata["comment"]);
			$idata["meta"]["alt"] = htmlentities($idata["meta"]["alt"]);
			$idata["meta"]["author"] = htmlentities($idata["meta"]["author"]);
		}

		if ($idata)
		{
			// Count comments, if needed
			$num_comments = 0;
			if(isset($args['add_show_link_add']) && is_array($args["add_show_link_arr"]) && count($args["add_show_link_arr"]))
			{
				$show_link_arr = $args["add_show_link_arr"];
			}
			$show_link_arr["id"] = $f["target"];
			if ($do_comments)
			{
				$com = get_instance(CL_COMMENT);
				$num_comments = $com->get_comment_count(array(
					'parent' => $idata["id"],
				));
				$show_link_arr["comments"] = 1; // Passed to popup window

				$idata["comment"] .= ' ('.$num_comments.' '. ($num_comments == 1 ? t("kommentaar") : t("kommentaari")) .')';
			}

			$alt = htmlspecialchars($idata["meta"]["alt"]);

			$size = array(0 => null, 1 => null);
			if (!empty($idata["meta"]["file2"]))
			{
				$size = getimagesize($idata["meta"]["file2"]);
			}
			if (isset($idata["meta"]["big_flash"]) && $this->can("view", $idata["meta"]["big_flash"]))
			{
				$flo = obj($idata["meta"]["big_flash"]);
				$size = array($flo->prop("width"), $flo->prop("height"));
			}
			$bi_show_link = $this->mk_my_orb("show_big", $show_link_arr);
			if (empty($args['addwidth']))
			{
				$args['addwidth'] = 0;
			}
			$popup_width = min(1000, $size[0] + ($do_comments ? 500 : 0)) + $args['addwidth'];
			if (empty($args['addheight']))
			{
				$args['addheight'] = 0;
			}
			$popup_height = max(5, $size[1]) + $args['addheight'];// + ($do_comments ? 200 : 0);
			$bi_link = "window.open('$bi_show_link','popup','width=".($popup_width).",height=".($popup_height)."');";

			// for case if there is a big pic, a little one is missing. then usual text link is shown with images name
			if($idata["file"] == "" && $idata["file2"] != "")
			{
				if($alt)
				{
					$alt = " alt=\"{$alt}\"";
				}

				$replacement = "<a href=\"javascript:void(0)\" onclick=\"{$bi_link}\"{$alt}>{$idata["name"]}</a>";
				if (isset($tpls["image_text_only"]))
				{
					$tpl = "image_text_only";
					$replacement = localparse($tpls[$tpl],$vars);
				}

				return array(
					"replacement" => $replacement,
					"inplace" => "",
                    );
			}

			if ($idata["file"] != "")
			{
				$image_file_path = $this->_get_fs_path($idata['file']);
				$i_size = 0;
				if (file_exists($image_file_path))
				{
					$i_size = getimagesize($image_file_path);
				}
				if (empty($idata['meta']['file2']) && $do_comments)
				{
					$size = $i_size;
				}
			}

			if ($idata["url"] == "")
			{
				return "";
			}

			if (!empty($args['link_prefix'])) // Override image link
			{
				$idata['link'] = $args['link_prefix'].$idata['oid'];
			}

			$d = new doc_display();
			$vars = array(
				"width" => $i_size[0],
				"height" => $i_size[1],
				"imgref" => $idata["url"],
				"imgcaption" => $idata["comment"],
				"align" => isset($align[$matches[4]]) ? $align[$matches[4]] : null,
				"alignstr" => $alstr[$matches[4]],
				"plink" => str_replace("&", "&amp;", $idata["link"]),
				"target" => ($idata["newwindow"] ? "target=\"_blank\"" : ""),
				"img_name" => $idata["name"],
				"alt" => $alt,
				"bigurl" => $idata["big_url"],
				"big_width" => isset($size[0]) ? $size[0] : "",
				"big_height" => isset($size[1]) ? $size[1] : "",
				"w_big_width" => isset($size[0]) ? $size[0]+10 : "",
				"w_big_height" => isset($size[1]) ? $size[1]+10 : "",
				"bi_show_link" => $bi_show_link,
				"bi_link" => $bi_link,
				"author" => $idata["meta"]["author"],
				"docid" => isset($args["oid"]) ? (is_object($args["oid"]) ? $args["oid"]->id() : $args["oid"]) : null,
				"doc_link" => empty($args["oid"]) ? "" : $d->get_doc_link(obj($args["oid"])),
				"image_id" => $idata["oid"],
				"document_link" => empty($f["source"]) ? "" : $d->get_doc_link(obj($f["source"])),
				"comments" => $num_comments,
				"longdesc" => $this->mk_my_orb("disp_longdesc", array("id" => $idata["oid"]))
			);
			$tmp = new aw_template();
			lc_site_load("document", $tmp);
			if (is_array($tmp->vars))
			{
				$vars += $tmp->vars;
			}


			if ($this->can("view", $idata["meta"]["big_flash"]))
			{
				$idata["big_url"] = " ";
			}
			$ha = "";
			if ($idata["meta"]["author"] != "")
			{
				$ha = localparse($tpls["HAS_AUTHOR"], $vars);
			}

			$vars["HAS_AUTHOR"] = $ha;
			if ($this->is_flash($idata["file"]))
			{
				$replacement = localparse($tpls["image_flash"],$vars);
			}
			elseif ($this->can("view", $idata["meta"]["big_flash"]) && isset($tpls["image_big_flash"]))
			{
				$replacement = localparse($tpls["image_big_flash"],$vars);
			}
			elseif (!empty($idata["link"]))
			{
				if ($idata["big_url"] != "" && isset($tpls["image_big_linked"]))
				{
					$replacement = localparse($tpls["image_big_linked"],$vars);
				}
				elseif (isset($tpls["image_inplace_linked"]))
				{
					$replacement = localparse($tpls["image_inplace_linked"],$vars);
					$inplace = "image_inplace_linked";
				}
				elseif (isset($tpls["image_linked"]))
				{
					$replacement = localparse($tpls["image_linked"],$vars);
				}
				elseif (!aw_ini_get("image.no_default_template"))
				{
					$authortxt = "";
					if ($idata['meta']['author'] != "")
					{
						$authortxt = ' ('.$idata['meta']['author'].')';
					}

					if ($idata["comment"] != "" || $authortxt != "")
					{
						$replacement = sprintf("<table border='0' cellpadding='0' cellspacing='0' %s><tr><td align=\"center\"><a href='%s' %s><img src='%s' border='0' alt='$alt' title='$alt' class='$use_style'$xhtml_slash></a></td></tr><tr><td align=\"center\" class=\"imagecomment\">&nbsp;%s%s</td></tr></table>",$vars["align"],str_replace("&", "&amp;", $idata["link"]),$vars["target"],$idata["url"],$idata["comment"], $authortxt);
					}
					elseif ($vars["align"] != "")
					{
						$replacement = sprintf("<table border='0' cellpadding='0' cellspacing='0' %s><tr><td><a href='%s' %s><img src='%s' border='0' alt='$alt' title='$alt' class='$use_style'$xhtml_slash></a></td></tr></table>",$vars['align'],str_replace("&", "&amp;", $idata["link"]),$vars["target"],$idata["url"]);
					}
					else
					{
						$replacement = sprintf("<a href='%s' %s><img src='%s' border='0' alt='$alt' title='$alt' class='{$use_style}'$xhtml_slash></a>", str_replace("&", "&amp;", $idata["link"]), $vars["target"], $idata["url"]);
					}
				}
			}
			else
			{
				if (isset($args["data"]["prop"]) && !empty($tpls["image_inplace_{$args["data"]["prop"]}_loop"]) /*&& $this->image_inplace_used*/)
				{
					$tpl = "image_inplace_".$args["data"]["prop"]."_loop";
					$inplace = $tpl;
					//$this->image_inplace_used = true;
				}
				elseif (isset($args["data"]["prop"]) && !empty($tpls["image_inplace_{$args["data"]["prop"]}"]) /*&& $this->image_inplace_used*/)
				{
					$tpl = "image_inplace_".$args["data"]["prop"];
					$inplace = $tpl;
					$this->image_inplace_used = true;
				}
				elseif (!empty($tpls["image_inplace"]) && !$GLOBALS["image_inplace_used"][$f["from"]])
				{
					$tpl = "image_inplace";
					$inplace = $tpl;
					// mix seda lauset vaja on?
					// sellep2rast et kui on 2 pilti pandud - siis esimese jaoks kasutatakse image_inplace subi ja j2rgmiste jaoks
					// tavalist image subi juba - terryf
					$GLOBALS["image_inplace_used"][$f["from"]] = true;
				}
				else
				{
					$tpl = "image";
					if ($idata["big_url"] != "")
					{
						$tpl = "image_has_big";
					}
					if (isset($tpls["image_text_only"]) && $idata["file"] == "")
					{
						$tpl = "image_text_only";
					}

					$inplace = 0;
				}

				if (isset($tpls[$tpl]))
				{
					$replacement = localparse($tpls[$tpl],$vars);
				}
				else if (!aw_ini_get("image.no_default_template"))
				{
					$replacement = "";
					if ($vars["align"] != "")
					{
						$replacement .= "<table border='0' cellpadding='5' cellspacing='0' $vars[align]><tr><td>";
					}

					if (!empty($idata["big_url"]) || $do_comments)
					{
						$replacement .= "<a href=\"javascript:void(0)\" onClick=\"$bi_link\">";
					}

					$replacement .= "<img src='$idata[url]' alt='$alt' title='$alt' border=\"0\" class=\"$use_style\" width='".$i_size[0]."' height='".$i_size[1]."'$xhtml_slash>";
					if (!empty($idata["big_url"]) || $do_comments)
					{
						$replacement .= "</a>";
					}

					$subtxt = "";
					if (!empty($idata["comment"]))
					{
						$subtxt .= $idata['comment'];
					}

					if (!empty($idata['meta']['author']))
					{
						$subtxt .= ' ('.$idata['meta']['author'].')';
					}

					if (strlen($subtxt))
					{
						$replacement .= "<br /><span class=\"imagecomment\">".$subtxt."</span>";
						if ($vars["align"] == "")
						{
							$replacement .= "<br />";
						}
					}

					if ($vars["align"] != "")
					{
						$replacement .= "</td></tr></table>";
					}
				}
			}
		}

		$retval = array(
				"replacement" => trim($replacement),
				"inplace" => trim($inplace),
		);
		return str_replace("\n", "", $retval);
	}

	function get_img_by_oid($oid,$idx)
	{
		$o = obj($oid);
		$c = reset($o->connections_from(array("idx" => $idx, "to.class_id" => CL_IMAGE)));
		if (is_object($c))
		{
			return $this->get_image_by_id($c->prop("to"));
		}
		else
		{
			$q = "SELECT images.*,objects.* FROM objects
				LEFT JOIN images ON objects.oid = images.id
				WHERE parent = '$oid' AND idx = '$idx' AND objects.status = 2 AND objects.class_id = 6
				ORDER BY created DESC";
			$this->db_query($q);
			$row = $this->db_next();
			if (is_array($row))
			{
				array_walk($row ,create_function('&$arr','$arr=trim($arr);'));
			}

			$row["url"] = $this->get_url($row["file"]);
			$row["meta"] = aw_unserialize($row["metadata"]);
			if ($row["meta"]["file2"] != "")
			{
				$row["big_url"] = $this->get_url($row["meta"]["file2"]);
			}

			$row["comment"] = $this->trans_get_val($o, "comment");
			$row["link"] = $this->trans_get_val($o, "link");
			$row["meta"]["author"] = $this->trans_get_val($o, "author");
			$row["meta"]["alt"] = $this->trans_get_val($o, "alt");

			return $row;
		}
	}

	/** Checks if the file is shockwave-flash file or not

		@attrib name=is_flash api=1 params=pos

		@param file required type=string
			path to the imagefile
		@errors
			none

		@returns
			true if it is flash file, false othervise

		@comment
			none

		@examples
			$inst = get_instance(CL_IMAGE);
			$o = new object(1234);
			var_dump( $inst->is_flash( $o->prop('file') ) );
	**/
	function is_flash($file)
	{
		$pos = strrpos($file,".");
		$ext = substr($file,$pos);
		if ($ext == ".x-shockwave-flash")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function add_upload_multifile($name,$parent)
	{
		$output = Array();
		$_fi = new file();
		foreach ($_FILES[$name]["error"] as $key => $error)
		{
			if ($error == UPLOAD_ERR_OK)
			{
				$img_obj = new object();
				$img_obj->set_parent($parent);
				$img_obj->set_class_id(CL_IMAGE);
				$img_obj->set_status(STAT_ACTIVE);
				$img_obj->set_name($_FILES[$name]["name"][$key]);
				if (is_uploaded_file($_FILES[$name]['tmp_name'][$key]))
				{
					$sz = getimagesize($_FILES[$name]['tmp_name'][$key]);
					$fl = $_fi->_put_fs(array(
						"type" => $_FILES[$name]['type'][$key],
						"content" => $this->get_file(array(
							"file" => $_FILES[$name]['tmp_name'][$key],
						)),
					));
					$img_obj->set_prop("file", $fl);
				}
				$img_obj->save();
				$img_id = $img_obj->id();
				$fname = ""; //TODO: oli 'undefined', kust v6tta v22rtus?
				$output[] = array("id" => $img_id,"url" => $this->get_url($img_id, $_FILES[$name]["name"][$key]), "orig_name" => $fname);
			}
		}
		return $output;
	}


	/** Saves a image that was uploaded in a form to the database

		@attrib name=is_flash api=1 params=pos

		@param name required type=string
			the name of the image input in form
		@param parent required type=oid
			the parent object of the image
		@param img_id optional type=int
			image id, if not specified, image will be added, else changed

		@errors
			none

		@returns
			- array of image data (image_id, image url and image size)
			- false if img_id is set and evaluates to false

		@comment
			none

		@examples
			none
	**/
	function add_upload_image($name,$parent,$img_id = 0, $file = true, $file2 = false)
	{
		$img_id = (int)$img_id;

		$_fi = new file();
		if ($_FILES[$name]['tmp_name'] != "" && $_FILES[$name]['tmp_name'] != "none")
		{
			if (!$this->can("view", $img_id))
			{
				$img_obj = new object();
				$img_obj->set_parent($parent);
				$img_obj->set_class_id(CL_IMAGE);
				$img_obj->set_status(STAT_ACTIVE);
				$img_obj->set_name($_FILES[$name]["name"]);
			}
			else
			{
				$img_obj = obj($img_id);
				$img_obj->set_name($_FILES[$name]["name"]);
			}
			if (is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				$sz = getimagesize($_FILES[$name]['tmp_name']);

				if($file)
				{
					$fl = $_fi->_put_fs(array(
						"type" => $_FILES[$name]['type'],
						"content" => $this->get_file(array(
							"file" => $_FILES[$name]['tmp_name'],
						)),
					));

					$img_obj->set_prop("file", $fl);
				}

				if($file2)
				{
					$f2 = $_fi->_put_fs(array(
						"type" => $_FILES[$name]['type'],
						"content" => $this->get_file(array(
							"file" => $_FILES[$name]['tmp_name'],
						)),
					));

					$img_obj->set_prop("file2", $f2);
				}
			}
			$img_obj->save();
			$img_id = $img_obj->id();
		}
		else
		{
			if ($img_id)
			{
				$id = $this->get_image_by_id($img_id);
				// we need to return the image size as well
				$sz = getimagesize($id['file']);
				$fl = $id["file"];
 				return array(
					"id" => $img_id,
					"url" => $id["url"],
					"sz" => $sz,
				);
			}
			else
			{
				return false;
			}
		}

		return array("id" => $img_id,"url" => $this->get_url($fl), "sz" => $sz);
	}

	/**

		@attrib name=show params=name nologin="1"

		@param file required

		@returns


		@comment

	**/
	function show($arr)
	{
		extract($arr);
		$rootdir = aw_ini_get("site_basedir");
		$f1 = substr($file,0,1);
		$fname = $rootdir . "img/$f1/" . $file;
		if ($file)
		{
			if (strpos("/",$file) !== false)
			{
				header("Content-type: text/html");
				print "access denied,";
			}

			// the site's img folder
			if (is_file($fname) && is_readable($fname))
			{
				$passed = true;
			}
			else
			{
				$fname = aw_ini_get("file.site_files_dir") . "{$f1}/{$file}";
				if (is_file($fname) && is_readable($fname))
				{
					$passed = true;
				}
				else
				{
					$passed = false;
				}
			}

			if ($passed)
			{
				if ($this->is_flash($file))
				{
					$size[2] = 69;
				}
				else
				{
					$size = GetImageSize($fname);
				}

				if (!is_array($size))
				{
					print "access denied.";
				}
				else
				{
					switch($size[2])
					{
						case "1":
							$type = "image/gif";
							break;
						case "2":
							$type = "image/jpg";
							break;
						case "3":
							$type = "image/png";
							break;
						case "69":
							$type = "application/x-shockwave-flash";
							break;
					}

					// if resize requested
					if (isset($GLOBALS["resize_x"]) && $GLOBALS["resize_x"] > 0 || isset($GLOBALS["resize_y"]) && $GLOBALS["resize_y"] > 0)
					{
						$im = imagecreatefromstring(file_get_contents($fname));

						if ($GLOBALS["resize_y"])
						{
							$y = $GLOBALS["resize_y"];
							$x = (int)(($GLOBALS["resize_y"] / $size[1]) * $size[0]);
						}
						else
						{
							$x = $GLOBALS["resize_x"];
							$y = (int)(($GLOBALS["resize_x"] / $size[0]) * $size[1]);
						}
						$tmpimg = imagecreatetruecolor($x, $y);
						imagecopyresampled($tmpimg, $im,0,0, 0, 0, $x, $y, $size[0], $size[1]);
						imagedestroy($im);

						header("Content-type: $type");
						switch($size[2])
						{
							case "1":
								imagegif($tmpimg);
								break;
							case "2":
								imagejpeg($tmpimg);
								break;
							case "3":
								imagepng($tmpimg);
								break;
						}
						die();
					}

					// let the browser cache images
					$cur_etag = md5($arr["file"]);


					$offset = 3600;
					header("Expires: ".gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
					header("Pragma:");
					header("Cache-control: max-age=".$offset);
					header("Content-type: $type");
					header("Content-length: ".filesize($fname));
					header("ETag: ".$cur_etag);

					if(isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) and strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) > filemtime($fname))
					{
						header("HTTP/1.x 304 Not Modified");
						die();
					}

					// check if we got an if-none-match and if we did, then check the etag and
					// finally, return is cached if all checks out
					if (isset($_SERVER["HTTP_IF_NONE_MATCH"]))
					{
						foreach(explode(",", $_SERVER["HTTP_IF_NONE_MATCH"]) as $check_etag)
						{
							$check_etag = trim($check_etag);
							if ($check_etag == $cur_etag)
							{
								header("HTTP/1.x 304 Not Modified");
								die();
							}
						}
					}

					readfile($fname);
				}
			}
			else
			{
				print "Access denied.";
			}
		}
		else
		{
			print "Access denied.";
		}
		die();
	}

	/** Creates HTML image tag

		@attrib name=view params=name nologin="1"

		@param id required type=int
			image id
		@param height optional type=int
			image's height

		@returns
			HTML image tag

		@comment
			none

		@examples
			none
	**/
	function view($args = array())
	{
		$idata = $this->get_image_by_id($args["id"]);
		$this->mk_path($idata["parent"],"Vaata pilti");
		$retval = html::img(array(
			"url" => $idata["url"],
			'height' => (isset($args['height']) ? $args['height'] : NULL),
		));
		return $retval;
	}

	/** Rewrites the image's url to the correct value

		@attrib name=view params=name nologin="1"

		@param url required type=string
			URL to be rewritten

		@returns
			- Rewrote URL
			- If url parameter is empty, then returns empty value

		@comment
			removes host name from url
			if url is site/img.aw , rewrites to the correct orb fastcall
			adds baseurl

		@examples
			none
	**/
	public static function check_url($url)
	{
		if ($url == "")
		{
			return $url;
		}

		$url = str_replace(aw_ini_get("baseurl"), "", $url);
		$url = preg_replace("/^http:\/\/.*\//U","/",$url);
		$url = preg_replace("/^https:\/\/.*\//U","/",$url);
		if (substr($url,0,4) == "/img")
		{
			$fname = substr($url,13);
			$url = aw_ini_get("baseurl")."orb.".aw_ini_get("ext")."/class=image/action=show/fastcall=1/file=".$fname;
		}
		else
		{
			if ($url == "")
			{
				$url = "automatweb/images/trans.gif";
			}
			$url = aw_ini_get("baseurl").$url;
		}
		$url = str_replace("automatweb/", "", $url);
		$imgbaseurl = aw_ini_get("image.imgbaseurl");
		if (!empty($imgbaseurl))
		{
			if (preg_match("/file=(.*)$/",$url,$m))
			{
				$fname = $m[1];
				$first = substr($fname,0,1);
				$url = aw_ini_get("baseurl") . $imgbaseurl . $first . "/" . $fname;
				if (substr($url,-11) == "/aw_img.jpg")
				{
					$url = str_replace("/aw_img.jpg","",$url);
				}
			}
		}
		return $url;
	}


	/** Creates HTML image tag
		@attrib name=make_img_tag params=pos

		@param url required type=string
			URL to the image
		@param alt optional type=string
			Alt text of the image

		@param size optional type=array
			array(
				height => int,
				width => int
			)
			sets img tag height and width
		@param arguments optional type=array
			array(
				show_title => true	// default true
			)

		@returns
			- Rewrote URL
			- If url parameter is empty, then returns empty value

		@comment
			removes host name from url
			if url is site/img.aw , rewrites to the correct orb fastcall
			adds baseurl

		@examples
			none
	**/
	public static function make_img_tag($url, $alt = "", $size = array(), $arr = array())
	{
		$tag = isset($size["height"]) ?" height=\"".$size["height"]."\"":"";
		$tag .= isset($size["width"]) ?" width=\"".$size["width"]."\"":"";

		$title = !isset($arr["show_title"]) || !empty($arr["show_title"]) ? " title=\"$alt\"" : "";
		if ($url == "")
		{
			return "<img src=\"".aw_ini_get("baseurl")."automatweb/images/trans.gif\" alt=\"$alt\"{$title}".$tag." />";
		}
		else
		{
			return "<img src=\"$url\" alt=\"$alt\"{$title}".$tag." />";
		}
	}

	function get_property($arr)
	{
		$prop = &$arr['prop'];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "newwindow":
			case "no_print":
				$retval = PROP_IGNORE;
				break;

			case "show_conditions":
				$prop["options"] = array(
					"newwindow" => t("Uues aknas"),
					"no_print" => t("&Auml;ra n&auml;ita print-vaates"),
				);
				$prop["value"]["newwindow"] = $arr['obj_inst']->prop("newwindow");
				$prop["value"]["no_print"] = $arr['obj_inst']->prop("no_print");
				break;


			case "resize_warn":
				if (is_oid($arr["obj_inst"]->id()))
				{
					if (($id = $this->_get_conf_for_folder($arr["obj_inst"]->parent(), true)))
					{
						$o = obj($id);
						$prop["value"] = t("Piltide automaatset suurendamist kontrollib objekt ").html::href(array(
							"url" => $this->mk_my_orb("change", array("id" => $id), $o->class_id()),
							"caption" => $o->name()
						));
						return PROP_OK;
					}
				}
				return PROP_IGNORE;
				break;
			/*
			case "file_show":
			case "file_show2":
				$propname = ($prop["name"] == "file_show") ? "file" : "file2";
				$url = $this->get_url($arr["obj_inst"]->prop($propname));
				if ($url != '')
				{
					$prop['value'] = html::img(array('url' => $url));
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;
			*/

			case "file":
				if (is_oid($arr["obj_inst"]->id()) && file_exists($this->_mk_fn($arr["obj_inst"]->file)))
				{
					$prop["value"] = "<div id=\"image_".$arr["obj_inst"]->id()."\">";
					$prop["value"] .= image::make_img_tag_wl($arr["obj_inst"]->id());
					$url = $this->mk_my_orb("fetch_image_tag_for_doc", array("id" => $arr["obj_inst"]->id()));
					$image_url = $this->get_url_by_id($arr["obj_inst"]->id());
					$alias_url = $this->mk_my_orb("gen_image_alias_for_doc", array(
						"img_id" => $arr["obj_inst"]->id(),
						"close" => true,
					), CL_IMAGE);
					if($this->can("delete", $arr["obj_inst"]->id()))
					{
						$delete_url = $this->mk_my_orb("delete_image", array("id" => $arr["obj_inst"]->id()));
						$prop["value"] .= "<br />\n".html::href(array(
							"url" => "javascript:void(0)",
							"onclick" => "if(confirm(\"Oled kindel?\")){ $.ajax({url: \"{$delete_url}\"}); $(\"#image_".$arr["obj_inst"]->id()."\").hide(); }",
							"caption" => t("Kustuta pilt"),
						));
					}
					$prop["value"] .= "&nbsp;&nbsp;
						<script language=\"javascript\">
						function getDocID()
						{
							doc_id = 0;
							q = window.parent.location.href;
							ar = new Array();
							ar = q.split('&');
							for(i=0;i<ar.length;i++)
							{
								pair = ar[i].split('=');
								if(pair[0]=='doc')
								{
									doc_url = pair[1];
									break;
								}
							}
							ar = doc_url.split('%26');
							for(i=0;i<ar.length;i++)
							{
								pair=ar[i].split('%3D');
								if(pair[0]=='id')
								{
									doc_id = pair[1];
									break;
								}
							}
							return doc_id;
						}
					</script>
					";
					$prop["value"] .= "</div>";
				}
				else
				{
					$prop["value"] = "";
				}
				break;

			case "file2":
				$url = $this->get_url($arr["obj_inst"]->prop($prop["name"]));
				if ($url != '')
				{
					$prop['value'] = html::img(array('url' => $url));
				}
				else
				{
					$prop["value"] = "";
				}
				break;

			case "dimensions_big":
				$fl = $arr["obj_inst"]->prop("file2");
				if (!empty($fl))
				{
					if ($fl{0} != "/")
					{
						$fl = aw_ini_get("file.site_files_dir").$fl{0}."/".$fl;
					}
					$sz = getimagesize($fl);
					$prop["value"] = $sz[0] . " X " . $sz[1];
				}
				else
				{
					$retval = PROP_IGNORE;
				};
				break;
			case "dimensions":
				$fl = $arr["obj_inst"]->prop("file");
				if (!empty($fl))
				{
					// rewrite $fl to be correct if site moved
					$fl = basename($fl);
					$fl = aw_ini_get("file.site_files_dir").$fl{0}."/".$fl;
					$sz = getimagesize($fl);
					$prop["value"] = $sz[0] . " X " . $sz[1];
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "comments_tb":
				$this->_comments_tb($arr);
				break;

			case "comments_tbl":
				$this->_comments_tbl($arr);
				break;
		}

		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr['prop'];
		$retval = PROP_OK;
		switch ($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "newwindow":
			case "no_print":
				$retval = PROP_IGNORE;
				break;

			case "show_conditions":
				$arr['obj_inst']->set_prop("newwindow",isset($prop["value"]["newwindow"]) ? 1 : 0);
				$arr['obj_inst']->set_prop("no_print",isset($prop["value"]["no_print"]) ? 1 : 0);
				break;


			case "file":
			case "file2":
				$src_file = $ftype = "";
				$oldfile = $arr["obj_inst"]->prop($prop["name"]);
				if (isset($_FILES[$prop["name"]]["tmp_name"]) and is_uploaded_file($_FILES[$prop["name"]]["tmp_name"]))
				{
					// this happens if file is uploaded from the image class directly
					$src_file = $_FILES[$prop["name"]]["tmp_name"];
					$ftype = $_FILES[$prop["name"]]["type"];
				}
				elseif (!empty($prop["value"]["tmp_name"]))
				{
					// this happens if for example releditor is used
					$src_file = $prop["value"]["tmp_name"];
					$ftype = $prop["value"]["type"];
					// I'm not quite sure how the type can be empty, but the code was here before,
					// so it must be needed
					if (empty($ftype))
					{
						$ftype = "image/jpg";
					}
				}

				// if a file was found, then move it to wherever it should be located
				if (is_uploaded_file($src_file))
				{
					$_fi = new file();
					$final_name = $_fi->generate_file_path(array(
						"type" => $ftype,
					));
					move_uploaded_file($src_file, $final_name);

					if (function_exists("exif_read_data") and function_exists("strptime"))
					{
						$type = exif_imagetype($final_name);
						if (IMAGETYPE_JPEG === $type or IMAGETYPE_TIFF_II  === $type or IMAGETYPE_TIFF_MM  === $type)
						{
							$dat = exif_read_data($final_name);
							$dt = $dat["DateTime"];
							$dt = strptime($dt, "%Y:%m:%d %H:%M:%S");
							$this->_set_dt = $dt;
						}
					}

					// get rid of the old file
					if (file_exists($oldfile))
					{
						// also, we should check if any OTHER file objects point to this file.
						// if they do, then don't delete the old one. this is sort-of like reference counting:P
						// because copy/paste on images creates a new object that points to the same file.
						$ol = new object_list(array(
							"class_id" => CL_IMAGE,
							"file" => "%".basename($oldfile)."%",
							"oid" => new obj_predicate_not($arr["obj_inst"]->id())
						));
						if (!$ol->count())
						{
							unlink($oldfile);
						}
					}

					if ($arr["obj_inst"]->name() == "")
					{
						if ($prop["value"]["name"] != "")
						{
							$arr["obj_inst"]->set_name($prop["value"]["name"]);
						}
						else
						{
							$arr["obj_inst"]->set_name($_FILES[$prop["name"]]["name"]);
						}
					}
					$prop["value"] = $final_name;
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "date_taken":
				if ($this->_set_dt)
				{
					$prop["value"] = $this->_set_dt;
					$arr["obj_inst"]->set_prop("date_taken", $prop["value"]);
					return PROP_OK;
				}
				break;

			case "file2_del":
				if ($prop["value"] == 1)
				{
					$oldfile = $arr["obj_inst"]->prop("file2");
					if (file_exists($oldfile))
					{
						unlink($oldfile);
					}
					$arr["obj_inst"]->set_prop("file2","");
				}
				break;

			case "do_resize":
				$this->do_resize = true;
				break;

			case "new_w":
				$this->new_w = $prop["value"];
				break;

			case "new_h":
				$this->new_h = $prop["value"];
				break;

			case "new_h_big":
				$this->new_h_big = $prop["value"];
				break;

			case "new_w_big":
				$this->new_w_big = $prop["value"];
				break;
		}
		return $retval;
	}

	/** Adds an image to the system

		@attrib name=add_image params=name default="0" nologin="1" api=1 all_args=1 caption="foo" is_public=1

		@param from required type=string
			Method how image is passed to the function. Options: [file|string|url]
		@param str optional type=string
			if from value is "string", then this is the file content
		@param file optional type=string
			if from is file, then this is the filename for file content
		@param url optional type=string
			if from is url, then this is the url for file, will be downloaded
		@param orig_name optional type=string
			the original name of the file, used as the object name
		@param parent required type=int
			the folder where to save the image
		@param id optional type=int
			the id of the image to change

		@errors
			none

		@returns
			array with image data:

			Array
			(
				[id] => Image object id
				[url] => Image url
				[sz] => Image size
			)

		@comment
			none

		@examples
			none
	**/
	function add_image($arr)
	{
		extract($arr);
		if ($from == "file")
		{
			$str = $this->get_file(array("file" => $file));
		}

		if ($from == "url" && !empty($url))
		{
			$str = file_get_contents($url); // since php 4.3.0
		}

		if (!$id)
		{
			$img_obj = new object();
			$img_obj->set_parent($parent);
			$img_obj->set_class_id(CL_IMAGE);
			$img_obj->set_status(STAT_ACTIVE);
			$img_obj->set_name($orig_name);
			$img_obj->save();
			$oid = $img_obj->id();
		}
		else
		{
			$oid = $id;
		}

		$_fi = get_instance(CL_FILE);
		$mime = get_instance("core/aw_mime_types");
		$fl = $_fi->_put_fs(array(
			"type" => $mime->type_for_file($orig_name),
			"content" => $str
		));

		$this->db_query("UPDATE images SET file = '$fl' WHERE id = '$oid'");
		$sz = getimagesize($fl);
		return array("id" => $oid,"url" => $this->get_url($fl), "sz" => $sz);
	}


	/** Resizes picture

		@attrib name=resize_picture params=name api=1

		@param id required type=int
			image id
		@param file required type=string

		@param width required type=int
			new width of the picture
		@param height required type=int
			new height of the picture
		@errors
			none

		@returns
			none

		@comment
			after resizing picture converts all pictures to JPG format!
		@examples
			none

	**/
	function resize_picture(&$arr)
	{
		$im = $this->get_image_by_id($arr["id"]);
		$file = $arr['file'];

		$img = get_instance("core/converters/image_convert");
		$fn = basename($im[$file]);
		$fn = aw_ini_get("file.site_files_dir").$fn{0}."/".$fn;
		$img->load_from_file($fn);
		list($i_width, $i_height) = $img->size();
		$width = $arr['width'];
		$height = $arr['height'];

		if ($width && !$height)
		{
			if ($width{strlen($width)-1} == "%")
			{
				$height = $width;
			}
			else
			{
				$ratio = $width / $i_width;
				$height = (int)($i_height * $ratio);
				//$this->new_h = $height;
			}
		}

		if (!$width && $height)
		{
			if ($height{strlen($height)-1} == "%")
			{
				$width = $height;
			}
			else
			{
				$ratio = $height / $i_height;
				$width = (int)($i_width * $ratio);
				//$this->new_w = $width;
			}
		}

		if ($width{strlen($width)-1} == "%")
		{
			$width = (int)($i_width * (((int)substr($width, 0, -1))/100));
		}
		if ($height{strlen($height)-1} == "%")
		{
			$height = (int)($i_height * (((int)substr($height, 0, -1))/100));
		}
		if($i_width < $width && $i_height < $height)
		{
			$width = $i_width;
			$height = $i_height;
		}
		elseif($i_height < $height && $i_width > $width)
		{
			$ratio = $i_height / $height;
			$width = (int)($i_width * $ratio);
			$height = (int)($i_height * $ratio);
		}
		elseif($i_width < $width && $i_height > $height)
		{
			$ratio = $i_width / $width;
			$width = (int)($i_width * $ratio);
			$height = (int)($i_height * $ratio);
		}
		$img->resize_simple($width, $height);

		$this->put_file(array(
			'file' => $fn,
			"content" => $img->get(IMAGE_JPEG)
		));

	}

	function callback_pre_save($arr)
	{
		if ($this->_set_dt)
		{
			$arr["obj_inst"]->set_prop("date_taken", $this->_set_dt);
		}
	}

	function callback_post_save($arr)
	{
		if($this->new_h_big || $this->new_w_big)
		{
			$arr['file'] = 'file2';
			$arr['height'] = $this->new_h_big;
			$arr['width'] = $this->new_w_big;
			/*echo $arr['file'],":<br>";
			echo $arr['height'],"<br>";
			echo $arr['width'],"<br>";*/
			$this->resize_picture($arr);
		}

		if($this->new_w || $this->new_h)
		{
			$arr['file'] = 'file';
			$arr['height'] = $this->new_h;
			$arr['width'] = $this->new_w;
			/*echo $arr['file'],":<br>";
			echo $arr['height'],"<br>";
			echo $arr['width'],"<br>";*/
			$this->resize_picture($arr);
		}

		$this->do_apply_gal_conf(obj($arr["id"]));
		if (!empty($arr["request"]["save_and_doc"]))
		{
			$url = $this->mk_my_orb("fetch_image_alias_for_doc", array("doc_id" => $arr["request"]["docid"], "image_id" => $arr["obj_inst"]->id()));
			$image_url = $this->get_url_by_id($arr["obj_inst"]->id());
			$this->gen_image_alias_for_doc(array(
				"img_id" => $arr["obj_inst"]->id(),
				"doc_id" => $arr["request"]["docid"] ? $arr["request"]["docid"] : $arr["request"]["id"],
				"no_die" => 1
			));
			die("
				<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."automatweb/js/jquery/jquery-1.2.3.min.js\"></script>
				<script language='javascript'>

				FCK=window.parent.opener.FCK;

				var eSelected = FCK.Selection.GetSelectedElement() ;

				if (eSelected)
				{
					if (eSelected.tagName == 'TABLE' && eSelected._awimageplaceholder  )
					{
						$.get(\"$url\", function(data){
							window.parent.opener.FCKAWImagePlaceholders.Add(FCK, data);
							window.parent.close();
						});
					}
					// ok, this should never happen but still.. u never know
					else if (eSelected.tagName == 'IMG' )
					{
						$.get(\"$url\", function(data){
							window.parent.opener.FCKAWImagePlaceholders.Add(FCK, data);
							window.parent.close();
						});
					}
				}
				else
				{
					$.get(\"$url\", function(data){
						window.parent.opener.FCKAWImagePlaceholders.Add(FCK, data);
						window.parent.close();
					});
				}
			</script>
			");
		}

	}


	/** Adding comment

		@attrib name=submit_comment params=name nologin="1" api=1

		@param id required type=int
		@param comments optional type=int

		@returns
			URL to the big image view
	**/
	function submit_comment($arr)
	{
		// Submitted new comment
		if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($arr['image_comm']) && is_array($arr['image_comm']))
		{
			$img = $arr['image_comm']['obj_id'];
			$comment = $arr['image_comm']['comment'];
			$o_img = is_oid($img) ? obj($img) : null;
			if (is_object($o_img) && $o_img->class_id() == CL_IMAGE && $this->can("view", $img) && !empty($comment))
			{
				// Store comment
				classload("vcl/comments");
				$comm = get_instance(CL_COMMENT);
				$added = $comm->submit(array(
					'parent' => $img,
					'commtext' => htmlspecialchars($comment),
					'return' => "id",
				));
				return $this->mk_my_orb(CL_IMAGE, array(
					'comments' => 1,
					'id' => $img,
					'action' => "show_big",
				));
			}

		}
		return (aw_global_get("HTTP_REFERER"));

	}

	/** Shows the big image

		@attrib name=show_big params=name nologin="1" api=1

		@param id required type=int
		@param comments optional type=int
		@param minigal optional type=int
			this is for cases where picture is shown from minigallery, and prev/next buttons have to correspond to minigallery sort

	**/
	function show_big($arr)
	{
		// Defaults
		$comments = 0;
		$parse = "minimal"; // name of SUB in template
		extract($arr);

		$im = $this->get_image_by_id($id);
		$imo = obj($id);
		$this->read_any_template("show_big.tpl");
		$this->lc_load("image","lc_image");
		lc_site_load("image", $this);
		if ($this->can("view", $imo->prop("big_flash")))
		{
			$fli = get_instance(CL_FLASH);
			$this->vars(array(
				"FLASH" => $fli->view(array("id" => $imo->prop("big_flash")))
			));
		}
		else
		{
			if (empty($im['meta']['file2']) || !is_file($im['meta']['file2']))
			{
				$img_url = $im['url']; // Revert to small image
			}
			else
			{
				$img_url = $this->get_url($im["meta"]["file2"]);
			}
			$this->vars(array(
				"big_url" => $img_url,
			));
			$this->vars(array(
				"IMAGE" => $this->parse("IMAGE")
			));
		}
		if ($comments)
		{
			$parse = "with_comments";
			$comments = new comments();
			$ret_list = $comments->init_vcl_property(array(
				'property' => array(
					'name' => "image_comm",
					"no_form" => 1,
					'no_heading' => 1,
					'sort_by' => "created desc", // Newer first
					'edit' => $this->_check_comment_delete($imo),
				),
				'obj_inst' => obj($id),
			));
			//$ret_form = array();
			$ret_form = $comments->init_vcl_property(array(
				'property' => array(
					'name' => "image_comm",
					"only_form" => true,
					'no_heading' => 1,
					'textarea_cols' => 30,
					'textarea_rows' => 5,
				),
				'obj_inst' => obj($id),
			));
			$ret_form += array(
				'submitbtn' => array(
					'type' => "submit",
					'value' => t("Lisa"),
				),
			);
			$hc_inst = new htmlclient(array(
				'template' => "real_webform",
			));
			foreach (($ret_form + $ret_list) as $el)
			{
				$hc_inst->add_property($el);
			}
			$hc_inst->finish_output(array(
				'action' => 'submit_comment',
				'data' => array('orb_class' => 'image'),
			));
			$out = $hc_inst->get_result(array('form_only' => true));

			$this->vars(array(
				'comments'=> $out,
			));
		}

		if ($this->is_template("NEXT_LINK") && !empty($arr["minigal"]) && $this->can("view", $arr["minigal"]))
		{
			$set_next = null;
			$mg = get_instance(CL_MINI_GALLERY);
			$ob = obj($arr["minigal"]);
			$images = $mg->_pic_list($ob);
			$mg->ob = $ob;
			$images->sort_by_cb(array(&$mg, "__sort_imgs"));
			foreach($images->ids() as $im_id)
			{
				if ($set_next)
				{
					$im = obj($im_id);
					$fn = $this->_mk_fn($im->prop("file"));
					$sz = getimagesize($fn);
					$this->vars(array(
						"next_url" => aw_url_change_var("id", $im_id),
						"width" => $sz[0],
						"height" => $sz[1]
					));
					$this->vars(array(
						"NEXT_LINK" => $this->parse("NEXT_LINK")
					));
					break;
				}
				if ($im_id == $id)
				{
					if ($prev)
					{
						$im = obj($im_id);
						$fn = $this->_mk_fn($im->prop("file"));
						$sz = getimagesize($fn);
						$this->vars(array(
							"prev_url" => aw_url_change_var("id", $prev),
							"width" => $sz[0],
							"height" => $sz[1]
						));
						$this->vars(array(
							"PREV_LINK" => $this->parse("PREV_LINK")
						));
					}
					$set_next = true;
				}
				$prev = $im_id;
			}
		}
		if (!$this->is_template($parse))
		{
			die($this->parse());
		}
		die($this->parse($parse));
	}

	/**

		@attrib name=show_small params=name nologin="1"
		@param id required type=int
		@returns
		@comment

	**/
	function show_small($arr)
	{
		extract($arr);
		$im = obj($id);
		$this->read_any_template("show_big.tpl");
		$this->vars(array(
			"big_url" => $this->get_url($im->prop("file")),
		));
		die($this->parse());
	}

	function request_execute($obj)
	{
		$this->show(array(
			"file" => basename($obj->prop("file"))
		));
	}


	/** Get image url by image id

		@attrib name=get_url_by_id params=pos api=1

		@param id required type=int
			Image object's id
		@errors
			none

		@returns
			empty value if the image object has no view access, url to the image othervise

		@comment
			none

		@examples
			none
	**/
	function get_url_by_id($id)
	{
		if (!object_loader::can("view", $id))
		{
			return "";
		}
		$o = obj($id);
		$url = $this->get_url($o->prop("file"));
		return $this->check_url($url);
	}

	/** Get big image url by image id

		@attrib params=pos api=1

		@param id required type=int
			Image object's id
		@errors
			none

		@returns
			empty value if the image object has no view access, url to the big image othervise

		@comment
			none

		@examples
			none
	**/
	function get_big_url_by_id($id)
	{
		if (!$this->can("view", $id))
		{
			return "";
		}
		$o = obj($id);
		$url = $this->get_url($o->prop("file2"));
		return $this->check_url($url);
	}

	function _get_conf_for_folder($pt, $apply_image = false)
	{
		if (!is_oid($pt) || !$this->can("view", $pt))
		{
			return false;
		}

		$oc = obj($pt);
		$oc = $oc->path(array(
			"full_path" => true
		));

		$rv = false;
		if ($apply_image)
		{
			$appi = " AND apply_image = 1 ";
		}
		foreach($oc as $dat)
		{
			$q = "SELECT conf_id FROM gallery_conf2menu LEFT JOIN objects ON objects.oid = gallery_conf2menu.conf_id WHERE menu_id = '".$dat->id()."' AND objects.status != 0 $appi";
			if (($mnid = $this->db_fetch_field($q,"conf_id")))
			{
				$rv = $mnid;
			}
		}
		// that config object might have been deleted, check it and return false, if so
		if (!$this->can("view",$rv))
		{
			$rv = false;
		};
		return $rv;
	}

	/** Apply gallery conf to an image

		@attrib name=do_apply_gal_conf params=pos api=1

		@param o required type=object
			Image object
		@errors
			none

		@returns
			none

		@comment
			Applies the gallery configuration to an image. Gallery configuration is set to the image's parent.

		@examples
			none
	**/
	function do_apply_gal_conf($o)
	{
		if ($o->prop("no_apply_gal_conf"))
		{
			return;
		}
		$conf = $this->_get_conf_for_folder($o->parent(), true);
		if ($conf)
		{
			// resize image as conf says
			$this->do_resize_image(array(
				"o" => $o,
				"conf" => obj($conf)
			));
		}
	}

	/** Resizes images as conf says

		@attrib name=do_resize_image params=name api=1

		@param o required type=object
			Image object
		@param conf required type=object
			Gallery configuration object

		@errors
			none

		@returns
			none

		@comment
			Applies the gallery configuration to an image. Gallery configuration is set to the image's parent.

		@examples
			none
	**/
	function do_resize_image($arr)
	{
		extract($arr);
		// big first
		if (($conf->prop("v_width") || $conf->prop("v_height") || $conf->prop("h_width") || $conf->prop("h_height")))
		{
			$bigf = $o->prop("file2");

			if (file_exists($bigf))
			{
				$img = get_instance("core/converters/image_convert");
				$img->set_error_reporting(false);
				$img->load_from_file($bigf);
				if ($img->is_error())
				{
					$bigf = false;
				}
			}

			if($o->prop("file") != $o->meta("old_file") && $bigf)
			{
				// If we changed the small file, change the big file also!
				unlink($bigf);
				$bigf = false;
			}

			if (!$bigf)
			{
				// no big file, copy from small file
				$bigf = $o->prop("file");
				if ($bigf)
				{
					$f = get_instance(CL_FILE);
					$bigf = $f->_put_fs(array(
						"type" => "image/jpg",
						"content" => $this->get_file(array("file" => $bigf))
					));
					$o->set_prop("file2", $bigf);
					$o->save();
				}
			}

			if ($bigf)
			{
				// do the actual resize-file thingie
				$this->do_resize_file_in_fs($bigf, $conf, "");
			}
		}

		// now small
		$smallf = $o->prop("file");
		if (!$smallf)
		{
			// do copy-big-to-small
			$smallf = $o->prop("file2");
			if ($smallf)
			{
				$f = get_instance(CL_FILE);
				$smallf = $f->_put_fs(array(
					"type" => "image/jpg",
					"content" => $this->get_file(array("file" => $smallf))
				));
				$o->set_prop("file", $smallf);
				$o->save();
			}
		}

		if ($smallf)
		{
			$this->do_resize_file_in_fs($smallf, $conf, "tn_");
			// if controller is set, let it do it's thing
			if ($this->can("view", $conf->prop("controller")))
			{
				$ctr = obj($conf->prop("controller"));
				$ctr_i = $ctr->instance();
				$ctr_i->eval_controller_ref($ctr->id(), $conf, $smallf, $smallf);
			}
		}

	}

	/** Resizes images in filesystem

		@attrib name=do_resize_image_in_fs params=pos api=1

		@param file required type=string
			Image file
		@param conf required type=object
			Gallery configuration object
		@param prefix required type=string


		@errors
			none

		@returns
			none

		@comment
			none

		@examples
			none
	**/
	function do_resize_file_in_fs($file, $conf, $prefix)
	{
		$img = get_instance("core/converters/image_convert");
		$img->set_error_reporting(false);

		$img->load_from_file($file);

		// get image size
		list($i_width, $i_height) = $img->size();

		$conf_i = $conf->instance();
		$xyd = $conf_i->get_xydata_from_conf(array(
			"conf" => $conf,
			"prefix" => $prefix,
			"w" => $i_width,
			"h" => $i_height
		));

		if ($xyd["is_subimage"] && $xyd["si_width"] && $xyd["si_height"])
		{
			if ($conf->prop("resize_before_crop"))
			{
				if ($i_width > $xyd["si_width"] && $i_height > $xyd["si_height"])
				{
					$img->resize_simple($xyd["width"], $xyd["height"]);
				}
				$img->resize(array(
					"x" => $xyd["si_left"],
					"y" => $xyd["si_top"],
					"width" => $xyd["si_width"],
					"height" => $xyd["si_height"],
					"new_width" => $xyd["si_width"],
					"new_height" => $xyd["si_height"]
				));
			}
			else
			{
				// make subimage
				$img->resize(array(
					"x" => $xyd["si_left"],
					"y" => $xyd["si_top"],
					"width" => $xyd["si_width"],
					"height" => $xyd["si_height"],
					"new_width" => $xyd["width"],
					"new_height" => $xyd["height"]
				));
			}
		}
		else
		if ($xyd["width"] < $i_width && $xyd["height"] < $i_height)
		{
			$img->resize_simple($xyd["width"], $xyd["height"]);
		}

		$gv = get_instance(CL_GALLERY_V2);
		$img = $gv->_do_logo($img, $conf, $prefix);

		$img->save($file, IMAGE_JPEG);
	}

	/** Composes img tag with a link to the big image

		@attrib name=make_img_tag_wl params=pos api=1

		@param id required type=int
			Image object's id
		@param alt optional type=string default=NULL
			Images alternate text
		@param has_big_alt optional type=string default=NULL
			If big image is set, then this is the big image's alternate text.
		@param size optional type=array
			array(
				height => int,
				width => int
			)
			sets img tag height and width
		@param arguments optional type=array
			array(
				show_title => true	// default true
			)

		@errors
			none

		@returns
			HTML image tag, with link when big image is set

		@comment
			none

		@examples
			none
	**/
	function make_img_tag_wl($id, $alt = NULL, $has_big_alt = NULL, $size = array(), $arr = array())
	{
		static $that;
		if (!$that)
		{
			$that = get_instance(CL_IMAGE);
		}
		$u = $that->get_url_by_id($id);

		$o = obj($id);

		if ($alt === NULL)
		{
			$alt = $o->name();
		}

		if ($o->prop("file2") != "")
		{
			$file2 = basename($o->prop("file2"));
			$file2 = aw_ini_get("file.site_files_dir").$file2{0}."/".$file2;
			if ($has_big_alt !== NULL)
			{
				$alt = $has_big_alt;
			}
			$imagetag = image::make_img_tag($u, $alt, $size, $arr);

			$size = getimagesize($file2);

			$bi_show_link = $that->mk_my_orb("show_big", array("id" => $id), "image");
			$bi_link = "window.open(\"$bi_show_link\",\"popup\",\"width=".($size[0]).",height=".($size[1])."\");";

			$imagetag = html::href(array(
				"url" => "javascript:void(0)",
				"onClick" => $bi_link,
				"caption" => $imagetag,
				"title" => $alt
			));
		}
		else
		{
			$imagetag = image::make_img_tag($u, $alt, $size, $arr);
		}

		return $imagetag;
	}

	/** Composes javascript onClick code to open big image in popup window

		@attrib name=get_on_click_js params=pos api=1

		@param id required type=int
			Image object's id

		@errors
			none

		@returns
			Empty value when big image is not set
			javascript onclick code to open big image in popup window

		@comment
			none

		@examples
			none
	**/
	function get_on_click_js($id)
	{
		$o = obj($id);
		if ($o->prop("file2") == "")
		{
			return "";
		}

		$that = new image;

		$size = getimagesize(self::_get_fs_path($o->prop("file2")));
		$bi_show_link = $that->mk_my_orb("show_big", array("id" => $id), "image");
		return  "window.open(\"$bi_show_link\",\"popup\",\"width=".($size[0]).",height=".($size[1])."\");";
	}

	function mime_type_for_image($arr)
	{
	}

	function callback_mod_reforb(&$arr, $request)
	{
		if (isset($request["docid"])) $arr["docid"] = $request["docid"];
	}

	function callback_mod_retval(&$arr)
	{
		if (isset($arr["request"]["docid"])) $arr["args"]["docid"] = $arr["request"]["docid"];
	}

	function callback_mod_tab($arr)
	{
		if (!empty($_REQUEST["docid"]))
		{
			$arr["link"] = aw_url_change_var("docid", $_REQUEST["docid"], $arr["link"]);
		}
		if ($arr["id"] === "resize" || $arr["id"] === "resize_big")
		{
			$cv = new image_convert();
			$ret = $cv->can_convert();
			if ($ret)
			{
				$cv->set_error_reporting(false);

				$prop = "file2";
				if ($arr["id"] === "resize")
				{
					$prop = "file";
				}
				if ($arr["obj_inst"]->prop($prop) == "")
				{
					$ret = false;
				}
				else
				{
					$cv->load_from_file($this->_mk_fn($arr["obj_inst"]->prop($prop)));
					if ($cv->is_error())
					{
						$ret = false;
					}
				}
			}
			return $ret;
		}

		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_generate_scripts($arr)
	{
		return "
		if (window.parent.name == \"InsertAWImageCommand\")
		{
		nsbt = document.createElement('input');nsbt.name='save_and_doc';nsbt.type='submit';nsbt.id='button';nsbt.value='".t("Salvesta ja paiguta dokumenti")."'; el = document.getElementById('buttons');el.appendChild(nsbt);}";
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function _mk_fn($fn)
	{
		$ret = basename($fn);
		return $ret ? (aw_ini_get("file.site_files_dir").$ret{0}."/{$ret}") : "";
	}

	/**
		@attrib name=fetch_image_tag_for_doc params=name

		@param id required type=int
	**/
	function fetch_image_tag_for_doc($arr)
	{
		$s = $this->parse_alias(array("alias" => array("target" => $arr["id"]), "htmlentities" => true));
		die(str_replace("automatweb/", "", $s["replacement"]));
	}

	/**
		@attrib name=gen_image_alias_for_doc params=name
		@param img_id required type=int
		@param doc_id optional
		@param close optional type=bool
	**/
	function gen_image_alias_for_doc($arr)
	{
		$close = "<script language=\"javascript\">javascript:window.parent.close();</script>";
		if (!is_oid($arr["doc_id"]))
		{
			die($close);
		}
		$from = obj($arr["doc_id"]);
		$rv = $from->connect(array("to" => $arr["img_id"]));

		if (!$arr["no_die"])
		{
			die($arr["img_id"]);
		}
	}

	/**
		@attrib name=fetch_image_alias_for_doc
		@param doc_id required
		@param image_id required
	**/
	function fetch_image_alias_for_doc($arr)
	{
		$alp = new alias_parser();
		$alias_list = $alp->get_alias_list_for_obj_as_aliasnames($arr["doc_id"]);

		foreach($alias_list as $obj_id => $alias_string)
		{
			if ($obj_id == $arr["image_id"])
			{
				die(str_replace("#", "", $alias_string));
			}
		}

		$this->gen_image_alias_for_doc(array(
			"img_id" => $arr["image_id"],
			"doc_id" => $arr["doc_id"],
			"no_die" => true
		));

		$alias_list = $alp->get_alias_list_for_obj_as_aliasnames($arr["doc_id"]);

		foreach($alias_list as $obj_id => $alias_string)
		{
			if ($obj_id == $arr["image_id"])
			{
				die(str_replace("#", "", $alias_string));
			}
		}

		die();
	}

	/**
		@attrib name=get_connection_details_for_doc params=name
		@param doc_id required type=int
		@param alias_name required type=string
		@param use_br optional type=int
	**/
	function get_connection_details_for_doc($arr)
	{
		header("Content-type: application/javascript; charset=utf-8");
		if ($arr["use_br"])
		{
			$sufix = "\n";
		}
		$out = "";
		$alp = new alias_parser();
		$alias_list = $alp->get_alias_list_for_obj_as_aliasnames($arr["doc_id"]);
		$out = 'connection_details_for_doc = new Array();'.$sufix;
		foreach($alias_list as $obj_id => $alias_string)
		{
			$alias_name = preg_replace  ( "/^([a-z]*[0-9]{1,})[vkp]?$/isU", "\\1", $arr["alias_name"]);
			if ("#".$alias_name."#" == $alias_string)
			{
				$o = obj($obj_id);
				$size = getimagesize($this->_get_fs_path($o->prop("file")));
				$out .= 'var item = {"name" : "'.$o->name().'", "id" : '.$obj_id.', "comment" : "'.$o->prop("comment").'", "url" : "'.$this->get_url_by_id($obj_id).'", "width" : '.$size[0].', "height" : '.$size[1].'};'.$sufix;
				$out .= 'connection_details_for_doc["#'.$arr["alias_name"].'#"] = item;'.$sufix;
			}
		}
		$out = iconv(aw_global_get("charset"), "utf-8", $out);
		die($out);
	}

	function _comments_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_COMMENT),$arr["obj_inst"]->id());
		$tb->add_delete_button();
	}

	function _comments_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "body",
			"caption" => t("Sisu"),
		));
		$t->define_field(array(
			"name" => "author",
			"caption" => t("Autor"),
		));
		$t->define_field(array(
			"name" => "date",
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"caption" => t("Aeg"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$ol = new object_list(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => CL_COMMENT,
		));
		foreach($ol->arr() as $oid=>$o)
		{
			$author = $o->prop("uname");
			if (empty($author))
			{
				$author = $o->createdby();
			};
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"body" => nl2br(create_links($o->prop("commtext"))),
				"oid" => $oid,
				"author" => $author,
				"date" => $o->created(),
			));
		}
	}

	/**
	@attrib name=del_comment all_args=1
	**/
	function del_comment($arr)
	{
		if(is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
			$imo = obj($o->parent());
			$check = $this->_check_comment_delete($imo);
		}
		if($check)
		{
			$o->delete();
		}
		return $arr["return_url"];
	}

	function _check_comment_delete($imo)
	{
		$ii = get_instance(CL_IMAGE);
		$cfid = $ii->_get_conf_for_folder($imo->parent());
		$ui = get_instance(CL_USER);
		$curid = $ui->get_current_user();
		if(is_oid($curid) && is_oid($cfid))
		{
			$cur = obj($curid);
			$conn = $cur->connections_from(array(
				"type" => "RELTYPE_GRP"
			));
			$cfo = obj($cfid);
			$grps = $cfo->prop("comm_edit_grp");
			$edit_ok = 0;
			foreach($grps as $grp)
			{
				foreach($conn as $c)
				{
					if($c->prop("to") == $grp)
					{
						return true;
					}
				}
			}
		}
		return false;
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_date_taken":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	/**
		@attrib name=disp_longdesc nologin="1"
		@param id required type=int acl=view
	**/
	function disp_longdesc($arr)
	{
		$o = obj($arr["id"]);
		die($o->prop("longdesc"));
	}

	/**
		@attrib name=delete_image
		@param id required type=int acl=delete
	**/
	function delete_image($arr)
	{
		if($this->can("delete", $arr["id"]))
		{
			$this_o = obj($arr["id"]);
			unlink($this_o->prop("file"));
			unlink($this_o->prop("file2"));
			$this_o->set_prop("file", "");
			$this_o->set_prop("file2", "");
			$this_o->save();
		}
	}
}

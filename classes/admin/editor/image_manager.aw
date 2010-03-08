<?php
/*
@classinfo maintainer=kristo
*/
class image_manager extends aw_editor_manager_base
{
	function image_manager()
	{
		$this->init("admin/image_manager");
	}

	/**
		@attrib name=manage default=1
		@param doc required
		@param imgsrc optional
		@param image_id optional
	**/
	function manage($arr)
	{
		$url = $arr["imgsrc"];
		$parts = parse_url($url);
		$path = $parts["path"];
		if (substr($path, 0, 8) == "/vvfiles")
		{
			$imgname = basename($path);
		}
		else
		{
			$imgname = substr($path, strrpos($path, "=")+1);
		}
		if ($imgname != "")
		{
			// now get image by file name
			$image_list = new object_list(array(
				"class_id" => CL_IMAGE,
				"lang_id" => array(),
				"site_id" => array(),
				"file" => "%".trim($imgname)
			));
		}
		else
		{
			$image_list = new object_list();
		}

		if ($arr["doc"] == "http:")
		{
			parse_str(urldecode($_SERVER["QUERY_STRING"]), $params);
		}
		else
		{
			parse_str($arr["doc"], $params);
		}
		
		if (!$this->can("view", $params["id"]))
		{
			// use parent from url as doc
			$doc = obj($params["parent"]);
		}
		else
		{
			$doc = obj($params["id"]);
		}

		if ($arr["image_id"])
		{
			$image_url = html::get_change_url($arr["image_id"], array("in_popup" => $_GET["in_popup"], "docid" => $doc->id()));
		}
		else if ($image_list->count())
		{
			$imgo = $image_list->begin();
			$image_url = html::get_change_url($imgo->id(), array("in_popup" => $_GET["in_popup"]));
		}
		else
		{
			$parent = $this->get_def_img_folder_from_path(obj($doc->parent()));
			if (!$parent)
			{
				$parent = $doc->parent();
			}
			$image_url = html::get_new_url(CL_IMAGE, $parent, array("docid" => $doc->id(), "in_popup"=>"1"));
		}
		
		$this->read_template("manage.tpl");

		$this->vars(array(
			"topf" => $this->mk_my_orb("topf", $arr),
			"image" => $image_url
		));
		die($this->parse());
	}

	private function get_def_img_folder_from_path($o)
	{
		$ret = aw_ini_get("image.default_folder");
		$pt = $o->path();
		foreach($pt as $path_item)
		{
			if ($this->can("view", $path_item->prop("default_image_folder")) && ($path_item->prop("default_image_folder_is_inherited") || $path_item->id() == $o->id()))
			{
				$ret = $path_item->prop("default_image_folder");
			}
		}
		return $ret;
	}

	/**
		@attrib name=topf 
		@param doc required
	**/
	function topf($arr)
	{
		$this->read_template("top_frame.tpl");
		$parent = aw_ini_get("image.default_folder");
		parse_str($arr["doc"], $params);
		$doc = obj($params["id"]);
		if (!$parent)
		{
			$parent = $doc->parent();
		}
		$this->vars(array(
			"img_new" => html::get_new_url(CL_IMAGE, $parent, array("in_popup"=>"1")),
			"img_mgr" => $this->mk_my_orb("manager", array("docid" => $doc->id())),
			"new_img_t" => t("Uus pilt"),
			"existing_img_t" => t("Vali olemasolev pilt")
		));
		die($this->parse());
	}

	/**
		@attrib name=manager
		@param docid required
	**/
	function manager($arr)
	{
		classload("vcl/table");
		$t = new vcl_table;
		$this->_init_t($t, t("Pilt"));
		$this->read_template("manager.tpl");

		$ol = new object_list(array(
			"class_id" => CL_IMAGE,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => "%".$_GET["s"]["name"]."%",
			"limit" => ($_GET["s"]["name"] == "" || $_GET["s"]["last"]) ? 30 : NULL,
			"createdby" => ($_GET["s"]["my"])?aw_global_get("uid"):"%",
			"sort_by" => "objects.created DESC",
		));

		$ii = get_instance(CL_IMAGE);
		foreach($ol->arr() as $o)
		{
			$url = $this->mk_my_orb("fetch_image_alias_for_doc", array("doc_id" => $arr["docid"], "image_id" => $o->id()), CL_IMAGE);
			$pop_url = $ii->get_url_by_id($o->id());

			$image_url = $ii->get_url_by_id($o->id());
			$gen_alias_url = $this->mk_my_orb("gen_image_alias_for_doc", array(
				"img_id" => $o->id(),
				"doc_id" => $arr["docid"],
			), CL_IMAGE);
			$location = $this->gen_location_for_obj($o);
			
			$name = html::href(array(
				"caption" => $o->name(),
				"onmouseover" => "showThumb(event, \"".$pop_url."\");",
				"onmouseout" => "hideThumb();",
				"url" => $this->mk_my_orb("change", array(
					"id" => $o->id(),
					"return_url" => get_ru(),
					"in_popup" => 1,
				), CL_IMAGE),
			));
			$t->define_data(array(
				"name" => $name,
				"location" => $location,
				"sel" => html::href(array(
					"url" => "javascript:void(0)",
					"caption" => t("Vali see"),
					"onClick" => "
						FCK=window.parent.opener.FCK;
						var eSelected = FCK.Selection.GetSelectedElement() ; 
						if (eSelected)
						{
							if (eSelected.tagName == \"SPAN\" && eSelected._awimageplaceholder  )
							{
								$.get(\"$url\", function(data){
									window.parent.opener.FCKAWImagePlaceholders.Add(FCK, data);
									window.parent.close();
								});
							}
							else if (eSelected.tagName == \"IMG\" )
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
					"
				))
			));
		}
		$t->set_default_sortby("name");
		$t->sort_by();
		$this->vars(array(
			"body" => $this->draw_form($arr).$t->draw(),
		));
		return $this->parse();
	}
}
?>

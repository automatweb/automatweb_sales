<?php

/*
@classinfo maintainer=kristo
*/

class link_manager extends aw_editor_manager_base
{
	function link_manager()
	{
		$this->init("admin/link_manager");
	}

	/**
		@attrib name=manage default="1"
		@param doc required
		@param link_url optional
	**/
	function manage($arr)
	{
		$url = $arr["link_url"];
		$parts = parse_url($url);
		$path = $parts["path"];
		// if path contains class=file , then redir to file manager
		if (strpos($path, "class=file") !== false)
		{
			header("Location: ".$this->mk_my_orb("manage", $arr, "file_manager"));
			die();
		}
		
		$imgname = substr($path, 1);
		if ($imgname != "")
		{
			// now, if the ini file says, that links are direct, then find the link by url, else we have the link oid
			if (aw_ini_get("extlinks.directlink") == 1)
			{
				// now get image by file name
				$image_list = new object_list(array(
					"class_id" => CL_EXTLINK,
					"lang_id" => array(),
					"site_id" => array(),
					"url" => $arr["link_url"]
				));
			}
			else
			{
				$image_list = new object_list(array(
					"oid" => $imgname,
					"lang_id" => array(),
					"site_id" => array()
				));
			}
		}
		else
		{
			$image_list = new object_list();
		}

		$parent = aw_ini_get("links.default_folder");
		parse_str($arr["doc"], $params);
		$doc = obj($params["id"]);
		if (!$parent)
		{
			$parent = $doc->parent();
		}
		if (!$parent)
		{
			// parse the documents parent from the url
			$parent = $params["parent"];
		}

		if ($image_list->count())
		{
			$imgo = $image_list->begin();
			$image_url = html::get_change_url($imgo->id(),  array("in_popup" => $_GET["in_popup"], "ldocid" => $doc->id()));
		}
		else
		{
			$image_url = html::get_new_url(CL_EXTLINK, $parent, array("ldocid" => $doc->id(), "in_popup"=>"1"));
		}
		
		$this->read_template("manage.tpl");

		$this->vars(array(
			"topf" => $this->mk_my_orb("topf", $arr),
			"image" => $image_url
		));
		die($this->parse());
	}

	/**
		@attrib name=topf 
		@param doc required
	**/
	function topf($arr)
	{
		$this->read_template("top_frame.tpl");
		$parent = aw_ini_get("links.default_folder");
		parse_str($arr["doc"], $params);
		$doc = obj($params["id"]);
		if (!$parent)
		{
			$parent = $doc->parent();
		}
		$this->vars(array(
			"img_new" => html::get_new_url(CL_EXTLINK, $parent),
			"img_mgr" => $this->mk_my_orb("manager", array("docid" => $doc->id())),
			"new_link_t" => t("Uus link"),
			"existing_link_t" => t("Vali olemasolev link")
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
		$this->_init_t($t, t("Link"));
		
		$ol = new object_list(array(
			"class_id" => CL_EXTLINK,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => "%".$_GET["s"]["name"]."%",
			"url" => "%".$_GET["s"]["url"]."%",
			"limit" => (($_GET["s"]["name"] == "" && $_GET["s"]["url"] == "") || $_GET["s"]["last"]) ? 30 : NULL,
			"createdby" => ($_GET["s"]["my"])?aw_global_get("uid"):"%",
			"sort_by" => "objects.created DESC",
		));
		$ii = get_instance(CL_EXTLINK);
		foreach($ol->arr() as $o)
		{
			$url = $this->mk_my_orb("fetch_file_tag_for_doc", array("id" => $o->id()), CL_FILE);
			if (aw_ini_get("extlinks.directlink") == 1)
			{
				$link_url = $o->prop("url");
			}
			else
			{
				$link_url = obj_link($o->id());
			}
			$gen_alias_url = $this->mk_my_orb("gen_link_alias_for_doc",array(
				"doc_id" => $arr["docid"],
				"link_id" => $o->id(),
			), CL_EXTLINK);
			//$link_name = html_entity_decode(str_replace("\"", "\\\"", $o->name()));
			$link_name = str_replace("\"", "\\\"", $o->name());
			$location = $this->gen_location_for_obj($o);
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"location" => $location,
				"sel" => html::href(array(
					"url" => "javascript:void(0)",
					"caption" => t("Vali see"),
					"onClick" => "
						FCK=window.parent.opener.FCK;
						var eSelected = FCK.Selection.MoveToAncestorNode(\"A\") ; 
						aw_get_url_contents(\"".$gen_alias_url."\");
						if (eSelected)
						{
							eSelected.href=\"$link_url\";
							eSelected.innerHTML=\"$link_name\";
							SetAttribute( eSelected, \"_fcksavedurl\", \"$link_url\" ) ;
						}
						else
						{
							ct=aw_get_url_contents(\"$url\");
							FCK.InsertHtml(ct);		
						}
						window.parent.close();
					"
				))
			));
		}
		$t->set_default_sortby("name");
		$t->sort_by();
		return "<script language=javascript>function SetAttribute( element, attName, attValue ) { if ( attValue == null || attValue.length == 0 ) {element.removeAttribute( attName, 0 ) ;} else {element.setAttribute( attName, attValue, 0 ) ;}}</script> ".$this->draw_form($arr).$t->draw();
	}

	protected function _get_searchable_props()
	{
		$rv = parent::_get_searchable_props();
		$rv[] = array(
			"name" => "s[url]",
			"type" => "textbox",
			"caption" => t("URL"),
			"value" => $_GET["s"]["url"]
		);
		return $rv;
	}
}
?>

<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/xml_output.aw,v 1.4 2008/01/31 13:52:15 kristo Exp $
// xml_output.aw - XML V&auml;ljund 
/*

@classinfo syslog_type=ST_XML_OUTPUT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

*/

class xml_output extends class_base
{
	function xml_output()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/xml_output",
			"clid" => CL_XML_OUTPUT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
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

	/**
		@attrib name=xml nologin="1"
		@param params required
		@param parent required 
		@param status optional
		@param recursive optional
		@param class_id required
	**/
	function xml($arr)
	{
		// so, make the filter 
		if ($arr["recursive"] == 1)
		{
			$this->recursive_xml($arr);
		}
		
		if ($arr["class_id"] == CL_DOCUMENT)
		{
			$ss = get_instance("contentmgmt/site_show");
			$ss->_init_path_vars();
			$docs = $ss->get_default_document(array(
				"obj" => obj($arr["parent"])
			));
			$data = array();
			if (is_array($docs))
			{
				foreach($docs as $docid)
				{
					$data[] = obj($docid);
				}
			}
			else
			if ($this->can("view", $docs))
			{
				$data[] = obj($docs);
			}

			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<objects>\n";
			foreach($data as $o)
			{
				$xml .= "\t<object id=\"".$o->id()."\">\n";
				$xml .= "\t\t<lead_url>".$this->_format($this->mk_my_orb("format_doc_content", array("id" => $o->id(), "leadonly" => 1)))."</lead_url>\n";
				$xml .= "\t\t<long_url>".$this->_format($this->mk_my_orb("format_doc_content", array("id" => $o->id())))."</long_url>\n";
				$xml .= "\t</object>";
			}
			$xml .= "</objects>";
			header("Content-type: text/xml; charset=utf-8");
			die($xml);
		}
		else
		{
			$filt = array(
				"parent" => $arr["parent"],
				"class_id" => $arr["class_id"]
			);
			if (!empty($arr["status"]))
			{
				$filt["status"] = $arr["status"];
			}
			$ol = new object_list($filt);
			$data = $ol->arr();
		}
		$params = explode(",", $arr["params"]);

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<objects>\n";
		foreach($data as $o)
		{
			$xml .= "\t<object id=\"".$o->id()."\">\n";
			foreach($params as $prop)
			{
				if (method_exists($o, $prop))
				{
					$val = $o->$prop();
				}
				else
				{
					$val = $o->prop($prop);
				}
				$alp = get_instance("alias_parser");
				$alp->parse_oo_aliases($o->id(), $val);
				$xml .= "\t\t<$prop>".$this->_format($val)."</$prop>\n";
			}
			$xml .= "\t</object>";
		}
		$xml .= "</objects>";
		header("Content-type: text/xml; charset=utf-8");
		die($xml);
	}

	function recursive_xml($arr)
	{
		if ($arr["class_id"] == 1)
		{
			return $this->menu_structure($arr);
		}
		$filt = array(
			"parent" => $arr["parent"],
			"class_id" => $arr["class_id"]
		);
		if (!empty($arr["status"]))
		{
			$filt["status"] = $arr["status"];
		}
		$ol = new object_tree($filt);
		$params = explode(",", $arr["params"]);

		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<objects>\n";

		$this->_req_xml($ol, $arr["parent"], $xml, $params);

		$xml .= "</objects>";
		header("Content-type: text/xml; charset=utf-8");
		die($xml);
	}

	function _req_xml($ol, $pt, &$xml, $params)
	{
		foreach($ol->level($pt) as $o)
		{
			$xml .= "\t<object id=\"".$o->id()."\">\n";
			foreach($params as $prop)
			{
				if (method_exists($o, $prop))
				{
					$xml .= "\t\t<$prop>".$this->_format($o->$prop())."</$prop>\n";
				}
				else
				{
					$xml .= "\t\t<$prop>".$this->_format($o->prop($prop))."</$prop>\n";
				}
			}
			
			$this->_req_xml($ol, $o->id(), $xml, $params);
			$xml .= "\t</object>";
		}
	}

	function _format($val)
	{
		$rv =  iconv(aw_global_get("charset"), "utf-8", str_replace(
			"&", "&", 
				$val
		));
		if (strpos($rv, "<") !== false || strpos($rv, "&") !== false)
		{
			$rv = "<![CDATA[".$rv."]]>";
		}
		return $rv;
	}

	function menu_structure($arr)
	{	
		$params = explode(",", $arr["params"]);
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<objects>\n";
		$this->_req_menus_xml($arr["parent"], $xml, $params, $arr);
		$xml .= "</objects>";
		header("Content-type: text/xml; charset=utf-8");
		die($xml);
	}

	function _req_menus_xml($pt, &$xml, $params, $arr)
	{
	        $parent_obj = obj($pt);
		$this->p_is_o_63_1[$parent_obj->id()] = NULL;
		if ($this->can("view", $parent_obj->prop("submenus_from_menu")))
		{
		        $parent_obj = obj($parent_obj->prop("submenus_from_menu"));
		}
		if ($parent_obj->prop("submenus_from_obj") || !empty($this->p_is_o_63_0[$parent_obj->id()]))
		{
		        if (!empty($this->p_is_o_63_0[$parent_obj->id()]))
		        {
                		$tmp = $this->p_is_o_63_0[$parent_obj->id()];
		        }
		        else
		        {
		                if ($this->can("view", $parent_obj->prop("submenus_from_obj")))
		                {
        		                $tmp = obj($parent_obj->prop("submenus_from_obj"));
                		        $this->p_is_o_level_63 = 1;
		                }
        		}
		        $this->p_is_o_63_1[$parent_obj->id()] = $tmp;
		        $o_obj_from = get_instance($tmp->class_id());

		        if (method_exists($o_obj_from, "make_menu_link"))
		        {
        		        $inst_63_1 =& $o_obj_from;
                		$fun_63_1 = "make_menu_link";
		        }
        		else
	        	{
	        	        $inst_63_1 =& $this;
                		$fun_63_1 = "make_menu_link";
		        }

		        if (method_exists($o_obj_from, "make_menu_item"))
        		{
                		$inst_63_1 =& $o_obj_from;
	                	$fun_63_1 = "make_menu_item";
	        	}
		        else
        		{
                		$list_63_1 = $o_obj_from->get_folders_as_object_list($tmp,1 - $this->p_is_o_level_63,$parent_obj);
		        }

		        $this->os_63_1 = true;
		}
		else
		if (!($parent_obj->prop("show_object_tree")  || !empty($this->ot_63_0)))
		{
		        $__list_filter = array(
                		"parent" => $parent_obj->brother_of(),
		                "class_id" => array(CL_MENU,CL_BROTHER),
		                "status" => STAT_ACTIVE,
		                new object_list_filter(array(
		                        "logic" => "OR",
                		        "conditions" => array(
		                                "lang_id" => aw_global_get("lang_id"),
                		                "type" => array(MN_CLIENT,MN_PMETHOD)
		                        )
                		)),
		                "lang_id" => array(),
                		"sort_by" => ($parent_obj->prop("sort_by_name") ? "objects.name" : "objects.jrk,objects.created"),
		        );
		        $list_63_1 = new object_list($__list_filter);
		        $inst_63_1 =& $this;
		        $fun_63_1 = "make_menu_link";
		}
		else
		{
		        $o_treeview = get_instance("contentmgmt/object_treeview");
		        $list_63_1 = $o_treeview->get_folders_as_object_list($parent_obj);
		        $inst_63_1 =& $o_treeview;
		        $fun_63_1 = "make_menu_link";
		        $this->ot_63_1 = true;
		}

		foreach($list_63_1->arr() as $o)
		{
			$xml .= "\t<object id=\"".$o->id()."\">\n";
			foreach($params as $prop)
			{
				if (method_exists($o, $prop))
				{
					$xml .= "\t\t<$prop>".$this->_format($o->$prop())."</$prop>\n";
				}
				else
				{
					$xml .= "\t\t<$prop>".$this->_format($o->prop($prop))."</$prop>\n";
				}
			}
			
			$this->_req_menus_xml($o->id(), $xml, $params, $arr);
			$xml .= "\t</object>";
		}
	}

	/**
		@attrib name=format_doc_content
		@param id required type=int acl=view
		@param leadonly optional type=int 
	**/
	function format_doc_content($arr)
	{
		$o = obj($arr["id"]);
		$dd = get_instance("doc_display");
		die($dd->gen_preview(array(
			"docid" => $o->id(),
			"tpl" => $arr["leadonly"] ? "xml_output_leadonly.tpl" : "xml_output.tpl",
			"leadonly" => $arr["leadonly"]
		)));
	}
}
?>

<?php
/*
@classinfo syslog_type=ST_CONTENTS relationmgr=yes no_status=1  maintainer=kristo

@default table=objects
@default group=general

@property intro_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Juhtkirja kataloog

@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog

*/

class contents extends class_base
{
	function contents()
	{
		$this->init(array(
			"tpldir" => "contents",
			"clid" => CL_CONTENTS
		));

		$this->mned = get_instance("contentmgmt/site_show");
		$this->doc = get_instance(CL_DOCUMENT);
		$this->per = get_instance(CL_PERIOD);
	}

	/**  
		@attrib name=show params=name nologin="1" is_public="1" caption="Show" default="1"
		
		@param leadonly optional type=int
		@param max optional type=int
		@param tpl optional
		@param d_tpl optional
	**/
	function show($arr = array())
	{
		extract($arr);
		if (isset($arr["tpl"]))
		{
			$tpl = $arr["tpl"];
		}
		else
		{
			$tpl = "show.tpl";
		}
		$this->read_template($tpl);

		$this->d_tpl = isset($d_tpl) && $d_tpl != "" ? basename($d_tpl) : "lead.tpl";
		
		$this->period = aw_global_get("act_per_id");
		if ($this->period < 2)
		{
			$this->period = 0;
		}
		
		$this->count = 0;
		$this->max_count = $arr["max"];
		
		if (!isset($arr["leadonly"]))
		{
			$this->leadonly = "1";
		}
		else
		{
			$this->leadonly = $arr["leadonly"];
		}

		$mareas = aw_ini_get("menuedit.menu_defs");
		$morder = aw_ini_get("contents.menu_order");

		if (is_array($morder))
		{
			//foreach($mareas as $pid => $an)
			foreach($morder as $order => $mname)
			{
				// now find the id from the menu_defs 
				$pid = array_search($mname, $mareas);
				$this->req_menus($pid);
			}
		}
		else
		{
			if (aw_ini_get("menuedit.lang_defs"))
			{
				foreach($mareas[aw_global_get("lang_id")] as $pid => $an)
				{
					$this->req_menus($pid);
				}
			}
			else
			{
				foreach($mareas as $pid => $an)
				{
					$this->req_menus($pid);
				}
			}
		}

		if ($arr["id"])
		{
			$o = obj($arr["id"]);
			$ld = $o->prop("intro_folder");
		}
		else
		{
			$ld = $this->get_cval("contents::document");
		}
		if ($ld)
		{
			$ol = new object_list(array(
				"class_id" => array(CL_DOCUMENT, CL_PERIODIC_SECTION, CL_BROTHER_DOCUMENT),
				"period" => $this->period, 
				"parent" => $ld,
				"status" => array(STAT_ACTIVE),
			));
			if ($ol->count() > 0)
			{
				$o = $ol->begin();
				
				$this->vars(array(
					"last_doc" => $this->doc->gen_preview(array(
						"docid" => $o->id(),
						"tpl" => "lead.tpl",
						"leadonly" => 1
					))
				));
			}
		}
		
		$act_per = $this->per->get($this->period);

		if (!empty($act_per["data"]["image"]))
		{
			$img = get_instance(CL_IMAGE);
			$dat = $img->get_image_by_id($act_per["data"]["image"]);
			$imgurl = $dat["url"];
		}
		else
		{
			$imgurl = $this->cfg["baseurl"] . "/automatweb/images/trans.gif";
		};

		$this->vars(array(
			"act_per_comment" => $act_per["comment"],
			"act_per_name" => $act_per["name"],
			"act_per_image_url" => $imgurl, 
			"MENU" => $this->l,
			"charset" => aw_global_get("charset")
		));

		return $this->parse();
	}

	private function req_menus($pid)
	{
		if (!is_oid($pid))
		{
			return;
		}

		// TODO: optimize this not to recurse and use object_tree to make the contents list
		$ol = new object_list(array(
			"parent" => $pid,
			"class_id" => CL_MENU,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"lang_id" => aw_global_get("lang_id"),
					"type" => MN_CLIENT
				)
			)),
			"status" => STAT_ACTIVE,
			"sort_by" => "objects.parent, jrk,objects.created"
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$s = "";

			// now docs under the menu
			$filta = array(
				"class_id" => array(CL_DOCUMENT, CL_PERIODIC_SECTION, CL_BROTHER_DOCUMENT),
				"parent" => $o->id(),
				"status" => array(STAT_ACTIVE),
				"sort_by" => "objects.jrk"
			);
			if ($this->period > 1)
			{
				$filta["period"] = $this->period;
			}

			$docl = new object_list($filta);
			for($d = $docl->begin(); !$docl->end(); $d = $docl->next())
			{
				$this->vars(array(
					"doc" => $this->doc->gen_preview(array(
						"docid" => $d->id(),
						"tpl" => $this->d_tpl,
						"leadonly" => $this->leadonly
					))
				));
				$s.=$this->parse("STORY");
			}
			$this->vars(array(
				"menu_name" => $o->name(),
				"menu_link" => $this->mned->make_menu_link($o),
				"STORY" => $s
			));
			$this->count++;
			if (($this->max_count > 0 ) && ($this->count > $this->max_count))
			{
				return;
			}
			if ($s != "")
			{
				$this->l.=$this->parse("MENU");
			}
			$this->req_menus($o->id());
		}
	}
}
?>

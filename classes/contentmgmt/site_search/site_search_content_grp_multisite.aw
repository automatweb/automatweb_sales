<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/site_search/site_search_content_grp_multisite.aw,v 1.5 2008/07/31 12:35:47 markop Exp $
// site_search_content_grp_multisite.aw - Saidi sisu otsingu grupp mitu saiti 
/*

@classinfo syslog_type=ST_SITE_SEARCH_CONTENT_GRP_MULTISITE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

	@property sel_grps type=relpicker multiple=1 reltype=RELTYPE_GRP field=meta method=serialize
	@caption Otsingu grupid

@reltype GRP value=1 clid=CL_SITE_SEARCH_CONTENT_GRP_HTML,CL_SITE_SEARCH_CONTENT_GRP
@caption Otsingu grupp

*/

class site_search_content_grp_multisite extends class_base
{
	function site_search_content_grp_multisite()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_search/site_search_content_grp_multisite",
			"clid" => CL_SITE_SEARCH_CONTENT_GRP_MULTISITE
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

        function scs_get_search_results($r)
        {
		if(!$r["opts"]["limit"])
		{
			$r["opts"]["limit"] = $r["obj"]->prop("max_num_results");
		}
		$go = obj($r["group"]);
                $i = get_instance(CL_SITE_SEARCH_CONTENT);
		$gps = array();
		foreach(safe_array($go->prop("sel_grps")) as $sel_grp)
		{
			if ($this->can("view", $sel_grp))
			{
				$go = obj($sel_grp);
				if ($go->class_id() == CL_SITE_SEARCH_CONTENT_GRP)
				{
					$gps[] = $go->site_id();
				}
				else
				{
					$gps[] = $go->id();
				}
			}
		}
                return $i->fetch_static_search_results(array(
                        "str" => $r["str"],
                        "no_lang_id" => true,
                        "site_id" => $gps,
			"opts" => $r["opts"]
                ));
        }
	
}
?>

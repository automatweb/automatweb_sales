<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_db_search.aw,v 1.12 2009/03/12 11:51:28 instrumental Exp $
// crm_db_search.aw - Kliendibaasi otsingu grupp 
/*

@classinfo syslog_type=ST_CRM_DB_SEARCH relationmgr=yes no_comment=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property crm_db type=relpicker reltype=RELTYPE_CRM_DB automatic=1 field=meta method=serialize
@caption Kliendibaas

@property url type=textbox
@caption Url, millele lisatakse org. objekti id

@property only_active type=checkbox ch_value=1 field=meta method=serialize
@caption Ainult aktiivsed

@property dir_tegevusala type=relpicker reltype=RELTYPE_TEGEVUSALA_CAT multiple=1
@caption Tegevusalade kaust

@property sector_recursive type=checkbox ch_value=1
@caption Otsi ka alategevusaladest

@property keywords type=textbox field=meta method=serialize
@caption V&ouml;tmes&ouml;nad (komadega eraldatult)

@property keywords2 type=relpicker multiple=1 field=meta method=serialize reltype=RELTYPE_KEYWORD
@caption AW V&otilde;tmes&otilde;nad

@property keywords_in_row type=textbox field=meta method=serialize
@caption V&otilde;tmes&otilde;nu &uuml;hel real

@property keywords_by_folder type=checkbox field=meta method=serialize
@caption V&otilde;tmes&otilde;nad kaustade kaupa

@reltype CRM_DB value=1 clid=CL_CRM_DB
@caption Organisatsioonide andmebaas

@reltype TEGEVUSALA_CAT value=8 clid=CL_MENU
@caption Tegevusalade kaust

@reltype KEYWORD value=2 clid=CL_MENU
@caption V&otilde;tmes&otilde;na


*/

class crm_db_search extends class_base
{
	const AW_CLID = 1049;

	function crm_db_search()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_db_search",
			"clid" => CL_CRM_DB_SEARCH
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	// For site search content
	function scs_get_search_results($arr)
	{
		$searcher = $arr['obj'];
		$oid = $arr['group'];
		$o = obj($oid);
		if (!is_oid($o->prop('crm_db')))
		{
			return;
		}
		
		$wvinst = get_instance(CL_CRM_COMPANY_WEBVIEW);
		$list = $wvinst->_list_companies(array(
			'crm_db' => $o->prop('crm_db'),
			'limit_plaintext' => $arr['str'],
			"only_active" => $o->prop("only_active"),
			"sector_recursive" => $o->prop("sector_recursive"),
			"field" => $arr["field"],
			"keyword" => $arr["keyword"],
			"area" => $arr["area"],
			"county" => $arr["county"],
			"city" => $arr["city"],
		));
		return $list;
	}

	function scs_display_search_results($arr)
	{
		$ob = obj($arr['group']);
		$url = $ob->prop('url');
		if (empty($url))
		{
			$url = '/org?org=';
		}
		$c = count($arr['results']);
		$out .= sprintf(t("Otsisid '<b>%s</b>', "), $arr['str']);
		$out .= sprintf( $c == 1 ? t('leiti %u asutus.') : t('leiti %u asutust.'), count($arr['results']));
		$out .= '<br>';
	
		$wvinst = get_instance(CL_CRM_COMPANY_WEBVIEW);
		$wvinst->read_template("default.tpl");
		$wvinst->vars(array(
			"res_count_row" => $out,
			"count" => count($arr["results"]),
		));
		$search_args = array(
			"str" => false,
			"field" => true,
			"county" => true,
			"city" => true
		);
		foreach($search_args as $search_arg => $is_oid)
		{
			if(!empty($arr[$search_arg]))
			{
				$wvinst->vars(array(
					$search_arg => $is_oid && $this->can("view", $arr[$search_arg]) ? obj($arr[$search_arg])->trans_get_val("name") : $arr[$search_arg],
				));
				$wvinst->vars(array(
					strtoupper($search_arg) => $wvinst->parse(strtoupper($search_arg)),
				));
			}
		}
		$wvinst->vars(array(
			"company_search_results_overview" => $wvinst->parse("company_search_results_overview")
		));

		$out = $wvinst->_get_companies_list_html(array(
			'list' => $arr['results'],
			'do_link' => true,
			'url' => $url,
		));
		return $out;
	}
		
}

?>

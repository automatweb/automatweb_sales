<?php

namespace automatweb;

/*

@classinfo syslog_type=ST_SITE_SEARCH maintainer=kristo

@groupinfo general caption=Üldine

@default table=objects
@default group=general

*/

class site_search extends class_base
{
	const AW_CLID = 205;

	function site_search()
	{
		$this->init(array(
			'tpldir' => 'layout/site_search',
			'clid' => CL_SITE_SEARCH
		));
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);
		return $this->show(array('id' => $alias['target']));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		extract($arr);

		$this->read_template('show.tpl');

		classload("layout/active_page_data");
		$this->vars(array(
			'section' => active_page_data::get_active_section()
		));

		return $this->parse();
	}
}
?>

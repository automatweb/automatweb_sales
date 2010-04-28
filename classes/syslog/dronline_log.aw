<?php

namespace automatweb;

/*
@classinfo  maintainer=kristo

@default table=objects

@property status type=status field=status
@caption Staatus

*/

class dronline_log extends class_base
{
	const AW_CLID = 164;

	function dronline_log()
	{
		$this->init(array(
			'tpldir' => 'syslog/dronline_log',
			'clid' => CL_DRONLINE_LOG
		));
	}

	function change(&$arr)
	{
		extract($arr);
		$ob = $this->_change_init($arr, 'AW_Log');

		$fn = '_do_'.($ob['meta']['dro_type'] != "" ? $ob['meta']['dro_type'] : "dronline");

		$dro = get_instance('syslog/dronline');
		$ret = $dro->$fn(array(
			'query' => $ob['meta']['query'],
			'cur_range' => $ob['meta']['cur_range']
		));

		return $ob['meta']['conf_desc'].$ret;
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

	function show($arr)
	{
		return $this->change($arr);
	}
}
?>

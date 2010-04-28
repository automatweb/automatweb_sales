<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/install/aw_site_gen_log.aw,v 1.5 2008/01/31 13:54:40 kristo Exp $
/*

@classinfo syslog_type=ST_AW_SITE_GEN_LOG maintainer=kristo

@groupinfo view caption="Vaata logi"

@default table=objects
@default group=general

@property show type=text field=meta method=serialize group=view no_caption=1

*/
class aw_site_gen_log extends class_base
{
	const AW_CLID = 204;

	function aw_site_gen_log()
	{
		$this->init(array(
			'clid' => CL_AW_SITE_GEN_LOG
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

	function show($arr)
	{
		extract($arr);
		$ob = new object($id);

		$t = new aw_table(array("layout" => "generic"));

		$df = aw_ini_get('config.dateformats');

		$t->define_field(array(
			'name' => 'tm',
			'caption' => t('Millal'),
			'sortable' => 1,
			'numeric' => 1,
			'type' => 'time',
			'format' => $df[2],
			'nowrap' => 1
		));

		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Kes"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "msg",
			"caption" => t("Mida"),
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus"),
			"sortable" => 1
		));
	
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"sortable" => 1
		));

		$ar = new aw_array($ob->meta('log'));
		foreach($ar->get() as $idx => $row)
		{
			$row["id"] = $idx;
			$t->define_data($row);
		}

		$t->set_default_sortby("id");
		$t->sort_by();
		return $t->draw();
	}

	////
	// !start site gen log
	// parameters:
	//	parent - where to create log
	//	name - log object name
	function start_log($arr)
	{
		extract($arr);
		if (!$parent)
		{
			$this->raise_error(ERR_SG_NO_PARENT, t("Saidi logi alustemisele ei antud parent menyy idd!"), false, true);
		}

		$this->cur_log_obj = new object;
		$this->cur_log_obj->set_class_id($this->clid);
		$this->cur_log_obj->set_parent($parent);
		$this->cur_log_obj->set_name($name);
		$this->cur_log_obj->set_status(STAT_ACTIVE);

		$this->log_entries = array();
	}


	//// 
	// !adds a line to the current site log object
	// parameters:
	//	uid - who did
	//	msg - did what
	//	comment - comment
	//	result
	function add_line($arr)
	{
		$arr["tm"] = time();
		$this->log_entries[] = $arr;
	}

	function finish_log()
	{
		if (!$this->cur_log_obj)
		{
			$this->raise_error(ERR_NO_LOG, t("Logimist pole alustatud!"), false, true);
		}

		$this->cur_log_obj->set_meta("log",$this->log_entries);
		$this->cur_log_obj->save();
	}

	function get_property($arr)
	{
		if ($arr['prop']['name'] == "show")
		{
			$arr['prop']['value'] = $this->show(array("id" => $arr['obj_inst']->id()));
		}
		return PROP_OK;
	}
}
?>

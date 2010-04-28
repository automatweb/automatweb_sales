<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_js_parser.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_js_parser.aw - Taketi JS Parser 
/*

@classinfo syslog_type=ST_TAKET_JSPARSER relationmgr=yes

@default table=objects
@default group=general

*/

class taket_js_parser extends class_base
{
	const AW_CLID = 248;

	function taket_js_parser()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_js_parser",
			"clid" => CL_TAKET_JSPARSER
		));
	}

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

	function get_js($arr)
	{
		$this->read_template('input_data.tpl');
		if(aw_global_get('lang_id')==2)
		{
			$parent = aw_ini_get('taket.js_main_menuENG');
		}
		else
		{
			$parent = aw_ini_get('taket.js_main_menu');
		}
		$ol = new object_list(array(
						'parent'=>$parent,
						'class_id' => CL_MENU,
						'sort_by' => 'objects.jrk',
						'status' => STAT_ACTIVE
				));
		$content='';
		for($o=$ol->begin(),$i=0;!$ol->end();$o=$ol->next(),$i++)
		{
			$ol2 = new object_list(array(
							'parent'=>$o->id(),
							'class_id' => CL_MENU,
							'sort_by' => 'objects.jrk',
							'status' => STAT_ACTIVE
					));
			$tmp='';
			for($o2=$ol2->begin(),$j=0;!$ol2->end();$o2=$ol2->next(),$j++)
			{
				$this->vars(array('counter1'=>$i,
										'counter2'=>$j,
										'text'=>$o2->prop('name'),
										'link'=>$o2->id()));
				$tmp.=$this->parse('scndLevel');
			}
			$this->vars(array(
							'counter1'=>$i,
							'scndLevelParsed'=>$tmp));
			$content.=$this->parse('frstLevel');
		}
		$this->vars(array('frstLevelParsed'=>$content));	
		die($this->parse());
	}
}
?>
